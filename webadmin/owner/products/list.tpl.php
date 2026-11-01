


<div style="margin:auto;width:95%;">
<?php
moduleLoad('product');

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
$criteria['section'][]=2;
$criteria['section'][]=3;
$item_query = productQuery($criteria);
$item_list  = productSort($item_query, $order_by);


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
$item_list     = $item_list[$page_id] ?? [];
$count_display = count($item_list);
if ($count_total > 0) {
    $page_url = $_GET;
    unset($page_url['page_id']);
    $page_url   = '.?' . http_build_query($page_url) . '&page_id=';
    $page_links = formatHTMLPageLinks($page_url, $page_id, $count_display, $count_total, $items_per_page);

?>
<div style="text-align:center;"><?=$page_links?></div>
<?php
include __DIR__ . '/_products.tpl.php';
?>
<div style="text-align:center;"><?=$page_links?></div>
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
?>

<!-- /list.tpl -->
