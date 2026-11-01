<?php
include_once 'currency_common.inc.php';
include_once 'templates/currency_header.tpl.php';

$order = $posted['order'];
if ('desc' != $order) {
    $order = 'asc';
}

$page_id  = intval($posted['page_id']);
$order_by = strtolower($posted['order_by']);

//Build a search criteria array
$criteria = array();
if ('' != $posted['search_for']) {
    $criteria['any_text'] = $posted['search_for'];
}

if ('' != $posted['code']) {
    $criteria['code'] = $posted['code'];
}

if ('' != $posted['code_first_letter']) {
    $criteria['code_first_letter'] = substr($posted['code_first_letter'], 0, 1);
}

if ('' != $posted['name_first_letter']) {
    $criteria['name_first_letter'] = substr($posted['name_first_letter'], 0, 1);
}

$amount_from   = floatval($posted['amount_from']);
$currency_from = $posted['currency_from'];
$currency_to   = $posted['currency_to'];
include 'templates/currency_search.tpl.php';

$page_links2 = $criteria;
unset($page_links2['name_first_letter']);
$page_links2 = array_keys(currencyChunk(currencySearch($page_links2), 'name_first_letter'));
asort($page_links2);
$page_url = $get_vars;
unset($page_url['page_id']);
unset($page_url['name_first_letter']);
$page_url = '?' . http_build_query($page_url) . '&name_first_letter=';
foreach ($page_links2 as $key => $value) {
    $page_links2[$key] = '<a href="' . $page_url . $value . '">' . strtoupper($value) . '</a>';
}

//Find and pagenate the currencys
$currency_list = currencySearch($criteria);
$currency_list = currencySort($currency_list, $order_by);
if ('desc' == $order) {
    $currency_list = array_reverse($currency_list);
}

$items_per_page = 50;
$count_total    = count($currency_list);

?>
<?=formatHTMLTitle('Currencies')?>
<?php

$currency_list = array_chunk($currency_list, $items_per_page);
$count_pages   = count($currency_list);
$page_id       = max(0, min($count_pages - 1, $page_id));
$currency_list = $currency_list[$page_id] ?? [];
$count_display = count($currency_list);

if ($count_total > 0) {
    $page_url = $get_vars;
    unset($page_url['page_id']);
    $page_url = '?' . http_build_query($page_url);

    $page_links = formatHTMLPageLinks($page_url . '&page_id=', $page_id, $count_display, $count_total, $items_per_page);
?>
<br />
<?php
include 'templates/currency_list.tpl.php';
?>
<br />
<?=implode(' ', $page_links2)?><br />
<br />
<?=$page_links?><br />
<br />
<?php
if (!$popup_window) {
?>
<input type="button" onclick="window.open('currency_list_printable.php?<?=http_build_query($get_vars)?>');" value="Print Currency List" />
<?php
} else {
?>
<input type="button" onclick="window.close();" value="Close Window" />
<?php
}
} else {
?>
No results were found
<?php
}

include_once 'templates/currency_footer.tpl.php';
