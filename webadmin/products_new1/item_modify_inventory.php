<?php
include_once __DIR__ . '/common.inc.php';
$posted     = array_merge($_GET, $_POST, $_FILES);
$id         = intval($posted['inventory_id']);
$action     = isset($posted['action'])?strtolower($posted['action']):'edit';

$data     = $DB->query_first(sprintf(
    "SELECT * FROM `products_inventory` where id=".$id.""
));
function showUpdateInventory($id, $data)
{
?>
<html>
	<head><title>Update Inventory Cost</title></head>
	<body style="text-align:center;vertical-align:middle;width:100%;height:100%;">
		<h3>Update Inventory Cost</h3>
		<form action="" method="post">
			<input type="hidden" name="action" value="do_edit" />
			
			<input type="hidden" name="id" value="<?php echo $id ?>" />
			<input type="hidden" name="supplier_id" value="<?php echo $data['supplier_id'] ?>" />
			<table align="center" style="width:600px;border:1px solid blue;background-color:#99CCFF;">
				<tr>
					<td style="text-align:right;font: bold 14px verdana;">Cost:</td>
					<td style="text-align:left;"><input style="width:365px;font: 13px verdana;" type="number" min="0" name="cost" value="<?= $data['unit_cost']?>"/></td>
				</tr>
				<tr><td style="text-align:right;">&nbsp;</td><td style="text-align:left;"><input type="submit" value="Update Price"/></td></tr>
			</table>
		</form>
</body></html>
<?php
}
function updateInventoryCost($posted)
{
    global $DB;
    $id=$posted['id'];
    $cost=$posted['cost'];
    $supplier_list  = productGetOptionList('supplier_id');
    $data     = $DB->query_first(sprintf(
    "SELECT * FROM `products_inventory` where id=".$id.""
	));
	$comment=$data['comments']."\n"." Cost updated form $".$data['unit_cost']." USD to $".$cost." USD";
	$DB->query("UPDATE `products_inventory` set `unit_cost`=".$cost.",`comments`='".$comment."' where id=".$id);
	?>
	<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title></title>
		<script type="text/javascript">
	//<![CDATA[
	var data = <?php echo formatJSON($data) ?>;
	//]]>
		</script>
	</head>
	<body style="text-align:center;vertical-align:middle;width:100%;height:100%;">
	<table align="center" style="width:30%;border:1px solid blue;background-color:#99CCFF;">
	<tr><td style="text-align:center;"><b>Invoice Cost Updated Successfully</b></td></tr>
	<tr><td align="center"><input type="button" onclick="window.close();" value="Close Window" /></td></tr>
	</table>
	</body>
	</html>
<?php
}
switch ($action) {
    case 'edit':
        showUpdateInventory($id,$data);
        break;
    case 'do_edit':
        updateInventoryCost($posted);
        break;
    default:
        die("Invalid Selection!!");
}
?>   