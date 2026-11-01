<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
 
class Sitemap extends MY_Controller {
	function __construct(){
	parent::__construct();
	$this->load->model('user_model', 'user');
	$this->load->model('Product_model');
	$this->load->model('Setting_model');
	$this->load->model('Common_model'); 
	___construct(1);
	}

	public function index()
	{
		$active_theme= $this->Product_model->getSettings("login","front_template");
		$data=$this->getStoreProduct(); 
	 	$data['categorys']=$this->db->query("SELECT * FROM categories")->result();
	 	$data['active_theme']=$active_theme['front_template']; 
	 	$data['store_footer_menu']= $this->Product_model->getSettings("store","footer_menu");
	 	$data['store_custom_page']= $this->Product_model->getSettings("store","custom_page");
	 	$data['theme_pages']=$this->getThemePages();  
	 	echo ($this->load->view('sitemap', $data, true)); 
	}	
 
	public function getThemePages()
	{
		$query = $this->db->where('status', 1)->order_by('position', 'ASC')->get('theme_pages');
		return $query->result();
	}

	public function getStoreProduct()
	{
		$sql = "
			SELECT p.*, c.sortname AS country_code, s.name AS state_name, c.name AS country_name, pc.category_id AS p_catecategory_id
			FROM product p
			LEFT JOIN product_affiliate pa ON pa.product_id = p.product_id
			LEFT JOIN users as seller ON pa.user_id = seller.id
			LEFT JOIN states s ON s.id = p.state_id
			LEFT JOIN countries c ON c.id = s.country_id
			LEFT JOIN product_categories pc ON pc.product_id = p.product_id
			WHERE   product_status = 1 
			";

			$store = $this->Product_model->getSettings('store');

			if((int)$store['status'] == 0){
				$sql .= " AND 1!=1 ";
			}
			else
				$sql .= " AND on_store = 1 AND (seller.is_vendor = 1 OR seller.type IS NULL) AND ( pa.id IS NULL OR seller.id > 0) ";
 
			$store_setting = $this->Product_model->getSettings('store', 'store_mode');
			if($store_setting['store_mode'] == 'sales')
				$sql .= " AND ( is_campaign_product=1 )";
			else
				$sql .= " AND ( is_campaign_product=0 )";

 
			$restricted_vendors = $this->get_restricted_vendors(); 
			if (isset($restricted_vendors) && !empty($restricted_vendors)) {
				$tempvq = "";

				foreach ($restricted_vendors as $vid) {
					if($tempvq != "") {
						$tempvq .= " AND (seller.id IS NULL OR seller.id != ".(int)$vid.") ";
					} else {
						$tempvq .= " (seller.id IS NULL OR seller.id != ".(int)$vid.") ";
					}
				}

				if($tempvq != "") {
					$sql .= " AND ( ".$tempvq." ) ";
				}
			}	
  
		$query = $this->db->query($sql);
		$result['products']=$query->result(); 
		$result['store_mode']=$store_setting['store_mode']; 
		return $result;
	}
}
