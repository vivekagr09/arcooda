
<?php
include_once __DIR__ . '/common.inc.php';

global $DB;

include_once __DIR__ . '/header.inc.php';
$crousel_images=[];
$id = intval($_GET['id']);
if ($id > 0) {
	
	$item = $DB->query_first("SELECT * FROM `product_page_carousel` WHERE `id` = " . $id);
	$query = $DB->query(sprintf(
		    "SELECT *, UNIX_TIMESTAMP(`created_at`) AS `created_time`, UNIX_TIMESTAMP(`updated_at`) AS `updated_time` FROM %s  Where `%s` = %d  %s",
		    '`product_page_carousel_images`',
		    'crousel_id',
		     $id,
		    'ORDER BY `rank` ASC'
        ));
	 while ($row = $DB->fetch_assoc($query)) {
	        if (is_null($primary_key)) {
	            $crousel_images[] = $row;
	        } 
	    }
	    $DB->free_result($query);
}
?>

<table class="table_fields">
		<tr>
			<th>Topology :</th>
			<td>
				<select class="wide update_crausel" name="item[topology]">
	                <?= formatHTMLOptions(['normal'=>'Normal','ring'=>'Ring','star'=>'Star'], $item['topology']) ?>
	        </td>
	        <th>Carousel-X-Radius :</th>
			<td><input class="wide update_crausel" type="number" name="item[x_radious]"  value="<?= $item['x_radious']?>" style="width:98%;" max="1600" min="500" /></td>
		</tr>
		<tr>
			<th>Carousel-Y-Radius :</th>
			<td><input class="wide update_crausel" type="number" name="item[y_radious]"  value="<?= $item['y_radious']?>" style="width:98%;" max="1600" min="500" /></td>
			<th>Carousel-X-Rotation :</th>
			<td><input class="wide update_crausel" type="number" name="item[x_rotation]"  value="<?= $item['x_rotation']?>" style="width:98%;"  min="1" /></td>
		</tr>
		<tr>
			<th> Image Width :</th>
			<td><input class="wide update_crausel" type="number" name="item[image_width]"  value="<?= $item['image_width']?>" style="width:98%;" max="800" min="200" /></td>
			<th> Image Height :</th>
			<td><input class="wide update_crausel" type="number" name="item[image_height]"  value="<?= $item['image_height']?>" style="width:98%;" max="800" min="200" /></td>
		</tr>
		<tr>
			<th>Thumbs-Y-Offset :</th>
			<td><input class="wide update_crausel" type="number" name="item[y_offset]"  value="<?= $item['y_offset']?>" style="width:98%;"   /></td>
				<th>Thumbs-Min-Alpha :</th>
			<td><input class="wide update_crausel" type="text" name="item[mil_alpha]"  value="<?= $item['mil_alpha']?>" style="width:98%;"  /></td>
		</tr>
		<tr>
			<th>Reflection Distance:</th>
			<td><input class="wide update_crausel" type="number" name="item[reflation_distance]"  value="<?= $item['reflation_distance']?>" style="width:98%;"  min="1" /></td>
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
<?php include 'carousel_view.php'; ?>