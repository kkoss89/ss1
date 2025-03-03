<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class order_model extends MY_Model 
{

    protected $tb_main;
    protected $filter_accepted;
    protected $field_search_accepted;

    public function __construct()
    {
        parent::__construct();
        $this->tb_main     = ORDER;
        //Status
        $this->filter_accepted = app_config('config')['status']['order'];
        unset($this->filter_accepted['all']);

        //Copy to clipboard
        $this->bulk_actions_copy_clipboard_accepted = app_config('config')['bulk_action']['order'];
        $this->bulk_actions_copy_clipboard_accepted = array_diff($this->bulk_actions_copy_clipboard_accepted, ['pending', 'inprogress', 'completed', 'resend', 'cancel']);

        $this->field_search_accepted = app_config('config')['search']['order'];
		
		$this->provider = new Smm_api();
    }

    public function list_items($params = null, $option = null)
    {
        $result = null;
        if ($option['task'] == 'list-items') {
            $item_main_id = 0;
            if (get('subscription')) {
                $item_main_id = (int)get('subscription');
            } elseif (get('drip-feed')){
                $item_main_id = (int)get('drip-feed');
            }

            $this->db->select('o.id, o.ids, o.type, o.service_type, o.service_id, o.api_provider_id, o.api_service_id, o.api_order_id, o.status, o.charge, o.formal_charge, o.profit, o.link, o.quantity, o.comments, o.remains, o.start_counter, o.created, o.note, o.order_id, o.is_interval_order, o.refill');
            $this->db->select('u.email');
            $this->db->select('s.name as service_name');
            $this->db->select('api.name as api_name');
            $this->db->select('c.name as category_name');
            $this->db->from($this->tb_main . ' o');
            $this->db->join($this->tb_users." u", "o.uid = u.id", 'left');
            $this->db->join($this->tb_services." s", "s.id = o.service_id", 'left');
            $this->db->join($this->tb_api_providers." api", "api.id = o.api_provider_id", 'left');
            $this->db->join($this->tb_categories." c", "c.id = s.cate_id", 'left'); // Join with categories table
           

            // filter
            if ($params['filter']['status'] != 'all' && in_array($params['filter']['status'], $this->filter_accepted)) {
                $this->db->where('o.status', $params['filter']['status']);
            }

            $this->db->where("o.service_type !=", "subscriptions");
            $this->db->where("o.is_drip_feed !=", 1);

            // Get all orders relate to main order id
            if ($item_main_id > 0) {
                $this->db->where("o.main_order_id", $item_main_id);
            }

            //Search
            if ($params['search']['field'] === 'all') {
                $i = 1;
                foreach ($this->field_search_accepted as $column) {
                    if ($column != 'all') {
                        $column = ($column == 'email') ? 'u.'.$column : 'o.'.$column;
                        if($i == 1){
                            $this->db->like($column, $params['search']['query']); 
                        }elseif ($i > 1) {
                            $this->db->or_like($column, $params['search']['query']); 
                        }
                        $i++;
                    }
                }
            }elseif (in_array($params['search']['field'], $this->field_search_accepted) && $params['search']['query'] != "") {
                if (in_array($params['search']['field'], ['id', 'api_order_id'])) {
                    $this->db->where_in('`o`.' . $params['search']['field'], convert_str_number_list_to_array($params['search']['query']));
                } else {
                    $column = ($params['search']['field'] == 'email') ? 'u.'.$params['search']['field'] : 'o.'.$params['search']['field'];
                    $this->db->like($column, $params['search']['query']); 
                }
            }

            $this->db->order_by('id', 'desc');
            if ($params['pagination']['limit'] != "" && $params['pagination']['start'] >= 0) {
                $this->db->limit($params['pagination']['limit'], $params['pagination']['start']);
            }
            $query = $this->db->get();
            $result = $query->result_array();
        }
		
		if ($option['task'] == 'list-suborders-items') {
            $this->db->select('o.id, o.ids, o.type, o.service_type, o.service_id, o.api_provider_id, o.api_service_id, o.api_order_id, o.status, o.charge, o.formal_charge, o.profit, o.link, o.quantity, o.comments, o.remains, o.start_counter, o.created, o.start_at, o.changed, o.note, o.order_id, o.is_interval_order');
            $this->db->select('u.email');
            $this->db->select('s.name as service_name');
            $this->db->select('api.name as api_name');
            $this->db->select('c.name as category_name');
            $this->db->from('sub_orders o');
            $this->db->join($this->tb_users." u", "o.uid = u.id", 'left');
            $this->db->join($this->tb_services." s", "s.id = o.service_id", 'left');
            $this->db->join($this->tb_api_providers." api", "api.id = o.api_provider_id", 'left');
            $this->db->join($this->tb_categories." c", "c.id = s.cate_id", 'left');
            $this->db->where("o.service_type !=", "subscriptions");
            $this->db->where("o.is_drip_feed !=", 1);
			$this->db->where("o.parent_order_id", $params['parent_order_id']);
            $this->db->order_by('id', 'ASC');
            $query = $this->db->get();
            $result = $query->result_array();
        }
		
        if ($option['task'] == 'list-items-in-bulk-action') {
            $this->db->select('id, ids, cate_id, service_id, service_type, api_provider_id, api_service_id, charge, uid, quantity, status, formal_charge, profit, order_id, is_interval_order');
            $this->db->from($this->tb_main);
            $this->db->where_in('id', $params['ids_arr']);
            $query = $this->db->get();
            $result = $query->result_array();
        }

        if ($option['task'] == 'best-seller-in-statistics') {

            $query = "SELECT count(service_id) as total_orders, service_id FROM {$this->tb_main} GROUP BY service_id ORDER BY total_orders DESC LIMIT ". $params['limit'];
            $items_best_seller =  $this->db->query($query)->result_array();
            if (!$items_best_seller) {
                return $result;
            }
            $items_arr_service_id = array_column($items_best_seller, 'total_orders', 'service_id');
            $this->db->select('s.id, s.ids, s.name, s.cate_id, s.price, s.original_price, s.min, s.max, s.type, s.add_type, s.api_service_id, s.api_provider_id, s.status, s.desc, , s.refill, s.refill_type, s.dripfeed');
            $this->db->select('api.name as api_name');
            $this->db->from($this->tb_services." s");
            $this->db->join($this->tb_api_providers." api", "s.api_provider_id = api.id", 'left');
            $this->db->where_in("s.id", array_keys($items_arr_service_id));
            $this->db->where("s.status", 1);
            $query = $this->db->get();
            $result = $query->result_array();
            if ($result) {
                foreach ($result as $key => $item) {
                    if (isset($items_arr_service_id[$item['id']])) {
                        $result[$key]['total_orders'] = $items_arr_service_id[$item['id']];
                    }
                } 
                usort($result, function ($item1, $item2) {
                    return $item2['total_orders'] <=> $item1['total_orders'];
                });    
            }
        }

        // copy to clipboard
        if ($option['task'] == 'list-items-copy-to-clip-board') {
            $this->db->select('o.' . $params['get-type']);
            $this->db->from($this->tb_main . ' o');
            $this->db->where_in('o.id', $params['arr_ids']);
            $query = $this->db->get();
            $result = $query->result_array();
        }

        return $result;
    }

    public function get_item($params = null, $option = null)
    {
        $result = null;
        if($option['task'] == 'get-item'){
            $result = $this->get("id, ids, cate_id, service_id, service_type, api_provider_id, api_service_id, charge, uid, quantity, status, formal_charge, profit, start_counter, remains, link, start_at, end_at", $this->tb_main, ['id' => $params['id']], '', '', true);
        }
        return $result;
    }

    public function count_items($params = null, $option = null)
    {
        $result = null;

        if ($option['task'] == 'count-items-by-status') {
            $this->db->select("id");
            $this->db->from($this->tb_main);
            $this->db->where("status", $params['status']);
            $this->db->where("service_type !=", "subscriptions");
            $this->db->where("is_drip_feed !=", 1);
            $query = $this->db->get();
            return $query->num_rows();
        }

        // Count items for pagination
        if ($option['task'] == 'count-items-for-pagination') {
            $item_main_id = 0;
            // get uid Array
            if ($params['search']['field'] == 'email') {
                $items_uid = $this->fetch_search_items('id', $this->tb_users, '', ['field' => $params['search']['field'], 'query' => $params['search']['query']]);
                if (!$items_uid) return null;
            }
            if (get('subscription')) {
                $item_main_id = (int)get('subscription');
            } elseif (get('drip-feed')){
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
            // Get all orders relate to main order id
            if ($item_main_id > 0) {
                $this->db->where("o.main_order_id", $item_main_id);
            }
            //Search
            if ($params['search']['field'] === 'all') {
                $i = 1;
                foreach ($this->field_search_accepted as $column) {
                    if ($column != 'all') {
                        $column = ($column == 'email') ? 'u.'.$column : 'o.'.$column;
                        if($i == 1){
                            $this->db->like($column, $params['search']['query']); 
                        }elseif ($i > 1) {
                            $this->db->or_like($column, $params['search']['query']); 
                        }
                        $i++;
                    }
                }
            }elseif (in_array($params['search']['field'], $this->field_search_accepted) && $params['search']['query'] != "") {
                if (in_array($params['search']['field'], ['id', 'api_order_id'])) {
                    $this->db->where_in('`o`.' . $params['search']['field'], convert_str_number_list_to_array($params['search']['query']));
                } else {
                    // Search Email
                    if ($params['search']['field'] == 'email') {
                        $this->db->where_in("o.uid", array_column($items_uid, 'id'));
                    } else {
                        $this->db->like('o.'.$params['search']['field'], $params['search']['query']); 
                    }
                } 
            }
            $query = $this->db->get();
            $result = $query->num_rows();
        }
        return $result;
    }

    public function delete_item($params = null, $option = null)
    {
        $result = [];
        if($option['task'] == 'delete-item'){
            $item = $this->get("id, ids", $this->tb_main, ['id' => $params['id']]);
            if ($item) {
                $this->db->delete($this->tb_main, ["id" => $params['id']]);
				
				$this->db->delete('sub_orders', ["parent_order_id" => $params['id']]);
				
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
        switch ($option['task']) {
            case 'edit-item':
                $item = $this->get('id, ids, cate_id, service_id, service_type, api_provider_id, api_service_id, charge, uid, quantity, status, formal_charge, profit, is_interval_order', $this->tb_main, ['id' => post('id')], '', '', true);
                if (!$item) {
                    return ["status"  => "error", "message" => 'The item does not exists'];
                }
                $data = array(
                    "link" 	    	=> post('link'),
                    "start_counter" => post('start_counter'),
                    "remains"    	=> post('remains'),
                    "changed" 		=> NOW,
                );

                if (post('status') != "") {
                    $data['status'] = post('status');
					
					if($item['is_interval_order'] == 1){
						$data_sub_item = array(
							"link" 	    	=> post('link'),
							"start_counter" => post('start_counter'),
							"remains"    	=> post('remains'),
							"changed" 		=> NOW,
							"status"        => post('status')
						);
						$this->db->update('sub_orders', $data_sub_item, ["parent_order_id" => $item['id']]);
					}
                }
                if (in_array(post('status'), ['refunded', 'partial', 'canceled'])) {
                    staff_check_role_permission($this->controller_name, 'cancel');
                    $new_order_attr = calculate_order_by_status($item, ['status' => post('status'), 'remains' => post('remains')]);
                    if (!in_array($item['status'], array('cancelled', 'refunded'))) {
                        $response = $this->crud_user(['uid' => $item['uid'], 'fields' => 'balance', 'new_amount' => $new_order_attr['refund_money']], ['task' => 'update-balance']);
                        if (!$response) {
                            return ['status' => 'error', 'message' => 'There was some issue with your request'];
                        }
                    }
                    $data['charge']        = $new_order_attr['real_charge'];
                    $data['formal_charge'] = $new_order_attr['formal_chagre'];
                    $data['profit']        = $new_order_attr['profit'];
					
					if($item['is_interval_order'] == 1){
						if (!in_array($item['status'], array('cancelled', 'refunded'))) {
							$data_sub_item = array(
								"changed"       => date('Y-m-d H:i:s', strtotime(NOW) + $rand_time),
								"status"        => $item['status'],
								"charge" 	    => 0,
								"formal_charge" => 0,
								"profit" 	    => 0,
								"is_refunded"   => 1
							);
							$this->db->update('sub_orders', $data_sub_item, ["parent_order_id" => $item['id']]);
						} else {
							$data_sub_item = array(
								"changed"       => date('Y-m-d H:i:s', strtotime(NOW) + $rand_time),
								"status"        => $item['status'],
								"charge" 	    => 0,
								"formal_charge" => 0,
								"profit" 	    => 0,
								"is_refunded"   => 0
							);
							$this->db->update('sub_orders', $data_sub_item, ["parent_order_id" => $item['id']]);
						}
					}
                }
                $this->db->update($this->tb_main, $data, ["id" => $item['id']]);
                return ["status"  => "success", "message" => 'Update successfully'];
                break;

            case 'resend-item':
                $item = $params['item'];
                $related_service = $this->get('id, cate_id, api_provider_id, api_service_id, original_price', $this->tb_services, ['id' => $item['service_id']]);
                $data = [
                    'status'       => 'pending',
                    'note'         => 'Resent',
                    'changed'      => NOW,
                    'api_order_id' => -1,
                ];

                if (!empty($related_service)) {
                    $data['cate_id']              = $related_service->cate_id;
                    $data['service_id']           = $related_service->id;
                    $data['api_provider_id']      = $related_service->api_provider_id;
                    $data['api_service_id']       = $related_service->api_service_id;
                    $data['formal_charge']        = ($item['quantity'] * $related_service->original_price)/1000;
                    $data['profit']               = $item['charge'] - $data['formal_charge'];
                }
                $this->db->update($this->tb_main, $data, ['id' => $item['id']]);
				
				//On resend place a new order without wait of cron - 30-11-2024
				$this->db->select("*");
				$this->db->from($this->tb_main);
				$this->db->where('id',$item['id']);
				$this->db->where("is_interval_order",0);
				$row = $this->db->get()->row(); 
				
				if(!empty($row)){
					$data_post = [
						'action'  => 'add',
						'service' => $row->api_service_id,
					];
					
					$requested_qty = $row->quantity;
					$api_quantity  = $requested_qty;
					
					$is_process_order         = 1;
					$is_comment_order_process = 1;
					$comments_enabled         = 0;
					
					$this->db->select('qty_percentage, min, max, is_regex_validation, regex_validations, comments_enabled');
					$this->db->from($this->tb_services);
					$this->db->where("id",$row->service_id);
					$this->db->order_by('id','DESC');
					$get_qty_percentage = $this->db->get()->row_array(); 
					if(!empty($get_qty_percentage)){
						$qty_percentage   = ($get_qty_percentage['qty_percentage']) ? $get_qty_percentage['qty_percentage'] : 0;
						$min_qty          = ($get_qty_percentage['min']) ? $get_qty_percentage['min'] : 0;
						$max_qty          = ($get_qty_percentage['max']) ? $get_qty_percentage['max'] : 0;
						$comments_enabled = ($get_qty_percentage['comments_enabled']) ? $get_qty_percentage['comments_enabled'] : 0;
						
						if($qty_percentage){
							if($api_quantity > $max_qty){
								$api_quantity = $max_qty;
							} elseif($api_quantity < $min_qty) {
								$api_quantity = $min_qty;
							}
							
							$final_percentage = ($api_quantity * $qty_percentage) / 100;
							$api_quantity     = ceil($api_quantity + $final_percentage);
						}
						
						$is_regex_validation = ($get_qty_percentage['is_regex_validation']) ? $get_qty_percentage['is_regex_validation'] : 0;
						$regex_validations   = ($get_qty_percentage['regex_validations']) ? json_decode($get_qty_percentage['regex_validations'],true) : [];
						
						if($is_regex_validation){
							if(!empty($regex_validations)){
								$check_link = $row->link;
								if($check_link != ''){
									foreach($regex_validations as $regex_validation){
										if (!preg_match($regex_validation, $check_link)) {
											$is_process_order = 0;
										} else {
											$is_process_order = 1;
											break;
										}
									}
								}
							}
						}
						
						if($comments_enabled == 1){
							$this->db->select('id');
							$this->db->from('services_comments');
							$this->db->where("service_id",$row->service_id);
							$this->db->order_by('id','DESC');
							$check_comments_present = $this->db->get()->row_array(); 
							
							if(empty($check_comments_present)){
								$is_comment_order_process = 0;
							}
						}
					}		
					
					if($is_process_order == 1)
					{
						if($is_comment_order_process == 1)
						{
							switch ($row->service_type) {
								case 'subscriptions':
									$data_post["username"] = $row->username;
									$data_post["min"]      = $row->sub_min;
									$data_post["max"]      = $row->sub_max;
									$data_post["posts"]    = ($row->sub_posts == -1) ? 0 : $row->sub_posts;
									$data_post["delay"]    = $row->sub_delay;
									$data_post["expiry"]   = (!empty($row->sub_expiry)) ? date("d/m/Y", strtotime($row->sub_expiry)) : "";
									break;

								case 'custom_comments':
									$data_post["link"]     = $row->link;
									$data_post["comments"] = json_decode($row->comments);
									break;

								case 'mentions_with_hashtags':
									$data_post["link"]      = $row->link;
									$data_post["quantity"]  = $api_quantity;
									$data_post["usernames"] = $row->usernames;
									$data_post["hashtags"]  = $row->hashtags;
									break;

								case 'mentions_custom_list':
									$data_post["link"]      = $row->link;
									$data_post["usernames"] = json_decode($row->usernames);
									break;

								case 'mentions_hashtag':
									$data_post["link"]     = $row->link;
									$data_post["quantity"] = $api_quantity;
									$data_post["hashtag"]  = $row->hashtag;
									break;

								case 'mentions_user_followers':
									$data_post["link"]     = $row->link;
									$data_post["quantity"] = $api_quantity;
									$data_post["username"] = $row->username;
									break;

								case 'mentions_media_likers':
									$data_post["link"]     = $row->link;
									$data_post["quantity"] = $api_quantity;
									$data_post["media"]    = $row->media;
									break;

								case 'package':
									$data_post["link"] = $row->link;
									break;

								case 'custom_comments_package':
									$data_post["link"]     = $row->link;
									$data_post["comments"] = json_decode($row->comments);
									break;

								case 'comment_likes':
									$data_post["link"]     = $row->link;
									$data_post["quantity"] = $api_quantity;
									$data_post["username"] = $row->username;
									break;

								default:
									if($comments_enabled == 1){
										$this->db->select('comment');
										$this->db->from('services_comments');
										$this->db->where("service_id",$row->service_id);
										$this->db->order_by('RAND()');
										$this->db->limit($api_quantity);
										$getRandomComments = $this->db->get()->result_array(); 
										
										$comments = [];
										if(!empty($getRandomComments)){
											foreach($getRandomComments as $getRandomComment){
												$comments[] = $getRandomComment['comment'];
											}
										}
										
										$data_post["link"]     = $row->link;
										$data_post["quantity"] = $api_quantity;
										
										if(!empty($api) && isset($api['id']) && $api['id'] == 2){
											$data_post["comments"] = ($comments) ? json_encode($comments) : [];
										} else {
											$data_post["comments"] = ($comments) ? implode("\n",$comments) : '';
										}
									} else {
										$data_post["link"]     = $row->link;
										$data_post["quantity"] = $api_quantity;
										
										if(!empty($api) && isset($api['id']) && $api['id'] == 2){
											$data_post["comments"] = ($row->comments) ? json_decode($row->comments) : [];
										}
									}
									
									if (isset($row->is_drip_feed) && $row->is_drip_feed == 1) {
										$data_post["runs"]     = $row->runs;
										$data_post["interval"] = $row->interval;
										$data_post["quantity"] = $row->dripfeed_quantity;
									} else {
										$data_post["quantity"] = $api_quantity;
									}
									break;
							}
							
							$balanceCheck = $this->provider->balance($api);
							if ($balanceCheck && isset($balanceCheck['balance'])) 
							{
								if($balanceCheck['balance'] > 0)
								{
									$response = $this->provider->order($api, $data_post);
									
									if(!empty($response) && isset($response['order']) && $response['order'] != '')
									{
										$this->save_instant_order_item(['order_id' => $row->id, 'response' => $response, 'api_quantity' => $api_quantity], ['task' => 'item-new-update']);	
									}
								}	
							} 
						}
						else 
						{
							$rand_time      = get_random_time();
							$params         = array('charge' => $row->charge,'remains' => 0);
							$new_order_attr = calculate_order_by_status($params, ['status' => 'canceled']);
							$this->cancel_order_regex_instant_order($row->uid,$new_order_attr['refund_money']);
							
							$data_item = array(
								"changed"       => date('Y-m-d H:i:s', strtotime(NOW) + $rand_time),
								"status"        => 'canceled',
								"note" 	        => "Some data is missing!!!",
								"charge" 	    => $new_order_attr['real_charge'],
								"formal_charge" => $new_order_attr['formal_chagre'],
								"profit" 	    => $new_order_attr['profit']
							);
							$this->db->update($this->tb_main, $data_item, ["id" => $row->id]);
						}
					}
					else
					{
						$rand_time      = get_random_time();
						$params         = array('charge' => $row->charge,'remains' => 0);
						$new_order_attr = calculate_order_by_status($params, ['status' => 'canceled']);
						$this->cancel_order_regex_instant_order($row->uid,$new_order_attr['refund_money']);
						
						$data_item = array(
							"changed"       => date('Y-m-d H:i:s', strtotime(NOW) + $rand_time),
							"status"        => 'canceled',
							"note" 	        => "Not a valid URL",
							"charge" 	    => $new_order_attr['real_charge'],
							"formal_charge" => $new_order_attr['formal_chagre'],
							"profit" 	    => $new_order_attr['profit']
						);
						$this->db->update($this->tb_main, $data_item, ["id" => $row->id]);
					}
				}
				//On resend place a new order without wait of cron - 30-11-2024
				
				$checkItem = $this->get('id, is_interval_order', $this->tb_main, ['id' => $item['id']], '', '', true);
				if($checkItem['is_interval_order'] == 1){
					$this->db->select('id');
					$this->db->from('sub_orders');
					$this->db->where("parent_order_id",$item['id']);
					$this->db->order_by("id", 'ASC');
					$getSubOrders = $this->db->get()->result_array(); 
					if(!empty($getSubOrders)){
						
						//On resend place a new order without wait of cron - 30-11-2024
						// set sub order to default state
						
						$first_sub_order_id = 0;
						
						foreach($getSubOrders as $key => $subOrders){
							if($key == 0){
								$first_sub_order_id = $subOrders['id'];
								
								$data_sub_item = array(
									'status'       => 'pending',
									'note'         => 'Resent',
									'changed'      => NOW,
									'start_at'     => NOW,
									'api_order_id' => -1,
								);
								$this->db->update('sub_orders', $data_sub_item, ["id" => $subOrders['id']]);
							} else {
								$data_sub_item = array(
									'status'       => 'pending',
									'note'         => 'Resent',
									'changed'      => null,
									'start_at'     => null,
									'api_order_id' => -1,
								);
								$this->db->update('sub_orders', $data_sub_item, ["id" => $subOrders['id']]);
							}
						} 
						// set sub order to default state
						
						if($first_sub_order_id)
						{
							$this->db->select("*");
							$this->db->from('sub_orders');
							$this->db->where("id",$first_sub_order_id);
							$this->db->where("is_interval_order",1);
							$this->db->where('start_at is NOT NULL', NULL, FALSE);
							$this->db->where('start_at <', NOW);
							$row = $this->db->get()->row(); 
							
							if(!empty($row))
							{
								$data_post = [
									'action'  => 'add',
									'service' => $row->api_service_id,
								];
								
								$requested_qty = $row->quantity;
								$api_quantity  = $requested_qty;
								
								$is_process_order = 1;
								
								$this->db->select('qty_percentage, min, max, is_regex_validation, regex_validations, is_repeat_interval, runs, interval');
								$this->db->from($this->tb_services);
								$this->db->where("id",$row->service_id);
								$this->db->order_by('id','DESC');
								$get_qty_percentage = $this->db->get()->row_array(); 
								
								$is_repeat_interval = 0;
								$runs_interval      = 0;
								$interval_minutes   = 0;
								
								if(!empty($get_qty_percentage))
								{
									$qty_percentage     = ($get_qty_percentage['qty_percentage']) ? $get_qty_percentage['qty_percentage'] : 0;
									$min_qty            = ($get_qty_percentage['min']) ? $get_qty_percentage['min'] : 0;
									$max_qty            = ($get_qty_percentage['max']) ? $get_qty_percentage['max'] : 0;
									
									$is_repeat_interval = ($get_qty_percentage['is_repeat_interval']) ? $get_qty_percentage['is_repeat_interval'] : 0;
									$runs_interval      = ($get_qty_percentage['runs']) ? $get_qty_percentage['runs'] : 0;
									$interval_minutes   = ($get_qty_percentage['interval']) ? $get_qty_percentage['interval'] : 0;
									
									if($qty_percentage){
										$final_percentage = ($requested_qty * $qty_percentage) / 100;
										$api_quantity     = ceil($requested_qty + $final_percentage);
										
										if($api_quantity > $max_qty){
											$api_quantity = $max_qty;
										} elseif($api_quantity < $min_qty) {
											$api_quantity = $min_qty;
										}
									}
									
									$is_regex_validation = ($get_qty_percentage['is_regex_validation']) ? $get_qty_percentage['is_regex_validation'] : 0;
									$regex_validations   = ($get_qty_percentage['regex_validations']) ? json_decode($get_qty_percentage['regex_validations'],true) : [];
									
									if($is_regex_validation)
									{
										if(!empty($regex_validations)){
											$check_link = $row->link;
											if($check_link != ''){
												foreach($regex_validations as $regex_validation){
													if (!preg_match($regex_validation, $check_link)) {
														$is_process_order = 0;
													} else {
														$is_process_order = 1;
														break;
													}
												}
											}
										}
									}
								}		
								
								if($is_process_order == 1)
								{
									switch ($row->service_type) {
										case 'subscriptions':
											$data_post["username"] = $row->username;
											$data_post["min"]      = $row->sub_min;
											$data_post["max"]      = $row->sub_max;
											$data_post["posts"]    = ($row->sub_posts == -1) ? 0 : $row->sub_posts;
											$data_post["delay"]    = $row->sub_delay;
											$data_post["expiry"]   = (!empty($row->sub_expiry)) ? date("d/m/Y", strtotime($row->sub_expiry)) : ""; //change date format dd/mm/YYYY
											break;

										case 'custom_comments':
											$data_post["link"]     = $row->link;
											$data_post["comments"] = json_decode($row->comments);
											break;

										case 'mentions_with_hashtags':
											$data_post["link"]      = $row->link;
											$data_post["quantity"]  = $api_quantity;
											$data_post["usernames"] = $row->usernames;
											$data_post["hashtags"]  = $row->hashtags;
											break;

										case 'mentions_custom_list':
											$data_post["link"]      = $row->link;
											$data_post["usernames"] = json_decode($row->usernames);
											break;

										case 'mentions_hashtag':
											$data_post["link"]     = $row->link;
											$data_post["quantity"] = $api_quantity;
											$data_post["hashtag"]  = $row->hashtag;
											break;

										case 'mentions_user_followers':
											$data_post["link"]     = $row->link;
											$data_post["quantity"] = $api_quantity;
											$data_post["username"] = $row->username;
											break;

										case 'mentions_media_likers':
											$data_post["link"]     = $row->link;
											$data_post["quantity"] = $api_quantity;
											$data_post["media"]    = $row->media;
											break;

										case 'package':
											$data_post["link"] = $row->link;
											break;

										case 'custom_comments_package':
											$data_post["link"]     = $row->link;
											$data_post["comments"] = json_decode($row->comments);
											break;

										case 'comment_likes':
											$data_post["link"]     = $row->link;
											$data_post["quantity"] = $api_quantity;
											$data_post["username"] = $row->username;
											break;

										default:
											$data_post["link"]     = $row->link;
											$data_post["quantity"] = $api_quantity;
											
											if(!empty($api) && isset($api['id']) && $api['id'] == 2){
												$data_post["comments"] = ($row->comments) ? json_decode($row->comments) : [];
											}
											
											if (isset($row->is_drip_feed) && $row->is_drip_feed == 1) {
												$data_post["runs"]     = $row->runs;
												$data_post["interval"] = $row->interval;
												$data_post["quantity"] = $row->dripfeed_quantity;
											} else {
												$data_post["quantity"] = $api_quantity;
											}
											break;
									}
									
									$balanceCheck = $this->provider->balance($api);
									if ($balanceCheck && isset($balanceCheck['balance'])) 
									{
										if($balanceCheck['balance'] > 0)
										{
											$response = $this->provider->order($api, $data_post);
											
											if(!empty($response) && isset($response['order']) && $response['order'] != '')
											{
												$this->save_instant_order_item(['order_id' => $row->id, 'response' => $response, 'api_quantity' => $api_quantity], ['task' => 'item-new-sub-order-update']);	
												
												$this->db->select('id');
												$this->db->from('sub_orders');
												$this->db->where("parent_order_id",$row->parent_order_id);
												$this->db->order_by("id", 'ASC');
												$this->db->limit(1);
												$getFirstOrderStartTime = $this->db->get()->row(); 
												
												if($row->id == $getFirstOrderStartTime->id){
													$data_item = array(
														"start_at" => date('Y-m-d H:i:s')
													);
													$this->db->update('orders', $data_item, ["id" => $row->parent_order_id]);
												}
												
												$this->db->select('id');
												$this->db->from('sub_orders');
												$this->db->where("parent_order_id",$row->parent_order_id);
												$this->db->where("id >",$row->id);
												$this->db->order_by('id','ASC');
												$this->db->limit(1);
												$getNextOrderId = $this->db->get()->row(); 
												
												if(!empty($getNextOrderId)){
													$new_changed_date = date("Y-m-d H:i:s",strtotime('+'.$interval_minutes.' minutes'));
													
													$data_item = array(
														"start_at" => $new_changed_date
													);
													$this->db->update('sub_orders', $data_item, ["id" => $getNextOrderId->id]);
												}
											}
										}	
									} 
								}
								else
								{
									$this->db->select('id,charge,uid');
									$this->db->from('orders');
									$this->db->where("id",$row->parent_order_id);
									$getParentRecord = $this->db->get()->row(); 
									
									if($getParentRecord)
									{
										$rand_time      = get_random_time();
										$params         = array('charge' => $getParentRecord->charge,'remains' => 0);
										$new_order_attr = calculate_order_by_status($params, ['status' => 'canceled']);
										$this->cancel_order_regex_instant_order($getParentRecord->uid,$new_order_attr['refund_money']);
										
										$data_item = array(
											"changed"       => date('Y-m-d H:i:s', strtotime(NOW) + $rand_time),
											"status"        => 'canceled',
											"note" 	        => "Not a valid URL",
											"charge" 	    => $new_order_attr['real_charge'],
											"formal_charge" => $new_order_attr['formal_chagre'],
											"profit" 	    => $new_order_attr['profit']
										);
										$this->db->update('orders', $data_item, ["id" => $getParentRecord->id]);

										$data_sub_item = array(
											"changed"       => date('Y-m-d H:i:s', strtotime(NOW) + $rand_time),
											"status"        => 'canceled',
											"note" 	        => "Not a valid URL",
											"charge" 	    => $new_order_attr['real_charge'],
											"formal_charge" => $new_order_attr['formal_chagre'],
											"profit" 	    => $new_order_attr['profit'],
											"is_refunded"   => 1
										);
										$this->db->update('sub_orders', $data_sub_item, ["parent_order_id" => $row->parent_order_id]);
									}
								}
							}
						}
						
						//On resend place a new order without wait of cron - 30-11-2024
					}
				}
				
                return ["status"  => "success", "message" => 'Update successfully'];
                break;

            case 'bulk-action':
                
                if (in_array($params['type'], ['delete', 'deactive', 'active']) && empty($params['ids'])) {
                    return ["status"  => "error", "message" => 'Please choose at least one item'];
                }
                $arr_ids = convert_str_number_list_to_array($params['ids']);
                switch ($params['type']) {
                    case 'delete':
                        $this->db->where_in('id', $arr_ids);
                        $this->db->delete($this->tb_main);
						
						$this->db->where_in('parent_order_id', $arr_ids);
                        $this->db->delete('sub_orders');
						
                        return ["status"  => "success", "message" => 'Update successfully'];
                        break;
                    case 'resend':
                        $items = $this->list_items(['ids_arr' => $arr_ids], ['task' => 'list-items-in-bulk-action']);
                        if ($items) {
                            foreach ($items as $key => $item) {
                                $this->save_item(['item' => $item], ['task' => 'resend-item']);
                            }
                        }
                        return ["status"  => "success", "message" => 'Update successfully'];
                        break;
                    case 'cancel':
                        staff_check_role_permission($this->controller_name, 'cancel');
                        $items = $this->list_items(['ids_arr' => $arr_ids], ['task' => 'list-items-in-bulk-action']);
                        if ($items) {
                            foreach ($items as $key => $item) {
                                $new_order_attr = calculate_order_by_status($item, ['status' => 'canceled', 'remains' => $item['quantity']]);
                                if (!in_array($item['status'], array('cancelled', 'refunded'))) {
                                    $response = $this->crud_user(['uid' => $item['uid'], 'fields' => 'balance', 'new_amount' => $new_order_attr['refund_money']], ['task' => 'update-balance']);
                                }
                                $data = [
                                    'status'       => 'canceled',
                                    'charge'        => 0,
                                    'formal_charge' => 0,
                                    'profit'        => 0,
                                    'remains'       => '',
                                    'changed'       => NOW,
                                ];
                                $this->db->update($this->tb_main, $data, ['id' => $item['id']]);
								
								if($item['is_interval_order'] == 1)
								{
									if (!in_array($item['status'], array('cancelled', 'refunded'))) {
										$data_sub_item = array(
											'status'       => 'canceled',
											'charge'        => 0,
											'formal_charge' => 0,
											'profit'        => 0,
											'remains'       => '',
											'changed'       => NOW,
											"is_refunded"   => 1
										);
										$this->db->update('sub_orders', $data_sub_item, ["parent_order_id" => $item['id']]);
									} else {
										$data_sub_item = array(
											'status'       => 'canceled',
											'charge'        => 0,
											'formal_charge' => 0,
											'profit'        => 0,
											'remains'       => '',
											'changed'       => NOW,
											"is_refunded"   => 0
										);
										$this->db->update('sub_orders', $data_sub_item, ["parent_order_id" => $item['id']]);
									}
								}
                            }
                        }
                        return ["status"  => "success", "message" => 'Update successfully'];
                        break;
                    default:
                        //copy to clipboard
                        if (in_array($params['type'], $this->bulk_actions_copy_clipboard_accepted)) {
                            $params_tmp = [
                                'arr_ids' => $arr_ids, 
                                'get-type' => str_replace('copy_', '', $params['type'])
                            ];
                            $order_ids = $this->list_items($params_tmp, ['task' => 'list-items-copy-to-clip-board']);
                            if ($order_ids) {
                                $order_ids = array_column($order_ids, $params_tmp['get-type']);
                                $order_ids = implode(',', $order_ids);
                                return ["status"  => "success", "value" => $order_ids];
                            } else {
                                return ["status"  => "error", "value" => 'There was issue with your request'];
                            }
                        }
                        // Action: In progress, Completed, Pending
                        if (in_array($params['type'], ['pending', 'completed', 'inprogress'])) {
                            staff_check_role_permission($this->controller_name, 'change_status');
                            $this->db->where_in('id', $arr_ids);
                            $this->db->update($this->tb_main, ['status' => $params['type'], 'changed' => NOW]);
                            return ["status"  => "success", "message" => 'Update successfully'];
                        }
                        break;
                }
                break;
        }
    }
	
	public function save_instant_order_item($params = null, $option = null)
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
                if (strtolower($params['response']['status']) == "completed" && $item['refill'] && is_table_exists($this->tb_orders_refill)) {
                    $data_item['refill_status'] = 'completed';
                    $data_item['refill_date'] = date('Y-m-d H:i:s', strtotime(NOW) + 86400); //next refill request 24h
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
                    $data_item['refill_status'] = 'completed';
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
	
	public function cancel_order_regex_instant_order($uid,$refund_money)
	{
		$this->crud_user(['uid' => $uid, 'fields' => 'balance', 'new_amount' => $refund_money], ['task' => 'update-balance']);
	}
}
