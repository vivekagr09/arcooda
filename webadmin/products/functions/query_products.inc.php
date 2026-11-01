<?php
if (!function_exists('roundAmount')) {
    function roundAmount($amount)
    {
        $amount               = sprintf('%.2f', $amount);
        list($before, $after) = explode('.', $amount);
        $num                  = substr($after, 0, 1);
        $numLast              = substr($after, 1, 1);
        if (9 == $num && $numLast >= 7) {
            $before++;
            $num = $newLast = 0;
        } else {
            $convert = array(0 => 0, 1 => 0, 2 => 0, 3 => 5, 4 => 5, 5 => 5, 6 => 5, 7 => 0, 8 => 0, 9 => 0);
            $newLast = $convert[$numLast];
            if ($numLast > 6) {
                $num++;
            }

        }
        $newAmount = $before . '.' . $num . $newLast;
        return $newAmount;
    }
}
if (!function_exists('getProductColourById')) {
    function getProductColourById($id)
    {
        global $DB;
        if ($row = $DB->query_first(sprintf(
            "SELECT * FROM product_colour WHERE  `id` = %d  LIMIT 1",
            $id
        ), MYSQLI_ASSOC)) {
            return $row;
        }
    }
}
if (!function_exists('productBulkPricingFromDatabase')) {
    function productBulkPricingFromDatabase($product_id)
    {
        global $DB;

        $results = array();
        $query   = $DB->query(sprintf(
            "SELECT * FROM `products_bulk_pricing` WHERE `product_id` = %d ORDER BY `qty` ASC",
            $product_id
        ));
        while ($row = $DB->fetch_assoc($query)) {
            $results[$row['id']] = $row;
        }
        $DB->free_result($query);

        return $results;
    }
}
if (!function_exists('productBulkPricingFromPost')) {
    function productBulkPricingFromPost($posted)
    {
        global $DB;

        $product_id           = intval($posted['id']);
        $affected             = explode('|', $posted['product_bulkpricing_affected']);
        $table                = $posted['product_bulkpricing'];
        $products_bulkpricing = array();
        if (productExists($product_id)) {
            $products_bulkpricing = productBulkPricingFromDatabase($product_id);
        }

        foreach ($affected as $key) {
            foreach ($products_bulkpricing as $key2 => $bulkpricing) {
                if ($bulkpricing['id'] == $key) {
                    unset($products_bulkpricing[$key2]);
                }
            }
        }

        if (is_array($table)) {
            foreach ($table as $bulkpricing) {
                $bulkpricing['product_id'] = $product_id;
                foreach ($DB->table_defaults('products_bulk_pricing') as $k => $v) {
                    if (!isset($bulkpricing[$k])) {
                        $bulkpricing[$k] = $v;
                    }
                }

                $products_bulkpricing[] = $bulkpricing;
            }
        }

        return $products_bulkpricing;
    }
}
if (!function_exists('productBulkPricingToDatabase')) {
    function productBulkPricingToDatabase($product_id, $products_bulkpricing)
    {
        global $DB;
        $product_id = intval($product_id);
        if (0 == $product_id) {
            return;
        }

        $sql = "DELETE FROM products_bulk_pricing WHERE product_id=" . $product_id;
        $DB->query($sql);
        $fields = $DB->table_fields('products_bulk_pricing');
        foreach ($products_bulkpricing as $bulkpricing) {
            $insert_vals               = array();
            $bulkpricing['product_id'] = $product_id;
            if (0 == $bulkpricing['id']) {
                unset($bulkpricing['id']);
            }

            foreach ($bulkpricing as $key => $value) {
                if (isset($fields[$key])) {
                    $insert_vals[$key] = $DB->escape($value);
                }
            }

            $sql = "INSERT INTO products_bulk_pricing(`" . implode('`, `', array_keys($insert_vals)) . "`) VALUES(" . implode(", ", $insert_vals) . ")";
            if (count($insert_vals)) {
                $DB->query($sql);
            }

        }
    }
}

if (!function_exists('productBulkWeightsFromDatabase')) {
    function productBulkWeightsFromDatabase($id)
    {
        global $DB;

        $result = array();
        $query  = $DB->query(sprintf(
            "SELECT * FROM `products_bulk_weights` WHERE `product_id` = %d ORDER BY `qty` ASC",
            $id
        ));
        while ($row = $DB->fetch_assoc($query)) {
            $result[] = $row;
        }
        $DB->free_result($query);

        return $result;
    }
}
if (!function_exists('productBulkWeightsFromPost')) {
    function productBulkWeightsFromPost(&$posted)
    {
        $id       = intval($posted['id']);
        $affected = explode('|', $posted['product_bulkweights_affected']);
        $table    = $posted['product_bulkweights'];
        $modified = array();
        if (is_array($table)) {
            foreach ($table as $v) {
                $modified[] = $v['id'];
            }
        }
        $before = productBulkWeightsFromDatabase($id);
        $after  = array();
        foreach ($before as $v) {
            if (!in_array($v['id'], $affected) || in_array($v['id'], $modified)) {
                $after[$v['id']] = $v;
            }
        }
        if (is_array($table)) {
            foreach ($table as $v) {
                if ($v['id']) {
                    foreach ($v as $k2 => $v2) {
                        $after[$v['id']][$k2] = $v2;
                    }
                } else {
                    $after[] = $v;
                }
            }
        }
        return $after;
    }
}
if (!function_exists('productBulkWeightsToDatabase')) {
    function productBulkWeightsToDatabase($id, &$bulkweight_list)
    {
        global $DB;
        foreach ($bulkweight_list as &$row) {
            $row['product_id'] = $id;
            if ($row['id']) {
                $DB->query(sprintf(
                    "UPDATE `products_bulk_weights` SET `product_id` = %d, `qty` = %d, `width` = %s, `depth` = %s, `height` = %s, `weight` = %s, `cubic` = %s, `online_weight` = %s WHERE `id` = %d LIMIT 1",
                    $row['product_id'],
                    $row['qty'],
                    $DB->escape($row['width']),
                    $DB->escape($row['depth']),
                    $DB->escape($row['height']),
                    $DB->escape($row['weight']),
                    $DB->escape($row['cubic']),
                    $DB->escape($row['online_weight']),
                    $row['id']
                ));
            } else {
                $DB->query(sprintf(
                    "INSERT INTO `products_bulk_weights` (`product_id`, `qty`, `width`, `depth`, `height`, `weight`, `cubic`, `online_weight`) VALUES (%d, %d, %s, %s, %s, %s, %s, %s)",
                    $row['product_id'],
                    $row['qty'],
                    $DB->escape($row['width']),
                    $DB->escape($row['depth']),
                    $DB->escape($row['height']),
                    $DB->escape($row['weight']),
                    $DB->escape($row['cubic']),
                    $DB->escape($row['online_weight'])
                ));
                $row['id'] = $DB->insert_id();
            }
        }
        $no_delete = array();
        foreach ($bulkweight_list as &$v) {
            $no_delete[] = intval($v['id']);
        }
        $DB->query(sprintf(
            "DELETE FROM `products_bulk_weights` WHERE `product_id` = %d%s",
            $id,
            (count($no_delete)) ? ' AND `id` NOT IN(' . implode(', ', $no_delete) . ')' : ''
        ));
    }
}

if (!function_exists("productGetOptionList")) {
    function productGetOptionList($table_name)
    {
        static $option_cache = array();
        if (isset($option_cache[$table_name])) {
            return $option_cache[$table_name];
        }

        global $DB;
        $table       = '';
        $option_list = array();
        switch ($table_name) {
            case 'image_path':
                return $_SERVER['DOCUMENT_ROOT'] . '/webadmin/uploads/product/image';

            case 'screenshot_path':
                return $_SERVER['DOCUMENT_ROOT'] . '/webadmin/uploads/screenshot/image';

            case 'thumb_path':
                return $_SERVER['DOCUMENT_ROOT'] . '/webadmin/uploads/product/thumb';

            case 'id':
                $table = 'products';
                $key   = 'id';
                $value = 'product_name';
                break;

            case 'condition':
                return getProductCondition();

            case 'country':
                return getCountryList(false);

            case 'category':
            case 'type':
                $table = "product_types";
                $key   = "id";
                $value = "name";
                $order = "name ASC";
                break;
            case 'section':
                $table = "product_types WHERE parent = 0";
                $key   = "id";
                $value = "name";
                $order = "name ASC";
                break;
            case 'parent_type':
                $table = "product_types";
                $key   = "id";
                $value = "parent";
                break;
            case 'storage':
                $table = "product_storage_location";
                $key   = "id";
                $value = "location";
                $order = "location ASC";
                break;
            case 'supplier':
                $table = "suppliers";
                $key   = "id";
                $value = "supplier";
                $order = "supplier ASC";
                break;
            case 'preferred_supplier':
                //return supplierGetOptionList('id');
                $table = 'suppliers';
                $key   = 'id';
                $value = 'supplier';
                break;

            case 'waranty':
            case 'warranty':
                return getWarrantyTermsList();

            case 'availability':
            case 'available':
                $table = '`product_availability` WHERE `deleted_at` IS NULL';
                $key   = '`id`';
                $value = '`availability`';
                $order = '`rank`';
                break;
                //return $cache[$key] = getAvailabilityList();

            case 'cost_currency':
            case 'sales_currency':
                //return currencyGetOptionList('id');
                return getCurrencyList();

            case 'active':
            case 'allow_invoice':
            case 'ce':
            case 'ctick':
            case 'latest_product':
            case 'on_special':
            case 'price_flux':
            case 'rohs':
            case 'special_flag':
            case 'superseded':
            case 'ul':
                return array('y' => 'Yes', 'n' => 'No');

            case 'joined_fields':
                return array(
                    'LEFT JOIN `products_linked_categories` AS `plc` ON `p`.`id` = `plc`.`product_id`'                        => array(
                        'category_id' => "GROUP_CONCAT(DISTINCT `plc`.`category_id` ORDER BY `plc`.`category_id` ASC)",
                    ),
                    /*
                    'LEFT JOIN `products_suppliers` AS `ps` ON `p`.`id` = `ps`.`product_id`'                                  => array(
                    'supplier_id' => "GROUP_CONCAT(DISTINCT `ps`.`supplier_id`)",
                    ),
                    'LEFT JOIN `products_linked_features` AS `plf` ON (`p`.`id` = `plf`.`product_id`)' => array(
                    'feature_id' => "GROUP_CONCAT(DISTINCT `plf`.`feature_id`)",
                    ),
                    'LEFT JOIN `products_partof` AS `pp` ON `p`.`id` = `pp`.`product_id`'                                     => array(
                    'partof_id' => "GROUP_CONCAT(DISTINCT `pp`.`partof_id`)",
                    ),
                     */
                    'LEFT JOIN `product_types` AS `pt` ON `p`.`type` = `pt`.`id`'          => array(
                        'section' => "`pt`.`parent`",
                    ),
                    "LEFT JOIN `products_related` AS `pr` ON `p`.`id` = `pr`.`product_id`" => array(
                        'related_id' => "GROUP_CONCAT(DISTINCT `pr`.`related_id`)",
                    ),
                );

            case 'all_fields':
                return array_merge(productGetOptionList('base_fields'), productGetOptionList('ext_fields'));
            case 'all_fields_types':
                return array_merge(productGetOptionList('base_fields_types'), productGetOptionList('ext_fields_types'));
            case 'base_fields':
                $fields = array();
                foreach ($DB->table_fields('products') as $key) {
                    $fields[$key] = 'p.`' . $key . '`';
                }

                return $fields;
            case 'base_fields_types':
                return $DB->table_columns('products');
            case 'ext_fields':
            case 'extra_fields':
                return array(
                    "product_active"     => "`p`.`active`",
                    "product_notes"      => "`p`.`notes`",
                    "product_str"        => "`p`.`id`",
                    "product_type"       => "`p`.`type`",
                    "sales_currency"     => "IF(`p`.`price_flux` = 'y', `p`.`cost_currency`, `p`.`sales_currency`)",
                    "sales_price"        => "IF(`p`.`price_flux` = 'y', `p`.`cost_price` * (`p`.`markup` + 100) / 100.0, `p`.`sales_price`)",
                    "time_added"         => "UNIX_TIMESTAMP(`p`.`date_added`)",
                    "time_modified"      => "UNIX_TIMESTAMP(`p`.`last_modified`)",

                    "primary_price"      => "IF(`p`.`price_flux` = 'y', IF(`p`.`old_price` <> 0.0, `p`.`old_price`, `p`.`cost_price` * (`p`.`markup` + 100.0) / 100.0), IF(`p`.`old_price` > `p`.`sales_price`, `p`.`old_price`, `p`.`sales_price`))",
                    "primary_currency"   => "IF(`p`.`price_flux` = 'y', IF(`p`.`old_price` <> 0.0, `p`.`sales_currency`, `p`.`cost_currency`), `p`.`sales_currency`)",
                    "secondary_price"    => "IF(`p`.`price_flux` = 'y', `p`.`cost_price` * (`p`.`markup` + 100.0) / 100.0, `p`.`sales_price`)",
                    "secondary_currency" => "IF(`p`.`price_flux` = 'y', `p`.`cost_currency`, `p`.`sales_currency`)",
                );
            case 'ext_fields_types':
                return array();
            default:
                trigger_error('productGetOptionList(' . json_encode($table_name) . ') is undefined');
                break;
        }
        if ('' != $table) {
            $query = $DB->query("SELECT " . $key . "," . $value . " FROM " . $table . (("" != $order) ? (" ORDER BY " . $order) : ""));
            while ($row = $DB->fetch_array($query)) {
                $option_list[$row[0]] = $row[1];
            }

            $DB->free_result($query);
        }
        $option_cache[$table_name] = $option_list;
        return $option_list;
    }
}
if (!function_exists('productGetLock')) {
    function productGetLock($id)
    {
        global $DB;
        $row = $DB->query_first("SELECT *, UNIX_TIMESTAMP(`expiry_date`) AS `expiry_time` FROM `generic_locks` WHERE `section_name` = 'product' AND `generic_id` = " . intval($id) . " AND `expiry_date` > NOW() LIMIT 1", MYSQLI_ASSOC);
        if ($row['expiry_time'] < time()) {
            $row = false;
        }

        return $row;
    }
}
if (!function_exists("productStrSplit")) {
    function productStrSplit($product_str, $limit = 0)
    {
        $temp   = explode(' ', $product_str);
        $result = array();
        $count  = 0;
        foreach (array('product_id', 'sub_id', 'site_id', 'product_qty') as $key) {
            $count++;
            if (count($temp) && reset($temp) != '') {
                $result[$key] = array_shift($temp);
            }

            if ($limit > 0 && $count >= $limit) {
                return $result;
            }

        }
        return $result;
    }
}

// @todo We have multiple conflicting product modules
include_once $_SERVER['DOCUMENT_ROOT'] . '/webadmin/products_new/query.inc.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/webadmin/new_products/functions/query_product.inc.php';
