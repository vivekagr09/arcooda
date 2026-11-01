<?php include_once "header.php"; ?>

        <div class="row align-items-center">
            <div class="col-12 col-lg-5">
                <div class="outer" id="outer">
                    <div class="boxx" id="register">
                        <div class="forny-logo text-center mb-3">
                            <a href="<?=base_url();?>">
                                <img src="<?= base_url($logo) ?>" <?= ($SiteSetting['front_custom_logo_size']) ? 'class="customLogoClass"' : '' ?> alt="<?= __('front.logo') ?>">
                            </a>
                        </div>
                        <div class="form-and-tabs-wrapper p-3"> <!-- Wrapper for the form and tabs -->
                            <div class="tabs-wrapper"> <!-- Wrapper specifically for the tabs -->
                                <ul class="nav nav-pills nav-fill mb-3" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link bg-transparent text-dark" href="<?= base_url() ?>">
                                            <span class="text-primary font-weight-bold"><?= __('front.login') ?></span> 
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link active bg-transparent text-dark" href="<?= base_url('register') ?>">
                                            <span class="text-primary font-weight-bold"><?= __('front.register') ?></span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            <p class="mb-3">
                                <?= __('front.enter_your_information_to_setup_a_new_account') ?>
                            </p>
                            <div class="register-form-container"> <!-- Container for the form -->
                                <?= $register_fomm ?>
                            </div>
                        </div>
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

        <?php include_once "footer.php"; ?>