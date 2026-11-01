<?php
moduleLoad('product'); //productFromDatabase

$item_list=totalSupplierInventoryFromDatabase($supplier_id);
   $inventory_list_url='';
foreach($item_list as $list){
    $inventory_list_url .= (empty($inventory_list_url))?('?product_id[]='.$list.''):('&product_id[]='.$list.'');
}
$linkurl=(count($item_list)>0)?"window.open('/webadmin/products_new/list.php".$inventory_list_url."')":'return false';
$edit_url = '/webadmin/products_new?' . http_build_query(array(
    'action' => 'edit'
));
$active_list = productGetOptionList('active');
?>
<?=formatHTMLTitle('<a href="#" onclick="toggleSection(\'purchase_inventory_stock\');return false">Purchase Inventory (Stock)</a>')?>
<div id="purchase_inventory_stock">
	 <table class="table_grid">
	 	 <tr>
            <th>&nbsp;</th>
            <th>ID</th>
            <th style="width:100%;">Product Name</th>
            <th>Active</th>
            <th>Qty</th>
            <th>Cost Price (USD)</th>
            <th>Sales Price (USD)</th>
            <th>&nbsp;</th>
        </tr>
	<?php 
		$count=0;
		foreach ($item_list as $id){
			$count++;
			if($count>5){
				break;
			}
			 $item             = productFromDatabase($id);
			    $item_dimension   = productDimensionFromDatabase($item['id']);

			    $item_invoices    = productInvoicesFromDatabase($item['id']);
			    $item_lock        = productGetLock($item['id']);
			    $item_packing     = productPackingFromDatabase($item['id']);
			    $item_reserved    = productReservedFromDatabase($item['id']);
			    $item_screenshots = productScreenshotsFromDatabase($item['id']);
			    $item_suppliers = productSuppliersFromDatabase($item['id']);
			    $reserved = 0;
			    foreach ($item_reserved as $row)
			    {
			        $reserved += $row['qty'];
			    }

			    ob_start();
			    include  $_SERVER['DOCUMENT_ROOT'] . '/webadmin/products_new/__product_tooltip.tpl.php'; 
			    $product_tooltip = ob_get_clean();

			    ob_start();
			    include  $_SERVER['DOCUMENT_ROOT'] . '/webadmin/products_new/__reserve_tooltip.tpl.php';
			    $reserve_tooltip = ob_get_clean();

			    ob_start();
			    include  $_SERVER['DOCUMENT_ROOT'] . '/webadmin/products_new/__issues_tooltip.tpl.php';
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

			    if ('' != $class)
			    {
			        $class = ' class="' . $class . '"';
			    }

			    $styles = '';
			    if ('y' == $item['on_special'])
			    {
			        $styles = 'color:#f00;';
			    }
			?>
			<tr<?=$class?> onmouseover="this.style.backgroundColor='#9DD6D2';" onmouseout="this.style.backgroundColor='';">
                <td style="text-align:center;<?=(array_key_exists('superseded', $item) && 'y' == $item['superseded']) ? 'background:#edf;' : '' ?>"><?=(array_key_exists('multiple', $_REQUEST) && 'y' == $_REQUEST['multiple']) ? ('<input type="checkbox" name="item_list[]" value="' . $item['id'] . '" />') : '' 
                ?>
            </td>
                <td style="text-align:center;<?=$styles . (('stock_for_sale' == $item['stock_type']) ? 'background:#BBFFBB;' : (('consignment' == $item['stock_type']) ? 'background:#EEBBEE;' : (('repair_item' == $item['stock_type']) ? 'background:#BBEEEE;' : ''))) ?>"><?=$item['product_number'] ?>
            </td>
                <td style="text-align:left;padding-left:4px;<?=$styles ?>">

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
                    <span  style="height:1.4em;display:block;overflow:hidden;"><?php hecho($item['product_name']) ?></span>
<?php
        if (array_key_exists('view_url', $item))
        {
            echo '</a>';
        }

?>
                </td>
                <td style="text-align:center;">

                    <?php hecho($active_list[$item['active']]) ?>

                </td>
                <td style="text-align:center;<?=$styles . (($reserved > 0) ? "background:#CACEEF" : '') ?>" onmouseover="<?php hecho('tt_in(' . jecho($reserve_tooltip, 1) . ');') ?>" onmouseout="tt_out();"><?=$item['qty'] ?></td>
                <td style="text-align:center;<?=$styles ?>"><?=$cost_price ?></td>
                <td style="text-align:center;<?=$styles ?>"><?=$sales_price ?></td>

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
                $links[] = '<a style="' . $styles . '" href="' . $edit_url .'&id='. $item['id'] . '" target="_blank">Edit</a>';
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
	<?php
		}
	?>
</table>
<div style="text-align:center;">
            <input type="button" value="View All (<?= count($item_list) ?>)" onclick="<?= $linkurl ?>" />
        </div>
</div>