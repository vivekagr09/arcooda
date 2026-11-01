<?php

class xendit {

	public $title = 'Xendit';
	public $icon = 'assets/payment_gateway/xendit.png';
	public $website = 'https://www.Xendit.com';

	function __construct($api){ 
		$this->api = $api; 
	}

	public function getPaymentGatewayView($settingData,$gatewayData){
		require_once(APPPATH . 'payment_gateway/library/xendit/XenditPHPClient.php');

		$options['success_redirect_url'] = $gatewayData['status_url'];
		$options['failure_redirect_url'] = $gatewayData['cancel_url'];
		$options['secret_api_key'] = ($settingData['environment'] == 1) ? $settingData['live_secret_key'] : $settingData['test_secret_key'];
       	$xenditPHPClient = new XenditClient\XenditPHPClient($options);

       	try {
            $apiResponce =  $xenditPHPClient->createInvoice(strval($gatewayData['id']), $gatewayData['total'], $gatewayData['email'], $gatewayData['firstname'].' '.$gatewayData['lastname'], $options);
        }
        catch (Exception $e) { 
            $apiResponce['error'] = $e->getMessage();    
        }

		if (isset($apiResponce['id']) && !empty($apiResponce['id']) ) {
			$this->api->session->set_userdata('xendit_id',$apiResponce['id']);
			$action    = $apiResponce['invoice_url'];
		}

		$view = APPPATH.'payment_gateway/views/xendit.php';

		require $view;
	}

	public function callback($settingData,$gatewayData){
		require_once(APPPATH . 'payment_gateway/library/xendit/XenditPHPClient.php');

		$options['secret_api_key'] = ($settingData['environment'] == 1) ? $settingData['live_secret_key'] : $settingData['test_secret_key'];
       	$xenditPHPClient = new XenditClient\XenditPHPClient($options);

       	$xendit_id = $this->api->session->userdata('xendit_id');
       	try {
            $apiResponce =   $xenditPHPClient->getInvoice($xendit_id);
        }
        catch (Exception $e) { 
            $apiResponce['error'] = $e->getMessage();    
        }
		if (isset($apiResponce['id']) && !empty($apiResponce['id']) ) {
			$xendit_id 		      = $apiResponce['id'];
			$payment_status 	  = $apiResponce['status'];
		}else{
			$payment_status       = '';
			$apiResponce['error'] = $e->getMessage();
		}

		$uncompletedData = $this->api->Product_model->getByField('uncompleted_payment','id',$gatewayData['id']);
		if($uncompletedData){
			if(isset($xendit_id) && $payment_status != '')
				$this->api->confirmPaymentGateway($gatewayData['id'],$settingData['order_success_status'],$xendit_id,$payment_status);
			else
				$this->api->confirmPaymentGateway($gatewayData['id'],$settingData['order_failed_status'],'',$apiResponce['error']);

			redirect($gatewayData['redirect']);
		}
	}
}