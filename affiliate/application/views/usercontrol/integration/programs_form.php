<div class="row">
			<div class="col-12">
				<div class="card">
					<div class="card-header">
						<div>
							<h5 class="pull-left"><?= __('admin.integration_programs') ?></h5>
							<div class="pull-right">
								<a class="btn btn-primary btn-sm" href="<?= base_url('usercontrol/programs') ?>"><?= __('admin.back') ?></a>
							</div>
						</div>
					</div>

					<div class="card-body">
						<form action="" method="get" id="form_program">
							<div class="row">
								<div class="col">
									<div class="form-group mb-3">
				                        <label class="control-label"><?= __('user.status'); ?> : </label>
				                        <?= program_status($programs['status']) ?>	
				                    </div>

				                    <?php if($programs['status'] == '0'){ ?>
					                    <div class="alert alert-warning in_review-status">
				                        	<?= __('user.when_program_under_review') ?>
				                        </div> 
				                    <?php } ?>

									<input name="program_id" type="hidden" value="<?= isset($programs) ? $programs['id'] : '0' ?>">
									<div class="form-group">
										<label class="control-label"><?= __('admin.program_name') ?></label>
										<input class="form-control" name="name" type="text" value="<?= isset($programs) ? $programs['name'] : '' ?>">
									</div>

									<fieldset class="custom-design mb-2">
										<legend><?= __('user.admin_commission') ?></legend>
										<?php 
											if((int)$programs['id'] == 0){
												$programs['admin_click_status'] = $market_vendor['click_status'];
												$programs['admin_commission_click_commission'] = $market_vendor['commission_click_commission'];
												$programs['admin_commission_number_of_click'] = $market_vendor['commission_number_of_click'];
												$programs['admin_sale_status'] = $market_vendor['sale_status'];
												$programs['admin_commission_type'] = $market_vendor['commission_type'];
												$programs['admin_commission_sale'] = $market_vendor['commission_sale'];
											}
										?>
										<div class="row">
											<div class="col">
												<div class="form-group mb-1">
													<label class="control-label"><?= __('user.click_commission') ?> : </label> 
													<?php if($programs['admin_click_status']){ ?>
														<span><?= c_format($programs['admin_commission_click_commission']) ?> Per <?= (int)$programs['admin_commission_number_of_click'] ?> <?= __('user.clicks') ?></span>
													<?php } else {?>
														<span>Disabled</span>
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
																	echo "Not Set..";
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
											<?php $comment = json_decode($programs['comment'],1); ?>
											<?php if($comment){ ?>
												<ul class="comment-products">
													<?php foreach ($comment as $key => $value) { ?>
														<li class="<?= $value['from'] == 'affiliate' ? 'me' : 'other' ?>"> <div><?= $value['comment'] ?></div> </li>
													<?php } ?>
												</ul>
											<?php } ?>
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
														<label class="control-label"><?= __('admin.commission_type') ?></label>
														<select name="commission_type" class="form-control">
															<option value=""><?= __('admin.select_product_commission_type') ?></option>
															<option <?= (isset($programs) && $programs['commission_type'] == 'percentage') ? 'selected' : '' ?> value="percentage"><?= __('admin.percentage') ?></option>
															<option <?= (isset($programs) && $programs['commission_type'] == 'fixed') ? 'selected' : '' ?> value="fixed"><?= __('admin.fixed') ?></option>
														</select>
													</div>
												</div>
												<div class="col-sm-6">
													<div class="form-group">
														<label class="control-label"><?= __('admin.commission_for_sale') ?> </label>
														<div class="input-group">
															<div class="input-group-prepend">
																<span class="input-group-text">
																	<?= (isset($programs) && $programs['commission_type'] == 'percentage') ? '%'  : $CurrencySymbol ?>
																</span>
															</div>
															<input class="form-control only-number-allow" name="commission_sale" type="text" value="<?= isset($programs) ? $programs['commission_sale'] : '' ?>">
														</div>
													</div>
												</div>
											</div>

											<div class="form-group">
												<label class="control-label"><?= __('admin.sale_status') ?></label>
												<div>
													<div class="radio radio-inline"> <label> <input type="radio" checked="" name="sale_status" value="0"> <?= __('admin.disable') ?> </label> </div>
													<div class="radio radio-inline"> <label> <input <?= (isset($programs) && $programs['sale_status']) ? 'checked' : '' ?> type="radio" name="sale_status" value="1"> <?= __('admin.enable') ?> </label> </div>
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
																<option <?= (isset($programs) && $programs['click_allow'] == 'multiple') ? 'selected' : '' ?> value="multiple"><?= __('user.allow_multi_clicks') ?></option>
																<option <?= (isset($programs) && $programs['click_allow'] == 'single') ? 'selected' : '' ?> value="single"><?= __('user.allow_single_click') ?></option>
															</select>
														</div>
													</div>
														
												<div class="col-sm-6">
													<div class="form-group">
														<label class="control-label"><?= __('admin.number_of_click') ?></label>
														<input class="form-control only-number-allow" name="commission_number_of_click" type="text" value="<?= isset($programs) ? $programs['commission_number_of_click'] : '' ?>">
													</div>
												</div>
												<div class="col-sm-6">
													<div class="form-group">
														<label class="control-label"><?= __('admin.amount_per_click') ?></label>
														<div class="input-group">
															<div class="input-group-prepend">
																<span class="input-group-text"><?= $CurrencySymbol ?></span>
															</div>
															<input class="form-control only-number-allow" name="commission_click_commission" type="text" value="<?= isset($programs) ? $programs['commission_click_commission'] : '' ?>">
														</div>
													</div>
												</div>
											</div>
											

											<div class="form-group">
												<label class="control-label"><?= __('admin.click_status') ?></label>
												<div>
													<div class="radio radio-inline"> <label> <input type="radio" checked="" name="click_status" value="0"> <?= __('admin.disable') ?> </label> </div>
													<div class="radio radio-inline"> <label> <input type="radio" <?= (isset($programs) && $programs['click_status']) ? 'checked' : '' ?> name="click_status" value="1"> <?= __('admin.enable') ?> </label> </div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>

						</form>	
					</div>

					<div class="card-footer text-right">
						<button class="btn btn-primary btn-save"><?= __('admin.save') ?></button>
					</div>
				</div>
			</div>
		</div>


<script type="text/javascript">
	$("select[name='commission_type']").on('change',function(){
		if($(this).val() == 'percentage')
			$("input[name='commission_sale']").siblings('.input-group-prepend').find('.input-group-text').text('%');
		else
			$("input[name='commission_sale']").siblings('.input-group-prepend').find('.input-group-text').text('<?= $CurrencySymbol ?>');
	})

	$(".btn-save").on('click',function(){
	 	$this = $("#form_program");
	 	
		$.ajax({
            url:'<?= base_url('usercontrol/editProgram') ?>',
            type:'POST',
            dataType:'json',
            data:$this.serialize(),
            success:function(result){
                $this.find(".has-error").removeClass("has-error");
                $this.find("span.text-danger").remove();
                
                if(result['location']){ window.location = result['location']; }

                if(result['errors']){
                    $.each(result['errors'], function(i,j){
                        $ele = $this.find('[name="'+ i +'"]');
                        if($ele){
                            $ele.parents(".form-group").addClass("has-error");
                            $ele.after("<span class='text-danger'>"+ j +"</span>");
                        }
                    });
                }
            },
        })
	})
</script>