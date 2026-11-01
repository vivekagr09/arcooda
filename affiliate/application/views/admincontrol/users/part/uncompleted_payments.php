<div class="card-body p-0">
	<div class="table-responsive">
		<table class="table transaction-table table-striped ">
			<thead>
				<tr>
					<th><?= __('admin.id') ?></th>
					<th><?= __('admin.username') ?></th>
					<th><?= __('admin.name') ?></th>
					<th><?= __('admin.email') ?></th>
					<th><?= __('admin.phone') ?></th>
					<th><?= __('admin.amount') ?></th>
					<th><?= __('admin.module') ?></th>
					<th><?= __('admin.additional_info') ?></th>
					<th><?= __('admin.datetime') ?></th>
					<th></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach($uncompleted_payments as $value){ ?>
					<tr>
						<td><?= $value['id'] ?></td>
						<td><?= $value['username'] ?></td>
						<td><?= $value['firstname'] ?> <?= $value['lastname'] ?></td>
						<td><?= empty($value['email']) ? '<span class="text-muted">'.__('admin.not_available').'</span>' : $value['email']; ?></td>
						<td><?= empty($value['phone']) ? '<span class="text-muted">'.__('admin.not_available').'</span>' : $value['phone']; ?></td>
						<td><?= c_format($value['ammount']) ?></td>
						<td><?= $payment_module[$value['payment_module']] ?></td>
						<td><?= empty($value['additional_info']) ? '<span class="text-muted">'.__('admin.not_available').'</span>' : $value['additional_info']; ?></td>
						<td><?= dateFormat($value['datetime'],'d F Y H:i'); ?></td>
						<td>
							<span class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#unsupoorted_payment_details_<?= $value['id']; ?>">
								<?= __('admin.details') ?>
							</span>

							<div class="modal fade" id="unsupoorted_payment_details_<?= $value['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
								<div class="modal-dialog modal-dialog-centered" role="document">
									<div class="modal-content">
										<div class="modal-header">
											<h5 class="modal-title" id="exampleModalLabel"><?= __('admin.payment_details') ?></h5>
											<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
										</div>
										<div class="modal-body">
											<?php
												switch ((int) $value['payment_module']) {
													case 1:
														?>
														<h4><?= __("admin.orders_details") ?></h4>
														<table class="table">
															<tbody>
																<tr>
																	<td><?= __("admin.created_at") ?></td>
																	<td><?= $value['content']['order']['created_at']; ?></td>
																</tr>
																<tr>
																	<td><?= __("admin.order_total") ?></td>
																	<td><?= c_format($value['content']['order']['total']); ?></td>
																</tr>
																<tr>
																	<td><?= __("admin.payment_method") ?></td>
																	<td><?= $value['content']['order']['payment_method']; ?></td>
																</tr>
															</tbody>
														</table>
														<h4><?= __("admin.products_details") ?></h4>
														<table class="table">
															<tbody>
																<tr>
																	<td><?= __("admin.product_name") ?></td>
																	<td><?= __("admin.price") ?></td>
																	<td><?= __("admin.quantity") ?></td>
																	<td><?= __("admin.total") ?></td>
																</tr>
																<?php foreach($value['content']['products'] as $products) { ?>
																	<tr>
																		<td><?= $products['product_name']; ?></td>
																		<td><?= c_format($products['price']); ?></td>
																		<td><?= $products['quantity']; ?></td>
																		<td><?= c_format($products['total']); ?></td>
																	</tr>
																<?php } ?>
															</tbody>
														</table>
														<?php
														break;
													case 2:
														?>
														<h4><?= __("admin.deposit_details") ?></h4>
														<table class="table">
															<tbody>
																<tr>
																	<td><?= __("admin.amount") ?></td>
																	<td><?= c_format($value['content']['deposit_details']['vd_amount']); ?></td>
																</tr>
																<tr>
																	<td><?= __("admin.status") ?></td>
																	<td><?= $value['content']['deposit_details']['status_label']; ?></td>
																</tr>
																<tr>
																	<td><?= __("admin.payment_method") ?></td>
																	<td><?= $value['content']['deposit_details']['vd_payment_method']; ?></td>
																</tr>
															</tbody>
														</table>
														<?php
														break;
													case 3:
														?>
														<h4><?= __("admin.membership_plan_details") ?></h4>
														<table class="table">
															<tbody>
																<tr>
																	<td><?= __("admin.name") ?></td>
																	<td><?= $value['content']['plan']['name']; ?></td>
																</tr>
																<tr>
																	<td><?= __("admin.type") ?></td>
																	<td><?= $value['content']['plan']['type']; ?></td>
																</tr>
																<tr>
																	<td><?= __("admin.billing_period") ?></td>
																	<td><?= $value['content']['plan']['billing_period']; ?></td>
																</tr>
																<tr>
																	<td><?= __("admin.price") ?></td>
																	<td><?= c_format($value['content']['plan']['price']); ?></td>
																</tr>
																<tr>
																	<td><?= __("admin.discount_price") ?></td>
																	<td><?= c_format($value['content']['plan']['special']); ?></td>
																</tr>
															</tbody>
														</table>
														<?php
														break;
												}
											?>
										</div>
									</div>
								</div>
							</div>

						</td>
					</tr>
				<?php } ?>
			</tbody>
		</table>
	</div>
</div>
<div class="card-footer">
	<ul class="pagination justify-content-end">
		<?= $pagination ?>
	</ul>
</div>
