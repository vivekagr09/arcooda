<style>
  .card-title{
      text-align:center;
  }

  .card-header{
      font-size:20px;
  }
</style>


<div class="my-vendor-panel">
  <div class="section">
    <div class="card-group">
      <div class="card card-title text-white bg-primary mb-3 mr-3 col-4">
        <div class="card-header"><?= __( 'user.my_vendor_store') ?></div>
        <div class="card-body">
          <p class="card-text"><?= __( 'user.my_vendor_store_info') ?></p>
       </div>
      </div>

      <div class="card card-title text-white bg-info mb-3 mr-3 col-4">
        <div class="card-header"><?= __( 'user.my_vendor_market_tools') ?></div>
        <div class="card-body">
          <p class="card-text"><?= __( 'user.my_vendor_market_tools_info') ?></p>
        </div>
      </div>

      <div class="card card-title text-white bg-info mb-3 col-4">
        <div class="card-header"><?= __( 'user.my_store') ?></div>
        <div class="card-body">
           <a href="<?= base_url('store/'.$store_details['store_slug']); ?>" target="_blank" class="display-my-store">
              <?= __('user.display_my_store') ?>
            </a>
        </div>
      </div>
    </div>
  </div>

  <div class="vendor-statistic">
    <div class="item bg-info text-white">
      <span> <?= __('user.balance') ?></span>
      <p><?= $fun_c_format($user_totals['user_balance']) ?></p>
    </div>
    <div class="item bg-info text-white">
      <span> <?= __('user.deposit_balance') ?></span>
      <p><?= $fun_c_format($total_deposited) ?></p>
    </div>
    <div class="item bg-info text-white">
      <span> <?= __('user.local_store_sale') ?></span>
      <p><?= $fun_c_format($user_totals['vendor_sale_localstore_total']) ?></p>
    </div>
    <div class="item bg-info text-white">
      <span> <?= __('user.external_order_sale') ?></span>
      <p><?= $fun_c_format($user_totals['vendor_order_external_total']) ?></p>
    </div>
    <div class="item bg-info text-white">
      <span><?= __('user.total_sale') ?></span>
      <p><?= $fun_c_format($user_totals['vendor_sale_localstore_total'] + $user_totals['vendor_order_external_total']) ?></p>
    </div>
  </div>

</div>

<!--This group is display the vendor store--> 
<?php if((isset($userdetails['is_vendor']) && $userdetails['is_vendor']) && (int)$vendor_setting['storestatus'] == 1 && (int)$store_setting['status'] == 1){ ?>
<?php } ?>


<!--This group is display the vendor market tools--> 
<?php if((isset($userdetails['is_vendor']) && $userdetails['is_vendor']) && (int)$market_vendor['marketvendorstatus'] == 1){ ?>
<?php } ?> 




