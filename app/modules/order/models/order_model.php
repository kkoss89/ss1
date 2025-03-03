<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class order_model extends MY_Model {
    

    public function __construct(){
        parent::__construct();
        $this->tb_main            = ORDER;
        $this->tb_orders_refill   = ORDERS_REFILL;
        $this->tb_blacklist_ip    = BLACKLIST_IP;
        $this->tb_blacklist_email = BLACKLIST_EMAIL;
        $this->tb_blacklist_link  = BLACKLIST_LINK;
        $this->filter_accepted = app_config('config')['status']['order'];
        $this->filter_accepted = array_diff($this->filter_accepted, ['all']);
        $this->field_search_accepted = ['id', 'link'];
    }

    public function list_items($params = null, $option = null)
    {
        $result = null;
        if ($option['task'] == 'list-items') {
            $item_main_id = 0;
            if (get('subscription')) {
                $item_main_id = (int)get('subscription');
            } elseif (get('drip-feed')) {
                $item_main_id = (int)get('drip-feed');
            }

            $this->db->select('o.id, o.ids, o.runs, o.service_id, o.status, o.charge, o.link, o.quantity, o.start_counter, o.changed, o.remains, o.created, o.order_id,o.is_interval_order');
            $this->db->select('o.refill, o.refill_status, o.refill_date');
            $this->db->select('s.name as service_name');
			$this->db->select('c.name as category_name');
            $this->db->from($this->tb_main . ' o');
            $this->db->join($this->tb_services." s", "s.id = o.service_id", 'left');
			$this->db->join("categories c", "c.id = s.cate_id", 'left'); 
            // filter
            if ($params['filter']['status'] != 'all' && in_array($params['filter']['status'], $this->filter_accepted)) {
                if ($params['filter']['status'] == 'pending') {
                    $this->db->where_in('o.status', ['pending', 'error', 'fail']);
                } else {
                    $this->db->where('o.status', $params['filter']['status']);
                }
            }
            $this->db->where("o.service_type !=", "subscriptions");
            $this->db->where("o.is_drip_feed !=", 1);
            $this->db->where("o.uid", session("uid"));
            // Get all orders relate to main order id
            if ($item_main_id > 0) {
                $this->db->where("o.main_order_id", $item_main_id);
            }

            //Search
            if ($params['search']['query'] != '') {
                $field_value = $this->db->escape_like_str($params['search']['query']);
                $where_like = "(`o`.`id` LIKE '%" . $field_value ."%' ESCAPE '!' OR `o`.`order_id` LIKE '%" . $field_value ."%' ESCAPE '!' OR `o`.`link` LIKE '%". $field_value ."%' ESCAPE '!')";
                $this->db->where($where_like);
            }

            $this->db->order_by('id', 'desc');
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

        if ($option['task'] == 'get-item') {
            $result = $this->get("*", $this->tb_main, ['ids' => $params['ids']], '', '', true);
        }
        return $result;
    }

    public function count_items($params = null, $option = null)
    {
        $result = null;

        // Count items for pagination
        if ($option['task'] == 'count-items-for-pagination') {
            $item_main_id = 0;
            if (get('subscription')) {
                $item_main_id = (int)get('subscription');
            } elseif (get('drip-feed')) {
                $item_main_id = (int)get('drip-feed');
            }

            $this->db->select('o.id');
            $this->db->from($this->tb_main . ' o');
            // filter
            if ($params['filter']['status'] != 'all' && in_array($params['filter']['status'], $this->filter_accepted)) {
                $this->db->where('o.status', $params['filter']['status']);
            }
            $this->db->where("o.service_type !=", "subscriptions");
            $this->db->where("o.is_drip_feed !=", 1);
            $this->db->where("o.uid", session("uid"));
            // Get all orders relate to main order id
            if ($item_main_id > 0) {
                $this->db->where("o.main_order_id", $item_main_id);
            }
             //Search
            if ($params['search']['query'] != '') {
                $field_value = $this->db->escape_like_str($params['search']['query']);
                $where_like = "(`o`.`id` LIKE '%" . $field_value ."%' ESCAPE '!' OR `o`.`order_id` LIKE '%" . $field_value ."%' ESCAPE '!' OR `o`.`link` LIKE '%". $field_value ."%' ESCAPE '!')";
                $this->db->where($where_like);
            }

            $query = $this->db->get();
            $result = $query->num_rows();
        }
        return $result;
    }

    public function save_item($params = null, $option = null)
    {
        $result = null;

        
        return $result;
    }

    function get_log_details($id){
        $this->db->select('o.*, u.email as user_email, s.name as service_name, api.name as api_name');
        $this->db->from($this->tb_order." o");
        $this->db->join($this->tb_users." u", "u.id = o.uid", 'left');
        $this->db->join($this->tb_services." s", "s.id = o.service_id", 'left');
        $this->db->join($this->tb_api_providers." api", "api.id = o.api_provider_id", 'left');
        $this->db->where("o.main_order_id", $id);
        $this->db->order_by("o.id", 'DESC');
        $query = $this->db->get();
        $result = $query->result();
        return $result;
    }

    function list_items_best_seller($params = [], $option = []){
        $result = [];
        $limit = (isset($params['limtt'])) ? $params['limtt'] : 10;

        $query = "SELECT count(service_id) as total_orders, service_id FROM {$this->tb_order} GROUP BY service_id ORDER BY total_orders DESC LIMIT 30";
        $items_top_sellers =  $this->db->query($query)->result_array();

        if (!$items_top_sellers) {
            return $result;
        }
        $items_arr_service_id = array_column($items_top_sellers, 'service_id');
        if ($option['task'] == 'admin') {
            $this->db->select('s.*, api.name as api_name');
            $this->db->from($this->tb_services." s");
            $this->db->join($this->tb_api_providers." api", "s.api_provider_id = api.id", 'left');
            $this->db->where("s.id", $item['service_id']);
            $this->db->where("s.status", 1);
            $this->db->order_by("s.price", 'ASC');
            $query = $this->db->get();
            $result = $query->result_array();
        }
        if ($option['task'] == 'user') {
            $this->db->select('id, ids, name, min, max, price, desc');
            $this->db->from($this->tb_services);
            $this->db->where_in("id", $items_arr_service_id);
            $this->db->where("status", 1);
            $query = $this->db->get();
            $result = $query->result_array();
        }
        return $result;
    }

    function check_blacklist() {
        if (is_table_exists($this->tb_blacklist_ip)) {
            $item_exists = $this->get('id', $this->tb_blacklist_ip, ['ip' => get_client_ip(), 'status' => 1]);
            if ($item_exists) {
                _validation('error', lang('You_do_not_have_permission_to_place_order_on_this_panel'));
            }
        }
        if (is_table_exists($this->tb_blacklist_link)) {
            if (post('link') != '') {
                $link = post('link');
            } else if (post('sub_username') != '') {
                $link = post('sub_username');
            } else {
                $link = '';
            }
            if ($link) {
                $this->db->select('id');
                $this->db->from($this->tb_blacklist_link);
                $this->db->where('status', 1);
                $link = $this->db->escape_like_str($link);
                $this->db->like('link', $link);
                $query = $this->db->get();
                if ($query->num_rows() > 0) {
                    _validation('error', lang('We_are_sorry_that_your_order_could_not_be_placed_with_this_order_link'));
                }
            }
        }
        return false;
    }
	
	function generate_unique_order_id(){
		do {
			$randomNumber = rand(10000, 99999);

			$this->db->select('order_id');
			$this->db->from($this->tb_main);
			$this->db->where('order_id',$randomNumber);
			$query = $this->db->get();
			$isNumberExists = $query->num_rows() > 0;

        } while ($isNumberExists);

        return $randomNumber;
	}
	
	function generate_unique_sub_order_id(){
		do {
			$randomNumber = rand(10000, 99999);

			$this->db->select('order_id');
			$this->db->from('sub_orders');
			$this->db->where('order_id',$randomNumber);
			$query = $this->db->get();
			$isNumberExists = $query->num_rows() > 0;

        } while ($isNumberExists);

        return $randomNumber;
	}
}
