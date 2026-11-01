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
<!--right side-->
<?php
include_once "footer.php";
?>