<?php
include_once __DIR__ . '/common.inc.php';
$supplier_id      = intval($_REQUEST['supplier_id']);
$product_id      = intval($_REQUEST['product_id']);
$item_inventory        = productInventoryFromDatabase($product_id);
$supplier_list = productGetOptionList('supplier_id');
if (isset($item_inventory) && is_array($item_inventory)) {
    foreach ($item_inventory as $row) {
        if ($row['supplier_id'] == $supplier_id) {
            $temp[] = $row;
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Purchase Data</title>
    <link rel="stylesheet" href="/webadmin/admin.css" type="text/css" />
    <link rel="stylesheet" href="style.css" type="text/css" />
</head>
<body>
<style type="text/css">
    .border-class-table  td {
  border: 1px solid black;
  text-align: center;
}
.border-class-table  th {
  border: 1px solid black;
  text-align: center;
}
</style>
<link rel="stylesheet" type="text/css" href="style.css" />
<div class="product_content">
    <table class="table_grid border-class-table" style="width:100%">
        <tr><th colspan="8"><b>Purchase Summary - <?=$supplier_list[$supplier_id]?></b></th></tr>
        <tr>
            <th style="background:#EEF;width:5%"><b>Date</b></th>
            <th style="background:#EEF;width:5%"><b>Qty</b></th>
            <th style="background:#EEF;width:10%"><b>Unit Cost (USD)</b></th>
            <th style="background:#EEF;width:15%"><b>Unit Freight Cost (USD)</b></th>
            <th style="background:#EEF;width:10%"><b>Unit Total Cost (USD)</b></th>
            <th style="background:#EEF;width:10%"><b>Unit Order Cost (USD)</b></th>
            <th style="background:#EEF;width:45%"><b>Comments</b></th>
        </tr>
        <?php
        $count = 0;
        foreach ($temp as $row) {
            $count++;
            ob_start();
            ?>
            Original Price: <?= convertCurrency($row['unit_cost'], $row['unit_currency'], '', false, $item['precision']) ?> <?= $row['unit_currency'] ?>
            <?php
                        $tt_in = ob_get_clean();

                        ob_start();
            ?>
            Original Freight: <?= convertCurrency($row['unit_freight'], $row['unit_currency'], '', false, $item['precision']) ?> <?= $row['unit_currency'] ?>
            <?php
                        $tt_in2 = ob_get_clean();
            ?>
            <tr style="text-align:center;" class="<?=($alt = !$alt)?(($count>5)?'alt hide-row row-hide-check-sup-'.$supplier['supplier_id'].'':'alt') : (($count>5)?' hide-row row-hide-check-sup-'.$supplier['supplier_id'].'':'')?>">
                <td><?=date(_('d/m/Y'), (is_null($row['created_at'])) ? time() : strtotime($row['created_at']))?></td>
                <td>
                    <?php
                      if($row['color_id']){
                        $color_data=productGenericFromDatabase($row['color_id'],'product_colour') ;
                    ?>
                    <span onmouseover="<?php hecho("tt_in(" . jecho($color_data['colour'], 1) . ")") ?>" onmouseout="tt_out()" style="height:12px;width: 15px; border-radius: 50%; display: inline-block; text-align: center; padding-top: 3px; margin-right: 4px!important;background-color: <?php echo $color_data['color_val'];?>" ></span>
                    <?php } ?>
                    <?=$row['movement']?>
                    

                </td>
                <td onmouseover="<?php hecho("tt_in(" . jecho($tt_in, 1) . ")") ?>" onmouseout="tt_out()">$<?php 
                    echo number_format((float)  convertCurrency($row['unit_cost'], $row['unit_currency'], 'USD'), 2, '.', '');

                ?></td>
                <td onmouseover="<?php hecho("tt_in(" . jecho($tt_in2, 1) . ")") ?>" onmouseout="tt_out()">$<?php
               
                    echo number_format((float) convertCurrency($row['unit_freight'], $row['unit_currency'], 'USD'), 2, '.', '');
                ?></td>
                <td>
                    <?php 
                    $unit_total_cost=$row['unit_cost']+$row['unit_freight'];
                    $unit_total_cost = convertCurrency($unit_total_cost, $row['unit_currency'], 'USD');
                    ?>
                   $<?php 
                   echo number_format((float) $unit_total_cost, 2, '.', '');
                   ?>
                </td>
                <td>
                    <?php 
                    $unit_freight_total_cost=$unit_total_cost*$row['movement'];

                    ?>
                    $<?php //$cost=convertCurrency($unit_freight_total_cost, $row['currency'], 'USD', false, $item['precision']);
                        echo number_format((float) $unit_freight_total_cost, 2, '.', '');
                    ?>
                </td>
                <?php
                    // Find position of $supplier_name in $comment
                    $position = strpos($row['comments'], $supplier_list[$row_data['supplier_id']]);

                    // Ensure $supplier_name was found in $comment
                    if ($position !== false) {
                        // Extract the substring after $supplier_name
                        $purchase_info = substr($row['comments'], $position + strlen($supplier_list[$row_data['supplier_id']]));

                        // Trim to remove any leading or trailing spaces and commas
                        $purchase_info = trim($purchase_info, ", ");
                    } else {
                        $purchase_info = $row['comments'];
                    }
                ?>
                <td style="text-align:left;"><?=nl2br($purchase_info)?></td>
            </tr>
        <?php
        }
        ?>
    </table>
</div>
</body>
</html>