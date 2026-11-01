<div class="mb-3">
	<label class="form-label"><?= __('admin.store_payment_status') ?></label>
	<select class="form-select" name="store">
		<option <?= (int)$setting_data['store']['status'] === 0 ? 'selected' : '' ?> value="0"><?= __('admin.disabled') ?></option>
		<option <?= (int)$setting_data['store']['status'] === 1 ? 'selected' : '' ?> value="1"><?= __('admin.enabled') ?></option>
	</select>
</div>

<div class="mb-3">
	<label class="form-label"><?= __('admin.deposit_payment_status') ?></label>
	<select class="form-select" name="deposit">
		<option <?= (int)$setting_data['deposit']['status'] === 0 ? 'selected' : '' ?> value="0"><?= __('admin.disabled') ?></option>
		<option <?= (int)$setting_data['deposit']['status'] === 1 ? 'selected' : '' ?> value="1"><?= __('admin.enabled') ?></option>
	</select>
</div>

<div class="mb-3">
	<label class="form-label"><?= __('admin.membership_payment_status') ?></label>
	<select class="form-select" name="membership">
		<option <?= (int)$setting_data['membership']['status'] === 0 ? 'selected' : '' ?> value="0"><?= __('admin.disabled') ?></option>
		<option <?= (int)$setting_data['membership']['status'] === 1 ? 'selected' : '' ?> value="1"><?= __('admin.enabled') ?></option>
	</select>
</div>

<div class="mb-3">
	<label class="form-label" data-bs-toggle="tooltip" title="Enter your Shop ID provided by Yandex">Shop ID</label>
	<input class="form-control" name="shop_id" value="<?= $setting_data['shop_id'] ?>">
</div>

<div class="mb-3">
	<label class="form-label" data-bs-toggle="tooltip" title="Enter your Secret Key provided by Yandex">Secret Key</label>
	<input class="form-control" name="secret_key" value="<?= $setting_data['secret_key'] ?>">
</div>

<div class="form-group">
	<label class="control-label">Order Status</label>
	<select name="order_status" class="form-control">
		<?php foreach ($order_status as $order_status_id => $name){ 
				if(isset($setting_data['order_status']))
					$selected = ($order_status_id == $setting_data['order_status']) ? 'selected' : '';
				else 
					$selected = ($order_status_id == 1) ? 'selected' : ''; ?>
				
				<option <?= $selected ?>  value="<?= $order_status_id; ?>"><?= $name ?></option>
		<?php } ?>
	</select>
</div>