<?php

class cod {

	public $title = 'Cash On Delivery';
	public $icon = 'assets/payment_gateway/cod.png';
	public $website = '';

	function __construct($api){ 
		$this->api = $api; 
	}

	public function getPaymentGatewayView($settingData,$gatewayData){
		$view = APPPATH.'payment_gateway/views/cod.php';

		require $view;
	}

	public function setPaymentGatewayRequest($settingData,$gatewayData){
		$this->api->confirmPaymentGateway($gatewayData['id'],7,'');

		$result['success'] = true;
		$result['redirect'] = $gatewayData['redirect'];
		
		return $result;
	}
}