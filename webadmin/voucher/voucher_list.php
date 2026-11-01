<?php
include_once 'voucher_common.inc.php';
$popup_window = (isset($posted['return_message']));
include_once 'templates/voucher_header.tpl.php';

$search_for = (isset($posted['search_for'])) ? $posted['search_for'] : '';
$page       = intval($posted['page']);
$order_by   = (isset($posted['order_by'])) ? $posted['order_by'] : 'voucher_id';
$order      = ('asc' != $posted['order']) ? 'desc' : 'asc';
if (isset($posted['search_for'])) {
    include 'templates/search_voucher.tpl.php';
}

$site_id    = intval($posted['site_id']);
$search_for = (isset($posted['search_for'])) ? $posted['search_for'] : '';
$tmp        = explode(' ', $posted['search_for']);
foreach ($tmp as $key => $value) {
    if (is_numeric($value)) {
        $tmp[$key] = intval($value);
    }
}

$posted['search_for'] = implode(' ', $tmp);

$criteria = array();
if ('' != $posted['site_id']) {
    $criteria['site_id'] = $posted['site_id'];
}

if ('' != $posted['member_id']) {
    $criteria['member_id'] = $posted['member_id'];
}

if ('' != $posted['search_for']) {
    $criteria['any_text'] = explode(' ', $posted['search_for']);
}

if ('' != $posted['status']) {
    $criteria['status'] = explode('|', $posted['status']);
}

if ('' != $posted['return_message']) {
    $criteria['status']  = 'open';
    $criteria['expired'] = 'n';
}

echo formatHTMLTitle('Voucher List');

$voucher_list = voucherSearch($criteria);
$voucher_list = voucherSort($voucher_list, $order_by);
if ('desc' == $order) {
    $voucher_list = array_reverse($voucher_list);
}

$count_total = count($voucher_list);

$yesno   = array('y' => 'Yes', 'n' => 'No');
$default = array('0' => ' -- Please select an option -- ');
if ($count_total > 0) {
    $items_per_page = 50;
    $voucher_list   = array_chunk($voucher_list, $items_per_page);
    $count_pages    = count($voucher_list);
    $page           = max(0, min($count_pages - 1, $page));
    $voucher_list   = $voucher_list[$page];
    $count_display  = count($voucher_list);

    $page_url            = $get_vars;
    $page_url['site_id'] = $site_id;
    unset($page_url['page']);
    $page_url = '?' . http_build_query($page_url);

    $page_links = formatHTMLPageLinks($page_url . '&page=', $page, $count_display, $count_total, $items_per_page);
    $status     = array('y' => 'Yes', 'n' => 'No');
    include 'templates/list_voucher.tpl.php';
    echo $page_links;
} else {
    echo 'No results were found';
}

echo '<br /><br />';
include_once 'templates/voucher_footer.tpl.php';
