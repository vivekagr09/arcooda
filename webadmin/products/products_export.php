<?php
include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/classes/ProductTabulator.php';

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
$allow_invoice_list      = productGetOptionList('allow_invoice');
$availability_list       = productGetOptionList('availability');
$category_list           = productGetOptionList('category');
$ce_list                 = productGetOptionList('ce');
$condition_list          = productGetOptionList('condition');
$country_list            = productGetOptionList('country');
$ctick_list              = productGetOptionList('ctick');
$latest_product_list     = productGetOptionList('latest_product');
$on_special_list         = productGetOptionList('on_special');
$rohs_list               = productGetOptionList('rohs');
$storage_list            = productGetOptionList('storage');
$superseded_list         = productGetOptionList('superseded');
$supplier_list           = productGetOptionList('supplier');
$ul_list                 = productGetOptionList('ul');
$warranty_list           = productGetOptionList('warranty');

$image_dir = productGetOptionList('image_path');

$table = new ProductTabulator();

foreach ($item_list as $item_str) {
    $item       = productFromDatabase($item_str);
    $item_id    = $item['id'];
    $sub_id     = $item['sub_id'];
    $precision  = $item['precision'] ?? $item['decimal'] ?? $item['dPoints'] ?? 2;
    $on_special = $item['on_special'] ?? $item['special_flag'] ?? 'n';

    $row = array();

    $row['Product ID']   = $item['product_number'];
    $row['Product Name'] = $item['product_name'];
    $row['Qty']          = $item['qty'];

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

    $row['Special']       = $on_special_list[$on_special];
    $row['Active']        = $active_list[$item['active']];
    $row['Supplier']      = $supplier_list[$item['supplier']];
    $row['Category Type'] = $category_list[$item['category']];
    //$row['Same As']               = '';
    $row['Storage Location']      = $storage_list[$item['storage']];
    $row["Country Location"]      = $country_list[$item['country']];
    $row['Availability']          = $availability_list[$item['availability']];
    $row["Date Available"]        = ($item['time_available']) ? date('d/m/Y', $item['time_available']) : '';
    $row['Condition']             = $condition_list[$item['condition']];
    $row['Warranty']              = $warranty_list[$item['warranty']];
    $row["Product Picture"]       = ($item['image'] != '' && is_file($image_dir . '/' . $item['image'])) ? 'Yes' : 'No';
    $row['Screenshots']           = count(productScreenshotsFromDatabase($item_str));
    //$row["Admin Screenshots"]     = count(productAdminShots($item_str));
    $row['Supplier Code']         = $item['sup_code'];
    $row["Supplier Product Name"] = $item['sup_name'];
    $row["Date Product Released"] = ($item['time_released']) ? date('d/m/Y', $item['time_released']) : '';
    $row['CE']                    = $ce_list[$item['ce']];
    $row['CTick']                 = $ctick_list[$item['ctick']];
    $row['UL']                    = $ul_list[$item['ul']];
    $row['ROHS']                  = $rohs_list[$item['rohs']];
    $row["Short Desc."]           = $yesno_list[(empty($item['short_desc'])) ? 'n' : 'y'];
    $row["Long Desc."]            = $yesno_list[(empty($item['long_desc'])) ? 'n' : 'y'];
    $row["Product Details"]       = $yesno_list[(empty($item['product_details'])) ? 'n' : 'y'];
    $row["META Title"]            = $yesno_list[(empty($item['meta_title'])) ? 'n' : 'y'];
    $row["META Keywords"]         = $yesno_list[(empty($item['meta_keys'])) ? 'n' : 'y'];
    $row["META Desc."]            = $yesno_list[(empty($item['meta_desc'])) ? 'n' : 'y'];
    $row['Manuals']               = count(productManualsFromDatabase($item_str));

    $item_dimensions = productDimensionFromDatabase($item_str);
    $item_dimension  = $item_dimensions;
    if (is_array(reset($item_dimensions))) {
        $item_dimension = reset($item_dimensions);
    }
    $display_unit_dimension    = array();
    $display_packing_dimension = array();
    $display_cubic             = '0.000';

    foreach ([
        'width'  => 'width',
        'depth'  => 'depth',
        'height' => 'height',
    ] as $k => $v) {
        if (bccomp($item_dimension[$k], '0.000', 3)) {
            $display_unit_dimension[$k] = ceil((float)$item_dimension[$k]);
        }
    }

    $temp = [];
    foreach ([
        'pack_width'   => 'width',
        'pack_depth'   => 'depth',
        'pack_height'  => 'height',
    ] as $k => $v) {
        if (bccomp($item_dimension[$k], '0.000', 3)) {
            $temp[$v] = bcceil($item_dimension[$k]);
        }
    }
    if ('y' == $item_dimension['pallet_enable']) {
        foreach ([
            'pallet_width' => 'width',
            'pallet_depth' => 'depth',
        ] as $k => $v) {
            if (bccomp($item_dimension[$k], '0.000', 3)) {
                $temp[$v] = bcceil($item_dimension[$k]);
            }
        }
    }
    if (count($temp)) {
        $display_packing_dimension[] = implode('x', $temp);
    }

    foreach (productPackingsFromDatabase($item_str) as $packing) {
        $temp = [];
        foreach ([
            'pack_width'   => 'width',
            'pack_depth'   => 'depth',
            'pack_height'  => 'height',
        ] as $k => $v) {
            if (bccomp($packing[$k], '0.000', 3)) {
                $temp[$v] = bcceil($packing[$k]);
            }
        }
        if ('y' == $packing['pallet_enable']) {
            foreach ([
                'pallet_width' => 'width',
                'pallet_depth' => 'depth',
            ] as $k => $v) {
                if (bccomp($packing[$k], '0.000', 3)) {
                    $temp[$v] = bcceil($packing[$k]);
                }
            }
        }
        if (count($temp)) {
            $display_packing_dimension[] = implode('x', $temp);
        }
        if (3 == count($temp)) {
            $display_cubic = bcadd(
                $display_cubic,
                bcmul(
                    bcmul($display_unit_dimension['width'], $display_unit_dimension['depth'], 6),
                    $display_unit_dimension['height'],
                    3
                ),
                3
            );
        }
    }

    if (0 == bccomp($display_cubic, '0.000', 3) && 3 == count($display_unit_dimension)) {
        $display_cubic = bcmul(
            bcmul($display_unit_dimension['width'], $display_unit_dimension['depth'], 6),
            $display_unit_dimension['height'],
            3
        );
    }

    $row["Unit Dimension (cm)"]    = implode('x', $display_unit_dimension);
    $row["Package Dimension (cm)"] = implode("\n", $display_packing_dimensions);
    $row["Actual Weight (kg)"]     = (bccomp($item_dimension['weight'], '0.000', 3)) ? $item_dimension['weight'] : '';
    $row["Volume Weight (kg)"]     = (bccomp($item_dimension['cubic'], '0.000', 3)) ? $item_dimension['cubic'] : '';
    $row["Shipping Weight (kg)"]   = (bccomp($item_dimension['online_weight'], '0.000', 3)) ? $item_dimension['online_weight'] : '';
    $row["Cubic (m^3)"]            = (bccomp($display_cubic, '0.000', 3)) ? $display_cubic : '';

    $row['Superseded']     = $superseded_list[$item['superseded']];
    $row["Latest Product"] = $latest_product_list[$item['latest_product']];
    $row["Allow Invoice"]  = $allow_invoice_list[$item['allow_invoice']];

    $table->addHeaderedRow($row);
}

echo $table->toCSVTable();
