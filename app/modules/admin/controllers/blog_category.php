<?php
defined('BASEPATH') or exit('No direct script access allowed');

class blog_category extends My_AdminController
{

    private $tb_main = BLOG_CATEGORIES;

    public function __construct()
    {
        parent::__construct();
        $this->load->model(get_class($this) . '_model', 'main_model');
        if (!is_current_logged_staff()) redirect(admin_url('logout'));
        $this->controller_name = strtolower(get_class($this));
        $this->controller_title = ucfirst(str_replace('_', ' ', get_class($this)));
        $this->path_views = "blog_category";
        $this->params = [];

        $this->columns = array(
            "name" => ['name' => 'Name', 'class' => ''],
            "status" => ['name' => 'Status', 'class' => 'text-center'],
        );

        $this->load->model('language_model', 'language_model');
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
        $items_lang = $this->language_model->list_items(['status' => 1], ['task' => 'list-items-by-params']);
        $data = array(
            "controller_name"   => $this->controller_name,
            "item"              => $item,
            "items_lang"        => array_sort_by_new_key($items_lang, 'code'),
        );
        $this->load->view($this->path_views . '/update', $data);
    }

    public function store()
    {
        if (!$this->input->is_ajax_request()) {
            redirect(admin_url($this->controller_name));
        }
        $this->form_validation->set_rules('status', 'Status', 'trim|required|in_list[0,1]|xss_clean');
        $this->form_validation->set_rules('url_slug', 'Url slug', 'trim|required');
        $task = 'add-item';
        $id = post('id');
        $name_unique = "|is_unique[$this->tb_main.name]";
        if ($id) {
            staff_check_role_permission($this->controller_name, 'edit');
            $task = 'edit-item';
            $name_unique = "|edit_unique[$this->tb_main.name.$id]";
        } else {
            staff_check_role_permission($this->controller_name, 'add');
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
