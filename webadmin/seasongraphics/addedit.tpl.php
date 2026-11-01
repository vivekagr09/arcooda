<div align="center">
	<?= formatHTMLTitle('Session Graphics');?>
</div>
<table class="wide" align="center" style="width:98%">
	<tr>
		<td class="left_title" style="width:20%">Theme Title :</td>
		<td colspan="2"><input type="text" name="title" value="<?= $season['title'] ?>" style="width:100%" /></td>
	</tr>
	<tr>
		<td class="left_title">
			Start Date : 
		</td>
		<td>
			<select name="start_day" style="width:33%" class="display_start_date">
				<?php for($i=1; $i<=31;$i++){
					$selected=($i==date("d", strtotime($season['start_date'])))?'selected':'';
					echo "<option value=".$i." ".$selected.">$i</option>";
				}?>
			</select>
			<select name="start_month" style="width:33%" class="display_start_date">
				<?php 
					$months = ['01'=>'Jan', '02'=>'Feb', '03'=>'Mar', '04'=>'Apr', '05'=>'May', '06'=>'Jun', '07'=>'Jul', '08'=>'Aug', '09'=>'Sep', '10'=>'Oct','11'=> 'Nov', '12'=>'Dec'];
					foreach ($months as $key => $value) {
						$selected=($key==date("m", strtotime($season['start_date'])))?'selected':'';
						echo "<option value=".$key." ".$selected.">$value</option>";
					}
				?>	
			</select>
			<input type="text" id="display_start_date" style="width:30%" value="<?= date("jS M", strtotime($season['start_date'])) ?>">
		</td>
	</tr>
	<tr>
		<td class="left_title">
			Finish Date :
		</td>
		<td>
			<select name="end_day" style="width:33%" class="display_end_date">
				<?php for($i=1; $i<=31;$i++){
					$selected=($i==date("d", strtotime($season['end_date'])))?'selected':'';
					echo "<option value=".$i." ".$selected.">$i</option>";
				}?>
			</select>
			<select name="end_month" style="width:33%" class="display_end_date">
				<?php 
					$months = ['01'=>'Jan', '02'=>'Feb', '03'=>'Mar', '04'=>'Apr', '05'=>'May', '06'=>'Jun', '07'=>'Jul', '08'=>'Aug', '09'=>'Sep', '10'=>'Oct','11'=> 'Nov', '12'=>'Dec'];
					foreach ($months as $key => $value) {
						$selected=($key==date("m", strtotime($season['end_date'])))?'selected':'';
						echo "<option value=".$key." ".$selected.">$value</option>";
					}
				?>	
			</select>
			<input type="text" id="display_end_date" style="width:30%" value="<?= date("jS M", strtotime($season['end_date'])) ?>">
		</td>
	</tr>
	<tr>
		<td class="left_title">
			Header Graphic :
		</td>
		<td><input type="button" value="Header Graphic" class="item_screenshots_image_file" display-class="header_image" onclick="AttachmentAdd();return false;">
		</td>

	</tr>
	<tr>
		<td class="left_title"></td>
		<td>
		<table class="wide" style="border: 0px;margin-top: 10px;width:100%;">
            <tr class="alt header_image">
            	<?php 
            		if(isset($season['header_graphic']) && count(json_decode($season['header_graphic']))){ 
            			foreach (json_decode($season['header_graphic']) as $key => $data) {
            			?>
            				<td style="width:20%" id="<?= 'header_image_'.$key?>">
            					<table class="wide" style="border: 0px;margin-top: 10px;">
            						<tr class="alt">
            							<td>
            								<img src="addAttachment.php?action=view&name=<?= $data  ?>&thumb=1" width="100px">
            								<input type="hidden" value="<?= $data  ?>" name="header_image[]"><p><a style="cursor:pointer;text-align:center;margin-left:34px;margin-top:10px;" onclick="deleteImage('header_image_<?= $key?>')">Delete</a></p></td></tr></table></td>
            	<?php	} } ?>
            </tr>
        </table>
    	</td>
	</tr>
	<tr>
		<td class="left_title">
			Menu Graphic :
		</td>
		<td><input type="button" value="Menu Graphic" class="item_screenshots_image_file" display-class="menu_image_data" onclick="AttachmentAdd();return false;">
		</td>
	</tr>
	<tr>
		<td class="left_title"></td>
		<td>
		<table class="wide" style="border: 0px;margin-top: 10px;width:100%;">
            <tr class="alt menu_image_data">
<?php 
foreach (json_decode($season['menu_graphic'] ?? '[]', true) ?? [] as $key => $data) {
?>
            				<td style="width:20%" id="<?= 'menu_graphic_'.$key?>">
            					<table class="wide" style="border: 0px;margin-top: 10px;">
            						<tr class="alt">
            							<td>
            								<img src="addAttachment.php?action=view&name=<?= $data  ?>&thumb=1" width="100px">
            								<input type="hidden" value="<?= $data  ?>" name="menu_image_data[]"><p><a style="cursor:pointer;text-align:center;margin-left:34px;margin-top:10px;" onclick="deleteImage('menu_graphic_<?= $key?>')">Delete</a></p></td></tr></table></td>
<?php
}
?>
            </tr>
        </table>
    	</td>
	</tr>
	<tr>
		<td class="left_title">
			Footer Graphic :
		</td>
		<td><input type="button" value="Footer Graphic" class="item_screenshots_image_file" display-class="display_image" onclick="AttachmentAdd();return false;">
		</td>	
	</tr>
	<tr>
		<td class="left_title"></td>
		<td>
		<table class="wide" style="border: 0px;margin-top: 10px;width:100%;">
            <tr class="alt display_image">
            	<?php 
            		if(isset($season['footer_graphic']) && count(json_decode($season['footer_graphic']))){ 
            			foreach (json_decode($season['footer_graphic']) as $key => $data) {
            			?>
            				<td style="width:20%" id="<?= 'footer_graphic_'.$key?>">
            					<table class="wide" style="border: 0px;margin-top: 10px;">
            						<tr class="alt">
            							<td>
            								<img src="addAttachment.php?action=view&name=<?= $data  ?>&thumb=1" width="100px">
            								<input type="hidden" value="<?= $data  ?>" name="display_image[]"><p><a style="cursor:pointer;text-align:center;margin-left:34px;margin-top:10px;" onclick="deleteImage('footer_graphic_<?= $key?>')">Delete</a></p></td></tr></table></td>
            	<?php	} } ?>
            </tr>
        </table>
    	</td>
	</tr>
	  <tr style="line-height: 20px;">
        <th class="left_title">Status :</th>
        <td><select name="status">
            <?= formatHTMLOptions(array('y' => 'Active', 'n' => 'Not Active'), $season['status']) ?>
        </select></td>
    </tr>
</table>
<br />
<center>
	<input type="hidden" name="id" value="<?= $season['id'] ?>">
	<input type="button" value="<?= ($add)?'Add':'Update'?>" onclick="javascript:this.form.submit();" />
</center>
<script type="text/javascript">
	  /*$(".item_screenshots_image_file").change(function() {
        var file = this.files[0];
        var reader = new FileReader();
       var displayclass=$(this).attr('display-class');
       console.log(displayclass);
        reader.onload = function (e) {
        	var image='<img src="'+e.target.result+'" width="100px;">';
            $('.'+displayclass+'').html(image);
        };
        reader.readAsDataURL(file);
    });*/
	$(".display_start_date").change(function() {
		var startd=getValue('start_day');
		var startm=getValue('start_month');
		newdate=startm+'-'+startd+'-'+<?= date('Y')?>;
		d=new Date(newdate)
		  var months = ['January', 'February', 'March', 'April', 'May', 'June',
		    'July', 'August', 'September', 'October', 'November', 'December'
		  ];
	  	var e = new Date(d);
   		var data=addOrd(e.getDate()) + ' ' + months[e.getMonth()];
   		$('#display_start_date').val(data);
	});
	$(".display_end_date").change(function() {
		var startd=getValue('end_day');
		var startm=getValue('end_month');
		newdate=startm+'-'+startd+'-'+<?= date('Y')?>;
		d=new Date(newdate)
		  var months = ['January', 'February', 'March', 'April', 'May', 'June',
		    'July', 'August', 'September', 'October', 'November', 'December'
		  ];
	  	var e = new Date(d);
   		var data=addOrd(e.getDate()) + ' ' + months[e.getMonth()];
   		$('#display_end_date').val(data);
	});

function addOrd(n) {
  var ords = [, 'st', 'nd', 'rd'];
  var ord, m = n % 100;
  return n + ((m > 10 && m < 14) ? 'th' : ords[m % 10] || 'th');
}
 $(".item_screenshots_image_file").click(function() {
   var displayclass=$(this).attr('display-class');
   openWindow('addAttachment.php?action=add&class='+displayclass+'', 700, 300, true);
});
function setImagedata(data){
	var image='<td style="width:20%" id="'+data.id+'"><table class="wide" style="border: 0px;margin-top: 10px;"><tr class="alt"><td><img src="addAttachment.php?action=view&name='+data.name+'&thumb=1" width="100px"><input type="hidden" value="'+data.name+'" name="'+data.class+'[]"><p><a style="cursor:pointer;text-align:center;margin-left:34px;margin-top:10px;" onclick="deleteImage(\''+data.id+'\')">Delete</a></p></td></tr></table></td>';
            $('.'+data.class+'').append(image);
}
function deleteImage(id){
	openWindow('addAttachment.php?action=delete&id='+id+'', 700, 300, true);
}
function deleteAttachment(id){
	 var theRowth = document.getElementById(id);
    theRowth.parentNode.removeChild(theRowth)
}
</script>