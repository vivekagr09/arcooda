<?php

class paypal {

	public $title = 'Paypal';
	public $icon = 'assets/payment_gateway/paypal.png';
	public $website = 'https://www.paypal.com/';

	function __construct($api){ 
		$this->api = $api; 
	}

	public function getPaymentGatewayView($settingData,$gatewayData){
		$view = APPPATH.'payment_gateway/views/paypal.php';

		require $view;
	}

	public function setPaymentGatewayRequest($settingData,$gatewayData){
		$settingSite = $this->api->Product_model->getSettings('site');
		$config = array(
            'Sandbox'      => $settingData['payment_mode'] == 'sandbox' ? TRUE : FALSE,
            'APIUsername'  => $settingData['api_username'],
            'APIPassword'  => $settingData['api_password'],
            'APISignature' => $settingData['api_signature'],
            'APISubject'   => '',
            'APIVersion'   => '98.0' ,
        );

        require_once(APPPATH . 'payment_gateway/library/paypal/Paypal_pro.php');
        $paypal_pro = new Paypal_pro($config);

        $SECFields = array(
			'returnurl' => $gatewayData['return_url'],
			'cancelurl' => $gatewayData['cancel_url'],
			'brandname' => $settingSite['name'],
			'hdrimg'    => ''
        );

        $PayPalRequestData = array(
			'SECFields' => $SECFields,
			'Payments'  => $gatewayData['payments'],
        );

        $PayPalResult = $paypal_pro->SetExpressCheckout($PayPalRequestData);
        if(isset($PayPalResult['ACK']) && $paypal_pro->APICallSuccessful($PayPalResult['ACK']))
            $result['redirect'] = $PayPalResult['REDIRECTURL'];
        else
            $result['warning'] = isset($PayPalResult['ERRORS'][0]['L_LONGMESSAGE']) ? $PayPalResult['ERRORS'][0]['L_LONGMESSAGE'] : 'Something went wrong';

		return $result;
	}

	public function notify($settingData,$gatewayData){
		$config = array(
            'Sandbox'      => $settingData['payment_mode'] == 'sandbox' ? TRUE : FALSE,
            'APIUsername'  => $settingData['api_username'],
            'APIPassword'  => $settingData['api_password'],
            'APISignature' => $settingData['api_signature'],
            'APISubject'   => '',
            'APIVersion'   => '98.0' ,
        );

        require_once(APPPATH . 'payment_gateway/library/paypal/Paypal_pro.php');
        $paypal_pro = new Paypal_pro($config);
        
		$token    = $this->api->input->get('token');
		$PayPalResult = $paypal_pro->GetExpressCheckoutDetails($token);
        if ($PayPalResult['ACK'] == 'Success'){
            $transaction_amount = c_format($gatewayData['total'],false);

            $payer_id = $this->api->input->get('PayerID');
            $DECPFields = array(
				'token'   => $token,
				'payerid' => $payer_id,
            );

            $Payments = array();
            $Payment = array(
				'amt'            => $transaction_amount,
				'currencycode'   => $gatewayData['currency'],
            );
            array_push($Payments, $Payment);
            $PayPalRequestData = array(
                'DECPFields' => $DECPFields,
                'Payments' => $Payments,
            );
            $PayPalResult = $paypal_pro->DoExpressCheckoutPayment($PayPalRequestData);
            if ($paypal_pro->APICallSuccessful($PayPalResult['ACK'])) {
            	$transaction_id = $PayPalResult['PAYMENTINFO_0_TRANSACTIONID'];
            	$payment_status = $PayPalResult['PAYMENTINFO_0_PAYMENTSTATUS'];

            	switch($payment_status) {
					case 'Canceled_Reversal' : $status_id = $settingData['canceled_reversal_status_id']; break;
					case 'Completed'         : $status_id = $settingData['completed_status_id']; break;
					case 'Denied'            : $status_id = $settingData['denied_status_id']; break;
					case 'Expired'           : $status_id = $settingData['expired_status_id']; break;
					case 'Failed'            : $status_id = $settingData['failed_status_id']; break;
					case 'Pending'           : $status_id = $settingData['pending_status_id']; break;
					case 'Processed'         : $status_id = $settingData['processed_status_id']; break;
					case 'Refunded'          : $status_id = $settingData['refunded_status_id']; break;
					case 'Reversed'          : $status_id = $settingData['reversed_status_id']; break;
					case 'Voided'            : $status_id = $settingData['voided_status_id']; break;
				}
		 		$this->api->confirmPaymentGateway($gatewayData['id'], $status_id, $transaction_id, $payment_status);
		 		
		 		redirect($gatewayData['return_url']);
            } 
        } else {
        	$this->api->confirmPaymentGateway($gatewayData['id'], 5, '','Failed!');
        }

        $this->api->cart->clearCart();

        redirect($gatewayData['cancel_url']);
	}

	public function cancel($settingData,$gatewayData){
		redirect($gatewayData['cancel_url']);
	}
}