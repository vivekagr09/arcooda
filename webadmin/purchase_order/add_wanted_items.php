<?php
include_once __DIR__ . '/common.inc.php';

$invoice_id = intval($posted['invoice_id']);
if (!$invoice_id) {
    die('Invalid Parameters passed....');
}

$invoice          = invoiceFromDatabase($invoice_id);
$invoice_products = invoiceProductsFromDatabase($invoice_id);
?><!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
		<title>Select Products</title>
		<script type="text/javascript" src="/webadmin/jscript/common.php.js"></script>
		<script type="text/javascript">
//<![CDATA[
var count = 0;
function http_build_query(query_data, numeric_prefix, arg_separator, enc_type) {
	return Object.keys(query_data).map(k => encodeURIComponent(k) + "=" + encodeURIComponent(query_data[k]));
}
function addWantedItems() {
    var urlToOpen = '/webadmin/wanted/?action=add&member_id=<?=$invoice['member_id']?>&member_name=<?=urlencode($invoice['bill_name'])?>&company_name=<?=urlencode($invoice['bill_company'])?>&invoice_id=<?=$invoice_id?>';
    for (var i = 0; i < count; i++) {
		var name = getElement('product_name[' + i + ']');
		if (name && name.checked) {
			var pid = getElement('product_id[' + i + ']');
			if (pid) {
				var sid = getElement('sub_id[' + i + ']');
				if (sid) {
					var qty = getElement('qty[' + i + ']');
					if (qty != null)
						window.opener.open(urlToOpen + '&product_name=' + encodeURIComponent(name.value) + '&product_id=' + encodeURIComponent(pid.value) + '&sub_id=' + encodeURIComponent(sid.value) + '&qty=' + encodeURIComponent(qty.value));
				}
			}
		}
    }
    window.close();
}
//]]>
		</script>
		<link type="text/css" rel="stylesheet" href="style.css" />
	</head>
	<body>

<div class="invoice_content" align="center"><?=formatHTMLTitle('Select Products To Add as Wanted')?><table class="wide" style="width:95%;" align="center">
	<tr><th style="width:15%;">Select</th><th>Product Name</th></tr>
<?php
$count = 0;
foreach ($invoice_products as $prod) {
?>
	<input type="hidden" name="product_id[<?=$count?>]" value="<?=$prod['product_id']?>" />
	<input type="hidden" name="sub_id[<?=$count?>]" value="<?=$prod['sub_id']?>" />
	<input type="hidden" name="qty[<?=$count?>]" value="<?=$prod['product_qty']?>" />
	<tr style="text-align:center;"<?=$count % 2 == 1 ? ' class="alt"' : ''?>>
		<td style="width:15%;"><input type="checkbox" name="product_name[<?=$count?>]" value="<?=$prod['product_name']?>" /></td>
		<td><?=$prod['product_name']?></td>
	</tr>
<?php
$count++;
}
?>
	<tr<?=($count % 2) ? ' class="alt"' : ''?>><td colspan="2" align="center"><input type="button" onclick="addWantedItems()" value="Add Products as Wanted" /></td></tr>
</table></div>

		<script type="text/javascript">
//<![CDATA[
count=<?=$count?>;
//]]>
		</script>
	</body>
</html>
