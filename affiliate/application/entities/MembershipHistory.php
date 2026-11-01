<?php
namespace App;
class MembershipHistory extends \Illuminate\Database\Eloquent\Model
{
	public $table = 'membership_buy_history';
	public $timestamps = false;

	public function getStatusTextAttribute(){
		if (isset(MembershipPlan::$status_list[$this->status_id])) {
			return MembershipPlan::$status_list[$this->status_id];
		} else {
			return "Unknown";
		}
	}
}