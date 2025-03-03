<?php
defined('BASEPATH') or exit('No direct script access allowed');

class admin extends My_AdminController
{

    private $tb_main = USERS;

    public function __construct()
    {
        parent::__construct();
        $this->load->model(get_class($this) . '_model', 'main_model');

        $this->controller_name = strtolower(get_class($this));
        $this->controller_title = ucfirst(str_replace('_', ' ', get_class($this)));
        $this->path_views = "admin";
        $this->params = [];
        $this->tb_main = USERS;
        $this->tb_staffs = STAFFS;
    }

    public function index()
    {
        if (is_admin_logged_in()) {
            redirect(admin_url('users'));
        } else {
            redirect(cn());
        }
    }

    public function login()
    {
        if (is_admin_logged_in()) {
            redirect(admin_url('users'));
        }

        if ($this->input->is_ajax_request()) {
            if (!$this->input->is_ajax_request()) {
                redirect(admin_url());
            }
            $email = post("email");
            $password = post("password");
            $remember = post("remember");

            if ($email == "") _validation('error', 'Email is required');
            if ($password == "") _validation('error', 'Password is required');

            // Check Google Recaptcha
            if (isset($_POST['g-recaptcha-response']) && get_option("enable_goolge_recapcha", '') && get_option('google_capcha_site_key') != "" && get_option('google_capcha_secret_key') != "") {
                require APPPATH . './libraries/Google_recaptcha/autoload.php';
                $goolge_recapcha = new \ReCaptcha\ReCaptcha(get_option('google_capcha_secret_key'));
                $resp = $goolge_recapcha->setExpectedHostname($_SERVER['SERVER_NAME'])
                    ->verify($_POST['g-recaptcha-response'], $_SERVER['REMOTE_ADDR']);
                    
                if (!$resp->isSuccess()) _validation('error', 'Please verify recaptcha');
            }
            $item = $this->main_model->get("id, status, ids, email, password, first_name, last_name, timezone", $this->tb_staffs, ['email' => $email], '', '', true);
            $error = false;
            // check the last hash password
            if ($item && $this->main_model->app_password_verify(post("password"), $item['password'])) {
                $error = false;
            } else {
                $error = true;
            }
            if (!$error) {
                if (!$item['status']) {
                    _validation('error', 'Your account has not been activated!');
                }
                set_session("sid", $item['id']);
                $data_session = array(
                    'first_name' => $item['first_name'],
                    'last_name' => $item['last_name'],
                    'timezone' => $item['timezone'],
                );
                /*----------  Insert User logs  ----------*/
                set_session('staff_current_info', $data_session);
                // Update new Reset key, ids key
                $item_update = [
                    'reset_key' => ids(),
                    'history_ip' => get_client_ip(),
                    'ids' => ids(),
                ];
                $this->db->update($this->tb_staffs, $item_update, ['id' => $item['id']]);
                _validation('success', 'Login successfully');
            } else {
                _validation('error', "Email address and password that You entered doesn't match any account. Please check your account again");
            }
        } else {
            $data = array();
            $this->template->set_layout('auth');
            $this->template->build($this->path_views . '/auth/sign_in', $data);
        }
    }

    public function profile()
    {
        $item = $this->main_model->get("id, status, ids, email, password, first_name, last_name, timezone", $this->tb_staffs, ['id' => session('sid')], '', '', true);
        $data = array(
            "controller_name" => $this->controller_name,
            "item" => $item,
        );
        $this->template->build('profile/profile', $data);
    }

    public function store()
    {
        if (!$this->input->is_ajax_request()) {
            redirect(admin_url($this->controller_name));
        }

        if ($this->input->post('store_type') == 'update_info') {
            $task = 'update-info-item';
            $this->form_validation->set_rules('first_name', 'first name', 'trim|required|xss_clean');
            $this->form_validation->set_rules('last_name', 'last name', 'trim|required|xss_clean');
            $this->form_validation->set_rules('timezone', 'timezone', 'trim|required|xss_clean');
        }

        if ($this->input->post('store_type') == 'change_pass') {
            is_demo_version();
            $task = 'change-pass-item';
            $this->form_validation->set_rules('old_password', 'old password', 'trim|required|min_length[6]|max_length[25]|xss_clean');
            $this->form_validation->set_rules('password', 'new password', 'trim|required|min_length[6]|max_length[25]|differs[old_password]|xss_clean');
            $this->form_validation->set_rules('confirm_password', 'confirm password', 'trim|required|min_length[6]|max_length[25]|matches[password]|xss_clean');
            //Check secret key
            $is_valid_secret_key = $this->main_model->verify_admin_access(['secret_key' => post('old_password')], ['task' => 'check-admin-secret-key']);
            if (!$is_valid_secret_key) {
                _validation('error', 'The old password you entered does not match your existing password');
            }
        }
        
        if ($this->input->post('store_type') == 'update_email_template') {
            $task = 'update-email-template';
    
            // Apply XSS filtering to other fields
            $this->form_validation->set_rules('verification_email_subject', 'Verification Email Subject', 'xss_clean');
    
            // Retrieve `verification_email_content` WITHOUT XSS filtering
            $content = $this->input->post('verification_email_content', false);
        }
        
        
        if (!$this->form_validation->run()) {
            _validation('error', validation_errors());
        }
        
        $data = [
        'verification_email_subject' => post('verification_email_subject'),
        'verification_email_content' => $content,
        ];
        
        $response = $this->main_model->save_item($this->params, ['task' => $task]);
        ms($response);
        
        print_r($_POST); 
        exit(); // Stop execution to check the output
    }
 
    public function logout()
    {
        unset_session("sid");
        unset_session("auto_confirm");
        unset_session("staff_current_info");
        $this->session->sess_destroy();
        if (get_option("is_maintenance_mode")) {
            delete_cookie("verify_maintenance_mode");
        }
        redirect(cn(''));
    }
}
