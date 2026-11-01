<?php
include_once "header.php";
?>
    <div class="row align-items-center">
        <div class="col-12 col-lg-5">
            <div class="outer" id="outer">
                <div class="boxx" id="fog-pass">
                    <div class="forny-logo mb-3 text-center">
                        <a href="<?=base_url();?>">
                            <img src="<?= base_url($logo) ?>" <?= ($SiteSetting['front_custom_logo_size']) ? 'class="customLogoClass"' : '' ?> alt="<?= __('front.logo') ?>">
                        </a>
                    </div>
                    <form method="POST" action="" class="reset-password-form p-3">
                        <div class="mb-3">
                            <input class="form-control" name="email" placeholder="<?= __('front.email') ?>" type="email">
                        </div>
                        <div class="d-grid gap-2">
                            <button class="btn btn-secondary" type="button" onclick="window.location='<?= base_url() ?>'"><?= __('front.back_to_login') ?></button>
                            <button class="btn btn-primary btn-submit submit" type="submit"><?= __('front.submit') ?></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-7">
            <img src="<?= base_url('assets/login/index3') ?>/image/group.png" class="img-fluid w-100 h-100 p-3" alt="<?= __('front.image') ?>">
            <div class="affiliate-description">
                <!-- Your description here -->
            </div>
        </div>
    </div>

    <?php
    include_once "footer.php";
    ?>