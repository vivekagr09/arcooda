(function ($) {
	$.fn.btn = function (action) {
		var self = $(this);
		if (action == 'loading') {
			if ($(self).attr("disabled") == "disabled") {
             //e.preventDefault();
         }
         $(self).attr("disabled", "disabled");
         $(self).attr('data-btn-text', $(self).html());
         $(self).html('<div class="spinner-border spinner-border-sm"></div>&nbsp;' + $(self).text());
     }
     if (action == 'reset') {
     	$(self).html($(self).attr('data-btn-text'));
     	$(self).removeAttr("disabled");
     }
 }
})(jQuery);

let afftemplate = $("[aff-template]");

afftemplate.each(function() {
	let section = $(this).attr("aff-template"); 

	if($("[aff-section='"+section+"']").length > 0) {

		let post_payload = {};
		
		let aff_ajax_url = BASE_URL+'theme_api/PublicData/get_'+section;

		if($("input[name='aff_item_id']").length > 0) {
			aff_ajax_url = aff_ajax_url+"/"+$("input[name='aff_item_id']").val();
		}
		
		if($("textarea[name='aff_query_payload']").length > 0) {
			post_payload.aff_query_payload = $("textarea[name='aff_query_payload']").val();
		}
		


		$.post(aff_ajax_url+window.location.search, post_payload).then(response => {

			if(response.length > 0) {
				response = JSON.parse(response);
				
				if(typeof window['aff_prepare_'+section] === 'function') {
					response = window['aff_prepare_'+section](response);
				}
			}


			let rendered = Mustache.render($("[aff-template='"+section+"']").html(), response);
			
			$("[aff-section='"+section+"']").html(rendered);

			if(typeof window.AFF_PREVIEW_PAGE === "undefined") {
				if(section === "classified_checkout_page") {
					init_classified_checkout_page();
				}

				if(section === "classified_home_page") {
					init_classified_home_page();
				}
			}

			$( document ).trigger("affPageReady");

		});
	}
});

var init_classified_checkout_page = () => {
	let classified_checkout_form = $('[aff-section="classified_checkout_form"]');
	let confirm_classified_checkout_form = $('[aff-section="confirm_classified_checkout_form"]');
	
	let classified_checkout_form_country_select = $('[aff-section="classified_checkout_form"] select[name="country"]');
	let classified_checkout_form_state_select = $('[aff-section="classified_checkout_form"] select[name="state"]');
	
	confirm_classified_checkout_form.hide();

	if(classified_checkout_form_country_select.length) {
		classified_checkout_form_country_select.load(BASE_URL+'theme_api/PublicData/get_checkout_countries');

		classified_checkout_form_country_select.on('change', function(event){
			if(classified_checkout_form_state_select.length) {
				classified_checkout_form_state_select.load(BASE_URL+'theme_api/PublicData/get_checkout_states/'+classified_checkout_form_country_select.val());
			}
		});
	}


	$(document).on('click', '[aff-button="classified_checkout_form"]', function(){
		
		$("span.text-danger").remove();

		let phoneNumberInp = null;

		if($("#phone").length) {
			phoneNumberInp = $("#phone");
		} else if($("#phoneguest").length) {
			phoneNumberInp = $("#phoneguest");
		}

		if(phoneNumberInp !== null) {
			var is_valid = false;
			
			var errorInnerHTML = '';

			if (phoneNumberInp.val().trim()) {
				if (window.tel_input.isValidNumber()) {
					is_valid = true;
					window.tel_input.setNumber(phoneNumberInp.val().trim());
					$("#phonenumber-input").val("+"+window.tel_input.getSelectedCountryData().dialCode +' '+ phoneNumberInp.val().trim());
				} else {
					var errorCode = window.tel_input.getValidationError();
					errorInnerHTML = window.errorMap[errorCode];
				}
			} else {
				is_valid = true;
			}


			if(!is_valid){
				console.log(errorInnerHTML);
				phoneNumberInp.parent().after("<span class='text-danger'>"+ errorInnerHTML +"</span>");
				return false;
			}
		}


		$(document).find(".has-error").removeClass("has-error");
		$(document).find("span.text-danger,.alert-danger").remove();
		$("#isErrorAgree").hide();
		
		let formData = new FormData();

		$(document).find("input[type=hidden],input[type=email],input[type=text],input[type=file],select,input[type=checkbox]:checked,input[type=radio]:checked,textarea").each(function(i,j){
			if($(j).val() !== null) {
				formData.append($(j).attr("name"),$(j).val());
			}
		})
		
		formData.append('classified_checkout', 1);
		
		formData = formDataFilter(formData);

		$.ajax({
			url:BASE_URL+'store/confirm_order',
			type:'POST',
			dataType:'json',
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
						console.log( 'Uploaded percent', percentComplete );
						$('.loading-submit').text(percentComplete + "% Loading");
					}
				}, false );

				jqXHR.addEventListener( "progress", function ( evt ){
					if ( evt.lengthComputable ){
						var percentComplete = Math.round( (evt.loaded * 100) / evt.total );
						$('.loading-submit').text("Save");
					}
				}, false );
				return jqXHR;
			},
			beforeSend:function(){$('[aff-button="classified_checkout_form"]').btn("loading");},
			complete:function(){$('[aff-button="classified_checkout_form"]').btn("reset");},
			success:function(result){
				if(result['confirm']){
					classified_checkout_form.hide(result['confirm']);
					confirm_classified_checkout_form.show();
					let confirm_checkout_html = '<div id="checkout-confirm">'+result['confirm']+'</div>';
					confirm_classified_checkout_form.html(confirm_checkout_html);
					$('[aff-button="classified_checkout_form"]').hide();
				}

				if(result['error']){
					$('[aff-button="classified_checkout_form"]').before('<div class="alert alert-danger">'+ result['error'] +'</div>');
				}

				if(result['errors']){
					$.each(result['errors'], function(i,j){
						$ele = $(document).find('[name="'+ i +'"]');
						if($ele){
							if($ele.attr('name')=="agree") {
								$("#isErrorAgree").show().html(j)
							}
							$ele.parents(".form-group").addClass("has-error");
							$ele.after("<span class='text-danger'>"+ j +"</span>");
						}
					})
				}
			},
		})
	});
}

var init_classified_home_page = () => {
	
}


function backCheckout(){
	$('[aff-section="confirm_classified_checkout_form"]').html('');
	$('[aff-section="confirm_classified_checkout_form"]').hide();
	$('[aff-section="classified_checkout_form"]').show();
	$('[aff-button="classified_checkout_form"]').show();
}


$(document).on('submit', '#aff-classified-login-form', function(e) {

	e.preventDefault();

	let check_captch = grecaptcha !== undefined;

	$("#captch_response").val('')

	if(check_captch){
		captch_response = grecaptcha.getResponse();
		$("#captch_response").val(captch_response)
	}

	$('#aff-classified-login-form').find(".has-error").removeClass("has-error");
	$('#aff-classified-login-form').find("span.text-danger").remove();
	$("#aff-classified-login-form").find("div.alert").remove();

	$.ajax({
		url: BASE_URL+'store/ajax_login',
		type:'POST',
		dataType:'JSON',
		data:$('#aff-classified-login-form').serialize(),
		beforeSend:function(){$("#aff-classified-login-form").find(".submitbtn").btn("loading");},
		complete:function(){$("#aff-classified-login-form").find(".submitbtn").btn("reset");},
		success:function(result){

			if(result['error']){
				$("#aff-classified-login-form").prepend('<div class="alert alert-danger">'+result['error']+'</div>');
			}

			if(result['success']){
				if(typeof ischeckout !="undefined")
					location = BASE_URL+'store/checkout';
				else
					location = BASE_URL+'store/profile';
			}
			if(result['errors']){
				
				$.each(result['errors'], function(i,j){
					if(i == 'captch_response' && grecaptcha){ grecaptcha.reset(0); }
					$ele = $('#aff-classified-login-form').find('[name="'+ i +'"]');
					if($ele){
						$ele.parents(".form-group").addClass("has-error");
						$ele.after("<span class='text-danger'>"+ j +"</span>");
					}
				})
			}
		},
	});

	return false;
});


$(document).on('submit', '#aff-classified-registration-form', function(e) {

	e.preventDefault();

	$("span.text-danger").remove();

	let phoneNumberInp = null;

	if($("#phone").length) {
		phoneNumberInp = $("#phone");
	} else if($("#phoneguest").length) {
		phoneNumberInp = $("#phoneguest");
	}

	if(phoneNumberInp !== null) {
		var is_valid = false;
		
		var errorInnerHTML = '';

		if (phoneNumberInp.val().trim()) {
			if (window.tel_input.isValidNumber()) {
				is_valid = true;
				window.tel_input.setNumber(phoneNumberInp.val().trim());
				$("#phonenumber-input").val("+"+window.tel_input.getSelectedCountryData().dialCode +' '+ phoneNumberInp.val().trim());
			} else {
				var errorCode = window.tel_input.getValidationError();
				errorInnerHTML = window.errorMap[errorCode];
			}
		} else {
			is_valid = true;
		}


		if(!is_valid){
			console.log(errorInnerHTML);
			phoneNumberInp.parent().after("<span class='text-danger'>"+ errorInnerHTML +"</span>");
			return false;
		}
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


	$("#aff-classified-registration-form").find(".has-error").removeClass("has-error");
	$("#aff-classified-registration-form").find("span.text-danger").remove();
	$("#aff-classified-registration-form").find("div.alert").remove();

	$.ajax({
		url:BASE_URL+'store/ajax_register',
		type:'POST',
		dataType:'json',
		data:$("#aff-classified-registration-form").serialize(),
		beforeSend:function(){$("#aff-classified-registration-form").find(".submitbtn").btn("loading");},
		complete:function(){$("#aff-classified-registration-form").find(".submitbtn").btn("reset");},
		success:function(result){

			if(result['error']){
				$("#aff-classified-registration-form").prepend('<div class="alert alert-danger">'+result['error']+'</div>');
			}

			if(result['success']){
				function getUrlParameter(name) {
					name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
					var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
					results = regex.exec(location.search);
					return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
				}
				if(getUrlParameter('url')=='checkout'){
					location = BASE_URL+'store/checkout';
				} else {
					location = BASE_URL+'store';

				}
			}
				if(result['errors']){
					$.each(result['errors'], function(i,j){
						if(i == 'captch_response' && grecaptcha){ grecaptcha.reset(captchIndex); }
						$ele = $("#aff-classified-registration-form").find('[name="'+ i +'"]');
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


$(document).on('submit', '#aff-classified-forgot-form', function(e) {

	e.preventDefault();

	$("#aff-classified-forgot-form").find(".has-error").removeClass("has-error");
	$("#aff-classified-forgot-form").find("span.text-danger").remove();
	$("#aff-classified-forgot-form").find("div.alert").remove();

	$.ajax({
		url:BASE_URL+'store/forgot',
		type:'POST',
		dataType:'json',
		data:$("#aff-classified-forgot-form").serialize(),
		beforeSend:function(){$("#aff-classified-forgot-form").find(".submitbtn").btn("loading");},
		complete:function(){$("#aff-classified-forgot-form").find(".submitbtn").btn("reset");},
		success:function(result){
			
			if(result['success']){
				$("#aff-classified-forgot-form").prepend('<div class="alert alert-success">'+result['success']+'</div>');
			}

			if(result['error']){
				$("#aff-classified-forgot-form").prepend('<div class="alert alert-danger">'+result['error']+'</div>');
			}
			
			if(result['errors']){
				$.each(result['errors'], function(i,j){
					if(i == 'captch_response' && grecaptcha){ 
						grecaptcha.reset(captchIndex); 
					}
					$ele = $("#aff-classified-forgot-form").find('[name="'+ i +'"]');
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

$(document).on('submit', '#aff-classified-contact-form', function(e) {

	e.preventDefault();

	$("#aff-classified-contact-form").find(".has-error").removeClass("has-error");
	$("#aff-classified-contact-form").find("span.text-danger").remove();
	$("#aff-classified-contact-form").find("div.alert").remove();
	var captchIndex = $('[name="captch_response"]').length == 2 ? 1 : 0;
	$.ajax({
		url:BASE_URL+'classified/send_contact_message',
		type:'POST',
		dataType:'json',
		data:$("#aff-classified-contact-form").serialize(),
		beforeSend:function(){$("#aff-classified-contact-form").find(".submit-btn").btn("loading");},
		complete:function(){$("#aff-classified-contact-form").find(".submit-btn").btn("reset");},
		success:function(result){
			
			if(result['success']){
				$("#aff-classified-contact-form").find('input').val('')
				$("#aff-classified-contact-form").find('textarea').val('')
				$("#aff-classified-contact-form").prepend('<div class="alert alert-success">'+result['success']+'</div>');
			}

			if(result['error']){
				$("#aff-classified-contact-form").prepend('<div class="alert alert-danger">'+result['error']+'</div>');
			}
			
			if(result['errors']){
				$.each(result['errors'], function(i,j){
					if(i == 'captch_response' && grecaptcha){ 
						grecaptcha.reset(captchIndex); 
					}
					$ele = $("#aff-classified-contact-form").find('[name="'+ i +'"]');
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

