<?php

?>
<div class="card m-t-30">
	<div class="card-header">
		<h6 class="card-title m-0 pull-left"><?= __('user.create_new_ticket') ?></h6>
	</div>
	<form id="mail-form">
		<div class="card-body">
			<div class="row">
				<div class="col-lg-12">
					<div class=" m-b-30">
						<div class="row">
							
							<div class="form-group col-lg-6">
								<label class="control-label"><?= __('user.username') ?></label>
								<input type="text" name="fname" class="form-control" value="<?php echo $userdetails['username'] ?>" readonly="readonly">
							</div>
							<div class="form-group col-lg-6">
								<label class="control-label"><?= __('user.email') ?></label>
								<input type="text" name="email" class="form-control" value="<?php echo $userdetails['email'] ?>" readonly="readonly">
							</div>
						</div>

						<div class="form-group">
							<label class="control-label"><?= __('user.subject') ?></label>
							<select name="subject_id" class="form-control">
								<option value=""><?=__('user.ticket_subject_selection')?></option>
								<?php foreach ($subjects as $key => $value): ?>
									<option value="<?=$value['id']?>"><?=$value['subject']?></option>
								<?php endforeach ?>
							</select>
						</div>

						<div class="form-group">
							<label class="control-label"><?= __('user.body') ?></label>
							<textarea name="message" class="form-control summernote-img"></textarea>
						</div>

						<div class="form-group" id="addmoreAttachment">
							<label><?= __('user.attachment') ?> (*<?= __('user.optional') ?>):</label>
							<input type="file" id="attachment" name="attachment[]" /><br/>
						</div>
						<div class="form-group float-right mt-2">
							<button type="button" id="addmore" class="btn btn-info"><?= __('user.tickets_add_more')?></button>
						</div>
						<div class="clearfix"></div>
					</div>
				</div>
			</div>
		</div>
		<div class="card-footer">
			<button class="btn btn-primary btn-submit"><?= __('user.create_new_ticket') ?></button>
		</div>
	</form>
</div>
<script type="text/javascript">

	$("#mail-form").on('submit',function(evt){
		evt.preventDefault();	    
		var formData = new FormData($("#mail-form")[0]);  

		$(".btn-submit").btn("loading");
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
					window.location.href = '<?=base_url()?>/usercontrol/tickets';
					$("#mail-form .card-body").prepend('<div class="alert mb-4 alert-success alert-dismissable">'+result['success']+'</div>');
					var body = $("html, body");
					$("#mail-form")[0].reset()
					body.stop().animate({scrollTop:0}, 500, 'swing', function() { });
				}

				if(result['errors']){

					if(typeof result['errors'] == 'string') {
						$("#mail-form .card-body").prepend('<div class="alert mb-4 alert-danger alert-dismissable"><?= __('user.mail_sent_fail') ?></div>');
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
	$("#addmore").click(function(event) {
		var attachment_text = '<?= __('user.attachment') ?>';
		$("#addmoreAttachment").append(`<label>`+attachment_text+`</label><input type="file" name="attachment[]" id="attachment" class="form-control">`);
	});

</script>