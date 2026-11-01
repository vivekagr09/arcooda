<!-- printlist.tpl -->

<?php
$available_list           = productGetOptionList('available');
$condition_list           = productGetOptionList('condition');
$preferred_supplier_list  = productGetOptionList('preferred_supplier');
?>

<table class="table_grid">
    <tr>
        <th style="width:5%">ID</th>
        <th style="width:10%">Product</th>
        <th style="width:5%">Supplier Code</th>
        <th style="width:5%">Cost</th>
        <th style="width:5%">Sell</th>
        <th style="width:5%">Stk</th>
        <th style="width:5%">Avail. Stock</th>
        <th style="width:5%">Supplier</th>
        <th style="width:10%">Avail.</th>
        <th style="width:5%">S</th>
        <th style="width:5%">L</th>
        <th style="width:5%">D</th>
        <th style="width:5%">C</th>
        <th style="width:5%">Weight</th>
        <th style="width:10%">Dimension (cms)</th>
        <th style="width:5%">SS</th>
        <th style="width:5%">Active</th>
    </tr>
<?php
foreach ($item_list as $id) {
    $current       = productFromDatabase($id);
    $dPoints       = $current['precision'];
    $round         = (4 == $current['precision']) ? false : true;
    $dimen         = productDimensionFromDatabase($current['id']);
    $dimen         = reset($dimen);
    $item_reserved = productReservedFromDatabase($current['id']);
    $reserved      = 0;
    foreach ($item_reserved as $row) {
        $reserved += $row['qty'];
    }
    $dms  = '-';
    $temp = array();
    if ($dimen['width'] > 0) {
        $temp[] = 'W' . $dimen['width'];
    }

    if ($dimen['depth'] > 0) {
        $temp[] = 'D' . $dimen['depth'];
    }

    if ($dimen['height'] > 0) {
        $temp[] = 'H' . $dimen['height'];
    }

    if (count($temp)) {
        $dms = implode(' x ', $temp);
    }

    $stock = $current['qty'] - $reserved;

    $cost  = convertCurrency($current['cost_price'], $current['cost_currency'], 'USD', $round, $dPoints);
    $price = convertCurrency($current['secondary_price'], $current['secondary_currency'], 'USD', $round, $dPoints);
?>
    <tr class="data">
        <td><?=$current['product_number']?></td>
        <td><?php hecho($current['product_name'])?></td>
        <td><?php hecho($current['sup_code'])?></td>
        <td><?=$cost?></td>
        <td><?=$price?></td>
        <td><?=intval($current['qty'])?></td>
        <td><?=$stock?></td>
        <td><?php hecho($preferred_supplier_list[$current['preferred_supplier']])?></td>
        <td><?php hecho($available_list[$current['available']])?></td>
        <td><?=(trim($current['short_desc']) != "") ? "Y" : "N"?></td>
        <td><?=(trim($current['long_desc']) != "") ? "Y" : "N"?></td>
        <td><?=(trim($current['item_details']) != "") ? "Y" : "N"?></td>
        <td><?php hecho($conditions[$current['condition']])?></td>
        <td><?=($dimen['online_weight'] > 0) ? $dimen['online_weight'] : '0.0'?></td>
        <td><?=$dms?></td>
        <td><?=count(productScreenshotsFromDatabase($current['id']))?></td>
        <td><?=ucfirst($current['active'])?></td>
    </tr>
<?php
}
?>
</table>
<br /><br />

<!-- /printlist.tpl -->
