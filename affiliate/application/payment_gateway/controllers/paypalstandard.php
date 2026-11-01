<?php

class paypalstandard {

	public $title = 'Paypal Standard';
	public $icon = 'assets/payment_gateway/paypal.png';
	public $website = 'https://www.paypal.com/';

	function __construct($api){ 
		$this->api = $api; 
	}

	public function getPaymentGatewayView($settingData,$gatewayData){
		$view = APPPATH.'payment_gateway/views/paypalstandard.php';

		require $view;
	}

	public function callback($settingData,$gatewayData){
		$uncompletedData = $this->api->Product_model->getByField('uncompleted_payment','id',$gatewayData['id']);
		if($uncompletedData){
			$raw_post_data = file_get_contents('php://input');
			$raw_post_array = explode('&', $raw_post_data);
			
			file_put_contents(APPPATH.'logs/1'.time(),json_encode($raw_post_array));
			//$raw_post_array = json_decode(file_get_contents(APPPATH.'logs/test'));

			$myPost = array();
			foreach($raw_post_array as $keyval){
			  	$keyval = explode ('=', $keyval);
			  	if(count($keyval) == 2)
			    	$myPost[$keyval[0]] = urldecode($keyval[1]);
			}
			$req = 'cmd=_notify-validate';
			foreach ($myPost as $key => $value) {
			   	$value = urlencode($value);
			  	$req .= "&$key=$value";
			}

			file_put_contents(APPPATH.'logs/2'.time(),json_encode($req));

			if($settingData['sandbox_mode'])
				$ch = curl_init('https://ipnpb.sandbox.paypal.com/cgi-bin/webscr');
			else
				$ch = curl_init('https://ipnpb.paypal.com/cgi-bin/webscr');

			curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
			curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close'));
			
			if(!($res = curl_exec($ch))){
			  	curl_close($ch);
			  	file_put_contents(APPPATH.'logs/3'.time(),json_encode(curl_error($ch)));
			  	exit;
			}
			curl_close($ch);
			
			file_put_contents(APPPATH.'logs/4'.time(),json_encode($res));

			if(strcmp($res, "VERIFIED") == 0 || strcmp($res, "INVALID") == 0){
				switch($raw_post_array['payment_status']) {
					case 'Canceled_Reversal' : $status_id = $settingData['canceled_reversal_status_id']; break;
					case 'Completed'         : $status_id = $settingData['completed_status_id']; break;
					case 'Denied'            : $status_id = $settingData['denied_status_id']; break;
					case 'Expired'           : $status_id = $settingData['expired_status_id']; break;
					case 'Failed'            : $status_id = $settingData['failed_status_id']; break;
					case 'Pending'           : $status_id = $settingData['pending_status_id']; break;
					case 'Processed'         : $status_id = $settingData['processed_status_id']; break;
					case 'Refunded'          : $status_id = $settingData['refunded_status_id']; break;
					case 'Reversed'          : $status_id = $settingData['reversed_status_id']; break;
					case 'Voided'            : $status_id = $settingData['voided_status_id']; break;
				}
			}

		$this->api->confirmPaymentGateway($gatewayData['id'],$status_id,$raw_post_array['txn_id'],$raw_post_array['payment_status']);

			redirect($gatewayData['redirect']);
		}
	}
}