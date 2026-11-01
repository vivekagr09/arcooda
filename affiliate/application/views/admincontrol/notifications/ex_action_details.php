<div class="row">
	<div class="col-12">
        <div>
            <div>
                <a href="#" class="list-group-item list-group-item-action flex-column align-items-start active">
                    <div class="d-flex w-100 justify-content-between">
                      <h5 class="mb-1"><?= __('admin.page_title_action_notification_details') ?></h5>
                    </div>
                  </a>
                <ul class="list-group">
                    <li class="list-group-item"><?= nl2br($notification_details);?></li>
                    <li class="list-group-item"><?= __('admin.click_id') ?> : <?= $order['click_id'] ?></li>
                    <li class="list-group-item"><?= __('admin.website') ?> : <?= $order['base_url'] ?></li>
                    <li class="list-group-item"><?= __('admin.ip') ?> : <?= $order['flag'] ?> <?= $order['ip'] ?> - <?= $order['country_code'] ?></li>
                    <li class="list-group-item"><?= __('admin.created_at') ?> : <?= $order['created_at'] ?></li>
                    <li class="list-group-item"><?= __('admin.click_type') ?> : <?= $order['click_type'] ?></li>
                    <li class="list-group-item"><?= __('user.page') ?> : <?= $order['link'] ?></li>
                    <li class="list-group-item"><?= __('user.browser') ?> : <?= $order['browserName'] ?> - <?= $order['browserVersion'] ?></li>
                    <li class="list-group-item"><?= __('user.os_platform') ?> : <?= $order['osPlatform'] ?> - <?= __('user.version') ?> : <?= $order['osVersion'] ?></li>
                    <li class="list-group-item"><?= __('user.mobile_name') ?> : <?= $order['mobileName'] ?></li>
                    <li class="list-group-item"><?= __('admin.custom_data') ?>:<br>
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