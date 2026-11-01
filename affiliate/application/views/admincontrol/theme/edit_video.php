<style>
.tooltip-inner {
	text-align:left;
    max-width: 100% !important;
}
</style>

<div class="row">
	<div class="col-12">
		<div class="card m-b-30">
			<div class="card-header">
				<h4 class="card-title pull-left"><?= __('admin.update_video') ?></h4>
				<div class="pull-right">
					<a class="btn btn-primary" href="<?= base_url('themes/multiple_theme/')  ?>"><?= __('admin.cancel') ?></a>
				</div>
			</div>
			<div class="card-body">
				<form id="admin-form">
					<input type="hidden" name="video_id" value="<?= $video->video_id ?>">
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
					                    if(isset($video) && $video->language_id==$language['id'])
					                    {
					                    	echo 'selected';
					                    }
					                    else if($language['is_default']==1) {echo 'selected';} ?> value="<?=$language['id']?>"><?=$language['name'] ?></option>
					                  
					                   <?php  }     
					                }?>
					                
					            </select>
					        </div>    
				    	</div>
				    	<div class="col-md-12">
				    	</div>
						<div class="col-sm-6">
							<div class="form-group">
								<label class="control-label"><?= __('admin.video_title') ?></label>
								<input placeholder="<?= __('admin.enter_video_title') ?>" name="video_title" value="<?php echo $video->video_title; ?>" class="form-control" type="text">
							</div>
							<div class="form-group">
								<label class="control-label"><?= __('admin.video_sub_title') ?></label>
								<input placeholder="<?= __('admin.enter_sub_title_for_video') ?>" id="input" name="video_sub_title" value="<?php echo $video->video_sub_title; ?>" class="form-control" type="text">
							</div>
							<div class="form-group">
								<label class="control-label"><?= __('admin.video_link') ?> <span data-html="true"  data-placement="right" data-toggle="tooltip" data-container="body" title="<h6>we support all links like:</h6> <ul><li>https://www.youtube.com/watch?v=R1StjWM_LOE&feature=youtu.be</li><li>https://www.youtu.com/R1StjWM_LOE</li><li>https://www.youtube.com/embed/R1StjWM_LOE</li></ul>"></span></label>
								<input placeholder="<?= __('admin.enter_url_link_video') ?>" name="video_link" id="link" class="form-control" value="<?php echo $video->video_link; ?>" type="text" >
								<span class="text-danger" id="linkError"></span>
							</div>
							<div class="form-group">
								<label class="control-label"><?= __('admin.status') ?></label>
								<div>
									<input type="radio" <?php echo ($video->status == 1) ? "checked" : "" ?>  name="status" value="1"><?= __('admin.active') ?>
									<input type="radio" <?php echo ($video->status == 0) ? "checked" : "" ?>  name="status" value="0"><?= __('admin.inactive') ?>
								</div>
							</div>
						</div>
						<div class="col-sm-6">
						<?php
						if(strpos(strtolower($video->video_link),'youtube') !== false && strpos($video->video_link,'embed') == false){
							$id = explode("v=",$video->video_link);
							$video->video_link = 'https://www.youtube.com/embed/'.$id[1];
						}
						if(strpos(strtolower($video->video_link),'youtu.be') !== false && strpos($video->video_link,'embed') == false){
							$id = explode("/",$video->video_link);
							$video->video_link = 'https://www.youtube.com/embed/'.$id[3];
						}
						?>
							<iframe width="560" height="315" src="<?= $video->video_link ?>" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen id ="ifrm_id"></iframe>
						</div>
					</div>
					<div class="form-group">
						<button type="button" class="btn btn-primary btn-submit"> <?= __('admin.update') ?> </button>
						<span class="loading-submit"></span>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<script>
$("#link").change(function(){
  var url = $('#link').val();
	if(url.toLowerCase().includes("youtube") && !url.toLowerCase().includes("embed")){
		$id = url.split("v=");
		url = 'https://www.youtube.com/embed/'+$id[1];
	} else if(url.toLowerCase().includes("youtu") && !url.toLowerCase().includes("embed")){
		$id = url.split("/");
		url = 'https://www.youtube.com/embed/'+$id[3];
	}
  	loadIframe('ifrm_id',url);
});

  function loadIframe(iframeName, url) {
    var $iframe = $('#' + iframeName);
    if ( $iframe.length ) {
        $iframe.attr('src',url);   
        return false;
    }
    return true;
}
</script>

<script type="text/javascript">
	
	var loadFile = function(event) {
	var image = document.getElementById('output');
	image.src = URL.createObjectURL(event.target.files[0]);
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
url:'<?= base_url('themes/update_video') ?>',
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
if($ele){
$ele.parents(".form-group").addClass("has-error");
$ele.after("<span class='text-danger'>"+ j +"</span>");
}
});
}
},
})
return false;
});
</script>