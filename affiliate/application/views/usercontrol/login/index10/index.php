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
    
    <form id="login-form">
      <div class="form-group">
            <span class="login100-form-title">
                <?= __('front.sign_in') ?>
            </span>
            <input type="hidden" name="type" value="user">

            <div class="wrap-input100">
                <span class="label-input100"><?= __('front.username') ?></span>
                <input required class="input100" type="text" name="username" placeholder="<?= __('front.username_email') ?>...">
                <span class="focus-input100"></span>
            </div>
        </div>

        <div class="form-group">
             <div class="wrap-input100">
                <span class="label-input100"><?= __('front.password') ?></span>
                <input required class="input100" type="password" name="password" placeholder="*************">
                <span class="focus-input100"></span>
            </div>
        </div>

        <?php if (isset($googlerecaptcha['affiliate_login']) && $googlerecaptcha['affiliate_login']) { ?>
        <div class="captch mb-3">
            <script src='https://www.google.com/recaptcha/api.js'></script>
            <div class="g-recaptcha" data-sitekey="<?= $googlerecaptcha['sitekey'] ?>"></div>
            <input type="hidden" name="captch_response" id="captch_response">
        </div>
        <?php } ?>
        <div class="login-error"></div>
        <div class="text-center">
            <a href="<?= base_url('forget-password') ?>" class="d-block txt3 hov1" data-type='forget'><?= __('front.forget_password') ?> ?</a>
            <br>
        </div>

        <div class="container-login100-form-btn">
        <div>
            <button class="btn btn-primary btn-lg btn-submit col-5 float-left">
            <?= __('front.sign_in') ?>
            </button>
             <?php if(isset($store['registration_status']) &&  $store['registration_status']==0) {} 
        else if( ($vendor_marketstatus["marketvendorstatus"]==1 || $vendor_storestatus['storestatus']) && $store['registration_status']!=3 ) 
            { ?>
                <a class="btn btn-primary btn-lg col-5 float-right" href="<?= base_url('register') ?>" data-type='register'>
                    <?= __('front.sign_up') ?>
                </a>
            <?php }else if($store['registration_status']!=2){ ?>
                <a class="btn btn-primary btn-lg col-5 float-right" href="<?= base_url('register') ?>" data-type='register'>
                    <?= __('front.sign_up') ?>
                </a>
            <?php } ?>
        </div>
    </div>
    </form>
</div>
<!--right side-->
<?php
include_once "footer.php";
?>





