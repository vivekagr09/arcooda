<div class="row-12">
<nav class="navbar navbar-expand-lg navbar-light bg-white">
  <a class="navbar-brand" href="<?php echo base_url();?>usercontrol/store_dashboard/"><?= __('user.store') ?></a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarNavDropdown">
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" href="<?php echo base_url();?>usercontrol/store_products">
            <?= __('user.my_vendor_products') ?>
        </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="<?php echo base_url();?>usercontrol/sales_products">
            <?= __('user.page_title_sales_products') ?>
        </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="<?php echo base_url();?>usercontrol/store_coupon">
            <?= __('user.my_vendor_coupons') ?>
        </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="<?php echo base_url();?>usercontrol/store_setting">
            <?= __('user.my_vendor_store_settings') ?>
        </a>
        </li>
    </ul>
  </div>
</nav>
</div>