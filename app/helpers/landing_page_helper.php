<?php
defined('BASEPATH') or exit('No direct script access allowed');


/**
 *  From V4.0
 * render html header nav link on landing page
 * 
 */
if (!function_exists('render_header_nav_ul')) {
    function render_header_nav_ul($theme_type = '', $params = '')
    {
        $header_nav_elements = app_config('client')['header_nav'];
        if (!get_option('enable_service_list_no_login', 0)) unset($header_nav_elements['services']);
        if (!get_option('enable_api_tab', 0)) unset($header_nav_elements['api']);
        if (!is_table_exists(BLOG_CATEGORIES)) unset($header_nav_elements['blog']);
        $xhtml_header_nav = '<ul class="navbar-nav ml-auto">';
        foreach ($header_nav_elements as $key => $item_li) {
            $item_li_name = lang($item_li['name']);
            $class_active = ($item_li['route-name'] == segment(1)) ? 'active' : '';
            $link = cn($item_li['route-name']);
            $xhtml_header_nav .= sprintf(
            '<li class="nav-item %s">
                <a class="nav-link js-scroll-trigger" href="%s">%s</a>
            </li>', $class_active, $link, lang($item_li['name']));
        }
        $xhtml_header_nav .= '</ul>';
        return $xhtml_header_nav;
    }
}


if (!function_exists('landing_page_count_area')) {
    function landing_page_count_area($task = '')
    {
        $CI = &get_instance();
        if (empty($CI->help_model)) {
            $CI->load->model('model', 'help_model');
        }
        if ($task == 'orders') {
            $item = $CI->help_model->get("id", ORDER, '', 'id', 'DESC', true);
            if ($item) {
               return $item['id'];
            } else {
                return rand(383323, 562729);
            }
        }
        if ($task == 'services') {
            $items = $CI->help_model->count_results("id", SERVICES, ['status' => 1]);
            if ($items >= 0) {
                return $items;
             } else {
                return 0;
             }
        }
        
    }
}
