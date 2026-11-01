<?php

class stripe {

	public $title = 'Stripe';
	public $icon = 'assets/payment_gateway/stripe.png';
	public $website = 'https://stripe.com/';

	function __construct($api){ 
		$this->api = $api; 
	}

	public function getPaymentGatewayView($settingData,$gatewayData){
		$billing_details['billing_details']['name'] = $gatewayData['firstname'] . ' ' . $gatewayData['lastname'];
		$billing_details['billing_details']['email'] = $gatewayData['email'];
		$billing_details['billing_details']['address']['line1'] = $gatewayData['address'];
		$billing_details['billing_details']['address']['city'] = $gatewayData['city'];
		$billing_details['billing_details']['address']['state'] = $gatewayData['state_name'];
		$billing_details['billing_details']['address']['postal_code'] = $gatewayData['zip_code'];
		if(!empty($gatewayData['country_code']))
			$billing_details['billing_details']['address']['country'] = $gatewayData['country_code'];

		$view = APPPATH.'payment_gateway/views/stripe.php';

		require $view;
	}

	public function setPaymentGatewayRequest($settingData,$gatewayData){
		$json = array('error' => 'Server did not get valid request to process');

		try {
			if((int)$gatewayData['id'] <= 0)
				throw new Exception("Your order seems lost in session. We did not charge your payment. Please contact administrator for more information.");

			$this->initStripe($settingData);

			$jsonString = file_get_contents('php://input');
			$jsonObject = json_decode($jsonString);
			
			if(empty($gatewayData['amount']))
				throw new Exception("Your order seems lost before payment. We did not charge your payment. Please contact administrator for more information.");

			if (isset($jsonObject->payment_method_id)) {
				$intent = \Stripe\PaymentIntent::create(array(
					'payment_method'      => $jsonObject->payment_method_id,
					'amount'              => $gatewayData['amount'],
					'currency'            => $gatewayData['currency'],
					'confirmation_method' => 'manual',
					'confirm'             => true,
					'description'         => $gatewayData['description']." #".$gatewayData['id'],
					'metadata'            => $gatewayData['metadata'],
				));
			}

			if (isset($jsonObject->payment_intent_id)) {
				$intent = \Stripe\PaymentIntent::retrieve(
					 $jsonObject->payment_intent_id
				);
				$intent->confirm();
			}

			if(!empty($intent)) {
				if (($intent->status == 'requires_action' || $intent->status == 'requires_source_action') &&
					$intent->next_action->type == 'use_stripe_sdk') {
					$json = array(
						'requires_action' => true,
						'payment_intent_client_secret' => $intent->client_secret
					);
				} else if ($intent->status == 'succeeded') {
					$chargeResult = $this->chargeAndUpdatePayment($intent,$gatewayData['id'],$settingData);

					if($chargeResult)
						$json = array('success' => $gatewayData['redirect']);
					else
						$json = array('error' => 'Payment could not be completed. Please try again.');
				} else {
					$json = array('error' => 'Invalid PaymentIntent Status ('.$intent->status.')');
				}
			}
		} catch (\Stripe\Error\Base $e) {
			$json = array('error' => $e->getMessage());
		} catch (\Exception $e) {
			$json = array('error' => $e->getMessage());
		}

		echo json_encode($json);
		die();
	}

	private function chargeAndUpdatePayment($intent, $id, $settingData){
		if(isset($intent->id)) {
			if($intent->status == "succeeded")
				$this->api->confirmPaymentGateway($id,$settingData['order_success_status'],$intent->id,$intent->status);
			else
				$this->api->confirmPaymentGateway($id,$settingData['order_failed_status'],$intent->id,$intent->status);

			return true;
		} else {			
			return false;
		}
	}

	private function initStripe($settingData){
		require_once(APPPATH . 'payment_gateway/library/stripe/stripe.php');

		if((int)$settingData['environment'] == 1)
			$stripe_secret_key = $settingData['live_secret_key'];
		else
			$stripe_secret_key = $settingData['test_secret_key'];


		if($stripe_secret_key != '' && $stripe_secret_key != null){
			\Stripe\Stripe::setApiKey($stripe_secret_key);
			return true;
		}

		throw new Exception("Unable to load stripe libraries.");
	}
}