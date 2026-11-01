<?php

class paytm {

	public $title = 'Paytm';
	public $icon = 'assets/payment_gateway/paytm.png';
	public $website = 'https://paytm.com/';

	function __construct($api){ 
		$this->api = $api; 
	}

	public function getPaymentGatewayView($settingData,$gatewayData){
		require_once(APPPATH . 'payment_gateway/library/paytm/encdec_paytm.php');

		$cust_id = '';
		if(isset($gatewayData['email']) && trim($gatewayData['email']) != ""){
			$cust_id = $gatewayData['email'];
		} else if($gatewayData['user_id'] > 0){
			$cust_id = $gatewayData['user_id'];
		}

		$parameters = array(
			"MID"              => $settingData['merchant_id'],
			"WEBSITE"          => $settingData['website_name'],
			"INDUSTRY_TYPE_ID" => $settingData['industry_type'],
			"CALLBACK_URL"     => $gatewayData['callback_url'],
			"ORDER_ID"         => $gatewayData['id'],
			"CHANNEL_ID"       => 'WEB',
			"CUST_ID"          => $cust_id,
			"TXN_AMOUNT"       => $gatewayData['total'],
			"MOBILE_NO"        => ($gatewayData['phone']) ? preg_replace('/\D/', '', $gatewayData['phone']) : '',
			"EMAIL"            => $gatewayData['email'],
		);
		$parameters["CHECKSUMHASH"] = getChecksumFromArray($parameters,$settingData['merchant_key']);

		$view = APPPATH.'payment_gateway/views/paytm.php';

		require $view;
	}

	public function setPaymentGatewayRequest($settingData,$gatewayData){
		/*
		* import checksum generation utility
		* You can get this utility from https://developer.paytm.com/docs/checksum/
		*/
		require_once(APPPATH . 'payment_gateway/library/paytm/PaytmChecksum.php');

		$paytmParams = array();

		$paytmParams["body"] = array(
		    "requestType"   => "Payment",
		    "mid"           => "NUtUkt97540691901050",
		    "websiteName"   => "WEBSTAGING",
		    "orderId"       => $gatewayData['id'],
		    "txnAmount"     => $gatewayData['txnAmount'],
		    "userInfo"      => $gatewayData['userInfo'],
		);

		// echo json_encode($paytmParams["body"]);exit;

		/*
		* Generate checksum by parameters we have in body
		* Find your Merchant Key in your Paytm Dashboard at https://dashboard.paytm.com/next/apikeys 
		*/
		$checksum = PaytmChecksum::generateSignature(json_encode($paytmParams["body"], JSON_UNESCAPED_SLASHES), "a6JizN@V5%C%&WK%
");

		$paytmParams["head"] = array(
		    "signature"    => $checksum
		);

		$post_data = json_encode($paytmParams, JSON_UNESCAPED_SLASHES);

		/* for Staging */
		$url = "https://securegw-stage.paytm.in/theia/api/v1/initiateTransaction?mid="."NUtUkt97540691901050"."&orderId=".$gatewayData['id'];

		/* for Production */
		// $url = "https://securegw.paytm.in/theia/api/v1/initiateTransaction?mid=YOUR_MID_HERE&orderId=ORDERID_98765";

		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json")); 
		$response = curl_exec($ch);
		curl_close($ch);

		$response = json_decode($response, true);


		if(isset($response['body']['txnToken'])) {
			echo json_encode([
				"mid" => "NUtUkt97540691901050",
				"orderId" => $gatewayData['id'],
				"txnToken" => $response['body']['txnToken'],
				"processPaymentUrl" => "https://securegw-stage.paytm.in/theia/api/v1/showPaymentPage?mid="."NUtUkt97540691901050"."&orderId=".$gatewayData['id']
			]);
		} else {
			echo json_encode($response);
		}
		exit;
	}

	public function callback($settingData,$gatewayData){
		require_once(APPPATH.'payment_gateway/library/paytm/encdec_paytm.php');

		$isValidChecksum = false;
		$txnstatus       = false;
		$authStatus      = false;

		if(isset($_POST['CHECKSUMHASH'])) {
			$checksum = htmlspecialchars_decode($_POST['CHECKSUMHASH']);
			$return = verifychecksum_e($_POST, $settingData['merchant_key'], $checksum);
			if($return == "TRUE") 
				$isValidChecksum = true;
		}

		$uncompleted_id = isset($_POST['ORDERID']) && !empty($_POST['ORDERID'])? $_POST['ORDERID'] : 0;
		if(isset($_POST['STATUS']) && $_POST['STATUS'] == "TXN_SUCCESS")
			$txnstatus = true;

		$uncompletedData = $this->api->Product_model->getByField('uncompleted_payment','id',$uncompleted_id);
		if($uncompletedData){
			if ($txnstatus && $isValidChecksum){
				$reqParams = array(
					"MID"     => $settingData['merchant_id'],
					"ORDERID" => $uncompleted_id
				);

				$reqParams['CHECKSUMHASH'] = getChecksumFromArray($reqParams, $settingData['merchant_key']);		
				$resParams = callNewAPI($settingData['transaction_status_url'], $reqParams);
				if($resParams['STATUS'] == 'TXN_SUCCESS' && $resParams['TXNAMOUNT'] == $_POST['TXNAMOUNT']) {
				  	$this->api->confirmPaymentGateway($uncompleted_id,$settingData['order_success_status_id'],$_POST['TXNID'],'Success');
					redirect($gatewayData['redirect'].$uncompleted_id);
				} else {
				   	$this->api->confirmPaymentGateway($uncompleted_id,$settingData['order_failed_status_id'],$_POST['TXNID'],'Failed!');
					redirect($gatewayData['redirect'].$uncompleted_id);
				}
			} else {
				$this->api->confirmPaymentGateway($uncompleted_id,$settingData['order_failed_status_id'],'','Failed!');
				redirect($gatewayData['redirect'].$uncompleted_id);
			}
		}

		redirect($gatewayData['cancel']);
	}
}