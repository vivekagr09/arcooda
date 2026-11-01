<div class="progressbar-container" <?php if(isset($update_version_outside)) {
	?>
	style="margin-top:100px;"		
	<?php
} ?>>
  	<ul class="progressbar">
		<li><?= __('admin.update_files') ?> <span class="pline"></span></li>
		<li><?= __('admin.finish') ?> <span class="pline"></span></li>
	</ul>
</div>

<div class="step-container">
	<div class="card">
		<div class="card-body">
			<div class="step-loading">
				<div class="loader-div"></div>
			</div>
			<div class="step-body"></div>
		</div>
	</div>
</div>
<p class="text-center text-muted"><small><?= __('admin.current_version') ?> <span class="current-version"><?= SCRIPT_VERSION ?></span></small></p>

<script type="text/javascript">
	$(".step-container").on("change", ".file-upload-field", function(){ 
	    $(this).parent(".file-upload-wrapper").attr("data-text",$(this).val().replace(/.*(\/|\\)/, '') );
	});


	function confirm_password(t,cb,fors) {
		$this = $(t);
		$(".step-container #model-adminpassword").remove();

		$.ajax({
			url:'<?= base_url("installversion/confirm_password") ?>',
			type:'POST',
			dataType:'json',
			data:{for:fors},
			beforeSend:function(){$this.btn("loading");},
			complete:function(){$this.btn("reset");},
			success:function(json){

                if(json['status'] === 'error') {
                showPrintMessage(json['message'], 'error');}

				if(json['html']){
					$(".step-container").append(json['html']);
					$("#model-adminpassword").modal("show");
				}

				if(json['callback']){
					if(typeof cb == 'function'){
						setTimeout(function(){ cb($this); }, 500);
					}
				}
			},
		})
	}

	function getStep(number,btn){
		$this = $(this);
		var number = parseInt(number) || 0;

		$(".progressbar-container .progressbar li").removeClass("active")
		$(".progressbar-container .progressbar li").eq(number).prevAll("li").addClass("active")
		$(".progressbar-container .progressbar li").eq(number).addClass("active")
		
		$.ajax({
			url:'<?= base_url("installversion/getStep") ?>',
			type:'POST',
			dataType:'json',
			data:{
				number:number,
			},
			beforeSend:function(){$(btn).btn("loading");},
			complete:function(){$(btn).btn("reset");},
			success:function(json){
				if(json['version']){
					$('.current-version').html(json['version']);
				} 
				
				if(json['html']){
					$(".step-container .step-body").html(json['html']);
				} else {
					window.location = '<?= base_url('admin'); ?>';
				}
			},
		})
	}

	getStep();
</script>