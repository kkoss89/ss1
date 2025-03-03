<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class cron_model extends MY_Model {
    protected $tb_main;

    public function __construct(){
        parent::__construct();
        $this->get_class();
        $this->tb_main    = ORDER;
    }

    public function list_items($params = null, $option = null)
    {
        $result = null;
        if ($option['task'] == 'list-items-new-order') {
            $where = "(`status` = 'active' or `status` = 'pending' or `status` = 'inprogress')";
            $this->db->select("*");
            $this->db->from($this->tb_main);
            $this->db->where($where);
            $this->db->where("api_provider_id !=", 0);
            $this->db->where("api_order_id =", -1);
			$this->db->where("is_interval_order",0);
			$this->db->order_by('rand()');
            $this->db->order_by("id", 'ASC');
            $this->db->limit(20, 0);
            $query = $this->db->get();
            $result = $query->result();
        }

		if($option['task'] == 'list-items-new-sub-order') {
			$where = "(`status` = 'active' or `status` = 'pending' or `status` = 'inprogress')";
			
            $this->db->select("*");
            $this->db->from('sub_orders');
            $this->db->where($where);
            $this->db->where("api_provider_id !=", 0);
            $this->db->where("api_order_id =", -1);
			$this->db->where("is_interval_order",1);
			$this->db->where('start_at is NOT NULL', NULL, FALSE);
			$this->db->where('start_at <', NOW);
			$this->db->order_by('rand()');
            $this->db->order_by("id", 'ASC');
            $this->db->limit(20, 0);
            $query = $this->db->get();
            $result = $query->result();
		}

        if ($option['task'] == 'list-items-status') {
            $this->db->select("*");
            $this->db->from($this->tb_main);
            $this->db->where_in('status', ['active', 'processing', 'inprogress', 'pending', 'error']);
            $this->db->where('api_order_id >', 0);
            $this->db->where('changed <', NOW);
            $this->db->where('service_type !=', 'subscriptions');
            $this->db->where('is_drip_feed', 0);
			$this->db->where("is_interval_order",0);
			$this->db->order_by('rand()');
            $this->db->order_by("id", 'ASC');
            $this->db->limit($params['limit'], $params['start']);
            $query = $this->db->get();
            $result = $query->result_array();
        }

		if ($option['task'] == 'list-items-suborders-status') {
            $this->db->select("*");
            $this->db->from('sub_orders');
            $this->db->where_in('status', ['active', 'processing', 'inprogress', 'pending', 'error']);
            $this->db->where('api_order_id >', 0);
            $this->db->where('changed <', NOW);
            $this->db->where('service_type !=', 'subscriptions');
            $this->db->where('is_drip_feed', 0);
			$this->db->where("is_interval_order",1);
			$this->db->order_by('rand()');
            $this->db->order_by("id", 'ASC');
            $this->db->limit($params['limit'], $params['start']);
            $query = $this->db->get();
            $result = $query->result_array();
        }
		
		if ($option['task'] == 'list-items-suborders-pending-to-complete') {
            $this->db->select("orders.id");
            $this->db->from('orders');
			$this->db->join('sub_orders', 'orders.id = sub_orders.parent_order_id');
			$this->db->where('sub_orders.status','completed');
			$this->db->where('orders.status !=','completed');
			$this->db->group_by('orders.id');
			$this->db->having('COUNT(CASE WHEN sub_orders.status != "completed" THEN 1 END) = 0');
            $this->db->order_by("id", 'ASC');
            $this->db->limit($params['limit'], $params['start']);
            $query = $this->db->get();
            $result = $query->result_array();
        }

        if ($option['task'] == 'list-items-dripfeed-status') {
            $this->db->select("*");
            $this->db->from($this->tb_main);
            $this->db->where_in('status', ['active', 'processing', 'inprogress', 'pending']);
            $this->db->where('api_order_id >', 0);
            $this->db->where('changed <', NOW);
            $this->db->where('service_type', 'default');
            $this->db->where('is_drip_feed', 1);
			$this->db->where("is_interval_order",0);
            $this->db->order_by('rand()');
            $this->db->order_by("id", 'ASC');
            $this->db->limit($params['limit'], $params['start']);
            $query = $this->db->get();
            $result = $query->result_array();
        }

        if ($option['task'] == 'list-items-subscriptions-status') {
            $this->db->select("*");
            $this->db->from($this->tb_main);
            $this->db->where_in('sub_status', ['Active', 'Paused', '']);
            $this->db->where('api_order_id >', 0);
            $this->db->where('changed <', NOW);
            $this->db->where('service_type', 'subscriptions');
			$this->db->where("is_interval_order",0);
            $this->db->order_by('rand()');
            $this->db->order_by("id", 'ASC');
            $this->db->limit($params['limit'], $params['start']);
            $query = $this->db->get();
            $result = $query->result_array();
        }

        if ($option['task'] == 'list-items-multiple-status') {
            $this->db->select("*");
            $this->db->from($this->tb_main);
            $this->db->where_in('status', ['active', 'processing', 'inprogress', 'pending']);
            $this->db->where('api_order_id >', 0);
            $this->db->where('changed <', NOW);
            $this->db->where('service_type !=', 'subscriptions');
            $this->db->where('is_drip_feed !=', 1);
			$this->db->where("is_interval_order",0);
            $this->db->order_by('rand()');
            $this->db->order_by("id", 'ASC');
            $this->db->limit($params['limit'], $params['start']);
            $query = $this->db->get();
            $result = $query->result_array();
        }

        return $result;
    }

    public function get_item($params = null, $option = null)
    {
        $result = null;
        if ($option['task'] == 'get-item-provider') {
            $result = $this->get('url, key, type, id', $this->tb_api_providers, ['id' => $params['id']], '', '', true);
        }
        return $result;
    }

    public function save_item($params = null, $option = null)
    {
        if ($option['task'] == 'item-new-update') {
            $order_log =  "Order ID - ". $params['order_id'];
            if (!$params['response']) {
                $data_item = [
                    "status"       => 'error',
                    "note"         => 'Troubleshooting API requests',
					"api_quantity" => ($params['api_quantity']) ? $params['api_quantity'] : 0,
                    "changed"      => NOW,
                ];
                $this->db->update($this->tb_main, $data_item, ["id" => $params['order_id']]);
            }
            if (isset($params['response']['error'])) {
                $order_log .= " : ". $params['response']['error'];
                $data_item = [
                    "status"       => 'error',
                    "note"         => $params['response']['error'],
					"api_quantity" => ($params['api_quantity']) ? $params['api_quantity'] : 0,
                    "changed"      => NOW,
                ];
                $this->db->update($this->tb_main, $data_item, ["id" => $params['order_id']]);
            }
            if (isset($params['response']['order'])) {
                $data_item = [
					"api_quantity" => ($params['api_quantity']) ? $params['api_quantity'] : 0,
                    "api_order_id" => $params['response']['order'],
                    "changed"      => NOW,
					"start_at"     => date("Y-m-d H:i:s"),
                ];
                $this->db->update($this->tb_main, $data_item, ["id" => $params['order_id']]);
            }
            echo $order_log . '<br>';
        }
		
		if ($option['task'] == 'item-new-sub-order-update') {
            $order_log =  "Order ID - ". $params['order_id'];
            if (!$params['response']) {
                $data_item = [
                    "status"       => 'error',
                    "note"         => 'Troubleshooting API requests',
					"api_quantity" => ($params['api_quantity']) ? $params['api_quantity'] : 0,
                    "changed"      => NOW,
                ];
                $this->db->update('sub_orders', $data_item, ["id" => $params['order_id']]);
            }
            if (isset($params['response']['error'])) {
                $order_log .= " : ". $params['response']['error'];
                $data_item = [
                    "status"       => 'error',
                    "note"         => $params['response']['error'],
					"api_quantity" => ($params['api_quantity']) ? $params['api_quantity'] : 0,
                    "changed"      => NOW,
                ];
                $this->db->update('sub_orders', $data_item, ["id" => $params['order_id']]);
            }
            if (isset($params['response']['order'])) {
                $data_item = [
					"api_quantity" => ($params['api_quantity']) ? $params['api_quantity'] : 0,
                    "api_order_id" => $params['response']['order'],
                    "changed"      => NOW,
					"started_at"   => date("Y-m-d H:i:s")
                ];
                $this->db->update('sub_orders', $data_item, ["id" => $params['order_id']]);
            }
            echo $order_log . '<br>';
        }
		
		if ($option['task'] == 'item-new-sub-order-multiple-update') {
            $order_log =  "Order ID - ". $params['parent_order_id'];
            if (!$params['response']) {
                $data_item = [
                    "status"       => 'error',
                    "note"         => 'Troubleshooting API requests',
					"api_quantity" => ($params['api_quantity']) ? $params['api_quantity'] : 0,
                    "changed"      => NOW,
                ];
                $this->db->update('sub_orders', $data_item, ["parent_order_id" => $params['parent_order_id']]);
            }
            if (isset($params['response']['error'])) {
                $order_log .= " : ". $params['response']['error'];
                $data_item = [
                    "status"       => 'error',
                    "note"         => $params['response']['error'],
					"api_quantity" => ($params['api_quantity']) ? $params['api_quantity'] : 0,
                    "changed"      => NOW,
                ];
                $this->db->update('sub_orders', $data_item, ["parent_order_id" => $params['parent_order_id']]);
            }
            if (isset($params['response']['order'])) {
                $data_item = [
					"api_quantity" => ($params['api_quantity']) ? $params['api_quantity'] : 0,
                    "api_order_id" => $params['response']['order'],
                    "changed"      => NOW,
                ];
                $this->db->update('sub_orders', $data_item, ["parent_order_id" => $params['parent_order_id']]);
            }
            echo $order_log . '<br>';
        }

        // For single Order
        if ($option['task'] == 'item-status') {
            $item = $params['item'];
            $order_log =  "Order ID - ". $item['id'];

            if (isset($params['response']['error'])) {
                $order_log .= " : ". $params['response']['error'];
                $data_item = [
                    "status"      => 'error',
                    "note"        => $params['response']['error'],
                    "changed"     => NOW,
                ];
                $this->db->update($this->tb_main, $data_item, ["id" => $item['id']]);
            }

            if (isset($params['response']['status'])) {
                $order_log .= " : ". $params['response']['status'];
                $received_status = order_status_format($params['response']['status']);

                $rand_time = get_random_time();
                $data_item = array(
                    "start_counter" => $params['response']['start_count'],
                    "remains"       => order_remains_format($params['response']['remains']),
                    "note" 	        => "",
                    "changed"       => date('Y-m-d H:i:s', strtotime(NOW) + $rand_time),
                    "status"        => $received_status,
                );

				if($received_status == 'Complete' || $received_status == 'Completed' || $received_status == 'complete' || $received_status == 'completed'){
					$data_item['end_at'] = date('Y-m-d H:i:s');
				}

                //Check refill order or not
                // if (strtolower($params['response']['status']) == "completed" && $item['refill'] && is_table_exists($this->tb_orders_refill)) {
                    // $data_item['refill_status'] = 'completed';
                    // $data_item['refill_date'] = date('Y-m-d H:i:s', strtotime(NOW) + 86400); //next refill request 24h
                // }
				
				//New implementation custom refill option
				if (strtolower($params['response']['status']) == "completed" && $item['refill']) {
                    $data_item['refill_status'] = 1;
                    $data_item['refill_date']   = date('Y-m-d H:i:s', strtotime(NOW) + 86460); //next refill request 24h and 1 minute
                }
                
                if (in_array($received_status, ['refunded', 'partial', 'canceled'])) {
                    if($params['response']['remains'] > $item['quantity']) { 
                        $params['response']['remains'] = $item['quantity']; 
                    }
                    $new_order_attr = calculate_order_by_status($item, ['status' => $received_status, 'remains' => $params['response']['remains']]);
                    $response = $this->crud_user(['uid' => $item['uid'], 'fields' => 'balance', 'new_amount' => $new_order_attr['refund_money']], ['task' => 'update-balance']);
                    $data_item['charge']        = $new_order_attr['real_charge'];
                    $data_item['formal_charge'] = $new_order_attr['formal_chagre'];
                    $data_item['profit']        = $new_order_attr['profit'];
                }
                $this->db->update($this->tb_main, $data_item, ["id" => $item['id']]);
            }
            echo $order_log . '<br>';
        }
		
		if ($option['task'] == 'item-suborder-status') {
            $item = $params['item'];
            $order_log =  "Order ID - ". $item['id'];

            if (isset($params['response']['error'])) {
                $order_log .= " : ". $params['response']['error'];
                $data_item = [
                    "status"      => 'error',
                    "note"        => $params['response']['error'],
                    "changed"     => NOW,
                ];
                $this->db->update('sub_orders', $data_item, ["id" => $item['id']]);
            }

            if (isset($params['response']['status'])) {
                $order_log .= " : ". $params['response']['status'];
                $received_status = order_status_format($params['response']['status']);

                $rand_time = get_random_suborder_time();
                $data_item = array(
                    "start_counter" => $params['response']['start_count'],
                    "remains"       => order_remains_format($params['response']['remains']),
                    "note" 	        => "",
                    "changed"       => date('Y-m-d H:i:s', strtotime(NOW) + $rand_time),
                    "status"        => $received_status,
                );
				
				if($received_status == 'Complete' || $received_status == 'Completed' || $received_status == 'complete' || $received_status == 'completed'){
					$data_item['ended_at'] = date('Y-m-d H:i:s');
				}
				
                //Check refill order or not
                if (strtolower($params['response']['status']) == "completed" && $item['refill'] && is_table_exists($this->tb_orders_refill)) {
                    $data_item['refill_status'] = 'Complete';
                    $data_item['refill_date'] = date('Y-m-d H:i:s', strtotime(NOW) + 86400); //next refill request 24h
                }
                
                if (in_array($received_status, ['refunded', 'partial', 'canceled'])) {
                    if($params['response']['remains'] > $item['quantity']) { 
                        $params['response']['remains'] = $item['quantity']; 
                    }
                    $new_order_attr = calculate_order_by_status($item, ['status' => $received_status, 'remains' => $params['response']['remains']]);
					
                    //$response = $this->crud_user(['uid' => $item['uid'], 'fields' => 'balance', 'new_amount' => $new_order_attr['refund_money']], ['task' => 'update-balance']);
					
                    $data_item['charge']        = $new_order_attr['real_charge'];
                    $data_item['formal_charge'] = $new_order_attr['formal_chagre'];
                    $data_item['profit']        = $new_order_attr['profit'];
                }
                $this->db->update('sub_orders', $data_item, ["id" => $item['id']]);
            }
            echo $order_log . '<br>';
        }
		
        // For multi Order
        if ($option['task'] == 'item-multiple_status') {
            if (isset($params['response']['error'])) {
                $order_log = "ID: " . implode(", ", $params['order_ids']) . " - ". $params['response']['error'];
                $data_item = [
                    "status"      => 'error',
                    "note"        => $params['response']['error'],
                    "changed"     => NOW,
                ];
                $this->db->where_in('id', $params['order_ids']);
                $this->db->update($this->tb_main, $data_item);
                echo $order_log . '<br>';
            }
        }
        // For single dripfeed Order
        if ($option['task'] == 'item-dripfeed-status') {
            $item = $params['item'];
            $order_log =  "Order ID - ". $item['id'];
            if (isset($params['response']['error'])) {
                $order_log .= " : ". $params['response']['error'];
                $data_item = [
                    "status"      => 'error',
                    "note"        => $params['response']['error'],
                    "changed"     => NOW,
                ];
                $this->db->update($this->tb_main, $data_item, ["id" => $item['id']]);
            }

            if (isset($params['response']['status'])) {
                $order_log .= " : ". $params['response']['status'];
                $rand_time = get_random_time();
                $status_dripfeed = order_status_format($params['response']['status'], 'dripfeed');
                $data_item = [
                    "changed"  => date('Y-m-d H:i:s', strtotime(NOW) + $rand_time),
                    "status"   => $status_dripfeed,
                ];
                if (isset($params['response']['runs'])) {
                    $data_item['sub_response_orders'] = json_encode((object)$params['response']);
                }else{
                    switch ($params['response']['status']) {
                        case 'Completed':
                            $params['response']['status'] = 'Completed';
                            $params['response']['runs']   = $item['runs'];
                            break;

                        case 'In progress':
                            $params['response']['status'] = 'Inprogress';
                            $params['response']['runs']   = 0;
                            break;

                        case 'Canceled':
                            $params['response']['status'] = 'Canceled';
                            $params['response']['runs']   = 0;
                            break;
                    }
                    $data_item['sub_response_orders'] = json_encode((object)$params['response']);
                }
                /*----------  Add new order from reponse Drip-feed Service data  ----------*/
                if (isset($params['response']['orders'])) {
                    $this->create_order_log($params, ['task' => 'dripfeed']);
                }
                // Return back to user balance
                if (in_array($params['response']['status'], ['Partial', 'Canceled'])) {
                    $charge = $item['charge'];
                    $data_item['quantity'] = 0;
                    $data_item['charge']   = 0;
                    $return_funds = $charge;

                    if ($params['response']['status'] == "Partial") {
                        $return_funds     = $charge - ($charge * ($params['response']['runs'] / $item['runs']));
                        $data_item['quantity'] = $params['response']['runs']  * $item['dripfeed_quantity'];
                        $data_item['charge']   = $charge * ($params['response']['runs']  / $item['runs']);
                    }
                    $response = $this->crud_user(['uid' => $item['uid'], 'fields' => 'balance', 'new_amount' => $return_funds], ['task' => 'update-balance']);
                }
                $this->db->update($this->tb_main, $data_item, ["id" => $item['id']]);
            }
            echo $order_log . '<br>';
        }

        // For single subscriptions Order
        if ($option['task'] == 'item-subscriptions-status') {
            $item = $params['item'];
            $order_log =  "Order ID - ". $item['id'];
            if (isset($params['response']['error'])) {
                $order_log .= " : ". $params['response']['error'];
                $data_item = [
                    "status"      => 'error',
                    "note"        => $params['response']['error'],
                    "changed"     => NOW,
                ];
                $this->db->update($this->tb_main, $data_item, ["id" => $item['id']]);
            }
            
            if (isset($params['response']['status'])) {
                $order_log .= " : ". $params['response']['status'];
                $data_item = array(
                    "status"        		    => order_status_format($params['response']['status'], 'subscriptions'),
                    "sub_status"        		=> $params['response']['status'],
                    "sub_response_orders" 	    => json_encode((object)$params['response']),
                    "sub_response_posts" 	    => $params['response']['posts'],
                    "note" 	                    => "",
                    "changed"           		=> date('Y-m-d H:i:s', strtotime(NOW) + get_random_time()),
                );
                /*----------  Add new order from reponse Drip-feed Service data  ----------*/
                if (isset($params['response']['orders'])) {
                    $this->create_order_log($params, ['task' => 'subscriptions']);
                }
                // Return back to user balance if Expired, Canceled
                if (in_array($params['response']['status'], ['Expired', 'Canceled', 'Paused'])) {
                    $return_funds = $item['charge'];
                    if (in_array($params['response']['status'], ['Expired', 'Paused'])) {
                        $return_funds  = $item['charge'] * (1 - ((int)$params['response']['posts'] / $item['sub_posts']));
                        $data_item['charge']   = $item['charge'] - $return_funds;
                    } else {
                        $data_item['charge'] = 0;
                    }
                    $response = $this->crud_user(['uid' => $item['uid'], 'fields' => 'balance', 'new_amount' => $return_funds], ['task' => 'update-balance']);
                }
                $this->db->update($this->tb_main, $data_item, ["id" => $item['id']]);
            }
            echo $order_log . '<br>';
        }

    }

    private function create_order_log($params = [], $option = [])
    {
        $item        = $params['item'];
        $item_api    = $params['item_api'];
        $db_orders = json_decode($item['sub_response_orders']);
        $new_orders   = [];
        if (isset($db_orders->orders)) {
            $new_orders = array_diff($params['response']['orders'], $db_orders->orders);
        }else{
            $new_orders = $params['response']['orders'];
        }
        if (empty($new_orders)) return false;
        $data_orders_batch = [];
        foreach ($new_orders as $order_id) {
            $exists_order = $this->get('id', $this->tb_main, ['api_order_id' => $order_id, 'service_id' => $item['service_id'], 'api_provider_id' => $item['api_provider_id']]);
            if (!empty($exists_order)) continue;
            $data_order = [
                "ids" 	        	  => ids(),
                "uid" 	        	  => $item['uid'],
                "cate_id" 	    	  => $item['cate_id'],
                "service_id" 		  => $item['service_id'],
                "main_order_id"       => $item['id'],
                "service_type" 		  => "default",
                "api_provider_id"  	  => $item['api_provider_id'],
                "api_service_id"  	  => $item['api_service_id'],
                "api_order_id"  	  => $order_id,
                "status"  	          => 'pending',
                "changed" 	    	  => NOW,
                "created" 	    	  => NOW,
            ];

            if ($option['task'] == 'dripfeed') {
                $data_order['link']          = $item['link'];
                $data_order['quantity']      = $item['dripfeed_quantity'];
                $data_order['charge']        = ($item['charge'] * $item['dripfeed_quantity']) / $item['quantity'];;
                $data_order['formal_charge'] = ($item['formal_charge'] * $item['dripfeed_quantity']) / $item['quantity'];
                $data_order['profit']        = ($item['profit'] * $item['dripfeed_quantity']) / $item['quantity'];
            }

            if ($option['task'] == 'subscriptions') {
                $data_order['link']          = "https://www.instagram.com/". $item['username'];
                $data_order['quantity']      = $item['sub_max'];
                $data_order['charge']        = $item['charge'] / $item['sub_posts'];
                $data_order['formal_charge'] = $item['formal_charge'] / $item['sub_posts'];
                $data_order['profit']        = $item['profit'] / $item['sub_posts'];
            }

            $data_orders_batch[] = $data_order;
        }
        if (!empty($data_orders_batch)) {
            $this->db->insert_batch($this->tb_main, $data_orders_batch);
            return true;
        }
    }
	
	public function cancel_order_regex($uid,$refund_money)
	{
		$this->crud_user(['uid' => $uid, 'fields' => 'balance', 'new_amount' => $refund_money], ['task' => 'update-balance']);
	}
	
	public function updateParentOrderStatus($order_id,$parent_order_id,$status)
	{
		if($status == 'pending')
		{
			// if the sub order gets keep in progress even after the completion time all the sub order and main order gets completed 09-12-2024
			$this->db->select('id,start_at,service_id,status');
			$this->db->from('sub_orders');
			$this->db->where("parent_order_id",$parent_order_id);
			$this->db->order_by("id", 'ASC');
			$this->db->limit(1);
			$getFirstOrderStartTime = $this->db->get()->row(); 
			if($getFirstOrderStartTime)
			{
				$this->db->select('id,start_at,service_id,status,start_counter,remains,note');
				$this->db->from('sub_orders');
				$this->db->where("parent_order_id",$parent_order_id);
				$this->db->order_by("id", 'DESC');
				$this->db->limit(1);
				$getLastCompletedOrder = $this->db->get()->row(); 
				if($getLastCompletedOrder)
				{
					if($getLastCompletedOrder->status == 'pending')
					{
						$this->db->select('runs,interval');
						$this->db->from('services');
						$this->db->where("id",$getFirstOrderStartTime->service_id);
						$this->db->order_by('id','DESC');
						$get_service_interval = $this->db->get()->row(); 
						
						$first_order_start_time = date("Y-m-d H:i:s",strtotime($getFirstOrderStartTime->start_at));
						$service_runs           = ($get_service_interval) ? $get_service_interval->runs : 0;
						$service_interval       = ($get_service_interval) ? $get_service_interval->interval : 0;
						$final_interval         = $service_interval * $service_runs; 
						$last_order_end_time    = date('Y-m-d H:i:s',strtotime($first_order_start_time.' +'.$final_interval.' minutes'));
						
						if(strtotime(NOW) > strtotime($last_order_end_time))
						{
							$rand_time = get_random_time();
							$data_item = array(
								"changed"  => date('Y-m-d H:i:s', strtotime(NOW) + $rand_time),
								"status"   => 'completed',
								"ended_at" => date('Y-m-d H:i:s')
							);
							$this->db->update('sub_orders',$data_item,["parent_order_id" => $parent_order_id,"status !=" => 'completed']);
							
							$data_item2 = array(
								"start_counter" => $getLastCompletedOrder->start_counter,
								"remains"       => $getLastCompletedOrder->remains,
								"note" 	        => $getLastCompletedOrder->note,
								"changed"       => date('Y-m-d H:i:s', strtotime(NOW) + $rand_time),
								"status"        => 'completed',
								"end_at"        => date('Y-m-d H:i:s')
							);
							$this->db->update('orders', $data_item2, ["id" => $parent_order_id]);	
						}
					}
				}
			}
			// if the sub order gets keep in progress even after the completion time all the sub order and main order gets completed 09-12-2024
		}
		elseif($status == 'processing') 
		{
			// if the sub order gets keep in progress even after the completion time all the sub order and main order gets completed 09-12-2024
			$this->db->select('id,start_at,service_id,status');
			$this->db->from('sub_orders');
			$this->db->where("parent_order_id",$parent_order_id);
			$this->db->order_by("id", 'ASC');
			$this->db->limit(1);
			$getFirstOrderStartTime = $this->db->get()->row(); 
			if($getFirstOrderStartTime)
			{
				$this->db->select('id,start_at,service_id,status,start_counter,remains,note');
				$this->db->from('sub_orders');
				$this->db->where("parent_order_id",$parent_order_id);
				$this->db->order_by("id", 'DESC');
				$this->db->limit(1);
				$getLastCompletedOrder = $this->db->get()->row(); 
				if($getLastCompletedOrder)
				{
					if($getLastCompletedOrder->status == 'processing')
					{
						$this->db->select('runs,interval');
						$this->db->from('services');
						$this->db->where("id",$getFirstOrderStartTime->service_id);
						$this->db->order_by('id','DESC');
						$get_service_interval = $this->db->get()->row(); 
						
						$first_order_start_time = date("Y-m-d H:i:s",strtotime($getFirstOrderStartTime->start_at));
						$service_runs           = ($get_service_interval) ? $get_service_interval->runs : 0;
						$service_interval       = ($get_service_interval) ? $get_service_interval->interval : 0;
						$final_interval         = $service_interval * $service_runs; 
						$last_order_end_time    = date('Y-m-d H:i:s',strtotime($first_order_start_time.' +'.$final_interval.' minutes'));
						
						if(strtotime(NOW) > strtotime($last_order_end_time))
						{
							$rand_time = get_random_time();
							$data_item = array(
								"changed"  => date('Y-m-d H:i:s', strtotime(NOW) + $rand_time),
								"status"   => 'completed',
								"ended_at" => date('Y-m-d H:i:s')
							);
							$this->db->update('sub_orders',$data_item,["parent_order_id" => $parent_order_id,"status !=" => 'completed']);
							
							$data_item2 = array(
								"start_counter" => $getLastCompletedOrder->start_counter,
								"remains"       => $getLastCompletedOrder->remains,
								"note" 	        => $getLastCompletedOrder->note,
								"changed"       => date('Y-m-d H:i:s', strtotime(NOW) + $rand_time),
								"status"        => 'completed',
								"end_at"        => date('Y-m-d H:i:s')
							);
							$this->db->update('orders', $data_item2, ["id" => $parent_order_id]);	
						}
					}
				}
			}
			// if the sub order gets keep in progress even after the completion time all the sub order and main order gets completed 09-12-2024
		}
		elseif($status == 'inprogress')
		{
			$this->db->select('id,start_counter,remains,note,status');
			$this->db->from('sub_orders');
			$this->db->where("id",$order_id);
			$this->db->where("parent_order_id",$parent_order_id);
			$this->db->where("status",'inprogress');
			$this->db->order_by("id", 'ASC');
			$checkInProgressOrder = $this->db->get()->row(); 
			
			if($checkInProgressOrder){
				$rand_time = get_random_time();
				$data_item = array(
					"start_counter" => $checkInProgressOrder->start_counter,
					"remains"       => $checkInProgressOrder->remains,
					"note" 	        => $checkInProgressOrder->note,
					"changed"       => date('Y-m-d H:i:s', strtotime(NOW) + $rand_time),
					"status"        => $checkInProgressOrder->status
				);
				$this->db->update('orders', $data_item, ["id" => $parent_order_id]);
			}	
			
			// if the sub order gets keep in progress even after the completion time all the sub order and main order gets completed 28-11-2024
			$this->db->select('id,start_at,service_id,status');
			$this->db->from('sub_orders');
			$this->db->where("parent_order_id",$parent_order_id);
			$this->db->order_by("id", 'ASC');
			$this->db->limit(1);
			$getFirstOrderStartTime = $this->db->get()->row(); 
			if($getFirstOrderStartTime)
			{
				$this->db->select('id,start_at,service_id,status,start_counter,remains,note');
				$this->db->from('sub_orders');
				$this->db->where("parent_order_id",$parent_order_id);
				$this->db->order_by("id", 'DESC');
				$this->db->limit(1);
				$getLastCompletedOrder = $this->db->get()->row(); 
				if($getLastCompletedOrder)
				{
					if($getLastCompletedOrder->status == 'inprogress')
					{
						$this->db->select('runs,interval');
						$this->db->from('services');
						$this->db->where("id",$getFirstOrderStartTime->service_id);
						$this->db->order_by('id','DESC');
						$get_service_interval = $this->db->get()->row(); 
						
						$first_order_start_time = date("Y-m-d H:i:s",strtotime($getFirstOrderStartTime->start_at));
						$service_runs           = ($get_service_interval) ? $get_service_interval->runs : 0;
						$service_interval       = ($get_service_interval) ? $get_service_interval->interval : 0;
						$final_interval         = $service_interval * $service_runs; 
						$last_order_end_time    = date('Y-m-d H:i:s',strtotime($first_order_start_time.' +'.$final_interval.' minutes'));
						
						if(strtotime(NOW) > strtotime($last_order_end_time))
						{
							$rand_time = get_random_time();
							$data_item = array(
								"changed"  => date('Y-m-d H:i:s', strtotime(NOW) + $rand_time),
								"status"   => 'completed',
								"ended_at" => date('Y-m-d H:i:s')
							);
							$this->db->update('sub_orders',$data_item,["parent_order_id" => $parent_order_id,"status !=" => 'completed']);
							
							$data_item2 = array(
								"start_counter" => $getLastCompletedOrder->start_counter,
								"remains"       => $getLastCompletedOrder->remains,
								"note" 	        => $getLastCompletedOrder->note,
								"changed"       => date('Y-m-d H:i:s', strtotime(NOW) + $rand_time),
								"status"        => 'completed',
								"end_at"        => date('Y-m-d H:i:s')
							);
							$this->db->update('orders', $data_item2, ["id" => $parent_order_id]);	
						}
					}
				}
			}
			// if the sub order gets keep in progress even after the completion time all the sub order and main order gets completed 28-11-2024
		}
		elseif($status == 'completed')
		{
			$this->db->select('id');
			$this->db->from('sub_orders');
			$this->db->where("parent_order_id",$parent_order_id);
			$this->db->order_by("id", 'ASC');
			$getTotalOrders = $this->db->get()->num_rows(); 
			
			$this->db->select('id');
			$this->db->from('sub_orders');
			$this->db->where("parent_order_id",$parent_order_id);
			$this->db->where("status",'completed');
			$this->db->order_by("id", 'ASC');
			$getTotalCompletedOrders = $this->db->get()->num_rows(); 
			
			if($getTotalOrders == $getTotalCompletedOrders)
			{
				$this->db->select('id,start_counter,remains,note,status');
				$this->db->from('sub_orders');
				$this->db->where("parent_order_id",$parent_order_id);
				$this->db->where("status",'completed');
				$this->db->order_by("id", 'DESC');
				$this->db->limit(1);
				$getLastCompletedOrder = $this->db->get()->row(); 
				
				if($getLastCompletedOrder)
				{
					$this->db->select('id,start_at,service_id');
					$this->db->from('sub_orders');
					$this->db->where("parent_order_id",$parent_order_id);
					$this->db->order_by("id", 'ASC');
					$this->db->limit(1);
					$getFirstOrderStartTime = $this->db->get()->row(); 
					
					if($getFirstOrderStartTime)
					{
						$this->db->select('runs,interval');
						$this->db->from('services');
						$this->db->where("id",$getFirstOrderStartTime->service_id);
						$this->db->order_by('id','DESC');
						$get_service_interval = $this->db->get()->row(); 
						
						$first_order_start_time = date("Y-m-d H:i:s",strtotime($getFirstOrderStartTime->start_at));
						$service_runs           = ($get_service_interval) ? $get_service_interval->runs : 0;
						$service_interval       = ($get_service_interval) ? $get_service_interval->interval : 0;
						$final_interval         = $service_interval * $service_runs; 
						$last_order_end_time    = date('Y-m-d H:i:s',strtotime($first_order_start_time.' +'.$final_interval.' minutes'));
						
						if(strtotime(NOW) > strtotime($last_order_end_time))
						{
							$rand_time = get_random_time();
							$data_item = array(
								"start_counter" => $getLastCompletedOrder->start_counter,
								"remains"       => $getLastCompletedOrder->remains,
								"note" 	        => $getLastCompletedOrder->note,
								"changed"       => date('Y-m-d H:i:s', strtotime(NOW) + $rand_time),
								"status"        => $getLastCompletedOrder->status,
								"end_at"        => date('Y-m-d H:i:s')
							);
							$this->db->update('orders', $data_item, ["id" => $parent_order_id]);	
						}
					}
				}
			}
			else
			{
				$data_item = array(
					"changed" => NOW,
					"status"  => 'inprogress'
				);
				$this->db->update('orders', $data_item, ["id" => $parent_order_id]);
			}
		}
		elseif($status == 'canceled')
		{
			$this->db->select('id');
			$this->db->from('sub_orders');
			$this->db->where("parent_order_id",$parent_order_id);
			$this->db->order_by("id", 'ASC');
			$getTotalOrders = $this->db->get()->num_rows(); 
			
			$this->db->select('id');
			$this->db->from('sub_orders');
			$this->db->where("parent_order_id",$parent_order_id);
			$this->db->where("status",'canceled');
			$this->db->order_by("id", 'ASC');
			$getTotalCanceledOrders = $this->db->get()->num_rows(); 
			
			if($getTotalOrders == $getTotalCanceledOrders)
			{
				$this->db->select('id,note');
				$this->db->from('sub_orders');
				$this->db->where("parent_order_id",$parent_order_id);
				$this->db->where("status",'canceled');
				$this->db->where("is_refunded",0);
				$this->db->order_by("id", 'DESC');
				$this->db->limit(1);
				$getLastCancelledSubOrder = $this->db->get()->row(); 
				
				if($getLastCancelledSubOrder)
				{
					$this->db->select('id,charge,uid');
					$this->db->from('orders');
					$this->db->where("id",$parent_order_id);
					$getParentRecord = $this->db->get()->row(); 
					
					if($getParentRecord)
					{
						$rand_time      = get_random_time();
						$params         = array('charge' => $getParentRecord->charge,'remains' => 0);
						$new_order_attr = calculate_order_by_status($params, ['status' => 'canceled']);
						$this->cancel_order_regex($getParentRecord->uid,$new_order_attr['refund_money']);
						
						$data_item = array(
							"changed"       => date('Y-m-d H:i:s', strtotime(NOW) + $rand_time),
							"status"        => 'canceled',
							"note" 	        => $getLastCancelledSubOrder->note,
							"charge" 	    => $new_order_attr['real_charge'],
							"formal_charge" => $new_order_attr['formal_chagre'],
							"profit" 	    => $new_order_attr['profit']
						);
						$this->db->update('orders', $data_item, ["id" => $getParentRecord->id]);
						
						$data_sub_item = array(
							"is_refunded" => 1
						);
						$this->db->update('sub_orders', $data_sub_item, ["parent_order_id" => $getParentRecord->id]);
					}
				}
			}
			else
			{
				$this->db->select('id,charge,uid,note');
				$this->db->from('sub_orders');
				$this->db->where("parent_order_id",$parent_order_id);
				$this->db->where("id >=",$order_id);
				$this->db->where("is_refunded",0);
				$this->db->order_by("id", 'ASC');
				$getAllRemainingOrders = $this->db->get()->result_array(); 
				
				if(!empty($getAllRemainingOrders))
				{
					$totalCancellationCharge       = 0;
					$totalCancellationFormalCharge = 0;
					$totalCancellationProfit       = 0;
					
					foreach($getAllRemainingOrders as $getAllRemainingOrder)
					{
						$rand_time      = get_random_time();
						$params         = array('charge' => $getAllRemainingOrder['charge'],'remains' => 0);
						$new_order_attr = calculate_order_by_status($params, ['status' => 'canceled']);
						$this->cancel_order_regex($getAllRemainingOrder['uid'],$new_order_attr['refund_money']);
						
						$data_sub_item = array(
							"changed"       => date('Y-m-d H:i:s', strtotime(NOW) + $rand_time),
							"status"        => 'canceled',
							"note" 	        => $getAllRemainingOrder['note'],
							"charge" 	    => $new_order_attr['real_charge'],
							"formal_charge" => $new_order_attr['formal_chagre'],
							"profit" 	    => $new_order_attr['profit'],
							"is_refunded"   => 1
						);
						$this->db->update('sub_orders', $data_sub_item, ["id" => $getAllRemainingOrder['id']]);
						
						$totalCancellationCharge       = $totalCancellationCharge + $new_order_attr['real_charge'];
						$totalCancellationFormalCharge = $totalCancellationFormalCharge + $new_order_attr['formal_chagre'];
						$totalCancellationProfit       = $totalCancellationProfit + $new_order_attr['profit'];
					}
					
					$this->db->select('id');
					$this->db->from('sub_orders');
					$this->db->where("parent_order_id",$parent_order_id);
					$this->db->order_by("id", 'ASC');
					$getTotalOrders = $this->db->get()->num_rows(); 
					
					$this->db->select('id');
					$this->db->from('sub_orders');
					$this->db->where("parent_order_id",$parent_order_id);
					$this->db->where("status",'canceled');
					$this->db->order_by("id", 'ASC');
					$getTotalCanceledOrders = $this->db->get()->num_rows(); 
					
					if($getTotalOrders == $getTotalCanceledOrders)
					{
						$this->db->select('id,charge,uid');
						$this->db->from('orders');
						$this->db->where("id",$parent_order_id);
						$getParentRecord = $this->db->get()->row(); 
						
						if($getParentRecord)
						{
							$rand_time      = get_random_time();
							$params         = array('charge' => $getParentRecord->charge,'remains' => 0);
							$new_order_attr = calculate_order_by_status($params, ['status' => 'canceled']);
							
							$data_item = array(
								"changed"       => date('Y-m-d H:i:s', strtotime(NOW) + $rand_time),
								"status"        => 'canceled',
								"note" 	        => $getLastCancelledSubOrder->note,
								"charge" 	    => $new_order_attr['real_charge'],
								"formal_charge" => $new_order_attr['formal_chagre'],
								"profit" 	    => $new_order_attr['profit']
							);
							$this->db->update('orders', $data_item, ["id" => $getParentRecord->id]);
						}
					}
					else
					{
						$this->db->select('id,charge,formal_charge,profit,uid');
						$this->db->from('orders');
						$this->db->where("id",$parent_order_id);
						$getParentRecord = $this->db->get()->row(); 
						
						if($getParentRecord)
						{
							if($totalCancellationCharge > 0)
							{
								$original_charge        = $getParentRecord->charge;
								$original_formal_charge = $getParentRecord->formal_charge;
								$original_profit        = $getParentRecord->profit;	
								
								$original_charge        = $original_charge - $totalCancellationCharge;
								$original_formal_charge = $original_formal_charge - $totalCancellationFormalCharge;
								$original_profit        = $original_profit - $totalCancellationProfit;
								
								$this->db->select('id');
								$this->db->from('sub_orders');
								$this->db->where("parent_order_id",$parent_order_id);
								$this->db->where("status",'completed');
								$this->db->order_by("id", 'ASC');
								$getTotalCompletedOrders = $this->db->get()->num_rows(); 
								
								if($getTotalCompletedOrders > 0)
								{
									$rand_time = get_random_time();
									
									$data_item = array(
										"changed"       => date('Y-m-d H:i:s', strtotime(NOW) + $rand_time),
										"status"        => 'partial',
										"note" 	        => '',
										"charge" 	    => $original_charge,
										"formal_charge" => $original_formal_charge,
										"profit" 	    => $original_profit
									);
									$this->db->update('orders', $data_item, ["id" => $getParentRecord->id]);
								} 
								else 
								{
									$rand_time      = get_random_time();
									$params         = array('charge' => $getParentRecord->charge,'remains' => 0);
									$new_order_attr = calculate_order_by_status($params, ['status' => 'canceled']);
									
									$data_item = array(
										"changed"       => date('Y-m-d H:i:s', strtotime(NOW) + $rand_time),
										"status"        => 'canceled',
										"note" 	        => '',
										"charge" 	    => $new_order_attr['real_charge'],
										"formal_charge" => $new_order_attr['formal_chagre'],
										"profit" 	    => $new_order_attr['profit']
									);
									$this->db->update('orders', $data_item, ["id" => $getParentRecord->id]);
								}
							}
						}
					}
				}
			}
		}
	}
}
