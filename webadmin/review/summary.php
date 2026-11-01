<?php
include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/header.inc.php';

$order_by = 'id';
$order = 'asc';
$review_id = 0;
$page_id = 0;

if (array_key_exists('page_id', $_REQUEST)) {
	$page_id = intval($_REQUEST['page_id']);
}

if (array_key_exists('order_by', $_REQUEST)) {
	$order_by = (in_array($_REQUEST['order_by'], array(
		'id',
		'created_at',
		'status',
	))) ? $_REQUEST['order_by'] : 'id';
}
if (array_key_exists('order', $_REQUEST)) {
	$order = ('desc' != $_REQUEST['order']) ? 'asc' : 'desc';
}
if (array_key_exists('id', $$posted)) {
	$id = intval($posted['id']);
}

//Build a search criteria array
$criteria              = array();

foreach (reviewGetOptionList('all_fields') as $k => $v) {
	if (array_key_exists($k, $posted)) {
		if (is_array($posted[$k])) {
			$criteria[$k] = $posted[$k];
		} else {
			$criteria[$k] = preg_split('/[^0-9]+/', $posted[$k]);
		}
	}
}
foreach (array(
	'search_for' => 'any_text',
	'category' => 'pr.section',
	'rating' => 'review.reviews',
	'ranking' => 'ranking',
) as $k => $v) {
	if (array_key_exists($k, $posted)) {
		$criteria[$v] = $posted[$k];
	}
}

$search_for = '';
if (array_key_exists('search_for', $posted)) {
	$search_for = $posted['search_for'];
}
?>
<div style="text-align:left;">
	<a href="#" onclick="$('#reviewSearch').toggle('slow');return false" class="toggleBtn" style="cursor:pointer;text-decoration:underline;"><img name="imEx" src="/webadmin/images/triDown.gif" alt="Expand/Collapse All" border="0" height="16" width="16" /> <b>Search</b></a>
	<br /><br />
	<div id="reviewSearch" class="child"<?=('' == $search_for) ? ' style="display:none;"' : ''?>>
<?php
include __DIR__ . '/templates/search.tpl.php';
?>
	</div>
</div>
<?php

?>


<?=formatHTMLTitle('Customer Product Review ')?>
<?php
//Find and pagenate the reviews
// $item_list = reviewearch($criteria);
$item_list = getProductsReview($criteria);

// $item_list = reviewSort($item_list, $order_by);
if ('desc' == $order) {
    $item_list = array_reverse($item_list);
}

$items_per_page = 50;
$count_total    = count($item_list);

if ($count_total > 0) {
    $item_list     = array_chunk($item_list, $items_per_page);
    $count_pages   = count($item_list);
    $page_id       = max(0, min($count_pages - 1, $page_id));
    $item_list     = $item_list[$page_id];
    $count_display = count($item_list);

    $page_url = $get_vars;
    unset($page_url['page_id']);
    $page_url = 'index.php?' . http_build_query($page_url) . '&page_id=';

    $page_links = formatHTMLPageLinks($page_url, $page_id, $count_display, $count_total, $items_per_page);

    // $select_url = $get_vars;
    // unset($select_url['action']);
    // $select_url = 'select.php?' . http_build_query($select_url);
?>
<br />
<?php
	include __DIR__ . '/templates/summary_list.tpl.php';
?>
<br />
<?=$page_links?>
<br />
<?php
} else {
?>
No results were found
<?php
}

if ($popup_window) {
?>
<br /><br /><input type="button" onclick="window.close();" value="Close Window" /><br /><br />&nbsp;
<?php
}

include_once __DIR__ . '/footer.inc.php';
