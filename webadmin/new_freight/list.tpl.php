<!-- list.tpl -->

<?php
moduleLoad('invoice'); //invoiceSearch, invoiceGroupCount, invoiceSort, invoiceGetValue, invoiceFormatNumber

//Expects: $display_currency
$active_list       = freightGetOptionList('active');
$country_list      = freightGetOptionList('service_from');
$service_type_list = freightGetOptionList('service_type');
$supplier_list     = freightGetOptionList('supplier_id');

$select_url = $get_vars;
unset($select_url['action']);
$select_url = 'select.php?' . http_build_query($select_url) . '&id=';

$sort_url = $get_vars;
unset($sort_url['order_by']);
unset($sort_url['order']);
unset($sort_url['page_id']);
if ('desc' != $order) {
    $sort_url['order'] = 'desc';
}

$sort_url = '?' . http_build_query($sort_url) . '&order_by=';

$edit_url           = $get_vars;
$edit_url['action'] = 'edit';
unset($edit_url['id']);
$edit_url = 'index.php?' . http_build_query($edit_url) . '&id=';
?>

<?=formatHTMLTitle('Freight Services')?>

<br />
<table class="grid_table">
	<tr>
		<th><a href="<?=$sort_url?>id">ID</a></th>
		<th><a href="<?=$sort_url?>name">Product Name</a></th>
		<th><a href="<?=$sort_url?>supplier">Supplier</a></th>
		<th><a href="<?=$sort_url?>country">Country</a></th>
		<th><a href="<?=$sort_url?>qty">Qty</a></th>
		<th><a href="<?=$sort_url?>value">Cost Price (<?=$display_currency?>)</a></th>
		<th><a href="<?=$sort_url?>value">Sell Price (<?=$display_currency?>)</a></th>
		<th><a href="<?=$sort_url?>site">Last Sold</a></th>
		<th><a href="<?=$sort_url?>open">Open Orders</a></th>
		<th><a href="<?=$sort_url?>total">Total Orders</a></th>
		<th><a href="<?=$sort_url?>date">Last Order</a></th>
		<th><a href="<?=$sort_url?>value">Value (<?=$display_currency?>)</a></th>
		<th><a href="<?=$sort_url?>active">Status</th>
		<th><a href="<?=$sort_url?>type">Courier Type</th>
		<th>&nbsp;</th>
	</tr>
<?php
$alt = false;
foreach ($item_list as $service_id) {
    $service        = freightFromDatabase($service_id);
    $invoice_id     = 0;
    $invoice_list   = array();
    $invoice_chunks = array();

    if (function_exists('moduleLoad') && moduleLoad('invoice')) {
        $invoice_list   = invoiceSearch(array('ship_id' => $service['id']));
        $invoice_chunks = invoiceGroupCount($invoice_list, 'invoice_status');
        $invoice_id     = end(invoiceSort($invoice_list, 'proforma_date'));

        $service['last_sold_time'] = max($service['last_sold_time'], invoiceGetValue($invoice_id, 'proforma_time'));
    }
?>
	<tr<?=($alt = !$alt) ? '' : ' class="alt"'?>>
		<td><?=htmlspecialchars($service['service_number'])?></td>
		<td><?=htmlspecialchars($service['name'])?></td>
		<td><?=htmlspecialchars($supplier_list[$service['supplier_id']])?></td>
		<td><?=htmlspecialchars($country_list[$service['service_from']])?></td>
<?php
switch ($service['service_type']) {
        case 'pp':
?>
		<td><?=intval($service['qty'])?></td>
		<td>
			<?=getCurrencySymbol($service['currency'])?>
			<?=convertCurrency($service['cost_price'], $service['currency'], $display_currency)?>
			<?=$display_currency?>
		</td>
		<td>
			<?=getCurrencySymbol($service['currency'])?>
			<?=convertCurrency($service['sell_price'], $service['currency'], $display_currency)?>
			<?=$display_currency?>
		</td>
<?php
            break;
        default:
?>
		<td>N/A</td>
		<td>N/A</td>
		<td>N/A</td>
<?php
            break;
    }
?>
		<td><?=(0 != $service['last_sold_time']) ? date('d/m/Y', $service['last_sold_time']) : 'N/A'?></td>
		<td><?=(0 != $invoice_chunks['enquiry'] || 0 != $invoice_chunks['inquiry'] || 0 != $invoice_chunks['proforma']) ? intval($invoice_chunks['enquiry'] + $invoice_chunks['inquiry'] + $invoice_chunks['proforma']) : 'N/A'?></td>
		<td><?=(count($invoice_list) != 0) ? count($invoice_list) : 'N/A'?></td>
		<td><?=(0 != $invoice_id) ? invoiceFormatNumber($invoice_id) : 'N/A'?></td>
		<td><?='N/A'?></td>
		<td><?=htmlspecialchars($active_list[$service['active']])?></td>
		<td><?=htmlspecialchars($service_type_list[$service['service_type']])?></td>
<?php
$links = array();
    if ($popup_window) {
        $links[] = '<a href="' . htmlspecialchars($select_url . $service_id) . '">Select</a>';
    } else {
        $links[] = '<a href="' . htmlspecialchars($edit_url . $service_id) . '" target="_blank">Edit</a>';
    }

?>
		<td><?=implode(' | ', $links)?></td>
	</tr>
<?php
}
?>
</table>
<br />

<!-- /list.tpl -->
