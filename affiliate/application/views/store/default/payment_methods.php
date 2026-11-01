<div class="payment-list checkout-payments-wrapper">
	<?php if(empty($payment_gateways)){ ?>
		<div class="alert alert-info"><?= __('store.no_payment_options') ?></div>
	<?php }; ?>
	
	<?php 
		$i = 0;
		foreach($payment_gateways as $key => $value){ ?>
		<a class="payment-gateway-step <?= ($i == 0) ? "active" : "" ?>" data-value="<?= $value['name'] ?>" href="javascript:void(0);">
			<img  src="<?= base_url($value['icon']); ?>" alt="<?= __('store.image') ?>" />
			<p><h3><?= $value['title']?></h3></p>
		</a>
		<input type="radio" name="payment_gateway" value="<?= $value['name'] ?>" <?= ($i == 0)? "checked" : ""?> style="display:none;">
	<?php $i++; } ?>
</div>
		   
<script type="text/javascript">
	$(document).on('click', ".payment-gateway-step", function(){
		$('.active').removeClass('active');
		$(this).addClass('active');
		$('input[name="payment_gateway"][value="'+$(this).data('value')+'"]').trigger('click');
	});
</script>