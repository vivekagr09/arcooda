<?php include(APPPATH.'/views/usercontrol/login/multiple_pages/header.php');
    $reg_content = $theme_settings[0]->reg_content;
    $reg_img = $theme_settings[0]->reg_img;
?>
<?php 
if ($reg_img != '' || !empty($reg_img)) { 
    $image_link =  base_url().'/assets/images/theme_images/'.$reg_img;
}else{ 
    $image_link =  base_url().'assets/login/multiple_pages/img/register-bg.png';
} 
?>
<a href="<?= base_url('/'); ?>" class="btn-orage back-to-home front_button_color front_button_hover_color front_button_text_color"><?= __('front.back_to_homepage') ?></a>
<div class="login-hero-area">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-6 banner-bg" style="background: url(<?= $image_link; ?>);">
                   <div class="register-inner">
                        <div class="login-info text-center">
                            <i class="fa fa-exclamation-circle fa-5x front_theme_text_color" aria-hidden="true"></i>
                            <h1><?= __('front.i_am_already_a_member') ?></h1>
                            <a class="back-to-home front_button_color front_button_hover_color front_button_text_color" href="<?= site_url('/login') ?>"><?= __('front.log_in') ?></a>
                        </div>
                        <div class="register-description text-center" id="scrollbar">
                        <p><?= (!empty($reg_content)) ? nl2br($reg_content) : __('front.register_content_if_not_exist');?></p>
                        </div>
                    </div>
                </div>
				<div class="col-lg-6 form-gray-bg">
					<div class="form-inner w-100">
                       <div class="row justify-content-center">
                           <div class="col-lg-6">
								<div class="login-form register-form text-center my-4">
                                    <div class="logo">
                                        <a href="<?= base_url() ?>">
                                            <img src="<?= $logo ?>" <?= ($theme_settings[0]->custom_logo_size) ? 'class="customLogoClass"' : '' ?> alt="<?= $setting['heading'] ?>">
                                        </a>
                                    </div>
                                    <?= $register_fomm; ?>
                                </div>
                           </div>
                       </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php include(APPPATH.'/views/usercontrol/login/multiple_pages/footer.php'); ?>
<script type="text/javascript">
    (function ($) {
        $.fn.btn = function (action) {
            var self = $(this);
            if (action == 'loading') { $(self).addClass("btn-loading"); }
            if (action == 'reset') { $(self).removeClass("btn-loading"); }
        }
    })(jQuery);
</script>