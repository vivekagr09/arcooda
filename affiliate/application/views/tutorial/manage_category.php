<?php 
$db =& get_instance();
$userdetails=$db->userdetails();
?>
<link rel="stylesheet" type="text/css" href="<?= base_url("assets/plugins/ui/jquery-ui.min.css") ?>">
<script type="text/javascript" src="<?= base_url('assets/plugins/ui/jquery-ui.min.js') ?>"></script>
<link rel="stylesheet" type="text/css" href="<?= base_url("assets/plugins/select2/select2.min.css") ?>">
<script type="text/javascript" src="<?= base_url('assets/plugins/select2/select2.full.min.js') ?>"></script>
<style>
	.jscolor-picker-wrap {
		z-index: 999999 !important;
	}
</style>

<form class="form-horizontal" method="post" action="" enctype="multipart/form-data" id="form_form">
	<div class="row">
		<div class="col-sm-12">
			<div class="card">
				<div class="card-header bg-secondary text-white">
					<h5><?= (int)$category['id'] == 0 ? __('admin.add_tutorial_category') : __('admin.edit_tutorial_category') ?></h5>
				</div>
				<div class="card-body">
					<input type="hidden" id="id" name="id" value="<?php echo $category['id'] ?>">
					<div class="row">
						<div class="col-sm-4">
							<div class="form-group">
								<label class="control-label"><?= __('admin.select_language') ?></label>
								<select class="form-control" name="language_id" id="drpLanguage">
									<?php 
									if(isset($languages)) {
										$language_id = 1;
										foreach($languages as $language) {
											$selected = $category['language_id'] == $language['id'] ? 'selected' : '';
											?>
											<option <?php echo $selected; ?> value="<?= $language['id'] ?>"><?= $language['name'] ?></option>
											<?php
										}
									}
									?>
								</select>
							</div>
							<div class="form-group">
								<label class="control-label"><?= __('admin.category_name') ?></label>
								<input placeholder="<?= __('admin.enter_tutorial_category_name') ?>" name="name" value="<?php echo $category['name']; ?>" class="form-control" type="text">
							</div>
						</div>
						
						<div class="col-sm-12">
							<div class="form-group text-end">
								<button type="submit" class="btn btn-lg btn-default btn-submit btn-primary" name="save"><?= __('admin.save') ?></button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</form>




<script type="text/javascript"> 
	$(document).ready(function() { 
	});
	var cache = {};
 	$(".btn-submit").on('click',function(evt){
		evt.preventDefault();
		$btn = $(this);
		var formData = new FormData($("#form_form")[0]);
		formData.append("action", $(this).attr("name"));
		$this = $("#form_form");	       

		$btn.btn("loading");
		$.ajax({
			url:'<?= base_url('admincontrol/manage_tutorial_catgory') ?>',
			type:'POST',
			dataType:'json',
			cache:false,
			contentType: false,
			processData: false,
			data:formData,
			error:function(){ $btn.btn("reset"); },
			success:function(result){            	
				$btn.btn("reset");
				$('.loading-submit').hide();
				$this.find(".has-error").removeClass("has-error");
				$this.find("span.text-danger").remove();

				if(result['location']){
					window.location = result['location'];
				}
				if(result['errors']){
					showPrintMessage(result['errors'],'error');
				}
			},
		});
		return false;
	});
</script>
