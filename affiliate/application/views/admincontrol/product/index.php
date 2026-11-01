<?php
   $db =& get_instance();
   $userdetails=$db->userdetails();
   $store_setting =$db->Product_model->getSettings('store');
   $Product_model =$db->Product_model;
   ?>
<div id="overlay"></div>
<div class="popupbox" style="display: none;">
   <div class="backdrop box">
      <div class="modalpopup" style="display:block;">
         <a href="javascript:void(0)" class="close js-menu-close" onclick="closePopup();"><i class="fa fa-times"></i></a>
         <div class="modalpopup-dialog">
            <div class="modalpopup-content">
               <div class="modalpopup-body">
               </div>
            </div>
         </div>
      </div>
   </div>
</div>

<?php if ($currentTheme=="sales" || $StoreStatus=="0"){ ?>
<!-- Notification On Pages -->
<h5 class="notification_on_pages mb-3">
    <div class="bg-danger text-white p-3 rounded">
        <?= __('admin.cart_product_notice') ?>
    </div>
</h5>
<?php } ?>

<div class="row product-page">
   <div class="col-12">
      <div class="card">
         <div class="card-header bg-secondary text-white">
            <div class="card-title-white pull-left m-0"><?= __('admin.cart_mode_products') ?>
            </div>
         </div>
         <div class="card-body">
            <div class="tab-pane p-3" id="store-setting" role="tabpanel">
               <div role="tabpanel">
               <ul class="nav nav-pills flex-column flex-sm-row" id="TabsNav">
                   <li class="nav-item flex-sm-fill text-sm-center">
                       <a class="nav-link active show product_tab_option" href="#product_tab" data-bs-toggle="tab"><?= __('admin.products') ?></a>
                   </li>
                   <li class="nav-item flex-sm-fill text-sm-center">
                       <a class="nav-link product-part product_coupons_tab_option" href="#product_coupons_tab" data-bs-toggle="tab"><?= __('admin.coupon') ?></a>
                   </li>
                   <li class="nav-item flex-sm-fill text-sm-center">
                       <a class="nav-link" href="#form_tab" data-bs-toggle="tab"><?= __('admin.forms') ?></a>
                   </li>
                   <li class="nav-item flex-sm-fill text-sm-center">
                       <a class="nav-link" href="#form_coupons_tab" data-bs-toggle="tab"><?= __('admin.forms_coupon') ?></a>
                   </li>
                   <li class="nav-item flex-sm-fill text-sm-center">
                       <a class="nav-link" href="#review_tab" data-bs-toggle="tab"><?= __('admin.review') ?></a>
                   </li>
               </ul>
               </div>
            </div>
                  
            <div class="tab-content">
               <div role="tabpanel" class="tab-pane active" id="product_tab">
                  <div class="filter">
                     <form id="filter-form">
                       <div class="row mt-4 g-3">
                         <div class="col-3">
                           <select name="category_id" class="form-select select-category">
                             <?php $selected = isset($_GET['category_id']) ? $_GET['category_id'] : ''; ?>
                             <option value=""><?= __('admin.all_category') ?></option>
                             <?php foreach ($categories as $key => $value) { ?>
                               <option <?= $selected == $value['id'] ? 'selected' : '' ?> value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                             <?php } ?>
                           </select>
                         </div>
                         <div class="col-3">
                           <select name="seller_id" class="form-select select-vendor">
                             <?php $selected = isset($_GET['seller_id']) ? $_GET['seller_id'] : ''; ?>
                             <option value=""><?= __('admin.all_vendor') ?></option>
                             <?php foreach ($vendors as $key => $value) { ?>
                               <option <?= $selected == $value['id'] ? 'selected' : '' ?> value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                             <?php } ?>
                           </select>
                         </div>
                         <div class="col-2">
                           <a id="toggle-uploader" class="btn btn-primary" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#manageBulkProducts"><?= __('admin.manage_bulk_products') ?></a>
                         </div>
                         <div class="col-2">
                           <a id="toggle-uploader" class="btn btn-light" href="<?php echo base_url('admincontrol/addproduct'); ?>"><?= __('admin.add_product') ?></a>
                         </div>
                         <div class="col-2">
                           <a style="display:none;" class="btn btn-danger" name="deletebutton" id="deletebutton" value="<?= __('admin.save_exit') ?>" onclick="deleteuserlistfunc('deleteAllproducts');"><?= __('admin.delete_products') ?></a>
                         </div>
                       </div>
                     </form>
                  </div>
                  <div class="table-rep-plugin">
                     <div class="row">
                        <div id="manageBulkProducts" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-hidden="true">
                           <div class="modal-dialog modal-lg">
                              <div class="modal-content">
                                 <div class="modal-header">
                                     <h5 class="modal-title"><?= __('admin.manage_bulk_products') ?></h5>
                                     <button type="button" class="btn-close" aria-label="Close" data-bs-dismiss="modal"></button>
                                 </div>
                  <div class="modal-body">
                      <div class="row g-4">
                          <div class="col-6 text-center">
                              <button class="btn btn-lg btn-success text-center export-products-btn w-100 mb-2"><?= __('admin.export_products') ?></button>
                          </div>
                          <div class="col-6 text-center">
                              <button class="btn btn-lg btn-success text-center export-structure-btn w-100 mb-2"><?= __('admin.export_structure_only') ?></button>
                          </div>
                      </div>

                      <div class="row g-4">
                          <div class="col-6 text-center">
                              <button class="btn btn-lg btn-success text-center xml-btn export-products-xml-btn w-100"><?= __('admin.export_products_xml') ?></button>
                          </div>
                          <div class="col-6 text-center">
                              <button class="btn btn-lg btn-success text-center xml-btn export-structure-xml-btn w-100"><?= __('admin.export_structure_xml_only') ?></button>
                          </div>
                      </div>


 
         <div class="row">
            <div class="col">
               <!-- tab start -->
               <div class="tab-pane p-3" id="tab_bulkprodcut_option" role="tabpanel">
                  <div role="tabpanel">
                     <ul class="nav nav-pills flex-column flex-sm-row" id="TabsNav">
                        <li class="nav-item flex-sm-fill text-sm-center">
                           <a class="nav-link active show" href="#import_file_tab" aria-controls="import_file_tab" role="tab" data-bs-toggle="tab"><?= __('admin.import_from_file') ?></a>
                        </li>
                        <li class="nav-item flex-sm-fill text-sm-center">
                           <a class="nav-link" href="#import_link_tab" aria-controls="import_link_tab" role="tab" data-bs-toggle="tab"><?= __('admin.import_from_url') ?></a>
                        </li> 
                     </ul>
                  </div>
               </div>
               <div class="tab-content">
                  <div role="tabpanel" class="tab-pane active" id="import_file_tab">
                     <form id="bulk_products_form" class="text-center">
                        <div class="custom-file my-3">
                           <input type="file" class="custom-file-input" name="file">
                           <label class="custom-file-label" for="customFile"><?= __('admin.upload_excel_file_for_bulk_product_manage') ?></label>
                        </div>
                        <button id="bulk_products_form_btn" type="submit" class="btn btn-lg btn-default btn-success text-center"><?= __('admin.import_products') ?></button><br/><br/>
                     </form>
                  </div>
                   <div role="tabpanel" class="tab-pane" id="import_link_tab">
                       <form id="bulk_products_form_url" class="text-center">
                        <div class="custom-file my-3">
                           <input name="txt_xmlurl" id="txt_xmlurl" class="textxmlurl"  type="text" tabindex="0" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" role="textbox" aria-autocomplete="list" 
                           placeholder="<?= __('admin.enter_xml_url_for_bulk_product_manage') ?>" style="width:100%;">
                            
                        </div>
                        <button id="bulk_products_form_url_btn" type="submit" class="btn btn-lg btn-default btn-success text-center"><?= __('admin.import_products') ?></button><br/><br/>
                     </form>
                  </div>
               </div>      
               <!--tab end -->

               

                  <a class="mb-4" href="javascript:void(0)" data-bs-toggle="collapse" data-bs-target="#collapseInstructions" aria-expanded="false" aria-controls="collapseInstructions"><?= __('admin.click_here_for_excel_file_upload') ?></a>
                  <div class="collapse" id="collapseInstructions">
                     <div class="card card-body text-left" style="max-height: 300px; overflow-y: scroll">
                        <table class="table table-stripped">
                           <thead>
                              <tr>
                                 <td><?= __('admin.column') ?></td>
                                 <td><?= __('admin.description') ?></td>
                              </tr>
                           </thead>
                           <tbody>
                              <tr>
                                 <td><?= __('admin.product_id') ?></td>
                                 <td>
                                    <ul>
                                    <li><?= __('admin.optional') ?></li>
                                    <li><?= __('admin.ip_product_id_desc_1') ?></li>
                                    <li><?= __('admin.ip_product_id_desc_2') ?></li>
                                    <li><?= __('admin.ip_product_id_desc_3') ?></li>
                                    <ul>
                                 </td>
                              </tr>
                              <tr>
                                 <td><?= __('admin.product_name') ?></td>
                                 <td>
                                    <ul>
                                    <li><?= __('admin.required') ?></li>
                                    <li><?= __('admin.ip_product_name_desc_1') ?></li>
                                    <ul>
                                 </td>
                              </tr>
                              <tr>
                                 <td><?= __('admin.product_sku') ?></td>
                                 <td>
                                    <ul>
                                    <li><?= __('admin.required') ?></li>
                                    <li><?= __('admin.ip_product_sku_desc_1') ?></li>
                                    <li><?= __('admin.ip_product_sku_desc_2') ?></li>
                                    <ul>
                                 </td>
                              </tr>
                              <tr>
                                 <td><?= __('admin.product_msrp') ?></td>
                                 <td>
                                    <ul>
                                    <li><?= __('admin.optional') ?></li>
                                    <li><?= __('admin.ip_product_msrp_desc_1') ?></li>
                                    <li><?= __('admin.ip_product_msrp_desc_2') ?></li>
                                    <ul>
                                 </td>
                              </tr>
                              <tr>
                                 <td><?= __('admin.product_price') ?></td>
                                 <td>
                                    <ul>
                                    <li><?= __('admin.required') ?></li>
                                    <li><?= __('admin.ip_product_price_desc_1') ?></li>
                                    <li><?= __('admin.ip_product_price_desc_2') ?></li>
                                    <ul>
                                 </td>
                              </tr>
                              <tr>
                                 <td><?= __('admin.product_short_desc') ?></td>
                                 <td>
                                    <ul>
                                    <li><?= __('admin.required') ?></li>
                                    <li><?= __('admin.ip_product_short_desc_desc_1') ?></li>
                                    <li><?= __('admin.ip_product_short_desc_desc_2') ?></li>
                                    <ul>
                                 </td>
                              </tr>
                              <tr>
                                 <td><?= __('admin.product_desc') ?></td>
                                 <td>
                                    <ul>
                                    <li><?= __('admin.required') ?></li>
                                    <li><?= __('admin.ip_product_desc_desc_1') ?></li>
                                    <li><?= __('admin.ip_product_desc_desc_2') ?></li>
                                    <ul>
                                 </td>
                              </tr>
                              <tr>
                                 <td><?= __('admin.product_tag') ?></td>
                                 <td>
                                    <ul>
                                    <li><?= __('admin.optional') ?></li>
                                    <li><?= __('admin.ip_product_tag_desc_1') ?></li>
                                    <li><?= __('admin.ip_product_tag_desc_2') ?></li>
                                    <ul>
                                 </td>
                              </tr>
                              <tr>
                                 <td><?= __('admin.product_type') ?></td>
                                 <td>
                                    <ul>
                                    <li><?= __('admin.required') ?></li>
                                    <li><?= __('admin.ip_product_type_disc_1') ?> "virtual", "downloadable"</li>
                                    <li><?= __('admin.ip_product_type_disc_2') ?>/</li>
                                    <ul>
                                 </td>
                              </tr>
                              <tr>
                                 <td><?= __('admin.product_variations') ?></td>
                                 <td>
                                    <ul>
                                    <li><?= __('admin.optional') ?></li>
                                    <li><?= __('admin.ip_product_variations_desc_1') ?></li>
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
                                    <pre>
                                    <ul>
                                       <li><?= __('admin.ip_product_variations_desc_2') ?></li>
                                    </ul>
                                 </td>
                              </tr>
                              <tr>
                                 <td><?= __('admin.allow_comment') ?></td>
                                 <td>
                                    <ul>
                                    <li><?= __('admin.required') ?></li>
                                    <li><?= __('admin.ip_allow_comment_desc_1') ?></li>
                                    <li><?= __('admin.ip_allow_comment_desc_2') ?></li>
                                    <ul>
                                 </td>
                              </tr>
                              <tr>
                                 <td><?= __('admin.allow_shipping') ?></td>
                                 <td>
                                    <ul>
                                    <li><?= __('admin.required') ?></li>
                                    <li><?= __('admin.ip_allow_shipping_desc_1') ?></li>
                                    <li><?= __('admin.ip_allow_shipping_desc_2') ?>t</li>
                                    <ul>
                                 </td>
                              </tr>
                              <tr>
                                 <td><?= __('admin.allow_file_uplolad') ?></td>
                                 <td>
                                    <ul>
                                    <li><?= __('admin.required') ?></li>
                                    <li><?= __('admin.ip_allow_file_uplolad_desc_1') ?></li>
                                    <li><?= __('admin.ip_allow_file_uplolad_desc_2') ?></li>
                                    <ul>
                                 </td>
                              </tr>
                              <tr>
                                 <td><?= __('admin.product_status') ?></td>
                                 <td>
                                    <ul>
                                    <li><?= __('admin.required') ?></li>
                                    <li><?= __('admin.ip_product_status_desc_1') ?></li>
                                    <li><?= __('admin.ip_product_status_desc_2') ?></li>
                                    <ul>
                                 </td>
                              </tr>
                              <tr>
                                 <td><?= __('admin.allow_on_store') ?></td>
                                 <td>
                                    <ul>
                                    <li><?= __('admin.required') ?></li>
                                    <li><?= __('admin.ip_allow_on_store_desc_1') ?>)</li>
                                    <li><?= __('admin.ip_allow_on_store_desc_2') ?></li>
                                    <ul>
                                 </td>
                              </tr>
                              <tr>
                                 <td><?= __('admin.state_id') ?></td>
                                 <td>
                                    <ul>
                                    <li><?= __('admin.optional') ?></li>
                                    <li><?= __('admin.ip_state_id_desc_1') ?></li>
                                    <li><?= __('admin.ip_state_id_desc_2') ?></li>
                                    <ul>
                                 </td>
                              </tr>
                              <tr>
                                 <td><?= __('admin.product_created_by') ?></td>
                                 <td>
                                    <ul>
                                    <li><?= __('admin.optional') ?></li>
                                    <li><?= __('admin.ip_product_created_by_desc_1') ?></li>
                                    <li><?= __('admin.ip_product_created_by_desc_2') ?></li>
                                    <ul>
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
                           <div class="modal-dialog modal-lg">
                              <div class="modal-content">
                                 <div class="modal-header">
                                    <h5 class="modal-title"><?= __('admin.manage_bulk_product_confirmation') ?></h5>
                                    <button type="button" class="btn-close" aria-label="Close" data-bs-dismiss="modal"></button>
                                 </div>
                                 <div class="modal-body" style="max-height:350px; overflow-y:scroll;">
                                 </div>
                                 <div class="modal-footer">
                                    <button class="btn btn-lg btn-success text-center import-products-confirm"><?= __('admin.confirm_product_import') ?></button>
                                    <button class="btn btn-lg btn-success text-center" data-bs-dismiss="modal"><?= __('admin.cancel') ?></button>
                                 </div>
                              </div>
                           </div>
                        </div>
                        <div id="manageBulkProductsResult" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-hidden="true">
                           <div class="modal-dialog modal-lg">
                              <div class="modal-content">
                                 <div class="modal-header">
                                    <h5 class="modal-title"><?= __('admin.manage_bulk_product_result') ?></h5>
                                    <button type="button" class="btn-close" aria-label="Close" data-bs-dismiss="modal"></button>
                                 </div>
                                 <div class="modal-body" style="max-height:350px; overflow-y:scroll;">
                                 </div>
                                 <div class="modal-footer">
                                    <button class="btn btn-lg btn-default btn-success text-center" id="bulkokbutton" onclick="window.location.reload()"><?= __('admin.ok') ?></button>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                     <br>
                     <?php if ($productlist == null) {?>
                     <div class="text-center">
                         <div class="d-flex justify-content-center align-items-center flex-column">
                             <i class="fas fa-exchange-alt fa-5x text-muted"></i>
                             <h3 class="text-muted"><?= __('admin.no_data_found') ?></h3>
                         </div>
                     </div>
                     <?php } else { ?>

                        <form method="post" name="deleteAllproducts" id="deleteAllproducts" action="<?php echo base_url('admincontrol/deleteAllproducts'); ?>">
                           <div class="table-responsive">
                           <table id="tech-companies-1" class="table table-striped btn-part">
                              <thead>
                                 <tr>
                                    <th><input name="product[]" type="checkbox" value="" onclick="checkAll(this)"></th>
                                    <th><?= __('admin.image') ?></th>
                                    <th><?= __('admin.product_name') ?></th>
                                    <th><?= __('admin.user') ?></th>
                                    <th><?= __('admin.price') ?></th>
                                    <th><?= __('admin.sku') ?></th>
                                    <th><?= __('admin.commission') ?></th>
                                    <th><?= __('admin.sales_/_commission') ?></th>
                                    <th><?= __('admin.clicks_/_commission') ?></th>
                                    <th><?= __('admin.total') ?></th>
                                    <th><?= __('admin.status') ?></th>
                                    <th><?= __('admin.action') ?></th>
                                 </tr>
                              </thead>
                              <tbody></tbody>
                              <tfoot>
                                 <tr>
                                    <td colspan="12" class="text-right">
                                       <ul class="pagination pagination-td"></ul>
                                    </td>
                                 </tr>
                              </tfoot>
                           </table>
                     </div>
                        </form>
                     <?php } ?>
                  </div>
               </div>
               <div role="tabpanel" class="tab-pane" id="product_coupons_tab">
                  <div class="table-rep-plugin">
                     <div class="pull-right mb-2 mt-2">
                        <a class="btn btn-primary" href="<?= base_url('admincontrol/coupon_manage/')  ?>"><?= __('admin.add_new'); ?></a>
                     </div>
                     <?php if ($coupons == null) {?>
                     <div class="text-center">
                         <div class="d-flex justify-content-center align-items-center flex-column">
                             <i class="fas fa-exchange-alt fa-5x text-muted"></i>
                             <h3 class="text-muted"><?= __('admin.no_data_found') ?></h3>
                         </div>
                     </div>
                     <?php }else {?>

                     <div class="table-responsive b-0" data-pattern="priority-columns">
                           <div class="col-12">
                              <table id="table-coupons" class="table table-striped">
                                 <thead>
                                    <tr>
                                       <th><?= __('admin.coupon_name'); ?></th>
                                       <th><?= __('admin.count_product_use'); ?></th>
                                       <th><?= __('admin.uses_total'); ?></th>
                                       <th><?= __('admin.code'); ?></th>
                                       <th><?= __('admin.discount'); ?></th>
                                       <th><?= __('admin.date_start'); ?></th>
                                       <th><?= __('admin.date_end'); ?></th>
                                       <th><?= __("admin.status") ?></th>
                                       <th><?= __("admin.actions") ?></th>
                                    </tr>
                                 </thead>
                                 <tbody>
                                    <?php foreach($coupons as $coupon){ ?>
                                    <tr>
                                       <td><?= $coupon['name'] ?></td>
                                       <td><?= (int)$coupon['product_count'] .' / '. (int)$coupon['count_coupon'] ?></td>
                                       <td><?= $coupon['uses_total'] ?></td>
                                       <td><?= $coupon['code'] ?></td>
                                       <td><?= $coupon['type']=="P" ? getDecimalNumberFormat($coupon['discount'],$_SESSION['userDecimalPlace']).' %' : c_format($coupon['discount']) ?></td>
                                       <td><?= dateGlobalFormat($coupon['date_start']) ?></td>
                                       <td><?= dateGlobalFormat($coupon['date_end']) ?></td>
                                       <td><?= $coupon['status'] == '1' ? __("admin.enabled") : __("admin.disabled") ?></td>
                                       <td>
                                          <a href="<?= base_url('admincontrol/coupon_manage/'.$coupon['coupon_id'])  ?>" class="btn btn-primary edit-button" id="<?= $coupon['id'] ?>"><?= __("admin.edit") ?></a>
                                          <a href="<?= base_url('admincontrol/coupon_delete/'.$coupon['coupon_id'])  ?>" class="btn btn-danger delete-button" id="<?= $coupon['id'] ?>"><?= __("admin.delete") ?></a>
                                       </td>
                                    </tr>
                                    <?php } ?>
                                 </tbody>
                              </table>
                           </div>
                     </div>
                     <?php } ?>
                  </div>
               </div>
               <div role="tabpanel" class="tab-pane" id="form_tab">
                  <div class="table-rep-plugin">
                     <div class="pull-right mb-2 form-tab-main">
                        <button style="display:none;" type="button" class="btn btn-info" name="deletebuttonform" id="deletebuttonform" value="<?= __('admin.save_exit') ?>" onclick="deleteformfunc('deleteAllforms');"><?= __('admin.delete_products') ?></button>
                        <a class="btn btn-primary" href="<?= base_url('admincontrol/form_manage/')  ?>"><?= __('admin.add_new'); ?></a>
                     </div>
                     
                      <?php if ($forms == null) {?>
                     <div class="text-center">
                         <div class="d-flex justify-content-center align-items-center flex-column">
                             <i class="fas fa-exchange-alt fa-5x text-muted"></i>
                             <h3 class="text-muted"><?= __('admin.no_data_found') ?></h3>
                         </div>
                     </div>
                           <?php } else { ?>
                        <div class="table-responsive b-0" data-pattern="priority-columns">
                           <form method="post" name="deleteAllforms" id="deleteAllforms" action="<?php echo base_url();?>admincontrol/deleteAllforms">
                              <table class="table table-striped">
                                 <thead>
                                    <tr>
                                       <th><input name="checkbox[]" type="checkbox" value="" onclick="checkAllForm(this)"></th>
                                       <th ><?= __('admin.form_title'); ?></th>
                                       
                                       <th><?= __('admin.vendor'); ?></th>
                                       <th><?= __('admin.coupon_code'); ?></th>
                                       <th><?= __('admin.coupon_use'); ?></th>
                                       <th><?= __('admin.sales_commission'); ?></th>
                                       <th><?= __('admin.clicks_commission'); ?>n</th>
                                       <th><?= __('admin.total_commission'); ?></th>
                                       <th><?= __('admin.status'); ?></th>
                                       <th><?= __('admin.action'); ?></th>
                                    </tr>
                                 </thead>
                                 <tbody>
                                    <?php 
                                       $form_setting = $this->Product_model->getSettings('formsetting');
                                    ?>
                                    <?php foreach($forms as $form){ ?>
                                       <tr>
                                          <td ><input name="checkbox[]" type="checkbox" id="check<?php echo $form['form_id'];?>" value="<?php echo $form['form_id'];?>" onclick="checkonly(this,'check<?php echo $form['form_id'];?>')"></td>
                                          <td>
                                             <?= $form['title'] ?>
                                             <div><small>
                                                <a href="<?= $form['public_page'] ?>" target='_black'><?= __('admin.public_page'); ?></a>
                                                </small>
                                             </div>
                                             <?php 
                                                if($form['form_recursion_type']){
                                                      if($form['form_recursion_type'] == 'custom'){
                                                         if($form['form_recursion'] != 'custom_time'){
                                                            echo '<b>'. __("admin.recurring") .'</b> : ' . $form['form_recursion'];
                                                         } else {
                                                            echo '<b>'. __("admin.recurring") .'</b> : '. timetosting($form['recursion_custom_time']);
                                                         }
                                                      } else{
                                                      if($form_setting['form_recursion'] == 'custom_time' ){
                                                            echo '<b>'. __("admin.recurring") .'</b> : '. timetosting($form_setting['recursion_custom_time']);
                                                      } else {
                                                         echo '<b>'. __("admin.recurring") .'</b> : '. $form_setting['form_recursion'];
                                                      }
                                                      }
                                                   }
                                             ?>
                                          </td>
                                          <td><?= $form['firstname'] ? $form['firstname'] ." ". $form['lastname'] : __("admin.admin") ?></td>
                                          <td><?= $form['coupon_code'] ? $form['coupon_code'] : 'N/A' ?></td>
                                          <td><?= ($form['coupon_name'] ? $form['coupon_name'] : 'N/A').' / '.$form['count_coupon'] ?></td>
                                          <td><?= (int)$form['count_commission'].' / '.c_format($form['total_commission']) ?><br/><br/>
                                             <b><?= __('admin.view') ?> / <?= __('admin.ratio') ?></b><br/>
                                             <?php 
                                             $ordercountratio=0;
                                             if($form['view_statistics']>0)
                                                $ordercountratio=$form['count_commission']*100/$form['view_statistics'];
                                             $ordercountratio = is_float($ordercountratio) ==1 ? number_format((float)$ordercountratio, 2, '.', '') : $ordercountratio ;
                                             echo $form['view_statistics'] . ' / '. $ordercountratio .'%';
                                             ?>
                                          </td>
                                          <td><?= (int)$form['commition_click_count'].' / '.c_format($form['commition_click']); ?><br/><br/>
                                             <b><?= __('admin.view') ?> / <?= __('admin.ratio') ?></b><br/>
                                                <?php 
                                                $ordercountratio=0;
                                                $comissionclickcount=(int)$form['commition_click_count']  ;
                                                if($form['view_statistics']>0)
                                                   $ordercountratio=$comissionclickcount*100/$form['view_statistics'];
                                                $ordercountratio = is_float($ordercountratio) ==1 ? number_format((float)$ordercountratio, 2, '.', '') : $ordercountratio;
                                                echo $form['view_statistics'] . ' / '. $ordercountratio.'%';
                                                ?>
                                          </td>
                                          <td><?= c_format($form['total_commission']+$form['commition_click']); ?></td>
                                          <td><?= form_status($form['status']); ?></td>
                                          <td>
                                             <a href="<?= base_url('admincontrol/form_manage/'.$form['form_id'])  ?>" class="btn ml-0 btn-primary edit-button" id="<?= $lang['id'] ?>"><?= __("admin.edit") ?></a>
                                             <button data-href="<?= base_url('admincontrol/form_delete/'.$form['form_id'])  ?>" class="btn ml-0 btn-danger delete-form-button" id="<?= $lang['id'] ?>"><?= __("admin.delete") ?></button>
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
               <div role="tabpanel" class="tab-pane" id="form_coupons_tab">
                  <div class="table-rep-plugin">
                     <div class="pull-right mb-2">
                        <a class="btn btn-primary" href="<?= base_url('admincontrol/form_coupon_manage/')  ?>"><?= __('admin.add_new'); ?></a>
                     </div>
                     <?php if ($form_coupons == null) {?>
                     <div class="text-center">
                         <div class="d-flex justify-content-center align-items-center flex-column">
                             <i class="fas fa-exchange-alt fa-5x text-muted"></i>
                             <h3 class="text-muted"><?= __('admin.no_data_found') ?></h3>
                         </div>
                     </div>
                             <?php }else {?>
                                <div class="table-responsive b-0" data-pattern="priority-columns">
                           <table class="table  table-striped">
                              <thead>
                                 <tr>
                                    <th ><?= __('admin.form_coupon_name'); ?></th>
                                    <th><?= __('admin.code'); ?></th>
                                    <th><?= __('admin.discount'); ?></th>
                                    <th><?= __('admin.date_start'); ?></th>
                                    <th><?= __('admin.date_end'); ?></th>
                                    <th><?= __("admin.status") ?></th>
                                    <th></th>
                                 </tr>
                              </thead>
                              <tbody>
                                 <?php foreach($form_coupons as $form_coupon){ ?>
                                    <tr>
                                       <td><?= $form_coupon['name'] ?></td>
                                       <td><?= $form_coupon['code'] ?></td>
                                       <td><?= $form_coupon['type']=="P" ? getDecimalNumberFormat($form_coupon['discount'],$_SESSION['userDecimalPlace']).' %' : c_format($form_coupon['discount']) ?></td>
                                       <td><?= $form_coupon['date_start'] ?></td>
                                       <td><?= $form_coupon['date_end'] ?></td>
                                       <td><?= $lang['status'] == '0' ? __("admin.enabled") : __("admin.disabled") ?></td>
                                       <td>
                                          <a href="<?= base_url('admincontrol/form_coupon_manage/'.$form_coupon['form_coupon_id'])  ?>" class="btn btn-primary edit-button" id="<?= $lang['id'] ?>"><?= __("admin.edit") ?></a>
                                          <button data-href="<?= base_url('admincontrol/form_coupon_delete/'.$form_coupon['form_coupon_id'])  ?>" class="btn btn-danger delete-form-button" id="<?= $lang['id'] ?>"><?= __("admin.delete") ?></button>
                                       </td>
                                    </tr>
                                 <?php } ?>
                              </tbody>
                           </table>
                        </div>
                     <?php } ?>
                  </div>
               </div>
               <div role="tabpanel" class="tab-pane" id="review_tab">
                  <div class="table-rep-plugin">
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
                                    <button class="btn btn-primary"><?= __('admin.total_review') ?> : <span id="total_review"></span></button>
                                  </div>
                              </div>
                              <div class="col-3">
                              </div> 
                              <div class="col-3">
                                 <div class="pull-right mb-2">
                                    <a class="btn btn-primary" href="<?= base_url('admincontrol/manage_review/')  ?>"><?= __('admin.add_new'); ?></a>
                                     <a class="btn btn-primary" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#manageBulkReviews"><?= __('user.manage_bulk_reviews') ?></a>
                                 </div>
                              </div>
                           </div>
                        </form>
                     </div>
                     <div class="table-responsive b-0" data-pattern="priority-columns">
                           <div class="col-12">
                              <table id="table-review" class="table table-striped">
                                 <thead>
                                    <tr>
                                       <th><?= __('admin.image'); ?></th>
                                       <th><?= __('admin.firstname'); ?></th>
                                       <th><?= __('admin.lastname'); ?></th>
                                       <th><?= __('admin.product_name'); ?></th>
                                       <th><?= __('admin.review'); ?></th>
                                       <th><?= __('admin.rating'); ?></th>
                                       <th><?= __('admin.datetime'); ?></th> 
                                       <th><?= __("admin.actions") ?></th>
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
 
<div id="manageBulkReviews" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?= __('admin.manage_bulk_reviews') ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

<div class="modal-body">
    <div class="row g-4 mb-4 px-3">
        <div class="col-12">
            <div class="row g-4">
                <div class="col-6 text-center">
                    <button class="btn btn-lg btn-success text-center export-reviews-xml-btn w-100"><?= __('admin.export_reviews_xml') ?></button>
                </div>
                <div class="col-6 text-center">
                    <button class="btn btn-lg btn-success text-center export-reviews-structure-xml-btn w-100"><?= __('admin.export_structure_xml_only') ?></button>
                </div>
            </div>
            <hr>
            <ul class="nav nav-pills flex-column flex-sm-row mb-4" id="TabsNav">
                <li class="nav-item flex-sm-fill text-sm-center">
                    <a class="nav-link active" id="import-review-file-tab" data-bs-toggle="tab" href="#import_review_file_tab_" role="tab" aria-controls="import_review_file_tab_" aria-selected="true"><?= __('admin.import_from_file') ?></a>
                </li>
                <li class="nav-item flex-sm-fill text-sm-center">
                    <a class="nav-link" id="import-review-link-tab" data-bs-toggle="tab" href="#import_review_link_tab" role="tab" aria-controls="import_review_link_tab" aria-selected="false"><?= __('admin.import_from_url') ?></a>
                </li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane fade show active p-4" id="import_review_file_tab_" role="tabpanel" aria-labelledby="import-review-file-tab">
                    <form id="bulk_reviews_form" class="text-center">
                        <div class="input-group mb-3">
                            <input type="file" class="form-control" name="file" id="customFile" aria-describedby="customFileLabel">
                            <label class="input-group-text" for="customFile"><?= __('admin.upload_xml_file_for_bulk_review_manage') ?></label>
                        </div>
                        <button id="bulk_reviews_form_btn" type="submit" class="btn btn-lg btn-success"><?= __('admin.import_reviews') ?></button>
                    </form>
                </div>
                <div class="tab-pane fade p-4 m-2" id="import_review_link_tab" role="tabpanel" aria-labelledby="import-review-link-tab">
                    <form id="bulk_reviews_form_url" class="text-center">
                        <div class="input-group mb-3">
                            <input name="txt_review_xmlurl" id="txt_review_xmlurl" class="form-control textxmlurl" type="text" placeholder="<?= __('admin.enter_xml_url_for_bulk_review_manage') ?>">
                        </div>
                        <button id="bulk_reviews_form_url_btn" type="submit" class="btn btn-lg btn-success"><?= __('admin.import_reviews') ?></button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>



        </div>
    </div>
</div>

               </div>   
            </div>
         </div>
      </div>
   </div>
</div>
</div>

<div id="manageBulkReviewsConfirmation" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title"><?= __('admin.manage_bulk_reviews_confirmation') ?></h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body" style="max-height:350px; overflow-y:auto;">
            <!-- Content goes here -->
        </div>
        <div class="modal-footer">
            <button class="btn btn-lg btn-success text-center import-reviews-confirm"><?= __('admin.confirm_product_image') ?></button>
            <button class="btn btn-lg btn-secondary text-center" data-bs-dismiss="modal"><?= __('admin.cancel') ?></button>
        </div>
    </div>
  </div>
</div>


<div id="manageBulkReviewsResult" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title"><?= __('admin.manage_bulk_reviews_result') ?></h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body" style="max-height:350px; overflow-y:auto;">
            <!-- Content goes here -->
        </div>
        <div class="modal-footer">
            <button class="btn btn-lg btn-success text-center" onclick="window.location.reload()"><?= __('admin.ok') ?></button>
        </div>
    </div>
  </div>
</div>


<?= $social_share_modal ?>
<script type="text/javascript" async="">

    $('.product_tab_option').on('click', function(){
        $(".product-options").show();
    });

    $('.product_coupons_tab_option').on('click', function(){
        $(".product-options").hide();
    });
   
    $temp_import_product_data = null;
   
    $('#bulk_products_form_btn').on('click', function(e){ 
        e.preventDefault();
        $("#bulk_products_form .alert-danger").remove();
        if($('#bulk_products_form input[name="file"]').val()) {
            $this = $(this);
            var fd = new FormData(document.getElementById("bulk_products_form"));
   
            $.ajax({
                url: '<?= base_url('admincontrol/bulkProductImport'); ?>',  
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
           $("#bulk_products_form .custom-file").after('<div class="alert alert-danger"><?= __('admin.please_select_excel_file') ?></div>');
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
                url: '<?= base_url('admincontrol/bulkProductImportFromUrl'); ?>',  
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
           $("#bulk_products_form_url .custom-file").after('<div class="alert alert-danger"><?= __('admin.please_enter_xml_url') ?></div>');
        }
    });

    $('#manageBulkProductsConfirmation .import-products-confirm').on('click', function(e){
        e.preventDefault();
        if($('#manageBulkProductsConfirmation textarea[name="product_for_import"]').val()) {
            $this = $(this);
            var data = new FormData();
            data.append( 'products', $('#manageBulkProductsConfirmation textarea[name="product_for_import"]').val());
            $.ajax({
                url: '<?= base_url('admincontrol/bulkProductImportConfirm'); ?>',  
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
            $("#bulk_products_form .custom-file").after('<div class="alert alert-danger"><?= __('admin.please_select_excel_file') ?></div>');
        }
    });
   
    $(".show-more").on('click',function(){
        $(this).parents("tfoot").remove();
        $("#product-list tr.d-none").hide().removeClass('d-none').fadeIn();
    });
   
    $(".delete-button").on('click',function(){
        if(!confirm("<?= __('admin.are_you_sure') ?>")) return false;
    });

    $(document).ready(function(){
      $('.delete-form-button').on('click',function(){
         var r = confirm("<?= __("admin.delete_form_confirmation") ?>");
         if (r == true) {         
            location = $(this).data("href");
         }
         return false;
      })
    })

    $(".toggle-child-tr").on('click',function(){
        $tr = $(this).parents("tr");
        $ntr = $tr.next("tr.detail-tr");
       
        if($ntr.css("display") == 'table-row'){
            $ntr.hide();
            $(this).find("i").attr("class","fa fa-plus");
        }else{
            $(this).find("i").attr("class","fa fa-minus");
            $ntr.show();
        }
    })
   
    function checkAll(bx) {
        var cbs = document.getElementsByTagName('input');
            if(bx.checked)
        {
            document.getElementById('deletebutton').style.display = 'block';
        } else {
            document.getElementById('deletebutton').style.display = 'none';
        }
        for(var i=0; i < cbs.length; i++) {
            if(cbs[i].type == 'checkbox') {
                cbs[i].checked = bx.checked;
            }
        }
    }
    
    function checkAllForm(bx) {
      var cbs = document.getElementsByTagName('input');
      if(bx.checked)
      {
         document.getElementById('deletebuttonform').style.display = 'block';
         } else {
         document.getElementById('deletebuttonform').style.display = 'none';
      }
      for(var i=0; i < cbs.length; i++) {
         if(cbs[i].type == 'checkbox') {
            cbs[i].checked = bx.checked;
         }
      }
    }

    function checkonly(bx,checkid) {
        if($(".list-checkbox:checked").length){
            $('#deletebutton').show();
        } else {
            $('#deletebutton').hide();
        }
    }
   
    function deleteuserlistfunc(formId){
        if(! confirm("<?= __('admin.are_you_sure') ?>")) return false;
   
        $('#'+formId).submit();
    }
   
    function deleteformfunc(formId){
      if(! confirm("<?= __('admin.are_you_sure') ?>")) return false;

      $('#'+formId).submit();
    }

    $("#filter-form").on("submit",function(){
        getPage('<?= base_url("admincontrol/listproduct_ajax/") ?>/1');
        return false;
    })

    $(".select-category, .select-vendor").on("change",function(){
        $("#filter-form").submit();
    })
   
    function getPage(url){
       var category_id = $('.select-category').find(":selected").val();
       var seller_id = $('.select-vendor').find(":selected").val();
       $this = $(this);
       $.ajax({
            url:url,
            type:'POST',
            dataType:'json',
            data:$("#filter-form").serialize(),
            beforeSend:function(){$this.btn("loading");},
            complete:function(){$this.btn("reset");},
            success:function(json){
               if(json['view']){
                  $("#tech-companies-1 tbody").html(json['view']);
                  $("#tech-companies-1").show();
               } else {
                  $(".empty-div").removeClass("d-none");
                  $("#tech-companies-1").hide();
               }
        
               $("#tech-companies-1 .pagination-td").html(json['pagination']);
            },
       });
    }
   
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
            url:'<?= base_url("admincontrol/exportproduct/") ?>',
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
            url:'<?= base_url("admincontrol/exportproductXML/") ?>',
            type:'POST',
            dataType:'json',
            data:{structure_only:structure_only},
                beforeSend:function(){thatBtn.btn("loading");},
                complete:function(){thatBtn.btn("reset");},
                success:function(json){
            if(json['download'])
            {
               if(structure_only==0)
                  window.location.href="downloadprodcutxmlfile/"; 
               else
                 window.location.href="downloadprodcutxmlstructurefile/";   
   
            }
        },
        });
    }
     
   
    getPage('<?= base_url("admincontrol/listproduct_ajax/") ?>/1');
        $("#tech-companies-1 .pagination-td").delegate("a","click",function(){
        getPage($(this).attr("href"));
        return false;
    })
   
    function closePopup(){
       $('.popupbox').hide();
       $('#overlay').hide();
    }
   function generateCode(affiliate_id){
        $('.popupbox').show();
        $('#overlay').show();
        $('.modalpopup-body').load('<?php echo base_url();?>admincontrol/generateproductcode/'+affiliate_id);
        $('.popupbox').ready(function () {
            $('.backdrop, .box').animate({
            'opacity': '.50'
            }, 300, 'linear');
            $('.box').animate({
                'opacity': '1.00'
            }, 300, 'linear');
            $('.backdrop, .box').css('display', 'block');
        });
   }
   
   $(document).delegate(".delete-product",'click',function(){
       if(! confirm("<?= __('admin.are_you_sure') ?>")) return false;
       window.location = $("#deleteAllproducts").attr("action") + "?delete_id=" + $(this).attr("data-id");
   })

   $("#filter-form-review").on("submit",function(){
      var urlreview='<?= base_url("admincontrol/listreviews_ajax/")?>';
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
       getReviews('<?= base_url("admincontrol/listreviews_ajax/")?>');
    });

    $('#bulk_reviews_form_url_btn').on('click', function(e){ 
        e.preventDefault();
 
        $("#bulk_reviews_form_url .alert-danger").remove();
        if($('#txt_review_xmlurl').val()!="") 
        {
            $this = $(this);
            var fd = new FormData(document.getElementById("bulk_reviews_form_url"));
   
            $.ajax({
                url: '<?= base_url('admincontrol/bulkReviewImportFromUrl'); ?>',  
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
                url: '<?= base_url('admincontrol/bulkReviewsImport'); ?>',  
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
                url: '<?= base_url('admincontrol/bulkReviewImportConfirm'); ?>',  
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
            url:'<?= base_url("admincontrol/exportReviewXML/") ?>',
            type:'POST',
            dataType:'json',
            data:{structure_only:structure_only},
                beforeSend:function(){thatBtn.btn("loading");},
                complete:function(){thatBtn.btn("reset");},
                success:function(json){
            if(json['download'])
            {
               if(structure_only==0)
                  window.location.href='<?= base_url('admincontrol/downloadproductreviewxmlfile'); ?>'; 
               else
                 window.location.href='<?= base_url('admincontrol/downloadproductreviewxmlstructurefile'); ?>';   
   
            }
        },
        });
    }
</script>