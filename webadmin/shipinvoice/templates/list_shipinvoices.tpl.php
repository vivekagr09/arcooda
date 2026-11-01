<!-- <?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->

<?php
$alt     = false;
$ord_img = '<img src="/webadmin/images/' . (('desc' == $order) ? 'desc' : 'asc') . '.png" />';

$sort_url           = $get_vars;
$sort_url['action'] = 'list';
$sort_url['order']  = ('desc' == $order) ? 'asc' : 'desc';
unset($sort_url['order_by']);
$sort_url = '.?' . http_build_query($sort_url);
$sort_url .= '&order_by=';

$edit_url           = $get_vars;
$edit_url['action'] = 'edit';
unset($edit_url['sinvoice_id']);
$edit_url = '.?' . http_build_query($edit_url);
$edit_url .= '&sinvoice_id=';

$view_url           = $get_vars;
$view_url['action'] = 'view';
unset($view_url['sinvoice_id']);
$view_url = '.?' . http_build_query($view_url);
$view_url .= '&sinvoice_id=';

$member_url           = $get_vars;
$member_url['action'] = 'edit';
unset($member_url['member_id']);
$member_url = '/webadmin/member/?' . http_build_query($member_url);
$member_url .= '&member_id=';

$unlock_url           = $get_vars;
$unlock_url['action'] = 'unlock';
unset($unlock_url['sinvoice_id']);
$unlock_url = '.?' . http_build_query($unlock_url);
$unlock_url .= '&sinvoice_id=';

$select_url           = $get_vars;
$select_url['action'] = 'select';
unset($select_url['sinvoice_id']);
$select_url = '.?' . http_build_query($select_url);
$select_url .= '&sinvoice_id=';

?>
<!-- list_invoices.tpl.php -->

<table class="wide spacetable" align="center">
	<tr>
		<th><a href="<?=$sort_url?>invoice_id">Invoice #</a><?=('invoice_id' == $order_by) ? $ord_img : ''?></th>
		<th><a href="<?=$sort_url?>open_date">Open Date</a><?=('open_date' == $order_by) ? $ord_img : ''?></th>
		<th><a href="<?=$sort_url?>bill_name">Customer</a><?=('bill_name' == $order_by) ? $ord_img : ''?></th>
		<th><a href="<?=$sort_url?>invoice_offerau">Invoice Value</a><?=('invoice_offerau' == $order_by) ? $ord_img : ''?></th>
		<th><a href="<?=$sort_url?>invoice_offerau">AUD Value</a><?=('invoice_offerau' == $order_by) ? $ord_img : ''?></th>
		<th>&nbsp;</th>
	</tr>

<?php
foreach ($sinvoice_list as $sinvoice_id) {
    $sinvoice          = shipInvoiceFromDatabase($sinvoice_id);
    $sinvoice_products = shipInvoiceProductsFromDatabase($sinvoice_id);

    $sinvoice_currency = $sinvoice['invoice_currency'];
    $class             = '';
    if ($alt = !$alt) {
        $class = ' class="alt"';
    }

    $products_tooltip = array();
    foreach ($sinvoice_products as $product) {
        $products_tooltip[] =
        '<tr>' .
        '<td style="text-align:right;">' . $product['product_qty'] . '</td>' .
        '<td>x</td>' .
        '<td>' . $product['product_name'] . '</td>' .
        '<td>@</td>' .
        '<td style="text-align:right;">' . convertCurrency($product['product_offer'], $sinvoice_currency) . '</td>' .
        '<td>=' . getCurrencySymbol($sinvoice_currency) . '</td>' .
        '<td style="text-align:right;">' . convertCurrency($product['product_offer'] * $product['product_qty'], $sinvoice_currency) . '</td>' .
            '<td>' . $sinvoice_currency . '</td>' .
            '</tr>';
    }

    $products_tooltip = '<table>' . implode('', $products_tooltip) . '</table>';
?>
	<tr<?=$class?>>
		<td style="text-align:center;" onmouseover="<?=htmlspecialchars('tt_in(' . json_encode($products_tooltip) . ')')?>" onmouseout="tt_out()"><?=shipinvoiceFormatNumber($sinvoice['sinvoice_id'], 0, $sinvoice['site_id'])?></td>
		<td style="text-align:center;"><?=date('d/m/Y', $sinvoice['open_time'])?></td>
		<td style="text-align:center;"><a href="<?=$member_url . $sinvoice['member_id']?>" target="_blank"><?=htmlspecialchars($sinvoice['bill_name'])?></a></td>
		<td style="text-align:center;" onmouseover="tt_in('Amount: $<?=$sinvoice['invoice_offer']?><br />Currency: <?=$sinvoice['invoice_currency']?><br />Method: <?=htmlspecialchars($sinvoice['paymenttype_id'])?>');" onmouseout="tt_out();">$<?=$sinvoice['invoice_offer'] . ' ' . $sinvoice['invoice_currency']?></td>
		<td style="text-align:center;">$<?=$sinvoice['invoice_offerau']?></td>
		<td style="text-align:center;">
<?php
    if ($popup_window) {
?>
            <a href="<?=htmlspecialchars($select_url . $sinvoice['invoice_id'])?>">Select</a> |
<?php
    }
?>
			<a href="<?=$edit_url . $sinvoice_id?>" target="_blank">Edit</a> |
			<a href="<?=$view_url . $sinvoice_id?>" target="_blank">View</a> |
			<a href="<?=$view_url . $sinvoice_id . '&format=pdf'?>" target="_blank">View PDF</a>
		</td>
	</tr>
<?php
}
?>
</table>

<?php
if (!$popup_window) {
?>
<br />

<!--
<span style="font-style:italic;">Notes:</span><br />
Rows marked in <span style="color:green;">green</span> have new messages<br />
Rows marked in <span style="color:red;">red</span> are locked by another user<br />
Rows marked in <span style="color:blue;">blue</span> are locked by you<br />
Rows marked in <span style="background-color:yellow;">yellow</span> have been idle for <?=$_GLOBALS['site_options']['WARNING_IDLE_DAYS']?> or more days<br />
Rows marked in <span style="background-color:orange;">orange</span> have special freight requests from the customer<br />-->
<?php
}
?>

<!-- /<?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->
