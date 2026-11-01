<?php

class MarketTools {

    public $data;

    function __construct($data) {
        $this->data = $data;
    }


	public function list()
	{
		extract($this->data);

		$response = [];

 		foreach($data_list as $index => $product) {
 			$response[] = $this->prepare($product);
		}

		return $response;
	}
    

    public function prepare($product)
    {
        
        extract($this->data);

        $newProduct = [];
            
        if(isset($product['is_form'])) {

            $newProduct['aff_tool_type'] = 'form';

            $newProduct['fevi_icon'] = $product['fevi_icon'];

            $newProduct['title'] = $product['title'];


            if($product['slug']) {
                $newProduct['share_url'] = base_url($product['slug']);
            }else{
                $newProduct['share_url'] = $product['public_page'];
            }

            $newProduct['public_page'] = $product['public_page'];

            if($product['sale_commision_type'] == 'default'){
                $commissionType = $form_default_commission['product_commission_type'];
                if($form_default_commission['product_commission_type'] == 'percentage'){
                    if($award_level_status == 1 && $userComission['status'] && $userComission['value'] && $userComission['value'] < $form_default_commission['product_commission'])
                        $product_commission = $userComission['value'];
                    else
                        $product_commission = $form_default_commission['product_commission'];

                    $newProduct['sale_commision_you_will_get'] = $product_commission .'% '.__('user.per_sale');
                }
                else if($form_default_commission['product_commission_type'] == 'Fixed'){
                    $newProduct['sale_commision_you_will_get'] = c_format($form_default_commission['product_commission']) .' '.__('user.per_sale');
                }
            } else if($product['sale_commision_type'] == 'percentage'){
                if($award_level_status == 1 && $userComission['status'] && $userComission['value'] && $userComission['value'] < $product['sale_commision_value'])
                    $sale_commision_value = $userComission['value'];
                else
                    $sale_commision_value = $product['sale_commision_value'];

                $newProduct['sale_commision_you_will_get'] = $sale_commision_value .'% '.__('user.per_sale');
            } else if($product['sale_commision_type'] == 'fixed'){
                $newProduct['sale_commision_you_will_get'] = c_format($product['sale_commision_value']) .' '.__('user.per_sale');
            }


            if($product['click_commision_type'] == 'default'){
                if((int)$product['vendor_id']){
                    $vendor_setting = $Product_model->getVendorSettingById((int)$product['vendor_id']);
                    $newProduct['click_commision_you_will_get'] = c_format($vendor_setting->form_affiliate_click_amount) .' '.__('user.of_per').' '. (int)$vendor_setting->form_affiliate_click_count .' '.__('user.click');
                } else {
                    $commissionType = $form_default_commission['product_commission_type'];
                    if($form_default_commission['product_commission_type'] == 'percentage'){
                        $newProduct['click_commision_you_will_get'] = c_format($form_default_commission['product_ppc']) .' '.__('user.of_per').' '. $form_default_commission['product_noofpercommission'] .' '.__('user.click');
                    }
                    else if($form_default_commission['product_commission_type'] == 'Fixed'){
                        $newProduct['click_commision_you_will_get'] = c_format($form_default_commission['product_ppc']) .' '.__('user.of_per').' '. $form_default_commission['product_noofpercommission'] .' '.__('user.click');
                    }
                }
            } else if($product['click_commision_type'] == 'custom') {
                $newProduct['click_commision_you_will_get'] = c_format($product['click_commision_per']) .' '.__('user.of_per').' '. $product['click_commision_ppc'] .' '.__('user.click');
            }

            if($product['form_recursion_type']){
                if($product['form_recursion_type'] == 'custom'){
                    if($product['form_recursion'] != 'custom_time'){
                        $newProduct['recurring'] =  __('user.'. $product['form_recursion']);
                    } else {
                        $newProduct['recurring'] = timetosting($product['recursion_custom_time']);
                    }
                } else{
                    if($form_setting['form_recursion'] == 'custom_time' ){
                        $newProduct['recurring'] = timetosting($form_setting['recursion_custom_time']);
                    } else {
                        $newProduct['recurring'] = __('user.'. $form_setting['form_recursion']);
                    }
                }
            }

            $newProduct['description'] = $product['description'];

            $newProduct['coupon_code'] = $product['coupon_code'] ? $product['coupon_code'] : __('user.n_a');

            $newProduct['coupon_use'] = ($product['coupon_name'] ? $product['coupon_name'] : '-').' / '.$product['count_coupon'];

            $newProduct['sales_commission'] = (int)$product['count_commission'].' / '.c_format($product['total_commission']);

            $newProduct['clicks_commission'] = (int)$product['commition_click_count'].' / '.c_format($product['commition_click']);

            $newProduct['total_commission'] = c_format($product['total_commission']+$product['commition_click']);

            // end of is form
        } else if(isset($product['is_product'])) {

            $newProduct['is_campaign_product'] = (bool)$product['is_campaign_product'];

            if($product['is_campaign_product']) {
                $newProduct['aff_tool_type'] = 'campaign_product';
                $af_id = _encrypt_decrypt($userdetails['id']."-".$product['product_id']);
                $newProduct['public_page'] = addParams($product['product_url'],"af_id",$af_id);
            } else {
                $newProduct['aff_tool_type'] = 'store_product';
                $newProduct['public_page'] = base_url('store/'. base64_encode($userdetails['id']) .'/product/'.$product['product_slug'] );
            }

            if($product['slug']) {
                $newProduct['share_url'] = base_url($product['slug']);
            }else{
                $newProduct['share_url'] = $newProduct['public_page'];
            }

            $newProduct['fevi_icon'] = base_url('assets/images/product/upload/thumb/'. $product['product_featured_image']);

            $newProduct['title'] = $product['product_name'];


            if($product['seller_id']){
                
                $seller = $Product_model->getSellerFromProduct($product['product_id']);
                
                $seller_setting = $Product_model->getSellerSetting($seller->user_id);

                $commnent_line = "";
                
                if($seller->affiliate_sale_commission_type == 'default'){ 
                    if($seller_setting->affiliate_sale_commission_type == ''){
                        $commnent_line .= __('user.warning_default_commission_not_set');
                    }
                    else if($seller_setting->affiliate_sale_commission_type == 'percentage'){
                        if($award_level_status == 1 && $userComission['status'] && $userComission['value'] && $userComission['value'] < $seller_setting->affiliate_commission_value)
                            $affiliate_commission_value = $userComission['value'];
                        else
                            $affiliate_commission_value = (float) $seller_setting->affiliate_commission_value;

                        $commnent_line .= $affiliate_commission_value .'%';
                    }
                    else if($seller_setting->affiliate_sale_commission_type == 'fixed'){
                        $commnent_line .= c_format($seller_setting->affiliate_commission_value);
                    }
                } else if($seller->affiliate_sale_commission_type == 'percentage'){
                    if($award_level_status == 1 && $userComission['status'] && $userComission['value'] && $userComission['value'] < $seller->affiliate_commission_value)
                        $affiliate_commission_value = $userComission['value'];
                    else
                        $affiliate_commission_value = (float) $seller->affiliate_commission_value;

                    $commnent_line .=  $affiliate_commission_value .'%';
                } else if($seller->affiliate_sale_commission_type == 'fixed'){
                    $commnent_line .= c_format($seller->affiliate_commission_value);
                } 

                $newProduct['sale_commision_you_will_get'] = $commnent_line.' '.__('user.per_sale');

                $commnent_line = "";
                if($seller->affiliate_click_commission_type == 'default'){ 
                    $commnent_line .= c_format($seller_setting->affiliate_click_amount) ." ".__('user.per')." ". (int)$seller_setting->affiliate_click_count ." ".__('user.clicks');
                } else{
                    $commnent_line .= c_format($seller->affiliate_click_amount) ." ".__('user.per')." ". (int)$seller->affiliate_click_count ." ".__('user.clicks');
                } 
                
                $newProduct['click_commision_you_will_get'] = $commnent_line;

                if($product['product_recursion_type']){
                    if($product['product_recursion_type'] == 'custom'){
                        if($product['product_recursion'] != 'custom_time'){
                            $newProduct['recurring'] = __('user.'.$product['product_recursion']);
                        } else {
                            $newProduct['recurring'] = timetosting($product['recursion_custom_time']);
                        }
                    } else{
                        if($pro_setting['product_recursion'] == 'custom_time' ){
                            $newProduct['recurring'] = timetosting($pro_setting['recursion_custom_time']);
                        } else {
                            $newProduct['recurring'] = __('user.'.$pro_setting['product_recursion']);
                        }
                    }
                }
            } else { 

                if($product['product_commision_type'] == 'default'){
                    
                    if($default_commition['product_commission_type'] == 'percentage'){
                        if($award_level_status == 1 && $userComission['status'] && $userComission['value'] && $userComission['value'] < $default_commition['product_commission'])
                            $product_commission = $userComission['value'];
                        else
                            $product_commission = $default_commition['product_commission'];

                        $newProduct['sale_commision_you_will_get'] = $product_commission. "% ".__('user.per_sale');
                    } else {
                        $newProduct['sale_commision_you_will_get'] = c_format($default_commition['product_commission']) ." ".__('user.per_sale');
                    }
                } else if($product['product_commision_type'] == 'percentage'){
                    if($award_level_status == 1 && $userComission['status'] && $userComission['value'] && $userComission['value'] < $product['product_commision_value'])
                        $product_commision_value = $userComission['value'];
                    else
                        $product_commision_value = $product['product_commision_value'];

                    $newProduct['sale_commision_you_will_get'] = $product_commision_value. "% ".__('user.per_sale');
                } else{
                    $newProduct['sale_commision_you_will_get'] = c_format($product['product_commision_value']) ." ".__('user.per_sale');
                }

                if($product['product_click_commision_type'] == 'default'){
                    $newProduct['click_commision_you_will_get'] = c_format($default_commition['product_ppc']) ." ".__('user.per')." {$default_commition['product_noofpercommission']} ".__('user.click');   
                } else{
                    $newProduct['click_commision_you_will_get'] = c_format($product['product_click_commision_ppc']) ." ".__('user.per')." {$product['product_click_commision_per']} ".__('user.click');
                }

                if($product['product_recursion_type']){
                    if($product['product_recursion_type'] == 'custom'){
                        if($product['product_recursion'] != 'custom_time'){
                            $newProduct['recurring'] = __('user.'.$product['product_recursion']);
                        } else {
                            $newProduct['recurring'] = timetosting($product['recursion_custom_time']);
                        }
                    } else{
                        if($pro_setting['product_recursion'] == 'custom_time' ){
                            $newProduct['recurring'] = timetosting($pro_setting['recursion_custom_time']);
                        } else {
                            $newProduct['recurring'] = __('user.'.$pro_setting['product_recursion']);
                        }
                    }
                }
            }

            $newProduct['description'] = $product['product_short_description'];
            $newProduct['price'] = c_format($product['product_price']);
            $newProduct['product_sku'] = $product['product_sku'];
            $newProduct['sales_commission'] = $product['order_count']."/".c_format($product['commission']);
            $newProduct['clicks_commission'] = (int)$product['commition_click_count']."/".c_format($product['commition_click']);

            $newProduct['total_commission'] = c_format((float)$product['commition_click'] + (float)$product['commission']);
            
            $newProduct['displayed_on_store'] = (bool)$product['on_store'];
        } else {

            $newProduct['aff_tool_type'] = $product['_tool_type'];

            $newProduct['public_page'] = $product['redirectLocation'][0];

            $newProduct['fevi_icon'] = base_url('assets/images/product/upload/thumb/'. $product['featured_image']);

            $newProduct['title'] = $product['name'];

            if($product['slug']) {
                $newProduct['share_url'] = base_url($product['slug']);
            }else{
                $newProduct['share_url'] = $product['redirectLocation'][0];
            }

            if($product['_tool_type'] == 'program' && $product['sale_status']) {
                $comm = '';
                
                if($product['commission_type'] == 'percentage'){
                    if($award_level_status == 1 && $userComission['status'] && $userComission['value'] && $userComission['value'] < $product['commission_sale'])
                        $commission_sale = $userComission['value'];
                    else
                        $commission_sale = $product['commission_sale'];

                    $comm = $commission_sale.'%'; 
                } else if($product['commission_type'] == 'fixed'){ 
                    $comm = c_format($product['commission_sale']); 
                }
                
                $newProduct['sale_commision_you_will_get'] = $comm." ".__('user.per_sale');
            }

            if($product['_tool_type'] == 'program' && $product['click_status']) {
                $newProduct['click_commision_you_will_get'] = c_format($product["commission_click_commission"]). " ".__('user.per')." ". $product['commission_number_of_click'] ." ".__('user.clicks');
            }

            if($product['_tool_type'] == 'general_click') {
                $newProduct['click_commision_you_will_get'] = c_format($product["general_amount"]). " ".__('user.per')." ". $product['general_click'] ." ".__('user.clicks');
            }

            if($product['_tool_type'] == 'action' || $product['_tool_type'] == 'single_action') {
                $newProduct['click_commision_you_will_get'] = c_format($product["action_amount"]). " ".__('user.per')." ". $product['action_click'] ." ".__('user.actions');
            }


            if($product['recursion']){
                if($product['recursion'] != 'custom_time'){
                    $newProduct['recurring'] = __('user.'.$product['recursion']);
                } else {
                    $newProduct['recurring'] = timetosting($product['recursion_custom_time']);
                }
            }


            if(isset($product['total_external_click_count'])) {
                $total_trigger_count = ($product['total_external_click_count'] > $product['total_trigger_count']) ? $product['total_external_click_count'] : $product['total_trigger_count'];

                if($total_trigger_count > 0) {
                    $click_conversion_ration = ($product['total_external_click_count'] / $total_trigger_count) * 100;
                    $click_conversion_ration = round($click_conversion_ration)."%";

                    $newProduct['click_ratio'] = $click_conversion_ration;
                }
            }


            if(isset($product['total_external_sale_count']) && $product['_tool_type'] == "program") {

                $total_external_click_trigger = ($product['total_external_sale_count'] > $product['total_trigger_count']) ? $product['total_external_sale_count'] : $product['total_trigger_count'];

                if($total_external_click_trigger > 0) {
                    $sale_conversion_ration = ($product['total_external_sale_count'] / $total_external_click_trigger) * 100;
                    $sale_conversion_ration = round($sale_conversion_ration)."%";

                    $newProduct['sale_ratio'] = $sale_conversion_ration;
                }
            }

            if($product['_tool_type'] == 'program' && $product['sale_status']){ 
                $newProduct['sale_count'] = (int)$product['total_sale_count'];
                $newProduct['sale_amount'] = $product['total_sale_amount'];
            }

            if($product['_tool_type'] == 'program' && $product['click_status']){
                $newProduct['click_count'] = (int)$product['total_click_count'];
                $newProduct['click_amount'] = $product['total_click_amount'];
            }

            if($product['_tool_type'] == 'general_click'){
                $newProduct['general_count'] = (int)$product['total_general_click_count'];
                $newProduct['general_amount'] = $product['total_general_click_amount'];
            }

            if($product['_tool_type'] == 'action' || $product['_tool_type'] == 'single_action'){
                $newProduct['action_count'] = (int)$product['total_action_click_count'];
                $newProduct['action_amount'] = $product['total_action_click_amount'];
            }
        }

        return $newProduct;
    }
}

