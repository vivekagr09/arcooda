<!-- images.tpl -->

<?php
function item_images_row($count = '[ROW_COUNT]', $row_data = array(), $alt = false)
{
    global $item;
    $post_var = 'item_images[' . $count . ']';
    $hidden   = array(
        $post_var . '[id]'         => $row_data['id'],
        $post_var . '[page_id]'    => $row_data['page_id'],
        $post_var . '[chapter_id]' => $row_data['chapter_id'],
    );

    ob_start();
?>
<tr<?=($alt) ? ' class="alt"' : ''?> id="item_images_rowa[<?=$count?>]">
<?php
    if ('' != $row_data['image_url']) {
        $src = hecho($row_data['image_url'], 1);
?>
	<td><input type="checkbox" name="select_image[]" value="<?=$row_data['id']?>" /></td>
	<td style="text-align:center;"><a href="/page-image/<?php hecho($row_data['id'])?>" target="_blank"><?=str_replace("\n", "", formatHTMLInputs($hidden))?><img width="96" src="<?php hecho($row_data['thumb_url'])?>" /></a></td>
	<td style="text-align:center;white-space:nowrap;"><?php if ("semihtml" == $item["content_type"]) {?>&lt;img src=&quot;<?php hecho($row_data['image_url'])?>&quot; /><br /><br /><?php }?><input type="text" name="<?php hecho($post_var . '[file_orig]')?>" value="<?php hecho($row_data['file_orig'])?>" disabled="disabled" /></td>
	<td style="text-align:center;"><?php if ("semihtml" == $item["content_type"]) {?><input type="button" value="Insert Into Content" onclick="<?=htmlspecialchars('generic_insert(getPageChapterContent(), "img", ' . json_encode(' src="' . $src . '"') . ')')?>" /> <input type="button" value="Insert Into Content (CA)" onclick="<?=htmlspecialchars('generic_insert(getPageChapterContent(), "center");generic_insert(getPageChapterContent(), "img", ' . json_encode(' src="' . $src . '"') . ')')?>" /> <input type="button" value="Insert Into Content (RA)" onclick="<?=htmlspecialchars('generic_insert(getPageChapterContent(), "div", ' . json_encode(' align="right"') . ');generic_insert(getPageChapterContent(), "img", ' . json_encode(' src="' . $src . '"') . ')')?>" /> <?php }?><input type="button" value="Delete" onclick="pageImagesDelete(<?=$count?>);" /></td>
<?php
    } else {
?>
	<td>&nbsp;</td>
	<td>New Image</td>
	<td>
		<input type="hidden" name="MAX_FILE_SIZE" value="<?=htmlspecialchars(convertSize(ini_get("upload_max_filesize")))?>" />
		<input type="file" name="<?php hecho($post_var . '[image_file]')?>" />
	</td>
	<td style="text-align:center;"><input type="button" value="Delete" onclick="pageImagesDelete(<?=$count?>);" /></td>
<?php
    }
?>
</tr>
<?php
    return ob_get_clean();
}

if (is_array($item_images)) {
    $cache = $urls = $links = array();
    foreach ($item_images as $img) {
        $tmp = pageImageFromDatabase($img);
        //print_array($tmp);
        $cache[$img] = $tmp;
        $links[$img] = $tmp['image_url'];
        $urls[$img]  = $tmp['thumb_url'];
    }
?>

<script type="text/javascript">
//<![CDATA[
var image_urls = <?=json_encode($urls)?>;
var image_links = <?=json_encode($links)?>;
var item_images_count = 100;

function pageImagesAdd()
{
	var count = String(item_images_count);
	var target_table = getElement("item_images_table");
	if (target_table != null)
	{
		var row_string = <?=json_encode(item_images_row('[ROW_COUNT]',array("id" => "[ID]", 'page_id' => $id, 'chapter_id' => 0)))?>;
	    	row_string = row_string.replace(/\[ROW_COUNT\]/g, count);
	    	$("#item_images_table").append(row_string);	
			item_images_count++;
	}
}
function pageImagesDelete(row)
{
	if (confirm("Are you sure you want to remove this image?"))
	{
		var theRow = getElement("item_images_rowa[" + row + "]");
		if (theRow) theRow.parentNode.deleteRow(theRow.sectionRowIndex);
	}
}
function insertImages(align)
{
	var elems = getElements('select_image[]');
	var ids = [];
	var iLinks = [];
	for(key in elems)
	{
		var elem = elems[key];
		if(elem.checked)
		{
			ids[ids.length] = image_urls[elem.value];
			iLinks[iLinks.length] = image_links[elem.value];
		}
	}
	if (ids.length>0)
	{
		var str = ["<table cellpadding=\"5\"" + ((align != null && align != "") ? " align=\"" + align + "\"" : "") + "><tr style=\"text-align:center;\">"];
		for (i = 0;i < ids.length;i++)
		{
			// put 5 img / row.
			if (i % 5 == 0 && i > 0) str[str.length] = "</tr><tr style=\"text-align:center;\">";
			var link = iLinks[i];
			str[str.length] = "<td><a href=\"" + link + "\" class=\"thickbox\" rel=\"gallery\"><img src=\"" + ids[i] + "\" style=\"border:1px solid black;\" /></a></td>";
		}
		str[str.length] = "</tr></table>";
		var output = implode("", str);
		var textarea = getElement(getPageChapterContent());
		if (output != "") textarea.value += output;
	}
}
function getPageChapterContent()
{
	//function to determine whether to insert the data into page content or chapter content..
	var element = getElement("chapter[content]");
	return ((element == null) ? "item[content]" : "chapter[content]");
}
//]]>
</script>

<?=formatHTMLTitle('<a href="#" onclick="$(\'#sectionPageImages\').toggle(\'slow\'); return false;">Images</a>')?>

<div id="sectionPageImages">
	<table class="table_grid">
		<tbody id="item_images_table">
		<tr>
			<th>Select Image</th>
			<th style="width:25%;">Image</th>
			<th>Filename</th>
			<th></th>
		</tr>
<?php
    $alt      = false;
    $affected = array();
    $count    = 0;
    foreach ($item_images as $image_id) {
        $row_data = $cache[$image_id];
        echo item_images_row($count++, $row_data, $alt = !$alt);
        $affected[] = $image_id;
    }
?>
		
	</tbody></table>
	<table>
	<tr id="images_foot"<?=$count % 2 == 1 ? ' class="alt"' : ''?>>
			<td style="text-align:center;" colspan="4"><input type="button" value="Add Image" onclick="pageImagesAdd();" /><?php if ("semihtml" == $item["content_type"]) {?><br /><input type="button" value="Insert Gallery" onclick="insertImages();" /><input type="button" value="Insert Gallery (RA)" onclick="insertImages('right');" /><input type="button" value="Insert Gallery (CA)" onclick="insertImages('center');" /><?php }?></td>
		</tr>
	</table>
	<input type="hidden" name="item_images_affected" value="<?=implode("|", $affected)?>" />
</div><br /><br />

<div style="background-color:white;text-align:center;padding:8px;display:none;" id="sectionpageImages_hideNote">
	Images can be viewed by Clicking "Images" above<br />
</div>
<?php
}
?>

<!-- /images.tpl -->
