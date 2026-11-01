<?= formatHTMLTitle('Carousel Setting') ?>

<table class="table_fields">
		<tr>
			<td rowspan="11" style="background: #eed;width:50px;"><button type="button" style="padding: 12px 11px;cursor:pointer;" onclick='window.open("/webadmin/product_carousel/help.html","_blank")'>HELP</button></td>
			<?php $product_tooltip='<p style="padding10px;">this is the topology for the geometry of the thumbnails of the carousel, it can be "normal", "ring" or "star"</p>.';?>
			<th>Topology : <i class="far fa-question-circle" onmouseover="<?php hecho('tt_in(' . jecho($product_tooltip, 1) . ');') ?>" onmouseout="tt_out();"></i></th>
			<td><select class="wide update_crausel" name="item[topology]">
	                <?= formatHTMLOptions(['normal'=>'Normal','ring'=>'Ring','star'=>'Star'], $item['topology']) ?>
	        </td>
		</tr>
		<tr>
			<?php $product_tooltip='<img src="images/carousel-x-radius-icon.png" style="max-width:96px;max-height:96px;position:absolute;left:8px;" /><br/><ul style="padding-left:128px;white-space:nowrap;min-height:96px;"><li>The carousel horizontal/x-axis radius in px.</li> </ul>';?>
			<th><span style="color:red;"><small>*</small></span>Carousel-X-Radius :  <i class="far fa-question-circle" onmouseover="<?php hecho('tt_in(' . jecho($product_tooltip, 1) . ');') ?>" onmouseout="tt_out();"></i></th>
			
			<td><input class="wide update_crausel" type="number" name="item[x_radious]"  value="<?= $item['x_radious']?>" style="width:98%;" max="1600" min="500" /></td>
		</tr>
		<tr>
			<?php $product_tooltip='<img src="images/carousel-y-radius-icon.png" style="max-width:96px;max-height:96px;position:absolute;left:8px;" /><br/><ul style="padding-left:128px;white-space:nowrap;min-height:96px;"><li>The carousel vertical/y-axis radius in px.</li> </ul>';?>
			<th><span style="color:red;"><small>*</small></span>Carousel-Y-Radius :  <i class="far fa-question-circle" onmouseover="<?php hecho('tt_in(' . jecho($product_tooltip, 1) . ');') ?>" onmouseout="tt_out();"></i></th>
			<td><input class="wide update_crausel" type="number" name="item[y_radious]"  value="<?= $item['y_radious']?>" style="width:98%;" max="100" min="0" /></td>
		</tr>
		<tr>
			<?php $product_tooltip='<img src="images/carousel-x-rotation-icon.png" style="max-width:96px;max-height:96px;position:absolute;left:8px;" /><br/><ul style="padding-left:128px;white-space:nowrap;min-height:96px;"><li>The carousel rotation on the x-axis.</li> </ul>';?>
			<th><span style="color:red;"><small>*</small></span>Carousel-X-Rotation :  <i class="far fa-question-circle" onmouseover="<?php hecho('tt_in(' . jecho($product_tooltip, 1) . ');') ?>" onmouseout="tt_out();"></i></th>
			<td><input class="wide update_crausel" type="number" name="item[x_rotation]"  value="<?= $item['x_rotation']?>" style="width:98%" min="1" /></td>
		</tr>
		<tr>
			<?php $product_tooltip='<img src="images/thumbs-y-offset-icon.png" style="max-width:96px;max-height:96px;position:absolute;left:8px;" /><br/><ul style="padding-left:128px;white-space:nowrap;min-height:96px;"><li> the carousel vertical/y-axis offest in px, this will move the thumbnails up if negative or down if positive vertically on the y-axis</li> </ul>';?>
			<th><span style="color:red;"><small>*</small></span>Thumbs-Y-Offset :  <i class="far fa-question-circle" onmouseover="<?php hecho('tt_in(' . jecho($product_tooltip, 1) . ');') ?>" onmouseout="tt_out();"></i></th>
			<td><input class="wide update_crausel" type="number" name="item[y_offset]"  value="<?= $item['y_offset']?>" style="width:98%;"   /></td>
		</tr>
		<tr>
			<?php $product_tooltip='<img src="images/thumb_thumbs-alpha--icon.png" style="max-width:96px;max-height:96px;position:absolute;left:8px;" /><br/><ul style="padding-left:128px;white-space:nowrap;min-height:96px;"><li>The minimum alpha opacity of the furthest away thumbnails of the carousel.</li> </ul>';?>
			<th><span style="color:red;"><small>*</small></span>Thumbs-Min-Alpha :  <i class="far fa-question-circle" onmouseover="<?php hecho('tt_in(' . jecho($product_tooltip, 1) . ');') ?>" onmouseout="tt_out();"></i></th>
			<td><input class="wide update_crausel" type="text" name="item[mil_alpha]"  value="<?= $item['mil_alpha']?>" style="width:98%;"  /></td>
		</tr>
		<tr>
			<th><span style="color:red;"><small>*</small></span>Reflection Distance:</th>
			<td><input class="wide update_crausel" type="number" name="item[reflation_distance]"  value="<?= $item['reflation_distance']?>" style="width:98%;" min="1" /></td>
		</tr>
		<tr>
			<th><span style="color:red;"><small>*</small></span>Image Width :</th>
			<td><input class="wide update_crausel" type="number" name="item[image_width]"  value="<?= $item['image_width']?>" style="width:98%;" max="800" min="200" /></td>
		</tr>
		<tr>
			<th><span style="color:red;"><small>*</small></span>Image Height :</th>
			<td><input class="wide update_crausel" type="number" name="item[image_height]"  value="<?= $item['image_height']?>" style="width:98%;" max="800" min="200" /></td>
		</tr>
		<tr>
			<th>Show Reflection :</th>
			<td><select class="wide update_crausel" name="item[show_reflection]">
	                <?= formatHTMLOptions(['y'=>'Yes','n'=>'No'], $item['show_reflection']) ?>
	        </td>
		</tr>
		<tr>
			<th>Show Center Image :</th>
			<td><select class="wide update_crausel" name="item[show_center_image]">
	                <?= formatHTMLOptions(['y'=>'Yes','n'=>'No'], $item['show_center_image']) ?>
	        </td>
		</tr>
</table>