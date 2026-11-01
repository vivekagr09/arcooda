


<div class="tab-pane" id="tab-paymentdetails">
	<?php if(isset($payment_methods) && $payment_methods['bank_transfer']['status']==1) { ?>
	<!--Bank Details section-->
	<div class="card">
		<div class="card-header">
			<h4 class="card-title"><?= __('user.bank_details') ?></h4>
		</div>	
		<div class="card-body">
			
		<form class="form-horizontal" method="post" action="<?= base_url('usercontrol/payment_details') ?>"  enctype="multipart/form-data">
			<input type="hidden" name="payment_id" value="<?= $paymentlist['payment_id'] ?>">
			<div class="form-group row">
				<label class="col-sm-3 col-form-label"><?= __('user.bank_name') ?> </label>
				<div class="col-sm-9">
					<input placeholder="<?= __('user.enter_your_bank_name') ?>" name="payment_bank_name" value="<?php echo $paymentlist['payment_bank_name']; ?>" class="form-control" required="required" type="text">
				</div>
			</div>
			<div class="form-group row">
				<label class="col-sm-3 col-form-label"><?= __('user.account_number') ?></label>
				<div class="col-sm-9">
					<input placeholder="<?= __('user.enter_your_account_number') ?>" name="payment_account_number" value="<?php echo $paymentlist['payment_account_number']; ?>" class="form-control" required="required" type="text">
				</div>
			</div>
			
			<div class="form-group row">
				<label class="col-sm-3 col-form-label"><?= __('user.account_name') ?></label>
				<div class="col-sm-9">
					<input placeholder="<?= __('user.enter_your_account_name') ?>" name="payment_account_name" class="form-control" value="<?php echo $paymentlist['payment_account_name']; ?>" required="required" type="text">
				</div>
			</div>
			<div class="form-group row">
				<label class="col-sm-3 col-form-label"><?= __('user.ifsc_code') ?> </label>
				<div class="col-sm-9">
					<input placeholder="<?= __('user.enter_your_ifsc_code') ?>" name="payment_ifsc_code" id="payment_ifsc_code" class="form-control" value="<?php echo $paymentlist['payment_ifsc_code']; ?>" required="required" type="text">
				</div>
			</div>
			<div class="form-group text-right">
				<button class="btn btn-default btn-success" id="update-payment"  type="submit"><i class="fa fa-save"></i> <?= __('user.submit') ?></button>
			</div>
		</form>

		</div>
	</div>
	<!--Bank Details section-->
	<?php } ?>
	
	<?php if(isset($payment_methods) && $payment_methods['paypal']['status']==1) { ?>
	<!--PayPal section -->
	<div class="card mt-3">
		<div class="card-header">
			<h5 class="card-title"><?= __('user.add_paypal_account') ?></h5>
		</div>
		<div class="card-body">
			<form class="form-horizontal" method="post" action="<?= base_url('usercontrol/payment_details') ?>" enctype="multipart/form-data">
				<div class="form-group row">
					<label class="col-sm-2 col-form-label"><?= __('user.paypal_email') ?> </label>
					<div class="col-sm-10">
						<input type="hidden" name="id" value="<?= $paypalaccounts['id'] ?>">
						<input name="paypal_email" class="form-control" value="<?= $paypalaccounts['paypal_email'] ?>" required="required" type="email">
					</div>
				</div>
				
				<div class="form-group text-right">
					<input name="add_paypal" value="<?= __('user.submit') ?>" type="submit" class="btn btn-default btn-success">
				</div>
			</form>
		</div>
	</div>
	<!--PayPal section-->
	<?php } ?>
	
	<!--Primary Method section-->
	<div class="card mt-3">
		<div class="card-header">
			<h5 class="card-title"><?= __('user.primay_payment_method') ?></h5>
		</div>
		<div class="card-body">
			<form class="form-horizontal" method="post" action="<?= base_url('usercontrol/payment_details') ?>" enctype="multipart/form-data">
				<?php  
				$paymentmethodcount=0;
				if(isset($payment_methods) && is_array($payment_methods ) && count($payment_methods)>0 )
				{
					foreach ($payment_methods as $paymentmethod) {
					if($paymentmethod["status"]==1) {
					$paymentmethodcount++; }
					}
				}
				if($paymentmethodcount>0)
				{
				?>
				<div class="col-sm-6 col-md-6">
					<div class="form-group row">
						
						<select class="form-control" id="primary_payment_method" name="primary_payment_method">
						<option value="">Select</option>	
						<?php 
							foreach ($payment_methods as $paymentmethod) {
							if($paymentmethod["status"]==1)
								{
									$paymentmethodcount++;
								?>
								<option value="<?=$paymentmethod['code'];?>" <?= $paymentmethod['code'] == $primary_payment_method ? 'selected' : '' ?>><?=$paymentmethod['title'];?></option>
							 <?php
								}
						 	}
						?>
						</select>
						
					</div>
				</div>	 
				<div class="form-group text-right">
					<input name="add_primary_payment" value="<?= __('user.submit') ?>" type="submit" class="btn btn-default btn-success">
				</div>
				<?php
				 } 
				 else 
				 {?>

				<h4 class="notification_on_pages">
					<span class="badge bg-info">
						<?= __('user.no_payment_method_available') ?>
					</span>
				</h4>
				<?php } 
				 ?>
						 
			</form>
		</div>
	</div>
	<!--Primary Method section-->
</div>