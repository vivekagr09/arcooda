<?php
	
?>
<div class="card m-t-30">
	<div class="card-header">
		<h6 class="card-title m-0 pull-left"><?= __('admin.page_title_store_contact') ?></h6>
	</div>
	<form id="mail-form">
		<div class="card-body">
			<div class="row">
				<div class="col-lg-12">
					<div class=" m-b-30">
						<div class="form-group">
							<label class="control-label"><?= __('user.first_name') ?></label>
							<input type="text" name="fname" class="form-control" value="<?php echo $userdetails['firstname'] ?>">
						</div>

						<div class="form-group">
							<label class="control-label"><?= __('user.last_name') ?></label>
							<input type="text" name="lname" class="form-control" value="<?php echo $userdetails['lastname'] ?>">
						</div>

						<div class="form-group">
							<label class="control-label"><?= __('user.phone_number') ?></label>
							<input type="text" name="phone" class="form-control" value="<?php echo $user_mobile ?>">
						</div>

						<div class="form-group">
							<label class="control-label"><?= __('user.domain_name') ?></label>
							<input type="text" name="domain" class="form-control" value="<?php echo $domain ?>">
						</div>

						<div class="form-group">
							<label class="control-label"><?= __('user.subject') ?></label>
							<input type="text" name="subject" class="form-control">
						</div>

						<div class="form-group">
							<label class="control-label"><?= __('user.body') ?></label>
							<textarea  name="body" class="form-control"></textarea>
						</div>

						<div class="form-group">
							<label><?= __('user.attachment') ?> (*<?= __('user.optional') ?>):</label>
							<input type="file" id="attachment" name="attachment" />
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="card-footer">
			<button class="btn btn-primary btn-submit"><?= __('user.send_mail') ?></button>
		</div>
	</form>
</div>
<script type="text/javascript">
	$("#mail-form").on('submit',function(evt){
	    evt.preventDefault();	    
    	var formData = new FormData($("#mail-form")[0]);  

	    $(".btn-submit").btn("loading");
	    formData = formDataFilter(formData);
	    $this = $("#mail-form");

	    $.ajax({
	        type:'POST',
	        dataType:'json',
	        cache:false,
	        contentType: false,
	        processData: false,
	        data:formData,
	        success:function(result){
	            $(".btn-submit").btn("reset");
	            $(".alert-dismissable").remove();
	            $this.find(".has-error").removeClass("has-error");
	            $this.find(".is-invalid").removeClass("is-invalid");
	            $this.find("span.text-danger").remove();	            

	            if(result['success']){
	                showPrintMessage("<?= __('user.mail_sent_successfully') ?>", 'success');
	                var body = $("html, body");
	                $("#mail-form")[0].reset()
					body.stop().animate({scrollTop:0}, 500, 'swing', function() { });
	            }
	            if(result['errors']){
	            	if(typeof result['errors'] == 'string') {
	            		showPrintMessage("<?= __('user.mail_sent_fail') ?>", 'error');
                		var body = $("html, body");
                		body.stop().animate({scrollTop:0}, 500, 'swing', function() { });
	                } else {
	                	$.each(result['errors'], function(i,j){
		                    $ele = $this.find('[name="'+ i +'"]');
		                    if(!$ele.length){ 
		                    	$ele = $this.find('.'+ i);
		                    }
		                    if($ele.length){
		                        $ele.addClass("is-invalid");
		                        $ele.parents(".form-group").addClass("has-error");
		                        $ele.after("<span class='d-block text-danger'>"+ j +"</span>");
		                    }
		                });
		                errors = result['errors'];
						$('.formsetting_error').text(errors['formsetting_recursion_custom_time']);
						$('.productsetting_error').text(errors['productsetting_recursion_custom_time']);
	                }
	            }
	        },
	    });
	    return false;
	});
</script>