<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

use App\User;
class Admincontrol extends MY_Controller {
	function __construct()
	{
	    parent::__construct();
	    $this->load->model('user_model', 'user');
	    $this->load->model('Product_model');
	    $this->load->model('Setting_model');
	    $this->load->model('Common_model');
	    $this->load->model('Review_model');
	    $this->load->helper('share');
	    $this->load->library('user_agent');
	    $this->load->model('Report_model');
	    $this->front_assets = APPPATH . 'views/auth/user/assets/';
	    $this->front_assets_url = base_url('application/views/auth/user/assets/');
	    
	    $this->Product_model->ping($this->session->userdata('administrator')['id']);

		$site_setting_timeout = $this->Product_model->getSettings('site', 'session_timeout');
		$timeout = (isset($site_setting_timeout['session_timeout']) && is_numeric($site_setting_timeout['session_timeout']) && ((int)$site_setting_timeout['session_timeout']) >= 60) ? (int)$site_setting_timeout['session_timeout'] : 1800;

			// Make the $timeout variable available to all views
			$this->load->vars(array('timeout' => $timeout));

			if ($this->session->has_userdata('timestamp') && (time() - $this->session->userdata('timestamp')) > $timeout) {
			    $this->session->sess_destroy();
			    redirect($this->admin_domain_url);
			} elseif ($this->uri->segment(2) != "ajax_dashboard") {
			    $this->session->set_userdata('timestamp', time());
			}
	}

	public function system_update_report() {
	    // Verify that the user is an admin
	    $userdetails = $this->userdetails();

	    // Retrieve log files, ignore '.' and '..'
	    $logFiles = array_diff(scandir(APPPATH . "logs/system_update_logs/"), array('.', '..'));

	    // Sort files by modified time, latest first
	    usort($logFiles, function($a, $b) {
	        return filemtime(APPPATH . "logs/system_update_logs/" . $b) - filemtime(APPPATH . "logs/system_update_logs/" . $a);
	    });

	    $data = [
	        'result' => null
	    ];

	    // If log files exist, load the most recent one
	    if (!empty($logFiles)) {
	        $logData = file_get_contents(APPPATH . "logs/system_update_logs/" . $logFiles[0]);
	        if ($logData !== false) {
	            $data['result'] = json_decode($logData, true);
	        }
	    }

	    // If session driver isn't database, add a new session to database
	    if (SESS_DRIVER != 'database') {
	        $array = [
	            'id' => 1,
	            'ip_address' => '127.0.0.1',
	            'timestamp' => time()
	        ];
	        $this->db->insert('ci_session', $array);
	    }

	    // Load update report view
	    $this->load->view('update_report', $data);
	}

	public function script_details(){
		$userdetails = $this->userdetails();

		list($code,$res) = api('codecanyon/get-details',['licence'=>CODECANYON_LICENCE]);
		$data = $res;
		$this->view($data,'script_details/index');
	}

	public function update_langueges_data() {
		$this->update_user_langauges();
		redirect('/admincontrol/dashboard');
	}

	public function system_status(){

		$userdetails = $this->userdetails();

		$this->load->model("Coupon_model");

		$data['mysql_version'] = $this->db->conn_id->server_info;

		$data['serverReq'] = checkReq();

		$this->view($data,'system_status');
	}

	public function date_compare($element1, $element2) { 

		$datetime1 = strtotime($element1['created_at']); 

		$datetime2 = strtotime($element2['created_at']); 

		return ($datetime1 == $datetime2) ? 0 : (($datetime1 < $datetime2) ? 1 : -1);
	}


	public function clear_commission_tables() {

		// Demo Mode
		if (ENVIRONMENT === 'demo') {
			echo json_encode([
				'status' => 'error',
				'message' => 'Disabled on demo mode'
			]);
			return;
		}
		// Demo Mode

		$userdetails = $this->userdetails();

		$password = $this->input->post('admin_password',true);

		$password_confirm = $this->input->post('password_confirm',true);

		$user = $this->db->query("SELECT * FROM users WHERE id=". (int)$userdetails['id'])->row();

		if(sha1($password) == $user->password){

			if($password_confirm == 'true'){

				$this->session->set_userdata('clear_database_password',1);

				$json['success'] = true;

			} else if($this->session->userdata('clear_database_password') == 1){

				$this->db->truncate('form_action');
				$this->db->query("ALTER TABLE form_action AUTO_INCREMENT=1;");


				$this->db->truncate('affiliate_session_log');
				$this->db->query("ALTER TABLE affiliate_session_log AUTO_INCREMENT=1;");


				$this->db->truncate('cart');
				$this->db->query("ALTER TABLE cart AUTO_INCREMENT=1;");


				$this->db->truncate('clicks_views');
				$this->db->query("ALTER TABLE clicks_views AUTO_INCREMENT=1;");

				
				$this->db->truncate('integration_clicks_action');
				$this->db->query("ALTER TABLE integration_clicks_action AUTO_INCREMENT=1;");


				$this->db->truncate('integration_admin_clicks_action');
				$this->db->query("ALTER TABLE integration_admin_clicks_action AUTO_INCREMENT=1;");


				$this->db->truncate('integration_clicks_logs');
				$this->db->query("ALTER TABLE integration_clicks_logs AUTO_INCREMENT=1;");


				$this->db->truncate('integration_orders');
				$this->db->query("ALTER TABLE integration_orders AUTO_INCREMENT=1;");


				$this->db->truncate('notification');
				$this->db->query("ALTER TABLE notification AUTO_INCREMENT=1;");


				$this->db->truncate('product_action');

				$this->db->query("ALTER TABLE product_action AUTO_INCREMENT=1;");


				$this->db->truncate('product_action_admin');

				$this->db->query("ALTER TABLE product_action_admin AUTO_INCREMENT=1;");


				$this->db->truncate('integration_refer_product_action');

				$this->db->query("ALTER TABLE integration_refer_product_action AUTO_INCREMENT=1;");

				
				$this->db->truncate('refer_product_action');

				$this->db->query("ALTER TABLE refer_product_action AUTO_INCREMENT=1;");


				$this->db->truncate('wallet');

				$this->db->query("ALTER TABLE wallet AUTO_INCREMENT=1;");


				$this->db->truncate('wallet_recursion');

				$this->db->query("ALTER TABLE wallet_recursion AUTO_INCREMENT=1;");


				$this->db->truncate('wallet_request');
				$this->db->query("ALTER TABLE wallet_request AUTO_INCREMENT=1;");


				$this->db->query("ALTER TABLE language AUTO_INCREMENT=2;");


				$this->db->truncate('order');
				$this->db->query("ALTER TABLE `order` AUTO_INCREMENT=1;");


				$this->db->truncate('orders_history');
				$this->db->query("ALTER TABLE orders_history AUTO_INCREMENT=1;");


				$this->db->truncate('order_products');
				$this->db->query("ALTER TABLE order_products AUTO_INCREMENT=1;");


				$this->db->truncate('order_proof');
				$this->db->query("ALTER TABLE order_proof AUTO_INCREMENT=1;");


				$this->db->truncate('integration_clicks_logs');
				$this->db->query("ALTER TABLE integration_clicks_logs AUTO_INCREMENT=1;");


				$this->db->truncate('wallet_requests_history');
				$this->db->query("ALTER TABLE wallet_requests_history AUTO_INCREMENT=1;");


				$this->db->truncate('wallet_requests');
				$this->db->query("ALTER TABLE wallet_requests AUTO_INCREMENT=1;");


				$this->db->truncate('product_view_logs');
				$this->db->query("ALTER TABLE product_view_logs AUTO_INCREMENT=1;");


			    $this->db->query("UPDATE integration_tools SET trigger_count = null");

			    $this->db->query("UPDATE product SET view_statistics = null");

			    $this->db->query("UPDATE form SET view_statistics = null");


				$this->session->set_flashdata('success', __('admin.data_was_deleted_successfully'));

				$json['success'] = true;

			}

		} else {

			$json['errors']['admin_password'] = "Wrong Password..!";
		}

		echo json_encode($json);
	}

	public function clear_tables() {

		// Demo Mode
		if (ENVIRONMENT === 'demo') {
			echo json_encode([
				'status' => 'error',
				'message' => 'Disabled on demo mode'
			]);
			return;
		}
		// Demo Mode

		$userdetails = $this->userdetails();

		$password = $this->input->post('admin_password',true);

		$password_confirm = $this->input->post('password_confirm',true);

		$user = $this->db->query("SELECT * FROM users WHERE id=". (int)$userdetails['id'])->row();

		if(sha1($password) == $user->password){

			if($password_confirm == 'true'){

				$this->session->set_userdata('clear_database_password',1);

				$json['success'] = true;

			} else if($this->session->userdata('clear_database_password') == 1){

				$tablesForTruncates = ['users','setting', 'affiliateads', 'affiliate_action','affiliate_session_log', 'cart', 'categories', 'clicks_views', 'coupon', 'form', 'form_action', 'form_coupon','integration_clicks_action','integration_admin_clicks_action','integration_category','integration_clicks_logs','integration_orders','integration_programs','integration_refer_product_action','integration_tools','integration_tools_ads', 'last_seen', 'notification', 'order', 'orders_history', 'order_products', 'order_proof', 'pagebuilder_theme', 'pagebuilder_theme_page', 'password_resets', 'payment_detail', 'paypal_accounts', 'product', 'productslog', 'product_action', 'product_action_admin', 'product_affiliate', 'product_categories', 'product_media_upload', 'rating', 'refer_market_action', 'refer_product_action', 'shipping_address', 'user_payment_request', 'vendor_setting', 'version_update', 'wallet', 'wallet_recursion', 'wallet_request', 'theme_faq', 'theme_homecontent', 'theme_home_sections_setting', 'theme_pages', 'theme_recommendation', 'theme_sections', 'theme_setting','theme_settings', 'theme_sliders', 'theme_videos','tickets','tickets_reply','tickets_subject','todo_list', 'slugs', 'membership_buy_history', 'membership_user','mail_templates', 'membership_plans', 'theme_links','user_groups','deposit_requests_history','vendor_deposit','unsubscribed_emails','wallet_requests','wallet_requests_history','uncompleted_payment',
				'ci_session','award_level','meta_data','product_view_logs','tutorial_categories','tutorial_pages','google_ads','product_meta','theme_colors','user_lms_product','vendor_config'];


				foreach ($tablesForTruncates as $tablename) {

					$database_name = $this->db->database;
					$count = $this->db->query("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE table_schema = '".$database_name."' AND TABLE_NAME = '".$tablename."'")->num_rows();
					if($count > 0)
					{
						$this->db->truncate($tablename);
						$this->db->query("ALTER TABLE `".$tablename."` AUTO_INCREMENT=1;");
					}
				}

				$this->db->query("DELETE FROM language WHERE id != '1'");

				$this->db->query("ALTER TABLE language AUTO_INCREMENT=2;");

				$this->db->query("DELETE FROM currency WHERE currency_id !='1' ");

				$this->db->query("ALTER TABLE currency AUTO_INCREMENT=2;");

				$this->db->query("DELETE FROM users WHERE id !='1' ");

				$this->db->query("ALTER TABLE users AUTO_INCREMENT=2;");

			$this->db->query("INSERT IGNORE INTO `theme_pages` (`theme_id`, `page_name`, `slug`, `parent_id`, `top_banner_title`, `top_banner_sub_title`, `page_content_title`, `page_content`, `link_footer_section`, `is_header_menu`, `is_header_dropdown`, `position`, `page_type`, `page_banner_image`, `created`, `status`) VALUES
					(0, 'Home', '/', 0, '', '', '', '', '', 1, 0, 1, 'fixed', NULL, '2021-03-15 05:34:48', 1),
					(0, 'Faq', 'faq', 0, '', '', '', '', '', 1, 0, 2, 'fixed', NULL, '2021-03-15 05:40:51', 1),
					(0, 'Terms', 'terms-of-use', NULL, '', '', '', '', '', 1, 0, 3, 'fixed', NULL, '2021-03-15 05:46:09', 1),
					(0, 'Contact', 'contact', NULL, '', '', '', '', '', 1, 0, 4, 'fixed', NULL, '2021-03-15 05:48:16', 1);");


			$this->db->query("INSERT INTO `users` (`id`, `plan_id`, `refid`, `level_id`, `type`, `firstname`, `lastname`, `email`, `username`, `password`, `phone`, `twaddress`, `address1`, `address2`, `ucity`, `ucountry`, `state`, `uzip`, `avatar`, `online`, `unique_url`, `bitly_unique_url`, `updated_at`, `google_id`, `facebook_id`, `twitter_id`, `umode`, `PhoneNumber`, `Addressone`, `Addresstwo`, `City`, `Country`, `StateProvince`, `Zip`, `f_link`, `t_link`, `l_link`, `products_wishlist`, `product_commission`, `affiliate_commission`, `product_commission_paid`, `affiliate_commission_paid`, `product_total_click`, `product_total_sale`, `affiliate_total_click`, `sale_commission`, `sale_commission_paid`, `status`, `reg_approved`, `is_vendor`, `store_meta`, `store_slug`, `store_name`, `store_contact_us_map`, `store_address`, `store_email`, `store_contact_number`, `store_terms_condition`, `value`, `last_ping`, `install_location_details`, `token`, `created_at`, `device_type`, `device_token`, `groups`, `verification_id`, `primary_payment_method`) VALUES
				(1, -1, 0, 0, 'admin', 'Admin', 'Admin', 'admin@gmail.com', 'admin', '7479305b3e914c467c0cb2eba57b352b58e1ff37', '', '', '', '', '', '', '', '', NULL, '1', '', '', '2021-01-01 16:15:31', '', '', '', '', '+1 201-555-0123', '', '', 'Test City', '13', NULL, '123456', '', '', '', NULL, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', '', '2022-07-17 11:05:43', '', NULL, '2021-01-01 16:15:31', 1, NULL, NULL, NULL, NULL);
				");

				$this->db->query("INSERT INTO `tickets_subject` (`id`, `subject`, `user_id`, `status`, `created_at`, `updated_at`) VALUES
					(1, 'General', 1, '1', '2022-07-15 20:13:12', NULL);");

				$this->db->query("INSERT INTO `membership_plans` (`id`, `name`, `type`, `billing_period`, `price`, `special`, `custom_period`, `have_trail`, `free_trail`, `total_day`, `bonus`, `commission_sale_status`, `level_id`, `status`, `user_type`, `campaign`, `product`, `description`, `plan_icon`, `label_text`, `label_background`, `label_color`, `sort_order`, `updated_at`, `created_at`) VALUES
					(1, 'Affiliate Plan', 'paid', 'monthly', 5, 3, 0, 0, 0, 30, 1, 0, 0, 1, 1, NULL, NULL, '<p>Affiliate Plan</p><p>Affiliate Plan</p><p>Affiliate Plan<br></p>', NULL, 'Affiliate Plan', '#0000FF', '#FFFDF5', 0, '2023-02-14 10:49:00', '2023-02-14 10:49:00'),
					(2, 'Vendor Plan', 'paid', 'monthly', 10, 7, 0, 0, 0, 30, 2, 0, 0, 1, 2, 5, 5, '<p>Vendor Plan</p><p>Vendor Plan</p><p>Vendor Plan</p><p>Vendor Plan<br></p>', NULL, 'Vendor Plan', '#0000FF', '#F8FFE3', 0, '2023-02-14 10:51:00', '2023-02-14 10:51:00');");

				$this->db->query("INSERT INTO `categories` (`id`, `name`, `slug`, `description`, `image`, `background_image`, `color`, `parent_id`, `tag`, `created_at`) VALUES
					(1, 'General', 'testrrr-10', '<p>General</p>', '', '', '#FFFFFF', 0, 1, '2022-07-16 12:13:44');
					");

				$this->db->query("INSERT INTO `integration_category` (`id`, `parent_id`, `name`, `created_at`) VALUES
						(1, 0, 'General', '2022-07-17 11:03:43');
						");
				
					$this->db->query("INSERT INTO `setting` (`setting_id`, `setting_key`, `setting_value`, `setting_type`, `setting_status`, `setting_ipaddress`, `setting_is_default`, `language_id`) VALUES
						(1, 'front_template', 'custom_9', 'login', 1, '::1', 0, 1),
						(2, 'top_affiliate', '1', 'userdashboard', 1, '::1', 0, 1),
						(3, 'wallet_min_amount', '200', 'site', 1, '::1', 0, 1),
						(4, 'wallet_min_message', '<p>The minimum limit is: 100</p>', 'site', 1, '::1', 0, 1),
						(5, 'name', 'Affiliate Script', 'site', 1, '::1', 0, 1),
						(6, 'maintenance_mode', '0', 'site', 1, '::1', 0, 1),
						(7, 'store_maintenance_mode', '0', 'site', 1, '::1', 0, 1),
						(8, 'notify_email', 'admin@gmail.com', 'site', 1, '::1', 0, 1),
						(9, 'session_timeout', '', 'site', 1, '::1', 0, 1),
						(10, 'footer', 'Copyright © 2023 Affiliate Script @ Company Name', 'site', 1, '::1', 0, 1),
						(11, 'time_zone', 'Africa/Abidjan', 'site', 1, '::1', 0, 1),
						(12, 'meta_description', '', 'site', 1, '::1', 0, 1),
						(13, 'meta_keywords', '', 'site', 1, '::1', 0, 1),
						(14, 'meta_author', '', 'site', 1, '::1', 0, 1),
						(15, 'google_analytics', '', 'site', 1, '::1', 0, 1),
						(16, 'faceboook_pixel', '', 'site', 1, '::1', 0, 1),
						(17, 'fbmessager_script', '', 'site', 1, '::1', 0, 1),
						(18, 'global_script', '', 'site', 1, '::1', 0, 1),
						(19, 'global_script_status', '[]', 'site', 1, '::1', 0, 1),
						(20, 'mail_type', 'php_mailer', 'email', 1, '::1', 0, 1),
						(21, 'from_email', 'admin@gmail.com', 'email', 1, '::1', 0, 1),
						(22, 'from_name', 'ADMIN SUPPORT', 'email', 1, '::1', 0, 1),
						(23, 'smtp_hostname', '', 'email', 1, '::1', 0, 1),
						(24, 'smtp_username', '', 'email', 1, '::1', 0, 1),
						(25, 'smtp_password', '', 'email', 1, '::1', 0, 1),
						(26, 'smtp_port', '', 'email', 1, '::1', 0, 1),
						(27, 'smtp_crypto', '', 'email', 1, '::1', 0, 1),
						(28, 'registration_status', '1', 'store', 1, '::1', 0, 1),
						(29, 'registration_approval', '0', 'store', 1, '::1', 0, 1),
						(30, 'language_status', '1', 'store', 1, '::1', 0, 1),
						(31, 'affiliate_cookie', '30', 'store', 1, '::1', 0, 1),
						(32, 'default_action_status', '0', 'referlevel', 1, '::1', 0, 1),
						(33, 'default_external_order_status', '0', 'referlevel', 1, '::1', 0, 1),
						(34, 'heading', 'Affiliate Script Terms', 'tnc', 1, '::1', 0, 1),
						(35, 'content', '<p>Affiliate Script Terms Affiliate Script Terms Affiliate Script Terms Affiliate Script Terms Affiliate Script Terms Affiliate Script Terms Affiliate Script Terms Affiliate Script Terms Affiliate Script Terms Affiliate Script Terms Affiliate Script Terms Affiliate Script Terms Affiliate Script Terms Affiliate Script Terms Affiliate Script Terms Affiliate Script Terms Affiliate Script Terms Affiliate Script Terms Affiliate Script Terms Affiliate Script Terms Affiliate Script Terms Affiliate Script Terms Affiliate Script Terms Affiliate Script Terms Affiliate Script Terms Affiliate Script Terms Affiliate Script Terms Affiliate Script Terms Affiliate Script Terms Affiliate Script Terms Affiliate Script Terms Affiliate Script Terms Affiliate Script Terms Affiliate Script Terms Affiliate Script Terms Affiliate Script Terms Affiliate Script Terms Affiliate Script Terms Affiliate Script Terms Affiliate Script Terms Affiliate Script Terms Affiliate Script Terms Affiliate Script Terms Affiliate Script Terms Affiliate Script Terms Affiliate Script Terms Affiliate Script Terms</p>', 'tnc', 1, '::1', 0, 1),
						(36, 'sitekey', '', 'googlerecaptcha', 1, '::1', 0, 1),
						(37, 'secretkey', '', 'googlerecaptcha', 1, '::1', 0, 1),
						(38, 'admin_login', '0', 'googlerecaptcha', 1, '::1', 0, 1),
						(39, 'affiliate_login', '0', 'googlerecaptcha', 1, '::1', 0, 1),
						(40, 'affiliate_register', '0', 'googlerecaptcha', 1, '::1', 0, 1),
						(41, 'client_login', '0', 'googlerecaptcha', 1, '::1', 0, 1),
						(42, 'client_register', '0', 'googlerecaptcha', 1, '::1', 0, 1),
						(43, 'heading', 'Affiliate Script Home Page', 'loginclient', 1, '::1', 0, 1),
						(44, 'content', 'Home content demo Home content demo Home content demo Home content demo Home content demo Home content demo Home content demo Home content demo Home content demo Home content demo Home content demo Home content demo Home content demo Home content demo Home content demo Home content demo Home content demo Home content demo Home content demo Home content demo Home content demo Home content demo Home content demo Home content demo Home content demo Home content demo Home content demo Home content demo Home content demo Home content demo Home content demo Home content demo Home content demo Home content demo Home content demo Home content demo Home content demo Home content demo Home content demo Home content demo Home content demo Home content demo                                                           ', 'loginclient', 1, '::1', 0, 1),
						(45, 'about_content', 'About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content DemoAbout Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo About Content Demo', 'loginclient', 1, '::1', 0, 1),
						(46, 'heading_color', '', 'loginclient', 1, '::1', 0, 1),
						(47, 'input_text_color', '', 'loginclient', 1, '::1', 0, 1),
						(48, 'input_bg_color', '', 'loginclient', 1, '::1', 0, 1),
						(49, 'input_label_color', '', 'loginclient', 1, '::1', 0, 1),
						(50, 'bg_left', '', 'loginclient', 1, '::1', 0, 1),
						(51, 'bg_right', '', 'loginclient', 1, '::1', 0, 1),
						(52, 'footer_bf', '', 'loginclient', 1, '::1', 0, 1),
						(53, 'footer_color', '', 'loginclient', 1, '::1', 0, 1),
						(54, 'btn_sendmail_bg', '', 'loginclient', 1, '::1', 0, 1),
						(55, 'btn_sendmail_color', '', 'loginclient', 1, '::1', 0, 1),
						(56, 'btn_backlogin_bg', '', 'loginclient', 1, '::1', 0, 1),
						(57, 'btn_backlogin_color', '', 'loginclient', 1, '::1', 0, 1),
						(58, 'btn_forgotlink_bg', '', 'loginclient', 1, '::1', 0, 1),
						(59, 'btn_forgotlink_color', '', 'loginclient', 1, '::1', 0, 1),
						(60, 'btn_signin_bg', '', 'loginclient', 1, '::1', 0, 1),
						(61, 'btn_signin_color', '', 'loginclient', 1, '::1', 0, 1),
						(62, 'btn_signup_bg', '', 'loginclient', 1, '::1', 0, 1),
						(63, 'btn_signup_color', '', 'loginclient', 1, '::1', 0, 1),
						(64, 'btn_registersubmit_bg', '', 'loginclient', 1, '::1', 0, 1),
						(65, 'btn_registersubmit_color', '', 'loginclient', 1, '::1', 0, 1),
						(66, 'status', '1', 'store', 1, '::1', 0, 1),
						(67, 'status', '1', 'referlevel', 1, '::1', 0, 1),
						(68, 'registration_builder', '[{\"type\":\"header\",\"label\":\"Firstname\"},{\"type\":\"header\",\"label\":\"Lastname\"},{\"type\":\"header\",\"label\":\"Email\"},{\"type\":\"text\",\"label\":\"Mobile Phone\",\"placeholder\":\"Enter your mobile number\",\"className\":\"form-control\",\"name\":\"text-1621449816785\",\"mobile_validation\":\"true\"},{\"type\":\"header\",\"label\":\"Username\"},{\"type\":\"header\",\"label\":\"Password\"},{\"type\":\"header\",\"label\":\"Confirm_password\"}]', 'registration_builder', 1, '::1', 0, 1),
						(69, 'admin_sound_status', '1', 'live_dashboard', 1, '::1', 0, 1),
						(70, 'admin_action_status', '1', 'live_dashboard', 1, '::1', 0, 1),
						(71, 'admin_integration_order_status', '1', 'live_dashboard', 1, '::1', 0, 1),
						(72, 'admin_affiliate_register_status', '1', 'live_dashboard', 1, '::1', 0, 1),
						(73, 'admin_local_store_order_status', '1', 'live_dashboard', 1, '::1', 0, 1),
						(74, 'admin_data_load_interval', '15', 'live_dashboard', 1, '::1', 0, 1),
						(75, 'admin_integration_logs', '1', 'live_log', 1, '::1', 0, 1),
						(76, 'admin_integration_orders', '1', 'live_log', 1, '::1', 0, 1),
						(77, 'admin_newuser', '1', 'live_log', 1, '::1', 0, 1),
						(78, 'levels', '20', 'referlevel', 1, '::1', 0, 1),
						(79, 'sale_type', 'percentage', 'referlevel', 1, '::1', 0, 1),
						(80, 'disabled_for', '[]', 'referlevel', 1, '::1', 0, 1),
						(81, 'commition', '', 'referlevel_1', 1, '::1', 0, 1),
						(82, 'sale_commition', '', 'referlevel_1', 1, '::1', 0, 1),
						(83, 'ex_commition', '', 'referlevel_1', 1, '::1', 0, 1),
						(84, 'ex_action_commition', '', 'referlevel_1', 1, '::1', 0, 1),
						(85, 'commition', '', 'referlevel_2', 1, '::1', 0, 1),
						(86, 'sale_commition', '', 'referlevel_2', 1, '::1', 0, 1),
						(87, 'ex_commition', '', 'referlevel_2', 1, '::1', 0, 1),
						(88, 'ex_action_commition', '', 'referlevel_2', 1, '::1', 0, 1),
						(89, 'commition', '', 'referlevel_3', 1, '::1', 0, 1),
						(90, 'sale_commition', '', 'referlevel_3', 1, '::1', 0, 1),
						(91, 'ex_commition', '', 'referlevel_3', 1, '::1', 0, 1),
						(92, 'ex_action_commition', '', 'referlevel_3', 1, '::1', 0, 1),
						(93, 'commition', '', 'referlevel_4', 1, '::1', 0, 1),
						(94, 'sale_commition', '', 'referlevel_4', 1, '::1', 0, 1),
						(95, 'ex_commition', '', 'referlevel_4', 1, '::1', 0, 1),
						(96, 'ex_action_commition', '', 'referlevel_4', 1, '::1', 0, 1),
						(97, 'commition', '', 'referlevel_5', 1, '::1', 0, 1),
						(98, 'sale_commition', '', 'referlevel_5', 1, '::1', 0, 1),
						(99, 'ex_commition', '', 'referlevel_5', 1, '::1', 0, 1),
						(100, 'ex_action_commition', '', 'referlevel_5', 1, '::1', 0, 1),
						(101, 'commition', '', 'referlevel_6', 1, '::1', 0, 1),
						(102, 'sale_commition', '', 'referlevel_6', 1, '::1', 0, 1),
						(103, 'ex_commition', '', 'referlevel_6', 1, '::1', 0, 1),
						(104, 'ex_action_commition', '', 'referlevel_6', 1, '::1', 0, 1),
						(105, 'commition', '', 'referlevel_7', 1, '::1', 0, 1),
						(106, 'sale_commition', '', 'referlevel_7', 1, '::1', 0, 1),
						(107, 'ex_commition', '', 'referlevel_7', 1, '::1', 0, 1),
						(108, 'ex_action_commition', '', 'referlevel_7', 1, '::1', 0, 1),
						(109, 'commition', '', 'referlevel_8', 1, '::1', 0, 1),
						(110, 'sale_commition', '', 'referlevel_8', 1, '::1', 0, 1),
						(111, 'ex_commition', '', 'referlevel_8', 1, '::1', 0, 1),
						(112, 'ex_action_commition', '', 'referlevel_8', 1, '::1', 0, 1),
						(113, 'commition', '', 'referlevel_9', 1, '::1', 0, 1),
						(114, 'sale_commition', '', 'referlevel_9', 1, '::1', 0, 1),
						(115, 'ex_commition', '', 'referlevel_9', 1, '::1', 0, 1),
						(116, 'ex_action_commition', '', 'referlevel_9', 1, '::1', 0, 1),
						(117, 'commition', '', 'referlevel_10', 1, '::1', 0, 1),
						(118, 'sale_commition', '', 'referlevel_10', 1, '::1', 0, 1),
						(119, 'ex_commition', '', 'referlevel_10', 1, '::1', 0, 1),
						(120, 'ex_action_commition', '', 'referlevel_10', 1, '::1', 0, 1),
						(121, 'marketvendorstatus', '1', 'market_vendor', 1, '::1', 0, 1),
						(122, 'commission_type', '', 'market_vendor', 1, '::1', 0, 1),
						(123, 'commission_sale', '', 'market_vendor', 1, '::1', 0, 1),
						(124, 'sale_status', '0', 'market_vendor', 1, '::1', 0, 1),
						(125, 'click_allow', 'single', 'market_vendor', 1, '::1', 0, 1),
						(126, 'commission_number_of_click', '', 'market_vendor', 1, '::1', 0, 1),
						(127, 'commission_click_commission', '', 'market_vendor', 1, '::1', 0, 1),
						(128, 'click_status', '0', 'market_vendor', 1, '::1', 0, 1),
						(129, 'storestatus', '1', 'vendor', 1, '::1', 0, 1),
						(130, 'admin_click_count', '', 'vendor', 1, '::1', 0, 1),
						(131, 'admin_click_amount', '0.00', 'vendor', 1, '::1', 0, 1),
						(132, 'admin_sale_commission_type', 'percentage', 'vendor', 1, '::1', 0, 1),
						(133, 'admin_commission_value', '', 'vendor', 1, '::1', 0, 1),
						(134, 'is_install', '1', 'withdrawalpayment_bank_transfer', 1, '::1', 0, 1),
						(135, 'is_install', '1', 'withdrawalpayment_paypal', 1, '::1', 0, 1),
						(136, 'status', '1', 'withdrawalpayment_bank_transfer', 1, '::1', 0, 1),
						(137, 'status', '1', 'withdrawalpayment_paypal', 1, '::1', 0, 1),
						(138, 'ClientID', '', 'withdrawalpayment_paypal', 1, '::1', 0, 1),
						(139, 'ClientSecret', '', 'withdrawalpayment_paypal', 1, '::1', 0, 1),
						(140, 'denied_status_id', '0', 'withdrawalpayment_paypal', 1, '::1', 0, 1),
						(141, 'pending_status_id', '0', 'withdrawalpayment_paypal', 1, '::1', 0, 1),
						(142, 'processing_status_id', '0', 'withdrawalpayment_paypal', 1, '::1', 0, 1),
						(143, 'success_status_id', '0', 'withdrawalpayment_paypal', 1, '::1', 0, 1),
						(144, 'canceled_status_id', '0', 'withdrawalpayment_paypal', 1, '::1', 0, 1),
						(145, 'is_install', '1', 'membershippayment_bank_transfer', 1, '::1', 0, 1),
						(146, 'is_install', '1', 'membershippayment_paypal', 1, '::1', 0, 1),
						(147, 'is_install', '1', 'membershippayment_stripe_payment', 1, '::1', 0, 1),
						(148, 'status', '1', 'membershippayment_bank_transfer', 1, '::1', 0, 1),
						(149, 'bank_details', '', 'membershippayment_bank_transfer', 1, '::1', 0, 1),
						(150, 'status', '1', 'membershippayment_paypal', 1, '::1', 0, 1),
						(151, 'api_username', '', 'membershippayment_paypal', 1, '::1', 0, 1),
						(152, 'api_password', '', 'membershippayment_paypal', 1, '::1', 0, 1),
						(153, 'api_signature', '', 'membershippayment_paypal', 1, '::1', 0, 1),
						(154, 'payment_currency', 'USD', 'membershippayment_paypal', 1, '::1', 0, 1),
						(155, 'denied_status_id', '0', 'membershippayment_paypal', 1, '::1', 0, 1),
						(156, 'pending_status_id', '0', 'membershippayment_paypal', 1, '::1', 0, 1),
						(157, 'processing_status_id', '0', 'membershippayment_paypal', 1, '::1', 0, 1),
						(158, 'success_status_id', '0', 'membershippayment_paypal', 1, '::1', 0, 1),
						(159, 'canceled_status_id', '0', 'membershippayment_paypal', 1, '::1', 0, 1),
						(160, 'status', '1', 'membershippayment_stripe_payment', 1, '::1', 0, 1),
						(161, 'environment', '0', 'membershippayment_stripe_payment', 1, '::1', 0, 1),
						(162, 'test_public_key', '', 'membershippayment_stripe_payment', 1, '::1', 0, 1),
						(163, 'test_secret_key', '', 'membershippayment_stripe_payment', 1, '::1', 0, 1),
						(164, 'live_public_key', '', 'membershippayment_stripe_payment', 1, '::1', 0, 1),
						(165, 'live_secret_key', '', 'membershippayment_stripe_payment', 1, '::1', 0, 1),
						(166, 'order_success_status', '0', 'membershippayment_stripe_payment', 1, '::1', 0, 1),
						(167, 'order_failed_status', '0', 'membershippayment_stripe_payment', 1, '::1', 0, 1),
						(168, 'status', '1', 'storepayment_bank_transfer', 1, '::1', 0, 1),
						(169, 'status', '1', 'storepayment_cod', 1, '::1', 0, 1),
						(170, 'shipping_in_limited', '0', 'shipping_setting', 1, '::1', 0, 1),
						(171, 'shipping_error_message', 'Our store shipping service does not support your country!', 'shipping_setting', 1, '::1', 0, 1),
						(172, 'cost', '[]', 'shipping_setting', 1, '::1', 0, 1),
						(173, 'name', 'Affiliate Script Store', 'store', 1, '::1', 0, 1),
						(174, 'menu_on_front', '1', 'store', 1, '::1', 0, 1),
						(175, 'menu_on_front_blank', '1', 'store', 1, '::1', 0, 1),
						(176, 'theme', '0', 'store', 1, '::1', 0, 1),
						(177, 'google_analytics', '', 'store', 1, '::1', 0, 1),
						(178, 'footer', 'Copyright © 2023 Affiliate Script Store @ Store Name', 'store', 1, '::1', 0, 1),
						(179, 'contact_us_map', '', 'store', 1, '::1', 0, 1),
						(180, 'address', '', 'store', 1, '::1', 0, 1),
						(181, 'email', '', 'store', 1, '::1', 0, 1),
						(182, 'contact_number', '', 'store', 1, '::1', 0, 1),
						(183, 'is_variation_filter', '1', 'store', 1, '::1', 0, 1),
						(184, 'homepage_banner', '{\"title\":\"\",\"content\":\"\",\"button_text\":\"\",\"button_link\":\"\"}', 'store', 1, '::1', 0, 1),
						(185, 'homepage_bottom_section', '{\"content\":\"\"}', 'store', 1, '::1', 0, 1),
						(186, 'about_content', '', 'store', 1, '::1', 0, 1),
						(187, 'contact_content', '', 'store', 1, '::1', 0, 1),
						(188, 'policy_content', '', 'store', 1, '::1', 0, 1),
						(189, 'homepage_slider', '[]', 'store', 1, '::1', 0, 1),
						(190, 'homepage_features', '[]', 'store', 1, '::1', 0, 1),
						(191, 'bs_cards', '[]', 'store', 1, '::1', 0, 1),
						(192, 'social_links', '[]', 'store', 1, '::1', 0, 1),
						(193, 'custom_page', '[]', 'store', 1, '::1', 0, 1),
						(194, 'per_task', 'null', 'store', 1, '::1', 0, 1),
						(195, 'footer_menu', '[]', 'store', 1, '::1', 0, 1),
						(196, 'recaptcha', '', 'formsetting', 1, '::1', 0, 1),
						(197, 'product_commission_type', '', 'formsetting', 1, '::1', 0, 1),
						(198, 'product_commission', '', 'formsetting', 1, '::1', 0, 1),
						(199, 'product_ppc', '', 'formsetting', 1, '::1', 0, 1),
						(200, 'product_noofpercommission', '', 'formsetting', 1, '::1', 0, 1),
						(201, 'form_recursion', '', 'formsetting', 1, '::1', 0, 1),
						(202, 'recursion_custom_time', '0', 'formsetting', 1, '::1', 0, 1),
						(203, 'recursion_endtime', NULL, 'formsetting', 1, '::1', 0, 1),
						(204, 'click_allow', 'single', 'productsetting', 1, '::1', 0, 1),
						(205, 'product_commission_type', '', 'productsetting', 1, '::1', 0, 1),
						(206, 'product_commission', '', 'productsetting', 1, '::1', 0, 1),
						(207, 'product_ppc', '', 'productsetting', 1, '::1', 0, 1),
						(208, 'product_noofpercommission', '', 'productsetting', 1, '::1', 0, 1),
						(209, 'product_recursion', '', 'productsetting', 1, '::1', 0, 1),
						(210, 'recursion_custom_time', '0', 'productsetting', 1, '::1', 0, 1),
						(211, 'recursion_endtime', NULL, 'productsetting', 1, '::1', 0, 1),
						(212, 'status', '1', 'order_comment', 1, '::1', 0, 1),
						(213, 'title', '{\"1\":\"Add Important Comments\"}', 'order_comment', 1, '::1', 0, 1),
						(214, 'status', '1', 'membership', 1, '::1', 0, 1),
						(215, 'custom_logo_size', '0', 'site', 1, '::1', 0, 1),
						(216, 'log_custom_height', '', 'site', 1, '::1', 0, 1),
						(217, 'log_custom_width', '', 'site', 1, '::1', 0, 1),
						(218, 'affiliate_tracking_place', '0', 'site', 1, '::1', 0, 1),
						(219, 'block_click_across_browser', '0', 'site', 1, '::1', 0, 1),
						(220, 'hide_currency_from', '', 'site', 1, '::1', 0, 1),
						(221, 'unsubscribed_page_title', 'Welcome To Our Unsubscribed Page', 'email', 1, '::1', 0, 1),
						(222, 'unsubscribed_page_message', 'We are sorry you go but we respect your decision! You are now unsubscribed from our list.\r\nyou always can enable it back from your user profile page. Thank you', 'email', 1, '::1', 0, 1),
						(223, 'vendor_min_deposit', '500', 'site', 1, '::1', 0, 1),
						(225, 'status', '1', 'depositpayment_bank_transfer', 1, '::1', 1, 1),
						(226, 'proof', '0', 'depositpayment_bank_transfer', 1, '::1', 0, 1),
						(227, 'bank_names', '[\"Bank Transfer Details\"]', 'depositpayment_bank_transfer', 1, '::1', 0, 1),
						(228, 'bank_details', 'Bank Transfer Details\r\nBank Transfer Details\r\nBank Transfer Details\r\nBank Transfer Details', 'depositpayment_bank_transfer', 1, '::1', 0, 1),
						(229, 'additional_bank_details', '[]', 'depositpayment_bank_transfer', 1, '::1', 0, 1),
						(230, 'autoacceptlocalstore', '0', 'referlevel', 1, '::1', 0, 1),
						(231, 'autoacceptexternalstore', '0', 'referlevel', 1, '::1', 0, 1),
						(232, 'autoacceptaction', '0', 'referlevel', 1, '::1', 0, 1),
						(233, 'show_sponser', '', 'referlevel', 1, '::1', 0, 1),
						(234, 'sponser_name', 'System Admin', 'referlevel', 1, '::1', 0, 1),
						(235, 'reg_comission_type', 'disabled', 'referlevel', 1, '::1', 0, 1),
						(236, 'reg_comission_custom_amt', '0', 'referlevel', 1, '::1', 0, 1),
						(237, 'reg_commission', '', 'referlevel_1', 1, '::1', 0, 1),
						(238, 'reg_commission', '', 'referlevel_2', 1, '::1', 0, 1),
						(239, 'reg_commission', '', 'referlevel_3', 1, '::1', 0, 1),
						(240, 'reg_commission', '', 'referlevel_4', 1, '::1', 0, 1),
						(241, 'reg_commission', '', 'referlevel_5', 1, '::1', 0, 1),
						(242, 'reg_commission', '', 'referlevel_6', 1, '::1', 0, 1),
						(243, 'reg_commission', '', 'referlevel_7', 1, '::1', 0, 1),
						(244, 'reg_commission', '', 'referlevel_8', 1, '::1', 0, 1),
						(245, 'reg_commission', '', 'referlevel_9', 1, '::1', 0, 1),
						(246, 'reg_commission', '', 'referlevel_10', 1, '::1', 0, 1),
						(247, 'reg_commission', '', 'referlevel_11', 1, '::1', 0, 1),
						(248, 'sale_commition', '', 'referlevel_11', 1, '::1', 0, 1),
						(249, 'commition', '', 'referlevel_11', 1, '::1', 0, 1),
						(250, 'ex_commition', '', 'referlevel_11', 1, '::1', 0, 1),
						(251, 'ex_action_commition', '', 'referlevel_11', 1, '::1', 0, 1),
						(252, 'reg_commission', '', 'referlevel_12', 1, '::1', 0, 1),
						(253, 'sale_commition', '', 'referlevel_12', 1, '::1', 0, 1),
						(254, 'commition', '', 'referlevel_12', 1, '::1', 0, 1),
						(255, 'ex_commition', '', 'referlevel_12', 1, '::1', 0, 1),
						(256, 'ex_action_commition', '', 'referlevel_12', 1, '::1', 0, 1),
						(257, 'reg_commission', '', 'referlevel_13', 1, '::1', 0, 1),
						(258, 'sale_commition', '', 'referlevel_13', 1, '::1', 0, 1),
						(259, 'commition', '', 'referlevel_13', 1, '::1', 0, 1),
						(260, 'ex_commition', '', 'referlevel_13', 1, '::1', 0, 1),
						(261, 'ex_action_commition', '', 'referlevel_13', 1, '::1', 0, 1),
						(262, 'reg_commission', '', 'referlevel_14', 1, '::1', 0, 1),
						(263, 'sale_commition', '', 'referlevel_14', 1, '::1', 0, 1),
						(264, 'commition', '', 'referlevel_14', 1, '::1', 0, 1),
						(265, 'ex_commition', '', 'referlevel_14', 1, '::1', 0, 1),
						(266, 'ex_action_commition', '', 'referlevel_14', 1, '::1', 0, 1),
						(267, 'reg_commission', '', 'referlevel_15', 1, '::1', 0, 1),
						(268, 'sale_commition', '', 'referlevel_15', 1, '::1', 0, 1),
						(269, 'commition', '', 'referlevel_15', 1, '::1', 0, 1),
						(270, 'ex_commition', '', 'referlevel_15', 1, '::1', 0, 1),
						(271, 'ex_action_commition', '', 'referlevel_15', 1, '::1', 0, 1),
						(272, 'reg_commission', '', 'referlevel_16', 1, '::1', 0, 1),
						(273, 'sale_commition', '', 'referlevel_16', 1, '::1', 0, 1),
						(274, 'commition', '', 'referlevel_16', 1, '::1', 0, 1),
						(275, 'ex_commition', '', 'referlevel_16', 1, '::1', 0, 1),
						(276, 'ex_action_commition', '', 'referlevel_16', 1, '::1', 0, 1),
						(277, 'reg_commission', '', 'referlevel_17', 1, '::1', 0, 1),
						(278, 'sale_commition', '', 'referlevel_17', 1, '::1', 0, 1),
						(279, 'commition', '', 'referlevel_17', 1, '::1', 0, 1),
						(280, 'ex_commition', '', 'referlevel_17', 1, '::1', 0, 1),
						(281, 'ex_action_commition', '', 'referlevel_17', 1, '::1', 0, 1),
						(282, 'reg_commission', '', 'referlevel_18', 1, '::1', 0, 1),
						(283, 'sale_commition', '', 'referlevel_18', 1, '::1', 0, 1),
						(284, 'commition', '', 'referlevel_18', 1, '::1', 0, 1),
						(285, 'ex_commition', '', 'referlevel_18', 1, '::1', 0, 1),
						(286, 'ex_action_commition', '', 'referlevel_18', 1, '::1', 0, 1),
						(287, 'reg_commission', '', 'referlevel_19', 1, '::1', 0, 1),
						(288, 'sale_commition', '', 'referlevel_19', 1, '::1', 0, 1),
						(289, 'commition', '', 'referlevel_19', 1, '::1', 0, 1),
						(290, 'ex_commition', '', 'referlevel_19', 1, '::1', 0, 1),
						(291, 'ex_action_commition', '', 'referlevel_19', 1, '::1', 0, 1),
						(292, 'reg_commission', '', 'referlevel_20', 1, '::1', 0, 1),
						(293, 'sale_commition', '', 'referlevel_20', 1, '::1', 0, 1),
						(294, 'commition', '', 'referlevel_20', 1, '::1', 0, 1),
						(295, 'ex_commition', '', 'referlevel_20', 1, '::1', 0, 1),
						(296, 'ex_action_commition', '', 'referlevel_20', 1, '::1', 0, 1),
						(297, 'tax_status', '0', 'tax_setting', 1, '::1', 0, 1),
						(298, 'common_tax_percentage', '', 'tax_setting', 1, '::1', 0, 1),
						(299, 'cost', '[]', 'tax_setting', 1, '::1', 0, 1),
						(300, 'top_tags_limit', '10', 'store', 1, '::1', 0, 1),
						(301, 'notificationbefore', '10', 'membership', 1, '::1', 0, 1),
						(302, 'default_plan_id', '1', 'membership', 1, '::1', 0, 1),
						(303, 'depositstatus', '1', 'vendor', 1, '::1', 0, 1),
						(304, 'status', '1', 'award_level', 1, '::1', 0, 1),
						(305, 'marketaddnewprogram', '0', 'market_vendor', 1, '::1', 0, 1),
						(306, 'marketaddnewcampaign', '0', 'market_vendor', 1, '::1', 0, 1),
						(307, 'marketaddnewstoreproduct', '0', 'market_vendor', 1, '::1', 0, 1),
						(308, 'marketvendorexternalordercampaign', '0', 'market_vendor', 1, '::1', 0, 1),
						(309, 'marketvendoractionscampaign', '0', 'market_vendor', 1, '::1', 0, 1),
						(310, 'marketvendorclickcampaign', '0', 'market_vendor', 1, '::1', 0, 1),
						(311, 'is_install', '1', 'payment_gateway_bank_transfer', 1, '::1', 0, 1),
						(312, 'is_install', '1', 'payment_gateway_cod', 1, '::1', 0, 1),
						(313, 'is_install', '1', 'payment_gateway_flutterwave', 1, '::1', 0, 1),
						(314, 'is_install', '1', 'payment_gateway_paypal', 1, '::1', 0, 1),
						(315, 'is_install', '1', 'payment_gateway_paypalstandard', 1, '::1', 0, 1),
						(316, 'is_install', '1', 'payment_gateway_paystack', 1, '::1', 0, 1),
						(317, 'is_install', '1', 'payment_gateway_razorpay', 1, '::1', 0, 1),
						(318, 'is_install', '1', 'payment_gateway_yappy', 1, '::1', 0, 1),
						(319, 'is_install', '1', 'payment_gateway_skrill', 1, '::1', 0, 1),
						(320, 'is_install', '1', 'payment_gateway_stripe', 1, '::1', 0, 1),
						(321, 'is_install', '1', 'payment_gateway_xendit', 1, '::1', 0, 1),
						(322, 'is_install', '1', 'payment_gateway_yookassa', 1, '::1', 0, 1),
						(323, 'user_session_timeout', '', 'site', 1, '::1', 0, 1),
						(324, 'admin-side-logo', '', 'site', 1, '::1', 0, 1),
						(325, 'front-side-themes-logo', '', 'site', 1, '::1', 0, 1),
						(326, 'front_custom_logo_size', '0', 'site', 1, '::1', 0, 1),
						(327, 'front_log_custom_height', '', 'site', 1, '::1', 0, 1),
						(328, 'front_log_custom_width', '', 'site', 1, '::1', 0, 1),
						(329, 'favicon', '', 'site', 1, '::1', 0, 1),
						(330, 'status', '1', 'payment_gateway_store_bank_transfer', 1, '::1', 1, 1),
						(331, 'status', '1', 'payment_gateway_store_cod', 1, '::1', 0, 1),
						(332, 'status', '1', 'payment_gateway_store_flutterwave', 1, '::1', 0, 1),
						(333, 'status', '1', 'payment_gateway_store_paypal', 1, '::1', 0, 1),
						(334, 'status', '1', 'payment_gateway_store_paypalstandard', 1, '::1', 0, 1),
						(335, 'status', '1', 'payment_gateway_store_paystack', 1, '::1', 0, 1),
						(336, 'status', '1', 'payment_gateway_store_razorpay', 1, '::1', 0, 1),
						(337, 'status', '1', 'payment_gateway_store_skrill', 1, '::1', 0, 1),
						(338, 'status', '1', 'payment_gateway_store_stripe', 1, '::1', 0, 1),
						(339, 'status', '1', 'payment_gateway_store_xendit', 1, '::1', 0, 1),
						(340, 'status', '1', 'payment_gateway_store_yappy', 1, '::1', 0, 1),
						(341, 'status', '1', 'payment_gateway_store_yookassa', 1, '::1', 0, 1),
						(342, 'status', '1', 'payment_gateway_deposit_yookassa', 1, '::1', 0, 1),
						(343, 'status', '1', 'payment_gateway_deposit_yappy', 1, '::1', 0, 1),
						(344, 'status', '1', 'payment_gateway_membership_yappy', 1, '::1', 0, 1),
						(345, 'status', '1', 'payment_gateway_membership_yookassa', 1, '::1', 0, 1),
						(346, 'status', '1', 'payment_gateway_membership_xendit', 1, '::1', 0, 1),
						(347, 'status', '1', 'payment_gateway_deposit_xendit', 1, '::1', 0, 1),
						(348, 'status', '1', 'payment_gateway_membership_stripe', 1, '::1', 0, 1),
						(349, 'status', '1', 'payment_gateway_deposit_stripe', 1, '::1', 0, 1),
						(350, 'status', '1', 'payment_gateway_deposit_skrill', 1, '::1', 0, 1),
						(351, 'status', '1', 'payment_gateway_membership_skrill', 1, '::1', 0, 1),
						(352, 'status', '1', 'payment_gateway_membership_razorpay', 1, '::1', 0, 1),
						(353, 'status', '1', 'payment_gateway_deposit_razorpay', 1, '::1', 0, 1),
						(354, 'status', '1', 'payment_gateway_deposit_paystack', 1, '::1', 0, 1),
						(355, 'status', '1', 'payment_gateway_membership_paystack', 1, '::1', 0, 1),
						(356, 'status', '1', 'payment_gateway_membership_paypalstandard', 1, '::1', 0, 1),
						(357, 'status', '1', 'payment_gateway_deposit_paypalstandard', 1, '::1', 0, 1),
						(358, 'status', '1', 'payment_gateway_deposit_paypal', 1, '::1', 0, 1),
						(359, 'status', '1', 'payment_gateway_membership_paypal', 1, '::1', 0, 1),
						(360, 'status', '1', 'payment_gateway_membership_flutterwave', 1, '::1', 0, 1),
						(361, 'status', '1', 'payment_gateway_deposit_flutterwave', 1, '::1', 0, 1),
						(362, 'status', '1', 'payment_gateway_deposit_cod', 1, '::1', 0, 1),
						(363, 'status', '1', 'payment_gateway_membership_cod', 1, '::1', 0, 1),
						(364, 'status', '1', 'payment_gateway_membership_bank_transfer', 1, '::1', 1, 1),
						(365, 'status', '1', 'payment_gateway_deposit_bank_transfer', 1, '::1', 1, 1),
						(366, 'admin_side_bar_color', '#ffffff', 'theme', 1, '::1', 0, 1),
						(367, 'admin_side_bar_scroll_color', '#007bff', 'theme', 1, '::1', 0, 1),
						(368, 'admin_side_bar_text_color', '#686868', 'theme', 1, '::1', 0, 1),
						(369, 'admin_side_bar_text_hover_color', '#007bff', 'theme', 1, '::1', 0, 1),
						(370, 'admin_top_bar_color', '#ffffff', 'theme', 1, '::1', 0, 1),
						(371, 'admin_footer_color', '#f2f3f5', 'theme', 1, '::1', 0, 1),
						(372, 'admin_logo_color', '#007bff', 'theme', 1, '::1', 0, 1),
						(373, 'user_side_bar_color', '#ffffff', 'theme', 1, '::1', 0, 1),
						(374, 'user_side_bar_text_color', '#3f567a', 'theme', 1, '::1', 0, 1),
						(375, 'user_side_bar_clock_text_color', '#085445', 'theme', 1, '::1', 0, 1),
						(376, 'user_side_bar_text_hover_color', '#5ec394', 'theme', 1, '::1', 0, 1),
						(377, 'user_top_bar_color', '#ffffff', 'theme', 1, '::1', 0, 1),
						(378, 'user_footer_color', '#ffffff', 'theme', 1, '::1', 0, 1),
						(379, 'admin_side_font', 'PT Sans', 'site', 1, '::1', 0, 1),
						(380, 'user_side_font', 'sans-serif', 'site', 1, '::1', 0, 1),
						(381, 'front_side_font', 'sans-serif', 'site', 1, '::1', 0, 1),
						(382, 'cart_store_side_font', 'Jost', 'site', 1, '::1', 0, 1),
						(383, 'sales_store_side_font', 'Roboto', 'site', 1, '::1', 0, 1),
						(384, 'notification_sound', 'notify2.mp3', 'site', 1, '::1', 0, 1),
						(385, 'admin_url', 'admin', 'security', 1, '::1', 0, 1),
						(386, 'front_url', '', 'security', 1, '::1', 0, 1),
						(387, 'status', '1', 'payment_gateway_store_toyyibpay', 1, '::1', 0, 1),
						(388, 'status', '1', 'payment_gateway_deposit_toyyibpay', 1, '::1', 0, 1),
						(389, 'status', '1', 'payment_gateway_membership_toyyibpay', 1, '::1', 0, 1),
						(390, 'withdrawal_proof', '0', 'withdrawalpayment_bank_transfer', 1, '::1', 0, 1),
						(391, 'classified_banner_title', '', 'store', 1, '::1', 0, 1),
						(392, 'classified_banner_subtitle', '', 'store', 1, '::1', 0, 1),
						(393, 'store_mode', 'cart', 'store', 1, '', 0, 1),
						(394, 'admin_button_color', '#3d5674', 'theme', 1, '::1', 0, 1),
						(395, 'admin_button_hover_color', '#007bff', 'theme', 1, '::1', 0, 1),
						(396, 'user_button_color', '#3d5674', 'theme', 1, '::1', 0, 1),
						(397, 'user_button_hover_color', '#085445', 'theme', 1, '::1', 0, 1),
						(398, 'contact_us_page', '1', 'userdashboard', 1, '', 0, 1),
						(399, 'tickets_page', '1', 'userdashboard', 1, '', 0, 1),
						(400, 'invitation_link_id', '1', 'userdashboard', 1, '', 0, 1),
						(401, 'admin_login_box_background_color', '#7a90a8', 'theme', 1, '::1', 0, 1),
						(402, 'admin_login_background_option', '0', 'theme', 1, '::1', 0, 1),
						(403, 'admin_login_background_color', '#5e7590', 'theme', 1, '::1', 0, 1),
						(404, 'admin-login-background-image', '', 'theme', 1, '::1', 0, 1),
						(405, 'show_popup', 'enable', 'welcome', 1, '::1', 0, 1),
						(406, 'heading', 'Popup Welcome Title', 'welcome', 1, '::1', 0, 1),
						(407, 'video_link', '', 'welcome', 1, '::1', 0, 1),
						(408, 'content', 'Popup Welcome Content\r\nPopup Welcome Content\r\nPopup Welcome Content', 'welcome', 1, '::1', 0, 1),
						(409, 'mail_send_option', 'enable', 'email', 1, '::1', 0, 1),
						(410, 'tickets_filter_status', '', 'site', 1, '::1', 0, 1),
						(411, 'default_affiliate_plan_id', '1', 'membership', 1, '::1', 0, 1),
						(412, 'default_vendor_plan_id', '2', 'membership', 1, '::1', 0, 1),
						(413, 'wallet_auto_withdrawal', '1', 'site', 1, '::1', 0, 1),
						(414, 'wallet_auto_withdrawal_days', '45', 'site', 1, '::1', 0, 1),
						(415, 'wallet_auto_withdrawal_limit', '1000', 'site', 1, '::1', 0, 1),
						(416, 'wallet_min_message_new', 'The minimum limit is', 'site', 1, '::1', 0, 1),
						(417, 'wallet_max_amount', '500', 'site', 1, '::1', 0, 1),
						(418, 'vendormlmmodule', '1', 'market_vendor', 1, '::1', 0, 1),
						(419, 'cookies_consent_mesag', 'We use cookies to improve your browsing experience. By continuing to use our site, you agree to our use of cookies.', 'site', 1, '::1', 0, 1),
						(420, 'logo', '', 'store', 1, '::1', 0, 1),
						(421, 'store_custom_logo_size', '0', 'store', 1, '::1', 0, 1),
						(422, 'store_logo_custom_width', '', 'store', 1, '::1', 0, 1),
						(423, 'store_logo_custom_height', '', 'store', 1, '::1', 0, 1),
						(424, 'cartimage', '', 'store', 1, '::1', 0, 1),
						(425, 'favicon', '', 'store', 1, '::1', 0, 1),
						(426, 'hbanimage', '', 'store', 1, '::1', 0, 1),
						(427, 'enable_shorten_numbers', '1', 'site', 1, '', 0, 1),
						(428, 'cookies_menu', '1', 'site', 1, '', 0, 1),
						(429, 'cookies_consent', '1', 'site', 1, '', 0, 1),
						(430, 'markettools_status', '1', 'market_tools', 1, '::1', 0, 1),
						(431, 'status', '1', 'market_tools', 1, '::1', 0, 1),
						(432, 'marketvendorpanelmode', '0', 'market_vendor', 1, '', 0, 1),
						(433, 'is_install', '1', 'payment_gateway_toyyibpay', 1, '::1', 0, 1);");

	
		$this->db->query("INSERT INTO `mail_templates` (`id`, `unique_id`, `name`, `subject`, `text`, `admin_subject`, `client_subject`, `client_text`, `admin_text`, `shortcode`) VALUES
			(1, '', 'User Registration', 'User Registration Successfully', '<p>Dear [[firstname]],</p>\r\n\r\n<p>Your new affiliate user account has been created welcome to the [[website_name]]</p>\r\n\r\n<p>your account details:</p>\r\n\r\n<p>================</p>\r\n\r\n<p>[[firstname]]</p>\r\n\r\n<p>[[username]]</p>\r\n\r\n<p>[[email]]</p>\r\n\r\n<p><br />\r\n[[website_name]]<br />\r\nSupport Team</p>\r\n', 'Admin : New affiliate user Register', NULL, NULL, '<p>Dear Admin,</p>\r\n\r\n<p>&nbsp;New affiliate user Register on your site&nbsp;[[website_name]]</p>\r\n\r\n<p>Affiliate details:</p>\r\n\r\n<p>============</p>\r\n\r\n<p>[[firstname]]</p>\r\n\r\n<p>[[username]]</p>\r\n\r\n<p>[[email]]</p>\r\n\r\n<p><br />\r\n[[website_name]]<br />\r\nSupport Team</p>\r\n', 'firstname,lastname,email,username,website_name,website_logo'),
			(2, '', 'Client Registration', 'New Client Register Under you', '<p>Dear [[firstname]],</p>\r\n\r\n<p>New client account has been created under you</p>\r\n\r\n<p><br />\r\n[[website_name]]<br />\r\nSupport Team</p>\r\n', 'Admin : New Client Register', 'Dear [[firstname]], Welcome To Our Store', '<p>Dear [[firstname]],</p>\r\n\r\n<p>welcome to the [[website_name]]</p>\r\n\r\n<p><br />\r\n[[website_name]]<br />\r\nSupport Team</p>\r\n', '<p>Dear Admin,</p>\r\n\r\n<p>New client has been registered on your store</p>\r\n\r\n<p>[[firstname]] ,&nbsp;[[lastname]]&nbsp;</p>\r\n\r\n<p>[[email]] | [[username]]</p>\r\n\r\n<p><br />\r\n[[website_name]]<br />\r\nSupport Team</p>\r\n', 'firstname,lastname,email,username,website_name,website_logo'),
			(3, '', 'Forget Password', 'User Forget Password', '<p>Dear [[firstname]],</p>\r\n\r\n<p>You recently request to reset your password from your [[website_name]] account click the below link to reset password</p>\r\n\r\n<p>[[reset_link]]</p>\r\n\r\n<p>If you did not request a password rest, please ignore this email or reply us know.</p>\r\n\r\n<p>[[website_name]]</p>\r\n\r\n<p>If you did not request a password rest, please ignore this email or reply us know.</p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p>Thanks<br />\r\n[[website_name]]</p>\r\n', 'Admin : Forget Password', 'Client : Forget Password', '<p>Dear [[firstname]],</p>\r\n\r\n<p>You recently request to reset your password from your [[website_name]] account click the below link to reset password</p>\r\n\r\n<p>[[reset_link]]</p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p>If you did not request a password rest, please ignore this email or reply us know.</p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p>Thanks<br />\r\n[[website_name]]</p>\r\n', '<p>Dear [[firstname]],</p>\r\n\r\n<p>You recently request to reset your password from your [[website_name]] account click the below link to reset password</p>\r\n\r\n<p>[[reset_link]]</p>\r\n\r\n<p>If you did not request a password rest, please ignore this email or reply us know.</p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p>Thanks<br />\r\n[[website_name]]</p>\r\n', 'reset_link,firstname,lastname,email,username,website_name,website_logo'),
			(4, '', 'Send Wallet withdrawal Request', 'Send Wallet Withdrawal Request', '<p>Dear [[name]],</p>\r\n\r\n<p>Your withdrawal request is accept successfully and procced shortly</p>\r\n\r\n<p>Amount : [[amount]]</p>\r\n\r\n<p>Thanks<br />\r\n[[website_name]]</p>\r\n', 'Admin : Send Wallet Withdrawal Request', '', '', '<p>Dear [[name]],</p>\r\n\r\n<p>Your withdrawal request is accept successfully and procced shortly</p>\r\n\r\n<p>Amount : [[amount]]</p>\r\n\r\n<p>Thanks<br />\r\n[[website_name]]</p>\r\n', 'amount,comment,name,user_email,commission_type,website_name,website_logo'),
			(5, '', 'withdrawal request status change', 'Your withdrawal request status change', '<p>Dear [[name]],</p>\r\n\r\n<p>Your withdrawal request status has been change to : <strong>[[new_status]]</strong></p>\r\n\r\n<p><br />\r\n[[website_name]]<br />\r\nSupport Team</p>\r\n', 'Admin side', '', '', '<p>Dear [[name]],</p>\r\n\r\n<p>Withdrawal request status has been change to : <strong>[[new_status]]</strong></p>\r\n\r\n<p><br />\r\n[[website_name]]<br />\r\nSupport Team</p>\r\n', 'amount,comment,name,user_email,commission_type,website_name,website_logo,new_status'),
			(6, '', 'Store Contact Us', '', '', 'Admin : Store Contact Us', 'We will contact to you shortly ..!', '<p>&nbsp;</p>\r\n\r\n<p><strong>Name </strong>: [[name]]</p>\r\n\r\n<p><strong>Email </strong>: [[email]]</p>\r\n\r\n<p><strong>Phone </strong>: [[phone]]</p>\r\n\r\n<p><strong>Message</strong> :</p>\r\n\r\n<p>[[message]]</p>\r\n\r\n<p>&nbsp;</p>\r\n', '<p>Hey Admin <strong>[[name]] </strong>trying to contact you.</p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p><strong>Name </strong>: [[name]]</p>\r\n\r\n<p><strong>Email </strong>: [[email]]</p>\r\n\r\n<p><strong>Phone </strong>: [[phone]]</p>\r\n\r\n<p><strong>Message</strong> :</p>\r\n\r\n<p>[[message]]</p>\r\n\r\n<p>&nbsp;</p>\r\n', 'name,email,phone,message,website_name,website_logo'),
			(7, '', 'Order Status Has Been Change', 'Your Order Status Has Been Change', '<p>Hello<strong>&nbsp;[[firstname]] [[lastname]]</strong></p>\r\n\r\n<p>Your Order Status Has Been Change to <strong>[[status]]</strong></p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p>[[comment]]</p>\r\n\r\n<p><br />\r\norder Id :<strong> [[order_id]]</strong></p>\r\n', 'Admin : Your Order Status Has Been Change', 'Client: Your Order Status Has Been Change', '<p>Hello<strong>&nbsp;[[firstname]] [[lastname]]</strong></p>\r\n\r\n<p>Your Order Status Has Been Change to <strong>[[status]]</strong></p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p>[[comment]]</p>\r\n\r\n<p><br />\r\norder Id :<strong> [[order_id]]</strong></p>\r\n', '<p>Hello<strong>&nbsp;[[firstname]] [[lastname]]</strong></p>\r\n\r\n<p>Your Order Status Has Been Change to <strong>[[status]]</strong></p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p>[[comment]]</p>\r\n\r\n<p><br />\r\norder Id :<strong> [[order_id]]</strong></p>\r\n', 'order_id,status,order_link,product_name,product_description,commission_type,PhoneNumber,firstname,lastname,commission,total,currency_code,txn_id,website_name,website_logo,comment'),
			(8, '', 'New Order', 'Affiliate: New Order Commission From [[firstname]] [[lastname]]', '<p>Hello Affiliate,</p>\r\n\r\n<p>you got new order Commission from sale thats done under [[firstname]] [[lastname]]</p>\r\n\r\n<p>Commission: [[commission]] -&nbsp;[[commission_type]]</p>\r\n\r\n<p><strong>Commission for product_name :&nbsp;</strong>[[product_name]]&nbsp;[[variation_details]]</p>\r\n\r\n<p><strong>product_description</strong> : [[product_description]]</p>\r\n', 'Admin : New Order [[order_id]] has been successfully placed.', 'Client : New Order [[order_id]] has been successfully placed.', '<p>Dear Client,</p>\r\n\r\n<p>New Order <strong>[[order_id]] </strong>has been successfully placed on your site [[website_name]] .</p>\r\n\r\n<p><strong>Order Status</strong> : [[status]]<br />\r\n<strong>Total Amount</strong> : [[total]]<br />\r\n<strong>Transaction ID</strong> : [[txn_id]]</p>\r\n\r\n<p>[[order_link]]</p>\r\n', '<p>Dear Admin,</p>\r\n\r\n<p>New Order <strong>[[order_id]] </strong>has been successfully placed on your site [[website_name]] .</p>\r\n\r\n<p><strong>Order Status</strong> : [[status]]<br />\r\n<strong>Total Amount</strong> : [[total]]<br />\r\n<strong>Transaction ID</strong> : [[txn_id]]</p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p>[[order_link]]</p>\r\n', 'order_id,status,order_link,product_name,variation_details,product_description,commission_type,PhoneNumber,firstname,lastname,commission,total,currency_code,txn_id,website_name,website_logo,order_id'),
			(10, '', 'get market click notification', 'Get market click notification', '<p>Dear [[name]],</p>\r\n\r\n<p>[[firstname]] [[lastname]] got commition from market [[affiliateads_type]] click</p>\r\n\r\n<p>Commition : [[affiliate_commission]]</p>\r\n\r\n<p><br />\r\n[[website_name]]<br />\r\nSupport Team</p>\r\n', 'Admin : Get market click notification', '', '', '<p>Dear [[name]],</p>\r\n\r\n<p>[[firstname]] [[lastname]] got commition from market [[affiliateads_type]] click</p>\r\n\r\n<p>Commition : [[affiliate_commission]]</p>\r\n\r\n<p><br />\r\n[[website_name]]<br />\r\nSupport Team</p>\r\n', 'affiliateads_type,affiliate_commission,firstname,lastname,email,username,website_name,website_logo'),
			(11, '', 'External Website New Order', 'External Website New Order [[external_website_name]]', '<p>Hey&nbsp;[[username]]</p>\r\n\r\n<p>You have got&nbsp;[[commission]] from [[external_website_name]]</p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p><strong>Thanks&nbsp;</strong></p>\r\n\r\n<p>[[website_name]]</p>\r\n', 'External Website New Order [[external_website_name]]', '', '', '<p>Hey New Order Placed at&nbsp;[[external_website_name]]</p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p><strong>User </strong>:&nbsp;[[username]]</p>\r\n\r\n<p><strong>Website </strong>:&nbsp;[[external_website_name]]</p>\r\n\r\n<p><strong>commission </strong>: [[commission]]</p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p><strong>Thanks&nbsp;</strong></p>\r\n\r\n<p>[[website_name]]</p>\r\n', 'external_website_name,commission,username,website_name,website_logo,product_ids,total,currency,commission_type,script_name'),
			(12, '', 'wallet status change to in wallet', '[[amount]] credited in your wallet', '<p>Dear [[name]],</p>\r\n\r\n<p>[[comment]]</p>\r\n\r\n<p><br />\r\n[[website_name]]<br />\r\nSupport Team</p>\r\n', '', '', '', '', 'amount,comment,name,user_email,website_name,website_logo,new_status'),
			(13, '', 'User Registration From Integration', 'User Registration Successfully', '<p>Dear [[firstname]],</p>\r\n\r\n<p>Your new affiliate user account has been created welcome to the [[website_name]]</p>\r\n\r\n<p>your account details:</p>\r\n\r\n<p>================</p>\r\n\r\n<p>[[firstname]]</p>\r\n\r\n<p>[[username]]</p>\r\n\r\n<p>[[email]]</p>\r\n\r\n<h2>password is :&nbsp;<strong>[[password]]</strong></h2>\r\n\r\n<p><br />\r\n[[website_name]]<br />\r\nSupport Team</p>\r\n', 'Admin : New affiliate user Register From Integration', '', '', '<p>Dear Admin,</p>\r\n\r\n<p>&nbsp;New affiliate user Register on your site&nbsp;[[website_name]]</p>\r\n\r\n<p>Affiliate details:</p>\r\n\r\n<p>============</p>\r\n\r\n<p>[[firstname]]</p>\r\n\r\n<p>[[username]]</p>\r\n\r\n<p>[[email]]</p>\r\n\r\n<p><br />\r\n[[website_name]]<br />\r\nSupport Team</p>\r\n', 'firstname,lastname,email,username,password,website_name,website_logo'),
			(36, 'new_order_for_vendor', 'Vendor Got New Order', 'Vendor: You have new order from [[firstname]] [[lastname]]', '<p>Hello Vendor,</p>\r\n                    <p>you got new order from [[firstname]] [[lastname]]</p>\r\n                    <p>Commission: [[vendor_commission]] </p>\r\n                    <p>Order Status: [[status]] </p>\r\n                    <p><strong>Commission for product_name :&nbsp;</strong>[[product_name]]</p>\r\n                    [[website_name]]<br />\r\n                            Support Team</p>\r\n                ', '', '', '', '', 'vendor_firstname,vendor_lastname,vendor_commission,order_id,status,order_link,product_name,PhoneNumber,firstname,lastname,commission,total,currency_code,txn_id,website_name,website_logo,order_id'),
			(37, 'vendor_form_status_1', 'Vendor Form Status Change To Approved', 'Form Status Change To Approved', '<p>Dear, [[username]]</p>\r\n                                <p>Form Status Change to Approved</p>\r\n                                <p>Name [[title]]</p>\r\n                                <p><br />\r\n                            [[website_name]]<br />\r\n                            Support Team</p>\r\n                        ', '', '', '', '', 'admin_last_message,vendor_last_message,firstname,lastname,email,username,website_name,website_logo,title'),
			(38, 'vendor_create_product', 'Vendor Create new product', '', '', 'New Product Created By Vendor', '', '', '<p>Dear Admin,</p>\r\n                                <p>New Product has been created</p>\r\n                                <p>Name [[product_name]]</p>\r\n                                <p>Username [[username]]</p>\r\n                                <p><br />\r\n                            [[website_name]]<br />\r\n                            Support Team</p>\r\n                        ', 'admin_last_message,vendor_last_message,firstname,lastname,email,username,website_name,website_logo,product_name,product_short_description,product_price,product_sku,product_id'),
			(39, 'vendor_product_status_1', 'Vendor Product Status Change To Approved', 'Product Status Change To Approved', '<p>Dear, [[username]]</p>\r\n                                <p>Product Status Change to Approved</p>\r\n                                <p>Name [[product_name]]</p>\r\n                                <p><br />\r\n                            [[website_name]]<br />\r\n                            Support Team</p>\r\n                        ', '', '', '', '', 'admin_last_message,vendor_last_message,firstname,lastname,email,username,website_name,website_logo,product_name,product_short_description,product_price,product_sku,product_id'),
			(40, 'vendor_create_form', 'Vendor Create new product', '', '', 'New Form Created By Vendor', '', '', '<p>Dear Admin,</p>\r\n                                <p>New Form has been created</p>\r\n                                <p>Name [[title]]</p>\r\n                                <p>Username [[username]]</p>\r\n                                <p><br />\r\n                            [[website_name]]<br />\r\n                            Support Team</p>\r\n                        ', 'admin_last_message,vendor_last_message,firstname,lastname,email,username,website_name,website_logo,title'),
			(41, 'vendor_form_status_0', 'Vendor Form Status Change To In Review', 'Form Status Change To In Review', '<p>Dear,</p>\r\n                                <p>Form Status Change to In Review</p>\r\n                                <p>Name [[title]]</p>\r\n                                <p><br />\r\n                            [[website_name]]<br />\r\n                            Support Team</p>\r\n                        ', 'Form Status Change To In Review', '', '', '<p>Dear,</p>\r\n                                <p>Form Status Change to In Review</p>\r\n                                <p>Name [[title]]</p>\r\n                                <p><br />\r\n                            [[website_name]]<br />\r\n                            Support Team</p>\r\n                        ', 'admin_last_message,vendor_last_message,firstname,lastname,email,username,website_name,website_logo,title'),
			(42, 'vendor_form_status_2', 'Vendor Form Status Change To Denied', 'Form Status Change To Denied', '<p>Dear, [[username]]</p>\r\n                                <p>Form Status Change to Denied</p>\r\n                                <p>Name [[title]]</p>\r\n                                <p><br />\r\n                            [[website_name]]<br />\r\n                            Support Team</p>\r\n                        ', '', '', '', '', 'admin_last_message,vendor_last_message,firstname,lastname,email,username,website_name,website_logo,title'),
			(43, 'vendor_order_status_complete', 'Vendor Order Status Has Been Change', 'Vendor: New Order Commission From [[firstname]] [[lastname]]', '<p>Hello Vendor,</p>\r\n                    <p>you got new order Sale Commission from sale thats done under [[firstname]] [[lastname]]</p>\r\n                    <p>Commission: [[vendor_commission]] </p>\r\n                    <p><strong>Commission for product_name :&nbsp;</strong>[[product_name]]</p>\r\n                    [[website_name]]<br />\r\n                            Support Team</p>\r\n                ', '', '', '', '', 'vendor_firstname,vendor_lastname,vendor_commission,order_id,status,order_link,product_name,commission_type,PhoneNumber,firstname,lastname,commission,total,currency_code,txn_id,website_name,website_logo,order_id'),
			(45, 'vendor_create_program', 'Vendor Create new product', '', '', 'New Program Created By Vendor : [[name]]', '', '', '<p>Dear Admin,</p>\r\n                    <p>New Program has been created</p>\r\n                    <p>Name [[name]]</p>\r\n                    <p>Username [[username]]</p>\r\n                    <p><br />\r\n                [[website_name]]<br />\r\n                Support Team</p>\r\n            ', 'admin_last_message,vendor_last_message,firstname,lastname,email,username,website_name,website_logo,name'),
			(46, 'vendor_program_status_2', 'Vendor Program Status Change To Denied', 'Program Status Change To Denied', '<p>Dear,</p>\r\n                    <p>Program Status Change to Denied</p>\r\n                    <p>Name [[name]]</p>\r\n                    <p><br />\r\n                [[website_name]]<br />\r\n                Support Team</p>\r\n            ', 'Program Status Change To Denied', '', '', '<p>Dear,</p>\r\n                    <p>Program Status Change to Denied</p>\r\n                    <p>Name [[name]]</p>\r\n                    <p><br />\r\n                [[website_name]]<br />\r\n                Support Team</p>\r\n            ', 'admin_last_message,vendor_last_message,firstname,lastname,email,username,website_name,website_logo,name'),
			(47, 'vendor_program_status_3', 'Vendor Program Status Change To Ask To Edit', 'Program Status Change To Ask To Edit', '<p>Dear,</p>\r\n                    <p>Program Status Change to Ask To Edit</p>\r\n                    <p>Name [[name]]</p>\r\n                    <p><br />\r\n                [[website_name]]<br />\r\n                Support Team</p>\r\n            ', 'Program Status Change To Ask To Edit', '', '', '<p>Dear,</p>\r\n                    <p>Program Status Change to Ask To Edit</p>\r\n                    <p>Name [[name]]</p>\r\n                    <p><br />\r\n                [[website_name]]<br />\r\n                Support Team</p>\r\n            ', 'admin_last_message,vendor_last_message,firstname,lastname,email,username,website_name,website_logo,name'),
			(48, 'vendor_program_status_0', 'Vendor Program Status Change To In Review', 'Program Status Change To In Review', '<p>Dear,</p>\r\n                    <p>Program Status Change to In Review</p>\r\n                    <p>Name [[name]]</p>\r\n                    <p><br />\r\n                [[website_name]]<br />\r\n                Support Team</p>\r\n            ', 'Program Status Change To In Review', '', '', '<p>Dear,</p>\r\n                    <p>Program Status Change to In Review</p>\r\n                    <p>Name [[name]]</p>\r\n                    <p><br />\r\n                [[website_name]]<br />\r\n                Support Team</p>\r\n            ', 'admin_last_message,vendor_last_message,firstname,lastname,email,username,website_name,website_logo,name'),
			(49, 'vendor_program_status_1', 'Vendor Program Status Change To Approved', 'Program Status Change To Approved', '<p>Dear,</p>\r\n                    <p>Program Status Change to Approved</p>\r\n                    <p>Name [[name]]</p>\r\n                    <p><br />\r\n                [[website_name]]<br />\r\n                Support Team</p>\r\n            ', 'Program Status Change To Approved', '', '', '<p>Dear,</p>\r\n                    <p>Program Status Change to Approved</p>\r\n                    <p>Name [[name]]</p>\r\n                    <p><br />\r\n                [[website_name]]<br />\r\n                Support Team</p>\r\n            ', 'admin_last_message,vendor_last_message,firstname,lastname,email,username,website_name,website_logo,name'),
			(51, 'vendor_create_ads', 'Vendor Create Ads (Banner, Text, Link, Video)', '', '', 'New Ads ([[type]]) Created By Vendor', '', '', '<p>Dear Admin,</p>\r\n                    <p>New Ads - [[type]] has been created</p>\r\n                    <p>Name [[name]]</p>\r\n                    <p>Username [[username]]</p>\r\n                    <p><br />\r\n                [[website_name]]<br />\r\n                Support Team</p>\r\n            ', 'admin_last_message,vendor_last_message,firstname,lastname,email,username,website_name,website_logo,name,type,tool_type'),
			(52, 'vendor_ads_status_1', 'Vendor Ads (Banner, Text, Link, Video) Status Change To Approved', 'Ads ([[type]]) Status Change To Approved', '<p>Dear</p>\r\n                    <p>Ads - [[type]] Status Change to Approved </p>\r\n                    <p>Name [[name]]</p>\r\n                    <p>Username [[username]]</p>\r\n                    <p><br />\r\n                [[website_name]]<br />\r\n                Support Team</p>\r\n            ', 'Ads ([[type]]) Status Change To Approved', '', '', '<p>Dear</p>\r\n                    <p>Ads - [[type]] Status Change to Approved </p>\r\n                    <p>Name [[name]]</p>\r\n                    <p>Username [[username]]</p>\r\n                    <p><br />\r\n                [[website_name]]<br />\r\n                Support Team</p>\r\n            ', 'admin_last_message,vendor_last_message,firstname,lastname,email,username,website_name,website_logo,name,type,tool_type'),
			(53, 'vendor_ads_status_0', 'Vendor Ads (Banner, Text, Link, Video) Status Change To In Review', 'Ads ([[type]]) Status Change To In Review', '<p>Dear</p>\r\n                    <p>Ads - [[type]] Status Change to In Review </p>\r\n                    <p>Name [[name]]</p>\r\n                    <p>Username [[username]]</p>\r\n                    <p><br />\r\n                [[website_name]]<br />\r\n                Support Team</p>\r\n            ', 'Ads ([[type]]) Status Change To In Review', '', '', '<p>Dear</p>\r\n                    <p>Ads - [[type]] Status Change to In Review </p>\r\n                    <p>Name [[name]]</p>\r\n                    <p>Username [[username]]</p>\r\n                    <p><br />\r\n                [[website_name]]<br />\r\n                Support Team</p>\r\n            ', 'admin_last_message,vendor_last_message,firstname,lastname,email,username,website_name,website_logo,name,type,tool_type'),
			(54, 'vendor_ads_status_3', 'Vendor Ads (Banner, Text, Link, Video) Status Change To Ask To Edit', 'Ads ([[type]]) Status Change To Ask To Edit', '<p>Dear</p>\r\n                    <p>Ads - [[type]] Status Change to Ask To Edit</p>\r\n                    <p>Name [[name]]</p>\r\n                    <p>Username [[username]]</p>\r\n                    <p><br />\r\n                [[website_name]]<br />\r\n                Support Team</p>\r\n            ', 'Ads ([[type]]) Status Change To Ask To Edit', '', '', '<p>Dear</p>\r\n                    <p>Ads - [[type]] Status Change to Ask To Edit</p>\r\n                    <p>Name [[name]]</p>\r\n                    <p>Username [[username]]</p>\r\n                    <p><br />\r\n                [[website_name]]<br />\r\n                Support Team</p>\r\n            ', 'admin_last_message,vendor_last_message,firstname,lastname,email,username,website_name,website_logo,name,type,tool_type'),
			(55, 'order_on_vendor_program', 'New Order in Vendor Program', 'New Order Create In Your Program', '<p>Dear Vendor,</p>\r\n                    <p>New Order Created under your Program</p>\r\n                    <p><b>Website</b> : [[external_website_name]]</p>\r\n                    <p><b>Total</b> : [[total]]</p>\r\n                    <p><br />\r\n                [[website_name]]<br />\r\n                Support Team</p>\r\n            ', '', '', '', '', 'external_website_name,commission,username,website_name,website_logo,product_ids,total,currency,commission_type,script_name'),
			(57, 'withdrwal_status_change', 'Withdrawal Request Status Changed', 'Withdrawal Request Status Changed', '<p>Dear,</p>\r\n                <p>Your Withdrawal Request #([[request_id]]) Status has been change to <b><i>[[status]]</i></b></p>\r\n\r\n                    <p>Comment: [[comment]] </p>\r\n                [[website_name]]<br />\r\n                Support Team</p>\r\n            ', '', NULL, NULL, '', 'comment,status,request_id,firstname,lastname,email,username,website_name,website_logo,name'),
			(59, 'send_register_mail_api', 'User Registration (API)', 'Your Account Created Successfully On [[website_name]]', '<p>Welcome to [[website_name]]</p>\r\n\r\n<p>Dear [[firstname]],</p>\r\n\r\n<p>Thanks for signing up [[website_name]].</p>\r\n\r\n<p>Your&nbsp;Login&nbsp;credentials:</p>\r\n\r\n<p>Username:&nbsp;<strong>[[username]]</strong><br />\r\nPassword:&nbsp;<strong>*******</strong></p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p><a href=\"[[website_url]]\">Login To [[website_name]]</a></p>\r\n\r\n<p><br />\r\n[[website_name]]<br />\r\nSupport Team</p>\r\n', 'User Registration Successfully', NULL, NULL, '<p>Dear Admin,</p>\r\n\r\n<p>New affiliate user Register on your site&nbsp;[[website_name]]</p>\r\n\r\n<p>Affiliate details:</p>\r\n\r\n<p>============</p>\r\n\r\n<p>[[firstname]]</p>\r\n\r\n<p>[[username]]</p>\r\n\r\n<p>[[email]]</p>\r\n\r\n<p><br />\r\n[[website_name]]<br />\r\nSupport Team</p>\r\n', 'firstname,lastname,email,username,website_url,website_name,website_logo,name'),
			(63, 'subscription_status_change', 'Subscription Status Changed', 'Subscription Status Changed', '<p>Dear [[firstname]],</p>\r\n                <p>Your subscription status has been changed to [[status_text]]</p>\r\n                <p>Comment: [[comment]] </p>\r\n                [[website_name]]<br />\r\n                Support Team</p>', '', NULL, NULL, '', 'comment,planname,price,expire_at,started_at,status_text,firstname,lastname,email,username,website_url,website_name,website_logo,name'),
			(64, 'subscription_buy', 'Subscription Buy', 'Subscription Buy', '<h2>Thanks for your order</h2>\r\n\r\n<p>Welcome to Prime. As a Prime member, enjoy these great benefits. If you have any questions, call us any time at or simply reply to this email.</p>\r\n', 'New Subscription Buy From [[firstname]] [[lastname]]', NULL, NULL, '<h2>Thanks for your order</h2>\r\n\r\n<p>Welcome to Prime. As a Prime member, enjoy these great benefits. If you have any questions, call us any time at or simply reply to this email.</p>\r\n', 'planname,price,expire_at,started_at,firstname,lastname,email,username,website_url,website_name,website_logo,name'),
			(65, 'subscription_expire_notification', 'Subscription Expire Notification', 'Your Subscription Will Be Expired Soon.', '<p>customText</p>\r\n', NULL, NULL, NULL, NULL, 'planname,price,expire_at,started_at,firstname,lastname,email,username,website_url,website_name,website_logo,name'),
			(66, 'wallet_noti_on_hold_wallet', 'Wallet Status Change To On Hold', '[[amount]] is put on hold in your wallet', '<p>Dear [[name]],</p>\n        <p>Transactions #[[id]] status changed to [[new_status]]. amount is [[amount]]</p>\n        <p><br />\n        [[website_name]]<br />\n        Support Team</p>\n', '', NULL, NULL, NULL, 'amount,id,name,new_status,user_email,website_name,website_logo,name'),
			(67, 'new_user_request', 'New User Request', 'User Registration Successfull', '<p>Dear [[firstname]] [[lastname]],</p>\r\n\r\n<p>User account has been registered successfully on [[website_name]], please wait while system admin apporve&nbsp;your request.<br />\r\nWe will inform you once account has been approved, Thank You.</p>\r\n\r\n<p>Support Team<br />\r\n[[website_name]]</p>\r\n', 'New User Registration - Approval Pending', NULL, NULL, '<p>Dear Admin,</p>\r\n\r\n<p>New user has been registered on [[website_name]], apporval is pending yet!</p>\r\n\r\n<p>User Details</p>\r\n\r\n<p>Name : [[firstname]][[lastname]]<br />\r\nEmail :&nbsp;[[email]]<br />\r\nUsername : [[username]]<br />\r\nSupport Team<br />\r\n[[website_name]]</p>', 'firstname,lastname,email,username,website_name,website_logo'),
			(68, 'new_user_approved', 'New User Request Approved', 'User Account Approved', '<p>Dear [[firstname]] [[lastname]],</p>\r\n\r\n<p>Your new user account registration request is accepted by admin, you can login and use services.</p>\r\n\r\n<p>[[website_name]]<br />\r\nSupport Team</p>\r\n', 'User Account Approved', NULL, NULL, '<p>Dear Admin,</p>\r\n\r\n<p>You have approced registration request of user having</p>\r\n\r\n<p>Name : [[firstname]]&nbsp;[[lastname]]</p>\r\n\r\n<p>Email : [[email]]</p>\r\n\r\n<p>Username : [[username]]</p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p>Support Team</p>\r\n\r\n<p>[[website_name]]</p>\r\n', 'firstname,lastname,email,username,website_name,website_logo'),
			(69, 'new_user_declined', 'New User Request Declined', 'User Account Declined', '<p>Dear [[firstname]] [[lastname]],</p>\r\n\r\n<p>Your new user account registration request is declined by admin, for more information please contact supprt team</p>\r\n\r\n<p>[[website_name]]<br />\r\nSupport Team</p>\r\n', 'User Account Declined', NULL, NULL, '<p>Dear Admin,</p>\r\n\r\n<p>You have declined registration request of user having</p>\r\n\r\n<p>Name : [[firstname]]&nbsp;[[lastname]]</p>\r\n\r\n<p>Email : [[email]]</p>\r\n\r\n<p>Username : [[username]]</p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p>Support Team</p>\r\n\r\n<p>[[website_name]]</p>\r\n', 'firstname,lastname,email,username,website_name,website_logo'),
			(70, 'new_vendor_deposit_request', 'New Vendor Deposit Request', 'New Deposit Request Added', '<p>Dear [[firstname]] [[lastname]],</p>\r\n\r\n<p>Your deposit request of amount [[amount]] is added, if your balance not updated please contact support team</p>\r\n\r\n<p>[[website_name]]<br /> \r\n Support Team</p>', 'New Deposit Request Added', '', '', '<p>Dear Admin,</p>\r\n\r\n<p>You have new deposit request of amount [[amount]] from vendor having</p>\r\n\r\n<p>Name : [[firstname]]&nbsp;[[lastname]]</p>\r\n\r\n<p>Email : [[email]]</p>\r\n\r\n<p>Username : [[username]]</p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p>Support Team</p>\r\n\r\n<p>[[website_name]]</p>', 'status,amount,firstname,lastname,email,username,website_name,website_logo'),
			(71, 'vendor_deposit_request_updated', 'Deposit Request Updated', 'Deposit Request Updated', '<p>Dear [[firstname]] [[lastname]],</p>\r\n\r\n<p>Your deposit request of amount [[amount]] is updated to [[status]], if have any queries please contact support team</p>\r\n\r\n<p>[[website_name]]<br /> \r\n Support Team</p>', 'Deposit Request Updated', '', '', '<p>Dear Admin,</p>\r\n\r\n<p>You have changed status of deposit request to [[status]] from vendor having</p>\r\n\r\n<p>Name : [[firstname]]&nbsp;[[lastname]]</p>\r\n\r\n<p>Email : [[email]]</p>\r\n\r\n<p>Username : [[username]]</p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p>Support Team</p>\r\n\r\n<p>[[website_name]]</p>', 'status,amount,firstname,lastname,email,username,website_name,website_logo'),
			(72, 'user_level_changed', 'Change user level', 'Your user level changed', '<p>Dear,</p><p>Your level changed from [[from_level]] to [[to_level]]</p>                     <p><br>                 [[website_name]]<br>                 Support Team</p>             ', '', '', '', '', 'from_level,to_level,website_name'),
			(73, 'ticket_created_email', 'Ticket Created Email', 'New ticket #[[ticket_id]] has been created', '<p>Dear [[firstname]],&nbsp;</p><p><br></p><p>Your ticket has been created successfully on the system. Please note down below the ticket number for future reference.</p><p><br></p><p>Ticket ID:</p><p><span style=\"font-size: 1rem;\">[[ticket_id]]</span><br></p><p><br></p><p><span style=\"font-size: 1rem;\">Ticket Status:</span><br></p><p><span style=\"font-size: 1rem;\">[[ticket_status]]</span><br></p><p><br></p><p><br></p><p>Subject :</p><p><span style=\"font-size: 1rem;\">[[ticket_subject]]</span><br></p><p><br></p><p><br></p><p>Message:</p><p><span style=\"font-size: 1rem;\">[[ticket_body]]</span><br></p><p><br></p><p><span style=\"font-size: 1rem;\">We will contact you very soon.</span><br></p><p><br></p><p><span style=\"font-size: 1rem;\">Thank You</span><br></p><p><span style=\"font-size: 1rem;\">Support Team</span><br></p>', 'New user ticket #[[ticket_id]] has been created', '', '', '<p>Dear Admin, </p><p><br></p><p>The user has created a new ticket on your site [[website_name]]. <br></p><p><br></p><p>Username:</p><p><span style=\"font-size: 1rem;\">[[username]]</span><br></p><p><br></p><p>Email:</p><p><span style=\"font-size: 1rem;\">[[email]]</span><br></p><p><br></p><p>Name:</p><p><span style=\"font-size: 1rem;\">[[firstname]] [[lastname]]</span><br></p><p><br></p><p>Ticket ID:</p><p><span style=\"font-size: 1rem;\">[[ticket_id]]</span><br></p><p><br></p><p>Ticket Status:</p><p><span style=\"font-size: 1rem;\">[[ticket_status]]</span><br></p><p><br></p><p><span style=\"font-size: 1rem;\">Subject :</span><br></p><p><span style=\"font-size: 1rem;\">[[ticket_subject]]</span><br></p><p><br></p><p><br></p><p>Message:</p><p><span style=\"font-size: 1rem;\">[[ticket_body]]</span><br></p><p><br></p><p><br></p><p>Thank You</p><p><span style=\"font-size: 1rem;\">[[website_name]]</span><br></p><p><br></p>', 'ticket_id,ticket_status,ticket_subject,ticket_body,ticket_datetime,firstname,lastname,email,username,website_name,website_logo'),
			(74, 'ticket_reply_email', 'Ticket Replied Email', 'You have a new reply on ticket #[[ticket_id]]', '<p>Dear [[firstname]], </p><p><br></p><p>You have a reply from the support team on your ticket #[[ticket_id]]</p><p><br></p><p>Ticket ID:</p><p><span style=\"font-size: 1rem;\">[[ticket_id]]</span><br></p><p><br></p><p><span style=\"font-size: 1rem;\">Ticket Status:</span><br></p><p><span style=\"font-size: 1rem;\">[[ticket_status]]</span><br></p><p><br></p><p><span style=\"font-size: 1rem;\">Subject :</span><br></p><p><span style=\"font-size: 1rem;\">[[ticket_subject]]</span><br></p><p><br></p><p>Message from the support team<br></p><p><span style=\"font-size: 1rem;\">[[ticket_reply_message]]</span></p><p><span style=\"font-size: 1rem;\"><br></span></p><p><span style=\"font-size: 1rem;\">Time</span></p><p><span style=\"font-size: 1rem;\">[[reply_datetime]]</span></p><p><span style=\"font-size: 1rem;\"><br></span></p><p><span style=\"font-size: 1rem;\">Thank You</span><br></p>', 'User added a new reply on ticket #[[ticket_id]]', '', '', '<p>Dear Admin, </p><p><br></p><p>User added a new reply on ticket #[[ticket_id]]</p><p><br></p><p>Username:</p><p><span style=\"font-size: 1rem;\">[[username]]</span><br></p><p><br></p><p>Email:</p><p><span style=\"font-size: 1rem;\">[[email]]</span><br></p><p><br></p><p>Name:</p><p><span style=\"font-size: 1rem;\">[[firstname]] [[lastname]]</span></p><p><br></p><p>Ticket ID:</p><p><span style=\"font-size: 1rem;\">[[ticket_id]]</span><br></p><p><br></p><p><span style=\"font-size: 1rem;\">Ticket Status:</span><br></p><p><span style=\"font-size: 1rem;\">[[ticket_status]]</span><br></p><p><br></p><p><span style=\"font-size: 1rem;\">Subject :</span><br></p><p><span style=\"font-size: 1rem;\">[[ticket_subject]]</span><br></p><p><br></p><p>Message from user<br></p><p><span style=\"font-size: 1rem;\">[[ticket_reply_message]]</span></p><p><span style=\"font-size: 1rem;\"><br></span></p><p><span style=\"font-size: 1rem;\">Time</span></p><p><span style=\"font-size: 1rem;\">[[reply_datetime]]</span></p><p><span style=\"font-size: 1rem;\"><br></span></p><p><span style=\"font-size: 1rem;\">Thank You</span></p>', 'ticket_id,ticket_status,ticket_subject,ticket_body,ticket_reply_message,reply_datetime,firstname,lastname,email,username,website_name,website_logo'),
			(75, 'ticket_status_email', 'Ticket Status Change Email', 'Ticket #[[ticket_id]] status has been updated', '<p>Dear [[firstname]],&nbsp;</p><p><br></p><p>The status of a ticket having id [[ticket_id]] has been updated, please log in to [[website_name]] to see full details of the ticket.</p><p><br></p><p>Ticket ID:</p><p><span style=\"font-size: 1rem;\">[[ticket_id]]</span><br></p><p><br></p><p><span style=\"font-size: 1rem;\">Ticket Status:</span><br></p><p><span style=\"font-size: 1rem;\">[[ticket_status]]</span><br></p><p><br></p><p><span style=\"font-size: 1rem;\">Subject :</span><br></p><p><span style=\"font-size: 1rem;\">[[ticket_subject]]</span><br></p><p><br></p><p><span style=\"font-size: 1rem;\">Thank You</span></p><p><span style=\"font-size: 1rem;\">Support Team<br></span><br></p>', 'Ticket #[[ticket_id]] status has been updated', '', '', '<p>Dear Admin,&nbsp;</p><p><br></p><p>The status of the ticket having id [[ticket_id]] has been updated.</p><p><br></p><p>Username:</p><p><span style=\"font-size: 1rem;\">[[username]]</span><br></p><p><br></p><p>Email:</p><p><span style=\"font-size: 1rem;\">[[email]]</span><br></p><p><br></p><p>Name:</p><p><span style=\"font-size: 1rem;\">[[firstname]] [[lastname]]</span></p><p><span style=\"font-size: 1rem;\"><br></span></p><p>Ticket ID:</p><p><span style=\"font-size: 1rem;\">[[ticket_id]]</span><br></p><p><br></p><p><span style=\"font-size: 1rem;\">Ticket Status:</span><br></p><p><span style=\"font-size: 1rem;\">[[ticket_status]]</span><br></p><p><br></p><p><span style=\"font-size: 1rem;\">Subject :</span><br></p><p><span style=\"font-size: 1rem;\">[[ticket_subject]]</span><br></p><p><br></p><p><span style=\"font-size: 1rem;\">Thank You</span></p><p><span style=\"font-size: 1rem;\">Support Team<br></span></p>', 'ticket_id,ticket_status,ticket_subject,ticket_body,firstname,lastname,email,username,website_name,website_logo');
			");

			$folder_path = [];

			$folder_path[] =  FCPATH."assets/images/product/upload/thumb/";

			$folder_path[] =  FCPATH."assets/images/product/upload/";

			$folder_path[] =  FCPATH."assets/user_upload/";

			$folder_path[] =  FCPATH."application/logs/";

			$folder_path[] =  FCPATH."application/logs/system_update_logs/";

			$folder_path[] =  FCPATH."application/cache/";

			$folder_path[] =  FCPATH."application/backup/mysql/";

			$folder_path[] =  FCPATH."application/core/excel/output/";

			$folder_path[] =  FCPATH."application/downloads/";

			$folder_path[] =  FCPATH."application/downloads_order/";

			$folder_path[] =  FCPATH."assets/integration/uploads/";

			$folder_path[] =  FCPATH."application/market_cache/";

			$folder_path[] =  FCPATH."application/logs/system_update_logs/";

			$folder_path[] =  FCPATH."assets/image_cache/cache/assets/images/form/favi/";

			$folder_path[] =  FCPATH."assets/image_cache/cache/assets/images/payments/";

			$folder_path[] =  FCPATH."assets/image_cache/cache/assets/images/product/upload/thumb/";

			$folder_path[] =  FCPATH."assets/image_cache/cache/assets/images/site/";

			$folder_path[] =  FCPATH."assets/image_cache/cache/assets/images/themes/";

			$folder_path[] =  FCPATH."assets/image_cache/cache/assets/images/wallet-icon/";

			$folder_path[] =  FCPATH."assets/image_cache/cache/assets/vertical/assets/images/users/";

			$folder_path[] =  FCPATH."assets/images/form/favi/";

			$folder_path[] =  FCPATH."assets/images/site/";

			$folder_path[] =  FCPATH."assets/images/theme_images/";

			$folder_path[] =  FCPATH."assets/images/users/";

			$folder_path[] =  FCPATH."assets/images/users/thumb/";

			$folder_path[] =  FCPATH."assets/integration/uploads/";

			$folder_path[] =  FCPATH."assets/user_upload/";

			$folder_path[] =  FCPATH."assets/user_upload/downloaded_tools/";

			$folder_path[] =  FCPATH."assets/user_upload/mail_template_images/";

			$folder_path[] =  FCPATH."assets/user_upload/vendor_store/";

			$folder_path[] =  FCPATH."assets/xml/";



			foreach ($folder_path as $key => $value) {

				$files = glob($value.'/*');

				foreach($files as $file) { 

					if(is_file($file) && ! str_contains($file, 'index.html'))  unlink($file);  

				}

			}

		$this->deleteAll(FCPATH."assets/integration/uploads", false);
		$this->deleteAll(FCPATH."application/backup/mysql", false);
		$this->deleteAll(FCPATH."application/backup", false);
		$this->deleteAll(FCPATH."application/downloads", false);
		$this->deleteAll(FCPATH."application/downloads_order", false);
		$this->deleteAll(FCPATH."application/logs", false);
		$this->deleteAll(FCPATH."application/logs/system_update_logs", false);


		$u = $this->session->administrator;

		$user_details_array = $this->db->query("SELECT * FROM users WHERE id=". $u['id'])->row_array();

		$this->session->set_userdata(array('administrator'=>$user_details_array));

		$this->session->set_flashdata('success', __('admin.data_was_deleted_successfully'));

		$json['success'] = true;

		}

		} else {
			$json['errors']['admin_password'] = "Wrong Password..!";
		}

		echo json_encode($json);

		}

		// function to delete all files and subfolders from folder
		public function deleteAll($dir, $remove = false) {
			$structure = glob(rtrim($dir, "/").'/*');

			if (is_array($structure)) {
				foreach($structure as $file) {
					if (is_dir($file))
						$this->deleteAll($file,true);
					else if(is_file($file)  && ! str_contains($file, 'index.html'))
						unlink($file);
				}
			}

			if($remove) rmdir($dir);
		}


			public function logs(){

				$data = array();

				$input = $this->input->post(null,true);

				$filter = array();

				$data['status'] = $this->Wallet_model->status();

				$data['status_icon'] = $this->Wallet_model->status_icon;

				if($input['type'] == 'sale'){

					$data['title'] = "Sales Logs";

					$record = $this->db->query('SELECT o.* FROM `order_products` op LEFT JOIN `order` as o ON o.id = op.order_id WHERE o.status = 1')->result_array();

						$order_status = $this->Order_model->status();

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

					else if($input['type'] == 'hold_orders'){

						$data['title'] = "Hold Orders Logs";

						$order_status = $this->Order_model->status();

						$record = $this->db->query('SELECT o.* FROM `order_products` op LEFT JOIN `order` as o ON o.id = op.order_id WHERE o.status = 7')->result_array();

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

					else if($input['type'] == 'orders'){

						$order_status = $this->Order_model->status();

						$data['title'] = "Digital Orders";

						$record = $this->db->query('SELECT o.* FROM `order_products` op LEFT JOIN `order` as o ON o.id = op.order_id WHERE o.status > 0')->result_array();

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

						$record = $this->db->query('SELECT * FROM `integration_orders`')->result_array();

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

					else if($input['type'] == 'click'){

						$data['title'] = "Wallet Logs";

						$data['title2'] = "Clicks Logs";

						$record = $this->db->query('SELECT * FROM wallet WHERE type IN ("click_commission","form_click_commission","affiliate_click_commission") AND comm_from = "store" AND status > 0')->result_array();

						$_record = array();

						foreach ($record as $_key => $value) {

							$_record[] = array(

								'created_at'   => $value['created_at'],

								'comment'      => $value['comment'],

								'status'       => $data['status'][$value['status']],

								'country_code' => $value['country_code'],

								'user_ip'      => json_decode($value['ip_details'], true)['ip'],

								'amount'       => $value['amount'],

							);
						}

						$data['data'] = $_record;

						$record = array();

						$record[] = $this->db->query('SELECT country_code,created_at,user_ip,pay_commition,"Product Click" as type  FROM product_action WHERE  1')->result_array();

						$record[] = $this->db->query('SELECT country_code,created_at,user_ip,pay_commition,"Form Click" as type  FROM form_action WHERE 1')->result_array();

						$record[] = $this->db->query('SELECT country_code,created_at,user_ip,commission as pay_commition,"Affiliate Click" as type FROM affiliate_action WHERE 1')->result_array();



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

					else if($input['type'] == 'action'){

						$data['title'] = "Action Logs";

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

					else if($input['type'] == 'member'){

						$data['title'] = "Member";

						$data['type'] = "members";



						$record = $this->db->query("SELECT u.created_at,c.name,c.sortname,u.firstname,u.lastname,u.email,u.username

							FROM users as u 

							LEFT JOIN countries c ON c.id = u.Country

							WHERE type='client' ORDER BY created_at DESC")->result_array();



						$data['data'] = array();

						foreach ($record as $key => $value) {

							if ($value['sortname'] != '') {

								$flag = base_url('assets/vertical/assets/images/flags/' . strtolower($value['sortname']) . '.png');

							} else {

								$flag = base_url('assets/vertical/assets/images/users/avatar-1.png');

							}

							$data['data'][] = array(

								'name'     => $value['firstname'] ." " .$value['lastname'],

								'username' => $value['username'],

								'sortname' => $value['sortname'],

								'email'    => $value['email'],

								'created_at'    => $value['created_at'],

								'flag'     => $flag,

							);

						}

					}

					$data['html'] = $this->load->view("common/log_model",$data,true);

					echo json_encode($data);die;

				}

				public function page_404(){
					$this->load->view("404");
				}


				public function install_new_version(){
					$userdetails = $this->userdetails();

					$this->view($data,'setting/install_new_version');
				}


				public function language_import(){

					$userdetails = $this->userdetails();

					$files = ['admin','client','store','user','front','template_simple'];

					require_once APPPATH . '/core/phpspreadsheet/autoload.php';

					$json = array();

					$translation_id = (int)$this->input->post('id',true);

					$language = $this->db->query("SELECT * FROM language WHERE id=".$translation_id)->row_array();

					if(!$language){

						$json['warning'] = "Something Wrong.!";
					}

					if(!isset($_FILES['file']['error']) || $_FILES['file']['error'] != 0){

						$json['warning'] = "Please Select Excel File..!";

					} else {

						$extension = pathinfo($_FILES['file']["name"], PATHINFO_EXTENSION);

						if($extension != 'xlsx'){

							$json['warning'] = "Only xlsx files are allowed.!";
						}

					}

					if(!isset($json['warning'])){

						$inputFileName = $_FILES['file']['tmp_name'];

						$objReader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');

						$worksheetList = $objReader->listWorksheetNames($inputFileName);

						$sheetname = $worksheetList[0];

						foreach ($files as $key => $file) {

							if(!in_array($file, $worksheetList)){

								$json['warning'] = "Sheet <b>{$file}</b> is missing check your excel file..!";

								break;
							}
						}

						$lang_data = array();

						if(!isset($json['warning'])){

							foreach ($files as $key => $file) {

								$objReader->setLoadSheetsOnly($file); 

								$objPHPExcel = $objReader->load($inputFileName);

								$worksheet = $objPHPExcel->getActiveSheet();

								$l = $worksheet->toArray(null,true,true,true);

								unset($l[1]);

								foreach ($l as $key => $value) {

									$lang_data[$file][$value['A']] = $value['B'];
								}
							}

							$translation_id = (int)$this->input->post('id',true);

							foreach ($lang_data as $file => $data) {

								$path = APPPATH.'language/'. $translation_id."/".$file.".php";

								$file_content = '<?php '.PHP_EOL;

								foreach ($data as $key => $value) {

									$file_content .= '$lang[\''. $key .'\'] = '. $this->db->escape($value) .';' .PHP_EOL;

								}

								file_put_contents($path, $file_content);
							}

							$json['success'] = "Languages file imported successfully..!";
						}

					}

					echo json_encode($json);die;
				}


				public function language_export($id = 'default'){

					$userdetails = $this->userdetails();

					$files = ['admin','client','store','user','front','template_simple'];

					require_once APPPATH . '/core/phpspreadsheet/autoload.php';

					if($id == "1") $id = 'default';

					$objPHPExcel = new \PhpOffice\PhpSpreadsheet\Spreadsheet();

					$sheet = $objPHPExcel->getActiveSheet();

					$objWriter = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($objPHPExcel);

					foreach ($files as $i => $file) {

						if(is_file(APPPATH.'/language/'. $id .'/'. $file .'.php')){

							$lang = array();

							require  APPPATH.'/language/default/'. $file .'.php';

							$defaultLang = $lang;

							$lang = array();

							require  APPPATH.'/language/'. $id .'/'. $file .'.php';

							$objWorkSheet = $objPHPExcel->createSheet($i);

							$data = array();

							$data[] = array('KEY','TRANSLATION');

							$lang = array_merge($defaultLang, $lang);

							foreach ($lang as $key => $value) {

								$data[] = array($key,$value);

							}

							$objWorkSheet->fromArray($data, NULL, 'A1');

							$objWorkSheet->setTitle($file);
						}
					}


					header('Content-type: application/vnd.ms-excel');

					header('Content-Disposition: attachment; filename="'. $id .'.xlsx"');

					$objWriter->save('php://output');
				}


				public function language(){

					$userdetails = $this->userdetails();

					$language = $this->db->query("SELECT * FROM language ")->result_array();

					$data['language_count'] = langCount('default');

					foreach ($language as $key => $value) {

						$data['language'][$key] = $value;

						$data['language'][$key]['count'] = langCount($value['id']);

					}

					$this->view($data,'language/index');
				}



				public function coupon_manage($coupon_id = 0){

					$userdetails = $this->userdetails();

					$this->load->model("Coupon_model");

					$data['coupon'] = $this->Coupon_model->getCoupon($coupon_id);

					$data['product'] = $this->db->query("SELECT product_id,product_name FROM product")->result_array();


					$this->view($data,'coupon/form');
				}



				public function coupon_delete($coupon_id){

					$userdetails = $this->userdetails();

					$this->load->model("Coupon_model");

					$this->Coupon_model->deleteCoupon($coupon_id);

					$this->session->set_flashdata('success', __('admin.coupon_deleted_successfully'));

					redirect(base_url("admincontrol/listproduct"));
				}


				public function coupon(){

					$userdetails = $this->userdetails();

					$this->load->model("Coupon_model");

					$data['coupons'] = $this->Coupon_model->getCoupons();

					$ptotal = $this->db->query('SELECT product_id FROM product')->num_rows();



					foreach ($data['coupons'] as $key => $value) {

						if(strtolower($value['allow_for']) == 's'){

							$data['coupons'][$key]['product_count'] = count(explode(',', $value['products']));

						}else{

							$data['coupons'][$key]['product_count'] = $ptotal;

						}

						$data['coupons'][$key]['count_coupon'] = $this->Coupon_model->getCouponCount($value['coupon_id']);

					}

					$this->view($data,'coupon/index');
				}


				public function save_coupon(){

					$userdetails = $this->userdetails();

					$this->load->library('form_validation');

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

						$data = $this->input->post(null,true);
						$product_array = isset($data['products']) && is_array($data['products']) ? $data['products'] : []; 
						
						$coupon = array(

							'name'       => $data['name'],

							'code'       => $data['code'],

							'type'       => $data['type'],

							'allow_for'  => $data['allow_for'],

							'discount'   => $data['discount'],

							'date_start' => date("Y-m-d", strtotime($data['date_start'])),

							'date_end'   => date("Y-m-d", strtotime($data['date_end'])),

							'uses_total' => $data['uses_total'],

							'status'     => $data['status'],

							'products'   => implode(",", $product_array),

							'date_added' => date("Y-m-d H:i:s"),

						);



						if($data['id'] > 0){

							unset($coupon['date_added']);

							$this->db->update("coupon",$coupon,['coupon_id' => $data['id']]);

						} else {

							$this->db->insert("coupon",$coupon);

							$coupon_id = $this->db->insert_id();

						}

						$json['location'] = base_url("admincontrol/listproduct");

					}

					echo json_encode($json);
				}



	public function change_language($language_id = null) {
	    if(empty($language_id) || !is_numeric($language_id)) {
	        show_404();
	        return;
	    }

	    $this->db->where('id', $language_id);
	    $query = $this->db->get('language');
	    $language = $query->row_array();

	    if($language) {
	        $_SESSION['userLang'] = $language_id;
	        $_SESSION['userLangName'] = $language['name'];
	        header('Location: ' . $_SERVER['HTTP_REFERER']);
	    } else {
	        show_404();
	    }
	}


	public function change_currency($currency_code = null) {
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
	        $_SESSION['userCurrencyName'] = $currency['title'];
	        $_SESSION['userCurrencyLeft'] = $currency['symbol_left'];
	        header('Location: ' . $_SERVER['HTTP_REFERER']);
	    } else {
	        show_404();
	    }
	}



	public function lang_status_toggle() {
		try {
			$userdetails = $this->userdetails();
			$json = array();
			$column = $this->input->post("column",true);
			$id = (int)$this->input->post("id",true);
			$status = (int)$this->input->post('status',true);
			if($column == 'is_default'){
				$this->db->query("UPDATE language SET is_default = 0");
				$this->db->query("UPDATE language SET is_default = 1 WHERE id =". $id);
				$_SESSION['userLang'] = $id;
				echo json_encode(['reload' => true]);exit;
			} else {
				$this->db->query("UPDATE language SET ".$column."='".$status."' WHERE id =".$id);
			}
			$json = array('status'=>$this->db->affected_rows(),'languages'=>$this->Product_model->getLanguageHtml());
		} catch (\Throwable $th) {
			$json = array('status'=>false,'message'=>$th->getMessage());
		}
		echo json_encode($json);
	}


	public function update_language(){

		$userdetails = $this->userdetails();
		$json = array();
		$name = $this->input->post("name",true);
		$language_id = (int)$this->input->post("id",true);
		$status = (int)$this->input->post('status',true);
		$is_rtl = (int)$this->input->post('is_rtl',true);

		if($language_id == 1){ $name = 'English'; }
		if($name == ''){ $json['errors']['name'] = __('admin.name_is_required'); }

		if(!isset($json['errors'])){
			$post = $this->input->post(null,true);

			if($language_id == 0){
				$created = true;
				$this->db->query("INSERT INTO language SET status='". $status ."',is_rtl='". $is_rtl ."', name=". $this->db->escape($name) );
				$language_id = $this->db->insert_id();
			} else {
				$created = false;
				$this->db->query("UPDATE language SET status='". $status ."', is_rtl='". $is_rtl ."', name=". $this->db->escape($name) ." WHERE id =". $language_id );
			}

			$languages_json = file_get_contents(base_url('assets/data/languages.json'));
			$languages = json_decode($languages_json, true);

			if( !is_array($languages) ) $languages = [];
			$languages_code = array_search($name,$languages);

			$DefaultLangPath = null;

			if($languages_code != false) {
				$DefaultLangPath = APPPATH.'language/default/'.$languages_code;
			} 

			$path = APPPATH.'language/'. $language_id;

			$lang_files = ['admin','client','store','user','front','template_simple'];
			$language_translation_notavailable = 0;
			foreach ($lang_files as $file) {
				if($DefaultLangPath == null || !is_file($DefaultLangPath .'/'. $file .'.php')) {
					$language_translation_notavailable++;
				}
			}

			if((int)$this->input->post("id",true) == 0){
				$DefaultPath = APPPATH.'language/default';
				lang_copy($DefaultPath, $path, $DefaultLangPath);
			}

			if($this->input->post('flag',true) != ''){
				copy($this->input->post('flag',true),$path."/flag.png");
				$this->db->query("UPDATE language SET flag = '{$post['flag']}' WHERE id =". $language_id );
			}

			if(isset($post['is_default'])){
				$this->db->query("UPDATE language SET is_default = 0");
				$this->db->query("UPDATE language SET status =1 , is_default = 1 WHERE id =". $language_id );
			}

			$msg_prefix = $created ? "Language created" : "Language updated";
			if(!isset($json['errors'])){
				if($language_translation_notavailable > 0 && $language_translation_notavailable == sizeof($lang_files)) {
					$this->session->set_flashdata(array('error' => $msg_prefix.' but auto translations not available, please contact admin for auto translations!'));
					redirect('admincontrol/language/', 'refresh');
				} else if ($language_translation_notavailable > 0) {
					$this->session->set_flashdata(array('error' => $msg_prefix.' but some translations is missing, please contact admin for autotranslations!'));
					redirect('admincontrol/language/', 'refresh');
				} else {
					$this->session->set_flashdata(array('success' => $msg_prefix." successfully"));
					redirect('admincontrol/language/', 'refresh');
				}
			} else {
				$this->session->set_flashdata(array('error' => implode("<br>", $json['errors'])));
				redirect('admincontrol/translation_edit/'. $language_id, 'refresh');
			}
		} else {
			$this->session->set_flashdata(array('error' => implode("<br>", $json['errors'])));
			redirect('admincontrol/translation_edit/'. $language_id, 'refresh');
		}
		echo json_encode($json);
	}

	public function translation($language_id){

		$userdetails = $this->userdetails();

		$data['language'] = $this->db->query("SELECT * FROM language WHERE id=".$language_id)->row_array();

		if($data['language']){
			$data['language']['count'] = langCount($data['language']['id']);
			$this->view($data,'language/translation');

		}
		else{
			show_404();
		}
	}

	// Upload and Extract zip file
	public function language_zip_upload(){

		$userdetails = $this->userdetails();

		if(!empty($_FILES['file']['name'])){ 

			$config['upload_path'] = APPPATH.'language/default/'; 
			$config['allowed_types'] = 'zip'; 
			$config['max_size'] = '1024'; 
			$config['file_name'] = $_FILES['file']['name'];
			$this->load->library('upload',$config); 

			unlink(APPPATH.'language/default/'.$_FILES['file']['name']);

			if($this->upload->do_upload('file')){ 
				$uploadData = $this->upload->data(); 
				$filename = $uploadData['file_name'];
				
				$zip = new ZipArchive;

				$res = $zip->open(APPPATH.'language/default/'.$filename);
				
				if ($res === TRUE) {
					
					$extractpath = APPPATH.'language/default/';

					// Extract file
					$zip->extractTo($extractpath);
					$zip->close();

					$extractedFolderPath = $extractpath.preg_replace('/\\.[^.\\s]{3,4}$/', '', $filename);
					$lang_files = ['admin','client','store','user','front','template_simple'];

					$files = scandir($extractedFolderPath);

					for ($i=2; $i < sizeof($files); $i++) { 
						$extractedFileName = preg_replace('/\\.[^.\\s]{3,4}$/', '', $files[$i]);
						if(!in_array($extractedFileName, $lang_files)) {
							$isInvalidFile = true;
							$this->deleteDir($extractedFolderPath);
						}
					}
					unlink(APPPATH.'language/default/'.$filename);
					if(isset($isInvalidFile)) {
						$this->session->set_flashdata(array('error' => 'Invalid language zip file!'));
					} else {
						redirect(base_url('/admincontrol/update_user_langauges/'.preg_replace('/\\.[^.\\s]{3,4}$/', '', $filename)));
						die;
					}
				} else {
					$this->session->set_flashdata(array('error' => 'Invalid language zip file!'));
				}
			} else { 
				$this->session->set_flashdata(array('error' => 'Please select valid language zip file!'));
			} 
		} else { 
			$this->session->set_flashdata(array('error' => 'Please select valid language zip file!'));
		} 
		redirect(base_url('/admincontrol/language'));
	}

	private function deleteDir($dir) {
		$it = new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS);
		$files = new RecursiveIteratorIterator($it,
			RecursiveIteratorIterator::CHILD_FIRST);
		foreach($files as $file) {
			if ($file->isDir()){
				rmdir($file->getRealPath());
			} else {
				unlink($file->getRealPath());
			}
		}
		rmdir($dir);
	}
	
	public function get_translation(){
		$userdetails = $this->userdetails();
		$default_language = $this->db->query("SELECT * FROM language WHERE is_default=1")->row_array();
		$file_name = $this->input->post('id',true);
		$translation_id = $this->input->post('translation_id',true);
		$path = APPPATH.'language/default/' .$file_name.".php";
		include $path;
		$defaultLanguageKeys = $lang;
		$path = APPPATH.'language/'. $translation_id."/".$file_name.".php";
		include $path;
		$targerLanguageKeys = $lang;
		$newArray = array();
		foreach ($defaultLanguageKeys as $key => $value) {
			$newArray[$key] = array(
				'text' => $value,
				'value' => $targerLanguageKeys[$key],
			);
		}
		echo json_encode($newArray);
	}

	public function save_translation(){

		$userdetails = $this->userdetails();

		$trans = json_decode($this->input->post('data',true));

		$get = $this->input->get(null,true);

		$translation_id = (int)$get['translation_id'];

		$targerLanguageKeys = $get['id'];

		$path = APPPATH.'language/'. $translation_id."/".$targerLanguageKeys.".php";

		$file_content = '<?php '.PHP_EOL;

		foreach ($trans as $key => $value) {

			$file_content .= '$lang[\''. $key .'\'] = '. $this->db->escape($value) .';' .PHP_EOL;

		}
		file_put_contents($path, $file_content);
		$json['success'] = "Language save successfully";
		echo json_encode($json);die;
	}

	public function get_update_language(){
		$userdetails = $this->userdetails();
		$json = $this->db->query("SELECT * FROM language WHERE id = ". (int)$this->input->post('id',true))->row_array();
		echo json_encode($json);
	}

	public function translation_edit($lang_id = 0){
		$userdetails = $this->userdetails();
		$data['flags_files'] = glob("./assets/vertical/assets/images/flags/*.*");
		$data['flags_code'] = [];

		foreach ($data['flags_files'] as $flagfile) {
			$path_parts = pathinfo($flagfile);
			$data['flags_code'][$path_parts['filename']] = $flagfile;
		}

		$data['lang'] = $this->db->query("SELECT * FROM language WHERE id = ". (int)$lang_id)->row_array();
		$languages_json = file_get_contents('assets/data/languages.json');
		$data['languages'] = json_decode($languages_json, true);
		
		$this->view($data,'language/edit');
	}

	public function delete_update_language(){
		$userdetails = $this->userdetails();
		if((int)$this->input->post('id',true) != 1){
			$path = APPPATH.'language/'. (int)$this->input->post('id',true)."/";
			$this->cart->delete_directory($path);
			$this->db->query("DELETE FROM language WHERE id = ". (int)$this->input->post('id',true));
		}
		echo json_encode(array());
	}

	public function mails(){
		$data = array();
		$data['templates'] = $this->db->query("SELECT * FROM mail_templates")->result_array();
		$data['emailsetting'] 	= $this->Product_model->getSettings('emailsetting');
		$post = $this->input->post(null,true);

		if(!empty($post)){
			$hasError = false;
			if(count($_FILES) > 0){
				$commonSetting = array('emailsetting');
				$path = 'assets/images/site';
				$this->load->helper('string');
				$config['upload_path'] = $path;
				$config['allowed_types'] = '*';
				$config['file_name']  = random_string('alnum', 32);
				$this->load->library('upload', $config);
				foreach ($_FILES as $fieldname => $input) {
					$extension = pathinfo($_FILES[$fieldname]["name"], PATHINFO_EXTENSION);
					if($_FILES[$fieldname]["error"] == 0){
						if($extension=='jpg' || $extension=='jpeg' || $extension=='png' || $extension=='gif'){
							$this->upload->initialize($config);
							if($input['error'] == 0){
								if (!$this->upload->do_upload($fieldname)) { }
									else {
										$upload_details = $this->upload->data();
										list($key,$subkey) = explode("_", $fieldname);
										$post[$key][$subkey] = $upload_details['file_name'];
									}
								}
							} else{
								$hasError = true;
								$this->session->set_flashdata('error', 'Only Image file allowed');
							}
						}
					}
				}

				foreach ($post as $key => $value) {
					if (in_array($key, $commonSetting)) {
						$this->Setting_model->save($key, $value);
					}
				}

				if(!$hasError){
					$this->session->set_flashdata('success', __('admin.setting_saved_successfully'));
				}

				redirect('admincontrol/mails');
			}

			$this->view($data,'mails/index');
	}


					public function preview_mail($template_id) {
						$this->load->model('Mail_model');
						$data['id'] = $template_id;
						$data['prefix'] = '';
						$data['test_email'] = 'test@test.com';
						echo $this->Mail_model->preview_mail($data);
					}

					public function mails_edit($template_id){

						$data = array();

						$post = $this->input->post(null,true);

						if (isset($post['send_test'])) {
							
							$json = array();

							if (!filter_var($this->input->post('test_email'), FILTER_VALIDATE_EMAIL)) {

								$json['error'] = __('admin.invalid_email_format');

							}

							else{

								$json['success'] = __('admin.testing_mail_sent_successfully');

								$this->load->model('Mail_model');

								$json['detais'] = $this->Mail_model->test_new($post);

							}

							echo json_encode($json);die;

						}

						else if (isset($post['id'])) {
							$this->db->query("UPDATE mail_templates SET

								`subject` = ". $this->db->escape($this->input->post("subject",true)) .",

								`text` = ". $this->db->escape($this->input->post("text")) .",

								`admin_subject` = ". $this->db->escape($this->input->post("admin_subject",true)) .",

								`admin_text` = ". $this->db->escape($this->input->post("admin_text")) .",

								`client_subject` = ". $this->db->escape($this->input->post("client_subject",true)) .",

								`client_text` = ". $this->db->escape($this->input->post("client_text")) ."

								WHERE id = ". $post['id']
							);

							redirect($this->uri->uri_string());

						}

						$data['templates'] = $this->db->query("SELECT * FROM mail_templates WHERE id = ". $template_id)->row_array();

						if($data['templates']){

							$this->view($data,'mails/editor');
						}

						else{

							show_404();
						}
					}


					public function backup($action = ''){

						$userdetails = $this->userdetails();

						$this->load->library("Backup");

						$get = $this->input->get(null,true);

						$this->backup->setMysql(array(

							'host' => $this->db->hostname, 

							'user' => $this->db->username, 

							'pass' => $this->db->password, 

							'dbname' => $this->db->database

						));

						$data['zip_loaded'] = extension_loaded('zip');

						if(isset($_FILES['backup_file'])){
							$path = APPPATH . 'backup/mysql';
							$ext = pathinfo($_FILES['backup_file']["name"],PATHINFO_EXTENSION);

							// Demo Mode
							if (ENVIRONMENT === 'demo') {
							    $this->session->set_flashdata('error', 'Disabled on demo mode');
							    redirect('admincontrol/backup');
							    return;
							}
							// Demo Mode

							$this->load->helper('string');

							$config['upload_path'] = $path;

							$config['allowed_types'] = 'zip';

							$config['file_name']  = 'Upload_'.date("Y.m.d H.i.s").'.'.$ext;

							$this->load->library('upload', $config);

							$this->upload->initialize($config);


							if (!$this->upload->do_upload('backup_file')) {
								$this->session->set_flashdata('error', $this->upload->display_errors());

							}

							else {

								$upload_details = $this->upload->data();

								$this->session->set_flashdata('success', __('admin.backup_upload_successfully'));

							}

							redirect('admincontrol/backup');

						}

						if ($action == 'getbackup') {
							try {
								// Demo Mode
						        if (ENVIRONMENT === 'demo') {
						            $this->session->set_flashdata('error', __('admin.demo_mode'));
						            redirect('admincontrol/backup');
						            return;
						        }
						        // Demo Mode

								$this->load->dbutil();
								$prefs = array(
									'format'        => 'txt',
									'filename'      => $this->db->database,
									'add_drop'      => true,
									'add_insert'    => true,
									'newline'       => "\n"
								);

								$backup =& $this->dbutil->backup($prefs);

								$db_name = 'database_backup_version_'.$this->config->item('app_version').'_'.time();

								$bk_path = 'application/backup/mysql/'.$db_name;

								$this->load->library('zip');
								$this->zip->add_data($db_name.'.sql', $backup);
								$this->zip->archive($bk_path.'.zip');

								$this->session->set_flashdata('success', __('admin.backup_created_successfully'));

							} catch (Exception $e) {
								$this->session->set_flashdata('error', $e->getMessage());
							}

							redirect('admincontrol/backup');
						}

						else if ($action == 'delete') {

							$status =  $this->backup->delFile( $get['file_name'] );

							if($status == 'ok_delete'){

								$this->session->set_flashdata('success', __('admin.backup_file_deleted_successfully'));

							} else {

								$this->session->set_flashdata('error', $status);

							}

							redirect('admincontrol/backup');

						}

						else if ($action == 'restore') {

							$status =  $this->backup->restore( $get['file_name'] );

							if($status == 'ok_res_backup'){

								$this->session->set_flashdata('success', __('admin.backup_file_restored_successfully'));

							} else {

								$this->session->set_flashdata('error', $status);

							}

							redirect('admincontrol/backup');

						}

						else if ($action == 'download') {

							$this->backup->getZipFile( $get['file_name'] );

						}

						$data['backups'] = $this->backup->getListZip();

						$this->view($data, 'backup/index');
					}


					public function userdetails(){
						if (isset($this->session) && $this->session->userdata('user_type') !== FALSE && $this->session->userdata('user_type')=='admin')
						{
							$this->session->unset_userdata('user');
							$this->session->unset_userdata('client');
							
							if(!isset($this->session->administrator))
								redirect($this->admin_domain_url, 'refresh');
			 				else
			 					return $this->session->administrator;
						}
						else 
						{
							 //show_404();
							 redirect($this->admin_domain_url, 'refresh');
						}
					}
			   

			   
					public function getSiteSetting(){

						return $this->Product_model->getSettings('site');

					}



					public function index($slug) {

						if($this->userdetails()){ redirect($this->admin_domain_url, 'refresh'); }

						else { redirect('usercontrol', 'refresh'); }

					}



					public function notification(){

						$userdetails = $this->userdetails();

						$this->load->library('pagination');

						$this->load->helper('url');

						$config['base_url'] = base_url('admincontrol/notification');

						$config['per_page'] = 10;

						$post = $this->input->post(null,true);

						$get = $this->input->get(null,true);

						if (isset($get['clearall'])) {

							$this->db->query("DELETE FROM notification WHERE notification_viewfor = 'admin'");

							redirect('admincontrol/notification', 'refresh');die;

						}

						if (isset($post['delete_ids'])) {

							$delete_ids = implode(",", $post['delete_ids']);

							$this->db->query("DELETE FROM notification WHERE notification_id IN ({$delete_ids})");

							echo json_encode(array());

							die;

						}

						$data['title'] = 'Notification';

						$page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;

						$notification = $this->user->getAllNotificationPaging('admin',null,$config['per_page'],$page);

						$config['total_rows'] = $notification['total'];

						$this->pagination->initialize($config);

						$data['pagination'] = $this->pagination->create_links();

						$data['notifications'] = $notification['notifications'];

						$this->view($data,'dashboard/notification');
					}



		public function register($refid = null) {

			if($this->userdetails()){ redirect($this->admin_domain_url, 'refresh'); }

			if(!empty($refid)){

			} else {

				$refid = base64_decode($this->input->get('refid'));

			}

			$data=array();

			if ($this->input->post()) {

				$this->load->library('form_validation');

				$checkmail=$this->user->checkmail($this->input->post('email',true));

				$checkuser=$this->user->checkuser($this->input->post('username',true));

				if(!empty($checkmail))

				{

					$this->session->set_flashdata('error', __('admin.this_email_already_register'));

					$this->session->set_flashdata('postdata', $this->input->post());

					redirect($this->admin_domain_url);

				} elseif(!empty($checkuser)) {

					$this->session->set_flashdata('error',__('admin.this_username_already_register'));

					$this->session->set_flashdata('postdata', $this->input->post());

					redirect($this->admin_domain_url);

				} else {

					$data=$this->user->insert(array(

						'firstname' => $this->input->post('firstname',true),

						'lastname'  => $this->input->post('lastname',true),

						'email'     => $this->input->post('email',true),

						'username'  => $this->input->post('username',true),

						'password'  => sha1($this->input->post('password',true)),

						'refid'     => !empty($refid) ? base64_decode($refid) : 0,

						'type'      => 'admin',

					));

					if(!empty($data)){

						$this->session->set_flashdata('success', __('admin.you_ve_successfully_registered'));

						redirect($this->admin_domain_url);

					}

				}

			}

			$this->load->view('admincontrol/login/register', $data);
		}


		public function changePassword(){

			$userdetails = $this->userdetails();

			if(empty($userdetails)){

				redirect($this->admin_domain_url);
			}

			$post = $this->input->post(null,true);

			if(isset($post) && !empty($post)){

				$this->form_validation->set_rules('old_pass', 'Old Password', 'required|trim', array('required' => '%s is required'));

				$this->form_validation->set_rules('password', 'New Password', 'required|trim', array('required' => '%s is required'));

				$this->form_validation->set_rules('conf_password', 'Confirm Password', 'required|trim|matches[password]', array('required' => '%s is required'));

				if ($this->form_validation->run() == FALSE) {

					$data['validate_err'] = validation_errors();

				} else {

				$admin = $this->db->from('users')->where('id',$userdetails['id'])->get()->row_array();

					if($admin['password'] == sha1($this->input->post('old_pass',true))){

						$res = array('password'=>sha1($this->input->post('password',true)));

						$this->db->where('id',$admin['id']);

						$this->db->update('users',$res);

						$this->session->set_flashdata(array('flash' => array('success' => __('admin.user_profile_updated_successfully!'))));

						redirect($this->admin_domain_url, 'refresh');

					}else{

						$this->session->set_flashdata(array('flash' => array('error' => __('admin.old_password_not_matched.'))));

						redirect('admincontrol/changePassword');

					}

				}

			}

			$data['title'] = 'Change Password';

			$this->view($data,'dashboard/change-password');
		}


		public function ask_again_withdrawal(){

			$this->db->query("UPDATE wallet SET status=1 WHERE (wv != 'V2' OR wv IS NULL) AND status = 2");



			$this->session->set_flashdata('success', 'All Transaction Set In Wallet. Now user need to send withdraw request.');

			$get = $this->input->get(null,true);



			if (isset($get['backto'])) {

				redirect('admincontrol/wallet_requests_list?tab=old');die;

			}

			redirect('admincontrol/wallet/withdraw');
		}



					public function wallet_withdraw(){

						$userdetails = $this->userdetails();

						$get = $this->input->get(null,true);

						$filter = array(

							'status' => 2,

							'old_with' => 'V2',

						);

						if (isset($get['user_id']) && $get['user_id'] > 0) {

							$filter['user_id'] = (int)$get['user_id'];

							$data['user_id'] = $filter['user_id'];
						}

						if (isset($get['date'])) {

							$filter['date'] = $get['date'];

							$data['date'] = $filter['date'];

						}

						$query = $this->db->query('SELECT sum(amount) as amount,count(`status`) as counts,`status` FROM `wallet` WHERE (wallet.wv != "V2" OR wallet.wv IS NULL) GROUP BY `status`')->result_array();

						foreach ($query as $key => $value) {

							switch ($value['status']) {

								case '0':

								$data['totals']['wallet_on_hold_amount'] = (float)$value['amount'];

								$data['totals']['wallet_on_hold_count'] = (float)$value['counts'];

								break;

								case '1':

								$data['totals']['wallet_unpaid_amount'] = (float)$value['amount'];

								$data['totals']['wallet_unpaid_count'] = (float)$value['counts'];

								break;

								case '2':

								$data['totals']['wallet_request_sent_amount'] = (float)$value['amount'];

								$data['totals']['wallet_request_sent_count'] = (float)$value['counts'];

								break;

								case '3':

								$data['totals']['wallet_accept_amount'] = (float)$value['amount'];

								$data['totals']['wallet_accept_count'] = (float)$value['counts'];

								break;

								default: break;

							}

						}

						$query = $this->db->query('SELECT sum(amount) as amount,count(`commission_status`) as counts,`commission_status` FROM `wallet` WHERE (wallet.wv != "V2" OR wallet.wv IS NULL) GROUP BY `commission_status`')->result_array();

						foreach ($query as $key => $value) {

							switch ($value['commission_status']) {

								case '1':

								$data['totals']['wallet_cancel_amount'] = (float)$value['amount'];

								$data['totals']['wallet_cancel_count'] = (float)$value['counts'];

								break;

								case '2':

								$data['totals']['wallet_trash_amount'] = (float)$value['amount'];

								$data['totals']['wallet_trash_count'] = (float)$value['counts'];

								break;

								default: break;

							}

						}




						$data['transaction'] = $this->Wallet_model->getTransaction($filter);

						$data['request_status'] = $this->Wallet_model->status();

						$post = $this->input->post(null,true);





						if (isset($post['request_payment_all'])) {

							$json = array();



							if($data['transaction']){

								$this->load->model('Mail_model');

								$userwise = array();

								foreach ($data['transaction'] as $key => $value) { $userwise[$value['user_id']][] = $value; }



								foreach ($userwise as $user_id => $value) {

									$user_name = $user_email = '';

									foreach ($value as $__value) {

										$this->Wallet_model->changeStatus($__value['id'],$post['status']);



										$user_name = $__value['firstname']. ' ' . $__value['lastname'];

										$user_email = $__value['user_email'];

									}



									if($user_name){

										$_data = array(

											'amount'          => c_format($data['wallet_unpaid_amount']),

											'comment'         => $user_name .' your withdrawal request status has been changed..!',

											'name'            => $user_name,

											'user_email'      => $user_email,

											'commission_type' => '',

											'new_status'      => $data['request_status'][$post['status']],

										);



										$this->Mail_model->send_wallet_withdrawal_status($_data);

									}

								}



								$json['success'] = __('admin.request_send_successfully');

							}



							echo json_encode($json);die;

						}

						$this->view($data,'payment/wallet_withdraw');

					}



					public function wallet_requests_details($id){

						$userdetails = $this->userdetails();

						$get = $this->input->get(null,true);

						$post = $this->input->post(null,true);

						$id=(int)$id;

						if (isset($post['status'])) {

							$this->form_validation->set_rules('status', 'Status', 'required|trim');

							$this->form_validation->set_rules('comment', 'Comment', 'required|trim');

							if ($this->form_validation->run() == FALSE) {
								$data['errors'] = $this->form_validation->error_array();
							} else {
								$this->load->model('Withdrawal_payment_model');

								$this->Withdrawal_payment_model->apiAddWithdrwalRequestHistory($id,[

									'status_id' => (int)$post['status'],

									'comment' => $post['comment'],

									'transaction_id' => '',

								]);

								$data['success'] = 1;
							}

							echo json_encode($data);die;
						}

						$data['request'] = $this->db->query("SELECT * FROM wallet_requests WHERE id={$id}")->row_array();

						if(!$data['request']){

							show_404();

						}

						$this->load->model('Withdrawal_payment_model');

						$filter = array(

							'id_in' => $data['request']['tran_ids'],
						);


						$data['transaction'] = $this->Wallet_model->getTransaction($filter);

						$data['status'] = $this->Wallet_model->status();

						$data['status_icon'] = $this->Wallet_model->status_icon;

						$data['status_list'] = $this->Withdrawal_payment_model->status_list;

						$data['confirm'] = $this->Withdrawal_payment_model->getConfirm($data['request']['prefer_method'],['request'=>$data['request']]);

						$this->view($data,'users/wallet_requests_details');
					}



					public function get_withdrwal_history($id)

					{
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



					public function wallet_requests_list(){

						$userdetails = $this->userdetails();

						$get = $this->input->get(null,true);

						$post = $this->input->post(null,true);

						if (isset($post['delete_request'])) {
							$json['id'] = [];

							$ids= explode(",", $post['id']);

							foreach ($ids as $id) {
								$dataCollection = $this->Wallet_model->getDeleteData((int)$id);

								$request = $this->db->query("SELECT id FROM wallet_requests WHERE tran_ids='".$post['id']."'")->row();


								foreach ($dataCollection as $data) {

									if(!empty($data['id'])) {
										$this->db->query("UPDATE wallet SET status=1 WHERE id =".$data['id']);
									}

									if(isset($request->id)) {
										$this->db->query("DELETE FROM wallet_requests WHERE id=".$request->id);

										$this->db->query("DELETE FROM wallet_requests_history WHERE req_id=".$request->id);
									}
								}
							}	

							$json['success'] = 1;

							echo json_encode($json);die;

						}




						if (isset($post['get_new'])) {

							$get = $this->input->post(null,true);

							$filter = array();

							if (isset($get['user_id']) && $get['user_id'] > 0) {

								$filter['user_id'] = (int)$get['user_id'];

								$data['user_id'] = $filter['user_id'];

							}



							if (isset($get['date'])) {

								$filter['date'] = $get['date'];

								$data['date'] = $filter['date'];

							}

							$this->load->model('Withdrawal_payment_model');

							$data['lists'] = $this->Withdrawal_payment_model->getRequests($filter);
							

							$json['html'] = $this->load->view("admincontrol/users/part/tr_w_request_new",$data,true);

							echo json_encode($json);die;

						}



						if (isset($post['get_old'])) {

							$get = $this->input->post(null,true);

							$filter = array(

								'status' => 2,

								'old_with' => 'V2',

							);



							if (isset($get['user_id']) && $get['user_id'] > 0) {

								$filter['user_id'] = (int)$get['user_id'];

								$data['user_id'] = $filter['user_id'];

							}



							if (isset($get['date'])) {

								$filter['date'] = $get['date'];

								$data['date'] = $filter['date'];

							}



							$data['transaction'] = $this->Wallet_model->getTransaction($filter);


							$data['request_status'] = $this->Wallet_model->status();

							$json['html'] = $this->load->view("admincontrol/users/part/tr_w_request_old",$data,true);



							echo json_encode($json);die;

						}


						$data['users'] = $this->db->query("SELECT id,username FROM users WHERE type = 'user'")->result_array();



						$query = $this->db->query('SELECT sum(amount) as amount,count(`status`) as counts,`status` FROM `wallet` WHERE (wallet.wv != "V2" OR wallet.wv IS NULL) GROUP BY `status`')->result_array();

						foreach ($query as $key => $value) {

							switch ($value['status']) {

								case '0':

								$data['totals']['wallet_on_hold_amount'] = (float)$value['amount'];

								$data['totals']['wallet_on_hold_count'] = (float)$value['counts'];

								break;

								case '1':

								$data['totals']['wallet_unpaid_amount'] = (float)$value['amount'];

								$data['totals']['wallet_unpaid_count'] = (float)$value['counts'];

								break;

								case '2':

								$data['totals']['wallet_request_sent_amount'] = (float)$value['amount'];

								$data['totals']['wallet_request_sent_count'] = (float)$value['counts'];

								break;

								case '3':

								$data['totals']['wallet_accept_amount'] = (float)$value['amount'];

								$data['totals']['wallet_accept_count'] = (float)$value['counts'];

								break;

								default: break;

							}

						}

						$query = $this->db->query('SELECT sum(amount) as amount,count(`commission_status`) as counts,`commission_status` FROM `wallet` WHERE (wallet.wv != "V2" OR wallet.wv IS NULL) GROUP BY `commission_status`')->result_array();

						foreach ($query as $key => $value) {

							switch ($value['commission_status']) {

								case '1':

								$data['totals']['wallet_cancel_amount'] = (float)$value['amount'];

								$data['totals']['wallet_cancel_count'] = (float)$value['counts'];

								break;

								case '2':

								$data['totals']['wallet_trash_amount'] = (float)$value['amount'];

								$data['totals']['wallet_trash_count'] = (float)$value['counts'];

								break;

								default: break;

							}

						} 

						$this->view($data,'users/wallet_requests_list');

					}


					public function mywallet(){

						$userdetails = $this->userdetails();

						$get = $this->input->get(null,true);

						$data['status'] = $this->Wallet_model->status();

						$data['status_icon'] = $this->Wallet_model->status_icon;

						$data['request_status'] = $this->Wallet_model->request_status;		

						$filter['sortBy'] = isset($get['sortby']) ? $get['sortby'] : '';

						$filter['orderBy'] = isset($get['order']) ? $get['order'] : '';

						if (isset($get['user_id']) && $get['user_id'] > 0) {

							$filter['user_id'] = (int)$get['user_id'];

							$data['user_id'] = $filter['user_id'];

						}

						if (isset($get['recurring']) && $get['recurring'] > 0) {

							$filter['recurring'] = (int)$get['recurring'];

							$data['recurring'] = $filter['recurring'];

						}


						if (isset($get['paid_status']) && $get['paid_status']) {

							$filter['paid_status'] = $get['paid_status'];

						}

						if (isset($get['status']) && $get['status'] != '') {

							$filter['status'] = (int)$get['status'];

						} else{

							$filter['status_gt'] = 0;

						}



						if (isset($get['date'])) {

							$filter['date'] = $get['date'];

						}

						$filter['parent_id'] = 0;



						if ( isset($get['type']) && $get['type'] ) {

							$filter['types'] = $get['type'];

						}

						$filter['not_negative_balence'] = true;


						$this->load->library('pagination');

						$config['base_url'] = base_url('admincontrol/mywallet/');

						$config['total_rows'] = $this->Wallet_model->getTransaction($filter, true, 'ONLY_PARENTS');

						$config['per_page'] = 100;

						$config['attributes'] = array('class' => 'single_paginate_link');

						$filter['per_page'] = $config['per_page'];

						$config['reuse_query_string'] = TRUE;

						$config['query_string_segment'] = 'page';

						$config['use_page_numbers'] = TRUE;

						$this->pagination->initialize($config);

						$filter['page_num'] = ($this->uri->segment(3)) ? $this->uri->segment(3) : 1;

						$data['offset'] = $filter['offset'] = ($filter['page_num'] - 1) * $config['per_page'];

						$data['transaction'] = $this->Wallet_model->getTransaction($filter, false, 'ONLY_PARENTS');

						$data['pagination_link'] = $this->pagination->create_links();

						$data['users'] = $this->db->query("SELECT id,CONCAT(firstname,' ',lastname) as name FROM users ")->result_array();

						$data['totals'] = $this->Wallet_model->getTotals(array(),true);



						$data['table'] = $this->load->view("admincontrol/users/part/wallet_tr", $data, true);


						if(isset($_GET['a'])){
							$this->view($data, 'users/mywallet');
							return false;
						}

						$_data = objectToArray($data);

						$this->load->model('Total_model');

						$data['admin_totals'] = $this->Total_model->adminTotals();

						unset($filter['per_page']);
						unset($filter['offset']);
						unset($filter['page_num']);

						$transactionSorted = [];
						
						
						for ($i=0; $i < sizeof($data['transaction']); $i++) {
							$filter['group_id'] = $data['transaction'][$i]['group_id'];

							$filter['not_tran_id'] = $data['transaction'][$i]['id'];
							
							$child_transaction = $this->Wallet_model->getTransaction($filter);

							 
							$child_transaction[]  = $data['transaction'][$i];

							$child_transaction = array_reverse($child_transaction);

							$child_transaction_sorted = $child_transaction;

							foreach($child_transaction as $key => $ch) {
								$moveFirst = false;

								if(strpos($a['type'], 'refer') === false) {
									if(in_array($ch['type'], ['vendor_sale_commission', 'sale_commission', 'external_sale_commission', 'click_comission'])) {
										$moveFirst = true;
									} else if(strpos($a['type'], 'click')) {
										$moveFirst = true;
									}
								}

								if($moveFirst) {
									unset($child_transaction_sorted[$key]);
									array_unshift($child_transaction_sorted , $ch);
								}
							}

							$transactionSorted = array_merge($transactionSorted, $child_transaction_sorted);
						}

						$data['userdetails'] = $this->userdetails();

						$data['transaction'] = $transactionSorted;

						$this->view($data, 'users/wallet');
					}

					public function change_commission_status(){ 

						$id = $this->input->post('id');

						$status_type = $this->input->post('status_type');

						$delete_id = $this->input->post("id",true);

						$dataCollection = $this->Wallet_model->getDeleteData((int)$id);



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

					public function getRecurringTransaction(){

						$id = (int)$this->input->post("id");

						$userdetails = $this->userdetails();

						if(empty($userdetails)){redirect($this->admin_domain_url);}

						$data['status'] = $this->Wallet_model->status();

						$data['status_icon'] = $this->Wallet_model->status_icon;

						$data['request_status'] = $this->Wallet_model->request_status;

						$filter['parent_id'] = $id;

						$data['transaction'] = $this->Wallet_model->getTransaction($filter);

						$data['recurring'] = $id;


						if (!isset($_POST['newtr'])) {

							$json['table'] = $this->load->view("admincontrol/users/part/wallet_tr", $data, true);

						} else{

							$json['table'] = '';

							foreach ($data['transaction'] as $key => $value) {

								$data['class'] = 'child-recurring';

								$data['force_class'] = $_POST['ischild'] == 'true' ? 'child-arrow' : '';

								$data['recurring'] = $id;

								$data['value'] = $value;

								$data['wallet_status'] = $data['status'];

								$json['table'] .= $this->load->view("admincontrol/users/part/new_wallet_tr", $data, true);
							}
						}
						echo json_encode($json);
					}

					public function ajax_dashboard(){
						$userdetails = $this->userdetails();
						$hcurrency = $this->Product_model->getSettings('site', 'hide_currency_from');
						$data['hcurrency'] = (isset($hcurrency['hide_currency_from']) && str_contains($hcurrency['hide_currency_from'], 'admin'));

						if($data['hcurrency']) {
							$data['fun_c_format'] =$fun_c_format = 'c_format_nosym';
						} else {
							$data['fun_c_format'] =$fun_c_format = 'c_format';
						}

						$post = $this->input->post(null,true);

						$data['online_count'] = $this->Product_model->onlineCount();

						$data['userworldmap'] = $this->Product_model->getUserWorldMap();


						$this->load->model('IntegrationModel');
						$this->load->model('Order_model');

						//green income popups
						$data['ajax_newuser'] = $this->Product_model->getAllUsers(array("limit" => 5,'id_gt' => $post['last_id_newuser']));
						$data['last_id_newuser'] = $post['last_id_newuser'];

						$data['ajax_integration_logs']   = $this->IntegrationModel->getLogs(array('page'  => 1, 'limit' => 5, 'id_gt' => $post['last_id_integration_logs'] ))['records'];

						$data['ajax_integration_orders'] = $this->IntegrationModel->getOrders(array("limit" => 5, 'id_gt' => $post['last_id_integration_orders']));

						$data['ajax_store_orders'] = $this->Order_model->getDashboardOrders(array("limit" => 5, 'id_gt' => $post['last_id_store_orders']));
						//green income popups

						
						$data['last_id_integration_logs'] = $post['last_id_integration_logs'];

						$data['last_id_integration_orders'] = $post['last_id_integration_orders'];

						$data['last_id_store_orders'] = $post['last_id_store_orders'];

						
						$data['newuser'] = $this->Product_model->getAllUsers(array("limit" => 50));

						$data['integration_orders'] = $this->IntegrationModel->getOrders(array("limit" => 50));

						$data['integration_logs'] = $this->IntegrationModel->getLogs(array('page' => 1,'limit' => 50))['records'];

						$data['last_id_notifications'] = $this->Product_model->getnotificationnew('admin',null,5,array('id_gt' => $post['last_id_notifications']));

						$data['notifications_count'] = $this->Product_model->getnotificationnew_count('admin', null);

						$data['notifications'] = $this->Product_model->getnotificationnew('admin', null, null);

						$this->load->model('Report_model');

						$data['live_window'] = $this->Report_model->combine_window($data);

						$data['live_dashboard'] = $this->Product_model->getSettings('live_dashboard');

						$admin_sound_status = $this->Product_model->getSettings('live_dashboard', 'admin_sound_status');
						$data['sound_status'] = $admin_sound_status['admin_sound_status'];

						$audio_sound = $this->Product_model->getSettings('site', 'notification_sound');

						if (sizeof($audio_sound) > 0) {
							$data['notification_sound'] = $audio_sound['notification_sound'];
						}else{
							$data['notification_sound'] = '';
						}

						$this->load->model('Total_model');

						$data['chart'] = $this->Total_model->chart([

							'year' => $post['selectedyear'],

							'group' => $post['renderChart'],

						]);


						$data['admin_totals'] = $this->Total_model->adminTotals();

						$data['admin_totals_week'] = $fun_c_format($this->Total_model->adminBalance(['week' => 1]));

						$data['admin_totals_month'] = $fun_c_format($this->Total_model->adminBalance(['month' => 1]));

						$data['admin_totals_year'] = $fun_c_format($this->Total_model->adminBalance(['year' => 1]));


						$data['admin_totals']['admin_balance'] = $fun_c_format($data['admin_totals']['admin_balance']);

						$data['admin_totals']['sale_localstore_vendor_total'] = $fun_c_format($data['admin_totals']['sale_localstore_vendor_total']);

						$data['admin_totals']['sale_total_admin_store'] = $fun_c_format($data['admin_totals']['sale_localstore_total'] + $data['admin_totals']['order_external_total']);

						$data['admin_totals']['click_action_total'] = (int)($data['admin_totals']['click_action_total']);

						$data['admin_totals']['click_action_commission'] = $fun_c_format($data['admin_totals']['click_action_commission']);

						$data['admin_totals']['all_click_total'] = (int)(

							intval($data['admin_totals']['click_localstore_total']) +

							intval($data['admin_totals']['click_integration_total']) +

							intval($data['admin_totals']['click_form_total'])

						);

						$data['admin_totals']['all_click_commission'] = $fun_c_format(

							floatval($data['admin_totals']['click_localstore_commission']) +

							floatval($data['admin_totals']['click_integration_commission']) +

							floatval($data['admin_totals']['click_form_commission'])
						);


						$data['admin_totals']['click_localstore_total'] = (int)($data['admin_totals']['click_localstore_total']);

						$data['admin_totals']['click_localstore_commission'] = $fun_c_format($data['admin_totals']['click_localstore_commission']);

						$data['admin_totals']['click_integration_total'] = (int)($data['admin_totals']['click_integration_total']);

						$data['admin_totals']['click_integration_commission'] = $fun_c_format($data['admin_totals']['click_integration_commission']);

						$data['admin_totals']['click_form_total'] = (int)($data['admin_totals']['click_form_total']);

						$data['admin_totals']['click_form_commission'] = $fun_c_format($data['admin_totals']['click_form_commission']);

						$data['admin_totals']['click_all_total'] = (int)(

							intval($data['admin_totals']['click_localstore_total']) +

							intval($data['admin_totals']['click_integration_total']) +

							intval($data['admin_totals']['click_form_total']) 

						);

						$data['admin_totals']['click_all_commission'] = $fun_c_format(

							floatval($data['admin_totals']['click_localstore_commission']) +

							floatval($data['admin_totals']['click_integration_commission']) +

							floatval($data['admin_totals']['click_form_commission']) 

						);

						$data['admin_totals']['all_sale_commission'] = $fun_c_format(

							floatval($data['admin_totals']['sale_localstore_commission']) +

							floatval($data['admin_totals']['order_external_commission']) +

							floatval($data['admin_totals']['sale_localstore_vendor_commission']) 

						);

						$data['admin_totals']['sale_localstore_count'] = (int)($data['admin_totals']['sale_localstore_count']);

						$data['admin_totals']['sale_localstore_commission'] = $fun_c_format($data['admin_totals']['sale_localstore_commission']);

						$data['admin_totals']['sale_localstore_vendor_count'] = (int)($data['admin_totals']['sale_localstore_vendor_count']);

						$data['admin_totals']['sale_localstore_vendor_commission'] = $fun_c_format($data['admin_totals']['sale_localstore_vendor_commission']);

						$data['admin_totals']['order_external_count'] = (int)($data['admin_totals']['order_external_count']);

						$data['admin_totals']['order_external_commission'] = $fun_c_format($data['admin_totals']['order_external_commission']);

						$data['admin_totals']['all_sale_count'] = (int)(

							intval($data['admin_totals']['sale_localstore_count']) +

							intval($data['admin_totals']['order_external_count']) +

							intval($data['admin_totals']['sale_localstore_vendor_count'] )

						);


						$data['admin_totals']['wallet_unpaid_amounton_hold_count'] = (int)($data['admin_totals']['wallet_unpaid_amounton_hold_count']);

						$data['admin_totals']['wallet_on_hold_amount'] = $fun_c_format($data['admin_totals']['wallet_on_hold_amount']);

						$data['admin_totals']['wallet_unpaid_count'] = (int)($data['admin_totals']['wallet_unpaid_count']);

						$data['admin_totals']['wallet_unpaid_amount'] = $fun_c_format($data['admin_totals']['wallet_unpaid_amount']);

						$data['admin_totals']['wallet_request_sent_count'] = (int)($data['admin_totals']['wallet_request_sent_count']);

						$data['admin_totals']['wallet_request_sent_amount'] = $fun_c_format($data['admin_totals']['wallet_request_sent_amount']);

						$data['admin_totals']['wallet_accept_count'] = (int)($data['admin_totals']['wallet_accept_count']);

						$data['admin_totals']['wallet_accept_amount'] = $fun_c_format($data['admin_totals']['wallet_accept_amount']);

						$data['admin_totals']['wallet_cancel_count'] = (int)($data['admin_totals']['wallet_cancel_count']);

						$data['admin_totals']['wallet_cancel_amount'] = $fun_c_format($data['admin_totals']['wallet_cancel_amount']);

						$data['admin_totals']['wallet_trash_count'] = (int)($data['admin_totals']['wallet_trash_count']);

						$data['admin_totals']['wallet_trash_amount'] = $fun_c_format($data['admin_totals']['wallet_trash_amount']);



						$data['admin_totals']['vendor_wallet_accept_count'] = (int)($data['admin_totals']['vendor_wallet_accept_count']);

						$data['admin_totals']['vendor_wallet_accept_amount'] = $fun_c_format($data['admin_totals']['vendor_wallet_accept_amount']);

						$data['admin_totals']['vendor_wallet_request_sent_count'] = (int)($data['admin_totals']['vendor_wallet_request_sent_count']);

						$data['admin_totals']['vendor_wallet_request_sent_amount'] = $fun_c_format($data['admin_totals']['vendor_wallet_request_sent_amount']);

						$data['admin_totals']['vendor_wallet_unpaid_count'] = (int)($data['admin_totals']['vendor_wallet_unpaid_count']);

						$data['admin_totals']['vendor_wallet_unpaid_amount'] = $fun_c_format($data['admin_totals']['vendor_wallet_unpaid_amount']);

						$data['admin_totals']['vendor_wallet_cancel_count'] = (int)($data['admin_totals']['vendor_wallet_cancel_count']);

						$data['admin_totals']['vendor_wallet_cancel_amount'] = $fun_c_format($data['admin_totals']['vendor_wallet_cancel_amount']);

						$data['admin_totals']['vendor_wallet_trash_count'] = (int)($data['admin_totals']['vendor_wallet_trash_count']);

						$data['admin_totals']['vendor_wallet_trash_amount'] = $fun_c_format($data['admin_totals']['vendor_wallet_trash_amount']);

						$data['admin_totals']['order_vendor_total'] = (int)($data['admin_totals']['order_vendor_total']);


						$data['integration_data'] = $this->Total_model->get_integartion_data(true);

						$data['time'] = date("h:i:s A");

    					$data['timeout'] = $this->timeout;

						echo json_encode($data);die;

					}


					public function dashboard(){

						$userdetails = $this->userdetails();

						//switch buttons code start
						if(isset($_POST['action'])) {
						$this->load->model('Setting_model');
						$this->Setting_model->save($_POST['setting_type'], [$_POST['setting_key']=>$_POST['val']]);

						// Condition for MarketTools
						    if ($_POST['setting_key'] == "markettools_status" && $_POST['setting_type'] == "market_tools") {
						        $this->Setting_model->save("market_tools", ["status" => $_POST['val']]);
						    }
						// Condition for MarketTools

						echo 'success'; exit;
					}

					$market_tools_status = $this->Product_model->getSettings('market_tools', 'status');

					$store_status = $this->Product_model->getSettings('store', 'status');

					$data = array (

						'market_tools_is_enable' => isset($market_tools_status['status']) ? $market_tools_status['status'] : 0,

						'store_is_enable' => isset($store_status['status']) ? $store_status['status'] : 0,

					);

					$data2['integration_modules'] = $this->modules_list('addons');
					$data['integration_modules_view'] = $this->load->view('admincontrol/integration/index', $data2, true);
					//switch buttons code end



						// Check if there are missing settings
						$data['missing'] = $this->Product_model->getSettingStatus();
					    if (!empty($data['missing'])) {
					        $data['showMissingDetailsModal'] = true;
					    }
					    // Check if there are missing settings

						$hcurrency = $this->Product_model->getSettings('site', 'hide_currency_from');

						$data['hcurrency'] = (isset($hcurrency['hide_currency_from']) && str_contains($hcurrency['hide_currency_from'], 'admin'));

						if($data['hcurrency']) {
							$data['fun_c_format'] =$fun_c_format = 'c_format_nosym';
						} else {
							$data['fun_c_format'] =$fun_c_format = 'c_format';
						}

						$post = $this->input->post(null,true);
						$data['online_count'] = $this->Product_model->onlineCount();
						$data['userworldmap'] = $this->Product_model->getUserWorldMap();
						$data['notifications'] = $this->Product_model->getnotificationnew('admin',null,5);
						$this->load->model('IntegrationModel');
						$this->load->model('Order_model');

						$popular_aff_filter=$this->db->query("SELECT * FROM setting WHERE  setting_key='popular_affiliates' and setting_type='popular_affiliates_sorting'")->row();
						$data['popular_affiliates'] = isset($popular_aff_filter)  ? $popular_aff_filter->setting_value : '';
						$popular_aff_filt=isset($popular_aff_filter)  ? $popular_aff_filter->setting_value : '';
						$data['newuser'] = $this->Product_model->getAllUsers(array("limit" => 50));
						$data['integration_orders'] = $this->IntegrationModel->getOrders(array("limit" => 50));
						$data['store_orders'] = $this->Order_model->getOrders(array("limit" => 50));
						$data['integration_logs'] = $this->IntegrationModel->getLogs(array('page' => 1,'limit' => 50))['records'];
						$this->load->model('Report_model');
						$data['live_window'] = $this->Report_model->combine_window($data);
						$data['populer_users'] = $this->Product_model->getPopulerUsers(array("limit" => 10),$popular_aff_filt);


						$data['months'] = array('All','01','02','03','04','05','06','07','08','09','10','11','12');
						$data['years'] = array('All',date("Y",strtotime("-3 year")),date("Y",strtotime("-2 year")),date("Y",strtotime("-1 year")),date("Y",strtotime("0 year")));
						$data['live_dashboard'] = $this->Product_model->getSettings('live_dashboard');

						$this->load->model('Total_model');
						$data['Total_model'] = $this->Total_model;
						if (isset($_GET['getChartData'])) {
							$json['chart'] = $this->Total_model->chart($post);

							echo json_encode($json);die;
						}

						$data['admin_totals'] = $this->Total_model->adminTotals();

						$data['admin_totals_week'] = $fun_c_format($this->Total_model->adminBalance(['week' => 1]));

						$data['admin_totals_month'] = $fun_c_format($this->Total_model->adminBalance(['month' => 1]));
						
						$data['admin_totals_year'] = $fun_c_format($this->Total_model->adminBalance(['year' => 1]));

						$this->load->library("socialshare");				

						$data['social_share_modal'] =  $this->socialshare->get_dynamic_social_share_btns();

						$data['integration_data'] = $this->Total_model->get_integartion_data(true, $fun_c_format);

						$audio_sound = $this->Product_model->getSettings('site', 'notification_sound');

						$front_url_slug = $this->Product_model->getSettings('security', 'front_url');
						$data['front_url_slug'] = $front_url_slug['front_url'];

						if (sizeof($audio_sound) > 0) {
							$data['notification_sound'] = $audio_sound['notification_sound'];
						}else{
							$data['notification_sound'] = '';
						}

						

						$data['status'] = $this->Order_model->status();
						$data['statistics'] = $this->Report_model->getStatistics();
						
						$this->view($data,'dashboard/dashboard');
					}
					public function popular_affiliates_sorting(){

						$hcurrency = $this->Product_model->getSettings('site', 'hide_currency_from');

						$data['hcurrency'] = (isset($hcurrency['hide_currency_from']) && str_contains($hcurrency['hide_currency_from'], 'admin'));

						if($data['hcurrency']) {
							$data['fun_c_format'] =$fun_c_format = 'c_format_nosym';
						} else {
							$data['fun_c_format'] =$fun_c_format = 'c_format';
						}
						$value=$this->input->post('value');
						$type=$this->input->post('type');
						
						$this->Setting_model->save($type, ["popular_affiliates"=>$_POST['value']]);
						$popular_aff_filter=$this->db->query("SELECT * FROM setting WHERE  setting_key='popular_affiliates' and setting_type='popular_affiliates_sorting'")->row();
						$popular_aff_filt=isset($popular_aff_filter)  ? $popular_aff_filter->setting_value : '';
						
						$data['populer_users'] = $this->Product_model->getPopulerUsers(array("limit" => 10),$popular_aff_filt);
						$json['view'] = $this->load->view("admincontrol/dashboard/popular_aff_list_tr", $data, true);

					echo json_encode($json);
					}

					public function admin_user(){

						$userdetails = $this->userdetails();

						$data['users'] = $this->db->query("SELECT users.*,countries.sortname FROM users LEFT JOIN countries ON countries.id = users.Country WHERE type='admin' AND users.id != 1")->result();

						$this->view($data,'admin_user/index');
					}


					public function admin_user_form($user_id = 0){

						$userdetails = $this->userdetails();

						$data['user'] 	= $this->Product_model->getUserDetailsObject($user_id);

						if ($this->input->server('REQUEST_METHOD') == 'POST'){

							$json = array();

							$id = (int)$this->input->post("user_id",true);

							$this->load->library('form_validation');

							$this->form_validation->set_rules('firstname', __('admin.firstname'), 'required');

							$this->form_validation->set_rules('lastname', __('admin.last_name'), 'required');

							$this->form_validation->set_rules('email', __('admin.email'), 'required|valid_email|xss_clean');

							$this->form_validation->set_rules('PhoneNumber', __('admin.phone_number'), 'required');

							$this->form_validation->set_rules('Country', __('admin.country'), 'required');

							$this->form_validation->set_rules('City', __('admin.city'), 'required');

							$this->form_validation->set_rules('Zip', __('admin.pincode'), 'required');

							$post = $this->input->post(null,true);

							if((int)$id == 0 || $post['password'] != ''){

								$this->form_validation->set_rules('password', 'Password', 'required|trim', array('required' => '%s is required'));

								$this->form_validation->set_rules('cpassword', 'Confirm Password', 'required|trim', array('required' => '%s is required'));

								$this->form_validation->set_rules('cpassword', 'Confirm Password', 'required|trim|matches[password]', array('required' => '%s is required'));

							}

							if($this->form_validation->run()){

								$errors= array();

								$checkmail = $this->Product_model->checkmail($this->input->post('email',true),$id);

								$checkuser = $this->Product_model->checkuser($this->input->post('username',true),$id);

								if(!empty($checkmail)){ $json['errors']['email'] = "Email Already Exist"; }

								if(!empty($checkuser)){ $json['errors']['username'] = "Username Already Exist"; }

								$avatar = $data['user']->avatar;

								if(!empty($_FILES['avatar']['name'])){

									$upload_response = $this->upload_photo('avatar','assets/images/users');

									if($upload_response['success']){

										$avatar = $upload_response['upload_data']['file_name'];

									}

									else{

										$json['errors']['avatar'] = $upload_response['msg'];

									}

								}

								if(!isset($json['errors'])){

									$userArray = array(

										'firstname'                 => $this->input->post('firstname',true),

										'lastname'                  => $this->input->post('lastname',true),

										'email'                     => $this->input->post('email',true),

										'username'                  => $this->input->post('username',true),

										'twaddress'                 => '',

										'type'                      => 'admin',

										'avatar'                      => $avatar,

										'address1'                  => '',

										'address2'                  => '',

										'uzip'                      => '',

										'online'                    => '0',

										'unique_url'                => '',

										'bitly_unique_url'          => '',

										'google_id'                 => '',

										'facebook_id'               => '',

										'twitter_id'                => '',

										'umode'                     => '',

										'PhoneNumber'               => $this->input->post('PhoneNumber',true),

										'Addressone'                => '',

										'Addresstwo'                => '',

										'StateProvince'             => $this->input->post('StateProvince',true),

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

										'Zip'                       => $this->input->post('Zip',true),

										'uzip'                      => $this->input->post('Zip',true),

										'City'                      => $this->input->post('City',true),

										'ucity'                     => $this->input->post('City',true),

										'ucountry'                  => $this->input->post('Country',true),

										'Country'                   => $this->input->post('Country',true),

										'value'                     => json_encode(array()),

									);



									if($post['password'] != ''){

										$userArray['password'] = sha1( $this->input->post('password',true) );

									}

									if($id == 0){

										$userArray['created_at'] = $userArray['updated_at'] = date("Y-m-d H:i:s");

										$data = $this->user->insert($userArray);

										$id = $this->db->insert_id();

									} else {

										$data = $this->user->update_user($id, $userArray);
									}

									$this->session->set_flashdata('success', __('admin.admin_updated_successfully'));

									$json['location'] = base_url('admincontrol/admin_user');
								}

							} else{

								$json['errors'] = $this->form_validation->error_array();
							}
							echo json_encode($json);die;
						}

						$data['country'] = $this->Product_model->getcountry();
						$this->view($data,'admin_user/form');

					}


	public function admin_user_delete($user_id) { 
		$userdetails = $this->userdetails();
		if($userdetails['id'] == 1){
			if((int)$user_id == 1){
				$this->session->set_flashdata('error', __('admin.error_delete_primary_admin'));
			} else {

				$this->db->query("DELETE FROM users WHERE type='admin' AND id= {$user_id}");

				$this->session->set_flashdata('success', __('admin.admin_deleted_successfully'));
			}

		} else{

			$this->session->set_flashdata('error', __('admin.can_not_allow_to_delete_admin'));
		}

		redirect('/admincontrol/admin_user');
	}

	public function logout(){
		$this->session->unset_userdata('administrator');
		$this->session->sess_destroy();
		redirect($this->admin_domain_url);
		exit;
	}

	public function deleteUser($id){

		$userdetails = $this->userdetails();

		$data['users'] = $this->admin_model->deleteUser($id);

		$this->session->set_flashdata('success', __('admin.user_deleted_successfullly'));

		redirect('admincontrol/manageUsers');
	}

	public function award_level($offset = 0){
		$userdetails = $this->userdetails();
		$award_level = $this->Product_model->getSettings('award_level','status');
		$data['award_level_status'] = $award_level['status'];
		if($data['award_level_status']){
			$this->load->library('pagination');
			$config['base_url'] = base_url('admincontrol/award_level');
			$config['uri_segment'] = 3;
			$config['per_page'] = 10;
			$config['total_rows'] = $this->Product_model->countByTable('award_level');
			$this->pagination->initialize($config);
			$data['pagination'] = $this->pagination->create_links();
			$data['award_level'] = $this->Product_model->getAllAwardLevel($config['per_page'],$offset);
			$data['CurrencySymbol'] = $this->currency->getSymbol();
		}

		$this->view($data, 'award_level/index');
	}

	public function create_award_level(){
		$userdetails = $this->userdetails();

		$award_level = $this->Product_model->getSettings('award_level','status');
		$data['award_level_status'] = $award_level['status'];
		if($data['award_level_status']){
			$data['CurrencySymbol'] = $this->currency->getSymbol();
			$data['award_levels'] = $this->Product_model->getAll('award_level',false,0,'id desc');

			if($this->input->method() == 'post'){
				$result['status'] = 0;
				$result['message'] = __('admin.something_went_wrong');

				$this->load->library('form_validation');
				$this->form_validation->set_rules('level_number',__('admin.level_number'),'trim|required|max_length[100]');
				$this->form_validation->set_rules('minimum_earning',__('admin.minimum_earning'),'trim|required');
				$this->form_validation->set_rules('sale_comission_rate',__('admin.sale_comission_rate'),'trim|required|greater_than_equal_to[0]|less_than_equal_to[100]');
				$this->form_validation->set_rules('bonus',__('admin.bonus'),'trim|required');
				if($this->form_validation->run() == TRUE){
					$jump_level = $this->input->post('jump_level',true);
					$exist = ($jump_level != '') ? $this->Product_model->checkJumpLevel($jump_level) : false;
					if(!$exist){
						$insert['level_number'] = $this->input->post('level_number',true);
						$insert['jump_level'] = ($jump_level != '') ? $jump_level : NULL;
						$insert['minimum_earning'] = $this->input->post('minimum_earning',true);
						$insert['sale_comission_rate'] = $this->input->post('sale_comission_rate',true);
						$insert['bonus'] = $this->input->post('bonus',true);
						$insert['default_registration_level'] = ($this->input->post('default_registration_level')) ? $this->input->post('default_registration_level',true) : 0;

						$success = true;
						if($insert['default_registration_level']){
							$updateDefautRegistrationLevel['default_registration_level'] = 0;
							$success = $this->db->update('award_level',$updateDefautRegistrationLevel);
						}

						if($success){
							$insertedId = $this->db->insert('award_level',$insert);
							if($insertedId){
								$result['status'] = 1;
								$result['message'] = __('admin.award_level_saved_successfully'); 
							}
						}
					} else {
						$result['message'] = __('admin.choosen_level_already_selected');
					}
				} else {
					$result['validation'] = $this->form_validation->error_array();
				}

				echo json_encode($result);
				die();
			}
		}

		$this->view($data, 'award_level/create');	
	}

	public function update_award_level($id){
		$userdetails = $this->userdetails();

		$award_level = $this->Product_model->getSettings('award_level','status');
		$data['award_level_status'] = $award_level['status'];
		if($data['award_level_status']){
			if(isset($id)){
				$id = (int) $id;
				if($id) {
					$data['award_level'] = $this->Product_model->getByField('award_level', 'id', $id);
					if($data['award_level']){
						$data['CurrencySymbol'] = $this->currency->getSymbol();
						$data['award_levels'] = $this->Product_model->getAllWithExcept('award_level','id',$id,false,0,'id desc');

						if($this->input->method() == 'post'){
							$result['status'] = 0;
							$result['message'] = __('admin.something_went_wrong');

							$this->load->library('form_validation');
							$this->form_validation->set_rules('level_number',__('admin.level_number'),'trim|required|max_length[100]');
							$this->form_validation->set_rules('minimum_earning',__('admin.minimum_earning'),'trim|required');
							$this->form_validation->set_rules('sale_comission_rate',__('admin.sale_comission_rate'),'trim|required|greater_than_equal_to[0]|less_than_equal_to[100]');
							$this->form_validation->set_rules('bonus',__('admin.bonus'),'trim|required');
							if($this->form_validation->run() == TRUE){
								$jump_level = $this->input->post('jump_level',true);
								$exist = ($jump_level != '') ? $this->Product_model->checkJumpLevel($jump_level,$id) : false;
								if(!$exist){
									$update['level_number'] = $this->input->post('level_number',true);
									$update['jump_level'] = ($jump_level != '') ? $jump_level : NULL;
									$update['minimum_earning'] = $this->input->post('minimum_earning',true);
									$update['sale_comission_rate'] = $this->input->post('sale_comission_rate',true);
									$update['bonus'] = $this->input->post('bonus',true);
									$update['default_registration_level'] = ($this->input->post('default_registration_level')) ? $this->input->post('default_registration_level',true) : 0;

									$success = true;
									if($update['default_registration_level']){
										$updateDefautRegistrationLevel['default_registration_level'] = 0;
										$success = $this->db->update('award_level',$updateDefautRegistrationLevel);
									} else {
										if($data['award_level']['default_registration_level']){
											$defaultLevel = $this->Product_model->getByField('award_level','jump_level',0);
											if($defaultLevel){
												$updateDefautRegistrationLevel['default_registration_level'] = 1;
												$success = $this->db->update('award_level',$updateDefautRegistrationLevel,['id' => $defaultLevel['id']]);
											}
										}
									}

									if($success){
										$success = $this->db->update('award_level',$update,['id' => $id]);
										if($success){
											$result['status'] = 1;
											$result['message'] = __('admin.award_level_saved_successfully'); 
										}
									}
								} else {
									$result['message'] = __('admin.choosen_level_already_selected');
								}
							} else {
								$result['validation'] = $this->form_validation->error_array();
							}

							echo json_encode($result);
							die();
						}

						$this->view($data, 'award_level/update');
					} else {
						redirect('admincontrol/award_level');
					}
				} else {
					redirect('admincontrol/award_level');
				}
			} else {
				redirect('admincontrol/award_level');
			}	
		} else {
			$this->view($data, 'award_level/update');
		}
	}

	public function delete_award_level($id){
		$userdetails = $this->userdetails();
		$result['status'] = 0;
		$result['message'] = __('admin.something_went_wrong');

		$award_level = $this->Product_model->getSettings('award_level','status');
		if($award_level['status']){
			if(isset($id)){
				$id = (int) $id;
				if($id) {
					$award_level = $this->Product_model->getByField('award_level', 'id', $id);
					if($award_level){
						$connected_level = $this->Product_model->checkLevelForUser($id);
						if(!$connected_level){
							$success = $this->db->delete('award_level',['id' => $id]);
							if($success)
								$result['status'] = 1;
								$this->session->set_flashdata('success', __('admin.award_level_deleted_successfully'));
						} else {
							$result['message'] = __('admin.level_connected_to_user');
						}
					}
				}
			}
		}

		echo json_encode($result);
		die();	
	}

	public function addproduct(){

		$userdetails = $this->userdetails();

		if(empty($userdetails)){

			redirect($this->admin_domain_url);

		}

		$data['setting'] 	= $this->Product_model->getSettings('productsetting');

		$data['country_list'] = $this->db->query("SELECT name,id FROM countries")->result();

		$data['product'] = $this->Product_model->getProductById($id);

		$this->view($data, 'product/add_product');
	}

	public function updateproduct($id = null){

		$userdetails = $this->userdetails();

		$data['product'] = $this->Product_model->getProductById($id);

		$data['tags'] = $this->Product_model->getAllTags();

		if($data['product']){

			$data['seller'] = $this->db->query("SELECT * FROM product_affiliate WHERE product_id=". (int)$data['product']->product_id ." ")->row();

			$data['seller_setting'] = $this->db->query("SELECT * FROM vendor_setting WHERE user_id=". (int)$data['seller']->user_id ." ")->row();

			$data['categories'] =$this->Product_model->getProductCategory($data['product']->product_id);

			$data['product_state'] = $this->db->query("SELECT * FROM states WHERE id=". (int)$data['product']->state_id )->row();

			$data['states'] = $this->db->query("SELECT * FROM states WHERE country_id=". (int)$data['product_state']->country_id )->result();

		}

		$data['downloads'] = $this->Product_model->parseDownloads($data['product']->downloadable_files,$data['product']->product_type);

		$data['setting'] = $this->Product_model->getSettings('productsetting');

		$data['vendor_setting'] = $this->Product_model->getSettings('vendor');

		$data['country_list'] = $this->db->query("SELECT name,id FROM countries")->result();

		$this->view($data, 'product/add_product');
	}

	public function duplicateProduct($product_id){

		$userdetails = $this->userdetails();

		$this->Product_model->duplicateProduct($product_id);

		$this->session->set_flashdata('success',__('admin.product_duplicate_successfully'));

		redirect(base_url('admincontrol/listproduct'));
	}

	public function editProduct(){

		$userdetails = $this->userdetails();

		$post = $this->input->post(null,true);
		
		if(!empty($post)){

			$product_id = (int)$this->input->post('product_id',true);

			$this->load->helper(array('form', 'url'));

			$this->load->library('form_validation');

			$this->form_validation->set_rules('product_name', __('admin.product_name_'), 'required');
			$this->form_validation->set_rules('product_description', __('admin.product_description'), 'required' );
			$this->form_validation->set_rules(
				'product_short_description', __('admin.short_description'),
				'required|min_length[5]|max_length[150]',
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

					$allKeys=array_keys($_downloads);
					if(isset($post['keep_video_files'])) 
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

				$pro_description = $_POST['product_description'];

				$doBase64Images = true;
				$imgCount = 0;

				while($doBase64Images) {
					preg_match('/src="data:(.*?)" /', $pro_description, $matchBase64);
					if(! isset($matchBase64[1]) || empty($matchBase64[1])) {
						$doBase64Images = false;
					} else {
						$image_parts = explode(";base64,", $matchBase64[1]);
						$image_type_aux = explode("image/", $image_parts[0]);
						$image_type = $image_type_aux[1];
						$image_base64 = base64_decode($image_parts[1]);
						$file = 'assets/user_upload/pro-desc-'.time().'-'.$imgCount.'.'.$image_type;
						file_put_contents($file, $image_base64);
						$pro_description = str_replace("data:".$matchBase64[1], base_url($file), $pro_description);
						$imgCount++;
					}
				}

				$details = array(

					'product_name'                 =>  $post['product_name'],

					'product_description'          =>  $pro_description,

					'product_short_description'    =>  $post['product_short_description'],

					'product_msrp'                =>  $post['product_msrp'],

					'product_price'                =>  $post['product_price'],

					'product_sku'                  =>  $post['product_sku'],

					'product_video'                =>  $post['product_video'],

					'product_price'                =>  $post['product_price'],

					'product_type'                 =>  $post['product_type'],

					'product_commision_type'       =>  $post['product_commision_type'],

					'state_id'                     =>  $post['allow_country'] == "on" ? (int)$post['state_id'] : 0,

					'product_commision_value'      =>  (float)$post['product_commision_value'],

					'product_click_commision_type' =>  $post['product_click_commision_type'],

					'product_click_commision_ppc'  =>  $post['product_click_commision_ppc'],

					'product_click_commision_per'  =>  (float)$post['product_click_commision_per'],

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
					
					if(isset($_FILES['downloadable_file']['name']) && is_countable($_FILES['downloadable_file']['name'])) 
					$count_file = count($_FILES['downloadable_file']['name']);
					else 
						$count_file =0;

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


								if(empty($FILES['downloadable_files']['error']))
								{

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
									} 
									else 
									{

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
								} 
								else 
								{

									$fileName = $zip_name;

									$zip = new ZipArchive();

									 if($zip->open(APPPATH.'/downloads/'.$zip_name, ZipArchive::CREATE) !== TRUE)
									  {
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
					}

				}
				
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

					$this->session->set_flashdata('success', __('admin.product_added_successfully'));

					$old_product_data =[];

					if($product_id){

						$old_product_data = $this->db->query("SELECT * FROM product WHERE product_id = ". (int)$product_id)->row_array();

						$details['product_updated_date'] = date('Y-m-d H:i:s');


						$this->Product_model->update_data('product', $details, array('product_id' => $product_id));

					} else {

						$details['product_created_by'] = $userdetails['id'];
						$details['product_updated_date'] = date('Y-m-d H:i:s');
						$details['product_created_date'] = date('Y-m-d H:i:s');

						$product_id = $this->Product_model->create_data('product', $details);

						$notificationData = array(

							'notification_url'          => '/listproduct/'.$product_id,

							'notification_type'         =>  'product',

							'notification_title'        =>  __('admin.new_product_added_in_affiliate_program'),

							'notification_view_user_id' =>  'all',

							'notification_viewfor'      =>  'user',

							'notification_actionID'     =>  $product_id,

							'notification_description'  =>  $post['product_name'].' product is addded by admin in affiliate Program on '.date('Y-m-d H:i:s'),

							'notification_is_read'      =>  '0',

							'notification_created_date' =>  date('Y-m-d H:i:s'),

							'notification_ipaddress'    =>  $_SERVER['REMOTE_ADDR']

						);

						$store_setting = $this->Product_model->getSettings('store');

						if($store_setting['status']) {

							$this->insertnotification($notificationData);

						}

					}

					$seofilename = $this->friendly_seo_string($post['product_name']);

					$seofilename = strtolower($seofilename);

					$product_slug = $seofilename.'-'.$product_id;

					$this->db->query("UPDATE product SET product_slug = ". $this->db->escape($product_slug) ." WHERE product_id =". $product_id);

					$seller = '';

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


						if(isset($post['admin_sale_commission_type'])){

							$seller_comm = [

								'admin_sale_commission_type'      => $post['admin_sale_commission_type'],

								'admin_commission_value'          => $post['admin_commission_value'],

								'admin_click_commission_type'     => $post['admin_click_commission_type'],

								'admin_click_amount'              => $post['admin_click_amount'],

								'admin_click_count'               => $post['admin_click_count'],

							];

							$seller = $this->db->query("SELECT * FROM product_affiliate WHERE product_id=". (int)$product_id ." ")->row();

							$this->Product_model->assignToSeller($product_id, $details, $userdetails['id'], $admin_comment, 'admin', $seller_comm);

						}

					}





					if($seller){

						$product_data = $this->db->query("SELECT * FROM product WHERE product_id = ". (int)$product_id)->row_array();

						$this->load->model('Mail_model');

						if($old_product_data['product_status'] != $product_data['product_status']){

							$this->Mail_model->vendor_product_status_change($product_id, 'vendor', true);

						}

					}


					if ($post['action'] == 'save_close') {
						$json['location'] = base_url('admincontrol/listproduct/');
					} else {
						$json['location'] = base_url('admincontrol/updateproduct/'.$product_id);
					}



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

					public function lmsResourceupdate() {
						if ($_SERVER['REQUEST_METHOD'] === 'POST') { 
							$id  		 = $this->input->post('id');
							$product_id  = $this->input->post('product_id');
							$product_details = $this->Product_model->getProductById($product_id);

							$_downloads = $this->Product_model->parseDownloads($product_details->downloadable_files,$product_details->product_type);
							foreach ($_downloads as $sectionKey => $sectionValue) {
								foreach ($sectionValue['data'] as $key => $value) {
									if($value['name'] ==$id) {
										
										if(!empty($value['zip']['mask'])){
											if(file_exists(APPPATH.'/downloads/'. $value['zip']['mask'])) {
												@unlink(APPPATH.'/downloads/'. $value['zip']['mask']);
											}
										}
										unset($_downloads[$sectionKey]['data'][$key]['zip']);
										$_downloads[$sectionKey]['data'][$key]['zip']=[];
									}
								}
							}

							$this->db->where('product_id',$product_id);
							$this->db->update('product',['downloadable_files'=>json_encode($_downloads)]);
							echo json_encode(['status'=>true]);
						}
					}

					private function getSettings($file,$data){
						extract($data);
						ob_start();
						require($file);
						return ob_get_clean();
					}



					public function store_dashboard(){

						$userdetails = $this->userdetails();

						$this->load->model('Form_model');

						$post = $this->input->post(null,true);

						$data['CurrencySymbol'] = $this->currency->getSymbol();

						if (isset($post['renderChart'])){

							if (isset($post['selectedyear'])) {

								$data = $this->Order_model->getSaleChart(array('selectedyear' => $post['selectedyear']),$post['renderChart']);

							}else{

								$data = $this->Order_model->getSaleChart(array(),$post['renderChart']);
							}

							echo json_encode($data); die;

						}


						/* Getting total order count */

						$data['total']['order_count'] = $this->db->query('SELECT COUNT(op.id) as total FROM `order_products` op LEFT JOIN `order` as o ON o.id = op.order_id WHERE o.status > 0 ')->row()->total;

						$data['form_count'] = $this->db->query('SELECT COUNT(*) as total FROM `form`')->row()->total;

						$data['coupon_count'] = $this->db->query('SELECT COUNT(*) as total FROM `coupon`')->row()->total;

						$data['form_coupon_count'] = $this->db->query('SELECT COUNT(*) as total FROM `form_coupon`')->row()->total;

						$data['product_count'] = $this->db->query('SELECT COUNT(*) as total FROM `product`')->row()->total;

						$data['category_count'] = $this->db->query('SELECT COUNT(*) as total FROM `categories`')->row()->total;

						$data['payment_gateway_count'] = count(glob(APPPATH."/payment_gateway/controllers/*.php"));


						/* Getting total admin shipping */
						$data['local_store_shipping_cost'] = $this->db->query("SELECT SUM(shipping_cost) as total FROM `order`")->row()->total;

						/* Getting total admin tax */
						$data['local_store_tax_cost'] = $this->db->query("SELECT SUM(tax_cost) as total FROM `order`")->row()->total;


						/* Getting total clients count */

						$data['client_count'] = $this->db->query('SELECT count(*) as total FROM users WHERE type like "client"')->row()->total;

						$data['client_count'] = $this->Product_model->getAllClientrecord();

						$data['guest_count'] = $this->Product_model->getAllClientrecord('guest');

						$data['ordercount']      = $this->Order_model->getCount();

						$data['salescount']      = $this->Order_model->getSale();

						$data['formcount']       = $this->Form_model->formcount();

						$data['userworldmap']    = $this->Product_model->getUserWorldMap(1);

						$this->load->model('Wallet_model');

						$this->load->model('IntegrationModel');


						$data['integration_logs']   = $this->IntegrationModel->getLogs(array('page' => 1,'limit' => 5))['records'];

						$filter_date = date('Y-m-01') . ' - ' . date('Y-m-t');

						$data['totals'] = $this->Wallet_model->getTotals(array(

							'total_commision_filter_month' => 'all',

							'total_commision_filter_year' => date("Y"),

						), true);



						$data['refer_total']        = $this->Product_model->getReferalTotals();

						$data['online_count']        = $this->Product_model->onlineCount();

						$data['integration_orders'] = $this->IntegrationModel->getOrders(array("limit" => 5));


						$totals = $this->Wallet_model->getTotals(array(), true);

						

						/* Getting total balance */

						$data['totals']['full_total_balance'] = c_format($totals['total_balance']);

						$data['totals']['total_sale_balance'] = c_format($totals['total_sale_balance']);



						/* Getting total order count */

						$data['totals']['full_local_store_hold_orders'] = $totals['store']['hold_orders'];



						$data['totals']['full_all_clicks_comm']            = $totals['all_clicks']."/".c_format($totals['all_clicks_comm']);

						$data['totals']['full_action_count_action_amount'] = (int)$totals['integration']['action_count'] .'/'. c_format($totals['integration']['action_amount']);

						$data['totals']['full_hold_action_count']          = $totals['integration']['hold_action_count'];

						$data['totals']['full_hold_orders']                = $totals['integration']['hold_orders'];

						$data['totals']['full_weekly_balance']             = c_format($totals['weekly_balance']);

						$data['totals']['full_monthly_balance']            = c_format($totals['monthly_balance']);

						$data['totals']['full_yearly_balance']             = c_format($totals['yearly_balance']);



						$this->load->model('Report_model');

						$data['live_window'] = $this->Report_model->combine_window($data);



						require APPPATH.'/core/latlong.php';

						$data['_countryCode'] = $_countryCode;


					$data['months'] = array('All','01','02','03','04','05','06','07','08','09','10','11','12');

					$data['years'] = array('All',date("Y",strtotime("-3 year")),date("Y",strtotime("-2 year")),date("Y",strtotime("-1 year")),date("Y",strtotime("0 year")));


					$this->view($data,'store/dashboard');

				}

				public function store_dashboard_order_list(){

					$userdetails = $this->userdetails();

					$get = $this->input->get(null,true);

					$post = $this->input->post(null,true);

					$filter = array(

						'limit' => 50,

						'page' => isset($get['page']) ? (int)$get['page'] : 1,

					);

					$this->load->model('Order_model');
					
					$data['status'] = $this->Order_model->status();

					$getallorders = $this->Order_model->getOrders($filter);

					$data['orders'] = $getallorders['data'];

					$this->load->library('pagination');

					$this->pagination->cur_page = $filter['page'];

					$config['base_url'] = base_url('admincontrol/store_dashboard_order_list');

					$config['per_page'] = $filter['limit'];

					$config['total_rows'] = $getallorders['total'];

					$config['use_page_numbers'] = TRUE;

					$config['page_query_string'] = TRUE;

					$config['enable_query_strings'] = TRUE;

					$config['query_string_segment'] = 'page';

					$this->pagination->initialize($config);

					$data['pagination'] = $this->pagination->create_links();

					$data['payment_methods'] = $this->Order_model->PaymentMethods();
					$json['view'] = $this->load->view("admincontrol/store/order_list_tr", $data, true);

					echo json_encode($json);
				}

				public function product_logs(){

					$category_id = (int)$this->input->post("category_id",true);

					$currentTheme = User::getActiveTheme();

					$where = "";

					$sql = "SELECT DISTINCT p.* FROM product p LEFT JOIN product_categories pc ON pc.product_id = p.product_id WHERE 1 $where ";

					$category = $this->db->query("SELECT * FROM categories WHERE id = ". (int)$category_id)->row_array();

					if($category){

						$sql .= " AND pc.category_id = ". $category['id'];
					}

					$data['category'] = $category;

					$data['products'] = $this->db->query($sql)->result_array();

					$json['html'] = $this->load->view("common/product_logs",$data,true);

					echo json_encode($json);die;

				}



				public function listproduct_ajax($page = 1){

					$userdetails = $this->userdetails();

					$get = $this->input->get(null,true);

					$post = $this->input->post(null,true);

					$filter = array(

						'page' => isset($get['page']) ? $get['page'] : $page,

						'limit' => 20,
					);


					if(isset($post['category_id']) && $post['category_id']){

						$filter['category_id'] = (int)$this->input->post('category_id');

					}



					if(isset($post['seller_id']) && $post['seller_id']){

						$filter['seller_id'] = (int)$this->input->post('seller_id');

					}
			 

					$filter['product_status_in'] =	 '1';

					if($only_review == 'reviews'){

						$filter['product_status_in'] =	 '0,2,3';
					}
			 

					$data['default_commition'] =$this->Product_model->getSettings('productsetting');

					$record = $this->Product_model->getAllProduct($userdetails['id'], $userdetails['type'], $filter);

					$data['productlist'] = $record['data'];

					$json['view'] = $this->load->view("admincontrol/product/product_list", $data, true);

					$this->load->library('pagination');

					$this->pagination->cur_page = $filter['page'];

					$config['base_url'] = base_url('admincontrol/listproduct_ajax');

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

				public function listproduct($only_review = false){

					$userdetails = $this->userdetails();

					$this->load->model('Form_model');

					$store_setting = $this->Product_model->getSettings('store');

					$data['totals'] = $this->Wallet_model->getTotals(array(), true);

					$filter = array();

					$get = $this->input->get(null,true);

					$filter['is_campaign_and_cart_product'] = 1; 

					if(isset($get['category_id']) && $get['category_id']){
						$filter['category_id'] = (int)$this->input->get('category_id');
					}

					if(isset($get['seller_id']) && $get['seller_id']){

						$filter['seller_id'] = (int)$this->input->get('seller_id');

					}

					$filter['product_status_in'] =	 '1';

					if($only_review == 'reviews'){

						$filter['product_status_in'] =	 '0,2,3';

					}
			 
					set_default_language();

					$data['productlist'] = $this->Product_model->getAllProduct($userdetails['id'], $userdetails['type'], $filter);

			 
					$data['client_count'] =$this->db->query('SELECT count(*) as total FROM users WHERE  type like "client"')->row()->total;

					$data['ordercount'] =$this->db->query('SELECT COUNT(op.id) as total FROM `order_products` op LEFT JOIN `order` as o ON o.id = op.order_id WHERE o.status > 0 ')->row()->total;

					$data['categories'] = $this->db->query("SELECT id,name FROM categories")->result_array();

					$data['vendors'] = $this->db->query("SELECT users.id,CONCAT(users.firstname,' ',users.lastname) as name FROM `product_affiliate` INNER JOIN users ON users.id= user_id GROUP by user_id")->result_array();


					$data['user'] = $userdetails;

					$this->load->library("socialshare");				

					$data['social_share_modal'] =  $this->socialshare->get_dynamic_social_share_btns();

					$this->load->model("Coupon_model");

					$data['coupons'] = $this->Coupon_model->getCoupons();

					$ptotal = $this->db->query('SELECT product_id FROM product')->num_rows();

					foreach ($data['coupons'] as $key => $value) {

						if(strtolower($value['allow_for']) == 's'){

							$data['coupons'][$key]['product_count'] = count(explode(',', $value['products']));

						}else{

							$data['coupons'][$key]['product_count'] = $ptotal;

						}

						$data['coupons'][$key]['count_coupon'] = $this->Coupon_model->getCouponCount($value['coupon_id']);

					}
					$data['currentTheme'] = User::getActiveTheme();
					$data['StoreStatus'] = User::getStoreStatus();

					$data['forms'] = $this->Form_model->getForms();	

					foreach ($data['forms'] as $key => $value) {

						$data['forms'][$key]['coupon_name'] = $this->Form_model->getFormCouponname(($value['coupon']) ? $value['coupon'] : 0);

						$data['forms'][$key]['public_page'] = base_url('form/'.$value['seo'].'/'.base64_encode($this->userdetails()['id']));

						$data['forms'][$key]['count_coupon'] = $this->Form_model->getFormCouponCount($value['form_id']);

						if($value['coupon']){

							$data['forms'][$key]['coupon_code'] = $this->Form_model->getFormCouponCode($value['coupon']);

						}

						$data['forms'][$key]['seo'] = str_replace('_', ' ', $value['seo']);

					}

					$data['product_count'] = $this->db->query("SELECT count(p.product_id) as total FROM product p 

						LEFT JOIN product_affiliate pa ON pa.product_id = p.product_id

						WHERE pa.user_id IS NULL ")->row()->total; 

					$data['form_coupons'] = $this->Form_model->getFormCoupons(); 

			 		if($only_review == 'reviews'){

						$this->view($data,'product/reviews');

					} else {

						$this->view($data,'product/index');

					}
				}

				public function bulkProductImportFromUrl() 
				{
					$userdetails = $this->userdetails();

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

						$json['warning'] = __('admin.please_enter_xml_url'); 

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
				 			else
				 				$json['warning'] = __('admin.not_valid_xm_format'); 
			  					
			    		}
			    		else 
			    		{  
			    			$json['warning'] = __('admin.url_entered_not_valid_xml_content');
			    		}
			   
					}
			 

					$data['action'] = 'confirm';
					$data['products'] = $bulkResult;
					echo $this->load->view('admincontrol/product/bulk_upload_modal', $data, true);
				}
				
				public function bulkProductImport() {

					require_once APPPATH . '/core/phpspreadsheet/autoload.php';

					$extension="";

					if(!isset($_FILES['file']['error']) || $_FILES['file']['error'] != 0){

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
			 		
			 
							for($proIndex = 1; $proIndex < sizeof($xlsdata); $proIndex++) {

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

								  	if(!empty($productArray)) {
										$cdata = $this->initialProductImportCheck($productArray);
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

							if($product_id > 0) {
								$product_exist = $this->db->query('select product_id from product where product_id='.$product_id)->row_array();
								if(empty($product_exist)) {
									return [
										"status" => "error",
										"message" => "Product not available having Product ID you provided!"
									];
								}
							}

							$this->load->helper(array('form', 'url'));

							$this->load->library('form_validation');

							$this->form_validation->reset_validation();

							$this->form_validation->set_rules('product_name', __('admin.product_name_'), 'required');

							$this->form_validation->set_rules('product_description', __('admin.product_description'), 'required');

							$this->form_validation->set_rules(

								'product_short_description', __('admin.short_description'),

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
									$pro_exist = $this->db->query('select product_id from product where product_id='.$post['product_id'])->row_array();
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

								if(!empty($post['product_created_by']) && $post['product_created_by'] !== 'admin'){
									$created_user_exist = $this->db->query('select id,is_vendor from users where username="'.$post['product_created_by'].'"')->row_array();
									if(empty($created_user_exist)) {
										$errors['product_created_by'] = "Product craeted by username not available with available vendors!";
									} else if ($created_user_exist['is_vendor'] == 0) {
										$errors['product_created_by'] = "Product craeted by username is not vendor!";
									}else {
										$post['product_created_by'] = $created_user_exist['id'];
									}
								} else {
									$post['product_created_by'] = 1;
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

										'product_status'               =>  isset($post['product_status']) ? (int)$post['product_status'] : 1,

										'product_ipaddress'            =>  $_SERVER['REMOTE_ADDR'],

										'product_recursion_type'       =>  '',

										'recursion_endtime'       =>  null,

										'product_recursion'            =>  '',

										'recursion_custom_time'        =>  0,

										'product_variations'        =>  $post['product_variations'],

										'product_tags'        =>  json_encode($post['product_tags']),

										'product_created_by' => $post['product_created_by']

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
							$json['success'] = 'Product has been updated successfully!';

						} else {

							copy('assets/images/dummy-product-img.jpg','assets/images/product/upload/thumb/dummy-product-img.jpg');
							$details['product_featured_image'] = 'dummy-product-img.jpg';
							$details['product_created_date'] = date('Y-m-d H:i:s');
							$details['product_updated_date'] = date('Y-m-d H:i:s');
							$product_id = $this->Product_model->create_data('product', $details);
							$json['created'] = true;
							$json['status'] = true;
							$json['success'] = 'Product has been added successfully!';

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

									'notification_title'        =>  __('admin.new_product_added_in_affiliate_program'),

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

						'product_status' => 'Product Status',
						'on_store' => 'Allow on Store',
						'state_id' => 'State ID',

						'product_created_by' => 'Product Created By',
					);
				}

				public function exportproduct(){

					$userdetails = $this->userdetails();

					$store_setting = $this->Product_model->getSettings('store');
					
					$json['structure_only'] = $structure_only = $this->input->post('structure_only');

					$filter = array();
					
					if($structure_only == 1) {
						$productlist = [];
					} else {
						$productlist = $this->Product_model->getAllProduct($userdetails['id'], $userdetails['type'],$filter);
					}

					$vendors = $this->db->query("SELECT users.id, users.username FROM `users`
						where is_vendor=1")->result_array();

					$created_by['cb1'] = 'admin';

					foreach($vendors as $v) {
						$created_by['cb'.$v['id']] = $v['username'];
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
									case 'product_created_by':
									$val = $created_by['cb'.$value[$name_key]];
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
					}

					if($structure_only == 1) {
						$objWriter->save(FCPATH.'assets/xml/export_products_structure.xlsx');
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
					$store_setting = $this->Product_model->getSettings('store');
					$json['structure_only'] = $structure_only = $this->input->post('structure_only');
					$filter = array();
					
					if($structure_only == 1) {
						$productlist = [];
					} else {
						$productlist = $this->Product_model->getAllProduct($userdetails['id'], $userdetails['type'],$filter);
					}

					$vendors = $this->db->query("SELECT users.id, users.username FROM `users`
						where is_vendor=1")->result_array();

					$created_by['cb1'] = 'admin';
					foreach($vendors as $v) {
						$created_by['cb'.$v['id']] = $v['username'];
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
							$dom->save(FCPATH.'assets/xml/export_products_structure.xml');
							$json['download'] = base_url('assets/xml/export_products_structure.xml');
							
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
											case 'product_created_by':
											$val = $created_by['cb'.$value[$name_key]];
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
							$dom->save(FCPATH.'assets/xml/export_products.xml');
							$json['download'] = base_url('assets/xml/export_products.xml');	
						}
			 
					echo json_encode($json);

					exit;
				}


				public function downloadprodcutxmlstructurefile($filename = NULL) {
				    $userdetails = $this->userdetails();
				    $this->load->helper('download');
				    $data = file_get_contents(FCPATH.'assets/xml/export_products_structure.xml');
				    force_download("export_products_structure.xml", $data);
				}

				public function downloadprodcutxmlfile($filename = NULL) {
				    $userdetails = $this->userdetails();
				    $this->load->helper('download');
				    $data = file_get_contents(FCPATH.'assets/xml/export_products.xml');
				    force_download("export_products.xml", $data);
				}


				public function insertnotification($postData = null){

					if(!empty($postData)) $this->Product_model->create_data('notification', $postData);

				}



				public function listorders(){

					$userdetails = $this->userdetails();

					$store_setting = $this->Product_model->getSettings('store');

					$this->load->model('Order_model');

					$data['status'] = $this->Order_model->status();

					$data['user'] = $userdetails;

					$data['wallet_status'] = $this->Wallet_model->status();

					if(isset($_POST['getOrdersRows'])) {

						$data['getallorders'] = $this->Order_model->getOrders();

						

						$json['view'] = $this->load->view("admincontrol/product/orders_list_tr", $data, true);

						echo json_encode($json); exit;
					}
					
					$this->load->model('Wallet_model');

					$totals = $this->Wallet_model->getTotals(array(), true);

					$data['full_local_store_hold_orders'] = $totals['store']['hold_orders'];

					$this->view($data,'product/orders');
				}


				public function order_change_status(){

					$order_id = (int)$this->input->post("id",true);

					$status = (int)$this->input->post("val",true);

					$remarks = '';

					$this->load->model('Order_model');

					$this->Order_model->changeStatus($order_id, $status,$remarks);

					$json['status'] = $this->Order_model->status($status);

					echo json_encode($json);

				}


				public function vieworder($order_id = null){
					$this->db->db_debug = FALSE;
					try {
						$userdetails = $this->userdetails();
						$this->load->model('Order_model');
						$this->load->model('Form_model');
						$post = $this->input->post(null,true);
						
						if($post){
							$this->Order_model->changeStatus($order_id, $post['payment_item_status'],$post['remarks']);
							$this->session->set_flashdata('success', __('admin.you_have_updated_order_status_successfully'));
							redirect('admincontrol/vieworder/'.$order_id);
							die();
						}

						$data['status'] = $this->Order_model->status();
						$data['order'] = $this->Order_model->getOrder($order_id);
						if(!empty($data['order']['id'])) {
							$data['products'] = $this->Order_model->getProducts($order_id);
							$data['totals'] = $this->Order_model->getTotals($data['products'],$data['order']);
							$data['payment_history'] = $this->Order_model->getHistory($order_id);
							$data['order_history'] = $this->Order_model->getHistory($order_id, 'order');
							$data['affiliate_user'] = $this->Order_model->getAffiliateUser($order_id);
							$data['venders'] = $this->Order_model->getVender($data['order'], $data['products']);
							$data['paymentsetting'] = $this->Product_model->getSettings('paymentsetting');
							$data['user'] = $userdetails;
							$data['orderProof'] = $this->Order_model->getPaymentProof($order_id);
							$data['shipping'] = $this->Order_model->getShippingDetails($data['order']['user_id']);
							unset($data['status']['0']); 
							$this->view( $data, 'product/vieworder');
						} else {
							$this->session->set_flashdata('error', sprintf(__("admin.order_id_no_longer_available"), $order_id));
							redirect('admincontrol/listorders/');
						}
					} catch (Exception $e) {
						$this->session->set_flashdata('error', $e->getMessage());
						redirect('admincontrol/listorders/');
					}
				}



				public function orderaction($order_id, $order_action, $transaction = false){

					$userdetails = $this->userdetails();

					if(empty($userdetails)){

						redirect($this->admin_domain_url);

					}

					if($order_action == 'delete'){

						$this->Order_model->orderdelete($order_id, $transaction);

						$this->session->set_flashdata('success', __('admin.order_has_been_deleted_successfully_'). orderId($order_id));

						redirect('admincontrol/listorders');

					}

					if($order_action == 'sendemail'){

						$this->load->model('Mail_model');

						$this->Mail_model->send_new_order_mail($order_id);

						$this->session->set_flashdata('success', __('admin.order_mail_send_successfully'));

						redirect('admincontrol/vieworder/'.$order_id);

					}

					if($order_action == 'print'){

						$data['order'] = $this->Order_model->getOrder($order_id);

						$data['affiliate_user'] = $this->Order_model->getAffiliateUser($order_id);

						$data['payment_history'] = $this->Order_model->getHistory($order_id);

						$data['products'] = $this->Order_model->getProducts($order_id);

						$data['totals'] = $this->Order_model->getTotals($data['products'],$data['order']);

						$data['status'] = $this->Order_model->status();

						$data['order_history'] = $this->Order_model->getHistory($order_id, 'order');

						$data['paymentsetting'] = $this->Product_model->getSettings('paymentsetting');

						$data['user'] = $userdetails;

						$this->load->view('admincontrol/product/printorder', $data);

					}

				}


				public function deleteusers($id = null,$type = 'user'){

					$userdetails = $this->userdetails();

					if(empty($userdetails)){

						redirect($this->admin_domain_url);

					}

					$this->Product_model->userdelete($id,$type);

					if($type == 'user'){

						$this->session->set_flashdata('success', __('admin.user_has_been_deleted_successfully'));

						redirect('admincontrol/userslist');

					} else {

						$this->session->set_flashdata('success', __('admin.client_has_been_deleted_successfully'));

						redirect('admincontrol/listclients');

					}

				}



				public function addusers($id = null){

					$userdetails = $this->userdetails();

					if(empty($userdetails)){

						redirect($this->admin_domain_url);

					}

					$data=array();

					$this->load->model('User_model');

					$this->load->model('PagebuilderModel');



					$data['countries'] = $this->User_model->getCountries();

					if ($this->input->post()) {

						$post = $this->input->post(null,true);

						$this->load->library('form_validation');

						$this->form_validation->set_rules('firstname', 'First Name', 'required|trim');

						$this->form_validation->set_rules('lastname', 'Last Name', 'required|trim');

						$this->form_validation->set_rules('email', 'Email', 'required|valid_email|xss_clean');

						$this->form_validation->set_rules('country_id', 'Country', 'required');

						if((int)$id == 0){
							$this->form_validation->set_rules('username', 'Username', 'required|trim');
						}

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

								$mobile_validation = (isset($_value['mobile_validation']) && $_value['mobile_validation'] ) ? $_value['mobile_validation'] : '';

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

							$checkuser = $this->Product_model->checkuser($this->input->post('username',true),$id);


							if(!empty($checkmail)){ $json['errors']['email'] = __('admin.email_already_exist'); }

							if(!empty($checkuser)){ $json['errors']['username'] = __('admin.username_already_exist'); }

								
							$phone = $this->input->post('phone',true);

							$phone_afftel_input_pre = $this->input->post('phone_afftel_input_pre',true);
			            	
			            	if(!empty($phone_afftel_input_pre) && !empty($phone)) {
			            		$phone = "+".$phone_afftel_input_pre." ".$phone;
			            		$checkPhone = $this->db->query("SELECT id, type FROM users WHERE phone like '%{$phone}%' ")->row_array($checkPhone);
								if(!empty($checkPhone) && $checkmail['type'] !== 'guest' && $checkPhone['id'] !== $id){ $json['errors']['phone'] = __('admin.phone_number_already_exist'); }
			            	}

			            	
							if(count($json['errors']) == 0){

								$custom_fields = array();
								$post = $this->input->post(null,true);



								foreach ($this->input->post() as $key => $value) {

									if(!in_array($key, array('affiliate_id','terms','cpassword','firstname','lastname','email','username','password', 'is_vendor', 'phone', 'refid', 'level_id', 'country_id' , 'groups')) && !strpos($key, "_afftel_input_pre")){

										if(isset($post[$key."_afftel_input_pre"]) && ! empty($post[$key."_afftel_input_pre"]) && ! empty($value)) {
				                    		$custom_fields[$key] = "+".$post[$key."_afftel_input_pre"]." ".$value;
			                    		} else {
				                    		$custom_fields[$key] = $value;
				                    	}
									}

								}

								$userGroups = $this->input->post('groups');

								if(!empty($userGroups)) {
									$userGroups = implode(',',$userGroups);
								}

								if ($this->input->post('is_vendor') == 'on') {
									$is_vendor = '1';
								}else{
									$is_vendor = '0';
								}

								

								$userArray = array(

									'firstname'                 => $this->input->post('firstname',true),

									'lastname'                  => $this->input->post('lastname',true),

									'email'                     => $this->input->post('email',true),

									'is_vendor'                 => $is_vendor,

									'phone'                     => $phone,

									'twaddress'                 => '',

									'address1'                  => '',

									'address2'                  => '',

									'uzip'                      => '',

									'avatar'                    => '',

									'online'                    => '0',

									'unique_url'                => '',

									'bitly_unique_url'          => '',

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

									'ucountry'                  => $this->input->post('country_id',true),

									'Country'                   => $this->input->post('country_id',true),

									'value'                     => json_encode(array_merge($custom_fields, $filesAttached)),

									'groups'	=> $userGroups
								);

								if($post['password'] != ''){

									$userArray['password'] = sha1( $post['password'] );

								}


								if (isset($post['refid'])) {

									$userArray['refid'] = (int)$post['refid'];

								}


								if(isset($post['level_id'])){
									if(!empty($post['level_id']) || $post['level_id'] == '0'){
										$userArray['level_id'] = (int) $post['level_id'];
									} else {
										$defaultRegistrationLevel = $this->Product_model->getByField('award_level','default_registration_level',1);
										if($defaultRegistrationLevel){
											$userArray['level_id'] = $defaultRegistrationLevel['id'];
										} else {
											$defaultLevel = $this->Product_model->getByField('award_level','jump_level',0);
											if($defaultLevel)
												$userArray['level_id'] = $defaultLevel['id'];
										}
									}
								}

								if((int)$id == 0){
									$userArray['City'] = '';
									$userArray['ucity'] = '';
									$userArray['state'] = '0';
									$userArray['created_at'] = $userArray['updated_at'] = date("Y-m-d H:i:s");
									$userArray['username'] = $this->input->post('username',true);

									$data = $this->user->insert($userArray);
									$id = $this->db->insert_id();

									$membership = $this->Product_model->getSettings('membership');

									if($is_vendor == 1) {
										$default_plan_id = $membership['default_vendor_plan_id'] ?? $membership['default_plan_id'];
									} else {
										$default_plan_id = $membership['default_affiliate_plan_id'] ?? $membership['default_plan_id'];
									}

									if($default_plan_id){
										$plan = App\MembershipPlan::find($default_plan_id);
										$user = App\User::find(array('id' => $id))->first();


										if(! empty($plan) && (($plan->user_type == 1 && $is_vendor != 1) || ($plan->user_type != 1 && $is_vendor == 1))) {
											$plan->buy($user, 1,'Automatically Added (Default Plan)','Free by Admin',0);
										}
									}
								} else {

									$data = $this->user->update_user($id, $userArray);

								}

								$this->session->set_flashdata('success', __('admin.youve_successfully_registered'));

								$json['location'] = base_url('admincontrol/userslist');

							}

						}
						
						echo json_encode($json);die;
					}

					$data['user'] 	= (array)$this->Product_model->getUserDetailsObject($id);

					$data['totals'] = $this->Wallet_model->getTotals(array("user_id" => $id), true);

					$this->load->model('PagebuilderModel');

					$register_form = $this->PagebuilderModel->getSettings('registration_builder');

					$data['data'] = json_decode($register_form['registration_builder'],1);

					$data['user_groups'] = $this->user->getgrouplist();

					$data['edit_view'] = true;

					if($id) {
						$data['read_only_user_membership_plan'] = true;
						$data['disable_username'] = true;
					}

					$data['allow_vendor_option'] = true;

					$data['edit_view_refer'] = true;

					$data['edit_view_level'] = true;

					$data['refer_users'] = $this->db->query("SELECT id,username FROM users WHERE id != ". (int)$id ." AND type='user'")->result_array();

					$data['membership'] = $this->Product_model->getSettings('membership', 'status');

					$data['award_level'] = $this->Product_model->getSettings('award_level', 'status');

					$data['userPlan'] = App\MembershipUser::select('membership_plans.name','membership_plans.commission_sale_status','award_level.level_number')->join('membership_plans','membership_plans.id','=','membership_user.plan_id')->join('award_level','award_level.id','=','membership_plans.level_id','left')->where('is_active',1)->where('user_id',$id)->first();

					$data['levels'] = $this->Product_model->getAll('award_level',false,0,'id desc');

					$data['html_form'] = $this->load->view('auth/user/templates/register_form',$data, true);

					$this->view($data,'users/add_users');

				}



				public function add_transaction(){

					$this->load->library('form_validation');

					$this->form_validation->set_rules('amount', 'Amount', 'required|trim');

					$this->form_validation->set_rules('comment', 'Comment', 'required|trim');

					$this->form_validation->set_rules('user_id', 'user_id', 'required|trim');



					if ($this->form_validation->run() == FALSE) {

						$json['errors'] = $this->form_validation->error_array();

					} else{

						$result = $this->Wallet_model->addTransaction(array(

							'status'         => 1,

							'user_id'        => $this->input->post("user_id",true),

							'amount'         => $this->input->post("amount",true),

							'comment'        => $this->input->post("comment",true) ,

							'type'           => 'admin_transaction',

							'dis_type'       => '',

							'comm_from'      => '',

							'reference_id'   => 0,

							'reference_id_2' => 0,

							'ip_details'     => '',

							'domain_name'    => '',

							'group_id'	=> time().rand(10,100)

						));

						if($result)
							$this->session->set_flashdata('success', __('admin.transaction_added'));
						else
							$this->session->set_flashdata('error', __('admin.transaction_not_add'));

						$json['location'] = base_url("admincontrol/addusers/" . $this->input->post("user_id",true));
					}

					echo json_encode($json);
				}



				public function getpaymentdetail($user_id)	{

					$userdetails = $this->userdetails();

					if(empty($userdetails)){

						redirect($this->admin_domain_url);

					}

					$data['paymentlist'] = $this->Product_model->getAllPayment($user_id);

					$data['paypalaccounts'] = $this->Product_model->getPaypalAccounts($user_id);

					$user = $this->Product_model->getUserDetailsObject($user_id);

					$data['user'] = array(

						'firstname' => $user->firstname,

						'lastname'  => $user->lastname,

						'username'  => $user->username,

						'email'     => $user->email,

						'phone'     => $user->phone,

						'address'   => $user->twaddress,

						'country'   => $this->getCountryName($user->Country),  

						'state'     => $this->getStateName($user->state),  

						'city'      => $user->City,

					);

					echo json_encode($data);

				}



				public function getCountryName($country_id){

					$query = $this->db->get_where('countries',array('id'=>$country_id))->row_array();

					if($query){

						return $query['name'];

					}else{

						return '';

					}

				}



				public function getStateName($state_id){

					$query = $this->db->get_where('states',array('id'=>$state_id))->row_array();

					if($query){

						return $query['name'];

					}else{

						return '';

					}

				}



				public function downline($user_id){

					$userdetails = $this->userdetails();

					if(empty($userdetails)){

						redirect($this->admin_domain_url);

					}

					$data['user'] 	= $this->Product_model->getUserDetailsObject($user_id);

					$mylevel = array();

					$this->view($data,'users/downline');

				}

				public function userslist(){

					$userdetails = $this->userdetails();

					$this->load->model('PagebuilderModel');

					$register_form = $this->PagebuilderModel->getSettings('registration_builder');

					$data['data'] = json_decode($register_form['registration_builder'],1);

					if ($this->input->post()) {

						$post = $this->input->post(null,true);

						if(isset($post['action']) && $post['action'] == "process_approval") {

							$approval_data = [];

							if(isset($post['approve_users']) && !empty($post['approve_users'])) {

								$approval_data['users_ids'] = $post['approve_users'];

								$approval_data['reg_approved'] = 1;

							}



							if(isset($post['decline_users']) && !empty($post['decline_users'])) {

								$approval_data['users_ids'] = $post['decline_users'];

								$approval_data['reg_approved'] = 2;

							}



							if(!empty($approval_data)) {

								$json['approvals_status'] = $this->Product_model->process_approval($approval_data);

								if($json['approvals_status']['status']) {

									$this->load->model('Mail_model');

									$user = App\User::find(array('id' => $approval_data['users_ids'][0]));

									if(isset($post['approve_users']) && !empty($post['approve_users'])) {

										$membership = $this->Product_model->getSettings('membership');

										switch ((int)$membership['status']) {
											case 0:
					                    		//disabled
											$plan_id = -1;
											break;
											case 1:
						                		//all users
											$plan_id = 0;
											break;
											case 2:
						                		//all vendors
											if($is_vendor == 1) {
												$plan_id = 0;
											} else {
												$plan_id = -1;
											}
											break;
											case 3:
						                		//all affiliates
											$plan_id = -1;
											if($is_vendor == 1) {
												$plan_id = -1;
											} else {
												$plan_id = 0;
											}
											break;
											default:
											$plan_id = -1;
											break;
										}

										if($plan_id == 0) {
											if((int)$user[0]['is_vendor'] == 1) {
												$plan_id = $membership['default_vendor_plan_id'] ?? $membership['default_plan_id'];
											} else {
												$plan_id = $membership['default_affiliate_plan_id'] ?? $membership['default_plan_id'];
											}
										}

										

										if($membership['status'] && $plan_id > 0){

											$plan = App\MembershipPlan::find($plan_id);

											if($plan){
												$plan->buy($user[0], 1, 'Default plan started','Default');

												$commission_processed = $this->db->query('SELECT id from wallet WHERE reference_id='.$approval_data['users_ids'][0].' AND type="refer_registration_commission"')->result();

												$refid = (int)$user[0]['refid'];

												if(empty($commission_processed) && $refid > 0) {
													$this->load->model('Wallet_model');
													$comission_group_id = time().rand(10,100);
													$referlevelSettings = $this->Product_model->getSettings('referlevel');
													$max_level = isset($referlevelSettings['levels']) ? (int)$referlevelSettings['levels'] : 3;
													
													$json['max_level'] = $max_level;

													$disabled_for = json_decode( (isset($referlevelSettings['disabled_for']) ? $referlevelSettings['disabled_for'] : '[]'),1);
													$refer_status = true;
													if((int)$referlevelSettings['status'] == 0){ $refer_status = false; }
													else if((int)$referlevelSettings['status'] == 2 && in_array($refid, $disabled_for)){ $refer_status = false; }

													$json['refer_status'] = $refer_status;

													if($refer_status) {
														$json['level'] = $level = $this->Product_model->getMyLevel($refid);	
														$json['max_level_user'] = [];
														for ($l=1; $l <= $max_level ; $l++) { 
															
															if($l == 1) {
																$json['max_level_user'][] = $levelUser = (int)$refid;	
															} else {
																$json['max_level_user'][] = $levelUser = (int)$level['level'.($l-1)];
															}

															$s = $this->Product_model->getSettings('referlevel_'. $l);
															

															if($s && $levelUser > 0){
																$_giveAmount = 0;
																
																if($referlevelSettings['reg_comission_type'] == 'custom_percentage'){
																	if((int) $referlevelSettings['reg_comission_custom_amt'] > 0) {
																		$_giveAmount = (($referlevelSettings['reg_comission_custom_amt'] * (float)$s['reg_commission']) / 100);
																	}
																} else if($referlevelSettings['reg_comission_type'] == 'fixed'){
																	$_giveAmount = (float)$s['reg_commission'];
																}

																$json['max_level_user']['_giveAmount'] = $_giveAmount;

																if($_giveAmount > 0){
																	$transaction_id1 = $this->Wallet_model->addTransaction(array(
																		'status'       => 1,
																		'user_id'      => $levelUser,
																		'amount'       => $_giveAmount,
																		'dis_type'     => '',
																		'comment'      => "Level {$l} : ".'Commission for new affiliate registrion Id ='. $user[0]['id'] .' | Name : '. $user[0]['firstname'] ." " .$user[0]['lastname'],
																		'type'         => 'refer_registration_commission',
																		'reference_id' => $user[0]['id'],
																		'group_id' => $comission_group_id,
																	));
																}
															}
														}
													}
												}
											}
										}

										$this->Mail_model->send_registration_approved_mail(json_decode(json_encode($user[0])));

									}


									if(isset($post['decline_users']) && !empty($post['decline_users'])) {

										$this->Mail_model->send_registration_declined_mail(json_decode(json_encode($user[0])));

									}

								}


								$json['approvals_count'] = $this->Product_model->getApprovalCounts();

								echo json_encode($json);die;

							}

						} else {

							if (isset($post['action']) && $post['action'] == 'get_all_ids') {

								$data['ids'] = array_column($this->db->query("SELECT id FROM users WHERE type='user' ")->result_array(),'id');

								echo json_encode($data);die;

							}



							$filter = array(

								'limit' => 25,

								'page' => isset($post['page']) ? (int)$post['page'] : 1,

								'reg_approved' => null

							);





							if(isset($post['apr']) && !empty($post['apr'])) {

								switch ($post['apr']) {

									case 'pending':

									$filter['reg_approved'] = 0;

									break;

									case 'approved':

									$filter['reg_approved'] = 1;

									break;

									case 'declined':

									$filter['reg_approved'] = 2;

									break;

									default:

									$filter['reg_approved'] = null;

									break;

								}

							}





							if(isset($post['name']) && $post['name'] != ''){

								$filter['name'] = $post['name'];

							}



							if(isset($post['email']) && $post['email'] != ''){

								$filter['email'] = $post['email'];

							}

							if(isset($post['groups']) && !empty($post['groups'])){

								$filter['groups'] = $post['groups'];

							}



							$userslist = $this->Product_model->getAllUsers($filter);



							$data['userslist'] = $userslist['data'];



							$this->load->library('pagination');



							$this->pagination->cur_page = $filter['page'];



							$config['base_url'] = base_url('admincontrol/userslist');

							$config['per_page'] = $filter['limit'];

							$config['total_rows'] = $userslist['total'];

							$config['use_page_numbers'] = TRUE;

							$config['page_query_string'] = TRUE;

							$config['enable_query_strings'] = TRUE;

							$_GET['page'] = $post['page'];

							$config['query_string_segment'] = 'page';

							$this->pagination->initialize($config);

							$data['commission_type'] = $this->Product_model->getCommissionType();

							$data['user'] = $userdetails;


							$data['membership'] = $this->Product_model->getSettings('membership', 'status');

							$data['award_level'] = $this->Product_model->getSettings('award_level', 'status');

							$json['table'] = $this->load->view("admincontrol/users/part/user_tr", $data, true);


							$json['pagination'] = $this->pagination->create_links();

							$json['approvals_count'] = $this->Product_model->getApprovalCounts();

							set_tmp_cache('user_list_cache');

							echo json_encode($json);die;

						}

					}

					$data['user_groups'] = $this->user->getgrouplist();
					$data['approvals_count'] = $this->Product_model->getApprovalCounts();

					$this->view($data,'users/index');

				}



				public function get_user_data(){

					// Demo Mode
					if (ENVIRONMENT === 'demo') {
						echo json_encode([
							'status' => 'error',
							'message' => 'Disabled on demo mode'
						]);
						return;
					}
					// Demo Mode

					$filter = $this->input->post(null,true);;

					$json = array();

					$this->load->model('PagebuilderModel');

					$register_form = $this->PagebuilderModel->getSettings('registration_builder');

					$datab = json_decode($register_form['registration_builder'],1);

					$data = $this->Product_model->getAllUsersExport($filter);

					$header = array(
						'auto'            => "#",
						'email'           => "Email",
						'username'        => "UserName",
						'firstname'       => "First Name",
						'lastname'        => "Last Name",
						'under_affiliate' => "Under Affiliate",
						'sortname'        => "Country",
						'password'        => "Password",
						'phone'	  		  => "Mobile Phone", 
					);

					foreach ($datab as $key => $value) {
						if($value['type'] != 'header'){
							$header[$value['name']] = $value['label'];
						}

					}

					unset($header["text-1621449816785"]);

					$header['paypal_email'] = 'Paypal Email';

					$header['payment_bank_name'] = 'Bank Name';

					$header['payment_account_number'] = 'Bank Account Name';

					$header['payment_account_name'] = 'Bank Account Number';

					$header['payment_ifsc_code'] = 'Bank IFSC Code';

					$index = 0;

					$_exportData = array();

					$_exportData[$index] = array_values($header);
			 
			 
					require_once APPPATH . '/core/phpspreadsheet/autoload.php';
			  

					if($filter['action'] == 'export'){

						foreach ($data as $key => $value) {

							$value['password'] = '';

							$index++;

							$v= json_decode($value['value'],1); 

							foreach ($header as $name_key => $_value) {

								$val = '';

								if($name_key == 'auto'){

									$val = $index;

								}

								else if(isset($value[$name_key])){

									$val = $value[$name_key];

								} else if(isset($v['custom_'.$name_key])){

									$val = $v['custom_'.$name_key];

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
						}

						$objWriter->save(FCPATH.'assets/xml/export_users.xlsx');

						$json['download'] = base_url('assets/xml/export_users.xlsx');

					} else {

						if($_FILES['import_control']['error'] == 0){

							$excelReader 	= new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
							$excelReader->setReadDataOnly(true); $excelReader->setReadEmptyCells(false);
							$excelObj = $excelReader->load($_FILES['import_control']['tmp_name']);

							$rows = $excelObj->getActiveSheet()->toArray(null,true,false,false);

							$headers = array_shift($rows);

							$db_headers = array();

							foreach ($header as $name_key => $_value) {

								$key = array_search($_value, $header); 

								$db_headers[] = $key;

							}

							$this->load->model('Imoprt_user');

							array_walk($rows, function(&$values) use($db_headers){

								$values = array_slice($values, 0, count($db_headers));

								$values = array_combine($db_headers, $values);
							});



							$json['errors'] = '<ol>';

							foreach ($rows as $key => $user) {

								$json['errors'] .=  $this->Imoprt_user->import($user,$datab);

							}

							$json['errors'] .= '</ol>';

						} else {

						$json['errors'] =  __('admin.unsupported_file_or_empty');

						}

					}

					echo json_encode($json);

				}



				public function import_user_data(){

					$filter = $this->input->post(null,true);;

					$file = $_FILES;

					if (!isset($filter['is_admin'])) { 

						$filter['user_id'] = (int)$this->userlogins()['id'];

					}

					echo "<pre>"; print_r($file); echo "</pre>";die; 

				}

				public function userslisttree(){

					$userdetails = $this->userdetails();

					if(empty($userdetails)){

						redirect($this->admin_domain_url);

					}

					$this->load->model('PagebuilderModel');
					$register_form = $this->PagebuilderModel->getSettings('registration_builder');
					$data['data'] = json_decode($register_form['registration_builder'],1);

					$data['userslist'] = $this->Product_model->getAllinOneQuery(array(),0,true,true);
					$data['userslistDetail'] = $this->Product_model->getAllUsers();

					$data['membership'] = $this->Product_model->getSettings('membership', 'status');
					$data['award_level'] = $this->Product_model->getSettings('award_level', 'status');
					$data['levels'] = $this->Product_model->getAll('award_level',false,0,'id desc');

					$this->view($data,'users/tree');

				}

				public function addons() {

					$userdetails = $this->userdetails();

					if(isset($_POST['action'])) {
						$this->load->model('Setting_model');
						$this->Setting_model->save($_POST['setting_type'], [$_POST['setting_key']=>$_POST['val']]);
						
						//enable-disable vendor mlm module
						if($_POST['setting_key']=="vendormlmmodule" && $_POST['setting_type']=="market_vendor")
						{
							///echo "execute only in vendormlmmodule";
							$status=(int)$_POST['val'];
							$query= $this->db->query("SELECT id FROM `users` where is_vendor=1 and status=1");
							$vendors=$query->result_array();
							for($i=0;$i<count($vendors);$i++)
							{
								$vid=$vendors[$i]['id'];
								$value=array("status"=>$status);
								$this->Setting_model->vendorSave($vid, "referlevel", $value);
							}
						}
						//enable-disable vendor mlm module
						else if($_POST['setting_type'] == 'market_vendor') {
							///echo "execute only in sass";
							$this->Setting_model->save("vendor", ["storestatus"=>$_POST['val']]);
						} 

						echo 'success'; exit;
					}

					$referlevel_status = $this->Product_model->getSettings('referlevel', 'status');

					$vendormlmmodule = $this->Product_model->getSettings('market_vendor', 'vendormlmmodule');

					$market_vendor_marketvendorstatus = $this->Product_model->getSettings('market_vendor', 'marketvendorstatus');

					$vendor_storestatus = $this->Product_model->getSettings('vendor', 'storestatus');

					$market_vendor_marketvendorstatus =  isset($market_vendor_marketvendorstatus['marketvendorstatus']) ? $market_vendor_marketvendorstatus['marketvendorstatus'] : 0;

					$vendor_storestatus =  isset($vendor_storestatus['storestatus']) ? $vendor_storestatus['storestatus'] : 0;

					$membership_status = $this->Product_model->getSettings('membership', 'status');

					$store_status = $this->Product_model->getSettings('store', 'status');

					$vendor_deposit_status = $this->Product_model->getSettings('vendor', 'depositstatus');

					$award_level_status = $this->Product_model->getSettings('award_level', 'status');

					$data = array (
						'mlm_admin_is_enable' => isset($referlevel_status['status']) ? $referlevel_status['status'] : 0,

						'mlm_vendor_is_enable' =>  isset($vendormlmmodule['vendormlmmodule']) ? $vendormlmmodule['vendormlmmodule'] : 0,

						'saas_is_enable' => ($market_vendor_marketvendorstatus == 1 || $vendor_storestatus == 1) ? 1 : 0,

						'membership_is_enable' => isset($membership_status['status']) ? $membership_status['status'] : 0,

						'store_is_enable' => isset($store_status['status']) ? $store_status['status'] : 0,

						'vendor_deposit_is_enable' => isset($vendor_deposit_status['depositstatus']) ? $vendor_deposit_status['depositstatus'] : 0,

						'award_level_is_enable' => isset($award_level_status['status']) ? $award_level_status['status'] : 0,
					);

					$data2['integration_modules'] = $this->modules_list('addons');
					$data['integration_modules_view'] = $this->load->view('admincontrol/integration/index', $data2, true);

					
					$this->view($data, 'addons/index');
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

				public function userslistmail(){

					$userdetails = $this->userdetails();

					$this->load->model('PagebuilderModel');

					$register_form = $this->PagebuilderModel->getSettings('registration_builder');

					$data['data'] = json_decode($register_form['registration_builder'],1);

					if ($this->input->server('REQUEST_METHOD') == 'POST'){

						$filter = $this->input->post(null,true);

						$get = $this->input->get(null,true);


						if (isset($filter['action']) && $filter['action'] == 'get_all_emails') {

							$data['emails'] = array_column($this->db->query("SELECT email FROM users WHERE type='user' ")->result_array(),'email');

							echo json_encode($data);die;

						}


						$filter['limit'] = 10;

						$filter['page'] = isset($get['per_page']) ? (int)$get['per_page'] : 1;

						$userslist = $this->Product_model->getAllUsersNormal($filter);

						$data['userslist'] = $userslist['data'];


						$this->load->library('pagination');

						$config['base_url'] = base_url('admincontrol/userslistmail');

						$config['per_page'] = $filter['limit'];

						$config['total_rows'] = $userslist['total'];

						$config['use_page_numbers'] = TRUE;

						$config['page_query_string'] = TRUE;

						$config['enable_query_strings'] = TRUE;

						$config['query_string_segment'] = 'per_page';

						$this->pagination->initialize($config);

						$data['html'] = $this->load->view('admincontrol/users/part/mail_list',$data,true);

						$data['pagination'] = $this->pagination->create_links();

						$data['total'] = $config['total_rows'];

						unset($data['userslist']);

						unset($data['data']);

						echo json_encode($data);die;

					}

					$data['country_list'] = $this->db->query("SELECT * FROM countries WHERE id IN (SELECT Country FROM users WHERE type='user' GROUP BY ucountry) ")->result();

					$data['user'] = $userdetails;

					$this->view($data,'users/mail');

				}

		public function addclients($id = null){

			$userdetails = $this->userdetails();

			if(empty($userdetails)){
				redirect($this->admin_domain_url);
			}

			$data=array();

			if ($this->input->post()) {

				$this->load->library('form_validation');

				$checkmail = $this->Product_model->checkmail($this->input->post('email',true),$id);

				$checkuser = $this->Product_model->checkuser($this->input->post('username',true),$id);

				if(!empty($checkmail))

				{

					$this->session->set_flashdata('error', __('admin.this_email_already_register'));

					$this->session->set_flashdata('postdata', $this->input->post());

					redirect('admincontrol/addclients');

				}

				elseif(!empty($checkuser))

				{
					$this->session->set_flashdata('error',__('admin.this_username_already_register'));

					$this->session->set_flashdata('postdata', $this->input->post());

					redirect('admincontrol/addclients');
				}

				else

				{
					if(empty($id)){

						$data=$this->user->insert(array(

							'firstname' => $this->input->post('firstname',true),

							'lastname'  => $this->input->post('lastname',true),

							'email'     => $this->input->post('email',true),

							'username'  => $this->input->post('username',true),

							'status'  => $this->input->post('status',true),

							'phone'  => '+'.$this->input->post('countrycode',true).' '.$this->input->post('phone',true),

							'ucountry'  => $this->input->post('country',true),

							'state'  => $this->input->post('state',true),

							'ucity'  => $this->input->post('ucity',true),

							'uzip'  => $this->input->post('uzip',true),

							'twaddress'  => $this->input->post('twaddress',true),

							'password'  => sha1($this->input->post('password',true)),

							'refid'     => 0,

							'type'      => 'client',

						));
						

					} else {

						$data = $id;

					}

					if(!empty($data))

					{

						$arrayName = array(

							'firstname' => $this->input->post('firstname',true),

							'lastname'  => $this->input->post('lastname',true),

							'email'  => $this->input->post('email',true),

							'status'  => $this->input->post('status',true),

							'ucountry'  => $this->input->post('country',true),

							'state'  => $this->input->post('state',true),

							'ucity'  => $this->input->post('ucity',true),

							'uzip'  => $this->input->post('uzip',true),
							
							'twaddress'  => $this->input->post('twaddress',true),

							'phone'  => '+'.$this->input->post('countrycode',true).' '.$this->input->post('phone',true),


						);

						if($this->input->post('password',true) != ''){

							$arrayName['password'] = sha1($this->input->post('password',true));

						}
						
						$this->user->update_user($data,$arrayName);

						$this->session->set_flashdata('success', __('admin.updated_successfully'));

						redirect('admincontrol/listclients/');

					}

				}

			}

			$data['client'] 	= $this->Product_model->getUserDetailsObject($id);
			$data['countries'] 	= $this->Product_model->getcountry('id,name');

			$this->view($data,'clients/add_clients');
		}

				public function listclients($page = 1){

					$userdetails = $this->userdetails();

					$data['countries'] 	= $this->Product_model->getcountry('id,name');

					$data['user'] = $userdetails;

					$store_setting = $this->Product_model->getSettings('store');

					if(isset($_POST['listclients'])) {

						$page = max((int)$page,1);

						$filter = array(
							'limit' => 50,
							'page' => $page 
						); 

						list($data['clientslist'],$total) = $this->Product_model->getAllClients($filter); 
						$data['start_from'] = (($page-1) * $filter['limit'])+1; 
						$json['html'] = $this->load->view("admincontrol/clients/clients_list_tr", $data, true);

						$this->load->library('pagination');
						$config['base_url'] = base_url('admincontrol/listclients/');
						$config['per_page'] = $filter['limit'];
						$config['total_rows'] = $total;
						$config['use_page_numbers'] = TRUE;
						$config['enable_query_strings'] = TRUE;
						$this->pagination->initialize($config);
						$json['pagination'] = $this->pagination->create_links();
						echo json_encode($json);die;

						exit;
					}

					$this->view($data,'clients/index');
				}

				public function affiliate_theme(){

					$userdetails = $this->userdetails();

					$commonSetting = array('email','paymentsetting','integration','login', 'loginclient','productsetting','formsetting','tnc','site','affiliateprogramsetting','store','doc','googlerecaptcha','referlevel','userdashboard');

					$post = $this->input->post(null,true);

					if(!empty($post)){

						$json = array();

						if(isset($post['loginclient'])) {
							try {
								$this->Setting_model->saveWithLanguage(
									'loginclient', 
									$post['language_id'],
									array(
										'heading' => $post['heading'],
										'content' => $post['content'],
										'about_content' => $post['about_content'],
									)
								);
								$json['success'] = true;
							} catch (\Throwable $th) {
								$json['message'] = $th->getMessage();
							}
						}
						if(isset($post['tnc'])) {
							try {
								$this->Setting_model->saveWithLanguage(
									'tnc', 
									$post['language_id'],
									array(
										'heading' => $post['policy_heading'],
										'content' => $post['policy_content'],
									)
								);
								$json['success'] = true;
							} catch (\Throwable $th) {
								$json['message'] = $th->getMessage();
							}
						}
						
						

						if (isset($post['action']) && $post['action'] == 'active_theme') {

							$login = array('front_template' => $post['id']);

							$this->Setting_model->save('login', $login);

							$json['success'] = __('admin.theme_activated_successfully');

							echo json_encode($json);die;

						}

						if(!isset($json['errors'])){

							foreach ($post as $key => $value) {

								if (in_array($key, $commonSetting)) {

									$this->Setting_model->save($key, $value);
								}

							}

							if(!isset($json['errors'])){

								$json['success'] =  __('admin.setting_saved_successfully');
							}
						}

						echo json_encode($json);die;

					} else {

						foreach ($commonSetting as $key => $value) {

							$data[$value] 	= $this->Product_model->getSettings($value);

						}

						$active_theme = [];

						$this->config->load('theme');

						$front_themes = $this->config->item('themes');

						$data['front_themes'] = [];

						foreach ($front_themes as $key => $theme) {

							if($data['login']['front_template'] != $theme['id']){

								$data['front_themes'][] = $theme;

							} else {

								$active_theme = $theme;
							}

						}

						if($active_theme){

							array_unshift($data['front_themes'], $active_theme);

						}

						$data['languages'] = $this->db->query("SELECT * FROM language where status=1")->result_array();

						$this->view($data, 'affiliate_theme/index');

					}
				}

				public function setting(){

					$userdetails = $this->userdetails();

					if(empty($userdetails)){redirect($this->admin_domain_url);}

					$post = $this->input->post(null,true);

					if(!empty($post)){

						$this->load->helper(array('form', 'url'));

						$errors= array();

						foreach($post as $key => $value) {

							if(!empty($key) && !empty($value)){

								$this->Product_model->deletesetting($key,$value,'setting');

							}

							$details = array(

								'setting_key'       =>  $key,

								'setting_value'     =>  $value,

								'setting_type'      =>  'setting',

								'setting_status'    =>  1,

								'setting_ipaddress' =>  $_SERVER['REMOTE_ADDR'],

							);

							if(!empty($key) && !empty($value)){

								$this->Product_model->create_data('setting', $details);

							}

						}

						$this->session->set_flashdata('success', __('admin.setting_updated_successfully'));

						redirect('admincontrol/setting');

					} else {

						$data['setting'] 	= $this->Product_model->getSettings('setting');

						$data['getAffiliate'] 	= $this->Product_model->getAffiliateById();

						$this->view($data,'setting/setting');

					}

				}



				public function store_setting(){
					
					
					$userdetails = $this->userdetails();

					if(empty($userdetails)){redirect($this->admin_domain_url);}

					$commonSetting = array('formsetting','productsetting','store','shipping_setting', 'tax_setting','order_comment');

					$post = $this->input->post(null,false);
					if(!empty($post)){

						$return = (isset($post['return'])) ? $post['return'] : false;

						$json = array();

						if (isset($post['recursion_endtime_status']) && isset($post['productsetting']['recursion_endtime']) && $post['productsetting']['recursion_endtime']) {

							$post['productsetting']['recursion_endtime'] = date("Y-m-d H:i:s",strtotime($post['productsetting']['recursion_endtime']));

						} else {

							$post['productsetting']['recursion_endtime'] = null;

						}

						unset($post['recursion_endtime_status']);



						if (isset($post['recursion_endtime_form_status']) && isset($post['formsetting']['recursion_endtime']) && $post['formsetting']['recursion_endtime']) {

							$post['formsetting']['recursion_endtime'] = date("Y-m-d H:i:s",strtotime($post['formsetting']['recursion_endtime']));

						} else {

							$post['formsetting']['recursion_endtime'] = null;

						}

			 
						unset($post['recursion_endtime_form_status']);

						if(!isset($post['shipping_setting']['cost'])){
							$post['shipping_setting']['cost'] = [];
						}

						foreach ($post['shipping_setting']['cost'] as $key => $value) {
							if((int)$value['country'] <= 0){
								$json['errors']['ssc-'. $key] = 'Choose country';
							}

							if((int)$value['cost'] <= 0){
								$json['errors']['ssv-'. $key] = 'Enter Shipping cost';
							}
						}

						if(!isset($post['tax_setting']['cost'])){
							$post['tax_setting']['cost'] = [];
						}

						foreach ($post['tax_setting']['cost'] as $key => $value) {
							if((int)$value['country'] <= 0){
								$json['errors']['taxc-'. $key] = 'Choose Country';
							}

							if((int)$value['cost'] <= 0){
								$json['errors']['taxv-'. $key] = 'Enter Tax Percentage';
							}
						}

						if($post['tax_setting']['tax_status'] == 1 && empty($post['tax_setting']['common_tax_percentage'])) {
							$json['errors']['common_tax_percentage'] = 'Enter Tax Percentage';
						}

						if(!isset($json['errors'])){

							if(count($_FILES) > 0){

								$path = 'assets/images/site';

								$this->load->helper('string');

								$config['upload_path'] = $path;

								$config['allowed_types'] = '*';

								$config['file_name']  = random_string('alnum', 32);

								$this->load->library('upload', $config);

								foreach ($_FILES as $fieldname => $input) {

									$this->upload->initialize($config);

									list($key,$subkey) = explode("_", $fieldname);

									$extension = pathinfo($_FILES[$fieldname]["name"], PATHINFO_EXTENSION);

									if($input['error'] == 0){

										$extension_allowed = array('jpg','jpeg','png','gif','JPG','PNG','JPEG');

										if($fieldname == 'store_favicon'){

											$extension_allowed = array('jpg','jpeg','png','gif','ico');

										}

										if(in_array($extension, $extension_allowed)){

											if (!$this->upload->do_upload($fieldname)) {

											}

											else {

												$upload_details = $this->upload->data();

												$post[$key][$subkey] = $upload_details['file_name'];


											}

										} else{

											$json['errors']["{$key}_{$subkey}"] = 'Only Image File are allowed';

										}

									}

								}

							}

							if(isset($post['store']['notification'])) {

								$notis = [];

								foreach($post['store']['notification'] as $n) {

									array_push($notis, $n);

								}				

								$post['store']['notification'] = json_encode($notis);

							}

							if(isset($post['store']['homepage_slider'])) {

								$slider = [];

								for ($i=0; $i < sizeOf($post['store']['homepage_slider']['index']); $i++) { 

									$imagePath = null;

									if(isset($post['store']['hsbackgroundimage']) && $post['store']['homepage_slider']['edited'][$i] == 1) {

										$imagePath = $post['store']['hsbackgroundimage'];

									}


									array_push($slider, array(

										'index' => $post['store']['homepage_slider']['index'][$i],

										'title' => $post['store']['homepage_slider']['title'][$i],

										'sub_title' => $post['store']['homepage_slider']['sub_title'][$i],

										'content' => $post['store']['homepage_slider']['content'][$i],

										'slider_background_image' => ($imagePath != null) ? $imagePath : $post['store']['homepage_slider']['slider_background_image'][$i],

										'button_text' => $post['store']['homepage_slider']['button_text'][$i],

										'button_link' => $post['store']['homepage_slider']['button_link'][$i],

										'slider_text_color' => $post['store']['homepage_slider']['slider_text_color'][$i],

										'button_text_color' => $post['store']['homepage_slider']['button_text_color'][$i],

										'button_bg_color' => $post['store']['homepage_slider']['button_bg_color'][$i]

									));

								}				

								$post['store']['homepage_slider'] = json_encode($slider);

							} else {

								$post['store']['homepage_slider'] = json_encode([]);

							}



							if(isset($post['store']['homepage_features'])) {

								$features = [];

								for ($i=0; $i < sizeOf($post['store']['homepage_features']['index']); $i++) { 

									$imagePath = null;

									if(isset($post['store']['hfimage']) && $post['store']['homepage_features']['edited'][$i] == 1) {

										$imagePath = $post['store']['hfimage'];

									}


									array_push($features, array(

										'index' => $post['store']['homepage_features']['index'][$i],

										'title' => $post['store']['homepage_features']['title'][$i],

										'sub_title' => $post['store']['homepage_features']['sub_title'][$i],

										'feature_image' => ($imagePath != null) ? $imagePath : $post['store']['homepage_features']['feature_image'][$i],

									));

								}				

								$post['store']['homepage_features'] = json_encode($features);

							} else {

								$post['store']['homepage_features'] = json_encode([]);

							}



							if(isset($post['store']['bs_cards'])) {

								$bsCards = [];

								for ($i=0; $i < sizeOf($post['store']['bs_cards']['index']); $i++) { 

									$imagePath = null;

									if(isset($post['store']['bscimage']) && $post['store']['bs_cards']['edited'][$i] == 1) {

										$imagePath = $post['store']['bscimage'];

									}


									array_push($bsCards, array(

										'index' => $post['store']['bs_cards']['index'][$i],

										'title' => $post['store']['bs_cards']['title'][$i],

										'sub_title' => $post['store']['bs_cards']['sub_title'][$i],

										'bg_color' => $post['store']['bs_cards']['bg_color'][$i],

										'feature_image' => ($imagePath != null) ? $imagePath : $post['store']['bs_cards']['feature_image'][$i],
										
										'button_link' => $post['store']['bs_cards']['button_link'][$i],
										'link_target' => $post['store']['bs_cards']['link_target'][$i] 

									));

								}				

								$post['store']['bs_cards'] = json_encode($bsCards);

							} else {

								$post['store']['bs_cards'] = json_encode([]);

							}


							if(isset($post['store']['social_links'])) {

								$bsCards = [];

								for ($i=0; $i < sizeOf($post['store']['social_links']['index']); $i++) { 

									$imagePath = null;

									if(isset($post['store']['slicon']) && $post['store']['social_links']['edited'][$i] == 1) {

										$imagePath = $post['store']['slicon'];

									}



									array_push($bsCards, array(

										'index' => $post['store']['social_links']['index'][$i],

										'title' => $post['store']['social_links']['title'][$i],

										'url' => $post['store']['social_links']['url'][$i],

										'image' => ($imagePath != null) ? $imagePath : $post['store']['social_links']['image'][$i],

									));

								}				

								$post['store']['social_links'] = json_encode($bsCards);

							} else {

								$post['store']['social_links'] = json_encode([]);

							}



							$custom_page_returns = [];

							if(isset($post['store']['custom_page'])) {

								$custom_page = [];

								for ($i=0; $i < sizeOf($post['store']['custom_page']['index']); $i++) { 

									$imagePath = null;

									if(isset($post['store']['cpimage']) && $post['store']['custom_page']['edited'][$i] == 1) {

										$imagePath = $post['store']['cpimage'];

									}

									$meta_where = null;

									if(isset($post['store']['custom_page']['meta_id'][$i]) && !empty($post['store']['custom_page']['meta_id'][$i])) {

										$meta_where = array('meta_id'=> $post['store']['custom_page']['meta_id'][$i]);

									}

									$meta_id = $this->Setting_model->save_meta(array('meta_key' => 'custom_page_content','meta_content'=>$post['store']['custom_page']['content'][$i]), $meta_where);

									array_push($custom_page, array(

										'index' => $post['store']['custom_page']['index'][$i],

										'title' => $post['store']['custom_page']['title'][$i],

										'slug' => $post['store']['custom_page']['slug'][$i],

										'meta_id' => $meta_id,

										'image' => ($imagePath != null) ? $imagePath : $post['store']['custom_page']['image'][$i],

									));

									array_push($custom_page_returns, array(

										'index' => $post['store']['custom_page']['index'][$i],

										'title' => $post['store']['custom_page']['title'][$i],

										'slug' => $post['store']['custom_page']['slug'][$i],

										'meta_id' => $meta_id,

										'content' => $post['store']['custom_page']['content'][$i],

										'image' => ($imagePath != null) ? $imagePath : $post['store']['custom_page']['image'][$i],

									));

								}				

								$post['store']['custom_page'] = json_encode($custom_page);

							} else {

								$post['store']['custom_page'] = json_encode([]);

							}

							if(!empty($post['store']['per_task'])) {
								$post['store']['per_task'] = array_filter($post['store']['per_task']);
							}					
							$post['store']['per_task'] = json_encode($post['store']['per_task']);

							if(isset($post['store']['footer_menu'])) {

								$available_custom_pages_slug = ['about', 'contact', 'policy', 'login', 'cart', 'profile', 'order', 'shipping', 'wishlist'];

								foreach($custom_page_returns as $page) {

									array_push($available_custom_pages_slug, $page['slug']);

								}

								$footer_menu = [];

								for ($i=0; $i < sizeOf($post['store']['footer_menu']['index']); $i++) { 

									$links = [];

									if(!empty($post['store']['footer_menu']['links'][$i]['title'])) {

									for ($j=0; $j < sizeOf($post['store']['footer_menu']['links'][$i]['title']); $j++) {

										$link_url = explode("/", $post['store']['footer_menu']['links'][$i]['url'][$j]);


										$link_slug = end($link_url);

										if($post['store']['footer_menu']['links'][$i]['type'][$j] == 'page' && !in_array($link_slug, $available_custom_pages_slug)) {

											continue;

										}

										array_push($links, [

											'title'=>$post['store']['footer_menu']['links'][$i]['title'][$j], 

											'url'=>$post['store']['footer_menu']['links'][$i]['url'][$j],

											'type'=>$post['store']['footer_menu']['links'][$i]['type'][$j]

										]);

									}
								}

									array_push($footer_menu, array(

										'index' => $post['store']['footer_menu']['index'][$i],

										'title' => $post['store']['footer_menu']['title'][$i],

										'links' => $links

									));

								}

								$post['store']['footer_menu'] = json_encode($footer_menu);

							} else {

								$post['store']['footer_menu'] = json_encode([]);

							}

							$productsetting = $post['productsetting'];			

							$formsetting = $post['formsetting'];

							if( $productsetting['product_recursion'] == 'custom_time' ){

								if($productsetting['recursion_custom_time'] < 1){

									$json['errors']['productsetting_recursion_custom_time'] = "Recursion Time is required.";

								}else{

									unset($json['errors']['productsetting_recursion_custom_time']) ;

								}

							}else{

								$post['productsetting']['recursion_custom_time'] = 0;

							}			



							if( $formsetting['form_recursion'] == 'custom_time' ){

								if($formsetting['recursion_custom_time'] < 1){

									$json['errors']['formsetting_recursion_custom_time'] = "Time is required.";

								}else{

									unset($json['errors']['formsetting_recursion_custom_time']) ;

								}

							}else{

								$post['formsetting']['recursion_custom_time'] = 0;

							}


							$staticpages = array("about_content", "contact_content", "policy_content");


							$language_id=$post['language_id'];

							foreach ($post as $key => $value) {

								if (in_array($key, $commonSetting)) {

									if($key == 'order_comment'){

										if(!isset($value['title'])){

											$value['title'] = array();
										}
										$this->Setting_model->save($key, $value);

									}
									else if($key == 'store')
									{

										$storesettings=$value;
										$staticcontent=array();
										foreach ($storesettings as $skey => $svalue) 
										{
											if(in_array($skey, $staticpages))
											{
												$staticcontent=array_merge($staticcontent,array($skey=>$svalue));
												unset($storesettings[$skey]);
											}

										} 
										$this->Setting_model->save($key, $storesettings); 
										$this->Setting_model->saveWithLanguage($key,$language_id, $staticcontent);

									}
									else
										$this->Setting_model->save($key, $value);
									

								}

							}



							if(!isset($json['errors'])){

								if($return == 'slider') {

									$json['homepage_slider'] = json_decode($post['store']['homepage_slider']);

								}

								if($return == 'features') {

									$json['homepage_features'] = json_decode($post['store']['homepage_features']);

								}

								if($return == 'bs_cards') {

									$json['bs_cards'] = json_decode($post['store']['bs_cards']);

								}



								if($return == 'footer_menu' || $return == 'custom_page') {

									$json['footer_menu'] = json_decode($post['store']['footer_menu']);

								}



								if($return == 'custom_page') {

									$json['custom_page'] = $custom_page_returns;

								}



								if($return == 'social_links') {

									$json['social_links'] = json_decode($post['store']['social_links']);

								}
								if($return == 'hbanimage') {

									$json['hbanimage'] = $post['store']['hbanimage'];

								}
								
			 

								$json['custom_page_for_menu'] = array(

									['name'=> 'About', 'slug' => 'about'],

									['name'=> 'Contact', 'slug' => 'contact'],

									['name'=> 'Policy', 'slug' => 'policy'],

									['name'=> 'Login', 'slug' => 'login'],

									['name'=> 'cart', 'slug' => 'cart'],

									['name'=> 'User Profile', 'slug' => 'profile'],

									['name'=> 'User Order', 'slug' => 'order'],

									['name'=> 'User Shipping', 'slug' => 'shipping'],

									['name'=> 'User Wishlist', 'slug' => 'wishlist'],

								);

								foreach($custom_page_returns as $page){

									array_push($json['custom_page_for_menu'], ['name'=> $page['title'], 'slug' => 'page/'.$page['slug']]);
								}
								$json['success'] =  __('admin.setting_saved_successfully');
							}

						}
						echo json_encode($json);die;

					}



					$this->load->model('PagebuilderModel');

					$data['CurrencySymbol'] = $this->currency->getSymbol();

					foreach ($commonSetting as $key => $value) {

						$data[$value] 	= $this->Product_model->getSettings($value);

						if($value == 'order_comment'){

							$data['order_comment']['title'] = json_decode($data['order_comment']['title'], true);

						}

					}


					$data['country'] = $this->Product_model->getcountry('id,name');

					$data['categories'] = $this->db->query("SELECT name,slug FROM categories")->result_array();

					$data['pages'] = array(

						['name'=> 'About', 'slug' => 'about'],

						['name'=> 'Contact', 'slug' => 'contact'],

						['name'=> 'Policy', 'slug' => 'policy'],

						['name'=> 'Login', 'slug' => 'login'],

						['name'=> 'cart', 'slug' => 'cart'],

						['name'=> 'User Profile', 'slug' => 'profile'],

						['name'=> 'User Order', 'slug' => 'order'],

						['name'=> 'User Shipping', 'slug' => 'shipping'],

						['name'=> 'User Wishlist', 'slug' => 'wishlist'],

					);

					$data['languages'] = $this->db->query("SELECT * FROM language where status=1")->result_array();
						
					$data['store_setting'] = $this->Product_model->getSettings('store');

					$custom_pages = json_decode($data['store_setting']['custom_page']);

					foreach($custom_pages as &$page){

						$page->content = $this->Setting_model->get_meta_content(['meta_id'=>$page->meta_id])->meta_content;

						array_push($data['pages'], ['name'=> $page->title, 'slug' => 'page/'.$page->slug]);

					}
			 	

					$data['store_setting']['custom_page'] = json_encode($custom_pages);
					
					$this->view($data, 'setting/store_setting');
				}



				public function market_tools_setting(){

					$userdetails = $this->userdetails();

					if(empty($userdetails)){redirect($this->admin_domain_url);}



					$commonSetting = array('marketpostback','market_vendor');

					$post = $this->input->post(null,true);



					if(!empty($post)){

						$json = array();

						if(!isset($json['errors'])){

							if (!isset($post['marketpostback']['static'])) {

								$post['marketpostback']['static'] = [];

							}

							foreach ($post as $key => $value) {

								if (in_array($key, $commonSetting)) {

									$this->Setting_model->save($key, $value);

								}

							}

							if(!isset($json['errors'])){

								$json['success'] =  __('admin.setting_saved_successfully');

							}

						}

						echo json_encode($json);die;

					}



					$data['CurrencySymbol'] = $this->currency->getSymbol();

					foreach ($commonSetting as $key => $value) {

						$data[$value] 	= $this->Product_model->getSettings($value);

					}

					$this->view($data,'setting/market_tools_setting');

				}

				public function saas_setting(){
					$userdetails = $this->userdetails();
					if(empty($userdetails)){redirect($this->admin_domain_url);}

					$store_mode = $this->Product_model->getSettings('store', 'store_mode');
					$data['store_mode'] = $store_mode['store_mode'] ?? 'cart'; //changing the store mode

					$market_vendor_marketvendorstatus = $this->Product_model->getSettings('market_vendor', 'marketvendorstatus');
					$vendor_storestatus = $this->Product_model->getSettings('vendor', 'storestatus');
					$market_vendor_marketvendorstatus =  isset($market_vendor_marketvendorstatus['marketvendorstatus']) ? $market_vendor_marketvendorstatus['marketvendorstatus'] : 0;
					$vendor_storestatus =  isset($vendor_storestatus['storestatus']) ? $vendor_storestatus['storestatus'] : 0;

					$data['saas_status'] = ($market_vendor_marketvendorstatus == 1 || $vendor_storestatus == 1) ? 1 : 0;
					if($data['saas_status']){
						$commonSetting = array('market_vendor','vendor', 'site');
						$post = $this->input->post(null,true);
						if(!empty($post)){
							$json = array();
							if(!isset($json['errors'])){
								if (!isset($post['marketpostback']['static'])) {
									$post['marketpostback']['static'] = [];
								}
								foreach ($post as $key => $value) {
									if (in_array($key, $commonSetting)) {
										$this->Setting_model->save($key, $value);
									}
								}
								if(!isset($json['errors'])){
									$json['success'] =  __('admin.setting_saved_successfully');
								}
							}
							echo json_encode($json);die;
						}

						$data['CurrencySymbol'] = $this->currency->getSymbol();
						foreach ($commonSetting as $key => $value) {
							$data[$value] 	= $this->Product_model->getSettings($value);
						}
					}

					$this->view($data,'setting/saas_setting');
				}

				public function wallet_setting(){
					$userdetails = $this->userdetails();
					if(empty($userdetails)){redirect($this->admin_domain_url);}
					$commonSetting = array('referlevel', 'site');
					$post = $this->input->post(null,true);
					if(!empty($post))
					{
						$json = array();
			 
						if($post["site"]["wallet_auto_withdrawal"]==1)
						{
							 
							if($post["site"]["wallet_auto_withdrawal_days"]=='')
								$json['errors'] = __('admin.enter_days_records_old_from_today');
							else if ($post["site"]["wallet_auto_withdrawal_limit"]=='')
								$json['errors'] = __('admin.enter_limit_of_record_auto_withdrawal');
							else if	($post["site"]["wallet_auto_withdrawal_limit"]<1 || $post["site"]["wallet_auto_withdrawal_limit"]>1000000)
								$json['errors'] = __('admin.number_of_limit_must_be_between');
						} 
						
						if(!isset($json['errors'])){
							foreach ($post as $key => $value) {
								if (in_array($key, $commonSetting)) {
									$this->Setting_model->save($key, $value);
								}
							}
							if(!isset($json['errors'])){
								$json['success'] =  __('admin.setting_saved_successfully');
							}
						}
						echo json_encode($json);die;
					}

					$data['CurrencySymbol'] = $this->currency->getSymbol();
					foreach ($commonSetting as $key => $value) {
						$data[$value] 	= $this->Product_model->getSettings($value);
					}

					$this->view($data,'setting/wallet_setting');
				}


				public function paymentsetting(){

					$this->load->library('deflanguage');

					$userdetails = $this->userdetails();

					$commonSetting = array('email','paymentsetting','integration','login', 'loginclient','productsetting','formsetting','site','affiliateprogramsetting','store','doc','googlerecaptcha','referlevel','userdashboard','security','theme','welcome');

					$data['font_families'] = [
						"PT Sans" 		=> "PT Sans",
						"LineIcons" 	=> "LineIcons",
						"FontAwesome" 	=> "FontAwesome",
						"Roboto" 		=> "Roboto",
						"Auto" 			=> "auto",
						"Cursive" 		=> "cursive",
						"Fangsong" 		=> "fangsong",
						"Emoji" 		=> "emoji",
						"Fantasy" 		=> "fantasy",
						"Inherit" 		=> "inherit",
						"Initial" 		=> "initial",
						"Math" 			=> "math",
						"Monospace" 	=> "monospace",
						"None" 			=> "none",
						"Revert" 		=> "revert",
						"Sans-Serif" 	=> "sans-serif",
						"Serif" 		=> "serif",
						"System-UI" 	=> "system-ui",
						"UI-Monospace" 	=> "ui-monospace",
						"UI-Rounded" 	=> "ui-rounded",
						"UI-Sans-Serif"	=> "ui-sans-serif",
						"UI-serif" 		=> "ui-serif",
						"Unset" 		=> "unset"
					];

					$post = $this->input->post(null,true);

					if (isset($post['send_test_mail'])) {

						$this->load->model('Mail_model');

						$json['message']=$this->Mail_model->send_test_mail($post['send_test_mail']);

						echo json_encode($json); 
						die;

					}else if(!empty($post)){


						$json = array();
						
						
						if(isset($post['googleads'])){
							
							try {
								if($post['googleads']['client_key'] != "" && $post['googleads']['unit_key'] != ""){
									$where=array();
									if($post['googleads']['id'] != ""){
										$where['id']=$post['googleads']['id'];
									}
									$checkAdsenseSec=$this->db->query("Select * from google_ads where ad_section=".$post['googleads']['ad_section']."")->row_array();
									

									$this->Setting_model->save_ads(
										array(
											'client_key' => $post['googleads']['client_key'],
											'unit_key' => $post['googleads']['unit_key'],
											'ad_section' => $post['googleads']['ad_section'],  
										),$where,$checkAdsenseSec
									);
									if(!empty($where)){
										$json['success'] = true;
									}else{
										if(empty($checkAdsenseSec)){
											$json['success'] = true;
										}else{
											$json['message']="AdSense already added for this section.";
										}
									}
									
								}
							} catch (\Throwable $th) {
								$json['message'] = $th->getMessage();
							}
							
							unset($post['googleads']);
							$googleadsStatus=$post['googleadsStatus'];
							$this->db->query("Update google_ads set status=0 where 1");
							foreach($googleadsStatus as $key => $adsStatus){
								$this->Setting_model->update_ads($key);
							}
						}
						if(isset($post['loginclient'])) {
							try {
								$this->Setting_model->save(
									'loginclient', 
									array(
										'heading' => $post['heading'],
										'content' => $post['content'],
										'about_content' => $post['about_content'],
									)
								);
								$json['success'] = true;
							} catch (\Throwable $th) {
								$json['message'] = $th->getMessage();
							}
						}

						if(isset($post['tnc']) && isset($post['tnc']['language_id']) && $post['tnc']['language_id']>0) {
							try {
								$this->Setting_model->saveWithLanguage(
									'tnc', 
									$post['tnc']['language_id'],
									array(
										'heading' => $post['tnc']['heading'],
										'content' => $post['tnc']['content'], 
									)
								);
								$json['success'] = true;
							} catch (\Throwable $th) {
								$json['message'] = $th->getMessage();
							}
						}


						$post['site']['google_analytics'] = base64_decode($post['site']['google_analytics']);

						$post['site']['faceboook_pixel'] = base64_decode($post['site']['faceboook_pixel']);

						$post['site']['global_script'] = base64_decode($post['site']['global_script']);

						$post['site']['fbmessager_script'] = base64_decode($post['site']['fbmessager_script']);

						if(isset($post['site']['hide_currency_from']) && !empty($post['site']['hide_currency_from'])) {
							$post['site']['hide_currency_from'] = implode(',',$post['site']['hide_currency_from']);
						} else {
							$post['site']['hide_currency_from'] = "";
						}

						if($post['site']['google_analytics'] != ''){

							$content = $post['site']['google_analytics'];

							preg_match_all('#<script(.*?)</script>#is', $content, $matches);



							if(count($matches[0]) != 2){

								$json['errors']['site[google_analytics]'] = 'Wrong Google Analytics Code';

							} else if (strpos($content, 'https://www.googletagmanager.com/gtag/js') === false) {

								$json['errors']['site[google_analytics]'] = 'Wrong Google Analytics Code';

							}

						}

						if($post['site']['faceboook_pixel'] != ''){

							$content = $post['site']['faceboook_pixel'];

							preg_match_all('#<script(.*?)</script>#is', $content, $matches);

							preg_match_all('#<noscript(.*?)</noscript>#is', $content, $matches2);



							if(count($matches[0]) != 1){

								$json['errors']['site[faceboook_pixel]'] = 'Wrong Facebook Pixel Code';

							} else if (strpos($content, 'https://www.facebook.com') === false) {

								$json['errors']['site[faceboook_pixel]'] = 'Wrong Facebook Pixel Code';

							}

						}

						if(!isset($json['errors'])){

							if(count($_FILES) > 0){

								$path = 'assets/images/site';

								$this->load->helper('string');

								$config['upload_path'] = $path;

								$config['allowed_types'] = '*';

								$config['file_name']  = random_string('alnum', 32);

								$this->load->library('upload', $config);


								foreach ($_FILES as $fieldname => $input) {

									$this->upload->initialize($config);

									list($key,$subkey) = explode("_", $fieldname);

									$extension = pathinfo($_FILES[$fieldname]["name"], PATHINFO_EXTENSION);

									if($input['error'] == 0){

										if($extension=='jpg' || $extension=='jpeg' || $extension=='png' || $extension=='gif'){

											if ($post[$key][$subkey]) {
												if (!$this->upload->do_upload($fieldname)) {

												}

												else {

													$upload_details = $this->upload->data();

													$post[$key][$subkey] = $upload_details['file_name'];

												}
											}

										} else{

											$json['errors']["{$key}_{$subkey}"] = 'Only Image File are allowed';

										}

									}

								}

							}
							
							if(!isset($post['site']['global_script_status'])){ $post['site']['global_script_status'] = array(); }

							if(!isset($post['marketpostback']['dynamicparam'])){ $post['marketpostback']['dynamicparam'] = array(); }

							if(!isset($post['marketpostback']['static'])){ $post['marketpostback']['static'] = array(); }

							foreach ($post as $key => $value) {
								
								if (in_array($key, $commonSetting)) {

									$this->Setting_model->save($key, $value);

								}

							}
							if(isset($post['site']['cookies_consent_mesag'])){
								$this->deflanguage->change_line('cookies_consent_custom_message',$post['site']['cookies_consent_mesag'],'admin','default');
							}

							if(!isset($json['errors'])){

								$json['success'] =  __('admin.setting_saved_successfully');
							}
						}

						echo json_encode($json);die;

					} else {

						$this->load->model('PagebuilderModel');
						 
						$data['languages'] = $this->db->query("SELECT * FROM language where status=1")->result_array();
						 
						$data['CurrencySymbol'] = $this->currency->getSymbol();

						$data['tnc'] 	= $this->Product_model->getSettingsWithLanaguage('tnc');

						
						foreach ($commonSetting as $key => $value) {

							$data[$value] 	= $this->Product_model->getSettings($value);

						}

						$data['getAffiliate'] 	= $this->Product_model->getAffiliateById();

						$data['googleads'] 	= $this->Setting_model->getGoogleAds();


						$data['users_list'] = $this->db->query("SELECT CONCAT(firstname,' ',lastname,' - (',email,')') as name ,id  FROM users WHERE type = 'user'")->result_array();

						$audio_sound = $this->Product_model->getSettings('site', 'notification_sound');

						if (sizeof($audio_sound) > 0) {
							$data['audio_sound'] = $audio_sound['notification_sound'];
						}else{
							$data['audio_sound'] = '';
						}

						$this->view($data, 'setting/paymentsetting');
					}

				}

				public function mlm_settings(){

					$userdetails = $this->userdetails();

					$mlm_status = $this->Product_model->getSettings('referlevel', 'status');
					$data['mlm_status'] = $mlm_status['status'];
					if($data['mlm_status']){
						$commonSetting = array('referlevel_1','referlevel_2','referlevel_3','referlevel_4','referlevel_5','referlevel_6','referlevel_7','referlevel_8','referlevel_9','referlevel_10','referlevel');

						$post = $this->input->post(null,true);

						if(!empty($post)){
							if(!isset($post['referlevel']['disabled_for'])){ $post['referlevel']['disabled_for'] = array(); }
							
							foreach ($post as $key => $value) {
								if (in_array($key, $commonSetting)) {
									$this->Setting_model->save($key, $value);
								}
							}

							if(!isset($json['errors'])){
								$json['success'] =  __('admin.setting_saved_successfully');
							}

							echo json_encode($json);die;
						}

						$this->load->model('PagebuilderModel');
						$data['CurrencySymbol'] = $this->currency->getSymbol();
						foreach ($commonSetting as $key => $value) {
							$data[$value] 	= $this->Product_model->getSettings($value);
						}

						$data['getAffiliate'] 	= $this->Product_model->getAffiliateById();
						$data['users_list'] = $this->db->query("SELECT CONCAT(firstname,' ',lastname,' - (',email,')') as name ,id  FROM users WHERE type = 'user'")->result_array();
					}
					

					$this->view($data,'setting/mlm_settings');
				}



				public function mlm_levels(){

					$userdetails = $this->userdetails();

					$mlm_status = $this->Product_model->getSettings('referlevel', 'status');
					$data['mlm_status'] = $mlm_status['status'];
					if($data['mlm_status']){
						$commonSetting = array('referlevel','referlevel_1','referlevel_2','referlevel_3','referlevel_4','referlevel_5','referlevel_6','referlevel_7','referlevel_8','referlevel_9','referlevel_10','referlevel_11','referlevel_12','referlevel_13','referlevel_14','referlevel_15','referlevel_16','referlevel_17','referlevel_18','referlevel_19','referlevel_20','referlevel');

						$post = $this->input->post(null,true);

						if(!empty($post)){

							if(!isset($post['referlevel']['disabled_for'])){ 
								$post['referlevel']['disabled_for'] = array(); 
							}

							foreach ($post as $key => $value) {
								if (in_array($key, $commonSetting)) {
									$this->Setting_model->save($key, $value);
								}
							}

							if(!isset($json['errors'])){
								$json['success'] =  __('admin.setting_saved_successfully');
							}

							echo json_encode($json);die;

						}

						$this->load->model('PagebuilderModel');

						$data['CurrencySymbol'] = $this->currency->getSymbol();

						foreach ($commonSetting as $key => $value) {

							$data[$value] 	= $this->Product_model->getSettings($value);

						}

						$data['getAffiliate'] 	= $this->Product_model->getAffiliateById();

						$data['users_list'] = $this->db->query("SELECT CONCAT(firstname,' ',lastname,' - (',email,')') as name ,id  FROM users WHERE type = 'user'")->result_array();
					}


					$this->view($data,'setting/mlm_levels');
				}



				public function generateproductcode($affiliateads_id = null){

					$userdetails = $this->userdetails();

					if(empty($userdetails)){

						redirect($this->admin_domain_url);

					}

					else {

						if($affiliateads_id){

							$data['product_id'] = $affiliateads_id;

							$data['user_id'] = $userdetails['id'];

							$data['getProduct'] 	= $this->Product_model->getProductByIdArray($affiliateads_id);

							$this->load->view('admincontrol/product/generatecode', $data);

						}

					}

				}



				public function setAffiliateClick($aff_id = null, $user_id = null ){

				}



				public function addsaveads($adsId = null){

					$userdetails = $this->userdetails();

					$post = $this->input->post(null,true);

					if(!empty($post)){

						$postdata['postdata'] =  $post;

						$InseredData['affiliateads_type'] =  $post['affiliateads_type'];

						if(!empty($_FILES['adsfile']['name'])){

							$upload_response = $this->upload_photo('adsfile','assets/images/ads');

							if($upload_response['success']) $postdata['adsfile'] = $upload_response['upload_data']['file_name'];

							else $errors = $upload_response['msg'];

						} else {

							if($post['adsfile']) $postdata['adsfile'] = $post['adsfile'];

							else $postdata['adsfile'] = '';

						}


						$InseredData['affiliateads_metadata'] =  json_encode($postdata);

						$InseredData['affiliateads_status'] =  $post['affiliateads_status'];

						if(empty($errors)){

							if(!empty($adsId)){

								$InseredData['affiliateads_updated_by'] =  $userdetails['id'];

								$InseredData['affiliateads_updated'] =  date('Y-m-d H:i:s');

								$this->Product_model->update_data('affiliateads', $InseredData,array('affiliateads_id' => $adsId));

								$this->session->set_flashdata('success', $post['affiliateads_type'].__('admin.updated_successfully'));

								redirect('admincontrol/affiliateadslist');

							} else {

								$InseredData['affiliateads_ipaddress'] =  $_SERVER['REMOTE_ADDR'];

								$InseredData['affiliateads_created_by'] =  $userdetails['id'];

								$InseredData['affiliateads_created'] =  date('Y-m-d H:i:s');

								$this->Product_model->create_data('affiliateads', $InseredData);

								$this->session->set_flashdata('success', $post['affiliateads_type'].__('admin.save_successfully'));

								redirect('admincontrol/affiliateadslist');

							}

						} else {

							$this->session->set_flashdata('error', $errors);

							redirect('admincontrol/'.$post['error']);

						}

					}

				}


				public function editProfile(){

					$userdetails = $this->userdetails();
			 
					$post = $this->input->post(null,true);

					$id =  $userdetails['id'];

					if(!empty($post)){

						$rules = $this->user->profile_rules;

						$this->form_validation->set_rules($rules);

						if($this->form_validation->run())

						{

							$errors= array();

							$details = array(

								'firstname'     =>  $this->input->post('firstname',true),

								'lastname'      =>  $this->input->post('lastname',true),

								'email'         =>  $this->input->post('email',true),

								'PhoneNumber'   =>  $this->input->post('PhoneNumber',true),

								'Country'       =>  $this->input->post('Country',true),

								'StateProvince' =>  $this->input->post('StateProvince',true),

								'City'          =>  $this->input->post('City',true),

								'Zip'           =>  $this->input->post('Zip',true),

							);

							if(!empty($_FILES['avatar']['name'])){

								$upload_response = $this->upload_photo('avatar','assets/images/users');

								if($upload_response['success']){

									$details['avatar'] = $upload_response['upload_data']['file_name'];

								}

								else{

									$errors['avatar_error'] = $upload_response['msg'];

								}

							}

							if(empty($errors)){

								$this->session->set_flashdata('success', __('admin.profile_updated_successfully'));

								$this->user->update($id, $details);

								$user_details_array=$this->user->get_user_by_id($id);

								$this->session->set_userdata(array('administrator'=>$user_details_array));

							}

							redirect('admincontrol/editProfile');

						}

						else

						{

							$this->session->set_flashdata('error', validation_errors());

							redirect('admincontrol/editProfile');

						}

						redirect($this->admin_domain_url);

					}else{

						$data['user']  = $this->user->get($id);

						$data['country'] = $this->Product_model->getcountry();

						$this->view($data,'users/edit_profile');

					}

				}



				public function getstate($country_id = null) {

					$userdetails = $this->userdetails();

					$post = $this->input->post(null,true);



					if(empty($userdetails)){

						redirect($this->admin_domain_url);

					}

					else {

						if(!empty($post['country_id'])){

							$states = $this->Product_model->getAllstate($post['country_id']);

						}

						echo '<option selected="selected">'.__('admin.select_state').'</option>';

						if(!empty($states)){
							$isIDs= !empty($post['isId']) ? true :false;
							foreach($states as $state){

								echo '<option value="'.$state[$isIDs?'id':'name'].'">'.$state['name'].'</option>';

							}

						}

						die;

					}

				}



				public function delete_image($image_id = null){

					$userdetails = $this->userdetails();

					$post = $this->input->post(null,true);



					if(empty($userdetails)){

						redirect('usercontrol');

					}

					else {

						if(!empty($post['image_id'])){

							$this->Product_model->deleteImage($post['image_id']);

						}

					}

				}

				public function resetnotify(){
					$this->output->set_content_type('application/json');

					$result['status'] = 0;

					$userdetails = $this->userdetails();
					if(!empty($userdetails)){
						$notifications = $this->Product_model->getnotificationnew('admin',null);

						foreach($notifications as $key => $value)
							$success = $this->Product_model->update_data('notification',array('notification_is_read' => 1),array('notification_id' => $value['notification_id']));
						
						if($success)
							$result['status'] = 1;
					}

					$this->output->set_output(json_encode($result));
				}

				public function updatenotify($country_id = null) {

					$userdetails = $this->userdetails();

					$json = array();

					$post = $this->input->post(null,true);

						if(!empty($post['id'])){

							$noti = $this->db->query("SELECT * FROM notification WHERE notification_id= ". $post['id'])->row();

							if($noti->notification_type == 'membership_order'){

								if($noti->notification_viewfor == 'admin'){

									$json['location'] = base_url($noti->notification_url);

								} else{

									$json['location'] = base_url('usercontrol/'.$noti->notification_url);

								}

							}

							else if($noti->notification_type == 'integration_program'){

								if($noti->notification_viewfor == 'admin'){

									$json['location'] = base_url($noti->notification_url);

								} else{

									$json['location'] = base_url('usercontrol/'.$noti->notification_url);

								}

							}

							else if($noti->notification_type == 'integration_tools'){

								if($noti->notification_viewfor == 'admin'){

									$json['location'] = base_url('integration/'.$noti->notification_url);

								} else{

									$json['location'] = base_url('usercontrol/'.$noti->notification_url);

								}

							}

							else if($noti->notification_type == 'integration_orders'){

								$json['location'] = base_url('admincontrol/'.$noti->notification_url);

							} else if($noti->notification_type == 'integration_click'){

								$json['location'] = base_url('admincontrol/'.$noti->notification_url);

							}else{

								$json['location'] = base_url('admincontrol/'.$noti->notification_url);

							}

							$this->Product_model->update_data('notification', array('notification_is_read' => 1),array('notification_id' => $post['id']));
						}

					echo json_encode($json);

				}


				public function getnotificationnew() {

					$userdetails = $this->userdetails();

					if(empty($userdetails)){

						redirect($this->admin_domain_url);

					}

					else {

						$notifications = $this->Product_model->getnotificationnew('admin');

						echo trim(count($notifications));
					}

				}



				public function getnotificationall() {

					$userdetails = $this->userdetails();

					if(empty($userdetails)){

						redirect($this->admin_domain_url);

					}

					else {

						$notifications = $this->Product_model->getnotificationall('admin');

						echo trim(count($notifications));
					}

				}



public function getnotification() {
    $userdetails = $this->userdetails();
    if (empty($userdetails)) {
        redirect($this->admin_domain_url);
    } else {
        $notifications = $this->Product_model->getnotification('admin');

        if (!empty($notifications)) {
            foreach ($notifications as $notification) {
                $icon = '';
                switch ($notification['notification_type']) {
                    case 'order':
                        $icon = 'mdi mdi-cart-outline';
                        break;
                    case 'client':
                        $icon = 'mdi mdi-account-circle';
                        break;
                    case 'paymentrequest':
                        $icon = 'mdi mdi-account-circle';
                        break;
                    case 'user':
                        $icon = 'mdi mdi-account';
                        break;
                    case 'product':
                        $icon = 'mdi mdi-basket';
                        break;
                    case 'commissionrequest':
                        $icon = 'mdi mdi-cash-usd';
                        break;
                }

                echo '<a href="javascript:void(0)" onclick="shownofication('.$notification['notification_id'].',\''.base_url().'admincontrol'.$notification['notification_url'].'\')" class="dropdown-item notify-item d-flex align-items-center py-3">';

                // Improved styling for the icon using Bootstrap 5
                echo '<div class="notify-icon bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">';
                echo '<i class="'.$icon.'" style="font-size: 1.5rem;"></i>';
                echo '</div>';

                // Improved styling for the text
                echo '<div class="flex-grow-1">';
                echo '<p class="mb-0 notify-details"><b>'.$notification['notification_title'].'</b></p>';
                echo '<small class="text-muted">'.$notification['notification_description'].'</small>';
                echo '</div>';
                echo '</a>';
            }
        }

        die;
    }
}




				public function productupload($id = null){

					$userdetails = $this->userdetails();

					if(empty($userdetails)){

						redirect($this->admin_domain_url);

					}

					if(empty($id)){

						$this->session->set_flashdata('error', __('admin.photo_can_not_be_uploaded'));

						redirect('admincontrol/listproduct');

					}

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

							$cpt = count(array_filter($_FILES['product_multiple_image']['name']));

							if($cpt > 0)
							{
								$this->load->helper('string');

								$config = array(

									'upload_path' => 'assets/images/product/upload/',

									'allowed_types' => 'png|gif|jpeg|jpg|PNG|GIF|JPEG|JPG',

									'file_name'  => random_string('alnum', 32),
									'create_thumb'   => TRUE,
											'width' => 300,
									'height' => 300

								);

								$this->load->library('upload', $config);

								$this->load->library('image_lib');

								$this->upload->initialize($config);

								for($i=0; $i<$cpt; $i++)
								{           

									$_FILES['product_multiple_images']['name'] = $files['product_multiple_image']['name'][$i];

									$_FILES['product_multiple_images']['type'] = $files['product_multiple_image']['type'][$i];

									$_FILES['product_multiple_images']['tmp_name'] = $files['product_multiple_image']['tmp_name'][$i];

									$_FILES['product_multiple_images']['error'] = $files['product_multiple_image']['error'][$i];

									$_FILES['product_multiple_images']['size'] = $files['product_multiple_image']['size'][$i];    


									$filename=random_string('alnum', 32);
									$upload_response = $this->upload_photo('product_multiple_images','assets/images/product/upload/thumb');
									//$this->upload->do_upload('product_multiple_images');
									//$upload_response = $this->upload_photo('product_multiple_images','assets/images/product/upload/'); 

									$upload_details = $this->upload->data();



									/*$config1 = array(

										'source_image' => $upload_details['full_path'],

										'new_image' => 'assets/images/product/upload/thumb',

										'maintain_ratio' => true,
										'allowed_types' => 'png|gif|jpeg|jpg|PNG|GIF|JPEG|JPG',


										'dynamic_output' => 1,
											
										'width' => 300,

										'height' => 300

									);


									$this->image_lib->clear();
									$this->image_lib->initialize($config1);

									$this->image_lib->resize();
			 
									$this->upload->do_upload('product_multiple_images');
									$this->image_lib->clear();*/


									if($upload_details){

										$details['product_media_upload_path'] = $upload_details['file_name'];

									}else{

										$errors['avatar_error'] = $upload_details['msg'];

									}


									//$upload_response = $this->upload_photo('video_thumbnail_image','assets/images/product/upload/thumb'); 

									$details['product_media_upload_created_date'] = date('Y-m-d H:i:s');

									$this->Product_model->create_data('product_media_upload', $details);				

								}
							}
							else
							{
								$this->session->set_flashdata('error', 'Please select at least one image');

								redirect('admincontrol/productupload/'.$id);

								exit();
							}

						}

						if(!empty($errors)){

							$this->session->set_flashdata('error', $errors['avatar_error']);

							redirect('admincontrol/productupload/'.$id);

							exit();

						}

						$this->session->set_flashdata('success', __('admin.product_images_added_successfully'));

						redirect('admincontrol/productupload/'.$id);

					}

					$data['imageslist'] = $this->Product_model->getAllImages($id);

					$data['user'] = $userdetails;

					$this->view($data,'product/productupload');

				}



				public function videoupload($id = null){

					$userdetails = $this->userdetails();

					if(empty($id)){ redirect('admincontrol/listproduct'); }

					$post = $this->input->post(null,true);

					if(!empty($post)){

						$this->load->helper(array('form', 'url'));

						$this->load->library('form_validation');

						$this->form_validation->set_rules('product_media_upload_path', __('admin.product_video'), 'required|trim');

						if($this->form_validation->run() == true)

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

								redirect('admincontrol/videoupload/'.$id);

								exit();

							}

							$this->session->set_flashdata('success', __('admin.product_video_and_images_added_successfully'));

							$details['product_media_upload_created_by'] = $userdetails['id'];

							$details['product_media_upload_created_date'] = date('Y-m-d H:i:s');

							$this->Product_model->create_data('product_media_upload', $details);

							$data['productinfo'] = $this->Product_model->getProductByIdArray($id);

							$notificationData = array(

								'notification_url'          => '/videoupload/'.$id,

								'notification_type'         =>  'product',

								'notification_title'        =>  __('admin.new_product_added_in_affiliate_program'),

								'notification_view_user_id' =>  '',

								'notification_viewfor'      =>  'user',

								'notification_actionID'     =>  $id,

								'notification_description'  =>  'New Video uploaded on product '.$data['productinfo']['product_name'].' by admin in affiliate Program on '.date('Y-m-d H:i:s'),

								'notification_is_read'      =>  '0',

								'notification_created_date' =>  date('Y-m-d H:i:s'),

								'notification_ipaddress'    =>  $_SERVER['REMOTE_ADDR']

							);

							$this->insertnotification($notificationData);

							redirect('admincontrol/videoupload/'.$id);

						}

						else

						{

							$this->session->set_flashdata('error', validation_errors());
							redirect('admincontrol/videoupload/'.$id);

						}

					} else {

						$data['videoimageslist'] = $this->Product_model->getAllVideoImages($id);

						$data['videoslist'] = $this->Product_model->getAllVideos($id);

						$data['user'] = $userdetails;

						$this->view($data,'product/videoupload');

					}

				}



				public function deleteAllusersMultiple(){

					// Demo Mode
					if (ENVIRONMENT === 'demo') {
						echo json_encode([
							'status' => 'error',
							'message' => 'Disabled on demo mode'
						]);
						return;
					}
					// Demo Mode

					$json = array();

					$post = $this->input->post(null,true);

					$ids  = explode(",", $post['ids']);

					$html = '';

					$html .= "<h6>". __('admin.following_affiliate_are_remove_from_this_affiliate_are_you_sure') ."</h6> <div class='scroll-table'><table class='table table-sm table-striped'>";

					$html .= "<thead><tr><th>...</th><th>". __('admin.name') ."</th><th>". __('admin.total_unpaid_commission') ."</th></tr></thead><tbody>";

					foreach ($ids as $key => $id) {

						$user = $this->db->query("SELECT id,firstname,lastname,refid FROM users WHERE id = ". (int)$id)->row();

						if($user){

							$unpaid_commition = (float)$this->db->query('SELECT sum(amount) as total FROM wallet WHERE status IN (1,2) AND user_id = '. (int)$id )->row_array()['total'];

							$unpaid_commition += (float)$this->db->query('SELECT sum(commission) as total FROM integration_orders WHERE user_id = '. (int)$id )->row_array()['total'];



							$html .= "<tr><td>{$user->id}</td><td>{$user->firstname} {$user->lastname}</td><td>". c_format($unpaid_commition) ."</td></tr>";

						}

					}

					$html .= '</tbody></table></div>';



					$json['message'] = $html;

					echo json_encode($json);

				}

				public function deleteGoogleAds(){

					$responce=$this->db->query("DELETE FROM google_ads WHERE id =". $_POST['id']);
					if($responce){
						$json['success'] ='success';
						$json['message'] = "<h6>". __('admin.ads_delete_successfully') ."</h6>";
					}else{
						$json['errors'] ='errors';
						$json['message'] = "<h6>". __('admin.ads_delete_failed') ."</h6>";
					}
					echo json_encode($json);
					
				}
				function refreshGoogleAds(){
					$data['googleads'] 	= $this->Setting_model->getGoogleAds();
					$json['adsList'] = $this->load->view("admincontrol/users/part/ads_tr", $data, true);
					echo json_encode($json);
				}

				public function deleteAllusers(){

					$json = array();

					$post = $this->input->post(null,true);

					$user = $this->db->query("SELECT id,firstname,lastname,refid FROM users WHERE id = ". (int)$post['id'])->row();

					if($user){

						$mylevels = $this->db->query("SELECT id,firstname,lastname,refid FROM users WHERE refid = ". (int)$post['id'])->result_array();

						if($mylevels){

							$level = $this->Product_model->getMyLevel($user->id);

							$firstLevel = (int)$level['level1'];

							$json['message'] = "<h6>". __('admin.following_affiliate_are_remove_from_this_affiliate_are_you_sure') ."</h6>";

						} else {

							$json['message'] = "<h2 class='text-center'>". __('admin.are_you_sure') ."</h2>";

						}


						$unpaid_commition = (float)$this->db->query('SELECT sum(amount) as total FROM wallet WHERE status IN (1,2) AND user_id = '. (int)$post['id'] )->row_array()['total'];

						$unpaid_commition += (float)$this->db->query('SELECT sum(commission) as total FROM integration_orders WHERE user_id = '. (int)$post['id'] )->row_array()['total'];

						$json['message'] .= "<br> ". __('admin.total_unpaid_commission') ." : ". c_format($unpaid_commition);

					}

					echo json_encode($json);

				}



				public function showTree(){

					$post = $this->input->post(null,true);

					$userdetails = $this->userdetails();

					$user_id = (int)$post['id'];

					$data['user'] 	= $this->Product_model->getUserDetailsObject($user_id);

					$json['html'] = $this->load->view('admincontrol/users/downline_modal', $data, true);

					echo json_encode($json);

				}



				public function myreferal_ajax($user_id){

					$data = $this->Product_model->getMyUnder($user_id);

					echo json_encode($data);

				}



				public function deleteUsersConfirm(){

					// Demo Mode
					if (ENVIRONMENT === 'demo') {
						echo json_encode([
							'status' => 'error',
							'message' => 'Disabled on demo mode'
						]);
						return;
					}
					// Demo Mode

					$json = array();

					$ids = array();

					$post = $this->input->post(null,true);



					if(isset($post['id']) && (int)$post['id'] == 0){

						$ids[] = $post['id'];

					} else{

						$ids = explode(",", $post['id']);

					}

					
					
					foreach ($ids as $key => $id) {
			
						$user = $this->db->query("SELECT id,firstname,lastname,refid FROM users WHERE id = ". (int)$id)->row();

						if($user){

							if(isset($post['delete_transaction']) && $post['delete_transaction'] == 'true'){

								$this->db->query("DELETE FROM wallet WHERE user_id =". (int)$id);

								$this->db->query("DELETE FROM wallet WHERE user_id  IN (SELECT id FROM users WHERE refid = $id) AND type='refer_registration_commission'");

								$this->db->query("UPDATE integration_orders SET user_id = 0, commission = 0 WHERE  user_id =". (int)$id);
							}

							$mylevels = $this->db->query("SELECT id,firstname,lastname,refid FROM users WHERE refid = ". (int)$id)->result_array();
							
							if($mylevels){

								$level = $this->Product_model->getMyLevel($user->id);

								$firstLevel = 0;

								foreach ($mylevels as $key => $value) {

									$this->db->query("UPDATE users SET refid = {$firstLevel} WHERE id = ". $value['id']);

								}		

							}

							$this->Product_model->deleteusers($user->id);
						}

					}

					$this->session->set_flashdata('success', __('admin.users_delete_successfully'));

					echo json_encode($json);

				}



				public function delete($id = null){

					if(!empty($id)){

						$res = $this->Product_model->deleteusers($id);

						$this->session->set_flashdata('success', __('admin.users_delete_successfully'));

						redirect(base_url() . 'admincontrol/userslist');

					}

					$this->session->set_flashdata('error', __('admin.users_delete_failed'));

					redirect(base_url() . 'admincontrol/userslist');

				}

				public function deleteAllproducts(){

					$post = $this->input->post(null,true);

					if(!empty($post['product']) || !empty($post['form'])){

						if(isset($post['product'])){
							
							foreach($post['product'] as $id){

								if(!empty($id)) {
									$orderProduct = $this->db->query('SELECT id FROM order_products WHERE product_id = '.$id)->row();

									if(empty($orderProduct)) {
										$this->Product_model->deleteproducts((int)$id);
									} else {
										$this->session->set_flashdata('error', __('admin.some_order_product_not_deleted'));
									}
								}
							}
						}

						if(isset($post['form'])){

							$this->load->model("Form_model");

							foreach($post['form'] as $id){
								if(!empty($id)) {
									$this->Form_model->deleteforms((int)$id);
								}
							}

						}

						$this->session->set_flashdata('success', __('admin.product_is_deleted_successfully'));

						redirect(base_url() . 'admincontrol/listproduct');

					} else {

						$id = (int)$this->input->get('delete_id');

						$orderProduct = $this->db->query('SELECT id FROM order_products WHERE product_id = '.$id)->row();

						if(empty($orderProduct)) {
							$res = $this->Product_model->deleteproducts($id);

							$this->session->set_flashdata('success', __('admin.product_is_deleted_successfully'));
						} else {
							$this->session->set_flashdata('error', __('admin.order_product_not_deleted'));
						}
						
						redirect(base_url() . 'admincontrol/listproduct');
					}

					$this->session->set_flashdata('error', __('admin.product_delete_failed'));

					redirect(base_url() . 'admincontrol/listproduct');
				}


				public function user_info(){

					$userdetails = $this->userdetails();

					return $this->Product_model->user_info($userdetails['id']);

				}



				public function docs(){

					$data['doc_config'] = $this->Product_model->getSettings('doc');
					$this->load->view($control.'/includes/header', $data);
					$this->load->view($control.'/includes/sidebar', $data);
					$this->load->view($control.'/includes/topnav', $data);
					$this->load->view('admincontrol/document/docs', $this);
					$this->load->view($control.'/includes/footer', $data);

				}



				public function form_manage($form_id = 0){

					$userdetails = $this->userdetails();

					$this->load->model("Form_model");
					$this->load->model("Product_model");

					$store_setting = $this->Product_model->getSettings('store');
					$is_campaign_product = $store_setting['theme'] =="0" ? "AND is_campaign_product = 0" : "AND is_campaign_product = 1";

					$data['form'] = $this->Form_model->getForm($form_id);

					$data['form']['seo'] = str_replace('_', ' ', $data['form']['seo']);

					$data['product'] = $this->db->query("SELECT DISTINCT p.product_id,p.product_name,p.product_price,p.product_type,p.allow_shipping FROM product p 

						LEFT JOIN product_affiliate pa ON pa.product_id = p.product_id

						WHERE pa.user_id IS NULL $is_campaign_product")->result_array();


					if(!$data['product']){ redirect("admincontrol/form", 'refresh'); }

					$data['setting'] = $this->Product_model->getSettings('formsetting');

					$data['coupons'] = $this->db->query("SELECT * FROM `form_coupon`")->result_array();	

					$data['paymets'] = json_decode($data['form']['payment']);

					$this->view($data,'form/form');

				}





				public function form(){

					$userdetails = $this->userdetails();

					$store_setting = $this->Product_model->getSettings('store');

					$this->load->model("Form_model");

					$data['forms'] = $this->Form_model->getForms();	

					foreach ($data['forms'] as $key => $value) {

						$data['forms'][$key]['coupon_name'] = $this->Form_model->getFormCouponname(($value['coupon']) ? $value['coupon'] : 0);

						$data['forms'][$key]['public_page'] = base_url('form/'.$value['seo'].'/'.base64_encode($this->userdetails()['id']));

						$data['forms'][$key]['count_coupon'] = $this->Form_model->getFormCouponCount($value['form_id']);

						if($value['coupon']){

							$data['forms'][$key]['coupon_code'] = $this->Form_model->getFormCouponCode($value['coupon']);

						}

						$data['forms'][$key]['seo'] = str_replace('_', ' ', $value['seo']);

					}



					$data['product_count'] = $this->db->query("SELECT count(p.product_id) as total FROM product p 

						LEFT JOIN product_affiliate pa ON pa.product_id = p.product_id

						WHERE pa.user_id IS NULL ")->row()->total; 	



					$this->load->library("socialshare");				

					$data['social_share_modal'] =  $this->socialshare->get_dynamic_social_share_btns();

					$data['form_coupons'] = $this->Form_model->getFormCoupons();

					$this->view($data,'form/index');

				}



				public function save_form(){

					$userdetails = $this->userdetails();

					$this->load->library('form_validation');

					$this->load->model("Form_model");

					$json = array();

					$json['errors'] = array();

					$data = $this->input->post(null,true);

					$this->form_validation->set_rules('title', 'Name', 'required|trim');

					$this->form_validation->set_rules('description', 'Description', 'required|trim');

					$this->form_validation->set_rules('allow_for', 'Allow For', 'required|trim');

					$this->form_validation->set_rules('footer_title', 'Footer Content', 'required|trim');

					$this->form_validation->set_rules('seo', 'Seo', 'required|trim');

					$form_id = 0;



					if( $data['form_recursion_type'] == 'custom' ){

						$this->form_validation->set_rules('form_recursion', 'Form Recursion', 'required');

						if( $data['form_recursion'] == 'custom_time' ){

							$this->form_validation->set_rules('recursion_custom_time', 'Custom Time', 'required|greater_than[0]');

						}

					}		

					$form_recursion = ($data['form_recursion_type'] && $data['form_recursion_type'] != 'default') ? $data['form_recursion'] : "";

					$recursion_custom_time = ($form_recursion == 'custom_time' ) ? $data['recursion_custom_time'] : 0;

					if($this->form_validation->run() == FALSE) {

						$json['errors'] = array_merge($this->form_validation->error_array(), $json['errors']);

					}else{

						$data['fevi_icon'] = '';

						if(!empty($_FILES['form_fevi_icon']['name'])){

							$upload_response = $this->upload_photo('form_fevi_icon','assets/images/form/favi/');

							if($upload_response['success']) $data['fevi_icon'] = $upload_response['upload_data']['file_name'];

							else $json['errors']['form_fevi_icon'] = $upload_response['msg'];

						} 

						$product_array = isset($data['product']) && is_array($data['product']) ? $data['product'] : []; 

						if(empty($json['errors'])){

							$form = array(

								'allow_for'             => $data['allow_for'],

								'coupon'                => $data['coupon'],

								'description'           => $data['description'],

								'seo'                   => str_replace(' ', '_', trim($data['seo'])),

								'footer_title'          => $data['footer_title'],

								'product'               => implode(",", $product_array),

								'title'                 => $data['title'],

								'google_analitics'      => $data['google_analitics'],

								'form_recursion_type'   => $data['form_recursion_type'],

								'status'                => isset($data['status']) ? (int)$data['status'] : 1,

								'form_recursion'        => $form_recursion,

								'recursion_custom_time' => (int)$recursion_custom_time,

								'recursion_endtime'     => (isset($data['recursion_endtime_status']) && $data['recursion_endtime']) ? date("Y-m-d H:i:s",strtotime($data['recursion_endtime'])) : null,

								'created_at' => date("Y-m-d H:i:s")

							);



							$form['sale_commision_type']  = $data['form_commision_type'];

							$form['sale_commision_value'] = $data['form_commision_value'];

							$form['click_commision_type'] = $data['form_click_commision_type'];

							$form['click_commision_ppc']  = $data['form_click_commision_ppc'];

							$form['click_commision_per']  = $data['form_click_commision_per'];



							if($data['fevi_icon']){ $form['fevi_icon'] = $data['fevi_icon']; }

							if($data['id'] > 0){

								$this->db->update("form",$form,['form_id' => $data['id']]);

								$form_id = $data['id'];

							} else {

								$form['created_at'] = date("Y-m-d H:i:s");

								$this->db->insert("form",$form);

								$form_id = $this->db->insert_id();

							}



							if($data['redirect'] == 'save_stay'){

								if($data['id'] > 0){

									$json['location'] = base_url("admincontrol/form_manage/".$data['id']);

								} else {

									$json['location'] = base_url("admincontrol/form_manage/".$form_id );

								}

							} else {

								$json['location'] = base_url("admincontrol/listproduct");

							}

						}

					}



					echo json_encode($json);

				}



				public function form_coupon_manage($form_coupon_id = 0){

					$userdetails = $this->userdetails();

					$store_setting = $this->Product_model->getSettings('store');

					$this->load->model("Form_model");

					$data['form_coupon'] = $this->Form_model->getFormCoupon($form_coupon_id);	


					$this->view($data,'form/form_coupon');
				}



				public function form_coupon_delete($form_coupon_id){

					$userdetails = $this->userdetails();

					$this->load->model("Form_model");

					$this->Form_model->deleteFormCoupon($form_coupon_id);

					
					redirect(base_url("admincontrol/listproduct"));

				}



				public function form_coupon(){

					$userdetails = $this->userdetails();

					$store_setting = $this->Product_model->getSettings('store');

					$this->load->model("Form_model");

					$data['form_coupons'] = $this->Form_model->getFormCoupons();

					$this->view($data,'form/form_coupon_index');
				}



				public function save_form_coupon(){

					$userdetails = $this->userdetails();

					$store_setting = $this->Product_model->getSettings('store');

					$this->load->library('form_validation');

					$json = array();

					$this->form_validation->set_rules('name', 'Name', 'required|trim');

					$this->form_validation->set_rules('code', 'Coupon Code', 'required|trim');

					$this->form_validation->set_rules('type', 'Type', 'required|trim');	

					$this->form_validation->set_rules('discount', 'Discount', 'required|trim');

					$this->form_validation->set_rules('date_start', 'Start Date', 'required|trim');

					$this->form_validation->set_rules('date_end', 'End Date', 'required|trim');

					$this->form_validation->set_rules('status', 'Status', 'required|trim');

					if ($this->form_validation->run() == FALSE) {

						$json['errors'] = $this->form_validation->error_array();

					} else {

						$data = $this->input->post(null,true);

						$coupon = array(

							'name'       => $data['name'],

							'code'       => $data['code'],

							'type'       => $data['type'],			

							'discount'   => $data['discount'],

							'date_start' => date("Y-m-d", strtotime($data['date_start'])),

							'date_end'   => date("Y-m-d", strtotime($data['date_end'])),

							'uses_total' => $data['uses_total'],

							'status'     => $data['status'],			

							'date_added' => date("Y-m-d H:i:s"),

						);

						if($data['id'] > 0){

							unset($coupon['date_added']);

							$this->db->update("form_coupon",$coupon,['form_coupon_id' => $data['id']]);

						} else {

							$this->db->insert("form_coupon",$coupon);

							$coupon_id = $this->db->insert_id();

						}

						$json['location'] = base_url("admincontrol/listproduct");

					}

					echo json_encode($json);

				}





				public function generateformcode($form = 0){

					$userdetails = $this->userdetails();

					if(empty($userdetails)){

						redirect($this->admin_domain_url);

					}

					else {

						if($form){

							$data['form_id'] = $form;

							$data['user_id'] = $userdetails['id'];

							$this->load->model("Form_model");

							$data['getForm'] 	= $this->Form_model->getForm($form);

							$this->load->view('admincontrol/form/generatecode', $data);

						}

					}

				}

				public function deleteAllforms($form = 0){

					$this->load->model("Form_model");

					$post = $this->input->post(null,true);

					if(!empty($post['checkbox'])){

						foreach($post['checkbox'] as $id){				 

							if(!empty($id)){

								$res = $this->Form_model->deleteforms($id);

							}

						}

						$this->session->set_flashdata('success', __('admin.form_is_deleted_successfully'));

						redirect(base_url() . 'admincontrol/listproduct');

					}

					$this->session->set_flashdata('error', __('admin.form_delete_failed'));

					redirect(base_url() . 'admincontrol/listproduct');

				}

				public function form_delete($form = 0){ 

					$this->load->model("Form_model");

					if(!empty($form)){		

						$res = $this->Form_model->deleteforms($form);			

						$this->session->set_flashdata('success', __('admin.form_is_deleted_successfully'));

						redirect(base_url() . 'admincontrol/listproduct');

					}

					$this->session->set_flashdata('error', __('admin.form_delete_failed'));

					redirect(base_url() . 'admincontrol/listproduct');
				}

				public function currency_list(){

					$userdetails = $this->userdetails();

					$data['currencys'] = $this->db->query("SELECT * FROM currency")->result_array();

					$this->load->model("Form_model");

					$data['form_coupons'] = $this->Form_model->getFormCoupons();

					$this->view($data,'currency/index');

				}

				public function currency_delete($currency_id){

					$userdetails = $this->userdetails();

					$this->db->query("DELETE FROM currency WHERE is_default = 0 AND currency_id = ". (int)$currency_id);

					$this->session->set_flashdata('success', __('admin.currency_delete_success'));

					redirect(base_url() . 'admincontrol/currency_list');

				}


				public function currency_edit($currency_id = 0){

					$userdetails = $this->userdetails();

					$post = $this->input->post(null,true);

					if (isset($post['currency_id'])) {


						$original_value = $this->db->query("SELECT title FROM currency WHERE currency_id = ".$currency_id)->row()->title ;
						if($this->input->post('title') != $original_value) {
							$is_unique =  '|is_unique[currency.title]';
						} else {
							$is_unique =  '';
						}

						$this->form_validation->set_rules('title', 'Name', 'required|trim'.$is_unique);
						
						$this->form_validation->set_rules('code', 'Currency Code', 'required|trim');

						$this->form_validation->set_rules('replace_comma_symbol', 'Replace Comma Symbol', 'required|trim');
						$this->form_validation->set_rules('decimal_symbol', 'Decimal Symbol', 'required|trim');

						$this->form_validation->set_rules('value', 'Value', 'required|trim|greater_than[0]');	
						
						if ($this->form_validation->run() == FALSE) {

							$json['errors'] = $this->form_validation->error_array();

						} else {

							$data = $this->input->post(null,true);

							$coupon = array(

								'title'         => $data['title'],

								'code'          => $data['code'],

								'symbol_left'   => $data['symbol_left'],

								'symbol_right'  => $data['symbol_right'],

								'replace_comma_symbol'  => $data['replace_comma_symbol'],

								'decimal_symbol'  => $data['decimal_symbol'],

								'decimal_place' => $data['decimal_place'],

								'value'         => $data['value'],

								'status'        => $data['status'],

								'is_default'    => isset($data['is_default']) ? 1 : 0,

								'date_modified' => date("Y-m-d H:i:s"),

							);

							if($data['currency_id'] > 0){

								$this->db->update("currency",$coupon,['currency_id' => $data['currency_id']]);

							} else {

								$this->db->insert("currency",$coupon);

								$data['currency_id'] = $this->db->insert_id();

							}

							if(isset($data['is_default'])){

								$this->db->query("UPDATE currency SET is_default = 0");

								$this->db->query("UPDATE currency SET is_default = 1 WHERE currency_id = ". $data['currency_id']);

							}
							$this->session->set_flashdata('success', __('admin.currency_saved_successfully'));
							$json['location'] = base_url("admincontrol/currency_list");

						}

						echo json_encode($json);die;

					}

					if($currency_id > 0){

						$data['currencys'] = $this->db->query("SELECT * FROM currency WHERE currency_id = {$currency_id} ")->row_array();

					}

					$this->load->model("Form_model");

					$data['form_coupons'] = $this->Form_model->getFormCoupons();
					
					$this->view($data,'currency/form');

				}

				public function currency_refresh() {

					$currency_data = array();

					$selected = $this->db->query("SELECT * FROM currency WHERE is_default=1")->row_array();

					$query = $this->db->query("SELECT * FROM currency WHERE code != '" . $selected['code'] . "'")->result_array();

					foreach ($query as $result) {

						$currency_data[] = $selected['code'] . $result['code'] . '=X';

						$currency_data[] = $result['code'] . $selected['code'] . '=X';

					}

					echo 'http://download.finance.yahoo.com/d/quotes.csv?s=' . implode(',', $currency_data) . '&f=sl1&e=.json';

					$curl = curl_init();

					curl_setopt($curl, CURLOPT_URL, 'http://download.finance.yahoo.com/d/quotes.csv?s=' . implode(',', $currency_data) . '&f=sl1&e=.json');

					curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

					curl_setopt($curl, CURLOPT_HEADER, false);

					curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30);

					curl_setopt($curl, CURLOPT_TIMEOUT, 30);

					$content = curl_exec($curl);

					curl_close($curl);

					$line = explode("\n", trim($content));

					echo "<pre>"; print_r($line); echo "</pre>";die; 

					for ($i = 0; $i < count($line); $i = $i + 2) {

						$currency = utf8_substr($line[$i], 4, 3);

						$value = utf8_substr($line[$i], 11, 6);

						if ((float)$value < 1 && isset($line[$i + 1])) {

							$value = (1 / utf8_substr($line[$i + 1], 11, 6));

						}

						if ((float)$value) {

							$this->db->query("UPDATE " . DB_PREFIX . "currency SET value = '" . (float)$value . "', date_modified = '" .  $this->db->escape(date('Y-m-d H:i:s')) . "' WHERE code = '" . $this->db->escape($currency) . "'");

						}

					}

					$this->db->query("UPDATE " . DB_PREFIX . "currency SET value = '1.00000', date_modified = '" .  $this->db->escape(date('Y-m-d H:i:s')) . "' WHERE code = '" . $this->db->escape($selected['code']) . "'");

					$this->cache->delete('currency');

				}


				public function order_attechment($filename,$mask){

					$userdetails = $this->userdetails();

					$file = APPPATH .'/downloads_order/'. $filename;

					$mask = basename($mask);

					if (!headers_sent()) {

						if (file_exists($file)) {

							header('Content-Type: application/octet-stream');

							header('Content-Disposition: attachment; filename="' . ($mask ? $mask : basename($file)) . '"');

							header('Expires: 0');

							header('Cache-Control: must-revalidate, post-check=0, pre-check=0');

							header('Pragma: public');

							header('Content-Length: ' . filesize($file));

							if (ob_get_level()) { ob_end_clean(); }

							readfile($file, 'rb');

							exit();

						} else {

							exit('Error: Could not find file ' . $file . '!');

						}

					} else {

						exit('Error: Headers already sent out!');

					}

				}



				public function u_status_toggle($user_id){

					// Demo Mode
					if (ENVIRONMENT === 'demo') {
						$this->session->set_flashdata('error', __('admin.demo_mode'));
						redirect('admincontrol/userslist');
						return;
					}
					// Demo Mode

					$userdetails = $this->userdetails();

					$this->db->query("UPDATE users SET status = IF(status=1,0,1) WHERE id= ". (int)$user_id);

					$this->session->set_flashdata('success', __('admin.user_status_change_success'));

					redirect(base_url() . 'admincontrol/userslist');

				}



				public function info_remove_tran_multiple(){

					$uniqIDS = [];

					$post = $this->input->post(null,true);

					$ids = explode(",", $post['ids']);

					$html = "";

					$html = '<h6 class="text-center">'.__('admin.important_this_action_can_not_be_undo').'</h6><hr>';

					$table = "<div class='transaction-datails-div-hidden' style='display:none;'><table class='table table-stripped'><thead style='width: calc( 100% - 1em )'><tr style='display: table;  width: 100%; table-layout: fixed;'><td class='text-center'>".__('admin.transaction_id')."</td><td class='text-center'>".__('admin.username')."</td><td class='text-center'> ".__('admin.amount')."</td></tr></thead><tbody style=' display: block; max-height: 200px; overflow-y: auto;'>";

					foreach ($ids as $key => $id) {

						$dataCollection = $this->Wallet_model->getDeleteData($id);

						foreach ($dataCollection as $data) {

							if(in_array($id, $uniqIDS)) {
								continue;
							}

							$amountTotal += ($data['amount'] > 0) ? $data['amount'] : 0;

							$table .= '<tr style="display: table; width: 100%; table-layout: fixed;"><td class="text-center">'. $data['id'] .'</td><td class="text-center">'. $data['name'] .'</td><td class="text-center">'. c_format($data['amount']) .'</td></tr>';

							$uniqIDS[] = $data['id'];
						}
					}

					$table .= "</tbody></table></div>";

					$html .= "<p><strong>".count($uniqIDS)."</strong> ".__('admin.transactions having a total amount')." <strong>".c_format($amountTotal)."</strong> ".__('admin.will_get_deleted')." <a href='javascript:void(0)' class='show-trans-aff-details'><br>".__('admin.see_details')."</a></p>";

					$html .= $table;

					$html .= "<br>
					<div class='row'>
					  <div class='col-sm-6'>
					    <button data-bs-dismiss='modal' class='btn btn-primary btn-block'>" . __('admin.cancel') . "</button>
					  </div>
					  <div class='col-sm-6'>
					    <button class='btn btn-danger btn-block' delete-mmultiple-confirm='" . $post['ids'] . "'>" . __('admin.yes_confirm') . "</button>
					  </div>
					</div>";



					$json['html'] = $html;

					echo json_encode($json);
				}

				public function confirm_remove_tran_multi(){

					$json = [];

					$json['dataCollection'] = [];

					$post = $this->input->post(null,true);

					$ids = explode(",", $post['id']);
					
					
					foreach ($ids as $key => $id) {

						$json['dataCollection'][] = $dataCollection = $this->Wallet_model->getDeleteData($id);
						
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
						

						}
						
						echo json_encode($json);
					}



					public function info_remove_tran(){

						$delete_id = $this->input->post("id",true);

						$dataCollection = $this->Wallet_model->getDeleteData((int)$delete_id);

						$html = "";

						$html = '<h6 class="text-center">'.__('admin.important_this_action_can_not_be_undo').'</h6>';

						$html .= '<hr>';

						$table = "<div class='transaction-datails-div-hidden' style='display:none;'><table class='table table-stripped'><thead style='width: calc( 100% - 1em )'><tr style='display: table;  width: 100%; table-layout: fixed;'><td class='text-center'>".__('admin.transaction_id')."</td><td class='text-center'>".__('admin.username')."</td><td class='text-center'> ".__('admin.amount')."</td></tr></thead><tbody style=' display: block; max-height: 200px; overflow-y: auto;'>";

						$amountTotal = 0;

						foreach ($dataCollection as $data) {

							$amountTotal += ($data['amount'] > 0) ? $data['amount'] : 0; 

							$table .= '<tr style="display: table; width: 100%; table-layout: fixed;"><td class="text-center">'. $data['id'] .'</td><td class="text-center">'. $data['name'] .'</td><td class="text-center">'. c_format($data['amount']) .'</td></tr>';
						}

						$table .= "</tbody></table></div>";

						$html .= "<p><strong>".count($dataCollection)."</strong> ".__('admin.transactions_having_total_amount')." <strong>".c_format($amountTotal)."</strong> ".__('admin.will_get_deleted')." <a href='javascript:void(0)' class='show-trans-aff-details'><br>".__('admin.see_details')."</a></p>";

						$html .= $table;

						$html .= "<br><div class='row'> <div class='col-sm-6'><button data-bs-dismiss='modal' class='btn btn-primary btn-block'>".__('admin.cancel')."</button></div> <div class='col-sm-6'><button class='btn btn-danger  btn-block' delete-tran-confirm='". $delete_id ."'>".__('admin.yes_confirm')."</button></div> </div>";


						$json['html'] = $html;

						echo json_encode($json);
					}

					public function info_remove_tran_by_commission(){

						$dataCollection = $this->Wallet_model->getDeleteData((int)$this->input->post("id",true));

						$id = $this->input->post("id",true);

						$status_type = $this->input->post("status_type",true);

						$delete_id = $this->input->post("id",true);

						$this->db->query("DELETE FROM wallet_requests WHERE FIND_IN_SET($delete_id,tran_ids)");

						$html = '<h6 class="text-center">'.__('admin.important_this_action_can_not_be_undo').'</h6><hr>';

						$html .= '<p> '.__('admin.once_you_change_status_trash_or_cancel').' </p>';
						$html .= '<hr>';

						$table = "<div class='transaction-datails-div-hidden' style='display:none;'><table class='table table-stripped'><thead style='width: calc( 100% - 1em )'><tr style='display: table;  width: 100%; table-layout: fixed;'><td class='text-center'>".__('admin.transaction_id')."</td><td class='text-center'>".__('admin.username')."</td><td class='text-center'> ".__('admin.amount')."</td></tr></thead><tbody style=' display: block; max-height: 200px; overflow-y: auto;'>";

						$amountTotal = 0;

						foreach ($dataCollection as $data) {

							$amountTotal += ($data['amount'] > 0) ? $data['amount'] : 0;

							$table .= '<tr style="display: table; width: 100%; table-layout: fixed;"><td class="text-center">'. $data['id'] .'</td><td class="text-center">'. $data['name'] .'</td><td class="text-center">'. c_format($data['amount']) .'</td></tr>';
						}

						$table .= "</tbody></table></div>";

						$html .= "<p><strong>".count($dataCollection)."</strong> ".__('admin.transactions_having_total_amount')." <strong>".c_format($amountTotal)."</strong> ".__('admin.will_get_affected')." <a href='javascript:void(0)' class='show-trans-aff-details'><br>".__('admin.see_details')."</a></p>";

						$html .= $table;

						$html .= "<br><div class='row'> <div class='col-sm-6'><button data-bs-dismiss='modal' class='btn btn-primary btn-block'>".__('admin.cancel')."</button></div> <div class='col-sm-6'><button class='btn btn-danger  btn-block' id='". $id ."' status_type='". $status_type ."' change-tran-by-commi-confirm>".__('admin.yes_confirm')."</button></div> </div>";


						$json['html'] = $html;

						echo json_encode($json);

					}


					public function confirm_remove_tran(){

						$json['dataCollection'] = $dataCollection = $this->Wallet_model->getDeleteData((int)$this->input->post("id",true));

						foreach ($dataCollection as $data) {

							foreach ($data['removed'] as $key => $value) {
								if(isset($value['query']) && $value['query']) $this->db->query($value['query']);
							}

							if(isset($data['details']) && ! empty($data['details'])) {
								$this->load->model('Product_model');
								$this->Product_model->delete_wallet_integration_clicks_action($data['details']);
							}

							if(isset($data['id']) && !empty($data['id'])){
								$this->db->query("DELETE FROM wallet_recursion WHERE transaction_id = ". $data['id']);

								$this->db->query("DELETE FROM wallet_requests WHERE FIND_IN_SET(".$data['id'].",tran_ids)");

									$this->db->query("DELETE FROM wallet WHERE parent_id = ". $data['id']);

									$this->db->query("DELETE FROM wallet WHERE id = ". $data['id']);
								}
							}

							echo json_encode($json);
						}



						public function info_recursion_tran(){

							$mainID = $this->input->post("id",true);

							$dataCollection = $this->Wallet_model->getDeleteData((int)$mainID, true);

							$table = "<div class='transaction-datails-div-hidden' style='display:none;'><table class='table table-stripped'><thead style='width: calc( 100% - 1em )'><tr style='display: table;  width: 100%; table-layout: fixed;'><td class='text-center'>".__('admin.transaction_id')."</td><td class='text-center'>".__('admin.username')."</td><td class='text-center'> ".__('admin.amount')."</td></tr></thead><tbody style=' display: block; max-height: 200px; overflow-y: auto;'>";

							$amountTotal = 0;

							foreach ($dataCollection as $data) {

								$amountTotal += ($data['amount'] > 0) ? $data['amount'] : 0; 

								$table .= '<tr style="display: table; width: 100%; table-layout: fixed;"><td class="text-center">'. $data['id'] .'</td><td class="text-center">'. $data['name'] .'</td><td class="text-center">'. c_format($data['amount']) .'</td></tr>';
							}

							$table .= "</tbody></table></div>";

							$html .= "<p><strong>".__('admin.recursion_setting_for')." ".count($dataCollection)."</strong> ".__('admin.transactions_having_total_amount')." <strong>".c_format($amountTotal)."</strong> ".__('admin.wil_be_updated')." <a href='javascript:void(0)' class='show-trans-aff-details'><br>".__('admin.see_details')."</a></p>";

							$html .= $table;

							$data['transactions_details'] = $html;

							$wallet_data = $this->Wallet_model->getbyId((int)$mainID);

							$recursion = $this->Wallet_model->GetTransactionRecursion($wallet_data->id);		


							$recursion_type	= array(

								"every_day"   => __("admin.every_day"),

								"every_week"  => __("admin.every_week"),

								"every_month" => __("admin.every_month"),

								"every_year"  => __("admin.every_year"),

								"custom_time" => __("admin.custom_time")

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



						public function confirm_recursion_tran(){
							$data = $this->input->post();

							$mainID = $data['transaction_id'];

							$dataCollection = $this->Wallet_model->getDeleteData((int)$mainID, true);

							$json['recursion_data'] = [];

							foreach($dataCollection as $d) {
								$data['transaction_id'] = $d['id'];
								$json['recursion_data'][$d['id']]  = $this->Wallet_model->addTransactionRecursion($data);
							}

							$data['status'] = $this->Wallet_model->status();

							$data['status_icon'] = $this->Wallet_model->status_icon;

							$data['request_status'] = $this->Wallet_model->request_status;

							$transaction = $this->Wallet_model->getTransaction(['id' => $mainID]);

							$json['table'] = '';

							foreach ($transaction as $key => $value) {

								$data['class'] = 'child-recurring';

								$data['force_class'] = $_POST['ischild'] == 'true' ? 'child-arrow' : '';

								$data['recurring'] = $id;

								$data['value'] = $value;

								$data['wallet_status'] = $data['status'];

								$json['table'] .= $this->load->view("admincontrol/users/part/new_wallet_tr", $data, true);
							}

							echo json_encode($json);

						}

						public function wallet_change_status(){

							$id = (int)$this->input->post("id",true);

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

								$table = "<div class='transaction-datails-div-hidden' style='display:none;'><table class='table table-stripped'><thead style='width: calc( 100% - 1em )'><tr style='display: table;  width: 100%; table-layout: fixed;'><td class='text-center'>".__('admin.transaction_id')."</td><td class='text-center'>".__('admin.username')."</td><td class='text-center'>".__('admin.amount')."</td></tr></thead><tbody style=' display: block; max-height: 200px; overflow-y: auto;'>";

								$amountTotal = 0;

								foreach ($dataCollection as $datas) {

									$amountTotal += ($datas['amount'] > 0) ? $datas['amount'] : 0; 

									$table .= '<tr style="display: table; width: 100%; table-layout: fixed;"><td class="text-center">'. $datas['id'] .'</td><td class="text-center">'. $datas['name'] .'</td><td class="text-center">'. c_format($datas['amount']) .'</td></tr>';
								}

								$table .= "</tbody></table></div>";

								$html .= "<p><strong>".__('admin.status_for')." ".count($dataCollection)."</strong> ".__('admin.transactions_having_total_amount')." <strong>".c_format($amountTotal)."</strong> ".__('admin.wil_be_updated')." <a href='javascript:void(0)' class='show-trans-aff-details'><br>".__('admin.see_details')."</a></p>";

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

						function list_files($path) {

							$files = array();

							$folders = array();

							if (is_dir($path)) {

								if ($handle = opendir($path)) {

									while (($name = readdir($handle)) !== false) {

										if (!preg_match("#^\.#", $name)){

											if (!is_dir($path . "/" . $name)) {

												$ext = pathinfo($name, PATHINFO_EXTENSION);

												if (in_array($ext, array('js','php','css','svg'))) {

													$files[] = realpath($path ."/". $name);

												}

											} else {

												$t = $this->list_files($path . "/" . $name);

												if($t) $folders[$name] = $t;

											}

										}

									}

									closedir($handle);

								}

							}

							$result = array_merge($folders, $files);

							return $result;

						}

						public function front_template(){

							$userdetails = $this->userdetails();

							$post = $this->input->post(null);

							unset($_FILES['files']);

							if(!empty($post) || !empty($_FILES)){

								$commonSetting = array('templates','loginclient');

								if(count($_FILES) > 0){

									$this->load->helper('string');

									$config['allowed_types'] = '*';

									$config['file_name']  = random_string('alnum', 32);

									$this->load->library('upload', $config);

									foreach ($_FILES as $fieldname => $input) {

										list($key,$subkey) = explode("_", $fieldname);

										if($key == 'files' || $key == 'templates'){

											$path = $this->front_assets."img/";

										} else{

											$path = 'assets/images/site';

										}

										$config['upload_path'] = $path;

										$this->upload->initialize($config);

										if($input['error'] == 0){

											$extension = pathinfo($_FILES[$fieldname]["name"], PATHINFO_EXTENSION);

											if($extension=='jpg' || $extension=='jpeg' || $extension=='png' || $extension=='gif'){

												if (!$this->upload->do_upload($fieldname)) {

													echo "<pre>"; print_r($this->upload); echo "</pre>";die; 

												}

												else {

													$upload_details = $this->upload->data();

													$post[$key][$subkey] = $upload_details['file_name'];

												}

											} else{

												$json['errors']["{$key}_{$subkey}"] = 'Only Image File are allowed';

											}

										}

									}

								}

								foreach ($post as $key => $value) {

									if (in_array($key, $commonSetting)) {

										$this->Setting_model->save($key, $value);
									}
								}

								$this->session->set_flashdata('success', __('admin.setting_saved_successfully'));

								redirect('admincontrol/front_template');

							}

							$data['template_file'] = $this->list_files(APPPATH . 'views/auth/user/');

							$data['image_manager_url'] = base_url('/admincontrol/load_image_manager');

							$data['templates'] = $this->Product_model->getSettings('templates');

							$data['loginclient'] = $this->Product_model->getSettings('loginclient');

							$data['templates_url'] = $this->front_assets_url ."img/";

							$this->view($data,'template_editor/editor');

						}

						public function load_image_manager(){

							$filter_name = '';

							$rootDir = $this->front_assets ."img";

							$rootDirUrl = $this->front_assets_url ."img";

							$get = $this->input->get(null,true);

							if (isset($get['directory'])) {

								$directory = rtrim($rootDir . str_replace(array('../', '..\\', '..'), '', $get['directory']), '/');

							} else { $directory = $rootDir; }



							$data['images'] = array();

							$directories = glob($directory . '/' . $filter_name . '*', GLOB_ONLYDIR);

							if (!$directories) { $directories = array(); }

							if (isset($get['target'])) {

								$data['target'] = $get['target'];

							} else { $data['target'] = ''; }

							if (isset($get['thumb'])) {

								$data['thumb'] = $get['thumb'];

							} else { $data['thumb'] = ''; }

							if (isset($get['directory'])) {

								$data['directory'] = $get['directory'];

							} else { $data['directory'] = ''; }

							$files = glob($directory . '/' . $filter_name . '*.{jpg,jpeg,png,gif,JPG,JPEG,PNG,GIF}', GLOB_BRACE);

							if (!$files) {

								$files = array();

							}

							$images = array_merge($directories, $files);

							$image_total = count($images);

							$fun_url = base_url('/admincontrol/front_template');

							$data['image_manager_url'] = $image_manager_url = base_url('/admincontrol/load_image_manager');

							foreach ($images as $image) {

								$name = str_split(basename($image), 14);

								if (is_dir($image)) {

									$url = '';

									if (isset($get['target'])) { $url .= '&target=' . $get['target']; }

									if (isset($get['thumb'])) { $url .= '&thumb=' . $get['thumb']; }

									$data['images'][] = array(

										'thumb' => '',

										'name'  => implode(' ', $name),

										'type'  => 'directory',

										'path'  => substr($image, strlen($rootDir)),

										'href'  => $image_manager_url.'?directory=' . urlencode(substr($image, strlen($directory))) . $url,

									);

								} elseif (is_file($image)) {

									$server = '';

									$data['images'][] = array(

										'thumb' => $rootDirUrl . str_replace($rootDir, '', $image),

										'name'  => implode(' ', $name),

										'type'  => 'image',

										'path'  => substr($image, strlen($rootDir)),

										'href'  => $rootDirUrl . $image

									);

								}

							}

							$config['base_url'] = $fun_url;

							$data['fun_url'] = $fun_url;

							$data['image_upload'] = base_url('/admincontrol/image_upload_filemanager');

							$data['folder_url'] = base_url('/admincontrol/folder_filemanager');

							$data['delete_image_url'] = base_url('/admincontrol/delete_image_filemanager');

							$data['entry_folder'] = 'Enter Folder';

							$data['button_folder'] = 'Folder';

							$data['text_confirm'] = 'Sure You want to delete?';

							$url = $image_manager_url;

							$eurl  = '' ;

							if (isset($get['directory'])) { $eurl .= '&directory=' . urlencode(html_entity_decode($get['directory'], ENT_QUOTES, 'UTF-8')); }

							if (isset($get['filter_name'])) { $eurl .= '&filter_name=' . urlencode(html_entity_decode($get['filter_name'], ENT_QUOTES, 'UTF-8')); }

							if (isset($get['target'])) { $eurl .= '&target=' . $get['target']; }

							if (isset($get['thumb'])) { $eurl .= '&thumb=' . $get['thumb']; }

							$data['url'] = $url .'?'. ltrim($eurl,'&'); 

							$url = '';

							if (isset($get['directory'])) {

								$pos = strrpos($get['directory'], '/');

								if ($pos) {

									$url .= '&directory=' . urlencode(substr($get['directory'], 0, $pos));

								}

							}

							if (isset($get['target'])) { $url .= '&target=' . $get['target']; }

							if (isset($get['thumb'])) { $url .= '&thumb=' . $get['thumb']; }

							$data['parent'] = $image_manager_url .'?'. ltrim($url,'&');

							echo $this->load->view('admincontrol/template_editor/editor_image', $data);

						}	


						public function image_upload_filemanager(){

							$json = array();

							$DIR_IMAGE = $this->front_assets ."img";;

							if (isset($get['directory'])) {

								$directory = rtrim($DIR_IMAGE . str_replace(array('../', '..\\', '..'), '', $get['directory']), '/');

							} else {

								$directory = $DIR_IMAGE ;

							}

							if (!is_dir($directory)) {

								$json['error'] = "Directory Not Found" ;

							}

							if (!$json) {

								if (!empty($_FILES['file']['name']) && is_file($_FILES['file']['tmp_name'])) {

									$filename = basename(html_entity_decode($_FILES['file']['name'], ENT_QUOTES, 'UTF-8'));

									if ((strlen($filename) < 3) || (strlen($filename) > 255)) {

										$json['error'] = "File Name not valid";

									}

									$allowed = array('jpg','jpeg','gif','png');

									if (!in_array(strtolower(substr(strrchr($filename, '.'), 1)), $allowed)) {

										$json['error'] = "File type Invalid";

									}

									$allowed = array('image/jpeg','image/pjpeg','image/png','image/x-png','image/gif');

									if (!in_array($_FILES['file']['type'], $allowed)) {

										$json['error'] = "File type Invalid";

									}

									if ($_FILES['file']['error'] != UPLOAD_ERR_OK) {

										$json['error'] = 'Upload Error ' . $_FILE['file']['error'];
									}

								} else {

									$json['error'] = "Upload File Fail";

								}

							}

							if (!$json) {

								move_uploaded_file($_FILES['file']['tmp_name'], $directory . '/' . $filename);

								$json['success'] = 'Upload successfully';

							}

							echo json_encode($json);die;

						}



						public function folder_filemanager(){

							$json = array();

							$DIR_IMAGE = $this->front_assets ."img";

							$post = $this->input->post(null,true);

							$get = $this->input->get(null,true);

							if (isset($get['directory'])) {

								$directory = rtrim($DIR_IMAGE  . str_replace(array('../', '..\\', '..'), '', $get['directory']), '/');

							} else { $directory = $DIR_IMAGE ; }

							if (!is_dir($directory)) { $json['error'] = 'Invalid Directory'; }

							if (!$json) {

								$folder = str_replace(array('../', '..\\', '..'), '', basename(html_entity_decode($post['folder'], ENT_QUOTES, 'UTF-8')));

								if ((strlen($folder) < 3) || (strlen($folder) > 128)) { $json['error'] = "Folder Name must be 3 to 128 characters"; }

								if (is_dir($directory . '/' . $folder)) { $json['error'] = "Folder Already exists"; }

							}

							if (!$json) {

								mkdir($directory . '/' . $folder, 0777);

								chmod($directory . '/' . $folder, 0777);

								$json['success'] = "Directory Create successfully";

							}
							echo json_encode($json);die;
						}



						public function delete_image_filemanager(){

							$json = array();

							$DIR_IMAGE = $this->front_assets ."img";

							$post = $this->input->post(null,true);

							if (isset($post['path'])) {

								$paths = $post['path'];

							} else {

								$paths = array();

							}

							foreach ($paths as $path) {

								$path = rtrim($DIR_IMAGE . str_replace(array('../', '..\\', '..'), '', $path), '/');

								if ($path == $DIR_IMAGE ) {

									$json['error'] = "Some Thing want wrong";

									break;

								}

							}



							if (!$json) {

								foreach ($paths as $path) {

									$path = rtrim($DIR_IMAGE . str_replace(array('../', '..\\', '..'), '', $path), '/');

									if (is_file($path)) { 

										unlink($path);

									} elseif (is_dir($path)) {

										$files = array();

										$path = array($path . '*');



										while (count($path) != 0) {

											$next = array_shift($path);

											foreach (glob($next) as $file) {

												if (is_dir($file)) { $path[] = $file . '/*'; }

												$files[] = $file;

											}

										}

										rsort($files);

										foreach ($files as $file) {

											if (is_file($file)) { unlink($file); } 

											elseif (is_dir($file)) { rmdir($file); }

										}

									}

								}

								$json['success'] = "Successfully Delete";

							}
							echo json_encode($json);die;
						}


						public function editor_get_file(){

							$json = array();

							$path = $this->input->post("path",true);

							if($path && is_file($path)){

								$json['contents'] = file_get_contents($path);



								$json['ext'] = pathinfo($path, PATHINFO_EXTENSION);

							} else {

								$json['erorr'] = "File not found ..!";

							}

							echo json_encode($json);
						}



						public function editor_save_file(){

							$json = array();

							$path = $this->input->post("path",true);

							$post = $this->input->post(null,true);

							if($path && is_file($path)){

								file_put_contents($path,trim($post['text']));

								$json['success'] = "File save successfully";

							} else {

								$json['erorr'] = "File not found ..!";

							}
							echo json_encode($json);
						}



						public function registration_builder()	{
							$userdetails = $this->userdetails();
							if ($this->input->server('REQUEST_METHOD') == 'POST'){
								$post = $this->input->post(null,true);
								$json = array();
								$this->Setting_model->save('registration_builder', $post );
								echo json_encode($json);die;
							}

							$data['builder'] = $this->Product_model->getSettings('registration_builder');
							$fields  = json_decode($data['builder']['registration_builder'],1);
							$default_fields = array('firstname' => 0,'lastname' => 0 ,'email' => 0,'username' => 0,'password' => 0,'confirm_password' => 0);

							foreach ($fields as $key => $value) {
								if($value['type'] == 'header' && !isset($default_fields[strtolower($value['label'])]) ){
									unset($fields[$key]);
								}
							}
							$allfield = array();
							foreach ($fields as $key => $value) {
								$allfield[strtolower($value['label'])] = 1;
							}
							foreach ($default_fields as $value => $key) {
								if (!isset($allfield[$value])) {
									$fields[] = array(
										'type' => 'header',
										'label' => ucfirst($value),
										'placeholder' => ucfirst($value),
										'className' => '',
										'name' => $value,
										'mobile_validation' => false,
									);
								}
							}

							$data['builder']['registration_builder'] = json_encode(array_values($fields));

							$this->view($data,'registration_builder/index');

						}


						public function sendAffiliateEmail(){

						   // Demo Mode
						    if (ENVIRONMENT === 'demo') {
						        echo json_encode([
						            'status' => 'error',
						            'message' => 'Disabled on demo mode'
						        ]);
						        return;
						    }
						    // Demo Mode


							$this->load->library('form_validation');
							$json = array();
							$this->form_validation->set_rules('to', 'To', 'required|trim');
							$this->form_validation->set_rules('subject', 'Subject', 'required|trim');
							$this->form_validation->set_rules('message', 'Message', 'required|trim');

							$attachment=NULL; 
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
									$attachment = base_url().'assets/user_upload/'.$config['file_name'].".".$fileNameArray[sizeof($fileNameArray)-1];
								}
							}  
							
							if ($this->form_validation->run() == FALSE) {

								$json['errors'] = $this->form_validation->error_array();

							} else {

								$emails = explode(",", $this->input->post("to",true));

								$this->load->model('Mail_model');

								$post = $this->input->post(null,true);

								foreach ($emails as $key => $email) {

								$this->Mail_model->affiliate_mail($email, $post,$attachment);
								}

								$json['success'] = count($emails). " mails sent successfully..!";

							}
							echo json_encode($json);
						}



						public function theme_setting(){

							$userdetails = $this->userdetails();

							$post = $this->input->post(null,true);

							if(!empty($post)){

								$commonSetting = array('adminside','affiliateside');

								foreach ($post as $key => $value) {

									if (in_array($key, $commonSetting)) {

										$this->Setting_model->save($key, $value);
									}
								}

								$this->session->set_flashdata('success', __('admin.setting_saved_successfully'));

								redirect('admincontrol/theme_setting');
							}

							$data['theme_setting']['adminside'] = $this->Product_model->getSettings('adminside');

							$data['theme_setting']['affiliateside'] = $this->Product_model->getSettings('affiliateside');

							$data['setting_tabs'] = array(

								'adminside'		=> __('admin.admin_side'),

								'affiliateside'	=> __('admin.affiliate_side'),

							);

							$this->view($data,'setting/themesetting');
						}



						public function getDatesFromType(){

							$userdetails = $this->userdetails();

							$data = array();

							$type = $this->input->post('type',true);



							if($type == 'month'){

								$data = array('All','01','02','03','04','05','06','07','08','09','10','11','12');

							}else{

								$data = array('All',date("Y",strtotime("-3 year")),date("Y",strtotime("-2 year")),date("Y",strtotime("-1 year")),date("Y",strtotime("0 year")));
							}

							echo json_encode($data);die;
						}



						public function get_integartion_data($return  = false){

							$userdetails = $this->userdetails();

							$post = $this->input->post();

							$json = array();

							if($post['integration_data_year'] && $post['integration_data_month']){

								$integration_filters = array(

									'integration_data_year' => $post['integration_data_year'],

									'integration_data_month' => $post['integration_data_month'],
								);

							}else{

								$integration_filters = array();

							}

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

									'. __( 'admin.total_balance' ) .'

									<span class="badge bg-primary badge-pill font-14 pull-right">

									'. c_format($totals['integration']['balance']) .'        

									</span>

									</li>

									<li class="list-group-item">

									'. __( 'admin.total_sales' ) .'

									<span class="badge bg-primary badge-pill font-14 pull-right">

									'. c_format($totals['integration']['balance']) .' / '. c_format($totals['integration']['sale']) .'        

									</span>

									</li>

									<li class="list-group-item">

									'. __( 'admin.total_clicks' ) .'

									<span class="badge bg-primary badge-pill font-14 pull-right">

									'. (int)$totals['integration']['click_count'] .' / '. c_format($totals['integration']['click_amount']) .'

									</span>

									</li>

									<li class="list-group-item">

									'. __('admin.total_actions') .'

									<span class="badge bg-primary badge-pill font-14 pull-right">

									'. (int)$totals['integration']['action_count'] .' / '. c_format($totals['integration']['action_amount']) .'

									</span>

									</li>

									<li class="list-group-item">

									'. __( 'admin.total_commission' ) .'

									<span class="badge bg-primary badge-pill font-14 pull-right">

									'. c_format($totals['integration']['total_commission']) .' 

									</span>

									</li>

									<li class="list-group-item">

									'. __( 'admin.total_orders' ) .'

									<span class="badge bg-primary badge-pill font-14 pull-right">

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

										'. __( 'admin.total_balance' ) .'

										<span class="badge bg-primary badge-pill font-14 pull-right">

										'. c_format($value['balance']) .'

										</span>

										</li>

										<li class="list-group-item">

										'. __( 'admin.total_sales' ) .'

										<span class="badge bg-primary badge-pill font-14 pull-right">

										'. c_format($value['balance']) .' / '. c_format($value['sale']) .'        

										</span>

										</li>

										<li class="list-group-item">

										'. __( 'admin.total_clicks' ) .'

										<span class="badge bg-primary badge-pill font-14 pull-right">

										'. (int)$value['click_count'] .' / '. c_format($value['click_amount']) .'

										</span>

										</li>

										<li class="list-group-item">

										'. __('admin.total_actions') .'

										<span class="badge bg-primary badge-pill font-14 pull-right">

										'. (int)$value['action_count'] .' / '. c_format($value['action_amount']) .'

										</span>

										</li>

										<li class="list-group-item">

										'. __( 'admin.total_commission' ) .'

										<span class="badge bg-primary badge-pill font-14 pull-right">

										'. c_format($value['click_amount'] + $value['sale'] + $value['action_amount']) .' 

										</span>

										</li>

										<li class="list-group-item">

										'. __( 'admin.total_orders' ) .'

										<span class="badge bg-primary badge-pill font-14 pull-right">

										'. (int)$value['total_orders'] .' 

										</span>

										</li>

										<li class="list-group-item">

										<a class="btn btn-lg btn-default btn-primary" href="http://'. $website .'" target="_blank">'. __( 'admin.preview_store' ) .'</a>

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



						public function category_auto(){

							$userdetails = $this->userdetails();

							$keyword = $this->input->get('query');

							$data = $this->db->query("SELECT id as value,name as label FROM categories WHERE name  like ". $this->db->escape("%".$keyword."%") ." ")->result_array();

							echo json_encode($data);die;

						}



						public function store_category_delete($category_id = 0){

							$userdetails = $this->userdetails();

							if($category_id > 0){

								$data['category'] = $this->db->query("DELETE FROM categories WHERE id = ". (int)$category_id);

							}



							$this->session->set_flashdata('success',__('admin.category_deleted_successfully'));

							redirect(base_url('admincontrol/store_category'));

						}



						public function store_category_add($category_id = 0){

							$userdetails = $this->userdetails();

							if ($this->input->server('REQUEST_METHOD') == 'POST'){

								$this->load->library('form_validation');

								$this->form_validation->set_rules('name', 'Category Name', 'required');

							$this->form_validation->set_rules('description', 'Category Description', 'required' );

								if($this->form_validation->run()){

									$details = array(

										'name'        =>  $this->input->post('name',true),

										'description' =>  $this->input->post('description',false),

										'parent_id'   =>  $this->input->post('parent_id',true),

										'color'   	  =>  $this->input->post('color',true),

										'tag'   	  =>  $this->input->post('tag',true),

									);



									$ext = pathinfo($_FILES['category_image']['name'], PATHINFO_EXTENSION);

									if($_FILES['category_image']['error'] != 0 && $category_id == 0 ){

										$errors['category_image'] = 'Select Featured Image File!';

									} else if( !in_array($ext, ['jpg','png','jpeg']) && $category_id == 0){

										$errors['category_image'] = 'Only image file are allowed';

									} else if(!empty($_FILES['category_image']['name'])){

										$upload_response = $this->upload_photo('category_image','assets/images/product/upload/thumb');

										if($upload_response['success']){

											$details['image'] = $upload_response['upload_data']['file_name'];

										}else{

											$errors['category_image'] = $upload_response['msg'];

										}

									}



									$ext = pathinfo($_FILES['category_background_image']['name'], PATHINFO_EXTENSION);

									if($_FILES['category_background_image']['error'] != 0 && $category_id == 0 ){

										$errors['category_background_image'] = 'Select Featured Image File!';

									} else if( !in_array($ext, ['jpg','png','jpeg']) && $category_id == 0){

										$errors['category_background_image'] = 'Only image file are allowed';

									} else if(!empty($_FILES['category_background_image']['name'])){

										$upload_response = $this->upload_photo('category_background_image','assets/images/product/upload/thumb');

										if($upload_response['success']){

											$details['background_image'] = $upload_response['upload_data']['file_name'];

										}else{

											$errors['category_background_image'] = $upload_response['msg'];

										}

									}





									if(empty($errors)){

										if($category_id){

											$this->Product_model->update_data('categories', $details, array('id' => $category_id));

										}else{

											$details['created_at'] = date('Y-m-d H:i:s');

											$category_id = $this->Product_model->create_data('categories', $details);

										}



										$slug = $this->friendly_seo_string($this->input->post('name',true).'-'.$category_id);

										$this->db->query("UPDATE categories SET slug = ". $this->db->escape($slug) ." WHERE id =". $category_id);



										$this->session->set_flashdata('success', 'Category Saved Successfully');

										$json['location'] = base_url('admincontrol/store_category');



									} else {

										$json['errors'] = $errors;

									}

								} else {

									$json['errors'] = $this->form_validation->error_array();

								}



								echo json_encode($json);die;

							}



							$data['category'] = array();

							if($category_id > 0){

								$data['category'] = $this->db->query("SELECT * FROM categories WHERE id = ". (int)$category_id)->row_array();

							}



							$data['categories'] = $this->db->query("SELECT id,name,parent_id FROM categories")->result_array();


							$this->view($data,'store/category_add');

						}



						public function store_category($page = 1){

							$userdetails = $this->userdetails();

							if ($this->input->server('REQUEST_METHOD') == 'POST'){

								$page = max((int)$page,1);

								$filter = array(
									'limit' => 100,
									'page' => $page,
								);
								
								$currentTheme = User::getActiveTheme();

								list($data['categories'],$total) = $this->Product_model->getCategory($filter,$currentTheme);

								$data['start_from'] = (($page-1) * $filter['limit'])+1;

								$json['html'] = $this->load->view("admincontrol/store/category_list",$data,true);

								$this->load->library('pagination');

								$config['base_url'] = base_url('admincontrol/store_category/');

								$config['per_page'] = $filter['limit'];

								$config['total_rows'] = $total;

								$config['use_page_numbers'] = TRUE;

								$config['enable_query_strings'] = TRUE;

								$this->pagination->initialize($config);

								$json['pagination'] = $this->pagination->create_links();

								echo json_encode($json);die;
							}

							$this->view($data,'store/store_category');
						}

						public function get_orders_transactions($orderType, $orderId, $type = '') {
							$userdetails = $this->userdetails();

							if(!$this->userdetails()){ die('unauthorised request'); }

							$filter['getSingleOrder'] = $orderType;
							$filter['order_id'] = $orderId;
							list($data['orders'],$total) = $this->Order_model->getAllOrders($filter);

							if($data['orders'][0]['wallet_transactions'])
								$filter = array(
									'id_in' => $data['orders'][0]['wallet_transactions'],
								);
							else
								$filter = array();

							$this->load->model('Withdrawal_payment_model');

							$data['orderType'] = $orderType;
							$data['orderId'] = $orderId;
							$data['transaction'] = $this->Wallet_model->getTransaction($filter);
							$data['is_dashboard'] = '0';
							$data['is_order_page'] = '0';

							if ($type == 'dashboard') {
								$data['is_dashboard'] = '1';
							}elseif ($type == 'order_page') {
								$data['is_order_page'] = '1';
							}

							$html = $this->load->view("admincontrol/store/wallet_detail_tr",$data,true);

							echo $html;die;
						}

						public function store_orders($page = 1){

							$userdetails = $this->userdetails();

							$data['status'] = $this->Order_model->status();

							$data['wallet_status'] = $this->Wallet_model->status();

							if ($this->input->server('REQUEST_METHOD') == 'POST'){

								$post = $this->input->post(null,true);

								$page = max((int)$page,1);

								$filter = array(
									'limit' => 25,
									'page' => $page,
								);

								if(isset($post['filter_status']) && $post['filter_status'] != ''){
									$filter['o_status'] = $this->input->post('filter_status',true);
								}

								list($data['orders'],$total) = $this->Order_model->getAllOrders($filter);

								$data['start_from'] = (($page-1) * $filter['limit'])+1;

								if(isset($post['action']) && $post['action'] == 'dashboard'){
									$json['html'] = $this->load->view("admincontrol/store/dashboard_order_list",$data,true);
								}elseif (isset($post['action']) && $post['action'] == 'order_page') {
									$json['html'] = $this->load->view("admincontrol/store/dashboard_order_list",$data,true);
								}else{
									$json['html'] = $this->load->view("admincontrol/store/order_list",$data,true);
								}

								$this->load->library('pagination');

								$config['base_url'] = base_url('admincontrol/store_orders/');

								$config['per_page'] = $filter['limit'];

								$config['total_rows'] = $total;

								$config['use_page_numbers'] = TRUE;

								$config['enable_query_strings'] = TRUE;

								$this->pagination->initialize($config);

								$json['pagination'] = $this->pagination->create_links();

								clear_tmp_cache('order_cache');

								echo json_encode($json);die;

							}

							$this->view($data, 'store/orders');
						}

						public function get_latest_dashboard_orders($page = 1){

						    $userdetails = $this->userdetails();

						    $data['status'] = $this->Order_model->status();
						    $data['wallet_status'] = $this->Wallet_model->status();

						    if ($this->input->server('REQUEST_METHOD') == 'POST'){

						        $post = $this->input->post(null,true);

						        $page = max((int)$page, 1);

						        $filter = array(
						            'limit' => 5,
						            'page' => $page,
						        );

						        list($data['orders'], $total) = $this->Order_model->getAllOrdersForDashboard($filter);

						        $totalPages = ceil($total / $filter['limit']);

						        $json['html'] = $this->load->view("admincontrol/store/order_list-dashboard", $data, true);

						        $json['total_pages'] = $totalPages;

						        echo json_encode($json);
						        die;
						    }
						}

						public function store_logs($page = 0){

							$userdetails = $this->userdetails();

							if ($this->input->server('REQUEST_METHOD') == 'POST'){

								$page = max((int)$page,1);


								$filter = array(

									'limit'   => 100,

									'page'    => $page,

								);

								$data['userdetails'] = $userdetails;

								list($data['clicks'],$total) = $this->Order_model->getAllClickLogs($filter);

								$data['start_from'] = (($page-1) * $filter['limit'])+1;

								$json['html'] = $this->load->view("admincontrol/store/log_list.php",$data,true);

								$this->load->library('pagination');

								$config['base_url'] = base_url('admincontrol/store_logs/');

								$config['per_page'] = $filter['limit'];

								$config['total_rows'] = $total;

								$config['use_page_numbers'] = TRUE;

								$config['enable_query_strings'] = TRUE;

								$this->pagination->initialize($config);

								$json['pagination'] = $this->pagination->create_links();

								echo json_encode($json);die;
							}
							$this->view($data,'store/logs');
						}


						public function store_markettools($page = 0){
							set_default_currency();
							$userdetails = $this->userdetails();
							$this->load->model('Form_model');
							$this->load->model('Report_model');
							$this->load->model('Wallet_model');
							$this->load->model('IntegrationModel');

							$data['form_default_commission'] = $this->Product_model->getSettings('formsetting');

							$data['default_commition']       = $this->Product_model->getSettings('productsetting');

							$data['tools'] = $this->IntegrationModel->getProgramTools([

								'status'           => 1,

								'redirectLocation' => 1,

								'restrict'         => $userdetails['id'],

							]);

							$products = $this->Product_model->getAllProduct($userdetails['id'], $userdetails['type']);

							$forms = $this->Form_model->getForms($userdetails['id']);

							foreach ($products as $key => $value) { $products[$key]['is_product'] = 1; }

							foreach ($forms as $key => $value) {

								$forms[$key]['coupon_name']          = $this->Form_model->getFormCouponname(($value['coupon']) ? $value['coupon'] : 0);

								$forms[$key]['public_page']          = base_url('form/'.$value['seo'].'/'.base64_encode($this->userdetails()['id']));

								$forms[$key]['count_coupon']         = $this->Form_model->getFormCouponCount($value['form_id'],$this->userdetails()['id']);

								$forms[$key]['seo']                  = str_replace('_', ' ', $value['seo']);

								$forms[$key]['is_form']              = 1;

								$forms[$key]['product_created_date'] = $value['created_at'];

								$forms[$key]['fevi_icon'] = $value['fevi_icon'] ? 'assets/images/form/favi/'.$value['fevi_icon'] : 'assets/images/users/no-image.jpg';


								if($value['coupon']){
									$forms[$key]['coupon_code'] = $this->Form_model->getFormCouponCode($value['coupon']);
								}
							}

							$data_list = array_merge($products,$forms,$data['tools']);

							usort($data_list,function($a,$b){
								$ad = strtotime($a['product_created_date']);
								$bd = strtotime($b['product_created_date']);
								return ($ad-$bd);
							});
							$data_list = array_reverse($data_list);
							$total = count( $data_list );
							$limit = 20; 
							$totalPages = ceil( $total/ $limit );
							$offset = $page;
							if( $offset < 0 ) $offset = 0;

							$data['data_list'] = array_slice( $data_list, $offset, $limit );

							$this->load->library('pagination');

							$config['base_url'] = base_url('/admincontrol/store_markettools/');

							$config['total_rows'] = $total;

							$config['per_page'] = $limit;

							$config['attributes'] = array('class' => 'single_paginate_link');

							$filter['per_page'] = $config['per_page'];

							$config['reuse_query_string'] = TRUE;

							$config['query_string_segment'] = 'page';

							$this->pagination->initialize($config);

							$data['pagination_link'] = $this->pagination->create_links();

							$this->load->library("socialshare");				
							$data['social_share_modal'] =  $this->socialshare->get_dynamic_social_share_btns();

							$this->view($data,'store/markettools');
						}



						public function info_remove_order(){

							$id = (int)$this->input->post("id",true);

							$type = $this->input->post("type",true);

							if($type == 'ex'){

								$order_amount = $this->db->query("SELECT total FROM integration_orders WHERE id= ".(int)$id)->row();

								$total_comm = $this->db->query("SELECT SUM(amount) as total FROM  wallet WHERE comm_from='ex' AND type IN('sale_commission','admin_sale_commission','refer_sale_commission') AND reference_id_2 = {$id}")->row();

							}

							else{

								$order_amount = $this->db->query("SELECT total FROM `order` WHERE id= ".(int)$id)->row();

								$total_comm = $this->db->query("SELECT SUM(amount) as total FROM  wallet WHERE comm_from='store' AND type IN('sale_commission','vendor_sale_commission') AND reference_id = {$id}")->row();

							}

							$html = '<h6 class="text-center"> Amount : '. c_format($order_amount->total) .' </h6>';

							$html .= '<h6 class="text-center"> Commission Amount : '. c_format($total_comm->total) .' </h6><hr>';

							$html .= '<p class="text-center"> Order ID : '. $this->input->post("id",true) .' </p>';

							$html .= '<p class="text-center"> <input type="hidden" value="'. $type .'" name="order_type"> <label>

							<input type="checkbox" name="sale_commission" class="wallet-checkbox">

							Sale Commission

							</label></p>';

							$html .= "<br><div class='row'> <div class='col-sm-6'><button data-dismiss='modal' class='btn btn-primary btn-block'>Cancel</button></div> <div class='col-sm-6'><button class='btn btn-danger  btn-block' delete-order-confirm='". $this->input->post("id",true) ."'>Yes Confirm</button></div> </div>";



							$json['html'] = $html;

							echo json_encode($json);
						}



				public function confirm_remove_order(){

					$id = $this->input->post('id',true);

					$order_type = $this->input->post('order_type',true);

					$sale_commission = $this->input->post('sale_commission',true);



					if($order_type == 'ex'){

						$this->db->query("DELETE FROM `integration_orders` WHERE id = {$id}");


						$wallet_trans = $this->db->query('SELECT id FROM wallet WHERE type LIKE "%sale%" AND comm_from="ex" AND reference_id_2='.$id)->result_array();

					} else{

						$this->db->query("DELETE FROM `order` WHERE id = {$id}");

						$this->db->query("DELETE FROM `order_products` WHERE order_id = {$id}");

						$this->db->query("DELETE FROM `order_proof` WHERE order_id = {$id}");

						$this->db->query("DELETE FROM `orders_history` WHERE order_id = {$id}");

						$wallet_trans = $this->db->query('SELECT id FROM wallet WHERE comm_from="store" AND type LIKE "%sale%" AND reference_id ='.$id)->result_array();
					}

					if($sale_commission == 'true' && count($wallet_trans) > 0){
						$trans = "";

						foreach ($wallet_trans as $wa) {
							$trans  .= (empty($trans)) ? $wa['id'] : ",".$wa['id'];

							$walletRequest = $this->db->query('SELECT * FROM wallet_requests WHERE find_in_set('.$wa['id'].', tran_ids)')->row_array();

								if(!empty($walletRequest)) {
									$this->db->query('UPDATE wallet SET status=1 WHERE id IN ('.$walletRequest['tran_ids'].')');
								}

								$this->db->query('DELETE FROM wallet_requests WHERE find_in_set('.$wa['id'].', tran_ids)');
							}


							$this->db->query('DELETE FROM wallet_recursion WHERE transaction_id IN ('.$trans.')');

								$this->db->query('DELETE FROM wallet WHERE id IN ('.$trans.')');
							}


							$json['success'] = true;

							echo json_encode($json);

						}

				public function calc_commission(){

					$data = $this->input->post(null,true);

					$setting = array(

						'product_id'                      => $data['product_id'],

						'product_price'                   => $data['product_price'],

						'admin_click_commission_type'     => $data['admin_click_commission_type'],

						'admin_click_count'               => $data['admin_click_count'],

						'admin_click_amount'              => $data['admin_click_amount'],

						'admin_sale_commission_type'      => $data['admin_sale_commission_type'],

						'admin_commission_value'          => $data['admin_commission_value'],

					    'affiliate_click_commission_type' => $data['affiliate_click_commission_type'],

						'affiliate_click_count'           => $data['affiliate_click_count'],

						'affiliate_click_amount'          => $data['affiliate_click_amount'],

						'affiliate_sale_commission_type'  => $data['affiliate_sale_commission_type'],

						'affiliate_commission_value'      => $data['affiliate_commission_value'], 

					);

						$json['commission'] = $this->Product_model->calcVendorCommission($setting);

						$json['success'] = true;

						echo json_encode($json);

						}


					public function withdrawal_payment_gateways_doc(){
						set_default_currency();

						$data = [];

						$this->view($data,'withdrawal_payment/doc');

					}



					public function withdrawal_payment_gateways(){

						set_default_currency();

						$userdetails = $this->userdetails();

						$this->load->model('Withdrawal_payment_model');

						$data['payment_methods'] = $this->Withdrawal_payment_model->getPaymentMethods();

						$this->view($data,'withdrawal_payment/index');

					}



					public function withdrawal_payment_gateways_status_change($code){
						// Demo Mode
						if (ENVIRONMENT === 'demo') {
							$this->session->set_flashdata('error', __('admin.demo_mode'));
							redirect('admincontrol/withdrawal_payment_gateways');
							return;
						}
						// Demo Mode

						set_default_currency();

						$userdetails = $this->userdetails();

						$this->load->model('Withdrawal_payment_model');

						$this->Withdrawal_payment_model->changeInstallUninstall($code);

						redirect(base_url('admincontrol/withdrawal_payment_gateways'));

					}


					public function withdrawal_payment_gateways_edit($code = null) {

						// Demo Mode
						if (ENVIRONMENT === 'demo') {
							$this->session->set_flashdata('error', __('admin.demo_mode'));
							redirect('admincontrol/withdrawal_payment_gateways');
							return;
						}
						// Demo Mode

					    if ($code === null) {
					        redirect('admincontrol/withdrawal_payment_gateways', 'refresh');
					        return;
					    }

					    set_default_currency();

					    $userdetails = $this->userdetails();

					    $this->load->model('Withdrawal_payment_model');

					    $data['details'] = $this->Withdrawal_payment_model->getDetails($code);

					    if (!$data['details']) {
					        redirect('admincontrol/withdrawal_payment_gateways', 'refresh');
					    }

					    list($html, $setting) = $this->Withdrawal_payment_model->getEditPage($code);

					    $data['html'] = $html;
					    $data = array_merge($data, $setting);

					    $customSetting = $this->Product_model->getSettings('withdrawalpayment_' . $code);

					    if (!empty($customSetting)) {
					        $data['setting_exist_status'] = 1;
					        $data['get_custom_fiels'] = $customSetting;
					    } else {
					        $data['setting_exist_status'] = 0;
					        $data['get_custom_fiels'] = array();
					    }

					    $this->view($data, 'withdrawal_payment/withdrawal_payment_settings');
					}




					public function withdrawal_payment_gateways_setting_save($code){

						$post = $this->input->post(null,true);
						$this->Setting_model->save('withdrawalpayment_'.$code, $post);

						$json['redirect'] = base_url('admincontrol/withdrawal_payment_gateways');

						$this->session->set_flashdata('success',__('admin.settings_saved_successfully'));

						echo json_encode($json);

					}

					public function withdrawal_payment_gateways_setting_save_ajax(){

						// Demo Mode
						if (ENVIRONMENT === 'demo') {
							echo json_encode([
								'status' => 'error',
								'message' => 'Disabled on demo mode'
							]);
							return;
						}
						// Demo Mode

						$json=array();

						$post = $this->input->post(null,true);

						if(isset($post) && $post['code']!='')
						{
							$code=$post['code'];
							$data['status']=$post['status'];
							$this->Setting_model->save('withdrawalpayment_'.$code, $data);
							$json['status'] = 'true';
							$json['msg'] = __('admin.settings_saved_successfully');
						}
						else
						{
							$json['status']='false';
							$json['msg'] = __('admin.settings_save_failed');
						}
						echo json_encode($json);
					}
 


					public function contactus($id=null)
					{
						$data  = array();
						$where = array('notification_type'=>'contact_us','notification_id'=>$id);
						$data['notification_details'] = $this->Common_model->select_where_result('notification', $where);

						$this->view($data,'conatctus/conatctus_details');
					}



					public function orders_notifications($id=null)
					{
						$userdetails = $this->userdetails();

						if(empty($userdetails) ){ redirect($this->admin_domain_url); }
						if(isset($id) && $id>0)
						{
							$data  = array();
							$where = array('notification_type'=>'integration_orders','notification_id'=>$id);
							 
							$notification = $this->Common_model->select_where_result('notification', $where);
							if(isset($notification) && is_array($notification) && count($notification)>0)
							{
								$order_id= $notification['notification_actionID'];
								$data['order']= $this->Order_model->getOrderDetails($order_id);
								$data['notification_title'] =$notification['notification_title'];
								$data['notification_details'] =$notification['notification_description'];

								$this->view($data,'notifications/ex_order_details');

							}
							else
								redirect('/admincontrol/notification');
							
						}
						else
							redirect('/admincontrol/notification');
					}

					public function click_notification($id=null)
					{
						$userdetails = $this->userdetails();

						if(empty($userdetails) ){ redirect($this->admin_domain_url); }
						if(isset($id) && $id>0)
						{
							$data  = array();
							$where = array('notification_type'=>'integration_click','notification_id'=>$id);
							 
							$notification = $this->Common_model->select_where_result('notification', $where);
							if(isset($notification) && is_array($notification) && count($notification)>0)
							{
								$click_id= $notification['notification_actionID'];
								$data['order']= $this->Order_model->getClickActionDetails($click_id);
								$data['notification_title'] =$notification['notification_title'];
								$data['notification_details'] =$notification['notification_description'];
			 
								if($data['order']['click_type']=='action') 
									$this->view($data,'notifications/ex_action_details');	
								else
									$this->view($data,'notifications/ex_click_details');

							}
							else
								redirect('/admincontrol/notification');
							
						}
						else
							redirect('/admincontrol/notification');
					}

				
					public function usergroup()
					{
						$userdetails = $this->userdetails();

						$data['groups'] = $this->user->getgrouplist();

						$this->view($data,'usergroup/index');
					}

					public function group_form($id='')
					{
						$userdetails = $this->userdetails();

						if(!empty($id))
						{
							$data['group']=$this->user->getgroupdetails($id);
						}

						$this->view($data,'usergroup/form');
					}

					public function admin_group_form()
					{
						
						$userdetails = $this->userdetails();

						if(empty($userdetails) ){ redirect($this->admin_domain_url); }

						if($userdetails['id'] != 1){ redirect($this->admin_domain_url); }
						
						if ($this->input->server('REQUEST_METHOD') == 'POST'){

							$json = array();

							$id = (int)$this->input->post("group_id",true);

							$this->load->library('form_validation');
							$this->form_validation->set_rules('group_name', __('admin.group_name'), 'required');
							$post = $this->input->post(null,true);

							if($this->form_validation->run()){

								$errors= array();
								$checkgroup = $this->user->checkgroup($this->input->post('group_name',true),$id);

								if(!empty($checkgroup)){ $json['errors']['group_name'] = __('admin.group_already_exists'); }

								$avatar = '';

								if(!empty($_FILES['avatar']['name'])){

									$upload_response = $this->upload_photo('avatar','assets/images/site');

									if($upload_response['success']){

										$avatar = $upload_response['upload_data']['file_name'];
										$oldfile=$this->input->post('oldfile');
										if(!empty($oldfile))
										{
											$path=FCPATH.'/assets/images/site/'.$oldfile;
											if(file_exists($path))
											{
												@unlink($path);
											}
										}
									}
									else{

										$json['errors']['avatar'] = $upload_response['msg'];
									}
								}
								if(!isset($json['errors'])){
									$userArray = array(
										'group_name'=> $this->input->post('group_name',true),
										'group_description'=> $this->input->post('group_description',true) 
									);

									if(!empty($avatar))
									{
										$userArray['avatar'] = $avatar;
									}
									
									if(empty($id)){
										$userArray['created_at'] = date("Y-m-d H:i:s");
										$data = $this->user->groupinsert($userArray);
										$id = $this->db->insert_id();

									} else {
										$userArray['updated_at'] = date("Y-m-d H:i:s");
										$data = $this->user->update_group($id, $userArray);
									}
									$this->session->set_flashdata('success', __('admin.group_updated_successfully'));

									$json['location'] = base_url('admincontrol/usergroup');
								}
							} else{

								$json['errors'] = $this->form_validation->error_array();
							}
							echo json_encode($json);die;
						}
					}

				public function group_status_toggle()
					{
						try {
							$userdetails = $this->userdetails();
							$json = array();
							$column = $this->input->post("column",true);
							$id = (int)$this->input->post("id",true);
							$status = (int)$this->input->post('status',true);
							if($column == 'is_default'){
								$this->db->query("UPDATE user_groups SET is_default = 0");
								$this->db->query("UPDATE user_groups SET is_default = ".$status." WHERE id =". $id);
							} else {
								$this->db->query("UPDATE user_groups SET ".$column."='".$status."' WHERE id =".$id);
							}
							$json = array('status'=>true,'languages'=>'Is default status updated!');
						} catch (\Throwable $th) {
							$json = array('status'=>false,'message'=>$th->getMessage());
						}
						echo json_encode($json);
					}
				
				public function delete_user_group() {
					$id = $this->input->post('id');
					
					$this->db->select('id');
					$this->db->from('users');
					$this->db->like('groups',$id,'both');
					$query = $this->db->get();
					$row = $query->row_array();
					
					if(empty($row)) {
						$row = $this->db->get_where('user_groups',['id'=>$id])->row_array();
						if(!empty($row['avatar']))
						{
							$path=FCPATH.'/assets/images/site/'.$row['avatar'];
							if(file_exists($path))
							{
								@unlink($path);
							}
						}
						$this->db->delete('user_groups',['id'=>$id]);
						echo json_encode(array('status'=>1,'message'=>'Group deleted successfully!'));
						die;
					} else {
						echo json_encode(array('status'=>0,'message'=>'Group is already assigned to one or more users!'));
						die;
					}
				}

				public function doLoginAff() {
					if(!$this->userdetails()){ die('Unauthorized Access!'); } else {
						$id = $this->input->post('id');
						$user_details_array = $this->db->query('SELECT * from users WHERE id='.$id)->row_array();
						$this->session->set_userdata(array('user'=>$user_details_array));
						echo 'success';
					}
				}

				public function vendor_deposits() {
					$userdetails = $this->userdetails();

					$market_vendor_marketvendorstatus = $this->Product_model->getSettings('market_vendor', 'marketvendorstatus');
					$vendor_storestatus = $this->Product_model->getSettings('vendor', 'storestatus');
					$market_vendor_marketvendorstatus =  isset($market_vendor_marketvendorstatus['marketvendorstatus']) ? $market_vendor_marketvendorstatus['marketvendorstatus'] : 0;
					$vendor_storestatus =  isset($vendor_storestatus['storestatus']) ? $vendor_storestatus['storestatus'] : 0;

					$data['saas_status'] = ($market_vendor_marketvendorstatus == 1 || $vendor_storestatus == 1) ? 1 : 0;
					if($data['saas_status']){
						$get = $this->input->get(null,true);

						$post = $this->input->post(null,true);

						if (isset($post['get_deposit'])) {

							$get = $this->input->post(null,true);

							$filter = array();

							if (isset($get['user_id']) && $get['user_id'] > 0) {

								$filter['user_id'] = (int)$get['user_id'];

								$data['user_id'] = $filter['user_id'];

							}


							if (isset($get['date'])) {

								$filter['date'] = $get['date'];

								$data['date'] = $filter['date'];

							}

							$this->load->model('Deposit_payment_model');

							$data['lists'] = $this->Deposit_payment_model->getDeposits($filter);

							$json['html'] = $this->load->view("admincontrol/users/part/tr_vendor_deposit",$data,true);

							echo json_encode($json);die;
						}

						if(isset($post['delete_request'])){
							$json['type'] = 'warning';
							$json['title'] = __('admin.error');
							$json['message'] = __('admin.vendor_deposit_not_delete');

							$post = $this->input->post(null,true);

							$this->load->model('Deposit_payment_model');
							$success = $this->Deposit_payment_model->deleteDeposit($post['id']);
							
							if($success){
								$json['type'] = 'success';
								$json['title'] = __('admin.success');
								$json['message'] = __('admin.vendor_deposit_deleted');
							}

							echo json_encode($json);die;
						}

						$data['user'] = $userdetails;

						$data['users'] = $this->db->query("SELECT id,username FROM users WHERE type = 'user' AND is_vendor=1")->result_array();

						$data['total_deposited'] = $this->db->query("SELECT SUM(vd_amount) as total FROM vendor_deposit WHERE vd_status=1")->row()->total;
					}
					

					$this->view($data,'users/deposit');
				}

				public function vendor_deposit_details($id){

					$userdetails = $this->userdetails();

					$market_vendor_marketvendorstatus = $this->Product_model->getSettings('market_vendor', 'marketvendorstatus');
					$vendor_storestatus = $this->Product_model->getSettings('vendor', 'storestatus');
					$market_vendor_marketvendorstatus =  isset($market_vendor_marketvendorstatus['marketvendorstatus']) ? $market_vendor_marketvendorstatus['marketvendorstatus'] : 0;
					$vendor_storestatus =  isset($vendor_storestatus['storestatus']) ? $vendor_storestatus['storestatus'] : 0;

					$data['saas_status'] = ($market_vendor_marketvendorstatus == 1 || $vendor_storestatus == 1) ? 1 : 0;
					if($data['saas_status']){
						$get = $this->input->get(null,true);

						$post = $this->input->post(null,true);

						$id=(int)$id;

						if (isset($post['status'])) {

							$this->form_validation->set_rules('status', 'Status', 'required|trim');

							$this->form_validation->set_rules('comment', 'Comment', 'required|trim');

							if ($this->form_validation->run() == FALSE) {

								$data['errors'] = $this->form_validation->error_array();

							} else {

								$this->load->model('Deposit_payment_model');

								$this->Deposit_payment_model->apiAddVendorDepositHistory($id,[

									'status_id' => (int)$post['status'],

									'comment' => $post['comment'],

									'transaction_id' => '',

								]);

								$data['success'] = 1;

								$update1['vd_status'] = (int)$post['status'];
								$this->Product_model->update_data( 'vendor_deposit', $update1, array('vd_id' => $id));

								$this->load->model('Mail_model');
								$deposit = $this->db->query('SELECT * FROM vendor_deposit WHERE vd_id='.$id)->row();
								$this->Mail_model->send_vendor_deposit_mail($deposit, 'added');
							}

							echo json_encode($data);die;

						}


						$this->load->model('Deposit_payment_model');

						$data['request'] = $this->Deposit_payment_model->getDeposits(['vd_id'=>$id]);

						if(!$data['request']){
							show_404();
						}

						$data['status_list'] = $this->Deposit_payment_model->status_list;
					}

					$this->view($data,'users/vendor_deposit_details');
				}

				public function get_vendor_deposit_history($id)
				{

					$status_history = $this->db->query("SELECT * FROM deposit_requests_history WHERE vd_id={$id} ORDER BY id DESC ")->result_array();

					$json['html'] = '';

					foreach ($status_history as $key => $value) {

						$badge = $value['transaction_id'] ?  ' <span class="badge bg-secondary d-inline-block vendor-tran-badge">Tran ID: '. $value['transaction_id'] .'</span>' : '';

						$json['html'].= '<tr><td style="width:250px;">'. withdrwal_status($value['status'])  .'</td>';

						if($value['transaction_id'])
							$json['html'].= '<td>'.$badge.'</td></tr>';
						else 
							$json['html'].= '<td>'.$value['comment'].'</td></tr>';
					}

					echo json_encode($json);die;
				}	

				public function payment_gateway(){

					$userdetails = $this->userdetails();

					$get = $this->input->get(null,true);
					$post = $this->input->post(null,true);

					if(isset($post['value'])){
						if($post['action'] == 'default'){
							$field = 'setting_type';
							$like = 'payment_gateway_'.$post['config'];
							$data_def_second = array('setting_is_default' => 0);
							$this->Product_model->updateWithLike('setting',$field, $like, $data_def_second);

							$where_def_second = array('setting_type' => 'payment_gateway_'.$post['config'].'_'.$post['method']);
							$data_def_second = array('setting_is_default' => (int) $post['value']);
							$this->Common_model->update('setting', $where_def_second, $data_def_second);
						}

						if($post['action'] == 'status')
							$this->Setting_model->save('payment_gateway_'.$post['config'].'_'.$post['method'], array('status' => (int) $post['value']));
						
						
						$json['result'] = true;
						echo json_encode($json);
						die;
					}

					$files = array();
					foreach (glob(APPPATH."/payment_gateway/controllers/*.php") as $file)
						$files[] = $file;

					$paymentGateways = array_unique($files);
					$configs = array('store','deposit','membership');
					$data['payment_gateways'] = array();
					foreach($paymentGateways as $key => $filename){
						require $filename;

						$paymentGateway = basename($filename,".php");
						$setting = $this->Product_model->getSettings('payment_gateway_'.$paymentGateway,'is_install');
						$object = new $paymentGateway($this);
						$gatewayData = array(
							'title' => $object->title,
							'icon' => $object->icon,
							'website' => $object->website,
							'name'  => $paymentGateway,
							'is_install' => ($setting['is_install'] == 1) ? 1 : 0
						);
						$data['payment_gateways'][$paymentGateway] = $gatewayData;

						foreach($configs as $config) {
							$configSetting = $this->Product_model->getSettings('payment_gateway_'.$config.'_'.$paymentGateway);

							$where = array(
								'setting_key' => 'status',
								'setting_type' => 'payment_gateway_'.$config.'_'.$paymentGateway,
								'setting_is_default' => 1
							);
							$default = $this->Common_model->get_total_rows('setting', $where);

							$gatewayConfigData = array(
								'status'  => (isset($configSetting['status']) && $configSetting['status']) ? 1 : 0,
								'setting_is_default' => $default ? 1 : 0
							);

							$data['payment_gateways'][$paymentGateway][$config] = $gatewayConfigData;
						}
					}

					$this->load->config('payment_gateway');
					$data['payment_method'] = config_item('payment_method');


					$data['user'] = $userdetails;
					$data['users'] = $this->db->query("SELECT id,username FROM users WHERE type = 'user' AND is_vendor=1")->result_array();

					$this->view($data,'users/payment_gateway');
				}

				public function payment_gateway_edit($edit_code){

					$userdetails = $this->userdetails();

					if($edit_code != 'opay' && $edit_code != 'paytm'){
						$post = $this->input->post(null,true);
						if($post){
							if($edit_code == 'bank_transfer' && !isset($post['additional_bank_details']))
								$post['additional_bank_details'] = [];
							
							if($edit_code == 'bank_transfer' && isset($post['bank_names']))
								$post['bank_names'] = json_encode($post['bank_names']);


							$status_store['status'] = $post['store'];
							$this->Setting_model->save('payment_gateway_store_'.$edit_code,$status_store);
							unset($post['store']);

							$status_deposit['status'] = $post['deposit'];
							$this->Setting_model->save('payment_gateway_deposit_'.$edit_code,$status_deposit);
							unset($post['deposit']);

							$status_membership['status'] = $post['membership'];
							$this->Setting_model->save('payment_gateway_membership_'.$edit_code,$status_membership);
							unset($post['membership']);

							$this->Setting_model->save('payment_gateway_'.$edit_code,$post);

							$json['redirect'] = base_url('admincontrol/payment_gateway');
							$this->session->set_flashdata('success',__('admin.payment_data_saved_successfully'));

							echo json_encode($json);
							die;
						}

						$files = array();
						foreach (glob(APPPATH."/payment_gateway/controllers/*.php") as $file)
							$files[] = $file;

						$payment_gateways = array_unique($files);
						$payment_gateway = array();

						foreach($payment_gateways as $key => $filename){
							require $filename;

							$code = basename($filename,".php");
							$obj = new $code($this);
							$pdata          = array();
							$pdata['title'] = $obj->title;
							$pdata['code']  = $code;
							if($edit_code == $code){
								$setting_file = APPPATH."/payment_gateway/settings/{$edit_code}.php";
								if(is_file($setting_file)){
									$data['setting_data'] = $this->Product_model->getSettings('payment_gateway_'.$edit_code);
									
									$configs = array('store','deposit','membership');
									foreach($configs as $config)
										$data['setting_data'][$config] = $this->Product_model->getSettings('payment_gateway_'.$config.'_'.$edit_code);

									$data['order_status'] = $this->Order_model->status();
									$pdata['setting'] = $this->getSettings($setting_file, $data);
								}
							}

							$payment_gateway[$code] = $pdata;
						}

						if(isset($payment_gateway[$edit_code])){
							$data['payment_gateway'] = $payment_gateway[$edit_code];
							$data['user'] = $userdetails;
							$this->view($data,'users/payment_gateway_edit');
						} else {
							redirect('admincontrol/payment_gateway');
						}
					} else {
						echo "<script>
						alert('".__('admin.payment_method_not_available')."');
						window.location.href='".base_url('admincontrol/payment_gateway')."';
						</script>";
					}
				}

				public function payment_gateway_documentation(){
					$data = array();
					foreach (glob(APPPATH."/payment_gateway/sample_data/*") as $file)
						$data['sample_data'][] = pathinfo(basename($file))['filename'];

					$this->view($data,'users/payment_gateway_documentation');
				}

				public function payment_gateway_documentation_sample_data($filename){
					if(file_exists(APPPATH.'payment_gateway/sample_data/'.$filename.'.json'))
						debug(file_get_contents(APPPATH.'payment_gateway/sample_data/'.$filename.'.json'));
					else
						redirect('admincontrol/payment_gateway_documentation');
				}

				public function payment_gateway_documentation_to_pdf(){
					$this->load->helper('documentation');
					documentationToPdf();
				}
				
				public function payment_gateway_sample_data_to_pdf(){
					foreach (glob(APPPATH."/payment_gateway/sample_data/*") as $file){
						$sample_data['filename'] = pathinfo(basename($file))['filename'];
						$sample_data['structure'] = file_get_contents($file);

						$data[] = $sample_data;
					}

					$this->load->helper('documentation');
					sampleDataToPdf($data);
				}

				public function payment_gateway_install(){

					// Demo Mode
					if (ENVIRONMENT === 'demo') {
						echo json_encode([
							'status' => 'error',
							'message' => 'Disabled on demo mode'
						]);
						return;
					}
					// Demo Mode

					$upload_path = APPPATH.'payment_gateway/tmp';
					if (!is_writable($upload_path)){
						$json['warning'] = APPPATH.'payment_gateway/tmp '.__('admin.folder_not_have_permission');
						echo json_encode($json);
						die;
					}

					$install = pathinfo($_FILES['install']['name']);
					if($install['extension'] != 'zip'){
						$json['warning'] = __('admin.only_zip_file_accepting');
						echo json_encode($json);
						die;
					}

					foreach (glob(APPPATH.'payment_gateway/controllers/*.php') as $paymentGateway)
						$paymentGateways[] = basename($paymentGateway,'.php');

					if(in_array($install['filename'],$paymentGateways)){
						$json['warning'] = __('admin.this_payment_gateway_already_exist');
						echo json_encode($json);
						die;
					}

					$zip = new ZipArchive();
					if($zip->open($_FILES['install']['tmp_name'])){
						$zip->extractTo($upload_path);
						$zip->close();
					} else {
						$json['warning'] = __('admin.can_not_extract_zip_file');
						echo json_encode($json);
						die;
					}

					$moveAbleFileAndFolder = [];

					$required_folders = ['controller','setting','view'];
					foreach($required_folders as $folder){
						$folder_exist = APPPATH.'payment_gateway/tmp/'.$install['filename'].'/'.$folder;
						if(!is_dir($folder_exist)){
							self::clearPaymentGatewayTmpDirectory(APPPATH.'payment_gateway/tmp/');
							$json['warning'] = $folder.' '.__('admin.folder_not_exist');
							echo json_encode($json);
							die;
						}

						$required_folder = glob(APPPATH.'payment_gateway/tmp/'.$install['filename'].'/'.$folder.'/*');
						if(count($required_folder) > 1){
							self::clearPaymentGatewayTmpDirectory(APPPATH.'payment_gateway/tmp/');
							$json['warning'] = $folder.' '.__('admin.folder_must_keep_only_one_file');
							echo json_encode($json);
							die;
						}

						$file_exist = $folder_exist.'/'.$install['filename'].'.php';
						if(!file_exists($file_exist)){
							self::clearPaymentGatewayTmpDirectory(APPPATH.'payment_gateway/tmp/');
							$json['warning'] = $folder.'/'.$install['filename'].'.php'.' '.__('admin.file_not_exist');
							echo json_encode($json);
							die;
						}

						$array['from'] = APPPATH.'payment_gateway/tmp/'.$install['filename'].'/'.$folder.'/'.$install['filename'].'.php';
						$array['to'] = APPPATH.'payment_gateway/'.$folder.'s/'.$install['filename'].'.php';
						$moveAbleFileAndFolder[] = $array;
					}

					$library_folder = glob(APPPATH.'payment_gateway/tmp/'.$install['filename'].'/library/*');
					if($library_folder){
						if(count($library_folder) > 1){
							self::clearPaymentGatewayTmpDirectory(APPPATH.'payment_gateway/tmp/');
							$json['warning'] = 'library '.__('admin.folder_must_keep_only_one_file');
							echo json_encode($json);
							die;
						}

						$library_exist = APPPATH.'payment_gateway/tmp/'.$install['filename'].'/library/'.$install['filename'];
						if(!is_dir($library_exist)){
							self::clearPaymentGatewayTmpDirectory(APPPATH.'payment_gateway/tmp/');
							$json['warning'] = 'library/'.$install['filename'].' '.__('admin.folder_not_exist');
							echo json_encode($json);
							die;
						}

						$array['from'] = APPPATH.'payment_gateway/tmp/'.$install['filename'].'/library/'.$install['filename'];
						$array['to'] = APPPATH.'payment_gateway/library/'.$install['filename'];
						$moveAbleFileAndFolder[] = $array;
					}

					$logo_folder = glob(APPPATH.'payment_gateway/tmp/'.$install['filename'].'/logo/*');
					if($logo_folder){
						if(count($logo_folder) > 1){
							self::clearPaymentGatewayTmpDirectory(APPPATH.'payment_gateway/tmp/');
							$json['warning'] = 'logo '.__('admin.folder_must_keep_only_one_file');
							echo json_encode($json);
							die;
						}

						$logo_exist = APPPATH.'payment_gateway/tmp/'.$install['filename'].'/logo/'.$install['filename'].'.png';
						if(!file_exists($logo_exist)){
							self::clearPaymentGatewayTmpDirectory(APPPATH.'payment_gateway/tmp/');
							$json['warning'] = 'logo/'.$install['filename'].'.png '.__('admin.file_not_exist');
							echo json_encode($json);
							die;
						}

						$array['from'] = APPPATH.'payment_gateway/tmp/'.$install['filename'].'/logo/'.$install['filename'].'.png';
						$array['to'] = FCPATH.'assets/payment_gateway/'.$install['filename'].'.png';
						$moveAbleFileAndFolder[] = $array;
					}


					foreach($moveAbleFileAndFolder as $key => $value)
						rename($value['from'],$value['to']);
					
					self::clearPaymentGatewayTmpDirectory(APPPATH.'payment_gateway/tmp/');

					$json['location'] = base_url('admincontrol/payment_gateway');
					echo json_encode($json);
					die();
				}

				private function clearPaymentGatewayTmpDirectory($tmpDirectory,$rmdir = false){
					$files = glob($tmpDirectory.'*',GLOB_MARK);
					foreach($files as $file){
						if(is_dir($file))
							self::clearPaymentGatewayTmpDirectory($file,true);
						else
							unlink($file);
					}

					if($rmdir)
						rmdir($tmpDirectory);

					return;
				}

	public function payment_gateway_status_change($code){

		// Demo Mode
		if (ENVIRONMENT === 'demo') {
			$this->session->set_flashdata('error', __('admin.demo_mode'));
			redirect('admincontrol/payment_gateway');
			return;
		}
		// Demo Mode

		if($code != 'opay' && $code != 'paytm'){
			if(file_exists(APPPATH."payment_gateway/controllers/{$code}.php")){
				$settingData = $this->Product_model->getSettings('payment_gateway_'.$code);
				$settingData['is_install'] = ($settingData['is_install'] == 1) ? 0 : 1;

				$this->Setting_model->clear('payment_gateway_'.$code);
				$this->Setting_model->save('payment_gateway_'.$code,$settingData);

				$operation = ($settingData['is_install'] == 0) ? __('admin.uninstalled') : __('admin.installed');
				$this->session->set_flashdata('success',__('admin.payment_gateway').' '.$operation.' '.__('admin.successfully'));
			} else {
				$this->session->set_flashdata('error',__('admin.payment_gateway_not_exist'));
			}

			redirect(base_url('admincontrol/payment_gateway'));
		} else {
			echo "<script>
			alert('".__('admin.payment_method_not_available')."');
			window.location.href='".base_url('admincontrol/payment_gateway')."';
			</script>";
		}
	}

				public function delete_payment_gateway($code){
					$this->load->config('payment_gateway');
					$payment_method = config_item('payment_method');
					if(!in_array($code,$payment_method)){
						if(file_exists(APPPATH."payment_gateway/controllers/{$code}.php")){
							$files= [
								APPPATH."payment_gateway/controllers/{$code}.php",
								APPPATH."payment_gateway/settings/{$code}.php",
								APPPATH."payment_gateway/views/{$code}.php",
								FCPATH."assets/payment_gateway/{$code}.png",
							];
							foreach($files as $key => $file)
								unlink($file);
							
							if(is_dir(APPPATH."payment_gateway/library/{$code}"))	
								$this->deleteDir(APPPATH."payment_gateway/library/{$code}");

							
							$this->load->model('Setting_model');
							$this->Setting_model->clear('payment_gateway_'.$code);

							$this->load->config('payment_gateway');
							foreach(config_item('payment_module') as $key => $value)
								$this->Setting_model->clear('payment_gateway_'.$value.'_'.$code);

							$this->session->set_flashdata('success',__('admin.payment_gateway_deleted_successfully'));
						} else {
							$this->session->set_flashdata('error',__('admin.payment_gateway_not_exist'));
						}
					} else {
						$this->session->set_flashdata('error',__('admin.not_have_permission_to_delete_this_method'));
					}
					

					redirect('admincontrol/payment_gateway');
				}

				public function all_transaction(){
					$userdetails = $this->userdetails();
					$filter = $this->input->post(null,true);
					$this->load->model('Order_model');
					$this->load->library('pagination');
					$config['base_url'] = base_url('admincontrol/all_transaction');
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
					$view['payment_methods'] = $this->Order_model->PaymentMethods();
					$html = $this->load->view("admincontrol/users/part/all_transaction",$view,true);
					if($filter){
						echo $html;
						die();
					}

					$data['html'] = $html;
					
					$this->load->config('payment_gateway');
					$data['payment_module'] =  config_item('payment_module');
					$data['filter_field'] =  $this->Wallet_model->getAllTransactionFilter($userdetails);

					$this->view($data,'users/all_transaction');
				}

				public function all_transaction_export_to_excel(){
					$userdetails = $this->userdetails();
					$filter = $this->input->get(null,true);

					$this->load->helper('all_transaction');
					$all_transaction = $this->Wallet_model->getAllTransaction($userdetails,$filter,false);
					exportToExcel($all_transaction);
				}

				public function all_transaction_export_to_pdf(){
					$userdetails = $this->userdetails();
					$filter = $this->input->get(null,true);
					$this->load->helper('all_transaction');
					$all_transaction = $this->Wallet_model->getAllTransaction($userdetails,$filter,false);
					exportToPdf($userdetails['admin'],$all_transaction);
				}

				public function getOrderDetails() {
					$post = $this->input->post(null,true);
					
					$filter = array(
						'limit' => 1,
						'page' => 1,
						'getSingleOrder' => $post['type'],
						'order_id' => $post['ref2']
					);
					

					list($data['orders'],$total) = $this->Order_model->getAllOrders($filter);

					$data['userdetails'] = $this->userdetails();

					$data['trans']['comment'] = isset($data['orders'][0]) ? $data['orders'][0]['wallet_comment'] : '';
					$data['trans']['comm_from'] = isset($data['orders'][0]) ? $data['orders'][0]['wallet_comm_from'] : '';
					$data['trans']['type'] = isset($data['orders'][0]) ? $data['orders'][0]['wallet_type'] : '';
					$data['trans']['is_action'] = isset($data['orders'][0]) ? $data['orders'][0]['wallet_is_action'] : '';
					
					echo $this->load->view("admincontrol/store/order_details_mb",$data,true);
				}
				
				public function uploadMailImages() {
					if (!is_dir('assets/user_upload/mail_template_images')) {
						mkdir('./assets/user_upload/mail_template_images', 0644, TRUE);
					}
					$imgUploadRes = $this->upload_photo('upload','assets/user_upload/mail_template_images');


					if(isset($imgUploadRes['upload_data']['file_name'])) {
						echo json_encode(array(
							"uploaded" => 1,
							"fileName" => $imgUploadRes['upload_data']['file_name'],
							"url"=> base_url('assets/user_upload/mail_template_images/' . $imgUploadRes['upload_data']['file_name']),
						));
						exit;
					}
					echo json_encode(array(
						"error" => array(
							"message" => $imgUploadRes['message']
						)
					));
					exit;
				}

				public function check_award_level(){
				    if(!$this->userdetails()){ die(); }

				    if ($this->input->server('REQUEST_METHOD') == 'POST'){

				        $result = [];

				        $post = $this->input->post(null,true);

				        $offset = isset($post['index']) ? $post['index'] - 1 : 0;

				        $jumped_user = $this->Product_model->checkJumpedUser(1,$offset);

				        $userCount = $this->Product_model->countByTable('users');

				        if($userCount > $post['index'])
				            $result['index'] = $post['index'] + 1;
				        
				        if($userCount > 0)
				            $result['progress_percentage'] = (($post['index'] / $userCount) * 100)."%";

				        $result['jumped'] = $jumped_user;  // This line is new.

				        if($jumped_user)
				            $result['message'] = __('admin.user_jumped_to_level');

				        echo json_encode($result);
				    }
				}

				public function multiApproveDecline(){
					$post = $this->input->post(null,true);

					$approval_data = [];

					if(isset($post['approve_users']) && !empty($post['approve_users'])) {
						$approval_data['reg_approved'] = 1;
					}

					if(isset($post['decline_users']) && !empty($post['decline_users'])) {
						$approval_data['reg_approved'] = 2;
					}
					
					$idsArray = explode(',', $post['ids']);

					foreach ($idsArray as $user_id) {
						$approval_data['users_ids'] = $user_id;
						
						$checkUser = $this->Product_model->getUserInfo($user_id);
						$json['approvals_status']['status'] = 'NULL';

						if ($checkUser[0]->reg_approved == '0' || $checkUser[0]->reg_approved == '2') {
							if(!empty($approval_data)) {
								$json['approvals_status'] = $this->Product_model->process_approval($approval_data);

								if($json['approvals_status']['status']) {
									$this->load->model('Mail_model');
									$user = App\User::find(array('id' => $approval_data['users_ids']));
									if(isset($post['approve_users']) && !empty($post['approve_users'])) {
										$membership = $this->Product_model->getSettings('membership');
										switch ((int)$membership['status']) {
											case 0:
					                    		//disabled
											$plan_id = -1;
											break;
											case 1:
						                		//all users
											$plan_id = 0;
											break;
											case 2:
						                		//all vendors
											if($is_vendor == 1) {
												$plan_id = 0;
											} else {
												$plan_id = -1;
											}
											break;
											case 3:
						                		//all affiliates
											$plan_id = -1;
											if($is_vendor == 1) {
												$plan_id = -1;
											} else {
												$plan_id = 0;
											}
											break;
											default:
											$plan_id = -1;
											break;
										}

										if($plan_id == 0) {
											if((int)$user[0]['is_vendor'] == 1) {
												$plan_id = $membership['default_vendor_plan_id'] ?? $membership['default_plan_id'];
											} else {
												$plan_id = $membership['default_affiliate_plan_id'] ?? $membership['default_plan_id'];
											}
										}
										if($membership['status'] && $plan_id > 0){
											$plan = App\MembershipPlan::find($plan_id);
											if($plan){
												$plan->buy($user[0], 1, 'Default plan started','Default');
												$commission_processed = $this->db->query('SELECT id from wallet WHERE reference_id='.$approval_data['users_ids'].' AND type="refer_registration_commission"')->result();

												$refid = (int)$user[0]['refid'];

												if(empty($commission_processed) && $refid > 0) {
													$this->load->model('Wallet_model');
													$comission_group_id = time().rand(10,100);
													$referlevelSettings = $this->Product_model->getSettings('referlevel');
													$max_level = isset($referlevelSettings['levels']) ? (int)$referlevelSettings['levels'] : 3;
													
													$json['max_level'] = $max_level;

													$disabled_for = json_decode( (isset($referlevelSettings['disabled_for']) ? $referlevelSettings['disabled_for'] : '[]'),1);
													$refer_status = true;
													if((int)$referlevelSettings['status'] == 0){ $refer_status = false; }
													else if((int)$referlevelSettings['status'] == 2 && in_array($refid, $disabled_for)){ $refer_status = false; }

													$json['refer_status'] = $refer_status;

													if($refer_status) {
														$json['level'] = $level = $this->Product_model->getMyLevel($refid);	
														$json['max_level_user'] = [];
														for ($l=1; $l <= $max_level ; $l++) { 
															if($l == 1) {
																$json['max_level_user'][] = $levelUser = (int)$refid;	
															} else {
																$json['max_level_user'][] = $levelUser = (int)$level['level'.($l-1)];
															}
															$s = $this->Product_model->getSettings('referlevel_'. $l);

															if($s && $levelUser > 0){
																$_giveAmount = 0;
																
																if($referlevelSettings['reg_comission_type'] == 'custom_percentage'){
																	if((int) $referlevelSettings['reg_comission_custom_amt'] > 0) {
																		$_giveAmount = (($referlevelSettings['reg_comission_custom_amt'] * (float)$s['reg_commission']) / 100);
																	}
																} else if($referlevelSettings['reg_comission_type'] == 'fixed'){
																	$_giveAmount = (float)$s['reg_commission'];
																}

																$json['max_level_user']['_giveAmount'] = $_giveAmount;

																if($_giveAmount > 0){
																	$transaction_id1 = $this->Wallet_model->addTransaction(array(
																		'status'       => 1,
																		'user_id'      => $levelUser,
																		'amount'       => $_giveAmount,
																		'dis_type'     => '',
																		'comment'      => "Level {$l} : ".'Commission for new affiliate registrion Id ='. $user[0]['id'] .' | Name : '. $user[0]['firstname'] ." " .$user[0]['lastname'],
																		'type'         => 'refer_registration_commission',
																		'reference_id' => $user[0]['id'],
																		'group_id' => $comission_group_id,
																	));
																}
															}
														}
													}
												}
											}
										}

										$this->Mail_model->send_registration_approved_mail(json_decode(json_encode($user[0])));
									}

									if(isset($post['decline_users']) && !empty($post['decline_users'])) {
										$this->Mail_model->send_registration_declined_mail(json_decode(json_encode($user[0])));
									}
								}
							}
						}
					}

					$json['approvals_count'] = $this->Product_model->getApprovalCounts();
					echo json_encode($json);die;
				}


				public function set_default_admin_url(){
					$set_default = $this->Setting_model->set_default_admin_url();
					echo $set_default;
				}

				public function set_default_front_url(){
					$set_default = $this->Setting_model->set_default_front_url();
					echo $set_default;
				}

				public function update_store_status(){
					$status = $this->input->post('status');

					$update = $this->Setting_model->update_store_status($status);

					if ($status == '0') {
						$update = $this->Setting_model->update_store_menu_on_front('0');
						$update = $this->Setting_model->update_store_menu_on_front_blank('0');
					}

					echo $update;
				}

				public function update_store_menu_on_front(){
					$status = $this->input->post('status');

					$update = $this->Setting_model->update_store_menu_on_front($status);
					echo $update;
				}

				public function update_cookies_menu(){
				    $status = $this->input->post('status');
				    $update = $this->Setting_model->update_cookies_menu($status);
				    echo $update;
				}


				public function update_store_menu_on_front_blank(){
					$status = $this->input->post('status');
					
					$update = $this->Setting_model->update_store_menu_on_front_blank($status);
					echo $update;
				}

				public function update_store_mode(){
					
					$mode = $this->input->post('mode');
					$theme = $this->input->post('theme');

					$update = $this->Setting_model->update_store_mode($mode); //changing the store 
					$theme_update = $this->Setting_model->update_store_theme($theme); //changing the theme
					echo $update;
				}

				public function update_all_settings(){

					$status = $this->input->post('status');
					$setting_key = $this->input->post('setting_key');
					$setting_type = $this->input->post('setting_type');

					//enable-disable vendor mlm module
					if($setting_key=="vendormlmmodule" && $setting_type=="market_vendor")
					{
						$query= $this->db->query("SELECT id FROM `users` where is_vendor=1 and status=1");
						$vendors=$query->result_array();
						for($i=0;$i<count($vendors);$i++)
						{
							$vid=$vendors[$i]['id'];
							$value=array("status"=>$status);
							$this->Setting_model->vendorSave($vid, "referlevel", $value);
						}
					}
					//enable-disable vendor mlm module
					
					$update = $this->Setting_model->update_all_settings($status, $setting_key, $setting_type);


					echo $update;
				}

				public function getShippingDetails() {
					if($this->input->server('REQUEST_METHOD') === 'POST') {
						$user_id = $this->input->post('id');
						$data= $this->db->query("SELECT shipping_address.*,countries.name as country_name,states.name as state_name FROM shipping_address INNER JOIN countries ON countries.id=shipping_address.country_id INNER JOIN states ON states.id=shipping_address.state_id WHERE user_id = $user_id")->row_array();
						echo json_encode(['status'=>empty($data)?false:true,'data'=>$data]);
						exit;
					}

				}

				public function cron(){
					$userdetails = $this->userdetails();
					$this->view($data,'cron/index');
					
				}

				public function update_product_settings(){
					$status = $this->input->post('status');
					$setting_key = $this->input->post('setting_key');
					$product_id = $this->input->post('product_id');
					
					$update = $this->Setting_model->update_product_settings($status, $setting_key, $product_id);
					echo $update;
				}

				public function default_theme_settings(){
					$setting = $this->input->post('setting');
					$color = $this->input->post('color');
					
					$update = $this->Setting_model->default_theme_settings($setting, $color);
					echo $update;
				}

				public function default_font_settings(){
					$setting = $this->input->post('setting');
					$font = $this->input->post('font');
					
					$update = $this->Setting_model->default_font_settings($setting, $font);
					echo $update;
				}

				public function set_default_theme_color_settings(){
					$setting_array = [
						'admin_side_bar_color' => '#ffffff',
						'admin_side_bar_scroll_color' => '#007bff',
						'admin_side_bar_text_color' => '#686868',
						'admin_side_bar_text_hover_color' => '#007bff',
						'admin_top_bar_color' => '#ffffff',
						'admin_footer_color' => '#f2f3f5',
						'admin_logo_color' => '#007bff',
						'admin_button_color' => '#3d5674',
						'admin_button_hover_color' => '#007bff',
						'user_side_bar_color' => '#ffffff',
						'user_side_bar_text_color' => '#3f567a',
						'user_side_bar_clock_text_color' => '#085445',
						'user_side_bar_text_hover_color' => '#5ec394',
						'user_top_bar_color' => '#ffffff',
						'user_footer_color' => '#ffffff',
						'user_button_color' => '#3d5674',
						'user_button_hover_color' => '#085445'
						
					];

					$update = $this->Setting_model->set_default_theme_settings($setting_array, $this->input->post('setting_type'));
					echo $update;
				}

				public function set_default_theme_font_settings(){
					$setting_array = [
						'admin_side_font' => 'PT Sans',
						'user_side_font' => 'Poppins',
						'front_side_font' => 'sans-serif',
						'cart_store_side_font' => 'Jost',
						'sales_store_side_font' => 'Roboto'
					];

					$update = $this->Setting_model->set_default_theme_settings($setting_array, $this->input->post('setting_type'));
					echo $update;
				}

				public function firstsetting() {
					$userdetails = $this->userdetails();
					$this->view($data, '../firstsetting/index');
				}


				public function todolist() {
					$userdetails = $this->userdetails();
					$this->view($data,'todo/todo');
				}

				public function ticketssubject() {
					$userdetails = $this->userdetails();
					$this->view($data,'ticket/ticket-subject');
				}

				public function tickets() {
					$userdetails = $this->userdetails();
					$this->load->model('Tickets_model');
					$this->load->model('Product_model');
					$data['tickets_filter_status'] = $this->Product_model->getSettings('site', 'tickets_filter_status')['tickets_filter_status'] ?? "";
					
					$data['status'] = $this->Tickets_model->status();
					$data['subjects'] = $this->Tickets_model->getsubjectlist();
					$this->view($data,'ticket/ticket-listing');
				}
				public function ticketdetails($ticket_id=Null){
					$userdetails = $this->userdetails();
					$this->userdetails();
					$this->load->model('Tickets_model');
					$res = $this->Tickets_model->getTicketDetails($ticket_id);
					if($res) {
						$data['details'] = $res;
						$data['userName'] = $res['firstname'].' '.$res['lastname'];
						$data['userEmail'] = $res['email'];
						$data['statusNAme'] = $this->Tickets_model->status()[$res['status']];
						$data['status'] = $this->Tickets_model->status();
						$this->view($data,'ticket/ticket-details');
					} else {
						redirect(base_url('admincontrol/tickets'),'refresh');
					}
				}
				public function ticketcreate(){
				    $userdetails = $this->userdetails();
					$data['subjects'] = $this->Common_model->get_data_all_asc('tickets_subject',[],'id,subject','id');
					$data['users'] = $this->db->query("SELECT id,username FROM users WHERE type = 'user'")->result_array();

					$this->view($data,'ticket/ticket-create');
				}

				public function countries_and_states(){
					$userdetails = $this->userdetails();
					//$data['countries'] = $this->Common_model->get_data_all_asc('countries',[],'*','name');
					$data['countries'] = $this->db->query("SELECT * FROM countries ORDER BY name ASC")->result_array();
					$data['states'] = $this->db->query("SELECT states.*, countries.name as country_name FROM states LEFT JOIN countries on states.country_id = countries.id ORDER BY created_by DESC")->result_array();
					$this->view($data,'countries_and_states/list');
				}

				public function createUpdateCountry()
				{
					$userdetails = $this->userdetails();
					$this->load->library('form_validation');
					$json = array();
					$this->form_validation->set_rules('name', __('admin.th_name'), 'required|trim');
					$this->form_validation->set_rules('sortname', __('admin.th_iso_code'), 'required|trim|min_length[2]|max_length[3]');
					$this->form_validation->set_rules('phonecode',  __('admin.th_phone_code'), 'required|trim|numeric');
					$this->form_validation->set_rules('lat', __('admin.th_latitude'), 'required|trim|numeric');
					$this->form_validation->set_rules('lng', __('admin.th_longitude'), 'required|trim|numeric');

					if ($this->form_validation->run() == FALSE) {

						$json['errors'] = $this->form_validation->error_array();

					} else {

						$data = $this->input->post(null,true);
						$nameExist = $this->db->query("SELECT id FROM countries WHERE name='{$data['name']}'")->row_array();

						if(!empty($nameExist) && $nameExist['id'] != $data['id']) {
							$json['errors']['name'] = __('admin.th_name')." ".__('admin.already_exist');
						} else {
						
							$isoExist = $this->db->query("SELECT id FROM countries WHERE sortname='{$data['sortname']}'")->row_array();

							if(!empty($isoExist) && $isoExist['id'] != $data['id']) {
								$json['errors']['sortname'] = __('admin.th_iso_code')." ".__('admin.already_exist');
							}
						}

						if(!isset($json['errors']) && empty($json['errors'])) {
							$country = array(
								'name'       => $data['name'],
								'sortname'       => $data['sortname'],
								'phonecode'       => $data['phonecode'],
								'lat'       => $data['lat'],
								'lng'       => $data['lng'],
								'created_by' => $userdetails['id'],
							);
							if(isset($data['id']) && !empty($data['id'])){
								if($this->db->update("countries",$country,['id' => $data['id']])) {
									$this->session->set_flashdata('success', __('admin.country_updated_success_msg'));
								} else {
									$this->session->set_flashdata('success', __('admin.something_wrong_try_again'));
								}

							} else {
								if($this->db->insert("countries",$country)) {
									$this->session->set_flashdata('success', __('admin.country_created_success_msg'));
								} else {
									$this->session->set_flashdata('success', __('admin.something_wrong_try_again'));
								}
							}
							$json['reload'] = true;
						}
					}

					echo json_encode($json);
				}

				public function createUpdateState()
				{

					$userdetails = $this->userdetails();

					if(empty($userdetails)){ redirect($this->admin_domain_url, 'refresh'); }

					$this->load->library('form_validation');

					$json = array();

					$this->form_validation->set_rules('name', __('admin.name'), 'required|trim');

					$this->form_validation->set_rules('country_id', __('admin.country'), 'required|numeric');

					if ($this->form_validation->run() == FALSE) {

						$json['errors'] = $this->form_validation->error_array();

					} else {

						$data = $this->input->post(null,true);

						$exists = $this->db->query("SELECT id FROM states WHERE name='{$data['name']}' AND country_id='{$data['country_id']}'")->row_array();

						if(!empty($exists) && $exists['id'] != $data['id']) {
							$json['errors']['name'] = __('admin.state')." ".__('admin.already_exist');
						} else {
							$state = array(
								'name'       => $data['name'],
								'country_id'       => $data['country_id'],
								'created_by' => $userdetails['id'],
							);

							if(isset($data['id']) && !empty($data['id'])){
								if($this->db->update("states",$state,['id' => $data['id']])) {
									$this->session->set_flashdata('success', __('admin.state_updated_success_msg'));
								} else {
									$this->session->set_flashdata('danger', __('admin.something_wrong_try_again'));
								}
							} else {
								if($this->db->insert("states",$state)) {
									$this->session->set_flashdata('success', __('admin.state_created_success_msg'));
								} else {
									$this->session->set_flashdata('danger', __('admin.something_wrong_try_again'));
								}
							}

						
							$json['reload'] = true;
						}
					}

					echo json_encode($json);
				}

				public function deleteCountry($id)
				{
					$userdetails = $this->userdetails();
					$country = $this->db->query("SELECT created_by FROM countries WHERE id='{$id}'")->row_array();
					if($userdetails['type']=='admin' || $country['created_by'] == $userdetails['id']) {
						$this->db->query("DELETE FROM countries WHERE id='{$id}'");
						$this->db->query("DELETE FROM states WHERE country_id='{$id}'");
						$this->session->set_flashdata('success', __('admin.country_delete_success'));
					} else {
						$this->session->set_flashdata('danger', __('admin.country_delete_not_allow'));
					}
				
					header('Location: ' . $_SERVER['HTTP_REFERER']);
				}

				public function deleteState($id)
				{
					$userdetails = $this->userdetails();
					$state = $this->db->query("SELECT created_by FROM states WHERE id='{$id}'")->row_array();
					if($userdetails['type']=='admin' || $state['created_by'] == $userdetails['id']) {
						$this->db->query("DELETE FROM states WHERE id='{$id}'");
						$this->session->set_flashdata('success', __('admin.state_delete_success'));
					} else {
						$this->session->set_flashdata('danger', __('admin.state_delete_not_allow'));
					}
					header('Location: ' . $_SERVER['HTTP_REFERER']);
				}


				public function refactor_database()
				{
					try {
						// Demo Mode
						if (ENVIRONMENT === 'demo') {
							$this->session->set_flashdata('error', __('admin.demo_mode'));
							redirect('admincontrol/backup');
							return;
						}
						// Demo Mode

						$userdetails = $this->userdetails();

						if(empty($userdetails)){ redirect($this->admin_domain_url, 'refresh'); }

						$srcFileName = APPPATH.'/backup/database_update_'.$this->config->item('app_version').'.sql';

						if (file_exists($srcFileName)) {
						    $copyFileName = './database_update_'.$this->config->item('app_version').'.sql';

							if (!copy($srcFileName, $copyFileName)) {
								$this->session->set_flashdata('error', __('admin.something_went_wrong'));
							}
						} else {
							$this->session->set_flashdata('error', __('admin.database_update_file_not_exist'));
						}

					} catch (Exception $e) {
						$this->session->set_flashdata('error', $e->getMessage());
					}

					header('Location: ' . $_SERVER['HTTP_REFERER']);
				}

				public function uncompleted_payments(){
					$userdetails = $this->userdetails();
					$filter = $this->input->post(null,true);
					$this->load->model('Order_model');
					$this->load->library('pagination');

					$config['base_url'] = base_url('admincontrol/uncompleted_payments');
					$config['uri_segment'] = 3;
					$filter['limit'] = $config['per_page'] = 10;
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

					$this->view($data,'users/uncompleted_payments');
				}


				public function listreviews_ajax($page = 1){

					$userdetails = $this->userdetails();
					$get = $this->input->get(null,true);
					$post = $this->input->post(null,true);
					
					$page=isset($get['page']) ? $get['page'] : $page;
					$limit=50;
					 
					$product_id=null;
					if(isset($post['product_name_review']) && $post['product_name_review']){
						$product_id = (int)$this->input->post('product_name_review');
				 	}
					
					$data = $this->Product_model->getAllReviewFilter($product_id,$limit,$page);
					
					$data['user_id']=$userdetails['id'];	

					$json['view'] = $this->load->view("admincontrol/product/review_list", $data, true);
					
					$this->load->library('pagination');

					$this->pagination->cur_page = $page;

					$config['base_url'] = base_url('admincontrol/listreviews_ajax');

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
					$post = $this->input->post(null,true);
					if(!empty($post) && isset($post['product_name'])){
			 			
						$this->load->helper(array('form', 'url'));
						$this->load->library('form_validation');

						$this->form_validation->set_rules('product_name', __('admin.product_name'), 'required');
						$this->form_validation->set_rules('firstname', __('admin.firstname'), 'required' );
						$this->form_validation->set_rules('lastname', __('admin.lastname'), 'required' );
						$this->form_validation->set_rules(
							'review_description', __('admin.review_description'),
							'required|min_length[5]|max_length[150]',
							array(
								'required'      => 'Enter %s',
								'is_unique'     => 'This %s already exists.',
								'min_length' 	=> '%s: the minimum of characters is %s',
								'max_length' 	=> '%s: the maximum of characters is %s',
							)
						);

						
						$this->form_validation->set_rules('rating',__('admin.rating'), "required"); 
						$this->form_validation->set_rules('rating_created',__('admin.review_date_-_time'), "required"); 
						

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
							 if(!empty($_FILES['user_image']['name'])){
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
									$this->session->set_flashdata('success', __('admin.review_updated_successfully'));
									$json['location'] = base_url('admincontrol/listproduct'); 
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
									$this->session->set_flashdata('success', __('admin.review_inserted_successfully'));
									$json['location'] = base_url('admincontrol/listproduct'); 
			 					}
								
					 		}
					 	 
							
						}
						echo json_encode($json);
						exit; 
					}	
					$data['review'] = $this->Product_model->getReviewById($id)[0]; 
					$filter['product_status_in'] =	 '1';
					$filter['only_admin_product'] =	 '1';
					

					$data['products'] = $this->Product_model-> getAllProduct($userdetails['id'],'admin',$filter);
					
					$data['setting'] = $this->Product_model->getSettings('productsetting'); 
					
					if(isset($data['review']['rating_created_by']) && $data['review']['rating_created_by']!= $userdetails['id'])
					{
			 			$this->session->set_flashdata('error', __('admin.you_can_not_edit_other_user_review'));	
			 			redirect('admincontrol/listproduct');
					}
					else
			 			$this->view($data, 'product/add_review');
					
				}


				public function deleteReview($id = null){

					$userdetails = $this->userdetails();
					if(empty($userdetails)){
						redirect($this->admin_domain_url);
					}

					if($id!="" && $id>0)
					{
						$res=$this->Product_model->deleteReview($id);
						if(isset($res))
							$this->session->set_flashdata('success', __('admin.review_has_been_deleted_successfully'));
						else
			 				$this->session->set_flashdata('success', __('admin.review_not_deleted'));
					} 
					redirect('admincontrol/listproduct');
			 	}

			 	public function checkDateTime($date)
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

			    public function bulkReviewImportFromUrl() 
				{
					$userdetails = $this->userdetails();
					$data=$this->Review_model->bulkReviewImportFromUrlData($userdetails); 
			 		echo $this->load->view("admincontrol/product/bulk_review_upload_modal",$data,true);

				}

			 	public function bulkReviewsImport() {
					$userdetails = $this->userdetails();
			 		$data=$this->Review_model->bulkReviewsImportData($userdetails); 
			 		echo $this->load->view("admincontrol/product/bulk_review_upload_modal",$data,true); 
				}

				public function bulkReviewImportConfirm() 
				{
					$userdetails = $this->userdetails();
					$data = json_decode(base64_decode($_POST['reviews']), true);
					$result=$this->Review_model->bulkReviewImportConfirmData($userdetails,$data); 
					echo $this->load->view('admincontrol/product/bulk_review_upload_modal', $result, true); 
				}


				public function exportReviewXML(){
					$userdetails = $this->userdetails();
					$json=$this->Review_model->exportReviewXMLData($userdetails); 
					echo json_encode($json);
				 	 
				}

				public function downloadproductreviewxmlstructurefile($filename = NULL) {
				    $userdetails = $this->userdetails();
				    $this->load->helper('download');
				    $data = file_get_contents(FCPATH.'assets/xml/export_admin_product_reviews_structure.xml');
				    force_download("export_admin_product_reviews_structure.xml", $data);
				}

				public function downloadproductreviewxmlfile($filename = NULL) {
				    $userdetails = $this->userdetails();
				    $this->load->helper('download');
				    $data = file_get_contents(FCPATH.'assets/xml/export_admin_product_reviews.xml');
				    force_download("export_admin_product_reviews.xml", $data);
				}

				public function getTermAndCondition() {
					$userdetails = $this->userdetails();
					$post = $this->input->post(null,true);
					if(!empty($post) && isset($post['language_id'])){
						$data = $this->Product_model->getSettingsWithLanaguage('tnc',$post['language_id'],'');
						$json['heading'] =  $data['heading'];
						$json['content'] =  $data['content'];
					}
					echo json_encode($json);
				}

				public function getStaticPages() {
					$userdetails = $this->userdetails();
					$post = $this->input->post(null,true);
					if(!empty($post) && isset($post['language_id'])){
					$storesettings = $this->Product_model->getSettingsWithLanaguage('store',$post['language_id'],'');
					$staticpages = array("about_content", "contact_content", "policy_content");
						$staticcontent=array();
						foreach ($storesettings as $skey => $svalue) 
						{
							if(in_array($skey, $staticpages))
							{
								$staticcontent=array_merge($staticcontent,array($skey=>$svalue)); 
							}
						}  
						
						$json=$staticcontent;

					}
					echo json_encode($json);
				}

				function troubleshoot()
				{
					$userdetails = $this->userdetails();
					$data=array();
					$this->view($data, 'document/troubleshoot');

				}

				public function tutorial()
				{
					$userdetails = $this->userdetails();
					$data=array(); 
					$data['site']=$this->Product_model->getSettings('site'); 
					$data['languages'] = $this->db->query("SELECT * FROM language where status=1")->result_array();
					
					// manage auto dropdown select language value
					if(isset($_SESSION['userLang'])) 
					$data['userlangid']=$_SESSION['userLang'];
					// manage auto dropdown select language value

					$this->view($data, '../tutorial/index');
				}
				public function listTutorals_ajax($page = 1){
					$userdetails = $this->userdetails();
					$this->load->model('Tutorial_model');
					$this->Tutorial_model->list();
				}

				public function manage_tutorial($id = null){
					$userdetails = $this->userdetails();
					$this->load->model('Tutorial_model');
					$data=$this->Tutorial_model->manage($userdetails,$id); 
					$this->view($data, '../tutorial/manage_tutorial');
				}

				public function getTutorialCategory(){
					$this->load->model('Tutorial_model');
					$json['html']=$this->Tutorial_model->getCateogryDropdown();
					echo json_encode($json); 
				}

				public function deleteTutorial($id = null){
					$userdetails = $this->userdetails();
					if(empty($userdetails)){
						redirect($this->admin_domain_url);
					}

					if($id!="" && $id>0)
					{
						$this->load->model('Tutorial_model');
						$res=$this->Tutorial_model->delete($id);
						if(isset($res))
							$this->session->set_flashdata('success', __('admin.tutorial_has_been_deleted_successfully'));
						else
			 				$this->session->set_flashdata('success', __('admin.tutorial_not_deleted'));
					} 
					redirect('admincontrol/tutorial');
			 	}

			 
			 	public function listTutorialCategory_ajax($page = 1){
					$userdetails = $this->userdetails();
					$this->load->model('Tutorial_model');
					$this->Tutorial_model->listCategory();
				}

				public function manage_tutorial_catgory($id = null){
					$userdetails = $this->userdetails();
					$this->load->model('Tutorial_model');
					$data=$this->Tutorial_model->manageCategory($userdetails,$id); 
					$this->view($data, '../tutorial/manage_category');
				}

				public function deleteTutorialCategory($id = null){
					$userdetails = $this->userdetails();
					if(empty($userdetails)){
						redirect($this->admin_domain_url);
					}

					if($id!="" && $id>0)
					{
						$this->load->model('Tutorial_model');
						$res=$this->Tutorial_model->deleteCategory($id); 
						if((int)$res===2)
							$this->session->set_flashdata('error', __('admin.category_can_not_deleted_it_already_used_in_pages'));
						else if(isset($res))
							$this->session->set_flashdata('success', __('admin.category_has_been_deleted_successfully'));
						else
			 				$this->session->set_flashdata('error', __('admin.category_not_deleted'));
					} 
					redirect('admincontrol/tutorial');
			 	}

			 	public function getLoginContent_ajax() {
					$userdetails = $this->userdetails();
					$post = $this->input->post(null,true);
					if(!empty($post) && isset($post['language_id'])){
					$data = $this->Product_model->getSettingsWithLanaguage('loginclient',$post['language_id'],'');
						$json['home_heading'] =  $data['heading'];
						$json['home_content'] =  $data['content'];
						$json['about_content'] =  $data['about_content'];
					}
					if (!empty($post) && isset($post['language_id'])){
						$data = $this->Product_model->getSettingsWithLanaguage('tnc',$post['language_id'],'');
						$json['policy_heading'] =  $data['heading'];
						$json['policy_content'] =  $data['content'];
					}

					echo json_encode($json);
				}

			}

