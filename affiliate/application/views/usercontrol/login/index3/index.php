<?php
include_once "header.php";
?>
    <div class="row align-items-center">
        <div class="col-12 col-lg-5">
            <form method="POST" action="" id="login-form" class="p-3">
                <div class="forny-logo text-center mb-3">
                    <a href="<?=base_url();?>">
                        <img src="<?= base_url($logo) ?>" <?= ($SiteSetting['front_custom_logo_size']) ? 'class="customLogoClass"' : '' ?> alt="<?= __('front.logo') ?>">
                    </a>
                </div>
                <div class="row">
                    <div class="col-12 text-center"><?= $title ?></div>
                </div>
                <div class="mb-3 form-group">
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-person-fill"></i>
                        </span>
                        <input type="text" class="form-control" name="username" placeholder="<?= __('front.username_email') ?>">
                    </div>
                </div>
                <div class="mb-3 form-group">
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-lock-fill"></i>
                        </span>
                        <input type="password" class="form-control" name="password" id="loginpassword" placeholder="*************">
                    </div>
                </div>
                <div>
                    <?php if (isset($googlerecaptcha['affiliate_login']) && $googlerecaptcha['affiliate_login']) { ?>
                        <div class="mb-3">
                            <script src='https://www.google.com/recaptcha/api.js'></script>
                            <div class="g-recaptcha" data-sitekey="<?= $googlerecaptcha['sitekey'] ?>"></div>
                        </div>
                    <?php } ?>
                </div>
                <button class="btn btn-primary"><?= __('front.login') ?></button>
                <p class="text-center mt-3">
                    <a href="<?= base_url('forget-password') ?>">
                        <?= __('front.forget_password') ?>?
                    </a>
                </p>
                <?php if(isset($store['registration_status']) &&  $store['registration_status']==0) {} 
                else if( ($vendor_marketstatus["marketvendorstatus"]==1 || $vendor_storestatus['storestatus']) && $store['registration_status']!=3 ) 
                    { ?> 
                    <p class="text-center mt-3"><?= __('front.dont_have_an_account') ?>
                        <a href="<?= base_url('register') ?>"><?= __('front.register') ?></a> 
                    </p>
                    <?php }else if($store['registration_status']!=2){ ?>
                        <p class="text-center mt-3"><?= __('front.dont_have_an_account') ?>
                        <a href="<?= base_url('register') ?>"><?= __('front.register') ?></a> 
                    </p>
                <?php } ?>
            </form>
        </div>

        <div class="col-12 col-lg-7">
            <img src="<?= base_url('assets/login/index3') ?>/image/group.png" class="img-fluid w-100 h-100 p-3" alt="<?=__('front.image') ?>">
        </div>

        <div class="affiliate-description p-3">
                <!-- Your description here -->
            </div>
        </div>
    </div>
</div>

<?php
include_once "footer.php";
?>

