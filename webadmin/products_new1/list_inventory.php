<?php
include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/header-inventory.inc.php';

$order = $_REQUEST['order'];
if ('desc' != $order) {
    $order = 'asc';
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

//Find and pagenate the items
$item_list = array();
$query     = $DB->query(sprintf(
    "SELECT `id` FROM `products_orders` ORDER BY `id` DESC"
));
while ($row = $DB->fetch_assoc($query)) {
    $item_list[] = $row['id'];
}
$DB->free_result($query);

if ('desc' == $order) {
    $item_list = array_reverse($item_list);
}

$items_per_page = 50;
if ($_GLOBALS['staff']['per_page'] > 0) {
    $items_per_page = $_GLOBALS['staff']['per_page'];
}

$count_total = count($item_list);

$item_list     = array_chunk($item_list, $items_per_page);
$count_pages   = count($item_list);
$page_id       = max(0, min($count_pages - 1, $page_id));
$item_list     = $item_list[$page_id] ?? [];
$count_display = count($item_list);

if ($count_total > 0) {
    $page_url           = $_GET;
    $page_url['action'] = 'list';
    unset($page_url['page_id']);
    $page_url = '.?' . http_build_query($page_url) . '&page_id=';

    $page_links = formatHTMLPageLinks($page_url, $page_id, $count_display, $count_total, $items_per_page);

    $display_currency = 'USD';
?>
<br />
<form action="<?php hecho($select_url)?>" method="post">

<!-- -->

<?php
$supplier_list = productGetOptionList('supplier_id');
?>

<?=formatHTMLTitle('View Inventory')?>

<table class="table_grid">
    <tr>
        <th>Date</th>
        <th>Invoice / Reference</th>
        <th>Supplier</th>
        <th>Total Invoice</th>
        <th>Total Freight Costs</th>
        <th>Ship Weight<br />(kgs)</th>
        <th>System Weight<br />(kgs)</th>
        <th></th>
    </tr>
<?php
$alt = true;
    foreach ($item_list as $id) {
        $item = productOrderFromDatabase($id);
?>
    <tr<?=($alt = !$alt) ? ' class="alt"' : ''?>>
        <td style="text-align:center;"><?=date(_('Y-m-d'), (is_null($item['created_time'])) ? time() : $item['created_time'])?></td>
        <td style="text-align:center;"><a href=".?action=view_inventory&amp;id=<?=$item['id']?>" target="_blank"><?php hecho($item['reference'])?></a></td>
        <td style="text-align:center;"><a href="/webadmin/supplier_new/edit.php?supplier_id=<?= $item['supplier_id'] ?>" target="_blank"><?php hecho($supplier_list[$item['supplier_id']])?></a></td>
        <td style="text-align:center;">
            <?=getCurrencySymbol($item['currency']) . convertCurrency($item['total_cost'], $item['currency'])?>
            <?=$item['currency']?>
        </td>
        <td style="text-align:center;">
            <?=getCurrencySymbol($item['currency']) . convertCurrency($item['freight_cost'], $item['currency'])?>
            <?=$item['currency']?>
        </td>
        <td style="text-align:right;"><?=$item['total_weight']?></td>
        <td style="text-align:right;"><?=$item['calc_weight']?></td>
        <td></td>
    </tr>
<?php
}
?>
</table>
<br />
<br />

<!-- / -->

</form>
<br />
<div style="text-align:center;"><?=$page_links?></div>
<br />
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
