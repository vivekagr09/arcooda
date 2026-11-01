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
<div class="row">
	<div class="col-12">
		<div class="card">
			<div class="card-header bg-secondary text-white">
					<h5 class="pull-left"><?= __('admin.product_reviews') ?></h5>
			</div>
			<div class="card-body">
				<div class="filter">
					<form>
						<div class="row">
							<div class="col-sm-6">
								<div class="form-group">
									<label class="control-label"><?= __('admin.category') ?></label>
									<select name="category_id" class="form-control">
										<?php $selected = isset($_GET['category_id']) ? $_GET['category_id'] : ''; ?>
										<option value=""><?= __('admin.all_category') ?></option>
										<?php foreach ($categories as $key => $value) { ?>
											<option <?= $selected == $value['id'] ? 'selected' : '' ?> value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
										<?php } ?>
									</select>
								</div>
							</div>
							<div class="col-sm-5">
								<div class="form-group">
									<label class="control-label"><?= __('admin.vendor') ?></label>
									<select name="seller_id" class="form-control">
										<?php $selected = isset($_GET['seller_id']) ? $_GET['seller_id'] : ''; ?>
										<option value=""><?= __('admin.all_vendors') ?></option>
										<?php foreach ($vendors as $key => $value) { ?>
											<option <?= $selected == $value['id'] ? 'selected' : '' ?> value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
										<?php } ?>
									</select>
								</div>
							</div>		
							<div class="col-sm-1">
								<div class="form-group">
									<label class="control-label d-block">&nbsp;</label>
									<button type="submit" class="btn btn-primary"><?= __('admin.search') ?></button>
									<button style="display:none;" type="button" class="btn mb-2 btn-danger" name="deletebutton" id="deletebutton" value="Save & Exit" onclick="deleteuserlistfunc('deleteAllproducts');"><?= __('admin.delete_products') ?></button>
								</div>
							</div>	
						</div>
					</form>
				</div>

				
				
			    <?php if ($productlist == null) {?>
                    <div class="text-center mt-5">
					 <div class="d-flex justify-content-center align-items-center flex-column">
						 <i class="fas fa-exchange-alt fa-5x text-muted"></i>
						 <h3 class="text-muted"><?= __('admin.no_data_found') ?></h3>
					 </div>
					</div>
                <?php } else { ?>
                	<div class="table-responsive b-0" data-pattern="priority-columns">
						<form method="post" name="deleteAllproducts" id="deleteAllproducts" action="<?php echo base_url('admincontrol/deleteAllproducts'); ?>">
							<table id="tech-companies-1" class="table table-striped table-white-space-normal">
								<thead class="product-vendor">
									<tr>
										<th><input name="product[]" type="checkbox" value="" onclick="checkAll(this)"></th>
										<th><?= __('admin.product_name') ?></th>
										<th width="180px"><?= __('admin.featured_image') ?></th>
										<th><?= __('admin.vendor_name') ?></th>
										<th><?= __('admin.price') ?></th>
										<th><?= __('admin.sku') ?></th>
										<th><?= __('admin.get_ncommission') ?></th>
										<th width="180px"><?= __('admin.sales_/_commission') ?></th>
										<th width="180px"><?= __('admin.clicks_/_commission') ?></th>
										<th><?= __('admin.total') ?></th>
										<th><?= __('admin.display') ?></th>
										<th><?= __('admin.action') ?></th>
									</tr>
								</thead>
								<tbody>
									<?php 
										$pro_setting = $this->Product_model->getSettings('productsetting');
										$vendor_setting = $Product_model->getSettings('vendor');
									?>

									<?php foreach($productlist as $product){ ?>
										<?php 
											$productLink = base_url('store/'. base64_encode($userdetails['id']) .'/product/'.$product['product_slug'] ).'?preview=1';
										?>
										<tr>
											<td class="text-center">
												<input class='list-checkbox' name="checkbox[]" type="checkbox" id="check<?php echo $product['product_id'];?>" value="<?php echo $product['product_id'];?>" onclick="checkonly(this,'check<?php echo $product['product_id'];?>')">
												<?php if($product['product_type'] == 'downloadable'){ ?>
													<img src="<?= base_url('assets/images/download.png') ?>" width="20px" class='d-block'>
												<?php } ?>
											</td>
											<td>
												<div class="tooltip-copy">
													<span><?php echo $product['product_name'];?></span>
													<div> <small>
														<a target="_blank" href="<?= $productLink ?>"><?= __('admin.public_page') ?></a>
													</small></div>
												</div>
											</td>
											<td>
												<div class="tooltip-copy">
													<img width="50px" height="50px" src="<?php echo base_url('assets/images/product/upload/thumb/'. $product['product_featured_image']) ?>" ><br>
												</div>
											</td>
											<td class="txt-cntr"><?php echo $product['seller_username'] ? $product['seller_username'] : __('admin.admin'); ?></td>
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
																$commnent_line .= __('admin.warning_default_commission_not_set');
															}
															else if($seller_setting->affiliate_sale_commission_type == 'percentage'){
																$commnent_line .= __('admin.percentage').' : '. (float)$seller_setting->affiliate_commission_value .'%';
															}
															else if($seller_setting->affiliate_sale_commission_type == 'fixed'){
																$commnent_line .= __('admin.fixed').' : '. c_format($seller_setting->affiliate_commission_value);
															}
														} else if($seller->affiliate_sale_commission_type == 'percentage'){
															$commnent_line .= __('admin.percentage').' : '. (float)$seller->affiliate_commission_value .'%';
														} else if($seller->affiliate_sale_commission_type == 'fixed'){
															$commnent_line .= __('admin.fixed').' : '. c_format($seller->affiliate_commission_value);
														} 

														echo '<b>'.__('admin.sale').'</b> : ' .$commnent_line;

														$commnent_line = "";
														if($seller->affiliate_click_commission_type == 'default'){ 
															$commnent_line .= c_format($seller_setting->affiliate_click_amount) ." ".__('admin.per')." ". (int)$seller_setting->affiliate_click_count ." ".__('admin.clicks');
														} else{
															$commnent_line .= c_format($seller->affiliate_click_amount) ." Per ". (int)$seller->affiliate_click_count ." ".__('admin.clicks');
														} 
														echo '<br><b>'.__('admin.click').'</b> : ' .$commnent_line;


														$commnent_line = '';
														if($seller->admin_click_commission_type == '' || $seller->admin_click_commission_type == 'default'){
															$commnent_line =  c_format($vendor_setting['admin_click_amount']) ." ".__('admin.per')." ". (int)$vendor_setting['admin_click_count'] ." ".__('admin.clicks');
														} else{ 
															$commnent_line =  c_format($seller->admin_click_amount) ." ".__('admin.per')." ". (int)$seller->admin_click_count ." ".__('admin.clicks');
														} 

														echo '<br><b>'.__('admin.admin_click').'</b> : ' .$commnent_line;

														$commnent_line = '';
														if($seller->admin_sale_commission_type == '' || $seller->admin_sale_commission_type == 'default'){ 
															if($vendor_setting['admin_sale_commission_type'] == ''){
																$commnent_line .= __('admin.warning_default_commission_not_set');
															}
															else if($vendor_setting['admin_sale_commission_type'] == 'percentage'){
																$commnent_line .= ' '. (float)$vendor_setting['admin_commission_value'] .'%';
															}
															else if($vendor_setting['admin_sale_commission_type'] == 'fixed'){
																$commnent_line .= ' '. c_format($vendor_setting['admin_commission_value']);
															}
														} else if($seller->admin_sale_commission_type == 'percentage'){
															$commnent_line .= ''. (float)$seller->admin_commission_value .'%';
														} else if($seller->admin_sale_commission_type == 'fixed'){
															$commnent_line .= ''. c_format($seller->admin_commission_value);
														} else {
															$commnent_line .= __('admin.warning_commission_not_set');
														} 

														echo '<br><b>'.__('admin.admin_sale').'</b> : ' .$commnent_line;
													} else {
												?>

													<b><?= __('admin.sale'); ?></b> : 
													<?php

														if($product['product_commision_type'] == 'default'){
															if($default_commition['product_commission_type'] == 'percentage'){
																echo $default_commition['product_commission']. "%";
															}
															else
															{
																echo c_format($default_commition['product_commission']);
															}
														}
														else if($product['product_commision_type'] == 'percentage'){
															echo $product['product_commision_value']. "%";
														}
														else{
															echo c_format($product['product_commision_value']);
														}
													?>
													
													<br> <b><?= __('admin.click'); ?></b> :
													<?php
												    	if($product['product_click_commision_type'] == 'default'){
															echo "<small>{$default_commition['product_noofpercommission']} ".__('admin.click_for')."  "; 	
															echo c_format($default_commition['product_ppc']);
															echo "</small>";
														}
														else{
															echo "<small>{$product['product_click_commision_per']} ".__('admin.click_for')." : ";
															echo c_format($product['product_click_commision_ppc']) ."</small>";
														}
													?>
												<?php } ?>

												<?php 
													if($product['product_recursion_type']){
										           		if($product['product_recursion_type'] == 'custom'){
										           			if($product['product_recursion'] != 'custom_time'){
										           				echo '<b>'. __('admin.recurring') .' </b> : ' .  __('admin.'.$product['product_recursion']);
										           			} else {
										           				echo '<b>'. __('admin.recurring') .' </b> : '. timetosting($product['recursion_custom_time']);
										           			}
										           		} else{
															if($pro_setting['product_recursion'] == 'custom_time' ){
									           					echo '<b>'. __('admin.recurring') .' </b> : '. timetosting($pro_setting['recursion_custom_time']);
															} else {
																echo '<b>'. __('admin.recurring') .' </b> : '.  __('admin.'.$pro_setting['product_recursion']);
															}
										           		}
										           	}
												?>
											</td>
											<td class="txt-cntr">
												<?php echo $product['order_count'];?> / 
												<?php echo c_format($product['commission']) ;?>
											</td>
											<td class="txt-cntr">
												<?php echo (int)$product['commition_click_count'] + (int)$product['commition_click_count_admin'];?> / 
												<?php echo c_format($product['commition_click']) ;?>
											</td>
											<td class="txt-cntr">
												<?php echo
													c_format((float)$product['commition_click'] + (float)$product['commission']);
												?>
											</td>
											<td class="txt-cntr">
												<?= product_status($product['product_status']) ?>	
											</td>
											<td class="txt-cntr">
												<a class="btn btn-sm btn-primary review-page ml-0 mb-2" onclick="return confirm(<?= __('admin.are_you_sure_to_edit'); ?>);" href="<?php echo base_url();?>admincontrol/updateproduct/<?php echo $product['product_id'];?>"><i class="fa fa-edit cursors" aria-hidden="true"></i></a>
												<a class="btn btn-sm btn-primary review-page ml-0 mb-2" href="<?php echo base_url('admincontrol/productupload/'. $product['product_id']);?>"><i class="fa fa-image cursors"></i></a>
                                    			<a class="btn btn-sm btn-primary review-page ml-0" href="<?php echo base_url('admincontrol/videoupload/'. $product['product_id']);?>"><i class="fa fa-youtube cursors"></i></a>
												<button class="btn btn-danger review-page btn-sm delete-product ml-0" type="button" data-id="<?= $product['product_id'] ?>"> <i class="fa fa-trash"></i> </button>
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
</div>
<script type="text/javascript" async="">

	$(".show-more").on('click',function(){
		$(this).parents("tfoot").remove();
		$("#product-list tr.d-none").hide().removeClass('d-none').fadeIn();
	});

	$(".delete-button").on('click',function(){
		if(!confirm("<?= __('admin.are_you_sure'); ?>")) return false;
	});
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
	function checkonly(bx,checkid) {
		if($(".list-checkbox:checked").length){
			$('#deletebutton').show();
		} else {
			$('#deletebutton').hide();
		}
	}
	function deleteuserlistfunc(formId){
		if(! confirm("<?= __('admin.are_you_sure'); ?>")) return false;

		$('#'+formId).submit();
	}

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

	$(".delete-product").on('click',function(){
		if(! confirm("<?= __('admin.are_you_sure'); ?>")) return false;
		window.location = $("#deleteAllproducts").attr("action") + "?delete_id=" + $(this).attr("data-id");
	})
</script>			