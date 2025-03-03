<?php 
defined('BASEPATH') or exit('No direct script access allowed');

class cryptomus extends MX_Controller
{
    public $tb_users;
    public $tb_transaction_logs;
    public $tb_payments;
    public $tb_payments_bonuses;
    public $cryptomus;
    public $payment_type;
    public $payment_id;
    public $currency_code;
    public $payment_lib;
    public $mode;

    public $merchant_id;
    public $payment_api_key;

    public function __construct($payment = "")
    {
        parent::__construct();
        $this->load->model('add_funds_model', 'model');

        $this->tb_users            = USERS;
        $this->tb_transaction_logs = TRANSACTION_LOGS;
        $this->tb_payments         = PAYMENTS_METHOD;
        $this->tb_payments_bonuses = PAYMENTS_BONUSES;
        $this->payment_type        = get_class($this);
        $this->currency_code       = get_option("currency_code", "USD");
        if ($this->currency_code == "") {
            $this->currency_code = 'USD';
        }
        if (!$payment) {
            $payment = $this->model->get('id, type, name, params', $this->tb_payments, ['type' => $this->payment_type]);
        }
        $this->payment_id  = $payment->id;
        $params            = $payment->params;
        $option            = get_value($params, 'option');
        $this->mode        = get_value($option, 'environment');
        $this->payment_fee = get_value($option, 'tnx_fee');

        // Payment Option
        $this->merchant_id     = get_value($option, 'merchant_id');
        $this->payment_api_key = get_value($option, 'payment_api_key');
    }

    public function index()
    {
        redirect(cn('add_funds'));
    }

    /*----------  Create payments  ----------*/
    public function create_payment($data_payment = "")
    {
        _is_ajax($data_payment['module']);
        $amount = $data_payment['amount'];
        if (!$amount) {
            _validation('error', lang('There_was_an_error_processing_your_request_Please_try_again_later'));
        }

        if (!$this->merchant_id || !$this->payment_api_key) {
            _validation('error', lang('this_payment_is_not_active_please_choose_another_payment_or_contact_us_for_more_detail'));
        }
		
        $amount = (float)$amount;
        $users  = session('user_current_info');
		
        // Create Signature
        $order_id  = "ORDS".strtotime(NOW);
		
		$call_user_id  = base64_encode(session("uid"));
		$call_order_id = base64_encode($order_id);
		
		//added 2% extra with added amount for cryptomus charges
		$payment_fee = ($this->payment_fee) ? $this->payment_fee : 0;
		$charges     = round($amount * ($payment_fee / 100), 3);
		$amount      = round(($amount + $charges),2);
		
        $data_post = array(
            'order_id'     => $order_id,
            'amount'       => (string)$amount,
            'currency'     => "USD",
			"url_return"   => cn("add_funds/cryptomus/cancel/".$call_user_id."/".$call_order_id),
			"url_success"  => cn("add_funds/cryptomus/complete/".$call_user_id."/".$call_order_id),
			"url_callback" => cn("add_funds/cryptomus/callback/".$call_user_id."/".$call_order_id)
        );
		
        $response = $this->send_post_curl("https://api.cryptomus.com/v1/payment", $data_post);
		
        if (!empty($response) && isset($response['result']) && !empty($response['result'])) {
			$redirectUrl = ($response['result']['url']) ? $response['result']['url'] : '';
			$uuid        = ($response['result']['uuid']) ? $response['result']['uuid'] : '';
			
            /*----------  Insert Transaction logs  ----------*/
            $data_tnx_log = array(
                "ids"                     => ids(),
                "uid"                     => session("uid"),
                "type"                    => $this->payment_type,
                "transaction_id"          => $uuid,
                "amount"                  => $amount,
                'txn_fee'                 => round($amount * ($payment_fee / 100), 4),
                "data"                    => $order_id,
                "status"                  => 0,
                "created"                 => NOW,
				"cryptomus_uuid"          => $uuid,
				"create_invoice_response" => json_encode($response),
            );
            $transaction_log_id = $this->db->insert($this->tb_transaction_logs, $data_tnx_log);
            $this->load->view("redirect", ['redirect_url' => $redirectUrl]);
        } else {
			$errors   = '';
			$messages = '';
			if(!empty($response) && isset($response['errors']) && !empty($response['errors'])){
				foreach($response['errors'] as $key => $err){
					$errors .= "Error in ".$key. " :- ".$err[0];
				}
			} elseif(!empty($response) && isset($response['message']) && !empty($response['message'])){
				$errors = $response['message'];
			}
			
            _validation('error', $errors);
        }
    }

    public function complete($user_id,$order_id)
    {
		if($user_id != ''){
			$user = $this->model->get("id, status, ids, email, password, first_name, last_name, timezone", $this->tb_users, ['id' => base64_decode($user_id)]);
			
			set_session("uid", $user->id);
			$data_session = array(
				'email'      => $user->email,
				'first_name' => $user->first_name,
				'last_name'  => $user->last_name,
				'timezone'   => $user->timezone,
			);
			set_session('user_current_info', $data_session);
		}
		
		if($order_id != '')
		{
			$transaction_logs = $this->model->get("*", $this->tb_transaction_logs, ['data' => base64_decode($order_id)]);
			if(!empty($transaction_logs))
			{
				$payment_info = array(
					'uuid'     => $transaction_logs->cryptomus_uuid,
					'order_id' => $transaction_logs->data
				);
				
				$payment_response = $this->send_post_curl("https://api.cryptomus.com/v1/payment/info",$payment_info);
				if(!empty($payment_response) && isset($payment_response['result']) && !empty($payment_response['result']))
				{
					$result                     = $payment_response['result'];
					
					$amount                     = $result['amount'];
					$payment_amount             = $result['payment_amount'];
					$payment_amount_usd         = $result['payment_amount_usd'];
					$payer_amount               = $result['payer_amount'];
					$payer_amount_exchange_rate = $result['payer_amount_exchange_rate'];
					$discount_percent           = $result['discount_percent'];
					$discount                   = $result['discount'];
					$payer_currency             = $result['payer_currency'];
					$currency                   = $result['currency'];
					$comments                   = $result['comments'];
					$merchant_amount            = $result['merchant_amount'];
					$network                    = $result['network'];
					$address                    = $result['address'];
					$from                       = $result['from'];
					$txid                       = $result['txid'];
					$payment_status             = $result['payment_status'];
					$status                     = $result['status'];
					$is_final                   = $result['is_final'];
					$additional_data            = $result['additional_data'];
					$created_at                 = $result['created_at'];
					$updated_at                 = $result['updated_at'];
					$commission                 = $result['commission'];
					
					if(($status == 'paid' && $payment_status == 'paid') || ($status == 'paid_over' && $payment_status == 'paid_over'))
					{
						if($transaction_logs->status == 0)
						{
							// $data_tnx_log = array(
								// "transaction_id" => ($txid) ? $txid : $transaction_logs->cryptomus_uuid,
								// "status"         => 1,
							// );
							// $this->db->update($this->tb_transaction_logs, $data_tnx_log, ['id' => $transaction_logs->id]);
							
							// $this->model->add_funds_bonus_email($transaction_logs, $this->payment_id);
						}
					}
					
					$this->db->update($this->tb_transaction_logs, ['resend_webhook_response' => json_encode($payment_response)], ['id' => $transaction_logs->id]);
				}
			}
			
			$data = array(
                "module"      => get_class($this),
                "transaction" => $transaction_logs,
            );
			
            $this->template->set_layout('user');
            $this->template->build('payment_successfully', $data);
		}
    }

    public function callback($user_id,$order_id)
	{
		if($order_id != '')
		{
			$transaction_logs = $this->model->get("*", $this->tb_transaction_logs, ['data' => base64_decode($order_id)]);
			if(!empty($transaction_logs))
			{
				$payment_info = array(
					'uuid'     => $transaction_logs->cryptomus_uuid,
					'order_id' => $transaction_logs->data
				);
				
				$payment_response = $this->send_post_curl("https://api.cryptomus.com/v1/payment/info",$payment_info);
				if(!empty($payment_response) && isset($payment_response['result']) && !empty($payment_response['result']))
				{
					$result                     = $payment_response['result'];
					
					$amount                     = $result['amount'];
					$payment_amount             = $result['payment_amount'];
					$payment_amount_usd         = $result['payment_amount_usd'];
					$payer_amount               = $result['payer_amount'];
					$payer_amount_exchange_rate = $result['payer_amount_exchange_rate'];
					$discount_percent           = $result['discount_percent'];
					$discount                   = $result['discount'];
					$payer_currency             = $result['payer_currency'];
					$currency                   = $result['currency'];
					$comments                   = $result['comments'];
					$merchant_amount            = $result['merchant_amount'];
					$network                    = $result['network'];
					$address                    = $result['address'];
					$from                       = $result['from'];
					$txid                       = $result['txid'];
					$payment_status             = $result['payment_status'];
					$status                     = $result['status'];
					$is_final                   = $result['is_final'];
					$additional_data            = $result['additional_data'];
					$created_at                 = $result['created_at'];
					$updated_at                 = $result['updated_at'];
					$commission                 = $result['commission'];
					
					if(($status == 'paid' && $payment_status == 'paid') || ($status == 'paid_over' && $payment_status == 'paid_over'))
					{
						if($transaction_logs->status == 0)
						{
							$data_tnx_log = array(
								"transaction_id" => ($txid) ? $txid : $transaction_logs->cryptomus_uuid,
								"status"         => 1,
							);
							$this->db->update($this->tb_transaction_logs, $data_tnx_log, ['id' => $transaction_logs->id]);
							
							$this->model->add_funds_bonus_email($transaction_logs, $this->payment_id);	
						}
					}
					
					$this->db->update($this->tb_transaction_logs, ['webhook_response' => json_encode($payment_response)], ['id' => $transaction_logs->id]);
				}
			}
		}
	}

	public function cancel($user_id,$order_id)
	{
		if($user_id != '')
		{
			$user = $this->model->get("id, status, ids, email, password, first_name, last_name, timezone", $this->tb_users, ['id' => base64_decode($user_id)]);
			
			set_session("uid", $user->id);
			$data_session = array(
				'email'      => $user->email,
				'first_name' => $user->first_name,
				'last_name'  => $user->last_name,
				'timezone'   => $user->timezone,
			);
			set_session('user_current_info', $data_session);
		}
		
		if($order_id != '')
		{
			$transaction_logs = $this->model->get("id", $this->tb_transaction_logs, ['data' => base64_decode($order_id)]);
			if(!empty($transaction_logs))
			{
				$this->db->update($this->tb_transaction_logs, ['webhook_response' => 'Cancelled and redirect back'], ['id' => $transaction_logs->id]);
			}
		}
		
		$data = array(
			"module" => get_class($this),
        );
        $this->template->set_layout('user');
        $this->template->build('payment_unsuccessfully', $data);
	}

    private function send_post_curl($url, $data_post, $method = "")
    {
        if ($method == "") {
            $method = "POST";
        }
		
		$data = json_encode($data_post);
		$sign = md5(base64_encode($data) . $this->payment_api_key);
		
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST  => $method,
            CURLOPT_POSTFIELDS     => json_encode($data_post),
            CURLOPT_HTTPHEADER     => [
                "content-type: application/json",
                "cache-control: no-cache",
				"merchant: ".$this->merchant_id,
				"sign: ".$sign,
            ],
        ));
        $response = curl_exec($curl);
        $err      = curl_error($curl);

        if ($err) {
            pr('Curl returned error: ' . $err, 1);
            die('Curl returned error: ' . $err);
        }
        $response = json_decode($response, true);
        return $response;
    }

}
