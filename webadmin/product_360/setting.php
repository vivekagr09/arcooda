<?php
include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/header.inc.php';

$item = view360GenericFromDatabase(
	1,
	"`360_view_setting`"
);
?>
<h1 style="text-align:center;">Edit Setting  - <?= htmlspecialchars(date(_('d/m/Y'))) ?></h1>
<form action=".?action=update_setting" method="post">
	<?= formatHTMLTitle('Setting') ?>
	<table class="table_fields">
		<tr>
			<th style="width:25%;"><span style="color:red;"><small>*</small></span>Presest Name :</th>
			<td style="width:25%;"><input class="wide" type="text" name="item[present_name]"  value="<?= $item['present_name']?>"  required/></td>
			<th style="width:25%;">Skin Type :</th>
			<td style="width:25%;">
				<select name="item[skin_type]"  class="wide" >
					<?=formatHTMLOptions(['skin1'=>'skin1','skin2'=>'skin2'], $item['skin_type'])?>
				</select>
			</td>
		</tr>
		<tr>
			<th style="width:25%;">Display Type :</th>
			<td style="width:25%;">
				<select name="item[display_type]"  class="wide" >
					<?=formatHTMLOptions(['responsive'=>'Responsive','fullscreen'=>'Full Screen'], $item['display_type'])?>
				</select>
			</td>
			<th style="width:25%;">Use Vector Icons:</th>
			<td style="width:25%;">
				<select name="item[vector_icons]"  class="wide" >
					<?=formatHTMLOptions(['yes'=>'Yes','no'=>'No'], $item['vector_icons'])?>
				</select>
			</td>
		</tr>
		</tr>
		<tr>
			<th style="width:25%;">Lazy Loadding:</th>
			<td style="width:25%;">
				<select name="item[lazy_loading]"  class="wide" >
					<?=formatHTMLOptions(['yes'=>'Yes','no'=>'No'], $item['lazy_loading'])?>
				</select>
			</td>
			<th style="width:25%;">Start Draging Mode:</th>
			<td style="width:25%;">
				<select name="item[dragging_mode]"  class="wide" >
					<?=formatHTMLOptions(['rotate'=>'Rotate','pan'=>'Pan'], $item['dragging_mode'])?>
				</select>
			</td>
		</tr>
		<tr>
			<th style="width:25%;">Drag Verticals:</th>
			<td style="width:25%;">
				<select name="item[drag_vertical]"  class="wide" >
					<?=formatHTMLOptions(['yes'=>'Yes','no'=>'No'], $item['drag_vertical'])?>
				</select>
			</td>
			<th style="width:25%;">Use Entire Screen:</th>
			<td style="width:25%;">
				<select name="item[entire_screen]"  class="wide" >
					<?=formatHTMLOptions(['yes'=>'Yes','no'=>'No'], $item['entire_screen'])?>
				</select>
			</td>
		</tr>
		<tr>
			<th style="width:25%;">Add Drag And Spin Support:</th>
			<td style="width:25%;">
				<select name="item[drag_spin]"  class="wide" >
					<?=formatHTMLOptions(['yes'=>'Yes','no'=>'No'], $item['drag_spin'])?>
				</select>
			</td>
			<th style="width:25%;">Stop Rotations At Horizontal Rotation End:</th>
			<td style="width:25%;">
				<select name="item[rotations_horizontal]"  class="wide" >
					<?=formatHTMLOptions(['yes'=>'Yes','no'=>'No'], $item['rotations_horizontal'])?>
				</select>
			</td>
		</tr>
		<tr>
			<th style="width:25%;">Stop Rotations At Vertical Rotation End:</th>
			<td style="width:25%;">
				<select name="item[rotations_vertical]"  class="wide" >
					<?=formatHTMLOptions(['yes'=>'Yes','no'=>'No'], $item['rotations_vertical'])?>
				</select>
			</td>
			<th style="width:25%;">Inverse Drag Rotation:</th>
			<td style="width:25%;">
				<select name="item[inverse_drag]"  class="wide" >
					<?=formatHTMLOptions(['yes'=>'Yes','no'=>'No'], $item['inverse_drag'])?>
				</select>
			</td>
		</tr>
		<tr>
			<th style="width:25%;">Disable Mousewheels Zooms:</th>
			<td style="width:25%;">
				<select name="item[mousewheel_zoom]"  class="wide" >
					<?=formatHTMLOptions(['yes'=>'Yes','no'=>'No'], $item['mousewheel_zoom'])?>
				</select>
			</td>
			<th style="width:25%;">Auto Scale:</th>
			<td style="width:25%;">
				<select name="item[auto_scale]"  class="wide" >
					<?=formatHTMLOptions(['yes'=>'Yes','no'=>'No'], $item['auto_scale'])?>
				</select>
			</td>
		</tr>
		<tr>
			<th style="width:25%;">Viewer Max Width:</th>
			<td style="width:25%;">
				<input class="wide" type="number" name="item[max_width]"  value="<?= $item['max_width']?>"  required min="0" /></td>
			</td>
			<th style="width:25%;">Viewer Max Height:</th>
			<td style="width:25%;">
				<input class="wide" type="number" name="item[max_height]"  value="<?= $item['max_height']?>"  required min="0" /></td>
			</td>
		</tr>
		<tr>
			<th style="width:25%;">Viewer Min Height:</th>
			<td style="width:25%;">
				<input class="wide" type="number" name="item[min_height]"  value="<?= $item['min_height']?>"  required min="0" /></td>
			</td>
			<th style="width:25%;">Image Offset X:</th>
			<td style="width:25%;">
				<input class="wide" type="number" name="item[image_offset_x]"  value="<?= $item['image_offset_x']?>"  required min="0" /></td>
			</td>
		</tr>
		<tr>
			<th style="width:25%;">Image Offset Y:</th>
			<td style="width:25%;">
				<input class="wide" type="number" name="item[image_offset_y]"  value="<?= $item['image_offset_y']?>"  required min="0" /></td>
			</td>
			<th style="width:25%;">Start At Image:</th>
			<td style="width:25%;">
				<input class="wide" type="number" name="item[start_image]"  value="<?= $item['start_image']?>"  required min="0" /></td>
			</td>
		</tr>
		<tr>
			<th style="width:25%;">Start At Set:</th>
			<td style="width:25%;">
				<input class="wide" type="number" name="item[start_set]"  value="<?= $item['start_set']?>"  required min="0" /></td>
			</td>
			<th style="width:25%;">Zoom Factor:</th>
			<td style="width:25%;">
				<input class="wide" type="number" name="item[zoom_fection]"  value="<?= $item['zoom_fection']?>"  required min="0" /></td>
			</td>
		</tr>
		<tr>
			<th style="width:25%;">Drag Rotaion Speed:</th>
			<td style="width:25%;">
				<input class="wide" type="text" name="item[rotation_speed]"  value="<?= $item['rotation_speed']?>"  required min="0" /></td>
			</td>
			<th style="width:25%;">Drag And Spin Sansitivity:</th>
			<td style="width:25%;">
				<input class="wide" type="number" name="item[spin_senstivity]"  value="<?= $item['spin_senstivity']?>"  required min="0" /></td>
			</td>
		</tr>
	</table>
	<div style="text-align:center;">
        <input type="submit" value="Update" /><br />
        <i><span style="color:red"><i><small>*</small></span> - Required Field</i>
    </div>
</form>
<?php
include_once __DIR__ . '/footer.inc.php';

?>