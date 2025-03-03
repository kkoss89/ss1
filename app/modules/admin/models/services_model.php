<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class services_model extends MY_Model 
{

    protected $tb_users;
    protected $tb_main;
    protected $filter_accepted;
    protected $field_search_accepted;

    public function __construct()
    {
        parent::__construct();
        $this->tb_main     = SERVICES;

        $this->filter_accepted = array_keys(app_config('template')['status']);
        unset($this->filter_accepted['3']);
        $this->field_search_accepted = app_config('config')['search']['services'];
    }

    public function list_items($params = null, $option = null)
    {
        $result = null;
       
        if ($option['task'] == 'list-items') {
            //$this->db->select('s.id, s.ids, s.name, s.cate_id, s.price, s.original_price, s.min, s.max, s.type, s.add_type, s.api_service_id, s.api_provider_id, s.status, s.desc, s.refill, s.refill_type, s.dripfeed, s.qty_percentage, s.is_regex_validation, s.regex_validations, s.is_repeat_interval, s.runs, s.interval,s.comments_enabled,s.previous_service_type');
			$this->db->select('s.id, s.ids, s.name, s.cate_id, s.price, s.original_price, s.min, s.max, s.type, s.add_type, s.api_service_id, s.api_provider_id, s.status, s.desc, s.refill, s.refill_type, s.dripfeed, s.qty_percentage, s.is_regex_validation, s.regex_validations, s.is_repeat_interval, s.runs, s.interval, s.comments_enabled, s.previous_service_type, api.name as api_name, c.name as category_name, c.image as category_image');
            $this->db->select('api.name as api_name, c.name as category_name');
            $this->db->from($this->tb_main." s");
            $this->db->join($this->tb_categories." c", "c.id = s.cate_id", 'left');
            $this->db->join($this->tb_api_providers." api", "s.api_provider_id = api.id", 'left');

            //Search
            if ($params['search']['field'] === 'all') {
                $i = 1;
                foreach ($this->field_search_accepted as $column) {
                    if ($column != 'all') {
                        $column = ($column == 'name') ? 's.'.$column : 's.'.$column;
                        if($i == 1){
                            $this->db->like($column, $params['search']['query']); 
                        }elseif ($i > 1) {
                            $this->db->or_like($column, $params['search']['query']); 
                        }
                        $i++;
                    }
                }
            } elseif (in_array($params['search']['field'], $this->field_search_accepted) && $params['search']['query'] != "") {
                $column = ($params['search']['field'] == 'name') ? 's.'.$params['search']['field'] : 's.'.$params['search']['field'];
                $this->db->like($column, $params['search']['query']); 
            }

            // Sort By
            if ($params['sort_by']['cate_id'] != "") {
                $this->db->where('s.cate_id', $params['sort_by']['cate_id']);
            }
            $this->db->order_by("c.sort", 'ASC');
            $this->db->order_by("s.status", 'DESC');
            $this->db->order_by("s.sort", 'ASC');
            $this->db->order_by("s.price", 'ASC');
            $this->db->order_by("s.name", 'ASC');
            $query = $this->db->get();
            $result = $query->result_array();
            if ($result) {
                $result = group_by_criteria($result, 'category_name');
            }
        }

		if($option['task'] == 'list-service-comments'){
			$this->db->select('*');
            $this->db->from('services_comments');
			$this->db->where('service_id',$params['service_id']['id']);
		
            //Search
            if ($params['search']['field'] === 'all') {
				
            } elseif (in_array($params['search']['field'], $this->field_search_accepted) && $params['search']['query'] != "") {
                $column = $params['search']['field'];
                $this->db->like($column, $params['search']['query']); 
            }

            // Sort By
            if ($params['sort_by']['comment'] != "") {
                $this->db->where('comment', $params['sort_by']['comment']);
            }
            
			if ($params['pagination']['limit'] != "" && $params['pagination']['start'] >= 0) {
                $this->db->limit($params['pagination']['limit'], $params['pagination']['start']);
            }
			
            $this->db->order_by("id",'DESC');
            $query  = $this->db->get();
            $result = $query->result_array();
		}

        if ($option['task'] == 'user-custom-rate-list-items') {
            $result = $this->fetch('id, price, name, original_price', $this->tb_services, ['status' => 1], '', '', 'id', 'ASC', true);
        }
        
        return $result;
    }

    public function get_item($params = null, $option = null)
    {
        $result = null;
        if($option['task'] == 'get-item'){
            $result = $this->get("id, ids, name, desc, cate_id, price, dripfeed, original_price, min, max, type, add_type, api_service_id, api_provider_id, status, refill, refill_type, qty_percentage, is_regex_validation, regex_validations, is_repeat_interval, runs, interval, url_type, comments_enabled, previous_service_type", $this->tb_main, ['id' => $params['id']], '', '', true);
        }
		
		if($option['task'] == 'get-service-comment-item'){
			$result = $this->get("*", 'services_comments', ['id' => $params['id']], '', '', true);
		}
        return $result;
    }

    public function count_items($params = null, $option = null)
    {
        $result = null;
		
		if($option['task'] == 'count-items-for-pagination'){
			$this->db->select("id");
            $this->db->from('services_comments');
			$this->db->where('service_id',$params['service_id']['id']);
            $query = $this->db->get();
            return $query->num_rows();
		}
		
        return $result;
    }

    public function delete_item($params = null, $option = null)
    {
        $result = [];
        is_demo_version();
        if ($option['task'] == 'delete-item') {
            $item = $this->get("id, ids", $this->tb_main, ['id' => $params['id']]);
            if ($item) {
                $this->db->delete($this->tb_main, ["id" => $params['id']]);
                $this->db->delete($this->tb_services, ["cate_id" => $params['id']]);
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

        if($option['task'] == 'delete-custom-rate-item'){
            $item = $this->get("id, ids", $this->tb_main, ['id' => $params['id']]);
            if ($item) {
                $this->db->delete($this->tb_users_price, ["service_id" => $params['id']]);
                $result = [
                    'status' => 'success',
                    'message' => 'Deleted custom rates successfully',
                    "ids"     => '',
                ];
            }else{
                $result = [
                    'status' => 'error',
                    'message' => 'There was an error processing your request. Please try again later',
                ];
            }
        }

		if($option['task'] == 'delete-service-comment-item'){
			$item = $this->get("id", 'services_comments', ['id' => $params['id']]);
            if ($item) {
                $this->db->delete('services_comments', ["id" => $params['id']]);
                $result = [
                    'status' => 'success',
                    'message' => 'Deleted comment successfully',
                    "ids"     => $params['id'],
                ];
            }else{
                $result = [
                    'status' => 'error',
                    'message' => 'There was an error processing your request. Please try again later',
                ];
            }
		}
		
		if($option['task'] == 'delete-bulk-service-comment-item'){
			$idArray = explode(',', $params['ids']);
			
			$this->db->where_in('id', $idArray);
			$query = $this->db->get('services_comments');
			
            if ($query->num_rows() > 0) {
				$this->db->where_in('id', $idArray);
                $this->db->delete('services_comments');
                
				if ($this->db->affected_rows() > 0) {
					$result = [
						'status' => 'success',
						'message' => 'Deleted comment successfully',
						"ids"     => $params['ids'],
					];
				} else {
					return "Error in deleting records.";
				}
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
            $data = [
                "name"             => $this->input->post('name', false),
                "add_type"         => post('add_type'),
                "cate_id"          => post('category'),
                "desc"             => $this->input->post('desc', false),
                "min"              => post('min'),
                "max"              => post('max'),
                "price"            => (double)post('price'),
                "qty_percentage"   => post('qty_percentage'),
				"url_type"         => post('url_type'),
				"comments_enabled" => post('comments_enabled')
            ];
			
			if(post('is_regex_validation')){
				$data['is_regex_validation'] = 1;
				$data['regex_validations']   = (post('regex_validations')) ? json_encode(post('regex_validations')) : '';
			} else {
				$data['is_regex_validation'] = 0;
				$data['regex_validations']   = '';
			}
			
			if(post('is_repeat_interval')){
				$data['is_repeat_interval'] = 1;
				$data['runs']               = (post('runs')) ? post('runs') : 0;
				$data['interval']           = (post('interval')) ? post('interval') : 0;
			} else {
				$data['is_repeat_interval'] = 0;
				$data['runs']               = 0;
				$data['interval']           = 0;
			}
			
            if (post('add_type') == 'api') {
                $data['api_provider_id'] = post("api_provider_id");
                $data['api_service_id']  = post("api_service_id");
                $data['original_price']  = post("original_price");
                $data['type']            = post("api_service_type");
                $data['dripfeed']        = (int)post("api_service_dripfeed");
                $data['refill']          = (int)post("api_service_refill");
				$data['refill_type']     = (int)post("refill_type");
				
				if(in_array($option['task'], ['add-item'])){
					$data['previous_service_type'] = post("api_service_type");
				}
            } else {
                $data['api_provider_id']       = "";
                $data['api_service_id']        = "";
                $data['type']                  = post("service_type");
                $data['dripfeed']              = (int)post("dripfeed");
                $data['refill']                = (int)post("refill");
				$data['refill_type']           = 0;
				
				if(in_array($option['task'], ['add-item'])){
					$data['previous_service_type'] = post("service_type");
				}
            }
			
			if(post('comments_enabled')){
				$data['type'] = "default";
			} else {
				$data['type'] = post("previous_service_type");
			}
        }
        switch ($option['task']) {
            case 'add-item':
                $data["ids"]     = ids();
                $data["status"]  = 1;
                $data["changed"] = NOW;
                $data["created"] = NOW;
                $this->db->insert($this->tb_main, $data);
                return ["status"  => "success", "message" => 'Add successfully'];
                break;
                
            case 'edit-item':
                $data["changed"] = NOW;
                $this->db->update($this->tb_main, $data, ["id" => post('id')]);
                return ["status"  => "success", "message" => 'Update successfully'];
                break;
			case 'add-service-comment-item':
                $comments = explode(PHP_EOL, post("comment", false));
				if(!empty($comments)){
					foreach($comments as $comment){
						$final_comment = trim($comment);
						if($final_comment != '' && $final_comment != null && !empty($final_comment)){
							$data = [];
							$data['service_id'] = post("service_id");
							$data['comment']    = stripslashes($final_comment);
							$data['created_at'] = date("Y-m-d H:i:s");
							$this->db->insert('services_comments', $data);
						}
					}
					
					return ["status"  => "success", "message" => 'Add successfully', "redirect" => admin_url('services/comments/'.post("service_id"))];
					break;
				} else {
					return ["status"  => "error", "message" => 'Comment field is required!!!'];
					break;
				}
            case 'edit-service-comment-item':
                $comment            = trim(post("comment", false));
				if($comment){
					$data               = [];
					$data['service_id'] = post("service_id");
					$data['comment']    = stripslashes($comment);
					$data['updated_at'] = date("Y-m-d H:i:s");
					$this->db->update('services_comments', $data, ["id" => post('id')]);
					return ["status"  => "success", "message" => 'Update successfully', "redirect" => admin_url('services/comments/'.post("service_id"))];
					break;
				} else {
					return ["status"  => "error", "message" => 'Comment field is required!!!'];
					break;
				}
            case 'change-status':
                $this->db->update($this->tb_main, ['status' => $params['status'], 'changed' => NOW], ["id" => $params['id']]);
                return ["status"  => "success", "message" => 'Update successfully'];
                break;

            case 'bulk-action':
                is_demo_version();
                if (in_array($params['type'], ['delete', 'deactive', 'active']) && empty($params['ids'])) {
                    return ["status"  => "error", "message" => 'Please choose at least one item'];
                }
                $arr_ids = convert_str_number_list_to_array($params['ids']);
                switch ($params['type']) {
                    case 'delete':
                        $this->db->where_in('id', $arr_ids);
                        $this->db->delete($this->tb_main);
                        return ["status"  => "success", "message" => 'Delete successfully'];
                        break;
                    case 'delete_custom_rates':
                        $this->db->where_in('service_id', $arr_ids);
                        $this->db->delete($this->tb_users_price);
                        return ["status"  => "success", "message" => 'Delete custom rates successfully'];
                        break;
                    case 'deactive':
                        $this->db->where_in('id', $arr_ids);
                        $this->db->update($this->tb_main, ['status' => 0]);
                        return ["status"  => "success", "message" => 'Update successfully'];
                        break;
                    case 'active':
                        $this->db->where_in('id', $arr_ids);
                        $this->db->update($this->tb_main, ['status' => 1]);
                        return ["status"  => "success", "message" => 'Update successfully'];
                        break;
                }
                break;
        }
    }
}
