<?php
include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/header.inc.php';

$page    = intval($_GET['page'] ?? 0);
$order   = ('asc' == ($_GET['order'] ?? '')) ? 'asc' : 'desc';
$sort_by = [
    'id' => 'id',
][$_GET['sort_by'] ?? ''] ?? 'id';
$eq = [];
if(isset($_GET['member_type']) && $_GET['member_type'] != '0' ){
  $eq['member_type'] =  $_GET['member_type']; 
}
if(isset($_GET['category']) && $_GET['category'] != '0' ){
  $eq['category'] =  $_GET['category']; 
}
if(isset($_GET['country']) && count($_GET['country']) > 0 ){

  $eq['country'] =  explode(',', $_GET['country']); 
}
if(isset($_GET['active'])){

  $eq['active'] =  explode(',', $_GET['active']); 
}
//$eq         = $_GET['eq'] ?? []; //array_merge($_GET['eq'] ?? [], $_GLOBALS['criteria_options']['distributor'] ?? []);
$lt         = $_GET['lt'] ?? null;
$gt         = $_GET['gt'] ?? null;
$item_query = wareHouseQuery($eq, $lt, $gt);
$item_list  = wareHouseSort($item_query, $sort_by);
if ('desc' != $order) {
    $item_list = array_reverse($item_list);
}

//include __DIR__ . '/_navigate.tpl.php';

$per_page    = $_GLOBALS['staff']['distributors_per_page'] ?? 100;
$count_total = count($item_list);

echo formatHTMLTitle('Inventory Warehouse');




$item_list     = array_chunk($item_list, $per_page);
$count_pages   = count($item_list);
$page          = max(0, min($count_pages - 1, $page));
$item_list     = $item_list[$page] ?? [];
$count_display = count($item_list);

if ($count_total > 0) {
    $page_url = $get_vars;
    unset($page_url['page']);
    $page_url = '?' . http_build_query($page_url);

	$page_links = formatHTMLPageLinks($page_url . '&page=', $page, $count_display, $count_total, $per_page);
	
	$select_url           = $get_vars;
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
<?= $page_links ?><br />
<br />
<?php
} else {
?>
No results were found
<?php
}

include_once __DIR__ . '/footer.inc.php';
