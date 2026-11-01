<div class="form-group">
	<label class="control-label"><?= __('admin.store_payment_status') ?></label>
	<select class="form-control" name="store">
		<option <?= (int)$setting_data['store']['status'] == '0' ? 'selected' : '' ?> value="0"><?= __('admin.disabled') ?></option>
		<option <?= (int)$setting_data['store']['status'] == '1' ? 'selected' : '' ?> value="1"><?= __('admin.enabled') ?></option>
	</select>
</div>

<div class="form-group">
	<label class="control-label"><?= __('admin.deposit_payment_status') ?></label>
	<select class="form-control" name="deposit">
		<option <?= (int)$setting_data['deposit']['status'] == '0' ? 'selected' : '' ?> value="0"><?= __('admin.disabled') ?></option>
		<option <?= (int)$setting_data['deposit']['status'] == '1' ? 'selected' : '' ?> value="1"><?= __('admin.enabled') ?></option>
	</select>
</div>

<div class="form-group">
	<label class="control-label"><?= __('admin.membership_payment_status') ?></label>
	<select class="form-control" name="membership">
		<option <?= (int)$setting_data['membership']['status'] == '0' ? 'selected' : '' ?> value="0"><?= __('admin.disabled') ?></option>
		<option <?= (int)$setting_data['membership']['status'] == '1' ? 'selected' : '' ?> value="1"><?= __('admin.enabled') ?></option>
	</select>
</div>

<div class="form-group">
	<label class="control-label"><span data-toggle="tooltip" title="" data-original-title="Enter your Merchant ID provided by Paytm">Merchant ID</span></label>
	<input class="form-control" name="merchant_id" value="<?= $setting_data['merchant_id'] ?>">
</div>

<div class="form-group">
	<label class="control-label"><span data-toggle="tooltip" title="" data-original-title="Enter your Merchant Key provided by Paytm">Merchant Key</span></label>
	<input class="form-control" name="merchant_key" value="<?= $setting_data['merchant_key'] ?>">
</div>

<div class="form-group">
	<label class="control-label"><span data-toggle="tooltip" title="" data-original-title="Enter your Website Name provded by Paytm" aria-describedby="tooltip962321">Website Name</span></label>
	<input class="form-control" name="website_name" value="<?= $setting_data['website_name'] ?>">
</div>

<div class="form-group">
	<label class="control-label"><span data-toggle="tooltip" title="" data-original-title="Eg. Retail, Entertainment etc.">Industry Type</span></label>
	<input class="form-control" name="industry_type" value="<?= $setting_data['industry_type'] ?>">
</div>

<div class="form-group">
	<label class="control-label"><span data-toggle="tooltip" title="" data-original-title="Enter Transaction URL provided by Paytm">Transaction URL</span></label>
	<input class="form-control" name="transaction_url" value="<?= $setting_data['transaction_url'] ?>">
</div>

<div class="form-group">
	<label class="control-label"><span data-toggle="tooltip" title="" data-original-title="Enter Transaction Status URL provided by Paytm" aria-describedby="tooltip789753">Transaction Status URL</span></label>
	<input class="form-control" name="transaction_status_url" value="<?= $setting_data['transaction_status_url'] ?>">
</div>



<div class="form-group">
	<label class="control-label"><span data-toggle="tooltip" title="" data-original-title="Order status that will set for Successful Payment">Order Success Status</span></label>
	<select name="order_success_status_id" class="form-control">
		<?php foreach ($order_status as $order_status_id => $name){ 
				if(isset($setting_data['order_success_status_id']))
					$selected = ($order_status_id == $setting_data['order_success_status_id']) ? 'selected' : '';
				else 
					$selected = ($order_status_id == 1) ? 'selected' : ''; ?>
				
				<option <?= $selected ?>  value="<?= $order_status_id; ?>"><?= $name ?></option>
		<?php } ?>
	</select>
</div>

<div class="form-group">
	<label class="control-label"><span data-toggle="tooltip" title="" data-original-title="Order status that will set for Failed Payment" aria-describedby="tooltip372536">Order Failed Status</span></label>
	<select name="order_failed_status_id" class="form-control">
		<?php foreach ($order_status as $order_status_id => $name){ 
				if(isset($setting_data['order_failed_status_id']))
					$selected = ($order_status_id == $setting_data['order_failed_status_id']) ? 'selected' : '';
				else 
					$selected = ($order_status_id == 5) ? 'selected' : ''; ?>
				
				<option <?= $selected ?>  value="<?= $order_status_id; ?>"><?= $name ?></option>
		<?php } ?>
	</select>
</div>