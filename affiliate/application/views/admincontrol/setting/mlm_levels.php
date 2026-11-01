<?php if($mlm_status){ ?>	
	<div class="card">
		<div class="card-header bg-secondary text-white">
			<h5><?= __('admin.mlm_levels') ?></h5>
		</div>
		<div class="card-body">
			<form class="form-horizontal" autocomplete="off" method="post" action=""  enctype="multipart/form-data" id="setting-form">
				<div class="row">
					<div class="col-sm-12">
						<div class="tab-content">
							<?php $levels = isset($referlevel['levels']) ? (int)$referlevel['levels'] : 3;  ?>
							<div class="form-group">
								<label class="control-label"><?= __('admin.refer_level') ?></label>
								<select class="form-control" id="referlevel_select" name="referlevel[levels]">
									<option <?= $levels == "1" ? 'selected': '' ?> value="1">1</option>
									<option <?= $levels == "2" ? 'selected': '' ?> value="2">2</option>
									<option <?= $levels == "3" ? 'selected': '' ?> value="3">3</option>
									<option <?= $levels == "4" ? 'selected': '' ?> value="4">4</option>
									<option <?= $levels == "5" ? 'selected': '' ?> value="5">5</option>
									<option <?= $levels == "6" ? 'selected': '' ?> value="6">6</option>
									<option <?= $levels == "7" ? 'selected': '' ?> value="7">7</option>
									<option <?= $levels == "8" ? 'selected': '' ?> value="8">8</option>
									<option <?= $levels == "9" ? 'selected': '' ?> value="9">9</option>
									<option <?= $levels == "10" ? 'selected': '' ?> value="10">10</option>
									<option <?= $levels == "11" ? 'selected': '' ?> value="11">11</option>
									<option <?= $levels == "12" ? 'selected': '' ?> value="12">12</option>
									<option <?= $levels == "13" ? 'selected': '' ?> value="13">13</option>
									<option <?= $levels == "14" ? 'selected': '' ?> value="14">14</option>
									<option <?= $levels == "15" ? 'selected': '' ?> value="15">15</option>
									<option <?= $levels == "16" ? 'selected': '' ?> value="16">16</option>
									<option <?= $levels == "17" ? 'selected': '' ?> value="17">17</option>
									<option <?= $levels == "18" ? 'selected': '' ?> value="18">18</option>
									<option <?= $levels == "19" ? 'selected': '' ?> value="19">19</option>
									<option <?= $levels == "20" ? 'selected': '' ?> value="20">20</option>
								</select>
							</div>

				<div class="card text-dark bg-light mt-3 mb-3 notification-card">
				  <div class="card-header"><?= __('admin.notification') ?></div>
				  <div class="card-body">
				    <h4 class="notification_on_pages reg_notis reg_percentage_notis">
				      <span class="badge bg-secondary text-wrap" style="max-width: 100%;">
				        <?= __('admin.registration_comission_type_1_notif') ?>
				      </span>
				    </h4>

				    <h4 class="notification_on_pages reg_notis reg_custom_percentage_notis">
				      <span class="badge bg-secondary text-wrap" style="max-width: 100%;">
				        <?= __('admin.registration_comission_type_2_notif') ?>
				      </span>
				    </h4>

				    <h4 class="notification_on_pages reg_notis reg_fixed_notis">
				      <span class="badge bg-secondary text-wrap" style="max-width: 100%;">
				        <?= __('admin.registration_comission_type_3_notif') ?>
				      </span>
				    </h4>
				  </div>
				</div>


<div class="new-comm mt-3">
    <div class="table-responsive">
        <table class="table" id="tbl_refer_level">
            <thead>
                <tr>
                    <th class="text-center border p-3"><?= __('admin.level_mlm') ?></th>
                    <th class="text-center border-end p-3">
                        <div class="row">
                            <div class="col-12">
                                <?= __('admin.cpr_cost') ?>
                            </div>
                            <div class="col-12">
                                <select class="form-control refer-reg-symball-select d-block w-100 mt-2" name="referlevel[reg_comission_type]">
                                    <option value="disabled" <?php if($referlevel['reg_comission_type'] == 'disabled') { ?> selected <?php } ?>><?= __('admin.select_registration_commission_plan') ?></option>
                                    <option symbal='%' <?php if($referlevel['reg_comission_type'] == 'percentage') { ?> selected <?php } ?> value="percentage"><?= __('admin.membership_registration_commission_perce') ?></option>
                                    <option symbal='%' <?php if($referlevel['reg_comission_type'] == 'custom_percentage') { ?> selected <?php } ?> value="custom_percentage"><?= __('admin.registration_custom_commission_amount_perce') ?></option>
                                    <option symbal='<?= $CurrencySymbol ?>' <?php if($referlevel['reg_comission_type'] == 'fixed') { ?> selected <?php } ?>  value="fixed"><?= __('admin.registration_fixed_amount') ?></option>
                                </select>
                                <input class="w-100 mt-2" type="number" name="referlevel[reg_comission_custom_amt]" value="<?php echo isset($referlevel['reg_comission_custom_amt']) ? $referlevel['reg_comission_custom_amt'] : 0;?>" placeholder="custom commission ammount" />
                            </div>
                        </div>
                    </th>
                    <th class="text-center border-end p-3">
                        <div class="row">
                            <div class="col-12">
                                <?= __('admin.cps_cost') ?>
                            </div>
                            <div class="col-12">
                                <select class="form-control refer-symball-select w-100 mt-2" name="referlevel[sale_type]">
                                    <option symbal='%' <?php if($referlevel['sale_type'] == 'percentage') { ?> selected <?php } ?> value="percentage"><?= __('admin.percentage') ?></option>
                                    <option symbal='<?= $CurrencySymbol ?>' <?php if($referlevel['sale_type'] == 'fixed') { ?> selected <?php } ?>  value="fixed"><?= __('admin.fixed') ?></option>
                                </select>
                            </div>
                        </div>
                    </th>
                    <th class="text-center border-end p-3" colspan="2">
                        <div class="row">
                            <div class="col-12">
                                <?= __('admin.clicks_count') ?> &amp; <?= __('admin.cpc_cost') ?>
                            </div>
                        </div>
                    </th>
                    <th class="text-center border p-3">
                        <div class="row">
                            <div class="col-12">
                                <?= __('admin.cpa_cost') ?>
                            </div>
                        </div>
                    </th>
                </tr>
            </thead>
            <tbody>

                <?php for ($level =1; $level <= $levels; $level++) { ?>
                    <tr>
                        <td class="border"><?= $level ?></td>
                        <td class="border-end">
                            <div class="input-group">
                                <input type="number" step="any" name="referlevel_<?= $level ?>[reg_commission]" value="<?php echo ${"referlevel_". $level}['reg_commission'] ?>" class="form-control" />
                                <div class="input-group-append"><span class="input-group-text refer-reg-symball"></span></div>
                            </div>
                        </td>
                        
                        <td class="border-end">
                            <div class="input-group">
                                <input type="number" step="any" name="referlevel_<?= $level ?>[sale_commition]" value="<?php echo ${"referlevel_". $level}['sale_commition'] ?>" class="form-control" />
                                <div class="input-group-append"><span class="input-group-text refer-symball"></span></div>
                            </div>
                        </td>
                        <td class="border-end"><input type="number" step="any" name="referlevel_<?= $level ?>[commition]" value="<?php echo ${"referlevel_". $level}['commition'] ?>" class="form-control" /></td>
                        <td class="border-end">
                            <div class="input-group">
                                <input type="number" step="any" name="referlevel_<?= $level ?>[ex_commition]" value="<?php echo ${"referlevel_". $level}['ex_commition'] ?>" class="form-control" />
                                <div class="input-group-append"><span class="input-group-text"><?= $CurrencySymbol ?></span></div>
                            </div>
                        </td>
                        <td class="border">
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
									<label  class="control-label"><?= __('admin.external_click_commission') ?> (<?= $CurrencySymbol ?></span>)</label>
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
						<input name="referlevel[ex_action_click]" value="<?php echo $referlevel['ex_action_click']; ?>" class="form-control" step="any" type="number" placeholder='External Action Click'>
					</div>
					<?php foreach (array('1','2','3') as $key => $v) { ?>
						<fieldset>
							<legend><?= __('admin.level') ?> <?= $v ?>:</legend>
								<div class="form-group">
									<label  class="control-label"><?= __('admin.external_action_click_Commission') ?> (<?= $CurrencySymbol ?></span>)</label>
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

    function chnage_teigger2() {
        var symbal = $(".refer-reg-symball-select").find("option:selected").attr("symbal");

        if($(".refer-reg-symball-select").val() == "disabled") {
            $(".refer-reg-symball").html('#');
        } else {
            $(".refer-reg-symball").html(symbal);
        }

        if($(".refer-reg-symball-select").val() != "custom_percentage") {
            $('input[name="referlevel[reg_comission_custom_amt]"]').hide();
        } else {
            $('input[name="referlevel[reg_comission_custom_amt]"]').show();
        }

        var selectedValue = $(".refer-reg-symball-select").val();
        if (selectedValue === 'percentage' || selectedValue === 'custom_percentage' || selectedValue === 'fixed') {
            $('.reg_notis').hide();
            $('.reg_'+ selectedValue +'_notis').show();
            // Show the notification card when a valid option is selected
            $('.notification-card').show();
        } else {
            // Hide the notification card when no or invalid option is selected
            $('.notification-card').hide();
        }
    }
    $(".refer-reg-symball-select").change(chnage_teigger2)
    chnage_teigger2();
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
				$(".input-affiliate_cookie").after("<div class='alert alert-danger alert-error'>"+'<?= __('admin.days_between_1_to_365') ?>'+"</div>");
			}
			if($("#setting-form .alert-error").length == 0) return true;
			return false;
		})

		$(".btn-submit").on('click',function(evt){
			if($(".refer-reg-symball-select").val() == "custom_percentage") {
				let reg_comission_custom_amt = $('input[name="referlevel[reg_comission_custom_amt]"]').val();
				if(reg_comission_custom_amt == '' || reg_comission_custom_amt == 0) {
					$(".refer-reg-symball-select").parent().append('<span class="text-danger d-block">'+'<?= __('admin.custom_comission_amount_should_be_greater_than_0') ?>'+'<span>');
					return false;
				}
			}

			$(".refer-reg-symball-select").parent().find('.text-danger').remove();

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
				html += '<td style="border-right: 1px solid lightgrey;"><div class="input-group"><input type="number" step="any" name="referlevel_'+i+'[reg_commission]" value="'+(levels[i] ? levels[i]['reg_commission'] : '' )+'" class="form-control" /><div class="input-group-append"><span class="input-group-text refer-reg-symball"></span></div>															</div></td>';
				html += '<td style="border-right: 1px solid lightgrey;"><div class="input-group"><input type="number" step="any" name="referlevel_'+i+'[sale_commition]" value="'+(levels[i] ? levels[i]['sale_commition'] : '' )+'" class="form-control" /><div class="input-group-append"><span class="input-group-text refer-symball"></span></div>															</div></td>';
				html += '<td><input type="number" step="any" name="referlevel_'+i+'[commition]" value="'+(levels[i] ? levels[i]['commition'] : '' )+'" class="form-control" /></td>';
				html += '<td style="border-right: 1px solid lightgrey;"><div class="input-group"><input type="number" step="any" name="referlevel_'+i+'[ex_commition]" value="'+(levels[i] ? levels[i]['ex_commition'] : '' )+'" class="form-control" /><div class="input-group-append"><span class="input-group-text"><?= $CurrencySymbol ?></span></div></div></td>';
				html += '<td><div class="input-group"><input type="number" step="any" name="referlevel_'+i+'[ex_action_commition]" value="'+(levels[i] ? levels[i]['ex_action_commition'] : '' )+'" class="form-control" /><div class="input-group-append"><span class="input-group-text"><?= $CurrencySymbol ?></span></div></div></td>';
			html += '</tr>';
		}

		$('#tbl_refer_level tbody').html(html);
			chnage_teigger();
			chnage_teigger2();
		});
	</script>
<?php } else { ?>
	<div class="row">
		<div class="col-12">
			<div class="alert alert-info">
				<span><?= __('admin.mlm_module_is_off') ?></span>
				<a href="<?= base_url('admincontrol/addons') ?>"><?= __('admin.admin_click_here_to_activate') ?></a>
			</div>
		</div>
	</div>
<?php } ?>