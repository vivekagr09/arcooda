    </div>
</div>

<footer class="footer" id="footer">
<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <div class="container-fluid">
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link" href="#" onclick="show_section('terms_use')"><?= __('front.terms_of_use') ?></a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#" onclick="show_section('about')"><?= __('front.about') ?></a>
        </li>
        <?php 
          $store_setting = $this->Product_model->getSettings('store');
          if ($store_setting['menu_on_front']) { 
        ?>
        <li class="nav-item">
          <a class="nav-link <?= (base_url(uri_string()) == base_url('/store')) ? 'active' : '' ?>" href="<?= base_url('/store') ?>" <?= ($store_setting['menu_on_front_blank']) ? 'target="_blank"' : ''; ?>>
            <?= __('front.my_store') ?>
          </a>
        </li>
        <?php } ?>
        <li class="nav-item">
          <span class="navbar-text ms-3">
            <?= $footer ?>
          </span>
        </li>
      </ul>
    </div>
  </div>
</nav>
</footer>

<!--bootstrap 5 js files-->
<script src="<?= base_url('assets/template/js/jquery-3.6.0.min.js'); ?>?v=<?= av() ?>"></script>
<script src="<?= base_url('assets/template/js/popper.min.js'); ?>?v=<?= av() ?>"></script>
<script src="<?= base_url('assets/template/js/bootstrap.min.js'); ?>?v=<?= av() ?>"></script>
<!--bootstrap 5 js files-->

<script src="<?= base_url('assets/login/index9/js/script.js') ?>?v=<?= av() ?>"></script>

<?php
include __DIR__ . "/../cookies_consent.php";
?>
<script type="text/javascript">
    (function ($) {
      $.fn.btn = function (action) {
          var self = $(this);
          if (action == 'loading') { $(self).addClass("btn-loading"); }
          if (action == 'reset') { $(self).removeClass("btn-loading"); }
      }
    })(jQuery);

    var grecaptcha = undefined;

    $('#login-form').on('submit',function(){
        $this = $(this);

        $.ajax({
            url:'<?= base_url('auth/login') ?>',
            type:'POST',
            dataType:'json',
            data: $this.serialize() + '&type=user',
            beforeSend:function(){ $this.find(".btn-submit").btn("loading"); },
            complete:function(){ $this.find(".btn-submit").btn("reset"); },
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
                                $('.error-text').remove();
                                $formGroup.find(".input-group").after("<span class='error-text'>"+ j +"</span>");
                            } else {
                                $ele.after("<span class='error-text'>"+ j +"</span>");
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