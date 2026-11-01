<?php
include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/ProductTabulator.php';

set_time_limit(300);

$display_currency = $_POST['currency'] ?? $_GET['currency'] ?? $_GLOBALS['site_settings']['default_currency'] ?? 'USD';

$criteria = array();
foreach ($_GET as $k => $v) {
    $criteria[$k] = (is_string($v)) ? preg_split('/[^a-z0-9]+/', $v) : $v;
}
foreach ($_POST as $k => $v) {
    $criteria[$k] = (is_string($v)) ? preg_split('/[^a-z0-9]+/', $v) : $v;
}

$item_list = [];
if ('stocklist' == $_REQUEST['export']) {
    $item_query = productQuery(array('active' => 'y'));
    $item_list  = productSearch($item_query);
} else {
    $item_list = productSearch(productQuery($criteria));
}
if ("stuffed" == $_REQUEST["pricing"]) {
    $temp      = productProblemSearch();
    $item_list = array_intersect($item_list, $temp);
    unset($temp);
}

$yesno_list = array(
    'y' => 'Yes',
    'n' => 'No',
);

$active_list             = productGetOptionList('active');
$availability_list       = productGetOptionList('availability');
$category_list           = productGetOptionList('category');
$ce_list                 = productGetOptionList('ce');
$condition_list          = productGetOptionList('condition');
$country_location_list   = productGetOptionList('country_location');
$ctick_list              = productGetOptionList('ctick');
$on_special_list         = productGetOptionList('on_special');
$preferred_supplier_list = productGetOptionList('preferred_supplier');
$rohs_list               = productGetOptionList('rohs');
$storage_list            = productGetOptionList('storage');
$superseded_list         = productGetOptionList('superseded');
$supplier_list           = productGetOptionList('supplier_id');
$ul_list                 = productGetOptionList('ul');
$warranty_list           = productGetOptionList('warranty');
$year_released_list      = productGetOptionList('year_released');

$image_dir = productGetOptionList('image_path');

$table = new ProductTabulator();

foreach ($item_list as $item_str) {
    $item       = productFromDatabase($item_str);
    $item_id    = $item['id'];
    $sub_id     = $item['sub_id'];
    $precision  = $item['precision'] ?? $item['decimal'] ?? $item['dPoints'] ?? 2;
    $on_special = $item['on_special'] ?? 'n';

    $row = array();

    $row['Product ID']   = $item['product_number'];
    $row['Product Name'] = $item['product_name'];

    $cats = array();
    foreach (productCategoriesFromDatabase($item_str) as $item_category) {
        $cats[] = $category_list[$item_category['category_id']];
    }
    $cats = array_unique($cats);
    $row['Category Type'] = implode(', ', $cats);

    $row['Storage Location'] = $storage_list[$item['storage']];
    $row['Country Location'] = $country_location_list[$item['country_location']];
    $row['Availability']     = $availability_list[$item['availability']];
    $row['Condition']        = $condition_list[$item['condition']];
    $row['Warranty']         = $warranty_list[$item['warranty']];
    $row['Product Picture']  = $yesno_list[(empty($item['image']) || !is_file($image_dir . '/' . $item['image'])) ? 'n' : 'y'];

    $row['Screenshots'] = count(productScreenshotsFromDatabase($item_str));

    $supplier_cell = array();
    if (0 != $item['preferred_supplier']) {
        $supplier_cell[] = $preferred_supplier_list[$item['preferred_supplier']];
    } elseif (0 != $item['supplier_id']) {
        $supplier_cell[] = $supplier_list[$item['supplier_id']];
    }
    foreach (productSuppliersFromDatabase($item_str) as $item_supplier) {
        if (0 != $item_supplier['supplier_id']) {
            $supplier_cell[] = $supplier_list[$item_supplier['supplier_id']];
        }
    }
    $supplier_cell = array_unique($supplier_cell);
    $row['Supplier'] = implode(", ", $supplier_cell);

    $row['Supplier Code']         = $item['sup_code'];
    $row['Supplier Product Name'] = $item['sup_name'];

    $row['Year Product Released'] = $year_released_list[$item['year_released']];

    $row['CE']              = $ce_list[$item['ce']];
    $row['CTick']           = $ctick_list[$item['ctick']];
    $row['UL']              = $ul_list[$item['ul']];
    $row['ROHS']            = $rohs_list[$item['rohs']];
    $row['Short Desc.']     = $yesno_list[('' != $item['short_desc']) ? 'y' : 'n'];
    $row['Long Desc.']      = $yesno_list[('' != $item['long_desc']) ? 'y' : 'n'];
    $row['Product Details'] = $yesno_list[('' != $item['item_details']) ? 'y' : 'n'];
    $row['META Title']      = $yesno_list[('' != $item['meta_title']) ? 'y' : 'n'];
    $row['META Keywords']   = $yesno_list[('' != $item['meta_keywords']) ? 'y' : 'n'];
    $row['META Desc.']      = $yesno_list[('' != $item['meta_description']) ? 'y' : 'n'];

    $row['Manuals'] = count(productManualsFromDatabase($item_str));

    $item_dimensions = productDimensionFromDatabase($item_str);
    $item_dimension = $item_dimensions;
    if (is_array(reset($item_dimensions))) {
        $item_dimension = reset($item_dimensions);
    }

    $row['Package Dimension'] = sprintf('%sx%sx%s cms', $item_dimension['pack_width'], $item_dimension['pack_depth'], $item_dimension['pack_height']);
    $row['Weight']            = $item_dimension['weight'];
    $row['Cubic']             = $item_dimension['cubic'];
    $row['Shipping Weight']   = $item_dimension['online_weight'];

    $row['Stock Qty'] = $item['qty'];

    $cost_price_xxx = convertCurrency($item['cost_price'], $item['cost_currency'], $display_currency, false, $precision);
    $sale_price_xxx = convertCurrency($item['secondary_price'], $item['secondary_currency'], $display_currency, false, $precision);
    $profit_xxx     = bcsub($sale_price_xxx, $cost_price_xxx, $precision);
    $markup_xxx     = (bccomp($cost_price_xxx, '0.0000', 4)) ? (bcmul(bcdiv($profit_xxx, $cost_price_xxx, 2), '100', 0) . '%') : '';

    $row["Cost Price (" . $display_currency . ")"]  = (bccomp($cost_price_xxx, '0.0000', $precision)) ? sprintf('$%s', $cost_price_xxx) : '';
    $row["Sales Price (" . $display_currency . ")"] = (bccomp($sale_price_xxx, '0.0000', $precision)) ? sprintf(
        '%s$%s%1$s',
        ('y' == $on_special) ? '==' : '',
        $sale_price_xxx
    ) : '';

    $row["Mark Up"] = ('y' == $item['price_flux']) ? ($item['markup'] . '%') : $markup_xxx;

    $row["Profit (" . $display_currency . ")"] = (bccomp($cost_price_xxx, '0.0000', $precision)) ? sprintf(
        '%s$%s%1$s',
        (bccomp($profit_xxx, '0.0000', $precision) < 0) ? '==' : '',
        $profit_xxx
    ) : '';

    $row['Special']    = $on_special_list[$item['on_special']];
    $row['Superseded'] = $superseded_list[$item['superseded']];
    $row['Active']     = $active_list[$item['active']];

    $table->addHeaderedRow($row);
}

echo $table->toCSVTable();
