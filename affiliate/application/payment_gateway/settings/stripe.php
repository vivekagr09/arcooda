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
	<label class="form-label">Environment</label>
	<select class="form-select" name="environment">
		<option <?= (int)$setting_data['environment'] === 0 ? 'selected' : '' ?> value="0">Test</option>
		<option <?= (int)$setting_data['environment'] === 1 ? 'selected' : '' ?> value="1">Live</option>
	</select>
</div>

<div class="mb-3">
	<label class="form-label">Test Public Key</label>
	<input class="form-control" name="test_public_key" value="<?= $setting_data['test_public_key'] ?>">
</div>

<div class="mb-3">
	<label class="form-label">Test Secret Key</label>
	<input class="form-control" name="test_secret_key" value="<?= $setting_data['test_secret_key'] ?>">
</div>

<div class="mb-3">
	<label class="form-label">Live Public Key</label>
	<input class="form-control" name="live_public_key" value="<?= $setting_data['live_public_key'] ?>">
</div>

<div class="mb-3">
	<label class="form-label">Live Secret Key</label>
	<input class="form-control" name="live_secret_key" value="<?= $setting_data['live_secret_key'] ?>">
</div>

<div class="form-group">
	<label class="control-label">Order Success Status</label>
	<select name="order_success_status" class="form-control">
		<?php foreach ($order_status as $order_status_id => $name){ 
				if(isset($setting_data['order_success_status']))
					$selected = ($order_status_id == $setting_data['order_success_status']) ? 'selected' : '';
				else 
					$selected = ($order_status_id == 1) ? 'selected' : ''; ?>
				
				<option <?= $selected ?>  value="<?= $order_status_id; ?>"><?= $name ?></option>
		<?php } ?>
	</select>
</div>

<div class="form-group">
	<label class="control-label">Order Failed Status</label>
	<select name="order_failed_status" class="form-control">
		<?php foreach ($order_status as $order_status_id => $name){ 
				if(isset($setting_data['order_failed_status']))
					$selected = ($order_status_id == $setting_data['order_failed_status']) ? 'selected' : '';
				else 
					$selected = ($order_status_id == 5) ? 'selected' : ''; ?>
				
				<option <?= $selected ?>  value="<?= $order_status_id; ?>"><?= $name ?></option>
		<?php } ?>
	</select>
</div>