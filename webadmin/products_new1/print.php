<?php
include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/header.inc.php';

$order = $_REQUEST['order'];
if ('asc' != $order) {
    $order = 'desc';
}

$page_id  = intval($_REQUEST['page_id']);
$order_by = strtolower($_REQUEST['order_by']);

$criteria = array();
if (array_key_exists('search_for', $_REQUEST)) {
    foreach (array('search_for') as $k => $v) {
        if (array_key_exists($k, $_REQUEST) && !empty($_REQUEST[$k])) {
            $criteria['any_text'] = (is_array($_REQUEST[$k])) ? $_REQUEST[$k] : preg_split('/[^a-zA-Z0-9]+/', $_REQUEST[$k]);
        }
    }
}
foreach (productGetOptionList('all_fields') as $k => $v) {
    if (array_key_exists($k, $_REQUEST) && !empty($_REQUEST[$k])) {
        $criteria[$k] = (is_array($_REQUEST[$k])) ? $_REQUEST[$k] : preg_split('/[^a-zA-Z0-9]+/', $_REQUEST[$k]);
    }
}
foreach (productGetOptionList('joined_fields') as $join => $columns) {
    foreach ($columns as $k => $v) {
        if (array_key_exists($k, $_REQUEST) && !empty($_REQUEST[$k])) {
            $criteria[$k] = (is_array($_REQUEST[$k])) ? $_REQUEST[$k] : preg_split('/[^a-zA-Z0-9]+/', $_REQUEST[$k]);
        }
    }
}

?>
<div style="margin:auto;width:95%;">
<?php
foreach (array(
    'storage' => 'in the Storage Location',
    'stock_type' => 'of Stock Type',
    'category_id' => 'in the Category',
    'category' => 'in the Category',
    'type' => 'in the Category',
) as $k => $v) {
    if ('' != $_REQUEST[$k]) {
        $temp_list = productGetOptionList($k);
        if ('' != $temp_list[$_REQUEST[$k]]) {
            echo '<p><b>This Page shows all the products ' . $v . ': ' . hecho($temp_list[$_REQUEST[$k]], 1) . '</b></p>';
        }

    }
}
?>
</div>
<?php

$item_query = productQuery($criteria);
$item_list  = productSort($item_query, $order_by);
if ('desc' == $order) {
    $item_list = array_reverse($item_list);
}

$items_per_page = max(count($item_list), 1);
if ($_GLOBALS['staff']['products_per_page'] > 0) {
    $items_per_page = $_GLOBALS['staff']['products_per_page'];
}

$count_total   = count($item_list);
$item_list     = array_chunk($item_list, $items_per_page);
$count_pages   = count($item_list);
$page_id       = max(0, min($count_pages - 1, $page_id));
$item_list     = $item_list[$page_id];
$count_display = count($item_list);

if ($count_total > 0) {
    $select_url = $_GET;
    unset($select_url['action']);
    $select_url = 'products_select.php?' . http_build_query($select_url);
?>
<form action="<?php hecho($select_url)?>" method="post">
<?php
include __DIR__ . '/printlist.tpl.php';
?>
</form>
<br /><br />
<?php
} else {
?>
No results were found
<?php
}
if ($popup_window) {
?>
<input type="button" onclick="window.close();" value="Close Window" />
<?php
}

include_once __DIR__ . '/footer.inc.php';
