<?php if($saas_status){ ?>	
	<div class="card saas-settings">
		<div class="card-header bg-secondary text-white">
			<h5><?= __('admin.saas_settings') ?></h5>
		</div>
		<div class="card-body">
			<form class="form-horizontal" method="post" action="" enctype="multipart/form-data" id="setting-form">
			<div class="row">
				<div class="col-12">
					<ul class="nav nav-pills flex-column flex-sm-row" id="TabsNav">
						<li class="nav-item flex-sm-fill text-sm-center">
							<a class="nav-link active bg-secondary show" data-bs-toggle="tab" href="#market_vendor-setting" role="tab"><?= __('admin.market_tools_admin_fee') ?></a>
						</li>
						<li class="nav-item flex-sm-fill text-sm-center">
							<a class="nav-link" href="#vendor_setting" role="tab" data-bs-toggle="tab"><?= __('admin.store_admin_fee') ?></a>
						</li>
						<li class="nav-item flex-sm-fill text-sm-center">
							<a class="nav-link" href="#vendor_deposite_setting" role="tab" data-bs-toggle="tab"><?= __('admin.vendor_deposit_settings') ?></a>
						</li>
						<li class="nav-item flex-sm-fill text-sm-center">
							<a class="nav-link" href="#vendor_permission_setting" role="tab" data-bs-toggle="tab"><?= __('admin.vendor_permission_setting') ?></a>
						</li>
					</ul>
				</div>

		<div class="col-sm-12">
				<div class="tab-content">
					<div class="tab-pane p-3 active show" id="market_vendor-setting" role="tabpanel">
						<div class="form-group">
							<label class="control-label"><?= __('admin.vendor_status') ?></label>
							<div class="form-check form-switch">
								<input class="form-check-input update_all_settings" type="checkbox" <?= $market_vendor['marketvendorstatus']==1 ? 'checked' : '' ?> data-toggle="toggle" data-size="normal" data-on="<?= __('admin.status_on'); ?>" data-off="<?= __('admin.status_off'); ?>" data-setting_key="marketvendorstatus" data-setting_type="market_vendor">
							</div>
						</div>

						<div class="row">
							<div class="col-sm-6">
								<div class="card h-100">
									<div class="card-header bg-secondary text-white text-center">
										<h5><?= __('admin.admin_sale_settings_from_vendors') ?>
										</h5>
									</div>

									<div class="card-body">
										<div class="row">
											<div class="col-sm-6">
												<div class="form-group">
													<label class="control-label"><?= __('admin.commission_type') ?></label>
													<select name="market_vendor[commission_type]" class="form-control">
														<option value=""><?= __('admin.select_product_commission_type') ?></option>
														<option <?= ($market_vendor['commission_type'] == 'percentage') ? 'selected' : '' ?> value="percentage"><?= __('admin.percentage') ?></option>
														<option <?= ($market_vendor['commission_type'] == 'fixed') ? 'selected' : '' ?> value="fixed"><?= __('admin.fixed') ?></option>
													</select>
												</div>
											</div>
											<div class="col-sm-6">
												<div class="form-group">
													<label class="control-label">
														<?= __('admin.commission_for_sale') ?> 
													</label>
													<div class="input-group">
														<div class="input-group-prepend">
															<span class="input-group-text currency-symbol">
																<?= ($market_vendor['commission_type'] == 'percentage') ? '%'  : $CurrencySymbol ?>
															</span>
														</div>
														<input class="form-control" name="market_vendor[commission_sale]" type="number" value="<?= isset($market_vendor) ? $market_vendor['commission_sale'] : '' ?>">
													</div>
												</div>
											</div>
										</div>

										<div class="form-group">
											<label class="control-label"><?= __('admin.sale_status') ?></label>
											<div class="form-check form-switch">
												<input class="form-check-input update_all_settings" type="checkbox" <?= $market_vendor['sale_status']==1 ? 'checked' : '' ?> data-toggle="toggle" data-size="normal" data-on="<?= __('admin.status_on'); ?>" data-off="<?= __('admin.status_off'); ?>" data-setting_key="sale_status" data-setting_type="market_vendor">
											</div>
										</div>
									</div>
								</div>
							</div>

							<div class="col-sm-6">
								<div class="card h-100">
									<div class="card-header bg-secondary text-white text-center"><h5 class="text-center"><?= __('admin.admin_click_settings_from_vendors') ?></h5></div>

									<div class="card-body">
										
										<div class="row">
											<div class="col-sm-12">
												<div class="form-group">
													<label class="control-label"><?= __('admin.click_allow') ?></label>
													<select name="market_vendor[click_allow]" class="form-control">
														<option <?php if($market_vendor['click_allow'] == 'single') { ?> selected <?php } ?> value="single"><?= __('admin.allow_single_click') ?></option>
														<option <?php if($market_vendor['click_allow'] == 'multiple') { ?> selected <?php } ?>  value="multiple"><?= __('admin.allow_multi_click') ?></option>
													</select>
												</div>
											</div>
											<div class="col-sm-6">
												<div class="form-group">
													<label class="control-label"><?= __('admin.number_of_click') ?></label>
													<input class="form-control" name="market_vendor[commission_number_of_click]" type="number" value="<?= isset($market_vendor) ? $market_vendor['commission_number_of_click'] : '' ?>">
												</div>
											</div>
											<div class="col-sm-6">
												<div class="form-group">
													<label class="control-label"><?= __('admin.amount_per_click') ?></label>
													<div class="input-group">
														<div class="currency-symbol"><?= $CurrencySymbol ?></div>
														<input class="form-control" name="market_vendor[commission_click_commission]" type="number" value="<?= isset($market_vendor) ? $market_vendor['commission_click_commission'] : '' ?>">
													</div>
												</div>
											</div>
										</div>
										

										<div class="form-group">
											<label class="control-label"><?= __('admin.click_status') ?></label>
											<div class="form-check form-switch">
												<input class="form-check-input update_all_settings" type="checkbox" <?= $market_vendor['click_status']==1 ? 'checked' : '' ?> data-toggle="toggle" data-size="normal" data-on="<?= __('admin.status_on'); ?>" data-off="<?= __('admin.status_off'); ?>" data-setting_key="click_status" data-setting_type="market_vendor">
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>

					<div role="tabpanel" class="tab-pane p-3" id="vendor_setting">
						<div class="form-group">
							<label class="control-label"><?= __('admin.vendor_status') ?></label>
							<div class="form-check form-switch">
								<input class="form-check-input update_all_settings" type="checkbox" <?= $vendor['storestatus']==1 ? 'checked' : '' ?> data-toggle="toggle" data-size="normal" data-on="<?= __('admin.status_on'); ?>" data-off="<?= __('admin.status_off'); ?>" data-setting_key="storestatus" data-setting_type="vendor">
							</div>
						</div>
						<div class="row">
							<div class="col-sm-12">
								<div class="card">
									<div class="card-header bg-secondary text-white text-center"><h5 class="text-center">
										<?= __('admin.store_admin_fee_settings_from_vendors') ?></h5></div>

						<div class="card-body">
							<div class="form-group">
								<div class="row">
									<div class="col-sm-5">
										<label class="control-label"><?= __('admin.click_commission'); ?></label>
										<div class="form-group">
											<div class="input-group mt-2">
												<div class="input-group-prepend"><span class="input-group-text">
													<?= __('admin.click'); ?></span></div>
												<input name="vendor[admin_click_count]"  class="form-control" value="<?php echo $vendor['admin_click_count']; ?>" type="text" placeholder='<?= __('admin.clicks'); ?>'>
											</div>
										</div>
									</div>
									<div class="col-sm-5">
										<div class="form-group">
											<label class="control-label m-0 d-block">&nbsp;</label>
											<div class="input-group mt-2">
												<div class="currency-symbol mt-2"><?= $CurrencySymbol ?></div>
												<input name="vendor[admin_click_amount]" class="form-control mt-2" value="<?php echo c_format($vendor['admin_click_amount'],false); ?>" type="number" placeholder='<?= __('admin.amount'); ?>'>
											</div>
										</div>	
									</div>
									<div class="col-sm-2">
										<div class="form-group">
											<label class="control-label"><?= __('admin.status') ?></label>
											<div class="form-check form-switch">
												<input class="form-check-input update_all_settings" type="checkbox" <?= $vendor['admin_click_status']==1 ? 'checked' : '' ?> data-toggle="toggle" data-size="normal" data-on="<?= __('admin.status_on'); ?>" data-off="<?= __('admin.status_off'); ?>" data-setting_key="admin_click_status" data-setting_type="vendor">
											</div>
										</div>
									</div>

								</div>
							</div>

							<div class="form-group">
								<div class="row">
									<div class="col-sm-5">
										<label class="control-label"><?= __('admin.sale_commission'); ?></label>
										<div>
											<?php
												$commission_type= array(
													'percentage' => __('admin.percentage'),
													'fixed'      => __('admin.fixed'),
												);
											?>
											<select name="vendor[admin_sale_commission_type]" class="form-control admin_sale_commission_type">
												<?php foreach ($commission_type as $key => $value) { ?>
													<option <?= $vendor['admin_sale_commission_type'] == $key ? 'selected' : '' ?> value="<?= $key ?>"><?= $value ?></option>
												<?php } ?>
											</select>
										</div>
									</div>
									<div class="col-sm-5">
										<div class="toggle-container">
											<div class="percentage-value d-none">									<div class="form-group">
													<label class="control-label m-0 d-block">&nbsp;</label>
													<div class="input-group">
														<div class="currency-symbol mt-2"><?= $vendor['admin_sale_commission_type'] == 'percentage' ? '%' : $CurrencySymbol ?></div>
														<input name="vendor[admin_commission_value]" id="admin_commission_value" class="form-control mt-2" value="<?php echo $vendor['admin_commission_value']; ?>" type="number" placeholder='<?= __('admin.sale_commission') ?>'>
													</div>
												</div>
											</div>
										</div>
									</div>
									<div class="col-sm-2">
										<div class="form-group">
											<label class="control-label"><?= __('admin.status') ?></label>
											<div class="form-check form-switch">
												<input class="form-check-input update_all_settings" type="checkbox" <?= $vendor['admin_sale_status']==1 ? 'checked' : '' ?> data-toggle="toggle" data-size="normal" data-on="<?= __('admin.status_on'); ?>" data-off="<?= __('admin.status_off'); ?>" data-setting_key="admin_sale_status" data-setting_type="vendor">
											</div>
										</div>
									</div>
							</div>
												
											<script type="text/javascript">
												$("select.admin_sale_commission_type").on("change",function(){
													$con = $(this).parents(".form-group");
													$con.find(".toggle-container .percentage-value, .toggle-container .default-value").addClass('d-none');
													if($(this).val() == 'default'){
														$con.find(".toggle-container .default-value").removeClass("d-none");
													}else{
														$con.find(".toggle-container .percentage-value").removeClass("d-none");
													}

													if($(this).val() == 'percentage')
														$("input[name='vendor[admin_commission_value]']").siblings('.currency-symbol').text('%');
													else
														$("input[name='vendor[admin_commission_value]']").siblings('.currency-symbol').text('<?= $CurrencySymbol ?>');
												})

												$("select.admin_sale_commission_type").trigger("change");
											</script>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>

					<div role="tabpanel" class="tab-pane p-3" id="vendor_deposite_setting">
						<div class="form-group">

						<h5>
						    <div class="mybadge bg-info text-white mb-2 text-wrap rounded px-2 py-1 vendor-deposit-on-message <?= ($vendor['depositstatus']) ? '' : 'd-none' ?>">
						        <?= __('admin.vendor_deposit_on_message') ?>
						    </div>
						</h5>

						<h5>
						    <div class="mybadge bg-danger text-white rounded mb-2 text-wrap px-2 py-1 vendor-deposit-off-message <?= ($vendor['depositstatus']) ? 'd-none' : '' ?>">
						        <?= __('admin.vendor_deposit_off_message') ?>
						    </div>
						</h5>

							<label class="control-label">
								<?= __('admin.vendor_status') ?>
							</label>
							<div class="form-check form-switch">
								<input class="form-check-input update_all_settings" type="checkbox" <?= $vendor['depositstatus']==1 ? 'checked' : '' ?> data-toggle="toggle" data-size="normal" data-on="<?= __('admin.status_on'); ?>" data-off="<?= __('admin.status_off'); ?>" data-setting_key="depositstatus" data-setting_type="vendor">
							</div>
						</div>
						<div class="form-group">
							<label  class="control-label"><?= __('admin.vendor_min_deposit') ?></label>
							<div class="input-group">
								<div class="currency-symbol"><?= $CurrencySymbol ?></div>
								<input name="site[vendor_min_deposit]" value="<?php echo empty($site['vendor_min_deposit']) ? 0 : $site['vendor_min_deposit']; ?>" class="form-control" type="number">
							</div>
						</div>
					</div>

					<div role="tabpanel" class="tab-pane p-3" id="vendor_permission_setting">
						<div class="form-group">
							<div class="text-wrap rounded bg-info text-white mb-2 px-2 py-1">
							    <?= __('admin.admin_approval_for_vendor')?>
							</div>
					    	<div class="form-group row mt-4">
					    		<label class="control-label col-sm-2">
									<?= __('admin.vendor_market_tool') ?>
								</label>
								<label class="control-label col-sm-3">
									<?= __('admin.vendor_add_new_program') ?>
								</label>
								<div class="form-check form-switch col-sm-2">
									<input class="form-check-input update_all_settings" type="checkbox" <?= $market_vendor['marketaddnewprogram']==1 ? 'checked' : '' ?> data-toggle="toggle" data-size="normal" data-on="<?= __('admin.status_on'); ?>" data-off="<?= __('admin.status_off'); ?>" data-setting_key="marketaddnewprogram" data-setting_type="market_vendor">
								</div>
							</div>
							<div class="form-group row mt-4">
								<label class="control-label col-sm-2">
									<?= __('admin.vendor_market_tool') ?>
								</label>
								<label class="control-label col-sm-3">
									<?= __('admin.vendor_add_new_campaign') ?>
								</label>
								<div class="form-check form-switch col-sm-2">
									<input class="form-check-input update_all_settings" type="checkbox" <?= $market_vendor['marketaddnewcampaign']==1 ? 'checked' : '' ?> data-toggle="toggle" data-size="normal" data-on="<?= __('admin.status_on'); ?>" data-off="<?= __('admin.status_off'); ?>" data-setting_key="marketaddnewcampaign" data-setting_type="market_vendor">
								</div>
							</div>
							<div class="form-group row mt-4">
								<label class="control-label col-sm-2">
									<?= __('admin.vendor_market_tool') ?>
								</label>
								<label class="control-label col-sm-3">
									<?= __('admin.vendor_external_order_campaign') ?>
								</label>
								<div class="form-check form-switch col-sm-2">
									<input class="form-check-input update_all_settings" type="checkbox" <?= $market_vendor['marketvendorexternalordercampaign']==1 ? 'checked' : '' ?> data-toggle="toggle" data-size="normal" data-on="<?= __('admin.status_on'); ?>" data-off="<?= __('admin.status_off'); ?>" data-setting_key="marketvendorexternalordercampaign" data-setting_type="market_vendor">
								</div>
							</div>
							<div class="form-group row mt-4">
								<label class="control-label col-sm-2">
									<?= __('admin.vendor_market_tool') ?>
								</label>
								<label class="control-label col-sm-3">
									<?= __('admin.vendor_actions_campaign') ?>
								</label>
								<div class="form-check form-switch col-sm-2">
									<input class="form-check-input update_all_settings" type="checkbox" <?= $market_vendor['marketvendoractionscampaign']==1 ? 'checked' : '' ?> data-toggle="toggle" data-size="normal" data-on="<?= __('admin.status_on'); ?>" data-off="<?= __('admin.status_off'); ?>" data-setting_key="marketvendoractionscampaign" data-setting_type="market_vendor">
								</div>
							</div>
							<div class="form-group row mt-4">
								<label class="control-label col-sm-2">
									<?= __('admin.vendor_market_tool') ?>
								</label>
								<label class="control-label col-sm-3">
									<?= __('admin.vendor_click_campaign') ?>
								</label>
								<div class="form-check form-switch col-sm-2">
									<input class="form-check-input update_all_settings" type="checkbox" <?= $market_vendor['marketvendorclickcampaign']==1 ? 'checked' : '' ?> data-toggle="toggle" data-size="normal" data-on="<?= __('admin.status_on'); ?>" data-off="<?= __('admin.status_off'); ?>" data-setting_key="marketvendorclickcampaign" data-setting_type="market_vendor">
								</div>
							</div>
							<div class="form-group row mt-4">
								<label class="control-label col-sm-2">
									<?= __('admin.vendor_store_tool') ?>
								</label>
								<label class="control-label col-sm-3">
									<?= __('admin.vendor_add_new_store_product') ?>
								</label>
								<div class="form-check form-switch col-sm-2">
									<input class="form-check-input update_all_settings" type="checkbox" <?= $market_vendor['marketaddnewstoreproduct']==1 ? 'checked' : '' ?> data-toggle="toggle" data-size="normal" data-on="<?= __('admin.status_on'); ?>" data-off="<?= __('admin.status_off'); ?>" data-setting_key="marketaddnewstoreproduct" data-setting_type="market_vendor">
								</div>
							</div>

							<div class="form-group row mt-4">
								<label class="control-label col-sm-2">
									<?= __('admin.vendor_store_review_') ?>
								</label>
								<label class="control-label col-sm-3">
									<?= __('admin.allow_vendor_manage_review') ?>
								</label>
								<div class="form-check form-switch col-sm-2">
									<input class="form-check-input update_all_settings" type="checkbox" <?= $market_vendor['vendormanagereview']==1 ? 'checked' : '' ?> data-toggle="toggle" data-size="normal" data-on="<?= __('admin.status_on'); ?>" data-off="<?= __('admin.status_off'); ?>" data-setting_key="vendormanagereview" data-setting_type="market_vendor">
								</div>
							</div>
							<div class="form-group row mt-4">
								<label class="control-label col-sm-2">
									<?= __('admin.vendor_store_review_image') ?>
								</label>
								<label class="control-label col-sm-3">
									<?= __('admin.allow_vendor_manage_review_image') ?>
								</label>
								<div class="form-check form-switch col-sm-2">
									<input class="form-check-input update_all_settings" type="checkbox" <?= $market_vendor['vendormanagereviewimage']==1 ? 'checked' : '' ?> data-toggle="toggle" data-size="normal" data-on="<?= __('admin.status_on'); ?>" data-off="<?= __('admin.status_off'); ?>" data-setting_key="vendormanagereviewimage" data-setting_type="market_vendor">
								</div>
							</div>

							<div class="form-group row mt-4">
								<label class="control-label col-sm-2">
									<?= __('admin.vendor_mlm_module') ?>
								</label>
								<label class="control-label col-sm-3">
									<?= __('admin.allow_vendor_mlm_module') ?>
								</label>
								<div class="form-check form-switch col-sm-2">
									<input class="form-check-input update_all_settings" type="checkbox" <?= $market_vendor['vendormlmmodule']==1 ? 'checked' : '' ?> data-toggle="toggle" data-size="normal" data-on="<?= __('admin.status_on'); ?>" data-off="<?= __('admin.status_off'); ?>" data-setting_key="vendormlmmodule" data-setting_type="market_vendor">
								</div>
							</div>

							<div class="form-group row mt-4">
								<label class="control-label col-sm-2">
									<?= __('admin.vendor_panel_sections') ?>
								</label>
								<label class="control-label col-sm-3">
									<?= __('admin.enable_disable_marketlinks_topaffs_section') ?>
								</label>
								<div class="form-check form-switch col-sm-2">
									<input class="form-check-input update_all_settings" type="checkbox" <?= $market_vendor['marketvendorpanelmode']==1 ? 'checked' : '' ?> data-toggle="toggle" data-size="normal" data-on="<?= __('admin.status_on'); ?>" data-off="<?= __('admin.status_off'); ?>" data-setting_key="marketvendorpanelmode" data-setting_type="market_vendor">
								</div>
							</div>
						</div>
					</div>
										
					<div class="col-sm-12 text-right">
						<button type="submit" class="btn btn-sm btn-primary btn-submit"><?= __('admin.save_settings') ?></button>
					</div>
				</div>
			</div>
			</form>
		</div>
	</div>
</div>

	<script type="text/javascript">
		$("select[name='market_vendor[commission_type]']").on('change',function(){
			if($(this).val() == 'percentage')
				$("input[name='market_vendor[commission_sale]']").siblings('.input-group-prepend').find('.input-group-text').text('%');
			else
				$("input[name='market_vendor[commission_sale]']").siblings('.input-group-prepend').find('.input-group-text').text('<?= $CurrencySymbol ?>');
		})

		$("#setting-form").on('submit',function(){
			$("#setting-form .alert-error").remove();
			var affiliate_cookie = parseInt($(".input-affiliate_cookie").val());
			if(affiliate_cookie <= 0 || affiliate_cookie > 365){
				$(".input-affiliate_cookie").after("<div class='alert alert-danger alert-error'><?= __('admin.days_between_1_and_365'); ?></div>");
			}
			if($("#setting-form .alert-error").length == 0) return true;
			return false;
		})

		$(".btn-submit").on('click',function(evt){
		    evt.preventDefault();
		    var formData = new FormData($("#setting-form")[0]);

		    $(".btn-submit").btn("loading");
		    formData = formDataFilter(formData);
		    $this = $("#setting-form");
		   
		    $.ajax({
		        type:'POST',
		        dataType:'json',
		        cache:false,
		        contentType: false,
		        processData: false,
		        data:formData,
		        success:function(result){
		            $(".btn-submit").btn("reset");
		            $(".alert-dismissable").remove();

		            $this.find(".has-error").removeClass("has-error");
		            $this.find("span.text-danger").remove();
		            
		            if(result['location']){
		                window.location = result['location'];
		            }

		            if(result['success']){
		                showPrintMessage(result['success'],'success');
		                var body = $("html, body");
						body.stop().animate({scrollTop:0}, 500, 'swing', function() { });
		            }

		            if(result['errors']){
		                $.each(result['errors'], function(i,j){
		                    $ele = $this.find('[name="'+ i +'"]');
		                    if($ele){
		                        $ele.parents(".form-group").addClass("has-error");
		                        $ele.after("<span class='d-block text-danger'>"+ j +"</span>");
		                    }
		                });
		            }
		        },
		    })
		    return false;
		});

		$('.update_all_settings').on('change', function(){
			
			var checked = $(this).prop('checked');
			var setting_key = $(this).data('setting_key');
			var setting_type = $(this).data('setting_type');

			if (setting_key == 'depositstatus') {
				$('.mybadge').addClass('d-none');

				if(checked == true){
					$('.vendor-deposit-on-message').removeClass('d-none');
				}else{
					$('.vendor-deposit-off-message').removeClass('d-none');
				}
			}

			if (checked == true) {
				var status = 1;
			}else{
				var status = 0;
			}

			$.ajax({
				url:'<?= base_url("admincontrol/update_all_settings") ?>',
				type:'POST',
				dataType:'json',
				data:{'action':'update_all_settings', status:status, setting_key:setting_key, setting_type:setting_type},
				success:function(json){
				},
			})
		});
	</script>
<?php } else { ?>
	<div>
		<h4 class="notification_on_pages">
			<div class="well">
				<span class="badge bg-info"><?= __('admin.saas_module_is_off') ?></span>
				<a href="<?= base_url('admincontrol/addons') ?>"><?= __('admin.admin_click_here_to_activate') ?></a>
			</div>
		</h4>
	</div>
<?php } ?>
