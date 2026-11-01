<?php

class bank_transfer {

	public $title = 'Bank Transfer';
	public $icon = 'assets/payment_gateway/bank-transfer.png';
	public $website = '';

	function __construct($api){ 
		$this->api = $api; 
	}

	public function getPaymentGatewayView($settingData,$gatewayData){
		$view = APPPATH.'payment_gateway/views/bank_transfer.php';

		require $view;
	}

	public function setPaymentGatewayRequest($settingData,$gatewayData){
		$bank_details = [];
		$bank_details[] = $settingData['bank_details'];
		if(isset($settingData['additional_bank_details'])){
			$additional_bank_details = (array)json_decode($settingData['additional_bank_details'],1);
			foreach($additional_bank_details as $key => $value)
				$bank_details[]= $value;
		}

		$selected_index = (int)$this->api->session->userdata('bank_method_index');
		$selected_bank_details = isset($bank_details[$selected_index]) ? $bank_details[$selected_index] : '';
		
		$this->api->confirmPaymentGateway($gatewayData['id'],$gatewayData['status'],'',$selected_bank_details);

		$result['success'] = true;
		$result['redirect'] = $gatewayData['redirect'];
		
		return $result;
	}
}