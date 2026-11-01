<?php
namespace App;
class User extends \Illuminate\Database\Eloquent\Model
{
	public $timestamps = false;

	public static function auth($type = 'user'){
		$CI =& get_instance();
		$user = false;

		$userdetails = $CI->session->userdata($type);
		if (isset($userdetails['id'])) {
			$user = self::find((int)$userdetails['id']);
		}

		return $user;
	}

	public function plan(){
		return MembershipUser::find($this->plan_id);
	}

	public static function getLoginUrl(){
		$getSetting = Setting::where(array('setting_key'=>'admin_url', 'setting_type'=>'security'))->first();

		if($getSetting){
            return $getSetting->setting_value;
        }else{
            return "";
        }
	}

	public static function getFrontUrl(){
		$getSetting = Setting::where(array('setting_key'=>'front_url', 'setting_type'=>'security'))->first();
		
        if($getSetting){
            return $getSetting->setting_value;
        }else{
            return "";
        }
	}

	public static function getActiveTheme(){
		$getSetting = Setting::where(array('setting_key'=>'store_mode', 'setting_type'=>'store'))->first();

		if($getSetting){
            return $getSetting->setting_value;
        }else{
            return "cart";
        }
	}

	public static function getStoreStatus(){
		$getSetting = Setting::where(array('setting_key'=>'status', 'setting_type'=>'store'))->first();

		if($getSetting){
            return $getSetting->setting_value;
        }else{
            return "";
        }
	}
}