<!-- Contact start -->
<section class="bg-accent section-padding-top-heading">
   <div class="container">
      <div class="row">
         <div class="col-lg-5 mx-auto col-sm-12 contact-page-box-layout1 light-shadow-bg p-4">
            <div id="main" class="site-content-block">
               <div class="main-content">
                  <div id="post-8" class="post-8 page type-page status-publish">
                     <div class="rtcl">
                        <div class="row" id="rtcl-user-login-wrapper">
                           <div class="col-md-12 rtcl-login-form-wrap login-1">
                              <h2><?= __('store.update_your_password')?></h2>
                              <div class="form-group ">
                                 <p class="rtcl-forgot-password">
                                    <?= __('store.enter_email_and_process')?>
                                 </p>
                              </div>
                              <form id="aff-classified-forgot-form" class="form-horizontal" method="post" novalidate>
                                 <div class="form-group">
                                    <label class="control-label">
                                       <?= __('store.username_or_email')?> <strong class="rtcl-required">*</strong>
                                    </label>
                                    <input type="text" name="forgot_email" class="form-control" required />
                                 </div>
                                 
                                 <div class="form-group">
                                    <div id="rtcl-login-g-recaptcha" class="mb-2"></div>
                                    <div id="rtcl-login-g-recaptcha-message"></div>
                                 </div>
                                 <div class="form-group d-flex align-items-center mt-4">
                                    <button type="submit" class="submitbtn"><?= __('store.send_email')?>
                                 </button>
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