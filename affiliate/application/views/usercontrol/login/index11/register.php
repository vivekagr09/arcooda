<?php
include_once "header.php";
?>
<div class="container main-con-2">
    <div class="row">
        <div class="col-xl-6">
            <img src="<?=base_url()?>assets/login/index11/images/register.jpg" draggable="false" class="img-fluid d-none d-lg-none d-xl-block border-radius-10"/>
        </div>
        <div class="col-xl-6 text-center">
            <h3 class="text-center"><?= __('front.register') ?></h3>
            <p class="mt-6 mb-6">
                <?= __('front.enter_your_information_to_setup_a_new_account') ?>
            </p>
            <?= $register_fomm ?>
            <div class="separator"><?= __('front.already_have_an_account') ?></div>
            <a href="<?=base_url();?>" class="btn btn-outline-primary btn-block">
                <?= __('front.login') ?>
            </a>
        </div>
    </div>
</div>
<?php
include_once "footer.php";
?>