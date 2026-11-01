<!-- manuals.tpl -->

<?php
function item_manuals_row($count = '[ROW_COUNT]', $row_data = array())
{
    $section_name='product';
    $name_key = $section_name . "[manuals][" . $count . "]";
    $hidden   = array(
        $name_key . "[id]" => $row_data["product_manual_id"],
    );
    $id = $row_data["product_manual_id"];

    ob_start();
?>
	<tr style="text-align: center;" <?=(($count && 1) ? ' class="alt manuals_row"' : 'class="manuals_row"')?> id="item_manuals_row_<?=$id?>">
		<td>
			<a href="#" onclick="return manualsMoveUp(this);"><img src="/webadmin/images/up_arrow.gif" alt="↑" border="0"></a>
			<a href="#" onclick="return manualsMoveDown(this);"><img src="/webadmin/images/down_arrow.gif" alt="↓" border="0"></a>
			<input type="hidden" class="row_count" name="manuals[<?=$id?>]" value="<?=$row_data["pos"]?>"/>
		</td>
		<td><?=$row_data["name"]?></td>
		<td style="text-align:center"><?= htmlspecialchars(strtoupper(pathinfo($row_data['file_name'], PATHINFO_EXTENSION))) ?></td>
		<td><?=$row_data["comment"]?></td>
		<td><?=($row_data["status"]=='y')?'Active':'In Active'?></td>
		<td><?=date("d/m/Y", strtotime($row_data["prodduct_created"]))?></td>
		<td>
			<a href="https://www.arcooda.com/manual/view-<?=$row_data['id']?>.html?type=manual" target="_blank">View</a>
			|
			<a target="_blank" href="/webadmin/owner/manuals/edit.php?id=<?=$row_data['id']?>">Edit</a>
		</td>
		<td><a style="cursor:pointer;" onclick="deletemanualMessage(<?=$row_data['product_id']?>,<?= $id ?>);" ><img src="/webadmin/images/icons/icon_remove.gif" alt="Remove" border="0" /></a></td>
	</tr>
<?php
	return ob_get_clean();
}
?>

<script type="text/javascript">

function manualsMoveUp(elem) {
	console.log('fdgfdg');
	var row = $(elem).parents(".manuals_row");

	if (row.prev().hasClass("manuals_row")) {
		var n = row.prev().find(".row_count").val();
		row.prev().find(".row_count").val(row.find(".row_count").val());
		row.find(".row_count").val(n);
		row.prev().before(row);
	}

	return false;
}

function manualsMoveDown(elem) {
	console.log('fdgfdgsf');
	var row = $(elem).parents(".manuals_row");

	if (row.next().hasClass("manuals_row")) {
		var n = row.next().find(".row_count").val();
		row.next().find(".row_count").val(row.find(".row_count").val());
		row.find(".row_count").val(n);
		row.next().after(row);
	}

	return false;
}
function deletemanualMessage(product_id,id) {
	var c = confirm("Are you sure you want to delete this manual?");
	if(c){
			window.open("/webadmin/products_new/manuals.php?action=do_delete&popup=3&product_id="+product_id+"&id="+id);
	}
}
function setManuals(data) {
	if (data["delete"] == true) {
		if ($("#item_manuals_row_" + data.id).length > 0) {
			$("#item_manuals_row_" + data.id).remove();
		}
	}
}
</script>

<?=formatHTMLTitle('<a href="#" onclick="$(\'#sectionManuals\').add(\'#sectionManuals_hideNote\').slideToggle(\'slow\');return false">' . htmlspecialchars(_("Manuals")) . "</a>", '<a href="/webadmin/owner/manuals/add.php"  target="_blank">Add Manual</a>')?>

<div id="sectionManuals">
	<table class="table_grid" id="product_manuals_table">
		<thead>
			<tr>
				<th></th>
				<th>Name of Manual</th>
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

$count   = "0";
$afected = array();
foreach ($item_manuals as $manual) {
    $affected[] = $manual['id'];

    $manual["active"] = ("y" == $manual["active"]) ? "Active" : "Inactive";

    echo item_manuals_row($count++, $manual);
}
?>
		</tbody>
	</table>
	<br /><br />
</div>
<div id="sectionManuals_hideNote" style="background:#fff;text-align:center;padding:8px;display:none;">
	<?=htmlspecialchars(sprintf(_("%s can be viewed by clicking \"%s\" above"), _("Manuals"), _("Manuals")))?><br />
</div>


<!-- /manuals.tpl -->
