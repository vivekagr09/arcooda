<?php
class Total_model extends MY_Model{
	
public function getGrowthPercentage($a, $b) {
    $a = (float)$a;
    $b = (float)$b;

    if ($a <= $b) {
        return 0;
    } else if ($b <= 0) {
        return 100;
    } else {
        return number_format((($a - $b) / $b) * 100, 2);
    }
}


	public function click_action_commission_growth($current) {
		$wallet_where .= ' YEARWEEK(wallet.`created_at`, 1) = YEARWEEK(CURDATE(), 1)-1 ';
		$last_weak = $this->db->query("SELECT SUM(amount) as total FROM wallet WHERE type IN ('external_click_commission','refer_click_commission','external_click_comm_admin') AND is_action = 1 AND {$wallet_where}")->row()->total;

		return $this->getGrowthPercentage((int)$current, (int)$last_weak);
	}

	public function all_clicks_comission_growth($current) {

		$wallet_where .= ' YEARWEEK(wallet.`created_at`, 1) = YEARWEEK(CURDATE(), 1)-1 ';

		$localstore_commission = $this->db->query("SELECT SUM(amount) as total FROM wallet WHERE type IN ('click_commission','refer_click_commission') AND comm_from = 'store' AND amount > 0 AND is_action = 0 AND {$wallet_where}")->row()->total;

		$integration_commission = $this->db->query("SELECT SUM(amount) as total FROM wallet WHERE type IN('external_click_commission','external_click_comm_admin','refer_click_commission') AND comm_from = 'ex' AND amount > 0 AND is_action = 0 AND {$wallet_where}")->row()->total;

		$form_commission = $this->db->query("SELECT SUM(amount) as total FROM wallet WHERE type='form_click_commission' AND {$wallet_where}")->row()->total;

		$all_clicks_comission = (int)$localstore_commission + (int)$integration_commission + (int) $form_commission;

		return $this->getGrowthPercentage((int)$current, (int)$all_clicks_comission);
	}

	public function sales_growth($current) {

		$order_external_where .= ' YEARWEEK(created_at, 1) = YEARWEEK(CURDATE(), 1)-1 ';
		
		$order_where .= ' YEARWEEK(o.`created_at`, 1) = YEARWEEK(CURDATE(), 1)-1 ';

		$order_external = $this->db->query("SELECT COUNT(id) as counts, SUM(total) as total,SUM(commission) as commission FROM integration_orders  WHERE 1 AND (vendor_id=0 or vendor_id=NULL) AND {$order_external_where}")->row();

		$sale_localstore = $this->db->query("SELECT 
			SUM(order_products.total) as total,
			SUM(order_products.admin_commission+order_products.commission) as total_commission,
			COUNT(order_products.id) as total_order 
		FROM order_products
		LEFT JOIN `order` o ON o.id = order_products.order_id
		 WHERE (order_products.vendor_id=0 OR order_products.vendor_id IS NULL) AND o.status > 0 AND {$order_where}")->row();

		$sale_total = (int)$order_external->total + (int)$sale_localstore->total;

		return $this->getGrowthPercentage((int)$current, (int)$sale_total);
	}

	public function vendor_sales_growth($current) {
		$order_where .= ' YEARWEEK(o.`created_at`, 1) = YEARWEEK(CURDATE(), 1)-1 ';

		$sale_localstore_vendor_total = $this->db->query("SELECT 
			SUM(order_products.total) as total,
			SUM(order_products.admin_commission+order_products.commission) as total_commission,
			COUNT(order_products.id) as total_order 
		FROM order_products 
		LEFT JOIN `order` o ON o.id = order_products.order_id
		WHERE vendor_id > 0 AND o.status > 0 AND {$order_where} GROUP BY order_id ")->result();
			
		$total = 0;
		foreach($sale_localstore_vendor_total as $key => $value) {
			$total += (int)$value->total;
		}

		return $this->getGrowthPercentage((int)$current, (int)$total);
	}
	
	public function chart($filter = []){
		$json = [];
		$orderBy = ' ORDER BY created_at DESC ';
		
		$current_year = " YEAR(created_at) = " . $filter['year'];
		   
		  $groupby = '';
		    switch ($filter['group']) {
		        case 'day':
		            $groupby = 'CONCAT(DAY(created_at),"-",MONTH(created_at),"-",YEAR(created_at))';
		            break;
		        case 'week':
		            $groupby = 'WEEK(created_at, 1)'; // Using mode 1 to start week from Monday
		            break;
		        case 'month':
		            $groupby = 'MONTH(created_at)';
		            break;
		        case 'year':
		            $groupby = 'YEAR(created_at)';
		            break;
		    }


        if($filter['group'] == 'day'){ $groupby = 'CONCAT(DAY(created_at),"-",MONTH(created_at),"-",YEAR(created_at))'; }
        else if($filter['group'] == 'week'){ $groupby = 'WEEK(created_at)';}
        else if($filter['group'] == 'month'){ $groupby = 'MONTH(created_at)';}
        else if($filter['group'] == 'year'){ $groupby = 'YEAR(created_at)';}

        $this->db->select(array(
            'sum(commission) as total_commission',
            'sum(total) as total_sale',
            'count(id) as total_order',
            "{$groupby} as groupby"
        ));
      
        $this->db->where($current_year);
        $this->db->order_by('created_at','DESC');
        $this->db->group_by($groupby);
        $data = $this->db->get('integration_orders')->result_array();
         
        $chart = array();
        foreach ($data as $key => $value) {
            $chart[] = array(
				'key'              => $value['groupby'],
				'order_total'      => c_format($value['total_sale'], false),
				'order_count'      => (int)$value['total_order'],
				'order_commission' => c_format($value['total_commission'], false),
            );
        }


        $this->db->select(array(
            'sum(op.total) as total_sale',
            'count(op.id) as total_order',
            "{$groupby} as groupby",
            'sum(op.commission + op.vendor_commission) as total_commission'
        ));
        $this->db->join("order_products op",'op.order_id = order.id','left');
        $this->db->where($current_year);   
        $this->db->where('order.status = 1');
        $this->db->group_by('op.order_id');
        $data = $this->db->get('order')->result_array();
        
        foreach ($data as $key => $value) {
            $chart[] = array(
                'key' => $value['groupby'],
                'order_total' => c_format($value['total_sale'], false),
                'order_count' => (int)$value['total_order'],
                'order_commission' => c_format($value['total_commission'], false),
            );
        }

       
        $integration_click_amount = $this->db->query('SELECT '. $groupby . ' as groupby,SUM(amount) as total,COUNT(amount) as total_count FROM `wallet` WHERE is_action=1 AND type="external_click_commission" AND '. $_where . $current_year .' AND comm_from = "ex"  AND status > 0 GROUP BY '. $groupby .'   '. $orderBy )->result_array();
        foreach ($integration_click_amount as $value) {
            $chart[] = array(
                'key' => $value['groupby'],
                'action_commission' => c_format($value['total'], false),
                'action_count' => $value['total_count'],
            );
        }

        $week = [];
        $day = [];
        $year = [];
        for($i=1;$i<=53;$i++){ $week[] = "Week {$i}"; }
        for($i=1;$i<=31;$i++){ $day[date($i."-n-Y")] = date($i."-n-Y"); }
        for($i=2016;$i<=date("Y");$i++){ $year[$i] = $i; }

        $defaultKey = [
        	'month' => ['','January','February','March','April','May','June','July','August','September','October','November','December'],
        	'week' => $week,
        	'day' => $day,
        	'year' => $year,
        ];

        $allData = [];
        foreach ($chart as $key => $value) {
        	$DK = $defaultKey[$filter['group']][$value['key']];
        	$allData[$DK]['order_total'] += isset($value['order_total']) ? $value['order_total'] : 0;
        	$allData[$DK]['order_count'] += isset($value['order_count']) ? $value['order_count'] : 0;
        	$allData[$DK]['order_commission'] += isset($value['order_commission']) ? $value['order_commission'] : 0;
        	$allData[$DK]['action_commission'] += isset($value['action_commission']) ? $value['action_commission'] : 0;
        	$allData[$DK]['action_count'] += isset($value['action_count']) ? $value['action_count'] : 0;
        }

        foreach ($defaultKey[$filter['group']] as $key => $value) {
        	if($value){
	        	$json['order_total'][$value] = isset($allData[$value]['order_total']) ? $allData[$value]['order_total'] : 0; 
	        	$json['order_count'][$value] = isset($allData[$value]['order_count']) ? $allData[$value]['order_count'] : 0; 
	        	$json['order_commission'][$value] = isset($allData[$value]['order_commission']) ? $allData[$value]['order_commission'] : 0; 
	        	$json['action_commission'][$value] = isset($allData[$value]['action_commission']) ? $allData[$value]['action_commission'] : 0; 
	        	$json['action_count'][$value] = isset($allData[$value]['action_count']) ? $allData[$value]['action_count'] : 0; 
        	}
        }

        $json['keys'] = $defaultKey[$filter['group']];
        if ($filter['group'] == 'month') {
        	$json['keys'] = array_filter($defaultKey[$filter['group']]);
        }

        return $json;
	}

	public function adminBalance($filter = []){
		$wallet_where = ' 1 ';
		$order_product_where = ' 1 ';
		$aw_where = ' 1 ';
		$vd_where = ' 1 ';
		$membership_where = ' 1 ';

		if (isset($filter['week'])) {
			$wallet_where .= ' AND YEARWEEK(wallet.`created_at`, 1) = YEARWEEK(CURDATE(), 1)';
			$order_product_where .= ' AND YEARWEEK(o.`created_at`, 1) = YEARWEEK(CURDATE(), 1)';
			$aw_where .= ' AND YEARWEEK(aw.`created_at`, 1) = YEARWEEK(CURDATE(), 1)';
			$vd_where .= ' AND MONTH(vd_created_on) = YEARWEEK(CURDATE(), 1)-1';
			$membership_where .= ' AND MONTH(created_at) = YEARWEEK(CURDATE(), 1)-1';
		}
		
		if (isset($filter['month'])) {
			$wallet_where .= ' AND MONTH(wallet.`created_at`) = MONTH(NOW())';
			$order_product_where .= ' AND MONTH(o.`created_at`) = MONTH(NOW())';
			$aw_where .= ' AND MONTH(aw.`created_at`) = MONTH(NOW())';
			$vd_where .= ' AND MONTH(vd_created_on) = YEARWEEK(CURDATE(), 1)-1';
			$membership_where .= ' AND MONTH(created_at) = YEARWEEK(CURDATE(), 1)-1';
		}
		
		if (isset($filter['year'])) {
			$year = date("Y");
			$wallet_where .= ' AND YEAR(wallet.`created_at`) ='. $year;
			$order_product_where .= ' AND YEAR(o.`created_at`) ='. $year;
			$aw_where .= ' AND YEAR(aw.`created_at`) ='. $year;
			$vd_where .= ' AND MONTH(vd_created_on) ='. $year;
			$membership_where .= ' AND MONTH(created_at) ='. $year;
		}

		$balance = 0;
		
		// amount for refer sale comission
		$balance += $this->db->query("SELECT SUM(amount) as total FROM wallet WHERE {$wallet_where} AND user_id > 1 AND amount > 0
		AND status != 0 AND type = 'refer_sale_commission' AND comm_from = 'ex' AND is_vendor = 1 ")->row()->total;
		
		// amount paid to affiliates or vendor
		$balance -= $this->db->query("SELECT SUM(amount) as total FROM wallet WHERE {$wallet_where} AND user_id > 1 AND status = 3 AND amount > 0")->row()->total;

		// amount for minus transaction
		$balance -= $this->db->query("SELECT SUM(amount) as total FROM wallet WHERE {$wallet_where} AND amount < 0 AND status > 0")->row()->total;

		// amount for external click comission
		$balance += $this->db->query("SELECT SUM(amount) as total FROM wallet WHERE {$wallet_where} AND type IN ('external_click_commission') AND user_id = 1 AND status > 0 AND is_action = 0 AND is_vendor = 1 ")->row()->total;

		// amount for refer click comission
		$balance += $this->db->query("SELECT SUM(amount) as total FROM wallet WHERE {$wallet_where} AND type IN ('refer_click_commission') AND status > 0 AND is_action = 0 AND is_vendor = 1 ")->row()->total;

		// amount for refer action comission
		$balance += $this->db->query("SELECT SUM(amount) as total FROM wallet WHERE {$wallet_where} AND type IN ('refer_click_commission') AND status > 0 AND is_action = 1 AND is_vendor = 1 ")->row()->total;

		// external order total
		$balance += $this->db->query("SELECT SUM(total) as total FROM ( SELECT total FROM integration_orders INNER JOIN wallet as aw ON aw.reference_id_2 = integration_orders.id WHERE {$aw_where} AND aw.status > 0 AND integration_orders.vendor_id = 0 GROUP BY integration_orders.id) as t")->row()->total;
		
		// localstore order toal
		$balance += $this->db->query("
			SELECT SUM(op.total) as total FROM order_products op
			LEFT JOIN `order` o ON o.id = op.order_id 
			WHERE {$order_product_where} AND o.status!=0
		")->row()->total;

		//  Deposit
		$balance += $this->db->query("SELECT SUM(vd_amount) as total FROM vendor_deposit WHERE {$vd_where} AND vd_status=1")->row()->total;
	
		//  Membership
		$balance += $this->db->query("SELECT SUM(total) as total FROM membership_user WHERE {$membership_where} AND status_id=1")->row()->total;

		// Admin shipping
		$balance += $this->db->query("SELECT SUM(shipping_cost) as total FROM `order`")->row()->total;

		// Admin taxs
		$balance += $this->db->query("SELECT SUM(tax_cost) as total FROM `order`")->row()->total;

		return $balance;
	}

	public function adminBalanceForGrowthCalculation($filter = []){
		$wallet_where = ' 1 ';
		$order_product_where = ' 1 ';
		$aw_where = ' 1 ';
		$vd_where = ' 1 ';
		$membership_where = ' 1 ';


		if (isset($filter['week'])) {
			$wallet_where .= ' AND YEARWEEK(wallet.`created_at`, 1) = YEARWEEK(CURDATE(), 1)-1';
			$order_product_where .= ' AND YEARWEEK(o.`created_at`, 1) = YEARWEEK(CURDATE(), 1)-1';
			$aw_where .= ' AND YEARWEEK(aw.`created_at`, 1) = YEARWEEK(CURDATE(), 1)-1';
			$vd_where .= ' AND YEARWEEK(vd_created_on) = YEARWEEK(CURDATE(), 1)-1';
			$membership_where .= ' AND YEARWEEK(created_at) = YEARWEEK(CURDATE(), 1)-1';
		}
		
		if (isset($filter['month'])) {
			$wallet_where .= ' AND MONTH(wallet.`created_at`) = MONTH(NOW())-1';
			$order_product_where .= ' AND MONTH(o.`created_at`) = MONTH(NOW())-1';
			$aw_where .= ' AND MONTH(aw.`created_at`) = MONTH(NOW())-1';
			$vd_where .= ' AND MONTH(vd_created_on) = MONTH(NOW())-1';
			$membership_where .= ' AND MONTH(created_at) = MONTH(NOW())-1';
		}
		
		if (isset($filter['year'])) {
			$year = date("Y")-1;
			$wallet_where .= ' AND YEAR(wallet.`created_at`) ='. $year;
			$order_product_where .= ' AND YEAR(o.`created_at`) ='. $year;
			$aw_where .= ' AND YEAR(aw.`created_at`) ='. $year;
			$vd_where .= ' AND MONTH(vd_created_on) ='. $year;
			$membership_where .= ' AND MONTH(created_at) ='. $year;
		}

		$balance = 0;
		
		// amount for refer sale comission
		$balance += $this->db->query("SELECT SUM(amount) as total FROM wallet WHERE {$wallet_where} AND user_id > 1 AND amount > 0
		AND status != 0 AND type = 'refer_sale_commission' AND comm_from = 'ex' AND is_vendor = 1 ")->row()->total;
		
		// amount paid to affiliates or vendor
		$balance -= $this->db->query("SELECT SUM(amount) as total FROM wallet WHERE {$wallet_where} AND user_id > 1 AND status = 3 AND amount > 0")->row()->total;

		// amount for minus transaction
		$balance -= $this->db->query("SELECT SUM(amount) as total FROM wallet WHERE {$wallet_where} AND amount < 0 AND status > 0")->row()->total;

		// amount for external click comission
		$balance += $this->db->query("SELECT SUM(amount) as total FROM wallet WHERE {$wallet_where} AND type IN ('external_click_commission') AND user_id = 1 AND status > 0 AND is_action = 0 AND is_vendor = 1 ")->row()->total;

		// amount for refer click comission
		$balance += $this->db->query("SELECT SUM(amount) as total FROM wallet WHERE {$wallet_where} AND type IN ('refer_click_commission') AND status > 0 AND is_action = 0 AND is_vendor = 1 ")->row()->total;

		// amount for refer action comission
		$balance += $this->db->query("SELECT SUM(amount) as total FROM wallet WHERE {$wallet_where} AND type IN ('refer_click_commission') AND status > 0 AND is_action = 1 AND is_vendor = 1 ")->row()->total;

		// external order total
		$balance += $this->db->query("SELECT SUM(total) as total FROM ( SELECT total FROM integration_orders INNER JOIN wallet as aw ON aw.reference_id_2 = integration_orders.id WHERE {$aw_where} AND aw.status > 0 AND integration_orders.vendor_id = 0 GROUP BY integration_orders.id) as t")->row()->total;
		
		// localstore order toatl
		$balance += $this->db->query("
			SELECT SUM(op.total) as total FROM order_products op
			LEFT JOIN `order` o ON o.id = op.order_id 
			WHERE {$order_product_where} AND o.status!=0
		")->row()->total;

		//  Deposit
		$balance += $this->db->query("SELECT SUM(vd_amount) as total FROM vendor_deposit WHERE {$vd_where} AND vd_status=1")->row()->total;
	
		//  Membership
		$balance += $this->db->query("SELECT SUM(total) as total FROM membership_user WHERE {$membership_where} AND status_id=1")->row()->total;


		// Admin shipping
		$balance += $this->db->query("SELECT SUM(shipping_cost) as total FROM `order`")->row()->total;

		// Admin taxs
		$balance += $this->db->query("SELECT SUM(tax_cost) as total FROM `order`")->row()->total;

		return $balance;
	}

	public function getIntegrationsTotals($integration_filters = false){
		$where1 = " YEAR(`integration_orders`.`created_at`) =  '".$integration_filters['integration_data_year']."' ";

		$where2 = " YEAR(`integration_clicks_action`.`created_at`) =  '".$integration_filters['integration_data_year']."' ";

		$where3 = " YEAR(`wallet`.`created_at`) =  '".$integration_filters['integration_data_year']."' ";

		if($integration_filters['integration_data_month']){
			$where1 .= "AND MONTH(`integration_orders`.`created_at`) =  '".$integration_filters['integration_data_month']."' ";

			$where2 .= "AND MONTH(`integration_clicks_action`.`created_at`) =  '".$integration_filters['integration_data_month']."' ";

			$where3 .= "AND MONTH(`wallet`.`created_at`) =  '".$integration_filters['integration_data_month']."' ";
		}

		$data['integration']['hold_action_count'] = 0;
		$data['integration']['hold_orders'] = 0;
		
		$integration_balance = $this->db->query('SELECT 
			integration_orders.vendor_id,
			integration_orders.base_url,
			integration_orders.total,
			wallet.status as wallet_status,
			integration_orders.commission as admin_comm 
		FROM integration_orders
			LEFT JOIN wallet ON wallet.id = integration_orders.affiliate_tran
		WHERE '.$where1)->result();


		foreach ($integration_balance as $vv) {
			if($vv->wallet_status > 0){
				if((int)$vv->vendor_id == 0){
					$data['integration']['balance'] += ($vv->total - $vv->admin_comm);
					$data['integration']['all'][$vv->base_url]['balance'] += ($vv->total- $vv->admin_comm);
				} else{
					$data['integration']['balance'] += $vv->admin_comm;
					$data['integration']['all'][$vv->base_url]['balance'] += $vv->admin_comm;
				}
			}

			$data['integration']['total_count'] += 1;
			$data['integration']['all'][$vv->base_url]['total_count'] += 1;

			if((int)$vv->vendor_id == 0){
				if($vv->wallet_status > 0){
					$data['integration']['sale'] += ($vv->total - $vv->admin_comm);
					$data['integration']['all'][$vv->base_url]['sale'] +=($vv->total - $vv->admin_comm);
				}
			} else{
				$data['integration']['sale'] += $vv->admin_comm;
				$data['integration']['all'][$vv->base_url]['sale'] += $vv->admin_comm;
			}
		}

		$integration_click_count  = $this->db->query('SELECT base_url,count(*) as total FROM `integration_clicks_action` WHERE '. $where2 .'  AND is_action=0 GROUP BY base_url' )->result();
		foreach ($integration_click_count as $vv) {
			$data['integration']['click_count'] += $vv->total;
			$data['integration']['all'][$vv->base_url]['click_count'] += $vv->total;
		}

		$integration_click_amount = $this->db->query('
			SELECT domain_name,SUM(amount) as total 
			FROM `wallet` 
			WHERE 
				type = "external_click_commission" AND 
				is_vendor=0 AND 
				is_action=0 AND 
				comm_from = "ex" AND '. $where3 .' 
				GROUP BY domain_name
		')->result();

		foreach ($integration_click_amount as $vv) {
			$data['integration']['click_amount'] -= $vv->total;
			$data['integration']['all'][$vv->domain_name]['click_amount'] -= $vv->total;

			$data['integration']['balance'] -= $vv->total;
			$data['integration']['all'][$vv->domain_name]['balance'] -= $vv->total;
		}

		$vendor_integration_click_amount = $this->db->query('
			SELECT domain_name,SUM(amount) as total 
			FROM `wallet` 
			WHERE 
				type = "external_click_comm_admin" AND is_vendor=1 AND 
				is_action=0 AND 
				comm_from = "ex" AND '. $where3 .' 
				GROUP BY domain_name
		')->result();


		foreach ($vendor_integration_click_amount as $vv) {
			$data['integration']['click_amount'] += $vv->total;
			$data['integration']['all'][$vv->domain_name]['click_amount'] += $vv->total;
			
			$data['integration']['balance'] += $vv->total;
			$data['integration']['all'][$vv->domain_name]['balance'] += $vv->total;
		}

		
		$integration_click_amount = $this->db->query('SELECT domain_name,count(*) as total FROM `wallet` WHERE is_action=1 AND type="external_click_commission" AND status = 0 AND comm_from = "ex" AND '. $where3 .' GROUP BY domain_name')->result();
		foreach ($integration_click_amount as $vv) {
			$data['integration']['hold_action_count'] += $vv->total;
			$data['integration']['all'][$vv->domain_name]['hold_action_count'] += $vv->total;
		}


		
		$integration_action_amount = $this->db->query('SELECT 
			status,
			domain_name,
			amount as total
			FROM `wallet` WHERE is_action=1 AND type="external_click_commission" AND comm_from = "ex" AND is_vendor=0 AND '. $where3 .' ')->result();

		foreach ($integration_action_amount as $vv) {
			if($vv->status > 0){
				$data['integration']['action_amount'] -= $vv->total;
				$data['integration']['all'][$vv->domain_name]['action_amount'] -= $vv->total;
			}

			$data['integration']['action_count'] += 1;
			$data['integration']['all'][$vv->domain_name]['action_count'] += 1;
		}

		$integration_action_amount = $this->db->query('SELECT domain_name,SUM(amount) as total FROM `wallet` WHERE is_action=1 AND type="external_click_commission" AND status > 0 AND comm_from = "ex" AND is_vendor=0 AND '. $where3 .' GROUP BY domain_name')->result();

		foreach ($integration_action_amount as $vv) {
			$data['integration']['balance'] -= $vv->total;
			$data['integration']['all'][$vv->domain_name]['balance'] -= $vv->total;
		}




		$integration_action_amount_vendor = $this->db->query('SELECT 
			status,
			domain_name,
			amount as total
		FROM `wallet` 
		WHERE 
			is_action=1 AND 
			type="external_click_comm_admin" AND 
			comm_from = "ex" AND 
			is_vendor=1 AND '. $where3 .' ')->result();

		foreach ($integration_action_amount_vendor as $vv) {
			if($vv->status > 0){
				$data['integration']['action_amount'] += $vv->total;
				$data['integration']['all'][$vv->domain_name]['action_amount'] += $vv->total;
			}

			$data['integration']['action_count'] += 1;
			$data['integration']['all'][$vv->domain_name]['action_count'] += 1;
		}


		$integration_action_amount_vendor = $this->db->query('SELECT domain_name,SUM(amount) as total FROM `wallet` WHERE 
			is_action=1 AND 
			type="external_click_comm_admin" AND 
			status > 0 AND 
			comm_from = "ex" AND 
			is_vendor=1 AND '. $where3 .' GROUP BY domain_name')->result();

		foreach ($integration_action_amount_vendor as $vv) {
			$data['integration']['balance'] += $vv->total;
			$data['integration']['all'][$vv->domain_name]['balance'] += $vv->total;
		}
		 

		$integration_total_orders = $this->db->query('SELECT base_url,count(*) as total,SUM(commission) as commission,SUM(total) as total_amount FROM `integration_orders` WHERE  '. $where1 .' GROUP BY base_url' )->result();
		foreach ($integration_total_orders as $vv) {
			$data['integration']['total_orders'] += $vv->total;
			$data['integration']['total_orders_amount'] += $vv->total_amount;
			$data['integration']['total_orders_commission'] += $vv->commission;
			$data['integration']['all'][$vv->base_url]['total_orders'] += $vv->total;
		}

		$data['integration']['total_commission'] = ($data['integration']['click_amount'] + $data['integration']['sale'] + $data['integration']['action_amount']);
		
		return $data;
	}

	public function get_integartion_data($return  = false, $fun_c_format = 'c_format'){
		$post = $this->input->post();
		$json = array();

		if($post['integration_data_year'] && $post['integration_data_month']){
			$integration_filters = array(
				'integration_data_year' => $post['integration_data_year'],
				'integration_data_month' => $post['integration_data_month'],
			);

			if($post['integration_data_year'] == 'All')
				$integration_filters['integration_data_year'] = '';
			
			if($post['integration_data_month'] == 'All')
				$integration_filters['integration_data_month'] = '';
		} else {
			$integration_filters = array(
				'integration_data_year' => date('Y'),
				'integration_data_month' => '',
			);
		}

		$totals = $this->getIntegrationsTotals($integration_filters);
		
		if($totals){
		    $html = '<tr>
	            <td>ALL WEBSITE</td>
	            <td class="no-wrap">'. $fun_c_format($totals['integration']['balance']) .'</td>
	            <td class="no-wrap">'. (int)$totals['integration']['total_count'] .' / '. $fun_c_format($totals['integration']['sale']) .'</td>
	            <td class="no-wrap">'. (int)$totals['integration']['click_count'] .' / '. $fun_c_format($totals['integration']['click_amount']) .'</td>
	            <td class="no-wrap">'. (int)$totals['integration']['action_count'] .' / '. $fun_c_format($totals['integration']['action_amount']) .'</td>
	            <td class="no-wrap">'. $fun_c_format($totals['integration']['total_commission']) .' </td>
	            <td class="no-wrap">'. (int)$totals['integration']['total_orders'] .' </td>
	        </tr>';

	        $json['array'] = [];
	        $array = array(
	        	'website' => 'ALL WEBSITE',
	        	'balance' => $fun_c_format($totals['integration']['balance']),
	        	'total_count_sale' => (int)$totals['integration']['total_count'] .' / '. $fun_c_format($totals['integration']['sale']),
	        	'click_count_amount' => (int)$totals['integration']['click_count'] .' / '. $fun_c_format($totals['integration']['click_amount']),
	        	'action_count_amount' => (int)$totals['integration']['action_count'] .' / '. $fun_c_format($totals['integration']['action_amount']),
	        	'total_commission' => $fun_c_format($totals['integration']['total_commission']),
	        	'total_orders' => (int)$totals['integration']['total_orders']
	        );
	        array_push($json['array'],$array);

		    foreach ($totals['integration']['all'] as $website => $value){
		        $html .= '<tr>
                	<td class="no-wrap">'. $website .'</td>
	                <td class="no-wrap">'. $fun_c_format($value['balance']) .'</td>
	                <td class="no-wrap">'. (int)$value['total_count'] .' / '. $fun_c_format($value['sale']) .'        </td>
	                <td class="no-wrap">'. (int)$value['click_count'] .' / '. $fun_c_format($value['click_amount']) .'</td>
	                <td class="no-wrap">'. (int)$value['action_count'] .' / '. $fun_c_format($value['action_amount']) .'</td>
	                <td class="no-wrap">'. $fun_c_format($value['click_amount'] + $value['sale'] + $value['action_amount']) .' </td>
	                <td class="no-wrap">'. (int)$value['total_orders'] .' </td>
	            </tr>';

	            $array = array(
		        	'website' => $website,
		        	'balance' => $fun_c_format($value['balance']),
		        	'total_count_sale' => (int)$value['total_count'] .' / '. $fun_c_format($value['sale']),
		        	'click_count_amount' => (int)$value['click_count'] .' / '. $fun_c_format($value['click_amount']),
		        	'action_count_amount' => (int)$value['action_count'] .' / '. $fun_c_format($value['action_amount']),
		        	'total_commission' => $fun_c_format($value['click_amount'] + $value['sale'] + $value['action_amount']),
		        	'total_orders' => (int)$value['total_orders']
		        );
	            array_push($json['array'],$array);
		    }

			$integration_data_selected = 'all';
			if(isset($post['integration_data_selected']) && $post['integration_data_selected'] != '') $integration_data_selected = $post['integration_data_selected'];

            $json['html'] = $html;
		}else{
			$json['html'] = false;
		}

		if($return) return $json;
		echo json_encode($json);die;
	}

	public function adminTotals(){
		$totals = [];

		$totals['admin_balance'] = $this->adminBalance();
		
		$admin_balance_growth_from_last_weak = $this->adminBalanceForGrowthCalculation(['week' => 1]);
		
		$totals['admin_balance_growth'] = $this->getGrowthPercentage((int)$totals['admin_balance'], (int)$admin_balance_growth_from_last_weak);

		$sale_localstore_total = $this->db->query("SELECT 
			SUM(order_products.total) as total,
			SUM(order_products.admin_commission+order_products.commission) as total_commission,
			COUNT(order_products.id) as total_order 
		FROM order_products
		LEFT JOIN `order` o ON o.id = order_products.order_id
		 WHERE (order_products.vendor_id=0 OR order_products.vendor_id IS NULL) AND o.status > 0 ")->row();

		$totals['sale_localstore_total'] = $sale_localstore_total->total;
		$totals['sale_localstore_commission'] = $sale_localstore_total->total_commission;
		$totals['sale_localstore_count'] = $sale_localstore_total->total_order;


		$sale_localstore_vendor_total = $this->db->query("SELECT 
			SUM(order_products.total) as total,
			SUM(order_products.admin_commission+order_products.commission) as total_commission,
			COUNT(order_products.id) as total_order 
		FROM order_products 
		LEFT JOIN `order` o ON o.id = order_products.order_id
		WHERE vendor_id > 0 AND o.status > 0 GROUP BY order_id ")->result();
			
		foreach($sale_localstore_vendor_total as $key => $value) {
			$totals['sale_localstore_vendor_total'] += $value->total;
			$totals['sale_localstore_vendor_commission'] += $value->total_commission;
			$totals['sale_localstore_vendor_count'] += $value->total_order;
		}


		$totals['vendor_all_sales_growth'] = $this->vendor_sales_growth((int) $totals['sale_localstore_vendor_total']);

		// Admin localstore clicks count
		$totals['click_localstore_total'] += (int)$this->db->query('SELECT COUNT(pa.action_id) as total FROM product_action pa LEFT JOIN product_affiliate paff ON (paff.product_id = pa.product_id) WHERE 1')->row()->total;

		// Admin localstore clicks + refer commission
		$totals['click_localstore_commission'] = $this->db->query("SELECT SUM(amount) as total FROM wallet WHERE type IN ('click_commission','refer_click_commission') AND comm_from = 'store' AND amount > 0 AND is_action = 0 ")->row()->total;

		// Admin wallet total clicks count
		$totals['click_integration_total'] += (int)$this->db->query('SELECT COUNT(id) as total FROM integration_clicks_action WHERE is_action=0')->row()->total;

		// Admin wallet total clicks + refer commission
		$totals['click_integration_commission'] = $this->db->query("SELECT SUM(amount) as total FROM wallet WHERE type IN('external_click_commission','external_click_comm_admin','refer_click_commission') AND comm_from = 'ex' AND amount > 0 AND is_action = 0 ")->row()->total;

		$totals['click_form_total'] = (int)$this->db->query('SELECT COUNT(action_id) as total FROM form_action WHERE 1')->row()->total;
		$totals['click_form_commission'] = $this->db->query("SELECT SUM(amount) as total FROM wallet WHERE type='form_click_commission'")->row()->total;


		$all_clicks_comission = $totals['click_localstore_commission'] + $totals['click_integration_commission'] + $totals['click_form_commission'];

		$totals['all_clicks_comission_growth'] = $this->all_clicks_comission_growth((int)$all_clicks_comission);

		$totals['click_action_total'] = (int)$this->db->query('SELECT COUNT(id) as total FROM integration_clicks_action WHERE is_action = 1')->row()->total;

		$totals['click_action_commission'] = $this->db->query("SELECT SUM(amount) as total FROM wallet WHERE type IN ('external_click_commission','refer_click_commission','external_click_comm_admin') AND is_action = 1 ")->row()->total;

		$totals['click_action_commission_growth'] = $this->click_action_commission_growth($totals['click_action_commission']);

	    // Admin shipping
		$totals['local_store_shipping_cost'] = $this->db->query("SELECT SUM(shipping_cost) as total FROM `order`")->row()->total;

		// Admin taxs
		$totals['local_store_tax_cost'] = $this->db->query("SELECT SUM(tax_cost) as total FROM `order`")->row()->total;


		$query = $this->db->query('SELECT sum(amount) as amount,count(`status`) as counts,`status` FROM `wallet` WHERE  amount > 0 GROUP BY `status`')->result_array();
		foreach ($query as $key => $value) {
			switch ($value['status']) {
				case '0':
					$totals['wallet_on_hold_amount'] = (float)$value['amount'];
					$totals['wallet_unpaid_amounton_hold_count'] = (float)$value['counts'];
					break;
				case '1':
					$totals['wallet_unpaid_amount'] = (float)$value['amount'];
					$totals['wallet_unpaid_count'] = (float)$value['counts'];
					break;
				case '2':
					$totals['wallet_request_sent_amount'] = (float)$value['amount'];
					$totals['wallet_request_sent_count'] = (float)$value['counts'];
					break;
				case '3':
					$totals['wallet_accept_amount'] = (float)$value['amount'];
					$totals['wallet_accept_count'] = (float)$value['counts'];
					break;
				default: break;
			}
		}

		$query = $this->db->query('SELECT sum(amount) as amount,count(`commission_status`) as counts,`commission_status` FROM `wallet` WHERE  amount > 0 GROUP BY `commission_status`')->result_array();
		foreach ($query as $key => $value) {
			switch ($value['commission_status']) {
				case '1':
					$totals['wallet_cancel_amount'] = (float)$value['amount'];
					$totals['wallet_cancel_count'] = (float)$value['counts'];
					break;
				case '2':
					$totals['wallet_trash_amount'] = (float)$value['amount'];
					$totals['wallet_trash_count'] = (float)$value['counts'];
					break;
				default: break;
			}
		}
		
		$query = $this->db->query('SELECT sum(amount) as amount,count(`status`) as counts,`status` FROM `wallet` WHERE type IN ("vendor_sale_commission") AND amount > 0 GROUP BY `status`')->result_array();
		foreach ($query as $key => $value) {
			switch ($value['status']) {
				case '0':
					$totals['vendor_wallet_on_hold_amount'] = (float)$value['amount'];
					$totals['vendor_wallet_on_hold_count'] = (float)$value['counts'];
					break;
				case '1':
					$totals['vendor_wallet_unpaid_amount'] = (float)$value['amount'];
					$totals['vendor_wallet_unpaid_count'] = (float)$value['counts'];
					break;
				case '2':
					$totals['vendor_wallet_request_sent_amount'] = (float)$value['amount'];
					$totals['vendor_wallet_request_sent_count'] = (float)$value['counts'];
					break;
				case '3':
					$totals['vendor_wallet_accept_amount'] = (float)$value['amount'];
					$totals['vendor_wallet_accept_count'] = (float)$value['counts'];
					break;
				default: break;
			}
		}

		$query = $this->db->query('SELECT sum(amount) as amount,count(`commission_status`) as counts,`commission_status` FROM `wallet` WHERE type IN ("vendor_sale_commission") AND amount > 0 GROUP BY `commission_status`')->result_array();
		foreach ($query as $key => $value) {
			switch ($value['commission_status']) {
				case '1':
					$totals['vendor_wallet_cancel_amount'] = (float)$value['amount'];
					$totals['vendor_wallet_cancel_count'] = (float)$value['counts'];
					break;
				case '2':
					$totals['vendor_wallet_trash_amount'] = (float)$value['amount'];
					$totals['vendor_wallet_trash_count'] = (float)$value['counts'];
					break;
				default: break;
			}
		}

		$totals['order_vendor_total'] += (float)$this->db->query('SELECT COUNT(op.id) as total FROM `order` o LEFT JOIN order_products op ON op.order_id = o.id WHERE 1 AND op.vendor_id > 0 AND o.status > 0')->row()->total;
		

		$order_external_count = $this->db->query('SELECT COUNT(id) as counts, SUM(total) as total,SUM(commission) as commission FROM integration_orders  WHERE 1 AND (vendor_id=0 or vendor_id=NULL)')->row();
		$totals['order_external_total'] = $order_external_count->total;
		
		$order_vendor_commission = $this->db->query('
			SELECT 
				COUNT(o.id) as total,
				SUM(aw.amount) as commission
			FROM integration_orders o
			INNER JOIN wallet as aw ON aw.reference_id_2 = o.id 
			WHERE o.status >= 0
			AND aw.comm_from = "ex"
			AND aw.type = "sale_commission"
			GROUP BY o.id
		')->row();
		$totals['order_external_count'] = $order_vendor_commission->total;
		$totals['order_external_commission'] = $order_vendor_commission->commission;
		
		$all_sales = (int)$totals['sale_localstore_total'] + (int)$totals['order_external_total'];

		$totals['admin_all_sales_growth'] = $this->sales_growth((int)$all_sales);

		return $totals;
	}

	public function getUserTotals($user_id){

		$totals = [];

		$totals['click_localstore_total'] += (int)$this->db->query("SELECT COUNT(pa.action_id) as total FROM product_action pa LEFT JOIN product_affiliate paff ON (paff.product_id = pa.product_id) WHERE (pa.user_id={$user_id})")->row()->total;		
		$totals['click_localstore_commission'] = $this->db->query("SELECT SUM(amount) as total FROM wallet WHERE type='click_commission' AND amount > 0 AND user_id={$user_id}  ")->row()->total;

		$totals['sale_localstore_total'] = 0;
		$totals['sale_localstore_commission'] = 0;
		$totals['sale_localstore_count'] = 0;
		
		$sale_localstore_total_query = $this->db->query("SELECT 
			SUM(total) as total,
			SUM(commission) as total_commission,
			COUNT(*) as total_order 
		FROM order_products WHERE refer_id={$user_id} GROUP BY order_id ")->result();

		foreach ($sale_localstore_total_query as $key => $value) {
			$totals['sale_localstore_total'] += $value->total;
			$totals['sale_localstore_commission'] += $value->total_commission;
			$totals['sale_localstore_count'] += $value->total_order;
		}


		$totals['click_external_total'] += (int)$this->db->query("SELECT COUNT(id) as total FROM integration_clicks_action WHERE user_id={$user_id} AND is_action=0")->row()->total;
		$totals['click_external_commission'] = $this->db->query("SELECT SUM(amount) as total FROM wallet WHERE type IN('external_click_commission') AND status > 0 AND is_action=0 AND user_id={$user_id} ")->row()->total;

		$order_external_count = $this->db->query("
			SELECT 
				COUNT(id) as counts, 
				SUM(total) as total,
				SUM(commission) as commission 
				FROM integration_orders  WHERE user_id={$user_id} ")->row();

		$totals['order_external_total'] = $order_external_count->total;
		$totals['order_external_count'] = $order_external_count->counts;
		$totals['order_external_commission'] = $order_external_count->commission;

		$totals['click_action_total'] = (int) $this->db->query("SELECT COUNT(id) as total FROM integration_clicks_action WHERE is_action=1 AND user_id={$user_id}")->row()->total;
		
		$totals['click_action_commission'] = $this->db->query("SELECT SUM(amount) as total FROM wallet WHERE type IN('external_click_commission','external_click_comm_admin') AND is_action = 1 AND user_id={$user_id}")->row()->total;

		/* VENDOR COUNTS */
		$totals['vendor_click_localstore_total'] += (int)$this->db->query("SELECT COUNT(pa.action_id) as total FROM product_action pa LEFT JOIN product_affiliate paff ON (paff.product_id = pa.product_id) WHERE (paff.user_id={$user_id})")->row()->total;
		//count the clicks and refer clicks of vendor local store products
		$totals['vendor_click_localstore_commission'] = $this->db->query("SELECT SUM(amount) as total FROM wallet WHERE type IN('click_commission','refer_click_commission') AND amount > 0  AND group_id IN (SELECT group_id FROM wallet WHERE type='click_commission' AND amount < 0 AND user_id={$user_id})")->row()->total;

		//count the clicks and refer clicks of vendor_pay local store products
		$totals['vendor_click_localstore_commission_pay'] = $this->db->query("SELECT SUM(amount) as total FROM wallet WHERE type IN('click_commission','refer_click_commission') AND amount < 0 AND user_id={$user_id}  ")->row()->total;
		
		$sale_localstore_total = $this->db->query("SELECT 
			SUM(total) as total,
			SUM(admin_commission+commission) as total_commission,
			COUNT(*) as total_order 
		FROM order_products WHERE vendor_id={$user_id} GROUP BY order_id ")->result();

		foreach($sale_localstore_total as $key => $value) {
			$totals['vendor_sale_localstore_total'] += $value->total;
			$totals['vendor_sale_localstore_commission_pay'] += $value->total_commission;
			$totals['vendor_sale_localstore_count'] += $value->total_order;
		}

		$totals['vendor_click_external_total'] += (int)$this->db->query("
			SELECT COUNT(ica.id) as total 
			FROM integration_clicks_action ica 
				LEFT JOIN integration_tools it ON it.id = ica.tools_id 
			WHERE it.vendor_id={$user_id} AND is_action=0")->row()->total;

		// Vendor click external commission
		$totals['vendor_click_external_commission'] += $this->db->query("SELECT SUM(amount) as total FROM wallet WHERE type IN ('external_click_commission','refer_click_commission') AND is_action = 0 AND group_id IN (SELECT group_id FROM wallet WHERE type = 'external_click_comm_pay' AND is_action = 0 AND user_id={$user_id})")->row()->total;

		// Vendor pay click external commission
		$totals['vendor_click_external_commission_pay'] = $this->db->query("SELECT SUM(amount) as total FROM wallet WHERE type IN ('external_click_comm_pay') AND status > 0 AND is_action=0 AND user_id={$user_id} ")->row()->total;

		// Vendor pay refer click external commission
		$totals['vendor_click_external_commission_pay'] -= $this->db->query("SELECT SUM(amount) as total FROM wallet WHERE type = 'refer_click_commission' AND is_action = 0 AND group_id IN (SELECT group_id FROM wallet WHERE type = 'external_click_comm_pay' AND status > 0 AND is_action = 0 AND user_id={$user_id}) ")->row()->total;

		// Vendor action external total
		$totals['vendor_action_external_total'] = (int)$this->db->query("
			SELECT COUNT(ica.id) as total 
			FROM integration_clicks_action ica 
				LEFT JOIN integration_tools it ON it.id = ica.tools_id 
			WHERE it.vendor_id={$user_id} AND is_action=1")->row()->total;

		
		// Vendor action external commission
		$totals['vendor_action_external_commission'] -= $this->db->query("SELECT SUM(amount) as total FROM wallet WHERE type = 'external_click_comm_pay' AND is_action = 1 AND user_id={$user_id}")->row()->total;

		// Vendor refer action external comimssion
		$totals['vendor_action_external_commission'] += $this->db->query("SELECT SUM(amount) as total FROM wallet WHERE type = 'refer_click_commission' AND is_action = 1 AND group_id IN (SELECT group_id FROM wallet WHERE type = 'external_click_comm_pay' AND is_action = 1 AND user_id={$user_id})")->row()->total;

		// Vendor pay action external commission
		$totals['vendor_action_external_commission_pay'] = $this->db->query("SELECT SUM(amount) as total FROM wallet WHERE type IN('external_click_comm_pay') AND status > 0 AND is_action=1 AND user_id={$user_id} ")->row()->total;

		// Vendor pay refer action external commission
		$totals['vendor_action_external_commission_pay'] -= $this->db->query("SELECT SUM(amount) as total FROM wallet WHERE type = 'refer_click_commission' AND is_action = 1 AND group_id IN (SELECT group_id FROM wallet WHERE type = 'external_click_comm_pay' AND status > 0 AND is_action = 1 AND user_id={$user_id}) ")->row()->total;


		$totals['vendor_order_external_commission_pay'] = $this->db->query("SELECT SUM(amount) as total FROM wallet WHERE type IN ('sale_commission_vendor_pay','admin_sale_commission_v_pay') AND user_id={$user_id} ")->row()->total;

		$vendor_order_external = $this->db->query("SELECT COUNT(id) as counts, SUM(total) as total FROM integration_orders  WHERE vendor_id={$user_id} ")->row();

		$totals['vendor_order_external_count'] = $vendor_order_external->counts;
		$totals['vendor_order_external_total'] = $vendor_order_external->total;

		$totals['click_form_total'] = (int)$this->db->query("SELECT COUNT(action_id) as total FROM form_action WHERE user_id={$user_id}")->row()->total;
		$totals['click_form_commission'] = $this->db->query("SELECT SUM(amount) as total FROM wallet WHERE type='form_click_commission' AND user_id={$user_id}")->row()->total;

		$query = $this->db->query("SELECT sum(amount) as amount, count(`status`) as counts,`status`,`commission_status` FROM `wallet` WHERE user_id={$user_id} AND commission_status=0 AND amount > 0 GROUP BY `status`")->result_array();


		foreach ($query as $key => $value) {
			switch ($value['status']) {
				case '0':
					$totals['wallet_on_hold_amount'] = (float)$value['amount'];
					$totals['wallet_unpaid_amounton_hold_count'] = (float)$value['counts'];
					break;
				case '1':
					$totals['wallet_unpaid_amount'] = (float)$value['amount'];
					$totals['wallet_unpaid_count'] = (float)$value['counts'];
					break;
				case '2':
					$totals['wallet_request_sent_amount'] = (float)$value['amount'];
					$totals['wallet_request_sent_count'] = (float)$value['counts'];
					break;
				case '3':
					$totals['wallet_accept_amount'] = (float)$value['amount'];
					$totals['wallet_accept_count'] = (float)$value['counts'];
					break;
				default: break;
			}
		}

		$query = $this->db->query("SELECT sum(amount) as amount, count(`commission_status`) as counts,`commission_status` FROM `wallet` WHERE user_id={$user_id} AND amount > 0 GROUP BY `commission_status`")->result_array();


		foreach ($query as $key => $value) {
			switch ($value['commission_status']) {
				case '1':
					$totals['wallet_cancel_amount'] = (float)$value['amount'];
					$totals['wallet_cancel_count'] = (float)$value['counts'];
					break;
				case '2':
					$totals['wallet_trash_amount'] = (float)$value['amount'];
					$totals['wallet_trash_count'] = (float)$value['counts'];
					break;
				default: break;
			}
		}

		//user total clicks count
		$totals['total_clicks_count'] = $totals['click_localstore_total'] +
				                        $totals['vendor_click_localstore_total'] +
				                        $totals['click_external_total'] +
				                        $totals['vendor_click_external_total'] +
				                        $totals['click_form_total'];

		//user total clicks commission
		$totals['total_clicks_commission'] = $totals['click_localstore_commission'] +
					                        $totals['click_integration_commission'] +
					                        $totals['click_external_commission'] +
					                        $totals['vendor_click_external_commission'] +
					                        $totals['vendor_click_localstore_commission'] +
					                        $totals['click_form_commission'];

				                        

		$totals['user_balance'] = $this->getUserBalance($user_id);

		// $totals['user_balance'] = $this->getUserBalance($user_id) - $totals['wallet_accept_amount'];
		
		return $totals;
	}

	public function vendor_user_totals_week_growth($user_id, $filter = [],$current){
		$wallet_where = ' AND 1 ';
		$deposit_where = ' AND 1 ';
		$order_product_where = ' AND 1 ';
		$aw_where = ' AND 1 ';

		if (isset($filter['week'])) {
			$wallet_where .= ' AND YEARWEEK(wallet.`created_at`, 1) = YEARWEEK(CURDATE(), 1)-1';
			$deposit_where .= ' AND YEARWEEK(vendor_deposit.`vd_created_on`, 1) = YEARWEEK(CURDATE(), 1)-1';
			$order_product_where .= ' AND YEARWEEK(o.`created_at`, 1) = YEARWEEK(CURDATE(), 1)-1';
		}
		
		if (isset($filter['month'])) {
			$wallet_where .= ' AND MONTH(wallet.`created_at`) = MONTH(NOW())-1';
			$deposit_where .= ' AND MONTH(vendor_deposit.`vd_created_on`) = MONTH(NOW())-1';
			$order_product_where .= ' AND MONTH(o.`created_at`) = MONTH(NOW())-1';
		}
		
		if (isset($filter['year'])) {
			$year = date("Y")-1;
			$wallet_where .= ' AND YEAR(wallet.`created_at`) ='. $year;
			$deposit_where .= ' AND YEAR(vendor_deposit.`vd_created_on`) ='. $year;
			$order_product_where .= ' AND YEAR(o.`created_at`) ='. $year;
		}
		

		$click_localstore_commission = $this->db->query("SELECT SUM(amount) as total FROM wallet WHERE type IN ('click_commission','refer_click_commission') AND amount > 0 AND commission_status=0 AND user_id={$user_id} {$wallet_where} ")->row()->total;

		$click_external_commission = $this->db->query("SELECT SUM(amount) as total FROM wallet WHERE type IN ('external_click_commission') AND status > 0 AND is_action = 0 AND user_id = {$user_id} {$wallet_where} ")->row()->total;

		$click_action_commission = $this->db->query("SELECT SUM(amount) as total FROM wallet WHERE type IN('external_click_commission','external_click_comm_admin') AND is_action=1 AND status>0 AND user_id={$user_id} {$wallet_where} ")->row()->total;

		$refer_registration_commission = $this->db->query("SELECT SUM(amount) as total FROM wallet WHERE type IN('refer_registration_commission') AND status>0 AND user_id={$user_id} {$wallet_where} ")->row()->total;

		// Localstore sale and refer comission
		$sale_localstore_commission_query = $this->db->query("SELECT `wallet`.`amount` as commission FROM `order_products` INNER JOIN `order` o ON o.id= `order_products`.`order_id` INNER JOIN `wallet` ON `wallet`.`reference_id_2`= o.id WHERE `wallet`.`user_id` = {$user_id} AND `wallet`.`comm_from`!='ex' AND `wallet`.`status` > 0 AND `wallet`.`type` IN ('sale_commission','refer_sale_commission') {$wallet_where} group by `wallet`.`id` ")->result();
		
		$sale_localstore_commission = 0;
		foreach ($sale_localstore_commission_query as $key => $value)
			$sale_localstore_commission += $value->commission;


		$order_external_commission  = $this->db->query("SELECT SUM(amount) as total FROM wallet WHERE comm_from='ex' AND type IN ('sale_commission','refer_sale_commission') AND status>0 AND commission_status=0 AND user_id={$user_id} {$wallet_where}")->row()->total;

		
		$click_form_commission = $this->db->query("SELECT SUM(amount) as total FROM wallet WHERE type='form_click_commission' AND commission_status=0 AND user_id={$user_id} {$wallet_where}")->row()->total;


		$vendor_click_localstore_commission_pay = $this->db->query("SELECT SUM(amount) as total FROM wallet WHERE type IN ('click_commission','refer_click_commission') AND amount < 0 AND user_id={$user_id} {$wallet_where} ")->row()->total;

	
		$vendor_click_localstore_refer_commission_pay =  $this->db->query("SELECT SUM(amount) as total FROM wallet WHERE type = 'refer_click_commission' AND reference_id_2 = 'vendor_click_commission' AND is_action = 0 AND group_id IN (SELECT group_id FROM wallet WHERE reference_id_2 = 'vendor_pay_click_commission' AND is_action = 0 AND user_id={$user_id}) AND status > 0 {$wallet_where} ")->row()->total;


		$sql = "SELECT 
				    SUM(sub.total) as total
				FROM (	SELECT 	op.total
						FROM `order_products` op
						LEFT JOIN `order` o ON o.id= op.order_id 
						LEFT JOIN `wallet` w ON (w.reference_id = o.id AND w.type='sale_commission')
						WHERE op.vendor_id={$user_id} AND w.status > 0 {$order_product_where} GROUP BY op.id
					) as sub";
							
		$sale_localstore_total = $this->db->query($sql)->result();

		foreach ($sale_localstore_total as $key => $value){
			$vendor_sale_localstore_total += $value->total;
		}


		$vendor_sale_localstore_commission_pay = $this->db->query("SELECT SUM(`wallet`.`amount`) as total FROM `wallet`
			WHERE `wallet`.`type` IN ('sale_commission','admin_sale_commission') AND group_id IN (SELECT group_id FROM wallet WHERE type = 'vendor_sale_commission' AND user_id={$user_id}) AND `wallet`.`status` > 0 {$wallet_where} ")->row()->total;

		$vendor_sale_localstore_refer_commission_pay = $this->db->query("SELECT SUM(`wallet`.`amount`) as total FROM `wallet`
			WHERE `wallet`.`type` = 'refer_sale_commission' AND group_id IN (SELECT group_id FROM wallet WHERE type = 'vendor_sale_commission' AND user_id={$user_id}) AND `wallet`.`status` > 0 {$wallet_where} ")->row()->total;

		$vendor_click_external_commission_pay = $this->db->query("SELECT SUM(amount) as total FROM wallet WHERE type IN('external_click_comm_pay') AND status > 0 AND is_action = 0 AND user_id={$user_id} {$wallet_where} ")->row()->total;
		
		$vendor_click_external_refer_commission_pay =  $this->db->query("SELECT SUM(amount) as total FROM wallet WHERE type = 'refer_click_commission' AND status > 0  AND group_id IN (SELECT group_id as total FROM wallet WHERE type IN('external_click_comm_pay') AND status > 0 AND is_action = 0 AND user_id={$user_id} {$wallet_where}) ")->row()->total;

		$vendor_action_external_commission_pay = $this->db->query("SELECT SUM(amount) as total FROM wallet WHERE type IN('external_click_comm_pay') AND status > 0 AND is_action=1 AND user_id={$user_id} {$wallet_where} ")->row()->total;
		
		$vendor_action_external_refer_commission_pay = $this->db->query("SELECT SUM(amount) as total FROM wallet WHERE type = 'refer_click_commission' AND is_action = 1 AND group_id IN (SELECT group_id FROM wallet WHERE type = 'external_click_comm_pay' AND is_action = 1 AND user_id={$user_id}) AND status > 0 {$wallet_where} ")->row()->total;
		
		$vendor_order_external_commission_pay = $this->db->query("SELECT SUM(amount) as total FROM wallet WHERE type IN ('sale_commission_vendor_pay','admin_sale_commission_v_pay') AND user_id={$user_id} AND status > 0 {$wallet_where} ")->row()->total;

		// Vendor refer comission for external order
		$vendor_order_external_refer_commission_pay = $this->db->query("SELECT SUM(`wallet`.`amount`) as total FROM `wallet` INNER JOIN `integration_orders` ON `integration_orders`.`id` = `wallet`.`reference_id_2` WHERE `wallet`.`type` ='refer_sale_commission' AND `integration_orders`.`vendor_id` = {$user_id} AND `wallet`.`status` > 0 {$wallet_where} ")->row()->total;
		
		$welcome_bonus = $this->db->query("SELECT SUM(amount) as total FROM wallet WHERE type IN('welcome_bonus','membership_plan_bonus') AND user_id={$user_id} {$wallet_where} ")->row()->total;
		$admin_transaction = $this->db->query("SELECT SUM(amount) as total FROM wallet WHERE type IN ('admin_transaction','award_level_comission') AND user_id={$user_id} {$wallet_where} ")->row()->total;

		$user_balance = $this->db->query("SELECT SUM(vd_amount) as total FROM vendor_deposit WHERE vd_status=1 AND vd_user_id={$user_id} {$deposit_where} ")->row()->total;


		$user_balance += $welcome_bonus;
		$user_balance += $click_localstore_commission;
		
		$user_balance += $sale_localstore_commission;
		$user_balance += $click_external_commission;
		$user_balance += $click_action_commission;
		$user_balance += $order_external_commission;
		$user_balance += $click_form_commission;
		$user_balance += $admin_transaction;
		$user_balance += $refer_registration_commission;
		$user_balance += $vendor_sale_localstore_total;

		$user_balance -= abs($vendor_click_localstore_commission_pay);
		$user_balance -= abs($vendor_click_localstore_refer_commission_pay);
		$user_balance -= abs($vendor_sale_localstore_commission_pay);
		$user_balance -= abs($vendor_sale_localstore_refer_commission_pay);
		$user_balance -= abs($vendor_click_external_commission_pay);
		$user_balance -= abs($vendor_click_external_refer_commission_pay);
		$user_balance -= abs($vendor_action_external_commission_pay);
		$user_balance -= abs($vendor_action_external_refer_commission_pay);
		$user_balance -= abs($vendor_order_external_commission_pay);
		$user_balance -= abs($vendor_order_external_refer_commission_pay);

		return $user_balance;
	}
	
	public function getUserBalance($user_id, $filter = []){
		$wallet_where = ' AND 1 ';
		$deposit_where = ' AND 1 ';
		$order_product_where = ' AND 1 ';
		$aw_where = ' AND 1 ';

		if (isset($filter['week'])) {
			$wallet_where .= ' AND YEARWEEK(wallet.`created_at`, 1) = YEARWEEK(CURDATE(), 1)';
			$deposit_where .= ' AND YEARWEEK(vendor_deposit.`vd_created_on`, 1) = YEARWEEK(CURDATE(), 1)';
			$order_product_where .= ' AND YEARWEEK(o.`created_at`, 1) = YEARWEEK(CURDATE(), 1)';
		}
		
		if (isset($filter['month'])) {
			$wallet_where .= ' AND MONTH(wallet.`created_at`) = MONTH(NOW())';
			$deposit_where .= ' AND MONTH(vendor_deposit.`vd_created_on`) = MONTH(NOW())';
			$order_product_where .= ' AND MONTH(o.`created_at`) = MONTH(NOW())';
		}
		
		if (isset($filter['year'])) {
			$year = date("Y");
			$wallet_where .= ' AND YEAR(wallet.`created_at`) ='. $year;
			$deposit_where .= ' AND YEAR(vendor_deposit.`vd_created_on`) ='. $year;
			$order_product_where .= ' AND YEAR(o.`created_at`) ='. $year;
		}


		$click_localstore_commission = $this->db->query("SELECT SUM(amount) as total FROM wallet WHERE type IN ('click_commission','refer_click_commission') AND amount > 0 AND commission_status=0 AND user_id={$user_id} {$wallet_where} ")->row()->total;

		$click_external_commission = $this->db->query("SELECT SUM(amount) as total FROM wallet WHERE type IN ('external_click_commission') AND status > 0 AND is_action = 0 AND user_id = {$user_id} {$wallet_where} ")->row()->total;

		$click_action_commission = $this->db->query("SELECT SUM(amount) as total FROM wallet WHERE type IN('external_click_commission','external_click_comm_admin') AND is_action=1 AND status>0 AND user_id={$user_id} {$wallet_where} ")->row()->total;

		$refer_registration_commission = $this->db->query("SELECT SUM(amount) as total FROM wallet WHERE type IN('refer_registration_commission') AND status>0 AND user_id={$user_id} {$wallet_where} ")->row()->total;

		// Localstore sale and refer comission
		$sale_localstore_commission_query = $this->db->query("SELECT `wallet`.`amount` as commission FROM `order_products` INNER JOIN `order` o ON o.id= `order_products`.`order_id` INNER JOIN `wallet` ON `wallet`.`reference_id_2`= o.id WHERE `wallet`.`user_id` = {$user_id} AND `wallet`.`comm_from`!='ex' AND `wallet`.`status` > 0 AND `wallet`.`type` IN ('sale_commission','refer_sale_commission') {$wallet_where} group by `wallet`.`id` ")->result();
		
		$sale_localstore_commission = 0;
		foreach ($sale_localstore_commission_query as $key => $value)
			$sale_localstore_commission += $value->commission;


		$order_external_commission  = $this->db->query("SELECT SUM(amount) as total FROM wallet WHERE comm_from='ex' AND type IN ('sale_commission','refer_sale_commission') AND status>0 AND commission_status=0 AND user_id={$user_id} {$wallet_where}")->row()->total;

		
		$click_form_commission = $this->db->query("SELECT SUM(amount) as total FROM wallet WHERE type='form_click_commission' AND commission_status=0 AND user_id={$user_id} {$wallet_where}")->row()->total;


		$vendor_click_localstore_commission_pay = $this->db->query("SELECT SUM(amount) as total FROM wallet WHERE type IN ('click_commission','refer_click_commission') AND amount < 0 AND user_id={$user_id} {$wallet_where} ")->row()->total;

	
		$vendor_click_localstore_refer_commission_pay =  $this->db->query("SELECT SUM(amount) as total FROM wallet WHERE type = 'refer_click_commission' AND reference_id_2 = 'vendor_click_commission' AND is_action = 0 AND group_id IN (SELECT group_id FROM wallet WHERE reference_id_2 = 'vendor_pay_click_commission' AND is_action = 0 AND user_id={$user_id}) AND status > 0 {$wallet_where} ")->row()->total;


		$sql = "SELECT 
				    SUM(sub.total) as total
				FROM (	SELECT 	op.total
						FROM `order_products` op
						LEFT JOIN `order` o ON o.id= op.order_id 
						LEFT JOIN `wallet` w ON (w.reference_id = o.id AND w.type='sale_commission')
						WHERE op.vendor_id={$user_id} AND w.status > 0 {$order_product_where} GROUP BY op.id
					) as sub";
							
		$sale_localstore_total = $this->db->query($sql)->result();

		foreach ($sale_localstore_total as $key => $value){
			$vendor_sale_localstore_total += $value->total;
		}


		$vendor_sale_localstore_commission_pay = $this->db->query("SELECT SUM(`wallet`.`amount`) as total FROM `wallet`
			WHERE `wallet`.`type` IN ('sale_commission','admin_sale_commission') AND group_id IN (SELECT group_id FROM wallet WHERE type = 'vendor_sale_commission' AND user_id={$user_id}) AND `wallet`.`status` > 0 {$wallet_where} ")->row()->total;

		$vendor_sale_localstore_refer_commission_pay = $this->db->query("SELECT SUM(`wallet`.`amount`) as total FROM `wallet`
			WHERE `wallet`.`type` = 'refer_sale_commission' AND group_id IN (SELECT group_id FROM wallet WHERE type = 'vendor_sale_commission' AND user_id={$user_id}) AND `wallet`.`status` > 0 {$wallet_where} ")->row()->total;

		$vendor_click_external_commission_pay = $this->db->query("SELECT SUM(amount) as total FROM wallet WHERE type IN('external_click_comm_pay') AND status > 0 AND is_action = 0 AND user_id={$user_id} {$wallet_where} ")->row()->total;
		
		$vendor_click_external_refer_commission_pay =  $this->db->query("SELECT SUM(amount) as total FROM wallet WHERE type = 'refer_click_commission' AND status > 0  AND group_id IN (SELECT group_id as total FROM wallet WHERE type IN('external_click_comm_pay') AND status > 0 AND is_action = 0 AND user_id={$user_id} {$wallet_where}) ")->row()->total;

		$vendor_action_external_commission_pay = $this->db->query("SELECT SUM(amount) as total FROM wallet WHERE type IN('external_click_comm_pay') AND status > 0 AND is_action=1 AND user_id={$user_id} {$wallet_where} ")->row()->total;
		
		$vendor_action_external_refer_commission_pay = $this->db->query("SELECT SUM(amount) as total FROM wallet WHERE type = 'refer_click_commission' AND is_action = 1 AND group_id IN (SELECT group_id FROM wallet WHERE type = 'external_click_comm_pay' AND is_action = 1 AND user_id={$user_id}) AND status > 0 {$wallet_where} ")->row()->total;
		
		$vendor_order_external_commission_pay = $this->db->query("SELECT SUM(amount) as total FROM wallet WHERE type IN ('sale_commission_vendor_pay','admin_sale_commission_v_pay') AND user_id={$user_id} AND status > 0 {$wallet_where} ")->row()->total;

		// Vendor refer comission for external order
		$vendor_order_external_refer_commission_pay = $this->db->query("SELECT SUM(`wallet`.`amount`) as total FROM `wallet` INNER JOIN `integration_orders` ON `integration_orders`.`id` = `wallet`.`reference_id_2` WHERE `wallet`.`type` ='refer_sale_commission' AND `integration_orders`.`vendor_id` = {$user_id} AND `wallet`.`status` > 0 {$wallet_where} ")->row()->total;
		
		$welcome_bonus = $this->db->query("SELECT SUM(amount) as total FROM wallet WHERE type IN('welcome_bonus','membership_plan_bonus') AND user_id={$user_id} {$wallet_where} ")->row()->total;
		$admin_transaction = $this->db->query("SELECT SUM(amount) as total FROM wallet WHERE type IN ('admin_transaction','award_level_comission') AND user_id={$user_id} {$wallet_where} ")->row()->total;

		$user_balance = $this->db->query("SELECT SUM(vd_amount) as total FROM vendor_deposit WHERE vd_status=1 AND vd_user_id={$user_id} {$deposit_where} ")->row()->total;


		$user_balance += $welcome_bonus;
		$user_balance += $click_localstore_commission;
		
		$user_balance += $sale_localstore_commission;
		$user_balance += $click_external_commission;
		$user_balance += $click_action_commission;
		$user_balance += $order_external_commission;
		$user_balance += $click_form_commission;
		$user_balance += $admin_transaction;
		$user_balance += $refer_registration_commission;
		$user_balance += $vendor_sale_localstore_total;

		$user_balance -= abs($vendor_click_localstore_commission_pay);
		$user_balance -= abs($vendor_click_localstore_refer_commission_pay);
		$user_balance -= abs($vendor_sale_localstore_commission_pay);
		$user_balance -= abs($vendor_sale_localstore_refer_commission_pay);
		$user_balance -= abs($vendor_click_external_commission_pay);
		$user_balance -= abs($vendor_click_external_refer_commission_pay);
		$user_balance -= abs($vendor_action_external_commission_pay);
		$user_balance -= abs($vendor_action_external_refer_commission_pay);
		$user_balance -= abs($vendor_order_external_commission_pay);
		$user_balance -= abs($vendor_order_external_refer_commission_pay);

		return $user_balance;
	}

	public function chartUser($userid , $filter = []){
		$json = [];
		$orderBy = ' ORDER BY created_at DESC ';
		$orderByInteg = ' ORDER BY ica.created_at DESC ';

        if($filter['group'] == 'month'){
            if(isset($filter['year'])){
                $current_year = " YEAR(created_at) = ". $filter['year'];
            }else{
                $current_year = " YEAR(created_at) = ". date("Y");
            }
        } else{
        	$current_year = " YEAR(created_at) = ". $filter['year'];
            //$current_year .= ' 1=1 ';
        }

        if($filter['group'] == 'day'){ $groupby = 'CONCAT(DAY(created_at),"-",MONTH(created_at),"-",YEAR(created_at))';  $groupbyInteg = 'CONCAT(DAY(ica.created_at),"-",MONTH(ica.created_at),"-",YEAR(ica.created_at))';}
        else if($filter['group'] == 'week'){ $groupby = 'WEEK(created_at)';$groupbyInteg = 'WEEK(ica.created_at)';}
        else if($filter['group'] == 'month'){ $groupby = 'MONTH(created_at)';$groupbyInteg = 'MONTH(ica.created_at)';}
        else if($filter['group'] == 'year'){ $groupby = 'YEAR(created_at)';$groupbyInteg = 'YEAR(ica.created_at)';}

        $this->db->select(array(
            'sum(commission) as total_commission',
            'sum(total) as total_sale',
            'count(id) as total_order',
            "{$groupby} as groupby"
        ));

        $this->db->where($current_year);
        $this->db->order_by('created_at','DESC');
        $this->db->where('user_id',$userid);
        $this->db->group_by($groupby);
        $data = $this->db->get('integration_orders')->result_array();
        
        $chart = array();
        foreach ($data as $key => $value) {
            $chart[] = array(
				'key'              => $value['groupby'],
				'order_total'      => c_format($value['total_sale'], false),
				'order_count'      => (int)$value['total_order'],
				'order_commission' => c_format($value['total_commission'], false),
            );
        }

        $this->db->select(array(
            'sum(op.total) as total_sale',
            'count(op.id) as total_order',
            "{$groupby} as groupby",
            'sum(op.vendor_commission) as total_commission'
        ));
        $this->db->join("order_products op",'op.order_id = order.id','left');
        $this->db->where('op.vendor_id',$userid);   
        $this->db->where($current_year);   
        $this->db->where('order.status = 1');
        $this->db->group_by('op.order_id');
        $data = $this->db->get('order')->result_array();
        
        foreach ($data as $key => $value) {
            $chart[] = array(
                'key' => $value['groupby'],
                'order_total' => c_format($value['total_sale'], false),
                'order_count' => (int)$value['total_order'],
                'order_commission' => c_format($value['total_commission'], false),
            );
        }

        $this->db->select(array(
            'sum(op.total) as total_sale',
            'count(op.id) as total_order',
            "{$groupby} as groupby",
            'sum(op.commission) as total_commission'
        ));
        $this->db->join("order_products op",'op.order_id = order.id','left');
        $this->db->where('op.refer_id',$userid);   
        $this->db->where($current_year);   
        $this->db->where('order.status = 1');
        $this->db->group_by('op.order_id');
        $data = $this->db->get('order')->result_array();
        
        foreach ($data as $key => $value) {
            $chart[] = array(
                'key' => $value['groupby'],
                'order_total' => c_format($value['total_sale'], false),
                'order_count' => (int)$value['total_order'],
                'order_commission' => c_format($value['total_commission'], false),
            );
        }
        $vendor_action_external_total = $this->db->query("
			SELECT COUNT(ica.id) as total
			FROM integration_clicks_action ica 
				LEFT JOIN integration_tools it ON it.id = ica.tools_id 
			WHERE it.vendor_id={$userid} AND is_action=1 GROUP BY ". $groupbyInteg ."   ". $orderByInteg."")->row()->total;;
		
		
		// Vendor action external commission
		$vendor_action_external_commission -= $this->db->query("SELECT SUM(amount) as total FROM wallet WHERE type = 'external_click_comm_pay' AND is_action = 1 AND user_id={$userid} GROUP BY ". $groupby ."   ". $orderBy."")->row()->total;
		
		// Vendor refer action external comimssion
		$vendor_action_external_commission += $this->db->query("SELECT SUM(amount) as total FROM wallet WHERE type = 'refer_click_commission' AND is_action = 1 AND group_id IN (SELECT group_id FROM wallet WHERE type = 'external_click_comm_pay' AND is_action = 1 AND user_id={$userid}) GROUP BY ". $groupby ."   ". $orderBy."")->row()->total;
     
        $integration_click_amount = $this->db->query('SELECT '. $groupby . ' as groupby,SUM(amount) as total,COUNT(amount) as total_count FROM `wallet` WHERE is_action=1 AND type IN("external_click_commission","external_click_comm_admin") AND user_id='. $userid .' AND '. $_where . $current_year .' AND comm_from = "ex"  AND status > 0 GROUP BY '. $groupby .'   '. $orderBy )->result_array();

        
        foreach ($integration_click_amount as $value) {
            $chart[] = array(
                'key' => $value['groupby'],
                'action_commission' => c_format($value['total'] + $vendor_action_external_commission, false),
                'action_count' => $value['total_count'] + $vendor_action_external_total,
            );
        }
      

        $week = [];
        $day = [];
        $year = [];
        for($i=1;$i<=53;$i++){ $week[] = "Week {$i}"; }
        for($i=1;$i<=31;$i++){ $day[date($i."-n-Y")] = date($i."-n-Y"); }
        for($i=2016;$i<=date("Y");$i++){ $year[$i] = $i; }

        $defaultKey = [
        	'month' => ['','January','February','March','April','May','June','July','August','September','October','November','December'],
        	'week' => $week,
        	'day' => $day,
        	'year' => $year,
        ];

        $allData = [];
        foreach ($chart as $key => $value) {
        	$DK = $defaultKey[$filter['group']][$value['key']];
        	$allData[$DK]['order_total'] += isset($value['order_total']) ? $value['order_total'] : 0;
        	$allData[$DK]['order_count'] += isset($value['order_count']) ? $value['order_count'] : 0;
        	$allData[$DK]['order_commission'] += isset($value['order_commission']) ? $value['order_commission'] : 0;
        	$allData[$DK]['action_commission'] += isset($value['action_commission']) ? $value['action_commission'] : 0;
        	$allData[$DK]['action_count'] += isset($value['action_count']) ? $value['action_count'] : 0;
        }
        $fun_c_format = 'c_format';
        $orderTotal=0;
        foreach ($defaultKey[$filter['group']] as $key => $value) {
        	if($value){
        		$orderTotal+=isset($allData[$value]['order_total']) ? $allData[$value]['order_total'] : 0;
	        	$json['order_total'][$value] = isset($allData[$value]['order_total']) ? $allData[$value]['order_total'] : 0; 
	        	$json['order_count'][$value] = isset($allData[$value]['order_count']) ? $allData[$value]['order_count'] : 0; 
	        	$json['order_commission'][$value] = isset($allData[$value]['order_commission']) ? $allData[$value]['order_commission'] : 0; 
	        	$json['action_commission'][$value] = isset($allData[$value]['action_commission']) ? $allData[$value]['action_commission'] : 0; 
	        	$json['action_count'][$value] = isset($allData[$value]['action_count']) ? $allData[$value]['action_count'] : 0; 

	        	
        	}
        }
        
        if($orderTotal > 0){
       		$json['order_total_sum'] = $fun_c_format($orderTotal); 
		}else{
			$json['order_total_sum'] = 0; 
		}


        $json['keys'] = $defaultKey[$filter['group']];
        if ($filter['group'] == 'month') {
        	$json['keys'] = array_filter($defaultKey[$filter['group']]);
        }

        return $json;
	}

	public function getVendorStoreStatistic($vendor_id){
		// Total sale
		$sql = "SELECT SUM(`total`) as 'total'
				FROM `order_products` 
				WHERE `vendor_id` = ? ";
		$query = $this->db->query($sql,(int) $vendor_id);
		$result['total_sale'] = $query->row_array()['total'];

		// Count order
		$sql = "SELECT count(`id`) as 'count' 
				FROM `order_products`
				WHERE `vendor_id` = ? ";
		$query = $this->db->query($sql,(int) $vendor_id);
		$result['count_order'] = $query->row_array()['count'];

		// Count click
		$sql = "SELECT count(`id`) as 'count' 
				FROM `wallet` 
				WHERE `type` = 'click_commission'
				AND `reference_id_2` = 'vendor_pay_click_commission'
				AND `comm_from` = 'store'
				AND `user_id` = ? ";
		$query = $this->db->query($sql,(int) $vendor_id);
		$result['count_click'] = $query->row_array()['count'];

		// Count product
		$sql = "SELECT count(`id`) as 'count' 
				FROM `product` 
				INNER JOIN `product_affiliate`
				ON `product_affiliate`.`product_id` = `product`.`product_id`
				WHERE `product_affiliate`.`user_id` = ? ";
		$query = $this->db->query($sql,(int) $vendor_id);
		$result['count_product'] = $query->row_array()['count'];

		// Count coupon
		$sql = "SELECT count(`coupon_id`) as 'count' 
				FROM `coupon` 
				WHERE `coupon`.`vendor_id` = ? ";
		$query = $this->db->query($sql,(int) $vendor_id);
		$result['count_coupon'] = $query->row_array()['count'];

		return $result;
	}
}