<?php
/**
 * @fixme This script needs updating to use phpspreadsheet instead of writeexcel
 */
?><?php

include_once __DIR__ . '/common.inc.php';

moduleLoad('writeexcel');

// $item_list = productSearch();

$fname                 = tempnam("/tmp", "metatag_category.xls");
$workbook              = new writeexcel_workbook($fname);
$worksheet             = &$workbook->addworksheet('Products');
$yesno                 = array('y' => 'Yes', 'n' => 'No');
//$worksheet->set_column('A:B', 32);
$worksheet->set_column('A:J', 25);

if (count($item_list) > 0) {
    $merged = &$workbook->addformat(array(bold => 1, color => 'black'));
    $merged->set_size(15);
    $merged->set_fg_color('white');
    $merged->set_align('center');
    $merged->set_align('vcenter');
    $merged->set_merge();
    $heading  = &$workbook->addformat(array(bold => 1, color => 'black', size => 12, merge => 1));
    $headings = array(
        'Parent Type',
        'Category/Title',
        'URL',
        'Meta Description',
        'Meta Keyword',
        'Top 5 Keywords',
        'SEO URL',
        'SEO Description',
        'SEO Keyword'
    );
    $worksheet->write_row('A2', $headings, $heading);
    $start  = 3;
    $format = &$workbook->addformat(array(color => 'black', size => 12, merge => 1, align => 'center'));
    $format->set_text_wrap(1);

    // $worksheet->write('A' . $start, , $format);
    // $worksheet->write('B' . $start, $item['product_name'], $format);
    // $worksheet->write('C' . $start, implode(', ', $cats), $format);
    // $worksheet->write('D' . $start, $storage_list[$item['storage']], $format);
    // $worksheet->write('E' . $start, $country_location_list[$item['country_location']], $format);
    // $worksheet->write('F' . $start, $available[$item['available']], $format);
    // $worksheet->write('G' . $start, $condition[$item['condition']], $format);
    // $worksheet->write('H' . $start, $warranty_list[$item['warranty']], $format);

}
$workbook->close();

header('Content-Type: application/vnd.ms-excel; name="all_products.xls"');
header('Content-Disposition: inline; filename="all_products.xls"');
readfile($fname);
unlink($fname);
