<!-- __issues_tooltip.tpl -->

<?php
/**
 * Product Issues Tooltip
 *
 * Inputs:
 * - $item
 * - - cost_price
 * - - preferred_supplier
 * - - primary_price
 * - - product_name
 * - - secondary_price
 * - - type
 * - $item_dimension
 * - - width
 * - - depth
 * - - height
 * - - weight
 * - $item_packing[]
 * - - width
 * - - depth
 * - - height
 * - - weight
 * - $item_screenshots[]
 * - - caption
 *
 * Overwrites:
 * - $temp
 * - $temp2
 */
?>

<ul style="white-space:nowrap;">
<?php

//TODO: This was just cut & pasted from hwent

if (0 == $item["type"]) {
?>
    <li>No Category</li>
<?php
}

if (0 == $item["preferred_supplier"]) {
?>
    <li>No Supplier</li>
<?php
}

if (!is_array($item_dimension)) {
?>
    <li>No Unit Size or Weight</li>
<?php
} else {
    if (bccomp($item_dimension["width"], "0.0", 1) <= 0 || bccomp($item_dimension["depth"], "0.0", 1) <= 0 || bccomp($item_dimension["height"], "0.0", 1) <= 0) {
?>
    <li>Invalid Unit Size</li>
<?php
    }
    if (bccomp($item_dimension["weight"], "0.000", 3) <= 0) {
?>
    <li>Invalid Unit Weight</li>
<?php
    }
}
unset($temp);

if (is_null($item_packing) || !count($item_packing)) {
?>
    <li>No Package Sizing or Weight</li>
<?php
} else {
    foreach ($item_packing as $temp) {
        if(isset($temp["width"])){
        if (bccomp($temp["width"], "0.0", 1) <= 0 || bccomp($temp["depth"], "0.0", 1) <= 0 || bccomp($temp["height"], "0.0", 1) <= 0) {
?>
    <li>Invalid Package Size</li>
<?php
        }
        if (bccomp($temp["weight"], "0.000", 3) <= 0) {
?>
    <li>Invalid Package Weight</li>
<?php
        }
    }
}
}
unset($temp);

if (bccomp($item['cost_price'], "0.0000", 4) <= 0) {
?>
    <li>No Cost Price</li>
<?php
}

if (bccomp($item["primary_price"], "0.0000", 4) <= 0 || bccomp($item["secondary_price"], "0.0000", 4) <= 0) {
?>
    <li>No Sell Price</li>
<?php
}

$temp2       = array();
foreach ($item_screenshots as $temp) {
    if (in_array($temp["caption"], $temp2)) {
?>
    <li>Duplicated Screenshot Name</li>
<?php
    }
    if (stripos($temp["caption"], $item["product_name"]) !== false) {
?>
    <li>Screenshot Name Contains Product Name</li>
<?php
    }
    $temp2[] = $temp["caption"];
}
unset($temp);
unset($temp2);

?>
</ul>

<!-- /__issues_tooltip.tpl -->
