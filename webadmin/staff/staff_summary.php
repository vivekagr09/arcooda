<?php
/**
 * @var mixed[]  $posted
 * @var mixed[]  $get_vars
 * @var string[] $active_staff
 * @var string[] $inactive_staff
 */
include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/templates/staff_header.tpl.php';

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

if ('' != $posted['status']) {
    $criteria['active'] = $posted['status'];
}

foreach (array('staff_id') as $key) {
    if ($posted[$key] > 0) {
        $criteria[$key] = intval($posted[$key]);
    }
}

//Add a search box
//$search=$posted['search_for'];
//include __DIR__ . '/templates/search_staff.tpl.php';

//Find and pagenate the staff
$staff_list = staffSearch($criteria);
if ('' != $order_by) {
    $staff_list = staffSort($staff_list, $order_by);
    if ('desc' == $order) {
        $staff_list = array_reverse($staff_list);
    }
} else {
    $staff_list = array_intersect(array_merge(array_keys($active_staff), array_keys($inactive_staff)), $staff_list);
}

$items_per_page = 30;
$count_total    = count($staff_list);

?>

<?=formatHTMLTitle('Staff')?>

<?php

$staff_list    = array_chunk($staff_list, $items_per_page);
$count_pages   = count($staff_list);
$page          = max(0, min($count_pages - 1, $page));
$staff_list    = $staff_list[$page_id];
$count_display = count($staff_list);

if ($count_total > 0) {
    $page_url = $get_vars;
    unset($page_url['page_id']);
    $page_url['action'] = 'staff_summary';
    $page_url           = '.?' . http_build_query($page_url) . '&page_id=';

    $page_links = formatHTMLPageLinks($page_url, $page_id, $count_display, $count_total, $items_per_page);
?>
<br />
<?php
include __DIR__ . '/templates/list_staff_summary.tpl.php';
?>
<br />
<?=$page_links?><br />
<?php
} else {
?>
No results were found
<?php
}

include_once __DIR__ . '/templates/staff_footer.tpl.php';
