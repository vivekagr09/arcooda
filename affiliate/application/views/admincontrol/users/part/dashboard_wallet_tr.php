<tr class="transaction-table-tr <?= ($class == 'child' || $class == 'child-recurring') ? 'child-row' : '' ?> wallet-id-<?= $value['id'] ?> <?= $recurring ? 'recurring recurringof-'.$recurring : '' ?>" group_id='<?= $value['group_id'] ?>' data-id="<?= $value['id'] ?>">

	<td>
		<div class="no-wrap"><?= dateFormat($value['created_at'],'d F Y') ?></div>
		<div class="no-wrap">
			<small class="badge bg-secondary"><?= __('admin.id') ?>: <?= $value['id'] ?></small>
		</div>
	</td>

	<td>
		<div class="no-wrap">
			<?php if($value['is_vendor']){ ?>
				<small class="badge bg-secondary"><?= __('admin.vendor') ?></small>
			<?php } else { ?>
				<small class="badge bg-secondary"><?= __('admin.admin') ?></small>
			<?php } ?>
		</div>
	</td>

	<td>
		<?php echo $value['username']; ?>
		<div>
			<small class="badge bg-secondary"><?= wallet_whos_commission($value) ?></small>
		</div>	

	</td>
	<td>
		<?= $order_type = wallet_ex_type($value,$class) ?>
		<?php if(!$order_type){ ?>
			<?= wallet_type($value) ?>
		<?php } ?>
		<?php if(!$value['parent_id'] && $class != "child"){
				if(($value['type'] == 'sale_commission' || $value['type'] == 'admin_sale_commission' || $value['type'] == 'vendor_sale_commission') && ($value['comm_from'] == 'store' || $value['comm_from'] == 'ex') && !empty($value['reference_id_2'])){ ?>
				
				<button class="hover-danger wallet-btn ml-2 mr-0 view-tran-details" data-ref_id_1="<?= $value['reference_id'] ?>" data-ref_id_2="<?= $value['reference_id_2'] ?>" data-comm_from="<?= $value['comm_from'] ?>">
				<i class="fa fa-info-circle" style="font-size:24px; overflow: hidden; vertical-align: middle;"></i>
				</button>
		<?php }
		} ?>

		<?php if($class != 'child' && $class != 'child-recurring'): ?>
			<div>
				<?php if($value['integration_orders_total']){ ?>
					<span class="badge bg-secondary payment-method fs-6"><?= c_format($value['integration_orders_total']) ?></span>
				<?php } ?>
				<?php if($value['local_orders_total']){ ?>
					<span class="badge bg-secondary payment-method fs-6"><?= c_format($value['local_orders_total']) ?></span>
				<?php } ?>

				<?php if($value['payment_method']){ ?>
				 	<span class="badge bg-secondary payment-method fs-6"><?= payment_method($value['payment_method']) ?></span>
				<?php } ?>
			</div>
		<?php endif ?>
	</td>
	<td style="min-width: 180px !important; vertical-align: middle;">
		<div class="no-wrap">
			<div class="dpopver-content d-none">
				<?php
					list($message, $ip_details) = parseMessage($value['comment'],$value,'admincontrol',true);
					echo "<div>". $message ."</div>";
				?>
			</div>
			<div class="badge bg-<?= is_need_to_pay($value) ? 'danger' : 'secondary' ?> py-1 pl-2 font-14 fs-6" toggle="popover">
			    <?= c_format($value['amount']) ?>
			</div>
			<button toggle="popover" class="wallet-popover btn btn-info btn-lg">
			    <i class="bi bi-info-circle-fill"></i>
			</button>
			<?php
				$ip_details2 = json_decode($value['ip_details'], true);
			?>
			<ul class="ip-list list-inline float-right mb-0">
				<li class="list-inline-item dropdown">
					<a href="javascript:void(0)" title="" data-bs-toggle="dropdown" aria-expanded="false">
						<i class="fa fa-align-justify"></i>
					</a>

					<ul class="dropdown-menu country-dropdown">
						<?php foreach ($ip_details2 as  $ip2) { ?>
					    	<li>
					    		<span class="flag"><i class="flag-sm m-auto d-block flag-sm-<?= strtoupper($ip2['country_code']) ?>"></i> </span>
					    		<span class="ip"> <?= $ip2['country_code'] ?> <?= (!empty($ip2['ip'])) ? $ip2['ip'] : '<i>'.__('admin.ip_not_available').'</i>' ?></span>
					    	</li>
					    <?php } ?>
					  </ul>
				</li>
			</ul>
		</div>
	</td>

	<?php $wallet_type_action = wallet_type($value, 'code'); ?>

	<td><div class="transaction-type"><?= $wallet_type_action; ?></div></td>

	<td class="text-center z-index-1">
		<?php
		if($value['user_id'] == 1 && $value['status'] == 1)
			$value['status'] = 3;

		if(!isset($hideStaticStatus)) { 
			$id = (!empty($child_id) && $value['amount'] < 0) ? $child_id : $value['id'];

			$req_query = $this->db->query("SELECT * from wallet_requests WHERE FIND_IN_SET($id,tran_ids)");
			$req_query = $req_query->row_array();

			if($value['amount'] < 0 && isset($req_query) && is_array($req_query) && ! sizeof($req_query) > 0) {
				$goups_res = $this->db->query("SELECT id from wallet WHERE group_id=".$value['group_id']."")->result();
				foreach ($goups_res as $res) {
					$req_query = $this->db->query("SELECT * from wallet_requests WHERE FIND_IN_SET(".$res->id.",tran_ids)");
					$req_query = $req_query->row_array();
					if(sizeof($req_query) > 0) {break;}
				}
			}

			if($req_query['status'] != ''){
				$fixed_status = array(2,3,4,5,7,8,9,10,11,12,13);

				if(in_array(intval($req_query['status']), $fixed_status, TRUE)){
					echo withdrwal_status($req_query['status']);
				} else {
					if($value['commission_status'] == 0)
				 		echo $status_icon[$value['status']];
				}	
			} else {
				if($value['commission_status'] == 0)
			 		echo $status_icon[$value['status']];
			}
		 
			echo commission_status($value['commission_status']);
		 } ?>
	</td>

	

	<td>
		<?php
			if($value['user_id'] == 1) {
				echo '<span class="badge bg-success text-white px-2 py-1 fs-6">' . __('admin.paid') . '</span>';
			
			} else {
				if(isset($req_query) && sizeof($req_query) > 0)
				{
					echo withdrwal_status($req_query['status']);
				}
				else
				{
					echo wallet_paid_status($value['status']);
				}
			}
		?>
	</td>
</tr>


