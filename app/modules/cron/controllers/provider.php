<?php 
defined('BASEPATH') or exit('No direct script access allowed');
require_once('cron.php');

class provider extends cron
{
    public function __construct()
    {
        $this->load->model('cron_model', 'main_model');
        $this->load->model('admin/provider_model', 'provider_model');
        $this->cron_token();
        $this->provider = new Smm_api();
        $this->tb_services = SERVICES;
    }
	
    public function sync_services()
    {
		// $inserCronLog['created_at']  = date("Y-m-d H:i:s");
		// $inserCronLog['cron_type']   = 'sync_services';
		// $inserCronLog['cron_server'] = 'aapanel';
		// $this->db->insert('cron_logs',$inserCronLog);
		
        $lock = fopen('_lock_file_multiple_services.lock', 'w');
        if (!($lock && flock($lock, LOCK_EX | LOCK_NB)))
            die('Services already running');
        $params = [
            'pagination' => [
                'limit'  => (get('limit')) ? get('limit') : 3,
                'start'  => 0,
            ],
        ];
        $items_api = $this->provider_model->list_items($params, ['task' => 'list-items-for-sync-services-on-cron']);
        if ($items_api) {
            $auto_sync_settings = get_option('auto_sync_provider_services_settings', '');
            foreach ($items_api as $key => $item_api) {
                // echo $item_api['name']. "<br>";
                // get current service
                $db_services = $this->main_model->fetch('id, ids, name, price, original_price, api_service_id, min, max, desc, dripfeed, status, refill', $this->tb_services, ['api_provider_id' => $item_api['id']], '', '', '', '', true);
                if ($db_services) {
                    $data_params                           = json_decode($auto_sync_settings, true);
                    $data_params['api_id']                 = $item_api['id'];
                    $data_params['item_provider']          = $item_api;
                    $data_params['items_provider_service'] = $this->provider->services($item_api);
                    $data_params['db_services'] = $db_services;
                    $response = $this->provider_model->crud_services($data_params, ['task' => 'sync-services']);
                }
                $number_current_services = count($db_services);
                $data_item_api = [
                    'name'                  => $item_api['name'],
                ];
                if ($number_current_services >= 50) {
                    $data_item_api['changed']    = date('Y-m-d H:i:s', strtotime(NOW) + rand(30*60, 60*60));
                } else if ($number_current_services >= 10 && $number_current_services < 50) {
                    $data_item_api['changed']    = date('Y-m-d H:i:s', strtotime(NOW) + rand(2*60*60, 3*60*60));
                } else if ($number_current_services > 0 && $number_current_services < 10) {
                    $data_item_api['changed']    = date('Y-m-d H:i:s', strtotime(NOW) + rand(3*60*60, 4*60*60));
                } else {
                    $data_item_api['changed']    = date('Y-m-d H:i:s', strtotime(NOW) + rand(3*24*60*60, 7*24*60*60));
                }
                $this->db->update(API_PROVIDERS, $data_item_api, ['id' => $item_api['id']]);
                file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/provider_sync_service_log.txt', json_encode($data_item_api) . PHP_EOL, FILE_APPEND);
            }
        } else {
            echo "There is empty api";
        }
        echo 'Successfully';
    }
	
	public function update_balance()
	{
		// $inserCronLog['created_at']  = date("Y-m-d H:i:s");
		// $inserCronLog['cron_type']   = 'update_balance';
		// $inserCronLog['cron_server'] = 'aapanel';
		// $this->db->insert('cron_logs',$inserCronLog);
		
		$lock = fopen('_lock_file_update_balance.lock', 'w');
        if (!($lock && flock($lock, LOCK_EX | LOCK_NB)))
            die('update balance already running');
        $params = [
            'pagination' => [
                'limit'  => 10,
                'start'  => 0,
            ],
        ];
        $items_api = $this->provider_model->list_items($params, ['task' => 'list-items-for-balanace_update-on-cron']);
		
        if($items_api) 
		{
			foreach ($items_api as $key => $item_api) 
			{
				$updateBalance = array();
				$id            = $item_api['id'];
				$result         = $this->provider->balance($item_api);
				
				if ($result && isset($result['balance'])) {
					$updateBalance["balance"] = $result['balance'];
					$updateBalance["id"]      = $id;
					
					$response = $this->provider_model->save_item($updateBalance, ['task' => 'balance-item']);
				}
			}
		} else {
            echo "There is empty api";
        }
		echo 'Successfully';
	}
}
