<?php
use App\User;

class MY_Controller extends CI_Controller {
	
	protected $header_menu;

	public $admin_domain_url = 'admin';

	function __construct() {
		parent::__construct();
		$this->db->query("SET SQL_MODE = ''");
		$this->load->model("Product_model");

		$security = $this->Product_model->getSettings('security','admin_url');
		if(isset($security['admin_url']) && ! empty($security['admin_url'])) {
			$this->admin_domain_url = $security['admin_url'];
		}

		$site_setting = $this->Product_model->getSettings('site');

		$method = $this->uri->segment('1');
	
		$escape_urls = ['store','admin','admincontrol','auth','integration','firstsetting', 'membership','theme_api',$this->admin_domain_url];
		
		 
		if ($site_setting['maintenance_mode'] && !in_array($method, $escape_urls))
		 {
		 	echo $this->load->view('common/maintenance', [], true);
			die;
		}

		if (isset($site_setting['time_zone']) && $site_setting['time_zone'] != '') {
			date_default_timezone_set($site_setting['time_zone']);
		} else{
			date_default_timezone_set('Asia/Kolkata');
		}

		$table = $this->db->table_exists('ci_sessions');
		if($table) { 
			$this->load->dbforge();
			$this->dbforge->drop_table('ci_sessions');
		}
		
		$loginData = [];
		$loginData[] = $this->session->userdata('user');
		$loginData[] = $this->session->userdata('client');

		foreach($loginData as $login) {
			if(isset($login['id']) && !empty($login['id'])) {
				$isUserExist = $this->db->query('SELECT id FROM users WHERE id='.$login['id'])->row();
				if(!isset($isUserExist->id)) {
					$this->session->sess_destroy();
					header("refresh: 1");
					break;
				}
			}
		}

		if ($this->session->userdata('administrator') && $this->input->is_ajax_request() == false && file_exists(APPPATH . "updates/database_update_" . $this->config->item('app_version') . ".sql")) {
		    $this->load->model('Version_changes_model');
		    $this->Version_changes_model->update_changes();
		    $this->update_user_langauges();
		    $this->Version_changes_model->store_version_update_details();
		    updateVersiontoserver($this->config->item('app_version'), CODECANYON_LICENCE);
		    if (current_url() != base_url('admincontrol/system_update_report'))
		        redirect(base_url('admincontrol/system_update_report'));
		}

		$this->load->model('Version_changes_model');
		$this->Version_changes_model->update_mail_templates();
		___construct(1);
	}

	public function checkSessionTimeout() {
        $site_setting_timeout = $this->Product_model->getSettings('site', 'session_timeout');
        $timeout = (isset($site_setting_timeout['session_timeout']) && is_numeric($site_setting_timeout['session_timeout']) && ((int)$site_setting_timeout['session_timeout']) >= 60) ? (int)$site_setting_timeout['session_timeout'] : 1800;

        $this->load->vars(array('timeout' => $timeout));

        if ($this->session->has_userdata('timestamp') && (time() - $this->session->userdata('timestamp')) > $timeout) {
            $this->session->sess_destroy();
            redirect($this->admin_domain_url);
        } elseif ($this->uri->segment(2) != "ajax_dashboard") {
            $this->session->set_userdata('timestamp', time());
        }
    }

	public function build_paginate($query,$base,$page = 1, $limit=15){
		$this->load->library('pagination');
		$this->pagination->cur_page = $page;

		$total = $query->total();
		$found = count($query);

		$config['base_url'] = base_url($base);
		$config['per_page'] = $limit;
		$config['total_rows'] = $total;
		$config['use_page_numbers'] = TRUE;
		$config['enable_query_strings'] = TRUE;
		$config['reuse_query_string'] = TRUE;
		$_GET['page'] = $page;
		$config['page_query_string'] = 'page';
		$this->pagination->initialize($config);

		if($total == 0) {
			$result = "";
		} else {
			$result = '<div><div style="color:#333">'.__('admin.showing').'<span style="color:#3E7CB3"> '. max((($page-1)*$limit),0) .'-'. ((($page-1)*$limit)+$found) .' </span>'.__('admin.of').' <span style="color:#3E7CB3">'.$total.'</span> '.__('admin.results').'</div></div>';
		}
		
		return  [$this->pagination->create_links(),$result];
	}

	public function checkLogin($type = 'admin'){
		if($type == 'admin'){
			$type = 'administrator';
		}

		$userdetails = $this->session->userdata($type);
		if(!$userdetails){
			if($type == 'administrator'){
				redirect($this->admin_domain_url);
			}
			else {
				redirect('/');
			}
		}

		return $userdetails;
	}

	public function post_data(){
		return $this->input->post(NULL,true);
	}

	public function session_message($message, $type = 'success'){
		$this->session->set_flashdata($type, $message);
	}

	public function json($json = array()){
		header('Content-Type: application/json');
		echo json_encode($json);
	}

	public function get_restricted_vendors(int $user_id = null, $product_slug=null) {

		$this->load->model('Product_model');
		$restricted_vendors = [];

		//get all vendors from database
		$vendors = $this->db->query('SELECT id from users where is_vendor = 1')->result_array();

		//restrict function for store mode
		if($product_slug) {
			$product = $this->db->query("SELECT * FROM product WHERE product_slug like '". $product_slug ."' ")->row_array();
			if(!empty($product)) {

				$site_setting = $this->Product_model->getSettings('marketvendorstatus');
				$store_setting = $this->Product_model->getSettings('store', 'store_mode');
				$market_vendor = $this->Product_model->getSettings('market_vendor');
				$vendor_setting = $this->Product_model->getSettings('vendor');
				
				if ($vendor_setting['storestatus'] == 0) {
					$restricted_vendors[] = $user_id;
				}
			}
		}

		$vendoerMinDeposit = $this->Product_model->getSettings('site', 'vendor_min_deposit');

		$vendoerMinDeposit = isset($vendoerMinDeposit['vendor_min_deposit']) ? $vendoerMinDeposit['vendor_min_deposit'] : 0;

		$this->load->model('Total_model');

		//restrict function for deposit
		$vendorDepositStatus = $this->Product_model->getSettings('vendor', 'depositstatus');
		if($vendorDepositStatus['depositstatus'] == 1){
			if($user_id == null) {
				foreach($vendors as $v) {
					$balence = $this->Total_model->getUserBalance($v['id']);
					if($balence < $vendoerMinDeposit) {
						$restricted_vendors[] = $v['id'];
					}
				}
			} else {
				
				if($product_slug==null){
					$balence = $this->Total_model->getUserBalance($user_id);
					if($balence < $vendoerMinDeposit) {
						$restricted_vendors[] = $user_id;
					}
				}
			}
		}

		$blocked_vendors = $this->Product_model->getBlockedVendors();				

		$restricted_vendors = array_unique(array_merge($blocked_vendors, $restricted_vendors));
		

		/* Local store Other Affiliates Selling My Products? */

			if($user_id>0)
			{
				$userrefidarray = $this->db->query('SELECT refid FROM users WHERE id='.$user_id )->row_array();

				if(is_array($userrefidarray))
					$userrefidcommon=$userrefidarray["refid"];
				else
					$userrefidcommon=0;	

				$escapevendorscommon = $this->db->query('SELECT user_id,vendor_status FROM vendor_setting WHERE vendor_status>0')->result_array();
	  		 
	  
				$allowVendorscommon = [];
				foreach($escapevendorscommon as $esc) 
				{
					if($esc['vendor_status']==1)
						$allowVendorscommon[] = $esc['user_id'];
					else if($esc['vendor_status']==2 && $esc['user_id']==$userrefidcommon)
						$allowVendorscommon[] = $esc['user_id'];

				}
	 
				$escapeUserscommon = [];
				foreach($vendors as $v) {
					if(!in_array($v['id'], $allowVendorscommon))
						$escapeUserscommon[] = $v['id'];
				} 

		 		$restricted_vendors=array_unique(array_merge($restricted_vendors, $escapeUserscommon));
			}
			 
		/* Local store Other Affiliates Selling My Products? */

		$MembershipSetting = $this->Product_model->getSettings('membership');

		if($MembershipSetting['status']){

			$noMembershipVendors = $this->getNoMembershipUsers($restricted_vendors);
			
			$restricted_vendors = array_unique(array_merge($noMembershipVendors, $restricted_vendors));
		}
		
		return $restricted_vendors;
	}

	//SaaS setting vendor mode on/off
	public function allowCommissionFromVendorPanelMode($owner, $isVendor)
	{
		$this->load->model('Product_model');
		$market_vendor_settings = $this->Product_model->getSettings('market_vendor');
		$marketvendorpanelmode = $market_vendor_settings['marketvendorpanelmode'] ?? 0;
		if($marketvendorpanelmode == 1 && $owner == 1 && $isVendor) {
			return false;
		}
		return true;
	}

	public function getNoMembershipUsers($alreadyRestricted) {

		$result = [];
		if(empty($alreadyRestricted)) {
			$users = $this->db->query('select id from users where type="user"')->result();
		} else {
			$users = $this->db->query('select id from users where type="user" and id NOT IN ('.implode(',', $alreadyRestricted).')')->result();
		}

		foreach($users as $v) {
			$user = User::find($v->id);

			if(empty($user) || (int)$user->plan_id == 0){
				$result[] = $v->id;continue;
			}

			if((int)$user->plan_id > 0){
				$plan = $user->plan();

				if(empty($plan)){

					$result[] = $v->id;continue;

				} else if($plan->isExpire() || !$plan->strToTimeRemains() > 0){

					$lifetime = ($plan->is_lifetime && $plan->status_id) ? true : false;

					if(!$lifetime){
						$result[] = $v->id;continue;
					}

				}

			}
		}


		return $result;
	}

	public function view($data, $file, $control = 'admincontrol'){
		
		$this->load->view($control.'/includes/header', $data);
		if($control == 'admincontrol'){
		$this->load->view($control.'/includes/sidebar', $data);
		$this->load->view($control.'/includes/topnav', $data);
		}
		$this->load->view($control.'/'. $file, $data);
		$this->load->view($control.'/includes/footer', $data);
	}
	
	public function update_user_langauges($is_admin_request = null) {
		$data['results'] = [["info"=>"Language files update is started..."]];
		try {
			$all_languages_json = file_get_contents(FCPATH.'assets/data/languages.json');
			$all_languages = json_decode($all_languages_json, true);

			$userLanguagesQuery = $this->db->get('language');
			$userLanguages = $userLanguagesQuery->result();
			
			if($is_admin_request != null) {
				$files_updated = 0;
			}

			foreach($userLanguages as $language) {
				if($language->name == "English") {
					$userLanguagesDataPath = FCPATH."application/language/default";
					$defaultLanguagesDataPath = FCPATH."application/language/default/default";
				} else {
					$userLanguagesDataPath = FCPATH."application/language/".$language->id;
					$languages_code = array_search($language->name, $all_languages);
					$defaultLanguagesDataPath = FCPATH."application/language/default/".$languages_code;
				}

				if(is_dir($userLanguagesDataPath) && is_dir($defaultLanguagesDataPath)) {
					$defaultLangData = [];
					$selected_folders = scandir($defaultLanguagesDataPath);
					for ($i = 2; $i < sizeof($selected_folders); $i++){
						if(is_file($defaultLanguagesDataPath."/".$selected_folders[$i]) && strpos($selected_folders[$i], '.php') !== false) {
							$defaultLangData[$selected_folders[$i]] = file($defaultLanguagesDataPath."/".$selected_folders[$i], FILE_SKIP_EMPTY_LINES);
						}
					}

					$userLangData = [];
					$selected_folders = scandir($userLanguagesDataPath);
					for ($i = 2; $i < sizeof($selected_folders); $i++){
						if(is_file($userLanguagesDataPath."/".$selected_folders[$i]) && strpos($selected_folders[$i], '.php') !== false) {
							$lines = file($userLanguagesDataPath."/".$selected_folders[$i], FILE_SKIP_EMPTY_LINES);   
							$lines = array_filter($lines, function($line) {
								return strpos($line, "'';") == false && (strpos($line, "\$lang") !== false || strpos($line, "?php") !== false);
							});
							file_put_contents($userLanguagesDataPath."/".$selected_folders[$i], implode("\n", $lines));
							$userLangData[$selected_folders[$i]] = file_get_contents($userLanguagesDataPath."/".$selected_folders[$i]);
						}
					}

					$newLineAdded = false;
					foreach($defaultLangData as $key => $default) {
						for ($i=0; $i < sizeof($default); $i++) {
							$lang_key = trim(explode("=",$default[$i])[0]); 
							$lang_key = str_replace("\$lang['", "", $lang_key);
							$lang_key = str_replace("']", "", $lang_key);
							if (!str_contains($userLangData[$key], $lang_key)) { 
								if(!$newLineAdded) {
									file_put_contents($userLanguagesDataPath.'/'.$key, "\n", FILE_APPEND);
									$newLineAdded = true;
								}
								file_put_contents($userLanguagesDataPath.'/'.$key, $default[$i], FILE_APPEND);
								if($is_admin_request != null) {
									$files_updated++;
								}
							}
						}
					}

					if($newLineAdded == true) {
						$data['results'][] = [
							"success" => $language->name. __('admin.language_files_updated_successfully')
						];
					}
				}
			}

			if($is_admin_request != null) {
				if($files_updated > 0) {
					$this->session->set_flashdata('success', __('admin.language_files_updated_successfully'));
				} else {
					$this->session->set_flashdata('success', __('admin.language_files_are_already_up_to_date'));
				}
				redirect('/admincontrol/language');
			}

			$data['results'][] = [
				"success" =>  __('admin.Language_files_update_completed')
			];
		} catch (Exception $e) {
			$data['results'][] = [
				"error" => $e->getMessage()
			];
		}

		return $data['results'];
	}

	public function upload_photo($fieldname,$path) {

		$config['upload_path'] = $path;

		$config['allowed_types'] = 'png|gif|jpeg|jpg|PNG|GIF|JPEG|JPG|ICO|ico';

		$config['max_size']      = 2048;

		$this->load->helper('string');

		$config['file_name']  = random_string('alnum', 32);

		$this->load->library('upload', $config);

		$this->upload->initialize($config);

		if (!$this->upload->do_upload($fieldname)) {

			$data = array('success' => false, 'msg' => $this->upload->display_errors());

		}

		else

		{

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

			$data = array('success' => true, 'upload_data' => $upload_details, 'msg' => __('admin.upload_success'));

		}

		return $data;
	}

	public function friendly_seo_string($vp_string){

		$vp_string = trim($vp_string);

		$vp_string = html_entity_decode($vp_string);

		$vp_string = strip_tags($vp_string);

		$vp_string = strtolower($vp_string);

		$vp_string = preg_replace('~[^ a-z0-9_.]~', ' ', $vp_string);

		$vp_string = preg_replace('~ ~', '-', $vp_string);

		$vp_string = preg_replace('~-+~', '-', $vp_string);

		return strtolower($vp_string);
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
}
