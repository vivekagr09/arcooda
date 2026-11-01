<div class="row">
	<div class="col-12">
		<div class="card">
			<div class="card-header"><?= __('user.integration_wallet') ?></div>
			<div class="card-body">
				<?php if ($transaction ==null) {?>
					<div class="text-center mt-5">
					 <div class="d-flex justify-content-center align-items-center flex-column mt-5">
						 <i class="fas fa-exchange-alt fa-5x text-muted"></i>
						 <h3 class="text-muted"><?= __('admin.no_data_found') ?></h3>
					 </div>
					</div>
				<?php } else { ?>         
					<div class="table-responsive">
						<table class="table table-sortable wallet-table">
							<thead>
								<tr>
									<th scope="col">#</th>
									<th scope="col"><?= __('user.comment') ?></th>
									<th scope="col"><?= __('user.order_total') ?></th>
									<th scope="col"><?= __('user.commission') ?></th>
									<th scope="col"><?= __('user.date') ?></th>
									<th scope="col"><?= __('user.type') ?></th>
									<th scope="col"><?= __('user.status') ?></th>
								</tr>
							</thead>
							<tbody>
								<?= $table ?>
							</tbody>
						</table>
					</div>
				<?php } ?>
			</div>
		</div>
	</div>
</div>
<div class="clearfix"></div><br>