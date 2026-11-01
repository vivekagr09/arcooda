<span id="alertdiv" style="display: none">
<div class="alert alert-danger alert-dismissable" >
	<button type="button" class="close" data-bs-dismiss="alert" aria-hidden="true">&times;</button>
	<span id="alert_msg"></span>
</div>
</span>
<div class="row">
	<div class="col-12">
		<div class="card m-b-30">
			<div class="card-header">
				<h4 class="card-title pull-left"><?= __('admin.page_content') ?></h4>
				<div class="pull-right">
					<a class="btn btn-primary" href="<?= base_url('themes/multiple_theme/')  ?>"><?= __('admin.cancel') ?></a>
				</div>
			</div>
			<div class="card-body">
				<form id="form" action='#' method='post' name='process' class="" name="add_page">
					<input type="hidden" name="page_id" value="<?= (int)$page->id ?>">
					<div class="row">
						<div class="col-sm-12">
							<div class="row">
								<div class="col-md-4">
									<div class="form-group">
							            <label class="control-label"><?= __('admin.select_language') ?></label>
							            <select class="form-control" name="language_id" id="drpLanguage">
							                <?php 
							                if(isset($languages))
							                {
							                    foreach($languages as $language)
							                    {?>
							                    <option <?php 

							                    if($language['is_default']==1) {echo 'selected';} ?> value="<?=$language['id']?>"><?=$language['name'] ?></option>
							                  
							                   <?php  }     
							                }?>
							                
							            </select>
							        </div>    
						    	</div>
						    	<div class="col-sm-12"></div>
								<div class="col-sm-6">
									<div class="form-group">
										<label class="control-label"><?= __('admin.page_name') ?></label>
										<input id="page_name"  placeholder="<?= __('admin.page_name') ?>" name="page_name" value="<?php echo $page->page_name; ?>" class="form-control" type="text">
										<span id="er_page_name" style="color: red;display: none"><?= __('admin.page_name_required') ?></span>
									</div>
								</div>
								
								<div class="col-sm-6">
									<div class="form-group">
										<label class="control-label"><?= __('admin.content_title') ?></label>
										<input id="page_content_title"  placeholder="<?= __('admin.content_title') ?>" name="page_content_title" value="<?php echo $page->page_content_title; ?>" class="form-control" type="text">
										<span id="er_page_content_title" style="color: red;display: none"><?= __('admin.content_title_required') ?></span>
									</div>
								</div>
								
								<div class="col-sm-6">
									<div class="form-group">
										<label class="control-label"><?= __('admin.top_banner_title') ?></label>
										<input id="top_banner_title"  placeholder="<?= __('admin.top_banner_title') ?>" name="top_banner_title" value="<?php echo $page->top_banner_title; ?>" class="form-control" type="text">
										<span id="er_top_banner_title" style="color: red;display: none"><?= __('admin.top_banner_title_required') ?></span>
									</div>

								</div>
								<div class="col-sm-6">
									<div class="form-group">
										<label class="control-label"><?= __('admin.top_banner_sub_title') ?></label>
										<input id="top_banner_sub_title"  placeholder="<?= __('admin.top_banner_sub_title') ?>" name="top_banner_sub_title" value="<?php echo $page->top_banner_sub_title; ?>" class="form-control" type="text">
										<span id="er_top_banner_sub_title" style="color: red;display: none"><?= __('admin.top_banner_sub_title_required') ?></span>
									</div>
								</div>
								<div class="col-sm-12">
									<div class="form-group">
										<label class="control-label"><?= __('admin.page_content') ?></label>
										<br/>
										<span id="er_page_content" style="color: red;display: none"><?= __('admin.page_content_required') ?></span>
										<textarea id="summernote" class="form-control" name="page_content">
										<?php echo $page->page_content; ?>
										</textarea>
									</div>
								</div>

								<div class="col-sm-12">
									<fieldset>
										<legend><?= __('admin.header_menu_settings') ?></legend>

										<div class="row">
											<div class="col-sm-4">
												<div class="form-group">
													<label class="control-label"><?= __('admin.is_header_menu') ?></label>
													<div>
														<input type="radio"<?php echo ($page->is_header_menu == 1) ? "checked" : "" ?>  name="is_header_menu" id="is_header_menu_yes" value="1"> <label for="is_header_menu_yes"><?= __('admin.yes') ?></label>
														<input type="radio"<?php echo ($page->is_header_menu == 0) ? "checked" : "" ?>  name="is_header_menu" id="is_header_menu_no" value="0"> <label for="is_header_menu_no"><?= __('admin.no') ?></label>
													</div>
												</div>
											</div>

											<div class="col-sm-4">
												<div class="form-group">
													<label class="control-label"><?= __('admin.is_dropdown') ?></label>
													<div>
														<input type="radio"<?php echo ($page->is_header_dropdown == 1) ? "checked" : "" ?>  name="is_header_dropdown" id="is_header_dropdown_yes" value="1"> <label for="is_header_dropdown_yes"><?= __('admin.yes') ?></label>
														<input type="radio"<?php echo ($page->is_header_dropdown == 0) ? "checked" : "" ?>  name="is_header_dropdown" id="is_header_dropdown_no" value="0"> <label for="is_header_dropdown_no"><?= __('admin.no') ?></label>
													</div>
												</div>
											</div>

											<div class="col-sm-4">
												<div class="form-group">
													<label for="link_footer_section"><?= __('admin.parent_menu') ?></label>
													<select class="form-control" name="parent_page_id" id="parent_page_id">
														<option value=""><?= __('admin.select') ?>..</option>
														<?php if (isset($dropdown_menus) && !empty($dropdown_menus)) { ?>
															<?php foreach ($dropdown_menus as $key => $value) { ?>
																<option value="<?= $value->page_id ?>"><?= $value->page_name ?></option>
															<?php } ?>
														<?php } ?>
													</select>
												</div>
											</div>
										</div>
									</fieldset>
								</div>

								<div class="col-md-4">
									<fieldset>
										<legend><?= __('admin.active_your_page') ?></legend>
										<div class="form-group">
											<label class="control-label"><?= __('admin.status') ?></label>
											<div>
												<input type="radio"<?php echo ($page->status == 1) ? "checked" : "" ?>  name="status" id="status_active" value="1"> <label for="status_active"><?= __('admin.active') ?></label>
												<input type="radio"<?php echo ($page->status == 0) ? "checked" : "" ?>  name="status" id="status_inactive" value="0"> <label for="status_inactive"><?= __('admin.inactive') ?></label>
											</div>
										</div>
									</fieldset>
								</div>
								<div class="col-md-4">
									<div class="form-group">
										<label for="link_footer_section"><?= __('admin.footer_section_display_page_link') ?></label>
										<select class="form-control" id="link_footer_section">
											<option value=""><?= __('admin.select') ?>..</option>
											<option value="1"><?= __('admin.menu_a') ?></option>
											<option value="2"><?= __('admin.menu_b') ?></option>
											<option value="3"><?= __('admin.menu_c') ?></option>
											<option value="4"><?= __('admin.menu_d') ?></option>
										</select>
									</div>
								</div>
								<div class="col-sm-4">
									<div class="form-group">
										<label class="control-label"><?= __('admin.banner_image') ?></label><br/>
										<input type="file" name="page_banner_image" /><br/><br/>
										<img id="page_banner_image_preview" src="<?= base_url('assets/login/multiple_pages/img/inner-hero-bg.jpg') ?>" alt="<?= __('admin.banner_image') ?>" width="100%" height="150" />
									</div>
								</div>
							</div>
							<div class="col-lg-3 mt-3">
								<button type="button"class="btn btn-primary btn-user btn-block submit_btn"><?= __('admin.save_page') ?></button>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">

$('input[name="page_banner_image"]').change(function(){
	if (this.files && this.files[0]) {
		let reader = new FileReader();
		reader.onload = function (e) {
			$('#page_banner_image_preview').attr('src', e.target.result);
		}
		reader.readAsDataURL(this.files[0]);
	}
});


$(document).ready(function() {
	$('#summernote').summernote({
		minHeight: 300,
		toolbar: [
			['style', ['style']],
			['font', ['bold', 'underline', 'clear']],
			['fontname', ['fontname']],
			['color', ['color']],
			['para', ['ul', 'ol', 'paragraph']],
			['table', ['table']],
			['insert', ['link', 'picture', 'video']],
			['view', ['fullscreen', 'codeview', 'help']],
		],
	});
});


$("button").click(function(e) {
	e.preventDefault();

	$(".alert").hide();
	var show_error = null;
	var parent_page_id = $("#parent_page_id").val();
	var link_footer_section = $("#link_footer_section").val(); 
	var page_name = $("#page_name").val(); 
	if (page_name == '' || page_name == null || !page_name.replace(/\s/g, '').length) {
		$('#er_page_name').show();
		show_error = true;
	}else{
		$('#er_page_name').hide();
	}
	var page_content_title = $("#page_content_title").val();
	if (page_content_title == '' || page_content_title == null || !page_content_title.replace(/\s/g, '').length) {
		$('#er_page_content_title').show();
		show_error = true;
	}else{
		$('#er_page_content_title').hide();
	}
	var top_banner_title = $("#top_banner_title").val();
	if (top_banner_title == '' || page_content_title == null || !top_banner_title.replace(/\s/g, '').length) {
		$('#er_top_banner_title').show();
		show_error = true;
	}else{
		$('#er_top_banner_title').hide();
	}
	var top_banner_sub_title = $("#top_banner_sub_title").val();
	if (top_banner_sub_title == '' || page_content_title == null || !top_banner_sub_title.replace(/\s/g, '').length) {
		$('#er_top_banner_sub_title').show();
		show_error = true;
	}else{
		$('#er_top_banner_sub_title').hide();
	}

	var is_header_menu = $("input[name='is_header_menu']:checked").val();
	var is_header_dropdown = $("input[name='is_header_dropdown']:checked").val();
	var status = $("input[name='status']:checked").val();
	
	var summernote = $('#summernote').summernote('code');
	if (summernote == '' || summernote == null || !summernote.replace(/\s/g, '').length) {
		$('#er_page_content').show();
		show_error = true;
	}else{
		$('#er_page_content').hide();
	}
	if (show_error == true) {
		return false;
	}

	let fdata = new FormData();

	if ($('input[name="page_banner_image"]')[0].files) {
		let files = $('input[name="page_banner_image"]')[0].files;
		fdata.append('page_banner_image', files[0]);
	}

	var language_id=$("#drpLanguage").val();

	fdata.append('page_name',page_name);
	fdata.append('page_content_title',page_content_title);
	fdata.append('top_banner_title',top_banner_title);
	fdata.append('top_banner_sub_title',top_banner_sub_title);
	fdata.append('page_content',summernote);
	fdata.append('is_header_menu',is_header_menu);
	fdata.append('is_header_dropdown',is_header_dropdown);
	fdata.append('parent_id',parent_page_id);
	fdata.append('link_footer_section',link_footer_section);
	fdata.append('status',status);
	fdata.append('language_id',language_id);

	$.ajax({
		url: "<?php echo base_url();?>themes/save_page",
		type: "POST",
		data: fdata,
		cache: false,
		contentType: false,
		processData: false,
		success: function(data){
			data = JSON.parse(data);
			if(data.status == "success"){
				window.location.href= "<?php echo base_url();?>themes/multiple_theme";
			} else {
				$("#alertdiv").html(data.message);
			}
		}
	});
});


$('input').on('keypress', function (event) {
	var regex = new RegExp("^[a-zA-Z0-9 ]+$");
	var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
	if (!regex.test(key)) {
       event.preventDefault();
       return false;
	}
});

</script>