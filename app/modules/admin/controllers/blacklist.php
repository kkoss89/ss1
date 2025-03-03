<?php
defined('BASEPATH') OR exit('No direct script access allowed');
 
class blacklist extends My_AdminController {

    public function __construct()
    {
        parent::__construct();
        $this->params            = [];
        $this->params['sub_controller'] = $this->router->fetch_method();
        if (isset($_REQUEST['type']) && $_REQUEST['type'] != '') {
            $this->params['sub_controller'] = $_REQUEST['type'];
        }
        if ($this->params['sub_controller'] == 'ip') {
            $this->tb_main           = BLACKLIST_IP;
            $this->load->model('blacklist_ip_model', 'main_model');
        } else if ($this->params['sub_controller'] == 'email') {
            $this->tb_main           = BLACKLIST_EMAIL;
            $this->load->model('blacklist_email_model', 'main_model');
        } else if ($this->params['sub_controller'] == 'link') {
            $this->tb_main           = BLACKLIST_LINK;
            $this->load->model('blacklist_link_model', 'main_model');
        } else {
            $this->tb_main           = BLACKLIST_IP;
            $this->load->model('blacklist_ip_model', 'main_model');
        }
        if (!is_current_logged_staff()) redirect(admin_url('logout'));
        $this->controller_name   = strtolower(get_class($this));
        $this->path_views        = "blacklist";
    }

    public function index()
    {
        staff_check_role_permission($this->controller_name, 'index');
        redirect(admin_url($this->controller_name .'/'. 'ip'));
    }

    public function link()
    {
        staff_check_role_permission($this->controller_name, 'index');
        $black_list_name = 'link';
        $page_title      = 'Blacklist Link';
        return $this->child_index($black_list_name, $page_title);
    }

    public function ip()
    {
        staff_check_role_permission($this->controller_name, 'index');
        $black_list_name = 'IP Address';
        $page_title      = 'Blacklist IP Address';
        return $this->child_index($black_list_name, $page_title);
    }

    public function email()
    {
        staff_check_role_permission($this->controller_name, 'index');
        $black_list_name = 'Email';
        $page_title      = 'Blacklist Email';
        return $this->child_index($black_list_name, $page_title);
    }

    private function child_index($black_list_name, $page_title)
    {
        $page        = (int)get("p");
        $page        = ($page > 0) ? ($page - 1) : 0;
        if (in_array($this->controller_name, ['order', 'dripfeed', 'subscriptions', 'refill'])) {
            $filter_status = (isset($_GET['status'])) ? get('status') : 'all';
        }else{
            $filter_status = (isset($_GET['status'])) ? (int)get('status') : '3';
        }
        $columns     =  array(
            "ip"          => ['name' => $black_list_name, 'class' => 'text-center'],
            "description" => ['name' => 'Description', 'class' => 'text-center'],
            "status"      => ['name' => 'status', 'class' => 'text-center'],
            "created"     => ['name' => 'Created',  'class' => 'text-center'],
        );
        $this->params['pagination'] = [
            'limit'  => $this->limit_per_page,
            'start'  => $page * $this->limit_per_page,
        ];
        $this->params['filter'] = ['status' => $filter_status];
        $this->params['search'] = ['query'  => get('query'), 'field' => get('field')];
        $items = $this->main_model->list_items($this->params, ['task' => 'list-items']);
        $items_status_count = $this->main_model->count_items($this->params, ['task' => 'count-items-group-by-status']);
        $data = array(
            "controller_name"     => $this->controller_name,
            "controller_title"    => $page_title,
            "params"              => $this->params,
            "columns"             => $columns,
            "items"               => $items,
            "items_status_count"  => $items_status_count,
            "from"                => $page * $this->limit_per_page,
            "pagination"          => create_pagination([
                'base_url'         => admin_url($this->controller_name) .'/'. $this->params['sub_controller'],
                'per_page'         => $this->limit_per_page,
                'query_string'     => $_GET, //$_GET 
                'total_rows'       => $this->main_model->count_items($this->params, ['task' => 'count-items-for-pagination']),
            ]),
        );
        $this->template->build($this->path_views . '/index', $data);
    }

    // Edit form
    public function update($id = null)
    {
        if (!$this->input->is_ajax_request()) redirect(admin_url($this->controller_name));
        $item = null;
        if ($id !== null) {
            $this->params = [
                'id'  => $id, 
                'ids' => $id
            ];
            $item = $this->main_model->get_item($this->params, ['task' => 'get-item']);
        }
        $data = array(
            "sub_controller"    => get('type'),
            "controller_name"   => $this->controller_name,
            "item"              => $item,
        );
        $this->load->view($this->path_views . '/update', $data);
    }

    public function store()
    {
        if (!$this->input->is_ajax_request()) redirect(admin_url($this->controller_name));
        $this->form_validation->set_rules('description', 'description', 'trim|xss_clean');
        $ids = post('ids');
        $black_field_type = post('type');
        $blacklist_field_unique = "|edit_unique[$this->tb_main.$black_field_type.$ids]";
        if ($ids) {
            staff_check_role_permission($this->controller_name, 'edit');
            $task   = 'edit-item';
        } else {
            $task = 'add-item';
            staff_check_role_permission($this->controller_name, 'add');
            $blacklist_field_unique = "|is_unique[$this->tb_main.$black_field_type]";
        }
        if ($black_field_type  == 'ip') {
            $this->form_validation->set_rules('ip', 'IP address', 'trim|required|xss_clean'. $blacklist_field_unique, [
                'is_unique' => 'The IP address already exists.',
            ]);
        }
        if ($black_field_type  == 'email') {
            $this->form_validation->set_rules('email', 'Email', 'trim|required|xss_clean'. $blacklist_field_unique, [
                'is_unique' => 'The Email already exists.',
            ]);
        }
        if ($black_field_type  == 'link') {
            $this->form_validation->set_rules('link', 'order link', 'trim|required|xss_clean'. $blacklist_field_unique, [
                'is_unique' => 'The order link already exists.',
            ]);
        }
        if (!$this->form_validation->run()) _validation('error', validation_errors());
        $response = $this->main_model->save_item( $this->params, ['task' => $task]);
        ms($response);
    }
}
