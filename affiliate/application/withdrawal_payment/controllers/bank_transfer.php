<?php

class bank_transfer {
	public $title = 'Bank Transfer';
	public $icon = 'assets/withdrawal_payment/bank-transfer.png';
	public $website = '';

	function __construct($api){ $this->api = $api; }

	public function onInstall() {

	}

	public function onUnInstall() {
	}

	public function saveUserSubmit(){
		$data = $this->api->input->post(null,true);
		$explode_data = explode(",",$data['get_custom_fiels_validate']);
		$json = [];
		$saveSetting = [];

		if(isset($_FILES['payment_proof']) && !empty($_FILES['payment_proof']['name'])) {
			$fileNameArray = explode('.', $_FILES['payment_proof']['name']);

			$config['upload_path'] = 'assets/user_upload';

			$config['allowed_types'] = 'png|gif|jpeg|jpg|PNG|GIF|JPEG|JPG|ICO|ico|pdf|docx|doc|ppt|xls|txt';

			$config['max_size']      = 2048;

			$this->api->load->helper('string');

			$config['file_name']  = random_string('alnum', 32);

			$this->api->load->library('upload', $config);

			$this->api->upload->initialize($config);

			if (!$this->api->upload->do_upload('payment_proof')) {
				$errors = $this->api->upload->display_errors();
			} else {
				$saveSetting['payment_proof'] = $config['file_name'].".".$fileNameArray[sizeof($fileNameArray)-1];
			}
		}

		if(!isset($errors) || empty($errors)) {

			$check = 0;

			if(sizeof($data) > 0)
			{
				foreach ($data as $key => $value) {
					if($key != 'code' && $key != 'ids' && $key != 'get_custom_fiels_validate')
					{	
						if (!isset($data[$key]) || trim($data[$key]) == '' && $explode_data[$check] == "Yes") {
							$json['errors'][$key] = str_replace("_", " ", $key)." is required";
						}
						$check++;
					}
				}
			}
			
			
			if (!isset($json['errors'])) {
				$this->api->load->model('Withdrawal_payment_model');

				if(sizeof($data) > 0)
				{
					foreach ($data as $key => $value) {
						if($key != 'code' && $key != 'ids' && $key != 'get_custom_fiels_validate')
						{
							$saveSetting[$key] = $value;
						}
					}
				}


				

				$status = $this->api->Withdrawal_payment_model->apiAddWithdrwalRequest($data['code'],$data['ids'], $saveSetting);

				
				if((int)$status['status'] == 1){
					$json['success'] = 1;
				} else{
					$json['errors']['bank_details'] = $status['error_message'];
				}
			}
		} else {
			$json['errors']['payment_proof'] = $errors;
		}

		return $json;
	}
	
}