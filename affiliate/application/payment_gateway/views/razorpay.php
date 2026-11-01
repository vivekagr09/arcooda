<?php 
if (isset($apiResponce['error'])){ 
    echo $apiResponce['error']; 
} else { ?>
    <div class="payment-button-group">
        <button type="button" class="btn btn-default" onclick='backCheckout()'>Back</button>
        <button id="button-confirm" class="btn btn-primary">Confirm</button>
    </div>
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
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
                        var options = {
                            "key": "<?= $razorpay_key_id; ?>", 
                            "amount": "<?= $gatewayData['total']; ?>",
                            "currency": "<?=  $gatewayData['currency_code']; ?>",
                            "name": "<?= $gatewayData['firstname'].' '.$gatewayData['lastname']; ?>",
                            "description": "<?= $gatewayData['firstname'].' ' .$gatewayData['lastname']; ?>",
                            "image": 'https://assets.piedpiper.com/logo.png',
                            "order_id": "<?= $razorpay_id; ?>",
                            "callback_url": "<?= $gatewayData['callback_url']; ?>",
                            "prefill": {
                                "name": "<?= $gatewayData['firstname'].' '.$gatewayData['lastname']; ?>",
                                "email": "<?= $gatewayData['email']; ?>",
                                "contact": "<?= $gatewayData['phone']; ?>"
                            },
                            "notes": {
                                "address": "<?= str_replace("\n", "", $gatewayData['address']); ?>"
                            },
                            "theme": {
                                "color": "#F37254"
                            }
                        };
                        var rzp1 = new Razorpay(options);
                		rzp1.open();
                    }
                },
            });
    	});
    </script>
<?php } ?>
