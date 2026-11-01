<?php

class UserProfile {

    public $data;

    function __construct($data) {
        $this->data = $data;
    }


	public function status() {

      extract($this->data);

      $db =& get_instance();
      
      $MembershipSetting = $db->Product_model->getSettings('membership');

      if($userdetails['reg_approved'] != 1){
         return 'user-approval-pending';
      }

      if($MembershipSetting['status']){
         
         $user = App\User::find($userdetails['id']);

         if((int)$user->plan_id == 0){
         
           return 'membership-not-purchased';
         
         } else if($user && $user->plan_id != -1){
           
            $plan = $user->plan();


            if(!$plan){

               return 'membership-not-purchased';

            } else if ((int)$plan->status_id !== 1) {

               $membershipPlanStatus = new App\MembershipPlan();

               return 'membership-status-'.$membershipPlanStatus->getStatusLable($plan->status_id);

            } else if ($plan->isExpire() || !$plan->strToTimeRemains() > 0){

             $lifetime = ($plan->is_lifetime && $plan->status_id) ? true : false;

               if(!$lifetime){
                  return 'membership-expired';
               }
            }
         }
      }

      return 'ok';
   }
}

