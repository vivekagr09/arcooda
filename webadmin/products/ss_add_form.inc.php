<?php
// inputs:
// - $id

$id_list = array();
if ($id) {
	$query = $DB->query(sprintf(
		"SELECT `id`, `product_name` FROM `products` WHERE `id` = %d LIMIT 1",
		$id
	));
} else {
	$query = $DB->query("SELECT `id`, `product_name` FROM `products` ORDER BY `product_name` ASC");
}
while ($row = $DB->fetch_assoc($query)) {
	$id_list[$row['id']] = $row['product_name'];
}
$DB->free_result($query);

$ss_type_list = array();
$query = $DB->query("SELECT `id`, `type` FROM `screenshot_type` ORDER BY `type` ASC");
while ($row = $DB->fetch_assoc($query)) {
  $ss_type_list[$row['id']] = $row['type'];
}
$DB->free_result($query);
?>

<table class="table_fields">
	<tr>
		<th>Product Item :</th>
		<td><select name="part_id" class="wide"><?= formatHTMLOptions($id_list, $id) ?></select></td>
	</tr>
<?php
/*
?>
	<tr>
		<th></th>
		<td><b>-- Select from above or enter ID below--</b></td>
	</tr>
	<tr>
		<th>Product ID :</th>
		<td><input type="text" name="part_id_cus" class="wide" value="<?php hecho($id) ?>" /></td>
	</tr>
<?php
*/
?>
</table>

<br />

<?php
$i = 0;
//for ($i = 0; $i < 1; $i++) {
?>
<table class="table_fields">
	<tr>
		<th>Screenshot <?= $i + 1 ?> :</th>
		<td>
			<input type="hidden" name="MAX_FILE_SIZE" value="<?=htmlspecialchars(convertSize(ini_get("upload_max_filesize")))?>" />
			<input type="file" name="part_ss[<?= $i ?>]" class="wide" />
		</td>
	</tr>
	<tr>
		<th>Caption <?= $i + 1 ?> :</th>
		<td><input type="text" name="part_ss_cap[<?= $i ?>]" size="40" class="wide" /></td>
	</tr>
	<tr>
		<th>Screenshot Type :</th>
		<td><select name="ss_type[<?= $i ?>]" class="wide"><?= formatHTMLOptions($ss_type_list, $ss['ss_type']) ?></select></td>
	</tr>
</table>

<br />

<?php
//}
?>
<div style="text-align:center;">
	<input type="hidden" name="action" value="do_add" />
	<input type="submit" name="add" value="Add" />
	<input type="reset" name="clear" value="Clear" />
	<input type="hidden" name="part_id" value="<?= $id ?>" />
</div>

<!-- /ss_add_form.inc -->
