<?php
include_once "header.php";
?>
<div class="container-fluid main-con">
    <div class="row">
        <!-- Left Column -->
        <div class="col-xl-6 d-none d-xl-block">
            <img src="<?=base_url()?>assets/login/index11/images/login-side-image.jpg" draggable="false" class="img-fluid" alt="Login side image"/>
        </div>
        <!-- Right Column -->
        <div class="col-xl-6 d-flex flex-column justify-content-center text-center pad-40">
            <h4 class="fw-bold"><?= __('front.have_an_account') ?></h4>
            <h3 class="text-primary"><?= __('front.login_now') ?></h3>
            <div class="col-12 pad-30">
                <form id="login-form" class="text-start needs-validation" novalidate>
                    <div class="mb-3 form-group">
                        <input type="text" class="form-control" name="username" placeholder="<?= __('front.username_email') ?>" required>
                    </div>
                    <div class="mb-3 form-group pad-bot-20">
                        <input type="password" class="form-control" name="password" placeholder="<?= __('front.password') ?>" required>
                        <a href="forgot-password" class="d-block text-end pad-10"><small><?= __('front.forget_password') ?>?</small></a>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block fw-bold"><?= __('front.login') ?></button>
                </form>
                <div class="text-center my-3"><?= __('front.or') ?></div>
                <a href="register" class="btn btn-outline-primary btn-block"><?= __('front.create_account') ?></a>
            </div>
        </div>
    </div>
</div>
<?php
include_once "footer.php";
?>