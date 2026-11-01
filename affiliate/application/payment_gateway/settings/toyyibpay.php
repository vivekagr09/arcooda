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
	<label class="form-label" data-bs-toggle="tooltip" title="Enter your userSecretKey">toyyibpay User Secret Key</label>
	<input class="form-control" name="toyyibpay_userSecretKey" value="<?= $setting_data['toyyibpay_userSecretKey'] ?>">
	<input type="hidden" class="form-control" name="toyyibpay_category_id" value="<?= $setting_data['toyyibpay_category_id'] ?>">
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
	<label class="control-label" for="input-pending-status">Order Pending Status</label>
	<div class="">
		<select name="pending_status_id" id="input-pending-status" class="form-control">
			<?php foreach ($order_status as $order_status_id => $name){ 
				if(isset($setting_data['pending_status_id']))
					$selected = ($order_status_id == $setting_data['pending_status_id']) ? 'selected' : '';
				else 
					$selected = ($order_status_id == 6) ? 'selected' : ''; ?>

				<option <?= $selected ?>  value="<?= $order_status_id; ?>"><?= $name ?></option>
			<?php } ?>
		</select>
	</div>
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