<?php
defined('BASEPATH') or exit('No direct script access allowed');

class staffs extends My_AdminController
{

    private $tb_main = STAFFS;

    public function __construct()
    {
        if (!is_current_logged_staff()) redirect(admin_url('logout'));
        parent::__construct();
        $this->load->model(get_class($this) . '_model', 'main_model');

        $this->controller_name = strtolower(get_class($this));
        $this->controller_title = ucfirst(str_replace('_', ' ', get_class($this)));
        $this->path_views = "staffs";
        $this->params = [];
        $this->tb_main = STAFFS;
        $this->columns = array(
            "staff" => ['name' => 'Staff Account', 'class' => ''],
            "type" => ['name' => 'Type', 'class' => 'text-center'],
            "created" => ['name' => 'Created', 'class' => 'text-center'],
            "status" => ['name' => 'Status', 'class' => 'text-center'],
        );
    }

    // Edit Staffs
    public function update($ids = null)
    {
        if (!$this->input->is_ajax_request()) {
            redirect(admin_url($this->controller_name));
        }
        staff_check_role_permission($this->controller_name, 'add');
        $item = null;
        $this->load->model('role_permission_model');
        $items_role_permission = $this->role_permission_model->list_items('', ['task' => 'staff-active-list-items']);

        if ($ids !== null) {
            $this->params = ['ids' => $ids];
            $item = $this->main_model->get_item($this->params, ['task' => 'get-item']);
        }
        $data = array(
            "controller_name" => $this->controller_name,
            "item" => $item,
            "items_role_permission" => $items_role_permission,
        );
        $this->load->view($this->path_views . '/update', $data);
    }

    public function store()
    {
        if (!$this->input->is_ajax_request()) {
            redirect(admin_url($this->controller_name));
        }
        staff_check_role_permission($this->controller_name, 'store');
        $this->form_validation->set_rules('first_name', 'first name', 'trim|required|xss_clean');
        $this->form_validation->set_rules('last_name', 'last name', 'trim|required|xss_clean');
        $this->form_validation->set_rules('status', 'status', 'trim|required|in_list[0,1]|xss_clean');
        $this->form_validation->set_rules('timezone', 'timezone', 'trim|required|xss_clean');
        $this->form_validation->set_rules('role_id', 'Account type', 'trim|required|xss_clean');
        $ids = post('ids');
        $email_unique = "|edit_unique[$this->tb_main.email.$ids]";
        is_demo_version();
        if ($ids) {
            if (post('store_type') != 'user_information') {
                $task = 'edit-item';
            } else {
                $task = 'edit-item-information';
            }
        } else {
            $task = 'add-item';
            $email_unique = "|is_unique[$this->tb_main.email]";
            $this->form_validation->set_rules('password', 'password', 'trim|required|min_length[6]|max_length[25]|xss_clean');
        }
        $this->form_validation->set_rules('email', 'email', 'trim|required|valid_email|xss_clean' . $email_unique, [
            'is_unique' => 'The email already exists.',
        ]);
        if (!$this->form_validation->run() && in_array($task, ['add-item', 'edit-item'])) {
            _validation('error', validation_errors());
        }

        $response = $this->main_model->save_item($this->params, ['task' => $task]);
        ms($response);
    }

    // Set Password
    public function set_password($ids = null)
    {
        if (!$this->input->is_ajax_request()) {
            redirect(admin_url($this->controller_name));
        }
        staff_check_role_permission($this->controller_name, 'set_password');
        if ($this->input->post('ids')) {
            is_demo_version();
            $this->form_validation->set_rules('password', 'password', 'trim|required|min_length[6]|max_length[25]|xss_clean');
            $this->form_validation->set_rules('secret_key', 'secret key', 'trim|required|xss_clean');
            if (!$this->form_validation->run()) {
                _validation('error', validation_errors());
            }
            //Check item
            $item = $this->main_model->get_item(['ids' => post('ids')], ['task' => 'get-item']);
            if (!$item) {
                _validation('error', 'The account does not exists');
            }
            $this->load->model('admin_model');
            $is_valid_secret_key = $this->admin_model->verify_admin_access(['secret_key' => post('secret_key')], ['task' => 'check-admin-secret-key']);
            if ($is_valid_secret_key) {
                $response = $this->main_model->save_item(null, ['task' => 'set-password']);
                ms($response);
            } else {
                _validation('error', 'The secret key is invalid.');
            }
        } else {
            $item = null;
            if ($ids !== null) {
                $this->params = ['ids' => $ids];
                $item = $this->main_model->get_item($this->params, ['task' => 'get-item']);
            }
            $data = array(
                "controller_name" => $this->controller_name,
                "item" => $item,
            );
            $this->load->view($this->path_views . '/set_password', $data);
        }
    }

}
