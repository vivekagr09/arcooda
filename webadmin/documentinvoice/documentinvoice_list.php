<?php
include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/header.inc.php';

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

foreach (array('member_id', 'staff_id', 'site_id') as $key) {
    if ($posted[$key] > 0) {
        $criteria[$key] = intval($posted[$key]);
    }
}

$site_id   = intval($posted['site_id']);
$status_id = $posted['status'];
/*
$search=$posted['search_for'];
if($search!='')
include __DIR__ . '/templates/search_invoices.tpl.php';
 */

//Find and pagenate the invoices
$sinvoice_list = documentinvoiceSearch($criteria);
$sinvoice_list = documentinvoiceSort($sinvoice_list, $order_by);
if ('desc' == $order) {
    $sinvoice_list = array_reverse($sinvoice_list);
}

$items_per_page = 50;
$count_total    = count($sinvoice_list);

$sinvoice_list = array_chunk($sinvoice_list, $items_per_page);
$count_pages   = count($sinvoice_list);
$page_id       = max(0, min($count_pages - 1, $page_id));
$sinvoice_list = $sinvoice_list[$page_id];
$count_display = count($sinvoice_list);

if ($count_total > 0) {
    $page_url = $get_vars;
    unset($page_url['page_id']);
    $page_url = '?' . http_build_query($page_url);

    $page_links = formatHTMLPageLinks($page_url . '&page_id=', $page_id, $count_display, $count_total, $items_per_page);
?>
<br />
<?php
include __DIR__ . '/templates/list_documentinvoices.tpl.php';
?>
<br />
<?=$page_links?><br />
<br />
<?php
} else {
?>
No results were found
<?php
}

include_once __DIR__ . '/footer.inc.php';
