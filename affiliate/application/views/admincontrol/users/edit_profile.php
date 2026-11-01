<form class="form-horizontal" method="post" id="profile-frm" enctype="multipart/form-data">
	<div class="row">
		<div class="col-12">
			<div class="card m-b-30">
				<div class="card-body">
					<div class="form-group row">
						<label for="example-text-input" class="col-sm-3 col-form-label"><?= __('admin.first_name') ?></label>
						<div class="col-sm-9">
							<input placeholder="<?= __('admin.enter_your_first_name') ?>" name="firstname" value="<?php echo $user->firstname; ?>" class="form-control" type="text">
						</div>
					</div>
					<div class="form-group row">
						<label for="example-text-input" class="col-sm-3 col-form-label"><?= __('admin.last_name') ?></label>
						<div class="col-sm-9">
							<input placeholder="<?= __('admin.enter_your_last_name') ?>" name="lastname" class="form-control" value="<?php echo $user->lastname; ?>" type="text">
						</div>
					</div>
					<div class="form-group row">
						<label for="example-text-input" class="col-sm-3 col-form-label"><?= __('admin.your_email') ?> </label>
						<div class="col-sm-9">
							<input placeholder="<?= __('admin.enter_your_email_address') ?>" name="email" id="email" class="form-control" value="<?php echo $user->email; ?>" required="required" type="email">
						</div>
					</div>
					<div class="form-group row">
						<label for="example-text-input" class="col-sm-3 col-form-label"><?= __('admin.phone_number') ?></label>
						<div class="col-sm-9">
							<input placeholder="<?= __('admin.enter_your_mobile_number') ?>" required="required" name="PhoneNumber" value="<?php echo $user->PhoneNumber; ?>" class="form-control" id="phonenumber" type="text">
						</div>
					</div>
					<div class="form-group row">
						<label for="example-text-input" class="col-sm-3 col-form-label"><?= __('admin.country') ?></label>
						<div class="col-sm-9">
							<select name="Country" class="form-control countries" id="Country" >
								<option value="" selected="selected" ><?= __('admin.select_country') ?></option>
								<?php foreach($country as $countries): ?>
								<option <?php if(!empty($user->Country) && $user->Country == $countries->id) { ?> selected <?php }?> value="<?php echo $countries->id; ?>"><?php echo $countries->name; ?></option>
								<?php endforeach; ?> 
							</select>
						</div>
					</div>
					<div class="form-group row">
						<label for="example-text-input" class="col-sm-3 col-form-label"><?= __('admin.city') ?></label>
						<div class="col-sm-9">
							<input class="form-control" placeholder="<?= __('admin.enter_your_city') ?>" name="City" id="City" value="<?php echo $user->City;?>" type="text">
						</div>
					</div>
					<div class="form-group row">
						<label for="example-text-input" class="col-sm-3 col-form-label"><?= __('admin.pincode') ?></label>
						<div class="col-sm-9">
							<input class="form-control" placeholder="<?= __('admin.enter_your_pincode') ?>" name="Zip" id="Zip" value="<?php echo $user->Zip;?>" type="text">
						</div>
					</div>
					<div class="form-group row">
						<label for="example-text-input" class="col-sm-3 col-form-label"><?= __('admin.member_image') ?></label><br>
						<div class="col-sm-9">
							<div class="fileUpload btn btn-sm btn-primary">
								<span><?= __('admin.choose_file') ?></span>
								<input id="uploadBtn" name="avatar" class="upload" type="file">
							</div>
							<?php $avatar = $user->avatar != '' ? 'assets/images/users/'.$user->avatar : 'assets/vertical/assets/images/no_image_yet.png' ; ?>
							<img src="<?php echo base_url($avatar); ?>" id="blah" class="thumbnail" border="0" width="220px">
						</div>
					</div>
				</div>
			</div>
		</div>
			<button class="btn btn-success" id="update-profile" type="submit"><i class="fa fa-save"></i> 
		<?= __('admin.update_profile') ?>
	</button>
	</div>
</form>

<script type="text/javascript">
	function readURL(input) {
		if (input.files && input.files[0]) {
			var reader = new FileReader();
			reader.onload = function(e) {
				jQuery('#blah').attr('src', e.target.result);
			}
			reader.readAsDataURL(input.files[0]);
		}
	}
	document.getElementById("uploadBtn").onchange = function () {
		readURL(this);
	};
</script>
