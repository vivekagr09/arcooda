<?php
include_once "header.php";
?>
<div class="forny-form col-lg-6">
    <div class="forget-forms">
        <div class="reset-form d-block">
            <form class="reset-password-form">
                <h4 class="mb-5"><?= __('front.reset_password') ?></h4>
                <p class="mb-10">
                    <?= __('front.email_sent_instructions') ?>
                </p>
                <div class="form-group">
                    <div class="input-group">
                        <input class="form-control" name="email" placeholder="<?= __('front.email') ?>" type="email">
                    </div>
                </div> 
                <div class="row">
                    <div class="col-md-12">
                        <button class="btn btn-submit btn-primary btn-block"><?= __('front.send_reset_link') ?></button>
                        <button type="button" onclick="window.location='<?= base_url() ?>'" class="btn btn-block"><?= __('front.back_to_login') ?></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<?php
include_once "footer.php";
?>