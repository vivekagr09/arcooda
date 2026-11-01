<div class="card">
	<div class="card-header bg-secondary text-white">
		<h5><?= __('admin.menu_report_all_transactions') ?></h4>
	</div>
	<div class="card-body">	
                      <?php if ($transaction ==null) {?>
						<div class="d-flex justify-content-center align-items-center flex-column mt-5">
						    <i class="fas fa-exchange-alt fa-5x text-muted"></i>
						    <h3 class="text-muted"><?= __('admin.no_data_found') ?></h3>
						</div>
                      <?php }
                      else {?>
                      
                	<div class="table-responsive" >
                    <table class="table table-striped btn-part admtrans">
				<thead>
					<tr>
						<th></th>
						<th class="sortTr <?= sort_order('admin.username') ?>"><a href="<?= sortable_link('ReportController/admin_transaction','admin.username') ?>"><?= __('admin.username') ?></a></th>
						<th class="sortTr <?= sort_order('wallet.amount') ?>"><a href="<?= sortable_link('ReportController/user_transaction','wallet.amount') ?>"><?= __('admin.commission') ?></a></th>
						<th class="sortTr <?= sort_order('wallet.comm_from') ?>"><a href="<?= sortable_link('ReportController/user_transaction','wallet.comm_from') ?>"><?= __('admin.comm_from') ?></a></th>
						<th class="sortTr <?= sort_order('wallet.type') ?>"><a href="<?= sortable_link('ReportController/user_transaction','wallet.type') ?>"><?= __('admin.type') ?></a></th>
						<th width="220px" style="width: 200px;"><?= __('admin.order_total') ?></th>
						<th width="220px"><?= __('admin.payment_method') ?></th>
						<th ><?= __('admin.comment') ?></th>
						<th class="sortTr <?= sort_order('wallet.status') ?> text-center"><a href="<?= sortable_link('ReportController/user_transaction','wallet.status') ?>"><?= __('admin.status') ?></a></th>
						<th class="sortTr <?= sort_order('wallet.created_at') ?> text-center"><a href="<?= sortable_link('ReportController/user_transaction','wallet.created_at') ?>"><?= __('admin.date') ?></a></th>
					</tr>
				</thead>
							
				<tbody>
				<?php foreach ($transaction as $key => $value) { ?>
				<tr>
					<td><?= $key + 1 ?></td>
					<td><?php echo $value['username'] ?></td>			
						<td><?= $value['amount'] ?></td>
						<td><?= $value['comm_from'] ?></td>
						<td><?= $value['dis_type'] ?></td>
						<td>
							<?php if($value['integration_orders_total']){ ?>
								<?= c_format($value['integration_orders_total']) ?>
							<?php } else { ?>
								<small class="text-muted"><?= __('admin.not_available') ?></small>
							<?php } ?>
						</td>
						<td width="220px"><?= (!empty($value['payment_method'])) ? __('admin.'.strtolower(str_replace(' ','_',$value['payment_method']))) : '<small class="text-muted">'.__('admin.not_available').'</small>' ?></td>
						<td width="220px" class="textwrap">
							<?php
								list($message,$ip_details) = parseMessage($value['comment'],$value,'usercontrol',true, false);
								echo $message."&nbsp;"; 
								echo (!empty($ip_details)) ? '<i style="font-size:18px;" class="ip-details-flag fa fa-info-circle mt-1" aria-hidden="true"></i><div class="ip-details-flag-details" style="display:none">'.$ip_details.'</div>' : "";
							?>
						</td>
						<td class="text-center">
							<?= $value['status_icon'] ?>		
						</td>
						<td class="text-center"><?= $value['created_at'] ?></td>
				</tr>
				<?php } ?>
				</tbody>
				<tfoot>
					<tr>
						<td colspan="100%" class="text-right">
							<div class="pagination">
								<?= $pagination ?>
							</div>
						</td>
					</tr>
				</tfoot>
			</table>
		</div>
		<?php } ?>
	</div>
</div>
