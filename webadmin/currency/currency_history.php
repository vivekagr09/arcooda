<?php
include_once 'currency_common.inc.php';
include_once 'templates/currency_header.tpl.php';

$order = $posted['order'];
if ('asc' != $order) {
    $order = 'desc';
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

$amount_from   = floatval($posted['amount_from']);
$currency_from = $posted['currency_from'];
$currency_to   = $posted['currency_to'];
include 'templates/currency_search.tpl.php';

//Find the affected dates
if ('USD' == $currency_from) {
    $from_history = array(array('usto' => 1.0));
} else {
    $from_history = currencyHistoryFromDatabase($currency_from);
}

if ('USD' == $currency_to) {
    $to_history = array(array('usto' => 1.0));
} else {
    $to_history = currencyHistoryFromDatabase($currency_to);
}

$date_list = array();
foreach ($from_history as $key => $row) {
    $date_list[$row['change_time']]['from'] = $key;
}

foreach ($to_history as $key => $row) {
    $date_list[$row['change_time']]['to'] = $key;
}

ksort($date_list);
$from = -1;
$to   = -1;
foreach ($date_list as $key => $row) {
    if (!isset($row['from'])) {
        $date_list[$key]['from'] = $from;
    } else {
        $from = $row['from'];
    }

    if (!isset($row['to'])) {
        $date_list[$key]['to'] = $to;
    } else {
        $to = $row['to'];
    }

}
foreach ($date_list as $key => $row) {
    $date_list[$key]['from'] = $from_history[$row['from']];
    $date_list[$key]['to']   = $to_history[$row['to']];
}
if ('desc' == $order) {
    $date_list = array_reverse($date_list, true);
}

$items_per_page = 20;
$count_total    = count($date_list);

?>
<?=formatHTMLTitle(htmlspecialchars('Currency History - ' . $currency_from . ' to ' . $currency_to))?>

<?php
$graph_url           = $get_vars;
$graph_url['action'] = 'graph';
$graph_url           = 'index.php?' . http_build_query($graph_url);
?>
<div style="text-align:center;"><img src="<?=$graph_url?>" alt="" /></div>

<?php

$date_list     = array_chunk($date_list, $items_per_page, true);
$count_pages   = count($date_list);
$page_id       = max(0, min($count_pages - 1, $page_id));
$date_list     = $date_list[$page_id];
$count_display = count($date_list);

if ($count_total > 0) {
    $page_url = $get_vars;
    unset($page_url['page_id']);
    $page_url = '?' . http_build_query($page_url);

    $page_links = formatHTMLPageLinks($page_url . '&page_id=', $page_id, $count_display, $count_total, $items_per_page);
?>
<br />
<?php
include 'templates/currency_history.tpl.php';
?>
<br />
<?=$page_links?><br />
<br />
<?php
if (!$popup_window) {
?>
<input type="button" onclick="window.open('currency_history_printable.php?<?=http_build_query($get_vars)?>');" value="Print Currency List" />
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
?>