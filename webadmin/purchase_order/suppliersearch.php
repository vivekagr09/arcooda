<?php
include_once __DIR__ . '/common.inc.php';

$sell_currency = $posted['currency'];
if ('' == $sell_currency) {
    $sell_currency = 'USD';
}

$product           = productFromDatabase($posted['product_id']);
$product_suppliers = productSuppliersFromDatabase($posted['product_id']);

switch ($_REQUEST['action']) {
    case 'select':
        $supplier                  = $product_suppliers[$posted['supplier_id']];
        $supplier_id               = $supplier['supplier_id'];
        $js_data                   = array();
        $js_data                   = array_merge(supplierFromDatabase($supplier_id), $supplier);
        $js_data['return_message'] = $posted['return_message'];
?><!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title></title>
		<script type="text/javascript">
//<![CDATA[
var data = <?php jecho($js_data)?>;
if(window.opener!=null && window.opener.setSupplierData!=null)
	window.opener.setSupplierData(data);
else
	alert('Unable to send supplier back to form');
window.close();
//]]>
		</script>
	</head>
	<body>
	</body>
</html>
<?php
break;
    default:
        include_once __DIR__ . '/header.inc.php';

        $action_url           = $get_vars;
        $action_url['action'] = 'select';
        $action_url           = '?' . http_build_query($action_url);

        $suppliers_url           = $get_vars;
        $suppliers_url['action'] = 'list';
        $suppliers_url           = '/webadmin/new_suppliers/?' . http_build_query($suppliers_url);

        $country_list = supplierGetOptionList('country');
?>
<form accept-charset="utf-8" method="post" action="<?php hecho($action_url)?>">
	<input type="hidden" name="action" value="select" />
	<input type="hidden" name="supplier_id" value="" />

<?= formatHTMLTitle('<a href="javascript:toggleSection(\'sectionShipSuppliers\');">Suppliers That Ship Product</a>') ?>

<div id="sectionShipSuppliers">
	<table class="wide spacetable">
		<tr>
			<th>Product Name</th>
			<th>Supplier Name</th>
			<th>Buy Price</th>
			<th>Buy Price <?= $sell_currency ?></th>
			<th>Sell Price <?= $sell_currency ?></th>
			<th>Location</th>
			<th>&nbsp;</th>
		</tr>
<?php
foreach ($product_suppliers as $key => $supply) {
            $supplier = supplierFromDatabase($supply['supplier_id']);
?>
		<tr<?= ($alt = !$alt) ? ' class="alt"' : '' ?>>
			<td><?php hecho($supply['product_name'])?></td>
			<td><?php hecho($supplier['supplier'])?> <?php hecho($supplier['supplier_name_ch'])?></td>
			<td><?= getCurrencySymbol($supply['cost_currency']) . convertCurrency($supply['cost_price'], $supply['cost_currency']) ?> <?= $supply['cost_currency'] ?></td>
			<td><?= getCurrencySymbol($sell_currency) . convertCurrency($supply['cost_price'], $supply['cost_currency'], $sell_currency) ?> <?= $sell_currency ?></td>
			<td><?= getCurrencySymbol($sell_currency) . convertCurrency($supply['sell_price'], $sell_currency) ?> <?= $sell_currency ?></td>
			<td><?= $country_list[$supplier['country']] ?></td>
			<td><input type="button" value="Select" onclick="<?php hecho('setValue("supplier_id", ' . jecho($key, 1) . ');form.submit()')?>" /></td>
		</tr>
<?php
}
?>
		<tr><th colspan="8" style="text-align:center;">Found <?= count($product_suppliers) ?> results</th></tr>
	</table>
	<br /><br />
</div>
</form>

<table align="center" cellpadding="7" width="95%"><tr><td bgcolor="#fafafa"><div align="center"><a href="<?php hecho($suppliers_url)?>">All other suppliers can be shown by clicking here</a></div></td></tr></table>
<br /><br />
<?php
        include_once __DIR__ . '/footer.inc.php';

        break;
}
?>
