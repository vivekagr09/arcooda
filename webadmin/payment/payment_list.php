<?php
include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/header.inc.php';

$order = $posted['order'];
if ('asc' != $order) {
    $order = 'desc';
}

$page_id  = intval($posted['page_id']);
$order_by = $posted['order_by'];
$portal   = $posted['portal'];

//Build a search criteria array
$criteria = array();
if ('' != $posted['search_for']) {
    $criteria['any_text'] = $posted['search_for'];
}

if ('' != $posted['Card']) {
    $criteria['Card'] = $posted['Card'];
}

if ('' != $posted['result_approval']) {
    $criteria['result_approval'] = $posted['result_approval'];
}

if ('' != $posted['merchant']) {
    $criteria['Merchant'] = $posted['merchant'];
}

//Find and pagenate the invoices
$payment_list = paymentSearch($criteria, $portal);
$payment_list = paymentSort($payment_list, $order_by, SORT_ASC, $portal);
if ('desc' == $order) {
    $payment_list = array_reverse($payment_list);
}

$items_per_page = 50;
$count_total    = count($payment_list);

?>
<h1>Payments</h1>
<?=formatHTMLTitle('Payments')?>
<?php

$payment_list  = array_chunk($payment_list, $items_per_page);
$count_pages   = count($payment_list);
$page_id       = max(0, min($count_pages - 1, $page_id));
$payment_list  = $payment_list[$page_id];
$count_display = count($payment_list);

if ($count_total > 0) {
    $sort_url           = $get_vars;
    $sort_url['action'] = 'list';
    unset($sort_url['order_by']);
    unset($sort_url['order']);
    unset($sort_url['page_id']);
    $sort_url = '.?' . http_build_query($sort_url);

    $view_url           = $get_vars;
    $view_url['action'] = 'view';
    unset($view_url['payment_id']);
    $view_url = '.?' . http_build_query($view_url);

    $page_url           = $get_vars;
    $page_url['action'] = 'list';
    unset($page_url['page_id']);
    $page_url = '.?' . http_build_query($page_url);

    $page_links = formatHTMLPageLinks($page_url . '&page_id=', $page_id, $count_display, $count_total, $items_per_page);
?>
<table class="wide" align="center">
	<tr>
		<th><a href="<?=$sort_url?>&order_by=MerchTxnRef&order=<?=('MerchTxnRef' == $order_by && 'asc' == $order) ? 'desc' : 'asc'?>">MerchTxnRef</a></th>
		<th><a href="<?=$sort_url?>&order_by=TransactionNo&order=<?=('TransactionNo' == $order_by && 'asc' == $order) ? 'desc' : 'asc'?>">TransactionNo</a></th>
		<th><a href="<?=$sort_url?>&order_by=ReceiptNo&order=<?=('ReceiptNo' == $order_by && 'asc' == $order) ? 'desc' : 'asc'?>">ReceiptNo</a></th>
		<th><a href="<?=$sort_url?>&order_by=Amount&order=<?=('Amount' == $order_by && 'asc' == $order) ? 'desc' : 'asc'?>">Amount</a></th>
		<th><a href="<?=$sort_url?>&order_by=Card&order=<?=('Card' == $order_by && 'asc' == $order) ? 'desc' : 'asc'?>">Card</a></th>
		<th><a href="<?=$sort_url?>&order_by=TxnResponseCode&order=<?=('TxnResponseCode' == $order_by && 'asc' == $order) ? 'desc' : 'asc'?>">Result</a></th>
		<th><a href="<?=$sort_url?>&order_by=AuthorizeId&order=<?=('AuthorizeId' == $order_by && 'asc' == $order) ? 'desc' : 'asc'?>">AuthorizeId</a></th>
		<th><a href="<?=$sort_url?>&order_by=BatchNo&order=<?=('BatchNo' == $order_by && 'asc' == $order) ? 'desc' : 'asc'?>">BatchNo</a></th>
		<th></th>
	</tr>
<?php
foreach ($payment_list as $payment_id) {
        $payment   = paymentFromDatabase($payment_id, $portal);
        $cardtypes = array('MC' => 'mastercard_48.png', 'VC' => 'visacard_48.png', 'AE' => 'amex_48.gif');
?>
	<tr<?=($alt = !$alt) ? '' : ' class="alt"'?> style="height:32px;">
		<td style="text-align:center;"><?=htmlspecialchars($payment['MerchTxnRef'])?></td>
		<td style="text-align:center;"><?=htmlspecialchars($payment['TransactionNo'])?></td>
		<td style="text-align:center;"><?=htmlspecialchars($payment['ReceiptNo'])?></td>
		<td style="text-align:center;"><?=bcmul($payment['Amount'], '0.01', 2)?></td>
		<td style="text-align:center;"><img src="/webadmin/images/payment/<?=$cardtypes[$payment['Card']]?>" /></td>
		<td style="text-align:center;"><img src="images/<?=$payment['result_approval']?>.png" /></td>
		<td style="text-align:center;"><?=htmlspecialchars($payment['AuthorizeId'])?></td>
		<td style="text-align:center;"><?=htmlspecialchars($payment['BatchNo'])?></td>
		<td style="text-align:center;">
			<a href="<?=$view_url?>&payment_id=<?=$payment_id?>" target="_blank">View</a>
		</td>
	</tr>
<?php
}
?>
</table>
<p align="center"><?=$page_links?></p>
<?php
} else {
?>
No results were found<br />
<?php
}

include_once __DIR__ . '/footer.inc.php';
