<?php 
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * From V4.0
 * Sort Services by ID
 * @param string $order status
 * @return array
 */
if (!function_exists('order_status_update_form')) {
    function order_status_update_form($controller_name, $status)
    {
        $tmpl_status = app_config('template')['order_status'];
        $order_status_array = [];

        if ($controller_name == 'subscriptions') {
            $order_status_array = ['canceled', 'completed', 'expired'];
        }

        if ($controller_name == 'dripfeed') {
            switch ($status) {
                case 'active':
                    $order_status_array = ['completed', 'canceled'];
                    break;

                case 'canceled':
                    $order_status_array = ['canceled'];
                    break;

                default:
                    $order_status_array = ['canceled', 'completed'];
                    break;
            }
        }

        if ($controller_name == 'order') {
            switch ($status) {
                case 'canceled':
                    $order_status_array = ['canceled'];
                    break;
                case 'completed':
                    $order_status_array = ['completed', 'canceled', 'partial'];
                    break;

                case 'partial':
                    $order_status_array = ['canceled'];
                    break;

                case 'error':
                    $order_status_array = ['canceled', 'error', 'partial', 'pending', 'inprogress', 'completed'];
                    break;

                default:
                    $order_status_array = ['pending', 'processing', 'inprogress', 'completed', 'partial', 'canceled'];
                    break;
            }
        }
        $form_status = array_intersect_key($tmpl_status, array_flip($order_status_array));
        $result = array_combine(array_keys($form_status), array_column($form_status, 'name'));
        if (isset($result['canceled']) && !staff_has_permission($controller_name, 'cancel')) {
            unset($result['canceled']);
        }
        if (isset($result['partial']) && !staff_has_permission($controller_name, 'partial')) {
            unset($result['partial']);
        }
        return $result;
    }
}

/**
 * return user logged data
 */
if (!function_exists("current_logged_staff")) {
    function current_logged_staff()
    {
        return $GLOBALS['current_staff'];
    }
}

/**
 * Is staff loggined
 * @return boolean
 */
if (!function_exists('is_current_logged_staff')) {
	function is_current_logged_staff()
    {
        if (session('sid') && $GLOBALS['current_staff'] != null &&  $GLOBALS['current_staff']->id == session('sid')) {
            return true;
        }
		return false;
	}
}

/**
 * Check Staff has permission
 * @return boolean
 */
if (!function_exists('staff_has_permission')) {
	function staff_has_permission($controller_name = '', $role = '')
    {
        if (session('sid') && $GLOBALS['current_staff']) {
            // Supper Admin
            if ($GLOBALS['current_staff']->role_id != 1 && $GLOBALS['current_staff']->admin = 10  && isset($GLOBALS['current_staff']->permissions) && $GLOBALS['current_staff']->permissions) {
                $permissions = json_decode($GLOBALS['current_staff']->permissions, true);
                $result = 0; 
                if (isset($permissions[$controller_name])) {
                    if ($permissions[$controller_name]['index'] && $role != 'index') {
                        if (empty($permissions[$controller_name]['rules'])) {
                            $result = 1;
                        } else if (isset($permissions[$controller_name]['rules'][$role])) {
                            $result = $permissions[$controller_name]['rules'][$role];
                        }
                    } else {
                        $result = $permissions[$controller_name]['index'];
                    }
                }
                return ($result) ? true : false;

            } else if ($GLOBALS['current_staff']->role_id == 1 && $GLOBALS['current_staff']->admin == 1) {
                return true;
            }
        }
		return false;
	}
}

/**
 * Check Staff check role permission 
 * @return redirect | validation message
 */
if (!function_exists('staff_check_role_permission')) {
	function staff_check_role_permission($controller_name = '', $role = 'index')
    {
        
        $CI = &get_instance();
        $has_permission = staff_has_permission($controller_name, $role);
        if (!$has_permission) {
            if ($CI->input->is_ajax_request()) {
                _validation('error', "403 Forbidden : You don't have permission to access");
            } else {
                redirect(admin_url('profile'));
            }
            exit();
        } else {
            return true;
        }
	}
}

if (!function_exists('limit_controllers')) {
	function limit_controllers($item_controllers = [])
    {
        if (!is_table_exists(USER_LOGS) && isset($item_controllers['users_activity'])) unset($item_controllers['users_activity']);
        if (!is_table_exists(USER_BLOCK_IP) && isset($item_controllers['users_activity'])) unset($item_controllers['users_banned_ip']);
        if (!is_table_exists(AFFILIATE) || !get_option('affiliate_mode', 0)) unset($item_controllers['affiliates']);
        if (!is_table_exists(ORDERS_REFILL) && isset($item_controllers['refill'])) unset($item_controllers['refill']);
        return $item_controllers;
	}
}

/**
 * Render Report html
 */
if (!function_exists('render_report_tbody')) {
    function render_report_tbody($controller_name, $params = [], $options = [])
    {
        $xhtml = null;
        // create data total per month
        $data_sum = [];
        for ($i=1; $i <= 12 ; $i++) { 
            $data_sum[$i] = 0;
        }
        // html day by day
        $xhtml .= '<tbody>';
        for ($day = 1; $day <= 31; $day++ ) { 
            $xhtml .= '<tr>';
            $xhtml .= sprintf('<td class="text-center small">%s</td>', $day);
            for ($month = 1; $month <= 12; $month++) {
                $datetime = date("Y"). "-" . $month . "-" . $day;
                $day_value = (isset($params['data_reports'][$datetime])) ? $params['data_reports'][$datetime] : 0;
                $data_sum[$month] +=  $day_value;
                $xhtml .= sprintf('<td class="text-center small">%s</td>', report_data_format(['value' => $day_value], $options));
            }
            $xhtml .= '</tr>';
        }
        // html total by month
        $xhtml .= '<tr>';
        $xhtml .= '<td class="text-center"><h5>Total</h5></td>';
        for ($month = 1; $month <= 12; $month++) {
            $total_per_month = $data_sum[$month];
            $total_per_month = report_data_format(['value' => $total_per_month], $options);
            $xhtml .= sprintf('<td class="text-center"><h5>%s</h5></td>', $total_per_month);
        }
        $xhtml .= '</tr></tbody>';
        return $xhtml;
    }
}

if (!function_exists('render_report_tbody_2')) {
    function report_data_format($params = [], $option = []) {
        $result = "";
        if (isset($params['value']) && $params['value']) {
            $result = $params['value'];
        }
        switch ($option['task']) {
            case 'payments':
                $result = number_format((double)$result, 2, '.','');
                break;
            case 'profits':
                $result = number_format((double)$result, 2, '.','');
                break;
            default:
                $result = $params['value'];
                break;
        }
        return $result;
    }
}

/**
 * Render Report html
 */
if (!function_exists('render_report_tbody_2')) {
    function render_report_tbody_2($controller_name, $params = [], $options = [])
    {
        $xhtml = null;
        // create data total per month
        $data_sum = [];
        for ($i=1; $i <= 12 ; $i++) { 
            $data_sum[$i] = 0;
        }
        // html day by day
        $xhtml .= '<tbody>';
        for ($day = 1; $day <= 31; $day++ ) { 
            $xhtml .= '<tr>';
            $xhtml .= sprintf('<td class="text-center small">%s</td>', $day);
            for ($month = 1; $month <= 12; $month++) {
                $day_value = get_report_data(['day' => $day, 'month' => $month], $options);
                $data_sum[$month] +=  $day_value;
                $xhtml .= sprintf('<td class="text-center small">%s</td>', $day_value);
            }
            $xhtml .= '</tr>';
        }
        // html total by month
        $xhtml .= '<tr>';
        $xhtml .= '<td class="text-center"><h5>Total</h5></td>';
        for ($month = 1; $month <= 12; $month++) {
            $total_per_month = $data_sum[$month];
            if ($options['task'] == 'payments') {
                $total_per_month = number_format($total_per_month, 2, '.','');
            }
            $xhtml .= sprintf('<td class="text-center"><h5>%s</h5></td>', $total_per_month);
        }
        $xhtml .= '</tr></tbody>';
        return $xhtml;
    }
}

/**
 * @param array $params ($day, $month, $year)
 * @param array $options ($task)
 * @return value by day
 */
if (!function_exists('get_report_data')) {
    function get_report_data($params = [], $options = [])
    {
        $result = null; // , 
        $CI = &get_instance();
        $year = (isset($params['year'])) ? $params['year'] : date("Y"); //
        $first 	= $year . "-" . $params['month'] . "-" . $params['day'] . " 00:00:00";
        $last 	= $year . "-" . $params['month'] . "-" . $params['day'] . " 23:59:59";
        if (in_array($options['task'], ['tickets', 'orders'])) {
            $table = ORDER;
            if ($options['task'] == 'tickets') {
                $table = TICKETS; 
            }
            $CI->db->select('id');
            $CI->db->from($table);
            $CI->db->where('created >=', $first);
            $CI->db->where('created <=', $last);
            $result = $CI->db->get()->num_rows();
        }
        if ($options['task'] == 'payments') {
            $CI->db->select_sum('amount');
            $CI->db->from(TRANSACTION_LOGS);
            $CI->db->where('created >=', $first);
            $CI->db->where('created <=', $last);
            $CI->db->where('status', 1);
            $result = $CI->db->get()->result_array();
            $result = (double)$result['0']['amount'];
            $result = number_format($result, 2, '.','');
        }
        if ($options['task'] == 'profit') {
            $CI->db->select_sum('profit');
            $CI->db->from(ORDER);
            $CI->db->where('created >=', $first);
            $CI->db->where('created <=', $last);
            $CI->db->where('is_drip_feed !=', 1);
            $result = $CI->db->get()->result_array();
            $result = (double)$result['0']['profit'];
            $result = number_format($result, 2, '.','');
        }
        return $result;
    }
}