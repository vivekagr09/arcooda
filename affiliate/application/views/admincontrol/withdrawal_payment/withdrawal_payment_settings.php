<div class="card">
	<div class="card-header bg-secondary text-white">
		<div class="card-title-white pull-left m-0"><?= __('admin.payment_gateway') ?> (<?= $details['title'] ?>)</div>
		<div class="card-title-white pull-right m-0">
			<button id="toggle-uploader" class="btn btn-light btn-submit"><?= __('admin.save_settings') ?></button>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-6">
			<div class="card-body">
				<form method="post" action="" id='form-setting'>
					<div class="form-group">
						<label class="form-control-label"><?= __('admin.status') ?></label>
						<select  name="status" class="form-control">
							<option value="0"><?= __('admin.disabled') ?></option>
							<option value="1" <?= $setting_data['status'] == "1" ? 'selected' : '' ?>><?= __('admin.enabled') ?></option>
						</select>
					</div>

					<?php
						if($details['code'] == "bank_transfer")
						{
							?>
								<div class="form-group">
									<label class="control-label"><?= __('admin.upload_withdrawal_proof_status') ?></label>
									<select class="form-control" name="withdrawal_proof">
										<option <?= (int)$setting_data['withdrawal_proof'] == '0' ? 'selected' : '' ?> value="0"><?= __('admin.disabled') ?></option>
										<option <?= (int)$setting_data['withdrawal_proof'] == '1' ? 'selected' : '' ?> value="1"><?= __('admin.enabled_and_optional') ?></option>
										<option <?= (int)$setting_data['withdrawal_proof'] == '2' ? 'selected' : '' ?> value="2"><?= __('admin.enabled_and_required') ?></option>
									</select>
								</div>

								<br/>
								
								<div class="row" id="custom-field-section">
								<?php
									if($setting_exist_status)
									{
										$count = 0;
										$response_validate = json_decode($get_custom_fiels['response_validate']);
										$get_custom_fiels = json_decode($get_custom_fiels['bt_custom_field']);
										foreach ($get_custom_fiels as $key => $cus_value) {
											$cus_value_read = str_replace("_"," ",$cus_value);
											$cus_value_read = ucfirst($cus_value_read);

											if(111==222)
											{
											?>
											<div class="col-md-12 row removediv">
												<div class="col-md-7">
												<div class="form-group">
													<input name="bt_custom_field[]" class="form-control bt_custom_field" type="text" value="<?=$cus_value_read;?>">
												</div>
												</div>
												<div class="col-md-2">
													<label class=""><?= __('admin.is_required') ?>
														</label>
													</div>
													<div class="col-md-2">
													<div class="form-group">
														
														<select class="form-control" name="response_validate[]">
															<option value="No" <?php if($response_validate[$count] == "No"){echo "selected";}?>><?= __('admin.no') ?></option>
															<option value="Yes" 
															<?php 
																if($response_validate[$count] == "Yes")
																{
																	echo "selected";
																}
															?>
															><?= __('admin.yes') ?></option>
														</select>
													</div>
												</div>
												<div class="col-md-1">
												<button type="button" class="btn btn-danger btn-md remove-field-btn" style="position: absolute; top: 0px; right: 11px;"><i class="fa fa-trash"></i></button>
												</div>
											</div>
											<?php
											}
											$count++;
										}
									}
									else
									{

										?>
										<div class="col-md-12 row removediv">
											<div class="col-md-7">
												<div class="form-group">
													<input name="bt_custom_field[]" class="form-control bt_custom_field" type="text" value="" placeholder="?= __('admin.please_enter_your_field_name') ?>">
												</div>
											</div>
											<div class="col-md-2">
													<label class=""><?= __('admin.is_required') ?>
														</label>
													</div>
											<div class="col-md-2">
												<div class="form-group">
													<select class="form-control" name="response_validate[]">
														<option value="No"><?= __('admin.no') ?></option>
														<option value="Yes"><?= __('admin.yes') ?></option>
													</select>
												</div>
											</div>
										</div>
										<?php
									}
								?>

								<div class="col-lg-12">

								<button id="add-more-field-btn" type="button" class="btn btn-primary" style="display: none;"><i class="fa fa-plus"></i> <?= __('admin.add_more_fields') ?></button>

							</div>
						</div>
							<?php
						}
					?>

					<?= $html  ?>
				</form>
			</div>
		</div>
		<div class="col-lg-6 payment-image"><img src="<?= base_url('/assets/images/payment-side2.jpg') ?>"></div>
	</div>
	
</div>

<script type="text/javascript">
	$(".btn-submit").click(function(){
		$this = $(this);

		$.ajax({
			url:'<?= base_url("admincontrol/withdrawal_payment_gateways_setting_save/". $details['code']) ?>',
			type:'POST',
			dataType:'json',
			data:$("#form-setting").serialize(),
			beforeSend:function(){
				$this.btn("loading");
			},
			complete:function(){
				$this.btn("reset");
			},
			success:function(json){
				$container = $("#form-setting");
				$container.find(".is-invalid").removeClass("is-invalid");
				$container.find("span.invalid-feedback").remove();

				if (json['redirect']) {
					window.location.href=json['redirect'];
				}
				
				if(json['errors']){
				    $.each(json['errors'], function(i,j){
				        $ele = $container.find('[name="'+ i +'"]');
				        if($ele){
				            $ele.addClass("is-invalid");
				            if($ele.parent(".input-group").length){
				                $ele.parent(".input-group").after("<span class='invalid-feedback'>"+ j[0] +"</span>");
				            } else{
				                $ele.after("<span class='invalid-feedback'>"+ j[0] +"</span>");
				            }
				        }
				    })
				}
			},
		})
	})

	$(document).on('click', '#add-more-field-btn', function(){

		let count = $('#custom-field-section .removediv').length;

		$(this).parent().before(`
		<div class = "col-md-12 row removediv">
		<div class="col-md-7">

			<div class="form-group">
				<input name="bt_custom_field[]" placeholder="`+'<?= __('admin.please_enter_your_field_name') ?>'+`" class="form-control bt_custom_field" type="text">

			</div>
			</div>
			<div class="col-md-2">
				<label class="">`+'<?= __('admin.is_required') ?>'+`
					</label>
				</div>
				<div class="col-md-2">
				<div class="form-group">
					
					<select class="form-control" name="response_validate[]">
						<option class="0">`+'<?= __('admin.no') ?>'+`</option>
						<option class="1">`+'<?= __('admin.yes') ?>'+`</option>
					</select>
				</div>
			</div>
				<div class="col-md-1">

			<button type="button" class="btn btn-danger btn-md remove-field-btn" style="position: absolute; top: 0px; right: 11px;"><i class="fa fa-trash"></i></button>
			</div>
		</div>`);

	});

	$(document).on('click', '.remove-field-btn', function(){

		let count = $('#custom-field-section .removediv').length;
		if(count != 1)
		{
			$(this).closest(".removediv").remove();
		}
		else
		{
			$(this).parent().closest(".removediv").remove();
		}


		$('#custom-field-section .removediv').each(function( index ) {

			$(this).find('.control-label').text('<?= __('admin.runner') ?>'+' '+(index+1));

		});

	});

	$("body").delegate(".bt_custom_field", "keypress", function(e){
	    var regex = new RegExp("^[a-zA-Z0-9 ]+$");
	    var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
	    if (regex.test(str)) {
	        return true;
	    }
	    e.preventDefault();
	    return false;


	});
	
</script>