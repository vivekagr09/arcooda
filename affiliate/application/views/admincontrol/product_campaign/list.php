<?php foreach($productlist as $product){ ?>
	<?php 
		$productLink = base_url('capmaign/'. base64_encode($userdetails['id']) .'/product/'.$product['product_slug'] );
	?>
	<tr>
		<td class="text-center">
			<input name="product[]" class="list-checkbox" type="checkbox" id="check<?php echo $product['product_id'];?>" value="<?php echo $product['product_id'];?>" onclick="checkonly(this,'check<?php echo $product['product_id'];?>')">
			<?php if($product['product_type'] == 'downloadable'){ ?>
				<img src="<?= base_url('assets/images/download.png') ?>" width="20px" class='d-block'>
			<?php } ?>
		</td>
		<td>
			<div class="tooltip-copy">
				<img width="50px" height="50px" src="<?php echo base_url('assets/images/product/upload/thumb/'. $product['product_featured_image']) ?>" ><br>
			</div>
		</td>
		<td class="white-space-normal">
			<div class="tooltip-copy">
				<span><?php echo $product['product_name'];?></span>
			</div>
		</td>
		<td class="txt-cntr"><?php echo $product['seller_username'] ? $product['seller_username'] : __('admin.admin'); ?></td>
		<td class="txt-cntr"><?php echo c_format($product['product_price']); ?></td>
		<td class="txt-cntr"><?php echo $product['product_sku'];?></td>
		<td class="commission-tr">

			<?php 

				if($product['seller_id']){
		
					$seller = $this->Product_model->getSellerFromProduct($product['product_id']);
					$seller_setting = $this->Product_model->getSellerSetting($seller->user_id);

					$commnent_line = "";
					if($seller->affiliate_sale_commission_type == 'default'){ 
						if($seller_setting->affiliate_sale_commission_type == ''){
							$commnent_line .= __('admin.warning_default_commission_not_set');
						}
						else if($seller_setting->affiliate_sale_commission_type == 'percentage'){
							$commnent_line .= __('admin.percentage').' : '. (float)$seller_setting->affiliate_commission_value .'%';
						}
						else if($seller_setting->affiliate_sale_commission_type == 'fixed'){
							$commnent_line .= __('admin.fixed').' : '. c_format($seller_setting->affiliate_commission_value);
						}
					} else if($seller->affiliate_sale_commission_type == 'percentage'){
						$commnent_line .= __('admin.percentage').' : '. (float)$seller->affiliate_commission_value .'%';
					} else if($seller->affiliate_sale_commission_type == 'fixed'){
						$commnent_line .= __('admin.fixed').' : '. c_format($seller->affiliate_commission_value);
					} 

					echo '<b>'.__('admin.sale').'</b> : ' .$commnent_line;

					$commnent_line = "";
					if($seller->affiliate_click_commission_type == 'default'){ 
						$commnent_line .= c_format($seller_setting->affiliate_click_amount) ." ".__('admin.per')." ". (int)$seller_setting->affiliate_click_count ." ".__('admin.clicks');
					} else{
						$commnent_line .= c_format($seller->affiliate_click_amount) ." ".__('admin.per')." ". (int)$seller->affiliate_click_count ." ".__('admin.clicks');
					} 
					echo '<br><b>'.__('admin.click').'</b> : ' .$commnent_line;

					if ($vendor_setting['admin_click_status'] == 1) {

					$commnent_line = '';
					if($seller->admin_click_commission_type == '' || $seller->admin_click_commission_type == 'default'){
						$commnent_line =  c_format($vendor_setting['admin_click_amount']) ." ".__('admin.per')." ". (int)$vendor_setting['admin_click_count'] ." ".__('admin.clicks');
					} else{ 
						$commnent_line =  c_format($seller->admin_click_amount) ." ".__('admin.per')." ". (int)$seller->admin_click_count ." ".__('admin.clicks');
					} 

					echo '<br><b>'.__('admin.admin_click').'</b> : ' .$commnent_line;
					}

					if ($vendor_setting['admin_sale_status'] == 1) {

					$commnent_line = '';
					if($seller->admin_sale_commission_type == '' || $seller->admin_sale_commission_type == 'default'){ 
						if($vendor_setting['admin_sale_commission_type'] == ''){
							$commnent_line .= __('admin.warning_default_commission_not_set');
						}
						else if($vendor_setting['admin_sale_commission_type'] == 'percentage'){
							$commnent_line .= ' '. (float)$vendor_setting['admin_commission_value'] .'%';
						}
						else if($vendor_setting['admin_sale_commission_type'] == 'fixed'){
							$commnent_line .= ' '. c_format($vendor_setting['admin_commission_value']);
						}
					} else if($seller->admin_sale_commission_type == 'percentage'){
						$commnent_line .= ''. (float)$seller->admin_commission_value  .'%';
					} else if($seller->admin_sale_commission_type == 'fixed'){
						$commnent_line .= ''. c_format($seller->admin_commission_value);
					} else {
						$commnent_line .= __('admin.warning_commission_not_set');
					} 

					echo '<br><b>'.__('admin.admin_sale').'</b> : ' .$commnent_line;
				}
				} else {
			?>

				<b><?= __('admin.sale') ?></b> : 
				<?php

					if($product['product_commision_type'] == 'default'){
						if($default_commition['product_commission_type'] == 'percentage'){
							echo $default_commition['product_commission']. "%";
						}
						else
						{
							echo c_format($default_commition['product_commission']);
						}
					}
					else if($product['product_commision_type'] == 'percentage'){
						echo $product['product_commision_value']. "%";
					}
					else{
						echo c_format($product['product_commision_value']);
					}
				?>
				
				<br> <b><?= __('admin.click') ?></b> :
				<?php
			    	if($product['product_click_commision_type'] == 'default'){
						echo "<small>{$default_commition['product_noofpercommission']} ".__('admin.click_for')." "; 	
						echo c_format($default_commition['product_ppc']);
						echo "</small>";
					}
					else{
						echo "<small>{$product['product_click_commision_per']} ".__('admin.click_for')." : ";
						echo c_format($product['product_click_commision_ppc']) ."</small>";
					}
				?>
			<?php } ?>

			<?php 
				if($product['product_recursion_type']){
	           		if($product['product_recursion_type'] == 'custom'){
	           			if($product['product_recursion'] != 'custom_time'){
	           				echo '<br><b>'. __('admin.recurring') .' </b> : ' .  __('admin.'.$product['product_recursion']);
	           			} else {
	           				echo '<br><b>'. __('admin.recurring') .' </b> : '. timetosting($product['recursion_custom_time']);
	           			}
	           		} else{
						if($pro_setting['product_recursion'] == 'custom_time' ){
           					echo '<br><b>'. __('admin.recurring') .' </b> : '. timetosting($pro_setting['recursion_custom_time']);
						} else {
							echo '<br><b>'. __('admin.recurring') .' </b> : '.  __('admin.'.$pro_setting['product_recursion']);
						}
	           		}
	           	}
			?>
		</td>
		<td class="txt-cntr">
			<?php echo $product['order_count'];?>  
			<?php echo c_format($product['commission']) ;?><br/><br/>
			<b><?= __('admin.view') ?> / <?= __('admin.ratio') ?></b><br/>
			<?php 
			$ordercountratio=0;
			if($product['view_statistics']>0)
				$ordercountratio=$product['order_count']*100/$product['view_statistics'];
			$ordercountratio = is_float($ordercountratio) ==1 ? number_format((float)$ordercountratio, 2, '.', '') : $ordercountratio ;
			echo $product['view_statistics'] . ' / '. $ordercountratio .'%';
			?>
		</td>
		<td class="txt-cntr">
			<?php echo (int)$product['commition_click_count'] + (int)$product['commition_click_count_admin'];?> / 
			<?php echo c_format($product['commition_click']) ;?><br/><br/>
			<b><?= __('admin.view') ?> / <?= __('admin.ratio') ?></b><br/>
			<?php 
			$ordercountratio=0;
			$comissionclickcount=(int)$product['commition_click_count'] + (int)$product['commition_click_count_admin'];
			if($product['view_statistics']>0)
				$ordercountratio=$comissionclickcount*100/$product['view_statistics'];
			$ordercountratio = is_float($ordercountratio) ==1 ? number_format((float)$ordercountratio, 2, '.', '') : $ordercountratio;
			echo $product['view_statistics'] . ' / '. $ordercountratio.'%';
			?>
		</td>
		<td class="txt-cntr">
			<?php echo
				c_format((float)$product['commition_click'] + (float)$product['commission']);
			?>
		</td>
		<td class="txt-cntr">
			<?= product_status_on_store_admin($product['on_store'], $product['product_created_by']) ?> <br/><br/>
			<?= product_status($product['product_status']) ?>	
		</td>
		<td class="txt-cntr no-wrap">
			<button data-toggle="tooltip" title="<?php echo __('admin.integration_code') ?>" class="btn-show-code btn btn-sm btn-primary" data-id='<?= $product['product_id'] ?>'>
				<i class="fa fa-code" aria-hidden="true"></i>
			</button>
			<a class="btn btn-sm btn-primary" onclick="return confirmpopup('<?=base_url().'Productsales/update/'.$product['product_id']?>');" href="<?php echo base_url();?>Productsales/update/<?php echo $product['product_id'];?>"><i class="fa fa-edit cursors" aria-hidden="true"></i></a>
			<button class="btn btn-danger btn-sm delete-product" type="button" data-id="<?= $product['product_id'] ?>"> <i class="fa fa-trash"></i> </button>
		</td>
	</tr>

<?php } ?>
 