<div class="row">
	<div class="col-12">
		<div class="card m-b-30">
			<div class="card-header">
				<h4 class="card-title pull-left"><?= (isset($faq->faq_id)) ? __('admin.update_faq') : __('admin.create_faq') ?></h4>
				<div class="pull-right">
					<a class="btn btn-primary" href="<?= base_url('themes/multiple_theme/')  ?>"><?= __('admin.cancel') ?></a>
				</div>
			</div>
			<div class="card-body">
				<form id="admin-form">
					<input type="hidden" name="faq_id" value="<?= (isset($faq->faq_id)) ? $faq->faq_id : 0; ?>">
					<input type="hidden" name="faq_theme_id" value="<?= (isset($faq->faq_theme_id)) ? $faq->faq_theme_id : 1; ?>">
					<input type="hidden" name="position" value="<?= (isset($faq->faq_id)) ? $faq->faq_id : $new_position; ?>">
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
					                    if(isset($faq) && $faq->language_id==$language['id'])
					                    {
					                    	echo 'selected';
					                    }
					                    else if($language['is_default']==1) {echo 'selected';} ?> value="<?=$language['id']?>"><?=$language['name'] ?></option>
					                  
					                   <?php  }     
					                }?>
					                
					            </select>
					        </div>    
				    	</div>
						<div class="col-sm-12">
							<div class="form-group">
								<label class="control-label"><?= __('admin.question') ?></label>
								<input placeholder="<?= __('admin.question') ?>" name="faq_question" value="<?= (isset($faq->faq_question)) ? $faq->faq_question : ""; ?>" class="form-control" type="text">
							</div>
						</div>
						<div class="col-sm-12">
							<div class="form-group">
								<label class="control-label"><?= __('admin.answer') ?></label>
								<textarea class="form-control" rows='5' placeholder="<?= __('admin.answer') ?>" name="faq_answer"><?= (isset($faq->faq_answer)) ? $faq->faq_answer : ""; ?></textarea>
							</div>
						</div>
						<div class="col-sm-12">
							<div class="form-group">
								<label class="control-label"><?= __('admin.status') ?></label>
								<div>
									<input type="radio" <?= (isset($faq->status) && $faq->status == 1) ? "checked" : "" ?> <?= (!isset($faq->status)) ? "checked" : "" ?>  name="status" value="1"><?= __('admin.active') ?>
									<input type="radio" <?= (isset($faq->status) && $faq->status == 0) ? "checked" : "" ?>  name="status" value="0"><?= __('admin.inactive') ?>
								</div>
							</div>
						</div>
						
					</div>
					<div class="form-group">
						<button type="button" class="btn btn-primary btn-submit"><?= (isset($faq->faq_id)) ? __('admin.update') :  __('admin.create') ?></button>
						<span class="loading-submit"></span>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
$(".btn-submit").on('click',function(evt){
$this = $("#admin-form");
$(".btn-submit").btn("loading");
$('.loading-submit').show();
evt.preventDefault();
var formData = new FormData($("#admin-form")[0]);
formData = formDataFilter(formData);
$.ajax({
url:'<?= base_url('themes/store_faq') ?>',
type:'POST',
dataType:'json',
cache:false,
contentType: false,
processData: false,
data:formData,
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