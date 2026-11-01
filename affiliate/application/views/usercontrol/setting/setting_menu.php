<ul class="nav nav-pills mb-3" id="TabsNav">
    <li class="nav-item">
        <a class="nav-link <?= ($vendorSettingTab == 'wallet_setting') ? ' active' : '' ?>" 
           href="<?php echo base_url('usercontrol/wallet_setting');?>">
           <?= __('user.page_title_vendor_wallet_settings') ?>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link <?= ($vendorSettingTab == 'share_sales_setting') ? ' active' : '' ?>" 
           href="<?php echo base_url('usercontrol/share_sales_setting');?>">
           <?= __('user.page_title_vendor_share_sales_setting') ?>
        </a>
    </li>
</ul>
