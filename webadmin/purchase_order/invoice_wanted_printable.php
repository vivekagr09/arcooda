<?php
include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/templates/printable_header.tpl.php';

$order = $posted['order'];
if ('asc' != $order) {
    $order = 'desc';
}

$page_id  = intval($posted['page_id']);
$order_by = strtolower($posted['order_by']);

//Build a search criteria array
$criteria = array();
if ('' != $posted['site_id']) {
    $criteria['site_id'] = explode('|', $posted['site_id']);
}

if ('' != $posted['search_for']) {
    $criteria['any_text'] = $posted['search_for'];
}

if ('' != $posted['status']) {
    $criteria['invoice_status'] = explode('|', $posted['status']);
}

if ('' != $posted['handler']) {
    $criteria['handler_id'] = $posted['handler'];
}

if ('' != $posted['invoice_action']) {
    $criteria['invoice_action'] = explode('|', $posted['invoice_action']);
}

if ('' != $posted['open_year']) {
    $criteria['open_year'] = mktime(0, 0, 0, 1, 1, date('Y', $posted['open_year']));
}

if ('' != $posted['open_month']) {
    $criteria['open_month'] = mktime(0, 0, 0, date('m', $posted['open_month']), 1, date('Y', $posted['open_month']));
}

if ('' != $posted['open_week']) {
    $criteria['open_week'] = mktime(0, 0, 0, date('m', $posted['open_week']), date('d', $posted['open_week']) + 1 - date('w', $posted['open_week']), date('Y', $posted['open_week']));
}

foreach (array('member_id', 'staff_id', 'site_id') as $key) {
    if ($posted[$key] > 0) {
        $criteria[$key] = intval($posted[$key]);
    }
}

$invoice_intersect = array();
$query             = $DB->query("SELECT `invoice_id` FROM `invoices` WHERE `products_locate_date` IS NULL");
while ($row = $DB->fetch_assoc($query)) {
    $invoice_intersect[] = $row['invoice_id'];
}

//Find and pagenate the invoices
$invoice_list = invoiceSearch($criteria);
$invoice_list = array_intersect($invoice_intersect, $invoice_list);
$invoice_list = invoiceSort($invoice_list, $order_by);
$product_list = array();
foreach ($invoice_list as $invoice_id) {
    $invoice_products = invoiceProductsFromDatabase($invoice_id);
    foreach ($invoice_products as $product) {
        if ('y' != $product['product_found']) {
            $product_list[] = $product;
        }
    }

}
if ('desc' == $order) {
    $product_list = array_reverse($product_list);
}

$count_total    = count($product_list);
$items_per_page = max(1, $count_total);

$site_list = invoiceGetOptionList('site_id');
if (array_key_exists($posted['site_id'], $site_list) && '' != $site_list[$posted['site_id']]) {
    $invoice_labels = $DB->query_first("SELECT * FROM `company_info` WHERE `company_id` = " . intval($posted['site_id']) . " LIMIT 1", MYSQLI_ASSOC);
} else {
    $invoice_labels = $DB->query_first("SELECT * FROM `company_info` WHERE `company_id` = " . $_GLOBALS["site_settings"]["site_id"] . " LIMIT 1", MYSQLI_ASSOC);
}

$temp = explode("\n", $invoice_labels['company_invoiceheader']);

?>
<div style="width:100%;text-align:left;">
	<span style="float:right;">
		Products Wanted Summary<br />
		Printed By <?php hecho($all_staff_list[$staff_id])?><br />
		Print Date: <?= date('d/m/y') ?>
	</span>
	<span style="font-size:23pt;"><?php hecho(array_shift($temp))?></span><br />
<?php
$temp = array_chunk($temp, 2);
foreach ($temp as $temp2) {
    echo implode(' ', $temp2) . "<br />\n";
}

?>
</div>
<?php

$count_pages   = 1;
$page_id       = 0;
$count_display = count($product_list);

if ($count_total > 0) {
    $page_url = $get_vars;
    unset($page_url['page_id']);
    $page_url = '?' . http_build_query($page_url);

    $page_links = formatHTMLPageLinks($page_url . '&page_id=', $page_id, $count_display, $count_total, $items_per_page);
?>
<br />
<?php include __DIR__ . '/templates/list_wanted_printable.tpl.php';?>
<br />
<?= $page_links ?><br />
<?php
} else {
?>
No results were found
<?php
}

include_once __DIR__ . '/templates/printable_footer.tpl.php';
