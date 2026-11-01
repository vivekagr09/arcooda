<?php  if(isset($_SESSION['guestFlow']) || $allow_shipping) { ?>
	<div class="shipping-warning"></div>
<div class="row">
	
	<?php  if(isset($_SESSION['guestFlow'])) {?>

		<div class="col-12 col-sm-4"><div class="form-group">
			<label><?= __('store.first_name') ?></label>
			<input type="text" placeholder="<?= __('store.first_name') ?>" name="firstname" class="form-control" type="text" value="" required="">
		</div>
		</div>
		<div class="col-12 col-sm-4"><div class="form-group">
			<label><?= __('store.last_name') ?></label>
			<input type="text" placeholder="<?= __('store.last_name') ?>" name="lastname" class="form-control" type="text" value="" required="">
		</div>
		</div>
		<div class="col-12 col-sm-4"><div class="form-group">
			<label><?= __('store.email_address') ?></label>
			<input type="text" placeholder="<?= __('store.email_address') ?>" name="email" class="form-control" type="text" value="" required="">
			<input type="hidden" name="classified_checkout" value="1">	
		</div>
		</div>
	<?php } ?>

	<div class="col-12 col-sm-4">
		<div class="form-group">
			<?php $selected =  isset($shipping) ? $shipping->country_id : '' ?>
			<?php $selected =  isset($country_id) ? $country_id : $selected ?>
			<select class="form-control" name="country">
					<option value="">Select Country</option>
				<?php foreach ($countries as $key => $value) { ?>
					<option <?= $selected == $value->id ? 'selected' : '' ?> value="<?= $value->id ?>"><?= $value->name ?></option>
				<?php } ?>
			</select>
		</div>
	</div>
	<div class="col-12 col-sm-4">
		<div class="form-group">
			<select class="form-control" name="state"></select>
		</div>
	</div>
	<div class="col-12 col-sm-4">
		<div class="form-group">
			<input class="form-control" name="city" placeholder="<?= __('store.city') ?>" type="text" value="<?= isset($shipping) ? $shipping->city : '' ?>">
		</div>
	</div>
</div>
<div class="row">
	<div class="col-12 col-sm-6">
		<div class="form-group">
			<input class="form-control" name="zip_code" placeholder="<?= __('store.postal_code') ?>" type="text" value="<?= isset($shipping) ? $shipping->zip_code : '' ?>">
		</div>
	</div>
	<div class="col-12 col-sm-6">
		<div class="form-group">
			<input type="hidden" id="phonenumber-input" name='PhoneNumberInput' value="" class="form-control" placeholder="<?= __('store.phone_number') ?>" />
			<div>
				<input id="phone" onkeypress="return isNumberKey(event);" class="form-control" type="text" name="phone" value="<?= isset($shipping) ? $shipping->phone : '' ?>">
			</div>
		</div>
		<script type="text/javascript">
			var tel_input = intlTelInput(document.querySelector("#phone"), {
				initialCountry: "auto",
				utilsScript: "<?= base_url('/assets/plugins/tel/js/utils.js?1562189064761') ?>",
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
</div>
<div class="row">
	<div class="col-12">
		<div class="form-group">
			<textarea class="form-control" placeholder="<?= __('store.full_address') ?>" name="address"><?= isset($shipping) ? $shipping->address : '' ?></textarea>
		</div>
	</div>
</div>
<script>
	renderStateAndCart(<?=$selected;?>);
</script>

<?php } else { ?>
	<?php if($show_blue_message){ ?>
		<div class="alert alert-info"><?= __('store.shipping_not_allows') ?></div>
	<?php } ?>
<?php } ?>