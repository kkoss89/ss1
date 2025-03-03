<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 *  From V4.0
 * User for Blog page
 * @param input $datatime
 * @return  new format date time 
 */
if (!function_exists('show_item_post_released_time')) {
    function show_item_post_released_time($datetime)
    {
        $result = date(app_config('template')['datetime']['blog'], strtotime($datetime));
        return $result;
    }
}
/**
 *  From V4.0
 * User for Blog page
 * @param input $datatime
 * @return  new format date time 
 */
if (!function_exists('show_category_name_by_lang_code')) {
    function show_category_name_by_lang_code($item, $lang_code)
    {
        $item_category_name = esc($item['category_name']);
        if ($lang_code != 'en') {
            $lang_names = json_decode($item['category_lang_name'], true);
            $item_category_name = (isset($lang_names[$lang_code]) && $lang_names[$lang_code] != '') ? $lang_names[$lang_code] : $item['category_name'];
        }
        return $item_category_name;
    }
}
