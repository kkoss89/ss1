<?php 
defined('BASEPATH') or exit('No direct script access allowed');

class payop extends MX_Controller
{
    public $tb_users;
    public $tb_transaction_logs;
    public $tb_payments;
    public $tb_payments_bonuses;
    public $paypal;
    public $payment_type;
    public $payment_id;
    public $currency_code;
    public $payment_lib;
    public $mode;

    public $pm_public_key;
    public $pm_secret_key;
    public $pm_jwt_token;

    public function __construct($payment = "")
    {
        parent::__construct();
        $this->load->model('add_funds_model', 'model');

        $this->tb_users = USERS;
        $this->tb_transaction_logs = TRANSACTION_LOGS;
        $this->tb_payments = PAYMENTS_METHOD;
        $this->tb_payments_bonuses = PAYMENTS_BONUSES;
        $this->payment_type = get_class($this);
        $this->currency_code = get_option("currency_code", "USD");
        if ($this->currency_code == "") {
            $this->currency_code = 'USD';
        }
        if (!$payment) {
            $payment = $this->model->get('id, type, name, params', $this->tb_payments, ['type' => $this->payment_type]);
        }
        $this->payment_id = $payment->id;
        $params = $payment->params;
        $option = get_value($params, 'option');
        $this->mode = get_value($option, 'environment');
        $this->payment_fee = get_value($option, 'tnx_fee');

        // Payment Option
        $this->pm_public_key           = get_value($option, 'public_key');
        $this->pm_secret_key              = get_value($option, 'secret_key');
        $this->pm_jwt_token            = get_value($option, 'jwt_token');
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

        if (!$this->pm_public_key || !$this->pm_secret_key || !$this->pm_jwt_token) {
            _validation('error', lang('this_payment_is_not_active_please_choose_another_payment_or_contact_us_for_more_detail'));
        }
        $amount = number_format($amount, 3, '.', ',');
        $users = session('user_current_info');

        // Create Signature
        $order_id = "ORDS" . strtotime(NOW);
        $data_order = array(
            'id' => $order_id,
            'amount' => $amount,
            'currency' => "USD",
        );
        ksort($data_order, SORT_STRING);
        $dataSet = array_values($data_order);
        $dataSet[] = $this->pm_secret_key;
        $signature = hash('sha256', implode(':', $dataSet));

        // Order details
        $data_order['items'] = [];
        $data_order['description'] = base64_encode("Balance recharge - " . $users['email']);

        // Payer
        $data_payer = array(
            'email' => $users['email'],
            'phone' => "+374841842151",
            'name' => $users['first_name'],
        );

        $data_post = [
            'publicKey' => $this->pm_public_key,
            'order' => $data_order,
            'signature' => $signature,
            'payer' => $data_payer,
            // 'paymentMethod'     => '381',
            'language' => "en",
            'resultUrl' => cn("statistics"),
            'failPath' => cn("add_funds/unsuccess"),
        ];

        $response = $this->send_post_curl("https://payop.com/v1/invoices/create", $data_post);
        if (isset($response['status']) && $response['status'] && isset($response['data'])) {
            $redirectUrl = "https://payop.com/en/payment/invoice-preprocessing/" . $response['data'];
            /*----------  Insert Transaction logs  ----------*/
            $data_tnx_log = array(
                "ids" => ids(),
                "uid" => session("uid"),
                "type" => $this->payment_type,
                "transaction_id" => $response['data'],
                "amount" => $amount,
                'txn_fee' => round($amount * ($this->payment_fee / 100), 4),
                "data" => $order_id,
                "status" => 0,
                "created" => NOW,
            );
            $transaction_log_id = $this->db->insert($this->tb_transaction_logs, $data_tnx_log);
            $this->load->view("redirect", ['redirect_url' => $redirectUrl]);

        } else {
            _validation('error', $response['message']);
        }
    }

    public function complete()
    {
        redirect(cn("add_funds/success"));
    }

    public function cron()
    {

        $transactions = $this->model->fetch('*', $this->tb_transaction_logs, ['status' => 0, 'type' => $this->payment_type]);

        foreach ($transactions as $key => $transaction) {
            if (empty($transaction)) {
                continue;
            }
            $result_invoice = $this->get_invoice_detail($transaction->transaction_id, $this->pm_jwt_token);
            if (isset($result_invoice['data']['transactionIdentifier']) && $result_invoice['data']['transactionIdentifier'] == "") {
                continue;
            }
            $transactionIdentifier = $result_invoice['data']['transactionIdentifier'];
            $result = $this->get_transaction_detail($transactionIdentifier, $this->pm_jwt_token);
            $tx_status = 0;
            if (isset($result['status']) && $result['status']) {
                $result['data']['invoiceIdentifier'] = $transaction->transaction_id;
                switch ($result['data']['status']) {
                    case 'success':
                        $tx_status = 1;
                        $data_tnx = [
                            'status' => $tx_status,
                            'transaction_id' => $result['data']['txid'],
                            'data' => json_encode($result['data']),
                        ];
                        break;

                    case 'fail':
                        $tx_status = -1;
                        $data_tnx = [
                            'status' => $tx_status,
                            'transaction_id' => $result['data']['txid'],
                            'data' => json_encode($result['data']),
                        ];
                        break;
                }
            }

            if ($result_invoice['data']['amount'] == $transaction->amount && $transaction->data == $result_invoice['data']['orderIdentifier']) {
                // update transaction
                if (isset($data_tnx)) {
                    $this->db->update($this->tb_transaction_logs, $data_tnx, ['ids' => $transaction->ids, 'type' => $this->payment_type]);
                }
                if ($tx_status == 1) {
                    // Update Balance
            		$this->model->add_funds_bonus_email($transaction, $this->payment_id);
                    echo $transaction->transaction_id;
                    echo "<br> Added Success";
                } else {
                    echo $transaction->transaction_id;
                    echo "<br> can't add balance";
                }
            } else {
                if (isset($result_invoice['message'])) {
                    echo $result_invoice['message'] . "<br>";
                    continue;
                } else {
                    echo "There is some issues with payment";
                }
            }
        }
    }

    private function get_invoice_detail($invoices_id, $payop_jwt_token)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://payop.com/v1/invoices/" . $invoices_id,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => [
                "authorization: Bearer " . $payop_jwt_token,
                "content-type: application/json",
                "cache-control: no-cache",
            ],
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        if ($err) {
            die('Curl returned error: ' . $err);
        }
        return json_decode($response, true);
    }

    private function get_transaction_detail($transaction_id, $payop_jwt_token)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://payop.com/v1/checkout/check-transaction-status/" . $transaction_id,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => [
                "authorization: Bearer " . $payop_jwt_token,
                "content-type: application/json",
                "cache-control: no-cache",
            ],
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        if ($err) {
            die('Curl returned error: ' . $err);
        }
        return json_decode($response, true);
    }

    private function send_post_curl($url, $data_post, $method = "")
    {
        if ($method == "") {
            $method = "POST";
        }
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_POSTFIELDS => json_encode($data_post),
            CURLOPT_HTTPHEADER => [
                "content-type: application/json",
                "cache-control: no-cache",
            ],
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);

        if ($err) {
            pr('Curl returned error: ' . $err, 1);
            die('Curl returned error: ' . $err);
        }
        $response = json_decode($response, true);
        return $response;
    }

}
