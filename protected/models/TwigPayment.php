<?php

namespace app\models;
/**
 * @property int         $payment referal id
 * @property string      currency
 * @property string      $surname
 * @property string      $address_1
 */
class TwigPayment
{
	public function getAuthToken(){
		global $_GLOBALS;
		$method=CURLOPT_POST;
		$url='authentication/login';
		$headers = [
	        'x-api-key: ' . $_GLOBALS['site_settings']['airwallex_api_key'],
	        'x-client-id: ' . $_GLOBALS['site_settings']['airwallex_client_id'],
	        'Content-Type: application/json',
	        'Content-Length:0 ' 
	    ];
		$data = null;
		return $this->executeCurl($method,$url,$headers,$data);
	}
	private function executeCurl($method,$url,$headers,$data){
		global $_GLOBALS;
		try{
			$ch = curl_init();
	  		curl_setopt($ch, CURLOPT_URL, $_GLOBALS['site_settings']['airwallex_base_url'] . $url);
	        curl_setopt($ch, $method, true);
	        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	        if($data){
	        	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	        }
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			$response=curl_exec($ch);
			if (curl_errno($ch)) {
			    $response = curl_error($ch);
			}
			curl_close($ch);
			return $response;
		
		}catch(\Exception $e){
			return $e->getMessage();
		}
	}
}