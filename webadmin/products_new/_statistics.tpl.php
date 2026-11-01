<!-- _statistics.tpl -->

<?php
/**
 * Product Statistics Template
 *
 * Inputs:
 * - $item_invoices
 */

moduleLoad('invoice'); //invoiceGetValue, invoiceSort

if (!isset($item_invoices) || !is_array($item_invoices)) {
    return;
}

$last_ordered = 0;
$total_sales = 0;
$temp = array();

if (count($item_invoices) && moduleLoad('invoice')) {
    $invoices         = array();
    $invoice_products = array();
    foreach ($item_invoices as $item_invoice) {
        $total_sales += $item_invoice['product_qty'];
        $invoices[$item_invoice['invoice_id']] = $item_invoice['invoice_id'];
        if (array_key_exists($item_invoice['invoice_id'], $invoice_products)) {
            $invoice_products[$item_invoice['invoice_id']] += $item_invoice['product_qty'];
        } else {
            $invoice_products[$item_invoice['invoice_id']] = $item_invoice['product_qty'];
        }
    }
    asort($invoices);
    foreach ($invoice_products as $invoice_id => $item_qty) {
        $temp[] = sprintf(
            '<span style="white-space:nowrap;"><a href="/webadmin/invoice/?action=view&amp;invoice_id=%s" target="_blank">%s</a> x %d</span>',
            $invoice_id,
            hecho(invoiceGetValue($invoice_id, 'invoice_number'), 1),
            $item_qty
        );
    }
    $last_ordered = invoiceGetValue(end(invoiceSort($invoices, 'open_date')), 'open_time');
} else {
    foreach ($item_invoices as $item_invoice) {
        $total_sales += $item_invoice['product_qty'];
        $temp[] = 'Invoice #' . $item_invoice['invoice_id'] . ': x' . $item_invoice['product_qty'];
    }
}
?>

<?=formatHTMLTitle('<a href="#" onclick="toggleSection(&quot;sectionStatistics&quot;);return false">Statistics</a>')?>

<div id="sectionStatistics">
    <table class="table_fields">
        <tr>
            <th>Last Ordered :</th>
            <td><?=(0 != $last_ordered) ? date(_('Y-m-d'), $last_ordered) : 'Never'?></td>
        </tr>
        <tr>
            <th>Total Unit Sales :</th>
            <td><?=$total_sales?></td>
        </tr>
        <tr>
            <th>Summary of Customer Orders :</th>
            <td><div style="max-height:250px;overflow-y:scroll;">
<?php
if (count($temp) == 0) {
    echo 'No Orders Found';
} else {
    echo implode(', ', $temp);
}
?>
            </div></td>
        </tr>
    </table>
    <br /><br />
</div>

<!-- /_statistics.tpl -->
