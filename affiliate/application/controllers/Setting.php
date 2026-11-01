<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Setting extends MY_Controller {
	function __construct() {
		parent::__construct();
		$this->load->model('Product_model');
		$this->load->library('user_agent');
		___construct(1);
	}

	public function getModal(){
		$data = array();
		$input = $this->input->post(null,true);
		$key = $input['key'];
		$type = $input['type'];

		$data['skey'] = $type;
		$data['setting_key'] = $key;
		
		if($key == 'live_dashboard'){
			$data['title'] = __('admin.dashboard_settings');
			$data['settings'] = array(
				'sound_status'              => array( 'name' => __('admin.notification_sound'), 'type' => 'switch'),
				'action_status'             => array( 'name' => __('admin.notification_banner_for_action'), 'type' => 'switch'),
				'integration_order_status'  => array( 'name' => __('admin.notification_banner_for_integration_order'), 'type' => 'switch'),
				'affiliate_register_status' => array( 'name' => __('admin.notification_banner_for_affiliate_register'), 'type' => 'switch'),
				'local_store_order_status'  => array( 'name' => __('admin.notification_banner_for_local_store_order'), 'type' => 'switch'),
				'data_load_interval'        => array( 'name' => __('admin.dashboard_data_load_time'), 'type' => 'number','help' => __('admin.time_interval_in_seconds')),
			);
		}
		else if($key == 'live_log'){
			$data['title'] = __('admin.log_window_setting');

			$data['settings'] = array(
				'integration_logs'   => array( 'name' => __('admin.integration_logs'), 'type' => 'switch'),
				'integration_orders' => array( 'name' => __('admin.integration_orders'), 'type' => 'switch'),
				'newuser'            => array( 'name' => __('admin.new_user'), 'type' => 'switch'),
			);
		}

		$data['db_value'] = $this->Product_model->getSettings($key);
		$data['html'] = $this->load->view("common/setting_model",$data,true);

		echo json_encode($data);die;
	}

	public function saveSetting(){
		$input = $this->input->post(null,true);
		$this->Setting_model->save($input['setting_key'], $input['settings']);

		$data['success'] = 1;
		echo json_encode($data);die;
	}
}