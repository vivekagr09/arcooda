<!-- __reserve_tooltip.tpl -->

<?php
moduleLoad('invoice'); //invoiceGetValue

/**
 * Product Reservation Tooltip
 *
 * Inputs:
 * - $item
 * - - qty
 * - $item_reserved[]
 * - - invoice_id
 * - - qty
 *
 * Overwrites:
 * - $temp
 * - $temp2
 */
?>

<table>
<?php
$temp2 = 0;
if (count($item_reserved)) {
?>
    <tr>
        <th>Invoice ID</th>
        <th>Customer Name</th>
        <th>Reserved Stock</th>
    </tr>
<?php
    foreach ($item_reserved as $temp) {
        $temp2 += $temp['qty'];
?>
    <tr>
        <td><?php hecho(invoiceGetValue($temp['invoice_id'], 'invoice_number'))?></td>
        <td><?php hecho(invoiceGetValue($temp['invoice_id'], 'bill_name'))?></td>
        <td><?php hecho($temp['qty'])?></td>
    <tr>
<?php
    }
    unset($temp);
}
?>
    <tr>
        <td></td>
        <td><b>Total Reserved :</b></td>
        <td><b><?php hecho($temp2)?></b></td>
    </tr>
    <tr>
        <td></td>
        <td><b>Available Stock :</b></td>
        <td><b><?php hecho($item['qty'] - $temp2)?></b></td>
    </tr>
</table>
<?php
unset($temp2);

?>

<!-- /__reserve_tooltip.tpl -->
