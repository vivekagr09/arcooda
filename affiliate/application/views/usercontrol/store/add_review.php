<?php 
$db =& get_instance();
$userdetails=$db->userdetails();
?>
<link rel="stylesheet" type="text/css" href="<?= base_url("assets/plugins/ui/jquery-ui.min.css") ?>">
<script type="text/javascript" src="<?= base_url('assets/plugins/ui/jquery-ui.min.js') ?>"></script>
<link rel="stylesheet" type="text/css" href="<?= base_url("assets/plugins/select2/select2.min.css") ?>">
<script type="text/javascript" src="<?= base_url('assets/plugins/select2/select2.full.min.js') ?>"></script>
<style>
	.jscolor-picker-wrap{
		z-index:999999 !important;
	} 
</style>

<form class="form-horizontal" method="post" action=""  enctype="multipart/form-data" id="form_form">
<div class="row">
	<div class="col-sm-12">
		<div class="card">
			<div class="card-header bg-blue-payment">
				<div class="card-title-white pull-left m-0"><?= (int)$review['rating_id'] == 0 ? __('user.add_review') : __('user.edit_review') ?></div>
			</div>

			<div class="card-body">
			 
				<input type="hidden" id="rating_id" name="rating_id" value="<?php echo $review['rating_id'] ?>">
			 	<div class="row">
			 		<div class="col-sm-12"> 
							<div class="form-group">
								<label class="col-form-label"><?= __('user.products') ?></label>
								<div class="category-container">
									<select id="product_name" name="product_name" class="form-control" >
									 <option value="">Select</option>
										<?php if(isset($products)){ ?>
											<?php foreach ($products as $key => $product) { ?>
												<option value="<?= $product['product_id'] ?>" <?php echo  $review['products_id']==$product['product_id'] ? 'selected' : ''?> ><?=$product['product_name']; ?></option> 
											<?php } ?>
										<?php } ?>
									</select>	 
								</div>
							</div>
				 
					</div>
							 
					 <div class="col-sm-4">
						<div class="form-group">
				            <label class="control-label"><?= __('user.firstname') ?></label>
				            <input name="firstname" id="firstname" type="text" value="<?php echo isset($review['firstname']) ? $review['firstname'] : '' ?>" class="form-control" />
				        </div>

				        <div class="form-group">
				            <label class="control-label"><?= __('user.lastname') ?></label>
				            <input name="lastname" id="lastname" type="text" value="<?php echo isset($review['lastname']) ? $review['lastname'] : '' ?>" class="form-control" />
				        </div>
				    </div>
				    <?php if($vendormanagereviewimage == 1){?>
				    <div class="col-sm-4">
						<div class="form-group form-image-group">
							<div>
								<label class="col-form-label"><?= __('user.user_image') ?></label><br>
								<div class="fileUpload btn btn-sm btn-primary">
									<span><?= __('user.choose_file') ?></span>
									<input onchange="readURL(this,'#featureImage')" id="user_image" name="user_image" class="upload" type="file">
									<input type="hidden" name="user_image_hidden"  value="<?php echo $review['avatar']; ?>" />
								</div>
								<?php $user_image = $review['avatar'] != '' ? 'assets/images/users/' . $review['avatar'] : 'assets/images/no-user_image.jpg' ; ?>
								<img src="<?php echo base_url($user_image); ?>" id="featureImage" class="thumbnail" border="0" width="220px" >
								 
							</div>
						</div>
					</div>
					<?php }?>
					<div class="col-sm-12">
			            <div class="form-group">
			                <label class="control-label"><?= __('user.review') ?></label>
			                <textarea name="review_description" id="review_description" placeholder="<?= __('user.enter_review') ?>" cols="80" class="form-control"><?= $review['rating_comments']?></textarea>
			                <div class="help-block"><span class="text-danger"><?= __('store.note') ?></span> <?= __('store.html_is_not_translated') ?></div>
			            </div>
			            
			            <div class="form-group">
			                <label class="control-label"><?= __('user.rating') ?></label>
			                <div class="clearfix"></div>
			                <div class="give-rating"></div>
			                <input name="rating" value="<?= $review['rating_number']?>" id="rating_star" type="hidden" />
			            </div>

			            <div class="endtime-chooser row">
							<div class="col-sm-12">
								<div class="form-group">
									<label class="control-label d-block"><?= __('user.choose_reviews_datetime') ?>  </label>
									<div  class='custom_time_container'>
										<input type="text" class="form-control" value="<?= $review['rating_created'] ? date("d-m-Y H:i",strtotime($review['rating_created'])) : '' ?>" name="rating_created" id="endtime" placeholder="<?= __('user.choose_reviews_datetime'); ?>" >
									</div>
								</div>
							</div>
						</div>
						 <div class="form-group">
							<button type="submit" class="btn btn-lg btn-default btn-submit btn-success" name="save"><?= __('user.save') ?></button>
						</div> 
					</div>	
				</div>
			</div>
		</div>
	</div>
</div>
			
<style type="text/css">
	
	.jq-stars {
    display: inline-block;
}
.jq-rating-label {
    font-size: 22px;
    display: inline-block;
    position: relative;
    vertical-align: top;
    font-family: helvetica, arial, verdana;
}
.jq-star {
    width: 100px;
    height: 100px;
    display: inline-block;
    cursor: pointer;
}
.jq-star-svg {
    padding-left: 3px;
    width: 100%;
    height: 100%;
}
.jq-star:hover .fs-star-svg path {}
.jq-star-svg path {
    /* stroke: #000; */
    stroke-linejoin: round;
}
.xdsoft_datetimepicker.xdsoft_inline{display: table;}
</style>
<link rel="stylesheet" type="text/css" href="<?= base_url('assets/store/default/slick/') ?>slick.css"/>
<link rel="stylesheet" type="text/css" href="<?= base_url('assets/store/default/slick/') ?>slick-theme.css"/>
<script type="text/javascript" src="<?= base_url('assets/store/default/slick/') ?>slick.js"></script>
<script type="text/javascript" src="<?= base_url('assets/plugins/store/') ?>jquery.star-rating-svg.js"></script> 
 <script type="text/javascript"> 

	var cache = {};
 
 
 
	$(".btn-submit").on('click',function(evt){
		evt.preventDefault();
		$btn = $(this);
		var formData = new FormData($("#form_form")[0]);
		formData.append("allow_upload_file", "1");
		 formData.append("action", $(this).attr("name"));

		formData = formDataFilter(formData);
		$this = $("#form_form");	       

		$btn.btn("loading");
		$.ajax({
			url:'<?= base_url('usercontrol/manage_review') ?>',
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
						$('.loading-submit').text(percentComplete + "% "+'<?= __('user.loading') ?>');
					}
				}, false );

				jqXHR.addEventListener( "progress", function ( evt ){
					if ( evt.lengthComputable ){
						var percentComplete = Math.round( (evt.loaded * 100) / evt.total );
						$('.loading-submit').text("Save");
					}
				}, false );
				return jqXHR;
			},
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
					$.each(result['errors'], function(i,j){
						$ele = $this.find('[name="'+ i +'"]');
						if($ele){
							$ele.parents(".form-group").addClass("has-error");
							$ele.after("<span class='text-danger'>"+ j +"</span>");
						}
					});
				}
			},
		});

		return false;
	});

 
if($('#endtime').length) {

	$('#endtime').datetimepicker({
		format:'d-m-Y H:i',
		inline:true,
	});
}

$(document).on('click','.deleteuser',function(e){
        var deleteaction = $(this).data('url');
        var message = '<?= __('user.lost_all_data_are_you_sure_delete') ?>';
        Swal.fire({
            icon: 'warning',
            html:message,
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: '<?= __('user.yes') ?>',
            cancelButtonText: '<?= __('user.no') ?>'

        }).then((result)=>{
           if(result.value) window.location.href = deleteaction;  
       });
    });


$(document).ready(function() {
	 //$("#rating_star").val();

	if($("#rating_star").val()>0)
		$defaultrating=$("#rating_star").val();
	else
		$defaultrating=0;

if($('.give-rating').length) {
      $('.give-rating').starRating({
        initialRating:$defaultrating,
        starSize: 20,
        readOnly:false,
        disableAfterRate:false,
        callback: function(currentRating, $el){
            $("#rating_star").val(currentRating);
        }
      });
    }
 

});		</script>
