<?php
defined('BASEPATH') OR exit('No direct script access allowed');
 
class users_activity extends My_AdminController {

    private $tb_main = USER_LOGS;

    public function __construct()
    {
        parent::__construct();
        $this->load->model(get_class($this).'_model', 'main_model');
        if (!is_current_logged_staff()) redirect(admin_url('logout'));
        $this->controller_name   = strtolower(get_class($this));
        $this->controller_title  = ucfirst(str_replace('_', ' ', get_class($this)));
        $this->path_views        = "users_activity";
        $this->params            = [];
        $this->tb_main           = USER_LOGS;
        $this->columns     =  array(
            "username"        => ['name' => 'Username', 'class' => 'text-center'],
            "type"          => ['name' => 'Type', 'class' => 'text-center'],
            "ip"              => ['name' => 'IP Address',    'class' => 'text-center'],
            "location"        => ['name' => 'Location',  'class' => 'text-center'],
            "date"            => ['name' => 'Date',  'class' => 'text-center'],
        );
    }

}
