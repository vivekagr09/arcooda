<?php
$is_need_to_pay = is_need_to_pay($value);
if($value['parent_id'] > 0) {
	$recurring = $value['parent_id'];
}
?>

<tr class="transaction-table-tr <?= ($class == 'child' || $class == 'child-recurring') ? 'child-row' : '' ?> wallet-id-<?= $value['id'] ?> <?= $recurring ? 'recurring recurringof-'.$recurring : '' ?>" group_id='<?= $value['group_id'] ?>' <?= $recurring && ($class == 'child' || $class == 'child-recurring') ? 'style="display: none;"' : '' ?>>
	<?php if($recurring && ($class == 'child' || $class == 'child-recurring')){ ?>
		<?php if (!isset($stop_checkbox)) { ?>
			<td class="escape-middle">
				<?php if(isset($walletauto_withdrawal) && $walletauto_withdrawal==1) { } else { ?>
					<?php if ($value['status'] == '1' && !$is_need_to_pay) { ?>
					<div class="checkbox-td">
						<label>
							<input type="checkbox" class="wallet-checkbox" value="<?= $value['id'] ?>">
						</label>
					</div>
					<?php }?>
				<?php } ?>
			</td>
		<?php } ?>

		<?php if (!isset($stop_child)) { ?>
			<td class="text-center p-relative child-arrow-rec escape-middle"></td>
		<?php } ?>
	<?php } else { ?>
		<?php if (!isset($stop_checkbox)) { ?>
			<td class="escape-middle">
				<?php if(isset($walletauto_withdrawal) && $walletauto_withdrawal==1) { } else { ?>
					<?php if ($userdetails['is_vendor'] && $userdetails['id'] == $value['user_id'] 
					&& $value['status'] == '1' && $value['amount'] > 0){ ?>
						<div class="checkbox-td">
							<label>
								<input type="checkbox" class="wallet-checkbox" value="<?= $value['id'] ?>">
							</label>
						</div>
					<?php } else if(!$userdetails['is_vendor'] && $value['status'] == '1' && !$is_need_to_pay){ ?>
						<div class="checkbox-td">
							<label>
								<input type="checkbox" class="wallet-checkbox" value="<?= $value['id'] ?>">
							</label>
						</div>
					<?php } ?>
				<?php } ?>	
			</td>
		<?php } ?>

		<?php if (!isset($stop_child)) { ?>
			<td class="escape-middle text-center p-relative <?= $force_class ?> <?= $class == 'child' ? 'child-arrow' : '' ?>">
				<?php if($has_child && $class != 'child'){ ?>
					<button class="show-child-transaction"><i class="fa fa-angle-down"></i></button>
					<div class="button-line"></div>
				<?php } ?>
			</td>
		<?php } ?>
	<?php }  ?>
 
	<td>
		<div class="no-wrap"><?= dateFormat($value['created_at'],'d F Y') ?></div>
		<span class="badge bg-secondary"><?= __('user.id') ?>: <?= $value['id'] ?></span>
	</td>
	<td>
		
			<?php
			

			 if($userdetails['is_vendor'] == 1){?>

		<?php if($userdetails['id'] != $value['user_id']){?>

			<?php if(wallet_whos_commission_vendor($value,$userdetails) == __('admin.pay_to_affiliate') || wallet_whos_commission_vendor($value,$userdetails) == __('admin.pay_to_admin')){?>
				<?php echo $value['username']; ?>
				<div>
					<span class="badge bg-danger font-weight-normal"><?= wallet_whos_commission_vendor($value,$userdetails) ?></span>
				</div>

			<?php }elseif (wallet_whos_commission_vendor($value) == __('admin.vendor_earning')) {?>
				<div>
				<span class="badge bg-secondary font-weight-normal"><?= wallet_whos_commission_vendor($value,$userdetails) ?></span>
			</div>
			<?php } else{?>
				<div>
				<span class="badge bg-secondary font-weight-normal"><?= wallet_whos_commission_vendor($value,$userdetails) ?></span>
			</div>
			<?php }?>
			
		<?php }else{ ?>

			<?php if(wallet_whos_commission($value,$userdetails) == __('admin.pay_to_affiliate') || wallet_whos_commission($value,$userdetails) == __('admin.pay_to_admin')){?>
				<?php echo $value['username']; ?>
				<div>
					<span class="badge bg-danger font-weight-normal"><?= wallet_whos_commission($value,$userdetails) ?></span>
				</div>

			<?php }elseif (wallet_whos_commission($value) == __('admin.vendor_earning')) {?>
				<div>
					<span class="badge bg-secondary font-weight-normal"><?= wallet_whos_commission($value,$userdetails) ?></span>
				</div>

			<?php } else{?>
				<div>
					<span class="badge bg-secondary font-weight-normal"><?= wallet_whos_commission($value,$userdetails) ?></span>
				</div>

			<?php }?>

		<?php }?>
		<?php }else{?>
			<?php if($userdetails['id'] != $value['user_id'] && $value['is_vendor'] == 0){ ?>

				<?php if(wallet_whos_commission_affiliate($value) == __('admin.pay_to_affiliate') || wallet_whos_commission_affiliate($value) == __('admin.pay_to_admin')){?>
					<?php echo $value['username']; ?>
					<div>
						<span class="badge bg-danger font-weight-normal"><?= wallet_whos_commission_affiliate($value) ?></span>
					</div>
				<?php }elseif (wallet_whos_commission_affiliate($value) == __('admin.vendor_earning')) {?>
				<div>
					<span class="badge bg-secondary font-weight-normal"><?= wallet_whos_commission_affiliate($value) ?></span>
				</div>

			<?php } else{?>
				<div>
				<span class="badge bg-secondary font-weight-normal"><?= wallet_whos_commission_affiliate($value) ?></span>
			</div>

			<?php }?>



				
			<?php }else{ ?>

				<?php if(wallet_whos_commission($value,$userdetails['id']) == __('admin.pay_to_affiliate') || wallet_whos_commission($value,$userdetails['id']) == __('admin.pay_to_admin')){?>
					<?php echo $value['username']; ?>
					<div>
						<span class="badge bg-danger font-weight-normal"><?= wallet_whos_commission($value,$userdetails['id']) ?></span>
				     </div>

				<?php }elseif (wallet_whos_commission($value,$userdetails['id']) == __('admin.vendor_earning')) {?>
					<div>
						<span class="badge bg-secondary font-weight-normal"><?= wallet_whos_commission($value,$userdetails['id']) ?></span>
				    </div>

			<?php } else{?>

				<div>
					<span class="badge bg-secondary font-weight-normal"><?= wallet_whos_commission($value,$userdetails['id']) ?></span>
				</div>

			<?php }?>
				
			<?php }?>
			
		<?php }?>
			
	</td>
	<td>
		<?= $order_type = wallet_ex_type($value) ?>
		<?php if(!$order_type) { ?>
			<?= wallet_type($value) ?>
		<?php } ?>
		<?php 
		if(($value['type'] == 'sale_commission' || $value['type'] == 'vendor_sale_commission' || $value['type'] == 'admin_sale_commission') && $value['comm_from'] == 'store' && !empty($value['reference_id_2']))
		{

			$product = $this->db->query('SELECT product_name,product_slug,is_campaign_product,product_id FROM product WHERE product_id='.$value['reference_id'])->row();
			if($product->is_campaign_product==1){
				$productLink = base_url('store/product/'.$product->product_id);
			} else {
				$productLink = base_url('store/'. base64_encode($userdetails['id']) .'/product/'.$product->product_slug);

			}
			echo " - <a target=\"_blank\" href=\"".$productLink."\">".substr($product->product_name, 0, 10);
			echo (strlen($product->product_name) > 10) ? "..." : "";
			echo "</a>";
		}
		?>
		<div>
			<?php if($value['integration_orders_total']){ ?>
				<small class="badge bg-secondary payment-method"><?= c_format($value['integration_orders_total']) ?></small>
			<?php } ?>
			<?php if($value['local_orders_total']){ ?>
				<small class="badge bg-secondary payment-method"><?= c_format($value['local_orders_total']) ?></small>
			<?php } ?>

			<?php if($value['payment_method']){ ?>
				<small class="badge bg-secondary payment-method"><?= payment_method($value['payment_method']) ?></small>
			<?php } ?>
		</div>
	</td>

	<td style="width: 180px !important; vertical-align: middle;">
		<div class="no-wrap">
			<div class="dpopver-content d-none">
				<?php
				list($message,$ip_details) = parseMessage($value['comment'],$value,'usercontrol',true);
				echo "<div>". $message ."</div>";
				?>
			</div>
			<div 
			class="badge bg-<?= $is_need_to_pay ? 'danger' : 'secondary' ?> py-1 pl-2 font-14" 
			toggle="popover"
			> 
			<?= c_format($value['amount']) ?> 
		</div>

		<?php
		
		list($message,$ip_details) = parseMessage($value['comment'],$value,'usercontrol',true);
		?>
		
		<button data-bs-toggle="popover" data-bs-html="true"  title="" data-bs-content="<?=  htmlspecialchars($message,ENT_QUOTES);?>" class="wallet-popover btn-wallet-info">
			<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-info"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
		</button>
		
		<?php
		$ip_details = json_decode($value['ip_details'], true);
		?>
		<ul class="ip-list list-inline float-right mb-0">
			<li class="list-inline-item dropdown">
				<a href="javascript:void(0)" title="" data-toggle="dropdown" aria-expanded="false">
					<i class="fa fa-align-justify"></i>
				</a>
				<ul class="dropdown-menu country-dropdown">
					<?php foreach ($ip_details as  $ip) { ?>
						<li>
							<span class="flag"><i class="flag-sm m-auto d-block flag-sm-<?= strtoupper($ip['country_code']) ?>"></i> </span>
							<span class="ip"> <?= $ip['country_code'] ?> <?= (!empty($ip['ip'])) ? $ip['ip'] : '<i>'.__('user.ip_not_available').'</i>' ?></span>
						</li>
					<?php } ?>
				</ul>
			</li>
		</ul>
	</div>
</td>

<td>
	<div class="transaction-type">
		<?= wallet_type($value, 'code') ?>
		<?php if($recurring){ ?> - <?= __('user.recurring') ?> <?php } ?>
	</div>
</td>

<td>

	<?php 
	if(($value['user_id'] == 1 || $value['from_user_id'] == 1) && $value['status'] == 1) {
		$value['status'] = 3;
	}
	?>

	<?php

	$id = (!empty($child_id) && $value['amount'] < 0) ? $child_id : $value['id'];

	$req_query = $this->db->query("SELECT * from wallet_requests WHERE FIND_IN_SET($id,tran_ids)");
	$req_query = $req_query->row_array();

	if($value['amount'] < 0 && isset($req_query) && ! sizeof($req_query) > 0) {
		$goups_res = $this->db->query("SELECT id from wallet WHERE group_id=".$value['group_id']."")->result();
		foreach ($goups_res as $res) {
			$req_query = $this->db->query("SELECT * from wallet_requests WHERE FIND_IN_SET(".$res->id.",tran_ids)");
				$req_query = $req_query->row_array();
				if(sizeof($req_query) > 0) {break;}
			}
		}

		if(isset($req_query) && sizeof($req_query) > 0)
		{
			echo withdrwal_status($req_query['status']);
		}
		else
		{
			echo wallet_paid_status($value['status']);
		}
		?> 
	</td>
	
	<td>
		<?php

		if($req_query['status'] != '')
		{
			$fixed_status = array(2,3,4,5,7,8,9,10,11,12,13);

			if(in_array(intval($req_query['status']), $fixed_status, TRUE))
			{
				echo withdrwal_status($req_query['status']);
			}
			else
			{
				if($value['commission_status'] == 0)
				{
					echo $status_icon[$value['status']];
				}	
			}	
		}
		else
		{
			if($value['commission_status'] == 0)
			{
				echo $status_icon[$value['status']];
			}	
		}

		echo commission_status($value['commission_status']);
		?>
	</td>
	<td>
	<?php if($userdetails['is_vendor'] && $userdetails['id']== $product_user): ?>
		
			<?php if(($value['comm_from'] == 'ex' && $value['is_vendor'] && empty($value['is_action']) 
				&& $value['reference_id_2'] != '__general_click__'  && $market_vendor['marketvendorexternalordercampaign'])
			||  ($value['is_action'] && $value['is_vendor'] && $market_vendor['marketvendoractionscampaign'])
			||  ($value['is_vendor'] && $value['reference_id_2'] == '__general_click__' && $market_vendor['marketvendorclickcampaign'])
		): ?>	
		<select id="tran-<?=$value['id']?>" class="form-control change-status" 
			onchange="changeStatus(this,'<?= $value['id'] ?>','<?= $value['status'] ?>');" >

			<option value="" disabled selected><?= __('user.select_status') ?></option>

			<option <?= ($req_query['status'] == 1) ? "disabled" : "" ?>
			value="1" data-type="comission"><?= __('user.cancel') ?></option>

			<option <?= ($req_query['status'] == 1) ? "disabled" : "" ?>  
			value="2" data-type="comission"><?= __('user.trash') ?></option>

			<option value="0" data-type="wallet"><?= __('user.on_hold') ?></option>

			<option value="1" data-type="wallet"><?= __('user.in_wallet') ?></option>

			<option value="" data-type="remove"><?= __('user.remove') ?></option>

			<?php if(!$value['parent_id'] && $class != 'child'): ?>
				<option value="" data-type="recursion"><?= __('user.recursion') ?></option>
			<?php endif ?>		
		</select>
	<?php endif ?>	

<?php endif ?>
</td>
<td class="text-right">
	<?php if(!isset($hide_recursion_btn)) { ?>
		<div class="text-center actions no-wrap">
			<?php if($value['has_recursion_records'] > 0){ ?>
				<?php if((int)$value['total_recurring']){ ?>
					<button data-bs-toggle="tooltip" title="<?= __('admin.show_recurring_transition') ?>" class="wallet-btn show-recurring-transition" data-id="<?= $value['id'] ?>" style="color:#EB940D !important">
						<span class="plus">
							<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-plus-circle"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="16"></line><line x1="8" y1="12" x2="16" y2="12"></line></svg>
						</span>
						<span class="minus">
							<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-minus-circle"><circle cx="12" cy="12" r="10"></circle><line x1="8" y1="12" x2="16" y2="12"></line></svg>
						</span>
					</button>
				<?php } ?>
			<?php } ?>
		</div>
	<?php } ?>	
</td>

</tr>