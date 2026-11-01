<!-- _products.tpl -->

<style type="text/css">
div.product_content tr.specialprice a ,div.product_content tr.specialprice td  {
    color: #f00 !important;
}

    /* Tooltip container */
    .tooltipdata {
			position: relative;
		display: inline-block;
		cursor: pointer;
	}

	/* Tooltip text */
	.tooltipdata .tooltiptext {
		visibility: hidden;
		width: 250px;
		text-align: left;
		border-radius: 5px;
		padding: 5px;
		position: absolute;
		z-index: 1;
		opacity: 0;
		transition: opacity 0.3s;
		background-color: #FFFFE1;
		color: #000000;
		border-color: #000000;
		border-width: 1px;
		border-style: solid;
	}

	/* Show the tooltip text when you mouse over the tooltip container */
	.tooltipdata:hover .tooltiptext {
		visibility: visible;
		opacity: 1;
	}

    .tooltiptable {
        border: none !important;
        background:none !important;
    }
</style>
<?php
moduleLoad('invoice'); //invoiceGetValue

/**
 * Product List Template
 *
 * Inputs:
 * - $item_list
 */
function reserve_stock_html($totalstock,$reserve_stock){
        ob_start();
    echo '<table>';
    $temp2 = 0;
    if (count($reserve_stock)) {
?>

    <tr>
        <th>Invoice ID</th>
        <th>Customer Name</th>
        <th>Reserved Stock</th>
    </tr>
<?php
    foreach ($reserve_stock as $temp) {
        $temp2 += $temp['qty'];
?>
     <tr>
        <td style="text-align: center;"><?php hecho(invoiceGetValue($temp['invoice_id'], 'invoice_number'))?></td>
        <td style="text-align: center;"><?php hecho(invoiceGetValue($temp['invoice_id'], 'bill_name'))?></td>
        <td style="text-align: center;"><?php hecho($temp['qty'])?></td>
    <tr>
<?php
        }
    }
    unset($temp);
?>
    <tr>
        <td></td>
        <td><b>Total Reserved :</b></td>
        <td><b><?php hecho($temp2)?></b></td>
    </tr>
    <tr>
        <td></td>
        <td><b>Available Stock :</b></td>
        <td><b><?php hecho($totalstock - $temp2)?></b></td>
    </tr>
</table>
<?php
    unset($temp2);
    return ob_get_clean();
}
if (!isset($item_list) || !is_array($item_list))
{
    return;
}

if (!moduleLoad('invoice'))
{
    return;
}

$active_list = productGetOptionList('active');

$alt = false;
$ord_img = '<i class="fas fa-sort-' . (('desc' == $order) ? 'down' : 'up') . '"></i>';

$action_url = $_GET;
unset($action_url['id']);
$action_url['id'] = '';
$activate_url = '.?' . http_build_query(array(
    'action' => 'activate'
) + $action_url);
$copy_url = '.?' . http_build_query(array(
    'action' => 'copy'
) + $action_url);
$deactivate_url = '.?' . http_build_query(array(
    'action' => 'deactivate'
) + $action_url);
$delete_url = '.?' . http_build_query(array(
    'action' => 'delete'
) + $action_url);
$edit_url = '.?' . http_build_query(array(
    'action' => 'edit'
) + $action_url);
$select_url = '.?' . http_build_query(array(
    'action' => 'select'
) + $action_url);
$unlock_url = '.?' . http_build_query(array(
    'action' => 'unlock'
) + $action_url);
$view_url = '.?' . http_build_query(array(
    'action' => 'view'
) + $action_url);
$sort_url = $_GET;
unset($sort_url['order_by']);
$sort_url['order_by'] = '';
$sort_url = '.?' . http_build_query(array(
    'order' => ('desc' == $order) ? 'asc' : 'desc'
) + $sort_url);

?>

<script type="text/javascript">
//<![CDATA[
function selectAll(checked) {
    var collection = getElements('item_list[]');
    for (i = 0; i < collection.length; i++) collection[i].checked = checked;
}
//]]>
</script>

<?=formatHTMLTitle('<a href="#" onclick="toggleSection(\'sectionProducts\');return false">Products</a>') ?>

<?php
$filter_url = $_GET;
unset($filter_url['stock_type']);
unset($filter_url['active']);
unset($filter_url['discontinued']);
$filter_url = '?' . http_build_query($filter_url);
?>
<form action="<?php hecho($filter_url) ?>" method="get"><div style="width:95%;background:#eee;margin:auto;padding:8px;">
    <input type="hidden" name="discontinued" value="n" />
    <input type="checkbox" name="stock_type" id="filter_stock_type" value="stock_for_sale"<?=('stock_for_sale' !== $_GET['stock_type']) ? '' : ' checked="checked"' ?> /> <label for="filter_stock_type">Stock Only</label>
    <input type="checkbox" name="active" id="filter_active" value="y"<?=('y' !== $_GET['active']) ? '' : ' checked="checked"' ?> /> <label for="filter_active">Active Only</label>
    <input type="checkbox" name="discontinued" id="filter_discontinued" value="y|n"<?=(strpos($_GET['discontinued'], 'y') !== false) ? ' checked="checked"' : '' ?> /> <label for="filter_discontinued">Include Discontinued</label>
    <input type="submit" value="Update" />
</div></form>

<div id="sectionProducts">
<?php
$select_url = $_GET;
$select_url['action'] = 'select';
$select_url = '.?' . http_build_query($select_url);
?>
    <form action="<?php hecho($select_url) ?>" method="post">
    <table class="table_grid">
        <tr>
            <th>&nbsp;</th>
            <th><a href="<?php hecho($sort_url) ?>id">ID</a><?=('id' == $order_by) ? $ord_img : '' ?></th>
            <th style="width:100%;"><a href="<?php hecho($sort_url) ?>product_name">Product Name</a><?=('product_name' == $order_by) ? $ord_img : '' ?></th>
            <th><a href="<?php hecho($sort_url) ?>active">Active</a><?=('active' == $order_by) ? $ord_img : '' ?></th>
            <th><a href="<?php hecho($sort_url) ?>qty">Qty</a><?=('qty' == $order_by) ? $ord_img : '' ?></th>
            <th><a href="<?php hecho($sort_url) ?>cost_price">Cost<br />Price<br />(USD)</a><?=('cost_price' == $order_by) ? $ord_img : '' ?></th>
            <th><a href="<?php hecho($sort_url) ?>secondary_price">Sales<br />Price<br />(USD)</a><?=('secondary_price' == $order_by) ? $ord_img : '' ?></th>
            <th>&nbsp;</th>
        </tr>
<?php
$keyid=1;
foreach ($item_list as $id)
{
    $item             = productFromDatabase($id);
    $item_dimension   = productDimensionFromDatabase($item['id']);

    $item_invoices    = productInvoicesFromDatabase($item['id']);
    $item_lock        = productGetLock($item['id']);
    $item_packing     = productPackingsFromDatabase($item['id'],null);
    $item_reserved    = productReservedFromDatabase($item['id']);
    $item_screenshots = productScreenshotsFromDatabase($item['id']);
    $item_suppliers   = productSuppliersFromDatabase($item['id']);
    $sub_product      = subProductFromDatabase($item['id']);
    $reserved = 0;
    foreach ($item_reserved as $row)
    {
        $reserved += $row['qty'];
    }

    ob_start();
    include __DIR__ . '/__product_tooltip.tpl.php';
    $product_tooltip = ob_get_clean();

    ob_start();
    include __DIR__ . '/__reserve_tooltip.tpl.php';
    $reserve_tooltip = ob_get_clean();

    ob_start();
    include __DIR__ . '/__issues_tooltip.tpl.php';
    $issues_tooltip = ob_get_clean();
    $dpoints = 2;
    if (array_key_exists('precision', $item))
    {
        $dpoints = $item['precision'];
    }

    $cost_price = convertCurrency($item['cost_price'], $item['cost_currency'], 'USD', false, $dpoints);
    $sales_price = convertCurrency($item['secondary_price'], $item['secondary_currency'], 'USD', false, $dpoints);

    $class = '';
    if ($alt = !$alt)
    {
        $class = 'alt';
    }

    if (!is_null($item_lock))
    {
        $class = 'locked';
        if ($item_lock['staff_id'] == $_GLOBALS['staff']['id'])
        {
            $class = 'mylock';
        }
    }

    if (!is_null($item['updated_at']) && formatDaysOpen(strtotime($item['updated_at'])) == 0)
    {
        $class = 'highlight';
    }

    if (formatDaysOpen(strtotime($item['created_at'])) == 0)
    {
        $class = 'highlight';
    }
    $classtext='';
    if('y' == $item['special_flag'] AND ($item['special_expiry'] >= date('Y-m-d 00:00:00') OR $item['special_expiry'] == '0000-00-00 00:00:00')){
        $classtext = 'specialprice';
    }
    if ('' != $class || '' != $classtext)
    {
        $class = ' class="' . $class . ' '.$classtext.'"';
    }

    $styles = '';
    if ('y' == $item['on_special'])
    {
        $styles = 'color:#f00;';
    }
    if($item['colour']==0){
?>
    <?php if(count($sub_product)>0){ 
        foreach ($sub_product as $key => $suppro) { ?>
           <tr<?=$class?> onmouseover="this.style.backgroundColor='#9DD6D2';" onmouseout="this.style.backgroundColor='';" >
                <td style="text-align:center;<?=(array_key_exists('superseded', $item) && 'y' == $item['superseded']) ? 'background:#edf;' : '' ?>"><?=(array_key_exists('multiple', $_REQUEST) && 'y' == $_REQUEST['multiple']) ? ('<input type="checkbox" name="item_list[]" value="' . $item['id'] . '" />') : '' 
                    ?>
                </td>
                <td style="text-align:center;<?=$styles . (('stock_for_sale' == $item['stock_type']) ? 'background:#BBFFBB;' : (('consignment' == $item['stock_type']) ? 'background:#EEBBEE;' : (('repair_item' == $item['stock_type']) ? 'background:#BBEEEE;' : ''))) ?>"><?=$item['product_number'].strtoupper(substr($suppro['name'], 0, 3)); ?>
                </td>
                    <td style="text-align:left;padding-left:4px;<?=$styles ?>">
<?php
                $classIcon = '';
                if ('invoice_for_everyone' == $item['order_rule']) {
                    $classIcon = 'invoiced';
                } elseif ('invoice_only_for_restricted_owners' == $item['order_rule']) {
                    $classIcon = 'restricted';
                } elseif ('inquiry_for_everyone' == $item['order_rule']) {
                    $classIcon = 'enquiry';
                }
?>
                        <span title="<?=strtr($item['order_rule'], ['_' => ' '])?>" class="f-right circlecart <?=$classIcon?>"><i class="fa fa-shopping-cart" aria-hidden="true"></i></span>
                <?php

                        if ($item['promotion'])
                        {
                ?>
                        <span style="float:right;" onmouseover="<?php hecho('tt_in(' . jecho('Promotional Offer', 1) . ')') ?>" onmouseout="tt_out()"><i class="fas fa-gift"></i></span>
                <?php
                        }

                   
                        if ($item["time_added"] < mktime(0, 0, 0, date("n") , date("j") , date("Y") - 1) && 0 == $sold_12_months)
                        {
                ?>
                                    <span style="float:right;" onmouseover="<?php hecho('tt_in(' . jecho('No Sales Last 12 Months', 1) . ')') ?>" onmouseout="tt_out()"><i class="fas fa-dollar-sign"></i></span>
                <?php
                        }

                ?>
                <span style="float:right;" onmouseover="<?php hecho('tt_in(' . jecho($issues_tooltip, 1) . ')') ?>" onmouseout="tt_out()"><i class="fas fa-exclamation-triangle"></i></span>
                <?php

                        if ($item['inventory_check_by'])
                        {
                            $inventory_check_by_list = productGetOptionList('inventory_check_by');
                ?>
                                    <span style="float:right;" onmouseover="<?php hecho('tt_in(' . jecho('Inventory Check ' . date(_('Y-m-d') , strtotime($item['inventory_check_at'])) . ' (by ' . hecho($inventory_check_by_list[$item['inventory_check_by']], 1) , 1) . ')') ?>" onmouseout="tt_out()"><i class="fas fa-check-circle"></i></span>
                <?php
                        }
                        if (array_key_exists('view_url', $item))
                        {
                            echo '<a href="' . hecho($item['view_url'], 1) . '" target="_blank" style="' . $styles . '">';
                        }

                ?>
                        <span onmouseover="<?php hecho('tt_in(' . jecho($product_tooltip, 1) . ');') ?>" onmouseout="tt_out();" style="height:1.4em;display:block;overflow:hidden;"><?php hecho($item['product_name'].'-'.$suppro['name']) ?></span>
                <?php
                        if (array_key_exists('view_url', $item))
                        {
                            echo '</a>';
                        }

                ?>
                                </td>
                                <td style="text-align:center;">
                <?php
                        if ('stock_sample' != $item['stock_type'])
                        {
                            echo '<a href="' . (('y' == $item['active']) ? $deactivate_url : $activate_url) . $item['id'] . '" style="' . $styles . '">';
                        }

                ?>
                        <?php hecho($active_list[$item['active']]) ?>
                <?php
                        if ('stock_sample' != $item['stock_type'])
                        {
                            echo '</a>';
                        }

                ?>
                    </td>
                    <td style="text-align:center;<?=$styles . (($reserved > 0) ? "background:#CACEEF" : '') ?>" onmouseover="<?php hecho('tt_in(' . jecho($reserve_tooltip, 1) . ');') ?>" onmouseout="tt_out();"><?=$item['qty'] ?></td>
<?php
                if ('consignment' == $item['stock_type']) {
?>
                    <td style="text-align:center;<?=$styles?>"><?=convertCurrency($item['consignment_cost_price'], $item['consignment_cost_currency'], 'USD', false, $item['precision'])?></td>
<?php
                } else {
?>
                    <td style="text-align:center;<?=$styles ?>"><?=number_format($suppro['price_usd'], 2); ?></td>
<?php
                }
                if ('consignment' == $item['stock_type']) {
?>
                    <td><?=$item['consignment_sales_price']?></td>
<?php
                } else {
?>
                    <td><?=number_format($suppro['sell_price'], 2); ?></td>
<?php
                }
?>
                <?php
                        $links = array();
                        if ($popup_window)
                        {
                            $links[] = '<input type="button" value=" Select " onclick="window.location=\'' . $select_url.'&id=' . $item['id'] . '\'" />';
                        }
                        else
                        {
                            if (is_null($item_lock) || $item_lock['staff_id'] == $_GLOBALS['staff']['id'])
                            {
                                $links[] = '<a style="' . $styles . '" href="' . $edit_url . $item['id'] . '" target="_blank">Edit</a>';
                                //$links[]='<a href="'.$delete_url.$item['id'].'" target="_blank">Delete</a>';

                            }
                            else
                            {
                                $links[] = '<a style="' . $styles . '" href="' . $view_url . $item['id'] . '" target="_blank">View</a>';
                            }

                            if ('stock_sample' != $item['stock_type'])
                            {
                                if ('y' == $item['active'])
                                {
                                    $links[] = '<a style="' . $styles . '" href="' . $deactivate_url . $item['id'] . '" style="' . $styles . '">De-Activate</a>';
                                }
                                else
                                {
                                    $links[] = '<a href="' . $activate_url . $item['id'] . '" style="' . $styles . '">Activate</a>';
                                }

                            }
                            if (is_null($item_lock) && $item_lock['staff_id'] == $_GLOBALS['staff']['id'])
                            {
                                $links[] = '<a style="' . $styles . '" href="' . $unlock_url . $item['id'] . '">Unlock</a>';
                            }
                            //$links[]='<a href="'.$copy_url.$item['id'].'" target="_blank">New</a>';

                        }
                ?>
                    <td style="text-align:center;<?=$styles?>white-space:nowrap;"><?=implode(' | ', $links) ?></td>
        </tr> 
    <?php } 
    }else{ ?>
        <tr<?=$class?> onmouseover="this.style.backgroundColor='#9DD6D2';" onmouseout="this.style.backgroundColor='';" >
            <td style="text-align:center;<?=(array_key_exists('superseded', $item) && 'y' == $item['superseded']) ? 'background:#edf;' : '' ?>"><?=(array_key_exists('multiple', $_REQUEST) && 'y' == $_REQUEST['multiple']) ? ('<input type="checkbox" name="item_list[]" value="' . $item['id'] . '" />') : '' 
                ?>
            </td>
            <td style="text-align:center;<?=$styles . (('stock_for_sale' == $item['stock_type']) ? 'background:#BBFFBB;' : (('consignment' == $item['stock_type']) ? 'background:#EEBBEE;' : (('repair_item' == $item['stock_type']) ? 'background:#BBEEEE;' : ''))) ?>"><?=$item['product_number'] ?>
            </td>
                <td style="text-align:left;padding-left:4px;<?=$styles ?>">
<?php
            $classIcon = '';
            if ('invoice_for_everyone' == $item['order_rule']) {
                $classIcon = 'invoiced';
            } elseif ('invoice_only_for_restricted_owners' == $item['order_rule']) {
                $classIcon = 'restricted';
            } elseif ('inquiry_for_everyone' == $item['order_rule']) {
                $classIcon = 'enquiry';
            }
?>
          <span title="<?=strtr($item['order_rule'], ['_' => ' '])?>" class="f-right circlecart <?=$classIcon?>"><i class="fa fa-shopping-cart" aria-hidden="true"></i></span>
<?php
                    if ($item['promotion'])
                    {
            ?>
                    <span style="float:right;" onmouseover="<?php hecho('tt_in(' . jecho('Promotional Offer', 1) . ')') ?>" onmouseout="tt_out()"><i class="fas fa-gift"></i></span>
            <?php
                    }

               
                    if ($item["time_added"] < mktime(0, 0, 0, date("n") , date("j") , date("Y") - 1) && 0 == $sold_12_months)
                    {
            ?>
                                <span style="float:right;" onmouseover="<?php hecho('tt_in(' . jecho('No Sales Last 12 Months', 1) . ')') ?>" onmouseout="tt_out()"><i class="fas fa-dollar-sign"></i></span>
            <?php
                    }

            ?>
            <span style="float:right;" onmouseover="<?php hecho('tt_in(' . jecho($issues_tooltip, 1) . ')') ?>" onmouseout="tt_out()"><i class="fas fa-exclamation-triangle"></i></span>
            <?php

                    if ($item['inventory_check_by'])
                    {
                        $inventory_check_by_list = productGetOptionList('inventory_check_by');
            ?>
                                <span style="float:right;" onmouseover="<?php hecho('tt_in(' . jecho('Inventory Check ' . date(_('Y-m-d') , strtotime($item['inventory_check_at'])) . ' (by ' . hecho($inventory_check_by_list[$item['inventory_check_by']], 1) , 1) . ')') ?>" onmouseout="tt_out()"><i class="fas fa-check-circle"></i></span>
            <?php
                    }
                    if (array_key_exists('view_url', $item))
                    {
                        echo '<a href="' . hecho($item['view_url'], 1) . '" target="_blank" style="' . $styles . '">';
                    }

            ?>
                    <span onmouseover="<?php hecho('tt_in(' . jecho($product_tooltip, 1) . ');') ?>" onmouseout="tt_out();" style="height:1.4em;display:block;overflow:hidden;"><?php hecho($item['product_name']) ?></span>
            <?php
                    if (array_key_exists('view_url', $item))
                    {
                        echo '</a>';
                    }

            ?>
                            </td>
                            <td style="text-align:center;">
            <?php
                    if ('stock_sample' != $item['stock_type'])
                    {
                        echo '<a href="' . (('y' == $item['active']) ? $deactivate_url : $activate_url) . $item['id'] . '" style="' . $styles . '">';
                    }

            ?>
                    <?php hecho($active_list[$item['active']]) ?>
            <?php
                    if ('stock_sample' != $item['stock_type'])
                    {
                        echo '</a>';
                    }

            ?>
                </td>
                <!-- <td style="text-align:center;<?=$styles . (($reserved > 0) ? "background:#CACEEF" : '') ?>" onmouseover="<?php hecho('tt_in(' . jecho($reserve_tooltip, 1) . ');') ?>" onmouseout="tt_out();"><?=$item['qty'] ?></td> -->
                <td style="text-align:center;<?=$styles . (($reserved > 0) ? "background:#CACEEF" : '') ?>">
                        
                    <div class="tooltipdata">
                        <?=$item['qty'] ?>
                        <div class="tooltiptext">
                        <?= $reserve_tooltip ?>
                        </div>
                    </div>
                </td>
<?php
            if ('consignment' == $item['stock_type']) {
?>
                <td style="text-align:center;<?=$styles ?>"><?=convertCurrency($item['consignment_cost_price'], $item['consignment_cost_currency'], 'USD', false, $item['precision'])?></td>
<?php
            } else {
?>
                <td style="text-align:center;<?=$styles ?>"><?=number_format($cost_price, 2); ?></td>
<?php
            }
            if ('consignment' == $item['stock_type']) {
?>
                <td><?=($item['special_flag'] == 'y') ? $item['special_priceus'] : $item['consignment_sales_price']?></td>
<?php
            } else {
?>
                <td><?=number_format($sales_price, 2); ?></td>
<?php
            }
?>

            <?php
                    $links = array();
                    if ($popup_window)
                    {
                        $links[] = '<input type="button" value=" Select " onclick="window.location=\'' . $select_url.'&id=' . $item['id'] . '\'" />';
                    }
                    else
                    {
                        if (is_null($item_lock) || $item_lock['staff_id'] == $_GLOBALS['staff']['id'])
                        {
                            $links[] = '<a style="' . $styles . '" href="' . $edit_url . $item['id'] . '" target="_blank">Edit</a>';
                            //$links[]='<a href="'.$delete_url.$item['id'].'" target="_blank">Delete</a>';

                        }
                        else
                        {
                            $links[] = '<a style="' . $styles . '" href="' . $view_url . $item['id'] . '" target="_blank">View</a>';
                        }

                        if ('stock_sample' != $item['stock_type'])
                        {
                            if ('y' == $item['active'])
                            {
                                $links[] = '<a style="' . $styles . '" href="' . $deactivate_url . $item['id'] . '" style="' . $styles . '">De-Activate</a>';
                            }
                            else
                            {
                                $links[] = '<a href="' . $activate_url . $item['id'] . '" style="' . $styles . '">Activate</a>';
                            }

                        }
                        if (is_null($item_lock) && $item_lock['staff_id'] == $_GLOBALS['staff']['id'])
                        {
                            $links[] = '<a style="' . $styles . '" href="' . $unlock_url . $item['id'] . '">Unlock</a>';
                        }
                        //$links[]='<a href="'.$copy_url.$item['id'].'" target="_blank">New</a>';

                    }
            ?>
                <td style="text-align:center;<?=$styles?>white-space:nowrap;"><?=implode(' | ', $links) ?></td>
    </tr>

    <?php } ?>
<?php
}else{
foreach(explode(',', $item['colour']) as $col){
    $color_data=productGenericFromDatabase($col,'product_colour') ;
    $stock=inventoryStockGetValue($item['id'],$col, 'qty');
    $reserve=productReservedByColourTotalStock($item['id'],$col);
    $reserve_stock=productReservedByColourFromDatabase($item['id'],$col);
    $data_stock_tooltip=reserve_stock_html($stock,$reserve_stock);
    $stock=$stock-$reserve;

?>

<tr<?=$class?> onmouseover="this.style.backgroundColor='#9DD6D2';" onmouseout="this.style.backgroundColor='';">
                <td style="text-align:center;<?=(array_key_exists('superseded', $item) && 'y' == $item['superseded']) ? 'background:#edf;' : '' ?>"><?=(array_key_exists('multiple', $_REQUEST) && 'y' == $_REQUEST['multiple']) ? ('<input type="checkbox" name="item_list[]" value="' . $item['id'] . '" />') : '' ?></td>
                <td style="text-align:center;<?=$styles . (('stock_for_sale' == $item['stock_type']) ? 'background:#BBFFBB;' : (('consignment' == $item['stock_type']) ? 'background:#EEBBEE;' : (('repair_item' == $item['stock_type']) ? 'background:#BBEEEE;' : ''))) ?>"><?=$item['product_number'] ?><?php hecho($color_data['code3']) ?></td>
                <td style="text-align:left;padding-left:4px;<?=$styles ?>">
<?php
            $classIcon = '';
            if ('invoice_for_everyone' == $item['order_rule']) {
                $classIcon = 'invoiced';
            } elseif ('invoice_only_for_restricted_owners' == $item['order_rule']) {
                $classIcon = 'restricted';
            } elseif ('inquiry_for_everyone' == $item['order_rule']) {
                $classIcon = 'enquiry';
            }
?>
          <span title="<?=strtr($item['order_rule'], ['_' => ' '])?>" class="f-right circlecart <?=$classIcon?>"><i class="fa fa-shopping-cart" aria-hidden="true"></i></span> 
<?php
        if ($item['promotion'])
        {
?>
                    <span style="float:right;" onmouseover="<?php hecho('tt_in(' . jecho('Promotional Offer', 1) . ')') ?>" onmouseout="tt_out()"><i class="fas fa-gift"></i></span>
<?php
        }
        if ($item["time_added"] < mktime(0, 0, 0, date("n") , date("j") , date("Y") - 1) && 0 == $sold_12_months)
        {
?>
                    <span style="float:right;" onmouseover="<?php hecho('tt_in(' . jecho('No Sales Last 12 Months', 1) . ')') ?>" onmouseout="tt_out()"><i class="fas fa-dollar-sign"></i></span>
<?php
        }
?>
                    <span style="float:right;" onmouseover="<?php hecho('tt_in(' . jecho($issues_tooltip, 1) . ')') ?>" onmouseout="tt_out()"><i class="fas fa-exclamation-triangle"></i></span>
<?php
        if ($item['inventory_check_by'])
        {
            $inventory_check_by_list = productGetOptionList('inventory_check_by');
?>
                    <span style="float:right;" onmouseover="<?php hecho('tt_in(' . jecho('Inventory Check ' . date(_('Y-m-d') , strtotime($item['inventory_check_at'])) . ' (by ' . hecho($inventory_check_by_list[$item['inventory_check_by']], 1) , 1) . ')') ?>" onmouseout="tt_out()"><i class="fas fa-check-circle"></i></span>
<?php
        }
        if (array_key_exists('view_url', $item))
        {
            echo '<a href="' . hecho($item['view_url'], 1) . '" target="_blank" style="' . $styles . '">';
        }

?>
                    <span onmouseover="<?php hecho('tt_in(' . jecho($product_tooltip, 1) . ');') ?>" onmouseout="tt_out();" style="height:1.4em;display:block;overflow:hidden;"><?php hecho($item['product_name']) ?>-<?php hecho($color_data['colour']) ?></span>
<?php
        if (array_key_exists('view_url', $item))
        {
            echo '</a>';
        }

?>
                </td>
                <td style="text-align:center;">
<?php
        if ('stock_sample' != $item['stock_type'])
        {
            echo '<a href="' . (('y' == $item['active']) ? $deactivate_url : $activate_url) . $item['id'] . '" style="' . $styles . '">';
        }

?>
                    <?php hecho($active_list[$item['active']]) ?>
<?php
        if ('stock_sample' != $item['stock_type'])
        {
            echo '</a>';
        }

?>
                </td>
                <td style="text-align:center;<?=$styles . (($reserve > 0) ? "background:#CACEEF" : '') ?>" onmouseover="<?php hecho('tt_in(' . jecho($data_stock_tooltip, 1) . ');') ?>" onmouseout="tt_out();"><?=$stock ?></td>
<?php
            if ('consignment' == $item['stock_type']) {
?>
                <td style="text-align:center;<?=$styles ?>"><?=convertCurrency($item['consignment_cost_price'], $item['consignment_cost_currency'], 'USD', false, $item['precision'])?></td>
<?php
            } else {
?>
                <td style="text-align:center;<?=$styles ?>"><?=number_format($cost_price, 2); ?></td>
<?php
            }
?>
                <td style="text-align:center;<?=$styles ?>"><?=number_format($sales_price, 2);  ?></td>

<?php
        $links = array();
        if ($popup_window)
        {
            $links[] = '<input type="button" value=" Select " onclick="window.location=\'' . $select_url .'&id='. $item['id'] . '\'" />';
        }
        else
        {
            if (is_null($item_lock) || $item_lock['staff_id'] == $_GLOBALS['staff']['id'])
            {
                $links[] = '<a style="' . $styles . '" href="' . $edit_url . $item['id'] . '" target="_blank">Edit</a>';
                //$links[]='<a href="'.$delete_url.$item['id'].'" target="_blank">Delete</a>';

            }
            else
            {
                $links[] = '<a style="' . $styles . '" href="' . $view_url . $item['id'] . '" target="_blank">View</a>';
            }

            if ('stock_sample' != $item['stock_type'])
            {
                if ('y' == $item['active'])
                {
                    $links[] = '<a style="' . $styles . '" href="' . $deactivate_url . $item['id'] . '" style="' . $styles . '">De-Activate</a>';
                }
                else
                {
                    $links[] = '<a href="' . $activate_url . $item['id'] . '" style="' . $styles . '">Activate</a>';
                }

            }
            if (is_null($item_lock) && $item_lock['staff_id'] == $_GLOBALS['staff']['id'])
            {
                $links[] = '<a style="' . $styles . '" href="' . $unlock_url . $item['id'] . '">Unlock</a>';
            }
            //$links[]='<a href="'.$copy_url.$item['id'].'" target="_blank">New</a>';

        }
?>
                <td style="text-align:center;<?=$styles?>white-space:nowrap;"><?=implode(' | ', $links) ?></td>
            </tr>

<?php } } $keyid++; }
if (array_key_exists('multiple', $_REQUEST) && 'y' == $_REQUEST['multiple'])
{
?>
        <tr<?=!$alt ? ' class="alt"' : '' ?>><td colspan="2"><input type="checkbox" name="select_all" onclick="selectAll(this)" /><br />Select All</td><td colspan="8">&nbsp;</td></tr>
<?php
}
?>
    </table>
</form>
    <br /><br />

<?php
if (!$popup_window)
{
?>
    <div style="text-align:center;">
        <span style="font-style:italic;">Notes:</span><br />
        Rows highlighted in <span class="highlight-modified">green</span> have been modified today<br />
        Rows highlighted in <span class="highlight-locked-other">red</span> are locked by another user<br />
        Rows highlighted in <span class="highlight-locked">blue</span> are locked by you<br />
        Products coloured in <span class="highlight-special">red</span> are on special<br />
        Cells highlighted in <span class="highlight-superseded">light purple</span> are superseded<br />
        Cells highlighted in <span class="highlight-stock-stock_for_sale">light green</span> are products in stock.<br />
        Cells highlighted in <span class="highlight-stock-repair_item">light cyan</span> are repair products.<br />
        Cells highlighted in <span class="highlight-stock-consignment">light purple</span> are consignment products.<br />
        Cells highlighted in <span class="highlight-stock-stock_sample">light orange</span> are sample products.<br />
    </div>
    <br /><br />
<?php
}
elseif ($popup_window)
{
?>
    <div style="text-align:center;">
        Rows highlighted in <span style="background:#DFC299">brown</span> are already linked with this product or is the same product
    </div>
<?php
}
if (array_key_exists('multiple', $_REQUEST) && 'y' == $_REQUEST['multiple'])
{
?>
    <p style="text-align:center;">
        <input type="submit" value="Select Products" />
    </p>
    <br /><br />
<?php
}
?>
</div>

<div style="color:#fff;text-align:center;padding:8px;display:none;" id="sectionProducts_hideNote">
    Product list can be viewed by Clicking "Products" above<br />
</div>

<!-- /_products.tpl -->
