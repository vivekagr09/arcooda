<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

use App\User;

require APPPATH . 'hooks/Affiliate_Hook.php';

class Productsales extends MY_Controller {

	protected $userdetails;

	function __construct() {
		parent::__construct();

		$this->userdetails = $this->session->userdata('administrator');
		___construct(1);

		$this->checkSessionTimeout();
	}

	public function index($only_review = false){

		$data['user'] = $userdetails = $this->userdetails;

		if(empty($userdetails)) redirect($this->admin_domain_url);

		set_default_language();

		$this->load->model('Form_model');
		$this->load->model('Product_model');
		$this->load->model('Wallet_model');


		$data['store_setting'] =	$this->Product_model->getSettings('store');
		
		$data['Product_model'] =	$this->Product_model;

		$data['totals'] = $this->Wallet_model->getTotals(array(), true);

		$filter = array();

		$get = $this->input->get(null,true);

		$filter['is_campaign_product'] = 1;
		
		$filter['product_status_in'] =	 '1';


		if(isset($get['seller_id']) && $get['seller_id']){

			$filter['seller_id'] = (int)$this->input->get('seller_id');

		}

		if($only_review == 'reviews'){

			$filter['product_status_in'] =	 '0,2,3';

		}

		$data['productlist'] = $this->Product_model->getAllProduct($userdetails['id'], $userdetails['type'], $filter);
 

		$data['client_count'] =$this->db->query('SELECT count(*) as total FROM users WHERE  type like "client"')->row()->total;

		$data['ordercount'] =$this->db->query('SELECT COUNT(op.id) as total FROM `order_products` op LEFT JOIN `order` as o ON o.id = op.order_id WHERE o.status > 0 ')->row()->total;

		$data['categories'] = $this->db->query("SELECT id,name FROM categories")->result_array();

		$data['vendors'] = $this->db->query("SELECT users.id,CONCAT(users.firstname,' ',users.lastname) as name FROM `product_affiliate` LEFT JOIN users ON users.id= user_id GROUP by user_id")->result_array();

		$this->load->library("socialshare");				

		$data['social_share_modal'] =  $this->socialshare->get_dynamic_social_share_btns();
		$data['currentTheme'] = User::getActiveTheme();
		$data['StoreStatus'] = User::getStoreStatus();

		if($only_review == 'reviews'){

			$this->view($data,'product_campaign/reviews');

		} else {

			$this->view($data,'product_campaign/index');

		}
	}

	public function listproduct_ajax($page = 1){

		$userdetails = $this->userdetails;

		if(empty($userdetails)) redirect($this->admin_domain_url);

		$get = $this->input->get(null,true);

		$post = $this->input->post(null,true);



		$filter = array(

			'page' => isset($get['page']) ? $get['page'] : $page,

			'limit' => 20,

		);

		$filter['is_campaign_product'] = 1;


		if(isset($post['seller_id']) && $post['seller_id']){

			$filter['seller_id'] = (int)$this->input->post('seller_id');

		}


		$filter['product_status_in'] =	 '1';

		if($only_review == 'reviews'){

			$filter['product_status_in'] =	 '0,2,3';

		}

		$data['default_commition'] =$this->Product_model->getSettings('productsetting');

		$record = $this->Product_model->getAllProduct($userdetails['id'], $userdetails['type'],$filter);

		$data['productlist'] = $record['data'];

		$data['pro_setting'] = $this->Product_model->getSettings('productsetting');

		$data['vendor_setting'] = $this->Product_model->getSettings('vendor');

		$json['view'] = $this->load->view("admincontrol/product_campaign/list", $data, true);

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

	public function create(){
		if(!$this->userdetails){ redirect('admincontrol/dashboard', 'refresh'); }

		$this->load->model('Product_model');

		$data['checkout_template'] = get_available_checkout_template();

		$data['userdetails'] 	= $this->userdetails;
		
		$data['setting'] 	= $this->Product_model->getSettings('productsetting');

		$data['country_list'] = $this->db->query("SELECT name,id FROM countries")->result();

		$data['product'] = $this->Product_model->getProductById($id);

		$this->view($data, 'product_campaign/form');
	}

	public function update($id = null){

		$userdetails = $this->userdetails;

		if(empty($userdetails)) redirect($this->admin_domain_url);

		$this->load->model('Product_model');

		$product = $this->Product_model->getProductById($id);

		$product = json_decode(json_encode($product), true);

		$data['product'] = $this->Product_model->productDataWithMeta($product);

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

		$this->view($data, 'product_campaign/form');
	}

	public function store(){

		$userdetails = $this->userdetails;

		$post = $this->input->post(null,true);

		if(!empty($post)){

			$product_id = (int)$this->input->post('product_id',true);

			$this->load->helper(array('form', 'url'));

			$this->load->library('form_validation');

			$this->form_validation->set_rules('product_url', __('admin.product_purchase_url'), 'required');

			$this->form_validation->set_rules('product_name', __('admin.product_name_'), 'required');

			$this->form_validation->set_rules('category[]', "Category", "required");

			$this->form_validation->set_rules('product_description', __('admin.product_description'), 'required' );

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
				);				

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

					if(isset($_FILES['downloadable_file']['name']) && is_countable($_FILES['downloadable_file']['name']))
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


				if(empty($errors)){

					$old_product_data =[];

					if($product_id){
						$old_product_data = $this->db->query("SELECT * FROM product WHERE product_id = ". (int)$product_id)->row_array();
						$details['product_updated_date'] = date('Y-m-d H:i:s');
						$this->Product_model->update_data('product', $details, array('product_id' => $product_id));
						$this->session->set_flashdata('success', __('admin.product_campaign_updated_successfully'));
					} else {
						$details['product_created_by'] = $userdetails['id'];
						$details['product_updated_date'] = date('Y-m-d H:i:s');
						$details['product_created_date'] = date('Y-m-d H:i:s');
						$product_id = $this->Product_model->create_data('product', $details);
						$this->session->set_flashdata('success', __('admin.product_campaign_added_successfully'));
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
						$json['location'] = base_url('Productsales/index');
					} else {
						$json['location'] = base_url('Productsales/update/'.$product_id);
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

	public function delete(){
		$post = $this->input->post(null,true);

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

			$this->session->set_flashdata('success', __('admin.product_is_deleted_successfully'));


		}  else {

			$id = (int)$this->input->get('delete_id');

			$orderProduct = $this->db->query('SELECT id FROM order_products WHERE product_id = '.$id)->row();

			if(empty($orderProduct)) {
				$res = $this->Product_model->deleteproducts($id);

				$this->session->set_flashdata('success', __('admin.product_is_deleted_successfully'));
			} else {
				$this->session->set_flashdata('error', __('admin.order_product_not_deleted'));
			}

		}

		redirect(base_url() . 'Productsales/index');
	}

	public function integration_code_modal(){
		if(!$this->userdetails){ redirect('admincontrol/dashboard', 'refresh'); }

		$data['product'] = $this->db->query('select * from product where product_id='.(int)$this->input->post('id',true))->row();

		echo $this->load->view('admincontrol/product_campaign/integration_code_modal', $data, true);
		die;
	}


	public function addProductCampaignClick(){



		$content = file_get_contents("php://input");

		if($content){
			parse_str($content, $data);
		} else {
			$data = $this->input->get(null);
		}

		if(isset($data['af_id']) && isset($data['product_id'])) {

			list($affiliate_id,$click_product_id) = explode("-", _encrypt_decrypt(parse_affiliate_id($data['af_id']),'decrypt'));
			
			$affiliate_id = (int) $affiliate_id;

			$vendor_setting = $this->Product_model->getSettings('vendor');

			$store_setting = $this->Product_model->getSettings('store');
 
			if($affiliate_id > 0) {

				$Affiliate_Hook = new Affiliate_Hook;

				if($Affiliate_Hook->is_suspicious_click($affiliate_id)) {
					die('suspicious click!');
				}
				
				$product_id = (int) _encrypt_decrypt(parse_affiliate_id($data['product_id']),'decrypt');

				if((int)$click_product_id !== (int)$product_id) {
					die('multi-product exception!');
				}

				$product = $this->db->query('select * from product where is_campaign_product = 1 AND product_status = 1 AND product_id='.$product_id)->row_array();


				$_user = $this->Product_model->getUserDetails($affiliate_id);
				

				$is_vendor = $_user['is_vendor'] == 1;

				if(! $this->allowCommissionFromVendorPanelMode($product['product_created_by'], $is_vendor)) {
					die(__('admin.commission_blocked_from_vendor_panel_mode'));
				}

				if(empty($product)) {
					die('campaign product not available!');
				}

				if ($store_setting['store_mode'] == 'cart') {
					die('restricted panel!');
				}


				$restricted_vendors = $this->get_restricted_vendors($affiliate_id,$product['product_slug']);

				//updated function - sales mode restricet
				if(in_array($affiliate_id, $restricted_vendors) || in_array($product['product_created_by'], $restricted_vendors)) {

					die('restricted user!');
				}

				$this->load->model('Product_model');

					//add view statistics 

					if(isset($product_id) && isset($affiliate_id))
					{
						if(isset($_COOKIE))
						$session_id = $_COOKIE['product_view_ck'];
						else
							$session_id=null;
						$Affiliate_Hook = new Affiliate_Hook; 
	 
						if(!isset($session_id))
						{
							$session_id = uniqid().rand();
							$time = (60 * 60);
							setcookie("product_view_ck", $session_id, time()+$time, "/" ); 
						}
	 					
						$ip = $Affiliate_Hook->get_client_ip();
						$viewData = array(
				            'user_id'  => (int)$affiliate_id,
				            'product_id'  => (int)$product_id,
				            'link' => '', 
				            'ip'      =>  $ip,
				            'session_id'      =>  $session_id, 
				            'created_at'      =>  date('Y-m-d H:m:s'),

				        );

						$viewcheck=$this->Product_model->save_view_logs($viewData);
						if($viewcheck!=2)
						{

							$this->db->set('view_statistics', 'view_statistics+1', FALSE);
							$this->db->where('product_id', (int)$product_id);
							$this->db->update('product');
						}  
					}
					
 

				// end view code.. 
				
				$match = $this->Product_model->getProductAction($product_id, $affiliate_id);
				
				if ($match == 0){
					$this->Product_model->setClicks($product_id, $affiliate_id);
				} else {

					$this->Product_model->getProductActionIncrese($product_id, $affiliate_id);
				}
				
				$wallet_group_id = time().rand(10,100);

				$transaction_id = $this->Product_model->giveClickCommition($product, $affiliate_id, 0, $wallet_group_id);
				
				if((int)$transaction_id > 0) {
					if ($vendor_setting['admin_click_status'] == 1) {

						$this->Product_model->giveAdminClickCommition($product, $wallet_group_id);
					}
					
					$this->Product_model->referClick($product, $affiliate_id, 0, $wallet_group_id);

					echo "success";
				}

			}
		} else {
			die('invalid request!');
		}
	}

	public function placeOrder($productID, $affiliateID) {

		$product_id = (int) _encrypt_decrypt(parse_affiliate_id($productID),'decrypt');

		list($affiliate_id,$click_product_id) = explode("-", _encrypt_decrypt(parse_affiliate_id($affiliateID),'decrypt'));

		$affiliate_id = (int) $affiliate_id;
		//$affiliate_id = $_COOKIE['affiliate_id'];

		$restricted_vendors = $this->get_restricted_vendors();

		$product = $this->db->query('select * from product where is_campaign_product = 1 AND product_status = 1 AND product_id='.$product_id)->row_array();

		if(empty($product)) {
			die("product not found!");
		}

		if(in_array($product['product_created_by'], $restricted_vendors)) {
			die("restricted vendor!");
		}

		$Affiliate_Hook = new Affiliate_Hook;

		if($Affiliate_Hook->is_suspicious_click($affiliate_id)) {
			$blockAffiliateCommission = true;
		}

		if(in_array($affiliate_id, $restricted_vendors)) {
			$blockAffiliateCommission = true;
		}

		$refer_id = isset($blockAffiliateCommission) ? null : $affiliate_id;

		$this->load->model('cart');
		$this->cart->clearCart();
		$this->cart->add($product_id, 1, null, $refer_id, $product);
		redirect(base_url() . 'store/checkout');
	}

	public function update_product_settings(){
		$status = $this->input->post('status');
		$setting_key = $this->input->post('setting_key');
		$product_id = $this->input->post('product_id');
		
		$update = $this->Setting_model->update_product_settings($status, $setting_key, $product_id);
		echo $update;
	}
}