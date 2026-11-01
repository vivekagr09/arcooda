<?php
class Theme_setting extends MY_Model{

	public function create_or_update($type, $key, $value) {
		$existing = $this->db->get_where('theme_setting', [
			"setting_type" => $type,
			"setting_key" => $key
		])->row_array();


		if (isset($existing['setting_id']) && ! empty($existing['setting_id'])){
			$this->db->where('setting_id', $existing['setting_id']);
			$this->db->update('theme_setting', array(
				'setting_value' => $value,
				'setting_ipaddress' => $_SERVER['REMOTE_ADDR'],
			));
		} else {
			$this->db->insert('theme_setting',array(
				'setting_value' => $value,
				'setting_key' => $key,
				'setting_status' => 1,
				'setting_is_default' => 0,
				'setting_type' => $type,
				'setting_ipaddress' => $_SERVER['REMOTE_ADDR'],
			));
		}
	}

	public function getByType($type)
	{
		$dbResults = $this->db->get_where('theme_setting', [
			"setting_type" => $type
		])->result_array();

		$settings = [];

		foreach($dbResults as $row)
		{
			$settings[$row['setting_key']] = $row['setting_value'];
		}

		return $settings;
	}
}