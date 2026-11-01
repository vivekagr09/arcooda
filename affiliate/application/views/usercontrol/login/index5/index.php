<?php
include_once "header.php";
?>
<div class="row">
    <div class="forny-form col-lg-6">
    	<div class="login-reg-form">
            <div class="forny-logo">
                <a href="<?= base_url() ?>">
                    <img src="<?= base_url($logo) ?>" <?= ($SiteSetting['front_custom_logo_size']) ? 'class="customLogoClass"' : '' ?> alt="<?= __('front.logo') ?>"></a>
            </div>
                <p class="subtitle"><?= __('front.Use_your_credentials_to_login_into_account') ?>   
                </p>
            <form id="login-form">
                <input required  class="form-control" name="username" placeholder="<?= __('front.username_email') ?>">
                <input required  class="form-control" type="password" name="password" id="loginpassowrd" value="" placeholder="<?= __('front.password') ?>">
                 <div class="d-block text-right my-3">
                    <a  href="<?= base_url('forget-password') ?>"><?= __('front.forget_password') ?>?</a>
                </div>
                <div>
                    <?php if (isset($googlerecaptcha['affiliate_login']) && $googlerecaptcha['affiliate_login']) { ?>
                        <div class="captch mb-3">
                            <script src='https://www.google.com/recaptcha/api.js'></script>
                            <div class="g-recaptcha" data-sitekey="<?= $googlerecaptcha['sitekey'] ?>"></div>
                        </div>
                    <?php } ?>
                </div> 
                <button class="btn btn-primary btn-submit btn-block"><?= __('front.login') ?></button>
            </form>
            <?php if(isset($store['registration_status']) &&  $store['registration_status']==0) {} 
            else if( ($vendor_marketstatus["marketvendorstatus"]==1 || $vendor_storestatus['storestatus']) && $store['registration_status']!=3 ) 
             { ?>
                <p class="mb-0 subtitle d-block text-center mt-3">
                    <?= __('front.dont_have_an_account_yet') ?>
                    <a href="<?= base_url('register') ?>" class="toggle-sign-up"> 
                        <?= __('front.sign_up') ?>
                    </a>
                </p>
                <?php }else if($store['registration_status']!=2){ ?>

                    <p class="mb-0 subtitle d-block text-center mt-3">
                    <?= __('front.dont_have_an_account_yet') ?>
                    <a href="<?= base_url('register') ?>" class="toggle-sign-up"> 
                        <?= __('front.sign_up') ?>
                    </a>
                </p>

            <?php } 
            ?>
        </div>
    </div>
    <div class="affiliate-description col-lg-6 pr-lg-auto pr-0">
        <div class="w-100 affiliate-description-content">
            <br>
            <h3><?= $setting['heading'] ?></h3>
            <br>
            <?= $setting['content'] ?>
            <br>
        </div>
    </div>
</div>
<?php
include_once "footer.php";
?>