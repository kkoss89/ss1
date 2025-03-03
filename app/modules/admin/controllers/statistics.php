<?php
defined('BASEPATH') OR exit('No direct script access allowed');
 
class statistics extends My_AdminController {

    private $tb_main = CATEGORIES;

    public function __construct(){
        parent::__construct();
        $this->load->model(get_class($this).'_model', 'main_model');
        if (!is_current_logged_staff()) redirect(admin_url('logout'));
        $this->controller_name   = strtolower(get_class($this));
        $this->controller_title  = ucfirst(str_replace('_', ' ', get_class($this)));
        $this->path_views        = "statistics";
        $this->params            = [];
    }

    public function index()
    {
        staff_check_role_permission($this->controller_name, 'index');
        $this->load->model('Users_model');
        $items_last_users = $this->Users_model->list_items(['limit' => 5], ['task' => 'items-last-users']);
        $header_area           = $this->main_model->header_statistics();
        $chart_and_orders_area = $this->main_model->chart_and_orders_statistics();
        $header_area['orders']['value'] = $chart_and_orders_area['orders_statistics']['orders']['value'];

        $this->load->model('order_model');
        $items_best_seller = $this->order_model->list_items(['limit' => 5], ['task' => 'best-seller-in-statistics']);
        $data = array(
            "controller_name"         => $this->controller_name,
            "header_area"             => $header_area,
            "chart_and_orders_area"   => $chart_and_orders_area,
            "items_last_users"        => $items_last_users,
            "items_best_seller"       => $items_best_seller,
        );
        
        $this->template->build($this->path_views . "/index", $data);
    }
}