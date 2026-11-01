<?php if($mlm_status){ ?>	
	<div class="card">
		<div class="card-header bg-secondary text-white">
			<h5><?= __('admin.mlm_settings') ?></h5>
		</div>
		<div class="card-body">
			<form class="form-horizontal" autocomplete="off" method="post" action=""  enctype="multipart/form-data" id="setting-form">
				<div class="row">
					<div class="col-sm-12">
						<div class="tab-content">
							<div class="form-group">
								<label class="control-label"><?= __('admin.status') ?></label>
<div class="d-flex">
    <div class="form-check form-check-inline">
        <input class="form-check-input referlevel_status" type="radio" name="referlevel[status]" id="enable" value="1" <?= (int)$referlevel['status'] == 1 ? 'checked' : '' ?>>
        <label class="form-check-label" for="enable"><?= __('admin.enable') ?></label>
    </div>
    
    <div class="form-check form-check-inline">
        <input class="form-check-input referlevel_status" type="radio" name="referlevel[status]" id="disabled" value="0" <?= (int)$referlevel['status'] == 0 ? 'checked' : '' ?>>
        <label class="form-check-label" for="disabled"><?= __('admin.disabled') ?></label>
    </div>
    
    <div class="form-check form-check-inline">
        <input class="form-check-input referlevel_status" type="radio" name="referlevel[status]" id="disable_only_for_selected_users" value="2" <?= (int)$referlevel['status'] == 2 ? 'checked' : '' ?>>
        <label class="form-check-label" for="disable_only_for_selected_users"><?= __('admin.disable_only_for_selected_users') ?></label>
    </div>
</div>



								<h4 class="notification_on_pages div-toggle status-1">
									<span class="badge bg-info mt-2">
										<?= __('admin.enable_for_all_users') ?>
									</span>
								</h4>
								<h4 class="notification_on_pages div-toggle status-0">
									<span class="badge bg-secondary mt-2">
										<?= __('admin.disable_for_all_users') ?>
									</span>
								</h4>

								<div class="bg-light border rounded p-3 mb-3 notification_on_pages div-toggle status-2">
								    <h4 class="mb-2 text-dark">
								        <span class="badge bg-secondary"><?= __('admin.disable_only_for_selected_users') ?></span>
								    </h4>
								    <?php
								        $_selected = json_decode( (isset($referlevel['disabled_for']) ? $referlevel['disabled_for'] : '[]') , 1);
								    ?>
								    <div class="overflow-auto" style="max-height: 200px;">
								        <ul class="list-unstyled">
								            <?php foreach ($users_list as $key => $value) { ?>
								                <li class="form-check">
								                    <input class="form-check-input" <?= in_array($value['id'], $_selected) ? 'checked' : '' ?> type="checkbox" name="referlevel[disabled_for][]" value="<?= $value['id'] ?>" id="disabledFor<?= $value['id'] ?>">
								                    <label class="form-check-label" for="disabledFor<?= $value['id'] ?>">
								                        <?= $value['name'] ?>
								                    </label>
								                </li>
								            <?php } ?>
								        </ul>
								    </div>
								</div>

								<script type="text/javascript">
									$('.referlevel_status').on('change',function(){
										$(".div-toggle").hide();
										$(".div-toggle.status-"+ $('.referlevel_status:checked').val()).show();
									})

									$('.referlevel_status:checked').trigger('change')
								</script>
							</div>

<div class="mb-3">
    <label class="form-label"><?= __('admin.local_store_refer_sale_commission') ?></label>
    <select class="form-select" name="referlevel[autoacceptlocalstore]">
        <option value="0"><?= __('admin.on_hold') ?></option>
        <option value="1" <?= $referlevel['autoacceptlocalstore'] ? 'selected' : '' ?>><?= __('admin.in_wallet') ?></option>
    </select>
</div>

<div class="mb-3">
    <label class="form-label"><?= __('admin.external_store_refer_sale_commission') ?></label>
    <select class="form-select" name="referlevel[autoacceptexternalstore]">
        <option value="0"><?= __('admin.on_hold') ?></option>
        <option value="1" <?= $referlevel['autoacceptexternalstore'] ? 'selected' : '' ?>><?= __('admin.in_wallet') ?></option>
    </select>
</div>

<div class="mb-3">
    <label class="form-label"><?= __('admin.action_refer_commission') ?></label>
    <select class="form-select" name="referlevel[autoacceptaction]">
        <option value="0"><?= __('admin.on_hold') ?></option>
        <option value="1" <?= $referlevel['autoacceptaction'] ? 'selected' : '' ?>><?= __('admin.in_wallet') ?></option>
    </select>
</div>
    
<div class="mb-3">
    <label class="form-label"><?= __('admin.show_sponser') ?></label>
    <select class="form-select" name="referlevel[show_sponser]">
        <option value=""><?= __('admin.show_admin_as_sponser') ?></option>
        <option <?= $referlevel['show_sponser'] == 'none' ? 'selected' : '' ?> value="none"><?= __('admin.not_show') ?></option>
        <option <?= $referlevel['show_sponser'] == 'real_sponser' ? 'selected' : '' ?> value="real_sponser"><?= __('admin.real_sponser') ?></option>
    </select>
</div>

<div class="mb-3">
    <label class="form-label"><?= __('admin.sponser_name') ?></label>
    <input name="referlevel[sponser_name]" value="<?= $referlevel['sponser_name']; ?>" class="form-control" type="text">
</div>


<?php if(false){ ?>
	<div class="row g-3">
		<div class="col-lg-3">
			<div class="card p-3">
				<div class="mb-3">
					<label class="form-label"><?= __('admin.no_of_click_per_commission') ?></label>
					<input name="referlevel[click]" value="<?= $referlevel['click']; ?>" class="form-control" step="any" type="number" placeholder='<?= __('admin.no_of_click_per_commission') ?>'>
				</div>
				<?php foreach (array('1','2','3') as $key => $v) { ?>
					<fieldset>
						<legend><?= __('admin.level') ?> <?= $v ?>:</legend>
						<div class="mb-3">
							<label class="form-label"><?= __('admin.refer_setting_click_commission') ?> (<?= $CurrencySymbol ?>)</label>
							<input name="referlevel_<?= $v ?>[commition]" value="<?= ${"referlevel_$v"}['commition']; ?>" class="form-control" step="any" type="number">
						</div>
					</fieldset>
				<?php } ?>
			</div>
		</div>
		<div class="col-lg-3">
			<div class="card p-3">
				<div class="mb-3">
					<label class="form-label"><?= __('admin.fix_amount_or_per') ?></label>
					<select class="form-select refer-symball-select" name="referlevel[sale_type]">
						<option symbal='%' <?= $referlevel['sale_type'] == 'percentage' ? 'selected' : '' ?> value="percentage"><?= __('admin.percentage') ?></option>
						<option symbal='<?= $CurrencySymbol ?>' <?= $referlevel['sale_type'] == 'fixed' ? 'selected' : '' ?>  value="fixed"><?= __('admin.fixed') ?></option>
					</select>
				</div>
				<?php foreach (array('1','2','3') as $key => $v) { ?>
					<fieldset>
						<legend><?= __('admin.level') ?> <?= $v ?>:</legend>
						<div class="mb-3">
							<label class="form-label"><?= __('admin.refer_setting_sale_commission') ?> (<span class="refer-symball"></span>)</label>
							<input name="referlevel_<?= $v ?>[sale_commition]" value="<?= ${"referlevel_$v"}['sale_commition']; ?>" class="form-control" step="any" type="number">
						</div>
					</fieldset>
				<?php } ?>
			</div>
		</div>
		<div class="col-lg-3">
			<div class="card p-3">
				<div class="mb-3">
					<label class="form-label"><?= __('admin.external_click') ?></label>
					<input name="referlevel[ex_click]" value="<?= $referlevel['ex_click']; ?>" class="form-control" step="any" type="number" placeholder='<?= __('admin.external_click') ?>'>
				</div>
				<?php foreach (array('1','2','3') as $key => $v) { ?>
					<fieldset>
						<legend><?= __('admin.level') ?> <?= $v ?>:</legend>
						<div class="mb-3">
							<label  class="form-label"><?= __('admin.external_click_commission') ?>  (<?= $CurrencySymbol ?>)</label>
							<input name="referlevel_<?= $v ?>[ex_commition]" value="<?= ${"referlevel_$v"}['ex_commition']; ?>" class="form-control" step="any" type="number">
						</div>
					</fieldset>
				<?php } ?>
			</div>
		</div>
		<div class="col-lg-3">
			<div class="card p-3">
				<div class="mb-3">
					<label class="form-label"><?= __('admin.external_action_click') ?></label>
					<input name="referlevel[ex_action_click]" value="<?= $referlevel['ex_action_click']; ?>" class="form-control" step="any" type="number" placeholder='<?= __('admin.external_action_click') ?>'>
				</div>
				<?php foreach (array('1','2','3') as $key => $v) { ?>
					<fieldset>
						<legend><?= __('admin.level') ?> <?= $v ?>:</legend>
						<div class="mb-3">
							<label  class="form-label"><?= __('admin.external_action_click_Commission') ?>  (<?= $CurrencySymbol ?>)</label>
							<input name="referlevel_<?= $v ?>[ex_action_commition]" value="<?= ${"referlevel_$v"}['ex_action_commition']; ?>" class="form-control" step="any" type="number">
						</div>
					</fieldset>
				<?php } ?>
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

<?php } else { ?>
	<div class="row">
		<div class="col-12">
			<div class="border border-info p-3 text-info">
				<span><?= __('admin.mlm_module_is_off') ?></span>
				<a href="<?= base_url('admincontrol/addons') ?>" class="text-info"><?= __('admin.admin_click_here_to_activate') ?></a>
			</div>
		</div>
	</div>
<?php } ?>

