
<div class="card m-t-30 border-0 bg-transparent">
	<div class="card-body p-0">
		<div class="row">
			<div class="col-lg-12">
				<div class="card m-b-30">
				    <div class="card-header border-0">
                		<h4 class="card-title"> <?= __('user.order_details') ?> </h4>
                	</div>
					<div class="card-body">
						<table class="table table-striped">
							<thead>
								<tr>
									<th colspan="2"><?= __('user.name') ?></th>
									<th><?= __('user.unit_price') ?></th>
									<th><?= __('user.variation_price') ?></th>
									<th><?= __('user.quantity') ?></th>
									<th><?= __('user.commission_type') ?></th>
									<th><?= __('user.commission_amount') ?></th>
									<th><?= __('user.total_discount') ?></th>
									<th><?= __('user.total') ?></th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($products as $key => $product) { ?>
									<tr>
										<td><img src="<?= $product['image'] ?>" style="width: 50px;height: 50px"></td>
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
				                                	<?= __('user.code') ?> : <span class="c-name"> <?= $product['coupon_code'] ?></span> <?= __('user.applied') ?>
				                                </p>
			                                <?php } ?>
										</td>
										<td><?php echo c_format($product['price']); ?></td>
										<td><?php echo c_format(json_decode($product['variation'])->price); ?></td>
										<td><?php echo $product['quantity']; ?></td>
										<td><?php echo $product['commission_type']; ?></td>
										<td><?php echo c_format($product['commission']);  ?></td>
										<td><?php echo c_format($product['coupon_discount']);  ?></td>
										<td><?php echo c_format($product['total']); ?></td>
									</tr>
								<?php } ?>
								<?php foreach ($totals as $key => $total) { ?>
								<tr>
									<td colspan="7"></td>
									<td><?= $total['text'] ?></td>
									<td><b><?php echo c_format($total['value']); ?></b></td>
								</tr>
								<?php } ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
		
		<div class="row">
			<div class="col-lg-8 col-md-8 pb-4">
				<div class="card m-b-30 h-100">
				    <div class="card-header border-0">
				        <h5 class="header-title mb-0 pb-0"><?= __('user.order_payment_info') ?></h5>
				    </div>
					<div class="card-body">
						<div class="table-responsive">
							<table class="table table-striped">
								<thead>
									<th class="border-top-0"><?= __('user.mode') ?></th>
									<th class="border-top-0"><?= __('user.transaction_id') ?></th>
									<th class="border-top-0"><?= __('user.payment_status') ?></th>
								</thead>
								<tbody>
									<?php if($order['status'] == 0){ ?>
										<tr>
											<td colspan="100%">
												<p class="text-muted text-center"> <?= __('user.waiting_for_payment_status') ?> </p>
											</td>
										</tr>
									<?php } ?>
									<?php foreach ($payment_history as $key => $value) { ?>
									<tr>
										<td><?php echo $value['payment_mode'];?></td>
										<td><?php echo $order['txn_id'];?></td>
										<td><?php echo $value['paypal_status'] ?></td>
									</tr>
									<?php } ?>
								</tbody>
							</table>
						</div>
						<?php if($order['order_country']){ ?>
							<div class="form-group mt-4">
								<label class="control-label"><b><?= __('user.order_done_from') ?></b></label>
								<div>
									<?php echo $order['order_country'];?><?php echo $order['order_country_flag'];?>
								</div>
							</div>
						<?php  } ?>
					</div>
				</div>
			</div>
			<?php if($order['allow_shipping']){ ?>
				<div class="col-lg-4 col-md-4">
					<div class="card m-b-30">
					    <div class="card-header border-0">
					        <h5 class="header-title pb-0 mb-0"><?= __('user.shipping_details') ?></h5>
					    </div>
						<div class="card-body">
							<div class="">
								<table class="table table-hover">
									<thead>
										<tr>
											<td><?= __('user.address') ?></td>
											<td><?php echo $order['address'] ?></td>
										</tr>
										<tr>
											<td><?= __('user.country') ?></td>
											<td><?php echo $order['country_name'] ?></td>
										</tr>
										<tr>
											<td><?= __('user.state') ?></td>
											<td><?php echo $order['state_name'] ?></td>
										</tr>
										<tr>
											<td><?= __('user.city') ?></td>
											<td><?php echo $order['city'] ?></td>
										</tr>
										<tr>
											<td><?= __('user.postal_code') ?></td>
											<td><?php echo $order['zip_code'] ?></td>
										</tr>
									</thead>
								</table>
							</div>
						</div>
					</div>
				<?php } ?>
			</div>
		</div>
		<div class="row">
			<div class="col-lg-12 col-sm-12 align-self-center">
				<div class="card m-b-30">
				    <div class="card-header border-0">
				        <h5 class="header-title mb-0 pb-0"><?= __('user.update_order_status') ?></h5>
				    </div>
					<div class="card-body new-user">
						<table class="table table-striped">
							<thead>
								<tr>
									<th width="50px">#</th>
									<th width="150px"><?= __('user.status') ?></th>
									<th><?= __('user.comment') ?></th>
								</tr>
							</thead>
							<tbody>
								<?php if(!$order_history){ ?>
									<tr>
										<td colspan="100%">
											<p class="text-muted text-center"><?= __('user.no_any_order_status') ?></p>
										</td>
									</tr>
								<?php } ?>
								<?php foreach ($order_history as $key => $value) { ?>
								<tr>
									<td>#<?= $key ?></td>
									<td><?= $status[$value['order_status_id']] ?></td>
									<td><?= $value['comment'] ?></td>
								</tr>
								<?php } ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>