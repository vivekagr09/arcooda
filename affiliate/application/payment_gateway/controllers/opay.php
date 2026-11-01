<?php

class opay {

	public $title = 'OPay';
	public $icon = 'assets/payment_gateway/opay.png';
	public $website = 'https://www.opay.tw/';

	function __construct($api){ 
		$this->api = $api; 
	}

	public function getPaymentGatewayView($settingData,$gatewayData){
		include(APPPATH.'core/opay/Opay.Payment.Integration.php');

		try {
	    	$object 							= new OpayAllInOne();
			$object->ServiceURL                = "https://payment-stage.opay.tw/Cashier/AioCheckOut/V5";
			$object->HashKey                   = $settingData['HashKey'];
			$object->HashIV                    = $settingData['HashIV'];
			$object->MerchantID                = $settingData['MerchantID'];
			$object->EncryptType               = OpayEncryptType::ENC_SHA256;
			$object->Send['ReturnURL']         = $gatewayData['return_url'];
			$object->Send['ClientBackURL']     = $gatewayData['back_url'];
			$object->Send['MerchantTradeNo']   = "DX".time();
			$object->Send['MerchantTradeDate'] = date('Y/m/d H:i:s');
			$object->Send['TotalAmount']       = (float) $gatewayData['total'];
			$object->Send['TradeDesc']         = "good to drink";
			$object->Send['ChoosePayment']     = OpayPaymentMethod::ALL;

			$TradeDesc = [];
			foreach($gatewayData['tradeDesc'] as $key => $value){
				$TradeDesc[] = $value['name'];
				array_push($object->Send['Items'],array(
														'Name'     => $value['name'],
														'Price'    => $value['price'],
														'Currency' => $value['currency'],
														'Quantity' => $value['quantity'],
														'URL'      => $value['url'],
		           									));
			}

			$object->Send['TradeDesc'] = implode(",", $TradeDesc);
	        $checkout = $object->CheckOutString('Submit');
	    } catch (Exception $e) {
	    	$error= $e->getMessage();
	    }

		$view = APPPATH.'payment_gateway/views/opay.php';

		require $view;
	}

	public function setPaymentGatewayRequest($settingData,$gatewayData){
		$json['success'] = true;
		$json['redirect'] = $gatewayData['redirect'];
		$message = 'Your payment has been proccessed!';
		
		$this->api->confirmPaymentGateway($gatewayData['id'],7, '', $message);

		return $json;
	}

	public function callback($settingData,$gatewayData){
		include(APPPATH.'core/opay/Opay.Payment.Integration.php');

	    try {
            $object = new OpayAllInOne();
			$object->HashKey     = $settingData['HashKey'];
			$object->HashIV      = $settingData['HashIV'];
			$object->MerchantID  = $settingData['MerchantID'];
			$object->EncryptType = OpayEncryptType::ENC_SHA256;
            $arFeedback = $object->CheckOutFeedback();

            if(is_array($arFeedback) && isset($arFeedback['RtnCode']) && (int)$arFeedback['RtnCode'] == 1)
            	$this->api->confirmPaymentGateway($gatewayData['id'],$settingData['order_status'],$arFeedback['TradeNo'],'Succeeded');
            else
            	$this->api->confirmPaymentGateway($gatewayData['id'],$settingData['failed_status'], '','Failed!');
	    } catch (Exception $e) {
        	$this->api->confirmPaymentGateway($gatewayData['id'],$settingData['failed_status'], '','Failed!');
	    }
	}
}