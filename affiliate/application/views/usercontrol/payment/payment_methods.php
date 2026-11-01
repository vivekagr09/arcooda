<style type="text/css">
.checkout-payments-wrapper {
	display: flex;
	flex-flow: wrap;
	justify-content: center;
}
.checkout-payments-wrapper a {
	text-decoration: none;
	width: 220px;
	height: 102px;
	background: #FFFFFF 0% 0% no-repeat padding-box;
	box-shadow: 0px 0px 6px #00000029;
	border-radius: 10px;
	display: flex;
	flex-flow: column;
	justify-content: center;
	align-items: center;
	text-align: center;
	margin: 10px;
	font: normal normal 17px/22px Jost;
	letter-spacing: 0px;
	color: #868382;
}
.checkout-payments-wrapper a p {
	margin: 5px 0 0;
	font: normal normal 17px/22px Jost;
    letter-spacing: 0px;
    color: #868382;
}
.checkout-payments-wrapper a h3 {
	margin: 5px 0 0;
	font: normal normal 17px/22px Jost;
    letter-spacing: 0px;
    color: #868382;
}
.checkout-payments-wrapper a img {
	max-width: 95px;
	max-height: 40px;
}
.checkout-payments-wrapper a:hover, .checkout-payments-wrapper a.active {
	border: 2px solid #442781;
}
.checkout-payments .custom-control.custom-checkbox .custom-control-label {
	font: normal normal normal 17px/22px Jost;
	letter-spacing: 0px;
	color: #676767;
}
.checkout-payments .custom-control.custom-checkbox {
	padding-left: 35px;
	margin-top: 35px;
}

.checkout-payments-wrapper a {
	margin: 8px;
	width: 200px;
}
.checkout-payments-wrapper a {
	padding: 0 0;
	margin: 5px;
}
</style>

<div class="payment-list checkout-payments-wrapper">
	<?php if(!$payment_gateways){ ?>
		<div class="alert alert-info"><?= __('user.warning_no_payment_available_contact_store_owner') ?> </div>
	<?php } ?>
	
	<?php $i = 0; foreach($payment_gateways as $key => $value) { ?>
		<a class="payment-gateway-step <?= ($i == 0)? "active" : ""?>" data-value="<?= $value['name'] ?>" href="javascript:void(0);">
			<img alt="img"  src="<?= base_url($value['icon']); ?>" />
			<p><?= $value['title']?></p>
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