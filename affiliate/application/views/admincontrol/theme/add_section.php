<div class="row">
	<div class="col-12">
		<div class="card m-b-30">
			<div class="card-header">
				<h4 class="card-title pull-left"><?= __('admin.section') ?></h4>
				<div class="pull-right">
					<a class="btn btn-primary" href="<?= base_url('themes/multiple_theme/')  ?>"><?= __('admin.cancel') ?></a>
				</div>
			</div>
			<div class="card-body">
				<form id="admin-form">
					<input type="hidden" name="section_id" value="<?= (int)$section->id ?>">
					<input type="hidden" name="hidden_image" id="hidden_image" value="<?= $section->image ?>">

					<div class="row">
						<div class="col-sm-4">
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
						<div class="col-sm-12">
							<div class="form-group">
								<label class="control-label"><?= __('admin.title') ?></label>
								<input placeholder="<?= __('admin.title') ?>" name="title" value="<?php echo $section->title; ?>" class="form-control" type="text">
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group">
								<label class="control-label"><?= __('admin.description') ?></label>
								<input placeholder="<?= __('admin.description') ?>" name="description" class="form-control" value="<?php echo $section->description; ?>" type="text">
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group">
								<label class="control-label"><?= __('admin.link') ?></label>
								<input placeholder="<?= __('admin.link') ?>" name="link" id="link" class="form-control" value="<?php echo $section->link; ?>" type="text">
								<span class="text-danger" id="linkError"></span>
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group">
								<label class="control-label"><?= __('admin.button_text') ?></label>
								<input placeholder="<?= __('admin.button_text') ?>" name="button_text" class="form-control" value="<?php echo $section->button_text; ?>" type="text">
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group">
        					<label class="control-label"><?= __('admin.position') ?></label>
            					<select class="form-control" name="position" id="position">
            						<option value="1" <?php echo ($section->position == 1)? "Selected" : "" ?>><?= __('admin.left') ?></option>
            						<option value="2" <?php echo ($section->position == 2)? "Selected" : "" ?>><?= __('admin.right') ?></option>
            						<option value="3" <?php echo ($section->position == 3)? "Selected" : "" ?>><?= __('admin.center') ?></option>
            				    </select>
        				    </div>
        				</div>
        				<div class="col-sm-6">
        				    <div class="form-group">
								<label class="control-label"><?= __('admin.status') ?></label>
								<div>
									<input type="radio" <?php echo ($section->status == 1) ? "checked" : "" ?>  name="status" value="1"><?= __('admin.active') ?>
									<input type="radio" <?php echo ($section->status == 0) ? "checked" : "" ?>  name="status" value="0"><?= __('admin.inactive') ?>
								</div>
							</div>
        				</div>
					</div>
					<div class="form-group">
						<label class="control-label"><?= __('admin.section_image') ?></label>
						
						<div class="fileUpload btn btn-sm btn-primary">
							<span><?= __('admin.choose_file') ?></span>
							<input id="uploadBtn" name="avatar" class="upload" type="file">
						</div>
						
						<?php $avatar = $section->image != '' ? $section->image : 'no-image-available.gif' ; ?>
						<?php 
						    if($section->image != '') {
						        echo '<img src="'.base_url().'assets/images/theme_images/'.$avatar.'" id="blah" class="thumbnail" border="0" width="220px">';
						    }
						    else {
						        echo '<img src="'.base_url().'assets/images/'.$avatar.'" id="blah" class="thumbnail" border="0" width="220px">';
						    }
						?>
						
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
		$('#hidden_image').val();
	};

	$(".btn-submit").on('click',function(evt){
	    $("#linkError").empty();
        $this = $("#admin-form");
        $(".btn-submit").btn("loading");
		$('.loading-submit').show();
		var res = $('#link').val();
        var result = res.match(/(http(s)?:\/\/.)?(www\.)?[-a-zA-Z0-9@:%._\+~#=]{2,256}\.[a-z]{2,6}\b([-a-zA-Z0-9@:%_\+.~#?&//=]*)/g);
        if(result == null && !res.includes("http://localhost") && !res.includes("https://localhost"))
        {
            $("#linkError").append('<?= __('admin.please_enter_valid_link') ?>');
            $(".btn-submit").btn("reset");
            return false;
        }
        
        evt.preventDefault();
        var formData = new FormData($("#admin-form")[0]);

        formData = formDataFilter(formData);
        
        $.ajax({
            url:'<?= base_url('themes/save_section') ?>',
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
                console.log(result['errors']);
                if(result['errors']){
                    $.each(result['errors'], function(i,j){
                        $ele = $this.find('[name="'+ i +'"]');
						$ele.parents(".form-group").addClass("has-error");
						if(i == 'avatar')
							$ele.parent().parent().append("<span class='text-danger'>"+ j +"</span>");
						else
							$ele.after("<span class='text-danger'>"+ j +"</span>");
                    });
                }
            },
        })
        return false;
    });
</script>