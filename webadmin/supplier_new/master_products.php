<?php
global $_GLOBALS;

include_once $_SERVER['DOCUMENT_ROOT'] . '/webadmin/common.inc.php';

moduleLoad('product');

include_once __DIR__ . '/header.inc.php';
$linked_id = intval($_REQUEST['supplier_id']);
$order     = $_REQUEST['order'];
if ('asc' != $order) {
    $order = 'desc';
}
include $_SERVER['DOCUMENT_ROOT'] . '/webadmin/products_new/query.inc.php';
$page_id  = intval($_REQUEST['page_id']);
$order_by = strtolower($_REQUEST['order_by']);

$criteria = array();

if (array_key_exists('search_for', $_REQUEST)) {
    foreach (array('search_for') as $k) {
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
$criteria['active']='y';
$search_for = $_REQUEST['search_for'];
$post_url   = $_GET;
unset($post_url['page_id']);
unset($post_url['search_for']);
$post_url = 'master_products.php?' . http_build_query($post_url);
?>
<div style="text-align:left;width:95%;margin:auto;">
    <a href="#" onclick="<?=htmlspecialchars('toggleSection(' . json_encode('sectionSearch') . ');return false')?>"><i class="fas fa-caret-right"></i> <b>Search</b></a>
    <br /><br />
    <div id="sectionSearch" class="child"<?=(isset($_REQUEST['search_for'])) ? '' : ' style="display:none;"'?>>
        <form action="<?php hecho($post_url)?>" method="post">
            <table class="table_search">
                <tr><td style="text-align:center;background:#FFFFCC;"><h2 style="color:#FF9900;">Search Products</h2></td></tr>
                <tr><td style="text-align:center;background:#FFFFCC;padding-bottom:10px;">Enter <b>Product ID</b> or <b>Product Name</b></td></tr>
                <tr><td style="text-align:center;background:#FFFFCC;">Search For: <input type="text" style="width:256px;" name="search_for" value="<?php hecho($search_for)?>" /></td></tr>
                <tr><td style="text-align:center;background:#FFFFCC;"><input type="submit" value="Search" /></td></tr>
            </table>
        </form>
        <br /><br />
    </div>
</div>
<?php

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
$list_by1     = 'type';
$list_by2     = '';
$list_columns = 3;
$sub_criteria = $criteria;
unset($sub_criteria[$list_by1]);
$item_query = productQuery($sub_criteria);
$item_list = productSortData($item_query, $order_by);
foreach (explode('|', $_REQUEST['hide_list']) as $k) {
    $key = array_search($k, $item_list);
    if (false !== $key) {
        unset($item_list[$key]);
    }

}

if ('desc' == $order) {
    $item_list = array_reverse($item_list);
}

$items_per_page = 50;
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
    $ignore_ids = explode('|', $_REQUEST['ignore_list']);
    if ($linked_id) {
        $ignore_ids[] = $linked_id;
        // ie ignore that product + all the products that are related..
        $related = productRelatedFromDatabase($linked_id);
        foreach ($related as $tmp) {
            $ignore_ids[] = $tmp;
        }

        $ignore_ids = array_unique($ignore_ids);
    }
    $page_url = $_GET;
    unset($page_url['page_id']);
    $page_url   = 'master_products.php?' . http_build_query($page_url) . '&page_id=';

    $page_links = formatHTMLPageLinks($page_url, $page_id, $count_display, $count_total, $items_per_page);
    $select_url = $_GET;
    unset($select_url['action']);
    $select_url = 'select-product.php?' . http_build_query($select_url);
?>
<form action="<?php hecho($select_url)?>" method="post">
<?php
include __DIR__ . '/master_products.tpl.php';
?>
</form>
<?=$page_links?>
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
