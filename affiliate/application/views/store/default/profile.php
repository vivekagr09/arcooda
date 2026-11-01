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
				<form action="<?php echo base_url('store/profile') ?>" class="form-horizontal" method="post" id="profile-frm" enctype="multipart/form-data">
					<h2><?= __('store.profile') ?></h2>
					<div class="form-group">
						<?php 
						$avatar = ($client['avatar'] != '') ? base_url('assets/images/users/'.$client['avatar']) : base_url('assets/store/default/img/blog1.png') ; 
						?>
						<img id="blah" src="<?= $avatar ?>" class="img-profile-main" alt="<?= __('store.profile') ?>">
						<div class="fileUpload btn text-dark text-center w-100">
							<span><i class="far fa-image mr-2"></i><?= __('store.choose_file') ?></span>
							<input id="uploadBtn" name="avatar" class="upload" type="file" style="display:none;">
						</div>
					</div>
					<div class="form-checkout-wrapper">
						<div class="checkout-form">
							<div class="form-row">
								<div class="form-group">
									<label><?= __('store.first_name') ?>*</label>
									<input placeholder="<?= __('store.enter_your_first_name') ?>" name="firstname" value="<?php echo $userDetails['firstname']; ?>" class="form-control" type="text" required>
								</div>

								<div class="form-group">
									<label><?= __('store.last_name') ?>*</label>
									<input placeholder="<?= __('store.enter_your_last_name') ?>" name="lastname" class="form-control" value="<?php echo $userDetails['lastname']; ?>" type="text" required>
								</div>
							</div>

							<div class="form-row">

								<div class="form-group">
									<label><?= __('store.your_email') ?>*</label>
									<input placeholder="<?= __('store.enter_your_email_address') ?>" name="email" id="email" class="form-control" value="<?php echo $userDetails['email']; ?>" type="email" required>
								</div>

								<div class="form-group">
									<label><?= __('store.phone_number') ?>*</label>
									<link rel="stylesheet" href="<?= base_url('assets/plugins/tel/css/intlTelInput.css?v='.av()) ?>">
									<script src="<?= base_url('assets/plugins/tel/js/intlTelInput.js') ?>"></script>
			        				<input type="hidden" name='PhoneNumberInput' id="phonenumber-input" value="" class="form-control" placeholder="<?= __('store.phone_number') ?>"  >

									<input onkeypress="return isNumberKey(event);" id="PhoneNumber" class="form-control" type="text" name="PhoneNumber" value="<?php echo $userDetails['phone']; ?>">
									<script type="text/javascript">
										var tel_input = intlTelInput(document.querySelector("#PhoneNumber"), {
											utilsScript: "<?= base_url('/assets/plugins/tel/js/utils.js?1562189064761') ?>",
											separateDialCode:true,
											geoIpLookup: function(success, failure) {
											$.get("https://ipinfo.io", function() {}, "jsonp").always(function(resp) {
												var countryCode = (resp && resp.country) ? resp.country : "";
												success(countryCode);
												setTimeout(function(){ 
													;
												}, 100);
											});
											},
										});

										$( document ).ready(function() {
											console.log('<?= $userDetails['phone']; ?>');
											tel_input.setNumber('<?= $userDetails['phone']; ?>');
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
								</div>
							</div>

							<div class="form-row">
								<div class="form-group">
									<label><?= __('store.country') ?></label>
									<select name="ucountry" class="custom-select form-control countries" id="ucountry" >
										<option value="" selected="selected" ><?= __('store.select_country') ?></option>
										<?php foreach($country as $countries): ?>
										<option <?php if(!empty($userDetails['ucountry']) && $userDetails['ucountry'] == $countries->id) { ?> selected <?php }?> value="<?php echo $countries->id; ?>"><?php echo $countries->name; ?></option>
										<?php endforeach; ?> 
									</select>
								</div>
								<div class="form-group">
									<label class="control-label"><?= __('store.state') ?></label>
									<select class="custom-select form-control" name="state"></select>
								</div>
							</div>
							

							<div class="form-row">

								<div class="form-group">
								    <label><?= __('store.city') ?>*</label>
								    <input class="form-control" placeholder="<?= __('store.enter_your_city') ?>" name="ucity" id="ucity" value="<?php echo $userDetails['ucity'];?>" type="text" required>
								</div>

								<div class="form-group">
									<label><?= __('store.pincode') ?>*</label>
									<input class="form-control" placeholder="<?= __('store.enter_your_pincode') ?>" name="uzip" id="uzip" value="<?php echo $userDetails['uzip'];?>" type="text" required>
								</div>

							</div>
							<div class="form-row">
								<div class="form-group">
									<label class="control-label"><?= __('store.full_address') ?></label>
									<textarea class="form-control" name="twaddress" required><?= isset($userDetails) ? $userDetails['twaddress'] : '' ?></textarea>
									<?php if($errors && isset($errors['twaddress'])) { ?>
									<div class="text-danger"><?php echo $errors['twaddress'] ?></div>
									<?php } ?>
								</div>
							</div>


							<h2 class="mt-3 mt-md-5"><?= __('store.change_password') ?></h2>

							<div class="form-row">
								<div class="form-group">
									<label><?= __('store.enter_new_password') ?></label>
									<input class="form-control" name="new_password" value="" type="password">
								</div>
								<div class="form-group">
									<label><?= __('store.confirm_password') ?></label>
									<input class="form-control" name="c_password" value="" type="password">
								</div>
							</div>

							<button id="update-profile" type="submit" class="btn btn-save-profile"><?= __('store.update_profile') ?></button>
						</div>
					</div>
				</form>
			</div>
	</div>	   
</section>
							

<script type="text/javascript">
	function readURL(input) {
		if (input.files && input.files[0]) {
			var reader = new FileReader();
			
			reader.onload = function(e) {
				jQuery('#blah').attr('src', e.target.result);
			}
			
			reader.readAsDataURL(input.files[0]);
		}
	}

	$(document).on('click', '.fileUpload span', function(){
		$('#uploadBtn').trigger('click');
	});

	document.getElementById("uploadBtn").onchange = function () {
		readURL(this);
	};
 
	var selected_state = '<?= isset($userDetails) ? $userDetails['state'] : '' ?>';
	$(document).delegate('[name="ucountry"]',"change",function(){
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

	$('#update-profile').on('click', function(){
		$("#profile-frm").submit();
	});
 

	$("#profile-frm").submit(function(){
		var errorMap = ['<?= __('store.invalid_number') ?>','<?= __('store.invalid_country_code') ?>','<?= __('store.too_short') ?>','<?= __('store.too_long') ?>','<?= __('store.invalid_number') ?>'];
		is_valid = false;
		var errorInnerHTML = '';
		if ($("#PhoneNumber").val().trim()) {
			if (tel_input.isValidNumber()) {
				is_valid = true;
				$("#phonenumber-input").val("+"+tel_input.getSelectedCountryData().dialCode +' '+ $("#PhoneNumber").val().trim());
			} else {
				var errorCode = tel_input.getValidationError();
				errorInnerHTML = errorMap[errorCode];
			}
		} else {
			errorInnerHTML = '<?= __('store.mobile_number_is_required') ?>';
		}
		$("#PhoneNumber").parents(".form-group").removeClass("has-error");
		$("#profile-frm .text-danger").remove();

		if(!is_valid){
			$("#PhoneNumber").parents(".form-group").addClass("has-error");
			$(".iti").after("<span class='text-danger'>"+ errorInnerHTML +"</span>");
			return false;
		}
	});

	$('[name="ucountry"]').trigger("change");
</script>
