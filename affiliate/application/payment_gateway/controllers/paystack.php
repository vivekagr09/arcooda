<?php

class paystack {

	public $title = 'paystack';
	public $icon = 'assets/payment_gateway/paystack.png';
	public $website = 'https://paystack.com/';

	function __construct($api){ 
		$this->api = $api; 
	}

	public function getPaymentGatewayView($settingData,$gatewayData){
		$view = APPPATH.'payment_gateway/views/paystack.php';

		require $view;
	}

	public function setPaymentGatewayRequest($settingData,$gatewayData){
        $paystackData = array(
								'email'     			=> $gatewayData['email'],
								'api_token' 			=> $settingData['public_key'],
					            'amt'       			=> $gatewayData['total'],
					            'currency'       		=> $gatewayData['currency']
					        );

     	return $paystackData;
	}

	public function update($settingData,$gatewayData){
    	$post = $this->api->input->post();

    	if($post['reference'])
    		$this->api->confirmPaymentGateway($gatewayData['id'],$settingData['order_success_status_id'],$post['reference'],'Succeeded');
    	else 
    		$this->api->confirmPaymentGateway($gatewayData['id'],$settingData['order_failed_status_id'],'','Failed');
		
        $json['redirect'] = $gatewayData['redirect'];
        
        echo json_encode($json);
        die;
    }
}