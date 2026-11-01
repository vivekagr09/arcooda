<section class="bg-accent section-padding-top-heading">
   <div class="container">
      <div class="row">
         <div class="col-lg-4 mx-auto col-sm-12 contact-page-box-layout1 light-shadow-bg p-4">
            <div id="main" class="site-content-block">
               <div class="main-content">
                  <div id="post-8" class="post-8 page type-page status-publish">
                     <div class="rtcl">
                        <div class="row" id="rtcl-user-login-wrapper">
                           <div class="col-md-12 rtcl-login-form-wrap login-1">
                              <h2><?= __('store.sign_in')?></h2>
                              <div class="form-group ">
                                 <p class="rtcl-forgot-password">
                                    <?= __('store.new_user')?><a href="<?= base_url('store/register') ?>"> <?= __('store.create_an_account')?></a>
                                 </p>
                              </div>
                              <form id="aff-classified-login-form" class="form-horizontal" method="post" novalidate>
                                 <div class="form-group">
                                    <label class="control-label">
                                    <?= __('store.username')?> <strong class="rtcl-required">*</strong>
                                    </label>
                                    <input type="text" name="username" autocomplete="username" value="" class="form-control" required>
                                 </div>
                                 <div class="form-group">
                                    <label class="control-label">
                                    <?= __('store.password')?> <strong class="rtcl-required">*</strong>
                                    </label>
                                    <input type="password" name="password" autocomplete="current-password" class="form-control" required>
                                 </div>
                                 <div class="form-group">
                                    <script type="text/javascript">
                                       var grecaptcha = undefined;
                                    </script>
                                    <?php 
                                       $db =& get_instance(); 
                                        $googlerecaptcha =$db->Product_model->getSettings('googlerecaptcha');
                                    ?>
                          
                                    <?php if (isset($googlerecaptcha['client_login']) && $googlerecaptcha['client_login']) { ?>
                                       <div class="captch">
                                          <script src='https://www.google.com/recaptcha/api.js'></script>
                                          <div class="g-recaptcha" data-sitekey="<?= $googlerecaptcha['sitekey'] ?>"></div>
                                          <input type="hidden" name="captch_response" id="captch_response"> 
                                       </div>
                                    <?php } ?>
                                 </div>
                                 <div class="form-group d-flex align-items-center mt-4">
                                    <button type="submit" class="submitbtn"><?= __('store.submit')?></button>
                                    <div class="form-check">
                                       <input type="checkbox" name="rememberme" id="rtcl-rememberme" value="forever"></div>
                                       <label class="form-check-label" for="rtcl-rememberme"> <?= __('store.remember_me')?> </label>
                                    </div>
                                 <div class="form-group ">
                                    <p class="rtcl-forgot-password">
                                       <a  href="<?= base_url('store/forgot') ?>"> <?= __('store.forgot_password')?>?</a>
                                    </p>
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
