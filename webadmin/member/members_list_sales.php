<?php
/**
 * @var mixed[] $posted
 * @var mixed[] $get_vars
 */
include_once __DIR__ . '/common.inc.php';

global $_GLOBALS;

include_once __DIR__ . '/header.inc.php';

$order = $posted['order'];
if ('asc' != $order) {
    $order = 'desc';
}

$page_id  = intval($posted['page_id']);
$order_by = strtolower($posted['order_by']);

$criteria = array('invoice_status' => array('sales', 'proforma', 'processed'));
if ('' != $posted['search_for']) {
    $criteria['any_text'] = explode(' ', $posted['search_for']);
}

if ('' != $posted['country']) {
    $criteria['bill_country'] = $posted['country'];
}

// now have to sort out the site_id array - mainly for the search part..
if (is_array($posted['site_id'])) {
    foreach ($posted['site_id'] as $key => $tmp) {
        if ('' == $tmp) {
            unset($posted['site_id'][$key]);
        }
    }
}

if ('' != $posted['site_id']) {
    if (!is_array($posted['site_id']) && '' != $posted['site_id']) {
        $criteria['site_id'] = explode('|', $posted['site_id']);
    } elseif (count($posted['site_id'])) {
        $criteria['site_id'] = $posted['site_id'];
    }

}

?>
<?php
include __DIR__ . '/templates/search_sales_members.tpl.php';

$items_per_page = 50;
if ($_GLOBALS['staff']['members_per_page'] > 0) {
    $items_per_page = $_GLOBALS['staff']['members_per_page'];
}

$member_list = memberProductSearch($criteria);
$member_list = memberSort($member_list, $order_by);
if ('desc' == $order) {
    $member_list = array_reverse($member_list);
}

$count_total   = count($member_list);
$member_list   = array_chunk($member_list, $items_per_page);
$count_pages   = count($member_list);
$page_id       = max(0, min($count_pages - 1, $page_id));
$member_list   = $member_list[$page_id];
$count_display = count($member_list);

$add_url           = $get_vars;
$add_url['action'] = 'add';
$add_url           = '.?' . http_build_query($add_url);

?>
<?=formatHTMLTitle('Members', '<i><a style="color:black;" href="' . htmlspecialchars($add_url) . '">Add New Member</a></i>')?>
<?php

if ($count_total > 0) {
    $page_url           = $get_vars;
    $page_url['action'] = 'sales_list';
    unset($page_url['page_id']);
    $page_url   = '?' . http_build_query($page_url);
    $page_links = formatHTMLPageLinks($page_url . '&page_id=', $page_id, $count_display, $count_total, $items_per_page);

    $select_url = $get_vars;
    unset($select_url['action']);
    $select_url = 'members_select.php?' . http_build_query($select_url);
    $sales_list = true;
?>
<br />
<form action="<?=htmlspecialchars($select_url)?>" method="post">
<?php
include __DIR__ . '/templates/list_members.tpl.php';
?>
</form>
<div style="text-align:center;background-color:#FAFAFA"><?=$page_links?></div>
<br />
<?php
if ($popup_window) {
?>
<input type="button" onclick="window.close();" value="Close Window" />
<?php
}
} else {
?>
No results were found
<?php
}

include_once __DIR__ . '/footer.inc.php';
