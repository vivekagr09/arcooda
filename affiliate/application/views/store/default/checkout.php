<link rel="stylesheet" href="<?= base_url('assets/plugins/tel/css/intlTelInput.css') ?>?v=<?= av() ?>">
<script src="<?= base_url('assets/plugins/tel/js/intlTelInput.js') ?>"></script>

<section class="cart-page checkout-page1">
	<div class="container my-5">
	    <h2 class="mb-4"><?= __('store.checkout') ?></h2>
	    <?php if (!$is_logged) { ?>
	        <div class="checkout-step auth-step bg-light border rounded p-3 mb-5" style="<?= isset($_SESSION['guestFlow']) ? 'display:none' : '' ?>">
	            <div class="step-head bg-light py-3 px-4 border-start border-3 border-primary">
	                <h5 class="mb-0"><?= __('store.personal_details') ?></h5>
	            </div>
	            <?php
	                $db =& get_instance();
	                $products = $db->Product_model;
	                $googlerecaptcha = $db->Product_model->getSettings('googlerecaptcha');
	            ?>
<div class="step-body">
    <div class="row">
        <div class="col-md-5">
            <div class="card border rounded">
                <div class="card-body">
                    <h5 class="h5 mb-5"><?= __('store.login_with_existing_account') ?></h5>
                    <form id="login-form">
                        <input type="hidden" name="store_checkout" class="form-control" value="1">
                        <div class="mb-3">
                            <label class="form-label"><?= __('store.username') ?></label>
                            <input type="text" name="username" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><?= __('store.password') ?></label>
                            <input type="password" name="password" class="form-control">
                        </div>
                        <?php if (isset($googlerecaptcha['client_login']) && $googlerecaptcha['client_login']) { ?>
                        <div class="mb-3">
                            <div class="captch mb-3">
                                <div class="g-recaptcha" id='client_login'></div>
                            </div>
                            <input type="hidden" name="captch_response">
                        </div>
                        <?php } ?>
                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary w-100"><?= __('store.login') ?></button>
                        </div>
                    </form>
                    <button id="btnGuestcontinues" class="btn btn-primary w-100"><?= __('store.guest_checkout') ?></button>
                </div>
            </div>
        </div>
        <div class="col-md-2 d-flex justify-content-center align-items-center">
            <h5 class="mb-5"><?= __('store.or') ?></h5>
        </div>
        <div class="col-md-5">
            <div class="card border rounded">
                <div class="card-body">
                    <h5 class="h5 mb-5"><?= __('store.create_a_new_account') ?></h5>
                    <form id="register-form">
                        <input type="hidden" name="store_checkout" class="form-control" value="1">
                        <div class="mb-3">
                            <label class="form-label"><?= __('store.first_name') ?></label>
                            <input type="text" name="f_name" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><?= __('store.last_name') ?></label>
                            <input type="text" name="l_name" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><?= __('store.username') ?></label>
                            <input type="text" name="username" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><?= __('store.email') ?></label>
                            <input type="email" name="email" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><?= __('store.phone_number') ?></label>
                            <input type="text" name="phone" id="phoneergister" onkeypress="return isNumberKey(event);" class="form-control">
                        </div>
                        <script type="text/javascript">
                            var tel_inputre = intlTelInput(document.querySelector("#phoneergister"), {
                                initialCountry: "auto",
                                utilsScript: "<?= base_url('/assets/plugins/tel/js/utils.js?1562189064761') ?>",
                                separateDialCode: true,
                                geoIpLookup: function(success, failure) {
                                    $.get("https://ipinfo.io", function() {}, "jsonp").always(function(resp) {
                                        var countryCode = (resp && resp.country) ? resp.country : "";
                                        success(countryCode);
                                    });
                                },
                            });
                        </script>
                        <div class="mb-3">
                            <label class="form-label"><?= __('store.password') ?></label>
                            <input type="password" name="password" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><?= __('store.confirm_password') ?></label>
                            <input type="password" name="c_password" class="form-control">
                        </div>
                        <?php if (isset($googlerecaptcha['client_register']) && $googlerecaptcha['client_register']) { ?>
                        <div class="mb-3">
                            <div class="captch mb-3">
                                <div class="g-recaptcha" id='client_register'></div>
                            </div>
                            <input type="hidden" name="captch_response">
                        </div>
                        <?php } ?>
                        <?php if (
                            (isset($googlerecaptcha['client_register']) && $googlerecaptcha['client_register']) ||
                            (isset($googlerecaptcha['client_login']) && $googlerecaptcha['client_login']) 
                        ) { ?>
                        <script async defer src='https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit'></script>
                        <script type="text/javascript">
                            var gcaptch = {};
                            var onloadCallback = function() {
                                var recaptchas = document.querySelectorAll('div[class=g-recaptcha]');
                                for (var i = 0; i < recaptchas.length; i++) {
                                    gcaptch[recaptchas[i].id] =  grecaptcha.render(recaptchas[i].id, {
                                        'sitekey' : '<?= $googlerecaptcha['sitekey'] ?>',
                                    });
                                }
                            }
                        </script>
                        <?php } ?>
                        <div class="mb-3 mt-4">
                            <button type="submit" class="btn btn-primary w-100"><?= __('store.register') ?></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

				<div class="step-footer"></div>
			</div>
		<?php } ?>

		<div class="cart-wrapper">
			<div class="checkout-setp cart-step">
				<div class="step-head">
					<h2> <?= __('store.purchase_of_details') ?></h2>
				</div>
				<div class="step-body">
					<div class="cart-loader"></div>
					<div class="cart-body"></div>
				</div>
				<div class="step-footer"></div>
			</div>
		</div>

		<div class="non-confirm mt-4">
			<?php if(isset($_SESSION['guestFlow']) || $allow_shipping){ ?> 
				<div class="checkout-form" <?= (!$is_logged) ? (!isset($_SESSION['guestFlow']) ? 'style="display:none;"' : "") : ""; ?> >	
					<h2><?php echo $allow_shipping == 1 ? __('store.billing_shipping_address') : __('store.billing_address');?></h2>
					<div class="form-checkout-wrapper">
						<div class="checkout-setp shipping-step">
							<div class="step-head">
								<h2></h2>
							</div>
							<div class="step-body">
								<?php if(isset($shipping_not_allow_error_message)){ ?>
									<div class="alert alert-danger">
										<?= $shipping_not_allow_error_message ?>
									</div>
								<?php } ?>
								<div class="cart-loader"></div>
								<div class="cart-body">
									<?php if (!$allow_shipping && !$is_logged): ?>
										<div class="form-row">
											<div class="form-group">
												<label><?php echo __('store.enter_your_first_name')?></label>
												<input type="text" placeholder="<?php echo __('store.enter_your_first_name')?>" name="firstname" class="form-control" type="text" value="" required="">
											</div>
											<div class="form-group">
												<label><?php echo __('store.enter_your_last_name')?></label>
												<input type="text" placeholder="<?php echo __('store.enter_your_last_name')?>" name="lastname" class="form-control" type="text" value="" required="">
											</div>
											<div class="form-group">
												<label><?php echo __('store.enter_your_email_address')?></label>
												<input type="text" placeholder="<?php echo __('store.enter_your_email_address')?>" name="email" class="form-control" type="text" value="" required="">	
												<input type="hidden" name="classified_checkout" value="1">	
											</div>

											<div class="form-group">
												<label for=""><?php echo __('store.phone')?></label>
												<input type="hidden" id="phonenumber-input" name='PhoneNumberInput' value="" class="form-control" placeholder="<?= __('store.phone_number') ?>" />
												<div>
													<input onkeypress="return isNumberKey(event);" id="phoneguest" placeholder="<?php echo __('store.phone')?>" class="form-control" type="text" name="phone" value="">
												</div>
											</div>
											<script type="text/javascript">
												var tel_input = intlTelInput(document.querySelector("#phoneguest"), {
													initialCountry: "auto",
													utilsScript: "<?=base_url()?>assets/plugins/tel/js/utils.js?1562189064761",
													separateDialCode:true,
													geoIpLookup: function(success, failure) {
														$.get("https://ipinfo.io", function() {}, "jsonp").always(function(resp) {
															var countryCode = (resp && resp.country) ? resp.country : "";
															success(countryCode);
														});
													},
												});
											</script>
										</div>
									<?php endif ?>

								</div>
							</div>
							<div class="step-footer"></div>
						</div>

					</div>
				</div>
			<?php } ?>
		</div>				

		<?php 
		 
		$check_total_for_skip_payment=1;
		foreach ($totals as $key => $value) 
		{  
		 	$check_total_for_skip_payment=$value['amount']; 
		}

		if($check_total_for_skip_payment>0)
		{ 
		?>
			<div class="non-confirm">
				<div class="checkout-payments" <?= (!$is_logged) ? (!isset($_SESSION['guestFlow']) ? 'style="display:none;"' : "") : ""; ?>>
					<div class="checkout-setp">
						<div class="step-head">
							<h2><?= __('store.payment_methods') ?></h2>
						</div>
						<div class="step-body">
							<div class="dynamic-payment"></div>
							<br>

							<?php if($allow_upload_file){ ?>
								<span class="order-file-info">
									<?= __('store.add_files_to_your_order') ?>
									<p>(gif,jpeg,jpg,png,pdf,doc,docx,zip,tar)</p>
								</span>
								<link rel="stylesheet" type="text/css" href="<?= base_url('assets/css/jquery.uploadPreviewer.css') ?>?v=<?= av() ?>">
								<div class="form-group downloadable_file_div well" style="white-space: inherit;">
									<div class="file-preview-button btn btn-primary">
										<?= __('store.order_upload_file') ?>
										<input type="file" class="downloadable_file_input" multiple="">
									</div>

									<div id="priview-table" class="table-responsive" style="display: none;">
										<table class="table table-hover">
											<tbody></tbody>
										</table>
									</div>
								</div>
							<?php } ?>
							<div class="custom-control custom-checkbox">
								<input type="checkbox" class="custom-control-input" id="customCheck" name="agree" value="1">
								<label class="custom-control-label" for="customCheck"><?= __('store.agree_text') ?></label>
							</div>
							<br>
							<div class="warning-div"></div>
						</div>
						<div class="step-footer cart-buttons-row">
							<a href="javascript:void(0);" class="btn btn-checoutcart bg-main2 confirm-order"><?= __('store.confirm_and_pay') ?></a>
						</div>
					</div>
				</div>
			</div>
		<?php
		}
		else
		{ ?>

			<div class="non-confirm">
				<div class="checkout-payments" <?= (!$is_logged) ? (!isset($_SESSION['guestFlow']) ? 'style="display:none;"' : "") : ""; ?>>

					<div class="checkout-setp">
						<div class="step-body">
							<div class="custom-control custom-checkbox">
								<input type="checkbox" class="custom-control-input" id="customCheck" name="agree" value="1">
								<label class="custom-control-label" for="customCheck"><?= __('store.agree_text') ?></label>
							</div>
							<br>
							<div class="warning-div"></div>
						</div>	
					</div>	
					<div class="step-footer cart-buttons-row">
							<a href="javascript:void(0);" class="btn btn-checoutcart bg-main2 confirm-order"><?= __('store.confirm_order') ?></a>
					</div>
				</div>
			</div>		

		<?php	
		}
		?>

			<div class="confirm-checkout" style="display:none;">
				<div class="checkout-setp confirm-step">
					<div class="step-head">
						<h2><?= __('store.confirm_order') ?></h2>
					</div>
					<div class="step-body">
						<div class="">
							<div id="checkout-confirm"></div>
						</div>
					</div>
					<div class="step-footer"></div>
				</div>
			</div>

	</div>
</section>


<script type="text/javascript">
	var isGuest = '<?=isset($_SESSION["guestFlow"])?>';
	var allow_shipping = '<?=$allow_shipping?>';

	$('[name="payment_gateway"]').on('change',function(){
		alert('as');
		if($(this).val() == 'bank_transfer'){
			$('.bank-transfer-instruction').slideDown();
		}else{
			$('.bank-transfer-instruction').slideUp();
		}
	});
	$(".cart-step").delegate(".btn-remove-cart","click",function(){
		$this = $(this);
		$.ajax({
			url:$this.attr("data-href"),
			type:'POST',
			dataType:'json',
			beforeSend:function(){},
			complete:function(){},
			success:function(json){
				getCart();			

			},
		})
		return false;
	});


	var xhr;
	$(".cart-step").delegate(".qty-input","change",function(){
		if(xhr && xhr.readyState != 4) xhr.abort();

		$this = $(this);
		xhr = $.ajax({
			url:'<?= $cart_update_url ?>',
			type:'POST',
			dataType:'json',
			data:$("#checkout-cart-form").serialize(),
			beforeSend:function(){},
			complete:function(){},
			success:function(json){
				getCart();			
				updateCart();
			},
		})
		return false;
	})

	var cart_xhr;
	function getCart() {
		if(cart_xhr && cart_xhr.readyState !=4) cart_xhr.abort();
		cart_xhr = $.ajax({
			url:'<?= base_url('store/checkout-cart') ?>',
			type:'POST',
			dataType:'html',
			beforeSend:function(){},
			complete:function(){},
			success:function(html){
				$(".cart-step .cart-body").html(html);
			},
		})
	}

	function getShipping(countryCode = null) {
		if(countryCode != null) {
			$(".shipping-step .cart-body").load('<?= base_url('store/checkout_shipping') ?>/'+countryCode);
		} else {
			$(".shipping-step .cart-body").load('<?= base_url('store/checkout_shipping') ?>');
		}
	}


	function getPaymentMethods(){
		$.ajax({
			url:'<?= base_url('store/get_payment_mothods') ?>',
			type:'POST',
			dataType:'json',
			data:{
				data:$("#checkout-cart-form").serialize(),
			},
			beforeSend:function(){},
			complete:function(){},
			success:function(json){

				$(".dynamic-payment").html(json['html']);
			},
		})
	}
	<?php if(!$allow_shipping){ ?>
		getCart();
	<?php } ?>
	if(allow_shipping)
		getShipping();

	getPaymentMethods();
	
	$('.shipping-step').delegate('[name="country"]',"change",function(){
		$this = $(this);
		let countryCode = $(this).val();
		if(isGuest)
			renderStateAndCart(countryCode);
		else	
			getShipping(countryCode);
	});

	function renderStateAndCart(countryCode) {
		$.ajax({
			url:'<?= base_url('store/getState') ?>',
			type:'POST',
			dataType:'json',
			data:{id:countryCode,checkShipping:true},
			beforeSend:function(){$('[name="state"]').prop("disabled",true);},
			complete:function(){$('[name="state"]').prop("disabled",false);},
			success:function(json){
				$(".shipping-warning").html('');

				var html = '<option value="">'+'<?= __('store.select_state') ?>'+'</option>';
				$.each(json['states'], function(i,j){
					var s = '';
					if(selected_state && selected_state == j['id']){
						s = 'selected';selected_state = 0;
					}
					html += "<option "+ s +" value='"+ j['id'] +"'>"+ j['name'] +"</option>";
				})
				$('[name="state"]').html(html);

				getCart();
			},
		});
	}


	$(".confirm-order").on('click',function(){


		let phoneNumberInp = null;

		if($("#phone").length) {
			phoneNumberInp = $("#phone");
		} else if($("#phoneguest").length) {
			phoneNumberInp = $("#phoneguest");
		}

		if(phoneNumberInp !== null) {
			var errorMap = ['<?= __('store.invalid_number') ?>','<?= __('store.invalid_country_code') ?>','<?= __('store.too_short') ?>','<?= __('store.too_long') ?>','<?= __('store.invalid_number') ?>'];

			var is_valid = false;
			
			var errorInnerHTML = '';

			if (phoneNumberInp.val().trim()) {
				if (tel_input.isValidNumber()) {
					is_valid = true;
					tel_input.setNumber(phoneNumberInp.val().trim());
					$("#phonenumber-input").val("+"+tel_input.getSelectedCountryData().dialCode +' '+ phoneNumberInp.val().trim());
				} else {
					var errorCode = tel_input.getValidationError();
					errorInnerHTML = errorMap[errorCode];
				}
			} else {
				errorInnerHTML = '<?= __('store.mobile_number_is_required') ?>';
			}

			$(".checkout-form .text-danger").remove();

			if(!is_valid){
				phoneNumberInp.parents(".form-group").addClass("has-error");
				phoneNumberInp.parents(".form-group").find('> div').after("<span class='text-danger'>"+ errorInnerHTML +"</span>");

				return false;
			}
		}


		$this = $(this);
		$container = $(".checkout-setp");		 
		var formData = new FormData();

		$container.find("input[type=text],input[type=hidden],input[type=file],select,input[type=checkbox]:checked,input[type=radio]:checked,textarea").each(function(i,j){
			formData.append($(j).attr("name"),$(j).val());
		})
		if(typeof fileArray != 'undefined'){
			$.each(fileArray, function(i,j){ formData.append("downloadable_file[]", j.rawData); });
		}


		formData = formDataFilter(formData);

		$.ajax({
			url:'<?= $base_url ?>confirm_order',
			type:'POST',
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
						$('.loading-submit').text(percentComplete + "% "+'<?= __('store.loading') ?>');
					}
				}, false );

				jqXHR.addEventListener( "progress", function ( evt ){
					if ( evt.lengthComputable ){
						var percentComplete = Math.round( (evt.loaded * 100) / evt.total );
						$('.loading-submit').text('<?= __('store.save') ?>');
					}
				}, false );
				return jqXHR;
			},
			beforeSend:function(){$this.btn("loading");},
			complete:function(){$this.btn("reset");},
			success:function(result){
				$container.find(".has-error").removeClass("has-error");
				$container.find("span.text-danger,.alert-danger").remove();
				$('.loading-submit').hide();

				if(IsJsonString(result)) 
				{
					var result = $.parseJSON(result);
					if(result['confirm']){
						$("#checkout-confirm").html(result['confirm']);
						$(".confirm-checkout").show();
						$(".non-confirm").hide();
					}
					if(result['error']){
						$(".warning-div").html('<div class="alert alert-danger">'+ result['error'] +'</div>');
					}
					if(result['errors']){
						$.each(result['errors'], function(i,j){
							$ele = $container.find('[name="'+ i +'"]');
							if($ele){
								$ele.parents(".form-group").addClass("has-error");
								$ele.after("<span class='text-danger'>"+ j +"</span>");
							}
						})
					}
				} else {
					$("#checkout-confirm").html(result);
					$(".confirm-checkout").show();
					$(".non-confirm").hide();
				}
			},
		})
	});

	function IsJsonString(str) {
		try {
			JSON.parse(str);
		} catch (e) {
			return false;
		}
		return true;
	}

	function backCheckout(){
		$("#checkout-confirm").html('');
		$(".confirm-checkout").hide();
		$(".non-confirm").show();
	}
	$("#login-form").on('submit',function(){
		$this = $(this);
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
					location = '<?= $checkout_url ?>';
				}

				if(result['errors']){
					$.each(result['errors'], function(i,j){
						if(i=='captch_response'){
							if(typeof gcaptch['client_login'] != 'undefined'){
								grecaptcha.reset(gcaptch['client_login']);
							}
						}

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

	$("#register-form").on('submit',function(e){
		e.preventDefault();
		$this = $(this);

		var errorMap = ['<?= __('store.invalid_number') ?>','<?= __('store.invalid_country_code') ?>','<?= __('store.too_short') ?>','<?= __('store.too_long') ?>','<?= __('store.invalid_number') ?>'];
		var is_valid = false;
		var errorInnerHTML = '';
		
		if ($("#phoneergister").val().trim()) {
			if (tel_inputre.isValidNumber()) {
				is_valid = true;
				tel_inputre.setNumber($("#phoneergister").val().trim());
				$("#register-form").find('input[name="PhoneNumberInput"]').val("+"+tel_inputre.getSelectedCountryData().dialCode +' '+ $("#phoneergister").val().trim());
			} else {
				var errorCode = tel_inputre.getValidationError();
				errorInnerHTML = errorMap[errorCode];
			}
		} else {
			errorInnerHTML = '<?= __('store.mobile_number_is_required') ?>';
		}

		$("#phoneergister").parents(".form-group").removeClass("has-error");

		$("#register-form .text-danger").remove();

		if(! is_valid){
			$("#phoneergister").parents(".form-group").addClass("has-error");
			$("#phoneergister").parents(".form-group").find('> div').after("<span class='text-danger'>"+ errorInnerHTML +"</span>");
			return false;
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
					location = '<?= $checkout_url ?>';
				}


				if(result['errors']){


					$.each(result['errors'], function(i,j){
						if(i=='captch_response'){
							if(typeof gcaptch['client_register'] != 'undefined'){
								grecaptcha.reset(gcaptch['client_register']);
							}
						}

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
	})
</script>

<script type="text/javascript">

	$(document).delegate(".cart-counter button","click",function(){
		var val = $(this).parent().find("input").val();
		if($(this).hasClass("plus")) { val ++ }
			else { val -- }
				if(val <= 0) val = 1;
			$(this).parent().find("input").val(val).trigger("change");
		});

	var fileArray = [];
	$('.downloadable_file_input').on('change',function(e){
		$.each(e.target.files, function(index, value){
			var fileReader = new FileReader(); 
			fileReader.readAsDataURL(value);
			fileReader.name = value.name;
			fileReader.rawData = value;
			fileArray.push(fileReader);
		});

		render_priview();
	});

	var getFileTypeCssClass = function(filetype) {
		var fileTypeCssClass;
		fileTypeCssClass = (function() {
			switch (true) {
				case /image/.test(filetype): return 'image';
				case /video/.test(filetype): return 'video';
				case /audio/.test(filetype): return 'audio';
				case /pdf/.test(filetype): return 'pdf';
				case /csv|excel/.test(filetype): return 'spreadsheet';
				case /powerpoint/.test(filetype): return 'powerpoint';
				case /msword|text/.test(filetype): return 'document';
				case /zip/.test(filetype): return 'zip';
				case /rar/.test(filetype): return 'rar';
				default: return 'default-filetype';
			}
		})();
		return fileTypeCssClass;
	};

	function render_priview() {
		var html = '';

		$.each(fileArray, function(i,j){
			html += '<tr>';
			html += '    <td width="70px"> <div class="upload-priview up-'+ getFileTypeCssClass(j.rawData.type) +'" ></div></td>';
			html += '    <td>'+ j.name +'</td>';
			html += '    <td width="70px"><button type="button" class="btn btn-danger btn-sm remove-priview" onClick="removeTr(this)" data-id="'+ i +'" >'+'<?= __('store.remove') ?>'+'</button></td>';
			html += '</tr>';
		})

		$("#priview-table tbody").html(html);
		if(html) {
			$("#priview-table").show();
		} else {
			$("#priview-table").hide();
		}
	}

	function removeTr(t){
		if(!confirm('<?= __('store.are_you_sure') ?>')) return false;

		var index = $(t).attr("data-id");
		fileArray.splice(index,1);
		render_priview()
	}

	let classified_checkout_form_country_select = $('select[name="country"]');
	let classified_checkout_form_state_select = $('select[name="state"]');

	if(classified_checkout_form_country_select.length) {
		classified_checkout_form_country_select.load('<?= base_url(); ?>theme_api/PublicData/get_checkout_countries');

		classified_checkout_form_country_select.on('change', function(event){
			if(classified_checkout_form_state_select.length) {
				classified_checkout_form_state_select.load('<?= base_url(); ?>theme_api/PublicData/get_checkout_states/'+classified_checkout_form_country_select.val());
			}
		});
	}
	$("#btnGuestcontinues").click(function(){
		$.ajax({
			url:'<?= base_url() ?>store/guestCheckout',
			type:'POST',
			dataType:'json',
			success:function(result){
				if(result.status) {
					$(".checkout-form").show();
					$(".checkout-payments").show();
					$(".auth-step").hide();
					window.location.reload();
				}
			},
		})
	})

	function isNumberKey(evt)
	{
	  var charCode = (evt.which) ? evt.which : event.keyCode;
	    if (charCode != 46 && charCode != 45 && charCode > 31
	    && (charCode < 48 || charCode > 57))
	     return false;

	  return true;
	}
</script>