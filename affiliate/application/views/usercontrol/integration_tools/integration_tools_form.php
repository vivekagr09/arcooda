<div class="row">
<div class="col-12">
<div class="card">
	<div class="card-header">
		<div>
			<h5 class="pull-left"><?= __('admin.integration_tools') ?>(<?= ucfirst(str_replace("_", " ", $type)) ?>) </h5>
			<div class="pull-right">
				<a class="btn btn-primary btn-sm" href="<?= base_url('usercontrol/integration_tools') ?>"><?= __('admin.back') ?></a>
			</div>
		</div>
	</div>

	<div class="card-body">


		<?php
		if(isset($tool)) {
			$security_alerts = external_integration_security_check($tool['target_link']);
		
			if(!is_array($security_alerts)){  ?>
				<h4 class="notification_on_pages">
					<div class="well">
					<span class="badge bg-danger"><?= "<?= __('admin.error')?> ".$security_alerts ?>: <?= __('admin.invalid_campaign_target_link')?></span>
					</div>
				</h4>
			<?php } ?>

			<?php if(is_array($security_alerts) && $security_alerts['comment']){ ?>

				<h4 class="notification_on_pages">
					<div class="well">
					<span class="badge bg-danger"><?= "<?= __('admin.error')?> ".$security_alerts ?>: <?= __('admin.code_has_comment_line')?></span>
					</div>
				</h4>
			<?php } ?>

			<?php if(is_array($security_alerts) && empty($security_alerts['common_code'])){ ?>

				<h4 class="notification_on_pages">
					<div class="well">
					<span class="badge bg-danger"><?= __('admin.warning')?>: <?= __('admin.common_integration_code_not_available_msg')?></span>
					</div>
				</h4>
			<?php } ?>

				 <?php if(isset($security_alerts['website_url']) && empty($security_alerts['website_url'])){ ?>
				 	<h4 class="notification_on_pages">
					<div class="well">
					<span class="badge bg-danger"><?= __('admin.warning')?>: <?= __('admin.website_url_not_available_msg')?></span>
					</div>
				</h4>
				<?php } ?>

				<?php if($tool['tool_type'] == 'program'){ ?>
						<?php $program = $this->IntegrationModel->getProgramByID($tool['program_id']);  ?>

						<?php if($program['sale_status'] == 1){ ?>
							<?php if(isset($security_alerts['sale_integration']) && empty($security_alerts['sale_integration'])){ ?>
								<h4 class="notification_on_pages">
									<div class="well">
									<span class="badge bg-danger"><?= __('admin.warning')?>: <?= __('admin.sale_integration_code_not_available_msg')?></span>
									</div>
								</h4>
							<?php } ?>
						<?php } ?>

						<?php if($program['click_status'] == 1){ ?>
							<?php if(isset($security_alerts['product_click_integration']) && empty($security_alerts['product_click_integration'])){ ?>
								<h4 class="notification_on_pages">
									<div class="well">
									<span class="badge bg-danger"><?= __('admin.warning')?>: <?= __('admin.product_click_integration_code_not_available_msg')?></span>
									</div>
								</h4>
							<?php } ?>
						<?php } ?>

						<?php if($program['sale_status'] == 1 && $program['click_status'] == 1){ ?>
							<?php  if(isset($security_alerts['website_url_count']) && $security_alerts['website_url_count'] != 2){ ?>
								<h4 class="notification_on_pages">
									<div class="well">
									<span class="badge bg-danger"><?= __('admin.warning')?>: <?= __('admin.website_url_not_available_msg')?></span>
									</div>
								</h4>
							<?php } ?>
						<?php } ?>
				<?php } ?>

				<?php if($tool['tool_type'] == 'single_action' || $tool['tool_type'] == 'action'){ ?>
					<?php if(isset($security_alerts['action_integration']) && empty($security_alerts['action_integration'])){ ?>
						<h4 class="notification_on_pages">
							<div class="well">
							<span class="badge bg-danger"><?= __('admin.warning')?>: <?= __('admin.action_integration_code_not_available_msg')?></span>
							</div>
						</h4>
					<?php } ?>
				<?php } ?>
		
				<?php if($tool['tool_type'] == 'general_click'){ ?>
					<?php if(isset($security_alerts['general_click_integration']) && empty($security_alerts['general_click_integration'])){ ?>
						<h4 class="notification_on_pages">
							<div class="well">
							<span class="badge bg-danger"><?= __('admin.warning')?>: <?= __('admin.click_integration_code_not_available_msg')?></span>
							</div>
						</h4>
					<?php }
				} ?>
			<?php }
			
			?>

			<form action="" method="get" id="form_tools">
				<h5 class="tool-name"><?= $tool['name'] ?></h5>

			<ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
			    <li class="nav-item" role="presentation">
			        <a class="nav-link active" id="home-tab" data-bs-toggle="pill" href="#home" role="tab" aria-controls="home" aria-selected="true"><?= __('user.general_setting')?></a>
			    </li>
			    <li class="nav-item" role="presentation">
			        <a class="nav-link" id="menu1-tab" data-bs-toggle="pill" href="#menu1" role="tab" aria-controls="menu1" aria-selected="false"><?= __('user.level_setting')?></a>
			    </li>
			    <li class="nav-item" role="presentation">
			        <a class="nav-link" id="postback-setting-tab" data-bs-toggle="pill" href="#postback-setting" role="tab" aria-controls="postback-setting" aria-selected="false"><?= __('user.postback_setting')?></a>
			    </li>
			    <li class="nav-item" role="presentation">
			        <a class="nav-link" id="conversion_api-tab" data-bs-toggle="pill" href="#conversion_api" role="tab" aria-controls="conversion_api" aria-selected="false"><?= __('user.conversion_api')?></a>
			    </li>
			</ul>


				<br>
				<div class="tab-content">
					<div class="tab-pane col-sm-12 active" id="home">
						<input type="hidden" name="type" value="<?= $type ?>">
						<input type="hidden" name="program_tool_id" id="program_tool_id"  value="<?= isset($tool) ? $tool['id'] : '0' ?>">

						<div class="row">
							<div class="col-sm-7">
								<div class="row">
									<div class="col-sm-12">
										<div class="form-group">
											<label class="control-label"><?= __('admin.tool_type') ?></label>
											<select class="form-control" name="tool_type" id="tool_type">
												<option value=""><?= __('admin.select_tool_type') ?></option>
												<option <?= (isset($tool) && $tool['tool_type'] == 'program') ? 'selected' : '' ?> value="program"><?= __('admin.sale_integration') ?></option>
												<option <?= (isset($tool) && $tool['tool_type'] == 'single_action') ? 'selected' : '' ?> value="single_action"><?= __('admin.single_action_integration') ?></option>
												<option <?= (isset($tool) && $tool['tool_type'] == 'action') ? 'selected' : '' ?> value="action"><?= __('admin.multi_action_integration') ?></option>
												<option <?= (isset($tool) && $tool['tool_type'] == 'general_click') ? 'selected' : '' ?> value="general_click"><?= __('admin.click_integration') ?></option>
											</select>
										</div>
									</div>

									<div class="col-sm-12">
										<div class="form-group for-program-tool" style="display:none;">
											<label class="control-label"><?= __('admin.tool_integration_plugin') ?></label>
											<select class="form-control" name="tool_integration_plugin">
												<option value=""><?= __('admin.select_tool_integration_plugin') ?></option>
												<?php 
												$pluginForSkipp = ['wp_user_register', 'wp_forms', 'postback', 'show_affiliate_id', 'wp_show_affiliate_id', 'affiliate_register_api', 'php_api_library'];

												foreach ($integration_plugins as $key => $module) {
													if(!in_array($key, $pluginForSkipp)) {
														?>

														<option <?= (isset($tool) && $tool['tool_integration_plugin'] == $key) ? 'selected' : '' ?> value="<?= $key; ?>"> <?= $module['name']; ?> </option>

													<?php }
												} ?>
											</select>
										</div>
									</div>

									<?php 
									$is_start_date = (isset($tool) && !empty($tool['start_date']) && $tool['start_date'] != '0000-00-00 00:00:00') ? true : false;
									$is_end_date = (isset($tool) && !empty($tool['end_date']) && $tool['end_date'] != '0000-00-00 00:00:00') ? true : false;

									$tool_period_val = 1;

									if($is_start_date && $is_end_date) {
										$tool_period_val = 4;
									}

									if($is_start_date && !$is_end_date) {
										$tool_period_val = 3;
									}

									if(!$is_start_date && $is_end_date) {
										$tool_period_val = 2;
									}
									?>

									<div class="col-sm-4">
										<div class="form-group">
											<label class="control-label"><?= __('admin.tool_period') ?></label>
											<select class="form-control" name="tool_period">
												<option value="1" <?= ($tool_period_val == '1') ? 'selected' : '' ?>><?= __('admin.always_running') ?></option>
												<option value="2" <?= ($tool_period_val == '2') ? 'selected' : '' ?>><?= __('admin.from_today_to_custom_date') ?></option>
												<option value="3" <?= ($tool_period_val == '3') ? 'selected' : '' ?>><?= __('admin.from_custom_date_to_lifetime') ?></option>
												<option value="4" <?= ($tool_period_val == '4') ? 'selected' : '' ?>><?= __('admin.for_custom_period') ?></option>
											</select>
										</div>
									</div>


									<div id="start_date_input" class="col-sm-4">
										<div class="form-group">
											<label class="control-label"><?= __('admin.start_date') ?></label>
											<input class="form-control datetime-picker" value="<?= (isset($tool) && !empty($tool['start_date']) && $tool['start_date'] != '0000-00-00 00:00:00') ? date('d-m-Y H:i', strtotime($tool['start_date'])) : '' ?>" name="start_date" type="text" autocomplete="off">
										</div>
									</div>

									<div id="end_date_input" class="col-sm-4">
										<div class="form-group">
											<label class="control-label"><?= __('admin.end_date') ?></label>
											<input class="form-control datetime-picker" value="<?= (isset($tool) && !empty($tool['end_date']) && $tool['end_date'] != '0000-00-00 00:00:00') ? date('d-m-Y H:i', strtotime($tool['end_date'])) : '' ?>" name="end_date" type="text" autocomplete="off">
										</div>
									</div>
								</div>



								<div class="form-group">
									<label class="control-label"><?= __('admin.name') ?></label>
									<input class="form-control" value="<?= isset($tool) ? $tool['name'] : '' ?>" name="name" type="text">
								</div>

								<div class="form-group">
									<label class="control-label"><?= __('admin.campaign_target_link') ?></label>
									<input class="form-control" value="<?= isset($tool) ? $tool['target_link'] : '' ?>" name="target_link" type="text">
								</div>

								<div class="form-group">
									<label class="control-label"><?= __('admin.terms') ?></label>
									<textarea name="terms" class="form-control" placeholder="<?= __('admin.terms') ?>"><?= isset($tool) ? $tool['terms'] : '' ?></textarea>
								</div>



								<div class="form-group">
									<label class="col-form-label"><?= __('admin.categories') ?></label>
									<div class="category-container">
										<input name="category_auto" placeholder="<?= __('admin.categories') ?>" id="category_auto" class="form-control" autocomplete="off">
										<ul class="category-selected">
											<?php if(isset($categories)){ ?>
												<?php foreach ($categories as $key => $category) { ?>
													<li>
														<i class="fa fa-trash remove-category"></i>
														<span><?= $category['label'] ?></span>
														<input type="hidden" name="category[]" type="" value="<?= $category['value'] ?>">
													</li>
												<?php } ?>
											<?php } ?>
										</ul>
									</div>
								</div>
							</div>
							<div class="col-sm-5">
								<div class="form-group">
									<label class="control-label"><?= __('user.status'); ?> : </label>
									<?= ads_status($tool['status']) ?>	
								</div>

								<div class="well">
									<div class="for-program-tool">
										<div class="form-group">
											<label class="control-label"><?= __('admin.select_program') ?> </label>
											<select class="form-control" name="program_id">
												<option value=""><?= __('admin.select_market_program') ?></option>
												<?php foreach ($programs as $key => $program) { ?>
													<option 
													data-admin_commission_type='<?= $program['admin_commission_type'] ?>'
													data-admin_commission_sale='<?= $program['admin_commission_type'] == 'fixed' ? c_format($program['admin_commission_sale']) : (int)$program['admin_commission_sale'] ."%" ?>'
													data-admin_commission_number_of_click='<?= $program['admin_commission_number_of_click'] ?>'
													data-admin_commission_click_commission='<?= c_format($program['admin_commission_click_commission']) ?>'
													data-admin_click_status='<?= $program['admin_click_status'] ?>'
													data-admin_sale_status='<?= $program['admin_sale_status'] ?>'

													data-commission_type='<?= $program['commission_type'] ?>'
													data-commission_sale='<?= $program['commission_type'] == 'fixed' ? c_format($program['commission_sale']) : (int)$program['commission_sale'] ."%" ?>'
													data-commission_number_of_click='<?= $program['commission_number_of_click'] ?>'
													data-commission_click_commission='<?= c_format($program['commission_click_commission']) ?>'
													data-click_status='<?= $program['click_status'] ?>'
													data-sale_status='<?= $program['sale_status'] ?>'
													<?= (isset($tool) && $tool['program_id'] == $program['id']) ? 'selected' : '' ?> value="<?= $program['id'] ?>"><?= $program['name'] ?></option>
												<?php } ?>
											</select>
										</div>

										<div class="form-group program-selector" style="display:none;">
											<label class="control-label"><?= __('user.admin_commission'); ?></label>
											<div class="program-admin-comission"></div>
											<label class="control-label"><?= __('user.affiliate_commission'); ?></label>
											<div class="program-affiliate-comission"></div>
										</div>

										<div class="text-right">
											<button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addProgram"><?= __('user.add_program') ?></button>
										</div>

										<script type="text/javascript">
											$('select[name="program_id"]').change(function(){
												if($(this).val() != ""){
													$(".program-selector").css('display','block');
												}else{
													$(".program-selector").css('display','none');
												}
												var data = $('select[name="program_id"] option:selected').data();
												var adminComissionString = '';
												var affiliateComissionString = '';
												if(Object.keys(data).length){
													adminComissionString += '<b>'+'<?= __('user.click') ?>'+'</b> : ';
													if(data['admin_click_status']){
														adminComissionString += data['admin_commission_click_commission'] + ' '+'<?= __('user.per') ?>'+' ' + data['admin_commission_number_of_click'] + " "+'<?= __('user.clicks') ?>';
													} else{
														adminComissionString += '<?= __('user.disabled') ?>';
													}

													adminComissionString += ' &nbsp; | &nbsp; <b> '+'<?= __('user.sale') ?>'+' </b> : ';
													if(data['admin_sale_status']){
														adminComissionString += data['admin_commission_sale'];
													} else{
														adminComissionString += '<?= __('user.disabled') ?>';
													}

													affiliateComissionString += '<b>'+'<?= __('user.click') ?>'+'</b> : ';
													if(data['click_status']){
														affiliateComissionString += data['commission_click_commission'] + ' '+'<?= __('user.per') ?>'+' ' + data['commission_number_of_click'] + " "+'<?= __('user.clicks') ?>';
													} else{
														affiliateComissionString += '<?= __('user.disabled') ?>';
													}

													affiliateComissionString += ' &nbsp; | &nbsp; <b> '+'<?= __('user.sale') ?>'+' </b> : ';
													if(data['sale_status']){
														affiliateComissionString += data['commission_sale'];
													} else{
														affiliateComissionString += '<?= __('user.disabled') ?>';
													}
												} else{
													adminComissionString += '<?= __('user.program_not_selected') ?>';
													affiliateComissionString += '<?= __('user.program_not_selected') ?>';
												}

												$(".program-admin-comission").html(adminComissionString);
												$(".program-affiliate-comission").html(affiliateComissionString);
												
											})
											$('select[name="program_id"]').trigger("change")
										</script>
									</div>

									<div class="for-action-tool">
										<div class="row">
											<div class="col-sm-6">
												<div class="form-group">
													<label class="control-label"><?= __('admin.number_of_action_per_commission') ?></label>
													<input class="form-control" name="action_click" value="<?= isset($tool) ? $tool['action_click'] : '' ?>">
												</div>
											</div>
											<div class="col-sm-6">
												<div class="form-group">
													<label class="control-label"><?= __('admin.cost_per_action') ?> ($)</label>
													<input class="form-control" name="action_amount" value="<?= isset($tool) ? $tool['action_amount'] : '' ?>">
												</div>
											</div>
										</div>

										<div class="row">
											<div class="col-sm-6">
												<div class="form-group">
													<label class="control-label">
														<?= __('admin.action_code') ?>
														<span data-toggle="tooltip" data-original-title="<?= __('user.code_of_action_commission_tracking_script_to_specify') ?>"></span>
													</label>
													<input class="form-control" name="action_code" id="action_code" value="<?= isset($tool) ? $tool['action_code'] : $randome_code ?>">
												</div>
											</div>
											<div class="col-sm-6">	
												<div class="form-group">
													<label class="control-label"> 
														<span><?= __('admin.generate_new_code') ?></span>
													</label>
													<button type="button" class="btn btn-primary btn-sm form-control" onclick="return GeneratenNewCode('action_code');"><?= __('admin.generate') ?></button>	
												</div>
											</div>	
										</div>	
										
										<div class="form-group">
											<label class="control-label"><?= __('user.admin_setting') ?>: 
												<?= ($tool['admin_action_amount'] && (int)$tool['admin_action_click']) ? c_format($tool['admin_action_amount']) ." ".__('user.per')." ". (int)$tool['admin_action_click'] ." ".__('user.clicks') : __('user.not_set') ?>
											</label>
										</div>
									
									</div>

									<div class="for-general_click-tool">
										<div class="row">
											<div class="col-sm-6">
												<div class="form-group">
													<label class="control-label"><?= __('admin.number_of_click') ?></label>
													<input class="form-control" name="general_click" value="<?= isset($tool) ? $tool['general_click'] : '' ?>">
												</div>
											</div>
											<div class="col-sm-6">
												<div class="form-group">
													<label class="control-label"><?= __('admin.cost_per_click') ?>($)</label>
													<input class="form-control" name="general_amount" value="<?= isset($tool) ? $tool['general_amount'] : '' ?>">
												</div>
											</div>
										</div>

										<div class="row">
											<div class="col-sm-6">
												<div class="form-group">
													<label class="control-label"><?= __('admin.general_code') ?>
													<span data-toggle="tooltip" data-original-title="<?= __('user.code_of_general_click_tracking_script_to_specify') ?>">
													</span>
													</label>
													<input class="form-control" name="general_code" id="general_code" value="<?= isset($tool) ? $tool['general_code'] : '' ?>">
												</div>
											</div>
											<div class="col-sm-6">
												<div class="form-group">
													<label class="control-label"> 
														<span><?= __('admin.generate_new_code') ?></span>
													</label>
													<button type="button" class="btn btn-primary btn-sm form-control" onclick="return GeneratenNewCode('general_code');"><?= __('admin.generate') ?></button>	
												</div>
											</div>	
										</div>

									<div class="form-group">
										<label class="control-label"><?= __('user.admin_setting') ?>:
											<?= ($tool['admin_general_amount'] && (int)$tool['admin_general_click']) ? c_format($tool['admin_general_amount']) ." ".__('user.per')." ". (int)$tool['admin_general_click'] ." ".__('user.clicks') : __('user.not_set') ?>
										</label>
									</div>
								</div>
							</div>

							<div class="card mt-3">
								<div class="card-header "><p class="m-0"><?= __('user.vendor_commnts') ?></p></div>
								<div class="card-body chat-card">
									<?php $comment = json_decode($tool['comment'],1); ?>
									<?php if($comment){ ?>
										<ul class="comment-products">
											<?php foreach ($comment as $key => $value) { ?>
												<li class="<?= $value['from'] == 'affiliate' ? 'me' : 'other' ?>"> 
													<?php if ($value['from']=='affiliate'): ?>
														
														<div  data-id="<?= $key ?>" class="comment-content-<?= $key ?>"><?= $value['comment'] ?></div><a href="javascript:void(0)" data-id="<?= $key ?>" class="edit-comment"><i class="fa fa-pencil-square-o"></i></a> 
													<?php else: ?>
														<div><?= $value['comment'] ?></div> 
													<?php endif ?>
												</li>
											<?php } ?>
										</ul>
									<?php } else echo '<ul class="comment-products"></ul>'; ?>
									<div class="bg-white form-group m-0 p-2">
										<textarea class="form-control" id="comment-box" placeholder="<?= __('user.enter_message_and_save_program_to_send') ?>" name="comment"></textarea>
									</div>
									<div class="form-group text-right d-none" id="btnUpdateArea">
										<button type="button" id="btnUpdate" class="btn btn-primary"><?php echo __('user.update')?></button>
										<input type="hidden" id="updateid" value="">
									</div>
								</div>
							</div>

							<div class="form-group">
								<label class="control-label"><?= __('admin.cookies_type') ?></label>
								<select class="form-control cookies_type_select" name="cookies_type">
									<option value="0" selected><?= __('admin.default') ?></option>
									<option value="1" <?= isset($tool) && $tool['cookies_type'] == 1 ? 'selected' : '' ?>><?= __('admin.custom') ?></option>
								</select>
							</div>

							<div class="form-group cookies_type_input" <?= isset($tool) && $tool['cookies_type'] == 1 ? '' : 'style="display:none"' ?>>
                                <label class="control-label"><?= __('admin.custom_cookies_tracker_in_days') ?></label>
                                <input class="form-control" type="number" value="<?= isset($tool) ? $tool['custom_cookies'] : '' ?>" name="custom_cookies" />
                            </div>
						</div>
					</div>

					<div class="form-group">
						<label class="control-label d-block"><?= __('admin.featured_image') ?></label>

						<div class="fileUpload btn btn-sm btn-primary">
							<span><?= __('admin.choose_file') ?></span>
							<input onchange="readURL(this,'#featured_image')" id="product_featured_image" name="featured_image" class="upload" type="file">
						</div>

						<?php $featured_image = $tool['featured_image'] != '' ? 'assets/images/product/upload/thumb/' . $tool['featured_image'] : 'assets/images/no_product_image.png' ; ?>
						<?php 
						$campaign_default_image_class = $tool['featured_image'] != '' ? '' : 'campaign_default_image' ;
						?>
						<input type="hidden" name="old_featured_image" value="<?= $tool['featured_image'] ?>">
						<img src="<?php echo base_url($featured_image); ?>" id='featured_image' class="img-thumbnail campaign_default_image" border="0" width="100px">
					</div>

					<?php if($type == 'banner'){ ?>
						<div class="well">
							<div class="bg-white p-3">
								<h5><?= __('admin.banner_images') ?></h5>

								<div class="table-responsive">
									<table class="table banner-table">
										<thead>
											<tr>
												<th><?= __('admin.image') ?></th>
												<th width="180px"><?= __('admin.size') ?></th>
												<th width="50px"></th>
											</tr>
										</thead>
										<tbody>
											<?php foreach ($tool['ads'] as $key => $ads) { ?>
												<tr>
													<td>
														<input type="hidden" name="keep_ads[]" value="<?= $ads['id'] ?>">
														<input type="hidden" name="custom_banner_ext[]" value="<?= $ads['value'] ?>">
														<img class="img-thumbnail campaign_default_image" src="<?= $ads['value'] ?>" width="100px">
														<input type="file" accept="image/*" class="file-input" name="custom_banner[]">
													</td>
													<td><input type="text"  class="form-control size-input" value="<?= $ads['size'] ?>" readonly="" name="custom_banner_size[]"></td>
													<td><button type="button" class="btn btn-sm btn-danger remove-custom-image"><i class="fa fa-trash"></i></button></td>
												</tr>
											<?php } ?>
											<?php if(!isset($tool['ads']) || empty($tool['ads'])) { ?>
												<tr>
													<td>
														<img class="img-thumbnail campaign_default_image" src="<?= base_url('assets/images/no_product_image.png'); ?>" width="100px">
														<input type="file" accept="image/*" class="file-input" name="custom_banner[]">
														<input type="hidden" name="custom_banner_ext[]" value="">
														<input type="hidden" name="keep_ads[]" value="0">
													</td>
													<td><input type="text"  class="form-control size-input" readonly="" name="custom_banner_size[]"></td>
													<td><button type="button" class="btn btn-sm btn-danger remove-custom-image"><i class="fa fa-trash"></i></button></td>
												</tr>
											<?php } ?>
										</tbody>
									</table>
								</div>

								<div class="text-right">
									<button type="button" class="btn add-banner btn-primary btn-sm"> <?= __('admin.add_banner') ?></button>
								</div>
							</div>
						</div>
					<?php } else if($type == 'text_ads'){ ?>
						<?php 
						$_text_ads = isset($tool['ads'][0]) ? $tool['ads'][0] : array();
						?>
						<div class="form-group">
							<label class="control-label"><?= __('admin.content') ?></label>
							<textarea class="form-control" rows="10" name="text_ads_content"><?= isset($_text_ads['value']) ? $_text_ads['value'] : '' ?></textarea>
						</div>

						<div class="row">
							<div class="col-sm-12">
								<label class="control-label"><?= __('admin.text_size_px') ?></label>
								<input class="form-control" name="text_size" value="<?= isset($_text_ads['text_size']) ? $_text_ads['text_size'] : '' ?>">
							</div>
						</div>
						<br>
						<div class="row mb-3">
							<div class="col-sm-4">
								<label class="control-label"><?= __('admin.text_color') ?></label>
								<input class="form-control form-control-color" type="color" name="text_color" value="<?= isset($_text_ads['text_color']) ? $_text_ads['text_color'] : '' ?>">
							</div>
							<div class="col-sm-4">
								<label class="control-label"><?= __('admin.background_color') ?></label>
								<input class="form-control form-control-color" type="color" name="text_bg_color" value="<?= isset($_text_ads['text_bg_color']) ? $_text_ads['text_bg_color'] : '' ?>">
							</div>
							<div class="col-sm-4">
								<label class="control-label"><?= __('admin.border_color') ?></label>
								<input class="form-control form-control-color" type="color" name="text_border_color" value="<?= isset($_text_ads['text_border_color']) ? $_text_ads['text_border_color'] : '' ?>">
							</div>	
						</div>

					<?php } else if($type == 'link_ads'){ ?>
						<?php 
						$link_ads = isset($tool['ads'][0]) ? $tool['ads'][0] : array();
						?>
						<div class="form-group">
							<label class="control-label"><?= __('admin.link_title') ?></label>
							<input class="form-control" name="link_title" value="<?= isset($link_ads['value']) ? $link_ads['value'] : '' ?>">
						</div>

					<?php } else if($type == 'video_ads'){ ?>
						<?php 
						$video_ads = isset($tool['ads'][0]) ? $tool['ads'][0] : array();
						?>
						<div class="form-group">
							<label class="control-label"><?= __('admin.video_link') ?></label>
							<div class="video-url-input">
								<input class="form-control parse-video" name="video_link" value="<?= isset($video_ads['value']) ? $video_ads['value'] : '' ?>">

								<input class="form-control video-priview" readonly="" >
							</div>
							<div class="clearfix"></div>
						</div>

						<div class="form-group">
							<label class="control-label"><?= __('admin.autoplay') ?></label>
							<div>
								<label class="radio-inline"> <input type="radio" checked="" name="autoplay" value="0"> <?= __('admin.disable') ?> </label>
								<label class="radio-inline"> <input type="radio" <?= (isset($video_ads) && $video_ads['autoplay']) ? 'checked' : '' ?> name="autoplay" value="1"> <?= __('admin.enable') ?> </label>
							</div>
						</div>

						<div class="row">
							<div class="col-sm-6">
								<label class="control-label"><?= __('admin.height_px') ?></label>
								<input class="form-control" name="video_height" value="<?= isset($video_ads['video_height']) ? $video_ads['video_height'] : '' ?>">
							</div>
							<div class="col-sm-6">
								<label class="control-label"><?= __('admin.width_px') ?></label>
								<input class="form-control" name="video_width" value="<?= isset($video_ads['video_width']) ? $video_ads['video_width'] : '' ?>">
							</div>	
						</div>

						<br>

						<div class="form-group">
							<label class="control-lable"><?= __('admin.button_text') ?></label>
							<input class="form-control" name="button_text" value="<?= isset($video_ads['size']) ? $video_ads['size'] : '' ?>">
						</div>	

					<?php } ?>


					<?php  $allow_for = array_filter(explode(",", $tool['allow_for'])); ?>
					<div class="grid-campaign-group">
						<div class="form-group">
							<label class="control-label"><?= __('admin.allow_for') ?></label>
							<div>
								<label class="radio-inline">
									<input type="radio" <?= count($allow_for) == 0 ? 'checked' : ''  ?> name="allow_for_radio" class="allow_for" value="0"> <?= __('admin.all') ?>
								</label>
								<label class="radio-inline">
									<input type="radio" <?= count($allow_for) > 0 ? 'checked' : ''  ?> name="allow_for_radio" class="allow_for" value="1"> <?= __('admin.selected_affiliate') ?>
								</label>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label"><?= __('admin.status'); ?></label>
							<div>
								<div class="radio status-radio">
									<div class="row">
										<div class="col-sm-1">
											<label>
												<input type="radio" name="status" value="0" <?= (int) $tool['status'] == 0 ? 'checked' : '' ?>> 
												<span class="badge bg-warning"><?= __('user.draft'); ?></span>
											</label>
										</div>
										<div class="col-sm-1">
											<label>
												<input type="radio" name="status" value="1" <?= (int) $tool['status'] == 1 ? 'checked' : '' ?>> 
												<span class="badge bg-success"><?= __('user.public'); ?></span>
											</label>
										</div>
									</div>
								</div>
							</div>   
						</div>  
					</div>

					<div class="show-allow_for">
						<div class="bg-light p-3 border" style="height: 200px;overflow: auto;">
							<?php foreach ($users as $v) { ?>
								<label class="d-block">
									<input type="checkbox" <?= in_array($v['id'],$allow_for) ? 'checked' : '' ?> name="allow_for[]" value="<?= $v['id'] ?>"> <?= $v['name'] ?>
								</label>
							<?php } ?>
						</div>
					</div>
					<script type="text/javascript">
						$(".allow_for").on('change',function(){
							$(".show-allow_for").hide();

							if($(this).val() == '1'){
								$(".show-allow_for").show();
							}
						})
						$(".allow_for:checked").trigger("change");
					</script>
				</div>
				<div class="tab-pane col-sm-12 fade" id="postback-setting">
					<?php $marketpostback = json_decode($tool['marketpostback'],1); ?>
					<div class="row">
						<div class="col-sm-12">
							<div class="form-group">
								<label class="control-label"><?=  __('user.postback_status') ?></label>
								<select class="form-control marketpostback-status" name="marketpostback[status]">
									<option value=""><?= __('admin.disable') ?></option>
									<option value="default" <?= $marketpostback['status'] == 'default' ? 'selected' : '' ?>><?= __('admin.default') ?></option>
									<option value="custom" <?= $marketpostback['status'] == 'custom' ? 'selected' : '' ?>><?= __('admin.custom') ?></option>
								</select>
							</div>
							<div class="marketpostback-default m-2">
								<div class="card">
									<div class="card-header"><h6 class="m-0"><?=  __('user.default_postback_settings') ?></h6></div>
									<div class="card-body">
										<div>
											<b><?=  __('user.status') ?>:</b> <?= (int)$default_marketpostback['status'] == 1 ?  __('admin.enable') : __('admin.disable') ?>
										</div>

										<div>
											<b><?=  __('user.postback_url') ?>:</b> <?= $default_marketpostback['url'] ? $default_marketpostback['url'] : 'N/A' ?>
										</div>

										<?php 
										$marketpostback_dynamicparam = json_decode($default_marketpostback['dynamicparam'],1);
										$marketpostback_static = json_decode($default_marketpostback['static'],1);

										$dynamicparam = [
											'city' => __('user.city'),
											'regionCode' => __('user.region_code'),
											'regionName' => __('user.region_name'),
											'countryCode' => __('user.country_code'),
											'countryName' => __('user.country_name'),
											'continentName' => __('user.continent_name'),
											'timezone' => __('user.time_zone'),
											'currencyCode' => __('user.currency_code'),
											'currencySymbol' => __('user.currency_symbol'),
											'ip' => __('user.ip'),
											'id' => __('user.id_sale_id_or_click_id'),
										];
										?>
										<div>
											<b><?=  __('user.dynamic_params') ?></b> 
											<ol>
												<?php foreach ($marketpostback_dynamicparam as $key => $value) { ?>
													<li><?= $dynamicparam[$value] ?></li>
												<?php } ?>
											</ol>									
										</div>

										<div>
											<b><?=  __('user.static_params') ?></b> 
											<ol>
												<?php foreach ($marketpostback_static as $key => $value) { ?>
													<li>
														<b><?= $value['key'] ?></b>: 
														<span><?= $value['value'] ?></span>
													</li>
												<?php } ?>
											</ol>
										</div>
									</div>
								</div>
							</div>
							<div class="marketpostback-custom">
								<div class="form-group">
									<label class="control-label"><?=  __('user.postback_url') ?></label>
									<input type="text" name="marketpostback[url]" value="<?= $marketpostback['url'] ?>" class="form-control marketpostback-url">
								</div>
								<div class="form-group">
									<label class="control-label"><?=  __('user.dynamic_params') ?></label>
									<div>
										<?php 
										$dynamicparam = [
											'city' => __('user.city'),
											'regionCode' => __('user.region_code'),
											'regionName' => __('user.region_name'),
											'countryCode' => __('user.country_code'),
											'countryName' => __('user.country_name'),
											'continentName' => __('user.continent_name'),
											'timezone' => __('user.time_zone'),
											'currencyCode' => __('user.currency_code'),
											'currencySymbol' => __('user.currency_symbol'),
											'ip' => __('user.ip'),
											'type' => __('user.type').' action,general_click,product_click,sale',
											'id' => __('user.id_sale_id_or_click_id'),
										];
										$marketpostback_dynamicparam = $marketpostback['dynamicparam'];
										$marketpostback_static = $marketpostback['static'];
										?>
										<div class="row">
											<?php foreach ($dynamicparam as $key => $value) { ?>
												<div class="col-sm-3">
													<label class="checkbox font-weight-light">
														<input type="checkbox" <?= isset($marketpostback_dynamicparam[$key]) ? 'checked' : '' ?> name="marketpostback[dynamicparam][<?= $key ?>]" value="<?= $key ?>">
														<span> <b><?= $key ?></b> - <?= $value ?> </span>
													</label>
												</div>
											<?php } ?>
										</div>
									</div>
								</div>

								<div class="card">
									<div class="card-header">
										<h6 class="card-title m-0"><?=  __('user.static_params') ?></h6>
									</div>
									<div class="card-body p-0">
										<div class="static-params table-responsive">
											<table class="table table-striped table-bordered ">
												<thead>
													<tr>
														<td><?=  __('user.param_key') ?></td>
														<td><?=  __('user.param_value') ?></td>
														<td width="50px">#</td>
													</tr>
												</thead>
												<tbody></tbody>
												<tfoot>
													<tr>
														<td colspan="3"><button class="pull-right btn btn-sm btn-primary add-static-params" type="button"><?=  __('user.add') ?></button></td>
													</tr>
												</tfoot>
											</table>
										</div>
									</div>
								</div>

								<script type="text/javascript">
									$(".add-static-params").click(function(){
										addStaticParam('','');
									})

									<?php foreach ($marketpostback_static as $key => $value) {
										echo "addStaticParam('". $value['key'] ."','". $value['value'] ."');";
									} ?>

									var addStaticParamIndex = 0;
									function addStaticParam(key,val) {
										var html = `<tr>
										<td>
										<input type="text" value='${key}' name="marketpostback[static][${addStaticParamIndex}][key]" placeholder="`+'<?=  __('user.param_key') ?>'+`" class="form-control">
										</td>
										<td>
										<input type="text" name="marketpostback[static][${addStaticParamIndex}][value]" value='${val}' placeholder="`+'<?=  __('user.param_value') ?>'+`" class="form-control">
										</td>
										<td>
										<button class="pull-right btn btn-sm btn-danger remove-static-params" type="button"><i class="fa fa-trash"></i></button>
										</td>
										</tr>`;

										addStaticParamIndex++;
										$(".static-params tbody").append(html);
									}

									$(".static-params").delegate(".remove-static-params","click",function(){
										$(this).parents("tr").remove();
									})
								</script>
							</div>

							<script type="text/javascript">
								$(".marketpostback-status").change(function(){
									var val = $(this).val();
									$(".marketpostback-default, .marketpostback-custom").hide();

									if(val == 'default') $(".marketpostback-default").show();
									else if(val == 'custom') $(".marketpostback-custom").show();
								})
								$(".marketpostback-status").trigger("change");
							</script>
						</div>
					</div>
				</div>

				<!-- conversion api -->
				<div class="tab-pane col-sm-12 fade" id="conversion_api">

					<!-- click integration-->
						<div class="row"> 
							<div class="col-sm-6">
									<h3 class="panel-title">Click conversion</h3>
									 
									Refer to the below parameters are identical for Click conversion integration.

 										<h3 class="panel-title">Request :  </h3>
 										<br/>
 										<span class="text-warning">POST</span>
 										 : <?php echo base_url('integration/addClick'); ?>
									<div class="panel-body" style="overflow: auto;">
							         <table class="table table-hover">
							            <thead>
							               <tr>
							                  <th>Parameter</th>
							                  <th>Type</th>
							                  <th>Value</th>
							                  <th>Description</th>
							               </tr>
							            </thead>
							            <tbody>
							               <tr>
							                  <td>page_name</td>
							                  <td><code>string</code></td>
							                  <td><code>vendor_click</code></td>
							                  <td>Get the General code from the General setting tab.</td>
							               </tr>
							               <tr>
							                  <td>customFields</td>
							                  <td><code>json array</code></td>
							                  <td><code>[{"city":"cityName"},
							                  {"countryName":"countryName"}]</code></td>
							                  <td>optional value</td>
							               </tr>
							               <tr>
							                  <td>base_url</td>
							                  <td><code>string</code></td>
							                  <td><code>target url</code></td>
							                  <td>Get Target Link from General Setting link and Convert it to base64_encode format and then assign it to base_url</td>
							               </tr>

							               <tr>
							                  <td>current_page_url</td>
							                  <td><code>string</code></td>
							                  <td><code>page url</code></td>
							                  <td>client url of from the this api is called and Convert it to base 64 encode and then assign to current_page_url</td>
							               </tr>

							               <tr>
							                  <td>af_id</td>
							                  <td><code>string</code></td>
							                  <td><code>affiliate id </code></td>
							                  <td>Affiliate Id from external link url ex.  NzdtSnkyMklYTWlXU1hIMDhCdkcydz09-Mi0yMA==</td>
							               </tr>
							               <tr>
							                  <td>script_name</td>
							                  <td><code>string</code></td>
							                  <td><code>general_integration</code></td>
							                  <td>-</td>
							               </tr>
							                  
							            </tbody>
							         </table>
							      	</div>
								</div>
								<div class="col-sm-6">	 
									<h3 class="panel-title">Php Code Example :  </h3>
 										<br/>
<pre class="response-view" style="background-color: #272822;color:#fff">

$page_name="vendor_click";
$customFields= '[{"city":"cityName"},{"countryName":"countryName"}]'; //optional
//Url of api caller
$current_page_url= "http://example.com/callapi.php"; 

//Replace this url with Target URL
$base_url = "http://localhost/aff/client/site.php";
$af_id = "NzdtSnkyMklYTWlXU1hIMDhCdkcydz09-Mi0yMA==";
$script_name = "general_integration";

$postData = [];

$current_page_url = base64_encode($current_page_url);
$base_url = base64_encode($base_url);


$postData['page_name'] = $page_name; 
$postData['customFields'] = $customFields; 
$postData['current_page_url'] = $current_page_url; 
$postData['base_url'] = $base_url; 
$postData['af_id'] = $af_id; 
$postData['script_name'] = $script_name;

$url='<?php echo base_url('integration/addClick');?>;'
$curl = curl_init($url);
$request = http_build_query($postData);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, $request);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_HEADER, false);
curl_setopt($curl, CURLOPT_TIMEOUT, 30);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
$response=curl_exec($curl);

$response => "OK" if success 
</pre>
								 
							</div>
						</div>

						<!-- action integration  -->
						<div class="row"> 
							<div class="col-sm-6">
									<h3 class="panel-title">Action conversion</h3>
									<p>Refer to the below parameters are identical for action conversion integration.
									</p>
									
 										<h3 class="panel-title">Request :  </h3>
 										<br/>
 										<span class="text-warning">POST</span>
 										 : <?php echo base_url('integration/addClick'); ?>
									<div class="panel-body" style="overflow: auto;">
							         <table class="table table-hover">
							            <thead>
							               <tr>
							                  <th>Parameter</th>
							                  <th>Type</th>
							                  <th>Value</th>
							                  <th>Description</th>
							               </tr>
							            </thead>
							            <tbody>
							               <tr>
							                  <td>actionCode</td>
							                  <td><code>string</code></td>
							                  <td><code>vendor_action</code></td>
							                  <td>Get the Action code from General setting tab.</td>
							               </tr>
							               <tr>
							                  <td>customFields</td>
							                  <td><code>json array</code></td>
							                  <td><code>[{"city":"cityName"},
							                  {"countryName":"countryName"}]</code></td>
							                  <td>-</td>
							               </tr>
							               <tr>
							                  <td>base_url</td>
							                  <td><code>string</code></td>
							                  <td><code>target url</code></td>
							                  <td>Get Target Link from General Setting link and Convert it to base64_encode format and then assign it to base_url</td>
							               </tr>

							               <tr>
							                  <td>current_page_url</td>
							                  <td><code>string</code></td>
							                  <td><code>page url</code></td>
							                  <td>client url of from the this api is called and Convert it to base 64 encode and then assign to current_page_url</td>
							               </tr>

							               <tr>
							                  <td>af_id</td>
							                  <td><code>string</code></td>
							                  <td><code>affiliate Id</code></td>
							                  <td>Affiliate Id from external link url ex.  NzdtSnkyMklYTWlXU1hIMDhCdkcydz09-Mi0yMA==</td>
							               </tr>
							               <tr>
							                  <td>script_name</td>
							                  <td><code>string</code></td>
							                  <td><code>general_integration</code></td>
							                  <td>-</td>
							               </tr>
							                  
							            </tbody>
							         </table>
							      	</div>
								</div>
								<div class="col-sm-6">	 
									<h3 class="panel-title">Php Code Example :  </h3>
 										<br/>
<pre class="response-view" style="background-color: #272822;color:#fff">

$actioncode="vendor_action";
$customFields= '[{"city":"cityName"},{"countryName":"countryName"}]'; //optional
//Url of api caller
$current_page_url= "http://example.com/callapi.php"; 

//Replace this url with Target URL
$base_url = "http://localhost/aff/client/site.php";
$af_id = "NzdtSnkyMklYTWlXU1hIMDhCdkcydz09-Mi0yMA==";
$script_name = "general_integration";

$postData = [];

$current_page_url = base64_encode($current_page_url);
$base_url = base64_encode($base_url);


$postData['actionCode'] = $actioncode; 
$postData['customFields'] = $customFields; 
$postData['current_page_url'] = $current_page_url; 
$postData['base_url'] = $base_url; 
$postData['af_id'] = $af_id; 
$postData['script_name'] = $script_name;

$url='<?php echo base_url('integration/addClick');?>;'
$curl = curl_init($url);
$request = http_build_query($postData);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, $request);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_HEADER, false);
curl_setopt($curl, CURLOPT_TIMEOUT, 30);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
$response=curl_exec($curl);

$response => "OK" if success 
</pre>
								 
							</div>
						</div>

						<!-- order sell conversion -->
							<div class="row">
							<div class="col-sm-6">
									
									<h3 class="panel-title">Order Conversion :  </h3>
									<p>Need to call a separate API for order conversion<p>
 										<h3 class="panel-title">Request :  </h3>
 										<br/>
 										<span class="text-warning">POST</span>
 										 : <?php echo base_url('integration/addOrder'); ?>
									<div class="panel-body" style="overflow: auto;">
							         <table class="table table-hover">
							            <thead>
							               <tr>
							                  <th>Parameter</th>
							                  <th>Type</th>
							                  <th>Required</th>
							                  <th>Description</th>
							               </tr>
							            </thead>
							            <tbody>
							               <tr>
							                  <td>product_ids</td>
							                  <td><code>integer</code></td>
							                  <td><code>proudct id</code></td>
							                  <td>-</td>
							               </tr> 
							               <tr>
							                  <td>order_id</td>
							                  <td><code>integer</code></td>
							                  <td><code>order number</code></td>
							                  <td>-</td>
							               </tr>
							               <tr>
							                  <td>order_currency</td>
							                  <td><code>string</code></td>
							                  <td><code>currrency code  like USA,INR</code></td>
							                  <td>-</td>
							               </tr>
							               <tr>
							                  <td>order_total</td>
							                  <td><code>decimal</code></td>
							                  <td><code>order total</code></td>
							                  <td>-</td>
							               </tr>
							               <tr>
							                  <td>customFields</td>
							                  <td><code>json array</code></td>
							                  <td><code>[{"city":"cityName"},
							                  {"countryName":"countryName"}]</code></td>
							                  <td>-</td>
							               </tr>
							               <tr>
							                  <td>base_url</td>
							                  <td><code>string</code></td>
							                  <td><code>target url</code></td>
							                  <td>Get Target Link from General Setting link and Convert it to base64_encode format and then assign it to base_url</td>
							               </tr>

							               <tr>
							                  <td>current_page_url</td>
							                  <td><code>string</code></td>
							                  <td><code>page url</code></td>
							                  <td>client url of from this API is called and Convert to base 64 encode and then assigned to current_page_url</td>
							               </tr>

							               <tr>
							                  <td>af_id</td>
							                  <td><code>string</code></td>
							                  <td><code>page url</code></td>
							                  <td>Affiliate Id from external link url ex.  NzdtSnkyMklYTWlXU1hIMDhCdkcydz09-Mi0yMA==</td>
							               </tr>
							               <tr>
							                  <td>script_name</td>
							                  <td><code>string</code></td>
							                  <td><code>general_integration</code></td>
							                  <td>-</td>
							               </tr>
							                  
							            </tbody>
							         </table>
							      	</div>
								</div>
								<div class="col-sm-6">	 
									<h3 class="panel-title">Php Code Example :  </h3>
 										<br/>
<pre class="response-view" style="background-color: #272822;color:#fff">

$customFields= '[{"city":"cityName"},{"countryName":"countryName"}]'; //optional

//Url of api caller
$current_page_url= "http://example.com/callapi.php"; 

//Replace this url with Target URL
$base_url = "http://localhost/aff/client/site.php"; 

$af_id = "NzdtSnkyMklYTWlXU1hIMDhCdkcydz09-Mi0yMA==";
$script_name = "general_integration";

$postData = [];

$current_page_url = base64_encode($current_page_url);
$base_url = base64_encode($base_url);


$postData['product_ids'] = 101; 
$postData['order_id'] = 1200; 
$postData['order_total'] = 120; 
$postData['order_currency'] ='USD';  
$postData['customFields'] = $customFields; 
$postData['current_page_url'] = $current_page_url; 
$postData['base_url'] = $base_url; 
$postData['af_id'] = $af_id; 
$postData['script_name'] = $script_name;

$url='<?= base_url('integration/addOrder');?>';
$curl = curl_init($url);
$request = http_build_query($postData);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, $request);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_HEADER, false);
curl_setopt($curl, CURLOPT_TIMEOUT, 30);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
$response=curl_exec($curl);

$response => "OKS-OFF" if success 
</pre>
								 
							</div>

						</div>

					<!-- order click integration  -->
						<div class="row"> 
							<div class="col-sm-6">
									<h3 class="panel-title">Order product click conversion</h3>
									<p>Refer to the below parameters are identical for order click conversion integration.
									</p>
									
 										<h3 class="panel-title">Request :  </h3>
 										<br/>
 										<span class="text-warning">POST</span>
 										 : <?php echo base_url('integration/addClick'); ?>
									<div class="panel-body" style="overflow: auto;">
							         <table class="table table-hover">
							            <thead>
							               <tr>
							                  <th>Parameter</th>
							                  <th>Type</th>
							                  <th>Value</th>
							                  <th>Description</th>
							               </tr>
							            </thead>
							            <tbody>
							               <tr>
							                  <td>product_id</td>
							                  <td><code>string</code></td>
							                  <td><code>ProductID</code></td>
							                  <td>Pass static value "ProductID"</td>
							               </tr>
							               <tr>
							                  <td>customFields</td>
							                  <td><code>json array</code></td>
							                  <td><code>[{"city":"cityName"},
							                  {"countryName":"countryName"}]</code></td>
							                  <td>-</td>
							               </tr>
							               <tr>
							                  <td>base_url</td>
							                  <td><code>string</code></td>
							                  <td><code>target url</code></td>
							                  <td>Get Target Link from General Setting link and Convert it to base64_encode format and then assign it to base_url</td>
							               </tr>

							               <tr>
							                  <td>current_page_url</td>
							                  <td><code>string</code></td>
							                  <td><code>page url</code></td>
							                  <td>client url of from the this api is called and Convert it to base 64 encode and then assign to current_page_url</td>
							               </tr>

							               <tr>
							                  <td>af_id</td>
							                  <td><code>string</code></td>
							                  <td><code>affiliate Id</code></td>
							                  <td>Affiliate Id from external link url ex.  NzdtSnkyMklYTWlXU1hIMDhCdkcydz09-Mi0yMA==</td>
							               </tr>
							               <tr>
							                  <td>script_name</td>
							                  <td><code>string</code></td>
							                  <td><code>general_integration</code></td>
							                  <td>-</td>
							               </tr>
							                  
							            </tbody>
							         </table>
							      	</div>
								</div>
								<div class="col-sm-6">	 
									<h3 class="panel-title">Php Code Example :  </h3>
 										<br/>
<pre class="response-view" style="background-color: #272822;color:#fff">

$product_id="ProductID";
$customFields= '[{"city":"cityName"},{"countryName":"countryName"}]'; //optional
//Url of api caller
$current_page_url= "http://example.com/callapi.php"; 

//Replace this url with Target URL
$base_url = "http://localhost/aff/client/site.php";
$af_id = "NzdtSnkyMklYTWlXU1hIMDhCdkcydz09-Mi0yMA==";
$script_name = "general_integration";

$postData = [];

$current_page_url = base64_encode($current_page_url);
$base_url = base64_encode($base_url);


$postData['product_id'] = $product_id; 
$postData['customFields'] = $customFields; 
$postData['current_page_url'] = $current_page_url; 
$postData['base_url'] = $base_url; 
$postData['af_id'] = $af_id; 
$postData['script_name'] = $script_name;

$url='<?php echo base_url('integration/addClick');?>;'
$curl = curl_init($url);
$request = http_build_query($postData);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, $request);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_HEADER, false);
curl_setopt($curl, CURLOPT_TIMEOUT, 30);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
$response=curl_exec($curl);

$response => "OK" if success 
</pre>
								 
							</div>
						</div>

				</div>
				<!-- -->

				<div class="tab-pane col-sm-12 fade" id="menu1">
					<div class="form-group">
						<label class="control-label"><?=  __('user.commission_type') ?> </label>
						<select class="form-control" name="commission_type">
							<option <?= (isset($tool) && $tool['commission_type'] == 'default') ? 'selected' : '' ?> value="default" ><?=  __('user.default') ?></option>
							<option <?= (isset($tool) && $tool['commission_type'] == 'custom') ? 'selected' : '' ?> value="custom"><?=  __('user.custom') ?></option>
							<option value="disabled" <?= (isset($tool) && $tool['commission_type'] == 'disabled') ? 'selected' : '' ?>><?= __('admin.disabled') ?></option>
						</select>
					</div>

					<div class="default-mlm"  <?= ($tool['commission_type'] != 'custom' && $tool['commission_type'] != 'disabled') ? '' : 'style="display:none;"' ?>>
						<div class="table-responsive">
							<table class="table" id="tbl_refer_level">
								<thead>
									<tr>
										<th style="vertical-align: top; border-right: 1px solid lightgrey;"><?= __('user.level_mlm') ?></th>
										<th style="vertical-align: top; border-right: 1px solid lightgrey; text-align: center;">
											<?= __('user.cps_cost') ?><br>
											<?php if ($default['referlevel']['sale_type'] == 'percentage'): ?>
												<span class="form-control"><?= __('user.percentage') ?></span>
											<?php endif ?>
											<?php if ($default['referlevel']['sale_type'] == 'fixed'): ?>
												<span class="form-control"><?= __('user.fixed') ?></span>
											<?php endif ?>
										</th>
										<th style="vertical-align: top; border-right: 1px solid lightgrey; text-align: center;" colspan="2"><?= __('admin.clicks_count') ?> &amp; <?= __('user.cpc_cost') ?></th>
										<th style="vertical-align: top; text-align: center;"><?= __('user.cpa_cost') ?></th>
									</tr>
								</thead>
								<tbody>
									<?php $default_levels = isset($default['referlevel']['levels']) ? (int)$default['referlevel']['levels'] : 3;
									for ($level =1; $level <= $default_levels; $level++) { ?>
										<tr>
											<td style="border-right: 0.1px solid lightgrey;"><?= $level ?></td>
											<td style="border-right: 0.1px solid lightgrey;">
												<div class="input-group">
													<span class="form-control"><?php echo $default['referlevel_'.$level]['sale_commition'] ?></span>
													<div class="input-group-append"><span class="input-group-text refer-symball"></span></div>
												</div>
											</td>
											<td><span class="form-control"><?php echo $default['referlevel_'.$level]['commition'] ?></span>
												<td style="border-right: 0.1px solid lightgrey;">
													<div class="input-group">
														<span class="form-control"><?php echo $default['referlevel_'.$level]['ex_commition'] ?></span>
														<div class="input-group-append"><span class="input-group-text"><?= $CurrencySymbol ?></span></div>
													</div>
												</td>
												<td>
													<div class="input-group">
														<span class="form-control"><?php echo $default['referlevel_'.$level]['ex_action_commition'] ?></span>
														<div class="input-group-append"><span class="input-group-text"><?= $CurrencySymbol ?></span></div>
													</div>
												</td>
											</tr>
										<?php } ?>
									</tbody>
								</table>
							</div>
						</div>

						<div class="commi-cube" <?= ($tool['commission_type'] != 'custom') ? 'style="display:none;"' : '' ?>>
							<div class="new-comm">
								<div class="form-group">
									<label class="control-label"><?= __('admin.refer_level') ?></label>
									<select class="form-control" id="referlevel_select" name="referlevel[levels]">
										<option <?= $levels == "1" ? 'selected' : '' ?> value="1">1</option>
										<option <?= $levels == "2" ? 'selected' : '' ?> value="2">2</option>
										<option <?= $levels == "3" ? 'selected' : '' ?> value="3">3</option>
										<option <?= $levels == "4" ? 'selected' : '' ?> value="4">4</option>
										<option <?= $levels == "5" ? 'selected' : '' ?> value="5">5</option>
										<option <?= $levels == "6" ? 'selected' : '' ?> value="6">6</option>
										<option <?= $levels == "7" ? 'selected' : '' ?> value="7">7</option>
										<option <?= $levels == "8" ? 'selected' : '' ?> value="8">8</option>
										<option <?= $levels == "9" ? 'selected' : '' ?> value="9">9</option>
										<option <?= $levels == "10" ? 'selected' : '' ?> value="10">10</option>
										<option <?= $levels == "11" ? 'selected' : '' ?> value="11">11</option>
										<option <?= $levels == "12" ? 'selected' : '' ?> value="12">12</option>
										<option <?= $levels == "13" ? 'selected' : '' ?> value="13">13</option>
										<option <?= $levels == "14" ? 'selected' : '' ?> value="14">14</option>
										<option <?= $levels == "15" ? 'selected' : '' ?> value="15">15</option>
										<option <?= $levels == "16" ? 'selected' : '' ?> value="16">16</option>
										<option <?= $levels == "17" ? 'selected' : '' ?> value="17">17</option>
										<option <?= $levels == "18" ? 'selected' : '' ?> value="18">18</option>
										<option <?= $levels == "19" ? 'selected' : '' ?> value="19">19</option>
										<option <?= $levels == "20" ? 'selected' : '' ?> value="20">20</option>
									</select>
								</div>
								<div class="table-responsive">
									<table class="table" id="tbl_refer_level">
										<thead>
											<tr>
												<th style="vertical-align: top; border-right: 1px solid lightgrey;"><?= __('admin.level_mlm') ?></th>
												<th style="vertical-align: top; border-right: 1px solid lightgrey; text-align: center;">
													<?= __('admin.cps_cost') ?><br>
													<select class="form-control refer-symball-select w-100 mt-2" name="referlevel[sale_type]">
														<option symbal='%' <?php if($referlevel['sale_type'] == 'percentage') { ?> selected <?php } ?> value="percentage"><?= __('admin.percentage') ?></option>
														<option symbal='<?= $CurrencySymbol ?>' <?php if($referlevel['sale_type'] == 'fixed') { ?> selected <?php } ?>  value="fixed"><?= __('admin.fixed') ?></option>
													</select>
												</th>
												<th style="vertical-align: top; border-right: 1px solid lightgrey; text-align: center;" colspan="2"><?= __('admin.clicks_count') ?> &amp; <?= __('admin.cpc_cost') ?></th>
												<th style="vertical-align: top; text-align: center;"><?= __('admin.cpa_cost') ?></th>
											</tr>
										</thead>
										<tbody>
											<?php for ($level=1; $level <= $levels; $level++) { ?>
												<tr>
													<td><?= $level ?></td>
													<td style="border-right: 0.1px solid lightgrey;">
														<div class="input-group">
															<input type="number" step="any" name="referlevel_<?= $level ?>[sale_commition]" value="<?php echo ${"referlevel_". $level}['sale_commition'] ?>" class="form-control" />
															<div class="input-group-append"><span class="input-group-text refer-symball"></span></div>
														</div>
													</td>
													<td><input type="number" step="any" name="referlevel_<?= $level ?>[commition]" value="<?php echo ${"referlevel_". $level}['commition'] ?>" class="form-control" /></td>
													<td style="border-right: 0.1px solid lightgrey;">
														<div class="input-group">
															<input type="number" step="any" name="referlevel_<?= $level ?>[ex_commition]" value="<?php echo ${"referlevel_". $level}['ex_commition'] ?>" class="form-control" />
															<div class="input-group-append"><span class="input-group-text"><?= $CurrencySymbol ?></span></div>
														</div>
													</td>
													<td>
														<div class="input-group">
															<input type="number" step="any" name="referlevel_<?= $level ?>[ex_action_commition]" value="<?php echo ${"referlevel_". $level}['ex_action_commition'] ?>" class="form-control" />
															<div class="input-group-append"><span class="input-group-text"><?= $CurrencySymbol ?></span></div>
														</div>
													</td>
												</tr>
											<?php } ?>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>
			</form>	
		</div>

		<div class="card-footer text-right">
			<?php if(isset($tool['id'])){ ?>
				<a class="get-code btn btn-info" href="javascript:void(0)" data-id="<?= $tool['id'] ?>"><?= __('user.get_code') ?></a>
			<?php } ?>
			<!-- <button class="btn btn-primary btn-save save-n-close"><span class="loading-submit"></span> <?= __('user.save') ?></button> -->
			<button class="btn btn-primary btn-save "><span class="loading-submit"></span> <?= __('user.save_and_close') ?></button>
		</div>
	</div>
</div>
</div>

<div class="modal fade" id="integration-code">
<div class="modal-dialog">
	<div class="modal-content"></div>
</div>
</div>

<div class="modal fade" id="addProgram">
<div class="modal-dialog modal-xl">
	<div class="modal-content">
		<div class="modal-header">
			<h4 class="modal-title mt-0"><?= __('user.add_program') ?></h4>
			<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
		</div>
		<div class="modal-body">
			<form action="" method="post">
				<input type="hidden" name="add_program_to_form" value="1">
				<div class="row">
					<div class="col">
						<div class="form-group">
							<label class="control-label"><?= __('admin.program_name') ?></label>
							<input class="form-control" name="name" type="text">
						</div>

						<fieldset class="custom-design mb-2">
							<legend><?= __('user.admin_commission') ?></legend>
							<?php 
							$programs['admin_click_status'] = $market_vendor['click_status'];
							$programs['admin_commission_click_commission'] = $market_vendor['commission_click_commission'];
							$programs['admin_commission_number_of_click'] = $market_vendor['commission_number_of_click'];
							$programs['admin_sale_status'] = $market_vendor['sale_status'];
							$programs['admin_commission_type'] = $market_vendor['commission_type'];
							$programs['admin_commission_sale'] = $market_vendor['commission_sale'];
							?>
							<div class="row">
								<div class="col">
									<div class="form-group mb-1">
										<label class="control-label"><?= __('user.click_commission') ?> : </label> 
										<?php if($programs['admin_click_status']){ ?>
											<span><?= c_format($programs['admin_commission_click_commission']) ?> <?= __('user.per') ?> <?= (int)$programs['admin_commission_number_of_click'] ?> <?= __('user.clicks') ?></span>
										<?php } else {?>
											<span><?= __('user.disabled') ?></span>
										<?php } ?>
									</div>
								</div>
								<div class="col">
									<div class="form-group mb-1">
										<label class="control-label"><?= __('user.sale_commission') ?> : </label> 
										<?php if($programs['admin_sale_status']){ ?>
											<span> 
												<?php 
												if($programs['admin_commission_type'] == 'percentage'){
													echo (float)$programs['admin_commission_sale']."%";
												}
												else if($programs['admin_commission_type'] == 'fixed'){
													echo c_format($programs['admin_commission_sale']);
												} else{
													echo __('user.not_set');
												}
												?>
											</span>
										<?php } else {?>
											<span><?= __('user.disabled') ?></span>
										<?php } ?>
									</div>
								</div>
							</div>
						</fieldset>


					</div>
					<div class="col">
						<div class="card mt-3">
							<div class="card-header "><p class="m-0"><?= __('user.vendor_commnts') ?></p></div>
							<div class="card-body chat-card">
								<div class="bg-white form-group m-0 p-2">
									<textarea class="form-control" placeholder="<?= __('user.enter_message_and_save_program_to_send') ?>" name="comment"></textarea>
								</div>

							</div>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-sm-6">
						<div class="custom-card card">
							<div class="card-header"><p class="text-center"><?= __('admin.other_affiliate_sale_settings') ?></p></div>
							<div class="card-body">
								<div class="row">
									<div class="col-sm-6">
										<div class="form-group">
											<label class="control-label"><?= __('user.commission_type') ?></label>
											<select name="commission_type" class="form-control">
												<option value=""><?= __('admin.select_product_commission_type') ?></option>
												<option value="percentage"><?= __('user.percentage') ?></option>
												<option value="fixed"><?= __('user.fixed') ?></option>
											</select>
										</div>
									</div>
									<div class="col-sm-6">
										<div class="form-group">
											<label class="control-label"><?= __('admin.commission_for_sale') ?> </label>
											<input class="form-control only-number-allow" name="commission_sale" type="text">
										</div>
									</div>
								</div>

								<div class="form-group">
									<label class="control-label"><?= __('admin.sale_status') ?></label>
									<div>
										<div class="radio radio-inline"> 
											<label> 
												<input type="radio" checked="" name="sale_status" value="0"> <?= __('admin.disable') ?> 
											</label> 
										</div>
										<div class="radio radio-inline"> 
											<label> 
												<input type="radio" name="sale_status" value="1"> <?= __('admin.enable') ?> 
											</label> 
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>

					<div class="col-sm-6">
						<div class="custom-card card">
							<div class="card-header"><p class="text-center"><?= __('admin.other_affiliate_click_settings') ?></p></div>
							<div class="card-body">
								<div class="row">
									<div class="col-sm-12">
										<div class="form-group">
											<label class="control-label"><?= __('user.clicks_allow') ?></label>
											<select name="click_allow" class="form-control">
												<option value="multiple"><?= __('user.allow_multi_clicks') ?></option>
												<option value="single"><?= __('user.allow_single_click') ?></option>
											</select>
										</div>
									</div>

									<div class="col-sm-6">
										<div class="form-group">
											<label class="control-label"><?= __('admin.number_of_click') ?></label>
											<input class="form-control only-number-allow" name="commission_number_of_click" type="text">
										</div>
									</div>
									<div class="col-sm-6">
										<div class="form-group">
											<label class="control-label"><?= __('admin.amount_per_click') ?></label>
											<input class="form-control only-number-allow" name="commission_click_commission" type="text">
										</div>
									</div>
								</div>


								<div class="form-group">
									<label class="control-label"><?= __('admin.click_status') ?></label>
									<div>
										<div class="radio radio-inline"> 
											<label> 
												<input type="radio" checked="" name="click_status" value="0"> <?= __('admin.disable') ?>
											</label>
										</div>
										<div class="radio radio-inline"> 
											<label> 
												<input type="radio" name="click_status" value="1"> <?= __('admin.enable') ?> 
											</label> 
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</form>
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-primary addProgramToFrom"><?= __('user.save_close') ?></button>
			<button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?= __('user.footer_close') ?></button>
		</div>
	</div>
</div>
</div>

<script type="text/javascript" src="<?= base_url('assets/plugins/ui/jquery-ui.min.js') ?>"></script>
<link rel="stylesheet" type="text/css" href="<?= base_url("assets/plugins/ui/jquery-ui.min.css") ?>">
<script type="text/javascript">

$('.datetime-picker').datetimepicker({
	format:'d-m-Y H:i',
});

render_tool_period_inputs();

$(document).on('change', 'select[name="tool_period"]', render_tool_period_inputs);

function render_tool_period_inputs(){
	var tool_period = $('select[name="tool_period"]').val();

	if( tool_period == 1){
		$('#start_date_input').hide();
		$('#end_date_input').hide();
	}else if (tool_period == 2){
		$('#start_date_input').hide();
		$('#end_date_input').show();
	} else if (tool_period == 3) {
		$('#start_date_input').show();
		$('#end_date_input').hide();
	} else {
		$('#start_date_input').show();
		$('#end_date_input').show();			
	}

	$('#endtime').datetimepicker({
		format:'d-m-Y H:i',
		inline:true,
	});

	$('.datetime-picker').datetimepicker({
		format:'d-m-Y H:i',
	});
};

$('#endtime').datetimepicker({
	format:'d-m-Y H:i',
	inline:true,
});

$('#setCustomTime').on('change', function(){
	$(".custom_time_container").hide();
	if($(this).prop("checked")){
		$(".custom_time_container").show();
	}
});

$("select[name=commission_type]").on('change',function(){
	if($(this).val() == 'custom'){
		$(".default-mlm").hide();
		$(".commi-cube").show();
	} else if($(this).val() == 'default'){
		$(".commi-cube").hide();
		$(".default-mlm").show();
	} else {
		$(".commi-cube").hide();
		$(".default-mlm").hide();
	}
})

function chnage_teigger() {
	var symbal = $(".refer-symball-select").find("option:selected").attr("symbal");
	$(".refer-symball").html(symbal);
}
$(".refer-symball-select").change(chnage_teigger)
chnage_teigger();

$('[name="tool_type"]').on('change',function(){

	$(".for-action-tool, .for-program-tool, .for-general_click-tool").hide();
	var click_value = "<?= isset($tool) ? $tool['action_click'] : '' ?>";
	let type = $(this).val();
	if(type == 'single_action'){
		$('.for-action-tool [name="action_click"]').val(1);	
		$('.for-action-tool [name="action_click"]').attr('readonly', 'readonly');	
		$(".for-action-tool").show();	
	}else if(type == 'action'){
		$('.for-action-tool [name="action_click"]').val(click_value);	
		$('.for-action-tool [name="action_click"]').removeAttr('readonly');	
		$(".for-action-tool").show();
	}else{
		$(".for-"+ $(this).val() +"-tool").show();
	}

	if(type != 'program'){
		$('[name="tool_integration_plugin"]').val("");
	}

	rendeCampignDefaultImages();
});

$('[name="tool_integration_plugin"]').on('change',function(){
	rendeCampignDefaultImages();
});

function rendeCampignDefaultImages() {
	let type = $('[name="tool_type"]').val();

	let featured_image = 'no_product_image.png';

	if(type == 'single_action' || type == 'action'){
		featured_image = 'plugins_icons/action.jpg';
	} else if(type == 'general_click') {
		featured_image = 'plugins_icons/click.jpg';
	} else if(type == 'program'){

		let program = $('[name="tool_integration_plugin"]').val();
		switch (program){
			case 'woocommerce':
			featured_image = 'plugins_icons/woo.png';
			break;
			case 'prestashop':
			featured_image = 'plugins_icons/prestashop.png';
			break;
			case 'opencart':
			featured_image = 'plugins_icons/opencart.png';
			break;
			case 'magento':
			featured_image = 'plugins_icons/magento.png';
			break;
			case 'shopify':
			featured_image = 'plugins_icons/shopify.png';
			break;
			case 'bigcommerce':
			featured_image = 'plugins_icons/Big-Commerce.jpg';
			break;
			case 'paypal':
			featured_image = 'plugins_icons/paypal.png';
			break;
			case 'oscommerce':
			featured_image = 'plugins_icons/oscommerce.png';
			break;
			case 'zencart':
			featured_image = 'plugins_icons/zencart.png';
			break;
			case 'xcart':
			featured_image = 'plugins_icons/xcart.png';
			break;
			case 'laravel':
			featured_image = 'plugins_icons/laravel.png';
			break;
			case 'cakephp':
			featured_image = 'plugins_icons/cackphp.png';
			break;
			case 'codeigniter':
			featured_image = 'plugins_icons/codeigniter.png';
			break;
			default:
			featured_image = 'plugins_icons/order.jpg';
		}

	}

	$('.campaign_default_image').attr('src', '<?= base_url('assets/images/')?>'+featured_image);

	var image = new Image();
	image.src = '<?= base_url('assets/images/')?>'+featured_image;
	$(image).one('load',function(){
		var width = image.width;
		var height = image.height;
		$('input[name="custom_banner_size[]"]').val(width + 'x' + height);
	});
}


$('[name="tool_type"]').trigger("change");

$("#addProgram .addProgramToFrom").on('click',function(){
	$this = $("#addProgram form");

	$.ajax({
		url:'<?= base_url('usercontrol/editProgram') ?>',
		type:'POST',
		dataType:'json',
		data:$this.serialize(),
		success:function(result){
			$this.find(".has-error").removeClass("has-error");
			$this.find("span.text-danger").remove();

			if(result['message']){
				if(result['newOption'])
					$("select[name='program_id']").append(result['newOption']);

				$this[0].reset();

				alert(result['message']);
				$("#addProgram").modal('hide');
			} else {
				if(result['errors']){
					$.each(result['errors'], function(i,j){
						$ele = $this.find('[name="'+ i +'"]');
						if($ele){
							$ele.parents(".form-group").addClass("has-error");
							$ele.after("<span class='text-danger'>"+ j +"</span>");
						}
					});
				}
			}
		},
	})
})

$(".parse-video").on('keyup',function(){
	var url = $(this).val();
	url.match(/(http:|https:|)\/\/(player.|www.)?(vimeo\.com|youtu(be\.com|\.be|be\.googleapis\.com))\/(video\/|embed\/|watch\?v=|v\/)?([A-Za-z0-9._%-]*)(\&\S+)?/);

	if (RegExp.$3.indexOf('youtu') > -1) {
		var type = 'Youtube';
	} else if (RegExp.$3.indexOf('vimeo') > -1) {
		var type = 'Vimeo';
	}

	$(".video-priview").val(type);
})
$(".parse-video").trigger("keyup");


$(".add-banner").on('click',function(){
	if($(".banner-table tbody tr").length < 5){

		$(".banner-table tbody").append('<tr>\
			<td>\
			<img class="img-thumbnail campaign_default_image" width="100px" src="<?= base_url('assets/images/no_product_image.png'); ?>">\
			<input type="file" accept="image/*" class="file-input" name="custom_banner[]">\
			<input type="hidden" name="keep_ads[]" value="0">\
			</td>\
			<td><input type="text" class="form-control size-input" readonly="" name="custom_banner_size[]"></td>\
			<td><button type="button" class="btn btn-sm btn-danger remove-custom-image"><i class="fa fa-trash"></i></button></td>\
			</tr>');
	}

	if($(".banner-table tbody tr").length >= 5){
		$(".add-banner").hide();
	}

	rendeCampignDefaultImages();
})

$(".banner-table tbody").delegate(".remove-custom-image","click",function(){
	if(!confirm('<?= __('user.are_you_sure') ?>')) return false;

	$(".add-banner").show();
	$(this).parents("tr").remove();
})

$(".banner-table tbody").delegate(".file-input","change",function(){
	var input = this;
	$this = $(this);

	if (input.files && input.files[0]) {
		var reader = new FileReader();

		reader.onload = function(e) {
			$tr = $this.parents("tr");
			var img = new Image;

			img.onload = function() {
				$tr.find(".size-input").val( img.width + " x " + img.height );
			};
			img.src = e.target.result;
			$tr.find("img").attr('src', e.target.result)
			$tr.find("[name=keep_ads]").val('0');
		}

		reader.readAsDataURL(input.files[0]);
	}
});


$(".btn-save").on('click',function(){
	$btn = $(this);
	$this = $("#form_tools");

	var formData = new FormData($this[0]);
	if($(this).hasClass('save-n-close')){
		formData.append("save_close",true);
	}
	formData = formDataFilter(formData);
	$btn.prop("disabled",true);


	$.ajax({
		url:'<?= base_url('usercontrol/integration_tools_form_post') ?>',
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
					$btn.find('.loading-submit').text(percentComplete + "%").show();
				}
			}, false );

			jqXHR.addEventListener( "progress", function ( evt ){
				if ( evt.lengthComputable ){
					var percentComplete = Math.round( (evt.loaded * 100) / evt.total );
					$btn.find('.loading-submit').hide();
				}
			}, false );
			return jqXHR;
		},
		error:function(){
			$btn.find('.loading-submit').hide();
			$btn.prop("disabled",false);
		},
		success:function(result){
			$btn.find('.loading-submit').hide();
			$btn.prop("disabled",false);
			$this.find(".has-error").removeClass("has-error");
			$this.find("span.text-danger").remove();

			if(result['location']){ window.location = result['location']; }

			if(result['errors']){
				$.each(result['errors'], function(i,j){
					if(i == 'custom_banner[]') {
						$.each(j, function(key,err){
							$ele = $('input[name="'+ i +'"]').get(key.split('-')[1]);
							if($ele){
								$($ele).parent().find('.text-danger').remove();
								$($ele).parent().append("<span class='text-danger'>"+ err +"</span>");
							}
						});
					} else {
						$ele = $this.find('[name="'+ i +'"]');
						if(!$ele.length) $ele = $this.find('.'+ i)
							if($ele){
								$ele.parents(".form-group").addClass("has-error");
								$ele.after("<span class='text-danger'>"+ j +"</span>");
							}

						}
					});
			}

			if(result['error']){
				Swal.fire({
					icon: 'error',
					html: result.error,
				});
			}
		},
	})
});

$(document).on('change', '#recursion_type', function(){
	var recursion_type = $(this).val();     

	if( recursion_type == 'custom_time' ){
		$('.custom_time').show();
	}else{
		$('.custom_time').hide();
	}
});

$(document).on('change', '#recur_day, #recur_hour, #recur_minute', function(){
	var days = $('#recur_day').val();
	var hours = $('#recur_hour').val();
	var minutes = $('#recur_minute').val();
	var total_minutes;      

	total_hours = parseInt(days*24) + parseInt(hours);
	total_minutes = parseInt(total_hours*60) + parseInt(minutes);
	$('.custom_time').find('input[name="recursion_custom_time"]').val(total_minutes);

});

$(".get-code").on('click',function(){
	$this = $(this);
	$.ajax({
		url:'<?= base_url("usercontrol/tool_get_code") ?>',
		type:'POST',
		dataType:'json',
		data:{id:$this.attr("data-id")},
		beforeSend:function(){ $this.btn("loading"); },
		complete:function(){ $this.btn("reset"); },
		success:function(json){
			if(json['html']){
				$("#integration-code .modal-content").html(json['html']);
				$("#integration-code").modal("show");
			}
		},
	})
});

var cache ={};
$("#category_auto").autocomplete({
	source: function( request, response ) {
		var term = request.term;
		if ( term in cache ) {response( cache[ term ] );return;}

		$.getJSON( '<?= base_url('usercontrol/integration_category_auto') ?>', request, function( data, status, xhr ) {
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
})

var levels = {};
<?php 
for ($i=1; $i <= 20; $i++) { 
	$v = 'referlevel_'.$i;
	if (isset(${$v})) { ?>
		levels['<?= $i ?>'] = <?= json_encode(${$v}) ?>;
	<?php }
}
?>

$('#referlevel_select').on('change',function(){
	var level =  $(this).val();

	var html = '';
	for(var i = 1; i <= level; i++){
		html += '<tr>';
		html += '<td style="border-right: 1px solid lightgrey;">'+i+'</td>';
		html += '<td style="border-right: 1px solid lightgrey;"><div class="input-group"><input type="number" step="any" name="referlevel_'+i+'[sale_commition]" value="'+(levels[i] ? levels[i]['sale_commition'] : '' )+'" class="form-control" /><div class="input-group-append"><span class="input-group-text refer-symball"></span></div>															</div></td>';
		html += '<td><input type="number" step="any" name="referlevel_'+i+'[commition]" value="'+(levels[i] ? levels[i]['commition'] : '' )+'" class="form-control" /></td>';
		html += '<td style="border-right: 1px solid lightgrey;"><div class="input-group"><input type="number" step="any" name="referlevel_'+i+'[ex_commition]" value="'+(levels[i] ? levels[i]['ex_commition'] : '' )+'" class="form-control" /><div class="input-group-append"><span class="input-group-text"><?= $CurrencySymbol ?></span></div></div></td>';
		html += '<td><div class="input-group"><input type="number" step="any" name="referlevel_'+i+'[ex_action_commition]" value="'+(levels[i] ? levels[i]['ex_action_commition'] : '' )+'" class="form-control" /><div class="input-group-append"><span class="input-group-text"><?= $CurrencySymbol ?></span></div></div></td>';
		html += '</tr>';
	}

	$('#tbl_refer_level tbody').html(html);

	chnage_teigger();
});
$(document).on('click','.edit-comment', function(){
	var id = $(this).data('id');
	var comment_content = $('.comment-content-'+id).text();
	$('#comment-box').text(comment_content);
	$('#updateid').val(id);
	$('#btnUpdateArea').removeClass('d-none');
});
$(document).on('click','#btnUpdate',function(){
	var comment_content = $('#comment-box').val();
	$this = $(this);
	if(comment_content.trim() !=""){
		var id = $('#updateid').val();
		$('.comment-content-'+id).text($('#comment-box').val());
		var tool_id = window.location.href.split("/").pop();

		$.ajax({
			url:'<?= base_url("usercontrol/updateComment") ?>',
			type:'POST',
			dataType:'json',
			data:{id:id,comment:comment_content,tool_id},
			beforeSend:function(){ $this.btn("loading"); },
			complete:function(){ $this.btn("reset"); },
			success:function(json){
				console.log(json)
				$('#btnUpdateArea').addClass('d-none');
				$('#comment-box').val('')
				$('#updateid').val('');
			},
		})

	} else {
		alert("Can't send blank message")
	}
});

function GeneratenNewCode(codeinput)
{
	$program_tool_id=$("#program_tool_id").val();
	$tool_type=$("#tool_type").val();
	$.ajax({
			url:'<?= base_url("integration/generateRandomCodeApi") ?>',
			type:'POST',
			dataType:'json',
			data:{tool_type:$tool_type,program_tool_id:$program_tool_id},
			beforeSend:function(){ 
			 },
			complete:function(){ 
			},
			success:function(json){

				 $('#'+codeinput).val(json);
			},
		})
}
</script>

<script>
$(document).on('change', 'select.cookies_type_select', function(){
	if($(this).val() == 1) {
		$('.cookies_type_input').show();
	} else {
		$('.cookies_type_input').hide();
	}
});
</script>