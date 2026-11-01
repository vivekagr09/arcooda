<div class="row">
	<div class="col-12">
		<div class="card mb-3">
			<div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
				<h5><?= __('admin.manage_admin') ?></h5>
				<a id="toggle-uploader" class="btn btn-light" href="<?= base_url('admincontrol/admin_user/')  ?>"><?= __('admin.cancel') ?></a>
			</div>
			<div class="card-body">
				<form id="admin-form">
					<input type="hidden" name="user_id" value="<?= (int)$user->id ?>">

					<div class="row mb-3">
						<div class="col-sm-6">
							<div class="form-floating">
								<input placeholder="<?= __('admin.enter_your_first_name') ?>" name="firstname" value="<?php echo $user->firstname; ?>" class="form-control" type="text" id="firstname">
								<label for="firstname"><?= __('admin.first_name') ?></label>
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-floating">
								<input placeholder="<?= __('admin.enter_your_last_name') ?>" name="lastname" class="form-control" value="<?php echo $user->lastname; ?>" type="text" id="lastname">
								<label for="lastname"><?= __('admin.last_name') ?></label>
							</div>
						</div>
					</div>


					<div class="form-floating mb-3">
						<input placeholder="<?= __('admin.enter_your_email_address') ?>" name="email" id="email" class="form-control" value="<?php echo $user->email; ?>"  type="email">
						<label for="email"><?= __('admin.your_email') ?></label>
					</div>

					<div class="form-floating mb-3">
						<input placeholder="<?= __('admin.enter_username_address') ?>" name="username" id="username" class="form-control" value="<?php echo $user->username; ?>"  type="text">
						<label for="username"><?= __('admin.username') ?></label>
					</div>

					<div class="form-floating mb-3">
						<input placeholder="<?= __('admin.enter_your_mobile_number') ?>"  name="PhoneNumber" value="<?php echo $user->PhoneNumber; ?>" class="form-control" id="phonenumber" type="text">
						<label for="phonenumber"><?= __('admin.phone_number') ?></label>
					</div>

					<div class="form-floating mb-3">
						<select name="Country" class="form-control countries" id="Country" >
							<option value="" selected="selected" ><?= __('admin.select_country') ?></option>
							<?php foreach($country as $countries): ?>
							<option <?php if(!empty($user->Country) && $user->Country == $countries->id) { ?> selected <?php }?> value="<?php echo $countries->id; ?>"><?php echo $countries->name; ?></option>
							<?php endforeach; ?> 
						</select>
						<label for="Country"><?= __('admin.country') ?></label>
					</div>
					
					<div class="form-floating mb-3">
						<input class="form-control" placeholder="<?= __('admin.enter_your_city') ?>" name="City" id="City" value="<?php echo $user->City;?>" type="text">
						<label for="City"><?= __('admin.city') ?></label>
					</div>

					<div class="form-floating mb-3">
						<input class="form-control" placeholder="<?= __('admin.enter_your_pincode') ?>" name="Zip" id="Zip" value="<?php echo $user->Zip;?>" type="text">
						<label for="Zip"><?= __('admin.pincode') ?></label>
					</div>

					<div class="row mb-3">
						<div class="col-sm-6">
							<div class="form-floating">
								<input class="form-control"  name="password" type="password" id="password">
								<label for="password"><?= __('admin.password') ?></label>
							</div>		
						</div>
						<div class="col-sm-6">
							<div class="form-floating">
								<input class="form-control"  name="cpassword" type="password" id="cpassword">
								<label for="cpassword"><?= __('admin.confirm_password') ?></label>
							</div>
						</div>
					</div>

					<div class="form-group mb-3">
						<label class="form-label"><?= __('admin.member_image') ?></label>
						<div class="input-group">
							<input id="uploadBtn" name="avatar" class="form-control" type="file" accept="image/*">
							<button class="btn btn-outline-secondary" type="button"><?= __('admin.choose_file') ?></button>
						</div>
						<?php $avatar = $user->avatar != '' ? 'users/' . $user->avatar : 'no-user_image.jpg' ; ?>
						<img src="<?php echo base_url();?>assets/images/<?php echo $avatar; ?>" id="blah" class="thumbnail img-thumbnail mt-2" border="0" width="220px">
					</div>

					<div class="form-group">
						<button type="button" class="btn btn-primary btn-submit"> <?= __('admin.submit') ?> </button>
						<span class="loading-submit"></span>
					</div>
				</form>
			</div>
		</div> 
	</div> 
</div>


<script type="text/javascript">
	$(document).ready(function(){
  function readURL(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            $('#blah').attr('src', e.target.result);
        }
        reader.readAsDataURL(input.files[0]);
    }
  }

  $("#uploadBtn").change(function(){
      readURL(this);
  });

  $(".btn-submit").on('click',function(evt){
    var $this = $("#admin-form");
    $(".btn-submit").btn("loading");
    $('.loading-submit').show();

    evt.preventDefault();
    var formData = new FormData($("#admin-form")[0]);

    $.ajax({
        url:'<?= base_url('admincontrol/admin_user_form') ?>',
        type:'POST',
        dataType:'json',
        cache:false,
        contentType: false,
        processData: false,
        data:formData,
        xhr: function (){
            var jqXHR = null;
            if ( window.ActiveXObject ){
                jqXHR = new window.ActiveXObject( "Microsoft.XMLHTTP" );
            }else {
                jqXHR = new window.XMLHttpRequest();
            }
            jqXHR.upload.addEventListener( "progress", function ( evt ){
                if ( evt.lengthComputable ){
                    var percentComplete = Math.round( (evt.loaded * 100) / evt.total );
                    $('.loading-submit').text(percentComplete + "% "+'<?= __('admin.loading') ?>');
                }
            }, false );
            jqXHR.addEventListener( "progress", function ( evt ){
                if ( evt.lengthComputable ){
                    var percentComplete = Math.round( (evt.loaded * 100) / evt.total );
                    $('.loading-submit').text('<?= __('admin.save') ?>');
                }
            }, false );
            return jqXHR;
        },
        complete:function(result){
            $(".btn-submit").btn("reset");
        },
        success:function(result){
            $('.loading-submit').hide();
            $this.find(".has-error").removeClass("has-error");
            $this.find("span.text-danger").remove();
            if(result['location']){
                window.location = result['location'];
            }
            if(result['errors']){
                $.each(result['errors'], function(i,j){
                    var $ele = $this.find('[name="'+ i +'"]');
                    if($ele){
                        $ele.parents(".form-group").addClass("has-error");
                        $ele.after("<span class='text-danger'>"+ j +"</span>");
                    }
                });
            }
        },
    });
    return false;
  });
});
</script>
