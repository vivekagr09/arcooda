                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <div class="background-image"></div>
</div>

<!-- Footer -->
<footer class="footer py-1 text-light bg-dark fixed-bottom">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <nav class="navbar navbar-expand-lg navbar-dark bg-transparent">
                    <button class="navbar-toggler btn-light" type="button" data-bs-toggle="collapse" data-bs-target="#footernav" aria-controls="footernav" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="footernav">
                        <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                            <li class="nav-item">
                                <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#termOfUse">
                                    <?= __('front.temrs_of_use') ?>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#about">
                                    <?= __('front.about') ?>
                                </a>
                            </li>
                            <?php 
                            $store_setting = $this->Product_model->getSettings('store');
                            if($store_setting['menu_on_front']){ ?>
                            <li class="nav-item <?php if(base_url(uri_string()) == base_url('/store')){ echo 'active'; } ?>">
                                <a class="nav-link" href="<?= base_url('/store') ?>" <?= ($store_setting['menu_on_front_blank']) ? 'target="_blank"' : ''; ?>><?= __('front.my_store') ?></a>
                            </li>
                            <?php } ?>

                            <?php if($cookies_menu_setting['cookies_menu']){ ?>
                                <li class="nav-item">
                                    <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#cookie-preferences-modal"><?= __('front.cookie_preferences') ?></a>
                                </li>
                            <?php } ?>
                        </ul>
                    </div>
                </nav>
            </div>
            <div class="col-12 text-center">
                <?= $footer ?>
            </div>
        </div>
    </div>
</footer>
<!-- Footer -->

<!--bootstrap 5 js files-->
<script src="<?= base_url('assets/template/js/jquery-3.6.0.min.js'); ?>?v=<?= av() ?>"></script>
<script src="<?= base_url('assets/template/js/popper.min.js'); ?>?v=<?= av() ?>"></script>
<script src="<?= base_url('assets/template/js/bootstrap.min.js'); ?>?v=<?= av() ?>"></script>
<!--bootstrap 5 js files-->

<script src="<?= base_url('assets/js/main.js') ?>?v=<?= av() ?>"></script>

<?php
include __DIR__ . "/../cookies_consent.php";
?>

<!-- login-form script -->
<script type="text/javascript">
    var grecaptcha = undefined;
    $('#login-form').on('submit',function(){
        $this = $(this);
        $.ajax({
            url:'<?= base_url('auth/login') ?>',
            type:'POST',
            dataType:'json',
            data: $this.serialize() + '&type=user',
            success:function(json){
                $this.find(".is-valid").removeClass("is-valid");
                $this.find(".has-error").removeClass("has-error");
                $this.find("span.text-danger").remove();
                if(json['errors']){
                    $.each(json['errors'], function(i,j){
                        if(i == 'captch_response' && grecaptcha){ grecaptcha.reset(); }
                        $ele = $this.find('[name="'+ i +'"]');
                        if($ele){
                            $formGroup = $ele.parents(".form-group");
                            $formGroup.addClass("has-error");
                            if($formGroup.find(".input-group").length){
                                $formGroup.find(".input-group").after("<span class='bg-white d-block text-danger'>"+ j +"</span>");
                            } else {
                                $ele.after("<span class='text-danger'>"+ j +"</span>");
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
<!-- login-form script -->

<!-- reset-password-form script -->
<script type="text/javascript">
    var grecaptcha = undefined;
    $('.reset-password-form').on('submit',function(){
        $this = $(this);
        $.ajax({
            url:'<?= base_url('auth/forget') ?>',
            type:'POST',
            dataType:'json',
            data: $this.serialize(),
            success:function(json){
                $this.find(".has-error").removeClass("has-error");
                $this.find("span.text-danger,.success-msg").remove();
                if(json['success']){
                    $this.find('[name="email"]').after("<div class='alert success-msg alert-success'> " + json['success'] + "</div>");
                }

                if(json['errors']){
                    $.each(json['errors'], function(i,j){
                        if(i == 'captch_response' && grecaptcha){ grecaptcha.reset(); }

                        $ele = $this.find('[name="'+ i +'"]');
                        if($ele){
                            $formGroup = $ele.parents(".form-group");
                            $formGroup.addClass("has-error");

                            if($formGroup.find(".input-group").length){
                                $formGroup.find(".input-group").after("<span class='text-danger'>"+ j +"</span>");
                            } else {
                                $ele.after("<span class='text-danger'>"+ j +"</span>");
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
<!-- reset-password-form script -->

</body>
</html>

