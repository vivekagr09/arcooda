<link rel="stylesheet" href="<?= base_url('assets/plugins/tel/css/intlTelInput.css') ?>?v=<?= av() ?>">
<script src="<?= base_url('assets/plugins/tel/js/intlTelInput.js') ?>"></script>

<section class="bg-accent section-padding-top-heading">
   <div class="container">
      <div class="row">
         <div class="col-lg-4 col-sm-12 contact-page-box-layout1 light-shadow-bg p-3 mx-auto">
            <div id="main" class="site-content-block">
               <div class="main-content">
                  <div id="post-8" class="post-8 page type-page status-publish">
                     <div class="rtcl">
                        <div class="row" id="rtcl-user-login-wrapper">
                           <div class="col-md-12 rtcl-login-form-wrap login-1">
                              <h2><?= __('store.register') ?></h2>
                              <div class="form-group">
                                 <p class="rtcl-forgot-password">
                                    <?= __('store.already_have_an_account') ?>? <a href="<?= base_url('store/login'); ?>"><?= __('store.login') ?></a>
                                 </p>
                              </div>
                              
                              <form id="aff-classified-registration-form" class="form-horizontal" method="post" novalidate>
                                 <div class="form-group">
                                    <label class="control-label"><?= __('store.first_name') ?></label>
                                    <input class="form-control" name="f_name" type="text">
                                 </div>
                                 
                                 <div class="form-group">
                                    <label class="control-label"><?= __('store.last_name') ?></label>
                                    <input class="form-control" name="l_name" type="text">
                                 </div>
                                 
                                 <div class="form-group">
                                    <label class="control-label"><?= __('store.username') ?></label>
                                    <input class="form-control" name="username" type="text">
                                 </div>
                                 
                                 <input type="hidden" name='PhoneNumberInput' id="phonenumber-input" value="" class="form-control" placeholder="<?= __('store.phone_number') ?>">
                                 <div class="form-group">
                                    <label for=""><?= __('store.phone_number') ?></label>
                                    <div>
                                       <input onkeypress="return isNumberKey(event);" id="phone" type="text" name="phone" value="">
                                    </div>
                                 </div>
                                               
                                 <div class="form-group">
                                    <label class="control-label"><?= __('store.email') ?></label>
                                    <input class="form-control" name="email" type="email">
                                 </div>

                                 <div class="form-group">
                                    <label class="control-label"><?= __('store.password') ?></label>
                                    <input class="form-control" name="password" type="password">
                                 </div>
                                 
                                 <div class="form-group">
                                    <label class="control-label"><?= __('store.confirm_password') ?></label>
                                    <input class="form-control" name="c_password" type="password">
                                 </div>
                                      
                                 <?php 
                                    $db =& get_instance(); 
                                    $googlerecaptcha =$db->Product_model->getSettings('googlerecaptcha');
                                 
                                    if (isset($googlerecaptcha['client_register']) && $googlerecaptcha['client_register']) { 
                                 ?>
                                    <script type="text/javascript"> var grecaptcha_register = 1; </script>
                                    <div class="captch">
                                       <script src='https://www.google.com/recaptcha/api.js'></script>
                                       <div class="g-recaptcha" data-sitekey="<?= $googlerecaptcha['sitekey'] ?>"></div>
                                       <input type="hidden" name="captch_response" id="captch_response_register"> 
                                    </div>
                                 <?php } ?>

                                 <div class="form-group d-flex align-items-center mt-4"> 
                                    <button type="submit" class="submitbtn"><?= __('store.register') ?></button>
                                 </div>
                              </form>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</section>      


<script type="text/javascript">
window.tel_input = intlTelInput(document.querySelector("#phone"), {
   initialCountry: "auto",
   utilsScript: "<?= base_url('/assets/plugins/tel/js/utils.js?1562189064761') ?>",
   separateDialCode:true,
   geoIpLookup: function(success, failure) {
      $.get("https://ipinfo.io", function() {}, "jsonp").always(function(resp) {
         var countryCode = (resp && resp.country) ? resp.country : "US";
         success(countryCode);
      });
   },
});

window.errorMap = ['<?= __('store.invalid_number') ?>','<?= __('store.invalid_country_code') ?>','<?= __('store.too_short') ?>','<?= __('store.too_long') ?>','<?= __('store.invalid_number') ?>', '<?= __('store.mobile_number_is_required') ?>'];

function isNumberKey(evt) {
  var charCode = (evt.which) ? evt.which : event.keyCode;
    if (charCode != 46 && charCode != 45 && charCode > 31
    && (charCode < 48 || charCode > 57))
     return false;

  return true;
}
</script>