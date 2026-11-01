<?php
include_once __DIR__ . '/common.inc.php';

$popup_window = (isset($_REQUEST['return_message'])) ? true : false;
$order_by     = ('' != $_REQUEST['order_by']) ? $_REQUEST['order_by'] : 'id';
$order        = ('' != $_REQUEST['order']) ? $_REQUEST['order'] : 'asc';

$list = false;

include_once __DIR__ . '/header.inc.php';

$criteria           = array('active' => array('y', 'n'), 'id_changed' => 'n', 'item_type' => 'ind', 'stock_type' => array('stock_for_sale', 'stock_sample'));
$all_product_query  = productQuery($criteria);
$category_subtotals = array();
foreach (productChunk($all_product_query, 'type') as $k => $v) {
    $category_subtotals[$k] = productSumValues($v);
}

$categories_by_category = productGetOptionList('category_id');
//print_array($categories_by_category);
$another         = array(array(0));
$tree_final      = array();
$category_totals = array();

// create the tree-view, so don't have to use recursive algorithm.
while (is_array($tree = array_pop($another))) {
    $tree_final[] = $tree;
    $category_id  = end($tree);
    if (is_array($category_subtotals[$category_id])) {
        foreach ($category_subtotals[$category_id] as $key => $value) {
            foreach ($tree as $supracategory_id) {
                $category_totals[$supracategory_id][$key] += $value;
            }
        }

    }
    if (count($categories_by_category[$category_id])) {
        foreach ($categories_by_category[$category_id] as $subcategory_id) {
            $new_tree   = $tree;
            $new_tree[] = $subcategory_id;
            array_push($another, $new_tree);
        }
    }
}
echo formatHTMLTitle('Stock Values');
?>
<table class="table_grid">
    <tr><th>Category</th><th>Sell Value</th><th>Cost Value</th><th>% Markup</th><th>List</th><th>Export</th>
<?php
$total_stock_value = $category_totals[0]['item_offer_s'];
$total_stock_cost  = $category_totals[0]['item_cost_s'];
$t_markup          = ($total_stock_cost > 0) ? ((($total_stock_value / $total_stock_cost) - 1) * 100) : 0;
$count             = 0;
foreach ($tree_final as $key => $tree) {
    if (count($tree) == 2) {
        $category_id = end($tree);
        $total_value = $category_totals[$category_id]['item_offer_s'];
        $total_cost  = $category_totals[$category_id]['item_cost_s'];
        $markup      = ($total_cost > 0) ? ((($total_value / $total_cost) - 1) * 100) : 0;
?>
    <tr <?=($count++ % 2 == 1) ? 'class="alt"' : ''?> style="text-align:center;">
        <td style="text-align:left;padding-left:20px;">
            <a href=".?action=print&active=y|n&stock_type=stock_for_sale|stock_sample&type=<?=$category_id?>" target="_blank"><?=$category_list[$category_id]?></a>
        </td>
        <td>$<?=sprintf('%.2f', $total_value)?></td><td>$<?=sprintf('%.2f', $total_cost)?></td>
        <td><?= sprintf('%.2f', $markup) ?>%</td>
        <td><a target="_blank" href="/webadmin/products/?action=type&type=<?=$category_id?>&active=y|n&stock_type=stock_for_sale|stock_sample">List</a></td>
        <td><a href="/webadmin/products/products_export_stock_values.php?type=<?=$category_id?>">Export To Excel</a></td>
    </tr>
<?php
}
}
?>
    <tr class="alt" style="text-align:center">
        <td style="text-align:right;"><b>Totals</b></td><td>$<?=sprintf('%.2f', $total_stock_value)?> AUD</td>
        <td>$<?=sprintf('%.2f', $total_stock_cost)?> AUD</td><td><?=sprintf('%.2f', $t_markup)?>%</td>
        <td>&nbsp;</td><td>&nbsp;</td>
    </tr>
<?php
$count = 0;
foreach ($tree_final as $key => $tree) {
    $category_id = end($tree);
    if ($category_id) {
        $total_value = $category_totals[$category_id]['item_offer_s'];
        $total_cost  = $category_totals[$category_id]['item_cost_s'];
        $markup      = ($total_cost > 0) ? ((($total_value / $total_cost) - 1) * 100) : 0;
        $colour      = (count($tree) == 2) ? '#ee0' : ((isset($categories_by_category[end($tree)])) ? "#ee9" : '');
?>
    <tr style="text-align:center;background:<?=$colour?>" <?=($count++ % 2 == 1) ? 'class="alt"' : ''?>>
        <td style="text-align:left;padding-left:<?=10 * count($tree)?>px;">
            <a href=".?action=print&active=y|n&stock_type=stock_For_sale|sample_stock&type=<?=$category_id?>" target="_blank"><?=$category_list[$category_id]?></a>
        </td>
        <td>$<?=sprintf('%.2f', $total_value)?></td><td>$<?=sprintf('%.2f', $total_cost)?></td>
        <td><?= sprintf('%.2f', $markup) ?>%</td>
        <td><a target="_blank" href="/webadmin/products/?action=type&type=<?=$category_id?>&active=y|n&stock_type=stock_for_sale|sample_stock">List</a></td>
        <td><a target="_blank" href="/webadmin/products/products_export_stock_values.php?type=<?=$category_id?>">Export To Excel</a></td>
    </tr>
<?php
}
}
?>
</table>
<br />
<div style="text-align:center;">
    <input type="button" value="Export To Excel" onclick="window.location='products_export_stock_values.php'" />
</div>
<br />
<?php

include_once __DIR__ . '/footer.inc.php';
