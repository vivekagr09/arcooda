<tr class="wallet-detail-tr <?= $orderType."-".$orderId ?>">
	<td colspan="100%" style="border-top:none !important;">
	    <div style="background-color: #dee2e6; padding: 15px; border-radius: 10px; border: 1px solid lightgrey;">
	    <h6><?= count($transaction) ?> <?= __('admin.wallet_transactions_generated') ?></h6>
		<div class="table-responsive" style="max-height:300px; overflow-y:scroll; border-bottom:solid 1px lightgrey;">
			<table class="table transaction-table table-scroll" style="background-color:#dee2e6;">
				<tbody>
					<?php
						if ($is_dashboard == '1' || $is_order_page == '1') {
							foreach ($transaction as $key => $value) {
								$data = [];
								$data['value'] = $value;
								$data['class'] = $class;
								$data['stop_checkbox'] = 1; 
								$data['stop_child'] = 1; 
								$data['wallet_status'] = $status;
								$data['status'] = $this->Wallet_model->status();
	                    		$data['status_icon'] = $this->Wallet_model->status_icon;
	                    		$data['status_list'] = $this->Withdrawal_payment_model->status_list;
								$data['hide_recursion_btn'] = true; 
								echo $this->Product_model->getHtml('admincontrol/users/part/dashboard_wallet_tr', $data);
							}
						}else{
							foreach ($transaction as $key => $value) {
								$data = [];
								$data['value'] = $value;
								$data['class'] = $class;
								$data['stop_checkbox'] = 1; 
								$data['stop_child'] = 1; 
								$data['wallet_status'] = $status;
								$data['status'] = $this->Wallet_model->status();
	                    		$data['status_icon'] = $this->Wallet_model->status_icon;
	                    		$data['status_list'] = $this->Withdrawal_payment_model->status_list;
								$data['hide_recursion_btn'] = true; 
								echo $this->Product_model->getHtml('admincontrol/users/part/new_wallet_tr', $data);
							}
						} 
					?>
				</tbody>
			</table>
		</div>
		<div>
	</td>
</tr>