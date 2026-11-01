<div class="row">
	<div class="col-12">
		<div class="card m-b-30">
			<div class="card-header">
				<h4 class="card-title pull-left"><?= __('admin.recommendations') ?></h4>
				<div class="pull-right">
					<a class="btn btn-primary" href="<?= base_url('themes/multiple_theme/')  ?>"><?= __('admin.cancel') ?></a>
				</div>
			</div>
			<div class="card-body">
				<form id="admin-form">
					<input type="hidden" name="recommendation_id" value="<?= (int)$recommendation->id ?>">
					<input type="hidden" name="hidden_image" id="hidden_image" value="<?= $recommendation->image ?>">


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
				    	<div class="col-sm-12"></div>
						<div class="col-sm-6">
							<div class="form-group">
								<label class="control-label"><?= __('admin.title') ?></label>
								<input placeholder="<?= __('admin.title') ?>" name="title" value="<?php echo $recommendation->title; ?>" class="form-control" type="text">
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group">
								<label class="control-label"><?= __('admin.occupation') ?></label>
								<input placeholder="<?= __('admin.occupation') ?>" name="occupation" value="<?php echo $recommendation->occupation; ?>" class="form-control" type="text">
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group">
								<label class="control-label"><?= __('admin.description') ?></label>
								<input placeholder="<?= __('admin.description') ?>" name="description" class="form-control" value="<?php echo $recommendation->description; ?>" type="text">
							</div>
						</div>

        				<div>
        				    <div class="form-group">
								<label class="control-label"><?= __('admin.status') ?></label>
								<div>
									<input type="radio" <?php echo ($recommendation->status == 1) ? "checked" : "" ?>  name="status" value="1"><?= __('admin.active') ?>
									<input type="radio" <?php echo ($recommendation->status == 0) ? "checked" : "" ?>  name="status" value="0"><?= __('admin.inactive') ?>
								</div>
							</div>
        				</div>
					</div>
					<div class="form-group">
						<label class="control-label"><?= __('admin.recommendations_image') ?></label>
						
						<div class="fileUpload btn btn-sm btn-primary">
							<span><?= __('admin.choose_file') ?></span>
							<input id="uploadBtn" name="avatar" class="upload" type="file">
						</div>
						<?php $avatar = $recommendation->image != '' ? $recommendation->image : 'no-image-available.gif' ; ?>
						<?php 
						    if($recommendation->image != '') {
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

        evt.preventDefault();
        var formData = new FormData($("#admin-form")[0]);

        formData = formDataFilter(formData);
        
        $.ajax({
            url:'<?= base_url('themes/save_recommendation') ?>',
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