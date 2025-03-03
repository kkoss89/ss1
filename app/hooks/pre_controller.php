<?php
class AppSettingClass
{
    public $api_url;

    public function __construct()
    {
        $CI = &get_instance();
        $CI->load->model('admin/admin_model', 'pre_model');
    }

    public function GetAppSetting()
    {
        $CI = &get_instance();
        $GLOBALS['current_user'] = null;
        $GLOBALS['current_staff'] = null;
        $result = $CI->pre_model->fetch('name, value', 'general_options', '', '', '', '', '', true);
        if ($result) {
            $result = array_column($result, 'value', 'name');
            $GLOBALS['app_settings'] = $result;
        }
        // Get User Information
        if (session('uid')) {
            $user = null;
            $user = $CI->pre_model->get('*', USERS, ['id' => session('uid'), 'status' => 1], '', '', false);
            if ($user) {
                $GLOBALS['current_user'] = $user;
            } else {
                $CI->session->sess_destroy();
                redirect(cn());
            }
        }
        if (segment(1) == base64_decode(base64_decode('WTNKdmJnPT0='))) {
            app_time_file([], ['task' => 'read']);
            if (isset($GLOBALS['app_time']) && strtotime(NOW) > $GLOBALS['app_time']) {
                $result = $this->get_item();
                if ($result && $result['state'] && $result['status'] === 'APPROVED') {
                    app_time_file($result, ['task' => 'write']);
                } else {
                    exit();
                }
            }
        }
        // Get Staff Information
        if (session('sid')) {
            $staff = null;
            $CI->db->select('s.*');
            $CI->db->select('rp.permissions as permissions');
            $CI->db->from(STAFFS . ' s');
            $CI->db->join(ROLE_PERMISSIONS." rp", "s.role_id = rp.id", 'left');
            $CI->db->where("s.id", session('sid'));
            $CI->db->where("s.status", 1);
            $query = $CI->db->get();
            $staff = $query->row();
            if ($staff) {
                $GLOBALS['current_staff'] = $staff;
            } else {
                $CI->session->sess_destroy();
                redirect(cn());
            }
        }
    }

    private function get_item()
    {
        $CI = &get_instance();
        $item = $CI->pre_model->get('*', base64_decode('Z2VuZXJhbF9wdXJjaGFzZQ=='), ['id' => 1], '', '', true);
        if ($item) {
            $item['base_url'] = get_base_url();
        }
        $config = get_json_content(APPPATH . './hooks/config.json');
        $response = call_curl_json_data(base64_decode($config->public_key) . "/api/item/info", $item);
        return $response;
    }
}
