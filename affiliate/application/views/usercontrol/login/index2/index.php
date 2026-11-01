<?php include_once "header.php"; ?>
<div class="login-reg-form">
    <ul class="nav nav-pills mb-3" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" href="<?= base_url() ?>">
                <?= __('front.login') ?>
            </a>
        </li>
        <?php if (isset($store['registration_status']) && $store['registration_status'] == 0) {} 
        else if (($vendor_marketstatus["marketvendorstatus"] == 1 || $vendor_storestatus['storestatus']) && $store['registration_status'] != 3) { ?>
            <li class="nav-item">
                <a class="nav-link" href="<?= base_url('register') ?>">
                    <?= __('front.register') ?>
                </a>
            </li>
        <?php } else if ($store['registration_status'] != 2) { ?>
            <li class="nav-item">
                <a class="nav-link" href="<?= base_url('register') ?>">
                    <?= __('front.register') ?>
                </a>
            </li>
        <?php } ?>
    </ul>
    <p class="mt-4 mb-4">
        <?= __('front.Use_your_credentials_to_login_into_account') ?>
    </p>
    <form id="login-form">

    <div class="mb-3 form-group">
        <div class="input-group">
            <span class="input-group-text">
                <i class="bi bi-person-fill"></i>
            </span>
            <input class="form-control" name="username" placeholder="<?= __('front.username_email') ?>">
        </div>
    </div>

    <div class="mb-3">
        <div class="input-group">
            <span class="input-group-text">
                <i class="bi bi-lock-fill"></i>
            </span>
            <input class="form-control" type="password" name="password" placeholder="*************">
            <button class="btn btn-outline-secondary toggle-password" type="button" data-bs-toggle="password" data-bs-target="input[name='password']">
                <i class="bi bi-eye-fill"></i>
            </button>
        </div>
    </div>

        <div>
            <?php if (isset($googlerecaptcha['affiliate_login']) && $googlerecaptcha['affiliate_login']) { ?>
                <div class="captch mb-3">
                    <script src='https://www.google.com/recaptcha/api.js'></script>
                    <div class="g-recaptcha" data-sitekey="<?= $googlerecaptcha['sitekey'] ?>"></div>
                </div>
            <?php } ?>
        </div>
        <div class="row mt-4">
            <div class="col-6">
                <button class="btn btn-primary btn-block"><?= __('front.login') ?></button>
            </div>
            <div class="col-6 d-flex align-items-center justify-content-end">
                <a href="<?= base_url('forget-password') ?>"><?= __('front.forget_password') ?>?</a>
            </div>
        </div>
    </form>
</div>
<?php include_once "footer.php"; ?>