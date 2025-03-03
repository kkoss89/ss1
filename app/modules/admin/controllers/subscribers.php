<?php
defined('BASEPATH') OR exit('No direct script access allowed');
 
class subscribers extends My_AdminController {

    public function __construct(){
        parent::__construct();
        $this->load->model(get_class($this).'_model', 'main_model');
        if (!is_current_logged_staff()) redirect(admin_url('logout'));
        $this->controller_name   = strtolower(get_class($this));
        $this->controller_title  = ucfirst(str_replace('_', ' ', get_class($this)));
        $this->path_views        = "subscribers";
        $this->params            = [];

        $this->columns     =  array(
            "mail"     => ['name' => 'Mail',    'class'    => ''],
            "ip"       => ['name' => 'IP address', 'class' => 'text-center'],
            "location" => ['name' => 'Location',  'class'    => 'text-center'],
            "created"  => ['name' => 'Created',  'class'   => 'text-center'],
        );
    }

    // Send Mail
    public function mail($id = null)
    {
        staff_check_role_permission($this->controller_name, 'index');
        if (!$this->input->is_ajax_request()) redirect(admin_url($this->controller_name));
        if ($this->input->post('id')) {
            $this->form_validation->set_rules('subject', 'subject', 'trim|required|min_length[6]|xss_clean');
            $this->form_validation->set_rules('message', 'message', 'trim|required|min_length[6]|xss_clean');
            $this->form_validation->set_rules('email_to', 'Receiving email', 'trim|required|xss_clean');
            if (!$this->form_validation->run()) _validation('error', validation_errors());
            $task     = 'send-mail';
            $response = $this->main_model->save_item(null, ['task' => $task]);
            ms($response);
        } else {
            $item = null;
            if ($id !== null) {
                $this->params = ['id' => $id];
                $item = $this->main_model->get_item($this->params, ['task' => 'get-item']);
                $data = array(
                    "controller_name"   => $this->controller_name,
                    "item"              => $item,
                );
                $this->load->view($this->path_views . '/send_mail', $data);
            } else {
                redirect(cn($this->controller_name));
            }
        }
    }
}