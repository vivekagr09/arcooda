<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class PublicData extends MY_Controller {
	public function get_classified_header()
	{


		$this->load->model('Product_model');

		$store_setting = $this->Product_model->getSettings('store');
		$defaultLogo =  $store_setting['theme'] == 'classified' ? base_url().'assets/store/classified/assets/img/logo.png' : base_url('assets/store/default/').'img/logo.png';

		$data['logo'] = ($store_setting['logo']) ? base_url('assets/images/site/'.$store_setting['logo']) : $defaultLogo;

		$data['home_page_url'] = base_url('store');
		$data['aboutus_page_url'] = base_url('store/about');
		$data['catalog_page_url'] = base_url('store/catalog');
		$data['contact_page_url'] = base_url('store/contact');

		
		$loginUser = $this->session->userdata('user');

		if(empty($loginUser)){
			$loginUser = $this->session->userdata('client');
		}
		
		if(!empty($loginUser)){
			$data['loginUser'] = $this->db->query("SELECT * FROM users WHERE id=". $loginUser['id'])->row_array();
			$data['customer_logout_url'] = base_url('store/logout');
			$data['customer_orders'] = base_url('store/orders');
			$data['customer_wishlist'] = base_url('store/wishlist');
			$data['customer_profile'] = base_url('store/profile');
			$data['affiliate_dashboard_url'] = base_url();
		} else {
			$data['affiliate_login_url'] = base_url();
			$data['customer_login_url'] = base_url('store/login');
		}

		$this->load->model('Product_model');

		$data = array_merge($data, $this->getChangeLanguageSelectorData(), $this->getChangeCurrencySelectorData());
		
		
		
		echo json_encode($data);
	}

	public function get_classified_checkout_language_and_currency($value='')
	{
		$data = array_merge([], $this->getChangeLanguageSelectorData(), $this->getChangeCurrencySelectorData());
		echo json_encode($data);
	}

	public function getChangeCurrencySelectorData()
	{
		$data['CurrencyHtml'] = $this->Product_model->getCurrencyHtml('AuthController', 'store-default', true);

		foreach ($data['CurrencyHtml'] as &$curr) {
			if(isset($_SESSION['userCurrency'])) {
				$data['SelectedCurrency'] = $_SESSION['userCurrency'];
			} else if((int)$curr['is_default'] == 1) {
				$data['SelectedCurrency'] = $curr['code'];
			}

			$curr['href'] = base_url("Admincontrol/change_currency/".$curr['code']);
		}

		return $data;
	}

	public function getChangeLanguageSelectorData()
	{

		return $this->Product_model->getLanguageHtml('AuthController', 'store-default', true);
	}

	public function get_classified_footer()
	{

		$this->load->model('Product_model');

		$data['home_page_url'] = base_url('store');
		
		$data['aboutus_page_url'] = base_url('store/about');

		$store_setting = $this->Product_model->getSettings('store');

		$defaultLogo =  $store_setting['theme'] == 'classified' ? base_url().'assets/store/classified/assets/img/logo.png' : base_url('assets/store/default/').'img/logo.png';

		$data['logo'] = ($store_setting['logo']) ? base_url('assets/images/site/'.$store_setting['logo']) : $defaultLogo;

		$data['about_content'] = !empty($store_setting['about_content']) ? $store_setting['about_content'] : __('store.about_us_if_not_exist');


		$data['about_content'] = strip_tags($data['about_content']);


		$privacy_and_copyrights = ($settings['footer'] != '') ? $settings['footer'] : __('store.all_rights_reserved');

		$data['privacy_and_copyrights'] = "<p>".$privacy_and_copyrights." ".date('Y')." | <a href=\"".base_url('store/policy')."\" class=\"text-light\">".__('store.policy')."</a></p>";

		$data['payment_gateways'] = [];
		
		$payments = get_payment_gateways();
		
		foreach ($payments as $key => $payment) {
			if($payment['status']){
				$data['payment_gateways'][] = [
					'title' => $payment['title'],
					'icon' => base_url($payment['icon'])
				];
			}
		}

		$product_query = $this->prepareProductQuery();

		$product_query .= $this->get_filter_for_restricted_vendor();

		$data['recent_products'] = $this->db->query($product_query. ' HAVING (product_launch_date IS NULL OR product_launch_date < "'.$currentDateTime.'") order by product.product_created_date DESC LIMIT 2')->result_array();

		foreach ($data['recent_products'] as &$product) {
			$data['recent_products_available'] = true;
			$product = $this->prepare_product_data_for_theme($product, $refer_id, [1000,1000]);
		}

		echo json_encode($data);
	}

	public function get_classified_home_page() 
	{
		
 
		$data['currentDateTime'] = $currentDateTime = date('Y-m-d H:i:s');


		$data['filter'] = $this->get_category_and_location_filter_options_all();
		
		$product_query = $this->prepareProductQuery();


		$product_query .= $this->get_filter_for_restricted_vendor();

		$data['latest_products'] = $this->db->query($product_query. ' HAVING (product_launch_date IS NULL OR product_launch_date < "'.$currentDateTime.'") order by product.product_updated_date DESC LIMIT 24')->result_array();
 
		
		$data['popular_products'] = $this->db->query($product_query. ' HAVING total_sale > 0 AND (product_launch_date IS NULL OR product_launch_date < "'.$currentDateTime.'") order by total_sale DESC LIMIT 24')->result_array();

		$data['launching_products'] = $this->db->query($product_query. ' HAVING (product_launch_date IS NOT NULL AND product_launch_date > "'.$currentDateTime.'") order by product_launch_date DESC LIMIT 24')->result_array();

		$data['discount_products'] = $this->db->query($product_query. ' HAVING (product_sale_start IS NOT NULL AND product_sale_start < "'.$currentDateTime.'" AND product_sale_end IS NOT NULL AND product_sale_end > "'.$currentDateTime.'") order by product_sale_start DESC LIMIT 24')->result_array();
 	
		$data['featured_products'] = $this->db->query($product_query. ' HAVING (product_launch_date IS NULL OR product_launch_date < "'.$currentDateTime.'") AND show_to_featured=1 order by product_updated_date')->result_array();


		$refer_id = $this->get_refer_id();

		foreach ($data['latest_products'] as &$product) {
			$data['latest_products_available'] = true;
			$product = $this->prepare_product_data_for_theme($product, $refer_id);
		}
 
		foreach ($data['popular_products'] as &$product) {
			$data['popular_products_available'] = true;
			$product = $this->prepare_product_data_for_theme($product, $refer_id);
		}

		foreach ($data['launching_products'] as &$product) {
			$data['launching_products_available'] = true;
			$product = $this->prepare_product_data_for_theme($product, $refer_id);
		}
		
		foreach ($data['discount_products'] as &$product) {
			$data['discount_products_available'] = true;
			$product = $this->prepare_product_data_for_theme($product, $refer_id);
		}

		foreach ($data['featured_products'] as &$product) {
			$data['featured_products_available'] = true;
			$product = $this->prepare_product_data_for_theme($product, $refer_id);
		}
 
		$catgelogcountquery="SELECT 
			category_id, COUNT(*) as products_count, categories.*
			FROM product_categories
			LEFT JOIN categories ON categories.id = product_categories.category_id
			LEFT JOIN product ON product_categories.product_id = product.product_id 
			INNER JOIN users ON product.product_created_by = users.id
			WHERE product.product_status = 1 AND product.is_campaign_product = 1 AND product.on_store = 1 ";

		$catgelogcountquery .= $this->get_filter_for_restricted_vendor();	
		$catgelogcountquery .=" GROUP BY category_id ORDER BY products_count DESC limit 6";

		$data['popular_categories'] = $this->db->query($catgelogcountquery)->result_array();

		foreach ($data['popular_categories'] as &$category) {
			$category = $this->prepare_category_data_for_theme($category);
		}

		

		$this->load->model('Product_model');
		
		$store_setting = $this->Product_model->getSettings('store');

		$data['theme_sections']['classified_banner_title'] = isset($store_setting['classified_banner_title']) && !empty($store_setting['classified_banner_title']) ? $store_setting['classified_banner_title'] : __('store.explore_places_in_suburb');

		$data['theme_sections']['classified_banner_subtitle'] = isset($store_setting['classified_banner_subtitle']) && !empty($store_setting['classified_banner_subtitle']) ? $store_setting['classified_banner_subtitle'] : __('store.keep_choose_best');

		$data['theme_sections']['classifiedbannerimg'] = isset($store_setting['classifiedbannerimg']) && ! empty($store_setting['classifiedbannerimg']) ? base_url('assets/images/site/'.$store_setting['classifiedbannerimg']) : base_url('assets/store/classified/media/banner/banner3.jpg');
 

		echo json_encode($data);
	}

	public  function get_filter_for_restricted_vendor()
	{
		$strquery="";
		$restricted_vendors = $this->get_restricted_vendors();	
		if (isset($restricted_vendors) && !empty($restricted_vendors)) {
			$tempvq = "";

			foreach ($restricted_vendors as $vid) {
				if($tempvq != "") {
					$tempvq .= " AND (users.id IS NULL OR users.id != ".(int)$vid.") ";
				} else {
					$tempvq .= " (users.id IS NULL OR users.id != ".(int)$vid.") ";
				}

			}

			if($tempvq != "") {
				$strquery .= " AND ( ".$tempvq." ) ";
			}
		}
		
		return $strquery;
	}

	public function get_classified_product_page($product_id) 
	{
		$data['currentDateTime'] = $currentDateTime = date('Y-m-d H:i:s');
		
		$product_query = $this->prepareProductQuery();

		$product = $this->db->query($product_query. ' AND product.product_id='.$product_id)->row_array();

		$refer_id = $this->get_refer_id();

		$data['product'] = $this->prepare_product_data_for_theme($product, $refer_id, [900,530]);

		$categories_of_product = explode(',', $product['categories_of_product']);

		$related_product_queries = "(";

		for ($i=0; $i < sizeof($categories_of_product); $i++) { 
			$related_product_queries .= ' categories_of_product LIKE "%'.$categories_of_product[$i].'%"';
			if($i != (sizeof($categories_of_product) - 1)) {
				$related_product_queries .= ' OR ';
			}
		}

		$related_product_queries .= ")";

		$this->db->set('view', 'view+1', FALSE);
		$this->db->where('product_id', $product_id);
		$this->db->update('product');


		$data['related_products'] = $this->db->query($product_query. ' having '.$related_product_queries)->result_array();

		foreach ($data['related_products'] as &$product) {
			$product = $this->prepare_product_data_for_theme($product, $refer_id);
		}

		echo json_encode($data);
	}

	public function get_classified_catalog_page($page = 1)
	{
		// generate product and pagination data from post data filter
		$catalog_filter_query = "";
		$catalog_having_query = "";
		$catalog_sort_query = " order by product.product_id DESC";

		if(isset($_POST['aff_query_payload'])) {
			$last_filter = json_decode($_POST['aff_query_payload'], true);
		} else {
			$last_filter = [];
		}
		$data['filter'] = $this->get_category_and_location_filter_options($last_filter);

		if(!empty($last_filter)) {
			if(isset($last_filter['aff_filter_country']) && !empty($last_filter['aff_filter_country'])) {

				$states = $this->db->query('SELECT * FROM states WHERE country_id = '.$last_filter['aff_filter_country'])->result_array();

				if(!empty($states)) {
					$statesQuery = "";
					foreach ($states as $state) {
						if(empty($statesQuery)) {
							$statesQuery .= "( product.state_id = ".$state['id']." ";
						} else {
							$statesQuery .= " OR product.state_id = ".$state['id']." ";
						}
					}
					$statesQuery .= ")";

					$catalog_filter_query .= ' AND '.$statesQuery.' ';
				}
			}

			if(isset($last_filter['aff_filter_location']) && !empty($last_filter['aff_filter_location'])) {
				$catalog_filter_query .= ' AND product.state_id = '.$last_filter['aff_filter_location'].' ';
			}
			
			if(isset($last_filter['aff_filter_keyword']) && !empty($last_filter['aff_filter_keyword'])) {
				$catalog_filter_query .= ' AND product.product_name LIKE "%'.$last_filter['aff_filter_keyword'].'%" ';
			}

			if(isset($last_filter['aff_filter_category']) && !empty($last_filter['aff_filter_category'])) {
				$having_filter = true;
				$catalog_having_query .= ' categories_of_product LIKE "%'.$last_filter['aff_filter_category'].'%" ';
			}

			if(isset($last_filter['aff_filter_price_min']) && !empty($last_filter['aff_filter_price_min'])) {
				if($having_filter) {
					$catalog_having_query .= " AND ";
				}
				$having_filter = true;
				$catalog_having_query .= ' a_product_sort_price >= '.$last_filter['aff_filter_price_min'].' ';
			}

			if(isset($last_filter['aff_filter_price_max']) && !empty($last_filter['aff_filter_price_max'])) {
				if($having_filter) {
					$catalog_having_query .= " AND ";
				}
				$having_filter = true;
				$catalog_having_query .= ' a_product_sort_price <='.$last_filter['aff_filter_price_max'].' ';
			}

			if($having_filter) {
				$catalog_having_query = " HAVING ".$catalog_having_query;
			}

			if(isset($last_filter['aff_sort_by']) && !empty($last_filter['aff_sort_by'])) {
				switch ($last_filter['aff_sort_by']) {
					case 1:
						// sort A-Z
					$catalog_sort_query = " order by product.product_name ASC";
					break;
					case 2:
						// sort Z-A
					$catalog_sort_query = " order by product.product_name DESC";
					break;
					case 3:
						// sort new
					$catalog_sort_query = " order by product.product_updated_date DESC";
					break;
					case 4:
						// sort old
					$catalog_sort_query = " order by product.product_updated_date ASC";
					break;
					case 5:
						// sort most view
					$catalog_sort_query = " order by total_views DESC";
					break;
					case 6:
						// sort less view
					$catalog_sort_query = " order by total_views ASC";
					break;
					case 7:
						// sort price low-high
					$catalog_sort_query = " order by a_product_sort_price ASC";
					break;
					case 8:
						// sort price hige-low
					$catalog_sort_query = " order by a_product_sort_price DESC";
					break;
					default:
					$catalog_sort_query = " order by product.product_id DESC";
					break;
				}
			}
		}

		$data['catalog_sort_query']= $catalog_sort_query;


		$items_per_page = 12;
		$current_page = $page;

		$offset = ($current_page - 1) * $items_per_page;

		$queryAppend  = $catalog_filter_query.$catalog_having_query.$catalog_sort_query;

		$product_query	=$this->prepareProductQuery();
		$product_query .= $this->get_filter_for_restricted_vendor();
 

		$data['products'] = $this->db->query($product_query.' '.$queryAppend.' LIMIT '.$items_per_page.' OFFSET '.$offset)->result_array();

		$data['total_products'] = $this->db->query($product_query)->num_rows();

		if(sizeof($data['products']) > 0) {
			$data['results_status'] = "Showing ".($offset + 1)."-".($offset + sizeof($data['products']))." of ".sizeof($data['products'])." results";
		}

		$data['pagination'] = generatePaginationLinks(base_url('store/catalog'), $data['total_products'], $items_per_page, $current_page, $last_filter);

		$refer_id = $this->get_refer_id();

		foreach ($data['products'] as &$product) {
			$product = $this->prepare_product_data_for_theme($product, $refer_id);
		} 

		echo json_encode($data);
	}
public function get_classified_vendor_products($page = 1)
	{

		$this->load->library('session');

		// generate product and pagination data from post data filter
		$catalog_filter_query = "";
		$catalog_having_query = "";
		$catalog_sort_query = " order by product.product_id DESC";

		if(isset($_POST['aff_query_payload'])) {
			$last_filter = json_decode($_POST['aff_query_payload'], true);
		} else {
			$last_filter = [];
		}
		$data['filter'] = $this->get_category_and_location_filter_options($last_filter);

		$usertype=$this->session->userdata['store_information']['type'];

		$vendor_id = $this->session->userdata['store_information']['id'];
 

		if(!empty($last_filter)) {
			if(isset($last_filter['aff_filter_country']) && !empty($last_filter['aff_filter_country'])) {

				$states = $this->db->query('SELECT * FROM states WHERE country_id = '.$last_filter['aff_filter_country'])->result_array();

				if(!empty($states)) {
					$statesQuery = "";
					foreach ($states as $state) {
						if(empty($statesQuery)) {
							$statesQuery .= "( product.state_id = ".$state['id']." ";
						} else {
							$statesQuery .= " OR product.state_id = ".$state['id']." ";
						}
					}
					$statesQuery .= ")";

					$catalog_filter_query .= ' AND '.$statesQuery.' ';
				}
			}

			if(isset($last_filter['aff_filter_location']) && !empty($last_filter['aff_filter_location'])) {
				$catalog_filter_query .= ' AND product.state_id = '.$last_filter['aff_filter_location'].' ';
			}
			
			if(isset($last_filter['aff_filter_keyword']) && !empty($last_filter['aff_filter_keyword'])) {
				$catalog_filter_query .= ' AND product.product_name LIKE "%'.$last_filter['aff_filter_keyword'].'%" ';
			}

			if(isset($last_filter['aff_filter_category']) && !empty($last_filter['aff_filter_category'])) {
				$having_filter = true;
				$catalog_having_query .= ' categories_of_product LIKE "%'.$last_filter['aff_filter_category'].'%" ';
			}

			if(isset($last_filter['aff_filter_price_min']) && !empty($last_filter['aff_filter_price_min'])) {
				if($having_filter) {
					$catalog_having_query .= " AND ";
				}
				$having_filter = true;
				$catalog_having_query .= ' a_product_sort_price >= '.$last_filter['aff_filter_price_min'].' ';
			}

			if(isset($last_filter['aff_filter_price_max']) && !empty($last_filter['aff_filter_price_max'])) {
				if($having_filter) {
					$catalog_having_query .= " AND ";
				}
				$having_filter = true;
				$catalog_having_query .= ' a_product_sort_price <='.$last_filter['aff_filter_price_max'].' ';
			}

			if($having_filter) {
				$catalog_having_query = " HAVING ".$catalog_having_query;
			}

			if(isset($last_filter['aff_sort_by']) && !empty($last_filter['aff_sort_by'])) {
				switch ($last_filter['aff_sort_by']) {
					case 1:
						// sort A-Z
					$catalog_sort_query = " order by product.product_name ASC";
					break;
					case 2:
						// sort Z-A
					$catalog_sort_query = " order by product.product_name DESC";
					break;
					case 3:
						// sort new
					$catalog_sort_query = " order by product.product_updated_date DESC";
					break;
					case 4:
						// sort old
					$catalog_sort_query = " order by product.product_updated_date ASC";
					break;
					case 5:
						// sort most view
					$catalog_sort_query = " order by total_views DESC";
					break;
					case 6:
						// sort less view
					$catalog_sort_query = " order by total_views ASC";
					break;
					case 7:
						// sort price low-high
					$catalog_sort_query = " order by a_product_sort_price ASC";
					break;
					case 8:
						// sort price hige-low
					$catalog_sort_query = " order by a_product_sort_price DESC";
					break;
					default:
					$catalog_sort_query = " order by product.product_id DESC";
					break;
				}
			}
		}

		$data['catalog_sort_query']= $catalog_sort_query;


		$items_per_page = 12;
		$current_page = $page;

		$offset = ($current_page - 1) * $items_per_page;

		$queryAppend  = $catalog_filter_query.$catalog_having_query.$catalog_sort_query;
		if ($vendor_id > 0) 
		{
			if($usertype=="admin")
			{
				$data['products'] = $this->db->query($this->prepareProductQueryForClassifiedAdmin($vendor_id).' '.$queryAppend.' LIMIT '.$items_per_page.' OFFSET '.$offset)->result_array();

				$data['total_products'] = $this->db->query($this->prepareProductQueryForClassifiedAdmin($vendor_id))->num_rows();
			}
			else
			{
				$data['products'] = $this->db->query($this->prepareProductQueryForClassifiedVendor($vendor_id).' '.$queryAppend.' LIMIT '.$items_per_page.' OFFSET '.$offset)->result_array();

				$data['total_products'] = $this->db->query($this->prepareProductQueryForClassifiedVendor($vendor_id))->num_rows();
			}
			

		}else{

			$product_query =$this->prepareProductQuery();
			$product_query .= $this->get_filter_for_restricted_vendor();
			$data['products'] = $this->db->query($product_query.' '.$queryAppend.' LIMIT '.$items_per_page.' OFFSET '.$offset)->result_array();

			$data['total_products'] = $this->db->query($product_query)->num_rows();
		}

		

		if(sizeof($data['products']) > 0) {
			$data['results_status'] = "Showing ".($offset + 1)."-".($offset + sizeof($data['products']))." of ".sizeof($data['products'])." results";
		}

		$data['pagination'] = generatePaginationLinks(base_url('store/catalog'), $data['total_products'], $items_per_page, $current_page, $last_filter);

		$refer_id = $this->get_refer_id();

		foreach ($data['products'] as &$product) {
			$product = $this->prepare_product_data_for_theme($product, $refer_id);
		}
		

		echo json_encode($data);
	}

	public function get_classified_checkout_page() {
		$data = [];
		if(isset((apache_request_headers())['aff-preview-page'])) {
			
		} else {
			$product_id = strip_tags($_GET['id']);
			if((int)$product_id < 0) {
				$product_id = null;
			}
			$product = $this->cart->getProducts($product_id)[0];

			if(!empty($product)){
				$data['product_origin'] = $product;
				
				$data['show_product_form'] = true;
				
				$data['product'] = [];

				$data['product']['product_name'] = $product['product_name'];
				$data['product']['product_featured_image'] = $product['product_featured_image'];
				$data['product']['product_description'] = $product['product_description'];

				$data['product']['product_price'] = c_format($product['product_price']);
				$data['product']['product_msrp'] = c_format($product['product_msrp']);
				$data['product']['is_discount_applied'] = (float)$product['product_msrp'] > (float)$product['product_price'];

				$data['product']['product_total_price'] = c_format((float)$product['total']);

				$totals = $this->cart->getTotals();
				
				if(!empty($totals)){
					foreach ($totals as $key => $value) {
						$data['totals'][] = array(
							'title' => $value['title'],
							'amount' =>  c_format($value['amount']),
						);
					}
				}

				$product_meta = $this->db->get_where('product_meta', array(
					'related_product_id' => $product['product_id']
				))->result_array();

				foreach ($product_meta as $meta) {
					if(! in_array($meta['meta_key'],['checkout_template', 'show_to_affiliates'])) {
						switch ($meta['meta_key']) {
							case 'product_reviews':
							$data['product'][$meta['meta_key']] = json_decode($meta['meta_value']);
							break;
							case 'product_checkout_terms':
							$client_ip = get_client_ip();
							$data['product'][$meta['meta_key']] = str_replace('%%customers_ip_address%%', $client_ip, $meta['meta_value']);
							break;
							case 'product_quantity':
							if((int) $meta['meta_value'] < 1) {
								$data['show_product_form'] = false;
								$data['alert_message'] = "Product you are trying to purchase is out of stock!";
							}
							break;
							case 'product_launching_datetime':
							$launchTime = strtotime($meta['meta_value']);
							if($launchTime > time()) {
								$data['show_product_form'] = false;
								$data['alert_message'] = "Product will be available to purchase on ".date('D d-m-Y H:i A', $launchTime);
							}
							break;
							default:
							$data['product'][$meta['meta_key']] = $meta['meta_value'];
							break;
						}
					}
				}

				$data = array_merge($data, $this->get_checkout_form_options());
			}
		}

		echo json_encode($data);
	}

	public function get_classified_about_page() {
		$this->load->model('Product_model');
		
		$store_setting = $this->Product_model->getSettings('store');

		$data['about_image'] = $store_setting['aboutimage'] ? base_url('assets/images/site/'. $store_setting['aboutimage']) : base_url('assets/store/default/img/about-img.png');
		$data['about_content'] = !empty($store_setting['about_content']) ? $store_setting['about_content'] : __('store.about_us_if_not_exist');
		
		echo json_encode($data);
	}

	public function get_classified_policy_page() {
		$this->load->model('Product_model');
		
		$store_setting = $this->Product_model->getSettings('store');

		$data['policy_image'] = $store_setting['policyimage'] ? base_url('assets/images/site/'. $store_setting['policyimage']) : base_url('assets/store/default/img/about-img.png');

		$data['policy_content'] = !empty($store_setting['policy_content']) ? $store_setting['policy_content'] : __('store.privacy_if_not_exist');

		$data['contact_page_url'] = base_url('classified/contact');

		echo json_encode($data);
	}

	public function get_classified_contact_page()
	{

		$this->load->model('Product_model');
		
		$store_setting = $this->Product_model->getSettings('store');

		$data['contact_page_content'] = $store_setting['contact_content'];

		$data['contact_page_image'] = ($store_setting['contactimage']) ? base_url('assets/images/site/'.$store_setting['contactimage']) : base_url('assets/store/default/img/cn-charact.png');

		$data['contact_number'] = !empty($store_setting['contact_number']) ? $store_setting['contact_number'] : '+00 000 000 0000';

		$data['contact_email'] = !empty($store_setting['email']) ? $store_setting['email'] : 'lorem@lorem.com';

		$data['contact_address'] = !empty($store_setting['address']) ? $store_setting['address'] : 'Keas 69 Str. 15234, Chalandri Athens, Greece';

		$data['contact_page_map'] = !empty($store_setting['contact_us_map']) ? $store_setting['contact_us_map'] : '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d55565170.29301636!2d-132.08532758867793!3d31.786060306224!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x54eab584e432360b%3A0x1c3bb99243deb742!2sUnited%20States!5e0!3m2!1sen!2sph!4v1592929054111!5m2!1sen!2sph";" width="600" height="450" allowfullscreen="" aria-hidden="false" tabindex="0"></iframe>';
		
		echo json_encode($data);
	}

	public function prepare_product_data_for_theme($product, $refer_id) 
	{
		
		if(!empty($product['product_sale_start']) && !empty($product['product_sale_end']) && !empty($product['product_sale_price']))
		 {
			$start_time = strtotime(trim($product['product_sale_start']));
			$end_time = strtotime(trim($product['product_sale_end']));
			$time = time();

			if($start_time < $time && $end_time > $time) {
				$product['product_sale_is_on'] = true;
				$product['product_price'] = trim($product['product_sale_price']);
			}
		}



		$product['is_discount_applied'] = (float)$product['product_msrp'] > (float)$product['product_price'];
		
		
		
		if($product['product_msrp'] >0)
		$product['discount_percentage'] = round((((float)$product['product_msrp'] - (float)$product['product_price']) / $product['product_msrp']) * 100);
		else
			$product['discount_percentage'] = round((float)($product['product_price']/ 100));
 

		$product['product_price'] = c_format($product['product_price']);


		$product['product_msrp'] = c_format($product['product_msrp']);

		$product['total_sale'] = !empty($product['total_sale']) ? $product['total_sale'] : 0;

		$product['total_views'] = !empty($product['total_views']) ? $product['total_views'] : 0;

		$product['view'] = !empty($product['view']) ? $product['view'] : 0;


		$product['product_featured_image'] = (!empty($product['product_featured_image'])) ? base_url('assets/images/product/upload/thumb/'. $product['product_featured_image']) : base_url('assets/store/default/').'img/product1.png';

		$product['product_url'] = addParams($product['product_url'], "af_id", _encrypt_decrypt($refer_id."-".$product['product_id']));
		// $product['product_url'] = addParams($product['product_url'], "af_id",$refer_id);

		$product['product_details_url'] = base_url('store/product/'.$product['product_id']);

		$product['product_launch_date'] = date('D d-m-Y h:i A', strtotime($product['product_launch_date']));

		$product['product_updated_date'] = date('D d-m-Y h:i A', strtotime($product['product_updated_date']));

		$product['product_created_date'] = date('D d-m-Y h:i A', strtotime($product['product_created_date']));

		$category = $this->db->query('
			SELECT 
			category_id, categories.*
			FROM 
			product_categories
			LEFT JOIN 
			categories ON categories.id = product_categories.category_id
			WHERE
			product_categories.product_id ="'.$product['product_id'].'"
			ORDER BY categories.parent_id DESC limit 1
			')->row_array();

		$product['product_category'] = $category['name'];

		$product['product_description_shorted'] = strlen($product['product_description']) > 100 ? substr($product['product_description'],0,100)."..." : $product['product_description'];
		
		return $product;
	}

public function prepare_category_data_for_theme($category) {
    if (!empty($category['background_image'])) {
        $category['background_image'] = base_url('assets/images/product/upload/thumb/').$category['background_image'];
    } else {
        $category['background_image'] = base_url('assets/images/no_image_available.png');
    }

    if (!empty($category['image'])) {
        $category['image'] = base_url('assets/images/product/upload/thumb/'.$category['image']);
    } else {
        $category['image'] = base_url('assets/images/no_image_available.png');
    }

    return $category;
}

	public function get_refer_id() {
		$this->load->model('Product_model');
		$this->load->model('Cart');
		$site_setting = $this->Product_model->getSettings('site');
		$cookie_user_id = $localstorage_user_id = 0;
		if(isset($site_setting['affiliate_tracking_place']) && ($site_setting['affiliate_tracking_place'] == 1 || $site_setting['affiliate_tracking_place'] == 2)) {
			$localstorage_user_id = $this->session->localStorageAffiliate;
		}
		if(! isset($site_setting['affiliate_tracking_place']) || $site_setting['affiliate_tracking_place'] == 0 || $site_setting['affiliate_tracking_place'] == 2) {
			$cookie_user_id = (isset($_COOKIE['af_id']) && !empty($_COOKIE['af_id'])) ? $_COOKIE['af_id']:0;
			if($cookie_user_id !==0) {
				list($cookie_user_id,$click_product_id) = explode("-", _encrypt_decrypt(parse_affiliate_id($cookie_user_id),'decrypt'));
				return $cookie_user_id;          
			}
		} 
		return $localstorage_user_id == 1 ? $cookie_user_id : $localstorage_user_id;
	}

	public function get_checkout_countries()
	{
		echo "<option selected disabled value=''>Select Country</option>";

		$countries_sql = 'SELECT * FROM countries WHERE 1';
		
		$country = $this->Product_model->getShippingCountry();
		
		if(is_array($country)){
			if(count($country) == 0){
				$countries_sql .= ' AND id IN (0) ';
			} else {
				$countries_sql .= ' AND id IN ('. implode(",", array_keys($country)) .') ';
			}
		}
		
		$countries = $this->db->query($countries_sql)->result();

		foreach ($countries as $key => $value) {
			echo '<option value="'.$value->id.'">'.$value->name.'</option>';
		}	
	}

	public function get_checkout_states($country = null)
	{
		echo "<option selected disabled value=''>Select State</option>";

		if((int)$country > 0){
			$states = $this->db->query('SELECT name,id FROM states WHERE country_id = '. $country)->result();
			foreach ($states as $key => $value) {
				echo '<option value="'.$value->id.'">'.$value->name.'</option>';
			}
		}
	}

	public function get_checkout_form_options()
	{
		$data = [];

		if(isset((apache_request_headers())['aff-preview-page'])) {
			
		} else {
			$this->cart->reloadCart();
			$data['allow_shipping'] = $this->cart->allow_shipping;


			$files = array();
			foreach (glob(APPPATH."/payment_gateway/controllers/*.php") as $file)
				$files[] = $file;

			$allPaymentGateways = array_unique($files);
			$activePaymentGateways = [];
			$defaultPaymntGateway = [];
			foreach($allPaymentGateways as $key => $filename){
				$paymentGateway = basename($filename,".php");

				$result = $this->Product_model->getSettings('payment_gateway_store_'.$paymentGateway,'status');
				$install = $this->Product_model->getSettings('payment_gateway_'.$paymentGateway,'is_install');
				if(isset($result['status']) && $result['status'] && $install['is_install']){
					require $filename;

					$object = new $paymentGateway($this);

					$activePaymentGateways[$paymentGateway] = $this->Product_model->getSettings('payment_gateway_'.$paymentGateway);
					
					$activePaymentGateways[$paymentGateway]['title'] = $object->title;
					$activePaymentGateways[$paymentGateway]['icon'] = $object->icon;
					$activePaymentGateways[$paymentGateway]['name']  = $paymentGateway;
					
					
					$where = array('setting_key'=>'status','setting_type'=>'payment_gateway_store_'.$paymentGateway,'setting_is_default'=>1);

					$this->load->model('Common_model');
					$is_default = $this->Common_model->get_total_rows('setting',$where);
					if($is_default){
						$defaultPaymntGateway[$paymentGateway] = $activePaymentGateways[$paymentGateway];
						$defaultPaymntGateway[$paymentGateway]['active']  = true;
						unset($activePaymentGateways[$paymentGateway]);
					}
				}
			}

			$payment_gateways = array_merge($defaultPaymntGateway,$activePaymentGateways);

			foreach ($payment_gateways as $key => $value) {
				$value['display_name'] = ucwords(strtolower(str_replace('-', ' ', str_replace('_', ' ', $key))));
				$value['icon'] = base_url($value['icon']);
				$session_payment_gateways[$key] = $value;
				$data['payment_gateways'][] = $value;
			}
			
			$this->session->set_userdata('payment_gateways', $session_payment_gateways);
		}

		return $data;
	}

	public function get_category_and_location_filter_options($filter=null) {
		$filter_data = [];
 
		$categoriesFilter ="";
		if(isset($filter['aff_filter_category']) &&  !empty($filter['aff_filter_category'])) {
			$categoriesFilter =" AND categories.id=".strip_tags($filter['aff_filter_category']);
		}

		// prepare nested categories list
		$parent_categories = $this->db->query("
			SELECT 
			categories.*,
			(select count(*) 
				from product_categories
				left join product on product.product_id = product_categories.product_id 
				INNER JOIN users ON users.id = product.product_created_by 
				where 
				product_categories.category_id = categories.id AND
				product.is_campaign_product = 1 $categoriesFilter
				) as product_count 
			FROM
			categories
			WHERE
			categories.parent_id = 0 $categoriesFilter")->result_array();
		
		$new_parent_categories = [];

		foreach ($parent_categories as $pp) {
			$new_parent_categories['c'.$pp['id']] = $this->prepare_category_data_for_theme($pp);
		}

 

		$child_categories = $this->db->query("
			SELECT 
			categories.*,
			(select count(*) 
				from product_categories
				left join product on product.product_id = product_categories.product_id 
				INNER JOIN users ON users.id = product.product_created_by
				where 

				product_categories.category_id = categories.id AND
				product.is_campaign_product = 1 $categoriesFilter
				) as product_count 
			FROM
			categories

			WHERE
			categories.parent_id > 0 $categoriesFilter")->result_array();

		foreach ($child_categories as $cc) {
			if(isset($new_parent_categories['c'.$cc['parent_id']])) {

				if(!isset($new_parent_categories['c'.$cc['parent_id']]['childs'])) {
					$new_parent_categories['c'.$cc['parent_id']]['childs'] = [];
					$new_parent_categories['c'.$cc['parent_id']]['catalog_filter_url'] = base_url('store/catalog/category/'.$cc['parent_id']);
				}

				$cc['catalog_filter_url'] = base_url('store/catalog/category/'.$cc['id']);

				$new_parent_categories['c'.$cc['parent_id']]['childs'][] = $this->prepare_category_data_for_theme($cc);
			}
		}

		$filter_data['categories_filter'] = [];

		foreach ($new_parent_categories as $categories) {
			if(! isset($filter_data['categories_filter']['data'])) {
				$filter_data['categories_filter']['status'] = true;
				$filter_data['categories_filter']['data'] = [];
			}
			$filter_data['categories_filter']['data'][] = $categories;
		}


		// prepare nested country -> states list
		$product_states_id = $this->db->query("
			SELECT 
			GROUP_CONCAT(state_id SEPARATOR ',') as product_states
			FROM 
			product 
			WHERE 
			state_id > 0 AND product_status = 1 AND is_campaign_product = 1 AND on_store = 1 
			GROUP BY on_store
			")->row_array()['product_states'];

		$countries = $this->db->query('SELECT * FROM countries')->result_array();

		if(!empty($product_states_id)) {
			$states = $this->db->query('
				SELECT 
				states.*,
				(select count(*) from product where product.state_id = states.id) as product_count 
				FROM 
				states 
				WHERE 
				states.id IN ('.$product_states_id.')'
			)->result_array();
		} else {
			$states = [];
		}
		

			$new_contries = [];

			foreach ($countries as $country) {
				$new_contries['c'.$country['id']] = $this->prepare_category_data_for_theme($country);
			}

			foreach ($states as $state) {
				if(isset($new_contries['c'.$state['country_id']])) {
					if(!isset($new_contries['c'.$state['country_id']]['states'])) {
						$new_contries['c'.$state['country_id']]['product_count'] = 0;
						$new_contries['c'.$state['country_id']]['states'] = [];
					}
					$new_contries['c'.$state['country_id']]['product_count'] += $state['product_count'];
					$state['catalog_filter_url'] = base_url('store/catalog/location/'.$state['id']);
					$new_contries['c'.$state['country_id']]['states'][] = $state;
				}
			}

			$filter_data['location_filter'] = [];

			foreach ($new_contries as $country) {
				if(isset($country['states']) && ! empty($country['states'])) {
					if(! isset($filter_data['location_filter']['data'])) {
						$filter_data['location_filter']['status'] = true;
						$filter_data['location_filter']['data'] = [];
					}
					$filter_data['location_filter']['data'][] = $country;
				}
			}

			return $filter_data;
		}

		public function get_category_and_location_filter_options_all() {
			$filter_data = [];
			$countyFilterSql= "";
			$categoryFilterSql= "";
			$ProductStates =$this->db->query("SELECT GROUP_CONCAT(state_id) as ids, GROUP_CONCAT(product_id) as productIds from product where product.product_status = 1 AND product.is_campaign_product = 1 AND product.on_store = 1")->row_array();
			if(!empty($ProductStates['ids'])) {
				$ProductcountyIds =$this->db->query("SELECT GROUP_CONCAT(country_id) as ids from states where id IN(".$ProductStates['ids'].")")->row_array();
					if(!empty($ProductcountyIds['ids'])) 
						$countyFilterSql = " WHERE id IN(".$ProductcountyIds['ids'].")";
				}
				if(!empty($ProductStates['productIds'])) {
					$ProductcateIds =$this->db->query("SELECT GROUP_CONCAT(DISTINCT category_id) as ids from product_categories where product_id IN(".$ProductStates['productIds'].")")->row_array();
						if(!empty($ProductcateIds['ids'])) 
							$categoryFilterSql = " WHERE id IN(".$ProductcateIds['ids'].")";
					}

					$filter_data['categories'] = $this->db->query("SELECT * FROM categories $categoryFilterSql")->result_array();
					$filter_data['countries'] = $this->db->query("SELECT * FROM countries $countyFilterSql")->result_array();
					return $filter_data;
				}
 
				private function prepareProductQuery(){
					$product_query = '
					SELECT 
					product.product_id, product.is_campaign_product, product.product_url, product.product_name, product.product_description,
					product.product_short_description, product.product_msrp, product.product_price, product.product_sku, product.product_slug,
					product.product_featured_image, product.product_status, product.product_created_date, product.product_updated_date, product.product_created_by,to_base64(product.product_created_by) as product_created_by_base64 , product.on_store, product.state_id,
					product.view,
					STR_TO_DATE(product_meta_ss.meta_value, "%Y-%m-%d %H:%i:%s") as product_sale_start,
					STR_TO_DATE(product_meta_se.meta_value, "%Y-%m-%d %H:%i:%s") as product_sale_end,
					product_meta_sp.meta_value as product_sale_price,
					product_meta_fp.meta_value as show_to_featured,
					STR_TO_DATE(product_meta_ld.meta_value, "%Y-%m-%d %H:%i:%s") as product_launch_date,
					(select sum(product_action.counter) from product_action WHERE product_action.product_id = product.product_id) as total_views,
					(select sum(order_products.quantity) from order_products WHERE order_products.product_id = product.product_id) as total_sale,
					(SELECT GROUP_CONCAT(product_categories.category_id SEPARATOR ",") FROM product_categories WHERE product_categories.product_id = product.product_id) as categories_of_product,
					(SELECT IF(product_sale_start IS NOT NULL AND product_sale_start < "current_date" AND product_sale_end IS NOT NULL AND product_sale_end > "current_date", CONVERT(product_sale_price, SIGNED INTEGER),CONVERT(product.product_price, SIGNED INTEGER)) ) as a_product_sort_price,
					CONCAT(users.firstname, " ", users.lastname) AS product_created_by_name
					FROM
					product
					LEFT JOIN 
					product_meta as product_meta_ss 
					ON product_meta_ss.related_product_id = product.product_id AND product_meta_ss.meta_key = "product_sale_start"
					LEFT JOIN 
					product_meta as product_meta_se 
					ON product_meta_se.related_product_id = product.product_id AND product_meta_se.meta_key = "product_sale_end"
					LEFT JOIN 
					product_meta as product_meta_sp 
					ON product_meta_sp.related_product_id = product.product_id AND product_meta_sp.meta_key = "product_sale_price"
					LEFT JOIN 
					product_meta as product_meta_fp 
					ON product_meta_fp.related_product_id = product.product_id AND product_meta_fp.meta_key = "show_to_featured"
					LEFT JOIN 
					product_meta as product_meta_ld 
					ON product_meta_ld.related_product_id = product.product_id AND product_meta_ld.meta_key = "product_launching_datetime"
					INNER JOIN users ON users.id = product.product_created_by
					WHERE 
					product.product_status = 1 AND product.is_campaign_product = 1 AND product.on_store = 1
					';

					$currentDateTime = date('Y-m-d H:i:s');

					return  preg_replace('/current_date/i', $currentDateTime, $product_query);
				}
				private function prepareProductQueryForClassifiedVendor($vendor_id){
					$product_query = '
					SELECT 
					product.product_id, product.is_campaign_product, product.product_url, product.product_name, product.product_description,
					product.product_short_description, product.product_msrp, product.product_price, product.product_sku, product.product_slug,
					product.product_featured_image, product.product_status, product.product_created_date, product.product_updated_date, product.product_created_by,to_base64(product.product_created_by) as product_created_by_base64 , product.on_store, product.state_id,
					product.view, product_affiliate_pp.user_id,
					STR_TO_DATE(product_meta_ss.meta_value, "%Y-%m-%d %H:%i:%s") as product_sale_start,
					STR_TO_DATE(product_meta_se.meta_value, "%Y-%m-%d %H:%i:%s") as product_sale_end,
					product_meta_sp.meta_value as product_sale_price,
					product_meta_fp.meta_value as show_to_featured,
					STR_TO_DATE(product_meta_ld.meta_value, "%Y-%m-%d %H:%i:%s") as product_launch_date,
					(select sum(product_action.counter) from product_action WHERE product_action.product_id = product.product_id) as total_views,
					(select sum(order_products.quantity) from order_products WHERE order_products.product_id = product.product_id) as total_sale,
					(SELECT GROUP_CONCAT(product_categories.category_id SEPARATOR ",") FROM product_categories WHERE product_categories.product_id = product.product_id) as categories_of_product,
					(SELECT IF(product_sale_start IS NOT NULL AND product_sale_start < "current_date" AND product_sale_end IS NOT NULL AND product_sale_end > "current_date", CONVERT(product_sale_price, SIGNED INTEGER),CONVERT(product.product_price, SIGNED INTEGER)) ) as a_product_sort_price,
					CONCAT(users.firstname, " ", users.lastname) AS product_created_by_name
					FROM
					product
					LEFT JOIN
					product_affiliate as product_affiliate_pp
					ON product_affiliate_pp.product_id = product.product_id
					LEFT JOIN
					product_meta as product_meta_ss 
					ON product_meta_ss.related_product_id = product.product_id AND product_meta_ss.meta_key = "product_sale_start"
					LEFT JOIN 
					product_meta as product_meta_se 
					ON product_meta_se.related_product_id = product.product_id AND product_meta_se.meta_key = "product_sale_end"
					LEFT JOIN 
					product_meta as product_meta_sp 
					ON product_meta_sp.related_product_id = product.product_id AND product_meta_sp.meta_key = "product_sale_price"
					LEFT JOIN 
					product_meta as product_meta_fp 
					ON product_meta_fp.related_product_id = product.product_id AND product_meta_fp.meta_key = "show_to_featured"
					LEFT JOIN 
					product_meta as product_meta_ld 
					ON product_meta_ld.related_product_id = product.product_id AND product_meta_ld.meta_key = "product_launching_datetime"
					LEFT JOIN users ON users.id = product.product_created_by
					WHERE 
					product.product_status = 1 AND product.is_campaign_product = 1 AND product.on_store = 1 AND product_affiliate_pp.user_id = '.$vendor_id.'
					';

					$currentDateTime = date('Y-m-d H:i:s');

					return  preg_replace('/current_date/i', $currentDateTime, $product_query);
				}

				private function prepareProductQueryForClassifiedAdmin($vendor_id){
					$product_query = '
					SELECT 
					product.product_id, product.is_campaign_product, product.product_url, product.product_name, product.product_description,
					product.product_short_description, product.product_msrp, product.product_price, product.product_sku, product.product_slug,
					product.product_featured_image, product.product_status, product.product_created_date, product.product_updated_date, product.product_created_by, to_base64(product.product_created_by) as product_created_by_base64 , product.on_store, product.state_id,
					product.view, product_affiliate_pp.user_id,
					STR_TO_DATE(product_meta_ss.meta_value, "%Y-%m-%d %H:%i:%s") as product_sale_start,
					STR_TO_DATE(product_meta_se.meta_value, "%Y-%m-%d %H:%i:%s") as product_sale_end,
					product_meta_sp.meta_value as product_sale_price,
					product_meta_fp.meta_value as show_to_featured,
					STR_TO_DATE(product_meta_ld.meta_value, "%Y-%m-%d %H:%i:%s") as product_launch_date,
					(select sum(product_action.counter) from product_action WHERE product_action.product_id = product.product_id) as total_views,
					(select sum(order_products.quantity) from order_products WHERE order_products.product_id = product.product_id) as total_sale,
					(SELECT GROUP_CONCAT(product_categories.category_id SEPARATOR ",") FROM product_categories WHERE product_categories.product_id = product.product_id) as categories_of_product,
					(SELECT IF(product_sale_start IS NOT NULL AND product_sale_start < "current_date" AND product_sale_end IS NOT NULL AND product_sale_end > "current_date", CONVERT(product_sale_price, SIGNED INTEGER),CONVERT(product.product_price, SIGNED INTEGER)) ) as a_product_sort_price,
					CONCAT(users.firstname, " ", users.lastname) AS product_created_by_name
					FROM
					product
					LEFT JOIN
					product_affiliate as product_affiliate_pp
					ON product_affiliate_pp.product_id = product.product_id
					LEFT JOIN
					product_meta as product_meta_ss 
					ON product_meta_ss.related_product_id = product.product_id AND product_meta_ss.meta_key = "product_sale_start"
					LEFT JOIN 
					product_meta as product_meta_se 
					ON product_meta_se.related_product_id = product.product_id AND product_meta_se.meta_key = "product_sale_end"
					LEFT JOIN 
					product_meta as product_meta_sp 
					ON product_meta_sp.related_product_id = product.product_id AND product_meta_sp.meta_key = "product_sale_price"
					LEFT JOIN 
					product_meta as product_meta_fp 
					ON product_meta_fp.related_product_id = product.product_id AND product_meta_fp.meta_key = "show_to_featured"
					LEFT JOIN 
					product_meta as product_meta_ld 
					ON product_meta_ld.related_product_id = product.product_id AND product_meta_ld.meta_key = "product_launching_datetime"
					LEFT JOIN users ON users.id = product.product_created_by
					WHERE 
					product.product_status = 1 AND product.is_campaign_product = 1 AND product.on_store = 1 AND product.product_created_by = '.$vendor_id.'
					';

					$currentDateTime = date('Y-m-d H:i:s');

					return  preg_replace('/current_date/i', $currentDateTime, $product_query);
				}

			}