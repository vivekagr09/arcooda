<style type="text/css">


.cat{
  margin: 4px;
  background-color: #F3A4CF;
  border-radius: 4px;
  border: 1px solid #fff;
  overflow: hidden;
  float: left;
}

.cat label {
  float: left; line-height: 3.0em;
  width: 8.0em; height: 3.0em;
}

.cat label span {
  text-align: center;
  padding: 3px 0;
  display: block;
}

.cat label input {
  position: absolute;
  display: none;
  color: #fff !important;
}
/* selects all of the text within the input element and changes the color of the text */
.cat label input + span{color: #fff;
    font-size: 16px;
    font-weight: 900;}


/* This will declare how a selected input will look giving generic properties */
.cat input:checked + span {
    color: #ffffff;
    text-shadow: 0 0  6px rgba(0, 0, 0, 0.8);
}
.action input:checked + span{background-color: #214A09;}
</style>
<script src="https://code.jquery.com/ui/1.13.1/jquery-ui.js"></script>
<script type="text/javascript">
	var imageid=0;
	$(document).on("change",'#image_id',function(event){
	  imageid=$(this).val();
	  $('.marker-class').hide();
	  $('.display_other_image').hide();
	  $('.dragimage').show();
	  $('.markerdisplay-'+imageid).show();
	  $('.image-marker-'+imageid).show();
	});
</script>
<?=formatHTMLTitle('<a href="#" onclick="toggleSection(\'activatemarker\');return false">Activate Marker</a>', '', false)?>
<div id="activatemarker">
	<small style="color:#f00;margin-left: 30px;margin-bottom: 10px;"><i>Clicking on the  marker will activated it (green) and to click on it again to deactive it(pink)</i></small>
	<div style="margin: auto; border-collapse: collapse; width: 95%;display: flex;min-height: 30vh;"> 
		<div style="width:25%;"> <?php 
				foreach ($marker as $key => $mark) {
				$typearray=['tooltip_plus'=>'Tooltip plus','link'=>'Link','tooltip_play'=>'Tooltip Play','infowindow'=>'Info Window'];
				$marker_locationmarker=view360MarkerLocationBymarker($mark['id']);
			?>		
			<div style="width: 100%;display: inline-block;">
					<span style="width:5%;text-align: center;float:left;padding-top: 14px; padding-bottom: 12px;color:#f00;"><?= ($marker_locationmarker)?$product_screenshots[$marker_locationmarker['image_id']]['rank']:''?></span>
					<div class="cat action" style="width:80%;text-align: center;">
					   <label style="width:100%;">
					      <input type="checkbox" value="<?= $mark['id'] ?>" name="activemarker[]" <?= ($mark['status']==1)?'checked':''?>><span style="font-size: 15px;"><?= $typearray[$mark['type']]?>-<?= $mark['name'] ?></span>
					   </label>
					</div>
					<span class="dragimage display_other_image" id="dragicon-<?=$mark['id']?>" style="width: 12%;"><img src="/img/<?= $mark['type']?>.png" style="padding-top: 12px; padding-bottom: 12px;"></span>
				<?php foreach ($product_screenshots as $shotv) { 
					$marker_location=view360MarkerLocation($mark['id'],$shotv['id']);

					?>
						<?php if($marker_location){   ?>
						<input type="hidden" name="marker_location[<?=$mark['id']?>][<?= $shotv['id']?>][id]" value="<?= $marker_location['id']?>">
						<input type="hidden" name="marker_location[<?=$mark['id']?>][<?= $shotv['id']?>][top]" value="<?= $marker_location['top']?>">
						<input type="hidden" name="marker_location[<?=$mark['id']?>][<?= $shotv['id']?>][left]" value="<?= $marker_location['left']?>">
						<?php  		
						}else{ ?>
							<input type="hidden" name="marker_location[<?=$mark['id']?>][<?= $shotv['id']?>][top]" value="0">
						<input type="hidden" name="marker_location[<?=$mark['id']?>][<?= $shotv['id']?>][left]"value="0">						
					<?php }  } ?>
					 <script type="text/javascript">
				$(function() {
					var markerid="<?=$mark['id']?>";
					var dragmarker=markerid;
				   $( "#dragicon-"+dragmarker).draggable({
				   		containment: '#dragablediv',
					    drag: function(event) {
					        var top = parseInt(($(this).position().top-50)-$('#dragablediv').position().top);
					        var left = parseInt(($(this).position().left-212)-$('#dragablediv').position().left);
					        $(this).addClass('markerdisplay-'+imageid+'');
					        $(this).removeClass('dragimage');
					        setValue('marker_location['+markerid+']['+imageid+'][top]',top)
					        setValue('marker_location['+markerid+']['+imageid+'][left]',left)
					    },
				   	});
				});
				</script>
		<?php echo '</div>'; } ?>
			</div>
			<div style="width: 75%;">
				<select class="wide" name="image_id" id="image_id">
					<?php 
						$image_count=1;
						foreach ($product_screenshots as $shotv) { 
						echo '<option value="'.$shotv['id'].'" data-name="'.$shotv['name'].'">#image'.$image_count.'</option>';
						$image_count++;
					}?>
				</select>
				<div id="dragablediv" style="width:850px;height: 600px;background-color: #fff">
					<?php 
					$image_count=1;
					foreach ($product_screenshots as $shotv) { ?>
					<div class="image-marker-<?= $shotv['id']?> marker-class" style="display:none;" id="image-marker-<?= $shotv['id']?>">
					<img src="addImages.php?name=<?=$shotv['name'] ?>&action=view&thumb=true&type=<?= $shotv['type']?>" style="overflow: hidden;width:500px;height: 500px;display: block;margin-left: auto;margin-right: auto;" >
					</div>
				<?php
				if($image_count==1){
					echo '<script>
					$(".image-marker-'.$shotv["id"].'").show();imageid='.$shotv["id"].'</script>';
				}

				 $image_count++; } ?>
					</div>
				</div>
			</div>
	</div>
	<?php ?>
	<div style="padding: 10px;text-align: center;"><button type="button" style="padding: 12px 81px;cursor:pointer;" onclick='window.open("/webadmin/product_360/preview.php?id=<?= $item['id']?>","_blank")'>PREVIEW</button></div>
	<?php 
		$count=0;
		foreach ($marker as $key => $mark) {
	
				foreach ($product_screenshots as $shotv) { 
					$marker_location=view360MarkerLocation($mark['id'],$shotv['id']);
					if($marker_location && ($marker_location["top"]>0 || $marker_location["left"]>0)){ ?>
						<script>
						$(function() {
							$("#dragicon-<?=$mark["id"] ?>").addClass("markerdisplay-<?=$shotv["id"] ?>");
					        $("#dragicon-<?=$mark["id"] ?>").removeClass('dragimage');
							$("#dragicon-<?=$mark["id"] ?>").css("top",parseInt("<?=$marker_location["top"]-($count*50)?>"));
							$("#dragicon-<?=$mark["id"] ?>").css("left",parseInt("<?=$marker_location["left"]?>"));
						});
						</script>
					<?php } 
			}
			$count++;
		}
	?>
</div>
<script type="text/javascript">
$(document).ready(function(){
	$('.display_other_image').hide();
	$('.dragimage').show();
	$('.markerdisplay-'+imageid).show();
});	
var product_screenshots=Object.values(<?= json_encode($product_screenshots)?>);
$('input[type="checkbox"]').change(function() {
     if (!$(this).is(':checked')) {
     		$("#dragicon-"+this.value).css("top",parseInt(0));
			$("#dragicon-"+this.value).css("left",parseInt(0));
			$("#dragicon-"+this.value).show();
			$("#dragicon-"+this.value).addClass('dragimage');
			
			for (const x of product_screenshots){ 
				setValue('marker_location['+this.value+']['+x.id+'][top]',0)
				setValue('marker_location['+this.value+']['+x.id+'][left]',0)
			}
        }
});
</script>
<!-- <script type="text/javascript">
$(function() {
   $( ".dragimage" ).draggable({
   		containment: '#dragablediv',
	    drag: function(event) {
	        var top = $(this).position().top;
	        var left = $(this).position().left;

	        console.log(top, left);
	    },
   	});
});
</script> -->