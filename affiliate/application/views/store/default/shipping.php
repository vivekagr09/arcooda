<!--sub_menu-->
<div>
    <nav class="navbar navbar-expand-lg navbar-light bg-light mt-1 mr-1">
		  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#profilesubnav" aria-controls="profilesubnav" aria-expanded="false" aria-label="Toggle navigation">
		    <span class="navbar-toggler-icon"></span>
		  </button>
            <div class="collapse navbar-collapse" id="profilesubnav">
                <ul class="nav navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= $base_url ?>profile"><?= __('store.profile') ?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= $base_url ?>order"><?= __('store.orders') ?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= $base_url ?>shipping"><?= __('store.shipping') ?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= $base_url ?>wishlist"><?= __('store.wishlist') ?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= $base_url ?>logout"><?= __('store.logout') ?></a>
                    </li>
                </ul>
            </div>
    </nav>
</div>
<!--sub_menu-->

<section class="profile-page">
	<div class="container main-container">
			<div class="">
				<form action="<?php echo base_url('store/shipping') ?>" class="form-horizontal" method="post" id="profile-frm" enctype="multipart/form-data">
					<h2><?= __('store.shipping_details') ?></h2>
					<div class="form-checkout-wrapper">
						<div class="checkout-form">

							<div class="form-row">
								<div class="form-group">
									<label class="control-label"><?= __('store.country') ?></label>
									<?php $selected =  isset($shipping) ? $shipping['country_id'] : '' ?>
									<select class="custom-select form-control" name="country">
										<?php foreach ($country as $key => $value) { ?>
											<option <?= $selected == $value->id ? 'selected' : '' ?> value="<?= $value->id ?>"><?= $value->name ?></option>
										<?php } ?>
									</select>
								</div>
								<div class="form-group">
									<label class="control-label"><?= __('store.state') ?></label>
									<select class="custom-select form-control" name="state"></select>
								</div>
								<div class="form-group">
									<label class="control-label"><?= __('store.city') ?></label>
									<input class="form-control" name="city" type="text" value="<?= isset($shipping) ? $shipping['city'] : '' ?>">
									<?php if($errors && isset($errors['city'])) { ?>
									<div class="text-danger"><?php echo $errors['city'] ?></div>
									<?php } ?>
								</div>
							</div>

							<div class="form-row">
								<div class="form-group">
									<label class="control-label"><?= __('store.postal_code') ?></label>
									<input class="form-control" name="zip_code" type="text" value="<?= isset($shipping) ? $shipping['zip_code'] : '' ?>">
									<?php if($errors && isset($errors['zip_code'])) { ?>
									<div class="text-danger"><?php echo $errors['zip_code'] ?></div>
									<?php } ?>
								</div>
								<div class="form-group">
									<label class="control-label"><?= __('store.phone_number') ?></label>
									<link rel="stylesheet" href="<?= base_url('assets/plugins/tel/css/intlTelInput.css') ?>?v=<?= av() ?>">
									<script src="<?= base_url('assets/plugins/tel/js/intlTelInput.js') ?>"></script>
									<input type="hidden" id="phonenumber-input" name='PhoneNumberInput' value="" class="form-control" placeholder="<?= __('store.phone_number') ?>" />

									<div>
										<input onkeypress="return isNumberKey(event);" id="phone" class="form-control" type="text" name="phone" value="<?= isset($shipping) ? $shipping['phone'] : '' ?>">
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
									</script>
									<?php if($errors && isset($errors['phone'])) { ?>
									<div class="text-danger"><?php echo $errors['phone'] ?></div>
									<?php } ?>
								</div>
							</div>

							<div class="form-row">
								<div class="form-group">
									<label class="control-label"><?= __('store.full_address') ?></label>
									<textarea class="form-control" name="address"><?= isset($shipping) ? $shipping['address'] : '' ?></textarea>
									<?php if($errors && isset($errors['address'])) { ?>
									<div class="text-danger"><?php echo $errors['address'] ?></div>
									<?php } ?>
								</div>
							</div>
							<button class="btn btn-save-profile" id="update-profile" type="submit"><?= __('client.update_shipping') ?></button>
						</div>
					</div>
				</form>
			</div>
	</div>	   
</section>

<script type="text/javascript">
	$('.form-horizontal').submit(function() {
		var errorMap = ['<?= __('store.invalid_number') ?>','<?= __('store.invalid_country_code') ?>','<?= __('store.too_short') ?>','<?= __('store.too_long') ?>','<?= __('store.invalid_number') ?>'];


		var is_valid = false;

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

		$(".checkout-form .text-danger").remove();

		if(!is_valid){
			$("#phone").parents(".form-group").addClass("has-error");
			$("#phone").parents(".form-group").find('> div').after("<span class='text-danger'>"+ errorInnerHTML +"</span>");
			return false;
		}
	});


	var selected_state = '<?= isset($shipping) ? $shipping['state_id'] : '' ?>';
	$(document).delegate('[name="country"]',"change",function(){
		$this = $(this);
		$.ajax({
			url:'<?= base_url('store/getState') ?>',
			type:'POST',
			dataType:'json',
			data:{id:$this.val()},
			beforeSend:function(){$this.prop("disabled",true);},
			complete:function(){$this.prop("disabled",false);},
			success:function(json){
				var html = '';
				$.each(json['states'], function(i,j){
					var s = '';
					if(selected_state && selected_state == j['id']){
						s = 'selected';selected_state = 0;
					}
					html += "<option "+ s +" value='"+ j['id'] +"'>"+ j['name'] +"</option>";
				})
				$('[name="state"]').html(html);
			},
		})
	})

	$('[name="country"]').trigger("change");

	function isNumberKey(evt)
	{
	  var charCode = (evt.which) ? evt.which : event.keyCode;
	    if (charCode != 46 && charCode != 45 && charCode > 31
	    && (charCode < 48 || charCode > 57))
	     return false;

	  return true;
	}
</script>
