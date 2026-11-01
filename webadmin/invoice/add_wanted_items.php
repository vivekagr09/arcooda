<?php
/**
 * @var mixed[] $posted
 */
include_once __DIR__ . '/common.inc.php';

$invoice_id = intval($posted['invoice_id']);
if (!$invoice_id) {
    die('Invalid Parameters passed....');
}

$invoice          = invoiceFromDatabase($invoice_id);
$invoice_products = invoiceProductsFromDatabase($invoice_id);
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8" />
		<title>Select Products</title>
		<link rel="stylesheet" type="text/css" href="style.css" />
		<script type="text/javascript" src="/webadmin/jscript/common.php.js"></script>
		<script type="text/javascript">
//<![CDATA[
var count = 0;
function addWantedItems()
{
	var urlToOpen = "/webadmin/wanted_items/?action=add&member_id=<?=$invoice['member_id']?>&member_name=<?=$invoice['bill_name']?>&company_name=<?=$invoice['bill_company']?>&invoice_id=<?=$invoice_id?>";
	for (var i = 0;i < count;i++) {
		var name = getElement('product_name[' + i + ']');
		var pid  = getElement('product_id[' + i + ']');
		var sid  = getElement('sub_id[' + i + ']');
		var qty  = getElement('qty[' + i + ']');
		if (name != null && pid != null && sid != null && qty != null && name.checked) {
			window.opener.open(urlToOpen + '&product_name=' + name.value + '&product_id=' + pid.value + '&sub_id=' + sid.value + '&qty=' + qty.value);
		}
	}
	window.close();
}
//]]>
		</script>
	</head>
	<body>

<div class="invoice_content" style="text-align:center"><?=formatHTMLTitle('Select Products To Add as Wanted')?><table class="wide" style="width:95%;margin:auto">
	<tr>
		<th style="width:15%;">Select</th>
		<th>Product Name</th>
	</tr>
<?php
$count = 0;
foreach ($invoice_products as $invoice_product) {
?>
	<input type="hidden" name="product_id[<?=$count?>]" value="<?=$invoice_product['product_id']?>" />
	<input type="hidden" name="sub_id[<?=$count?>]" value="<?=$invoice_product['sub_id']?>" />
	<input type="hidden" name="qty[<?=$count?>]" value="<?=$invoice_product['product_qty']?>" />
	<tr style="text-align:center;"<?=($count % 2 == 1) ? ' class="alt"' : ''?>>
		<td style="width:15%;"><input type="checkbox" name="product_name[<?=$count?>]" value="<?=htmlspecialchars($invoice_product['product_name'])?>" /></td>
		<td><?=htmlspecialchars($invoice_product['product_name'])?></td>
	</tr>
<?php
    $count++;
}
?>
	<tr<?=($count % 2 == 1) ? ' class="alt"' : ''?>><td colspan="2" style="text-align:center"><input type="button" onclick="addWantedItems()" value="Add Products as Wanted" /></td></tr>
</table></div>

		<script type="text/javascript">
//<![CDATA[
count = <?=$count?>;
//]]>
		</script>
	</body>
</html>
