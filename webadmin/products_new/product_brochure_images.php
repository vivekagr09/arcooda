<?php
/**
 * @var mixed[][] $product_brouchures
 */
?>
<!-- <?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->

<?=formatHTMLTitle('<a href="#" onclick="toggleSection(\'brochure_image\');return false">Brochure Images </a>', '', false)?>

<script type="text/javascript">
//<![CDATA[
function brouchures_rebuild() {
	$("#brouchures>tr").removeClass("alt").odd().addClass("alt");
}
function addbrouchure(data){
	console.log(data);
	data.forEach(addBrochureImages);
}
function brouchures_moveup(e) {
	const $row = $(e).closest('tr');
	var currentrow=$row.attr('ssid');
	var previousrow = $row.next().attr('ssid');
	var rankcurrent=$('#rank_'+currentrow).val();
	var rankprevious=$('#rank_'+previousrow).val();
	$('#rank_'+currentrow).val(rankprevious);
	$('#rank_'+previousrow).val(rankcurrent);
	$row.next().after($row);
	brouchures_rebuild();
}
function brouchures_movedown(e) {
	
	const $row = $(e).closest('tr');
	var currentrow=$row.attr('ssid');
	var previousrow = $row.prev().attr('ssid');
	var rankcurrent=$('#rank_'+currentrow).val();
	var rankprevious=$('#rank_'+previousrow).val();
	$('#rank_'+currentrow).val(rankprevious);
	$('#rank_'+previousrow).val(rankcurrent);
	$row.prev().before($row);
	brouchures_rebuild();
}
function brouchures_delete(rowid,dataid) {
	if (confirm('Are you sure you want to delete the brochure??')) {
		var theRow = getElement("brochure_image_rowa[" + rowid + "]");
        if (theRow) theRow.parentNode.deleteRow(theRow.sectionRowIndex);
        if(dataid){
        setValue("brochure_images_deleted_val", getValue("brochure_images_deleted_val") + "," + dataid);
        }
	}
}
function brouchures_changeShotStatus(product_id, shot_id, action) {
	if (shot_id != 0) {
		window.location = '.?action=' + action + '&product_id=' + product_id + '&ss_id=' + shot_id;
	}
}
var image_count="<?=count($brochure_images); ?>";
function addBrochureImages(data)
{

html='<tr ssid="'+image_count+'" id="brochure_image_rowa['+image_count+']">\
	<td>\
		<input type="hidden" name="brochure_image['+image_count+'][name]" value="'+data.image+'">\
		<input type="hidden" name="brochure_image['+image_count+'][size]" value="'+data.size+'">\
		<input type="hidden" name="brochure_image['+image_count+'][filetype]" value="'+data.filetype+'">\
		<input type="hidden" name="brochure_image['+image_count+'][staff]" value="'+data.staff+'">\
		<input type="hidden" name="brochure_image['+image_count+'][caption]" value="'+data.caption+'">\
		<input type="hidden" name="brochure_image['+image_count+'][rank]" value="'+image_count+'" id="rank_'+image_count+'">\
		<img src="down_arrow.gif" onclick="brouchures_moveup(this)" />\
		<img src="up_arrow.gif" onclick="brouchures_movedown(this)" />\
	</td>\
	<td><a href="addbrochureImages.php?name='+data.image+'&amp;action=view" target="_blank"><img src="addbrochureImages.php?name='+data.image+'&amp;action=view&amp;thumb=true" width="100" height="50" /></a></td>\
	<td style="text-align:left"> <textarea rows="5" name="brochure_image['+image_count+'][caption]" value="'+data.caption+'" style="width:90%">'+data.caption+'</textarea></td>\
	<td><?= date('d/m/y') ?></td>\
	<td>'+data.staff+'</td>\
	<td>'+data.filetype+'</td>\
	<td>'+data.size+'</td>\
	<td><input type="button" value="Delete" onclick="brouchures_delete('+image_count+',0)" /></td>\
</tr>'
	
$('#brouchures').append(html);
console.log('dfgdfg');
image_count++;
	//brouchures_rebuild();
}


function updateBrochureRankOrder()
{
	var table=getElement("brouchures");
	var nodes = table.getElementsByTagName("tr");
	var ranking = new Array(1);

	var count = 0;
	for(i=0;i<nodes.length;i++) {
		var node = nodes[i];
		if(node.getAttribute("ssid")){
			ranking[count] = node.getAttribute("ssid");
			count++;
		}
	}
	setValue("ss_order",ranking.join(","));
	//alert(getValue("ss_order"));
}
//]]>
</script>

<div id="sectionSS">


<table class="table_grid">
	<thead>
		<tr>
			<th style="width:10%">Rank</th>
			<th style="width:5%">Image</th>
			<th style="width:auto">Description</th>
			<th style="width:5%">Attach Date</th>
			<th style="width:5%">Attached By</th>
			<th style="width:5%">File Type</th>
			<th style="width:10%">File Size</th>
			<th style="width:5%">Edit/Delete</th></tr>
		</tr>
	</thead>
	<tbody id="brouchures">
		
<?php
$count = 0;
foreach ($brochure_images as $shot) {
	$slug  = str_replace(' ', '-', trim($shot['caption'])).'-'.$shot['id'];
	$url   = 'brochure-images/'.$slug;
	$value = (('y' == $shot['active']) ? 'De-' : '') . 'Activate';
	$link  = (('y' == $shot['active']) ? 'de' : '') . 'activate_ss';
?>
	<tr <?=($count % 2 == 1) ? 'class="alt"' : ''?> id="brochure_image_rowa[<?=$count ?>]" ssid="<?=$count ?>">
		<td>
		<input type="hidden" name="brochure_image[<?= $count ?>][id]" value="<?=$shot['id'] ?>">
		<input type="hidden" name="brochure_image[<?= $count ?>][name]" value="<?=$shot['name'] ?>">
		<input type="hidden" name="brochure_image[<?= $count ?>][size]" value="<?=$shot['size'] ?>">
		<input type="hidden" name="brochure_image[<?= $count ?>][filetype]" value="<?=$shot['filetype'] ?>">
		<input type="hidden" name="brochure_image[<?= $count ?>][staff]" value="<?=$shot['staff'] ?>">
		<input type="hidden" name="brochure_image[<?= $count ?>][rank]" value="<?=$shot['rank'] ?>" id="rank_<?= $count ?>">
		<img src="down_arrow.gif" onclick="brouchures_moveup(this)" />
		<img src="up_arrow.gif" onclick="brouchures_movedown(this)" />
	</td>
	<td>
		<a href="/brochure-image/<?php hecho($slug)?>.jpg" target="_blank">
		<!-- <a href="addbrochureImages.php?name=<?=$shot['name'] ?>&action=view" target="_blank"> -->
		<img src="addbrochureImages.php?name=<?=$shot['name'] ?>&action=view&thumb=true" width="100" height="50" /></a></td>
	<td style="text-align:left">
		<textarea name="brochure_image[<?= $count ?>][caption]" value="<?=trim($shot['caption']) ?>" style="width:90%" rows="5"><?=$shot['caption'] ?> </textarea>
	</td>
	<td><?= ($shot['created_at'] && $shot['created_at'] != '0000-00-00 00:00:00') ? date('d/m/y', strtotime($shot['created_at'])) : '' ?></td>
	<td><?=$shot['staff'] ?></td>
	<td><?=$shot['filetype'] ?></td>
	<td><?=$shot['size'] ?></td>
	<td><input type="button" value="Delete" onclick="brouchures_delete(<?= $count ?>,<?=$shot['id']?>)" /></td>
	</tr>
<?php
	$count++;
}
?>
	</tbody>

	<tfoot>
		<input type="hidden" name="brochure_images_deleted_val" value="" />
		<tr id="foot"<?=($count % 2) == 1 ? ' class="alt"' : ''?>>
			<td colspan=8 style="text-align:center">
				<input type="button" value="Add Images" onclick="window.open('addbrochureImages.php?id=<?=$product_id?>&action=add','','width=700,height=500')" />
			</td>
		</tr>
	</tfoot>
</table>
</div>

<br />

<!-- /<?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->
