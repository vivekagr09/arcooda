<?php

class yookassa {

	public $title = 'Yookassa';
	public $icon = 'assets/payment_gateway/yookassa.png';
	public $website = 'https://yookassa.ru/';

	function __construct($api){ 
		$this->api = $api; 
	}

	public function getPaymentGatewayView($settingData,$gatewayData){
		$view = APPPATH.'payment_gateway/views/yookassa.php';

		require $view;
	}

	public function setPaymentGatewayRequest($settingData,$gatewayData){
		require_once(APPPATH.'payment_gateway/library/yookassa/autoload.php');

		$client = new \YooKassa\Client();
        $client->setAuth($settingData['shop_id'],$settingData['secret_key']);
		$response   = $client->createPayment(
	        array(
	            'amount' => array(
	                'value' => $gatewayData['total'],
	                'currency' => 'RUB',
	            ),
	            'confirmation' => array(
	                'type' => 'redirect',
	                'return_url' => $gatewayData['return_url'],
	            ),
	            'description' => 'Order No '. $gatewayData['id'],
	        ),
	        uniqid('', true)
	    );
	    
	    $uncompletedData = $this->api->Product_model->getByField('uncompleted_payment','id',$gatewayData['id']);
		$uncompleted['additional_info'] = serialize($response->id);
		$this->api->db->update('uncompleted_payment',$uncompleted,array('id' => $gatewayData['id']));

	    if(isset($response) && !empty($response))
	    	$json['confirmationUrl'] = $response->getConfirmation()->getConfirmationUrl();

		echo json_encode($json);
		die;
	}

	public function confirmation($settingData,$gatewayData){
		$uncompletedData = $this->api->Product_model->getByField('uncompleted_payment','id',$gatewayData['id']);
		$transaction_id  = unserialize($uncompletedData['additional_info']);

		$this->api->confirmPaymentGateway($gatewayData['id'],$settingData['order_status'],$transaction_id,'Succeeded');

		redirect($gatewayData['redirect']);
	}
}