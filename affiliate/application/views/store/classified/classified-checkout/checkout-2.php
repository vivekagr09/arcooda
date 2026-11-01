 <link href="<?php echo base_url('assets/store/classified/classified-checkout/checkout-2/') ?>css/form-validation.css" rel="stylesheet">

 <link href="<?php echo base_url('assets/store/classified/classified-checkout/checkout-2/') ?>css/bootstrap.min.css" rel="stylesheet">
 <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

 <script src="<?php echo base_url('assets/store/classified/classified-checkout/checkout-2/') ?>js/bootstrap.bundle.min.js"></script>
 <link rel="stylesheet" href="<?= base_url('assets/plugins/tel/css/intlTelInput.css') ?>?v=<?= av() ?>">
 <script src="<?= base_url('assets/plugins/tel/js/intlTelInput.js') ?>"></script>

 <script type="text/javascript">
   var grecaptcha = undefined;
   var ischeckout =true;
</script>
<section aff-section="classified_checkout_language_and_currency" class="d-flex flex-row-reverse mt-3 w-100 pe-3" style="position: absolute;"></section>

<script aff-template="classified_checkout_language_and_currency" type="text/html">
   {{#SelectedLanguage}}
   <div class="dropdown mx-3">
     <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
       {{SelectedLanguage}}
     </button>
     <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
         {{#LanguageHtml}}
           <li><a class="dropdown-item" href="{{href}}">{{name}}</a></li>
         {{/LanguageHtml}}
     </ul>
   </div>
   {{/SelectedLanguage}}

   {{#SelectedCurrency}}
   <div class="dropdown mx-3">
     <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton2" data-bs-toggle="dropdown" aria-expanded="false">
       {{SelectedCurrency}}
     </button>
     <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton2">
         {{#CurrencyHtml}}
           <li><a class="dropdown-item" href="{{href}}">{{code}}</a></li>
         {{/CurrencyHtml}}
     </ul>
   </div>
   {{/SelectedCurrency}}      
</script>
<section aff-section="classified_checkout_page"></section>
<script aff-template="classified_checkout_page" type="text/html">
    <?php if (isset($checkout_preview) && $checkout_preview==0): ?>
      <div class="row">
         <div class="col-md-12 text-center">
           <img src="https://picsum.photos/500/800" alt="">
        </div>
     </div>
  <?php endif;  ?>
  <main class="<?=(isset($checkout_preview) && $checkout_preview==0) ? 'd-none':''?>">
   <div class="container">
      <div class="col-lg-9 col-sm-12 mx-auto bg-white">
         <div class="secure-img">
            <img src="<?php echo base_url('assets/store/classified/classified-checkout/checkout-2/') ?>img/shipping.jpg" alt="shipping" >
         </div>
         <div class="row mt-4 mb-4">

            <div class="col-lg-8 col-md-12">
              <div class="row">
               <div class="col-md-6">
                  <h4 class="mb-3"><?php echo $allow_shipping == 1 ? __('store.billing_shipping_address') : __('store.billing_address');?></h4>
               </div>
               <div class="col-md-6">
                  <?php  if(!$is_logged)      {?>
                     <a href="#loginModal" title="" style="color:#000" data-bs-toggle="modal" data-bs-target="#loginModal"><?php echo __('store.already_user_login');?></a>
                  <?php } else { ?>
                     <h5><?php echo __('store.welcome');?> <?php echo !empty($userdetails['username'])? $userdetails['username'] :$userdetails['firstname'];?><h5>
                     <?php } ?> 
                  </div>
               </div>
               <section aff-section="classified_checkout_form"> 
                  <form class="needs-validation" novalidate>
                     <div class="row g-3">
                     
                        <div class="col-sm-6 <?php echo $is_logged==true && $allow_shipping ? 'd-none':'';?>">
                           <input type="text" class="form-control" name="firstname" placeholder="<?php echo __('store.enter_your_first_name')?>" value ="<?php echo $is_logged==true ? $userdetails['firstname']:''?>"/>

                        </div>
                        

                           <div class="col-sm-6 <?php echo $is_logged==true && $allow_shipping ? 'd-none':'';?>">

                              <input type="text" class="form-control" name="lastname" placeholder="<?php echo __('store.enter_your_last_name')?>" value ="<?php echo $is_logged==true ? $userdetails['lastname']:''?>"/>

                           </div>
                           <div class="col-6 <?php echo $is_logged==true && $allow_shipping  ? 'd-none':'';?>">
                              <input type="email" class="form-control" name="email" placeholder="<?php echo __('store.enter_your_email_address')?>" value ="<?php echo $is_logged==true ? $userdetails['email']:''?>" />

                           </div>
                           <?php if (!$allow_shipping): ?>
                              <input type="hidden" id="phonenumber-input" name='PhoneNumberInput' value="" class="form-control" placeholder="<?= __('store.phone_number') ?>" />

                           <div class="col-6 <?php echo $is_logged==true && $allow_shipping  ? 'd-none':'';?>">
                              <input onkeypress="return isNumberKey(event);" type="text" id="phoneguest" class="form-control" name="phone" placeholder="<?php echo __('store.phone')?>" value ="<?php echo $is_logged==true ? $userdetails['phone']:''?>" />

                           </div>
                      
                           <?php endif ?>


                        {{#allow_shipping}}

                        <div class="col-6">
                           <input type="hidden" id="phonenumber-input" name='PhoneNumberInput' value="" class="form-control" placeholder="<?= __('store.phone_number') ?>" />

                         <input onkeypress="return isNumberKey(event);" type="text" class="form-control" name="phone" id="phone"  value ="<?php echo $is_logged==true ? $shipping->phone:''?>" placeholder="<?= __('store.phone') ?>" /> 

                      </div>
                      <div class="col-md-6">
                       <select class="form-select" name="country" id="country_id">
                        <option disabled selected><?php echo __('store.select_country');?></option>
                     </select>

                  </div>
                  <div class="col-md-6">
                   <select class="form-select" name="state" id="state_id">
                     <option disabled selected><?php echo __('store.select_state');?></option>
                  </select>

               </div>
               <div class="col-md-6">
                <input type="text" class="form-control"  value ="<?php echo $is_logged==true ? $shipping->city:''?>" name="city" placeholder="<?php echo __('store.city');?>" />

             </div>
             <div class="col-md-6">
              <input type="text"  value ="<?php echo $is_logged==true ? $shipping->zip_code:''?>" class="form-control" name="zip_code" placeholder="<?php echo __('store.postal_code');?>" />

           </div>
           <div class="col-12">
            <input type="text" class="form-control" name="address" placeholder="<?php echo __('store.address')?>" value ="<?php echo $is_logged==true ? $shipping->address:''?>" />

         </div>
         {{/allow_shipping}}
      </div>
      <hr class="my-4">
      <h4 class="mb-4"><?php echo __('store.payment')?></h4>
      <div class="my-3">
        <div class="row image-size-payment">
         {{^payment_gateways}}
         <div class="col-12">
            <div class="alert alert-info"><?= __('store.no_payment_options') ?></div>
         </div>
         {{/payment_gateways}}
         {{#payment_gateways}} 
         <div class="col-sm-6">
            <div class="form-check border-style">

               {{#active}} 
               <input id="{{name}}" name="payment_gateway" type="radio" class="form-check-input" value="{{name}}" checked />
               {{/active}}
               {{^active}} 
               <input id="{{name}}" name="payment_gateway" type="radio" class="form-check-input" value="{{name}}"  />
               {{/active}}
               <img src="{{icon}}" class="img-fluid" style="max-width:75%" alt="{{display_name}}">
            </div>
         </div>
         {{/payment_gateways}}
      </div>
   </div>



   <div class="form-check mt-4">
      <input type="checkbox" id="chkAgree" class="form-check-input" value="1" name="agree" />
      <label class="form-check-label" for="same-address"><?= __('store.agree_text') ?></label>
   </div>
</form>
</section>
<section aff-section="confirm_classified_checkout_form">

</section>

{{#product}}
<div class="review-bg mt-4">
 {{#show_product_form}}
 <h4 class="mb-4"><?php echo __('store.review_your_order')?></h4>
 {{/show_product_form}}
 <div class="col-lg-12 white-bg">
    <div class="row"> 
       <div class="col-lg-6"><p>{{product_name}}</p>
       </div>
       <div class="col-lg-6"> 
         <p class="text-right"><strong>{{product_total_price}}</strong></p></div>
      </div>
   </div>
   <div class="col-lg-12 white-bg">
      <div class="row">
         <div class="col-lg-4"><img src="{{product_featured_image}}"  alt="product1" class="style-none">
         </div>
         <div class="col-lg-7">   <p>{{product_description}}</p>
         </div>
      </div>
   </div>

   <div class="col-lg-12 round-img mt-4">
    {{#product_reviews}}
    <div class=" border-style">
      <div class="row">
         <div class="col-lg-12">
            <p>{{comment}}</p>
            <p class="text-right orange"><i>{{name}}</i></p>
         </div></div>
      </div>
      {{/product_reviews}}

   </div>
</div>
{{/product}}
</div>


<div class="col-lg-4 col-md-12 order-md-last">
   <div class="cart-bg">
      {{#show_product_form}}
      <h4 class="d-flex justify-content-between align-items-center mb-3 text-white">
         <?php __('store.your_cart')?>
      </h4>
      {{/show_product_form}}
      <ul class="list-group mb-3">
       {{#product}}
       <li class="list-group-item">
         <img src="{{product_featured_image}}" alt="{{product_name}}">   
         <h6 class="my-0 d-flex justify-content-between lh-sm">{{product_name}}
           <span class="text-muted">{{product_total_price}}</span></h6>
        </li>
        {{/product}}
        {{#totals}}
        <li class="list-group-item d-flex justify-content-between">
         <h6 class="my-0">{{title}}</h6>
         <span  class="text-muted">{{amount}}</span>
      </li>
      {{/totals}}
   </ul>
</div>
<div>
 <img src="<?= base_url('assets/store/classified/classified-checkout/checkout-2/'); ?>img/btn.png" class="img-fluid mt-4 height-img mb-3" alt="btn">
</div>
{{#show_product_form}}
<div class="col-lg-12">
  <a href="#">
   <button type="button" class="btn" aff-button="classified_checkout_form"><?php echo __('store.continue_to_checkout')?></button>
   <input type="hidden" name="checkout_page" value="true">   
   <p class="text-danger" style="display: none;" id="isErrorAgree"><?php echo __('store.the_agree_field_is_required');?></p>  
</a>
</div>                             
{{/show_product_form}}
{{#product.product_checkout_terms}}
<div class="detail mt-5">
   {{{product.product_checkout_terms}}}
</div>
{{/product.product_checkout_terms}}
<div class="guarantee-container">
  <h5><i class="fa fa-lock fa-fw"></i> &nbsp;<?php echo __('store.secure_assured');?></h5>
  <h5><i class="fa fa-certificate fa-fw"></i> &nbsp;<?php echo __('store.money_back_guaranteed');?></h5>
</div>

</div>


</div>     </div>
<footer>
  <div class="legal-footer mt-4">
    <ul>
     <?php  if(isset($products[0]['_meta_product_footer']) && !empty($products[0]['_meta_product_footer'])) {

      $_meta_product_footer = json_decode($products[0]['_meta_product_footer'],true);
      foreach ($_meta_product_footer as $key => $fvalue) { ?>
        <li><a href="#" class="viewData" data-value="<?=$fvalue['description'] ?>"><?=$fvalue['name']?></a></li>
     <?php } }  ?>
  </ul>
</div>
</footer>

</div>
</main>
</script> 


<div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
         <h5 class="modal-title" id="exampleModalLabel"><?php echo __('store.sign_in');?></h5>
         <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row" id="rtcl-user-login-wrapper">
         <div class="col-md-12 rtcl-login-form-wrap login-1">

            <form id="aff-classified-login-form" class="form-horizontal" method="post" novalidate>
               <div class="form-group">
                  <label class="control-label">
                     <?php echo __('store.username');?> <strong class="rtcl-required">*</strong>
                  </label>
                  <input type="text" name="username" autocomplete="username" value="" class="form-control" required>
               </div>
               <div class="form-group">
                  <label class="control-label">
                     <?php echo __('store.password');?> <strong class="rtcl-required">*</strong>
                  </label>
                  <input type="password" name="password" autocomplete="current-password" class="form-control" required>
               </div>

               <div class="form-group d-flex align-items-center mt-4">
                  <button type="submit" class="btn btn-primary btn-sm"><?php echo __('store.login');?></button>
                  <div class="form-check">
                     <input type="checkbox" name="rememberme" id="rtcl-rememberme" value="forever"></div>
                     <label class="form-check-label" for="rtcl-rememberme"> <?php echo __('store.remember_me');?> </label>
                  </div>

               </form>
            </div>
         </div>
      </div>
      <div class="modal-footer">
        <div class="row">
         <div class="col-md-6 float-left"> 
            <a href="<?= base_url('store/forgot') ?>" ><?php echo __('store.forgot_password_?');?></a>
         </div>
         <div class="col-md-6 float-right"><?php echo __('store.new_user');?><a href="<?= base_url('store/register?url=checkout') ?>" > <?php echo __('store.create_a_new_account');?></a>
         </div>
      </div>
   </div>
</div>
</div>
</div>

<!-- Register Modal -->

<div class="modal fade" id="linkModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
         <h5 class="modal-title" id="linktitle"><?php echo __('store.sign_in');?></h5>
         <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row" >
         <div class="col-md-12" id="linkdata">

         </div>
      </div>
   </div>

</div>
</div>
</div>


<script type="text/javascript">
   var country_id = '<?php echo $is_logged==true ? $shipping->country_id:""?>';
   var state_id = '<?php echo $is_logged==true ? $shipping->state_id:""?>';
   var allow_shipping = '<?=$allow_shipping?>';
   
   setTimeout(function(){
     if(country_id!=""){
         $("#country_id").val(country_id);
         $("#country_id").trigger('change')
      }
   },500);

   setTimeout(function(){
      if(state_id!="")
         $("#state_id").val(state_id)
   },1000)

   $(document).ready(function() {
      setTimeout(function(){
         window.tel_input = intlTelInput(document.querySelector("#"+(allow_shipping ? 'phone' :'phoneguest')), {
            initialCountry: "auto",
            utilsScript: "<?= base_url('/assets/plugins/tel/js/utils.js?1562189064761') ?>",
            separateDialCode:true,
            geoIpLookup: function(success, failure) {
               $.get("https://ipinfo.io", function() {}, "jsonp").always(function(resp) {
                 var countryCode = (resp && resp.country) ? resp.country : "";
                 success(countryCode);
              });
            },
         })
      },1000);

      $(document).on('click','.viewData',function(e){
         e.preventDefault();
         $("#linkModal").find('#linktitle').html('');
         $("#linkModal").find('#linkdata').html('');
         $("#linkModal").find('#linktitle').html($(this).text());
         $("#linkModal").find('#linkdata').html($(this).attr('data-value'));
         $("#linkModal").modal('show');
      });
   });

   function isNumberKey(evt) {
     var charCode = (evt.which) ? evt.which : event.keyCode;
       if (charCode != 46 && charCode != 45 && charCode > 31
       && (charCode < 48 || charCode > 57))
        return false;

     return true;
   }

      window.errorMap = ['<?= __('store.invalid_number') ?>','<?= __('store.invalid_country_code') ?>','<?= __('store.too_short') ?>','<?= __('store.too_long') ?>','<?= __('store.invalid_number') ?>', '<?= __('store.mobile_number_is_required') ?>'];

</script>