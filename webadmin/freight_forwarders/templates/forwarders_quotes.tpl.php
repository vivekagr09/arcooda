<!-- forwarders_quotes.tpl.php -->
<?php
moduleLoad('invoice'); //invoiceFormatNumber, invoiceGetValue
moduleLoad('member'); //memberGetValue

$links['sea'] = '<a href="index.php?action=add_quote&id=' . $forwarder_id . '&sea_air=sea" target="_blank">Add Sea Quote</a>';
$links['air'] = '<a href="index.php?action=add_quote&id=' . $forwarder_id . '&sea_air=air" target="_blank">Add Air Quote</a>';
$extra        = $links[$forwarder['type']];
if ('sea_air' == $forwarder['type']) {
    $extra = implode("&nbsp;&nbsp;", $links);
}

?>
<?=formatHTMLTitle('<a href="javascript:toggleSection(\'sectionQuotes\');">Quotes/Bookings</a>', $extra)?>
<div id="sectionQuotes">
<table class="wide" id="quote_table">
	<tr><th>ID</th><th>Date</th><th>Customer Name</th><th>Invoice #</th><th>Sea/Air</th><!--<th>Cost Price (AUD)</th>--><th>Sale Price (AUD)</th><th>Status</th><th>&nbsp;</th></tr>
<?php
$alt = false;
foreach ($forwarder_quotes as $id => $quote) {
    $invoice_id           = $quote['invoice_id'];
    $invoice_id_formatted = invoiceFormatNumber($invoice_id, 0, invoiceGetValue($invoice_id, 'site_id'));
    $inv_url              = "/webadmin/invoice/?action=edit&invoice_id=" . $invoice_id;
    $member_id            = $quote['customer_id'];
    $name                 = memberGetValue($quote['customer_id'], 'full_name');
    $mem_url              = "/webadmin/member/?action=edit&member_id=" . $member_id;
?>
	<tr <?=($alt = !$alt) ? 'class="alt"' : ''?> style="text-align:center">
		<td>ID#<?=$id?></td><td><?=date('d/m/Y', $quote['time_added']);?></td><td><a href="<?=$mem_url?>" target="_blank"><?=$name?></td>
		<td><a href="<?=$inv_url?>" target="_blank"><?=$invoice_id_formatted?></a></td><td><?=ucwords($quote['sea_air'])?></td>
		<!--<td>$<?=sprintf('%.2f', convertCurrency($quote['cost_price'], $quote['quote_currency'], 'AUD'))?></td>-->
		<td>$<?=sprintf('%.2f', convertCurrency($quote['sale_price'], $quote['quote_currency'], 'AUD'))?></td>
		<td><?=('y' == $quote['confirmed_flag']) ? 'Confirmed' : 'Quotation'?></td><td><a href="index.php?action=view_quote&quote_id=<?=$id?>" target="_blank" >View</a></td>
	</tr>
<?php
}
?>
</table>
</div>

<!-- /forwarders_contact.tpl.php -->