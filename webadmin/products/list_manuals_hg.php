<?php
include_once __DIR__ . '/common.inc.php';

?><!DOCTYPE html>
<html>
<head>
<style type="text/css">
<!--
.titles
{
	font-weight: bold;
	font-size: 10pt;
	color: #FFFFFF;
	background-color: #555555;
	border:1px solid black;
	text-align: center;
/*	writing-mode:tb-rl; */
}
.data
{
	border:1px solid black;
	font-size: 10pt;
	text-align: center;
	padding:2px 2px 2px 2px;
}
-->
</style>
<script language="JavaScript">
function printpage() {
        if (window.print)
        {
                window.print();
        }
        else
        {
                alert('To print this page, select Print Frame from the file menu.');
        }
}

function doPrint()
{
        if (confirm("Click OK to print this document"))
        {
                printpage();
        }

}
</script>
</head>
<?php
$whereClause = "";
$category    = "";
$subCategory = "";
$parent      = $_GET['parent'];
$type        = $_GET['type'];
/*if($parent !="")
{
$temp=$DB->query_first("SELECT name FROM paroduct_types WHERE id='".$parent."'");
$category=ucwords($temp[0]);
$whereClause=" WHERE pt.parent='".$parent."'";*/
if ("" != $_GET['type']) {
    $temp2       = $DB->query_first("SELECT type_name FROM parts_types WHERE type_id='" . $type . "'");
    $subCategory = ucwords($temp2[0]);
    $whereClause .= " AND p.part_type='" . $type . "'";
}
//}

$sql = " SELECT" .
    "	p.part_id" .
    " FROM parts p" .
    " LEFT JOIN parts_types pt ON p.part_type=pt.type_id" .
    $whereClause .
    " ORDER BY p.part_name";

$query = $DB->query($sql);
while ($row = $DB->fetch_array($query, MYSQLI_ASSOC)) {
    $items[] = $row;
}

?>
<body onload="javascript:doPrint();">
<div align="center">
	<font size="+1"><b><u><?php echo $subCategory ?></u></b></font>
</div>

<br />

<table width="100%" style="border-collapse:collapse;" align="center">
	<tr>
		<td class="titles">Product ID</td>
		<td class="titles">Product Name</td>
		<td class="titles">Type Of Manual</td>
		<td class="titles">Notes</td>
		<td class="titles">File Type</td>
		<td class="titles">Size</td>
		<td class="titles">Uploaded By</td>
		<td class="titles">Date Added</td>
	</tr>
<?php
$sql = "SELECT * FROM product_attachments pa, parts p WHERE (pa.product_id = p.part_id AND p.part_type='$type') ORDER BY pa.attachment_id";
$res = $DB->query($sql);
while ($row = $DB->fetch_array($res)) {
    $size = (0 == $row['size']) ? '' : human_file_size($row['size']);
?>
	<tr>
		<td class="data"><?php echo $row['product_id'] ?></td>
		<td class="data"><?php echo str_replace('<', '&lt;', stripslashes($row['part_name'])) ?></td>
		<td class="data"><?php echo typeofManual($row['manual_type']) ?></td>
		<td class="data"><?php echo $row['description'] ?></td>
		<td class="data"><?php echo $row['filetype'] ?></td>
		<td class="data"><?php echo $size ?></td>
		<td class="data"><?php echo $row['user'] ?></td>
		<td class="data"><?php echo date("d/m/y", strtotime($row['attach_date'])) ?></td>
	</tr>
<?php
}

function typeofManual($code)
{
    $array = array('coe' => 'COE Manual', 'hgr' => 'HG Manual', 'fm' => 'Factory Manual', 'wd' => 'Wiring Diagram/Schematics', 'sm' => 'Service Manual', 'dss' => 'Dip Switch Settings', 'oth' => 'Other');
    return $array[$code];
}
function human_file_size($size)
{
    $filesizename = array(" Bytes", " KB", " MB", " GB", " TB", " PB", " EB", " ZB", " YB");
    return round($size / pow(1024, ($i = floor(log($size, 1024)))), 2) . $filesizename[$i];
}
?>
</table>

<br />

</body>
</html>

