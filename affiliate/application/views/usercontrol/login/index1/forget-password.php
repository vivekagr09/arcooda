<?php
include_once "header.php";
?>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow">
                <div class="card-body">
                    <h4 class="card-title text-center mb-4"><?= __('front.reset_your_password') ?></h4>
                    <p class="card-text mb-4 text-center"><?= __('front.email_sent_instructions') ?></p>
                    <form class="reset-password-form">
                        <div class="form-group mb-4">
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-envelope-fill"></i>
                                </span>
                                <input class="form-control" name="email" placeholder="<?= __('front.insert_email') ?>" type="email">
                            </div>
                        </div>
                        <div class="d-grid gap-2">
                            <button class="btn btn-primary btn-block"><?= __('front.send_reset_link') ?></button>
                            <button type="button" onclick="window.location='<?= base_url() ?>'" class="btn btn-block btn-link"><?= __('front.back_to_login') ?></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
include_once "footer.php";
?>