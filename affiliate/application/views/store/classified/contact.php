

<section aff-section="classified_contact_page"></section>
<script aff-template="classified_contact_page" type="text/html">
   <section class="section-padding-equal-70 bg-accent">
      <div class="container">
         <div class="contact-page-box-layout1 light-shadow-bg col-lg-10 col-sm-12 mx-auto">
            <div class="light-box-content">
               <div class="row">
                  <div class="col-md-6">
                     <div class="contact-info">
                        <h3 class="item-title"><?= __('store.information')?></h3>
                        <ul>
                           <li><i class="fas fa-paper-plane"></i>{{contact_address}}</li>
                           <li><i class="fas fa-phone-volume"></i>{{contact_number}}</li>
                           <li><i class="far fa-envelope"></i>{{contact_email}}</li>
                        </ul>
                        <hr>
                        <div class="pt-2">{{{contact_page_content}}}</div>
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="contact-form-box">
                        <h3 class="item-title"><?= __('store.send_us_a_message')?></h3>
                        <form id="aff-classified-contact-form">
                           <div class="form-group">
                              <input type="text" placeholder="<?= __('store.name')?>" class="form-control" name="name" data-error="Name field is required" required>
                              <div class="help-block with-errors"></div>
                           </div>
                           <div class="form-group">
                              <input type="email" placeholder="<?= __('store.email')?>" class="form-control" name="email" data-error="email field is required" required>
                              <div class="help-block with-errors"></div>
                           </div>
                           <div class="form-group">
                              <input type="text" placeholder="<?= __('store.phone')?>" class="form-control" name="phone" data-error="Phone field is required" required>
                              <div class="help-block with-errors"></div>
                           </div>
                           <div class="form-group">
                              <textarea placeholder="<?= __('store.message')?>" class="textarea form-control" name="message" id="form-message" rows="3" cols="20" data-error="Message field is required" required></textarea>
                              <div class="help-block with-errors"></div>
                           </div>

                           <?php 
                              $db =& get_instance(); 
                               $googlerecaptcha =$db->Product_model->getSettings('googlerecaptcha');
                           ?>
                 
                           <?php if (isset($googlerecaptcha['client_login']) && $googlerecaptcha['client_login']) { ?>
                              <div class="form-group captch">

                                 <div class="g-recaptcha" data-sitekey="<?= $googlerecaptcha['sitekey'] ?>"></div>
                                 <input type="hidden" name="captch_response" id="captch_response"> 
                              </div>
                           <?php } ?>
                           <div class="form-group">
                              <button type="submit" class="submit-btn"><?= __('store.submit')?></button>
                           </div>
                           <div class="form-response"></div>
                        </form>
                     </div>
                  </div>
               </div>
            </div>
            <div class="item-review light-shadow-bg contact-map-area">
               {{{contact_page_map}}}
            </div>
         </div>
      </div>
   </section>
   <script src='https://www.google.com/recaptcha/api.js' async defer></script>
</script>
