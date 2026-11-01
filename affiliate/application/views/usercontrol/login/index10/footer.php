     </div>
</div>

<!--bootstrap 5 js files-->
<script src="<?= base_url('assets/template/js/jquery-3.6.0.min.js'); ?>?v=<?= av() ?>"></script>
<script src="<?= base_url('assets/template/js/popper.min.js'); ?>?v=<?= av() ?>"></script>
<script src="<?= base_url('assets/template/js/bootstrap.min.js'); ?>?v=<?= av() ?>"></script>
<!--bootstrap 5 js files-->

<?php
include __DIR__ . "/../cookies_consent.php";
?>
<!-- Start login-form script -->
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
<!-- End login-form script -->

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
