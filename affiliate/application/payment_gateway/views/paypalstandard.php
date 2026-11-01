<?php if($settingData['sandbox_mode']) { ?>
	<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> Test mode is on</div>
<?php } ?>

<form action="<?= ($settingData['sandbox_mode']) ? 'https://www.sandbox.paypal.com/cgi-bin/webscr' : 'https://www.paypal.com/cgi-bin/webscr'; ?>" method="post">
	<input type="hidden" name="cmd" value="_xclick">
	<input type="hidden" name="business" value="<?= $settingData['email']; ?>">
	<input type="hidden" name="item_name" value="Donation">
	<input type="hidden" name="item_number" value="1">
	<input type="hidden" name="amount" value="<?= $gatewayData['total']; ?>">
	<input type="hidden" name="no_shipping" value="0">
	<input type="hidden" name="no_note" value="1">
	<input type="hidden" name="currency_code" value="<?= $gatewayData['currency_code']; ?>">
	<input type="hidden" name="lc" value="AU">
	<input type="hidden" name="bn" value="PP-BuyNowBF">
	<input type="hidden" name="return" value="<?= $gatewayData['return']; ?>" />
	<input type="hidden" name="notify_url" value="<?= $gatewayData['notify_url']; ?>" />
	<input type="hidden" name="cancel_return" value="<?= $gatewayData['cancel_return']; ?>" />
	<input type="hidden" name="paymentaction" value="<?= ((int) $settingData['transaction'] == 0) ? 'authorization' : 'sale'; ?>" />
	<input type="hidden" name="custom" value="<?= $this->api->session->session_id; ?>" />
	<div class="payment-button-group">
		<button type="button" class="btn btn-default" onclick='backCheckout()'>Back</button>
		<input type="submit" id="btn-confirm" class="btn btn-primary" value="Confirm" style="display: none;" />
		<button id="button-confirm" class="btn btn-primary">Confirm</button>
	</div>
</form>


<script type="text/javascript">
	$("#button-confirm").click(function(){
		$this = $(this);
		
		$this.prop('disabled',true);

		$.ajax({
			url:'<?= $gatewayData['payment_confirmation'] ?>',
			type:'POST',
			dataType:'json',
			data:$('[name^="comment"]').serialize(),
			beforeSend:function(){$("#button-confirm").btn("loading");},
			complete:function(){$("#button-confirm").btn("reset");},
			success:function(json){
				$container = $("#checkout-confirm");
				$container.find(".has-error").removeClass("has-error");
				$container.find("span.text-danger").remove();

				if(json['errors']){
					$.each(json['errors']['comment'], function(ii,jj){
						$ele = $container.find('#comment_textarea'+ ii);
						if($ele){
							$ele.parents(".form-group").addClass("has-error");
							$ele.after("<span class='text-danger'>"+ jj +"</span>");
						}
					});
				}

				if(json['success'])
					$('#btn-confirm').trigger('click');
			},
		});
	})
</script>
