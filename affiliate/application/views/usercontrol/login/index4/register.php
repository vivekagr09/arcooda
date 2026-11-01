<?php
include_once "header.php";
?>
<div class="forny-form">
	<div>
        <ul class="nav nav-tabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link bg-transparent" href="<?= base_url() ?>">
                    <span><?= __('front.login') ?></span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link active bg-transparent" href="<?= base_url('register') ?>">
                    <span><?= __('front.register') ?></span>
                </a>
            </li>
        </ul>
        <p class="mt-6 mb-6">
            <?= __('front.enter_your_information_to_setup_a_new_account') ?>
        </p>
        <?= $register_fomm ?>
    </div>
</div>
</div>
<div class="right-pane">
<div class="text-center">
    <div class="forny-logo">
        <a href="<?=base_url();?>">
            <img src="<?= base_url($logo) ?>" <?= ($SiteSetting['front_custom_logo_size']) ? 'class="customLogoClass"' : '' ?> alt="<?= __('front.logo') ?>">
        </a>
    </div>
    <br>
    <div class="mt-8">
        <h4 class="mb-4"><?= $title ?></h4>
    <br>
    </div>
</div>
</div>
<?php
include_once "footer.php";
?>