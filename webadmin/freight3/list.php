<?php
include_once __DIR__ . '/common.inc.php';
include_once __DIR__ . '/header.inc.php';
$search_for = (isset($posted['search_for'])) ? $posted['search_for'] : '';
$page_id = intval($posted['page_id']);
$order_by = (isset($posted['order_by'])) ? $posted['order_by'] : 'id';
$order = ($posted['order'] != 'asc') ? 'desc' : 'asc';
if (isset($posted['search_for'])) include 'search.tpl.php';

echo formatHTMLTitle("Freight Service List");

$criteria = array();
if ($posted['search_for'] != '') $criteria['any_text'] = explode(' ', $posted['search_for']);
if ($posted['active'] != '') $criteria['active'] = explode('|', $posted['active']);

$item_list = $section_handler->search($criteria);
$item_list = $section_handler->sort($item_list, $order_by);
if ($order == 'desc') $item_list = array_reverse($item_list);

$items_per_page = 50;
if ($_GLOBALS['staff']['items_per_page'] > 0) $items_per_page = $_GLOBALS['staff']['items_per_page'];
$count_total = count($item_list);
$item_list = array_chunk($item_list, $items_per_page);
$count_pages = count($item_list);
$page_id = max(0, min($count_pages - 1, $page_id));
$item_list = $item_list[$page_id];
$count_display = count($item_list);

if ($count_total > 0)
{
	$page_url = $get_vars;
	$page_url['site_id'] = $site_id;
	unset($page_url['page_id']);
	$page_url = '?' . http_build_query($page_url) . '&page_id=';

	$page_links = formatHTMLPageLinks($page_url, $page_id, $count_display, $count_total, $items_per_page);

	include 'list.tpl.php';

	echo $page_links;
}
else echo 'No results were found';

include_once __DIR__ . '/footer.inc.php';
?>