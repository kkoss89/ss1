<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Nowpayments extends MX_Controller {

    private $tb_transaction_logs;
    private $tb_payments;
    private $tb_users;
    private $payment_type;
    private $currency_code;
    private $public_api_key;
    private $ipn_secret;

    public function __construct($payment = "") {
        parent::__construct();
        $this->load->model('add_funds_model', 'model');
        $this->load->model('wallet_model');
        $this->tb_users = USERS;
        $this->tb_transaction_logs = TRANSACTION_LOGS;
        $this->tb_payments = PAYMENTS_METHOD;
        $this->payment_type = get_class($this);
        $this->currency_code = get_option("currency_code", "USD");
        
        if (!$payment) {
            $payment = $this->model->get('id, type, name, params', $this->tb_payments, ['type' => $this->payment_type]);
        }
        
        $this->payment_id = $payment->id;
        $params = $payment->params;
        $option = get_value($params, 'option');
        
		$this->api_key = get_value($option, 'api_key'); // Actual API key from admin config
		$this->ipn_secret = get_value($option, 'ipn_secret'); // HMAC secret
    }

    public function create_payment($data_payment) {
        _is_ajax($data_payment['module']);
        
        $amount = (float)$data_payment['amount'];
        $user_id = session("uid");
        $order_id = "NOWP_".time()."_".$user_id; // Add this line
        
        $payload = [
            'price_amount' => $amount,
            'price_currency' => $this->currency_code,
            'order_id' => $order_id,
            'ipn_callback_url' => cn("nowpayments_ipn"),
            'success_url' => cn("add_funds/nowpayments/complete"),
            'cancel_url' => cn("add_funds/nowpayments/cancel")
        ];

        $response = $this->send_api_request('https://api.nowpayments.io/v1/invoice', $payload);
        
        if (isset($response['invoice_url'])) {
            // Insert transaction log
            $data_tnx = [
                'ids' => ids(),
                'uid' => $user_id,
                'type' => $this->payment_type,
                'transaction_id' => $response['id'],
                'amount' => $amount,
                'data' => $order_id,
                'status' => 0,
                'created' => NOW
            ];
            $this->db->insert($this->tb_transaction_logs, $data_tnx);
            
            $this->load->view("redirect", ['redirect_url' => $response['invoice_url']]);
        } else {
            $error = $response['message'] ?? 'Unknown API error';
            _validation('error', "NOWPayments Error: ".$error);
        }
    }



	// Nowpayments.php controller - IPN handler update
	public function nowpayments_ipn() {
		$payload = file_get_contents('php://input');
		$received_sig = $this->input->server('HTTP_X_NOWPAYMENTS_SIG');
		$expected_sig = hash_hmac('sha512', $payload, $this->ipn_secret);
		if (hash_equals($expected_sig, $received_sig)) {
			$data = json_decode($payload, true);
			$order_parts = explode('_', $data['order_id']);
			$user_id = end($order_parts);
			$payment_status = (string)$data['payment_status'];
			$order_id = (string)$data['order_id'];
			$amount = (float)$data['pay_amount'];
		}		
		
		$order_id = $data['order_id'];
		$order_parts = explode('_', $order_id);
		$user_id = end($order_parts); // Extracts user ID from "NOWP_TIMESTAMP_USERID"
		
		// Use $user_id for balance updates
		$this->wallet_model->update_balance($user_id, $data['pay_amount']);
		if (hash_equals($expected_sig, $received_sig)) {
			$data = json_decode($payload, true, 512, JSON_BIGINT_AS_STRING);
			// Convert string values to proper types
			$payment_status = (string)$data['payment_status'];
			$order_id = (string)$data['order_id'];
			//$customer_id = (int)$data['customer_id'];
			$amount = (float)$data['pay_amount'];
			
			if ($payment_status === 'finished') {
				$transaction = $this->model->get('*', $this->tb_transaction_logs, [
					'transaction_id' => (string)$data['payment_id'],
					'status' => 0
				]);
				
				if ($transaction && $this->validate_payment($data)) {
					$this->db->update($this->tb_transaction_logs, [
						'status' => 1,
						'webhook_response' => json_encode($data)
					], ['id' => $transaction->id]);
					
					$this->model->add_funds_bonus_email($transaction, $this->payment_id);
				}
			}
			http_response_code(200);
		} else {
			log_message('error', 'Invalid NOWPayments signature: '.$payload);
			http_response_code(403);
		}
	}

	private function validate_payment($data) {
		// Add validation for minimum amount
		$min_payment = 0.003; // 0.003 BTC equivalent
		if ((float)$data['price_amount'] < $min_payment) {
			log_message('error', 'Payment below minimum: '.$data['price_amount']);
			return false;
		}
		
		// Verify currency match
		if ($data['pay_currency'] !== $this->currency_code) {
			log_message('error', 'Currency mismatch: '.$data['pay_currency']);
			return false;
		}
		
		return true;
	}


    public function complete() {
        $txn_id = $this->input->get('payment_id');
        $transaction = $this->model->get('*', $this->tb_transaction_logs, [
            'transaction_id' => $txn_id,
            'uid' => session('uid')
        ]);
        
        $data = [
            "module" => $this->payment_type,
            "transaction" => $transaction ?? null
        ];
        
        $this->template->set_layout('user');
        $this->template->build('payment_successfully', $data);
    }

    public function cancel() {
        $data = ["module" => $this->payment_type];
        $this->template->set_layout('user');
        $this->template->build('payment_unsuccessfully', $data);
    }

    private function send_api_request($url, $data) {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'x-api-key: '.$this->api_key
            ]
        ]);
        
        $response = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);
        
        return json_decode($response, true) ?? ['error' => $error];
    }

}