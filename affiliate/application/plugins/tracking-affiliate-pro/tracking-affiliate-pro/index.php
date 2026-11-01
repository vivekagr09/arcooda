<?php
/*
Plugin Name: Tracking affiliate Pro
Plugin URI: 
Description: Tracking affiliate Pro.
Author: All4Bussiness
Version: 2.0.0
Author URI: https://affiliatepro.org/
*/
function affi_action_links( $links ) {
	$links = array_merge( array('<a href="' . esc_url( admin_url( '/options-general.php?page=affi_settings_plugin' ) ) . '">' . __( 'Settings', 'textdomain' ) . '</a>'), $links );
	return $links;
}
add_action( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'affi_action_links' );
function affi_settings_page() {
    add_options_page( 'Affi settings', 'Affi settings', 'manage_options', 'affi_settings_plugin', 'affi_settings_page_fun' );
}

add_action( 'admin_menu', 'affi_settings_page' );
function affi_settings_page_fun(){
	include "option.php";
}

add_action('init','affi_init_fun');
function affi_init_fun(){
	$affi_woo_option = (int)get_option('affi_woo_option');
	$affi_plugin_option = (int)get_option('affi_plugin_option');
	$affi_cartflows_option = (int)get_option('affi_cartflows_option');
	if($affi_plugin_option){
		if($affi_woo_option || $affi_cartflows_option){
			add_action('wp_head','affi_wp_head_fun',10);

			if($affi_woo_option){
				add_action('woocommerce_thankyou','affi_woocommerce_thankyou_fun');
			}
			add_action( 'woocommerce_single_product_summary', 'fun_affiliate', 4 );
		}
	}
}
function affi_wp_head_fun(){ ?>
	<script type="text/javascript" src="__baseurl__integration/script"></script>
<?php }
if(!function_exists('call_affiliate_api')){
	function call_affiliate_api($endpoint, $data){
		global $wp;
	    $data['current_page_url'] = base64_encode(home_url( $wp->request ));
		$context_options = stream_context_create(array(
			'http'=>array(
				'method'=>"GET",
				'header'=> "User-Agent: ". $_SERVER['HTTP_USER_AGENT'] ."\r\n"."Referer: ". $data['current_page_url'] ."\r\n"
			)
		));
		file_get_contents("__baseurl__{$endpoint}?".http_build_query($data), false, $context_options);
	}
}
function affi_woocommerce_thankyou_fun($order_ID){ 
	$order = new WC_Order($order_ID);

	$ipaddress = "";
	if (getenv("HTTP_CLIENT_IP")) $ipaddress = getenv("HTTP_CLIENT_IP");
	else if(getenv("HTTP_X_FORWARDED_FOR")) $ipaddress = getenv("HTTP_X_FORWARDED_FOR");
	else if(getenv("HTTP_X_FORWARDED")) $ipaddress = getenv("HTTP_X_FORWARDED");
	else if(getenv("HTTP_FORWARDED_FOR")) $ipaddress = getenv("HTTP_FORWARDED_FOR");
	else if(getenv("HTTP_FORWARDED")) $ipaddress = getenv("HTTP_FORWARDED");
	else if(getenv("REMOTE_ADDR")) $ipaddress = getenv("REMOTE_ADDR");
	else $ipaddress = "UNKNOWN";
	
	$affiliateData = array(
		"order_id"       => $order->get_id(),
		"order_currency" => $order->get_order_currency(),
		"order_total"    => ($order->order_total - $order->order_shipping - $order->total_tax),
		"product_ids"    => array(),
		"af_id"          => isset($_COOKIE["af_id"]) ? $_COOKIE["af_id"] : null,
		"ip"             => $ipaddress,
		"base_url"       => base64_encode(get_site_url()),
		"script_name"    => "woocommerce",
	);
	foreach ($order->get_items() as $item) {
		$_product = $order->get_product_from_item($item);
		$affiliateData["product_ids"][] = $_product->id;
	}	
	
	call_affiliate_api('integration/addOrder', $affiliateData);
}

function fun_affiliate(){
	global $product, $post;
	
	$affi_woo_option = (int)get_option('affi_woo_option');
	$affi_cartflows_option = (int)get_option('affi_cartflows_option');

	$allow_page = array();
	if($affi_woo_option) $allow_page[] = "product";
	if($affi_cartflows_option) $allow_page[] = "cartflows_step";

	if($affi_cartflows_option && 
	   isset($_GET['wcf-key'],$_GET['wcf-order']) && $_GET['wcf-order'] && 
	   (function_exists('_is_wcf_thankyou_type') && _is_wcf_thankyou_type())
	  ){
		affi_woocommerce_thankyou_fun($_GET['wcf-order']);
	}else if($post && in_array($post->post_type, $allow_page) && is_single()){
		if (is_singular('product') && get_queried_object_id() == $post->ID) {
			$ipaddress = "";
			if (getenv("HTTP_CLIENT_IP")) $ipaddress = getenv("HTTP_CLIENT_IP");
			else if(getenv("HTTP_X_FORWARDED_FOR")) $ipaddress = getenv("HTTP_X_FORWARDED_FOR");
			else if(getenv("HTTP_X_FORWARDED")) $ipaddress = getenv("HTTP_X_FORWARDED");
			else if(getenv("HTTP_FORWARDED_FOR")) $ipaddress = getenv("HTTP_FORWARDED_FOR");
			else if(getenv("HTTP_FORWARDED")) $ipaddress = getenv("HTTP_FORWARDED");
			else if(getenv("REMOTE_ADDR")) $ipaddress = getenv("REMOTE_ADDR");
			else $ipaddress = "UNKNOWN";

			$affiliateData = array(
				"product_id"  => $product->get_id(),
				"af_id"       => isset($_COOKIE["af_id"]) ? $_COOKIE["af_id"] : null,
				"ip"          => $ipaddress,
				"base_url"    => base64_encode(get_site_url()),
				"script_name" => "woocommerce",
			);

			call_affiliate_api('integration/addClick', $affiliateData);
		}
	} 
}