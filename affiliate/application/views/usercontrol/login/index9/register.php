<?php
include_once "header.php";
?>
<!-- sign up page -->
<div>
    <div class="row justify-content-center flex-grow-1 flex-strink-1 ">
        <div class="col-md-10 form_box_1 signup-wrapper">
            <h3 class="text-center"><?= __('front.register') ?></h3>
            <p class="subtitle"><?= __('user.enter_your_information_to_setup_a_new_account') ?></p>
                            <?= $register_fomm ?>
            <a href="<?=base_url();?>"  class="link_ text-center"><?= __('front.already_have_an_account') ?></a>
        </div>
    </div>
</div>
<!-- sign up page -->
</div>

<div class="col-md-6 subscribe_box">
<div class="row justify-content-end">
    <button class="cross_btn d-none" onclick="show_section('input');hide_cross(this)" id="cross_btn"> &#10006;</button>
</div>
<div data-view-section="input" class="row justify-content-center align-items-center flex-grow-1 align-items-center flex-strink-1">
    <div class="col-md-10 form_box_1 form_box_2 extra_margin mt-2 text-center">
        <img src="<?= base_url('assets/login/index9') ?>/img/affiliate-image.png" class="img-fluid mt-4" alt="<?= __('front.image') ?>">

        <h1 class="text-white h3"><?= $setting['heading'] ?></h1>
        <div class="content_area_home">
        <h5 class="text-white h5"><?= $setting['content'] ?></h5>
    </div>
    </div>
</div>
 
<div data-view-section="terms_use" class="row justify-content-center align-items-center flex-grow-1 align-items-center flex-strink-1 d-none">
    <div class="col-md-10 form_box_1 form_box_2">
        <h3 class="text-center text-light"><?= $tnc['heading'] ?></h3>
        <div class="content_area">
        <p class="terms_text">
            <?= $tnc['content'] ?>
        </p>
        </div>
    </div>
</div>
<div data-view-section="about" class="row justify-content-center align-items-center flex-grow-1 align-items-center flex-strink-1 d-none">
    <div class="col-md-10 form_box_1 form_box_2">
        <h3 class="text-center text-light"><?= __('front.about') ?></h3>
        <div class="content_area">
       
        <p class="terms_text">
            <?= $setting['about_content'] ?>
        </p>
        </div>
    </div>
</div>
</div>
</div>
<?php
include_once "footer.php";
?>