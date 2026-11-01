<?php
include_once "header.php";
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-lg p-4">
                <ul class="nav nav-pills justify-content-center mb-4" id="pills-tab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url() ?>">
                            <?= __('front.login') ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="<?= base_url('register') ?>">
                            <?= __('front.register') ?>
                        </a>
                    </li>
                </ul>
                <p class="text-center mb-4">
                    <?= __('front.enter_your_information_to_setup_a_new_account') ?>
                </p>
                <?= $register_fomm ?>
            </div>
        </div>
    </div>
</div>

<?php
include_once "footer.php";
?>