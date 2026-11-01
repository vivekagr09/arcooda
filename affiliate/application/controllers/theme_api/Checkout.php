<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Checkout extends MY_Controller {
	public function get_cart_items() {
		$this->load->model('cart');
		$data['products'] = $this->cart->getProducts();
		$data['is_logged'] = $this->cart->is_logged();
		if($data['products']){
			$data['totals'] = $this->cart->getTotals();
			$data['allow_shipping'] = $this->cart->allow_shipping;
		}
		echo json_encode($data);
	}
}