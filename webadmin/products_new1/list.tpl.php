<!-- list.tpl -->

<div style="text-align:left;width:95%;margin:auto;">
    <a href="#" onclick="toggleSection(&quot;sectionViewByItemTypes&quot;);return false"><i class="fas fa-caret-right"></i> <b>List by Category</b></a>
    <br /><br />
</div>
<div id="sectionViewByItemTypes" class="child" style="display:none;">
<?php
$list_by1     = 'type';
$list_by2     = '';
$list_columns = 3;
$sub_criteria = $criteria;
unset($sub_criteria[$list_by1]);
$item_query = productQuery($sub_criteria);
include __DIR__ . '/_navigate.tpl.php';
?>
</div>


<div style="text-align:left;width:95%;margin:auto;">
    <a href="#" onclick="toggleSection(&quot;sectionViewBySuppliers&quot;);return false"><i class="fas fa-caret-right"></i> <b>List by Supplier</b></a>
    <br /><br />
</div>
<div id="sectionViewBySuppliers" class="child" style="display:none;">
<?php
$list_by1     = 'preferred_supplier';
$list_by2     = '';
$list_columns = 1;
$sub_criteria = $criteria;
unset($sub_criteria[$list_by1]);
$item_query = productQuery($criteria);
include __DIR__ . '/_navigate.tpl.php';
?>
</div>
<div style="text-align:left;width:95%;margin:auto;">
    <a href="#" onclick="toggleSection(&quot;sectionStock&quot;);return false"><i class="fas fa-caret-right"></i> <b>Stocklist</b></a>
    <br /><br />
</div>
<div id="sectionStock" class="child" style="display:none;">
<?php
include __DIR__ . '/_stock_values.tpl.php';
?>
</div>




<div style="text-align:left;width:95%;margin:auto;">
    <a href="#" onclick="toggleSection(&quot;sectionSearch&quot;);return false"><i class="fas fa-caret-right"></i> <b>Search</b></a>
    <br /><br />
</div>
<div id="sectionSearch" class="child"<?=(array_key_exists('search_for', $_REQUEST)) ? '' : ' style="display:none;"'?>>
<?php
$search_for = (array_key_exists('search_for', $_REQUEST)) ? $_REQUEST['search_for'] : '';
include __DIR__ . '/_search.tpl.php';
?>
</div>


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

if (array_key_exists('hide_list', $_REQUEST)) {
    foreach (explode('|', $_REQUEST['hide_list']) as $k) {
        $key = array_search($k, $item_list);
        if (false !== $key) {
            unset($item_list[$key]);
        }
    }
}

if (array_key_exists('ignore_list', $_REQUEST)) {
    foreach (explode('|', $_REQUEST['ignore_list']) as $k) {
        $key = array_search($k, $item_list);
        if (false !== $key) {
            unset($item_list[$key]);
        }
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
