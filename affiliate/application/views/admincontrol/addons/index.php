<?php if(false){ ?>
  <div class="page-content-wrapper">
    <div class="container_">
      <?php echo $doc_config['content']; ?>
    </div>
  </div>
<?php } ?>  

<div class="addon-module-switcher">

  <div class="card bg-secondary mb-3">
    <div class="card-header text-white <?= ((int)$mlm_admin_is_enable > 0) ? "bg-info" : ""; ?>"><?= __('admin.mlm_admin') ?>
    </div>
    <div class="card-body">
      <label class="form-check form-switch">
        <input class="form-check-input activity" type="checkbox" data-setting_type="referlevel" data-setting_key="status" data-sidebar="mlm" <?= ((int)$mlm_admin_is_enable > 0) ? "checked" : ""; ?>>
        <span class="form-check-label">Enable</span>
      </label>
    </div>
  </div>

  <div class="card bg-secondary mb-3">
    <div class="card-header text-white <?= ((int)$mlm_vendor_is_enable > 0) ? "bg-info" : ""; ?>"><?= __('admin.mlm_vendor') ?></div>
    <div class="card-body">
      <label class="form-check form-switch">
        <input class="form-check-input activity" type="checkbox" data-setting_type="market_vendor" data-setting_key="vendormlmmodule" data-sidebar="vendormlmmodule" <?= ((int)$mlm_vendor_is_enable > 0) ? "checked" : ""; ?>>
        <span class="form-check-label">Enable</span>
      </label>
    </div>
  </div>

  <!--SaaS module-->
  <div class="card bg-secondary mb-3">
    <div class="card-header text-white <?= ((int)$saas_is_enable > 0) ? "bg-info" : ""; ?>"><?= __('admin.saas_module') ?></div>
    <div class="card-body">
      <label class="form-check form-switch">
        <input class="form-check-input activity" type="checkbox" data-setting_type="market_vendor" data-setting_key="marketvendorstatus" data-sidebar="saas" <?= ((int)$saas_is_enable > 0) ? "checked" : ""; ?>>
        <span class="form-check-label">Enable</span>
      </label>
    </div>
  </div>

<!--Store module-->
  <div class="card bg-secondary mb-3">
    <div class="card-header text-white <?= ((int)$store_is_enable > 0) ? "bg-info" : ""; ?>"><?= __('admin.store_module') ?></div>
    <div class="card-body">
      <label class="form-check form-switch">
        <input class="form-check-input activity" type="checkbox" data-setting_type="store" data-setting_key="status" data-sidebar="store" <?= ((int)$store_is_enable > 0) ? "checked" : ""; ?>>
        <span class="form-check-label">Enable</span>
      </label>
    </div>
  </div>

  <!--Membership module-->
  <div class="card bg-secondary mb-3">
    <div class="card-header text-white <?= ((int)$membership_is_enable > 0) ? "bg-info" : ""; ?>"><?= __('admin.membership_module') ?></div>
    <div class="card-body">
      <label class="form-check form-switch">
        <input class="form-check-input activity" type="checkbox" data-setting_type="membership" data-setting_key="status" data-sidebar="membership" <?= ((int)$membership_is_enable > 0) ? "checked" : ""; ?>>
        <span class="form-check-label">Enable</span>
      </label>
    </div>
  </div>

  <div class="card bg-secondary mb-3">
    <div class="card-header text-white <?= ((int)$vendor_deposit_is_enable > 0) ? "bg-info" : ""; ?>"><?= __('admin.vendor_deposit_module') ?></div>
    <div class="card-body">
      <label class="form-check form-switch">
        <input class="form-check-input activity" type="checkbox" data-setting_type="vendor" data-setting_key="depositstatus" data-sidebar="vendor" <?= ((int)$vendor_deposit_is_enable > 0) ? "checked" : ""; ?>>
        <span class="form-check-label">Enable</span>
      </label>
    </div>
  </div>

  <div class="card bg-secondary mb-3">
    <div class="card-header text-white <?= ((int)$award_level_is_enable > 0) ? "bg-info" : ""; ?>"><?= __('admin.award_level') ?></div>
    <div class="card-body">
      <label class="form-check form-switch">
        <input class="form-check-input activity" type="checkbox" data-setting_type="award_level" data-setting_key="status" data-sidebar="award_level" <?= ((int)$award_level_is_enable > 0) ? "checked" : ""; ?>>
        <span class="form-check-label">Enable</span>
      </label>
    </div>
  </div>
</div>

<div class="row addons-common">
  <div class="col-md-2">
    <div class="card bg-info mb-3">
      <div class="card-header text-white"><?= __( 'admin.menu_settings') ?></div>
      <div class="card-body">
        <a href="<?= base_url('admincontrol/paymentsetting') ?>" target="_blank" role="button" class="btn btn-primary btn-sm"><?php echo __( 'admin.go_to_module') ?></a>
      </div>
    </div>
  </div>

  <div class="col-md-2">
    <div class="card bg-info mb-3">
      <div class="card-header text-white"><?= __( 'admin.language') ?></div>
      <div class="card-body">
        <a href="<?= base_url('admincontrol/language') ?>" target="_blank" role="button" class="btn btn-primary btn-sm"><?= __( 'admin.go_to_module') ?></a>
      </div>
    </div>
  </div>

  <div class="col-md-2">
    <div class="card bg-info mb-3">
      <div class="card-header text-white"><?= __( 'admin.currency') ?></div>
      <div class="card-body">
        <a href="<?= base_url('admincontrol/currency_list') ?>" target="_blank" role="button" class="btn btn-primary btn-sm"><?php echo __( 'admin.go_to_module') ?></a>
      </div>
    </div>
  </div>

  <div class="col-md-2">
    <div class="card bg-info mb-3">
      <div class="card-header text-white"><?= __('admin.mail_templates') ?></div>
      <div class="card-body">
        <a href="<?= base_url('admincontrol/mails') ?>" target="_blank" role="button" class="btn btn-primary btn-sm"><?php echo __( 'admin.go_to_module') ?></a>
      </div>
    </div>
  </div>

  <div class="col-md-2">
    <div class="card bg-info mb-3">
      <div class="card-header text-white"><?= __( 'admin.registration_form') ?></div>
      <div class="card-body">
        <a href="<?= base_url('admincontrol/registration_builder') ?>" target="_blank" role="button" class="btn btn-primary btn-sm"><?php echo __( 'admin.go_to_module') ?></a>
      </div>
    </div>
  </div>

  <div class="col-md-2">
    <div class="card bg-info mb-3">
      <div class="card-header text-white"><?= __( 'admin.backups') ?></div>
      <div class="card-body">
        <a href="<?= base_url('admincontrol/backup') ?>" target="_blank" role="button" class="btn btn-primary btn-sm"><?= __( 'admin.go_to_module') ?></a>
      </div>
    </div>
  </div>
</div>

<div class="row addons-common">
  <div class="col-md-2">
    <div class="card text-white bg-info mb-3">
      <div class="card-header"><?= __( 'admin.countries_and_states') ?></div>
      <div class="card-body">
        <a href="<?= base_url('admincontrol/countries_and_states') ?>" target="_blank" role="button" class="btn btn-primary btn-sm"><?php echo __( 'admin.go_to_module') ?></a>
      </div>
    </div>
  </div>

  <div class="col-md-2">
    <div class="card text-white bg-info mb-3">
      <div class="card-header"><?= __( 'admin.cron_job') ?></div>
      <div class="card-body">
        <a href="<?= base_url('admincontrol/cron') ?>" target="_blank" role="button" class="btn btn-primary btn-sm"><?php echo __( 'admin.go_to_module') ?></a>
      </div>
    </div>
  </div>

  <div class="col-md-2">
    <div class="card text-white bg-info mb-3">
      <div class="card-header"><?= __( 'admin.menu_to_do_list') ?></div>
      <div class="card-body">
        <a href="<?= base_url('admincontrol/todolist') ?>" target="_blank" role="button" class="btn btn-primary btn-sm"><?php echo __( 'admin.go_to_module') ?></a>
      </div>
    </div>
  </div>

  <div class="col-md-2">
    <div class="card text-white bg-info mb-3">
      <div class="card-header"><?= __( 'admin.menu_tickets') ?></div>
      <div class="card-body">
        <a href="<?= base_url('admincontrol/tickets') ?>" target="_blank" role="button" class="btn btn-primary btn-sm"><?php echo __( 'admin.go_to_module') ?></a>
      </div>
    </div>
  </div>

  <div class="col-md-2">
    <div class="card text-white bg-info mb-3">
      <div class="card-header"><?= __('admin.user_api') ?></div>
      <div class="card-body">
        <a href="<?= base_url('api-document') ?>" target="_blank" role="button" class="btn btn-primary btn-sm"><?php echo __( 'admin.go_to_module') ?></a>
      </div>
    </div>
  </div>

  <div class="col-md-2">
    <div class="card text-white bg-info mb-3">
      <div class="card-header"><?= __('admin.system_update_logs') ?></div>
      <div class="card-body">
        <a href="<?= base_url('debug/sysupdatereport') ?>" target="_blank" role="button" class="btn btn-primary btn-sm"><?php echo __( 'admin.go_to_module') ?></a>
      </div>
    </div>
  </div>
</div>

<div class="row g-3 addons-common">

  <div class="col-md-2">
    <div class="card bg-info text-white">
      <div class="card-header"><?= __('admin.tutorial') ?></div>
      <div class="card-body">
        <a href="<?= base_url('admincontrol/tutorial') ?>" target="_blank" role="button" class="btn btn-primary btn-sm"><?php echo __( 'admin.go_to_module') ?></a>
      </div>
    </div>
  </div>

  <div class="col-md-2">
    <div class="card bg-info text-white">
      <div class="card-header"><?= __('admin.troubleshoot') ?></div>
      <div class="card-body">
        <a href="<?= base_url('admincontrol/troubleshoot') ?>" target="_blank" role="button" class="btn btn-primary btn-sm"><?php echo __( 'admin.go_to_module') ?></a>
      </div>
    </div>
  </div>

  <div class="col-md-2">
    <div class="card bg-info text-white">
      <div class="card-header"><?= __('admin.affiliate_theme') ?></div>
      <div class="card-body">
        <a href="<?= base_url('admincontrol/affiliate_theme') ?>" target="_blank" role="button" class="btn btn-primary btn-sm"><?php echo __( 'admin.go_to_module') ?></a>
      </div>
    </div>
  </div>

  <div class="col-md-2">
    <div class="card bg-info text-white">
      <div class="card-header"><?= __('admin.security') ?></div>
      <div class="card-body">
        <a href="<?= base_url('admincontrol/paymentsetting#security') ?>" target="_blank" role="button" class="btn btn-primary btn-sm"><?php echo __( 'admin.go_to_module') ?></a>
      </div>
    </div>
  </div>

  <div class="col-md-2">
    <div class="card bg-info text-white">
      <div class="card-header"><?= __('admin.user_dashboard') ?></div>
      <div class="card-body">
        <a href="<?= base_url('admincontrol/paymentsetting#user-dashboard-setting') ?>" target="_blank" role="button" class="btn btn-primary btn-sm"><?php echo __( 'admin.go_to_module') ?></a>
      </div>
    </div>
  </div>

  <div class="col-md-2">
    <div class="card bg-info text-white">
      <div class="card-header"><?= __('admin.cookies_tracking') ?></div>
      <div class="card-body">
        <a href="<?= base_url('admincontrol/paymentsetting#tracking') ?>" target="_blank" role="button" class="btn btn-primary btn-sm"><?php echo __( 'admin.go_to_module') ?></a>
      </div>
    </div>
  </div>

</div>


<div class="row addons-common">
  <div class="col-md-2 mb-3">
    <div class="card bg-info text-white">
      <div class="card-header"><?= __('admin.award_level') ?></div>
      <div class="card-body">
        <a href="<?= base_url('admincontrol/award_level') ?>" target="_blank" role="button" class="btn btn-primary btn-sm"><?php echo __( 'admin.go_to_module') ?></a>
      </div>
    </div>
  </div>

  <div class="col-md-2 mb-3">
    <div class="card bg-info text-white">
      <div class="card-header"><?= __( 'admin.update_version') ?></div>
      <div class="card-body">
        <a href="<?= base_url('admincontrol/install_new_version') ?>" target="_blank" role="button" class="btn btn-primary btn-sm"><?php echo __( 'admin.go_to_module') ?></a>
      </div>
    </div>
  </div>

  <div class="col-md-2 mb-3">
    <div class="card bg-info text-white">
      <div class="card-header"><?= __( 'admin.system_status') ?></div>
      <div class="card-body">
        <a href="<?= base_url('admincontrol/system_status') ?>" target="_blank" role="button" class="btn btn-primary btn-sm"><?php echo __( 'admin.go_to_module') ?></a>
      </div>
    </div>
  </div>

  <div class="col-md-2 mb-3">
    <div class="card bg-info text-white">
      <div class="card-header"><?= __( 'admin.system_license') ?></div>
      <div class="card-body">
        <a href="<?= base_url('admincontrol/script_details') ?>" target="_blank" role="button" class="btn btn-primary btn-sm"><?php echo __( 'admin.go_to_module') ?></a>
      </div>
    </div>
  </div>

  <div class="col-md-2 mb-3">
    <div class="card bg-info text-white">
      <div class="card-header"><?= __( 'admin.admin_user_theme') ?></div>
      <div class="card-body">
        <a href="<?= base_url('admincontrol/paymentsetting') ?>" target="_blank" role="button" class="btn btn-primary btn-sm"><?php echo __( 'admin.go_to_module') ?></a>
      </div>
    </div>
  </div>

  <div class="col-md-2 mb-3">
    <div class="card bg-info text-white">
      <div class="card-header"><?= __('admin.first_settings') ?></div>
      <div class="card-body">
        <a href="<?= base_url('firstsetting') ?>" target="_blank" role="button" class="btn btn-primary btn-sm"><?php echo __( 'admin.go_to_module') ?></a>
      </div>
    </div>
  </div>
</div>

<?= $integration_modules_view; ?>

<script type="text/javascript">
  $("input[data-setting_key='depositstatus']").on('change',function(){
    if($(this).is(':checked')){
      Swal.fire({
        icon: 'info',
        text: "<?= __('admin.vendor_deposit_on_message')  ?>",
      })
    } else {
      Swal.fire({
        icon: 'warning',
        text: "<?= __('admin.vendor_deposit_off_message')  ?>",
      }) 
    }
  })

  $(document).on('change', '.activity', function(){
    let setting_type = $(this).data('setting_type');
    let setting_key = $(this).data('setting_key');
    let val = $(this).prop('checked') ? 1 : 0;
    
    let menu =  $(this).data('sidebar');

    if(val) {
      $('#sidebar_'+menu).show();
      $(this).closest('.card').find('.card-header').addClass('bg-info');
    } else {
      $('#sidebar_'+menu).hide();
      $(this).closest('.card').find('.card-header').removeClass('bg-info');
    }

    $.ajax({
      type: "POST",
      data: {
        action: 'change_status', 
        setting_type: setting_type, 
        setting_key : setting_key, 
        val : val
      },
      success: function(res){
      },
    });
  });

  // Dynamically set background color of card headers based on checkbox values
  $('.activity').each(function() {
    var cardHeader = $(this).closest('.card').find('.card-header');
    if ($(this).is(':checked')) {
      cardHeader.addClass('bg-info');
    } else {
      cardHeader.removeClass('bg-info');
    }
  });
</script>
