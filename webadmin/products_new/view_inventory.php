<?php
include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/header-inventory.inc.php';

$id    = $_REQUEST['id'];
$stock = productOrderFromDatabase($id);

$stock_additions = productInventoryFromDatabase($id);
foreach ($stock_additions as $key => $row) {
    $stock_additions[$key]['item_qty']       = $row['movement'];
    $stock_additions[$key]['unit_costus']    = $row['unit_cost_usd'];
    $stock_additions[$key]['unit_freightus'] = $row['unit_freight_usd'];
    $item                  = productFromDatabase($row['product_id']);
    $stock_additions[$key]['product_name']   = $item['product_name'];
    $dimension             = productDimensionFromDatabase($row['product_id']);
    $stock_additions[$key]['item_weight']    = $dimension['online_weight'];
}
include __DIR__ . '/supplier_details.tpl.php';
include __DIR__ . '/stock_additions.tpl.php';

include_once __DIR__ . '/footer.inc.php';
