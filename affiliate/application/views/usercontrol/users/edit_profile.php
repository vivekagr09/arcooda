		<div class="form-horizontal" method="post" id="profile-frm" enctype="multipart/form-data">
			<div class="row">
				<div class="col-12">
					<div class="card m-b-30">
						<div class="card-body">
							<div class="row">
								<div class="col-sm-8">
									<?= $html_form ?>
									<button class="btn btn-block btn-default btn-success" id="update-user" type="submit"><i class="fa fa-save"></i> <?= __('user.update_profile') ?> <span class="loading-submit"></span> </button>
								</div>
								<div class="col-sm-3">
									<div class="form-group">
										<label for="example-text-input" class="col-form-label"><?= __('user.member_image') ?></label><br>

										<?php $avatar = $user['avatar'] != '' ? 'assets/images/users/'.$user['avatar'] : 'assets/vertical/assets/images/users/avatar-1.jpg' ; ?>
										<img src="<?php echo base_url($avatar); ?>" id="blah" class="thumbnail" border="0" width="220px">
										<br>

										<div class="fileUpload btn btn-sm btn-primary">
											<span><?= __('user.choose_file') ?></span>
											<input id="uploadBtn" name="avatar" class="upload" type="file">
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>



<script type="text/javascript">
	$( document ).ready(function() {

		$("#update-user").click(function(evt){
			$this = $(".reg_form");
			evt.preventDefault();

	        var is_valid = 0;
	        var need_valid = 0;

			$(".tel_input").each(function() {

				let this_is_valid = true;

			    $(this).parents(".form-group").removeClass("has-error");
			    
			    $(this).parents(".form-group").find(".text-danger").remove();

			    if(window["tel_input"+$(this).attr('id')]){
			        var errorMap = ['<?= __('user.invalid_number') ?>','<?= __('user.invalid_country_code') ?>','<?= __('user.too_short') ?>','<?= __('user.too_long') ?>','<?= __('user.invalid_number') ?>'];
			        var errorInnerHTML = '';
			        
			        if ($(this).val().trim()) {
			        	need_valid++;
			            if (window["tel_input"+$(this).attr('id')].isValidNumber()) {

							window["tel_input"+$(this).attr('id')].setNumber($(this).val().trim());

			                is_valid++;
			                this_is_valid = true;
			            } else {
			                var errorCode = window["tel_input"+$(this).attr('id')].getValidationError();
			                errorInnerHTML = errorMap[errorCode];
			                this_is_valid = false;
			            }
			        } else {
			        	if($(this).attr('required') !== undefined) {
			        		need_valid++;
			                this_is_valid = false;
				        	errorInnerHTML = 'The Mobile Number field is required.'; 
				        }
			        }

			        if(!this_is_valid){
			            $(this).parents(".form-group").addClass("has-error");
			            $(this).parents(".form-group").find('> div').after("<span class='text-danger'>"+ errorInnerHTML +"</span>");
			        }
			    }
			});

	        var formData = new FormData($this[0]);

	        formData.append("action", $(this).attr("name"));
	        formData.append("avatar", $("#uploadBtn")[0].files[0]);

	        formData = formDataFilter(formData);


	            $(".tel_input").each(function() {
			        if ($(this).val().trim() && window["tel_input"+$(this).attr('id')].isValidNumber()) {
			        	country_id = window["tel_input"+$(this).attr('id')].getSelectedCountryData().dialCode;
		                formData.append($(this).attr('name')+'_afftel_input_pre', country_id);
			        }
			    });


			if(is_valid === need_valid){
				$.ajax({
					url:'',
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
		                        console.log( 'Uploaded percent', percentComplete );
		                        $('.loading-submit').text(percentComplete + "% "+'<?= __('user.loading') ?>');
		                    }
		                }, false );

		                jqXHR.addEventListener( "progress", function ( evt ){
		                    if ( evt.lengthComputable ){
		                        var percentComplete = Math.round( (evt.loaded * 100) / evt.total );
		                        $('.loading-submit').text('<?= __('user.save') ?>');
		                    }
		                }, false );
		                return jqXHR;
		            },
					beforeSend:function(){},
					complete:function(){},
					success:function(json){
						if(json['location']){
							window.location = json['location'];
						}

						$this.find(".has-error").removeClass("has-error");
						$this.find("span.text-danger").remove();
						if(json['errors']){
						    $.each(json['errors'], function(i,j){
					        	$ele = $this.find('#'+ i);
						        if($ele.hasClass('form-group')){
						            $ele.addClass("has-error");
						            $ele.append("<br><span class='text-danger'>"+ j +"</span>");
						        } else {
						        	$ele.parents(".form-group").addClass("has-error");
						            $ele.after("<span class='text-danger'>"+ j +"</span>");
						        }
						    })
						}	
					}
				})
			}
		})
	})
</script>

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
var state_id = '<?php echo $user->state ?>';

$("#Country").on('change',function(){
    var country = $(this).val();
    $.ajax({
        url: '<?php echo base_url('get_state') ?>',
        type: 'post',
        dataType: 'json',
        data: {
            country_id : country
        },
        success: function (json) {
            if(json){
                var html = '';
                $.each(json, function(k,v){
                    if(v.id == state_id){
                        html += '<option value="'+v.id+'" selected="selected">'+v.name+'</option>';
                    }else{
                        html += '<option value="'+v.id+'">'+v.name+'</option>';
                    }
                });
                $('#states').html(html);
            }
        }
    });
});
$("#Country").trigger('change');
</script>