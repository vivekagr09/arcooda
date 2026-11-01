<?php
class flutterwave {

	public $title = 'Flutterwave';
	public $icon = 'assets/payment_gateway/flutterwave.png';
	public $website = 'https://www.flutterwave.com';

	function __construct($api){ 
		$this->api = $api; 
	}

	public function getPaymentGatewayView($settingData,$gatewayData){
		$view = APPPATH.'payment_gateway/views/flutterwave.php';

		require $view;
	}

	public function callback($settingData,$gatewayData){
		$transaction_id = $_GET['transaction_id'];
		$transaction_status =$_GET['status'];
		
		$uncompletedData = $this->api->Product_model->getByField('uncompleted_payment','id',$gatewayData['id']);
		if($uncompletedData){
			if(isset($transaction_status) && $transaction_status == "successful")
				$this->api->confirmPaymentGateway($gatewayData['id'],$settingData['order_success_status'],$transaction_id,'Succeeded');
			else
				$this->api->confirmPaymentGateway($gatewayData['id'],$settingData['order_failed_status'],$transaction_id,'Failed!');

			redirect($gatewayData['redirect']);
		}
	}
}