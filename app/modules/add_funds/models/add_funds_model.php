<?php 
defined('BASEPATH') or exit('No direct script access allowed');

class add_funds_model extends MY_Model
{
    public $tb_users;
    public $tb_transaction_logs;
    public $tb_payments;
    public $tb_payments_bonuses;
    public $module;
    public $module_icon;

    public function __construct()
    {
        parent::__construct();
        $this->tb_users            = USERS;
        $this->tb_transaction_logs = TRANSACTION_LOGS;
        $this->tb_payments         = PAYMENTS_METHOD;
        $this->tb_payments_bonuses = PAYMENTS_BONUSES;
    }

    // Add fund, bonus and send email
    public function add_funds_bonus_email($data_tnx, $payment_id = "")
    {
        if (!$data_tnx) {
            return false;
        }

        if (!isset($data_tnx->transaction_id)) {
            return false;
        }
        // Update Balance  and total spent
        if (is_table_exists(AFFILIATE)) {
            $user = $this->model->get('id, first_name, last_name, email, balance, timezone, spent, ref_uid', $this->tb_users, ["id" => $data_tnx->uid]);
        } else {
            $user = $this->model->get('id, first_name, last_name, email, balance, timezone, spent', $this->tb_users, ["id" => $data_tnx->uid]);
        }
        if (!$user) {
            return false;
        }
        $new_funds = $data_tnx->amount - $data_tnx->txn_fee;
        $new_balance = $user->balance + $new_funds;
        if ($user->spent == "") {
            $total_spent_before = $this->model->sum_results('amount', $this->tb_transaction_logs, ['status' => 1, 'uid' => $data_tnx->uid]);
            $total_spent = (double) round($total_spent_before + $data_tnx->amount, 4);
        } else {
            $total_spent = (double) round($user->spent + $data_tnx->amount, 4);
        }
        $user_update_data = [
            "balance" => $new_balance,
            "spent" => $total_spent,
        ];
        $this->db->update($this->tb_users, $user_update_data, ["id" => $data_tnx->uid]);
        // Update Transaction for previous balance
		$this->db->update($this->tb_transaction_logs, ["old_balance" => $user->balance], ["id" => $data_tnx->id]);
        //Add bonus
        if ($payment_id) {
            $data_pm_bonus = [
                'payment_id'      => $payment_id,
                'uid'             => $data_tnx->uid,
                'amount'          => $new_funds,
                'id'              => (isset($data_tnx->id)) ? $data_tnx->id : "" ,
                'current_balance' => $new_balance,
            ];
            $this->add_payment_bonuses((object) $data_pm_bonus);
        }
        // affiliates
        if (is_table_exists(AFFILIATE) && $user->ref_uid > 0) {
            $this->load->model('affiliates/affiliates_model', 'affiliates_model');
            $test = $this->affiliates_model->save_item(['id' => $user->ref_uid, 'amount' => $data_tnx->amount], ['task' =>'referral']);
        }
        /*----------  Send payment notification email  ----------*/
        if (get_option("is_payment_notice_email", '')) {
            $this->send_mail_payment_notification(['user' => $user]);
        }
        return true;
    }

    private function add_payment_bonuses($data_pm = "")
    {

        if (!$data_pm) {
            return false;
        }
        if (!isset($data_pm->payment_id)) {
            return false;
        }
		
		$this->db->select("id, bonus_from, percentage, status");
		$this->db->from($this->tb_payments_bonuses);
		$this->db->where(['payment_id' => $data_pm->payment_id, 'status' => 1]);
		$this->db->where('bonus_from <=', $data_pm->amount);
		$this->db->where('FIND_IN_SET("'.$data_pm->uid.'", user_ids) >', 0, false);
		$this->db->order_by('bonus_from', 'DESC');
		$this->db->limit(1); 
		$query = $this->db->get();
		$get_user_payment_bonus = $query->row();
		
		// $get_user_payment_bonus = $this->model->get("id, bonus_from, percentage, status", $this->tb_payments_bonuses, ['payment_id' => $data_pm->payment_id, 'status' => 1, 'bonus_from >=' => $data_pm->amount, 'FIND_IN_SET("'.$data_pm->uid.'", user_ids) >' => 0]);
		
        if ($get_user_payment_bonus) {
			// add bonuses
			$bonus = ($get_user_payment_bonus->percentage / 100) * $data_pm->amount;
			$this->db->update($this->tb_users, ["balance" => $data_pm->current_balance + $bonus], ["id" => $data_pm->uid]);

			// insert transaction id:
			$data_tnx_log = array(
				"ids"            => ids(),
				"uid"            => $data_pm->uid,
				"type"           => 'Bonus',
				"transaction_id" => (isset($data_pm->id) && $data_pm->id) ? "Transaction Bonus #" . $data_pm->id : "" ,
				"old_balance"    => $data_pm->current_balance,
				"amount"         => $bonus,
				"status"         => 1,
				"created"        => NOW,
			);
			$transaction_log_id = $this->db->insert($this->tb_transaction_logs, $data_tnx_log);
			return true;	
		} else {
			// get payment bonuses
			$this->db->select("id, bonus_from, percentage, status");
			$this->db->from($this->tb_payments_bonuses);
			$this->db->where(['payment_id' => $data_pm->payment_id, 'status' => 1]);
			$this->db->where('bonus_from <=', $data_pm->amount);
			$this->db->order_by('bonus_from', 'DESC');
			$this->db->limit(1); 
			$query = $this->db->get();
			$payment_bonus = $query->row();
			
			// $payment_bonus = $this->model->get("id, bonus_from, percentage, status", $this->tb_payments_bonuses, ['payment_id' => $data_pm->payment_id, 'status' => 1, 'bonus_from >=' => $data_pm->amount]);
			if (!$payment_bonus) {
				return false;
			}
			// add bonuses
			$bonus = ($payment_bonus->percentage / 100) * $data_pm->amount;
			$this->db->update($this->tb_users, ["balance" => $data_pm->current_balance + $bonus], ["id" => $data_pm->uid]);

			// insert transaction id:
			$data_tnx_log = array(
				"ids"            => ids(),
				"uid"            => $data_pm->uid,
				"type"           => 'Bonus',
				"transaction_id" => (isset($data_pm->id) && $data_pm->id) ? "Transaction Bonus #" . $data_pm->id : "" ,
				"old_balance"    => $data_pm->current_balance,
				"amount"         => $bonus,
				"status"         => 1,
				"created"        => NOW,
			);
			$transaction_log_id = $this->db->insert($this->tb_transaction_logs, $data_tnx_log);
			return true;	
		}
    }
    
    private function send_mail_payment_notification($data_pm_mail = "")
    {
        if ($data_pm_mail['user']) {

            $user = $data_pm_mail['user'];
            $subject = get_option('email_payment_notice_subject', '');
            $message = get_option('email_payment_notice_content', '');
            // get Merge Fields
            $merge_fields = [
                '{{user_firstname}}' => $user->first_name,
            ];
            $template = ['subject' => $subject, 'message' => $message, 'type' => 'default', 'merge_fields' => $merge_fields];
            $send_message = $this->model->send_mail_template($template, $user->id);

            if ($send_message) {
                ms(array(
                    'status' => 'error',
                    'message' => $send_message,
                ));
            }
            return true;
        } else {
            return false;
        }
    }
}
