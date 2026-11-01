<?php
include_once "header.php";
?>
<!--right side-->
<div class="wrap-login100">
    <?php  if($store['language_status']){  ?>
    <div class="lang-div">
        <?= $LanguageHtml ?>
    </div>
    <?php } ?>
    
    <form class="reset-password-form">
            <span class="login100-form-title">
                <?= __('front.forget_password') ?>
            </span>
            <div class="wrap-input100">
                <span class="label-input100"><?= __('front.registered_email') ?></span>
                <input required class="input100" type="text" name="email" placeholder="<?= __('front.email_address') ?>...">
                <span class="focus-input100"></span>
            </div>
            <div class="container-login100-form-btn">
                <div class="wrap-login100-form-btn">
                    <div class="login100-form-bgbtn"></div>
                    <button class="btn btn-primary btn-submit login100-form-btn btn_sendmail_bg">
                    <?= __('front.send_mail') ?>
                    </button>
                </div>
                <a href="<?= base_url() ?>" class="forget-link d-block txt3 hov1" data-type='login'>
                    <i class="fa fa-long-arrow-left ml-5"></i>
                    <?= __('front.back_to_login') ?>
                </a>
            </div>
        </form>
</div>
<!--right side-->
<?php
include_once "footer.php";
?>