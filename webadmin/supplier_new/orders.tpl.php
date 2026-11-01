<!-- orders.tpl -->

<?php
moduleLoad('invoice'); //invoiceProductsFromDatabase, invoiceGetOptionList, invoiceFromDatabase

$member['total_orders']=0;
$member['total_inquiries']=0;
if (isset($member_invoices) && is_array($member_invoices) && moduleLoad('invoice')) {
	$pt = array(
		0 => "/webadmin/images/other.jpg",
		1 => "/webadmin/images/parts.jpg",
		2 => "/webadmin/images/machines.jpg",
		3 => "/webadmin/images/pcbs.jpg",
		4 => "/webadmin/images/merchandises.jpg",
	);

    $paymenttype_list = invoiceGetOptionList('paymenttype_id');
    $status_list      = invoiceGetOptionList('invoice_action');
?>

<?=formatHTMLTitle('<a href="#" onclick="toggleSection(\'sectionMemberOrders\');return false">Purchase Order History</a>', '<i><a href="/webadmin/purchase_order/?action=add&amp;status=sales&amp;member_id=' . $member_id . '" style="color:#000;" target="_blank">Create New Purchase Order</a> | <a href="/webadmin/purchase_order/?action=list&amp;member_id=' . $member_id . '&amp;status=sales|proforma|processed|return|completed" target="_blank">View all Purchase Orders (' . $member['total_orders'] . ')</a></i>')?>

<div id="sectionMemberOrders">
	<table class="table_grid">
		<tr>
			<th>Date</th>
			<th>Invoice #</th>
			<th>Value USD</th>
			<th>Status</th>
			<th>Payment Type</th>
			<th></th>
		</tr>
<?php
    foreach ($member_invoices as $invoice_id) {
        $invoice          = invoiceFromDatabase($invoice_id);
        $products_tooltip = array();
        foreach (invoiceProductsFromDatabase($invoice_id) as $product) {
            $products_tooltip[] =
            '<tr>' .
            '<td style="text-align:right;">' . $product['product_qty'] . '</td>' .
            '<td>x</td>' .
            '<td nowrap>#' . $product['product_id'] . ': ' . $product['product_name'] . '</td>' .
            '<td>@</td>' .
            '<td style="text-align:right;">' . convertCurrency($product['product_offer'], $invoice_currency) . '</td>' .
            '<td>=' . $symbol . '</td>' .
            '<td style="text-align:right;">' . convertCurrency($product['product_offer'] * $product['product_qty'], $invoice_currency) . '</td>' .
                '<td>' . $invoice_currency . '</td>' .
                '</tr>';
        }
        $ship_offer = convertCurrency($invoice['ship_offer'], $invoice['invoice_currency']);
        if ('' != $invoice['ship_product_name']) {
            $products_tooltip[] = '<tr><td style="text-align:right;">1</td><td>x</td><td nowrap>Freight: ' . $invoice['ship_product_name'] . '</td><td>@</td> <td>' . $ship_offer . '</td><td>=' . $symbol . '</td><td style="text-align:right;">' . $ship_offer . '</td><td>' . $invoice['invoice_currency'] . '</td></tr>';
        }

        $products_tooltip = '<table>' . implode('', $products_tooltip) . '</table>';
?>
		<tr<?=($alt = !$alt) ? ' class="alt"' : ''?>>
			<td><?=date("d/m/y", $invoice['open_time'])?></td>
			<td onmouseover="<?php hecho('tt_in(' . jecho($products_tooltip, 1) . ')')?>" onmouseout="tt_out()"><?php hecho($invoice['invoice_number']) ?></td>
			<td>$<?= convertCurrency($invoice['invoice_offerus'], 'USD') ?></td>
			<td><?=$status_list[$invoice['invoice_action']]?></td>
			<td><?=$paymenttype_list[$invoice['paymenttype_id']]?></td>
			<td>
				<a target="_blank" href="/webadmin/invoice/?action=edit&amp;invoice_id=<?=$invoice["invoice_id"]?>">Edit</a> |
				<a target="_blank" href="/webadmin/invoice/?action=view&amp;invoice_id=<?=$invoice['invoice_id']?>">View</a> |
				<a target="_blank" href="/webadmin/invoice/?action=copy&amp;invoice_id=<?=$invoice['invoice_id']?>">Make New</a>
			</td>
		</tr>
<?php
    }
?>
	</table>
	<br />
	<input type="button" onclick="<?php hecho("window.open(" . jecho("/webadmin/invoice/?action=list&member_id=" . $member_id . "&status=sales|proforma|processed|return|completed", 1) . ")") ?>" value="View All Orders (<?= $member['total_inquiries'] ?>)" />
	<br />
	<br />
	<br />
</div>

<div style="background-color:#fff;text-align:center;padding:8px;display:none;" id="sectionMemberOrders_hideNote">Customer Orders can be viewed by Clicking "Customer Orders" above<br /></div>

<?php
}
?>

<!-- /orders.tpl -->
