<?php
define('PRODUCT_SCREENSHOT_TYPE_ACTUAL', 1);
define('PRODUCT_SCREENSHOT_TYPE_REFERENCE', 2);

if (!function_exists('TODO')) {
    function TODO($func, $args)
    {
        echo "\n";
        printf("%s(%s);", $func, substr(jecho($args, 1), 1, -1));
        die();
    }
}
if (!function_exists('getlatestmanual')) {
    function getlatestmanual($id)
    {
        /*echo "select m.name,m.id,pm.created_at from manuals m,product_manuals pm where pm.product_id=".$id." and m.id=pm.manual_id order by pm.created_at DESC limit 1";
         die;*/
         global $DB;
         $row = $DB->query_first(sprintf(
            "select m.name,m.id,pm.created_at from manuals m,product_manuals pm where pm.product_id=%d and m.id=pm.manual_id order by pm.created_at DESC limit 1",
            $id
        ), MYSQLI_ASSOC);
        $data= ($row) ? ['name'=>$row['name'],'id'=>$row['id']] : false;
        return $data;

    }
}
if (!function_exists('getserialNumberProduct')) {
    function getserialNumberProduct($id)
    {
        global $DB, $_GLOBALS;
        $result = array();
        $query  = $DB->query("Select * from `highwaygroup`.`serial_numbers` where product_id=".$id." ORDER BY serial_id DESC");
            while ($row = $DB->fetch_assoc($query)) {
                $result[$row["id"]] = $row;
            }
            $DB->free_result($query);
            return $result;
    }
}
if (!function_exists('getlatestfirmware')) {
    function getlatestfirmware($id)
    { 
         global $DB;
         $row = $DB->query_first(sprintf(
            "select f.name,f.id,fm.created from firmware f,product_firmware fm where fm.product_id=%d and f.id=fm.firmware_id order by fm.created DESC limit 1",
            $id
        ), MYSQLI_ASSOC);
        $data= ($row) ? ['name'=>$row['name'],'id'=>$row['id']] : false;
        return $data;

    }
}
if (!function_exists('productExists')) {
    function productExists($id)
    {
        return productGenericExists($id, '`products`');
    }
}
if (!function_exists('productFormatNumber')) {
    function productFormatNumber($id)
    {
        return sprintf('%05d', $id);
    }
}
if (!function_exists('productVirtual')) {
    function productVirtual($row)
    {
        global $_GLOBALS;

        $row['image'] = basename($row['image']);
        $row['thumb'] = basename($row['thumb']);

        $row['product_number'] = sprintf('#%05d', $row['id']);

        $row['product_online'] = $row['secondary_price'];

        $row['product_offerau'] = convertCurrency($row['secondary_price'], $row['secondary_currency'], 'AUD', false, 4);
        $row['product_offerus'] = convertCurrency($row['secondary_price'], $row['secondary_currency'], 'USD', false, 4);

        $row['product_costau'] = convertCurrency($row['cost_price'], $row['cost_currency'], 'AUD', false, 4);
        $row['product_costus'] = convertCurrency($row['cost_price'], $row['cost_currency'], 'USD', false, 4);

        $row['reserved'] = 0;
        foreach (productReservedFromDatabase($row['id']) as $temp) {
            $row['reserved'] += $temp['qty'];
        }

        $section_list    = productGetOptionList('section');
        $type_list       = productGetOptionList('type');
        $row['view_url'] = sprintf(
            '/buy/%s-%d',
            /*
            '/p/%s-%d.html',
            */
            urlencode(preg_replace('/\s+/', '-', $row['product_name'])),
            $row['id']
        );
        if ('' !== $row['slug']) {
            $row['view_url'] = sprintf(
                '/buy/%s-%d',
                /*
                '/p/%s-%d.html',
                */
                urlencode($row['slug']),
                $row['id']
            );
        }

        if ('' != $row['image'] && is_file(productGetOptionList('image_path') . $row['image'])) {
            $row['image_url'] = $row['image_180_url'] = $row['thumb_url'] = $row['tiny_url'] = sprintf(
                '/images/%s-%d.jpg',
                /*
                '/p/%s-%d.jpg',
                */
                urlencode(trim(preg_replace('/[^A-Za-z0-9]+/', '-', $row['name']), '-')),
                $row['id']
            );
        }

        if ('' != $row['thumb'] && is_file(productGetOptionList('thumb_path') . $row['thumb'])) {
            $row['thumb_url'] = $row['tiny_url'] = sprintf(
                '/images/%s-%d.jpg',
                /*
                '/p/%s-%d.jpg',
                */
                urlencode(trim(preg_replace('/[^A-Za-z0-9]+/', '-', $row['name']), '-')),
                $row['id']
            );
        }

        if (!empty($_GLOBALS["site_settings"]["static_path"] ?? '') && substr($_SERVER["REQUEST_URI"], 0, 10) != "/webadmin/") {
            $test = "/images/products/large/" . $row["id"] . ".jpg";
            if (is_file($_GLOBALS["site_settings"]["static_path"] . $test)) {
                $row['image_url'] = $_GLOBALS["site_settings"]["static_uri"] . $test;
            }

            $test = "/images/products/medium/" . $row["id"] . ".jpg";
            if (is_file($_GLOBALS["site_settings"]["static_path"] . $test)) {
                $row['image_180_url'] = $_GLOBALS["site_settings"]["static_uri"] . $test;
            }

            $test = "/images/products/thumb/" . $row["id"] . ".jpg";
            if (is_file($_GLOBALS["site_settings"]["static_path"] . $test)) {
                $row['thumb_url'] = $_GLOBALS["site_settings"]["static_uri"] . $test;
            }

            $test = "/images/products/icon/" . $row["id"] . ".jpg";
            if (is_file($_GLOBALS["site_settings"]["static_path"] . $test)) {
                $row['tiny_url'] = $_GLOBALS["site_settings"]["static_uri"] . $test;
            }
        }

        return $row;
    }
}
if (!function_exists('productFromDatabase')) {
    function productFromDatabase($id)
    {
        global $DB;

        $fields = array('*' => '`p`.*');
        foreach (productGetOptionList('ext_fields') as $k => $v) {
            $fields[$k] = $v . " AS `" . $k . "`";
        }

        $row = $DB->query_first(sprintf(
            "SELECT %s FROM `products` AS `p` WHERE `p`.`id` = %d LIMIT 1",
            implode(", ", $fields),
            $id
        ), MYSQLI_ASSOC);
        return ($row) ? productVirtual($row) : false;
    }
}
if (!function_exists('productGetOptionList')) {
    function productGetOptionList($key)
    {
        $map = array(
            //'category' => 'category',
            'category_id'            => 'category',
            'item_type'              => 'category',
            'item_types'             => 'category',
            'products_type'          => 'category',
            'products_types'         => 'category',
            'type'                   => 'category',
            'types'                  => 'category',

            //'availability' => 'availability',
            'available'              => 'availability',

            //'created_by' => 'created_by',
            'factory_meta_by'        => 'created_by',
            'inventory_check_by'     => 'created_by',
            'meta_by'                => 'created_by',
            'staff_id'               => 'created_by',
            'updated_by'             => 'created_by',

            //'active_staff_id' => 'active_staff_id',

            //'cost_currency' => 'cost_currency',
            'currency'               => 'cost_currency',
            'forwarder_fee_currency' => 'cost_currency',
            'sales_currency'         => 'cost_currency',

            //'discontinued' => 'discontinued',
            'jamma_wiring'           => 'discontinued',
            'linking'                => 'discontinued',

            //'ext_fields' => 'ext_fields',
            'extra_fields'           => 'ext_fields',
            'storage'                => 'location',
            'storage_location'       => 'location',

            //'preferred_supplier' => 'preferred_supplier',
            'supplier'               => 'preferred_supplier',
            'supplier_id'            => 'preferred_supplier',

            //'item_waranty' => 'item_waranty',
            'item_warranty'          => 'item_waranty',
            'products_waranty'       => 'item_waranty',
            'products_warranty'      => 'item_waranty',
            'waranty'                => 'item_waranty',
            'warranty'               => 'item_waranty',
            'product_series'         => 'product_series',
            '360_view'               => '360_view',
            'product_slider'         => 'product_slider',
            'product_builder'        => 'product_builder',
            'product_carousel'       => 'product_carousel',
            'product_supported'      => 'product_supported',
            'monitor_size'           => 'monitor_size',
            'monitor_specifications' => 'monitor_specifications',
            'monitor_touchscreen'    => 'monitor_touchscreen',
            'monitor_other_options'  => 'monitor_other_options',
            'monitor_part_number'    => 'monitor_part_number',
            'cabinet_material'       => 'cabinet_material',
            'cabinet_panel_material' => 'cabinet_panel_material',
            'cabinet_lighting'       => 'cabinet_lighting',
            'cabinet_joystick_brand' => 'cabinet_joystick_brand',
            'cabinet_button_brand'   => 'cabinet_button_brand',
        );
        if (array_key_exists($key, $map)) {
            $key = $map[$key];
        }

        static $cache = array();
        if (array_key_exists($key, $cache)) {
            return $cache[$key];
        }

        global $DB;

        switch ($key) {
            //case 'on_special':
            //case 'special':
            case 'active':
            case 'additional_fees':
            case 'ce':
            case 'ctick':
            case 'featured':
            case 'manufacturer_online':
            case 'price_flux':
            case 'price_guarantee':
            case 'price_online':
            case 'rohs':
            case 'ul':
                return array(
                    'y' => 'Yes',
                    'n' => 'No',
                );

            case 'alert_enable':
                return array(
                    'y' => 'Enabled',
                    'n' => 'Disabled',
                );

            case 'all_fields_types':
                return array_merge(
                    productGetOptionList('base_fields_types'),
                    productGetOptionList('ext_fields_types')
                );

            case 'all_fields':
                return array_merge(
                    productGetOptionList('base_fields'),
                    productGetOptionList('ext_fields')
                );

            case 'availability':
            case 'available':
                $cache[$key] = array();
                $query       = $DB->query(sprintf(
                    "SELECT `id`, `availability` FROM `product_availability` WHERE `deleted_at` IS NULL ORDER BY `rank` ASC"
                ));
                while ($row = $DB->fetch_assoc($query)) {
                    $cache[$key][$row['id']] = $row['availability'];
                }
                $DB->free_result($query);
                break;

            case 'base_fields_types':
                return $cache[$key] = $DB->get_table_fields('`products`');

            case 'base_fields':
                $result = array();
                foreach (productGetOptionList('base_fields_types') as $k => $v) {
                    $result[$k] = '`p`.`' . $k . '`';
                }
                return $result;

            case 'buttons':
                return range(0, 64);

            case 'category':
            case 'category_id':
            case 'item_type':
            case 'item_types':
            case 'products_type':
            case 'products_types':
            case 'type':
            case 'types':
            case 'type_tree':
            case 'category_parent':
                $cache['category']        = array();
                $cache['type_tree']       = array();
                $cache['category_parent'] = array();
                $query                    = $DB->query(sprintf(
                    "SELECT `id`, `parent`, `name` FROM `product_types` WHERE `deleted_at` IS NULL ORDER BY `ordering` ASC"
                ));
                while ($row = $DB->fetch_assoc($query)) {
                    $cache['category'][$row['id']]        = $row['name'];
                    $cache['type_tree'][$row['parent']][] = $row['id'];
                    $cache['category_parent'][$row['id']] = $row['parent'];
                }
                $DB->free_result($query);
                break;

            case 'category_path':
                return $_SERVER['DOCUMENT_ROOT'] . '/webadmin/uploads/category/image/';

            case 'created_by':
            case 'factory_meta_by':
            case 'inventory_check_by':
            case 'meta_by':
            case 'staff_id':
            case 'updated_by':
                return $cache[$key] = getStaffList();

            case 'active_staff_id':
                $cache[$key] = array();
                $query = $DB->query(sprintf(
                    "SELECT `id`, `first_name`, `last_name` FROM `highwaygroup`.`users` WHERE `active` = 'y' ORDER BY `first_name` ASC, `last_name` ASC"
                ));
                while ($row = $DB->fetch_assoc($query)) {
                    $cache[$key][$row['id']] = trim($row['first_name'] . ' ' . $row['last_name']);
                }
                $DB->free_result($query);
                break;

            case 'colour':
                $cache[$key] = array();
                $query       = $DB->query(sprintf(
                    "SELECT `id`, `colour` FROM `product_colour` WHERE `deleted_at` IS NULL ORDER BY `colour` ASC"
                ));
                while ($row = $DB->fetch_assoc($query)) {
                    $cache[$key][$row['id']] = $row['colour'];
                }
                $DB->free_result($query);
                break;


            case 'condition':
                return $cache[$key] = getProductCondition();

            case 'controller_type':
                return array(
                    "2" => "Gun",
                    "3" => "Joystick",
                    "4" => "Steering",
                    "5" => "Touchscreen",
                    "6" => "Trackball",
                    "1" => "Other",
                );

            case 'cost_currency':
            case 'currency':
            case 'forwarder_fee_currency':
            case 'sales_currency':
            return $cache[$key] = getCurrencyList();

            case 'country_location':
                $cache[$key] = array();
                $query       = $DB->query(sprintf(
                    "SELECT `id`, `name` FROM `product_country_location` WHERE `deleted_at` IS NULL ORDER BY `name` ASC"
                ));
                while ($row = $DB->fetch_assoc($query)) {
                    $cache[$key][$row['id']] = $row['name'];
                }
                $DB->free_result($query);
                break;

            case 'country_manufacture':
            
                return $cache[$key] = getCountrylist(false);

            case 'discontinued':
            case 'jamma_wiring':
            case 'linking':
                return array(
                    'n' => 'No',
                    'y' => 'Yes',
                );

            case 'ext_fields_types':
                return array();

            case 'ext_fields':
            case 'extra_fields':
                $today = date("'Y-m-d 00:00:00'");
                return array(
                    "active_3"           => "`p`.`active`",
                    'alert_flag'         => "`p`.`_alert_flag`",
                    //"alert_flag"             => "IF(`p`.`alert_enable` = 'y' AND `p`.`alert_level` > `p`.`qty`, 'y', 'n')",
                    'alpha_product_name' => "`p`.`_first_letter`",
                    //"alpha_product_name"     => "IF(LCASE(LEFT(`p`.`product_name`, 1)) IN('" . implode("','", range("a", "z")) . "'), LCASE(LEFT(`p`.`product_name`, 1)), '0')",
                    'first_letter'       => "`p`.`_first_letter`",
                    //"first_letter"           => "IF(LCASE(LEFT(`p`.`product_name`, 1)) IN('" . implode("', '", range("a", "z")) . "'), LCASE(LEFT(`p`.`product_name`, 1)), '_')",

                    "product_id"         => "`p`.`id`",
                    "name"               => "`p`.`product_name`",

                    "added_today"        => "IF(" . $today . " <= `p`.`created_at`, 'y', 'n')",
                    "precision"          => "2",

                    "featured"           => "IF(`p`.`feature` = 'y' AND (`p`.`featured_expiry` ='0000-00-00 00:00:00' OR `p`.`featured_expiry` >= " . $today . "), 'y', 'n')",
                    "num_days_old"       => "(TO_DAYS(" . $today . ") - TO_DAYS(`p`.`created_at`))",
                    "secondary_currency" => "`p`.`_secondary_currency`",
                    "secondary_price"    => "IF(`p`.`special_flag` = 'y' AND `p`.`special_priceus` > 0.0 AND (`p`.`special_expiry` >= " . $today . " OR `p`.`special_expiry` = '0000-00-00 00:00:00'), `p`.`special_priceus`, IF(`p`.`price_flux` = 'y', `p`.`cost_price` * (`p`.`markup` + 100.0) / 100.0, `p`.`sales_price`))",
                    "primary_currency"   => "`p`.`_primary_currency`",
                    "primary_price"      => "`p`.`_primary_price`",

                    "on_special"         => "`p`.`_on_special`",

                    //Compatibility FIelds
                    'availability'       => "`p`.`available`",
                    'category'           => "`p`.`type`",
                    'change_by'          => "`p`.`meta_by`",
                    "floor_stock"        => "0",
                    "location"           => "`p`.`storage`",
                    'supplier'           => "`p`.`preferred_supplier`",
                    'total_sales'        => "0",
                    'orig_priceus'       => "`p`.`old_price`", //TODO: Wrong currency
                    'product_offerus'    => "`p`.`sales_price`", //TODO: Wrong currency
                    'product_costus'     => "`p`.`cost_price`", //TODO: Wrong currency
                    'product_offer'      => "`p`.`sales_price`",
                    'product_cost'       => "`p`.`cost_price`",
                    'featured_product'   => "`p`.`feature`",
                    'id_changed'         => "'n'",
                    'price_guarantee'    => "'n'",
                    'superseded'         => "'n'",
                    'stock'              => "IF(`p`.`stock_type` = 'stock_for_sale', 's', IF(`p`.`stock_type` = 'stock_sample', 'ss', IF(`p`.`stock_type` = 'consignment', 'c', IF(`p`.`stock_type` = 'repair_item', 'r', ''))))",
                    'active_id'          => "IF(`p`.`active` = 'y', '3', '')",
                    'site_id'            => "'39'",
                    'image_file'         => "`p`.`image`",
                    'meta_factory_keys'  => "`p`.`factory_meta_keywords`",
                    'meta_factory_title' => "`p`.`factory_meta_title`",
                    'factory_url_keys'   => "`p`.`factory_slug`",
                    'meta_keys'          => "`p`.`meta_keywords`",
                    'thumb_file'         => "`p`.`thumb`",
                    'url_keys'           => "`p`.`slug`",
                    'meta_desc'          => "`p`.`meta_description`",
                    'meta_factory_desc'  => "`p`.`factory_meta_description`",
                    'packaging'          => "`p`.`notes`",
                    'date_released'      => "`p`.`date_available`",
                    'last_change'        => "`p`.`meta_at`",
                    'country'            => "`p`.`country_location`", //TODO: Mismatched type
                    'timestamp'          => "`p`.`updated_at`",
                );

            case 'first_letter':
                $cache[$key] = array();
                $query       = $DB->query(sprintf(
                    "SELECT DISTINCT `_first_letter` FROM `products` WHERE `deleted_at` IS NULL ORDER BY `_first_letter` ASC"
                ));
                while ($row = $DB->fetch_assoc($query)) {
                    $cache[$key][$row['_first_letter']] = strtoupper($row['_first_letter']);
                }
                $DB->free_result($query);
                break;

            case 'game_play':
                return array(
                    1 => 'Co-operative Play',
                    2 => 'Versus Play',
                    3 => 'Tournament Play',
                    4 => 'Co-operative & Versus Play',
                    5 => 'Single Play',
                    6 => 'Single Play & Versus Play',
                    7 => 'Single & Co-operative Play',
                    8 => 'Single Play, Versus Play & Co-operative Play',
                    9 => 'Single, Versus, Tournament',
                );

            case 'gun_type':
                return array(
                    1 => 'Standard',
                    2 => 'Force Feedback',
                );

            case 'icons':
                $cache[$key] = array();
                $query       = $DB->query(sprintf(
                    "SELECT * FROM `icons` ORDER BY `name` ASC"
                ));
                while ($row = $DB->fetch_assoc($query)) {
                    $cache[$key][$row['id']] = $row;
                }
                $DB->free_result($query);
                break;

            case 'image_path':
                return $_SERVER['DOCUMENT_ROOT'] . '/webadmin/uploads/product/image/';

            case 'thumb_path':
                return $_SERVER['DOCUMENT_ROOT'] . '/webadmin/uploads/product/thumb/';

            case 'screenshot_path':
                return $_SERVER['DOCUMENT_ROOT'] . '/webadmin/uploads/screenshot/image/';

            case 'joined_fields':
                return array(
                    'LEFT JOIN `products_linked_categories` AS `plc` ON `p`.`id` = `plc`.`product_id`'                        => array(
                        'category_id' => "GROUP_CONCAT(DISTINCT `plc`.`category_id` ORDER BY `plc`.`category_id` ASC)",
                    ),
                    'LEFT JOIN `product_types` AS `pt` ON `p`.`type` = `pt`.`id`'          => array(
                        'section' => "`pt`.`parent`",
                    ),
                    "LEFT JOIN `products_related` AS `pr` ON `p`.`id` = `pr`.`product_id`" => array(
                        'related_id' => "GROUP_CONCAT(DISTINCT `pr`.`related_id`)",
                    ),
                );

            case 'joysticks':
                return range(0, 8);

            case 'location':
            case 'storage':
            case 'storage_location':
                $cache[$key] = array();
                $query       = $DB->query(sprintf(
                    "SELECT `id`, `location` FROM `product_storage_location` WHERE `deleted_at` IS NULL ORDER BY `location` ASC"
                ));
                while ($row = $DB->fetch_assoc($query)) {
                    $cache[$key][$row['id']] = $row['location'];
                }
                $DB->free_result($query);
                break;

            case 'manual_path':
                return $_SERVER['DOCUMENT_ROOT'] . '/webadmin/uploads/product/attachment/';

            case 'manufacturer':
                $cache[$key] = array();
                $query       = $DB->query(sprintf(
                    "SELECT `id`, `manufacturer` FROM `manufacturers` ORDER BY `manufacturer` ASC"
                ));
                while ($row = $DB->fetch_assoc($query)) {
                    $cache[$key][$row['id']] = $row['manufacturer'];
                }
                $DB->free_result($query);
                break;
            case 'manufacturer-active-list':
                $cache[$key] = array();
                $query       = $DB->query(sprintf(
                    "SELECT `id`, `manufacturer`,`show_front` FROM `manufacturers` ORDER BY `manufacturer` ASC"
                ));
                while ($row = $DB->fetch_assoc($query)) {
                    $cache[$key][$row['id']] = $row['show_front'];
                }
                $DB->free_result($query);
                break;


            case 'no_players':
                return range(0, 20);

            case 'pcb_type':
                $cache[$key] = array();
                $query       = $DB->query(sprintf(
                    "SELECT `id`, `pcb_type` FROM `products_pcb_types` WHERE `deleted_at` IS NULL ORDER BY `pcb_type` ASC"
                ));
                while ($row = $DB->fetch_assoc($query)) {
                    $cache[$key][$row['id']] = $row['pcb_type'];
                }
                $DB->free_result($query);
                break;

            case 'pedal_configuration':
                return range(0, 3);

            case 'promotion':
                $cache[$key] = array();
                $query       = $DB->query(sprintf(
                    "SELECT `id`, `name` FROM `product_promotion` WHERE `deleted_at` IS NULL ORDER BY `name` ASC"
                ));
                while ($row = $DB->fetch_assoc($query)) {
                    $cache[$key][$row['id']] = $row['name'];
                }
                $DB->free_result($query);
                break;

            case 'screen_orientation':
                return array(
                    1 => 'Vertical',
                    2 => 'Horizontal',
                    3 => 'Horizontal & Vertical',
                );

            case 'screen_resolution':
                return array(
                    1  => 'Standard (15kHz)',
                    2  => 'Medium (25kHz)',
                    3  => 'High (31kHz)',
                    4  => 'Standard (15kHz) + Medium (25kHz)',
                    5  => 'Standard (15kHz) + Medium (25kHz) + High (31kHz)',
                    6  => '1080p (1920x1080)',
                    7  => '720p (1366x768)',
                    8  => '15kHz ~ 48Khz',
                    9  => 'Standard (15kHz) + High (31kHz)',
                    10 => '50/60Hz',
                    11 => '4K (3840 x 2160)',
                );
                break;

            case 'screen_size':
                $cache[$key] = array();
                foreach (range(14, 150) as $k) {
                    $cache[$key][$k] = sprintf("%d\" Screen", $k);
                }
                break;

            case 'section':
                $cache[$key] = array();
                $query       = $DB->query(sprintf(
                    "SELECT `id`, `name` FROM `product_types` WHERE `deleted_at` IS NULL AND `parent` = 0 ORDER BY `ordering` ASC"
                ));
                while ($row = $DB->fetch_assoc($query)) {
                    $cache[$key][$row['id']] = $row['name'];
                }
                $DB->free_result($query);
                break;

            case 'shifter':
                return array(
                    2 => 'Up / Down',
                    3 => '3 Speed',
                    4 => '4 Speed',
                    6 => '6 Speed',
                );

            case 'site_id':
                return array(
                    '3' => 'Coinop Express',
                );

            case 'steering':
                return array(
                    1 => 'Force Feedback Steering',
                    2 => 'Spring Steering (Left & Right)',
                    3 => 'Free Wheel Steering',
                );

            case 'stock_type':
                return array(
                    'stock_for_sale'  => 'Stock for Sale',
                    'consignment'     => 'Consignment',
                    'stock_sample'    => 'Stock Sample',
                    'repair_item'     => 'Repair',
                );

            case 'style':
                $cache[$key] = array();
                $query       = $DB->query(sprintf(
                    "SELECT `id`, `style` FROM `product_style` WHERE `deleted_at` IS NULL ORDER BY `style` ASC"
                ));
                while ($row = $DB->fetch_assoc($query)) {
                    $cache[$key][$row['id']] = $row['style'];
                }
                $DB->free_result($query);
                break;

            case 'preferred_supplier':
            case 'supplier':
            case 'supplier_id':
                $cache[$key] = array();
                $query       = $DB->query(sprintf(
                    "SELECT `supplier_id`, `company` FROM `new_suppliers` ORDER BY `company` ASC"
                ));
                while ($row = $DB->fetch_assoc($query)) {
                    $cache[$key][$row['supplier_id']] = $row['company'];
                }
                $DB->free_result($query);
                break;
            //return $cache[$key] = getProductSuppliers();

            case 'version':
            case 'version_type':
                return array(
                    1 => 'Chinese Version',
                    2 => 'English Version',
                    3 => 'Japanese Version',
                    4 => 'Korean Version',
                );

            case 'item_waranty':
            case 'item_warranty':
            case 'products_waranty':
            case 'products_warranty':
            case 'waranty':
            case 'warranty':
                return getWarrantyTermsList();

            case 'when_sold_out':
                return array(
                    'active'     => 'Leave Active Online',
                    'deactivate' => 'De-activate',
                );

            case 'year_released':
                $cache[$key] = array();
                $query       = $DB->query(sprintf(
                    "SELECT `id`, `year_released` FROM `year_released` ORDER BY `year_released` ASC"
                ));
                while ($row = $DB->fetch_assoc($query)) {
                    $cache[$key][$row['id']] = $row['year_released'];
                }
                $DB->free_result($query);
                break;

            case 'coin_mech':
                $cache[$key] = array();
                $query       = $DB->query(sprintf(
                    "SELECT `id`, `product_name` FROM `products` WHERE `deleted_at` IS NULL AND `type` = 311 ORDER BY `product_name` ASC"
                ));
                while ($row = $DB->fetch_assoc($query)) {
                    $cache[$key][$row['id']] = sprintf('%s (#%05d)', $row['product_name'], $row['id']);
                }
                $DB->free_result($query);
                break;
            //return $cache[$key] = getCoinMechList();

            default:
                die('TODO: productGetOptionList(\'' . $key . '\')');
        }

        return $cache[$key];
    }
}

if (!function_exists('productQuery')) {
    /**
     * Generate a SQL WHERE expression to find matching products.
     *
     * @param array $criteria    (optional) Array of key => values that the
     *    product should match when searching. Values may be arrays.
     * @param array $lessthan    (optional) Array of key => values that the
     *    product should be less than when searching. Values may be arrays.
     * @param array $greaterthan (optional) Array of key => values that the
     *    product should be greater than when searching. Values may be arrays.
     */
    function productQuery($criteria = array(), $lessthan = array(), $greaterthan = array())
    {
        $criteria['section'][]=2;
        $criteria['section'][]=3;
        global $DB;

        // Simple case, all products
        if (empty($criteria) && empty($lessthan) && empty($greaterthan)) {
            return '1';
        }

        // Normalize all parameters to nested arrays
        foreach ($criteria as $k => $v) {
            if (!is_array($v)) {
                $criteria[$k] = array($v);
            }
        }
        foreach ($lessthan as $k => $v) {
            if (!is_array($v)) {
                $lessthan[$k] = array($v);
            }
        }
        foreach ($greaterthan as $k => $v) {
            if (!is_array($v)) {
                $greaterthan[$k] = array($v);
            }
        }

        // Short-list any joined fields
        foreach (productGetOptionList('joined_fields') as $join => $fields) {
            foreach ($fields as $field => $expr) {
                $whereAND = array();
                if (array_key_exists($field, $criteria)) {
                    $whereOR = array();
                    foreach ($criteria[$field] as $v) {
                        $whereOR[] = sprintf("%s = %s", $expr, $DB->escape($v));
                    }
                    if (count($whereOR)) {
                        $whereAND[] = sprintf("(%s)", implode(" OR ", $whereOR));
                    }
                    unset($criteria[$field]);
                }
                if (array_key_exists($field, $lessthan)) {
                    foreach ($lessthan[$field] as $v) {
                        $whereAND[] = sprintf("%s < %s", $expr, $DB->escape($v));
                    }
                    unset($criteria[$field]);
                }
                if (array_key_exists($field, $greaterthan)) {
                    foreach ($greaterthan[$field] as $v) {
                        $whereAND[] = sprintf("%s > %s", $expr, $DB->escape($v));
                    }
                    unset($criteria[$field]);
                }
                if (count($whereAND)) {
                    $sub_list = array();
                    $query    = $DB->query(sprintf(
                        "SELECT DISTINCT `p`.`id` FROM `products` AS `p` %s WHERE %s",
                        $join,
                        implode(" AND ", $whereAND)
                    ));
                    while ($row = $DB->fetch_assoc($query)) {
                        $sub_list[] = $row['id'];
                    }
                    $DB->free_result($query);
                    if (array_key_exists('id', $criteria)) {
                        $criteria['id'] = array_intersect($criteria['id'], $sub_list);
                    } else {
                        $criteria['id'] = $sub_list;
                    }
                }
            }
        }

        $whereAND    = array();
        $field_types = productGetOptionList('all_fields_types');
        foreach (productGetOptionList('all_fields') as $field => $expr) {
            if (array_key_exists($field, $criteria)) {
                $whereOR = array();
                foreach ($criteria[$field] as $v) {
                    if (array_key_exists($field, $field_types) && substr($field_types[$field]['Type'], 0, 4) == 'set(') {
                        $whereOR[] = sprintf("FIND_IN_SET(%s, %s)", $DB->escape($v), $expr);
                    } else {
                        $whereOR[] = sprintf("%s = %s", $expr, $DB->escape($v));
                    }
                }
                if (count($whereOR)) {
                    $whereAND[] = sprintf("(%s)", implode(" OR ", $whereOR));
                }
                unset($criteria[$field]);
            }
            if (array_key_exists($field, $lessthan)) {
                foreach ($lessthan[$field] as $v) {
                    $whereAND[] = sprintf("%s < %s", $expr, $DB->escape($v));
                }
                unset($criteria[$field]);
            }
            if (array_key_exists($field, $greaterthan)) {
                foreach ($greaterthan[$field] as $v) {
                    $whereAND[] = sprintf("%s > %s", $expr, $DB->escape($v));
                }
                unset($criteria[$field]);
            }
        }

        if (array_key_exists('any_text', $criteria)) {
            foreach ($criteria['any_text'] as $findme) {
                if (!empty($findme)) {
                    $whereOR = array();
                    foreach (array(
                        '`p`.`factory_slug`',
                        '`p`.`id`',
                        '`p`.`product_name`',
                        '`p`.`slug`'
                    ) as $expr) {
                        $whereOR[] = sprintf(
                            "%s LIKE %s",
                            $expr,
                            formatSQLLikeString($findme)
                        );
                    }
                    if (count($whereOR)) {
                        $whereAND[] = sprintf("(%s)", implode(" OR ", $whereOR));
                    }
                }
            }
        }

        if (1 == count($whereAND)) {
            return implode(" AND ", $whereAND);
        } elseif (count($whereAND)) {
            return sprintf("(%s)", implode(" AND ", $whereAND));
        }
        return "1";
    }
}
if (!function_exists('productSearch')) {
    function productSearch($criteria = array())
    {
        global $DB;

        if (is_array($criteria)) {
            $criteria = productQuery($criteria);
        }
        $results = array();
        $query   = $DB->query(sprintf(
            "SELECT `id` FROM `products` AS `p`%s ORDER BY `p`.`id` ASC",
            (!is_null($criteria)) ? sprintf(" WHERE %s", $criteria) : ""
        ));
        while ($row = $DB->fetch_assoc($query)) {
            $results[] = $row['id'];
        }
        $DB->free_result($query);

        return $results;
    }
}
if (!function_exists('productSetLock')) {
    function productSetLock($id, $staff_id = 0, $timeout = 120)
    {
        global $DB;
        $section_name = $DB->escape("product");
        if ($staff_id) {
            $DB->query(sprintf(
                "UPDATE `generic_locks` SET `expiry_date` = ADDTIME(NOW(), '0:2:0.0') WHERE `section_name` = %s AND `generic_id` = %d AND `staff_id` = %d AND `expiry_date` > NOW() LIMIT 1",
                //formatSQLDateTime(time() + $timeout),
                $section_name,
                $id,
                $staff_id
            ));
            if ($DB->affected_rows() == 0) {
                $DB->query(sprintf(
                    "INSERT INTO `generic_locks` (`section_name`, `generic_id`, `staff_id`, `expiry_date`) VALUES (%s, %d, %d, ADDTIME(NOW(), '0:2:0.0'))",
                    $section_name,
                    $id,
                    $staff_id//,
                    //formatSQLDateTime(time() + $timeout)
                ));
            }
        } else {
            $DB->query(sprintf(
                "UPDATE `generic_locks` SET `expiry_date` = NOW() WHERE `section_name` = %s AND `generic_id` = %d AND `expiry_date` > NOW()",
                $section_name,
                $id
            ));
        }
    }
}
if (!function_exists('productsFromDatabase')) {
    function productsFromDatabase($product_list = array())
    {
        TODO(__FUNCTION__, func_get_args());return array(); //TODO: Double check this function

        if (count($product_list) == 0) {
            return array();
        }

        if (count($product_list) > 200) {
            die('Not a good idea... too many products');
        }

        global $DB;
        $fields = array('*' => 'p.*');
        foreach (productGetOptionList('ext_fields') as $k => $value) {
            $fields[$k] = $value . " AS `" . $k . "`";
        }

        $results = array();
        $query = $DB->query(sprintf(
            "SELECT %s FROM `products` AS `p` WHERE `p`.`id` IN(%s)",
            implode(", ", $fields),
            implode(", ", $product_list)
        ), MYSQLI_ASSOC);
        while ($result = $DB->fetch_assoc($query)) {
            $results[$result['id']] = productVirtual($result);
        }
        $DB->free_result($query);
        return $results;
    }
}
if (!function_exists('productSetValue')) {
    function productSetValue($id, $field, $value)
    {
        global $DB;
        $fields = $DB->get_table_columns('`products`');
        if (array_key_exists($field, $fields)) {
            $DB->query(sprintf(
                "UPDATE `products` SET `%s` = %s WHERE `id` = %d LIMIT 1",
                $field,
                $DB->escape($value),
                $id
            ));
            return true;
        }
        return false;
    }
}
if (!function_exists('productSort')) {
    function productSort($criteria = array(), $order_by = 'product_id')
    {
        global $DB;

        if (is_array($criteria)) {
            $criteria = productQuery($criteria);
        }

        $fields = productGetOptionList('all_fields');

        $results = array();
        $query   = $DB->query(sprintf(
            "SELECT `id` FROM `products` AS `p`%s ORDER BY %s ASC",
            (!is_null($criteria)) ? sprintf(" WHERE %s", $criteria) : "",
            (array_key_exists($order_by, $fields)) ? $fields[$order_by] : '`p`.`id`'
        ));
        while ($row = $DB->fetch_assoc($query)) {
            $results[] = $row['id'];
        }
        $DB->free_result($query);

        return $results;
    }
}
if (!function_exists('productSortData')) {
    function productSortData($criteria = array(), $order_by = 'product_id')
    {
        global $DB;

        if (is_array($criteria)) {
            $criteria = productQuery($criteria);
        }

        $fields = productGetOptionList('all_fields');

        $results = array();
        $query   = $DB->query(sprintf(
            "SELECT `id` FROM `products` AS `p`%s ORDER BY %s ASC",
            (!is_null($criteria)) ? sprintf(" WHERE %s", $criteria) : "",
            (array_key_exists($order_by, $fields)) ? $fields[$order_by] : '`p`.`id`'
        ));
        while ($row = $DB->fetch_assoc($query)) {
            $results[] = $row['id'];
        }
        $DB->free_result($query);

        return $results;
    }
}

