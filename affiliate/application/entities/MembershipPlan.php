<?php
namespace App;
class MembershipPlan extends \Illuminate\Database\Eloquent\Model
{
	public $table = 'membership_plans';
	public $timestamps = false;

	public static $status_list = array(
        '0'  =>  'Pending',
        '1'  =>  'Active',
        '2'  =>  'Total not match',
        '3'  =>  'Denied',
        '4'  =>  'Expired',
        '5'  =>  'Failed',
        '7'  =>  'Processed',
        '8'  =>  'Refunded',
    );

	public static $lang_status_list = array(
        '0'  =>  'pending',
        '1'  =>  'active',
        '2'  =>  'total_not_match',
        '3'  =>  'denied',
        '4'  =>  'expired',
        '5'  =>  'failed',
        '7'  =>  'processed',
        '8'  =>  'refunded',
    );

    public static $status_color = array(
        '0'  =>  'text-warning',
        '1'  =>  'text-success',
        '2'  =>  'text-danger',
        '3'  =>  'text-danger',
        '4'  =>  'text-warning',
        '5'  =>  'text-danger',
        '7'  =>  'text-info',
        '8'  =>  'text-primary',
    );

    public static function getStatusLable($id)
    {
    	return self::$lang_status_list[$id];
    }

    public static function senBuyMail($buy_id,$ci)
    {
    	$ci->load->model('Mail_model');
		$ci->Mail_model->send_subscription_buy($buy_id);
    }

	public function buy($user, $status_id, $comment = '', $payment_method = '', $notify = 1, $payment_details = array(),$plan=array()){
		
		$checkMembership = MembershipUser::where(array('plan_id'=>$this->id, 'user_id'=> $user->id))->first();

		$addPreviousDays=0;
		if(isset($user) && isset($user->plan_id) && $user->plan_id>0)
		{
			$ci =& get_instance(); 
			$where=array('id'=>$user->plan_id);
			$plandetail=$ci->Common_model->select_where_result('membership_user',$where);
			
			if(isset($plandetail) && count($plandetail)>0 && $plandetail['is_active']==1 && $plandetail['status_id']==1)
			{
				$diff = $ci->Common_model->common_datedifference($plandetail['expire_at']);
				$addPreviousDays=$diff->days;
				if($addPreviousDays<0)
					$addPreviousDays=0; 
			} 
		}

		$addDays=0;

		if($this->have_trail>0 && $this->free_trail>0)
			$addDays=$this->total_day+$this->free_trail;
		else
			$addDays=$this->total_day;

		$addDays = $addDays + $addPreviousDays;
		
		$membership_user = new MembershipUser();
		$membership_user->plan_id = $this->id;
		$membership_user->user_id = $user->id;
		$membership_user->total_day = $addDays;
		if($this->billing_period != 'lifetime_free')
		$membership_user->expire_at = date("Y-m-d H:i:s",strtotime('+ '. $addDays .' '.__('user.days'))); // also chnage $addDays
		


		$membership_user->started_at = date("Y-m-d H:i:s");
		$membership_user->status_id = $status_id;
		$membership_user->is_active = 1;
		$membership_user->is_lifetime = $this->billing_period == 'lifetime_free' ? 1 : 0;
		$membership_user->payment_method = $payment_method;
	
		$membership_user->payment_details = json_encode($payment_details);
	
		$membership_user->total = ($this->special ? $this->special : $this->price);
	
		$membership_user->bonus_commission = (float)$this->bonus;
		$membership_user->created_at = date("Y-m-d H:i:s");

		MembershipUser::where('user_id', $user->id)->update(['is_active'=>0]);

		$membership_user->save();

		$user->plan_id = $membership_user->id;

		if($plan->level_id){
		$user->level_id = $plan->level_id;
		}
		
		$user->save();

		$history = new MembershipHistory();
		$history->buy_id = $membership_user->id;
		$history->status_id = $status_id;
		$history->comment = $comment;
		$history->created_at = date("Y-m-d H:i:s");
		$history->save();

		if ($notify) {
			$cdate = date('Y-m-d H:i:s');
			$notification = new Notification();
			$notification->notification_url          = '/membership/membership_purchase_edit/'.$membership_user->id;
			$notification->notification_type         =  'membership_order';
			$notification->notification_title        =  'New Subscription Buy From '.$user->username;
			$notification->notification_viewfor      =  'admin';
			$notification->notification_actionID     =  $membership_user->id;
			$notification->notification_description  =  $user->firstname.' '.$user->lastname.' buy a new subscription at affiliate program on '.$cdate;
			$notification->notification_is_read      =  '0';
			$notification->notification_created_date =  $cdate;
			$notification->notification_ipaddress    =  $_SERVER['REMOTE_ADDR'];
			$notification->save();
		}

		if (!empty($checkMembership)) {

		}else{
			if((float)$this->bonus > 0 && $status_id == 1){
				$ci =& get_instance(); 
				$ci->Wallet_model->addTransaction(array(
					'status'       => 1,
					'user_id'      => (int)$membership_user->user_id,
					'amount'       => (float)$this->bonus,
					'comment'      => 'Membership plan Bonus',
					'type'         => 'membership_plan_bonus',
					'comm_from'    => 'membership',
					'reference_id' => $membership_user->id,
					'group_id'     => time().rand(10,100),
					'is_vendor'    => 0,
				));
			}
		}

		if ($notify) {
			$ci =& get_instance(); 
			MembershipPlan::senBuyMail($membership_user->id,$ci);
		}
	

		return $membership_user;
	}

	public function getBillingPeriodTextAttribute()
    {

    	if($this->billing_period == 'custom'){
    		if($this->custom_period == 7){
    			return 'Per Week';
    		}
    		else if($this->custom_period == 30){
    			return 'Per Month';
    		}
    		else {
				return 'Per '. $this->custom_period ." Days";
    		}
    	}

        return 'Per '. ucfirst(str_replace("_", " ", $this->billing_period));
    }

    public function getBillingPeriodPlainAttribute()
    {

    	if(strtolower($this->billing_period) == 'lifetime_free'){
    		return 'Lifetime';
    	}
    	else
    	{
        	return ucfirst($this->billing_period);
    	}

    }

    public static function getPaymentMethods($filter = array()){
    	$ci =& get_instance();
    	
		$files = array();
		foreach (glob(APPPATH."/payment_gateway/controllers/*.php") as $file)
		  	$files[] = $file;

		$allPaymentGateways = array_unique($files);
		$activePaymentGateways = [];
		$defaultPaymntGateway = [];
		foreach($allPaymentGateways as $key => $filename){
			if(!str_contains($filename,'cod.php')){
				$paymentGateway = basename($filename,".php");

				$result = $ci->Product_model->getSettings('payment_gateway_membership_'.$paymentGateway,'status');
				$install = $ci->Product_model->getSettings('payment_gateway_'.$paymentGateway,'is_install');
				if(isset($result['status']) && $result['status'] && $install['is_install']){
					require $filename;

					$object = new $paymentGateway($ci);

					$activePaymentGateways[$paymentGateway] = $ci->Product_model->getSettings('payment_gateway_'.$paymentGateway);
					
					$activePaymentGateways[$paymentGateway]['title'] = $object->title;
					$activePaymentGateways[$paymentGateway]['icon'] = $object->icon;
					$activePaymentGateways[$paymentGateway]['name']  = $paymentGateway;
					
					$where = array('setting_key'=>'status','setting_type'=>'payment_gateway_membership_'.$paymentGateway,'setting_is_default'=>1);
					$is_default = $ci->Common_model->get_total_rows('setting',$where);
					if($is_default){
						$defaultPaymntGateway[$paymentGateway] = $activePaymentGateways[$paymentGateway];
						unset($activePaymentGateways[$paymentGateway]);
					}
				}
			}
		}

		$payment_gateways = array_merge($defaultPaymntGateway,$activePaymentGateways);

		$ci->session->set_userdata('payment_gateways',$payment_gateways);

		return $payment_gateways;
	}
}