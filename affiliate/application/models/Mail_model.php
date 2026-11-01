<?php   
class Mail_model extends MY_Model{
    public $mobile_number = '';


    public function send_ticket_mail($ticketID, $templateUniqueCode) {

        $ticket = $this->db->get_where('tickets', ['ticket_id'=>$ticketID])->row_array();
        
        $reply = $this->db->order_by('created_at', 'DESC')->get_where('tickets_reply', ['ticket_id'=>$ticket['ticket_id']])->row_array();
        
        $subject = $this->db->get_where('tickets_subject', ['id'=>$ticket['subject_id']])->row_array();

        $user = App\User::find($ticket['user_id'])->toArray();

        $status = [
            '1' => 'Open',
            '2' => 'Pending',
            '3' => 'Closed',
        ];

        $data = [
            'ticket_id' => $ticket['ticket_id'],
            'ticket_status' => $status[$ticket['status']],
            'ticket_subject' => $subject['subject'],
            'ticket_body' => $reply['message'],
            'ticket_datetime' => $ticket['created_at'],
            'firstname' => $user['firstname'],
            'lastname' => $user['lastname'],
            'email' => $user['email'],
            'username' => $user['username'],
            'ticket_reply_message' => $reply['message'],
            'reply_datetime' => $reply['created_at']
        ];

        $data['website_name'] = $setting['name'];
        $data['website_logo'] = ($setting['logo']) ? "<img src='".base_url("assets/images/site/".$setting['logo'])."' >" : "";

        $template = $this->getTemplateByID(0,$templateUniqueCode);

        $sendToUser = false;
        $sendToAdmin = false;

        if($templateUniqueCode == 'ticket_reply_email') {
            if($reply['user_id'] == 1) {
                $sendToUser = true;
            } else {
                $sendToAdmin = true;
            }
        } else {
            $sendToUser = true;
            $sendToAdmin = true;
        }
        
        if($sendToUser) {
            $template['subject'] = $this->parseTemplate($template['subject'],$template['shortcode'],$data);
            $template['text'] = $this->parseTemplate($template['text'],$template['shortcode'],$data);
            $this->sendMail($user['email'], $template['subject'], $template['text']);
        }

        if($sendToAdmin) {
            $template['subject'] = $this->parseTemplate($template['admin_subject'],$template['shortcode'],$data);
            $template['admin_text'] = $this->parseTemplate($template['admin_text'],$template['shortcode'],$data);
            $this->sendMail('admin', $template['subject'], $template['admin_text']);
        }
    }


    public function send_vendor_deposit_mail($deposit, $action='added'){
        if($action == 'added') {
            $template = $this->getTemplateByID(0,'new_vendor_deposit_request');
        } else {
            $template = $this->getTemplateByID(0,'vendor_deposit_request_updated');
        }


        $this->load->model('Deposit_payment_model');
        $statusList = $this->Deposit_payment_model->status_list;

        $this->load->model('Product_model');
        $setting = $this->Product_model->getSettings('site');

        $data['website_name'] = $setting['name'];

        $data['website_logo'] = ($setting['logo']) ? "<img src='".base_url("assets/images/site/".$setting['logo'])."' >" : "";  
            
        $user = App\User::find($deposit->vd_user_id);
        
        $data['firstname'] = $user->firstname;
        $data['lastname'] = $user->lastname;
        $data['email'] = $user->email;
        $data['username'] = $user->username;
        $data['amount'] = c_format($deposit->vd_amount);
        $data['status'] = $statusList[$deposit->vd_status];

        $data['mail_templates'] = $this->parseTemplate($template['text'],$template['shortcode'],$data);
        // $body = $this->load->view('mails/subscription_buy',$data, true);

        $template['subject'] = $this->parseTemplate($template['subject'],$template['shortcode'],$data);

        $this->sendMail($user->email,$template['subject'], $data['mail_templates']);

        $data['mail_templates'] = $this->parseTemplate($template['admin_text'],$template['shortcode'],$data);
        // $body = $this->load->view('mails/subscription_buy',$data, true);
        $template['subject'] = $this->parseTemplate($template['admin_subject'],$template['shortcode'],$data);
        $this->sendMail('admin',$template['subject'], $data['mail_templates']);                                                            
    }   


    public function send_subscription_status_change($plan_id, $comment){
        $template = $this->getTemplateByID(0,'subscription_status_change');
        $data['buy'] = App\MembershipUser::find($plan_id);
        $user = App\User::find($data['buy']->user_id);

        $data['mob']       = $this->mobile_number;
        $data['orderLink'] = base_url('membership/membership_purchase_edit/'. $plan_id);
        $data['planname'] = ($data['buy']->plan ? $data['buy']->plan->name : '');
        $data['price'] = ($data['buy']->plan ? ($data['buy']->plan->price > 0 ? c_format($data['buy']->plan->price) : 'Free') : 'Free');
        $data['expire_at'] = $data['buy']->expire_at;
        $data['status_text'] = $data['buy']->status_text;
        $data['started_at'] = $data['buy']->started_at;
        $data['firstname'] = $user->firstname;
        $data['lastname'] = $user->lastname;
        $data['email'] = $user->email;
        $data['username'] = $user->username;
        $data['comment'] = $comment;

        $body = $this->parseTemplate($template['text'],$template['shortcode'],$data);
        $template['subject'] = $this->parseTemplate($template['subject'],$template['shortcode'],$data);
        $this->sendMail($user->email,$template['subject'],$body);
    }

    public function send_subscription_buy($plan_id){
        $template = $this->getTemplateByID(0,'subscription_buy');
        $data['buy'] = App\MembershipUser::find($plan_id);
        $user = App\User::find($data['buy']->user_id);

        $data['mob']       = $this->mobile_number;
        $data['orderLink'] = base_url('usercontrol/membership_purchase_details/'. $plan_id);
        $data['planname'] = ($data['buy']->plan ? $data['buy']->plan->name : '');
        $final_price = ((isset($data['buy']->plan->special)) && ($data['buy']->plan->special > 0)) ? $data['buy']->plan->special : $data['buy']->plan->price;

        $data['price'] = ($data['buy']->plan ? ($final_price > 0 ? c_format($final_price) : 'Free') : 'Free');
        $data['expire_at'] = $data['buy']->expire_at;
        $data['started_at'] = $data['buy']->started_at;
        $data['firstname'] = $user->firstname;
        $data['lastname'] = $user->lastname;
        $data['email'] = $user->email;
        $data['username'] = $user->username;

            $data['mail_templates'] = $this->parseTemplate($template['text'],$template['shortcode'],$data);
            $body = $this->load->view('mails/subscription_buy',$data, true);
            $template['subject'] = $this->parseTemplate($template['subject'],$template['shortcode'],$data);
            $this->sendMail($user->email,$template['subject'],$body);

            $data['orderLink'] = base_url('membership/membership_purchase_edit/'. $plan_id);

            $data['mail_templates'] = $this->parseTemplate($template['admin_text'],$template['shortcode'],$data);
            $body = $this->load->view('mails/subscription_buy',$data, true);
            $template['subject'] = $this->parseTemplate($template['admin_subject'],$template['shortcode'],$data);
            $this->sendMail('admin',$template['subject'],$body);        
    }

    public function external_order($order_id, $vendor_id = 0){
        $this->load->model("IntegrationModel");

        $order = $this->IntegrationModel->getOrder($order_id);
        $data = array(
            'external_website_name' => $order['base_url'],
            'commission'            => c_format($order['commission']),
            'username'              => $order['user_name'],
            'product_ids'           => $order['product_ids'],
            'total'                 => c_format($order['total']),
            'currency'              => $order['currency'],
            'commission_type'       => $order['commission_type'],
            'script_name'           => $order['script_name'],
            'email'                 => $order['email'],
        );

        $template = $this->getTemplateByID(11);

        
        $body = $this->parseTemplate($template['text'],$template['shortcode'],$data);
        $to      = $data['email'];
        $template['subject'] = $this->parseTemplate($template['subject'],$template['shortcode'],$data);
        $this->sendMail($to,$template['subject'],$body);


        $body = $this->parseTemplate($template['admin_text'],$template['shortcode'],$data);
        $to      = $data['email'];
        $template['subject'] = $this->parseTemplate($template['admin_subject'],$template['shortcode'],$data);
        $this->sendMail('admin',$template['subject'],$body);

        if($vendor_id){
            $vendor = $this->db->query("SELECT * FROM users WHERE id=". (int)$vendor_id)->row_array();
            $template = $this->getTemplateByID(false,'order_on_vendor_program');
            $body = $this->parseTemplate($template['text'],$template['shortcode'],$data);
            $template['subject'] = $this->parseTemplate($template['subject'],$template['shortcode'],$data);
            $this->sendMail($vendor['email'],$template['subject'],$body);
        }
        
    }

    public function send_forget_mail($user,$resetlink){
        $data = (array)$user;
        $data['reset_link'] = '<a href="' .$resetlink . '">RESET PASSWORD</a>';
        $template = $this->getTemplateByID(3);   

        if($data['type'] == 'client'){
            $body = $this->parseStoreTemplate($template['client_text'],$template['shortcode'],$data);
            $to      = $data['email'];
            $template['subject'] = $this->parseStoreTemplate($template['client_subject'],$template['shortcode'],$data);
            return $this->sendMail($to,$template['subject'],$body, true);
        } 
        else if($data['type'] == 'admin'){
            $body = $this->parseTemplate($template['admin_text'],$template['shortcode'],$data);
            $to      = $data['email'];
            $template['subject'] = $this->parseTemplate($template['admin_subject'],$template['shortcode'],$data);
            return $this->sendMail($to,$template['subject'],$body);
        } else{
            $body = $this->parseTemplate($template['text'],$template['shortcode'],$data);         
            $to      = $data['email'];
            $template['subject'] = $this->parseTemplate($template['subject'],$template['shortcode'],$data);
            return $this->sendMail($to,$template['subject'],$body);
        }
    }
    public function send_wallet_withdrawal_req($total, $userdetails){

        $data = array(
            'amount'          => c_format($total),
            'comment'         => $userdetails['firstname']. ' ' . $userdetails['lastname'] .' send a withdrawal request',
            'name'            => $userdetails['firstname']. ' ' . $userdetails['lastname'],
            'user_email'      => $userdetails['email'],
            'commission_type' => '',
        );

        $template = $this->getTemplateByID(4);   
        $body = $this->parseTemplate($template['text'],$template['shortcode'],$data);
        $to      = $data['user_email'];
        $template['subject'] = $this->parseTemplate($template['subject'],$template['shortcode'],$data);
        $this->sendMail($to,$template['subject'],$body);


        $template = $this->getTemplateByID(4);   
        $body = $this->parseTemplate($template['admin_text'],$template['shortcode'],$data);
        $template['subject'] = $this->parseTemplate($template['admin_subject'],$template['shortcode'],$data);
        
        $this->sendMail('admin',$template['subject'],$body);


        $this->load->model('Product_model');
        $notificationData = array(
            'notification_url'          => 'wallet_requests_list',
            'notification_type'         =>  'wallet',
            'notification_title'        =>  __('admin.you_made_withdrawal_request'),
            'notification_viewfor'      =>  'user',
            'notification_view_user_id' =>  $userdetails['id'],
            'notification_actionID'     =>  0,
            'notification_description'  =>  __('admin.you_made_withdrawal_request'),
            'notification_is_read'      =>  '0',
            'notification_created_date' =>  date('Y-m-d H:i:s'),
            'notification_ipaddress'    =>  $_SERVER['REMOTE_ADDR']
        );
        $this->Product_model->create_data('notification', $notificationData);
        $notificationData = array(
            'notification_url'          => 'wallet_requests_list',
            'notification_type'         =>  'wallet',
            'notification_title'        =>  $userdetails['firstname']. ' ' . $userdetails['lastname']. ' made a withdrawal request',
            'notification_viewfor'      =>  'admin',
            'notification_actionID'     =>  0,
            'notification_description'  =>  $userdetails['firstname']. ' ' . $userdetails['lastname']. ' made a withdrawal request',
            'notification_is_read'      =>  '0',
            'notification_created_date' =>  date('Y-m-d H:i:s'),
            'notification_ipaddress'    =>  $_SERVER['REMOTE_ADDR']
        );
        $this->Product_model->create_data('notification', $notificationData);
        return true;
      
    }

    public function wallet_noti_in_wallet($noti){

        $data = array(
            'amount'     => c_format($noti->amount),
            'comment'    => $noti->comment,
            'name'       => $noti->firstname. ' ' . $noti->lastname,
            'user_email' => $noti->email,
        );

        $template = $this->getTemplateByID(12);   
        $body = $this->parseTemplate($template['text'],$template['shortcode'],$data);
       
        $template['subject'] = $this->parseTemplate($template['subject'],$template['shortcode'],$data);
        $this->sendMail( $noti->email ,$template['subject'],$body);

        return true;      
    }

    public function wallet_noti_on_hold_wallet($noti){
        $status = $this->Wallet_model->status();
        $data = array(
            'amount'     => c_format($noti->amount),
            'comment'    => $noti->comment,
            'name'       => $noti->firstname. ' ' . $noti->lastname,
            'user_email' => $noti->email,
            'id'         => $noti->id,
            'new_status' => $status[$noti->status],
        );

        $template = $this->getTemplateByID(0,'wallet_noti_on_hold_wallet');   
        $body = $this->parseTemplate($template['text'],$template['shortcode'],$data);
       
        $template['subject'] = $this->parseTemplate($template['subject'],$template['shortcode'],$data);
        $this->sendMail( $noti->email ,$template['subject'],$body);

        return true;      
    }


    public function send_wallet_withdrawal_status($data){
        //$wallet = (array)$wallet;
        //$status = $this->Wallet_model->status();
        /*$data = array(
            'amount'          => c_format($wallet['amount']),
            'comment'         => $wallet['comment'],
            'name'            => $wallet['name'],
            'user_email'      => $wallet['user_email'],
            'commission_type' => $wallet['type'],
            'new_status'      => $status[$new_status],
        );*/
        $template = $this->getTemplateByID(5);   
        $body = $this->parseTemplate($template['text'],$template['shortcode'],$data);
        $to      = $data['user_email'];
        $template['subject'] = $this->parseTemplate($template['subject'],$template['shortcode'],$data);
        $this->sendMail($to,$template['subject'],$body);

        $template = $this->getTemplateByID(5);   
        $body = $this->parseTemplate($template['admin_text'],$template['shortcode'],$data);
        $template['subject'] = $this->parseTemplate($template['admin_subject'],$template['shortcode'],$data);
        $this->sendMail('admin',$template['subject'],$body);
    }
    public function send_test_mail($email){
        $data['mob']       = $this->mobile_number;
        $data['base_url']  = base_url();
        
        $template = $this->load->view('mails/header', $data, true);
        $template .= $this->load->view('mails/test', $data, true);
        $template .= $this->load->view('mails/footer', $data, true);
        $subject = "Email Testing";
        return $this->sendMail($email,$subject,$template);   
    }

    public function affiliate_mail($email,$data,$attachment){
        $body = $this->parseTemplate($data['message'],'',array());
        
        return $this->sendMail($email,$data['subject'],$body, false, true,$attachment);
    }

    public function send_store_contact_mail($data){
        $template = $this->getTemplateByID(6); 
        $body = $this->parseTemplate($template['admin_text'],$template['shortcode'],$data);
        $_subject = $this->parseTemplate($template['admin_subject'],$template['shortcode'],$data);
        $this->sendMail('admin',$_subject,$body);
        $body = $this->parseStoreTemplate($template['client_text'],$template['shortcode'],$data);
        $to   = $data['email'];
        $template_subject = $this->parseStoreTemplate($template['client_subject'],$template['shortcode'],$data);
        $this->sendMail($to,$template_subject,$body, true);
        

        $notificationData = array(
            'notification_url'          => '/contactus/',
            'notification_type'         =>  'contact_us',
            'notification_title'        =>  __('user.contact-us'),
            'store_contactus_description'        => $this->input->post('message',true),
            'notification_viewfor'      =>  'admin',
            'notification_actionID'     =>  0,
            'notification_description'  =>  $this->input->post('name',true).' '. __('admin.contact_us_message_sent_on') .' '.date('Y-m-d H:i:s'),
            'notification_is_read'      =>  '0',
            'notification_created_date' =>  date('Y-m-d H:i:s'),
            'notification_ipaddress'    =>  $_SERVER['REMOTE_ADDR']
        );
        $this->Product_model->create_data('notification', $notificationData);
        $last_insert_id = $this->db->insert_id();

        $update_data = array(
            'notification_url'          => 'contactus/'.$last_insert_id,
        );
        $where = array('notification_id'=>$last_insert_id);
        $this->Common_model->update('notification', $where, $update_data);

    }

    public function send_store_vendor_contact_mail($email, $message,$userid=0,$onlymessage){

        $result=$this->Mail_model->sendMail($email, __('store.vendor_store_contact_form_subject'), $message);
      
         $notificationData = array(
            'notification_url'          => '/contactus/',
            'notification_type'         =>  'contact_us',
            'notification_title'        =>  __('user.contact-us'),
            'store_contactus_description'   => $onlymessage,
            'notification_viewfor'      =>  'user',
            'notification_view_user_id' =>  $userid,
            'notification_actionID'     =>  0,
            'notification_description'  =>  $this->input->post('name',true).' '. __('admin.contact_us_message_sent_on') .' '.date('Y-m-d H:i:s'),
            'notification_is_read'      =>  '0',
            'notification_created_date' =>  date('Y-m-d H:i:s'),
            'notification_ipaddress'    =>  $_SERVER['REMOTE_ADDR']
        );
        $this->Product_model->create_data('notification', $notificationData);
        $last_insert_id = $this->db->insert_id();

        $update_data = array(
            'notification_url'          => 'contactus/'.$last_insert_id,
        );
        $where = array('notification_id'=>$last_insert_id);
        $this->Common_model->update('notification', $where, $update_data);
        return $result;
    }

    public function send_store_contact_vendor($data = array()){

        $this->reply_to = $data['email'];
        $this->reply_name = $data['firstname']."".$data['lastname'];

        $message = "";

        if(isset($data['subject'])) $message .= "Subject : ".$data['subject']."<br>";
        if(isset($data['email'])) $message .= "Email Address : ".$data['email']."<br>";
        if(isset($data['fname'])) $message .= "First name : ".$data['fname']."<br>";
        if(isset($data['lastname'])) $message .= "Last name : ".$data['lastname']."<br>";
        if(isset($data['phone'])) $message .= "Phone Number : ".$data['phone']."<br>";
        if(isset($data['domain'])) $message .= "Doamin Name : ".$data['domain']."<br>";
        if(isset($data['body'])) $message .= "Body : <pre>".$data['body']."</pre><br>";

        $attachment = (isset($data['attachment'])) ? $data['attachment'] : null;
        $body = $this->parseTemplate($message,[],$data);
        $_subject = $this->parseTemplate("New Message: ". $data['subject'],[],$data);

        $result=$this->sendMail('admin',$_subject,$body, false, false, $attachment);

        $notificationData = array(
            'notification_url'          => '/contactus/',
            'notification_type'         =>  'contact_us',
            'notification_title'        =>  __('user.contact-us'),
            'store_contactus_description'    => (isset($data['body'])) ? $data['body'] : '',
            'notification_viewfor'      =>  'admin',
            'notification_actionID'     =>  0,
            'notification_description'  =>   $this->reply_name.' '. __('admin.contact_us_message_sent_on') .' '.date('Y-m-d H:i:s'),
            'notification_is_read'      =>  '0',
            'notification_created_date' =>  date('Y-m-d H:i:s'),
            'notification_ipaddress'    =>  $_SERVER['REMOTE_ADDR']
        );
        $this->Product_model->create_data('notification', $notificationData);
        $last_insert_id = $this->db->insert_id();

        $update_data = array(
            'notification_url'          => 'contactus/'.$last_insert_id,
        );
        $where = array('notification_id'=>$last_insert_id);
        $this->Common_model->update('notification', $where, $update_data);

        return $result;
    }

    public function send_registration_approved_mail($user) {
        try {
            $data = (array)$user;
            $this->load->model('Product_model');
            $setting = $this->Product_model->getSettings('site');
            $template = $this->getTemplateByID(null, 'new_user_approved');
            $body = $this->parseTemplate($template['text'],$template['shortcode'],$data);
            $to   = $data['email'];
            $template_subject = $this->parseTemplate($template['subject'],$template['shortcode'],$data);
            $usermail = $this->sendMail($to,$template_subject,$body);
            $body = $this->parseTemplate($template['admin_text'],$template['shortcode'],$data);
            $template_subject = $this->parseTemplate($template['admin_subject'],$template['shortcode'],$data);
            $to = $setting['notify_email'];
            $adminmail =$this->sendMail($to,$template_subject,$body);
            return $usermail." ". __('admin.to_user_and') ." ".$adminmail." ".__('admin.to_admin');
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
 

    public function send_registration_declined_mail($user) {
        try {
            $data = (array)$user;
            $this->load->model('Product_model');
            $setting = $this->Product_model->getSettings('site');
            $template = $this->getTemplateByID(null, 'new_user_declined');
            $body = $this->parseTemplate($template['text'],$template['shortcode'],$data);
            $to   = $data['email'];
            $template_subject = $this->parseTemplate($template['subject'],$template['shortcode'],$data);
            $usermail = $this->sendMail($to,$template_subject,$body);
            $body = $this->parseTemplate($template['admin_text'],$template['shortcode'],$data);
            $template_subject = $this->parseTemplate($template['admin_subject'],$template['shortcode'],$data);
            $to = $setting['notify_email'];
            $adminmail = $this->sendMail($to,$template_subject,$body);
            return $usermail." ". __('admin.to_user_and') ." ".$adminmail." ".__('admin.to_admin');
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function send_registration_request_mail($user) {
        try {

            $data = (array)$user;
            $this->load->model('Product_model');
            $setting = $this->Product_model->getSettings('site');
            $template = $this->getTemplateByID(null, 'new_user_request');
            $body = $this->parseTemplate($template['text'],$template['shortcode'],$data);

            $to = $data['email'];
            $template_subject = $this->parseTemplate($template['subject'],$template['shortcode'],$data);
            $userMailRes = $this->sendMail($to,$template_subject,$body);
            $body = $this->parseTemplate($template['admin_text'],$template['shortcode'],$data);
            $template_subject = $this->parseTemplate($template['admin_subject'],$template['shortcode'],$data);
            $to = $setting['notify_email'];
            $adminMailRes = $this->sendMail($to,$template_subject,$body);
            return $userMailRes." ". __('admin.to_user_and') ." ".$adminMailRes." ".__('admin.to_admin');
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function send_register_mail($user,$subject){
        $data = (array)$user;

        if($data['user_type'] == 'user'){
            $this->load->model('Product_model');
            $setting = $this->Product_model->getSettings('site');
            $template = $this->getTemplateByID(1);
           
            $body = $this->parseTemplate($template['text'],$template['shortcode'],$data);

            $mail_verifiy=$data['mail_verifiy'];
            if($mail_verifiy==1)
            {
                $verification_id=$data['verification_id'];
                $verify_url=base_url('pagebuilder/verification')."/".$verification_id."/".$data['user_id'];
                $body.="<br/>";
                $body.=__('admin.please_click_below_link_to_verify_your_account');
                $body.="<a href='".$verify_url."'>".__('admin.click_here_to_verify_account')."</a>";  
            }


            $to      = $user['email'];
            $template_subject = $this->parseTemplate($template['subject'],$template['shortcode'],$data);
            $this->sendMail($to,$template_subject,$body);
            
            $body = $this->parseTemplate($template['admin_text'],$template['shortcode'],$data);
           
            $template_subject = $this->parseTemplate($template['admin_subject'],$template['shortcode'],$data);
            $to = $setting['notify_email'];
            $this->sendMail($to,$template_subject,$body);

        } else if($data['user_type'] == 'client'){

            $template = $this->getTemplateByID(2);
            $body = $this->parseStoreTemplate($template['client_text'],$template['shortcode'],$data);
            $to      = $data['email'];
            $template_subject = $this->parseStoreTemplate($template['client_subject'],$template['shortcode'],$data);
            
            $this->sendMail($to,$template_subject,$body, true);
            $body = $this->parseTemplate($template['admin_text'],$template['shortcode'],$data);
            $template_subject = $this->parseTemplate($template['admin_subject'],$template['shortcode'],$data);
            $this->sendMail('admin',$template_subject,$body);
        }
     
    }


    

    public function send_register_integration_mail($data,$subject){
        $this->load->model('Product_model');
        $setting = $this->Product_model->getSettings('site');
    
        $template = $this->getTemplateByID(13);
        $body = $this->parseTemplate($template['text'],$template['shortcode'],$data);
        $template_subject = $this->parseTemplate($template['subject'],$template['shortcode'],$data);
        $this->sendMail( $data['email'],$template_subject,$body);

        $body = $this->parseTemplate($template['admin_text'],$template['shortcode'],$data);
        $template_subject = $this->parseTemplate($template['admin_subject'],$template['shortcode'],$data);
        $this->sendMail($setting['notify_email'],$template_subject,$body);
        
    }
    
    public function send_order_mail($order_id){
        $this->load->model('Order_model');
        $data['order']          = $this->Order_model->getOrder($order_id);
        $data['status']         = $this->Order_model->status();
        $data['orderLink']      = base_url('store/vieworder/'.$order_id);
        $data['mob']            = $this->mobile_number;
        $mailData               = $data['order'];
        $mailData['total']      = c_format($mailData['total']);
        $mailData['order_id']   = orderId($mailData['id']);
        $mailData['order_link'] = '<a href="'. $data['orderLink'] .'"> '.__('admin.view_order').' </a>';
        $mailData['status']     = $data['status'][$mailData['status']];
        $comment  = $this->db->query("SELECT * FROM orders_history WHERE order_id = {$order_id} ORDER BY id DESC LIMIT 1")->row_array();
        $mailData['comment']     = $comment['comment'];
        $template = $this->getTemplateByID(7);   
        $body = $this->parseStoreTemplate($template['text'],$template['shortcode'],$mailData);
        $to = $data['order']['email'];
        $template['subject'] = $this->parseStoreTemplate($template['subject'],$template['shortcode'],$mailData);
        return $this->sendMail($to,$template['subject'],$body, true);
       
    }
    public function send_new_order_mail($order_id, $wallet_status = 0){
        $this->load->model('Order_model');
        $this->load->model('Product_model');
        $data['order'] = $this->Order_model->getOrder($order_id);
        $data['products'] = $this->Order_model->getProducts($order_id);
        $data['totals'] = $this->Order_model->getTotals($data['products'],$data['order']);
        $data['payment_history'] = $this->Order_model->getHistory($order_id);
        $data['order_history'] = $this->Order_model->getHistory($order_id, 'order');
        $data['status'] = $this->Order_model->status();
        $data['orderLink'] = base_url('store/vieworder/'.$order_id);
        $data['mob'] = $this->mobile_number;
            
        $data['paymentsetting'] = $this->Product_model->getSettings('paymentsetting');

        $mailData  = $data['order'];
        $mailData['order_link']  = '<a href="'. $data['orderLink'] .'"> '.__('admin.view_order').' </a>';
        $mailData['status']  = $data['status'][$mailData['status']];
        $mailData['total']  = c_format($mailData['total']);
        $mailData['order_id']  = orderId($mailData['id']);

        foreach ($data['products'] as $key => $value) {
            if($value['vendor_id'] > 0){
                $user = (array)$this->Product_model->getUserDetails($value['vendor_id']);

                $mailData['vendor_firstname'] = $user['firstname'];
                $mailData['vendor_lastname'] = $user['lastname'];
                $mailData['vendor_commission_type'] = $value['vendor_commission_type'];
                $mailData['vendor_commission'] = c_format($value['vendor_commission']);
                $mailData['commission_type'] = $value['commission_type'];
                $mailData['product_name'] = $value['product_name'];
                if(isset($product['variation']) && !empty($product['variation'])) {
                    $variation = json_decode($product['variation']);
                    foreach ($variation as $key => $value) {
                        if($key == 'colors') {
                            $combinationString .= ($combinationString == "") ? explode("-",$value)[1] : ",".explode("-",$value)[1];
                        } else {
                            $combinationString .= ($combinationString == "") ? $value : ",".$value;
                        }
                    }
                }
                $combinationString = (isset($combinationString) && $combinationString != "") ? "(".$combinationString.")" : "";
                $mailData['variation_details'] = $combinationString;
                $mailData['product_description'] = $value['product_short_description'];
                $mailData['commission'] = c_format($value['commission']);

                $template = $this->getTemplateByID(0,'new_order_for_vendor');   
                $body = $this->parseTemplate($template['text'],$template['shortcode'],$mailData);
                $_subject = $this->parseTemplate($template['subject'],$template['shortcode'],$mailData);

                $this->sendMail($user['email'],$_subject,$body);
            }
        }
            
        $template = $this->getTemplateByID(8);   
        $data['show_commition'] = 1;
        $body = $this->parseTemplate($template['admin_text'],$template['shortcode'],$mailData);
        $body .= $this->load->view('form/order_mail',$data, true);
        $_subject = $this->parseTemplate($template['admin_subject'],$template['shortcode'],$mailData);
        $this->sendMail('admin',$_subject,$body);

        /* For Client */
        $data['show_commition'] = 0;
        $body = $this->parseStoreTemplate($template['client_text'],$template['shortcode'],$mailData);
        $body .= $this->load->view('form/order_mail',$data, true);
        $_subject = $this->parseStoreTemplate($template['client_subject'],$template['shortcode'],$mailData);
        $to = $data['order']['email'];
        $this->sendMail($to,$_subject,$body,true);

        $this->send_commition_mail($order_id);
        return true;
    }

    public function send_register_mail_api($user,$subject){
        $data = (array)$user;
        $this->load->model('Product_model');
        $setting = $this->Product_model->getSettings('site');
        $data['website_url'] = base_url('/');
    
        $template = $this->getTemplateByID(0,'send_register_mail_api');
        $body = $this->parseTemplate($template['text'],$template['shortcode'],$data);
        $to      = $user['email'];

        $template_subject = $this->parseTemplate($template['subject'],$template['shortcode'],$data);
        $this->sendMail($to,$template_subject,$body);

        $body = $this->parseTemplate($template['admin_text'],$template['shortcode'],$data);
        $template_subject = $this->parseTemplate($template['admin_subject'],$template['shortcode'],$data);
        $to = $setting['notify_email'];
        $this->sendMail($to,$template_subject,$body);
        
    }

    public function send_commition_mail($order_id,$allow_bank_transfer = false)
    {
        $this->load->model('Order_model');
        $this->load->model('Product_model');
        $data['order'] = $this->Order_model->getOrder($order_id);
        $data['products'] = $this->Order_model->getProducts($order_id);
        $data['totals'] = $this->Order_model->getTotals($data['products'],$data['order']);
        $data['payment_history'] = $this->Order_model->getHistory($order_id);
        $data['order_history'] = $this->Order_model->getHistory($order_id, 'order');
        $data['status'] = $this->Order_model->status();
        $data['orderLink'] = base_url('store/vieworder/'.$order_id);
        $data['mob'] = $this->mobile_number;

        $data['paymentsetting'] = $this->Product_model->getSettings('paymentsetting');

        $mailData  = $data['order'];
        $mailData['order_link']  = '<a href="'. $data['orderLink'] .'"> '.__('admin.view_order').' </a>';
        $mailData['status']  = $data['status'][$mailData['status']];
        $mailData['total']  = c_format($mailData['total']);
        $mailData['order_id']  = orderId($mailData['id']);

         

        if((int)$data['order']['status'] == 1){
                $_getAffiliateUser = $this->Order_model->getAffiliateUser($order_id);
                if($_getAffiliateUser){
                    foreach ($_getAffiliateUser as $key => $getAffiliateUser) {
                        $to = $getAffiliateUser['email'];
                        foreach ($getAffiliateUser as $key => $value) {
                            $mailData['affiliate_'. $key] = $value;
                        }

                        $template = $this->getTemplateByID(8);

                        $mailData['commission_type'] = $getAffiliateUser['commission_type'];
                        $mailData['product_name'] = $getAffiliateUser['product_name'];
                        $mailData['product_description'] = $getAffiliateUser['product_short_description'];
                        $mailData['commission'] = c_format($getAffiliateUser['commission']);

                        $body = $this->parseTemplate($template['text'],$template['shortcode'],$mailData);
                        $template['subject'] = $this->parseTemplate($template['subject'],$template['shortcode'],$mailData);
                        $this->sendMail($to,$template['subject'],$body);
                    }
                }

            foreach ($data['products'] as $key => $value) {
                if($value['vendor_id'] > 0){
                    $user = (array)$this->Product_model->getUserDetails($value['vendor_id']);
                    $mailData['vendor_firstname'] = $user['firstname'];
                    $mailData['vendor_lastname'] = $user['lastname'];
                    $mailData['vendor_commission_type'] = $value['vendor_commission_type'];
                    $mailData['vendor_commission'] = c_format($value['vendor_commission']);
                    $mailData['commission_type'] = $value['commission_type'];
                    $mailData['product_name'] = $value['product_name'];
                    $mailData['product_description'] = $value['product_short_description'];
                    $mailData['commission'] = c_format($value['commission']);

                    $template = $this->getTemplateByID(0,'vendor_order_status_complete');   
                    $body = $this->parseTemplate($template['text'],$template['shortcode'],$mailData);
                    $_subject = $this->parseTemplate($template['subject'],$template['shortcode'],$mailData);

                    $this->sendMail($user['email'],$_subject,$body);
                }
            }
        }

        return true;
    }


    public function market_click_notification($user_id,$affiliate_id,$affiliateads_type,$affiliate_commission){
        $this->load->model('user_model', 'user');
        $user = $this->user->get_user_by_id($user_id);
        $data['affiliateads_type']    = $affiliateads_type;
        $data['affiliate_commission'] = $affiliate_commission;
        $data['user']                 = $user;
        $data['mob']                  = $this->mobile_number;
        $data['base_url']             = base_url();
        $this->load->model('Product_model');

        $setting = $this->Product_model->getSettings('site');
        $admin = $setting['notify_email'];
        $notificationData = array(
            'notification_url'          => '/mywallet',
            'notification_type'         =>  'market_click',
            'notification_title'        =>  __('admin.you_got_commition_from_market')." {$affiliateads_type} ".__('admin.click')."",
            'notification_viewfor'      =>  'user',
            'notification_view_user_id' =>  $user['id'],
            'notification_actionID'     =>  $affiliate_id,
            'notification_description'  =>  __('admin.you_got_commition_from_market')." {$affiliateads_type} ".__('admin.click')."",
            'notification_is_read'      =>  '0',
            'notification_created_date' =>  date('Y-m-d H:i:s'),
            'notification_ipaddress'    =>  $_SERVER['REMOTE_ADDR']
        );
        $this->Product_model->create_data('notification', $notificationData);
        $notificationData = array(
            'notification_url'          => '/mywallet',
            'notification_type'         =>  'market_click',
            'notification_title'        =>  $user['firstname'] ." ". $user['lastname'] ." ".__('admin.got_commition_from_market')." {$affiliateads_type} ".__('admin.click')."",
            'notification_viewfor'      =>  'admin',
            'notification_actionID'     =>  $affiliate_id,
            'notification_description'  =>  $user['firstname'] ." ". $user['lastname'] ." ".__('admin.got_commition_from_market')." {$affiliateads_type} ".__('admin.click')."",
            'notification_is_read'      =>  '0',
            'notification_created_date' =>  date('Y-m-d H:i:s'),
            'notification_ipaddress'    =>  $_SERVER['REMOTE_ADDR']
        );
        $this->Product_model->create_data('notification', $notificationData);

        $mailData = $data['user'];
        $mailData['affiliateads_type'] = $data['affiliateads_type'];
        $mailData['affiliate_commission'] = $data['affiliate_commission'];
        $template = $this->getTemplateByID(10);
        $body = $this->parseTemplate($template['text'],$template['shortcode'],$mailData);
        $template['subject'] = $this->parseTemplate($template['subject'],$template['shortcode'],$mailData);
        $this->sendMail($to,$template['subject'],$body);

        $template = $this->getTemplateByID(10);
        $body = $this->parseTemplate($template['admin_text'],$template['shortcode'],$mailData);
        $template['subject'] = $this->parseTemplate($template['admin_subject'],$template['shortcode'],$mailData);
        $this->sendMail('admin',$template['subject'],$body);
        return true;
    }
    
    
    private function sendMailPreview($to, $subject, $htmlContent, $is_store = false, $allow_bootstrap = false, $attachments = null){
        try { 
            $this->load->library('email');
            $setting = $this->Product_model->getSettings('email');
            if($to == 'admin'){
                $to = $setting['from_email'];
            }
            
            $config = array(
                'mailtype'  => 'html',
                'charset'   => 'utf-8'
            );

            if($allow_bootstrap){
                $data['bootstrap_cdn'] = true;
            }

            $data['emailsetting']   = $this->Product_model->getSettings('emailsetting');
            $data['mob']       = $this->mobile_number;
            $data['base_url']  = base_url();
            $data['html']  = $htmlContent;
            $data['unsub_text'] .= '<a href="'.base_url('unsubscribe/'.base64_encode($to)).'" style="color:#0000ff; text-decoration:underline;">'.__('admin.unsubscribe').'</a>';
            
            $template = $this->load->view('mails/body', $data, true);

            return $template;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
    
    private function sendMail($to, $subject, $htmlContent, $is_store = false, $allow_bootstrap = false, $attachments = null){
        $unsbscribed = $this->db->query('SELECT id FROM unsubscribed_emails WHERE email="'.$to.'"')->row();
        if(!empty($unsbscribed)) {
            return __('admin.email_is_unsubscribed');
        } else {
            try { 
                $this->load->library('email');
                $settingSite = $this->Product_model->getSettings('site');

                $setting = $this->Product_model->getSettings('email');
                if($to == 'admin'){
                    $to = $settingSite['notify_email'];
                }
                
                $config = array(
                    'mailtype'  => 'html',
                    'charset'   => 'utf-8'
                );

                
                if($allow_bootstrap){
                    $data['bootstrap_cdn'] = true;
                }

                $data['emailsetting']   = $this->Product_model->getSettings('emailsetting');
                $data['mob']       = $this->mobile_number;
                $data['base_url']  = base_url();
                $data['html']  = $htmlContent;
                

                $data['unsub_text'] .= '<a href="'.base_url('unsubscribe/'.base64_encode($to)).'" style="color:#0000ff; text-decoration:underline;">'.__('admin.unsubscribe').'</a>';
            
                $template = $this->load->view('mails/body', $data, true);
                
                if ($setting['smtp_crypto'] != '') {
                    $config['smtp_crypto']  = $setting['smtp_crypto'];
                }


                if(!isset($this->reply_to) || empty($this->reply_to) || ! isset($from)) {
                    $from = $this->reply_to;
                    $fromName = $this->reply_name;
                }

                if ($setting['mail_send_option'] == 'disable') {
                    return __('admin.send_mail_is_disabled');
                }
                else
                {
                    if ($setting['mail_type'] == 'smtp') {
                        if(!empty($setting['smtp_hostname']) && !empty($setting['smtp_port'])) {
                            $config['protocol']  = 'smtp';
                            $config['smtp_host'] = $setting['smtp_hostname'];
                            $config['smtp_port'] = $setting['smtp_port'];
                            $config['mailtype']  = 'html';
                            $config['charset']   = 'utf-8';

                            if (!empty($setting['smtp_username']) and !empty($setting['smtp_password'])) {
                                $config['smtp_user'] = $setting['smtp_username'];
                                $config['smtp_pass'] = $setting['smtp_password'];
                            }

                            if(!isset($this->reply_to) || empty($this->reply_to) || ! isset($from)) {
                                $from = (!empty($setting['from_email'])) ? $setting['from_email'] : $setting['smtp_username'];
                                $fromName = (!empty($setting['from_name'])) ? $setting['from_name'] : $_SERVER['SERVER_NAME'];
                            }
                            $this->email->initialize($config);
                            $this->email->set_mailtype("html");
                            $this->email->set_newline("\r\n");
                            $this->email->to($to);
                            $this->email->from($from,$fromName);
                            $this->email->reply_to($from);
                            $this->email->subject($subject);
                            $this->email->message($template);

                            if($attachments != null) {
                                $this->email->attach($attachments);
                            }
                            if($this->email->send()) {
                            return __('admin.mail_sent_successfully_using_smtp');
                            } else {
                                // Display the error message on the screen
                                $error_message = "Email error: " . $this->email->print_debugger();
                                return $error_message;
                            }
                        } else {
                            return __('admin.smtp_details_not_saved');
                        }
                    }

                    if ($setting['mail_type'] == 'php_mailer') {
                        if($attachments == null) {
                            if(!isset($this->reply_to) || empty($this->reply_to)) {
                                $from = (!empty($setting['from_email'])) ? $setting['from_email'] : $setting['smtp_username'];
                                $fromName = (!empty($setting['from_name'])) ? $setting['from_name'] : $_SERVER['SERVER_NAME'];
                            }

                            $headers  = 'MIME-Version: 1.0' . "\r\n";
                            $headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
                            $headers .= 'From: '.$fromName."\r\n".'Reply-To: '.$from."\r\n" .'X-Mailer: PHP/' . phpversion();

                            if(mail($to, $subject, $template, $headers)) {
                                return __('admin.mail_sent_successfully_using_php_mailer');
                            } else {
                                return __('admin.mail_not_sent_using_php_mailer');
                            };
                        } else {
                            $random_hash = md5(date('r', time()));

                            $headers = "From: ".$fromName."\r\nReply-To: ".$from."";

                            $headers .= "\r\nContent-Type: multipart/mixed; boundary=\"PHP-mixed-".$random_hash."\"";

                            $attachment = chunk_split(base64_encode(file_get_contents($attachments)));

                            $attachmentPAthArray = explode('/', $attachments);

                            //define the body of the message.
                            ob_start(); //Turn on output buffering
                            ?>
                            --PHP-mixed-<?php echo $random_hash; ?> 
                            Content-Type: multipart/alternative; boundary="PHP-alt-<?php echo $random_hash; ?>"

                            --PHP-alt-<?php echo $random_hash; ?> 
                            Content-Type: text/html; charset="iso-8859-1"
                            Content-Transfer-Encoding: 7bit

                            <?php echo $template ?>;

                            --PHP-alt-<?php echo $random_hash; ?>--

                            --PHP-mixed-<?php echo $random_hash; ?> 
                            Content-Type: <?php echo mime_content_type($attachments) ?>; name="<?php echo $attachmentPAthArray[sizeof($attachmentPAthArray)-1] ?>" 
                            Content-Transfer-Encoding: base64 
                            Content-Disposition: attachment 

                            <?php echo $attachment; ?>
                            --PHP-mixed-<?php echo $random_hash; ?>--

                            <?php
                            //copy current buffer contents into $message variable and delete current output buffer
                            $message = ob_get_clean();
                            //send the email
                            $mail_sent = @mail( $to, $subject, $message, $headers );

                            if($mail_sent) {
                                return __('admin.mail_sent_successfully_using_php_mailer');
                            } else {
                                return __('admin.mail_not_sent_using_php_mailer');
                            }
                        }
                    }

                //
                }
                ////

            } catch (Exception $e) {
                return $e->getMessage();
            }
        }
    }
    
    public function preview_mail($data){
        $template = $this->getTemplateByID($data['id']);
        $testing = array();
        $body = $this->parseTemplate($template[$data['prifix'] . 'text'],$template['shortcode'],$testing);
        $_subject = $this->parseTemplate($template[$data['prifix'] . 'subject'],$testing);
        return $this->sendMailPreview($data['test_email'],$_subject,$body);
        die();
    }


    public function test_new($data){
      
        $template = $this->getTemplateByID($data['id']);
        $testing = array();
        $prifix = '';
        if($data['test_for'] == 'for-admin'){
          $prifix = 'admin_';  
        } else if($data['test_for'] == 'for-client') {
            $prifix = 'client_';

            $body = $this->parseStoreTemplate($data[$prifix . 'text'],$template['shortcode'],$testing);
            $_subject = $this->parseStoreTemplate($data[$prifix . 'subject'],$template['shortcode'],$testing);
            
            return $this->sendMail($data['test_email'],$_subject,$body, true);
        }

        $body = $this->parseTemplate($data[$prifix . 'text'],$template['shortcode'],$testing);
        $_subject = $this->parseTemplate($data[$prifix . 'subject'],$testing);
  
        return $this->sendMail($data['test_email'],$_subject,$body);
    }

    public function vendor_create_ads($program_id){
        $data = $this->db->query("SELECT integration_tools.*,users.firstname,users.lastname,users.email,users.username FROM integration_tools LEFT JOIN users ON users.id=integration_tools.vendor_id WHERE integration_tools.id=". (int)$program_id)->row_array();
        $data['mob'] = $this->mobile_number;

        $comment = (array)json_decode($data['comment'],1);
        $vendor_last_message = '';
        $admin_last_message = '';

        foreach ($comment as $com) {
            if($com['from'] == 'affiliate') $vendor_last_message = $com['comment'];
            if($com['from'] == 'admin') $admin_last_message = $com['comment'];
        }
        
        $data['vendor_last_message'] = $vendor_last_message;
        $data['admin_last_message'] = $admin_last_message;
        $data['type'] = ucfirst( str_replace("_", " ", $data['type']));
        $data['tool_type'] = ucfirst( str_replace("_", " ", $data['tool_type']));

        $template = $this->getTemplateByID(0,'vendor_create_ads');   
        $body = $this->parseTemplate($template['admin_text'],$template['shortcode'],$data);
        $_subject = $this->parseTemplate($template['admin_subject'],$template['shortcode'],$data);
        $this->sendMail('admin',$_subject,$body);
    }


    public function withdrwal_status_change($request_id,$post){
        
        $query = $this->db->query("SELECT wr.*,u.firstname,u.lastname,u.username,u.email,u.device_token,u.device_type FROM wallet_requests wr LEFT JOIN users u ON u.id=wr.user_id WHERE wr.id=". (int)$request_id)->row();

        $data['comment']  = $post['comment'];
        $data['status']  = strip_tags(withdrwal_status($post['status_id']));
        $data['request_id']  = $request_id;
        $data['firstname']  = $query->firstname;
        $data['lastname']  = $query->lastname;
        $data['email']  = $query->email;
        $data['username']  = $query->username;
        $device_type  = $query->device_type;
        $device_token  = $query->device_token;

        $data['mob'] = $this->mobile_number;


        $template = $this->getTemplateByID(0,'withdrwal_status_change');   
        $body = $this->parseTemplate($template['text'],$template['shortcode'],$data);
        $_subject = $this->parseTemplate($template['subject'],$template['shortcode'],$data);


        $notificationData = array(
            'notification_url'          => 'wallet_requests_list/',
            'notification_type'         =>  'wallet_requests',
            'notification_title'        => __('admin.withdrawal_request_status_changed'),
            'notification_view_user_id' =>  $query->user_id,
            'notification_viewfor'      =>  'user',
            'notification_actionID'     =>  $query->id,
            'notification_description'  =>  __('admin.your_Withdrawal_request_status_changed_to').' '. $data['status'] .' on '.date('Y-m-d H:i:s'),
            'notification_is_read'      =>  '0',
            'notification_created_date' =>  date('Y-m-d H:i:s'),
            'notification_ipaddress'    =>  $_SERVER['REMOTE_ADDR']
        );

        $this->Product_model->create_data('notification', $notificationData);

        $this->sendMail($query->email,$_subject,$body);

        $title = __('admin.withdrawal_request_status_changed');
        $message = __('admin.your_Withdrawal_request_status_changed_to').' '. $data['status'] .' on '.date('Y-m-d H:i:s');

        if($device_type == 1)
        {
            send_push_android($device_token,$title,$message);
        }
        else if($device_type == 2)
        {
            send_push_ios($device_token,$title,$message);
        }
    }

    public function vendor_ads_status_change($program_id, $mail_for = 'admin', $send_notification = false)
    {
        $data = $this->db->query("SELECT integration_tools.*,users.firstname,users.lastname,users.email,users.username FROM integration_tools LEFT JOIN users ON users.id=integration_tools.vendor_id WHERE integration_tools.id=". (int)$program_id)->row_array();
        $data['mob'] = $this->mobile_number;

        $comment = (array)json_decode($data['comment'],1);
        $vendor_last_message = '';
        $admin_last_message = '';

        foreach ($comment as $com) {
            if($com['from'] == 'affiliate') $vendor_last_message = $com['comment'];
            if($com['from'] == 'admin') $admin_last_message = $com['comment'];
        }
        $type = $data['type'];

        $data['vendor_last_message'] = $vendor_last_message;
        $data['admin_last_message'] = $admin_last_message;
        $data['type'] = ucfirst( str_replace("_", " ", $data['type']));
        $data['tool_type'] = ucfirst( str_replace("_", " ", $data['tool_type']));

        $template = $this->getTemplateByID(0,'vendor_ads_status_'. $data['status']);
        if($mail_for == 'admin'){
            $body = $this->parseTemplate($template['admin_text'],$template['shortcode'],$data);
            $_subject = $this->parseTemplate($template['admin_subject'],$template['shortcode'],$data);

            if($send_notification){
                $notificationData = array(
                    'notification_url'          => "integration_tools_form/{$type}/{$program_id}",
                    'notification_type'         =>  'integration_tools',
                    'notification_title'        =>  __('admin.vendor_ads_status_change_to_0'),
                    'notification_viewfor'      =>  'admin',
                    'notification_actionID'     =>  $program_id,
                    'notification_description'  =>  $data['name'].' '.__('admin.ads_status_change_to_in_review').' '. $user['username'] .' on '.date('Y-m-d H:i:s'),
                    'notification_is_read'      =>  '0',
                    'notification_created_date' =>  date('Y-m-d H:i:s'),
                    'notification_ipaddress'    =>  $_SERVER['REMOTE_ADDR']
                );  

                $this->db->insert('notification', $notificationData);
            }

            $this->sendMail('admin',$_subject,$body);
        } else {
            $body = $this->parseTemplate($template['text'],$template['shortcode'],$data);
            $_subject = $this->parseTemplate($template['subject'],$template['shortcode'],$data);

            if($send_notification){
                $notificationData = array(
                    'notification_url'          => "integration_tools_form/{$type}/{$program_id}",
                    'notification_type'         =>  'integration_tools',
                    'notification_title'        =>  __('user.vendor_ads_status_change_to_' . $data['status']),
                    'notification_view_user_id' =>  $data['vendor_id'],
                    'notification_viewfor'      =>  'user',
                    'notification_actionID'     =>  $program_id,
                    'notification_description'  =>  __('admin.your_ads_status_changed_by_admin_please_review').' '.date('Y-m-d H:i:s'),
                    'notification_is_read'      =>  '0',
                    'notification_created_date' =>  date('Y-m-d H:i:s'),
                    'notification_ipaddress'    =>  $_SERVER['REMOTE_ADDR']
                ); 

                $this->db->insert('notification', $notificationData);
            }

            $this->sendMail($data['email'],$_subject,$body);
        }
    }

    public function vendor_create_program($program_id){
        $data = $this->db->query("SELECT integration_programs.*,users.firstname,users.lastname,users.email,users.username FROM integration_programs LEFT JOIN users ON users.id=integration_programs.vendor_id WHERE integration_programs.id=". (int)$program_id)->row_array();
        $data['mob'] = $this->mobile_number;

        $comment = (array)json_decode($data['comment'],1);
        $vendor_last_message = '';
        $admin_last_message = '';

        foreach ($comment as $com) {
            if($com['from'] == 'affiliate') $vendor_last_message = $com['comment'];
            if($com['from'] == 'admin') $admin_last_message = $com['comment'];
        }
        
        $data['vendor_last_message'] = $vendor_last_message;
        $data['admin_last_message'] = $admin_last_message;

        $template = $this->getTemplateByID(0,'vendor_create_program');   
        $body = $this->parseTemplate($template['admin_text'],$template['shortcode'],$data);
        $_subject = $this->parseTemplate($template['admin_subject'],$template['shortcode'],$data);
        $this->sendMail('admin',$_subject,$body);

  
        $notificationData = array(
            'notification_url'          => "integration/programs_form/{$program_id}",
            'notification_type'         =>  'integration_program',
            'notification_title'        =>  __('admin.vendor_created_new_program'),
            'notification_view_user_id' =>  $data['vendor_id'],
            'notification_viewfor'      =>  'admin',
            'notification_actionID'     =>  $program_id,
            'notification_description'  =>  __('admin.vendor_created_new_program_at').' '.date('Y-m-d H:i:s'),
            'notification_is_read'      =>  '0',
            'notification_created_date' =>  date('Y-m-d H:i:s'),
            'notification_ipaddress'    =>  $_SERVER['REMOTE_ADDR']
        ); 

        $this->db->insert('notification', $notificationData);
        
    }

    public function vendor_program_status_change($program_id, $mail_for = 'admin', $send_notification = false)
    {
        $data = $this->db->query("SELECT integration_programs.*,users.firstname,users.lastname,users.email,users.username FROM integration_programs LEFT JOIN users ON users.id=integration_programs.vendor_id WHERE integration_programs.id=". (int)$program_id)->row_array();
        $data['mob'] = $this->mobile_number;

        $comment = (array)json_decode($data['comment'],1);
        $vendor_last_message = '';
        $admin_last_message = '';

        foreach ($comment as $com) {
            if($com['from'] == 'affiliate') $vendor_last_message = $com['comment'];
            if($com['from'] == 'admin') $admin_last_message = $com['comment'];
        }
        
        $data['vendor_last_message'] = $vendor_last_message;
        $data['admin_last_message'] = $admin_last_message;

        $template = $this->getTemplateByID(0,'vendor_program_status_'. $data['status']);
        if($mail_for == 'admin'){
            $body = $this->parseTemplate($template['admin_text'],$template['shortcode'],$data);
            $_subject = $this->parseTemplate($template['admin_subject'],$template['shortcode'],$data);

            if($send_notification){
                $notificationData = array(
                    'notification_url'          => 'programs_form/'.$program_id,
                    'notification_type'         =>  'integration_tools',
                    'notification_title'        =>  __('admin.vendor_program_status_change_to_0'),
                    'notification_viewfor'      =>  'admin',
                    'notification_actionID'     =>  $program_id,
                    'notification_description'  =>  $data['name'].' '.__('admin.program_status_change_to_in_review_by').' '. $user['username'] .' '.__('admin.in_store_on').' '.date('Y-m-d H:i:s'),
                    'notification_is_read'      =>  '0',
                    'notification_created_date' =>  date('Y-m-d H:i:s'),
                    'notification_ipaddress'    =>  $_SERVER['REMOTE_ADDR']
                );  

                $this->db->insert('notification', $notificationData);
            }

            $this->sendMail('admin',$_subject,$body);
        } else {
            $body = $this->parseTemplate($template['text'],$template['shortcode'],$data);
            $_subject = $this->parseTemplate($template['subject'],$template['shortcode'],$data);

            if($send_notification){
                $notificationData = array(
                    'notification_url'          => 'programs_form/'.$program_id,
                    'notification_type'         =>  'integration_tools',
                    'notification_title'        =>  __('user.vendor_program_status_change_to_' . $data['status']),
                    'notification_view_user_id' =>  $data['vendor_id'],
                    'notification_viewfor'      =>  'user',
                    'notification_actionID'     =>  $program_id,
                    'notification_description'  =>  __('admin.your_program_status_changed_admin_review_changes').' '.date('Y-m-d H:i:s'),
                    'notification_is_read'      =>  '0',
                    'notification_created_date' =>  date('Y-m-d H:i:s'),
                    'notification_ipaddress'    =>  $_SERVER['REMOTE_ADDR']
                ); 

                $this->db->insert('notification', $notificationData);
            }

            $this->sendMail($data['email'],$_subject,$body);
        }
    }

    public function vendor_create_product($product_id){
        $this->load->model('Product_model');

        $data = (array)$this->db->query("SELECT * FROM product WHERE product_id =". (int)$product_id)->row_array();
        $seller = $this->Product_model->getSellerFromProduct($product_id);
        $user = (array)$this->Product_model->getUserDetails($seller->user_id);
        $data = array_merge($data,$user);
        $data['mob'] = $this->mobile_number;

        $template = $this->getTemplateByID(0,'vendor_create_product');   
        $body = $this->parseTemplate($template['admin_text'],$template['shortcode'],$data);
        $_subject = $this->parseTemplate($template['admin_subject'],$template['shortcode'],$data);

        $this->sendMail('admin',$_subject,$body);

        return true;
    }

    public function vendor_product_status_change($product_id, $mail_for = 'admin', $send_notification = false){
        $this->load->model('Product_model');

        $data = (array)$this->db->query("SELECT * FROM product WHERE product_id =". (int)$product_id)->row_array();
        $seller = $this->Product_model->getSellerFromProduct($product_id);
        $user = (array)$this->Product_model->getUserDetails($seller->user_id);
        $data = array_merge($data,$user);
        $data['mob'] = $this->mobile_number;

        $comment = (array)json_decode($seller->comment,1);
        $vendor_last_message = '';
        $admin_last_message = '';

        foreach ($comment as $com) {
            if($com['from'] == 'affiliate') $vendor_last_message = $com['comment'];
            if($com['from'] == 'admin') $admin_last_message = $com['comment'];
        }
        
        $data['vendor_last_message'] = $vendor_last_message;
        $data['admin_last_message'] = $admin_last_message;
       
        $template = $this->getTemplateByID(0,'vendor_product_status_'. $data['product_status']);
        if($mail_for == 'admin'){
            $body = $this->parseTemplate($template['admin_text'],$template['shortcode'],$data);
            $_subject = $this->parseTemplate($template['admin_subject'],$template['shortcode'],$data);

            if($send_notification){
                $notificationData = array(
                    'notification_url'          => 'updateproduct/'.$product_id,
                    'notification_type'         =>  'vendor_product',
                    'notification_title'        =>  __('admin.product_status_change_to_0'),
                    'notification_viewfor'      =>  'admin',
                    'notification_actionID'     =>  $product_id,
                    'notification_description'  =>  $data['product_name'].' '.__('admin.product_is_addded_by').' '. $user['username'] .' '.__('admin.in_store_on').' '.date('Y-m-d H:i:s'),
                    'notification_is_read'      =>  '0',
                    'notification_created_date' =>  date('Y-m-d H:i:s'),
                    'notification_ipaddress'    =>  $_SERVER['REMOTE_ADDR']
                );  

                $this->db->insert('notification', $notificationData);
            }

            $this->sendMail('admin',$_subject,$body);
        }
        else {
            $body = $this->parseTemplate($template['text'],$template['shortcode'],$data);
            $_subject = $this->parseTemplate($template['subject'],$template['shortcode'],$data);

            if($send_notification){
                $notificationData = array(
                    'notification_url'          => 'store_edit_product/'.$product_id,
                    'notification_type'         =>  'vendor_product',
                    'notification_title'        =>  __('user.product_status_change_to_' . $data['product_status']),
                    'notification_view_user_id' =>  $seller->user_id,
                    'notification_viewfor'      =>  'user',
                    'notification_actionID'     =>  $product_id,
                    'notification_description'  =>  __('admin.your_product_status_changed_admin_review_changes').' '.date('Y-m-d H:i:s'),
                    'notification_is_read'      =>  '0',
                    'notification_created_date' =>  date('Y-m-d H:i:s'),
                    'notification_ipaddress'    =>  $_SERVER['REMOTE_ADDR']
                ); 

                $this->db->insert('notification', $notificationData);
            }

            $this->sendMail($user['email'],$_subject,$body);
        }

        return true;
    }

    private function getTemplateByID($template_id, $unique_id = false) {
        if($unique_id){
            $template = $this->db->query("SELECT * FROM mail_templates WHERE unique_id = '". $unique_id ."'")->row_array();
        } else {
            $template = $this->db->query("SELECT * FROM mail_templates WHERE id = ". $template_id)->row_array();
        }


        if(!$template){
            require APPPATH . '/config/mail_templates.php';
        }

        return $template;
    }

    private function parseTemplate($body, $shortcode , $data = array()) {
        $this->load->model('Product_model');
         
        if(!is_array($shortcode))       // added new code for php 8.0
        $shortcode = explode(",", $shortcode);

        $setting = $this->Product_model->getSettings('site');
        $emailsetting = $this->Product_model->getSettings('emailsetting');

        $data['website_name'] = $setting['name'];
        
        if($emailsetting['logo']){
           
            $data['website_logo'] = "<img src='".base_url("assets/images/site/".$emailsetting['logo'])."' >";
        }else{
            $data['website_logo'] = base_url('assets/images/no_image_available.png');
        }

        foreach ($shortcode as $key => $value) {
            if(isset($data[$value])){
               
                $body = str_replace("[[". $value ."]]", $data[$value], $body);
            }
        }
         
        return $body;
    }
    
    private function parseStoreTemplate($body, $shortcode , $data = array()) {
        $this->load->model('Product_model');
        $shortcode = explode(",", $shortcode);
        $setting = $this->Product_model->getSettings('store');
        $data['website_name'] = $setting['name'];
        
        $logo = base_url(trim($setting['logo']) ? 'assets/images/site/'.$setting['logo'] : 'assets/vertical/assets/images/users/avatar-1.jpg');

        $data['website_logo'] = "<img src='". $logo ."' >";
        foreach ($shortcode as $key => $value) {
            if(isset($data[$value])){
                $body = str_replace("[[". $value ."]]", $data[$value], $body);
            }
        }
        
        return $body;
    }
    public function send_subscription_expire_notification($membership_id, $plan_id){
        $template = $this->getTemplateByID(0,'subscription_expire_notification');
        $data['buy'] = App\MembershipUser::find($plan_id);
        $user = App\User::find($data['buy']->user_id);
        $data['mob']       = $this->mobile_number;
        $data['orderLink'] = base_url('membership/membership_purchase_edit/'. $plan_id);
        $data['planname'] = ($data['buy']->plan ? $data['buy']->plan->name : '');
        $data['price'] = ($data['buy']->plan ? ($data['buy']->plan->price > 0 ? c_format($data['buy']->plan->price) : 'Free') : 'Free');
        $data['expire_at'] = $data['buy']->expire_at;
        $data['started_at'] = $data['buy']->started_at;
        $data['firstname'] = $user->firstname;
        $data['lastname'] = $user->lastname;
        $data['email'] = $user->email;
        $data['username'] = $user->username;
        $data['mail_templates'] = $this->parseTemplate($template['text'],$template['shortcode'],$data);
        $body = $this->load->view('mails/subscription_expire_notification',$data, true);
        $template['subject'] = $this->parseTemplate($template['subject'],$template['shortcode'],$data);
        $this->sendMail($user->email,$template['subject'],$body);
        $data['mail_templates'] = $this->parseTemplate($template['admin_text'],$template['shortcode'],$data);
        $body = $this->load->view('mails/subscription_expire_notification',$data, true);
        $template['subject'] = $this->parseTemplate($template['admin_subject'],$template['shortcode'],$data);
        $this->sendMail('admin',$template['subject'],$body);        
        $this->load->model('Product_model');
        $notificationData = array(
            'notification_url'          => '/dashboard',
            'notification_type'         =>  'package_expire_notification',
            'notification_title'        =>  __('admin.your_package_will_be_expired_in_7_days'),
            'notification_viewfor'      =>  'user',
            'notification_view_user_id' =>  $user['id'],
            'notification_actionID'     =>  0,
            'notification_description'  =>  __('admin.your_package_will_be_expired_in_7_days'),
            'notification_is_read'      =>  '0',
            'notification_created_date' =>  date('Y-m-d H:i:s'),
            'notification_ipaddress'    =>  $_SERVER['REMOTE_ADDR']
        );
        $this->Product_model->create_data('notification', $notificationData);
        $this->db->query("UPDATE membership_user SET expire_mail_sent = 1 WHERE id = '".$membership_id."'");
    }

    public function user_level_changed($id,$email,$from_level,$to_level){
        $data['from_level'] = $from_level;
        $data['to_level'] = $to_level;
        $data['mob'] = $this->mobile_number;

        $template = $this->getTemplateByID(0,'user_level_changed');   
        $body = $this->parseTemplate($template['text'],$template['shortcode'],$data);
        $_subject = $this->parseTemplate($template['subject'],$template['shortcode'],$data);

        $this->sendMail($email,$_subject,$body);

        $this->load->model('Product_model');
        $notificationData = array(
            'notification_url'          => '/dashboard',
            'notification_type'         =>  'user_level_changed',
            'notification_title'        =>  __('admin.your_level_changed_from')." $from_level to $to_level",
            'notification_viewfor'      =>  'user',
            'notification_view_user_id' =>  $id,
            'notification_actionID'     =>  0,
            'notification_description'  =>  __('admin.your_level_changed_from')." $from_level to $to_level",
            'notification_is_read'      =>  '0',
            'notification_created_date' =>  date('Y-m-d H:i:s'),
            'notification_ipaddress'    =>  $_SERVER['REMOTE_ADDR']
        );
        $this->Product_model->create_data('notification', $notificationData);

        return true;
    }
    
}