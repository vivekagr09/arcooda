<?php foreach($orders as $index => $order){ ?>

	<?php if($order['type'] == 'store'){ ?>
<tr>
    <td>
        <button type="button" class="btn btn-sm btn-primary toggle-child-tr"><i class="bi bi-plus-circle"></i></button>
        <?php echo $start_from + $index; ?>
    </td>
    <td><?php echo $order['id']; ?></td>
    <td><?php echo c_format($order['total_sum']); ?></td>
    <td><?php echo $order['order_country_flag']; ?></td>
    <td><?= __('user.local_store') ?></td>
    <?php 
        $icon = strtolower(str_replace(" ", "_", $status[$order['status']])) . '.png';
    ?>
    <td><img width="30px" height="30px" title="<?= $status[$order['status']] ?>" alt="<?php echo $icon; ?>" src="<?= base_url('assets/images/wallet-icon/'. $icon) ?>"></td>
    <td>
        <?php echo c_format($order['commission_amount']); ?>
        <br>
        <?php

        if ($order['wallet_commission_status'] == 0) {
            ?>
            <span class="badge <?php if ((int)$order['wallet_status'] > 0) { ?>bg-success<?php } else { ?>bg-warning<?php } ?>"><?= $wallet_status[(int)$order['wallet_status']] ?></span>
            <?php
        } else {
            echo commission_status($order['wallet_commission_status']);
        }

        ?>
    </td>
    <td><?php echo wallet_paid_status($order['wallet_status']); ?></td>
    <td class="text-center"><?php echo date("d-m-Y h:i A", strtotime($order['created_at'])); ?></td>
</tr>

<tr class="detail-tr">
    <td colspan="100%">
        <div class="container-fluid">
            <ul>
                <li><b><?= __('user.payment_method') ?> :</b> <span><?= $order['payment_method']; ?></span> </li>
                <li><b><?= __('user.transaction') ?> :</b> <span><?= $order['txn_id'] ?></span> </li>
                <li><b><?= __('user.ip') ?> :</b> <span><?= $order['ip'] ?></span> </li>
                <li><b><?= __('user.country_code') ?> :</b> <span><?= $order['country_code'] ?></span> </li>
                <li><b><?= __('user.currency_code') ?> :</b> <span><?= $order['currency_code'] ?></span> </li>
                <li>
                    <b><?= __('user.products') ?></b>
                    <table class="detail-table table table-bordered">
                        <thead>
                            <tr>
                                <th><?= __('user.name') ?></th>
                                <th><?= __('user.unit_price') ?></th>
                                <th><?= __('user.variation_price') ?></th>
                                <th><?= __('user.quantity') ?></th>
                                <th><?= __('user.commission_type') ?></th>
                                <th><?= __('user.total_discount') ?></th>
                                <th><?= __('user.total') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($order['products'] as $key => $product) { ?>
                                <tr>
                                    <td>
                                        <div class="media">
                                            <img src="<?= $product['image'] ?>" alt="<?= $product['product_name'] ?>" class="mr-3" style="width: 40px; height: 40px;">
                                            <div class="media-body">
                                                <?= $product['product_name'] ?>
                                                <?php
                                                $combinationString = "";
                                                if (isset($product['variation']) && !empty($product['variation'])) {
                                                    $variation = json_decode($product['variation']);
                                                    foreach ($variation as $key => $value) {
                                                        if ($key == 'colors') {
                                                            $combinationString .= ($combinationString == "") ? explode("-", $value)[1] : "," . explode("-", $value)[1];
                                                        } else {
                                                            $combinationString .= ($combinationString == "") ? $value : "," . $value;
                                                        }
                                                    }
                                                }
                                                if ($product['coupon_discount'] > 0) {
                                                    echo '<p class="couopn-code-text">' . __('user.code') . ': <span class="c-name">' . $product['coupon_code'] . '</span> ' . __('user.applied') . '</p>';
                                                }
                                                ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td><?php echo c_format($product['price']); ?></td>
                                    <td><?php echo c_format(json_decode($product['variation'])->price); ?></td>
                                    <td><?php echo $product['quantity']; ?></td>
                                    <td><?php echo $product['commission_type']; ?></td>
                                    <td><?php echo c_format($product['coupon_discount']);  ?></td>
                                    <td><?php echo c_format($product['total']); ?></td>
                                </tr>
                            <?php } ?>
                            <?php foreach ($order['totals'] as $key => $total) { ?>
                                <tr>
                                    <td colspan="5"></td>
                                    <td><?= $total['text'] ?></td>
                                    <td><?php echo c_format($total['value']); ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </li>

                <li>
                    <b><?= __('user.payment_info') ?></b>
                    <table class="detail-table table table-bordered">
                        <thead>
                            <th><?= __('user.mode') ?></th>
                            <th><?= __('user.transaction_id') ?></th>
                            <th><?= __('user.payment_status') ?></th>
                        </thead>
                        <tbody>
                            <?php if ($order['status'] == 0) { ?>
                                <tr>
                                    <td colspan="100%">
                                        <p class="text-muted text-center"> <?= __('user.waiting_for_payment_status') ?> </p>
                                    </td>
                                </tr>
                            <?php } ?>
                            <?php foreach ($order['payment_history'] as $key => $value) { ?>
                                <tr>
                                    <td><?php echo $value['payment_mode']; ?></td>
                                    <td><?php echo $order['txn_id']; ?></td>
                                    <td><?php echo $value['paypal_status'] ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </li>

                <li>
                    <b><?= __('user.order_info') ?></b>
                    <table class="detail-table table table-bordered">
                        <thead>
                            <tr>
                                <th width="50px">#</th>
                                <th width="150px"><?= __('user.status') ?></th>
                                <th><?= __('user.comment') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!$order['order_history']) { ?>
                                <tr>
                                    <td colspan="100%">
                                        <p class="text-muted text-center"><?= __('user.no_any_order_status') ?></p>
                                    </td>
                                </tr>
                            <?php } ?>
                            <?php foreach ($order['order_history'] as $key => $value) { ?>
                                <tr>
                                    <td>#<?= $key ?></td>
                                    <td><?= $status[$value['order_status_id']] ?></td>
                                    <td style="white-space: pre-line;"><?= $value['comment'] ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </li>

                <?php if ($order['custom_data']) {
                    $order['custom_data'] = json_decode($order['custom_data']);
                    foreach ($order['custom_data'] as $key => $value) { ?>
                        <li><b><?= $value->key ?></b>: <?= $value->value ?></li>
                    <?php }
                } ?>
            </ul>
        </div>
    </td>
</tr>

	<?php } else { ?>
<tr>
    <td>
        <button type="button" class="btn btn-sm btn-primary toggle-child-tr"><i class="bi bi-plus-circle"></i></button>
        <?php echo $start_from + $index; ?>
    </td>
    <td><?php echo $order['id']; ?></td>
    <td><?php echo c_format($order['total']); ?></td>
    <td><?php echo $order['order_country_flag']; ?></td>
    <td><?= __('user.external') ?></td>
    <td><?= __('user.complete') ?></td>
    <td>
        <?= c_format($order['commission']) ?>
        <br>
        <?php

        if ($order['wallet_commission_status'] == 0) {
            ?>
            <span class="badge <?php if ((int)$order['wallet_status'] > 0) { ?>bg-success<?php } else { ?>bg-warning<?php } ?>"><?= $wallet_status[(int)$order['wallet_status']] ?></span>
            <?php
        } else {
            echo commission_status($order['wallet_commission_status']);
        }

        ?>
    </td>
    <td><?php echo wallet_paid_status($order['wallet_status']);?></td>
    <td class="text-center"><?php echo date("d-m-Y h:i A",strtotime($order['created_at'])); ?></td>
</tr>

<tr class="detail-tr">
    <td colspan="100%">
        <div class="container-fluid">
            <ul class="list-group list-group-flush">
                <li class="list-group-item"><strong><?= __('user.product_ids') ?> :</strong> <span><?= $order['product_ids'] ?></span></li>
                <li class="list-group-item"><strong><?= __('user.total') ?> :</strong> <span><?= $order['total'] ?></span></li>
                <li class="list-group-item"><strong><?= __('user.currency') ?> :</strong> <span><?= $order['currency'] ?></span></li>
                <li class="list-group-item"><strong><?= __('user.commission_type') ?> :</strong> <span><?= $order['commission_type'] ?></span></li>
                <li class="list-group-item"><strong><?= __('user.ip') ?> :</strong> <span><?= $order['ip'] ?></span></li>
                <li class="list-group-item"><strong><?= __('user.country_code') ?> :</strong> <span><?= $order['country_code'] ?>&nbsp;<img title="<?= $order['country_code'] ?>" src="<?= base_url('assets/vertical/assets/images/flags/'. strtolower($order['country_code'])) ?>.png" class="img-fluid" width='25' height='15'></span></li>
                <li class="list-group-item"><strong><?= __('user.website') ?> :</strong> <span><a href="//<?= $order['base_url'] ?>" class="text-decoration-none" target='_blank'><?= $order['base_url'] ?></a></span></li>
                <li class="list-group-item"><strong><?= __('user.script_name') ?> :</strong> <span><?= ucfirst($order['script_name']) ?></span></li>
                <?php if ($order['custom_data']) {
                    $order['custom_data'] = json_decode($order['custom_data']);
                    foreach ($order['custom_data'] as $key => $value) { ?>
                        <li class="list-group-item"><strong><?= $value->key ?></strong>: <?= $value->value ?></li>
                    <?php }
                } ?>
            </ul>
        </div>
    </td>
</tr>


	<?php } ?>
<?php } ?>