<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class admin_model extends MY_Model 
{
    protected $tb_main;

    public function __construct()
    {
        parent::__construct();
        $this->tb_main     = STAFFS;
    }

    public function get_item($params = null, $option = null)
    {
        $result = null;
        if ($option['task'] == 'get-item-current-admin') {
            $result = $this->get("id, ids, first_name, last_name, email, timezone, password", $this->tb_main, ['id' => session('sid')], '', '', true);
        }
        return $result;
    }

    public function save_item($params = null, $option = null)
    {
        switch ($option['task']) {
            case 'update-info-item':
                $data = array(
                    "first_name"   => post("first_name"),
                    "last_name"    => post("last_name"),
                    "timezone"     => post("timezone"),
                );
                $this->db->update($this->tb_main, $data, ["ids" => post('ids')]);
                return ["status"  => "success", "message" => 'Update successfully'];
                break;

            case 'change-pass-item':
                $data = [
                    'password' => $this->app_password_hash(post('password')),
                    'changed'  => NOW,
                ];
                $this->db->update($this->tb_main, $data, ["ids" => post('ids')]);
                return ["status"  => "success", "message" => 'Password changed successfully!'];
                break;
        }
    }

    public function verify_admin_access($params = null, $option = null)
    {
        if ($option['task'] == 'check-admin-secret-key') {
            $item_admin = $this->get_item(null, ['task' => 'get-item-current-admin']);
            $check_secret_key   = $this->app_password_verify($params['secret_key'], $item_admin['password']);
            if ($check_secret_key) {
                return true;
            } else {
                return false;
            }
        }
    }
}
