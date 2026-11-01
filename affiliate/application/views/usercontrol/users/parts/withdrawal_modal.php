<?php if(isset($warning)){ ?>
    <div class="modal-header">
        <h5 class="modal-title"><?= __('user.withdrawal_notification_info') ?></h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body"> 
        <h4 class="notification_on_pages">
            <div class="badge bg-warning text-center"><?= $warning ?></div>
        </h4>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?= __('user.close') ?></button>
    </div>

<?php } 
else if(isset($danger)){ ?>
    <div class="modal-header">
        <h5 class="modal-title"><?= __('user.withdrawal_notification_info') ?></h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        <h4 class="notification_on_pages">
            <div class="badge bg-danger"><?= $danger ?></div>
        </h4>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?= __('user.close') ?></button>
    </div>
<?php }
else { ?>
<div class="modal-body p-0">
    <div id="accordion">
        <?php $index = 0; foreach ($payment_methods as $key => $value) { if ($value['status']==1 && $value['is_install']==1) { ?>
            <div class="wpayment border-radius-0">
                <div class="border-bottom border-radius-0 wpayment-header p-3 <?= $index == 0 ? 'active-payment' : '' ?>" data-tab='collapse-<?= $value['code'] ?>'>
                    <h5 class="m-0 font-16">
                        <?= __('user.'.str_replace(' ','_',strtolower($value['title']))) ?>
                    </h5>
                </div>
            </div>
        <?php $index++; } } ?>
 
        <div class="wpayment-container">
            <?php $index = 0; foreach ($payment_methods as $key => $value) {  if ($value['status']) {  
                    ?>

                <div id="collapse-<?= $value['code'] ?>" class="<?= $index == 0 ? '' : 'd-none'  ?> wpayment-body"> 

                    <h3 class="payment-heading"><?= __('user.get_paid_with') ?> <?= __('user.'.str_replace(' ','_',strtolower($value['title']))) ?></h3>
                    <form id="payment-form-<?= $value['code'] ?>" enctype="multipart/form-data">
                        <input type="hidden" name="ids" value="<?= $ids ?>">
                        <input type="hidden" name="code" value="<?= $value['code'] ?>">

                        <?= $value['user_setting'] ?>

                        <div class="pb-3">
                        
                        <?php
                        if($value['code'] == 'bank_transfer')
                        {
                            
                            if(isset($paymentlist) && isset($paymentlist[0]) && $PrimaryPaymentMethodStatus != "") 
                            {    
                            ?>

                            <label><?=__('user.bank_name')?>: <?= $paymentlist[0]['payment_bank_name']?> </label><br/>
                            <label><?=__('user.account_number')?>: <?= $paymentlist[0]['payment_account_number']?> </label><br/>
                            <label><?=__('user.account_name')?>: <?= $paymentlist[0]['payment_account_name']?> </label><br/>
                            <label><?=__('user.ifsc_code')?>: <?= $paymentlist[0]['payment_ifsc_code']?> </label><br/>
 
                          <input class="form-control"  type="hidden" name="bank_name" value="<?= $paymentlist[0]['payment_bank_name']?>">   
                           <input class="form-control"  type="hidden" name="account_number" value="<?= $paymentlist[0]['payment_account_number']?>"> 
                            <input class="form-control"  type="hidden" name="account_name" value="<?= $paymentlist[0]['payment_account_name']?>"> 
                             <input class="form-control"  type="hidden" name="ifsc_code" value="<?= $paymentlist[0]['payment_ifsc_code']?>">
                                
                                <?php 

                                 if($setting_exist_status == 1)
                                 {
                                    $get_custom_fiels_data = json_decode($get_custom_fiels['bt_custom_field']);
                                    $get_custom_fiels_validate = json_decode($get_custom_fiels['response_validate']);
                                ?>   

                                        <?php if(isset($get_custom_fiels['withdrawal_proof']) && in_array($get_custom_fiels['withdrawal_proof'], [1,2])) {
                                    ?>
                                    <div class="form-group mt-4">
                                        <label><?= __('user.payment_proof') ?></label>
                                        <input type="file" id="payment_proof" name="payment_proof" <?= $get_custom_fiels['withdrawal_proof'] == 2 ? "required" : "" ?>/>
                                    </div>
                                    <div class="text-info mb-4">
                                    <?= __('user.if_admin_asked_you_send_payment_proof') ?>
                                    </div>
                                    <?php
                                     }
                                 }   
                               ?>
                                    </div>
                                <?php
                                $get_custom_fiels_validate = isset($get_custom_fiels_validate) && is_array($get_custom_fiels_validate) ? $get_custom_fiels_validate : []; 
                                 ?>
                                <input type="hidden" name="get_custom_fiels_validate" value="<?= implode(",",$get_custom_fiels_validate);?>">
                                <div class="text-right">
                                    <button class="btn btn-submit btn-primary"><?= __('user.submit') ?></button>
                                </div> 
                                  
                                <?php
                            }
                            else
                            {
                                ?>
                                <?php if(isset($paymentlist) && !isset($paymentlist[0]) && $PrimaryPaymentMethodStatus == ""){?>
                                <div class="form-group mt-4">
                                    <?=__('user.payouts_bank_transfer_payment_details_missing')?>
                                    <br/>
                                    <?=__('user.payouts_primary_payment_not_chosen')?>
                                    <br/>
                                    <a href="<?=base_url('usercontrol/payment_details')?>"> <?= __('user.click_here_to_enter_payment_details')?> </a>
                                </div> 
                            <?php }elseif(isset($paymentlist) && isset($paymentlist[0]) && $PrimaryPaymentMethodStatus != ""){?>
                                <div class="form-group mt-4">
                                    <?=__('user.payouts_bank_transfer_payment_details_missing')?>
                                    <br/>
                                    <a href="<?=base_url('usercontrol/payment_details')?>"> <?= __('user.click_here_to_enter_payment_details')?> </a>
                                </div>

                            <?php }elseif(isset($paymentlist) && !isset($paymentlist[0]) && $PrimaryPaymentMethodStatus != ""){?>
                                <div class="form-group mt-4">
                                    
                                    <?=__('user.payouts_bank_transfer_payment_details_missing')?>
                                    <br/>
                                    <a href="<?=base_url('usercontrol/payment_details')?>"> <?= __('user.click_here_to_enter_payment_details')?> </a>
                                </div> 
                            <?php }else{?>
                                <div class="form-group mt-4">
                                    
                                    <?=__('user.payouts_primary_payment_not_chosen')?>
                                    <br/>
                                    <a href="<?=base_url('usercontrol/payment_details')?>"> <?= __('user.click_here_to_enter_payment_details')?> </a>
                                </div> 
                            <?php }?>
                                <!-- closing div -->
                                </div>
                                <?php
                            }

                        }
                        else  if($value['code'] == 'paypal')
                        {
                            if(isset($paypalaccounts) && isset($paypalaccounts[0])) 
                            {    
                            ?>
                                <div class="form-group mt-4">
                                        <label class="form-control-label"><?=__('user.paypal_email')?>: <?=$paypalaccounts[0]['paypal_email']?></label>
                                         
                                        <input type="hidden" class="form-control" name="paypal_email" value="<?=$paypalaccounts[0]['paypal_email']?>" >
                                </div>

                               </div>
                            <?php
                                $get_custom_fiels_validate = isset($get_custom_fiels_validate) && is_array($get_custom_fiels_validate) ? $get_custom_fiels_validate : []; 
                             ?>
                            <input type="hidden" name="get_custom_fiels_validate" value="<?= implode(",",$get_custom_fiels_validate);?>">
                            <div class="text-right">
                                <button class="btn btn-submit btn-primary"><?= __('user.submit') ?></button>
                            </div>     

                            <?php
                            }
                            else
                            {
                                ?>
                                <div class="form-group mt-4">
                                    <?=__('user.payouts_paypal_payment_details_missing')?>
                                    <br/>
                                    <a href="<?=base_url('usercontrol/payment_details')?>"> <?= __('user.click_here_to_enter_payment_details')?> </a>
                                </div> 
                                <!-- closing div -->
                                </div>
                                <?php

                            }
                           
                        }
                        else 
                        { 
                            ?>

                            </div>
                            <?php
                                $get_custom_fiels_validate = isset($get_custom_fiels_validate) && is_array($get_custom_fiels_validate) ? $get_custom_fiels_validate : []; 
                             ?>
                            <input type="hidden" name="get_custom_fiels_validate" value="<?= implode(",",$get_custom_fiels_validate);?>">
                            <div class="text-right">
                                <button class="btn btn-submit btn-primary"><?= __('user.submit') ?></button>
                            </div>        

                            <?php

                        }    
                        
                        ?>
                        
                    </form>
                </div>
            <?php $index++; } } ?>
        </div> 
        <?php if ($index == 0) { ?>
            <h5 class="notification_on_pages mb-3">
                <div class="bg-danger text-white p-3 rounded">
                 <?= __('user.warning_no_payment_options_available_contact_assistance') ?>
                </div>
            </h5>
        <?php } ?>
    </div>
</div>
<script type="text/javascript">
$(document).ready(function() {
    $(".wpayment-header").click(function(){
        $(".wpayment-container .wpayment-body").addClass("d-none");
        var tab = $(this).attr("data-tab");
        $("#" + tab).removeClass("d-none");
        $(".wpayment-header.active-payment").removeClass("active-payment");
        $(this).addClass("active-payment");
    });
});
</script>

<?php  } ?>