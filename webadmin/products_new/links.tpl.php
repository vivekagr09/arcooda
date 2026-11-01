<!-- links.tpl -->

<?php
    $section_name='productlink';
function item_links_row($count = "[ROW_COUNT]", $row_data = array())
{
	static $alt = false;

	$section_name = 'productlink';
    $name_key = $section_name . "[links][" . $count . "]";
    $hidden   = array(
        $name_key . "[id]"   => $row_data["id"],
        //$name_key . "[product_id]" => $row_data["product_id"],
        $name_key . "[rank]" => $row_data["rank"],
        //$name_key . "[name]" => $row_data["name"],
        //$name_key . "[link]" => $row_data["link"],
        //$name_key . "[active]" => $row_data["active"],
        //$name_key . "[created]" => $row_data["created"],
        //$name_key . "[modified]" => $row_data["modified"],
    );
    $active_list = array("y" => "Yes", "n" => "No");
    ob_start();
?>
	<tr id="item_links_rowa[<?=$count?>]"<?=(($alt = !$alt) ? ' class="alt"' : "")?>>
		<td style="white-space:nowrap;">
			<a href="#" onclick="item_links_move('<?=$count?>',-1,1);return false"><img src="/webadmin/images/up_arrow.gif" alt="&uarr;" border="0" /></a>
			<a href="#" onclick="item_links_move('<?=$count?>',1,0);return false"><img src="/webadmin/images/down_arrow.gif" alt="&darr;" border="0" /></a>
		</td>
		<td style="white-space:nowrap;">
			<?=formatHTMLInputs($hidden)?>
			<input class="wide" type="text" name="<?=htmlspecialchars($name_key . "[name]")?>" value="<?=htmlspecialchars($row_data["name"])?>" />
		</td>
		<td style="white-space:nowrap;">
			<select class="wide link_type" name="<?=htmlspecialchars($name_key . "[type]")?>"  row-id="<?= $count ?>"><?=formatHTMLOptions(['external'=>'External','internal'=>'Internal'], $row_data["type"])?></select>
		</td>
		<td style="white-space:nowrap;">
			<div id="linkurl-<?= $count?>" style=" <?= ($row_data["type"]=='internal')?'display:none;':''?>">
			<input class="wide" type="text" name="<?=htmlspecialchars($name_key . "[link]")?>"  value="<?=htmlspecialchars($row_data["link"])?>" placeholder="https://..."/>
			</div>
			<div id="link_attachment-<?= $count?>" style=" <?= ($row_data["type"]=='internal')?'':'display:none;'?>">
			<p><a href="/webadmin/uploads/product/links/<?= $row_data["attachment"] ?>" target="_blank" id="image-name-<?= $count ?>"><?= $row_data["file_original"] ?></a></p>
			<?php if($row_data["file_original"]){ ?>
			<p><small style="color:#f00;"><i>NB: Uploading new image will overwrite existing image</i></small></p>
			<table>
				<tr>
					<th>File Type</th>
					<th>Size</th>
					<th>date Added</th>
				</tr>
				<tr>
					<td><?=htmlspecialchars($row_data["filetype"])?></td>
					<td><?=htmlspecialchars($row_data["size"])?></td>
					<td><?= date('d/m/Y', strtotime($row_data["created"]))?></td>
				</tr>
			</table>
			<?php } ?>
			<input type="hidden"  name="<?=htmlspecialchars($name_key . "[attachment]")?>" value="<?=htmlspecialchars($row_data["attachment"])?>" >
			<input type="hidden"  name="<?=htmlspecialchars($name_key . "[file_original]")?>" value="<?=htmlspecialchars($row_data["file_original"])?>" >
			<input type="hidden"  name="<?=htmlspecialchars($name_key . "[filetype]")?>" value="<?=htmlspecialchars($row_data["filetype"])?>" >
			<input type="hidden"  name="<?=htmlspecialchars($name_key . "[size]")?>" value="<?=htmlspecialchars($row_data["size"])?>" >
			<input type="hidden" name="MAX_FILE_SIZE" value="<?=htmlspecialchars(convertSize(ini_get("upload_max_filesize")))?>" />
			<input class="data-image" type="file" placeholder="image" id="option-val-input-<?= $count ?>" name="link_image[<?=$count?>]" data-id="<?= $count ?>" style="width:100px; display:none;" /><br />
			<label for="option-val-input-<?= $count ?>" class="btnDN" style="padding: 2px;cursor: pointer;margin-top: 2px;">Upload</label>
			<br/>
			</div>
		</td>
		<td style="white-space:nowrap;">
			<textarea name="<?=htmlspecialchars($name_key . "[description]")?>" cols="25" rows="5"><?=htmlspecialchars($row_data["description"])?></textarea>
		</td>
		<td style="white-space:nowrap;">
			<select class="wide" name="<?=htmlspecialchars($name_key . "[active]")?>"><?=formatHTMLOptions($active_list, $row_data["active"])?></select>
		</td>
		<td style="white-space:nowrap;">
			<input type="checkbox" class="wide" name="<?=htmlspecialchars($name_key . "[for_everyone]")?>" value="y" <?= $row_data["for_everyone"] == 'y' ? 'checked' : '' ?>>
		</td>
		<td>

			<a href="#" onclick="return item_links_delete(<?=$count?>)"><img src="/webadmin/images/icons/icon_remove.gif" alt="Remove" border="0" /></a></td>
	</tr>
<?php
return ob_get_clean();
}

if (is_array($item_links)) {
?>

<script type="text/javascript">
	$(document).on("change", '.link_type', function(event){
		var row_id=$(this).attr('row-id');
		if($(this).val()=='external'){
			$("#linkurl-"+row_id).show();
			$("#link_attachment-"+row_id).hide();
		}else{
			$("#linkurl-"+row_id).hide();
			$("#link_attachment-"+row_id).show();
		}
		
	        
	});
	$(document).on("change", '.data-image', function(event){
			var dataid=$(this).attr('data-id');
			var filename = event.target.files[0].name;
			$('#image-name-'+dataid).html(filename);
	});
var item_links_count = <?=json_encode(count($item_links) + 256)?>;
function item_links_add()
{
	var row_string = <?=json_encode(item_links_row())?>;
	row_string = row_string.replace(/\[ROW_COUNT\]/g, item_links_count);
	$("#item_links_table").append(row_string);
	var count = 0;
	$("[id^='item_links_rowa[']").each(function() {
		$("[name$='[rank]']",this).val(count++);
	});
	$("[id^='item_links_rowa']").removeClass("alt").filter(":odd").addClass("alt");
	item_links_count++;
}
function item_links_move(row_id, direction, limit)
{
	var $the_row = $("[id='item_links_rowa["+row_id+"]']");
	var $siblings = $the_row.parent().children();
	var position = 0;
	for (var i = 0; i < $siblings.length; i++) if ($siblings[i].id == $the_row[0].id) position = i;
	if (direction < 0 && position + direction >= limit)
	{
		$the_row.insertBefore($siblings.eq(position + direction));
	}
	else if (direction > 0 && position + direction <= $siblings.length - limit)
	{
		$the_row.insertAfter($siblings.eq(position + direction));
	}
	var count = 0;
	$("[id^='item_links_rowa']").each(function() {
		$("[name$='[rank]']", this).val(count++);
	});
	$("[id^='item_links_rowa']").removeClass("alt").filter(":odd").addClass("alt");
}
function item_links_delete(row_id)
{
	if(confirm("Are you sure you want to delete this link ??"))
	{
		$("[id='item_links_rowa["+row_id+"]']").remove();
		$("[id^='item_links_rowa']").removeClass("alt").filter(":odd").addClass("alt");
	}
	return false;
}
function item_links_sort(column)
{
	var rows = [];
	var vals = [];
	$("[id^='item_links_rowa[']").each(function() {
		var row_id = $(this).attr("id").substr(16);
		rows.push(row_id);
		var val = $("[name$='[" + column + "]']", this).text();
		if (val == "") val = $("[name$='[" + column + "]']", this).val();
		vals.push(val);
	});
	var reverse = true;
	var bubble = true;
	while(bubble)
	{
		bubble = false;
		for(var i = 0;i < (rows.length - 1);i++) for(var j = i + 1;j < rows.length;j++) if (vals[i] > vals[j])
		{
			temp = rows[i]; rows[i] = rows[j]; rows[j] = temp;
			temp = vals[i]; vals[i] = vals[j]; vals[j] = temp;
			bubble = true;
			reverse = false;
		}
	}
	if (reverse) rows = rows.reverse();
	console.log(rows);
	var table = $("#item_links_table");
	for(var i = 0;i < rows.length;i++)
	{
		table.append($("[id='item_links_rowa" + rows[i] + "']"));
	}
	var count = 0;
	$("[id^='item_links_rowa[']").each(function() {
		$("[name$='[rank]']",this).val(count++);
	});
	$("[id^='item_links_rowa']").removeClass("alt").filter(":odd").addClass("alt");
	return false;
}
$(function() {
	$('.item_links_add_button').css('display','').click(function() {
		item_links_add();
		return false;
	});
});
//]]>
</script>

<?=formatHTMLTitle('<a href="#" onclick="$(\'#sectionLinks\').add(\'#sectionLinks_hideNote\').slideToggle(\'slow\');return false">' . htmlspecialchars(_("Support")) . "</a>", '<a href="#" class="item_links_add_button" style="display:none;">Add Support</a>')?>

<div id="sectionLinks">
	<table class="table_grid" id="item_links_table">
		<tr>
			<th></th>
			<th><a href="#" onclick="return item_links_sort('name')">Name</a></th>
			<th ><a href="#" onclick="return item_links_sort('type')">Type</a></th>
			<th><a href="#" onclick="return item_links_sort('link')">Link/File</a></th>
			<th>Description</th>
			<th>Active</th>
			<th>For Everyone</th>
			<th></th>
		</tr>
<?php
$count    = 0;
    $affected = array();

    foreach ($item_links as $links) {
        $affected[] = $links['id'];
        echo item_links_row($count++, $links);
    }
?>
	</table>
	<input type="hidden" name="<?=htmlspecialchars($section_name)?>[links][affected]" value="<?=implode(',', $affected)?>" />
	<br /><br />

</div>
<div id="sectionLinks_hideNote" style="background:#fff;text-align:center;padding:8px;display:none;">
	<?=htmlspecialchars(sprintf(_("%s can be viewed by clicking \"%s\" above"), _("Links"), _("Links")))?><br />
</div>

<?php
}
?>

<!-- /links.tpl -->
