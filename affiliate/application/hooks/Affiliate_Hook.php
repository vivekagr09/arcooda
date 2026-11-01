<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Affiliate_Hook {
	public function sync_aff_session() {
		$this->CI =& get_instance();

		$data = $this->prepareUserData();

		$loginUser = $this->CI->session->userdata("user");

		if(isset($loginUser) && $loginUser['type'] == 'user') {

			$data['user_id'] = $loginUser['id'];

			$previousLog = $this->CI->db->query('SELECT * FROM affiliate_session_log WHERE user_id='.$loginUser['id'])->row();

			if(isset($previousLog->id)) {

				if(!empty($previousLog->user_agent)) {
					$previousUserAgents = explode(',', $previousLog->user_agent);
				} else {
					$previousUserAgents = [];
				}

				if(!in_array($data['user_agent'], $previousUserAgents)) {
					$previousUserAgents[] = $data['user_agent'];
				}

				if(sizeof($previousUserAgents) > 10) {
					array_slice($previousUserAgents, -10, 10, true);
				}

				$data['user_agent'] = implode(",", $previousUserAgents);

				$this->CI->db->update('affiliate_session_log', $data, array('id' => $previousLog->id));

			} else {

				$this->CI->db->insert('affiliate_session_log', $data);

			}
		}
	}

	public function is_suspicious_click($user_id) {
		$this->CI =& get_instance();

		$userSysDetails = $this->prepareUserData();
		
		$similarSysDetails = $this->CI->db->order_by('time', 'DESC')->get_where('affiliate_session_log', array(
			'user_id' => $user_id,
			'user_ip' => $userSysDetails['user_ip'],
			'user_os' => $userSysDetails['user_os']
		))->row();

		$blockSettigs = $this->CI->db->get_where('setting', array(
			'setting_key' => 'block_click_across_browser',
			'setting_type' => 'site'
		))->row();

		if(isset($similarSysDetails->time)) {

			$similarSysDetails->user_agent = explode(',', $similarSysDetails->user_agent);

			if (in_array($userSysDetails['user_agent'], $similarSysDetails->user_agent)){
				return true;
			} else if(!empty($blockSettigs) && (int)$blockSettigs->setting_value == 1) {
				return true;
			}
		}

		return false;
	}

	public function prepareUserData() {
		return array(
			'user_ip' => $this->get_client_ip(),
			'user_agent' => md5(trim(strtolower($_SERVER['HTTP_USER_AGENT']))),
			'user_os' => $this->getOS(),
			'time' => time(),
		);
	}

	function getOS() { 

		$user_agent = $_SERVER['HTTP_USER_AGENT'];

		$os_platform  = "Unknown OS Platform";

		$os_array     = array(
			'/windows nt 10/i'      =>  'Windows 10',
			'/windows nt 6.3/i'     =>  'Windows 8.1',
			'/windows nt 6.2/i'     =>  'Windows 8',
			'/windows nt 6.1/i'     =>  'Windows 7',
			'/windows nt 6.0/i'     =>  'Windows Vista',
			'/windows nt 5.2/i'     =>  'Windows Server 2003/XP x64',
			'/windows nt 5.1/i'     =>  'Windows XP',
			'/windows xp/i'         =>  'Windows XP',
			'/windows nt 5.0/i'     =>  'Windows 2000',
			'/windows me/i'         =>  'Windows ME',
			'/win98/i'              =>  'Windows 98',
			'/win95/i'              =>  'Windows 95',
			'/win16/i'              =>  'Windows 3.11',
			'/macintosh|mac os x/i' =>  'Mac OS X',
			'/mac_powerpc/i'        =>  'Mac OS 9',
			'/linux/i'              =>  'Linux',
			'/ubuntu/i'             =>  'Ubuntu',
			'/iphone/i'             =>  'iPhone',
			'/ipod/i'               =>  'iPod',
			'/ipad/i'               =>  'iPad',
			'/android/i'            =>  'Android',
			'/blackberry/i'         =>  'BlackBerry',
			'/webos/i'              =>  'Mobile'
		);

		foreach ($os_array as $regex => $value)
			if (preg_match($regex, $user_agent))
				$os_platform = $value;

			return trim(strtolower($os_platform));
		}

		function get_client_ip() {
			$ipaddress = '';
			if (isset($_SERVER['HTTP_CLIENT_IP']))
				$ipaddress = $_SERVER['HTTP_CLIENT_IP'];
			else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
				$ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
			else if(isset($_SERVER['HTTP_X_FORWARDED']))
				$ipaddress = $_SERVER['HTTP_X_FORWARDED'];
			else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
				$ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
			else if(isset($_SERVER['HTTP_FORWARDED']))
				$ipaddress = $_SERVER['HTTP_FORWARDED'];
			else if(isset($_SERVER['REMOTE_ADDR']))
				$ipaddress = $_SERVER['REMOTE_ADDR'];
			else
				$ipaddress = 'UNKNOWN';
			return ($ipaddress == "::1" || $ipaddress == "127.0.0.1") ? "localhost" : $ipaddress;
		}

	}