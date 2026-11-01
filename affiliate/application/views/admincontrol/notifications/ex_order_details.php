<div class="row">
    <div class="col-12">
        <div>
            <div>
                <a href="#" class="list-group-item list-group-item-action flex-column align-items-start active">
                    <div class="d-flex w-100 justify-content-between">
                      <h5 class="mb-1"><?= __('admin.page_title_order_notification_details') ?></h5>
                    </div>
                  </a>
                <ul class="list-group">
                    <li class="list-group-item"><?= nl2br($notification_details);?></li>
                    <li class="list-group-item"><?= __('admin.product_ids') ?> : <?= $order['product_ids'] ?></li>
                    <li class="list-group-item"><?= __('admin.total') ?> : <?= $order['total'] ?></li>
                    <li class="list-group-item"><?= __('admin.currency') ?> : <?= $order['currency'] ?></li>
                    <li class="list-group-item"><?= __('admin.commission_type') ?> : <?= $order['commission_type'] ?></li>
                    <li class="list-group-item"><?= __('admin.ip') ?> : <?= $order['ip'] ?></li>

                    <li class="list-group-item"><?= __('admin.country_code') ?>: <?= $order['country_code'].'&nbsp;<img title="'. $order['country_code'].'" src="'. base_url('assets/vertical/assets/images/flags/'. strtolower($order['country_code'])).'.png" width="25" height="15"</li>' ?>

                    <li class="list-group-item"><?= __('admin.website') ?>: <a href="<?= $order['base_url'] ?>" target="_blank"> <?= $order['base_url'] ?></a></li>

                    <li class="list-group-item"><?= __('admin.script_name') ?> : <?= ucfirst($order['script_name']) ?></li>

                   <li class="list-group-item"><?= __('admin.custom_data') ?> : <br>

                     <?php 
                        $custom_data=(isset($order['custom_data']) && !empty($order['custom_data'])) ? json_decode($order['custom_data'], 1) : array();
                        if($custom_data) {
                        foreach ($custom_data as $key => $value) { ?>
                        <b><?= $value['key'] ?></b>: <?= $value['value'] ?><br>
                        <?php }
                        } ?>

                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>