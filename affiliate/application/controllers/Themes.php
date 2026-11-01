<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Themes extends MY_Controller {
	function __construct($default = true) {
		if($default){
			parent::__construct();
			$this->load->model('user_model', 'user');
			$this->load->model('Product_model');
			$this->load->model('theme');
			$this->load->helper('share');
			$this->load->library('user_agent');
			$this->Product_model->ping($this->session->userdata('administrator')['id']);
			if(!$this->userdetails()){ redirect($this->admin_domain_url, 'refresh');}
			___construct(1);
			$this->checkSessionTimeout();
		}
	}

	public function userdetails(){ return $this->session->userdata('administrator'); }
	
	public function multiPages($ci, $childPage, $is_vendor_registration = false){
		
		$language_id=1;
		if (isset($_SESSION['userLang']) && $_SESSION['userLang'] !== FALSE)
			$language_id=$_SESSION['userLang'];

		$data['theme_sliders'] = $ci->theme->getSliders(0,$language_id);
		$data['theme_sections'] = $ci->theme->getSections(0,$language_id);
		$data['theme_recommendation'] = $ci->theme->getRecommendation(0,$language_id);
		$data['theme_homecontent'] = $ci->theme->getHomeContent(0,$language_id);
		$data['theme_videos'] = $ci->theme->getVideos(0,$language_id);
		$data['theme_pages'] = $ci->theme->getPages(0,$language_id);
		$data['theme_links'] = $ci->theme->get_links(true,$language_id);

		$data['footer_menu'] = [
			'menu_1' => [],
			'menu_2' => [],
			'menu_3' => [],
			'menu_4' => []
		];
  
		foreach($data['theme_pages'] as $page){
			if ($page->status==1 && $page->link_footer_section>0) {
				$page_name=$page->page_type=='editable' ? $page->page_name : __('front.'.$page->page_name);
				$page_slug=$page->page_type=='editable' ? base_url("p/".$page->slug) : base_url($page->slug);
			
				$data['footer_menu']['menu_'.$page->link_footer_section][] = array('title'=> $page_name,'url'=>$page_slug,'target_blank'=> 0);
			}
		}

 
		foreach($data['theme_links'] as $link){
			$data['footer_menu']['menu_'.$link->tlink_position][] = array('title'=> $link->tlink_title,'url'=> $link->tlink_url,'target_blank'=> $link->tlink_target_blank);
		}



		$data['theme_settings'] = $ci->theme->getSettings($language_id);
		$data['store'] = $ci->Product_model->getSettings('store');
		$data['site_setting'] = $ci->Product_model->getSettings('site');
		
		$data['title'] = $data['site_setting']['name'];
		$data['meta_keywords'] = $data['site_setting']['meta_keywords'];
		$data['meta_author'] = $data['site_setting']['meta_author'];
		$data['meta_description'] = $data['site_setting']['meta_description'];
		$data['footer'] = $data['site_setting']['footer'];
		
		$data['theme_faqs'] = $ci->theme->get_faq(0,$language_id);
		$data['home_sections_settings'] = $ci->theme->getThemeHomeSectionsSettings();

		$data['header_menus'] = $ci->theme->getHeaderMenus($language_id);

		$data['LanguageHtml'] = $ci->Product_model->getLanguageHtml('AuthController');
		$register_form = $ci->PagebuilderModel->getSettings('registration_builder');
		$registration_builder['data'] = array();

		$registration_builder['allow_back_to_login'] = true;
		if(isset($register_form['registration_builder'])){
			$registration_builder['data'] = json_decode($register_form['registration_builder'],1);
		}

		$data['vendor_storestatus'] = $ci->Product_model->getSettings('vendor','storestatus');
		$data['vendor_marketstatus'] = $ci->Product_model->getSettings('market_vendor', 'marketvendorstatus');
		$registration_builder['vendor_storestatus'] = $data['vendor_storestatus']['storestatus'];
		$registration_builder['vendor_marketstatus'] = $data['vendor_marketstatus']['marketvendorstatus'];
		$registration_builder['registration_status'] = $data['store']['registration_status'];

		if($data['store']['registration_status']){
			$registration_builder['tnc_link'] = base_url('/terms-of-use');
			$registration_builder['is_vendor_registration'] = $is_vendor_registration;
			$data['register_fomm'] = $ci->load->view('auth/user/templates/register_form',$registration_builder, true);
		}
		
		if ($childPage) {
			$fixed_pages= ['register','register','login','contact','forget-password','terms-of-use','page'];
			$theme_page_data = $ci->theme->get_page_data_by_slug($childPage);
			if($theme_page_data->page_type=='editable' && $theme_page_data->language_id!=$language_id)
				redirect(base_url('/'));

			if (!empty($theme_page_data->page_id) && $theme_page_data->page_type!='fixed') {
				if (!in_array($childPage, $fixed_pages)) {
					$data['page_data'] = $theme_page_data;
					$childPage = "daynamic-page";
				}
			}
		}

		if ($childPage) {
			switch ($childPage) {
				case 'page':
				$ci->load->view('usercontrol/login/multiple_pages/page', $data);
				break;
				case 'register':
				$ci->load->view('usercontrol/login/multiple_pages/register', $data);
				break;
				case 'login':
				$ci->load->view('usercontrol/login/multiple_pages/login', $data);
				break;
				case 'faq':
				$ci->load->view('usercontrol/login/multiple_pages/faq',$data);
				break;
				case 'contact':
				$ci->load->view('usercontrol/login/multiple_pages/contact', $data);
				break;
				case 'forget-password':
				$ci->load->view('usercontrol/login/multiple_pages/forget_password', $data);
				break;
				case 'terms-of-use':
				$data['page_term'] = $ci->Product_model->getSettings('tnc');
				$ci->load->view('usercontrol/login/multiple_pages/terms_of_use', $data);
				break;
				case 'daynamic-page':
				$ci->load->view('usercontrol/login/multiple_pages/page', $data);
				break;
				
				default: show_404(); break;
			}

			return true;
		}

		$ci->load->model('Theme_setting');

		$data['theme_multiple_page_settings'] = $ci->Theme_setting->getByType('theme_multiple_page');
		$ci->load->view('usercontrol/login/multiple_pages/index', $data);
	}
	//code is start from this point
	public function multiple_theme(){
		$this->load->model("Theme");

		$commonSetting = ['theme'];

		$data['theme_sliders'] = $this->Theme->getSliders();
		$data['theme_sections'] = $this->Theme->getSections();
		$data['theme_recommendation'] = $this->Theme->getRecommendation();
		$data['theme_homecontent'] = $this->Theme->getHomeContent();
		$data['theme_videos'] = $this->Theme->getVideos();
		$data['theme_pages'] = $this->Theme->getpages();
		$data['theme_links'] = $this->Theme->get_links();
		$data['theme_settings'] = $this->Theme->getSettings();
		$data['theme_faqs'] = $this->Theme->get_faq();
		$data['home_sections_settings'] = $this->Theme->getThemeHomeSectionsSettings();

		foreach ($commonSetting as $key => $value) {
			$data[$value] 	= $this->Product_model->getFrontThemeSettings($value);
		}

		$this->load->model('Theme_setting');

		$data['theme_multiple_page_settings'] = $this->Theme_setting->getByType('theme_multiple_page');

		$data['languages'] = $this->db->query("SELECT * FROM language where status=1")->result_array();
		$this->view($data,'theme/multiple_pages_theme');
	}

	public function store_theme_multiple_page_settings ()
	{

		if(!$this->session->administrator) {
			die(__('admin.unauthorized'));
		}

		$post = $this->input->post(null,true);
		$post = $this->security->xss_clean($post);
	
		$this->load->model('Theme_setting');

		foreach($post['theme_multiple_page'] as $key => $value) {
			$this->Theme_setting->create_or_update('theme_multiple_page', $key, $value);
		}
	}
	

	public function store_link() {
		$this->load->model("theme");
		try {
			$linkData = $this->input->post(null,true);
			$linkData = $this->security->xss_clean($linkData);
			$this->form_validation->set_rules('tlink_title', 'Title', 'required');
			$this->form_validation->set_rules('tlink_url', 'Link URL', 'required');
			if($this->form_validation->run()){
				$data = array (
					'tlink_title' => $linkData['tlink_title'],  
					'tlink_url' => $linkData['tlink_url'],  
					'tlink_status' => $linkData['tlink_status'],  
					'tlink_position' => $linkData['tlink_position'],  
					'tlink_target_blank' => $linkData['tlink_target_blank'],
					'language_id' => $linkData['language_id'],  
				);
				if(isset($linkData['tlink_id']) && $linkData['tlink_id'] != 0 && $linkData['tlink_id'] != '') {
					$dbResult = $this->theme->update_tlink($linkData['tlink_id'], $data);
				} else {
					$dbResult = $this->theme->create_tlink($data);
				}

				if($dbResult) {
					$result = ['status' => true, 'message' => 'Link has been saved successfully!', 'data' => $this->theme->get_links()];
				} else {
					$result = ['status' => false, 'message' => 'Something went wrong, please try again!', 'data' => $this->theme->get_links()];
				}
			} else {
				$result = ['status' => false, 'message' => 'Title and URL are should not be empty!', 'data' => $this->theme->get_links()];
			}
		} catch (\Throwable $th) {
			$result = ['status' => false, 'message' => $th->getMessage(), 'data' => $this->theme->get_links()];
		}

		echo json_encode($result);
	}

	public function tlink_status_toggle() {
		$this->load->model("theme");
		$linkData = $this->input->post(null,true);
		$this->theme->update_tlink($linkData['tlink_id'], array(
			'tlink_status' => $linkData['tlink_status']
		));
	}

	public function delete_link(){
		$this->load->model("theme");
		$linkData = $this->input->post(null,true);
		echo $this->theme->delete_tlink($linkData['tlink_id']);
	}

	public function add_new_page(){

		$data['languages'] = $this->db->query("SELECT * FROM language where status=1")->result_array();
		$data['dropdown_menus'] = $this->theme->get_dropdown_parent_menus(); 

		$this->view($data,'theme/add_new_page');
	}


	public function add_new_section(){
		
		$data['languages'] = $this->db->query("SELECT * FROM language where status=1")->result_array();
		$this->view($data,'theme/add_section');
	}

	public function add_new_slider(){
		$data['languages'] = $this->db->query("SELECT * FROM language where status=1")->result_array();
		$this->view($data,'theme/add_slider');
	}
	
	public function add_new_recommendation(){
		$data['languages'] = $this->db->query("SELECT * FROM language where status=1")->result_array();
		$this->view($data,'theme/add_recommendation');
	}

	public function add_new_faq(){
		$this->load->model("Theme");
		$data['languages'] = $this->db->query("SELECT * FROM language where status=1")->result_array();
		$data['new_position'] = $this->Theme->getNewPosition('theme_faq');

		$this->view($data,'theme/faq_form');
	}

	public function add_new_homecontent(){
		
		$data['languages'] = $this->db->query("SELECT * FROM language where status=1")->result_array();
		$this->view($data,'theme/add_homeContent');
	}

	public function add_new_video(){
		
		$data['languages'] = $this->db->query("SELECT * FROM language where status=1")->result_array();
		$this->view($data,'theme/add_video');
	}
	
	public function save_slider(){

		if ($this->input->server('REQUEST_METHOD') == 'POST'){

			$form_data = $this->input->post(null,true);
			$form_data = $this->security->xss_clean($form_data);
			$this->load->library('form_validation');

			$this->form_validation->set_rules('title', 'Title', 'required');
			$this->form_validation->set_rules('description', 'description', 'required');
			$this->form_validation->set_rules('link', 'Link', 'required');
			$this->form_validation->set_rules('button_text', 'button text', 'required');
			if (empty($_FILES['avatar']['name']))
			{
				$this->form_validation->set_rules('avatar', 'Image ', 'required');
			}
			if ($this->security->xss_clean($_FILES['avatar']['name'], true) === FALSE)
			{
				$this->form_validation->set_message('avatar', 'Image contain invalid data.');
			}
			if($this->form_validation->run()){
				$errors= array();

				$avatar = $data['user']->avatar;
				if(!empty($_FILES['avatar']['name'])){
					$upload_response = $this->upload_photo('avatar','assets/images/theme_images/');
					if($upload_response['success']){
						$avatar = $upload_response['upload_data']['file_name'];
					}
					else{
						$json['errors']['avatar'] = $upload_response['msg'];
					}
				}


				if(!isset($json['errors'])){

					 $this->theme->save_slider_into_db($form_data,$avatar); // sending data to model
	                 // now return to back
	                 $this->session->set_flashdata('success', 'Slider added successfully'); // set msg which you want display 
	                 $json['location'] = base_url('themes/multiple_theme');
	             }

	         } else{
	         	$json['errors'] = $this->form_validation->error_array();
	         }

	         echo json_encode($json);die;

	     }
	 }

	 public function save_section(){

	 	if ($this->input->server('REQUEST_METHOD') == 'POST'){

	 		$form_data = $this->input->post(null,true);
	 		$form_data = $this->security->xss_clean($form_data);
	 		$this->load->library('form_validation');

	 		$this->form_validation->set_rules('title', 'Title', 'required');
	 		$this->form_validation->set_rules('description', 'description', 'required');
	 		$this->form_validation->set_rules('link', 'Link', 'required');
	 		$this->form_validation->set_rules('button_text', 'button text', 'required');
	 		$this->form_validation->set_rules('position' , 'Position' , 'required');
	 		if (empty($_FILES['avatar']['name']))
	 		{
	 			$this->form_validation->set_rules('avatar', 'Image ', 'required');
	 		}
	 		if ($this->security->xss_clean($_FILES['avatar']['name'], true) === FALSE)
	 		{
	 			$this->form_validation->set_message('avatar', 'Image contain invalid data.');
	 		}
	 		if($this->form_validation->run()){
	 			$errors= array();

	 			$avatar = $data['user']->avatar;
	 			if(!empty($_FILES['avatar']['name'])){
	 				$upload_response = $this->upload_photo('avatar','assets/images/theme_images/');
	 				if($upload_response['success']){
	 					$avatar = $upload_response['upload_data']['file_name'];
	 				}
	 				else{
	 					$json['errors']['avatar'] = $upload_response['msg'];
	 				}
	 			}


	 			if(!isset($json['errors'])){

					 $this->theme->save_section_into_db($form_data,$avatar); // sending data to model
	                 // now return to back
	                 $this->session->set_flashdata('success', 'Section added successfully'); // set msg which you want display 
	                 $json['location'] = base_url('themes/multiple_theme');
	             }

	         } else{
	         	$json['errors'] = $this->form_validation->error_array();
	         }

	         echo json_encode($json);die;

	     }
	 }

	 public function save_recommendation(){

	 	if ($this->input->server('REQUEST_METHOD') == 'POST'){

	 		$form_data = $this->input->post(null,true);
	 		$form_data = $this->security->xss_clean($form_data);
	 		$this->load->library('form_validation');

	 		$this->form_validation->set_rules('title', 'Title', 'required');
	 		$this->form_validation->set_rules('description', 'description', 'required');
	 		$this->form_validation->set_rules('occupation', 'Occupation', 'required');
	 		if (empty($_FILES['avatar']['name']))
	 		{
	 			$this->form_validation->set_rules('avatar', 'Image ', 'required');
	 		}
	 		if ($this->security->xss_clean($_FILES['avatar']['name'], true) === FALSE)
	 		{
	 			$this->form_validation->set_message('avatar', 'Image contain invalid data.');
	 		}
	 		if($this->form_validation->run()){
	 			$errors= array();

	 			$avatar = $data['user']->avatar;
	 			if(!empty($_FILES['avatar']['name'])){
	 				$upload_response = $this->upload_photo('avatar','assets/images/theme_images/');
	 				if($upload_response['success']){
	 					$avatar = $upload_response['upload_data']['file_name'];
	 				}
	 				else{
	 					$json['errors']['avatar'] = $upload_response['msg'];
	 				}
	 			}


	 			if(!isset($json['errors'])){
	 				$this->theme->save_recommendation_into_db($form_data,$avatar);
	 				$this->session->set_flashdata('success', 'Recommendation added successfully');
	 				$json['location'] = base_url('themes/multiple_theme');
	 			}

	 		} else{
	 			$json['errors'] = $this->form_validation->error_array();
	 		}

	 		echo json_encode($json);die;

	 	}
	 }


	 public function save_homecontent(){

	 	if ($this->input->server('REQUEST_METHOD') == 'POST'){

	 		$form_data = $this->input->post(null,true);
	 		$form_data = $this->security->xss_clean($form_data);
	 		$this->load->library('form_validation');

	 		$this->form_validation->set_rules('title', 'Title', 'required');
	 		$this->form_validation->set_rules('description', 'description', 'required');
	 		if (empty($_FILES['avatar']['name']))
	 		{
	 			$this->form_validation->set_rules('avatar', 'Image ', 'required');
	 		}
	 		if ($this->security->xss_clean($_FILES['avatar']['name'], true) === FALSE)
	 		{
	 			$this->form_validation->set_message('avatar', 'Image contain invalid data.');
	 		}

	 		$form_data = array(
	 			'title'                 => $this->input->post('title',true),
	 			'description'           => $this->input->post('description',false),
	 			'status'                => $this->input->post('status',true),
	 			'image'                 => $avatar,
	 			'language_id'			=> $this->input->post('language_id',true)
	 		);
 

	 		if($this->form_validation->run()){
	 			$errors= array();
	 			$avatar = $data['user']->avatar;
	 			if(!empty($_FILES['avatar']['name'])){
	 				$upload_response = $this->upload_photo('avatar','assets/images/theme_images/');
	 				if($upload_response['success']){
	 					$avatar = $upload_response['upload_data']['file_name'];
	 				}
	 				else{
	 					$json['errors']['avatar'] = $upload_response['msg'];
	 				}
	 			}

	 			if(!isset($json['errors'])){
	 				$this->theme->save_homecontent_into_db($form_data,$avatar);
	 				$this->session->set_flashdata('success', 'Content added successfully');
	 				$json['location'] = base_url('themes/multiple_theme');
	 			}
	 		} 
	 		else
	 		{
	 			$json['errors'] = $this->form_validation->error_array();
	 		}
	 		echo json_encode($json);die;
	 	}
	 }

	 public function save_page(){

	 	if ($this->input->server('REQUEST_METHOD') == 'POST'){

	 		$form_data = $this->input->post(null,true);
	 		$form_data = $this->security->xss_clean($form_data);
	 		$this->load->library('form_validation');

	 		$this->form_validation->set_rules('page_name', 'Page Name', 'required');
	 		$this->form_validation->set_rules('top_banner_title', 'Top Banner Title', 'required');
	 		$this->form_validation->set_rules('top_banner_sub_title', 'Top Banner Sub Title', 'required');
	 		$this->form_validation->set_rules('page_content_title', 'Page Content Title', 'required');
	 		$this->form_validation->set_rules('page_content', 'Page Content', 'required');

	 		if(!empty($_FILES['page_banner_image']['name'])){
	 			$upload_response = $this->upload_photo('page_banner_image','assets/images/theme_images/');
	 			$page_banner_image = $upload_response['success'] ? $upload_response['upload_data']['file_name'] : null; 
	 		}

	 		$form_data = array(
	 			'link_footer_section' 	=> $this->input->post('link_footer_section',true),
	 			'page_name' 			=> $this->input->post('page_name',true),
	 			'top_banner_title' 		=> $this->input->post('top_banner_title',true),
	 			'top_banner_sub_title' 	=> $this->input->post('top_banner_sub_title',true),
	 			'page_content_title' 	=> $this->input->post('page_content_title',true),
	 			'page_content' 			=> $this->input->post('page_content',false),
	 			'is_header_menu' 		=> $this->input->post('is_header_menu',true),
	 			'is_header_dropdown' 	=> $this->input->post('is_header_dropdown',true),
	 			'parent_id' 			=> $this->input->post('parent_id',true),
	 			'status' 				=> $this->input->post('status',true),
	 			'page_banner_image' 	=> $page_banner_image,
	 			'language_id' 			=> $this->input->post('language_id',true),
	 		);

	 		if($this->form_validation->run()){
	 			$errors= array();

	 			if(!isset($json['errors'])){
	 				$this->theme->save_page_into_db($form_data);
					// $this->save_routes();
	 				$this->session->set_flashdata('success', 'Page added successfully');

	 				print(json_encode(array("status"=>"success","message"=>"page successfully added")));
	 				die();
	 			}
	 		}
	 		else
	 		{
	 			print(json_encode(array("status"=>"danger","message"=>"Page Content is Required!")));
	 			die();
	 		}
	 	}
	 }


	 public function update_page(){

	 	$post 		= $this->input->post(null,true);
	 	$post 		= $this->security->xss_clean($post);
	 	$page_id 	= $this->security->xss_clean((int)$this->input->post("page_id",true));

	 	$pageInfo 	= $this->theme->get_page_data_byid($page_id);

	 	$this->load->library('form_validation');

	 	$this->form_validation->set_rules('page_name', 'Page Name', 'required');

	 	if ($pageInfo->page_type!='fixed') {
	 		$this->form_validation->set_rules('top_banner_title', 'Top Banner Title', 'required');
	 		$this->form_validation->set_rules('top_banner_sub_title', 'Top Banner Sub Title', 'required');
	 		$this->form_validation->set_rules('page_content_title', 'Page Content Title', 'required');
	 		$this->form_validation->set_rules('page_content', 'Page Content', 'required');
	 	}


	 	if(!empty($_FILES['page_banner_image']['name'])){
	 		$upload_response = $this->upload_photo('page_banner_image','assets/images/theme_images/');
	 		$page_banner_image = $upload_response['success'] ? $upload_response['upload_data']['file_name'] : null; 
	 	}

	 	$pageArray = array(
	 		'link_footer_section' 	=> $this->input->post('link_footer_section',true),
	 		'page_name'             => $this->input->post('page_name',true),
	 		'top_banner_title'      => $this->input->post('top_banner_title',true),
	 		'top_banner_sub_title'  => $this->input->post('top_banner_sub_title',true),
	 		'page_content_title'    => $this->input->post('page_content_title',true),
	 		'page_content'          => $this->input->post('page_content',false),
	 		'is_header_menu'    	=> $this->input->post('is_header_menu',true),
	 		'is_header_dropdown'    => $this->input->post('is_header_dropdown',true),
	 		'parent_id'    			=> $this->input->post('parent_id',true),
	 		'status'                => $this->input->post('status',true),
	 		'page_banner_image' 	=> $page_banner_image,
	 		'language_id' 			=> $this->input->post('language_id',true),
	 		'page_type' 			=> $this->input->post('page_type',true),
	 	);


	 	if($this->form_validation->run()){
	 		$errors= array();

	 		if(!isset($json['errors'])){

	 			$this->theme->update_page_data($page_id, $pageArray);
	 			$this->session->set_flashdata('success', 'Page Updated successfully');

	 			print(json_encode(array("status"=>"success","message"=>"page successfully updated")));
	 			die();
	 		}
	 	}
	 	else
	 	{
	 		print(json_encode(array("status"=>"danger","message"=>"Oops! An Error Occured")));
	 		die();
	 	}
	 }

	 public function save_video(){

	 	if ($this->input->server('REQUEST_METHOD') == 'POST')
	 	{

	 		$form_data = $this->input->post(null,true);
	 		$form_data = $this->security->xss_clean($form_data);
	 		$this->load->library('form_validation');

	 		$this->form_validation->set_rules('video_title', 'Video Title', 'required');
	 		$this->form_validation->set_rules('video_sub_title', 'Video Sub Title', 'required');
	 		$this->form_validation->set_rules('video_link', 'Video Link', 'required');

	 		if($this->form_validation->run())
	 		{
	 			$videoArray = array(
	 			'video_title'             => $this->input->post('video_title',true),
	 			'video_sub_title'         => $this->input->post('video_sub_title',true),
	 			'video_link'              => $this->input->post('video_link',true),
	 			'status'                  => $this->input->post('status',true),
	 			);
	 			
	 			$this->theme->save_video_into_db($form_data);
	 			$this->session->set_flashdata('success', 'Video added successfully');
	 			$json['location'] = base_url('themes/multiple_theme');
		 	}
		 	else
		 	{
		 		$json['errors'] = $this->form_validation->error_array();
		 	}
	 	}
	 	else
	 	{
	 		$json['errors'] = $this->form_validation->error_array();
	 	}

	 	echo json_encode($json);die;

	 }

	 public function delete_faq($faq) { 
	 	$this->theme->delete_faq($faq);
	 	$this->session->set_flashdata('success', 'FAQ deleted successfully');
	 	redirect('/themes/multiple_theme');
	 }

	 public function delete_section($section_id) { 

	 	$this->theme->delete_section($section_id);
	 	$this->session->set_flashdata('success', 'Section deleted successfully');

	 	redirect('/themes/multiple_theme');
	 }

	 public function theme_delete($slider_id) { 

	 	$this->theme->delete_slider($slider_id);
	 	$this->session->set_flashdata('success', 'Slider deleted successfully');

	 	redirect('/themes/multiple_theme');
	 }

	 public function delete_recommendation($recommendation_id) { 

	 	$this->theme->delete_recommendation($recommendation_id);
	 	$this->session->set_flashdata('success', 'Recommendation deleted successfully');

	 	redirect('/themes/multiple_theme');
	 }

	 public function delete_page($page_id) { 
	 	$this->theme->delete_page($page_id);
	 	print(json_encode(array("status" => "success")));
	 	die();
	 }

	 public function update_page_status(){
	 	$post = $this->input->post(null,true);
	 	$page_id = $post['id'];
	 	$update_status = $post['status'];
	 	$this->theme->update_page_status($page_id,$update_status);
	 	print(json_encode(array("status" => "success")));
	 	die();
	 }

	 public function delete_homecontent($homecontent_id) { 

	 	$this->theme->delete_homecontent($homecontent_id);
	 	$this->session->set_flashdata('success', 'Content deleted successfully');

	 	redirect('/themes/multiple_theme');
	 }

	 public function delete_video($video_id) { 

	 	$this->theme->delete_video($video_id);
	 	$this->session->set_flashdata('success', 'Video deleted successfully');

	 	redirect('/themes/multiple_theme');
	 }

	 public function edit_slider($slider_id){

	 	$data['languages'] = $this->db->query("SELECT * FROM language where status=1")->result_array();
	 	$data['slider'] = $this->theme->get_slider_data_byid($slider_id);
	 	$this->load->view('admincontrol/includes/header',$data); 
	 	$this->load->view('admincontrol/includes/sidebar', $data);
	 	$this->load->view('admincontrol/includes/topnav',$data);
	 	$this->load->view('admincontrol/theme/edit_slider',$data);
	 	$this->load->view('admincontrol/includes/footer',$data);
	 }

	 public function edit_faq($faq_id){

	 	$data['languages'] = $this->db->query("SELECT * FROM language where status=1")->result_array();
	 	$data['faq'] = $this->theme->get_faq($faq_id);
	 	$this->load->view('admincontrol/includes/header',$data); 
	 	$this->load->view('admincontrol/includes/sidebar', $data);
	 	$this->load->view('admincontrol/includes/topnav',$data);
	 	$this->load->view('admincontrol/theme/faq_form',$data);
	 	$this->load->view('admincontrol/includes/footer',$data);
	 }

	 public function edit_section($section_id){

	 	$data['languages'] = $this->db->query("SELECT * FROM language where status=1")->result_array();
	 	$data['section'] = $this->theme->get_section_data_byid($section_id);
	 	$this->load->view('admincontrol/includes/header',$data); 
	 	$this->load->view('admincontrol/includes/sidebar', $data);
	 	$this->load->view('admincontrol/includes/topnav',$data);
	 	$this->load->view('admincontrol/theme/edit_section',$data);
	 	$this->load->view('admincontrol/includes/footer',$data);
	 }

	 public function edit_recommendation($recommendation_id){
	 	$data['languages'] = $this->db->query("SELECT * FROM language where status=1")->result_array();
	 	$data['recommendation'] = $this->theme->get_recommendation_data_byid($recommendation_id);
	 	$this->load->view('admincontrol/includes/header',$data); 
	 	$this->load->view('admincontrol/includes/sidebar', $data);
	 	$this->load->view('admincontrol/includes/topnav',$data);
	 	$this->load->view('admincontrol/theme/edit_recommendation',$data);
	 	$this->load->view('admincontrol/includes/footer',$data);
	 }

	 public function edit_homecontent($homecontent_id){

		$data['languages'] = $this->db->query("SELECT * FROM language where status=1")->result_array();
	 	$data['homecontent'] = $this->theme->get_homecontent_data_byid($homecontent_id);
	 	$this->load->view('admincontrol/includes/header',$data); 
	 	$this->load->view('admincontrol/includes/sidebar', $data);
	 	$this->load->view('admincontrol/includes/topnav',$data);
	 	$this->load->view('admincontrol/theme/edit_homecontent',$data);
	 	$this->load->view('admincontrol/includes/footer',$data);
	 }

	 public function edit_page($page_id){
	 	$data['languages'] = $this->db->query("SELECT * FROM language where status=1")->result_array();
	 	$data['page'] = $this->theme->get_page_data_byid($page_id);
	 	$data['dropdown_menus'] = $this->theme->get_dropdown_parent_menus();

	 	$this->load->view('admincontrol/includes/header',$data); 
	 	$this->load->view('admincontrol/includes/sidebar', $data);
	 	$this->load->view('admincontrol/includes/topnav',$data);
	 	$this->load->view('admincontrol/theme/edit_page',$data);
	 	$this->load->view('admincontrol/includes/footer',$data);
	 }


	 public function edit_video($video_id){
	 	$data['languages'] = $this->db->query("SELECT * FROM language where status=1")->result_array();
	 	$data['video'] = $this->theme->get_video_data_byid($video_id);
	 	$this->load->view('admincontrol/includes/header',$data); 
	 	$this->load->view('admincontrol/includes/sidebar', $data);
	 	$this->load->view('admincontrol/includes/topnav',$data);
	 	$this->load->view('admincontrol/theme/edit_video',$data);
	 	$this->load->view('admincontrol/includes/footer',$data);
	 }

	 public function store_faq(){

	 	$post = $this->input->post(null,true);
	 	$post = $this->security->xss_clean($post);

	 	$this->load->library('form_validation');

	 	$this->form_validation->set_rules('faq_question', 'Question', 'required');
	 	$this->form_validation->set_rules('faq_answer', 'Answer', 'required');

	 	if($this->form_validation->run()){

	 		$data = array(
	 			'faq_theme_id' 				=> !empty($form_data['faq_theme_id']) ? $form_data['faq_theme_id'] : 1,
	 			'faq_question'          => $this->input->post('faq_question',true),
	 			'faq_answer'           => $this->input->post('faq_answer',true),
	 			'position'                  => $this->input->post('position',true),
	 			'status'                => $this->input->post('status',true),
	 			'language_id'           => $this->input->post('language_id',true),
	 		);

	 		$faq_id =  (int)$this->input->post("faq_id",true);
	 		if(!empty($faq_id) && $faq_id != 0) {
	 			$json['status'] = $this->theme->update_faq_data($faq_id,$data);
	 			$json['message'] = "Faq Updated";
	 		} else {
	 			$json['status'] = $this->theme->create_faq_data($data);
	 			$json['message'] = "Faq Created";
	 		}
	 		$this->session->set_flashdata('success', $json['message'].' successfully');
	 		$json['location'] = base_url('themes/multiple_theme');
	 	}else{
	 		$json['errors'] = $this->form_validation->error_array();
	 	}
	 	echo json_encode($json);die;
	 }

	public function update_slider(){

	 	$post = $this->input->post(null,true);
	 	$post = $this->security->xss_clean($post);
	 	$this->load->library('form_validation');

	 	$this->form_validation->set_rules('title', 'Title', 'required');
	 	$this->form_validation->set_rules('description', 'description', 'required');
	 	$this->form_validation->set_rules('link', 'Link', 'required');
	 	$this->form_validation->set_rules('button_text', 'button text', 'required');

	 	if($this->form_validation->run()){
	 		if(!empty($_FILES['avatar']['name'])){
	 			$upload_response = $this->upload_photo('avatar','assets/images/theme_images/');
	 			if($upload_response['success']){
	 				$avatar = $upload_response['upload_data']['file_name'];
	 			}
	 			else{
	 				$json['errors']['avatar'] = $upload_response['msg'];
	 				echo json_encode($json);die;
	 			}
	 		}
	 		
	 		if(isset($avatar) && !empty($avatar)){
	 			$avatar = $avatar;
	 		}else{
	 			$avatar = $this->input->post('hidden_image',true);
	 		}

	 		$sliderArray = array(
	 			'title'                 => $this->input->post('title',true),
	 			'description'           => $this->input->post('description',true),
	 			'link'                  => $this->input->post('link',true),
	 			'button_text'           => $this->input->post('button_text',true),
	 			'status'                => $this->input->post('status',true),
	 			'language_id'                => $this->input->post('language_id',true),
	 			'image'                 => $avatar,
	 		);


	 		$slider_id =  (int)$this->input->post("slider_id",true);

	 		if(!empty($slider_id)) {
	 			$json['status'] = $this->theme->update_slider_data($slider_id,$sliderArray);
	 			$json['message'] = "Slider Updated";
	 		} else {
	 			$json['status'] = $this->theme->create_slider_data($sliderArray);
	 			$json['message'] = "Slider Created";
	 		}
	 		$this->session->set_flashdata('success', 'Slider Updated successfully');
	 		$json['location'] = base_url('themes/multiple_theme');
	 	}else{
	 		$json['errors'] = $this->form_validation->error_array();
	 	}
	 	echo json_encode($json);die;
	 }

	public function update_section(){

	 	$post = $this->input->post(null,true);
	 	$post = $this->security->xss_clean($post);
	 	$this->load->library('form_validation');

	 	$this->form_validation->set_rules('title', 'Title', 'required');
	 	$this->form_validation->set_rules('description', 'description', 'required');
	 	$this->form_validation->set_rules('link', 'Link', 'required');
	 	$this->form_validation->set_rules('button_text', 'button text', 'required');
	 	if($this->form_validation->run()){
	 		if(!empty($_FILES['avatar']['name'])){
	 			$upload_response = $this->upload_photo('avatar','assets/images/theme_images/');
	 			if($upload_response['success']){
	 				$avatar = $upload_response['upload_data']['file_name'];
	 			}
	 			else{
	 				$json['errors']['avatar'] = $upload_response['msg'];
	 				echo json_encode($json);die;
	 			}
	 		}
	 		if(isset($avatar) && !empty($avatar)){
	 			$avatar = $avatar;
	 		}else{
	 			$avatar = $this->input->post('hidden_image',true);
	 		}
	 		$sectionArray = array(
	 			'title'                 => $this->input->post('title',true),
	 			'description'           => $this->input->post('description',true),
	 			'link'                  => $this->input->post('link',true),
	 			'button_text'           => $this->input->post('button_text',true),
	 			'status'                => $this->input->post('status',true),
	 			'position'				=> $this->input->post('position',true),
	 			'image'                 => $avatar,
	 			'language_id'                => $this->input->post('language_id',true),
	 		);
	 		$section_id =  (int)$this->input->post("section_id",true);
	 		$this->theme->update_section_data($section_id,$sectionArray);
	 		$this->session->set_flashdata('success', 'Section Updated successfully');
	 		$json['location'] = base_url('themes/multiple_theme');
	 	}else{
	 		$json['errors'] = $this->form_validation->error_array();
	 	}
	 	echo json_encode($json);die;
	 }

	 public function update_recommendation(){

	 	$post = $this->input->post(null,true);
	 	$post = $this->security->xss_clean($post);
	 	$this->load->library('form_validation');

	 	$this->form_validation->set_rules('title', 'Title', 'required');
	 	$this->form_validation->set_rules('description', 'description', 'required');
	 	$this->form_validation->set_rules('occupation', 'Occupation', 'required');
	 	if($this->form_validation->run()){
	 		if(!empty($_FILES['avatar']['name'])){
	 			$upload_response = $this->upload_photo('avatar','assets/images/theme_images/');
	 			if($upload_response['success']){
	 				$avatar = $upload_response['upload_data']['file_name'];
	 			}
	 			else{
	 				$json['errors']['avatar'] = $upload_response['msg'];
	 				echo json_encode($json);die;
	 			}
	 		}
	 		if(isset($avatar) && !empty($avatar)){
	 			$avatar = $avatar;
	 		}else{
	 			$avatar = $this->input->post('hidden_image',true);
	 		}
	 		$recommendationArray = array(
	 			'title'                 => $this->input->post('title',true),
	 			'description'           => $this->input->post('description',true),
	 			'occupation'            => $this->input->post('occupation',true),
	 			'status'                => $this->input->post('status',true),
	 			'image'                 => $avatar,
	 			'language_id'           => $this->input->post('language_id',true),
	 		);
	 		$recommendation_id =  (int)$this->input->post("recommendation_id",true);
	 		$this->theme->update_recommendation_data($recommendation_id,$recommendationArray);
	 		$this->session->set_flashdata('success', 'Recommendation Updated successfully');
	 		$json['location'] = base_url('themes/multiple_theme');
	 	}else{
	 		$json['errors'] = $this->form_validation->error_array();
	 	}
	 	echo json_encode($json);die;
	 }

	 public function update_homecontent(){

	 	$post = $this->input->post(null,true);
	 	$post = $this->security->xss_clean($post);
	 	$this->load->library('form_validation');

	 	$this->form_validation->set_rules('title', 'Title', 'required');
	 	$this->form_validation->set_rules('description', 'description', 'required');
	 	if($this->form_validation->run()){
	 		if(!empty($_FILES['avatar']['name'])){
	 			$upload_response = $this->upload_photo('avatar','assets/images/theme_images/');
	 			if($upload_response['success']){
	 				$avatar = $upload_response['upload_data']['file_name'];
	 			}
	 			else{
	 				$json['errors']['avatar'] = $upload_response['msg'];
	 				echo json_encode($json);die;
	 			}
	 		}
	 		if(isset($avatar) && !empty($avatar)){
	 			$avatar = $avatar;
	 		}else{
	 			$avatar = $this->input->post('hidden_image',true);
	 		}
	 		$homecontentArray = array(
	 			'title'                 => $this->input->post('title',true),
	 			'description'           => $this->input->post('description',false),
	 			'status'                => $this->input->post('status',true),
	 			'image'                 => $avatar,
	 			'language_id'           => $this->input->post('language_id',true),
	 		);

	 		$homecontent_id =  (int)$this->input->post("homecontent_id",true);
	 		$this->theme->update_homecontent_data($homecontent_id,$homecontentArray);
	 		$this->session->set_flashdata('success', 'Content Updated successfully');
	 		$json['location'] = base_url('themes/multiple_theme');
	 	}else{
	 		$json['errors'] = $this->form_validation->error_array();
	 	}
	 	echo json_encode($json);die;
	 }







	 public function update_video(){

	 	$post = $this->input->post(null,true);
	 	$post = $this->security->xss_clean($post);
	 	$this->load->library('form_validation');

	 	$this->form_validation->set_rules('video_title', 'Video Title', 'required');
	 	$this->form_validation->set_rules('video_sub_title', 'Video Sub Title', 'required');
	 	$this->form_validation->set_rules('video_link', 'Video Link', 'required');

	 	if($this->form_validation->run()){
	 		$videoArray = array(
	 			'video_title'             => $this->input->post('video_title',true),
	 			'video_sub_title'         => $this->input->post('video_sub_title',true),
	 			'video_link'              => $this->input->post('video_link',true),
	 			'status'                  => $this->input->post('status',true),
	 		);
	 		$video_id =  (int)$this->input->post("video_id",true);
	 		$this->theme->update_video_data($video_id,$videoArray);
	 		$this->session->set_flashdata('success', 'Video Updated successfully');
	 		$json['location'] = base_url('themes/multiple_theme');
	 	}else{
	 		$json['errors'] = $this->form_validation->error_array();
	 	}
	 	echo json_encode($json);die;
	 }

	 public function update_about(){

	 	$post = $this->input->post(null,true);
	 	$post = $this->security->xss_clean($post);
	 	$this->load->library('form_validation');

	 	$this->form_validation->set_rules('top_banner_title', 'Top Banner Title', 'required');
	 	$this->form_validation->set_rules('top_banner_sub_title', 'Top Banner Sub Title', 'required');
	 	$this->form_validation->set_rules('about_content_title', 'About Title', 'required');
	 	$this->form_validation->set_rules('about_content', 'About Content', 'required');
	 	if($this->form_validation->run()){
	 		if(!empty($_FILES['avatar']['name'])){
	 			$upload_response = $this->upload_photo('avatar','assets/images/theme_images/');
	 			if($upload_response['success']){
	 				$avatar = $upload_response['upload_data']['file_name'];
	 			}
	 			else{
	 				$json['errors']['avatar'] = $upload_response['msg'];
	 				echo json_encode($json);die;
	 			}
	 		}
	 		if(isset($avatar) && !empty($avatar)){
	 			$avatar = $avatar;
	 		}else{
	 			$avatar = $this->input->post('hidden_image',true);
	 		}

	 		$aboutArray = array(
	 			'top_banner_title'           => $this->input->post('top_banner_title',true),
	 			'top_banner_sub_title'       => $this->input->post('top_banner_sub_title',true),
	 			'about_content_title'        => $this->input->post('about_content_title',true),
	 			'about_content'              => $this->input->post('about_content',true),
	 			'status'                     => $this->input->post('status',true),
	 			'about_image'                => $avatar,
	 		);

	 		$about_id =  (int)$this->input->post("about_id",true);

	 		$this->theme->update_about_data($about_id,$aboutArray);
	 		$this->session->set_flashdata('success', 'About Page Data Updated successfully');
	 		$json['location'] = base_url('themes/multiple_theme');
	 	}else{
	 		$json['errors'] = $this->form_validation->error_array();
	 	}
	 	echo json_encode($json);die;
	 }

	 public function change_homepage_top_menu_positions(){
	 	echo $this->theme->updateThemeHomePageTopMenuPositionSettings($this->input->post('page_id'));
	 }

	 public function change_home_sections_positions(){
	 	echo $this->theme->updateThemeHomeSectionsSettings($this->input->post('sec_id'),$this->input->post('sec_status'));
	 }

	 public function update_settings(){

	 	$post = $this->input->post(null,true);
	 	$post = $this->security->xss_clean($post);
	 	$this->load->library('form_validation');

	 	if ($this->security->xss_clean($_FILES['logo']['name'], true) === FALSE)
	 	{
	 		$this->form_validation->set_message('logo', 'Image contain invalid data.');
	 	}

	 	if ($this->security->xss_clean($_FILES['faq_banner_image']['name'], true) === FALSE)
	 	{
	 		$this->form_validation->set_message('faq_banner_image', 'Image contain invalid data.');
	 	}

	 	if ($this->security->xss_clean($_FILES['contact_banner_image']['name'], true) === FALSE)
	 	{
	 		$this->form_validation->set_message('contact_banner_image', 'Image contain invalid data.');
	 	}

	 	if ($this->security->xss_clean($_FILES['avatar_login']['name'], true) === FALSE)
	 	{
	 		$this->form_validation->set_message('avatar_login', 'Image contain invalid data.');
	 	}

	 	if ($this->security->xss_clean($_FILES['avatar_registration']['name'], true) === FALSE)
	 	{
	 		$this->form_validation->set_message('avatar_registration', 'Image contain invalid data.');
	 	}

	 	if ($this->security->xss_clean($_FILES['avatar_terms']['name'], true) === FALSE)
	 	{
	 		$this->form_validation->set_message('avatar_terms', 'Image contain invalid data.');
	 	}


	 	if(!$this->form_validation->run()){
	 		$json['errors'] = $this->form_validation->error_array();

	 		if(!empty($json['errors'])) {
	 			echo json_encode($json);die;
	 		}

	 	}

	 	$this->theme->updateThemeHomeSectionsSettings($this->input->post('sec_id'),$this->input->post('sec_status'));
	 	$settings_id =  (int)$this->input->post("settings_id",true);
	 	$oldSettings =[];
	 	if($settings_id) {
	 		$oldSettings = $this->theme->getSettingsDetailsObject($settings_id);
	 	}
	 	$settingsArray=[];
		// $homepage_video_section_bg = $data['user']->homepage_video_section_bg;
	 	if(!empty($post['hidden_homepage_video_section_bg']) && !empty($_FILES['homepage_video_section_bg']['name'])){
	 		$upload_response = $this->upload_photo('homepage_video_section_bg','assets/images/theme_images/');
	 		if($upload_response['success']){
	 			$settingsArray['homepage_video_section_bg'] = $homepage_video_section_bg = $upload_response['upload_data']['file_name'];
	 		} else {
	 			$json['errors']['homepage_video_section_bg'] = $upload_response['msg'];
	 			echo json_encode($json);die;
	 		}
	 	} else {
	 		if(empty($post['hidden_homepage_video_section_bg'])){
	 			$settingsArray['homepage_video_section_bg']='';
	 			if(!empty($oldSettings) && !empty($oldSettings->homepage_video_section_bg)) {
	 				if(file_exists('assets/images/theme_images/'.$oldSettings->homepage_video_section_bg)) {
	 					@unlink('assets/images/theme_images/'.$oldSettings->homepage_video_section_bg);
	 				}

	 			}
	 		}
	 	} 
	 	


	 	if(!empty($post['hidden_logo']) && !empty($_FILES['logo']['name'])){
	 		$upload_response = $this->upload_photo('logo','assets/images/theme_images/');
	 		if($upload_response['success']){
	 			$settingsArray['logo'] = $upload_response['upload_data']['file_name']; 
	 		} else {
	 			$json['errors']['logo'] = $upload_response['msg'];
	 			echo json_encode($json);die;
	 		}
	 	} else {
			// Actul FileDlete  also add here 
	 		if(empty($post['hidden_logo'])){
	 			$settingsArray['logo']='';
	 			if(!empty($oldSettings) && !empty($oldSettings->logo)) {
	 				if(file_exists('assets/images/theme_images/'.$oldSettings->logo)) {
	 					@unlink('assets/images/theme_images/'.$oldSettings->logo);
	 				}

	 			}
	 		}
	 	}
		// first move uploaded image of login
	 	if(!empty($post['hidden_faq_banner_image']) && !empty($_FILES['faq_banner_image']['name'])){
	 		$upload_response = $this->upload_photo('faq_banner_image','assets/images/theme_images/');
	 		if($upload_response['success']){
	 			$settingsArray['faq_banner_image']=	$faq_banner_image = $upload_response['upload_data']['file_name'];
	 		} else {
	 			$json['errors']['faq_banner_image'] = $upload_response['msg'];
	 			echo json_encode($json);die;
	 		}
	 	} else {
	 		if(empty($post['hidden_faq_banner_image'])) {

	 			$settingsArray['faq_banner_image']='';
	 			if(!empty($oldSettings) && !empty($oldSettings->faq_banner_image)) {
	 				if(file_exists('assets/images/theme_images/'.$oldSettings->faq_banner_image)) {
	 					@unlink('assets/images/theme_images/'.$oldSettings->faq_banner_image);
	 				}

	 			}
	 		}
	 	}

		// first move uploaded image of login
	 	if(!empty($post['hidden_contact_banner_image']) && !empty($_FILES['contact_banner_image']['name'])){
	 		$upload_response = $this->upload_photo('contact_banner_image','assets/images/theme_images/');
	 		if($upload_response['success']){
	 			$settingsArray['contact_banner_image'] =  $contact_banner_image = $upload_response['upload_data']['file_name'];
	 		} else {
	 			$json['errors']['contact_banner_image'] = $upload_response['msg'];
	 			echo json_encode($json);die;
	 		}
	 	} else {
	 		if(empty($post['hidden_contact_banner_image'])){
	 			$settingsArray['contact_banner_image']='';
	 			if(!empty($oldSettings) && !empty($oldSettings->contact_banner_image)) {
	 				if(file_exists('assets/images/theme_images/'.$oldSettings->contact_banner_image)) {
	 					@unlink('assets/images/theme_images/'.$oldSettings->contact_banner_image);
	 				}

	 			}
	 		}
	 	}

		// first move uploaded image of login 
	 	if(!empty($post['hidden_login_img']) && !empty($_FILES['avatar_login']['name'])){
	 		$upload_response = $this->upload_photo('avatar_login','assets/images/theme_images/');
	 		if($upload_response['success']){
	 			$settingsArray['login_img'] =	$avatar_login = $upload_response['upload_data']['file_name'];
	 		}
	 		else{
	 			$json['errors']['avatar_login'] = $upload_response['msg'];
	 			echo json_encode($json);die;
	 		}
	 	}  else {
	 		if(empty($post['hidden_login_img'])){
	 			$settingsArray['login_img']='';
	 			if(!empty($oldSettings) && !empty($oldSettings->login_img)) {
	 				if(file_exists('assets/images/theme_images/'.$oldSettings->login_img)) {
	 					@unlink('assets/images/theme_images/'.$oldSettings->login_img);
	 				}

	 			}
	 		}
	 	}

		// upload registration image
	 	if(!empty($post['hidden_reg_img']) && !empty($_FILES['avatar_registration']['name'])){
	 		$upload_response = $this->upload_photo('avatar_registration','assets/images/theme_images/');
	 		if($upload_response['success']){
	 			$settingsArray['reg_img'] =	$avatar_registration = $upload_response['upload_data']['file_name'];
	 		}
	 		else{
	 			$json['errors']['avatar_registration'] = $upload_response['msg'];
	 			echo json_encode($json);die;
	 		}
	 	}  else {
	 		if(empty($post['hidden_reg_img'])){
	 			$settingsArray['reg_img']='';
	 			if(!empty($oldSettings) && !empty($oldSettings->reg_img)) {
	 				if(file_exists('assets/images/theme_images/'.$oldSettings->reg_img)) {
	 					@unlink('assets/images/theme_images/'.$oldSettings->reg_img);
	 				}

	 			}
	 		}
	 	}

		// upload terms image
	 	if(!empty($post['hidden_terms_img']) && !empty($_FILES['avatar_terms']['name'])){
	 		$upload_response = $this->upload_photo('avatar_terms','assets/images/theme_images/');
	 		if($upload_response['success']){
	 			$settingsArray['terms_img']= $avatar_terms = $upload_response['upload_data']['file_name'];
	 		}
	 		else{
	 			$json['errors']['avatar_terms'] = $upload_response['msg'];
	 			echo json_encode($json);die;
	 		}
	 	} else {
	 		if(empty($post['hidden_terms_img'])){
	 			$settingsArray['terms_img']='';
	 			if(!empty($oldSettings) && !empty($oldSettings->terms_img)) {
	 				if(file_exists('assets/images/theme_images/'.$oldSettings->terms_img)) {
	 					@unlink('assets/images/theme_images/'.$oldSettings->terms_img);
	 				}

	 			}
	 		}
	 	}

	 	$settingsArray['custom_logo_size']				 = $this->input->post('custom_logo_size',true);
	 	$settingsArray['log_custom_height']				 = $this->input->post('log_custom_height',true);
	 	$settingsArray['log_custom_width']				 = $this->input->post('log_custom_width',true);
	 	$settingsArray['top_banner_slider']				 = json_encode($this->input->post('top_banner_slider',true));
	 	$settingsArray['membership_top_title']           = $this->input->post('membership_top_title',true);
	 	$settingsArray['membership_sub_title']           = $this->input->post('membership_sub_title',true);
	 	$settingsArray['contact_us_t_title']             = $this->input->post('contact_us_t_title',true);
	 	$settingsArray['contact_us_slug_title']          = $this->input->post('contact_us_slug_title',true);
	 	$settingsArray['contact_sec_title']				 = $this->input->post('contact_sec_title',true);
	 	$settingsArray['contact_sec_subtitle']			 = $this->input->post('contact_sec_subtitle',true);
	 	$settingsArray['contact_us_full_address']        = $this->input->post('contact_us_full_address',true);
	 	$settingsArray['contact_us_phone']               = $this->input->post('contact_us_phone',true);
	 	$settingsArray['contact_us_email']               = $this->input->post('contact_us_email',true);
	 	$settingsArray['contact_us_iframe']              = $this->input->post('contact_us_iframe',false);
	 	$settingsArray['youtube_link']                   = $this->input->post('youtube_link',true);
	 	$settingsArray['facebook_link']                  = $this->input->post('facebook_link',true);
	 	$settingsArray['twitter_link']                   = $this->input->post('twitter_link',true);
	 	$settingsArray['instegram_link']                 = $this->input->post('instegram_link',true);
	 	$settingsArray['whatsapp_number']				 = $this->input->post('whatsapp_number',true);
	 	$settingsArray['whatsapp_default_msg']			 = $this->input->post('whatsapp_default_msg',true);
	 	$settingsArray['footer_menu_title_a']            = $this->input->post('footer_menu_title_a',true);
	 	$settingsArray['footer_menu_title_b']            = $this->input->post('footer_menu_title_b',true);
	 	$settingsArray['footer_menu_title_c']            = $this->input->post('footer_menu_title_c',true);
	 	$settingsArray['footer_menu_title_d']            = $this->input->post('footer_menu_title_d',true);
	 	$settingsArray['home_section_title']             = $this->input->post('home_section_title',true);
	 	$settingsArray['home_section_subtitle']          = $this->input->post('home_section_subtitle',true);
	 	$settingsArray['recommendation_section_title']   = $this->input->post('recommendation_section_title',true);
	 	$settingsArray['recommendation_section_subtitle']= $this->input->post('recommendation_section_subtitle',true);
	 	$settingsArray['faq_banner_title']				 = $this->input->post('faq_banner_title',true);
	 	$settingsArray['faq_section_title']				 = $this->input->post('faq_section_title',true);
	 	$settingsArray['faq_section_subtitle']			 = $this->input->post('faq_section_subtitle',true);
	 	$settingsArray['copyright']						 = $this->input->post('copyright',true);
	 	$settingsArray['banner_bottom_title']			 = $this->input->post('banner_bottom_title',true);
	 	$settingsArray['banner_bottom_slug']			 = $this->input->post('banner_bottom_slug',true);
	 	$settingsArray['banner_button_text']			 = $this->input->post('banner_button_text',true);
	 	$settingsArray['banner_button_link']			 = $this->input->post('banner_button_link',true);
	 	$settingsArray['login_content']				     = $this->input->post('login_content',true);
	 	$settingsArray['reg_content']				     = $this->input->post('reg_content',true);
	 	$settingsArray['terms_content']				     = $this->input->post('terms_content',true);

	 	$language_id= $this->input->post('language_id',true);


	 	$this->store_theme_multiple_page_settings();

	 	$this->theme->update_settings_data($settings_id,$settingsArray,$language_id);
	 	$this->session->set_flashdata('success', 'Settings Data Updated successfully');
	 	$json['location'] = base_url('themes/multiple_theme');
	 	echo json_encode($json);die;
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
	 		$dat =$this->image_lib->resize();
	 		$data = array('success' => true, 'upload_data' => $upload_details, 'msg' => "Upload success!");
	 	}
	 	return $data;
	 }


	 public function change_positions(){
	 	try {
	 		$table = $this->input->post('table');
	 		$whe_column = $this->input->post('whe_column');
	 		$pos_column = $this->input->post('pos_column');
	 		$positions = json_decode($this->input->post('positions'));
	 		for ($i=0; $i < sizeof($positions); $i++) { 
	 			$where[$whe_column] = $positions[$i];
	 			$data[$pos_column] = $i + 1;
	 			$this->theme->update_data($table, $where, $data);
	 		}
	 		echo json_encode(array('status'=> true, 'message'=> 'Position updated successfully!', 'query' => $this->db->last_query()));
	 	} catch (\Throwable $th) {
	 		echo json_encode(array('status'=> false, 'message'=> $th->getMessage()));
	 	}
	 }

	 public function themesetting(){
		$post = $this->input->post();

		foreach ($post as $key => $value) {
			$this->Setting_model->saveThemeSetting($key, $value);
		}

		$json['success'] =  __('admin.setting_saved_successfully');

		echo json_encode($json);die;
	 }

	 public function default_front_theme_settings(){
		$setting = $this->input->post('setting');
		
		$color = $this->input->post('color');
		
		$update = $this->Setting_model->default_front_theme_settings($setting, $color);
		echo $update;
	 }

	 public function set_default_front_theme_settings(){
		$setting_array = [
			'front_header_color_before_scroll' => 'transparent',
			'front_header_color_after_scroll' => '#E98024',
			'front_button_color' => '#f1a05a',
			'front_button_hover_color' => '#F66B14',
			'front_button_text_color' => '#ffffff',
			'front_footer_color' => '#363839'
		];
		
		$update = $this->Setting_model->set_default_front_theme_settings($setting_array, $this->input->post('setting_type'));
		echo $update;
	 }

	 public function getSettingTabContent(){
	 	$post = $this->input->post(null,true);
		$language_id=1;
		if(!empty($post) && isset($post['language_id']))
		{
			$language_id=$post['language_id'];
		}
		$this->load->model("Theme");

		$data['theme_settings'] = $this->Theme->getSettings($language_id);
		$this->load->model('Theme_setting');
		$json['html']= $this->load->view('admincontrol/theme/seting_tab_content_subview',$data, true);
		echo json_encode($json); 
	}

}




