<?php
include_once "header.php";
?>
<div class="forny-form col-lg-6">
	<div class="login-reg-form">
	    <div>
	         <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active bg-transparent" href="<?= base_url() ?>">
                        <span><?= __('user.login') ?></span>
                    </a>
                </li>
                <?php if(isset($store['registration_status']) &&  $store['registration_status']==0) {} 
    else if( ($vendor_marketstatus["marketvendorstatus"]==1 || $vendor_storestatus['storestatus']) && $store['registration_status']!=3 ) 
     { ?>
            <li class="nav-item">
                <a class="nav-link bg-transparent" href="<?= base_url('register') ?>">
                    <span><?= __('user.register') ?></span>
                </a>
            </li>
                  <?php }else if($store['registration_status']!=2){ ?>
                    <li class="nav-item">
                        <a class="nav-link bg-transparent" href="<?= base_url('register') ?>">
                            <span><?= __('user.register') ?></span>
                        </a>
                    </li>
                <?php }  ?>
            </ul>
            <p class="subtitle"><?= __('user.Use_your_credentials_to_login_into_account') ?></p>
            <form id="login-form">
                <div class="form-group">
                    <input required  class="form-control" name="username" placeholder="<?= __('user.username_email') ?>">
                </div>
                <div class="form-group">
                    <input required  class="form-control" type="password" name="password" id="loginpassowrd" value="" placeholder="<?= __('front.password') ?>">
                </div>
                 <div class="d-block text-right my-3">
                    <a href="<?= base_url('forget-password') ?>"><?= __('user.forget_password') ?>?</a>
                </div>
                <div>
                    <?php if (isset($googlerecaptcha['affiliate_login']) && $googlerecaptcha['affiliate_login']) { ?>
                        <div class="captch mb-3">
                            <script src='https://www.google.com/recaptcha/api.js'></script>
                            <div class="g-recaptcha" data-sitekey="<?= $googlerecaptcha['sitekey'] ?>"></div>
                        </div>
                    <?php } ?>
                </div> 
                <button class="btn btn-primary btn-submit btn-block"><?= __('user.login') ?></button>
            </form>
	    </div>
    </div>
</div>
<?php
include_once "footer.php";
?>