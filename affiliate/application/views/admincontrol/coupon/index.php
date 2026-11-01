<div class="row">
<div class="col-12">
	<div class="card m-b-30">
		<div class="card-header">
			<h4 class="card-title pull-left"><?= __('admin.coupon') ?></h4>
			<div class="pull-right">
				<a class="btn btn-primary" href="<?= base_url('admincontrol/coupon_manage/')  ?>"><?= __('admin.add_new'); ?></a>
			</div>
		</div>
		<div class="card-body">
			<div class="table-rep-plugin">
			    
			    <?php if ($coupons == null) {?>
					<div class="text-center mt-5">
					 <div class="d-flex justify-content-center align-items-center flex-column mt-5">
						 <i class="fas fa-exchange-alt fa-5x text-muted"></i>
						 <h3 class="text-muted"><?= __('admin.no_data_found') ?></h3>
					 </div>
					</div>
                    <?php }
                 else {?>
           
				<div class="table-responsive b-0" data-pattern="priority-columns">
					<table id="tech-companies-1" class="table  table-striped">
						<thead>
							<tr>
								<th ><?= __('admin.coupon_name'); ?></th>
								<th width="100px"><?= __('admin.count_product_use'); ?></th>
								<th width="100px"><?= __('admin.uses_total'); ?></th>
								<th width="100px"><?= __('admin.code'); ?></th>
								<th width="100px"><?= __('admin.discount'); ?></th>
								<th width="50px"><?= __('admin.date_start'); ?></th>
								<th width="50px"><?= __('admin.date_end'); ?></th>
								<th width="50px"><?= __("admin.status") ?></th>
								<th width="180px"></th>
							</tr>
						</thead>
						<tbody>
							<?php foreach($coupons as $coupon){ ?>
								<tr>
									<td><?= $coupon['name'] ?></td>
									<td><?= (int)$coupon['product_count'] .' / '. (int)$coupon['count_coupon'] ?></td>
									<td><?= $coupon['uses_total'] ?></td>
									<td><?= $coupon['code'] ?></td>
									<td><?= $coupon['type']=="P" ? getDecimalNumberFormat($coupon['discount'],$_SESSION['userDecimalPlace']).' %' : c_format($coupon['discount']) ?></td>
									<td><?= dateGlobalFormat($coupon['date_start']) ?></td>
									<td><?= dateGlobalFormat($coupon['date_end']) ?></td>
									<td><?= $coupon['status'] == '1' ? __("admin.enabled") : __("admin.disabled") ?></td>
									<td>
										<a href="<?= base_url('admincontrol/coupon_manage/'.$coupon['coupon_id'])  ?>" class="btn btn-primary edit-button" id="<?= $coupon['id'] ?>"><?= __("admin.edit") ?></a>
										<a href="<?= base_url('admincontrol/coupon_delete/'.$coupon['coupon_id'])  ?>" class="btn btn-danger delete-button" id="<?= $coupon['id'] ?>"><?= __("admin.delete") ?></a>
									</td>
								</tr>
							<?php } ?>
							<?php } ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>

</div>

<script type="text/javascript">
$(".delete-button").on('click',function(){
	return confirm("<?= __("admin.are_you_sure") ?>");
})
</script>