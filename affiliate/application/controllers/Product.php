<?php
if (!defined('BASEPATH')) exit ('No direct script access allowed');

class Product extends CI_Controller {
	function __construct() {
		parent::__construct();
		$this->load->model('Product_model');
		$this->load->helper('share');
		___construct(1);
		$this->load->library('user_agent');
	}
	function index($product_slug = null, $user_id = null){
		redirect(base_url("store/product/". $product_slug));die;
		$data = array();
		$setting = array();
		$data['session'] = $this->session->userdata('client') ? $this->session->userdata('client') : '';
		if($product_slug){
			$data['product'] = $this->Product_model->getProductBySlug($product_slug);
			if(!$data['product']) die("Product Not Found..!");
			$data['product_slug'] = $product_slug;
			$data['user_id'] = $user_id;
			$this->db->set('view', 'view+1', FALSE);
			$this->db->where('product_id', $data['product']['product_id']);
			$this->db->update('product');
			if(!empty($user_id)){
				$data['user'] = $this->Product_model->getUserDetails($user_id);
			} else {
				$data['user'] = '';
			}
			$data['setting'] 	= $this->Product_model->getSettings('paymentsetting');
			$data['ratings'] = $this->Product_model->getReview($data['product']['product_id']);
			$client_id = 0;
			if($this->session->userdata('client') != false) $client_id = $this->session->userdata('client')['id'];
			if (
				$this->session->userdata('administrator') == false && 
				$this->session->userdata('user') == false && 
				$user_id && 
				$client_id != $user_id
			) {
				
				$match = $this->Product_model->getProductAction(
					$data['product']['product_id'],
					$user_id
				);
				$this->Product_model->referClick($data['product']['product_id'],$user_id);
				if ($match == 0){
					$this->Product_model->setClicks($data['product']['product_id'],$user_id);
					$this->Product_model->giveClickCommition($data['product'], $user_id);
					$details = array(
						'clicks_views_refuser_id'       =>  $user_id,
						'clicks_views_action_id'        =>  $data['product']['product_id'],
						'clicks_views_status'           =>  1,
						'clicks_views_type'             =>  'productclick',
						'clicks_views_click'            =>  1,
						'clicks_views_view'             =>  0,
						'clicks_views_referrer'         =>  $this->agent->referrer(),
						'clicks_views_user_agent'       =>  $this->agent->agent_string(),
						'clicks_views_os'               =>  $this->agent->platform(),
						'clicks_views_browser'          =>  $this->agent->browser(),
						'clicks_views_isp'              =>  gethostbyaddr($_SERVER['REMOTE_ADDR']),
						'clicks_views_ipaddress'        =>  $_SERVER['REMOTE_ADDR'],
						'clicks_views_created_by'       =>  $user_id,
						'clicks_views_created'          =>  date('Y-m-d H:i:s'),
						'clicks_views_click_commission' =>  $click,
						'clicks_views_data_commission'  =>  json_encode($setting),
					);
					$this->Product_model->create_data('clicks_views', $details);
					$userData['product_commission'] = $getUserData['product_commission'] + $click;
					$userData['product_total_click'] = $getUserData['product_total_click'] + 1;
					$this->Product_model->update_data('users', $userData,array('id' => $getUserData['id']));
					$adminData['product_commission'] = $getAdminUserData['product_commission'] + $click;
					$adminData['product_total_click'] = $getAdminUserData['product_total_click'] + 1;
					$this->Product_model->update_data('users', $adminData,array('id' => $getAdminUserData['id']));
					$notificationData = array(
						'notification_url'          => '/dashboard',
						'notification_type'         =>  'commission',
						'notification_title'        =>  'New Commission added for click your reffered product in to your wallet '.$click,
						'notification_view_user_id' =>  $getUserData['id'],
						'notification_viewfor'      =>  'user',
						'notification_actionID'     =>  '',
						'notification_description'  =>  'New Commission added to your wallet '.$sales.' on '.date('Y-m-d H:i:s'),
						'notification_is_read'      =>  '0',
						'notification_created_date' =>  date('Y-m-d H:i:s'),
						'notification_ipaddress'    =>  $_SERVER['REMOTE_ADDR']
					);
					$this->insertproductlogs($orderlogData);
				} else {
					$this->Product_model->getProductActionIncrese($data['product']['product_id'], $user_id);
				}
			}
			$this->load->view('product/index', $data);
		}
	}
	function clicks($product_slug = null, $user_id = null) {
		$data = array();
		$setting = array();
		$setting = $this->Product_model->getSettings('productsetting');
		$getUserData = $this->Product_model->getUserDetails($user_id);
		$getAdminUserData = $this->Product_model->getUserDetails(9);
		$click = 0;
		if($product_slug){
			$data['product'] = $this->Product_model->getProductBySlug($product_slug);
			if($setting && $setting['product_commission_type'] && $setting['product_noofpercommission'] && $setting['product_ppc']){
				if($setting['product_commission_type'] == 'percentage'){
					$click = ($setting['product_ppc']) * ($setting['product_noofpercommission'])  / (100);
				} else {
					$click = ($setting['product_ppc']) / ($setting['product_noofpercommission']);
				}
			}
			$data['product_slug'] = $product_slug;
			$data['user_id'] = $user_id;
			if(!empty($user_id)){
				$data['user'] = $this->Product_model->getUserDetails($user_id);
			} else {
				$data['user'] = '';
			}
			if ($this->session->userdata('user') == false && $this->session->userdata('administrator') == false) {
				$match = $this->Product_model->getProductAction($data['product']['product_id'], $_SERVER['REMOTE_ADDR']);
				if ($match == 0){
					$this->Product_model->setClicks($data['product']['product_id'],$user_id);
					$details = array(
						'clicks_views_action_id'        =>  $data['product']['product_id'],
						'clicks_views_browser'          =>  $this->agent->browser(),
						'clicks_views_click'            =>  1,
						'clicks_views_click_commission' =>  $click,
						'clicks_views_created'          =>  date('Y-m-d H:i:s'),
						'clicks_views_created_by'       =>  $user_id,
						'clicks_views_data_commission'  =>  json_encode($setting),
						'clicks_views_ipaddress'        =>  $_SERVER['REMOTE_ADDR'],
						'clicks_views_isp'              =>  gethostbyaddr($_SERVER['REMOTE_ADDR']),
						'clicks_views_os'               =>  $this->agent->platform(),
						'clicks_views_referrer'         =>  $this->agent->referrer(),
						'clicks_views_refuser_id'       =>  $user_id,
						'clicks_views_status'           =>  1,
						'clicks_views_type'             =>  'productclick',
						'clicks_views_user_agent'       =>  $this->agent->agent_string(),
						'clicks_views_view'             =>  0,
					);
					$this->Product_model->create_data('clicks_views', $details);
					$userData['product_commission'] = $getUserData['product_commission'] + $click;
					$userData['product_total_click'] = $getUserData['product_total_click'] + 1;
					$this->Product_model->update_data('users', $userData,array('id' => $getUserData['id']));
					$adminData['product_commission'] = $getAdminUserData['product_commission'] + $click;
					$adminData['product_total_click'] = $getAdminUserData['product_total_click'] + 1;
					$this->Product_model->update_data('users', $adminData,array('id' => $getAdminUserData['id']));
					$notificationData = array(
						'notification_url'	=> '/dashboard',
						'notification_type'	=>  'commission',
						'notification_title'	=>  'New Commission added for click your reffered product in to your wallet '.$click,
						'notification_view_user_id'	=>  $getUserData['id'],
						'notification_viewfor'	=>  'user',
						'notification_actionID'	=>  '',
						'notification_description'		=>  'New Commission added to your wallet '.$sales.' on '.date('Y-m-d H:i:s'),
						'notification_is_read'		=>  '0',
						'notification_created_date'		=>  date('Y-m-d H:i:s'),
						'notification_ipaddress'		=>  $_SERVER['REMOTE_ADDR']
					);
					$this->insertproductlogs($orderlogData);
				}
			}
			redirect('product/'.$product_slug.'/'.$user_id);
		}
	}
	function views($product_slug = null, $user_id = null) {
		$data = array();
		$setting = array();
		$setting = $this->Product_model->getSettings('productsetting');
		$getUserData = $this->Product_model->getUserDetails($user_id);
		$getAdminUserData = $this->Product_model->getUserDetails(9);
		$views = 0;
		if($product_slug){
			$data['product'] = $this->Product_model->getProductBySlug($product_slug);
			$data['product_slug'] = $product_slug;
			$data['user_id'] = $user_id;
			if(!empty($user_id)){
				$data['user'] = $this->Product_model->getUserDetails($user_id);
			} else {
				$data['user'] = '';
			}
			$details = array(
				'clicks_views_refuser_id'	=>  $user_id,
				'clicks_views_action_id'	=>  $data['product']['product_id'],
				'clicks_views_status'		=>  1,
				'clicks_views_type'		=>  'productview',
				'clicks_views_click'		=>  0,
				'clicks_views_view'		=>  1,
				'clicks_views_referrer'		=>  $this->agent->referrer(),
				'clicks_views_user_agent'		=>  $this->agent->agent_string(),
				'clicks_views_os'		=>  $this->agent->platform(),
				'clicks_views_browser'		=>  $this->agent->browser(),
				'clicks_views_isp'		=>  gethostbyaddr($_SERVER['REMOTE_ADDR']),
				'clicks_views_ipaddress'		=>  $_SERVER['REMOTE_ADDR'],
				'clicks_views_created_by'		=>  $user_id,
				'clicks_views_created'		=>  date('Y-m-d H:i:s'),
				'clicks_views_click_commission'		=>  0,
			);
			$this->Product_model->create_data('clicks_views', $details);
			$update['product_view_count'] = $data['product']['product_view_count'] + 1;
			$this->Product_model->update_data('product', $update,array('product_id' => $data['product']['product_id']));
			$data['ratings'] = $this->Product_model->getReview($data['product']['product_id']);
		}
	}

	function insertnotification($postData = null){
		if(!empty($postData)){
			$data['custom'] = $this->Product_model->create_data('notification', $postData);
		}
	}
	function rating() {
		$data = array();
		$post = $this->input->post(null,true);
		if($post){
			$details = array(
				'rating_user_id'    =>  !empty($post['user_id']) ? $post['user_id'] : 0,
				'products_id '      =>  $post['product_id'],
				'rating_status'     =>  1,
				'rating_number'     =>  $post['number'],
				'rating_name'       =>  !empty($post['name']) ? $post['name'] : '',
				'rating_email'      =>  !empty($post['email']) ? $post['email'] : '',
				'rating_comments'   =>  !empty($post['comment']) ? $post['comment'] : '',
				'rating_referrer'   =>  $this->agent->referrer(),
				'rating_user_agent' =>  $this->agent->agent_string(),
				'rating_os'         =>  $this->agent->platform(),
				'rating_browser'    =>  $this->agent->browser(),
				'rating_isp'        =>  gethostbyaddr($_SERVER['REMOTE_ADDR']),
				'rating_ipaddress'  =>  $_SERVER['REMOTE_ADDR'],
				'rating_created_by' =>  !empty($post['user_id']) ? $post['user_id'] : 0,
				'rating_created'    =>  date('Y-m-d H:i:s'),
			);

			$this->Product_model->create_data('rating', $details);
			$this->Product_model->update_avg_rating($post['product_id']);
		}
	}
	function insertproductlogs($postData = null){
		if(!empty($postData)){
			$data['custom'] = $this->Product_model->create_data('payment_log', $postData);
		}
	}
	public function sendOrderNoti($order_info){
		$userDetail = $this->Product_model->getUserDetails($order_info['order_user_id']);
		$cdate = date('Y-m-d H:i:s');
		$notificationData = array(
			'notification_url'          => '/vieworder/'.$order_info['order_id'],
			'notification_type'         =>  'order',
			'notification_title'        =>  'New Order Generated by '.$userDetail['username'],
			'notification_viewfor'      =>  'admin',
			'notification_actionID'     =>  $order_info['order_id'],
			'notification_description'  =>  $userDetail['firstname'].' '.$userDetail['lastname'].' created a new order at affiliate Program on '.date('Y-m-d H:i:s'),
			'notification_is_read'      =>  '0',
			'notification_created_date' =>  $cdate,
			'notification_ipaddress'    =>  $_SERVER['REMOTE_ADDR']
		);
		$this->insertnotification($notificationData);
		$notificationData = array(
			'notification_url'          => '/vieworder/'.$order_info['order_id'],
			'notification_type'         =>  'order',
			'notification_title'        =>  'Your Order has been place',
			'notification_viewfor'      =>  'client',
			'notification_view_user_id' =>  $userDetail['id'],
			'notification_actionID'     =>  $order_info['order_id'],
			'notification_description'  =>  'Your Order has been place',
			'notification_is_read'      =>  '0',
			'notification_created_date' =>  $cdate,
			'notification_ipaddress'    =>  $_SERVER['REMOTE_ADDR']
		);
		$this->insertnotification($notificationData);
		$notificationData = array(
			'notification_url'          => '/vieworder/'.$order_info['order_id'],
			'notification_type'         =>  'order',
			'notification_title'        =>  'New Order Generated by '.$userDetail['username'],
			'notification_viewfor'      =>  'user',
			'notification_view_user_id' =>  $order_info['affiliate_id'],
			'notification_actionID'     =>  $order_info['order_id'],
			'notification_description'  =>  $userDetail['firstname'].' '.$userDetail['lastname'].' created a new order which you refered to him at affiliate Program on '.date('Y-m-d H:i:s'),
			'notification_is_read'      =>  '0',
			'notification_created_date' =>  $cdate,
			'notification_ipaddress'    =>  $_SERVER['REMOTE_ADDR']
		);
		$this->insertnotification($notificationData);
	}

	
	public function thankyou($order_id){
		$this->load->model('Order_model');
		$user = $this->session->userdata('client') ? $this->session->userdata('client') : '';
		$data['client_loged'] = $this->session->userdata('client') ? true : false;
		$data['order'] = $this->Order_model->getOrder($order_id);
		$this->load->model('User_model');
		$admin_info = $this->User_model->get_user_by_type('admin');
		$data['store_name'] =  $admin_info['firstname'].' '.$admin_info['lastname'];
		$data['store_email'] =  $admin_info['email'];
		if($data['order']['order_user_id'] == $user['id']){
			$data['affiliateuser'] = $this->Order_model->getAffiliateUser($order_id);
			$data['payment_history'] = $this->Order_model->getHistory($order_id);
			$data['status'] = $this->Order_model->status();
			$data['order_history'] = $this->Order_model->getHistory($order_id, 'order');
			$this->load->view('product/thanks', $data);
		}
		else{
			die("You are not allow to see.. !");
		}
	}

	//called from javascript when variant price button clicked
	function displayprice() {
	    $post = $this->input->post(null, true);

	    if ($post) { 
	        $response = array();

	        if (isset($_POST['variationSelectedPrice']) && isset($_POST['product_regular_price']) && isset($_POST['product_sale_price'])) {
	            $currencyRatio = floatval($_POST['currencyRatio']);
	            $variationSelectedPrice = floatval($_POST['variationSelectedPrice']);
	            $product_regular_price = floatval($_POST['product_regular_price']);
	            $product_sale_price = floatval($_POST['product_sale_price']);

	            $product_regular_price_new = $variationSelectedPrice + $product_regular_price;
	            $product_sale_price_new = $variationSelectedPrice + $product_sale_price;

	            $response['value1'] = c_format($product_sale_price_new);
	            $response['value2'] = c_format($product_regular_price_new);
	        }
	        
	        echo json_encode($response);
	    }
	}

}