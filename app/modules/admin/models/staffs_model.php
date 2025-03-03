<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class staffs_model extends MY_Model 
{

    protected $tb_main;
    protected $filter_accepted;
    protected $field_search_accepted;

    public function __construct()
    {
        parent::__construct();
        $this->tb_main     = STAFFS;

        $this->filter_accepted = array_keys(app_config('template')['status']);
        unset($this->filter_accepted['3']);
        $this->field_search_accepted = app_config('config')['search']['staffs'];
    }

    public function list_items($params = null, $option = null)
    {
        $result = null;
       
        if ($option['task'] == 'list-items') {
            $this->db->select('s.id, s.ids, s.first_name, s.admin, s.last_name, s.email, s.role_id, s.history_ip, s.status, s.created');
            $this->db->select('rp.name as permission_name');
            $this->db->from($this->tb_main .' s');
            $this->db->join($this->tb_role_permission . " rp", "rp.id = s.role_id", 'left');
            $this->db->where('s.admin', 10);
            // filter
            if ($params['filter']['status'] != 3 && in_array($params['filter']['status'], $this->filter_accepted)) {
                $this->db->where('s.status', $params['filter']['status']);
            }
            //Search
            if ($params['search']['field'] === 'all') {
                $i = 1;
                foreach ($this->field_search_accepted as $column) {
                    if ($column != 'all') {
                        if($i == 1){
                            $this->db->like('s' . $column, $params['search']['query']); 
                        }elseif ($i > 1) {
                            $this->db->or_like('s' . $column, $params['search']['query']); 
                        }
                        $i++;
                    }
                }
            }elseif (in_array($params['search']['field'], $this->field_search_accepted) && $params['search']['query'] != "") {
                $this->db->like($params['search']['field'], $params['search']['query']); 
            }

            $this->db->order_by('s.id', 'DESC');
            if ($params['pagination']['limit'] != "" && $params['pagination']['start'] >= 0) {
                $this->db->limit($params['pagination']['limit'], $params['pagination']['start']);
            }

            $query = $this->db->get();
            $result = $query->result_array();
        }
        return $result;
    }

    public function get_item($params = null, $option = null)
    {
        $result = null;
        if($option['task'] == 'get-item'){
            $result = $this->get("id, ids, role_id, first_name, last_name, timezone, email,  history_ip, status, created, settings", $this->tb_main, ['ids' => $params['ids']], '', '', true);
        }
        return $result;
    }

    public function count_items($params = null, $option = null)
    {
        $result = null;
        if ($option['task'] == 'count-items-group-by-status') {
            $this->db->select('count(id) as count, status');
            $this->db->from($this->tb_main);
            $this->db->where('admin', 10);
            //Search
            if ($params['search']['field'] === 'all') {
                $i = 1;
                foreach ($this->field_search_accepted as $column) {
                    if ($column != 'all') {
                        if($i == 1){
                            $this->db->like($column, $params['search']['query']); 
                        }elseif ($i > 1) {
                            $this->db->or_like($column, $params['search']['query']); 
                        }
                        $i++;
                    }
                }
            }elseif (in_array($params['search']['field'], $this->field_search_accepted) && $params['search']['query'] != "") {
                $this->db->like($params['search']['field'], $params['search']['query']); 
            }

            $this->db->order_by('status', 'DESC');
            $this->db->group_by('status');
            $query = $this->db->get();
            $result = $query->result_array();
        }

        // Count items for pagination
        if ($option['task'] == 'count-items-for-pagination') {
            $this->db->select('id');
            $this->db->from($this->tb_main);
            $this->db->where('admin', 10);
            if ($params['filter']['status'] != 3 && in_array($params['filter']['status'], $this->filter_accepted)) {
                $this->db->where('status', $params['filter']['status']);
            }
            //Search
            if ($params['search']['field'] === 'all') {
                $i = 1;
                foreach ($this->field_search_accepted as $column) {
                    if ($column != 'all') {
                        if($i == 1){
                            $this->db->like($column, $params['search']['query']); 
                        }elseif ($i > 1) {
                            $this->db->or_like($column, $params['search']['query']); 
                        }
                        $i++;
                    }
                }
            }elseif (in_array($params['search']['field'], $this->field_search_accepted) && $params['search']['query'] != "") {
                $this->db->like($params['search']['field'], $params['search']['query']); 
            }
            $query = $this->db->get();
            $result = $query->num_rows();
        }
        return $result;
    }

    public function delete_item($params = null, $option = null)
    {
        $result = [];
        is_demo_version();
        if($option['task'] == 'delete-item'){
            $item = $this->get("id, ids", $this->tb_main, ['ids' => $params['id']]);
            if ($item) {
                $this->db->delete($this->tb_main, ["ids" => $params['id']]);
                $this->db->delete($this->tb_tickets, ["uid" => $item->id]);
                $this->db->delete($this->tb_ticket_message, ["uid" => $item->id]);
                $this->db->delete($this->tb_order, ["uid" => $item->id]);
                $this->db->delete($this->tb_users_price, ["uid" => $item->id]);
                $result = [
                    'status' => 'success',
                    'message' => 'Deleted successfully',
                    "ids"     => $item->ids,
                ];
            }else{
                $result = [
                    'status' => 'error',
                    'message' => 'There was an error processing your request. Please try again later',
                ];
            }
        }
        return $result;
    }

    public function save_item($params = null, $option = null)
    {
        if (in_array($option['task'], ['add-item', 'edit-item'])) {
            $data = array(
                "first_name"   => post("first_name"),
                "last_name"    => post("last_name"),
                "email"        => post("email"),
                "status"       => (int)post("status"),
                "timezone"     => post("timezone"),
                "changed"      => NOW,
                "settings"     => json_encode(post('settings')),
                "reset_key"    => ids(),
                "role_id"      => post("role_id"),
                "admin"        => 10, // Default Staff
            );

            if (in_array(post("role_id"), [1])) {
                return ["status"  => "error", "message" => 'Permission denied'];
            }
        }
        switch ($option['task']) {
            
            case 'add-item':
                $data['ids']         = ids();
                $data['password']    = $this->app_password_hash(post('password'));
                $data['login_type']  = 'create_by_'. current_logged_staff()->first_name;
                $data['created']     = NOW;
                $this->db->insert($this->tb_main, $data);
                return ["status"  => "success", "message" => 'Added successfully'];
                break;

            case 'edit-item':
                $this->db->update($this->tb_main, $data, ["ids" => post('ids')]);
                return ["status"  => "success", "message" => 'Updated successfully'];
                break;

            case 'change-status':
                $this->db->update($this->tb_main, ['status' => $params['status'], 'changed' => NOW], ["ids" => $params['id']]);
                return ["status"  => "success", "message" => 'Updated successfully'];
                break;

            case 'set-password':
                $data = [
                    'password' => $this->app_password_hash(post('password')),
                    'changed'  => NOW,
                ];
                $this->db->update($this->tb_main, $data, ["ids" => post('ids')]);
                return ["status"  => "success", "message" => 'Password changed successfully!'];
                break;

            case 'bulk-action':
                is_demo_version();
                if (in_array($params['type'], ['delete', 'deactive', 'active']) && empty($params['ids'])) {
                    return ["status"  => "error", "message" => 'Please choose at least one item'];
                }
                $arr_ids = convert_str_number_list_to_array($params['ids']);
                switch ($params['type']) {
                    case 'delete':
                        $this->db->where_in('ids', $arr_ids);
                        $this->db->delete($this->tb_main);

                        return ["status"  => "success", "message" => 'Delete successfully'];
                        break;
                    case 'deactive':
                        // Category
                        $this->db->where_in('ids', $arr_ids);
                        $this->db->update($this->tb_main, ['status' => 0]);

                        return ["status"  => "success", "message" => 'Update successfully'];
                        break;
                    case 'active':
                        $this->db->where_in('ids', $arr_ids);
                        $this->db->update($this->tb_main, ['status' => 1]);

                        return ["status"  => "success", "message" => 'Update successfully'];
                        break;
                }
                break;
        }
    }
}
