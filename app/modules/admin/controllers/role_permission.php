<?php
defined('BASEPATH') or exit('No direct script access allowed');

class role_permission extends My_AdminController
{

    private $tb_main = ROLE_PERMISSIONS;

    public function __construct()
    {
        parent::__construct();
        $this->load->model(get_class($this) . '_model', 'main_model');
        if (!is_current_logged_staff()) redirect(admin_url('logout'));
        $this->controller_name = strtolower(get_class($this));
        $this->controller_title = ucfirst(str_replace('_', ' ', get_class($this)));
        $this->path_views = "role_permission";
        $this->params = [];

        $this->columns = array(
            "name" => ['name' => 'Role Name', 'class' => ''],
            "description" => ['name' => 'Description', 'class' => 'text-center'],
        );
    }

    public function store()
    {
        if (!$this->input->is_ajax_request()) {
            redirect(admin_url($this->controller_name));
        }
        staff_check_role_permission($this->controller_name, 'store');
        $this->form_validation->set_rules('name', 'Name', 'trim|required|xss_clean');
        $task = 'add-item';
        $id = post('id');
        $name_unique = "|is_unique[$this->tb_main.name]";
        if ($id) {
            $task = 'edit-item';
            $name_unique = "|edit_unique[$this->tb_main.name.$id]";
        }
        $this->form_validation->set_rules('name', 'name', 'trim|required|xss_clean' . $name_unique, [
            'is_unique' => 'The name already exists.',
        ]);
        if (!$this->form_validation->run()) {
            _validation('error', validation_errors());
        }
        $response = $this->main_model->save_item($this->params, ['task' => $task]);
        ms($response);
    }
}
