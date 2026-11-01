<?php
include_once "products_common.inc.php";

set_time_limit(300);
moduleLoad('writeexcel');

$product_list_query = $DB->query("SELECT * FROM `products` WHERE `type` = " . intval($posted['type']));

/*
$criteria['site_id'] = 2;
if ($posted['active'] != '') $criteria['active'] = explode('|', $posted['active']);
if ($posted['superseded'] != '') $criteria['superseded'] = $posted['superseded'];
if ($posted['category'] != '') $criteria['category'] = $posted['category'];
if ($posted['product_type'] != '') $criteria['product_type'] = $posted['product_type'];
if ($posted['storage'] != '') $criteria['storage'] = $posted['storage'];
if ($criteria['category'] != '')
{
unset($criteria['category']);
$product_list = productCategorySearch($criteria, $posted['category']);
}
else $product_list = productSearch($criteria);
//$product_list=productNestToList($product_list);
 */
$fname     = tempnam("/tmp", "all_products.xls");
$workbook  = new writeexcel_workbook($fname);
$worksheet = $workbook->addworksheet('Products');
$yesno     = array('y' => 'Yes', 'n' => 'No');
$active    = array('n' => 'No', 'y' => 'Yes', 'cs' => 'Closed - Sold', 'cc' => 'Closed - Cancelled', 'cw' => 'Closed - Supplier Withdrawn');
//$locations = getStorageLocations();
//$availability = getAvailabilityList();
//$condition = getProductCondition();
//$suppliers = getProductSuppliers();
//$countries = getCountryList(false);
//$categories = productGetOptionList('category');
//$list_currency = $posted['currency'];
//if ($list_currency == '') $list_currency = $current_staff['currency'];

$fmt_normal      = &$workbook->addformat(array());
$fmt_header      = &$workbook->addformat(array("bold" => 1, "text_wrap" => 1, "align" => "center", "valign" => "vcenter"));
$fmt_header_vert = &$workbook->addformat(array("bold" => 1, "text_wrap" => 1, "align" => "center", "valign" => "vcenter", "rotation" => 270));
$fmt_center      = &$workbook->addformat(array("align" => "center"));
$fmt_right       = &$workbook->addformat(array("align" => "right"));
$fmt_highlight   = &$workbook->addformat(array("align" => "right", "color" => "red"));

foreach (array("A" => "9.86", "B" => "81.86", "C" => "5.29", "D" => "10.86", "E" => "10.86", "F" => "6.00", "G" => "9.86", "H" => "5.00", "I" => "5.00", "J" => "23.00", "K" => "29.71", "L" => "5.00", "M" => "27.14", "N" => "7.57", "O" => "11.43", "P" => "5.00", "Q" => "20.29", "R" => "69.71", "S" => "5.00", "T" => "2.57", "U" => "5.00", "V" => "44.57", "W" => "7.43", "X" => "9.43", "Y" => "5.00", "Z" => "5.00", "AA" => "5.00", "AB" => "5.00", "AC" => "5.00", "AD" => "5.00", "AE" => "5.00", "AF" => "5.00", "AG" => "5.00", "AH" => "2.57", "AI" => "12.43", "AJ" => "17.71", "AK" => "7.29", "AL" => "8.29", "AM" => "8.29", "AN" => "8.29", "AO" => "5.00", "AP" => "5.00", "AQ" => "5.00") as $k => $v) {
    $worksheet->set_column($k . ":" . $k, $v);
}

//foreach(array("A" => "9.86", "B" => "85.57", "C" => "5.29", "D" => "9.86", "E" => "10.57", "F" => "7.71", "G" => "9.86", "H" => "7.14", "I" => "24.14", "J" => "23.00", "K" => "29.71", "L" => "8.29", "M" => "27.14", "N" => "15.86", "O" => "11.43", "P" => "13.71", "Q" => "26.57", "R" => "69.71", "S" => "14.43", "T" => "11.29", "U" => "18.86", "V" => "44.57", "W" => "21.86", "X" => "21.43", "Y" => "3.57", "Z" => "5.29", "AA" => "3.57", "AB" => "10.57", "AC" => "10.29", "AD" => "14.14", "AE" => "10.00", "AF" => "15.00", "AG" => "10.86", "AH" => "7.86", "AI" => "20.43", "AJ" => "7.29", "AK" => "9.29", "AL" => "15.57", "AM" => "11.14", "AN" => "13.43") as $k => $v) $worksheet->set_column($k . ":" . $k, $v);
$worksheet->set_row(0, "63.00", $fmt_header);

$worksheet->write(0, 0, "Product ID");
$worksheet->write(0, 1, "Product Name");
$worksheet->write(0, 2, "Qty");
$worksheet->write(0, 3, "Cost\nPrice\n(" . $list_currency . ")");
$worksheet->write(0, 4, "Sales\nPrice\n(" . $list_currency . ")");
$worksheet->write(0, 5, "Mark\nUp");
$worksheet->write(0, 6, "Profit");
$worksheet->write(0, 7, "Special", $fmt_header_vert);
$worksheet->write(0, 8, "Active", $fmt_header_vert);
$worksheet->write(0, 9, "Supplier");
$worksheet->write(0, 10, "Category Type");
$worksheet->write(0, 11, "Same As", $fmt_header_vert);
$worksheet->write(0, 12, "Storage Location");
$worksheet->write(0, 13, "Country\nLocation", $fmt_header_vert);
$worksheet->write(0, 14, "Availability");
$worksheet->write(0, 15, "Date\nAvailable", $fmt_header_vert);
$worksheet->write(0, 16, "Condition");
$worksheet->write(0, 17, "Warranty");
$worksheet->write(0, 18, "Product\nPicture", $fmt_header_vert);
$worksheet->write(0, 19, "Screenshots", $fmt_header_vert);
$worksheet->write(0, 20, "Admin\nScreenshots", $fmt_header_vert);
$worksheet->write(0, 21, "Supplier Code");
$worksheet->write(0, 22, "Supplier\nProduct\nName", $fmt_header_vert);
$worksheet->write(0, 23, "Date\nProduct\nReleased");
$worksheet->write(0, 24, "CE", $fmt_header_vert);
$worksheet->write(0, 25, "CTick", $fmt_header_vert);
$worksheet->write(0, 26, "UL", $fmt_header_vert);
$worksheet->write(0, 27, "Short\nDesc.", $fmt_header_vert);
$worksheet->write(0, 28, "Long\nDesc.", $fmt_header_vert);
$worksheet->write(0, 29, "Product\nDetails", $fmt_header_vert);
$worksheet->write(0, 30, "META\nTitle", $fmt_header_vert);
$worksheet->write(0, 31, "META\nKeywords", $fmt_header_vert);
$worksheet->write(0, 32, "META\nDesc.", $fmt_header_vert);
$worksheet->write(0, 33, "Manuals", $fmt_header_vert);
$worksheet->write(0, 34, "Unit\nDimension\n(cm)");
$worksheet->write(0, 35, "Package\nDimension\n(cm)");
$worksheet->write(0, 36, "Actual\nWeight\n(kg)");
$worksheet->write(0, 37, "Volume\nWeight\n(kg)");
$worksheet->write(0, 38, "Shipping\nWeight\n(kg)");
$worksheet->write(0, 39, "Cubic\n(m^3)");
$worksheet->write(0, 40, "Superseded", $fmt_header_vert);
$worksheet->write(0, 41, "Latest\nProduct", $fmt_header_vert);
$worksheet->write(0, 42, "Allow\nInvoice", $fmt_header_vert);

$row_id = 2;

while ($product_list = $DB->fetch_assoc($product_list_query)) {
    $product    = $product_list;
    $product_id = $product['id'];
    $dimen      = $product['dimension'];

    //$shots_query = $DB->query("SELECT * FROM `product_screenshots`")

}
