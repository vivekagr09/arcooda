<?php
class Order_model extends MY_Model{

    public $status = array(
        //'0'  =>  'Waiting For Payment',
        '1'  =>  'Complete',
        '2'  =>  'Total not match',
        '3'  =>  'Denied',
        '4'  =>  'Expired',
        '5'  =>  'Failed',
        '6'  =>  'Pending',
        '7'  =>  'Processed',
        '8'  =>  'Refunded',
        '9'  =>  'Reversed',
        '10' =>  'Voided',
        '11' =>  'Canceled Reversal',
        '12'  => 'Waiting For Payment',
    );
    
    public function status($find=null) {
        $status = [
            '0'  =>  __('admin.waiting_for_payment'),
            '1'  =>  __('admin.complete'),
            '2'  =>  __('admin.total_not_match'),
            '3'  =>  __('admin.denied'),
            '4'  =>  __('admin.expired'),
            '5'  =>  __('admin.failed'),
            '6'  =>  __('admin.pending_integration'),
            '7'  =>  __('admin.processed'),
            '8'  =>  __('admin.refunded'),
            '9'  =>  __('admin.reversed'),
            '10' =>  __('admin.voided'),
            '11' =>  __('admin.cancel_reversal'),
            '12'  => __('admin.waiting_for_payment')
        ];
        if($find!=null) {
            return $status[$find];
        } else {
            return $status;
        }
    }

    public function PaymentMethods() {
        return  [
            'free_by_admin'=> 'Free by admin',
            'bank_transfer' => 'Bank Transfer',
            'cod' => 'Cash On Delivery',
            'cash_on_delivery' => 'Cash On Delivery',
            'flutterwave' => 'Flutterwave',
            'opay' => 'OPay',
            'paypal' => 'Paypal',
            'paypalstandard' => 'Paypal Standard',
            'paystack' => 'paystack',
            'paytm' => 'Paytm',
            'razorpay' => 'Razorpay',
            'skrill' => 'Skrill',
            'stripe' => 'Stripe',
            'xendit' => 'Xendit',
            'yookassa' => 'Yookassa',
            'toyyibpay' => 'Toyyibpay'
        ];
    }
    
    public function changeStatus($order_id, $status , $comment = ''){
        $this->load->model('Mail_model');

        $historyData = array(
            'order_id'        => $order_id,
            'paypal_status'   => $status,
            'comment'         => $comment,
            'payment_mode'    => '',
            'history_type'    => 'order',
            'created_at'      => date("Y-m-d H:i:s"),
            'order_status_id' => $status,
        );

        $this->db->insert('orders_history',$historyData);
        $this->db->set('status', $status);
        $this->db->where('id', $order_id);
        $this->db->update('order');

        if($status == 1){
            $sql = "UPDATE `orders_history` SET `paypal_status` = 'Complete' WHERE `orders_history`.`order_id` = ? AND `orders_history`.`history_type` = 'payment'";
            $this->db->query($sql,(int) $order_id);

            $this->db->query("UPDATE `wallet` SET status = 1 WHERE status = 0 AND user_id != 1 AND type IN('sale_commission','refer_sale_commission','vendor_sale_commission', 'admin_sale_commission') AND reference_id_2 = {$order_id} ");

            $this->db->query("UPDATE `wallet` SET status = 3 WHERE status = 0 AND user_id = 1 AND type IN('sale_commission','refer_sale_commission','vendor_sale_commission','admin_sale_commission') AND reference_id_2 = {$order_id} ");
            
            $this->Mail_model->send_commition_mail($order_id, true);

            

            $order_info = $this->getOrder($order_id, 'store');
            $wallet_group_id = time().rand(10,100);
            $products = $this->getProducts($order_id);

            foreach ($products as $key => $product) {
                $check = $this->db->query("SELECT * FROM  `wallet` WHERE user_id = ".$product['vendor_id']." AND type IN('vendor_sale_commission') AND reference_id_2 = {$order_id} ")->num_rows();

                if(! $check > 0 ) {
                    if($product['refer_id'] > 0){
                        $is_recurrsive = false;
                        if($product['form_id'] == 0){
                            $orignal_pro = $this->Product_model->getProductById($product['product_id']);
                            $product_recursion_type = $orignal_pro->product_recursion_type;
                            if($product_recursion_type){
                                $is_recurrsive = true;

                                if($product_recursion_type == 'default'){
                                    $pro_setting = $this->Product_model->getSettings('productsetting');     
                                    $recursion = $pro_setting['product_recursion'];
                                    $recursion_endtime = $pro_setting['recursion_endtime'];
                                    $recursion_custom_time = ($recursion == 'custom_time' ) ? $pro_setting['recursion_custom_time'] : 0;
                                }else{
                                    $recursion = $orignal_pro->product_recursion;
                                    $recursion_endtime = $orignal_pro->recursion_endtime;
                                    $recursion_custom_time = ($recursion == 'custom_time' ) ? $orignal_pro->recursion_custom_time : 0;
                                }
                            }
                        }else{
                            $this->load->model('Form_model');
                            $orignal_form = $this->Form_model->getForm($product['form_id']);
                            $form_recursion_type = $orignal_form['form_recursion_type'];
                            if($form_recursion_type){
                                $is_recurrsive = true;
                                if($form_recursion_type == 'default'){
                                    $form_setting = $this->Product_model->getSettings('formsetting');
                                    $recursion = $form_setting['form_recursion'];
                                    $recursion_custom_time = ($recursion == 'custom_time' ) ? $form_setting['recursion_custom_time'] : 0;
                                    $recursion_endtime = $form_setting['recursion_endtime'];
                                }else{
                                    $recursion = $orignal_form['form_recursion'];
                                    $recursion_custom_time = ($recursion == 'custom_time' ) ? $orignal_form['recursion_custom_time'] : 0;
                                    $recursion_endtime = $orignal_form['recursion_endtime'];
                                }
                            }
                        }
                    }

                    if($product['vendor_commission'] > 0){

                        $total_deduct = $this->db->query("SELECT SUM(amount) as total_deduct FROM `wallet` WHERE status > 0 AND type IN('sale_commission','refer_sale_commission', 'admin_sale_commission') AND reference_id_2 = {$order_id} GROUP BY reference_id_2")->row()->total_deduct;

                        $recursion_id3 = $this->Wallet_model->addTransaction(array(
                            'status'       => 1,
                            'user_id'      => $product['vendor_id'],
                            'group_id' => $wallet_group_id,
                            'amount'       => $product['vendor_commission'],
                            'comment'      => 'Vendor Sell Earning Commission for order Id order_id='. $order_id .' | Order By : '. $order_info['firstname'] ." " .$order_info['lastname'] ." | ".c_format($total_deduct)." deducted from order as admin and affiliate commission | sale commission  <br> Sale done from ip_message" ,
                            'type'         => 'vendor_sale_commission',
                            'reference_id_2' => $order_id,
                            'reference_id' => $product['product_id'],
                            'is_vendor'    => 1,
                        ));

                        if($is_recurrsive){
                            $this->Wallet_model->addTransactionRecursion(array(
                                'transaction_id'          => $recursion_id3,
                                'type'                    => $recursion,
                                'custom_time'             => $recursion_custom_time,
                                'force_recursion_endtime' => $recursion_endtime,
                            ));                 
                        }
                    }
                }
            }
            // }
        }

        $this->Mail_model->send_order_mail($order_id);
    }

    public function getAllClickLogs($filter = array()){
        $where1  = $where2 = $where3 = '';

        if(isset($filter['user_id'])){
            $where1 .= " AND (ic.user_id = ". (int)$filter['user_id'] ." OR ic.vendor_id = ". (int)$filter['user_id'] .")";
            //$where1 .= " AND ic.user_id = ". (int)$filter['user_id'];
            $where2 .= " AND pa.user_id = ". (int)$filter['user_id'];
            $where3 .= " AND op.refer_id = ". (int)$filter['user_id'];
        }

        $union1 = array(
            '"ex" as type',
            'u.firstname',
            'u.lastname ',
            'ic.user_id',
            'ic.created_at',
            'ic.country_code',
            'ic.ip',

            'ic.id',
            'ic.base_url',
            'ic.link',
            'ic.agent',
            'ic.browserName',
            'ic.browserVersion',
            'ic.systemString',
            'ic.osPlatform',
            'ic.osVersion',
            'ic.osShortVersion',
            'ic.isMobile',
            'ic.mobileName',
            'ic.osArch',
            'ic.isIntel',
            'ic.isAMD',
            'ic.isPPC',
            'ic.click_id',
            'ic.click_type',
            'ic.custom_data',

            '"action_id" as action_id',
            '"action_type" as action_type',
            '"product_id" as product_id',
            '"viewer_id" as viewer_id',
            '"counter" as counter',
            '"pay_commition" as pay_commition',  

            '"status" as status',
            '"txn_id" as txn_id',
            '"address" as address',
            '"country_id" as country_id',
            '"state_id" as state_id',
            '"city" as city',
            '"zip_code" as zip_code',
            '"phone" as phone',
            '"payment_method" as payment_method',
            '"shipping_cost" as shipping_cost',
            '"total" as total',
            '"coupon_discount" as coupon_discount',
            '"total_commition" as total_commition',
            '"shipping_charge" as shipping_charge',
            '"currency_code" as currency_code',
            '"allow_shipping" as allow_shipping',
            '"files" as files',
            '"comment" as comment',
        );

        $union2 = array(
            '"store" as type',
            'u.firstname',
            'u.lastname ',
            'pa.user_id',
            'pa.created_at',
            'pa.country_code',
            'pa.user_ip as ip',

            '"id" as id',
            '"base_url" as base_url',
            '"link" as link',
            '"agent" as agent',
            '"browserName" as browserName',
            '"browserVersion" as browserVersion',
            '"systemString" as systemString',
            '"osPlatform" as osPlatform',
            '"osVersion" as osVersion',
            '"osShortVersion" as osShortVersion',
            '"isMobile" as isMobile',
            '"mobileName" as mobileName',
            '"osArch" as osArch',
            '"isIntel" as isIntel',
            '"isAMD" as isAMD',
            '"isPPC" as isPPC',
            '"click_id" as click_id',
            '"click_type" as click_type',
            '"custom_data" as custom_data',

            'pa.action_id',
            'pa.action_type',
            'pa.product_id',
            'pa.viewer_id',
            'pa.counter',
            'pa.pay_commition',

            '"status" as status',
            '"txn_id" as txn_id',
            '"address" as address',
            '"country_id" as country_id',
            '"state_id" as state_id',
            '"city" as city',
            '"zip_code" as zip_code',
            '"phone" as phone',
            '"payment_method" as payment_method',
            '"shipping_cost" as shipping_cost',
            '"total" as total',
            '"coupon_discount" as coupon_discount',
            '"total_commition" as total_commition',
            '"shipping_charge" as shipping_charge',
            '"currency_code" as currency_code',
            '"allow_shipping" as allow_shipping',
            '"files" as files',
            '"comment" as comment',
        );

        $union3 = array(
            '"order" as type',
            'u.firstname',
            'u.lastname ',
            'o.user_id',
            'o.created_at',
            'o.country_code',
            'o.ip',

            'o.id',

            '"base_url" as base_url',
            '"link" as link',
            '"agent" as agent',
            '"browserName" as browserName',
            '"browserVersion" as browserVersion',
            '"systemString" as systemString',
            '"osPlatform" as osPlatform',
            '"osVersion" as osVersion',
            '"osShortVersion" as osShortVersion',
            '"isMobile" as isMobile',
            '"mobileName" as mobileName',
            '"osArch" as osArch',
            '"isIntel" as isIntel',
            '"isAMD" as isAMD',
            '"isPPC" as isPPC',
            '"click_id" as click_id',
            '"click_type" as click_type',
            '"custom_data" as custom_data',

            '"action_id" as action_id',
            '"action_type" as action_type',
            '"product_id" as product_id',
            '"viewer_id" as viewer_id',
            '"counter" as counter',
            '"pay_commition" as pay_commition', 

            'o.status',
            'o.txn_id',
            'o.address',
            'o.country_id',
            'o.state_id',
            'o.city',
            'o.zip_code',
            'o.phone',
            'o.payment_method',
            'o.shipping_cost',
            'o.total',
            'o.coupon_discount',
            'o.total_commition',
            'o.shipping_charge',
            'o.currency_code',
            'o.allow_shipping',
            'o.files',
            'o.comment',
        );


        $select1 = implode(",", $union1);         
        $union_query1 = "
        SELECT {$select1} FROM `integration_clicks_logs` ic 
        LEFT JOIN users u ON u.id = ic.user_id
        WHERE 1 {$where1}
        ";

        $select2 = implode(",", $union2);
        $union_query2 = "
        SELECT {$select2}
        FROM product_action pa
        LEFT JOIN users u ON u.id = pa.user_id  
        WHERE 1 {$where2}
        ";

        $union2[0] = '"store_other_aff" as type';
        $select5 = implode(",", $union2);
        $union_query5 = "
        SELECT {$select5}
        FROM product_action pa
        LEFT JOIN users u ON u.id = pa.user_id  
        LEFT JOIN product_affiliate paff ON (paff.product_id = pa.product_id) 
        WHERE paff.user_id= ". (int)$filter['user_id'] ."
        ";

        $union2[0] = '"store_admin" as type';
        $select4 = implode(",", $union2);
        $union_query4 = "
        SELECT {$select4}
        FROM product_action_admin pa
        LEFT JOIN users u ON u.id = pa.user_id  
        LEFT JOIN product_affiliate paff ON (paff.product_id = pa.product_id)
        WHERE  paff.user_id= ". (int)$filter['user_id'] ."
        ";

        $select3 = implode(",", $union3);
        $union_query3 = "
        SELECT {$select3}
        FROM `order` o 
        LEFT JOIN order_products op ON op.order_id = o.id
        LEFT JOIN users u ON u.id = o.user_id
        WHERE 1 AND vendor_id = 0 {$where3} GROUP BY order_id
        ";

        
        $union = "SELECT SQL_CALC_FOUND_ROWS * FROM ( 
            ({$union_query1}) UNION ALL 
            ({$union_query2}) UNION ALL 
            ({$union_query3}) UNION ALL 
            ({$union_query5}) UNION ALL 
            ({$union_query4}) ) as tmp";
        $union.= " ORDER BY created_at DESC ";
        if (isset($filter['page'],$filter['limit'])) {
            $offset = (($filter['page']-1) * $filter['limit']);
            $union.= " LIMIT {$offset},". $filter['limit'];
        }
         
        $clicks = $this->db->query($union)->result_array();
        $total = $this->db->query("SELECT FOUND_ROWS() AS total")->row()->total;

        $data = array();
        foreach ($clicks as $key => $value) {
            if($value['type'] == 'store'|| $value['type'] == 'store_admin' || $value['type'] == 'store_other_aff'){ 
                $data[] = array(
                    'type'          => $value['type'],
                    'firstname'     => $value['firstname'],
                    'lastname'      => $value['lastname'],
                    'action_id'     => $value['action_id'],
                    'action_type'   => $value['action_type'],
                    'product_id'    => $value['product_id'],
                    'user_id'       => $value['user_id'],
                    'ip'            => $value['user_ip'],
                    'viewer_id'     => $value['viewer_id'],
                    'counter'       => $value['counter'],
                    'pay_commition' => $value['pay_commition'],
                    'created_at'    => $value['created_at'],
                    'country_code'  => $value['country_code'],
                    'flag'          => "<img class='country-flag' title='". $value['country_code'] ."' src='". base_url('assets/vertical/assets/images/flags/'. strtolower($value['country_code'])) .".png'>",
                    'custom_data'   => (isset($value['custom_data']) && !empty($value['custom_data'])) ? json_decode($value['custom_data'], 1) : array(),
                );
            }
            else if($value['type'] == 'order'){ 
                $data[] = array(
                    'type'            => $value['type'],
                    'status'          => $value['status'],
                    'txn_id'          => $value['txn_id'],
                    'address'         => $value['address'],
                    'country_id'      => $value['country_id'],
                    'state_id'        => $value['state_id'],
                    'city'            => $value['city'],
                    'zip_code'        => $value['zip_code'],
                    'phone'           => $value['phone'],
                    'payment_method'  => $value['payment_method'],
                    'shipping_cost'   => $value['shipping_cost'],
                    'total'           => $value['total'],
                    'coupon_discount' => $value['coupon_discount'],
                    'total_commition' => $value['total_commition'],
                    'shipping_charge' => $value['shipping_charge'],
                    'currency_code'   => $value['currency_code'],
                    'allow_shipping'  => $value['allow_shipping'],
                    'files'           => $value['files'],
                    'comment'         => $value['comment'],
                    'firstname'       => $value['firstname'],
                    'lastname'        => $value['lastname'],
                    'user_id'         => $value['user_id'],
                    'created_at'      => $value['created_at'],
                    'country_code'    => $value['country_code'],
                    'ip'              => $value['ip'],
                    'id'              => $value['id'],
                    'flag'            => "<img class='country-flag' title='". $value['country_code'] ."' src='". base_url('assets/vertical/assets/images/flags/'. strtolower($value['country_code'])) .".png'>",
                    'custom_data'   => (isset($value['custom_data']) && !empty($value['custom_data'])) ? json_decode($value['custom_data'], 1) : array(),
                );
            } else {
                $data[] = array(
                    'type'          => $value['type'],
                    'id'             => $value['id'],
                    'base_url'       => $value['base_url'],
                    'link'           => $value['link'],
                    'agent'          => $value['agent'],
                    'browserName'    => $value['browserName'],
                    'browserVersion' => $value['browserVersion'],
                    'systemString'   => $value['systemString'],
                    'osPlatform'     => $value['osPlatform'],
                    'osVersion'      => $value['osVersion'],
                    'osShortVersion' => $value['osShortVersion'],
                    'isMobile'       => $value['isMobile'],
                    'mobileName'     => $value['mobileName'],
                    'osArch'         => $value['osArch'],
                    'isIntel'        => $value['isIntel'],
                    'isAMD'          => $value['isAMD'],
                    'isPPC'          => $value['isPPC'],
                    'ip'             => $value['ip'],
                    'country_code'   => $value['country_code'],
                    'created_at'     => date("d-m-Y h:i A",strtotime($value['created_at'])),
                    'click_id'       => $value['click_id'],
                    'username'       => $value['username'],
                    'click_type'     => str_replace("_", " ", ucfirst($value['click_type'])),
                    'flag'           => "<img class='country-flag' title='". $value['country_code'] ."' src='". base_url('assets/vertical/assets/images/flags/'. strtolower($value['country_code'])) .".png'>",
                    'custom_data'   => (isset($value['custom_data']) && !empty($value['custom_data'])) ? json_decode($value['custom_data'], 1) : array(),
                );
            }
        }
        
        return array($data,$total);
    }

    public function getAllOrders($filter = array(), $addShipping = true){
        $store_setting = $this->Product_model->getSettings('store');

        $where1  = $where2 = '';

        if(isset($filter['getSingleOrder'])) {
            if($filter['getSingleOrder'] == 'ex') {
                $where1 .= " AND o.id < 0";
                $where2 .= " AND io.id=".$filter['order_id'];
            } else {
                $where1 .= " AND o.id=".$filter['order_id'];
                $where2 .= " AND io.id < 0";
            }
        } else {

            if(!$store_setting['status']){
                $where1 .= " AND 1=2 ";
            }
            
            if(isset($filter['myorder']))
            {
                 $where1 .= " AND (op.vendor_id = ". (int)$filter['user_id'] .")";

            }
            else if(isset($filter['external_orders'])) 
            {
                 $where2 .= " AND (io.vendor_id = ". (int)$filter['user_id'] ." ) ";
            }
            else if(isset($filter['user_id']))
            {
                if(isset($filter['is_vendor']) && $filter['is_vendor']==1 )
                {
                    $where1 .= " AND (  op.refer_id = ". (int)$filter['user_id'] .")";
                    $where2 .= " AND (io.user_id = ". (int)$filter['user_id'] ." ) ";

                }
                else
                {
                    $where1 .= " AND (op.vendor_id = ". (int)$filter['user_id'] ." OR op.refer_id = ". (int)$filter['user_id'] .")";
                    $where2 .= " AND (io.user_id = ". (int)$filter['user_id'] ." OR io.vendor_id = ". (int)$filter['user_id'] .") ";
                }
                
            }

            if(isset($filter['o_status'])){
                $where1 .= " AND o.status = ". (int)$filter['o_status'];
                if((int)$filter['o_status'] != 1){
                    $where2 .= " AND 1=2 ";
                }
            }

            if(isset($filter['o_status_gt'])){
                $where1 .= " AND o.status >= ". (int)$filter['o_status'];
            }
        }

        $union1 = array(
            '"store" as type',
            '"[]" as custom_data',
            '(SELECT status FROM wallet WHERE wallet.reference_id_2 = op.order_id AND comm_from="store" AND type LIKE "%sale%" ORDER BY wallet.id ASC  LIMIT 1) as wallet_status',
            '(SELECT commission_status FROM wallet WHERE wallet.reference_id_2 = op.order_id AND comm_from="store" AND type LIKE "%sale%" ORDER BY wallet.id ASC  LIMIT 1) as wallet_commission_status',
            '(SELECT type FROM wallet WHERE wallet.reference_id_2 = op.order_id AND comm_from="store" AND type LIKE "%sale%" ORDER BY wallet.id ASC  LIMIT 1) as wallet_type',
            '(SELECT comm_from FROM wallet WHERE wallet.reference_id_2 = op.order_id AND comm_from="store" AND type LIKE "%sale%" ORDER BY wallet.id ASC  LIMIT 1) as wallet_comm_from',
            '(SELECT reference_id FROM wallet WHERE wallet.reference_id_2 = op.order_id AND comm_from="store" AND type LIKE "%sale%" ORDER BY wallet.id ASC  LIMIT 1) as wallet_reference_id_2',
            '(SELECT comment FROM wallet WHERE wallet.reference_id_2 = op.order_id AND comm_from="store" AND type LIKE "%sale%" ORDER BY wallet.id ASC  LIMIT 1) as wallet_comment',
            '(SELECT is_action FROM wallet WHERE wallet.reference_id_2 = op.order_id AND comm_from="store" AND type LIKE "%sale%" ORDER BY wallet.id ASC  LIMIT 1) as wallet_is_action',
            'o.id',
            'o.status',
            'o.user_id',
            'o.total',
            'o.ip',
            'o.country_code',
            'u.firstname',
            'u.lastname ',
            'o.created_at',    

            '"order_id" as order_id',
            '"product_ids" as product_ids',
            '"currency" as currency',
            '"commission_type" as commission_type',
            '"commission" as commission',
            '"base_url" as base_url',
            '"ads_id" as ads_id',
            '"script_name" as script_name',       

            'o.txn_id',
            'o.address',
            'o.country_id',
            'o.state_id',
            'o.city',
            'o.zip_code',
            'o.phone',
            'o.payment_method',
            'o.shipping_cost',
            'o.tax_cost',
            'o.coupon_discount',
            'o.total_commition',
            'o.shipping_charge',
            'o.currency_code',
            'o.allow_shipping',
            'o.files',
            'o.comment',
            'sum(op.total) AS total_sum',
            '(SELECT paypal_status FROM orders_history WHERE orders_history.order_id = o.id ORDER BY id DESC LIMIT 1) as last_status',
        );

        $union2 = array(
            '"ex" as type',
            'io.custom_data as custom_data',
            '(SELECT status FROM wallet WHERE wallet.reference_id_2 = io.id AND type LIKE "%sale%" ORDER BY wallet.id ASC LIMIT 1 ) as wallet_status',
            '(SELECT commission_status FROM wallet WHERE wallet.reference_id_2 = io.id AND type LIKE "%sale%" ORDER BY wallet.id ASC LIMIT 1) as wallet_commission_status',
            '(SELECT type FROM wallet WHERE wallet.reference_id_2 = io.id AND type LIKE "%sale%" ORDER BY wallet.id ASC LIMIT 1 ) as wallet_type',
            '(SELECT comm_from FROM wallet WHERE wallet.reference_id_2 = io.id AND type LIKE "%sale%" ORDER BY wallet.id ASC LIMIT 1 ) as wallet_comm_from',
            '(SELECT reference_id_2 FROM wallet WHERE wallet.reference_id_2 = io.id AND type LIKE "%sale%" ORDER BY wallet.id ASC LIMIT 1 ) as wallet_reference_id_2',
            '(SELECT comment FROM wallet WHERE wallet.reference_id_2 = io.id AND type LIKE "%sale%" ORDER BY wallet.id ASC LIMIT 1 ) as wallet_comment',
            '(SELECT is_action FROM wallet WHERE wallet.reference_id_2 = io.id AND type LIKE "%sale%" ORDER BY wallet.id ASC LIMIT 1 ) as wallet_is_action',
            'io.id',
            'io.status',
            'io.user_id',
            'io.total',
            'io.ip',
            'io.country_code',
            'u.firstname',
            'u.lastname ',
            'io.created_at',
            'io.order_id',
            'io.product_ids',
            'io.currency',
            'io.commission_type',
            'io.commission',
            'io.base_url',
            'io.ads_id',
            'io.script_name',

            '"txn_id" as txn_id',
            '"address" as address',
            '"country_id" as country_id',
            '"state_id" as state_id',
            '"city" as city',
            '"zip_code" as zip_code',
            '"phone" as phone',
            '"payment_method" as payment_method',
            '"shipping_cost" as shipping_cost',
            '"tax_cost" as tax_cost',
            '"coupon_discount" as coupon_discount',
            '"total_commition" as total_commition',
            '"shipping_charge" as shipping_charge',
            '"currency_code" as currency_code',
            '"allow_shipping" as allow_shipping',
            '"files" as files',
            '"comment" as comment',
            '"total_sum" as total_sum',
            '"last_status" as last_status',
        );

        $select1 = implode(",", $union1);
        $union_query1 = "
        SELECT {$select1} FROM `order` o 
        LEFT JOIN users u ON u.id = o.user_id   
        LEFT JOIN order_products op ON op.order_id = o.id
        WHERE o.status > 0 {$where1} 
        GROUP BY o.id
        ORDER BY o.id DESC
        ";
        
        $select2 = implode(",", $union2);
        $union_query2 = "
        SELECT {$select2} 
        FROM integration_orders io
        LEFT JOIN users u ON u.id = io.user_id  
        WHERE 1 {$where2}
        ";
         

        if(isset($filter['myorder']))
        {
            $union = "
            SELECT 
            SQL_CALC_FOUND_ROWS * 
            FROM (({$union_query1})) as tmp";
        }
        else if(isset($filter['external_orders'])) 
        {
             $union = "
            SELECT 
            SQL_CALC_FOUND_ROWS * 
            FROM (({$union_query2})) as tmp";
        }
        else
        {
            $union = "
            SELECT 
            SQL_CALC_FOUND_ROWS * 
            FROM (({$union_query1}) 
                UNION ALL ({$union_query2})) as tmp";
        }

        $union.= " ORDER BY created_at DESC ";

        if (isset($filter['page'],$filter['limit'])) {
            $offset = (($filter['page']-1) * $filter['limit']);
            $union.= " LIMIT {$offset},". $filter['limit'];
        }
   
        $orders = $this->db->query($union)->result_array(); 


        $total = $this->db->query("SELECT FOUND_ROWS() AS total")->row()->total;

        $data = array();
        foreach ($orders as $key => $value) {
            if($value['type'] == 'store'){
                $products        = $this->Order_model->getProducts($value['id'],['vendor_or_refer_id' => $filter['user_id']]);
                $payment_history = $this->Order_model->getHistory($value['id']);
                $order_history   = $this->Order_model->getHistory($value['id'], 'order');
                $totals = $this->Order_model->getTotals($products,$value);
                $totalVendorStoreLevels= $this->Order_model->getVendorStoreLevels($products);
                $commission_amount = 0;
                foreach ($products as $key => $p) {
                    $commission_amount += ((float)$p['commission'] + (float)$p['admin_commission']+(float)$totalVendorStoreLevels);
                }

                $total_sum = $addShipping ? ($value['total_sum'] + $value['shipping_cost']) : $value['total_sum'];

                $wallet_trans = $this->db->query('SELECT id FROM wallet WHERE type LIKE "%sale%" AND comm_from="store" AND reference_id='.$value['id'])->result_array();
                $trans = [];
                foreach ($wallet_trans as $wa) {
                    $trans[] = $wa['id'];
                }

                $data[] = array(
                    'id'                 => $value['id'],
                    'status'             => $value['status'],
                    'wallet_status'      => $value['wallet_status'],
                    'commission_amount'  => (float)$commission_amount,
                    'wallet_commission_status' => $value['wallet_commission_status'],
                    'wallet_transactions' => implode(', ', $trans),
                    'wallet_type'         => $value['wallet_type'],
                    'wallet_comm_from'    => $value['wallet_comm_from'],
                    'wallet_reference_id_2' => $value['wallet_reference_id_2'],
                    'wallet_comment'    => $value['wallet_comment'],
                    'wallet_is_action' => $value['wallet_is_action'],
                    'type'               => $value['type'],
                    'txn_id'             => $value['txn_id'],
                    'user_id'            => $value['user_id'],
                    'address'            => $value['address'],
                    'country_id'         => $value['country_id'],
                    'state_id'           => $value['state_id'],
                    'city'               => $value['city'],
                    'zip_code'           => $value['zip_code'],
                    'phone'              => $value['phone'],
                    'payment_method'     => $value['payment_method'],
                    'shipping_cost'      => $value['shipping_cost'],
                    'tax_cost'      => $value['tax_cost'],
                    'total'              => $value['total'],
                    'coupon_discount'    => $value['coupon_discount'],
                    'total_commition'    => $value['total_commition'],
                    'shipping_charge'    => $value['shipping_charge'],
                    'currency_code'      => $value['currency_code'],
                    'created_at'         => $value['created_at'],
                    'allow_shipping'     => $value['allow_shipping'],
                    'ip'                 => $value['ip'],
                    'country_code'       => $value['country_code'],
                    'files'              => $value['files'],
                    'comment'            => $value['comment'],
                    'firstname'          => $value['firstname'],
                    'lastname'           => $value['lastname'],
                    'total_sum'          => $total_sum,
                    'last_status'        => $value['last_status'],
                    'custom_data'        => $value['custom_data'],
                    'products'           => $products,
                    'order_history'      => $order_history,
                    'payment_history'    => $payment_history,
                    'totals'             => $totals,
                    'order_country_flag' => '<img style="width: 20px;margin: 0 10px;" src="'. base_url('assets/vertical/assets/images/flags/'. strtolower($value['country_code'])  ) .'.png"> IP: '. $value['ip'],
                );
            } else{
                $wallet_trans = $this->db->query('SELECT id FROM wallet WHERE type LIKE "%sale%" AND comm_from="ex" AND reference_id_2='.$value['id'])->result_array();
                $trans = [];
                foreach ($wallet_trans as $wa) {
                    $trans[] = $wa['id'];
                }
                $data[] = array(
                    'id'              => $value['id'],
                    'status'             => $value['status'],
                    'wallet_status'            => $value['wallet_status'],
                    'wallet_commission_status' => $value['wallet_commission_status'],
                    'wallet_transactions' => implode(', ', $trans),
                    'wallet_type'         => $value['wallet_type'],
                    'wallet_comm_from'    => $value['wallet_comm_from'],
                    'wallet_reference_id_2' => $value['wallet_reference_id_2'],
                    'wallet_comment'    => $value['wallet_comment'],
                    'wallet_is_action' => $value['wallet_is_action'],
                    'type'            => $value['type'],
                    'order_id'        => $value['order_id'],
                    'product_ids'     => $value['product_ids'],
                    'total'           => $value['total'],
                    'currency'        => $value['currency'],
                    'user_id'         => $value['user_id'],
                    'commission_type' => $value['commission_type'],
                    'commission'      => $value['commission'],
                    'ip'              => $value['ip'],
                    'country_code'    => $value['country_code'],
                    'base_url'        => $value['base_url'],
                    'ads_id'          => $value['ads_id'],
                    'script_name'     => $value['script_name'],
                    'custom_data'        => $value['custom_data'],
                    'created_at'      => date("d-m-Y h:i A", strtotime($value['created_at'])),
                    'user_name'       => $value['firstname'] ." " .$value['lastname'],
                    'order_country_flag' =>  '<img style="width: 20px;margin: 0 10px;" src="'. base_url('assets/vertical/assets/images/flags/'. strtolower($value['country_code'])  ) .'.png"> IP: '. $value['ip'],
                );
            }
        }
        
        return array($data,$total);
    }


    public function getAllOrdersForDashboard($filter = array(), $addShipping = true){
        $store_setting = $this->Product_model->getSettings('store');

        $where1  = $where2 = '';

        if(isset($filter['getSingleOrder'])) {
            if($filter['getSingleOrder'] == 'ex') {
                $where1 .= " AND o.id < 0";
                $where2 .= " AND io.id=".$filter['order_id'];
            } else {
                $where1 .= " AND o.id=".$filter['order_id'];
                $where2 .= " AND io.id < 0";
            }
        } else {

            if(!$store_setting['status']){
                $where1 .= " AND 1=2 ";
            }
            
            if(isset($filter['myorder']))
            {
                 $where1 .= " AND (op.vendor_id = ". (int)$filter['user_id'] .")";

            }
            else if(isset($filter['external_orders'])) 
            {
                 $where2 .= " AND (io.vendor_id = ". (int)$filter['user_id'] ." ) ";
            }
            else if(isset($filter['user_id']))
            {
                if(isset($filter['is_vendor']) && $filter['is_vendor']==1 )
                {
                    $where1 .= " AND (  op.refer_id = ". (int)$filter['user_id'] .")";
                    $where2 .= " AND (io.user_id = ". (int)$filter['user_id'] ." ) ";

                }
                else
                {
                    $where1 .= " AND (op.vendor_id = ". (int)$filter['user_id'] ." OR op.refer_id = ". (int)$filter['user_id'] .")";
                    $where2 .= " AND (io.user_id = ". (int)$filter['user_id'] ." OR io.vendor_id = ". (int)$filter['user_id'] .") ";
                }
                
            }

            if(isset($filter['o_status'])){
                $where1 .= " AND o.status = ". (int)$filter['o_status'];
                if((int)$filter['o_status'] != 1){
                    $where2 .= " AND 1=2 ";
                }
            }

            if(isset($filter['o_status_gt'])){
                $where1 .= " AND o.status >= ". (int)$filter['o_status'];
            }
        }

        $union1 = array(
            '"store" as type',
            '"[]" as custom_data',
            '(SELECT status FROM wallet WHERE wallet.reference_id = op.order_id AND comm_from="store" AND type LIKE "%sale%" ORDER BY wallet.id ASC  LIMIT 1) as wallet_status',
            '(SELECT commission_status FROM wallet WHERE wallet.reference_id = op.order_id AND comm_from="store" AND type LIKE "%sale%" ORDER BY wallet.id ASC  LIMIT 1) as wallet_commission_status',
            '(SELECT type FROM wallet WHERE wallet.reference_id = op.order_id AND comm_from="store" AND type LIKE "%sale%" ORDER BY wallet.id ASC  LIMIT 1) as wallet_type',
            '(SELECT comm_from FROM wallet WHERE wallet.reference_id = op.order_id AND comm_from="store" AND type LIKE "%sale%" ORDER BY wallet.id ASC  LIMIT 1) as wallet_comm_from',
            '(SELECT reference_id_2 FROM wallet WHERE wallet.reference_id = op.order_id AND comm_from="store" AND type LIKE "%sale%" ORDER BY wallet.id ASC  LIMIT 1) as wallet_reference_id_2',
            '(SELECT comment FROM wallet WHERE wallet.reference_id = op.order_id AND comm_from="store" AND type LIKE "%sale%" ORDER BY wallet.id ASC  LIMIT 1) as wallet_comment',
            '(SELECT is_action FROM wallet WHERE wallet.reference_id = op.order_id AND comm_from="store" AND type LIKE "%sale%" ORDER BY wallet.id ASC  LIMIT 1) as wallet_is_action',
            'o.id',
            'o.status',
            'o.user_id',
            'o.total',
            'o.ip',
            'o.country_code',
            'u.firstname',
            'u.lastname ',
            'o.created_at',    

            '"order_id" as order_id',
            '"product_ids" as product_ids',
            '"currency" as currency',
            '"commission_type" as commission_type',
            '"commission" as commission',
            '"base_url" as base_url',
            '"ads_id" as ads_id',
            '"script_name" as script_name',       

            'o.txn_id',
            'o.address',
            'o.country_id',
            'o.state_id',
            'o.city',
            'o.zip_code',
            'o.phone',
            'o.payment_method',
            'o.shipping_cost',
            'o.tax_cost',
            'o.coupon_discount',
            'o.total_commition',
            'o.shipping_charge',
            'o.currency_code',
            'o.allow_shipping',
            'o.files',
            'o.comment',
            'sum(op.total) AS total_sum',
            '(SELECT paypal_status FROM orders_history WHERE orders_history.order_id = o.id ORDER BY id DESC LIMIT 1) as last_status',
        );

        $union2 = array(
            '"ex" as type',
            'io.custom_data as custom_data',
            '(SELECT status FROM wallet WHERE wallet.reference_id_2 = io.id AND type LIKE "%sale%" ORDER BY wallet.id ASC LIMIT 1 ) as wallet_status',
            '(SELECT commission_status FROM wallet WHERE wallet.reference_id_2 = io.id AND type LIKE "%sale%" ORDER BY wallet.id ASC LIMIT 1) as wallet_commission_status',
            '(SELECT type FROM wallet WHERE wallet.reference_id_2 = io.id AND type LIKE "%sale%" ORDER BY wallet.id ASC LIMIT 1 ) as wallet_type',
            '(SELECT comm_from FROM wallet WHERE wallet.reference_id_2 = io.id AND type LIKE "%sale%" ORDER BY wallet.id ASC LIMIT 1 ) as wallet_comm_from',
            '(SELECT reference_id_2 FROM wallet WHERE wallet.reference_id_2 = io.id AND type LIKE "%sale%" ORDER BY wallet.id ASC LIMIT 1 ) as wallet_reference_id_2',
            '(SELECT comment FROM wallet WHERE wallet.reference_id_2 = io.id AND type LIKE "%sale%" ORDER BY wallet.id ASC LIMIT 1 ) as wallet_comment',
            '(SELECT is_action FROM wallet WHERE wallet.reference_id_2 = io.id AND type LIKE "%sale%" ORDER BY wallet.id ASC LIMIT 1 ) as wallet_is_action',
            'io.id',
            'io.status',
            'io.user_id',
            'io.total',
            'io.ip',
            'io.country_code',
            'u.firstname',
            'u.lastname ',
            'io.created_at',
            'io.order_id',
            'io.product_ids',
            'io.currency',
            'io.commission_type',
            'io.commission',
            'io.base_url',
            'io.ads_id',
            'io.script_name',

            '"txn_id" as txn_id',
            '"address" as address',
            '"country_id" as country_id',
            '"state_id" as state_id',
            '"city" as city',
            '"zip_code" as zip_code',
            '"phone" as phone',
            '"payment_method" as payment_method',
            '"shipping_cost" as shipping_cost',
            '"tax_cost" as tax_cost',
            '"coupon_discount" as coupon_discount',
            '"total_commition" as total_commition',
            '"shipping_charge" as shipping_charge',
            '"currency_code" as currency_code',
            '"allow_shipping" as allow_shipping',
            '"files" as files',
            '"comment" as comment',
            '"total_sum" as total_sum',
            '"last_status" as last_status',
        );

        $select1 = implode(",", $union1);
        $union_query1 = "
        SELECT {$select1} FROM `order` o 
        LEFT JOIN users u ON u.id = o.user_id   
        LEFT JOIN order_products op ON op.order_id = o.id
        WHERE o.status > 0 {$where1} 
        GROUP BY o.id
        ORDER BY o.id DESC
        ";
        
        $select2 = implode(",", $union2);
        $union_query2 = "
        SELECT {$select2} 
        FROM integration_orders io
        LEFT JOIN users u ON u.id = io.user_id  
        WHERE 1 {$where2}
        ";
         

        if(isset($filter['myorder']))
        {
            $union = "
            SELECT 
            SQL_CALC_FOUND_ROWS * 
            FROM (({$union_query1})) as tmp";
        }
        else if(isset($filter['external_orders'])) 
        {
             $union = "
            SELECT 
            SQL_CALC_FOUND_ROWS * 
            FROM (({$union_query2})) as tmp";
        }
        else
        {
            $union = "
            SELECT 
            SQL_CALC_FOUND_ROWS * 
            FROM (({$union_query1}) 
                UNION ALL ({$union_query2})) as tmp";
        }

        $union.= " ORDER BY created_at DESC ";

        if (isset($filter['page'],$filter['limit'])) {
            $offset = (($filter['page']-1) * $filter['limit']);
            $union.= " LIMIT {$offset},". $filter['limit'];
        }
   
        $orders = $this->db->query($union)->result_array();
        $total = $this->db->query("SELECT FOUND_ROWS() AS total")->row()->total;

        $data = array();
        foreach ($orders as $key => $value) {
            if($value['type'] == 'store'){
                $products        = $this->Order_model->getProducts($value['id'],['vendor_or_refer_id' => $filter['user_id']]);
                $payment_history = $this->Order_model->getHistory($value['id']);
                $order_history   = $this->Order_model->getHistory($value['id'], 'order');
                $totals = $this->Order_model->getTotals($products,$value);

                $commission_amount = 0;
                foreach ($products as $key => $p) {
                    $commission_amount += ((float)$p['commission'] + (float)$p['admin_commission']);
                }

                $total_sum = $addShipping ? ($value['total_sum'] + $value['shipping_cost']) : $value['total_sum'];

                $wallet_trans = $this->db->query('SELECT id FROM wallet WHERE type LIKE "%sale%" AND comm_from="store" AND reference_id='.$value['id'])->result_array();
                $trans = [];
                foreach ($wallet_trans as $wa) {
                    $trans[] = $wa['id'];
                }

                $data[] = array(
                    'id'                 => $value['id'],
                    'status'             => $value['status'],
                    'wallet_status'      => $value['wallet_status'],
                    'commission_amount'  => (float)$commission_amount,
                    'wallet_commission_status' => $value['wallet_commission_status'],
                    'wallet_transactions' => implode(', ', $trans),
                    'wallet_type'         => $value['wallet_type'],
                    'wallet_comm_from'    => $value['wallet_comm_from'],
                    'wallet_reference_id_2' => $value['wallet_reference_id_2'],
                    'wallet_comment'    => $value['wallet_comment'],
                    'wallet_is_action' => $value['wallet_is_action'],
                    'type'               => $value['type'],
                    'txn_id'             => $value['txn_id'],
                    'user_id'            => $value['user_id'],
                    'address'            => $value['address'],
                    'country_id'         => $value['country_id'],
                    'state_id'           => $value['state_id'],
                    'city'               => $value['city'],
                    'zip_code'           => $value['zip_code'],
                    'phone'              => $value['phone'],
                    'payment_method'     => $value['payment_method'],
                    'shipping_cost'      => $value['shipping_cost'],
                    'tax_cost'      => $value['tax_cost'],
                    'total'              => $value['total'],
                    'coupon_discount'    => $value['coupon_discount'],
                    'total_commition'    => $value['total_commition'],
                    'shipping_charge'    => $value['shipping_charge'],
                    'currency_code'      => $value['currency_code'],
                    'created_at'         => $value['created_at'],
                    'allow_shipping'     => $value['allow_shipping'],
                    'ip'                 => $value['ip'],
                    'country_code'       => $value['country_code'],
                    'files'              => $value['files'],
                    'comment'            => $value['comment'],
                    'firstname'          => $value['firstname'],
                    'lastname'           => $value['lastname'],
                    'total_sum'          => $total_sum,
                    'last_status'        => $value['last_status'],
                    'custom_data'        => $value['custom_data'],
                    'products'           => $products,
                    'order_history'      => $order_history,
                    'payment_history'    => $payment_history,
                    'totals'             => $totals,
                    'order_country_flag' => '<img style="width: 20px;margin: 0 10px;" src="'. base_url('assets/vertical/assets/images/flags/'. strtolower($value['country_code'])  ) .'.png"> IP: '. $value['ip'],
                );
            } else{
                $wallet_trans = $this->db->query('SELECT id FROM wallet WHERE type LIKE "%sale%" AND comm_from="ex" AND reference_id_2='.$value['id'])->result_array();
                $trans = [];
                foreach ($wallet_trans as $wa) {
                    $trans[] = $wa['id'];
                }
                $data[] = array(
                    'id'              => $value['id'],
                    'status'             => $value['status'],
                    'wallet_status'            => $value['wallet_status'],
                    'wallet_commission_status' => $value['wallet_commission_status'],
                    'wallet_transactions' => implode(', ', $trans),
                    'wallet_type'         => $value['wallet_type'],
                    'wallet_comm_from'    => $value['wallet_comm_from'],
                    'wallet_reference_id_2' => $value['wallet_reference_id_2'],
                    'wallet_comment'    => $value['wallet_comment'],
                    'wallet_is_action' => $value['wallet_is_action'],
                    'type'            => $value['type'],
                    'order_id'        => $value['order_id'],
                    'product_ids'     => $value['product_ids'],
                    'total'           => $value['total'],
                    'currency'        => $value['currency'],
                    'user_id'         => $value['user_id'],
                    'commission_type' => $value['commission_type'],
                    'commission'      => $value['commission'],
                    'ip'              => $value['ip'],
                    'country_code'    => $value['country_code'],
                    'base_url'        => $value['base_url'],
                    'ads_id'          => $value['ads_id'],
                    'script_name'     => $value['script_name'],
                    'custom_data'        => $value['custom_data'],
                    'created_at'      => date("d-m-Y h:i A", strtotime($value['created_at'])),
                    'user_name'       => $value['firstname'] ." " .$value['lastname'],
                    'order_country_flag' =>  '<img style="width: 20px;margin: 0 10px;" src="'. base_url('assets/vertical/assets/images/flags/'. strtolower($value['country_code'])  ) .'.png"> IP: '. $value['ip'],
                );
            }
        }
        
        return array($data,$total);
    }

    public function getCount($filter = array()){
        $this->db->where('o.status > 0');
        if(isset($filter['affiliate_id'])){
            $this->db->join("order_products op",'o.id = op.order_id','left');
            $this->db->where('op.refer_id', (int)$filter['affiliate_id']) ;
        }
        if(isset($filter['user_id'])){
            $this->db->where('o.user_id', (int)$filter['user_id']) ;
        }
        return $this->db->count_all_results('order o');
    }
    public function getSale($filter = array()){
        $this->db->where('o.status > 0');
        $this->db->join("order_products op",'o.id = op.order_id','left');
        if(isset($filter['affiliate_id'])){
            $this->db->where('op.refer_id', (int)$filter['affiliate_id']) ;
        }
        $this->db->select_sum('op.total');
        return (float)$this->db->get('order o')->row_array()['total'];
    }
    public function getSaleChart($filter = array(), $group = 'day'){
        $zero = '';
        $orderBy = ' ORDER BY created_at DESC ';

        if($group == 'month'){
            if(isset($filter['selectedyear'])){
                $current_year = " YEAR(created_at) = ". $filter['selectedyear'];
            }else{
                $current_year = " YEAR(created_at) = ". date("Y");
            }
        } else{
            $current_year .= ' 1=1 ';
        }
        if($group == 'day'){ $groupby = 'CONCAT(DAY(created_at),"-",MONTH(created_at),"-",YEAR(created_at))'; $zero = '2016-1-1'; }
        else if($group == 'week'){ $groupby = 'DATE_FORMAT(created_at, "%b %e")'; $zero = 'Jan 1'; }
        //else if($group == 'month'){ $groupby = 'CONCAT(YEAR(created_at),"-",MONTH(created_at))'; $zero = '2016-1'; }
        else if($group == 'month'){ $groupby = 'MONTH(created_at)'; $zero = '1'; }
        else if($group == 'year'){ $groupby = 'YEAR(created_at)'; $zero = '2016'; }


        $this->db->select(array(
            'sum(commission) as total_commission',
            'sum(total) as total_sale',
            'count(id) as total_order',
            "{$groupby} as groupby"
        ));
        
        if(isset($filter['affiliate_id'])) { $this->db->where('user_id', $filter['affiliate_id']); }
        //$this->db->where('status > 0');
        $this->db->where($current_year);
        $this->db->order_by('created_at','DESC');
        $this->db->group_by($groupby);
        $data = $this->db->get('integration_orders')->result_array();

        $chart = array();
        foreach ($data as $key => $value) {
            $chart[] = array(
                'y' => $value['groupby'],
                'a' => c_format($value['total_sale'], false),
                'b' => (int)$value['total_order'],
                'c' => c_format($value['total_commission'], false),
                'd' => 0,
                'e' => 0,
            );
        }

        $select = array(
            'sum(op.total) as total_sale',
            'count(op.id) as total_order',
            "{$groupby} as groupby"
        );
        if(isset($filter['affiliate_id'])) {
            $select [] = 'sum( IF(op.vendor_id = '. (int)$filter['affiliate_id'].',op.vendor_commission,op.commission) ) as total_commission';
        } else{
            $select [] = 'sum(op.commission + op.vendor_commission) as total_commission';
        }
        $this->db->select($select);


        $this->db->join("order_products op",'op.order_id = order.id','left');
        $this->db->where($current_year);

        if(isset($filter['affiliate_id'])) {
            $this->db->where('( op.vendor_id = '. $filter['affiliate_id'] .' OR  op.refer_id = '. $filter['affiliate_id'] .')');
            $this->db->where('order.status = 1');
        } else{
            $this->db->where('order.status = 1');
        }
        $this->db->order_by('created_at','DESC');
        $this->db->group_by('op.order_id');
        $data = $this->db->get('order')->result_array();
        foreach ($data as $key => $value) {
            $chart[] = array(
                'y' => $value['groupby'],
                'a' => c_format($value['total_sale'], false),
                'b' => (int)$value['total_order'],
                'c' => c_format($value['total_commission'], false),
                'd' => 0,
                'e' => 0,
            );
        }

        
        /*  FOR ACTION */
        $_where = '';
        if(isset($filter['affiliate_id'])) {
            $_where .=  ' user_id = '. (int)$filter['affiliate_id'] ." AND ";
        }

        $integration_click_amount = $this->db->query('SELECT '. $groupby . ' as groupby,count(amount) as total,SUM(amount) as total_count FROM `wallet` WHERE is_action=1 AND type="external_click_commission" AND '. $_where . $current_year .' AND comm_from = "ex"  AND status > 0 GROUP BY '. $groupby .'   '. $orderBy )->result_array();

        foreach ($integration_click_amount as $value) {
            $chart[] = array(
                'y' => $value['groupby'],
                'a' => 0,
                'b' => 0,
                'c' => 0,
                'd' => c_format($value['total'], false),
                'e' => $value['total_count'],
            );
        }


        $data = array();
        $sale = array();
        $order = array();
        $commissions = array();
        $action = array();
        $actioncommissions = array();
        $keys = array();
        if($group == 'month'){
            for ($i=1; $i <= 12; $i++) { 
                $keys[] = $i;
                $sale[$i] = $order[$i] = $commissions[$i] = $action[$i] = $actioncommissions[$i] = 0;
            }
        }
        
        foreach ($chart as $key => $value) {
            $tmp                            = $data[$value['y']];
            $sale[$value['y']]              += c_format((float)$value['a'] + (float)$tmp['a'], false);
            $order[$value['y']]             += (int)((float)$value['b'] + (float)$tmp['b']);
            $commissions[$value['y']]       += c_format((float)$value['c'] + (float)$tmp['c'], false);
            $action[$value['y']]            += c_format((float)$value['d'] + (float)$tmp['d'], false);
            $actioncommissions[$value['y']] += c_format((float)$value['e'] + (float)$tmp['e'], false);
            $data['y'][$value['y']]         = $value['y'];

            if($group != 'month' && !in_array($keys, $value['y'])){
                $keys[] = (string)$value['y'];
            }
        }
        function setup_tooltip($arr,$meta)
        {   
            return array("meta"=> $meta, "value" => $arr);
        } 

        if($group == 'day' || $group == 'week'){
            function date_sort($a, $b) {
                return strtotime($a) - strtotime($b);
            }
            
            usort($keys, "date_sort");
        }
        else if($group == 'year'){
            sort($keys);
        }

        $data['series_new'] = array(
            'keys'              => array_unique($keys),
            'sale'              => array('sale') + $sale,
            'order'             => array('order') + $order,
            'commissions'       => array('commissions') + $commissions,
            'action'            => array('action') + $action,
            'actioncommissions' => array('actioncommissions') + $actioncommissions,
        ); 

        
        return $data;
    }

    public function ___getSaleChart($filter = array(), $group = 'day'){
        $zero = '';
        $orderBy = ' ORDER BY created_at DESC ';

        if($group == 'day'){ $groupby = 'CONCAT(YEAR(created_at),"-",MONTH(created_at),"-",DAY(created_at))'; $zero = '2016-1-1'; }
        else if($group == 'week'){ $groupby = 'DATE_FORMAT(created_at, "%b %e")'; $zero = 'Jan 1'; }
        else if($group == 'month'){ $groupby = 'CONCAT(YEAR(created_at),"-",MONTH(created_at))'; $zero = '2016-1'; }
        else if($group == 'year'){ $groupby = 'YEAR(created_at)'; $zero = '2016'; }

        $this->db->select(array(
            'sum(commission) as total_commission',
            'sum(total) as total_sale',
            'count(id) as total_order',
            "{$groupby} as groupby"
        ));
        
        if(isset($filter['affiliate_id'])) {
            $this->db->where('user_id', $filter['affiliate_id']);
        }
        $this->db->where('status > 0');
        $this->db->order_by('created_at','DESC');
        $data = $this->db->get('integration_orders')->result_array();
        $chart = array();
        $chart[] = array(
            'y' => $zero,
            'a' => c_format(0, false),
            'b' => 0,
            'c' => c_format(0, false),
            'd' => 0,
            'e' => 0,
        );
        foreach ($data as $key => $value) {
            $chart[] = array(
                'y' => $value['groupby'],
                'a' => c_format($value['total_sale'], false),
                'b' => (int)$value['total_order'],
                'c' => c_format($value['total_commission'], false),
                'd' => 0,
                'e' => 0,
            );
        }

        $this->db->select(array(
            'sum(op.commission) as total_commission',
            'sum(order.total) as total_sale',
            'count(op.id) as total_order',
            "{$groupby} as groupby"
        ));
        $this->db->join("order_products op",'op.order_id = order.id','left');
        $this->db->where('order.status > 0');
        if(isset($filter['affiliate_id'])) {
            $this->db->where('op.refer_id', $filter['affiliate_id']);
        }
        $this->db->order_by('created_at','DESC');
        $this->db->group_by('op.order_id');
        $data = $this->db->get('order')->result_array();

        


        foreach ($data as $key => $value) {
            $chart[] = array(
                'y' => $value['groupby'],
                'a' => c_format($value['total_sale'], false),
                'b' => (int)$value['total_order'],
                'c' => c_format($value['total_commission'], false),
                'd' => 0,
                'e' => 0,
            );
        }


        $_where = '';
        if(isset($filter['affiliate_id'])) {
            $_where .= ' user_id = '. (int)$filter['affiliate_id'] ." AND ";
        }


        /*$integration_action_count  = $this->db->query('SELECT '. $groupby . ' as groupby,count(*) as total FROM `integration_clicks_action` WHERE '. $_where .' is_action=1 GROUP BY '. $groupby )->result_array();*/
        $integration_click_amount = $this->db->query('SELECT '. $groupby . ' as groupby,count(amount) as total FROM `wallet` WHERE is_action=1 AND type="external_click_commission" AND '. $_where .' comm_from = "ex"   GROUP BY domain_name '. $orderBy .'')->result_array();

        foreach ($integration_click_amount as $value) {
            $chart[] = array(
                'y' => $value['groupby'],
                'a' => 0,
                'b' => 0,
                'c' => 0,
                'd' => c_format($value['total'], false),
                'e' => 0,
            );
        }

        $integration_click_amount = $this->db->query('SELECT '. $groupby . ' as groupby,SUM(amount) as total FROM `wallet` WHERE is_action=1 AND type="external_click_commission" AND '. $_where .' comm_from = "ex"   GROUP BY domain_name '. $orderBy .'')->result_array();
        foreach ($integration_click_amount as $value) {
            $chart[] = array(
                'y' => $value['groupby'],
                'a' => 0,
                'b' => 0,
                'c' => 0,
                'd' => 0,
                'e' => c_format($value['total'], false),
            );
        }



        $data = array();
        foreach ($chart as $key => $value) {
            $tmp = $data[$value['y']];

            $data[$value['y']] = array(
                'y' => $value['y'],
                'a' => c_format($value['a'] + $tmp['a'], false),
                'b' => (int)($value['b'] + $tmp['b']),
                'c' => c_format($value['c'] + $tmp['c'], false),
                'd' => c_format($value['d'] + $tmp['d'], false),
                'e' => c_format($value['e'] + $tmp['e'], false),
            );
        }

        return array_values($data);
        return array_reverse(array_values($data));
        return $chart;
    }
    
     public function getOrder($order_id, $control = 'admincontrol',$filternew=null){
        $where = '';
        if(isset($filter['affiliate_id'])){
            $where .= " AND op.refer_id = ". (int)$filter['affiliate_id'];
        }

        if(isset($filternew["user_id"]))
        {
            $where .= " AND  o.user_id = ". (int)$filternew['user_id'];
        }

        $order = $this->db->query("
            SELECT o.*,u.firstname,u.lastname,u.email,c.name as country_name,s.name as state_name,cc.name as order_country,u.ucity as client_city,u.uzip as client_zipcode,u.phone as client_phone,u.twaddress as client_twaddress,u.address1 as client_addres1,u.address2 as client_addres2,
            (SELECT paypal_status FROM orders_history WHERE orders_history.order_id = o.id ORDER BY id DESC LIMIT 1) as last_status,
            (SELECT form_id FROM order_products WHERE order_products.order_id = o.id ORDER BY form_id DESC LIMIT 1) as form_id,
            (SELECT name FROM countries WHERE countries.id = u.ucountry LIMIT 1) as client_country,
            (SELECT name FROM states WHERE states.id = u.state LIMIT 1) as client_state
            FROM `order` o 
            LEFT JOIN users u ON u.id = o.user_id   
            LEFT JOIN order_products op ON op.order_id = o.id
            LEFT JOIN countries c ON c.id = o.country_id
            LEFT JOIN states s ON s.id = o.state_id
            LEFT JOIN countries cc ON cc.sortname = o.country_code
            WHERE o.id= ". (int)$order_id ." {$where} 
            ")->row_array();
        

        $order['files'] = json_decode($order['files'], true);
        if($order['files']){
            $html = '';
            foreach ($order['files'] as $v) {
                $html .= '<a target="_blank" href="'. base_url($control."/order_attechment/". $v['name'] ."/". $v['mask'] ) .'">'. $v['mask'] .'</a><br>';
            }

            $order['files'] = $html;
        }

        $order['order_country_flag'] = '<img style="width: 20px;margin: 0 10px;" src="'. base_url('assets/vertical/assets/images/flags/'. strtolower($order['country_code'])  ) .'.png"> IP: '. $order['ip'] ;
        if($order['comment']){
            $order['comment'] = json_decode($order['comment'], true);
        }else{
            $order['comment'] = array();
        }
        return $order;
    }

    public function getTotals($products, $order){
        $totals = array();
        $total = 0;
        $discount = 0;
        foreach ($products as $key => $value) {
            $total += ($value['total']);
            $discount += ($value['coupon_discount']);
        }
        $totals['total'] = array("text" => __('admin.sub_total'), 'value' => $total);
        if($discount > 0){
            $totals['discount_total'] = array("text" => __('admin.discount'), 'value' => $discount);
        }
        if($order['coupon_discount'] > 0){
            $totals['discount_total'] = array("text" => __('admin.coupon_discount'), 'value' => $order['coupon_discount']);
            $total -= $order['coupon_discount'];
        }
        if($order['shipping_cost'] > 0){
            $totals['shipping_cost'] = array("text" =>__('admin.shipping_cost'), 'value' => $order['shipping_cost']);
            $total += $order['shipping_cost'];
        }

        if($order['tax_cost'] > 0){
            $totals['tax_cost'] = array("text" => __('admin.tax'), 'value' => $order['tax_cost']);
            $total += $order['tax_cost'];
        }

        $totals['grand_total'] = array("text" => __('admin.grand_total'), 'value' => $total);


        return $totals;
    }
    public function getProducts($order_id,$filter = array(),$select="",$user_id=null){
        $order_id= (int)$order_id;
        $where = '';
        if(isset($filter['refer_id'])) { 
            $where .= " AND op.refer_id =  ". $filter['refer_id']; 
        }
        if(isset($filter['product_id'])) { 
            $where .= " AND op.product_id =  ". $filter['product_id']; 
        }
        if(isset($filter['vendor_or_refer_id'])) { 
            $where .= " AND (op.vendor_id = ". $filter['vendor_or_refer_id'] ." OR op.refer_id =  ". $filter['vendor_or_refer_id'] .")"; 
        }
        
        $isRatting = ",(SELECT rating_number FROM rating WHERE products_id= op.product_id  LIMIT 1) as ratting";
        
        $cart_products = $this->db->query("SELECT 
            op.*,p.product_name,p.product_variations,p.product_featured_image,p.product_type,p.product_created_by,p.downloadable_files,CONCAT(u.firstname,' ',u.lastname) as refer_name,u.email as refer_email $isRatting $select
            FROM order_products op 
            LEFT JOIN product as p ON p.product_id = op.product_id 
            LEFT JOIN users as u ON u.id = op.refer_id 
            WHERE op.order_id = {$order_id}  {$where} ")->result_array();
        $products  = array();
        $this->load->model('Product_model');
        foreach ($cart_products as $key => $product) {
            $product_featured_image = base_url('assets/images/product/upload/thumb/'. $product['product_featured_image']);
            $temp_product = array(
                'id'                     => $product['id'],
                'order_id'               => $product['order_id'],
                'product_id'             => $product['product_id'],
                'refer_id'               => $product['refer_id'],
                'form_id'                => $product['form_id'],
                'product_type'           => $product['product_type'],
                'downloadable_files'     => $this->Product_model->parseDownloads($product['downloadable_files'],$product['product_type']),
                'price'                  => $product['price'],
                'msrp'                   => $product['msrp'],
                'quantity'               => $product['quantity'],
                'commission'             => $product['commission'],
                'commission_type'        => $product['commission_type'],
                'coupon_code'            => $product['coupon_code'],
                'coupon_name'            => $product['coupon_name'],
                'total'                  => $product['total'],
                'coupon_discount'        => $product['coupon_discount'],
                'product_name'           => $product['product_name'],
                'refer_email'            => $product['refer_email'],
                'refer_name'             => $product['refer_name'],
                'admin_commission'       => $product['admin_commission'],
                'admin_commission_type'  => $product['admin_commission_type'],
                'vendor_commission'      => $product['vendor_commission'],
                'vendor_commission_type' => $product['vendor_commission_type'],
                'vendor_id'              => (int)$product['vendor_id'],
                'image'                  => $product_featured_image,
                'variation'              => $product['variation'],
                'product_variations'     => $product['product_variations'],
                'product_description'    => $product['product_description']??null,
                'product_slug'           => $product['product_slug']??null,
                'product_ratting'        => $product['ratting']??0,
                'product_avg_rating'     => $product['product_avg_rating'],
                'product_created_by'     => $product['product_created_by']
            );
            $temp_product['variation_price'] = $this->getSelectedVariationPrice($temp_product);
            $products[] = $temp_product;
        }
        return $products;
    }
    public function getSelectedVariationPrice($product){
      $selected_var = json_decode($product['variation']);
      $product_var = json_decode($product['product_variations']);
      $price = 0;
      foreach($selected_var as $var_key => $var_value) {
         $searchVal = ($var_key == 'colors') ? explode('-', $var_value)[1] : $var_value;
         foreach($product_var->{$var_key} as $p_var) {
            if($p_var->name == $searchVal) {
               $price += $p_var->$price;
               break;
           }
       }
   }
   return $price;
}
public function getAffiliateUser($order_id){
    $this->db->select('users.*,order_products.commission,order_products.commission_type,product.product_name,product.product_short_description');
    $this->db->where('order_products.order_id', $order_id);
    $this->db->join('users', 'users.id = order_products.refer_id');
    $this->db->join('product', 'product.product_id = order_products.product_id');
    return $this->db->get_where('order_products')->result_array();
}
public function getVender($order_info = array(), $product_info = array()){
    $p_ids = array_column($product_info, 'product_id');
    $this->db->select('order_products.product_id, order_products.vendor_commission, users.*');
    $this->db->join('users', 'users.id = order_products.vendor_id');
    $this->db->where('order_products.order_id', $order_info['id']);
    $this->db->where_in('order_products.product_id', $p_ids);
    $vendors = $this->db->get_where('order_products')->result_array();

    $return_vendors = array();
    foreach ($vendors as $vendor){
        $return_vendors[$vendor['product_id']] = array(
            'id'                => $vendor['id'],
            'product_id'        => $vendor['product_id'],
            'firstname'         => $vendor['firstname'],
            'lastname'          => $vendor['lastname'],
            'email'             => $vendor['email'],
            'username'          => $vendor['username'],
            'vendor_commission' => $vendor['vendor_commission'],
        );
    }
    return $return_vendors;
}
public function getHistory($order_id, $type = 'payment'){
    $this->db->where('order_id', $order_id);
    $this->db->where('history_type', $type);
    $this->db->order_by('created_at', 'DESC');
    return $this->db->get('orders_history')->result_array();
}

public function getPaymentProof($order_id){
    $this->db->where('order_id', $order_id);
    $orderProof =  $this->db->get('order_proof')->row();
    if($orderProof){
        $orderProof->downloadLink = base_url('assets/user_upload/'. $orderProof->proof);
    }

    return $orderProof;
}

public function getOrders($filter = array(), $addShipping = true){
    $where = '';
    if(isset($filter['user_id'])){
        $where .= " AND o.user_id = ". (int)$filter['user_id'];
    }
    if(isset($filter['affiliate_id'])){
        $where .= " AND op.refer_id = ". (int)$filter['affiliate_id'];
    }
    if(isset($filter['vendor_id'])){
        $where .= " AND op.vendor_id = ". (int)$filter['vendor_id'];
    }

    $limit = '';
    if(isset($filter['limit']) && (int)$filter['limit'] > 0){
        $limit = " LIMIT ". (int)$filter['limit'];
    }

    if(isset($filter['page'])){
        $offset = (int)$filter['limit'] * ((int)$filter['page'] - 1);
        $limit = " LIMIT ". $offset ." ,". (int)$filter['limit'];
    }

    $query = "
    SELECT 
    o.*,
    u.firstname,
    u.id as user_id,
    u.lastname,
    u.username,
    u.type as user_type,
    sum(op.total) AS total_sum,
    (SELECT paypal_status FROM orders_history WHERE orders_history.order_id = o.id ORDER BY id DESC LIMIT 1) as last_status,
    (SELECT status FROM wallet WHERE wallet.reference_id_2 = op.order_id AND comm_from = 'store' AND type LIKE '%sale%'ORDER BY wallet.id DESC  LIMIT 1) as wallet_status,
    (SELECT commission_status FROM wallet WHERE wallet.reference_id_2 = op.order_id AND comm_from = 'store' AND type LIKE 
        '%sale%' ORDER BY wallet.id ASC  LIMIT 1) as wallet_commission_status
    FROM `order` o 
    LEFT JOIN users u ON u.id = o.user_id   
    LEFT JOIN order_products op ON op.order_id = o.id
    WHERE o.status > 0 {$where} 
    GROUP BY o.id
    ORDER BY o.id DESC
    ". $limit;

    if(!isset($filter['page'])){
        $orders = $this->db->query($query)->result_array();
        
        $data = array();
        foreach ($orders as $key => $value) {
            $products        = $this->Order_model->getProducts($value['id']);
            
            //get total levels commission from vendor store product
            $totalVendorStoreLevels= $this->Order_model->getVendorStoreLevels($products);
           
            $commission_amount = 0;
            foreach($products as $pkey => $pvalue)
                $commission_amount += ((float)$pvalue['commission'] + (float)$pvalue['admin_commission'] + (float)$totalVendorStoreLevels);
            
            $total_sum = $addShipping ? ($value['total_sum'] + $value['shipping_cost'] + $value['tax_cost']) : $value['total_sum'];

            $data[] = array(
                'id'                 => $value['id'],
                'status'             => $value['status'],
                'wallet_status'      => $value['wallet_status'],
                'commission_amount'  => (float)$commission_amount,
                'wallet_commission_status' => $value['wallet_commission_status'],
                'txn_id'             => $value['txn_id'],
                'user_id'            => $value['user_id'],
                'address'            => $value['address'],
                'country_id'         => $value['country_id'],
                'state_id'           => $value['state_id'],
                'city'               => $value['city'],
                'zip_code'           => $value['zip_code'],
                'phone'              => $value['phone'],
                'payment_method'     => $value['payment_method'],
                'shipping_cost'      => $value['shipping_cost'],
                'tax_cost'      => $value['tax_cost'],
                'total'              => $total,
                'coupon_discount'    => $value['coupon_discount'],
                'total_commition'    => $value['total_commition'],
                'shipping_charge'    => $value['shipping_charge'],
                'currency_code'      => $value['currency_code'],
                'created_at'         => $value['created_at'],
                'allow_shipping'     => $value['allow_shipping'],
                'ip'                 => $value['ip'],
                'country_code'       => $value['country_code'],
                'files'              => $value['files'],
                'comment'            => $value['comment'],
                'firstname'          => $value['firstname'],
                'lastname'           => $value['lastname'],
                'username'           => $value['username'],
                'user_type'           => $value['user_type'],
                'total_sum'          => $total_sum,
                'last_status'        => $value['last_status'],
                'order_country_flag' =>  '<img style="width: 20px;margin: 0 10px;" src="'. base_url('assets/vertical/assets/images/flags/'. strtolower($value['country_code'])  ) .'.png"> IP: '. $value['ip'],
            );
        }

        return $data;
    } else{
        $json['data'] =  $this->db->query($query)->result_array();
        $query = "SELECT COUNT(o.id) as total 
        FROM `order` o 
        LEFT JOIN users u ON u.id = o.user_id   
        LEFT JOIN order_products op ON op.order_id = o.id
        WHERE o.status > 0 {$where} 
        ORDER BY o.id DESC";

        $json['total'] = $this->db->query($query)->row()->total;
        return $json;
    }
}
public function getUserdetail($product_created_by){
    $this->db->where('id',$product_created_by);
    return $this->db->get('users')->row_array();
}




public function getDashboardOrders($filter = array(), $addShipping = true){
    $where = '';
    if(isset($filter['user_id'])){
        $where .= " AND o.user_id = ". (int)$filter['user_id'];
    }
    if(isset($filter['affiliate_id'])){
        $where .= " AND op.refer_id = ". (int)$filter['affiliate_id'];
    }
    if(isset($filter['vendor_id'])){
        $where .= " AND op.vendor_id = ". (int)$filter['vendor_id'];
    }

    $limit = '';
    if(isset($filter['limit']) && (int)$filter['limit'] > 0){
        $limit = " LIMIT ". (int)$filter['limit'];
    }

    if(isset($filter['id_gt'])){
        $where .= " AND o.id > ". (int)$filter['id_gt'];
    }

    if(isset($filter['page'])){
        $offset = (int)$filter['limit'] * ((int)$filter['page'] - 1);
        $limit = " LIMIT ". $offset ." ,". (int)$filter['limit'];
    }

    $query = "
    SELECT 
    o.*,
    u.firstname,
    u.id as user_id,
    u.lastname,
    u.username,
    u.type as user_type,
    sum(op.total) AS total_sum,
    (SELECT paypal_status FROM orders_history WHERE orders_history.order_id = o.id ORDER BY id DESC LIMIT 1) as last_status,
    (SELECT status FROM wallet WHERE wallet.reference_id = op.order_id AND comm_from = 'store' AND type LIKE '%sale%'ORDER BY wallet.id ASC  LIMIT 1) as wallet_status,
    (SELECT commission_status FROM wallet WHERE wallet.reference_id = op.order_id AND comm_from = 'store' AND type LIKE 
        '%sale%' ORDER BY wallet.id ASC  LIMIT 1) as wallet_commission_status
    FROM `order` o 
    LEFT JOIN users u ON u.id = o.user_id   
    LEFT JOIN order_products op ON op.order_id = o.id
    WHERE o.status > 0 {$where} 
    GROUP BY o.id
    ORDER BY o.id DESC
    ". $limit;

    if(!isset($filter['page'])){
        $orders = $this->db->query($query)->result_array();
        $data = array();
        foreach ($orders as $key => $value) {
            $products        = $this->Order_model->getProducts($value['id']);
            $commission_amount = 0;
            foreach($products as $pkey => $pvalue)
                $commission_amount += ((float)$pvalue['commission'] + (float)$pvalue['admin_commission']);
            
            $total_sum = $addShipping ? ($value['total_sum'] + $value['shipping_cost'] + $value['tax_cost']) : $value['total_sum'];

            $data[] = array(
                'id'                 => $value['id'],
                'status'             => $value['status'],
                'wallet_status'      => $value['wallet_status'],
                'commission_amount'  => (float)$commission_amount,
                'wallet_commission_status' => $value['wallet_commission_status'],
                'txn_id'             => $value['txn_id'],
                'user_id'            => $value['user_id'],
                'address'            => $value['address'],
                'country_id'         => $value['country_id'],
                'state_id'           => $value['state_id'],
                'city'               => $value['city'],
                'zip_code'           => $value['zip_code'],
                'phone'              => $value['phone'],
                'payment_method'     => $value['payment_method'],
                'shipping_cost'      => $value['shipping_cost'],
                'tax_cost'      => $value['tax_cost'],
                'total'              => $total,
                'coupon_discount'    => $value['coupon_discount'],
                'total_commition'    => $value['total_commition'],
                'shipping_charge'    => $value['shipping_charge'],
                'currency_code'      => $value['currency_code'],
                'created_at'         => $value['created_at'],
                'allow_shipping'     => $value['allow_shipping'],
                'ip'                 => $value['ip'],
                'country_code'       => $value['country_code'],
                'files'              => $value['files'],
                'comment'            => $value['comment'],
                'firstname'          => $value['firstname'],
                'lastname'           => $value['lastname'],
                'username'           => $value['username'],
                'user_type'           => $value['user_type'],
                'total_sum'          => $total_sum,
                'last_status'        => $value['last_status'],
                'order_country_flag' =>  '<img style="width: 20px;margin: 0 10px;" src="'. base_url('assets/vertical/assets/images/flags/'. strtolower($value['country_code'])  ) .'.png"> IP: '. $value['ip'],
            );
        }

        return $data;
    } else{
        $json['data'] =  $this->db->query($query)->result_array();
        $query = "SELECT COUNT(o.id) as total 
        FROM `order` o 
        LEFT JOIN users u ON u.id = o.user_id   
        LEFT JOIN order_products op ON op.order_id = o.id
        WHERE o.status > 0 {$where} 
        ORDER BY o.id DESC";

        $json['total'] = $this->db->query($query)->row()->total;
        return $json;
    }
}

 

public function orderdelete($order_id, $transaction){
    if((int)$transaction > 0){
        $this->db->query("DELETE FROM wallet WHERE reference_id = {$order_id} AND type IN ('sale_commission','vendor_sale_commission','refer_sale_commission') ");
    }
    
    $this->db->query("DELETE FROM `order` WHERE id = {$order_id} ");

    $this->db->query("DELETE FROM orders_history WHERE order_id = {$order_id} ");
    
    $this->db->query("DELETE FROM order_products WHERE order_id = {$order_id} ");
}

    public function getShippingDetails($user_id){
        return $this->db->query("SELECT shipping_address.*, states.name as state, countries.name as country FROM shipping_address 
            LEFT JOIN states ON states.id = shipping_address.state_id  
            LEFT JOIN countries ON countries.id = shipping_address.country_id
            WHERE shipping_address.user_id =  ". $user_id
        )->row();
    }


    public function getOrderDetails($order_id)
    {
        $sql='SELECT "ex" as type,io.custom_data as custom_data,(SELECT status FROM wallet WHERE wallet.reference_id_2 = io.id AND type LIKE "%sale%" ORDER BY wallet.id ASC LIMIT 1 ) as wallet_status,(SELECT commission_status FROM wallet WHERE wallet.reference_id_2 = io.id AND type LIKE "%sale%" ORDER BY wallet.id ASC LIMIT 1) as wallet_commission_status,(SELECT type FROM wallet WHERE wallet.reference_id_2 = io.id AND type LIKE "%sale%" ORDER BY wallet.id ASC LIMIT 1 ) as wallet_type,(SELECT comm_from FROM wallet WHERE wallet.reference_id_2 = io.id AND type LIKE "%sale%" ORDER BY wallet.id ASC LIMIT 1 ) as wallet_comm_from,(SELECT reference_id_2 FROM wallet WHERE wallet.reference_id_2 = io.id AND type LIKE "%sale%" ORDER BY wallet.id ASC LIMIT 1 ) as wallet_reference_id_2,(SELECT comment FROM wallet WHERE wallet.reference_id_2 = io.id AND type LIKE "%sale%" ORDER BY wallet.id ASC LIMIT 1 ) as wallet_comment,(SELECT is_action FROM wallet WHERE wallet.reference_id_2 = io.id AND type LIKE "%sale%" ORDER BY wallet.id ASC LIMIT 1 ) as wallet_is_action,io.id,io.status,io.user_id,io.total,io.ip,io.country_code,u.firstname,u.lastname ,io.created_at,io.order_id,io.product_ids,io.currency,io.commission_type,io.commission,io.base_url,io.ads_id,io.script_name,"txn_id" as txn_id,"address" as address,"country_id" as country_id,"state_id" as state_id,"city" as city,"zip_code" as zip_code,"phone" as phone,"payment_method" as payment_method,"shipping_cost" as shipping_cost,"tax_cost" as tax_cost,"coupon_discount" as coupon_discount,"total_commition" as total_commition,"shipping_charge" as shipping_charge,"currency_code" as currency_code,"allow_shipping" as allow_shipping,"files" as files,"comment" as comment,"total_sum" as total_sum,"last_status" as last_status 
        FROM integration_orders io
        LEFT JOIN users u ON u.id = io.user_id  
        WHERE io.id='.$order_id;

     
       return  $this->db->query($sql)->row_array();

    }

    public function getClickActionDetails($click_id)
    {
        $sql='SELECT "ex" as type,u.firstname,u.lastname ,ic.user_id,ic.created_at,ic.country_code,ic.ip,ic.id,ic.base_url,ic.link,ic.agent,ic.browserName,ic.browserVersion,ic.systemString,ic.osPlatform,ic.osVersion,ic.osShortVersion,ic.isMobile,ic.mobileName,ic.osArch,ic.isIntel,ic.isAMD,ic.isPPC,ic.click_id,ic.click_type,ic.custom_data,"action_id" as action_id,"action_type" as action_type,"product_id" as product_id,"viewer_id" as viewer_id,"counter" as counter,"pay_commition" as pay_commition,"status" as status,"txn_id" as txn_id,"address" as address,"country_id" as country_id,"state_id" as state_id,"city" as city,"zip_code" as zip_code,"phone" as phone,"payment_method" as payment_method,"shipping_cost" as shipping_cost,"total" as total,"coupon_discount" as coupon_discount,"total_commition" as total_commition,"shipping_charge" as shipping_charge,"currency_code" as currency_code,"allow_shipping" as allow_shipping,"files" as files,"comment" as comment FROM `integration_clicks_logs` ic 
        LEFT JOIN users u ON u.id = ic.user_id
        WHERE ic.click_id='.$click_id;

        return  $this->db->query($sql)->row_array();
    }


    // new section of all commissions for levels that related to vendor side

    //fucntion 1 - get total levels commission from vendor store product
    public function getVendorStoreLevels($products){
        
        $product_created_by = $products[0]['product_created_by'];
        $user_id = $products[0]['refer_id'];/// Afiliate ID
        $userDetail=$this->Order_model->getUserdetail($product_created_by);

        $totalVendorStoreLevels=0;

        $level = $this->Product_model->getMyLevel($user_id, $setting);

        $setting = $this->Product_model->getVendorSettings($userDetail['id'], 'referlevel');

        $max_level = isset($setting['levels']) ? (int)$setting['levels'] : 3;
        $getSettingsFor = [];
        for ($l=1; $l <= $max_level ; $l++) {
            $getSettingsFor[] = 'referlevel_'. $l;
        }
        $referlevelSettings = $this->Product_model->getVendorSettingsWhereIn($userDetail['id'], $getSettingsFor);
       
        for ($l=1; $l <= $max_level ; $l++) {
            $levelUser = (int)$level['level'. $l];
            $s = $referlevelSettings['referlevel_'. $l];
            if ($products[0]["product_created_by"] == $levelUser) {
                continue;
            }
            
            if(isset($referlevelSettings['referlevel_'. $l]) && $levelUser > 0){
              
                if($setting['sale_type'] == 'percentage')
                {
                    $total=$products[0]["total"];
                   

                    $_giveAmount = (($total * (float)$s['sale_commition']) / 100);
                } else{
                    $_giveAmount = (float)$s['sale_commition'];
                }

                $totalVendorStoreLevels=$totalVendorStoreLevels + $_giveAmount;
            }
        }
        return $totalVendorStoreLevels;
    }

    // new section of all commissions for levels that related to vendor side      
}