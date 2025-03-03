<?php 
defined('BASEPATH') or exit('No direct script access allowed');

class cron extends MX_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model(get_class($this) . '_model', 'main_model');
        $this->cron_token();
		
		$this->tb_services = SERVICES;
		$this->tb_order    = ORDER;
		
        $this->provider    = new Smm_api();
    }

    public function index()
    {
        redirect(cn());
    }

    public function status()
    {
		// $inserCronLog['created_at']  = date("Y-m-d H:i:s");
		// $inserCronLog['cron_type']   = 'status';
		// $inserCronLog['cron_server'] = 'aapanel';
		// $this->db->insert('cron_logs',$inserCronLog);
		
        $lock = fopen('_lock_file_status.lock', 'w');
        if (!($lock && flock($lock, LOCK_EX | LOCK_NB)))
            die('Order already running');
        $params = [
            'limit' => 15,
            'start' => 0,
        ];
        $items = $this->main_model->list_items($params, ['task' => 'list-items-status']);
        if (!$items) {
            echo "There is no order at the present.<br>";
            exit();
        }
		
        foreach ($items as $key => $item) {
            $api = $this->main_model->get_item(['id' => $item['api_provider_id']], ['task' => 'get-item-provider']);
            if (!$api) {
                $response = ['error' => "API Provider does not exists"];
                $this->main_model->save_item(['item' => $item, 'response' => $response], ['task' => 'item-status']);
                continue;
            }
            $response = $this->provider->status($api, $item['api_order_id']);
            $this->main_model->save_item(['item' => $item, 'response' => $response], ['task' => 'item-status']);
        }
        echo "Successfully";
    }

    public function dripfeed()
    {
		// $inserCronLog['created_at']  = date("Y-m-d H:i:s");
		// $inserCronLog['cron_type']   = 'dripfeed';
		// $inserCronLog['cron_server'] = 'aapanel';
		// $this->db->insert('cron_logs',$inserCronLog);
		
        $lock = fopen('_lock_file_dripfeed.lock', 'w');
        if (!($lock && flock($lock, LOCK_EX | LOCK_NB)))
            die('Dripfeed already running');
        $params = [
            'limit' => 15,
            'start' => 0,
        ];
        $items = $this->main_model->list_items($params, ['task' => 'list-items-dripfeed-status']);
        if (!$items) {
            echo "There is no order at the present.<br>";
            exit();
        }
        foreach ($items as $key => $item) {
            $api = $this->main_model->get_item(['id' => $item['api_provider_id']], ['task' => 'get-item-provider']);
            if (!$api) {
                $response = ['error' => "API Provider does not exists"];
                $this->main_model->save_item(['order_id' => $item['id'], 'response' => $response], ['task' => 'item-dripfeed-status']);
                continue;
            }
            $response = $this->provider->status($api, $item['api_order_id']);
            $this->main_model->save_item(['item' => $item, 'item_api' => $api, 'response' => $response], ['task' => 'item-dripfeed-status']);
        }
        echo "Successfully";
    }

    public function subscriptions()
    {
		// $inserCronLog['created_at']  = date("Y-m-d H:i:s");
		// $inserCronLog['cron_type']   = 'subscriptions';
		// $inserCronLog['cron_server'] = 'aapanel';
		// $this->db->insert('cron_logs',$inserCronLog);
		
        $lock = fopen('_lock_file_subscriptions.lock', 'w');
        if (!($lock && flock($lock, LOCK_EX | LOCK_NB)))
            die('Subscriptions already running');
        $params = [
            'limit' => 15,
            'start' => 0,
        ];
        $items = $this->main_model->list_items($params, ['task' => 'list-items-subscriptions-status']);
        if (!$items) {
            echo "There is no order at the present.<br>";
            exit();
        }
        foreach ($items as $key => $item) {
            $api = $this->main_model->get_item(['id' => $item['api_provider_id']], ['task' => 'get-item-provider']);
            if (!$api) {
                $response = ['error' => "API Provider does not exists"];
                $this->main_model->save_item(['order_id' => $item['id'], 'response' => $response], ['task' => 'item-subscriptions-status']);
                continue;
            }
            $response = $this->provider->status($api, $item['api_order_id']);
            $this->main_model->save_item(['item' => $item, 'item_api' => $api, 'response' => $response], ['task' => 'item-subscriptions-status']);
        }
        echo "Successfully";
    }

    public function multiple_status()
    {
		// $inserCronLog['created_at']  = date("Y-m-d H:i:s");
		// $inserCronLog['cron_type']   = 'multiple_status';
		// $inserCronLog['cron_server'] = 'aapanel';
		// $this->db->insert('cron_logs',$inserCronLog);
		
        $lock = fopen('_lock_file_multiple_status.lock', 'w');
        if (!($lock && flock($lock, LOCK_EX | LOCK_NB)))
            die('Orders already running');
        $params = [
            'limit' => 100,
            'start' => 0,
        ];
        $items = $this->main_model->list_items($params, ['task' => 'list-items-multiple-status']);
        if (!$items) {
            echo "There is no order at the present.<br>";
            exit();
        }

        $items_group_by_api = group_by_criteria($items, 'api_provider_id');
        foreach ($items_group_by_api as $api_id => $items_group) {
            $api = $this->main_model->get_item(['id' => $api_id], ['task' => 'get-item-provider']);
            if (!$api) {
                $response = ['error' => "API Provider does not exists"];
                $params = [
                    'order_ids' => array_column($items_group, 'id'),
                    'response' => $response,
                ];
                $this->main_model->save_item($params, ['task' => 'item-multiple_status']);
                continue;
            } else {

                $response = $this->provider->multiStatus($api, array_column($items_group, 'api_order_id'));
                if ($response) {
                    $exist_items = [];
                    foreach ($items_group as $key => $item) {
                        if (isset($response[$item['api_order_id']]) && !in_array($item['api_order_id'], $exist_items)) {
                            $this->main_model->save_item(['item' => $item, 'response' => $response[$item['api_order_id']]], ['task' => 'item-status']);
                            $exist_items[] = $item['api_order_id'];
                        }
                    }
                }
            }
        }
        echo "Successfully";
    }

    //Send
    public function order()
    {
		// $inserCronLog['created_at']  = date("Y-m-d H:i:s");
		// $inserCronLog['cron_type']   = 'order';
		// $inserCronLog['cron_server'] = 'aapanel';
		// $this->db->insert('cron_logs',$inserCronLog);
		
        $lock = fopen('_lock_file_multiple_order.lock', 'w');
        if (!($lock && flock($lock, LOCK_EX | LOCK_NB)))
            die('Order already running');
		
        $items = $this->main_model->list_items('', ['task' => 'list-items-new-order']);
        if (!$items) {
            echo "There is no order at the present.<br>";
            exit();
        }
		
        foreach ($items as $key => $row) 
		{
            $api = $this->main_model->get_item(['id' => $row->api_provider_id], ['task' => 'get-item-provider']);
            if (!$api) {
                $response = ['error' => "API Provider does not exists"];
                $this->main_model->save_item(['order_id' => $row->id, 'response' => $response], ['task' => 'item-new-update']);
                continue;
            }
			
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
							// $inserCronLog['created_at']   = date("Y-m-d H:i:s");
							// $inserCronLog['request_data'] = json_encode($data_post);
							// $this->db->insert('cron_logs',$inserCronLog);
							
							$response = $this->provider->order($api, $data_post);
							
							if(!empty($response) && isset($response['order']) && $response['order'] != '')
							{
								$this->main_model->save_item(['order_id' => $row->id, 'response' => $response, 'api_quantity' => $api_quantity], ['task' => 'item-new-update']);	
							}
						}	
					} 
				}
				else 
				{
					$rand_time      = get_random_time();
					$params         = array('charge' => $row->charge,'remains' => 0);
					$new_order_attr = calculate_order_by_status($params, ['status' => 'canceled']);
					$this->main_model->cancel_order_regex($row->uid,$new_order_attr['refund_money']);
					
					$data_item = array(
						"changed"       => date('Y-m-d H:i:s', strtotime(NOW) + $rand_time),
						"status"        => 'canceled',
						"note" 	        => "Some data is missing!!!",
						"charge" 	    => $new_order_attr['real_charge'],
						"formal_charge" => $new_order_attr['formal_chagre'],
						"profit" 	    => $new_order_attr['profit']
					);
					$this->db->update($this->tb_order, $data_item, ["id" => $row->id]);
				}
			}
			else
			{
				$rand_time      = get_random_time();
				$params         = array('charge' => $row->charge,'remains' => 0);
				$new_order_attr = calculate_order_by_status($params, ['status' => 'canceled']);
				$this->main_model->cancel_order_regex($row->uid,$new_order_attr['refund_money']);
				
				$data_item = array(
                    "changed"       => date('Y-m-d H:i:s', strtotime(NOW) + $rand_time),
                    "status"        => 'canceled',
					"note" 	        => "Not a valid URL",
					"charge" 	    => $new_order_attr['real_charge'],
					"formal_charge" => $new_order_attr['formal_chagre'],
					"profit" 	    => $new_order_attr['profit']
                );
				$this->db->update($this->tb_order, $data_item, ["id" => $row->id]);
			}
        }
        echo "Successfully";
    }

	public function sub_order()
	{
		$lock = fopen('_lock_file_multiple_sub_order.lock', 'w');
        if (!($lock && flock($lock, LOCK_EX | LOCK_NB)))
            die('Order already running');
		
        $items = $this->main_model->list_items('', ['task' => 'list-items-new-sub-order']);
        if (!$items) {
            echo "There is no order at the present.<br>";
            exit();
        }
		
        foreach ($items as $key => $row) 
		{
            $api = $this->main_model->get_item(['id' => $row->api_provider_id], ['task' => 'get-item-provider']);
            if (!$api) {
                $response = ['error' => "API Provider does not exists"];
                $this->main_model->save_item(['parent_order_id' => $row->parent_order_id, 'response' => $response], ['task' => 'item-new-sub-order-multiple-update']);
                continue;
            }
			
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
							$this->main_model->save_item(['order_id' => $row->id, 'response' => $response, 'api_quantity' => $api_quantity], ['task' => 'item-new-sub-order-update']);	
							
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
					$this->main_model->cancel_order_regex($getParentRecord->uid,$new_order_attr['refund_money']);
					
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
        echo "Successfully";
	}
	
	public function suborder_status()
    {
        $lock = fopen('_lock_file_suborder_status.lock', 'w');
        if (!($lock && flock($lock, LOCK_EX | LOCK_NB)))
            die('Order already running');
        $params = [
            'limit' => 15,
            'start' => 0,
        ];
        $items               = $this->main_model->list_items($params, ['task' => 'list-items-suborders-status']);
		$pending_to_complete = $this->main_model->list_items($params, ['task' => 'list-items-suborders-pending-to-complete']);
		
        if (!$items && !$pending_to_complete) {
            echo "There is no order at the present.<br>";
            exit();
        }
		
		if(!empty($items))
		{
			foreach ($items as $key => $item) 
			{
				$api = $this->main_model->get_item(['id' => $item['api_provider_id']], ['task' => 'get-item-provider']);
				if (!$api) {
					$response = ['error' => "API Provider does not exists"];
					$this->main_model->save_item(['item' => $item, 'response' => $response], ['task' => 'item-suborder-status']);
					continue;
				}
				$response = $this->provider->status($api, $item['api_order_id']);
				$this->main_model->save_item(['item' => $item, 'response' => $response], ['task' => 'item-suborder-status']);
				
				// $inserCronLog = array();
				// $inserCronLog['cron_type']    = "API Order ID : ".$item['api_order_id'];
				// $inserCronLog['cron_server']  = "Order ID : ".$item['order_id']. ' Parent Order ID : '.$item['parent_order_id'];
				// $inserCronLog['created_at']   = date("Y-m-d H:i:s");
				// $inserCronLog['request_data'] = json_encode($response);
				// $this->db->insert('cron_logs',$inserCronLog);
				
				$order_id        = $item['id'];
				$parent_order_id = $item['parent_order_id'];
				$status          = order_status_format($response['status']);
				
				if(isset($order_id) && isset($parent_order_id) && isset($status)){
					$this->main_model->updateParentOrderStatus($order_id,$parent_order_id,$status);
				}
			}
		}
		
		if(!empty($pending_to_complete))
		{
			foreach($pending_to_complete as $key => $item)
			{	
				$this->db->select('id,start_counter,remains,note,status');
				$this->db->from('sub_orders');
				$this->db->where("parent_order_id",$item['id']);
				$this->db->where("status",'completed');
				$this->db->order_by("id", 'DESC');
				$this->db->limit(1);
				$getLastCompletedOrder = $this->db->get()->row(); 
				
				if($getLastCompletedOrder)
				{
					$this->db->select('id,start_at,service_id');
					$this->db->from('sub_orders');
					$this->db->where("parent_order_id",$item['id']);
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
							$this->db->update('orders', $data_item, ["id" => $item['id']]);	
						}
					}
				}
			}
		}
        
        echo "Successfully";
    }
	
	public function refill_orders()
	{
		$lock = fopen('_lock_file_refill_status.lock', 'w');
        if (!($lock && flock($lock, LOCK_EX | LOCK_NB)))
            die('Order Refilling Already in Process');
		
		$currentTime = date('Y-m-d H:i');
		//$currentTime = date('Y-m-d H:i',strtotime('2025-02-08 15:00'));
		
		//get refill enabled orders only
		$this->db->select('id,order_id,api_order_id,api_provider_id');
		$this->db->from('orders');
		$this->db->where("refill",1);
		$this->db->where_not_in("refill_status",[3,2,4,5,7]);
		$this->db->where('refill_date is NOT NULL', NULL, FALSE);
		$this->db->where("DATE_FORMAT(refill_date, '%Y-%m-%d %H:%i') =", $currentTime);
		$this->db->limit(20, 0);
		$this->db->order_by('rand()');
		$items = $this->db->get()->result_array(); 
		
		if (!$items) {
            echo "There is no refill order at the present.<br>";
            exit();
        }
		
		foreach($items as $getRefillOrder){
			$id              = $getRefillOrder['id'];
			$order_id        = $getRefillOrder['order_id'];
			$api_order_id    = $getRefillOrder['api_order_id'];
			$api_provider_id = $getRefillOrder['api_provider_id'];
			
			$api = $this->main_model->get_item(['id' => $api_provider_id], ['task' => 'get-item-provider']);
            if (!$api) {
                $response  = ['error' => "API Provider does not exists"];
				$data_item = array(
					"response" => $response
				);
				$this->db->update('orders', $data_item, ["id" => $id]);	
                continue;
            }
            $response = $this->provider->refill($api, $api_order_id);
			
			if(!empty($response)){
				if(isset($response['error'])){
					//nothing should happen
					if(strtolower($response['error']) == strtolower('Refill days are over'))
					{
						$data_item = array(
							"refill_status" => 7
						);
						$this->db->update('orders', $data_item, ["id" => $id]);	
					}
				} elseif(isset($response['refill'])) {
					$refill_id = $response['refill'];
					
					$insertRefillOrders                 = array();
					$insertRefillOrders['order_tbl_id'] = $id;
					$insertRefillOrders['order_id']     = $order_id;
					$insertRefillOrders['api_order_id'] = $api_order_id;
					$insertRefillOrders['refill_id']    = $refill_id;
					$insertRefillOrders['created_at']   = date("Y-m-d H:i:s");
					$insertRefillOrders['created_at']   = date("Y-m-d H:i:s");
					$this->db->insert('refill_orders',$insertRefillOrders);
					
					$data_item = array(
						"refill_status" => 1,
						"refill_date"   => date('Y-m-d H:i:s', strtotime(NOW) + 86460)
					);
					$this->db->update('orders', $data_item, ["id" => $id]);	
				}
			} else {
				$data_item = array(
					"refill_status" => 5
				);
				$this->db->update('orders', $data_item, ["id" => $id]);	
			}
		}
		
		echo "Successfully";
	}
	
	public function refill_order_status()
	{
		$lock = fopen('_lock_file_refill_order_status.lock', 'w');
        if (!($lock && flock($lock, LOCK_EX | LOCK_NB)))
            die('Order Refilling Status Already in Process');

		$currentTime = date('Y-m-d H:i', strtotime('+5 minutes'));		
		//$currentTime = '2025-02-12 15:54';
		
		$this->db->select('id,order_id,api_order_id,api_provider_id');
		$this->db->from('orders');
		$this->db->where("refill",1);
		$this->db->where_not_in("refill_status",[3,2,4,5,7]);
		$this->db->where('refill_date is NOT NULL', NULL, FALSE);
		$this->db->where("DATE_FORMAT(refill_date, '%Y-%m-%d %H:%i') =", $currentTime);
		$this->db->limit(20, 0);
		$this->db->order_by('rand()');
		$items = $this->db->get()->result_array(); 
		
		if (!$items) {
            echo "There is no refill order at the present.<br>";
            exit();
        }
		
        foreach ($items as $key => $item) {
			$id              = $item['id'];
			$order_id        = $item['order_id'];
			$api_order_id    = $item['api_order_id'];
			$api_provider_id = $item['api_provider_id'];
			
			$api = $this->main_model->get_item(['id' => $api_provider_id], ['task' => 'get-item-provider']);
            if (!$api) {
                $response  = ['error' => "API Provider does not exists"];
				$data_item = array(
					"response" => $response
				);
				$this->db->update('orders', $data_item, ["id" => $id]);	
                continue;
            }
			
			$this->db->select('refill_id');
			$this->db->from('refill_orders');
			$this->db->where("order_tbl_id",$id);
			$this->db->where("order_id",$order_id);
			$this->db->where("api_order_id",$api_order_id);
			$this->db->limit(1);
			$this->db->order_by('id','DESC');
			$refill_response = $this->db->get()->row_array(); 
			
			if(!empty($refill_response))
			{
				$refill_id = $refill_response['refill_id'];
				
				$response = $this->provider->refill_status($api, $refill_id);	
				
				if(!empty($response))
				{
					if(isset($response['status']) && $response['status'] != '')
					{
						if($response['status'] == 'Rejected' || $response['status'] == 'rejected')
						{
							$data_item = array(
								"refill_status" => 4
							);
							$this->db->update('orders', $data_item, ["id" => $id]);	
						}
						elseif($response['status'] == 'Completed' || $response['status'] == 'completed')
						{
							$data_item = array(
								"refill_status" => 7
							);
							$this->db->update('orders', $data_item, ["id" => $id]);	
						}
						
					}
					elseif(isset($response['error']) && $response['error'] != '')					
					{
						$data_item = array(
							"refill_status" => 7
						);
						$this->db->update('orders', $data_item, ["id" => $id]);	
					}
				}
				else
				{
					$data_item = array(
						"refill_status" => 5
					);
					$this->db->update('orders', $data_item, ["id" => $id]);	
				}
			}
        }
		
        echo "Successfully";
	}
	
    protected function cron_token() 
    {
        $cron_key =  get_cron_key();
        if ($cron_key != get('key')) {
            exit('Invalid token');
        }
        return true;
    }

}
