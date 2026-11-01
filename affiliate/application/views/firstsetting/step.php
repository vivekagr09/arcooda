<form id="stepwizard-form">
	<div class="stepwizard-data">
<?php if($number == 1) { ?>
    <div class="stepwizard-title"><?= __('admin.general_settings') ?></div>

    <!-- Local or External Store -->
    <div class="form-group mb-3">
        <label><?= __('admin.local_or_external') ?></label>
        <select class="form-select" name="store[status]">
            <option value="1" <?= (int)$store['status'] == 1 ? 'selected' : '' ?> ><?= __('admin.enable') ?></option>
            <option value="0" <?= (int)$store['status'] == 0 ? 'selected' : '' ?> ><?= __('admin.disable') ?></option>
        </select>
    </div>

    <!-- Time Zone -->
    <?php
        $zones_array = array();
        $timestamp = time();
        foreach(timezone_identifiers_list() as $key => $zone) {
            date_default_timezone_set($zone);
            $zones_array[$zone] = date('P', $timestamp) . " {$zone} ";
        }
    ?>
    <div class="form-group mb-3">
        <label><?= __('admin.time_zone') ?></label>
        <select class="form-select" name="site[time_zone]">
            <?php foreach ($zones_array as $key => $value) { ?>
                <option value="<?= $key ?>" <?= $site['time_zone'] == $key ? 'selected' : '' ?> ><?= $value ?></option>
            <?php } ?>
        </select>
    </div>

    <!-- Registration form -->
    <div class="form-group mb-3">
        <label><?= __('admin.registration_form') ?></label>
        <select class="form-select" name="store[registration_status]">
            <option value="1" ><?= __('admin.enable') ?></option>
            <option value="0" <?= (int)$store['registration_status'] == 0 ? 'selected' : '' ?> ><?= __('admin.disable') ?></option>
        </select>
    </div>

    <!-- Front Template -->
    <div class="form-group mb-3">
        <label><?= __('admin.front_template') ?></label>
        <select class="form-select" name="login[front_template]">
            <?php foreach ($themes as $key => $value) { ?>
                <option value="<?= $value['id'] ?>" <?= $login['front_template'] == $value['id'] ? 'selected' : '' ?> ><?= $value['name'] ?></option>
            <?php } ?>
        </select>
    </div>

    <!-- Default Action Status -->
    <div class="form-group mb-3">
        <label><?= __('admin.default_action_status') ?></label>
        <select class="form-select" name="referlevel[default_action_status]">
            <option value="0" <?= (int)$referlevel['default_action_status'] == 0 ? 'selected' : '' ?>><?= __('admin.on_hold') ?></option>
            <option value="1" <?= (int)$referlevel['default_action_status'] == 1 ? 'selected' : '' ?>><?= __('admin.in_wallet') ?></option>
        </select>
    </div>

    <!-- Affiliate Cookie -->
    <div class="form-group mb-3">
        <label><?= __('admin.affiliate_cookie') ?></label>
        <input class="form-control" type="number" value="<?= $store['affiliate_cookie'] ?>" name="store[affiliate_cookie]">
    </div>

    <!-- Refer Level Status -->
    <div class="form-group mb-3">
        <label><?= __('admin.refer_level_status') ?></label>
        <select class="form-select" name="referlevel[status]">
            <option value="1" <?= (int)$referlevel['status'] == 1 ? 'selected' : '' ?>><?= __('admin.enable') ?></option>
            <option value="0" <?= (int)$referlevel['status'] == 0 ? 'selected' : '' ?>><?= __('admin.disable') ?></option>
        </select>
    </div>

    <!-- Minimum Withdraw -->
    <div class="form-group mb-3">
        <label><?= __('admin.minimum_withdraw') ?></label>
        <input class="form-control" type="number" value="<?= $site['wallet_min_amount'] ?>" name="site[wallet_min_amount]">
    </div>

<?php } ?>


<?php if($number == 2) { ?>
    <div class="stepwizard-title"><?= __('admin.admin_email_profile_title') ?></div>

    <div class="form-group">
        <label class="col-form-label"><?= __('admin.profile_email_label') ?></label>
        <input name="profile_email" value="<?= $profile_email; ?>" class="form-control" type="text">
    </div>
<?php } ?>


<?php if($number == 3) { ?>
    <div class="stepwizard-title"><?= __('admin.email_settings_title') ?></div>

    <div class="form-group">
        <label class="col-form-label"><?= __('admin.from_email_label') ?></label>
        <input name="email[from_email]" value="<?= $setting['from_email']; ?>" class="form-control" type="text">
    </div>

    <div class="form-group">
        <label class="col-form-label"><?= __('admin.from_name_label') ?></label>
        <input name="email[from_name]" value="<?= $setting['from_name']; ?>" class="form-control" type="text">
    </div>

    <div class="form-group">
        <label class="col-form-label"><?= __('admin.smtp_hostname_label') ?></label>
        <input name="email[smtp_hostname]" value="<?= $setting['smtp_hostname']; ?>" class="form-control" type="text">
    </div>

    <div class="form-group">
        <label class="col-form-label"><?= __('admin.smtp_username_label') ?></label>
        <input name="email[smtp_username]" value="<?= $setting['smtp_username']; ?>" class="form-control" type="text">
    </div>

    <div class="form-group">
        <label class="col-form-label"><?= __('admin.smtp_password_label') ?></label>
        <input name="email[smtp_password]" value="<?= $setting['smtp_password']; ?>" class="form-control" type="text">
    </div>

    <div class="form-group">
        <label class="col-form-label"><?= __('admin.smtp_port_label') ?></label>
        <input name="email[smtp_port]" value="<?= $setting['smtp_port']; ?>" class="form-control" type="text">
    </div>

    <div class="form-group">
        <label class="col-form-label"><?= __('admin.notification_email_label') ?></label>
        <input name="site[notify_email]" value="<?= $site['notify_email']; ?>" class="form-control" type="email">
    </div>
<?php } ?>



<?php if($number == 4) { ?>
    <div class="stepwizard-title"><?= __('admin.default_currency_language_title') ?></div>

    <div class="form-group">
        <label class="col-form-label"><?= __('admin.select_default_currency_label') ?></label>
        <select class="form-control" name="currency">
            <option value=""><?= __('admin.select_option_placeholder') ?></option>
            <?php foreach ($currency as $key => $value) { ?>
                <option <?= $value['is_default'] ? 'selected' : '' ?> value="<?= $value['currency_id'] ?>"><?= $value['title'] ?></option>
            <?php } ?>
        </select>
    </div>

    <div class="form-group">
        <label class="col-form-label"><?= __('admin.select_default_language_label') ?></label>
        <select class="form-control" name="language">
            <option value=""><?= __('admin.select_option_placeholder') ?></option>
            <?php foreach ($language as $key => $value) { ?>
                <option <?= $value['is_default'] ? 'selected' : '' ?> value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
            <?php } ?>
        </select>
    </div>
<?php } ?>



<?php if($number == 5) { ?>
    <div class="stepwizard-title"><?= __('admin.change_password_title') ?></div>

    <div class="form-group">
        <label class="col-form-label"><?= __('admin.enter_new_password_label') ?></label>
        <input name="password" value="" class="form-control" type="password" placeholder="<?= __('admin.new_password_placeholder') ?>">
    </div>

    <div class="form-group">
        <label class="col-form-label"><?= __('admin.confirm_password_label') ?></label>
        <input name="c_password" value="" class="form-control" type="password" placeholder="<?= __('admin.confirm_password_placeholder') ?>">
    </div>
<?php } ?>


<?php if($number == 6) { ?>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-10 offset-md-1">
                <h2 class="display-4 text-center mb-1" style="font-weight: bold;">Thank You!</h2>
                <p class="text-center mb-5" style="font-size: 24px; color: gray; white-space: nowrap;"><?= __('admin.final_step_instruction_message') ?></p>

                <div class="card">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <span><strong>1. <?= __('admin.choose_system_mode') ?></strong></span>
                                <a href="<?= base_url('admincontrol/dashboard') ?>" class="btn btn-outline-primary btn-sm"><?= __('admin.s_mode_tab') ?></a>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <span><strong>2. <?= __('admin.add_first_cart_product') ?></strong></span>
                                <div class="btn-group" role="group">
                                    <a href="<?= base_url('admincontrol/listproduct') ?>" class="btn btn-outline-secondary btn-sm"><?= __('admin.local_store_tab') ?></a>
                                    <a href="<?= base_url('Productsales/index') ?>" class="btn btn-outline-secondary btn-sm"><?= __('admin.sales_product_tab') ?></a>
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <span><strong>3. <?= __('admin.add_first_affiliate_program') ?></strong></span>
                                <div class="btn-group" role="group">
                                    <a href="<?= base_url('integration/programs') ?>" class="btn btn-outline-info btn-sm"><?= __('admin.external_mode_tab') ?></a>
                                    <a href="<?= base_url('integration/integration_tools') ?>" class="btn btn-outline-info btn-sm"><?= __('admin.banner_integration_tab') ?></a>
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <span><strong>4. <?= __('admin.setup_mlm_with_affiliate_program') ?></strong></span>
                                <div class="btn-group" role="group">
                                    <a href="<?= base_url('admincontrol/mlm_settings') ?>" class="btn btn-outline-dark btn-sm"><?= __('admin.mlm_settings_tab') ?></a>
                                    <a href="<?= base_url('admincontrol/mlm_levels') ?>" class="btn btn-outline-dark btn-sm"><?= __('admin.mlm_levels_tab') ?></a>
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <span><strong>5. <?= __('admin.to_set_all_system_modules') ?></strong></span>
                                <div class="btn-group" role="group">
                                    <a href="<?= base_url('admincontrol/addons') ?>" class="btn btn-outline-success btn-sm"><?= __('admin.set_modules') ?></a>
                                    <a href="<?= base_url('admincontrol/paymentsetting') ?>" class="btn btn-outline-success btn-sm"><?= __('admin.set_system_settings') ?></a>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
<?php } ?>





	</div>
</form>

<div class="stepwizard-footer">
	<?php if($number > 1){ ?>
		<button class="btn btn-primary mt-3" onclick="getStep(<?= $number-1 ?>)"><?= __('admin.prev') ?></button>
	<?php } ?>
	<?php if($number < $total_step){ ?>
		<button class="btn btn-primary mt-3 btn-next" onclick="getStep(<?= $number+1 ?>, <?= $number ?>)"><?= __('admin.next') ?></button>
	<?php } ?>
</div>