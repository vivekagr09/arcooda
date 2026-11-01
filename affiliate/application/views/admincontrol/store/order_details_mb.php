<?php
$order = $orders[0];
?>

<?php if($order['type'] == 'store') { ?>
    <div>
        <ul>
        	<?php 
        	if(($order['wallet_type'] == 'sale_commission' || $order['wallet_type'] == 'admin_sale_commission' || 
        		$order['wallet_type'] == 'vendor_sale_commission') && $order['wallet_comm_from'] == 'store' && 
        		!empty($order['wallet_reference_id_2'])){

				$product = $this->db->query('SELECT product_id,product_name, product_slug,is_campaign_product FROM product WHERE product_id='.$order['wallet_reference_id_2'])->row();

			
			if($product->is_campaign_product==1) {

				$productLink = base_url('store/product/'.$product->product_id);
			}else {

				$productLink = base_url('store/'. base64_encode($userdetails['id']) .'/product/'.$product->product_slug);
			}

				echo "<li>";
					echo "<b>".wallet_ex_type($trans)." -</b>";
					echo " <a target=\"_blank\" href=\"".$productLink."?preview=1\">".ucwords($product->product_name)."</a>";
				echo "</li>";
			} ?>
			<li><b><?= __('admin.payment_method') ?> :</b> <span><?= $order['payment_method']; ?></span> </li>
			<li><b><?= __('admin.transaction') ?> :</b> <span><?= $order['txn_id'] ?></span> </li>
			<li><b><?= __('admin.ip') ?> :</b> <span><?= $order['ip'] ?></span> </li>
			<li><b><?= __('admin.country_code') ?> :</b> <span><?= $order['country_code'] ?></span> </li>
			<li><b><?= __('admin.currency_code') ?> :</b> <span><?= $order['currency_code'] ?></span> </li>
			<li><br>
				<b><?= __('admin.products') ?></b>
				<table class="table table-bordered table stripped">
					<tr>
						<th colspan="2"><?= __('admin.name') ?></th>
						<th><?= __('admin.unit_price') ?></th>
						<th><?= __('admin.quantity') ?></th>
						<th><?= __('admin.commission_type') ?></th>
						
						<th><?= __('admin.total_discount') ?></th>
						<th><?= __('admin.total') ?></th>
					</tr>
					<?php foreach ($order['products'] as $key => $product) { ?>
						<tr>
							<td><img src="<?= $product['image'] ?>" style="width: 40px;height: 40px"></td>
							<td>
							<?php
								$combinationString = "";
								if(isset($product['variation']) && !empty($product['variation'])) {
									$variation = json_decode($product['variation']);
									foreach ($variation as $key => $value) {
										if($key == 'colors') {
											$combinationString .= ($combinationString == "") ? explode("-",$value)[1] : ",".explode("-",$value)[1];
										} else {
											$combinationString .= ($combinationString == "") ? $value : ",".$value;
										}
									}
								}
							?>
							<?= $product['product_name'] ?> <?= ($combinationString != "") ? "(".$combinationString.")" : "" ?>
								<?php if($product['coupon_discount'] > 0){ ?>
	                                <p class="couopn-code-text">
	                                	<?= __('admin.code') ?> : <span class="c-name"> <?= $product['coupon_code'] ?></span> <?= __('admin.applied') ?>
	                                </p>
                                <?php } ?>
							</td>
							<td><?php echo c_format($product['price']); ?></td>
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
				</table>
			</li>

			<li>
				<b><?= __('admin.payment_info') ?></b>
				<table class="table table-bordered table stripped">
					<thead>
						<th class="border-top-0"><?= __('admin.mode') ?></th>
						<th class="border-top-0"><?= __('admin.transaction_id') ?></th>
						<th class="border-top-0"><?= __('admin.payment_status') ?></th>
					</thead>
					<tbody>
						<?php if($order['status'] == 0){ ?>
							<tr>
								<td colspan="100%">
									<p class="text-muted text-center"> <?= __('admin.waiting_for_payment_status') ?> </p>
								</td>
							</tr>
						<?php } ?>
						<?php foreach ($order['payment_history'] as $key => $value) { ?>
						<tr>
							<td><?php echo $value['payment_mode'];?></td>
							<td><?php echo $order['txn_id'];?></td>
							<td><?php echo $value['paypal_status'] ?></td>
						</tr>
						<?php } ?>
					</tbody>
				</table>
			</li>

			<li>
				<b><?= __('admin.order_info') ?></b>
				<table class="table table-bordered table stripped">
					<thead>
						<tr>
							<th width="50px">#</th>
							<th width="150px"><?= __('admin.status') ?></th>
							<th><?= __('admin.comment') ?></th>
						</tr>
					</thead>
					<tbody>
						<?php if(!$order['order_history']){ ?>
							<tr>
								<td colspan="100%">
									<p class="text-muted text-center"><?= __('admin.no_any_order_status') ?></p>
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
			
			<?php if(!empty($order['comment'])) {
			    $cmt = json_decode($order['comment'], true);
    			 foreach($cmt as $c) {
			    if(is_array($c)) {
			        ?>
        			<li><b><?= $c['title']; ?> :</b> <span><?= $c['comment'] ?></span> </li>
        			<?php
			    }
			    }
			} ?>
			<li><b><?= __('admin.order_created_at') ?> :</b> <span><?= $order['created_at'] ?></span> </li>
        </ul>
   	</div>
<?php } else { ?>
    <div>
        <ul>
			<li><b><?= __('admin.product_ids') ?> :</b> <span><?= $order['product_ids'] ?></span></li>
			<li><b><?= __('admin.total') ?> :</b> <span><?= $order['total'] ?></span></li>
			<li><b><?= __('admin.currency') ?> :</b> <span><?= $order['currency'] ?></span></li>
			<li><b><?= __('admin.commission_type') ?> :</b> <span><?= $order['commission_type'] ?></span></li>
			<li><b><?= __('admin.ip') ?> :</b> <span><?= $order['ip'] ?></span></li>
			<li><b><?= __('admin.country_code') ?> :</b> <span><?= $order['country_code'] ?>&nbsp;<img title="<?= $order['country_code'] ?>" src="<?= base_url('assets/vertical/assets/images/flags/'. strtolower($order['country_code'])) ?>.png" width='25' height='15'></span></li>
			<li><b><?= __('admin.website') ?> :</b> <span><a href="//<?= $order['base_url'] ?>" target='_blank'><?= $order['base_url'] ?></a></span></li>
			<li><b><?= __('admin.script_name') ?> :</b> <span><?= ucfirst($order['script_name']) ?></span></li>

			<?php 
				$custom_data = json_decode($order['custom_data'],1);
				foreach ($custom_data as $value) { ?>
					<li class="">
						<b><?= $value['key'] ?>:</b> <span><?= $value['value'] ?></span>
					</li>
			<?php } ?>
        <li><b><?= __('admin.order_created_at') ?> :</b> <span><?= $order['created_at'] ?></span> </li>
        </ul>
   	</div>
<?php } ?>
