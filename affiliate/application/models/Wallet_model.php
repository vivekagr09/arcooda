<?php

class Wallet_model extends MY_Model{
	public $request_status = array(
		'0' => 'Pending',
		'1' => 'Complete',
		'2' => 'Proccessing',
		'3' => 'Cancel',
		'4' => 'Decline',
	);
	public $status = array(
		'0' => 'ON HOLD',
		'1' => 'IN WALLET',
		'2' => 'REQUEST SENT',
		'3' => 'ACCEPT',
		'4' => 'DECLINE',
	);

	public $status_icon = array();

	function __construct(){
		parent::__construct();

		$this->status_icon = array(
			'0' => "<span class='badge bg-danger text-light px-3 py-2 fs-6'>".__('admin.on_hold')."</span>",
			'1' => "<span class='badge bg-primary text-light px-3 py-2 fs-6'>".__('admin.in_wallet')."</span>",
			'2' => "<span class='badge bg-warning text-light px-3 py-2 fs-6'>".__('admin.request_sent')."</span>",
			'3' => "<span class='badge bg-success text-light px-3 py-2 fs-6'>".__('admin.accept')."</span>",
			'4' => "<span class='badge bg-danger text-light px-3 py-2 fs-6'>".__('admin.decline')."</span>",
		);
	}

	public function request_status($find=null) {
		$status = [
			'0' => __('admin.pending_integration'),
			'1' => __('admin.complete'),
			'2' => __('admin.proccessing'),
			'3' => __('admin.cancelsssssss'),
			'4' => __('admin.decline'),
		];
		if($find!=null){
			return $status[$find];
		} else {
			return $status;
		}
	}

	public function status($find=null) {
		$status = [
			'0' => __('admin.onhold'),
			'1' => __('admin.inwallet'),
			'2' => __('admin.request_sent'),
			'3' => __('admin.accept'),
			'4' => strtoupper(__('admin.decline')),
		];
		if($find!=null){
			return $status[$find];
		} else {
			return $status;
		}
	}
	public function getDeleteData($transaction_id, $only_parent = false){
		$allTrans = [];

		$allTrans[] = $tranMain = $this->getbyId($transaction_id);

		
		if(!in_array($tranMain->type, ['refer_click_commission']) || ! str_contains($tranMain->comment, 'Level')) {

			$tranReleted = $this->getRelatedTrans($transaction_id, $tranMain->group_id, $only_parent);

			foreach ($tranReleted as $key => $value) {
				if(isset($value->id) && !empty($value->id)) {
					$allTrans[] = $value;
				}
			}
		}
		

		$dataCollection = [];

		foreach ($allTrans as $tran) {
			$data = array(
				'amount' => 0,
				'removed' => array(),
			);

			if($tran){
				$ips = array();
				$unique_ids = array();
				foreach ($tran->ip_details as $ip) {

					$ips[] = "'". $ip['ip'] ."'";
					$unique_ids[] = (int)$ip['id'];
				}

				$data['amount'] = $tran->amount;
				$data['id'] = $tran->id;
				$data['name'] = $tran->name;
				$data['details'] = $tran;
			}

			$dataCollection[] = $data;
		}
		
		return $dataCollection;	

	}

	public function getRelatedTrans($trans_id, $group_id, $only_parent){

		if($only_parent) {
			$tran =  $this->db->query('
				SELECT wallet.*, CONCAT(users.firstname," ",users.lastname) as name, users.email as user_email
				FROM wallet LEFT JOIN users ON users.id = wallet.user_id
				WHERE wallet.id != '.$trans_id.' AND wallet.group_id="'.$group_id.'" AND wallet.parent_id=0')->result();
		} else {
			$tran =  $this->db->query('
				SELECT wallet.*, CONCAT(users.firstname," ",users.lastname) as name, users.email as user_email
				FROM wallet LEFT JOIN users ON users.id = wallet.user_id
				WHERE wallet.id != '.$trans_id.' AND wallet.group_id="'.$group_id.'"
				')->result();
		}
		
		for ($i=0; $i < sizeof($tran); $i++) { 
			$tran[$i]->ip_details = json_decode($tran[$i]->ip_details,1);
		}
		
		return $tran;
	}

	public function addTransaction($data, $is_recurrsive= false, $recursion_data = []){
		$loginUser = $this->session->userdata('user');

		$loginClient = $this->session->userdata('client');

		if(((isset($loginUser['type']) && ($loginUser['type'] == 'user' || $loginUser['type'] == 'administrator')) || (isset($loginClient['type']) && ($loginClient['type'] == 'user' || $loginClient['type'] == 'administrator'))) && ! str_contains($data['type'], 'sale') && ! str_contains($data['type'], 'refer_registration_commission') && ! str_contains($data['type'], 'membership_plan_bonus')) 
		{
			$fakeTrans = true;
		}

		if(isset($this->session->administrator) && $this->session->administrator['type'] == 'admin')
			$fakeTrans = false;

		if(! isset($fakeTrans) || ! $fakeTrans){
			
			$this->load->model('Product_model');
			
			$ipInformatiom = $this->Product_model->ip_info();

			if(!isset($data['ip_details'])){
				$ips[] = array(
					'ip' => @$ipInformatiom['ip'],
					'country_code' => @$ipInformatiom['country_code'],
				);
				$data['ip_details'] = json_encode($ips);
			}

			$data['created_at']  = date("Y-m-d H:i:s");

			$this->db->insert('wallet',$data);

			$insertID = $this->db->insert_id();

			if($is_recurrsive){
				$recursion_data['transaction_id'] = $insertID;
				$this->addTransactionRecursion($recursion_data);					
			}

			return $insertID;
		} else {
			return 0;
		}
	}

	public function addTransactionBatch($dataBatch){

		$dataInsertBatch = [];

		$loginUser = $this->session->userdata('user');

		$loginClient = $this->session->userdata('client');

		$ipInformatiom = $this->Product_model->ip_info();
		
		foreach($dataBatch as $data) {
			$ips = [];

			if(((isset($loginUser['type']) && ($loginUser['type'] == 'user' || $loginUser['type'] == 'administrator')) || (isset($loginClient['type']) && ($loginClient['type'] == 'user' || $loginClient['type'] == 'administrator'))) && ! str_contains($data['type'], 'sale')  && ! str_contains($data['type'], 'refer_registration_commission')) 
			{
				$fakeTrans = true;
			}

			if(! isset($fakeTrans) || ! $fakeTrans){
				
				$this->load->model('Product_model');
				
				

				if(!isset($data['ip_details'])){
					$ips[] = array(
						'ip' => @$ipInformatiom['ip'],
						'country_code' => @$ipInformatiom['country_code'],
					);
					$data['ip_details'] = json_encode($ips);
				}

				$data['created_at']  = date("Y-m-d H:i:s");

				$dataInsertBatch[] = $data;
			}
		}

		if(!empty($dataInsertBatch)){
			$this->db->insert_batch('wallet',$dataInsertBatch);
		}
	}

	public function addRequest($data){
		$data['created_at']  = date("Y-m-d H:i:s");
		$this->db->insert('wallet_request',$data);
		return $this->db->insert_id();
	}

	public function getRequest($filter){
		if (isset($filter['user_id'])) {
			$this->db->where('wallet_request.user_id', (int)$filter['user_id']);
		}
		if (isset($filter['id'])) {
			$this->db->where('wallet_request.id', (int)$filter['id']);
		}
		$this->db->select(array(
			'wallet_request.*',
			'users.firstname',
			'users.lastname',
		));
		$this->db->from('wallet_request');
		$this->db->join('users', 'users.id = wallet_request.user_id','left');
		$this->db->order_by('wallet_request.created_at','DESC');
		if (isset($filter['id'])) {
			return $this->db->get()->row_array();
		}
		else{
			return $this->db->get()->result_array();
		}
	}

	public function balance($filter = array()){
		$where = ' 1 ';
		if (isset($filter['user_id'])) {
			$where .= '  AND wallet.user_id = '. (int)$filter['user_id'];
		}
		return (float)$this->db->query('SELECT sum(amount) as total FROM wallet WHERE '. $where)->row_array()['total'];
	}
	
	public function getbyId($id){
		$this->db->select(array(
			'wallet.*',
			'CONCAT(users.firstname," ",users.lastname) as name',
			'users.email as user_email'
		));
		$this->db->from('wallet');
		$this->db->join('users', 'users.id = wallet.user_id','left');
		$this->db->where('wallet.id', (int)$id);
		$tran =  $this->db->get()->row();

		//$tran->ip_details = json_decode(@$tran->ip_details,1);
		return $tran;
	}
	public function getallUnpaid($id){
		$this->db->select(array(
			'wallet.*',
			'CONCAT(users.firstname," ",users.lastname) as name',
			'users.email as user_email'
		));
		$this->db->from('wallet');
		$this->db->join('users', 'users.id = wallet.user_id','left');
		$this->db->where('wallet.user_id', (int)$id);
		$this->db->where('wallet.status', 1);
		$tran =  $this->db->get()->result_array();

		return $tran;
	}
	public function changeStatus($id, $status){
		return $this->db->update('wallet', array('status' => $status), array('id' => (int)$id));
	}
	public function getTransaction($filter = array(), $olnyNumRows = false , $additional = null){
		if($olnyNumRows === true) {
			$select = array(
				'wallet.id',
			);
		} else {
			$select = array(
				'wallet.*',
				'users.username',
				'users.firstname',
				'users.lastname',
				'users.type as usertype',
				'wallet_recursion.id as wallet_recursion_id',
				'wallet_recursion.status as wallet_recursion_status',
				'wallet_recursion.type as wallet_recursion_type',
				'wallet_recursion.custom_time as wallet_recursion_custom_time',
				'wallet_recursion.next_transaction as wallet_recursion_next_transaction',
				'wallet_recursion.endtime as wallet_recursion_endtime',
				"(SELECT payment_method FROM `order` WHERE wallet.reference_id_2 = `order`.id AND wallet.type IN('sale_commission','vendor_sale_commission', 'admin_sale_commission') AND wallet.dis_type is NULL ) AND wallet.comm_from != 'ex' as payment_method ",
				"(SELECT total FROM `integration_orders` WHERE comm_from='ex' AND wallet.reference_id_2 = `integration_orders`.id AND wallet.type IN ('sale_commission','admin_sale_commission')) as integration_orders_total ",
				"(SELECT total FROM `order` WHERE comm_from != 'ex' AND id = wallet.reference_id_2 AND wallet.type IN('sale_commission','vendor_sale_commission', 'admin_sale_commission') ) as local_orders_total "
			);
		}


		$where = '';
		if (isset($filter['user_id'])) {
			$where .= ' AND wallet.user_id = '. (int)$filter['user_id'];
		}
		if (isset($filter['old_with'])) {
			$where .= ' AND (wallet.wv != "V2" OR wallet.wv IS NULL)';
		}
		if (isset($filter['id_ne'])) {
			$where .= ' AND wallet.id != '. (int)$filter['id_ne'];
		}
		if (isset($filter['group_id'])) {
			$where .= ' AND wallet.group_id = '. $filter['group_id'];
		}
		if (isset($filter['paid_status'])) {
			if($filter['paid_status'] == 'unpaid'){
				$where .= ' AND wallet.status IN (0,1)';
			} else if($filter['paid_status'] == 'paid'){
				$where .= ' AND wallet.status = 3 ';
			}
		}
		if (isset($filter['id'])) {
			$where .= ' AND wallet.id = '. (int)$filter['id'];
		}
		if (isset($filter['id_in'])) {
			$where .= ' AND wallet.id IN ('. $filter['id_in'] .")";
		}
		if (isset($filter['parent_id'])) {

			$where .= ' AND wallet.parent_id = '. (int)$filter['parent_id'];
		}
		if (isset($filter['recurring']) && $filter['recurring'] == '1') {
			$where .= ' AND wallet_recursion.id > 0 ';
		} else if (isset($filter['recurring']) && $filter['recurring'] == '0') {
			$where .= ' AND wallet_recursion.id  = "" ';
		}

		if (isset($filter['status_gt'])) {
			$where .= ' AND wallet.status >= '. (int)$filter['status_gt'];
		}
		if (isset($filter['status'])) {
			$where .= ' AND wallet.status= ' . (int)$filter['status'];
		}
		if (isset($filter['type'])) {
			$where .= ' AND wallet.type = ' . "'".$filter['type']."'";
		}
		if (isset($filter['type_in'])) {
			$where .= ' AND wallet.type IN (' . $filter['type_in']. ')';
		}
		if (isset($filter['is_action'])) {
			$where .= ' AND wallet.is_action = '. $filter['is_action'];
		}

		if (isset($filter['not_negative_balence']) && $filter['not_negative_balence'] == true) {
			$where .= ' AND wallet.amount >= 0 ';
		}

		if (isset($filter['withdraw_type']) && !empty($filter['withdraw_type'])) {
			$where .= ' AND wallet.commission_status='.$filter['withdraw_type'];
		}

		if (isset($filter['click_log'])) {
			$where .= " AND  (wallet.type IN ('click_commission','form_click_commission','affiliate_click_commission') OR (wallet.type = 'external_click_commission' AND is_action=0 AND comm_from = 'ex')) ";
		}

		if (isset($filter['date'])) {
			if (strpos($filter['date'], ' - ') !== false) {
				list($start_date, $end_date) = explode(" - ", $filter['date']);

				$start_date = date("Y-m-d", strtotime($start_date));
				$end_date = date("Y-m-d", strtotime($end_date));

				$where .= " AND DATE(wallet.created_at) >= '{$start_date}'";
				$where .= " AND DATE(wallet.created_at) <= '{$end_date}'";
			}
		}

		if (isset($filter['types'])) {
			switch ($filter['types']) {
				case 'actions':
				$where .= ' AND wallet.is_action=1 AND (wallet.type="external_click_commission" OR wallet.type="refer_click_commission") ';
				break;

				case 'sale':
				$where .= ' AND wallet.type IN("sale_commission","admin_sale_commission", "refer_sale_commission") ';
				break;
				case 'external_integration':

				$where .= " AND  wallet.comm_from = 'ex' ";
				break;
				case 'clicks':
				$where .= " AND (wallet.type IN ('click_commission','form_click_commission','affiliate_click_commission') OR (wallet.type = 'external_click_commission' AND is_action=0 AND comm_from = 'ex')) ";
				break;

				default:
				   # code...
				break;
			}
		}

		if (isset($filter['group_id'])) {
			$where .= " AND wallet.group_id='".$filter['group_id']."' ";
		}

		if (isset($filter['not_tran_id'])) {
			$where .= " AND wallet.id!='".$filter['not_tran_id']."' ";
		}
		if (isset($filter['amount'])) {
			$where .= " AND wallet.amount > 0 ";
		}


		$query = " SELECT ". implode(",", $select);
		$query .= " FROM wallet LEFT JOIN users ON users.id = wallet.user_id";
		$query .= " LEFT JOIN  wallet_recursion ON wallet_recursion.transaction_id = wallet.id";
		$query .= " WHERE 1 {$where} ";


		if($additional === "ONLY_PARENTS") {
			$query .= " GROUP BY wallet.group_id ";
		}

		if (isset($filter['sortBy']) && $filter['sortBy'] && isset($filter['orderBy']) && $filter['orderBy']) {
			$query .= " ORDER BY ".$filter['sortBy'] ." ".$filter['orderBy'];
		} else{
			$query .= " ORDER BY wallet.id DESC";
		}

		if (isset($filter['per_page']) && isset($filter['offset'])) {
			$query .= " LIMIT ".$filter['per_page']." OFFSET ".$filter['offset'];
		}
		

		if($olnyNumRows == true) {
			$data = $this->db->query($query)->num_rows();
		} else {
			$data = $this->db->query($query)->result_array();
		
			foreach ($data as &$d) {
				$d['total_recurring'] = $this->db->query("SELECT count(id) as total FROM `wallet` WHERE parent_id = ".$d['id']."")->row()->total;
				$d['total_recurring_amount'] = $this->db->query("SELECT SUM(amount) as total FROM `wallet` WHERE parent_id = ".$d['id']."")->row()->total;
				$d['has_recursion_records'] = $this->db->query('SELECT id from wallet where parent_id='.$d['id'].' LIMIT 1')->row_array()['id'];
			}
		}

		return $data;
	}

	public function getIntegrationTransaction($filter = array()){
		$select = array(
			'wallet.*',
			'users.username',
			'users.firstname',
			'users.lastname',
			'wallet_recursion.id as wallet_recursion_id',
			'wallet_recursion.status as wallet_recursion_status',
			'wallet_recursion.type as wallet_recursion_type',
			'wallet_recursion.custom_time as wallet_recursion_custom_time',
			'wallet_recursion.next_transaction as wallet_recursion_next_transaction',
			'wallet_recursion.endtime as wallet_recursion_endtime',
			"(SELECT count(id) as total FROM `wallet` w WHERE wallet.id = w.parent_id) as total_recurring ",
			"(SELECT total FROM `integration_orders` WHERE comm_from='ex' AND wallet.reference_id_2 = `integration_orders`.id AND wallet.type IN ('sale_commission','admin_sale_commission') ) as integration_orders_total ",
			"(SELECT SUM(amount) FROM `wallet` ww WHERE ww.parent_id=wallet.id) as total_recurring_amount"
		);

		$where = '';
		$user_id= (int)$filter['user_id'];
		if (isset($filter['user_id'])) {
		}
		if (isset($filter['parent_id'])) {
			$where .= ' AND wallet.parent_id = '. (int)$filter['parent_id'];
		}
		if (isset($filter['recurring']) && $filter['recurring'] == '1') {
			$where .= ' AND wallet_recursion.id > 0 ';
		} else if (isset($filter['recurring']) && $filter['recurring'] == '0') {
			$where .= ' AND wallet_recursion.id  = "" ';
		}

		if (isset($filter['status_gt'])) {
			$where .= ' AND wallet.status >= '. (int)$filter['status_gt'];
		}
		if (isset($filter['status'])) {
			$where .= ' AND wallet.status= ' . (int)$filter['status'];
		}
		if (isset($filter['type'])) {
			$where .= ' AND wallet.type = ' . "'".$filter['type']."'";
		}
		if (isset($filter['type_in'])) {
			$where .= ' AND wallet.type IN (' . $filter['type_in']. ')';
		}
		if (isset($filter['is_action'])) {
			$where .= ' AND wallet.is_action = '. $filter['is_action'];
		}

		if (isset($filter['click_log'])) {
			$where .= " AND  (wallet.type IN ('click_commission','form_click_commission','affiliate_click_commission') OR (wallet.type = 'external_click_commission' AND is_action=0 AND comm_from = 'ex')) ";
		}

		if (isset($filter['date'])) {
			if (strpos($filter['date'], ' - ') !== false) {
				list($start_date, $end_date) = explode(" - ", $filter['date']);

				$start_date = date("Y-m-d", strtotime($start_date));
				$end_date = date("Y-m-d", strtotime($end_date));

				$where .= " AND DATE(wallet.created_at) >= '{$start_date}'";
				$where .= " AND DATE(wallet.created_at) <= '{$end_date}'";
			}
		}

		$query = " SELECT ". implode(",", $select);
		$query .= " FROM wallet LEFT JOIN users ON users.id = wallet.user_id";
		$query .= " LEFT JOIN  wallet_recursion ON wallet_recursion.transaction_id = wallet.id";
		$query .= " WHERE (wallet.id IN (SELECT affiliate_tran FROM integration_orders WHERE vendor_id = {$user_id}) OR wallet.id IN (SELECT admin_tran FROM integration_orders WHERE vendor_id = {$user_id}) ){$where} ";


		if (isset($filter['sortBy']) && $filter['sortBy'] && isset($filter['orderBy']) && $filter['orderBy']) {
			$query .= " ORDER BY ".$filter['sortBy'] ." ".$filter['orderBy'];
		} else{
			$query .= " ORDER BY wallet.id DESC";
		}

		if (isset($filter['per_page']) && isset($filter['page_num'])) {
			$query .= " LIMIT ".$filter['page_num'].",".$filter['per_page'];
		}

		$data = $this->db->query($query)->result_array();


		return $data;
	}

	public function get_totals_for_admin_users_stat($filter = array(), $extraTotals = false, $calling_for = 'admin'){
		$where = ' 1 ';
		$where1 = ' 1 ';		 
		$where_vendor = ' 1 ';

		if (isset($filter['date'])) {
			if (strpos($filter['date'], ' - ') !== false) {
				list($start_date, $end_date) = explode(" - ", $filter['date']);
				$start_date = date("Y-m-d", strtotime($start_date));
				$end_date = date("Y-m-d", strtotime($end_date));
				$where .= "  AND DATE(created_at) >= '{$start_date}' AND DATE(created_at) <= '{$end_date}' ";
				$where1 .= "  AND DATE(o.created_at) >= '{$start_date}' AND DATE(o.created_at) <= '{$end_date}' ";

				$clickWhere = "  AND DATE(created_at) >= '{$start_date}' AND DATE(created_at) <= '{$end_date}' ";
				$clickWherePa = "  AND DATE(pa.created_at) >= '{$start_date}' AND DATE(pa.created_at) <= '{$end_date}' ";
				$clickWhereIca = "  AND DATE(ica.created_at) >= '{$start_date}' AND DATE(ica.created_at) <= '{$end_date}' ";
			}
		}

		if (isset($filter['user_id'])) {
			$where .= '  AND user_id = '. (int)$filter['user_id'];
			$where_vendor = $where1;
			$where1 .= '  AND op.refer_id = '. (int)$filter['user_id'];
			$where_vendor .= '  AND op.vendor_id = '. (int)$filter['user_id'];
		}
		
		if (isset($filter['user_id'])) {
			$user_id = $filter['user_id'];

			$click_localstore_total = (int)$this->db->query("SELECT COUNT(pa.action_id) as total FROM product_action pa LEFT JOIN product_affiliate paff ON (paff.product_id = pa.product_id) WHERE (pa.user_id={$user_id}) {$clickWherePa} ")->row()->total;	

			$vendor_click_localstore_total = (int)$this->db->query("SELECT COUNT(pa.action_id) as total FROM product_action pa LEFT JOIN product_affiliate paff ON (paff.product_id = pa.product_id) WHERE (paff.user_id={$user_id}) {$clickWherePa}")->row()->total;

			$click_external_total = (int)$this->db->query("SELECT COUNT(id) as total FROM integration_clicks_action WHERE user_id={$user_id} AND is_action=0 {$clickWhere}")->row()->total;

			$vendor_click_external_total = (int)$this->db->query("SELECT COUNT(ica.id) as total FROM integration_clicks_action ica LEFT JOIN integration_tools it ON it.id = ica.tools_id WHERE it.vendor_id={$user_id} AND is_action=0 {$clickWhereIca}")->row()->total;

			$click_form_total = (int)$this->db->query("SELECT COUNT(action_id) as total FROM form_action WHERE user_id={$user_id} {$clickWhere}")->row()->total;

			$data['n_total_click_count'] = $click_localstore_total + $vendor_click_localstore_total + $click_external_total + $vendor_click_external_total + $click_form_total;
		} else {
			$data['n_total_click_count'] = $this->db->query('SELECT count(id) as total FROM wallet WHERE type LIKE "%click%" AND is_action=0 AND status > 0 AND '. $where )->row_array()['total'];
		}

		$data['n_total_click_comission'] = $this->db->query('SELECT sum(amount) as total FROM wallet WHERE type LIKE "%click%" AND is_action=0 AND status > 0 AND '. $where )->row_array()['total'];

		$data['n_total_action_count'] = $this->db->query('SELECT count(id) as total FROM wallet WHERE type LIKE "%click%" AND status > 0 AND is_action=1 AND '. $where )->row_array()['total'];

		$data['n_total_action_comission'] = $this->db->query('SELECT sum(amount) as total FROM wallet WHERE type LIKE "%click%" AND status > 0 AND is_action=1 AND '. $where )->row_array()['total'];

		$data['n_total_sale_count'] = $this->db->query('SELECT count(id) as total FROM wallet WHERE type LIKE "%sale%" AND status > 0 AND '. $where )->row_array()['total'];

		$data['n_total_sale_comission'] = $this->db->query('SELECT sum(amount) as total FROM wallet WHERE type LIKE "%sale%" AND status > 0 AND '. $where )->row_array()['total'];
		
		$data['n_total_comission_paid'] = $this->db->query('SELECT sum(amount) as total FROM wallet WHERE status = 3 AND '. $where )->row_array()['total'];

		$data['total_click_commi'] = (float)$this->db->query('SELECT sum(amount) as total FROM wallet WHERE type = "click_commission" AND status > 0 AND '. $where )->row_array()['total'];

		$data['total_form_click_commi'] = (float)$this->db->query('SELECT sum(amount) as total FROM wallet WHERE type = "form_click_commission" AND status > 0 AND '. $where )->row_array()['total'];

		$data['total_affiliate_click_commission'] = (float)$this->db->query('SELECT sum(amount) as total FROM wallet WHERE type = "affiliate_click_commission" AND status > 0 AND '. $where )->row_array()['total'];

		$data['total_sale'] = (float)$this->db->query('SELECT SUM(op.total) as total FROM `order_products` op LEFT JOIN `order` as o ON o.id = op.order_id WHERE o.status = 1 AND '. $where1)->row_array()['total'];

		$data['order_external_commission']  = $this->db->query("SELECT SUM(amount) as total FROM wallet WHERE comm_from='ex' AND type LIKE '%sale%' AND status>0 AND commission_status=0 AND $where")->row()->total;

		$data['all_clicks_comm'] = $data['total_click_commi'] + $data['total_form_click_commi'] + $data['total_affiliate_click_commission'] ;

		$data['all_sale_comm'] = (float)$this->db->query('SELECT sum(amount) as total FROM wallet WHERE status > 0 AND  type IN("sale_commission","vendor_sale_commission") AND '. $where )->row_array()['total'];

		$data['total_sale_count'] = (float)$this->db->query('SELECT COUNT(op.order_id) as total FROM order_products op LEFT JOIN `order` o on (o.id = op.order_id) WHERE o.status > 0  AND '.$where1 ." GROUP BY o.id ")->row_array()['total'];

		$data['wallet_accept_amount'] = $data['wallet_accept_count'] = 0;
		$query = $this->db->query('SELECT sum(amount) as amount,count(`status`) as counts,`status` FROM `wallet` WHERE '. $where .' GROUP BY `status`')->result_array();
		foreach ($query as $key => $value) {
			if($value['status'] == 3) {
				$data['wallet_accept_amount'] = (float)$value['amount'];
				$data['wallet_accept_count'] = (float)$value['counts'];
			}
		}

		$data['order_external_sale'] = $this->db->query('SELECT sum(total) as total FROM integration_orders WHERE  '. $where ."  ")->row_array()['total'];
		$data['n_total_sale_amount'] = $data['total_sale'] + $data['order_external_sale'];


		$integration_balance = $this->db->query('SELECT vendor_id,base_url,total,(SELECT amount FROM wallet WHERE integration_orders.admin_tran = wallet.id) as admin_comm FROM integration_orders WHERE  '. $where ."  ")->result();

		foreach ($integration_balance as $vv) {
			if((int)$vv->vendor_id == 0){
				$data['integration']['balance'] += $vv->total;
				$data['integration']['all'][$vv->base_url]['balance'] += $vv->total;
			} else{
				$data['integration']['balance'] += $vv->admin_comm;
				$data['integration']['all'][$vv->base_url]['balance'] += $vv->admin_comm;
			}
		}


		$integration_balance = $this->db->query('SELECT domain_name,sum(amount) as total FROM wallet WHERE comm_from = "ex" AND status = 3 AND '. $where ." GROUP BY domain_name")->result();
		foreach ($integration_balance as $vv) {
			$data['integration']['user_balance'] += $vv->total;
		}

		$integration_balance = $this->db->query('SELECT domain_name,sum(amount) as total FROM wallet WHERE comm_from != "ex" AND status = 3 AND '. $where ." GROUP BY domain_name")->result();
		foreach ($integration_balance as $vv) {
			$data['store']['user_balance'] += $vv->total;
		}

		$data_integration_sale  = $this->db->query('SELECT domain_name,SUM(amount) as total,COUNT(*) as total_count FROM `wallet` WHERE type = "sale_commission" AND status > 0 AND comm_from = "ex" AND '. $where .' GROUP BY domain_name' )->result();

		foreach ($data_integration_sale as $vv) {
			$data['integration']['sale'] += $vv->total;
			$data['integration']['total_count'] += $vv->total_count;
			$data['integration']['all'][$vv->domain_name]['sale'] += $vv->total;
			$data['integration']['all'][$vv->domain_name]['total_count'] += $vv->total_count;
		}

		$integration_click_count  = $this->db->query('SELECT base_url,count(*) as total FROM `integration_clicks_action` WHERE '. $where .'  AND is_action=0 GROUP BY base_url' )->result();
		foreach ($integration_click_count as $vv) {
			$data['integration']['click_count'] += $vv->total;
			$data['integration']['all'][$vv->base_url]['click_count'] += $vv->total;
		}

		$integration_click_amount = $this->db->query('SELECT domain_name,SUM(amount) as total FROM `wallet` WHERE type = "external_click_commission" AND is_action=0 AND comm_from = "ex" AND '. $where .' GROUP BY domain_name')->result();
		foreach ($integration_click_amount as $vv) {
			$data['integration']['click_amount'] += $vv->total;
			$data['integration']['all'][$vv->domain_name]['click_amount'] += $vv->total;
		}

		$integration_click_amount = $this->db->query('SELECT domain_name,count(*) as total FROM `wallet` WHERE is_action=1 AND type="external_click_commission" AND status > 0 AND comm_from = "ex" AND '. $where .' GROUP BY domain_name')->result();
		foreach ($integration_click_amount as $vv) {
			$data['integration']['action_count'] += $vv->total;
			$data['integration']['all'][$vv->domain_name]['action_count'] += $vv->total;
		}

		$integration_click_amount = $this->db->query('SELECT domain_name,SUM(amount) as total FROM `wallet` WHERE is_action=1 AND type="external_click_commission" AND status > 0 AND comm_from = "ex" AND '. $where .' GROUP BY domain_name')->result();
		foreach ($integration_click_amount as $vv) {
			$data['integration']['action_amount'] += $vv->total;
			$data['integration']['all'][$vv->domain_name]['action_amount'] += $vv->total;
		}


		$integration_total_orders = $this->db->query('SELECT base_url,count(*) as total,SUM(commission) as commission,SUM(total) as total_amount FROM `integration_orders` WHERE  '. $where .' GROUP BY base_url' )->result();

		foreach ($integration_total_orders as $vv) {
			$data['integration']['total_orders'] += $vv->total;
			$data['integration']['total_orders_amount'] += $vv->total_amount;
			$data['integration']['total_orders_commission'] += $vv->commission;
			$data['integration']['all'][$vv->base_url]['total_orders'] += $vv->total;
		}

		$data['integration']['total_commission'] = ($data['integration']['click_amount'] + $data['integration']['sale'] + $data['integration']['action_amount']);

		$data['total_sale_count'] += $data['integration']['total_orders'] ;

		$data['total_sale_amount'] = $data['total_sale'] + $data['integration']['total_orders_amount'];

		return $data;
	}
	
	public function getTotals($filter = array(),$extraTotals = false, $calling_for = 'admin'){
		$where = ' 1 ';
		$where1 = ' 1 ';		 
		$where_vendor = ' 1 ';		 

		if (isset($filter['date'])) {
			if (strpos($filter['date'], ' - ') !== false) {

				list($start_date, $end_date) = explode(" - ", $filter['date']);

				$start_date = date("Y-m-d", strtotime($start_date));
				$end_date = date("Y-m-d", strtotime($end_date));

				$where .= "  AND DATE(created_at) >= '{$start_date}' AND DATE(created_at) <= '{$end_date}' ";
				$where1 .= "  AND DATE(o.created_at) >= '{$start_date}' AND DATE(o.created_at) <= '{$end_date}' ";
			}
		}

		if (isset($filter['total_commision_filter_month']) && (int)$filter['total_commision_filter_month'] > 0) {
			$where .= "  AND MONTH(created_at) = '". (int)$filter['total_commision_filter_month'] ."' ";
			$where1 .= "  AND MONTH(o.created_at) = '". (int)$filter['total_commision_filter_month'] ."' ";
		}

		if (isset($filter['total_commision_filter_year']) && (int)$filter['total_commision_filter_year'] > 0) {
			$where .= "  AND YEAR(created_at) = '". (int)$filter['total_commision_filter_year'] ."' ";
			$where1 .= "  AND YEAR(o.created_at) = '". (int)$filter['total_commision_filter_year'] ."' ";
		}

		if (isset($filter['integration_data_month']) && (int)$filter['integration_data_month'] > 0) {
			$where .= "  AND MONTH(created_at) = '". (int)$filter['integration_data_month'] ."' ";
			$where1 .= "  AND MONTH(o.created_at) = '". (int)$filter['integration_data_month'] ."' ";
		}

		if (isset($filter['integration_data_year']) && (int)$filter['integration_data_year'] > 0) {
			$where .= "  AND YEAR(created_at) = '". (int)$filter['integration_data_year'] ."' ";
			$where1 .= "  AND YEAR(o.created_at) = '". (int)$filter['integration_data_year'] ."' ";
		}

		if (isset($filter['user_id'])) {
			$where .= '  AND user_id = '. (int)$filter['user_id'];
			$where_vendor = $where1;
			$where1 .= '  AND op.refer_id = '. (int)$filter['user_id'];
			$where_vendor .= '  AND op.vendor_id = '. (int)$filter['user_id'];
		}

		$data['unpaid_commition'] = (float)$this->db->query('SELECT sum(amount) as total FROM wallet WHERE status IN (1,2) AND '. $where)->row_array()['total'];
		$data['total_sale_commi'] = (float)$this->db->query('SELECT sum(amount) as total FROM wallet WHERE status > 0 AND  type IN("sale_commission","vendor_sale_commission") AND '. $where )->row_array()['total'];

		$data['total_in_request'] = (float)$this->db->query('SELECT sum(amount) as total FROM wallet WHERE status=2 AND '. $where )->row_array()['total'];

		$data['total_click_commi'] = (float)$this->db->query('SELECT sum(amount) as total FROM wallet WHERE type = "click_commission" AND status > 0 AND '. $where )->row_array()['total'];

		$data['total_form_click_commi'] = (float)$this->db->query('SELECT sum(amount) as total FROM wallet WHERE type = "form_click_commission" AND status > 0 AND '. $where )->row_array()['total'];

		$data['total_store_m_commission'] = (float)$this->db->query('SELECT sum(amount) as total FROM wallet WHERE type = "store_m_commission" AND '. $where )->row_array()['total'];

		$data['total_affiliate_click_commission'] = (float)$this->db->query('SELECT sum(amount) as total FROM wallet WHERE type = "affiliate_click_commission" AND status > 0 AND '. $where )->row_array()['total'];

		$data['total_no_click'] = (int)$this->db->query('SELECT COUNT(action_id) as total FROM product_action WHERE  '.$where)->row_array()['total'];

		$data['total_no_form_click'] = (int)$this->db->query('SELECT COUNT(action_id) as total FROM form_action WHERE '.$where)->row_array()['total'];

		$data['aff_total_no_click'] = (int)$this->db->query('SELECT COUNT(id) as total FROM affiliate_action WHERE '.$where)->row_array()['total'];

		$data['admin_click_earning'] = (float)$this->db->query('SELECT SUM(amount) as total FROM wallet WHERE reference_id_2 = "vendor_click_commission_for_admin" ')->row_array()['total'];

		$data['all_clicks_comm'] = $data['total_click_commi'] + $data['total_form_click_commi'] + $data['total_affiliate_click_commission'] ;

		$data['all_sale_comm'] = $data['total_sale_commi'];

		if($extraTotals){
			$data['affiliates_program'] = (float)$this->db->query('SELECT COUNT(*) as total FROM affiliateads')->row_array()['total'];
			$data['total_sale_count'] = (float)$this->db->query('SELECT COUNT(op.order_id) as total FROM order_products op LEFT JOIN `order` o on (o.id = op.order_id) WHERE o.status > 0  AND '.$where1 ." GROUP BY o.id ")->row_array()['total'];

			$data['total_sale'] = (float)$this->db->query('SELECT SUM(op.total) as total FROM `order_products` op LEFT JOIN `order` as o ON o.id = op.order_id WHERE o.status = 1 AND '. $where1)->row_array()['total'];
			$data['total_vendor_sale'] = (float)$this->db->query('SELECT SUM(op.total) as total FROM `order_products` op LEFT JOIN `order` as o ON o.id = op.order_id WHERE o.status = 1 AND '. $where_vendor)->row_array()['total'];
			if($calling_for == 'admin'){
				$data['total_sale_balance'] = (float)$this->db->query('SELECT SUM(op.total) as total FROM `order_products` op LEFT JOIN `order` as o ON o.id = op.order_id WHERE o.status = 1 AND vendor_id=0 AND '. $where1)->row_array()['total'];
				$data['total_sale_balance'] += (float)$this->db->query('SELECT SUM(op.admin_commission) as total FROM `order_products` op LEFT JOIN `order` as o ON o.id = op.order_id WHERE o.status = 1 AND vendor_id > 0 AND '. $where1)->row_array()['total'];
			} else {
				$data['total_sale_balance'] = (float)$this->db->query('SELECT SUM(op.total) as total FROM `order_products` op LEFT JOIN `order` as o ON o.id = op.order_id WHERE o.status = 1 AND '. $where1)->row_array()['total'];
			}

			if($calling_for == 'admin'){
				$data['total_sale_week'] = (float)$this->db->query('SELECT SUM(op.total) as total FROM `order` o LEFT JOIN order_products op ON op.order_id = o.id WHERE '. $where1 .' AND vendor_id = 0 AND YEARWEEK(o.`created_at`, 1) = YEARWEEK(CURDATE(), 1) AND o.status  = 1')->row_array()['total'];
				$data['total_sale_week'] += (float)$this->db->query('SELECT SUM(op.admin_commission) as total FROM `order` o LEFT JOIN order_products op ON op.order_id = o.id WHERE '. $where1 .' AND vendor_id > 0 AND YEARWEEK(o.`created_at`, 1) = YEARWEEK(CURDATE(), 1) AND o.status  = 1')->row_array()['total'];
				$data['total_sale_month'] = (float)$this->db->query('SELECT SUM(op.total) as total FROM `order` o LEFT JOIN order_products op ON op.order_id = o.id WHERE '. $where1 .' AND vendor_id = 0 AND MONTH(o.`created_at`) = MONTH(NOW())  AND o.status  = 1')->row_array()['total'];
				$data['total_sale_month'] += (float)$this->db->query('SELECT SUM(op.admin_commission) as total FROM `order` o LEFT JOIN order_products op ON op.order_id = o.id WHERE '. $where1 .' AND vendor_id > 0 AND MONTH(o.`created_at`) = MONTH(NOW())  AND o.status  = 1')->row_array()['total'];
				$data['total_sale_year'] = (float)$this->db->query('SELECT SUM(op.total) as total FROM `order` o LEFT JOIN order_products op ON op.order_id = o.id WHERE '. $where1 .' AND vendor_id = 0 AND YEAR(o.`created_at`) = '.date("Y").' AND o.status  = 1')->row_array()['total'];
				$data['total_sale_year'] += (float)$this->db->query('SELECT SUM(op.admin_commission) as total FROM `order` o LEFT JOIN order_products op ON op.order_id = o.id WHERE '. $where1 .' AND vendor_id > 0 AND YEAR(o.`created_at`) = '.date("Y").' AND o.status  = 1')->row_array()['total'];


				$data['admin_click_earning_week'] = (float)$this->db->query('SELECT SUM(amount) as total FROM wallet WHERE reference_id_2 = "vendor_click_commission_for_admin" AND YEARWEEK(`created_at`, 1) = YEARWEEK(CURDATE(), 1) ')->row_array()['total'];
				$data['admin_click_earning_month'] = (float)$this->db->query('SELECT SUM(amount) as total FROM wallet WHERE reference_id_2 = "vendor_click_commission_for_admin" AND MONTH(`created_at`) = MONTH(NOW()) ')->row_array()['total'];
				$data['admin_click_earning_year'] = (float)$this->db->query('SELECT SUM(amount) as total FROM wallet WHERE reference_id_2 = "vendor_click_commission_for_admin" AND YEAR(`created_at`) = '.date("Y").' ')->row_array()['total'];


				$data['admin_total_no_click'] = (int)$this->db->query('SELECT COUNT(action_id) as total FROM product_action_admin ')->row_array()['total'];

			} else {
				$data['total_sale_week'] = (float)$this->db->query('SELECT SUM(op.total) as total FROM `order` o LEFT JOIN order_products op ON op.order_id = o.id WHERE vendor_id = 0 AND '. $where1 .' AND YEARWEEK(o.`created_at`, 1) = YEARWEEK(CURDATE(), 1) AND o.status  = 1')->row_array()['total'];
				$data['total_sale_week'] += (float)$this->db->query('SELECT SUM(op.commission) as total FROM `order` o LEFT JOIN order_products op ON op.order_id = o.id WHERE vendor_id > 0 AND '. $where1 .' AND YEARWEEK(o.`created_at`, 1) = YEARWEEK(CURDATE(), 1) AND o.status  = 1')->row_array()['total'];
				$data['total_sale_week'] += (float)$this->db->query('SELECT SUM(op.vendor_commission) as total FROM `order` o LEFT JOIN order_products op ON op.order_id = o.id WHERE '. $where_vendor .' AND YEARWEEK(o.`created_at`, 1) = YEARWEEK(CURDATE(), 1) AND o.status  = 1')->row_array()['total'];

				$data['total_sale_month'] = (float)$this->db->query('SELECT SUM(op.total) as total FROM `order` o LEFT JOIN order_products op ON op.order_id = o.id WHERE vendor_id = 0 AND '. $where1 .' AND MONTH(o.`created_at`) = MONTH(NOW())  AND o.status  = 1')->row_array()['total'];
				$data['total_sale_month'] += (float)$this->db->query('SELECT SUM(op.commission) as total FROM `order` o LEFT JOIN order_products op ON op.order_id = o.id WHERE vendor_id > 0 AND '. $where1 .' AND MONTH(o.`created_at`) = MONTH(NOW())  AND o.status  = 1')->row_array()['total'];
				$data['total_sale_month'] += (float)$this->db->query('SELECT SUM(op.vendor_commission) as total FROM `order` o LEFT JOIN order_products op ON op.order_id = o.id WHERE '. $where_vendor .' AND MONTH(o.`created_at`) = MONTH(NOW())  AND o.status  = 1')->row_array()['total'];

				$data['total_sale_year'] = (float)$this->db->query('SELECT SUM(op.total) as total FROM `order` o LEFT JOIN order_products op ON op.order_id = o.id WHERE vendor_id = 0 AND '. $where1 .' AND YEAR(o.`created_at`) = '.date("Y").' AND o.status  = 1')->row_array()['total'];
				$data['total_sale_year'] += (float)$this->db->query('SELECT SUM(op.commission) as total FROM `order` o LEFT JOIN order_products op ON op.order_id = o.id WHERE vendor_id > 0 AND '. $where1 .' AND YEAR(o.`created_at`) = '.date("Y").' AND o.status  = 1')->row_array()['total'];
				$data['total_sale_year'] += (float)$this->db->query('SELECT SUM(op.vendor_commission) as total FROM `order` o LEFT JOIN order_products op ON op.order_id = o.id WHERE '. $where_vendor .' AND YEAR(o.`created_at`) = '.date("Y").' AND o.status  = 1')->row_array()['total'];

				$data['admin_total_no_click'] = (int)$this->db->query('SELECT COUNT(action_id) as total FROM product_action_admin WHERE product_id IN (SELECT product_id FROM product_affiliate WHERE user_id = '. (int)$filter['user_id'] .' ) ')->row_array()['total'];

					$data['total_no_click'] += (int)$this->db->query('SELECT COUNT(pa.action_id) as total FROM product_action pa LEFT JOIN product_affiliate paff ON (paff.product_id = pa.product_id) WHERE paff.user_id=   '. (int)$filter['user_id'])->row_array()['total'];

				}

			$data['all_clicks'] = $data['total_no_click'] + $data['total_no_form_click'] + $data['aff_total_no_click'];
			$data['vendor_order_count'] += (float)$this->db->query('SELECT COUNT(op.id) as total FROM `order` o LEFT JOIN order_products op ON op.order_id = o.id WHERE '. $where_vendor .' AND op.vendor_id > 0 AND o.status > 0')->row_array()['total'];
			$data['vendor_order_count'] += (float)$this->db->query('SELECT COUNT(id) as total FROM `integration_orders`  WHERE '. $where .'  AND status > 0')->row_array()['total'];

			$data['total_paid'] = (float)$this->db->query('SELECT sum(amount) as total FROM wallet WHERE status=3 AND '. $where)->row_array()['total'];

			$data['total_paid_commition'] = (float)$this->db->query('SELECT sum(amount) as total FROM wallet WHERE status=3 AND '. $where )->row_array()['total'];
			$data['paid_commition'] = (float)$this->db->query('SELECT sum(amount) as total FROM wallet WHERE status=3 AND type IN("click_commission","sale_commission") AND '. $where )->row_array()['total'];
			$data['requested_commition'] = (float)$this->db->query('SELECT sum(amount) as total FROM wallet WHERE status=2 AND type IN("click_commission","sale_commission") AND '. $where )->row_array()['total'];
			$data['aff_paid_commition'] = (float)$this->db->query('SELECT sum(amount) as total FROM wallet WHERE status=3 AND type IN("affiliate_click_commission") AND '. $where )->row_array()['total'];
			$data['aff_unpaid_commition'] = (float)$this->db->query('SELECT sum(amount) as total FROM wallet WHERE status=1 AND type IN("affiliate_click_commission") AND '. $where )->row_array()['total'];
			$data['aff_requested_commition'] = (float)$this->db->query('SELECT sum(amount) as total FROM wallet WHERE status=2 AND type IN("affiliate_click_commission") AND '. $where )->row_array()['total'];

			$data['form_paid_commition'] = (float)$this->db->query('SELECT sum(amount) as total FROM wallet WHERE status=3 AND type IN("form_click_commission") AND '. $where )->row_array()['total'];
			$data['form_unpaid_commition'] = (float)$this->db->query('SELECT sum(amount) as total FROM wallet WHERE status=1 AND type IN("form_click_commission") AND '. $where )->row_array()['total'];
			$data['form_requested_commition'] = (float)$this->db->query('SELECT sum(amount) as total FROM wallet WHERE status=2 AND type IN("form_click_commission") AND '. $where )->row_array()['total'];

			$data['total_transaction'] = (float)$this->db->query('SELECT count(amount) as total FROM wallet WHERE 1 AND '. $where )->row_array()['total'];

			$data['wallet_on_hold_amount'] = $data['wallet_on_hold_count'] = $data['wallet_request_sent_amount'] = $data['wallet_request_sent_count'] = $data['wallet_accept_amount'] = $data['wallet_accept_count'] = $data['wallet_cancel_amount'] = $data['wallet_cancel_count'] = $data['wallet_trash_amount'] = $data['wallet_trash_count'] = 0;

			$query = $this->db->query('SELECT sum(amount) as amount,count(`status`) as counts,`status` FROM `wallet` WHERE '. $where .' GROUP BY `status`')->result_array();
			foreach ($query as $key => $value) {
				switch ($value['status']) {
					case '0':
					$data['wallet_on_hold_amount'] = (float)$value['amount'];
					$data['wallet_on_hold_count'] = (float)$value['counts'];
					break;
					case '1':
					$data['wallet_unpaid_amount'] = (float)$value['amount'];
					$data['wallet_unpaid_count'] = (float)$value['counts'];
					break;
					case '2':
					$data['wallet_request_sent_amount'] = (float)$value['amount'];
					$data['wallet_request_sent_count'] = (float)$value['counts'];
					break;
					case '3':
					$data['wallet_accept_amount'] = (float)$value['amount'];
					$data['wallet_accept_count'] = (float)$value['counts'];
					break;
					default: break;
				}
			}

			$query = $this->db->query('SELECT sum(amount) as amount,count(`commission_status`) as counts,`commission_status` FROM `wallet` WHERE '. $where .' GROUP BY `commission_status`')->result_array();
			foreach ($query as $key => $value) {
				switch ($value['commission_status']) {
					case '1':
					$data['wallet_cancel_amount'] = (float)$value['amount'];
					$data['wallet_cancel_count'] = (float)$value['counts'];
					break;
					case '2':
					$data['wallet_trash_amount'] = (float)$value['amount'];
					$data['wallet_trash_count'] = (float)$value['counts'];
					break;
					default: break;
				}
			}

			$query = $this->db->query('SELECT sum(amount) as amount,count(`status`) as counts,`status` FROM `wallet` WHERE type IN ("vendor_sale_commission") AND  '. $where .' GROUP BY `status`')->result_array();
			foreach ($query as $key => $value) {
				switch ($value['status']) {
					case '0':
					$data['vendor_wallet_on_hold_amount'] = (float)$value['amount'];
					$data['vendor_wallet_on_hold_count'] = (float)$value['counts'];
					break;
					case '1':
					$data['vendor_wallet_unpaid_amount'] = (float)$value['amount'];
					$data['vendor_wallet_unpaid_count'] = (float)$value['counts'];
					break;
					case '2':
					$data['vendor_wallet_request_sent_amount'] = (float)$value['amount'];
					$data['vendor_wallet_request_sent_count'] = (float)$value['counts'];
					break;
					case '3':
					$data['vendor_wallet_accept_amount'] = (float)$value['amount'];
					$data['vendor_wallet_accept_count'] = (float)$value['counts'];
					break;
					default: break;
				}
			}

			$query = $this->db->query('SELECT sum(amount) as amount,count(`commission_status`) as counts,`commission_status` FROM `wallet` WHERE type IN ("vendor_sale_commission") AND  '. $where .' GROUP BY `commission_status`')->result_array();
			foreach ($query as $key => $value) {
				switch ($value['commission_status']) {
					case '1':
					$data['vendor_wallet_cancel_amount'] = (float)$value['amount'];
					$data['vendor_wallet_cancel_count'] = (float)$value['counts'];
					break;
					case '2':
					$data['vendor_wallet_trash_amount'] = (float)$value['amount'];
					$data['vendor_wallet_trash_count'] = (float)$value['counts'];
					break;
					default: break;
				}
			}


			$integration_balance_week = $this->db->query("SELECT sum(total) as total FROM integration_orders WHERE ". $where ." AND (vendor_id=0 OR vendor_id is NULL) AND YEARWEEK(`created_at`, 1) = YEARWEEK(CURDATE(), 1)")->row_array()['total'];
			$integration_balance_week += $this->db->query("SELECT sum(amount) as total FROM wallet WHERE type='admin_sale_commission' AND comm_from='ex' AND user_id=1 AND YEARWEEK(`created_at`, 1) = YEARWEEK(CURDATE(), 1)")->row_array()['total'];

			

			$integration_balance_month = $this->db->query("SELECT sum(total) as total FROM integration_orders WHERE ". $where ." AND (vendor_id=0 OR vendor_id is NULL) AND  MONTH(`created_at`) = MONTH(NOW())")->row_array()['total'];
			$integration_balance_month += $this->db->query("SELECT sum(amount) as total FROM wallet WHERE type='admin_sale_commission' AND comm_from='ex' AND user_id=1 AND MONTH(`created_at`) = MONTH(NOW())")->row_array()['total'];
			

			$integration_balance_year = $this->db->query('SELECT sum(total) as total FROM integration_orders WHERE '. $where .' AND (vendor_id=0 OR vendor_id is NULL) AND YEAR(created_at) = '.date("Y"))->row_array()['total'];
			$integration_balance_year += $this->db->query("SELECT sum(amount) as total FROM wallet WHERE type='admin_sale_commission' AND comm_from='ex' AND user_id=1 AND YEAR(created_at) = " .date("Y"))->row_array()['total'];

			$data['integration']['hold_action_count'] = 0;
			$data['integration']['hold_orders'] = 0;

			$integration_balance = $this->db->query('SELECT vendor_id,base_url,total,(SELECT amount FROM wallet WHERE integration_orders.admin_tran = wallet.id) as admin_comm FROM integration_orders WHERE  '. $where ."  ")->result();

			foreach ($integration_balance as $vv) {
				if((int)$vv->vendor_id == 0){
					$data['integration']['balance'] += $vv->total;
					$data['integration']['all'][$vv->base_url]['balance'] += $vv->total;
				} else{
					$data['integration']['balance'] += $vv->admin_comm;
					$data['integration']['all'][$vv->base_url]['balance'] += $vv->admin_comm;
				}

			}


			$integration_balance = $this->db->query('SELECT domain_name,sum(amount) as total FROM wallet WHERE comm_from = "ex" AND status = 3 AND '. $where ." GROUP BY domain_name")->result();
			foreach ($integration_balance as $vv) {
				$data['integration']['user_balance'] += $vv->total;
			}

			$integration_balance = $this->db->query('SELECT domain_name,sum(amount) as total FROM wallet WHERE comm_from != "ex" AND status = 3 AND '. $where ." GROUP BY domain_name")->result();
			foreach ($integration_balance as $vv) {
				$data['store']['user_balance'] += $vv->total;
			}

			$data_integration_sale  = $this->db->query('SELECT domain_name,SUM(amount) as total,COUNT(*) as total_count FROM `wallet` WHERE type = "sale_commission" AND status > 0 AND comm_from = "ex" AND '. $where .' GROUP BY domain_name' )->result();

			foreach ($data_integration_sale as $vv) {
				$data['integration']['sale'] += $vv->total;
				$data['integration']['total_count'] += $vv->total_count;
				$data['integration']['all'][$vv->domain_name]['sale'] += $vv->total;
				$data['integration']['all'][$vv->domain_name]['total_count'] += $vv->total_count;
			}

			$integration_click_count  = $this->db->query('SELECT base_url,count(*) as total FROM `integration_clicks_action` WHERE '. $where .'  AND is_action=0 GROUP BY base_url' )->result();
			foreach ($integration_click_count as $vv) {
				$data['integration']['click_count'] += $vv->total;
				$data['integration']['all'][$vv->base_url]['click_count'] += $vv->total;
			}

			$integration_click_amount = $this->db->query('SELECT domain_name,SUM(amount) as total FROM `wallet` WHERE type = "external_click_commission" AND is_action=0 AND comm_from = "ex" AND '. $where .' GROUP BY domain_name')->result();
			foreach ($integration_click_amount as $vv) {
				$data['integration']['click_amount'] += $vv->total;
				$data['integration']['all'][$vv->domain_name]['click_amount'] += $vv->total;
			}

			$data['admin_transaction'] = $this->db->query('SELECT SUM(amount) as total  FROM `wallet` WHERE type="admin_transaction" AND  '. $where .'')->row()->total;


			$integration_click_amount = $this->db->query('SELECT domain_name,count(*) as total FROM `wallet` WHERE is_action=1 AND type="external_click_commission" AND status = 0 AND comm_from = "ex" AND '. $where .' GROUP BY domain_name')->result();
			foreach ($integration_click_amount as $vv) {
				$data['integration']['hold_action_count'] += $vv->total;
				$data['integration']['all'][$vv->domain_name]['hold_action_count'] += $vv->total;
			}

			$integration_click_amount = $this->db->query('SELECT domain_name,count(*) as total FROM `wallet` WHERE is_action=1 AND type="external_click_commission" AND status > 0 AND comm_from = "ex" AND '. $where .' GROUP BY domain_name')->result();
			foreach ($integration_click_amount as $vv) {
				$data['integration']['action_count'] += $vv->total;
				$data['integration']['all'][$vv->domain_name]['action_count'] += $vv->total;
			}

			$integration_click_amount = $this->db->query('SELECT domain_name,SUM(amount) as total FROM `wallet` WHERE is_action=1 AND type="external_click_commission" AND status > 0 AND comm_from = "ex" AND '. $where .' GROUP BY domain_name')->result();
			foreach ($integration_click_amount as $vv) {
				$data['integration']['action_amount'] += $vv->total;
				$data['integration']['all'][$vv->domain_name]['action_amount'] += $vv->total;
			}

			$integration_total_orders = $this->db->query('SELECT base_url,count(*) as total,SUM(commission) as commission,SUM(total) as total_amount FROM `integration_orders` WHERE  '. $where .' GROUP BY base_url' )->result();

			foreach ($integration_total_orders as $vv) {
				$data['integration']['total_orders'] += $vv->total;
				$data['integration']['total_orders_amount'] += $vv->total_amount;
				$data['integration']['total_orders_commission'] += $vv->commission;
				$data['integration']['all'][$vv->base_url]['total_orders'] += $vv->total;
			}


			$data['integration']['total_commission'] = ($data['integration']['click_amount'] + $data['integration']['sale'] + $data['integration']['action_amount']);

			$data['store']['hold_orders'] = (int)$this->db->query('SELECT count(*) as total FROM `order` WHERE '. $where .' AND status IN (1,7)')->row_array()['total'];

			$data['integration']['hold_orders'] = (int)$this->db->query('SELECT count(*) as total FROM `wallet` WHERE type="sale_commission" AND '. $where .' AND status  = 0')->row_array()['total'];

			$data['total_sale_count']                += $data['integration']['total_orders'] ;


			$data['store']['balance']                = $data['total_sale'];
			$data['store']['sale']                   = ($data['all_sale_comm'] - $data['integration']['sale']);
			

			$data['store']['click_count']            = ($data['all_clicks'] - $data['integration']['click_count']);
			$data['store']['click_amount']           = ($data['all_clicks_comm'] - $data['integration']['click_amount']);
			$data['store']['total_commission']       = ($data['store']['click_amount'] + $data['store']['sale']);

			$data['total_sale_amount'] = $data['total_sale'] + $data['integration']['total_orders_amount'];

			$data['total_balance'] = $data['total_sale'] + $data['integration']['balance'];
			$data['weekly_balance'] =  $data['admin_click_earning_week'] +  $data['total_sale_week'] + $integration_balance_week;
			$data['monthly_balance'] =  $data['admin_click_earning_month'] +  $data['total_sale_month'] + $integration_balance_month;
			$data['yearly_balance'] =  $data['admin_click_earning_year'] +  $data['total_sale_year'] + $integration_balance_year;
		}


		return $data;
	}

	public function addTransactionRecursion($data){

		$transaction_id = $data['transaction_id'];
		
		$data['endtime'] = ($data['setCustomTime'] =='true' && $data['endtime']) ? date("Y-m-d H:i:00",strtotime($data['endtime'])) : '0000-00-00 00:00:00.000000';

		if (isset($data['force_recursion_endtime'])) {
			$data['endtime'] = ($data['force_recursion_endtime']) ? date("Y-m-d H:i:s",strtotime($data['force_recursion_endtime'])) : '0000-00-00 00:00:00.000000';
		}

		unset($data['force_recursion_endtime']);
		unset($data['setCustomTime']);

		$type = $data['type'];
		
		$json = [];

		if($type == ''){
			$this->db->query('DELETE FROM wallet_recursion WHERE transaction_id='.$transaction_id);
			$json['status']= 'removed';
		} else {

			$data['next_transaction'] = $this->next_transaction_date($data['type'], $data['custom_time']);		

			$last_transaction = $this->db->query('SELECT * FROM wallet_recursion WHERE transaction_id='.$transaction_id)->row();

			if ( !empty($last_transaction) ) {
				$data['last_transaction'] = (!isset($last_transaction->next_transaction) || empty($last_transaction->next_transaction)) ? '0000-00-00 00:00:00.000000' : $last_transaction->next_transaction;
				$this->UpdateTransactionRecursion($data);
			} else {
				$data['last_transaction'] = '0000-00-00 00:00:00.000000';
				$data['status'] = 1;
				$this->db->insert('wallet_recursion',$data);
			}

			$next_transaction = $data['next_transaction'];

			$json['status'] = 'added';
		}

		$next_transaction = $this->db->query('SELECT next_transaction FROM wallet_recursion WHERE transaction_id='.$transaction_id.'  ORDER BY id DESC LIMIT 1' )->row()->next_transaction;

		$total_recurring = $this->db->query("SELECT count(*) as total,SUM(amount) as total_amount FROM wallet WHERE parent_id = {$transaction_id} ")->row();

		$json['button'] = "<span class='badge bg-primary p-2'>". cycle_details((int)$total_recurring->total, $next_transaction, $data['endtime'],(float)$total_recurring->total_amount) ."</span>";

		return $json;
	}

	public function addTransactionRecursionBatch($transactions){
		$recData = [];
		foreach ($transactions as $tran) {
			$data = $tran;

			$transaction_id = (int)$data['transaction_id'];

			$data['endtime'] = (isset($data['force_recursion_endtime']) && !empty($data['force_recursion_endtime'])) ? date("Y-m-d H:i:s",strtotime($data['force_recursion_endtime'])) : '0000-00-00 00:00:00.000000';

			unset($data['force_recursion_endtime']);

			unset($data['setCustomTime']);

			$type = $data['type'];
			
			$json = [];

			if($type == ''){
				$this->db->query('UPDATE wallet_recursion SET type="", endtime="0000-00-00 00:00:00.000000", status=0 WHERE transaction_id='.$transaction_id);
			} else {
				$data['next_transaction'] = $this->next_transaction_date($data['type'], $data['custom_time']);
				$data['last_transaction'] = '0000-00-00 00:00:00.000000';
				$data['status'] = 1;
				$recData[] = $data;
			}
		}

		if(! empty($recData)) {
			$this->db->insert_batch('wallet_recursion', $recData);
		}

	}

	public function GetTransactionRecursion($transaction_id){
		$row = $this->db->query("SELECT * FROM wallet_recursion WHERE transaction_id='$transaction_id'")->row_array();	


		return $row;
	}

	public function UpdateTransactionRecursion($data){
		$transaction_id = $data['transaction_id'];
		$custom_time = $data['custom_time'];
		$next_transaction = $data['next_transaction'];

		$type = $data['type'];


		$this->db->set("type", $type)
		->set("custom_time", $custom_time)                 
		->set("status", 1)                 
		->set("next_transaction", $next_transaction)                 
		->set("endtime", $data['endtime'])                 
		->where( 'transaction_id' , $transaction_id );

		if( $this->db->update('wallet_recursion') ){
			return true;
		}       

		return false;

	}

public function CronTransaction(){
    $today = date('Y-m-d H:i:s'); 		     

    $results = $this->db->query("SELECT * FROM wallet_recursion WHERE status=1 AND (next_transaction <= endtime OR endtime IS NULL OR endtime = '0000-00-00 00:00:00.000000') AND next_transaction <= '$today' LIMIT 100");

    if ( $results->num_rows () > 0 ) {
        $results = $results->result_array();
        $wallet_payment_batch = [];
        foreach ($results as $recursion) {
            $transaction_id = $recursion['transaction_id'];			

            $wallet_payment = $this->db->query("SELECT * FROM wallet WHERE id = '$transaction_id'")->row_array();
            if(!empty($wallet_payment['id'])) {
                $wallet_payment['parent_id'] = $wallet_payment['id'];
                $wallet_payment['created_at'] = date("Y-m-d H:i:s");
                $wallet_payment['status'] = (int)$wallet_payment['status'] > 1 ? 1 : $wallet_payment['status'];
                $wallet_payment['id'] = "";	
                $wallet_payment_batch[] = $wallet_payment;
                if( $this->db->insert('wallet',$wallet_payment)){
                    $transaction_id = $recursion['transaction_id'];

                    $next_transaction = $this->next_transaction_date($recursion['type'],$recursion['custom_time']);
                    $this->db->set("last_transaction", $recursion['next_transaction'])              
                    ->set("next_transaction", $next_transaction)                 
                    ->where ( 'transaction_id' , $transaction_id );

                    $this->db->update('wallet_recursion');
                }
            } else {
                log_message('debug', 'Transaction ID: ' . $transaction_id);
                log_message('debug', 'Wallet Payment: ' . print_r($wallet_payment, true));
            }
        }

        return true;
    }
    return false;	
}


	public function next_transaction_date($type, $minutes = null){	

		$today = strtotime( date('Y-m-d H:i:s') ); 
		
		if($type == 'every_day') {   
			$next_date = date('Y-m-d H:i:s', strtotime("+1 day", $today));
		}else if($type == 'every_week'){
			$next_date = date('Y-m-d H:i:s', strtotime("+7 day", $today));
		}else if($type == 'every_month'){
			$next_date = date('Y-m-d H:i:s', strtotime("+1 month", $today));
		}else if($type == 'every_year'){
			$next_date = date('Y-m-d H:i:s', strtotime("+1 year", $today));
		}else if($type == 'custom_time'){   
			$next_date = date('Y-m-d H:i:s', strtotime("+".$minutes." minutes", $today));
		}


		return $next_date;
	}


	public function getAllTransactionFilter($userdetails){
		$sql = "SELECT 	`all_transaction`.`module`,
		`all_transaction`.`id`,
		`all_transaction`.`user_id`,
		`all_transaction`.`username`,
		`all_transaction`.`price`,
		`all_transaction`.`payment_gateway`,
		`all_transaction`.`payment_detail`,
		`all_transaction`.`status_id`,
		`all_transaction`.`datetime`
		FROM (SELECT 'deposit' as 'module',
			`vendor_deposit`.`vd_id` as `id`,
			`vendor_deposit`.`vd_user_id` as `user_id`,
			`users`.`username`,
			`vendor_deposit`.`vd_amount` as 'price',
			`vendor_deposit`.`vd_payment_method` as 'payment_gateway',
			`vendor_deposit`.`vd_txn_id` as 'payment_detail',
			`vendor_deposit`.`vd_status` as 'status_id',
			`vendor_deposit`.`vd_created_on` as 'datetime'
			FROM `vendor_deposit`
			INNER JOIN `users`
			ON `users`.`id` = `vendor_deposit`.`vd_user_id`

			UNION ALL

			SELECT 	'membership' as 'module',
			`membership_user`.`id`,
			`membership_user`.`user_id`,
			`users`.`username`,
			`membership_user`.`total` as 'price',
			`membership_user`.`payment_method` as 'payment_gateway',
			`membership_user`.`payment_details` as 'payment_detail',
			`membership_user`.`status_id`,
			`membership_user`.`created_at` as 'datetime'
			FROM `membership_user`
			INNER JOIN `users`
			ON `users`.`id` = `membership_user`.`user_id`

			UNION ALL

			SELECT 	'store' as 'module',
			`order`.`id`,
			`order`.`user_id`,
			`users`.`username`,
			`order`.`total` as 'price',
			`order`.`payment_method` as 'payment_gateway',
			`order`.`txn_id` as 'payment_detail',
			`order`.`status` as 'status_id',
			`order`.`created_at` as 'datetime'
			FROM `order`
			INNER JOIN `users`
			ON `users`.`id` = `order`.`user_id`) as `all_transaction` ";
		
		if($userdetails['type'] != 'admin'){
			$sql .= "WHERE `all_transaction`.`user_id` = ?
			ORDER BY `all_transaction`.`datetime` desc ";
			$query = $this->db->query($sql,(int) $userdetails['id']);
		} else {
			$sql .= "ORDER BY `all_transaction`.`datetime` desc ";
			$query = $this->db->query($sql);
		}
		$res = $query->result_array();

		foreach($res as $key => $value){
			$result['user'][$value['user_id']] = $value['username'];

			$payment_gateway = strtolower(str_replace(' ','_',$value['payment_gateway']));
			$result['payment_gateway'][$payment_gateway] = __('admin.'.$payment_gateway);

			switch($value['module']){
				case 'deposit':
				$status_text = strip_tags(withdrwal_status($value['status_id']));
				break;
				case 'membership':
				$status_text = strip_tags(membership_withdrwal_status($value['status_id']));
				break;
				case 'store':
				$status_text = strip_tags(store_withdrwal_status($value['status_id']));
				break;
			}
			$result['status'][$value['module'].'_'.$value['status_id']] = $status_text.' ('.__('admin.'.$value['module']).')';
		}

		return $result;
	}

	public function getAllTransaction($userdetails,$filter,$limit = false, $justCount = false){
		$array = [];

		$sql = "SELECT 	`all_transaction`.`module`,
		`all_transaction`.`id`,
		`all_transaction`.`user_id`,
		`all_transaction`.`username`,
		`all_transaction`.`price`,
		`all_transaction`.`payment_gateway`,
		`all_transaction`.`payment_detail`,
		`all_transaction`.`status_id`,
		`all_transaction`.`datetime`
		FROM (SELECT 'deposit' as 'module',
			`vendor_deposit`.`vd_id` as `id`,
			`vendor_deposit`.`vd_user_id` as `user_id`,
			`users`.`username`,
			`vendor_deposit`.`vd_amount` as 'price',
			`vendor_deposit`.`vd_payment_method` as 'payment_gateway',
			`vendor_deposit`.`vd_txn_id` as 'payment_detail',
			`vendor_deposit`.`vd_status` as 'status_id',
			`vendor_deposit`.`vd_created_on` as 'datetime'
			FROM `vendor_deposit`
			INNER JOIN `users`
			ON `users`.`id` = `vendor_deposit`.`vd_user_id`

			UNION ALL

			SELECT 	'membership' as 'module',
			`membership_user`.`id`,
			`membership_user`.`user_id`,
			`users`.`username`,
			`membership_user`.`total` as 'price',
			`membership_user`.`payment_method` as 'payment_gateway',
			`membership_user`.`payment_details` as 'payment_detail',
			`membership_user`.`status_id`,
			`membership_user`.`created_at` as 'datetime'
			FROM `membership_user`
			INNER JOIN `users`
			ON `users`.`id` = `membership_user`.`user_id`

			UNION ALL

			SELECT 	'store' as 'module',
			`order`.`id`,
			`order`.`user_id`,
			`users`.`username`,
			`order`.`total` as 'price',
			`order`.`payment_method` as 'payment_gateway',
			`order`.`txn_id` as 'payment_detail',
			`order`.`status`,
			`order`.`created_at` as 'datetime'
			FROM `order`
			INNER JOIN `users`
			ON `users`.`id` = `order`.`user_id`) as `all_transaction`
		WHERE 1 ";
		
		if($filter['module']){
			$sql .= "AND `all_transaction`.`module` = ? ";
			array_push($array,$filter['module']);
		}

		if($userdetails['type'] == 'admin'){
			if($filter['user']){
				$sql .= "AND `all_transaction`.`user_id` = ? ";
				array_push($array,(int) $filter['user']);
			}
		} else {
			$sql .= "AND `all_transaction`.`user_id` = ? ";
			array_push($array,(int) $userdetails['id']);
		}

		if($filter['date']){
			$sql .= "AND DATE_FORMAT(`all_transaction`.`datetime`,'%Y-%m-%d') = ? ";
			array_push($array,date('Y-m-d',strtotime($filter['date'])));
		}

		if($filter['payment_gateway']){
			$sql .= "AND ( `all_transaction`.`payment_gateway` = ? OR `all_transaction`.`payment_gateway` = ? ) ";
			array_push($array,$filter['payment_gateway'],__('admin.'.$filter['payment_gateway']));
		}

		if($filter['status']){
			$sql .= "AND `all_transaction`.`module` = ? AND `all_transaction`.`status_id` = ? ";
			$module_status = explode('_',$filter['status']);
			array_push($array,$module_status[0],$module_status[1]);
		}

		if($filter['transaction']){
			$sql .= "AND `all_transaction`.`payment_detail` LIKE CONCAT('%',?,'%') ";
			array_push($array,$filter['transaction']);
		}

		$sql .= "ORDER BY `all_transaction`.`datetime` desc ";

		if($limit){
			$page = ($filter['page']) ? $filter['page'] - 1 : 0;
			$sql .= "LIMIT ? OFFSET ? ";
			array_push($array,(int) $limit,(int) $page * $limit);
		}
		
		$query = $this->db->query($sql,$array);
		$result = $query->result_array();

		return $result;
	}



	public function getUncompletedPayment($filter, $justCount = false){
		$array = [];

		$sql = "SELECT 
			uncompleted_payment.*, 
			users.username, users.firstname, users.lastname, users.email, users.phone
			FROM uncompleted_payment LEFT JOIN users ON users.id = uncompleted_payment.user_id WHERE users.id IS NOT NULL and uncompleted_payment.completed_id=0 ";

		if(isset($filter['module']) && ! empty($filter['module'])){
			$sql .= " AND uncompleted_payment.payment_module=".$filter['module']." ";
		}

		if(isset($filter['user']) && ! empty($filter['user'])){
			$sql .= " AND uncompleted_payment.user_id=".$filter['user']." ";
		}

		if(isset($filter['date']) && ! empty($filter['date'])){
			$sql .= " AND DATE_FORMAT(`uncompleted_payment`.`datetime`,'%Y-%m-%d') = ? ";
			array_push($array,date('Y-m-d',strtotime($filter['date'])));
		}

		$sql .= " ORDER BY id desc ";

		if(! $justCount && isset($filter['limit'])){
			$page = ($filter['page']) ? $filter['page'] - 1 : 0;
			$sql .= "LIMIT ? OFFSET ? ";
			array_push($array,(int) $filter['limit'],(int) $page * $filter['limit']);
		}
		
		$query = $this->db->query($sql,$array);
		
		if($justCount) {
			$result = $query->num_rows();
		} else {
			$result = $query->result_array();
		}

		return $result;
	}

	public function getVendorClick($vendor_id){
		$sql = "SELECT `wallet`.`created_at`,
		`wallet`.`comment`,
		`wallet`.`status`,
		`wallet`.`amount`,
		`wallet`.`ip_details`
		FROM `wallet`
		WHERE `type` = 'click_commission'
		AND `reference_id_2` = 'vendor_click_commission'
		AND `wallet`.`group_id` IN (
			SELECT `wallet`.`group_id`
			FROM `wallet`
			WHERE `type` = 'click_commission'
			AND `reference_id_2` = 'vendor_pay_click_commission'
			AND `comm_from` = 'store'
			AND `user_id` = ?
		) ";
		$query = $this->db->query($sql,(int) $vendor_id);
		$result = $query->result_array();

		return $result;
	}

	public function getUserPlan($user_id){
		$sql = "SELECT * FROM `membership_user` WHERE `user_id` = '".$user_id."' ORDER BY `created_at` DESC";

		$query = $this->db->query($sql);
		$result = $query->result_array();

		return $result;
	}


	public function prepareUncompletedPaymentData($uncompleted_payments, $depositStatusList)
	{
		foreach ($uncompleted_payments as &$value) {
			$contents = explode(" || ", $value['content']);

			$newContents = [];

			if((int)$value['payment_module'] == 1) {
				if(isset($contents[0])) {
					$newContents['order'] = unserialize($contents[0]);
					$value['ammount'] = $newContents['order']['total'];
				}
				if(isset($contents[1])) {
				    $newContents['products'] = unserialize($contents[1]);

				    if (is_array($newContents['products']) || is_countable($newContents['products'])) {
				        for ($i = 0; $i < sizeof($newContents['products']); $i++) { 
				            $newContents['products'][$i]['product_name'] = $this->db->query('SELECT product_name FROM product WHERE product_id=' . (int)$newContents['products'][$i]['product_id'])->row_array()['product_name'];
				        }
				    } else {
				        log_message('debug', 'unserialize returned a non-array value. Serialized value: ' . $contents[1]);
				        $newContents['products'] = [];
				    }
				} else {
				    log_message('debug', 'Contents[1] is not set.');
				}

				if(isset($contents[2])) {
					$newContents['payment'] = unserialize($contents[2]);
				}
			} else if((int)$value['payment_module'] == 2) {
				if(isset($contents[0])) {
					$newContents['deposit_details'] = unserialize($contents[0]);
					$value['ammount'] = $newContents['deposit_details']['vd_amount'];



					$newContents['deposit_details']['status_label'] = $depositStatusList[$newContents['deposit_details']['vd_status']];
				}
			} else if((int)$value['payment_module'] == 3) {
				if(isset($contents[0])) {
					$newContents['user'] = unserialize($contents[0])->toArray();
				}
				if(isset($contents[1])) {
					$newContents['plan'] = unserialize($contents[1])->toArray();
					$value['ammount'] = ! empty($newContents['plan']['special']) ? $newContents['plan']['special'] : $newContents['plan']['price']; 
				}
				if(isset($contents[2])) {
					$newContents['payment'] = unserialize($contents[2]);
				}
			}

			$value['content'] = $newContents;
		}


		return $uncompleted_payments;
	}

	function getHoldTransactionsByUserId($dayfrom=10,$limit=100,$user_id)
	{
		$sql="SELECT  SUM(ifnull(wallet.amount,0)) as total  FROM wallet   
		INNER JOIN users ON users.id = wallet.user_id   
		WHERE users.id=$user_id and DATEDIFF(NOW(),wallet.created_at)>=".$dayfrom."  AND wallet.parent_id = 0 AND wallet.status = 1 AND wallet.amount >= 0    ORDER BY wallet.user_id,wallet.created_at ASC LIMIT ".$limit." OFFSET 0";

		$query=$this->db->query($sql);
		if($query->num_rows()>0)
 			$data = $query->row()->total;
 		else 
 			$data=0;
		return $data;

	}

	function getHoldTransactions($dayfrom=10,$limit=100)
	{
		$sql="SELECT 
		wallet.id,wallet.user_id,wallet.group_id,wallet.from_user_id,wallet.amount,wallet.type,wallet.comm_from,wallet.comment,wallet.created_at,DATEDIFF(NOW(),wallet.created_at) AS DaysFrom ,wallet.status,wallet.commission_status,wallet.reference_id,wallet.reference_id_2 ,users.username,users.firstname,users.lastname,users.type as usertype,users.primary_payment_method,payment_detail.payment_bank_name,payment_account_number,payment_account_name,payment_ifsc_code,payment_created_by, paypal_accounts.paypal_email,(SELECT payment_method FROM `order` WHERE wallet.reference_id = `order`.id AND wallet.type IN('sale_commission','vendor_sale_commission', 'admin_sale_commission') AND wallet.dis_type is NULL ) AND wallet.comm_from != 'ex' as payment_method ,(SELECT total FROM `integration_orders` WHERE comm_from='ex' AND wallet.reference_id_2 = `integration_orders`.id AND wallet.type IN ('sale_commission','admin_sale_commission')) as integration_orders_total ,(SELECT total FROM `order` WHERE comm_from != 'ex' AND id = wallet.reference_id AND wallet.type IN('sale_commission','vendor_sale_commission', 'admin_sale_commission') ) as local_orders_total  FROM wallet 
		LEFT JOIN users ON users.id = wallet.user_id 
		LEFT JOIN  wallet_recursion ON wallet_recursion.transaction_id = wallet.id 
		LEFT join  payment_detail on  payment_detail.payment_created_by=wallet.user_id 
      	LEFT join  paypal_accounts on  paypal_accounts.user_id=wallet.user_id  
		WHERE DATEDIFF(NOW(),wallet.created_at)>=$dayfrom  AND wallet.parent_id = 0 AND wallet.status = 1 AND wallet.amount >= 0   GROUP BY wallet.group_id  ORDER BY wallet.user_id,wallet.created_at ASC LIMIT ".$limit." OFFSET 0";

			 $data = $this->db->query($sql)->result_array();

			 return $data;

	}

	function getPrimaryPaymentmethodData($tran)
	{	
		
		if($tran['primary_payment_method']=='paypal')
		{
			$setting['paypal_email'] = $tran['paypal_email'];
		}
		else if($tran['primary_payment_method']=='bank_transfer')
		{
			$setting['account_name'] = $tran['payment_account_name'];
			$setting['account_number'] = $tran['payment_account_number'];
			$setting['bank_name'] = $tran['payment_bank_name'];
			$setting['ifsc_code'] = $tran['payment_ifsc_code'];
		}
		
 		return $setting;
	}
	public function getProductDetail($userid,$pID){
		
		$this->db->where('product_id',$pID);
		$this->db->where('product_created_by',$userid);
		return $this->db->get('product')->row_array();
	}
}