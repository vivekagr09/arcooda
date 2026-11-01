<!-- Store Payment Status -->
<div class="mb-3 form-group">
	<label for="store" class="form-label"><?= __('admin.store_payment_status') ?></label>
	<select class="form-select" id="store" name="store">
		<option <?= (int)$setting_data['store']['status'] === 0 ? 'selected' : '' ?> value="0"><?= __('admin.disabled') ?></option>
		<option <?= (int)$setting_data['store']['status'] === 1 ? 'selected' : '' ?> value="1"><?= __('admin.enabled') ?></option>
	</select>
</div>

<!-- Deposit Payment Status -->
<div class="mb-3 form-group">
	<label for="deposit" class="form-label"><?= __('admin.deposit_payment_status') ?></label>
	<select class="form-select" id="deposit" name="deposit">
		<option <?= (int)$setting_data['deposit']['status'] === 0 ? 'selected' : '' ?> value="0"><?= __('admin.disabled') ?></option>
		<option <?= (int)$setting_data['deposit']['status'] === 1 ? 'selected' : '' ?> value="1"><?= __('admin.enabled') ?></option>
	</select>
</div>

<!-- Membership Payment Status -->
<div class="mb-3 form-group">
	<label for="membership" class="form-label"><?= __('admin.membership_payment_status') ?></label>
	<select class="form-select" id="membership" name="membership">
		<option <?= (int)$setting_data['membership']['status'] === 0 ? 'selected' : '' ?> value="0"><?= __('admin.disabled') ?></option>
		<option <?= (int)$setting_data['membership']['status'] === 1 ? 'selected' : '' ?> value="1"><?= __('admin.enabled') ?></option>
	</select>
</div>

<!-- E-Mail -->
<div class="mb-3 form-group">
	<label for="email" class="form-label">E-Mail</label>
	<input type="email" class="form-control" id="email" name="email" value="<?= $setting_data['email'] ?>">
</div>

<!-- Secret -->
<div class="mb-3 form-group">
	<label for="secret" class="form-label">Secret</label>
	<input type="text" class="form-control" id="secret" name="secret" value="<?= $setting_data['secret'] ?>">
</div>

<!-- Order Status -->
<div class="mb-3 form-group">
	<label for="order_status" class="form-label">Order Status</label>
	<select class="form-select" id="order_status" name="order_status">
		<?php foreach ($order_status as $order_status_id => $name): 
			$selected = isset($setting_data['order_status']) ? ($order_status_id == $setting_data['order_status'] ? 'selected' : '') : ($order_status_id == 1 ? 'selected' : ''); ?>
			<option <?= $selected ?> value="<?= $order_status_id ?>"><?= $name ?></option>
		<?php endforeach; ?>
	</select>
</div>

<!-- Failed Status -->
<div class="mb-3 form-group">
	<label for="failed_status" class="form-label">Failed Status</label>
	<select class="form-select" id="failed_status" name="failed_status">
		<?php foreach ($order_status as $order_status_id => $name): 
			$selected = isset($setting_data['failed_status']) ? ($order_status_id == $setting_data['failed_status'] ? 'selected' : '') : ($order_status_id == 5 ? 'selected' : ''); ?>
			<option <?= $selected ?> value="<?= $order_status_id ?>"><?= $name ?></option>
		<?php endforeach; ?>
	</select>
</div>

<!-- Pending Status -->
<div class="mb-3 form-group">
	<label for="pending_status" class="form-label">Pending Status</label>
	<select class="form-select" id="pending_status" name="pending_status">
		<?php foreach ($order_status as $order_status_id => $name): 
			$selected = isset($setting_data['pending_status']) ? ($order_status_id == $setting_data['pending_status'] ? 'selected' : '') : ($order_status_id == 6 ? 'selected' : ''); ?>
			<option <?= $selected ?> value="<?= $order_status_id ?>"><?= $name ?></option>
		<?php endforeach; ?>
	</select>
</div>

<!-- Canceled Status -->
<div class="mb-3 form-group">
	<label for="canceled_status" class="form-label">Canceled Status</label>
	<select class="form-select" id="canceled_status" name="canceled_status">
		<?php foreach ($order_status as $order_status_id => $name): 
			$selected = isset($setting_data['canceled_status']) ? ($order_status_id == $setting_data['canceled_status'] ? 'selected' : '') : ($order_status_id == 11 ? 'selected' : ''); ?>
			<option <?= $selected ?> value="<?= $order_status_id ?>"><?= $name ?></option>
		<?php endforeach; ?>
	</select>
</div>

<!-- Chargeback Status -->
<div class="mb-3 form-group">
	<label for="chargeback_status" class="form-label">Chargeback Status</label>
	<select class="form-select" id="chargeback_status" name="chargeback_status">
		<?php foreach ($order_status as $order_status_id => $name): 
			$selected = isset($setting_data['chargeback_status']) ? ($order_status_id == $setting_data['chargeback_status'] ? 'selected' : '') : ($order_status_id == 7 ? 'selected' : ''); ?>
			<option <?= $selected ?> value="<?= $order_status_id ?>"><?= $name ?></option>
		<?php endforeach; ?>
	</select>
</div>