<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

require(APPPATH.'/libraries/REST_Controller.php');
use Restserver\Libraries\REST_Controller;
require APPPATH . 'libraries/Format.php';

class Vendor_Market_Place extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('email');
        $this->load->library('form_validation');
        $this->load->model('Common_model');
        $this->load->model('Product_model');
        $this->load->model('Total_model');
        $this->load->library('user_agent');
        $this->load->model("Form_model");
        $this->load->model('IntegrationModel');
        $this->load->model("Coupon_model");
    }

    public function store_product_list_get()
    {
        $headers = $this->input->request_headers();
        $verify_data = verify_request();
        if(isset($verify_data['status']) && $verify_data['status'] == 401) {
            $response = array(
                'status' => 401,
                'message' => 'Unauthorized Access!',
            );

            $this->response($response, 401); 
        }
        else
        {
            $id = $verify_data['userdata']['id'];
            $type = $verify_data['userdata']['type'];

            $vendor_setting = $this->Product_model->getSettings('vendor');
            $store_setting = $this->Product_model->getSettings('store');

            $filter = array(
                'seller_id' => $id,
            );
            $get = $this->input->get(null,true);

            $data['default_commition'] =$this->Product_model->getSettings('productsetting');
            $data['productlist'] = $this->Product_model->getAllProductForVendor($id, $type, $filter);

            $response = array(
                'status' => TRUE,
                'message' => 'store product list get successfully',
                'data' => $data
            );
            $this->response($response, REST_Controller::HTTP_OK);
        }
    }

    public function manage_product_post()
    {
        $headers = $this->input->request_headers();
        $verify_data = verify_request();
        if(isset($verify_data['status']) && $verify_data['status'] == 401) {
            $response = array(
                'status' => 401,
                'message' => 'Unauthorized Access!',
            );

            $this->response($response, 401); 
        }
        else
        {
            $id = $verify_data['userdata']['id'];
            $device_type = $verify_data['userdata']['device_type'];
            $device_token = $verify_data['userdata']['device_token'];
            $username = $verify_data['userdata']['username'];
            $post = $this->input->post(null,true);
            if(!empty($post)){
            $product_id = (int)$this->input->post('product_id',true);
            $this->load->helper(array('form', 'url'));
            $this->load->library('form_validation');

            $this->form_validation->set_rules('product_name', __('admin.product_name_'), 'required');
            $this->form_validation->set_rules('product_description', __('admin.product_description'), 'required' );

            if($post['allow_country'] == "1"){
                $this->form_validation->set_rules('country_id', 'Country', 'required' );
                $this->form_validation->set_rules('state_id', 'State', 'required' );
            }
            $this->form_validation->set_rules('product_short_description', __('admin.short_description'),'required|min_length[5]|max_length[150]',
                array(
                    'required'      => 'Enter %s',
                    'is_unique'     => 'This %s already exists.',
                    'min_length'    => '%s: the minimum of characters is %s',
                    'max_length'    => '%s: the maximum of characters is %s',
                )
            );
            $this->form_validation->set_rules('category[]',"Category", "required");
            $this->form_validation->set_rules('product_price', 'Product Price', 'required');
            $this->form_validation->set_rules('product_sku', 'Product SKU', 'required');
            // $this->form_validation->set_rules('product_video', 'Product Video', 'trim');

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

                //call product edit time
                // if($product_id){
                //     $product_details = $this->Product_model->getProductById($product_id);
                //     $_downloads = $this->Product_model->parseDownloads($product_details->downloadable_files);

                //     foreach ($post['keep_files'] as $_value) {
                //         if(isset($_downloads[$_value])){
                //             $downloadable_files[] = $_downloads[$_value];
                //         } else{
                //             @unlink(APPPATH.'/downloads/'.$_value);
                //         }
                //     }
                // }
                //end call product edit time

                $details = array(
                    'product_name'                 =>  $post['product_name'],
                    'product_description'          =>  $post['product_description'],
                    'product_short_description'    =>  $post['product_short_description'],
                    'product_price'                =>  $post['product_price'],
                    'product_sku'                  =>  $post['product_sku'],
                    // 'product_video'                =>  $post['product_video'],
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
                    //'product_status'             =>  1,
                    'product_ipaddress'            =>  $_SERVER['REMOTE_ADDR'],
                    'product_recursion_type'       =>  $post['product_recursion_type'],
                    'recursion_endtime'            =>  (isset($post['recursion_endtime_status']) && $post['recursion_endtime']) ? date("Y-m-d H:i:s",strtotime($post['recursion_endtime'])) : null,
                    'product_recursion'            =>  $product_recursion,
                    'recursion_custom_time'        =>  (int)$recursion_custom_time,
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
                    $count_file = count($_FILES['downloadable_file']['name']);
                

                    $this->load->helper('string');  

                    for($i=0; $i<$count_file; $i++){
                        $extension = pathinfo($files['name'][$i], PATHINFO_EXTENSION);
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
                            }else{
                                $errors['downloadable_files'] = $FILES['downloadable_files']['error'];
                            }
                        } else {
                            $errors['downloadable_files'] = 'Only zip file are allow..';
                        }
                    }
                }

                $new_product_created = false;
                $old_product_data = [];
                if(empty($errors)){
                    $details['downloadable_files'] = json_encode($downloadable_files);

                    $details['product_created_by'] = $id;
                    $details['product_created_date'] = date('Y-m-d H:i:s');             


                    if($product_id){
                        $old_product_data = $this->db->query("SELECT * FROM product WHERE product_id = ". (int)$product_id)->row_array();

                        $this->Product_model->update_data('product', $details, array('product_id' => $product_id));
                    }else{
                        $details['product_status'] = 0;
                        $product_id = $this->Product_model->create_data('product', $details);

                        $new_product_created = true;

                        $notificationData = array(
                            'notification_url'          => 'updateproduct/'.$product_id,
                            'notification_type'         =>  'vendor_product',
                            'notification_title'        =>  __('admin.new_product_added_by_vendor'),
                            'notification_viewfor'      =>  'admin',
                            'notification_actionID'     =>  $product_id,
                            'notification_description'  =>  $post['product_name'].' product is addded by '. $username .' in store on '.date('Y-m-d H:i:s'),
                            'notification_is_read'      =>  '0',
                            'notification_created_date' =>  date('Y-m-d H:i:s'),
                            'notification_ipaddress'    =>  $_SERVER['REMOTE_ADDR']
                        );

                        $this->insertnotification($notificationData);
                    }

                    $title = __('admin.new_product_added_by_vendor');
                    $message = $post['product_name'].' product is addded by '. $username .' in store on '.date('Y-m-d H:i:s');

                    if($device_type == 1)
                    {
                        send_push_android($device_token,$title,$message);
                    }
                    else if($device_type == 2)
                    {
                        send_push_ios($device_token,$title,$message);
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

                    

                        $old_setting = $this->db->query("SELECT * FROM product_affiliate WHERE product_id=". (int)$product_id ." ")->row();

                        if($old_setting){
                            if(
                                $seller_comm['affiliate_click_commission_type'] != $old_setting->affiliate_click_commission_type ||
                                $seller_comm['affiliate_click_count']           != $old_setting->affiliate_click_count ||
                                $seller_comm['affiliate_click_amount']          != $old_setting->affiliate_click_amount ||
                                $seller_comm['affiliate_sale_commission_type']  != $old_setting->affiliate_sale_commission_type ||
                                $seller_comm['affiliate_commission_value']      != $old_setting->affiliate_commission_value
                            ){
                                $this->db->query("UPDATE product SET product_status=0 WHERE product_id=". (int)$product_id);
                            }
                        }

                        if($post['action'] == 'ask_to_review'){
                            $this->db->query("UPDATE product SET product_status=0 WHERE product_id=". (int)$product_id);
                        }

                        $this->Product_model->assignToSeller($product_id, $details, $id, $admin_comment,'affiliate', $seller_comm);

                        $this->load->model('Mail_model');
                        if($new_product_created){
                            $this->Mail_model->vendor_create_product($product_id);
                        } else {
                            $product_data = $this->db->query("SELECT * FROM product WHERE product_id = ". (int)$product_id)->row_array();

                            if($old_product_data['product_status'] != $product_data['product_status'] && $product_data['product_status'] == 0){
                                $this->Mail_model->vendor_product_status_change($product_id, 'admin',true);
                            }
                        }
                    }  
                    else{
                     if(isset($post['category']) && is_array($post['category'])){
                        foreach ($post['category'] as $category_id) {
                            $category = array(
                                'product_id' => $product_id,
                                'category_id' => $category_id,
                            );

                            $this->Product_model->create_data('product_categories', $category);
                        }
                    }
                    }             


                    $json['status'] = TRUE;
                    $json['message'] = 'product manage successfully';
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
            
            $response = array(
                'status' => TRUE,
                'message' => 'product add successfully',
            );
            $this->response($response, REST_Controller::HTTP_OK);
        }
    }

    public function delete_product_delete()
    {
        $headers = $this->input->request_headers();
        $verify_data = verify_request();
        if(isset($verify_data['status']) && $verify_data['status'] == 401) {
            $response = array(
                'status' => 401,
                'message' => 'Unauthorized Access!',
            );

            $this->response($response, 401); 
        }
        else
        {
            $id = $verify_data['userdata']['id'];
            $product_id = $this->input->get('product_id');
            $delete = $this->Product_model->deleteproducts($product_id);
            if($delete)
            {
                $response = array(
                    'status' => TRUE,
                    'message' => 'product delete successfully'
                );
            }
            else
            {
                $response = array(
                    'status' => FALSE,
                    'message' => 'product delete failed'
                );
            }

            $this->response($response, REST_Controller::HTTP_OK);
        }
    }

    public function create_duplicate_product_post()
    {
        $headers = $this->input->request_headers();
        $verify_data = verify_request();
        if(isset($verify_data['status']) && $verify_data['status'] == 401) {
            $response = array(
                'status' => 401,
                'message' => 'Unauthorized Access!',
            );

            $this->response($response, 401); 
        }
        else
        {
            $this->form_validation->set_rules('product_id', 'product_id', 'required|trim',
            array('required'      => 'Oops ! product id is required.'
            ));

            $this->form_validation->set_error_delimiters('', '');
            if($this->form_validation->run()== false)
            {
                if(!empty(form_error('product_id')))$errors['product_id'] =form_error('product_id');

                $response['message'] = "Please required field";
                $response['errors'] = $errors;

                $this->response($response, 422);
            }
            else
            {

                $id = $verify_data['userdata']['id'];
                $product_id = $this->input->post('product_id');
                $duplicate = $this->Product_model->duplicateProduct($product_id,true);
                $response = array(
                    'status' => TRUE,
                    'message' => 'create duplicate product successfully'
                );

                $this->response($response, REST_Controller::HTTP_OK);
            }
        }
    }

    

    public function get_product_all_images_get()
    {
        $headers = $this->input->request_headers();
        $verify_data = verify_request();
        if(isset($verify_data['status']) && $verify_data['status'] == 401) {
            $response = array(
                'status' => 401,
                'message' => 'Unauthorized Access!',
            );

            $this->response($response, 401); 
        }
        else
        {
            $id = $verify_data['userdata']['id'];

            $product_id = $this->input->get('product_id');
             
            $data = $this->Product_model->getAllImages($product_id);

            $response = array(
                'status' => TRUE,
                'message' => 'get all images list successfully',
                'data' => $data
            );
            $this->response($response, REST_Controller::HTTP_OK);
        }
    }

    public function delete_product_image_delete()
    {
        $headers = $this->input->request_headers();
        $verify_data = verify_request();
        if(isset($verify_data['status']) && $verify_data['status'] == 401) {
            $response = array(
                'status' => 401,
                'message' => 'Unauthorized Access!',
            );

            $this->response($response, 401); 
        }
        else
        {
            $id = $verify_data['userdata']['id'];
            $image_id = $this->input->get('image_id');
            $delete = $this->Product_model->deleteImage($image_id);
            if($delete)
            {
                $response = array(
                    'status' => TRUE,
                    'message' => 'product image delete successfully'
                );
            }
            else
            {
                $response = array(
                    'status' => FALSE,
                    'message' => 'product image delete failed'
                );
            }

            $this->response($response, REST_Controller::HTTP_OK);
        }
    }

    public function get_countrie_list_get()
    {
        $headers = $this->input->request_headers();
        $verify_data = verify_request();
        if(isset($verify_data['status']) && $verify_data['status'] == 401) {
            $response = array(
                'status' => 401,
                'message' => 'Unauthorized Access!',
            );

            $this->response($response, 401); 
        }
        else
        {
            $id = $verify_data['userdata']['id'];

            $country_list = $this->db->query("SELECT name,id FROM countries")->result();

            $response = array(
                'status' => TRUE,
                'message' => 'country list get successfully',
                'data' => $country_list
            );
            $this->response($response, REST_Controller::HTTP_OK);
        }
    }


    public function add_product_images_post()
    {
        $headers = $this->input->request_headers();
        $verify_data = verify_request();
        if(isset($verify_data['status']) && $verify_data['status'] == 401) {
            $response = array(
                'status' => 401,
                'message' => 'Unauthorized Access!',
            );

            $this->response($response, 401); 
        }
        else
        {
            $this->form_validation->set_rules('product_id', 'product_id', 'required|trim',
            array('required'      => 'Oops ! prodcut id is required.'
            ));

            $this->form_validation->set_error_delimiters('', '');
            if($this->form_validation->run()== false)
            {
                if(!empty(form_error('product_id')))$errors['product_id'] =form_error('product_id');

                $response['message'] = "Please required field";
                $response['errors'] = $errors;

                $this->response($response, 422);
            }
            else
            {
                $id = $verify_data['userdata']['id'];
                $product_id = $this->input->post('product_id');

                $details = array(
                    'product_id'                        =>  $product_id,
                    'product_media_upload_type'         =>  'image',
                    'product_media_upload_status'       =>  1,
                    'product_media_upload_os'           =>  $this->agent->platform(),
                    'product_media_upload_browser'      =>  $this->agent->browser(),
                    'product_media_upload_isp'          =>  gethostbyaddr($_SERVER['REMOTE_ADDR']),
                    'product_media_upload_ipaddress'    =>  $_SERVER['REMOTE_ADDR'],
                    'product_media_upload_created_by'   =>  $id,
                    'product_media_upload_created_date' =>  date('Y-m-d H:i:s'),
                );

                $details['product_media_upload_created_by'] = $id;
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
                                $response = array(
                                    'status' => FALSE,
                                    'message' => $upload_details['msg']

                                );
                                $this->response($response, REST_Controller::HTTP_OK);
                            }

                            $details['product_media_upload_created_date'] = date('Y-m-d H:i:s');
                            $data = $this->Product_model->create_data('product_media_upload', $details);  
                        }
                    }
                }


                $response = array(
                    'status' => TRUE,
                    'message' => 'product images add successfully'
                );
                $this->response($response, REST_Controller::HTTP_OK);

            }
        }
    }


    public function get_state_list_post()
    {
        $headers = $this->input->request_headers();
        $verify_data = verify_request();
        if(isset($verify_data['status']) && $verify_data['status'] == 401) {
            $response = array(
                'status' => 401,
                'message' => 'Unauthorized Access!',
            );

            $this->response($response, 401); 
        }
        else
        {
            $this->form_validation->set_rules('country_id', 'country_id', 'required|trim',
            array('required'      => 'Oops ! country id is required.'
            ));

            $this->form_validation->set_error_delimiters('', '');
            if($this->form_validation->run()== false)
            {
                if(!empty(form_error('country_id')))$errors['country_id'] =form_error('country_id');

                $response['message'] = "Please required field";
                $response['errors'] = $errors;

                $this->response($response, 422);
            }
            else
            {
                $id = $verify_data['userdata']['id'];
                $country_id = $this->input->post('country_id');
                $where = array('country_id'=>$country_id);
                $country_list = $this->Common_model->get_data_all_asc('states', $where, 'id,name', 'id');

                $response = array(
                    'status' => TRUE,
                    'message' => 'state list get successfully',
                    'data' => $country_list
                );
                $this->response($response, REST_Controller::HTTP_OK);

            }
        }
    }

    public function get_product_name_get()
    {
        $headers = $this->input->request_headers();
        $verify_data = verify_request();
        if(isset($verify_data['status']) && $verify_data['status'] == 401) {
            $response = array(
                'status' => 401,
                'message' => 'Unauthorized Access!',
            );

            $this->response($response, 401); 
        }
        else
        {
            $id = $verify_data['userdata']['id'];

            $product = $this->db->query("SELECT p.product_id,p.product_name FROM product p LEFT JOIN product_affiliate pa ON(pa.product_id = p.product_id) WHERE pa.user_id = '".$id."'")->result_array();

            $response = array(
                'status' => TRUE,
                'message' => 'product name list get successfully',
                'data' => $product
            );
            $this->response($response, REST_Controller::HTTP_OK);
        }
    }

    public function manage_product_coupon_post()
    {
        $headers = $this->input->request_headers();
        $verify_data = verify_request();
        if(isset($verify_data['status']) && $verify_data['status'] == 401) {
            $response = array(
                'status' => 401,
                'message' => 'Unauthorized Access!',
            );

            $this->response($response, 401); 
        }
        else
        {
            $this->form_validation->set_rules('name', 'name', 'required|trim',
            array('required'      => 'Oops ! name is required.'
            ));

            $this->form_validation->set_rules('code', 'code', 'required|trim',
                array('required'      => 'Oops ! code is required.'
            ));

            $this->form_validation->set_rules('type', 'type', 'required|trim',
                array('required'      => 'Oops ! type is required.'
            ));

            $this->form_validation->set_rules('allow_for', 'allow_for', 'required|trim',
                array('required'      => 'Oops ! allow for is required.'
            ));

            $this->form_validation->set_rules('discount', 'discount', 'required|trim',
                array('required'      => 'Oops ! discount is required.'
            ));

            $this->form_validation->set_rules('date_start', 'date_start', 'required|trim',
                array('required'      => 'Oops ! date start is required.'
            ));

            $this->form_validation->set_rules('date_end', 'date_end', 'required|trim',
                array('required'      => 'Oops ! date end is required.'
            ));

            $this->form_validation->set_rules('status', 'status', 'required|trim',
                array('required'      => 'Oops ! status is required.'
            ));

            $this->form_validation->set_error_delimiters('', '');
            if($this->form_validation->run()== false)
            {
                if(!empty(form_error('name')))$errors['name'] =form_error('name');
                if(!empty(form_error('code')))$errors['code'] =form_error('code');
                if(!empty(form_error('type')))$errors['type'] =form_error('type');
                if(!empty(form_error('allow_for')))$errors['allow_for'] =form_error('allow_for');
                if(!empty(form_error('discount')))$errors['discount'] =form_error('discount');
                if(!empty(form_error('date_start')))$errors['date_start'] =form_error('date_start');
                if(!empty(form_error('date_end')))$errors['date_end'] =form_error('date_end');
                if(!empty(form_error('status')))$errors['status'] =form_error('status');

                $response['message'] = "Please required field";
                $response['errors'] = $errors;

                $this->response($response, 422);
            }
            else
            {
                $id = $verify_data['userdata']['id'];

                $data = $this->input->post(null,true);

                $coupon = array(
                    'vendor_id'  => $id,
                    'name'       => $data['name'],
                    'code'       => $data['code'],
                    'type'       => $data['type'],
                    'allow_for'  => $data['allow_for'],
                    'discount'   => $data['discount'],
                    'date_start' => date("Y-m-d", strtotime($data['date_start'])),
                    'date_end'   => date("Y-m-d", strtotime($data['date_end'])),
                    'uses_total' => $data['uses_total'],
                    'status'     => $data['status'],
                    'products'   => $data['products'] ? implode(",", $data['products']) : '',
                    'date_added' => date("Y-m-d H:i:s"),
                );

                if($data['id'] > 0){
                    unset($coupon['date_added']);
                    $manage = $this->db->update("coupon",$coupon,['coupon_id' => $data['id']]);
                    $message = 'coupon code update successfully';
                } else {
                    $this->db->insert("coupon",$coupon);
                    $manage = $this->db->insert_id();
                    $message = 'coupon code add successfully';
                }

                if($manage)
                {
                    $response = array(
                        'status' => TRUE,
                        'message' => $message
                    );
                    $this->response($response, REST_Controller::HTTP_OK);
                }
                else
                {
                    $response = array(
                        'status' => FALSE,
                        'message' => 'coupon manage failed'
                    );
                    $this->response($response, REST_Controller::HTTP_OK);
                }
            }
        }
    }

    public function delete_coupon_delete()
    {
        $headers = $this->input->request_headers();
        $verify_data = verify_request();
        if(isset($verify_data['status']) && $verify_data['status'] == 401) {
            $response = array(
                'status' => 401,
                'message' => 'Unauthorized Access!',
            );

            $this->response($response, 401); 
        }
        else
        {
            $id = $verify_data['userdata']['id'];

            $coupon_id = $this->input->get('coupon_id');
            
            $delete = $this->Coupon_model->deleteCoupon($coupon_id);
            if($delete)
            {
                $response = array(
                    'status' => TRUE,
                    'message' => 'delete coupon successfully',
                );
                $this->response($response, REST_Controller::HTTP_OK);
            }
            else
            {
                $response = array(
                    'status' => FALSE,
                    'message' => 'delete coupon failed',
                );
                $this->response($response, REST_Controller::HTTP_OK);
            }
        }
    }

    public function get_store_setting_details_get()
    {
        $headers = $this->input->request_headers();
        $verify_data = verify_request();
        if(isset($verify_data['status']) && $verify_data['status'] == 401) {
            $response = array(
                'status' => 401,
                'message' => 'Unauthorized Access!',
            );

            $this->response($response, 401); 
        }
        else
        {
            $id = $verify_data['userdata']['id'];

            $setting = $this->db->query("SELECT * FROM vendor_setting WHERE user_id=". $id ." ")->row_array();

            $response = array(
                'status' => TRUE,
                'message' => 'store setting get successfully',
                'data' => $setting
            );
            $this->response($response, REST_Controller::HTTP_OK);
        }
    }

    public function manage_store_setting_details_post()
    {
        $headers = $this->input->request_headers();
        $verify_data = verify_request();
        if(isset($verify_data['status']) && $verify_data['status'] == 401) {
            $response = array(
                'status' => 401,
                'message' => 'Unauthorized Access!',
            );

            $this->response($response, 401); 
        }
        else
        {
            $id = $verify_data['userdata']['id'];

            $data = $this->input->post(null,true);

            $update_data = [
                'vendor_status'                       => $data['vendor_status'],
                'affiliate_click_count'               => $data['affiliate_click_count'],
                'affiliate_click_amount'              => $data['affiliate_click_amount'],
                'affiliate_sale_commission_type'      => $data['affiliate_sale_commission_type'],
                'affiliate_commission_value'          => $data['affiliate_commission_value'],
                'form_affiliate_click_count'          => $data['form_affiliate_click_count'],
                'form_affiliate_click_amount'         => $data['form_affiliate_click_amount'],
                'form_affiliate_sale_commission_type' => $data['form_affiliate_sale_commission_type'],
                'form_affiliate_commission_value'     => $data['form_affiliate_commission_value'],
                'user_id'                             => $id,
            ];

            $get_data = $this->db->query("SELECT * FROM vendor_setting WHERE user_id=". $id ." ")->row();
            if($get_data){
                $manage = $this->db->update("vendor_setting", $update_data, ['user_id'=> $id ]);
            } else{
                $manage = $this->db->insert("vendor_setting", $update_data);
            }

            if($manage)
            {
                $response = array(
                    'status' => TRUE,
                    'message' => 'store setting change successfully'
                );
            }
            else
            {
                $response = array(
                    'status' => TRUE,
                    'message' => 'store setting change failed'
                );
            }
            
            $this->response($response, REST_Controller::HTTP_OK);
        }
    }

    public function get_store_coupon_list_get()
    {
        $headers = $this->input->request_headers();
        $verify_data = verify_request();
        if(isset($verify_data['status']) && $verify_data['status'] == 401) {
            $response = array(
                'status' => 401,
                'message' => 'Unauthorized Access!',
            );

            $this->response($response, 401); 
        }
        else
        {
            $id = $verify_data['userdata']['id'];

            $coupons_data = $this->Coupon_model->getCoupons($id);
            $ptotal = $this->db->query('SELECT product_id FROM product')->num_rows();
             
            foreach ($coupons_data as $key => $value) {
                if(strtolower($value['allow_for']) == 's'){
                    $coupons_data[$key]['product_count'] = count(explode(',', $value['products']));
                }else{
                    $coupons_data[$key]['product_count'] = $ptotal;
                }
                $coupons_data[$key]['count_coupon'] = $this->Coupon_model->getCouponCount($value['coupon_id']);
            }

            $response = array(
                'status' => TRUE,
                'message' => 'get store coupon list successfully',
                'data' => $coupons_data
            );
            $this->response($response, REST_Controller::HTTP_OK);
        }
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

    public function insertnotification($postData = null){
        if(!empty($postData)){
            $data['custom'] = $this->Product_model->create_data('notification', $postData);
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
}
?>