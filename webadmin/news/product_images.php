<?php
/**
 * @var mixed[][] $product_screenshots
 */
?>
<!-- <?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->

<?=formatHTMLTitle('<a href="#" onclick="toggleSection(\'sectionSS\');return false">News Images</a>', '', false)?>

<script type="text/javascript">
//<![CDATA[
function screenshots_rebuild() {
	$("#screenshots>tr").removeClass("alt").odd().addClass("alt");
}
function addScreenShot(data){
	console.log(data);
	data.forEach(addImages);
}
function screenshots_moveup(e) {
	const $row = $(e).closest('tr');
	var currentrow=$row.attr('ssid');
	var previousrow = $row.next().attr('ssid');
	var rankcurrent=$('#rank_'+currentrow).val();
	var rankprevious=$('#rank_'+previousrow).val();
	$('#rank_'+currentrow).val(rankprevious);
	$('#rank_'+previousrow).val(rankcurrent);
	$row.next().after($row);
	screenshots_rebuild();
}
function screenshots_movedown(e) {
	
	const $row = $(e).closest('tr');
	var currentrow=$row.attr('ssid');
	var previousrow = $row.prev().attr('ssid');
	var rankcurrent=$('#rank_'+currentrow).val();
	var rankprevious=$('#rank_'+previousrow).val();
	$('#rank_'+currentrow).val(rankprevious);
	$('#rank_'+previousrow).val(rankcurrent);
	$row.prev().before($row);
	screenshots_rebuild();
}
function screenshots_delete(rowid,dataid) {
	if (confirm('Are you sure you want to delete the screenshot??')) {
		var theRow = getElement("product_image_rowa[" + rowid + "]");
        if (theRow) theRow.parentNode.deleteRow(theRow.sectionRowIndex);
        if(dataid){
        setValue("item_images_deleted_val", getValue("item_images_deleted_val") + "," + dataid);
        }
	}
}
function screenshots_changeShotStatus(product_id, shot_id, action) {
	if (shot_id != 0) {
		window.location = '.?action=' + action + '&product_id=' + product_id + '&ss_id=' + shot_id;
	}
}
$(document).ready(function () {
$(document).on('click', '.option-val-checkbox:radio', function() {
	if ( $(this).is(':checked') ) {
	    var uncheck=document.getElementsByClassName('option-val-checkbox');
		 for(var i=0;i<uncheck.length;i++)
		 {
			  if(uncheck[i].type=='radio')
			  {
			   uncheck[i].checked=false;
			  }
			}
			$(this).prop("checked", true);
		}
		
	});
});
var image_count="<?=count($news_images); ?>";
function addImages(data)
{

html='<tr ssid="'+image_count+'" id="product_image_rowa['+image_count+']">\
	<td><input type="radio" class="option-val-checkbox" value="y" name="product_image['+image_count+'][default_image]"></td>\
	<td><input type="checkbox" class="option-val-online" value="y" name="product_image['+image_count+'][online]"></td>\
	<td>\
		<input type="hidden" name="product_image['+image_count+'][name]" value="'+data.image+'">\
		<input type="hidden" name="product_image['+image_count+'][size]" value="'+data.size+'">\
		<input type="hidden" name="product_image['+image_count+'][filetype]" value="'+data.filetype+'">\
		<input type="hidden" name="product_image['+image_count+'][staff]" value="'+data.staff+'">\
		<input type="hidden" name="product_image['+image_count+'][caption]" value="'+data.caption+'">\
		<input type="hidden" name="product_image['+image_count+'][rank]" value="'+image_count+'" id="rank_'+image_count+'">\
		<img src="down_arrow.gif" onclick="screenshots_moveup(this)" />\
		<img src="up_arrow.gif" onclick="screenshots_movedown(this)" />\
	</td>\
	<td><a href="addImages.php?name='+data.image+'&amp;action=view" target="_blank"><img src="addImages.php?name='+data.image+'&amp;action=view&amp;thumb=true" width="100" height="50" /></a></td>\
	<td style="text-align:left"><input type="text"  name="product_image['+image_count+'][caption]" value="'+data.caption+'" style="width:90%" /></td>\
	<td><?= date('d/m/y') ?></td>\
	<td>'+data.staff+'</td>\
	<td>'+data.filetype+'</td>\
	<td>'+data.size+'</td>\
	<td><input type="button" value="Delete" onclick="screenshots_delete('+image_count+',0)" /></td>\
</tr>'
	
$('#screenshots').append(html);
image_count++;
	//screenshots_rebuild();
}
function rebuild()
{
	var table=getElement("screenshots");
	var nodes = table.getElementsByTagName("tr");

	if(nodes.length > 1) {
		for(i=0;i<nodes.length;i++) {
			var node = nodes[i];
			var imgs = node.getElementsByTagName("img");

			node.style.backgroundColor = (i%2==1) ? "#EEEEE0" : "#FFFFFF";

			imgs[0].style.visibility = ((i==0) ? 'visible' : ((i==(nodes.length-1)) ? 'hidden' : 'visible'));
			imgs[1].style.visibility = ((i==0) ? 'hidden' : ((i==(nodes.length-1)) ? 'visible' : 'visible'));
		}
	}
	else {
		node = nodes[0];
		var imgs = node.getElementsByTagName("img");
		node.style.backgroundColor = "#FFFFFF";
		imgs[0].style.visibility='hidden';
		imgs[1].style.visibility='hidden';
	}

	updateRankOrder();
	//alert(nodes.length);
}

function updateRankOrder()
{
	var table=getElement("screenshots");
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

function moveUp(node)
{
	var newNode = node.cloneNode("true");
	newNode.setAttribute("position", (newNode.getAttribute("position") - 1));

	var prevRow = node_before(node);
	var prevRowNode = prevRow.cloneNode("true");
	prevRowNode.setAttribute("position", (parseInt(prevRowNode.getAttribute("position")) + 1.0));
	var tableNode = node.parentNode;

	tableNode.replaceChild( newNode, prevRow);
	tableNode.replaceChild(prevRowNode, node);

	rebuild();
}

function moveDown(node)
{
	var newNode = node.cloneNode("true");
	newNode.setAttribute("position", (parseInt(newNode.getAttribute("position")) + 1.0));

	var nextRow = node_after(node);

	var nextRowNode = nextRow.cloneNode("true");
	nextRowNode.setAttribute("position", (nextRowNode.getAttribute("position") - 1));
	var tableNode = node.parentNode;

	tableNode.replaceChild( newNode, nextRow);
	tableNode.replaceChild(nextRowNode, node);
	rebuild();
}
//]]>
</script>

<div id="sectionSS">
<?php
	$tooltip = "The  choice will show as the news story thumbnail (front page + news summary page) as also as the background image in the news story";
?>

<table class="table_grid" style="width:95%;">
	<thead>
		<tr>
			<th style="width:5%;"><i class="far fa-question-circle" style="margin-left: 10px;font-size:15px;" onmouseover="<?=htmlspecialchars("tt_in(" . json_encode($tooltip) . ")")?>" onmouseout="tt_out()"></i></th>
			<th style="width:10%">Active</th>
			<th style="width:10%">Rank</th>
			<th style="width:5%">Image</th>
			<th style="width:auto">Description / Image ALT Tag</th>
			<th style="width:5%">Attach Date</th>
			<th style="width:5%">Attached By</th>
			<th style="width:5%">File Type</th>
			<th style="width:10%">File Size</th>
			<th style="width:5%">Edit/Delete</th></tr>
		</tr>
	</thead>
	<tbody id="screenshots">
		
<?php
$count = 0;
$newimagedir=$_SERVER['DOCUMENT_ROOT'] . '/webadmin/uploads/news/extra/';
foreach ($news_images as $shot) {
	$value = (('y' == $shot['active']) ? 'De-' : '') . 'Activate';
	$link  = (('y' == $shot['active']) ? 'de' : '') . 'activate_ss';
	$imagesize = 0;
	if (is_file($test = $newimagedir . $shot['name'])) {
		 $imagesize = formatFileSize(filesize($test)); 
	}

?>
	<tr <?=($count % 2 == 1) ? 'class="alt"' : ''?> id="product_image_rowa[<?=$count ?>]" ssid="<?=$count ?>">
		<!-- <td><input type="checkbox" value="y" class="option-val-checkbox" name="product_image[<?= $count ?>][default_image]" <?= ($shot['default_image']=='y')?'checked':''?>></td> -->
		<td><input type="radio" value="y" class="option-val-checkbox" name="product_image[<?= $count ?>][default_image]" <?= ($shot['default_image']=='y')?'checked':''?> onchange="toggleThumbnailRow(this, <?=$count?>, '<?=$shot['name']?>', '<?= $imagesize ?>')"></td>
		<td><input type="checkbox" value="y" class="option-val-online" name="product_image[<?= $count ?>][online]" <?= ($shot['online']=='y')?'checked':''?>></td>
		<td>
		<input type="hidden" name="product_image[<?= $count ?>][id]" value="<?=$shot['id'] ?>">
		<input type="hidden" name="product_image[<?= $count ?>][name]" value="<?=$shot['name'] ?>">
		<input type="hidden" name="product_image[<?= $count ?>][size]" value="<?= $imagesize ?>">
		<input type="hidden" name="product_image[<?= $count ?>][filetype]" value="<?=$shot['filetype'] ?>">
		<input type="hidden" name="product_image[<?= $count ?>][staff]" value="<?=$shot['staff'] ?>">
		<input type="hidden" name="product_image[<?= $count ?>][rank]" value="<?=$shot['rank'] ?>" id="rank_<?= $count ?>">
		<img src="down_arrow.gif" onclick="screenshots_moveup(this)" />
		<img src="up_arrow.gif" onclick="screenshots_movedown(this)" />
	</td>
	<td><a href="addImages.php?name=<?=$shot['name'] ?>&action=view" target="_blank"><img src="addImages.php?name=<?=$shot['name'] ?>&action=view&thumb=true" width="100" height="50" alt="<?=$shot['caption'] ?>" title="<?=$shot['caption'] ?>"/></a></td>
	<td style="text-align:left">
		<input type="text" name="product_image[<?= $count ?>][caption]" value="<?=$shot['caption'] ?>" style="width:90%">
	</td>
	<td><?= date('d/m/y',strtotime($shot['created_at'])) ?></td>
	<td><?=$shot['staff'] ?></td>
	<td><?=$shot['filetype'] ?></td>
	<td><?php
		if (is_file($test = $newimagedir . $shot['name'])) {
		?>
		        <?php hecho(formatFileSize(filesize($test))) ?>
		<?php
		    }
		?></td>
	<td><input type="button" value="Delete" onclick="screenshots_delete(<?= $count ?>,<?=$shot['id']?>)" /></td>
	</tr>
<?php
	$count++;
}
?>
	</tbody>

	<tfoot>
		<input type="hidden" name="item_images_deleted_val" value="" />
		<tr id="foot"<?=($count % 2) == 1 ? ' class="alt"' : ''?>>
			<td colspan=8 style="text-align:center">
				<input type="button" value="Add Images" onclick="window.open('addImages.php?id=<?=$product_id?>&action=add','','width=700,height=500')" />
			</td>
		</tr>
	</tfoot>
</table>
<div id="thumbnail_row" style="display:none;">
    <table class="table_grid" style="width:95%;">
        <tr>
            <td style="width:15%;padding:35px;">News Thumbnail</td>
            <td style="width:7%;"><a href="#" id="thumbnail_link" target="_blank"><img id="thumbnail_image" src="" width="100" height="50" /></a></td>
            <td><input type="text" style="width:90%" id="selected_thumbnail"></td>
            <td id="thumbnail_size" style="width:7%;"></td>
        </tr>
    </table>
</div>
</div>

<script type="text/javascript">
	function toggleThumbnailRow(checkbox, count, name, size) {
        var row = document.getElementById('thumbnail_row');
        var link = document.getElementById('thumbnail_link');
        var image = document.getElementById('thumbnail_image');
        var sizeElement = document.getElementById('thumbnail_size');
		var title = getValue('data1[news_title]');
		$('#selected_thumbnail').val(title);
        if (checkbox.checked) {
            link.href = 'addImages.php?name=' + name + '&action=view';
            image.src = 'addImages.php?name=' + name + '&action=view&thumb=true';
            sizeElement.textContent = size;
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    }

    // On page load, check if any checkbox is already selected
    $(function() {
        var checkboxes = document.querySelectorAll('.option-val-checkbox');
        checkboxes.forEach(function(checkbox, index) {
            if (checkbox.checked) {
                var name = checkbox.closest('tr').querySelector('input[name="product_image[' + index + '][name]"]').value;
                var size = checkbox.closest('tr').querySelector('input[name="product_image[' + index + '][size]"]').value;
                console.log(size);
                toggleThumbnailRow(checkbox, index, name, size);
            }
        });
    });
</script>

<br />

<!-- /<?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->
