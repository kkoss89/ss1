<?php
defined('BASEPATH') or exit('No direct script access allowed');

class My_UserController extends MX_Controller
{
    protected $controller_title  = '';
    protected $controller_name   = '';
    protected $path_views        = '';
    protected $params = [];
    protected $columns = [];
    protected $limit_per_page = 5;

    public function __construct()
    {
        parent::__construct();

        if (!in_array(segment(2), array('cron', 'set_language')) && !in_array(segment(3), array('cron', 'complete'))) {
            $allowed_controllers = ['auth', 'api', 'client', 'services', 'crons', 'cryptomus', 'nowpayments'];
            $allowed_page        = ['logout', 'ipn'];
            if (!session('uid') && !$this->maintenance_mode && !in_array($this->router->fetch_class(), $allowed_controllers) && !in_array($this->router->fetch_method(), $allowed_page)) {
                if(segment(1) != ""  && !in_array(segment(1), ['cron', 'ipn', 'cryptomus', 'nowpayments'])){
                    redirect(PATH);
                }
            }
        }
        
        if (session("uid")) {
            $user_allowed_controllers = [];
            $user_allowed_controllers = ['faqs', 'users', 'setting', 'module', 'api_provider', 'category', 'user_mail_logs', 'user_block_ip', 'user_logs', 'payments', 'subscribers','payments_bonuses'];
            if ((in_array($this->router->fetch_class(), $user_allowed_controllers) || in_array(segment(2), ['update']))) {
                redirect(PATH . "statistics");
            }
        }
        $this->limit_per_page = get_option("default_limit_per_page", 10);
    }
}
