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
            <p class="mt-6 mb-6">
                <?= __('front.enter_your_information_to_setup_a_new_account') ?>
            </p>
            <?= $register_fomm ?>
            <p class="mb-0 subtitle d-block text-center mt-3"><?= __('front.already_have_an_account') ?> 
            <a href="<?= base_url() ?>" class="toggle-sign-in"><?= __('front.login') ?></a></p>
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