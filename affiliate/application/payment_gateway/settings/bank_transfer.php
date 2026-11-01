<div class="mb-3">
    <label class="form-label"><?= __('admin.store_payment_status') ?></label>
    <select class="form-select" name="store">
        <option <?= (int) $setting_data['store']['status'] == '0' ? 'selected' : '' ?> value="0"><?= __('admin.disabled') ?></option>
        <option <?= (int) $setting_data['store']['status'] == '1' ? 'selected' : '' ?> value="1"><?= __('admin.enabled') ?></option>
    </select>
</div>

<div class="mb-3">
    <label class="form-label"><?= __('admin.deposit_payment_status') ?></label>
    <select class="form-select" name="deposit">
        <option <?= (int) $setting_data['deposit']['status'] == '0' ? 'selected' : '' ?> value="0"><?= __('admin.disabled') ?></option>
        <option <?= (int) $setting_data['deposit']['status'] == '1' ? 'selected' : '' ?> value="1"><?= __('admin.enabled') ?></option>
    </select>
</div>

<div class="mb-3">
    <label class="form-label"><?= __('admin.membership_payment_status') ?></label>
    <select class="form-select" name="membership">
        <option <?= (int) $setting_data['membership']['status'] == '0' ? 'selected' : '' ?> value="0"><?= __('admin.disabled') ?></option>
        <option <?= (int) $setting_data['membership']['status'] == '1' ? 'selected' : '' ?> value="1"><?= __('admin.enabled') ?></option>
    </select>
</div>

<div class="mb-3">
    <label class="form-label"><?= __('admin.upload_proof_status') ?></label>
    <select class="form-select" name="proof">
        <option <?= (int) $setting_data['proof'] == '0' ? 'selected' : '' ?> value="0"><?= __('admin.disabled') ?></option>
        <option <?= (int) $setting_data['proof'] == '1' ? 'selected' : '' ?> value="1"><?= __('admin.enabled_and_optional') ?></option>
        <option <?= (int) $setting_data['proof'] == '2' ? 'selected' : '' ?> value="2"><?= __('admin.enabled_and_required') ?></option>
    </select>
</div>

<?php 
    $bank_names = [];

    if (isset($setting_data['bank_names']) && !empty($setting_data['bank_names'])) {
        $bank_names = (array) json_decode($setting_data['bank_names'], 1);
    }
?>

<div class="mb-3">
    <label class="form-label"><?= __('admin.bank_details_1') ?></label>
    <input type="text" class="form-control required" name="bank_names[]" value="<?= isset($bank_names[0]) ? $bank_names[0] : ""; ?>" placeholder="<?= __('admin.bank_details_1') ?>">
    <textarea class="form-control required mt-2" rows="8" name="bank_details"><?= $setting_data['bank_details'] ?></textarea>
</div>

<div class="additional-bank">
    <?php
        if (isset($setting_data['additional_bank_details'])) {
            $additional_bank_details = (array) json_decode($setting_data['additional_bank_details'], 1);
            foreach ($additional_bank_details as $key => $value) {
                echo '<div class="mb-3">';
                echo '  <label class="form-label d-flex justify-content-between">'.__('admin.bank_details').' '.($key + 2).' <span class="text-danger cursor-pointer remove-bank">'.__('admin.remove').'</span></label>';
                ?>
                <input type="text" class="form-control required" name="bank_names[]" value="<?= isset($bank_names[$key + 1]) ? $bank_names[$key + 1] : ""; ?>" placeholder="<?= __('admin.bank_name') ?> <?= ($key + 2) ?>">
                <?php
                echo '  <textarea class="form-control required mt-2" rows="8" name="additional_bank_details[]">'. $value .'</textarea>';
                echo '</div>';
            }
        }
    ?>
</div>

<div class="d-flex justify-content-end">
    <button type="button" class="btn btn-primary btn-add-bank">
        <?= __('admin.add_bank') ?>
    </button>
</div>



<script type="text/javascript">
    // Attach click event to the button with class '.btn-add-bank'
    $(".btn-add-bank").on("click", function() {
        // Get the length of existing additional bank divs
        let blengths = $(".additional-bank > div").length;
        
        // Initialize the HTML to append
        var html = '';
        html += '<div class="mb-3">';
        html += '    <label class="form-label d-flex justify-content-between">Bank Details ' + (blengths + 2) + ' <span class="text-danger cursor-pointer remove-bank">Remove</span></label>';
        html += '    <input type="text" class="form-control required" name="bank_names[]" placeholder="Bank Name ' + (blengths + 2) + '">';
        html += '    <textarea class="form-control required mt-2" rows="8" name="additional_bank_details[]"></textarea>';
        html += '</div>';
        
        // Append the new HTML to the '.additional-bank' div
        $(".additional-bank").append(html);
    });
    
    // Delegate the click event for dynamically generated '.remove-bank' span
    $(".additional-bank").on("click", ".remove-bank", function() {
        // Remove the parent '.mb-3' div (previously '.form-group')
        $(this).parents(".mb-3").remove();
    });
</script>