<?php
$db =& get_instance();
$products = $db->Product_model;
$cookies_consent = $products->getSettings('site', 'cookies_consent');
$cookies_consent_mesag = $products->getSettings('site', 'cookies_consent_mesag');
?>

<!-- Policy Modal -->
<div class="modal fade" id="termOfUse" tabindex="-1" aria-labelledby="termsOfUseTitle" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="exampleModalLongTitle"><?= $tnc['heading'] ?></h5>
        <button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-label="<?= __('front.close') ?>"></button>
      </div>
      <div class="modal-body">
        <div class="container">
          <div class="row">
            <div class="col-lg-12">
              <p class="modal-text"><?= $tnc['content'] ?></p>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?= __('front.close') ?></button>
      </div>
    </div>
  </div>
</div>
<!-- Policy Modal -->

<!-- About Modal -->
<div class="modal fade" id="about" tabindex="-1" aria-labelledby="aboutTitle" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="aboutTitle"><?= __('front.about') ?></h5>
        <button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-label="<?= __('front.close') ?>"></button>
      </div>
      <div class="modal-body">
        <div class="container">
          <div class="row">
            <div class="col-lg-12">
              <p class="modal-text"><?= $setting['about_content'] ?></p>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?= __('front.close') ?></button>
      </div>
    </div>
  </div>
</div>
<!-- About Modal -->



<?php if (!empty($cookies_consent) && $cookies_consent['cookies_consent'] == 1) : ?>
<!-- Cookie consent popup -->
    <div id="cookie-consent-popup" class="fixed-bottom d-flex justify-content-center align-items-center py-4" style="display: none !important; background-color: rgba(0, 0, 0, 0.8); border-top: 1px solid rgba(255, 255, 255, 0.1);">
        <div class="container">
            <div class="row">
                <div class="col-md-8">
                    <h4 class="mb-2" style="color: #fff; font-weight: bold;">We value your privacy</h4>
                    <p class="mb-0" style="color: #fff; font-size: 16px;">
                    <?php if(!empty($cookies_consent_mesag)){?>
                        <?= @$cookies_consent_mesag['cookies_consent_mesag'];?>
                    <?php }else{?>
                        <?= __('admin.cookies_consent_default_message') ?>
                    <?php }?>
                </p>
                </div>
                <div class="col-md-4 d-flex justify-content-md-end align-items-center">
                    <button id="cookie-consent-accept" class="btn btn-primary me-2" style="font-size: 14px; padding: 8px 16px;">Accept</button>
                    <button id="cookie-consent-decline" class="btn btn-outline-light me-2" style="font-size: 14px; padding: 8px 16px;">Decline</button>
                    <button id="cookie-consent-edit" class="btn btn-outline-light" style="font-size: 14px; padding: 8px 16px;">Edit Preferences</button>
                </div>
            </div>
        </div>
    </div>
<!-- Cookie consent popup -->

<!-- Cookie preferences modal -->
    <div class="modal fade" id="cookie-preferences-modal" tabindex="-1" aria-labelledby="cookiePreferencesLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-secondary text-white">
                    <h5 class="modal-title" id="cookiePreferencesLabel"><?= __('front.cookie_preferences') ?></h5>
                    <button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="cookie-preferences-form">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="cookie1" value="affiliate_id">
                            <label class="form-check-label" for="cookie1"><?= __('front.enable_affiliate_tracking') ?></label>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?= __('front.close') ?></button>
                    <button type="button" class="btn btn-secondary" id="cookie-preferences-save"><?= __('front.save_preferences') ?></button>
                </div>
            </div>
        </div>
    </div>
<!-- Cookie preferences modal -->

<!-- Cookie preferences script -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Check if the user has already given consent
        var data = null;
        try {
            data = localStorage.getItem("selectedCookies");
            if (!data) {
                // If no data is found in localStorage, set default value to true
                data = JSON.stringify({cookie1: true});
                localStorage.setItem("selectedCookies", data);
            }
            data = JSON.parse(data);
        } catch(e) {
            // Handle any potential errors
            console.error('Error retrieving or parsing "selectedCookies"', e);
        }

        const cookieCheckbox = document.getElementById('cookie1');
        if(data && data['cookie1']){
            $("#affiliate_cookie").val(data['cookie1']);
            cookieCheckbox.checked = data['cookie1'];
        }

        if (!localStorage.getItem("cookieConsent")) {
            // If not, show the popup
            document.getElementById("cookie-consent-popup").style.display = "flex";
        }

        // When the user clicks "Accept"
        document.getElementById("cookie-consent-accept").addEventListener("click", function () {
            localStorage.setItem("cookieConsent", "accepted");
            const selectedCookies = { 'cookie1': true };
            localStorage.setItem("selectedCookies", JSON.stringify(selectedCookies));
            cookieCheckbox.checked = true;
            $("#cookie-consent-popup").remove();
        });

        // When the user clicks "Decline"
        document.getElementById("cookie-consent-decline").addEventListener("click", function () {
            localStorage.setItem("cookieConsent", "declined");
            const declinedCookies = { 'cookie1': false };
            localStorage.setItem("selectedCookies", JSON.stringify(declinedCookies));
            const cookieCheckbox = document.getElementById('cookie1');
            cookieCheckbox.checked = false;
            $("#cookie-consent-popup").remove();
            document.cookie = "cookie1=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
        });

        // When the user clicks "Edit Preferences"
        document.getElementById("cookie-consent-edit").addEventListener("click", function () {
            const myModalEl = document.getElementById('cookie-preferences-modal');
            const myModal = new bootstrap.Modal(myModalEl);
            myModal.show();
        });

        // When the user clicks "Save"
        document.getElementById("cookie-preferences-save").addEventListener("click", function () {
            const selectedCookies = {};
            selectedCookies['cookie1'] = cookieCheckbox.checked;

            localStorage.setItem("cookieConsent", "custom");
            localStorage.setItem("selectedCookies", JSON.stringify(selectedCookies));
            const myModalEl = document.getElementById('cookie-preferences-modal');
            const myModal = bootstrap.Modal.getInstance(myModalEl);
            myModal.hide();
        });
    });
</script>
<!-- Cookie preferences script -->
<?php endif; ?>