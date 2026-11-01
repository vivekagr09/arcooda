<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cart extends CI_Model {
	public $allow_shipping = false;
	public $allow_comment = false;
	public $allow_upload_file = false;

	public function add($product_id,$quantity,$variation = null,$refer_id = 0, $product = false){
		$this->session->unset_userdata('last_order_id');

		$refer_id = (int)$refer_id;
		$session_id = $this->session->session_id;
		$cart_product = $this->db->query("SELECT * FROM cart WHERE product_id  = {$product_id} AND product_variation = '{$variation}' AND session_id = ". $this->db->escape($session_id) ." ")->num_rows();

		if($cart_product == 0){
			$date = date("Y-m-d H:i:s");
			$this->db->query("INSERT INTO cart SET date_added='{$date}', product_id  = {$product_id}, product_variation = '{$variation}', quantity ={$quantity}, refer_id={$refer_id}, session_id = ". $this->db->escape($session_id) ." ");
		}else{
			if($product && (in_array($product['product_type'],['downloadable','video','videolink']))){
				$this->db->query("UPDATE cart SET quantity = 1, refer_id={$refer_id} WHERE session_id = ". $this->db->escape($session_id) ." AND  product_variation = '{$variation}'");
			} else {
				$this->db->query("UPDATE cart SET quantity = quantity + {$quantity} , refer_id={$refer_id} WHERE session_id = ". $this->db->escape($session_id) ." AND  product_variation = '{$variation}'");
			}
		}
		return $this->reloadCart();
		return true;
	}

	public function getcookieAffiliate(){
		$id = $_COOKIE["affiliate_id"];

		if( (int)$id > 0 ){
			return $id;
		}

		return 1;
	}

	public function has_purchase($product_id,$arr=null){
		$user = (int)$this->getUserId();
		if($arr) {
			return $this->db->query("SELECT count(op.id) as total,`o`.`id` as order_id FROM `order_products` op LEFT JOIN `order` o ON o.id = op.order_id WHERE o.status > 0 AND o.user_id = {$user} AND op.product_id = ". (int)$product_id)->row();
		} else {

			return (int)$this->db->query("SELECT count(op.id) as total FROM `order_products` op LEFT JOIN `order` o ON o.id = op.order_id WHERE o.status > 0 AND o.user_id = {$user} AND op.product_id = ". (int)$product_id)->row()->total;
		}
	}



	public function setcookieAffiliate($affiliate_id){
		$this->load->model('Product_model');
		$store_setting = $this->Product_model->getSettings('store');
		$time = ($store_setting['affiliate_cookie'] * 24 * 60 * 60);
		setcookie("affiliate_id", $affiliate_id, time()+$time, "/" );
		$this->setReferId($affiliate_id);
	}

	public function addCoupon($product_id, $coupon,$type= 'product'){
		if($type == 'product'){
			$allCoupon = $this->session->userdata('coupon');
			$allCoupon[$product_id] = $coupon;
			$this->session->set_userdata('coupon',$allCoupon);
		} else {
			$allCoupon = $this->session->userdata('form_coupon');
			$allCoupon = $coupon;
			$this->session->set_userdata('form_coupon',$allCoupon);	
		}
		$this->reloadCart();
	}
	public function reloadCart()
	{
		$products = $this->getProducts();
		$allCoupon = $this->session->userdata('coupon');
		$allFormCoupon = $this->session->userdata('form_coupon');
		$form_id = $this->session->userdata('form_id');
		
		foreach ($products as $key => $product) {
			$variation_total_price = $this->getSelectedVariationPrice($product);
			$quantity = $product['quantity'];
			$price = ($product['product_price'] + $variation_total_price) * $quantity;

			$myCopon = isset($allCoupon[$product['product_id']]) ? $allCoupon[$product['product_id']] : false;
			$coupon_name = '';
			$coupon_code = '';
			$coupon_discount = 0;
			if($myCopon){
				$discount = $myCopon['discount'];
				if($myCopon['type'] == 'P'){
					$discount = (($price * $myCopon['discount']) / 100);
				}
				$coupon_name = $myCopon['name'];
				$coupon_code = $myCopon['code'];
				$coupon_discount = $discount;
			}else{
				$myCopon = isset($allFormCoupon) ? $allFormCoupon : false;
				$coupon_name = '';
				$coupon_code = '';
				$coupon_discount = 0;
				if($myCopon){
					$discount = $myCopon['discount'];
					if($myCopon['type'] == 'P'){
						$discount = (($price * $myCopon['discount']) / 100);
					}
					$coupon_name = $myCopon['name'];
					$coupon_code = $myCopon['code'];
					$coupon_discount = $discount;
				}
			}
			$final_total = ($price - $coupon_discount);
			$final_total = max($final_total,0);
			
			$this->db->query("UPDATE cart SET 
				total = ". $this->db->escape($final_total) .", 
				coupon_code = ". $this->db->escape($coupon_code) .", 
				coupon_name = ". $this->db->escape($coupon_name) .", 
				coupon_discount = ". $this->db->escape($coupon_discount) ." 
				WHERE cart_id = ". $this->db->escape($product['cart_id']));
		}

		
		$logged_user = $this->is_logged();
		if($logged_user){
			$session_id = $this->session->session_id;
			$this->db->query("UPDATE cart SET user_id = '". $logged_user['id'] ."' WHERE session_id = ". $this->db->escape($session_id) );
		}
	}
	public function clearCart(){
		$session_id = $this->session->session_id;
		$cart_products = $this->db->query("DELETE FROM cart WHERE session_id = ". $this->db->escape($session_id) ." ");
		$this->session->unset_userdata('coupon');
		$this->session->unset_userdata('form_coupon');
		$this->session->unset_userdata('form_coupon_discount');
		$this->session->unset_userdata('last_order_id');
	}
	public function setReferId($refer_id){
		$this->session->set_userdata('refer_id', $refer_id);	
	}
	public function getReferId(){
		return (int)$this->session->userdata('refer_id');	
	}
	public function getUserId(){
		return (int)$this->session->userdata('client')['id'];	
	}
	public function getStoreUrl($url = ''){
		$refer_id = base64_encode( $this->session->userdata('refer_id') );
		return base_url('store/'. $url);
	}
	public function getProducts($previewId=null){
		$store_setting = $this->Product_model->getSettings('store');

		$isClassified = $store_setting['store_mode'] == 'sales' ? " product.product_status = 1 AND product.is_campaign_product = 1 AND product.on_store = 1 AND " : " product.product_status = 1 AND product.is_campaign_product = 0 AND product.on_store = 1 AND ";

		$session_id = $this->session->session_id;
		
		$user = $this->is_logged();
		if($previewId) {

			$previewProduct= ['session_id'=>time(),'product_variation'=>0,'quantity'=>1,'total'=>150];
			$this->db->where(['user_id'=>0,'product_id'=>$previewId]);
			$q = $this->db->get('cart');
			$this->db->reset_query();
			if ( $q->num_rows() > 0 )  {
				$this->db->where(['user_id'=>0,'product_id'=>$previewId])->update('cart', $previewProduct);
			} else {
				$this->db->set(['user_id'=>0,'product_id'=>$previewId])->insert('cart', $previewProduct);
			}
			$session_id = $previewProduct['session_id'];
		}
		$lmsQuery = "";
		if($user) {
			$sql= "SELECT GROUP_CONCAT(op.product_id) as ids FROM `order` o LEFT JOIN users u ON u.id = o.user_id LEFT JOIN order_products op ON op.order_id = o.id LEFT JOIN product ON op.product_id=product.product_id WHERE product.product_type IN('video','videolink') AND `o`.`user_id`=".$user['id'];
			$query = $this->db->query($sql);
			if($query->num_rows()) {
				$result = $query->row_array();
				if(!empty($result['ids'])) {
					$lmsQuery = " AND  product.product_id NOT IN(".$result['ids'].") ";
				}
			}
		}
		$cart_products = $this->db->query("
			SELECT product.*,cart.cart_id,cart.quantity,cart.refer_id,cart.coupon_code,cart.coupon_name,cart.coupon_discount,cart.total,cart.product_variation as cart_variation, product_affiliate.user_id as vendor_id
			FROM cart 
			LEFT JOIN product ON product.product_id = cart.product_id
			LEFT JOIN product_affiliate ON product_affiliate.product_id = cart.product_id
			WHERE  
			$isClassified session_id = ". $this->db->escape($session_id) ."  $lmsQuery ")->result_array();
		$products  = array();
		$this->allow_shipping = false;
		$this->allow_comment = false;
		$this->allow_upload_file = false;
		
		foreach ($cart_products as $key => $product) {
			$product_featured_image = base_url('assets/images/product/upload/thumb/'. $product['product_featured_image']);

			if($product['allow_shipping']){ $this->allow_shipping = true; }
			if($product['allow_comment']){ $this->allow_comment = true; }
			if($product['allow_upload_file']){ $this->allow_upload_file = true; }

			$sale_details = $this->db->get_where('product_meta', array(
				'related_product_id' => $product->product_id
			))->result_array();

			if(! empty($sale_details)) {
				$metaData = [];	

				foreach ($sale_details as $key => $value) {
					if(!empty($value['meta_value'])) {
						$metaData[$value['meta_key']] = $value['meta_value'];
					}
				}

				if(isset($metaData['product_sale_start']) && isset($metaData['product_sale_end']) && isset($metaData['product_sale_price'])) {

					$start_time = strtotime(trim($metaData['product_sale_start']));
					$end_time = strtotime(trim($metaData['product_sale_end']));
					$time = time();

					if($start_time < $time && $end_time > $time) {
						$product->product_price = trim($metaData['product_sale_price']);
					}
				}
			}

			$temp_product = array(
				'product_id'                   => $product['product_id'],
				'product_name'                 => $product['product_name'],
				'product_description'    	   => $product['product_description'],
				'product_short_description'    => $product['product_short_description'],
				'product_price'                => $product['product_price'],
				'product_msrp'				   => $product['product_msrp'],
				'product_sku'                  => $product['product_sku'],
				'product_slug'                 => $product['product_slug'],
				'product_share_count'          => $product['product_share_count'],
				'product_click_count'          => $product['product_click_count'],
				'product_view_count'           => $product['product_view_count'],
				'product_sales_count'          => $product['product_sales_count'],
				'product_featured_image'       => $product_featured_image,
				'product_banner'               => $product['product_banner'],
				'product_video'                => $product['product_video'],
				'product_type'                 => $product['product_type'],
				'product_commision_type'       => $product['product_commision_type'],
				'product_commision_value'      => $product['product_commision_value'],
				'product_status'               => $product['product_status'],
				'product_ipaddress'            => $product['product_ipaddress'],
				'product_created_date'         => $product['product_created_date'],
				'product_updated_date'         => $product['product_updated_date'],
				'product_created_by'           => $product['product_created_by'],
				'product_updated_by'           => $product['product_updated_by'],
				'product_click_commision_type' => $product['product_click_commision_type'],
				'product_click_commision_ppc'  => $product['product_click_commision_ppc'],
				'product_click_commision_per'  => $product['product_click_commision_per'],
				'product_total_commission'     => $product['product_total_commission'],
				'product_variations'		   => $product['product_variations'],
				'view'                         => $product['view'],
				'on_store'                     => $product['on_store'],
				'cart_id'                      => $product['cart_id'],
				'quantity'                     => $product['quantity'],
				'refer_id'                     => $product['refer_id'],
				'coupon_code'                  => $product['coupon_code'],
				'coupon_name'                  => $product['coupon_name'],
				'coupon_discount'              => $product['coupon_discount'],
				'allow_shipping'               => $product['allow_shipping'],
				'allow_comment'                => $product['allow_comment'],
				'allow_upload_file'            => $product['allow_upload_file'],
				'total'                        => $product['total'],
				'variation'					   => $product['cart_variation'],
				'vendor_id'                        => (int)$product['vendor_id'],
				'link'                         => base_url("store/". base64_encode( (int)$product['refer_id'] )  ."/product/". $product['product_slug']),
			);
			
			$temp_product['variation_price'] = $this->getSelectedVariationPrice($temp_product);

			$products[] = $temp_product;
		}

		$downloadable = 0;
		foreach ($products as $key => $value) {
			if($value['product_type'] == 'downloadable'){
				$downloadable++;
			}
		}

		$all_is_download_product = 0;
		if(count($products) == $downloadable){
			$this->allow_shipping = false;
		}

		return $products;
	}

public function getSelectedVariationPrice($product){
    $selected_var = json_decode($product['variation']);
    $product_var = json_decode($product['product_variations']);
    $price = 0;
    foreach($selected_var as $var_key => $var_value) {
        $searchVal = ($var_key == 'colors') ? explode('-', $var_value)[1] : $var_value;
        foreach($product_var->{$var_key} as $p_var) {
            if($p_var->name == $searchVal) {
                $price += (float) $p_var->price;  // Type cast to float before addition
                break;
            }
        }
    }
    return $price;
}


	public function syncCart(){
		$this->load->model("Coupon_model");
		$finalCoupon = $allCoupon = $this->session->userdata('coupon');
		$session_id = $this->session->session_id;
		foreach ($allCoupon as $product_id => $coupon) {
			$error = false;
			if($coupon['allow_for'] == "S"){
				$product_ids = explode(",", $coupon['products']);
				if (!in_array($product_id, $product_ids)) {
					$error = true;
				}
			}
			$logged_user = $this->is_logged();
			$total_use = $this->Coupon_model->getUses($logged_user['id'],$coupon['code']);
			if($total_use >= $coupon['uses_total']){
				$error = true;
			}
			
			if ($error) {
				$product = $this->db->query("SELECT product_price FROM product WHERE product_id = {$product_id} ")->row();
				if($product){
					$total = $product->product_price;
					$this->db->query("UPDATE cart SET 
						total = '{$total}',
						coupon_code='',
						coupon_name='',
						coupon_discount='' 
						WHERE product_id = '{$product_id}' AND session_id = ". $this->db->escape($session_id) );
				}
				unset($finalCoupon[$product_id]);
			}
		}
		$this->session->set_userdata('coupon',$finalCoupon);
		$this->reloadCart();
	}
	public function subTotal(){
		$products = $this->getProducts();
		$total = 0;
		foreach ($products as $key => $value) {
			$total += ($value['total']);
		}
		return $total;
	}

	public function getTotals($countryId = null){
		$_total = [];


		$products = $this->getProducts();
		$sub_total = 0;
		$allow_shipping = false;
		foreach ($products as $key => $value) {
			if($value['product_type'] != 'downloadable') $allow_shipping = true;

			$sub_total += ($value['total']);
		}		

		$_total['sub_total'] = array('title' => __('store.subtotal'), 'amount' => $sub_total);

		if(!empty($this->cart->allow_shipping)){
			if($countryId != null) {
				$shipping_country= (int)$countryId;
			} else {
				$shipping_country= (int)$this->session->userdata('shipping_country');
			}
			$rate = $this->Product_model->getShippingRate($shipping_country);
			if($rate > 0){
				$_total['shipping_cost'] = array('title' => __('store.shipping_cost'), 'amount' => $rate);
			}
		}


		$form_coupon = $this->session->userdata('form_coupon');
		if($form_coupon){
			$discount = $form_coupon['discount'];
			if($form_coupon['type'] == 'P'){
				$discount = (($total * $form_coupon['discount']) / 100);
			}
			$coupon_discount = $discount;
			$this->session->set_userdata('form_coupon_discount', $coupon_discount);

			if($coupon_discount){
				$_total['discount_total'] = array('title' => __('store.coupon_discount'), 'amount' => $coupon_discount);
			}
		}

		$total = array_column($_total, 'amount');
		$finalTotal = array_sum($total);

		$tax = $this->Product_model->getTaxRate($shipping_country);

		if($tax > 0) {
			$taxAmt = ($finalTotal/100) * $tax;
			$_total['tax'] = array('title' => __('store.tax'), 'amount' => $taxAmt);

			$finalTotal = $finalTotal + $taxAmt;
		}

		$_total['total'] = array('title' => __('store.grand_total'), 'amount' => $finalTotal);

		return $_total;
	}

	public function finalTotal(){
		$total = $this->subTotal();
		$form_coupon = $this->session->userdata('form_coupon');
		if($form_coupon){
			$discount = $form_coupon['discount'];
			if($form_coupon['type'] == 'P'){
				$discount = (($total * $form_coupon['discount']) / 100);
			}
			$coupon_discount = $discount;
			$this->session->set_userdata('form_coupon_discount', $coupon_discount);
			$total = $total - $coupon_discount;
		}

		$total = max($total,0);
		return $total;
	}
	public function update($cart_id,$quantity){
		$this->db->query("UPDATE cart SET quantity = {$quantity} WHERE cart_id = ". $cart_id );
		$this->reloadCart();
	}
	public function remove($cart_id){
		$this->db->query("DELETE FROM  cart WHERE cart_id = ". (int)$cart_id );
	}
	public function is_logged(){
		if($this->session->userdata('client') != false) 
			$user =  $this->session->userdata('client');
		else
			$user =  $this->session->userdata('user');
		if(isset($user['id'])){
			return $user;
		}
		return false;
	}
	public function delete_directory($dirname) {
		if (is_dir($dirname)){
			$dir_handle = opendir($dirname);
		}
		if (!$dir_handle){
			return false;
		}
		while($file = readdir($dir_handle)) {
			if ($file != "." && $file != "..") {
				if (!is_dir($dirname."/".$file))
					unlink($dirname."/".$file);
				else
					$this->delete_directory($dirname.'/'.$file);
			}
		}
		closedir($dir_handle);
		rmdir($dirname);
		return true;
	}

	public function updateCartRefereId($user_id)
	{
		$session_id = $this->session->session_id;
		if(isset($session_id) && $session_id!="")
		{
			$refer_id=$user_id;
			$cartProducts = $this->db->query("SELECT * FROM cart WHERE  session_id =". $this->db->escape($session_id)." and refer_id>0")->result_array();
			 
			if(count($cartProducts)>0)
			{
				foreach($cartProducts as $cp)
				{
					$sql="UPDATE cart SET refer_id={$refer_id} WHERE  cart_id=".$cp['cart_id'];
					$this->db->query($sql); 
				}
			}
		}
		 
	}
}