<?php
include_once("../connection.inc.php");
$product_list=explode('_',$_REQUEST['product_list']);

$total_labels=count($product_list);
$rows=array_chunk($product_list,3);
?>
<table style="<?= ($total_labels>2)?'width:7.5in;':'' ?>border-collapse:collapse;" align="center">
<?php
foreach($rows as $row)
{
?>
	<tr>
<?php
	foreach($row as $col)
	{
		$product=$DB->query_first("SELECT * FROM products WHERE id=".intval($col));
?>
		<td style="border:1px solid black;vertical-align:middle;text-align:center;width:2.5in;height:2.5in;font-family:arial;">
			<span style="font-size:28pt;font-weight:bold;">COE #<?= $col ?></span><br />
			<img src="product_image.php?product_id=<?= $col ?>" style="width:4cm;height:4cm;" /><br />
			<span style="font-size:10pt;font-weight:bold;"><?= str_replace('<','&lt;',stripslashes($product['product_name'])) ?></span>
		</td>
<?php
	}
	for($i=count($row);$i<3;$i++)
		echo '<td></td>';
?>
	</tr>
<?php
}
?>
</table>
