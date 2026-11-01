<div class="card">
    <div class="card-body">
        <form autocomplete="off" method="post" enctype="multipart/form-data" id="setting-form">
            <div class="row row-cols-1 row-cols-md-3 g-4 theme-row">
                <?php foreach ($front_themes as $theme) { ?>
                    <div class="col mb-3 <?php if ($login['front_template'] == $theme['id']) { echo 'active-theme'; } ?>">
                        <div class="card <?php if ($login['front_template'] == $theme['id']) { echo 'border-primary'; } ?>">
                            <img class="card-img-top" src="<?= base_url('assets/images/themes/'.$theme['image']) ?>">
                            <div class="card-body">
                                <h5 class="card-title fw-bold"><?= $theme['name'] ?></h5>
                                <div class="d-flex justify-content-center">
                                    <?php if(in_array($theme['name'],['Index 1','Index 2','Index 3','Index 4','Index 5','Index 6','Index 7','Index 8','Index 9','Index 10','Index 11'])) { ?>
                                        <button type="button" data-id='<?= $theme['id'] ?>' class="btn btn-primary me-2 theme-btn" data-bs-toggle="modal" data-bs-target="#title-and-content-form-modal">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                    <?php } ?>
                                    <?php if(!empty($theme['id']) && $theme['id'] == "multiple_pages") { ?>
                                        <a class="btn btn-primary me-2 theme-btn" href="<?= base_url('themes/multiple_theme') ?>"><i class="bi bi-pencil-square"></i></a>
                                    <?php } ?>
                                    <button type="button" data-id='<?= $theme['id'] ?>' class="btn btn-primary me-2 theme-btn btn-theme-active <?= $login['front_template'] == $theme['id'] ? 'd-none' : '' ?>"><?= __('admin.active') ?></button>
                                    <a href="<?= base_url('?tmp_theme='. $theme['id']) ?>" target="_blank" class="btn btn-primary theme-btn"><?= __('admin.preview') ?></a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </form>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="title-and-content-form-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title text-dark m-0">
                    <?= __('admin.update_home_about_policy') ?>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label"><?= __('admin.select_language') ?></label>
                    <select class="form-select" name="language_id" id="drpLanguage" onchange="changeLanguage();">
                        <?php 
                        if(isset($languages))
                        {
                            $language_id=1;
                            foreach($languages as $language)
                            {?>
                            <option 
                            <?php 
                                if($language['is_default']==1) {echo 'selected';} ?> value="<?=$language['id']?>">
                                <?=$language['name'] ?>
                            </option>
                            <?php  }     
                        }?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label"><?= __('admin.home_heading') ?></label>
                    <input id="loginclient_heading" type="text" class="form-control" value="<?= (isset($loginclient['heading'])) ? $loginclient['heading'] : ""; ?>">
                </div>
                
                <div class="mb-3">
                    <label class="form-label"><?= __('admin.home_content') ?></label>
                    <textarea id="loginclient_content" class="form-control" rows="3"><?= (isset($loginclient['content'])) ? $loginclient['content'] : ""; ?></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label"><?= __('admin.about_content') ?></label>
                    <textarea id="loginclient_about_content" class="form-control" rows="3"><?= (isset($loginclient['about_content'])) ? $loginclient['about_content'] : ""; ?></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label"><?= __('admin.policy_heading') ?></label>
                    <input name="policy_heading" id="policy_heading" type="text" class="form-control" value="<?= (isset($tnc['heading'])) ? $tnc['heading'] : ""; ?>" />
                </div>

                <div class="mb-3">
                    <label class="form-label"><?= __('admin.policy_content') ?></label>
                    <textarea name="policy_content" id="policy_content" class="form-control summernote"><?= (isset($tnc['content'])) ? $tnc['content'] : ""; ?></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <?= __('admin.close') ?>
                </button>
                <button id="loginclient_details_submit" type="button" class="btn btn-primary">
                    <?= __('admin.save_changes') ?>
                </button>
            </div>
        </div>
    </div>
<!-- Modal -->


<!--Js code to auto display the active theme on left top corner-->
<script>
 $(document).ready(function(){
    $(document).on("click",".btn-theme-active",function() {
        var id = $(this).data("id");
        var $col = $(this).closest(".col");
        var $this=$(this);
       
        $.ajax({
            type: 'POST',
            dataType: 'json',
            data: {
                id: id,
                action: 'active_theme',
            },
            beforeSend: function() {
                $(this).addClass("disabled").attr("disabled", true);
            },
            complete: function() {
                $(this).removeClass("disabled").attr("disabled", false);
            },
            success: function(result) {
                $(".alert-dismissable").remove();
                $col.siblings().removeClass('active-theme').find('.card').removeClass('border-primary');
                $col.addClass('active-theme').find('.card').addClass('border-primary');
                $(".btn-theme-active").removeClass('d-none');
                $this.addClass('d-none');
                if(result['success']) {
                    showPrintMessage(result['success'], 'success');
                    var body = $("body");
                    
                    body.stop().animate({scrollTop:0}, 500, 'swing', function() {
                      
                        var div = $col.clone();
                        $col.remove();
                        $(".theme-row").prepend(div);
                    });
                }
            },
        });
    });
});
</script>
<!--Js code to auto display the active theme on left top corner-->



<script type="text/javascript">
	function error_function($cname,$msg)
	{
			$ele=$("#"+ $cname); 
      		$ele.parents(".form-group").addClass("has-error");
			$ele.after("<span class='text-danger'>"+$msg +"</span>");
	}

	$('#loginclient_details_submit').on('click', function(){
		$this = $(this);
      	let data = {
      		loginclient:true,
      		tnc:true,
      		language_id : $('#drpLanguage').val(),
      		heading : $('#loginclient_heading').val(),
      		content : $('#loginclient_content').val(),
      		about_content : $('#loginclient_about_content').val(),
      		policy_heading : $('#policy_heading').val(),
      		policy_content : $('#policy_content').val()
      	};

      	if(data.heading=='')
      	{
      		error_function('loginclient_heading','<?= __('admin.home_heading_is_required') ?>');  
      	}

      	if(data.content=='')
      	{
      		error_function('loginclient_content','<?= __('admin.home_content_is_required') ?>');  
      	}
      	
      	if(data.about_content=='')
      	{
      		error_function('loginclient_about_content','<?= __('admin.about_content_is_required') ?>');  
      	}

      	if(data.policy_heading=='')
      	{
      		error_function('policy_heading','<?= __('admin.policy_heading_is_required') ?>'); 
      	}
      	 
      	if(data.policy_content=='')
      	{
      		error_function('policy_content','<?= __('admin.policy_content_is_required') ?>');  
      	}

		$this.btn("loading");
 		 if(data.heading != "" && data.content != "" && data.about_content != "" && data.policy_heading != "" && data.policy_content != "") {
			$.ajax({
				type:'POST',
				dataType:'json',
				data:data,
				complete:function(){
					$this.btn("reset");
				},
				success:function(response){
					if(response.success) {
						$('#title-and-content-form-modal').modal('hide');
						$('.modal-backdrop').remove();
						showPrintMessage(response['success'],'success');
					} else {
						Swal.fire({
							icon: 'error',
							text: response.message,
						});
					} 
				},
			});
		} else {
			$this.btn("reset");
				console.log(json);
				if(json['errors']){
				    $.each(json['errors'], function(i,j){
				    	console.log(i);
				        $ele = $this.find('[name="'+ i +'"]');
				        if($ele){
				            $ele.parents(".form-group").addClass("has-error");
				            $ele.after("<span class='text-danger'>"+ j +"</span>");
				        }
				    })
				}
		}
	});

	function changeLanguage()
	{
		getContent('<?= base_url("admincontrol/getLoginContent_ajax")?>');
		return false;
	}

	function getContent(url)
    {
		$("#loginclient_heading").val('');
		$("#loginclient_content").val(''); 
		$("#loginclient_about_content").val('');
		$("#policy_heading").val('');
		$("#policy_content").val('');
		$('.summernote').summernote('code', '');
		$('.summernote').html('');

      $this = $(this);
      let data = {
				loginclient : true,
				tnc : true,
				language_id : $('#drpLanguage').val() 
			};
       $.ajax({
            url:url,
            type:'POST',
            dataType:'json',
            data:data,
            beforeSend:function(){$this.btn("loading");},
            complete:function(){$this.btn("reset");},
            success:function(json){ 
               if(json){
                  $("#loginclient_heading").val(json['home_heading']); 
                  $("#loginclient_content").val(json['home_content']); 
                  $("#loginclient_about_content").val(json['about_content']);
                  $("#policy_heading").val(json['policy_heading']);
                  $("#policy_content").val(json['policy_content']);
                  $('.summernote').summernote('code', '')
                  $('.summernote').html(escape($('.summernote').summernote('code', json.policy_content)))
               } else {
                 
               }
            },
       });
    }
</script>