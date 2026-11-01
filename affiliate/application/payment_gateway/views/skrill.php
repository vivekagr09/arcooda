<form action='https://www.moneybookers.com/app/payment.pl' method="post">
    <input type="hidden" name="pay_to_email" value="<?= $settingData['email']; ?>" />
    <input type="hidden" name="recipient_description" value="<?= $gatewayData['firstname']." " .$gatewayData['lastname']; ?>" />
    <input type="hidden" name="transaction_id" value="<?= time().'-'.$gatewayData['id']; ?>" />
    <input type="hidden" name="return_url" value="<?= $gatewayData['return_url']; ?>" />
    <input type="hidden" name="cancel_url" value="<?= $gatewayData['cancel_url']; ?>" />
    <input type="hidden" name="status_url" value="<?= $gatewayData['status_url']; ?>" />
    <input type="hidden" name="language" value='en' />
    <input type="hidden" name="pay_from_email" value="<?= $gatewayData['email']; ?>" />
    <input type="hidden" name="firstname" value="<?= $gatewayData['firstname']; ?>" />
    <input type="hidden" name="lastname" value="<?= $gatewayData['lastname']; ?>" />
    <input type="hidden" name="address" value="<?= $gatewayData['address']; ?>" />
    <input type="hidden" name="address2" value="<?= $gatewayData['address2']; ?>" />
    <input type="hidden" name="phone_number" value="<?= $gatewayData['phone']; ?>" />
    <input type="hidden" name="postal_code" value="<?= $gatewayData['zip_code']; ?>" />
    <input type="hidden" name="city" value="<?= $gatewayData['city']; ?>" />
    <input type="hidden" name="state" value="<?= $gatewayData['state_name']; ?>" />
    <input type="hidden" name="country" value="<?= $gatewayData['country_code']; ?>" />
    <input type="hidden" name="amount" value="<?= $gatewayData['total']; ?>" />
    <input type="hidden" name="currency" value="<?= $gatewayData['currency_code']; ?>" />
    <input type="hidden" name="detail1_text" value="<?= $gatewayData['detail1_text']; ?>" />
    <input type="hidden" name="merchant_fields" value="id" />
    <input type="hidden" name="id" value="<?= $gatewayData['id']; ?>" />
    <input type="hidden" name="platform" value="" />
    <div class="payment-button-group">
        <button type="button" class="btn btn-default" onclick='backCheckout()'>Back</button>
        <input id="btn-confirm" type="submit" class="btn btn-primary" value="Confirm" style="display: none;" />
        <button id="button-confirm" class="btn btn-primary">Confirm</button>
    </div>
</form>
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
