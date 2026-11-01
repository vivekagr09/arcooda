<?php foreach ($tools as $key => $tool) { ?>
	<tr>
		<td>
		<div class="modal fade" id="campaign-details-<?= $tool['id']; ?>" tabindex="-1">
		    <div class="modal-dialog">
		        <div class="modal-content">
		            <div class="modal-header">
		                <h5 class="modal-title"><?= ucwords(strtolower($tool['name'])) ?></h5>
		                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
		            </div>
			      <div class="modal-body">
			        <h6><?= __('admin.tool_type') ?> : <?= parseIntegrationType($tool['_tool_type']); ?></h6>
                    <?php if($tool['_tool_type'] == 'program'){ ?>
                    <h6><?= __('admin.integration_plugin_name') ?> : <?= isset($integration_plugins[$tool['tool_integration_plugin']]) ? $integration_plugins[$tool['tool_integration_plugin']]['name'] : "<span class='text-muted'>".__('admin.not_available_dashed')."</span>" ?></h6>

                    <?php } ?>
			        <div>
			        	<?php if ($tool['_tool_type'] == 'action' || $tool['_tool_type'] == 'single_action') { ?>
							<div class="mb-2"><h6 class="my-0 font-weight-normal-bold"><?= __('admin.action_click') ?></h6>
							<?php 
								if($tool['vendor_id']){
									echo __('admin.affiliate_will_get') . " : ";
								}  else{
									echo __('admin.affiliate_will_get') . ": ";
								}
								echo c_format($tool["action_amount"]). " ".__('admin.per')." ". $tool['action_click'] ." ".__('admin.actions')." <br>";
								if($tool['vendor_id']){
									echo __('admin.admin_will_get') ." : ";
									echo c_format($tool["admin_action_amount"]). " ".__('admin.per')." ". $tool['admin_action_click'] ." ".__('admin.actions')." <br>";
								}
							?></div>
						<?php } ?>

						<?PHP if($tool['_tool_type'] == 'general_click') { ?>
							<div class="mb-2"><h6 class="my-0 font-weight-normal-bold"><?= __('admin.general_click') ?></h6>
							<?php 
								if($tool['vendor_id']){
									echo __('admin.affiliate_will_get') . " : ";
								}  else{
									echo __('admin.affiliate_will_get') .  ": ";
								}
								echo c_format($tool["general_amount"]). " ".__('admin.per')." ". $tool['general_click'] ." ".__('admin.clicks')." <br>";

								if($tool['vendor_id']){
									echo __('admin.admin_will_get') . " : ";
									echo c_format($tool["admin_general_amount"]). " ".__('admin.per')." ". $tool['admin_general_click'] ." ".__('admin.clicks')." <br>";
								}
							?></div>
						<?php } ?>

						<?php if($tool['_tool_type'] == 'program' && $tool['click_status']) { ?>
							<div class="mb-2"><h6 class="my-0 font-weight-normal-bold"><?= __('admin.product_click') ?></h6>
							<?php 
								if($tool['vendor_id']){
									echo __('admin.affiliate_will_get') . " : ";
								}  else{
									echo __('admin.affiliate_will_get') .  ": ";
								}
								
								echo c_format($tool["commission_click_commission"]). " ".__('admin.per')." ". $tool['commission_number_of_click'] ." ".__('admin.clicks')." <br>";

								if($tool['vendor_id']){
									echo __('admin.admin_will_get') . " : ";
									echo c_format($tool["admin_commission_click_commission"]). " ".__('admin.per')." ". $tool['admin_commission_number_of_click'] ." ".__('admin.clicks')." <br>";
								}
							?></div>
						<?php } ?> 
						<?php if($tool['_tool_type'] == 'program' && $tool['sale_status']) { ?>
							<div class="mb-2"><h6 class="my-0 font-weight-normal-bold"><?= __('admin.sale_commisssion') ?></h6>
							<?php 
								$comm = '';
								if($tool['commission_type'] == 'percentage'){ $comm = $tool['commission_sale'].'%'; }
								else if($tool['commission_type'] == 'fixed'){ $comm = c_format($tool['commission_sale']); }
								
								if($tool['vendor_id']){
									echo __('admin.affiliate_will_get') . ":{$comm} <br>";
								} else{
									echo __('admin.affiliate_will_get') . ": {$comm} <br>";
								}
								if($tool['vendor_id']){
									$comm = '';
									if($tool['admin_commission_type'] == 'percentage'){ $comm = $tool['admin_commission_sale'].'%'; }
									else if($tool['admin_commission_type'] == 'fixed'){ $comm = c_format($tool['admin_commission_sale']); }

									echo __('admin.admin_will_get') . " : {$comm} <br>";
								}
							?></div>
						<?php } ?>
			        </div>
			        <hr/>
			        <h6><?= __('admin.transactions_details') ?>:</h6>
			        <div>
			        	<?php if($tool['_tool_type'] == 'action' || $tool['_tool_type'] == 'single_action') { ?>
							<div class="mb-2"><h6 class="my-0 font-weight-normal-bold"><?= __('admin.action_click') ?></h6>
							<?php 
								echo __('admin.admin_count').": ". (int)$tool['total_action_click_count'] ."<br>";
								echo __('admin.admin_amount').": ". $tool['total_action_click_amount'];
							?> </div>
						<?php } ?>
						<?PHP if($tool['_tool_type'] == 'general_click') { ?>
							<div class="mb-2"><h6 class="my-0 font-weight-normal-bold"><?= __('admin.general_click') ?></h6>
							<?php 
								echo __('admin.admin_count').": ". (int)$tool['total_general_click_count'] ."<br>";
								echo __('admin.admin_amount').": ". $tool['total_general_click_amount'];
							?> </div>
						<?php } ?>
						<?php if($tool['_tool_type'] == 'program' && $tool['click_status']) { ?>
							<div class="mb-2"><h6 class="my-0 font-weight-normal-bold"><?= __('admin.product_click') ?></h6>
							<?php 
								echo __('admin.admin_count').": ". (int)$tool['total_click_count'] ."<br>";
								echo __('admin.admin_amount').": ". $tool['total_click_amount'];
							?> </div>
						<?php } ?>

						<?php if($tool['_tool_type'] == 'program' && $tool['sale_status']) { ?>
							<div class="mb-2"><h6 class="my-0 font-weight-normal-bold"><?= __('admin.sale_commisssion') ?></h6>
							<?php 
								echo __('admin.sales').": ". (int)$tool['total_sale_count'] ."<br>";
								echo __('admin.admin_amount').": ". $tool['total_sale_amount'];
							?> </div>
						<?php } ?>
			        </div>
			        <hr/>
			        <h6><?php echo __('admin.view') ?>: <?= $tool['total_trigger_count'] ?></h6>
			        <h6>
			        	<?php 

			        	if($tool['_tool_type'] == 'action' || $tool['_tool_type'] == 'single_action')
			        	{
			        		$conversionratio=0;
			        		$totalratiocount=(int)$tool['total_action_click_count'];
			        		if($tool['total_trigger_count']>0)
			        			$conversionratio=(int)($totalratiocount*100/$tool['total_trigger_count']);
			        		$conversionratio = is_float($conversionratio) ==1 ? number_format((float)$conversionratio, 2, '.', '') : $conversionratio;
			        		echo __('admin.action_ratio'). ' : '. $conversionratio . '%<br/>';
			        		
			        	}
			        	if($tool['_tool_type'] == 'general_click')
			        	{
			        		$conversionratio=0;
			        		$totalratiocount=(int)$tool['total_general_click_count'];
			        		if($tool['total_trigger_count']>0)
			        			$conversionratio=(int)($totalratiocount*100/$tool['total_trigger_count']);
			        		$conversionratio = is_float($conversionratio) ==1 ? number_format((float)$conversionratio, 2, '.', '') : $conversionratio;
			        		echo __('admin.click_ratio'). ' : '. $conversionratio . '%<br/>';
			        	}
			        	if($tool['_tool_type'] == 'program' && $tool['click_status']) 
			        	{
			        		$conversionratio=0;
			        		$totalratiocount=(int)$tool['total_click_count'];
			        		if($tool['total_trigger_count']>0)
			        			$conversionratio=(int)($totalratiocount*100/$tool['total_trigger_count']);
			        		$conversionratio = is_float($conversionratio) ==1 ? number_format((float)$conversionratio, 2, '.', '') : $conversionratio;
			        		echo __('admin.product_click_ratio'). ' : '. $conversionratio . '%<br/>';
			        	}
			        	if($tool['_tool_type'] == 'program' && $tool['sale_status'])  
			        	{
			        		$conversionratio=0;
			        		$totalratiocount=(int)$tool['total_sale_count'];
			        		if($tool['total_trigger_count']>0)
			        		$conversionratio=(int)($totalratiocount*100/$tool['total_trigger_count']);
			        		$conversionratio = is_float($conversionratio) ==1 ? number_format((float)$conversionratio, 2, '.', '') : $conversionratio;
			        		echo __('admin.sale_ratio'). ' : '. $conversionratio . '%<br/>';
			        	}
  						
			        	 ?> 
			        </h6>
			        <hr/>
			        <h6><?php echo __('admin.group') ?>:</h6>
			        <div>
			        	<?php

						$groupInfo = "";

						if(empty($tool['groups'])) {
							$groupInfo = '<span class="badge bg-secondary">'.__('admin.groups_not_assigned').'!</span>';
						} else {
							$groups = explode(',', $tool['groups']);
							foreach($groups as $g) {
								$groupInfo .= '<span class="badge bg-primary mx-1">'.$g.'</span>';
							}
						}

						?>
			        	<?= $groupInfo ?>
			        </div>
			      </div>
				<div class="modal-footer justify-content-between">
				    <img width="45px" height="45px" src="<?php echo base_url('assets/images/product/upload/thumb/'. $tool['featured_image']) ?>" >
				    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?= __('admin.close') ?></button>
				</div>
			    </div>
			  </div>
			</div>
			<img class="campaign-img" src="<?php echo base_url('assets/images/product/upload/thumb/'. $tool['featured_image']) ?>" >
		</td>
		
		<td>
			<?= $tool['username'] ? ucwords($tool['username']) : __('admin.admin') ?>
		</td>

		<td class="max-width-300">
			<?php
			$printableToolName = ucwords(strtolower($tool['name']));
			$printableToolName = strlen($printableToolName) > 50 ? substr($printableToolName,0,50)."..." : $printableToolName;
			echo $printableToolName;
			?>
			 
		</td>

<td>
    <?= isset($integration_plugins[$tool['tool_integration_plugin']]) ? 
        $integration_plugins[$tool['tool_integration_plugin']]['name'] : 
        '<span">' . parseIntegrationType($tool['_tool_type']) . '</span>'; ?>
</td>



		<td class="text-center">
			<?= $tool['created_at'] ?>
		</td>
		<td class="text-center">
			<?= $tool['total_trigger_count'] ?>
		</td>
		<td>
			<?php 
	        	if($tool['_tool_type'] == 'action' || $tool['_tool_type'] == 'single_action')
	        	{
	        		$conversionratio=0;
	        		$totalratiocount=(int)$tool['total_action_click_count'];
	        		if($tool['total_trigger_count']>0)
	        			$conversionratio=(int)($totalratiocount*100/$tool['total_trigger_count']);
	        		$conversionratio = is_float($conversionratio) ==1 ? number_format((float)$conversionratio, 2, '.', '') : $conversionratio;
	        		echo __('admin.action'). ' : '. $conversionratio . '%<br/>';
	        		
	        	}
	        	if($tool['_tool_type'] == 'general_click')
	        	{
	        		$conversionratio=0;
	        		$totalratiocount=(int)$tool['total_general_click_count'];
	        		if($tool['total_trigger_count']>0)
	        			$conversionratio=(int)($totalratiocount*100/$tool['total_trigger_count']);
	        		$conversionratio = is_float($conversionratio) ==1 ? number_format((float)$conversionratio, 2, '.', '') : $conversionratio;
	        		echo __('admin.click'). ' : '. $conversionratio . '%<br/>';
	        	}
	        	if($tool['_tool_type'] == 'program' && $tool['click_status']) 
	        	{
	        		$conversionratio=0;
	        		$totalratiocount=(int)$tool['total_click_count'];
	        		if($tool['total_trigger_count']>0)
	        			$conversionratio=(int)($totalratiocount*100/$tool['total_trigger_count']);
	        		$conversionratio = is_float($conversionratio) ==1 ? number_format((float)$conversionratio, 2, '.', '') : $conversionratio;
	        		echo __('admin.product_click'). ' : '. $conversionratio . '%<br/>';
	        	}
	        	if($tool['_tool_type'] == 'program' && $tool['sale_status'])  
	        	{
	        		$conversionratio=0;
	        		$totalratiocount=(int)$tool['total_sale_count'];
	        		if($tool['total_trigger_count']>0)
	        		$conversionratio=(int)($totalratiocount*100/$tool['total_trigger_count']);
	        		$conversionratio = is_float($conversionratio) ==1 ? number_format((float)$conversionratio, 2, '.', '') : $conversionratio;
	        		echo __('admin.sale'). ' : '. $conversionratio . '%<br/>';
	        	}
 
	        	 ?>
		</td>

		<td class="text-center security-status">
			<?= ads_security_status($tool['security_status']); ?>
		</td>

		<!--CODE ICONS-->
		<td class="text-center">
			<button data-bs-toggle="tooltip" title="<?php echo __('admin.integration_code') ?>" class="btn-show-code btn btn-sm btn-primary" data-id='<?= $tool['id'] ?>'>
				<i class="bi bi-code" aria-hidden="true"></i>
			</button>
		</td>
		<!--CODE ICONS-->

		<td>
			<?= ads_status($tool['status']); ?>
			<?php if($tool['status'] == 1):
				$isRunning = $tool['status'] == 1 ? 1 : 0;

				$isRunningTooltip = __('admin.lifetime');

				if(!empty($tool['start_date']) && $tool['start_date'] != "0000-00-00 00:00:00") {
					$startDateAvailable = date('d M Y H:i A', strtotime($tool['start_date']));
					if(time() < strtotime($tool['start_date'])) {
						$isRunning = 0;
					}
				} else {
					$startDateAvailable = date('d M Y H:i A', strtotime($tool['created_at']));
				}

				if(!empty($tool['end_date']) && $tool['end_date'] != "0000-00-00 00:00:00") {
					$endDateAvailable = date('d M Y H:i A', strtotime($tool['end_date']));
					if(time() > strtotime($tool['end_date'])) {
						$isRunning = 2;
					}
				} else {
					$endDateAvailable = __('admin.lifetime');
				} ?>

				<button type="button" class="btn btn-sm btn-primary cursor" data-bs-toggle="tooltip" 
				        title="<?= $startDateAvailable ?> To <?= $endDateAvailable ?>" 
				        class="bg-<?= ads_running_status($isRunning); ?>">
				    <i class="bi bi-clock" aria-hidden="true"></i>
				</button>

			<?php endif ?>
		</td>

		<!--INFO ICONS-->
		<td class="text-center">
			<?php if($tool['main_commission_type'] != 'disabled'): ?>
				<button data-bs-toggle="tooltip" title="<?php echo __('admin.integration_mlm_info') ?>" class="btn-show-integration-mlm-info btn btn-sm btn-primary" data-id='<?= $tool['id'] ?>'>
					<i class="fas fa-network-wired"></i>
				</button>
			<?php endif ?>
			
			<button class="btn btn-sm btn-primary btn-campaign-detail" data-bs-toggle="tooltip" title="<?php echo __('admin.campaign_info') ?>" data-toggle="modal" data-id="<?= $tool['id']; ?>" data-target="#campaign-details-<?= $tool['id']; ?>">
				<i class="fa fa-info-circle" aria-hidden="true"></i>
			</button>

			<button data-bs-toggle="tooltip" title="<?php echo __('admin.terms') ?>"  class="btn-show-terms btn btn-sm btn-primary" data-id='<?= $tool['id'] ?>'>
				<i class="fa fa-file-alt" aria-hidden="true"></i>
			</button>
		</td>
		<!--INFO ICONS-->



		<!--ACTION ICONS-->
		<td class="text-center">
			<a data-bs-toggle="tooltip" title="<?php echo __('admin.validate_campaign') ?>" href="javascript:void(0);" class="btn btn-sm btn-primary check-campaign-with-id" data-id="<?= $tool['id'] ?>">
				<i class="fa fa-shield" aria-hidden="true"></i>
			</a>
			<a data-bs-toggle="tooltip" title="<?php echo __('admin.duplicate') ?>" class="btn btn-sm btn-primary" href="<?= base_url('integration/integration_tools_duplicate/'. $tool['id']) ?>">
				<i class="fa fa-copy cursors" aria-hidden="true"></i>
			</a>
			<a data-bs-toggle="tooltip" title="<?php echo __('admin.edit') ?>" class="btn btn-sm btn-primary" onclick="return confirm('<?= __('admin.are_you_sure_to_edit') ?>');" href="<?= base_url('integration/integration_tools_form/'. $tool['_type'] .'/' . $tool['id']) ?>">
				<i class="fa fa-edit cursors" aria-hidden="true"></i>
			</a>
			<a data-bs-toggle="tooltip" title="<?php echo __('admin.delete') ?>" class="btn btn-sm btn-danger tool-remove-link" href="<?= base_url('integration/integration_tools_delete/'. $tool['id']) ?>">
				<i class="fa fa-trash-o cursors" aria-hidden="true"></i>
			</a>
		</td>
		<!--ACTION ICONS-->
	</tr>
<?php } ?>
