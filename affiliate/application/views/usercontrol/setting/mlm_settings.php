<div class="card">
	<div class="card-header">
		<?php include 'mlm_menu.php'; ?>
	</div>
	<div class="card-body">
		<form class="form-horizontal" autocomplete="off" method="post" action=""  enctype="multipart/form-data" id="setting-form">
			<div class="row">
				<div class="col-sm-12">
					<div class="tab-content">
						<div class="form-group">
							<label class="control-label"><?= __('admin.status') ?></label>
							<div class="radio-group">
								
								<label class="radio radio-inline"><input type="radio" class="referlevel_status" <?= (int)$referlevel['status'] == 
									1 ? 'checked' : '' ?> name="referlevel[status]" value="1" > <?= __('admin.enable') ?> </label>
								
								
								<label class="radio radio-inline"><input type="radio" class="referlevel_status" <?= (int)$referlevel['status'] == 
									0 ? 'checked' : '' ?> name="referlevel[status]" value="0" > <?= __('admin.disabled') ?> </label>
							</div>

							<h4 class="notification_on_pages div-toggle status-1">
								<span class="badge bg-info mt-2">
									<?= __('user.enable_for_all_users') ?>
								</span>
							</h4>

							<h4 class="notification_on_pages div-toggle status-0">
								<span class="badge bg-secondary mt-2">
									<?= __('user.disable_for_all_users') ?>
								</span>
							</h4>

							<script type="text/javascript">
								$('.referlevel_status').on('change',function(){
									$(".div-toggle").hide();
									$(".div-toggle.status-"+ $('.referlevel_status:checked').val()).show();
								})

								$('.referlevel_status:checked').trigger('change')
							</script>
						</div>
						<br>
						<div class="form-group">
							<label class="control-label"><?= __('admin.local_store_refer_sale_commission') ?></label>
							<select class="form-control" name="referlevel[autoacceptlocalstore]">
								<option value="0"><?= __('admin.on_hold') ?></option>
								<option value="1" <?= $referlevel['autoacceptlocalstore'] ? 'selected' : '' ?>><?= __('admin.in_wallet') ?></option>
							</select>
						</div>

						<div class="form-group">
							<label class="control-label"><?= __('admin.external_store_refer_sale_commission') ?></label>
							<select class="form-control" name="referlevel[autoacceptexternalstore]">
								<option value="0"><?= __('admin.on_hold') ?></option>
								<option value="1" <?= $referlevel['autoacceptexternalstore'] ? 'selected' : '' ?>><?= __('admin.in_wallet') ?></option>
							</select>
						</div>

						<div class="form-group">
							<label class="control-label"><?= __('admin.action_refer_commission') ?></label>
							<select class="form-control" name="referlevel[autoacceptaction]">
								<option value="0"><?= __('admin.on_hold') ?></option>
								<option value="1" <?= $referlevel['autoacceptaction'] ? 'selected' : '' ?>><?= __('admin.in_wallet') ?></option>
							</select>
						</div>
						
						<!-- <div class="form-group">
							<label class="control-label"><?= __('admin.show_sponser') ?></label>
							<select class="form-control" name="referlevel[show_sponser]">
								<option value=""><?= __('admin.show_admin_as_sponser') ?></option>
								<option <?= $referlevel['show_sponser'] == 'none' ? 'selected' : '' ?> value="none"><?= __('admin.not_show') ?></option>
								<option <?= $referlevel['show_sponser'] == 'real_sponser' ? 'selected' : '' ?> value="real_sponser"><?= __('admin.real_sponser') ?></option>
							</select>
						</div>
						<div class="form-group">
							<label  class="control-label"><?= __('admin.sponser_name') ?></label>
							<input name="referlevel[sponser_name]" value="<?php echo $referlevel['sponser_name']; ?>" class="form-control" type="text">
						</div> -->

						<?php if(false){ ?>
						<div class="commi-cube">
							<div class="row">
								<div class="col-sm-3">
									<div class="comm-cube-box">
										<div class="form-group">
											<label  class="control-label"><?= __('admin.no_of_click_per_commission') ?></label>
											<input name="referlevel[click]" value="<?php echo $referlevel['click']; ?>" class="form-control" step="any" type="number" placeholder='<?= __('admin.no_of_click_per_commission') ?>'>
										</div>
										<?php foreach (array('1','2','3') as $key => $v) { ?>
											<fieldset>
												<legend><?= __('admin.level') ?> <?= $v ?>:</legend>
												
													<div class="form-group">
														<label  class="control-label"><?= __('admin.refer_setting_click_commission') ?> (<?= $CurrencySymbol ?></span>)</label>
														<input name="referlevel_<?php echo $v ?>[commition]" value="<?php echo ${"referlevel_$v"}['commition']; ?>" class="form-control" step="any" type="number">
													</div>
											</fieldset>
										<?php } ?>
									</div>
								</div>
								<div class="col-sm-3">
									<div class="comm-cube-box">
										<div class="form-group">
											<label  class="control-label"><?= __('admin.fix_amount_or_per') ?></label>
											<select class="form-control refer-symball-select" name="referlevel[sale_type]">
												<option symbal='%' <?php if($referlevel['sale_type'] == 'percentage') { ?> selected <?php } ?> value="percentage"><?= __('admin.percentage') ?></option>
												<option symbal='<?= $CurrencySymbol ?>' <?php if($referlevel['sale_type'] == 'fixed') { ?> selected <?php } ?>  value="fixed"><?= __('admin.fixed') ?></option>
											</select>
										</div>
										<?php foreach (array('1','2','3') as $key => $v) { ?>
											<fieldset>
												<legend><?= __('admin.level') ?> <?= $v ?>:</legend>
													<div class="form-group">
														<label  class="control-label"><?= __('admin.refer_setting_sale_commission') ?> (<span class="refer-symball"></span>)</label>
														<input name="referlevel_<?php echo $v ?>[sale_commition]" value="<?php echo ${"referlevel_$v"}['sale_commition']; ?>" class="form-control" step="any" type="number">
													</div>
											</fieldset>
										<?php } ?>
									</div>
								</div>
								<div class="col-sm-3">
									<div class="comm-cube-box">
										<div class="form-group">
											<label  class="control-label"><?= __('admin.external_click') ?></label>
											<input name="referlevel[ex_click]" value="<?php echo $referlevel['ex_click']; ?>" class="form-control" step="any" type="number" placeholder='<?= __('admin.external_click') ?>'>
										</div>
										<?php foreach (array('1','2','3') as $key => $v) { ?>
											<fieldset>
												<legend><?= __('admin.level') ?> <?= $v ?>:</legend>
													<div class="form-group">
														<label  class="control-label"><?= __('admin.external_click_commission') ?>  (<?= $CurrencySymbol ?></span>)</label>
														<input name="referlevel_<?php echo $v ?>[ex_commition]" value="<?php echo ${"referlevel_$v"}['ex_commition']; ?>" class="form-control" step="any" type="number">
													</div>
											</fieldset>
										<?php } ?>
									</div>
								</div>
								<div class="col-sm-3">
									<div class="comm-cube-box">
										<div class="form-group">
											<label  class="control-label"><?= __('admin.external_action_click') ?></label>
											<input name="referlevel[ex_action_click]" value="<?php echo $referlevel['ex_action_click']; ?>" class="form-control" step="any" type="number" placeholder='<?= __('admin.external_action_click') ?>'>
										</div>
										<?php foreach (array('1','2','3') as $key => $v) { ?>
											<fieldset>
												<legend><?= __('admin.level') ?> <?= $v ?>:</legend>
													<div class="form-group">
														<label  class="control-label"><?= __('admin.external_action_click_Commission') ?>  (<?= $CurrencySymbol ?></span>)</label>
														<input name="referlevel_<?php echo $v ?>[ex_action_commition]" value="<?php echo ${"referlevel_$v"}['ex_action_commition']; ?>" class="form-control" step="any" type="number">
													</div>
											</fieldset>
										<?php } ?>
									</div>
								</div>
							</div>
						</div>
						<?php } ?>

						<script type="text/javascript">
							function chnage_teigger() {
								var symbal = $(".refer-symball-select").find("option:selected").attr("symbal");
								$(".refer-symball").html(symbal);
							}
							$(".refer-symball-select").change(chnage_teigger)
							chnage_teigger();
						</script>
					</div>
				</div>
				<div class="col-sm-12 text-right">
					<button type="submit" class="btn btn-sm btn-primary btn-submit"><?= __('admin.save_settings') ?></button>
				</div>
			</div>
		</form>
	</div>
</div>
<script type="text/javascript">
$("#setting-form").on('submit',function(){
	$("#setting-form .alert-error").remove();
	var affiliate_cookie = parseInt($(".input-affiliate_cookie").val());
	if(affiliate_cookie <= 0 || affiliate_cookie > 365){
		$(".input-affiliate_cookie").after("<div class='alert alert-danger alert-error'><?= __('admin.days_between_1_to_365') ?></div>");
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
</script>
