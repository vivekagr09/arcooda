<?php include(APPPATH.'/views/usercontrol/login/multiple_pages/header.php'); ?>

    <a href="<?= base_url('/'); ?>" class="btn-orage back-to-home front_button_color front_button_hover_color front_button_text_color"><?= __('front.back_to_homepage') ?></a>
    <div class="login-hero-area forgat-password-area">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-6 banner-bg" style="background: url(<?= base_url('assets/login/multiple_pages') ?>/img/forgat.png);">
                </div>
				<div class="col-lg-6 form-gray-bg">
					<div class="form-inner w-100">
                       <div class="row justify-content-center">
                           <div class="col-lg-6">
								<div class="login-form register-form text-center">
                                    <div class="logo">
                                        <div class="logo mt-4">
                                            <a href="<?= base_url() ?>">
                                                <img src="<?= $logo ?>" <?= ($theme_settings[0]->custom_logo_size) ? 'class="customLogoClass"' : '' ?> alt="<?= $setting['heading'] ?>">
                                            </a>
                                        </div>
                                    </div>
                                    <form class="reset-password-form">
                                        <div class="form-group">
                                            <input class="form-control" name="email" placeholder="<?= __('front.email') ?>" type="email">
                                        </div>  
										<div class="form-group">
                                            <p><?= __('front.email_sent_instructions') ?></p>
                                        </div>                                        
                                        <input class="btn-submit front_button_color front_button_hover_color front_button_text_color" type="submit" value="<?= __('front.send_reset_link') ?>">
                                    </form>
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
    
    $('.reset-password-form').on('submit',function(){
        $this = $(this);
        $.ajax({
            url:'<?= base_url('auth/forget') ?>',
            type:'POST',
            dataType:'json',
            data: $this.serialize(),
            beforeSend:function(){ $this.find(".btn-submit").btn("loading"); },
            complete:function(){ $this.find(".btn-submit").btn("reset"); },
            success:function(json){
                $this.find(".is-invalid").removeClass("is-invalid");
                $this.find("span.invalid-feedback,.success-msg").remove();

                if(json['success']){
                    $this.find(".btn-submit").before("<div class='alert success-msg alert-success'> " + json['success'] + "</div>");
                }
                if(json['errors']){
                    $.each(json['errors'], function(i,j){
                        if(i == 'captch_response' && grecaptcha){ grecaptcha.reset(); }
                        $ele = $this.find('[name="'+ i +'"]');
                        if($ele){
                            $formGroup = $ele.parents(".form-group");
                            $ele.addClass("is-invalid");
                            if($formGroup.find(".input-group").length){
                                $formGroup.find(".input-group").after("<span class='bg-white d-block invalid-feedback'>"+ j +"</span>");
                            } else {
                                $ele.after("<span class='invalid-feedback'>"+ j +"</span>");
                            }
                        }
                    })
                }
                if(json['redirect']){ window.location = json['redirect']; }
            },
        })
        return false;
    });
</script>