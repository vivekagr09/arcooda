<?php
include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/header.inc.php';

$order_by = 'id';
$order = 'asc';
$review_id = 0;
$page_id = intval($posted['page_id'] ?? 0);;

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
if (array_key_exists('id', $_REQUEST)) {
	$id = intval($_REQUEST['id']);
}

//Build a search criteria array
$criteria              = array();

foreach (reviewGetOptionList('all_fields') as $k => $v) {
	if (array_key_exists($k, $_REQUEST)) {
		if (is_array($_REQUEST[$k])) {
			$criteria[$k] = $_REQUEST[$k];
		} else {
			$criteria[$k] = preg_split('/[^0-9]+/', $_REQUEST[$k]);
		}
	}
}
foreach (array(
	'search_for' => 'any_text',
) as $k => $v) {
	if (array_key_exists($k, $_REQUEST)) {
		if (is_array($_REQUEST[$k])) {
			$criteria[$v] = $_REQUEST[$k];
		} else {
			$criteria[$v] = preg_split('/\\s+/', $_REQUEST[$k]);
		}
	}
}


/*
$search_for = '';
if (array_key_exists('search_for', $_REQUEST)) {
	$search_for = $_REQUEST['search_for'];
}
?>
<div style="text-align:left;">
	<a href="#" onclick="$('#sectionSearch').toggle('slow');return false" class="toggleBtn" style="cursor:pointer;text-decoration:underline;"><img name="imEx" src="/webadmin/images/triDown.gif" alt="Expand/Collapse All" border="0" height="16" width="16" /> <b>Search</b></a>
	<br /><br />
	<div id="sectionSearch" class="child"<?=('' == $search_for) ? ' style="display:none;"' : ''?>>
<?php
include __DIR__ . '/search.tpl.php';
?>
	</div>
</div>
<?php
*/


?>


<?=formatHTMLTitle('Product Review ')?>
<?php
//Find and pagenate the reviews
// $item_list = reviewearch($criteria);
$item_list = invoiceProductReview();
// echo "<pre>";
// print_r($item_list); die;
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
	include __DIR__ . '/templates/list.tpl.php';
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
