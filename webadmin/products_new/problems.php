<?php
include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/header.inc.php';

$order = $_REQUEST['order'];
if ('asc' != $order) {
    $order = 'desc';
}

$page_id  = intval($_REQUEST['page_id']);
$order_by = strtolower($_REQUEST['order_by']);

$problems = array();
$query    = $DB->query(sprintf(
    //"SELECT `id` FROM `products` WHERE `product_offerus` < `product_costus` AND `product_offerus` <> 0.0"
    "SELECT `p`.`id` FROM `products` AS `p` INNER JOIN `highwaygroup`.`currency` AS `c1` ON `c1`.`code` = `p`.`sales_currency` INNER JOIN `highwaygroup`.`currency` AS `c2` ON `c2`.`code` = `p`.`cost_currency` WHERE `p`.`sales_price` * `c1`.`usfrom` < `p`.`cost_price` * `c2`.`usfrom` AND `p`.`sales_price` <> 0.0"
));
while ($row = $DB->fetch_assoc($query)) {
    $problems[$row['id']] = $row['id'];
}
$DB->free_result($query);

$query = $DB->query(sprintf(
    //"SELECT `id` FROM `products` WHERE `active` = 'y' AND `product_offerus` = 0.0"
    "SELECT `id` FROM `products` WHERE `active` = 'y' AND `sales_price` = 0.0"
));
while ($row = $DB->fetch_assoc($query)) {
    $problems[$row['id']] = $row['id'];
}
$DB->free_result($query);

$criteria = $_GLOBALS['criteria_options']['products'];
unset($criteria['active']);
foreach (productGetOptionList('all_fields') as $key => $value) {
    if ('' != $_REQUEST[$key]) {
        $criteria[$key] = explode('|', $_REQUEST[$key]);
    } elseif (array_key_exists($key, $_REQUEST)) {
        $criteria[$key] = $_REQUEST[$key];
    }
}

unset($criteria['id']);
if ('' != $_REQUEST['type']) {
    $criteria['type'] = explode('|', $_REQUEST['type']);
}

if ('' != $_REQUEST['search_for']) {
    $criteria['any_text'] = $_REQUEST['search_for'];
}

$criteria['id'] = $problems;

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
        $related      = productRelatedFromDatabase($linked_id);
        foreach ($related as $tmp) {
            $ignore_ids[] = $tmp;
        }

        $ignore_ids = array_unique($ignore_ids);
    }
    $page_url = $_GET;
    unset($page_url['page_id']);
    $page_url   = '.?' . http_build_query($page_url) . '&page_id=';
    $page_links = formatHTMLPageLinks($page_url, $page_id, $count_display, $count_total, $items_per_page);
    $select_url = $_GET;
    $select_url['action'] = 'select';
    $select_url = '.?' . http_build_query($select_url);
?>
<form action="<?php hecho($select_url)?>" method="post">
<?php
include __DIR__ . '/_products.tpl.php';
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
