<!-- firmware.tpl -->

<?php
function item_firmware_row($count = '[ROW_COUNT]', $row_data = array())
{
    global $section_name;
    $name_key = $section_name . "[firmware][" . $count . "]";
    $hidden   = array(
        $name_key . "[id]" => $row_data["product_firmware_id"],
    );
    ob_start();
?>
	<tr style="text-align: center;" id="item_firmware_row_<?=$row_data['product_firmware_id']?>" <?=(($count && 1) ? ' class="alt firmware_row"' : "class='firmware_row'")?>>
		<td>
			<a href="#" onclick="return firmwareMoveUp(this);"><img src="/webadmin/images/up_arrow.gif" alt="↑" border="0"></a>
			<a href="#" onclick="return firmwareMoveDown(this);"><img src="/webadmin/images/down_arrow.gif" alt="↓" border="0"></a>
			<input type="hidden" class="row_count" name="firmware_pos[<?=$row_data['product_firmware_id']?>]" value="<?=$row_data["pos"]?>"/>
		</td>
		<td><?=$row_data['name']?></td>
		<td style="text-align:center"><?= htmlspecialchars(strtoupper(pathinfo($row_data['file_name'], PATHINFO_EXTENSION))) ?></td>
		<td><?=htmlspecialchars($row_data["comment"])?></td>
		<td><?=($row_data["status"]=='y')?'Active':'In Active'?></td>
		<td style="white-space:nowrap;"><?=htmlspecialchars(date("d/m/Y", strtotime($row_data['created'])))?></td>
		<td>
			<a href="https://www.arcooda.com/manual/view-<?=$row_data['id']?>.html?type=firm" target="_blank">View</a>
			|
			<a target="_blank" href="/webadmin/owner/firmware/edit.php?id=<?=$row_data['id']?>">Edit</a>
		</td>
		<td><a style="cursor:pointer;" onclick="return deleteMessage(<?=$row_data['product_id']?>,<?= $row_data['product_firmware_id'] ?>);"><img src="/webadmin/images/icons/icon_remove.gif" alt="Remove" border="0" /></a></td>
	</tr>
<?php
    return ob_get_clean();
}

if (is_array($item_firmware)) {
?>

<script type="text/javascript">
//<![CDATA[
function deleteMessage(product_id,id) {
	var c = confirm("Are you sure you want to delete this firmware?");
	if(c){
			window.open("/webadmin/products_new/firmware.php?action=do_delete&popup=3&product_id="+product_id+"&id="+id);
	}
}
function firmwareMoveUp(elem) {
	var row = $(elem).parents(".firmware_row");

	if (row.prev().hasClass("firmware_row")) {
		var n = row.prev().find(".row_count").val();
		row.prev().find(".row_count").val(row.find(".row_count").val());
		row.find(".row_count").val(n);
		row.prev().before(row);
	}

	return false;
}

function firmwareMoveDown(elem) {
	var row = $(elem).parents(".firmware_row");

	if (row.next().hasClass("firmware_row")) {
		var n = row.next().find(".row_count").val();
		row.next().find(".row_count").val(row.find(".row_count").val());
		row.find(".row_count").val(n);
		row.next().after(row);
	}

	return false;
}
function setFirmware(data)
{
	if (data["delete"] == true) {
		if ($("#item_firmware_row_" + data.id).length > 0) {
			$("#item_firmware_row_" + data.id).remove();
		}
	} 
}

//]]>
</script>

<?=formatHTMLTitle('<a href="#" onclick="$(\'#sectionFirmware\').add(\'#sectionFirmware_hideNote\').slideToggle(\'slow\');return false">' . htmlspecialchars(_("Firmware")) . "</a>", '<a href="/webadmin/owner/firmware/add.php" target="_blank" >Add Firmware</a>')?>

<div id="sectionFirmware">
	<table class="table_grid" id="<?=htmlspecialchars($section_name)?>_firmware_table">
		<thead>
			<tr>
				<th></th>
				<th>Name of firmware</th>
				<th>File Type</th>
				<th>Comments</th>
				<th>Status</th>
				<th>Created Date</th>
				<th></th>
				<th></th>
			</tr>
		</thead>
		<tbody>
<?php
$count    = 0;
    $affected = array();
    foreach ($item_firmware as $firmware) {
        $affected[] = $firmware['id'];

        $firmware["active"] = ("y" == $firmware["active"]) ? "Active" : "Inactive";
        echo item_firmware_row($count++, $firmware);
    }
?>
		</tbody>
	</table>
	<input type="hidden" name="<?=htmlspecialchars($section_name)?>[firmware][affected]" value="<?=implode(',', $affected)?>" />
	<br /><br />

</div>
<div id="sectionFirmware_hideNote" style="background:#fff;text-align:center;padding:8px;display:none;">
	<?=htmlspecialchars(sprintf(_("%s can be viewed by clicking \"%s\" above"), _("Firmware"), _("Firmware")))?><br />
</div>

<?php
}
?>

<!-- /firmware.tpl -->
