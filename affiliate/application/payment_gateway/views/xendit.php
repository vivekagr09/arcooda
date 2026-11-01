<?php if ($settingData['sandbox_mode']) { ?>
  <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> Test mode is on</div>
<?php } ?>
<?php if (isset($apiResponce['error']) && $apiResponce['error']) { ?>
    <div class="alert alert-danger">
        <i class="fa fa-exclamation-circle"></i> <?= $apiResponce['error']; ?>
    </div>
    <div class="payment-button-group">
        <button class="btn btn-default" onclick='backCheckout()'>Back</button>
    </div>
<?php } else { ?>
    <form action="<?= $action; ?>" method="post">
        <div class="payment-button-group">
            <button type="button" class="btn btn-default" onclick='backCheckout()'>Back</button>
            <input id="btn-confirm" type="submit" class="btn btn-primary" value="Confirm" style="display: none;" />
            <button id="button-confirm" class="btn btn-primary">Confirm</button>
        </div>
    </form>
<?php } ?>
<script type="text/javascript">
    $("#button-confirm").click(function(){
        $this = $(this);

        $this.prop('disabled',true);
        
        $.ajax({
            url:'<?= $gatewayData['payment_confirmation'] ?>',
            type:'POST',
            dataType:'json',
            data:$('[name^="comment"]').serialize(),
            beforeSend:function(){$("#button-confirm").btn("loading");},
            complete:function(){$("#button-confirm").btn("reset");},
            success:function(json){
                $container = $("#checkout-confirm");
                $container.find(".has-error").removeClass("has-error");
                $container.find("span.text-danger").remove();

                if(json['errors']){
                    $.each(json['errors']['comment'], function(ii,jj){
                        $ele = $container.find('#comment_textarea'+ ii);
                        if($ele){
                            $ele.parents(".form-group").addClass("has-error");
                            $ele.after("<span class='text-danger'>"+ jj +"</span>");
                        }
                    });
                }

                if(json['success']){
                    $('#btn-confirm').trigger('click');
                }
            },
        });
    })
</script>
