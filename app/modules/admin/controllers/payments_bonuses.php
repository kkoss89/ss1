<?php 
defined('BASEPATH') or exit('No direct script access allowed');

class payments_bonuses extends My_AdminController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model(get_class($this) . '_model', 'main_model');
        if (!is_current_logged_staff()) {
            redirect(admin_url('logout'));
        }

        $this->controller_name = strtolower(get_class($this));
        $this->controller_title = ucfirst(str_replace('_', ' ', get_class($this)));
        $this->path_views = "payments_bonuses";
        $this->params = [];

        $this->columns = array(
            "method"            => ['name' => 'Payment Method', 'class' => ''],
			"bonus_description" => ['name' => 'Bonus Description', 'class' => ''],
			"bonus_user_type"   => ['name' => 'Bonus For', 'class' => ''],
            "bonus"             => ['name' => 'Bonus from', 'class' => ''],
            "percentage"        => ['name' => 'Bonus percentage (%)', 'class' => 'text-center'],
            "status"            => ['name' => 'Status', 'class' => 'text-center'],
        );
    }

    // Edit form
    public function update($id = null)
    {
        staff_check_role_permission($this->controller_name, 'edit');
        if (!$this->input->is_ajax_request()) {
            redirect(admin_url($this->controller_name));
        }
        $item = null;
        if ($id !== null) {
            $this->params = ['id' => $id];
            $item = $this->main_model->get_item($this->params, ['task' => 'get-item']);
        }
        $this->load->model('payments_model', 'payments_model');
        $items_payment = $this->payments_model->list_items(null, ['task' => 'admin-list-items-on-pm-bonuses']);
		$all_users     = $this->main_model->list_items(null, ['task' => 'list-items-users']);
		
        $data = array(
            "controller_name" => $this->controller_name,
            "item"            => $item,
            "items_payment"   => $items_payment,
			"all_users"       => $all_users
        );
        $this->load->view($this->path_views . '/update', $data);
    }

    public function store()
    {
        if (!$this->input->is_ajax_request()) {
            redirect(admin_url($this->controller_name));
        }
        staff_check_role_permission($this->controller_name, 'edit');
        $this->form_validation->set_rules('payment_id', 'payment method', 'trim|greater_than[0]|xss_clean', [
            'greater_than' => 'Payment Method field is required',
        ]);
        $this->form_validation->set_rules('percentage', 'bonus percentage (%)', 'trim|required|greater_than[0]|xss_clean');
        $this->form_validation->set_rules('bonus_from', 'deposit from', 'trim|required|greater_than[0]|xss_clean');
        $this->form_validation->set_rules('status', 'Status', 'trim|required|in_list[0,1]|xss_clean');

        if (!$this->form_validation->run()) {
            _validation('error', validation_errors());
        }

        $task = 'add-item';
        $id = post('id');
        if ($id) {
            $task = 'edit-item';
        }

        $response = $this->main_model->save_item($this->params, ['task' => $task]);
        ms($response);
    }
}
