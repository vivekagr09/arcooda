<?php
include_once __DIR__ . '/../products_new/common.inc.php';

global $DB;

$id = intval(isset($_GET['id']) ? $_GET['id'] : 0);
$transfer = $id ? productGenericFromDatabase($id, '`products_stock_transfer`') : false;
if (!$transfer || !empty($transfer['deleted_at'])) {
    header('Location: list.php');
    exit;
}

$warehouses = array();
if ($query = $DB->query("SELECT `id`, `company` FROM `tbl_warehouse`")) {
    while ($row = $DB->fetch_assoc($query)) {
        $warehouses[intval($row['id'])] = $row['company'];
    }
    $DB->free_result($query);
}

$items = array();
if ($query = $DB->query(sprintf(
    "SELECT * FROM `products_stock_transfer_item` WHERE `transfer_id` = %d ORDER BY `id` ASC",
    $id
))) {
    while ($row = $DB->fetch_assoc($query)) {
        $items[] = $row;
    }
    $DB->free_result($query);
}

$fromName = isset($warehouses[$transfer['from_warehouse_id']]) ? $warehouses[$transfer['from_warehouse_id']] : '';
$toName = isset($warehouses[$transfer['to_warehouse_id']]) ? $warehouses[$transfer['to_warehouse_id']] : '';

include_once $_SERVER['DOCUMENT_ROOT'] . '/webadmin/header.inc.php';
include __DIR__ . '/bar.inc.php';
?>
<?=formatHTMLTitle('View Transferred Inventory')?>
<table class="table_fields">
    <tr>
        <th>Date</th>
        <td><?=date('Y-m-d', !empty($transfer['created_time']) ? $transfer['created_time'] : time())?></td>
    </tr>
    <tr>
        <th>Invoice / Reference</th>
        <td><?=htmlspecialchars($transfer['reference'])?></td>
    </tr>
    <tr>
        <th>From</th>
        <td><?=htmlspecialchars($fromName)?></td>
    </tr>
    <tr>
        <th>To</th>
        <td><?=htmlspecialchars($toName)?></td>
    </tr>
    <tr>
        <th>Comments</th>
        <td><?=nl2br(htmlspecialchars($transfer['comment']))?></td>
    </tr>
</table>
<br />
<table class="table_grid" style="width:98%;margin:auto;">
    <tr>
        <th>Product ID</th>
        <th>Product Name</th>
        <th>Qty</th>
        <th>Unit Price</th>
        <th>Unit Price USD</th>
        <th>Current Stock</th>
    </tr>
<?php
$alt = true;
foreach ($items as $item) {
    $product = productFromDatabase($item['product_id']);
?>
    <tr<?=($alt = !$alt) ? ' class="alt"' : ''?>>
        <td style="text-align:center;"><?=intval($item['product_id'])?></td>
        <td><?=htmlspecialchars($product ? $product['product_name'] : '')?></td>
        <td style="text-align:right;"><?=intval($item['qty'])?></td>
        <td style="text-align:right;"><?=htmlspecialchars($item['unit_cost'])?> <?=htmlspecialchars($transfer['currency'])?></td>
        <td style="text-align:right;"><?=htmlspecialchars($item['unit_cost_usd'])?></td>
        <td style="text-align:right;"><?=intval($item['from_stock'])?></td>
    </tr>
<?php
}
?>
</table>
<br />
<p style="text-align:center;"><a href="list.php">Back to transferred inventory</a></p>
<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/webadmin/footer.inc.php';
