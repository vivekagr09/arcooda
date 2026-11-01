<?php

class razorpay {

	public $title = 'Razorpay';
	public $icon = 'assets/payment_gateway/razorpay.png';
	public $website = 'https://www.razorpay.com';

	function __construct($api){ 
		$this->api = $api; 
	}

	public function getPaymentGatewayView($settingData,$gatewayData){
		require_once(APPPATH.'payment_gateway/library/razorpay/Razorpay.php');
		
		if((int)$settingData['environment'] == 1) {
			$razorpay_key_id =  $settingData['live_key_id'];
			$razorpay_key_secret =  $settingData['live_key_secret'];
		} else {
			$razorpay_key_id =  $settingData['test_key_id'];
			$razorpay_key_secret =  $settingData['test_key_secret'];
		}
    	
    	$apiData = new Razorpay\Api\Api($razorpay_key_id, $razorpay_key_secret);
        try {
        	$paymentData['receipt'] = $gatewayData['id'];
			$paymentData['amount'] = $gatewayData['total'];
			$paymentData['currency'] = $gatewayData['currency_code'];
			$paymentData['payment_capture'] = '0';

            $apiResponce = $apiData->order->create($paymentData);
        }

        catch (Exception $e) { 
            $apiResponce['error'] = $e->getMessage();    
        }        

		if (isset($apiResponce['id']) && !empty($apiResponce['id']) )
			$razorpay_id = $apiResponce['id'];

		$view = APPPATH.'payment_gateway/views/razorpay.php';

		require $view;
	}

	public function callback($settingData,$gatewayData){
		$uncompletedData = $this->api->Product_model->getByField('uncompleted_payment','id',$gatewayData['id']);
		if($uncompletedData){
			$post_data = $this->api->input->post();
			$razorpay_payment_id = $post_data['razorpay_payment_id'];
			if(isset($razorpay_payment_id) && $razorpay_payment_id != '')
				$this->api->confirmPaymentGateway($gatewayData['id'],$settingData['order_success_status'],$razorpay_payment_id,'Succeeded');
			else
			   	$this->api->confirmPaymentGateway($gatewayData['id'],$settingData['order_failed_status'],$razorpay_payment_id,'Failed!');

			redirect($gatewayData['redirect']);
		}
	}
}