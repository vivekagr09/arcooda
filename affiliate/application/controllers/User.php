<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

require(APPPATH.'/libraries/REST_Controller.php');
use Restserver\Libraries\REST_Controller;
require APPPATH . 'libraries/Format.php';
require APPPATH . 'libraries/Affiliate-Script-SDK/AffiliateScript.php';

class User extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('user_model', 'user');
        $this->load->library('email');
        $this->load->library('form_validation');
        $this->load->model('Common_model');
        $this->load->model('Product_model');
        $this->load->model('Total_model');
        $this->load->library('user_agent');
        $this->load->model("Form_model");
        $this->load->model('IntegrationModel');
        $this->load->model('PagebuilderModel');
        $this->load->helper('reusable');
    }

    //common function used same in my_controller 

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

        $MembershipSetting = $this->Product_model->getSettings('membership');

        if($MembershipSetting['status']){
            $noMembershipVendors = $this->getNoMembershipUsers($restricted_vendors); 
            $restricted_vendors = array_unique(array_merge($noMembershipVendors, $restricted_vendors));
        }

        return $restricted_vendors;
    }

    public function getNoMembershipUsers($alreadyRestricted) {
        $result = [];
        if(empty($alreadyRestricted)) {
            $users = $this->db->query('select id from users where type="user"')->result();
        } else {
            $users = $this->db->query('select id from users where type="user" and id NOT IN ('.implode(',', $alreadyRestricted).')')->result();
        }

        foreach($users as $v) {
            $user = App\User::find($v->id);

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

    //common function

    public function get_registration_form_get()
    {
        $json = array(
            'status' => FALSE,
            'message' => 'registration form not prepared',
            'errors' => array()
        );

        $register_form = $this->PagebuilderModel->getSettings('registration_builder');

        if($register_form && count($json['errors']) == 0) {
            $customField = json_decode($register_form['registration_builder'],1);
            $json = array(
                'status' => TRUE,
                'data' => $customField,
            );
        }

        $this->response($json, REST_Controller::HTTP_OK);
    }

    public function registarion_post() 
    {
        $json = array(
            'status' => FALSE,
            'message' => 'user registration failed',
            'errors' => array()
        );

        $post = $this->input->post(null,true);
        
        if(!$post){
            $_POST = json_decode(file_get_contents('php://input'), TRUE);
            $post = $_POST;
        }

        $post['user_type'] = 'user';

        $post['affiliate_id'] = $refid = reh_fetchRefferer();

        if(isset($post['refid']) && !empty($post['refid'])){
            $sql = "SELECT id,email FROM users WHERE id = ?";
            $checkRefUsername = $this->db->query($sql,(int) $post['refid'])->row_array();
            if($checkRefUsername)
                $refid = ($refid == 0) ? $post['refid'] : $refid;
        }
        
        $this->load->library('form_validation');
        
        $form_validation_rules = vfor_user_registration_api();

        $this->form_validation->set_rules($form_validation_rules);

        $json['errors'] = array();

        if ($this->form_validation->run() == FALSE) {
            $json['errors'] = $this->form_validation->error_array();
        }

        $register_form = $this->PagebuilderModel->getSettings('registration_builder');
        
        if($register_form && count($json['errors']) == 0) {
            
            $customField = json_decode($register_form['registration_builder'],1);
            
            $filesAttached = [];

            foreach ($customField as $_key => $_value) {

                if(isset($_value['hide_on_registration']) && $_value['hide_on_registration']) continue;

                $field_name = 'custom_'. $_value['name'];

                $config = reh_user_registration_file_upload_config();

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

                                $this->CI->load->library('upload', $config);

                                $this->CI->upload->initialize($config);

                                if (!$this->CI->upload->do_upload('attahced_multi_azkja')) {
                                    $error = $this->CI->upload->display_errors();
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

                            $filesAttached[$field_name] = $attahced_multi_azkja;
                        } else {
                            if(isset($_value['required']) && $_value['required'] && (!isset($filesAttached[$field_name]) || empty($filesAttached[$field_name]))) {
                                $json['errors'][$field_name] = "Please select file for upload!";
                                break;
                            }
                        }
                    } else {
                        if(isset($_FILES[$field_name]['name']) && !empty($_FILES[$field_name]['name'])) {

                            $config['file_name']  = random_string('alnum', 32);

                            $this->CI->load->library('upload', $config);

                            $this->CI->upload->initialize($config);

                            if (!$this->CI->upload->do_upload($field_name)) {
                                $error = $this->CI->upload->display_errors();
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

                            $filesAttached[$field_name] = $attahced_multi_azkja;
                        } else {
                            if(isset($_value['required']) && $_value['required'] && (!isset($filesAttached[$field_name]) || empty($filesAttached[$field_name]))) {
                                $json['errors'][$field_name] = "Please select file for upload!";
                                break;
                            }
                        }
                    }

                    
                } else {
                    $json = vfor_user_registration_custom_fields($json, $post, $_value, $field_name);
                }

            }
        }

        $googlerecaptcha = $this->PagebuilderModel->getSettings('googlerecaptcha');
        
        if (isset($googlerecaptcha['affiliate_register']) && $googlerecaptcha['affiliate_register']) {
            if($post['g-recaptcha-response'] == ''){
                $json['errors']['captch_response'] = 'Invalid Recaptcha';
            }
        }

        if( count($json['errors']) == 0 ){
            if (isset($googlerecaptcha['affiliate_register']) && $googlerecaptcha['affiliate_register']) {
                $post = http_build_query(
                    array (
                        'response' => $this->input->post('g-recaptcha-response',true),
                        'secret' => $googlerecaptcha['secretkey'],
                        'remoteip' => $_SERVER['REMOTE_ADDR']
                    )
                );
                $opts = array('http' => 
                    array (
                        'method' => 'POST',
                        'header' => 'application/x-www-form-urlencoded',
                        'content' => $post
                    )
                );
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

        if( count($json['errors']) == 0){
                $checkEmail = $this->db->query("SELECT id FROM users WHERE email like ". $this->db->escape($this->input->post('email',true)) ." ")->num_rows();
                if($checkEmail > 0){ $json['errors']['email'] = "Email Already Exist"; }
                $checkUsername = $this->db->query("SELECT id FROM users WHERE username like ". $this->db->escape($this->input->post('username',true)) ." ")->num_rows();
                if($checkUsername > 0){ $json['errors']['username'] = "Username Already Exist"; }

                /*if(!isset($post['terms'])){
                    $json['warning'] = __('user.accept_our_affiliate_policy');
                }*/

                if(count($json['errors']) == 0){    
                    $user_type = 'user';
                    $geo = $this->ip_info();
                    


                    $commition_setting = $this->Product_model->getSettings('referlevel');

                    // echo json_encode($commition_setting);exit;

                    $disabled_for = json_decode( (isset($commition_setting['disabled_for']) ? $commition_setting['disabled_for'] : '[]'),1); 
                    
                    if((int)$commition_setting['status'] == 0){ $refid  = 0; }
                    else if((int)$commition_setting['status'] == 2 && in_array($refid, $disabled_for)){ $refid = 0; }

                    $custom_fields = array();
                    foreach ($this->input->post(null,true) as $key => $value) {
                        if(!in_array($key, array('affiliate_id','terms','cpassword','firstname','lastname','email','username','password'))){
                            $custom_fields[$key] = $value;
                        }
                    }

                    $membership = $this->Product_model->getSettings('membership');

                    $allMembershipPlans = App\MembershipPlan::all();

                    $is_vendor = $this->input->post('is_vendor',true);

                    if(empty($is_vendor)) {
                        $is_vendor = 0;
                    }

                    $plan_id = -1;
                    
                    if($is_vendor == 1 && ($membership['status'] == 0 || $membership['status'] == 3)) {
                        $plan_id = -1;
                    } else if($membership['status']){
                        $plan_id = 0;
                    }

                    $is_approval_needed = $this->Product_model->getSettings('store', 'registration_approval');

                    $registration_approval = (isset($is_approval_needed['registration_approval']) && $is_approval_needed['registration_approval'] == 0) ? 1 : 0;

                    $defaultUserGroup = $this->user->getDefaultGroup();

                    $userGroups = (!empty($defaultUserGroup)) ? $defaultUserGroup->id : null;

                    $data = $this->user->insert(array(
                        'firstname'                 => $this->input->post('firstname',true),
                        'lastname'                  => $this->input->post('lastname',true),
                        'email'                     => $this->input->post('email',true),
                        'username'                  => $this->input->post('username',true),
                        'password'                  => sha1($this->input->post('password',true)),
                        'device_type'               => $this->input->post('device_type',true),
                        'device_token'              => $this->input->post('device_token',true),
                        'refid'                     => $refid,
                        'plan_id'                   => (int)$plan_id,
                        'type'                      => $user_type,
                        'Country'                   => $geo['id'],
                        'City'                      => (string)$geo['city'],
                        'phone'                     => '',
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
                        'is_vendor'                 =>  $is_vendor,
                        'reg_approved'              => $registration_approval,
                        'value'                     => json_encode(array_merge($custom_fields, $filesAttached)),
                        'groups'                    => $userGroups
                    ));

                    $last_user_id = $this->db->insert_id();

                    if($membership['status'] && $registration_approval == 1){
                        if((int)$is_vendor == 1) {
                            $plan_id = $membership['default_vendor_plan_id'] ?? $membership['default_plan_id'];
                        } else {
                            $plan_id = $membership['default_affiliate_plan_id'] ?? $membership['default_plan_id'];
                        }
                        if($plan_id > 0){
                            $plan = App\MembershipPlan::find($plan_id);
                            if($plan){
                                $user = App\User::find($data);
                                $plan->buy($user,1, 'Default plan started','Default');

                                // process fixed and custom % comission
                                $processReferComission = true;
                            }
                        }
                    }

                    if(empty($allMembershipPlans) || !isset($membership['status']) || empty($membership['status']) || !$membership['status']) {
                        // process fixed and custom % comission
                        $processReferComission = true;      
                    }

                    $json['processReferComission'] = $processReferComission;

                    if(!empty($data) && $user_type == 'user'){
                        
                        if(isset($processReferComission) && (int)$refid > 0) {
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
                                                'status'       => (int)$registration_approval,
                                                'user_id'      => $levelUser,
                                                'amount'       => $_giveAmount,
                                                'dis_type'     => '',
                                                'comment'      => "Level {$l} : ".'Commission for new affiliate registrion Id ='. $last_user_id .' | Name : '. $this->input->post('firstname',true) ." " .$this->input->post('lastname',true),
                                                'type'         => 'refer_registration_commission',
                                                'reference_id' => $last_user_id,
                                                'group_id' => $comission_group_id,
                                            ));
                                        }
                                    }
                                }
                            }
                        }

                        $notificationData = array(
                            'notification_url'          => '/userslist/'.$data,
                            'notification_type'         =>  'user',
                            'notification_title'        =>  __('user.new_user_registration'),
                            'notification_viewfor'      =>  'admin',
                            'notification_actionID'     =>  $data,
                            'notification_description'  =>  $this->input->post('firstname',true).' '.$this->input->post('lastname',true).' register as a  on affiliate Program on '.date('Y-m-d H:i:s'),
                            'notification_is_read'      =>  '0',
                            'notification_created_date' =>  date('Y-m-d H:i:s'),
                            'notification_ipaddress'    =>  $_SERVER['REMOTE_ADDR']
                        );
                        
                        $this->insertnotification($notificationData);
                        
                        if ($post['affiliate_id'] > 0) {
                            $notificationData = array(
                                'notification_url'          => '/managereferenceusers',
                                'notification_type'         =>  'user',
                                'notification_title'        =>  __('user.new_user_registration_under_your'),
                                'notification_viewfor'      =>  'user',
                                'notification_view_user_id' =>  $post['affiliate_id'],
                                'notification_actionID'     =>  $data,
                                'notification_description'  =>  $this->input->post('firstname',true).' '.$this->input->post('lastname',true).' has been register under you on '.date('Y-m-d H:i:s'),
                                'notification_is_read'      =>  '0',
                                'notification_created_date' =>  date('Y-m-d H:i:s'),
                                'notification_ipaddress'    =>  $_SERVER['REMOTE_ADDR']
                            );
                            $this->insertnotification($notificationData);
                        }

                        $json['success']  =  "You've Successfully registered";
                        $user_details_array=$this->user->login($this->input->post('username',true));

                        $this->user->update_user_login($user_details_array['id']);
                        
                        $this->load->model('Mail_model');

                        // 1 == enable (Approval For Registration)

                        $where = array('id'=>$last_user_id);
                        $new_post = $this->Common_model->select_where_result('users', $where);
                        $new_post['user_type'] = $new_post['type'];
                        if($registration_approval == 0) {
                            $this->Mail_model->send_registration_request_mail($post);
                        } else {
                            $this->Mail_model->send_register_mail($post,__('user.welcome_to_new_user_registration'));
                        }

                        $json = array(
                            'status' => TRUE,
                            'message' => 'user registration successfully'
                        );
                    }
                }
        }

        $this->response($json, REST_Controller::HTTP_OK);
    }


    public function insertnotification($postData = null){
        if(!empty($postData)){
            $data['custom'] = $this->Product_model->create_data('notification', $postData);
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
 
    public function login_post()
    {

        $this->form_validation->set_rules('username', 'username', 'required|trim',
            array('required'      => 'Oops ! user name is required.'
        ));

        $this->form_validation->set_rules('password', 'password', 'required|trim',
            array('required'      => 'Oops ! password is required.'
        ));

        $this->form_validation->set_rules('device_type', 'device_type', 'required|trim',
            array('required'      => 'Oops ! device type is required.'
        ));

        $this->form_validation->set_rules('device_token', 'device_token', 'required|trim',
            array('required'      => 'Oops ! device token is required.'
        ));

        $this->form_validation->set_error_delimiters('', '');
        if($this->form_validation->run()== false)
        {
            if(!empty(form_error('username')))$errors['username'] =form_error('username');
            if(!empty(form_error('password')))$errors['password'] =form_error('password');
            if(!empty(form_error('device_type')))$errors['device_type'] =form_error('device_type');
            if(!empty(form_error('device_token')))$errors['device_token'] =form_error('device_token');

            $response['message'] = "Please required field";
            $response['errors'] = $errors;

            $this->response($response, 422);
        }
        else
        {
            $created_date = date('Y-m-d H:i:s');

            extract($_POST);
            $uniquestring = $created_date.$username;

            $where = array(
                'username'=>$username,
                'password'=>sha1($password)
            );
            $count = $this->Common_model->get_total_rows('users', $where);

            if($count == 0)
            {
                $response = array(
                    'status' => FALSE,
                    'message' => 'username and password something went wrong'
                );
                $this->response($response, REST_Controller::HTTP_OK);
            }
            else
            {
                $get_user_data = $this->Common_model->get_data_row('users', $where, $field = '*', 'id');
                if($get_user_data['status'] == 0)
                {
                    $response = array(
                        'status' => FALSE,
                        'message' => 'user blocked by admin'
                    );
                    $this->response($response, REST_Controller::HTTP_OK);
                }

                if($get_user_data['reg_approved'] == 0)
                {
                    $response = array(
                        'status' => FALSE,
                        'message' => 'you are not approved by admin'
                    );
                    $this->response($response, REST_Controller::HTTP_OK);
                }

                $token = AUTHORIZATION::generateToken($uniquestring);
                $data = array('token'=>$token,'device_type'=>$device_type,'device_token'=>$device_token);
                $update_token = $this->Common_model->update('users', $where, $data);
                if($update_token)
                {
                    $response = array(
                        'status' => TRUE,
                        'message' => 'user login successfully',
                        'data' => [
                            'token' => $token,
                            'user_status' => AffiliateScript::usersProfileStatus(['userdetails'=>$get_user_data])['status'],
                            'firstname' => $get_user_data['firstname'],
                            'lastname' => $get_user_data['lastname'],
                            'email' => $get_user_data['email'],
                            'is_vendor' => $get_user_data['is_vendor'],
                            'profile_avatar' =>  (!empty($get_user_data['avatar'])) ? base_url('assets/images/users/'.$get_user_data['avatar']) : base_url('assets/vertical/assets/images/no-image.jpg')
                        ]
                    );
                    $this->response($response, REST_Controller::HTTP_OK);
                }
                else
                {
                    $response = array(
                        'status' => FALSE,
                        'message' => 'user login failed'
                    );
                    $this->response($response, REST_Controller::HTTP_OK);
                }
            }
        }
    }

    public function dashboard_get()
    {
        $invalidRequest = FALSE;

        $availableKeys = ['plan_details', 'totals_count', 'top_affiliate', 'chart_data', 'notifications', 'market_tools'];

        $requestKeys = isset($this->_get_args['includes']) ? $this->_get_args['includes'] : null;

        if(!empty($requestKeys)) {
            $requestKeys = explode(',',$requestKeys);
        }

        if(!empty($requestKeys)) {
            foreach ($requestKeys as $key) {
                if(empty($key))
                {
                    unset($requestKeys[$key]);
                } else if(! in_array($key, $availableKeys)){
                    $invalidRequest = TRUE;
                    
                    $response = array(
                        'status' => 401,
                        'message' => 'Invalid includes arguments provided!',
                    );

                    $this->response($response, 401);
                }
            }
        }

        $headers = $this->input->request_headers();
        $verify_data = verify_request();
        
        if(isset($verify_data['status']) && $verify_data['status'] == 401 && !$invalidRequest) {
            $response = array(
                'status' => 401,
                'message' => 'Unauthorized Access!',
            );

            $this->response($response, 401);
        }
        else
        {
            $id = $verify_data['userdata']['id'];
            $plan_id = $verify_data['userdata']['plan_id'];
            $type = $verify_data['userdata']['type'];
            $store_slug_url=$verify_data['userdata']['store_slug'];
            $userdashboard_settings = $this->Common_model->getUserDashboardSettings();
            $store = $this->Product_model->getSettings('store');
            $referlevelSettings = $this->Product_model->getSettings('referlevel');
            $refer_status = reh_fetchReffererStatus($referlevelSettings, $id);

            $store_slug = $this->db->query("SELECT slug FROM slugs WHERE user_id = '".(int)$id."' AND type = 'store'")->row()->slug;
            $invitationlinkid=0;
            if(isset($userdashboard_settings) && isset($userdashboard_settings['invitation_link_id']))
            {
              $invitationlinkidarray=$userdashboard_settings['invitation_link_id'];
              $invitationlinkid=$invitationlinkidarray['setting_value'];
            }
            
            if(($store['status']) || $refer_status){
                if($store_slug){
                    $share_url = base_url($store_slug);
                } else {
                    $share_url = base_url('store/' . base64_encode($id));
                }

                if($invitationlinkid == 1){
                    
                    $share_url=$share_url.'/?id='.(int)$id;
                }
                if($invitationlinkid == 0){
                    $share_url=$share_url;
                }
                $data['affiliate_store_url'] = $share_url;

                if(!empty($store_slug_url)){
                    $store_page_url = base_url('store/' .$store_slug_url.'/'.base64_encode($id));
                    if($invitationlinkid == 1){
                    
                    $store_page_url=$store_page_url.'/?id='.(int)$id;
                    }
                    if($invitationlinkid == 0){
                        $store_page_url=$store_page_url;
                    }
                    $data['store_page_url'] = $store_page_url;
                }
            }

            $market_vendor = $this->Product_model->getSettings('market_vendor');
            $marketvendorpanelmode = $market_vendor['marketvendorpanelmode'] ?? 0;

            $referlevelSettings = $this->Product_model->getSettings('referlevel');
           
            $data['refer_status'] = reh_fetchReffererStatus($referlevelSettings, $id);

            $register_slug = $this->db->query("SELECT slug FROM slugs WHERE user_id = '".(int)$id."' AND type = 'register'")->row()->slug;

            if($data['refer_status'] && allowMarketVendorPanelSections($marketvendorpanelmode, $verify_data['userdata']['is_vendor'])){ 
                if($register_slug){
                    $unique_reseller_link = base_url($register_slug);
                } else {
                    $unique_reseller_link = base_url('register/' . base64_encode($id));
                }
                if($invitationlinkid == 1){
                    
                $unique_reseller_link=$unique_reseller_link.'/?id='.(int)$id;
                }
                if($invitationlinkid == 0){
                    $unique_reseller_link=$unique_reseller_link;
                }
                
                $data['unique_reseller_link'] = $unique_reseller_link;
            }
 

            if(in_array('top_affiliate', $requestKeys)) {
                $data['top_affiliate'] = $this->Product_model->getPopulerUsers(["limit" => 10]);
            }

            if(in_array('plan_details', $requestKeys)) {
                $data['isMembershipAccess'] = $this->Product_model->isMembershipAccess();

                if($plan_id == -1){
                    $data['is_lifetime_plan'] = 1;
                }
                else if($plan_id != -1) 
                {
                    $where_plan = array('user_id'=>$id);
                    $user_plan = $this->Common_model->get_data_row('membership_user', $where_plan, 'total_day,expire_at,started_at,status_id,is_active,is_lifetime', 'id');
                    $data['user_plan'] = $user_plan;
                }
            }

            if(in_array('totals_count', $requestKeys)) {
                $data['user_totals'] = $this->Total_model->getUserTotals((int)$id);
                $data['refer_total'] = $this->Product_model->getReferalTotals($id);
                $data['user_totals']['click_action_commission'] = c_format($data['user_totals']['click_action_commission']);
                $data['user_totals']['click_external_commission'] = c_format($data['user_totals']['click_external_commission']);
                $data['user_totals']['user_balance'] = c_format($data['user_totals']['user_balance']);
                $data['user_totals_week'] = c_format($this->Total_model->getUserBalance((int)$id, ['week' => 1]));
                $data['user_totals_month'] = c_format($this->Total_model->getUserBalance((int)$id, ['month' => 1]));
                $data['user_totals_year'] = c_format($this->Total_model->getUserBalance((int)$id, ['year' => 1]));
            }

            if(in_array('notifications', $requestKeys)) {
                $data['notifications'] = $this->user->getAllNotification($userdetails['id']);
            }

            if (in_array('chart_data', $requestKeys)) {
                $data['chart'] = $this->Total_model->chartUser((int)$id, [
                    'year' => date('Y'),
                    'group' => 'month'
                ]);
                echo json_encode($data);die;
            }

            if (in_array('market_tools', $requestKeys)) {
                $data['market_tools'] = $this->generate_market_tools($id, $verify_data['userdata'], [], 15);
            }

            $response = array(
                'status' => TRUE,
                'message' => 'dahsboard data get successfully',
                'data' => $data
            );

            $this->response($response, REST_Controller::HTTP_OK);
        }
    }

    public function my_affiliate_links_post()
    {

        $headers = $this->input->request_headers();
        $verify_data = verify_request();
        if(isset($verify_data['status']) && $verify_data['status'] == 401) {
            $response = array(
                'status' => 401,
                'message' => 'Unauthorized Access!',
            );

            $this->response($response, 401); 
        } else {
            $id = $verify_data['userdata']['id'];

            $type = $verify_data['userdata']['type'];

            $requestFilter = array(
                'req_category_id' => $this->input->post('category_id'),
                'req_market_category_id' => $this->input->post('market_category_id'),
                'req_ads_name' => $this->input->post('ads_name'),
                'req_check_vendor' => $this->input->post('check_vendor')
            );

            $json = $this->generate_market_tools($id, $verify_data['userdata'], $requestFilter);
            
            $response = array(
                'status' => TRUE,
                'message' => 'my affiliate links get successfully',
                'data' => $json
            );

            $this->response($response, REST_Controller::HTTP_OK);
        }
    }


    private function generate_market_tools($id, $userdetails, $req_fileter = array(), $limited = false) {

        if($limited !== false) {
            $remain = $limited;
        }

        $market_tools = [];
        
        $market_tools['userdetails'] = $userdetails;

        $market_tools['form_setting'] = $market_tools['form_default_commission'] = $this->Product_model->getSettings('formsetting');

        $market_tools['pro_setting'] = $market_tools['default_commition'] = $this->Product_model->getSettings('productsetting');
        $market_vendor = $this->Product_model->getSettings('market_vendor');

        $restricted_vendors = $this->get_restricted_vendors();

        $filter = [
            'user_id'          => $id,
            'restrict'         => $id,
            'redirectLocation' => 1,
            'status'           => 1,
            'restrict_vendors' => $restricted_vendors,
            'not_show_my'      => $id,
            'userdetails' => $userdetails
        ];
     

        if($req_fileter['check_vendor'] == 'true'){
            unset($filter['not_show_my']);
        }

        if($limited !== false) {
            $filter['limit'] = ceil($remain / 3);
            $filter['start'] = 0;
        }
         //new code... 
             if($userdetails['is_vendor']==1)  // for vend
             {  
                 if ($market_vendor['marketvendorstatus'] == 0) {
                    $filter['marketvendorstatus'] = 1;
                    if (isset($post['vendor_id'])) {
                        $filter['vendor_id'] = $post['vendor_id'];
                    }
                 }else{
                    $filter['marketvendorstatus'] = 0;
                 }
            }
            else
            {
                // for aff
                if ($market_vendor['marketvendorstatus'] == 0) {
                    $filter['marketvendorstatus'] = 0;
                    if (isset($post['vendor_id'])) {
                        $filter['vendor_id'] = $post['vendor_id'];
                    }
                 }else{
                    $filter['marketvendorstatus'] = 1;
                 }
            }


        $filter["is_vendor"]=$userdetails['is_vendor'];
        $filtertools=$filter;
        if (isset($post['vendor_id'])) 
        {
            $filtertools['vendor_id'] = $post['vendor_id'];
        }
        
        $tools = $this->IntegrationModel->getProgramTools($filtertools);
     
        $filter = [];
        
        if($limited !== false) {                
            $remain = $remain - count($tools);
            $filter = array(
                'limit' => ceil($remain / 2),
                'start' => 0,
            );
        }

        if (isset($req_fileter['category_id']) && !empty($req_fileter['category_id'])) {
            $filter['category_id'] = $req_fileter['category_id'];
        }

        if (isset($req_fileter['ads_name']) && !empty($req_fileter['ads_name'])) {
            $filter['ads_name'] = $req_fileter['ads_name'];
        }
               
        $forms = $this->Form_model->getForms($id, $filter);


        $filter = array(
            'product_status' => 1,
            'not_show_my' => $id
        );
        
        if($req_fileter['check_vendor'] == 'true'){
            unset($filter['not_show_my']);
        }

        if($limited !== false) {                
            $remain = $remain - count($forms);
            $filter['limit'] = $remain;
            $filter['start'] = 0;
        }

        if (isset($req_fileter['market_category_id']) && !empty($req_fileter['market_category_id'])) {
            $filter['category_id'] = $req_fileter['market_category_id'];
        }
                
        $store_setting = $this->Product_model->getSettings('store');

        if(is_array($store_setting) && $store_setting['status'])
        { 
            if($store_setting['store_mode']=="sales")
             $filter['is_campaign_product'] = 1;

            $products = $this->Product_model->getAllProduct($id, $type, $filter);
            foreach ($products as $key => $value) {
                $slug_query = $this->db->query("SELECT slug FROM slugs WHERE type = 'product' AND related_id = '".(int)$value['product_id']."' AND user_id = '".(int)$id."'")->row();
                $products[$key]['slug'] = $slug_query ? $slug_query->slug : '';
                $products[$key]['is_product'] = 1;
            }
        } else {
            $products = [];
        }

        foreach ($forms as $key => $value) {
            $slug_query = $this->db->query("SELECT slug FROM slugs WHERE type = 'form' AND related_id = '".(int)$value['form_id']."' AND user_id = '".(int)$id."'")->row();
            $forms[$key]['slug'] = $slug_query ? $slug_query->slug : '';
            $forms[$key]['coupon_name']  = $this->Form_model->getFormCouponname(($value['coupon']) ? $value['coupon'] : 0);
            $forms[$key]['public_page']  = base_url('form/'.$value['seo'].'/'.base64_encode($id));
            $forms[$key]['count_coupon'] = $this->Form_model->getFormCouponCount($value['form_id'],$id);
            if($value['coupon']){
                $forms[$key]['coupon_code'] = $this->Form_model->getFormCouponCode($value['coupon']);
            }
            $forms[$key]['seo'] = str_replace('_', ' ', $value['seo']);
            $forms[$key]['is_form'] = 1;
            $forms[$key]['product_created_date'] = $value['created_at'];
        }
        
        if(is_array($store_setting) && $store_setting['status'] && $store_setting['store_mode']=="sales"){

            $data_list = array_merge($products,$tools);   
            } 
         else{

            $data_list = array_merge($products,$forms,$tools);
        }

        usort($data_list,function($a, $b){
            $ad = strtotime($a['product_created_date']);
            $bd = strtotime($b['product_created_date']);
            return ($ad-$bd);
        });

        $market_tools['data_list'] = array_reverse($data_list);


        $market_tools['Product_model'] = $this->Product_model;

        return AffiliateScript::usersMarketTools($market_tools);
    }

    public function change_password_post()
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
            $this->form_validation->set_rules('old_pass', 'old_pass', 'required|trim',
            array('required'      => 'Oops ! old pass is required.'
            ));

            $this->form_validation->set_rules('password', 'password', 'required|trim',
            array('required'      => 'Oops ! password is required.'
            ));

            $this->form_validation->set_rules('conf_password', 'conf_password', 'required|trim|matches[password]',
            array('required'      => 'Oops ! conf password is required.'
            ));

            $this->form_validation->set_error_delimiters('', '');
            if($this->form_validation->run()== false)
            {
                if(!empty(form_error('old_pass')))$errors['old_pass'] =form_error('old_pass');
                if(!empty(form_error('password')))$errors['password'] =form_error('password');
                if(!empty(form_error('conf_password')))$errors['conf_password'] =form_error('conf_password');

                $response['message'] = "Please required field";
                $response['errors'] = $errors;

                $this->response($response, 422);
            }
            else
            {
                extract($_POST);

                $id = $verify_data['userdata']['id'];

                $admin = $this->db->from('users')->where('id',$id)->get()->row_array();
                if($admin['password'] == sha1($old_pass))
                {
                    $res = array('password'=>sha1($password));
                    $this->db->where('id',$id);
                    $update = $this->db->update('users',$res);

                    if($update)
                    {
                        $response = array(
                            'status' => TRUE,
                            'message' => 'password change successfully',
                        );
                    }
                    else
                    {
                        $response = array(
                            'status' => FALSE,
                            'message' => 'password change failed',
                        );
                    }
                }
                else
                {
                    $response = array(
                        'status' => FALSE,
                        'message' => 'enter password not valid',
                    );
                }
                
                $this->response($response, REST_Controller::HTTP_OK);
            }
        }
    }

    public function get_my_profile_details_get()
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
            $user_details = (array)$this->user->get($id);
            
            $response = array(
                'status' => TRUE,
                'message' => 'user details get successfully',
                'data' => [
                    'user_status' => AffiliateScript::usersProfileStatus(['userdetails'=>$user_details])['status'],
                    'firstname' => $user_details['firstname'],
                    'lastname' => $user_details['lastname'],
                    'email' => $user_details['email'],
                    'is_vendor' => $user_details['is_vendor'],
                    'profile_image' =>  (!empty($user_details['avatar'])) ? base_url('assets/images/users/'.$user_details['avatar']) : base_url('assets/vertical/assets/images/no-image.jpg')
                ]
            );

            $this->response($response, REST_Controller::HTTP_OK);
        }
    }

    public function update_my_profile_post()
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

            $password = $this->input->post('password');

            $this->form_validation->set_rules('firstname', 'firstname', 'required|trim',
            array('required'      => 'Oops ! firstname is required.'
            ));

            $this->form_validation->set_rules('lastname', 'lastname', 'required|trim',
            array('required'      => 'Oops ! lastname is required.'
            ));

            $this->form_validation->set_rules('username', 'username', 'required|trim',
            array('required'      => 'Oops ! username is required.'
            ));

            $this->form_validation->set_rules('email', 'email', 'required|valid_email|xss_clean',
            array('required'      => 'Oops ! email is required.'
            ));

            $this->form_validation->set_rules('country_id', 'country_id', 'required|trim',
            array('required'      => 'Oops ! country id is required.'
            ));

            if($password != ''){
                $this->form_validation->set_rules('password', 'Password', 'required|trim', array('required' => '%s is required'));
                $this->form_validation->set_rules('cpassword', 'Confirm Password', 'required|trim', array('required' => '%s is required'));
                $this->form_validation->set_rules('cpassword', 'Confirm Password', 'required|trim|matches[password]', array('required' => '%s is required'));
            }

            $this->form_validation->set_error_delimiters('', '');
            if($this->form_validation->run()== false)
            {
                if(!empty(form_error('firstname')))$errors['firstname'] =form_error('firstname');
                if(!empty(form_error('lastname')))$errors['lastname'] =form_error('lastname');
                if(!empty(form_error('username')))$errors['username'] =form_error('username');
                if(!empty(form_error('email')))$errors['email'] =form_error('email');
                if(!empty(form_error('country_id')))$errors['country_id'] =form_error('country_id');
                if($password != ''){
                    if(!empty(form_error('password')))$errors['password'] =form_error('password');
                    if(!empty(form_error('cpassword')))$errors['cpassword'] =form_error('cpassword');
                }

                $response['message'] = "Please required field";
                $response['errors'] = $errors;

                $this->response($response, 422);
            }
            else
            {
                
                $id = $verify_data['userdata']['id'];

                $email = $this->input->post('email');
                $username = $this->input->post('username');

                $checkmail = $this->Product_model->checkmail($email,$id);
                $checkuser = $this->Product_model->checkuser($username,$id);
            
                if(!empty($checkmail)){
                    $response = array(
                        'status' => FALSE,
                        'message' => 'email already exist'
                    );
                    $this->response($response, REST_Controller::HTTP_OK);
                }

                if(!empty($checkuser)){ 
                    $response = array(
                        'status' => FALSE,
                        'message' => 'username already exist'
                    );
                    $this->response($response, REST_Controller::HTTP_OK);
                }

                $userArray = array(
                        'firstname'                 => $this->input->post('firstname'),
                        'lastname'                  => $this->input->post('lastname'),
                        'email'                     => $this->input->post('email'),
                        'username'                  => $this->input->post('username'),
                        //'password'                  => sha1($this->input->post('password',true)),
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
                        'ucountry'                    => $this->input->post('country_id'),
                        'Country'                    => $this->input->post('country_id')
                );

                if($password != ''){
                        $userArray['password'] = sha1( $password );
                    }

                if(!empty($_FILES['avatar']['name'])){
                    $upload_response = $this->upload_photo('avatar','assets/images/users');

                    if($upload_response['success']){
                        $userArray['avatar'] = $upload_response['upload_data']['file_name'];
                    }
                }

                $this->user->update_user($id, $userArray);
                $userArray = $this->db->query("SELECT * FROM users WHERE id = ". (int)$id)->row_array();

                $response = array(
                    'status' => TRUE,
                    'message' => 'user details update successfully',
                    'data' => $userArray
                );
                
                $this->response($response, REST_Controller::HTTP_OK); 
            }
            
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
            $response = array('success' => FALSE, 'message' => $this->upload->display_errors());
            $this->response($response, REST_Controller::HTTP_OK);
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
}
?>