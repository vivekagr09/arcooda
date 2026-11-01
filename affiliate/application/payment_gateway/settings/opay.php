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
	<label class="control-label"><span data-toggle="tooltip" title="" data-original-title="Enter your HashKey">HashKey</span></label>
	<input class="form-control" name="HashKey" value="<?= $setting_data['HashKey'] ?>">
</div>

<div class="form-group">
	<label class="control-label"><span data-toggle="tooltip" title="" data-original-title="Enter your HashIV">HashIV</span></label>
	<input class="form-control" name="HashIV" value="<?= $setting_data['HashIV'] ?>">
</div>

<div class="form-group">
	<label class="control-label"><span data-toggle="tooltip" title="" data-original-title="Enter your MerchantID">MerchantID</span></label>
	<input class="form-control" name="MerchantID" value="<?= $setting_data['MerchantID'] ?>">
</div>


<div class="form-group">
	<label class="control-label">Order Status</label>
	<select name="order_status" class="form-control">
		<?php foreach ($order_status as $order_status_id => $name) { ?>
			<?php if ($order_status_id == $setting_data['order_status']) { ?>
				<option value="<?php echo $order_status_id; ?>" selected="selected"><?= $name ?></option>
			<?php } else { ?>
				<option value="<?php echo $order_status_id; ?>"><?= $name ?></option>
			<?php } ?>
		<?php } ?>
	</select>
</div>

<div class="form-group">
	<label class="control-label">Failed Status</label>
	<select name="failed_status" class="form-control">
		<?php foreach ($order_status as $order_status_id => $name) { ?>
			<?php if ($order_status_id == $setting_data['failed_status']) { ?>
				<option value="<?php echo $order_status_id; ?>" selected="selected"><?= $name ?></option>
			<?php } else { ?>
				<option value="<?php echo $order_status_id; ?>"><?= $name ?></option>
			<?php } ?>
		<?php } ?>
	</select>
</div>