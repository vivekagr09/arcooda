<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Incomereport extends MY_Controller {

	function __construct() {
		parent::__construct();
		$this->load->model('user_model', 'user');
		$this->load->model('Product_model');
		$this->load->model('Income_model');
		$this->load->model('Wallet_model');
		$this->load->model('Report_model');
		___construct(1);
		$this->checkSessionTimeout();
	}

	public function userdetails(){ return $this->session->userdata('administrator'); }

	public function userlogins(){ return $this->session->userdata('user'); }

	public function index(){

		if(!$this->userdetails()){ redirect('admincontrol/dashboard', 'refresh'); }

		$data = array();

		$this->Report_model->view('incomereports/admin/admin_transaction', $data);

	}



	public function statistics(){

		if(!$this->userlogins()){ redirect('admincontrol/dashboard', 'refresh'); }

		$data = array();

		$this->Report_model->view('incomereports/user/transaction', $data , 'usercontrol');
	}



public function get_data() {
    $filter = $this->input->post(null, true);

	$userdetails = $this->userdetails();

    $data = $this->Income_model->get_report($filter);

    $jsonResponse = ['data' => []];

    if (isset($filter['destination']) && $filter['destination'] === 'admin-user-stat') {
        $jsonResponse['recordsFiltered'] = $jsonResponse['recordsTotal'] = $this->Income_model->get_report_count($filter);
    }

    $loop = $filter['page_no'];

    foreach ($data['data'] as $key => $record) {
        $countryFlagSrc = base_url('assets/vertical/assets/images/flags/' . strtolower($record['country_code']) . '.png');
        
        $jsonResponse['data'][] = [
            $loop + 1,
            "{$record['name']} <img src='{$countryFlagSrc}' class='pull-right top-affiliate-country-flag'>",
            $record['username'],
            $record['total_click'],
            $record['total_click_amount'],
            $record['total_sale_count'],
            $record['total_sale_amount'],
            $record['total_sale_comm'],
            "{$record['external_action_click']}/{$record['action_click_commission']}",
            $record['wallet_accept_amount'],
            $record['total_commission'],
        ];
        
        $loop++;
    }

    echo json_encode($jsonResponse);
}




	public function user_search(){

		$data = $this->db->query("SELECT id,CONCAT(firstname,' ',lastname, ' - (', username ,')') as name  FROM users WHERE type='user' AND firstname like '%". $this->input->get('p') ."%' ")->result_array();

		echo json_encode($data);

	}

}

