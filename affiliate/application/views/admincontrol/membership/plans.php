<?php if(!empty($license_alret)) { ?>
  <h5 class="notification_on_pages mb-3">
    <div class="bg-danger text-white p-3 rounded">
      <?= $license_alret; ?>
    </div>
  </h5>
<?php } ?>

<div class="row">
    <div class="col-12">
        <form id="form_plan">
            <div class="row">
                <div class="col-12">
                    <div class="card mb-3">
						<div class="card-header d-flex justify-content-between align-items-center bg-secondary text-white">
						    <h5><?= __('admin.plans') ?></h5>
							<a id="toggle-uploader" href="<?= base_url('membership/plan_create') ?>" class="btn btn-light btn-sm text-white"><?= __('admin.create_new') ?>
							</a>
						</div>
                        <div class="card-body p-0">
                        	<div class="table-responsive">
	                        	<table class="table table-striped mb-0">
	                        		<thead>
	                        			<tr>
	                        				<th><?= __('admin.id') ?></th>
	                        				<th><?= __('admin.name') ?></th>
	                        				<th><?= __('admin.plan_user_type') ?></th>
	                        				<th><?= __('admin.price') ?></th>
	                        				<th><?= __('admin.bonus') ?></th>
	                        				<th><?= __('admin.commission_sale_status') ?></th>
	                        				<th><?= __('admin.plan_level') ?></th>
	                        				<th><?= __('admin.type') ?></th>
	                        				<th><?= __('admin.billing_period') ?></th>
	                        				<th><?= __('admin.period_days') ?></th>
	                        				<th><?= __('admin.is_display') ?></th>
	                        				<th><?= __('admin.updated_at') ?></th>
	                        				<th><?= __('admin.action') ?></th>
	                        			</tr>
	                        		</thead>
	                        		<tbody>
	                        			<?php if(count($plans) == 0){ ?>
			                        		<tr>
			                        			<td colspan="13" class="text-center"><?= __('admin.no_records_found') ?></td>
			                        		</tr>
			                        	<?php } ?>

	                        			<?php foreach ($plans as $key => $plan) { ?>

	                        				<tr>
	                        					<td><?= $plan->id ?></td>
	                        					<td><?= $plan->name ?></td>
	                        					<td><?= ($plan->user_type == 2) ? __('admin.vendor') : __('admin.affiliate') ?></td>
	                        					<td><?= c_format($plan->price) ?></td>
	                        					<td><?= c_format($plan->bonus) ?></td>
	                        					<?php if ($award_level['status']){ ?>
	                        						<td><?= ($plan->commission_sale_status) ? __('admin.enabled') : __('admin.disabled') ?></td>
	                        						<td>
	                        							<?php
		                        							if($plan->commission_sale_status){
		                        								if($plan->plan_level)
		                        									echo $plan->plan_level;
		                        								else
		                        									echo __('admin.default');
		                        							} else {
		                        								echo __('admin.disabled');
		                        							}
	                        							?>
	                        						</td>
	                        					<?php } else { ?>
	                        						<td><?= __('admin.disabled') ?></td>
	                        						<td><?= __('admin.disabled') ?></td>
	                        					<?php } ?>
	                        					<td><?= ($plan->type == 'free') ? __('admin.free') : (($plan->type == 'paid') ? __('admin.paid') : $plan->type) ?></td>
	                        					<td><?= $plan->billing_period_plain ?></td>
	                        					<td><?= ($plan->billing_period == 'lifetime_free') ? __('admin.life_time') : $plan->total_day ?></td>
	                        					<td><?= $plan->status ? __('admin.yes') : __('admin.no') ?></td>
	                        					<td><?= dateFormat($plan->updated_at) ?></td>
	                        					<td>
	                        						<a href="javascript:void(0);" class="btn btn-info btn-sm plan-user-setting" data-user_type="<?= $plan->user_type ?>" data-campaign="<?= isset($plan->campaign) ? (int) $plan->campaign : 'NULL' ?>" data-product="<?= isset($plan->product) ? (int) $plan->product : 'NULL' ?>">
	                        							<i class="fas fa-cog"></i>
	                        						</a>
	                        						<a href="<?= base_url('membership/plan_edit/'. $plan->id) ?>" class="btn btn-primary btn-sm"><?= __('admin.edit') ?></a>
	                        						<a href="javascript:void(0)" onclick="delete_confirm('<?= base_url('membership/plan_delete/'. $plan->id) ?>')" class="btn btn-danger btn-sm"><?= __('admin.delete') ?></a>
	                        					</td>
	                        				</tr>
	                        			<?php } ?>
	                        		</tbody>
	                        	</table>
                        	</div>
                        </div>
                        <?php if($links){ ?>
	                        <div class="card-footer text-end d-flex justify-content-between align-items-center">
	                        	<div>
	                        		<?= $links[1] ?>
	                        	</div>
	                        	<div>
		                        	<ul class="pagination m-0"><?= $links[0] ?></ul>
		                        </div>
	                        </div>
                       <?php } ?>
                    </div>
                </div>
            </div>
        </form>
    </div> 
</div>


<div id="plan-user-setting" class="modal" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog" role="document">
	    <div class="modal-content">
	      	<div class="modal-header text-center">
	        	<h5 class="modal-title"><?= __('admin.plan_user_setting') ?></h5>
	      	</div>
	      	<div class="modal-body">
	    		<div class="row">
					<div class="col-sm-12">
						<div class="form-group">
							<label><?= __('admin.user_type').' : ' ?></label>
							<span class="user-type"></span>
						</div>
						<div class="form-group">
							<label><?= __('admin.campaign').' : ' ?></label>
							<span class="campaign"></span>
						</div>
						<div class="form-group">
							<label><?= __('admin.product').' : ' ?></label>
							<span class="product"></span>
						</div>
					</div>
				</div>
	      	</div>
	    	<div class="modal-footer">
		      	<button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?= __('admin.close') ?></button>
	    	</div> 
	    </div>
	</div>  	 	
</div>


<script type="text/javascript">

	function delete_confirm(url) {

		Swal.fire({

			title: '<?= __('admin.are_you_sure') ?>',

			text: "<?= __('admin.you_want_be_able_to_revert_this') ?>",

			icon: 'warning',

			showCancelButton: true,

			confirmButtonText: '<?= __('admin.yes_delete_it') ?>',

			cancelButtonText: '<?= __('admin.no_cancel') ?>',

			reverseButtons: true

		}).then((result) => {

			if (result.value) {

				window.location.href = url;

			}

		})

		return false;

	}

	$(".plan-user-setting").on('click',function(){
		if($(this).data('user_type') == 2){
			$("#plan-user-setting .user-type").text('<?= __('admin.vendor') ?>');

			let campaign = ($(this).data('campaign') != 'NULL') ? $(this).data('campaign') : '<?= __('admin.unlimited') ?>';
			$("#plan-user-setting .campaign").text(campaign).parents('.form-group').removeClass('d-none');

			let product = ($(this).data('product') != 'NULL') ? $(this).data('product') : '<?= __('admin.unlimited') ?>';
			$("#plan-user-setting .product").text(product).parents('.form-group').removeClass('d-none');;
		} else {
			$("#plan-user-setting .user-type").text('<?= __('admin.affiliate') ?>');
			$("#plan-user-setting .campaign").text('').parents('.form-group').addClass('d-none');;
			$("#plan-user-setting .product").text('').parents('.form-group').addClass('d-none');;
		}

		$("#plan-user-setting").modal("show");
	})
</script>