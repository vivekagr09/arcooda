<?php
include_once "header.php";
?>
<div class="forny-form col-lg-6">
	<div class="login-reg-form">
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
            <p class="subtitle"><?= __('front.enter_your_information_to_setup_a_new_account') ?></p>
            <?= $register_fomm ?>
	    </div>
    </div>
</div>
<?php
include_once "footer.php";
?>