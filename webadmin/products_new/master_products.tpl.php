<!-- master_products.tpl -->

<?php
$alt     = false;
$ord_img = '<i class="fas fa-sort-' . (('desc' == $order) ? 'down' : 'up') . '"></i>';

$activate_url           = $_GET;
$activate_url['action'] = 'activate';
unset($activate_url['id']);
$activate_url = '.?' . http_build_query($activate_url) . '&id=';

$deactivate_url           = $_GET;
$deactivate_url['action'] = 'deactivate';
unset($deactivate_url['id']);
$deactivate_url = '.?' . http_build_query($deactivate_url) . '&id=';

$sort_url           = $_GET;
$sort_url['action'] = 'list';
$sort_url['order']  = ('desc' == $order) ? 'asc' : 'desc';
unset($sort_url['order_by']);
$sort_url = '?' . http_build_query($sort_url) . '&order_by=';

$edit_url           = $_GET;
$edit_url['action'] = 'edit';
unset($edit_url['id']);
$edit_url = '.?' . http_build_query($edit_url) . '&id=';

$copy_url           = $_GET;
$copy_url['action'] = 'copy';
unset($copy_url['id']);
$copy_url = '.?' . http_build_query($copy_url) . '&id=';

$view_url           = $_GET;
$view_url['action'] = 'view';
unset($view_url['id']);
$view_url = '.?' . http_build_query($view_url) . '&id=';

$unlock_url           = $_GET;
$unlock_url['action'] = 'unlock';
unset($unlock_url['id']);
$unlock_url = '.?' . http_build_query($unlock_url) . '&id=';

$select_url           = $_GET;
$select_url['action'] = 'select';
unset($select_url['id']);
$select_url = '.?' . http_build_query($select_url) . '&id=';

$delete_url           = $_GET;
$delete_url['action'] = 'delete';
unset($delete_url['id']);
$delete_url = '.?' . http_build_query($delete_url) . '&id=';

$active_list = productGetOptionList('active');
?>

<script type="text/javascript">
//<![CDATA[
function selectAll(checked) {
    var collection = getElements('item_list[]');
    for (i = 0; i < collection.length; i++) collection[i].checked = checked;
}
//]]>
</script>
<style type="text/css">
/*<![CDATA[*/
table.wide tr.exists { background:#db9; }
/*]]>*/
</style>

<?=formatHTMLTitle('<a href="#" onclick="toggleSection(&quot;sectionList&quot;);return false">Master Products</a>')?>

<div id="sectionList">
    <table class="table_grid">
        <tr>
            <th>&nbsp;</th>
            <th><a href="<?=$sort_url?>id">ID</a><?=('id' == $order_by) ? $ord_img : ''?></th>
            <th style="width:100%;"><a href="<?=$sort_url?>product_name">Product Name</a><?=('product_name' == $order_by) ? $ord_img : ''?></th>
            <th><a href="<?=$sort_url?>active">Active</a><?=('active' == $order_by) ? $ord_img : ''?></th>
            <th><a href="<?=$sort_url?>product_qty">Qty</a><?=('item_qty' == $order_by) ? $ord_img : ''?></th>
            <th><a href="<?=$sort_url?>date_added">Cost<br />Price<br />(USD)</a><?=('cost_price' == $order_by) ? $ord_img : ''?></th>
            <th><a href="<?=$sort_url?>active">Sales<br />Price<br />(USD)</a><?=('sales_price_us' == $order_by) ? $ord_img : ''?></th>
<?php if (!empty($from_warehouse)) { ?>
            <th>Current Warehouse Stock</th>
            <th>Transfer Price</th>
<?php } ?>
            <th>&nbsp;</th>
        </tr>
<?php
foreach ($item_list as $id) {
    $item          = productFromDatabase($id);
    $item_lock     = productGetLock($item['id']);
    $item_reserved = productReservedFromDatabase($item['id']);
    $item_suppliers = productSuppliersFromDatabase($item['id']);
    $item_invoices    = productInvoicesFromDatabase($item['id']);
    $reserved      = 0;
    foreach ($item_reserved as $row) {
        $reserved += $row['qty'];
    }

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

    $cost_price  = convertCurrency($item['cost_price'], $item['cost_currency'], 'USD', false, $dpoints);
    $sales_price = convertCurrency($item['secondary_price'], $item['secondary_currency'], 'USD', false, $dpoints);
    

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

    $styles = '';
    if ('y' == $item['on_special']) {
        $styles = 'color:#f00;';
    }
if($item['colour']==0 || $_REQUEST['return_message']!='addstock'){
?>
        <tr<?=$class?> onmouseover="this.style.backgroundColor='#9DD6D2';" onmouseout="this.style.backgroundColor='';">
            <td style="text-align:center;"><?=('y' == $_REQUEST['multiple'] && !in_array($item['id'], $ignore_ids)) ? ('<input type="checkbox" name="item_list[]" value="' . $item['id'] . '" />') : ''?></td>
            <td style="text-align:center;<?=$styles . (('stock_for_sale' == $item['stock_type']) ? 'background:#BBFFBB;' : (('consignment' == $item['stock_type']) ? 'background:#EEBBEE;' : (('repair_item' == $item['stock_type']) ? 'background:#BBEEEE;' : '')))?>"><?=$item['product_number']?></td>
            <td style="text-align:left;padding-left:4px;<?=$styles?>" onmouseover="<?php hecho('tt_in(' . jecho($product_tooltip, 1) . ');') ?>" onmouseout="tt_out();">
<?php
if ('' != $item['view_url']) {
        echo '<a href="' . hecho($item['view_url'], 1) . '" target="_blank" style="' . $styles . '">';
    }

?>
                <?php hecho($item['product_name'])?>
<?php
if ('' != $item['view_url']) {
        echo '</a>';
    }

?>
            </td>
            <td style="text-align:center;">

                <?php hecho($active_list[$item['active']])?>

            </td>
            <td style="text-align:center;<?=$styles . (($reserved > 0) ? "background:#CACEEF" : '')?>" onmouseover="<?php hecho('tt_in(' . jecho($reserve_tooltip, 1) . ')')?>" onmouseout="tt_out();"><?=$item['qty']?></td>
            <td style="text-align:center;<?=$styles?>"><?=$cost_price?></td>
            <td style="text-align:center;<?=$styles?>"><?=$sales_price?></td>
<?php if (!empty($from_warehouse)) {
    $warehouse_row = isset($warehouse_stock[$item['id']]) ? $warehouse_stock[$item['id']] : array('stock' => 0, 'average_cost' => '0.00');
?>
            <td style="text-align:center;"><?=intval($warehouse_row['stock'])?></td>
            <td style="text-align:center;"><?=htmlspecialchars($warehouse_row['average_cost'])?></td>
<?php } ?>

<?php
$links = array();
    if ($popup_window) {
        $links[] = '<input type="button" value=" Select " onclick="window.location=\'' . $select_url . $item['id'] .'&color_id=0'. '\'"' . ((in_array($item['id'], $ignore_ids)) ? ' disabled="disabled"' : '') . ' />';
    } else {
        if (is_null($item_lock) || $item_lock['staff_id'] == $_GLOBALS['staff']['id']) {
            $links[] = '<a style="' . $styles . '" href="' . $edit_url . $item['id'] . '" target="_blank">Edit</a>';

        } else {
            $links[] = '<a style="' . $styles . '" href="' . $view_url . $item['id'] . '" target="_blank">View</a>';
        }

        if ('stock_sample' != $item['stock_type']) {
            if ('y' == $item['active']) {
                $links[] = '<a style="' . $styles . '" href="' . $deactivate_url . $item['id'] . '" style="' . $styles . '">De-Activate</a>';
            } else {
                $links[] = '<a href="' . $activate_url . $item['id'] . '" style="' . $styles . '">Activate</a>';
            }

        }
        if (~is_null($item_lock) && $item_lock['staff_id'] == $_GLOBALS['staff']['id']) {
            $links[] = '<a style="' . $styles . '" href="' . $unlock_url . $item['id'] . '">Unlock</a>';
        }
    }
?>
            <td style="text-align:center;<?=$styles?>white-space:nowrap;"><?=implode(' | ', $links)?></td>
        </tr>
<?php
    }else{
        foreach(explode(',', $item['colour']) as $col){
        $color_data=productGenericFromDatabase($col,'product_colour') ;
?>
<tr<?=$class?> onmouseover="this.style.backgroundColor='#9DD6D2';" onmouseout="this.style.backgroundColor='';">
            <td style="text-align:center;"><?=('y' == $_REQUEST['multiple'] && !in_array($item['id'], $ignore_ids)) ? ('<input type="checkbox" name="item_list[]" value="' . $item['id'] . '" />') : ''?></td>
            <td style="text-align:center;<?=$styles . (('stock_for_sale' == $item['stock_type']) ? 'background:#BBFFBB;' : (('consignment' == $item['stock_type']) ? 'background:#EEBBEE;' : (('repair_item' == $item['stock_type']) ? 'background:#BBEEEE;' : '')))?>"><?=$item['product_number']?></td>
            <td style="text-align:left;padding-left:4px;<?=$styles?>">
<?php
if ('' != $item['view_url']) {
        echo '<a href="' . hecho($item['view_url'], 1) . '" target="_blank" style="' . $styles . '">';
    }

?>
                <?php hecho($item['product_name'])?>-<?php hecho($color_data['colour']) ?>
<?php
if ('' != $item['view_url']) {
        echo '</a>';
    }

?>
            </td>
            <td style="text-align:center;">

                <?php hecho($active_list[$item['active']])?>

            </td>
            <td style="text-align:center;<?=$styles . (($reserved > 0) ? "background:#CACEEF" : '')?>" onmouseover="<?php hecho('tt_in(' . jecho($reserve_tooltip, 1) . ')')?>" onmouseout="tt_out();"><?=$item['qty']?></td>
            <td style="text-align:center;<?=$styles?>"><?=$cost_price?></td>
            <td style="text-align:center;<?=$styles?>"><?=$sales_price?></td>
<?php if (!empty($from_warehouse)) {
    $warehouse_row = isset($warehouse_stock[$item['id']]) ? $warehouse_stock[$item['id']] : array('stock' => 0, 'average_cost' => '0.00');
?>
            <td style="text-align:center;"><?=intval($warehouse_row['stock'])?></td>
            <td style="text-align:center;"><?=htmlspecialchars($warehouse_row['average_cost'])?></td>
<?php } ?>

<?php
$links = array();
    if ($popup_window) {
        $links[] = '<input type="button" value=" Select " onclick="window.location=\'' . $select_url . $item['id'] .'&color_id='.$color_data['id']. '\'"' . ((in_array($item['id'], $ignore_ids)) ? ' disabled="disabled"' : '') . ' />';
    } else {
        if (is_null($item_lock) || $item_lock['staff_id'] == $_GLOBALS['staff']['id']) {
            $links[] = '<a style="' . $styles . '" href="' . $edit_url . $item['id'] . '" target="_blank">Edit</a>';

        } else {
            $links[] = '<a style="' . $styles . '" href="' . $view_url . $item['id'] . '" target="_blank">View</a>';
        }

        if ('stock_sample' != $item['stock_type']) {
            if ('y' == $item['active']) {
                $links[] = '<a style="' . $styles . '" href="' . $deactivate_url . $item['id'] . '" style="' . $styles . '">De-Activate</a>';
            } else {
                $links[] = '<a href="' . $activate_url . $item['id'] . '" style="' . $styles . '">Activate</a>';
            }

        }
        if (~is_null($item_lock) && $item_lock['staff_id'] == $_GLOBALS['staff']['id']) {
            $links[] = '<a style="' . $styles . '" href="' . $unlock_url . $item['id'] . '">Unlock</a>';
        }
    }
?>
            <td style="text-align:center;<?=$styles?>white-space:nowrap;"><?=implode(' | ', $links)?></td>
        </tr>
<?php
    }
    }
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

<!-- /master_products.tpl -->
