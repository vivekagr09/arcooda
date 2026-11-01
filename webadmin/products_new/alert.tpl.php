<!-- alert.tpl -->

<?php
global $_GLOBALS;

$alt           = false;
$ord_img       = '<i class="fas fa-sort-' . (('desc' == $order) ? 'down' : 'up') . '"></i>';

$active_url            = $_GET;
$active_url['action']  = 'active';
unset($active_url['id']);
$active_url = '.?' . http_build_query($active_url) . '&id=';

$deactive_url            = $_GET;
$deactive_url['action']  = 'deactive';
unset($deactive_url['id']);
$deactive_url = '.?' . http_build_query($deactive_url) . '&id=';

$edit_url            = $_GET;
$edit_url['action']  = 'edit';
unset($edit_url['id']);
$edit_url = '.?' . http_build_query($edit_url) . '&id=';

$select_url            = $_GET;
$select_url['action']  = 'select';
unset($select_url['id']);
$select_url = '.?' . http_build_query($select_url) . '&id=';

$similar_url               = $_GET;
$similar_url['alert_flag'] = 'y|n';
unset($similar_url['type']);
$similar_url = 'alert.php?' . http_build_query($similar_url) . '&type=';

$sort_url           = $_GET;
$sort_url['action'] = 'alert';
$sort_url['order']  = ('desc' == $order) ? 'asc' : 'desc';
unset($sort_url['order_by']);
$sort_url = '.?' . http_build_query($sort_url) . '&order_by=';

$supplier_url               = $_GET;
$supplier_url['alert_flag'] = 'y|n';
unset($supplier_url['supplier_id']);
$supplier_url = 'alert.php?' . http_build_query($supplier_url) . '&supplier_id=';

$unlock_url           = $_GET;
$unlock_url['action'] = 'unlock';
unset($unlock_url['id']);
$unlock_url = '.?' . http_build_query($unlock_url) . '&id=';

$view_url            = $_GET;
$view_url['action']  = 'view';
unset($view_url['id']);
$view_url = '.?' . http_build_query($view_url) . '&id=';

?>
<div id="sectionAlertList">
    <table class="table_grid">
        <tr>
<?php
if ('y' == $_REQUEST['multiple']) {
?>
            <th>&nbsp;</th>
<?php
}
?>
            <th><a href="<?=$sort_url?>id">ID</a><?=('id' == $order_by) ? $ord_img : ''?></th>
            <th style="width:100%;"><a href="<?=$sort_url?>product_name">Product Name</a><?=('product_name' == $order_by) ? $ord_img : ''?></th>
            <th>Last Supplier</th>
            <th><a href="<?=$sort_url?>product_costus">Cost<br />Price<br />(USD)</a><?=('cost_price' == $order_by) ? $ord_img : ''?></th>
            <th><a href="<?=$sort_url?>qty">Current Stock</a><?=('qty' == $order_by) ? $ord_img : ''?></th>
            <th>Last Qty Ordered</th>
            <th><a href="<?=$sort_url?>alert_recommend" title="Recommended Order Quantity">Rec. Order Qty</a><?=('alert_recommend' == $order_by) ? $ord_img : ''?></th>
            <th>&nbsp;</th>
        </tr>
<?php
$supplier_list = productGetOptionList('supplier_id');
foreach ($item_list as $id) {
    $item           = productFromDatabase($id);
    $item_invoices  = productInvoicesFromDatabase($item['id']);
    $item_lock      = productGetLock($item['id']);
    $item_reserved  = productReservedFromDatabase($item['id']);
    $item_suppliers = productSuppliersFromDatabase($item['id']);
    $dpoints = $item['precision'];
    if (0 == $dpoints) {
        $dpoints = 2;
    }

    ob_start();
    include __DIR__ . '/__product_tooltip.tpl.php';
    $product_tooltip = ob_get_clean();

    ob_start();
    include __DIR__ . '/__reserve_tooltip.tpl.php';
    $reserve_tooltip = ob_get_clean();

    $cost_price = convertCurrency($item['cost_price'], $item['cost_currency'], 'USD', false);

    $last_order = array('movement' => 'NA', 'unit_cost' => $cost_price, 'unit_currency' => 'USD', 'unit_freight' => 'NA', 'supplier_id' => $item['preferred_supplier']);
    foreach (productInventoryFromDatabase($item['id']) as $row) {
        if ($row['movement'] > 0 && 0 != $row['supplier_id']) {
            $last_order = $row;
            break;
        }
    }

    $supplier_id = $item['preferred_supplier'];
    if (0 != $item['supplier_id']) {
        $supplier_id = $item['supplier_id'];
    }

    if (0 != $last_order['supplier_id']) {
        $supplier_id = $last_order['supplier_id'];
    }

    $class = '';
    if ($alt = !$alt) {
        $class = 'alt';
    }

    if (!is_null($item_lock)) {
        $class = 'locked';
        if ($item_lock['staff_id'] == $_GLOBALS['staff']['id']) {
            $class = 'mylock';
        }
    }

    if (!is_null($item['updated_at']) && formatDaysOpen(strtotime($item['updated_at'])) == 0) {
        $class = 'highlight';
    }

    if (formatDaysOpen(strtotime($item['created_at'])) == 0) {
        $class = 'highlight';
    }

    if (in_array($item['id'], $ignore_ids)) {
        $class = 'exists';
    }

    if ('' != $class) {
        $class = ' class="' . $class . '"';
    }

    $row_style = array();
    if ('y' == $item['special']) {
        $row_style['color'] = 'color:#f00;';
    }

    if ('y' == $item['special_flag']) {
        $row_style['color'] = 'color:#f00;';
    }

    $row_style = implode("", $row_style);

    $id_style = array();
    if ('stock_for_sale' == $item['stock_type']) {
        $id_style['background'] = 'background:#bfb;';
    }

    if ('consignment' == $item['stock_type']) {
        $id_style['background'] = 'background:#ebe;';
    }

    if ('repair_item' == $item['stock_type']) {
        $id_style['background'] = 'background:#bee;';
    }

    if ('y' == $item['superseded']) {
        $id_style['background'] = 'background:#edf;';
    }

    $id_style = implode("", $id_style);

    $qty_style = array();
    if (count($item_reserved) > 0) {
        //$qty_style['color'] = 'color:#fd6;';
        $qty_style['background'] = 'background:#cce;';
    }
    $qty_style = implode("", $qty_style);

?>
        <tr<?=$class . (("" != $row_style) ? (' style="' . $row_style . '"') : '')?>>
<?php
if ('y' == $_REQUEST['multiple']) {
?>
            <td style="text-align:center;"><?=(!in_array($item['id'], $ignore_ids)) ? ('<input type="checkbox" name="item_list[]" value="' . $item['id'] . '" />') : ''?></td>
<?php
}
?>
            <td style="text-align:center;<?=$id_style?>"><?=$item['product_number']?></td>
            <td style="text-align:left;padding-left:4px;" onmouseover="<?php hecho('tt_in(' . jecho($product_tooltip, 1) . ');')?>" onmouseout="tt_out();"><a href="<?php hecho($item['view_url'])?>" target="_blank" style="<?=("" != $row_style) ? $row_style : 'color:#000;'?>"><?php hecho($item['product_name'])?></a></td>
            <td style="text-align:center;">
<?php
if ($supplier_id > 0) {
?>
                <a href="<?php hecho($supplier_url . $supplier_id)?>" target="_blank"><?php hecho($supplier_list[$supplier_id])?></a>
<?php
} else {
        hecho($last_order['supplier_id']);
    }

?>
            </td>
            <td style="text-align:center;"><?=$cost_price?></td>
            <td style="text-align:center;<?=$qty_style?>"<?=("" != $reserve_tooltip) ? (' onmouseover="' . hecho('tt_in(' . jecho($reserve_tooltip, 1) . ')', 1) . '" onmouseout="tt_out()"') : ''?>><?=$item['qty'] - $item['floor_stock']?> - <?=$item['floor_stock']?></td>
            <td style="text-align:center;"><?php hecho($last_order['movement'])?></td>
            <td style="text-align:center;"><?php hecho($item['alert_recommend'])?></td>
<?php
$links = array();
    if (is_null($item_lock) || $item_lock['staff'] == $_GLOBALS['staff']['user']) {
        $links[] = '<a href="' . $edit_url . $item['id'] . '">Edit</a>';
    }

    if ($popup_window) {
        $links[] = '<a href="' . $select_url . $item['id'] . '">Select</a>';
    }

    if (!is_null($item_lock) && $item_lock['staff'] == $_GLOBALS['staff']['user']) {
        $links[] = '<a href="' . $unlock_url . $item['id'] . '">Unlock</a>';
    }

    $links[] = '<a href="' . hecho($similar_url . $item['type'], 1) . '" target="_blank">Similar</a>';
?>
            <td style="white-space:nowrap;"><?=implode(" | ", $links)?></td>
        </tr>
<?php
}
if ('y' == $_REQUEST['multiple']) {
?>
        <tr<?=!$alt ? ' class="alt"' : ''?>><td colspan="2"><input type="checkbox" name="select_all" onclick="selectAll(this)" /><br />Select All</td><td colspan="8">&nbsp;</td></tr>
<?php
}
?>
    </table>
    <br /><br />

<?php
if (!$popup_window && false) {
?>
    <div style="text-align:center;">
        <span style="font-style:italic;">Notes:</span><br />
        Rows highlighted in <span style="background:#90EEA0;;">green</span> have been modified today<br />
        Rows highlighted in <span style="background:#EE90A0;">red</span> are locked by another user<br />
        Rows highlighted in <span style="background:#90A0EE;">blue</span> are locked by you<br />
        Products coloured in <span style="color:#f00;">red</span> are on special<br />
        Cells highlighted in <span style="background:#edf;">light purple</span> are superseded<br />
        Cells highlighted in <span style="background:#BBFFBB;">light green</span> are products in stock.<br />
        Cells highlighted in <span style="background:#EEBBEE;">light purple</span> are consignment products.<br />
        Cells highlighted in <span style="background:#FEDC6C;">light orange</span> are sample products.<br />
        Cells highlighted in <span style="background:#BBEEEE;">light cyan</span> are repair products.<br />
    </div>
    <br /><br />
<?php
} elseif ($popup_window) {
?>
    <div style="text-align:center;">
        Rows highlighted in <span style="background:#DFC299">brown</span> are already linked with this product or is the same product
    </div>
<?php
}
if ('y' == $_REQUEST['multiple']) {
?>
    <p style="text-align:center;">
        <input type="submit" value="Select Products" />
    </p>
    <br /><br />
<?php
}
?>
</div>

<div style="color:#fff;text-align:center;padding:8px;display:none;" id="sectionList_hideNote">
    Product list can be viewed by Clicking "Products" above<br />
</div>

<!-- /alert.tpl -->
