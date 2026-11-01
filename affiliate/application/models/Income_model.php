<?php	
class Income_model extends MY_Model{

	public function get_report_count($filter) {
		$where = '';
		if (isset($filter['user_id']) && $filter['user_id'] > 0) {
			$where .= " AND id= ". (int)$filter['user_id'];
		}
		return $this->db->query("SELECT COUNT(id) as total FROM users WHERE type = 'user' {$where}")->row_array()['total'];
	}

	public function get_report($filter){
		$where = '';

		if (isset($filter['user_id']) && $filter['user_id'] > 0) {
			$where .= " AND u.id= ". (int)$filter['user_id'];
		}

		$sql = " 
			SELECT u.id, CONCAT(firstname,' ',lastname) as name, c.sortname as country_code, u.username 
			FROM users u 
			LEFT JOIN countries c ON u.Country = c.id 
			WHERE u.type = 'user' {$where}
		";

		if(isset($filter['destination']) && $filter['destination'] == 'admin-user-stat') {
			$limit = (isset($filter['page_lenght'])) ? $filter['page_lenght'] : 100;
			$page = (isset($filter['page_no'])) ? ($filter['page_no']/$limit) + 1 : 1;
			$offset = ($page-1) * $limit;
			$sql .= " LIMIT ".$limit." OFFSET ".$offset;
		}

		$users = $this->db->query($sql)->result_array();
			
		$data['data'] = array();
		foreach ($users as $key => &$value) {
			$filter['user_id'] = $value['id'];

			$totals = $this->Wallet_model->get_totals_for_admin_users_stat($filter,true);
				
			$data['data'][] = array(
				'id'                 		=> $value['id'],
				'name'               		=> $value['name'],
				'country_code'		 		=> $value['country_code'],
				'username'           		=> $value['username'],
				'total_commission'   		=> c_format($totals['n_total_click_comission'] + $totals['n_total_sale_comission'] + $totals['n_total_action_comission']),
				'total_click'        		=> (int)$totals['n_total_click_count'],
				'total_click_amount' 		=> c_format($totals['n_total_click_comission']),
				'total_sale_count'   		=> (int)$totals['n_total_sale_count'],
				'total_sale_amount'  		=> c_format($totals['n_total_sale_amount']),
				'total_sale_comm'     		=> c_format($totals['n_total_sale_comission']),
				'wallet_accept_amount'  	=> c_format($totals['n_total_comission_paid']),
				'external_action_click' 	=> (int)($totals['n_total_action_count']),
				'action_click_commission'   => c_format($totals['n_total_action_comission']),
			);
		}

		return $data;
	}
}