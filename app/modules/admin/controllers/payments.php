<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
 
class payments extends My_AdminController 
{
    private $tb_main = PAYMENTS_METHOD;

    public function __construct()
    {
        parent::__construct();
        $this->load->model(get_class($this).'_model', 'main_model');
        if (!is_current_logged_staff()) redirect(admin_url('logout'));
        $this->controller_name   = strtolower(get_class($this));
        $this->controller_title  = ucfirst(str_replace('_', ' ', get_class($this)));
        $this->path_views        = "payments";
        $this->params            = [];

        $this->columns     =  array(
            "method"           => ['name' => 'Payment Method',    'class' => ''],
            "name"             => ['name' => 'Name',    'class' => ''],
            "min"              => ['name' => 'Min',    'class' => 'text-center'],
            "max"              => ['name' => 'Max',    'class' => 'text-center'],
            "new_users"        => ['name' => 'New users', 'class' => 'text-center'],
            "status"           => ['name' => 'Status',  'class' => 'text-center'],
        );
    }

    public function store()
    {
        if (!$this->input->is_ajax_request()) redirect(admin_url($this->controller_name));
        staff_check_role_permission($this->controller_name, 'edit');
        $min = $this->input->post('payment_params[min]');
        $this->form_validation->set_rules('id', 'payment method', 'trim|required|xss_clean');
        $this->form_validation->set_rules('payment_params[name]', 'name', 'trim|required|xss_clean');
        $this->form_validation->set_rules('payment_params[min]', 'minimum payment', 'trim|required|validate_money|greater_than[0]|xss_clean');
        $this->form_validation->set_rules('payment_params[max]', 'maximum payment', "trim|required|validate_money|greater_than[$min]|xss_clean", [
            'greater_than' => 'The maximum payment field must contain a number greater than minimum payment',
        ]);
        $this->form_validation->set_rules('payment_params[new_users]', 'new users', 'trim|required|in_list[0,1]|xss_clean');
        $this->form_validation->set_rules('payment_params[status]', 'status', 'trim|required|in_list[0,1]|xss_clean');

        if (!in_array(post('type'), ['paypal'])) {
            $this->form_validation->set_rules('payment_params[option][tnx_fee]', 'transaction fee', 'trim|required|validate_money|xss_clean');
        }
        $id = post('id');
        if (!$this->form_validation->run()) _validation('error', validation_errors());
        $task   = 'edit-item';
        $response = $this->main_model->save_item( post('payment_params'), ['task' => $task]);
        ms($response);
    }
}