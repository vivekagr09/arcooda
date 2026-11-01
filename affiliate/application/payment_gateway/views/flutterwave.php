<div class="payment-button-group">
	<button type="button" class="btn btn-default" onclick='backCheckout()'>Back</button>
	<button id="button-confirm" class="btn btn-primary">Confirm</button>
</div>
<script src="https://checkout.flutterwave.com/v3.js"></script>
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

				if(json['success']){
					FlutterwaveCheckout({
						public_key: "<?= ($settingData['environment'] == 1) ? $settingData['live_public_key'] : $settingData['test_public_key']; ?> ",
						tx_ref: "<?= $gatewayData['id']; ?>",
						amount: <?= $gatewayData['total']; ?>,
						currency: "<?= $gatewayData['currency_code']; ?>",
						payment_options: "card, mobilemoneyghana, ussd",
						redirect_url: "<?= $gatewayData['redirect_url']; ?>",
						meta: {
							consumer_id: <?= $gatewayData['id']; ?>,
							consumer_mac: "<?= exec('getmac'); ?>",
					    },
						customer: {
							email: "<?= $gatewayData['email']; ?>",
							phone_number: "<?= $gatewayData['phone']; ?>",
							name: "<?= $gatewayData['firstname'].' '.$gatewayData['lastname']; ?>",
						},
						customizations: {
							title: "<?= $gatewayData['title']; ?>",
							description: "<?= $gatewayData['firstname'] . ' ' . $gatewayData['lastname']; ?>",
							logo: 'https://assets.piedpiper.com/logo.png',
						},
					});
				}
			},
		});
	});
</script>