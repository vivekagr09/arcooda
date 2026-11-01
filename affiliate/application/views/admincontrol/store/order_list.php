<?php foreach($orders as $index => $order){ ?>
	<?php if($order['type'] == 'store'){ ?>
		<tr>
			<td><?= orderId($order['id']);?></td>
			<td><?php echo c_format($order['total']); ?></td>
			<td><?php echo $order['order_country_flag'];?></td>
			<td><?= __('admin.local_store') ?></td>
			<?php 
				$icon = strtolower(str_replace(" ", "_", $status[$order['status']])) .'.png';
			?>
			<td>
				<div class="badge <?= ($order['status'] == 1) ? 'bg-success' : 'bg-warning' ?>">
					<?= $status[$order['status']] ?>
				</div>
			</td>
			<td>
				<?php
				if($order['wallet_commission_status'] == 0) {
					?>
					<span class="badge <?php if((int)$order['wallet_status'] > 0){ ?>bg-success<?php }else{ ?>bg-warning<?php } ?>"><?= $wallet_status[(int)$order['wallet_status']] ?></span>
					<?php
			 	} else {
					echo commission_status($order['wallet_commission_status']);
			 	}

				?>
				<br>
				<?php echo c_format($order['commission_amount']); ?>
			</td>
			<td><?= date("d-m-Y h:i A",strtotime($order['created_at'])); ?></td>
			<td>
				<button class='btn btn-primary btn-sm order-detail' data-order_type="store" data-order_id="<?= $order['id']; ?>">
					<?= __('admin.order_details') ?>
				</button>
				<button class='btn btn-primary btn-sm order-transactions-toggle' data-order_type="store" data-order_id="<?= $order['id']; ?>">
					<?= __('admin.show_transactions') ?>
				</button>
				<button class="btn btn-sm btn-danger remove-order" data-order_type="store" data-order_id="<?= $order['id'] ?>">
					<i class="fa fa-trash"></i>
				</button>
			</td>
		</tr>
		<?php
			$jsOrders['store'][$order['id']] = 
				'<ul>'
					.'<li><b>'.__('admin.payment_method').' :</b> <span>'. $order['payment_method'] .'</span> </li>'
					.'<li><b>'.__('admin.transaction').' :</b> <span>'. $order['txn_id'] .'</span> </li>'
					.'<li><b>'. __('admin.ip').' :</b> <span>'. $order['ip'].'</span> </li>'
					.'<li><b>'. __('admin.country_code').' :</b> <span>'. $order['country_code'].'</span> </li>'
					.'<li><b>'. __('admin.currency_code').' :</b> <span>'. $order['currency_code'].'</span> </li>'
					.'<li><br>'
						.'<b>'. __('admin.products').'</b>'
							.'<table class="table table-white-space-normal detail-table">'
								.'<tr>'
									.'<th colspan="2">'. __('admin.name').'</th>'
									.'<th>'. __('admin.unit_price').'</th>'
									.'<th>'. __('admin.variation_price').'</th>'
									.'<th>'. __('admin.quantity').'</th>'
									.'<th>'. __('admin.commission_type').'</th>'
									.'<th>'. __('admin.total_discount').'</th>'
									.'<th>'. __('admin.total').'</th>'
								.'</tr>';
							foreach($order['products'] as $key => $product){
								$jsOrders['store'][$order['id']] .=
									'<tr>'
										.'<td><img src="'. $product['image'].'" style="width: 40px;height: 40px"></td>'
										.'<td class="max-width-150">';
										$combinationString = "";
										if(isset($product['variation']) && !empty($product['variation'])){
											$variation = json_decode($product['variation']);
											foreach($variation as $key => $value){
												if($key == 'colors')
													$combinationString .= ($combinationString == "") ? explode("-",$value)[1] : ",".explode("-",$value)[1];
												else
													$combinationString .= ($combinationString == "") ? $value : ",".$value;
											}
										}

								$jsOrders['store'][$order['id']] .= $product['product_name'];
								$jsOrders['store'][$order['id']] .= ($combinationString != "") ? "(".$combinationString.")" : "";
								if($product['coupon_discount'] > 0){
									$jsOrders['store'][$order['id']] .= 
			                            '<p class="couopn-code-text">'. __('admin.code') .' : '
			                            .'<span class="c-name">'.$product['coupon_code'] .'</span>'.__('admin.applied').'</p>';
			                    }
			                    	$jsOrders['store'][$order['id']] .= '</td>'
										.'<td>'. c_format($product['price']) .'</td>'
										.'<td>'. c_format(json_decode($product['variation'])->price) .'</td>'
										.'<td>'. $product['quantity'] .'</td>'
										.'<td>'. $product['commission_type'] .'</td>'
										.'<td>'. c_format($product['coupon_discount']) .'</td>'
										.'<td>'. c_format($product['total']) .'</td>'
									.'</tr>';
							}
							foreach($order['totals'] as $key => $total){
								$jsOrders['store'][$order['id']] .=
									'<tr>'
										.'<td colspan="5"></td>'
										.'<td>'. $total['text'].'</td>'
										.'<td>'. c_format($total['value']) .'</td>'
									.'</tr>';
							}
							$jsOrders['store'][$order['id']] .= 
							'</table>'
						.'</li>'
						.'<li>'
							.'<b>'.__('admin.payment_info').'</b>'
							.'<table class="table detail-table">'
								.'<thead>'
									.'<th class="border-top-0">'. __('admin.mode').'</th>'
									.'<th class="border-top-0">'. __('admin.transaction_id').'</th>'
									.'<th class="border-top-0">'. __('admin.payment_status').'</th>'
								.'</thead>'
								.'<tbody>';
								if($order['status'] == 0){
									$jsOrders['store'][$order['id']] .=
									'<tr>'
										.'<td colspan="100%">'
											.'<p class="text-muted text-center">'.__('admin.waiting_for_payment_status').'</p>'
										.'</td>'
									.'</tr>';
								}
								foreach($order['payment_history'] as $key => $value){
									$jsOrders['store'][$order['id']] .=
									'<tr>'
										.'<td>'. $value['payment_mode'] .'</td>'
										.'<td>'. $order['txn_id'] .'</td>'
										.'<td>'. $value['paypal_status'] .'</td>'
									.'</tr>';
								}
								$jsOrders['store'][$order['id']] .=
								'</tbody>'
							.'</table>'
						.'</li>'

						.'<li>'
							.'<b>'. __('admin.order_info'). '</b>'
							.'<table class="table detail-table">'
								.'<thead>'
									.'<tr>'
										.'<th width="50px">#</th>'
										.'<th width="150px">'. __('admin.status').'</th>'
										.'<th>'. __('admin.comment') .'</th>'
									.'</tr>'
								.'</thead>'
								.'<tbody>';
								if(!$order['order_history']){
									$jsOrders['store'][$order['id']] .=
									'<tr>'
										.'<td colspan="100%">'
											.'<p class="text-muted text-center">'. __('admin.no_any_order_status') .'</p>'
										.'</td>'
									.'</tr>';
								}
								foreach($order['order_history'] as $key => $value){
									$jsOrders['store'][$order['id']] .=
									'<tr>'
										.'<td>#'.$key.'</td>'
										.'<td>'. $status[$value['order_status_id']] .'</td>'
										.'<td style="white-space: pre-line;">'. $value['comment'] .'</td>'
									.'</tr>';
								}
								$jsOrders['store'][$order['id']] .=
								'</tbody>'
							.'</table>'
						.'</li>';
						
						if(!empty($order['comment'])){
	        			    $cmt = json_decode($order['comment'], true);
	            			 foreach($cmt as $c){
		        			    if(is_array($c))
		        			    	$jsOrders['store'][$order['id']] .=
		                			'<li><b>'.$c['title'] .' :</b> <span>'. $c['comment'] .'</span> </li>';
	        			    }
	        			}
	        			$jsOrders['store'][$order['id']] .= '</ul>';
	} else { ?>
		<tr>
		    <td><?= orderId($order['order_id']);?></td>
		    <td><?= c_format($order['total']); ?></td>
		    <td><?= $order['order_country_flag'];?></td>
			<td><?= __('admin.local_store') ?></td>
		    <td>
		        <span class="badge <?= ($order['status'] == 1) ? 'bg-success' : 'bg-warning' ?>">
		            <?= $status[$order['status']] ?>
		        </span>
		    </td>
		    <td>
		        <?php
		        if($order['wallet_commission_status'] == 0) {
		            ?>
		            <span class="badge <?= ((int)$order['wallet_status'] > 0) ? 'bg-success' : 'bg-warning' ?>">
		                <?= $wallet_status[(int)$order['wallet_status']] ?>
		            </span>
		            <?php
		        } else {
		            echo commission_status($order['wallet_commission_status']);
		        }
		        ?>
		        <br>
		        <?= c_format($order['commission']) ?>
		    </td>
		    <td><?= date("d-m-Y h:i A", strtotime($order['created_at'])); ?></td>
		    <td>
		        <button class='btn btn-primary btn-sm order-detail' data-order_type="ex" data-order_id="<?= $order['id'] ?>">
		            <?= __('admin.order_details') ?>
		        </button>
		        <button class='btn btn-primary btn-sm order-transactions-toggle' data-order_type="ex" data-order_id="<?= $order['id'] ?>">
		            <?= __('admin.transaction_details') ?>
		        </button>
		        <button class="btn btn-sm btn-danger remove-order" data-order_type="ex" data-order_id="<?= $order['id'] ?>">
		            <i class="fas fa-trash"></i>
		        </button>
		    </td>
		</tr>

		<?php 
			$jsOrders['ex'][$order['id']] = 
                '<ul>'
					.'<li><b>'. __('admin.product_ids').' :</b> <span>'. $order['product_ids'].'</span></li>'
					.'<li><b>'. __('admin.total').' :</b> <span>'. $order['total'].'</span></li>'
					.'<li><b>'. __('admin.currency').' :</b> <span>'. $order['currency'].'</span></li>'
					.'<li><b>'. __('admin.commission_type').' :</b> <span>'. $order['commission_type'].'</span></li>'
					.'<li><b>'. __('admin.ip').' :</b> <span>'. $order['ip'].'</span></li>'
					.'<li><b>'. __('admin.country_code').' :</b> <span>'. $order['country_code'].'&nbsp;<img title="'. $order['country_code'].'" src="'. base_url('assets/vertical/assets/images/flags/'. strtolower($order['country_code'])).'.png" width="25" height="15"</span></li>'
					.'<li><b>'. __('admin.website').' :</b> <span><a href="'. $order['base_url'].'" target="_blank">'. $order['base_url'].'</a></span></li>'
					.'<li><b>'. __('admin.script_name').' :</b> <span>'. ucfirst($order['script_name']).'</span></li>';

				$custom_data = json_decode($order['custom_data'],1);
				foreach ($custom_data as $value){
					$jsOrders['store'][$order['id']] .=  
					'<li class="">'
						.'<b>'. $value['key'].':</b> <span>'. $value['value'].'</span>'
					.'</li>';
				}
				$jsOrders['store'][$order['id']] .= '</ul>';
	}
} ?>

<script type="text/javascript">
	var jsOrders = <?= json_encode($jsOrders) ?>;
</script>