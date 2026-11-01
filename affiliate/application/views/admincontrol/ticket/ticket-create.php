<div class="card m-t-30">
	<div class="card-header">
		<h6 class="card-title m-0 pull-left"><?= __('admin.create_new_ticket') ?></h6>
	</div>
	<form id="mail-form">
		<div class="card-body">
			<div class="row">
				<div class="col-lg-12">
					<div class=" m-b-30">
						<div class="form-group">
							<label class="control-label"><?= __('admin.ticket_create_user') ?></label>
							<select class="form-control" name="user_id" required>
								<option value=""><?= __('admin.ticket_create_user') ?></option>
								<?php foreach ($users as $key => $value) { ?>
									<option <?= isset($user_id) && $user_id == $value['id'] ? 'selected' : '' ?> value="<?= $value['id'] ?>"><?= $value['username'] ?></option>	
								<?php } ?>
							</select>
						</div>

						<div class="form-group">
							<label class="control-label"><?= __('admin.subject') ?></label>
							<select name="subject_id" class="form-control">
								<option value=""><?=__('admin.ticket_subject_selection')?></option>
								<?php foreach ($subjects as $key => $value): ?>
									<option value="<?=$value['id']?>"><?=$value['subject']?></option>
								<?php endforeach ?>
							</select>
						</div>

						<div class="form-group">
							<label class="control-label"><?= __('admin.body') ?></label>
							<textarea  name="message" class="form-control summernote"></textarea>
						</div>

						<div class="form-group" id="addmoreAttachment">
							<label><?= __('admin.attachment') ?> (*<?= __('admin.optional') ?>):</label>
							<input type="file" id="attachment" name="attachment[]" /><br/>
						</div>
						<div class="form-group float-right mt-2">
							<button type="button" id="addmore" class="btn btn-info"><?= __('admin.tickets_add_more')?></button>
						</div>
						<div class="clearfix"></div>
					</div>
				</div>
			</div>
		</div>
		<div class="card-footer">
			<button class="btn btn-primary btn-submit"><?= __('admin.create_new_ticket') ?></button>
		</div>
	</form>
</div>
<script type="text/javascript">

	var attachment_text = '<?= __('admin.attachment')?>';
	$( document ).ready(function() {
		$('.summernote').summernote({
			tabsize: 2,
			height: 400,
			toolbar: [
			['style', ['bold', 'italic', 'underline', 'clear']],
			['font', ['strikethrough', 'superscript', 'subscript']],
			['fontsize', ['fontsize']],
			['color', ['color']],
			['para', ['ul', 'ol', 'paragraph']],
			['height', ['height']]
			]
		});
	});

	$("#mail-form").on('submit',function(evt){
		evt.preventDefault();	    
		var formData = new FormData($("#mail-form")[0]);  

		$(".btn-submit").btn("loading");
		$this = $("#mail-form");

		$.ajax({
			type:'POST',
			dataType:'json',
			url: '<?=base_url()?>/tickets/create_ticket',
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
					$redirecturl='<?=base_url()?>/admincontrol/tickets';
					showPrintMessage(result['success'],'success',$redirecturl);
					var body = $("html, body");
					$("#mail-form")[0].reset()
					body.stop().animate({scrollTop:0}, 500, 'swing', function() { });
				}

				if(result['errors']){

					if(typeof result['errors'] == 'string') {
						$("#mail-form .card-body").prepend('<div class="alert mb-4 alert-danger alert-dismissable"><?= __('admin.mail_sent_fail') ?></div>');
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
		$("#addmoreAttachment").append(`<label>`+attachment_text+`</label><input type="file" name="attachment[]" id="attachment" class="form-control">`);
	});
</script>