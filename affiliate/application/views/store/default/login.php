<div class="container py-5">    
	<div class="row">
		<div class="col-md-5">
	
			<form id="login-form">
			    <div class="card shadow-sm border-top-card">
			        <div class="card-body p-md-5">
			            <h5 class="sub-title display-5 mb-5"><?= __('store.login_with_existing_account') ?></h5>
		            	<div class="form-group">
        					<label class="control-label"><?= __('store.username') ?></label>
        					<input class="form-control" name="username" type="text">
        				</div>
        				<div class="form-group">
        					<label class="control-label"><?= __('store.password') ?></label>
        					<input class="form-control" name="password" type="password">
        				</div>
        
        				<script type="text/javascript">
        					var grecaptcha = undefined;
        				</script>
        				<?php 
        					$db =& get_instance(); 
        				    $googlerecaptcha =$db->Product_model->getSettings('googlerecaptcha');
        				?>
        
        				<?php if (isset($googlerecaptcha['client_login']) && $googlerecaptcha['client_login']) { ?>
        					<div class="captch">
        						<script src='https://www.google.com/recaptcha/api.js'></script>
        						<div class="g-recaptcha" data-sitekey="<?= $googlerecaptcha['sitekey'] ?>"></div>
        						<input type="hidden" name="captch_response" id="captch_response"> 
        					</div>
        				<?php } ?>
        				<div class="forgot mb-3 text-right">
        					<a data-toggle="modal" href='#forgot-password-model' class="text-muted"><?= __('store.forget_password') ?>?</a>
        				</div>
        				<div class="form-group">
        					<button class="btn btn-primary btn-submit w-100"><?= __('store.login') ?></button>
        				</div>
			        </div>
			    </div>
			</form>
		</div>
		<div class="col-md-1 pt-md-5 text-center">
		    <h5 class="mt-5 pt-md-5 mb-5 mb-md-0"><?= __('store.or') ?></h5>
		</div>
		<div class="col-md-5">
			
			<form id="register-form">
			    <div class="card shadow-sm border-top-card">
			        <div class="card-body p-md-5">
			            <h5 class="sub-title display-5 mb-5"><?= __('store.create_a_new_account') ?></h5>
		            	<div class="form-group">
        					<label class="control-label"><?= __('store.first_name') ?></label>
        					<input class="form-control" name="f_name" type="text">
        				</div>
        				<div class="form-group">
        					<label class="control-label"><?= __('store.last_name') ?></label>
        					<input class="form-control" name="l_name" type="text">
        				</div>
        				<div class="form-group">
        					<label class="control-label"><?= __('store.username') ?></label>
        					<input class="form-control" name="username" type="text">
        				</div>
        				<link rel="stylesheet" href="<?= base_url('assets/plugins/tel/css/intlTelInput.css') ?>?v=<?= av() ?>">
        				<script src="<?= base_url('assets/plugins/tel/js/intlTelInput.js') ?>"></script>
        				<input type="hidden" name='PhoneNumberInput' id="phonenumber-input" value="" class="form-control" placeholder="<?= __('store.phone_number') ?>"  >
        				<div class="form-group">
        					<label for=""><?= __('store.phone_number') ?></label>
        					<div>
        						<input onkeypress="return isNumberKey(event);" id="phone" type="text" name="phone" value="">
        					</div>
        				</div>
        				<script type="text/javascript">
        					var tel_input = intlTelInput(document.querySelector("#phone"), {
        					  initialCountry: "auto",
        					  utilsScript: "<?= base_url('/assets/plugins/tel/js/utils.js?1562189064761') ?>",
        					  separateDialCode:true,
        					  geoIpLookup: function(success, failure) {
        					    $.get("https://ipinfo.io", function() {}, "jsonp").always(function(resp) {
        					      var countryCode = (resp && resp.country) ? resp.country : "";
        					      success(countryCode);
        					    });
        					  },
        					});

        					function isNumberKey(evt)
							{
							  var charCode = (evt.which) ? evt.which : event.keyCode;
							    if (charCode != 46 && charCode != 45 && charCode > 31
							    && (charCode < 48 || charCode > 57))
							     return false;

							  return true;
							}
        				</script>
        				<div class="form-group">
        					<label class="control-label"><?= __('store.email') ?></label>
        					<input class="form-control" name="email" type="email">
        				</div>
        				<div class="form-group">
        					<label class="control-label"><?= __('store.password') ?></label>
        					<input class="form-control" name="password" type="password">
        				</div>
        				<div class="form-group">
        					<label class="control-label"><?= __('store.confirm_password') ?></label>
        					<input class="form-control" name="c_password" type="password">
        				</div>
        
        				<?php 
        					$db =& get_instance(); 
        				    $googlerecaptcha =$db->Product_model->getSettings('googlerecaptcha');
        				?>
        
        				<?php if (isset($googlerecaptcha['client_register']) && $googlerecaptcha['client_register']) { ?>
        					<script type="text/javascript">
        						var grecaptcha_register = 1;
        					</script>
        					<div class="captch">
        						<script src='https://www.google.com/recaptcha/api.js'></script>
        						<div class="g-recaptcha" data-sitekey="<?= $googlerecaptcha['sitekey'] ?>"></div>
        						<input type="hidden" name="captch_response" id="captch_response_register"> 
        					</div>
        				<?php } ?>
        
        				<div class="form-group mt-4">
        					<button class="btn btn-primary btn-submit w-100"><?= __('store.register') ?></button>
        				</div>
			        </div>
			    </div>
			
			</form>
		</div>
	</div>
</div>
<div class="modal fade" id="forgot-password-model">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content rounded-0 border-0 shadow">
			<form action="store/forgot" method="post" id="forgot-password">
				<div class="modal-header border-0 pb-0">
					<button type="button" class="close" data-bs-dismiss="modal" aria-hidden="true">&times;</button>
				</div>
				<div class="modal-body pb-5 px-5">
				    <h4 class="modal-title display-5 mb-4"><?= __('store.forgot_password_?') ?></h4>
					<div class="form-group">
						<input type="text" name="forgot_email" class="form-control" placeholder="<?= __('store.email_address') ?>" />
						<span class="text-danger"></span>
					</div>
					<div class="text-right">
					    <button type="submit" class="btn btn-primary btn-submit"><?= __('store.submit') ?></button>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
<script type="text/javascript">
	$("#login-form").on('submit',function(){
		$this = $(this);

		var check_captch = true;
      	if (grecaptcha === undefined) {
          	check_captch = false;
      	}

      	$("#captch_response").val('')

      	if(check_captch){
          captch_response = grecaptcha.getResponse();
          $("#captch_response").val(captch_response)
      	}

		$.ajax({
			url:'<?= $base_url ?>ajax_login',
			type:'POST',
			dataType:'json',
			data:$this.serialize(),
			beforeSend:function(){$this.find(".btn-submit").btn("loading");},
			complete:function(){$this.find(".btn-submit").btn("reset");},
			success:function(result){
				$this.find(".has-error").removeClass("has-error");
				$this.find("span.text-danger").remove();
				
				if(result['success']){
					location = '<?= $redirect_url ?>';
				}

				if(result['errors']){
				
				    $.each(result['errors'], function(i,j) {
				    	if(i == 'captch_response' && grecaptcha){ grecaptcha.reset(0); }
				        $ele = $this.find('[name="'+ i +'"]');
				        if($ele){
				            $ele.parents(".form-group").addClass("has-error");
				            $ele.after("<span class='text-danger'>"+ j +"</span>");
				        }
				    })
				}
			},
		})
		return false;
	});

	$("#register-form").on('submit',function(){
		$this = $(this);

		var errorMap = ['<?= __('store.invalid_number') ?>','<?= __('store.invalid_country_code') ?>','<?= __('store.too_short') ?>','<?= __('store.too_long') ?>','<?= __('store.invalid_number') ?>'];
		is_valid = false;
		var errorInnerHTML = '';
		if ($("#phone").val().trim()) {
			if (tel_input.isValidNumber()) {
				is_valid = true;
				tel_input.setNumber($("#phone").val().trim());
				$("#phonenumber-input").val("+"+tel_input.getSelectedCountryData().dialCode +' '+ $("#phone").val().trim());
			} else {
				var errorCode = tel_input.getValidationError();
				errorInnerHTML = errorMap[errorCode];
			}
		} else {
			errorInnerHTML = '<?= __('store.mobile_number_is_required') ?>';
		}
		$("#phone").parents(".form-group").removeClass("has-error");
		$("#register-form .text-danger").remove();

		if(!is_valid){
			$("#phone").parents(".form-group").addClass("has-error");
			$("#phone").parents(".form-group").find('> div').after("<span class='text-danger'>"+ errorInnerHTML +"</span>");
			return false;
		}


		var captchIndex = $('[name="captch_response"]').length == 2 ? 1 : 0;

		var check_captch = true;
      	if (typeof grecaptcha_register === 'undefined') {
          	check_captch = false;
      	}

      	$("#captch_response_register").val('')
      	if(check_captch){
          captch_response = grecaptcha.getResponse(captchIndex);
          $("#captch_response_register").val(captch_response)
      	}


		$.ajax({
			url:'<?= $base_url ?>ajax_register',
			type:'POST',
			dataType:'json',
			data:$this.serialize(),
			beforeSend:function(){$this.find(".btn-submit").btn("loading");},
			complete:function(){$this.find(".btn-submit").btn("reset");},
			success:function(result){
				$this.find(".has-error").removeClass("has-error");
				$this.find("span.text-danger").remove();
				if(result['success']){
					location = '<?= $redirect_url ?>';
				}
				
				if(result['errors']){
				    $.each(result['errors'], function(i,j){
				    	if(i == 'captch_response' && grecaptcha){ grecaptcha.reset(captchIndex); }
				        $ele = $this.find('[name="'+ i +'"]');
				        if($ele){
				            $ele.parents(".form-group").addClass("has-error");
				            $ele.after("<span class='text-danger'>"+ j +"</span>");
				        }
				    })
				}
			},
		})

		return false;
	});

	$("#forgot-password").on('submit',function(){
		$this = $(this);
		$.ajax({
			url:'<?= $base_url ?>forgot',
			type:'POST',
			dataType:'json',
			data:$this.serialize(),
			beforeSend:function(){$this.find(".btn-submit").btn("loading");},
			complete:function(){$this.find(".btn-submit").btn("reset");},
			success:function(json){
				$this.find("span.text-danger").text('');
				if(json.success){
					$('#forgot-password-model').modal('hide');
					alert(json.success);
				}
				if(json.error){
				    $this.find("span.text-danger").text(json.error);
				}
			},
		})
		return false;
	});
</script>