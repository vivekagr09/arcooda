<?php
include_once __DIR__ . '/../products_new/common.inc.php';

global $DB, $_GLOBALS;

$order = (isset($_REQUEST['order']) && $_REQUEST['order'] === 'asc') ? 'asc' : 'desc';
$page_id = intval(isset($_REQUEST['page_id']) ? $_REQUEST['page_id'] : 0);

$item_list = array();
if ($query = $DB->query("SELECT `id` FROM `products_stock_transfer` WHERE `deleted_at` IS NULL ORDER BY `id` DESC")) {
    while ($row = $DB->fetch_assoc($query)) {
        $item_list[] = $row['id'];
    }
    $DB->free_result($query);
}
if ($order === 'asc') {
    $item_list = array_reverse($item_list);
}

$items_per_page = 50;
if (!empty($_GLOBALS['staff']['per_page'])) {
    $items_per_page = intval($_GLOBALS['staff']['per_page']);
}

$count_total = count($item_list);
$chunks = array_chunk($item_list, max(1, $items_per_page));
$count_pages = count($chunks);
$page_id = max(0, min($count_pages - 1, $page_id));
$item_list = ($count_total > 0) ? $chunks[$page_id] : array();
$count_display = count($item_list);

$warehouses = array();
if ($query = $DB->query("SELECT `id`, `company` FROM `tbl_warehouse`")) {
    while ($row = $DB->fetch_assoc($query)) {
        $warehouses[intval($row['id'])] = $row['company'];
    }
    $DB->free_result($query);
}

include_once $_SERVER['DOCUMENT_ROOT'] . '/webadmin/header.inc.php';
include __DIR__ . '/bar.inc.php';

if (isset($_GET['saved']) && $_GET['saved'] === '1') {
?>
<p style="color:#060;font-weight:bold;text-align:center;">Inventory was transferred between the selected warehouses.</p>
<?php
}

if ($count_total > 0) {
    $page_url = $_GET;
    unset($page_url['page_id']);
    $page_url = 'list.php?' . http_build_query($page_url) . '&page_id=';
    $page_links = formatHTMLPageLinks($page_url, $page_id, $count_display, $count_total, $items_per_page);
?>
<table class="table_grid">
    <tr>
        <th>Date</th>
        <th>Invoice / Reference</th>
        <th>From</th>
        <th>To</th>
        <th>Total Value</th>
        <th>Total Freight Costs</th>
        <th>Ship Weight<br />(kgs)</th>
        <th>System Weight<br />(kgs)</th>
    </tr>
<?php
    $alt = true;
    foreach ($item_list as $id) {
        $item = productGenericFromDatabase($id, '`products_stock_transfer`');
        $fromName = isset($warehouses[$item['from_warehouse_id']]) ? $warehouses[$item['from_warehouse_id']] : '';
        $toName = isset($warehouses[$item['to_warehouse_id']]) ? $warehouses[$item['to_warehouse_id']] : '';
        $goods = (float) $item['total_cost'];
        $freight = (float) $item['freight_cost'];
        $totalValue = $goods + $freight;
        $transferDate = !empty($item['created_at']) ? strtotime($item['created_at']) : time();
        $invoiceId = 0;
        if (preg_match('/#(\d+)/', (string) $item['reference'], $invoiceMatch)) {
            $invoiceId = intval($invoiceMatch[1]);
        }
?>
    <tr<?=($alt = !$alt) ? ' class="alt"' : ''?>>
        <td style="text-align:center;"><?=date('Y-m-d', $transferDate)?></td>
        <td style="text-align:center;">
<?php
        if ($invoiceId > 0) {
?>
            <a href="/webadmin/invoice/?action=view&amp;invoice_id=<?=$invoiceId?>" target="_blank"><?=htmlspecialchars($item['reference'])?></a>
<?php
        } else {
            echo htmlspecialchars($item['reference']);
        }
?>
        </td>
        <td style="text-align:center;"><?=htmlspecialchars($fromName)?></td>
        <td style="text-align:center;"><?=htmlspecialchars($toName)?></td>
        <td style="text-align:center;">
            <?=getCurrencySymbol($item['currency']) . convertCurrency($totalValue, $item['currency'])?>
            <?=htmlspecialchars($item['currency'])?>
        </td>
        <td style="text-align:center;">
            <?=getCurrencySymbol($item['currency']) . convertCurrency($item['freight_cost'], $item['currency'])?>
            <?=htmlspecialchars($item['currency'])?>
        </td>
        <td style="text-align:right;"><?=htmlspecialchars($item['total_weight'])?></td>
        <td style="text-align:right;"><?=htmlspecialchars($item['calc_weight'])?></td>
    </tr>
<?php
    }
?>
</table>
<br />
<div style="text-align:center;"><?=$page_links?></div>
<br />
<?php
} else {
?>
<p style="text-align:center;">No transferred inventory was found.</p>
<?php
}

include_once $_SERVER['DOCUMENT_ROOT'] . '/webadmin/footer.inc.php';
