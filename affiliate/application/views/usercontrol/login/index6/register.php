<?php
include_once "header.php";
?>
<div class="forny-form col-lg-6">
	<div class="login-reg-form">
        <div>
            <h1 class="card-title"><?= __('front.sign_up') ?></h1>
            <p class="mt-6 mb-6">
                <?= __('front.enter_your_information_to_setup_a_new_account') ?>
            </p>
            <?= $register_fomm ?>
            <a href="<?= base_url() ?>" class="toggle-sign-in btn btn-light btn-block mt-2">
                <?= __('front.back_to_login') ?>
            </a>
       </div>
    </div>
</div>
<?php
include_once "footer.php";
?>