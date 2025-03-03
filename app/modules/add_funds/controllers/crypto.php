<?php 
defined('BASEPATH') or exit('No direct script access allowed');

class crypto extends MX_Controller
{
    public $tb_users;
    public $tb_transaction_logs;
    public $tb_payments;
    public $tb_payments_bonuses;
    public $crypto;
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

    public function complete()
    {
		$data_tnx_log2 = array(
			"request_data" => json_encode($_REQUEST),
			"cron_type"    => 'Complete Request',
		);
		$cron_logs2 = $this->db->insert('cron_logs', $data_tnx_log2);
		
		$id      = (isset($_REQUEST['transaction_id'])) ? $_REQUEST['transaction_id'] : '';
		$user_id = (isset($_REQUEST['external_reference'])) ? $_REQUEST['external_reference'] : '';
		
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
		
		if($id != '' && $user_id != '')
		{
			$transaction_logs = $this->model->get("*", $this->tb_transaction_logs, ['data' => $id]);
			if(!empty($transaction_logs))
			{	
				$this->db->update($this->tb_transaction_logs, ['resend_webhook_response' => json_encode($_REQUEST)], ['id' => $transaction_logs->id]);	
			}
			
			if(!empty($transaction_logs))
			{
				$data = array(
					"module"      => get_class($this),
					"transaction" => $transaction_logs,
				);
				
				$this->template->set_layout('user');
				$this->template->build('payment_successfully', $data);
			}
			else
			{
				$data = array(
					"module" => get_class($this),
				);
				$this->template->set_layout('user');
				$this->template->build('payment_unsuccessfully', $data);
			}
		}
    }

    public function callback()
	{
		$response = json_decode(file_get_contents('php://input'), true);
		
		$data_tnx_log = array(
			"request_data" => json_encode($response),
			"cron_type"    => 'Webhook',
		);
		$cron_logs = $this->db->insert('cron_logs', $data_tnx_log);
		
		$id                 = (isset($response['transaction']['id'])) ? $response['transaction']['id'] : '';
		$amount             = (isset($response['transaction']['amount'])) ? $response['transaction']['amount'] : 0;
		$amount_fiat        = (isset($response['transaction']['amount_fiat'])) ? $response['transaction']['amount_fiat'] : 0;
		$external_reference = (isset($response['transaction']['external_reference'])) ? $response['transaction']['external_reference'] : '';
		$status             = (isset($response['transaction']['status'])) ? $response['transaction']['status'] : '';
		
		if($id != '' && $status == 'C' && $external_reference != '')
		{
			$data_tnx_log2 = array(
                "ids"                     => ids(),
                "uid"                     => base64_decode($external_reference),
                "type"                    => 'crypto',
                "transaction_id"          => $id,
                "amount"                  => round($amount_fiat,2),
                'txn_fee'                 => 0,
                "data"                    => $id,
                "status"                  => 0,
                "created"                 => NOW,
				"cryptomus_uuid"          => $id,
				"create_invoice_response" => json_encode($response),
            );
            $transaction_log_id = $this->db->insert($this->tb_transaction_logs, $data_tnx_log2);
			
			$transaction_logs = $this->model->get("*", $this->tb_transaction_logs, ['data' => $id]);
			if(!empty($transaction_logs))
			{
				if($status == 'C')
				{	
					$data_tnx_log = array(
						"transaction_id" => $id,
						"status"         => 1,
					);
					$this->db->update($this->tb_transaction_logs, $data_tnx_log, ['id' => $transaction_logs->id]);
					
					$this->model->add_funds_bonus_email($transaction_logs, $this->payment_id);	
				}
			}
		}
	}
}
