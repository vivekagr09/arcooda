<div>
	<form id="update-files" enctype="multipart/form-data">
		<div class="form-group">
			<div class="file-upload-wrapper-container">
				<p class="m-0 text-muted"><?= __('admin.upload_update_xx_zip_from_latest_pack') ?></p>
				<div>
					<div class="file-upload-wrapper" data-text="<?= __('admin.select_update_zip_file') ?>">
				      	<input name="update" type="file" class="file-upload-field" value="">
				    </div>
				</div>
			</div>
		</div>

		<div class="text-center">
			<div class="text-primary font-500"><?= __('admin.upload_file_size_limit_is') ?> <?= file_upload_max_size() ?> <span class="upload-file-size"></span></div>
		</div>

		<div class="progress-w">
		    <div class="progress-w-bar" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="max-width:0%">
		    	<span class="title">0%</span>
	    	</div>
	  	</div>
	</form>
</div>
<div class="step-action">
	<div class="text-center">
		<button class="btn btn-success btn-upload-update"><?= __('admin.migrate_update') ?></button>
	</div>
</div>

<div class="modal" id="model-databaseinstruction">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><?= __('admin.how_to_find_update_sql_file') ?></h5>
        <button type="button" class="btn-close" aria-label="Close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <img src="<?= base_url('assets/images/update_zip_1.png') ?>" class='img-responsive w-100'>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal"><?= __('admin.close') ?></button>
      </div>
    </div>
  </div>
</div>
<style type="text/css">
	.wavy {
	  position: relative;
	  -webkit-box-reflect: below -12px linear-gradient(transparent, rgba(0, 0, 0, 0.2));
	  margin-bottom:20px
	}
	.wavy span {
	  position: relative;
	  display: inline-block;
	  font-size: 20px;
	  text-transform: uppercase;
	  animation: animate 1.5s ease-in-out infinite;
	  animation-delay: calc(.1s * var(--i))
	}
	@keyframes animate {
	  0%, 100% {
	    transform: translateY(0px);
	  }
	  20% {
	    transform: translateY(-10px);
	  }
	  40% {
	    transform: translateY(0px);
	  }
	}
</style>
<script type="text/javascript">
	var onlyOne = false;
	function updateProgressBar(percentComplete, title = "<?= __('admin.uploading') ?>") {
		$('.progress-w-bar').css("max-width",percentComplete + "%");
		var text = title+' '+ percentComplete + "%";
		if(percentComplete >= 98 && title == '<?= __('admin.extracting') ?>'){
				text = `<div class="wavy">
						  <span style="--i:1;">E</span>
						  <span style="--i:2;">x</span>
						  <span style="--i:3;">t</span>
						  <span style="--i:4;">r</span>
						  <span style="--i:5;">a</span>
						  <span style="--i:6;">c</span>
						  <span style="--i:7;">t</span>
						  <span style="--i:8;">i</span>
						  <span style="--i:9;">n</span>
						  <span style="--i:10;">g</span>
						  <span style="--i:11;"></span>
						  <span style="--i:12;">Z</span>
						  <span style="--i:13;">i</span>
						  <span style="--i:14;">p</span>
						  <span style="--i:15;">.</span>
						  <span style="--i:16;">.</span>
						</div>`; 
			if(!onlyOne){
				onlyOne = true;
				$(".progress-w-bar .title").text("<?= __('admin.loading') ?>");
				$('#swal2-title').html(text);
			}
		} else {
			$(".progress-w-bar .title").text("<?= __('admin.loading') ?>");
			$('#swal2-title').html(text);
		}
	}

	$('input[name="update"]').change(function(event) {
        var _size = this.files[0].size;
        var fSExt = new Array('Bytes', 'KB', 'MB', 'GB'),
    	i=0;while(_size>900){_size/=1024;i++;}
        var exactSize = (Math.round(_size*100)/100)+' '+fSExt[i];
        
        $('.upload-file-size').html(" <?= __('admin.and_uploading_file_size_is') ?> : "+ exactSize).show();
    });

	function migrateFiles(t) {
		$this = $(t);

		var form = $('#update-files')[0];
		var formData = new FormData(form);
		$.ajax({
			url:'<?= base_url("installversion/migrateFiles") ?>',
			type:'POST',
			dataType:'json',
			data:formData,
			contentType: false,
    		processData: false,
			beforeSend:function(){
				$this.btn("loading");
				$(".progress-w").show();
				Swal.fire({
					icon: 'info',
					allowOutsideClick: false,
					showCancelButton: false, 
					showConfirmButton: false,
					title: '0%',
					footer: '<?= __('admin.uploading_files') ?>',
					html: '<?= __('admin.please_do_not_refresh_while_processing') ?>'
				});
			},
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
                        updateProgressBar(percentComplete);
                    }
                }, false );

                jqXHR.addEventListener( "progress", function ( evt ){
                    if ( evt.lengthComputable ){
                        var percentComplete = Math.round( (evt.loaded * 100) / evt.total );
                        updateProgressBar(percentComplete);
                    }
                }, false );
                return jqXHR;
            },
			success:function(json){
				Swal.close();
				$container = $("#update-files");
				$container.find(".has-error").removeClass("has-error");
				$container.find("span.text-danger").remove();
				
				if(json['success']){
					extractFiles(json, $this);
				}

				if(json['errors']){
					$(".progress-w").hide()
				    $.each(json['errors'], function(i,j){
				        $ele = $container.find('[name="'+ i +'"]');
				        if($ele){
				            $ele.parents(".file-upload-wrapper-container").addClass("has-error");
				            $ele.parents(".file-upload-wrapper-container").append("<span class='text-danger'>"+ j +"</span>");
				        }
				    })
				}
			},
		})
	}


	function extractFiles(data, thatBtn) {
		$.ajax({
			url:'<?= base_url("installversion/extractFiles") ?>',
			type:'POST',
			dataType:'json',
			data:{filepath:data['filepath'], version:data['new_version']},
			beforeSend:function(){
				thatBtn.btn("loading");
				$(".progress-w").show();
				Swal.fire({
					icon: 'info',
					allowOutsideClick: false,
					showCancelButton: false, 
					showConfirmButton: false,
					title: '0%',
					footer: '<?= __('admin.extracting_files') ?>',
					html: '<?= __('admin.please_do_not_refresh_while_processing') ?>'
				});
			},
			complete:function(){
				thatBtn.btn("reset");
				Swal.close();
			},
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
                        updateProgressBar(percentComplete, "<?= __('admin.extracting') ?>");
                    }
                }, false );

                jqXHR.addEventListener( "progress", function ( evt ){
                    if ( evt.lengthComputable ){
                        var percentComplete = Math.round( (evt.loaded * 100) / evt.total );
                        updateProgressBar(percentComplete, "<?= __('admin.extracting') ?>");
                    }
                }, false );
                return jqXHR;
            },
			success:function(json){
				Swal.close();
				$container = $("#update-files");
				$container.find(".has-error").removeClass("has-error");
				$container.find("span.text-danger").remove();
				
				if(json['success']){
					$(".step-container .step-body").html(json['success']);
					setTimeout(function(){ 
						window.location = '<?= base_url('admin'); ?>';
					}, 1500);
				}

				if(json['errors']){
					$(".progress-w").hide()
				    $.each(json['errors'], function(i,j){
				        $ele = $container.find('[name="'+ i +'"]');
				        if($ele){
				            $ele.parents(".file-upload-wrapper-container").addClass("has-error");
				            $ele.parents(".file-upload-wrapper-container").append("<span class='text-danger'>"+ j +"</span>");
				        }
				    })
				}
			},
		})
	}

	$(".btn-upload-update").click(function(){
		confirm_password(this,function(){
			migrateFiles(this);
		},'files');
	})
</script>