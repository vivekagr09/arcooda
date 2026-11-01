<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class CronJob extends MY_Controller{
	function __construct(){
		parent::__construct();	
		___construct(1);	
	}

	public function transaction(){
		$this->load->model('Wallet_model');

		$result = $this->Wallet_model->CronTransaction();

		$cur = date("Y-m-d H:i:s");

		echo $cur."<br>";
	
		$this->db->query("UPDATE wallet_recursion SET status=0 WHERE status=1 AND endtime <= '{$cur}' AND endtime IS NOT NULL AND endtime != '0000-00-00 00:00:00.000000'");			

		echo ($result) ? "Success" : "No Records";
	}

	public function expire_package_notification(){
		echo date("Y-m-d H:i:s")."<br>";
		$this->load->model('Product_model');
		$this->load->model('Mail_model');
		$MembershipSetting =$this->Product_model->getSettings('membership');

		$today = date('Y-m-d');
		$notificationbefore = (int)$MembershipSetting['notificationbefore'];
		$days_after = strtotime("+".$notificationbefore." day");
		$expire_at = date("Y-m-d", $days_after);

		$results = $this->db->query("SELECT * FROM membership_user WHERE is_active = 1 AND expire_mail_sent = 0 AND expire_at = '".$expire_at."'")->result_array();
		foreach ($results as $key => $result) {
			$this->Mail_model->send_subscription_expire_notification($result['id'], $result['plan_id']);
		}

		if($results){
			echo "Success";
		}else{
			echo "No Records";
		}
	}

	
	public function check_campaign_security() {
		$integration_tools = $this->db->query('SELECT * FROM integration_tools WHERE security_check_perform_on != "'.date('d-m-Y').'" OR security_check_perform_on IS NULL LIMIT 10')->result_array();

		foreach ($integration_tools as $tool){
			$security_alerts = external_integration_security_check($tool['target_link']);
			$status = getSecurityStatus($security_alerts,$tool['tool_type'],$tool['tool_integration_plugin'],$tool['program_id']);

			if($tool['security_status'] == 1 && $status == 0)
				$this->db->query('UPDATE integration_tools SET security_status=0 WHERE id='.$tool['id']);

			if($tool['security_status'] == 0 && $status == 1)
				$this->db->query('UPDATE integration_tools SET security_status=1 WHERE id='.$tool['id']);

			$this->db->query('UPDATE integration_tools SET security_check_perform_on="'.date('d-m-Y').'" WHERE id='.$tool['id']);
		}
	}

	
	public function check_campaign_security_all() {
		$integration_tools = $this->db->query('SELECT * FROM integration_tools')->result_array();

		foreach ($integration_tools as $tool){
			$security_alerts = external_integration_security_check($tool['target_link']);
			$status = getSecurityStatus($security_alerts,$tool['tool_type'],$tool['tool_integration_plugin'],$tool['program_id']);
			
			if($tool['security_status'] == 1 && $status == 0)
				$this->db->query('UPDATE integration_tools SET security_status=0 WHERE id='.$tool['id']);

			if($tool['security_status'] == 0 && $status == 1)
				$this->db->query('UPDATE integration_tools SET security_status=1 WHERE id='.$tool['id']);
		}
	}

	public function check_award_level(){
		$sql = 'SELECT `users`.`id`,
                        `users`.`level_id`,
                        `users`.`email`,
                        `award_level`.`level_number`,
                        `award_level`.`jump_level`,
                        `award_level`.`minimum_earning`,
                        `award_level`.`bonus`
                FROM `users`
                INNER JOIN `award_level`
                ON `award_level`.`id` = `users`.`level_id`';

        $query = $this->db->query($sql);
        $users_level = $query->result_array();
        
        foreach($users_level as $key => $value){
        	$this->load->model('Total_model');
            $userBalance = $this->Total_model->getUserBalance($value['id']);

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

            if($value['minimum_earning'] <= $userBalance || ($value['minimum_earning'] > $userBalance && $change_level['id'] != $value['level_id'])){
                
                $change_level_id = ($change_level) ? $change_level['id'] : $value['jump_level'];

                $update['level_id'] = $change_level_id;
                $levelSuccess = $this->db->update('users',$update,['id' => $value['id']]);

                //gives bonuse when jump to level
                if($levelSuccess){
                	if($value['bonus']){
	                    $walletSuccess = $this->Wallet_model->addTransaction(
                            array(
                                'status'         => 1,
                                'user_id'        => $value['id'],
                                'amount'         => $value['bonus'],
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
                        	$sql = 'SELECT `award_level`.`level_number`
					                FROM `award_level`
					                WHERE `award_level`.`id` = ?';

					        $query = $this->db->query($sql,(int) $value['jump_level']);
					        $change_level = $query->row_array();
                            
                            if($change_level)
                                $to_level = $change_level['level_number']; 
                            else
                                $to_level = __('admin.default');
                        }

                        $this->load->model('Mail_model');
                        $this->Mail_model->user_level_changed($value['id'],$value['email'],$value['level_number'],$to_level);
                    } else {
                    	$update['level_id'] = $value['level_id'];
                        $this->db->update('users',$update,['id' => $value['id']]);
                    }
                }
            } 
        }

        return;
	}

	public function check_ven_product_limit($vendor_id = 0){
		$userPlan = App\MembershipUser::with("plan")->where('is_active',1)->where('user_id',$vendor_id)->first();
		$plan_product_count = $userPlan->plan->product;
		$vendor_product_count = $this->Product_model->countByField('product_affiliate','user_id',$vendor_id);

		if ($vendor_id != 0) {
			$productlist = $this->Product_model->getAllVendorProducts($vendor_id, 'vendor');
			$i = 0;
			foreach ($productlist as $product) {
				if ($product['seller_id'] == $vendor_id) {
					$i++;
					if ($i > $plan_product_count) {
						$sql = "UPDATE `product` SET `on_store` = '0', `product_status` = '2' WHERE `product_id` = '".$product['product_id']."'";

						$query = $this->db->query($sql);
					}
				}	
			}	

			return "1";
		}else{
			echo "Vendor ID required!";
		}
	}

	public function check_ven_campaign_limit($vendor_id = 0){
		$this->load->model('IntegrationModel');
		$userPlan = App\MembershipUser::with("plan")->where('is_active',1)->where('user_id',$vendor_id)->first();
		$plan_campaign_count = $userPlan->plan->campaign;
		$vendor_campaign_count = $this->Product_model->countByField('integration_tools','vendor_id',$vendor_id);

		if ($vendor_id != 0) {
			$toollist = $this->IntegrationModel->getVendorProgramTools($vendor_id);
			$i = 0;
			foreach ($toollist as $tool) {
				if ($tool['vendor_id'] == $vendor_id) {
					$i++;
					if ($i > $plan_campaign_count) {
						$sql = "UPDATE `integration_tools` SET `status` = '0' WHERE `id` = '".$tool['id']."'";

						$query = $this->db->query($sql);
					}
				}	
			}

			return "1";
		}else{
			echo "Vendor ID required!";
		}
	}

	public function check_ven_limitation(){
		$this->load->model('IntegrationModel');
		$user_sql = "SELECT * FROM `users` WHERE `is_vendor` = '1'";

		$user_query = $this->db->query($user_sql);
		$vendors = $user_query->result_array();
		
		if (count($vendors) > 0) {
			foreach ($vendors as $vendor) {
				$vendor_id = $vendor['id'];

				$userPlan = App\MembershipUser::with("plan")->where('is_active',1)->where('user_id',$vendor_id)->first();
				$plan_product_count = $userPlan->plan->product;

				if(! empty($plan_product_count)) {
					$vendor_product_count = $this->Product_model->countByField('product_affiliate','user_id',$vendor_id);

					$productlist = $this->Product_model->getAllVendorProducts($vendor_id, 'vendor');

					$i = 0;
					foreach ($productlist as $product) {
						if ($product['seller_id'] == $vendor_id) {
							$i++;
							if ($i > $plan_product_count) {
								$sql = "UPDATE `product` SET `on_store` = '0', `product_status` = '2' WHERE `product_id` = '".$product['product_id']."'";

								$query = $this->db->query($sql);
							}
						}	
					}
				}


				$plan_campaign_count = $userPlan->plan->campaign;

				if(! empty($plan_campaign_count)) {
					$vendor_campaign_count = $this->Product_model->countByField('integration_tools','vendor_id',$vendor_id);

					$toollist = $this->IntegrationModel->getVendorProgramTools($vendor_id);

					$i = 0;
					foreach ($toollist as $tool) {
						if ($tool['vendor_id'] == $vendor_id) {
							$i++;
							if ($i > $plan_campaign_count) {
								$sql = "UPDATE `integration_tools` SET `status` = '0' WHERE `id` = '".$tool['id']."'";

								$query = $this->db->query($sql);
							}
						}	
					}
				}
			}
			echo ($query) ? "Success" : "No Records";
		}else{
			echo "Vendors not found!";
		}
	}
 
	public function autoProcessInWalletTransactions()
	{
		$this->load->model('Mail_model');
			
		
		$sitesetting = $this->Product_model->getSettings('site'); 
		if(isset($sitesetting) && is_array($sitesetting))
		{ 
			if($sitesetting["wallet_auto_withdrawal"]==1)
			{

				$wallet_min_amount=$sitesetting["wallet_min_amount"];
				$wallet_max_amount=$sitesetting["wallet_max_amount"];
				 
				$recordeDaysBeforeTodayDate=$sitesetting["wallet_auto_withdrawal_days"];
				$limit=$sitesetting["wallet_auto_withdrawal_limit"];
				if($limit=="" || $limit==0)
				$limit=300;
				
				$trasactions=$this->Wallet_model->getHoldTransactions($recordeDaysBeforeTodayDate,$limit);
				
				
				if(isset($trasactions) && is_array($trasactions))
				{
					$previoustrans=array();
					$preuserid=0;
					$totalamount=0;
					$ids=array();
					foreach($trasactions as $tran)
					{
						$userdetails = $this->db->query("SELECT * FROM users WHERE id=". (int)$tran['user_id'])->row_array();

						
 						if($preuserid!=0 && $preuserid!=$tran["user_id"])
						{
							
							if($totalamount > 0 && $totalamount>=$wallet_min_amount)
							{
								
								if($wallet_max_amount!="" && $wallet_max_amount>0 && $totalamount>$wallet_max_amount)
								{
									//echo not transfer if total amount grater than maximum amount lmit 
								}
								else
								{

									$code=$previoustrans["primary_payment_method"];
									$setting=$this->Wallet_model->getPrimaryPaymentmethodData($previoustrans) ;
									$commids=implode(",", $ids);
									$request = [
										'tran_ids' => $commids,
										'status' => 0,
										'user_id' => $preuserid,
										'total' => $totalamount,
										'prefer_method' => $code,
										'settings'      => json_encode($setting),
										'created_at' => date("Y-m-d H:i:s"),
									];
									$this->db->query("UPDATE wallet SET  wv='V2',status=2 WHERE id IN (". $commids .") ");
									$this->db->insert("wallet_requests", $request);
									$insert_id = $this->db->insert_id();
									$date = date("Y-m-d H:i:s");
									$this->db->query("INSERT INTO wallet_requests_history SET created_at='{$date}', req_id={$insert_id}, status=0, comment='Your request is received'");
									
									$this->Mail_model->send_wallet_withdrawal_req($request['total'], $userdetails);

									echo "UserId:".$preuserid."  Ids:".$commids." == Done<br/>";
								}
								
							}
							 

							$ids=array();
							$totalamount=0;
							$commids="";
							$previoustrans=array();
							
						}
 			
 						if($tran["amount"]>0)
 						{
 							$ids[]=$tran["id"];
							$totalamount=$totalamount+(float)$tran["amount"];
 						}
						$preuserid=$tran["user_id"];
						$previoustrans=$tran;

					}
 
					if($totalamount > 0 && $totalamount>=$wallet_min_amount)
					{
						$userdetails = $this->db->query("SELECT * FROM users WHERE id=". (int)$preuserid)->row_array();
						if($wallet_max_amount!="" && $wallet_max_amount>0 && $totalamount>$wallet_max_amount)
						{
							//echo not transfer if total amount grater than maximum amount lmit 
						}
						else
						{
							$code=$previoustrans["primary_payment_method"];
							$setting=$this->Wallet_model->getPrimaryPaymentmethodData($previoustrans) ;
							$commids=implode(",", $ids);
							$request = [
								'tran_ids' => $commids,
								'status' => 0,
								'user_id' => $preuserid,
								'total' => $totalamount,
								'prefer_method' => $code,
								'settings'      => json_encode($setting),
								'created_at' => date("Y-m-d H:i:s"),
							];
							
							$this->db->query("UPDATE wallet SET  wv='V2',status=2 WHERE id IN (". $commids .") ");
							$this->db->insert("wallet_requests", $request);
							$insert_id = $this->db->insert_id();
							$date = date("Y-m-d H:i:s");
							$this->db->query("INSERT INTO wallet_requests_history SET created_at='{$date}', req_id={$insert_id}, status=0, comment='Your request is received'");
							$this->Mail_model->send_wallet_withdrawal_req($request['total'], $userdetails);
							echo "UserId:".$preuserid."  Ids:".$commids." == Done<br/>";
						}	
						
					}

				}

			}
		}
		

	}
}