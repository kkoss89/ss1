<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
 
/**
 * From V3.6
 * Get app_config
 * @param string #params
 * @return config data
 */

if (!function_exists('app_config')) {
    function app_config($params)
    {
        $CI = &get_instance();
        $value = $CI->config->item($params);
        return $value;
    }
}

/**
 * From V3.6
 * Performs simple auto-escaping of data for security reasons.
 * Might consider making this more complex at a later date.
 *
 * If $data is a string, then it simply escapes and returns it.
 * If $data is an array, then it loops over it, escaping each
 * 'value' of the key/value pairs.
 *
 * Valid context values: html, js, css, url, attr, raw, null
 *
 * @param array|string $data
 * @param string       $encoding
 *
 * @throws InvalidArgumentException
 *
 * @return array|string
 */
if (!function_exists('esc')) {
    function esc($data, $context = 'html', $encoding = null)
    {
        if (is_array($data)) {
            foreach ($data as &$value) {
                $value = esc($value, $context);
            }
        }

        if (is_string($data)) {
            $context = strtolower($context);

            // Provide a way to NOT escape data since
            // this could be called automatically by
            // the View library.
            if (empty($context) || $context === 'raw') {
                return $data;
            }

            if (!in_array($context, ['html', 'js', 'css', 'url', 'attr'], true)) {
                throw new InvalidArgumentException('Invalid escape context provided.');
            }

            $method = $context === 'attr' ? 'escapeHtmlAttr' : 'escape' . ucfirst($context);

            static $escaper;
            $CI = &get_instance();
            $CI->load->library('Escaper');
            if (!$escaper) {
                $escaper = new Escaper($encoding);
            }

            if ($encoding && $escaper->getEncoding() !== $encoding) {
                $escaper = new Escaper($encoding);
            }

            $data = $escaper->{$method}($data);
        }

        return $data;
    }
}


/**
 * From V4.0
 * Prevent simultaneous execution
 * @param array #params
 */

if (!function_exists('lock_file')) {
    function lock_file($params = [], $options = [])
    {
        if (isset($params['file_name'])) {
            $file_path = 'assets/tmp/lock_file_'. $params['file_name'] .'.lock';
        } else {
            $file_path = 'assets/tmp/lock_file_default.lock';
        }
        $text_notification = (isset($params['title_message'])) ? $params['title_message'] : 'already running!';
        $lock = fopen($file_path, 'w'); 
        if ( !($lock && flock($lock, LOCK_EX | LOCK_NB)) ) {
            exit($text_notification);
        }
        return true;
    }
}


/**
 * @param $table
 * @return boole
 */
if (!function_exists('is_table_exists')) {
    function is_table_exists($table = '')
    {
        $CI = &get_instance();
        if ($table && $CI->db->table_exists($table)) {
            return true;
        }
        return false;
    }
}

/**
 * @return cron key
 */
if (!function_exists('get_cron_key')) {
    function get_cron_key()
    {
        $cron_key =  mb_substr(ENCRYPTION_KEY, 0, 10);
        return $cron_key;
    }
}

/**
 * @param array $data_current, data_new
 * return json data payment log before saving to transaction log
 */
if (!function_exists('payment_transaction_log')) {
    function payment_transaction_log($data_current = [], $data_new = [])
    {
        $arr_log_new = [];
        $result = [];
        if (is_array($data_new) && $data_new) {
            $arr_log_new = [
                'time' => NOW,
                'data' => $data_new
            ];
            // Exists Data current and new
            if (is_array($data_current) && $data_current) {
                array_push($data_current, $arr_log_new);
                $result = $data_current;
            } else if (!$data_current) {
                $result[] = [
                    'time' => NOW,
                    'data' => $data_new
                ]; 
            }
        }
        return json_encode($result);
    }
}


/**
 * @return base_url
 */
if (!function_exists('get_base_url')) {
    function get_base_url()
    {
        $base_url = (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1) || isset($_SERVER['HTTP_X_FORWARDED_PROTO']) &&
        $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') ? "https" : "http";
        $base_url .= "://" . $_SERVER['HTTP_HOST'];
        $base_url .= str_replace(basename($_SERVER['SCRIPT_NAME']), "", $_SERVER['SCRIPT_NAME']);
        return $base_url;
    }
}


/**
 * Check Demo Permission
 * @return redirect | validation message
 */
if (!function_exists('is_demo_version')) {
	function is_demo_version()
    {
        $CI = &get_instance();
        if (DEMO_VERSION && $CI->input->is_ajax_request()) {
            _validation('error', "For security reasons, in demo version there have been disabled some features");
            die();
        } else {
            return true;
        }
	}
}