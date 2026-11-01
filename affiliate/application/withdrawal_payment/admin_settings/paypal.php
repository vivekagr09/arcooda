<div class="form-group">

	<label class="form-control-label d-block">

		<small class="pull-right">

			<a href="https://www.appinvoice.com/en/s/documentation/how-to-get-paypal-client-id-and-secret-key-22" target="_blank"><?= __('admin.paypal_info')?></a>

		</small>

	</label>

	<label class="form-control-label"><?= __('admin.client_id')?></label>

	<input class="form-control" name="ClientID" value="<?= $setting_data['ClientID'] ?>">

</div>



<div class="form-group">

	<label class="form-control-label"><?= __('admin.client_secret')?></label>

	<input class="form-control" name="ClientSecret" value="<?= $setting_data['ClientSecret'] ?>" >

</div>





<div class="well">

	<div class="form-group">

		<label class="control-label"><?= __('admin.denied_status')?></label>

		<div class="">

			<select name="denied_status_id" class="form-control">

				<?php foreach ($status_list as $status_id => $name) { ?>

					<?php if ($status_id == $setting_data['denied_status_id']) { ?>

						<option value="<?php echo $status_id; ?>" selected="selected"><?= $name ?></option>

					<?php } else { ?>

						<option value="<?php echo $status_id; ?>"><?= $name ?></option>

					<?php } ?>

				<?php } ?>

			</select>

		</div>

	</div>



	<div class="form-group">

		<label class="control-label"><?= __('admin.pending_status')?></label>

		<div class="">

			<select name="pending_status_id" class="form-control">

				<?php foreach ($status_list as $status_id => $name) { ?>

					<?php if ($status_id == $setting_data['pending_status_id']) { ?>

						<option value="<?php echo $status_id; ?>" selected="selected"><?= $name ?></option>

					<?php } else { ?>

						<option value="<?php echo $status_id; ?>"><?= $name ?></option>

					<?php } ?>

				<?php } ?>

			</select>

		</div>

	</div>



	<div class="form-group">

		<label class="control-label"><?= __('admin.processing_status')?></label>

		<div class="">

			<select name="processing_status_id" class="form-control">

				<?php foreach ($status_list as $status_id => $name) { ?>

					<?php if ($status_id == $setting_data['processing_status_id']) { ?>

						<option value="<?php echo $status_id; ?>" selected="selected"><?= $name ?></option>

					<?php } else { ?>

						<option value="<?php echo $status_id; ?>"><?= $name ?></option>

					<?php } ?>

				<?php } ?>

			</select>

		</div>

	</div>



	<div class="form-group">

		<label class="control-label"><?= __('admin.success_status')?></label>

		<div class="">

			<select name="success_status_id" class="form-control">

				<?php foreach ($status_list as $status_id => $name) { ?>

					<?php if ($status_id == $setting_data['success_status_id']) { ?>

						<option value="<?php echo $status_id; ?>" selected="selected"><?= $name ?></option>

					<?php } else { ?>

						<option value="<?php echo $status_id; ?>"><?= $name ?></option>

					<?php } ?>

				<?php } ?>

			</select>

		</div>

	</div>



	<div class="form-group">

		<label class="control-label"><?= __('admin.canceled_status')?></label>

		<div class="">

			<select name="canceled_status_id" class="form-control">

				<?php foreach ($status_list as $status_id => $name) { ?>

					<?php if ($status_id == $setting_data['canceled_status_id']) { ?>

						<option value="<?php echo $status_id; ?>" selected="selected"><?= $name ?></option>

					<?php } else { ?>

						<option value="<?php echo $status_id; ?>"><?= $name ?></option>

					<?php } ?>

				<?php } ?>

			</select>

		</div>

	</div>

</div>