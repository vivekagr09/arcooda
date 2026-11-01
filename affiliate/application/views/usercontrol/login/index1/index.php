<?php
include_once "header.php";
?>

<div class="container mt-5 mt-md-7 mt-lg-9">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-lg p-4">
        <ul class="nav nav-pills justify-content-center mb-4" id="pills-tab" role="tablist">
          <li class="nav-item">
            <a class="nav-link active" href="<?= base_url() ?>">
              <?= __('front.login') ?>
            </a>
          </li>
          <?php 
            if(isset($store['registration_status']) &&  $store['registration_status'] != 0) {
              if(($vendor_marketstatus["marketvendorstatus"] == 1 || $vendor_storestatus['storestatus']) && $store['registration_status'] != 3) { ?>
                <li class="nav-item">
                  <a class="nav-link" href="<?= base_url('register') ?>">
                    <?= __('front.register') ?>
                  </a>
                </li>
              <?php } 
            } else if($store['registration_status'] != 2) { ?>
              <li class="nav-item">
                <a class="nav-link" href="<?= base_url('register') ?>">
                  <?= __('front.register') ?>
                </a>
              </li>
            <?php } ?>
        </ul>

        <form id="login-form">
          <div class="mb-3 position-relative form-group">
              <div class="input-group">
                  <span class="input-group-text">
                      <i class="bi bi-person-fill"></i>
                  </span>
                  <input required class="form-control" type="text" name="username" placeholder="<?= __('front.username_email') ?>">
              </div>
          </div>

          <div class="mb-3 position-relative form-group">
              <div class="input-group">
                  <span class="input-group-text">
                      <i class="bi bi-lock-fill"></i>
                  </span>
                  <input required class="form-control" type="password" name="password" placeholder="*************">
                  <button class="btn btn-outline-secondary toggle-password position-absolute top-50 end-0 translate-middle-y" type="button" aria-label="Toggle Password Visibility">
                      <i class="bi bi-eye-fill"></i>
                  </button>
              </div>
          </div>

          <?php if (isset($googlerecaptcha['affiliate_login']) && $googlerecaptcha['affiliate_login']) { ?>
            <div class="mb-3">
              <script src='https://www.google.com/recaptcha/api.js'></script>
                            <div class="g-recaptcha" data-sitekey="<?= $googlerecaptcha['sitekey'] ?>"></div>
            </div>
          <?php } ?>

          <div class="row mb-3">
            <div class="col-12">
              <button class="btn btn-primary btn-submit w-100 py-2"><?= __('front.login') ?></button>
            </div>
          </div>

          <div class="row mb-3">
              <div class="col-12 d-flex justify-content-end">
                <a href="<?= base_url('forget-password') ?>" class="text-muted"><?= __('front.forget_password') ?>?</a>
              </div>
          </div>

          <div class="row my-5">
            <div class="col-12 text-center">
              <h4 class="text-muted"><?= $title ?></h4>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<?php
include_once "footer.php";
?>

