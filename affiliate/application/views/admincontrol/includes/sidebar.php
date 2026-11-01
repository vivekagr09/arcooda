<?php
  $db =& get_instance();
  $products = $db->Product_model;
  $store_setting =$db->Product_model->getSettings('store');
  $userdetails=$db->Product_model->userdetails();
  $license = $products->getLicese();
  $notifications = $products->getnotificationnew('admin',null,5);
  $notifications_count = $products->getnotificationnew_count('admin',null);
  $referlevel_status = $this->Product_model->getSettings('referlevel', 'status');

  $market_vendor_marketvendorstatus = $this->Product_model->getSettings('market_vendor', 'marketvendorstatus');
  $vendor_storestatus = $this->Product_model->getSettings('vendor', 'storestatus');
  $market_vendor_marketvendorstatus =  isset($market_vendor_marketvendorstatus['marketvendorstatus']) ? $market_vendor_marketvendorstatus['marketvendorstatus'] : 0;
  $vendor_storestatus =  isset($vendor_storestatus['storestatus']) ? $vendor_storestatus['storestatus'] : 0;
  
  $membership_status = $this->Product_model->getSettings('membership', 'status');
  $store_status = $this->Product_model->getSettings('store', 'status');
  $market_tools_status = $this->Product_model->getSettings('market_tools', 'status');
  $award_level_status = $this->Product_model->getSettings('award_level','status');
  $admin_side_bar_text_color = $this->Product_model->getSettings('theme','admin_side_bar_text_color');
  $admin_side_bar_text_hover_color = $this->Product_model->getSettings('theme','admin_side_bar_text_hover_color');
  
  $sidebar_data = array (
      'mlm_is_enable' => isset($referlevel_status['status']) ? $referlevel_status['status'] : 0,
      'saas_is_enable' => ($market_vendor_marketvendorstatus == 1 || $vendor_storestatus == 1) ? 1 : 0,
      'membership_is_enable' => isset($membership_status['status']) ? $membership_status['status'] : 0,
      'store_is_enable' => isset($store_status['status']) ? $store_status['status'] : 0,
      'award_level_is_enable' => isset($award_level_status['status']) ? $award_level_status['status'] : 0,
      'market_tools_is_enable' => isset($market_tools_status['status']) ? $market_tools_status['status'] : 1,
  );
?> 
<style type="text/css">
  .menu-title {
    color: <?= $admin_side_bar_text_color['admin_side_bar_text_color'] ?>;
  }
  .left-menu ul>li>.dropdown-menu a {
    color: <?= $admin_side_bar_text_color['admin_side_bar_text_color'] ?>;
    font-size: 0.875rem;
    font-weight: 400;
    position: relative;
    margin: 10px 0px 18px 0;
    padding: 0 0 0 4rem;
  }
  .left-menu ul>li>.dropdown-menu a:hover {
    color: <?= $admin_side_bar_text_hover_color['admin_side_bar_text_hover_color'] ?>;
  }
  .left-menu ul>li>.dropdown-menu a.active {
    background-color: unset;
    color: <?= $admin_side_bar_text_hover_color['admin_side_bar_text_hover_color'] ?>;
  }
</style>

<div class="left-menu sidebar sidebar-offcanvas admin_side_bar_color">
        <div class="admin-balance">
            <div class="profile-image">
                 <?php $login_user_profile_avatar = (!empty($userdetails['avatar'])) ? base_url('assets/images/users/'.$userdetails['avatar']) : base_url('assets/vertical/assets/images/no-image.jpg'); ?> 
                <img src="<?= $login_user_profile_avatar; ?>">
            </div>
              <div class="profile-name">
              <p class="name" style="color: <?= $admin_side_bar_text_color['admin_side_bar_text_color'] ?>;"><?= __('admin.menu_welcome') ?>! <?= $this->session->userdata('administrator')['firstname'].' '.$this->session->userdata('administrator')['lastname'] ?></p>
              <p class="designation" style="color: <?= $admin_side_bar_text_color['admin_side_bar_text_color'] ?>;"><?= __('admin.super_admin') ?></p>
            </div>
        </div>
      <div class="scroll-bar d-block sidebar">
            <ul class="navbar-nav scroll-wrap navbar-height admin_side_bar_color">
            <li class="nav-item dropdown">
              <a class="nav-link d-flex" href="
                <?= base_url('admincontrol/dashboard') ?>">
                <div class="color-blue">
                  <i class="fas fa-home"></i>
                  <span class="menu-title" style="color: <?= $admin_side_bar_text_color['admin_side_bar_text_color'] ?>;"><?= __('admin.menu_dashboard') ?> </span>
                </div>
              </a>
            </li>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown" href="javascript:void(0);" aria-haspopup="dropdown" data-bs-toggle="dropdown">
                <div>
                  <i class="fas fa-info-circle menu-icon"></i>
                 <span class="menu-title" style="color: <?= $admin_side_bar_text_color['admin_side_bar_text_color'] ?>;"> <?= __('admin.useful_links') ?></span>
                </div>
                <div>
                  <i class="fas fa-angle-right"></i>
                </div>
              </a>
              <div class="dropdown-menu">
                <a class="dropdown-item" href="<?= base_url('admincontrol/paymentsetting') ?>">
                  <i class="bi bi-gear"></i>
                  <?= __('admin.menu_settings') ?></a>

                <a class="dropdown-item" href="<?= base_url('admincontrol/addons') ?>">
                  <i class="bi bi-plugin"></i>
                  <?= __('admin.menu_addons') ?></a>

                <a class="dropdown-item" <?= $sidebar_data['award_level_is_enable'] == 0 ? 'style="display:none;"' : ''; ?> href="<?= base_url('admincontrol/award_level') ?>">
                  <i class="bi bi-award"></i>
                  <?= __('admin.award_level') ?></a>

                <a class="dropdown-item" href="<?= base_url('admincontrol/affiliate_theme') ?>">
                  <i class="bi bi-palette"></i>
                  <?= __('admin.affiliate_theme') ?></a>

                <a class="dropdown-item" href="<?= base_url('admincontrol/language') ?>">
                  <i class="bi bi-translate"></i>
                  <?= __('admin.menu_language') ?></a>

                <a class="dropdown-item" href="<?= base_url('admincontrol/currency_list') ?>">
                  <i class="bi bi-coin"></i>
                  <?= __('admin.menu_currency') ?></a>

                <a class="dropdown-item" href="<?= base_url('admincontrol/system_status') ?>">
                  <i class="bi bi-life-preserver"></i>
                  <?= __('admin.server') ?></a>

                <a class="dropdown-item" href="<?= base_url('admincontrol/cron') ?>">
                  <i class="bi bi-folder-symlink"></i>
                  <?= __('admin.cron_job') ?></a>

                <a class="dropdown-item" href="<?= base_url('admincontrol/todolist') ?>">
                  <i class="bi bi-check2-all"></i>
                  <?= __('admin.menu_to_do_list') ?></a>

                <a class="dropdown-item" href="<?= base_url('admincontrol/tickets') ?>">
                  <i class="bi bi-ticket"></i>
                  <?= __('admin.menu_tickets') ?></a>

                <a class="dropdown-item" href="<?= base_url('admincontrol/countries_and_states') ?>">
                  <i class="bi bi-flag"></i>
                  <?= __('admin.countries_and_states') ?></a>

                <a class="dropdown-item" target= "_blank" href="<?= base_url('/api-document') ?>">
                  <i class="bi bi-file-code"></i>
                  <?= __('admin.user_api') ?></a>

                <a class="dropdown-item" href="<?= base_url('admincontrol/tutorial') ?>">
                  <i class="bi bi-file-person"></i>
                  <?= __('admin.tutorial') ?></a>

                <a class="dropdown-item" target="_blank" href="<?= base_url('debug/sysupdatereport') ?>">
                  <i class="bi bi-flag"></i>
                  <?= __('admin.system_update_logs') ?></a>

                <a class="dropdown-item" href="<?= base_url('admincontrol/troubleshoot') ?>">
                  <i class="bi bi-tropical-storm"></i>
                  <?= __('admin.troubleshoot') ?></a>

                <a class="dropdown-item" target="_blank" href="<?= base_url('/sitemap.xml') ?>">
                  <i class="bi bi-map"></i>
                  <?= __('admin.sitemap') ?></a>
              </div>
            </li>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown" href="javascript:void(0);" aria-haspopup="dropdown" data-bs-toggle="dropdown">
                <div>
                  <i class="fas fa-credit-card"></i>
                  <span class="menu-title" style="color: <?= $admin_side_bar_text_color['admin_side_bar_text_color'] ?>;"><?= __('admin.payments_system') ?></span>
                </div>
                <div>
                 <i class="fas fa-angle-right"></i>
                </div>
              </a>
              <div class="dropdown-menu">
                <a class="dropdown-item" href="<?= base_url('admincontrol/payment_gateway') ?>">
                  <i class="fas fa-sliders-h"></i>
                  <?= __('admin.payment_gateways') ?>
                </a>
                <a class="dropdown-item" href="<?= base_url('admincontrol/all_transaction') ?>">
                  <i class="fas fa-sliders-h"></i> 
                  <?= __('admin.menu_all_transactions') ?>
                </a>
                <a class="dropdown-item" href="<?= base_url('admincontrol/uncompleted_payments') ?>">
                  <i class="fas fa-sliders-h"></i> 
                  <?= __('admin.menu_uncompleted_payments') ?>
                </a>
              </div>
            </li>
             <li class="nav-item dropdown">
              <a class="nav-link dropdown" href="javascript:void(0);" data-bs-toggle="dropdown">
                <div>
                  <i class="fas fa-users"></i>
                  <span class="menu-title" style="color: <?= $admin_side_bar_text_color['admin_side_bar_text_color'] ?>;"><?= __('admin.menu_members') ?></span>
                </div>
                <div>
                  <i class="fas fa-angle-right"></i>
                </div>
              </a>
              <div class="dropdown-menu">
                <a class="dropdown-item" href="<?= base_url('admincontrol/userslist') ?>">
                  <i class="fas fa-sliders-h"></i>
                  <?= __('admin.menu_list_affiliates') ?>
                </a>
                <a class="dropdown-item" href="<?= base_url('admincontrol/usergroup') ?>">
                  <i class="fas fa-sliders-h"></i>
                  <?= __('admin.menu_user_group') ?>
                </a>
                <a class="dropdown-item" href="<?= base_url('admincontrol/userslisttree') ?>">
                  <i class="fas fa-sliders-h"></i>
                  <?= __('admin.menu_referring_tree') ?>
                </a>
                <a class="dropdown-item" href="<?= base_url('admincontrol/userslistmail') ?>">
                  <i class="fas fa-sliders-h"></i>
                  <?= __('admin.menu_list_affiliates_email') ?>
                </a> <?php if($userdetails['id'] == 1){ ?>
                  <a class="dropdown-item" href="<?= base_url('admincontrol/admin_user') ?>">
                    <i class="fas fa-sliders-h"></i>
                    <?= __('admin.menu_manage_admin') ?>
                  </a> 
                <?php } ?>
              </div>
            </li>
            <li id="sidebar_store" class="nav-item dropdown" <?= $sidebar_data['store_is_enable'] == 0 ? 'style="display:none;"' : ''; ?>>
              <a class="nav-link dropdown" href="javascript:void(0);" data-bs-toggle="dropdown">
                <div>
                  <i class="fas fa-shopping-bag"></i>
                  <span class="menu-title" style="color: <?= $admin_side_bar_text_color['admin_side_bar_text_color'] ?>;">
                    <?= __('admin.menu_my_store') ?>
                  </span>
                </div>
                <div>
                  <i class="fas fa-angle-right"></i>
                </div>
              </a>
              <div class="dropdown-menu">
                <a class="dropdown-item" href="<?= base_url('admincontrol/store_dashboard') ?>">
                  <i class="fas fa-sliders-h"></i>
                  <?= __('admin.store_home') ?>
                </a>
                <a class="dropdown-item" href="<?= base_url('admincontrol/store_setting') ?>">
                  <i class="fas fa-sliders-h"></i>
                  <?= __('admin.store_settings') ?>
                </a>
                <a class="dropdown-item" href="<?= base_url('admincontrol/listproduct') ?>">
                  <i class="fas fa-sliders-h"></i>
                  <?= __('admin.store_cart_products') ?>
                </a>
                <a class="dropdown-item" href="<?= base_url('Productsales/index') ?>">
                  <i class="fas fa-sliders-h"></i>
                  <?= __('admin.store_sales_products') ?>
                </a>
                <a class="dropdown-item" href="<?= base_url('admincontrol/listproduct/reviews') ?>">
                  <i class="fas fa-sliders-h"></i>
                  <?= __('admin.store_v_requests') ?>
                </a>
                <a class="dropdown-item" href="<?= base_url('admincontrol/store_category') ?>">
                  <i class="fas fa-sliders-h"></i>
                  <?= __('admin.store_categories') ?>
                </a>
                <a class="dropdown-item" href="<?= base_url('admincontrol/listorders') ?>">
                  <i class="fas fa-sliders-h"></i>
                  <?= __('admin.store_orders') ?>
                </a>
                <a class="dropdown-item" href="<?= base_url('admincontrol/listclients') ?>">
                  <i class="fas fa-sliders-h"></i>
                  <?= __('admin.store_clients') ?>
                </a>
              </div>
            </li>
           

            <li id="sidebar_market_tools" class="nav-item dropdown" <?= $sidebar_data['market_tools_is_enable'] == 0 ? 'style="display:none;"' : ''; ?>>
              <a class="nav-link dropdown" href="javascript:void(0);" data-bs-toggle="dropdown">
                <div>
                 <i class="fas fa-cog"></i>
                 <span class="menu-title" style="color: <?= $admin_side_bar_text_color['admin_side_bar_text_color'] ?>;"><?= __('admin.program_integrations') ?></span>
                </div>
                <div>
                  <i class="fas fa-angle-right"></i>
                </div>
              </a>
              <div class="dropdown-menu">
                <a class="dropdown-item" href="<?= base_url('integration/integration_tools') ?>">
                  <i class="fas fa-sliders-h"></i>
                  <?= __('admin.menu_affiliate_marketing') ?>
                </a>
                <a class="dropdown-item" href="<?= base_url('integration/programs') ?>">
                  <i class="fas fa-sliders-h"></i>
                  <?= __('admin.sub_menu_integration_programs') ?>
                </a>
                <a class="dropdown-item" href="<?= base_url('integration/integration_category') ?>">
                  <i class="fas fa-sliders-h"></i>
                  <?= __('admin.integration_category') ?>
                </a>
              </div>
            </li>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown" href="javascript:void(0);" data-bs-toggle="dropdown">
                <div>
                  <i class="fas fa-hiking"></i>
                  <span class="menu-title" style="color: <?= $admin_side_bar_text_color['admin_side_bar_text_color'] ?>;">
                    <?= __('admin.system_activity') ?>
                  </span>
                </div>
                <div>
                  <i class="fas fa-angle-right"></i>
                </div>
              </a>
              <div class="dropdown-menu">
                <a class="dropdown-item" href="<?= base_url('admincontrol/store_orders') ?>">
                  <i class="fas fa-sliders-h"></i>
                  <?= __('admin.my_all_orders') ?>
                </a>
                <a class="dropdown-item" href="<?= base_url('admincontrol/store_logs') ?>">
                  <i class="fas fa-sliders-h"></i>
                  <?= __('admin.my_all_logs') ?>
                </a>
                <a class="dropdown-item" href="<?= base_url('incomereport') ?>">
                  <i class="fas fa-sliders-h"></i>
                  <?= __('admin.menu_users_statistics') ?>
                </a>
                <a class="dropdown-item" href="<?= base_url('reportController/admin_transaction') ?>">
                  <i class="fas fa-sliders-h"></i>
                  <?= __('admin.menu_report_transactions') ?>
                </a>
                <a class="dropdown-item" href="<?= base_url('reportController/admin_statistics') ?>">
                  <i class="fas fa-sliders-h"></i>
                  <?= __('admin.menu_report_graphs') ?>
                </a>
              </div>
            </li>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown" href="javascript:void(0);" data-bs-toggle="dropdown">
                <div>
                  <i class="fas fa-wallet"></i>
                  <span class="menu-title" style="color: <?= $admin_side_bar_text_color['admin_side_bar_text_color'] ?>;">
                    <?= __('admin.menu_admin_wallet') ?>
                  </span>
                </div>
                <div>
                  <i class="fas fa-angle-right"></i>
                </div>
              </a>
              <div class="dropdown-menu">
                <a class="dropdown-item" href="<?= base_url('admincontrol/mywallet') ?>">
                  <i class="fas fa-sliders-h"></i>
                  <?= __('admin.menu_all_transactions') ?>
                </a>
                <a class="dropdown-item" href="<?= base_url('admincontrol/wallet_requests_list') ?>">
                  <i class="fas fa-sliders-h"></i>
                  <?= __('admin.menu_withdraw_request_v2') ?>
                </a>
                <a class="dropdown-item" href="<?= base_url('admincontrol/withdrawal_payment_gateways') ?>">
                  <i class="fas fa-sliders-h"></i>
                  <?= __('admin.withdrawal_payment_gateways') ?>
                </a>
                <a class="dropdown-item" href="<?= base_url('admincontrol/wallet_setting') ?>">
                  <i class="fas fa-sliders-h"></i>
                  <?= __('admin.wallet_setting') ?>
                </a>
              </div>
            </li>

            <li id="sidebar_mlm" class="nav-item dropdown" <?= $sidebar_data['mlm_is_enable'] == 0 ? 'style="display:none;"' : ''; ?>>
              <a class="nav-link dropdown" href="javascript:void(0);" data-bs-toggle="dropdown">
                <div>
                  <i class="fas fa-drum-steelpan"></i>
                  <span class="menu-title" style="color: <?= $admin_side_bar_text_color['admin_side_bar_text_color'] ?>;">
                    <?= __('admin.menu_mlm') ?>
                  </span>
                </div>
                <div>
                  <i class="fas fa-angle-right"></i>
                </div>
              </a>
              <div class="dropdown-menu">
                <a class="dropdown-item" href="<?= base_url('admincontrol/mlm_settings') ?>">
                  <i class="fas fa-sliders-h"></i>
                  <?= __('admin.menu_mlm_settings') ?>
                </a>
                <a class="dropdown-item" href="<?= base_url('admincontrol/mlm_levels') ?>">
                  <i class="fas fa-sliders-h"></i>
                  <?= __('admin.menu_mlm_levels') ?>
                </a>
              </div>
            </li>

            <li id="sidebar_saas" class="nav-item dropdown" <?= $sidebar_data['saas_is_enable'] == 0 ? 'style="display:none;"' : ''; ?>>
              <a class="nav-link dropdown" href="javascript:void(0);" data-bs-toggle="dropdown">
                <div>
                <i class="fas fa-sliders-h"></i> <span class="menu-title" style="color: <?= $admin_side_bar_text_color['admin_side_bar_text_color'] ?>;"><?= __('admin.menu_saas') ?></span>
                </div>
                <div>
                  <i class="fas fa-angle-right"></i>
                </div>
              </a>
              <div class="dropdown-menu">
                <a class="dropdown-item" href="<?= base_url('admincontrol/saas_setting') ?>">
                  <i class="fas fa-sliders-h"></i>
                  <?= __('admin.menu_saas_settings') ?>
                </a>
                <a class="dropdown-item" href="<?= base_url('admincontrol/vendor_deposits') ?>">
                  <i class="fas fa-sliders-h"></i>
                  <?= __('admin.vendor_deposit') ?>
                </a>
              </div>
            </li>

            <li id="sidebar_membership" class="nav-item dropdown" <?= $sidebar_data['membership_is_enable'] == 0 ? 'style="display:none;"' : ''; ?>>
              <a class="nav-link dropdown" href="javascript:void(0);" data-bs-toggle="dropdown">
                <div>
                  <i class="fas fa-layer-group"></i>
                  <span class="menu-title" style="color: <?= $admin_side_bar_text_color['admin_side_bar_text_color'] ?>;">
                    <?= __('admin.membership') ?>
                  </span>
                </div>
                <div>
                  <i class="fas fa-angle-right"></i>
                </div>
              </a>
              <div class="dropdown-menu">
                <a class="dropdown-item" href="<?= base_url('membership/plans') ?>">
                  <i class="fas fa-sliders-h"></i>
                  <?= __('admin.membership_plans') ?>
                </a>
                <a class="dropdown-item" href="<?= base_url('membership/membership_orders') ?>">
                  <i class="fas fa-sliders-h"></i>
                  <?= __('admin.membership_orders') ?>
                </a>
                <a class="dropdown-item" href="<?= base_url('membership/settings') ?>">
                  <i class="fas fa-sliders-h"></i>
                  <?= __('admin.membership_settings') ?>
                </a>
              </div>
            </li>

          </ul>
      </div>
</div>
  </div>

<script type="text/javascript">
  var localStorageValue = localStorage.getItem("close-sidebar");
  if (localStorageValue) $(".main .left-menu").addClass("deactive");
  else $(".main .left-menu").removeClass("deactive");
</script>