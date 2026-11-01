<?php foreach($getallorders as $product){ ?>
											<tr>
												<td class="txt-cntr"><?php echo $product['id'];?></td>
												<td class="txt-cntr"><?php echo $product['username'];?></td>
												<td class="txt-cntr"><?php echo !empty($product['user_type']) ? __('admin.type_'. $product['user_type']) : '';?></td>
												<td class="txt-cntr"><?php echo c_format($product['total_sum']); ?></td>
												<td class="txt-cntr"><?php
													if ($product['payment_method'] == 'Bank Transfer') {
														echo __('admin.bank_transfer');
													}elseif ($product['payment_method'] == 'Cash On Delivery') {
														echo __('admin.cash_on_delivery');
													}elseif ($product['payment_method'] == 'OPay') {
														echo __('admin.opay');
													}elseif ($product['payment_method'] == 'Paypal') {
														echo __('admin.paypal');
													}elseif ($product['payment_method'] == 'Paypal') {
														echo __('admin.paypal');
													}elseif ($product['payment_method'] == 'Razorpay') {
														echo __('admin.razorpay');
													}elseif ($product['payment_method'] == 'Flutterwave') {
														echo __('admin.flutterwave');
													}else{
														echo str_replace("_", " ", $product['payment_method']);
													}
													//echo str_replace("_", " ", $product['payment_method']);
												?></td>
												<td class="txt-cntr"><?php echo $product['order_country_flag'];?></td>
												<td class="txt-cntr"><?php echo $product['txn_id'];?></td>

												<td class="txt-cntr">
													<?php 
														if($product['wallet_commission_status'] == 0){ ?>
															<span class="badge <?= ((int)$product['wallet_status'] > 0) ? 'bg-success' : 'bg-warning' ?>">
																<?php  
																	if ($wallet_status[(int)$product['wallet_status']] == 'ON HOLD') {
																		echo __('admin.onhold');
																	}elseif ($wallet_status[(int)$product['wallet_status']] == 'IN WALLET') {
																		echo __('admin.inwallet');
																	}else{
																		echo $wallet_status[(int)$product['wallet_status']];
																	}
																?>
															</span>
													<?php } else {
															echo commission_status($product['wallet_commission_status']);
													 	} ?>

													 	<br>
													 	<?= c_format($product['commission_amount']); ?>
												</td>
												<td class="txt-cntr order-status"><?php 
													if ($status[$product['status']] == 'Processed') {
														echo __('admin.processed');
													}elseif ($status[$product['status']] == 'Complete') {
														echo __('admin.complete');
													}else{
														echo $status[$product['status']];
													}
												?></td>
												<td>
													<select class="status-change-rdo"
															name="status_<?= $product['id'] ?>"
														 	data-id='<?= $product['id'] ?>'>
														<option value=""><?= __('admin.please_choose') ?></option>
														<?php 
															unset($status['0']);
															foreach ($status as $key => $value) { ?>
																<option value="<?= $key ?>"><?= $value ?></option>
														<?php } ?>
													</select>
												</td>
												<td>
													<a href="<?= base_url('admincontrol/vieworder/'. $product['id']) ?>" class="btn btn-primary btn-sm"><?= __('admin.details') ?></a>
												</td>
											</tr>
										<?php } ?>