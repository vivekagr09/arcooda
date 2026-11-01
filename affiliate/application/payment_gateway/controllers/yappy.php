<?php
require APPPATH."payment_gateway/library/yappy/src/BgFirma.php";
use Bg\BgFirma;

class yappy {

	public $title = 'Yappy';
	public $icon = 'assets/payment_gateway/yappy.png';
	public $website = '';
	function __construct($api){ 
		$this->api = $api; 
	}
	public function getPaymentGatewayView($settingData,$gatewayData){
		$view = APPPATH."payment_gateway/views/yappy.php";
		require $view;
	}
	public function setPaymentGatewayRequest($settingData,$gatewayData){
		$domain = base_url();
		$response = BgFirma::checkCredentials($settingData['ID_DEL_COMERCIO'], $settingData['CLAVE_SECRETA'], $domain);
		if ($response && $response['success']) {
			$bg = new BgFirma(
		        $_POST["total"],
		        $settingData['ID_DEL_COMERCIO'],
		        $_POST["currency"],
		        $_POST["subtotal"],
		        $_POST["taxes"],
		        $response['unixTimestamp'],
		        'YAP',
		        'VEN',
		        $_POST["orderId"],
		        $_POST["successUrl"],
		        $_POST["failUrl"],
		        $domain,
		        $settingData['CLAVE_SECRETA'],
		        $settingData['MODO_DE_PRUEBAS'],
		        $response['accessToken'],
		        $_POST['phone']
		    );

	    	$Getresponse = $bg->createHash();
	    	if ($Getresponse['success']) {
	    		$url = $Getresponse['url'];
	    		redirect($url,'refresh');
			}else {
				$bg->showAlertError($Getresponse);
			}
		} else {
		    return false;
		}

	}

	public function customCallbackFunction($settingData,$gatewayData){
		if (isset($_GET['orderId']) && isset($_GET['status']) && isset($_GET['domain']) && isset($_GET['hash'])) {
		    header('Content-Type: application/json');
		    try {
		        $orderId = $_GET['orderId'];
		        $status = $_GET['status'];
		        $hash = $_GET['hash'];
		        $domain = $_GET['domain'];
		        $values = base64_decode($settingData['CLAVE_SECRETA']);
		        $secrete = explode('.', $values);
		        $signature =  hash_hmac('sha256', $orderId . $status . $domain, $secrete[0]);
		        $success = strcmp($hash, $signature) === 0;
		    } catch (\Throwable $th) {
		        $success = false;
		    }
		    if ($success) {
		        $this->api->confirmPaymentGateway($gatewayData["id"],0,$orderId,$success);
		    }
		    echo json_encode(['succes' => $success]);
		}
	}

}
