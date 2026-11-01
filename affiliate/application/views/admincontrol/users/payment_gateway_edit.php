<?php
	$db = & get_instance();
	$userdetails = $db->userdetails();
	$store_setting = $db->Product_model->getSettings('store');
?>

<form id="setting-form">
	<div class="row">
		<div class="col-sm-12">
		    <div class="card">
		    	<div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
		    		<h5><?= __('admin.payment_gateway') ?> (<?= $payment_gateway['title'] ?>)
		    		</h5>
			    	<button id="toggle-uploader" class="btn btn-submit btn-light" type="submit"><?= __('admin.save_settings') ?>
			    	</button>
		    	</div>
		    	<div class="row">
		    		<div class="col-lg-6">
				    	<div class="card-body">
							<?php if($payment_gateway['code'] == 'paystack'){ ?>
								<h4 class="notification_on_pages">
								    <button id="toggle-uploader" class="btn btn-light" type="button">
								        <?= __('admin.paystack_accept_only_currency'); ?>
								    </button>
								</h4>
							<?php } ?>
				    		<?php if($payment_gateway['code'] == 'xendit'){ ?>
									<h4 class="notification_on_pages">
									    <button id="toggle-uploader" class="btn btn-light" type="button">
									        <?= __('admin.xendit_accept_only_currency'); ?>
									    </button>
									</h4>
				    		<?php } ?>
				    		<?php if($payment_gateway['code'] == 'yookassa'){ ?>
									<h4 class="notification_on_pages">
									    <button id="toggle-uploader" class="btn btn-light" type="button">
									        <?= __('admin.yookassa_accept_only_currency'); ?>
									    </button>
									</h4>
				    		<?php } ?>
				    		<?= $payment_gateway['setting'] ?>
				    	</div>
		    		</div>
		    		<div class="col-lg-6 payment-image"><img src="<?= base_url('/assets/images/payment-side2.jpg') ?>"></div>
		    	</div>
			</div>
	    </div>
	</div>
</form>

<script type="text/javascript">
	$("#setting-form").on('submit',function(){
		let isDirtyForm = false;

		$( ".form-control.required" ).each(function() {
			if($(this).val() == "" || $(this).val() == null){
			  	$(this).parent().addClass('has-error');
			  	$(this).after('<span class="text-danger">'+'<?= __('admin.this_field_is_required') ?>'+'</span>');
			  	isDirtyForm = true;
			}
		});

		if(!isDirtyForm){
			$this = $(this);
			$.ajax({
				type:'POST',
				dataType:'json',
				data:$this.serialize(),
				beforeSend:function(){ $this.find('.btn-submit').btn("loading"); },
				complete:function(){ $this.find('.btn-submit').btn("reset"); },
				success:function(json){
					if(json['redirect'])
						window.location.href = json['redirect'];
				},
			});
		}

		return false;
	})
</script>
