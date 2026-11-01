<?php
/**
 * @var Serial_Handler $section_handler
 * @var string         $section_name
 * @var mixed[]        $get_vars
 */
include_once __DIR__ . '/common.inc.php';

global $_GLOBALS;

include_once __DIR__ . '/header.inc.php';

$action   = 'list';
$order    = $_REQUEST['order'] ?? 'desc';
$page_id  = intval($_REQUEST['page_id'] ?? 0);
$order_by = strtolower($_REQUEST['order_by'] ?? 'serial_id');

if ('desc' != $order) {
    $order = 'asc';
}

//Build a search criteria array
$criteria = $_GLOBALS['criteria_options'][$section_name] ?? [];
foreach ($section_handler->getOptionList('all_fields') as $k => $v) {
    if (array_key_exists($k, $_REQUEST)) {
        if (is_array($_REQUEST[$k])) {
            $criteria[$k] = $_REQUEST[$k];
        } elseif ('' !== $_REQUEST[$k]) {
            $criteria[$k] = preg_split('/[^0-9a-z]+/i', $_REQUEST[$k]);
        }

        foreach ($criteria[$k] as $k2 => $v2) {
            if (empty($v2)) {
                unset($criteria[$k][$k2]);
            }
        }

    }
}

unset($criteria['id']);

if (array_key_exists('search_for', $_REQUEST) && '' != $_REQUEST['search_for']) {
    $criteria['any_text'] = explode('|', $_REQUEST['search_for']);
}

if (array_key_exists('subset_list', $_REQUEST) && '' != $_REQUEST['subset_list']) {
    $criteria['id'] = explode('|', $_REQUEST['subset_list']);
}

$search_for = $_REQUEST['search_for'] ?? '';

?>

<div style="text-align:left">
	<a href="#" onclick="toggleSection('sectionSearch');return false"><img name="imEx" src="/webadmin/images/triDown.gif" alt="Expand/Collapse All" height="16" width="16" /> <b><?=htmlspecialchars(_('Search'))?></b></a>
	<br /><br />
	<div id="sectionSearch" class="child"<?=('' == $search_for) ? ' style="display:none"' : ''?>>
<?php
include __DIR__ . '/search.tpl.php';
?>
	</div>
</div>
<?php

//Find and pagenate the items
$item_list = $section_handler->search($criteria);
unset($criteria['product_id']);
$item_list_product = $section_handler->search($criteria);
$item_list = $section_handler->sort($item_list, $order_by);
if ('desc' == $order) {
    $item_list = array_reverse($item_list);
}
if (count($item_list) > 0) {
    $list_by1 = 'product_id';
    $list_by2 = '';
?>
<div style="text-align:left;">
    <a href="javascript:void(toggleSection('sectionViewByCountry'))"><img name="imEx" src="/webadmin/images/triDown.gif" alt="Expand/Collapse All" border="0" height="16" width="16" /> <b>Products</b></a>
    <br /><br />
    <div id="sectionViewByCountry" class="child" style="display:none;">
<?php
include __DIR__ . '/navigate.tpl.php';
?>
    </div>
</div>
<?php
}
$items_per_page = $_GLOBALS['staff']['per_page'] ?? 50;

$count_total = count($item_list);

if ($count_total > 0) {
    $item_list   = array_chunk($item_list, $items_per_page, true);
    $count_pages = count($item_list);
    $page_id     = max(0, min($count_pages - 1, $page_id));
    $item_list   = $item_list[$page_id] ?? [];

    $count_display = count($item_list);

    $page_url           = $get_vars;
    $page_url['action'] = 'list';
    unset($page_url['page_id']);
    $page_url = '.?' . http_build_query($page_url) . '&page_id=';

    $page_links = formatHTMLPageLinks($page_url, $page_id, $count_display, $count_total, $items_per_page);

    $select_url = $get_vars;
    unset($select_url['action']);
    $select_url = 'select.php?' . http_build_query($select_url);
?>
<div>
	<form action="<?=htmlspecialchars($select_url)?>" method="post">
<?php
    include __DIR__ . '/list.tpl.php';
?>
	</form>

	<div style="text-align:center"><?=$page_links?></div>
	<br /><br />

	<!-- <div style="text-align:center">
		<input onclick="window.open('.?action=print')" type="button" value="Print List" />
		<input onclick="window.open('.?action=export')" type="button" value="Export List" />
	</div> -->
	<br /><br />
</div>
<?php
} else {
?>
<?=htmlspecialchars(_('No results were found.'))?>
<?php
}

include_once __DIR__ . '/footer.inc.php';
