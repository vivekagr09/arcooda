<?php
    $db =& get_instance();
    $Product_model = $db->Product_model;
    $userdetails = $db->Product_model->userdetails('user');
?>
<?php if ($currentTheme=="cart" ||$StoreStatus=="0"){?>
<h4 class="notification_on_pages">
    <div class="well">
        <span class="badge bg-danger"><?= __('user.sales_product_notice') ?></span>
    </div>
</h4>
<?php } ?>

<div class="card">
    <div class="card-header">
        <h5 class="m-0 card-title pull-left"><?= __('user.sales_mode_products') ?></h5>
        <div class="pull-right">
            <a class="btn btn-lg btn-default btn-success" href="<?php echo base_url("usercontrol/create") ?>"><?= __('user.add_product_campaign') ?></a>
            <a style="display: none;" class="btn btn-lg btn-default btn-success" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#manageBulkProducts"><?= __('user.manage_bulk_products') ?></a>
            <button type="button" class="btn d-none btn-danger" id="deletebutton" onclick="deleteuserlistfunc('deleteAllproducts');"><?= __('user.delete_products') ?></button>
            <div id="manageBulkProducts" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-hidden="true">
                      <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title"><?= __('user.manage_bulk_products') ?></h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                                  <span aria-hidden="true">&times;</span>
                                </button>
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
                                <hr/>
                                <div class="row">
                                    <div class="col">
                                    <form id="bulk_products_form" class="text-center">
                                        <div class="custom-file my-3">
                                          <input type="file" class="custom-file-input" name="file">
                                          <label class="custom-file-label" for="customFile"><?= __('user.upload_excel_file_for_bulk_product_manage') ?></label>
                                        </div>
                                        <button id="bulk_products_form_btn" type="submit" class="btn btn-lg btn-default btn-success text-center">Import Products</button><br/><br/>
                                        <a class="mb-4" href="javascript:void(0)" data-toggle="collapse" data-bs-target="#collapseInstructions" aria-expanded="false" aria-controls="collapseInstructions"><?= __('user.click_here_for_excel_file_upload_instructions') ?></a>
                                        <div class="collapse" id="collapseInstructions">
                                          <div class="card card-body text-left" style="max-height: 300px; overflow-y: scroll">
                                              <table class="table table-stripped">
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
                                                                <ul>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td><?= __('user.product_name') ?></td>
                                                            <td>
                                                                <ul>
                                                                    <li><?= __('user.required') ?></li>
                                                                    <li><?= __('user.datatype_should_be_string_or_empty') ?></li>
                                                                <ul>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td><?= __('user.product_sku') ?></td>
                                                            <td>
                                                                <ul>
                                                                    <li><?= __('user.required') ?></li>
                                                                    <li><?= __('user.datatype_should_be_string_or_empty') ?></li>
                                                                    <li><?= __('user.you_have_to_enter_stock_keeping') ?></li>
                                                                <ul>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td><?= __('user.product_msrp') ?></td>
                                                            <td>
                                                                <ul>
                                                                    <li><?= __('user.optional') ?></li>
                                                                    <li><?= __('user.datatype_should_be_number_or_empty') ?></li>
                                                                    <li><?= __('user.you_can_maximum_sale_retail_price_for_compare') ?></li>
                                                                <ul>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td><?= __('user.product_price') ?></td>
                                                            <td>
                                                                <ul>
                                                                    <li><?= __('user.required') ?></li>
                                                                    <li><?= __('user.datatype_should_be_number_greater_than_0') ?></li>
                                                                    <li><?= __('user.this_will_be_actual_sale_price_of_products') ?></li>
                                                                <ul>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td><?= __('user.product_short_desc') ?></td>
                                                            <td>
                                                                <ul>
                                                                    <li><?= __('user.required') ?></li>
                                                                    <li><?= __('user.datatype_should_be_string_or_html') ?></li>
                                                                    <li><?= __('user.you_have_to_enter_short_description') ?></li>
                                                                <ul>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td><?= __('user.product_description') ?></td>
                                                            <td>
                                                                <ul>
                                                                    <li><?= __('user.required') ?></li>
                                                                    <li><?= __('user.datatype_should_be_string_or_html') ?></li>
                                                                    <li><?= __('user.you_have_to_enter_detail_description') ?></li>
                                                                <ul>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td><?= __('user.product_tags') ?></td>
                                                            <td>
                                                                <ul>
                                                                    <li><?= __('user.optional') ?></li>
                                                                    <li><?= __('user.datatype_should_be_comma_separated_string_or_empty') ?></li>
                                                                    <li><?= __('user.you_can_enter_here_tags_of_products_for_seo') ?></li>
                                                                <ul>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td><?= __('user.product_type') ?></td>
                                                            <td>
                                                                <ul>
                                                                    <li><?= __('user.required') ?></li>
                                                                    <li><?= __('user.datatype_should_be_string_either_virtual_or_downloadable') ?></li>
                                                                    <li><?= __('user.you_can_enter_any_ine_string_like_virtual_or_downloadable') ?>/</li>
                                                                <ul>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td><?= __('user.product_variations') ?></td>
                                                            <td>
                                                                <ul>
                                                                    <li><?= __('user.optional') ?></li>
                                                                    <li><?= __('user.datatype_should_be_well_formatted_json') ?></li>
                                                                <ul>
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
                                                                <ul>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td><?= __('user.allow_shpping') ?></td>
                                                            <td>
                                                                <ul>
                                                                    <li><?= __('user.required') ?></li>
                                                                    <li><?= __('user.datatype_should_be_number_false_or_true') ?></li>
                                                                    <li><?= __('user.product_shippable_or_not') ?></li>
                                                                <ul>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td><?= __('user.allow_file_upload') ?></td>
                                                            <td>
                                                                <ul>
                                                                    <li><?= __('user.required') ?></li>
                                                                    <li><?= __('user.datatype_should_be_number_false_or_true') ?></li>
                                                                    <li><?= __('user.customer_can_upload_files_while_product_order_or_not') ?></li>
                                                                <ul>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td><?= __('user.allow_on_store') ?></td>
                                                            <td>
                                                                <ul>
                                                                    <li><?= __('user.required') ?></li>
                                                                    <li><?= __('user.datatype_should_be_number_false_or_true') ?></li>
                                                                    <li><?= __('user.product_need_to_show_on_store_or_not') ?></li>
                                                                <ul>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td><?= __('user.state_id') ?></td>
                                                            <td>
                                                                <ul>
                                                                    <li><?= __('user.optional') ?></li>
                                                                    <li><?= __('user.datatype_be_valid_country_of_script') ?></li>
                                                                    <li><?= __('user.product_available_country_location_need_state_id') ?></li>
                                                                <ul>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                              </table>
                                          </div>
                                        </div>
                                    </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                      </div>
                    </div>
                    
                    
                    <div id="manageBulkProductsConfirmation" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-hidden="true">
                      <div class="modal-dialog modal-lg">
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
                <form method="post" name="deleteAllproducts" id="deleteAllproducts" action="<?php echo base_url('usercontrol/deleteALLSaleproducts'); ?>">

                    <table id="tech-companies-1" class="table  table-striped">

                        <thead>

                            <tr>

                                <th><input name="product[]" type="checkbox" value="" onclick="checkAll(this)"></th>

                                <th width="220px"><?= __('user.product_name') ?></th>

                                <th><?= __('user.featured_image') ?></th>

                                <th><?= __('user.price') ?></th>

                                <th><?= __('user.sku') ?></th>

                                <th width="220px"><?= __('user.get_ncommission') ?></th>

                                <th><?= __('user.sales_/_commission') ?></th>

                                <th><?= __('user.clicks_/_commission') ?></th>

                                <th><?= __('user.total') ?></th>

                                <th><?= __('user.status') ?></th>

                                <th><?= __('user.action') ?></th>

                            </tr>

                        </thead>

                        <tbody>

                            <?php 

                                $pro_setting = $Product_model->getSettings('productsetting');

                                $vendor_setting = $Product_model->getSettings('vendor');

                            ?>



                            <?php 
                            foreach($productlist as $product){ ?>

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

                                            <!-- <div><small><a target="_blank" href="<?= $productLink.'?preview=1' ?>">Public Page</a></small></div> -->

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

                                                if ($vendor_setting['admin_click_status'] == 1) {


                                                $commnent_line = '';

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

                                                        echo '<br><b>'. __('user.recurring') .' </b> : ' .  __('user.'.$product['product_recursion']);

                                                    } else {

                                                        echo '<br><b>'. __('user.recurring') .' </b> : '. timetosting($product['recursion_custom_time']);

                                                    }

                                                } else{

                                                    if($pro_setting['product_recursion'] == 'custom_time' ){

                                                        echo '<br><b>'. __('user.recurring') .' </b> : '. timetosting($pro_setting['recursion_custom_time']);

                                                    } else {

                                                        echo '<br><b>'. __('user.recurring') .' </b> : '.  __('user.'.$pro_setting['product_recursion']);

                                                    }

                                                }

                                            }

                                        ?>

                                    </td>

                                    <td class="txt-cntr">

                                        <?php echo $product['order_count'];?> / 

                                        <?php echo c_format($product['commission']) ;?><br/><br/>
                                        <b><?= __('admin.view') ?> / <?= __('admin.ratio') ?></b><br/>
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

                                        <?php echo c_format($product['commition_click']) ;?><br/><br/>

                                        <b><?= __('admin.view') ?> / <?= __('admin.ratio') ?></b><br/>
                                        <?php 
                                        $ordercountratio=0;
                                        $comissionclickcount=(int)$product['all_commition_click_count'] ;
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
 
                                        <?= product_status_on_store_admin($product['on_store'], $product['product_created_by']) ?> <br>
                                        <?= product_status($product['product_status']) ?>   

                                    </td>

                                    <td class="txt-cntr">

                                         <button data-toggle="tooltip" title="<?php echo __('user.integration_code') ?>" class="btn-show-code btn btn-sm btn-primary mb-1" data-id='<?= $product['product_id'] ?>'>
                                            <i class="fa fa-code" aria-hidden="true"></i>
                                        </button>

                                        <?php if((int)$product['product_status'] == 0){ ?>

                                            <button type="button" class="btn btn-sm btn-primary edit-product mb-1" data-href="<?php echo base_url('usercontrol/update_sale_products/'. $product['product_id']);?>"><i class="fa fa-edit cursors" aria-hidden="true"></i></button>

                                        <?php } else { ?>

                                            <a class="btn btn-sm btn-primary mb-1" onclick="return confirm('<?= __('user.are_you_sure_to_edit') ?>');" href="<?php echo base_url('usercontrol/update_sale_products/'. $product['product_id']);?>"><i class="fa fa-edit cursors" aria-hidden="true"></i></a>

                                        <?php } ?>

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

<div class="modal fade" id="showcode-code"></div>

<script type="text/javascript">
    $(document).on('click', '.export-products-btn', function() {
         exportProducts($(this), 0);
    });
    
    $(document).on('click', '.export-structure-btn', function() {
         exportProducts($(this), 1);
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

        $('.btn-show-code').on('click', function(e){
                    e.preventDefault();

                    $this = $(this);

                    $.ajax({
                        url:'<?= base_url("usercontrol/integration_code_modal_sale") ?>',
                        type:'POST',
                        dataType:'html',
                        data:{
                            id: $this.attr("data-id"),
                        },
                        beforeSend:function(){
                            $this.btn("loading");
                        },
                        complete:function(){
                            $this.btn("reset");
                        },
                        success:function(html){
                            $("#showcode-code").html(html);
                            $("#showcode-code").modal("show");
                        },
                    })
                });

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

</script>