<?php 
	$unique_url= base_url().'register/'.base64_encode( $userdetails['id']);
	$store_url = base_url('store/'. base64_encode($userdetails['id']));
?>
<?php foreach($clicks as $index => $order){ ?>
	<?php if($order['type'] == 'store'){ ?>
	<tr>
	  <td>
	    <button type="button" class="btn btn-link d-inline-block toggle-child-tr">
	      <i class="bi bi-plus"></i>
	    </button>
	    <?= $index + $start_from ?>
	  </td>
	  <td><?= $order['action_id'] ?></td>
	  <td><?= $unique_url ?></td>
	  <td>
	    <?= $order['flag'] ?>
	    <?= $order['ip'] ?> - <span class="text-muted"><?= $order['country_code'] ?></span>
	  </td>
	  <td><?= $order['created_at'] ?></td>
	  <td><?= __('admin.store_product_click') ?></td>
	  <td>
	    <?php if($order['custom_data']) { ?>
	      <?php foreach ($order['custom_data'] as $key => $value) { ?>
	        <b><?= $value['key'] ?></b>: <?= $value['value'] ?><br>
	      <?php } ?>
	    <?php } ?>
	  </td>
	</tr>

	<tr class="detail-tr">
	  <td colspan="100%">
	    <div>
	      <ul class="list-unstyled">
	        <li>
	          <b><?= __('admin.product_id') ?> : </b>
	          <span><?= $order['product_id'] ?></span>
	        </li>
	      </ul>
	    </div>
	  </td>
	</tr>

	<?php } else if($order['type'] == 'order') { ?>
	<tr>
	  <td>
	    <button type="button" class="btn btn-link d-inline-block toggle-child-tr">
	      <i class="bi bi-plus"></i>
	    </button>
	    <?= $index + $start_from ?>
	  </td>
	  <td><?= $order['id'] ?></td>
	  <td><?= $store_url ?></td>
	  <td>
	    <?= $order['flag'] ?> <?= $order['ip'] ?> - <span class="text-muted"><?= $order['country_code'] ?></span>
	  </td>
	  <td><?= $order['created_at'] ?></td>
	  <td><?= __('admin.store_order') ?></td>
	  <td>
	    <?php if($order['custom_data']) { ?>
	      <?php foreach ($order['custom_data'] as $key => $value) { ?>
	        <b><?= $value['key'] ?></b>: <?= $value['value'] ?><br>
	      <?php } ?>
	    <?php } ?>
	  </td>
	</tr>

	<tr class="detail-tr">
	  <td colspan="100%">
	    <div>
	      <ul class="list-unstyled">
	        <li>
	          <b><?= __('user.payment_method') ?> :</b>
	          <span>
	            <?php
	            if ($order['payment_method'] == 'Bank Transfer') {
	              echo __('admin.bank_transfer');
	            } elseif ($order['payment_method'] == 'Cash On Delivery') {
	              echo __('admin.cash_on_delivery');
	            } elseif ($order['payment_method'] == 'OPay') {
	              echo __('admin.opay');
	            } elseif ($order['payment_method'] == 'Paypal') {
	              echo __('admin.paypal');
	            } elseif ($order['payment_method'] == 'Razorpay') {
	              echo __('admin.razorpay');
	            } elseif ($order['payment_method'] == 'Flutterwave') {
	              echo __('admin.flutterwave');
	            } else {
	              echo str_replace("_", " ", $order['payment_method']);
	            }
	            ?>
	          </span>
	        </li>
	        <li><b><?= __('user.transaction') ?> :</b> <span><?= $order['txn_id'] ?></span> </li>
	        <li><b><?= __('user.ip') ?> :</b> <span><?= $order['ip'] ?></span> </li>
	        <li><b><?= __('user.country_code') ?> :</b> <span><?= $order['country_code'] ?></span> </li>
	        <li><b><?= __('user.currency_code') ?> :</b> <span><?= $order['currency_code'] ?></span> </li>
	      </ul>
	    </div>
	  </td>
	</tr>

	<?php } else  { ?>
	<tr>
	  <td>
	    <button type="button" class="btn btn-link d-inline-block toggle-child-tr">
	      <i class="bi bi-plus"></i>
	    </button>
	    <?= $index + $start_from ?>
	  </td>
	  <td><?= $order['id'] ?></td>
	  <td><?= $order['base_url'] ?></td>
	  <td>
	    <?= $order['flag'] ?> <?= $order['ip'] ?> - <span class="text-muted"><?= $order['country_code'] ?></span>
	  </td>
	  <td><?= $order['created_at'] ?></td>
	  <td><?= $order['click_type'] ?></td>
	  <td>
	    <?php if($order['click_type'] == "Action"){ ?>
	      <b>Action</b> <?= $order['base_url'] ?><br>
	    <?php } else { ?>
	      <b><?= $order['click_type'] ?></b> <?= $order['base_url'] ?><br>
	    <?php } ?>
	    <?php if($order['custom_data']) { ?>
	      <?php foreach ($order['custom_data'] as $key => $value) { ?>
	        <b><?= $value['key'] ?></b>: <?= $value['value'] ?><br>
	      <?php } ?>
	    <?php } ?>
	  </td>
	</tr>

	<tr class="detail-tr">
	  <td colspan="100%">
	    <div>
	      <ul class="list-unstyled">
	        <li><b><?= __('user.page') ?> : </b> <span><?= $order['link'] ?></span></li>
	        <li><b><?= __('user.browser') ?> : </b> <span><?= $order['browserName'] ?> - <span class="text-muted"><?= $order['browserVersion'] ?></span></span></li>
	        <li><b><?= __('user.os_platform') ?> : </b> <span><?= $order['osPlatform'] ?> - <span class="text-muted"><?= __('user.version') ?> : <?= $order['osVersion'] ?></span></span></li>
	        <li><b><?= __('user.mobile_name') ?> : </b> <span><?= $order['mobileName'] ?></span></li>
	      </ul>
	    </div>
	  </td>
	</tr>

	<?php } ?>
<?php } ?>