<div class="row">
	<div class="col-12">
		<div class="card mb-3">
			<div class="card-header d-flex justify-content-between align-items-center">
				<h4 class="card-title mb-0"><?= __('admin.currencies') ?></h4>
				<a href="<?= base_url('admincontrol/currency_edit/') ?>" class="btn btn-primary add-new"><?= __("admin.add_new") ?></a>
			</div>
			<div class="card-body">
				<div class="table-responsive">
					<table id="tech-companies-1" class="table table-striped">
						<thead>
							<tr>
								<th><?= __('admin.currency_type') ?></th>
								<th><?= __('admin.symbol_right') ?></th>
								<th><?= __('admin.symbol_left') ?></th>
								<th><?= __('admin.replace_comma_symbol') ?></th>
								<th><?= __('admin.decimal_symbol') ?></th>
								<th><?= __('admin.decimal_places') ?></th>
								<th><?= __('admin.is_default') ?></th>
								<th><?= __('admin.status') ?></th>
								<th><?= __('admin.code') ?></th>
								<th><?= __('admin.value') ?></th>
								<th><?= __('admin.last') ?></th>
								<th></th>
							</tr>
						</thead>
						<tbody>
							<?php foreach($currencys as $currency){ ?>
								<tr>
									<td><?= $currency['title'] . ($currency['is_default'] ? ' - ('.__('admin.default').')' : '') ?></td>
									<td><?= $currency['symbol_right'] ?></td>
									<td><?= $currency['symbol_left'] ?></td>
									<td><?= $currency['replace_comma_symbol'] ?></td>
									<td><?= $currency['decimal_symbol'] ?></td>
									<td><?= $currency['decimal_place'] ?></td>
									<td><?= $currency['is_default'] ? __('admin.default') : '' ?></td>
									<td><?= $currency['status'] ?></td>
									<td><?= $currency['code'] ?></td>
									<td><?= $currency['value'] ?></td>
									<td><?= $currency['date_modified'] ?></td>
									<td>
										<a href="<?= base_url('admincontrol/currency_edit/'. $currency['currency_id']) ?>" class="btn btn-sm btn-primary"><?= __('admin.edit') ?></a>
										<a href="<?= base_url('admincontrol/currency_delete/'. $currency['currency_id']) ?>" class="btn btn-sm btn-danger btn-delete"><?= __('admin.delete') ?></a>
									</td>
								</tr>
							<?php } ?>
						</tbody>
					</table>
				</div>
			</div>
		</div> 
	</div> 
</div>