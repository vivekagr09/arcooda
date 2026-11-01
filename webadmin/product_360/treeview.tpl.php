<?php
/**
 * @var int[] $item_list
 */
?>
<!-- <?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->

<?php
global $_GLOBALS;

moduleLoad('category'); //categoryGetOptionList
moduleLoad('product');

$category_list     = categoryGetOptionList('name');
$category_children = categoryGetOptionList('category_children');
$category_products = array();
$category_links    = array();
if (function_exists('productSearch')) {
    $criteria = $_GLOBALS['criteria_options']['product'];
    unset($criteria['active']);
    $product_query     = productQuery($criteria);
    $product_list      = productSearch($product_query);
    $category_products = productGroupCount($product_list, 'type');
    $category_links    = productGroupCount($product_list, 'category_id');
}

?>
<style type="text/css">
/*<![CDATA[*/
.treeview,.treeview ul{list-style:none;padding-left:1em}
.treeview li{margin:4px}
.treeview li>ul{display:none}
.treeview li.active>ul{display:block}
.treeview i.rotate{transform:rotate(90deg)}
/*]]>*/
</style>

<script type="text/javascript">
//<![CDATA[
$(function() {
    $(".treeview span,.treeview .bi-chevron-right").click(function() {
        $(this).closest("li").toggleClass("active").children("i").toggleClass("bi-chevron-right").toggleClass("bi-chevron-down");
    }).css("cursor", "pointer");
});
//]]>
</script>
<?php

function category_treeview($id)
{
    global $category_list;
    global $category_children;
    global $category_products;
    global $category_links;

?>
<li>
    <i class="bi bi-chevron-right"></i>
    <span>
        <i class="bi bi-folder"></i>
        <?=(array_key_exists($id, $category_list)) ? htmlspecialchars($category_list[$id]) : ''?>
    </span>
    <ul>
<?php
    if (array_key_exists($id, $category_children)) {
        foreach ($category_children[$id] as $category_id) {
            category_treeview($category_id);
        }
    }
    if (array_key_exists($id, $category_links)) {
?>
        <li>
            <i class="bi bi-three-dots-vertical" style="visibility:hidden;"></i>
            <i class="bi bi-link"></i>
            <a href="/webadmin/products/?category_id=<?=$id?>" target="_blank">Linked Products</a>
            (<?=(array_key_exists($id, $category_links)) ? $category_links[$id] : 0?>)
        </li>
<?php
    }
    if (array_key_exists($id, $category_products)) {
?>
        <li>
            <i class="bi bi-three-dots-vertical" style="visibility:hidden;"></i>
            <i class="bi bi-file"></i>
            <a href="/webadmin/products/?type=<?=$id?>" target="_blank">Actual Products</a>
            (<?=(array_key_exists($id, $category_products)) ? $category_products[$id] : 0?>)
        </li>
<?php
    }
    if (!array_key_exists($id, $category_children) && !array_key_exists($id, $category_links) && !array_key_exists($id, $category_products)) {
?>
        <li>
            <i class="bi bi-three-dots-vertical" style="visibility:hidden;"></i>
            <i class="bi bi-three-dots"></i>
            <i>empty</i>
            <i class="bi bi-three-dots"></i>
        </li>
<?php
    }
?>
    </ul>
</li>
<?php
}

?>
<div style="text-align:left;margin:auto;width:95%;">

    <div><a href="#" onclick="toggleSection('sectionTreeView');return false"><i class="bi bi-caret-right-fill"></i> <b>Tree View</b></a>   </div>
    <br />

    <div id="sectionTreeView" style="display:none;">
        <i class="bi bi-folder"></i>
        <b>Product Categories</b>
        <ul class="treeview">
<?php
foreach ($item_list as $id) {
    category_treeview($id);
}
?>
        </ul>
        <br />
    </div>
</div>

<!-- /<?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->
