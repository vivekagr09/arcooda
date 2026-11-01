<div class="plugin-uploader text-center mb-3">
	<p><?= __('admin.if_have_plugin_in_zip_format_you_masy_insttall') ?> <br> <?= __('admin.if_want_to_creat_custom_payment_gateway') ?> <a href="<?= base_url('admincontrol/withdrawal_payment_gateways_doc') ?>"><?= __('admin.documentation') ?></a></p>
	<div class="d-flex justify-content-center">
		<div class="mb-2 me-2">
			<input type="file" id="plugin-file" name="plugin" class="form-control">
			<div class="bg-danger text-start text-light warning d-none"></div>
		</div>
		<div>
			<button class="btn btn-primary btn-sm" id="plugin-file-button" disabled><?= __('admin.install_now') ?></button>
		</div>
	</div>
</div>

<div class="card">
	<div class="card-header bg-secondary text-white">
		<div class="d-flex justify-content-between">
			<h5><?= __('admin.payments_settings') ?></h5>
			<button id="toggle-uploader" class="btn btn-light"><?= __('admin.install_payment_gateway') ?></button>
		</div>
	</div>
	<div class="card-body p-0">
		<div class="table-responsive">
			<table class="table">
				<thead>
					<tr>
						<th><?= __('admin.payment_method') ?></th>
						<th><?= __('admin.icon') ?></th>
						<th><?= __('admin.status') ?></th>
						<th><?= __('admin.action') ?></th>
					</tr>
				</thead>
				<tbody>
					<?php if(count($payment_methods) == 0){ ?>
						<tr>
							<td class="text-center" colspan="100%"><?= __('admin.no_payment_methods_available') ?></td>
						</tr>
					<?php } ?>
					<?php foreach ($payment_methods as $key => $payment) { ?>
						<tr>
							<td><?= __('admin.'.$payment['code']) ?></td>
							<td><img src="<?= base_url($payment['icon']) ?>" class="img-fluid"></td>
							<td>
							    <div class="form-check form-switch">
							        <input class="form-check-input paymentstatus" type="checkbox" <?= $payment['status'] == 1 ? 'checked' : '' ?> id="payment-status-switch" data-bs-on="<?= __('admin.status_on') ?>" data-bs-off="<?= __('admin.status_off') ?>" data_code="<?=$payment['code']?>">
							    </div>
							</td>
							<td>
								<?php if($payment['is_install'] == '1'){ ?>
									<a href="<?= base_url('admincontrol/withdrawal_payment_gateways_edit/'. $payment['code']) ?>" class="btn btn-sm btn-info"><?= __('admin.edit') ?></a>
								<?php } ?>
								<a onclick="return confirm('<?= __('admin.are_you_sure') ?>')" href="<?= base_url('admincontrol/withdrawal_payment_gateways_status_change/'. $payment['code']) ?>" class="btn btn-sm btn-<?= $payment['is_install'] == "1" ? "danger" : "success" ?>"><?= $payment['is_install'] == "1" ? __('admin.un_install') : __('admin.install') ?></a>
								<a onclick="return confirm('<?= __('admin.are_you_sure') ?>')" href="<?= base_url('payment/delete_plugin/'.$payment['code']) ?>" class="btn btn-sm btn-danger"><?= __('admin.delete') ?></a>
							</td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
		</div>
	</div>
</div>


<script type="text/javascript">
	$("#toggle-uploader").on("click",function(){
		$(".plugin-uploader").slideToggle();
	})

	$("#plugin-file").on("change",function(){
		if($(this).val() == ''){
			$("#plugin-file-button").prop("disabled",1)
		} else{
			$("#plugin-file-button").prop("disabled",0)
		}
	})

	$("#plugin-file-button").on("click", function(evt) {
	    evt.preventDefault();
	    $btn = $(this);

	    $(".plugin-uploader .warning").addClass('d-none');

	    var formData = new FormData();
	    formData.append("plugin", $("#plugin-file")[0].files[0]);
	    $btn.btn("loading");
	    
	    $.ajax({
	        url: '<?= base_url('payment/installPayementGateway') ?>',
	        type: 'POST',
	        dataType: 'json',
	        cache: false,
	        contentType: false,
	        processData: false,
	        data: formData,
	        error: function() { 
	            $btn.btn("reset"); 
	        },
	        success: function(result) {
	            $btn.btn("reset");

	            if(result['status'] === 'error') {
	                showPrintMessage(result['message'], 'error');
	            } else {
	                if(result['location']) {
	                    window.location.reload();
	                }
	                if(result['warning']) {
	                    $(".plugin-uploader .warning").html(result['warning']);
	                    $(".plugin-uploader .warning").removeClass('d-none');
	                }
	            }
	        },
	    });
	});

	$('.paymentstatus').on('change', function() {
	    var checked = $(this).prop('checked');
	    var code = $(this).attr('data_code');
	    console.log(code);
	    
	    var status = checked ? 1 : 0;

	    $.ajax({
	        url: '<?= base_url("admincontrol/withdrawal_payment_gateways_setting_save_ajax") ?>',
	        type: 'POST',
	        dataType: 'json',
	        data: {"status": status, "code": code},
	        success: function(json) {
	            if (json['status'] === 'error') {
	                showPrintMessage(json['message'], 'error');
	            } else {
	                if(json.status == 'true') {
	                    showPrintMessage(json.msg, 'success');
	                } else {
	                    showPrintMessage(json.msg, 'error');
	                }
	            }
	        },
	    });
	});


</script>