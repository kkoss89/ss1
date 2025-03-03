<?php 
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Include class
 */
class Smm_api
{
    protected $provider_services_dir;
    protected $provider_services_limit_update_time;

    public function __construct()
    {
        $this->provider_services_dir = $this->create_dir(['path' => "public/provider_services/"]);
        $this->provider_services_limit_update_time = 15; //minutes
        require_once 'smms/standard.php';
		require_once 'smms/everve.php';
    }

    public function services($api_params = [], $option = null)
    {
        $items = null;
        switch ($option) {
            case 'directly':
                $api = new smm_standard($api_params);
                $items = $api->services();
                break;

            case 'json':
                $items = $this->crud_provider_services_json_file(['api' => $api_params], ['task' => 'read']);
                break;
            
            default:
                $items = $this->crud_provider_services_json_file(['api' => $api_params], ['task' => 'read']);
                if (empty($items)) {
					if(!empty($api_params) && isset($api_params['id']) && $api_params['id'] == 2){
						$api   = new smm_everve($api_params);
						$items = $api->services();
					} else {
						$api   = new smm_standard($api_params);
						$items = $api->services();
					}
                    
                    $this->crud_provider_services_json_file(['api' => $api_params, 'data_services' => $items], ['task' => 'create']);
                }
                break;
        }
		
        return $items;
    }

    public function order($api_params = [], $data_post = [])
    {
		if(!empty($api_params) && isset($api_params['id']) && $api_params['id'] == 2){ 
			$api    = new smm_everve($api_params);
			$result = $api->order($data_post);
			return $result;	
		} else {
			$api = new smm_standard($api_params);
			$result = $api->order($data_post);
			return $result;
		}
    }

    public function status($api_params = [], $order_id = '')
    {
		if(!empty($api_params) && isset($api_params['id']) && $api_params['id'] == 2){
			$api = new smm_everve($api_params);
			$result = $api->status($order_id);
			return $result;	
		} else {
			$api = new smm_standard($api_params);
			$result = $api->status($order_id);
			return $result;
		}
    }

    public function multiStatus($api_params = [], $order_ids = '')
    {
        $api = new smm_standard($api_params);
        $result = $api->multiStatus($order_ids);
        return $result;
    }

    public function balance($api_params = [])
    {
		if(!empty($api_params) && isset($api_params['id']) && $api_params['id'] == 2){
			$api = new smm_everve($api_params);
			$result = $api->balance();
			return $result;	
		} else {
			$api = new smm_standard($api_params);
			$result = $api->balance();
			return $result;	
		}
    }

    public function refill($api_params = [], $order_id = '')
    {
        $api = new smm_standard($api_params);
        $result = $api->refill($order_id);
        return $result;
    }

    public function refill_status($api_params = [], $refill_id = '')
    {
        $api = new smm_standard($api_params);
        $result = $api->refill_status($refill_id);
        $result = $this->re_format_refill_status($result);
        return $result;
    }

    private function re_format_refill_status($result = '')
    {   
        if (isset($result['status'])) {
            if(strpos($result['status'], "tasks.status.") !== false){
                $result = explode('tasks.status.', $result['status']);
                if (isset($result[1])) {
                    return ['status' => ucfirst($result[1])];
                }
            }
        }
        return $result;
    }

    public function crud_provider_services_json_file($params = null, $option = null)
    {
        // Delete old Json services  list
        $provider_services_json_path =$this->provider_services_dir . $this->provider_json_file_name($params['api']);
		
        //Delete
        if ($option['task'] == 'delete') {
            if (file_exists($provider_services_json_path)) {
                unlink($provider_services_json_path);
            }
        }

        // Update new services list
        if ($option['task'] == 'update') {
            $this->services($params['api']);
        }

        // Read services list
        if ($option['task'] == 'read') {
            if (!file_exists($provider_services_json_path)) {
                return false;
            }
            $data_api   = json_decode(file_get_contents($provider_services_json_path), true);
            if (!isset($data_api['data'])) {
                return false;
            }
            $last_time = strtotime(NOW) - ($this->provider_services_limit_update_time * 60);
            if (strtotime($data_api['time']) > $last_time) {
                return $data_api['data'];
            }
            return false;
        }

        // Save services list
        if ($option['task'] == 'create') {
            $mode 		= (isset($params['mode'])) ? $params['mode'] : 'w';
            $content 	= json_encode(['time' => NOW , 'data' =>  $params['data_services']], JSON_PRETTY_PRINT);
            $handle 	= fopen($provider_services_json_path, $mode);
            if ( is_writable($provider_services_json_path) ){
                fwrite($handle, $content);
            }
            fclose($handle);
        }

    }

    private function provider_json_file_name($api_params = [])
    {
        if (isset($api_params['id']) && isset($api_params['name'])) {
            $name = trim(str_replace(' ', '_', strtolower($api_params['name'])));
            return $api_params['id'] . '-' . $name . '.json';
        } else {
            return $name . '.json';
        }
    }
    
    private function create_dir($params = null, $option = null)
    {
        $path = FCPATH . $params['path'];
        $mode = (isset($option['mode'])) ? $option['mode'] : '0777'; 
        if (!file_exists($params['path'])) {
            $uold = umask(0);
            mkdir($path, 0777);
            umask($uold);
            file_put_contents($path . "index.html", "<h1>404 Not Found</h1>");
        } else {
            return $path;
        }
    }
}
