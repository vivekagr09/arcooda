<?php
class toyyibpay {
	public $title = 'Toyyibpay';
	public $icon = 'assets/payment_gateway/toyyibpay.png';
	public $website = 'https://toyyibpay.com/';
	private $APIURL = 'https://dev.toyyibpay.com/index.php/api/';
	private $PaymentURL = 'https://dev.toyyibpay.com/';

	function __construct($api){ $this->api = $api; }

	public function getPaymentGatewayView($settingData,$gatewayData){
		$view = APPPATH."payment_gateway/views/toyyibpay.php";
		
		require $view;
	}

	public function setPaymentGatewayRequest($settingData,$gatewayData){
		$settingSite = $this->api->Product_model->getSettings('payment_gateway_toyyibpay');
		if(empty($settingSite['toyyibpay_category_id'])){
			$CategoryCode = $this->getcategoryId($settingSite['toyyibpay_userSecretKey']);
		} else {
			$CategoryCode = $settingSite['toyyibpay_category_id'];
		}
		$res= [];
		$request_data = array(
			'userSecretKey'=>$settingSite['toyyibpay_userSecretKey'],
			'categoryCode'=>$CategoryCode,
			'billName'=>$gatewayData['name'],
			'billDescription'=>$gatewayData['description'],
			'billPriceSetting'=>0,
			'billPayorInfo'=>1,
			'billAmount'=>$gatewayData['total']*100,
			'billReturnUrl'=>$gatewayData['return_url'],
			'billCallbackUrl'=>$gatewayData['return_url'],
			'billExternalReferenceNo' =>$gatewayData['id'],
			'billTo'=>$gatewayData['name'],
			'billEmail'=>$gatewayData['email'],
			'billPhone'=>is_numeric(str_replace('-', '',$gatewayData['phone']))==true ? str_replace('-', '',$gatewayData['phone']) :'1234567890',
		);  
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_URL, $this->APIURL.'createBill');  
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $request_data);

		$result = curl_exec($curl);
		$info = curl_getinfo($curl);  
		curl_close($curl);
		$obj = json_decode($result,true);
		$BillCode = $obj[0]['BillCode'];
		$res['redirect'] = $this->PaymentURL.$BillCode;

		return $res;
	}

	public function callback($settingData,$gatewayData){

		$transaction_status =$_POST['status'];
		$transaction_id =$_POST['refno'];
		
		$uncompletedData = $this->api->Product_model->getByField('uncompleted_payment','id',$gatewayData['id']);
		if($uncompletedData){
			if ($_SERVER['REQUEST_METHOD'] === 'POST') {
				if(isset($transaction_status) && $transaction_status == "1"){

						$this->api->confirmPaymentGateway($gatewayData['id'],$settingData['order_success_status_id'],$transaction_id,'Succeeded');
					} else {
						$this->api->confirmPaymentGateway($gatewayData['id'],$settingData['order_failed_status_id'],$_GET['transaction_id'],'Failed!');
					}
				}
				sleep(3);
				redirect($gatewayData['return_url'].'?txno='. $_GET['transaction_id']);
			}
		}

		function getcategoryId($toyyibpay_userSecretKey) {

			$some_data = array(
				'catname' => 'PaymentServices', 
				'catdescription' => 'PaymentServices',
				'userSecretKey' => $toyyibpay_userSecretKey 
			);  

			$curl = curl_init();
			curl_setopt($curl, CURLOPT_POST, 1);
			curl_setopt($curl, CURLOPT_URL, $this->APIURL.'createCategory'); 
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $some_data);

			$result = curl_exec($curl);

			$info = curl_getinfo($curl);
			$httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);  
			curl_close($curl);
			$obj = json_decode($result,true);
			$CategoryCode='';
			if(isset($obj[0]['CategoryCode']) && !empty($obj[0]['CategoryCode'])) {
				$CategoryCode = $obj[0]['CategoryCode'];
			}
			if(isset($obj['CategoryCode']) && !empty($obj['CategoryCode'])) {
				$CategoryCode = $obj['CategoryCode'];
			}
			if($CategoryCode!='') {
				$where=['setting_type'=>'payment_gateway_toyyibpay','setting_key'=>'toyyibpay_category_id'];
				$update = ['setting_value'=>$CategoryCode];
				$this->api->Common_model->update('setting',$where,$update);
				return $CategoryCode;
			}
			return $CategoryCode;
		}
	}