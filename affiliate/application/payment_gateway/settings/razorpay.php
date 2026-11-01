<!-- Store Payment Status -->
<div class="mb-3">
    <label class="form-label"><?= __('admin.store_payment_status') ?></label>
    <select class="form-control" name="store">
        <option <?= (int)$setting_data['store']['status'] === 0 ? 'selected' : '' ?> value="0"><?= __('admin.disabled') ?></option>
        <option <?= (int)$setting_data['store']['status'] === 1 ? 'selected' : '' ?> value="1"><?= __('admin.enabled') ?></option>
    </select>
</div>

<!-- Deposit Payment Status -->
<div class="mb-3">
    <label class="form-label"><?= __('admin.deposit_payment_status') ?></label>
    <select class="form-control" name="deposit">
        <option <?= (int)$setting_data['deposit']['status'] === 0 ? 'selected' : '' ?> value="0"><?= __('admin.disabled') ?></option>
        <option <?= (int)$setting_data['deposit']['status'] === 1 ? 'selected' : '' ?> value="1"><?= __('admin.enabled') ?></option>
    </select>
</div>

<!-- Membership Payment Status -->
<div class="mb-3">
    <label class="form-label"><?= __('admin.membership_payment_status') ?></label>
    <select class="form-control" name="membership">
        <option <?= (int)$setting_data['membership']['status'] === 0 ? 'selected' : '' ?> value="0"><?= __('admin.disabled') ?></option>
        <option <?= (int)$setting_data['membership']['status'] === 1 ? 'selected' : '' ?> value="1"><?= __('admin.enabled') ?></option>
    </select>
</div>

<!-- Environment -->
<div class="mb-3">
    <label class="form-label">Environment</label>
    <select class="form-control" name="environment">
        <option <?= (int)$setting_data['environment'] === 0 ? 'selected' : '' ?> value="0">Test</option>
        <option <?= (int)$setting_data['environment'] === 1 ? 'selected' : '' ?> value="1">Live</option>
    </select>
</div>

<!-- Test Key ID -->
<div class="mb-3">
    <label class="form-label">Test Key ID</label>
    <input type="text" class="form-control" name="test_key_id" value="<?= $setting_data['test_key_id'] ?>">
</div>

<!-- Test Key Secret -->
<div class="mb-3">
    <label class="form-label">Test Key Secret</label>
    <input type="text" class="form-control" name="test_key_secret" value="<?= $setting_data['test_key_secret'] ?>">
</div>

<!-- Live Key ID -->
<div class="mb-3">
    <label class="form-label">Live Key ID</label>
    <input type="text" class="form-control" name="live_key_id" value="<?= $setting_data['live_key_id'] ?>">
</div>

<!-- Live Key Secret -->
<div class="mb-3">
    <label class="form-label">Live Key Secret</label>
    <input type="text" class="form-control" name="live_key_secret" value="<?= $setting_data['live_key_secret'] ?>">
</div>

<!-- Order Success Status -->
<div class="mb-3">
    <label class="form-label">Order Success Status</label>
    <select class="form-control" name="order_success_status">
        <?php foreach ($order_status as $order_status_id => $name): 
            $selected = isset($setting_data['order_success_status']) && $order_status_id == $setting_data['order_success_status'] ? 'selected' : '';
            $selected = $order_status_id === 1 && !isset($setting_data['order_success_status']) ? 'selected' : $selected; ?>
            <option <?= $selected ?> value="<?= $order_status_id ?>"><?= $name ?></option>
        <?php endforeach; ?>
    </select>
</div>

<!-- Order Failed Status -->
<div class="mb-3">
    <label class="form-label">Order Failed Status</label>
    <select class="form-control" name="order_failed_status">
        <?php foreach ($order_status as $order_status_id => $name): 
            $selected = isset($setting_data['order_failed_status']) && $order_status_id == $setting_data['order_failed_status'] ? 'selected' : '';
            $selected = $order_status_id === 5 && !isset($setting_data['order_failed_status']) ? 'selected' : $selected; ?>
            <option <?= $selected ?> value="<?= $order_status_id ?>"><?= $name ?></option>
        <?php endforeach; ?>
    </select>
</div>