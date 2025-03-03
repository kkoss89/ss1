<?php

class smm_everve {

    public $api_url;
    public $api_key;
    public $ci;
	public $format = 'json';

    public function __construct($api_params = "") {
        $this->api_url = $api_params['url'];
        $this->api_key = $api_params['key'];
        $this->ci      = &get_instance();
    }

	private function make_request($endpoint, $params = []) 
	{
        $params['api_key'] = $this->api_key;
        $params['format']  = $this->format;
        $url               = $this->api_url .'/'. $endpoint . '?' . http_build_query($params);
		
        $response          = file_get_contents($url);
        return json_decode($response, true);
    }
	
	private function connect($endpoint,$post) 
	{
		$post['api_key'] = $this->api_key;
        $post['format']  = $this->format;
		$url = $this->api_url .'/'. $endpoint . '?' . http_build_query($post);
		
		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL            => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING       => "",
			CURLOPT_MAXREDIRS      => 10,
			CURLOPT_TIMEOUT        => 30,
			CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST  => "POST",
			CURLOPT_HTTPHEADER     => array(
				"content-type: application/json"
			),
		));
	
		$response = curl_exec($curl);
		$err      = curl_error($curl);
		curl_close($curl);
		
		if ($err) {
			return false;
		} else {
			$response = json_decode($response,true);
			return $response;
		}
    }
	
	public function balance() 
	{
		$response = $this->make_request('user');
		if(!empty($response)){
			$returndata = array(
				'id' => $response['user_id'],
				'balance' => $response['user_balance']
			);
			
			return $returndata;
		} else {
			return [];
		}
    }
	
	public function services($id = null)
	{
		$endpoint = $id ? "categories/{$id}" : 'categories';
        $response = $this->make_request($endpoint);
		if(!empty($response)){
			$returData = array();
			foreach($response as $res){
				$temparray = array(
					'service'   => $res['category_id'],
					'name'      => $res['category_name'],
					'category'  => $res['category_name_short'],
					'rate'      => $res['category_price_usd'] * 1000,
					'type'      => 'default',
					'dripfeed'  => 0
				);
				
				array_push($returData,$temparray);
			}
			
			return $returData;
		} else {
			return [];
		}
	}
	
	public function order($data)
	{
		if(!empty($data) && isset($data['service'])){
			$request = array(
				'category_id'         => ($data['service']) ? $data['service'] : 0,
				'order_url'           => ($data['link']) ? $data['link'] : '',
				'order_overall_limit' => ($data['quantity']) ? $data['quantity'] : 0,
				'order_custom_data'   => ($data['comments']) ? $data['comments'] : ''
			);
			
			$order = $this->connect('orders',$request);
			 
			if(isset($order) && !empty($order)){
				$response = array(
					'order' => $order['order_id']
				);
				
				return $response;
			} else {
				return [];
			}
		} else {
			return [];
		}
	}
	
	public function status($id)
	{
		$endpoint = $id ? "orders/{$id}" : 'orders';
        $response = $this->make_request($endpoint);
		if(!empty($response)){
			$returData = array();
			
			if(isset($response['order_id'])){
				$order_status = 'Pending';
				if($response['order_status'] == 'in_progress'){
					$order_status = 'Inprogress';
				} elseif($response['order_status'] == 'completed') {
					$order_status = 'Completed';
				} elseif($response['order_status'] == 'deleted') {
					$order_status = 'Canceled';
				} elseif($response['order_status'] == 'blocked') {
					$order_status = 'Partial';
				}
				
				$order_initial_social_counter = ($response['order_initial_social_counter']) ? $response['order_initial_social_counter'] : 0;
				$order_overall_limit          = ($response['order_overall_limit']) ? $response['order_overall_limit'] : 0;
				$order_overall_counter        = ($response['order_overall_counter']) ? $response['order_overall_counter'] : 0;
				$remains                      = $order_overall_limit - $order_overall_counter;
				
				$returData = array(
					'start_count' => $order_initial_social_counter,
					'remains'     => $remains,
					'status'      => $order_status 
				);
			}
			
			return $returData;
		} else {
			return [];
		}
	}
	
    /*private function Connect($endpoint, $params) {
        $url = $this->api_url . '/' . $endpoint . '/?' . http_build_query($params);
        $c = curl_init();
        curl_setopt($c, CURLOPT_URL, $url);
        curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);
        $contents = curl_exec($c);
        curl_close($c);

        if ($contents)
            return json_decode($contents)->user_balance; // Return user_balance
        else
            return FALSE;
    }*/

}
