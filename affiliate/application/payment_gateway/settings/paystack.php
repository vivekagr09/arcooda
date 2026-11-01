<!-- Store Payment Status -->
<div class="mb-3">
    <label for="store-status" class="form-label"><?= __('admin.store_payment_status') ?></label>
    <select class="form-select" name="store" id="store-status">
        <option <?= (int)$setting_data['store']['status'] == '0' ? 'selected' : '' ?> value="0"><?= __('admin.disabled') ?></option>
        <option <?= (int)$setting_data['store']['status'] == '1' ? 'selected' : '' ?> value="1"><?= __('admin.enabled') ?></option>
    </select>
</div>

<!-- Deposit Payment Status -->
<div class="mb-3">
    <label for="deposit-status" class="form-label"><?= __('admin.deposit_payment_status') ?></label>
    <select class="form-select" name="deposit" id="deposit-status">
        <option <?= (int)$setting_data['deposit']['status'] == '0' ? 'selected' : '' ?> value="0"><?= __('admin.disabled') ?></option>
        <option <?= (int)$setting_data['deposit']['status'] == '1' ? 'selected' : '' ?> value="1"><?= __('admin.enabled') ?></option>
    </select>
</div>

<!-- Membership Payment Status -->
<div class="mb-3">
    <label for="membership-status" class="form-label"><?= __('admin.membership_payment_status') ?></label>
    <select class="form-select" name="membership" id="membership-status">
        <option <?= (int)$setting_data['membership']['status'] == '0' ? 'selected' : '' ?> value="0"><?= __('admin.disabled') ?></option>
        <option <?= (int)$setting_data['membership']['status'] == '1' ? 'selected' : '' ?> value="1"><?= __('admin.enabled') ?></option>
    </select>
</div>

<!-- Paystack Public Key -->
<div class="mb-3">
    <label for="public-key" class="form-label" data-toggle="tooltip" title="" data-original-title="Enter your Paystack Public Key">Paystack Public Key</label>
    <input type="text" class="form-control" name="public_key" id="public-key" value="<?= $setting_data['public_key'] ?>">
</div>

<!-- Order Success Status -->
<div class="mb-3">
    <label for="order-success-status" class="form-label" data-toggle="tooltip" title="" data-original-title="Order status that will set for Successful Payment">Order Success Status</label>
    <select class="form-select" name="order_success_status_id" id="order-success-status">
        <?php foreach ($order_status as $order_status_id => $name): 
            $selected = isset($setting_data['order_success_status_id']) 
                ? ($order_status_id == $setting_data['order_success_status_id'] ? 'selected' : '') 
                : ($order_status_id == 1 ? 'selected' : ''); ?>
            <option <?= $selected ?> value="<?= $order_status_id; ?>"><?= $name ?></option>
        <?php endforeach; ?>
    </select>
</div>

<!-- Order Failed Status -->
<div class="mb-3">
    <label for="order-failed-status" class="form-label" data-toggle="tooltip" title="" data-original-title="Order status that will set for Failed Payment">Order Failed Status</label>
    <select class="form-select" name="order_failed_status_id" id="order-failed-status">
        <?php foreach ($order_status as $order_status_id => $name): 
            $selected = isset($setting_data['order_failed_status_id']) 
                ? ($order_status_id == $setting_data['order_failed_status_id'] ? 'selected' : '') 
                : ($order_status_id == 5 ? 'selected' : ''); ?>
            <option <?= $selected ?> value="<?= $order_status_id; ?>"><?= $name ?></option>
        <?php endforeach; ?>
    </select>
</div>