<?php
include_once __DIR__ . '/common.inc.php';

moduleLoad('invoice'); //invoiceProductsFromDatabase, invoiceFromDatabase

include_once __DIR__ . '/header.inc.php';
$id  = intval($_GET['id']);
$colour_id  = intval($_GET['color_id']);
$query     = $DB->query(sprintf(
    "SELECT * FROM `products_inventory` where product_id=".$id." and color_id=".$colour_id." ORDER BY `id` DESC"
));
?>
    <?=formatHTMLTitle('Inventory History')?>
    <table class="table_grid">
        <tr style="background:#E3EBF7;text-align:center;">
            <td style="width:80px;"><b>Date</b></td>
            <td style="width:80px;"><b>Movement</b></td>
            <td style="width:<?=(has_permission('STOCK', $allowed_sections)) ? '65' : '70'?>%"><b>Description</b></td>
            <td style="width:80px;"><b>Available</b></td>
<?php
if (has_permission('STOCK', $allowed_sections)) {
?>
            <td style="width:80px;"><b>Edit Pricing</b></th>
<?php
}
?>
    </tr>
<?php

    $alt = false;
    while ($row = $DB->fetch_assoc($query)) {
        $comments = hecho($row['comments'], 1);
        if (preg_match('/(ECO|ICO|PI|I)#([0-9]+)/', $comments, $m)) {
            $invoice_id = intval(ltrim($m[2], '0'));
            $invoice    = invoiceFromDatabase($invoice_id);
            if ('' != $invoice['id']) {
                $invoice_products = invoiceProductsFromDatabase($invoice_id);

                ob_start();
                include __DIR__ . '/__inventory_invoice_tooltip.tpl.php';
                $invoice_tooltip = ob_get_clean();

                $comments = str_replace($m[0], '<a href="/webadmin/invoice/?action=view&amp;invoice_id=' . $invoice_id . '" target="_blank" onmouseover="' . hecho('tt_in(' . jecho($invoice_tooltip, 1) . ')', 1) . '" onmouseout="tt_out()">' . $m[0] . '</a>', $comments);
            }
        }
?>
        <tr style="text-align:center;" <?=($alt = !$alt) ? 'class="alt"' : ''?>>
            <td style="white-space:nowrap;"><?= date(_('Y-m-d'), strtotime($row['created_at'])) ?></td>
            <td style="white-space:nowrap;">
<?php
                    if($row['color_id']){
                        $color_data=productGenericFromDatabase($row['color_id'],'product_colour') ;
?>
                    <span style="height:12px;width: 15px; border-radius: 50%; display: inline-block; text-align: center; padding-top: 3px; margin-right: 4px!important;background-color: <?php echo $color_data['color_val'];?>"></span><?php echo sprintf('%+d', $row['movement']);?>
<?php
                    }else{
                        echo sprintf('%+d', $row['movement']);
                    }
?>

            </td>
            <td style="text-align:left;"><?=$comments?></td>
            <td style="white-space:nowrap;"><?=$row['available']?></td>
<?php
        if (has_permission('STOCK', $allowed_sections) && $row['movement'] > 0) {
?>
            <td style="white-space:nowrap;">
                <input class="unfinished" type="button" value="EDIT"<?=($row['stock_left'] <= 0) ? ' disabled="disabled"' : ''?> onclick="openWindow('item_modify_inventory.php?inventory_id=<?=$row['id']?>', 800, 300, true);return false" />
            </td>
<?php
        }
?>
        </tr>
<?php
    }
?>
    </table>
    <br />
    <br />
<?php
$DB->free_result($query);
include_once __DIR__ . '/footer.inc.php';
