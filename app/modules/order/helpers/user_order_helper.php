<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 *  From V4.0
 * @param array $controller_name
 * @param array $item - item data
 * @return  refill button html
 */
if (!function_exists('show_item_refill_button')) {
    function show_item_refill_button($controller_name = '', $item = [])
    {
        $xhtml = null;

        $xhtml .= '<td class="text-center  w-5p component-button-refill">';
        $limit_refill_date_time = strtotime($item['created']) + 30 * 24 * 60 * 60; //limit under 30days
        if ($item['refill'] && strtotime(NOW) < $limit_refill_date_time && $item['status'] == 'completed') {
            $limit_refill_day = strtotime($item['refill_date']);
            if (strtotime($item['refill_date']) < strtotime(NOW) && strtotime(NOW) > $limit_refill_day) {
                $title = 'Refill';
                $link  = cn('/refill/add/' . $item['ids']);
                $xhtml .= sprintf('<btn class="btn btn-sm btn-info btn-refill" data-href="%s">%s</btn>', $link, $title);
            } elseif (strtotime($item['refill_date']) >= strtotime(NOW)) {
                $title       = 'Refill';
                $refill_note = sprintf('Refill will be available in approximately %s', estimated_time_arrival_string($item['refill_date']));
                $xhtml .= sprintf('<span class="btn btn-sm  btn-gray" data-toggle="tooltip" data-placement="right" title="" data-original-title="%s">%s</span>', $refill_note, $title);
            }
        }
        $xhtml .= '</td>';
        return $xhtml;
    }
}

/**
 *  From V4.0
 * @param input $datatime
 * @return  Get different estimated time arrival hours and minutes
 */
if (!function_exists('estimated_time_arrival_string')) {
    function estimated_time_arrival_string($datetime)
    {
        $now = new DateTime;
        $next = new DateTime($datetime);
        $string = array(
            'h' => 'hour',
            'i' => 'minute',
        );
        $interval = $next->diff($now);

        foreach ($string as $k => $v) {
            if ($interval->d > 0) {
                $interval->h = $interval->h + 24 * $interval->d;
            }
            $string[$k] = $interval->$k . ' ' . $v . ($interval->$k > 1 ? 's' : '');
        }

        if ($interval->h > 0) {
            $result = $string['h'] . ' ' . $string['i'];
        } else {
            $result = $string['i'];
        }
        return $result;
    }
}

/**
 *  From V4.1
 * @param input $data Message place order successfullty
 * @return  Get message
 */
if (!function_exists('get_message_order_success')) {
    function get_message_order_success($data = [], $params = [])
    {
        $result = [];
        $title = lang('order_received');
        $message = lang('thank_you_your_order_has_been_received');
        $details = null;
        if ($data) {
            $details = '<ul>';
            foreach ($data as $key => $row) {
                $details .= '<li>' . lang($key) .': '. $row.'</li>';
            }
            $details .= '</ul>';
        }
        $result = sprintf(
            '<div class="alert alert-info order-success alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true"></span></button>
                <h3><span class="fe fe-check"></span> %s</h3>
                <div class="p-b-20"> %s</div>
                <small>
                    %s
                </small>
            </div>', $title, $message, $details
        );
        return $result;
    }
}
