<div class="modal fade" id="setting-widzard" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true"></div>
<div class="modal fade" id="log-widzard" tabindex="-1" aria-hidden="true"></div>

<div class="modal fade" id="model-ajaxError" tabindex="-1" aria-labelledby="model-ajaxErrorLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content shadow-lg">
      <div class="modal-body text-center p-4">
        <img src="<?= base_url('assets/images/ajax-warning.png') ?>" alt="Warning" class="img-fluid mb-3 rounded-circle">
        <div class="-body"></div>
      </div>
      <div class="modal-footer justify-content-center">
        <button type="button" class="btn btn-danger btn-lg" data-bs-dismiss="modal"><?= __('admin.dismiss') ?></button>
      </div>
    </div>
  </div>
</div>



<script type="text/javascript" src="<?= base_url('assets/plugins/toastr/toastr.js') ?>"></script>
<script type="text/javascript">

	const serverErrorCode = {
	    100 : '<?= __('admin.continue') ?>',
	    101 : '<?= __('admin.switching_protocols') ?>',
	    102 : '<?= __('admin.processing') ?>', 
	    200 : '<?= __('admin.ok') ?>',
	    201 : '<?= __('admin.created') ?>',
	    202 : '<?= __('admin.accepted') ?>',
	    203 : '<?= __('admin.non_authoritative_information') ?>', 
	    204 : '<?= __('admin.no_content') ?>',
	    205 : '<?= __('admin.reset_content') ?>',
	    206 : '<?= __('admin.partial_content') ?>',
	    207 : '<?= __('admin.multi_status') ?>', 
	    208 : '<?= __('admin.already_reported') ?>', 
	    226 : '<?= __('admin.im_used') ?>', 
	    300 : '<?= __('admin.multiple_choices') ?>',
	    301 : '<?= __('admin.moved_permanently') ?>',
	    302 : '<?= __('admin.found') ?>',
	    303 : '<?= __('admin.see_other') ?>', 
	    304 : '<?= __('admin.not_modified') ?>',
	    305 : '<?= __('admin.use_proxy') ?>', 
	    306 : '<?= __('admin.switch_proxy') ?>',
	    307 : '<?= __('admin.temporary_redirect') ?>', 
	    308 : '<?= __('admin.permanent_redirect') ?>', 
	    400 : '<?= __('admin.bad_request') ?>',
	    401 : '<?= __('admin.unauthorized') ?>',
	    402 : '<?= __('admin.payment_required') ?>',
	    403 : '<?= __('admin.forbidden') ?>',
	    404 : '<?= __('admin.not_found') ?>',
	    405 : '<?= __('admin.method_not_allowed') ?>',
	    406 : '<?= __('admin.not_acceptable') ?>',
	    407 : '<?= __('admin.proxy_authentication_required') ?>',
	    408 : '<?= __('admin.request_timeout') ?>',
	    409 : '<?= __('admin.conflict') ?>',
	    410 : '<?= __('admin.gone') ?>',
	    411 : '<?= __('admin.length_required') ?>',
	    412 : '<?= __('admin.precondition_failed') ?>',
	    413 : '<?= __('admin.request_entity_too_large') ?>',
	    414 : '<?= __('admin.request_uri_too_long') ?>',
	    415 : '<?= __('admin.unsupported_media_type') ?>',
	    416 : '<?= __('admin.requested_range_not_satisfiable') ?>',
	    417 : '<?= __('admin.expectation_failed') ?>',
	    418 : '<?= __('admin.i_am_teapot') ?>', 
	    419 : '<?= __('admin.authentication_timeout') ?>', 
	    420 : '<?= __('admin.enhance_your_calm') ?>', 
	    420 : '<?= __('admin.method_failure') ?>', 
	    422 : '<?= __('admin.unprocessable_entity') ?>', 
	    423 : '<?= __('admin.locked') ?>', 
	    424 : '<?= __('admin.failed_dependency') ?>', 
	    424 : '<?= __('admin.method_failure') ?>', 
	    425 : '<?= __('admin.unordered_collection') ?>', 
	    426 : '<?= __('admin.upgrade_required') ?>', 
	    428 : '<?= __('admin.precondition_required') ?>', 
	    429 : '<?= __('admin.too_many_requests') ?>', 
	    431 : '<?= __('admin.request_header_fields_too_large') ?>', 
	    444 : '<?= __('admin.no_response') ?>', 
	    449 : '<?= __('admin.retry_with') ?>', 
	    450 : '<?= __('admin.blocked_by_windows_parental_controls') ?>', 
	    451 : '<?= __('admin.redirect') ?>', 
	    451 : '<?= __('admin.unavailable_for_legal_reasons') ?>', 
	    494 : '<?= __('admin.request_header_too_large') ?>', 
	    495 : '<?= __('admin.cert_error') ?>', 
	    496 : '<?= __('admin.no_cert') ?>', 
	    497 : '<?= __('admin.http_to_https') ?>', 
	    499 : '<?= __('admin.client_closed_request') ?>', 
	    500 : '<?= __('admin.internal_server_error') ?>',
	    501 : '<?= __('admin.not_implemented') ?>',
	    502 : '<?= __('admin.bad_gateway') ?>',
	    503 : '<?= __('admin.service_unavailable') ?>',
	    504 : '<?= __('admin.gateway_timeout') ?>',
	    505 : '<?= __('admin.http_version_not_supported') ?>',
	    506 : '<?= __('admin.variant_also_negotiates') ?>', 
	    507 : '<?= __('admin.insufficient_storage') ?>', 
	    508 : '<?= __('admin.loop_detected') ?>', 
	    509 : '<?= __('admin.bandwidth_limit_exceeded') ?>', 
	    510 : '<?= __('admin.not_extended') ?>', 
	    511 : '<?= __('admin.network_authentication_required') ?>', 
	    598 : '<?= __('admin.network_read_timeout_error') ?>', 
	    599 : '<?= __('admin.network_connect_timeout_error') ?>', 
   	}

	toastr.options = {
	  "closeButton": true,
	  "debug": false,
	  "newestOnTop": true,
	  "progressBar": true,
	  "positionClass": "toast-top-right",
	  "preventDuplicates": false,
	  "onclick": null,
	  "showDuration": "300",
	  "hideDuration": "20000",
	  "timeOut": "20000",
	  "extendedTimeOut": "20000",
	  "showEasing": "swing",
	  "hideEasing": "linear",
	  "showMethod": "fadeIn",
	  "hideMethod": "fadeOut"
	};

	const show_tost = (type, title, message) => {
	  toastr[type](message, title);
	};
	
	$(".btn-setting").on('click', function() {
		$this = $(this);
		
		$.ajax({
			url:'<?= base_url('setting/getModal') ?>',
			type:'POST',
			dataType:'json',
			data:{
				'key' : $this.attr('data-key'),
				'type' : $this.attr('data-type'),
			},
			success:function(json){
				if(json['html']){
					$("#setting-widzard").html(json['html']);
					$("#setting-widzard").modal('show');
				}
			},
		})
	})

  	$(document).delegate('.allow-number','keypress keyup blur',function(event) {  		
    	$(this).val($(this).val().replace(/[^0-9\.]/g,''));
        if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
            event.preventDefault();
        }
  	});

  	$(document).delegate("[data-log]",'click',function(){
  		$this = $(this);

  		var data = {};
  		var search = $this.attr('data-extra');
  		if(search){
			data = JSON.parse('{"' + decodeURI(search).replace(/"/g, '\\"').replace(/&/g, '","').replace(/=/g,'":"') + '"}')
  		}

  		data['type'] =$this.attr("data-log");
        <?php $usercontrol = isset($usercontrol)?$usercontrol:''; ?>
  		$.ajax({
  			url:'<?= base_url( $usercontrol ? 'usercontrol/logs' : 'admincontrol/logs') ?>',
  			type:'POST',
  			dataType:'json',
  			data:data,
  			beforeSend:function(){$this.btn("loading");},
  			complete:function(){$this.btn("reset");},
  			success:function(json){
  				if(json['html']){
  					$("#log-widzard").modal({
						backdrop: 'static',
						keyboard: false
					});
					$("#log-widzard").html(json['html']);
				}
  			},
  		})
  	})

  	$(".password-group .input-group-prepend button").on('click',function(){
		$input = $(this).parents(".password-group").find("input");
		$i = $(this).parents(".password-group").find("i");
  		if($i.hasClass("fa-eye")){
  			$i.addClass("fa-eye-slash");
  			$i.removeClass("fa-eye");
  			$input.attr('type','text');
  		} else {
  			$i.addClass("fa-eye");
  			$i.removeClass("fa-eye-slash");
  			$input.attr('type','password');
  		}
  	})

  	$(document).ajaxComplete(function myErrorHandler(event, xhr, ajaxOptions, thrownError) {
  		var statusCode = xhr.status;

  		
  		if(statusCode != 200 && ajaxOptions.type == 'POST'){  			
	  		var title = '';
	  		var body = '';

	  		title = '<?= __('admin.internal_server_error') ?>';
	  		body += '<h3><?= __('admin.sorry_an_error_has_occured') ?></h3>';

		  	if(serverErrorCode[statusCode]){
	  			body += "<p><?= __('admin.error_message') ?> : " + serverErrorCode[statusCode] + "</p>";
		  		body += "<p><?= __('admin.error_code') ?> : " + statusCode + "</p>";

				$("#model-ajaxError .modal-title").html(title);
				$("#model-ajaxError .modal-body .-body").html(body);
				$("#model-ajaxError").modal("show");
		  	} else {
			  	body += '<p><?= __('admin.error_message') ?> : <?= __('admin.uknown_error') ?> </p>';
			}

			$(".btn-loading").removeClass('btn-loading');
  		}
	});
</script>