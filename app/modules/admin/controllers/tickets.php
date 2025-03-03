<?php
defined('BASEPATH') or exit('No direct script access allowed');

class tickets extends My_AdminController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model(get_class($this) . '_model', 'main_model');
        if (!is_current_logged_staff()) redirect(admin_url('logout'));
        $this->controller_name = strtolower(get_class($this));
        $this->controller_title = ucfirst(str_replace('_', ' ', get_class($this)));
        $this->path_views = "tickets";
        $this->params = [];

        $this->columns = array(
            "id" => ['name' => 'ID', 'class' => 'text-center'],
            "user" => ['name' => 'User', 'class' => 'text-center'],
            "subject" => ['name' => 'Subject', 'class' => 'text-center'],
            "status" => ['name' => 'Status', 'class' => 'text-center'],
            "created" => ['name' => 'Created', 'class' => 'text-center'],
        );
    }

    // Add_ticket
    public function add_ticket()
    {
        if (!$this->input->is_ajax_request()) redirect(admin_url($this->controller_name));
        staff_check_role_permission($this->controller_name, 'add');
        if (post('action') == 'add-ticket') {
            $this->form_validation->set_rules('email', 'email', 'trim|required|valid_email|xss_clean');
            $this->form_validation->set_rules('subject', 'subject', 'trim|required|xss_clean');
            $this->form_validation->set_rules('message', 'message', 'trim|required|xss_clean');
            if (!$this->form_validation->run()) {
                _validation('error', validation_errors());
            }
            $task = 'add-new-ticket-from-admin';
            $response = $this->main_model->save_item(null, ['task' => $task]);
            ms($response);
        } else {
            $data = array(
                "controller_name"   => $this->controller_name,
            );
            $this->load->view($this->path_views . '/add_ticket', $data);
        }
        
    }

    public function view($id = "")
    {
        if (!staff_has_permission($this->controller_name, 'view')) redirect(admin_url($this->controller_name));
        $item = $this->main_model->get_item(['id' => (int) $id], ['task' => 'view-get-item']);
        if (!$item) {
            redirect(admin_url($this->controller_name));
        }

        $items_ticket_message = $this->main_model->list_items(['ticket_id' => $id], ['task' => 'list-items-ticket-message']);
        $data = array(
            "controller_name" => $this->controller_name,
            "item" => $item,
            "items_ticket_message" => $items_ticket_message,
        );
        $this->template->build($this->path_views . '/view', $data);
    }

    public function store()
    {
        if (!$this->input->is_ajax_request()) {
            redirect(admin_url($this->controller_name));
        }
        staff_check_role_permission($this->controller_name, 'submit_message');
        $this->form_validation->set_rules('message', 'message', 'trim|required|xss_clean');
        if (!$this->form_validation->run()) {
            _validation('error', validation_errors());
        }
        if (!$this->input->post('ids')) {
            _validation('error', 'There was some wrong with your request');
        }
        $task = 'add-item-ticket-massage';
        $response = $this->main_model->save_item(null, ['task' => $task]);
        ms($response);
    }

    // Change status
    public function change_status($status = "", $id = "")
    {
        if (!in_array($status, ['closed', 'pending', 'unread', 'answered']) || !$id) {
            redirect(admin_url($this->controller_name));
        }
        if (in_array($status, ['closed', 'unread']) || !$id) {
            staff_check_role_permission($this->controller_name, $status);
        }
        $params = [
            'id' => $id,
            'status' => $status,
        ];
        $response = $this->main_model->save_item($params, ['task' => 'change-status']);
        if ($response['status'] && $status == 'unread') {
            redirect(admin_url($this->controller_name));
        } else {
            redirect(admin_url($this->controller_name . '/view/' . $id));
        }
    }

    public function delete_item_ticket_message($ids = "")
    {
        if (!$this->input->is_ajax_request()) redirect(admin_url($this->controller_name));
        staff_check_role_permission($this->controller_name, 'delete_message');
        $response = $this->main_model->delete_item(['ids' => $ids], ['task' => 'delete-item-ticket-message']);
        ms($response);
    }

    public function edit_item_ticket_message()
    {
        if (!$this->input->is_ajax_request()) redirect(admin_url($this->controller_name));
        staff_check_role_permission($this->controller_name, 'edit_message');
        $actions = get('action');
        $ids     = get('ids');
        if ($actions == 'form' && $ids) {
            $item = $this->main_model->get_item(['ids' => $ids], ['task' => 'get-item-ticket-message']);
            $data = array(
                "controller_name"   => $this->controller_name,
                "item"              => $item,
            );
            $this->load->view($this->path_views . '/edit_ticket_message', $data);
        }
        if ($actions == 'edit' && $ids) {
            $this->form_validation->set_rules('message', 'message', 'trim|required|xss_clean');
            if (!$this->form_validation->run()) {
                _validation('error', validation_errors());
            }
            $task = 'edit-item-ticket-massage';
            $response = $this->main_model->save_item(['ids' => $ids], ['task' => $task]);
            ms($response);
        }
    }
}
