<nav class="navbar navbar-expand-lg navbar-light bg-white">
  <a class="navbar-brand" href="<?= base_url('admincontrol/store_dashboard'); ?>"><?= __('admin.store_dashboard') ?></a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarNavDropdown">
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" href="<?= base_url('admincontrol/listproduct/'); ?>">
                <?= __('admin.menu_list_products') ?>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="<?= base_url('Productsales/index') ?>">
                <?php echo __('admin.product_campaign') ?>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="<?= base_url('admincontrol/listproduct/reviews'); ?>">
            <?= __('admin.page_title_listproduct_review') ?>
        </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="<?= base_url('admincontrol/store_category/'); ?>">
            <?= __('admin.menu_store_category') ?>
        </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="<?= base_url('admincontrol/listorders/'); ?>">
            <?= __('admin.menu_list_orders') ?>
        </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="<?= base_url('admincontrol/form/'); ?>">
            <?= __('admin.menu_form_list') ?>
        </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="<?= base_url('admincontrol/listclients/'); ?>">
            <?= __('admin.menu_list_clients') ?>
        </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="<?= base_url('admincontrol/coupon/'); ?>">
            <?= __('admin.coupon') ?>
        </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="<?= base_url('admincontrol/form_coupon/'); ?>">
            <?= __('admin.menu_form_coupon') ?>
        </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="<?= base_url('admincontrol/store_setting/'); ?>">
            <?= __('admin.page_title_store_setting') ?>
        </a>
        </li>
    </ul>
  </div>
</nav>