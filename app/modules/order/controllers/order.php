<?php 

defined('BASEPATH') OR exit('No direct script access allowed');

class order extends My_UserController {

    public $tb_users;
    public $tb_users_price;
    public $tb_order;
    public $tb_orders_refill;
    public $tb_categories;
    public $tb_services;
    public $tb_staff;
    public $module;
    public $module_name;
    public $module_icon;

    public function __construct() {
        $this->load->model(get_class($this) . '_model', 'model');

        parent::__construct();
        $this->load->model(get_class($this) . '_model', 'main_model');

        $this->controller_name = strtolower(get_class($this));
        $this->controller_title = ucfirst(str_replace('_', ' ', get_class($this)));
        $this->path_views = "";
        $this->params = [];
        $this->columns = [];

        //Config Module
        $this->tb_users = USERS;
        $this->tb_staff = STAFFS;
        $this->tb_users_price = USERS_PRICE;
        $this->tb_order = ORDER;
        $this->tb_orders_refill = ORDERS_REFILL;
        $this->tb_categories = CATEGORIES;
        $this->tb_services = SERVICES;
        $this->module = get_class($this);
        $this->module_name = 'Order';
        $this->module_icon = "fa ft-users";
    }

    public function index() {
        $page = (int) get("p");
        $page = ($page > 0) ? ($page - 1) : 0;
        if (in_array($this->controller_name, ['order', 'dripfeed', 'subscriptions'])) {
            $filter_status = (isset($_GET['status'])) ? get('status') : 'all';
        } else {
            $filter_status = (isset($_GET['status'])) ? (int) get('status') : '3';
        }

        $order_status_array = app_config('config')['status']['order'];
        $order_status_array = array_diff($order_status_array, ['error', 'fail', 'all']);
        if (!in_array($filter_status, $order_status_array)) {
            $filter_status = "all";
        }

        $this->params = [
            'pagination' => [
                'limit' => $this->limit_per_page,
                'start' => $page * $this->limit_per_page,
            ],
            'filter' => ['status' => $filter_status],
            'search' => ['query' => get('query')],
        ];
        $items = $this->main_model->list_items($this->params, ['task' => 'list-items']);

        $this->columns = array(
            "id" => ['name' => lang("order_id"), 'class' => ''],
            "order_details" => ['name' => lang("order_basic_details"), 'class' => 'text-center'],
            "created" => ['name' => lang("Created"), 'class' => 'text-center'],
            "status" => ['name' => lang("Status"), 'class' => 'text-center'],
        );

        $data = array(
            "controller_name" => $this->controller_name,
            "params" => $this->params,
            "columns" => $this->columns,
            "items" => $items,
            "order_status_array" => $order_status_array,
            "from" => $page * $this->limit_per_page,
            "pagination" => create_pagination([
                'base_url' => cn($this->controller_name),
                'per_page' => $this->limit_per_page,
                'query_string' => $_GET, //$_GET
                'total_rows' => $this->main_model->count_items($this->params, ['task' => 'count-items-for-pagination']),
            ]),
        );
        $this->template->set_layout('user');
        $this->template->build('logs/index', $data);
    }

    // New order page
    public function new_order() {
        $this->load->model("services/services_model", 'services_model');
        $items_service = $this->services_model->list_items(null, ['task' => 'list-items']);

        $this->load->model('client/client_model', 'client_model');
        $items_category = $this->client_model->list_items(null, ['task' => 'list-items-category-in-services']);
        $data = array(
            "controller_name" => $this->controller_name,
            'items_category' => $items_category,
            'items_service' => $items_service,
        );
        $this->template->set_layout('user');
        $this->template->build('add/add', $data);
    }

    // Get Services by cate ID
    public function get_services($id = "") {
        if (!$this->input->is_ajax_request())
            redirect(cn($this->controller_name));
        $check_category = $this->main_model->check_record("id", $this->tb_categories, $id, false, false);
        if ($check_category) {
            $this->load->model("services/services_model", 'services_model');
            $items_service = $this->services_model->list_items(['cate_id' => $id], ['task' => 'list-items-by-category-in-new-order']);
            $items_user_price = $this->services_model->list_items('', ['task' => 'list-items-user-price']);
            $data = array(
                "module" => get_class($this),
                "items_service" => $items_service,
                "items_user_price" => $items_user_price,
            );
            $this->load->view('add/get_services', $data);
        }
    }

    // Get Service Detail by ID
    public function get_service($id = "") {
        if (!$this->input->is_ajax_request())
            redirect(cn($this->controller_name));

        $this->load->model("services/services_model", 'services_model');
        $item_service = $this->services_model->get_item(['id' => $id], ['task' => 'get-item-in-new-order']);
        $data = array(
            "controller_name" => $this->controller_name,
            "service" => $item_service,
        );
        $this->load->view('add/get_service', $data);
    }

	// Get Mass Services by cate ID
    public function get_mass_services($id = "") {
        if (!$this->input->is_ajax_request())
            redirect(cn($this->controller_name));
        $check_category = $this->main_model->check_record("id", $this->tb_categories, $id, false, false);
        if ($check_category) {
            $this->load->model("services/services_model", 'services_model');
            $items_service = $this->services_model->list_items(['cate_id' => $id], ['task' => 'list-items-by-category-in-new-order']);
            $items_user_price = $this->services_model->list_items('', ['task' => 'list-items-user-price']);
            $data = array(
                "module" => get_class($this),
                "items_service" => $items_service,
                "items_user_price" => $items_user_price,
            );
            $this->load->view('add/get_mass_services', $data);
        }
    }
	
	public function get_mass_service($id = "")
	{
		if (!$this->input->is_ajax_request())
            redirect(cn($this->controller_name));

        $this->load->model("services/services_model", 'services_model');
        $item_service = $this->services_model->get_item(['id' => $id], ['task' => 'get-item-in-new-order']);
        
		$item_service['price'] = get_user_price(session('uid'), (object)$item_service);
		
		print_r(json_encode($item_service));
		die;
	}

	public function update_old_orders()
	{
		/*$this->db->select('*');
		$this->db->from($this->tb_order);
		$this->db->order_by('id','DESC');
		$get_orders = $this->db->get()->result_array(); 

		if(!empty($get_orders)){
			foreach($get_orders as $get_order){
				
				$total_quantity = $get_order['quantity'];
				$total_charge   = (float)$get_order['charge'];
				
				$check_service  = $this->main_model->check_record("*", $this->tb_services, $get_order['service_id'], false, true);
				if($check_service)
				{
					$overflow = ($check_service->qty_percentage) ? $check_service->qty_percentage : 0;
					
					$provider_price_per_item = ($check_service->original_price * 1) / 1000;
					$formal_charge           = (($total_quantity + (($total_quantity * $overflow) / 100)) * $provider_price_per_item);
					$profit                  = $total_charge - $formal_charge;
					
					if($get_order['id'])
					{
						$data = array();
						$data['formal_charge'] = $formal_charge;
						$data['profit']        = $profit;
						
						$this->db->update($this->tb_order, $data, ["id" => $get_order['id']]);
					}
				}
			}
		}*/	
	}
	
	function is_youtube_url($url) {
		$pattern = "/^(https?:\/\/)?(www\.)?(youtube\.com|youtu\.be)\/.+$/";
		return preg_match($pattern, $url);
	}
	
	function normalize_youtube_url($url) {
		$parsed_url = parse_url($url);

		if (!isset($parsed_url['host']) || 
			!preg_match('/(youtube\.com|youtu\.be)$/', $parsed_url['host'])) {
			return null; 
		}

		if (strpos($parsed_url['host'], 'youtu.be') !== false) {
			$video_id = ltrim($parsed_url['path'], '/');
			return $video_id ? "https://www.youtube.com/watch?v=$video_id" : null;
		}

		parse_str($parsed_url['query'] ?? '', $queryParams);
		$path = trim($parsed_url['path'], '/');

		if (isset($queryParams['v'])) {
			return "https://www.youtube.com/watch?v=" . $queryParams['v'];
		} elseif (strpos($path, 'shorts/') === 0) {
			$video_id = explode('/', $path)[1] ?? null;
			return $video_id ? "https://www.youtube.com/shorts/$video_id" : null;
		}

		return null;
	}
	
    public function ajax_add_order() {
        if (!$this->input->is_ajax_request())
            redirect(cn($this->controller_name));
        $this->main_model->check_blacklist();
		
        $service_id   = post("service_id");
        $cate_id      = (int) post("category_id");
        $quantity     = post("quantity");
        $link         = post("link");
        $runs         = post("runs");
        $interval     = post("interval");
        $is_drip_feed = (post("is_drip_feed") == "on") ? 1 : 0;
        $agree        = (post("agree") == "on") ? 1 : 0;
		
        if ($cate_id <= 0)
            _validation('error', lang("please_choose_a_category"));
        if (!$service_id)
            _validation('error', lang("please_choose_a_service"));

        $check_category = $this->main_model->check_record("*", $this->tb_categories, $cate_id, false, true);
        $check_service  = $this->main_model->check_record("*", $this->tb_services, $service_id, false, true);
		$new_order_id   = $this->main_model->generate_unique_order_id();

        if (!$check_category)
            _validation('error', lang("category_does_not_exists"));
        if (!$check_service)
            _validation('error', lang("service_does_not_exists"));
		
        /* ----------  Add all order without quantity  ---------- */
        $service_type    = $check_service->type;
        $api_provider_id = $check_service->api_provider_id;
        $api_service_id  = $check_service->api_service_id;
		$url_type        = $check_service->url_type;
		
        if ($service_type == "subscriptions") {
            $this->add_order_subscriptions($_POST, $check_service, $check_category);
            exit();
        }
		
        if (!$link)
            _validation('error', lang("invalid_link"));
		
		if($url_type == 1)
		{
			$checkYoutubeUrl = $this->is_youtube_url($link);
			if($checkYoutubeUrl)
			{
				$getNormalizeUrl = $this->normalize_youtube_url($link);
				if($getNormalizeUrl)
				{
					$link = $getNormalizeUrl;
					
					$this->db->select('id');
					$this->db->from($this->tb_order);
					$this->db->where("link",$link);
					$this->db->where('api_provider_id',$api_provider_id);
					$this->db->where('api_service_id',$api_service_id);
					$this->db->group_start();
						$this->db->where('status','active');
						$this->db->or_where('status','processing');
						$this->db->or_where('status','inprogress');
						$this->db->or_where('status','pending');
					$this->db->group_end();
					$this->db->order_by('id','DESC');
					
					$get_id = $this->db->get()->row_array();  
					
					$already_exist_id = (!empty($get_id)) ? $get_id['id'] : 0;
					if($already_exist_id){
						$message_first = lang("you_have_already_an_active_order_for_this_link_on_this_service_id").", ";
						$message_first .= lang("wait_until_it_is_completed_or_cancelled").", ";
						$message_first .= lang("you_can_still_order_other_services_types_for_this_link_but_not_this_kind_of_service_type_or_id.");
						
						_validation('error', $message_first);
					}
				}
				else
				{
					$message_first = lang("the_provided_url_is_not_valid_please_check_the_url_and_try_again")."!!!";
					_validation('error', $message_first);
				}
			}
			else
			{
				$link                 = strip_tags($link);
				$parsedUrl            = parse_url($link);
				$urlBeforeQueryString = $parsedUrl['scheme'] . '://' . $parsedUrl['host'];
				if(isset($parsedUrl['path'])) {
					$urlBeforeQueryString .= $parsedUrl['path'];
				}
				
				$urlBeforeQueryString = addslashes($urlBeforeQueryString);
				
				$this->db->select('id');
				$this->db->from($this->tb_order);
				$this->db->like("link",$urlBeforeQueryString,'both');
				$this->db->where('api_provider_id',$api_provider_id);
				$this->db->where('api_service_id',$api_service_id);
				$this->db->group_start();
					$this->db->where('status','active');
					$this->db->or_where('status','processing');
					$this->db->or_where('status','inprogress');
					$this->db->or_where('status','pending');
				$this->db->group_end();
				$this->db->order_by('id','DESC');
				
				$get_id           = $this->db->get()->row_array();  
				
				$already_exist_id = (!empty($get_id)) ? $get_id['id'] : 0;
				if($already_exist_id){
					$message_first = lang("you_have_already_an_active_order_for_this_link_on_this_service_id").", ";
					$message_first .= lang("wait_until_it_is_completed_or_cancelled").", ";
					$message_first .= lang("you_can_still_order_other_services_types_for_this_link_but_not_this_kind_of_service_type_or_id.");
					
					_validation('error', $message_first);
				}
			}
		} 
		elseif ($url_type == 0) 
		{
			$link                 = strip_tags($link);
			$parsedUrl            = parse_url($link);
			$urlBeforeQueryString = $parsedUrl['scheme'] . '://' . $parsedUrl['host'];
			if(isset($parsedUrl['path'])) {
				$urlBeforeQueryString .= $parsedUrl['path'];
			}
			
			$urlBeforeQueryString = addslashes($urlBeforeQueryString);
			
			$this->db->select('id');
			$this->db->from($this->tb_order);
			$this->db->like("link",$urlBeforeQueryString,'both');
			$this->db->where('api_provider_id',$api_provider_id);
			$this->db->where('api_service_id',$api_service_id);
			$this->db->group_start();
				$this->db->where('status','active');
				$this->db->or_where('status','processing');
				$this->db->or_where('status','inprogress');
				$this->db->or_where('status','pending');
			$this->db->group_end();
			$this->db->order_by('id','DESC');
			
			$get_id           = $this->db->get()->row_array();  
			
			$already_exist_id = (!empty($get_id)) ? $get_id['id'] : 0;
			if($already_exist_id){
				$message_first = lang("you_have_already_an_active_order_for_this_link_on_this_service_id").", ";
				$message_first .= lang("wait_until_it_is_completed_or_cancelled").", ";
				$message_first .= lang("you_can_still_order_other_services_types_for_this_link_but_not_this_kind_of_service_type_or_id.");
				
				_validation('error', $message_first);
			}	
		}
        
		if($api_provider_id == 2){
			$get_max_quota = $check_service->max;

			$this->db->select('api_order_id');
			$this->db->from($this->tb_order);
			$this->db->like("link",$urlBeforeQueryString,'both');
			$this->db->where('api_provider_id',$api_provider_id);
			$this->db->where('api_service_id',$api_service_id);
			$this->db->group_start();
				$this->db->where('status','completed');
				$this->db->or_where('status','partial');
			$this->db->group_end();
			$this->db->order_by('id','DESC');
			
			$get_api_order_id = $this->db->get()->row_array();  
			$api_order_id     = (!empty($get_api_order_id)) ? $get_api_order_id['api_order_id'] : 0;
			
			if($api_order_id > 0){
				$this->db->select_sum('quantity');
				$this->db->from($this->tb_order);
				$this->db->where('api_order_id',$api_order_id);
				$this->db->where('api_provider_id',$api_provider_id);
				$this->db->where('api_service_id',$api_service_id);
				$this->db->group_start();
					$this->db->where('status','completed');
					$this->db->or_where('status','partial');
				$this->db->group_end();
				
				$check_orders = $this->db->get()->row_array();   
				$sum_qty      = (!empty($check_orders)) ? $check_orders['quantity'] : 0;

				if($sum_qty >= $get_max_quota){
					$message_first = lang("you_can_not_order_more_than");
					$message_first .= " ".$get_max_quota." ";
					$message_first .= lang("per_link");
					
					_validation('error', $message_first);
				}		
			}		
		}
		
        switch ($service_type) {
            case 'custom_comments':
                $comments = strip_tags(trim($_POST['comments']));
                if (!$comments)
                    _validation('error', lang("comments_field_is_required"));
                $quantity = count(explode("\n", $comments));
                break;

            case 'mentions_custom_list':
                $usernames_custom = post("usernames_custom");
                if (!$usernames_custom)
                    _validation('error', lang("username_field_is_required"));
                $quantity = count(explode("\n", $usernames_custom));
                break;

            case 'package':
                $quantity = 1;
                break;

            case 'custom_comments_package':
                $comments = strip_tags($_POST['comments_custom_package']);
                if (!$comments)
                    _validation('error', lang("comments_field_is_required"));
                $quantity = 1;
                break;
        }

        if (!$quantity)
            _validation('error', lang("quantity_is_required"));

        /* ----------  Check dripfeed  ---------- */
        if ($is_drip_feed && !$check_service->dripfeed) {
            $is_drip_feed = 0;
        }
        if ($is_drip_feed) {
            if (!$runs)
                _validation('error', lang("runs_is_required"));
            if (!$interval)
                _validation('error', lang("interval_time_is_required"));
            if ($interval > 60)
                _validation('error', lang("interval_time_must_to_be_less_than_or_equal_to_60_minutes"));
            $total_quantity = $runs * $quantity;
        } else {
            $total_quantity = $quantity;
        }

        /* ----------  Check quantity  ---------- */
        $min      = $check_service->min;
        $max      = $check_service->max;
		$overflow = $check_service->qty_percentage;
        $price = get_user_price(session('uid'), $check_service);

        if ($service_type == "package" || $service_type == "custom_comments_package") {
            $total_charge = $price;
        } else {
            $total_charge = ($price * $total_quantity) / 1000;
        }

        if ($total_quantity <= 0 || ($total_quantity < $min) || $quantity < $min) {
            _validation('error', lang("quantity_must_to_be_greater_than_or_equal_to_minimum_amount"));
        }

        if ($total_quantity > $max) {
            _validation('error', lang("quantity_must_to_be_less_than_or_equal_to_maximum_amount"));
        }
        /* ----------  Get balance ---------- */
        $user = $this->main_model->get("balance", $this->tb_users, ['id' => session('uid')]);

        /* ----------  Get Formal Charge and profit  ---------- */
        // $formal_charge = ($check_service->original_price * $total_charge) / $check_service->price;
        // $profit = $total_charge - $formal_charge;
        /* ----------  Collect data import to database  ---------- */
		
		// new payment logic
		$overflow_quantity       = ($total_quantity * $overflow) / 100;
		$final_quantity          = $total_quantity + $overflow_quantity;
		$provider_price_per_item = ($check_service->original_price * 1) / 1000;
		$formal_charge           = (($total_quantity + (($total_quantity * $overflow) / 100)) * $provider_price_per_item);
		$profit                  = $total_charge - $formal_charge;
		// new payment logic
		
        $data = array(
            "ids"             => ids(),
            "uid"             => session("uid"),
            "cate_id"         => $cate_id,
            "service_id"      => $service_id,
            "service_type"    => $service_type,
            "service_mode"    => $check_service->add_type,
            "link"            => $link,
            "quantity"        => $total_quantity,
            "charge"          => $total_charge,
            "formal_charge"   => $formal_charge,
            "profit"          => $profit,
            "api_provider_id" => $api_provider_id,
            "api_service_id"  => $api_service_id,
            "is_drip_feed"    => $is_drip_feed,
            "status"          => 'pending',
            "changed"         => NOW,
            "created"         => NOW,
			"order_id"        => $new_order_id,
        );
		
        /* ----------  get the different required paramenter for each service type  ---------- */
        switch ($service_type) {

            case 'mentions_with_hashtags':
                $hashtags = post("hashtags");
                $usernames = post("usernames");
                $usernames = strip_tags($usernames);
                if (!$usernames)
                    _validation('error', lang("username_field_is_required"));
                if (!$hashtags)
                    _validation('error', lang("hashtag_field_is_required"));
                $data["usernames"] = $usernames;
                $data["hashtags"] = $hashtags;
                break;

            case 'mentions_hashtag':
                $hashtag = post("hashtag");
                if (!$hashtag)
                    _validation('error', lang("hashtag_field_is_required"));
                $data["hashtag"] = $hashtag;
                break;

            case 'comment_likes':
                $username = post("username");
                $username = strip_tags($username);
                if (!$username)
                    _validation('error', lang("username_field_is_required"));
                $data["username"] = $username;
                break;

            case 'mentions_user_followers':
                $username = post("username");
                $username = strip_tags($username);
                if (!$username)
                    _validation('error', lang("username_field_is_required"));
                $data["username"] = $username;
                break;

            case 'mentions_media_likers':
                $media_url = post("media_url");

                if ($media_url == "" || !filter_var($media_url, FILTER_VALIDATE_URL)) {
                    _validation('error', lang("invalid_link"));
                }
                $data["media"] = $media_url;
                break;

            case 'custom_comments':
                $data["comments"] = json_encode($comments);
                break;

            case 'custom_comments_package':
                $data["comments"] = json_encode($comments);
                break;

            case 'mentions_custom_list':
                $data["usernames"] = json_encode($usernames_custom);
                break;
        }
        // Check agree
        //if (!$agree) {
        // _validation('error', lang("you_must_confirm_to_the_conditions_before_place_order"));
        //}
        // check balance
        if ($user->balance < $total_charge) {
            _validation('error', lang("not_enough_funds_on_balance"));
        }

        if ($is_drip_feed) {
            $data['runs'] = $runs;
            $data['interval'] = $interval;
            $data['dripfeed_quantity'] = $quantity;
            $data['status'] = 'active';
        }

        if (!empty($api_provider_id) && !empty($api_service_id)) {
            $data['api_order_id'] = -1;
        }

        // Check order refill or not
        // if ($check_service->refill && is_table_exists($this->tb_orders_refill)) {
            // $data['refill'] = 1;
        // }
		
		if ($check_service->refill) {
            $data['refill'] = 1;
        }
        $more_params['service_name'] = $check_service->name;
		
		if($check_service->is_repeat_interval == 1){
			$data['is_interval_order'] = 1;
			$this->save_interval_order($this->tb_order, $data, $user->balance, $total_charge, $more_params, $check_service);
		} else {
			$data['is_interval_order'] = 0;
			$this->save_order($this->tb_order, $data, $user->balance, $total_charge, $more_params);	
		}
    }

    private function add_order_subscriptions($post, $check_service, $item_category) {
        $api_provider_id = $check_service->api_provider_id;
        $api_service_id = $check_service->api_service_id;
        $service_id = $check_service->id;
        $cate_id = $post["category_id"];
        $agree = (isset($post['agree']) && $post["agree"] == "on") ? 1 : 0;
        $service_type = $check_service->type;
        $link = $post["link"];
        $link = strip_tags($link);

        /* ----------  Collect data import to database  ---------- */
        $data = array(
            "ids" => ids(),
            "uid" => session("uid"),
            "cate_id" => $cate_id,
            "service_id" => $service_id,
            "service_type" => $service_type,
            "service_mode" => $check_service->add_type,
            "api_provider_id" => $api_provider_id,
            "api_service_id" => $api_service_id,
            "sub_status" => 'Active',
            "status" => 'pending',
            "changed" => NOW,
            "created" => NOW,
        );
        switch ($service_type) {
            case 'subscriptions':
                $username = $post["sub_username"];
                $posts = (int) $post["sub_posts"];
                $min = (int) $post["sub_min"];
                $max = (int) $post["sub_max"];
                $delay = (int) $post["sub_delay"];
                $expiry = $post["sub_expiry"];

                if ($username == "")
                    _validation('error', lang("username_field_is_required"));
                if ($min == "")
                    _validation('error', lang("quantity_must_to_be_greater_than_or_equal_to_minimum_amount"));
                if ($min < $check_service->min)
                    _validation('error', lang("quantity_must_to_be_greater_than_or_equal_to_minimum_amount"));
                if ($max < $min)
                    _validation('error', lang("min_cannot_be_higher_than_max"));
                if ($max > $check_service->max)
                    _validation('error', lang("quantity_must_to_be_less_than_or_equal_to_maximum_amount"));

                if (!in_array($delay, array(0, 5, 10, 15, 30, 60, 90))) {
                    _validation('error', lang("incorrect_delay"));
                }

                if ($posts <= 0 || $posts == "") {
                    _validation('error', lang("new_posts_future_posts_must_to_be_greater_than_or__equal_to_1"));
                }

                // Check agree
                if (!$agree) {
                    _validation('error', lang("you_must_confirm_to_the_conditions_before_place_order"));
                }
                // calculate total charge
                $price = get_user_price(session('uid'), $check_service);
                $charge = ($max * $posts * $price) / 1000;

                // check balance
                $user = $this->main_model->get("balance", $this->tb_users, ['id' => session('uid')]);
                if (($user->balance != 0 && $user->balance < $charge) || $user->balance == 0) {
                    _validation('error', lang("not_enough_funds_on_balance"));
                }
                if ($expiry != "") {
                    $expiry = str_replace('/', '-', $expiry);
                    $expiry = date("Y-m-d", strtotime($expiry));
                } else {
                    $expiry = "";
                }

                $data["username"] = $username;
                $data["sub_posts"] = ($posts == "") ? -1 : $posts;
                $data["sub_min"] = $min;
                $data["sub_max"] = $max;
                $data["sub_delay"] = $delay;
                $data["sub_expiry"] = $expiry;

                // From V3.6
                $data["charge"] = $charge;
                $data["formal_charge"] = $expiry;
                $data["profit"] = $expiry;

                if (!empty($api_provider_id) && !empty($api_service_id)) {
                    $data['api_order_id'] = -1;
                }
                $more_params['service_name'] = $check_service->name;
                $more_params['order_type'] = 'subscriptions';
                $this->save_order($this->tb_order, $data, $user->balance, $charge, $more_params);
                break;
        }
    }

    /* ----------  insert data to order  ---------- */

    private function save_order($table, $data_orders, $user_balance = "", $total_charge = "", $more_params = []) {
        $service_mode = $data_orders['service_mode'];
        unset($data_orders['service_mode']);
        $new_balance = $user_balance - $total_charge;
        $new_balance = ($new_balance > 0) ? $new_balance : 0;
        $this->db->update($this->tb_users, ["balance" => $new_balance], ["id" => session("uid")]);

        if ($this->db->affected_rows() > 0) {
            $this->db->insert($table, $data_orders);
            $order_id = $this->db->insert_id();
			
			if(isset($data_orders['order_id']) && $data_orders['order_id'] != ''){
				$order_id = $data_orders['order_id'];
			}
			
            /* ----------  Send Order notificaltion notice to Admin  ---------- */
            if (get_option("is_order_notice_email", '')) {
                $user_email = $this->model->get("email", $this->tb_users, "id = '" . session('uid') . "'")->email;
                $subject = getEmailTemplate("order_success")->subject;
                $subject = str_replace("{{website_name}}", get_option("website_name", "SmartPanel"), $subject);
                $email_content = getEmailTemplate("order_success")->content;
                $email_content = str_replace("{{user_email}}", $user_email, $email_content);
                $email_content = str_replace("{{order_id}}", $order_id, $email_content);
                $email_content = str_replace("{{currency_symbol}}", get_option("currency_symbol", ""), $email_content);
                $email_content = str_replace("{{total_charge}}", $total_charge, $email_content);
                $email_content = str_replace("{{website_name}}", get_option("website_name", "SmartPanel"), $email_content);

                $mail_params = [
                    'template' => [
                        'subject' => $subject,
                        'message' => $email_content,
                        'type' => 'default',
                    ],
                ];
                $staff_mail = $this->model->get("id, email", $this->tb_staff, [], "id", "ASC")->email;
                if ($staff_mail) {
                    // $send_message = $this->model->send_mail_template($mail_params['template'], $staff_mail);
                    // if ($send_message) {
                        // send_mail_error_log(["status" => "error", "message" => $send_message]);
                    // }
                }
            }

            /* ----------  Notification for admin (new manual order email)  ---------- */
            if (get_option("is_new_manual_order_notice_email", 0) && $service_mode == 'manual') {
                $user_email = $this->model->get("email", $this->tb_users, "id = '" . session('uid') . "'")->email;

                $subject = getEmailTemplate("new_manual_order")->subject;
                $subject = str_replace("{{website_name}}", get_option("website_name", "SmartPanel"), $subject);
                $email_content = getEmailTemplate("new_manual_order")->content;
                $email_content = str_replace("{{user_email}}", $user_email, $email_content);
                $email_content = str_replace("{{order_id}}", $order_id, $email_content);
                $email_content = str_replace("{{website_name}}", get_option("website_name", "SmartPanel"), $email_content);
                $mail_params = [
                    'template' => [
                        'subject' => $subject,
                        'message' => $email_content,
                        'type' => 'default',
                    ],
                ];
                $staff_mail = $this->model->get("id, email", $this->tb_staff, [], "id", "ASC")->email;
                if ($staff_mail) {
                    // $send_message = $this->model->send_mail_template($mail_params['template'], $staff_mail);
                    // if ($send_message) {
                        // send_mail_error_log(["status" => "error", "message" => $send_message]);
                    // }
                }
            }
            $data_order_message_success = [
                'status' => 'success',
                'notification_type' => 'place-order',
                'user_balance' => get_option('currency_symbol', "$") . currency_format($new_balance),
                'order_type' => 'default',
                'order_detail' => [
                    'id' => $order_id,
                    'service_name' => $more_params['service_name'],
                ]
            ];
            if (isset($more_params['order_type']) && $more_params['order_type'] == 'subscriptions') {
                $data_order_message_success['order_type'] = 'subscriptions';
                $data_order_message_success['order_detail']['username'] = $data_orders['username'];
                $data_order_message_success['order_detail']['posts'] = $data_orders['sub_posts'];
                $data_order_message_success['order_detail']['charge'] = $total_charge;
                $data_order_message_success['order_detail']['balance'] = currency_format($new_balance);
            } else {
                $data_order_message_success['order_detail']['link'] = $data_orders['link'];
                $data_order_message_success['order_detail']['quantity'] = $data_orders['quantity'];
                $data_order_message_success['order_detail']['charge'] = $total_charge;
                $data_order_message_success['order_detail']['balance'] = currency_format($new_balance);
            }
            ms($data_order_message_success);
        } else {
            _validation('error', lang("There_was_an_error_processing_your_request_Please_try_again_later"));
        }
    }
	
	private function save_interval_order($table, $data_orders, $user_balance = "", $total_charge = "", $more_params = [], $check_service)
	{
		$service_mode = $data_orders['service_mode'];
        unset($data_orders['service_mode']);
        $new_balance = $user_balance - $total_charge;
        $new_balance = ($new_balance > 0) ? $new_balance : 0;
        $this->db->update($this->tb_users, ["balance" => $new_balance], ["id" => session("uid")]);

        if ($this->db->affected_rows() > 0) {
            $this->db->insert($table, $data_orders);
            $order_id = $this->db->insert_id();
			
			$is_repeat_interval = $check_service->is_repeat_interval;
			$runs               = $check_service->runs;
			$interval           = $check_service->interval;
			if($is_repeat_interval == 1 && $runs > 0){
				$per_run_charge        = $total_charge / $runs;
				$per_run_formal_charge = $data_orders['formal_charge'] / $runs;
				$per_run_profit        = $data_orders['profit'] / $runs;
				
				for($i = 0;$i < $runs;$i++){
					$sub_orders = [];
					$sub_orders                    = $data_orders;
					$sub_orders['parent_order_id'] = $order_id;
					$sub_orders['charge']          = $per_run_charge;
					$sub_orders['formal_charge']   = $per_run_formal_charge;
					$sub_orders['profit']          = $per_run_profit;
					$sub_orders['order_id']        = $this->main_model->generate_unique_sub_order_id();
					if($i == 0){ 
						$sub_orders['start_at']    = NOW;
						$sub_orders['changed']     = NOW;
					} else { 
						$sub_orders['start_at']    = null;
						$sub_orders['changed']     = null;
					}
					$this->db->insert('sub_orders', $sub_orders);
				}
			}
			
			if(isset($data_orders['order_id']) && $data_orders['order_id'] != ''){
				$order_id = $data_orders['order_id'];
			}
			
            /* ----------  Send Order notificaltion notice to Admin  ---------- */
            if (get_option("is_order_notice_email", '')) {
                $user_email = $this->model->get("email", $this->tb_users, "id = '" . session('uid') . "'")->email;
                $subject = getEmailTemplate("order_success")->subject;
                $subject = str_replace("{{website_name}}", get_option("website_name", "SmartPanel"), $subject);
                $email_content = getEmailTemplate("order_success")->content;
                $email_content = str_replace("{{user_email}}", $user_email, $email_content);
                $email_content = str_replace("{{order_id}}", $order_id, $email_content);
                $email_content = str_replace("{{currency_symbol}}", get_option("currency_symbol", ""), $email_content);
                $email_content = str_replace("{{total_charge}}", $total_charge, $email_content);
                $email_content = str_replace("{{website_name}}", get_option("website_name", "SmartPanel"), $email_content);

                $mail_params = [
                    'template' => [
                        'subject' => $subject,
                        'message' => $email_content,
                        'type' => 'default',
                    ],
                ];
                $staff_mail = $this->model->get("id, email", $this->tb_staff, [], "id", "ASC")->email;
                if ($staff_mail) {
                    // $send_message = $this->model->send_mail_template($mail_params['template'], $staff_mail);
                    // if ($send_message) {
                        // send_mail_error_log(["status" => "error", "message" => $send_message]);
                    // }
                }
            }

            /* ----------  Notification for admin (new manual order email)  ---------- */
            if (get_option("is_new_manual_order_notice_email", 0) && $service_mode == 'manual') {
                $user_email = $this->model->get("email", $this->tb_users, "id = '" . session('uid') . "'")->email;

                $subject = getEmailTemplate("new_manual_order")->subject;
                $subject = str_replace("{{website_name}}", get_option("website_name", "SmartPanel"), $subject);
                $email_content = getEmailTemplate("new_manual_order")->content;
                $email_content = str_replace("{{user_email}}", $user_email, $email_content);
                $email_content = str_replace("{{order_id}}", $order_id, $email_content);
                $email_content = str_replace("{{website_name}}", get_option("website_name", "SmartPanel"), $email_content);
                $mail_params = [
                    'template' => [
                        'subject' => $subject,
                        'message' => $email_content,
                        'type' => 'default',
                    ],
                ];
                $staff_mail = $this->model->get("id, email", $this->tb_staff, [], "id", "ASC")->email;
                if ($staff_mail) {
                    // $send_message = $this->model->send_mail_template($mail_params['template'], $staff_mail);
                    // if ($send_message) {
                        // send_mail_error_log(["status" => "error", "message" => $send_message]);
                    // }
                }
            }
            $data_order_message_success = [
                'status' => 'success',
                'notification_type' => 'place-order',
                'user_balance' => get_option('currency_symbol', "$") . currency_format($new_balance),
                'order_type' => 'default',
                'order_detail' => [
                    'id' => $order_id,
                    'service_name' => $more_params['service_name'],
                ]
            ];
            if (isset($more_params['order_type']) && $more_params['order_type'] == 'subscriptions') {
                $data_order_message_success['order_type'] = 'subscriptions';
                $data_order_message_success['order_detail']['username'] = $data_orders['username'];
                $data_order_message_success['order_detail']['posts'] = $data_orders['sub_posts'];
                $data_order_message_success['order_detail']['charge'] = $total_charge;
                $data_order_message_success['order_detail']['balance'] = currency_format($new_balance);
            } else {
                $data_order_message_success['order_detail']['link'] = $data_orders['link'];
                $data_order_message_success['order_detail']['quantity'] = $data_orders['quantity'];
                $data_order_message_success['order_detail']['charge'] = $total_charge;
                $data_order_message_success['order_detail']['balance'] = currency_format($new_balance);
            }
            ms($data_order_message_success);
        } else {
            _validation('error', lang("There_was_an_error_processing_your_request_Please_try_again_later"));
        }
	}

    // MASS ORDER
    public function ajax_mass_order() {
        if (!$this->input->is_ajax_request())
            redirect(cn($this->controller_name));
		
		$mass_category_id  = post("mass_category_id");
		$mass_service_id   = post("mass_service_id");
		$mass_order        = post("mass_order");
		$mass_quantity     = post("mass_quantity");
        $mass_min_quantity = post("mass_min_quantity");
		$mass_max_quantity = post("mass_max_quantity");
        $agree      = (post("agree") == "on") ? 1 : 0;
	
        if (!$agree) {
            _validation('error', lang("you_must_confirm_to_the_conditions_before_place_order"));
        }

		if($mass_category_id == ''){
			_validation('error', lang("Category id can not be blank"));
		} elseif($mass_service_id == '') {
			_validation('error', lang("Service id can not be blank"));
		} elseif ($mass_order == "") {
            _validation('error', lang("field_cannot_be_blank"));
        } elseif($mass_quantity == '') {
			_validation('error', lang("Quantity can not be blank"));
		} 

        /* ----------  get balance   ---------- */
        $user = $this->model->get("balance", $this->tb_users, ['id' => session('uid')]);

        if ($user->balance == 0) {
            _validation('error', lang("you_do_not_have_enough_funds_to_place_order"));
        }
        $total_order   = 0;
        $total_errors  = 0;
        $sum_charge    = 0;
        $error_details = array();
        $orders = array();
        if(is_array($mass_order)) 
		{
			$check_service = $this->model->check_record("*", $this->tb_services, $mass_service_id, false, true);
			if(!empty($check_service) && $check_service->is_repeat_interval == 1) {
				foreach ($mass_order as $key => $row) {
					$service_id = $mass_service_id;
					$quantity   = $mass_quantity;
					$link       = $row;

					// check service id
					$check_service = $this->model->check_record("*", $this->tb_services, $service_id, false, true);
					if (empty($check_service)) {
						$error_details[$row] = lang("service_id_does_not_exists");
						continue;
					}

					// check quantity and balance
					$min                = $check_service->min;
					$max                = $check_service->max;
					$overflow           = $check_service->qty_percentage;
					$price              = get_user_price(session('uid'), $check_service);
					$charge             = (double) $price * ($quantity / 1000);
					$is_repeat_interval = $check_service->is_repeat_interval;
					$runs               = $check_service->runs;
					$interval           = $check_service->interval;

					if ($quantity <= 0 || $quantity < $min) {
						$error_details[$row] = lang("quantity_must_to_be_greater_than_or_equal_to_minimum_amount");
						continue;
					}

					if ($quantity > $max) {
						$error_details[$row] = lang("quantity_must_to_be_less_than_or_equal_to_maximum_amount");
						continue;
					}

					// Order charge to .00 decimal points
					$charge = number_format($charge, 2, '.', '');
					$sum_charge += $charge;
				}

				$isBalanceError = 0;
				// check sum_charge and balance
				if ($sum_charge > $user->balance) {
					$isBalanceError = 1;
					_validation('error', lang("not_enough_funds_on_balance"));
				}
				
				if($isBalanceError == 0)
				{
					$successMass = 0;
					foreach ($mass_order as $key => $row) {
						$service_id = $mass_service_id;
						$quantity   = $mass_quantity;
						$link       = $row;

						// check service id
						$check_service = $this->model->check_record("*", $this->tb_services, $service_id, false, true);
						if (empty($check_service)) {
							$error_details[$row] = lang("service_id_does_not_exists");
							continue;
						}

						// check quantity and balance
						$min                = $check_service->min;
						$max                = $check_service->max;
						$overflow           = $check_service->qty_percentage;
						$price              = get_user_price(session('uid'), $check_service);
						$charge             = (double) $price * ($quantity / 1000);
						$is_repeat_interval = $check_service->is_repeat_interval;
						$runs               = $check_service->runs;
						$interval           = $check_service->interval;

						if ($quantity <= 0 || $quantity < $min) {
							$error_details[$row] = lang("quantity_must_to_be_greater_than_or_equal_to_minimum_amount");
							continue;
						}

						if ($quantity > $max) {
							$error_details[$row] = lang("quantity_must_to_be_less_than_or_equal_to_maximum_amount");
							continue;
						}

						// Order charge to .00 decimal points
						$charge = number_format($charge, 2, '.', '');

						/* ----------  Get Formal Charge and profit  ---------- */
						// $formal_charge = ($check_service->original_price * $charge) / $check_service->price;
						// $profit = $charge - $formal_charge;

						$overflow_quantity       = ($quantity * $overflow) / 100;
						$final_quantity          = $quantity + $overflow_quantity;
						$provider_price_per_item = ($check_service->original_price * 1) / 1000;
						$formal_charge           = (($quantity + (($quantity * $overflow) / 100)) * $provider_price_per_item);
						$profit                  = $charge - $formal_charge;

						$new_order_id = $this->main_model->generate_unique_order_id();

						// every thing is ok
						$orders = array(
							"ids"             => ids(),
							"uid"             => session("uid"),
							"cate_id"         => $check_service->cate_id,
							"service_id"      => $service_id,
							"link"            => $link,
							"quantity"        => $quantity,
							"charge"          => $charge,
							"formal_charge"   => $formal_charge,
							"profit"          => $profit,
							"api_provider_id" => $check_service->api_provider_id,
							"api_service_id"  => $check_service->api_service_id,
							"api_order_id"    => (!empty($check_service->api_provider_id) && !empty($check_service->api_service_id)) ? -1 : 0,
							"status"          => 'pending',
							"changed"         => NOW,
							"created"         => NOW,
							"order_id"        => $new_order_id,
						);
						$this->db->insert($this->tb_order, $orders);
						$order_id = $this->db->insert_id();
						$successMass = 1;
						
						if($runs){
							$per_run_charge        = $charge / $runs;
							$per_run_formal_charge = $formal_charge / $runs;
							$per_run_profit        = $profit / $runs;
							
							for($i = 0;$i < $runs;$i++){
								$sub_orders = [];
								$sub_orders['parent_order_id'] = $order_id;
								$sub_orders['ids']             = ids();
								$sub_orders['uid']             = session("uid");
								$sub_orders['cate_id']         = $check_service->cate_id;
								$sub_orders['service_id']      = $service_id;
								$sub_orders['link']            = $link;
								$sub_orders['quantity']        = $quantity;
								$sub_orders['charge']          = $per_run_charge;
								$sub_orders['formal_charge']   = $per_run_formal_charge;
								$sub_orders['profit']          = $per_run_profit;
								$sub_orders['api_provider_id'] = $check_service->api_provider_id;
								$sub_orders['api_service_id']  = $check_service->api_service_id;
								$sub_orders['api_order_id']    = (!empty($check_service->api_provider_id) && !empty($check_service->api_service_id)) ? -1 : 0;
								$sub_orders['status']          = 'pending';
								$sub_orders['created']         = NOW;
								$sub_orders['order_id']        = $this->main_model->generate_unique_sub_order_id();
								if($i == 0){ 
									$sub_orders['start_at']    = NOW;
									$sub_orders['changed']     = NOW;
								} else { 
									$sub_orders['start_at']    = null;
									$sub_orders['changed']     = null;
								}
								$this->db->insert('sub_orders', $sub_orders);
							}
						}
					}
				}
				
				if ($successMass == 1) {
					$new_balance = $user->balance - $sum_charge;
					$this->db->update($this->tb_users, ["balance" => $new_balance], ["id" => session("uid")]);
				}
			} else {
				foreach ($mass_order as $key => $row) {
					
					// check format
					// $order_count = count($mass_order);
					// if ($order_count > 3 || $order_count <= 2) {
						// $error_details[$row] = lang("invalid_format_place_order");
						// continue;
					// }
					$service_id = $mass_service_id;
					$quantity   = $mass_quantity;
					$link       = $row;

					// check service id
					$check_service = $this->model->check_record("*", $this->tb_services, $service_id, false, true);
					if (empty($check_service)) {
						$error_details[$row] = lang("service_id_does_not_exists");
						continue;
					}

					// check quantity and balance
					$min                = $check_service->min;
					$max                = $check_service->max;
					$overflow           = $check_service->qty_percentage;
					$price              = get_user_price(session('uid'), $check_service);
					$charge             = (double) $price * ($quantity / 1000);
					$is_repeat_interval = $check_service->is_repeat_interval;
					$runs               = $check_service->runs;
					$interval           = $check_service->interval;

					if ($quantity <= 0 || $quantity < $min) {
						$error_details[$row] = lang("quantity_must_to_be_greater_than_or_equal_to_minimum_amount");
						continue;
					}

					if ($quantity > $max) {
						$error_details[$row] = lang("quantity_must_to_be_less_than_or_equal_to_maximum_amount");
						continue;
					}

					// Order charge to .00 decimal points
					$charge = number_format($charge, 2, '.', '');

					/* ----------  Get Formal Charge and profit  ---------- */
					// $formal_charge = ($check_service->original_price * $charge) / $check_service->price;
					// $profit = $charge - $formal_charge;

					$overflow_quantity       = ($quantity * $overflow) / 100;
					$final_quantity          = $quantity + $overflow_quantity;
					$provider_price_per_item = ($check_service->original_price * 1) / 1000;
					$formal_charge           = (($quantity + (($quantity * $overflow) / 100)) * $provider_price_per_item);
					$profit                  = $charge - $formal_charge;

					$new_order_id = $this->main_model->generate_unique_order_id();

					// every thing is ok
					$orders[] = array(
						"ids"             => ids(),
						"uid"             => session("uid"),
						"cate_id"         => $check_service->cate_id,
						"service_id"      => $service_id,
						"link"            => $link,
						"quantity"        => $quantity,
						"charge"          => $charge,
						"formal_charge"   => $formal_charge,
						"profit"          => $profit,
						"api_provider_id" => $check_service->api_provider_id,
						"api_service_id"  => $check_service->api_service_id,
						"api_order_id"    => (!empty($check_service->api_provider_id) && !empty($check_service->api_service_id)) ? -1 : 0,
						"status"          => 'pending',
						"changed"         => NOW,
						"created"         => NOW,
						"order_id"        => $new_order_id,
					);
					$sum_charge += $charge;
				}

				// check sum_charge and balance
				if ($sum_charge > $user->balance) {
					_validation('error', lang("not_enough_funds_on_balance"));
				}
				if (!empty($orders)) {
					$this->db->insert_batch($this->tb_order, $orders);
					$new_balance = $user->balance - $sum_charge;
					$this->db->update($this->tb_users, ["balance" => $new_balance], ["id" => session("uid")]);
				}
			}
        }
        if (!empty($error_details)) {
            $this->load->view("add/mass_order_notification", ["error_details" => $error_details]);
        } else {
            ms(array(
                "status" => "success",
                "message" => lang("place_order_successfully")
            ));
        }
    }

}
