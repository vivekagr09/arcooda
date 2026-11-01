<?php
/**
 * @var string $asset_type
 * @var string $section_plural
 */
include_once __DIR__ . '/common.inc.php';

global $_GLOBALS;
global $DB;

moduleLoad('staff');

include_once __DIR__ . '/header.inc.php';

$action = 'list';

$order = $_GET['order'];
if ('asc' != $order) {
    $order = 'desc';
}

$page_id  = intval($_GET['page_id'] ?? 0);
$order_by = strtolower($_GET['order_by']);

//Build a search criteria array
$criteria = [];

if ('' != $_GET['search_for']) {
    $criteria['any_text'] = $_GET['search_for'];
}

foreach (array('staff_id', 'site_id') as $k) {
    if (array_key_exists($k, $_GET)) {
        $criteria[$k] = $_GET[$k];
    }
}

//Add a search box
$status_id = $_GET['status'];
$search    = $_GET['search_for'];
include __DIR__ . '/templates/search_assets.tpl.php';

$all_staff_list = staffGetOptionList('all_staff'); //TODO: assetGetOptionList('staff_id');

?>
<br />

<?=formatHTMLTitle($section_plural)?>
<?php

//Find and pagenate the assets
$asset_list = assetSearch($asset_type, $criteria);
//$asset_list=assetSort($asset_type,$asset_list,$order_by);
if ('desc' == $order) {
    $asset_list = array_reverse($asset_list);
}

$items_per_page = 50;
$count_total    = count($asset_list);

if ($count_total > 0) {
    include __DIR__ . '/templates/navigate_assets.tpl.php';
}

$asset_list    = array_chunk($asset_list, $items_per_page);
$count_pages   = count($asset_list);
$page_id       = max(0, min($count_pages - 1, $page_id));
$asset_list    = $asset_list[$page_id];
$count_display = count($asset_list);

if ($count_total > 0) {
    $page_url = $_GET;
    unset($page_url['page_id']);
    $page_url = '?' . http_build_query($page_url);

    $page_links = formatHTMLPageLinks($page_url . '&page_id=', $page_id, $count_display, $count_total, $items_per_page);
?>
<?=$page_links?><br />
<br />
<?php
include __DIR__ . '/templates/list_assets.tpl.php';
?>
<br />
<?=$page_links?><br />
<?php
} else {
?>
No results were found
<?php
}

include_once __DIR__ . '/footer.inc.php';
