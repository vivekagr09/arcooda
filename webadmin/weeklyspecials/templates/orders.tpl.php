<!-- orders.tpl.php -->

<?php
moduleLoad('invoice'); //invoiceProductsFromDatabase, invoiceSort, invoiceSearch, invoiceGetValue, invoiceSumValue, invoiceGetOptionList, invoiceFromDatabase, invoiceFormatNumber

$invoice_list = invoiceSort(invoiceSearch(array('id' => $id, 'invoice_status' => array('proforma', 'sales', 'processed'))), 'open_date');
$last_invoice_date = 'Never';
if (count($invoice_list)) $last_invoice_date = date('d/m/Y', invoiceGetValue(end($invoice_list), 'open_time'));
$invoice_purchase_total = convertCurrency(invoiceSumValue($invoice_list,'invoice_offerus'),'USD');

$invoice_list = array_reverse($invoice_list);
$paymenttype_list=invoiceGetOptionList('paymenttype_id');
$status_list=invoiceGetOptionList('invoice_action');
?>

<script type="text/javascript">
//<![CDATA[
//]]>
</script>

<?= formatHTMLTitle('<a href="javascript:toggleSection(\'sectionWeeklySpecialOrders\');">Customer Orders</a>', '<i><a href="/webadmin/invoice/?action=add&amp;status=inquiry&amp;id='.$id.'" style="color:#000;" target="_blank">Create New Inquiry</a> | <a href="/webadmin/invoice/?action=add&amp;status=sales&amp;id='.$id.'" style="color:#000;" target="_blank">Create New Invoice</a></i>') ?>

<div id="sectionWeeklySpecialOrders">
	<table class="wide">
		<tr>
			<th>Date</th>
			<th>Invoice #</th>
			<th>Value USD</th>
			<th>Status</th>
			<th>Payment Type</th>
			<th></th>
		</tr>
<?php
$count=0;
foreach($invoice_list as $invoice_id)
{
	$count++;
	$invoice = invoiceFromDatabase($invoice_id);
	$invoice_products = invoiceProductsFromDatabase($invoice_id);
	$products_tooltip=array();
	foreach($invoice_products as $product)
	{
		$products_tooltip[]=
			'<tr>'.
				'<td style="text-align:right;">'.$product['product_qty'].'</td>'.
				'<td>x</td>'.
				'<td nowrap>#'.$product['product_id'].': '.$product['product_name'].'</td>'.
				'<td>@</td>'.
				'<td style="text-align:right;">'.convertCurrency($product['product_offer'],$invoice_currency).'</td>'.
				'<td>='.$symbol.'</td>'.
				'<td style="text-align:right;">'. convertCurrency($product['product_offer']*$product['product_qty'],$invoice_currency).'</td>'.
				'<td>'.$invoice_currency.'</td>'.
			'</tr>';
	}
	$ship_offer=convertCurrency($invoice['ship_offer'],$invoice['invoice_currency']);
	if($invoice['ship_product_name']!='')
		$products_tooltip[]='<tr><td style="text-align:right;">1</td><td>x</td><td nowrap>Freight: '.$invoice['ship_product_name'].'</td><td>@</td> <td>'.$ship_offer.'</td><td>='.$symbol.'</td><td style="text-align:right;">'.$ship_offer.'</td><td>'.$invoice['invoice_currency'].'</td></tr>';
	$products_tooltip='<table>'.implode('',$products_tooltip).'</table>';
?>
		<tr<?= ($alt=!$alt)?' class="alt"':'' ?>>
			<td><?= date("d/m/y",$invoice['open_time']) ?></td>
			<td onmouseover="<?=htmlspecialchars('tt_in(' . json_encode($products_tooltip) . ')')?>" onmouseout="tt_out()"><?= invoiceFormatNumber($invoice['invoice_id'],$invoice['revision_id'],$invoice['site_id']) ?></td>
			<td>$<?= sprintf('%.2f',convertCurrency($invoice['invoice_offerus'],'USD')) ?></td>
			<td><?= $status_list[$invoice['invoice_action']] ?></td>
			<td><?= $paymenttype_list[$invoice['paymenttype_id']] ?></td>
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
	<br /><br />
</div>

<div style="background-color:white;text-align:center;padding:8px 8px 8px 8px;display:none;" id="sectionWeeklySpecialOrders_hideNote">Customer Orders can be viewed by Clicking "Customer Orders" above<br /></div>

<!-- /orders.tpl.php -->
