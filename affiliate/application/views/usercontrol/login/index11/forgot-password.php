<?php
include_once "header.php";
?>
<div class="container main-con">
    <div class="row">
        <div class="col-xl-6"><img src="<?=base_url()?>assets/login/index11/images/login-side-image.jpg" draggable="false"  class="img-fluid d-none d-lg-none d-xl-block" alt="Login side image"/></div>
        <div class="col-xl-6 text-center pad-40">
            <h4 class="font-weight-bold"><?= __('front.reset_password') ?></h4>
            <p><?= __('front.email_sent_instructions') ?></p>
            <div class="col-xl-12 pad-30">
                <form class="reset-password-form" class="text-left">
                    <div class="form-group">
                    <input type="email" class="form-control" name="email" placeholder="<?= __('front.email') ?>">
                    </div>
                    <button type="submit" class="btn btn-primary btn-block font-weight-bold btn-submit"><?= __('front.send_reset_link') ?></button>
                </form>
                <a href="<?=base_url();?>" class="btn btn-outline-primary btn-block second-btn"><?= __('front.back_to_login') ?></a>
            </div>
        </div>
    </div>
</div>
<?php
include_once "footer.php";
?>