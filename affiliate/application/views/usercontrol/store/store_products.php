<?php
    $db =& get_instance();
    $Product_model = $db->Product_model;
    $userdetails = $db->Product_model->userdetails('user');
?>

<?php if ($currentTheme=="sales" || $StoreStatus=="0"){ ?>

<h4 class="notification_on_pages">
   <div class="well">
      <span class="badge bg-danger"><?= __('admin.cart_product_notice') ?></span>
   </div>
</h4>
<?php } ?>

<div class="card">
    <div class="tab-pane p-3" id="tab_main" role="tabpanel">
        <div role="tabpanel">
            <ul class="nav nav-pills orange-color-bg" role="tablist" id="TabsNav">
                <li class="nav-item" role="presentation">
                    <a class="nav-link active" id="products-tab" data-bs-toggle="tab" href="#products_tab" role="tab" aria-controls="products_tab" aria-selected="true" style="margin-right:10px; background-color: var(--bs-primary); color: var(--bs-white);"><?= __('user.products') ?></a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" id="review-tab" data-bs-toggle="tab" href="#review_tab" role="tab" aria-controls="review_tab" aria-selected="false" style="background-color: var(--bs-success); color: var(--bs-white);"><?= __('user.review') ?></a>
                </li>
            </ul>
        </div>
    </div>

    <div class="tab-content">
        <div role="tabpanel" class="tab-pane active" id="products_tab">
           <div class="card">
    	   <div class="card-header">

            <h5 class="m-0 card-title pull-left"><?= __('user.products') ?></h5>

    		<div class="pull-right">

    		<a class="btn btn-lg btn-default btn-success" href="<?php echo base_url("usercontrol/store_edit_product") ?>"><?= __('admin.add_product') ?></a>
            <a class="btn btn-lg btn-default btn-success" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#manageBulkProducts"><?= __('user.manage_bulk_products') ?></a>
    		
    		<button type="button" class="btn d-none btn-danger" id="deletebutton" onclick="deleteuserlistfunc('deleteAllproducts');"><?= __('admin.delete_products') ?></button>
        
        
            <div id="manageBulkProducts" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-hidden="true">
              <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><?= __('user.manage_bulk_products') ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-6 text-center">
    							<button class="btn btn-lg btn-default btn-success text-center export-products-btn"><?= __('user.export_products') ?></button>
                            </div>
                            <div class="col-6 text-center">
    							<button class="btn btn-lg btn-default btn-success text-center export-structure-btn"><?= __('user.export_structure_only') ?></button>
                            </div>

                        </div>
                        <br/>
                        <div class="row">
                            <div class="col-6 text-center">
                                <button class="btn btn-lg btn-default btn-success text-center export-products-xml-btn"><?= __('user.export_products_xml') ?></button>
                            </div>
                            <div class="col-6 text-center">
                                <button class="btn btn-lg btn-default btn-success text-center export-structure-xml-btn"><?= __('user.export_structure_xml_only') ?></button>
                            </div>
                        </div>    
                        <hr/>
                        <div class="row">
                            <div class="col">

            <!-- tab start -->
            <div class="container">
                <div class="tab-content">
                    <ul class="nav nav-pills mb-3" id="TabsNav" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active" id="import_file_tab-tab" data-bs-toggle="pill" href="#import_file_tab" role="tab" aria-controls="import_file_tab" aria-selected="true"><?= __('user.import_from_file') ?></a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" id="import_link_tab-tab" data-bs-toggle="pill" href="#import_link_tab" role="tab" aria-controls="import_link_tab" aria-selected="false"><?= __('user.import_from_url') ?></a>
                        </li>
                    </ul>
                    <div class="tab-content" id="TabsNavContent">
                        <div class="tab-pane fade show active" id="import_file_tab" role="tabpanel" aria-labelledby="import_file_tab-tab">
                            <form id="bulk_products_form" class="text-center">
                                <div class="mb-3">
                                    <label for="formFile" class="form-label"><?= __('user.upload_excel_file_for_bulk_product_manage') ?></label>
                                    <input class="form-control" type="file" id="formFile" name="file">
                                </div>
                                <button id="bulk_products_form_btn" type="submit" class="btn btn-success"><?= __('user.import_products') ?></button>
                            </form>
                        </div>
                        <div class="tab-pane fade" id="import_link_tab" role="tabpanel" aria-labelledby="import_link_tab-tab">
                            <form id="bulk_products_form_url" class="text-center">
                                <div class="mb-3">
                                    <label for="xmlurl" class="form-label"><?= __('user.enter_xml_url_for_bulk_product_manage') ?></label>
                                    <input type="text" class="form-control" id="xmlurl" name="txt_xmlurl" placeholder="<?= __('user.enter_xml_url_for_bulk_product_manage') ?>">
                                </div>
                                <button id="bulk_products_form_url_btn" type="submit" class="btn btn-success"><?= __('user.import_products') ?></button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
      

        <a class="mb-4" href="javascript:void(0)" data-bs-toggle="collapse" data-bs-target="#collapseInstructions" aria-expanded="false" aria-controls="collapseInstructions"><?= __('user.click_here_for_excel_file_upload_instructions') ?></a>

        <div class="collapse" id="collapseInstructions">
            <div class="card card-body text-left" style="max-height: 300px; overflow-y: scroll">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <td><?= __('user.column') ?></td>
                            <td><?= __('user.description') ?></td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?= __('user.product_id') ?></td>
                            <td>
                                <ul>
                                    <li><?= __('user.optional') ?></li>
                                    <li><?= __('user.datatype_be_number_or_empty_ex_1_2_3_4') ?></li>
                                    <li><?= __('user.for_update_products_use_correct_affiliate_script') ?> <strong><?= __('user.product_id') ?></strong> <?= __('user.in_column_or_just_export_product_and_check') ?> <strong><?= __('user.product_id') ?></strong> </li>
                                    <li><?= __('user.for_create_products_keep_product_id_column_empty_or_use') ?> <strong><?= __('user.product_id') ?></strong></li>
                                </ul>
                            </td>
                        </tr>
                        <tr>
                            <td><?= __('user.product_name') ?></td>
                            <td>
                                <ul>
                                    <li><?= __('user.required') ?></li>
                                    <li><?= __('user.datatype_should_be_string_or_empty') ?></li>
                                </ul>
                            </td>
                        </tr>
                        <tr>
                            <td><?= __('user.product_sku') ?></td>
                            <td>
                                <ul>
                                    <li><?= __('user.required') ?></li>
                                    <li><?= __('user.datatype_should_be_string_or_empty') ?></li>
                                    <li><?= __('user.you_have_to_enter_stock_keeping') ?></li>
                                </ul>
                            </td>
                        </tr>
                        <tr>
                            <td><?= __('user.product_msrp') ?></td>
                            <td>
                                <ul>
                                    <li><?= __('user.optional') ?></li>
                                    <li><?= __('user.datatype_should_be_number_or_empty') ?></li>
                                    <li><?= __('user.you_can_maximum_sale_retail_price_for_compare') ?></li>
                                </ul>
                            </td>
                        </tr>
                        <tr>
                            <td><?= __('user.product_price') ?></td>
                            <td>
                                <ul>
                                    <li><?= __('user.required') ?></li>
                                    <li><?= __('user.datatype_should_be_number_greater_than_0') ?></li>
                                    <li><?= __('user.this_will_be_actual_sale_price_of_products') ?></li>
                                </ul>
                            </td>
                        </tr>
                        <tr>
                            <td><?= __('user.product_short_desc') ?></td>
                            <td>
                                <ul>
                                    <li><?= __('user.required') ?></li>
                                    <li><?= __('user.datatype_should_be_string_or_html') ?></li>
                                    <li><?= __('user.you_have_to_enter_short_description') ?></li>
                                </ul>
                            </td>
                        </tr>
                        <tr>
                            <td><?= __('user.product_description') ?></td>
                            <td>
                                <ul>
                                    <li><?= __('user.required') ?></li>
                                    <li><?= __('user.datatype_should_be_string_or_html') ?></li>
                                    <li><?= __('user.you_have_to_enter_detail_description') ?></li>
                                </ul>
                            </td>
                        </tr>
                        <tr>
                            <td><?= __('user.product_tags') ?></td>
                            <td>
                                <ul>
                                    <li><?= __('user.optional') ?></li>
                                    <li><?= __('user.datatype_should_be_comma_separated_string_or_empty') ?></li>
                                    <li><?= __('user.you_can_enter_here_tags_of_products_for_seo') ?></li>
                                </ul>
                            </td>
                        </tr>
                        <tr>
                            <td><?= __('user.product_type') ?></td>
                            <td>
                                <ul>
                                    <li><?= __('user.required') ?></li>
                                    <li><?= __('user.datatype_should_be_string_either_virtual_or_downloadable') ?></li>
                                    <li><?= __('user.you_can_enter_any_ine_string_like_virtual_or_downloadable') ?>/</li>
                                </ul>
                            </td>
                        </tr>
                        <tr>
                            <td><?= __('user.product_variations') ?></td>
                            <td>
                                <ul>
                                    <li><?= __('user.optional') ?></li>
                                    <li><?= __('user.datatype_should_be_well_formatted_json') ?></li>
                                </ul>
                                <pre style="overflow: visible;">
                                    {
                                        "colors":[
                                            {"code":"#FF0000","name":"Red","price":"10"},
                                            {"code":"#3014FF","name":"Blue","price":"15"}
                                        ],
                                        "size":[
                                            {"name":"Horizontal 56","price":"10"},
                                            {"name":"Horizontal 112","price":"15"}
                                        ]
                                    }
                                </pre>
                                <ul>
                                    <li><?= __('user.variations_can_be_colors_size_weight_material_anything_etc') ?></li>
                                </ul>
                            </td>
                        </tr>
                        <tr>
                            <td><?= __('user.allow_comment') ?></td>
                            <td>
                                <ul>
                                    <li><?= __('user.required') ?></li>
                                    <li><?= __('user.datatype_should_be_number_false_or_true') ?></li>
                                    <li><?= __('user.customer_can_comments_on_product_or_not') ?></li>
                                </ul>
                            </td>
                        </tr>
                        <tr>
                            <td><?= __('user.allow_shpping') ?></td>
                            <td>
                                <ul>
                                    <li><?= __('user.required') ?></li>
                                    <li><?= __('user.datatype_should_be_number_false_or_true') ?></li>
                                    <li><?= __('user.product_shippable_or_not') ?></li>
                                </ul>
                            </td>
                        </tr>
                        <tr>
                            <td><?= __('user.allow_file_upload') ?></td>
                            <td>
                                <ul>
                                    <li><?= __('user.required') ?></li>
                                    <li><?= __('user.datatype_should_be_number_false_or_true') ?></li>
                                    <li><?= __('user.customer_can_upload_files_while_product_order_or_not') ?></li>
                                </ul>
                            </td>
                        </tr>
                        <tr>
                            <td><?= __('user.allow_on_store') ?></td>
                            <td>
                                <ul>
                                    <li><?= __('user.required') ?></li>
                                    <li><?= __('user.datatype_should_be_number_false_or_true') ?></li>
                                    <li><?= __('user.product_need_to_show_on_store_or_not') ?></li>
                                </ul>
                            </td>
                        </tr>
                        <tr>
                            <td><?= __('user.currency') ?></td>
                            <td>
                                <ul>
                                    <li><?= __('user.required') ?></li>
                                    <li><?= __('user.datatype_should_be_string') ?></li>
                                    <li><?= __('user.currency_code_as_per_international_3_digits_code') ?></li>
                                </ul>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

                            </div>
                        </div>
                    </div>
                </div>
              </div>
            </div>
                            
                            
            <div id="manageBulkProductsConfirmation" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-xl">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title"><?= __('user.manage_bulk_products_confirmation') ?></h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body" style="max-height:350px; overflow-y:scroll;">
                                
                            </div>
                            <div class="modal-footer">
                                <button class="btn btn-lg btn-default btn-success text-center import-products-confirm"><?= __('user.confirm_product_image') ?></button>
                                <button class="btn btn-lg btn-default btn-success text-center" data-bs-dismiss="modal"><?= __('user.cancel') ?></button>
                            </div>
                        </div>
                      </div>
                    </div>
                    
                    <div id="manageBulkProductsResult" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-hidden="true">
                      <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title"><?= __('user.manage_bulk_products_result') ?></h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body" style="max-height:350px; overflow-y:scroll;">
                                
                            </div>
                            <div class="modal-footer">
                                <button class="btn btn-lg btn-default btn-success text-center" onclick="window.location.reload()"><?= __('user.ok') ?></button>
                            </div>
                        </div>
                      </div>
                    </div>



    	        </div>
           </div>

    	   <div class="card-body">

    		<?php if ($productlist == null) {?>
                <div class="text-center mt-5">
                    <div class="d-flex justify-content-center align-items-center flex-column mt-5">
                         <i class="fas fa-exchange-alt fa-5x text-muted"></i>
                         <h3 class="text-muted"><?= __('admin.no_data_found') ?></h3>
                    </div>
                </div>
            <?php } else { ?>

            	<div class="table-responsive b-0" data-pattern="priority-columns">

    				<form method="post" name="deleteAllproducts" id="deleteAllproducts" action="<?php echo base_url('usercontrol/deleteAllproducts'); ?>">

    					<table id="tech-companies-1" class="table  table-striped">
    						<thead>
    							<tr>
    								<th><input name="product[]" type="checkbox" value="" onclick="checkAll(this)"></th>

    								<th width="220px"><?= __('admin.product_name') ?></th>

    								<th><?= __('admin.featured_image') ?></th>

    								<th><?= __('admin.price') ?></th>

    								<th><?= __('admin.sku') ?></th>

    								<th width="220px"><?= __('admin.get_ncommission') ?></th>

    								<th><?= __('admin.sales_/_commission') ?></th>

    								<th><?= __('admin.clicks_/_commission') ?></th>

    								<th><?= __('admin.total') ?></th>

    								<th><?= __('admin.status') ?></th>

    								<th><?= __('admin.action') ?></th>

    							</tr>

    						</thead>

    						<tbody>

    							<?php 

    								$pro_setting = $Product_model->getSettings('productsetting');

    								$vendor_setting = $Product_model->getSettings('vendor');

    							?>
 
    							<?php foreach($productlist as $product){ ?>

    								<?php 

    									$productLink = base_url('store/'. base64_encode($userdetails['id']) .'/product/'.$product['product_slug'] );

    								?>

    								<tr>
    									<td><input name="product[]" type="checkbox" id="check<?php echo $product['product_id'];?>" value="<?php echo $product['product_id'];?>"></td>

    									<td class="white-space-normal">

    										<div class="tooltip-copy">

    											<?php if($product['product_type'] == 'downloadable'){ ?>

    												<img src="<?= base_url('assets/images/download.png') ?>" width="20px">

    											<?php } ?>

    											<span><?php echo $product['product_name'];?></span>

    											<div><small><a target="_blank" href="<?= $productLink.'?preview=1' ?>">Public Page</a></small></div>

    										</div>

                                           <div class="give-rating m-2">
                                                <span><?=$product['totalreviews'];?> <?= __('user.customer_reviews') ?></span><br/>  
                                                <?php 
                                                $totalreviews=$product['totalreviews'];
                                                $totalrating=$product['totalrating'];
                                                if($totalreviews>0 && $totalrating>0)
                                                    $ratingAvg = number_format(($totalrating / $totalreviews), 0);
                                                else
                                                    $ratingAvg = 0;

                                                if($ratingAvg!="")
                                                {
                                                    for($i=0;$i<5;$i++)  {
                                                ?>

                                                    <div class="jq-star" style="width:20px;  height:20px;"><svg version="1.0" class="jq-star-svg" shape-rendering="geometricPrecision" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="305px" height="305px" viewBox="60 -62 309 309" style="enable-background:new 64 -59 305 305; stroke-width:4px;" xml:space="preserve"><style type="text/css">.svg-empty-388{fill:url(#388_SVGID_1_);}.svg-hovered-388{fill:url(#388_SVGID_2_);}.svg-active-388{fill:url(#388_SVGID_3_);}.svg-rated-388{fill:crimson;}</style><linearGradient id="388_SVGID_1_" gradientUnits="userSpaceOnUse" x1="0" y1="-50" x2="0" y2="250"><stop offset="0" style="stop-color:lightgray"></stop><stop offset="1" style="stop-color:lightgray"></stop> </linearGradient><linearGradient id="388_SVGID_2_" gradientUnits="userSpaceOnUse" x1="0" y1="-50" x2="0" y2="250"><stop offset="0" style="stop-color:orange"></stop><stop offset="1" style="stop-color:orange"></stop> </linearGradient><linearGradient id="388_SVGID_3_" gradientUnits="userSpaceOnUse" x1="0" y1="-50" x2="0" y2="250"><stop offset="0" style="stop-color:#FEF7CD"></stop><stop offset="1" style="stop-color:#FF9511"></stop> </linearGradient><polygon data-side="center" class="svg-empty-388" points="281.1,129.8 364,55.7 255.5,46.8 214,-59 172.5,46.8 64,55.4 146.8,129.7 121.1,241 212.9,181.1 213.9,181 306.5,241 " style="fill: transparent; stroke: black;"></polygon>
                                                        <?php if($i<$ratingAvg){ ?>
                                                        <polygon data-side="left" class="svg-active-388" points="281.1,129.8 364,55.7 255.5,46.8 214,-59 172.5,46.8 64,55.4 146.8,129.7 121.1,241 213.9,181.1 213.9,181 306.5,241 " style="stroke-opacity: 0;"></polygon><?php } ?></svg></div>
                                                <?php }
                                                    }
                                                 ?>
                                            </div>    

    									</td>

    									<td>

    										<div class="tooltip-copy">

    											<img width="50px" height="50px" src="<?php echo base_url('assets/images/product/upload/thumb/'. $product['product_featured_image']) ?>" ><br>

    										</div>

    									</td>

    									<td class="txt-cntr"><?php echo c_format($product['product_price']); ?></td>

    									<td class="txt-cntr"><?php echo $product['product_sku'];?></td>

    									<td class="txt-cntr commission-tr">

    										<?php 

    											if($product['seller_id']){

    												$seller = $Product_model->getSellerFromProduct($product['product_id']);

    												$seller_setting = $Product_model->getSellerSetting($seller->user_id);

    												$commnent_line = "";

    												if($seller->affiliate_sale_commission_type == 'default'){ 

    													if($seller_setting->affiliate_sale_commission_type == ''){

    														$commnent_line .= __('user.warning_default_commission_not_set');

    													}

    													else if($seller_setting->affiliate_sale_commission_type == 'percentage'){




    														$commnent_line .=  __('user.percentage').' : '. (float)$seller_setting->affiliate_commission_value .'%';

    													}

    													else if($seller_setting->affiliate_sale_commission_type == 'fixed'){

    														$commnent_line .= __('user.fixed').' : '. c_format($seller_setting->affiliate_commission_value);

    													}

    												} else if($seller->affiliate_sale_commission_type == 'percentage'){

    													$commnent_line .=  __('user.percentage').' : '. (float)$seller->affiliate_commission_value .'%';

    												} else if($seller->affiliate_sale_commission_type == 'fixed'){

    													$commnent_line .=  __('user.fixed').' : '. c_format($seller->affiliate_commission_value);

    												} 

    												echo '<b>'.__('user.sale').'</b> : ' .$commnent_line;

    												$commnent_line = "";

    												if($seller->affiliate_click_commission_type == 'default'){ 

    													$commnent_line .= c_format($seller_setting->affiliate_click_amount) ." ".__('user.per')." ". (int)$seller_setting->affiliate_click_count ." ".__('user.clicks');

    												} else{

    													$commnent_line .= c_format($seller->affiliate_click_amount) ." ".__('user.per')." ". (int)$seller->affiliate_click_count ." ".__('user.clicks');

    												} 

    												echo '<br><b>'.__('user.click').'</b> : ' .$commnent_line;

    												$commnent_line = '';

                                                    if ($vendor_setting['admin_click_status'] == 1) {
    												if($seller->admin_click_commission_type == '' || $seller->admin_click_commission_type == 'default'){



    													$commnent_line =  c_format($vendor_setting['admin_click_amount']) ." ".__('user.per')." ". (int)$vendor_setting['admin_click_count'] ." ".__('user.clicks');

    												} else{ 

    													$commnent_line =  c_format($seller->admin_click_amount) ." ".__('user.per')." ". (int)$seller->admin_click_count ." ".__('user.clicks');

    												} 

    												echo '<br><b>'.__('user.admin_click').'</b> : ' .$commnent_line;
                                                }
                                                if ($vendor_setting['admin_sale_status'] == 1) {

    												$commnent_line = '';



    												if($seller->admin_sale_commission_type == '' || $seller->admin_sale_commission_type == 'default'){ 

    													if($vendor_setting['admin_sale_commission_type'] == ''){

    														$commnent_line .= __('user.warning_default_commission_not_set');

    													}

    													else if($vendor_setting['admin_sale_commission_type'] == 'percentage'){

    														$commnent_line .= ''. (float)$vendor_setting['admin_commission_value'] .'%';

    													}

    													else if($vendor_setting['admin_sale_commission_type'] == 'fixed'){

    														$commnent_line .= ''. c_format($vendor_setting['admin_commission_value']);

    													}

    												} else if($seller->admin_sale_commission_type == 'percentage'){

    													
                                                        $commnent_line .= ''. (float)$seller->admin_commission_value  .'%';

    												} else if($seller->admin_sale_commission_type == 'fixed'){

    													$commnent_line .= ''. c_format($seller->admin_commission_value);

    												} else {

    													$commnent_line .= __('user.warning_commission_not_set');

    												}
                                                    echo '<br><b>'.__('user.admin_sale').'</b> : ' .$commnent_line;
                                                    }



    												

    											} 

    										?>


    										<?php 

    											if($product['product_recursion_type']){

    								           		if($product['product_recursion_type'] == 'custom'){

    								           			if($product['product_recursion'] != 'custom_time'){

    								           				echo '<br><b>'. __('user.recurring') .' </b> : ' .  __('admin.'.$product['product_recursion']);

    								           			} else {

    								           				echo '<br><b>'. __('user.recurring') .' </b> : '. timetosting($product['recursion_custom_time']);

    								           			}

    								           		} else{

    													if($pro_setting['product_recursion'] == 'custom_time' ){

    							           					echo '<br><b>'. __('user.recurring') .' </b> : '. timetosting($pro_setting['recursion_custom_time']);

    													} else {

    														echo '<br><b>'. __('user.recurring') .' </b> : '.  __('admin.'.$pro_setting['product_recursion']);

    													}

    								           		}

    								           	}

    										?>

    									</td>

    									<td class="txt-cntr">

    										<?php echo $product['order_count'];?> / 

    										<?php echo c_format($product['commission']) ;?> <br/><br/>

                                            <b><?= __('user.view') ?> / <?= __('user.ratio') ?></b><br/>
                                            <?php 
                                            $ordercountratio=0;
                                            if($product['view_statistics']>0)
                                                $ordercountratio=$product['all_order_count']*100/$product['view_statistics'];
                                            $ordercountratio = is_float($ordercountratio) ==1 ? number_format((float)$ordercountratio, 2, '.', '') : $ordercountratio ;
                                            echo $product['view_statistics'] . ' / '. $ordercountratio .'%';
                                            ?>

    									</td>

    									<td class="txt-cntr">

    										<?php echo (int)$product['commition_click_count'];?> / 

    										<?php echo  c_format($product['commition_click']) ;?><br/><br/>

                                            <b><?= __('user.view') ?> / <?= __('user.ratio') ?></b><br/>
                                            <?php 
                                            $ordercountratio=0;
                                            $comissionclickcount=(int)$product['all_commition_click_count'];
                                            if($product['view_statistics']>0)
                                                $ordercountratio=$comissionclickcount*100/$product['view_statistics'];
                                            $ordercountratio = is_float($ordercountratio) ==1 ? number_format((float)$ordercountratio, 2, '.', '') : $ordercountratio;
                                            echo $product['view_statistics'] . ' / '. $ordercountratio.'%';
                                            ?>

    									</td>

    									<td class="txt-cntr">

    										<?php echo

    											c_format((float)$product['commition_click'] + (float)$product['commission']);

    										?>

    									</td>

    									<td class="txt-cntr">

    										<?= product_status_on_store($product['on_store']) ?>

    										<?= product_status($product['product_status']) ?>	

    									</td>

    									<td class="txt-cntr">

    										<?php if((int)$product['product_status'] == 0){ ?>

    											<button type="button" class="btn btn-sm btn-primary edit-product mb-1" data-href="<?php echo base_url('usercontrol/store_edit_product/'. $product['product_id']);?>"><i class="fa fa-edit cursors" aria-hidden="true"></i></button>

    										<?php } else { ?>

    											<a class="btn btn-sm btn-primary mb-1" onclick="return confirm('<?= __('user.are_you_sure_to_edit') ?>');" href="<?php echo base_url('usercontrol/store_edit_product/'. $product['product_id']);?>"><i class="fa fa-edit cursors" aria-hidden="true"></i></a>

    										<?php } ?>

    										<a class="btn btn-sm btn-primary mb-1"  href="<?php echo base_url('usercontrol/duplicateProduct/'. $product['product_id']);?>"><i class="fa fa-clone cursors" aria-hidden="true"></i></a>

    										<a class="btn btn-sm btn-primary mb-1" href="<?php echo base_url('usercontrol/productupload/'. $product['product_id']);?>"><i class="fa fa-image cursors"></i></a>

                                			<a class="btn btn-sm btn-primary mb-1" href="<?php echo base_url('usercontrol/videoupload/'. $product['product_id']);?>"><i class="fa fa-youtube cursors"></i></a>

    										<span class="btn btn-sm btn-primary mb-1" data-social-share data-share-url="<?= $productLink; ?>?id=<?= $userdetails['id'] ?>" data-share-title="<?= $product['product_name'];?>" data-share-desc="<?= $product['product_short_description'];?>"><i class="fa fa-share-alt" aria-hidden="true"></i></span>

    										<button class="btn btn-danger btn-sm delete-product mb-1" type="button" data-id="<?= $product['product_id'] ?>"> <i class="fa fa-trash"></i> </button>

    									</td>

    								</tr>

    							<?php } ?>

    						</tbody>

    					</table>

    				</form>

    			</div>

    		<?php } ?>
        </div>
      </div>
    </div>
        <div role="tabpanel" class="tab-pane" id="review_tab">
            <div class="card">
                <div class="card-header">
                    <h5 class="m-0 card-title pull-left"><?= __('user.review') ?></h5>
                    <?php if($vendormanagereview==1) { ?>
                    <div class="pull-right">
                        <a class="btn btn-lg btn-default btn-success" href="<?php echo base_url("usercontrol/manage_review") ?>"><?= __('user.add_review') ?></a>
                        <a class="btn btn-lg btn-default btn-success" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#manageBulkReviews"><?= __('user.manage_bulk_reviews') ?></a>
                    </div>
                    <?php  } ?>
                    <div id="manageBulkReviews" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-hidden="true">
                      <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title"><?= __('user.manage_bulk_reviews') ?></h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-6 text-center">
                                        <button class="btn btn-lg btn-default btn-success text-center export-reviews-xml-btn"><?= __('user.export_reviews_xml') ?></button>
                                    </div>
                                    <div class="col-6 text-center">
                                        <button class="btn btn-lg btn-default btn-success text-center export-reviews-structure-xml-btn"><?= __('user.export_structure_xml_only') ?></button>
                                    </div> 
                                </div>

                    <!-- Tab Start -->
                    <div class="container mt-3">
                        <div class="tab-content">
                            <ul class="nav nav-pills mb-3" id="TabsNav" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link active" id="import_review_file_tab_-tab" data-bs-toggle="pill" href="#import_review_file_tab_" role="tab" aria-controls="import_review_file_tab_" aria-selected="true"><?= __('user.import_from_file') ?></a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" id="import_review_link_tab-tab" data-bs-toggle="pill" href="#import_review_link_tab" role="tab" aria-controls="import_review_link_tab" aria-selected="false"><?= __('user.import_from_url') ?></a>
                                </li>
                            </ul>
                            <div class="tab-content" id="TabsNavContent">
                                <div class="tab-pane fade show active" id="import_review_file_tab_" role="tabpanel" aria-labelledby="import_review_file_tab_-tab">
                                    <form id="bulk_reviews_form" class="text-center">
                                        <div class="mb-3">
                                            <label for="formFileReview" class="form-label"><?= __('user.upload_xml_file_for_bulk_review_manage') ?></label>
                                            <input class="form-control" type="file" id="formFileReview" name="file">
                                        </div>
                                        <button id="bulk_reviews_form_btn" type="submit" class="btn btn-success"><?= __('user.import_reviews') ?></button>
                                    </form>
                                </div>
                                <div class="tab-pane fade" id="import_review_link_tab" role="tabpanel" aria-labelledby="import_review_link_tab-tab">
                                    <form id="bulk_reviews_form_url" class="text-center">
                                        <div class="mb-3">
                                            <label for="reviewXmlUrl" class="form-label"><?= __('user.enter_xml_url_for_bulk_review_manage') ?></label>
                                            <input type="text" class="form-control" id="reviewXmlUrl" name="txt_review_xmlurl" placeholder="<?= __('user.enter_xml_url_for_bulk_review_manage') ?>">
                                        </div>
                                        <button id="bulk_reviews_form_url_btn" type="submit" class="btn btn-success"><?= __('user.import_reviews') ?></button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Tab End -->


                            </div>
                        </div>
                    </div>
                </div>

                <!-- -->
                    <div id="manageBulkReviewsConfirmation" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-hidden="true">
                          <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title"><?= __('user.manage_bulk_reviews_confirmation') ?></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body" style="max-height:350px; overflow-y:scroll;">
                                    
                                </div>
                                <div class="modal-footer">
                                    <button class="btn btn-lg btn-default btn-success text-center import-reviews-confirm"><?= __('user.confirm_product_image') ?></button>
                                    <button class="btn btn-lg btn-default btn-success text-center" data-bs-dismiss="modal"><?= __('user.cancel') ?></button>
                                </div>
                            </div>
                          </div>
                        </div>

                        <div id="manageBulkReviewsResult" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-hidden="true">
                          <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title"><?= __('user.manage_bulk_reviews_result') ?></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body" style="max-height:350px; overflow-y:scroll;">
                                    
                                </div>
                                <div class="modal-footer">
                                    <button class="btn btn-lg btn-default btn-success text-center" onclick="window.location.reload()"><?= __('user.ok') ?></button>
                                </div>
                            </div>
                          </div>
                        </div>
                    <!-- -->
                <div class="card-body">
                     <div class="filter">
                        <form id="filter-form-review">
                           <div class="row mt-5">
                              <div class="col-4">
                                 <div class="form-group"> 
                                    <select id="product_name_review" name="product_name_review" class="form-control" >
                                     <option value=""><?= __('admin.all_product') ?></option>
                                       <?php if(isset($productlist)){ ?>
                                          <?php foreach ($productlist as $key => $product) { ?>
                                             <option value="<?= $product['product_id'] ?>" <?php echo  $review['products_id']==$product['product_id'] ? 'selected' : ''?> ><?=$product['product_name']; ?></option> 
                                          <?php } ?>
                                       <?php } ?>
                                    </select>   
                                 </div>
                              </div> 
                              <div class="col-2">
                                  <div class="form-group">
                                    <button class="btn btn-default btn-success"><?= __('admin.total_review') ?> : <span id="total_review"></span></button>
                                  </div>
                              </div>
                              <div class="col-3">
                              </div>  
                           </div>
                        </form>
                     </div>
                     <div class="table-responsive b-0" data-pattern="priority-columns">
                           <div class="col-12">
                              <table id="table-review" class="table table-striped">
                                 <thead>
                                    <tr>
                                        <?php if($vendormanagereviewimage == 1){?>
                                       <th><?= __('user.image'); ?></th>
                                        <?php }?>
                                       <th><?= __('user.firstname'); ?></th>
                                       <th><?= __('user.lastname'); ?></th>
                                       <th><?= __('user.product_name'); ?></th>
                                       <th><?= __('user.review'); ?></th>
                                       <th><?= __('user.rating'); ?></th>
                                       <th><?= __('user.datetime'); ?></th> 
                                       <th><?= __("user.actions") ?></th>
                                    </tr>
                                 </thead>
                                 <tbody>
                                 </tbody>
                                 <tfoot>
                                    <tr>
                                       <td colspan="12" class="text-right">
                                          <ul class="pagination pagination-td"></ul>
                                       </td>
                                    </tr>
                                 </tfoot>
                              </table>
                           </div>
                     </div>

                </div>          
            </div>    
        </div>
    </div>    
</div>

<div class="modal" id="model-reviewmessage">

	<div class="modal-dialog">

		<div class="modal-content">

			<div class="modal-header">

				<h5 class="m-0 modal-title"><?= __('user.review') ?></h5>

				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

			</div>

			<div class="modal-body">

				<p><?= __('user.product_in_review_tru_to_edit_review_then_will_be_cancel') ?></p>

			</div>

			<div class="modal-footer">

				<a href="" class="btn btn-primary" id="edit_product_link"><?= __('user.okay_edit') ?></a>

				<button type="button" class="btn btn-danger" data-bs-dismiss="modal"><?= __('user.cancel') ?></button>

			</div>

		</div>

	</div>

</div>

<!-- -->


<?= $social_share_modal ?>

<script type="text/javascript">
    $(document).on('click', '.export-products-btn', function() {
	     exportProducts($(this), 0);
	});
	
	$(document).on('click', '.export-structure-btn', function() {
	     exportProducts($(this), 1);
	});

    $(document).on('click', '.export-products-xml-btn', function() {
        exportproductXML($(this), 0);
    });
   
    $(document).on('click', '.export-structure-xml-btn', function() {
        exportproductXML($(this), 1);
    });

	function exportProducts(thatBtn, structure_only  = 0) {
		$.ajax({
			url:'<?= base_url("usercontrol/exportproduct/") ?>',
			type:'POST',
			dataType:'json',
			data:{structure_only:structure_only},
			beforeSend:function(){thatBtn.btn("loading");},
			complete:function(){thatBtn.btn("reset");},
			success:function(json){
				if(json['download']){
					window.location.href = json['download'];
				}
			},
		});
	}

    function exportproductXML(thatBtn, structure_only  = 0) {
        $.ajax({
            url:'<?= base_url("usercontrol/exportproductXML/") ?>',
            type:'POST',
            dataType:'json',
            data:{structure_only:structure_only},
                beforeSend:function(){thatBtn.btn("loading");},
                complete:function(){thatBtn.btn("reset");},
                success:function(json){
            if(json['download'])
            {
               if(structure_only==0)
                  window.location.href='<?= base_url('usercontrol/downloadprodcutxmlfile'); ?>'; 
               else
                 window.location.href='<?= base_url('usercontrol/downloadprodcutxmlstructurefile'); ?>';   
   
            }
        },
        });
    }

    $('#bulk_products_form_btn').on('click', function(e){ 
        e.preventDefault();
        $("#bulk_products_form .alert-danger").remove();
        if($('#bulk_products_form input[name="file"]').val()) {
            $this = $(this);
            var fd = new FormData(document.getElementById("bulk_products_form"));
    
            $.ajax({
                url: '<?= base_url('usercontrol/bulkProductImport'); ?>',  
                type: 'POST',
                data: fd,
                dataType: 'html',
                beforeSend:function(){$this.btn("loading");},
                complete:function(){
                    $this.btn("reset");
                    $('#manageBulkProducts').modal('hide');
                },
                success:function(response){               
                    $('#manageBulkProductsConfirmation .modal-body').html(response);
                    $('#manageBulkProductsConfirmation').modal('show');
                    
                    if(! $('#manageBulkProductsConfirmation textarea[name="product_for_import"]').length > 0) {
                     $('#manageBulkProductsConfirmation .import-products-confirm').hide();  
                    } else {
                      $('#manageBulkProductsConfirmation .import-products-confirm').show();  
                    }
                },
                cache: false,
                contentType: false,
                processData: false
            });   
        } else {
            $("#bulk_products_form .custom-file").after('<div class="alert alert-danger">'+'<?= __('user.please_select_ecel_file_before_proceed') ?>'+'</div>');
        }
    });

    $('#bulk_products_form_url_btn').on('click', function(e){ 
        e.preventDefault();
        $("#bulk_products_form_url .alert-danger").remove();
        if($('#txt_xmlurl').val()!="") 
        {
            $this = $(this);
            var fd = new FormData(document.getElementById("bulk_products_form_url"));
   
            $.ajax({
                url: '<?= base_url('usercontrol/bulkProductImportFromUrl'); ?>',  
                type: 'POST',
                data: fd,
                dataType: 'html',
                beforeSend:function(){$this.btn("loading");},
                complete:function(){ 
                    $this.btn("reset");
                    $('#manageBulkProducts').modal('hide');
                },
                success:function(response){               
                    $('#manageBulkProductsConfirmation .modal-body').html(response);
                    $('#manageBulkProductsConfirmation').modal('show');
   
                    if(! $('#manageBulkProductsConfirmation textarea[name="product_for_import"]').length > 0) {
                        $('#manageBulkProductsConfirmation .import-products-confirm').hide();  
                    } else {
                        $('#manageBulkProductsConfirmation .import-products-confirm').show();  
                    }
                },
                cache: false,
                contentType: false,
                processData: false
            });   
        } else {
           $("#bulk_products_form_url .custom-file").after('<div class="alert alert-danger"><?= __('user.please_enter_xml_url') ?></div>');
        }
    });

    
    $('#manageBulkProductsConfirmation .import-products-confirm').on('click', function(e){
        e.preventDefault();
        if($('#manageBulkProductsConfirmation textarea[name="product_for_import"]').val()) {
            $this = $(this);
                var data = new FormData();
                data.append( 'products', $('#manageBulkProductsConfirmation textarea[name="product_for_import"]').val());
            $.ajax({
                url: '<?= base_url('usercontrol/bulkProductImportConfirm'); ?>',  
                type: 'POST',
                data: data,
                beforeSend:function(){$this.btn("loading");},
                complete:function(){
                    $this.btn("reset");
                    $('#manageBulkProductsConfirmation').modal('hide');
                },
                success:function(response){               
                    $('#manageBulkProductsResult .modal-body').html(response);
                    $('#manageBulkProductsResult').modal('show');
                },
                cache: false,
                contentType: false,
                processData: false
            });   
        } else {
            $("#bulk_products_form .custom-file").after('<div class="alert alert-danger">'+'<?= __('user.please_select_ecel_file_before_proceed') ?>'+'</div>');
        }
    });

	$(".delete-product").on('click',function(){
		if(! confirm('<?= __('user.are_you_sure') ?>')) return false;

		window.location = $("#deleteAllproducts").attr("action") + "?delete_id=" + $(this).attr("data-id");
	})


	$(".edit-product").on('click',function(){
		$("#model-reviewmessage").modal("show");

		$("#edit_product_link").attr("href", $(this).attr("data-href"))
	})

	

	function checkAll(bx) {

		var cbs = $(bx).prop("checked");

		if(cbs){

			$('#deletebutton').removeClass('d-none');

		} else {

			$('#deletebutton').addClass('d-none');

		}


		$("tbody input[type=checkbox]").prop("checked", cbs);

	}



	$("tbody input[type=checkbox]").on("change",function(){

		if($("tbody input[type=checkbox]:checked").length){

			$('#deletebutton').removeClass('d-none');

		} else {

			$('#deletebutton').addClass('d-none');

		}

	})



	function deleteuserlistfunc(formId){

		if(! confirm('<?= __('user.are_you_sure') ?>')) return false;

		$('#'+formId).submit();

	}

    $(".not-show-alert").on('click',function(){
        $this = $(this);
        $.ajax({
            url:'<?= base_url("usercontrol/setCookie") ?>',
            type:'POST',
            dataType:'json',
            data:{
                name: 'product_count_alert',
            },
            success:function(result){
                if(result)
                    $this.parents('.row').remove();
            },
        })
    })

    /*  */

    $('#bulk_reviews_form_url_btn').on('click', function(e){ 
        e.preventDefault();
        $("#bulk_reviews_form_url .alert-danger").remove();
        if($('#txt_review_xmlurl').val()!="") 
        {
            $this = $(this);
            var fd = new FormData(document.getElementById("bulk_reviews_form_url"));
   
            $.ajax({
                url: '<?= base_url('usercontrol/bulkReviewImportFromUrl'); ?>',  
                type: 'POST',
                data: fd,
                dataType: 'html',
                beforeSend:function(){$this.btn("loading");},
                complete:function(){ 
                    $this.btn("reset");
                    $('#manageBulkReviews').modal('hide');
                },
                success:function(response){               
                    $('#manageBulkReviewsConfirmation .modal-body').html(response);
                    $('#manageBulkReviewsConfirmation').modal('show');
   
                    if(! $('#manageBulkReviewsConfirmation textarea[name="reviews_for_import"]').length > 0) {
                        $('#manageBulkReviewsConfirmation .import-reviews-confirm').hide();  
                    } else {
                        $('#manageBulkReviewsConfirmation .import-reviews-confirm').show();  
                    }
                },
                cache: false,
                contentType: false,
                processData: false
            });   
        } else {
           $("#bulk_reviews_form_url .custom-file").after('<div class="alert alert-danger"><?= __('user.please_enter_xml_url') ?></div>');
        }
    });

    $('#bulk_reviews_form_btn').on('click', function(e){ 
        e.preventDefault();
        $("#bulk_reviews_form .alert-danger").remove();
        if($('#bulk_reviews_form input[name="file"]').val()) {
            $this = $(this);
            var fd = new FormData(document.getElementById("bulk_reviews_form"));

            $.ajax({
                url: '<?= base_url('usercontrol/bulkReviewsImport'); ?>',  
                type: 'POST',
                data: fd,
                dataType: 'html',
                beforeSend:function(){$this.btn("loading");},
                complete:function(){
                    $this.btn("reset");
                    $('#manageBulkReviews').modal('hide');
                },
                success:function(response){               
                    $('#manageBulkReviewsConfirmation .modal-body').html(response);
                    $('#manageBulkReviewsConfirmation').modal('show');
                    
                    if(! $('#manageBulkReviewsConfirmation textarea[name="reviews_for_import"]').length > 0) {
                     $('#manageBulkReviewsConfirmation .import-reviews-confirm').hide();  
                    } else {
                      $('#manageBulkReviewsConfirmation .import-reviews-confirm').show();  
                    }
                },
                cache: false,
                contentType: false,
                processData: false
            });   
        } else {
            $("#bulk_products_form .custom-file").after('<div class="alert alert-danger">'+'<?= __('user.please_select_xml_file_before_proceed') ?>'+'</div>');
        }
    });

    $('#manageBulkReviewsConfirmation .import-reviews-confirm').on('click', function(e){
        e.preventDefault(); 
        if($('#manageBulkReviewsConfirmation textarea[name="reviews_for_import"]').val()) {
             $this = $(this);
                var data = new FormData();
                data.append('reviews', $('#manageBulkReviewsConfirmation textarea[name="reviews_for_import"]').val());
            $.ajax({
                url: '<?= base_url('usercontrol/bulkReviewImportConfirm'); ?>',  
                type: 'POST',
                data: data,
                beforeSend:function(){$this.btn("loading");},
                complete:function(){
                    $this.btn("reset");
                    $('#manageBulkReviewsConfirmation').modal('hide');
                },
                success:function(response){               
                    $('#manageBulkReviewsResult .modal-body').html(response);
                    $('#manageBulkReviewsResult').modal('show');
                },
                cache: false,
                contentType: false,
                processData: false
            });   
        } else {
            $("#bulk_reviews_form .custom-file").after('<div class="alert alert-danger">'+'<?= __('user.please_select_xml_file_before_proceed') ?>'+'</div>');
        }
    });

    $(document).on('click', '.export-reviews-xml-btn', function() {
         exportReviewXML($(this), 0);
    });
    
    $(document).on('click', '.export-reviews-structure-xml-btn', function() {
         exportReviewXML($(this), 1);
    });
    
    function exportReviewXML(thatBtn, structure_only  = 0) {
        $.ajax({
            url:'<?= base_url("usercontrol/exportReviewXML/") ?>',
            type:'POST',
            dataType:'json',
            data:{structure_only:structure_only},
                beforeSend:function(){thatBtn.btn("loading");},
                complete:function(){thatBtn.btn("reset");},
                success:function(json){
            if(json['download'])
            {

               if(structure_only==0)
                  window.location.href='<?= base_url('usercontrol/downloadproductreviewxmlfile'); ?>'; 
               else
                 window.location.href='<?= base_url('usercontrol/downloadproductreviewxmlstructurefile'); ?>';   
   
            }
        },
        });
    }

    $("#filter-form-review").on("submit",function(){
      var urlreview='<?= base_url("usercontrol/listreviews_ajax/")?>';
      getReviews(urlreview);
        return false;
    });

   $("#product_name_review").on("change",function(){
        $("#filter-form-review").submit();
    })

    $("#table-review .pagination-td").delegate("a","click",function(){
         getReviews($(this).attr("href"));
         return false;
    })

    function getReviews(url)
    {
      $this = $(this);
       $.ajax({
            url:url,
            type:'POST',
            dataType:'json',
            data:$("#product_name_review").serialize(),
            beforeSend:function(){$this.btn("loading");},
            complete:function(){$this.btn("reset");},
            success:function(json){
               if(json['view']){
                  $("#table-review tbody").html(json['view']);
                  $("#total_review").text(json['total']);
                  $("#table-review").show();
               } else {
                  $("#table-review").hide();
               }
        
               $("#table-review .pagination-td").html(json['pagination']);
            },
       });
    }
    $( document ).ready(function() {
       getReviews('<?= base_url("usercontrol/listreviews_ajax/")?>');
    });
</script>