<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . 'hooks/Affiliate_Hook.php';

class Integration extends MY_Controller {
	
	function __construct() {
		parent::__construct();
		$this->load->model('user_model', 'user');
		$this->load->model('Product_model');
		$this->load->model('Report_model');
		$this->load->model('IntegrationModel');
		$this->load->model('Tutorial_model');
		___construct(1);

		$this->checkSessionTimeout();
	}

	public function userdetails(){ return $this->session->userdata('administrator'); }
	public function userlogins(){ return $this->session->userdata('user'); }

	public function script() {
        $this->integration('script');
    }
    
    public function show_affiliate_id() {
        $data = $this->input->get(null);
        $data['script'] = "general_integration";
        header('Content-Type: application/javascript');
    
        $data['aff_external_cookies_duration'] = $this->get_aff_external_cookies_duration();
        $this->load->view('integration/show_id', $data);
    }

    public function general_integration() {
        $data['script'] = "general_integration";
        header('Content-Type: application/javascript');
    
        $aff_external_cookies_duration = $this->get_aff_external_cookies_duration();
    
        echo "var aff_external_cookies_duration = " . $aff_external_cookies_duration . ";\n";
    
        $data['aff_external_cookies_duration'] = $aff_external_cookies_duration;
        $this->load->view('integration/general_integration', $data);
    }
    
    public function shopify() {
        $this->integration('shopify');
    }
    
    public function xcart() {
        $this->integration('xcart');
    }
    
    public function zencart() {
        $this->integration('zencart');
    }
    
    public function paypal() {
        $this->integration('paypal');
    }
    
    public function bigcommerce() {
        $this->integration('bigcommerce');
    }
    
    public function oscommerce() {
        $this->integration('oscommerce');
    }
    
    private function integration($script) {
        $data['script'] = $script;
        header('Content-Type: application/javascript');
    
        $data['aff_external_cookies_duration'] = $this->get_aff_external_cookies_duration();
        $this->load->view('integration/general_integration', $data);
    }
    
    private function get_aff_external_cookies_duration() {
        if (isset($_SESSION['aff_external_cookies_duration'])) {
            return $_SESSION['aff_external_cookies_duration'];
        } else {
            try {
                return $this->IntegrationModel->getDefaultCookiesDuration();
            } catch (Exception $e) {
                // Log the error and set a default value
                // log_message('Error fetching default cookies duration: ' . $e->getMessage());
                return 30;
            }
        }
    }

public function addClick(){

		//reject the campaign click commission if MarketTools is disable
	    $market_tools_status = $this->Product_model->getSettings('market_tools', 'status');
	    
	    if ($market_tools_status === null || !isset($market_tools_status['status'])) {
	        $market_tools_status['status'] = 1;
	    }
	    
	    if (!$market_tools_status['status']) {
	        echo '<script>console.log("MarketTools is disabled. Order aborted.");</script>';
	        return;
	    }
	    //reject the campaign click commission if MarketTools is disable
		
		$content = file_get_contents("php://input");
		if($content){
			parse_str($content, $data);
		}else{
			$data = $this->input->get(null);
		}

		if(isset($data['af_id'])) {

			list($user_id,$ads_id) = explode("-", _encrypt_decrypt(parse_affiliate_id($data['af_id']),'decrypt'));

			$Affiliate_Hook = new Affiliate_Hook;

			$is_suspicious_click = $Affiliate_Hook->is_suspicious_click($user_id);

			if($is_suspicious_click) {
				die('suspicious click');
			}
		}


		$restricted_vendors= $this->get_restricted_vendors();
		/* Marketing tools : Other Affiliates Selling My Products? */

			if($user_id>0)
			{
				$userrefidarray = $this->db->query('SELECT refid FROM users WHERE id='.$user_id)->row_array();

				if(is_array($userrefidarray))
					$userrefidcommon=$userrefidarray["refid"];
				else
					$userrefidcommon=0;	

				$escapevendorscommon = $this->db->query('SELECT user_id,vendor_shares_sales_status FROM vendor_setting WHERE vendor_shares_sales_status>0')->result_array();
	  		 
	  			$vendors = $this->db->query('SELECT id from users where is_vendor = 1')->result_array();

				$allowVendorscommon = [];
				foreach($escapevendorscommon as $esc) 
				{
					if($esc['vendor_shares_sales_status']==1)
						$allowVendorscommon[] = $esc['user_id'];
					else if($esc['vendor_shares_sales_status']==2 && $esc['user_id']==$userrefidcommon)
						$allowVendorscommon[] = $esc['user_id'];

				}
	 
				$escapeUserscommon = [];
				foreach($vendors as $v) {
					if(!in_array($v['id'], $allowVendorscommon))
						$escapeUserscommon[] = $v['id'];
				} 

		 		$restricted_vendors=array_unique(array_merge($restricted_vendors, $escapeUserscommon));
			}
			 
		/* Marketing tools : Other Affiliates Selling My Products? */

		$data['restricted_vendors'] = $restricted_vendors;

		$this->IntegrationModel->addClick($data);
	}


	public function addOrder()
	{

	//reject the campaign order commission if MarketTools is disable
    $market_tools_status = $this->Product_model->getSettings('market_tools', 'status');
    
    if ($market_tools_status === null || !isset($market_tools_status['status'])) {
        $market_tools_status['status'] = 1;
    }
    
    if (!$market_tools_status['status']) {
        echo '<script>console.log("MarketTools is disabled. Order aborted.");</script>';
        return;
    }
    //reject the campaign order commission if MarketTools is disable


		$content = file_get_contents("php://input");

		if($content){
			parse_str($content, $data);
		}else{
			$data = $this->input->get(null, true);
		}

		$restricted_vendors= $this->get_restricted_vendors();

		/* Marketing tools : Other Affiliates Selling My Products? */

			if(isset($data['af_id']))
			{
				list($user_id,$ads_id) = explode("-", _encrypt_decrypt(parse_affiliate_id($data['af_id']),'decrypt'));

				$userrefidarray = $this->db->query('SELECT refid FROM users WHERE id='.$user_id)->row_array();

				if(is_array($userrefidarray))
					$userrefidcommon=$userrefidarray["refid"];
				else
					$userrefidcommon=0;	

				$vendors = $this->db->query('SELECT id from users where is_vendor = 1')->result_array();

				$escapevendorscommon = $this->db->query('SELECT user_id,vendor_shares_sales_status FROM vendor_setting WHERE vendor_shares_sales_status>0')->result_array();
	  		 
	  
				$allowVendorscommon = [];
				foreach($escapevendorscommon as $esc) 
				{
					if($esc['vendor_shares_sales_status']==1)
						$allowVendorscommon[] = $esc['user_id'];
					else if($esc['vendor_shares_sales_status']==2 && $esc['user_id']==$userrefidcommon)
						$allowVendorscommon[] = $esc['user_id'];

				}
	 
				$escapeUserscommon = [];
				foreach($vendors as $v) {
					if(!in_array($v['id'], $allowVendorscommon))
						$escapeUserscommon[] = $v['id'];
				} 

		 		$restricted_vendors=array_unique(array_merge($restricted_vendors, $escapeUserscommon));
			}
			 
		/* Marketing tools : Other Affiliates Selling My Products? */
  
		$data['restricted_vendors'] = $restricted_vendors;

		$this->IntegrationModel->addOrder($data);
	}

	public function stopRecurring(){
		$content = file_get_contents("php://input");
		if($content){
			parse_str($content, $data);
		}else{
			$data = $this->input->get(null);
		}
		$this->IntegrationModel->stopRecurring($data);
	}

	public function addUser(){
		$content = file_get_contents("php://input");
		if($content){
			parse_str($content, $data);
		}else{
			$data = $this->input->get(null);
		}

		list($firstname, $lastname) = explode(" ", $data['display_name']);
		$username = $data['user_login'];
		$password = rand(11111111,99999999);

		$geo = $this->ip_info();
		
		$_data = array(
			'firstname'                 => $firstname,
			'lastname'                  => $lastname ? $lastname : $firstname,
			'email'                     => $data['user_email'],
			'username'                  => $username,
			'password'                  => sha1($password),
			'refid'                     => 0,
			'type'                      => 'user',
			'Country'                   => $geo['id'],
			'City'                      => (string)$geo['city'],
			'phone'                     => $geo['city'],
			'twaddress'                 => '',
			'address1'                  => '',
			'address2'                  => '',
			'ucity'                     => $geo['city'],
			'ucountry'                  => $geo['id'],
			'state'                     => $geo['state'],
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
			'PhoneNumber'               => '',
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
			'status'                    => '1',
			'value'                     => json_encode(array()),
		);

		$json = array();

		$checkEmail = $this->db->query("SELECT id FROM users WHERE email like ". $this->db->escape($_data['email']))->num_rows();
		if($checkEmail > 0){ $json['error'][] = "Email Already Exist"; }

		$checkUsername = $this->db->query("SELECT id FROM users WHERE username like ". $this->db->escape($_data['username']))->num_rows();
		if($checkUsername > 0){ $json['error'][] = "Username Already Exist"; }

		if(!isset($json['error'])){
			$this->user->insert($_data);

			$_data['password'] = $password;
			$this->load->model('Product_model');
			$this->load->model('Mail_model');

			$this->Mail_model->send_register_integration_mail($_data,__('user.welcome_to_new_user_registration'));

			$notificationData = array(
				'notification_url'          => '/userslist/',
				'notification_type'         =>  'user',
				'notification_title'        =>  __('user.new_user_registration'),
				'notification_viewfor'      =>  'admin',
				'notification_actionID'     =>  0,
				'notification_description'  =>  $_data['firstname'].' '.$_data['lastname'].' register as a  on affiliate Program on '.date('Y-m-d H:i:s'),
				'notification_is_read'      =>  '0',
				'notification_created_date' =>  date('Y-m-d H:i:s'),
				'notification_ipaddress'    =>  $_SERVER['REMOTE_ADDR']
			);

			$this->Product_model->create_data('notification', $notificationData);
		} else {
			echo "<pre>"; print_r($json); echo "</pre>";die; 
		}

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
	                    //$output = @$ipdat->geoplugin_countryName;
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

	public function addOrderPaypal(){
		$post = $this->input->post(null,true);
		$paypalData = json_decode($post['post'] , 1);

		if($paypalData && isset($paypalData['payment_status'])){
			switch ($paypalData['payment_status']) {
				case 'Completed':
				case 'Pending':
				$this->IntegrationModel->addOrder(array(
					'script_name'    => 'paypal',
					'order_currency' => $paypalData['mc_currency'],
					'order_total'    => $paypalData['auth_amount'],
					'af_id'          => $post['af_id'],
					'order_id'       => $post['order_id'],
					'base_url'       => $post['base_url'],
					'product_ids'    => $post['product_ids'],
				));

				break;
				default:
				echo $paypalData['payment_status'];
				break;
			}
		}
		
	}

	public function programs(){
		if(!$this->userdetails()){ redirect('admincontrol/dashboard', 'refresh'); }
		
		$filter = array();

		$name = isset($_GET['name']) ? $_GET['name'] : '';
		$is_admin = isset($_GET['is_admin']) ? $_GET['is_admin'] : '';
		$status = isset($_GET['status']) ? $_GET['status'] : '';
		
		if($name != '')
		{
			$filter['name'] = $name;
		}
		if($is_admin != '')
		{
			$filter['is_admin'] = $is_admin;
		}
		if($status != '')
		{
			$filter['status'] = $status;
		}

		$data['programs'] = $this->IntegrationModel->getPrograms($filter);
		$this->view($data,'integration/programs');
	}


	public function search_programs()
	{
		$prgoname=$this->input->post('progname');
		$filter = array();
		if(!empty($prgoname))
		{
			$filter['name'] = $prgoname;
		}
		$data['programs'] = $this->IntegrationModel->getPrograms($filter);

		$row=$this->load->view('admincontrol/integration/search_programs_row', $data,true);

		echo json_encode($row);
	}

	public function programs_form($program_id = 0){
		if(!$this->userdetails()){ redirect('admincontrol/dashboard', 'refresh'); }
		$data = array();
		if($program_id){
			$data['programs'] = $this->IntegrationModel->getProgramByID($program_id);
		}
		
		$this->Report_model->view('admincontrol/integration/programs_form', $data);
	}

	public function delete_programs_form(){

		if(!$this->userdetails()){ redirect('admincontrol/dashboard', 'refresh'); }
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
		if(!$this->userdetails()){ redirect('admincontrol/dashboard', 'refresh'); }
		$data = $this->input->post(null,true);
		$json=array();

		$program_id = (int)$data['program_id'];
		$programs = $this->IntegrationModel->getProgramByID($program_id);

		if(empty($program_id))
			$this->form_validation->set_rules('name', 'Name', 'required|trim');
		else 
			$this->form_validation->set_rules('program_id', 'Program', 'required|trim|integer');

		if($data['sale_status']){
			$this->form_validation->set_rules('commission_type', 'Name', 'required|trim');
			$this->form_validation->set_rules('commission_sale', 'Name', 'required|trim');
		}
		if($data['click_status']){
			$this->form_validation->set_rules('commission_number_of_click', 'Name', 'required|trim');
			$this->form_validation->set_rules('commission_click_commission', 'Name', 'required|trim');
		}

		if($programs['vendor_id']){
			if((float)$data['admin_commission_sale'] > 0 && $data['admin_commission_sale'] > $programs['commission_sale']){
				$custom_errors['admin_commission_sale'] = 'Must be less than vendor commission ('. c_format($programs['commission_sale']) .')';
			}

			if((float)$data['admin_commission_click_commission'] > 0 && $data['admin_commission_click_commission'] > $programs['commission_click_commission']){
				$custom_errors['admin_commission_click_commission'] = 'Must be less than vendor commission ('. c_format($programs['commission_click_commission']) .')';
			}

			if((float)$data['admin_commission_number_of_click'] > 0 && $data['admin_commission_number_of_click'] > $programs['commission_number_of_click']){
				$custom_errors['admin_commission_number_of_click'] = 'Must be less than vendor commission ('. (int)$programs['commission_number_of_click'] .')';
			}
		}
		
		if ($this->form_validation->run() == FALSE) {
			$json['errors'] = $this->form_validation->error_array();
			foreach ($custom_errors as $key => $value) { $json['errors'][$key] = $value; }
		} else {
			foreach ($custom_errors as $key => $value) { $json['errors'][$key] = $value; }

			if(count($json)==0 || count($json['errors']) == 0){
				$program_id = $this->IntegrationModel->editProgram($data,$program_id, 'admin');
				if($program_id){
					if(isset($data['add_program_to_form'])){
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
					} else {
						$this->session->set_flashdata('success',__('admin.program_saved_successfully'));
						$json['location'] = base_url('integration/programs');
					}
				} else{
					$this->session->set_flashdata('success',__('admin.something_wrong'));
				}
			}

		}

		echo json_encode($json);
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

		$data['views'] = '';
		
		$this->load->model('PagebuilderModel');

		$register_form = $this->PagebuilderModel->getSettings('registration_builder');

		$data['customField'] = json_decode($register_form['registration_builder'],1);

		$this->Report_model->view('admincontrol/integration/instructions', $data);
	}

	private function modules_list($requestingFor = null){

		if($requestingFor == null) {

			$integration_modules['general_integration'] = array(
				'name' => "Custom Order Integration",
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

	public function integration_tools($page= 1){
		if(!$this->userdetails()){ redirect('admincontrol/dashboard', 'refresh'); }
		$userdetails = $this->userdetails();

		if ($this->input->server('REQUEST_METHOD') == 'POST'){
			$market_vendor = $this->Product_model->getSettings('market_vendor');
			$post = $this->input->post(null,true);
			$get = $this->input->get(null,true);

			$filter = array(
				'page' => isset($get['page']) ? $get['page'] : $page,
				'limitdata' => 25,
				'userdetails' => $userdetails
			);

			if(isset($post['category_id']))
				$filter['category_id'] = $post['category_id'];

			if(isset($post['ads_name']))
				$filter['ads_name'] = $post['ads_name'];

			if(isset($post['vendor_id']) && $post['vendor_id'] == 'only_admins')
				$filter['show_only'] = 'admin';
			
			else if (isset($post['vendor_id']) && $post['vendor_id'] == 'only_vendors')
				$filter['show_only'] = 'vendor';
			
			if(isset($post['groups']) && !empty($post['groups']))
				$filter['groups'] = $post['groups'];

			if(isset($post['show_only']) && $post['show_only'] == 'true')
				$filter['show_only'] = 'admin';

			if (isset($post['status']))
				$filter['status'] = $post['status'];

			if ($market_vendor['marketvendorstatus'] == 1) {
				$filter['marketvendorstatus'] = 1;
				if (isset($post['vendor_id'])) {
					$filter['vendor_id'] = $post['vendor_id'];
				}
			 }else{
			 	$filter['marketvendorstatus'] = 0;
			 }


			$json = array();
			list($data['tools'],$total) = $this->IntegrationModel->getProgramTools($filter);
			
			$json['tools'] = $data['tools'];
			$data['integration_plugins'] = $this->modules_list();
			
			if($post['paginate']){
				$this->load->library('pagination');
				$this->pagination->cur_page = $filter['page'];
				$config['base_url'] = base_url('integration/integration_tools');
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
				$json = $this->load->view("admincontrol/integration/integration_tools_list", $data, true);
			}
			
			echo $json;
			exit;
		}

		$data['vendors'] = $this->db->query("SELECT users.id, CONCAT(users.firstname,' ',users.lastname) as name FROM `integration_tools` LEFT JOIN users ON users.id=vendor_id WHERE vendor_id > 0 && users.id > 0 GROUP by vendor_id")->result_array();

			$data['categories'] = $this->db->query("SELECT DISTINCT integration_category.id  as value ,integration_category.name as label, CASE WHEN integration_category.parent_id=0 THEN integration_category.id ELSE integration_category.parent_id END AS pid FROM `integration_category`
			 inner JOIN integration_tools on integration_tools.category=	 integration_category.id 
		 order by pid,integration_category.id")->result_array();

		$data['status'] = $this->db->query("SELECT id as value,name as label FROM integration_category ")->result_array();

		$groups = $this->db->query("SELECT id, group_name FROM user_groups")->result_array();

		$data['groups'] = [];
		foreach($groups as $g) {
			$data['groups'][$g['id']] = $g['group_name'];
		}

		$this->load->library("socialshare");				
		$data['social_share_modal'] =  $this->socialshare->get_dynamic_social_share_btns();

		$this->view($data,'integration/integration_tools');
	}

	public function getIntegrationMlmInfo(){
		if(!$this->userdetails()){ redirect('admincontrol/dashboard', 'refresh'); }

		$html = '';

		$tool = $this->IntegrationModel->getProgramToolsByID((int)$this->input->post('id',true));
		if($tool){
			$data['tool']['commission_type'] = $tool['commission_type'];
			$data['tool']['vendor_id'] = $tool['vendor_id'];
			$data['CurrencySymbol'] = $this->currency->getSymbol();
			if($tool['commission_type'] == 'custom'){
				if($tool['vendor_id'])
					$setting = $this->Product_model->getVendorSettings($tool['vendor_id'], 'referlevel');
				else 
					$setting = $this->Product_model->getSettings('referlevel');
				
				$data['tool']['referlevel'] = $tool['commission']['referlevel'];
				$data['tool']['referlevel']['levels'] = ($tool['commission']['referlevel']['levels']) ? $tool['commission']['referlevel']['levels'] : (isset($setting['levels']) ? (int)$setting['levels'] : 3);

				for ($i=1; $i <= $data['tool']['referlevel']['levels']; $i++) { 
					$data['tool']['referlevel_'. $i] = $tool['commission']['referlevel_'. $i];
				}
			} else {
				$commonSetting = array('referlevel','referlevel_1','referlevel_2','referlevel_3','referlevel_4','referlevel_5','referlevel_6','referlevel_7','referlevel_8','referlevel_9','referlevel_10','referlevel_11','referlevel_12','referlevel_13','referlevel_14','referlevel_15','referlevel_16','referlevel_17','referlevel_18','referlevel_19','referlevel_20','referlevel');

				foreach($commonSetting as $key => $value){
					if($tool['vendor_id'])
						$data['tool'][$value] 	= $this->Product_model->getVendorSettings($tool['vendor_id'], $value);
					else
						$data['tool'][$value] 	= $this->Product_model->getSettings($value);
				}
			}

			$html = $this->load->view('admincontrol/integration/integration_mlm_info',$data,true);
		}

		echo $html;
		die;
	}

	public function integration_code_modal(){
		if(!$this->userdetails()){ redirect('admincontrol/dashboard', 'refresh'); }

		$data['action_code'] = 'action_code';
		$data['general_code'] = 'general_code';

		$tools = $this->IntegrationModel->getProgramToolsByID((int)$this->input->post('id',true));

		if($tools){
			$data['program_id'] = $tools['program_id'];
			$data['name'] = $tools['name'];
			$data['target_link'] = $tools['target_link'];
			$data['tool_type'] = $tools['tool_type'];
			if($tools['tool_type'] == 'action' || $tools['tool_type'] == 'single_action'){
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
		$data['tool'] = $tools;
		$data['integration_plugins'] = $this->modules_list();
		echo $this->load->view('admincontrol/integration/integration_code_modal', $data, true);
		exit;
	}

	public function integration_terms_modal(){
		if(!$this->userdetails()){ redirect('admincontrol/dashboard', 'refresh'); }

		$data['terms_data'] = $this->IntegrationModel->getTermsToolsByID((int)$this->input->post('id',true));
		
		$json['html'] = $this->load->view('admincontrol/integration/integration_terms_modal', $data, true);

		echo json_encode($json);die;
	}

	public function integration_tools_duplicate($tools_id){
		$this->IntegrationModel->duplicate_tools($tools_id);
		$this->session->set_flashdata('success', __('admin.add_duplicate_campaign_successfully'));
		redirect(base_url('integration/integration_tools'));
		exit;
	}

	public function integration_tools_form($type="banner", $tools_id = 0){
		if(!$this->userdetails()){ redirect('admincontrol/dashboard', 'refresh'); }

		
		$setting = $this->Product_model->getSettings('referlevel');
		$data['levels'] = isset($setting['levels']) ? (int)$setting['levels'] : 3;

		$program_filter = [];
		if($tools_id){

			$data['tool'] = $this->IntegrationModel->getProgramToolsByID($tools_id);

			$program_filter['vendor_id'] = (int)$data['tool']['vendor_id'];

			$category_ids = explode(",", $data['tool']['category']);
			if(count(array_filter($category_ids)) > 0){
				$data['categories'] = $this->db->query("SELECT id as value,name as label FROM integration_category WHERE id IN (". implode(",", $category_ids) .") ")->result_array();
			}

			$data['referlevel'] = $data['tool']['commission']['referlevel'];

			$data['levels'] = ($data['tool']['commission']['referlevel']['levels']) ? $data['tool']['commission']['referlevel']['levels'] : $data['levels'];
			for ($i=1; $i <= $data['levels']; $i++) { 
				$data['referlevel_'. $i] = $data['tool']['commission']['referlevel_'. $i];
			}
		} else {
			$program_filter['vendor_id'] = 0;
		}

		$commonSetting = array('referlevel','referlevel_1','referlevel_2','referlevel_3','referlevel_4','referlevel_5','referlevel_6','referlevel_7','referlevel_8','referlevel_9','referlevel_10','referlevel_11','referlevel_12','referlevel_13','referlevel_14','referlevel_15','referlevel_16','referlevel_17','referlevel_18','referlevel_19','referlevel_20','referlevel');
		foreach($commonSetting as $key => $value){
			if($data['tool']['vendor_id'])
				$data['default'][$value] 	= $this->Product_model->getVendorSettings($data['tool']['vendor_id'], $value);
			else
				$data['default'][$value] 	= $this->Product_model->getSettings($value);
		}

		$data['default_marketpostback'] = $this->Product_model->getSettings('marketpostback');
		$data['cookie_setting'] = $this->Product_model->getSettings('store', 'affiliate_cookie');
		$data['programs'] = $this->IntegrationModel->getPrograms($program_filter);
		$data['type'] = $type;
		$data['p_categories'] = $this->db->query("SELECT id,name FROM integration_category ".$where)->result_array();
		$data['CurrencySymbol'] = $this->currency->getSymbol();
		$data['integration_plugins'] = modules_list();
		$data['randome_code'] = generateRandomAlpahaNemericCode();
 
		$this->Report_model->view('admincontrol/integration/integration_tools_form', $data);
	}

	public function generateRandomCodeApi()
	{
		$post = $this->input->post();
		if(isset($post['tool_type']) && isset($post['program_tool_id']))
		{
			$tooltype=$post['tool_type'];
			$program_tool_id=$post['program_tool_id'];

			start:
			$randome_code= generateRandomAlpahaNemericCode();
			if($tooltype == 'action' || $tooltype == 'single_action'){
				$checkActionCode = $this->db->query("SELECT * FROM integration_tools WHERE action_code like ". $this->db->escape($randome_code) ." AND id != ". $program_tool_id)->num_rows();
				if($checkActionCode > 0) 
					goto start;	

			}  else if($data['tool_type'] == 'general_click'){
				$checkActionCode = $this->db->query("SELECT * FROM integration_tools WHERE general_code like ". $this->db->escape($$randome_code) ." AND id != ". $program_tool_id)->num_rows();
				if($checkActionCode > 0) 
					goto start;	
			}

		}
		else
			$randome_code= generateRandomAlpahaNemericCode();
		
		echo json_encode($randome_code);
	}


	function get_users_for_integration() {
		$post = $this->input->post();
		$where = "";
		if(isset($post['users_name_string']) && !empty($post['users_name_string'])) {
			$where = " AND username LIKE '%".$post['users_name_string']."%'";
		}
		$users = $this->db->query("SELECT username as name,id FROM users WHERE type='user' ".$where)->result_array();
		echo json_encode($users);
		exit;
	}

	function get_groups_for_integration() {
		$post = $this->input->post();
		$where = "";
		if(isset($post['group_name_string']) && !empty($post['group_name_string'])) {
			$where = " AND group_name LIKE '%".$post['group_name_string']."%'";
		}
		$users = $this->db->query("SELECT group_name, id FROM user_groups WHERE id!=0 ".$where)->result_array();
		echo json_encode($users);
		exit;
	}

	function valid_url_custom($url) {
		if(filter_var($url, FILTER_VALIDATE_URL)){
			return TRUE;
		}
		else{
			return TRUE;
		}
	}

	public function integration_tools_form_post(){
		if(!$this->userdetails()){ redirect('admincontrol/dashboard', 'refresh'); }
		
		$json =array();
		$custom_errors = [];
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
		$this->form_validation->set_rules('status', 'Status', 'required|trim');
		$this->form_validation->set_rules('type', 'Type', 'required|trim');
		$this->form_validation->set_rules('tool_type', 'Tool Type', 'required|trim');

		$old = $this->db->query("SELECT * FROM integration_tools WHERE id=". (int)$program_tool_id)->row();

		if($data['tool_period'] == 2){
			$this->form_validation->set_rules('end_date', 'End Date', 'required');
		} else if($data['tool_period'] == 3){
			$this->form_validation->set_rules('start_date', 'Start Date', 'required');
		} else if($data['tool_period'] == 4){ 
			$this->form_validation->set_rules('start_date', 'Start Date', 'required');
			$this->form_validation->set_rules('end_date', 'End Date', 'required');
		}

		if($data['cookies_type'] == 1){
			$this->form_validation->set_rules('custom_cookies', __('admin.custom_cookies_tracker_in_days'), 'required');
		} else {
			$data['custom_cookies'] = null;
		}

		if($data['tool_type'] == 'action'){
			if($old->vendor_id){
				$this->form_validation->set_rules('admin_action_click', 'Action Click', 'required|trim');
				$this->form_validation->set_rules('admin_action_amount', 'Action Amount', 'required|trim');

				if((float)$data['admin_action_amount'] > 0 && $old->action_amount < (float)$data['admin_action_amount']){
					$custom_errors['admin_action_amount'] = 'Must be less than vendor commission ('. c_format($old->action_amount) .')';
				}

			} else{
				$this->form_validation->set_rules('action_click', 'Action Click', 'required|trim');
				$this->form_validation->set_rules('action_amount', 'Action Amount', 'required|trim');
			}
			$this->form_validation->set_rules('action_code', 'Action Code', 'required|trim');
			$data['program_id'] = 0;
		} else if($data['tool_type'] == 'general_click'){
			if($old->vendor_id){
				$this->form_validation->set_rules('admin_general_click', 'General Click', 'required|trim');
				$this->form_validation->set_rules('admin_general_amount', 'General Amount', 'required|trim');

				if((float)$data['admin_general_amount'] > 0 && $old->general_amount < (float)$data['admin_general_amount']){
					$custom_errors['admin_general_amount'] = 'Must be less than vendor commission ('. c_format($old->general_amount) .')';
				}

			} else{
				$this->form_validation->set_rules('general_click', 'General Click', 'required|trim');
				$this->form_validation->set_rules('general_amount', 'General Amount', 'required|trim');
			}

			$this->form_validation->set_rules('general_code', 'General Code', 'required|trim');
			$data['program_id'] = 0;
		} else if($data['tool_type'] == 'program'){
			$this->form_validation->set_rules('program_id', 'Program', 'required|trim');
			$this->form_validation->set_rules('tool_integration_plugin', 'Integration Plugin', 'required|trim');
		}

		if($data['type'] == 'text_ads'){
			$this->form_validation->set_rules('text_ads_content', 'Ads Content', 'required|trim');
			$this->form_validation->set_rules('text_color', 'Color', 'required|trim');
			$this->form_validation->set_rules('text_bg_color', 'Background color', 'required|trim');
			$this->form_validation->set_rules('text_border_color', 'Border color', 'required|trim');
			$this->form_validation->set_rules('text_size', 'Border color', 'required|trim');
		} else if($data['type'] == 'link_ads'){
			$this->form_validation->set_rules('link_title', 'Link Title', 'required|trim');
		} else if($data['type'] == 'video_ads'){
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
			foreach ($custom_errors as $key => $value) { $json['errors'][$key] = $value; }
		} else {
			
			$checkActionCode = 0;

			foreach ($custom_errors as $key => $value) { $json['errors'][$key] = $value; }

			 
			if($data['tool_type'] == 'action' || $data['tool_type'] == 'single_action'){
				$checkActionCode = $this->db->query("SELECT * FROM integration_tools WHERE action_code like ". $this->db->escape($data['action_code']) ." AND id != ". $program_tool_id)->num_rows();
				if($checkActionCode > 0)  $json['errors']['action_code'] = "Action code to be unique";
			}  else if($data['tool_type'] == 'general_click'){
				$checkActionCode = $this->db->query("SELECT * FROM integration_tools WHERE general_code like ". $this->db->escape($data['general_code']) ." AND id != ". $program_tool_id)->num_rows();
				if($checkActionCode > 0) $json['errors']['general_code'] = "General code to be unique";
			}

			if($data['tool_type'] == 'single_action' || $data['tool_type'] == 'action'){
				$featured_image = 'action.jpg';
			} else if($data['tool_type'] == 'general_click') {
				$featured_image = 'click.jpg';
			} else if($data['tool_type'] == 'program'){
				switch ($data['tool_integration_plugin']){
					case 'woocommerce':
					$featured_image = 'woo.png';
					break;
					case 'prestashop':
					$featured_image = 'prestashop.png';
					break;
					case 'opencart':
					$featured_image = 'opencart.png';
					break;
					case 'magento':
					$featured_image = 'magento.png';
					break;
					case 'shopify':
					$featured_image = 'shopify.png';
					break;
					case 'bigcommerce':
					$featured_image = 'Big-Commerce.jpg';
					break;
					case 'paypal':
					$featured_image = 'paypal.png';
					break;
					case 'oscommerce':
					$featured_image = 'oscommerce.png';
					break;
					case 'zencart':
					$featured_image = 'zencart.png';
					break;
					case 'xcart':
					$featured_image = 'xcart.png';
					break;
					case 'laravel':
					$featured_image = 'laravel.png';
					break;
					case 'cakephp':
					$featured_image = 'cackphp.png';
					break;
					case 'codeigniter':
					$featured_image = 'codeigniter.png';
					break;
					default:
					$featured_image = 'order.jpg';
				}
			}
			$data['deafult_featured_image'] = $featured_image;


			if (isset($data['marketpostback']['status']) && $data['marketpostback']['status'] == 'custom') {
				if (filter_var($data['marketpostback']['url'], FILTER_VALIDATE_URL) === FALSE) {
					$json['errors']['marketpostback-url'] = 'Enter a valid postback URL';
				}
			}

			if(count($json)==0 || count($json['errors']) == 0){
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
 
				$program_tool_id = $this->IntegrationModel->editProgramTools($data, $_FILES['custom_banner']);

				if($program_tool_id){
					if(isset($data['save_close'])){
						$json['location'] = base_url("integration/integration_tools_form/". $data['type'] ."/". $program_tool_id);
					} else{
						$json['location'] = base_url("integration/integration_tools");
					}
				} else{
					$json['errors']['name'] = "Something Wrong";
				}
			}
		}

		echo json_encode($json);
	}

	public function integration_tools_delete($tools_id){
		if(!$this->userdetails()){ redirect('admincontrol/dashboard', 'refresh'); }
		$this->IntegrationModel->deleteTools($tools_id);
		$this->session->set_flashdata('success', __('admin.campaign_deleted_successfully'));
		redirect(base_url("integration/integration_tools"));
	}

	public function tool_get_code($control = 'admincontrol'){
		$tools_id = (int)$this->input->post("id",true);
		if($control == 'admincontrol'){
			if(!$this->userdetails()){ redirect('admincontrol/dashboard', 'refresh'); }
			$data['user_id'] = $this->userdetails()['id'];
		}
		else if($control == 'usercontrol'){
			if(!$this->userlogins()){ redirect('usercontrol/dashboard', 'refresh'); }
			$data['user_id'] = $this->userlogins()['id'];
		}

		$this->load->library("socialshare");
		$data['social_share_modal'] =  $this->socialshare->get_dynamic_social_share_btns();
		$data['tool'] = $this->IntegrationModel->getProgramToolsByID($tools_id,$data['user_id']);
		if(!empty($data['tool'])){
		 	
			$json['html'] = $this->load->view("integration/code", $data, true);
		}
	
		echo json_encode($json);die;
	}

	public function tool_get_terms($control = 'usercontrol'){
		$tools_id = (int)$this->input->post("id",true);
		if($control == 'usercontrol'){
			if(!$this->userlogins()){ redirect('usercontrol/dashboard', 'refresh'); }
			$data['user_id'] = $this->userlogins()['id'];
		}
		
		$data['terms_data'] = $this->IntegrationModel->getTermsToolsByID($tools_id);
		if($data['terms_data']){
			$json['html'] = $this->load->view("integration/terms", $data, true);
		}
		
		echo json_encode($json);die;
	}

	public function user_integration_tools(){
		$user = $this->userlogins();
		if(!$user){ redirect('usercontrol/dashboard', 'refresh'); }
		
		$data['tools'] = $this->IntegrationModel->getProgramTools([
			'user_id' => $user['id'],
			'status' => 1,
			'redirectLocation'=> 1,
			'restrict'=> $user['id'],
		]);

		$this->Report_model->view('usercontrol/integration/integration_tools', $data,'usercontrol');
	}

	public function orders(){
		if(!$this->userdetails()){ redirect('admincontrol/dashboard', 'refresh'); }

		if ($this->input->server('REQUEST_METHOD') == 'POST'){
			$json = array();
			$orders = $this->IntegrationModel->getDeleteOrders($this->input->post('ids',true));
			$html = '<table class="table table-sm table-bordered"><tr><td>Id</td><td>Commission</td><td>Refer Commission</td></tr>';
			foreach ($orders as $key => $value) {
				$html .= '<tr>';
				$html .= '	<td>'. $key ."</td>";
				$html .= '	<td>'. c_format($value['commission']) ."</td>";
				$html .= '	<td>'. c_format($value['refer_commission']) ."</td>";
				$html .= '</tr>';
			}
			$html .= '</table>';

			$json['html'] = $html;
			echo json_encode($json);die;
		}

		$data['orders'] = $this->IntegrationModel->getOrders();
		$this->Report_model->view('admincontrol/integration/orders', $data);
	}
	
	public function deleteOrdersConfirm(){
		if(!$this->userdetails()){ redirect('admincontrol/dashboard', 'refresh'); }

		if ($this->input->server('REQUEST_METHOD') == 'POST'){
			$json = array();
			$orders = $this->IntegrationModel->getDeleteOrders($this->input->post('ids',true));
			
			foreach ($orders as $key => $value) {
				foreach ($value['sql'] as $key => $sql) {
					$this->db->query($sql);
				}
			}
		}		

		echo json_encode($json);die;
	}
	
	public function user_orders(){
		$user = $this->userlogins();
		if(!$user){ redirect('usercontrol/dashboard', 'refresh'); }

		$data['orders'] = $this->IntegrationModel->getOrders(['user_id' => $user['id']]);
		$this->Report_model->view('usercontrol/integration/orders', $data,'usercontrol');
	}

	public function logs(){
		if(!$this->userdetails()){ redirect('admincontrol/dashboard', 'refresh'); }
		$this->load->library('pagination');
		$this->load->helper('url');

		$filter['page'] = isset($_GET['page']) ? $_GET['page'] : 1;
		if(isset($_GET['type']) && $_GET['type']){
			$filter['type'] = $_GET['type'];
		}

		$_data = $this->IntegrationModel->getLogs($filter);

		$config['base_url'] = base_url('integration/logs');
		$config['per_page'] = 50;
		$config['total_rows'] = $_data['total'];
		$this->pagination->initialize($config);
		$data['pagination'] = $this->pagination->create_links();
		
		$data['logs'] = $_data['records'];

		$this->Report_model->view('admincontrol/integration/logs', $data);
	}

	public function click_logs(){
		$user = $this->userlogins();
		if(!$user){ redirect('usercontrol/dashboard', 'refresh'); }
		$this->load->library('pagination');
		$this->load->helper('url');

		$filter['page'] = isset($_GET['page']) ? $_GET['page'] : 1;
		$filter['user_id'] = $user['id'];

		if(isset($_GET['type']) && $_GET['type']){
			$filter['type'] = $_GET['type'];
		}

		$_data = $this->IntegrationModel->getLogs($filter);


		$config['base_url'] = base_url('integration/click_logs');
		$config['per_page'] = 50;
		$config['total_rows'] = $_data['total'];
		$this->pagination->initialize($config);
		$data['pagination'] = $this->pagination->create_links();
		
		$data['logs'] = $_data['records'];

		$this->Report_model->view('usercontrol/integration/logs', $data ,'usercontrol');
	}

	public function delete_log(){
		$ids = (array)$this->input->post('ids',true);
		if($ids){
			$ids = implode(",", $ids);

			$this->db->query("DELETE FROM integration_clicks_logs WHERE id IN ({$ids})");
		}

		echo json_encode(array());		 
	}

	public function _zip($archive_folder,  $archive_name){
		$zip = new ZipArchive; 
		$archive_path = APPPATH . "cache/". $archive_name;
		if ($zip->open($archive_path, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) 
		{ 
			$dir = preg_replace('/[\/]{2,}/', '/', $archive_folder."/"); 
			$dirs = array($dir); 

			$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($archive_folder), RecursiveIteratorIterator::SELF_FIRST);
			foreach ($files as $file){
				$file = str_replace('\\', '/', $file);
				if( in_array(substr($file, strrpos($file, '/')+1), array('.', '..')) ) continue;

				$file = realpath($file);

				$n = str_replace(APPPATH. (str_replace('application/', '', $archive_folder)) , '', $file);
				if (is_dir($file) === true){
					$zip->addEmptyDir(str_replace($archive_folder, '', $n . '/'));
				}else if (is_file($file) === true){
					$content = str_replace('__baseurl__', base_url(),file_get_contents($file));
					$zip->addFromString($n, $content);
				}
			}

			if ($zip->status == ZIPARCHIVE::ER_OK){
				$zip->close();
			}

			header($_SERVER['SERVER_PROTOCOL'].' 200 OK');
			header("Content-Type: application/zip");
			header("Content-Transfer-Encoding: Binary");
			header("Content-Length: ".filesize($archive_path));
			header("Content-Disposition: attachment; filename=\"".$archive_name."\"");
			header('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT');
			header("Cache-control: private");
			header('Pragma: private');

			ob_end_clean();
			readfile($archive_path);

			@unlink($archive_path);
		} 
		else 
		{ 
			echo 'Error, can\'t create a zip file!'; 
		}
	}

	public function download_plugin($script, $version = 0){
		if($script == 'woocommerce'){
			$path = "application/plugins/tracking-affiliate-pro/";
			$this->_zip($path,'AffiliatePro_WooCommerce.zip');
		}
		else if($script == 'php_api_library'){
			$path = "application/plugins/php_api_library/";
			$this->_zip($path,'php_api_library.zip');
		}
		else if($script == 'wp_user_register'){
			$path = "application/plugins/wp_user_register/";
			$this->_zip($path,'WordpressWoocommerceRegistrationBridge.zip');
		}
		else if($script == 'show_affiliate_id'){
			$path = "application/plugins/show-affiliate-id/";
			$this->_zip($path,'WordpressShowAffiliateID.zip');
		}
		else if($script == 'prestashop'){
			$path = "application/plugins/ps_aff/";
			$this->_zip($path,'ps_aff.zip');
		}
		else if($script == 'magento'){
			if($version == 1){
				$path = "application/plugins/magento1/";
				$this->_zip($path,'AffiliatePro_Magento.zip');
			} else{
				$path = "application/plugins/magento/";
				$this->_zip($path,'AffiliatePro_Magento.zip');
			}
		}
		else if($script == 'opencart'){
			if($version  == 1){
				$path = "application/plugins/opencart-1564-2200/";
				$this->_zip($path,'AffiliatePro_Opencart-1564-2200.ocmod.zip');
			}
			else if($version  == 2){
				$path = "application/plugins/opencart-2300-3011/";
				$this->_zip($path,'AffiliatePro_Opencart-2300-3011.ocmod.zip');
			}
		}
	}

	public function integration_category_delete($category_id = 0){
		$userdetails = $this->userdetails();
		if(!$this->userdetails()){ redirect($this->admin_domain_url, 'refresh'); }

		if($category_id > 0){
			$data['category'] = $this->db->query("DELETE FROM integration_category WHERE id = ". (int)$category_id);
		}

		$this->session->set_flashdata('success',__('admin.category_deleted_successfully'));
		redirect(base_url('integration/integration_category'));
	}

	public function category_auto(){
		$userdetails = $this->userdetails();
		if(!$this->userdetails()){ redirect($this->admin_domain_url, 'refresh'); }
		$keyword = $this->input->get('query');
		
		 
		$data = $this->db->query("SELECT integration_category.id as value,integration_category.name as label, CASE WHEN integration_category.parent_id=0 THEN integration_category.id ELSE integration_category.parent_id END AS pid FROM integration_category WHERE integration_category.name  like ". $this->db->escape("%".$keyword."%") ." order by pid,integration_category.id")->result_array();
		
		echo json_encode($data);die;
	}

	public function integration_category_add($category_id = 0){
		$userdetails = $this->userdetails();
		if(!$this->userdetails()){ redirect($this->admin_domain_url, 'refresh'); }

		if ($this->input->server('REQUEST_METHOD') == 'POST'){
			$this->load->library('form_validation');
			$this->form_validation->set_rules('name', 'Category Name', 'required');
			
			if($this->form_validation->run()){
				$details = array(
					'name' =>  $this->input->post('name',true),
					'parent_id' =>  $this->input->post('parent_id',true),
				);

				if($category_id){
					$this->Product_model->update_data('integration_category', $details, array('id' => $category_id));
				}else{
					$details['created_at'] = date('Y-m-d H:i:s');
					$category_id = $this->Product_model->create_data('integration_category', $details);
				}

				$add_category_to_form = $this->input->post('add_category_to_form',true);
				if(isset($add_category_to_form)){
					$category = $this->db->query("SELECT id,name FROM integration_category WHERE id = ". (int) $category_id)->row_array();
					$json['message'] = __('admin.category_save_successfully');
					$json['newOption'] = '<option value="'.$category['id'].'">'.$category['name'].'</option>';
				} else {
					$this->session->set_flashdata('success',__('admin.category_save_successfully'));
					$json['location'] = base_url('integration/integration_category');
				}
			} else {
				$json['errors'] = $this->form_validation->error_array();
			}

			echo json_encode($json);die;
		}

		$data['category'] = array();

		$where = "";
		
		if($category_id > 0){
			$data['category'] = $this->db->query("SELECT * FROM integration_category WHERE id = ". (int)$category_id)->row_array();
			
			$where = " where id != ".$category_id." and ( parent_id != ".$category_id." OR parent_id IS NULL)";

		}

		$data['p_categories'] = $this->db->query("SELECT id,name FROM integration_category where parent_id=0")->result_array();
		
		$data['categories'] = $this->db->query("SELECT id,name FROM integration_category")->result_array();

		$this->view($data, 'integration/category_add');
	}

	public function integration_category($page = 1){
		$userdetails = $this->userdetails();
		if(!$this->userdetails()){ redirect($this->admin_domain_url, 'refresh'); }

		if ($this->input->server('REQUEST_METHOD') == 'POST'){
			$page = max((int)$page,1);
			
			$filter = array(
				'limit' => 100,
				'page' => $page,
			);

			list($data['categories'],$total) = $this->Product_model->getIntegrationCategory($filter);
			$data['start_from'] = (($page-1) * $filter['limit'])+1;
			$json['html'] = $this->load->view("admincontrol/integration/category_list.php",$data,true);

			$this->load->library('pagination');
			$config['base_url'] = base_url('admincontrol/integration_category/');
			$config['per_page'] = $filter['limit'];
			$config['total_rows'] = $total;
			$config['use_page_numbers'] = TRUE;
			$config['enable_query_strings'] = TRUE;
			$this->pagination->initialize($config);

			$json['pagination'] = $this->pagination->create_links();

			echo json_encode($json);die;
		}
		
		$this->view($data, 'integration/integration_category');
	}


	public function check_campaign_security_with_id($id) {
		if(!$this->userdetails()){ die(); }

		if((int) $id){
			$data = [];

			$tool = $this->IntegrationModel->getProgramToolsByID($id);
			if(!empty($tool)){
				$security_alerts = external_integration_security_check($tool['target_link']);
				$status = getSecurityStatus($security_alerts,$tool['tool_type'],$tool['tool_integration_plugin'],$tool['program_id']);

				if($tool['security_status'] == 1 && $status == 0){
					$this->db->query('UPDATE integration_tools SET security_status=0 WHERE id='.$tool['id']);
					$data['security_status'] = 0;
					$data['statusClass'] = 'badge bg-info';
					$data['message'] = __('admin.pending_integration');
					$data['integration_code_button'] = '<button data-toggle="tooltip" title="'.__('admin.integration_code').'" 
					class="btn-show-code badge bg-info" data-id="'.$tool['id'].'">
					<i class="fa fa-code" aria-hidden="true"></i>
					</button>';
				}

				if($tool['security_status'] == 0 && $status == 1){
					$this->db->query('UPDATE integration_tools SET security_status=1 WHERE id='.$tool['id']);
					$data['security_status'] = 1;
					$data['statusClass'] = 'badge bg-success';
					$data['message'] = __('admin.approved');
				}
			}
			
			echo json_encode($data);
		}	
	}

	public function check_campaign_security() {
		if(!$this->userdetails()){ die(); }

		if ($this->input->server('REQUEST_METHOD') == 'POST'){

			$result = [];

			$post = $this->input->post(null,true);

			$offset = isset($post['index']) ? $post['index'] - 1 : 0;

			$tool = $this->db->query('SELECT * FROM integration_tools LIMIT '.$offset.', 1')->row_array();

			if(!empty($tool)){
				$integration_tools_count = $this->db->query('SELECT COUNT(id) as tools_count FROM integration_tools')->row()->tools_count;

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
					$result['message'] = __('admin.campaigns_verified_successfully');
				} else {
					$result['security_status'] = 'pending';
					$result['message'] = __('admin.campaigns_in_pending_integration');
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
}