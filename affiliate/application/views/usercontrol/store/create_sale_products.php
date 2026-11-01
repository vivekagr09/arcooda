<link rel="stylesheet" type="text/css" href="<?= base_url("assets/plugins/ui/jquery-ui.min.css") ?>">
<script type="text/javascript" src="<?= base_url('assets/plugins/ui/jquery-ui.min.js') ?>"></script>
<link rel="stylesheet" type="text/css" href="<?= base_url("assets/plugins/select2/select2.min.css") ?>">
<script type="text/javascript" src="<?= base_url('assets/plugins/select2/select2.full.min.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/plugins/datatable/moment.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/plugins/datatable/daterangepicker.min.js') ?>"></script>
<link rel="stylesheet" type="text/css" href="<?= base_url('assets/plugins/datatable/daterangepicker.css') ?>" />

<style type="text/css">
	.btn_active {
		background-color: #8f9499 !important;
		border-color: #8f9499 !important;
	}
</style>

		<form class="form-horizontal" method="post" action=""  enctype="multipart/form-data" id="form_form">
			<input type="hidden" name="product_id" value="<?php echo $product['product_id'] ?>" />
					<div class="card">
						<div class="card-header bg-blue-payment change-category-color">
			                <div class="card-title-white pull-left m-0"><?= __('user.product_details') ?></div>
            			</div>
						<div class="card-body">
							<fieldset class="custom-design mb-2">
								<legend><?= __('user.product_type'); ?></legend>
								<div class="row">
									<div style="background: #fc836e; color:  #fff; margin-left: 10px;" class="col-md-3 btn proType <?= ($product['product_type'] == 'virtual' || $product['product_type'] == '') ? 'btn_active' : ''?>" data-value="virtual">
										<label class="radio-inline">
											<input class="invisible" type="radio" name="product_type" value="virtual" <?= ($product['product_type'] == 'virtual' || $product['product_type'] == '') ? 'checked="checked"' : '' ?> > <?= __('user.virtual_product'); ?>
										</label>
									</div>
									<div style="background: #fc836e; color:  #fff; margin-left: 10px;" class="col-md-3 btn proType <?= ($product['product_type'] == 'downloadable') ? 'btn_active' : '' ?>" data-value="downloadable">
										<label class="radio-inline">
											<input class="invisible" type="radio" name="product_type" value="downloadable" <?= ($product['product_type'] == 'downloadable') ? 'checked="checked"' : '' ?> > <?= __('user.downloadable_product'); ?>
										</label>
									</div>
								</div>
							</fieldset>
							<div class="form-group">
								<label class="col-form-label"><?= __('user.product_promotion_url') ?></label>
								<div>
									<input placeholder="<?= __('user.enter_product_promotion_url') ?>" name="product_url" value="<?php echo $product['product_url']; ?>" class="form-control" type="text" />
								</div>
							</div>

							<div class="row">
								<div class="col-sm-8">
									<div class="form-group">
										<label class="col-form-label"><?= __('user.product_name') ?></label>
										<div>
											<input placeholder="<?= __('user.enter_your_product_name') ?>" name="product_name" value="<?php echo $product['product_name']; ?>" class="form-control" type="text">
										</div>
									</div>
									<div class="form-group">
										<label class="col-form-label"><?= __('user.product_sku') ?> </label>
										<div>
											<input placeholder="<?= __('user.enter_your_product_sku') ?>" name="product_sku" id="product_sku" class="form-control" value="<?php echo $product['product_sku']; ?>" type="text">
										</div>
									</div>
									<div class="form-group">
										<label class="col-form-label"><?= __('user.product_msrp') ?></label>
										<div>
											<input placeholder="<?= __('user.product_msrp_placeholder') ?>" name="product_msrp" class="form-control" value="<?php echo $product['product_msrp']; ?>" type="number">
										</div>
									</div>
									<div class="form-group">
										<label class="col-form-label"><?= __('user.product_price') ?></label>
										<div>
											<input placeholder="<?= __('user.enter_your_product_price') ?>" name="product_price" class="form-control" value="<?php echo $product['product_price']; ?>" type="number">
										</div>
									</div>
									<div class="form-group">
										<label class="col-form-label"><?= __('user.product_quantity') ?></label>
										<div>
											<input placeholder="<?= __('user.product_quantity_placeholder') ?>" name="product_quantity" class="form-control" value="<?php echo $product['_meta_product_quantity']; ?>" type="number">
										</div>
									</div>
								</div>
								<div class="col-sm-4">
									<div class="form-group form-image-group text-center">
										<div>
											<label class="col-form-label"><?= __('user.product_featured_image') ?></label><br>
											<div class="fileUpload btn btn-sm btn-primary">
												<span><?= __('user.choose_file') ?></span>
												<input onchange="readURL(this,'#featureImage')" id="product_featured_image" name="product_featured_image" class="upload" type="file">
											</div>
											<?php $product_featured_image = $product['product_featured_image'] != '' ? 'assets/images/product/upload/thumb/' . $product['product_featured_image'] : 'assets/images/no_product_image.png' ; ?>
											<img src="<?php echo base_url($product_featured_image); ?>" id="featureImage" class="thumbnail" border="0" width="220px">
										</div>
									</div>
								</div>
								<div class="col-sm-12">
									<div class="form-group">
										<label class="col-form-label"><?= __('user.categories') ?></label>
										<div class="category-container">
											<input name="category_auto" placeholder="<?= __('user.categories') ?>" id="category_auto" class="form-control" autocomplete="off">
											<ul class="category-selected">
												<?php if(isset($categories)){ ?>
													<?php foreach ($categories as $key => $category) { ?>
														<li>
										            		<i class="fa fa-trash remove-category"></i>
										            		<span><?= $category['name'] ?></span>
										            		<input type="hidden" name="category[]" type="" value="<?= $category['id'] ?>">
										            	</li>
													<?php } ?>
												<?php } ?>
											</ul>
										</div>
									</div>

									<div class="form-group">
										<label class="col-form-label"><?= __('user.description') ?></label>
										<div>
											<textarea rows="4" placeholder="<?= __('user.enter_your_product_description') ?>" class="form-control" name="product_description"  type="text"><?php echo $product['product_description']; ?></textarea>
										</div>
									</div>

									<div class="form-group">
										<label class="col-form-label"><?= __('user.product_launching_datetime') ?></label>
										<div>
											<input placeholder="<?= __('user.product_launching_datetime_placeholder') ?>" name="product_launching_datetime" class="form-control" value="<?= (!empty($product['_meta_product_launching_datetime'])) ? date('d-m-Y H:i', strtotime($product['_meta_product_launching_datetime'])) : ""; ?>" type="text">
										</div>
									</div>

									<div class="row">
										<?php

										if(isset($product['_meta_product_sale_start']) && ! empty($product['_meta_product_sale_start'])) {
											$product_sale_period = date('d-m-Y H:i:s', strtotime($product['_meta_product_sale_start']))." - ".date('d-m-Y H:i:s', strtotime($product['_meta_product_sale_end']));
										} else {
											$product_sale_period = '';
										}

										?>
										<div class="col-md-7">
											<div class="form-group">
												<label class="col-form-label"><?= __('user.product_sale_period') ?></label>
												<div>
													<input placeholder="<?= __('user.product_sale_period_placeholder') ?>" name="product_sale_period" class="form-control daterange-picker" type="text" value="<?= $product_sale_period; ?>" autocomplete="off">
												</div>
											</div>
										</div>
										<div class="col-md-5">
											<div class="form-group">
												<label class="col-form-label"><?= __('user.product_sale_period_price') ?></label>
												<div>
													<input placeholder="<?= __('user.product_sale_period_price') ?>" name="product_sale_price" class="form-control" type="number" value="<?= $product['_meta_product_sale_price']; ?>">
												</div>
											</div>
										</div>
									</div>

									<div class="row mt-4">
										<div class="col-sm-4">
											<div class="form-group mb-0">
												<label class="control-label"><?= __('user.show_on_store'); ?></label>
												<div class="form-switch">
													<input name="on_store" class="form-check-input update_product_settings" type="checkbox" <?= (isset($product['on_store']) && (int)$product['on_store'] == 1) ? 'checked' : '' ?> data-toggle="toggle" data-size="normal" data-on="<?= __('user.status_on'); ?>" data-off="<?= __('user.status_off'); ?>" data-setting_key="on_store"  data-product_id="<?= $product['product_id'] ?>">
												</div>
											</div>
										</div>
										<div class="col-sm-4">
											<div class="form-group mb-0 ">
												<label class="control-label"><?= __('user.show_to_affiliates'); ?></label>
												<div class="form-switch">
													<input name="show_to_affiliates" class="form-check-input update_product_settings" type="checkbox" <?= (isset($product['_meta_show_to_affiliates']) && (int)$product['_meta_show_to_affiliates'] == 1) ? 'checked' : '' ?> data-toggle="toggle" data-size="normal" data-on="<?= __('user.status_on'); ?>" data-off="<?= __('user.status_off'); ?>" data-setting_key="_meta_show_to_affiliates"  data-product_id="<?= $product['product_id'] ?>">
												</div>
											</div>
										</div>

										<div class="col-sm-4">
											<div class="form-group mb-0 ">
												<label class="control-label"><?= __('user.show_to_featured'); ?></label>
												<div class="form-switch">
													<input name="show_to_featured" class="form-check-input update_product_settings" type="checkbox" <?= (isset($product['_meta_show_to_featured']) && (int)$product['_meta_show_to_featured'] == 1) ? 'checked' : '' ?> data-toggle="toggle" data-size="normal" data-on="<?= __('user.status_on'); ?>" data-off="<?= __('user.status_off'); ?>" data-setting_key="_meta_show_to_featured"  data-product_id="<?= $product['product_id'] ?>">
												</div>
											</div>
										</div>
									</div>
								</div>

							</div>


						</div>
					</div>

					<div class="card mt-4 <?= ($product['product_type'] == 'downloadable') ? '' : 'd-none' ?>" id="downloadable_card">
						<div class="card-header">
							<h4 class="header-title">
								<?= __('user.downloadable_product'); ?>
							</h4>
						</div>
						<div class="card-body">
							<div class="file-preview-button btn btn-primary">
								<?= __('user.downloadable_file'); ?>
								<input type="file" class="downloadable_file_input" name="downloadable_file" multiple="multiple">
							</div>

							<div id="priview-table" class="table-responsive">
								<table class="table table-hover">
									<thead>
										<?php if($product['product_type'] == 'downloadable') {   $product['downloadable_files'] = json_decode($product['downloadable_files'],true);
										foreach ($product['downloadable_files'] as $key => $value) { ?>
											<tr>
												<td width="70px"> <div class="upload-priview up-zip" ></div></td>
												<td>
													<?= $value['mask'] ?>
													<input type="hidden" name="keep_files[]" value="<?= $value['name'] ?>">
												</td>
												<td width="70px"><button type="button" class="btn btn-danger btn-sm remove-priview-server" data-id="'+ i +'" ><?= __('user.remove'); ?></button></td>
											</tr>
										<?php } } ?>
									</thead>
									<tbody>

									</tbody>
								</table>
							</div>


						</div>
					</div>

					<div class="card mt-4" id="shipping_detail_card">
						<div class="card-header">
							<h4 class="header-title">
								<?= __('user.shipping_detail'); ?>
							</h4>
						</div>

						<div class="card-body">

							<div class="row">
								<div class="col-sm-4">
									<div class="form-group mb-0 ">
										<label class="control-label"><?= __('user.enable_shipping'); ?></label>
										<div class="form-switch">
											<input name="allow_shipping" class="form-check-input update_product_settings" type="checkbox" <?= (isset($product['allow_shipping']) && (int)$product['allow_shipping'] == 1) ? 'checked' : '' ?> data-toggle="toggle" data-size="normal" data-on="<?= __('user.status_on'); ?>" data-off="<?= __('user.status_off'); ?>" data-setting_key="allow_shipping"  data-product_id="<?= $product['product_id'] ?>">
										</div>
									</div>
								</div>
								<div class="col-sm-3">
									<div class="form-group">
										<label class="control-label"><?= __('user.country_location'); ?></label>
										<div class="form-switch">
											<input name="allow_country" class="form-check-input allow_country_settings" type="checkbox" <?= (int)$product['state_id'] >= 1 ? 'checked' : '' ?> data-toggle="toggle" data-size="normal" data-on="<?= __('user.status_on'); ?>" data-off="<?= __('user.status_off'); ?>" data-setting_key="allow_country"  data-product_id="<?= $product['product_id'] ?>">
										</div>
									</div>	
								</div>
								<div class="col-sm-12">
									<div class="country-chooser">
										<div class="row">
											<div class="col">
												<select class="form-control" name="country_id" id="country_id">
													<option value="0"><?= __('user.select_country'); ?></option>
													<?php foreach ($country_list as $key => $value) { ?>
														<option <?= $product_state->country_id == $value->id ? 'selected' : '' ?> value="<?= $value->id ?>"><?= $value->name ?></option>
													<?php } ?>
												</select>
											</div>
											<div class="col">
												<select class="form-control" name="state_id" id="state_id">
													<option value=""><?= __('user.select_state'); ?></option>
													<?php foreach ($states as $key => $value) { ?>
														<option <?= $product_state->id == $value->id ? 'selected' : '' ?> value="<?= $value->id ?>"><?= $value->name ?></option>
													<?php } ?>
												</select>
											</div>
										</div>
									</div>
								</div>
								<script type="text/javascript">
									$(document).on("change", ".allow_country_settings", function(){
										var checked = $(this).prop('checked');
										if (checked == true) {
											$(".country-chooser").show();
										}else{
											$(".country-chooser").hide();
										}
									})

									$( document ).ready(function() {
									    $(".allow_country_settings").trigger('change');
									});

									$("#country_id").on('change',function(){
										var country = $(this).val();
										$('#state_id').prop("disabled",true)
										$.ajax({
											url: '<?php echo base_url('get_state') ?>',
											type: 'post',
											dataType: 'json',
											data: {country_id : country},
											success: function (json) {
												$('#state_id').prop("disabled",false)
												if(json){
													var html = '<option value=""><?= __('user.select_state'); ?></option>';
													$.each(json, function(k,v){
														html += '<option value="'+v.id+'">'+v.name+'</option>';
													});
													$('#state_id').html(html);
												}
											}
										});
									});
								</script>
							</div>
						</div>
					</div>

					<div class="card mt-4">
						<div class="card-header">
							<h4 class="header-title">
								<?= __('user.recurssion_details'); ?>
							</h4>
						</div>

						<div class="card-body">
							<div class="row">
								<div class="col-sm-12">
									<fieldset class="custom-design mb-2">
										<legend><?= __('user.product_recursion'); ?></legend>
										<div class="form-group">
											<div>
												<?php
												$product_recursion_type = $product['product_recursion_type'];
												$product_recursion = $product['product_recursion'];
												?>
												<select name="product_recursion_type" class="form-control">
													<option <?= '' == $product_recursion_type ? 'selected' : '' ?> value=""><?=  __('user.none') ?></option>
													<option <?= 'default' == $product_recursion_type ? 'selected' : '' ?> value="default"><?= __('user.default') ?></option>
													<option <?= 'custom' == $product_recursion_type ? 'selected' : '' ?> value="custom"><?= __('user.custom') ?></option>								
												</select>							
											</div>
											<div class="toggle-container mt-2">
												<div class="d-none default-value">
													<p class="text-muted">
														<?php
														if($setting['product_recursion'] == 'custom_time'){
															if ($setting['recursion_endtime'] == NULL || $setting['recursion_endtime'] == '') {
																echo __('user.default_recursion'). " : " . timetosting($setting['recursion_custom_time']). " | ".__('user.endtime').": ".__('user.life_time');
															}else{
																echo __('user.default_recursion'). " : " . timetosting($setting['recursion_custom_time']). " | ".__('user.endtime')." : " . dateFormat($setting['recursion_endtime']);
															}
														}else{
															if ($setting['recursion_endtime'] == NULL || $setting['recursion_endtime'] == '') {
																echo __('user.default_recursion'). " : " . __('user.'.$setting['product_recursion']) . " | ".__('user.endtime')." : ".__('user.life_time');
															}else{
																echo __('user.default_recursion'). " : " . __('user.'.$setting['product_recursion']) . " | ".__('user.endtime')." : " . dateFormat($setting['recursion_endtime']);
															}
														}
														?>
													</p>
												</div>

												<div class="d-none custom-value">
													<div class="custom_recursion">
														<div class="form-group">
															<select name="product_recursion" class="form-control" id="recursion_type">
																<option value=""><?= __('user.select_recursion'); ?></option>
																<option <?php if($product_recursion == 'every_day') { ?> selected <?php } ?> value="every_day"><?=  __('user.every_day') ?></option>
																<option <?php if($product_recursion == 'every_week') { ?> selected <?php } ?>  value="every_week"><?=  __('user.every_week') ?></option>
																<option <?php if($product_recursion == 'every_month') { ?> selected <?php } ?>  value="every_month"><?=  __('user.every_month') ?></option>
																<option <?php if($product_recursion == 'every_year') { ?> selected <?php } ?>  value="every_year"><?=  __('user.every_year') ?></option>
																<option <?php if($product_recursion == 'custom_time') { ?> selected <?php } ?>  value="custom_time"><?=  __('user.custom_time') ?></option>
															</select>
														</div>

														<div class="form-group custom_time">
															<?php
															$minutes = $product['recursion_custom_time'];
															$day = floor ($minutes / 1440);
															$hour = floor (($minutes - $day * 1440) / 60);
															$minute = $minutes - ($day * 1440) - ($hour * 60);
															?>

															<input type="hidden" name="recursion_custom_time" value="<?php echo $minutes; ?>">

															<div class="row">
																<div class="col-sm-4">
																	<label class="control-label"><?= __('user.days'); ?> : </label>
																	<input placeholder="Days" type="number" class="form-control" value="<?= $day ? $day : '' ?>" id="recur_day" onkeydown="if(event.key==='.'){event.preventDefault();}"  oninput="event.target.value = event.target.value.replace(/[^0-9]*/g,'');">
																</div>						
																<div class="col-sm-4">
																	<label class="control-label"><?= __('user.hours'); ?> : </label>
																	<select class="form-control" id="recur_hour">
																		<?php for ($x = 0; $x <= 23; $x++) {
																			$selected = ($x == $hour ) ? 'selected="selected"' : '';
																			echo '<option value="'.$x.'" '.$selected.'>'.$x.'</option>';
																		} ?>
																	</select>
																</div>						
																<div class="col-sm-4">
																	<label class="control-label"><?= __('user.minutes'); ?> : </label>
																	<select class="form-control" id="recur_minute">
																		<?php for ($x = 0; $x <= 59; $x++) {
																			$selected = ($x == $minute ) ? 'selected="selected"' : '';
																			echo '<option value="'.$x.'" '.$selected.'>'.$x.'</option>';
																		} ?>
																	</select>
																</div>						
															</div>									
														</div>
														<br>
														<div class="endtime-chooser row">
															<div class="col-sm-12">
																<div class="form-group">
																	<label class="control-label d-block"><?= __('user.choose_custom_endtime') ?> <input <?= $product['recursion_endtime'] ? 'checked' : '' ?>  id='setCustomTime' name='recursion_endtime_status' type="checkbox"> </label>
																	<div style="<?= !$product['recursion_endtime'] ? 'display:none' : '' ?>" class='custom_time_container'>
																		<input type="text" class="form-control" value="<?= $product['recursion_endtime'] ? date("d-m-Y H:i",strtotime($product['recursion_endtime'])) : '' ?>" name="recursion_endtime" id="endtime" placeholder="<?= __('user.choose_endtime'); ?>" >
																	</div>
																</div>
															</div>
														</div>
													</div>								
												</div>
											</div>

											<script type="text/javascript">
												$("select[name=product_recursion_type]").on("change",function(){
													$con = $(this).parents(".form-group");
													$con.find(".toggle-container .custom-value, .toggle-container .default-value").addClass('d-none');

													if($(this).val() == 'default'){
														$con.find(".toggle-container .default-value").removeClass("d-none");
													}else if($(this).val() == 'custom'){
														$con.find(".toggle-container .custom-value").removeClass("d-none");
													}
												})
												$("select[name=product_recursion_type]").trigger("change");


												$("select[name=product_recursion]").on("change",function(){
													$con = $(this).parents(".custom_recursion");
													$con.find(".custom_time").addClass('d-none');

													if($(this).val() == 'custom_time'){
														$con.find(".custom_time").removeClass("d-none");
													}
												})
												$("select[name=product_recursion]").trigger("change");
											</script>
										</div>
									</fieldset>
								</div>
							</div>
						</div>
					</div>

					<div class="card mt-4">
						<div class="card-header">
							<h4 class="header-title">
								<?= __('user.checkout_details'); ?>
							</h4>
						</div>

						<div class="card-body">
							<div class="row mt-4">
								<div class="col">
									<label class="col-form-label"><?= __('user.choose_checkout_template'); ?></label>
									<div class="input-group mb-3">
										<select class="custom-select" name="checkout_template">
											<?php 
											foreach ($checkout_template as $key => $value) {
												$selected = $product['_meta_checkout_template'] == $key ? "selected" : ""; 
												echo '<option value="'.$key.'" '.$selected.'>'.$value.'</option>';
											} 
											?>
										</select>
										<div class="input-group-append">
											<span class="btn btn-primary preview-selected-template" data-id="<?=$product['product_id']?>"><?= __('user.preview_checkout_template'); ?></span>
										</div>
									</div>
								</div>
							</div>

<div class="form-group">
	<label class="col-form-label"><?= __('user.product_checkout_terms') ?></label>
	<div>
		<textarea data-height='300px' placeholder="<?= __('user.enter_your_product_checkout_terms') ?>" class="summernote-img form-control" name="product_checkout_terms"><?php echo $product['_meta_product_checkout_terms']; ?></textarea>
	</div>
</div>

							<div class="row">
								<div class="col-sm-12">
									<fieldset class="custom-design mb-2 product_reviews_area">
										<legend><?= __('user.product_reviews'); ?></legend>

										<div class="form-group">
											<label class="col-form-label"><?= __('user.product_reviewer_name') ?></label>
											<input class="form-control" type="text" placeholder="<?= __('user.enter_product_reviewer_name') ?>" id="product_reviewer_name" />
										</div>

										<div class="form-group">
											<label class="col-form-label"><?= __('user.product_reviewer_comment') ?></label>
											<textarea class="form-control" placeholder="<?= __('user.enter_product_reviewer_comment') ?>" id="product_reviewer_comment"></textarea>
										</div>

										<span class="btn btn-primary add-product-review"><?= __('user.add_product_review'); ?></span>

										<div class="row">
											<div class="col">
												<table class="table-bordered mt-4 product_reviews_table <?= (isset($product['_meta_product_reviews']) && !empty($product['_meta_product_reviews'])) ? "" : "d-none"; ?> w-100">
													<thead>
														<tr>
															<td class="p-2" style="min-width:250px;"><?= __('user.product_reviewer_name') ?></td>
															<td class="p-2"><?= __('user.product_reviewer_comment') ?></td>
															<td class="p-2"></td>
														</tr>
													</thead>
													<tbody>
														<?php
														if(isset($product['_meta_product_reviews']) && !empty($product['_meta_product_reviews'])){

															$product_reviews = json_decode($product['_meta_product_reviews']);

															foreach ($product_reviews as $value) {
																echo '<tr><td class="p-2">'.$value->name.'</td><td class="p-2">'.$value->comment.'</td><td class="p-2"><input type="hidden" name="product_reviewer_name[]" value="'.$value->name.'"><input type="hidden" name="product_reviewer_comment[]" value="'.$value->comment.'"><span class="btn btn-danger remove-product-review">'. __('user.remove').'</span></td></tr>';
															}

														}
														?>
													</tbody>
												</table>
											</div>
										</div>
									</fieldset>



									<script type="text/javascript">
										$(document).on('click', '.add-product-review', function () {

											$('.product_reviews_area').find('.has-error').removeClass('has-error');
											$('.product_reviews_area').find('span.text-danger').remove();

											let reviewer_name = $('#product_reviewer_name').val();
											let reviewer_comment = $('#product_reviewer_comment').val();


											if(reviewer_name.length < 2){
												$('#product_reviewer_name').closest('.form-group').addClass('has-error');
												$('#product_reviewer_name').closest('.form-group').append('<span class="text-danger"><?= __('user.product_reviewer_name_is_required'); ?></span>');
											}

											if(reviewer_comment.length < 10) {
												$('#product_reviewer_comment').closest('.form-group').addClass('has-error');
												$('#product_reviewer_comment').closest('.form-group').append('<span class="text-danger"><?= __('user.product_reviewer_comment_is_required'); ?></span>');
											}

											if(reviewer_name.length > 2 && reviewer_comment.length > 10){
												$('.product_reviews_table tbody').append('<tr><td class="p-2">'+reviewer_name+'</td><td class="p-2">'+reviewer_comment+'</td><td class="p-2"><input type="hidden" name="product_reviewer_name[]" value="'+reviewer_name+'"><input type="hidden" name="product_reviewer_comment[]" value="'+reviewer_comment+'"><span class="btn btn-danger remove-product-review"><?= __('user.remove'); ?></span></td></tr>');
												$('.product_reviews_table').removeClass('d-none');
												$('#product_reviewer_name').val('');
												$('#product_reviewer_comment').val('');
											}
										});

										$(document).on('click', '.remove-product-review', function () {
											$(this).parent().parent().remove();
											if($('.product_reviews_table tbody tr').length === 0) {
												$('.product_reviews_table').addClass('d-none');
											}
										});
									</script>

								</div>
							</div>
							<!-- product footer -->
							<div class="row">
								<div class="col-sm-12">
									<fieldset class="custom-design mb-2 product_footer_area">
										<legend><?= __('user.product_footer'); ?></legend>

										<div class="form-group">
											<label class="col-form-label"><?= __('user.product_footer_page_name') ?></label>
											<input class="form-control" type="text" placeholder="<?= __('user.product_footer_page_name') ?>" id="product_footer_page_name" />
										</div>

										<div class="form-group">
											<label class="col-form-label"><?= __('user.product_footer_page_description') ?></label>
											<textarea class="form-control" placeholder="<?= __('user.product_footer_page_description') ?>" id="product_footer_page_description"></textarea>
										</div>

										<span class="btn btn-primary add-product-footer"><?= __('user.add_product_footer_page'); ?></span>

										<div class="row">
											<div class="col">
												<table class="table-bordered mt-4 product_footer_table <?= (isset($product['_meta_product_footer']) && !empty($product['_meta_product_footer'])) ? "" : "d-none"; ?> w-100">
													<thead>
														<tr>
															<td class="p-2" style="min-width:250px;"><?= __('user.product_footer_page_name') ?></td>
															<td class="p-2"><?= __('user.product_footer_page_description') ?></td>
															<td class="p-2"></td>
														</tr>
													</thead>
													<tbody>
														<?php
														if(isset($product['_meta_product_footer']) && !empty($product['_meta_product_footer'])){

															$product_footer = json_decode($product['_meta_product_footer']);
															$i= 1;
															foreach ($product_footer as $value) {
																echo '<tr><td class="p-2">'.$value->name.'</td><td class="p-2">'.$value->description.'</td><td class="p-2"><input type="hidden" name="product_footer_name[]" value="'.$value->name.'"><input type="hidden" name="product_footer_description[]" value="'.$value->description.'"><span class="btn btn-primary update-product-footer" data-id="'.$i.'" id="tr_'.$i.'">'. __('user.update').'</span><span class="btn btn-danger remove-product-footer">'. __('user.remove').'</span></td></tr>';
																$i++;
															}

														}
														?>
													</tbody>
												</table>
											</div>
										</div>
									</fieldset>



									<script type="text/javascript">
										$(document).on('click', '.add-product-footer', function () {

											$('.product_footer_area').find('.has-error').removeClass('has-error');
											$('.product_footer_area').find('span.text-danger').remove();

											let reviewer_name = $('#product_footer_page_name').val();
											let reviewer_comment = $('#product_footer_page_description').val();


											if(reviewer_name.length < 2){
												$('#product_footer_page_name').closest('.form-group').addClass('has-error');
												$('#product_footer_page_name').closest('.form-group').append('<span class="text-danger"><?= __('user.product_footer_name_is_required'); ?></span>');
											}

											if(reviewer_comment.length < 10) {
												$('#product_footer_page_description').closest('.form-group').addClass('has-error');
												$('#product_footer_page_description').closest('.form-group').append('<span class="text-danger"><?= __('user.product_footer_description_is_required'); ?></span>');
											}

											if(reviewer_name.length > 2 && reviewer_comment.length > 10){
												$('.product_footer_table tbody').append('<tr><td class="p-2">'+reviewer_name+'</td><td class="p-2">'+reviewer_comment+'</td><td class="p-2"><input type="hidden" name="product_footer_name[]" value="'+reviewer_name+'"><input type="hidden" name="product_footer_description[]" value="'+reviewer_comment+'"><span class="btn btn-danger remove-product-footer"><?= __('user.remove'); ?></span></td></tr>');
												$('.product_footer_table').removeClass('d-none');
												$('#product_footer_page_name').val('');
												$('#product_footer_page_description').val('');
											}
										});

										$(document).on('click', '.remove-product-footer', function () {
											$(this).parent().parent().remove();
											if($('.product_footer_table tbody tr').length === 0) {
												$('.product_footer_table').addClass('d-none');
											}
										});
										$(document).on('click', '.update-product-footer', function () {
											$("#updatepageModal").modal('show');
											var name = $(this).parent().find('input[name="product_footer_name[]"]').val();
											var description = $(this).parent().find('input[name="product_footer_description[]"]').val();
											$("#footer_page_name").val(name);
											$("#footer_page_description").val(description);
											$("#updatetr").val($(this).data('id'));
										});
										$(document).on('click', '#btnUpdate', function () {
											var name = $("#footer_page_name").val();
											var  description = $("#footer_page_description").val();
											var id = $("#updatetr").val();

											$("#tr_"+id).parent().find('input[name="product_footer_name[]"]').val(name);
											$("#tr_"+id).parent().find('input[name="product_footer_description[]"]').val(description);
											$("#tr_"+id).parent().prev().text(description)
											$("#tr_"+id).parent().prev().prev().text(name)
											$("#updatepageModal").modal('hide');
										});
									</script>

								</div>
							</div>
						</div>
					</div>
			
			<div class="col-sm-12">
			<div class="card commission-setting">
				<div class="card-header"><h4 class="header-title"><?= __('user.commission') ?></h4></div>

				<div class="card-body">

					<div class="form-group">
                        <label class="control-label"><?= __('user.status'); ?> : </label>
                        <?= product_status($product->product_status) ?>	
                    </div>

                 
                    <fieldset class="custom-design mb-2">
						<legend><?= __('user.commission_for_affiliate'); ?></legend>
						<div class="form-group">
							<label class="control-label"><?= __('user.click_commission'); ?></label>
							<div>
								<?php
									$commission_type= array(
										'default'    => 'Default',
										'fixed'      => 'Fixed',
									);
								?>
								<select name="affiliate_click_commission_type" class="form-control">
									<?php foreach ($commission_type as $key => $value) { ?>
										<option <?= $seller->affiliate_click_commission_type == $key ? 'selected' : '' ?> value="<?= $key ?>"><?= $value ?></option>
									<?php } ?>
								</select>
							</div>

							<div class="toggle-container">
								<div class="default-value d-none">
									<small class="text-muted d-block">
										<?php
											$commnent_line = "<b>Default Commission: </b>";
											if($seller_setting->affiliate_click_amount && $seller_setting->affiliate_click_count){
												$commnent_line .= c_format($seller_setting->affiliate_click_amount) ." Per ". (int)$seller_setting->affiliate_click_count ." Clicks";
											}
											else{
												$commnent_line .= __('user.not_set');
											}
											echo $commnent_line;
										?>
									</small>
								</div>
								<div class="custom-value d-none">										
									<div class="form-group">
										<div class="comm-group">
											<div>
												<div class="input-group mt-2">
												  	<div class="input-group-prepend"><span class="input-group-text"><?= __('user.click') ?></span></div>
													<input name="affiliate_click_count"  class="form-control" value="<?php echo $seller->affiliate_click_count; ?>" type="text" placeholder='Clicks'>
												</div>
											</div>
											<div>
												<div class="input-group mt-2">
												  	<div class="input-group-prepend"><span class="input-group-text"><?= $CurrencySymbol ?></span></div>
													<input name="affiliate_click_amount" class="form-control" value="<?php echo $seller->affiliate_click_amount; ?>" type="text" placeholder='Amount'>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>

							<script type="text/javascript">
								$("select[name=affiliate_click_commission_type]").on("change",function(){
									$con = $(this).parents(".form-group");
									$con.find(".toggle-container .percentage-value, .toggle-container .custom-value").addClass('d-none');

									if($(this).val() == 'default'){
										$con.find(".toggle-container .default-value").removeClass("d-none");
									}else{
										$con.find(".toggle-container .custom-value").removeClass("d-none");
									}
								})
								$("select[name=affiliate_click_commission_type]").trigger("change");
							</script>								
						</div>

						<div class="form-group">
							<div class="row">
								<div class="col-sm-6">
									<label class="control-label"><?= __('user.sale_commission'); ?></label>
									<div>
										<?php
											$commission_type= array(
												'default'    => 'Default',
												'percentage' => 'Percentage (%)',
												'fixed'      => 'Fixed',
											);
										?>
										<select name="affiliate_sale_commission_type" class="form-control">
											<?php foreach ($commission_type as $key => $value) { ?>
												<option <?= $seller->affiliate_sale_commission_type == $key ? 'selected' : '' ?> value="<?= $key ?>"><?= $value ?></option>
											<?php } ?>
										</select>
									</div>
								</div>
								<div class="col-sm-6">
									<div class="toggle-container">
										<div class="default-value d-none">
											<label class="control-label"><?= __('user.default_commission') ?></label>
											<small class="text-muted d-block">
												<?php
													$commnent_line = "";
													if($seller_setting->affiliate_sale_commission_type == ''){
														$commnent_line .= __('user.not_set');
													}
													else if($seller_setting->affiliate_sale_commission_type == 'percentage'){
														$commnent_line .= (float)$seller_setting->affiliate_commission_value .'%';
													}
													else if($seller_setting->affiliate_sale_commission_type == 'fixed'){
														$commnent_line .= 'Fixed : '. c_format($seller_setting->affiliate_commission_value);
													}
													echo $commnent_line;
												?>
											</small>
										</div>
										<div class="percentage-value d-none">										
											<div class="form-group">
												<label class="control-label m-0"><?= __('user.sale_commission') ?></label>
												<input name="affiliate_commission_value" id="affiliate_commission_value" class="form-control mt-2" value="<?php echo $seller->affiliate_commission_value; ?>" type="text" placeholder='Sale'>
											</div>
										</div>
									</div>
								</div>
							</div>


							<script type="text/javascript">
								$("select[name=affiliate_sale_commission_type]").on("change",function(){
									$con = $(this).parents(".form-group");
									$con.find(".toggle-container .percentage-value, .toggle-container .default-value").addClass('d-none');

									if($(this).val() == 'default'){
										$con.find(".toggle-container .default-value").removeClass("d-none");
									}else{
										$con.find(".toggle-container .percentage-value").removeClass("d-none");
									}
								})
								$("select[name=affiliate_sale_commission_type]").trigger("change");
							</script>
						</div>
					</fieldset> 
					<fieldset class="custom-design mb-2"
					 <?php if($vendor_setting['admin_click_status']==0 && $vendor_setting['admin_sale_status']==0) 
                     	echo 'style="display: none;"';
 					 else if($vendor_setting['admin_click_amount']==0 && $vendor_setting['admin_commission_value']==0) 
 					 	echo 'style="display: none;"';?> >
						<legend><?= __('user.commission_for_admin'); ?></legend>

						<div class="form-group mb-1" 
						<?php if($vendor_setting['admin_click_status']==0) 
						echo 'style="display: none;"';
						else if($vendor_setting['admin_click_amount']<=0 )
							echo 'style="display: none;"';
                     	  ?>>
							<label class="control-label"><?= __('user.click_commission'); ?> : </label> 
							<span>
								<?php 
									if((int)$product->product_id == 0 || $seller->admin_click_commission_type == '' || $seller->admin_click_commission_type == 'default'){
										echo c_format($vendor_setting['admin_click_amount']) ." Per ". (int)$vendor_setting['admin_click_count'] ." Clicks";
									} else{ 
										echo c_format($seller->admin_click_amount) ." Per ". (int)$seller->admin_click_count ." Clicks";
									} 
								?>
							</span>
						</div>

						<div class="form-group mb-1" <?php 
						if($vendor_setting['admin_sale_status']==0)
							echo 'style="display: none;"';
						else if ($vendor_setting['admin_commission_value']<=0 ) 
						echo 'style="display: none;"';?>>
							<label class="control-label"><?= __('user.sale_commission'); ?>: </label> 
							<span>
								<?php 
								 
									$commnent_line = "";
									
									if((int)$product["product_id"] == 0 || $seller->admin_sale_commission_type == '' || $seller->admin_sale_commission_type == 'default')
									{ 
										 
										if($vendor_setting['admin_sale_commission_type'] == ''){
											$commnent_line .= __('user.not_set');
										}
										else if($vendor_setting['admin_sale_commission_type'] == 'percentage'){
											$commnent_line .= (float)$vendor_setting['admin_commission_value'] .'%';
										}
										else if($vendor_setting['admin_sale_commission_type'] == 'fixed'){
											$commnent_line .= 'Fixed : '. c_format($vendor_setting['admin_commission_value']);
										}
									} else if($seller->admin_sale_commission_type == 'percentage'){
										$commnent_line .= 'Percentage : '. (float)$seller->admin_commission_value .'%';
									} else if($seller->admin_sale_commission_type == 'fixed'){
										$commnent_line .= 'Fixed : '. c_format($seller->admin_commission_value);
									} else {
										$commnent_line .= __('user.warning') . ":" . __('user.commission_not_set');
									} 

									echo $commnent_line;

								?>
								
							</span>
						</div>

						<div class="percentage-value d-none">
							<div class="form-group">	
								<div class="col-sm-6">
									<label class="control-label"><?= __('admin.sale_commission'); ?></label>
									<div>
										<?php
										$commission_type= array(
											'default'    => 'Default',
											'percentage' => 'Percentage (%)',
											'fixed'      => 'Fixed',
										);
										?>
										<select name="admin_sale_commission_type" class="form-control">
											<?php foreach ($commission_type as $key => $value) { ?>
												<option <?= $seller->admin_sale_commission_type == $key ? 'selected' : '' ?> value="<?= $key ?>"><?= $value ?></option>
											<?php } ?>
										</select>
									</div>
								</div>									
							<div class="form-group">
								<label class="control-label m-0"><?= __('admin.sale_commission'); ?></label>
								<input name="admin_commission_value" id="admin_commission_value" class="form-control mt-2" value="<?php echo $seller->admin_commission_value; ?>" type="text" placeholder='<?= __('admin.sale') ?>'>
							</div>
						</div>
					</fieldset>

					<fieldset class="custom-design mb-2">
						<legend><?= __('user.finalize_commission'); ?></legend>
						<div class="row">
							<div class="col-sm-4">
								<label class="control-label"><?= __('user.vendor') ?>  <span data-toggle='tooltip' title="<?= __('user.info_lbl_product_owner') ?>"></span></label>
								<input type="text" readonly="" value="0" class="form-control" id="ipt-vendor_commission">
							</div>
							<div class="col-sm-4">
								<label class="control-label"><?= __('user.admin') ?> <span data-toggle='tooltip' title="<?= __('user.info_lbl_admin') ?>"></span></label>
								<input type="text" readonly="" value="0" class="form-control" id="ipt-admin_sale_com">
							</div>
							<div class="col-sm-4">
								<label class="control-label"><?= __('user.affiliate') ?> <span data-toggle='tooltip' title="<?= __('user.info_lbl_product_other_affiliate') ?>"></span></label>
								<input type="text" readonly="" value="0" class="form-control" id="ipt-affiliate_sale_com">
							</div>
						</div>
					</fieldset>
					
				</div>
			</div>

			<div class="card">
				<div class="card-header"><h4 class="header-title"><?= __('user.admin_comments') ?></h4></div>
				<div class="card-body chat-card">
					<?php $comment = json_decode($seller->comment,1); ?>
					<?php if($comment){ ?>
						<ul class="comment-products">
							<?php foreach ($comment as $key => $value) { ?>
								<li class="<?= $value['from'] == 'affiliate' ? 'me' : 'other' ?>"> <div><?= $value['comment'] ?></div> </li>
							<?php } ?>
						</ul>
					<?php } ?>
					<div class="bg-white form-group m-0 p-2">
						<textarea class="form-control" placeholder="Enter message and save product to send" name="admin_comment"></textarea>
					</div>
				</div>
				
			</div>
		</div>

			<div class="card-footer">
					<div class="text-right">
						<span class="loading-submit"></span>
						<?php if((int)$product->product_id > 0){ ?>
							<button type="submit" class="btn btn-lg btn-default btn-submit btn-success" name="ask_to_review"><?= __('user.send_to_review') ?>
							</button>
						<?php } ?>
						<button type="submit" class="btn btn-lg btn-default btn-submit btn-success" name="save">
							<?= (int)$product->product_id == 0 ? 'Save & Submit For Review' : __('user.save') ?>
						</button>
					</div>
				</div>
		</form>

		<div class="modal fade" id="updatepageModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="exampleModalLabel"><?php echo __('user.top_update');?></h5>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
					</div>
					<div class="modal-body">
						<div class="row" id="rtcl-user-login-wrapper">
							<div class="col-md-12 rtcl-login-form-wrap login-1">

								<form id="frm_update_footer_page" class="form-horizontal" method="post" novalidate>
									<div class="form-group">
										<label class="control-label">
											<?php echo __('user.product_footer_page_name')?>
										</label>
										<input type="text" name="name" id="footer_page_name" class="form-control" required>
									</div>
									<div class="form-group">
										<label class="control-label">
											<?php __('user.product_footer_page_description') ?>
										</label>
										<textarea name="footer_page_description" class="form-control" id="footer_page_description"></textarea>
										<input type="hidden" id="updatetr" value="">
									</div>
									<div class="modal-footer">
										<button type="button" class="btn btn-primary"  id="btnUpdate" ><?php echo __('user.top_update')?></button>
									</div>
								</form>
							</div>
						</div>
					</div>

				</div>
			</div>
		</div>

		<script type="text/javascript">
			var cache = {};
			$( document ).ready(function() {
				$('input[name="product_type"]:checked').trigger('change');
			});

			$(".comment-products").animate({ scrollTop: $('.comment-products').prop("scrollHeight")}, 1000);

			$(".commission-setting :input, input[name=product_price]").on("change",calcCommission);

			$('#endtime').datetimepicker({
				format:'d-m-Y H:i',
				inline:true,
			});

			$('input[name="product_launching_datetime"]').datetimepicker({
				minDate:new Date(),
				format:'d-m-Y H:i'
			});

			$(function() {
				$('input[name="product_sale_period"]').daterangepicker({
					timePicker: true,
					locale: {
						format: 'DD-MM-Y HH:mm:ss',
						cancelLabel: 'Clear'
					},
					autoUpdateInput: false,
					timePicker24Hour:true,
					timePickerSeconds:true,
					autoApply:true
				});

				$('input[name="product_sale_period"]').on('apply.daterangepicker', function(ev, picker) {
					$(this).val(picker.startDate.format('DD-MM-Y HH:mm:ss') + ' - ' + picker.endDate.format('DD-MM-Y HH:mm:ss'));
				});

				$('input[name="product_sale_period"]').on('cancel.daterangepicker', function(ev, picker) {
					$(this).val('');
				});
			});

			$('#setCustomTime').on('change', function(){
				$(".custom_time_container").hide();
				if($(this).prop("checked")){
					$(".custom_time_container").show();
				}
			});

			var xhrCommission;

			function calcCommission(){
				$this = $(this);
				if(xhrCommission && xhrCommission.readyState != 4){
					xhrCommission.abort()
				}

				xhrCommission = $.ajax({
					url:'<?= base_url('admincontrol/calc_commission') ?>',
					type:'POST',
					dataType:'json',
					data:$(".commission-setting :input, input[name=product_price], input[name=product_id]"),
					success:function(json){
						if(json['success']){
							$("#ipt-vendor_commission").val(json['commission']['vendor_commission']);
							$("#ipt-admin_sale_com").val(json['commission']['admin_sale_com']);
							$("#ipt-affiliate_sale_com").val(json['commission']['affiliate_sale_com']);
						}
					},
				})
			}

			calcCommission();

			$("#category_auto").autocomplete({
				source: function( request, response ) {
					var term = request.term;
					if ( term in cache ) {response( cache[ term ] );return;}

					$.getJSON( '<?= base_url('usercontrol/category_auto') ?>', request, function( data, status, xhr ) {
						cache[ term ] = data;
						response( data );
					});
				},
				minLength: 0,
				select: function (event, ui) {
					$("#category_auto").blur();
					event.preventDefault();
					if($(".category-selected input[value='"+ ui.item.value +"']").length == 0){
						$(".category-selected").append('\
							<li>\
							<i class="fa fa-trash remove-category"></i>\
							<span>'+ ui.item.label +'</span>\
							<input type="hidden" name="category[]" type="" value="'+ ui.item.value +'">\
							</li>\
							');
					}
				},
			}).on('focus',function(){
				$(this).data("uiAutocomplete").search($(this).val());
			});

			$(".category-selected").delegate(".remove-category",'click', function(){
				$(this).parents("li").remove();
			});

			function readURLBanner(input) {
				if (input.files && input.files[0]) {
					var reader = new FileReader();
					reader.onload = function(e) {
						$('#bannerImage').attr('src', e.target.result);
					}
					reader.readAsDataURL(input.files[0]);
				}
			}

			$(document).on('change', '#recur_day, #recur_hour, #recur_minute', function(){
				var days = $('#recur_day').val();
				var hours = $('#recur_hour').val();
				var minutes = $('#recur_minute').val();
				var total_minutes;		

				total_hours = parseInt(days*24) + parseInt(hours);
				total_minutes = parseInt(total_hours*60) + parseInt(minutes);
				$('.custom_time').find('input[name="recursion_custom_time"]').val(total_minutes);
			});

			$('.preview-selected-template').on('click', function() {
				var product_id = $(this).data('id');
				let selected_template = $('select[name="checkout_template"]').val();
				window.open('<?= base_url('store/checkout_preview/') ?>'+ encodeURI(selected_template)+'?id='+product_id, "_blank");
			});

			$(".btn-submit").on('click',function(evt){
				evt.preventDefault();
				$btn = $(this);
				var formData = new FormData($("#form_form")[0]);

				var on_store_checked = $('input[name=on_store]').prop('checked');
				if (on_store_checked == true) {
					formData.append("on_store", "1");
				}else{
					formData.append("on_store", "0");
				}

				var show_to_affiliates_checked = $('input[name=show_to_affiliates]').prop('checked');
				if (show_to_affiliates_checked == true) {
					formData.append("show_to_affiliates", "1");
				}else{
					formData.append("show_to_affiliates", "0");
				}

				var show_to_featured_checked = $('input[name=show_to_featured]').prop('checked');
				if (show_to_featured_checked == true) {
					formData.append("show_to_featured", "1");
				}else{
					formData.append("show_to_featured", "0");
				}

				var allow_shipping_checked = $('input[name=allow_shipping]').prop('checked');
				if (allow_shipping_checked == true) {
					formData.append("allow_shipping", "1");
				}else{
					formData.append("allow_shipping", "0");
				}
				$.each(fileArray, function(i,j){ formData.append("downloadable_file[]", j.rawData); });
				formData.append("action", $(this).attr("name"));

				formData = formDataFilter(formData);
				$this = $("#form_form");	       

				$btn.btn("loading");
				$.ajax({
					url:'<?= base_url('usercontrol/save_sale_product') ?>',
					type:'POST',
					dataType:'json',
					cache:false,
					contentType: false,
					processData: false,
					data:formData,
					xhr: function (){
						var jqXHR = null;

						if ( window.ActiveXObject ){
							jqXHR = new window.ActiveXObject( "Microsoft.XMLHTTP" );
						}else {
							jqXHR = new window.XMLHttpRequest();
						}

						jqXHR.upload.addEventListener( "progress", function ( evt ){
							if ( evt.lengthComputable ){
								var percentComplete = Math.round( (evt.loaded * 100) / evt.total );
								$('.loading-submit').text(percentComplete + "% "+'<?= __('user.loading') ?>');
							}
						}, false );

						jqXHR.addEventListener( "progress", function ( evt ){
							if ( evt.lengthComputable ){
								var percentComplete = Math.round( (evt.loaded * 100) / evt.total );
								$('.loading-submit').text("Save");
							}
						}, false );
						return jqXHR;
					},
					error:function(){ $btn.btn("reset"); },
					success:function(result){            	
						$btn.btn("reset");
						$('.loading-submit').hide();
						$this.find(".has-error").removeClass("has-error");
						$this.find("span.text-danger").remove();

						if(result['location']){
							window.location = result['location'];
						}
						if(result['errors']){
	                    $.each(result['errors'], function(i,j){
	                        $ele = $this.find('[name="'+ i +'"]');

	                        if($ele.length){
	                            $ele.parents(".form-group").addClass("has-error");
	                            $ele.after("<span class='text-danger'>"+ j +"</span>");
	                        } else {
	                        	Swal.fire({
									icon: 'error',
									html: j,
								});
	                        }	
	                    });
	                }
					},
				});

				return false;
			});

			$('.update_product_settings').on('change', function(){
				var checked = $(this).prop('checked');
				var setting_key = $(this).data('setting_key');
				var product_id = $(this).data('product_id');

				if (checked == true) {
					var status = 1;
				}else{
					var status = 0;
				}

				$.ajax({
					url:'<?= base_url("productsales/update_product_settings") ?>',
					type:'POST',
					dataType:'json',
					data:{'action':'update_all_settings', status:status, setting_key:setting_key, product_id:product_id},
					success:function(json){
					},
				})
			});

			$(document).on('click',".proType",function(e){
				e.preventDefault();
				var value =$(this).data('value');
				$(".proType").removeClass('btn_active');
				$(this).addClass('btn_active');
				$(`input[name="product_type"][value="`+value+`"]`).prop('checked',true).change();
				
				if(value=='downloadable'){
					$("#downloadable_card").removeClass('d-none');
					$("#shipping_detail_card").addClass('d-none');
				} else {
					$("#downloadable_card").addClass('d-none')		
					$("#shipping_detail_card").removeClass('d-none');
				}	
			});
			var fileArray = [];

			$('.downloadable_file_input').change(function(e){
				$.each(e.target.files, function(index, value){
					var fileReader = new FileReader(); 
					fileReader.readAsDataURL(value);
					fileReader.name = value.name;
					fileReader.rawData = value;
					fileArray.push(fileReader);
				});

				render_priview();
			});

			var getFileTypeCssClass = function(filetype) {
				var fileTypeCssClass;
				fileTypeCssClass = (function() {
					switch (true) {
						case /image/.test(filetype): return 'image';
						case /video/.test(filetype): return 'video';
						case /audio/.test(filetype): return 'audio';
						case /pdf/.test(filetype): return 'pdf';
						case /csv|excel/.test(filetype): return 'spreadsheet';
						case /powerpoint/.test(filetype): return 'powerpoint';
						case /msword|text/.test(filetype): return 'document';
						case /zip/.test(filetype): return 'zip';
						case /rar/.test(filetype): return 'rar';
						default: return 'default-filetype';
					}
				})();
				return fileTypeCssClass;
			};

			function render_priview() {
				var html = '';

				$.each(fileArray, function(i,j){
					html += '<tr>';
					html += '    <td width="70px"> <div class="upload-priview up-'+ getFileTypeCssClass(j.rawData.type) +'" ></div></td>';
					html += '    <td>'+ j.name +'</td>';
					html += '    <td width="70px"><button type="button" class="btn btn-danger btn-sm remove-priview" data-id="'+ i +'" ><?= __('user.remove') ?></button></td>';
					html += '</tr>';
				})

				$("#priview-table tbody").html(html);
			}

			$("#priview-table").delegate('.remove-priview','click', function(){
				if(!confirm('<?= __('user.are_you_sure') ?>')) return false;

				var index = $(this).attr("data-id");
				fileArray.splice(index,1);
				render_priview()
			})
			$(document).on('click','.remove-priview-server',function(){
				if(!confirm("<?= __('user.are_you_sure') ?>")) return false;
				$(this).parents("tr").remove();
			})
		</script>
