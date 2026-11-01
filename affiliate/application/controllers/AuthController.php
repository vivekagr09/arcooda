<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
use App\User;

class AuthController extends MY_Controller {
	function __construct() {
		parent::__construct();
		$this->load->model('Product_model');
        $this->load->model('Report_model');
		$this->load->model('User_model');
		$this->load->model('Common_model');
		$this->load->model('theme');

		___construct(1);

		$this->login_settings = $this->Product_model->getSettings('login');

		if(!isset($_SESSION['userLang'])) {
			$this->Product_model->setBrowserLanguage();
		}
	}


	public function user_login(){
		if($this->login_settings['front_template'] != 'landing'){ redirect("/"); }
        
        $data['SiteSetting'] = $this->Product_model->getSettings('site');;
        $data['title'] = 'Affiliate login';
		$data['meta_keywords'] = $data['SiteSetting']['meta_keywords'];
		$data['meta_description'] = $data['SiteSetting']['meta_description'];
        $this->render_page('auth/user/templates/login', $data);
    }

    public function user_forget_password(){
		if($this->login_settings['front_template'] != 'landing'){ redirect("/"); }
		$data['SiteSetting'] = $this->Product_model->getSettings('site');
		$data['title'] = "Affiliate Login";
		$this->render_page('auth/user/templates/forget_password', $data);
	}

	public function privacy_policy(){
		if($this->login_settings['front_template'] != 'landing'){ redirect("/"); }
		$data['tnc'] = $this->Product_model->getSettings('tnc');
		$data['title'] = $data['tnc']['heading'];
		$this->render_page('auth/user/templates/privacy_policy', $data);
	}

	public function change_language($language_id){
		$language = $this->db->query("SELECT * FROM language WHERE id=".$language_id)->row_array();
		if($language){
			$_SESSION['userLang'] = $language_id;
			$_SESSION['userLangName'] = $language['name'];
			header('Location: ' . $_SERVER['HTTP_REFERER']);
		}
		else { show_404(); }
	}

	public function changeLanguage(){
		$language_id = $this->input->post('language_id');
		$language = $this->db->query("SELECT * FROM language WHERE id=".$language_id)->row_array();
		if($language){
			$_SESSION['userLang'] = $language_id;
		}
		print_r($language_id);
	}

	public function user_register($refid = null){
		$this->session->set_userdata(array(
			'login_data'=> array(
				'refid' => $refid,
			),
		));
		redirect(base_url('register'));
	}

	public function vendor_register(){

		$registration_status = $this->Product_model->getSettings('store','registration_status');
		$data['vendor_storestatus'] = $this->Product_model->getSettings('vendor','storestatus');
		$data['vendor_marketstatus'] = $this->Product_model->getSettings('market_vendor', 'marketvendorstatus');
 

		if ((int)$registration_status['registration_status']==2  && $data['vendor_storestatus']["storestatus"]==0 && $data['vendor_marketstatus']["marketvendorstatus"]==0)
		{
			redirect(base_url().'register');
			die();
		}

		else if( ($data['vendor_storestatus']["storestatus"]==1 || $data['vendor_marketstatus']["marketvendorstatus"] ==1 ) && $registration_status['registration_status'] == 1 || $registration_status['registration_status'] == 2){

 
			$data['login'] = $this->login_settings;
		
			$siteSetting = $this->Product_model->getSettings('site');

			$this->load->model('PagebuilderModel');

			$login_data = $this->session->userdata("login_data");

			if(isset($login_data['refid'])){
				$data['refid'] = $login_data['refid'];
			}
			
			$data['design'] = '';
			$data['register_fomm'] = '';
			
			/*Get loginclient and tnc data to front**/
			$language_id=1;
			 if (isset($this->session) && $this->session->userdata('userLang') !== FALSE)
				$language_id=$this->session->userdata('userLang');

			$data['setting'] = $this->Product_model->getSettingsWithLanaguage('loginclient',$language_id);
			$data['tnc'] = $this->Product_model->getSettingsWithLanaguage('tnc',$language_id); 
			/*Get loginclient and tnc data to front**/

	        $data['SiteSetting'] = $this->Product_model->getSettings('site');
	        $data['countries'] = $this->User_model->getCountries();
			$data['title'] = $data['SiteSetting']['name'];
			$data['meta_keywords'] = $data['SiteSetting']['meta_keywords'];
			$data['meta_author'] = $data['SiteSetting']['meta_author'];
			$data['meta_description'] = $data['SiteSetting']['meta_description'];
			$data['footer'] = $data['SiteSetting']['footer'];
			$data['store'] = $this->Product_model->getSettings('store');
			
			$front_template = $this->login_settings['front_template'];
			
			if(isset($_GET['tmp_theme'])){
				$front_template = $_GET['tmp_theme'];
			}

			$lang = $_SESSION['userLang'];
			
			$data['selected_language'] = $this->db->query("SELECT * FROM language WHERE status=1 AND id=". (int)$lang)->row_array();
			
			if($front_template == 'multiple_pages'){
			   require(APPPATH.'controllers/Themes.php');
			   $Themes = new Themes(false);
			   $Themes->multiPages($this, 'register', true);
			} else if(substr($front_template,0,7) == 'custom_'){
				$registration_builder['template_index'] = substr($front_template, 7);
				$register_form = $this->PagebuilderModel->getSettings('registration_builder');
				$registration_builder['data'] = array();
				$registration_builder['allow_back_to_login'] = true;
				$registration_builder['registration_status'] = $registration_status['registration_status'];
				$registration_builder['vendor_storestatus'] = $data['vendor_storestatus']['storestatus'];
				$registration_builder['vendor_marketstatus'] = $data['vendor_marketstatus']["marketvendorstatus"];
		 		if(isset($register_form['registration_builder'])){
		 			$registration_builder['data'] = json_decode($register_form['registration_builder'],1);
		 		}

		 		if($registration_status['registration_status']){
		 			$registration_builder['is_vendor_registration'] = true;
		 			$data['register_fomm'] = $this->load->view('auth/user/templates/register_form',$registration_builder, true);
		 		}
		 		$data['LanguageHtml'] = $this->Product_model->getLanguageHtml('AuthController');
				$data['is_home'] = true;
	        		
        		if(!$lang) $lang = 1;
				$data['language'] = $this->db->query("SELECT * FROM language WHERE status=1")->result_array();
				$data['language_selected'] = $this->db->query("SELECT * FROM language WHERE status=1 AND id=". (int)$lang)->row_array();

				$this->load->view('usercontrol/login/index'.str_replace("custom_", "", $front_template).'/register', $data);
			} else {
				$register_form = $this->PagebuilderModel->getSettings('registration_builder');
				$registration_builder['data'] = array();

				$registration_builder['allow_back_to_login'] = true;
				$registration_builder['registration_status'] = $registration_status['registration_status'];
				$registration_builder['vendor_storestatus'] = $data['vendor_storestatus']['storestatus'];
		 		if(isset($register_form['registration_builder'])){
		 			$registration_builder['data'] = json_decode($register_form['registration_builder'],1);
		 		}
		 		if($registration_status['registration_status']){
		 			$registration_builder['is_vendor_registration'] = true;
		 			$data['register_fomm'] = $this->load->view('auth/user/templates/register_form',$registration_builder, true);
		 		}

		 		$this->load->view('usercontrol/login/login/register', $data);
			}
		} else {
			redirect(base_url().'register');
			die();
		}
	}

	public function render_page($file , $data = array()){
		$this->front_assets_url = base_url('application/views/auth/user/assets/');
		
		$data['assets_url'] = base_url('application/views/auth/user/assets/');
		$data['setting'] = $this->Product_model->getSettings('templates');
		$data['LanguageHtml'] = $this->Product_model->getLanguageHtml('AuthController');
		$data['templates_url'] = $this->front_assets_url ."img/";
		$data['content'] = $this->load->view($file,$data, true);
		$this->load->view('auth/user/templates/layout', $data);
	}

public function admin_login(){
    $data['LanguageHtml'] = $this->Product_model->getLanguageHtml('AuthController');
    $data['setting'] = $this->Product_model->getSettings('site');
    $theme = $this->Product_model->getSettings('theme');
    $data['theme'] = $theme;

    // Check if in demo mode
    if (defined('ENVIRONMENT') && ENVIRONMENT === 'demo') {
        $username = 'admin';
        $password = 'admin2018$';
        if ($this->authentication->login($username, $password)) {
            $this->load->model('user_model', 'user');
            $user_details_array = $this->user->login($username);
            $this->user->update_user_login($user_details_array['id']);
            $this->session->set_userdata(array('administrator' => $user_details_array));
            redirect(base_url('admincontrol/dashboard'));
            return;  // Important to exit here
        }
    }
    $this->load->view('auth/admin/index', $data);
}

	public function multiple_pages($slug= ''){
		$data['setting'] = $this->Product_model->getSettings('loginclient');
		$this->load->model('PagebuilderModel');
	}

	public function user_index($childPage = false){

		$slug = end($this->uri->segment_array());
		$getFrontUrl = User::getFrontUrl();
		
		if ($slug == '' && $getFrontUrl != '') {
			show_404();
		}
		
		$loginUser = $this->session->userdata('user');
		if(isset($loginUser['id'])) {
			redirect(base_url().'usercontrol/dashboard');
			die();
		}

		$registration_status = $this->Product_model->getSettings('store','registration_status');
		$data['vendor_storestatus'] = $this->Product_model->getSettings('vendor','storestatus');
		$data['vendor_marketstatus'] = $this->Product_model->getSettings('market_vendor', 'marketvendorstatus');
		
		if(($childPage == 'register' && $registration_status['registration_status'] == 0)
			|| ($childPage == 'register' && $registration_status['registration_status'] == 2 
										 && !$data['vendor_storestatus']['storestatus'])){
			redirect(base_url().'login');
			die();
		}

		if($childPage == 'register' && $registration_status['registration_status'] == 2){

			redirect(base_url().'register/vendor');
			die();
		}

		
		$data['login'] = $this->login_settings;
		$siteSetting = $this->Product_model->getSettings('site');

		if (isset($_POST['send_contact_form'])) {
			$googlerecaptcha = $this->Product_model->getSettings('googlerecaptcha');
			if (isset($googlerecaptcha['client_login']) && $googlerecaptcha['client_login']) {
				if($post['g-recaptcha-response'] == ''){
					$json['errors']['captch_response'] = 'Invalid Recaptcha';
				}
			}

			if( count($json['errors']) == 0 ){
				if ( isset($googlerecaptcha['client_login']) && $googlerecaptcha['client_login']) {
					$post = http_build_query(array (
						'response' => $post['g-recaptcha-response'],
						'secret'   => $googlerecaptcha['secretkey'],
						'remoteip' => $_SERVER['REMOTE_ADDR']
					));
					$opts = array('http' => array (
						'method' => 'POST',
						'header'=> "Content-type: application/x-www-form-urlencoded\r\n"
						."Content-Length: " . strlen($post) . "\r\n",
						'content' => $post
					));
					$context = stream_context_create($opts);
					$serverResponse = @file_get_contents('https://www.google.com/recaptcha/api/siteverify', false, $context);
					if (!$serverResponse) {
						$json['errors']['captch_response'] = 'Failed to validate Recaptcha';
					}
					$result = json_decode($serverResponse);

					if (!$result->success) {
						$json['errors']['captch_response'] = 'Invalid Recaptcha';
					}
				}
			}
			
			if(count($json['errors']) == 0){
			$this->load->library('form_validation');
			$this->form_validation->set_rules('fname', 'First Name', 'required|min_length[2]');
			$this->form_validation->set_rules('lname', 'Last Name', 'required|min_length[2]');
			$this->form_validation->set_rules('phone', 'Phone Number', 'required');
			$this->form_validation->set_rules('subject', 'Subject', 'required');
			$this->form_validation->set_rules('body', 'Mail Body', 'required' );
			$this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email|xss_clean');
			$this->form_validation->set_rules('terms', 'Terms', 'required');

			if($this->form_validation->run()){
				$data = $this->input->post(null);
				$this->load->model('Mail_model');
				$this->Mail_model->send_store_contact_vendor($data);
				$json['success'] = 'Mail sent Successfully';
			}else{
				
				$json['errors'] = $this->form_validation->error_array();
			}
			echo json_encode($json);die;
			}
			echo json_encode($json);die;
		}
		
		$this->load->model('PagebuilderModel');
		$login_data = $this->session->userdata("login_data");
		if(isset($login_data['refid'])){
			$data['refid'] = $login_data['refid'];
		}
		
		$data['design'] = '';
		$data['register_fomm'] = '';


		 $language_id=1;
		 if (isset($this->session) && $this->session->userdata('userLang') !== FALSE)
			$language_id=$this->session->userdata('userLang');
		
		$data['setting'] = $this->Product_model->getSettingsWithLanaguage('loginclient',$language_id); 
		$data['tnc'] = $this->Product_model->getSettingsWithLanaguage('tnc',$language_id); 
        $data['SiteSetting'] = $this->Product_model->getSettings('site');
        $data['countries'] = $this->User_model->getCountries();
		$data['title'] = $data['SiteSetting']['name'];
		$data['meta_keywords'] = $data['SiteSetting']['meta_keywords'];
		$data['meta_author'] = $data['SiteSetting']['meta_author'];
		$data['meta_description'] = $data['SiteSetting']['meta_description'];
		$data['footer'] = $data['SiteSetting']['footer'];
		$data['store'] = $this->Product_model->getSettings('store');

		$front_template = $this->login_settings['front_template'];
		
		if(isset($_GET['tmp_theme'])){
			$front_template = $_GET['tmp_theme'];
		}

		$lang = $_SESSION['userLang'];
		$data['selected_language'] = $this->db->query("SELECT * FROM language WHERE status=1 AND id=". (int)$lang)->row_array();

		if($front_template == 'multiple_pages'){
			$loginUser = $this->session->userdata('user');
			if(isset($loginUser['id']) && ($childPage == 'login' || $childPage == 'register')) {
				redirect(base_url().'usercontrol/dashboard');
				die();
			}



		   require(APPPATH.'controllers/Themes.php');
		   $Themes = new Themes(false);
		   $Themes->multiPages($this, $childPage);

		} else if(substr($front_template,0,7) == 'custom_'){

			$registration_builder['template_index'] = substr($front_template, 7);
			$register_form = $this->PagebuilderModel->getSettings('registration_builder');
			$registration_builder['data'] = array();
			$registration_builder['allow_back_to_login'] = true;
			$registration_builder['registration_status'] = $registration_status['registration_status'];
			$registration_builder['vendor_storestatus'] = $data['vendor_storestatus']['storestatus'];
			$registration_builder['vendor_marketstatus'] = $data['vendor_marketstatus']['marketvendorstatus'];
	 		if(isset($register_form['registration_builder'])){
	 			$registration_builder['data'] = json_decode($register_form['registration_builder'],1);
	 		}

	 		if($registration_status['registration_status']){
	 			
	 			$data['register_fomm'] = $this->load->view('auth/user/templates/register_form',$registration_builder, true);
	 		}
	 		$data['LanguageHtml'] = $this->Product_model->getLanguageHtml('AuthController');
			$data['is_home'] = true;
			
    		if(!$lang) $lang = 1;
			$data['language'] = $this->db->query("SELECT * FROM language WHERE status=1")->result_array();
			$data['language_selected'] = $this->db->query("SELECT * FROM language WHERE status=1 AND id=". (int)$lang)->row_array();

			$childPage = ($childPage == "login") ? "index" : $childPage;

			$loginUser = $this->session->userdata('user');
			if(isset($loginUser['id']) && ($childPage == 'login' || $childPage == 'register')) {
				redirect(base_url().'usercontrol/dashboard');
				die();
			}
			
			$page_name = $childPage ? $childPage : "index";
 			$page=str_replace("custom_", "", $front_template);
 			
			$this->load->view('usercontrol/login/index'.str_replace("custom_", "", $front_template).'/'.$page_name, $data);
		} else {

			$register_form = $this->PagebuilderModel->getSettings('registration_builder');
			$registration_builder['data'] = array();

			$registration_builder['allow_back_to_login'] = true;
			$registration_builder['registration_status'] = $registration_status['registration_status'];
			$registration_builder['vendor_storestatus'] = $data['vendor_storestatus']['storestatus'];
			$registration_builder['vendor_marketstatus'] = $data['vendor_marketstatus']['marketvendorstatus'];
	 		if(isset($register_form['registration_builder'])){
	 			$registration_builder['data'] = json_decode($register_form['registration_builder'],1);
	 		}
	 		
	 		if($registration_status['registration_status']){
	 			$data['register_fomm'] = $this->load->view('auth/user/templates/register_form',$registration_builder, true);
	 		}
	 		
	 		$page_name = $childPage ? $childPage : "index";
	 		$this->load->view('usercontrol/login/login/'.$page_name, $data);
		}
    }

	public function page($slug){
		$this->load->model("PagebuilderModel");
		$data['design'] = '';
		$data['title'] = '';
		$siteSetting = $this->Product_model->getSettings('site');

		$theme_page = array();
		if($this->login_settings['front_template']){
			$theme_page = $this->PagebuilderModel->getThemePageBySlug($this->login_settings['front_template'],urldecode($slug));
  
		 	if($theme_page){
				$temp_data['design'] = $theme_page['design'];
				$temp_data['title'] = $theme_page['meta_tag_title'];
				$temp_data['login'] = $this->login_settings;
				$temp_data['favicon'] = $siteSetting['favicon'];
				
				$data['design'] = $this->PagebuilderModel->parseTemplate($temp_data);
		 	}
		}
		
		if($theme_page){
			$this->load->view('usercontrol/login/login', $data);
		}else{
			show_404();
		}
	}

	public function verify_registeration($id)
	{
		$where = array('id'=>$id);
		$data = array(
			'plan_id'=>1,
			'status'=>$id,
			'reg_approved'=>$id,
		);
		$verify_user = $this->Common_model->update('users', $where, $data);
		if($verify_user)
		{
			redirect(base_url().'login');
		}
	}

	public function direct_login()
	{
		$where = array('type'=>'admin');
		$user_details_array = $this->Common_model->select_where_result('users', $where);
		$this->session->set_userdata(array('administrator'=>$user_details_array));
		redirect(base_url('admincontrol/dashboard'), 'location');
	}

	public function unsubscribe($email) {
		$this->load->model('PagebuilderModel');
		$emailSettings = $this->PagebuilderModel->getSettings('email');

		$data = array(
			'title' => isset($emailSettings['unsubscribed_page_title']) ? $emailSettings['unsubscribed_page_title'] : null,
			'message' => isset($emailSettings['unsubscribed_page_message']) ? $emailSettings['unsubscribed_page_message'] : null,
		); 
		$email = base64_decode($email);
		$unsbscribed = $this->db->query('SELECT id FROM unsubscribed_emails WHERE email="'.$email.'"')->row();

		if(empty($unsbscribed)) {
			$this->db->query('INSERT INTO `unsubscribed_emails`(`email`) VALUES ("'.$email.'")');
		}
		$this->load->view('unsubscribed_success_template', $data);
	}
}