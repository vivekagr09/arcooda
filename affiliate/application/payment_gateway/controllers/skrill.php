<?php

class skrill {

	public $title = 'Skrill';
	public $icon = 'assets/payment_gateway/skrill.png';
	public $website = 'https://www.skrill.com';

	function __construct($api){ 
		$this->api = $api; 
	}

	public function getPaymentGatewayView($settingData,$gatewayData){
		$view = APPPATH.'payment_gateway/views/skrill.php';

		require $view;
	}

	public function callback($settingData,$gatewayData){
		if(isset($_POST['id']))
			$uncompleted_id = (int)$_POST['id'];
		else
			$uncompleted_id = 0;

		$uncompletedData = $this->api->Product_model->getByField('uncompleted_payment','id',$uncompleted_id);
		if($uncompletedData) {
			$verified = true;

			if($settingData['secret']) {
				$hash  		= $_POST['merchant_id'];
				$hash 		.= $_POST['transaction_id'];
				$hash 		.= strtoupper(md5($settingData['secret']));
				$hash 		.= $_POST['mb_amount'];
				$hash 		.= $_POST['mb_currency'];
				$hash 		.= $_POST['status'];
				$md5hash 	= strtoupper(md5($hash));
				$md5sig 	= $_POST['md5sig'];
				if(($md5hash != $md5sig) || (strtolower($_POST['pay_to_email']) != strtolower($settingData['email'])))
					$verified = false;
			}

			if($verified){
				switch($_POST['status']) {
					case '2'  : 
						$status_id = $settingData['order_status']; 
						$payment_status = 'Succeeded'; 
						break;
					case '0'  : 
						$status_id = $settingData['pending_status'];
						$payment_status = 'Pending';  
						break;
					case '-1' : 
						$status_id = $settingData['canceled_status']; 
						$payment_status = 'Canceled';  
						break;
					case '-2' : 
						$status_id = $settingData['failed_status'];
						$payment_status = 'Failed!';   
						break;
					case '-3' : 
						$status_id = $settingData['chargeback_status']; 
						$payment_status = 'Chargeback';  
						break;
				}

				$this->api->confirmPaymentGateway($uncompleted_id,$status_id,$_POST['transaction_id'],$payment_status);
			}
		}
	}
}