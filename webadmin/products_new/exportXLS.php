<?php
/**
 * @fixme This script needs updating to use phpspreadsheet instead of writeexcel
 */
?><?php

include_once __DIR__ . '/common.inc.php';

moduleLoad('writeexcel');

$item_list = productSearch();
if ('stocklist' == $_REQUEST['export']) {
    $item_query = productQuery(array('active' => 'y'));
    $item_list  = productSearch($item_query);
}

if ("stuffed" == $_REQUEST["pricing"]) {
    $temp      = productProblemSearch();
    $item_list = array_intersect($item_list, $temp);
    unset($temp);
}

$fname                 = tempnam("/tmp", "all_products.xls");
$workbook              = new writeexcel_workbook($fname);
$worksheet             = &$workbook->addworksheet('Products');
$storage_list          = productGetOptionList('storage');
$country_location_list = productGetOptionList('country_location');
$warranty_list         = productGetOptionList('warranty');
$available             = productGetOptionList('available');
$condition             = productGetOptionList('condition');
$year_released         = productGetOptionList('year_released');
$yesno                 = array('y' => 'Yes', 'n' => 'No');
//$worksheet->set_column('A:B', 32);
$worksheet->set_column('A:AM', 25);

$supplier_list = productGetOptionList('supplier_id');
$preferred_supplier_list = productGetOptionList('preferred_supplier');

if (count($item_list) > 0) {
    $merged = &$workbook->addformat(array(bold => 1, color => 'black'));
    $merged->set_size(15);
    $merged->set_fg_color('white');
    $merged->set_align('center');
    $merged->set_align('vcenter');
    $merged->set_merge();
    $heading  = &$workbook->addformat(array(bold => 1, color => 'black', size => 12, merge => 1));
    $headings = array(
        'Product ID',
        'Product Name',
        'Category Type',
        'Storage Location',
        'Country Location',
        'Availability',
        'Condition',
        'Warranty',
        'Product Picture',
        'Screenshots',
        'Supplier',
        'Supplier Code',
        'Supplier Product Name',
        'Year Product Released',
        'CE',
        'CTick',
        'UL',
        'ROHS',
        'Short Desc.',
        'Long Desc.',
        'Product Details',
        'META Title',
        'META Keywords',
        'META Desc.',
        'Manuals',
        'Package Dimension',
        'Weight',
        'Cubic',
        'Shipping Weight',
        'Stock Qty',
        'Cost Price',
        'Sales Price',
        'Mark Up',
        'Profit',
        'Special',
        'Superseded',
        'Active',
    );
    $worksheet->write_row('A2', $headings, $heading);
    $start  = 3;
    $format = &$workbook->addformat(array(color => 'black', size => 12, merge => 1, align => 'center'));
    $format->set_text_wrap(1);

    $type_list = productGetOptionList('type');
    foreach ($item_list as $id) {
        $item    = productFromDatabase($id);
        $dPoints = $item['precision'];
        //$pricing = productPricingFromDatabase($id);
        $dimen      = reset(productDimensionFromDatabase($id));
        $shots      = productScreenshotsFromDatabase($id);
        $mans            = productManualsFromDatabase($id);
        $cats       = array();
        foreach (productCategoriesFromDatabase($id) as $row) {
            $cats[] = $type_list[$row['category_id']];
        }

        //print_array($mans);
        $sdesc = ('' != $item['short_desc']) ? 'y' : 'n';
        $ldesc = ('' != $item['long_desc']) ? 'y' : 'n';
        $pdets = ('' != $item['item_details']) ? 'y' : 'n';
        $mttle = ('' != $item['meta_title']) ? 'y' : 'n';
        $mkeys = ('' != $item['meta_keywords']) ? 'y' : 'n';
        $mdesc = ('' != $item['meta_description']) ? 'y' : 'n';
        $worksheet->write('A' . $start, $id, $format);
        $worksheet->write('B' . $start, $item['product_name'], $format);
        $worksheet->write('C' . $start, implode(', ', $cats), $format);
        $worksheet->write('D' . $start, $storage_list[$item['storage']], $format);
        $worksheet->write('E' . $start, $country_location_list[$item['country_location']], $format);
        $worksheet->write('F' . $start, $available[$item['available']], $format);
        $worksheet->write('G' . $start, $condition[$item['condition']], $format);
        $worksheet->write('H' . $start, $warranty_list[$item['warranty']], $format);
        $dir       = $_SERVER['DOCUMENT_ROOT'] . '/images/products/thumbnails/';
        $itemImage = 'No';
        if ('' != $item['image'] && is_file($dir . $item['image'])) {
            $itemImage = 'Yes';
        }

        $worksheet->write('I' . $start, $itemImage, $format);
        $worksheet->write('J' . $start, count($shots), $format);

        $supplier_cell = array();
        if (0 != $item['preferred_supplier']) {
            $supplier_cell[] = $preferred_supplier_list[$item['preferred_supplier']];
        } elseif (0 != $item['supplier_id']) {
            $supplier_cell[] = $supplier_list[$item['supplier_id']];
        }

        foreach (productSuppliersFromDatabase($id) as $row) {
            if (0 != $row['supplier_id']) {
                $supplier_cell[] = $supplier_list[$row['supplier_id']];
            }
        }
        $supplier_cell = array_unique($supplier_cell);

        $worksheet->write('K' . $start, implode(", ", $supplier_cell), $format);
        $worksheet->write('L' . $start, $item['sup_code'], $format);
        $worksheet->write('M' . $start, $item['sup_name'], $format);
        $worksheet->write('N' . $start, $year_released[$item['year_released']], $format);
        $worksheet->write('O' . $start, $yesno[$item['ce']], $format);
        $worksheet->write('P' . $start, $yesno[$item['ctick']], $format);
        $worksheet->write('Q' . $start, $yesno[$item['rohs']], $format);
        $worksheet->write('R' . $start, $yesno[$item['ul']], $format);
        $worksheet->write('S' . $start, $yesno[$sdesc], $format);
        $worksheet->write('T' . $start, $yesno[$ldesc], $format);
        $worksheet->write('U' . $start, $yesno[$pdets], $format);
        $worksheet->write('V' . $start, $yesno[$mttle], $format);
        $worksheet->write('W' . $start, $yesno[$mkeys], $format);
        $worksheet->write('X' . $start, $yesno[$mdesc], $format);
        $worksheet->write('Y' . $start, count($mans), $format);
        $dimenStr = $dimen['pack_width'] . 'x' . $dimen['pack_depth'] . 'x' . $dimen['pack_height'] . ' cms';
        $worksheet->write('Z' . $start, $dimenStr, $format);
        $worksheet->write('AA' . $start, $dimen['weight'], $format);
        $worksheet->write('AB' . $start, $dimen['cubic'], $format);
        $worksheet->write('AC' . $start, $dimen['online_weight'], $format);
        $worksheet->write('AD' . $start, $item['qty'], $format);
        $worksheet->write('AE' . $start, ('$' . convertCurrency($item['cost_price'], $item['cost_currency'], 'USD', false, $dPoints)), $format);
        $worksheet->write('AF' . $start, ('$' . convertCurrency($item['secondary_price'], $item['secondary_currency'], 'USD', false, $dPoints)), $format);
        $worksheet->write('AG' . $start, ((0 != $item['cost_price']) ? floor(($item['item_offerus'] - $item['cost_price']) * 100 / convertCurrency($item['cost_price'], $item['cost_currency'], 'USD', false)) : '0') . '%', $format);
        $worksheet->write('AH' . $start, ('$' . convertCurrency($item['item_offerus'] - convertCurrency($item['cost_price'], $item['cost_currency'], 'USD', false), 'USD', '', false, $dPoints)), $format);
        $worksheet->write('AI' . $start, $yesno[$item['special_flag']], $format);
        $worksheet->write('AJ' . $start, $yesno[$item['superseded']], $format);
        $worksheet->write('AK' . $start, $yesno[$item['active']], $format);
        $start++;
    }
}
$workbook->close();

header('Content-Type: application/vnd.ms-excel; name="all_products.xls"');
header('Content-Disposition: inline; filename="all_products.xls"');
readfile($fname);
unlink($fname);
