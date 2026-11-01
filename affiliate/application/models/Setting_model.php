<?php	
class Setting_model extends MY_Model{
	public function clear($setting_type){
		$this->db->query('DELETE FROM setting WHERE setting_type= "'. $setting_type .'" ');
	}

	public function save($setting_type, $data){
		foreach ($data as $key => $value) {
			$this->db->where('setting_type',$setting_type);
			$this->db->where('setting_key',$key);
			$q = $this->db->get('setting');
			if (is_array($value)) {
				$value = json_encode($value);
			}

			if ( $q->num_rows() > 0 ){
				$this->db->where('setting_id',$q->row()->setting_id );
				$this->db->update('setting', array(
					'setting_value' => $value,
					'setting_ipaddress' => $_SERVER['REMOTE_ADDR'],
				));
			} else {
				$this->db->insert('setting',array(
					'setting_value' => $value,
					'setting_key' => $key,
					'setting_status' => 1,
					'setting_is_default' => 0,
					'setting_type' => $setting_type,
					'setting_ipaddress' => $_SERVER['REMOTE_ADDR'],
				));
			}
		}
	}

	public function vendorSave($user_id, $setting_type, $data){
		foreach ($data as $key => $value) {
			$this->db->where('user_id',$user_id);
			$this->db->where('setting_type',$setting_type);
			$this->db->where('setting_key',$key);
			$q = $this->db->get('vendor_config');
			if (is_array($value)) {
				$value = json_encode($value);
			}

			if ( $q->num_rows() > 0 ){
				$this->db->where('setting_id',$q->row()->setting_id );
				$this->db->update('vendor_config', array(
					'setting_value' => $value,
					'setting_ipaddress' => $_SERVER['REMOTE_ADDR'],
				));
			} else {
				$this->db->insert('vendor_config',array(
					'user_id' => $user_id,
					'setting_value' => $value,
					'setting_key' => $key,
					'setting_status' => 1,
					'setting_is_default' => 0,
					'setting_type' => $setting_type,
					'setting_ipaddress' => $_SERVER['REMOTE_ADDR'],
				));
			}
		}
	}

	public function save_meta($data, $where = null) {
		if($where != null) {
			$this->db->where($where);
			$this->db->update('meta_data', $data);
			return $where['meta_id'];
		} else {
			$this->db->insert('meta_data', $data);
			return $this->db->insert_id();
		}
	}
	public function save_ads($data, $where = null,$duplicate=array()) {
		
		if($where != null) {
			$this->db->where($where);
			$this->db->update('google_ads', $data);
			return $where['id'];
		} else {
			if(empty($duplicate)){
				$this->db->insert('google_ads', $data);
				return $this->db->insert_id();
			}
		}
	}
	public function update_ads($status) {
		
		if($status == 1) {

			$this->db->where('ad_section IN (1,2)');
			$this->db->update('google_ads', array('status'=>1));
			
		} else {
			$this->db->where('ad_section',$status);
			$this->db->update('google_ads', array('status'=>1));
			
		}
	}

	public function get_meta_content($where) {
		$this->db->where($where);
		return $this->db->get('meta_data')->row();
	}
	public function getGoogleAds($ad_section=0,$status=0) {
		if($ad_section){
			$this->db->where('ad_section',$ad_section);
		}
		if($status == 1){
			$this->db->where('status',1);
		}
		return $this->db->get('google_ads')->result_array();
	}
	

	public function set_default_admin_url(){
		$this->db->where('setting_key','admin_url');
		$this->db->where('setting_type','security');
		$this->db->update('setting', array(
			'setting_value' => 'admin'
		));
		return $this->db->affected_rows();
	}

	public function set_default_front_url(){
		$this->db->where('setting_key','front_url');
		$this->db->where('setting_type','security');
		$this->db->update('setting', array(
			'setting_value' => '',
		));
		return $this->db->affected_rows();
	}

	public function update_store_status($status){
		$this->db->where('setting_key','status');
		$this->db->where('setting_type','store');
		$this->db->update('setting', array(
			'setting_value' => $status,
		));
		return $this->db->affected_rows();
	}

	public function update_store_menu_on_front($status){
		$this->db->where('setting_key','menu_on_front');
		$this->db->where('setting_type','store');
		$this->db->update('setting', array(
			'setting_value' => $status,
		));
		return $this->db->affected_rows();
	}

	public function update_cookies_menu($status){
	    $this->db->where('setting_key','cookies_menu');
	    $this->db->where('setting_type','site');
	    $this->db->update('setting', array(
	        'setting_value' => $status,
	    ));
	    return $this->db->affected_rows();
	}


	public function update_store_menu_on_front_blank($status){
		$this->db->where('setting_key','menu_on_front_blank');
		$this->db->where('setting_type','store');
		$this->db->update('setting', array(
			'setting_value' => $status,
		));
		return $this->db->affected_rows();
	}

	public function update_store_mode($mode){
		$this->db->where('setting_key','store_mode');
		$this->db->where('setting_type','store');
		$q = $this->db->get('setting');

		if ( $q->num_rows() > 0 ) 
		{
			$this->db->where('setting_key','store_mode');
			$this->db->where('setting_type','store');
			$this->db->update('setting',['setting_value'=>$mode, 'setting_status' => 1]);
		} else {
			$this->db->insert('setting',['setting_value'=>$mode,'setting_key'=>'store_mode','setting_type'=>'store', 'setting_status' => 1]);
		}

		
		return $this->db->affected_rows();
	}

	public function update_store_theme($theme){
		$this->db->where('setting_key','theme');
		$this->db->where('setting_type','store');
		$this->db->update('setting', array(
			'setting_value' => $theme,
		));
		return $this->db->affected_rows();
	}

	public function update_all_settings($status, $setting_key, $setting_type){

		$exists = $this->db->get_where('setting', array(
			'setting_key' => $setting_key,
			'setting_type' => $setting_type
		))->num_rows();

		if($exists > 0) {
			$this->db->where('setting_key', $setting_key);
			$this->db->where('setting_type', $setting_type);
			$this->db->update('setting', array(
				'setting_status' => 1,
				'setting_value' => $status,
			));
		} else {
			$this->db->insert('setting', array(
				'setting_key' => $setting_key,
				'setting_type' => $setting_type,
				'setting_status' => 1,
				'setting_value' => $status,
			));
		}

		return $this->db->affected_rows();
	}

	public function update_product_settings($status, $setting_key, $product_id){
		if ($setting_key == '_meta_show_to_affiliates') {
			$this->db->where('related_product_id',$product_id);
			$this->db->where('meta_key','show_to_affiliates');
			$this->db->update('product_meta', array(
				'meta_value' => $status,
			));
		}elseif ($setting_key == '_meta_show_to_featured') {
			$this->db->where('related_product_id',$product_id);
			$this->db->where('meta_key','show_to_featured');
			$this->db->update('product_meta', array(
				'meta_value' => $status,
			));
		}else{
			$this->db->where('product_id',$product_id);
			$this->db->update('product', array(
				$setting_key => $status,
			));
		}

		return $this->db->affected_rows();
	}

	public function default_theme_settings($setting, $color){
		$this->db->where('setting_key',$setting);
		$this->db->where('setting_type','theme');
		$this->db->update('setting', array(
			'setting_value' => $color,
		));
		return $this->db->affected_rows();
	}

	public function default_font_settings($setting, $font){
		$this->db->where('setting_key',$setting);
		$this->db->where('setting_type','site');
		$this->db->update('setting', array(
			'setting_value' => $font,
		));
		return $this->db->affected_rows();
	}

	public function set_default_theme_settings($setting_array, $setting_type){
		$update = 0;

		foreach ($setting_array as $key => $value) {
			$this->db->where('setting_type',$setting_type);
			$this->db->where('setting_key',$key);
			$q = $this->db->get('setting');

			if ( $q->num_rows() == 0 ){
				$update = $this->db->insert('setting',array(
					'setting_value' => $value,
					'setting_key' => $key,
					'setting_status' => 1,
					'setting_is_default' => 0,
					'setting_type' => $setting_type,
					'setting_ipaddress' => $_SERVER['REMOTE_ADDR'],
				));
			}
		}

		return $update;
	}

	public function saveThemeSetting($setting_type, $data){
		foreach ($data as $key => $value) {
			$this->db->where('setting_type',$setting_type);
			$this->db->where('setting_key',$key);
			$q = $this->db->get('theme_colors');
			if (is_array($value)) {
				$value = json_encode($value);
			}
			
			if ( $q->num_rows() > 0 ){
				$this->db->where('setting_id',$q->row()->setting_id );
				$this->db->update('theme_colors', array(
					'setting_value' => $value,
					'setting_ipaddress' => $_SERVER['REMOTE_ADDR'],
				));
			} else {
				$this->db->insert('theme_colors',array(
					'setting_value' => $value,
					'setting_key' => $key,
					'setting_status' => 1,
					'setting_is_default' => 0,
					'setting_type' => $setting_type,
					'setting_ipaddress' => $_SERVER['REMOTE_ADDR'],
				));
			}
		}
	}

	public function default_front_theme_settings($setting, $color){
		$this->db->where('setting_key',$setting);
		$this->db->where('setting_type','theme');
		$this->db->update('theme_colors', array(
			'setting_value' => $color,
		));
		return $this->db->affected_rows();
	}

	public function set_default_front_theme_settings($setting_array, $setting_type){
		$update = 0;

		foreach ($setting_array as $key => $value) {
			$this->db->where('setting_type',$setting_type);
			$this->db->where('setting_key',$key);
			$q = $this->db->get('theme_colors');

			if ( $q->num_rows() == 0 ){
				$update = $this->db->insert('theme_colors',array(
					'setting_value' => $value,
					'setting_key' => $key,
					'setting_status' => 1,
					'setting_is_default' => 0,
					'setting_type' => $setting_type,
					'setting_ipaddress' => $_SERVER['REMOTE_ADDR'],
				));
			}
		}

		return $update;
	}

	public function allToDo(){
		$this->db->select('*');
		$this->db->from('todo_list');
		$query = $this->db->get();
		return $query->result_array();
	}

	public function saveWithLanguage($setting_type, $language_id,$data){
		foreach ($data as $key => $value)
		 {
			$this->db->where('setting_type',$setting_type);
			$this->db->where('setting_key',$key);
			$this->db->where('language_id',$language_id);
			$q = $this->db->get('setting');
			if (is_array($value)) {
				$value = json_encode($value);
			}

			if ( $q->num_rows() > 0 ){
				$this->db->where('setting_id',$q->row()->setting_id );
				$this->db->update('setting', array(
					'setting_value' => $value,
					'setting_ipaddress' => $_SERVER['REMOTE_ADDR'],
				));
			} else {
				$this->db->insert('setting',array(
					'setting_value' => $value,
					'setting_key' => $key,
					'setting_status' => 1,
					'setting_is_default' => 0,
					'setting_type' => $setting_type,
					'setting_ipaddress' => $_SERVER['REMOTE_ADDR'],
					'language_id' => $language_id,
				));
			}

			 
		}
	}
}