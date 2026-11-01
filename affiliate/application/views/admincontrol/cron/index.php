<div class="row">
  <div class="col-12">
    <div class="cron-title mb-4">
      <i class="bi bi-journal-check"></i> <?= __('admin.cron_job') ?>
    </div>

    <div class="card m-b-30">
      <div class="card-body">
        <label class="cron-desc"><i class="bi bi-dot"></i> <?= __('admin.wallet_transactions_cron_job') ?></label>
        <div class="cron-div">
          <div class="copy trans-copy" onclick="copyText('cron-trans')"><span><?= __('admin.copy') ?></span></div>
          <input type="text" class="mt-4 cron-input" id="cron-trans" name="" value="curl <?= base_url('/cronJob/transaction') ?>">
        </div>
      </div>
    </div>

    <div class="card m-b-30">
      <div class="card-body">
        <label class="cron-desc"><i class="bi bi-dot"></i> <?= __('admin.auto_process_in_wallet_transaction_cron_job') ?></label>
        <div class="cron-div">
          <div class="copy auto-wallet-copy" onclick="copyText('cron-auto-wallet')"><span><?= __('admin.copy') ?></span></div>
          <input type="text" class="mt-4 cron-input" id="cron-auto-wallet" name="" value="curl <?= base_url('/cronJob/autoProcessInWalletTransactions') ?>">
        </div>
      </div>
    </div>

    <div class="card m-b-30">
      <div class="card-body">
        <label class="cron-desc"><i class="bi bi-dot"></i> <?= __('admin.campaigns_validator_cron_job') ?></label>
        <div class="cron-div">
          <div class="copy camp-copy" onclick="copyText('cron-camp')"><span><?= __('admin.copy') ?></span></div>
          <input type="text" class="mt-4 cron-input" id="cron-camp" name="" value="curl <?= base_url('/cronJob/check_campaign_security') ?>">
        </div>
      </div>
    </div>

    <div class="card m-b-30">
      <div class="card-body">
        <label class="cron-desc"><i class="bi bi-dot"></i> <?= __('admin.expire_package_notification') ?></label>
        <div class="cron-div">
          <div class="copy expi-copy" onclick="copyText('cron-expi')"><span><?= __('admin.copy') ?></span></div>
          <input type="text" class="mt-4 cron-input" id="cron-expi" name="" value="curl <?= base_url('/cronJob/expire_package_notification') ?>">
        </div>
      </div>
    </div>

    <div class="card m-b-30">
      <div class="card-body">
        <label class="cron-desc"><i class="bi bi-dot"></i> <?= __('admin.check_vendor_limit') ?></label>
        <div class="cron-div">
          <div class="copy ven-copy" onclick="copyText('cron-ven')"><span><?= __('admin.copy') ?></span></div>
          <input type="text" class="mt-4 cron-input" id="cron-ven" name="" value="curl <?= base_url('/cronJob/check_ven_limitation') ?>">
        </div>
      </div>
    </div>

    <div class="card m-b-30">
      <div class="card-body">
        <label class="cron-desc"><i class="bi bi-dot"></i> <?= __('admin.check_award_level') ?></label>
        <div class="cron-div">
          <div class="copy ven-copy" onclick="copyText('cron-award')"><span><?= __('admin.copy') ?></span></div>
          <input type="text" class="mt-4 cron-input" id="cron-award" name="" value="curl <?= base_url('/cronJob/check_award_level') ?>">
        </div>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
  function copyText(elementId) {
    var textBox = document.getElementById(elementId);
    var linkValue = textBox.value.replace(/^curl\s*/, ''); // Remove "curl" prefix from the value
    textBox.value = linkValue; // Update the input field value without the "curl" prefix
    textBox.focus(); // Focus on the input field
    textBox.select(); // Select the input field contents

    try {
      document.execCommand("copy"); // Copy the selected contents to the clipboard
      var copyButton = document.querySelector("#" + elementId).previousElementSibling;
      copyButton.querySelector("span").innerText = "Copied!";
      setTimeout(function(){
        textBox.value = 'curl ' + linkValue; // Restore the "curl" prefix
        textBox.setSelectionRange(0, 0); // Clear the selection range
        textBox.blur(); // Remove focus from the input field
        copyButton.querySelector("span").innerText = "Copy";
      }, 1000);
    } catch (error) {
      console.error('Error copying text: ', error);
    }
  }
</script>


