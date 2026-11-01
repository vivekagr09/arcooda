<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

use App\User;
use App\MembershipPlan;
use App\MembershipUser;
class Membership extends MY_Controller {
	function __construct() {
		parent::__construct();
		___construct(1);
		$this->load->model('Product_model');
		$this->load->model('membership_model');
		$this->load->model('Tutorial_model');
		$this->Product_model->ping($this->session->userdata('administrator')['id']);
		$this->checkSessionTimeout();
	}

	public function user_plan_modal(){
		$data = [];
		$this->checkLogin('admin');
		$user_id = $this->input->get("user_id");
		$is_vendor = $this->input->get("is_vendor");

		$data['MembershipSetting'] =$this->Product_model->getSettings('membership');
	


		if ($this->input->server('REQUEST_METHOD') == 'POST'){
			$post = $this->input->post(null,true);
			$json = array();

			$json = [];
			$this->form_validation->set_rules('user_id', 'User', 'required|trim');
			$this->form_validation->set_rules('new_planid', 'Plan', 'required|trim');
			$this->form_validation->set_rules('status_id', 'Status', 'required|trim');
			$this->form_validation->set_rules('comment', 'Comment', 'required|trim');
		

			if ($this->form_validation->run() == FALSE) {
				$json['errors'] = $this->form_validation->error_array();
			}

			if (!isset($json['errors'])) {
				$user = App\User::find($post['user_id']);
				$plan = MembershipPlan::find($post['new_planid']);
				
				$membership = $plan->buy($user,$post['status_id'], $post['comment'],'Free by Admin',0,'',$plan);

				// set default plan acive if refund given
				if((int)$post['status_id'] == 8) {
					$this->setDefaultPlan($post['user_id']) ;
				}

				$json['reload']=1;
				$this->session_message("Success: {$plan->name} assign to {$user->firstname} {$user->lastname}");
			}

			$this->json($json);die;
		}

		$user = App\User::find($user_id);
	    if((int)$user->plan_id == 0){ }
	    else if($user->plan_id == -1){
	    	$data['is_lifetime_plan'] = 1;
	    } else if ($user) {
	      $plan = $user->plan();
	      if($plan){
	       	$data['plan']  = $plan;
	      }
	    }

	    $data['user'] = $user;
	    $data['plan_lists'] = MembershipPlan::get();
		$data['is_vendor'] = $is_vendor;
		$this->load->view('admincontrol/membership/user_plan_modal',$data);
	}

	public function plan_edit($plan_id = 0){
		$data = [];
		$this->checkLogin('admin');
		
		$data['plan'] = MembershipPlan::findOrNew((int)$plan_id);
		$data['award_level'] = $this->Product_model->getSettings('award_level','status');
		$data['levels'] = $this->Product_model->getAll('award_level',false,0,'id desc');
		$data['CurrencySymbol'] = $this->currency->getSymbol();

		$this->view($data,'membership/plan_edit');
	}

	public function plan_create($plan_id = 0){
		$data = [];
		$this->checkLogin('admin');

		$data['award_level'] = $this->Product_model->getSettings('award_level','status');
		$data['levels'] = $this->Product_model->getAll('award_level',false,0,'id desc');
		$data['CurrencySymbol'] = $this->currency->getSymbol();

		$this->view($data,'membership/plan_create');
	}

	public function odrer_plan_delete($plan_id = 0){
		$data = [];
		$this->checkLogin('admin');
		$plan = MembershipUser::find((int)$plan_id);
		if($plan && $plan->is_active == 0){
			App\MembershipHistory::where('buy_id', $plan_id)->delete();
			$plan->delete();
			$this->session_message("Success: Membership Order Deleted Successfully");
		}

		redirect('membership/membership_orders');
	}

	public function odrer_plan_delete_multiple(){
		$data = [];
		$this->checkLogin('admin');
		$post = $this->input->post(null,true);

		if (isset($post['delete'])) {
			foreach ($post['delete'] as $key => $plan_id) {
				$plan = MembershipUser::find((int)$plan_id);
				if($plan && $plan->is_active == 0){
					App\MembershipHistory::where('buy_id', $plan_id)->delete();
					$plan->delete();
				}
			}
		}
		$this->session_message("Success: Membership Order Deleted Successfully");
		redirect('membership/membership_orders');
	}

	public function plan_delete($plan_id = 0){
		$data = [];
		$this->checkLogin('admin');

		$plan = MembershipPlan::findOrNew((int)$plan_id);

		$activeUser = MembershipUser::where('is_active',1)->where("plan_id",$plan->id)->count();
		if($activeUser > 0){
			$this->session_message("Warning: This plan cannot be deleted as it is currently assigned to {$activeUser} users!",'error');
		} else{
			$plan->delete();
			$this->session_message("Plan deleted successfully..");
		}

		redirect('membership/plans');
	}

	public function plans($page=1){
		$data = [];
		$this->checkLogin('admin');
		$page = max((int)$page,1);

		\Illuminate\Pagination\Paginator::currentPageResolver(function () use ($page) {
	        return $page;
	    });
	

		$limit = 10;
		$query = MembershipPlan::select('membership_plans.*','award_level.level_number as plan_level')->join('award_level','award_level.id','=','membership_plans.level_id','left')->paginate($limit);
		$data['links'] = $this->build_paginate($query, 'membership/plans',$page, $limit);

		$data['plans'] = $query;
		$data['award_level'] = $this->Product_model->getSettings('award_level','status');
		$data['license_alret'] = $this->getRegularLicenseAlert();
		$this->view($data,'membership/plans');
	}

	public function settings($plan_id = 0){
		$this->checkLogin('admin');

		$post = $this->input->post(null,true);
		if(!empty($post)){
			$json = array();

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

				$commonSetting = ['membership'];
				foreach ($post as $key => $value) {
					if (in_array($key, $commonSetting)) {

						$this->Setting_model->save($key, $value);

						if(isset($post['membership']['status']) && (int)$post['membership']['status'] > 0) {
							$this->assignDefaultPlans($post['membership']);
						}
					}
				}

				if(!isset($json['errors'])){

					$json['success'] =  __('admin.setting_saved_successfully');
				}
			}
	

			$this->json($json);die;
		}

		$data['membership'] = $this->Product_model->getSettings('membership');
		$data['plans'] = MembershipPlan::get();
		$data['license_alret'] = $this->getRegularLicenseAlert();
		$this->view($data,'membership/settings');
	}

	public function assignDefaultPlans($membership) {
		switch ((int)$membership['status']) {
			case 1:
				$users = $this->db->query('SELECT id, is_vendor FROM users WHERE (plan_id=-1 or plan_id=0) and type="user" and status=1 and reg_approved=1')->result_array();
				break;
			case 2:
				$users = $this->db->query('SELECT id, is_vendor FROM users WHERE (plan_id=-1 or plan_id=0) and type="user" and status=1 and reg_approved=1 and is_vendor=1')->result_array();				break;
			case 3:
				$users = $this->db->query('SELECT id, is_vendor FROM users WHERE (plan_id=-1 or plan_id=0) and type="user" and status=1 and reg_approved=1 and is_vendor=0')->result_array();
				break;
			default:
				$users = [];
				break;
		}
		
		foreach($users as $u) {
			$user = App\User::find($u['id']);
			if((int)$u['is_vendor'] == 1) {
    			$plan_id = $membership['default_vendor_plan_id'] ?? $membership['default_plan_id'];
    		} else {
    			$plan_id = $membership['default_affiliate_plan_id'] ?? $membership['default_plan_id'];
    		}

    		if($plan_id > 0){
    			$plan = App\MembershipPlan::find($plan_id);
        		$plan->buy($user, 1, 'Default plan started','Default');
	    	}
		}
	}

	public function upload_plan_icon(){
		if (isset($_FILES['plan_icon']) && $_FILES['plan_icon']['name'] != "") {
			$icon_name = md5(rand(1000, 10000000));
            if(move_uploaded_file($_FILES['plan_icon']['tmp_name'], 'assets/login/multiple_pages/img/'.$icon_name.'.jpg')){
            	echo $icon_name.'.jpg';
            }else{
            	echo 'default.jpg';
            }
        }
	}

	public function submit_plan_form($plan_id = 0){

		$data = $_POST;
		$this->checkLogin('admin');
		$json = [];
		$this->form_validation->set_rules('name', 'Name', 'required|trim');
		$this->form_validation->set_rules('type', 'Type', 'required|trim');
		$this->form_validation->set_rules('status', 'Plan Status', 'required|trim');
		
		$this->form_validation->set_rules('billing_period', 'Billing Period', 'required|trim');
		$this->form_validation->set_rules('user_type', 'User type', 'required|trim');

		if (isset($data['type']) && $data['type'] =='paid') {
			$this->form_validation->set_rules('price', 'Price', 'required|trim');
			$this->form_validation->set_rules('have_trail', 'Free Trail', 'required|trim');
		}
		if (isset($data['billing_period']) && $data['billing_period'] =='custom') {
			$this->form_validation->set_rules('custom_period', 'Custom Period', 'required|trim|greater_than[0]');
		}
		if (isset($data['have_trail']) && $data['have_trail'] =='1') {
			$this->form_validation->set_rules('free_trail', 'Free Trail Day', 'required|trim');
		}
	 
		if ($this->form_validation->run() == FALSE) {
			$json['errors'] = $this->form_validation->error_array();
		}
 
		if (!isset($json['errors'])) 
		{
			$plan = MembershipPlan::findOrNew((int)$data['id']);

			if (isset($plan->type) && $plan->type=="paid" && $data['type']=="free") {
				$this->session->set_flashdata('error', __('admin.paid_plan_cant_change_to_free_plan'));
				$json['location'] = base_url('membership/plan_edit/'. $plan->id);
			}
			else
			{
				$plan->name = $data['name'];
				$plan->type = $data['type'];
				$plan->billing_period = $data['billing_period'];
				$plan->description = $data['description'];
			
				$plan->label_text = $data['label_text'];
			
				$plan->label_background = $data['label_background'];
			
				$plan->label_color = $data['label_color'];
				$plan->price = (float)$data['price'];
			
				$plan->special = (float)$data['special'];
				$plan->bonus = (float)$data['bonus'];

				$award_level = $this->Product_model->getSettings('award_level', 'status');
				if($award_level['status']){
					$plan->commission_sale_status = $data['commission_sale_status'];
					$plan->level_id = $data['plan_level'];
				}

				$plan->custom_period = (int)$data['custom_period'];
				$plan->status = (int)$data['status'];
				$plan->user_type = $data['user_type'];
				$plan->campaign = ($data['campaign'] || $data['campaign'] != '') ? (int) $data['campaign'] : NULL;
				$plan->product = ($data['product'] || $data['product'] != '') ? (int) $data['product'] : NULL;
				$plan->have_trail = (int)$data['have_trail'];
				$plan->free_trail = (int)$data['free_trail'];
				$plan->plan_icon = $data['plan_icon'] ? $data['plan_icon'] : $plan['plan_icon'];

				if($plan->billing_period == 'daily'){
					$plan->total_day = 1;
				}
				else if($plan->billing_period == 'weekly'){
					$plan->total_day = 7;
				}
				else if($plan->billing_period == 'monthly'){
					$plan->total_day = 30;
				}
				else if($plan->billing_period == 'yearly'){
					$plan->total_day = 365;
				}
				else if($plan->billing_period == 'custom'){
					$plan->total_day = $plan->custom_period;
				}
				$plan->sort_order = (int)$data['sort_order'];

				if($plan->created_at == ''){
					$plan->created_at = date("Y-m-d H:i:s");
				}
				$plan->updated_at = date("Y-m-d H:i:s"); 

				$plan->save();
				$this->session->set_flashdata('success', __('admin.plan_saved_successfully')); 

				if ((int)$this->input->get('close') == 1) {
					$json['location'] = base_url('membership/plans');
				} else {
					$json['location'] = base_url('membership/plan_edit/'. $plan->id);
				}
			}
		}

		$this->json($json);
	}

	public function submit_plan_update($plan_id = 0){
		$data = $this->post_data();
		$this->checkLogin('admin');
		$json = [];

		$this->form_validation->set_rules('expire_at', 'Expire date', 'required|trim');
		if ($this->form_validation->run() == FALSE) {
			$json['errors'] = $this->form_validation->error_array();
		}


		if (!isset($json['errors'])) {
			$plan = MembershipUser::find((int)$plan_id);
			$plan->expire_at = date("Y-m-d", strtotime($data['expire_at']));
			$plan->save();
			$this->session_message("Plan updated successfully..");

			if ((int)$this->input->get('close') == 1) {
				$json['location'] = base_url('membership/plans');
			} else {
				$json['location'] = base_url('membership/membership_purchase_edit/'. $plan->id);
			}
		}

		$this->json($json);
	}

	public function buy_membership($plan_id, $paymentGateway = null){
		if($this->Product_model->isMembershipAccess()){
			if($paymentGateway == 'paystack' && !in_array($this->session->userdata('userCurrency'), ['GHS' , 'NGN', 'USD', 'ZAR']))
				show_404();

			if ($paymentGateway == 'xendit' && $this->session->userdata('userCurrency') != 'IDR' && $this->session->userdata('userCurrency') != 'PHP') {
			    show_404();
			}

			if($paymentGateway == 'yookassa' && $this->session->userdata('userCurrency') != 'RUB')
				show_404();

			$user = User::auth('user');

			$data = ['notcheckmember'=>1];
			$data['plan'] = MembershipPlan::find($plan_id);
			$paymentGateways = $this->session->userdata('payment_gateways');
			if($data['plan'] && $paymentGateways[$paymentGateway]){

				if($data['plan']->type == 'free' && $data['plan']->special == 0 && $data['plan']->price == 0){
					$data['confirm'] = '<form action="'.base_url('membership/activateFreePlan/'.$data['plan']->id).'" method="post" enctype="multipart/form-data"><div class="text-center"><button type="submit" class="btn btn-primary">Activate Now</button></div></form>';
				} else if($user->is_vendor == 0 && $data['plan']->user_type == 2){
					show_404();
				} else {
					require APPPATH."/payment_gateway/controllers/". $paymentGateway .".php";

					$this->session->set_userdata('payment_gateway',$paymentGateway);
					$this->session->set_userdata('plan_id',$data['plan']->id);

					$object = new $paymentGateway($this);

					$settingData = $paymentGateways[$paymentGateway];

					$insert['payment_module'] = 3;
					$insert['content'] = serialize($user).' || '.serialize($data['plan']);
					$insert['user_id'] = $user->id;
					$insert['datetime'] = date('Y-m-d H:i:s');
					$this->db->insert('uncompleted_payment',$insert);
					$uncompleted_id = $this->db->insert_id();
					$this->session->set_userdata('uncompleted_id',$uncompleted_id);

					require APPPATH.'/payment_gateway/module/membership/view.php';
					$gatewayData = prepareDataForView($paymentGateway,$uncompleted_id,$user,$data['plan']);

					ob_start();
					$object->getPaymentGatewayView($settingData,$gatewayData);
					$data['confirm'] = ob_get_clean();
				}

				$this->view($data,'membership/buy_plan','usercontrol');
			} elseif ($data['plan'] && $paymentGateway == '') {
				$data['confirm'] = '<form action="'.base_url('membership/activateFreePlan/'.$data['plan']->id).'" method="post" enctype="multipart/form-data"><div class="text-center"><button type="submit" class="btn btn-primary">Activate Now</button></div></form>';

				$this->view($data,'membership/buy_plan','usercontrol');
			} else {
				show_404();
			}
		} else {
			show_404();
		}
	}

	public function activateFreePlan($plan_id) {
		if($this->Product_model->isMembershipAccess()){
			$plan = MembershipPlan::find($plan_id);
			$user = User::auth('user');
			
			$plan_details = $plan->toArray();

			if($plan && $plan_details['type'] == 'free' && $plan_details['special'] == 0 && $plan_details['price'] == 0) {
				$membership = $plan->buy($user, $status_id = 1, $comment = 'Free Plan Activated', $payment_method='', 1);
				if($membership){
					redirect('usercontrol/membership_purchase_details/'. $membership->id);
					die;
				}
			}
		}
		show_404();
	}

	public function payment_confirmation(){
		$json['success'] = true;

		echo json_encode($json);
		die;
	}

	public function confirm_plan(){
		$json = [];
		
		$paymentGateway = $this->session->userdata('payment_gateway');
		$paymentGateways = $this->session->userdata('payment_gateways');

		if($paymentGateways && isset($paymentGateways[$paymentGateway])){
			$payment_details = [];
			if($paymentGateway == 'bank_transfer') {
				if(isset($_FILES['payment_proof']) && !empty($_FILES['payment_proof']['name'])) {
					$fileNameArray = explode('.', $_FILES['payment_proof']['name']);
					$config['upload_path'] = 'assets/user_upload';
					$config['allowed_types'] = 'png|gif|jpeg|jpg|PNG|GIF|JPEG|JPG|ICO|ico|pdf|docx|doc|ppt|xls|txt';
					$config['max_size']      = 2048;
					
					$this->load->helper('string');
					$config['file_name'] = random_string('alnum', 32);
					
					$this->load->library('upload',$config);
					$this->upload->initialize($config);

					if(!$this->upload->do_upload('payment_proof'))
						$json['error'] = $this->api->upload->display_errors();
					else
						$payment_details = ['payment_proof' => $config['file_name'].'.'.$fileNameArray[sizeof($fileNameArray)-1]];
				}
			}
			
			if(!isset($json['error']) || empty($json['error'])){
				$uncompletedData = $this->Product_model->getByField('uncompleted_payment','id',$this->session->userdata('uncompleted_id'));
				$content = explode(' || ',$uncompletedData['content']);
				$user = unserialize($content[0]);
				$plan = unserialize($content[1]);


				$uncompleted['content'] = serialize($user).' || '.serialize($plan).' || '.serialize($payment_details);
				$this->db->update('uncompleted_payment',$uncompleted,array('id' => $this->session->userdata('uncompleted_id')));

				require APPPATH."/payment_gateway/controllers/". $paymentGateway .".php";

				$object = new $paymentGateway($this);

				$settingData = $paymentGateways[$paymentGateway];


				require APPPATH.'/payment_gateway/module/membership/request.php';
				$gatewayData = prepareDataForRequest($paymentGateway,$this->session->userdata('uncompleted_id'),$user,$plan);


				$json = $object->setPaymentGatewayRequest($settingData,$gatewayData);

			}
		}

		echo json_encode($json);
		die();
	}

	public function paymentGateway($paymentGateway, $method, $uncompleted_id = '', $action = ''){
		if(is_file(APPPATH.'/payment_gateway/controllers/'.$paymentGateway.'.php')){
			require APPPATH.'/payment_gateway/controllers/'.$paymentGateway.'.php';

			$object = new $paymentGateway($this);

			$settingData = $this->Product_model->getSettings('payment_gateway_'.$paymentGateway);

			require APPPATH.'/payment_gateway/module/membership/callback.php';
			$gatewayData = prepareDataForCallback($paymentGateway,$method,$uncompleted_id,$action);

			$object->$method($settingData,$gatewayData);
		}
	}
	
	public function confirmPaymentGateway($uncompleted_id, $status_id, $transaction_id = '', $payment_status = ''){

		$ex = new Exception();
		$trace = $ex->getTrace(); 
		if(!isset($trace[1]['class'])){ return false; }

		$paymentGateway = $trace[1]['class']; 
		$filename = APPPATH."/payment_gateway/controllers/{$paymentGateway}.php";
		require_once $filename;

		$object = new $paymentGateway($this);
		if($object->title){
			$uncompletedData = $this->Product_model->getByField('uncompleted_payment','id',$uncompleted_id);
			$content = explode(' || ',$uncompletedData['content']);
			$user = unserialize($content[0]);
			$plan = unserialize($content[1]);
			$payment_details = unserialize($content[2]);
			
			if(empty($payment_details)){
				$payment_details = array(
										'transaction_id' => $transaction_id,
										'payment_status' => MembershipPlan::$status_list[$status_id],
									);
			}

			$membership = $plan->buy($user,$status_id,$payment_status,__('user.'.$paymentGateway),1,$payment_details,$plan);

			if($membership){
				$uncompleted['completed_id'] = $membership->id;
				$this->db->update('uncompleted_payment',$uncompleted,array('id' => $uncompleted_id));

				$this->session->set_flashdata('success', 'Thank You for Your Purchase, Your Order is now Completed.');
				
				return true;
			}
		}

		return false;
	}

	public function changeUrlAfterSuccessPayment($uncompleted_id){
		$uncompletedData = $this->Product_model->getByField('uncompleted_payment','id',$uncompleted_id);

		$user = $this->checkLogin('user');
		$query = App\MembershipUser::where('id', $uncompletedData['completed_id'])->where('user_id', $user['id'])->first();
		if($query){
			$user = App\User::find(array('id' => $user['id']));

			$refid = (int)$user[0]['refid'];
			if($refid > 0) {
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
					$getSettingsFor = [];
    
		            for ($l=1; $l <= $max_level ; $l++)
		                $getSettingsFor[] = 'referlevel_'. $l;

		            $referlevelSettingsss = $this->Product_model->getSettingsWhereIn($getSettingsFor);
					$transactionsBatch = [];
					for ($l=1; $l <= $max_level ; $l++) { 
						
						if($l == 1) {
							$json['max_level_user'][] = $levelUser = (int)$refid;	
						} else {
							$json['max_level_user'][] = $levelUser = (int)$level['level'.($l-1)];
						}

						$s = $referlevelSettingsss['referlevel_'. $l];
						
						if($s && $levelUser > 0){
							$_giveAmount = 0;
							
							if($referlevelSettings['reg_comission_type'] == 'custom_percentage'){
								if((int) $referlevelSettings['reg_comission_custom_amt'] > 0) {
									$_giveAmount = (($referlevelSettings['reg_comission_custom_amt'] * (float)$s['reg_commission']) / 100);
								}
							} else if($referlevelSettings['reg_comission_type'] == 'fixed'){
								$_giveAmount = (float)$s['reg_commission'];
							} else if($referlevelSettings['reg_comission_type'] == 'percentage'){
								$_giveAmount = (((float)$query->total * (float)$s['reg_commission']) / 100);
							}

							$json['max_level_user']['_giveAmount'] = $_giveAmount;

							if($_giveAmount > 0){
								$aff_user = $this->Wallet_model->getUserPlan($levelUser);
								if ($aff_user[0]['status_id'] == '1') {
									$transactionsBatch[] = array(
										'status'       => ($query->status_id == 1) ? 1 : 0,
										'user_id'      => $levelUser,
										'amount'       => $_giveAmount,
										'dis_type'     => '',
										'comment'      => "Level {$l} : ".'Commission for new affiliate registrion Id ='. $user[0]['id'] .' | Name : '. $user[0]['firstname'] ." " .$user[0]['lastname'],
										'type'         => 'refer_registration_commission',
										'comm_from'    => 'membership',
										//'reference_id' => $user[0]['id'],
										'reference_id' => $query->id,
										'group_id' => $comission_group_id,
									);
								}
							}
						}
					}

					if(!empty($transactionsBatch)){
						$this->Wallet_model->addTransactionBatch($transactionsBatch);
					}
				}
			}
		}

		

		redirect('usercontrol/membership_purchase_details/'.$uncompletedData['completed_id']);
	}

	public function membership_purchase_edit($plan_id){
		$this->checkLogin('admin');
		$query = App\MembershipUser::find($plan_id); 
		if($query){
			
			if (isset($_GET['addhistory'])) {
				$json = [];
				$data = $this->post_data();
				$this->form_validation->set_rules('status_id', 'Status', 'required|trim');
				$this->form_validation->set_rules('comment', 'Comment', 'required|trim');

				if ($this->form_validation->run() == FALSE) {
					$json['errors'] = $this->form_validation->error_array();
				}

				if (!isset($json['errors'])) {
					
					$planPurchasedBefore = $this->membership_model->is_plan_activated_before($query->id);	

					$MembershipPlan = $this->db->query('select bonus from membership_plans where id='.$query->plan_id)->row();


					$history = new App\MembershipHistory();
					$history->buy_id = $query->id;
					$history->status_id = $data['status_id'];
					$history->comment = $data['comment'];
					$history->created_at = date("Y-m-d H:i:s");
					$history->save();

					if($data['status_id'] == 1) {
						$this->db->query('UPDATE wallet SET status=1 WHERE reference_id='.$query->id.' AND type="refer_registration_commission" AND status=0');
					}

					if($planPurchasedBefore == 0 && $data['status_id'] == 1) {
						$query->expire_at = ($query->billing_period == 'lifetime_free') ? null : date("Y-m-d H:i:s",strtotime('+ '. $query->total_day .' days'));
						$query->started_at = date("Y-m-d H:i:s");


						if((float)$MembershipPlan->bonus > 0){
							$this->load->model('Wallet_model');
							$this->Wallet_model->addTransaction(array(
								'status'       => 1,
								'user_id'      => (int)$query->user_id,
								'amount'       => (float)$MembershipPlan->bonus,
								'comment'      => 'Membership plan Bonus',
								'type'         => 'membership_plan_bonus',
								'comm_from'    => 'membership',
								'reference_id' => $query->id,
								'group_id'     => time().rand(10,100),
								'is_vendor'    => 0,
							));
						}
					}

					$query->status_id = $history->status_id;

					$payment_details = json_decode($query->payment_details, true);
					$payment_details['payment_status'] = ($data['status_id'] == 1) ? 'Succeeded' : MembershipPlan::$status_list[$history->status_id];
					$query->payment_details = json_encode($payment_details);

					$query->save();

					// set default plan acive if refund given
					if($data['status_id'] == 8) {
						$this->setDefaultPlan($query->user_id) ;
					}

					$this->load->model('Mail_model');
					$this->Mail_model->send_subscription_status_change($plan_id, $history->comment);

					$json['reload'] = 1;
					$this->session_message('History added successfully');
				}
				


				$this->json($json);die;
			}

 
			$data['history'] = $query->status_history();
			$data['plan'] = $query;
			 
			$this->view($data,"membership/purchase_edit");
		} else {
			show_404();
		}
	}

	public function membership_orders($page = 1){
		$user = $this->checkLogin('admin');
		$filter = $this->input->get(null,true);

		$page = max((int)$page,1);
		\Illuminate\Pagination\Paginator::currentPageResolver(function () use ($page) {
	        return $page;
	    });

		$limit = 10;
		$query = App\MembershipUser::with(["plan","user"])->orderBy("id","DESC");

		if (isset($filter['user_id']) && $filter['user_id'] != '') {
			$query->where('user_id', (int)$filter['user_id']);
			$data['user_id'] = (int)$filter['user_id'];
		}

		if (isset($filter['status_id']) && $filter['status_id'] != '') {
			$query->where('status_id', (int)$filter['status_id']);
		}

		if (isset($filter['date'])) {
			if (strpos($filter['date'], ' - ') !== false) {
				list($start_date, $end_date) = explode(" - ", $filter['date']);

				$start_date = date("Y-m-d", strtotime($start_date));
				$end_date = date("Y-m-d", strtotime($end_date));

				$query->whereRaw('DATE(created_at) >= "'. $start_date .'"');
				$query->whereRaw('DATE(created_at) <= "'. $end_date .'"');
			}
		}

		$query = $query->paginate($limit);

		$data['users'] = $this->db->query("SELECT id,CONCAT(firstname,' ',lastname) as name FROM users WHERE type='user'")->result_array();

		$data['links'] = $this->build_paginate($query, 'membership/membership_orders',$page, $limit);
		$data['plans'] = $query;


		$year_ago = date('Y-01-01');
		$month_ago = date('Y-m-d', strtotime('-1 month'));
		$week_ago = date('Y-m-d', strtotime('-7 days'));
	
		$data['dashboard_totals'] = $this->db->query("SELECT 
			(SELECT SUM(total) as total FROM membership_user WHERE created_at >= '".$year_ago."' AND status_id = 1) as year_ago_total_order_amount,
			(SELECT COUNT(id) as total FROM membership_user WHERE created_at >= '".$year_ago."' AND status_id = 1) as year_ago_total_orders,
			(SELECT SUM(total) as total FROM membership_user WHERE created_at >= '".$month_ago."' AND status_id = 1) as month_ago_total_order_amount,
			(SELECT COUNT(id) as total FROM membership_user WHERE created_at >= '".$month_ago."' AND status_id = 1) as month_ago_total_orders,
			(SELECT SUM(total) as total FROM membership_user WHERE created_at >= '".$week_ago."' AND status_id = 1) as week_ago_total_order_amount,
			(SELECT COUNT(id) as total FROM membership_user WHERE created_at >= '".$week_ago."' AND status_id = 1) as week_ago_total_orders,
			(SELECT SUM(total) as total FROM membership_user WHERE status_id = 1) as all_time_total_order_amount,
			(SELECT COUNT(id) as total FROM membership_user) as all_time_total_orders,
			(SELECT SUM(bonus_commission) as total FROM membership_user WHERE created_at >= '".$year_ago."' AND status_id = 1) as year_ago_total_bonus_commission,
			(SELECT SUM(bonus_commission) as total FROM membership_user WHERE created_at >= '".$month_ago."' AND status_id = 1) as month_ago_total_bonus_commission,
			(SELECT SUM(bonus_commission) as total FROM membership_user WHERE created_at >= '".$week_ago."' AND status_id = 1) as week_ago_total_bonus_commission,
			(SELECT SUM(bonus_commission) as total FROM membership_user WHERE status_id = 1) as all_time_total_bonus_commission
			FROM membership_user")->row();

		$data['license_alret'] = $this->getRegularLicenseAlert();
		
		$this->view($data,"membership/membership_orders");
	}

	public function getRegularLicenseAlert(){
		list($code,$res) = api('codecanyon/get-details',['licence'=>CODECANYON_LICENCE]);
		if($res['licence']['license'] == "Regular License") {
			return __('admin.regular_license_alert_text');
		} else {
			return "";
		}
	}

	public function setDefaultPlan($user_id)
	{
		$this->checkLogin('admin');
		$user = App\User::find($user_id);
		$is_vendor =  $user->is_vendor;
		$membership = $this->Product_model->getSettings('membership');
		$allMembershipPlans = App\MembershipPlan::all();
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


         if($membership['status'] && $plan_id != -1){
        	if($membership['default_plan_id'] > 0){

        		if($is_vendor == 1) {
        			$plan_id = $membership['default_vendor_plan_id'] ?? $membership['default_plan_id'];
        		} else {
        			$plan_id = $membership['default_affiliate_plan_id'] ?? $membership['default_plan_id'];
        		} 

        		$plan = App\MembershipPlan::find($plan_id);
        		if($plan){
					$plan->buy($user,1, 'Default plan started','Default'); 
            		 
        		}

        	}
        }
	}


	public function checkPendingPlan()
	{
	    $data = array("status" => "false", "plan_id" => "");
	    $user = $this->checkLogin('user');
	    $user = App\User::find(array('id' => $user['id']));
	    
	    if (isset($user) && count($user) > 0) {
	        
	        $checkMembership = MembershipUser::where(array('id' => $user[0]->plan_id))->first();

	        if (!$checkMembership && $user[0]->plan_id == 0) {
	            $data = array("status" => "no_plan");
	        } else {
	            
	            $payment_details = json_decode($checkMembership->payment_details);

	            if ($payment_details != "" && $payment_details->payment_status != "Pending") {
	                $data = array("status" => "true");
	            } else {

	                if ($user[0]->plan_id == -1) {
	                    $data = array("status" => "true", "plan_id" => $user[0]->plan_id);
	                } else {

	                    $plan = MembershipPlan::find($checkMembership->plan_id);
	                    if ($plan) {
	                        $plan_status = ($checkMembership->is_active == 1) ? "true" : "false";
	                        $data = array("status" => $plan_status, "plan_id" => $user[0]->plan_id, "name" => $plan->name, "type" => $plan->type, "price" => $plan->price, "payment_status" => $payment_details->payment_status, "plan_status" => $plan->status, "total_day" => $plan->total_day);
	                    } else {
	                        $data = array("status" => "false", "plan_id" => $user[0]->plan_id);
	                    }
	                }
	            }
	        }
	    }
	    
	    $json = $data;
	    $this->json($json); die;
	}
	
}