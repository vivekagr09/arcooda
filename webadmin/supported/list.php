<?php
include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/header.inc.php';

$page     = intval($_GET['page'] ?? 0);
$order    = ('asc' == ($_GET['order'] ?? '')) ? 'asc' : 'desc';
$order_by = [
    'id' => 'id',
][$_GET['order_by'] ?? ''] ?? 'id';

$eq         = $_GET['eq'] ?? [];
$lt         = $_GET['lt'] ?? null;
$gt         = $_GET['gt'] ?? null;
$item_query = supportedQuery($eq, $lt, $gt);
$item_list  = supportedSort($item_query, $order_by);
if ('desc' != $order) {
    $item_list = array_reverse($item_list);
}

//include __DIR__ . '/_navigate.tpl.php';

$per_page    = $_GLOBALS['staff']['supports_per_page'] ?? 100;
$count_total = count($item_list);

echo formatHTMLTitle('Products Supported');




$item_list     = array_chunk($item_list, $per_page);
$count_pages   = count($item_list);
$page          = max(0, min($count_pages - 1, $page));
$item_list     = $item_list[$page] ?? [];
$count_display = count($item_list);

if ($count_total > 0) {
    $page_url = $_GET;
    unset($page_url['page']);
    $page_url = '?' . http_build_query($page_url);

	$page_links = formatHTMLPageLinks($page_url . '&page=', $page, $count_display, $count_total, $per_page);
	
	$select_url           = $_GET;
    $select_url['action'] = 'select';
    unset($select_url['action']);
    $select_url = '.?' . http_build_query($select_url);
?>
<br />
<form accept-charset="utf-8" action="<?=htmlspecialchars($select_url)?>" method="post">
<?php
    include __DIR__ . '/_list.tpl.php';
?>
</form>
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
