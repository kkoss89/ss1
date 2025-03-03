<?php 
defined('BASEPATH') or exit('No direct script access allowed');

class cronjobs extends My_AdminController
{

    

    public function __construct()
    {
        parent::__construct();
        $this->controller_name = strtolower(get_class($this));
        $this->path_views = "cronjobs";
        if (!is_current_logged_staff()) redirect(admin_url('logout'));
    }
    
    public function index()
    {
        staff_check_role_permission($this->controller_name, 'index');
        $cron_key = get_cron_key();
        // $link_format = 'curl -s %s';
        $link_format = 'curl -s %s >/dev/null 2>&1';
        $data = array(
            "link_format" => $link_format,
            "cron_links"     => [
                'order'           => cn('cron/order?key=' . $cron_key),
				'status'          => cn('cron/status?key=' . $cron_key),
                'multiple_status' => cn('cron/multiple_status?key=' . $cron_key),
                'dripfeed'        => cn('cron/dripfeed?key=' . $cron_key),
                'subscriptions'   => cn('cron/subscriptions?key=' . $cron_key),
                'sync_services'   => cn('cron/sync_services?key=' . $cron_key),
				'update_balance'  => cn('cron/update_balance?key=' . $cron_key),
				'sub_order'       => cn('cron/sub_order?key=' . $cron_key),
				'suborder_status' => cn('cron/suborder_status?key=' . $cron_key),
            ],
        );
        $this->template->build($this->path_views . '/index', $data);
    }

}
