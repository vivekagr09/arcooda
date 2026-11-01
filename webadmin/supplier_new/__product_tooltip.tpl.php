<!-- __product_tooltip.tpl -->

<?php
moduleLoad('invoice'); //invoiceSearch, invoiceChunk, invoiceGetValue

$item_default_suppliers = defaultProductSuppliersFromDatabase($item['id']);
/**
 * Product Hover Tooltip
 *
 * Inputs:
 * - $item
 * - - created_at
 * - - man_code
 * - - man_name
 * - - preferred_supplier
 * - - product_name
 * - - product_number
 * - - sup_code
 * - - sup_name
 * - - thumb_url
 * - $item_invoices[]
 * - - invoice_id
 * - - product_qty
 * - $item_suppliers[]
 * - - supplier_id
 *
 * Overwrites:
 * - $invoice_by_time
 * - $invoice_list
 * - $item_invoice_qty
 * - $k
 * - $last_invoice
 * - $sold_1_month
 * - $sold_12_months
 * - $temp
 * - $temp2
 * - $time_1_month
 * - $time_12_months
 * - $time_last_sold
 * - $v
 */
?>

<h2 style="white-space:nowrap;text-align:center;"><?php hecho($item['product_name'])?></h2>

<?php
$item_invoices=[];
if (array_key_exists('thumb_url', $item)) {
?>
<img src="<?php hecho($item['thumb_url'])?>" alt="" style="max-width:96px;max-height:96px;position:absolute;left:8px;" />
<?php
}
?>
<ul style="padding-left:128px;white-space:nowrap;min-height:96px;">
    <li>ID: <?php hecho($item['product_number'])?></li>
<?php
if ('' !== $item['man_code']) {
?>
    <li>Manufacturer Code: <?php hecho($item['man_code'])?></li>
<?php
}
if ('' !== $item['man_name']) {
?>
    <li>Manufacturer Name: <?php hecho($item['man_name'])?></li>
<?php
}

if ('' !== $item['sup_code']) {
?>
    <li>Supplier Code: <?php hecho($item['sup_code'])?></li>
<?php
}
if ('' !== $item['sup_name']) {
?>
    <li>Supplier Name: <?php hecho($item['sup_name'])?></li>
<?php
}
?>
<li>Supplier: <?php hecho(($item_default_suppliers['supplier_name'])?$item_default_suppliers['supplier_name']:'N/A')?></li>
    <li>Date Added: <?=date(_('Y-m-d'), strtotime($item['created_at'])) ?></li>
<?php
if (moduleLoad('invoice')) {

    $item_invoice_qty = array();
    foreach ($item_invoices as $temp) {
        $item_invoice_qty[$temp['invoice_id']] = (array_key_exists($temp['invoice_id'], $item_invoice_qty)) ? $item_invoice_qty[$temp['invoice_id']] + $temp['product_qty'] : $temp['product_qty'];
    }
    unset($temp);

    $time_last_sold = 0;
    $last_invoice   = 0;
    $sold_1_month   = 0;
    $sold_12_months = 0;

    if (count($item_invoice_qty)) {
        $invoice_list = invoiceSearch(array("invoice_id" => array_keys($item_invoice_qty), "invoice_action" => "processed"));
        if (count($invoice_list)) {
            $invoice_by_time = invoiceChunk($invoice_list, "open_time");
            $time_last_sold  = max(array_keys($invoice_by_time));
            $last_invoice    = $invoice_by_time[$time_last_sold];
            $time_1_month    = mktime(0, 0, 0, intval(date("n")) - 1);
            $time_12_months  = mktime(0, 0, 0, intval(date("n")), intval(date("j")), intval(date("Y")) - 1);
            foreach (invoiceChunk($invoice_list, "open_day") as $k => $v) {
                if ($k >= $time_1_month) {
                    foreach ($v as $temp) {
                        $sold_1_month += $item_invoice_qty[$temp];
                    }
                    unset($temp);
                }
                if ($k >= $time_12_months) {
                    foreach ($v as $temp) {
                        $sold_12_months += $item_invoice_qty[$temp];
                    }
                    unset($temp);
                }
            }
            unset($k);
            unset($v);
        }
    }
    if (0 == $time_last_sold) {
?>
    <li>Last Sold: Never</li>
<?php
    } else {
?>
    <li>Last Sold: <?=date(_('Y-m-d'), $time_last_sold)?></li>
    <li>Qty Sold Past 1 Month: <?=$sold_1_month?></li>
    <li>Qty Sold Past 12 Months: <?=$sold_12_months?></li>
<?php
        if (is_array($last_invoice)) {
?>
    <li>Last Invoice:
<?php
            foreach ($last_invoice as $temp) {
?>
        <?=invoiceGetValue($temp, "invoice_number")?>
<?php
            }
            unset($temp);
?>
    </li>
<?php
        }
    }
}
?>
    </ul>
    <br style="clear:left;" />
</div>

<!-- /__product_tooltip.tpl -->
