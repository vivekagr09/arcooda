<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

use App\MembershipPlan;
use App\MembershipUser;
use App\Slug;
use App\User;
class Usercontrol extends MY_Controller {
    function __construct()
    {
        parent::__construct();

        $this->load->model('user_model', 'user');
        $this->load->model('Product_model');
        $this->load->model('Withdrawal_payment_model');
        $this->load->helper('share');
        $this->load->helper('url');
        $this->load->library('user_agent');
        $this->load->model('IntegrationModel');
        $this->load->model('Common_model');
        $this->load->model('Tutorial_model');


        $session = $this->session->userdata('user_session'); // Check if a user session exists
        $path_info = (isset($_SERVER['PATH_INFO']) && !empty($_SERVER['PATH_INFO'])) ? $_SERVER['PATH_INFO'] : (!empty($_SERVER['ORIG_PATH_INFO']) ? $_SERVER['ORIG_PATH_INFO'] : '');

        if (!$session && $this->router->class != 'usercontrol' && $this->router->method != 'index') {
            redirect('usercontrol');
        } else if ($session && ($path_info == '/usercontrol' || $path_info == '/usercontrol/')) {
            redirect('usercontrol/dashboard');
        }
        
        // Timeout logic here
        $site_setting_timeout = $this->Product_model->getSettings('site', 'user_session_timeout');
        $timeout = (isset($site_setting_timeout['user_session_timeout']) && is_numeric($site_setting_timeout['user_session_timeout']) && ((int)$site_setting_timeout['user_session_timeout']) >= 60) ? (int)$site_setting_timeout['user_session_timeout'] : 1800;

        $this->load->vars(array('timeout' => $timeout));

        if ($this->session->has_userdata('timestamp') && (time() - $this->session->userdata('timestamp')) > $timeout) {
            $this->session->sess_destroy();
            redirect('usercontrol/dashboard');
        } else {
            $this->session->set_userdata('timestamp', time());
        }
    }

	public function my_vendor_panel() {
		$userdetails = $this->userdetails();
		
		if(empty($userdetails)) redirect('usercontrol/dashboard');
		
		$market_vendor = $this->Product_model->getSettings('market_vendor');
		if(!isset($userdetails['is_vendor']) || $userdetails['is_vendor'] != 1 || ((int)$market_vendor['marketvendorstatus'] != 1 && ((int)$vendor_setting['storestatus'] != 1 || (int)$store_setting['status'] != 1)))
			redirect('usercontrol/dashboard');
		
		$data['userdetails'] = $userdetails;
		$data['store_setting'] = $store_setting = $this->Product_model->getSettings('store');
		$data['vendor_setting'] = $vendor_setting = $this->Product_model->getSettings('vendor');
		$data['market_vendor'] = $market_vendor;

		$hcurrency = $this->Product_model->getSettings('site', 'hide_currency_from');
		$data['hcurrency'] = (isset($hcurrency['hide_currency_from']) && str_contains($hcurrency['hide_currency_from'], 'user'));
		if($data['hcurrency'])
			$data['fun_c_format'] =$fun_c_format = 'c_format_nosym';
		else
			$data['fun_c_format'] =$fun_c_format = 'c_format';

		$data['store_details'] = $this->db->query('SELECT store_name, store_slug, store_meta FROM users WHERE id='.$data['userdetails']['id'])->row_array();

		$this->load->model('Total_model');
		$data['user_totals'] = $this->Total_model->getUserTotals((int)$data['userdetails']['id']);
		$data['total_deposited'] = $this->db->query('SELECT SUM(vd_amount) as total FROM vendor_deposit WHERE vd_status=1 AND vd_user_id='.$data['userdetails']['id'])->row()->total;

		$this->view($data, 'vendor/index','usercontrol');
	}

	public function approval_status() {
		$data['userdetails'] = $this->Product_model->userdetails('user', true); 
		if($userdetails['reg_approved'] == 1) {
			redirect('usercontrol/dashboard');die;
		} else {
			$data['notcheckapproval'] = 1;
			$data['notcheckmember'] = 1;
			$this->view($data,"users/approval_status",'usercontrol');
		}
	}

	public function duplicateProduct($product_id){
		$userdetails = $this->userdetails();
		if(empty($userdetails)) redirect('usercontrol/dashboard');

		$userPlan = App\MembershipUser::with("plan")->where('is_active',1)->where('user_id',$userdetails['id'])->first();
		$plan_product_count = $userPlan->plan->product;
		$vendor_product_count = $this->Product_model->countByField('product_affiliate','user_id',$userdetails['id']);

		if(! empty($plan_product_count) && $vendor_product_count >= $plan_product_count){
			$this->session->set_flashdata('error',__('user.reached_maximum_limit_package_upgrade').'<a href="'.base_url('usercontrol/purchase_plan').'"> '.__('user.here').'</a>');
		}else{
			$market_vendor = $this->Product_model->getSettings('market_vendor','marketaddnewstoreproduct');
			
			if($market_vendor['marketaddnewstoreproduct']){
				$status_review = 1;
			}else{
				$status_review = 0;
			}

			$this->Product_model->duplicateProduct($product_id, $status_review);
			$this->session->set_flashdata('success',__('user.product_duplicate_successfully'));
		}

		redirect(base_url('usercontrol/store_products'));
	}

	public function integration(){
		$userdetails = $this->userdetails();

		if(empty($userdetails)) redirect('usercontrol/dashboard');

		$market_vendor = $this->Product_model->getSettings('market_vendor');
		if(!isset($userdetails['is_vendor']) || $userdetails['is_vendor'] != 1 || (int)$market_vendor['marketvendorstatus'] != 1)
			redirect('usercontrol/dashboard');

		$data['integration_modules'] = $this->modules_list('addons');

		$this->view($data, 'integration/index','usercontrol');
	}

	private function modules_list($requestingFor = null){
		
		if($requestingFor == null) {
			
			$integration_modules['general_integration'] = array(
				'name' => "General Integration",
				'image' => base_url('assets/integration/general_integration-logo.png'),
			);
			

			$integration_modules['woocommerce'] = array(
				'name' => "WooCommerce",
				'image' => base_url('assets/integration/woocommerce-logo.png'),
			);

			$integration_modules['prestashop'] = array(
				'name' => "PrestaShop",
				'image' => base_url('assets/integration/prestashop-logo.png'),
			);

			$integration_modules['opencart'] = array(
				'name' => "Opencart",
				'image' => base_url('assets/integration/opencart-logo.png'),
			);

			$integration_modules['magento'] = array(
				'name' => "Magento",
				'image' => base_url('assets/integration/magento-logo.png'),
			);

			$integration_modules['shopify'] = array(
				'name' => "Shopify",
				'image' => base_url('assets/integration/shopify-logo.png'),
			);

			$integration_modules['bigcommerce'] = array(
				'name' => "Big Commerce",
				'image' => base_url('assets/integration/big-commerce.png'),
			);

			$integration_modules['paypal'] = array(
				'name' => "Paypal",
				'image' => base_url('assets/integration/paypal.jpg'),
			);

			$integration_modules['oscommerce'] = array(
				'name' => "osCommerce",
				'image' => base_url('assets/integration/oscommerce.jpg'),
			);

			$integration_modules['zencart'] = array(
				'name' => "Zen Cart",
				'image' => base_url('assets/integration/zencart.png'),
			);

			$integration_modules['xcart'] = array(
				'name' => "XCART",
				'image' => base_url('assets/integration/xcart.jpg'),
			);

			$integration_modules['laravel'] = array(
				'name' => "Laravel",
				'image' => base_url('assets/integration/laravel.png'),
			);

			$integration_modules['cakephp'] = array(
				'name' => "Cake PHP",
				'image' => base_url('assets/integration/cakephp.png'),
			);

			$integration_modules['codeigniter'] = array(
				'name' => "CodeIgniter",
				'image' => base_url('assets/integration/codeIgniter.png'),
			);
		}

		$integration_modules['wp_user_register'] = array(
			'name' => "Wordpress/Woocommerce registration bridge",
			'image' => base_url('assets/integration/WordpressWoocommerceRegistrationBridge.png'),
		);
		

		$integration_modules['wp_forms'] = array(
			'name' => "WordPress Forms",
			'image' => base_url('assets/integration/wpforms.png'),
		);
		$integration_modules['postback'] = array(
			'name' => "Postback URL",
			'image' => base_url('assets/integration/postback.png'),
		);

		$integration_modules['show_affiliate_id'] = array(
			'name' => "Show Affiliate ID",
			'image' => base_url('assets/integration/show-affiliate-id.png'),
		);

		$integration_modules['wp_show_affiliate_id'] = array(
			'name' => "Wordpress Show Affiliate ID",
			'image' => base_url('assets/integration/wp-show-affiliate-id.jpg'),
		);

		$integration_modules['affiliate_register_api'] = array(
			'name' => "Affiliate Register API",
			'image' => base_url('assets/integration/affiliate_register_api.jpg'),
		);

		$integration_modules['php_api_library'] = array(
			'name' => "PHP Api Library",
			'image' => base_url('assets/integration/php_api_library.jpg'),
		);

		return $integration_modules;
	}

	public function instructions($module_key){
		if(!$this->userdetails()){ redirect('admincontrol/dashboard', 'refresh'); }

		$data['integration_modules'] = $this->modules_list();
		$data['module_key'] = $module_key;

		

		$data['action_codes'] = $this->db->select('integration_tools.action_code')
		->from('integration_tools')
		->where("tool_type",'action')
		->where("status",1)
		->get()
		->result_array();

		$data['general_codes'] = $this->db->select('integration_tools.general_code')
		->from('integration_tools')
		->where("tool_type",'general_click')
		->where("status",1)
		->get()
		->result_array();
		$data['module'] = $data['integration_modules'][$module_key];

		$this->view($data, 'integration/instructions','usercontrol');
	}

public function change_language($language_id = null) {
    if(empty($language_id)) {
        show_404();
        return;
    }

    if(!is_numeric($language_id)) {
        show_404();
        return;
    }

    $this->db->where('id', $language_id);
    $query = $this->db->get('language');
    $language = $query->row_array();

    if($language) {
        $_SESSION['userLang'] = $language_id;
        header('Location: ' . $_SERVER['HTTP_REFERER']);
    } else {
        show_404();
    }
}


public function change_currency($currency_code = null){
    if(empty($currency_code)) {
        show_404();
        return;
    }

    $this->db->where('code', $currency_code);
    $query = $this->db->get('currency');
    $currency = $query->row_array();

    if($currency) {
        $_SESSION['userCurrency'] = $currency_code;
        $_SESSION['userDecimalPlace'] = $currency['decimal_place'];
        header('Location: ' . $_SERVER['HTTP_REFERER']);
    } else {
        show_404();
    }
}


	public function getSiteSetting(){return $this->Product_model->getSettings('site');}

	public $loginUser = [];

	public function userdetails(){
	     
	 	 	if (isset($this->session) && $this->session->userdata('client') !== FALSE && $this->session->userdata('client')['type']=='user') 
			{  
				$this->session->unset_userdata('administrator');
				$this->loginUser = $this->db->query("SELECT * FROM users WHERE id=". $this->session->userdata('client')['id'])->row_array();
				
				$this->session->set_userdata(array('user'=>$this->loginUser));
				return $this->loginUser;

			}
 			else if (isset($this->session) && $this->session->userdata('user_type') !== FALSE && $this->session->userdata('user_type')=='admin') 
			{
				if(!$this->loginUser){
					$u = $this->session->userdata('user');

					if($u){
						$this->loginUser = $this->db->query("SELECT * FROM users WHERE id=". $u['id'])->row_array();
					}
				}
				$this->session->set_userdata(array('user'=>$this->loginUser));
				return $this->loginUser;
			}
			else { 

				if(!$this->loginUser){
					$u = $this->session->userdata('user');

					if($u){
						$this->loginUser = $this->db->query("SELECT * FROM users WHERE id=". $u['id'])->row_array();
					}
				}
				$this->session->set_userdata(array('user'=>$this->loginUser));
				return $this->loginUser;
 
  			}  
 
	}

	public function myreferal_ajax(){
		$userdetails = $this->userdetails();
		if(empty($userdetails)){ redirect('/login'); }

		$data = $this->Product_model->getMyUnder($userdetails['id']);

		echo json_encode($data);die;
	}

	public function resetpassword($token){
		$tok  =  $this->db->query("SELECT * FROM password_resets WHERE token like '{$token}' ")->row();
		$post = $this->input->post(null,true);

		$data['LanguageHtml'] = $this->Product_model->getLanguageHtml('AuthController');

		if($tok){
			$data['SiteSetting'] =$this->Product_model->getSettings('site');
			$data['user_type'] = $this->db->query("SELECT type FROM `users` WHERE email like '{$tok->email}' ")->row()->type;
			if($data['user_type'] == 'client'){
				$data['redirect_url'] = base_url('store/login');
			} else if($data['user_type'] == 'admin'){
				$data['redirect_url'] = base_url('admin');
			} else {
				$data['redirect_url'] = base_url();
			}
			if (isset($post['conf_password'])) {
				if($post['password'] == $post['conf_password']){
					$password = $this->input->post('password',true);
					$res = array('password'=>sha1($password));
					$this->db->where('email',$tok->email);
					$this->db->update('users',$res);
					$this->db->query("DELETE  FROM password_resets WHERE email like '{$tok->email}' ");
					$this->session->set_flashdata('success' , __('user.password_reset_successfully_successfully'));
					redirect($data['redirect_url']);
				} else {
					$this->session->set_flashdata('error',__('user.confirm_password_not_match'));
					redirect(base_url('resetpassword/' . $token));
				}
			}
			$this->load->view('resetpassword', $data);
		} else {
			die("Token Expire..!");
		}
	}
	public function index(){ redirect('/', 'refresh'); }

	public function notification(){
		if(!$this->userdetails()){ redirect('/login', 'refresh'); }
		$this->load->library('pagination');
		$this->load->helper('url');
		$config['base_url'] = base_url('usercontrol/notification');
		$config['per_page'] = 10;
		$post = $this->input->post(null,true);

		if (isset($post['delete_ids'])) {
			$delete_ids = implode(",", $post['delete_ids']);
			$this->db->query("DELETE FROM notification WHERE notification_id IN ({$delete_ids})");
			echo json_encode(array());
			die;
		}
		
		$data['title'] = 'Notification';
		$page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
		$notification = $this->user->getAllNotificationPaging('user',$this->userdetails()['id'],$config['per_page'],$page);
		$config['total_rows'] = $notification['total'];
		$this->pagination->initialize($config);
		$data['pagination'] = $this->pagination->create_links();
		$data['notifications'] = $notification['notifications'];
		$data['user_id'] = $this->userdetails()['id'];
		$this->load->view('usercontrol/includes/header', $data);
		$this->load->view('usercontrol/dashboard/notification', $data);
		$this->load->view('usercontrol/includes/footer', $data);
	}
	public function ip_info($ip = NULL, $purpose = "location", $deep_detect = TRUE) {
		$output = NULL;
		if (filter_var($ip, FILTER_VALIDATE_IP) === FALSE) {
			$ip = $_SERVER["REMOTE_ADDR"];
			if ($deep_detect) {
				if (filter_var(@$_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP))
					$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
				if (filter_var(@$_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP))
					$ip = $_SERVER['HTTP_CLIENT_IP'];
			}
		}
		$purpose    = str_replace(array("name", "\n", "\t", " ", "-", "_"), NULL, strtolower(trim($purpose)));
		$support    = array("country", "countrycode", "state", "region", "city", "location", "address");
		$continents = array(
			"AF" => "Africa",
			"AN" => "Antarctica",
			"AS" => "Asia",
			"EU" => "Europe",
			"OC" => "Australia (Oceania)",
			"NA" => "North America",
			"SA" => "South America"
		);
		if (filter_var($ip, FILTER_VALIDATE_IP) && in_array($purpose, $support)) {
			
			$curl = curl_init("http://www.geoplugin.net/json.gp?ip=" . $ip);
			$request = '';
			curl_setopt($curl, CURLOPT_POSTFIELDS, $request);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_HEADER, false);
			curl_setopt($curl, CURLOPT_TIMEOUT, 30);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
			
			$ipdat = json_decode(curl_exec($curl));
			if (@strlen(trim($ipdat->geoplugin_countryCode)) == 2) {
				switch ($purpose) {
					case "location":
					$id = 0;
					$code = @$ipdat->geoplugin_countryCode;
					$data = $this->db->query("SELECT id FROM countries WHERE sortname LIKE '{$code}' ")->row();
					if($data){
						$id = $data->id;
					}
					$output = array(
						"city"           => @$ipdat->geoplugin_city,
						"state"          => @$ipdat->geoplugin_regionName,
						"country"        => @$ipdat->geoplugin_countryName,
						"country_code"   => @$ipdat->geoplugin_countryCode,
						"continent"      => @$continents[strtoupper($ipdat->geoplugin_continentCode)],
						"continent_code" => @$ipdat->geoplugin_continentCode,
						"id"             => $id
					);
					break;
					case "address":
					$address = array($ipdat->geoplugin_countryName);
					if (@strlen($ipdat->geoplugin_regionName) >= 1)
						$address[] = $ipdat->geoplugin_regionName;
					if (@strlen($ipdat->geoplugin_city) >= 1)
						$address[] = $ipdat->geoplugin_city;
					$output = implode(", ", array_reverse($address));
					break;
					case "city":
					$output = @$ipdat->geoplugin_city;
					break;
					case "state":
					$output = @$ipdat->geoplugin_regionName;
					break;
					case "region":
					$output = @$ipdat->geoplugin_regionName;
					break;
					case "country":
					$output = 0;
					$code = @$ipdat->geoplugin_countryCode;
					$data = $this->db->query("SELECT id FROM countries WHERE sortname LIKE '{$code}' ")->row();
					if($data){
						$output = $data->id;
					}
					break;
					case "countrycode":
					$output = @$ipdat->geoplugin_countryCode;
					break;
				}
			}
		}
		
		return $output;
	}
	public function getState(){
		$this->load->model('User_model');
		$country_id = $this->input->post('country_id',true);
		$states = $this->User_model->getState($country_id);
		echo json_encode($states);
		die;
	}
	public function check_ven_product_limit($vendor_id = 0){
		$userPlan = App\MembershipUser::with("plan")->where('is_active',1)->where('user_id',$vendor_id)->first();
		$plan_product_count = $userPlan->plan->product;
		if(! empty($plan_product_count)) {
		$vendor_product_count = $this->Product_model->countByField('product_affiliate','user_id',$vendor_id);

		if ($vendor_id != 0) {
			$productlist = $this->Product_model->getAllVendorProducts($vendor_id, 'vendor');
			$i = 0;
			foreach ($productlist as $product) {
				if ($product['seller_id'] == $vendor_id) {
					$i++;
					if ($i > $plan_product_count) {
						$sql = "UPDATE `product` SET `on_store` = '0', `product_status` = '2' WHERE `product_id` = '".$product['product_id']."'";

						$query = $this->db->query($sql);
					}
				}	
			}	

			return "1";
			}else{
			echo "Vendor ID required!";
			}
		}
		
	}


	public function check_ven_campaign_limit($vendor_id = 0){
		$this->load->model('IntegrationModel');
		$userPlan = App\MembershipUser::with("plan")->where('is_active',1)->where('user_id',$vendor_id)->first();
		$plan_campaign_count = $userPlan->plan->campaign;
		if(! empty($plan_campaign_count)) {
			$vendor_campaign_count = $this->Product_model->countByField('integration_tools','vendor_id',$vendor_id);

			if ($vendor_id != 0) {
				$toollist = $this->IntegrationModel->getVendorProgramTools($vendor_id);
				$i = 0;
				foreach ($toollist as $tool) {
					if ($tool['vendor_id'] == $vendor_id) {
						$i++;
						if ($i > $plan_campaign_count) {
							$sql = "UPDATE `integration_tools` SET `status` = '0' WHERE `id` = '".$tool['id']."'";

							$query = $this->db->query($sql);
						}
					}	
				}

				return "1";
			}else{
				echo "Vendor ID required!";
			}
		}
	}
	public function auth($action){
		$json = array();
		$post = $this->input->post(null,true);
		if ($action == 'login') 
		{

			$username = $this->input->post('username',true);
			$password = $this->input->post('password',true);

			$checking_key = (isset($post['type']) && $post['type'] == 'admin') ? 'admin_login' : 'affiliate_login';

			$googlerecaptcha = $this->Product_model->getSettings('googlerecaptcha');

			if (isset($googlerecaptcha[$checking_key]) && $googlerecaptcha[$checking_key]) {
				if($post['g-recaptcha-response'] == ''){
					if($checking_key != 'admin_login'){
						$json['errors']['username'] = 'Invalid Recaptcha';
					}
				}
			}
 
			 
			if(count($json)==0 || count($json['errors']) == 0 )
			{
				if (isset($googlerecaptcha[$checking_key]) && $googlerecaptcha[$checking_key]) {
					$post = http_build_query(
						array (
							'response' => $post['g-recaptcha-response'],
							'secret' => $googlerecaptcha['secretkey'],
							'remoteip' => $_SERVER['REMOTE_ADDR']
						)
					);
					$opts = array('http' => 
						array (
							'method' => 'POST',
							'header'=> "Content-type: application/x-www-form-urlencoded\r\n"
							."Content-Length: " . strlen($post) . "\r\n",
							'content' => $post
						)
					);
					$context = stream_context_create($opts);
					$serverResponse = @file_get_contents('https://www.google.com/recaptcha/api/siteverify', false, $context);
					if (!$serverResponse) {
						$json['errors']['username'] = 'Failed to validate Recaptcha';
						$json['errors']['captch_response'] = 'Failed to validate Recaptcha';
					}
					$result = json_decode($serverResponse);

					if (!$result->success) {
						$json['errors']['username'] = 'Invalid Recaptcha';
						$json['errors']['captch_response'] = 'Invalid Recaptcha';
					}
				}
			}
			$post = $this->input->post(null,true);
			if(count($json)==0 || count($json['errors']) == 0 ){
				if($this->authentication->login($username, $password)){
					$user_details_array=$this->user->login($username);
					if(!empty($user_details_array['username']) && sha1($password) == $user_details_array['password']){

						if($user_details_array['status']){
							if($user_details_array['type'] == 'user' && isset($post['type']) && $post['type'] == 'user' ){
								$this->user->update_user_login($user_details_array['id']);
								$this->session->set_userdata(array('user'=>$user_details_array));
								
								if($user_details_array['reg_approved'] == 1) {
									$this->session->set_userdata(array('client'=>$user_details_array));
								}

								$this->Product_model->checkJumpedUserWithId($user_details_array['id']);
								if ($user_details_array['is_vendor'] == '1') {
									$this->check_ven_product_limit($user_details_array['id']);
									$this->check_ven_campaign_limit($user_details_array['id']);
								}
								$json['redirect'] = base_url('usercontrol/dashboard');

							}else if($user_details_array['type'] == 'admin' && isset($post['type']) && $post['type'] == 'admin' ){
								$this->user->update_user_login($user_details_array['id']);
								$this->session->set_userdata(array('administrator'=>$user_details_array));
								$json['redirect'] = base_url('admincontrol/dashboard');
							}else if($user_details_array['type'] == 'client' && !isset($post['type'])){
								$this->user->update_user_login($user_details_array['id']);
								$this->session->set_userdata(array('client'=>$user_details_array));
								$l = $this->session->userdata('login_data');
								if($l['refid'] && $l['product_slug'] && $l['user_id']){
									$json['redirect'] = base_url('product/payment/'. $l['product_slug'].'/'.$l['user_id']);
								}else if($this->session->userdata('refer_id')){
									$json['redirect'] = base_url('store/'. base64_encode($this->session->userdata('refer_id')));
								}else{
									$json['redirect'] = base_url('store/profile/');
								}
							}else {
								$json['errors']['username'] = __('user.invalid_valid_user');
							}
						} else{
							$json['errors']['username'] = __('user.account_block_message');
						}
					}
				} else {
					$json['errors']['password'] = __('user.invalid_credentials');
				}
			}
		} else if ($action == 'register') {
			$refid = isset($post['refid']) ? $post['refid'] : '';
			$post['affiliate_id'] = !empty($refid) ? base64_decode($refid) : 0;
			if($this->userdetails()){
				$json['redirect'] = base_url('usercontrol/dashboard');
			} else {

				$this->load->library('form_validation');
				$this->form_validation->set_rules('firstname', 'First Name', 'required|trim');
				$this->form_validation->set_rules('lastname', 'Last Name', 'required|trim');
				$this->form_validation->set_rules('username', 'Username', 'required|trim');
				$this->form_validation->set_rules('email', 'Email', 'required|valid_email|xss_clean');
				$this->form_validation->set_rules('terms', 'Terms and Condition', 'required');
				$this->form_validation->set_rules('password', 'Password', 'required|trim', array('required' => '%s is required'));
				$this->form_validation->set_rules('cpassword', 'Confirm Password', 'required|trim', array('required' => '%s is required'));
				$this->form_validation->set_rules('cpassword', 'Confirm Password', 'required|trim|matches[password]', array('required' => '%s is required'));
				$this->form_validation->set_rules('address', 'Address', 'required|trim|xss_clean', array('required' => '%s is required'));
				$this->form_validation->set_rules('state', 'State', 'required', array('required' => '%s is required'));
				$this->form_validation->set_rules('paypal_email', 'Payal Email', 'required|valid_email|xss_clean', array('required' => '%s is required'));
				$this->form_validation->set_rules('phone_number', 'Phone Number', 'required|regex_match[/^[0-9]{10}$/]', array('required' => '%s is required'));
				$this->form_validation->set_rules('alternate_phone_number', 'Alternate Phone Number', 'required|regex_match[/^[0-9]{10}$/]', array('required' => '%s is required'));
				if ($this->form_validation->run() == FALSE) {
					$json['errors'] = $this->form_validation->error_array();
				} else {
					$checkEmail = $this->db->query("SELECT id FROM users WHERE email like ". $this->db->escape($this->input->post('email',true)) ." ")->num_rows();
					if($checkEmail > 0){ $json['errors']['email'] = "Email Already Exist"; }
					$checkUsername = $this->db->query("SELECT id FROM users WHERE username like ". $this->db->escape($this->input->post('username',true)) ." ")->num_rows();
					if($checkUsername > 0){ $json['errors']['username'] = "Username Already Exist"; }

					if(!isset($post['terms'])){
						$json['warning'] = __('user.accept_our_affiliate_policy');
					}

					if(!isset($json['errors'])){

						$user_type = 'user';
						$geo = $this->ip_info();

						$refid = !empty($refid) ? base64_decode($refid) : 0;
						$commition_setting = $this->Product_model->getSettings('referlevel');

						$disabled_for = json_decode( (isset($commition_setting['disabled_for']) ? $commition_setting['disabled_for'] : '[]'),1); 
						if((int)$commition_setting['status'] == 0){ $refid  = 0; }
						else if((int)$commition_setting['status'] == 2 && in_array($refid, $disabled_for)){ $refid = 0; }

						$data = $this->user->insert(array(
							'firstname'                 => $this->input->post('firstname',true),
							'lastname'                  => $this->input->post('lastname',true),
							'email'                     => $this->input->post('email',true),
							'username'                  => $this->input->post('username',true),
							'password'                  => sha1($this->input->post('password',true)),
							'refid'                     => $refid,
							'type'                      => $user_type,
							'Country'                   => $this->input->post('country',true),
							'City'                      => (string)$geo['city'],
							'phone'                     => $this->input->post('phone_number',true),
							'twaddress'                 => $this->input->post('address',true),
							'address1'                  => '',
							'address2'                  => '',
							'ucity'                     => '',
							'ucountry'                  => '',
							'state'                     => $this->input->post('state',true),
							'uzip'                      => '',
							'avatar'                    => '',
							'online'                    => '0',
							'unique_url'                => '',
							'bitly_unique_url'          => '',
							'created_at'                => date("Y-m-d H:i:s"),
							'updated_at'                => date("Y-m-d H:i:s"),
							'google_id'                 => '',
							'facebook_id'               => '',
							'twitter_id'                => '',
							'umode'                     => '',
							'PhoneNumber'               => $this->input->post('alternate_phone_number',true),
							'Addressone'                => '',
							'Addresstwo'                => '',
							'StateProvince'             => '',
							'Zip'                       => '',
							'f_link'                    => '',
							't_link'                    => '',
							'l_link'                    => '',
							'product_commission'        => '0',
							'affiliate_commission'      => '0',
							'product_commission_paid'   => '0',
							'affiliate_commission_paid' => '0',
							'product_total_click'       => '0',
							'product_total_sale'        => '0',
							'affiliate_total_click'     => '0',
							'sale_commission'           => '0',
							'sale_commission_paid'      => '0',
							'status'                    => '1'
						));

						$this->db->insert("paypal_accounts", array(
							'paypal_email' => $this->input->post('paypal_email',true),
							'user_id' => $data,
						));
						$post['refid'] = !empty($refid) ? base64_decode($refid) : 0;
						if(!empty($data) && $user_type == 'user'){
							$notificationData = array(
								'notification_url'          => '/userslist/'.$data,
								'notification_type'         =>  'user',
								'notification_title'        =>  __('user.new_user_registration'),
								'notification_viewfor'      =>  'admin',
								'notification_actionID'     =>  $data,
								'notification_description'  =>  $this->input->post('firstname',true).' '.$this->input->post('lastname',true).' register as a '. $this->input->post("affliate_type",true) . ' on affiliate Program on '.date('Y-m-d H:i:s'),
								'notification_is_read'      =>  '0',
								'notification_created_date' =>  date('Y-m-d H:i:s'),
								'notification_ipaddress'    =>  $_SERVER['REMOTE_ADDR']
							);
							$this->insertnotification($notificationData);
							if ($post['affiliate_id'] > 0) {
								$notificationData = array(
									'notification_url'          => '/managereferenceusers',
									'notification_type'         =>  'user',
									'notification_title'        =>  __('user.new_user_registration_under_your'),
									'notification_viewfor'      =>  'user',
									'notification_view_user_id' =>  $post['affiliate_id'],
									'notification_actionID'     =>  $data,
									'notification_description'  =>  $this->input->post('firstname',true).' '.$this->input->post('lastname',true).' has been register under you on '.date('Y-m-d H:i:s'),
									'notification_is_read'      =>  '0',
									'notification_created_date' =>  date('Y-m-d H:i:s'),
									'notification_ipaddress'    =>  $_SERVER['REMOTE_ADDR']
								);
								$this->insertnotification($notificationData);
							}
							$json['success']  =  __('user.youve_successfully_registered');
							$user_details_array=$this->user->login($this->input->post('username',true));
							$this->load->model('Mail_model');
							
							$this->user->update_user_login($user_details_array['id']);
							$this->Mail_model->send_register_mail($post,__('user.welcome_to_new_user_registration'));
							if ($user_type == 'user') {
								$this->session->set_userdata(array('user'=>$user_details_array));
								$json['redirect'] = base_url('usercontrol/dashboard');
							} else {
								$this->session->set_userdata(array('client'=>$user_details_array));
								$json['redirect'] = base_url('clientcontrol/dashboard');
							}
						}
					}
				}
			}
		}
		else if ($action == 'forget') {


			$email = $this->input->post('email',true); 
			if(isset($email) && $email!="" )
			{
				$data = $this->db->query("SELECT * FROM users WHERE email like '{$email}' ")->row();
				if ($data) {
					$token = md5(uniqid(rand(), true));
					$resetlink = base_url('resetpassword/'. $token);
					$this->db->query("DELETE  FROM password_resets WHERE email like '{$email}' ");
					$this->db->query("INSERT INTO password_resets SET 
						email = '{$email}',
						token = '{$token}'
						");
					$this->load->model('Mail_model');
					$this->Mail_model->send_forget_mail($data, $resetlink);
					$json['success'] = __('user.password_reset_instructions_will_be_sent_to_your_registered_email_address');
				}
				else
				{
					 
					$json['errors']['email'] = __('user.email_address_not_found');
				}
			}
			else
				$json['errors']['email'] = __('user.email_address_not_found');
			
		}

		echo json_encode($json);
	}
	public function insertnotification($postData = null){
		if(!empty($postData)){
			$data['custom'] = $this->Product_model->create_data('notification', $postData);
		}
	}
	public function changePassword(){
		$userdetails = $this->userdetails();
		if(empty($userdetails)){ redirect('/login'); }
		$post = $this->input->post(null,true);

		if(isset($post) && !empty($post)){
			$this->form_validation->set_rules('old_pass', __('user.old_password'), 'required|trim', array('required' => '%s is required'));
			$this->form_validation->set_rules('password', __('user.new_password'), 'required|trim', array('required' => '%s is required'));
			$this->form_validation->set_rules('conf_password', 'Confirm Password', 'required|trim|matches[password]', array('required' => '%s is required'));
			if ($this->form_validation->run() == FALSE) {
				$data['validate_err'] = validation_errors();
			} else {
				$admin = $this->db->from('users')->where('id',$userdetails['id'])->get()->row_array();
				if($admin['password'] == sha1($post['old_pass'])){
					$res = array('password'=>sha1($post['password']));
					$this->db->where('id',$admin['id']);
					$this->db->update('users',$res);
					$this->session->set_flashdata(array('flash' => array('success' => __('user.user_profile_updated_successfully'))));
					redirect('usercontrol/changePassword', 'refresh');
				}else{
					$this->session->set_flashdata(array('flash' => array('error' => __('user.old_password_not_matched'))));
					redirect('usercontrol/changePassword');
				}
			}
		}
		

		$data['title'] = __('user.change_password');
		$this->load->view('usercontrol/includes/header', $data);
		$this->load->view('usercontrol/dashboard/change-password', $data);
		$this->load->view('usercontrol/includes/footer', $data);
		

	}

	
	public function dashboardlist(){
		$data['title'] = __('user.user_dashboard');
		$this->load->view('usercontrol/includes/header', $data);
		$this->load->view('usercontrol/dashboard/dashboardlist', $data);
		$this->load->view('usercontrol/includes/footer', $data);
	}


	public function dashboard(){
		$userdetails = $this->userdetails();

		if(empty($userdetails)){ redirect('/login'); }
		$post = $this->input->post(null,true);
		$get = $this->input->get(null,true);

		$this->load->model('Total_model');
		$this->load->model("Form_model");
		$this->load->model('IntegrationModel');

		$hcurrency = $this->Product_model->getSettings('site', 'hide_currency_from');

		$data['hcurrency'] = (isset($hcurrency['hide_currency_from']) && str_contains($hcurrency['hide_currency_from'], 'user'));

		if($data['hcurrency']) {
			$data['fun_c_format'] =$fun_c_format = 'c_format_nosym';
		} else {
			$data['fun_c_format'] =$fun_c_format = 'c_format';
		}

		if(isset($post['get_tools'])){

			$restricted_vendors = $this->get_restricted_vendors();

			//Start share sale filter
			$escapevendors = $this->db->query('SELECT user_id,vendor_shares_sales_status,vendor_status FROM vendor_setting ')->result_array();

			$allVendors = $this->db->query('SELECT id FROM users WHERE is_vendor=1')->result_array();
			$userrefid=$userdetails['refid']; 
			$allowVendors = [];
			foreach($escapevendors as $esc) 
			{
				if($esc['vendor_shares_sales_status']==1)
					$allowVendors[] = $esc['user_id'];
				else if($esc['vendor_shares_sales_status']==2 && $esc['user_id']==$userrefid)
					$allowVendors[] = $esc['user_id'];
				else if($esc['vendor_status'] !=0 )
					$allowVendors[] = $esc['user_id'];

			}
			
			$escapeUsers = [];
			foreach($allVendors as $v) {
				if(!in_array($v['id'], $allowVendors)){
					
					$escapeUsers[] = $v['id'];
				}
			}

			$restricted_vendors=array_unique(array_merge($restricted_vendors, $escapeUsers));
			
			//End share sale filter

			$filter = [
				'user_id'          => $userdetails['id'],
				'restrict'         => $userdetails['id'],
				'redirectLocation' => 1,
				'status'           => 1,
				'start'            => 0,
				'restrict_vendors' => $restricted_vendors,
				'not_show_my'	   => $userdetails['id']
			];

			$market_vendor = $this->Product_model->getSettings('market_vendor');
			// filter for market tools on or off 
			if($userdetails['is_vendor']==1)  // for vendor 
			 {	
				 if ($market_vendor['marketvendorstatus'] == 0) {
					$filter['marketvendorstatus'] = 1; 
				 }else{
				 	$filter['marketvendorstatus'] = 0;
				 }
			}
			else
			{
				// for affiliate 
				if ($market_vendor['marketvendorstatus'] == 0) {
					$filter['marketvendorstatus'] = 0;
				 }else{
				 	$filter['marketvendorstatus'] = 1;
				 }
			}

			$filter["is_vendor"]=$userdetails['is_vendor'];
			
			$data['tools'] = $this->IntegrationModel->getProgramTools($filter);

			$data['form_default_commission'] = $this->Product_model->getSettings('formsetting');
			$data['default_commition'] = $this->Product_model->getSettings('productsetting');

			$filter = array(
				'start' => 0,
			);

			//$escapevendors = $this->db->query('SELECT user_id FROM vendor_setting WHERE vendor_status=1')->result_array();
			
			$allVendors = $this->db->query('SELECT id FROM users WHERE is_vendor=1')->result_array();
			
			$allowVendors = [];
			foreach($escapevendors as $esc) 
			{
				if($esc['vendor_status']==1)
					$allowVendors[] = $esc['user_id'];
				else if($esc['vendor_status']==2 && $esc['user_id']==$userrefid)
					$allowVendors[] = $esc['user_id'];

			}
			
			$escapeUsers = [];
			foreach($allVendors as $v) {
				if(!in_array($v['id'], $allowVendors))
					$escapeUsers[] = $v['id'];
			}
			
			$filter = array(
				'product_status' => 1,
				'start' => 0,
				'on_store' => 1,
				'restrict_vendors' => array_unique(array_merge($restricted_vendors, $escapeUsers)),
				'not_show_my'	   => $userdetails['id']
			);
			
			$store_setting = $this->Product_model->getSettings('store');
			$filter['is_campaign_product'] = $store_setting['store_mode'] == 'sales' ? 1 : 0;

			if($store_setting['status']){ 
				$products = $this->Product_model->getAllProduct($userdetails['id'], $userdetails['type'], $filter);
			}

			$filter['show_to_affiliates'] = 1;
			$productsCampaign = $this->Product_model->getAllProduct($userdetails['id'], $userdetails['type'], $filter);
		
			 
			if(isset($products) && isset($productsCampaign))
				$products = array_unique(array_merge($products, $productsCampaign),SORT_REGULAR);
			else if(isset($products))
				$products = array_unique($products,SORT_REGULAR);	
			else 
				$products = array_unique($productsCampaign,SORT_REGULAR);	
			
			foreach ($products as $key => $value) {
				$slug_query = $this->db->query("SELECT slug FROM slugs WHERE type = 'product' AND related_id = '".(int)$value['product_id']."' AND user_id = '".(int)$userdetails['id']."'")->row();
				$products[$key]['slug'] = $slug_query ? $slug_query->slug : '';
				$products[$key]['is_product'] = 1;
			}

			$filter = [];
			
			if (isset($post['ads_name'])) {
				$filter['ads_name'] = $post['ads_name'];
			}

			$store_setting = $this->Product_model->getSettings('store', 'store_mode');

			$forms = $store_setting['store_mode'] != 'sales' ? $this->Form_model->getForms($userdetails['id'], $filter) : [];

			foreach ($forms as $key => $value) {
				$slug_query = $this->db->query("SELECT slug FROM slugs WHERE type = 'form' AND related_id = '".(int)$value['form_id']."' AND user_id = '".(int)$userdetails['id']."'")->row();
				$forms[$key]['slug'] = $slug_query ? $slug_query->slug : '';
				$forms[$key]['coupon_name']          = $this->Form_model->getFormCouponname(($value['coupon']) ? $value['coupon'] : 0);
				$forms[$key]['public_page']          = base_url('form/'.$value['seo'].'/'.base64_encode($this->userdetails()['id']));
				$forms[$key]['count_coupon']         = $this->Form_model->getFormCouponCount($value['form_id'],$this->userdetails()['id']);
				$forms[$key]['seo']                  = str_replace('_', ' ', $value['seo']);
				$forms[$key]['is_form']              = 1;
				$forms[$key]['product_created_date'] = $value['created_at'];
				$forms[$key]['fevi_icon'] = $value['fevi_icon'] ? 'assets/images/form/favi/'.$value['fevi_icon'] : 'assets/images/no_image_available.png';

				if($value['coupon']){
					$forms[$key]['coupon_code'] = $this->Form_model->getFormCouponCode($value['coupon']);
				}
			}

			$data_list = array_merge($products,$forms,$data['tools']);

			usort($data_list,function($a, $b){
				$ad = strtotime($a['product_created_date']);
				$bd = strtotime($b['product_created_date']);
				return ($ad-$bd);
			});

			$data_slice = array_reverse($data_list);

			$perpage = 3;
			$data['page'] = $post['page'];
			$data['data_list'] = array_slice($data_slice,($data['page']-1)*$perpage,$perpage);
			
			
			$this->load->library('pagination');
			
			$config['base_url'] = base_url('usercontrol/dashboard');
			$config['cur_page'] = $data['page'];
			$config['per_page'] = $perpage;
			$config['total_rows'] = count($data_slice);
			$config['use_page_numbers'] = TRUE;
			

			$config['full_tag_open'] = '<nav aria-label="Page navigation example"><ul class="pagination">';        
		    $config['full_tag_close'] = '</ul></nav>';        
		    $config['first_link'] = 'First';        
		    $config['last_link'] = 'Last';        
		    $config['first_tag_open'] = '<li class="page-item"><span class="page-link">';        
		    $config['first_tag_close'] = '</span></li>';        
		    $config['prev_link'] = '&laquo';        
		    $config['prev_tag_open'] = '<li class="page-item"><span class="page-link">';        
		    $config['prev_tag_close'] = '</span></li>';        
		    $config['next_link'] = '&raquo';        
		    $config['next_tag_open'] = '<li class="page-item"><span class="page-link">';        
		    $config['next_tag_close'] = '</span></li>';        
		    $config['last_tag_open'] = '<li class="page-item"><span class="page-link">';        
		    $config['last_tag_close'] = '</span></li>';        
		    $config['cur_tag_open'] = '<li class="page-item active"><a class="page-link" href="#">';        
		    $config['cur_tag_close'] = '</a></li>';        
		    $config['num_tag_open'] = '<li class="page-item"><span class="page-link">';        
		    $config['num_tag_close'] = '</span></li>';

			$this->pagination->initialize($config);
			$json['pagination'] = $this->pagination->create_links();

			$award_level = $this->Product_model->getSettings('award_level', 'status');
			$data['award_level_status'] = $award_level['status'];
			
			$comission_sale_status = 0;
			$sale_comission_rate = 0;
			$userPlan = App\MembershipUser::select('membership_plans.commission_sale_status','award_level.sale_comission_rate')->join('membership_plans','membership_plans.id','=','membership_user.plan_id')->join('award_level','award_level.id','=','membership_plans.level_id','left')->where('is_active',1)->where('user_id',$userdetails['id'])->first();
			if($userPlan->commission_sale_status){
				$comission_sale_status = $userPlan->commission_sale_status;
				$sale_comission_rate = $userPlan->sale_comission_rate;
			} else {
				$userLevel = $this->Product_model->getByField('award_level','id',$userdetails['level_id']);
				if($userLevel){
					$comission_sale_status = 1;
					$sale_comission_rate = $userLevel['sale_comission_rate'];
				}
			}
			$data['userComission']['status'] = $comission_sale_status;
			$data['userComission']['value'] = $sale_comission_rate;

			$json['html'] = $this->load->view("usercontrol/dashboard/market_tools",$data, true);

			echo json_encode($json);
			die;
		}
		$currency = $_SESSION['userCurrency'];
		if($currency!="")
		{
			$default_currency = $this->db->query("SELECT * FROM currency WHERE code='".$currency."'")->row_array();
			
		}else{
			$default_currency = $this->db->query("SELECT * FROM currency WHERE is_default=1")->row_array();
		}

		if (isset($get['getChartData'])) {
			$data['chart'] = $this->Total_model->chartUser(
				(int)$userdetails['id'],
				[
					'year' => $post['year'],
					'group' => $post['group']
				]
			);
			$data['symbol']=$default_currency;
			echo json_encode($data);die;
		}

		$this->load->model('Deposit_payment_model');

		$data['vendor_deposits'] = $this->Deposit_payment_model->getDeposits([
			'user_id'=>$userdetails['id'],
			'limit' => 5
		]);

		$data['title'] = __('user.user_dashboard');

		$data['notifications'] = $this->Product_model->getnotificationnew('user',$userdetails['id']);
		
		$data['populer_users'] = $this->Product_model->getPopulerUsers(array("limit" => 10));

		$referlevelSettings = $this->Product_model->getSettings('referlevel');
		
		$data['refer_status'] = reh_fetchReffererStatus($referlevelSettings, $userdetails['id']);

		$data['store'] = $this->Product_model->getSettings('store');

		$data['MembershipSetting'] =$this->Product_model->getSettings('membership');
		
		$user = App\User::auth();
		
		if((int)$user->plan_id != 0){
			if($user->plan_id == -1){
				$data['is_lifetime_plan'] = 1;
			} else if ($user) {
				$plan = $user->plan();
				if($plan){
					$data['plan']  = $plan;
				}
			}
		}

		$data['refer_total'] = $this->Product_model->getReferalTotals($userdetails['id']);

		$data['user_totals'] = $this->Total_model->getUserTotals((int)$userdetails['id']);
		
		$data['user_totals_week'] = $fun_c_format($this->Total_model->getUserBalance((int)$userdetails['id'], ['week' => 1]));
		$user_total_week=$this->Total_model->getUserBalance((int)$userdetails['id']);
		
		$user_last_week_growth = $this->Total_model->vendor_user_totals_week_growth((int)$userdetails['id'], ['week' => 1],$user_total_week);
		
		$data['user_totals_week_grouwth']=$growth=$this->Total_model->getGrowthPercentage((int)$user_total_week, (int)$user_last_week_growth);
		
		$data['user_totals_month'] = $fun_c_format($this->Total_model->getUserBalance((int)$userdetails['id'], ['month' => 1]));
		
		$user_totals_month=$this->Total_model->getUserBalance((int)$userdetails['id']);
		
		$user_last_month_growth = $this->Total_model->vendor_user_totals_week_growth((int)$userdetails['id'], ['month' => 1],$user_totals_month);

		$data['user_totals_month_grouwth']=$monthgrowth=$this->Total_model->getGrowthPercentage((int)$user_totals_month, (int)$user_last_month_growth);
		

		$data['user_totals_year'] = $fun_c_format($this->Total_model->getUserBalance((int)$userdetails['id'], ['year' => 1]));
		$user_totals_year=$this->Total_model->getUserBalance((int)$userdetails['id']);
		$user_last_year_growth = $this->Total_model->vendor_user_totals_week_growth((int)$userdetails['id'], ['year' => 1],$user_totals_year);
		$data['user_totals_year_grouwth']=$yeargrowth=$this->Total_model->getGrowthPercentage((int)$user_totals_year, (int)$user_last_year_growth);

		$data['register_slug'] = $this->db->query("SELECT slug FROM slugs WHERE user_id = '".(int)$userdetails['id']."' AND type = 'register'")->row()->slug;
		
		$data['store_slug'] = $this->db->query("SELECT slug FROM slugs WHERE user_id = '".(int)$userdetails['id']."' AND type = 'store'")->row()->slug;
		
		$data['isMembershipAccess'] = $this->Product_model->isMembershipAccess();

		$this->load->library("socialshare");				
		
		$data['social_share_modal'] =  $this->socialshare->get_dynamic_social_share_btns();

		$data['userdashboard_settings'] = $this->Common_model->getUserDashboardSettings();

		$data['welcome'] = $this->Product_model->getSettings('welcome');

		$market_vendor = $this->Product_model->getSettings('market_vendor');
		$data['marketvendorpanelmode'] = $market_vendor['marketvendorpanelmode'] ?? 0;
	
		$this->view($data,'dashboard/dashboard', 'usercontrol');
	}

	public function get_integartion_data($return  = false){
		$post = $this->input->post();
		$userdetails = $this->userdetails();
		if(empty($userdetails)){ redirect('/login'); }
		$json = array();

		if($post['integration_data_year'] && $post['integration_data_month']){
			$integration_filters = array(
				'integration_data_year' => $post['integration_data_year'],
				'integration_data_month' => $post['integration_data_month'],
			);
		}else{
			$integration_filters = array();
		}

		$integration_filters['user_id'] = $userdetails['id'];

		$totals = $this->Wallet_model->getTotals($integration_filters, true);
		if($totals){
			$html = '';
			if ($totals['integration']['all'] ==null) {
				
			$html .= '<div class="text-center mt-5">
			    <div class="d-flex justify-content-center align-items-center flex-column mt-5">
			        <i class="fas fa-exchange-alt fa-5x text-muted"></i>
			        <h3 class="text-muted">'. __('admin.no_data_found') .'</h3>
			    </div>
			</div>';

			} else {
				$html .= '<div role="tabpanel" class="tab-pane" id="site-all" style="display: block">
				<ul class="list-group p-t-10" style="min-height:360px">
				<li class="list-group-item">
				'. __( 'user.total_balance' ) .'
				<span class="badge bg-light font-14 pull-right">
				'. c_format($totals['integration']['balance']) .'        
				</span>
				</li>
				<li class="list-group-item">
				'. __( 'user.total_sales' ) .'
				<span class="badge bg-light font-14 pull-right">
				'. c_format($totals['integration']['balance']) .' / '. c_format($totals['integration']['sale']) .'        
				</span>
				</li>
				<li class="list-group-item">
				'. __( 'user.total_clicks' ) .'
				<span class="badge bg-light font-14 pull-right">
				'. (int)$totals['integration']['click_count'] .' / '. c_format($totals['integration']['click_amount']) .'
				</span>
				</li>
				<li class="list-group-item">
				'. __('user.total_actions') .'
				<span class="badge bg-light font-14 pull-right">
				'. (int)$totals['integration']['action_count'] .' / '. c_format($totals['integration']['action_amount']) .'
				</span>
				</li>
				<li class="list-group-item">
				'. __( 'user.total_commission' ) .'
				<span class="badge bg-light font-14 pull-right">
				'. c_format($totals['integration']['total_commission']) .' 
				</span>
				</li>
				<li class="list-group-item">
				'. __( 'user.total_orders' ) .'
				<span class="badge bg-light font-14 pull-right">
				'. (int)$totals['integration']['total_orders'] .' 
				</span>
				</li>
				</ul>
				</div>';
				$index = 0; 
				foreach ($totals['integration']['all'] as $website => $value) {
					$html .= '<div role="tabpanel" class="tab-pane" id="site-'. ++$index .'" style="height:360px;display: none;">
					<ul class="list-group p-t-10" >
					<li class="list-group-item">
					'. __( 'user.total_balance' ) .'
					<span class="badge bg-light font-14 pull-right">
					'. c_format($value['balance']) .'
					</span>
					</li>
					<li class="list-group-item">
					'. __( 'user.total_sales' ) .'
					<span class="badge bg-light font-14 pull-right">
					'. c_format($value['balance']) .' / '. c_format($value['sale']) .'        
					</span>
					</li>
					<li class="list-group-item">
					'. __( 'user.total_clicks' ) .'
					<span class="badge bg-light font-14 pull-right">
					'. (int)$value['click_count'] .' / '. c_format($value['click_amount']) .'
					</span>
					</li>
					<li class="list-group-item">
					'. __('user.total_actions') .'
					<span class="badge bg-light font-14 pull-right">
					'. (int)$value['action_count'] .' / '. c_format($value['action_amount']) .'
					</span>
					</li>
					<li class="list-group-item">
					'. __( 'user.total_commission' ) .'
					<span class="badge bg-light font-14 pull-right">
					'. c_format($value['click_amount'] + $value['sale'] + $value['action_amount']) .' 
					</span>
					</li>
					<li class="list-group-item">
					'. __( 'user.total_orders' ) .'
					<span class="badge bg-light font-14 pull-right">
					'. (int)$value['total_orders'] .' 
					</span>
					</li>
					<li class="list-group-item">
					<a class="btn btn-lg btn-default btn-success" href="http://'. $website .'" target="_blank">'. __( 'user.preview_store' ) .'</a>
					</li>
					</ul>
					</div>';
				}
			}

			$integration_data_selected = 'all';
			if(isset($post['integration_data_selected']) && $post['integration_data_selected'] != '') $integration_data_selected = $post['integration_data_selected'];

			$newHTML = "<div class='p-3'>
			<select name='integration-chart-type' id='integration-chart-type' class='form-control show-tabs select2-input'>
			<option value='all' data-id='all' data-website='all'>All</option>";
			$index = 0;
			foreach ($totals['integration']['all'] as $website => $value) {
				$k = base64_encode($website); 
				$newHTML .= "<option ". ( $integration_data_selected == $k ? 'selected' : '' ) ." value='". $k ."' data-id='". ++$index ."' data-website='". $website ."' >". $website ."</option>";
			}
			$newHTML .= "</select>
			</div>
			<div class='tab-content'>
			{$html}
			</div>";


			$json['html'] = $newHTML;


			$type = isset($post['integration_data_website_selected']) && $post['integration_data_website_selected'] != '' ?  $post['integration_data_website_selected'] : 'all';

			if($type == 'all'){
				$data = array(
					'balance'				=>	(float)$totals['integration']['balance'],
					'total_orders_amount'	=>	(float)$totals['integration']['total_orders_amount'],
					'sale'					=>	(float)$totals['integration']['sale'],
					'click_count'			=>	(float)$totals['integration']['click_count'],
					'click_amount'			=>	(float)$totals['integration']['click_amount'],
					'action_count'			=>	(float)$totals['integration']['action_count'],
					'action_amount'			=>	(float)$totals['integration']['action_amount'],
					'total_commission'		=>	(float)$totals['integration']['total_commission'],
					'total_orders'			=>	(float)$totals['integration']['total_orders'],
				);
			}else{
				$integration = $totals['integration']['all'];
				if(isset($integration[$type])){
					$data = array(
						'balance'				=>	isset($integration[$type]['balance']) ? (float)$integration[$type]['balance'] : 0,
						'total_orders_amount'	=>	isset($integration[$type]['total_orders_amount']) ? (float)$integration[$type]['total_orders_amount'] : 0,
						'sale'					=>	isset($integration[$type]['sale']) ? (float)$integration[$type]['sale'] : 0,
						'click_count'			=>	isset($integration[$type]['click_count']) ? (float)$integration[$type]['click_count'] : 0,
						'click_amount'			=>	isset($integration[$type]['click_amount']) ? (float)$integration[$type]['click_amount'] : 0,
						'action_count'			=>	isset($integration[$type]['action_count']) ? (float)$integration[$type]['action_count'] : 0,
						'action_amount'			=>	isset($integration[$type]['action_amount']) ? (float)$integration[$type]['action_amount'] : 0,
						'total_commission'		=>	isset($integration[$type]['total_commission']) ? (float)$integration[$type]['total_commission'] : 0,
						'total_orders'			=>	isset($integration[$type]['total_orders']) ? (float)$integration[$type]['total_orders'] : 0,
					);
				}
			}

			$json['chart'][$post['integration_data_year']] = $data;
		}else{
			$json['html'] = false;
		}



		if($return) return $json;
		echo json_encode($json);die;
	}
	public function logs(){
		$data = array();
		$userdetails = $this->userdetails();
		if(empty($userdetails)){ redirect('/login'); }
		$input = $this->input->post(null,true);
		

		$filter = array(
			'user_id' => $userdetails['id'],
		);

		$data['status'] = $this->Wallet_model->status();
		$data['status_icon'] = $this->Wallet_model->status_icon;
		if($input['type'] == 'sale'){
			$data['title'] = "Sales Logs";

			$filter['type_in'] = "'sale_commission','vendor_sale_commission'";
			$data['data'] = $this->Wallet_model->getTransaction($filter);
		}
		else if($input['type'] == 'hold_orders'){
			$data['title'] = "Hold Orders Logs";

			$filter['type'] = "sale_commission";
			$filter['status'] = 0;
			
			$data['data'] = $this->Wallet_model->getTransaction($filter);
		}
		else if($input['type'] == 'click'){
			$data['title'] = "Clicks Logs";
			$filter['click_log'] = 1;
			$data['data'] = $this->Wallet_model->getTransaction($filter);

			$data['title2'] = "Clicks Logs";
			$record = array();

			$where = ' AND user_id = '. $userdetails['id'];

			$record[] = $this->db->query('SELECT country_code,created_at,ip  as user_ip,commission as pay_commition,"Integration Click" as type FROM integration_clicks_action WHERE is_action=0'.$where)->result_array();
			$record[] = $this->db->query('SELECT country_code,created_at,user_ip,pay_commition,"Product Click" as type  FROM product_action WHERE  1'.$where)->result_array();
			$record[] = $this->db->query('SELECT country_code,created_at,user_ip,pay_commition,"Form Click" as type  FROM form_action WHERE 1'.$where)->result_array();
			$record[] = $this->db->query('SELECT country_code,created_at,user_ip,commission as pay_commition,"Affiliate Click" as type FROM affiliate_action WHERE 1'.$where)->result_array();

			$record[] = $this->db->query('SELECT pa.country_code,pa.created_at,pa.user_ip,pa.pay_commition,"Store Product Click (Other Affiliate)" as type  FROM product_action pa LEFT JOIN product_affiliate paff ON (paff.product_id = pa.product_id)  WHERE paff.user_id=  '. (int)$userdetails['id'] .' ')->result_array();


			$_record = array();
			foreach ($record as $key => $re) {
				foreach ($re as $_key => $value) {
					$_record[] = array(
						'created_at' => $value['created_at'],
						'comment' => 'Click from ip_message ',
						'status' => $value['type'],
						'country_code' => $value['country_code'],
						'user_ip' => $value['user_ip'],
					);
				}
			}

			usort($_record, array('Admincontrol', 'date_compare') ); 
			$data['data2'] = $_record;
		}
		else if($input['type'] == 'orders'){
			$order_status = $this->Order_model->status();
			$data['title'] = "Digital Orders";
			$record = $this->db->query('SELECT o.* FROM `order_products` op LEFT JOIN `order` as o ON o.id = op.order_id WHERE o.status > 0 AND op.refer_id='. (int)$userdetails['id'])->result_array();

			$_record = array();
			foreach ($record as $_key => $value) {
				$_record[] = array(
					'created_at'   => $value['created_at'],
					'comment'      => 'Order from ip_message ',
					'status'       => $order_status[$value['status']],
					'country_code' => $value['country_code'],
					'user_ip'      => $value['ip'],
					'amount'       => $value['total'],
				);
			}

			$data['data'] = $_record;

		}
		else if($input['type'] == 'ex_orders'){
			$data['title'] = "External Orders";
			$record = $this->db->query('SELECT * FROM `integration_orders` WHERE user_id='. (int)$userdetails['id'])->result_array();
			
			$_record = array();
			foreach ($record as $_key => $value) {
				$_record[] = array(
					'created_at'   => $value['created_at'],
					'comment'      => 'Order from ip_message ',
					'status'       => 'Complete',
					'country_code' => $value['country_code'],
					'user_ip'      => $value['ip'],
					'amount'       => $value['total'],
				);
			}

			$data['data'] = $_record;

		}
		else if($input['type'] == 'action'){
			$data['title'] = "Actions Logs";
			

			$filter['type'] = "external_click_commission";
			$filter['is_action'] = 1;
			$data['data'] = $this->Wallet_model->getTransaction($filter);
		}
		else if($input['type'] == 'hold_actions'){
			$data['title'] = "Hold Action Logs";
			

			$filter['type'] = "external_click_commission";
			$filter['is_action'] = 1;
			$filter['status'] = 0;
			$data['data'] = $this->Wallet_model->getTransaction($filter);
		}
		else if($input['type'] == 'vendor_click'){
			$data['title'] = "Clicks Logs";
			$data['data'] = $this->Wallet_model->getVendorClick($userdetails['id']);
		}
		

		$data['html'] = $this->load->view("common/log_model",$data,true);

		echo json_encode($data);die;
	}
	public function logout(){
		$this->session->unset_userdata('user');
		$this->session->unset_userdata('client');
		redirect('/login');
	}
	public function deleteUser($id){
		$data['users'] = $this->admin_model->deleteUser($id);
		$this->session->set_flashdata('success', __('user.user_deleted_successfullly'));
		redirect('usercontrol/manageUsers');
	}
	public function addComission(){
		$post = $this->input->post(null,true);
		if(isset($post) && !empty($post)){
			$this->form_validation->set_rules('buyid', 'BuyId', 'required|trim', array('required' => '%s is required'));
			$this->form_validation->set_rules('amount', 'Amount', 'required|trim', array('required' => '%s is required.')
		);
			$this->form_validation->set_rules('qty', 'Qty', 'required|trim', array('required' => '%s is required.')
		);
			

			if ($this->form_validation->run() == FALSE) {
				$data['validate_err'] = validation_errors();
			} else {
				$db = new MY_Controller();
				$userdetails=$db->userdetails();
				$kirim = array('RefiD'=>$userdetails['refid'],'buyiD'=>$post['buyid'],'userID'=>$userdetails['id'],'worlbit_qty'=>$post['qty'],'Amount'=>$post['amount']);
				

				$res = $this->commisioninfo->set_commission($kirim);
				$this->session->set_flashdata(array('flash' => array('success' => __('user.comission_added_successfully!'))));
				redirect('usercontrol/addComission', 'refresh');
			}
		}
		$data['title'] = 'Add Comission';
		$this->load->view('usercontrol/includes/header', $data);
		$this->load->view('usercontrol/dashboard/addComission', $data);
		$this->load->view('usercontrol/includes/footer', $data);
	}
	public function addUser(){
		$post = $this->input->post(null,true);
		if(isset($post) && !empty($post)){
			$this->form_validation->set_rules('firstname', __('user.first_name'), 'required|trim', array('required' => '%s is required'));
			$this->form_validation->set_rules('lastname', __('user.last_name'), 'required|trim', array('required' => '%s is required.'));
			$this->form_validation->set_rules('username', __('user.username'), 'required|trim|is_unique[users.username]', array('required' => '%s is required'));
			$this->form_validation->set_rules('email', __('user.email'), 'required|trim', array('required' => '%s is required'));
			$this->form_validation->set_rules('password', __('user.password'), 'required|trim', array('required' => '%s is required'));
			$this->form_validation->set_rules('conf_password', __('user.confirm_password'), 'required|trim|matches[password]', array('required' => '%s is required'));
			

			if ($this->form_validation->run() == FALSE) {
				$data['validate_err'] = validation_errors();
			} else {
				

				$res = array('firstname'=>$post['firstname'],'lastname'=>$post['lastname'],'email'=>$post['email'],'username'=>$post['username'],'password'=>sha1($post['password']),'updated_at'=>date('Y-m-d H:i:s'));
				

				$this->db->insert('users',$res);
				$this->session->set_flashdata(array('flash' => array('success' => __('user.user_added_successfully'))));
				redirect('usercontrol/manageUsers', 'refresh');
			}
		}
		$data['title'] = 'Add User';
		$this->load->view('usercontrol/includes/header', $data);
		$this->load->view('usercontrol/dashboard/addUser', $data);
		$this->load->view('usercontrol/includes/footer', $data);

	}
	public function editUser($id){
		$post = $this->input->post(null,true);
		if(isset($post['id']) && !empty($post['id'])){
			$res = array('firstname'=>$post['firstname'],'lastname'=>$post['lastname'],'updated_at'=>date('Y-m-d H:i:s'));
			$this->db->where('id',$post['id']);
			$this->db->update('users',$res);
			$this->session->set_flashdata(array('flash' => array('success' => __('user.user_profile_updated_successfully'))));
			redirect('usercontrol/manageUsers', 'refresh');
		}
		

		

		$data['users'] = $this->admin_model->getUsers($id);
		$data['title'] = 'Edit User';
		$this->load->view('usercontrol/includes/header', $data);
		$this->load->view('usercontrol/dashboard/edit-user', $data);
		$this->load->view('usercontrol/includes/footer', $data);

	}
	public function messages(){
		$data['title'] = 'Message';
		$data['users'] = $this->admin_model->getUsers($id=null);
		$this->load->view('usercontrol/includes/header', $data);
		$this->load->view('usercontrol/dashboard/message', $data);
		$this->load->view('usercontrol/includes/footer', $data);

	}
	public function chatmessage(){
		$this->load->helper('smiley');
		$data['title'] = 'Message';
		$data['users'] = $this->admin_model->getUsers($id=null);
		$this->load->view('usercontrol/includes/header', $data);
		$this->load->view('chat', $data);
		$this->load->view('usercontrol/includes/footer', $data); 	

	}
	public function google_login(){
		$get = $this->input->get(null,true);
		$clientId = '163214076002-9o582d2urnpc10nebsd032sgadhcgvmf.apps.googleusercontent.com'; //Google client ID
		$clientSecret = 'Ent8s37alsTYf6Ai8Z7y0Z6l'; //Google client secret
		$redirectURL = base_url() . 'admin/google_login/';
		

		//Call Google API
		$gClient = new Google_Client();
		$gClient->setApplicationName('Login');
		$gClient->setClientId($clientId);
		$gClient->setClientSecret($clientSecret);
		$gClient->setRedirectUri($redirectURL);
		$google_oauthV2 = new Google_Oauth2Service($gClient);
		

		if(isset($get['code']))
		{
			$gClient->authenticate($get['code']);
			$_SESSION['token'] = $gClient->getAccessToken();
			header('Location: ' . filter_var($redirectURL, FILTER_SANITIZE_URL));
		}
		

		if (isset($_SESSION['token']))
		{
			$gClient->setAccessToken($_SESSION['token']);
		}
		

		if ($gClient->getAccessToken()) {
			$userProfile = $google_oauthV2->userinfo->get();
			echo "<pre>";
			print_r($userProfile);
			die;
		}
		else
		{
			$url = $gClient->createAuthUrl();
			header("Location: $url");
			exit;
		}
	}
	public function store_orders($page = 1){
		$userdetails = $this->userdetails();
 
		if(empty($userdetails)){ redirect('/login'); }
		$data['status'] = $this->Order_model->status();

		unset($data['status']['0']);

		if ($this->input->server('REQUEST_METHOD') == 'POST'){
			$page = max((int)$page,1);
			$post = $this->input->post(null,true);
			$filter = array(
				'limit' => 100,
				'page' => $page,
				'user_id' => $userdetails['id'],
				'is_vendor' => $userdetails['is_vendor'],
				'o_status_gt' => 1,
			);

			if(isset($post['filter_status']) && $post['filter_status'] != ''){
				$filter['o_status'] = $this->input->post('filter_status',true);
			}

			list($data['orders'],$total) = $this->Order_model->getAllOrders($filter);

			$data['start_from'] = (($page-1) * $filter['limit'])+1;
			$data['wallet_status'] = $this->Wallet_model->status();
			$json['html'] = $this->load->view("usercontrol/store/order_list.php",$data,true);

			$this->load->library('pagination');
			$config['base_url'] = base_url('usercontrol/store_orders/');
			$config['per_page'] = $filter['limit'];
			$config['total_rows'] = $total;
			$config['use_page_numbers'] = TRUE;
			$config['enable_query_strings'] = TRUE;
			$this->pagination->initialize($config);

			$json['pagination'] = $this->pagination->create_links();

			echo json_encode($json);die;
		}
		
		$this->load->view('usercontrol/includes/header', $data);
		$this->load->view('usercontrol/store/orders', $data);
		$this->load->view('usercontrol/includes/footer', $data);
	}

	public function store_venodr_orders($page = 1){
		$userdetails = $this->userdetails();
		if(empty($userdetails)){ redirect('/login'); }
		$vendor_setting = $this->Product_model->getSettings('vendor');
		$store_setting = $this->Product_model->getSettings('store');
		if(!isset($userdetails['is_vendor']) || $userdetails['is_vendor'] != 1 || (int)$vendor_setting['storestatus'] != 1 || (int)$store_setting['status'] != 1) redirect('usercontrol/dashboard');

		$data['status'] = $this->Order_model->status();
		$data['myorder'] = 1;

		unset($data['status']['0']);


		if ($this->input->server('REQUEST_METHOD') == 'POST')
		{

			$page = max((int)$page,1);
			$post = $this->input->post(null,true);
			$filter = array(
				'limit' => 100,
				'page' => $page,
				'user_id' => $userdetails['id'],
				'o_status_gt' => 1,
				'myorder'=>1
			);
 

			if(isset($post['filter_status']) && $post['filter_status'] != ''){
				$filter['o_status'] = $this->input->post('filter_status',true);
			}

			list($data['orders'],$total) = $this->Order_model->getAllOrders($filter);

			$data['start_from'] = (($page-1) * $filter['limit'])+1;
			$data['wallet_status'] = $this->Wallet_model->status();
			$json['html'] = $this->load->view("usercontrol/store/order_list.php",$data,true);

			$this->load->library('pagination');
			$config['base_url'] = base_url('usercontrol/store_venodr_orders/');
			$config['per_page'] = $filter['limit'];
			$config['total_rows'] = $total;
			$config['use_page_numbers'] = TRUE;
			$config['enable_query_strings'] = TRUE;
			$this->pagination->initialize($config);

			$json['pagination'] = $this->pagination->create_links();

			echo json_encode($json);die;
		}
		
		$this->load->view('usercontrol/includes/header', $data);
		$this->load->view('usercontrol/store/vendor-orders', $data);
		$this->load->view('usercontrol/includes/footer', $data);
	}

	public function external_vendor_orders($page = 1){
		$userdetails = $this->userdetails();
		if(empty($userdetails)){ redirect('/login'); }
		$vendor_setting = $this->Product_model->getSettings('vendor');
		$store_setting = $this->Product_model->getSettings('store');
		if(!isset($userdetails['is_vendor']) || $userdetails['is_vendor'] != 1 || (int)$vendor_setting['storestatus'] != 1 || (int)$store_setting['status'] != 1) redirect('usercontrol/dashboard');

		$data['status'] = $this->Order_model->status();
		$data['myorder'] = 1;

		unset($data['status']['0']);

		if ($this->input->server('REQUEST_METHOD') == 'POST')
		{
			$page = max((int)$page,1);
			$post = $this->input->post(null,true);
			$filter = array(
				'limit' => 100,
				'page' => $page,
				'user_id' => $userdetails['id'],
				'o_status_gt' => 1,
				'external_orders'=>1
			);
 
			if(isset($post['filter_status']) && $post['filter_status'] != ''){
				$filter['o_status'] = $this->input->post('filter_status',true);
			}

			list($data['orders'],$total) = $this->Order_model->getAllOrders($filter);

			$data['start_from'] = (($page-1) * $filter['limit'])+1;
			$data['wallet_status'] = $this->Wallet_model->status();
			$json['html'] = $this->load->view("usercontrol/store/order_list.php",$data,true);

			$this->load->library('pagination');
			$config['base_url'] = base_url('usercontrol/external_vendor_orders/');
			$config['per_page'] = $filter['limit'];
			$config['total_rows'] = $total;
			$config['use_page_numbers'] = TRUE;
			$config['enable_query_strings'] = TRUE;
			$this->pagination->initialize($config);

			$json['pagination'] = $this->pagination->create_links();

			echo json_encode($json);die;
		}
		
		$this->load->view('usercontrol/includes/header', $data);
		$this->load->view('usercontrol/store/vendor-external-orders', $data);
		$this->load->view('usercontrol/includes/footer', $data);
	}


	public function store_logs($page = 0){

		$userdetails = $this->userdetails();
		if(empty($userdetails)){ redirect('/login'); }

		if ($this->input->server('REQUEST_METHOD') == 'POST'){
			$page = max((int)$page,1);
			

			$filter = array(
				'limit'   => 100,
				'page'    => $page,
				'user_id' => $userdetails['id'],
			);
			$data['userdetails'] = $userdetails;

			list($data['clicks'],$total) = $this->Order_model->getAllClickLogs($filter);
			$data['start_from'] = (($page-1) * $filter['limit'])+1;

			$json['html'] = $this->load->view("usercontrol/store/log_list.php",$data,true);

			$this->load->library('pagination');
			$config['base_url'] = base_url('usercontrol/store_logs/');
			$config['per_page'] = $filter['limit'];
			$config['total_rows'] = $total;
			$config['use_page_numbers'] = TRUE;
			$config['enable_query_strings'] = TRUE;
			$this->pagination->initialize($config);


			$json['pagination'] = $this->pagination->create_links();
			echo json_encode($json);die;
		}
		
		$this->load->view('usercontrol/includes/header', $data);
		$this->load->view('usercontrol/store/logs', $data);
		$this->load->view('usercontrol/includes/footer', $data);
	}

	public function store_markettools(){
		$userdetails = $this->userdetails();
		
		if(empty($userdetails)) redirect('login');

		$market_vendor = $this->Product_model->getSettings('market_vendor');

		if(! allowMarketVendorPanelSections($market_vendor['marketvendorpanelmode'], $userdetails['is_vendor'])) {
			redirect('login');
		}

		$restricted_vendors = $this->get_restricted_vendors();
		
		$this->load->model('Form_model');
		$this->load->model('Report_model');
		$this->load->model('Wallet_model');
		$this->load->model('IntegrationModel');


		if ($this->input->server('REQUEST_METHOD') == 'POST')
		{
			$escapevendors = $this->db->query('SELECT user_id,vendor_shares_sales_status,vendor_status FROM vendor_setting ')->result_array();

			$userrefid=$userdetails['refid']; 
			$allVendors = $this->db->query('SELECT id FROM users WHERE is_vendor=1')->result_array();
			$allowVendors = [];
			foreach($escapevendors as $esc) 
			{
				if($esc['vendor_shares_sales_status']==1)
					$allowVendors[] = $esc['user_id'];
				else if($esc['vendor_shares_sales_status']==2 && $esc['user_id']==$userrefid)
					$allowVendors[] = $esc['user_id'];
				else if($esc['vendor_status'] !=0 )
					$allowVendors[] = $esc['user_id'];

			}
 			
			$escapeUsers = [];
			foreach($allVendors as $v) {
				if(!in_array($v['id'], $allowVendors)){
					
					$escapeUsers[] = $v['id'];
				}
			}

  			$restricted_vendors=array_unique(array_merge($restricted_vendors, $escapeUsers));

			$post = $this->input->post(null,true);
			$get = $this->input->get(null,true);
			$json['form_default_commission'] = $this->Product_model->getSettings('formsetting');
			$json['default_commition']       = $this->Product_model->getSettings('productsetting');

			$filter = [
				'user_id'          => $userdetails['id'],
				'status'           => 1,
				'redirectLocation' => 1,
				'restrict'         => $userdetails['id'],
				'restrict_vendors' => $restricted_vendors,
				'not_show_my'	   => $userdetails['id'],
				'userdetails' => $userdetails
			];


			if (isset($post['category_id'])) {
				$filter['category_id'] = $post['category_id'];
			}
			if (isset($post['ads_name'])) {
				$filter['ads_name'] = $post['ads_name'];
			}

			if (isset($post['ads_name'])) {
				$filter['ads_name'] = $post['ads_name'];
			}

  			 //new code... 
			 if($userdetails['is_vendor']==1)  // for vend
			 {	
				 if ($market_vendor['marketvendorstatus'] == 0) {
					$filter['marketvendorstatus'] = 1;
					if (isset($post['vendor_id'])) {
						$filter['vendor_id'] = $post['vendor_id'];
					}
				 }else{
				 	$filter['marketvendorstatus'] = 0;
				 }
			}
			else
			{
				// for aff
				if ($market_vendor['marketvendorstatus'] == 0) {
					$filter['marketvendorstatus'] = 0;
					if (isset($post['vendor_id'])) {
						$filter['vendor_id'] = $post['vendor_id'];
					}
				 }else{
				 	$filter['marketvendorstatus'] = 1;
				 }
			}

			$filter["is_vendor"]=$userdetails['is_vendor'];

			//filter for campaign
			$filtertools=$filter;
			if (isset($post['vendor_id'])) 
			{
				$filtertools['vendor_id'] = $post['vendor_id'];
			}
			
			$json['tools'] = $this->IntegrationModel->getProgramTools($filtertools);

//reject the marketTools banners on user banners page when external mode is disable
$market_tools_status = $this->Product_model->getSettings('market_tools', 'status');
if ($market_tools_status === null || !isset($market_tools_status['status'])) {
    $market_tools_status['status'] = 1;
}
if (!$market_tools_status['status']) {
    $json['tools'] = [];
}
//reject the marketTools banners on user banners page when external mode is disable

			
			$userrefid=$userdetails['refid']; 

			$allVendors = $this->db->query('SELECT id FROM users WHERE is_vendor=1')->result_array();
  
			$allowVendors = [];
			foreach($escapevendors as $esc) 
			{
				if($esc['vendor_status']==1)
					$allowVendors[] = $esc['user_id'];
				else if($esc['vendor_status']==2 && $esc['user_id']==$userrefid)
					$allowVendors[] = $esc['user_id'];

			}
 
			$escapeUsers = [];
			foreach($allVendors as $v) {
				if(!in_array($v['id'], $allowVendors))
					$escapeUsers[] = $v['id'];
			}
 	
			$products = [];
			
			$filter = ['product_status' => 1,'is_campaign_product'=> 0, 'restrict_vendors' => array_unique(array_merge($restricted_vendors, $escapeUsers))];
			
			if (isset($post['market_category_id'])) {
				$filter['category_id'] = $post['market_category_id'];
			}
			
			if (isset($post['ads_name'])) {
				$filter['ads_name'] = $post['ads_name'];
			}

			if (isset($post['vendor_id'])) {
				$filter['vendor_id'] = $post['vendor_id'];
			}

			$filter['not_show_my'] = $userdetails['id'];

			$store_setting = $this->Product_model->getSettings('store', 'store_mode');

			$filter['is_campaign_product'] = $store_setting['store_mode'] == 'sales' ? 1 : 0;

			if($store_setting['status']){ 
				$products = $this->Product_model->getAllProduct($userdetails['id'], $userdetails['type'], $filter);
			}

			$filter['show_to_affiliates'] = 1;
			$productsCampaign = $this->Product_model->getAllProduct($userdetails['id'], $userdetails['type'], $filter);
			
			$products = array_unique(array_merge($products, $productsCampaign),SORT_REGULAR);

			foreach ($products as $key => $value) {
				$slug_query = $this->db->query("SELECT slug FROM slugs WHERE type = 'product' AND related_id = '".(int)$value['product_id']."' AND user_id = '".(int)$userdetails['id']."'")->row();
				$products[$key]['slug'] = $slug_query ? $slug_query->slug : '';
				$products[$key]['is_product'] = 1;
			}

			$filterform = [];

			if (isset($post['ads_name']) && !empty($post['ads_name'])) {
				$filterform['ads_name'] = $post['ads_name'];
			}
 
			if (isset($post['vendor_id'])) {
				$filterform['vendor_id'] = $post['vendor_id'];
			}

			$forms = $store_setting['store_mode'] != 'sales' ? $this->Form_model->getForms($userdetails['id'], $filterform) : [];


//reject the store products and forms on user banners page when store is disable
$store_status = $this->Product_model->getSettings('store', 'status');
if ($store_status === null || !isset($store_status['status'])) {
    $store_status['status'] = 1;
}
if (!$store_status['status']) {
    $forms = [];
}
if (!$store_status['status']) {
    $products = [];
}
//reject the store products and forms on user banners page when store is disable

 			 
			foreach ($forms as $key => $value) {
				$slug_query = $this->db->query("SELECT slug FROM slugs WHERE type = 'form' AND related_id = '".(int)$value['form_id']."' AND user_id = '".(int)$userdetails['id']."'")->row();
				$forms[$key]['slug'] = $slug_query ? $slug_query->slug : '';
				$forms[$key]['coupon_name']          = $this->Form_model->getFormCouponname(($value['coupon']) ? $value['coupon'] : 0);
				$forms[$key]['public_page']          = base_url('form/'.$value['seo'].'/'.base64_encode($this->userdetails()['id']));
				$forms[$key]['count_coupon']         = $this->Form_model->getFormCouponCount($value['form_id'],$this->userdetails()['id']);
				$forms[$key]['seo']                  = str_replace('_', ' ', $value['seo']);
				$forms[$key]['is_form']              = 1;
				$forms[$key]['product_created_date'] = $value['created_at'];
				$forms[$key]['fevi_icon'] = $value['fevi_icon'] ? 'assets/images/form/favi/'.$value['fevi_icon'] : 'assets/images/no_image_available.png';

				if($value['coupon']){
					$forms[$key]['coupon_code'] = $this->Form_model->getFormCouponCode($value['coupon']);
				}
			}
 			 
			$data_list = array_merge($products,$forms,$json['tools']);

			usort($data_list,function($a,$b){
				$ad = isset($a['product_created_date']) ? strtotime($a['product_created_date']) : strtotime($a['created_at']);
				$bd = isset($b['product_created_date']) ? strtotime($b['product_created_date']) : strtotime($b['created_at']);;
				return ($ad-$bd);
			});		
			
			$json['data_list'] = $data_slice=array_reverse($data_list);

			$award_level = $this->Product_model->getSettings('award_level', 'status');
			$json['award_level_status'] = $award_level['status'];

			$comission_sale_status = 0;
			$sale_comission_rate = 0;
			$userPlan = App\MembershipUser::select('membership_plans.commission_sale_status','award_level.sale_comission_rate')->join('membership_plans','membership_plans.id','=','membership_user.plan_id')->join('award_level','award_level.id','=','membership_plans.level_id','left')->where('is_active',1)->where('user_id',$userdetails['id'])->first();
			if($userPlan->commission_sale_status){
				$comission_sale_status = $userPlan->commission_sale_status;
				$sale_comission_rate = $userPlan->sale_comission_rate;
			} else {
				$userLevel = $this->Product_model->getByField('award_level','id',$userdetails['level_id']);
				if($userLevel){
					$comission_sale_status = 1;
					$sale_comission_rate = $userLevel['sale_comission_rate'];
				}
			}
			$json['userComission']['status'] = $comission_sale_status;
			$json['userComission']['value'] = $sale_comission_rate;

			/*=============== Pagination======================*/
			
			$perpage = 25;
			$data['page'] = $post['page'];
			$data['data_list'] = array_slice($data_slice,($data['page']-1)*$perpage,$perpage);
			$json['data_list'] = array_slice($data_slice,($data['page']-1)*$perpage,$perpage);
			
			$this->load->library('pagination');
			
			$config['base_url'] = base_url('usercontrol/store_markettools');
			$config['cur_page'] = $data['page'];
			$config['per_page'] = $perpage;
			$config['total_rows'] = count($data_slice);
			$config['use_page_numbers'] = TRUE;
			

			$config['full_tag_open'] = '<nav aria-label="Page navigation example"><ul class="pagination">';        
		    $config['full_tag_close'] = '</ul></nav>';        
		    $config['first_link'] = 'First';        
		    $config['last_link'] = 'Last';        
		    $config['first_tag_open'] = '<li class="page-item"><span class="page-link">';        
		    $config['first_tag_close'] = '</span></li>';        
		    $config['prev_link'] = '&laquo';        
		    $config['prev_tag_open'] = '<li class="page-item"><span class="page-link">';        
		    $config['prev_tag_close'] = '</span></li>';        
		    $config['next_link'] = '&raquo';        
		    $config['next_tag_open'] = '<li class="page-item"><span class="page-link">';        
		    $config['next_tag_close'] = '</span></li>';        
		    $config['last_tag_open'] = '<li class="page-item"><span class="page-link">';        
		    $config['last_tag_close'] = '</span></li>';        
		    $config['cur_tag_open'] = '<li class="page-item active"><a class="page-link" href="#">';        
		    $config['cur_tag_close'] = '</a></li>';        
		    $config['num_tag_open'] = '<li class="page-item"><span class="page-link">';        
		    $config['num_tag_close'] = '</span></li>';

			$this->pagination->initialize($config);
			$json['pagination'] = $this->pagination->create_links();

			$json['view'] = $this->load->view("usercontrol/store/markettools_list",$json,true);

			echo json_encode($json);die;
		}

		if(isset($restricted_vendors) && count($restricted_vendors)>0) 
		{
			$restricted_vendors_str=implode(",",$restricted_vendors);
 			$data['vendors_list'] = $this->db->query("SELECT id,username FROM users WHERE type = 'user' AND is_vendor=1 AND id!=".$userdetails['id'] . " and id not in ($restricted_vendors_str) ")->result_array();
		}
 		else
 		{
 			$data['vendors_list'] = $this->db->query("SELECT id,username FROM users WHERE type = 'user' AND is_vendor=1 AND id!=".$userdetails['id'])->result_array();
 		}


		$data['categories'] = $this->db->query("SELECT DISTINCT integration_category.id  as value ,integration_category.name as label, CASE WHEN integration_category.parent_id=0 THEN integration_category.id ELSE integration_category.parent_id END AS pid FROM `integration_category`
			 inner JOIN integration_tools on integration_tools.category=	 integration_category.id 
		 order by pid,integration_category.id")->result_array();
		
		$data['store_categories'] = $this->db->query("SELECT id as value,name as label FROM categories WHERE 1")->result_array();

		$this->load->library("socialshare");	

		
		$data['social_share_modal'] =  $this->socialshare->get_dynamic_social_share_btns();


		$this->view($data, 'store/markettools', 'usercontrol');
	}

	public function listproduct_ajax($page = 1){
		$userdetails = $this->userdetails();
		if(empty($userdetails)){ redirect('/'); }
		$store_setting =$this->Product_model->getSettings('store');
		if(!$store_setting['status']){redirect('/usercontrol/dashboard');}

		$get = $this->input->get(null,true);
		$post = $this->input->post(null,true);
		$filter = array(
			'page' => isset($get['page']) ? $get['page'] : $page,
			'limit' => 20,
			'only_admin_product' => 1,
		);

		$record = $this->Product_model->getAllProduct($userdetails['id'], $userdetails['type'], $filter);
		$data['productlist'] = $record['data'];
		$data['store_setting'] = $store_setting;
		$data['default_commition'] =$this->Product_model->getSettings('productsetting');

		$json['view'] = $this->load->view("usercontrol/product/product_list", $data, true);

		$this->load->library('pagination');
		$this->pagination->cur_page = $filter['page'];

		$config['base_url'] = base_url('usercontrol/listproduct_ajax');
		$config['per_page'] = $filter['limit'];
		$config['total_rows'] = $record['total'];
		$config['use_page_numbers'] = TRUE;
		$config['page_query_string'] = TRUE;
		$config['enable_query_strings'] = TRUE;
		$_GET['page'] = $filter['page'];
		$config['query_string_segment'] = 'page';
		$this->pagination->initialize($config);
		$json['pagination'] = $this->pagination->create_links();

		echo json_encode($json);

	}
	public function listproduct(){
		$userdetails = $this->userdetails();
		

		if(empty($userdetails)){ redirect('/login'); }

		$store_setting =$this->Product_model->getSettings('store');
		if(!$store_setting['status']){
			redirect('/usercontrol/dashboard');		

		}
		$this->load->model('Form_model');

		$data['totals'] = $this->Wallet_model->getTotals(array('user_id' => $userdetails['id']), true);

		$data['ordercount'] =$this->db->query('SELECT COUNT(op.id) as total FROM `order_products` op LEFT JOIN `order` as o ON o.id = op.order_id WHERE o.status > 0 AND op.`refer_id` = '. (int)$userdetails['id'] )->row()->total;

		$data['user'] = $userdetails;
		$this->load->view('usercontrol/includes/header', $data);
		$this->load->view('usercontrol/product/index', $data);
		$this->load->view('usercontrol/includes/footer', $data);
	}
	public function managereferenceusers(){redirect('usercontrol/my_network');}
	
	public function my_network(){

		$userdetails = $this->userdetails();
		if(empty($userdetails)){ redirect('/login'); }

		$referlevelSettings = $this->Product_model->getSettings('referlevel');
		$disabled_for = json_decode( (isset($referlevelSettings['disabled_for']) ? $referlevelSettings['disabled_for'] : '[]'),1);
		$refer_status = true;

		if((int)$referlevelSettings['status'] == 0){ show_404(); }
		else if((int)$referlevelSettings['status'] == 2 && in_array($userdetails['id'], $disabled_for)){ $refer_status = false; }


		$userlist = $this->Product_model->getAllUsersTreeV3(array(),$userdetails['id']);


		$site_setting = $this->Product_model->getSettings('site');
		$referlevel_setting = $this->Product_model->getSettings('referlevel');
		
		if($referlevel_setting['show_sponser'] == 'none'){
			$data['userslist'] = $userlist;
		}
		else if($referlevel_setting['show_sponser'] == 'real_sponser'){

			$userdetails['refid'] = $userdetails['refid'] > 0 ? $userdetails['refid'] : 1;

			$admin_result= $this->db->query("SELECT id,username as name,avatar,refid FROM users WHERE  id= " . (int)$userdetails['refid'])->row_array();

			if($admin_result){
				$_children = [];
				$_children[] = array(
					'name'  => $admin_result['name'] ."<img class='user-avtar-tree' src='". $this->Product_model->getAvatar($admin_result['avatar']) ."'>",
					'children' => $userlist,
				);

				$data['userslist'] = $_children;
			}

			if(!isset($data['userslist'])){
				$data['userslist'] = $userlist;
			}
			
		} else{
			$admin_result= $this->db->query("SELECT id,username as name,avatar,refid FROM users WHERE type='admin'")->row_array();

			
			$_children = [];
			$_children[] = array(
				'name'  => ($referlevel_setting['sponser_name'] ? $referlevel_setting['sponser_name'] : $admin_result['name']) ."<img class='user-avtar-tree' src='". $this->Product_model->getAvatar($admin_result['avatar']) ."'>",
				'children' => $userlist,
			);
			
			$data['userslist'] = $_children;
		}

		$data['refer_total'] = $this->Product_model->getReferalTotals($userdetails['id']);
		

		$this->load->view('usercontrol/includes/header', $data);
		$this->load->view('usercontrol/users/my_network', $data);
		$this->load->view('usercontrol/includes/footer', $data);
	}
	public function addpayment($id = null){
		$userdetails = $this->userdetails();
		if(empty($userdetails)){ redirect('/login'); }
		$post = $this->input->post(null,true);

		if (isset($post['add_paypal'])) {
			$email = $this->input->post('paypal_email',true);
			if ((int)$post['id'] > 0) {
				$this->db->update("paypal_accounts", array(
					'paypal_email' => $email,
					'user_id' => $userdetails['id'],
				),
				array(
					'id' => $post['id']
				));
			}
			else
			{
				$this->db->insert("paypal_accounts", array(
					'paypal_email' => $email,
					'user_id' => $userdetails['id'],
				));
			}
			$this->session->set_flashdata('success', __('user.paypal_account_saved_successfully'));
			redirect('usercontrol/mywallet/#tab-paymentdetails');
		} else if(!empty($post)){
			$this->load->helper(array('form', 'url'));
			

			$this->load->library('form_validation');
			

			$this->form_validation->set_rules('payment_account_number', __('user.account_number'), 'required');
			$this->form_validation->set_rules('payment_account_name', __('user.account_name'), 'required' );
			$this->form_validation->set_rules('payment_ifsc_code', __('user.ifsc_code'), 'required');
			if($this->form_validation->run())
			{
				$errors= array();
				

				$details = array(
					'payment_bank_name'      =>  $this->input->post('payment_bank_name',true),
					'payment_account_number' =>  $this->input->post('payment_account_number',true),
					'payment_account_name'   =>  $this->input->post('payment_account_name',true),
					'payment_ifsc_code'      =>  $this->input->post('payment_ifsc_code',true),
					'payment_status'         =>  1,
					'payment_ipaddress'      =>  $_SERVER['REMOTE_ADDR'],
				);
				if(empty($errors)){
					
					if( (int)$post['payment_id'] > 0 ){
						$this->session->set_flashdata('success', __('user.payment_updated_successfully'));
						$details['payment_updated_by'] = $userdetails['id'];
						$details['payment_updated_date'] = date('Y-m-d H:i:s');
						$this->Product_model->update_data('payment_detail', $details,array('payment_id' => (int)$post['payment_id']));

						redirect('usercontrol/mywallet/#tab-paymentdetails');
					}
					else {
						$this->session->set_flashdata('success', __('user.payment_added_successfully'));
						$details['payment_created_by'] = $userdetails['id'];
						$details['payment_created_date'] = date('Y-m-d H:i:s');
						$this->Product_model->create_data('payment_detail', $details);

						redirect('usercontrol/mywallet/#tab-paymentdetails');
					}
					

				} else {
					if(!empty($id)){
						$this->session->set_flashdata('error', $errors['avatar_error'] );
						redirect('usercontrol/mywallet/#tab-paymentdetails');
					} else {
						$this->session->set_flashdata('error', $errors['avatar_error'] );
						redirect('usercontrol/mywallet/#tab-paymentdetails');
					}
				}
			} else {
				$this->session->set_flashdata('error', __('user.form_validation_error'));
				redirect('usercontrol/addpayment');
			}
			

		} else {
			redirect('usercontrol/mywallet/#tab-paymentdetails');
		}			

	}
	public function generateproductcode($affiliateads_id = null){
		$userdetails = $this->userdetails();
		if(empty($userdetails)){ redirect('/login'); }

		else {
			if($affiliateads_id){
				$data['product_id'] = $affiliateads_id;	

				$data['user_id'] = $userdetails['id'];	

				$data['getProduct'] 	= $this->Product_model->getProductByIdArray($affiliateads_id);
				$this->load->view('usercontrol/product/generatecode', $data);

			}
		}

	}
	public function listbuyproduct(){
		$userdetails = $this->userdetails();
		if(empty($userdetails)){ redirect('/login'); }

		$data['buyproductlist'] = $this->Product_model->getAllBuyProduct($userdetails['id']);
		

		$data['user'] = $userdetails;
		$this->load->view('usercontrol/includes/header', $data);
		$this->load->view('usercontrol/product/listofallbuyproduct', $data);
		$this->load->view('usercontrol/includes/footer', $data);
	}

	public function listbuyaffiproduct(){
		$userdetails = $this->userdetails();
		if(empty($userdetails)){ redirect('/login'); }

		$store_setting = $this->Product_model->getSettings('store');
		if(!$store_setting['status']){ show_404(); }

		$filter = array( 'affiliate_id' => $userdetails['id'] );

		$data['buyproductlist'] = $this->Order_model->getOrders($filter);
		foreach ($data['buyproductlist'] as $key => $value) {
			$p = $this->Order_model->getProducts($value['id'],['refer_id' => $userdetails['id']]);
			$t = $this->Order_model->getTotals($p,array());
			$data['buyproductlist'][$key]['total'] = $t['total']['value'];
		}

		$data['status'] = $this->Order_model->status();
		$data['user'] = $userdetails;

		$this->load->view('usercontrol/includes/header', $data);
		$this->load->view('usercontrol/product/listbuyaffiproduct', $data);
		$this->load->view('usercontrol/includes/footer', $data);

	}
	public function editProfile(){
		$userdetails = $this->userdetails();
		if(empty($userdetails)){ redirect('/login'); }
		else { $id =  $userdetails['id']; }

		$this->load->model('PagebuilderModel');
		$this->load->model('User_model');
		if ($this->input->post()) {

			$this->load->library('form_validation');
			$this->form_validation->set_rules('firstname', 'First Name', 'required|trim');
			$this->form_validation->set_rules('lastname', 'Last Name', 'required|trim');
			$this->form_validation->set_rules('email', 'Email', 'required|valid_email|xss_clean');
			$this->form_validation->set_rules('country_id', 'Country', 'required');
			$post = $this->input->post(null,true);

			if($post['password'] != ''){
				$this->form_validation->set_rules('password', 'Password', 'required|trim', array('required' => '%s is required'));
				$this->form_validation->set_rules('cpassword', 'Confirm Password', 'required|trim', array('required' => '%s is required'));
				$this->form_validation->set_rules('cpassword', 'Confirm Password', 'required|trim|matches[password]', array('required' => '%s is required'));
			}

			$json['errors'] = array();

			$register_form = $this->PagebuilderModel->getSettings('registration_builder');
			if($register_form){
				$customField = json_decode($register_form['registration_builder'],1);

				$filesAttached = [];

				$this->load->helper('string');

				$mobile_validation_done = false;

				foreach ($customField as $_key => $_value) {

					$mobile_validation         = (isset($_value['mobile_validation']) && $_value['mobile_validation'] ) ? $_value['mobile_validation'] : '';

					if($mobile_validation == 'true' && $mobile_validation_done == false) {
						$field_name = 'phone';
						$mobile_validation_done = true;
					} else {
						$field_name = 'custom_'. $_value['name'];
					}

					$config['upload_path'] = "assets/user_upload/";
					$config['allowed_types'] = 'png|gif|jpeg|jpg|PNG|GIF|JPEG|JPG|ICO|ico|pdf|docx|doc|ppt|xls|txt';
					$config['max_size']      = 2048;

					if($_value['type'] == 'file') {
						if(isset($post['existing_'.$field_name])){
							if(is_array($post['existing_'.$field_name])) {
								$attahced_multi_azkja = $post['existing_'.$field_name];
							} else {
								$attahced_multi_azkja = [$post['existing_'.$field_name]];
							}
						} else {
							$attahced_multi_azkja = [];
						}
						if(is_array($_FILES[$field_name]['name'])) {
							if(isset($_FILES[$field_name]['name'][0]) && !empty($_FILES[$field_name]['name'][0])) {
								
								foreach ($_FILES[$field_name]['name'] as $key => $image) {
									$_FILES['attahced_multi_azkja']['name']= $_FILES[$field_name]['name'][$key];
									$_FILES['attahced_multi_azkja']['type']= $_FILES[$field_name]['type'][$key];
									$_FILES['attahced_multi_azkja']['tmp_name']= $_FILES[$field_name]['tmp_name'][$key];
									$_FILES['attahced_multi_azkja']['error']= $_FILES[$field_name]['error'][$key];
									$_FILES['attahced_multi_azkja']['size']= $_FILES[$field_name]['size'][$key];

									$config['file_name']  = random_string('alnum', 32);
									
									$this->load->library('upload', $config);
									
									$this->upload->initialize($config);

									if (!$this->upload->do_upload('attahced_multi_azkja')) {
										$error = $this->upload->display_errors();
										if(!str_contains($error, 'select a file')){
											$json['errors'][$field_name] = $error;
											break;
										} else {
											if((!isset($filesAttached[$field_name]) || empty($filesAttached[$field_name])) && isset($_value['required']) && $_value['required']) {
												$json['errors'][$field_name] = $error;
												break;
											}
										}
									} else {
										$ext = explode('.', $_FILES[$field_name]['name'][$key]);
										$attahced_multi_azkja[] = $config['file_name'].".".$ext[sizeof($ext)-1];
									}
								}
							} 

							
						} else {
							if(isset($_FILES[$field_name]['name']) && !empty($_FILES[$field_name]['name'])) {
								
								$config['file_name']  = random_string('alnum', 32);
								
								$this->load->library('upload', $config);
								
								$this->upload->initialize($config);

								if (!$this->upload->do_upload($field_name)) {
									$error = $this->upload->display_errors();
									if(!str_contains($error, 'select a file')){
										$json['errors'][$field_name] = $error;
										break;
									} else {
										if((!isset($filesAttached[$field_name]) || empty($filesAttached[$field_name])) && isset($_value['required']) && $_value['required']) {
											$json['errors'][$field_name] = $error;
											break;
										}
									}
								} else {
									$ext = explode('.', $_FILES[$field_name]['name']);
									$attahced_multi_azkja = [$config['file_name'].".".$ext[sizeof($ext)-1]];
								}
							}
						}

						$filesAttached[$field_name] = $attahced_multi_azkja;
						
						if(isset($_value['required']) && $_value['required'] && (!isset($filesAttached[$field_name]) || empty($filesAttached[$field_name]))) {
							$json['errors'][$field_name] = "Please select file for upload!";
							break;
						}
					} else {
						if($_value['required'] == 'true'){
							if(!isset($post[$field_name]) || $post[$field_name] == ''){
								$json['errors'][$field_name] = $_value['label'] ." is required.!";
							}
						}

						if(!isset($json['errors'][$field_name]) && (int)$_value['maxlength'] > 0){
							if(strlen( $post[$field_name] ) > (int)$_value['maxlength']){
								$json['errors'][$field_name] = $_value['label'] ." Maximum length is ". (int)$_value['maxlength'];
							}
						}

						if(!isset($json['errors'][$field_name]) && (int)$_value['minlength'] > 0){
							if(strlen( $post[$field_name] ) > (int)$_value['minlength']){
								$json['errors'][$field_name] = $_value['label'] ." Minimum length is ". (int)$_value['minlength'];
							}
						}
					}

				}

			}
			
			if ($this->form_validation->run() == FALSE) {
				$json['errors'] = array_merge($this->form_validation->error_array(), $json['errors']);
			}
			if( count($json['errors']) == 0){
				$checkmail = $this->Product_model->checkmail($this->input->post('email',true),$id);

				if(!empty($checkmail)){ $json['errors']['email'] = "Email Already Exist"; }

				if(count($json['errors']) == 0){

					$custom_fields = array();
					$post = $this->input->post(null,true);
					foreach ($this->input->post() as $key => $value) {
						if(!in_array($key, array('id', 'plan_id', 'refid', 'level_id', 'type', 'firstname', 'lastname', 'email', 'username', 'password', 'phone', 'twaddress', 'address1', 'address2', 'ucity', 'ucountry', 'state', 'uzip', 'avatar', 'online', 'unique_url', 'bitly_unique_url', 'updated_at', 'google_id', 'facebook_id', 'twitter_id', 'umode', 'PhoneNumber', 'Addressone', 'Addresstwo', 'City', 'Country', 'StateProvince', 'Zip', 'f_link', 't_link', 'l_link', 'products_wishlist', 'product_commission', 'affiliate_commission', 'product_commission_paid', 'affiliate_commission_paid', 'product_total_click', 'product_total_sale', 'affiliate_total_click', 'sale_commission', 'sale_commission_paid', 'status', 'reg_approved', 'is_vendor', 'store_meta', 'store_slug', 'store_name', 'store_contact_us_map', 'store_address', 'store_email', 'store_contact_number', 'store_terms_condition', 'value', 'last_ping', 'install_location_details', 'token', 'created_at', 'device_type', 'device_token', 'groups', 'email_subscription', 'cpassword','country_id','action')) && !strpos($key, "_afftel_input_pre")){
							if(isset($post[$key."_afftel_input_pre"]) && ! empty($post[$key."_afftel_input_pre"]) && ! empty($value)) {
	                    		$custom_fields[$key] = "+".$post[$key."_afftel_input_pre"]." ".$value;
                    		} else {
	                    		$custom_fields[$key] = $value;
	                    	}
						}
					}

					$phone = $this->input->post('phone',true);

					$phone_afftel_input_pre = $this->input->post('phone_afftel_input_pre',true);
                	
                	if(! empty($phone_afftel_input_pre) && ! empty($phone)) {
                		$phone = "+".$phone_afftel_input_pre." ".$phone;
                	}
                	
					$userArray = array(
						'firstname'                 => $this->input->post('firstname',true),
						'lastname'                  => $this->input->post('lastname',true),
						'email'                     => $this->input->post('email',true),
						'ucountry'                 	=> $this->input->post('country_id',true),
						'Country'                 	=> $this->input->post('country_id',true),
						'phone'                     => $phone,
						'value'                    	=> json_encode(array_merge($custom_fields, $filesAttached)),
					);

					if($post['password'] != ''){
						$userArray['password'] = sha1( $post['password'] );
					}

					if(!empty($_FILES['avatar']['name'])){
						$upload_response = $this->upload_photo('avatar','assets/images/users');

						if($upload_response['success']){
							$userArray['avatar'] = $upload_response['upload_data']['file_name'];
						}
					}

					$this->user->update_user($id, $userArray);
					$userArray = $this->db->query("SELECT * FROM users WHERE id = ". (int)$id)->row_array();
					$this->session->set_userdata(array('user'=>$userArray));


					$this->session->set_flashdata('success', 'Profile Updated Successfully');
					$json['location'] = base_url('usercontrol/editProfile/');
				}
			}

			if(isset($_POST['email_subscription']) && $_POST['email_subscription'] == 1) {
				$unsbscribed = $this->db->query('SELECT id FROM unsubscribed_emails WHERE email="'.$_POST['email'].'"')->row();

				if(empty($unsbscribed)) {
					$this->db->query('INSERT INTO `unsubscribed_emails`(`email`) VALUES ("'.$_POST['email'].'")');
				}
			} else if(isset($_POST['email_subscription'])) {
				$this->db->query('DELETE FROM `unsubscribed_emails` WHERE email="'.$_POST['email'].'"');
			}


			echo json_encode($json);die;
		} else {
			$data['user']  = (array)$this->user->get($id);
			$data['countries'] = $this->User_model->getCountries();
			$register_form = $this->PagebuilderModel->getSettings('registration_builder');
			$data['data'] = json_decode($register_form['registration_builder'],1);
			$data['edit_view'] = true;
			$data['user_groups'] = $this->User_model->getgrouplist();
			$data['user_groups_readonly'] = true;
			$data['disable_username'] = true;
			$data['html_form'] = $this->load->view('auth/user/templates/register_form',$data, true);

			$this->load->view('usercontrol/includes/header', $data);
			$this->load->view('usercontrol/users/edit_profile', $data);
			$this->load->view('usercontrol/includes/footer', $data);
		}
		

		function getstate($country_id = null) {
			$userdetails = $this->userdetails();
			if(empty($userdetails)){
				redirect('usercontrol');
			}
			else {
				$states = $this->Product_model->getAllstate($country_id);
				echo '<option selected="selected">Select State</option>';
				if(!empty($states)){
					foreach($states as $state){
						echo '<option value="'.$state['name'].'">'.$state['name'].'</option>';
					}
				}
				die;
				

			}
		}
	}
	public function friendly_seo_string($vp_string){
		$vp_string = trim($vp_string);
		$vp_string = html_entity_decode($vp_string);	

		$vp_string = strip_tags($vp_string);
		$vp_string = strtolower($vp_string);	

		$vp_string = preg_replace('~[^ a-z0-9_.]~', ' ', $vp_string);
		$vp_string = preg_replace('~ ~', '-', $vp_string);

		$vp_string = preg_replace('~-+~', '-', $vp_string);
		return $vp_string;
	}
	public function upload_photo($fieldname,$path) {
		

		$config['upload_path'] = $path;
		$config['allowed_types'] = 'png|gif|jpeg|jpg';
		

		$this->load->helper('string');
		$config['file_name']  = random_string('alnum', 32);
		$this->load->library('upload', $config);
		$this->upload->initialize($config);
		

		if (!$this->upload->do_upload($fieldname)) {
			echo $this->upload->display_errors();
			die;
			$data = array('success' => false, 'msg' => $this->upload->display_errors());
		} else {
			$upload_details = $this->upload->data();
			

			$config1 = array(
				'source_image' => $upload_details['full_path'],
				'new_image' => $path.'/thumb',
				'maintain_ratio' => true,
				'width' => 300,
				'height' => 300
			);
			$this->load->library('image_lib', $config1);
			$this->image_lib->resize();
			$data = array('success' => true, 'upload_data' => $upload_details, 'msg' => "Upload success!");
		}
		return $data;
	}
	public function updatenotify($country_id = null) {
		$userdetails = $this->userdetails();
		$post = $this->input->post(null,true);

		if(empty($userdetails)){ redirect('/login'); }
		else {
			if(!empty($post['id'])){
				$noti = $this->db->query("SELECT * FROM notification WHERE notification_id= ". $post['id'])->row();
				

				if($noti->notification_type == 'integration_click'){
					$json['location'] = base_url('usercontrol/'.$noti->notification_url);
				}
				else if($noti->notification_type == 'integration_orders'){
					$json['location'] = base_url('usercontrol/'.$noti->notification_url);
				} else{
					$json['location'] = base_url('usercontrol/'.$noti->notification_url);
				}
				

				$this->Product_model->update_data('notification', array('notification_is_read' => 1),array('notification_id' => $post['id']));
			}
		}

		echo json_encode($json);
	}
	public function getnotificationnew() {
		$userdetails = $this->userdetails();
		if(empty($userdetails)){ redirect('/login'); }
		else {
			$notifications = $this->Product_model->getnotificationnew('user', $userdetails['id']);
			echo trim(count($notifications));
		}
	}
	public function getnotificationall() {
		$userdetails = $this->userdetails();
		if(empty($userdetails)){ redirect('/login'); }
		else {
			$notifications = $this->Product_model->getnotificationall('user', $userdetails['id']);
			echo trim(count($notifications));
		}
	}
	public function delete_image($image_id = null){
		$userdetails = $this->userdetails();
		$post = $this->input->post(null,true);

		if(empty($userdetails)){ redirect('/login'); }
		else {
			if(!empty($post['image_id'])){
				$this->Product_model->deleteImage($post['image_id']);
			}
		}
	}
	public function getnotification() {
		$userdetails = $this->userdetails();
		if(empty($userdetails)){ redirect('/login'); }
		else {
			$notifications = $this->Product_model->getnotification('user', $userdetails['id']);
			if(!empty($notifications)){
				foreach($notifications as $notification){
					if($notification['notification_type'] == 'order'){
						if($notification['notification_view_user_id'] == $userdetails['id']){
							echo '<a href="javascript:void(0)" onclick=shownofication('.$notification['notification_id'].',"'.base_url().'usercontrol'.$notification['notification_url'].'") class="dropdown-item notify-item">
							<div class="notify-icon bg-primary"><i class="mdi mdi-cart-outline"></i></div>
							<p class="notify-details"><b>'.$notification['notification_title'].'</b><small class="text-muted">'.$notification['notification_description'].'</small></p>
							</a>';
						}
					}
					

					if($notification['notification_type'] == 'client'){
						echo '<a href="javascript:void(0)" onclick=shownofication('.$notification['notification_id'].',"'.base_url().'usercontrol'.$notification['notification_url'].'") class="dropdown-item notify-item">
						<div class="notify-icon bg-primary"><i class="mdi mdi-account-circle"></i></div>
						<p class="notify-details"><b>'.$notification['notification_title'].'</b><small class="text-muted">'.$notification['notification_description'].'</small></p>
						</a>';
					}
					

					if($notification['notification_type'] == 'paymentrequest'){
						echo '<a href="javascript:void(0)" onclick=shownofication('.$notification['notification_id'].',"'.base_url().'usercontrol'.$notification['notification_url'].'") class="dropdown-item notify-item">
						<div class="notify-icon bg-primary"><i class="mdi mdi-account-circle"></i></div>
						<p class="notify-details"><b>'.$notification['notification_title'].'</b><small class="text-muted">'.$notification['notification_description'].'</small></p>
						</a>';
					}
					

					if($notification['notification_type'] == 'user'){
						echo '<a href="javascript:void(0)" onclick=shownofication('.$notification['notification_id'].',"'.base_url().'usercontrol'.$notification['notification_url'].'") class="dropdown-item notify-item">
						<div class="notify-icon bg-primary"><i class="mdi mdi-account"></i></div>
						<p class="notify-details"><b>'.$notification['notification_title'].'</b><small class="text-muted">'.$notification['notification_description'].'</small></p>
						</a>';
					}
					

					if($notification['notification_type'] == 'product'){
						echo '<a href="javascript:void(0)" onclick=shownofication('.$notification['notification_id'].',"'.base_url().'usercontrol'.$notification['notification_url'].'") class="dropdown-item notify-item">
						<div class="notify-icon bg-primary"><i class="mdi mdi-basket"></i></div>
						<p class="notify-details"><b>'.$notification['notification_title'].'</b><small class="text-muted">'.$notification['notification_description'].'</small></p>
						</a>';
					}
					

					if($notification['notification_type'] == 'commission'){
						echo '<a href="javascript:void(0)" onclick=shownofication('.$notification['notification_id'].',"'.base_url().'usercontrol'.$notification['notification_url'].'") class="dropdown-item notify-item">
						<div class="notify-icon bg-primary"><i class="mdi mdi-basket"></i></div>
						<p class="notify-details"><b>'.$notification['notification_title'].'</b><small class="text-muted">'.$notification['notification_description'].'</small></p>
						</a>';
					}
					

					if($notification['notification_type'] == 'commissionrequest'){
						echo '<a href="javascript:void(0)" onclick=shownofication('.$notification['notification_id'].',"'.base_url().'usercontrol'.$notification['notification_url'].'") class="dropdown-item notify-item">
						<div class="notify-icon bg-primary"><i class="mdi mdi-cash-usd"></i></div>
						<p class="notify-details"><b>'.$notification['notification_title'].'</b><small class="text-muted">'.$notification['notification_description'].'</small></p>
						</a>';
					}
					

				}
			}
			die;
			

		}
	}
	public function vieworder($order_id){
		$userdetails = $this->userdetails();
		if(empty($userdetails)){ redirect('/login'); }

		$this->load->model('Form_model');
		$data['order'] = $this->Order_model->getOrder($order_id);
		$data['products'] = $this->Order_model->getProducts($order_id,['vendor_or_refer_id' => $userdetails['id']]);
		if($data['products']){
			

			$data['affiliateuser'] = $this->Order_model->getAffiliateUser($order_id);
			$data['payment_history'] = $this->Order_model->getHistory($order_id);
			$data['status'] = $this->Order_model->status();
			$data['order_history'] = $this->Order_model->getHistory($order_id, 'order');
			$data['totals'] = $this->Order_model->getTotals($data['products'],$data['order']);
			

			$this->load->view('usercontrol/includes/header', $data);
			$this->load->view('usercontrol/product/vieworder', $data);
			$this->load->view('usercontrol/includes/footer', $data);
		}
		else{
			die("You are not allow to see.. !");
		}
	}

	public function all_transaction(){
		$userdetails = $this->userdetails();
		if(empty($userdetails)) redirect('/login');

		$filter = $this->input->post(null,true);

		$this->load->library('pagination');
		$config['base_url'] = base_url('usercontrol/all_transaction');
		$config['uri_segment'] = 3;
		$config['per_page'] = 10;
		$config['total_rows'] = count($this->Wallet_model->getAllTransaction($userdetails,$filter,false));
		$config['use_page_numbers'] = TRUE;
		$config['page_query_string'] = TRUE;
		$config['enable_query_strings'] = TRUE;
		$_GET['page'] = $filter['page'];
		$config['query_string_segment'] = 'page';
		$this->pagination->initialize($config);
		$view['pagination'] = $this->pagination->create_links();
		$view['all_transaction'] = $this->Wallet_model->getAllTransaction($userdetails,$filter,$config['per_page']);
		
		$html = $this->load->view("usercontrol/users/parts/all_transaction",$view,true);
		if($filter){
			echo $html;
			die();
		}

		$data['html'] = $html;
		
		$this->load->config('payment_gateway');
		$data['payment_module'] =  config_item('payment_module');
		$data['filter_field'] =  $this->Wallet_model->getAllTransactionFilter($userdetails);

		$this->view($data,'users/all_transaction','usercontrol');
	}

	public function all_transaction_export_to_excel(){
		$userdetails = $this->userdetails();
		if(empty($userdetails)) redirect('/login');

		$filter = $this->input->get(null,true);

		$this->load->helper('all_transaction');
		$all_transaction = $this->Wallet_model->getAllTransaction($userdetails,$filter,false);
		exportToExcel($all_transaction);
	}

	public function all_transaction_export_to_pdf(){
		$userdetails = $this->userdetails();
		if(empty($userdetails)) redirect('/login');

		$filter = $this->input->get(null,true);

		$this->load->helper('all_transaction');
		$all_transaction = $this->Wallet_model->getAllTransaction($userdetails,$filter,false);
		exportToPdf($userdetails['admin'],$all_transaction);
	}

	public function wallet_requests_details($id){
		$userdetails = $this->userdetails();
		$get = $this->input->get(null,true);
		$id=(int)$id;

		if(empty($userdetails)){ redirect('/login'); }

		$data['request'] = $this->db->query("SELECT * FROM wallet_requests WHERE id={$id} AND user_id=". $userdetails['id'])->row_array();
		if(!$data['request']){
			show_404();
		}

		$filter = array(
			'user_id' => $userdetails['id'],
			'id_in' => $data['request']['tran_ids'],
		);

		$data['transaction'] = $this->Wallet_model->getTransaction($filter);
		$data['status'] = $this->Wallet_model->status();
		$data['status_icon'] = $this->Wallet_model->status_icon;

		$this->view($data,'users/wallet_requests_details','usercontrol');
	}

	public function wallet_requests_list(){
		$userdetails = $this->userdetails();
		$get = $this->input->get(null,true);
		$post = $this->input->post(null,true);

		if(empty($userdetails)){ redirect('/login'); }

		if (isset($post['delete_request'])) {

			$id= (int)$post['id'];



			$req = $this->db->query("SELECT * FROM wallet_requests WHERE id={$id}")->row();

			if($req){

				if($req->tran_ids){

					$this->db->query("UPDATE wallet SET status=1 WHERE id in (". $req->tran_ids .") ");

				}

				$this->db->query("DELETE FROM wallet_requests WHERE id= {$id}");

				$this->db->query("DELETE FROM wallet_requests_history WHERE id= {$id}");

			}

			$json['success'] = 1;

			echo json_encode($json);die;

		}

		$data['lists'] = $this->db->query("SELECT * FROM wallet_requests WHERE user_id=". $userdetails['id']. " ORDER BY id DESC")->result_array();

		$filter = array(
			'user_id' => $userdetails['id'],
			'status_gt' => 2,
			'old_with' => 1,
		);

		$data['status'] = $this->Wallet_model->status();
		$data['status_icon'] = $this->Wallet_model->status_icon;
		$data['payout_transaction'] = $this->Wallet_model->getTransaction($filter);
		$data['notcheckmember'] = 1;
		$this->view($data,'users/wallet_requests_list','usercontrol');
	}


	public function my_deposits(){
		$userdetails = $this->userdetails();
		
		if(empty($userdetails)) redirect('usercontrol/dashboard');

		$market_vendor = $this->Product_model->getSettings('market_vendor');
		if(!isset($userdetails['is_vendor']) || $userdetails['is_vendor'] != 1)
			redirect('usercontrol/dashboard');

		$get = $this->input->get(null,true);
		$post = $this->input->post(null,true);

		$this->load->model('Deposit_payment_model');

		$data['total_deposited'] = $this->db->query("SELECT SUM(vd_amount) as total FROM vendor_deposit WHERE vd_status=1 AND vd_user_id={$userdetails['id']} {$deposit_where} ")->row()->total;

		if(empty($data['total_deposited'])) {
			$data['total_deposited'] = 0;
		}
		$data['status']= $status = $this->Deposit_payment_model->status;
		
		$data['status_icon']= $status_icon = $this->Deposit_payment_model->status_icon;

		if(isset($get['vd'])){
			$this->session->set_flashdata('success', __('user.deposit_payment_success'));
			redirect(base_url('usercontrol/my_deposits'));
		}
		
		if (isset($post['get_deposit'])) {
			$get = $this->input->post(null,true);
			$filter = array(
				'user_id' => $userdetails['id']
			);

			if (isset($post['date'])) {
				$filter['date'] = $post['date'];
				$data['date'] = $filter['date'];
			}

			$this->load->model('Deposit_payment_model');

			$lists = $this->Deposit_payment_model->getDeposits($filter);

			$json['html'] = "";

			foreach ($lists as $key => $value) { 
				$json['html'] .= '<tr>
				<td>'. $value['vd_id'] .'</td>
				<td>'. $value['username'] .'</td>
				<td>'. dateFormat($value['vd_created_on'],'d F Y') .'</td>
				<td>'. $value['vd_payment_method'] .'</td>
				<td>'. $value['vd_txn_id'] .'</td>
				<td>'. c_format($value['vd_amount']) .'</td>
				<td>'. withdrwal_status($value['vd_status']) .'</td>
				<td class="text-right">
				<a href="'. base_url('usercontrol/deposit_details/'. $value['vd_id']) .'" class="btn btn-primary btn-sm">Details</a>
				</td>
				</tr>';
			}

			echo json_encode($json);die;
		}
		
		$data['vendorDepositStatus'] = $this->Product_model->getSettings('vendor', 'depositstatus');
		$data['vendorMinDepositAmt'] = $this->Product_model->getSettings('site', 'vendor_min_deposit');
		
		$this->view($data,'users/deposit_requests_list','usercontrol');
	}

	public function deposit_details($id)
	{
		$userdetails = $this->userdetails();

		$id=(int)$id;

		if(empty($userdetails)){ redirect('/login'); }


		$this->load->model('Deposit_payment_model');

		$data['request'] = $this->Deposit_payment_model->getDeposits(['vd_id'=>$id]);

		if(!$data['request']){
			show_404();
		}

		$data['status_list'] = $this->Deposit_payment_model->status_list;

		$this->view($data,'users/deposit_details','usercontrol');
	}

	public function mywallet(){

		$userdetails = $this->userdetails();

		$get = $this->input->get(null,true);

		if(empty($userdetails)){ redirect('/login'); }
		$filter = array(
			'user_id' => $userdetails['id'],
			'status_gt' => 0,
 			'parent_id' => 0,
		);

		if ( isset($get['type']) && $get['type'] ) {
			$filter['types'] = $get['type'];
		}

		if (isset($get['paid_status']) && $get['paid_status']) {
			$filter['paid_status'] = $get['paid_status'];
		}

		if (isset($get['withdraw_type']) && !empty($get['withdraw_type'])) {
			$filter['withdraw_type'] = $get['withdraw_type'];
		}


		if (isset($get['date'])) {
			$filter['date'] = $get['date'];
		}
 
		$site_setting = $this->Product_model->getSettings('site');
		$data['site_setting'] = $site_setting;
		$data['userdetails']=$userdetails;

		$this->load->model('Total_model');

		$data['user_totals'] = $this->Total_model->getUserTotals((int)$userdetails['id']);
		
		$post = $this->input->post(null,true);
		$get = $this->input->get(null,true);

		if (isset($post['request_payment_all'])) {
			$json = array();

			$ids = (array)$post['ids'];

			$transactions = $this->db->query("SELECT * FROM wallet WHERE id IN (". implode(",", $ids) .")")->result();

				$request = [
					'tran_ids' => implode(",", $ids),
					'status' => 0,
					'user_id' => (int)$userdetails['id'],
					'total' => 0,
					'created_at' => date("Y-m-d H:i:s"),
				];

				foreach ($transactions as $key => $value) {
					$request['total'] += (float)$value->amount;
				}

				if($request['total'] > 0){
					$this->db->query("UPDATE wallet SET status=2 WHERE id IN (". implode(",", $ids) .") ");
						$this->db->insert("wallet_requests", $request);
						$this->load->model('Mail_model');
						$this->Mail_model->send_wallet_withdrawal_req($request['total'], $userdetails);
						$json['success'] = 1;
					} else{
						$json['error'] = __('user.withdrwal_total_must_be_greater_than_zero');
					}

					echo json_encode($json);die;
				}

				$data['wallet_unpaid_amount'] = (float)$this->db->query("SELECT SUM(amount) as total FROM wallet WHERE status=1 AND amount > 0 AND commission_status=0 AND user_id=". (int)$userdetails['id'])->row()->total;

				$filter['sortBy'] = isset($get['sortby']) ? $get['sortby'] : '';
				$filter['orderBy'] = isset($get['order']) ? $get['order'] : '';

				$data['request_status'] = $this->Wallet_model->request_status;
				$data['status'] = $this->Wallet_model->status();
				$data['status_icon'] = $this->Wallet_model->status_icon;


				$config['base_url'] = base_url('usercontrol/mywallet/');
				$config['total_rows'] = $this->Wallet_model->getTransaction($filter, true, 'ONLY_PARENTS');
				$config['per_page'] = 25;
				$config['attributes'] = array('class' => 'single_paginate_link');
				$filter['per_page'] = $config['per_page'];
				$config['reuse_query_string'] = TRUE;
				$config['query_string_segment'] = 'page';
				$config['use_page_numbers'] = TRUE;
				
				$this->pagination->initialize($config);

				$filter['page_num'] = ($this->uri->segment(3)) ? $this->uri->segment(3) : 1;
				
				$filter['offset'] = ($filter['page_num'] - 1) * $config['per_page'];

				$data['transaction'] = $this->Wallet_model->getTransaction($filter, false, 'ONLY_PARENTS');

				if($userdetails['is_vendor'] == 1)
				unset($filter['user_id']);
				unset($filter['per_page']);
				unset($filter['offset']);
				unset($filter['page_num']);
				
				$transactionSorted = [];

				$child_transaction=array();

					
				$filter['not_negative_balence']=true;
				
				for ($i=0; $i < sizeof($data['transaction']); $i++) {



					$filter['group_id'] = $data['transaction'][$i]['group_id'];

					$filter['not_tran_id'] = $data['transaction'][$i]['id'];
					
					if($userdetails['is_vendor'] != 1) {
						
						$child_transaction = $this->Wallet_model->getTransaction($filter);

						if($userdetails['is_vendor'] != 1)
						$child_transaction[]  = $data['transaction'][$i];

						$child_transaction = array_reverse($child_transaction);
					}else{
					

						if (!empty($data['transaction'][$i]['from_user_id'])) {
							$child_transaction = $this->Wallet_model->getTransaction($filter);

							if($userdetails['is_vendor'] != 1)
							$child_transaction[]  = $data['transaction'][$i];

							$child_transaction = array_reverse($child_transaction);
						}else if($data['transaction'][$i]['is_vendor'] == 1 && $data['transaction'][$i]['comm_from'] =='store'){

							$filter['amount']=1;
							$product_detail=$this->Wallet_model->getProductDetail($userdetails['id'],$data['transaction'][$i]['reference_id']);
							
							if(!empty($product_detail)){
								$child_transaction = $this->Wallet_model->getTransaction($filter);
							
								if($userdetails['is_vendor'] == 1 && $data['transaction'][$i]['amount'] > 0)
								$child_transaction[]  = $data['transaction'][$i];

								$child_transaction = array_reverse($child_transaction);
							}else{
								$vendor=$data['transaction'][$i];
							
								array_push($child_transaction,$vendor);

								$child_transaction = array_reverse($child_transaction);

							}
							


						}else if($data['transaction'][$i]['is_vendor'] == 0 && $data['transaction'][$i]['comm_from'] =='store'){

							$filter['amount']=1;
							$product_detail=$this->Wallet_model->getProductDetail($userdetails['id'],$data['transaction'][$i]['reference_id']);
							if(!empty($product_detail)){
								$child_transaction = $this->Wallet_model->getTransaction($filter);
							
								if($userdetails['is_vendor'] != 1)
								$child_transaction[]  = $data['transaction'][$i];

								$child_transaction = array_reverse($child_transaction);
							}else{
								$vendor=$data['transaction'][$i];
							
								array_push($child_transaction,$vendor);

								$child_transaction = array_reverse($child_transaction);

							}
						}
						else{

							

							$vendor=$data['transaction'][$i];
							
							array_push($child_transaction,$vendor);

							$child_transaction = array_reverse($child_transaction);
							
						}
						
					}
					
					
					$child_transaction_sorted = $child_transaction;

					foreach($child_transaction as $key => $ch) {
						$moveFirst = false;

						if(strpos($a['type'], 'refer') === false) {
							if(in_array($ch['type'], ['vendor_sale_commission', 'sale_commission', 'external_sale_commission', 'click_comission']) && $ch['parent_id'] == 0) {
								$moveFirst = true;
							} else if(strpos($a['type'], 'click') && $ch['parent_id'] == 0) {
								$moveFirst = true;
							}
						}

						if($moveFirst) {
							unset($child_transaction_sorted[$key]);
							array_unshift($child_transaction_sorted , $ch);
						}
					}
					
					if($child_transaction_sorted[0]['status'] > 1) {
						$re_child_transaction_sorted = $child_transaction_sorted;
						foreach($child_transaction_sorted as $key => $ch) {
							if($ch['status'] == 0 || $ch['status'] == 1) {
								$moveFirst = true;
							}
							
							if($moveFirst) {
								unset($re_child_transaction_sorted[$key]);
								array_unshift($re_child_transaction_sorted , $ch);
							}
						}
						$child_transaction_sorted = $re_child_transaction_sorted;
					}

					
					if($userdetails['is_vendor'] != 1) {
						$transactionSorted = array_merge($transactionSorted, $child_transaction_sorted);
					}else{
						if (!empty($data['transaction'][$i]['from_user_id'])) {
							$transactionSorted = array_merge($transactionSorted, $child_transaction_sorted);
						}else if($data['transaction'][$i]['is_vendor'] == 1 && $data['transaction'][$i]['comm_from'] =='store'){
							$transactionSorted = array_merge($transactionSorted, $child_transaction_sorted);
						
						}else if($data['transaction'][$i]['is_vendor'] == 0 && $data['transaction'][$i]['comm_from'] =='store'){
							$transactionSorted = array_merge($transactionSorted, $child_transaction_sorted);
						}else{
							$transactionSorted = array_merge($transactionSorted, $child_transaction_sorted);
						}
					}
					
				}

				
				$transactionSorted=array_values(array_column($transactionSorted, null, 'id'));

				
				
				if(isset($site_setting) && is_array($site_setting))
				{ 
					if($site_setting["wallet_auto_withdrawal"]==1)
					{
						$wallet_auto_withdrawal_days=$site_setting["wallet_auto_withdrawal_days"];
						$wallet_auto_withdrawal_limit=$site_setting["wallet_auto_withdrawal_limit"];
						$AutoWithdrawaltotal=$this->Wallet_model->getHoldTransactionsByUserId($wallet_auto_withdrawal_days,$wallet_auto_withdrawal_limit,(int)$userdetails['id']);

						$data['walletauto_withdrawal']=1;
						$data['wallet_auto_withdrawal_days']=$wallet_auto_withdrawal_days;
						$data['wallet_auto_withdrawal_total']=$AutoWithdrawaltotal;
						
						$message=__('user.your_eligible_approved_commission_amount_is')." [".c_format($AutoWithdrawaltotal)."]"." | ".__('user.and_it_will_be_available_for_payment_in')." ".$wallet_auto_withdrawal_days." ".__('user.days')." | " ." ". "Eligible minimum withdrawal commission:"." ".c_format($site_setting["wallet_min_amount"]);
						$data['wallet_auto_withdrawal_message']=$message;
					}
				}
				
				
				$data['transaction'] = $transactionSorted;

				$data['pagination_link'] = $this->pagination->create_links();

				$data['Wallet_model'] = $this->Wallet_model;

				$data['refer_total'] = $this->Product_model->getReferalTotals($userdetails['id']);
				
				$data['site_setting'] = $this->Product_model->getSettings('site');

				$data['userdetails'] = $this->userdetails();

				if($data['userdetails']['is_vendor'])
					$data['market_vendor'] = $this->Product_model->getSettings('market_vendor');

				$data['notcheckmember'] = 1;
 				

				$this->view($data,'users/newmywallet','usercontrol');
			}

			public function info_remove_tran_by_commission(){
				$userdetails = $this->userdetails();
				if(empty($userdetails) || empty($userdetails['is_vendor'])) redirect('usercontrol/dashboard');

				$id = (int) $this->input->post("id",true);
				$wallet = $this->Wallet_model->getbyId($id);

				$market_vendor = $this->Product_model->getSettings('market_vendor');
				if(($wallet->comm_from == 'ex' && empty($wallet->is_action) 
					&& $wallet->reference_id_2 != '__general_click__'  && $market_vendor['marketvendorexternalordercampaign'])
					||  ($wallet->is_action && $market_vendor['marketvendoractionscampaign'])
					||  ($wallet->reference_id_2 == '__general_click__' && $market_vendor['marketvendorclickcampaign'])){

					$dataCollection = $this->Wallet_model->getDeleteData($id);

				$status_type = $this->input->post("status_type",true);

				$delete_id = $this->input->post("id",true);

				$this->db->query("DELETE FROM wallet_requests WHERE FIND_IN_SET($delete_id,tran_ids)");


				$html = '<h6 class="text-center">'.__('user.important_this_action_can_not_be_undo').'</h6><hr>';

				$html .= '<p> '.__('user.once_you_change_status_trash_or_cancel').' </p>';
				$html .= '<hr>';

				$table = "<div class='transaction-datails-div-hidden' style='display:none;'><table class='table table-stripped'><thead style='width: calc( 100% - 1em )'><tr style='display: table;  width: 100%; table-layout: fixed;'><td class='text-center'>".__('user.transaction_id')."</td><td class='text-center'>".__('user.username')."</td><td class='text-center'> ".__('user.amount')."</td></tr></thead><tbody style=' display: block; max-height: 200px; overflow-y: auto;'>";
				
				$amountTotal = 0;
				
				foreach ($dataCollection as $data) {

					$amountTotal += ($data['amount'] > 0) ? $data['amount'] : 0;

					$table .= '<tr style="display: table; width: 100%; table-layout: fixed;"><td class="text-center">'. $data['id'] .'</td><td class="text-center">'. $data['name'] .'</td><td class="text-center">'. c_format($data['amount']) .'</td></tr>';
				}

				$table .= "</tbody></table></div>";

				$html .= "<p><strong>".count($dataCollection)."</strong> ".__('user.transactions_having_total_amount')." <strong>".c_format($amountTotal)."</strong> ".__('user.will_get_affected')." <a href='javascript:void(0)' class='show-trans-aff-details'><br>".__('user.see_details')."</a></p>";

				$html .= $table;

				$html .= "<br><div class='row'> <div class='col-sm-6'><button data-bs-dismiss='modal' class='btn btn-primary btn-block'>".__('user.cancel')."</button></div> <div class='col-sm-6'><button class='btn btn-danger  btn-block' id='". $id ."' status_type='". $status_type ."' change-tran-by-commi-confirm>".__('user.yes_confirm')."</button></div> </div>";


				$json['html'] = $html;

				echo json_encode($json);
			}
		}

		public function change_commission_status(){
			$userdetails = $this->userdetails();
			if(empty($userdetails) || empty($userdetails['is_vendor'])) redirect('usercontrol/dashboard');

			$id = (int) $this->input->post("id",true);
			$wallet = $this->Wallet_model->getbyId($id);

			$market_vendor = $this->Product_model->getSettings('market_vendor');
			if(($wallet->comm_from == 'ex' && empty($wallet->is_action) 
				&& $wallet->reference_id_2 != '__general_click__'  && $market_vendor['marketvendorexternalordercampaign'])
				||  ($wallet->is_action && $market_vendor['marketvendoractionscampaign'])
				||  ($wallet->reference_id_2 == '__general_click__' && $market_vendor['marketvendorclickcampaign'])){
				
				$status_type = $this->input->post('status_type');

			$delete_id = $this->input->post("id",true);
			
			$dataCollection = $this->Wallet_model->getDeleteData($id);
			
			foreach ($dataCollection as $tran) {
				if(!empty($tran['id'])) {
					$where = array('id'=>$tran['id']);
					$data = array('commission_status'=>$status_type);
					$update = $this->Common_model->update('wallet', $where, $data);
					if($update)
					{
						$where_request = array('tran_ids'=>$tran['id']);
						
						$data = array('status'=>0);
						
						$update = $this->Common_model->update('wallet', $where, $data);
						
						$where_request = array('tran_ids'=>$tran['id']);
						
						$update_request = $this->Common_model->update('wallet_requests', $where_request, $data);

						$json['message'] = "status change successfully";
						$json['status'] = 1;
					}
					else
					{
						$json['message'] = "status change failed";
						$json['status'] = 0;
					}
				}

			}
			
			echo json_encode($json);
		}
	}

	public function wallet_change_status(){
		$userdetails = $this->userdetails();
		if(empty($userdetails) || empty($userdetails['is_vendor'])) redirect('usercontrol/dashboard');

		$id = (int) $this->input->post("id",true);
		$wallet = $this->Wallet_model->getbyId($id);

		$market_vendor = $this->Product_model->getSettings('market_vendor');
		if(($wallet->comm_from == 'ex' && empty($wallet->is_action) 
			&& $wallet->reference_id_2 != '__general_click__'  && $market_vendor['marketvendorexternalordercampaign'])
			||  ($wallet->is_action && $market_vendor['marketvendoractionscampaign'])
			||  ($wallet->reference_id_2 == '__general_click__' && $market_vendor['marketvendorclickcampaign'])){

			$val = (int)$this->input->post("val",true);

		$confirm = $this->input->post("confirm",true);
		
		$tran = $this->db->query("
			
			SELECT w.*,u.firstname,u.lastname,u.email,wallet_recursion.id as wallet_recursion_id,
			
			(SELECT SUM(amount) FROM `wallet` ww WHERE ww.parent_id=w.id) as total_recurring_amount
			
			FROM wallet w 
			
			LEFT JOIN users u ON u.id=w.user_id  
			
			LEFT JOIN  wallet_recursion ON wallet_recursion.transaction_id = w.id
			
			WHERE w.id= {$id}
			
			")->row();

		$json = [];
		
		if($tran->comm_from != "ex" && ($tran->type == 'sale_commission' || $tran->type == 'vendor_sale_commission' && $tran->comm_from == 'store' && $val != 0)){
			$order_status = $this->db->query("select order_status_id from orders_history where order_id=". $tran->reference_id." order by id DESC")->row_array();
		}
		
		if(isset($order_status) && $order_status['order_status_id'] != 1) {
			$data['invalid_order_status'] = true;
			$data['id'] = $id;

			$json['ask_confirm'] = $tran;

			$json['html'] = $this->load->view("admincontrol/users/part/confirmstatus",$data,true);
		} else if(!$confirm) {
			$dataCollection = $this->Wallet_model->getDeleteData((int)$id);
			
			$table = "<div class='transaction-datails-div-hidden' style='display:none;'><table class='table table-stripped'><thead style='width: calc( 100% - 1em )'><tr style='display: table;  width: 100%; table-layout: fixed;'><td class='text-center'>".__('user.transaction_id')."</td><td class='text-center'>".__('user.username')."</td><td class='text-center'>".__('user.amount')."</td></tr></thead><tbody style=' display: block; max-height: 200px; overflow-y: auto;'>";
			
			$amountTotal = 0;
			
			foreach ($dataCollection as $datas) {
				
				$amountTotal += ($datas['amount'] > 0) ? $datas['amount'] : 0; 
				
				$table .= '<tr style="display: table; width: 100%; table-layout: fixed;"><td class="text-center">'. $datas['id'] .'</td><td class="text-center">'. $datas['name'] .'</td><td class="text-center">'. c_format($datas['amount']) .'</td></tr>';
			}
			
			$table .= "</tbody></table></div>";
			
			$html .= "<p><strong>".__('user.status_for')." ".count($dataCollection)."</strong> ".__('user.transactions_having_total_amount')." <strong>".c_format($amountTotal)."</strong> ".__('user.wil_be_updated')." <a href='javascript:void(0)' class='show-trans-aff-details'><br>".__('user.see_details')."</a></p>";
			
			$html .= $table;
			
			$data['transactions_details'] = $html;
			$json['ask_confirm'] = $tran;
			$data['status'] = $val;
			$data['tran'] = $tran;
			$data['id'] = $id;
			$json['html'] = $this->load->view("admincontrol/users/part/confirmstatus",$data,true);
		} else {

			if($tran->type == 'sale_commission' && $tran->comm_from == 'ex'){
				$this->db->query("UPDATE integration_orders SET status = {$val} WHERE id=". $tran->reference_id_2 );
			}
			
			
			if($val == 1){
				$tran->comment = str_replace('Clicked done from ip_message', '', $tran->comment);
				
				$notificationData = array(
					
					'notification_url'          => 'mywallet',
					
					'notification_type'         => 'wallet',
					
					'notification_title'        => c_format($tran->amount) ." Credited in your wallet",
					
					'notification_view_user_id' => $tran->user_id,
					
					'notification_viewfor'      => 'user',
					
					'notification_actionID'     => $tran->id,
					
					'notification_description'  => $tran->comment,
					
					'notification_is_read'      => '0',
					
					'notification_created_date' => date('Y-m-d H:i:s'),
					
					'notification_ipaddress'    => $_SERVER['REMOTE_ADDR']
				);
				
				$this->load->model('Mail_model');
				
				$this->Mail_model->wallet_noti_in_wallet($tran);
				
				$this->insertnotification($notificationData);
				
			} else {
				
				$notificationData = array(
					
					'notification_url'          => 'mywallet',
					
					'notification_type'         => 'wallet',
					
					'notification_title'        => "Transactions status changed",
					
					'notification_view_user_id' => $tran->user_id,
					
					'notification_viewfor'      => 'user',
					
					'notification_actionID'     => $tran->id,
					
					'notification_description'  => "Transactions #{$id} status changed to ". ($val == 1 ? 'In Wallet' : 'On Hold') .". Amount is " . c_format($tran->amount),
					
					'notification_is_read'      => '0',
					
					'notification_created_date' => date('Y-m-d H:i:s'),
					
					'notification_ipaddress'    => $_SERVER['REMOTE_ADDR']
				);
				
				$this->insertnotification($notificationData);
				
				$this->load->model('Mail_model');
				
				$this->Mail_model->wallet_noti_on_hold_wallet($tran);
				
			}
			
			$this->db->query("UPDATE wallet SET status = {$val},commission_status = 0 WHERE group_id =". $tran->group_id);
			
			$json['success'] = true;
		}

		echo json_encode($json);
	}
}

public function info_remove_tran(){
	$userdetails = $this->userdetails();
	if(empty($userdetails) || empty($userdetails['is_vendor'])) redirect('usercontrol/dashboard');

	$id = (int) $this->input->post("id",true);
	$wallet = $this->Wallet_model->getbyId($id);

	$market_vendor = $this->Product_model->getSettings('market_vendor');
	if(($wallet->comm_from == 'ex' && empty($wallet->is_action) 
		&& $wallet->reference_id_2 != '__general_click__'  && $market_vendor['marketvendorexternalordercampaign'])
		||  ($wallet->is_action && $market_vendor['marketvendoractionscampaign'])
		||  ($wallet->reference_id_2 == '__general_click__' && $market_vendor['marketvendorclickcampaign'])){

		$delete_id = (int) $this->input->post("id",true);
	
	$dataCollection = $this->Wallet_model->getDeleteData($delete_id);



	$html = "";

	$html = '<h6 class="text-center">'.__('user.important_this_action_can_not_be_undo').'</h6>';

	$html .= '<hr>';

	$table = "<div class='transaction-datails-div-hidden' style='display:none;'><table class='table table-stripped'><thead style='width: calc( 100% - 1em )'><tr style='display: table;  width: 100%; table-layout: fixed;'><td class='text-center'>".__('user.transaction_id')."</td><td class='text-center'>".__('user.username')."</td><td class='text-center'> ".__('user.amount')."</td></tr></thead><tbody style=' display: block; max-height: 200px; overflow-y: auto;'>";
	
	$amountTotal = 0;
	
	foreach ($dataCollection as $data) {

		$amountTotal += ($data['amount'] > 0) ? $data['amount'] : 0; 

		$table .= '<tr style="display: table; width: 100%; table-layout: fixed;"><td class="text-center">'. $data['id'] .'</td><td class="text-center">'. $data['name'] .'</td><td class="text-center">'. c_format($data['amount']) .'</td></tr>';
	}

	$table .= "</tbody></table></div>";

	$html .= "<p><strong>".count($dataCollection)."</strong> ".__('user.transactions_having_total_amount')." <strong>".c_format($amountTotal)."</strong> ".__('user.will_get_deleted')." <a href='javascript:void(0)' class='show-trans-aff-details'><br>".__('user.see_details')."</a></p>";

	$html .= $table;

	$html .= "<br><div class='row'> <div class='col-sm-6'><button data-bs-dismiss='modal' class='btn btn-primary btn-block'>".__('user.cancel')."</button></div> <div class='col-sm-6'><button class='btn btn-danger  btn-block' delete-tran-confirm='". $delete_id ."'>".__('user.yes_confirm')."</button></div> </div>";


	$json['html'] = $html;

	echo json_encode($json);
}
}

public function confirm_remove_tran(){
	$userdetails = $this->userdetails();
	if(empty($userdetails) || empty($userdetails['is_vendor'])) redirect('usercontrol/dashboard');

	$id = (int) $this->input->post("id",true);
	$wallet = $this->Wallet_model->getbyId($id);

	$market_vendor = $this->Product_model->getSettings('market_vendor');
	if(($wallet->comm_from == 'ex' && empty($wallet->is_action) 
		&& $wallet->reference_id_2 != '__general_click__'  && $market_vendor['marketvendorexternalordercampaign'])
		||  ($wallet->is_action && $market_vendor['marketvendoractionscampaign'])
		||  ($wallet->reference_id_2 == '__general_click__' && $market_vendor['marketvendorclickcampaign'])){
		
		$json['dataCollection'] = $dataCollection = $this->Wallet_model->getDeleteData($id);

	foreach ($dataCollection as $data) {

		foreach ($data['removed'] as $key => $value) {
			if(isset($value['query']) && $value['query']) $this->db->query($value['query']);
		}

		if(isset($data['details']) && ! empty($data['details'])) {
			$this->load->model('Product_model');
			$this->Product_model->delete_wallet_integration_clicks_action($data['details']);
		}

		if(isset($data['id']) && !empty($data['id'])) {
			$this->db->query("DELETE FROM wallet_recursion WHERE transaction_id = ". $data['id']);

			$this->db->query("DELETE FROM wallet_requests WHERE FIND_IN_SET(".$data['id'].",tran_ids)");

				$this->db->query("DELETE FROM wallet WHERE parent_id = ". $data['id']);

				$this->db->query("DELETE FROM wallet WHERE id = ". $data['id']);
			}
		}

		echo json_encode($json);
	}
}

public function info_recursion_tran(){
	$userdetails = $this->userdetails();
	if(empty($userdetails) || empty($userdetails['is_vendor'])) redirect('usercontrol/dashboard');

	$id = (int) $this->input->post("id",true);
	$wallet = $this->Wallet_model->getbyId($id);

	$market_vendor = $this->Product_model->getSettings('market_vendor');
	if(($wallet->comm_from == 'ex' && empty($wallet->is_action) 
		&& $wallet->reference_id_2 != '__general_click__'  && $market_vendor['marketvendorexternalordercampaign'])
		||  ($wallet->is_action && $market_vendor['marketvendoractionscampaign'])
		||  ($wallet->reference_id_2 == '__general_click__' && $market_vendor['marketvendorclickcampaign'])){

		$mainID = $this->input->post("id",true);
	
	$dataCollection = $this->Wallet_model->getDeleteData((int)$mainID, true);

	$table = "<div class='transaction-datails-div-hidden' style='display:none;'><table class='table table-stripped'><thead style='width: calc( 100% - 1em )'><tr style='display: table;  width: 100%; table-layout: fixed;'><td class='text-center'>".__('user.transaction_id')."</td><td class='text-center'>".__('user.username')."</td><td class='text-center'> ".__('user.amount')."</td></tr></thead><tbody style=' display: block; max-height: 200px; overflow-y: auto;'>";
	
	$amountTotal = 0;
	
	foreach ($dataCollection as $data) {

		$amountTotal += ($data['amount'] > 0) ? $data['amount'] : 0;

		$table .= '<tr style="display: table; width: 100%; table-layout: fixed;"><td class="text-center">'. $data['id'] .'</td><td class="text-center">'. $data['name'] .'</td><td class="text-center">'. c_format($data['amount']) .'</td></tr>';
	}

	$table .= "</tbody></table></div>";

	$html .= "<p><strong>".__('user.recursion_setting_for')." ".count($dataCollection)."</strong> ".__('user.transactions_having_total_amount')." <strong>".c_format($amountTotal)."</strong> ".__('user.wil_be_updated')." <a href='javascript:void(0)' class='show-trans-aff-details'><br>".__('user.see_details')."</a></p>";

	$html .= $table;
	
	$data['transactions_details'] = $html;
	
	$wallet_data = $this->Wallet_model->getbyId((int)$mainID);

	$recursion = $this->Wallet_model->GetTransactionRecursion($wallet_data->id);		


	$recursion_type	= array(

		"every_day"   => __("user.every_day"),

		"every_week"  => __("user.every_week"),

		"every_month" => __("user.every_month"),

		"every_year"  => __("user.every_year"),

		"custom_time" => __("user.custom_time")

	);



	$minutes = $recursion['custom_time'];

	$day = floor ($minutes / 1440);

	$hour = floor (($minutes - $day * 1440) / 60);

	$minute = $minutes - ($day * 1440) - ($hour * 60);



	$data['day'] = $day;

	$data['hour'] = $hour;

	$data['minute'] = $minute;

	$data['recursion_type'] = $recursion_type;

	$data['wallet_data'] = $wallet_data;

	$recursion['endtime'] = ($recursion['endtime'] == "0000-00-00 00:00:00") ? null : $recursion['endtime'];

	$data['recursion'] = $recursion;

	$json['html'] = $this->load->view("admincontrol/users/part/recurring", $data,true);

	$json['recursion_type'] = $recursion['type'];

	echo json_encode($json);
}
}

public function getRecurringTransaction(){
	$userdetails = $this->userdetails();
	if(empty($userdetails)){ redirect('/login'); }

	$id = (int)$this->input->post('id');
	$filter = array(
		//'user_id' => $userdetails['id'],
		'parent_id' => $id,
	);

	$data['recurring'] = $id;
	$data['request_status'] = $this->Wallet_model->request_status;
	$data['status'] = $this->Wallet_model->status();
	$data['status_icon'] = $this->Wallet_model->status_icon;
	$data['userdetails'] = $userdetails;
	$data['transaction'] = $this->Wallet_model->getTransaction($filter);

	
	$json['table'] = '';
	foreach ($data['transaction'] as $key => $value) {
		$data['class'] = 'child-recurring';
		$data['force_class'] = $_POST['ischild'] == 'true' ? 'child-arrow' : '';
		$data['recurring'] = $id;
		$data['value'] = $value;
		$data['wallet_status'] = $data['status'];
		$json['table'] .= $this->load->view("usercontrol/users/parts/new_wallet_tr", $data, true);
	}


	echo json_encode($json);
}
public function form(){
	$userdetails = $this->userdetails();
	if(empty($userdetails)){ redirect('/login'); }

	$store_setting = $this->Product_model->getSettings('store');
	if(!$store_setting['status']){ show_404(); }
	
	$this->load->model("Form_model");
	$data['forms'] = $this->Form_model->getForms($userdetails['id']);	

	foreach ($data['forms'] as $key => $value) { 			 
		$data['forms'][$key]['coupon_name'] = $this->Form_model->getFormCouponname(($value['coupon']) ? $value['coupon'] : 0);
		$data['forms'][$key]['public_page'] = base_url('form/'.$value['seo'].'/'.base64_encode($this->userdetails()['id']));
		$data['forms'][$key]['count_coupon'] = $this->Form_model->getFormCouponCount($value['form_id'],$this->userdetails()['id']);
		$data['forms'][$key]['coupon_code'] = $this->Form_model->getFormCouponCode($value['coupon']);
		$data['forms'][$key]['seo'] = str_replace('_', ' ', $value['seo']) ;
	}
	$this->load->view('usercontrol/includes/header', $data);
	$this->load->view('usercontrol/form/index', $data);
	$this->load->view('usercontrol/includes/footer', $data);
}
public function generateformcode($form = 0){
	$userdetails = $this->userdetails();
	if(empty($userdetails)){ redirect('/login'); }

	else {
		if($form){
			$data['form_id'] = $form;
			$data['user_id'] = $userdetails['id'];
			$this->load->model("Form_model");
			$data['getForm'] 	= $this->Form_model->getForm($form);

			$this->load->view('usercontrol/form/generatecode', $data);
		}
	}
}
public function category_auto(){
	$userdetails = $this->userdetails();
	if(!$this->userdetails()){ redirect('/', 'refresh'); }
	$keyword = $this->input->get('query');
	

	$data = $this->db->query("SELECT id as value,name as label FROM categories WHERE name  like ". $this->db->escape("%".$keyword."%") ." ")->result_array();
	

	echo json_encode($data);die;
}

public function store_products(){
	$userdetails = $this->userdetails();

	if(empty($userdetails)) redirect('usercontrol/dashboard');

	$vendor_setting = $this->Product_model->getSettings('vendor');
	$store_setting = $this->Product_model->getSettings('store');
	if(!isset($userdetails['is_vendor']) || $userdetails['is_vendor'] != 1 || (int)$vendor_setting['storestatus'] != 1 || (int)$store_setting['status'] != 1) redirect('usercontrol/dashboard');

	
	if($store_setting['store_mode'] == 'sales') redirect('usercontrol/store_dashboard');

	$filter = array(
		'seller_id' => $userdetails['id'],
	);
	$get = $this->input->get(null,true);

	if(isset($get['category_id']) && $get['category_id'])
		$filter['category_id'] = (int)$this->input->get('category_id');

	$data['default_commition'] =$this->Product_model->getSettings('productsetting');
	$data['productlist'] = $this->Product_model->getAllProductForVendor($userdetails['id'], $userdetails['type'],$filter);

	$this->load->library("socialshare");				
	$data['social_share_modal'] =  $this->socialshare->get_dynamic_social_share_btns();

	$userPlan = App\MembershipUser::with("plan")->where('is_active',1)->where('user_id',$userdetails['id'])->first();
	$vendor_product_count = $this->Product_model->countByField('product_affiliate','user_id',$userdetails['id']);
	if(isset($userPlan->plan->product) && $userPlan->plan->product <= $vendor_product_count){
		$this->load->helper('cookie');
		$cookie = get_cookie('product_count_alert_'.$userdetails['id']);
		$data['product_count_alert'] = __('user.reached_maximum_limit_package_upgrade').' <a href="'.base_url('usercontrol/purchase_plan').'">'.__('user.here').'</a>';
	}
	$data['currentTheme'] = User::getActiveTheme();
	$data['StoreStatus'] = User::getStoreStatus();

	$vendormanagereview= $this->db->query("SELECT * FROM setting WHERE  setting_key='vendormanagereview' and setting_type='market_vendor'")->row();
	$data['vendormanagereview'] = isset($vendormanagereview) && $vendormanagereview->setting_value==1 ? 1 : 0;
	$vendormanagereviewimage=$this->db->query("SELECT * FROM setting WHERE  setting_key='vendormanagereviewimage' and setting_type='market_vendor'")->row();
	$data['vendormanagereviewimage'] = isset($vendormanagereviewimage) && $vendormanagereviewimage->setting_value==1 ? 1 : 0;
	$this->view($data,'store/store_products','usercontrol');
}

public function sales_products(){

	$userdetails = $this->userdetails();

	if(empty($userdetails)) redirect('usercontrol/dashboard');

	$vendor_setting = $this->Product_model->getSettings('vendor');
	$store_setting = $this->Product_model->getSettings('store');

	if(!isset($userdetails['is_vendor']) || $userdetails['is_vendor'] != 1 || (int)$vendor_setting['storestatus'] != 1 || (int)$store_setting['status'] != 1) redirect('usercontrol/dashboard');

	if($store_setting['store_mode'] == 'cart') redirect('usercontrol/store_dashboard');

	$filter = array(
		'seller_id' => $userdetails['id'],
	);
	$get = $this->input->get(null,true);

	if(isset($get['category_id']) && $get['category_id'])
		$filter['category_id'] = (int)$this->input->get('category_id');

	$data['default_commition'] =$this->Product_model->getSettings('productsetting');
	$data['productlist'] = $this->Product_model->getAllSaleProductForVendor($userdetails['id'], 'admin',$filter);

	$this->load->library("socialshare");				
	$data['social_share_modal'] =  $this->socialshare->get_dynamic_social_share_btns();

	$userPlan = App\MembershipUser::with("plan")->where('is_active',1)->where('user_id',$userdetails['id'])->first();
	$vendor_product_count = $this->Product_model->countByField('product_affiliate','user_id',$userdetails['id']);
	if(isset($userPlan->plan->product) && $userPlan->plan->product <= $vendor_product_count){
		$this->load->helper('cookie');
		$cookie = get_cookie('product_count_alert_'.$userdetails['id']);
		$data['product_count_alert'] = __('user.reached_maximum_limit_package_upgrade').' <a href="'.base_url('usercontrol/purchase_plan').'">'.__('user.here').'</a>';
	}

	$data['currentTheme'] = User::getActiveTheme();
	$data['StoreStatus'] = User::getStoreStatus();

	$this->view($data,'store/sale_products','usercontrol');
}

public function update_sale_products($id = null){

		$userdetails = $this->userdetails();

		if(empty($userdetails)) redirect('usercontrol/dashboard');

		$this->load->model('Product_model');

		$product = $this->Product_model->getProductById($id);

		$product = json_decode(json_encode($product), true);

		$data['product'] = $this->Product_model->productDataWithMeta($product);

		$data['CurrencySymbol'] = $this->currency->getSymbol();

		if($data['product']){

			$data['seller'] = $this->db->query("SELECT * FROM product_affiliate WHERE product_id=". (int)$product['product_id'] ." ")->row();

			$data['seller_setting'] = $this->db->query("SELECT * FROM vendor_setting WHERE user_id=". (int)$data['seller']->user_id ." ")->row();

			$data['product_state'] = $this->db->query("SELECT * FROM states WHERE id=". (int)$data['product']['state_id'] )->row();

			$data['states'] = $this->db->query("SELECT * FROM states WHERE country_id=". (int)$data['product_state']->country_id )->result();

			$data['categories'] =$this->Product_model->getProductCategory($product['product_id']);

		}

		$data['checkout_template'] = get_available_checkout_template();

		$data['setting'] = $this->Product_model->getSettings('productsetting');

		$data['vendor_setting'] = $this->Product_model->getSettings('vendor');

		$data['country_list'] = $this->db->query("SELECT name,id FROM countries")->result();

		$this->view($data,'store/create_sale_products','usercontrol');
}

public function bulkProductImportFromUrl() 
{
	$userdetails = $this->userdetails();
	if(empty($userdetails)) redirect('usercontrol/dashboard');

		$f_result = [
		'products_available' => 0,
		'products_managed' => 0,
		'status' => 'danger',
		'message' => 'something went wrong, please try again!',
		'data'  => [],
		'dataPreview' => ""
	];
	
	$bulkResult = [];
	$json=array();
	$post = $this->input->post(null,true); 
	if(!isset($post['txt_xmlurl'])){

		$json['warning'] = __('user.please_enter_xml_url'); 

	} 
	else {

		$xmlurl = $post['txt_xmlurl'];
		$featchurldata=file_get_contents($xmlurl);
		$xml=simplexml_load_string($featchurldata);

		if($xml)
		{
		 	$products=$xml;
 			if(isset($products))
 			{
 				foreach($products as $product) 
				{
					$productArray = [];
					foreach($product as $key => $value) 
					{
						$xmlobjvalue= (string)$value[0];
						if(isset($xmlobjvalue)) 
						{
							$productArray[$key] = $xmlobjvalue != null ? $xmlobjvalue : '';
						} 
						else {
							$productArray[$key] = '';
						} 
					} 

					if(!empty($productArray)) {
						$cdata = $this->initialProductImportCheck($productArray);
						$cdata['row'] = $productArray;
						$bulkResult[] = $cdata;
					} 
				}
 			}
 			else
 				$json['warning'] = __('user.not_valid_xm_format'); 
					
		}
		else 
		{  
			$json['warning'] = __('user.url_entered_not_valid_xml_content');
		}

	}


	$data['action'] = 'confirm';
	$data['products'] = $bulkResult;
	echo $this->load->view('admincontrol/product/bulk_upload_modal', $data, true);
}

public function bulkProductImport() {

	require_once APPPATH . '/core/phpspreadsheet/autoload.php';
	$extension="";
	if(!isset($_FILES['file']['error']) || $_FILES['file']['error'] != 0)
	{
		$json['warning'] = "Please Select Excel or Xml File..!";

	} else {

		$extension = pathinfo($_FILES['file']["name"], PATHINFO_EXTENSION);

		if($extension == 'xlsx' || $extension == 'xml')
		{}
		else
		{
			$json['warning'] = "Only xlsx or Xml files are allowed.!";
		}
	}
 
	$f_result = [
		'products_available' => 0,
		'products_managed' => 0,
		'status' => 'danger',
		'message' => 'something went wrong, please try again!',
		'data'  => [],
		'dataPreview' => ""
	];
	
	$bulkResult = [];

	if(!isset($json['warning'])){

		$inputFileName = $_FILES['file']['tmp_name'];

		if($extension == 'xlsx')
		{

			$objReader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
			
			$objPHPExcel = $objReader->load($inputFileName);

			$sheet = $objPHPExcel->getSheet(0); 
			$highestRow = $sheet->getHighestRow(); 
			$highestColumn = $sheet->getHighestColumn();

			$xlsdata = [];            
			for ($row = 1; $row <= $highestRow; $row++){ 
				$xlsdata[] = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE)[0];
			}

			$indexData = $this->getProductXlsIndex($xlsdata[0]);
	
			for($proIndex = 1; $proIndex < sizeof($xlsdata); $proIndex++) 
			{
				
				$productArray = [];
				
				foreach($indexData as $key => $value) {
					if(isset($xlsdata[$proIndex][$value])) {
						$productArray[$key] = $xlsdata[$proIndex][$value] != null ? $xlsdata[$proIndex][$value] : '';
					} else {
						$productArray[$key] = '';
					}
				}

				if(!empty($productArray)) {
					$cdata = $this->initialProductImportCheck($productArray);
					$cdata['row'] = $productArray;
					$bulkResult[] = $cdata;
				}
			}
		}
		else if($extension == 'xml')
		{
			$xml = simplexml_load_file($inputFileName);
			if ($xml === false) 
			{
				$xmlerrrostring="";
				  $json['warning'] = "Failed loading XML!";
			  foreach(libxml_get_errors() as $error) 
			  {
			    $xmlerrrostring.= "<br>". $error->message;
			  }

			  $json['warning'] =$xmlerrrostring;
			} 
			else 
			{
				$products=$xml;
				foreach($products as $product) 
				{
					$productArray = [];
					foreach($product as $key => $value) 
					{
				  		$xmlobjvalue= (string)$value[0];
				  		if(isset($xmlobjvalue)) 
				  		{
							$productArray[$key] = $xmlobjvalue != null ? $xmlobjvalue : '';
						} else {
							$productArray[$key] = '';
						} 
				  	} 

				  	if(!empty($productArray)) 
				  	{
						$cdata = $this->initialProductImportCheck($productArray);
						if(isset($cdata) && is_array($cdata))
						$productArray['product_status']=$cdata['data']['product_status'];

						$cdata['row'] = $productArray;
						$bulkResult[] = $cdata;
					} 
				}
			}
		}

	} 
 
	
	$data['action'] = 'confirm';
	$data['products'] = $bulkResult;
	echo $this->load->view('admincontrol/product/bulk_upload_modal', $data, true);
}

public function initialProductImportCheck($post){

	try {
		
		$userdetails = $this->userdetails();
		
		if(!empty($post)){

			unset($this->validation);

			$product_id = (int) $post['product_id'];

			$product_status=0;		
			

			if($product_id > 0) 
			{
				$product_exist = $this->db->query('select product_status from product where product_created_by='.$userdetails['id'].' AND product_id='.$product_id)->row_array();
				if(empty($product_exist))
				 {
				 	 
					return [
						"status" => "error",
						"message" => "Product not available having Product ID you provided!"
					];

					$marketaddnewstoreproduct= $this->db->query("SELECT * FROM setting WHERE  setting_key='marketaddnewstoreproduct' and setting_type='market_vendor'")->row();
					if(isset($marketaddnewstoreproduct) && $marketaddnewstoreproduct->setting_value==1)
 							$product_status=0;
 					else 
 							$product_status=1;	
				}
				else
				{
					 
					 $product_status=$product_exist["product_status"];	
				}
			}
			else
			{
					$marketaddnewstoreproduct= $this->db->query("SELECT * FROM setting WHERE  setting_key='marketaddnewstoreproduct' and setting_type='market_vendor'")->row();
					if(isset($marketaddnewstoreproduct) && $marketaddnewstoreproduct->setting_value==1)
 							$product_status=0;
 					else 
 							$product_status=1;	
			}
 

			$this->load->helper(array('form', 'url'));
			
			$this->load->library('form_validation');
			
			$this->form_validation->reset_validation();

			$this->form_validation->set_rules('product_name', __('user.product_name_'), 'required');
			
			$this->form_validation->set_rules('product_description', __('user.product_description'), 'required');
			
			$this->form_validation->set_rules(
				
				'product_short_description', __('user.short_description'),
				
				'required|min_length[5]|max_length[150]',
				
				array(
					
					'required'      => 'Enter %s',
					
					'is_unique'     => 'This %s already exists.',
					
					'min_length'    => '%s: the minimum of characters is %s',
					
					'max_length'    => '%s: the maximum of characters is %s',
					
				)
				
			);
			
			$this->form_validation->set_rules('product_price', 'Product Price', 'required');
			
			$this->form_validation->set_rules('product_sku', 'Product SKU', 'required');
			
			if($post['allow_country'] == "1"){
				
				$this->form_validation->set_rules('state_id', 'State', 'required' );
				
			}
			
			$this->form_validation->set_data($post);
			
			if($this->form_validation->run()){
				
				$errors = array();
				
				if(isset($post['product_id']) && !empty($post['product_id']) && $post['product_id'] != 0){
					$pro_exist = $this->db->query('select product_id from product where product_created_by='.$userdetails['id'].' AND product_id='.$post['product_id'])->row_array();
					if(empty($pro_exist)) {
						$errors['product_id'] = "Product not available having Product ID you provided!";
					}
				}
				
				
				if(empty($post['product_variations']) || $post['product_variations'] == "[]") {
					$post['product_variations'] = json_encode([]);
				} else {
					$validJson = true;
					
					try{
						$variationJson = json_decode($post['product_variations']);
						$validJson = json_last_error() === JSON_ERROR_NONE;
					} catch(Exception $e) {
						$validJson = false;
					}
					
					if(!$validJson || !is_array($variationJson)) {
						$errors['product_variations'] = "Invalid json string provided for Product Variation!";
					}
				}
				
				if($post['allow_country'] == "1"){
					if($product_id > 0) {
						$state_exist = $this->db->query('select id from states where id='.$post['state_id'])->row_array();
						if(empty($state_exist)) {
							$errors['state_id'] = "State not available having State ID you provided!";
						}
					}
				}

			
				
				if(empty($errors)){
					
					$details = array(
						'product_id' => $post['product_id'],
						
						'product_name'                 =>  $post['product_name'],
						
						'product_description'          =>  $post['product_description'],
						
						'product_short_description'    =>  $post['product_short_description'],
						
						'product_msrp'                 =>  $post['product_msrp'],
						
						'product_price'                =>  $post['product_price'],
						
						'product_sku'                  =>  $post['product_sku'],
						
						'product_type'                 =>  $post['product_type'],
						
						'state_id'                     =>  $post['allow_country'] == "1" ? (int)$post['state_id'] : 0,
						
						'product_commision_type'       =>  'default',

						'product_commision_value'      =>  0,
						
						'product_click_commision_type' =>  'default',
						
						'product_click_commision_ppc'  =>  0,
						
						'product_click_commision_per'  =>  0,
						
						'on_store'                     =>  (int)$post['on_store'],
						
						'allow_shipping'               =>  (int)$post['allow_shipping'],
						
						'allow_upload_file'            =>  (int)$post['allow_upload_file'],
						
						'allow_comment'                =>  (int)$post['allow_comment'],
						
						'product_status'               =>  $product_status,
						
						'product_ipaddress'            =>  $_SERVER['REMOTE_ADDR'],
						
						'product_recursion_type'       =>  '',
						
						'recursion_endtime'       =>  null,
						
						'product_recursion'            =>  '',
						
						'recursion_custom_time'        =>  0,
						
						'product_variations'        =>  $post['product_variations'],
						
						'product_tags'        =>  json_encode($post['product_tags']),
						
						'product_created_by' => $userdetails['id']
						
					);
					
					if(isset($post['product_id']) && !empty($post['product_id']) && $post['product_id'] != 0){
						
						return [
							"status" => "Warning",
							"message" => "<span class='badge bg-warning'>update</span>",
							"data" => $details
						];

					} else {
						return [
							"status" => "Warning",
							"message" => "<span class='badge bg-success'>create</span>",
							"data" => $details
						];
					}
				} else {
					return [
						"status" => "error",
						"errors" => $errors
					];
				}
			} else {
				return [
					"status" => "error",
					"errors" => $this->form_validation->error_array()
				];
			}
		} else {
			return [
				"status" => "error",
				"errors" => ["Something went wrong"]
			];
		}
	} catch (Exception $e) {
		return [
			"status" => "error",
			"errors" => [$e->getMessage()]
		];
	}
}

public function bulkProductImportConfirm() {
	$data = json_decode(base64_decode($_POST['products']), true);
	
	$result = [
		'total_products' => 0,
		'created_products' => 0,
		'updated_products' => 0,
		'failed_products' => 0,
		'skipped_products' => 0,
		'details' => []
	];
	
	foreach($data as $d) {
		if($d['status'] !== 'error') {
			$r = $this->createUpdateImportedProduct($d['data']);
			if(isset($r['created'])) {
				$result['created_products']++;
			} else if(isset($r['updated'])) {
				$result['updated_products']++;
			} else {
				$result['failed_products']++;
			}
			
			$result['details'][] = [
				'product' => $d['data'],
				'result' => $r
			];
		} else {
			$result['skipped_products']++;
		}
		$result['total_products']++;
	}
	
	echo $this->load->view('admincontrol/product/bulk_upload_modal', $result, true);
}

public function createUpdateImportedProduct($post){

	try {
		
		$json['status'] = false;
		
		$userdetails = $this->userdetails();

		$old_product_data =[];
		
		$details = $post;
		
		

		if(isset($post['product_id']) && !empty($post['product_id']) && $post['product_id'] != 0){
			$product_id = $post['product_id'];

			unset($details['product_id']);

			$this->Product_model->update_data('product', $details, array('product_id' => $product_id));
			$details['product_created_date'] = date('Y-m-d H:i:s');
			
			$json['updated'] = true;
			$json['status'] = true;
			$json['success'] = __('user.product_updated_successfully');

		} else {

			copy('assets/images/dummy-product-img.jpg','assets/images/product/upload/thumb/dummy-product-img.jpg');
			$details['product_featured_image'] = 'dummy-product-img.jpg';
			$details['product_created_date'] = date('Y-m-d H:i:s');
			$details['product_updated_date'] = date('Y-m-d H:i:s');
			$product_id = $this->Product_model->create_data('product', $details);
			$json['created'] = true;
			$json['status'] = true;
			$json['success'] = __('user.product_added_successfully');

			$general_category = $this->db->query('SELECT id FROM categories WHERE name="General"')->row_array();
			
			if(!empty($general_category)) {
				$general_category_id = $general_category['id'];
			} else {
				copy('assets/images/dummy-product-img.jpg','assets/images/product/upload/thumb/dummy-product-img.jpg');
				$general_category_id = $this->Product_model->create_data('categories', array(
					'name'        =>  "General",
					'description' =>  "This is general products category.",
					'parent_id'   =>  0,
					'color'   	  =>  "#FFFFFF",
					'tag'   	  => 1,
					'slug'      => $this->friendly_seo_string('General-0')
				));
			}

			$category = array(
				'product_id' => $product_id,
				'category_id' => $general_category_id,
			);
			
			$this->Product_model->create_data('product_categories', $category);

			$store_setting = $this->Product_model->getSettings('store');

			if($store_setting['status']) {
				
				$notificationData = array(

					'notification_url'          => '/listproduct/'.$product_id,
					
					'notification_type'         =>  'product',
					
					'notification_title'        =>  __('user.new_product_added_in_affiliate_program'),
					
					'notification_view_user_id' =>  'all',
					
					'notification_viewfor'      =>  'user',
					
					'notification_actionID'     =>  $product_id,
					
					'notification_description'  =>  $post['product_name'].' product is addded by admin in affiliate Program on '.date('Y-m-d H:i:s'),
					
					'notification_is_read'      =>  '0',
					
					'notification_created_date' =>  date('Y-m-d H:i:s'),
					
					'notification_ipaddress'    =>  $_SERVER['REMOTE_ADDR']
					
				);

				$this->insertnotification($notificationData);
				
			}
			
			if($post['product_created_by'] !== 1) {
				$seller_comm = [
					'admin_sale_commission_type'      => "default",
					'admin_commission_value'          => 0,
					'admin_click_commission_type'     => "default",
					'admin_click_amount'              => 0,
					'admin_click_count'               => 0,
					'affiliate_click_commission_type' => "default",
					'affiliate_click_count'           => 0,
					'affiliate_click_amount'          => 0,
					'affiliate_sale_commission_type'  => "default",
					'affiliate_commission_value'      => 0,
				];
				
				$seller = $this->db->query("SELECT * FROM product_affiliate WHERE product_id=". (int)$product_id ." ")->row();
				
				$this->Product_model->assignToSellerForce($product_id, $details, $post['product_created_by'], '', 'admin', $seller_comm);
			}
		}


		$seofilename = $this->friendly_seo_string($post['product_name']);
		$seofilename = strtolower($seofilename);
		$product_slug = $seofilename.'-'.$product_id;
		$this->db->query("UPDATE product SET product_slug = ". $this->db->escape($product_slug) ." WHERE product_id =". $product_id);
		
	} catch (Exception $e) {
		$json['status'] = false;
		$json['errors'] = $e->getMessage();
	}
	
	return $json;
	die;
}

private function getProductXlsIndex($xlsHeaders) {
	$headers = $this->productXLSheaders();
	$newHeaders = [];
	foreach($headers as $key => $value) {
		$newHeaders[$key] = array_search($value, $xlsHeaders);
	}
	
	return $newHeaders;
}

private function productXLSheaders() {
	return array(
		'product_id' => 'Product ID',

		'product_name' => 'Product Name',
		'product_sku' => 'Product Sku',
		'product_msrp' => 'Product MSRP',
		'product_price' => 'Product Price',
		'product_short_description' => 'Product Short Desc',
		'product_description' => 'Product Description',
		'product_tags' => 'Product Tags',
		'product_type' => 'Product Type',
		'product_variations' => 'Product Variations',
		
		'allow_comment' => 'Allow Comment',
		'allow_shipping' => 'Allow Shipping',
		'allow_upload_file' => 'Allow File Upload',
		'on_store' => 'Allow on Store',
		'state_id' => 'State ID',

	);
}

public function exportproduct(){

	$userdetails = $this->userdetails();
	if(empty($userdetails)) redirect('usercontrol/dashboard');

	$store_setting = $this->Product_model->getSettings('store');
	
	$json['structure_only'] = $structure_only = $this->input->post('structure_only');

	$filter = array(
		'seller_id' => $userdetails['id'],
	);
	
	if($structure_only == 1) {
		$productlist = [];
	} else {
		$productlist = $this->Product_model->getAllProduct($userdetails['id'], $userdetails['type'],$filter);
	}

	$header = $this->productXLSheaders();

	$index = 0;

	$_exportData = array();

	$_exportData[$index] = array_values($header);

	require_once APPPATH . '/core/phpspreadsheet/autoload.php';

	foreach ($productlist as $key => $value) {

		$index++;

		foreach ($header as $name_key => $_value) {
			$val = '';

			if(isset($value[$name_key])){

				switch ($name_key) {
					case 'product_tags':
					$t = ( is_array(json_decode($value[$name_key], true)) ? json_decode($value[$name_key], true): [] );
					$val = implode(",", $t);
					break;
					default:
					$val = $value[$name_key];
					break;
				}
			} 

			$_exportData[$index][$name_key] = $val;

		}

	}


    $objPHPExcel = new \PhpOffice\PhpSpreadsheet\Spreadsheet();

	$objPHPExcel->getActiveSheet()->fromArray($_exportData, NULL, 'A1');

    $objWriter = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($objPHPExcel);

	$alphas = range('A', 'Z');


	foreach(range('A',$alphas[count($header)]) as $columnID) {

		$objPHPExcel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
		$objWriter->save(FCPATH.'assets/xml/export_products_structure.xlsx');

	}

	if($structure_only == 1) {
		$json['download'] = base_url('assets/xml/export_products_structure.xlsx');
	} else {
		$objWriter->save(FCPATH.'assets/xml/export_products.xlsx');
		$json['download'] = base_url('assets/xml/export_products.xlsx');
	}

	echo json_encode($json);
	
	exit;
}

public function exportproductXML(){
	$userdetails = $this->userdetails();
	if(empty($userdetails)) redirect('usercontrol/dashboard');
	$store_setting = $this->Product_model->getSettings('store');
	$json['structure_only'] = $structure_only = $this->input->post('structure_only');
	$filter = array(
		'seller_id' => $userdetails['id'],
	);
	
	if($structure_only == 1) {
		$productlist = [];
	} else {
		$productlist = $this->Product_model->getAllProduct($userdetails['id'], $userdetails['type'],$filter);
	}

	$header = $this->productXLSheaders();

	$dom = new DOMDocument();
	$dom->encoding = 'utf-8';
	$dom->xmlVersion = '1.0';
	$dom->formatOutput = true;
	$root = $dom->createElement('products');
	
	if($structure_only == 1) 
	{
		$product_node = $dom->createElement('product');
		foreach ($header as $name_key => $_value) 
		{
			if($name_key!='product_short_description' && $name_key!='product_description' )
			{
				$child_node_title = $dom->createElement($name_key, $_value);
				 $product_node->appendChild($child_node_title);
			}
			else
			{
				$child_node_title = $dom->createElement($name_key);
				$cdataname     = $dom->createCDATASection($_value);
				$child_node_title->appendChild($cdataname);
				$product_node->appendChild($child_node_title);
				 
			}

		}
		$root->appendChild($product_node);
		$dom->appendChild($root);
		$dom->save(FCPATH.'assets/xml/export_vendor_products_structure.xml');
		$json['download'] = base_url('assets/xml/export_vendor_products_structure.xml');
		
	}
	else
	{

		$index = 0;
		$_exportData = array();
		$_exportData[$index] = array_values($header);
		foreach ($productlist as $key => $value) 
		{
			$product_node = $dom->createElement('product');
			$index++;
			foreach ($header as $name_key => $_value) 
			{
				$val = '';

				if(isset($value[$name_key])){

					switch ($name_key) {
						case 'product_tags':
						$t = ( is_array(json_decode($value[$name_key], true)) ? json_decode($value[$name_key], true): [] );
						$val = implode(",", $t);
						break;
						default:
						$val = $value[$name_key];
						break;
					}
				} 

				if($name_key!='product_short_description' && $name_key!='product_description' )
				{
					 $child_node_title = $dom->createElement($name_key, $val);
					 $product_node->appendChild($child_node_title);
				}
				else
				{
					
					$child_node_title = $dom->createElement($name_key);
					$cdataname     = $dom->createCDATASection($val);
					$child_node_title->appendChild($cdataname);
					$product_node->appendChild($child_node_title);
					 
				}
  			}

  			$root->appendChild($product_node);
		}

		$dom->appendChild($root);
		$dom->save(FCPATH.'assets/xml/export_vendor_products.xml');
		$json['download'] = base_url('assets/xml/export_vendor_products.xml');	
	}
  
	echo json_encode($json);
 	exit;
}

public function downloadprodcutxmlstructurefile($filename = NULL) {
    $userdetails = $this->userdetails();
	if(empty($userdetails)) redirect('usercontrol/dashboard');
    $this->load->helper('download');
    $data = file_get_contents(FCPATH.'assets/xml/export_vendor_products_structure.xml');
    force_download("export_vendor_products_structure.xml", $data);
}

public function downloadprodcutxmlfile($filename = NULL) {
    $userdetails = $this->userdetails();
	if(empty($userdetails)) redirect('usercontrol/dashboard');
    $this->load->helper('download');
    $data = file_get_contents(FCPATH.'assets/xml/export_vendor_products.xml');
    force_download("export_vendor_products.xml", $data);
}

public function check_duplicate_store() {
	$userdetails = $this->userdetails();
	$data = $this->input->post(null,true);
	$data['store_name'] = urldecode($data['store_name']);
	$json = [
		'store_name' => $data['store_name']
	];
	if(isset($data['store_name']) && !empty($data['store_name'])) {
		$store_name = $data['store_name'];
		$store_slug = slugifyThis($store_name);
		$checkStorename = $this->db->query("SELECT id FROM users WHERE store_slug like '".$store_slug."' AND id!=".$userdetails['id'])->num_rows();
		if($checkStorename > 0){ 
			$json['error'] = __('user.store_name_already_exists'); 
		}
	}
	echo json_encode($json); die;
}

public function store_setting(){
	$userdetails = $this->userdetails();

	if(empty($userdetails)) redirect('usercontrol/dashboard');

	$vendor_setting = $this->Product_model->getSettings('vendor');

	$store_setting = $this->Product_model->getSettings('store');

	$data['CurrencySymbol'] = $this->currency->getSymbol();
	
	if(!isset($userdetails['is_vendor']) || $userdetails['is_vendor'] != 1 || (int)$vendor_setting['storestatus'] != 1 || (int)$store_setting['status'] != 1) redirect('usercontrol/dashboard');

	if($this->input->server('REQUEST_METHOD') == 'POST'){
		$json = [];
		$data = $this->input->post(null,true);

		if(isset($data['store_page_settings']) && $data['store_page_settings'] == 1) {
			$updateData = [];
			$storeMeta = [];
			if(isset($data['store_name']) && !empty($data['store_name'])) {
				$store_name = $data['store_name'];
				$store_contact_us_map = $data['store_contact_us_map'];
				$store_address = $data['store_address'];
				$store_email = $data['store_email'];
				$store_contact_number = $data['store_contact_number'];
				$store_terms_condition = $data['store_terms_condition'];
				$store_slug = slugifyThis($store_name);
				$checkStorename = $this->db->query("SELECT id FROM users WHERE store_slug like '".$store_slug."' AND id!=".$userdetails['id'])->num_rows();
				if($checkStorename > 0){ 
					$json['errors']['store_name'] = "Store Name Already Exist"; 
					echo json_encode($json); die;
				}

				$updateData = [
					'store_name' => $store_name,
					'store_contact_us_map' => $store_contact_us_map,
					'store_address' => $store_address,
					'store_email' => $store_email,
					'store_contact_number' => $store_contact_number,
					'store_terms_condition' => $store_terms_condition,
					'store_slug' => $store_slug
				];
			} else {
				$updateData = [
					'store_name' => null,
					'store_slug' => null
				];
			}

			$storeMeta['cover_text_color'] = (isset($data['cover_text_color']) && !empty($data['cover_text_color'])) ? $data['cover_text_color'] : "#FFFFFF";

			$storeMeta['cover_show_vendor_name'] = (int)$data['cover_show_vendor_name'] ?? 0;

			$filesForUpload = ['store_logo', 'cover_background'];

			if (!file_exists('path/to/directory')) {
				mkdir('assets/user_upload/vendor_store', 0644, true);
			}

			foreach($filesForUpload as $file) {	
				$ext = pathinfo($_FILES[$file]['name'], PATHINFO_EXTENSION);

				if(isset($_FILES[$file]['name']) && !empty($_FILES[$file]['name'])){
					if( !in_array($ext, ['jpg','png','jpeg']) && $category_id == 0){
						$json['errors'][$file] = 'Only image file are allowed';
						echo json_encode($json); die;
					} else if(!empty($_FILES[$file]['name'])){
						$upload_response = $this->upload_photo($file,'assets/user_upload/vendor_store');
						if($upload_response['success']){
							$storeMeta[$file] = $upload_response['upload_data']['file_name'];
						}else{
							$json['errors'][$file] = $upload_response['msg'];
							echo json_encode($json); die;
						}
					}
				}
			}

			$updateData['store_meta'] = !empty($storeMeta) ? json_encode($storeMeta) : null;

			$this->db->where('id', $userdetails['id']);
			$this->db->update('users', $updateData);
			$json['success'] = __('user.settings_updated_successfully');
			$json['store_page_url'] = base_url('store/').$store_slug;
		} else {
			$update = [
				'vendor_status'                       => $data['vendor_status'],
				'affiliate_click_count'               => $data['affiliate_click_count'],
				'affiliate_click_amount'              => $data['affiliate_click_amount'],
				'affiliate_sale_commission_type'      => $data['affiliate_sale_commission_type'],
				'affiliate_commission_value'          => $data['affiliate_commission_value'],
				'form_affiliate_click_count'          => $data['form_affiliate_click_count'],
				'form_affiliate_click_amount'         => $data['form_affiliate_click_amount'],
				'form_affiliate_sale_commission_type' => $data['form_affiliate_sale_commission_type'],
				'form_affiliate_commission_value'     => $data['form_affiliate_commission_value'], 
				'user_id'                             => (int)$userdetails['id'],
			];

			$id = $this->db->query("SELECT * FROM vendor_setting WHERE user_id=". (int)$userdetails['id'] ." ")->row();
			
			if($id){
				$this->db->update("vendor_setting", $update, ['user_id'=> (int)$userdetails['id'] ]);
			} else{
				$this->db->insert("vendor_setting", $update);
			}
			
			$json['success'] = __('user.setting_saved_successfully');
		}

		echo json_encode($json);die;
	}

	$data['store_details'] = $this->db->query('SELECT store_name, store_contact_us_map, store_address, store_email, store_contact_number, store_terms_condition,store_slug, store_meta FROM users WHERE id='.$userdetails['id'])->row_array();

	$data['setting'] = $this->db->query("SELECT * FROM vendor_setting WHERE user_id=". (int)$userdetails['id'] ." ")->row_array();
	
	$this->view($data,'store/store_setting','usercontrol');
}

public function contact_us(){
	$userdetails = $this->userdetails();
	if(empty($userdetails)){ redirect('/login'); }

	$userdashboard_settings = $this->Common_model->getUserDashboardSettings();

	if(! isShowUserControlParts($userdashboard_settings['contact_us_page'])) {
		show_404();
	}

	if ($this->input->server('REQUEST_METHOD') == 'POST'){
		$this->load->library('form_validation');
		$this->form_validation->set_rules('subject', 'Subject', 'required');
		$this->form_validation->set_rules('body', 'Mail Body', 'required' );

		if($this->form_validation->run()){
			$data = $this->input->post(null);

			if(isset($_FILES['attachment']) && !empty($_FILES['attachment']['name'])) {
				$fileNameArray = explode('.', $_FILES['attachment']['name']);

				$config['upload_path'] = 'assets/user_upload';

				$config['allowed_types'] = 'png|gif|jpeg|jpg|PNG|GIF|JPEG|JPG|ICO|ico|zip|doc|docs|pdf|xls|xlsx|ppt|pptx|txt';

				$config['max_size']      = 2048;

				$this->load->helper('string');

				$config['file_name']  = random_string('alnum', 32);

				$this->load->library('upload', $config);

				$this->upload->initialize($config);

				if (!$this->upload->do_upload('attachment')) {
					$errors = $this->upload->display_errors();
				} else {
					$data['attachment'] = base_url().'assets/user_upload/'.$config['file_name'].".".$fileNameArray[sizeof($fileNameArray)-1];
				}
			}

			if(!isset($errors) || empty($errors)) {
				$this->load->model('Mail_model');
				$data['email'] = $userdetails['email'];
				$data['firstname'] = $userdetails['firstname'];
				$data['lastname'] = $userdetails['lastname'];
				$mailRes = $this->Mail_model->send_store_contact_vendor($data);
				if(str_contains($mailRes, 'successfully')) {
					$json['success'] = $mailRes;
				} else {
					$json['errors'] = $mailRes;
				}
			} else {
				$json['errors']['attachment'] = $errors;
			}
		}else{
			$json['errors'] = $this->form_validation->error_array();
		}

		echo json_encode($json);die;
	}

	$data['notcheckapproval'] = 1; 
	$data['notcheckmember'] = 1;

	$data['userdetails'] = $this->Product_model->userdetails('user');
	$data['domain'] = base_url('/');
	$data['user_mobile'] = '';
	

	$this->load->model('PagebuilderModel');
	$register_form = $this->PagebuilderModel->getSettings('registration_builder');
	if($register_form){
		$customField = json_decode($register_form['registration_builder'],1);
		

		foreach ($customField as $_key => $_value) {
			$field_name = 'custom_'. $_value['name'];
			if(!isset($json['errors'][$field_name]) && $_value['mobile_validation']  == 'true'){
				$custom_val = json_decode($data['userdetails']['value'],1);
				$data['user_mobile'] = isset($custom_val[$field_name]) ? $custom_val[$field_name] : '';
			}
		}
	}

	$this->view($data,'store/store_contact','usercontrol');
}

public function create(){
	$userdetails = $this->userdetails();
	if(empty($userdetails)){ redirect('/login'); }
	$vendor_setting = $this->Product_model->getSettings('vendor');
	if((int)$vendor_setting['storestatus'] == 0) show_404();

	$data['checkout_template'] = get_available_checkout_template();
	$data['vendor_setting'] = $vendor_setting;
	$data['setting'] 	= $this->Product_model->getSettings('productsetting');
	$data['product'] = $this->Product_model->getProductById($product_id);
	$data['tags'] = $this->Product_model->getAllTags();
	$data['CurrencySymbol'] = $this->currency->getSymbol();

	if($data['product']){
		$data['seller'] = $this->db->query("SELECT * FROM product_affiliate WHERE product_id=". (int)$data['product']->product_id ." ")->row();
		if(!$data['seller'] || $data['seller']->user_id != $userdetails['id']){
			show_404();
		}

		$data['categories'] =$this->Product_model->getProductCategory($data['product']->product_id);
		$data['downloads'] = $this->Product_model->parseDownloads($data['product']->downloadable_files,$data['product']->product_type);
		$data['product_state'] = $this->db->query("SELECT * FROM states WHERE id=". (int)$data['product']->state_id )->row();
		$data['states'] = $this->db->query("SELECT * FROM states WHERE country_id=". (int)$data['product_state']->country_id )->result();
	}

	$data['country_list'] = $this->db->query("SELECT name,id FROM countries")->result();
	$data['seller_setting'] = $this->db->query("SELECT * FROM vendor_setting WHERE user_id=". (int)$userdetails['id'] ." ")->row();
		$this->view($data,'store/create_sale_products','usercontrol');
	}

	public function save_sale_product(){

		$userdetails = $this->userdetails();

		$post = $this->input->post(null,true);

		$vendor_setting = $this->Product_model->getSettings('vendor');
		if((int)$vendor_setting['storestatus'] == 0) show_404();

		if(!empty($post)){

			$product_id = (int)$this->input->post('product_id',true);

			$this->load->helper(array('form', 'url'));

			$this->load->library('form_validation');

			$this->form_validation->set_rules('product_url', __('user.product_purchase_url'), 'required');

			$this->form_validation->set_rules('product_name', __('user.product_name_'), 'required');

			$this->form_validation->set_rules('category[]', "Category", "required");

			$this->form_validation->set_rules('product_description', __('user.product_description'), 'required' );

			$this->form_validation->set_rules('product_price', 'Product Price', 'required');

			$this->form_validation->set_rules('product_sku', 'Product SKU', 'required');

			
			if((isset($post['product_sale_period']) && !empty($post['product_sale_period'])) || (isset($post['product_sale_period_price']) && !empty($post['product_sale_period_price']))) {
				$this->form_validation->set_rules('product_sale_period', 'Sale Durataion', 'required');
				$this->form_validation->set_rules('product_sale_price', 'Sale Duration Price', 'required|numeric|greater_than[0]');
			}


			if($post['allow_country'] == "on"){

				$this->form_validation->set_rules('state_id', 'State', 'required' );

			}

			if( $post['product_recursion_type'] == 'custom' ){

				$this->form_validation->set_rules('product_recursion', 'Product Recursion', 'required');

				if( $post['product_recursion'] == 'custom_time' ){

					$this->form_validation->set_rules('recursion_custom_time', 'Custom Time', 'required|greater_than[0]');

				}

			}

			$product_recursion = ($post['product_recursion_type'] && $post['product_recursion_type'] != 'default') ? $post['product_recursion'] : "";

			$recursion_custom_time = ($product_recursion == 'custom_time' ) ? $post['recursion_custom_time'] : 0;


			if($this->form_validation->run()){

				$post = $this->input->post(null,true);			

				$errors = array();


				$downloadable_files = array();
				if($product_id){
					$product_details = $this->Product_model->getProductById($product_id);
					$_downloads = $this->Product_model->parseDownloads($product_details->downloadable_files,$product_details->product_type);
					foreach($_downloads as $key=> $value) {
						$isKeep =false;
						foreach($post['keep_files'] as $innerKey =>$innerValue) {
							if($innerValue == $key) {
								$downloadable_files[] = $_downloads[$key];
								unset($post['keep_files'][$innerKey]);
								$isKeep=true;
							}
							
						}
						if(!$isKeep){
							@unlink(APPPATH.'/downloads/'.$key);
						}
					}
				}

				$details = array(
					'is_campaign_product'		   => 1,
					'product_url'				   => $post['product_url'],
					'product_name'                 =>  $post['product_name'],
					'product_msrp'                 =>  $post['product_msrp'],
					'product_description'          =>  $post['product_description'],
					'product_price'                =>  $post['product_price'],
					'product_sku'                  =>  $post['product_sku'],
					'product_price'                =>  $post['product_price'],
					'product_type'                 =>  $post['product_type'],
					'state_id'                     =>  $post['allow_country'] == "on" ? (int)$post['state_id'] : 0,
					'product_commision_type'       =>  'default',
					'product_commision_value'      =>  0,
					'product_click_commision_type' =>  'default',
					'product_click_commision_ppc'  =>  0,
					'product_click_commision_per'  =>  0,
					'on_store'                     =>  (int)$post['on_store'],
					'allow_shipping'               =>  (int)$post['allow_shipping'],
					'allow_upload_file'            =>  (int)$post['allow_upload_file'],
					'allow_comment'                =>  (int)$post['allow_comment'],
					'product_status'               =>  isset($post['product_status']) ? (int)$post['product_status'] : 1,
					'product_ipaddress'            =>  $_SERVER['REMOTE_ADDR'],
					'product_recursion_type'       =>  $post['product_recursion_type'],
					'recursion_endtime'       =>  (isset($post['recursion_endtime_status']) && $post['recursion_endtime']) ? date("Y-m-d H:i:s",strtotime($post['recursion_endtime'])) : null,
					'product_recursion'            =>  $product_recursion,
					'recursion_custom_time'        =>  (int)$recursion_custom_time,
				);
				//use for update product on review
				$market_vendor = $this->Product_model->getSettings('market_vendor','marketaddnewstoreproduct');
				if($market_vendor['marketaddnewstoreproduct']){
					$details['product_status'] = 0;
				}else{
					$details['product_status'] = 1;
				}

				if($_FILES['product_featured_image']['error'] != 0 && $product_id == 0 ){

					$errors['product_featured_image'] = 'Select Featured Image File!';

				} else if(!empty($_FILES['product_featured_image']['name'])){

					$upload_response = $this->upload_photo('product_featured_image','assets/images/product/upload/thumb');

					if($upload_response['success']){

						$details['product_featured_image'] = $upload_response['upload_data']['file_name'];

					}else{

						$errors['product_featured_image'] = $upload_response['msg'];

					}
				}

				if(!empty($_FILES['downloadable_file'])){

					$files = $_FILES['downloadable_file'];

					if(isset($_FILES['downloadable_file']['name']) && is_array($_FILES['downloadable_file']['name']))
					$count_file = count($_FILES['downloadable_file']['name']);
					else
					$count_file=0;


					$this->load->helper('string');	

					for($i=0; $i<$count_file; $i++){

						$extension = pathinfo($files['name'][$i], PATHINFO_EXTENSION);
						if(!empty(trim($files['name'][$i]))){
							if($extension=='zip'){

								$FILES['downloadable_files']['name'] = md5(random_string('alnum', 10));

								$FILES['downloadable_files']['type'] = $files['type'][$i];

								$FILES['downloadable_files']['tmp_name'] = $files['tmp_name'][$i];

								$FILES['downloadable_files']['error'] = $files['error'][$i];

								$FILES['downloadable_files']['size'] = $files['size'][$i];    


								if(empty($FILES['downloadable_files']['error'])){

									move_uploaded_file($FILES['downloadable_files']['tmp_name'], APPPATH.'/downloads/'. $FILES['downloadable_files']['name']);

									$downloadable_files[] = array(

										'type' => $FILES['downloadable_files']['type'],

										'name' => $FILES['downloadable_files']['name'],

										'mask' => $files['name'][$i],

									);
									$details['downloadable_files']=json_encode($downloadable_filess);
								}else{

									$errors['downloadable_files'] = $FILES['downloadable_files']['error'];
								}

							} else {

								$zip = new ZipArchive();

								$zip_name = md5(random_string('alnum', 10));

								if ($zip->open(APPPATH.'/downloads/'.$zip_name, ZipArchive::CREATE) !== TRUE) {
									$errors['downloadable_files'] = "Sorry ZIP creation is not working currently.";
								}

								$zip->addFromString($files['name'][$i], file_get_contents($files['tmp_name'][$i]));

								$zip->close(); 

								$downloadable_files[] = array(

									'type' => 'application/x-zip-compressed',

									'name' =>$zip_name,

									'mask' => preg_replace('/\\.[^.\\s]{3,4}$/', '', $files['name'][$i]).'.zip',

								);
							}

						}
					}
										
					$details['downloadable_files']=json_encode($downloadable_files);

				}

				$userPlan = App\MembershipUser::with("plan")->where('is_active',1)->where('user_id',$userdetails['id'])->first();
				$plan_product_count = ($product_id) ? $userPlan->plan->product : $userPlan->plan->product - 1;
				$vendor_product_count = $this->Product_model->countByField('product_affiliate','user_id',$userdetails['id']);
				if(isset($userPlan->plan->product) && $plan_product_count < $vendor_product_count)
					$errors['upgrade_membership_plan'] = __('user.reached_maximum_limit_package_upgrade').' <a href="'.base_url('usercontrol/purchase_plan').'">'.__('user.here').'</a>';
				
				if(empty($errors)){

					$old_product_data =[];

					if($product_id){
						$old_product_data = $this->db->query("SELECT * FROM product WHERE product_id = ". (int)$product_id)->row_array();
						$details['product_updated_date'] = date('Y-m-d H:i:s');
						$this->Product_model->update_data('product', $details, array('product_id' => $product_id));
						$this->session->set_flashdata('success', __('user.product_campaign_updated_successfully'));
					} else {
						$details['product_created_by'] = $userdetails['id'];
						$details['product_updated_date'] = date('Y-m-d H:i:s');

						//Used for new product review
						$market_vendor = $this->Product_model->getSettings('market_vendor','marketaddnewstoreproduct');
						if($market_vendor['marketaddnewstoreproduct'])
							$details['product_status'] = 0;
						else 
							$details['product_status'] = 1;


						$details['product_created_date'] = date('Y-m-d H:i:s');

						$product_id = $this->Product_model->create_data('product', $details);
						$new_product_created = true;

						$notificationData = array(
							'notification_url'          => 'updateproduct/'.$product_id,
							'notification_type'         =>  'vendor_product',
							'notification_title'        =>  __('user.new_product_added_by_vendor'),
							'notification_viewfor'      =>  'admin',
							'notification_actionID'     =>  $product_id,
							'notification_description'  =>  $post['product_name'].' product is addded by '. $userdetails['username'] .' in store on '.date('Y-m-d H:i:s'),
							'notification_is_read'      =>  '0',
							'notification_created_date' =>  date('Y-m-d H:i:s'),
							'notification_ipaddress'    =>  $_SERVER['REMOTE_ADDR']
						);

						$this->insertnotification($notificationData);

						$this->session->set_flashdata('success', __('user.product_campaign_added_successfully'));
					}

					$seofilename = $this->friendly_seo_string($post['product_name']);

					$seofilename = strtolower($seofilename);

					$product_slug = $seofilename.'-'.$product_id;

					$this->db->query("UPDATE product SET product_slug = ". $this->db->escape($product_slug) ." WHERE product_id =". $product_id);

					$seller = '';

					if($product_id){

						$this->db->query("DELETE FROM product_categories WHERE product_id = {$product_id}");
						$this->db->query("DELETE FROM product_meta WHERE related_product_id = {$product_id}");

						if(isset($post['product_sale_period']) && !empty($post['product_sale_period']) && isset($post['product_sale_price']) && !empty($post['product_sale_price'])) {
							$product_sale_period = explode(" - ", $post['product_sale_period']);
							$post['product_sale_start'] = date('Y-m-d H:i:s', strtotime($product_sale_period[0])); 
							$post['product_sale_end'] = date('Y-m-d H:i:s', strtotime($product_sale_period[1])); 
							$post['product_sale_price'] = $post['product_sale_price'];
						}


						if(isset($post['product_reviewer_name']) && !empty($post['product_reviewer_name'])) {
							$post['product_reviews'] = [];

							foreach ($post['product_reviewer_name'] as $key => $value) {
								array_push($post['product_reviews'], [
									'name' => $value,
									'comment' => $post['product_reviewer_comment'][$key],
								]);
							}

							$post['product_reviews'] = json_encode($post['product_reviews']);
						}
						if(isset($post['product_footer_name']) && !empty($post['product_footer_name'])) {
							$post['product_footer'] = [];

							foreach ($post['product_footer_name'] as $key => $value) {
								array_push($post['product_footer'], [
									'name' => $value,
									'description' => $post['product_footer_description'][$key],
								]);
							}

							$post['product_footer'] = json_encode($post['product_footer']);
						}

						$metaKeys = ['checkout_template', 'show_to_affiliates', 'product_launching_datetime', 'product_quantity', 'product_sale_start', 'product_sale_end', 'product_sale_price', 'product_checkout_terms', 'product_reviews','product_footer', 'show_to_featured'];

						foreach ($metaKeys as $metaKey) {
							if(isset($post[$metaKey]) && $post[$metaKey] != ""){

								if($metaKey === 'product_launching_datetime') {
									$post[$metaKey] = date('Y-m-d H:i:s', strtotime($post[$metaKey]));
								}

								$this->Product_model->create_data('product_meta', array(
									'related_product_id' => $product_id,
									'meta_key' => $metaKey,
									'meta_value' => $post[$metaKey]
								));
							}
						}

						
						if(isset($post['category']) && is_array($post['category'])){

							foreach ($post['category'] as $category_id) {

								$category = array(

									'product_id' => $product_id,

									'category_id' => $category_id,

								);

								$this->Product_model->create_data('product_categories', $category);
							}
						}

						$admin_comment = '';

						if(isset($post['admin_comment']) && $post['admin_comment']){

							$admin_comment = $post['admin_comment'];

						}

						$seller_comm = [
						'affiliate_click_commission_type' => $post['affiliate_click_commission_type'],
						'affiliate_click_count'           => $post['affiliate_click_count'],
						'affiliate_click_amount'          => $post['affiliate_click_amount'],
						'affiliate_sale_commission_type'  => $post['affiliate_sale_commission_type'],
						'affiliate_commission_value'      => $post['affiliate_commission_value'],
					];

					$this->Product_model->assignToSeller($product_id, $details, $userdetails['id'], $admin_comment,'affiliate', $seller_comm);

					if(empty($market_vendor['marketaddnewstoreproduct'])){
						$vendor_setting = $this->Product_model->getSettings('vendor');
						$seller = $this->db->query("SELECT * FROM product_affiliate WHERE product_id=".$product_id." ")->row();

						$seller_comm = [

							'admin_sale_commission_type'      => $vendor_setting['admin_sale_commission_type'],

							'admin_commission_value'          => $vendor_setting['admin_commission_value'],

							'admin_click_commission_type'     => ($seller->admin_click_commission_type) ? $seller->admin_click_commission_type : 'default',

							'admin_click_amount'              => $vendor_setting['admin_click_amount'],

							'admin_click_count'               => $vendor_setting['admin_click_count'],

						];

						$this->Product_model->assignToSeller($product_id, $details, $userdetails['id'], $admin_comment,'admin', $seller_comm);
					}

					$this->load->model('Mail_model');
					if($new_product_created){
						$this->Mail_model->vendor_create_product($product_id);
					} else {
						$product_data = $this->db->query("SELECT * FROM product WHERE product_id = ". (int)$product_id)->row_array();

						if($old_product_data['product_status'] != $product_data['product_status'] && $product_data['product_status'] == 0)
							$this->Mail_model->vendor_product_status_change($product_id, 'admin',true);
					}
				}

				$json['location'] = base_url('usercontrol/sales_products');


				} else {
					$json['errors'] = $errors;
				}

			} else {

				$json['errors'] = $this->form_validation->error_array();

				if(isset($json['errors']['category[]'])){

					$json['errors']['category_auto'] = $json['errors']['category[]'];

				}

			}

			echo json_encode($json);

			die;

		}
	}


	public function integration_code_modal_sale(){
		$userdetails = $this->userdetails();
		if(empty($userdetails)){ redirect('/login'); }
		$data['product'] = $this->db->query('select * from product where product_id='.(int)$this->input->post('id',true))->row();
		echo $this->load->view('admincontrol/product_campaign/integration_code_modal', $data, true);
		die;
	}

public function store_edit_product($product_id = 0){

 	$userdetails = $this->userdetails();
	if(empty($userdetails)){ redirect('/login'); }
	$vendor_setting = $this->Product_model->getSettings('vendor');
	if((int)$vendor_setting['storestatus'] == 0) show_404();

	$data['vendor_setting'] = $vendor_setting;
	$data['setting'] 	= $this->Product_model->getSettings('productsetting');
	$data['product'] = $this->Product_model->getProductById($product_id);
	$data['tags'] = $this->Product_model->getAllTags();
	$data['CurrencySymbol'] = $this->currency->getSymbol();


	if($data['product']){
		$data['seller'] = $this->db->query("SELECT * FROM product_affiliate WHERE product_id=". (int)$data['product']->product_id ." ")->row();
		if(!$data['seller'] || $data['seller']->user_id != $userdetails['id']){
			show_404();
		}

		$data['categories'] =$this->Product_model->getProductCategory($data['product']->product_id);
		$data['downloads'] = $this->Product_model->parseDownloads($data['product']->downloadable_files,$data['product']->product_type);
		$data['product_state'] = $this->db->query("SELECT * FROM states WHERE id=". (int)$data['product']->state_id )->row();
		$data['states'] = $this->db->query("SELECT * FROM states WHERE country_id=". (int)$data['product_state']->country_id )->result();

	}

	$data['country_list'] = $this->db->query("SELECT name,id FROM countries")->result();
	$data['seller_setting'] = $this->db->query("SELECT * FROM vendor_setting WHERE user_id=". (int)$userdetails['id'] ." ")->row();
	 
	$this->view($data,'store/product_form','usercontrol');
}

public function store_save_product(){

	$userdetails = $this->userdetails();
	$post = $this->input->post(null,true);


	$vendor_setting = $this->Product_model->getSettings('vendor');
	if((int)$vendor_setting['storestatus'] == 0) show_404();


	if(!empty($post)){
		$product_id = (int)$this->input->post('product_id',true);
		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');

		$this->form_validation->set_rules('product_name', __('user.product_name_'), 'required');
		$this->form_validation->set_rules('product_description', __('user.product_description'), 'required' );

		if($post['allow_country'] == "1"){
			$this->form_validation->set_rules('state_id', 'State', 'required' );
		}
		$this->form_validation->set_rules('product_short_description', __('user.short_description'),'required|min_length[5]|max_length[150]',
			array(
				'required'      => 'Enter %s',
				'is_unique'     => 'This %s already exists.',
				'min_length' 	=> '%s: the minimum of characters is %s',
				'max_length' 	=> '%s: the maximum of characters is %s',
			)
		);
		$this->form_validation->set_rules('category[]',"Category", "required");
		$this->form_validation->set_rules('product_price', 'Product Price', 'required');
		$this->form_validation->set_rules('product_sku', 'Product SKU', 'required');
		$this->form_validation->set_rules('product_video', 'Product Video', 'trim');

		if( $post['product_recursion_type'] == 'custom' ){
			$this->form_validation->set_rules('product_recursion', 'Product Recursion', 'required');
			if( $post['product_recursion'] == 'custom_time' ){
				$this->form_validation->set_rules('recursion_custom_time', 'Custom Time', 'required|greater_than[0]');
			}
		}

		$variations = [];
		if(isset($post['variations']) && !empty($post['variations'])) {
			foreach($post['variations'] as $key => $value) {
				if(!empty($value)) {
					$new_value = [];
					if($key == 'colors') {
						for ($i=0; $i < sizeof($post['variations'][$key]['code']); $i++) { 
							if(!empty($post['variations'][$key]['code'][$i]) && $post['variations'][$key]['name'][$i]) {
								array_push($new_value, [
									'code'=>$post['variations'][$key]['code'][$i], 
									'name'=> $post['variations'][$key]['name'][$i],
									'price'=> $post['variations'][$key]['price'][$i]
								]);
							}
						}
					} else {
						for ($i=0; $i < sizeof($post['variations'][$key]['name']); $i++) { 
							if(!empty($post['variations'][$key]['name'][$i])) {
								array_push($new_value, [
									'name'=> $post['variations'][$key]['name'][$i],
									'price'=> $post['variations'][$key]['price'][$i]
								]);
							}
						}
					}
					$variations[$key] = $new_value;
				}
			}
		}
		

		$product_recursion = ($post['product_recursion_type'] && $post['product_recursion_type'] != 'default') ? $post['product_recursion'] : "";
		$recursion_custom_time = ($product_recursion == 'custom_time' ) ? $post['recursion_custom_time'] : 0;

		if($this->form_validation->run()){
			$post = $this->input->post(null,true);			


			$errors = array();
			$downloadable_files = array();

			if($product_id){

						$product_details = $this->Product_model->getProductById($product_id);

						$_downloads = $this->Product_model->parseDownloads($product_details->downloadable_files,$product_details->product_type);
						
						foreach ($post['keep_files'] as $key => $_value) {

							if(isset($_downloads[$_value])){
								if($post['product_type'] =='video' && $post['sub_product_type'] =="video"){
									$_downloads[$_value]['videotext'] = $post['videotext'][$key]??null;
									$downloadable_files[] = $_downloads[$_value];
								} else if($post['product_type'] =='video' && $post['sub_product_type'] =="videolink"){ 
									@unlink(APPPATH.'/downloads/'.$_value.".zip");
								} else {
									$downloadable_files[] = $_downloads[$_value];
								}


							} else{

								@unlink(APPPATH.'/downloads/'.$_value);

							}
						}

						if(isset($_downloads) && is_array($_downloads))	
						$allKeys=array_keys($_downloads);
						else
						$allKeys=array();	

						if(isset($post['keep_video_files']) && is_array($_downloads))	
						$keepKeys=array_keys($post['keep_video_files']);
						else
						$keepKeys=array();

						$deletedSectionKeys  = array_diff($allKeys,$keepKeys);
						$deletedSectionKeys = array_values($deletedSectionKeys);
						$_download_new=[];
						if(isset($post['keep_video_files'])) {
							
							foreach($post['keep_video_files'] as $innerKey =>$innerValue) {
								$keepVideo =[];
								for ($i=0; $i < count($innerValue); $i++) { 
									$key = array_search($innerValue[$i], array_column($_downloads[$innerKey]['data'], 'name'));
									if($key!=FALSE || $key ==0) {
										$keepVideo[]=$key;
									}
								}
								$deleteVideoFromSectionKey = array_diff(array_keys($_downloads[$innerKey]['data']),$keepVideo);

								// Remove video from Section
								foreach ($deleteVideoFromSectionKey as $key=>  $value) {
									if(file_exists(APPPATH.'/downloads/'.$_downloads[$innerKey]['data'][$value]['mask'])) {
										@unlink(APPPATH.'/downloads/'.$_downloads[$innerKey]['data'][$value]['mask']);
										@unlink(APPPATH.'/downloads/'.$_downloads[$innerKey]['data'][$value]['zip']['mask']);
									}
									unset($_downloads[$innerKey]['data'][$value]);
								}
								for ($i=0; $i < count($deletedSectionKeys) ; $i++) { 
									foreach ($_downloads[$deletedSectionKeys[$i]]['data'] as $key => $value) {
										if(file_exists(APPPATH."/downloads/").$value['mask']) {
											@unlink(APPPATH."/downloads/".$value['mask']);
											@unlink(APPPATH."/downloads/".$value['zip']['mask']);
										}
									}
									unset($_downloads[$deletedSectionKeys[$i]]);
								}
								// update title  
								$oldVideo= [];
								foreach ($keepVideo as $key => $value) {
									$zip = $_downloads[$innerKey]['data'][$value]['zip']??[];
									$zip['title'] = $post['VideoFileResourceText'][$innerKey][$value]??($_downloads[$innerKey]['data'][$value]['zip']['title']??'');
									$oldVideo[]=[
										'type'=>$_downloads[$innerKey]['data'][$value]['type'],
										'name'=>$_downloads[$innerKey]['data'][$value]['name'],
										'mask'=>$_downloads[$innerKey]['data'][$value]['mask'],
										'size'=>$_downloads[$innerKey]['data'][$value]['size'],
										'videotext'=>$post['videotext'][$innerKey][$value]??$_downloads[$innerKey]['data'][$value]['videotext'],
										'duration'=>$post['duration'][$innerKey][$value]??$_downloads[$innerKey]['data'][$value]['duration'],
										'description'=>$post['description'][$innerKey][$value]??$_downloads[$innerKey]['data'][$value]['description'],
										'zip'=> $zip,
									];
								}
								$_download_new[] = [
									'title'=>$post['section'][$innerKey],
									'data'=>$oldVideo
								];
							}

							
							$downloadable_files =$_download_new;
						}

					}

			$post['product_recursion_type'] = ($post['product_recursion_type']) ? $post['product_recursion_type'] : '';
			
			$details = array(
				'product_name'                 =>  $post['product_name'],
				'product_description'          =>  $post['product_description'],
				'product_short_description'    =>  $post['product_short_description'],
				'product_msrp'                =>  $post['product_msrp'],
				'product_price'                =>  $post['product_price'],
				'product_sku'                  =>  $post['product_sku'],
				'product_video'                =>  $post['product_video'],
				'product_price'                =>  $post['product_price'],
				'product_type'                 =>  $post['product_type'],
				'state_id'                     =>  $post['allow_country'] == "1" ? (int)$post['state_id'] : 0,
				'product_commision_type'       =>  'default',
				'product_commision_value'      =>  0,
				'product_click_commision_type' =>  'default',
				'product_click_commision_ppc'  =>  0,
				'product_click_commision_per'  =>  0,
				'on_store'                     =>  (int)$post['on_store'],
				'allow_shipping'               =>  (int)$post['allow_shipping'],
				'allow_upload_file'            =>  (int)$post['allow_upload_file'],
				'allow_comment'                =>  (int)$post['allow_comment'],
				'product_status'               =>  isset($post['product_status']) ? (int)$post['product_status'] : 1,
				'product_ipaddress'            =>  $_SERVER['REMOTE_ADDR'],
				'product_recursion_type'       =>  $post['product_recursion_type'],
				'recursion_endtime'            =>  (isset($post['recursion_endtime_status']) && $post['recursion_endtime']) ? date("Y-m-d H:i:s",strtotime($post['recursion_endtime'])) : null,
				'product_recursion'            =>  $product_recursion,
				'recursion_custom_time'        =>  (int)$recursion_custom_time,
				'product_variations'        =>  json_encode($variations),
				'product_tags'        =>  json_encode($post['product_tags']),
			);				

			if($_FILES['product_featured_image']['error'] != 0 && $product_id == 0 ){
				$errors['product_featured_image'] = 'Select Featured Image File!';
			}else if(!empty($_FILES['product_featured_image']['name'])){
				$upload_response = $this->upload_photo('product_featured_image','assets/images/product/upload/thumb');
				if($upload_response['success']){
					$details['product_featured_image'] = $upload_response['upload_data']['file_name'];
				}else{
					$errors['product_featured_image'] = $upload_response['msg'];
				}
			}

					if(!empty($_FILES['downloadable_file'])){

						$files = $_FILES['downloadable_file'];

						$count_file = count($_FILES['downloadable_file']['name'])/2;
						$keep_files_count =  isset($post['keep_files']) ?  count($post['keep_files']): 0 ;
 					
						$this->load->helper('string');	

						for($i=0; $i<$count_file; $i++){

							$extension = pathinfo($files['name'][$i], PATHINFO_EXTENSION);
							if(!empty(trim($files['name'][$i]))){
								if($extension=='zip'){

									$FILES['downloadable_files']['name'] = md5(random_string('alnum', 10));

									$FILES['downloadable_files']['type'] = $files['type'][$i];

									$FILES['downloadable_files']['tmp_name'] = $files['tmp_name'][$i];

									$FILES['downloadable_files']['error'] = $files['error'][$i];

									$FILES['downloadable_files']['size'] = $files['size'][$i];    


									if(empty($FILES['downloadable_files']['error'])){

										move_uploaded_file($FILES['downloadable_files']['tmp_name'], APPPATH.'/downloads/'. $FILES['downloadable_files']['name']);

										if($post['product_type']=='video' || $post['sub_product_type']=='videolink') {
											$store_file_temp = [
												'type' => $FILES['downloadable_files']['type'],

												'name' => $FILES['downloadable_files']['name'],

												'mask' => $files['name'][$i]
											];

											if($post['product_type']=='video' && $post['sub_product_type'] !="videolink") {
												$store_file_temp['videotext'] = $post['videotext'][$keep_files_count+$i]; 
											} else {

											}
											$downloadable_files[] = $store_file_temp; 
										} else {

											$downloadable_files[] = array(

												'type' => $FILES['downloadable_files']['type'],

												'name' => $FILES['downloadable_files']['name'],

												'mask' => $files['name'][$i],

											);

										}
									}else{

										$errors['downloadable_files'] = $FILES['downloadable_files']['error'];

									}

								} else {

									$zip_name = md5(random_string('alnum', 10));

									if($post['product_type']=='video' || $post['sub_product_type']=='videolink') {

										$ext = pathinfo($files['name'][$i], PATHINFO_EXTENSION);
										$fileName = md5(random_string('alnum', 10)).".$ext";

										move_uploaded_file($files['tmp_name'][$i], APPPATH.'/downloads/'. $fileName);

										$store_file_temp = [
											'type' => $files['type'][$i],

											'name' => $zip_name,

											'mask' => $fileName,

											'thumb' =>preg_replace('/\\.[^.\\s]{3,4}$/', '', $fileName).'.png',

										];
										if($post['product_type']=='video' && $post['sub_product_type'] !="videolink") {
											$store_file_temp['videotext'] = $post['videotext'][$keep_files_count+$i];
										}
										$downloadable_files[] = $store_file_temp; 
									} else {

										$downloadable_files[] = array(

											'type' => 'application/x-zip-compressed',

											'name' =>$zip_name,

											'mask' => preg_replace('/\\.[^.\\s]{3,4}$/', '', $files['name'][$i]).'.zip',

										);

									}
								}

							}
						}

					}

					$userPlan = App\MembershipUser::with("plan")->where('is_active',1)->where('user_id',$userdetails['id'])->first();
					$plan_product_count = ($product_id) ? $userPlan->plan->product : $userPlan->plan->product - 1;
					$vendor_product_count = $this->Product_model->countByField('product_affiliate','user_id',$userdetails['id']);
					if(isset($userPlan->plan->product) && $plan_product_count < $vendor_product_count)
						$errors['upgrade_membership_plan'] = __('user.reached_maximum_limit_package_upgrade').' <a href="'.base_url('usercontrol/purchase_plan').'">'.__('user.here').'</a>';

					$new_product_created = false;
					$old_product_data = [];


					if(!empty($_FILES['lms_videos_files'])){

						foreach ($_FILES['lms_videos_files']['name'] as $key => $value) {
							if(isset($_FILES['lms_videos_files']['name'][$key]) && !empty($_FILES['lms_videos_files']['name'][$key][0])) {
								$index = $key;
								for ($i=0; $i < count($_FILES['lms_videos_files']['name'][$key]); $i++) { 
									$ext = pathinfo($_FILES['lms_videos_files']['name'][$key][$i], PATHINFO_EXTENSION);
									$fileName = md5(random_string('alnum', 10)).".$ext";
									move_uploaded_file($_FILES['lms_videos_files']['tmp_name'][$key][$i], APPPATH.'/downloads/'. $fileName);
									if(!isset($downloadable_files[$index]) && $index!=0) {
										$index= (count($downloadable_files)-1) < $key ? $key : 0;
									}

									if(isset($post['keep_video_files']))
										$keepvidoefilescount=count($post['keep_video_files'][$index]);
									else 
										$keepvidoefilescount=0;
									
									$store_file_temp = [
										'type' => $_FILES['lms_videos_files']['type'][$key][$i],

										'name' => md5(random_string('alnum', 10)),

										'mask' => $fileName,

										'size' => format_filesize($_FILES['lms_videos_files']['size'][$key][$i]),

										'duration'=> $_POST['lms_videos_files_duration'][$key][$i],

										'videotext'=> $post['videotext'][$index][$keepvidoefilescount+$i],

										'description'=> $post['description'][$index][$keepvidoefilescount+$i] 
									];


									if(!empty($_FILES['lms_videos_files_zip']['name'][$key][$i])) {
										$ext = pathinfo($_FILES['lms_videos_files_zip']['name'][$key][$i], PATHINFO_EXTENSION);
										$fileName = md5(random_string('alnum', 10)).".$ext";
										move_uploaded_file($_FILES['lms_videos_files_zip']['tmp_name'][$key][$i], APPPATH.'/downloads/'. $fileName);

										$store_file_temp['zip']= [
											'name'=> md5(random_string('alnum', 10)),
											'mask'=> $fileName,
											'title'=> $post['VideoFileResourceText'][$index][count($post['keep_video_files'][$index])+$i],
											'type' => $_FILES['lms_videos_files_zip']['type'][$key][$i],
											'size' => format_filesize($_FILES['lms_videos_files_zip']['size'][$key][$i])
										];
									}
									$downloadable_files[$index]['data'][] = $store_file_temp;

								}
							}
							$downloadable_files[$key]['title'] = $post['section'][$key];

						}
					}

					if(!empty($_FILES['lms_videos_files_update'])){
						foreach ($_FILES['lms_videos_files_update']['name'] as $key => $value) {
							if(isset($_FILES['lms_videos_files_update']['name'][$key])) {
								foreach ($_FILES['lms_videos_files_update']['name'][$key] as $oldname => $newFile) {
									$ext = pathinfo($_FILES['lms_videos_files_update']['name'][$key][$oldname], PATHINFO_EXTENSION);
									$fileName = md5(random_string('alnum', 10)).".$ext";
									move_uploaded_file($_FILES['lms_videos_files_update']['tmp_name'][$key][$oldname], APPPATH.'/downloads/'. $fileName);
									foreach($downloadable_files[$key]['data'] as $dkey=>$datavalue) {
										if($datavalue['name'] == $oldname) {

											$downloadable_files[$key]['data'][$dkey]['name'] = md5(random_string('alnum', 10));
											$oldFileName = $downloadable_files[$key]['data'][$dkey]['mask']; 
											$downloadable_files[$key]['data'][$dkey]['mask'] = $fileName;
											$downloadable_files[$key]['data'][$dkey]['type'] = $_FILES['lms_videos_files_update']['type'][$key][$oldname];
											$downloadable_files[$key]['data'][$dkey]['size'] = format_filesize($_FILES['lms_videos_files_update']['size'][$key][$oldname]);
											$downloadable_files[$key]['data'][$dkey]['duration'] = $_POST['lms_videos_files_update_duration'][$key][$oldname];


											if(file_exists(APPPATH.'/downloads/'. $oldFileName)) {
												@unlink(APPPATH.'/downloads/'. $oldFileName);
											}
										}
									}
								}
							}
						}
					}
					if(!empty($_FILES['lms_videos_files_zip_update'])){
						if(isset($_POST['sub_product_type']) && $_POST['sub_product_type']=='videolink'){
							$downloadable_files = $_downloads;
						}
						foreach ($_FILES['lms_videos_files_zip_update']['name'] as $key => $value) {
							if(isset($_FILES['lms_videos_files_zip_update']['name'][$key])) {
								foreach ($_FILES['lms_videos_files_zip_update']['name'][$key] as $oldname => $newFile) {
									$ext = pathinfo($_FILES['lms_videos_files_zip_update']['name'][$key][$oldname], PATHINFO_EXTENSION);
									$fileName = md5(random_string('alnum', 10)).".$ext";
									move_uploaded_file($_FILES['lms_videos_files_zip_update']['tmp_name'][$key][$oldname], APPPATH.'/downloads/'. $fileName);
									foreach($downloadable_files[$key]['data'] as $dkey=>$datavalue) {

										if($datavalue['name'] == $oldname) {
											$downloadable_files[$key]['data'][$dkey]['zip']['name'] = md5(random_string('alnum', 10));
											$oldFileName = $downloadable_files[$key]['data'][$dkey]['zip']['mask']; 
											$downloadable_files[$key]['data'][$dkey]['zip']['mask'] = $fileName;
											$downloadable_files[$key]['data'][$dkey]['zip']['type'] = $_FILES['lms_videos_files_zip_update']['type'][$key][$oldname];
											$downloadable_files[$key]['data'][$dkey]['zip']['size'] = format_filesize($_FILES['lms_videos_files_zip_update']['size'][$key][$oldname]);
											$downloadable_files[$key]['data'][$dkey]['zip']['title']= $post['VideoFileResourceText'][$key][$dkey];
											

											if(file_exists(APPPATH.'/downloads/'. $oldFileName)) {
												@unlink(APPPATH.'/downloads/'. $oldFileName);
											}
										} 
									}
								}
							}
						}
					}

					if(!empty($post['videolink'])) {
						$TmpDownloadable_files = $downloadable_files;
						$downloadable_files=[];
						foreach ($post['sectionlink'] as $key => $value) {
							$tmp['title'] = $value;
							foreach ($post['videolink'][$key] as $keyInner => $InnerValue) {
								if(!empty($post['videolink'][$key][$keyInner]) && !empty($post['videotext'][$key][$keyInner])) {
									$zip =$TmpDownloadable_files[$key]['data'][$keyInner]['zip']??[];
									if(!empty($_FILES['lms_videos_files_zip_update']['name'][$key][$keyInner])) {

										$ext = pathinfo($_FILES['lms_videos_files_zip_update']['name'][$key][$keyInner], PATHINFO_EXTENSION);
										$fileName = md5(random_string('alnum', 10)).".$ext";
										move_uploaded_file($_FILES['lms_videos_files_zip_update']['tmp_name'][$key][$keyInner], APPPATH.'/downloads/'. $fileName);
										$zip = [
											'name'=>md5(random_string('alnum', 10)),
											'mask'=>$fileName,
											'type' => $_FILES['lms_videos_files_zip_update']['type'][$key][$keyInner],
											'size' => format_filesize($_FILES['lms_videos_files_zip']['size'][$key][$keyInner]),
											'title'=> $post['VideoFileResourceText'][$key][$keyInner]
										];
									}

									$tmp['data'][] = [
										'type' => 'link',

										'name' => $TmpDownloadable_files[$key]['data'][$keyInner]['name']??md5(random_string('alnum', 10)),

										'mask' =>$post['videolink'][$key][$keyInner],

										'videotext'=> $post['videotext'][$key][$keyInner],

										'description'=> $post['description'][$key][$keyInner],

										'zip'=>$zip

									];
								}
							} 
							$downloadable_files[] = $tmp;
							$tmp=[];
						}
						$details['product_type'] = 'videolink';
					}

			if(empty($errors)){
				$details['downloadable_files'] = json_encode($downloadable_files);
				$this->session->set_flashdata('success', __('user.product_added_successfully'));

				$details['product_created_by'] = $userdetails['id'];
				$details['product_created_date'] = date('Y-m-d H:i:s');				

				$market_vendor = $this->Product_model->getSettings('market_vendor','marketaddnewstoreproduct');
				if($market_vendor['marketaddnewstoreproduct'])
					$details['product_status'] = 0;
				else 
					$details['product_status'] = 1;
				
				if($product_id){
					$old_product_data = $this->db->query("SELECT * FROM product WHERE product_id = ". (int)$product_id)->row_array();

					$this->Product_model->update_data('product', $details, array('product_id' => $product_id));
				}else{
					$product_id = $this->Product_model->create_data('product', $details);
					$new_product_created = true;

					$notificationData = array(
						'notification_url'          => 'updateproduct/'.$product_id,
						'notification_type'         =>  'vendor_product',
						'notification_title'        =>  __('user.new_product_added_by_vendor'),
						'notification_viewfor'      =>  'admin',
						'notification_actionID'     =>  $product_id,
						'notification_description'  =>  $post['product_name'].' product is addded by '. $userdetails['username'] .' in store on '.date('Y-m-d H:i:s'),
						'notification_is_read'      =>  '0',
						'notification_created_date' =>  date('Y-m-d H:i:s'),
						'notification_ipaddress'    =>  $_SERVER['REMOTE_ADDR']
					);

					$this->insertnotification($notificationData);
				}

				$seofilename = $this->friendly_seo_string($post['product_name']);
				$seofilename = strtolower($seofilename);
				$product_slug = $seofilename.'-'.$product_id;
				$this->db->query("UPDATE product SET product_slug = ". $this->db->escape($product_slug) ." WHERE product_id =". $product_id);

				if($product_id){
					$this->db->query("DELETE FROM product_categories WHERE product_id = {$product_id}");
					if(isset($post['category']) && is_array($post['category'])){
						foreach ($post['category'] as $category_id) {
							$category = array(
								'product_id' => $product_id,
								'category_id' => $category_id,
							);

							$this->Product_model->create_data('product_categories', $category);
						}
					}

					$admin_comment = '';
					if(isset($post['admin_comment']) && $post['admin_comment']){
						$admin_comment = $post['admin_comment'];
					}

					$seller_comm = [
						'affiliate_click_commission_type' => $post['affiliate_click_commission_type'],
						'affiliate_click_count'           => $post['affiliate_click_count'],
						'affiliate_click_amount'          => $post['affiliate_click_amount'],
						'affiliate_sale_commission_type'  => $post['affiliate_sale_commission_type'],
						'affiliate_commission_value'      => $post['affiliate_commission_value'],
					];

					$this->Product_model->assignToSeller($product_id, $details, $userdetails['id'], $admin_comment,'affiliate', $seller_comm);

					if(empty($market_vendor['marketaddnewstoreproduct']))
					{

					

						$vendor_setting = $this->Product_model->getSettings('vendor');
						$seller = $this->db->query("SELECT * FROM product_affiliate WHERE product_id=".$product_id." ")->row();

						$seller_comm = [

							'admin_sale_commission_type'      => $post['admin_sale_commission_type'],

							'admin_commission_value'          => $post['admin_commission_value'],

							'admin_click_commission_type'     => ($seller->admin_click_commission_type) ? $seller->admin_click_commission_type : 'default',

							'admin_click_amount'              => $post['admin_click_amount'],

							'admin_click_count'               => $post['admin_click_count'],

						];


						$this->Product_model->assignToSeller($product_id, $details, $userdetails['id'], $admin_comment,'admin', $seller_comm);
					}

					$this->load->model('Mail_model');
					if($new_product_created){
						$this->Mail_model->vendor_create_product($product_id);
					} else {
						$product_data = $this->db->query("SELECT * FROM product WHERE product_id = ". (int)$product_id)->row_array();

						if($old_product_data['product_status'] != $product_data['product_status'] && $product_data['product_status'] == 0)
							$this->Mail_model->vendor_product_status_change($product_id, 'admin',true);
					}
				}		
 	

				$json['location'] = base_url('usercontrol/store_products');
			} else {
				$json['errors'] = $errors;
			}
		} else {
			$json['errors'] = $this->form_validation->error_array();

			if(isset($json['errors']['category[]'])){
				$json['errors']['category_auto'] = $json['errors']['category[]'];
			}
		}

		echo json_encode($json);die;
	}
}
public function productupload($id = null){
	$userdetails = $this->userdetails();
	if(empty($userdetails)){ redirect('login'); }
	if(empty($id)){
		$this->session->set_flashdata('error', __('user.photo_can_not_be_uploaded'));
		redirect('usercontrol/store_products');
	}

	$vendor_setting = $this->Product_model->getSettings('vendor');
	if((int)$vendor_setting['storestatus'] == 0) show_404();

	if(!empty($_FILES)){
		$errors= array();

		$details = array(
			'product_id'                        =>  $id,
			'product_media_upload_type'         =>  'image',
			'product_media_upload_status'       =>  1,
			'product_media_upload_os'           =>  $this->agent->platform(),
			'product_media_upload_browser'      =>  $this->agent->browser(),
			'product_media_upload_isp'          =>  gethostbyaddr($_SERVER['REMOTE_ADDR']),
			'product_media_upload_ipaddress'    =>  $_SERVER['REMOTE_ADDR'],
			'product_media_upload_created_by'   =>  $userdetails['id'],
			'product_media_upload_created_date' =>  date('Y-m-d H:i:s'),
		);

		$details['product_media_upload_created_by'] = $userdetails['id'];
		if(!empty($_FILES['product_multiple_image'])){
			$files = $_FILES;
			$cpt = count($_FILES['product_multiple_image']['name']);
			

			$this->load->helper('string');
			$config = array(
				'upload_path'   => 'assets/images/product/upload/',
				'allowed_types' => 'png|gif|jpeg|jpg|PNG|GIF|JPEG|JPG',
				'max_size'      => 2048,
				'file_name'     => random_string('alnum', 32),
			);

			$this->load->library('upload', $config);
			$this->load->library('image_lib');
			$this->upload->initialize($config);

			for($i=0; $i<$cpt; $i++){
				if($files['product_multiple_image']['error'][$i] == 0){
					$_FILES['product_multiple_images']['name'] = $files['product_multiple_image']['name'][$i];
					$_FILES['product_multiple_images']['type'] = $files['product_multiple_image']['type'][$i];
					$_FILES['product_multiple_images']['tmp_name'] = $files['product_multiple_image']['tmp_name'][$i];
					$_FILES['product_multiple_images']['error'] = $files['product_multiple_image']['error'][$i];
					$_FILES['product_multiple_images']['size'] = $files['product_multiple_image']['size'][$i];    
					
					$this->upload->do_upload('product_multiple_images');
					$upload_details = $this->upload->data();
					

					$config1 = array(
						'source_image'   => $upload_details['full_path'],
						'new_image'      => 'assets/images/product/upload/thumb',
						'maintain_ratio' => true,
						'width'          => 300,
						'dynamic_output' => 1,
						'height'         => 300
					);

					$this->image_lib->initialize($config1);
					$this->image_lib->resize();
					$this->image_lib->clear();
					

					if($upload_details){
						$details['product_media_upload_path'] = $upload_details['file_name'];
					} else {
						$errors['avatar_error'] = $upload_details['msg'];
					}

					$details['product_media_upload_created_date'] = date('Y-m-d H:i:s');
					$this->Product_model->create_data('product_media_upload', $details);				

				}
			}
		}

		if(!empty($errors)){
			$this->session->set_flashdata('error', $errors['avatar_error']);
			redirect('usercontrol/productupload/'.$id);exit();
		}

		$this->session->set_flashdata('success', __('user.product_images_added_successfully'));
		redirect('usercontrol/productupload/'.$id);
	}

	$data['imageslist'] = $this->Product_model->getAllImages($id);
	$this->view($data,'store/productupload','usercontrol');
}
public function videoupload($id = null){
	$userdetails = $this->userdetails();
	if(empty($userdetails)){ redirect($this->admin_domain_url); }
	if(empty($id)){ redirect('usercontrol/store_products'); }

	$vendor_setting = $this->Product_model->getSettings('vendor');
	if((int)$vendor_setting['storestatus'] == 0) show_404();

	$post = $this->input->post(null,true);

	if(!empty($post)){
		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
		$this->form_validation->set_rules('product_media_upload_video', __('user.product_video'), 'trim');
		if($this->form_validation->run())
		{
			$errors= array();
			$details = array(
				'product_id'                        => $id,
				'product_media_upload_path'         =>  $this->input->post('product_media_upload_path',true),
				'product_media_upload_type'         =>  'video',
				'product_media_upload_status'       =>  1,
				'product_media_upload_os'           =>  $this->agent->platform(),
				'product_media_upload_browser'      =>  $this->agent->browser(),
				'product_media_upload_isp'          =>  gethostbyaddr($_SERVER['REMOTE_ADDR']),
				'product_media_upload_ipaddress'    =>  $_SERVER['REMOTE_ADDR'],
				'product_media_upload_created_by'   =>  $userdetails['id'],
				'product_media_upload_created_date' =>  date('Y-m-d H:i:s'),
			);
			if(!empty($_FILES['video_thumbnail_image']['name'])){
				$upload_response = $this->upload_photo('video_thumbnail_image','assets/images/product/upload/thumb');
				if($upload_response['success']){
					$details['product_media_upload_video_image'] = $upload_response['upload_data']['file_name'];
				}
				else{
					$errors['avatar_error'] = $upload_response['msg'];
				}
			}
			if(!empty($errors)){
				$this->session->set_flashdata('error', $errors['avatar_error']);
				redirect('usercontrol/videoupload/'.$id);
				exit();
			}

			$this->session->set_flashdata('success', __('user.product_video_and_images_added_successfully'));
			$details['product_media_upload_created_by'] = $userdetails['id'];
			$details['product_media_upload_created_date'] = date('Y-m-d H:i:s');
			$this->Product_model->create_data('product_media_upload', $details);
			$data['productinfo'] = $this->Product_model->getProductByIdArray($id);

				redirect('usercontrol/videoupload/'.$id);
			} else {
				$this->session->set_flashdata('error', __('user.form_validation_error'));
				redirect('usercontrol/videoupload/'.$id);
			}

		} else {
			$data['videoimageslist'] = $this->Product_model->getAllVideoImages($id);
			$data['videoslist'] = $this->Product_model->getAllVideos($id);
			$data['user'] = $userdetails;
			

			$this->view($data,'store/videoupload','usercontrol');
		}
	}
	public function deleteAllproducts(){
		$post = $this->input->post(null,true);

		$vendor_setting = $this->Product_model->getSettings('vendor');
		if((int)$vendor_setting['storestatus'] == 0) show_404();
 

		if(!empty($post['product']) || !empty($post['form'])){
			if(isset($post['product'])){
				foreach($post['product'] as $id){
 
 					if($id!='')
 					{
 						$orderProduct = $this->db->query('SELECT id FROM order_products WHERE product_id = '.$id)->row();
 						if(empty($orderProduct)) {
						 	$this->Product_model->deleteproducts((int)$id);
						} else {
							$this->session->set_flashdata('error', __('user.some_order_product_not_deleted'));
						} 
 					}
				}
			}
 
			$this->session->set_flashdata('success', __('user.product_is_deleted_successfully'));
			redirect(base_url() . 'usercontrol/store_products');
		}
		else{
			$id = (int)$this->input->get('delete_id');
			$orderProduct = $this->db->query('SELECT id FROM order_products WHERE product_id = '.$id)->row();
			if(empty($orderProduct)) {
				$res = $this->Product_model->deleteproducts($id);
				$this->session->set_flashdata('success', __('user.product_is_deleted_successfully'));
			} else {
				$this->session->set_flashdata('error', __('user.order_product_not_deleted'));
			}
			redirect(base_url() . 'usercontrol/store_products');
		}

		$this->session->set_flashdata('error', __('user.product_delete_failed'));
		redirect(base_url() . 'usercontrol/store_products');
	}

public function deleteALLSaleproducts(){
		$post = $this->input->post(null,true);

		$vendor_setting = $this->Product_model->getSettings('vendor');
		if((int)$vendor_setting['storestatus'] == 0) show_404();

		if(!empty($post['product']) || !empty($post['form'])){
			if(isset($post['product'])){
				foreach($post['product'] as $id){
					$orderProduct = $this->db->query('SELECT id FROM order_products WHERE product_id = '.$id)->row();

					if(empty($orderProduct)) {
						$this->Product_model->deleteproducts((int)$id);
					} else {
						$this->session->set_flashdata('error', __('user.some_order_product_not_deleted'));
					}
				}
			}

			$this->session->set_flashdata('success', __('user.product_is_deleted_successfully'));
			redirect(base_url() . 'usercontrol/sales_products');
		}
		else{
			$id = (int)$this->input->get('delete_id');
			$orderProduct = $this->db->query('SELECT id FROM order_products WHERE product_id = '.$id)->row();
			if(empty($orderProduct)) {
				$res = $this->Product_model->deleteproducts($id);
				$this->session->set_flashdata('success', __('user.product_is_deleted_successfully'));
			} else {
				$this->session->set_flashdata('error', __('user.order_product_not_deleted'));
			}
			redirect(base_url() . 'usercontrol/sales_products');
		}

		$this->session->set_flashdata('error', __('user.product_delete_failed'));
		redirect(base_url() . 'usercontrol/sales_products');
	}



	public function calc_commission(){
		$data = $this->input->post(null,true);
		$userdetails = $this->userdetails();

		$vendor_setting = $this->Product_model->getSettings('vendor');
		if((int)$vendor_setting['storestatus'] == 0) show_404();

		if (isset($data['product_id']) && (int)$data['product_id'] > 0) {
			$product = $this->db->query("SELECT * FROM product_affiliate WHERE product_id=". (int)$data['product_id'])->row();
			

			if($product){
				$data['admin_sale_commission_type']      = $product->admin_sale_commission_type;
				$data['admin_commission_value']          = $product->admin_commission_value;
				$data['admin_click_commission_type']     = $product->admin_click_commission_type;
				$data['admin_click_amount']              = $product->admin_click_amount;
				$data['admin_click_count']               = $product->admin_click_count;
				
			}
		} else {
			$data['admin_sale_commission_type']      = 'default';
			$data['admin_click_commission_type']     = 'default';
		}

		$setting = array(
			'product_id' => $data['product_id'],
			'product_price' => $data['product_price'],

			'admin_click_commission_type' => ($data['admin_click_commission_type'] != '' ? $data['admin_click_commission_type'] : 'default'),
			'admin_click_count'           => $data['admin_click_count'],
			'admin_click_amount'          => $data['admin_click_amount'],
			'admin_sale_commission_type'  => $data['admin_sale_commission_type'] != '' ? $data['admin_sale_commission_type'] : 'default',
			'admin_commission_value'      => $data['admin_commission_value'],

			'affiliate_click_commission_type' => $data['affiliate_click_commission_type'],
			'affiliate_click_count' => $data['affiliate_click_count'],
			'affiliate_click_amount' => $data['affiliate_click_amount'],
			'affiliate_sale_commission_type' => $data['affiliate_sale_commission_type'],
			'affiliate_commission_value' => $data['affiliate_commission_value'],
			'user_id' => (int)$userdetails['id'],
		);
  
		$json['commission'] = $this->Product_model->calcVendorCommission($setting);
		$json['success'] = true;

		echo json_encode($json);
	}

	public function store_coupon_manage($coupon_id = 0){
		if(!$this->userdetails()){ redirect('/', 'refresh'); }
		$this->load->model("Coupon_model");
		$data['coupon'] = $this->Coupon_model->getCoupon($coupon_id);
		$data['product'] = $this->db->query("SELECT p.product_id,p.product_name FROM product p LEFT JOIN product_affiliate pa ON(pa.product_id = p.product_id) WHERE is_campaign_product=0 and pa.user_id = '".(int)$this->userdetails()['id']."'")->result_array();

		if(isset($data['coupon']['vendor_id']) && $data['coupon']['vendor_id'] != $this->userdetails()['id']){
			show_404();
		}
		$this->view($data,'store/coupon_form','usercontrol');
	}

	public function store_coupon_delete($coupon_id){
		if(!$this->userdetails()){ redirect('/', 'refresh'); }

		$this->load->model("Coupon_model");
		$this->Coupon_model->deleteCoupon($coupon_id);

		$this->session->set_flashdata('success', __('user.coupon_deleted_successfully'));
		

		redirect(base_url("usercontrol/store_coupon"));
	}
	public function store_coupon(){
		$userdetails = $this->userdetails();
		
		if(empty($userdetails)) redirect('usercontrol/dashboard');

		$vendor_setting = $this->Product_model->getSettings('vendor');
		$store_setting = $this->Product_model->getSettings('store');
		if(!isset($userdetails['is_vendor']) || $userdetails['is_vendor'] != 1 || (int)$vendor_setting['storestatus'] != 1 || (int)$store_setting['status'] != 1) redirect('usercontrol/dashboard');

		if($store_setting['store_mode'] == 'sales') redirect('usercontrol/store_dashboard');

		$this->load->model("Coupon_model");
		$data['coupons'] = $this->Coupon_model->getCoupons((int)$userdetails['id']);
		$ptotal = $this->db->query('SELECT product_id FROM product WHERE is_campaign_product=0 and product_created_by='.$userdetails['id'])->num_rows();
		
		foreach ($data['coupons'] as $key => $value) {
			if(strtolower($value['allow_for']) == 's')
				$data['coupons'][$key]['product_count'] = count(explode(',', $value['products']));
			else
				$data['coupons'][$key]['product_count'] = $ptotal;

			$data['coupons'][$key]['count_coupon'] = $this->Coupon_model->getCouponCount($value['coupon_id']);
		}
		

		$this->view($data,'store/coupon_index','usercontrol');
	}

	public function store_dashboard(){
		$userdetails = $this->userdetails();

		if(empty($userdetails)) redirect('usercontrol/dashboard');
		
		$vendor_setting = $this->Product_model->getSettings('vendor');
		$store_setting = $this->Product_model->getSettings('store');
		if(!isset($userdetails['is_vendor']) || $userdetails['is_vendor'] != 1 || (int)$vendor_setting['storestatus'] != 1 || (int)$store_setting['status'] != 1) redirect('usercontrol/dashboard');

		$this->load->model('Total_model');
		$data['vendor_store_statistic'] = $this->Total_model->getVendorStoreStatistic($userdetails['id']);

		$this->view($data,'store/dashboard','usercontrol');
	}

	public function store_dashboard_order_list(){

		$userdetails = $this->userdetails();

		if(!$userdetails){ redirect('/', 'refresh'); }

		$get = $this->input->get(null,true);

		$post = $this->input->post(null,true);

		$filter = array(

			'vendor_id' => $userdetails['id'],

			'limit' => 50,

			'page' => isset($get['page']) ? (int)$get['page'] : 1,

		);

		$this->load->model('Order_model');

		$data['status'] = $this->Order_model->status();

		$getallorders = $this->Order_model->getOrders($filter);

		$data['orders'] = $getallorders['data'];

		$this->load->library('pagination');

		$this->pagination->cur_page = $filter['page'];

		$config['base_url'] = base_url('usercontrol/store_dashboard_order_list');

		$config['per_page'] = $filter['limit'];

		$config['total_rows'] = $getallorders['total'];

		$config['use_page_numbers'] = TRUE;

		$config['page_query_string'] = TRUE;

		$config['enable_query_strings'] = TRUE;

		$config['query_string_segment'] = 'page';

		$this->pagination->initialize($config);

		$data['pagination'] = $this->pagination->create_links();

		

		$json['view'] = $this->load->view("usercontrol/store/order_list_tr", $data, true);

		echo json_encode($json);
	}

	public function save_coupon(){
		if(!$this->userdetails()){ redirect('/', 'refresh'); }
		$this->load->library('form_validation');
		$data = $this->input->post(null,true);
		$json = array();

		$this->form_validation->set_rules('name', 'Name', 'required|trim');
		$this->form_validation->set_rules('code', 'Coupon Code', 'required|trim');
		$this->form_validation->set_rules('type', 'Type', 'required|trim');
		$this->form_validation->set_rules('allow_for', 'Allow For', 'required|trim');
		$this->form_validation->set_rules('discount', 'Discount', 'required|trim');
		$this->form_validation->set_rules('date_start', 'Start Date', 'required|trim');
		$this->form_validation->set_rules('date_end', 'End Date', 'required|trim');
		$this->form_validation->set_rules('status', 'Status', 'required|trim');
		
		if ($this->form_validation->run() == FALSE) {
			$json['errors'] = $this->form_validation->error_array();
		} else {

			//Custom validation for date comparison
	        $date_start = strtotime($data['date_start']);
	        $date_end = strtotime($data['date_end']);
	        
	        if ($date_start > $date_end) {
	            $json['errors'] = array("date_validation" => __("user.start_date_greater_error"));
	            echo json_encode($json);
	            die();
	        }
			
			if($data['allow_for']=='S' && $data['products']=="")
			{
				$json['errors']=array("select-product"=>__('user.please_select_at_least_one_product'));
				 echo json_encode($json);
				 die(); 
			}
  
			if($data['allow_for']=='A')
			{
				$products = $this->db->query("SELECT p.product_id FROM product p LEFT JOIN product_affiliate pa ON(pa.product_id = p.product_id) WHERE is_campaign_product=0 and pa.user_id = '".(int)$this->userdetails()['id']."'")->result_array();

				$productStr='';
				foreach ($products as $product)
				{
					if($productStr!='')
						$productStr.=",";
					$productStr.=$product['product_id'];
				}
			}
			else
			{

				$productStr=implode(",", $data['products']);
			}

			$coupon = array(
				'vendor_id'  => $this->userdetails()['id'],
				'name'       => $data['name'],
				'code'       => $data['code'],
				'type'       => $data['type'],
				'allow_for'  => $data['allow_for'],
				'discount'   => $data['discount'],
				'date_start' => date("Y-m-d", strtotime($data['date_start'])),
				'date_end'   => date("Y-m-d", strtotime($data['date_end'])),
				'uses_total' => $data['uses_total'],
				'status'     => $data['status'],
				'products'   => $productStr,
				'date_added' => date("Y-m-d H:i:s"),
			);

			if($data['id'] > 0){
				unset($coupon['date_added']);
				$this->db->update("coupon",$coupon,['coupon_id' => $data['id']]);
			} else {
				$this->db->insert("coupon",$coupon);
				$coupon_id = $this->db->insert_id();
			}
			$json['location'] = base_url("usercontrol/store_coupon");
			$this->session->set_flashdata('success', __('user.coupon_saved_successfully'));
		}

		echo json_encode($json);
	}

	public function programs(){
		$userdetails = $this->userdetails();
		
		if(empty($userdetails)) redirect('usercontrol/dashboard');

		$market_vendor = $this->Product_model->getSettings('market_vendor');
		if(!isset($userdetails['is_vendor']) || $userdetails['is_vendor'] != 1 || (int)$market_vendor['marketvendorstatus'] != 1)
			redirect('usercontrol/dashboard');
		
		$filter = [
			'vendor_id' => $userdetails['id'],
		];

		$data['programs'] = $this->IntegrationModel->getPrograms($filter);

		$this->view($data,'integration/programs','usercontrol');
	}

	public function programs_form($program_id = 0){
		$userdetails = $this->userdetails();
		if(!$this->userdetails()){ redirect('usercontrol/dashboard', 'refresh'); }

		$market_vendor = $this->Product_model->getSettings('market_vendor');
		if((!isset($userdetails['is_vendor']) || !$userdetails['is_vendor']) || (int)$market_vendor['marketvendorstatus'] == 0) show_404();
		
		$data = array();
		if($program_id){
			$data['programs'] = $this->IntegrationModel->getProgramByID($program_id);
		}

		$data['CurrencySymbol'] = $this->currency->getSymbol();
		$data['market_vendor'] = $this->Product_model->getSettings('market_vendor');

		$this->view($data,'integration/programs_form','usercontrol');
	}

	public function delete_programs_form(){
		$userdetails = $this->userdetails();
		if(!$this->userdetails()){ redirect('usercontrol/dashboard', 'refresh'); }
		$market_vendor = $this->Product_model->getSettings('market_vendor');
		if((!isset($userdetails['is_vendor']) || !$userdetails['is_vendor']) || (int)$market_vendor['marketvendorstatus'] == 0) show_404();

		$program_id = (int)$this->input->post("id",true);
		$ads = $this->db->select("*")->from("integration_tools")->where("program_id",$program_id)->get()->num_rows();
		

		if($ads == 0){
			$this->db->query("DELETE FROM integration_programs WHERE id=". $program_id);
			$json['success'] = true;
		} else{
			$json['message'] = "There are {$ads} Integration Tools Assgin to This Program";
		}
		

		echo json_encode($json);
	}

	public function editProgram(){
	    $userdetails = $this->userdetails();
	    if(!$userdetails){ redirect('usercontrol/dashboard', 'refresh'); }
	    $market_vendor = $this->Product_model->getSettings('market_vendor');
	    if((!isset($userdetails['is_vendor']) || !$userdetails['is_vendor']) || (int)$market_vendor['marketvendorstatus'] == 0) show_404();

	    $data = $this->input->post(null,true);

	    $this->form_validation->set_rules('name', 'Name', 'required|trim');
	    if($data['sale_status']){
	        $this->form_validation->set_rules('commission_type', 'Commission Type', 'required|trim');
	        $this->form_validation->set_rules('commission_sale', 'Sale Comission', 'required|trim|numeric|greater_than[0]');
	    }
	    if($data['click_status']){
	        $this->form_validation->set_rules('commission_number_of_click', 'Number of click', 'required|trim|numeric|greater_than[0]');
	        $this->form_validation->set_rules('commission_click_commission', 'Click Commission', 'required|trim|numeric|greater_than[0]');
	    }

	    if ($this->form_validation->run() == FALSE) {
	        $json['errors'] = $this->form_validation->error_array();
	    } else {
	        $program_id = (int)$data['program_id'];

	        $program_id = $this->IntegrationModel->editProgram($data,$program_id,'user',$userdetails['id']);
	        if($program_id){

	            $this->session->set_flashdata('success', __('user.program_saved_successfully'));

	            if(isset($data['add_program_to_form'])){
	                $market_vendor = $this->Product_model->getSettings('market_vendor','marketaddnewprogram');
	                if($market_vendor['marketaddnewprogram']){
	                    $json['message'] = __('user.vendor_program_send_to_review');
	                } else {
	                    $json['message'] = __('user.vendor_program_approved');
	                    
	                    $program = $this->IntegrationModel->getProgramByID($program_id);
	                    $program['commission_sale'] = ($program['commission_type'] == 'fixed') ? c_format($program['commission_sale']) : (int) $program['commission_sale']."%";
	                    $json['newOption'] = '<option 
	                    data-commission_type="'.$program['commission_type'].'"
	                    data-commission_sale="'.$program['commission_sale'].'"
	                    data-commission_number_of_click="'.$program['commission_number_of_click'].'"
	                    data-commission_click_commission="'.c_format($program['commission_click_commission']).'"
	                    data-click_status="'.$program['click_status'].'"
	                    data-sale_status="'.$program['sale_status'].'"
	                    value="'.$program['id'].'">'.$program['name'].'
	                    </option>';
	                }
	            } else {
	                $json['location'] = base_url("usercontrol/programs");
	            }
	        } else {
	            $json['errors']['name'] = "Something Wrong";
	        }
	    }

	    echo json_encode($json);
	}

	public function integration_tools($page= 1){
		$userdetails = $this->userdetails();

		if(empty($userdetails)) redirect('usercontrol/dashboard');

		$market_vendor = $this->Product_model->getSettings('market_vendor');
		if(!isset($userdetails['is_vendor']) || $userdetails['is_vendor'] != 1 || (int)$market_vendor['marketvendorstatus'] != 1)
			redirect('usercontrol/dashboard');

		if ($this->input->server('REQUEST_METHOD') == 'POST'){
			$post = $this->input->post(null,true);
			$get = $this->input->get(null,true);
			$filter = array(
				'page' => isset($get['page']) ? $get['page'] : $page,
				'limitdata' => 25,
				'vendor_id' => $userdetails['id'],
			);


			$filter['userdetails'] = $userdetails;
 
			if ($market_vendor['marketvendorstatus'] == 1) {
				$filter['marketvendorstatus'] = 1;
			 }else{
			 	$filter['marketvendorstatus'] = 0;
			 }

			if(isset($post['category_id']))
				$filter['category_id'] = $post['category_id'];
			
			if(isset($post['ads_name']))
				$filter['ads_name'] = $post['ads_name'];
			
			if(isset($post['status']))
				$filter['status'] = $post['status'];
			 
			$json = array();
			list($data['tools'],$total) = $this->IntegrationModel->getProgramTools($filter);
			$data['integration_plugins'] = modules_list();	

			if($post['paginate']){
				$this->load->library('pagination');
				$this->pagination->cur_page = $filter['page'];
				$config['base_url'] = base_url('usercontrol/integration_tools');
				$config['per_page'] = $filter['limitdata'];
				$config['total_rows'] = $total;
				$config['use_page_numbers'] = TRUE;
				$config['page_query_string'] = TRUE;
				$config['enable_query_strings'] = TRUE;
				$_GET['page'] = $filter['page'];
				$config['query_string_segment'] = 'page';
				$this->pagination->initialize($config);
				$json = $this->pagination->create_links();
			} else {
				$json = $this->load->view("usercontrol/integration_tools/integration_tools_list", $data, true);
			}	

			echo $json;
			die;
		}
 

		$data['categories'] = $this->db->query("SELECT DISTINCT integration_category.id  as value ,integration_category.name as label, CASE WHEN integration_category.parent_id=0 THEN integration_category.id ELSE integration_category.parent_id END AS pid FROM `integration_category`
			 inner JOIN integration_tools on integration_tools.category=	 integration_category.id 
		 order by pid,integration_category.id")->result_array();

		$this->load->library("socialshare");				
		$data['social_share_modal'] =  $this->socialshare->get_dynamic_social_share_btns();

		$userPlan = App\MembershipUser::with("plan")->where('is_active',1)->where('user_id',$userdetails['id'])->first();
		$vendor_campaign_count = $this->Product_model->countByField('integration_tools','vendor_id',$userdetails['id']);
		if(isset($userPlan->plan->campaign) && $userPlan->plan->campaign <= $vendor_campaign_count){
			$this->load->helper('cookie');
			$cookie = get_cookie('campaign_count_alert_'.$userdetails['id']);
			$data['campaign_count_alert'] = __('user.reached_maximum_limit_package_upgrade').' <a href="'.base_url('usercontrol/purchase_plan').'">'.__('user.here').'</a>';
		}

		$this->view($data,'integration_tools/integration_tools','usercontrol');
	}

	public function getIntegrationMlmInfo(){
		if(!$this->userdetails()){ redirect('admincontrol/dashboard', 'refresh'); }

		$html = '';

		$tool = $this->IntegrationModel->getProgramToolsByID((int)$this->input->post('id',true));
		if($tool){
			$data['tool']['commission_type'] = $tool['commission_type'];
			$data['CurrencySymbol'] = $this->currency->getSymbol();
			if($tool['commission_type'] == 'custom'){
				$setting = $this->Product_model->getVendorSettings($tool['vendor_id'], 'referlevel');
				
				$data['tool']['referlevel'] = $tool['commission']['referlevel'];
				$data['tool']['referlevel']['levels'] = ($tool['commission']['referlevel']['levels']) ? $tool['commission']['referlevel']['levels'] : (isset($setting['levels']) ? (int)$setting['levels'] : 3);

				for ($i=1; $i <= $data['tool']['referlevel']['levels']; $i++) { 
					$data['tool']['referlevel_'. $i] = $tool['commission']['referlevel_'. $i];
				}
			} else {
				$commonSetting = array('referlevel','referlevel_1','referlevel_2','referlevel_3','referlevel_4','referlevel_5','referlevel_6','referlevel_7','referlevel_8','referlevel_9','referlevel_10','referlevel_11','referlevel_12','referlevel_13','referlevel_14','referlevel_15','referlevel_16','referlevel_17','referlevel_18','referlevel_19','referlevel_20','referlevel');

				foreach($commonSetting as $key => $value){
					$data['tool'][$value] 	= $this->Product_model->getVendorSettings($tool['vendor_id'], $value);
				}
			}

			$html = $this->load->view('usercontrol/integration_tools/integration_mlm_info',$data,true);
		}

		echo $html;
		die;
	}

	public function integration_terms_modal(){
		if(!$this->userdetails()){ redirect('usercontrol/dashboard', 'refresh'); }

		$data['terms_data'] = $this->IntegrationModel->getTermsToolsByID((int)$this->input->post('id',true));
		
		$json['html'] = $this->load->view('admincontrol/integration/integration_terms_modal', $data, true);

		echo json_encode($json);die;
	}

	public function get_plugin_instructions_for_modal($module_key, $toolsname){
		if(!$this->userdetails()){ redirect('admincontrol/dashboard', 'refresh'); }

		$data['integration_modules'] = $this->modules_list();

		$data['module_key'] = $module_key;
		$data['toolsname'] = $toolsname;
		
		$data['action_codes'] = $this->db->select('integration_tools.action_code')
		->from('integration_tools')
		->where("tool_type",'action')
		->where("status",1)
		->get()
		->result_array();

		$data['general_codes'] = $this->db->select('integration_tools.general_code')
		->from('integration_tools')
		->where("tool_type",'general_click')
		->where("status",1)
		->get()
		->result_array();

		$data['module'] = $data['integration_modules'][$module_key];

		$data['views'] = '';
		
		$this->load->model('PagebuilderModel');

		$register_form = $this->PagebuilderModel->getSettings('registration_builder');
		
		$data['customField'] = json_decode($register_form['registration_builder'],1);

		return $this->load->view('admincontrol/integration/instructions', $data, TRUE);
	}


	public function integration_code_modal(){
		if(!$this->userdetails()){ redirect('usercontrol/dashboard', 'refresh'); }

		$market_vendor = $this->Product_model->getSettings('market_vendor');
		if((!isset($userdetails['is_vendor']) || !$userdetails['is_vendor']) || (int)$market_vendor['marketvendorstatus'] == 0) show_404();

		$data['action_code'] = 'action_code';
		$data['general_code'] = 'general_code';

		$tools = $this->IntegrationModel->getProgramToolsByID((int)$this->input->post('id',true));
		if($tools){
			

			$data['name'] = $tools['name'];
			$data['target_link'] = $tools['target_link'];
			$data['tool_type'] = $tools['tool_type'];
			if($tools['tool_type'] == 'action'){
				$data['action_code'] = $tools['action_code'];
			}
			if($tools['tool_type'] == 'general_click'){
				$data['general_code'] = $tools['general_code'];
			}
		}


		$skipNewViewFor = ['general_integration', 'laravel', 'cakephp', 'codeigniter'];

		if($tools['tool_type'] == 'program' && !empty($tools['tool_integration_plugin']) && !in_array($tools['tool_integration_plugin'], $skipNewViewFor)){
			$data['tool_integration_plugin_html'] = $this->get_plugin_instructions_for_modal($tools['tool_integration_plugin'], $tools['name']);
		}

		$json['tool'] = $data['tool'] = $tools;


		$data['integration_plugins'] = modules_list();

		$json['html'] = $this->load->view('usercontrol/integration/integration_code_modal', $data, true);

		echo json_encode($json);die;
	}

	public function integration_tools_form($type="banner", $tools_id = 0){
		$userdetails = $this->userdetails();
		if(!$userdetails){ redirect('usercontrol/dashboard', 'refresh'); }

		$market_vendor = $this->Product_model->getSettings('market_vendor');
		if((!isset($userdetails['is_vendor']) || !$userdetails['is_vendor']) || (int)$market_vendor['marketvendorstatus'] == 0) show_404();
		
		$setting = $this->Product_model->getVendorSettings($userdetails['id'],'referlevel');
		$data['levels'] = isset($setting['levels']) ? (int)$setting['levels'] : 3;

		if($tools_id){
			$data['tool'] = $this->IntegrationModel->getProgramToolsByID($tools_id);
			$category_ids = explode(",", $data['tool']['category']);
			if(count(array_filter($category_ids)) > 0){
				$data['categories'] = $this->db->query("SELECT id as value,name as label FROM integration_category WHERE id IN (". implode(",", $category_ids) .") ")->result_array();
			}
			
			$data['referlevel'] = $data['tool']['commission']['referlevel'];
			
			$data['levels'] = ($data['tool']['commission']['referlevel']['levels']) ? $data['tool']['commission']['referlevel']['levels'] : $data['levels'];
			for ($i=1; $i <= $data['levels']; $i++) { 
				$data['referlevel_'. $i] = $data['tool']['commission']['referlevel_'. $i];
			}
		}

		$commonSetting = array('referlevel','referlevel_1','referlevel_2','referlevel_3','referlevel_4','referlevel_5','referlevel_6','referlevel_7','referlevel_8','referlevel_9','referlevel_10','referlevel_11','referlevel_12','referlevel_13','referlevel_14','referlevel_15','referlevel_16','referlevel_17','referlevel_18','referlevel_19','referlevel_20','referlevel');
		foreach($commonSetting as $key => $value){
			$data['default'][$value] 	= $this->Product_model->getVendorSettings($userdetails['id'], $value);
		}

		$data['default_marketpostback'] = $this->Product_model->getSettings('marketpostback');
		$data['programs'] = $this->IntegrationModel->getPrograms(['vendor_id' => $userdetails['id'],'status' => 1]);
		$data['market_vendor'] = $this->Product_model->getSettings('market_vendor');
		$data['type'] = $type;
		$data['CurrencySymbol'] = $this->currency->getSymbol();
		$data['users'] = $this->db->query("SELECT username as name,id FROM users WHERE type='user'")->result_array();
		$data['integration_plugins'] = modules_list();	
		$data['randome_code'] = generateRandomAlpahaNemericCode();
		$this->view($data,'integration_tools/integration_tools_form','usercontrol');
	}

	function valid_url_custom($url) {
		if(filter_var($url, FILTER_VALIDATE_URL)){
			return TRUE;
		}
		else{
			return FALSE;
		}
	}

	public function integration_tools_form_post(){

		$userdetails = $this->userdetails();
		if(!$userdetails){ redirect('usercontrol/dashboard', 'refresh'); }

		$market_vendor = $this->Product_model->getSettings('market_vendor');
		if((!isset($userdetails['is_vendor']) || !$userdetails['is_vendor']) || (int)$market_vendor['marketvendorstatus'] == 0) show_404();
		

		$data = $this->input->post(null,true);

		if(empty($data['end_date']) || $data['tool_period'] == 1 || $data['tool_period'] == 3) {
			$data['end_date'] = "0000-00-000 00:00:00";
		} else {
			$enddate = strtotime($data['end_date']);
			$data['end_date'] = date('Y-m-d H:i:s', $enddate);
		}

		if(empty($data['start_date']) || $data['tool_period'] == 1 || $data['tool_period'] == 2) {
			$data['start_date'] = "0000-00-000 00:00:00";
		} else {
			$startdate = strtotime($data['start_date']);
			$data['start_date'] = date('Y-m-d H:i:s', $startdate);
		}

		$program_tool_id = isset($data['program_tool_id']) ? (int)$data['program_tool_id'] : 0;
		
		$this->form_validation->set_rules('target_link', 'Target Link', 'callback_valid_url_custom');
		$this->form_validation->set_rules('name', 'Name', 'required|trim');
		$this->form_validation->set_rules('type', 'Type', 'required|trim');
		$this->form_validation->set_rules('tool_type', 'Tool Type', 'required|trim');

		if($data['tool_period'] == 2){
			$this->form_validation->set_rules('end_date', 'End Date', 'required');
		} else if($data['tool_period'] == 3){
			$this->form_validation->set_rules('start_date', 'Start Date', 'required');
		} else if($data['tool_period'] == 4){ 
			$this->form_validation->set_rules('start_date', 'Start Date', 'required');
			$this->form_validation->set_rules('end_date', 'End Date', 'required');
		}

		if($data['tool_type'] == 'action'){
			$this->form_validation->set_rules('action_click', 'Action Click', 'required|trim');
			$this->form_validation->set_rules('action_amount', 'Action Amount', 'required|trim');
			$this->form_validation->set_rules('action_code', 'Action Code', 'required|trim');
			$data['program_id'] = 0;
		}
		else if($data['tool_type'] == 'general_click'){
			$this->form_validation->set_rules('general_click', 'General Click', 'required|trim');
			$this->form_validation->set_rules('general_amount', 'General Amount', 'required|trim');
			$this->form_validation->set_rules('general_code', 'General Code', 'required|trim');
			$data['program_id'] = 0;
		}
		else if($data['tool_type'] == 'program'){
			$this->form_validation->set_rules('program_id', 'Program', 'required|trim');
			$this->form_validation->set_rules('tool_integration_plugin', 'Integration Plugin', 'required|trim');
		}

		if($data['type'] == 'text_ads'){
			$this->form_validation->set_rules('text_ads_content', 'Ads Content', 'required|trim');
			$this->form_validation->set_rules('text_color', 'Color', 'required|trim');
			$this->form_validation->set_rules('text_bg_color', 'Background color', 'required|trim');
			$this->form_validation->set_rules('text_border_color', 'Border color', 'required|trim');
			$this->form_validation->set_rules('text_size', 'Border color', 'required|trim');
		}
		else if($data['type'] == 'link_ads'){
			$this->form_validation->set_rules('link_title', 'Link Title', 'required|trim');
		}
		else if($data['type'] == 'video_ads'){
			$this->form_validation->set_rules('video_link', 'Video Link', 'required|trim');
			$this->form_validation->set_rules('button_text', 'Video Button Text', 'required|trim');
			$this->form_validation->set_rules('video_height', 'Video Height', 'required|trim');
			$this->form_validation->set_rules('video_width', 'Video Width', 'required|trim');
		}
		$this->form_validation->set_message('valid_url_custom','Enter a valid URL.');

		if( $data['recursion'] == 'custom_time' ){
			$this->form_validation->set_rules('recursion_custom_time', 'Custom Time', 'required|greater_than[0]');
		}

		if ($this->form_validation->run() == FALSE) {
			$json['errors'] = $this->form_validation->error_array();
		} else {
			$checkActionCode = 0;

			if($data['tool_type'] == 'action' || $data['tool_type'] == 'single_action'){
				$checkActionCode = $this->db->query("SELECT * FROM integration_tools WHERE action_code like ". $this->db->escape($data['action_code']) ." AND id != ". $program_tool_id)->num_rows();
				if($checkActionCode > 0)  $json['errors']['action_code'] = "Action code to be unique";
			}
			else if($data['tool_type'] == 'general_click'){
				$checkActionCode = $this->db->query("SELECT * FROM integration_tools WHERE general_code like ". $this->db->escape($data['general_code']) ." AND id != ". $program_tool_id)->num_rows();
				if($checkActionCode > 0) $json['errors']['general_code'] = "General code to be unique";
			}

			if($data['tool_type'] == 'program'){
				$featured_image = getDefaultCampaignImageByTool($data['tool_type'], $data['tool_integration_plugin']);
			} else {
				$featured_image = getDefaultCampaignImageByTool($data['tool_type']);
			}
			
			$data['deafult_featured_image'] = $featured_image;

			if (isset($data['marketpostback']['status']) && $data['marketpostback']['status'] == 'custom') {
				if (filter_var($data['marketpostback']['url'], FILTER_VALIDATE_URL) === FALSE) {
					$json['errors']['marketpostback-url'] = 'Enter a valid postback URL';
				}
			}

			$userPlan = App\MembershipUser::with("plan")->where('is_active',1)->where('user_id',$userdetails['id'])->first();
			$plan_campaign_count = ($program_tool_id) ? $userPlan->plan->campaign : $userPlan->plan->campaign - 1;
			$vendor_campaign_count = $this->Product_model->countByField('integration_tools','vendor_id',$userdetails['id']);
			if(isset($userPlan->plan->campaign) && $plan_campaign_count < $vendor_campaign_count)
				$json['error'] = __('user.reached_maximum_limit_package_upgrade').' <a href="'.base_url('usercontrol/purchase_plan').'">'.__('user.here').'</a>';
			 
			if(!isset($json) || $json==0 || ( count($json['errors']) == 0 && !$json['error']))
			{
				$data['featured_image'] = $data['old_featured_image'];
				
				if(!empty($_FILES['featured_image']['name'])){
					$upload_response = $this->Product_model->upload_photo('featured_image','assets/images/product/upload/thumb');
					if($upload_response['success']){
						$data['featured_image'] = $upload_response['upload_data']['file_name'];
					}
				} else if(empty($data['featured_image'])) {
					copy('assets/images/plugins_icons/'.$featured_image, 'assets/images/product/upload/thumb/'.$featured_image);
					$data['featured_image'] = $featured_image;
				}

				if(!isset($json) || $json==0 || count($json['errors']) == 0){
					$program_tool_id = $this->IntegrationModel->editProgramTools($data,$_FILES['custom_banner'],'vendor', $userdetails['id']);

					if($program_tool_id){

						$this->session->set_flashdata('success', __('user.campaign_saved_successfully'));

						if(isset($data['save_close'])){
							$json['location'] = base_url("usercontrol/integration_tools_form/". $data['type'] ."/". $program_tool_id);
						} else{
							$json['location'] = base_url("usercontrol/integration_tools");
						}
					} else{
						$json['errors']['name'] = "Something Wrong";
					}
				}
			}
		}

		echo json_encode($json);
	}

	public function integration_tools_delete($tools_id){
		$userdetails = $this->userdetails();
		if(!$this->userdetails()){ redirect('usercontrol/dashboard', 'refresh'); }
		$market_vendor = $this->Product_model->getSettings('market_vendor');
		if((!isset($userdetails['is_vendor']) || !$userdetails['is_vendor']) || (int)$market_vendor['marketvendorstatus'] == 0) show_404();
		$this->IntegrationModel->deleteTools($tools_id);
		$this->session->set_flashdata('success', __('user.campaign_deleted_successfully'));
		redirect(base_url("usercontrol/integration_tools"));
	}

	public function tool_get_code($control = 'usercontrol'){
		$tools_id = (int)$this->input->post("id",true);
		if($control == 'usercontrol'){
			if(!$this->userdetails()){ redirect('usercontrol/dashboard', 'refresh'); }
			$data['user_id'] = $this->userdetails()['id'];
		}
		else if($control == 'usercontrol'){
			if(!$this->userlogins()){ redirect('usercontrol/dashboard', 'refresh'); }
			$data['user_id'] = $this->userlogins()['id'];
		}
		

		$data['tool'] = $this->IntegrationModel->getProgramToolsByID($tools_id);
		if($data['tool']){
			$json['html'] = $this->load->view("integration/code", $data, true);
		}
		

		echo json_encode($json);die;
	}

	public function integration_category_auto(){
		$userdetails = $this->userdetails();
		if(!$this->userdetails()){ redirect('/', 'refresh'); }
		$keyword = $this->input->get('query');
		

		$data = $this->db->query("SELECT integration_category.id as value,integration_category.name as label, CASE WHEN integration_category.parent_id=0 THEN integration_category.id ELSE integration_category.parent_id END AS pid FROM integration_category WHERE integration_category.name  like ". $this->db->escape("%".$keyword."%") ." order by pid,integration_category.id")->result_array();
		 
		echo json_encode($data);die;
	}

	public function integration_tools_duplicate($tools_id){
		$userdetails = $this->userdetails();
		
		$userPlan = App\MembershipUser::with("plan")->where('is_active',1)->where('user_id',$userdetails['id'])->first();

		$plan_campaign_count = ($userPlan && !empty($userPlan->plan)) ? $userPlan->plan->campaign : null;
		$vendor_campaign_count = $this->Product_model->countByField('integration_tools','vendor_id',$userdetails['id']);
		
		if(! empty($plan_campaign_count) && $plan_campaign_count <= $vendor_campaign_count){
			$_SESSION['error'] = __('user.reached_maximum_limit_package_upgrade').'<a href="'.base_url('usercontrol/purchase_plan').'"> '.__('user.here').'</a>';
		}else{
			$this->IntegrationModel->duplicate_tools($tools_id);
			$this->session->set_flashdata('success', __('user.add_duplicate_campaign_successfully'));
		}

		redirect(base_url('usercontrol/integration_tools'));
	}

	public function integration_code_modal_new(){
		$userdetails = $this->userdetails();
		if(!$userdetails){ redirect('/', 'refresh'); }

		$data['action_code'] = 'action_code';
		$data['single_action'] = 'single_action';
		$data['general_code'] = 'general_code';

		$tools = $this->IntegrationModel->getProgramToolsByID((int)$this->input->post('id',true));
		if($tools){
			
			$data['name'] = $tools['name'];
			$data['target_link'] = $tools['target_link'];
			$data['tool_type'] = $tools['tool_type'];
			
			if($tools['tool_type'] == 'action'){
				$data['action_code'] = $tools['action_code'];
			}
			if($tools['tool_type'] == 'single_action'){
				$data['action_code'] = $tools['action_code'];
			}
			if($tools['tool_type'] == 'general_click'){
				$data['general_code'] = $tools['general_code'];
			}
		}

		$skipNewViewFor = ['general_integration', 'laravel', 'cakephp', 'codeigniter'];

		if($tools['tool_type'] == 'program' && !empty($tools['tool_integration_plugin']) && !in_array($tools['tool_integration_plugin'], $skipNewViewFor)){
			$data['tool_integration_plugin_html'] = $this->get_plugin_instructions_for_modal($tools['tool_integration_plugin'], $tools['name']);
		}

		$json['tool'] = $data['tool'] = $tools;


		$data['integration_plugins'] = modules_list();

		$json['html'] = $this->load->view('admincontrol/integration/integration_code_modal', $data, true);

		echo json_encode($json);die;
	}

	public function get_withdrawal_modal(){
		$userdetails = $this->userdetails();
		if(!$userdetails){ redirect('/', 'refresh'); }

		$site_setting = $this->Product_model->getSettings('site');
		
		$ids = $this->input->post("ids",true);

		if($ids==""){
			
			$data['danger'] =  __('user.please_select_at_least_one_wallet_record');
			$json['html'] = $this->load->view('usercontrol/users/parts/withdrawal_modal', $data, true);
			echo json_encode($json);die;
		}
		
		
		$data['ids']=$ids; 
		
		if($userdetails['is_vendor']){
			if($data['ids'] == 'all') {
				$transaction_total = $this->db->query("SELECT SUM(amount) total FROM wallet WHERE status = 1 AND amount > 0 AND user_id=".(int)$userdetails['id']  )->row()->total;
			} else {
				$transaction_total = $this->db->query("SELECT SUM(amount) total FROM wallet WHERE id IN (". $data['ids'] .") ")->row()->total;
			}
			
		} else {
			if($data['ids'] == 'all'){
				$transaction_total = $this->db->query("SELECT SUM(amount) total FROM wallet WHERE status = 1 AND user_id=".(int)$userdetails['id'])->row()->total;
			}else{
				$transaction_total = $this->db->query("SELECT SUM(amount) total FROM wallet WHERE id IN (". $data['ids'] .") ")->row()->total;
			}
		}
				
		if(isset($site_setting['wallet_max_amount']) && $site_setting['wallet_max_amount']>0 && (float)$transaction_total> (float)$site_setting['wallet_max_amount'])
		{
			$newwarningmessage=__('user.the_maximum_withdrawal_limit_is').": ".c_format($site_setting['wallet_max_amount']);
			$data['danger'] = $newwarningmessage;
		}
		else if( (float)$transaction_total >= (float)$site_setting['wallet_min_amount']){
			$this->load->model('Withdrawal_payment_model');
			$data['payment_methods'] = $this->Withdrawal_payment_model->getPaymentMethods([
				'get_user_setting' => true,
			]);
		} else{

			if(isset($site_setting['wallet_min_message_new']) && $site_setting['wallet_min_message_new']!='')
				$newwarningmessage=$site_setting['wallet_min_message_new'].": ".c_format($site_setting['wallet_min_amount']);
			else
				$newwarningmessage=__('user.the_minimum_limit_is').": ".c_format($site_setting['wallet_min_amount']);
			$data['warning'] = $newwarningmessage;
		} 

	 $customSetting = $this->Product_model->getSettings('withdrawalpayment_bank_transfer');

		if (!empty($customSetting))
		{
			$data['setting_exist_status'] = 1;
			$data['get_custom_fiels'] = $customSetting;
		}
		else
		{
			$data['setting_exist_status'] = 0;
			$data['get_custom_fiels'] = array();
		}
		$data['PrimaryPaymentMethodStatus'] = $userdetails['primary_payment_method'];
		$data['paymentlist'] = $this->Product_model->getAllPayment($userdetails['id']);
		$data['paypalaccounts'] = $this->Product_model->getPaypalAccounts($userdetails['id']);
		
		$json['html'] = $this->load->view('usercontrol/users/parts/withdrawal_modal', $data, true);
		echo json_encode($json);die;
	}

			public function get_withdrwal_history($id){
				$status_history = $this->db->query("SELECT * FROM wallet_requests_history WHERE req_id={$id} ORDER BY id DESC ")->result_array();
				$json['html'] = '';

				foreach ($status_history as $key => $value) {
					$badge = $value['transaction_id'] ?  ' <span class="badge bg-secondary d-inline-block">Tran ID: '. $value['transaction_id'] .'</span>' : '';
					$json['html'].= '<tr>
					<td>'. withdrwal_status($value['status'])  .'</td>
					<td>'. $value['comment'] . $badge.'</td>
					</tr>';
				}

				echo json_encode($json);die;
			}

			public function purchase_plan(){
				$userdetails = $this->Product_model->userdetails('user',1);
				$membership = $this->Product_model->getSettings('membership');

				if(($membership['status'] == 1) || (($membership['status'] == 2) && ($userdetails['is_vendor'] == 1)) || (($membership['status'] == 3) && ($userdetails['is_vendor'] == 0))){
					$data = ['notcheckmember'=>1];
					$membership = $this->Product_model->getSettings('membership');
					if ((int)$membership['status'] == 0) {
						show_404();
					}
					$data['MembershipSetting'] =$this->Product_model->getSettings('membership');
					
					$user = App\User::auth();
					
					if((int)$user->plan_id == 0){
						
					}
					else if($user->plan_id == -1 && $userdetails['is_vendor'] == 1){
						$data['is_lifetime_plan'] = 1;
					} else if ($user) {
						$plan = $user->plan();
						if($plan){
							$data['plan']  = $plan;
						}
					}
					
					if($userdetails['is_vendor'] == 1)
						$data['plans'] = MembershipPlan::select('membership_plans.*','award_level.sale_comission_rate')->join('award_level','award_level.id','=','membership_plans.level_id','left')->where('status',1)->orderBy('sort_order', 'ASC')->get();
					else 
						$data['plans'] = MembershipPlan::select('membership_plans.*','award_level.sale_comission_rate')->join('award_level','award_level.id','=','membership_plans.level_id','left')->where('status',1)->where('user_type',1)->orderBy('sort_order', 'ASC')->get();
 
					$data['payment_gateways'] = MembershipPlan::getPaymentMethods();
					
					$this->view($data,"membership/notaccess",'usercontrol');
				} else {
					if($membership['status'] == 1 || $membership['status'] == 3  || $membership['status'] == 2){
						$data = ['notcheckmember'=>1];
						$data['MembershipSetting'] =$this->Product_model->getSettings('membership');
						$user = App\User::auth();
						if((int)$user->plan_id == 0){
							
						}
						else if($user->plan_id == -1){
							$data['is_lifetime_plan'] = 1;
						} else if ($user) {
							$plan = $user->plan();
							if($plan){
								$data['plan']  = $plan;
							}
						}

						if($userdetails['is_vendor'] == 1)
							$data['plans'] = MembershipPlan::select('membership_plans.*','award_level.sale_comission_rate')->join('award_level','award_level.id','=','membership_plans.level_id','left')->where('status',1)->orderBy('sort_order', 'ASC')->get();
						else 
							$data['plans'] = MembershipPlan::select('membership_plans.*','award_level.sale_comission_rate')->join('award_level','award_level.id','=','membership_plans.level_id','left')->where('status',1)->where('user_type',1)->orderBy('sort_order', 'ASC')->get();

						$data['payment_gateways'] = MembershipPlan::getPaymentMethods();
						
						$this->view($data,"membership/notaccess",'usercontrol');
					} else {
						show_404();
					}
				}
			}

			public function purchase_plan_expire(){
				$data = ['notcheckmember'=>1];
				$membership = $this->Product_model->getSettings('membership');
				if ((int)$membership['status'] == 0) {
					show_404();
				}

				$user = App\User::auth();

				if((int)$user->plan_id == 0){
					redirect(base_url());	
				}
				else if($user->plan_id == -1){
					$data['is_lifetime_plan'] = 1;
				} else if ($user) {
					$plan = $user->plan();
					if($plan){
						$data['plan']  = $plan;

						if($plan->isExpire() || !$plan->strToTimeRemains() > 0)
						{}
						else
						redirect(base_url());	
					}
				}
 				
				$this->view($data,"membership/purchase_plan_expire",'usercontrol');
			}

			public function purchase_history($page=1){
				$userdetails = $this->Product_model->userdetails('user',1);
				$membership = $this->Product_model->getSettings('membership');
				if(($membership['status'] == 1) || (($membership['status'] == 2) && ($userdetails['is_vendor'] == 1)) || (($membership['status'] == 3) && ($userdetails['is_vendor'] == 0))){
					$data = ['notcheckmember'=>1];
					$user = $this->checkLogin('user');
					$membership = $this->Product_model->getSettings('membership');
					if ((int)$membership['status'] == 0) {
						show_404();
					}
					$page = max((int)$page,1);

					\Illuminate\Pagination\Paginator::currentPageResolver(function () use ($page) {
						return $page;
					});

					$limit = 10;
					$query = App\MembershipUser::with("plan")->where("user_id",$user['id'])->orderBy("id","DESC")->paginate($limit);
					$data['links'] = $this->build_paginate($query, 'usercontrol/purchase_history',$page, $limit);
					$data['plans'] = $query;
					
					$this->view($data,"membership/purchase_history",'usercontrol');
				}else{
					show_404();
				}
			}

			public function membership_purchase_details($plan_id=1){
				if($this->Product_model->isMembershipAccess()){
					$data = ['notcheckmember'=>1];
					$membership = $this->Product_model->getSettings('membership');
					if((int)$membership['status'] == 0)
						show_404();

					$user = $this->checkLogin('user');
					$query = App\MembershipUser::select('membership_user.*','membership_plans.commission_sale_status','award_level.level_number')->join('membership_plans','membership_plans.id','=','membership_user.plan_id')->join('award_level','award_level.id','=','membership_plans.level_id','left')->where('membership_user.id', $plan_id)->where('membership_user.user_id', $user['id'])->first();
					if($query){
						$data['history'] = $query->status_history();
						$data['plan'] = $query;
						
						$this->view($data,"membership/purchase_detail",'usercontrol');
					} else {
						show_404();
					}
				} else {
					show_404();
				}
			}

	public function create_slug(){
		$json = array();

		$userdetails = $this->userdetails();
		$post = $this->input->post(null,true);
		$this->load->library('form_validation');
		
		$this->form_validation->set_rules('slug', 'Slug', 'callback__alpha_dash_space');

		if ($this->form_validation->run() == FALSE) {
		    $json['errors'] = $this->form_validation->error_array();
		} else {
		    // Check for existing slug from other users
		    $existingForOthers = Slug::where('slug', $post['slug'])
		                              ->where('type', $post['type'])
		                              ->where('user_id', '!=', (int)$userdetails['id'])
		                              ->first();

		    // Check for existing slug for the current user
		    $existingForUser = Slug::where('slug', $post['slug'])
		                            ->where('type', $post['type'])
		                            ->where('user_id', (int)$userdetails['id'])
		                            ->first();

			if ($existingForOthers) {
			    $json['error'] = __('user.slug_taken_by_another_user');
			} elseif ($existingForUser && (int)$existingForUser->related_id !== (int)$post['related_id']) {
			    $json['error'] = __('user.slug_already_used_choose_another');
		    } else {
		        // If no existing slug is found for other users, proceed to update or create new slug
		        $Slug = Slug::where('type', $post['type'])
		                    ->where('user_id', (int)$userdetails['id'])
		                    ->where('related_id', (int)$post['related_id'])
		                    ->first();

		        if ($Slug) {
		            // Update existing slug
		            $Slug->slug = $post['slug'];
		            $Slug->updated_at = date('Y-m-d H:i:s');
		            $Slug->save();
		        } else {
		            // Create new slug
		            $Slug = new Slug();
		            $Slug->user_id = (int)$userdetails['id'];
		            $Slug->related_id = (int)$post['related_id'];
		            $Slug->type = $post['type'];
		            $Slug->slug = $post['slug'];
		            $Slug->created_at = date('Y-m-d H:i:s');
		            $Slug->updated_at = date('Y-m-d H:i:s');
		            $Slug->save();
		        }

		        $json['slug_url'] = base_url($Slug->slug);
		        $json['success'] = __('user.slug_saved_successfully');
		    }
		}


		echo json_encode($json);
		die;
	}
			
	public function delete_slug(){
	    $json = array();

	    $userdetails = $this->userdetails();
	    $post = $this->input->post(null,true);

	    $Slug = Slug::where('type', $post['type'])
	                ->where('user_id', (int)$userdetails['id'])
	                ->where('related_id', (int)$post['related_id'])
	                ->first();

	    if(!$Slug){
	        $json['error'] = __('user.slug_not_found');
	    } else {
	        $url = ""; // Initialize URL
	        
	        if($Slug->type == 'register'){
	            $url = base_url('register/' . base64_encode($Slug->user_id));
	        } else if($Slug->type == 'store'){
	            $url = base_url('store/' . base64_encode($Slug->user_id));
	        } else if($Slug->type == 'product'){
	            $result = $this->db->query("SELECT product_slug FROM product WHERE `product_id` = '".(int)$Slug->related_id."'")->row();
	            $url = base_url('store/'.base64_encode($Slug->user_id).'/product/'.$result->product_slug);
	        } else if($Slug->type == 'form'){
	            $result = $this->db->query("SELECT seo FROM form WHERE `form_id` = '".(int)$Slug->related_id."'")->row();
	            $url = base_url('form/'.$result->seo.'/'.base64_encode($Slug->user_id));
	        } else {
	            $result = $this->db->query("SELECT target_link FROM integration_tools WHERE `id` = '".(int)$Slug->related_id."' AND `tool_type` = '".$Slug->type."'")->row();
	            if($result){
	                $url = $result->target_link;
	            }
	        }

	        $Slug->delete();
	        
	        $json['url'] = $url;
	        $json['success'] = __('user.slug_deleted_successfully');
	    }

	    echo json_encode($json);
	    die;
	}

	public function get_slug(){
	    $json = array();

	    $userdetails = $this->userdetails();
	    $post = $this->input->post(null, true);

	    $Slug = Slug::where('type', $post['type'])
	                ->where('user_id', (int)$userdetails['id'])
	                ->where('related_id', (int)$post['related_id'])
	                ->first();

	    if ($Slug) {
	        $json['slug'] = $Slug->slug;
	        $json['slug_url'] = base_url($Slug->slug);
	        $json['success'] = true;
	    } else {
	        $json['success'] = false;
	    }

	    echo json_encode($json);
	    die;
	}

	function _alpha_dash_space($str_in){
		$post = $this->input->post(null,true);

		$userdetails = $this->userdetails();
		$ignoreSlugs = array('store','usercontrol','admincontrol','product','auth','resetpassword','form','membership_callback','cronjob','admin','login','register','forget-password','default_controller','backend','page');

		if (!preg_match("/^([-a-z0-9])+$/i", $str_in)){
			$this->form_validation->set_message('_alpha_dash_space', 'The %s field may only contain alpha-numeric characters and dashes.');
			return FALSE;
		}else if(in_array($str_in, $ignoreSlugs)){
			$this->form_validation->set_message('_alpha_dash_space', 'You can\'t use specific word in slug');
			return FALSE;
		}else{
			$Slug = Slug::where('slug', 'like', $str_in)->where('type', '!=', $post['type'])->first();
			if($Slug){
				$this->form_validation->set_message('_alpha_dash_space', '%s already used, Enter unique slug.');
				return FALSE;
			}else{
				return TRUE;
			}
		}
	}


	public function downloadToolCode($id, $category) {
		$userdetails = $this->userdetails();
		if(empty($userdetails)){ die(); }

		$user_id = $userdetails['id'];

		$files = [];	

		if($category == 'form') {
			$this->load->model("Form_model");

			$data['user_id'] = $userdetails['id'];
			$getForm = $this->Form_model->getForm($id);

			$code = array();
			$code[] = '<a href="'. base_url('form/'. $getForm['seo'] .'/'.base64_encode($user_id) ) .'">';
			$code[] = '    <h3>'. $getForm['title'] .'</h3>';
			if ($getForm['fevi_icon']) {
				$code[] = '    <img src="'. base_url('assets/images/form/favi/'.$getForm['fevi_icon']) .'" style="max-width:100%">';
			}
			$code[] = '</a>';

			$files[] = ["code.txt", implode("\n", $code)];

			$zip_file_name = str_replace(" ", "-", $getForm['title']);
			
		}

		if($category == 'product') {
			$getProduct 	= $this->Product_model->getProductByIdArray($id);
			$productLink = base_url('store/'. base64_encode($user_id) .'/product/'.$getProduct['product_slug'] );
			$product_featured_image = $getProduct['product_featured_image'] != '' ? $getProduct['product_featured_image'] : '' ; 
			$product_featured_image = base_url('assets/images/product/upload/thumb/'. $product_featured_image);

			$code = array();
			$code[] = '<a href="'. $productLink .'">';
			$code[] = '<h3>'. ($getProduct['product_name'] ? $getProduct['product_name'] : '') .'</h3>';
			$code[] = '<img src="'. $product_featured_image .'" width="200" height="200" border="0" class="img-responsive" />';
			$code[] = '</a>';

			$files[] = ["code.txt", implode("\n", $code)];

			$zip_file_name = str_replace(" ", "-", $getProduct['product_name']);
		}

		if($category == 'tool') {
			$this->load->model("IntegrationModel");
			$tool = $this->IntegrationModel->getProgramToolsByID($id);
			
			$zip_file_name = str_replace(" ", "-", $tool['name']);

			if($tool['type'] == 'banner') {
				foreach ($tool['ads'] as $key => $value) {
					$a_link = $this->prepareParamLink($tool['target_link'],"af_id",_encrypt_decrypt($user_id."-".$value['id']));

					$files[] = ["share_link.txt", $a_link];

					$code = '<a href="'.$a_link.'"><img src="'. $value['value'] .'" ></a>';

					$files[] = ["code.txt", $code];

					$imgSize = $value['size'];

					$imgArr = explode(".", $value['value']);

					$files[] = ["preview-".trim($imgSize).".".$imgArr[sizeof($imgArr) - 1], file_get_contents($value['value'])];
				}
			} else if($tool['type'] == 'text_ads') {
				$value = $tool['ads'][0];
				
				if($value){
					$style = array(
						'padding : 5px',
						'white-space : pre-line',
						'border : solid '. $value['text_border_color'] .' 1px',
						'display : inline-block',
						'line-height : 1',
						'color : '. $value['text_color'],
						'background-color :'. $value['text_bg_color'],
						'font-size :'. $value['text_size']."px",
					);
					
					$a_link = $this->prepareParamLink($tool['target_link'],"af_id",_encrypt_decrypt($user_id."-".$value['id']));

					$code = '<span style="'. implode(";", $style) .'"><a style="display: block;color: inherit;font-size: inherit;" href="'. $a_link .'">'. $value['value'] .'</a></span>';
					
					$files[] = ["share_link.txt", $a_link];
					$files[] = ["code.txt", $code];
				}
			} else if($tool['type'] == 'link_ads'){

				$value = $tool['ads'][0];
				if($value){
					$a_link = $this->prepareParamLink($tool['target_link'],"af_id",_encrypt_decrypt($user_id."-".$value['id']));
					$code = '<a style="display: block;font-size: 12px;" href="'. $a_link .'">'. $value['value'] .'</a>';
					$files[] = ["share_link.txt", $a_link];
					$files[] = ["code.txt", $code];
				} 

			} else if($tool['type'] == 'video_ads'){
				$value = $tool['ads'][0];
				if($value){
					$a_link = $this->prepareParamLink($tool['target_link'],"af_id",_encrypt_decrypt($user_id."-".$value['id']));

					$code = isset($value['iframe']) ? $value['iframe'] : '';
					$code .= '<div style="display:table;clear:both;"></div><br><a style="-moz-box-shadow:inset 0 1px 0 0 #fff;-webkit-box-shadow:inset 0 1px 0 0 #fff;box-shadow:inset 0 1px 0 0 #fff;background:-webkit-gradient(linear,left top,left bottom,color-stop(.05,#f9f9f9),color-stop(1,#e9e9e9));background:-moz-linear-gradient(top,#f9f9f9 5%,#e9e9e9 100%);background:-webkit-linear-gradient(top,#f9f9f9 5%,#e9e9e9 100%);background:-o-linear-gradient(top,#f9f9f9 5%,#e9e9e9 100%);background:-ms-linear-gradient(top,#f9f9f9 5%,#e9e9e9 100%);background:linear-gradient(to bottom,#f9f9f9 5%,#e9e9e9 100%);filter:progid:DXImageTransform.Microsoft.gradient(startColorstr=\'#f9f9f9\', endColorstr=\'#e9e9e9\', GradientType=0);background-color:#f9f9f9;-moz-border-radius:6px;-webkit-border-radius:6px;border-radius:6px;border:1px solid #dcdcdc;display:inline-block;cursor:pointer;color:#666;font-family:Arial;font-size:15px;font-weight:700;padding:6px 24px;text-decoration:none;text-shadow:0 1px 0 #fff" href="'. $a_link .'">'. $value['size'] .'</a>';
					$files[] = ["share_link.txt", $a_link];
					$files[] = ["code.txt", $code];
				}
			}
		}

		if (!file_exists('assets/user_upload/downloaded_tools')) {
			mkdir('assets/user_upload/downloaded_tools', 0777, true);
		}

		$existingfiles = glob('assets/user_upload/downloaded_tools/*');
		foreach($existingfiles as $existingfile) {
			if(is_file($existingfile)) {
				unlink($existingfile);
			}
		}

		if(!empty($files)) {
			$this->load->library('zip');

			foreach($files as $file) {
				$this->zip->add_data($file[0], $file[1]);
			}

			$this->zip->archive('assets/user_upload/downloaded_tools/'.$zip_file_name.'.zip');

			echo base_url('assets/user_upload/downloaded_tools/'.$zip_file_name.'.zip');
		}
	}

	private function prepareParamLink($url, $key, $value) {
		$url = preg_replace('/(.*)(?|&)'. $key .'=[^&]+?(&)(.*)/i', '$1$2$4', $url .'&');
		$url = substr($url, 0, -1);
		
		if (strpos($url, '?') === false) {
			return ($url .'?'. $key .'='. $value);
		} else {
			return ($url .'&'. $key .'='. $value);
		}
	}

	public function get_payment_methods() {
		$vendorDepositStatus = $this->Product_model->getSettings('vendor', 'depositstatus');
		
		if($vendorDepositStatus['depositstatus']){
			$files = array();
			foreach (glob(APPPATH."/payment_gateway/controllers/*.php") as $file)
				$files[] = $file;

			$allPaymentGateways = array_unique($files);
			$activePaymentGateways = [];
			$defaultPaymntGateway = [];
			foreach($allPaymentGateways as $key => $filename){
				if(!str_contains($filename,'cod.php')){
					$paymentGateway = basename($filename,".php");

					$result = $this->Product_model->getSettings('payment_gateway_deposit_'.$paymentGateway,'status');
					$install = $this->Product_model->getSettings('payment_gateway_'.$paymentGateway,'is_install');
					if(isset($result['status']) && $result['status'] && $install['is_install']){
						require $filename;

						$object = new $paymentGateway($this);

						$activePaymentGateways[$paymentGateway] = $this->Product_model->getSettings('payment_gateway_'.$paymentGateway);

						$activePaymentGateways[$paymentGateway]['title'] = $object->title;
						$activePaymentGateways[$paymentGateway]['icon'] = $object->icon;
						$activePaymentGateways[$paymentGateway]['name']  = $paymentGateway;

						$where = array('setting_key'=>'status','setting_type'=>'payment_gateway_deposit_'.$paymentGateway,'setting_is_default'=>1);
						$is_default = $this->Common_model->get_total_rows('setting',$where);
						if($is_default){
							$defaultPaymntGateway[$paymentGateway] = $activePaymentGateways[$paymentGateway];
							unset($activePaymentGateways[$paymentGateway]);
						}
					}
				}
			}
			$data['payment_gateways'] = array_merge($defaultPaymntGateway,$activePaymentGateways);
			$this->session->set_userdata('payment_gateways',$data['payment_gateways']);

			$json['payment_gateways_count'] = count($data['payment_gateways']);
			$json['html'] = $this->load->view("usercontrol/payment/payment_methods", $data, true);
		} else {
			$json['error'] = true;
		}
		
		echo json_encode($json);
	}

	public function confirm_deposit(){
		$vendorDepositStatus = $this->Product_model->getSettings('vendor', 'depositstatus');
		if($vendorDepositStatus['depositstatus']){
			$data = $this->input->post(null,true);
			$userdetails = $this->Product_model->userdetails('user', true);
			if(!isset($userdetails['id']) || empty($userdetails['id'])){
				$json['error'] = __('user.unauthorized_access');
			} else if($data['payment_gateway'] == 'paystack' && !in_array($this->session->userdata('userCurrency'), 
				['GHS' , 'NGN', 'USD', 'ZAR'])){
				$json['error'] = "<div class='alert alert-danger'>".__('user.paystack_accept_only_currency')."</div>";
			} else if ($data['payment_gateway'] == 'xendit' && ($this->session->userdata('userCurrency') != 'IDR' && $this->session->userdata('userCurrency') != 'PHP')) {
			    $json['error'] = "<div class='alert alert-danger'>".__('user.xendit_accept_only_currency')."</div>";
			} else if($data['payment_gateway'] == 'yookassa' && $this->session->userdata('userCurrency') != 'RUB'){
				$json['error'] = "<div class='alert alert-danger'>".__('user.yookassa_accept_only_currency')."</div>";
			} else if((int)$data['amount'] > 0)
			{
				$vendorMinDepositAmt = $this->Product_model->getSettings('site', 'vendor_min_deposit');

				if(isset($vendorMinDepositAmt) && is_array($vendorMinDepositAmt))
					$vendorMinDepositAmt=$vendorMinDepositAmt['vendor_min_deposit'];
				else 
					$vendorMinDepositAmt=0;

				$total_deposited = $this->db->query("SELECT SUM(vd_amount) as total FROM vendor_deposit WHERE vd_status=1 AND vd_user_id=".(int) $userdetails['id'])->row()->total;

				$requiredeposit=$vendorMinDepositAmt-$total_deposited;
				 
				$vendor_deposit['vd_user_id'] = (int) $userdetails['id'];

				$default_currency = $this->db->query("SELECT `code` FROM currency WHERE is_default=1")->row_array();
				$vendor_deposit['vd_amount'] = $this->currency->convert($data['amount'],
					$this->session->userdata('userCurrency'),
					$default_currency['code']);

				$vendor_deposit['vd_status'] = 13;
				$vendor_deposit['vd_payment_method'] = $data['payment_gateway'];

				if(c_format($vendor_deposit["vd_amount"])>=c_format($requiredeposit))
				{
					$paymentGateways = $this->session->userdata('payment_gateways');
					if($paymentGateways && isset($paymentGateways[$data['payment_gateway']])){
						require APPPATH."/payment_gateway/controllers/". $data['payment_gateway'] .".php";

						$paymentGateway = $data['payment_gateway'];
						$this->session->set_userdata('payment_gateway',$paymentGateway);

						$object = new $paymentGateway($this);

						$insert['payment_module'] = 2;
						$insert['user_id']= (int) $userdetails['id'];
						$insert['content']= serialize($vendor_deposit);
						$insert['datetime'] = date('Y-m-d H:i:s');
						$this->db->insert('uncompleted_payment',$insert);
						$uncompleted_id = $this->db->insert_id();

						$this->session->set_userdata('uncompleted_id',$uncompleted_id);

						$settingData = $paymentGateways[$paymentGateway];

						$country = $this->Product_model->getByField('states','id',$userdetails['Country']);
						$userdetails['sortname'] = $country['sortname'];

						require APPPATH.'/payment_gateway/module/deposit/view.php';
						$gatewayData = prepareDataForView($paymentGateway,$uncompleted_id,$userdetails,$vendor_deposit);
						
						ob_start();
						$object->getPaymentGatewayView($settingData,$gatewayData);
						$json['confirm'] = ob_get_clean();
					}

				}
				else
				{
					$json['requireamt']=$requiredeposit;
					$json['error'] = "<p class='text-danger'>".$this->session->userdata('userCurrency')." ". $requiredeposit. " " .__('user.minimum_deposit_amount_required')."</p>";
				}
					

			} else {
				$json['error'] = "<p class='text-danger'>".__('user.please_enter_valid_amount')."</p>";
			}
		} else {
			$json['error'] = __('user.deposit_module_disabled_info');
		}

		echo json_encode($json);
		die();
	}

	public function payment_confirmation(){
		$json = array();
		
		$meta = array();

		$post = $this->input->post(null,true);
		
		if(isset($post['comment']) && !empty($post['comment'])){
			if(is_array($post['comment'])) {
				$meta["comment"] = "";
				foreach($post['comment'] as $key => $value){
					if(empty($value['comment']))
						$json['errors']['comment'][$key] = "Comment can not be blank!";
					else
						$meta["comment"] .=  isset($value['comment']) ? $value['comment'] : $value;
				}
			} else {
				$meta["comment"] = $post['comment'];
			}
		}

		if(isset($post['bank_method']) && isset($post['bank_details'][$post['bank_method']])){
			$meta["bank_details"] = $post['bank_details'][$post['bank_method']];
			$meta["bank_details"] = str_replace("\r\n", "<br>", $meta["bank_details"]);
		}

		if(!$json['errors']){
			$uncompletedData = $this->Product_model->getByField('uncompleted_payment','id',$this->session->userdata('uncompleted_id'));
			$vendor_deposit = unserialize($uncompletedData['content']);

			$vendor_deposit['vd_meta'] = json_encode($meta,JSON_UNESCAPED_UNICODE);
			$uncompleted['content'] = serialize($vendor_deposit);
			$this->db->update('uncompleted_payment',$uncompleted,array('id' => $this->session->userdata('uncompleted_id')));

			$json['success'] = true;
		}else{
			$json['success'] = false;
		}

		echo json_encode($json);
		die;
	}

	public function confirm_payment(){
		$comment = $this->input->post('comment',true);

		$paymentGateways = $this->session->userdata('payment_gateways');
		$paymentGateway = $this->session->userdata('payment_gateway');
		if($paymentGateways && isset($paymentGateways[$paymentGateway])){
			$uncompletedData = $this->Product_model->getByField('uncompleted_payment','id',$this->session->userdata('uncompleted_id'));
			$vendor_deposit = unserialize($uncompletedData['content']);

			$file = isset($_FILES['payment_proof']) ? $_FILES['payment_proof'] : false;
			if((int)$payment_methods[$code]['setting_data']['proof'] == 1 && !$file){
				$json['errors']['payment_proof'] = 'Payment proof is required!';
			} else if($file){
				$extension = pathinfo($file["name"], PATHINFO_EXTENSION);
				$allow_ext = ['pdf', 'doc', 'docs', 'jpg', 'jpeg', 'png', 'PNG'];
				if(in_array($extension, $allow_ext)){
					$name = 'pp-'.time().$file['name'];
					move_uploaded_file($file['tmp_name'], FCPATH.'/assets/user_upload/'.$name);

					if(isset($meta->vd_meta))
						$meta = json_decode($meta->vd_meta, true);
					else
						$meta = [];

					$meta['payment_proof'] = $name;

					$vendor_deposit['vd_meta'] = json_encode($meta,JSON_UNESCAPED_UNICODE);
					$uncompleted['content'] = serialize($vendor_deposit);
					$this->db->update('uncompleted_payment',$uncompleted,array('id' => $this->session->userdata('uncompleted_id')));
				} else {
					$json['errors']['payment_proof'] = 'Allow only pdf | doc | docs | jpg | jpeg | png';
				}
			}

			if($this->input->post('bank_method') != ''){
				$this->session->set_userdata('bank_method_index', $this->input->post('bank_method'));
			}

			if(!isset($json['errors'])){
				if($this->session->userdata('uncompleted_id')){
					require APPPATH."/payment_gateway/controllers/". $paymentGateway .".php";
					$object = new $paymentGateway($this);

					$settingData = $paymentGateways[$paymentGateway];

					$userdetails = $this->Product_model->userdetails('user', true);

					require APPPATH.'/payment_gateway/module/deposit/request.php';
					$gatewayData = prepareDataForRequest($paymentGateway,$this->session->userdata('uncompleted_id'),$userdetails,$vendor_deposit);

					$json = $object->setPaymentGatewayRequest($settingData,$gatewayData);
				} else {
					$json['redirect'] = base_url('usercontrol/my_deposits');
				}
			}
		}

		echo json_encode($json);
		die;
	}

	public function paymentGateway($paymentGateway, $method, $uncompleted_id = '', $action = ''){
		if(is_file(APPPATH.'/payment_gateway/controllers/'.$paymentGateway.'.php')){
			require APPPATH.'/payment_gateway/controllers/'.$paymentGateway.'.php';

			$object = new $paymentGateway($this);

			$settingData = $this->Product_model->getSettings('payment_gateway_'.$paymentGateway);
			
			require APPPATH.'/payment_gateway/module/deposit/callback.php';
			$gatewayData = prepareDataForCallback($paymentGateway,$method,$uncompleted_id,$action);

			$object->$method($settingData,$gatewayData);
		}
	}
	
	public function confirmPaymentGateway($uncompleted_id, $status, $transaction_id = '', $comment = ''){
		$ex = new Exception();
		$trace = $ex->getTrace(); 
		if(!isset($trace[1]['class'])){ 
			return false; 
		}

		$paymentGateway = $trace[1]['class']; 
		$filename = APPPATH."/payment_gateway/controllers/{$paymentGateway}.php";
		require_once $filename;

		$object = new $paymentGateway($this);
		if($object->title){
			$uncompletedData = $this->Product_model->getByField('uncompleted_payment','id',$uncompleted_id);
			$vendor_deposit = unserialize($uncompletedData['content']);
			$vendor_deposit['vd_status'] = (int) $status;
			$vendor_deposit['vd_txn_id'] = $transaction_id;

			$this->db->insert('vendor_deposit',$vendor_deposit);
			$vendor_deposit_id = $this->db->insert_id();

			$uncompleted['completed_id'] = $vendor_deposit_id;
			$this->db->update('uncompleted_payment',$uncompleted,array('id' => $uncompleted_id));

			$this->load->model('Deposit_payment_model');
			$this->Deposit_payment_model->apiAddVendorDepositHistory($vendor_deposit_id,[
				'status_id' => (int)$status,
				'comment' => (!empty($comment)) ? $comment : 'system generated status at time of payment',
				'transaction_id' => $transaction_id,
			]);

			$this->load->model('Mail_model');
			$deposit = $this->db->query('SELECT * FROM vendor_deposit WHERE vd_id='.$vendor_deposit_id)->row();

			$this->Mail_model->send_vendor_deposit_mail($deposit,'added');
			return true;
		}

		return false;
	}

	public function mlm_levels(){
		$userdetails = $this->userdetails();

		if(empty($userdetails)) redirect('usercontrol/dashboard');

		$market_vendor = $this->Product_model->getSettings('market_vendor');
		$vendor_setting = $this->Product_model->getSettings('vendor');


		if(!isset($userdetails['is_vendor']) || $userdetails['is_vendor'] != 1)
			redirect('usercontrol/dashboard');

		if(!isset($market_vendor) || $market_vendor['vendormlmmodule'] != 1)
			redirect('usercontrol/dashboard');

		if((int)$market_vendor['marketvendorstatus'] != 1 && (int)$vendor_setting['storestatus'] != 1)
			redirect('usercontrol/dashboard');

		$commonSetting = array('referlevel','referlevel_1','referlevel_2','referlevel_3','referlevel_4','referlevel_5','referlevel_6','referlevel_7','referlevel_8','referlevel_9','referlevel_10','referlevel_11','referlevel_12','referlevel_13','referlevel_14','referlevel_15','referlevel_16','referlevel_17','referlevel_18','referlevel_19','referlevel_20','referlevel');

		$post = $this->input->post(null,true);
		if(!empty($post)){
			if(!isset($post['referlevel']['disabled_for'])){ 
				$post['referlevel']['disabled_for'] = array(); 
			}

			foreach ($post as $key => $value) {
				if (in_array($key, $commonSetting)) {
					$this->Setting_model->vendorSave($userdetails['id'], $key, $value);
				}
			}

			if(!isset($json['errors'])){
				$json['success'] =  __('user.setting_saved_successfully');
			}

			echo json_encode($json);die;

		} else {
			$data['vendorSettingTab'] = 'mlm_levels';
			$data['CurrencySymbol'] = $this->currency->getSymbol();

			foreach ($commonSetting as $key => $value)
				$data[$value] 	= $this->Product_model->getVendorSettings($userdetails['id'], $value);

			$this->view($data,'setting/mlm_levels','usercontrol');
		}
	}

	public function mlm_settings(){
		$userdetails = $this->userdetails();

		if(empty($userdetails)) redirect('usercontrol/dashboard');

		$market_vendor = $this->Product_model->getSettings('market_vendor');
		$vendor_setting = $this->Product_model->getSettings('vendor');

		if(!isset($userdetails['is_vendor']) || $userdetails['is_vendor'] != 1)
			redirect('usercontrol/dashboard');

		if(!isset($market_vendor) || $market_vendor['vendormlmmodule'] != 1)
			redirect('usercontrol/dashboard');

		if((int)$market_vendor['marketvendorstatus'] != 1 && (int)$vendor_setting['storestatus'] != 1)
			redirect('usercontrol/dashboard');

		$post = $this->input->post(null,true);

		if(!empty($post)){
			if(!isset($post['referlevel']['disabled_for'])){ 
				$post['referlevel']['disabled_for'] = array(); 
			}

			foreach($post as $key => $value){
				if($key == 'referlevel')
					$this->Setting_model->vendorSave($userdetails['id'], $key, $value);
			}

			if(!isset($json['errors'])){
				$json['success'] =  __('user.setting_saved_successfully');
			}

			echo json_encode($json);die;
		} else {
			$data['vendorSettingTab'] = 'mlm_settings';
			$data['CurrencySymbol'] = $this->currency->getSymbol();

			$data['referlevel'] = $this->Product_model->getVendorSettings($userdetails['id'], 'referlevel');

			$this->view($data,'setting/mlm_settings','usercontrol');
		}
	}

	public function wallet_setting(){
		$userdetails = $this->userdetails();

		if(empty($userdetails)) redirect('usercontrol/dashboard');

		$market_vendor = $this->Product_model->getSettings('market_vendor');
		if(!isset($userdetails['is_vendor']) || $userdetails['is_vendor'] != 1 || (int)$market_vendor['marketvendorstatus'] != 1)
			redirect('usercontrol/dashboard');
		
		$commonSetting = array('referlevel');

		$post = $this->input->post(null,true);
		if(!empty($post)){
			$json = array();
			if(!isset($json['errors'])){
				foreach($post as $key => $value){
					if(in_array($key,$commonSetting)){
						$this->Setting_model->vendorSave($userdetails['id'], $key, $value);
					}
				}
				if(!isset($json['errors']))
					$json['success'] =  __('user.setting_saved_successfully');
			}
			echo json_encode($json);
			die;
		}

		$data['vendorSettingTab'] = 'wallet_setting';
		$data['CurrencySymbol'] = $this->currency->getSymbol();
		foreach($commonSetting as $key => $value)
			$data[$value] 	= $this->Product_model->getVendorSettings($userdetails['id'], $value);

		$this->view($data,'setting/wallet_setting','usercontrol');
	}

	public function share_sales_setting(){
		$userdetails = $this->userdetails();

		if(empty($userdetails)) redirect('usercontrol/dashboard');

		$market_vendor = $this->Product_model->getSettings('market_vendor');

		if(!isset($userdetails['is_vendor']) || $userdetails['is_vendor'] != 1 || (int)$market_vendor['marketvendorstatus'] != 1)
			redirect('usercontrol/dashboard');
		 
		$post = $this->input->post(null,true);
		if(!empty($post)){
			$json = array();
			if(!isset($json['errors']))
			{
				$update = [
					'vendor_shares_sales_status' => $post['vendor_shares_sales_status']
				];

				$id = $this->db->query("SELECT * FROM vendor_setting WHERE user_id=". (int)$userdetails['id'] ." ")->row();


				if($id){
					$this->db->update("vendor_setting", $update, ['user_id'=> (int)$userdetails['id'] ]);
				} else{
					$update['user_id']=(int)$userdetails['id'];
					$this->db->insert("vendor_setting", $update);
				}


				 
				if(!isset($json['errors']))
					$json['success'] =  __('user.setting_saved_successfully');
			}
			echo json_encode($json);
			die;
		}

		$data['vendorSettingTab'] = 'share_sales_setting';
		$data['CurrencySymbol'] = $this->currency->getSymbol();
		$data['setting']=$this->db->query("SELECT * FROM vendor_setting WHERE user_id=". (int)$userdetails['id'] ." ")->row_array();

		$this->view($data,'setting/share_sales_setting','usercontrol');
	}

	public function setCookie(){
		$userdetails = $this->userdetails();
		if(empty($userdetails)) redirect('usercontrol/dashboard');

		$this->load->helper('cookie');

		$name = $this->input->post('name',true).'_'.$userdetails['id'];
		setcookie($name,true,time() + 3600 * 24 * 30,'/');

		if(get_cookie($name))
			$result = true;
		else 
			$result = false;

		echo json_encode($result);
		die();
	}

	public function getSettings($key){
		return $this->Product_model->getSettings($key);
	}

	public function check_campaign_security_with_id($id) {
		$userdetails = $this->userdetails();
		if(empty($userdetails)) redirect('usercontrol/dashboard');

		$market_vendor = $this->Product_model->getSettings('market_vendor');
		if((!isset($userdetails['is_vendor']) || !$userdetails['is_vendor']) || (int)$market_vendor['marketvendorstatus'] == 0) show_404();

		if((int) $id){
			$data = [];
			$tool = $this->IntegrationModel->getProgramToolsByID($id);

			if(!empty($tool) && $tool['vendor_id'] == $userdetails['id']){
				$security_alerts = external_integration_security_check($tool['target_link']);
				$status = getSecurityStatus($security_alerts,$tool['tool_type'],$tool['tool_integration_plugin'],$tool['program_id']);

				if($tool['security_status'] == 1 && $status == 0){
					$this->db->query('UPDATE integration_tools SET security_status=0 WHERE id='.$tool['id']);
					$data['security_status'] = 0;
					$data['statusClass'] = 'badge bg-info';
					$data['message'] = __('user.pending_integration');
					$data['integration_code_button'] = '<button data-bs-toggle="tooltip" title="'.__('user.integration_code').'" 
					class="btn-show-code badge bg-info" data-id="'.$tool['id'].'">
					<i class="fa fa-code" aria-hidden="true"></i>
					</button>';
				}

				if($tool['security_status'] == 0 && $status == 1){
					$this->db->query('UPDATE integration_tools SET security_status=1 WHERE id='.$tool['id']);
					$data['security_status'] = 1;
					$data['statusClass'] = 'badge bg-success';
					$data['message'] = __('user.approved');
				}
			}
			
			echo json_encode($data);
		}	
	}

	public function check_campaign_security() {
		$userdetails = $this->userdetails();
		if(empty($userdetails)) redirect('usercontrol/dashboard');

		$market_vendor = $this->Product_model->getSettings('market_vendor');
		if((!isset($userdetails['is_vendor']) || !$userdetails['is_vendor']) || (int)$market_vendor['marketvendorstatus'] == 0) show_404();

		if ($this->input->server('REQUEST_METHOD') == 'POST'){

			$result = [];

			$post = $this->input->post(null,true);

			$offset = isset($post['index']) ? $post['index'] - 1 : 0;

			$tool = $this->db->query('SELECT * FROM integration_tools WHERE vendor_id = '.$userdetails['id'].' LIMIT '.$offset.', 1')->row_array();

			if(!empty($tool)){
				$integration_tools_count = $this->db->query('SELECT COUNT(id) as tools_count FROM integration_tools WHERE vendor_id = '.$userdetails['id'])->row()->tools_count;

				if($integration_tools_count > $post['index'])
					$result['index'] = $post['index'] + 1;

				if($integration_tools_count > 0)
					$result['progress_percentage'] = (($post['index'] / $integration_tools_count) * 100)."%";

				$security_alerts = external_integration_security_check($tool['target_link']);

				$status = getSecurityStatus($security_alerts,$tool['tool_type'],$tool['tool_integration_plugin'],$tool['program_id']);
				
				if($tool['security_status'] == 1 && $status == 0){
					$this->db->query('UPDATE integration_tools SET security_status=0 WHERE id='.$tool['id']);
					$tool['security_status'] = 0;
				}


				if($tool['security_status'] == 0 && $status == 1){
					$this->db->query('UPDATE integration_tools SET security_status=1 WHERE id='.$tool['id']);
					$tool['security_status'] = 1;
				}

				if($tool['security_status']){
					$result['security_status'] = 'approved';
					$result['message'] = __('user.campaigns_verified_successfully');
				} else {
					$result['security_status'] = 'pending';
					$result['message'] = __('user.campaigns_in_pending_integration');
				}
			} else {
				$result['warning'] = true;
			}
			
			echo json_encode($result);
		}
	}

	public function updateComment() {

		if ($this->input->server('REQUEST_METHOD') === 'POST') {
			$comment = $this->input->post('comment');
			$index = $this->input->post('id');
			$tool_id = $this->input->post('tool_id');
			$old = $this->db->query("SELECT * FROM integration_tools WHERE id=". (int)$tool_id)->row(); 
			$oldcomment = json_decode($old->comment,1);
			$oldcomment[$index]['comment'] = $comment;
			$data = json_encode($oldcomment);
			$this->db->update("integration_tools",['comment'=>$data],['id' => $tool_id]);
			echo json_encode(['status'=>true]);
			exit;
			
		}
	}

	public function tickets() {

		$userdashboard_settings = $this->Common_model->getUserDashboardSettings();

		if(! isShowUserControlParts($userdashboard_settings['tickets_page'])) {
			show_404();
		}

		$userdetails = $this->userdetails();
		$this->load->model('Tickets_model');
		if(empty($userdetails)){ redirect('/login'); }
		if ($this->input->server('REQUEST_METHOD') == 'POST'){
			$this->load->library('datatables');
			
			echo $res = $this->Tickets_model->getUserTickets($userdetails['id']);
			exit;	
		}
		$data['status'] = $this->Tickets_model->status();
		$data['subjects'] = $this->Tickets_model->getsubjectlist();

		$this->view($data,'tickets/tickets','usercontrol');
	}
	
	public function createticket(){
		$userdetails = $this->userdetails();

		if(empty($userdetails)){ redirect('/login'); }

		if ($this->input->server('REQUEST_METHOD') == 'POST'){
			$this->load->library('form_validation');
			$this->form_validation->set_rules('subject_id', 'Subject', 'required');
			$this->form_validation->set_rules('message', 'Message', 'required' );

			if($this->form_validation->run()){
				$data = $this->input->post(null);
				$imageFile=[];

				if(isset($_FILES['attachment']) && !empty($_FILES['attachment']['name'])) {


					$count_file = count($_FILES['attachment']['name']);
					$files = $_FILES['attachment'];	

					for($i=0; $i<$count_file; $i++){

						$extension = pathinfo($files['name'][$i], PATHINFO_EXTENSION);
						if(!empty(trim($files['name'][$i]))){
							if(in_array($extension, ['png','gif','jpeg','jpg','PNG','GIF','JPEG','JPG','ICO','ico','zip','doc','docs','pdf','xls','xlsx','ppt','pptx','txt'])) {

								$Fname= md5(random_string('alnum', 10)).'.'.$extension;
								$destinationPath = 'assets'.DIRECTORY_SEPARATOR.'user_upload'.DIRECTORY_SEPARATOR.$Fname;
								if(empty($files['error'][$i])){
									if(@move_uploaded_file($files['tmp_name'][$i], $destinationPath)) {
										$imageFile[] = $Fname;
									} else {
										echo json_encode(array('status' => false, 'message' => 'Fail to upload please try again'));
										exit;
									}
								} else {
									echo json_encode(array('status' => false, 'message' => 'Fail to upload please try again'));
									exit;
								}
							}

						}
					}
				}


				if(!isset($errors) || empty($errors)) {

					$data_to_add= [
						'user_id'=> $userdetails['id'],
						'subject_id'=> $data['subject_id'],
						'status'=> 1,
						'created_at'=>date('Y-m-d H:i:s'),
						'updated_at'=>date('Y-m-d H:i:s'),
						'ticket_id'=>'AF'.date(('Ymd'),time()).rand()
					];

					$is_generate = $this->Common_model->insert('tickets',$data_to_add);

					if($is_generate) {

						$data_to_replay= [
							'user_id'=> $userdetails['id'],
							'message'=> $data['message'],
							'attachment' => !empty($imageFile) ? json_encode($imageFile):'[]',
							'message_type' => !empty($imageFile) ? 2 : 1,
							'user_type'=> 2,
							'created_at'=>date('Y-m-d H:i:s'),
							'updated_at'=>date('Y-m-d H:i:s'),
							'ticket_id'=>$data_to_add['ticket_id']
						];

						$this->Common_model->insert('tickets_reply',$data_to_replay);
						
						$this->load->model('Mail_model');
						
						$this->Mail_model->send_ticket_mail($data_to_add['ticket_id'], 'ticket_created_email');

						$this->load->model('Product_model');
						$this->load->model('Tickets_model');
						
						$this->Product_model->sendTicketNotification([
							'id'	=> $data_to_add['ticket_id'],
							'type'	=> 'ticket_created',
							'title'	=> '#'.$data_to_add['ticket_id'].' '.__('user.new_ticket_created'),
							'desc'	=> $userdetails['username'].', '.__('user.has_created_new_ticket').', '.$this->Tickets_model->subject($data['subject_id']),
							'admin_notification'	=> 1,
							'user_notification'	=> $userdetails['id']
						]);

				$json['success'] = __('user.ticket_generated_successfully');

					} else {
						$json['errors'] = __('user.please_try_again');
					}
				} else {
					$json['errors']['attachment'] = $errors;
				}
			}else{
				$json['errors'] = $this->form_validation->error_array();
			}

			echo json_encode($json);die;
		}

		$data['notcheckapproval'] = 1; 
		$data['notcheckmember'] = 1;

		$data['userdetails'] = $this->Product_model->userdetails('user');
		

		$data['subjects'] = $this->Common_model->get_data_all_asc('tickets_subject',[],'id,subject','id');


		$this->view($data,'tickets/create','usercontrol');
	}
	public function ticketdetails($id){
		$userdetails = $this->userdetails();
		if(empty($userdetails)){ redirect('/login'); }
		$user_id = $userdetails['id'];
		$id =  decryptString($id);
		$this->load->model('Tickets_model');

		$res = $this->Tickets_model->getTicketDetails($id,$user_id);
		
		if(!empty($res)) {
			$data['details'] = $res;
			$data['statusNAme'] = $this->Tickets_model->status()[$res['status']];
			$data['userName'] = $userdetails['firstname'].' '.$userdetails['lastname'];
			$this->view($data,'tickets/tickets-details','usercontrol');
		} else {
			redirect(base_url('usercontrol/tickets'),'refres');
		}
	}

	public function getTickestReply() {
		$userdetails = $this->userdetails();
		if(empty($userdetails)){ redirect('/login'); }
		if ($this->input->server('REQUEST_METHOD') == 'POST'){
			$user_id = $userdetails['id'];
			$ticket_id = $this->input->post('ticket_id');
			$this->load->model('Tickets_model');
			$res = $this->Tickets_model->getTicketDetails($ticket_id,$user_id);
			if(!empty($res)) {	
				$res = $this->Tickets_model->getTickestReply($ticket_id);
				echo json_encode($res);	
			}
			exit;
		}
	}

	public function sendMessage() {
		$userdetails = $this->userdetails();
		if(empty($userdetails)){ redirect('/login'); }
		if ($this->input->server('REQUEST_METHOD') == 'POST'){

			$user_id = $userdetails['id'];
			extract($this->input->post(),true);
			$imageFile = [];
			if (isset($_FILES['attachment']) && !empty($_FILES['attachment']['name'])) {

				$count_file = count($_FILES['attachment']['name']);
				$files = $_FILES['attachment'];	

				for($i=0; $i<$count_file; $i++){

					$extension = pathinfo($files['name'][$i], PATHINFO_EXTENSION);
					if(!empty(trim($files['name'][$i]))){
						if(in_array($extension, ['png','gif','jpeg','jpg','PNG','GIF','JPEG','JPG','ICO','ico','zip','doc','docs','pdf','xls','xlsx','ppt','pptx','txt'])) {

							$Fname= md5(random_string('alnum', 10)).'.'.$extension;
							$destinationPath = 'assets'.DIRECTORY_SEPARATOR.'user_upload'.DIRECTORY_SEPARATOR.$Fname;
							if(empty($files['error'][$i])){
								if(@move_uploaded_file($files['tmp_name'][$i], $destinationPath)) {
									$imageFile[] = $Fname;
								} else {
									echo json_encode(array('status' => false, 'message' => 'Fail to upload please try again'));
									exit;
								}
							} else {
								echo json_encode(array('status' => false, 'message' => 'Fail to upload please try again'));
								exit;
							}
						}

					}
				}
			}
			$data_to_add = array(
				'ticket_id' => $ticket_id,
				'user_id' => $user_id,
				'message' =>  $sendMessage,
				'attachment' => !empty($imageFile) ? json_encode($imageFile):'[]',
				'message_type' => !empty($imageFile) ? 2 :1,
				'created_at' => date('Y-m-d H:i:s'),
				'user_type'=>2
			);
			$res = $this->Common_model->insert('tickets_reply', $data_to_add);
			$this->load->model('Tickets_model');
			$old_status = $this->Tickets_model->getTicketDetails($ticket_id,$user_id);
			if($old_status['status'] == 3) {
				$update_data =['updated_at'=>date('Y-m-d H:i:s'),'status'=>1]; 
			} else{
				$update_data =['updated_at'=>date('Y-m-d H:i:s')]; 
			}
			$res = $this->Common_model->update('tickets',['ticket_id' => $ticket_id],$update_data);
			if ($res) {
				$this->load->model('Mail_model');
				$this->Mail_model->send_ticket_mail($ticket_id, 'ticket_reply_email');

				$this->load->model('Product_model');
				
				$this->Product_model->sendTicketNotification([
					'id'	=> $ticket_id,
					'type'	=> 'ticket_reply',
					'title'	=> __('user.new_replay_on_ticket').' #'.$ticket_id,
					'desc'	=> $userdetails['username'].', '.__('user.has_replyed_on_ticket'),
					'admin_notification'	=> 1,
				]);

				echo json_encode(array('status' => true, 'data' => [], 'message' => 'message replay successfully'));
			} else {
				echo json_encode(array('status' => false, 'message' => 'Please try again'));
			}
		}
	}

	public function closetickets() {
		$userdetails = $this->userdetails();
		if(empty($userdetails)){ redirect('/login'); }
		if ($this->input->server('REQUEST_METHOD') == 'POST'){
			$user_id = $userdetails['id'];
			$ticket_id = $this->input->post('ticket_id');
			$this->load->model('Tickets_model');
			$res = $this->Tickets_model->getTicketDetails($ticket_id,$user_id);
			if(!empty($res)) {	
				$res = $this->Common_model->update('tickets',['user_id'=>$user_id,'ticket_id'=>$ticket_id],['status'=>3,'updated_at'=>date('Y-m-d H:i:s')]);
				echo json_encode(array('status' => true));
				$this->load->model('Mail_model');
				$this->Mail_model->send_ticket_mail($ticket_id, 'ticket_status_email');

				$this->load->model('Tickets_model');

				$this->Product_model->sendTicketNotification([
					'id'	=> $ticket_id,
					'type'	=> 'ticket_status_updated',
					'title'	=> '#'.$ticket_id.' '.__('user.ticket_has_closed'),
					'desc'	=> $userdetails['username'].', '.__('user.has_closed_ticket_on').' '.date('d M Y'),
					'admin_notification' => 1,
					'user_notification'	 => $this->Tickets_model->ticket_owner($ticket_id)
				]);
			}
			exit;
		}
	}

	public function getStaticData() {
		$userdetails = $this->userdetails();
		if(empty($userdetails)){ redirect('/login'); }
		$user_id 	= $this->userdetails()['id'];
		$where = ['user_id'=>$user_id];
		$this->load->model('Tickets_model');
		$data['total'] = $this->Tickets_model->getTotalCountTickest(null,'tickets',$where)['total']??0;
		$data['totalopen'] = $this->Tickets_model->getTotalCountTickest(1,'tickets',$where)['total']??0;
		$data['totalclose'] = $this->Tickets_model->getTotalCountTickest(3,'tickets',$where)['total']??0;
		echo json_encode($data);
	}

	public function uncompleted_payments(){
		$userdetails = $this->userdetails();
		if(empty($userdetails)) redirect($this->admin_domain_url);

		$filter = $this->input->post(null,true);
		
		$this->load->model('Order_model');
		$this->load->library('pagination');

		$config['base_url'] = base_url('usercontrol/uncompleted_payments');
		$config['uri_segment'] = 3;
		$filter['limit'] = $config['per_page'] = 10;
		$filter['user'] = $userdetails['id'];

		$config['total_rows'] = $this->Wallet_model->getUncompletedPayment($filter, true);
		$config['use_page_numbers'] = TRUE;
		$config['page_query_string'] = TRUE;
		$config['enable_query_strings'] = TRUE;
		$_GET['page'] = $filter['page'];
		$config['query_string_segment'] = 'page';
		$this->pagination->initialize($config);
		$view['pagination'] = $this->pagination->create_links();
		
		$uncompleted_payments = $this->Wallet_model->getUncompletedPayment($filter);

		$this->load->model('Deposit_payment_model');

		$view['uncompleted_payments'] = $this->Wallet_model->prepareUncompletedPaymentData(
			$uncompleted_payments,
			$this->Deposit_payment_model->status_list
		);

		$view['payment_methods'] = $this->Order_model->PaymentMethods();

		$this->load->config('payment_gateway');
		$view['payment_module'] = $data['payment_module'] = config_item('payment_module');


		$html = $this->load->view("admincontrol/users/part/uncompleted_payments",$view,true);
		
		if(isset($filter['ajax'])){
			echo $html;
			die();
		}

		$data['html'] = $html;
		
		$data['users'] =  $this->db->query('SELECT id, CONCAT(firstname, " ", lastname) as username FROM users')->result_array();
		
		$this->view($data,'users/uncompleted_payments','usercontrol');
	}

	public function listclients($page = 1){

		$userdetails = $this->userdetails();
		if(empty($userdetails)) redirect('usercontrol/dashboard');
		$vendor_setting = $this->Product_model->getSettings('vendor');
		$store_setting = $this->Product_model->getSettings('store');
		if(!isset($userdetails['is_vendor']) || $userdetails['is_vendor'] != 1 || (int)$vendor_setting['storestatus'] != 1 || (int)$store_setting['status'] != 1) redirect('usercontrol/dashboard');

		$data['user'] = $userdetails; 

		 
		if(isset($_POST['listclients'])) 
		{
			$vendor_id=$userdetails['id'];
				$page = max((int)$page,1);

			$filter = array(
				'limit' => 25,
				'page' => $page,
				'vendor_id' => $vendor_id 
			); 

			list($data['clientslist'],$total) = $this->Product_model->getVendorClients($filter);  
			$data['start_from'] = (($page-1) * $filter['limit'])+1; 
			$json['html'] = $this->load->view("usercontrol/clients/clients_list_tr", $data, true);

			$this->load->library('pagination');
			$config['base_url'] = base_url('usercontrol/listclients/');
			$config['per_page'] = $filter['limit'];
			$config['total_rows'] = $total;
			$config['use_page_numbers'] = TRUE;
			$config['enable_query_strings'] = TRUE;
			$this->pagination->initialize($config);
			$json['pagination'] = $this->pagination->create_links();
			echo json_encode($json);die;

			exit;
		} 

		$this->view($data,'clients/index','usercontrol'); 

	}

	public function getShippingDetails() {
		if($this->input->server('REQUEST_METHOD') === 'POST') {
			$user_id = $this->input->post('id');
			$data= $this->db->query("SELECT shipping_address.*,countries.name as country_name,states.name as state_name FROM shipping_address INNER JOIN countries ON countries.id=shipping_address.country_id INNER JOIN states ON states.id=shipping_address.state_id WHERE user_id = $user_id")->row_array();
			echo json_encode(['status'=>empty($data)?false:true,'data'=>$data]);
			exit;
		}

	}

	public function payment_details($id = null){

		$userdetails = $this->userdetails();
		if(empty($userdetails)){ redirect('/login'); }
		$post = $this->input->post(null,true);


		if (isset($post['add_paypal'])) 
		{
			$email = $this->input->post('paypal_email',true);
			if ((int)$post['id'] > 0) {
				$this->db->update("paypal_accounts", array(
					'paypal_email' => $email,
					'user_id' => $userdetails['id'],
				),
				array(
					'id' => $post['id']
				));
			}
			else
			{
				$this->db->insert("paypal_accounts", array(
					'paypal_email' => $email,
					'user_id' => $userdetails['id'],
				));
			}
			$this->session->set_flashdata('success', __('user.paypal_account_saved_successfully'));
			redirect('usercontrol/payment_details');
		}
		else if (isset($post['add_primary_payment'])) 
		{
			$primary_payment_method = $this->input->post('primary_payment_method',true);
			if (isset($primary_payment_method) && $primary_payment_method!="") {
				$this->db->update("users", array(
					'primary_payment_method' => $primary_payment_method 
				),
				array(
					'id' => $userdetails['id']
				));
				$this->session->set_flashdata('success', __('user.primay_payment_method_saved_successfully'));
			}
			else
			{
				$this->session->set_flashdata('error', __('user.primay_payment_method_not_saved'));
			} 
			
			redirect('usercontrol/payment_details');
		}
		 else if(!empty($post)){
			$this->load->helper(array('form', 'url'));
			

			$this->load->library('form_validation');
			

			$this->form_validation->set_rules('payment_account_number', __('user.account_number'), 'required');
			$this->form_validation->set_rules('payment_account_name', __('user.account_name'), 'required' );
			$this->form_validation->set_rules('payment_ifsc_code', __('user.ifsc_code'), 'required');
			if($this->form_validation->run())
			{
				$errors= array();
				

				$details = array(
					'payment_bank_name'      =>  $this->input->post('payment_bank_name',true),
					'payment_account_number' =>  $this->input->post('payment_account_number',true),
					'payment_account_name'   =>  $this->input->post('payment_account_name',true),
					'payment_ifsc_code'      =>  $this->input->post('payment_ifsc_code',true),
					'payment_status'         =>  1,
					'payment_ipaddress'      =>  $_SERVER['REMOTE_ADDR'],
				);
				if(empty($errors)){
					
					if( (int)$post['payment_id'] > 0 ){
						$this->session->set_flashdata('success', __('user.payment_updated_successfully'));
						$details['payment_updated_by'] = $userdetails['id'];
						$details['payment_updated_date'] = date('Y-m-d H:i:s');
						$this->Product_model->update_data('payment_detail', $details,array('payment_id' => (int)$post['payment_id']));

						redirect('usercontrol/payment_details');
					}
					else {
						$this->session->set_flashdata('success', __('user.payment_added_successfully'));
						$details['payment_created_by'] = $userdetails['id'];
						$details['payment_created_date'] = date('Y-m-d H:i:s');
						$this->Product_model->create_data('payment_detail', $details);

						redirect('usercontrol/payment_details');
					}
					

				} else {
					if(!empty($id)){
						$this->session->set_flashdata('error', $errors['avatar_error'] );
						redirect('usercontrol/payment_details');
					} else {
						$this->session->set_flashdata('error', $errors['avatar_error'] );
						redirect('usercontrol/payment_details');
					}
				}
			} 
			else 
			{
				 
				$this->session->set_flashdata('error', __('user.form_validation_error'));
				redirect('usercontrol/payment_details');
			}
			 
		}
		 else 
		{
			  
				$data=array();
				
				$data['payment_methods'] = $this->Withdrawal_payment_model->getPaymentMethods(); 
			
				$data['primary_payment_method'] = $userdetails['primary_payment_method'];

				 $data['paymentlist'] = $this->Product_model->getAllPayment($userdetails['id']);
		        if (isset($data['paymentlist'][0])) {
		            $data['paymentlist'] = array(
		                'payment_id'             => $data['paymentlist'][0]['payment_id'],
		                'payment_bank_name'      => $data['paymentlist'][0]['payment_bank_name'],
		                'payment_account_number' => $data['paymentlist'][0]['payment_account_number'],
		                'payment_account_name'   => $data['paymentlist'][0]['payment_account_name'],
		                'payment_ifsc_code'      => $data['paymentlist'][0]['payment_ifsc_code'],
		            );
		        } else {
		            $data['paymentlist'] = array(
		                'payment_id'             => 0,
		                'payment_bank_name'      => '',
		                'payment_account_number' => '',
		                'payment_account_name'   => '',
		                'payment_ifsc_code'      => '',
		            );
		        }
		        $data['paypalaccounts'] = $this->Product_model->getPaypalAccounts($userdetails['id']);
		        if (isset($data['paypalaccounts'][0])) {
		            $data['paypalaccounts'] = array(
		                'paypal_email' => $data['paypalaccounts'][0]['paypal_email'],
		                'id'           => $data['paypalaccounts'][0]['id'],
		            );
		        } else {
		            $data['paypalaccounts'] = array(
		                'paypal_email' => '',
		                'id'           => 0,
		            );
		        }

		        $data['paymentlist']['paypalaccounts'] = $data['paypalaccounts'];

				$this->view($data,'payment/payment_form','usercontrol');
		}			

	}
 			
	public function listreviews_ajax($page = 1)
		{
			$userdetails = $this->userdetails();
			if(empty($userdetails)){ redirect('/login'); }
			$get = $this->input->get(null,true);
			$post = $this->input->post(null,true);
			
			$page=isset($get['page']) ? $get['page'] : $page;
			$limit=25;
			 
			$product_id=null;
			if(isset($post['product_name_review']) && $post['product_name_review']){
				$product_id = (int)$this->input->post('product_name_review');
	 	} 
	 	
		
		$filter=array("product_created_by"=>$userdetails['id']);

		$data = $this->Product_model->getAllReviewFilter($product_id,$limit,$page,$filter);

		$data['user_id']=$userdetails['id'];

		$vendormanagereviewimage=$this->db->query("SELECT * FROM setting WHERE  setting_key='vendormanagereviewimage' and setting_type='market_vendor'")->row();
		$data['vendormanagereviewimage'] = isset($vendormanagereviewimage) && $vendormanagereviewimage->setting_value==1 ? 1 : 0;	

		$json['view'] = $this->load->view("usercontrol/store/review_list", $data, true);
		
		$this->load->library('pagination');

		$this->pagination->cur_page = $page;

		$config['base_url'] = base_url('usercontrol/listreviews_ajax');

		$config['per_page'] = $limit;

		$config['total_rows'] = $data['total'];

		$config['use_page_numbers'] = TRUE;

		$config['page_query_string'] = TRUE;

		$config['enable_query_strings'] = TRUE;

		$_GET['page'] = $page;

		$config['query_string_segment'] = 'page';

		$this->pagination->initialize($config);

		$json['pagination'] = $this->pagination->create_links();

		$json['total']=$data['total'];
 
		echo json_encode($json);
	}


	public function manage_review($id = null){

		$userdetails = $this->userdetails();
		if(empty($userdetails)){ redirect('/login'); }

		$vendormanagereview= $this->db->query("SELECT * FROM setting WHERE  setting_key='vendormanagereview' and setting_type='market_vendor'")->row();
	  	$managereview=isset($vendormanagereview) && $vendormanagereview->setting_value==1 ? 1 : 0;
	  	if($managereview==0)
	  		redirect('usercontrol/dashboard');

		$post = $this->input->post(null,true);
	
		if(!empty($post) && isset($post['product_name'])){
 			
			$this->load->helper(array('form', 'url'));
			$this->load->library('form_validation');

			$this->form_validation->set_rules('product_name', __('user.product_name'), 'required');
			$this->form_validation->set_rules('firstname', __('user.firstname'), 'required' );
			$this->form_validation->set_rules('lastname', __('user.lastname'), 'required' );
			$this->form_validation->set_rules(
				'review_description', __('user.review_description'),
				'required|min_length[5]|max_length[150]',
				array(
					'required'      => 'Enter %s',
					'is_unique'     => 'This %s already exists.',
					'min_length' 	=> '%s: the minimum of characters is %s',
					'max_length' 	=> '%s: the maximum of characters is %s',
				)
			);

			
			$this->form_validation->set_rules('rating',__('user.rating'), "required"); 
			$this->form_validation->set_rules('rating_created',__('user.review_date_-_time'), "required"); 
			

			 if ($this->form_validation->run() == FALSE) {

				$json['errors'] = $this->form_validation->error_array();
				
			} 
			else 
			{ 
				$post = $this->input->post(null,true);	
				$rating_id = (int)$this->input->post('rating_id',true);
				$product_id = (int)$this->input->post('product_name',true); 

				$review=array();		

				$errors = array();
				$clientphoto=$post['user_image_hidden'];
				 if(isset($_FILES['user_image']) && !empty($_FILES['user_image']['name'])){
						$upload_response = $this->upload_photo('user_image','assets/images/users/');
						if($upload_response['success']){
							$clientphoto= $upload_response['upload_data']['file_name'];
						}else{
							$errors['user_image'] = $upload_response['msg'];
						}
					}

				if(count($errors)==0)	 
				{
 					if($rating_id>0)
 					{
 					
 						$user_id= $this->db->get_where('rating',array('rating_id'=>$rating_id))->row_array()['rating_user_id'];
 						$this->db->where('id',$user_id)->update('users',array(
						'firstname' => $post['firstname'],
						'lastname'  => $post['lastname'],
						'avatar'  => $clientphoto  
						));  
							
						$review['products_id'] = $product_id;
						$review['rating_comments'] = $post['review_description'];
						$review['rating_number'] = $post['rating']; 
						$review['rating_status'] = 1; 
						$review['rating_updated_by'] = $userdetails['id'];
						$review['rating_created'] =  (isset($post['rating_created']) && $post['rating_created']) ? date("Y-m-d H:i:s",strtotime($post['rating_created'])) : null ;
						$review['rating_updated'] =  date("Y-m-d H:i:s");
						$review['rating_user_agent'] =  $this->agent->agent_string();
						$review['rating_os'] =  $this->agent->platform();
						$review['rating_browser'] =  $this->agent->browser();
						$review['rating_isp'] =  gethostbyaddr($_SERVER['REMOTE_ADDR']);
						$review['rating_ipaddress'] =  $_SERVER['REMOTE_ADDR'];

						$this->db->where('rating_id',$rating_id)->update('rating',$review);
						$this->Product_model->update_avg_rating($product_id);
						$this->session->set_flashdata('success', __('user.review_updated_successfully'));
						$json['location'] = base_url('usercontrol/store_products'); 
 					}
 					else
 					{
 						$data=$this->user->insert(array(

						'firstname' => $post['firstname'],
						'lastname'  => $post['lastname'],
						'avatar'  => $clientphoto, 
						'status'  => 1,
						'refid'     => 0,
						'type'      => 'client',
						));
						$insert_id = $this->db->insert_id(); 
							
						$review['products_id'] = $product_id;
						$review['rating_comments'] = $post['review_description'];
						$review['rating_number'] = $post['rating'];
						$review['rating_user_id'] = $insert_id; 
						$review['rating_status'] = 1; 
						$review['rating_created_by'] = $userdetails['id'];
						$review['rating_updated_by'] = $userdetails['id'];
						$review['rating_created'] =  (isset($post['rating_created']) && $post['rating_created']) ? date("Y-m-d H:i:s",strtotime($post['rating_created'])) : null ;
						$review['rating_updated'] =  date("Y-m-d H:i:s");
						$review['rating_user_agent'] =  $this->agent->agent_string();
						$review['rating_os'] =  $this->agent->platform();
						$review['rating_browser'] =  $this->agent->browser();
						$review['rating_isp'] =  gethostbyaddr($_SERVER['REMOTE_ADDR']);
						$review['rating_ipaddress'] =  $_SERVER['REMOTE_ADDR'];

						$this->Product_model->create_data('rating', $review);
						$this->Product_model->update_avg_rating($product_id);
						$this->session->set_flashdata('success', __('user.review_inserted_successfully'));
						$json['location'] = base_url('usercontrol/store_products'); 
 					}
					
		 		}
		 	 
				
			}
			echo json_encode($json);
			exit; 
		}	
		$data['review'] = $this->Product_model->getReviewById($id)[0]; 
		$filter['product_status_in'] =	 '1';
		$filter['vendor_id']=$userdetails['id'];
		$data['products'] = $this->Product_model-> getAllProduct($userdetails['id'],'user',$filter);
		$vendormanagereviewimage=$this->db->query("SELECT * FROM setting WHERE  setting_key='vendormanagereviewimage' and setting_type='market_vendor'")->row();
		$data['vendormanagereviewimage'] = isset($vendormanagereviewimage) && $vendormanagereviewimage->setting_value==1 ? 1 : 0;	
		$data['setting'] = $this->Product_model->getSettings('productsetting'); 
		
		if(isset($data['review']['rating_created_by']) && $data['review']['rating_created_by']!= $userdetails['id'])
		{
 			$this->session->set_flashdata('error', __('user.you_can_not_edit_other_user_review'));	
 			redirect('usercontrol/store_products');
		}
		else{

 			$this->view($data, 'store/add_review','usercontrol');
		}
	}

	public function deleteReview($id = null){
		$userdetails = $this->userdetails();
		if(empty($userdetails)){
			if(empty($userdetails)){ redirect('/login'); }
		}

		if($id!="" && $id>0)
		{
			$res=$this->Product_model->deleteReview($id);
			if(isset($res))
				$this->session->set_flashdata('success', __('user.review_has_been_deleted_successfully'));
			else
 				$this->session->set_flashdata('success', __('user.review_not_deleted'));
		} 
		redirect('usercontrol/store_products');
	}	

	public function exportReviewXML(){
		$userdetails = $this->userdetails();
		if(empty($userdetails)){ redirect('/login'); }

		$store_setting = $this->Product_model->getSettings('store');
		$json['structure_only'] = $structure_only = $this->input->post('structure_only');
		
		$datalist = [];
		if($structure_only == 1) {
			
		} else {
			$filter=array("product_created_by"=>$userdetails['id']);
			$data = $this->Product_model->getAllReviewFilter($product_id,$limit,$page,$filter);
			if(isset($data['reviews']))
			$datalist =$data['reviews'];
		}
 
		$header = array(
			'rating_id' => 'Review ID [need to be empty for new]',
			'products_id' => 'Product ID [available in products export]',
			'firstname' => 'First Name',
			'lastname' => 'Last Name',
			'rating_number' => 'Rating Number [1 to 5]',
			'rating_comments' => 'Review Description',
			'rating_created' => 'Review Date and Time [ex. 2022-11-25 22:40:40]',
		); 

		$dom = new DOMDocument();
		$dom->encoding = 'utf-8';
		$dom->xmlVersion = '1.0';
		$dom->formatOutput = true;
		$root = $dom->createElement('reviews');
		
		if($structure_only == 1) 
		{
			$product_node = $dom->createElement('review');
			foreach ($header as $name_key => $_value) 
			{
				if($name_key!='product_short_description' && $name_key!='product_description' )
				{
					$child_node_title = $dom->createElement($name_key, $_value);
					$product_node->appendChild($child_node_title);
				}
				else
				{
					$child_node_title = $dom->createElement($name_key);
					$cdataname     = $dom->createCDATASection($_value);
					$child_node_title->appendChild($cdataname);
					$product_node->appendChild($child_node_title);
					 
				}

			}
			$root->appendChild($product_node);
			$dom->appendChild($root);
			
			$dom->save(FCPATH.'assets/xml/export_vendor_product_reviews_structure.xml');
			$json['download'] = base_url('assets/xml/export_vendor_product_reviews_structure.xml'); 
		}
		else
		{

			$index = 0;
			$_exportData = array();
			$_exportData[$index] = array_values($header);
			foreach ($datalist as $key => $value) 
			{
				$xml_node = $dom->createElement('review');
				$index++;
				foreach ($header as $name_key => $_value) 
				{
					$val = '';

					if(isset($value[$name_key])){

						switch ($name_key) {
							case 'product_tags':
							$t = ( is_array(json_decode($value[$name_key], true)) ? json_decode($value[$name_key], true): [] );
							$val = implode(",", $t);
							break;
							default:
							$val = $value[$name_key];
							break;
						}
					} 

					if($name_key!='product_short_description' && $name_key!='product_description' )
					{
						 $child_node_title = $dom->createElement($name_key, $val);
						 $xml_node->appendChild($child_node_title);
					}
					else
					{
						
						$child_node_title = $dom->createElement($name_key);
						$cdataname     = $dom->createCDATASection($val);
						$child_node_title->appendChild($cdataname);
						$xml_node->appendChild($child_node_title);
						 
					}
	  			}

	  			$root->appendChild($xml_node);
			}

			$dom->appendChild($root);
			$dom->save(FCPATH.'assets/xml/export_vendor_product_reviews.xml');
			$json['download'] = base_url('assets/xml/export_vendor_product_reviews.xml');	 
		}
	  
		echo json_encode($json);
	 	exit;
	}

	public function bulkReviewsImport() {
		$userdetails = $this->userdetails();
		if(empty($userdetails)){ redirect('/login'); }
 
		$extension="";
		if(!isset($_FILES['file']['error']) || $_FILES['file']['error'] != 0)
		{
			$json['warning'] = __('user.please_select_xml_file');

		} else {

			$extension = pathinfo($_FILES['file']["name"], PATHINFO_EXTENSION);

			if($extension == 'xml')
			{}
			else
			{
				$json['warning'] = __('user.only_xml_file_are_allowed');
			}
		}
	 
		$f_result = [
			'products_available' => 0,
			'products_managed' => 0,
			'status' => 'danger',
			'message' => __('user.something_went_wrong_please_try_again!'),
			'data'  => [],
			'dataPreview' => ""
		];
		
		$bulkResult = [];

		if(!isset($json['warning'])){

			$inputFileName = $_FILES['file']['tmp_name'];

			if($extension == 'xml')
			{
				$xml = simplexml_load_file($inputFileName);
				if ($xml === false) 
				{
					$xmlerrrostring="";
					  $json['warning'] = __('user.failed_loading_xml');
				  foreach(libxml_get_errors() as $error) 
				  {
				    $xmlerrrostring.= "<br>". $error->message;
				  }

				  $json['warning'] =$xmlerrrostring;
				} 
				else 
				{
					$reviews=$xml;

					foreach($reviews as $review) 
					{
						$reviewArray = [];
						foreach($review as $key => $value) 
						{
					  		$xmlobjvalue= (string)$value[0];
					  		if(isset($xmlobjvalue)) 
					  		{
								$reviewArray[$key] = $xmlobjvalue != null ? $xmlobjvalue : '';
							} else {
								$reviewArray[$key] = '';
							} 
					  	} 

					  	if(!empty($reviewArray)) 
					  	{
							$cdata = $this->initialReviewImportCheck($reviewArray);
							if(isset($cdata) && is_array($cdata))
							$reviewArray['rating_status']=$cdata['data']['rating_status'];

							$cdata['row'] = $reviewArray;
							$bulkResult[] = $cdata;
						} 
					}
				}
			}

		} 
	 
		
		$data['action'] = 'confirm';
		$data['reviews'] = $bulkResult;
		echo $this->load->view('usercontrol/store/bulk_review_upload_modal', $data, true); 
	}

	public function bulkReviewImportFromUrl() 
	{
		$userdetails = $this->userdetails();
		if(empty($userdetails)){ redirect('/login'); }

			$f_result = [
			'reviews_available' => 0,
			'reviews_managed' => 0,
			'status' => 'danger',
			'message' => __('user.something_went_wrong_please_try_again'),
			'data'  => [],
			'dataPreview' => ""
		];
		
		$bulkResult = [];
		$json=array();
		$post = $this->input->post(null,true); 
		if(!isset($post['txt_review_xmlurl'])){
 
			$json['warning'] = __('user.please_enter_xml_url'); 

		} 
		else {
 
			$xmlurl = $post['txt_review_xmlurl'];
			$featchurldata=file_get_contents($xmlurl);
			$xml=simplexml_load_string($featchurldata);

			if($xml)
			{
			 	$reviews=$xml;
	 			if(isset($reviews))
	 			{
	 				foreach($reviews as $review) 
					{
						$reviewArray = [];
						foreach($review as $key => $value) 
						{
							$xmlobjvalue= (string)$value[0];
							if(isset($xmlobjvalue)) 
							{
								$reviewArray[$key] = $xmlobjvalue != null ? $xmlobjvalue : '';
							} 
							else {
								$reviewArray[$key] = '';
							} 
						} 

						if(!empty($reviewArray)) {
							$cdata = $this->initialReviewImportCheck($reviewArray);
							if(isset($cdata) && is_array($cdata))
							$reviewArray['rating_status']=$cdata['data']['rating_status'];

							$cdata['row'] = $reviewArray;
							$bulkResult[] = $cdata;
						} 
					}
	 			}
	 			else
	 				$json['warning'] = __('user.not_valid_xm_format'); 
						
			}
			else 
			{  
				$json['warning'] = __('user.url_entered_not_valid_xml_content');
			}

		}


		$data['action'] = 'confirm';
		$data['reviews'] = $bulkResult;
		echo $this->load->view('usercontrol/store/bulk_review_upload_modal', $data, true); 
	}

	public function downloadproductreviewxmlstructurefile($filename = NULL) {
	   $userdetails = $this->userdetails();
		if(empty($userdetails)){ redirect('/login'); }
	    $this->load->helper('download');
	    $data = file_get_contents(FCPATH .'assets/xml/export_vendor_product_reviews_structure.xml');
	    force_download("export_vendor_product_reviews_structure.xml", $data);

	}

	public function downloadproductreviewxmlfile($filename = NULL) {
	    $userdetails = $this->userdetails();
		if(empty($userdetails)){ redirect('/login'); }
	    $this->load->helper('download');
	    $data = file_get_contents(FCPATH .'assets/xml/export_vendor_product_reviews.xml');
	    force_download("export_vendor_product_reviews.xml", $data);
	}

	function checkDateTime($date)
	{
		 $format = 'Y-m-d H:i:s';
		 $d = DateTime::createFromFormat($format, $date);
 		if($d && $d->format($format) == $date)
	    	return true; 
	    else
	    { 
	    	$this->form_validation->set_message('checkDateTime', __('user.invalid_date_format'));
	    	return false;
	    }
         
	}

	public function initialReviewImportCheck($post){

	try {
			$userdetails = $this->userdetails();
			if(empty($userdetails)){ redirect('/login'); }
			if(!empty($post))
			{
				unset($this->validation);
				$rating_id = (int) $post['rating_id'];
				$product_id = (int) $post['products_id'];
				$rating_status=0;		
				if($rating_id > 0) 
				{
					$rating_exist = $this->db->query('select rating_created_by,products_id from rating where rating_id='.$rating_id)->row_array();
					if(empty($rating_exist))
					 {
					 	return [
							"status" => "error",
							"message" => __('user.review_not_available_having_rating_id_you_provided')
						];
							 	
					}
					else
					{ 	
						if($rating_exist["rating_created_by"]==$userdetails['id'])
							$rating_status=1;
						else
						return [
							"status" => "error",
							"message" => __('user.review_reated_by_other_can_not_be_change')
						];
					}
				}
				else
				{
					$product_owner = $this->db->query('SELECT product_created_by FROM `product` where `product_id`='.$product_id)->row_array(); 

					if(empty($product_owner)) 
			 		{
			 			return [
							"status" => "error",
							"message" => __('user.product_not_available_having_product_id_you_provided')
						];

			 		}
			 		else if ($product_owner["product_created_by"]!=$userdetails['id'])
			 			return [
							"status" => "error",
							"message" => __('user.review_can_not_be_add_on_product_created_by_other')
						];
					else  
						$rating_status=1;
				}
		 
	 			$this->load->helper(array('form', 'url'));
				$this->load->library('form_validation');
				$this->form_validation->reset_validation();

				$this->form_validation->set_rules('products_id', __('user.product_id'), 'required');
				$this->form_validation->set_rules('firstname', __('user.firstname'), 'required' );
				$this->form_validation->set_rules('lastname', __('user.lastname'), 'required' );
				$this->form_validation->set_rules(
					'rating_comments', __('user.review_description'),
					'required|min_length[5]|max_length[150]',
					array(
						'required'      => 'Enter %s',
						'is_unique'     => 'This %s already exists.',
						'min_length' 	=> '%s: the minimum of characters is %s',
						'max_length' 	=> '%s: the maximum of characters is %s',
					)
				);
				
				$this->form_validation->set_rules('rating_number',__('user.rating'), "required"); 
				
				$this->form_validation->set_rules('rating_created',__('user.review_date_-_time'),  'required|callback_checkDateTime'); 
			
				$this->form_validation->set_data($post);
				 if ($this->form_validation->run() == FALSE) {

					return [
							"status" => "error",
							"errors" => $this->form_validation->error_array()
						];
					
				}
				else 
				{
						
						$errors = array();
						
						  	$review=array();
							$review['rating_id'] = $post['rating_id']; 
							$review['products_id'] = $post['products_id'];  
							$review['rating_comments'] = $post['rating_comments'];
							$review['rating_number'] = $post['rating_number']; 
							$review['rating_status'] = 1; 
							$review['rating_created_by'] = $userdetails['id'];
							$review['rating_updated_by'] = $userdetails['id'];
							$review['rating_created'] =  (isset($post['rating_created']) && $post['rating_created']) ? date("Y-m-d H:i:s",strtotime($post['rating_created'])) : null ;
							$review['rating_updated'] =  date("Y-m-d H:i:s");
							$review['rating_user_agent'] =  $this->agent->agent_string();
							$review['rating_os'] =  $this->agent->platform();
							$review['rating_browser'] =  $this->agent->browser();
							$review['rating_isp'] =  gethostbyaddr($_SERVER['REMOTE_ADDR']);
							$review['rating_ipaddress'] =  $_SERVER['REMOTE_ADDR'];

							$review['firstname'] =  $post['firstname']; 
							$review['lastname'] =  $post['lastname'];  
					 
							
							if(isset($post['rating_id']) && !empty($post['rating_id']) && $post['rating_id'] != 0){
								
								return [
									"status" => "Warning",
									"message" => "<span class='badge bg-warning'>update</span>",
									"data" => $review
								];

							} else {
								return [
									"status" => "Warning",
									"message" => "<span class='badge bg-success'>create</span>",
									"data" => $review
								];
							}

					 
				} 
			}
			else
			{
				return [
					"status" => "error",
					"errors" => ["Something went wrong"]
				];
			}

		} catch (Exception $e) {
			return [
				"status" => "error",
				"errors" => [$e->getMessage()]
			];
		}
	}

	public function bulkReviewImportConfirm() 
	{
		$userdetails = $this->userdetails();
		if(empty($userdetails)){ redirect('/login'); }

		$data = json_decode(base64_decode($_POST['reviews']), true);

		$result = [
			'total_products' => 0,
			'created_products' => 0,
			'updated_products' => 0,
			'failed_products' => 0,
			'skipped_products' => 0,
			'details' => []
		];

		foreach($data as $d) {
			
			if($d['status'] !== 'error') {
				$r = $this->createUpdateImportedReview($d['data']);
				if(isset($r['created'])) {
					$result['created_products']++;
				} else if(isset($r['updated'])) {
					$result['updated_products']++;
				} else {
					$result['failed_products']++;
				}
				
				$result['details'][] = [
					'product' => $d['data'],
					'result' => $r
				];
			} else {
				$result['skipped_products']++;
			}
			$result['total_products']++;
		}
		
		echo $this->load->view('usercontrol/store/bulk_review_upload_modal', $result, true);
	}

	public function createUpdateImportedReview($post)
	{
		$userdetails = $this->userdetails();
		if(empty($userdetails)){ redirect('/login'); }
		try {
		
			$json['status'] = false;
			
			$userdetails = $this->userdetails();

			$old_product_data =[];
			
			$details = $post;

			if(isset($post['rating_id']) && !empty($post['rating_id']) && $post['rating_id'] != 0){
				$rating_id = $post['rating_id'];

				unset($details['rating_id']);

				$user_id= $this->db->get_where('rating',array('rating_id'=>$rating_id))->row_array()['rating_user_id'];
					$this->db->where('id',$user_id)->update('users',array(
				'firstname' => $post['firstname'],
				'lastname'  => $post['lastname'] 
				));  

				unset($details['firstname']);
				unset($details['lastname']);	
				$this->Product_model->update_data('rating', $details, array('rating_id' => $rating_id));

				$details['product_created_date'] = date('Y-m-d H:i:s');
				
				$json['updated'] = true;
				$json['status'] = true;
				$json['success'] = __('user.review_updated_successfully');

			} else {
	 
				$data=$this->user->insert(array(
				'firstname' => $post['firstname'],
				'lastname'  => $post['lastname'], 
				'status'  => 1,
				'refid'     => 0,
				'type'      => 'client',
				));
				$insert_id = $this->db->insert_id(); 

				$details['rating_user_id']=$insert_id;

				unset($details['firstname']);
				unset($details['lastname']);	

				$rating_id = $this->Product_model->create_data('rating', $details);
				$json['created'] = true;
				$json['status'] = true;
				$json['success'] = __('user.review_added_successfully');
			}
	 
			
		} catch (Exception $e) {
			$json['status'] = false;
			$json['errors'] = $e->getMessage();
		}
		
		return $json;
		die;
	}


	public function tutorial($id)
	{
		$userdetails = $this->userdetails();
		if(empty($userdetails)){ redirect('/login'); }

		$tutorial=$this->Tutorial_model->viewTutorial($userdetails,$id); 
		$data['title'] = $tutorial['title'];
		$this->load->view('usercontrol/includes/header', $data);
		$this->load->view('tutorial/display-tutorial', $tutorial);
		$this->load->view('usercontrol/includes/footer', $data); 
	}

	public function contactus($id)
	{
		$userdetails = $this->userdetails();
		if(empty($userdetails)){ redirect('/login'); }

		$data  = array();
		$where = array('notification_type'=>'contact_us','notification_id'=>$id);
		$data['notification_details'] = $this->Common_model->select_where_result('notification', $where);
		
		$data['title'] = _('user.contact_us');
		$this->load->view('usercontrol/includes/header', $data);
		$this->load->view('usercontrol/conatctus/conatctus_details', $data);
		$this->load->view('usercontrol/includes/footer', $data);
	}


	public function orders_notifications($id=null)
	{
		$userdetails = $this->userdetails();
		if(empty($userdetails)){ redirect('/login'); }

		if(isset($id) && $id>0)
		{
			$data  = array();
			$where = array('notification_view_user_id'=>$userdetails['id'],'notification_type'=>'integration_orders','notification_id'=>$id);
			 
			$notification = $this->Common_model->select_where_result('notification', $where);
			if(isset($notification) && is_array($notification) && count($notification)>0)
			{
				$order_id= $notification['notification_actionID'];
				$data['order']= $this->Order_model->getOrderDetails($order_id);
				$data['notification_title'] =$notification['notification_title'];
				$data['notification_details'] =$notification['notification_description'];

				$this->load->view('usercontrol/includes/header', $data);
				$this->load->view('usercontrol/notifications/ex_order_details', $data);
				$this->load->view('usercontrol/includes/footer', $data);

			}
			else
				redirect('/usercontrol/notification');
		}
		else
			redirect('/usercontrol/notification');
	}

	
	public function click_notification($id=null)
	{
		$userdetails = $this->userdetails();
		if(empty($userdetails)){ redirect('/login'); }

		if(isset($id) && $id>0)
		{
			$data  = array();
			$where = array('notification_view_user_id'=>$userdetails['id'],'notification_type'=>'integration_click','notification_id'=>$id);
			 
			$notification = $this->Common_model->select_where_result('notification', $where);
			if(isset($notification) && is_array($notification) && count($notification)>0)
			{
				$click_id= $notification['notification_actionID'];
				$data['order']= $this->Order_model->getClickActionDetails($click_id);
				$data['notification_title'] =$notification['notification_title'];
				$data['notification_details'] =$notification['notification_description'];

				$this->load->view('usercontrol/includes/header', $data);
				
				if($data['order']['click_type']=='action') 
					$this->load->view('usercontrol/notifications/ex_action_details', $data);	
				else
					$this->load->view('usercontrol/notifications/ex_click_details', $data);
				$this->load->view('usercontrol/includes/footer', $data);

			}
			else
				redirect('/usercontrol/notification');
		}
		else
			redirect('/usercontrol/notification');
	}
}