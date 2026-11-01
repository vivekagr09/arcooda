<!-- _product_summary.tpl -->
<style>
    /* Tooltip container */
	.tooltipdata {
			position: relative;
		display: inline-block;
		cursor: pointer;
	}

	/* Tooltip text */
	.tooltipdata .tooltiptext {
		visibility: hidden;
		width: 300px;
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
moduleLoad('invoice'); //invoiceGetValue, invoiceSearch, invoiceChunk

/**
 * Product Summary Template
 *
 * Inputs:
 * - $item
 * - - created_at
 * - - floor_stock
 * - - on_special
 * - - precision
 * - - preferred_supplier
 * - - primary_currency
 * - - primary_price
 * - - product_costus
 * - - product_name
 * - - product_number
 * - - qty
 * - - secondary_currency
 * - - secondary_price
 * - - view_url
 * - $item_invoices
 * - - invoice_id
 * - - product_costus
 * - - product_offerus
 * - - product_qty
 * - $item_reserved
 * - $item_purchase_summary
 * - - supplier_id
 */

if (!isset($item) || !is_array($item)) {
    return;
}

$preferred_supplier_list = productGetOptionList('preferred_supplier');
$supplier_list           = productGetOptionList('supplier_id');

ob_start();
include __DIR__ . '/__reserve_tooltip.tpl.php';
$reserve_tooltip = ob_get_clean();
function reserve_stock_html($totalstock, $reserve_stock) {
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
$supplier_name = '';
if (array_key_exists($item['preferred_supplier'], $preferred_supplier_list)) {
    $supplier_name = $preferred_supplier_list[$item['preferred_supplier']];
} elseif (isset($item_purchase_summary)) {
    foreach ($item_purchase_summary as $row) {
        if (array_key_exists($row['supplier_id'], $supplier_list)) {
            $supplier_name = $supplier_list[$row['supplier_id']];
            break;
        }
    }
}
?>

<?=formatHTMLTitle('<a href="#" onclick="toggleSection(\'sectionSummary\');return false">Product Summary</a>')?>

<div id="sectionSummary">

<table class="table_fields">
    <tr>
        <th>Product ID :</th>
        <td style="width:25%;" class="highlight-stock-<?= $item['stock_type'] ?>">
<?php
if (array_key_exists('view_url', $item)) {
?>
            <a href="<?php hecho($item["view_url"])?>" target="_blank">
<?php
}
?>
                <?php hecho($item["product_number"])?>
<?php
if (array_key_exists('view_url', $item)) {
?>
            </a>
<?php
}
?>
        </td>
         <th><?= ($item['stock_type'] == 'consignment') ? 'Consignment Stock' : 'Inventory Stock'?> :</th>
          <?php $pro_color=explode(',', $item['colour']);
        if(count($pro_color)>1){
            echo '<td><table style="border:none;"><tr>';
            foreach($pro_color as $key => $colour) {
            $color_data=productGenericFromDatabase($colour,'product_colour') ;
            $stock=inventoryStockGetValue($_REQUEST['id'],$colour, 'qty');
            $reserve_stock=productReservedByColourFromDatabase($_REQUEST['id'],$colour);
            $data_stock_tooltip=reserve_stock_html($stock,$reserve_stock);
?>
        <th style="width:5%;background: #fff;" <?=(count($reserve_stock)) ? ' style="background:#cce;"' : ''?> onmouseover="<?php hecho("tt_in(" . jecho($data_stock_tooltip, 1) . ")")?>" onmouseout="tt_out()">
            <div style="padding:5px;border: 1px solid black;width: 5px; height: 5px;background-color: <?php echo $color_data['color_val'];?>"></div>
        </th>
        <th style="width:15%;text-align: left; <?=(count($reserve_stock)) ? 'background:#CACEEF;' : ''?>" onmouseover="<?php hecho("tt_in(" . jecho($data_stock_tooltip, 1) . ")")?>" onmouseout="tt_out()"><?php echo $stock-productReservedByColourTotalStock($_REQUEST['id'],$colour);?></th>

<?php
            }
            echo '</tr></table></td>';
        }else{
        if($item['stock_type'] == 'consignment') {
?>  
        <td>
                <?=$item['consignment_qty'] ?>
        </td>
    <?php  } else { ?>
             <td <?=(count($item_reserved)) ? ' style="background:#cce;"' : ''?>>
                        
                <div class="tooltipdata">
                    <?=$item['qty'] ?>
                    <div class="tooltiptext">
                    <?= $reserve_tooltip ?>
                    </div>
                </div>
            </td>
    <?php    } } ?>
    </tr>
    <tr>
        <th>Product Name :</th>
        <td>
<?php
if (array_key_exists('view_url', $item)) {
?>
            <a href="<?php hecho($item["view_url"])?>" target="_blank">
<?php
}
?>
                <?php hecho($item["product_name"])?>
<?php
if (array_key_exists('view_url', $item)) {
?>
            </a>
<?php
}
?>
        </td>
        <th>Supplier :</th>
        <td style="width:25%;"><?php hecho(($item_default_suppliers['supplier_name'])?$item_default_suppliers['supplier_name']:'N/A')?></td>
    </tr>
    <tr>
        <th>Cost (USD) :</th>
        <?php 
            if($item['stock_type']!='consignment'){
                $item['product_costus']=productAverageCostPrice($item['product_id']);
            }
        ?>
<?php
if ('consignment' == $item['stock_type']) {
?>
        <td><?=convertCurrency($item['consignment_cost_price'], $item['consignment_cost_currency'], 'USD', false, $item['precision'])?></td>
<?php
} else {
?>
        <td><?=(array_key_exists('product_costus', $item)) ? ('$' . number_format((float)$item['product_costus'], 2, '.', '')) : 'N/A'?></td>
<?php
}
?>
        <th>Supplier Code :</th>
        <td style="width:25%;"><?php hecho(($item_default_suppliers['sup_code'])?$item_default_suppliers['sup_code']:'N/A')?></td>
    </tr>
    <tr>
        <th>Sales price (USD) :</th>
        <?php $selles_price_usd=number_format((float)convertCurrency($item["secondary_price"], $item['secondary_currency'], 'USD', false, $item['precision']), 2, '.', ''); 
        ?>
        <td<?=('y' == $item['special_flag'] AND ($item['special_expiry'] >= date('Y-m-d 00:00:00') OR $item['special_expiry'] == '0000-00-00 00:00:00')) ? ' style="color:red;"' : ''?>>
<?php
if ('consignment' == $item['stock_type']) {
?>
            <span style="color:red;">$<?=$item['special_flag'] == 'y' ? number_format($item['special_priceus'],2) :number_format($item['sales_price'], 2)?></span> <b>Consignment</b>
<?php
} else {
?>
            <span title="Retail Price $<?php hecho(convertCurrency($item["primary_price"], $item['primary_currency'], 'USD', false, $item['precision']))?>">$<?php hecho($selles_price_usd)?> <?=('y' == $item['special_flag'] AND ($item['special_expiry'] >= date('Y-m-d 00:00:00') OR $item['special_expiry'] == '0000-00-00 00:00:00')) ? '(RRP : $'.convertCurrency($item["primary_price"], $item['primary_currency'], 'USD', false, $item['precision']).' )' : ''?></span> <b>Retail</b>
<?php
}
?>
        </td>
        <th></th>
        <td></td>
    </tr>
    <tr >
        <th >Sales price (USD) :</th>
<?php
if ('consignment' == $item['stock_type']) {
?>
        <td>$<?=($item['sales_price_arcooda']) ? number_format((float)($item['sales_price_arcooda']), 2) : '0.000000'?> <b>VIP Member</b></td>
<?php
} else {
?>
        <td>$<?=number_format((float)$item['sales_price_arcooda'], 2)?> <b>VIP Member</b></td>
<?php
}
?>
        <th></th>
        <td></td>
    </tr>
     <tr >
        <th>Sales price (USD) :</th>
<?php
if ('consignment' == $item['stock_type']) {
?>
        <td>$<?=($item['sales_price_distributor']) ? number_format((float)$item['sales_price_distributor'], 2) : '0.000000'?>  <b>Distributor Sample</b></td>
<?php
} else {
?>
        <td>$<?=  number_format((float)$item['sales_price_distributor'], 2)?>  <b>Distributor Sample</b></td>
<?php
}
?>
        <th></th>
        <td></td>
    </tr>
     <tr >
        <th>Sales price (USD) :</th>
<?php
if ('consignment' == $item['stock_type']) {
?>
        <td>$<?=($item['sales_price_container']) ? number_format((float)($item['sales_price_container']), 2) : '0.000000'?>   <b>Distributor Container</b></td>
<?php
} else {
?>
        <td>$<?= number_format((float)$item['sales_price_container'], 2)?>   <b>Distributor Container</b></td>
<?php
}
?>
        <th></th>
        <td></td>
    </tr>
</table>
<br /><br />

<?php
if (count($item_reserved)) {
?>
<div style="text-align:center;">
    <i>If "Stock" highlighted in <span style="background:#CACEEF;">light blue</span>, then there are stocks in reserve for this item. Mouse-over the cell to view more information.</i>
</div>
<br /><br />
<?php
}
?>

<?php
if (isset($item_invoices) && is_array($item_invoices) && moduleLoad('invoice')) {
    $item_sales = array();

    foreach ($item_invoices as $row) {
        $invoice_id = $row["invoice_id"];
        unset($row["invoice_id"]);
        foreach (array("product_costus", "product_offerus") as $key) {
            $row[$key] = bcmul($row[$key], $row["product_qty"], 4);
        }
        if (array_key_exists($invoice_id, $item_sales)) {
            foreach ($row as $k => $v) {
                if(is_numeric($v))
                $item_sales[$invoice_id][$k] += $v;
            }
        } else {
            $item_sales[$invoice_id] = $row;
        }
    }

    $time_1_month   = mktime(0, 0, 0, intval(date("n")) - 1);
    $time_12_months = mktime(0, 0, 0, intval(date("n")), intval(date("j")), intval(date("Y")) - 1);

    $inquiry_list = array_keys($item_sales);
    $invoice_list = invoiceSearch(array("invoice_id" => $inquiry_list, "invoice_status" => "processed"));

    $time_last_inquire = 0;
    $inquire_1_month   = 0;
    $inquire_12_months = 0;
    if (count($inquiry_list) && is_array($inquiry_list)) {
        //$time_last_inquire = max(array_keys(invoiceChunk($inquiry_list, "open_time")));
        foreach (invoiceChunk($inquiry_list, "open_day") as $open_day => $inquiry_list) {
            if ($open_day >= $time_1_month) {
                $inquire_1_month += count($inquiry_list);
            }

            if ($open_day >= $time_12_months) {
                $inquire_12_months += count($inquiry_list);
            }

        }
    }

    $time_last_sold = 0;
    $sold_1_month   = 0;
    $sold_12_months = 0;
    $total_offerus  = '0.0000';
    $total_costus   = '0.0000';
    if (count($invoice_list)) {
        foreach ($invoice_list as $invoice_id) {
            $total_offerus = bcadd($total_offerus, $item_sales[$invoice_id]["product_offerus"], 4);
            $total_costus  = bcadd($total_costus, $item_sales[$invoice_id]["product_costus"], 4);
        }
        $time_last_sold = max(array_keys(invoiceChunk($invoice_list, "open_time")));
        foreach (invoiceChunk($invoice_list, "open_day") as $open_day => $invoice_list) {
            if ($open_day >= $time_1_month) {
                foreach ($invoice_list as $invoice_id) {
                    $sold_1_month += $item_sales[$invoice_id]["product_qty"];
                }
            }

            if ($open_day >= $time_12_months) {
                foreach ($invoice_list as $invoice_id) {
                    $sold_12_months += $item_sales[$invoice_id]["product_qty"];
                }
            }

        }
    }
?>
    <table class="table_fields">
        <tr>
            <th>Time since last inquiry :</th>
            <td style="width:25%;"><?=(0 == $time_last_inquire) ? "Never" : formatSecondsToText(time() - $time_last_inquire)?></td>
            <th>Time since last sold :</th>
            <td style="width:25%;"><?=(0 == $time_last_sold) ? "Never" : formatSecondsToText(time() - $time_last_sold)?></td>
        </tr>
        <tr>
            <th>Total inquiries Past Mth :</th>
            <td><?=$inquire_1_month?></td>
            <th>Total sold Past Mth :</th>
            <td><?=$sold_1_month?></td>
        </tr>
        <tr>
            <th>Total inquiries Past Year :</th>
            <td><?=$inquire_12_months?></td>
            <th>Total sold Past Year :</th>
            <td><?=$sold_12_months?></td>
        </tr>
        <tr>
            <th>Total sales USD :</th>
            <td>$<?=bcadd($total_offerus, "0.0000", $item['precision'])?></td>
            <th>Total profit USD :</th>
            <td>$<?=bcsub($total_offerus, $total_costus, $item['precision'])?></td>
        </tr>
    </table>
    <br /><br />
<?php
}
?>
</div>
<!-- /_product_summary.tpl -->
