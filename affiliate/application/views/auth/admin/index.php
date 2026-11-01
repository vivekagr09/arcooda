<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <title><?= __('admin.admin_login') ?></title>
    <meta content="Admin Dashboard" name="description">
    <meta content="Mannatthemes" name="author">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <?php if($setting['favicon']){ ?>
        <link rel="icon" href="<?php echo base_url('assets/images/site/'.$setting['favicon']) ?>" type="image/*" sizes="16x16">
    <?php } ?>

    <!--Include layout.php-->
    <?php include(APPPATH.'views/includes/layout.php'); ?>
    <!--Include layout.php-->
  </head>
  <body>

<?php if(isset($theme['admin_login_background_option']) && $theme['admin_login_background_option']==0) { ?>
<div class="login-main" style="<?php echo isset($theme['admin-login-background-image']) && $theme['admin-login-background-image']!="" ? 'background-image:url(assets/images/site/'.$theme['admin-login-background-image'].'':''?>">
 <?php } 
 elseif(isset($theme['admin_login_background_option']) && $theme['admin_login_background_option']==1) { ?>
    <div class="login-main" style="<?php echo isset($theme['admin_login_background_color']) ? 'background:'.$theme['admin_login_background_color'].'':''?>">
 <?php } else {?>  
        <div class="login-main">
        <?php } ?>
            <div class="login-wrapper">
                <div class="login-form" style="<?php echo isset($theme['admin_login_box_background_color']) ? 'background:'.$theme['admin_login_box_background_color'].'':''?>">

                    <?php $logo = $setting['admin-side-logo'] ? base_url('assets/images/site/'. $setting['admin-side-logo'] ) : base_url('assets/admin/img/logo.png'); ?>
                    <img src="<?= $logo ?>" class="img-fluid img-logo" alt="<?= __('admin.logo') ?>">
                    <form class="form-login">
                        
        <div class="form-group position-relative">
            <input name="type" type="hidden" value="admin"/>
            <input type="text" id="username-field" class="form-control rounded-pill ps-5" placeholder="<?= __('admin.username') ?>" name="username" required>
            <i class="bi bi-person position-absolute top-50 start-0 translate-middle-y text-muted ps-2"></i>
        </div>

        <div class="form-group position-relative mt-3">
            <input type="password" id="password-field" class="form-control rounded-pill ps-5 pe-5" placeholder="<?= __('admin.password') ?>" name="password" required>
            <i class="bi bi-lock position-absolute top-50 start-0 translate-middle-y text-muted ps-2"></i>
            <i id="toggle-password" class="bi bi-eye-fill position-absolute top-50 end-0 translate-middle-y cursor-pointer text-muted pe-2"></i>
        </div>


<script>
    $(document).ready(function() {
    $('#toggle-password').click(function() {
        const passwordField = $('#password-field');
        const passwordFieldType = passwordField.attr('type');
        
        if(passwordFieldType === 'password') {
            passwordField.attr('type', 'text');
            $(this).removeClass('bi-eye-fill').addClass('bi-eye-slash-fill');
        } else {
            passwordField.attr('type', 'password');
            $(this).removeClass('bi-eye-slash-fill').addClass('bi-eye-fill');
        }
    });
});

</script>
                        <div class="nav-item dropdown">
                            <?= $LanguageHtml ?>
                        </div>
                        <script type="text/javascript">
                            var grecaptcha = undefined;
                        </script>
                        <?php 
                            $db =& get_instance(); 
                            $googlerecaptcha =$db->Product_model->getSettings('googlerecaptcha');
                        ?>
                        <?php if (isset($googlerecaptcha['admin_login']) && $googlerecaptcha['admin_login']) { ?>
                            <div class="captch">
                                <script src='https://www.google.com/recaptcha/api.js'></script>
                                <div class="g-recaptcha" data-sitekey="<?= $googlerecaptcha['sitekey'] ?>"></div>
                                <input type="hidden" name="captch_response" id="captch_response"> <br><br>
                            </div>
                        <?php } ?>
                        <div class="form-group">
                            <input type="submit" class="btn btn-secondary text-white" value="<?= __('admin.log_in') ?>">
                        </div>
                        <a href="javascript:void(0)" onclick="_open('forget-form')" class="forgotpass"><?= __('admin.forgot_your_password') ?></a>
                    </form>

                    <form class="forget-form" style="display:none;">
                        <div class="form-group">
                            <input type="email" name="email" placeholder="<?= __('admin.enter_your_email_address') ?>" required>
                            <span></span>
                            <img src="<?= base_url('assets/admin/'); ?>img/user.png" alt="<?= __('admin.icon') ?>">
                        </div>
                        <div class="form-group">
                            <input type="submit" class="btn btn-secondary text-white" value="<?= __('admin.forgot_password') ?>">
                        </div>
                        <a href="javascript:void(0)" onclick="_open('form-login')" class="forgotpass"><?= __('admin.back_to_login') ?></a>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<script type="text/javascript">
            function _open(form) {
                $(".form-login, .forget-form").hide();
                $("."+form).show();
            }

            $('.form-login').on('submit',function(){
                var check = true;
                var inputLogin = $('.form-login');
                
                if (check) {
                    var check_captch = true;
                    if (grecaptcha === undefined) {
                        check_captch = false;
                    }

                    $("#captch_response").val('')

                    if(check_captch){
                      captch_response = grecaptcha.getResponse();
                      $("#captch_response").val(captch_response)
                    }

                    $.ajax({
                        url:'<?= base_url('auth') ?>/login',
                        type:'POST',
                        dataType:'json',
                        data: $('.form-login').serialize(),
                        beforeSend:function(){ $('.form-login button').prop("disabled",true); },
                        complete:function(){ $('.form-login button').prop("disabled",false); },
                        success:function(json){
                              
                            $container = inputLogin;
                            $container.find(".has-error").removeClass("has-error");
                            $container.find("span.text-danger,.alert").remove();
                            
                            if(json['errors']){
                                $.each(json['errors'], function(i,j){
                                    if(i == 'captch_response' && grecaptcha){ grecaptcha.reset(); }
                                    $ele = $container.find('[name="'+ i +'"]');
                                    if($ele){
                                        $ele.addClass("has-error");
                                        $ele.parent().before("<div class='alert alert-danger mb-3'>"+ j +"</div>");
                                    }
                                })
                            }

                            <?php 
                            if(isset($update_version_outside)) {
                                ?>
                                if(json['redirect']){
                                    window.location = '<?= base_url('update'); ?>';
                                }
                                <?php
                            } else {
                                ?>
                                if(json['redirect']){
                                    window.location = json['redirect'];
                                }
                                <?php
                            } 
                            ?> 
                        },
                    })
                }

                return false;
            });

            $('.forget-form').on('submit',function(){
              
                $.ajax({
                    url:'<?= base_url('auth') ?>/forget',
                    type:'POST',
                    dataType:'json',
                    data: $('.forget-form').serialize(),
                    beforeSend:function(){ $('.forget-form button').prop("disabled",true); },
                    complete:function(){ $('.forget-form button').prop("disabled",false); },
                    success:function(json){
                        
                        $container = $('.forget-form');
                        $container.find(".has-error").removeClass("has-error");
                        $container.find("span.text-danger,.alert").remove();
                        
                        if(json['errors']){
                            $.each(json['errors'], function(i,j){
                                $ele = $container.find('[name="'+ i +'"]');
                                if($ele){
                                    $ele.addClass("has-error");
                                    $ele.parent().before("<div class='alert alert-danger mb-3'>"+ j +"</div>");
                                }
                            })
                        }

                        if(json['success']){
                            $('.forget-form').prepend("<div class='alert alert-success'>"+ json['success'] +"</div>");
                        }

                        if(json['redirect']){
                            window.location = json['redirect'];
                        }
                    },
                })
                
                return false;
            });
        </script>
  </body>
</body>
</html>