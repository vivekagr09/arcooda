<?php
class Product_model extends MY_Model{
    function create_data($table, $details){
        $this->db->insert($table, $details);
        return $this->db->insert_id();
    }

    function create_batch_data($table, $details){
        $this->db->insert_batch($table, $details);
    }

    public function getHtml($file,$data = []){
        return $this->load->view($file,$data,true);
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

    public function get_wishlist_products($data,$isCart=null) {
        $where = "";
        if(isset($data))
        {
           for ($i=0; $i < sizeOf($data); $i++) { 
                if(!empty($data[$i])) {
                    $where .= ($where == "") ? "WHERE (product_id= ".$data[$i]." " : "OR product_id= ".$data[$i]." "; 
                }
            } 
        }
        

        if($where == "") {
            return [];
        } else {
            $where.=")";
            $is_campaign_product  = $isCart == 1 ? " AND is_campaign_product = 1" : "AND is_campaign_product = 0";

            return $this->db->query("SELECT product_id,product_name,product_slug,product_featured_image FROM product $where $is_campaign_product")->result_array();
        }
    }

    public function duplicateProduct($product_id, $status_review = false){
        $product = $this->db->query("SELECT * FROM product WHERE product_id=". (int)$product_id)->row_array();
        
        $product['product_name'] = $product['product_name'] ." - Duplicate";
        $product['product_created_date'] =  date("Y-m-d H:i:s");
        unset($product['product_id']);

        if($status_review){
            $product['product_status'] = 0;
        }
        
        $this->db->insert("product", $product);
        $new_product_id = $this->db->insert_id();

        $seofilename = $this->friendly_seo_string($product['product_name']);
        $product_slug = $seofilename.'-'.$new_product_id;
        $this->db->query("UPDATE product SET product_slug='". $product_slug ."' WHERE product_id=". (int)$new_product_id);


        // Duplicate product category
        $product_categories = $this->db->query("SELECT * FROM product_categories WHERE product_id=". (int)$product_id)->result_array();
        foreach ($product_categories as $key => $category) {
            unset($category['id']);
            $category['product_id'] = $new_product_id;
            $this->db->insert("product_categories", $category);
        }

        // Duplicate product product media data
        $product_media_upload = $this->db->query("SELECT * FROM product_media_upload WHERE product_id=". (int)$product_id)->result_array();
        foreach ($product_media_upload as $key => $media) {
            unset($media['product_media_upload_id']);
            $media['product_id'] = $new_product_id;
            $this->db->insert("product_media_upload", $media);
        }

        // Duplicate product seller data
        $product_affiliate = $this->db->query("SELECT * FROM product_affiliate WHERE product_id=". (int)$product_id)->result_array();
        foreach ($product_affiliate as $key => $seller) {
            unset($seller['id']);
            $seller['product_id'] = $new_product_id;
            $this->db->insert("product_affiliate", $seller);
        }

        $notificationData = array(
            'notification_url'          => 'updateproduct/'.$new_product_id,
            'notification_type'         =>  'vendor_product',
            'notification_title'        =>  __('admin.new_product_added_by_vendor'),
            'notification_viewfor'      =>  'admin',
            'notification_actionID'     =>  $product_id,
            'notification_description'  =>  $product['product_name'].' product is addded by '. $userdetails['username'] .' in store on '.date('Y-m-d H:i:s'),
            'notification_is_read'      =>  '0',
            'notification_created_date' =>  date('Y-m-d H:i:s'),
            'notification_ipaddress'    =>  $_SERVER['REMOTE_ADDR']
        );

        $this->db->insert('notification', $notificationData);
        
    }
    public function calcVendorCommission($data){
        $json = [];
        $product_price = (float)$data['product_price'];
        $vendor_setting = $this->getSettings('vendor');
        $admin_sale_com = $affiliate_sale_com = 0;


        if($data['admin_sale_commission_type'] == 'default'){
            $admin_sale_com = 0;
            if($vendor_setting['admin_sale_commission_type'] == 'percentage'){
                $admin_sale_com = ($product_price * (float)$vendor_setting['admin_commission_value']) / 100;
            } else {
                $admin_sale_com = (float)$vendor_setting['admin_commission_value'];
            }
        } else if($data['admin_sale_commission_type'] == 'percentage'){
            $admin_sale_com = ($product_price * (float)$data['admin_commission_value']) / 100;
        } else if($data['admin_sale_commission_type'] == 'fixed'){
            $admin_sale_com = (float)$data['admin_commission_value'];
        }

        if($data['affiliate_sale_commission_type'] == 'default')
        {
            
            if(isset($data['user_id']) && $data['user_id']>0)
            {
                $seller_setting = $this->db->query("SELECT vs.* FROM vendor_setting vs WHERE vs.user_id=". (int)$data['user_id'] ." ")->row();
            } 
            else if(isset($data['product_id']) && $data['product_id']>0){
               
                $seller_setting = $this->db->query("SELECT vs.* FROM vendor_setting vs LEFT JOIN product_affiliate pa ON (pa.user_id = vs.user_id) WHERE pa.product_id=". (int)$data['product_id'] ." ")->row();
            }
            else {
                  
                $seller_setting = $this->db->query("SELECT * FROM vendor_setting")->row();
            }
 
            $affiliate_sale_com = 0;
            if($seller_setting->affiliate_sale_commission_type == 'percentage'){
                $affiliate_sale_com = ($product_price * (float)$seller_setting->affiliate_commission_value) / 100;
            } else {
                $affiliate_sale_com = (float)$seller_setting->affiliate_commission_value;
            }
        } else if($data['affiliate_sale_commission_type'] == 'percentage')
        {
            
            $affiliate_sale_com = ($product_price * (float)$data['affiliate_commission_value']) / 100;
           
        } else if($data['affiliate_sale_commission_type'] == 'fixed'){
            $affiliate_sale_com = (float)$data['affiliate_commission_value'];
        }

        $vendor_commission = ($product_price - $admin_sale_com - $affiliate_sale_com);

        return [
            'vendor_commission'  => round($vendor_commission,2),
            'admin_sale_com'     => round($admin_sale_com,2),
            'affiliate_sale_com' => round($affiliate_sale_com,2),
        ];
    }
    public function assignToSeller($product_id, $product, $user_id, $admin_comment, $comment_from = 'affiliate', $comm = array()){
        $data = [
            'product_id' => (int)$product_id,
            'user_id' => (int)$user_id,
            'comment' => '[]',
        ];

        $check = $this->db->query("SELECT * FROM product_affiliate WHERE product_id=". (int)$product_id ." ")->row();
        if($comment_from == 'admin'){
            unset($data['user_id']);
        } else {
            if($check && $check->user_id != $user_id){
                return false;
            }
        }

        if($comment_from == 'admin'){
            $data['admin_sale_commission_type']  = $comm['admin_sale_commission_type'];
            $data['admin_commission_value']      = $comm['admin_commission_value'];
            $data['admin_click_commission_type'] = $comm['admin_click_commission_type'];
            $data['admin_click_amount']          = $comm['admin_click_amount'];
            $data['admin_click_count']           = $comm['admin_click_count'];
        } else {
            $data['affiliate_click_commission_type'] = $comm['affiliate_click_commission_type'];
            $data['affiliate_click_count']           = $comm['affiliate_click_count'];
            $data['affiliate_click_amount']          = $comm['affiliate_click_amount'];
            $data['affiliate_sale_commission_type']  = $comm['affiliate_sale_commission_type'];
            $data['affiliate_commission_value']      = $comm['affiliate_commission_value'];
        }

        if($check){
            if($admin_comment){
                $c = json_decode($check->comment,1);
                $c[] = [
                    'from'    => $comment_from,
                    'comment' => $admin_comment,
                ];

                $check->comment = json_encode($c);
            }
            $data['comment'] = $check->comment;
            $this->db->update('product_affiliate', $data, ['id' => $check->id]);
        } else {
            if($comment_from = 'affiliate'){
                $this->create_data('product_affiliate', $data);
            }
        }
    }
    
    public function assignToSellerForce($product_id, $product, $user_id, $admin_comment, $comment_from = 'affiliate', $comm = array()){
        $data = [
            'product_id' => (int)$product_id,
            'user_id' => (int)$user_id,
            'comment' => '[]',
        ];

        $check = $this->db->query("SELECT * FROM product_affiliate WHERE product_id=". (int)$product_id ." ")->row();

        $data['admin_sale_commission_type']  = $comm['admin_sale_commission_type'];
        $data['admin_commission_value']      = $comm['admin_commission_value'];
        $data['admin_click_commission_type'] = $comm['admin_click_commission_type'];
        $data['admin_click_amount']          = $comm['admin_click_amount'];
        $data['admin_click_count']           = $comm['admin_click_count'];
        $data['affiliate_click_commission_type'] = $comm['affiliate_click_commission_type'];
        $data['affiliate_click_count']           = $comm['affiliate_click_count'];
        $data['affiliate_click_amount']          = $comm['affiliate_click_amount'];
        $data['affiliate_sale_commission_type']  = $comm['affiliate_sale_commission_type'];
        $data['affiliate_commission_value']      = $comm['affiliate_commission_value'];

        if($check){
            $this->db->update('product_affiliate', $data, ['id' => $check->id]);
        } else {
            $this->create_data('product_affiliate', $data);
        }
    }
    
    public function getShippingCountry(){
        $shipping_setting = $this->Product_model->getSettings('shipping_setting');
        $cost = (array)(isset($shipping_setting['cost']) ? json_decode($shipping_setting['cost'],1) : []);

        if((int)$shipping_setting['shipping_in_limited'] == 1){
            $_cost = [];
            foreach ($cost as $key => $value) {
                $_cost[$value['country']] = $value['cost'];
            }

            return $_cost;
        }

        return 'all';
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
            $data = array('success' => true, 'upload_data' => $upload_details, 'msg' => "Upload success!");
        }
        return $data;
    }
    public function getShippingRate($countryId){
        $shipping_setting = $this->Product_model->getSettings('shipping_setting');
        $cost = (array)(isset($shipping_setting['cost']) ? json_decode($shipping_setting['cost'],1) : []);

        $_cost = [];
        foreach ($cost as $key => $value) {
            $_cost[$value['country']] = $value['cost'];
        }

        if(isset($_cost[(int)$countryId])){
            return $_cost[(int)$countryId];
        }

        return 0;
    }

    public function getTaxRate($countryId){
        $tax = 0;

        $tax_setting = $this->Product_model->getSettings('tax_setting');

        switch ((int)$tax_setting['tax_status']) {
            case 1:
            $tax = (int)$tax_setting['common_tax_percentage'];
            break;
            case 2:
            $cost = (array)(isset($tax_setting['cost']) ? json_decode($tax_setting['cost'],1) : []);
            foreach ($cost as $key => $value) {
                if($countryId == $value['country']) {
                    $tax = (int)$value['cost'];
                    break;
                }
            }
            break;
        }

        return $tax;
    }

    public function insertOrDelete($data,$where){
        $this->db->delete('order_proof', $where);
        $this->db->insert('order_proof', $data);
    }
    public function getProductCategory($product_id){
        return $this->db->query("SELECT pc.product_id,c.* FROM product_categories pc LEFT JOIN categories c ON c.id = pc.category_id WHERE pc.product_id = {$product_id}")->result_array();
    }
    private function buildTreeForCategory(array $elements, $parentId = 0) {
        $branch = array();

        foreach ($elements as $element) {
            if ($element['parent_id'] == $parentId) {
                $children = $this->buildTreeForCategory($elements, $element['id']);
                if ($children) {
                    $element['children'] = $children;
                }
                $branch[] = $element;
                unset($elements[$key]);
            }
        }

        return $branch;
    }
    public function getCategoryTree($filter = array()){
        $categories = $this->db->query("SELECT * FROM categories")->result_array();
        return $this->buildTreeForCategory($categories);
    }


    public function getCategoriesHavingCartProducts($limit){
        $cats = $this->db->query("SELECT SQL_CALC_FOUND_ROWS c.*, pc.name as parent_name,(SELECT count(pc.category_id) FROM product_categories pc INNER JOIN product ON product.product_id=pc.product_id WHERE pc.category_id = c.id AND product.is_campaign_product!=1) as total_product FROM categories c LEFT JOIN categories pc ON pc.id = c.parent_id WHERE c.tag=1
            LIMIT ".$limit)->result_array();

        $returnCats = [];

        foreach ($cats as $cat) {
            if($cat['total_product'] > 0) {
                $returnCats[] = $cat;
            }
        }

        return $returnCats;
    }
    public function getCategory($filter = array(),$isModeCheck=null){
    $sql = "";

    $sql = "SELECT SQL_CALC_FOUND_ROWS c.*, pc.name as parent_name,(SELECT count(pc.category_id) FROM product_categories pc WHERE pc.category_id = c.id ) as total_product FROM categories c LEFT JOIN categories pc ON pc.id = c.parent_id WHERE 1";

$sql.= " ORDER BY c.id DESC ";

if (isset($filter['page'],$filter['limit'])) {
    $offset = (($filter['page']-1) * $filter['limit']);
    $sql.= " LIMIT {$offset},". $filter['limit'];
}
       
$categories = $this->db->query($sql)->result_array();
$total = $this->db->query("SELECT FOUND_ROWS() AS total")->row()->total;

$data = array();
foreach ($categories as $key => $value) {
    $data[] = array(
        'id'          => $value['id'],
        'name'        => $value['name'],
        'description' => $value['description'],
        'parent_name' => $value['parent_name'],
        'image'       => $value['image'],
        'total_product'       => (int)$value['total_product'],
        'image_url'   => base_url($value['image'] != '' ? 'assets/images/product/upload/thumb/' . $value['image'] : 'assets/images/no_image_available.png'),
        'parent_id'   => $value['parent_id'],
        'created_at'  => date("d-m-Y h:i A",strtotime($value['created_at'])),
    );
}

return array($data,$total);
}
public function getIntegrationCategory($filter = array()){
    $sql = "SELECT SQL_CALC_FOUND_ROWS c.*, p.name as parent_name FROM integration_category c LEFT JOIN integration_category p on p.id = c.parent_id WHERE 1";

    $sql.= " ORDER BY c.id DESC ";

    if (isset($filter['page'],$filter['limit'])) {
        $offset = (($filter['page']-1) * $filter['limit']);
        $sql.= " LIMIT {$offset},". $filter['limit'];
    }

    $categories = $this->db->query($sql)->result_array();

    $total = $this->db->query("SELECT FOUND_ROWS() AS total")->row()->total;

    $data = array();
    foreach ($categories as $key => $value) {
        $data[] = array(
            'id'          => $value['id'],
            'name'        => $value['name'],
            'parent_name' => $value['parent_name'],
            'created_at'  => date("d-m-Y h:i A",strtotime($value['created_at'])),
        );
    }

    return array($data,$total);
}
public function my_refer_status($user_id){
    $referlevelSettings = $this->Product_model->getSettings('referlevel');
    $disabled_for = json_decode( (isset($referlevelSettings['disabled_for']) ? $referlevelSettings['disabled_for'] : '[]'),1);
    $refer_status = true;
    if((int)$referlevelSettings['status'] == 0){ $refer_status = false; }
    else if((int)$referlevelSettings['status'] == 2 && in_array($user_id, $disabled_for)){ $refer_status = false; }

    return $refer_status;
}
public function ping($user_id){
    $this->db->query("UPDATE users SET last_ping = '". date("Y-m-d H:i:s") ."' WHERE id = ". (int)$user_id);
}

public function onlineCount() {
    $data = array();
    $result = $this->db->query("
    SELECT SUM(IF(TIMESTAMPDIFF(SECOND, last_ping, '". date("Y-m-d H:i:s") ."') < 60, 1, 0)) as online, count(*) as total, type, is_vendor
        FROM `users`
        GROUP BY type, is_vendor
        ")->result_array();

    $data['admin'] = array('total' => 0, 'online' => 0);
    $data['user'] = array('total' => 0, 'online' => 0);
    $data['vendor'] = array('total' => 0, 'online' => 0);
    $data['client'] = array('total' => 0, 'online' => 0);

    foreach ($result as $key => $value) {

        if ($value['type'] == 'admin') {
            $data['admin'] = array(
                'online' => $value['online'],
                'total' => $value['total'],
            );
        }

        if ($value['type'] == 'client') {
            $data['client'] = array(
                'online' => $value['online'],
                'total' => $value['total'],
            );
        }

        if ($value['is_vendor']) {
            $data['vendor'] = array(
                'online' => $value['online'],
                'total' => $value['total'],
            );
        }

        if ($value['type'] == 'user' && !$value['is_vendor']) {
            $data['user']['online'] += $value['online'];
            $data['user']['total'] += $value['total'];
        }
    }

    return $data;
}

    public function page_id()  {
        return str_replace("-", "_", $this->router->fetch_class().'_'.$this->router->fetch_method());
    }
    public function getUserWorldMap($isStore=null){
        $where = $isStore!=null ? " type IN ('client','guest') " :" type='user' ";
        $data = $this->db->query("
            SELECT c.name,c.sortname,count(u.id) as total
            FROM users as u
            LEFT JOIN countries c ON c.id = u.ucountry
            WHERE $where AND ucountry > 0 GROUP BY ucountry
            ")->result_array();
        require APPPATH.'/core/latlong.php';

        $markers = array();
        foreach ($data as $key => $value) {
            $l = $_lat_lng[$value['sortname']];

            $code = $_countryCode[strtoupper($value['sortname'])];
            if($code){
                $markers[] = array(
                    'latLng' => array((float)$l[0], (float)$l[1]),
                    'code'      => $value['sortname'],
                    'total'      => (int)$value['total'],
                    'name'      => $value['name'] ." - ".$value['total'],
                );
            }
        }

        return $markers;
    }

    public function getPaystackCurrencyValue() {
        return $this->db->query("SELECT value FROM currency WHERE title='Nigerian Naira'")->row();
    }

    public function getSettingStatus(){

        $this->load->model('PagebuilderModel');

        $json = array();

        $site = $this->Product_model->getSettings('site');
        $login = $this->Product_model->getSettings('login');
        $email = $this->Product_model->getSettings('email');
        $store = $this->Product_model->getSettings('store');
        $productsetting = $this->Product_model->getSettings('productsetting');


        $check = $this->db->query("SELECT currency_id FROM currency WHERE is_default = 1 ")->num_rows();

        //General settings
        if($check == 0){
            $json['currency'] = true;
        }

        $check = $this->db->query("SELECT id FROM language WHERE is_default = 1 ")->num_rows();

        if($check == 0){
            $json['language'] = true;
        }

//General settings
if ($site['time_zone'] == '') {
    $json['site']['time_zone'] = 'Not Set';
} else {
    $json['site']['time_zone'] = $site['time_zone'];
}

if ($login['front_template'] == '') {
    $json['login']['front_template'] = 'Not Set';
} else {
    $json['login']['front_template'] = $login['front_template'];
}


// Store settings
if ($store['status'] == '') {
    $json['store']['status'] = __('admin.not_net');
    $json['store']['theme'] = $store['theme'];
}
else if ($store['status'] == '0') {
    $json['store']['status'] = 'Off';
    $json['store']['theme'] = $store['theme'];

    // If the store is off, set all product settings to "Off"
    $keys_to_check = [
        'product_commission_type',
        'product_commission',
        'product_ppc',
        'product_noofpercommission'
    ];

    foreach ($keys_to_check as $key) {
        $json['productsetting'][$key] = 'Off';
    }
}
else if ($store['status'] == '1') {
    $json['store']['status'] = 'On';
    if ($store['theme'] == '0') {
        $json['store']['theme'] = 'Cart';
    } else {
        $json['store']['theme'] = 'Sales';
    }

    // Additional keys to check
    $keys_to_check = [
        'product_commission_type',
        'product_commission',
        'product_ppc',
        'product_noofpercommission'
    ];

    // Iterate through each key and check its value
    foreach ($keys_to_check as $key) {
        if (array_key_exists($key, $productsetting) && $productsetting[$key] !== '' && $productsetting[$key] !== null) {
            $json['productsetting'][$key] = $productsetting[$key];
        } else {
            $json['productsetting'][$key] = __('admin.not_net');
        }
    }
}



//Email settings
//This if need to run only if is smtp
if($email['mail_type'] == 'smtp') {

    $json['email']['mail_type'] = __('admin.smtp');

    if ($site['notify_email'] == '') {
        $json['site']['notify_email'] = __('admin.not_net');
    } else {
        $json['site']['notify_email'] = $site['notify_email'];
    }

    if ($email['from_email'] == '') {
        $json['email']['from_email'] = __('admin.not_net');
    } else {
        $json['email']['from_email'] = $email['from_email'];
    }

    if ($email['from_name'] == '') {
        $json['email']['from_name'] = __('admin.not_net');
    } else {
        $json['email']['from_name'] = $email['from_name'];
    }

    if ($email['smtp_hostname'] == '') {
        $json['email']['smtp_hostname'] = __('admin.not_net');
    } else {
        $json['email']['smtp_hostname'] = $email['smtp_hostname'];
    }

    if ($email['smtp_username'] == '') {
        $json['email']['smtp_username'] = __('admin.not_net');
    } else {
        $json['email']['smtp_username'] = $email['smtp_username'];
    }

    if ($email['smtp_password'] == '') {
        $json['email']['smtp_password'] = __('admin.not_net');
    } else {
        $json['email']['smtp_password'] = $email['smtp_password'];
    }

    if ($email['smtp_port'] == '') {
        $json['email']['smtp_port'] = __('admin.not_net');
    } else {
        $json['email']['smtp_port'] = $email['smtp_port'];
    }

    if ($email['smtp_crypto'] == '') {
        $json['email']['smtp_crypto'] = __('admin.not_net');
    } else {
        $json['email']['smtp_crypto'] = $email['smtp_crypto'];
    }
}
else if($email['mail_type'] == 'php_mailer'){

    $json['email']['mail_type'] = 'PHP Mailer';

    if ($site['notify_email'] == '') {
        $json['site']['notify_email'] = __('admin.not_net');
    } else {
        $json['site']['notify_email'] = $site['notify_email'];
    }

    if ($email['from_name'] == '') {
        $json['email']['from_name'] = __('admin.not_net');
    } else {
        $json['email']['from_name'] = $email['from_name'];
    }

    if ($email['from_email'] == '') {
        $json['email']['from_email'] = __('admin.not_net');
    } else {
        $json['email']['from_email'] = $email['from_email'];
    }
    // $json['email']['smtp_hostname'] = __('admin.off');
    // $json['email']['smtp_username'] = __('admin.off');
    // $json['email']['smtp_password'] = __('admin.off');
    // $json['email']['smtp_port'] = __('admin.off');
    // $json['email']['smtp_crypto'] = __('admin.off');
}

//Wallet setting
if($site['wallet_auto_withdrawal'] == ''){
    $json['site']['wallet_auto_withdrawal'] = __('admin.not_net');
}
else if ($site['wallet_auto_withdrawal'] == '0') {
        $json['site']['wallet_auto_withdrawal'] = __('admin.manually');
    } 
    else {
        $json['site']['wallet_auto_withdrawal'] = __('admin.auto');
    }


if ($site['wallet_min_amount'] == '') {
        $json['site']['wallet_min_amount'] = __('admin.not_net');
    } else {
        $json['site']['wallet_min_amount'] = $site['wallet_min_amount'];
    }
if ($site['wallet_max_amount'] == '') {
        $json['site']['wallet_max_amount'] = __('admin.not_net');
    } else {
        $json['site']['wallet_max_amount'] = $site['wallet_max_amount'];
    }
if ($site['wallet_min_message'] == '') {
        $json['site']['wallet_min_message'] = __('admin.not_net');
    } else {
        $json['site']['wallet_min_message'] = $site['wallet_min_message'];
    }
if ($site['wallet_min_message_new'] == '') {
        $json['site']['wallet_min_message_new'] = __('admin.not_net');
    } else {
        $json['site']['wallet_min_message_new'] = $site['wallet_min_message_new'];
    }
if ($site['wallet_auto_withdrawal_days'] == '') {
        $json['site']['wallet_auto_withdrawal_days'] = __('admin.not_net');
    } else {
        $json['site']['wallet_auto_withdrawal_days'] = $site['wallet_auto_withdrawal_days'];
    }
if ($site['wallet_auto_withdrawal_limit'] == '') {
        $json['site']['wallet_auto_withdrawal_limit'] = __('admin.not_net');
    } else {
        $json['site']['wallet_auto_withdrawal_limit'] = $site['wallet_auto_withdrawal_limit'];
    }

    // $json['site']['wallet_min_amount'] = __('admin.not_net');
    // $json['site']['wallet_max_amount'] = __('admin.not_net');
    // $json['site']['wallet_min_message'] = __('admin.not_net');
    // $json['site']['wallet_min_message_new'] = __('admin.not_net');
    // $json['site']['wallet_auto_withdrawal'] = __('admin.not_net');
    // $json['site']['wallet_auto_withdrawal_days'] = __('admin.not_net');
    // $json['site']['wallet_auto_withdrawal_limit'] = __('admin.not_net');

        return $json;
    }

    public function hold_noti($filter = array()){
        $where = ' 1 ';
        if (isset($filter['user_id'])) {
            $where .= '  AND user_id = '. (int)$filter['user_id'];
        }

        $data['store_hold_orders'] = (int)$this->db->query('SELECT count(*) as total FROM `order` WHERE '. $where .' AND status  = 7')->row_array()['total'];
        $data['integration_hold_orders'] = (int)$this->db->query('SELECT count(*) as total FROM `integration_orders` WHERE '. $where .' AND status  = 0')->row_array()['total'];

        return $data;
    }
    public function getPaymentWarning(){
        $userdetails = $this->userdetails('user');
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
        return $data['paymentlist'];
    }
    public $loginUser = [];

    public function userdetails($guard = 'administrator',$force = 0){ 

        if($force){
            $this->loginUser[$guard] = $this->db->query("SELECT * FROM users WHERE id=". (int)$this->session->userdata($guard)['id'])->row_array();
        }

        if(!isset($this->loginUser[$guard])){
            $u = $this->session->userdata($guard);  

            return $u;

            if($u){
                $this->loginUser[$guard] = $this->db->query("SELECT * FROM users WHERE id=". (int)$u['id'])->row_array();
            }
        }

        return $this->loginUser[$guard];
    }
    public function getSiteSetting(){ return $this->getSettings('site'); }
    public function getLicese(){ return $this->session->userdata('license'); }
    public function getMultipleProductById($product_id){ return $this->db->get_where('product_media_upload', array('product_media_upload_id' => $product_id))->row_object(); }
    public function getProductByIdArray($product_id){ return $this->db->get_where('product', array('product_id' => $product_id))->row_array(); }
    public function getAffiliateById($affiliateads_id = null){ return $this->db->get_where('affiliateads', array('affiliateads_id' => $affiliateads_id))->row_array(); }
    public function getProductById($product_id){ return $this->db->get_where('product', array('product_id' => $product_id))->row_object(); }
    public function getSettingById($product_id){ return $this->db->get_where('setting', array('setting_id' => $setting_id))->row_object(); }
    public function getProductBySlug($product_slug){ return $this->db->get_where('product', array('product_slug' => $product_slug))->row_array(); }
    public function getUserDetails($user_id){ return $this->db->get_where('users', array('id' => $user_id))->row_array(); }
    public function getProductDetails($product_id){ return $this->db->get_where('product', array('product_id' => $product_id))->row_array(); }
    public function getUserDetailsObject($user_id){ return $this->db->get_where('users', array('id' => $user_id))->row_object(); }
    public function getAllImages($id){ return $this->db->get_where('product_media_upload', array('product_media_upload_status' => 1, 'product_media_upload_type' => 'image', 'product_id' => $id))->result_array(); }
    public function getAllVideoImages($id){ return $this->db->get_where('product_media_upload', array('product_media_upload_status' => 1, 'product_media_upload_type' => 'video', 'product_id' => $id))->result_array(); }
    public function getAllVideos($id){ return $this->db->get_where('product_media_upload', array('product_media_upload_status' => 1, 'product_media_upload_type' => 'video', 'product_id' => $id))->result_array(); }
    public function getAllSettings(){ return $this->db->get_where('setting', array('setting_status' => 1))->result_array(); }
    public function getAllProductrecord(){ return $this->db->get_where('product', array('product_status' => 1))->result_array(); }
    public function getPaymentById($payment_id){ return $this->db->get_where('payment_detail', array('payment_id' => $payment_id))->row_object(); }
    public function getRequestPaymentById($user_payment_request_id){ return $this->db->get_where('user_payment_request', array('user_payment_request_id' => $user_payment_request_id))->row_object(); }
    public function getAllPaymentRequest(){ return $this->db->get_where('user_payment_request')->result_array(); }
    public function getUserPaymentRequest($id){ return $this->db->get_where('user_payment_request', array('user_payment_request_amount_status' => 'pending', 'user_payment_request_user_id' => $id))->result_array(); }
    public function getPaymentRequestById($id){ return $this->db->get_where('user_payment_request', array('user_payment_request_id' => $id))->row_array(); }
    function update_data($product, $details, $where_data_array = NULL){
        if ($where_data_array){
            foreach ($where_data_array as $key => $data) $this->db->where($key, $data);
        }
        return $this->db->update($product, $details);
    }
    function getProductByIds($product_ids){
        $this->db->select('*');
        $this->db->from('product');
        $this->db->where_in('product_id', $product_ids);
        return $this->db->get()->result();
    }
    function getAllProducts($filter = []){

       $sql = " SELECT DISTINCT p.*,seller.id seller_id FROM product p INNER JOIN users as seller ON seller.id =p.product_created_by WHERE 1 and seller.type='admin' and is_campaign_product=0 ";

        if (isset($filter['vendor_id'])) {
            if($filter['vendor_id'] == 'admin'){
                $sql .= " AND seller.type='admin' ";
            } else {
                $sql .= " AND seller.id=". (int)$filter['vendor_id'];
            }
        }

        return $this->db->query($sql)->result();
    }
    function getDeleteById($affiliateads_id){
        $this->db->where('affiliateads_id', $affiliateads_id);
        return $this->db->delete('affiliateads');
    }
    
    function getLanguageHtmlUser($control = 'admincontrol' , $theme = null, $isonlyList=null){
        $lang = $_SESSION['userLang'];

        if(!$lang) {
            $lang = 1;
            $selected = $this->db->query("SELECT * FROM language WHERE status=1 AND is_default=1")->row_array();
            $_SESSION['userLangName'] = $selected['name'];
        } else {
            $selected = $this->db->query("SELECT * FROM language WHERE status=1 AND id=".$lang)->row_array();
            $_SESSION['userLangName'] = $selected['name'];
        };


        $all = $this->db->query("SELECT * FROM language WHERE status=1")->result_array();

        if($isonlyList) {
            $dataUpdated = []; 
            foreach ($all as &$lang) {
                if(isset($_SESSION['userLang'])) {
                    if((int)$lang['id'] === (int)$_SESSION['userLang']) {
                        $dataUpdated['SelectedLanguage'] = $lang['name'];
                        $dataUpdated['SelectedLanguageFlag'] = $lang['flag'];
                    }
                } else if(!isset($_SESSION['userLang']) && (int)$lang['is_default'] == 1) {
                    $dataUpdated['SelectedLanguage'] = $lang['name'];
                }

                if($theme == 'store-default') {
                    $lang['href'] = base_url("store/change_language/".$lang['id']);
                } else {
                    $lang['href'] = base_url("Admincontrol/change_language/".$lang['id']);
                }

                $dataUpdated['LanguageHtml'] = $all;
            }

            return $dataUpdated;
        }
        if($theme == 'store-default') {
            
            $_html = '';
            foreach ($all as $key => $value) {
                if(!$selected) {
                    $selected = $value;
                    $this->db->query("UPDATE language SET is_default = 1 WHERE id=". (int)$selected['id']);
                }
                $_html .='<li class="iq-sub-card list-group-item"><a class="p-0" href="'. base_url($control."/change_language/".$value['id']) .'">
                              <img src="'. base_url($value['flag']) .'" alt="'.$value['name'].'" class="img-fluid me-2" style="width: 15px;height: 15px;min-width: 15px;">'. $value['name'] .' </a></li>';
            }

             $html='<a href="#" class="search-toggle nav-link" id="dropdownMenuButton4" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <img src="'. base_url($selected['flag']) .'" class="img-fluid rounded-circle" alt="'. $selected['name'] .'" style="height: 30px; min-width: 30px; width: 30px;">
                    <span class="bg-primary"></span> </a><div class="sub-drop dropdown-menu dropdown-menu-end p-0"><div class="card shadow-none m-0 border-0"><div class=" p-0 ">
                        <ul class="list-group list-group-flush p-0">'. $_html.'</ul></div></div></div>';
        } else {

            $_html = '';
            foreach ($all as $key => $value) {
                if(!$selected) {
                    $selected = $value;
                    $this->db->query("UPDATE language SET is_default = 1 WHERE id=". (int)$selected['id']);
                }
                    $_html .='<li class="iq-sub-card list-group-item"><a class="p-0" href="'. base_url($control."/change_language/".$value['id']) .'">
                              <img src="'. base_url($value['flag']) .'" alt="'.$value['name'].'" class="img-fluid me-2" style="width: 15px;height: 15px;min-width: 15px;">'. $value['name'] .' </a></li>';
            }

            //language on user panel
            $html='<a href="#" class="dropdown-toggle nav-link" id="dropdownMenuButton4" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <img src="'. base_url($selected['flag']) .'" class="img-fluid rounded-circle" alt="'. $selected['name'] .'" style="height: 30px; min-width: 30px; width: 30px;">
            </a>
            <div class="dropdown-menu dropdown-menu-end">
                <ul class="list-group list-group-flush">'. $_html .'</ul>
            </div>';

            return $html;
        }

        return $html;
    }


    function getLanguageHtml($control = 'admincontrol' , $theme = null, $isonlyList=null){
        $lang = $_SESSION['userLang'];

        if(!$lang) {
            $lang = 1;
            $selected = $this->db->query("SELECT * FROM language WHERE status=1 AND is_default=1")->row_array();
            $_SESSION['userLangName'] = $selected['name'];
        } else {
            $selected = $this->db->query("SELECT * FROM language WHERE status=1 AND id=".$lang)->row_array();
            $_SESSION['userLangName'] = $selected['name'];
        };


        $all = $this->db->query("SELECT * FROM language WHERE status=1")->result_array();

        if($isonlyList) {
            $dataUpdated = []; 
            foreach ($all as &$lang) {
                if(isset($_SESSION['userLang'])) {
                    if((int)$lang['id'] === (int)$_SESSION['userLang']) {
                        $dataUpdated['SelectedLanguage'] = $lang['name'];
                        $dataUpdated['SelectedLanguageFlag'] = $lang['flag'];
                    }
                } else if(!isset($_SESSION['userLang']) && (int)$lang['is_default'] == 1) {
                    $dataUpdated['SelectedLanguage'] = $lang['name'];
                }

                if($theme == 'store-default') {
                    $lang['href'] = base_url("store/change_language/".$lang['id']);
                } else {
                    $lang['href'] = base_url("Admincontrol/change_language/".$lang['id']);
                }

                $dataUpdated['LanguageHtml'] = $all;
            }

            return $dataUpdated;
        }
        
        if($theme == 'store-default') {
            $_html = '';
            foreach ($all as $key => $value) {
                if(!$selected) {
                    $selected = $value;
                    $this->db->query("UPDATE language SET is_default = 1 WHERE id=". (int)$selected['id']);
                }
                $_html .='<li class="d-flex"><a class="text-dark" href="'. base_url($control."/change_language/".$value['id']) .'"><img alt="image" src="'. base_url($value['flag']) .'" class="mr-1" height="15" width="17"/>'. $value['name'] .'</a></li>';
            }

            $toggleClass = sizeof($all) > 1 ? 'js-link' : '';

            $html = '<a href="javascript:void(0);" class="'.$toggleClass.' text-dark"><img alt="image" src="'. base_url($selected['flag']) .'" class="mr-1" height="15" width="20"/>'. $selected['name'] .'</a><ul class="js-dropdown-list">'.$_html;
            $html .= '</ul>';
        } else {
            $_html = '';
            foreach ($all as $key => $value) {
                if(!$selected) {
                    $selected = $value;
                    $this->db->query("UPDATE language SET is_default = 1 WHERE id=". (int)$selected['id']);
                }
                    $_html .='<a class="dropdown-item" href="'. base_url($control."/change_language/".$value['id']) .'"><img src="'. base_url($value['flag']) .'" alt="'.$value['name'].'" />'. $value['name'] .'</a>';
            }

            $toggleClass = sizeof($all) > 1 ? 'data-bs-toggle="dropdown"' : '';

            $html = '<a class="nav-link dropdown-toggle" href="#" role="button" '.$toggleClass.' aria-haspopup="true" aria-expanded="false">
                <img class="selected-language" src="'. base_url($selected['flag']) .'" alt="'. $selected['name'] .'" style="height: 33px; min-width: 33px; width: 33px;" />
                </a>
                    <div id="language-dropdown" class="dropdown-menu dropdown-menu-end shadow country-wrap">
                        '. $_html .'
                    </div>';

            return $html;
        }

        return $html;
    }


function getCurrencyHtmlUser($control = 'admincontrol', $theme = null, $onlyList=null){
    $lang = $_SESSION['userCurrency'];
    $selected = $this->db->query("SELECT * FROM currency WHERE code = '{$lang}' ")->row_array();
    if(!$selected){
        $selected = $this->db->query("SELECT * FROM currency WHERE is_default=1")->row_array();
    }
    $_SESSION['userCurrency'] = $selected['code'];
    $_SESSION['userCurrencyName'] = $selected['title'];
    $_SESSION['userCurrencyLeft'] = $selected['symbol_right'];


    $all = $this->db->query("SELECT * FROM currency WHERE status=1 AND code != '{$lang}' ")->result_array();
    if($onlyList){
        return $all;
    }
    if($theme == 'store-default') {

        $html = '<a href="#" class="search-toggle nav-link" id="dropdownMenuButton3" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <img src="'.base_url('assets/template/images/dollar-icon.png').'" class="img-fluid rounded-circle" alt="'.$selected['code'].'" style="height: 30px; min-width: 30px; width: 30px;">
                <span class="bg-primary"></span>
              </a>';
        $html .= '<div class="sub-drop dropdown-menu dropdown-menu-end p-0"> <div class="card shadow-none m-0 border-0"><div class=" p-0 "><ul class="list-group list-group-flush p-0">';

        foreach($all as $key => $value)
            $html .= '<li class="iq-sub-card list-group-item">
                        <a class="p-0" href="'. base_url($control."/change_currency/".$value['code']) .'">
                          <img src="'.base_url('assets/template/images/dollar-icon.png').'" alt="img-flaf" class="img-fluid me-2" style="width: 15px;height: 15px;min-width: 15px;">'.$value['code'].' </a>
                      </li>';
        $html .= '</ul></div></div></div>';
    } else {
        $html = '
            <a href="#" class="search-toggle nav-link dropdown-toggle d-flex align-items-center" id="dropdownCurrencyUser" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="bg-secondary text-white d-flex justify-content-center align-items-center rounded-circle" style="width: 35px; height: 35px; line-height: 30px;">'. $selected['code'] .'</span>
            </a>
        ';

        $html .= '<div class="sub-drop dropdown-menu dropdown-menu-end p-0" style="width: fit-content;"> <div class="card shadow-none m-0 border-0"><div class=" p-0 "><ul class="list-group list-group-flush p-0">';

            foreach($all as $key => $value)
                $html .= '<li class="iq-sub-card list-group-item">
                            <a class="p-0" href="'. base_url($control."/change_currency/".$value['code']) .'">
                              <img src="'.base_url('assets/template/images/dollar-icon.png').'" alt="img-flaf" class="img-fluid me-2" style="width: 15px;height: 15px;min-width: 15px;">'.$value['code'].' </a>
                          </li>';
            $html .= '</ul></div></div></div>';
        }
        
        return $html;
    }
    
    function getCurrencyHtml($control = 'admincontrol', $theme = null, $onlyList=null){
        $lang = $_SESSION['userCurrency'];
        $selected = $this->db->query("SELECT * FROM currency WHERE code = '{$lang}' ")->row_array();
        if(!$selected){
            $selected = $this->db->query("SELECT * FROM currency WHERE is_default=1")->row_array();
        }
        $_SESSION['userCurrency'] = $selected['code'];
        $_SESSION['userCurrencyName'] = $selected['title'];
        $_SESSION['userCurrencyLeft'] = $selected['symbol_right'];

        $all = $this->db->query("SELECT * FROM currency WHERE status=1 AND code != '{$lang}' ")->result_array();
        if($onlyList){
            return $all;
        }
        
        $dropdownToggle = count($all) > 0 ? 'data-bs-toggle="dropdown"' : '';
        $caretIcon = count($all) > 0 ? '<i class="fas fa-caret-down"></i>' : '';

        if($theme == 'store-default') {
            $html = '<a href="#" class="js-link1 text-dark" data-currency-symbol="'.$selected['code'].'">'.$selected['code'] .'</a><ul class=" js-dropdown-list1">';
            foreach ($all as $key => $value) {
                $html .= '<a class="text-dark" href="'. base_url($control."/change_currency/".$value['code']) .'"><li class="d-flex">'. $value['code'] .'</li></a>';
            }
            $html .= '</ul>';
        } else {
            $html = '<a class="nav-link dropdown-toggle" href="#" role="button" ' . $dropdownToggle . ' aria-haspopup="true" 
            aria-expanded="false">
            <span class="currency-code bg-secondary text-white rounded-circle" style="display: inline-block; width: 33px; height: 33px; line-height: 33px; text-align: center;">'.$selected['code'].'</span>
            ' . $caretIcon . '
            </a>';

            $html .= '<div id="currency-dropdown" class="dropdown-menu dropdown-menu-end shadow currency-wrap">';
            
            foreach($all as $key => $value) {
                $html .= '<a class="dropdown-item" href="'. base_url($control."/change_currency/".$value['code']) .'">
                '.$value['code'].'</a>';
            }
            $html .= '</div>';
        }
        return $html;
    }

    function getAffiliateByType($affiliateads_type, $user_id = 0){
        $where = '';
        if($user_id > 0){
            $where = " AND user_id =  {$user_id} ";
        }
        $this->db->select(array(
            'affiliateads.*',
            "( SELECT count(*) FROM affiliate_action WHERE  affiliate_action.affiliate_id = affiliateads.affiliateads_id {$where} ) total_click",
            "( SELECT SUM(amount) FROM wallet WHERE wallet.reference_id = affiliateads.affiliateads_id AND type='affiliate_click_commission' AND wallet.status = 1 {$where}) total_unpaid",
            "( SELECT SUM(amount) FROM wallet WHERE wallet.reference_id = affiliateads.affiliateads_id AND type='affiliate_click_commission' AND wallet.status = 2 {$where}) total_inrequest",
            "( SELECT SUM(amount) FROM wallet WHERE wallet.reference_id = affiliateads.affiliateads_id AND type='affiliate_click_commission' AND wallet.status = 3 {$where}) total_paid",
        ));
        if ($affiliateads_type) {
            $data = $this->db->get_where('affiliateads', array('affiliateads_type' => trim($affiliateads_type)))->result_array();
        } else {
            $data = $this->db->get_where('affiliateads')->result_array();
        }
        return $data;
    }
    
    function getAffiliateUserByByType($affiliateads_type = null, $user_id = null){
        $this->db->select('affiliateads.affiliateads_id,SUM( clicks_views.clicks_views_click ) as total_click, SUM( clicks_views.clicks_views_view ) as total_view,SUM( clicks_views.clicks_views_click_commission ) as total_commission  ');
        $this->db->join('affiliateads', 'affiliateads.affiliateads_id = clicks_views.clicks_views_action_id');
        if ($affiliateads_type) {
            $this->db->where('clicks_views.clicks_views_refuser_id', $user_id);
            $this->db->group_by('clicks_views.clicks_views_action_id');
            $getData = $this->db->get_where('clicks_views', array('clicks_views.clicks_views_type' => trim($affiliateads_type)))->result_array();
        } else {
            $this->db->where('clicks_views.clicks_views_type IN ("banner","html","viralvideo","invisilinks")');
            $this->db->where('clicks_views.clicks_views_refuser_id', $user_id);
            $this->db->group_by('clicks_views.clicks_views_action_id');
            $getData = $this->db->get_where('clicks_views')->result_array();
        }
        foreach ($getData as $setArray) {
            $getArray[$setArray['affiliateads_id']] = $setArray;
        }
        return $getArray;
    }

    function getAllTags() {
        $tags = [];
        $query = "SELECT product_tags FROM product";
        $data = $this->db->query($query)->result_array();
        foreach($data as $d) {
            $temp_tags = json_decode($d['product_tags']);
            foreach($temp_tags as $t) {
                array_push($tags, $t);
            }
        }
        return array_unique($tags);
    }

    function getAllColors() {
        $colors = [];
        $query = "SELECT product_variations FROM product";
        $data = $this->db->query($query)->result_array();
        foreach($data as $d) {
            $variations = json_decode($d['product_variations']);
            foreach ($variations as $key => $value) {
                if($key == "colors") {
                    foreach($value as $op) {
                        array_push($colors, $op->code);
                    }
                }
            }
        }
        return array_unique($colors);
    }

   function getAllProduct($user_id, $user_type, $filter = array()){

        if($user_type == 'admin') $clause = ' ';
        else{
            $clause = " user_id = $user_id AND ";
            $clause_orders = " op.refer_id = {$user_id} AND ";
        }

        $left_join = $where = '';
        $vendor = $this->getSettings('vendor');

        if((int)$vendor['storestatus'] == 0){
            $where .= " AND( seller.id=0 OR seller.id IS NULL)";
        }

        if(isset($filter['seller_id'])){
            $where .= " AND pa.user_id=". (int)$filter['seller_id'];
        }

        if (isset($filter['restrict_vendors']) && !empty($filter['restrict_vendors'])) {
            $tempvq = "";

            foreach ($filter['restrict_vendors'] as $vid) {
                if($tempvq != "") {
                    $tempvq .= " AND (seller.id IS NULL OR seller.id != ".(int)$vid.") ";
                } else {
                    $tempvq .= " (seller.id IS NULL OR seller.id != ".(int)$vid.") ";
                }
            }

            if($tempvq != "") {
                $where .= " AND ( ".$tempvq." ) ";
            }
        }

        if(isset($filter['on_store'])){
            $where .= " AND on_store=". (int)$filter['on_store'];
        }

        if(isset($filter['seller_allow_only_status']) && $filter['seller_allow_only_status']){
            $where .= " AND (vs.user_id = ". $filter['seller_allow_only_status'] ." OR  vs.vendor_status = 1 OR vs.user_id IS NULL) ";
            $left_join .= " LEFT JOIN vendor_setting AS vs ON (seller.id = vs.user_id)";
        }

        if(isset($filter['only_admin_product'])){
           $where .= " AND( seller.id=0 OR  seller.id IS NULL) AND on_store=1 ";
       }


       if(isset($filter['not_show_my'])){
           $where .= " AND( seller.id != ". (int)$filter['not_show_my'] ." OR  seller.id IS NULL )";
       }

       if(isset($filter['product_status'])){
        $where .= " AND product.product_status=". (int)$filter['product_status'];
    }

    if(isset($filter['product_status_in'])){
        $where .= " AND product.product_status IN (". $filter['product_status_in'].")";
    }

    if(isset($filter['category_id']) && $filter['category_id']){
        $where .= " AND product.product_id IN ( SELECT product_id FROM product_categories WHERE category_id = ". $filter['category_id'] ." GROUP BY product_id)";
    }

    if (isset($filter['ads_name']) && $filter['ads_name']) {
        $where .= " AND product.product_name like '%". $filter['ads_name'] ."%' ";
    }

    if (isset($filter['vendor_id']) && !empty($filter['vendor_id'])) {
        $where .= " AND product.product_created_by =". $filter['vendor_id'] ." ";
    }

    if(isset($filter['is_campaign_and_cart_product']) && !empty($filter['is_campaign_and_cart_product'])) {
         $where .= " AND product.is_campaign_product >=0 ";
    } else if (isset($filter['is_campaign_product']) && !empty($filter['is_campaign_product'])) {
        $where .= " AND product.is_campaign_product = 1 ";
    } else {
        $where .= " AND product.is_campaign_product != 1 ";
    }

    if (isset($filter['show_to_affiliates']) && !empty($filter['show_to_affiliates'])) {
        $where .= " AND (pm.meta_value = 1 OR pm.meta_value IS NULL) ";
    }


    $limit = '';
    if(isset($filter['limit']) && (int)$filter['limit'] > 0){
        $limit = " LIMIT ". (int)$filter['limit'];
    }
    if(isset($filter['start']) && (int)$filter['start'] && $limit){
        $limit = " LIMIT {$filter['start']} , {$filter['limit']} ";
    }
    if(isset($filter['page']) && $limit){
        $offset = (int)$filter['limit'] * ((int)$filter['page'] - 1);
        $limit = " LIMIT ". $offset ." ,". (int)$filter['limit'];
    }

    //added for all click count that match with admin ratio
    $all_count_sql="  (SELECT count(action_id) FROM product_action WHERE   product_id = product.product_id) as all_commition_click_count,
        (SELECT count(op.commission) FROM order_products op LEFT JOIN `order` o ON (o.id = op.order_id) WHERE  op.product_id = product.product_id AND o.status > 0 ) as all_order_count 
        ";

    $query = "SELECT SQL_CALC_FOUND_ROWS
    product.*,
    seller.firstname as seller_firstname,
    seller.lastname as seller_lastname,
    seller.username as seller_username,
    seller.id as seller_id,
    pm.meta_value as show_to_affiliates,
    (
        SELECT sum(op.commission)
        FROM order_products op
        LEFT JOIN `order` o ON (o.id = op.order_id)
        WHERE
        {$clause_orders}
        op.product_id = product.product_id AND o.status > 0 AND op.refer_id > 0) as commission,
    (SELECT count(op.commission) FROM order_products op LEFT JOIN `order` o ON (o.id = op.order_id) WHERE {$clause_orders} op.product_id = product.product_id AND o.status > 0 ) as order_count,
    (SELECT SUM(IF(amount=-1,0,amount)) FROM wallet WHERE {$clause} type = 'click_commission' AND reference_id = product.product_id) as commition_click,
    (SELECT count(action_id) FROM product_action WHERE {$clause} product_id = product.product_id) as commition_click_count,
    (SELECT COUNT(rating_id) FROM `rating` INNER join users on users.id=rating.rating_user_id WHERE rating.products_id=product.product_id) as totalreviews,
    (SELECT  SUM(rating.rating_number)  FROM `rating` INNER join users on users.id=rating.rating_user_id WHERE rating.products_id=product.product_id) as totalrating,". $all_count_sql ."


    FROM product
    LEFT JOIN product_affiliate pa ON pa.product_id = product.product_id
    LEFT JOIN users as seller ON pa.user_id = seller.id
    LEFT JOIN product_meta as pm ON pm.related_product_id = product.product_id and pm.meta_key = 'show_to_affiliates'
    {$left_join}
    WHERE 1 {$where} AND (seller.id IS NOT NULL OR pa.id IS NULL) ORDER BY product_created_date DESC {$limit}";
 
    $data =  $this->db->query($query)->result_array();

    if(isset($filter['page'])){
        $total = $this->db->query("SELECT FOUND_ROWS() AS total")->row()->total;

        return [
            'data'  => $data,
            'total'  => $total,
        ];
    }

    return $data;
}

public function getAllVendorProducts($user_id, $user_type){
    if($user_type == 'admin') $clause = ' ';
    else{
        $clause = " ";
        $clause_orders = " op.vendor_id = {$user_id} AND ";
    }
    $where = '';
    $query = "SELECT
    product.*,
    seller.firstname as seller_firstname,
    seller.lastname as seller_lastname,
    seller.username as seller_username,
    seller.id as seller_id,
    (
        SELECT sum(op.commission)
        FROM order_products op
        LEFT JOIN `order` o ON (o.id = op.order_id)
        WHERE
        {$clause_orders}
        o.status = 1 AND
        op.product_id = product.product_id AND o.status > 0 AND op.refer_id > 0) as commission,
    (SELECT count(op.commission) FROM order_products op LEFT JOIN `order` o ON (o.id = op.order_id) WHERE {$clause_orders} op.product_id = product.product_id AND o.status > 0 ) as order_count,
    (SELECT count(action_id) FROM product_action WHERE {$clause} product_id = product.product_id) as commition_click_count,
    (SELECT count(action_id) FROM product_action_admin WHERE product_id = product.product_id) as commition_click_count_admin,
    (SELECT SUM(IF(amount=-1,0,amount)) FROM wallet WHERE {$clause} type = 'click_commission' AND reference_id = product.product_id) as commition_click
    
    
    FROM product
    LEFT JOIN product_affiliate pa ON pa.product_id = product.product_id
    LEFT JOIN users as seller ON pa.user_id = seller.id
    WHERE 1 {$where}
    ORDER BY product_created_date ASC
    ";

    $data =  $this->db->query($query)->result_array();

    return $data;
}
public function getAllProductForVendor($user_id, $user_type, $filter = array()){
    if($user_type == 'admin') $clause = ' ';
    else{
        $clause = " ";
        $clause_orders = " op.vendor_id = {$user_id} AND ";
    }
    $where = '';

    if(isset($filter['seller_id'])){
        $where .= " AND pa.user_id=". (int)$filter['seller_id'];
    }

    if(isset($filter['only_admin_product'])){
        $where .= " AND( seller.id=0 OR  seller.id IS NULL)";
    }

    if(isset($filter['product_status'])){
        $where .= " AND product.product_status=". (int)$filter['product_status'];
    }

    $where .= " AND product.is_campaign_product=0";

    if(isset($filter['product_status_in'])){
        $where .= " AND product.product_status IN (". $filter['product_status_in'].")";
    }

    if(isset($filter['category_id'])){
        $where .= " AND product_id IN ( SELECT product_id FROM product_categories WHERE category_id = ". $filter['category_id'] ." GROUP BY product_id)";
    }

    //added for ratio count
    $all_count_sql= " (SELECT count(action_id) FROM product_action WHERE   product_id = product.product_id) as all_commition_click_count,
        (SELECT count(op.commission) FROM order_products op LEFT JOIN `order` o ON (o.id = op.order_id) WHERE  op.product_id = product.product_id AND o.status > 0 ) as all_order_count ";

    $query = "SELECT
    product.*,
    seller.firstname as seller_firstname,
    seller.lastname as seller_lastname,
    seller.username as seller_username,
    seller.id as seller_id,
    (
        SELECT sum(op.commission)
        FROM order_products op
        LEFT JOIN `order` o ON (o.id = op.order_id)
        WHERE
        {$clause_orders}
        o.status = 1 AND
        op.product_id = product.product_id AND o.status > 0 AND op.refer_id > 0) as commission,
    (SELECT count(op.commission) FROM order_products op LEFT JOIN `order` o ON (o.id = op.order_id) WHERE {$clause_orders} op.product_id = product.product_id AND o.status > 0 ) as order_count,
    (SELECT count(action_id) FROM product_action WHERE {$clause} product_id = product.product_id) as commition_click_count,
    (SELECT count(action_id) FROM product_action_admin WHERE product_id = product.product_id) as commition_click_count_admin,
    (SELECT SUM(IF(amount=-1,0,amount)) FROM wallet WHERE {$clause} type = 'click_commission' AND reference_id = product.product_id) as commition_click,
    (SELECT COUNT(rating_id) FROM `rating` INNER join users on users.id=rating.rating_user_id WHERE rating.products_id=product.product_id) as totalreviews,
    (SELECT  SUM(rating.rating_number)  FROM `rating` INNER join users on users.id=rating.rating_user_id WHERE rating.products_id=product.product_id) as totalrating," . $all_count_sql . "  
    FROM product
    LEFT JOIN product_affiliate pa ON pa.product_id = product.product_id
    LEFT JOIN users as seller ON pa.user_id = seller.id
    WHERE 1 {$where}
    ORDER BY product_created_date DESC";
  
    $data =  $this->db->query($query)->result_array();

    return $data;
}
public function getAllSaleProductForVendor($user_id, $user_type, $filter = array()){

    if($user_type == 'admin') $clause = ' ';
        else{
            $clause = " user_id = $user_id AND ";
            $clause_orders = " op.refer_id = {$user_id} AND ";
        }

        $left_join = $where = '';
        $vendor = $this->getSettings('vendor');

        if((int)$vendor['storestatus'] == 0){
            $where .= " AND( seller.id=0 OR seller.id IS NULL)";
        }

        if(isset($filter['seller_id'])){
            $where .= " AND pa.user_id=". (int)$filter['seller_id'];
        }

        if (isset($filter['restrict_vendors']) && !empty($filter['restrict_vendors'])) {
            $tempvq = "";

            foreach ($filter['restrict_vendors'] as $vid) {
                if($tempvq != "") {
                    $tempvq .= " AND (seller.id IS NULL OR seller.id != ".(int)$vid.") ";
                } else {
                    $tempvq .= " (seller.id IS NULL OR seller.id != ".(int)$vid.") ";
                }
            }

            if($tempvq != "") {
                $where .= " AND ( ".$tempvq." ) ";
            }
        }

        if(isset($filter['on_store'])){
            $where .= " AND on_store=". (int)$filter['on_store'];
        }

        if(isset($filter['seller_allow_only_status']) && $filter['seller_allow_only_status']){
            $where .= " AND (vs.user_id = ". $filter['seller_allow_only_status'] ." OR  vs.vendor_status = 1 OR vs.user_id IS NULL) ";
            $left_join .= " LEFT JOIN vendor_setting AS vs ON (seller.id = vs.user_id)";
        }

        if(isset($filter['only_admin_product'])){
           $where .= " AND( seller.id=0 OR  seller.id IS NULL) AND on_store=1 ";
       }


       if(isset($filter['not_show_my'])){
           $where .= " AND( seller.id != ". (int)$filter['not_show_my'] ." OR  seller.id IS NULL )";
       }

       if(isset($filter['product_status'])){
        $where .= " AND product.product_status=". (int)$filter['product_status'];
    }

    if(isset($filter['product_status_in'])){
        $where .= " AND product.product_status IN (". $filter['product_status_in'].")";
    }

    if(isset($filter['category_id']) && $filter['category_id']){
        $where .= " AND product.product_id IN ( SELECT product_id FROM product_categories WHERE category_id = ". $filter['category_id'] ." GROUP BY product_id)";
    }

    if (isset($filter['ads_name']) && $filter['ads_name']) {
        $where .= " AND product.product_name like '%". $filter['ads_name'] ."%' ";
    }

    if (isset($filter['vendor_id']) && !empty($filter['vendor_id'])) {
        $where .= " AND product.product_created_by =". $filter['vendor_id'] ." ";
    }

    $where .= " AND product.is_campaign_product = 1 ";

    if (isset($filter['show_to_affiliates']) && !empty($filter['show_to_affiliates'])) {
        $where .= " AND (pm.meta_value = 1 OR pm.meta_value IS NULL) ";
    }

    $limit = '';
    if(isset($filter['limit']) && (int)$filter['limit'] > 0){
        $limit = " LIMIT ". (int)$filter['limit'];
    }
    if(isset($filter['start']) && (int)$filter['start'] && $limit){
        $limit = " LIMIT {$filter['start']} , {$filter['limit']} ";
    }
    if(isset($filter['page']) && $limit){
        $offset = (int)$filter['limit'] * ((int)$filter['page'] - 1);
        $limit = " LIMIT ". $offset ." ,". (int)$filter['limit'];
    }

    //added for ratio count
    $all_count_sql= " (SELECT count(action_id) FROM product_action WHERE   product_id = product.product_id) as all_commition_click_count,
        (SELECT count(op.commission) FROM order_products op LEFT JOIN `order` o ON (o.id = op.order_id) WHERE  op.product_id = product.product_id AND o.status > 0 ) as all_order_count ";

    $query = "SELECT SQL_CALC_FOUND_ROWS
    product.*,
    seller.firstname as seller_firstname,
    seller.lastname as seller_lastname,
    seller.username as seller_username,
    seller.id as seller_id,
    pm.meta_value as show_to_affiliates,
    (
        SELECT sum(op.commission)
        FROM order_products op
        LEFT JOIN `order` o ON (o.id = op.order_id)
        WHERE
        {$clause_orders}
        op.product_id = product.product_id AND o.status > 0 AND op.refer_id > 0) as commission,
    (SELECT count(op.commission) FROM order_products op LEFT JOIN `order` o ON (o.id = op.order_id) WHERE {$clause_orders} op.product_id = product.product_id AND o.status > 0 ) as order_count,
    (SELECT count(action_id) FROM product_action_admin WHERE product_id = product.product_id) as commition_click_count_admin,
    (SELECT SUM(IF(amount=-1,0,amount)) FROM wallet WHERE {$clause} reference_id_2 = 'vendor_sale_commission' AND reference_id = product.product_id) as commition_click,
    (SELECT count(action_id) FROM product_action WHERE   product_id = product.product_id) as commition_click_count, 

     ".$all_count_sql."

    FROM product
    LEFT JOIN product_affiliate pa ON pa.product_id = product.product_id
    LEFT JOIN users as seller ON pa.user_id = seller.id
    LEFT JOIN product_meta as pm ON pm.related_product_id = product.product_id and pm.meta_key = 'show_to_affiliates'
    {$left_join}
    WHERE 1 {$where} AND (seller.id IS NOT NULL OR pa.id IS NULL) ORDER BY product_created_date DESC {$limit}

    ";

    $data =  $this->db->query($query)->result_array();

    if(isset($filter['page'])){
        $total = $this->db->query("SELECT FOUND_ROWS() AS total")->row()->total;

        return [
            'data'  => $data,
            'total'  => $total,
        ];
    }

    return $data;
}
public function getSellerFromProduct($product_id)   {
    return $this->db->query("SELECT * FROM product_affiliate WHERE product_id=". (int)$product_id ." ")->row();
}
public function getSellerSetting($user_id)   {
    return $this->db->query("SELECT * FROM vendor_setting WHERE user_id=". (int)$user_id ." ")->row();
}
public function getAllUsersExport($filter = array()){
    $query = 'SELECT
    countries.sortname,
    users.*,
    pd.*,
    pa.paypal_email,
    up.username as under_affiliate

    FROM users
    LEFT JOIN countries ON countries.id = users.Country
    LEFT JOIN users up ON up.id = users.refid
    LEFT JOIN payment_detail pd ON pd.payment_created_by = users.id
    LEFT JOIN paypal_accounts pa ON pa.user_id = users.id
    WHERE
    users.TYPE = "user"
    ORDER BY users.id DESC
    ';

    return $this->db->query($query)->result_array();
}
public function getAllUsersNormal($filter = array()){
    $where = '';
    if(isset($filter['country_id']) && (int)$filter['country_id'] > 0){
        $where .= " AND countries.id = ". (int)$filter['country_id'];
    }
    
    if(isset($filter['user_type']) && $filter['user_type'] != "" && $filter['user_type'] != "all"){
        $where .= " AND users.is_vendor = ". (int)$filter['user_type'];
    }
    if(isset($filter['name'])){
        $where .= " AND (users.firstname like '%". $filter['name'] ."%' OR users.lastname like '%". $filter['name'] ."%') ";
    }
    if(isset($filter['email'])){
        $where .= " AND users.email like '%". $filter['email'] ."%' ";
    }
    if(isset($filter['id_gt']) && (int)$filter['id_gt'] > 0){
        $where .= " AND users.id > ". (int)$filter['id_gt'];
    }

    $limit = '';
    if(isset($filter['limit']) && (int)$filter['limit'] > 0){
        $limit = " LIMIT ". (int)$filter['limit'];
    }

    if(isset($filter['page'])){
        $offset = (int)$filter['limit'] * ((int)$filter['page'] - 1);
        $limit = " LIMIT ". $offset ." ,". (int)$filter['limit'];
    }
 

    $query = 'SELECT
    countries.*,
    users.*,
    up.username as under_affiliate

    FROM users
    LEFT JOIN countries ON countries.id = users.Country
    LEFT JOIN users up ON up.id = users.refid
    WHERE
    users.TYPE = "user" '. $where .'
    ORDER BY users.id DESC
    '. $limit;

    $json['data'] =  $this->db->query($query)->result_array();
    $query = 'SELECT count(users.id) as total
    FROM users
    LEFT JOIN countries ON countries.id = users.Country
    LEFT JOIN users up ON up.id = users.refid
    WHERE users.TYPE = "user" '. $where ;

    $json['total'] = $this->db->query($query)->row()->total;
    return $json;

}
function getPopulerUsers($filter = array(),$popular_aff_filter=''){

    $where = '';
    if(isset($filter['country_id']) && (int)$filter['country_id'] > 0){
        $where .= " AND countries.id = ". (int)$filter['country_id'];
    }
    if(isset($filter['name'])){
        $where .= " AND (users.firstname like '%". $filter['name'] ."%' OR users.lastname like '%". $filter['name'] ."%') ";
    }
    if(isset($filter['email'])){
        $where .= " AND users.email like '%". $filter['email'] ."%' ";
    }
    if(isset($filter['id_gt']) && (int)$filter['id_gt'] > 0){
        $where .= " AND users.id > ". (int)$filter['id_gt'];
    }
    if($popular_aff_filter == 'Week'){
        $startDate=date('Y-m-d',strtotime('-1 week'));
        $endDate=date('Y-m-d');
        $where .= " AND date(wallet.created_at) BETWEEN date('".$startDate."') AND date('".$endDate."')";
    }
    if($popular_aff_filter == 'Month'){
        $startDate=date('Y-m-d',strtotime('-1 month'));
        $endDate=date('Y-m-d');
        $where .= " AND date(wallet.created_at) BETWEEN date('".$startDate."') AND date('".$endDate."')";
    }
    if($popular_aff_filter == 'Year'){
        $startDate=date('Y-m-d',strtotime('-1 year'));
        $endDate=date('Y-m-d');
        $where .= " AND date(wallet.created_at) BETWEEN date('".$startDate."') AND date('".$endDate."')";
    }




    $limit = '';
    if(isset($filter['limit']) && (int)$filter['limit'] > 0){
        $limit = " LIMIT ". (int)$filter['limit'];
    }

    if(isset($filter['page'])){
        $offset = (int)$filter['limit'] * ((int)$filter['page'] - 1);
        $limit = " LIMIT ". $offset ." ,". (int)$filter['limit'];
    }


    $query = '
    SELECT sum(wallet.amount) as amount,sum(wallet.amount) as all_commition, wallet.user_id, users.type, users.avatar, users.firstname, users.lastname, users.Country, users.email, countries.sortname
    FROM wallet 
    LEFT JOIN users ON wallet.user_id = users.id
    LEFT JOIN countries ON countries.id = users.Country
    WHERE wallet.status > 0 AND wallet.type NOT IN("vendor_sale_commission") AND users.type = "user" '.$where.'
    GROUP BY wallet.user_id
    ORDER BY amount DESC
    '. $limit;
    return  $this->db->query($query)->result_array();

    $dataUsers = [];

    if(!isset($filter['page'])){
        $dataUsers= $this->db->query($query)->result_array();
    } else{
        $json['data'] =  $this->db->query($query)->result_array();
        $query = 'SELECT count(users.id) as total
        FROM users
        LEFT JOIN countries ON countries.id = users.Country
        LEFT JOIN users up ON up.id = users.refid
        WHERE users.TYPE = "user" '. $where ;

        $json['total'] = $this->db->query($query)->row()->total;
        return $json;
    }

    $this->load->model('Total_model');
    $filterData = [];
    foreach ($dataUsers as $key => $value) {
        $filterData[$key] = $value;
        $filterData[$key]['amount'] = $this->Total_model->getUserBalance($value['id']);
    }

    return $filterData;

}
function getAllUsers($filter = array()){

    $where = '';

    if(isset($filter['reg_approved']) && $filter['reg_approved'] !== null){
        $where .= " AND users.reg_approved = ".$filter['reg_approved'];
    }

    if(isset($filter['country_id']) && (int)$filter['country_id'] > 0){
        $where .= " AND countries.id = ". (int)$filter['country_id'];
    }
    if(isset($filter['name'])){
        $where .= " AND (users.firstname like '%". $filter['name'] ."%' OR users.lastname like '%". $filter['name'] ."%') ";
    }
    if(isset($filter['email'])){
        $where .= " AND users.email like '%". $filter['email'] ."%' ";
    }
    if(isset($filter['id_gt']) && (int)$filter['id_gt'] > 0){
        $where .= " AND users.id > ". (int)$filter['id_gt'];
    }
    if(isset($filter['groups']) && !empty($filter['groups'])){
        $grpQuery = "";
        foreach($filter['groups'] as $group) {
            if($grpQuery == "") {
                $grpQuery .= " users.groups LIKE '%".$group."%' ";
            } else {
                $grpQuery .= " OR users.groups LIKE '%".$group."%' ";
            }
        }

        if($grpQuery != "") {
            $where .= " AND ( ".$grpQuery." ) ";
        }
    }

    $limit = '';
    if(isset($filter['limit']) && (int)$filter['limit'] > 0){
        $limit = " LIMIT ". (int)$filter['limit'];
    }

    if(isset($filter['page'])){
        $offset = (int)$filter['limit'] * ((int)$filter['page'] - 1);
        $limit = " LIMIT ". $offset ." ,". (int)$filter['limit'];
    }

    $query = 'SELECT
    countries.*,
    users.*,
    up.username as under_affiliate,
    mp.commission_sale_status as user_plan_comission_sale_status,
    (SELECT `level_number` FROM `award_level` WHERE `mp`.`level_id` = `award_level`.`id`) as user_plan_level,
    al.level_number as user_level,
    mp.name as membership_plan,
    mu.id as membership_plan_id
    FROM users
    LEFT JOIN countries ON countries.id = users.Country
    LEFT JOIN users up ON up.id = users.refid
    LEFT JOIN award_level al ON al.id = users.level_id
    LEFT JOIN membership_user mu ON mu.user_id = users.id AND is_active=1
    LEFT JOIN membership_plans mp ON mu.plan_id = mp.id
    WHERE users.TYPE = "user" '. $where .'
    ORDER BY users.id DESC
    '. $limit;

    $json['data'] =  $this->db->query($query)->result_array();

    foreach ($json['data'] as &$user) {
        $user['all_commition'] = $this->db->query("SELECT sum(amount) as all_commition FROM wallet WHERE status > 0 AND user_id = ".$user['id']."")->row()->all_commition; 
        $user['paid_commition'] = $this->db->query("SELECT sum(amount) as paid_commition FROM wallet WHERE status = 3 AND user_id = ".$user['id']."")->row()->paid_commition;
        $user['in_request_commiton'] = $this->db->query("SELECT sum(amount) as in_request_commiton FROM wallet WHERE status = 2 AND user_id = ".$user['id']."")->row()->in_request_commiton; 
        $user['unpaid_commition'] = $this->db->query("SELECT sum(amount) as unpaid_commition FROM wallet WHERE status IN(1,2) AND user_id = ".$user['id']."")->row()->unpaid_commition;

        $user['click_commission'] = $this->db->query("SELECT SUM(amount) as click_commission FROM wallet WHERE type IN ('click_commission','external_click_commission','form_click_commission','affiliate_click_commission') AND is_action=0 AND user_id = ".$user['id']."")->row()->click_commission; 

        $user['action_click_commission'] = $this->db->query("SELECT SUM(amount) as action_click_commission FROM wallet WHERE type IN ('external_click_commission') AND is_action=1 AND status > 0 AND user_id = ".$user['id']."")->row()->action_click_commission; 

        $user['external_action_click'] = $this->db->query("SELECT COUNT(amount) as external_action_click FROM wallet WHERE type IN ('external_click_commission') AND is_action=1 AND status > 0 AND user_id = ".$user['id']."")->row()->external_action_click; 

        $user['click'] = $this->db->query("SELECT COUNT(action_id) as click FROM product_action WHERE user_id = ".$user['id']."")->row()->click; 

        $user['external_click'] = $this->db->query("SELECT COUNT(id) as external_click FROM integration_clicks_action WHERE is_action=0 AND user_id = ".$user['id']."")->row()->external_click; 

        $user['form_click'] = $this->db->query("SELECT COUNT(action_id) as form_click FROM form_action WHERE user_id = ".$user['id']."")->row()->form_click; 

        $user['aff_click'] = $this->db->query("SELECT COUNT(id) as aff_click FROM affiliate_action WHERE user_id = ".$user['id']."")->row()->aff_click; 

        $user['sale_commission'] = $this->db->query("SELECT SUM(amount) as sale_commission FROM wallet WHERE type IN ('sale_commission') AND status > 0 AND user_id = ".$user['id']."")->row()->sale_commission; 

        $user['amount'] = $this->db->query("SELECT SUM(order.total) as amount FROM `order` LEFT JOIN `order_products` ON (order.id = order_products.order_id) WHERE order_products.refer_id = ".$user['id']." AND ((order.payment_method='bank_transfer' AND order.status = 1) OR (order.payment_method != 'bank_transfer' AND order.status > 0))")->row()->amount; 

        $user['external_sale_amount'] = $this->db->query("SELECT SUM(io.total) as external_sale_amount FROM `integration_orders` io WHERE io.status > 0 AND io.user_id = ".$user['id']."")->row()->external_sale_amount;

        if(!empty($user['groups'])) {
           $user['groups_name'] = $this->db->query('SELECT group_name from user_groups where id in ('.$user['groups'].')')->result_array(); 
       } else {
        $user['groups_name'] = null;
    }

}

if(!isset($filter['page'])){
    return $json['data'];
} else{
    $query = 'SELECT count(users.id) as total
    FROM users
    LEFT JOIN countries ON countries.id = users.Country
    LEFT JOIN users up ON up.id = users.refid
    WHERE users.TYPE = "user" '. $where ;

    $json['total'] = $this->db->query($query)->row()->total;
    return $json;
}
}
public function getAvatar($image){
    if ($image != '')
        return base_url('assets/images/users/'. $image);
    else
        return base_url('assets/vertical/assets/images/users/avatar-1.jpg');
}
public $level_count = 0;
public function getAllUsersTree($filter = array()){
    $children = array();
    $this->level_count = 0;

    $where = '';
    $admin_result= $this->db->query("SELECT id,CONCAT(firstname,' ',lastname) as name,avatar,refid FROM users WHERE type='admin' AND refid = 0")->row();
    if(isset($filter['user_id'])){
        $where .= " AND id=". (int)$filter['user_id'];
    } else {
        $where .= " AND (refid = 0 or refid = ". (int)$admin_result->id ." )";
    }


    $result = $this->db->query("SELECT id,CONCAT(firstname,' ',lastname) as name,avatar,refid FROM users WHERE type='user'  {$where}")->result_array();

    foreach ($result as $key => $value) {
        $c = $this->getAllUsersTreeChildren($value['id'], $filter);
        $children[] = array(
            'text' => array(
                'name' => $value['name']
            ),
            'image' => $this->getAvatar($value['avatar']),
            'collapsed' => count($c) ? true : false,
            'children' => $c,
        );
    }


    $tree = array(
        'text' => array(
            'name' => $admin_result->name
        ),
        'image' => $this->getAvatar($admin_result->avatar),
        'collapsed' => true,
        'children' => $children
    );

    return $tree;
}
public function getAllUsersTreeV2($filter = array()){
    $children = array();
    $this->level_count = 0;

    $where = '';
    $admin_result= $this->db->query("SELECT id,username as name,avatar,refid FROM users WHERE type='admin'")->row();
    if(isset($filter['user_id'])){
        $where .= " AND id=". (int)$filter['user_id'];
    } else {
            //$where .= " AND (refid = 0 or refid = ". (int)$admin_result->id ." )";
    }


    $result = $this->db->query("SELECT id,username as name,avatar,refid FROM users WHERE type='user'  {$where}")->result_array();

    $children[] = array(
        array(
            'v' => $admin_result->id,
            'f' => $admin_result->name ."<img class='user-avtar-tree' src='". $this->getAvatar($admin_result->avatar) ."'>",
        ),
        $admin_result->id,
        $admin_result->name,
    );

    foreach ($result as $key => $value) {

        $children[] = array(
            array(
                'v' => $value['id'],
                'f' => $value['name'] ."<img class='user-avtar-tree' src='". $this->getAvatar($value['avatar']) ."'>"
            ),
            ((int)$value['refid'] != 0 ? $value['refid'] : $admin_result->id),
            $value['name']
        );
    }
    return $children;
}
private function buildTree(array $elements, $parentId = 0) {
    $branch = array();

    foreach ($elements as $element) {
        if ($element['parent_id'] == $parentId) {
            $children = $this->buildTree($elements, $element['id']);
            if ($children) {
                $element['children'] = $children;
            }
            $branch[] = $element;
        }
    }

    return $branch;
}

public function getAllAwardLevel($limit = false,$offset = 0){
    $ci = & get_instance();

    $sql = "SELECT `child`.`id`,   
    `child`.`level_number`,
    `child`.`jump_level`, 
    `parent`.`level_number` as 'jump_level_name',  
    `child`.`minimum_earning`,
    `child`.`sale_comission_rate`,
    `child`.`bonus`,
    `child`.`default_registration_level`
    FROM `award_level` as `child` 
    LEFT JOIN `award_level` as `parent` 
    ON `parent`.`id` = `child`.`jump_level`";

    if($limit != false){
      $sql .= " LIMIT ? OFFSET ?";
      $query = $ci->db->query($sql,array((int)$limit,(int)$offset));
  } else {
      $query = $ci->db->query($sql);
  }

  $result = $query->result_array();

  return $result;
}

public function checkJumpLevel($jump_level,$id = false){
    $ci = & get_instance();

    $sql = "SELECT `id`
    FROM `award_level`
    WHERE `jump_level` = ? ";

    if($id != false){
        $sql .= " AND `id` != ? ";
        $query = $ci->db->query($sql,array((int) $jump_level,(int) $id));
    } else {
        $query = $ci->db->query($sql,(int) $jump_level);
    }

    $result = $query->result_array();

    return $result;
}

public function getAllinOneQuery($filter = array()){
    $_children = [];
    
    $admin_result= $this->db->query("SELECT id,username as name,avatar,refid FROM users WHERE type='admin'")->row_array();
    
    $_children[] = array(
        'id'        => $admin_result['id'],
        'parent_id' => 0,
        'name'      => $admin_result['name'] ."<img class='user-avtar-tree' src='". $this->getAvatar($admin_result['avatar']) ."'>",
    );

    $users= $this->db->query("SELECT id,username as name,avatar,refid FROM users WHERE type='user'")->result_array();
    
    foreach ($users as $key => $value) {
        $_children[] = array(
            'id'        => $value['id'],
            'parent_id' => $value['refid'] ? $value['refid'] : $admin_result['id'],
            'name'      => $value['name'] ."<img class='user-avtar-tree' src='". $this->getAvatar($value['avatar']) ."'>"
        );
    }

    return $this->buildTree($_children);
}
public function getAllUsersTreeV3($filter = array(), $user_id = 0, $first_time = true, $is_admin = false){
    $children = array();

    if(!$is_admin && $first_time){
        $this->level_count = 0;
        $setting = $this->Product_model->getSettings('referlevel');
        $this->max_level = isset($setting['levels']) ? $setting['levels'] : 3;
    }

    $this->max_level=25;

    if($this->level_count >= $this->max_level) return [];

    $where = '';
    if(isset($filter['user_id'])){
        $where .= " AND id=". (int)$filter['user_id'];
    }

    $children = [];

    $users= $this->db->query("SELECT id,username as name,avatar,refid
     FROM users WHERE type='user' {$where} AND  refid = ". $user_id)->result_array();

    foreach ($users as $key => $value) {
        if($key == 0) {
            $this->level_count++;
        }

        $value['children'] = $this->getAllUsersTreeV3($filter, $value['id'], false, $is_admin);
        
        $children[] = array(
            'name'  => $value['name'] ."<img class='user-avtar-tree' src='". $this->getAvatar($value['avatar']) ."'>",
            'children' => $value['children'],
        );
    }

    if($first_time){
        $user_result = $this->db->query("SELECT id,username as name,avatar,refid FROM users WHERE id={$user_id} AND type='user'")->row_array();

        $user_children[] = array(
            'name'  => $user_result['name'] ."<img class='user-avtar-tree' src='". $this->getAvatar($user_result['avatar']) ."'>",
            'children' => $children,
        );

        return $user_children;
    }

    return $children;
}

public function getAllUsersTreeV2ForUser($user_id, $first_time = true){
        $children = array();
        $result = $this->db->query("SELECT id,username as name,avatar,refid FROM users WHERE type='user'  AND refid=". (int)$user_id)->result_array();

        if($first_time){
            $admin_result= $this->db->query("SELECT id,username as name,avatar,refid FROM users WHERE type='admin'")->row();
            $children[] = array(
                array(
                    'v' => $admin_result->id,
                    'f' => $admin_result->name ."<img class='user-avtar-tree' src='". $this->getAvatar($admin_result->avatar) ."'>"
                ),
                $admin_result->id,
                $admin_result->name,
            );

            $result = $this->db->query("SELECT id,username as name,avatar,refid FROM users WHERE type='user'  AND id=". (int)$user_id)->result_array();

            $children[] = array(
                array(
                    'v' => $result->id,
                    'f' => $result->name ."<img class='user-avtar-tree' src='". $this->getAvatar($result->avatar) ."'>"
                ),
                $admin_result->id,
                $result->name
            );

            $user_id = $admin_result->id;
            $this->level_count = 0;
        }

        $this->level_count++;

        $setting = $this->Product_model->getSettings('referlevel');
        $max_level = isset($setting['levels']) ? $setting['levels'] : 3;

        if($this->level_count >= ($max_level)+2) return array();

        foreach ($result as $key => $value) {
            $_children = $this->getAllUsersTreeV2ForUser($value['id'], false);

            $children[] = array(
                array(
                    'v' => $value['id'],
                    'f' => $value['name'] ."<img class='user-avtar-tree' src='". $this->getAvatar($value['avatar']) ."'>"
                ),
                $user_id,
                $value['name']
            );

            $children = array_merge($_children,$children);
        }

        return $children;
    }
    public function getAllUsersTreeChildren($parent, $filter){
        $children = array();
        $this->level_count ++;

        if($this->level_count <= 3 || !isset($filter['user_id'])){
            $result = $this->db->query("SELECT id,CONCAT(firstname,' ',lastname) as name,avatar,refid FROM users WHERE type='user' AND refid = {$parent}")->result_array();

            foreach ($result as $key => $value) {
                $c = $this->getAllUsersTreeChildren($value['id'], $filter);

                $children[] = array(
                    'text' => array(
                        'name' => $value['name']
                    ),
                    'image' => $this->getAvatar($value['avatar']),
                    'children' => $c,
                    'collapsed' => count($c) ? true : false,
                );
            }

            return $children;
        }

        return array();
    }

    function getAllClients($filter){
        $query = '
        SELECT
        users.*,

        (SELECT CONCAT(firstname, " " ,lastname) FROM users u WHERE u.id = users.refid) as ref_user,
        (SELECT COUNT(action_id) FROM product_action WHERE user_id = users.id) as click,
        (SELECT name FROM countries WHERE id = users.Country LIMIT 1) as country_name,
        (SELECT name FROM states WHERE id = users.StateProvince LIMIT 1) as state_name,
        (SELECT SUM(o.total) FROM `order` o WHERE  o.user_id = users.id AND o.status > 0) as amount ,
        (SELECT COUNT(o.id) FROM `order` o WHERE  o.user_id = users.id AND o.status > 0) as total_sale ,
        (SELECT SUM(amount) FROM product_action WHERE type IN ("click_commission","sale_commission") AND user_id = users.id) as commission
        FROM  users
        WHERE TYPE IN ("client","guest")
        ORDER BY id DESC';

        $total = $this->db->query($query)->num_rows(); 

         if (isset($filter['page'],$filter['limit'])) {
            $offset = (($filter['page']-1) * $filter['limit']);
            $query.= " LIMIT {$offset},". $filter['limit'];
        }
        
        $list = $this->db->query($query)->result_array(); 
        return array($list,$total);
    }

    function getVendorClients($filter){

        $vendor_id=$filter["vendor_id"];
        $query = '
        SELECT
        DISTINCT users.*,

        (SELECT CONCAT(firstname, " " ,lastname) FROM users u WHERE u.id = users.refid) as ref_user,
        (SELECT COUNT(action_id) FROM product_action WHERE user_id = users.id) as click,
        (SELECT name FROM countries WHERE id = users.Country LIMIT 1) as country_name,
        (SELECT name FROM states WHERE id = users.StateProvince LIMIT 1) as state_name,
        (SELECT SUM(o.total) FROM `order` o  inner JOIN `order_products` p on p.`order_id`=o.id and p.`vendor_id`='. $vendor_id.'  WHERE  o.user_id = users.id AND o.status > 0) as amount ,
        (SELECT COUNT(o.id) FROM `order` o WHERE  o.user_id = users.id AND o.status > 0) as total_sale 
        FROM  users
        inner join `order` on `order`.user_id=users.id  
        inner JOIN `order_products` on `order_products`.`order_id`=`order`.id and `order_products`.`vendor_id`='. $vendor_id.'
        WHERE TYPE IN ("client","guest")
        ORDER BY id DESC';

        $total = $this->db->query($query)->num_rows(); 
 
         if (isset($filter['page'],$filter['limit'])) {
            $offset = (($filter['page']-1) * $filter['limit']);
            $query.= " LIMIT {$offset},". $filter['limit'];
        }
        
        $list = $this->db->query($query)->result_array(); 
        return array($list,$total);
    }

    function checkmail($email, $user_id = null){
        if ($user_id) {
            $this->db->where('id !=', $user_id);
        }
        return $this->db->get_where('users', array('email' => $email))->result_array();
    }
    function checkuser($username, $user_id = null){
        if ($user_id) {
            $this->db->where('id !=', $user_id);
        }
        return $this->db->get_where('users', array('username' => $username))->result_array();
    }
    function getAllUserrecord(){
        $this->db->select('countries.*, users.*');
        $this->db->from('users');
        $this->db->where('users.type', 'user');
        $this->db->join('countries', 'countries.id = users.Country', 'left');
        $query = $this->db->get();
        return $query->result_array();
    }

    
    function process_approval($data) {
        $where = "";
        
        if(is_array($data['users_ids'])){
            foreach ($data['users_ids'] as $id) {
                $where .= " OR id=".$id." ";
            }
        }else{
            $where .= " OR id=".$data['users_ids']." ";
        }

        if($this->db->query("UPDATE users SET reg_approved = ".$data['reg_approved']." WHERE id=0 ".$where)){
            $affected_rows = $this->db->affected_rows();
            $msg_suffix = ($affected_rows > 1) ? ' for '.$affected_rows.' users' : '';
            
            $status = ($data['reg_approved'] == 1) ? 'approved' : 'declined';
            
            if(is_array($data['users_ids'])){
                foreach ($data['users_ids'] as $id) {
                    $this->db->query('UPDATE wallet SET status=1 WHERE reference_id IN ('.implode(',', $data['users_ids']).') AND type="refer_registration_commission" AND status=0');
                }
            }else{
                $this->db->query('UPDATE wallet SET status=1 WHERE reference_id='.$data['users_ids'].' AND type="refer_registration_commission" AND status=0');
            }

            return array('status' => true, 'message' => "Registration has been ".$status." ".$msg_suffix);
        } else {
            return array('status' => false, 'message' => "something went wrong, please try again!");
        }
    }

    function getApprovalCounts(){
        $data = [];
        $this->db->select('count(*) as total');
        $this->db->from('users');
        $this->db->where('users.type', "user");
        $query = $this->db->get();
        $data['total'] = $query->row()->total;
        $this->db->select('count(*) as total');
        $this->db->from('users');
        $this->db->where('users.reg_approved', 0);
        $this->db->where('users.type', "user");
        $query = $this->db->get();
        $data['pending'] = $query->row()->total;
        $this->db->select('count(*) as total');
        $this->db->from('users');
        $this->db->where('users.type', "user");
        $this->db->where('users.reg_approved', 1);
        $query = $this->db->get();
        $data['approved'] = $query->row()->total;
        $this->db->select('count(*) as total');
        $this->db->from('users');
        $this->db->where('users.type', "user");
        $this->db->where('users.reg_approved', 2);
        $query = $this->db->get();
        $data['declined'] = $query->row()->total;
        return $data;
    }

    function getAllUserrecordCount(){
        $this->db->select('count(*) as total');
        $this->db->from('users');
        $this->db->where('users.type', 'user');
        $query = $this->db->get();

        return $query->row()->total;
    }
    function getAllClientrecord($type='client'){
        $this->db->order_by('created_at', 'desc');
        return $this->db->get_where('users', array('type' => "$type"))->result_array();
    }
    function getAllUserNew(){
        $this->db->select(array(
            'countries.*',
            'users.*',
            '(SELECT sum(amount) FROM wallet WHERE status = 3 AND wallet.user_id = users.id AND type IN("click_commission","sale_commission")) as paid_commition',
            '(SELECT sum(amount) FROM wallet WHERE status = 1 AND wallet.user_id = users.id AND type IN("click_commission","sale_commission")) as unpaid_commition',
            '(SELECT SUM(o.total) FROM `order` o LEFT JOIN order_products op ON (o.id = op.order_id) WHERE  op.refer_id = users.id AND o.status > 0) as amount',
            '(SELECT COUNT(action_id) FROM product_action WHERE user_id = users.id) as click',
            '(SELECT SUM(amount) FROM wallet WHERE type IN ("click_commission") AND user_id = users.id) as click_commission',
            '(SELECT SUM(amount) FROM wallet WHERE type IN ("sale_commission") AND user_id = users.id) as sale_commission',
            '(SELECT SUM(amount) FROM wallet WHERE type IN ("affiliate_click_commission") AND user_id = users.id) as aff_click_commission',
            '(SELECT COUNT(id) FROM affiliate_action WHERE user_id = users.id) as aff_click',
        ));

        $this->db->from('users');
        $this->db->where('users.type', 'user');
        $this->db->join('countries', 'countries.id = users.Country', 'left');
        $this->db->order_by('users.created_at', 'DESC');
        $this->db->limit(10);
        $query = $this->db->get();
        return $query->result_array();
    }
    function getAllClientNew(){
        $this->db->select('
            countries.*,
            users.*,
            (SELECT SUM(total) FROM `order` WHERE order.user_id = users.id AND status > 0 ) as buy_product_amount,
            (SELECT count(total) FROM `order` WHERE order.user_id = users.id AND status > 0 ) as buy_product
            ');
        $this->db->from('users');
        $this->db->where('users.type', 'client');
        $this->db->join('countries', 'countries.id = users.Country', 'left');
        $this->db->order_by('users.created_at', 'DESC');
        $this->db->limit(10);
        $query = $this->db->get();
        return $query->result_array();
    }
    function getReview($product_id){
        $this->db->select('product.product_name, rating.*, users.avatar, users.firstname, users.lastname');
        $this->db->order_by('rating_created', 'desc');
        $this->db->from('rating');
        $this->db->where('products_id', $product_id);
        $this->db->join('product', 'product.product_id = rating.products_id');
        $this->db->join('users', 'users.id = rating.rating_user_id');
        $query = $this->db->get();
        return $query->result_array();
    }

    function getReviewById($rating_id){
        $this->db->select('product.product_name, rating.*, users.avatar, users.firstname, users.lastname');
        $this->db->order_by('rating_created', 'desc');
        $this->db->from('rating'); 
        $this->db->where('rating_id', $rating_id);
        $this->db->join('product', 'product.product_id = rating.products_id');
        $this->db->join('users', 'users.id = rating.rating_user_id');
        $query = $this->db->get();
        return $query->result_array();
    }

    function getAllReview($filter=null){
        $this->db->select('product.product_name, rating.*, users.avatar, users.firstname, users.lastname');
        $this->db->order_by('rating_created', 'desc');
        $this->db->from('rating'); 
        $this->db->join('product', 'product.product_id = rating.products_id');
        $this->db->join('users', 'users.id = rating.rating_user_id');
         if(isset($filter) && isset($filter['product_created_by']))
            $this->db->where('product.product_created_by', $filter['product_created_by']); 
        $query = $this->db->get(); 
        return $query->result_array();

    }

    function getAllReviewFilter($product_id=null,$limit=20, $offset=0,$filter=null){
        $this->db->select('product.product_name, rating.*, users.avatar, users.firstname, users.lastname');
        $this->db->order_by('rating_created', 'desc');
        $this->db->from('rating'); 
        $this->db->join('product', 'product.product_id = rating.products_id');
        $this->db->join('users', 'users.id = rating.rating_user_id');

        if(isset($filter) && isset($filter['product_created_by']))
            $this->db->where('product.product_created_by', $filter['product_created_by']);    
        
        if($product_id>0)
        $this->db->where('rating.products_id', $product_id);

        $query = $this->db->get(); 
        $res["total"]=$query->num_rows();

        $this->db->select('product.product_name, rating.*, users.avatar, users.firstname, users.lastname');
        $this->db->order_by('rating_created', 'desc');
        $this->db->from('rating'); 
        $this->db->join('product', 'product.product_id = rating.products_id');
        $this->db->join('users', 'users.id = rating.rating_user_id');

        if(isset($filter) && isset($filter['product_created_by']))
        $this->db->where('product.product_created_by', $filter['product_created_by']);    

        if($product_id>0)
        $this->db->where('rating.products_id', $product_id);

 
        $offset = $limit * ($offset-1);
        $this->db->limit($limit, $offset);
        $query = $this->db->get(); 
        $res["reviews"]=$query->result_array();
        return $res;
    }

    function deleteReview($rating_id){
        $this->db->select('products_id'); 
        $this->db->where('rating_id', $rating_id);
        $this->db->from('rating');  
        $query = $this->db->get();
        $row= $query->row_array();
        $res=null;
        if(count($row)>0)
        {
            $product_id=$row['products_id'];
            $this->db->where('rating_id', $rating_id); 
            $res= $this->db->delete('rating');
            $this->update_avg_rating($product_id);
        }
       return $res;
    }

    function getAllUserOnline(){
        $this->db->order_by('created_at', 'desc');
        $this->db->limit(7);
        return $this->db->get_where('users', array('type' => 'user', 'online' => 1))->result_array();
    }

     function getSettings($type='', $key = "") {
        $language = 0;
        if ( $this->db->field_exists('language_id', 'setting') ) $language = 1;

        $settingdata = array();
        if( $language == 1 ) {
            if( $key != "" ) {
                $this->db->where(['setting_type'=> $type, 'setting_key'=> $key, 'language_id'=> 1]);
            } else {
                $this->db->where(['setting_type'=> $type, 'language_id'=> 1]);
            }
        } else {
            if( $key != "" ) {
                $this->db->where(['setting_type'=> $type, 'setting_key'=> $key]);
            } else {
                $this->db->where(['setting_type'=> $type]);
            }
        }

        $getSetting = $this->db->get_where('setting', array('setting_status' => 1))->result_array();
        foreach ($getSetting as $setting) {
            $settingdata[$setting['setting_key']] = $setting['setting_value'];
        }
        return $settingdata;
    }

    function getSettingsWithLanaguage( $type = '', $language_id = 1, $key = "" ) {
        $language = 0;
        if ( $this->db->field_exists('language_id', 'setting') ) $language = 1;

        $settingdata = array();
        if( $language == 1 ) {
            if( $key != "" ) {
                $this->db->where(['setting_type'=> $type, 'setting_key'=> $key, 'language_id'=> $language_id]);
            } else {
                $this->db->where(['setting_type'=>  $type, 'language_id'=> $language_id]);
            }
        } else {
             if( $key != "" ) {
                $this->db->where(['setting_type'=> $type, 'setting_key'=> $key]);
            } else {
                $this->db->where(['setting_type'=>  $type]);
            }
        }
       
        $getSetting = $this->db->get_where('setting', array('setting_status' => 1))->result_array();
        foreach ($getSetting as $setting) {
            $settingdata[$setting['setting_key']] = $setting['setting_value'];
        }

        $settingdata['language_id'] = $language_id; 

        return $settingdata;
    }

    function getFrontThemeSettings($type='', $key = ""){
        $settingdata = array();
        if($key != "") {
            $this->db->where(['setting_type'=> $type, 'setting_key'=> $key]);
        } else {
            $this->db->where('setting_type', $type);
        }
        $getSetting = $this->db->get_where('theme_colors', array('setting_status' => 1))->result_array();
        foreach ($getSetting as $setting) {
            $settingdata[$setting['setting_key']] = $setting['setting_value'];
        }
        return $settingdata;
    }

    function getVendorSettings($user_id, $type='', $key = ""){
        $settingdata = array();
        if($key != "") {
            $this->db->where(['user_id'=> $user_id, 'setting_type'=> $type, 'setting_key'=> $key]);
        } else {
            $this->db->where(['user_id'=> $user_id, 'setting_type'=> $type]);
        }
        $getSetting = $this->db->get_where('vendor_config', array('setting_status' => 1))->result_array();
        foreach ($getSetting as $setting) {
            $settingdata[$setting['setting_key']] = $setting['setting_value'];
        }
        return $settingdata;
    }

    function getSettingsWhereIn($array=''){
        $settingdata = array();

        $setting_types = implode("','", $array);

        $query = $this->db->query('SELECT * FROM setting WHERE setting_status=1 AND setting_type IN (\''.$setting_types.'\')');

            $getSetting = $query->result_array();

            foreach ($getSetting as $setting) {
                if(!isset($settingdata[$setting['setting_type']])) {
                    $settingdata[$setting['setting_type']] = [];
                }
                $settingdata[$setting['setting_type']][$setting['setting_key']] = $setting['setting_value'];
            }
            return $settingdata;
        }

        function getVendorSettingsWhereIn($user_id, $array=''){
            $settingdata = array();

            $setting_types = implode("','", $array);

            $query = $this->db->query('SELECT * FROM vendor_config WHERE user_id = ? AND setting_status=1 AND setting_type IN (\''.$setting_types.'\')',(int) $user_id);

                $getSetting = $query->result_array();


                foreach ($getSetting as $setting) {
                    if(!isset($settingdata[$setting['setting_type']])) {
                        $settingdata[$setting['setting_type']] = [];
                    }
                    $settingdata[$setting['setting_type']][$setting['setting_key']] = $setting['setting_value'];
                }
                return $settingdata;
            }

            function deletesetting($key, $value, $type){
                $this->db->where('setting_key', $key);
                $this->db->where('setting_type', $type);
                return $this->db->delete('setting');
            }
            function getrefUsers($user_id = null){
                if ($user_id) {
                    return $this->db->get_where('users', array('refid' => $user_id,'type' => 'user'))->result_array();
                }
                return false;
            }
            function getAllPayment($id){
                if ($id) {
                    return $this->db->get_where('payment_detail', array('payment_status' => 1, 'payment_created_by' => $id))->result_array();
                }
                return false;
            }
            function getPaypalAccounts($user_id){
                $this->db->from("paypal_accounts");
                $this->db->where("user_id", (int)$user_id);
                return $this->db->get()->result_array();
            }
            function getAllBuyProduct($payment_user_id = null){
                if ($payment_user_id) {
                    $this->db->join('users', 'users.id=order.user_id');
                    $this->db->join('order_products', 'order_products.order_id=order.id');
                    $this->db->join('product', 'product.product_id=order_products.product_id');
                    $this->db->where('user_id', $payment_user_id);
                    $this->db->where('status > 0');
                    return $this->db->get('order')->result_array();
                }
            }
            function getAllRefBuyProduct($payment_user_id = null){
                if ($payment_user_id) {
                    $this->db->join('users', 'users.id=payment.payment_ref_user_id');
                    $this->db->join('product', 'product.product_id=payment.payment_item_id');
                    $this->db->where('payment_ref_user_id', $payment_user_id);
                    return $this->db->get('payment')->result_array();
                }
            }
            function getorderById($order_id){
                if ($order_id) {
                    $this->db->where('payment_id', $order_id);
                    $this->db->join('users', 'users.id=payment.payment_user_id');
                    $this->db->join('product', 'product.product_id=payment.payment_item_id');
                    return $this->db->get_where('payment')->row_array();
                }
            }
            function getcommentById($order_id){
                if ($order_id) {
                    $this->db->where('payment_comments_action_id', $order_id);
                    return $this->db->get_where('payment_comments')->result_array();
                }
            }
            function orderdelete($order_id = null){
                if ($order_id) {
                    $this->db->where('payment_id', $order_id);
                    $this->db->delete('payment');
                }
                if ($order_id) {
                    $this->db->where('payment_comments_action_id', $order_id);
                    $this->db->delete('payment_comments');
                }
            }
            function userdelete($user_id = null, $type = null){
                if ($user_id) {
                    $this->db->where('id', $user_id);
                    $this->db->where('type', $type);
                    $this->db->delete('users');
                    $this->db->where('productslog_user_id', $user_id);
                    $this->db->delete('productslog');
        }
    }
    function getallorders($user_id = null){

        return array();
    }
    function getallsales($user_id = null){
        if(!empty($user_id)){
            $this->db->where('payment_user_id', $user_id);
        }
        $this->db->join('users', 'users.id=payment.payment_user_id');
        $this->db->join('product', 'product.product_id=payment.payment_item_id');
        return $this->db->get_where('payment', array('payment_item_status' => 'Completed'))->result_array();
    }
    function getallPercentageByallorders($user_id = null){
        return 0;
    }
    function getallPercentageByallsales($user_id = null){
        return 0;
    }
    function getcountry($select = '*'){
        $this->db->select($select);
        $query = $this->db->get('countries');
        return $query->result();
    }
    function getAllstate($country_id = ''){
        $this->db->select('states.*');
        $this->db->where('country_id', $country_id);
        $query = $this->db->get('states');
        return $query->result_array();
    }

    function getnotification($viewfor = null, $user_id){
        $this->db->select('notification.*');
        $this->db->where('notification_view_user_id', $user_id);
        $this->db->where('notification_viewfor', $viewfor);
        $this->db->where('notification_is_read', 0);
        $this->db->order_by('notification_id', 'desc');
        $this->db->limit(10);
        $query = $this->db->get('notification');
        return $query->result_array();
    }

    function getnotificationnew($viewfor = null, $user_id, $limit = 0, $filter = array()){
        $this->db->select('notification.*');
        if($user_id > 0){
            $this->db->where(" (notification_view_user_id = {$user_id} OR notification_view_user_id = 'all')  ",NULL,false);
        }

        if (isset($filter['id_gt'])) {
            $this->db->where('notification_id > '. (int)$filter['id_gt']);
        }
        $this->db->where('notification_is_read', 0);
        $this->db->where('notification_viewfor', $viewfor);
        if($viewfor == 'admin')
            $this->db->where(' (notification_view_user_id = "" OR notification_view_user_id = 1 OR notification_view_user_id IS NULL) ');
        $this->db->order_by('notification_id', 'desc');
        if($limit > 0)  $this->db->limit($limit);
        $query = $this->db->get('notification');

        return $query->result_array();
    }

    function getnotificationnew_count($viewfor = null, $user_id){
        $this->db->select('notification.notification_id');
        if($user_id > 0){
            $this->db->where(" (notification_view_user_id = {$user_id} OR notification_view_user_id = 'all')  ",NULL,false);
        }
        $this->db->where('notification_is_read', 0);
        $this->db->where('notification_viewfor', $viewfor);

        if($viewfor == 'admin')
            $this->db->where(' (notification_view_user_id = "" OR notification_view_user_id = 1 OR notification_view_user_id IS NULL) ');

        $query = $this->db->get('notification');
        return $query->num_rows();
    }
    function getnotificationall($viewfor = null, $user_id){
        $this->db->select('notification.*');
        $this->db->where('notification_view_user_id', $user_id);
        $this->db->where('notification_viewfor', $viewfor);
        $this->db->order_by('notification_id', 'desc');
        $query = $this->db->get('notification');
        return $query->result_array();
    }
    function deleteusers($id = null){
        $membership_user = $this->db->query("SELECT GROUP_CONCAT(id) as ids FROM membership_user WHERE user_id = {$id}  GROUP BY user_id")->row();
        
        if(!empty($membership_user->ids)) {
            $this->db->query("DELETE FROM membership_user WHERE id IN ({$membership_user->ids})");
            $this->db->query("DELETE FROM membership_buy_history WHERE buy_id IN ({$membership_user->ids})");
        }
        
        if (!empty($id)) {
            $this->db->where('id', $id);
            
            return $this->db->delete('users');
        }

        return false;
    }
    function deleteproducts($id = null){
        if (!empty($id)) 
        {
            $this->db->query("DELETE FROM product_categories WHERE product_id = {$id} ");
            $this->db->query("DELETE FROM product_affiliate WHERE product_id = {$id} ");
            $this->db->query("DELETE FROM product WHERE product_id = {$id} "); 

            return true;
        }
        return false;
    }
    function deleteImage($id = null){
        if (!empty($id)) {
            $this->db->where('product_media_upload_id', $id);
            return $this->db->delete('product_media_upload');
        }
    }
    function getProductAction($product_id, $user_id, $viewer_id = 0){
        $ip_address = $_SERVER['REMOTE_ADDR'];
        $this->db->from('product_action');
        $this->db->where('product_id', $product_id);

        if($viewer_id) $this->db->where('viewer_id', $viewer_id);

        $this->db->where('user_ip', $ip_address);
        $this->db->where('user_id', $user_id);
        $result = $this->db->get()->num_rows();
        return $result;
    }
    function getFormAction($product_id, $user_id, $viewer_id = 0){
        $ip_address = $_SERVER['REMOTE_ADDR'];
        $this->db->from('form_action');
        $this->db->where('form_id', $product_id);
        if($viewer_id) $this->db->where('viewer_id', $viewer_id);
        $this->db->where('user_ip', $ip_address);
        $this->db->where('user_id', $user_id);
        $result = $this->db->get()->num_rows();
        return $result;
    }
    public function calcCommitions($product, $type= 'sale', $shareUser = []){

        $product = (array)$product;
        $seller = $this->db->query("SELECT * FROM product_affiliate WHERE product_id=". (int)$product['product_id'] ." ")->row();
        $product_price = ((int)$product['quantity'] * ((float)$product['product_price'] + (float)$product['variation_price']));
       
        $vendor_setting = $this->Product_model->getSettings('vendor');

        if($seller && (int)$vendor_setting['storestatus'] == 1){
            $seller_setting = $this->Product_model->getSellerSetting($seller->user_id);

            $data = [
                'type' => '',
                'commission' => 0,

                'admin_commission_type' => '',
                'admin_commission' => 0,
            ];

            if($shareUser && $shareUser['type'] == 'user' && $product['refer_id'] != $product['vendor_id']){
                if($seller->affiliate_sale_commission_type == 'default'){
                    if($seller_setting->affiliate_sale_commission_type == 'percentage'){

                        $award_level = $this->Product_model->getSettings('award_level', 'status');
                        
                        $comission_sale_status = 0;
                        $sale_comission_rate = 0;
                        $userPlan = App\MembershipUser::select('membership_plans.commission_sale_status','award_level.sale_comission_rate')->join('membership_plans','membership_plans.id','=','membership_user.plan_id')->join('award_level','award_level.id','=','membership_plans.level_id','left')->where('is_active',1)->where('user_id',$product['refer_id'])->first();
                        if($userPlan->commission_sale_status){

                            $comission_sale_status = $userPlan->commission_sale_status;
                            $sale_comission_rate = $userPlan->sale_comission_rate;
                        } else {
                            $userdetails = $this->Product_model->getByField('users','id',$product['refer_id']);
                            $userLevel = $this->Product_model->getByField('award_level','id',$userdetails['level_id']);


                            if($userLevel){
                                $comission_sale_status = 1;
                                $sale_comission_rate = $userLevel['sale_comission_rate'];
                            }
                        }
                        if($award_level['status'] && $comission_sale_status && $sale_comission_rate && $sale_comission_rate < $seller_setting->affiliate_commission_value)
                            $affiliate_commission_value = $sale_comission_rate;
                        else 
                            $affiliate_commission_value = (float) $seller_setting->affiliate_commission_value;

                        $data['type'] = 'Percentage ('. $affiliate_commission_value .'%) ';
                        $data['commission'] = max(($product_price * $affiliate_commission_value),0) / 100;
                    }
                    else if($seller_setting->affiliate_sale_commission_type == 'fixed'){
                       
                        $data['type'] = 'Fixed';
                        $data['commission'] = $seller_setting->affiliate_commission_value;
                    }
                } else if($seller->affiliate_sale_commission_type == 'percentage'){
                                       $award_level = $this->Product_model->getSettings('award_level', 'status');

                    $comission_sale_status = 0;
                    $sale_comission_rate = 0;
                    $userPlan = App\MembershipUser::select('membership_plans.commission_sale_status','award_level.sale_comission_rate')->join('membership_plans','membership_plans.id','=','membership_user.plan_id')->join('award_level','award_level.id','=','membership_plans.level_id','left')->where('is_active',1)->where('user_id',$product['refer_id'])->first();
                    if($userPlan->commission_sale_status){
                        $comission_sale_status = $userPlan->commission_sale_status;
                        $sale_comission_rate = $userPlan->sale_comission_rate;
                    } else {
                        $userdetails = $this->Product_model->getByField('users','id',$product['refer_id']);
                        $userLevel = $this->Product_model->getByField('award_level','id',$userdetails['level_id']);
                        if($userLevel){
                            $comission_sale_status = 1;
                            $sale_comission_rate = $userLevel['sale_comission_rate'];
                        }
                    }
                    if($award_level['status'] && $comission_sale_status && $sale_comission_rate && $sale_comission_rate < $seller->affiliate_commission_value)
                        $affiliate_commission_value = $sale_comission_rate;
                    else 
                        $affiliate_commission_value = (float) $seller->affiliate_commission_value;

                    $data['type'] = 'Percentage ('. $affiliate_commission_value  .'%) ';
                    $data['commission'] = max(($product_price * $affiliate_commission_value),0) / 100;

                } else if($seller->affiliate_sale_commission_type == 'fixed'){
                    $data['type'] = 'Fixed';
                    $data['commission'] = (float)$seller->affiliate_commission_value;
                }
            }

            $commnent_line = '';
            if($seller->admin_sale_commission_type == 'default'){
               if($vendor_setting['admin_sale_commission_type'] == 'percentage'){
                $data['admin_commission_type'] = 'Percentage ('. (float)$vendor_setting['admin_commission_value'] .'%) ';
                $data['admin_commission'] = max(($product_price * (float)$vendor_setting['admin_commission_value']),0) / 100;
            }
            else if($vendor_setting['admin_sale_commission_type'] == 'fixed'){
                $data['admin_commission_type'] = 'Fixed';
                $data['admin_commission'] = $vendor_setting['admin_commission_value'];
            }

        } else if($seller->admin_sale_commission_type == 'percentage'){
            $data['admin_commission_type'] = 'Percentage ('. (float)$seller->admin_commission_value .'%) ';
            $data['admin_commission'] = max(($product_price * (float)$seller->admin_commission_value),0) / 100;

        } else if($seller->admin_sale_commission_type == 'fixed'){
            $data['admin_commission_type'] = 'Fixed';
            $data['admin_commission'] = (float)$seller->admin_commission_value;
        }
        $levelAmount=0;
        $setting = $referlevelSettings = $this->Product_model->getVendorSettings($product['vendor_id'],'referlevel','levels');
        
        $max_level = isset($setting['levels']) ? (int)$setting['levels'] : 3;
        $level = $this->Product_model->getMyLevel($product['refer_id']);
        $getSettingsFor = [];
        for ($l=1; $l <= $max_level ; $l++)
            $getSettingsFor[] = 'referlevel_'. $l;

        if(isset($product['vendor_id']) && $product['vendor_id']){
            
            $referlevelSettings = $this->Product_model->getVendorSettingsWhereIn($product['vendor_id'], $getSettingsFor);
        }
        else{
           
            $referlevelSettings = $this->Product_model->getSettingsWhereIn($getSettingsFor);
        }
        for ($l=1; $l <= $max_level ; $l++) 
            { 
                
                $s = $referlevelSettings['referlevel_'. $l];
                 $levelUser = (int)$level['level'. $l];
                
                if($product['vendor_id'] == $levelUser)
                    continue;

                if($s && $levelUser > 0)
                {    
                    $_giveAmount = ((((int)$product['quantity'] * ((float)$product['product_price'] + (float)$product['variation_price'])) * (float)$s['sale_commition']) / 100);
                    $levelAmount=$levelAmount + $_giveAmount;  
                }
            }
            
        $data['vendor_commission_type'] = 'her_sale';
        $data['vendor_commission'] = max( ($product_price - $data['admin_commission'] - $data['commission'] - $levelAmount), 0 );

        return $data;
    } else {
        if($type == 'sale'){
            $commission = 0;
            $commissionType = $product['product_commision_type'];
            if($product['product_commision_type'] == 'default'){
                $commissionSetting = $this->Product_model->getSettings('productsetting');
                $commissionType = $commissionSetting['product_commission_type'];
                if($commissionSetting['product_commission_type'] == 'percentage'){
                    $award_level = $this->Product_model->getSettings('award_level', 'status');

                    $comission_sale_status = 0;
                    $sale_comission_rate = 0;
                    $userPlan = App\MembershipUser::select('membership_plans.commission_sale_status','award_level.sale_comission_rate')->join('membership_plans','membership_plans.id','=','membership_user.plan_id')->join('award_level','award_level.id','=','membership_plans.level_id','left')->where('is_active',1)->where('user_id',$product['refer_id'])->first();
                    if($userPlan->commission_sale_status){
                        $comission_sale_status = $userPlan->commission_sale_status;
                        $sale_comission_rate = $userPlan->sale_comission_rate;
                    } else {
                        $userdetails = $this->Product_model->getByField('users','id',$product['refer_id']);
                        $userLevel = $this->Product_model->getByField('award_level','id',$userdetails['level_id']);
                        if($userLevel){
                            $comission_sale_status = 1;
                            $sale_comission_rate = $userLevel['sale_comission_rate'];
                        }
                    }
                    if($award_level['status'] && $comission_sale_status && $sale_comission_rate && $sale_comission_rate < $commissionSetting['product_commission'])
                        $product_commission = $sale_comission_rate;
                    else 
                        $product_commission = $commissionSetting['product_commission'];

                    $commissionType = 'percentage ('. $product_commission .'%)';
                    $commission = max(($product_price * $product_commission),0) / 100;
                } else if($commissionSetting['product_commission_type'] == 'Fixed'){
                    $commission = $commissionSetting['product_commission'];
                }
            } else if($product['product_commision_type'] == 'percentage'){
                $award_level = $this->Product_model->getSettings('award_level', 'status');

                $comission_sale_status = 0;
                $sale_comission_rate = 0;
                $userPlan = App\MembershipUser::select('membership_plans.commission_sale_status','award_level.sale_comission_rate')->join('membership_plans','membership_plans.id','=','membership_user.plan_id')->join('award_level','award_level.id','=','membership_plans.level_id','left')->where('is_active',1)->where('user_id',$product['refer_id'])->first();
                if($userPlan->commission_sale_status){
                    $comission_sale_status = $userPlan->commission_sale_status;
                    $sale_comission_rate = $userPlan->sale_comission_rate;
                } else {
                    $userdetails = $this->Product_model->getByField('users','id',$product['refer_id']);
                    $userLevel = $this->Product_model->getByField('award_level','id',$userdetails['level_id']);
                    if($userLevel){
                        $comission_sale_status = 1;
                        $sale_comission_rate = $userLevel['sale_comission_rate'];
                    }
                }
                if($award_level['status'] && $comission_sale_status && $sale_comission_rate && $sale_comission_rate < $product['product_commision_value'])
                    $product_commision_value = $sale_comission_rate;
                else 
                    $product_commision_value = $product['product_commision_value'];

                $commissionType = 'percentage ('. $product_commision_value .'%)';
                $commission = max(($product_price * $product_commision_value),0) / 100;
            } else if($product['product_commision_type'] == 'fixed'){
                $commission = $product['product_commision_value'];
            }
        } else if($type == 'click'){
            $commission = 0;
            $commissionType = $product['product_commision_type'];
            if($product['product_click_commision_type'] == 'default'){
                $commissionSetting = $this->Product_model->getSettings('productsetting');
                $commissionType = $commissionSetting['product_commission_type'];
                if($commissionSetting['product_commission_type'] == 'percentage'){
                    $commissionType = 'percentage ('. $commissionSetting['product_ppc'] .'%)';
                    $commission = max(($product_price * $commissionSetting['product_ppc']),0) / 100;
                }
                else if($commissionSetting['product_commission_type'] == 'Fixed'){
                    $commission = $commissionSetting['product_ppc'];
                }
            }
            else if($product['product_click_commision_type'] == 'percentage'){
                $commissionType = 'percentage ('. $product['product_click_commision_value'] .'%)';
                $commission = max(($product_price * $product['product_click_commision_value']),0) / 100;
            }
            else if($product['product_click_commision_type'] == 'fixed'){
                $commission = $product['product_click_commision_value'];
            }
        }

        return array(
            'type' => strtolower($commissionType),
            'commission' => (float)$commission,
        );
    }
}
public function formcalcCommitions($product, $type= 'sale', $shareUser = [], $form = null){
    $product = (array)$product;

    $product_price = ((int)$product['quantity'] * (float)$product['product_price']);

    if($type == 'sale'){
        $commission = 0;
        $commissionType = $product['product_commision_type'];
        if($product['product_commision_type'] == 'default'){
            $commissionType = $form->sale_commision_type;
            if($commissionType == 'percentage'){
                $award_level = $this->Product_model->getSettings('award_level', 'status');

                $comission_sale_status = 0;
                $sale_comission_rate = 0;
                $userPlan = App\MembershipUser::select('membership_plans.commission_sale_status','award_level.sale_comission_rate')->join('membership_plans','membership_plans.id','=','membership_user.plan_id')->join('award_level','award_level.id','=','membership_plans.level_id','left')->where('is_active',1)->where('user_id',$product['refer_id'])->first();
                if($userPlan->commission_sale_status){
                    $comission_sale_status = $userPlan->commission_sale_status;
                    $sale_comission_rate = $userPlan->sale_comission_rate;
                } else {
                    $userdetails = $this->Product_model->getByField('users','id',$product['refer_id']);
                    $userLevel = $this->Product_model->getByField('award_level','id',$userdetails['level_id']);
                    if($userLevel){
                        $comission_sale_status = 1;
                        $sale_comission_rate = $userLevel['sale_comission_rate'];
                    }
                }
                if($award_level['status'] && $comission_sale_status && $sale_comission_rate && $sale_comission_rate < $form->sale_commision_value)
                    $sale_commision_value = $sale_comission_rate;
                else 
                    $sale_commision_value = $form->sale_commision_value;

                $commissionType = 'percentage ('. $sale_commision_value .'%)';
                $commission = max(($product_price * $sale_commision_value),0) / 100;
            } else if($commissionType == 'Fixed'){
                $commission = $form->sale_commision_value;
            } else {
                $commissionSetting = $this->Product_model->getSettings('formsetting');
                $commissionType = $commissionSetting['product_commission_type'];
                if($commissionSetting['product_commission_type'] == 'percentage'){
                    $award_level = $this->Product_model->getSettings('award_level', 'status');

                    $comission_sale_status = 0;
                    $sale_comission_rate = 0;
                    $userPlan = App\MembershipUser::select('membership_plans.commission_sale_status','award_level.sale_comission_rate')->join('membership_plans','membership_plans.id','=','membership_user.plan_id')->join('award_level','award_level.id','=','membership_plans.level_id','left')->where('is_active',1)->where('user_id',$product['refer_id'])->first();
                    if($userPlan->commission_sale_status){
                        $comission_sale_status = $userPlan->commission_sale_status;
                        $sale_comission_rate = $userPlan->sale_comission_rate;
                    } else {
                        $userdetails = $this->Product_model->getByField('users','id',$product['refer_id']);
                        $userLevel = $this->Product_model->getByField('award_level','id',$userdetails['level_id']);
                        if($userLevel){
                            $comission_sale_status = 1;
                            $sale_comission_rate = $userLevel['sale_comission_rate'];
                        }
                    }
                    if($award_level['status'] && $comission_sale_status && $sale_comission_rate && $sale_comission_rate < $commissionSetting['product_commission'])
                        $product_commission = $sale_comission_rate;
                    else 
                        $product_commission = $commissionSetting['product_commission'];
                    
                    $commissionType = 'percentage ('. $product_commission .'%)';
                    $commission = max(($product_price * $product_commission),0) / 100;
                } else if($commissionSetting['product_commission_type'] == 'Fixed'){
                    $commission = $commissionSetting['product_commission'];
                } 
            }
        } else if($product['product_commision_type'] == 'percentage'){
            $commissionType = 'percentage ('. $product['product_commision_value'] .'%)';
            $commission = max(($product_price * $product['product_commision_value']),0) / 100;
        } else if($product['product_commision_type'] == 'fixed'){
            $commission = $product['product_commision_value'];
        }
    } else if($type == 'click'){
        $commission = 0;
        $commissionType = $product['product_commision_type'];
        if($product['product_click_commision_type'] == 'default'){
            $commissionSetting = $this->Product_model->getSettings('formsetting');
            $commissionType = $commissionSetting['product_commission_type'];
            if($commissionSetting['product_commission_type'] == 'percentage'){
                $commissionType = 'percentage ('. $commissionSetting['product_ppc'] .'%)';
                $commission = max(($product_price * $commissionSetting['product_ppc']),0) / 100;
            }
            else if($commissionSetting['product_commission_type'] == 'Fixed'){
                $commission = $commissionSetting['product_ppc'];
            }
        }
        else if($product['product_click_commision_type'] == 'percentage'){
            $commissionType = 'percentage ('. $product['product_click_commision_value'] .'%)';
            $commission = max(($product_price * $product['product_click_commision_value']),0) / 100;
        }
        else if($product['product_click_commision_type'] == 'fixed'){
            $commission = $product['product_click_commision_value'];
        }
    }

    return array(
        'type' => strtolower($commissionType),
        'commission' => (float)$commission,
    );

}

function getProductActionIncrese($product_id, $user_id, $viewer_id =0){
    $ip_address = $_SERVER['REMOTE_ADDR'];
    $this->db->from('product_action');
    $this->db->where('action_type', 'click');
    $this->db->where('product_id', $product_id);
    $this->db->where('user_id', $user_id);
    if($viewer_id) $this->db->where('viewer_id', $viewer_id);
    $this->db->where('user_ip', $ip_address);
    $result_array = $this->db->get()->row_array();
    if($result_array){
        $this->db->update(
            'product_action',
            array('counter'=> ($result_array['counter']+1)) ,
            array('action_id' => $result_array['action_id'])
        );
    }
}

function getFormActionIncrese($form_id, $user_id, $viewer_id =0){
    $ip_address = $_SERVER['REMOTE_ADDR'];
    $this->db->from('form_action');
    $this->db->where('action_type', 'click');
    $this->db->where('form_id', $form_id);
    $this->db->where('user_id', $user_id);
    if($viewer_id) $this->db->where('viewer_id', $viewer_id);
    $this->db->where('user_ip', $ip_address);
    $result_array = $this->db->get()->row_array();
    if($result_array){
        $this->db->update(
            'product_action',
            array('counter'=> ($result_array['counter']+1)) ,
            array('action_id' => $result_array['action_id'])
        );
    }
}

public function giveAdminClickCommition($product, $wallet_group_id = null){

    $ip_address = $_SERVER['REMOTE_ADDR'];
    $productsetting = $this->getSettings('productsetting');

    $this->db->from('product_action_admin');

    if ($productsetting['click_allow'] != 'single') {
        $this->db->where('product_id', (int)$product['product_id']);
    }

    $this->db->where('user_ip', $ip_address);
    $this->db->where('user_id', 1);
    $match = $this->db->get()->num_rows();

    if ($match == 0){
        $new_record = array(
            'action_type'  => 'click',
            'product_id'   => (int)$product['product_id'],
            'user_id'      => 1,
            'user_ip'      => $ip_address,
            'created_at'   => date('Y-m-d h:i:s'),
            'counter'      => 1,
            'country_code' => @$this->ip_info()['country_code'],
        );
        $this->db->insert('product_action_admin', $new_record);
    } else {
        if ($productsetting['click_allow'] != 'single') {
            $this->db->set('counter','`counter` + 1', false);
            $this->db->where('action_type', 'click');
            $this->db->where('product_id', (int)$product['product_id']);
            $this->db->where('user_id', 1);
            $this->db->where('user_ip', $ip_address);
            $this->db->update('product_action_admin');
        }
    }

    $product_id = $product['product_id'];


    $vendor_id = $this->db->get_where('product_affiliate', array('product_id' => (int)$product['product_id']))->row_array();



    $totalClick = $this->db->query("SELECT * FROM  product_action_admin WHERE pay_commition = 0 AND user_id = 1");

    $wallet_group_id = ($wallet_group_id != null) ? $wallet_group_id : time().rand(10,100);

    

    if(isset($product['seller']['id']) OR $vendor_id != ''){ 
        $needClick = 0;
        $payPerClick = 0;
        $vendor_setting = $this->getSettings('vendor');

        if($product['seller']['admin_click_commission_type'] == 'default' OR $vendor_id['admin_click_commission_type'] == 'default'){
            
            $needClick = (int)$vendor_setting['admin_click_count'];
            $payPerClick = (float)$vendor_setting['admin_click_amount'];
        } else{
            $needClick = (int)$vendor_id['admin_click_count'];
            $payPerClick = $vendor_id['admin_click_amount'];
        }

        if($needClick > 0 && $needClick && $payPerClick){
            $tC = $totalClick->num_rows();
            if($tC >= $needClick){
                $ips = [];

                foreach ($totalClick->result() as $vv) {
                    $ips[] = array('ip' => $vv->user_ip,'country_code' => $vv->country_code);
                }

                if ($vendor_setting['admin_click_status'] == 1) {

                $this->Wallet_model->addTransaction(array(
                    'status'         => 3,
                    'user_id'        => 1,
                    'amount'         => $payPerClick,
                    'comment'        => "Commission for {$tC} click on product <br> Clicked done from ip_message",
                    'type'           => 'click_commission',
                    'reference_id'   => $product_id,
                    'reference_id_2' => 'vendor_click_commission',
                    'ip_details'     => json_encode($ips),
                    'group_id'       => $wallet_group_id,
                    'is_vendor'      => 1,
                ));

                $this->Wallet_model->addTransaction(array(
                    'status'         => 1,
                    'user_id'        =>  $vendor_id['user_id'],
                    'amount'         => -$payPerClick,
                    'comment'        => "Pay Commission for {$tC} click on product <br> Clicked done from ip_message",
                    'type'           => 'click_commission',
                    'reference_id'   => $product_id,
                    'reference_id_2' => 'vendor_pay_click_commission_for_admin',
                    'ip_details'     => json_encode($ips),
                    'group_id'       => $wallet_group_id,
                    'is_vendor'      => 1,
                ));
            }

                

            }

        }
    }
    $this->db->query("UPDATE product_action_admin SET pay_commition = 1 WHERE user_id = 1");
}

public function giveClickCommition($product, $user_id, $viewer_id= 0, $wallet_group_id = null){
    $transaction_id = 0;

    $product_id = $product['product_id'];

    $vendor_id = $this->db->get_where('product_affiliate', array('product_id' => $product_id))->row()->user_id;
 
    $totalClick = $this->db->query("SELECT * FROM  product_action WHERE pay_commition = 0 AND user_id = '{$user_id}'");


    $wallet_group_id = $wallet_group_id != null ? $wallet_group_id : time().rand(10,100);
    $commission = 0;
    $needClick = 0;
    $payPerClick = 0;

    $minus_amount = 0;
    $reference_id_2 = '';
    if(isset($product['seller']['id'])){ //if cart product,
        if($product['seller']['affiliate_click_commission_type'] == 'default'){
            $seller_setting = $this->Product_model->getSellerSetting($product['seller']['user_id']);
            $needClick = (int)$seller_setting->affiliate_click_count;
            $payPerClick = $seller_setting->affiliate_click_amount;
        } else{
            $needClick = (int)$product['seller']['affiliate_click_count'];
            $payPerClick = $product['seller']['affiliate_click_amount'];
        }

        $reference_id_2 = '';
        //$reference_id_2 = 'vendor_click_commission';
        $minus_amount = $product['seller']['user_id'];
    } else {
        if($product['product_click_commision_type'] == 'custom'){
            $payPerClick = (float)$product['product_click_commision_ppc'];
            $needClick = (int)$product['product_click_commision_per'];
        }else{
            $commissionSetting = $this->getSettings('productsetting');
            $needClick = (int)$commissionSetting['product_noofpercommission'];
            $payPerClick = (float)$commissionSetting['product_ppc'];
            $reference_id_2 = '';
           // $reference_id_2 = 'vendor_sale_commission';
            $minus_amount = $vendor_id;
        }
    }

    


    if($needClick && $payPerClick){
        $tC = $totalClick->num_rows();
        
        if($tC >= $needClick){

            $addTransactionsData = [];

            $ips = [];

            foreach ($totalClick->result() as $vv) {
                $ips[] = array(
                    'ip' => $vv->user_ip,
                    'country_code' => $vv->country_code,
                );
            }
            
            
            if($product['product_url'] == ""){
                $url=base_url('store/'.$user_id.'/product/'.$product['product_slug'].'');
            }else{
                $url=$product['product_url'];
            }
            $addTransactionsData[] = array(
                'status'         => 1,
                'user_id'        => $user_id,
                'amount'         => $payPerClick,
                'comment'        => "Commission for {$tC} click on product | Product id: ".$product_id."  <a target='_blank' href=". $url .">Product Link </a><br> Clicked done from ip_message",
                'type'           => 'click_commission',
                'reference_id'   => $product_id,
                'reference_id_2' => $reference_id_2,
                'ip_details'     => json_encode($ips),
                'group_id'       => $wallet_group_id,
                'is_vendor'      => $minus_amount ? 1 : 0,
            );


            if($minus_amount > 0){
                $addTransactionsData[] = array(
                    'status'         => 1,
                    'user_id'        => $minus_amount,
                    'amount'         => -$payPerClick,
                    'comment'        => "Pay Commission for {$tC} click on product <br> Clicked done from ip_message",
                    'type'           => 'click_commission',
                    'reference_id'   => $product_id,
                    'reference_id_2' => 'vendor_pay_click_commission',
                    'ip_details'     => json_encode($ips),
                    'group_id'       => $wallet_group_id,
                    'is_vendor'      => $minus_amount ? 1 : 0,
                );
            }

            if(!empty($addTransactionsData)) {
                $this->Wallet_model->addTransactionBatch($addTransactionsData);
            }

            $this->db->query("UPDATE product_action SET pay_commition = 1 WHERE user_id = '{$user_id}'");

            return 1;
        }
    }

    return 0;
}

public function giveFormClickCommition($form, $user_id, $viewer_id= 0){
    $form_id = $form['form_id'];
    $seo = $form['seo'];
    $totalClick = $this->db->query("SELECT * FROM  form_action WHERE pay_commition = 0 AND user_id = '{$user_id}' AND  form_id = '{$form_id}' ");

    $commission = 0;
    $needClick = 0;
    $payPerClick = 0;


    $wallet_group_id = time().rand(10,100);
     
    
    if((int)$form['vendor_id'] == 0){
        
        if($form['click_commision_type'] == 'default'){
            $commissionSetting = $this->getSettings('formsetting');

            $needClick = (int)$commissionSetting['product_noofpercommission'];
            $payPerClick = (float)$commissionSetting['product_ppc'];
        }else{
            $needClick = (int)$form['click_commision_ppc'];
            $payPerClick = (float)$form['click_commision_per'];
        }
    } else {
        $vendor_setting = $this->db->query("SELECT * FROM vendor_setting WHERE user_id=". (int)$form['vendor_id'] ." ")->row();
       

        if($form['click_commision_type'] == 'default'){
            $needClick = (int)$vendor_setting->form_affiliate_click_count;
            $payPerClick = (float)$vendor_setting->form_affiliate_click_amount;
        }else{
            $needClick = (int)$form['click_commision_ppc'];
            $payPerClick = (float)$form['click_commision_per'];
        }
    }
   
 
    if($needClick && $payPerClick){
        $tC = $totalClick->num_rows();

        if($tC >= $needClick){
            $ips = [];
            foreach ($totalClick->result() as $vv) {
                $ips[] = array(
                    'ip' => $vv->user_ip,
                    'country_code' => $vv->country_code,
                );
            }

            $this->Wallet_model->addTransaction(array(
                'user_id'      => $user_id,
                'amount'       => $payPerClick,
                'comment'      => "Commission for {$tC} click on form_id={$form_id} <br> seo_id={$seo}  Clicked done from ip_message",
                'type'         => 'form_click_commission',
                'reference_id' => $form_id,
                'reference_id_2' =>'vendor_form_click_commission',
                'ip_details'   => json_encode($ips),
                'group_id' => $wallet_group_id,
            ));
            $this->Product_model->formReferClick($form, $user_id, 0, $wallet_group_id);
            $this->db->query("UPDATE  form_action SET pay_commition = 1 WHERE user_id = '{$user_id}' AND  form_id = '{$form_id}' ");
           
        }
    }
}
function formReferClick($form, $user_id,  $viewer_id = 0, $wallet_group_id){
    if((int)$form['vendor_id'] == 0){
        $form_id = $form['form_id'];
    $seo = $form['seo'];
    $totalClick = $this->db->query("SELECT * FROM  form_action WHERE pay_commition = 0 AND user_id = '{$user_id}' AND  form_id = '{$form_id}' ");
    $tC = $totalClick->num_rows();
    $form_id = $form['form_id'];
    $setting = $this->Product_model->getSettings('referlevel');
    $level = $this->getMyLevel($user_id, $setting);
    $max_level = isset($setting['levels']) ? (int)$setting['levels'] : 3;
    $getSettingsFor = [];

        for ($l=1; $l <= $max_level ; $l++) {
            $getSettingsFor[] = 'referlevel_'. $l;
        }
    $referlevelSettings = $this->getSettingsWhereIn($getSettingsFor);
    for ($l=1; $l <= $max_level ; $l++) {
            $levelUser = (int)$level['level'. $l];

            if ($product['seller']['user_id'] == $levelUser) {
                continue;
            }

            if(isset($referlevelSettings['referlevel_'. $l]) && $levelUser > 0){
                $_giveAmount = (float)$referlevelSettings['referlevel_'. $l]['ex_commition'];
                if($_giveAmount > 0){
                    $referComissionData[] = array(
                        'status'       => 1,
                        'user_id'      => $levelUser,
                        'amount'       => $_giveAmount,
                        'dis_type'     => '',
                        'comment'      => "Level {$l} Commition  For {$tC} click on form_id={$form_id} <br> seo_id={$seo}",
                        'type'         => 'refer_click_commission',
                        'reference_id' => $form_id,
                        'reference_id_2' => 'vendor_form_click_commission',
                        'group_id'       => $wallet_group_id,
                        'is_vendor' => 0
                    );
                }
            }
        }

      

        if(!empty($referComissionData)) {
            $this->Wallet_model->addTransactionBatch($referComissionData);
        }
    }
    
}

function setFormClicks($form_id, $user_id, $viewer_id = 0){
    $ip_address = $_SERVER['REMOTE_ADDR'];
    $this->db->from('form_action');
    $this->db->where('action_type', 'click');
    $this->db->where('form_id', $form_id);
    $this->db->where('user_id', $user_id);
    $this->db->where('user_ip', $ip_address);
    $result = $this->db->get()->num_rows;
    if($result == 0){
        $cdate = date('Y-m-d h:i:s');
        $new_record = array(
            'action_type'  => 'click',
            'form_id'      => $form_id,
            'user_id'      => $user_id,
            'user_ip'      => $ip_address,
            'created_at'   => $cdate,
            'counter'      => 1,
            'country_code' => @$this->ip_info()['country_code'],
        );
        if($viewer_id) $new_record['viewer_id'] = $viewer_id;
        $this->db->insert('form_action', $new_record);
    }
}

function setClicks($product_id, $user_id, $viewer_id = 0){
    $productsetting = $this->getSettings('productsetting');

    $ip_address = $_SERVER['REMOTE_ADDR'];
    $this->db->from('product_action');
    $this->db->where('action_type', 'click');

    if ($productsetting['click_allow'] != 'single') {
        $this->db->where('product_id', $product_id);
    }

    $this->db->where('user_id', $user_id);
    $this->db->where('user_ip', $ip_address);
    $result = $this->db->get()->num_rows();

    if($result == 0){
        $cdate = date('Y-m-d h:i:s');
        $new_record = array(
            'action_type'  => 'click',
            'product_id'   => $product_id,
            'user_id'      => $user_id,
            'user_ip'      => $ip_address,
            'created_at'   => $cdate,
            'counter'      => 1,
            'country_code' => @$this->ip_info()['country_code'],
        );
        if($viewer_id) $new_record['viewer_id'] = $viewer_id;
        $this->db->insert('product_action', $new_record);
    }
}

public function referClick($product, $user_id, $viewer_id = 0, $wallet_group_id = null ) {
    $product_id = $product['product_id'];
    $product_created_by = $product['product_created_by'];

    $this->db->where('id',$product_created_by);
    $userDetail=$this->db->get('users')->row_array();

    $minus_amount = 0;
    $reference_id_2 = '';

    if(isset($product['seller']['id'])){
        if($product['seller']['affiliate_click_commission_type'] == 'default'){
            $seller_setting = $this->Product_model->getSellerSetting($product['seller']['user_id']);
            $needClick = (int)$seller_setting->affiliate_click_count;
            $payPerClick = $seller_setting->affiliate_click_amount;
        } else{
            $needClick = (int)$product['seller']['affiliate_click_count'];
            $payPerClick = $product['seller']['affiliate_click_amount'];
        }

        $reference_id_2 = 'vendor_click_commission';
        $minus_amount = $product['seller']['user_id'];
    } else {
        if($product['product_click_commision_type'] == 'custom'){
            $payPerClick = (float)$product['product_click_commision_ppc'];
            $needClick = (int)$product['product_click_commision_per'];
        }else{
            $commissionSetting = $this->getSettings('productsetting');
            $needClick = (int)$commissionSetting['product_noofpercommission'];
            $payPerClick = (float)$commissionSetting['product_ppc'];
        }
        

         $minus_amount=$userDetail['is_vendor'];
    }
    
    

    $wallet_group_id = $wallet_group_id != null ? $wallet_group_id : time().rand(10,100);

    if(isset($product['seller']['is_vendor']) && $product['seller']['is_vendor']){
        $store_commition_setting = $setting = $this->Product_model->getVendorSettings($product['seller']['user_id'], 'referlevel');
    }
    else {
        if($userDetail['is_vendor'] == 0){
        $store_commition_setting = $setting = $this->Product_model->getSettings('referlevel');
        }else{
            $store_commition_setting = $setting = $this->Product_model->getVendorSettings($userDetail['id'], 'referlevel');
        }
    }
    

    $disabled_for = json_decode( (isset($store_commition_setting['disabled_for']) ? $store_commition_setting['disabled_for'] : '[]'),1);
    if((int)$store_commition_setting['status'] == 0){ return false; }
    else if((int)$store_commition_setting['status'] == 2 && in_array($user_id, $disabled_for)){ return false; }

    $ip_address = $_SERVER['REMOTE_ADDR'];

    $level = $this->getMyLevel($user_id, $setting);
   
    $count_for = '';
    

    $max_level = isset($setting['levels']) ? (int)$setting['levels'] : 3;
    for ($l=1; $l <= $max_level ; $l++) {
        $count_for .= (int)$level['level'. $l] > 0 ? $level['level'. $l] ."," : "";
    }

    $productsetting = $this->getSettings('productsetting');

    if ($count_for) {
        $this->db->from('refer_product_action');
        $this->db->where('action_type', 'click');
        if ($productsetting['click_allow'] != 'single') {
            $this->db->where('product_id', $product_id);
        }
        $this->db->where('user_id', $user_id);
        $this->db->where('user_ip', $ip_address);
        $result = $this->db->get()->num_rows();

        if($result == 0){
            $new_record = array(
                'action_type' => 'click',
                'product_id'  => $product_id,
                'count_for'   => trim($count_for,","),
                'user_id'     => $user_id,
                'user_ip'     => $ip_address,
                'created_at'  => date('Y-m-d h:i:s'),
                'counter'     => 1,
            );
            if($viewer_id) $new_record['viewer_id'] = $viewer_id;

            $this->db->insert('refer_product_action', $new_record);
        }
    }

    /* Give Refer Commition */
    $sql = '';
    if ($productsetting['click_allow'] != 'single') {
        $sql = " AND  product_id = '{$product_id}'";
    }

    $totalClick = $this->db->query("SELECT count(*) as total FROM  refer_product_action WHERE pay_commition = 0 AND user_id = '{$user_id}' {$sql} ")->row()->total;

    $_needClick = (int)$store_commition_setting['click'];

    if($totalClick > 0 && $totalClick >= $_needClick){
        $this->load->model('Mail_model');
        $max_level = isset($setting['levels']) ? (int)$setting['levels'] : 3;

        $referComissionData = [];

        $getSettingsFor = [];

        for ($l=1; $l <= $max_level ; $l++) {
            $getSettingsFor[] = 'referlevel_'. $l;
        }

        if(isset($product['seller']['is_vendor']) && $product['seller']['is_vendor']){
            $referlevelSettings = $this->getVendorSettingsWhereIn($product['seller']['user_id'], $getSettingsFor);
        }
        else{
            if($userDetail['is_vendor'] == 0){
                $referlevelSettings = $this->getSettingsWhereIn($getSettingsFor);
            }else{
                $referlevelSettings = $this->getVendorSettingsWhereIn($userDetail['id'], $getSettingsFor);
            }
        }
        

        for ($l=1; $l <= $max_level ; $l++) {
            $levelUser = (int)$level['level'. $l];

            if ($product['seller']['user_id'] == $levelUser) {
                continue;
            }

            if(isset($referlevelSettings['referlevel_'. $l]) && $levelUser > 0){
                $_giveAmount = (float)$referlevelSettings['referlevel_'. $l]['ex_commition'];
                if($_giveAmount > 0){
                    if($product_created_by != $levelUser){
                    $referComissionData[] = array(
                        'status'       => 1,
                        'user_id'      => $levelUser,
                        'amount'       => $_giveAmount,
                        'dis_type'     => '',
                        'comment'      => "Level {$l} Commition  For {$totalClick} click on product ",
                        'type'         => 'refer_click_commission',
                        'reference_id' => $product_id,
                        'reference_id_2' => $reference_id_2,
                        'group_id'       => $wallet_group_id,
                        'is_vendor' => $minus_amount ? 1 : 0
                    );


                    if($minus_amount > 0){
                        $referComissionData[] = array(
                            'status'         => 1,
                            'user_id'        => $minus_amount,
                            'amount'         => -$_giveAmount,
                            'dis_type'       => '',
                            'comment'        => "Level {$l} Commition  For {$totalClick} click on product ",
                            'type'           => 'refer_click_commission',
                            'reference_id'   => $product_id,
                            'reference_id_2' => 'vendor_pay_click_commission',
                            'group_id'       => $wallet_group_id,
                            'is_vendor'      => $minus_amount ? 1 : 0,
                        );
                    }
                }
                }
            }
        }
      

        if(!empty($referComissionData)) {
            $this->Wallet_model->addTransactionBatch($referComissionData);
        }

        $this->db->query("UPDATE refer_product_action SET pay_commition = 1 WHERE user_id = '{$user_id}' AND  product_id = '{$product_id}' ");
    }
}

public function getMyUnder($user_id, $first_time = true){

    if($first_time){
        $this->level_count = 0;
    }

    $setting = $this->Product_model->getSettings('referlevel');

    $max_level = isset($setting['levels']) ? $setting['levels'] : 3;

    $max_level=25;
 
    if($this->level_count > $max_level) 
    return array();

    $users= $this->db->query("SELECT
        id,
        CONCAT(firstname,' ',lastname) AS title,
        phone,
        email,

        (SELECT sum(amount) FROM wallet WHERE status > 0 AND wallet.user_id = users.id) as all_commition,
        (SELECT sum(amount) FROM wallet WHERE status = 3 AND wallet.user_id = users.id) as paid_commition,
        (SELECT sum(amount) FROM wallet WHERE status = 2 AND wallet.user_id = users.id) as in_request_commiton,
        (SELECT sum(amount) FROM wallet WHERE status IN(1,2) AND wallet.user_id = users.id) as unpaid_commition,

        (SELECT SUM(amount) FROM wallet WHERE type IN ('click_commission','refer_click_commission','external_click_commission','form_click_commission','affiliate_click_commission') AND is_action=0 AND user_id = users.id) as click_commission,
        (SELECT COUNT(action_id) FROM product_action WHERE user_id = users.id) as click,

        (SELECT SUM(amount) FROM wallet WHERE type IN ('external_click_commission') AND is_action=1 AND user_id = users.id) as action_click_commission,
        (SELECT COUNT(id) FROM integration_clicks_action WHERE is_action=1 AND user_id = users.id) as external_action_click,

        (SELECT COUNT(id) FROM integration_clicks_action WHERE page_name = '' AND is_action=0 AND user_id = users.id) as external_click,

        (SELECT COUNT(action_id) FROM form_action WHERE user_id = users.id) as form_click,
        (SELECT COUNT(id) FROM affiliate_action WHERE user_id = users.id) as aff_click,

        (SELECT SUM(amount) FROM wallet WHERE type IN ('sale_commission') AND status > 0 AND user_id = users.id) as sale_commission,
        (SELECT SUM(o.total) FROM `order` o LEFT JOIN order_products op ON (o.id = op.order_id) WHERE  op.refer_id = users.id AND ((o.payment_method = 'bank_transfer' AND o.status = 1) OR (o.payment_method != 'bank_transfer' AND o.status > 0)) ) as amount,
        (SELECT SUM(io.total) FROM `integration_orders` io WHERE io.status > 0 AND io.user_id = users.id) as external_sale_amount,

        username
        FROM users WHERE type='user' AND  refid = ". $user_id)->result_array();

    $children = [];
    foreach ($users as $key => $value) {
        if($key == 0) {
            $this->level_count++;
        }
        $value['children'] = $this->getMyUnder($value['id'], false);
        $children[] = array(
            'title'                       => $value['username'],
            'phone'                       => $value['phone'],
            'email'                       => $value['email'],
            'click'                       => (int)$value['click'],
            'external_click'              => (int)$value['external_click'],
            'form_click'                  => (int)$value['form_click'],
            'aff_click'                   => (int)$value['aff_click'],
            'click_commission'            => c_format($value['click_commission']),
            'refer_click_commission'            => c_format($value['refer_click_commission']),
            'external_action_click'       => (int)$value['external_action_click'],
            'action_click_commission'     => c_format($value['action_click_commission']),
            'amount_external_sale_amount' => c_format($value['amount'] + $value['external_sale_amount']),
            //'external_sale_amount'      => $value['external_sale_amount'],
            'sale_commission'             => c_format($value['sale_commission']),
            'paid_commition'              => c_format($value['paid_commition']),
            'unpaid_commition'            => c_format($value['unpaid_commition']),
            'in_request_commiton'         => c_format($value['in_request_commiton']),
            'all_commition'               => c_format($value['all_commition']),
            'children'               => $value['children'],
        );
    }

    return $children;

}
public function getReferalTotals($user_id = 0){
    $where = '';
    if($user_id > 0){
        $where .= " AND user_id=". $user_id;
        $find_in_set .= " AND find_in_set({$user_id},count_for)";
    }

    /* Products Click */
    $data['total_product_click'] = $this->db->query("SELECT sum(amount) as amounts FROM wallet WHERE type IN ('refer_click_commission')  AND is_action = 0 AND (page_name = '' OR page_name is NULL) ". $where)->row_array();

    $data['total_product_click']['clicks']  = $this->db->query("SELECT count(*) as counts FROM refer_product_action WHERE 1  $find_in_set ")->row()->counts;

    $data['total_product_click']['clicks']  += $this->db->query("SELECT count(*) as counts FROM integration_refer_product_action WHERE is_action = 0 AND page_name = '' $find_in_set ")->row()->counts;

    $clicks = $this->db->query("SELECT sum(amount) as amounts,status FROM wallet WHERE type = 'refer_click_commission' AND is_action = 0 AND page_name = '' ". $where ." GROUP BY status")->result_array();
    foreach ($clicks as $key => $value) {
        if($value['status'] == 3){
            $data['total_product_click']['paid'] = $value['amounts'];
        }
        else if($value['status'] == 2){
            $data['total_product_click']['request'] = $value['amounts'];
        }
        else if($value['status'] == 1){
            $data['total_product_click']['unpaid'] = $value['amounts'];
        }
    }

    /*  Ganeral Click */
    $G_clicks = $this->db->query("SELECT sum(amount) as amounts,status FROM `wallet` WHERE 1 {$where} AND wallet.type = 'refer_click_commission' AND wallet.comm_from = 'ex'  AND reference_id_2 IN ('__general_click__') GROUP BY status ")->result_array();
    foreach ($G_clicks as $key => $value) {
        $data['total_ganeral_click']['total_amount'] = $value['amounts'];
        if($value['status'] == 3){
            $data['total_ganeral_click']['paid'] = $value['amounts'];
        }
        else if($value['status'] == 2){
            $data['total_ganeral_click']['request'] = $value['amounts'];
        }
        else if($value['status'] == 1){
            $data['total_ganeral_click']['unpaid'] = $value['amounts'];
        }
    }

    $data['total_ganeral_click']['total_clicks'] = $this->db->query("SELECT count(*) as amounts FROM `integration_refer_product_action` WHERE 1 {$find_in_set} AND is_action  = 0 AND page_name != '' ")->row()->amounts;

    /* Action Counts */

    $data['total_action']['click_count'] = $this->db->query("SELECT count(*) as total FROM `integration_refer_product_action` WHERE 1 {$find_in_set} AND is_action  = 1")->row()->total;
    $a_clicks = $this->db->query("SELECT sum(amount) as amounts , status FROM `wallet` WHERE 1  {$where} AND  wallet.type = 'refer_click_commission' AND wallet.comm_from = 'ex' AND wallet.is_action = 1 GROUP BY status ")->result_array();

    foreach ($a_clicks as $key => $value) {
        $data['total_action']['total_amount'] = $value['amounts'];
        if($value['status'] == 3){
            $data['total_action']['paid'] = $value['amounts'];
        }
        else if($value['status'] == 2){
            $data['total_action']['request'] = $value['amounts'];
        }
        else if($value['status'] == 1){
            $data['total_action']['unpaid'] = $value['amounts'];
        }
    }

    /* Sale Counts */
    $data['total_product_sale']             = $this->db->query("SELECT sum(amount) as amounts,count(*) as counts FROM wallet WHERE status > 0 AND type = 'refer_sale_commission' ". $where)->row_array();
    $data['total_product_sale']['paid']     = $this->db->query("SELECT sum(amount) as amounts FROM wallet WHERE status=3 AND type = 'refer_sale_commission' ". $where)->row()->amounts;
    $data['total_product_sale']['request']  = $this->db->query("SELECT sum(amount) as amounts FROM wallet WHERE status=2 AND type = 'refer_sale_commission' ". $where)->row()->amounts;
    $data['total_product_sale']['unpaid']   = $this->db->query("SELECT sum(amount) as amounts FROM wallet WHERE status=1 AND type = 'refer_sale_commission' ". $where)->row()->amounts;


    return $data;
}

public function setAffiliateStoreClick($affiliate_id, $user_id, $affiliateads_type){

    $store_commition_setting = $this->Product_model->getSettings('referlevel');
    $disabled_for = json_decode( (isset($store_commition_setting['disabled_for']) ? $store_commition_setting['disabled_for'] : '[]'),1);
    if((int)$store_commition_setting['status'] == 0){ return false; }
    else if((int)$store_commition_setting['status'] == 2 && in_array($user_id, $disabled_for)){ return false; }


    $ip_address = $_SERVER['REMOTE_ADDR'];
    $level = $this->getMyLevel($user_id);
    $cdate = date('Y-m-d h:i:s');
    $count_for = '';
    $count_for .= (int)$level['level1'] > 0 ? $level['level1'] ."," : "";
    $count_for .= (int)$level['level2'] > 0 ? $level['level2'] ."," : "";
    $count_for .= (int)$level['level3'] > 0 ? $level['level3'] ."," : "";
    if($count_for){
        $this->db->from('refer_market_action');
        $this->db->where('affiliate_id', $affiliate_id);
        $this->db->where('user_id', $user_id);
        $this->db->where('user_ip', $ip_address);
        $result = $this->db->get()->num_rows()  ;

        if($result == 0){
            $new_record = array(
                'affiliate_id' => $affiliate_id,
                'user_id'      => $user_id,
                'count_for' => trim($count_for,","),
                'user_ip'      => $ip_address,
                'created_at'   => $cdate,
                'commission'   => 0
            );
            $this->db->insert('refer_market_action', $new_record);
        }
        $this->db->select('COUNT(*) AS total');
        $this->db->from('refer_market_action');
        $this->db->where('user_id',$user_id);
        $this->db->where('commission',0);
        $totalClick = $this->db->get()->row()->total;
        $_needClick = (int)$store_commition_setting['m_click'];
        if($totalClick >= $_needClick){
            $setting = $this->Product_model->getSettings('referlevel');
            $max_level = isset($setting['levels']) ? (int)$setting['levels'] : 3;

            $this->load->model('Mail_model');
            for ($l=1; $l <= $max_level ; $l++) {
                $s = $this->Product_model->getSettings('referlevel_'. $l);
                $levelUser = (int)$level['level'. $l];
                if($s && $levelUser > 0){
                    $_giveAmount = (float)$s['m_commition'];
                    $this->Wallet_model->addTransaction(array(
                        'user_id' => $levelUser,
                        'amount' => $_giveAmount,
                        'dis_type' => $affiliateads_type,
                        'comment' => "Level {$l} Commition  For {$totalClick} click on Affiliate link",
                        'type' => 'store_m_commission',
                        'reference_id' => $affiliate_id,
                    ));
                }
            }
            $this->db->query("UPDATE  refer_market_action SET commission = 1 WHERE user_id = '{$user_id}' AND  affiliate_id = '{$affiliate_id}' ");
        }
    }
}

public function getMyLevel($user_id, $setting = null){
    if($setting == null) {
        $setting = $this->getSettings('referlevel');
    }
    
    $max_level = isset($setting['levels']) ? (int)$setting['levels'] : 3;

    $select = [];

    $join=  [];

    for ($i=1; $i <= $max_level ; $i++) {
        $select[] = "l{$i}.id as level{$i},l{$i}.firstname as name_level{$i}";
        $join[] = " LEFT JOIN users as l{$i} ON (l{$i}.id=l". ($i-1) .".refid AND  l{$i}.type = 'user') ";
    }

    $q = $this->db->query("SELECT ". implode(",", $select) .", l0.id as myid
        FROM `users` as l0
        ". implode(" ", $join) ."
        WHERE l0.id=" . (int)$user_id
    )->row_array();

    return $q;
}

public function ip_info($ip = false) {
    $output = array('country_code' => '');

    if(!$ip){
        $ip = $_SERVER['REMOTE_ADDR'];
    }

    if (filter_var($ip, FILTER_VALIDATE_IP)) {

        $curl = curl_init("http://www.geoplugin.net/json.gp?ip=" . $ip);
        $request = '';
        curl_setopt($curl, CURLOPT_POSTFIELDS, $request);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $ipdat = json_decode(curl_exec($curl));
        if (@strlen(trim($ipdat->geoplugin_countryCode)) == 2) {
            $output = array(
                "ip"             => $ip,
                "city"           => @$ipdat->geoplugin_city,
                "state"          => @$ipdat->geoplugin_regionName,
                "country"        => @$ipdat->geoplugin_countryName,
                "country_code"   => @$ipdat->geoplugin_countryCode,
                "continent_code" => @$ipdat->geoplugin_continentCode,
                "regionCode"     => @$ipdat->geoplugin_regionCode,
                "regionName"     => @$ipdat->geoplugin_regionName,
                "countryCode"    => @$ipdat->geoplugin_countryCode,
                "countryName"    => @$ipdat->geoplugin_countryName,
                "continentName"  => @$ipdat->geoplugin_continentName,
                "timezone"       => @$ipdat->geoplugin_timezone,
                "currencyCode"   => @$ipdat->geoplugin_currencyCode,
                "currencySymbol" => @$ipdat->geoplugin_currencySymbol,
            );
        }
    }

    return $output;
}

function setAffiliateClick($affiliate_id, $user_id, $affiliateads_type){

    $ip_address = $_SERVER['REMOTE_ADDR'];
    $this->db->from('affiliate_action');
    $this->db->where('affiliate_id', $affiliate_id);
    $this->db->where('user_id', $user_id);
    $this->db->where('user_ip', $ip_address);
    $result = $this->db->get()->num_rows();

    if($result == 0){
        $cdate = date('Y-m-d h:i:s');
        $new_record = array(
            'affiliate_id' => $affiliate_id,
            'user_id'      => $user_id,
            'user_ip'      => $ip_address,
            'created_at'   => $cdate,
            'commission'   => 0,
            'country_code' => @$this->ip_info()['country_code'],
        );
        $this->db->insert('affiliate_action', $new_record);


        $this->db->select('*');
        $this->db->from('affiliate_action');
        $this->db->where('user_id',$user_id);
        $this->db->where('affiliate_id',$affiliate_id);
        $this->db->where('commission',0);
        $totalClick = $this->db->get();

        $setting = $this->Product_model->getSettings('affiliateprogramsetting');

        if($setting['affiliate_ppc'] <= $totalClick->num_rows()){
            $tC = $totalClick->num_rows();
            $ips = [];

            foreach ($totalClick->result() as $vv) {
                $ips[] = array(
                    'ip' => $vv->user_ip,
                    'country_code' => $vv->country_code,
                );
            }

            $this->load->model('Mail_model');
            $this->Mail_model->market_click_notification($user_id,$affiliate_id,$affiliateads_type,$setting['affiliate_commission']);
            $this->Wallet_model->addTransaction(array(
                'user_id'      => $user_id,
                'amount'       => $setting['affiliate_commission'],
                'dis_type'     => $affiliateads_type,
                'comment'      => "Commission for {$tC} click On {$affiliateads_type} <br> Clicked done from ip_message",
                'type'         => 'affiliate_click_commission',
                'reference_id' => $affiliate_id,
                'ip_details'   => json_encode($ips),
            ));
            $this->db->query("UPDATE  affiliate_action SET commission = 1 WHERE user_id = '{$user_id}' AND  affiliate_id = '{$affiliate_id}' ");
        }

    }
}

function user_info($user_id){
    $this->db->from('users');
    $this->db->where('id', $user_id);
    $query = $this->db->get()->row();
    return $query;
}

function update_payment($cdate){
    $this->db->from('payment');
    $this->db->where('payment_created_date', $cdate);
    $this->db->update('payment', array(
        'payment_item_status' => 'Completed'
    ));
}

function update_avg_rating($product_id){
    $this->db->select('avg(rating_number) as avg');
    $this->db->from('rating');
    $this->db->where('products_id', $product_id);
    $result =  $this->db->get()->row();
    
    if(isset($result) && isset($result->avg) && !empty($result->avg)) {
        $this->db->set('product_avg_rating', round($result->avg));
    } else {
        $this->db->set('product_avg_rating', 0);
    }
    $this->db->where('product_id', $product_id);
    return $this->db->update('product');
}

function getProductCommission($user_id = null){
    if(empty($user_id)){
        $sub_query = '1 = 1';
    }else{
        $sub_query = 'user_id = '. $user_id;
    }
    $query = 'SELECT SUM(commission) AS total_commission FROM product_action WHERE '. $sub_query;
    $value['total_commission'] = $this->db->query($query)->row()->total_commission;
    $query = 'SELECT COUNT(action_type) AS click FROM product_action WHERE ' . $sub_query . ' AND action_type = "click"';
    $value['click'] = $this->db->query($query)->row()->click;
    return $value;
}
function getAffiliateCommission($user_id = null){
    if(empty($user_id)){
        $sub_query = '1 = 1';
    }else{
        $sub_query = 'user_id = '. $user_id;
    }
    $query = 'SELECT COUNT(id) AS click, SUM(commission) AS total_commission FROM affiliate_action WHERE '. $sub_query;
    return $this->db->query($query)->row_array();
}
function getCommissionType(){
    $this->db->from('setting');
    $this->db->where('setting_key', 'product_commission_type');
    return $this->db->get()->row()->setting_value;
}
function add_product_media($user_id, $cdate){
    $this->db->from('product');
    $this->db->where('product_created_by', $user_id);
    $this->db->where('product_created_date', $cdate);
    $product = $this->db->get()->row();
    if(!empty($product->product_featured_image)){
        $image_data = array(
            'product_id' => $product->product_id,
            'product_media_upload_type' => 'image',
            'product_media_upload_path' => $product->product_featured_image,
            'product_media_upload_status' => 1,
            'product_media_upload_ipaddress' => $product->product_ipaddress,
            'product_media_upload_created_date'=>$cdate
        );
        $this->db->insert('product_media_upload', $image_data);
    }
    if(!empty($product->product_video)){
        $image_data = array(
            'product_id' => $product->product_id,
            'product_media_upload_type' => 'video',
            'product_media_upload_path' => $product->product_video,
            'product_media_upload_status' => 1,
            'product_media_upload_ipaddress' => $product->product_ipaddress,
            'product_media_upload_created_date'=>$cdate
        );
        $this->db->insert('product_media_upload', $image_data);
    }
}
public function parseDownloads($downloadable_files,$type=null){
    $_data = json_decode($downloadable_files, 1);
    $data = [];
    if($type =='video'  || $type =="videolink") {
        return $_data;
    }
    foreach($_data as $key =>$value){
        
        $data[$value['name']] = array(
            'mask' => $value['mask'],
            'name' => $value['name'],
            'type' => $this->get_types($value['type']),
            'videotext' => $value['videotext']??null,
            'thumb' => $value['thumb']??null,
        );
    }
    return $data;
}
private function get_types($filetype){
    switch (true) {
        case preg_match('/image/', $filetype): return 'image';
        case preg_match('/video/', $filetype): return 'video';
        case preg_match('/audio/', $filetype): return 'audio';
        case preg_match('/pdf/', $filetype): return 'pdf';
        case preg_match('/(csv|excel)/', $filetype): return 'spreadsheet';
        case preg_match('/powerpoint/', $filetype): return 'powerpoint';
        case preg_match('/(msword|text)/', $filetype): return 'document';
        case preg_match('/zip/', $filetype): return 'zip';
        case preg_match('/rar/', $filetype): return 'rar';
        default: return 'default-filetype';
    }
}
public function isMembershipAccess(){
    $membership = $this->getSettings('membership');
    $userdetails = $this->userdetails('user',1);

    if(($membership['status'] == 1) || (($membership['status'] == 2) && ($userdetails['is_vendor'] == 1)) || (($membership['status'] == 3) && ($userdetails['is_vendor'] == 0))){
        return true;
    }else{
        return false;
    }
}

public function checkLevelForUser($id){
    $sql = "SELECT `users`.`id`
    FROM `award_level`
    INNER JOIN `users`
    ON `users`.`level_id` = `award_level`.`id`
    WHERE `users`.`level_id` = ?

    UNION ALL

    SELECT `t`.`id` 
    FROM (
        SELECT `membership_user`.`id`
        FROM `membership_user`
        INNER JOIN `membership_plans`
        ON `membership_plans`.`id` = `membership_user`.`plan_id`
        WHERE `membership_user`.`is_active` = 1
        AND `membership_plans`.`level_id` = ?
        ORDER BY `membership_user`.`created_at` DESC 
        ) as `t`
    LIMIT 1 ";

    $query = $this->db->query($sql,array((int) $id,(int) $id));
    $result = $query->result_array();

    return $result;
}

public function checkJumpedUser($limit,$offset){
    $sql = 'SELECT `users`.`id`,
    `users`.`level_id`,
    `users`.`email`,
    `award_level`.`level_number`,
    `award_level`.`jump_level`,
    `award_level`.`minimum_earning`,
    `award_level`.`bonus`
    FROM `users`
    INNER JOIN `award_level`
    ON `award_level`.`id` = `users`.`level_id`
    LIMIT ? OFFSET ?';

    $query = $this->db->query($sql,array((int) $limit,(int) $offset));
    $user_level = $query->row_array();

    if($user_level){
        $this->load->model('Total_model');
        $userBalance = $this->Total_model->getUserBalance($user_level['id']);

        $sql = 'SELECT `t`.`id`,
        `t`.`level_number`
        FROM (
            SELECT `award_level`.`id`,
            `award_level`.`level_number`,
            `award_level`.`minimum_earning`
            FROM `award_level`
            WHERE `award_level`.`minimum_earning` > ?
            ) as `t`
        ORDER BY `t`.`minimum_earning` asc 
        LIMIT 0,1';
        $query = $this->db->query($sql,$userBalance);
        $change_level = $query->row_array();

        if($user_level['minimum_earning'] <= $userBalance || ($user_level['minimum_earning'] > $userBalance && $change_level['id'] != $user_level['level_id'])){

            $change_level_id = ($change_level) ? $change_level['id'] : $user_level['jump_level'];

            $update['level_id'] = $change_level_id;
            $levelSuccess = $this->db->update('users',$update,['id' => $user_level['id']]);

            if($levelSuccess){
                if($user_level['bonus']){
                    $walletSuccess = $this->Wallet_model->addTransaction(
                        array(
                            'status'         => 1,
                            'user_id'        => $user_level['id'],
                            'amount'         => $user_level['bonus'],
                            'comment'        => __('admin.bonus'),
                            'type'           => 'award_level_comission',
                            'dis_type'       => '',
                            'comm_from'      => '',
                            'reference_id'   => 0,
                            'reference_id_2' => 0,
                            'ip_details'     => '',
                            'domain_name'    => '',
                            'group_id'       => time().rand(10,100)

                        )
                    );    
                } else {
                    $walletSuccess = true;
                }

                if($walletSuccess){
                    if($change_level){
                        $to_level = $change_level['level_number']; 
                    } else {
                        $change_level = $this->Product_model->getByField('award_level','id',$user_level['jump_level']);
                        if($change_level)
                            $to_level = $change_level['level_number']; 
                        else
                            $to_level = __('admin.default');
                    }

                    $this->load->model('Mail_model');
                    $this->Mail_model->user_level_changed($user_level['id'],$user_level['email'],$user_level['level_number'],$to_level);

                    return true;
                } else {
                    $update['level_id'] = $user_level['level_id'];
                    $this->db->update('users',$update,['id' => $user_level['id']]);
                }
            }
        }
    }

    return false;
}

public function checkJumpedUserWithId($id)
{
    $sql = 'SELECT `users`.`id`,
    `users`.`level_id`,
    `users`.`email`,
    `award_level`.`level_number`,
    `award_level`.`jump_level`,
    `award_level`.`minimum_earning`,
    `award_level`.`bonus`
    FROM `users`
    INNER JOIN `award_level`
    ON `award_level`.`id` = `users`.`level_id`
    WHERE `users`.`id` = ?';

    $query = $this->db->query($sql,(int) $id);
    $user_level = $query->row_array();

    if($user_level){
        $this->load->model('Total_model');
        $userBalance = $this->Total_model->getUserBalance($user_level['id']);

        $sql = 'SELECT `t`.`id`,
        `t`.`level_number`
        FROM (
            SELECT `award_level`.`id`,
            `award_level`.`level_number`,
            `award_level`.`minimum_earning`
            FROM `award_level`
            WHERE `award_level`.`minimum_earning` > ?
            ) as `t`
        ORDER BY `t`.`minimum_earning` asc 
        LIMIT 0,1';
        $query = $this->db->query($sql,$userBalance);
        $change_level = $query->row_array();

        if($user_level['minimum_earning'] <= $userBalance || ($user_level['minimum_earning'] > $userBalance && $change_level['id'] != $user_level['level_id'])){

            $change_level_id = ($change_level) ? $change_level['id'] : $user_level['jump_level'];

            $update['level_id'] = $change_level_id;
            $this->db->update('users',$update,['id' => $user_level['id']]);

            if($user_level['bonus']){
                $this->Wallet_model->addTransaction(
                    array(
                        'status'         => 1,
                        'user_id'        => $user_level['id'],
                        'amount'         => $user_level['bonus'],
                        'comment'        => __('admin.bonus'),
                        'type'           => 'award_level_comission',
                        'dis_type'       => '',
                        'comm_from'      => '',
                        'reference_id'   => 0,
                        'reference_id_2' => 0,
                        'ip_details'     => '',
                        'domain_name'    => '',
                        'group_id'       => time().rand(10,100)

                    )
                );    
            }

            if($change_level){
                $to_level = $change_level['level_number']; 
            } else {
                $sql = 'SELECT `award_level`.`level_number`
                FROM `award_level`
                WHERE `award_level`.`id` = ?';

                $query = $this->db->query($sql,(int) $user_level['jump_level']);
                $change_level = $query->row_array();

                if($change_level)
                    $to_level = $change_level['level_number']; 
                else
                    $to_level = __('admin.default');
            }

            $this->load->model('Mail_model');
            $this->Mail_model->user_level_changed($user_level['id'],$user_level['email'],$user_level['level_number'],$to_level);
        }          
    }

    return;
}

public function updateWithLike($table, $field, $like, $data){
    $this->db->like($field,$like);
    return $this->db->update($table, $data);
}

public function getAll($table,$limit = false, $offset = 0, $orderby = false){
    $ci = get_instance();
    if($limit != false) {
        $ci->db->limit($limit, $offset);
    }
    if ($orderby != false) {
        $ci->db->order_by($orderby);
    }
    $ci->db->from($table);
    $query = $ci->db->get();
    $result = $query->result_array();

    return $result;
} 

public function getAllFor($table,$field,$value,$limit = false, $offset = 0, $orderby = false){
    $ci = get_instance();
    if($limit != false) {
        $ci->db->limit($limit, $offset);
    }
    if ($orderby != false) {
        $ci->db->order_by($orderby);
    }
    $ci->db->from($table);
    $ci->db->where($field,$value);
    $query = $ci->db->get();
    $result = $query->result_array();

    return $result;
}

public function getAllWithExcept($table,$field,$value,$limit = false, $offset = 0, $orderby = false){
    $ci = get_instance();
    if($limit != false) {
        $ci->db->limit($limit, $offset);
    }
    if ($orderby != false) {
        $ci->db->order_by($orderby);
    }
    $ci->db->from($table);
    $ci->db->where($field.' !=',$value);
    $query = $ci->db->get();
    $result = $query->result_array();

    return $result;
}

public function getByField($table,$field,$value){
    $ci = get_instance();
    $ci->db->select('*');
    $ci->db->from($table);
    $ci->db->where($field,$value);
    $query = $ci->db->get();
    $result = $query->row_array();

    return $result;
}

public function countByTable($table){
   $ci = get_instance();
   $ci->db->from($table);
   $result = $ci->db->count_all_results();

   return $result;
}

public function countByField($table,$field,$value){
   $ci = get_instance();
   $ci->db->where($field,$value);
   $ci->db->from($table);
   $result = $ci->db->count_all_results();

   return $result;
}

public function setBrowserLanguage() {
    $languages = explode(',',$_SERVER['HTTP_ACCEPT_LANGUAGE']);

    foreach($languages as $lang) {
        $all_languages = json_decode(file_get_contents('assets/data/languages.json')); 

        $admin_languge = $this->db->query('SELECT id,name FROM language')->result();

        $lang = explode("-", $lang);
        $lang = explode(";", $lang[0]);

        if(isset($all_languages->{$lang[0]})) {
            foreach ($admin_languge as $l) {
                if($l->name == $all_languages->{$lang[0]}) {
                    $_SESSION['userLang'] = $l->id;
                    $langChanged = true;
                    break;
                }
            }
        }

        if(isset($langChanged)) {
            break;
        }           
    }
}

public function getBlockedVendors() {
    $result = [];
    $restricted_vendors = $this->db->query('select id from users where status=0')->result();
    foreach($restricted_vendors as $v) {
        $result[] = $v->id;
    }
    return $result;
}

public function delete_wallet_integration_clicks_action($current_transaction) {
    if (strpos($current_transaction->type, 'form_click') !== false && strpos($current_transaction->type, 'refer') === false) {
        if($current_transaction->comm_from === 'store') {
            $ct_record = $this->db->query('select action_id from form_action where 
                action_type="click" and user_id='.$current_transaction->user_id.' and form_id='.$current_transaction->reference_id.' limit 1')->row();

            if(!empty($ct_record)) {
                $this->db->query("DELETE FROM form_action WHERE action_id = ". $ct_record->action_id);
            }
        }
    } else if (strpos($current_transaction->type, 'click') !== false && strpos($current_transaction->type, 'refer') === false) {

        if($current_transaction->comm_from === 'store') {
            $ct_record = $this->db->query('select action_id from product_action where 
                action_type="click" and user_id='.$current_transaction->user_id.' and product_id='.$current_transaction->reference_id.' limit 1')->row();

            if(!empty($ct_record)) {
                $this->db->query("DELETE FROM product_action WHERE action_id = ". $ct_record->action_id);
            }
            $ct_record = $this->db->query('select action_id from product_action_admin where 
                action_type="click" and user_id='.$current_transaction->user_id.' and product_id='.$current_transaction->reference_id.' limit 1')->row();

            if(!empty($ct_record)) {
                $this->db->query("DELETE FROM product_action_admin WHERE action_id = ". $ct_record->action_id);
            }
        } else {
            $ct_record = $this->db->query('select id from integration_clicks_action where is_action='.$current_transaction->is_action.' and user_id='.$current_transaction->user_id.' and tools_id='.$current_transaction->reference_id.' and base_url="'.$current_transaction->domain_name.'" limit 1')->row();

            if(!empty($ct_record)) {
                $this->db->query("DELETE FROM integration_clicks_action WHERE id = ". $ct_record->id);
            }
        }
    }
}


public function productDataWithMeta($product) {
    $meta = $this->db->get_where('product_meta', ['related_product_id' => $product['product_id']])->result_array();

    foreach($meta as $m) {
        $product['_meta_'.$m['meta_key']] = $m['meta_value'];
    }

    return $product;
}

public function categoryInfo($categorySlug) {
    $categoryInfo = $this->db->query("select id from categories where slug='".$categorySlug."'")->result();

    return $categoryInfo;
}

public function getUserInfo($id){

    $userInfo = $this->db->query("select * from users where id='".$id."'")->result();

    return $userInfo;
}


    public function sendTicketNotification($data)
    {
        if(isset($data['admin_notification'])) {
            $notificationAdmin = array(
                'notification_url'          => 'ticketdetails/'.$data['id'],
                'notification_type'         =>  $data['type'],
                'notification_title'        =>  $data['title'],
                'notification_viewfor'      =>  'admin',
                'notification_view_user_id' =>  $data['admin_notification'],
                'notification_actionID'     =>  '',
                'notification_description'  =>  $data['desc'],
                'notification_is_read'      =>  '0',
                'notification_created_date' =>  date('Y-m-d H:i:s'),
                'notification_ipaddress'    =>  $_SERVER['REMOTE_ADDR'],
            );
            $this->db->insert('notification', $notificationAdmin);
        }
        
        if(isset($data['user_notification'])) {
            $notificationUser = array(
                'notification_url'          => 'ticketdetails/'.encryptString($data['id']),
                'notification_type'         =>  $data['type'],
                'notification_title'        =>  $data['title'],
                'notification_viewfor'      =>  'user',
                'notification_view_user_id' =>  $data['user_notification'],
                'notification_actionID'     =>  '',
                'notification_description'  =>  $data['desc'],
                'notification_is_read'      =>  '0',
                'notification_created_date' =>  date('Y-m-d H:i:s'),
                'notification_ipaddress'    =>  $_SERVER['REMOTE_ADDR']
            );
            $this->db->insert('notification', $notificationUser);
        }
    }


    public function getVendorSettingById($vendor_id)
    {
        return $this->db->get_where("vendor_setting", ["user_id" => (int)$vendor_id])->row();
    }

    public function getUserPaymentMethodStatus($user_id,$PrimaryPaymentMethod)
    {
        $status=false;
         
        if($PrimaryPaymentMethod=='paypal')
        {
           $data = $this->getPaypalAccounts($user_id);
            if (isset($data) && is_array($data) && isset($data[0]) && $data[0]['paypal_email']!="") 
             {
                 $status=true;
             }
        }
        else if($PrimaryPaymentMethod=='bank_transfer')
        {
            $data=$this->getAllPayment($user_id);
          
             if (isset($data) && is_array($data) && isset($data[0]) && $data[0]['payment_id']>0) 
            {
                $status=true;
            }
        }
        return $status;
    }

    public function save_view_logs($data)
    {
        $row=$this->db->get_where("product_view_logs", ["user_id" => $data['user_id'],"product_id" => $data['product_id'],"ip" => $data['ip'],"session_id" => $data['session_id']])->row();
 
       if(isset($row))
            $result= 2;
       else
       {
            $this->load->library('Uagent');
            $this->uagent->init();
            $uagentString = $this->uagent->string;
            if(empty($uagentString)) {
                $logData = $this->session->userdata('uncompleted_uagent');
            } else {
                $logData = array(
                    'agent'          => $this->uagent->string,
                    'browserName'    => $this->uagent->browserName,
                    'browserVersion' => $this->uagent->browserVersion,
                    'systemString'   => $this->uagent->systemString,
                    'osPlatform'     => $this->uagent->osPlatform,
                    'osVersion'      => $this->uagent->osVersion,
                    'osShortVersion' => $this->uagent->osShortVersion, 
                    'ip'      =>  $data['ip'],
                    'created_at'      =>  date('Y-m-d H:m:s')
                );
            }

            $viewData=array_merge($data,$logData);

            $this->db->insert('product_view_logs', $viewData);

            $insert_id =  $this->db->insert_id();
             $result= 1;
       }

       return $result;
 
    } 
    public function getPaymentMethodsList($filter = array()){
        $files = array();
        foreach (glob(APPPATH."/withdrawal_payment/controllers/*.php") as $file) {
            $files[] = $file;
        }
        $methods = array_unique($files);
        echo '<pre>';
        print_r($methods);exit;
        $payment_methods = array();
        foreach ($methods as $key => $filename) {
            $code = basename($filename, ".php");
            $pdata = $this->getDetails($code, $filter);
            if($pdata){
                $payment_methods[$code] = $pdata;
            }
        }

        return $payment_methods;
    } 
}