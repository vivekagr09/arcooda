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

<table style="border:none;">
<?php
$temp2 = 0;
if (count($item_reserved)) {
?>
    <tr style="background:none;">
        <th class="tooltiptable">Invoice ID</th>
        <th class="tooltiptable">Customer Name</th>
        <th class="tooltiptable">Reserved Stock</th>
    </tr>
<?php
    foreach ($item_reserved as $temp) {
        $temp2 += $temp['qty'];
?>
    <tr style="background:none;">
        <td class="tooltiptable"><a href="/webadmin/invoice/invoice_edit.php?search_for=<?= $temp['invoice_id']?>&invoice_id=<?= $temp['invoice_id']?>" target="_blank"><?php hecho(invoiceGetValue($temp['invoice_id'], 'invoice_number'))?></a></td>
        <td class="tooltiptable"><?php hecho(invoiceGetValue($temp['invoice_id'], 'bill_name'))?></td>
        <td class="tooltiptable"><?php hecho($temp['qty'])?></td>
    <tr>
<?php
    }
    unset($temp);
}
?>
    <tr style="background:none;">
        <td style="border:none;"></td>
        <td style="border:none;"><b>Total Reserved :</b></td>
        <td style="border:none;"><b><?php hecho($temp2)?></b></td>
    </tr>
    <tr style="background:none;">
        <td style="border:none;"></td>
        <td style="border:none;"><b>Available Stock :</b></td>
        <td style="border:none;"><b><?php hecho($item['qty'] - $temp2)?></b></td>
    </tr>
</table>
<?php
unset($temp2);

?>

<!-- /__reserve_tooltip.tpl -->
