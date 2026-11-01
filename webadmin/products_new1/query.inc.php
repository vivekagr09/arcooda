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

if (!function_exists('productGenericDelete')) {
    function productGenericDelete($id, $table, $primary_key = 'id') {
        global $DB;
        $DB->query(sprintf(
            "UPDATE %s SET `deleted_at` = NOW() WHERE `deleted_at` IS NULL AND `%s` = %d LIMIT 1",
            //"DELETE FROM %s WHERE `%s` = %d LIMIT 1",
            $table,
            $primary_key,
            $id
        ));
        return $DB->affected_rows();
    }
}
if (!function_exists('productGenericExists')) {
    function productGenericExists($id, $table, $primary_key = 'id') {
        global $DB;
        return !!$DB->query_first(sprintf(
            "SELECT `%s` FROM %s WHERE `%s` = %d LIMIT 1",
            $primary_key,
            $table,
            $primary_key,
            $id
        ));
    }
}
if (!function_exists('purchaseOrderOpendate')) {
    function purchaseOrderOpendate($order_id)
    {
        global $DB;

        if ($row = $DB->query_first(sprintf(
            "SELECT open_date,invoice_currency FROM `purchase_order` WHERE `order_id` = %d",
            $order_id,
        ), MYSQLI_ASSOC)) {
            return $row;
        }
    }
}
if (!function_exists('purchaseOrderSupplierProductsFromDatabase')) {
    function purchaseOrderSupplierProductsFromDatabase($product_id,$supplier_id)
    {
        global $DB;
        $result = array();
        //$invoice_id=mysqli_real_escape_string($invoice_id);
        $query = $DB->query("SELECT `ip`.* FROM `purchase_order_products` AS `ip` WHERE `ip`.`product_id` = " . intval($product_id) ." AND `ip`.`product_supplier_id` = " . intval($supplier_id) ." ORDER BY `id` ASC");
        while ($row = $DB->fetch_assoc($query)) {
            $result[] = $row;
        }

        $DB->free_result($query);

       
        return $result;
    }
}
if (!function_exists('totalunitPurchasesSupplierProductsFromDatabase')) {
    function totalunitPurchasesSupplierProductsFromDatabase($product_id,$supplier_id)
    {
        global $DB;
        //$result = array();
        //$invoice_id=mysqli_real_escape_string($invoice_id);
        $result = $DB->query_first("SELECT SUM(`ip`.movement) as total FROM `products_inventory` AS `ip` WHERE `ip`.`product_id` = " . intval($product_id) ." AND `ip`.`supplier_id` = " . intval($supplier_id) ." AND `ip`.`movement`>0");
       
        return $result['total'];
    }
}
if (!function_exists('productGenericFromDatabase')) {
    function productGenericFromDatabase($id, $table, $primary_key = 'id') {
        global $DB;
        return $DB->query_first(sprintf(
            "SELECT *, UNIX_TIMESTAMP(`created_at`) AS `created_time`, UNIX_TIMESTAMP(`updated_at`) AS `updated_time` FROM %s WHERE `%s` = %d LIMIT 1",
            $table,
            $primary_key,
            $id
        ), MYSQLI_ASSOC);
    }
}
if (!function_exists('productGenericGetValue')) {
    function productGenericGetValue($id, $field, $table, $primary_key = 'id') {
        global $DB;
        $fields     = $DB->table_columns($table);
        if (array_key_exists($field, $fields)) {
            $row = $DB->query_first(sprintf(
                "SELECT `%s` FROM %s WHERE `%s` = %d LIMIT 1",
                $field,
                $table,
                $primary_key,
                $id
            ));
            if ($row) {
                return $row[$field];
            }
        }
        return null;
    }
}
if (!function_exists('productGenericSetValue')) {
    function productGenericSetValue($id, $field, $value, $table, $primary_key = 'id') {
        global $DB;
        $fields     = $DB->table_columns($table);
        if (array_key_exists($field, $fields)) {
            $DB->query(sprintf(
                "UPDATE %s SET `%s` = %s WHERE `%s` = %d LIMIT 1",
                $table,
                $field,
                secho($value, 1),
                $primary_key,
                $id
            ));
            return $DB->affected_rows();
        }
        return 0;
    }
}
if (!function_exists('productGenericToDatabase')) {
    function productGenericToDatabase(&$item, $table, $primary_key = 'id') {
        global $DB, $_GLOBALS;
        $id         = intval($item[$primary_key]);
        $fields     = $DB->table_columns($table);
        $old_vals   = $DB->query_first(sprintf(
            "SELECT * FROM %s WHERE `%s` = %d LIMIT 1",
            $table,
            $primary_key,
            $id
        ), MYSQLI_ASSOC);
        if ($old_vals) {
            $set_vals = array();
            foreach ($fields as $k => $v) {
                if (array_key_exists($k, $item) && $item[$k] !=$old_vals[$k]) {
                    if (is_array($item[$k])) {
                        $set_vals[$k] = "`" . $k . "` = " . $DB->escape(implode(",", $item[$k]));
                    } elseif (isset($item[$k])) {
                        $set_vals[$k] = "`" . $k . "` = " . $DB->escape($item[$k]);
                    } else {
                        $set_vals[$k] = "`" . $k . "` = NULL";
                    }
                }
            }
            if (count($set_vals)) {
                $item['updated_by'] = $_GLOBALS['staff']['id'];
                $set_vals['updated_by'] = "`updated_by` = " . intval($item['updated_by']);
                if($table=='`supplier_product`'){
                   $set_vals['updated_at'] = "`updated_at` = NOW()";
                }

                $DB->query(sprintf(
                    "UPDATE %s SET %s WHERE `%s` = %d LIMIT 1",
                    $table,
                    implode(', ', $set_vals),
                    $primary_key,
                    $id
                ));
            }
        } else {
            $item['created_by'] = $_GLOBALS['staff']['id'];
                        
            $insert_vals = array();
            foreach ($fields as $k => $v) {
                if (array_key_exists($k, $item)) {
                    if (is_array($item[$k])) {
                        $insert_vals[$k] = $DB->escape(implode(",", $item[$k]));
                    } elseif (isset($item[$k])) {
                        $insert_vals[$k] = $DB->escape($item[$k]);
                    } else {
                        $insert_vals[$k] = "NULL";
                    }
                }
            }
            if (count($insert_vals)) {
                $DB->query(sprintf(
                    "INSERT INTO %s (`%s`) VALUES (%s)",
                    $table,
                    implode('`, `', array_keys($insert_vals)),
                    implode(", ", $insert_vals)
                ));
                if($table!='`products_inventory`'){
                    $item[$primary_key] = $id = $DB->insert_id();
                }else{
                    $id = $DB->insert_id();
                }

            }
        }
        return $id;
    }
}
if (!function_exists('productGenericsDelete')) {
    function productGenericsDelete($id, $table, $secondary_key = 'product_id') {
        global $DB;
        $DB->query(sprintf(
            "UPDATE %s SET `deleted_at` = NOW() WHERE `deleted_at` IS NULL AND `%s` = %d",
            //"DELETE FROM %s WHERE `%s` = %d",
            $table,
            $secondary_key,
            $id
        ));
        return $DB->affected_rows();
    }
}
if (!function_exists('productGenericsFromDatabase')) {
    function productGenericsFromDatabase($id, $table, $primary_key = 'id', $secondary_key = 'product_id',$orderby='') {
        global $DB;
        $result = array();
        $query = $DB->query(sprintf(
            "SELECT *, UNIX_TIMESTAMP(`created_at`) AS `created_time`, UNIX_TIMESTAMP(`updated_at`) AS `updated_time` FROM %s WHERE `deleted_at` IS NULL AND `%s` = %d  %s",
            $table,
            $secondary_key,
            $id,
            $orderby,
        ));
        while ($row = $DB->fetch_assoc($query)) {
            if (is_null($primary_key)) {
                $result[] = $row;
            } else {
                $result[$row[$primary_key]] = $row;
            }
        }
        $DB->free_result($query);
        return $result;
    }
}
if (!function_exists('productInventoryFromDatabasewithcolor')) {
    function productInventoryFromDatabasewithcolor($id,$color_id,$table='`products_inventory`', $primary_key = 'id', $secondary_key = 'product_id') {
        global $DB;
        $result = array();
        $query = $DB->query(sprintf(
            "SELECT *, UNIX_TIMESTAMP(`created_at`) AS `created_time`, UNIX_TIMESTAMP(`updated_at`) AS `updated_time` FROM %s WHERE `deleted_at` IS NULL AND `%s` = %d AND `color_id`=%d",
            $table,
            $secondary_key,
            $id,
            $color_id
        ));
        while ($row = $DB->fetch_assoc($query)) {
            if (is_null($primary_key)) {
                $result[] = $row;
            } else {
                $result[$row[$primary_key]] = $row;
            }
        }
        $DB->free_result($query);
        return $result;
    }
}
if (!function_exists('productGenericsFromPost')) {
    function productGenericsFromPost($posted, $table)
    {
         global $DB;
        if (!is_array($posted)) {
            return array();
        }
        $result = array();
        $fields = $DB->table_columns($table);
        foreach ($posted as $k1 => $v1) {
            $item = array();
            foreach ($fields as $k2 => $v2) {
                if (array_key_exists($k2, $v1)) {
                    $item[$k2] = $v1[$k2];
                }
            }
            $result[$k1] = $item;
        }
        return $result;
    }
}
if (!function_exists('productSuppliersGenericsFromPost')) {
    function productSuppliersGenericsFromPost($posted, $table)
    {
         global $DB;
        if (!is_array($posted)) {
            return array();
        }
        $result = array();
        $fields = $DB->table_columns($table);
        foreach ($posted as $k1 => $v1) {
            $item = array();
            foreach ($fields as $k2 => $v2) {
                if (array_key_exists($k2, $v1)) {
                    $item[$k2] = $v1[$k2];
                }
            }
            if(!array_key_exists('main', $item)){
                $item['main']='n';
            }
            $result[$k1] = $item;
        }
        return $result;
    }
}
if (!function_exists('productGenericsToDatabase')) {
    function productGenericsToDatabase($id, &$items, $table, $primary_key = 'id', $secondary_key = 'product_id') {
        foreach ($items as &$v) {
            if($id){
                $v[$secondary_key] = $id;
            }else{
                $v[$secondary_key] = $v['product_id'];
            }
            productGenericToDatabase($v, $table, $primary_key);
        }
    }
}

if (!function_exists('productBulkPriceDelete')) {
    function productBulkPriceDelete($id)
    {
        return productGenericDelete($id, '`products_bulk_pricing`');
    }
}
if (!function_exists('productBulkPriceFromDatabase')) {
    function productBulkPriceFromDatabase($id)
    {
        return productGenericFromDatabase($id, '`products_bulk_pricing`');
    }
}
if (!function_exists('productBulkPriceToDatabase')) {
    function productBulkPriceToDatabase(&$item)
    {
        return productGenericToDatabase($item, '`products_bulk_pricing`');
    }
}
if (!function_exists('productBulkPricingDelete')) {
    function productBulkPricingDelete($id)
    {
        return productGenericsDelete($id, '`products_bulk_pricing`');
    }
}
if (!function_exists('productBulkPricingFromDatabase')) {
    function productBulkPricingFromDatabase($id)
    {
        $result = productGenericsFromDatabase($id, '`products_bulk_pricing`');
        uasort($result, function($a, $b) {
            return $b['product_qty'] - $a['product_qty'];
        });
        return $result;
    }
}
if (!function_exists('productBulkPricingFromPost')) {
    function productBulkPricingFromPost($posted)
    {
        return productGenericsFromPost($posted, '`products_bulk_pricing`');
    }
}
if (!function_exists('productBulkPricingToDatabase')) {
    function productBulkPricingToDatabase($id, &$items)
    {
        return productGenericsToDatabase($id, $items, '`products_bulk_pricing`');
    }
}

if (!function_exists('productBulkWeightDelete')) {
    function productBulkWeightDelete($id)
    {
        return productGenericDelete($id, '`products_bulk_weights`');
    }
}
if (!function_exists('productBulkWeightFromDatabase')) {
    function productBulkWeightFromDatabase($id)
    {
        return productGenericFromDatabase($id, '`products_bulk_weights`');
    }
}
if (!function_exists('productBulkWeightToDatabase')) {
    function productBulkWeightToDatabase(&$item)
    {
        return productGenericToDatabase($item, '`products_bulk_weights`');
    }
}
if (!function_exists('productBulkWeightsDelete')) {
    function productBulkWeightsDelete($id)
    {
        return productGenericsDelete($id, '`products_bulk_weights`');
    }
}
if (!function_exists('productBulkWeightsFromDatabase')) {
    function productBulkWeightsFromDatabase($id)
    {
        $result = productGenericsFromDatabase($id, '`products_bulk_weights`');
        uasort($result, function($a, $b) {
            return $b['qty'] - $a['qty'];
        });
        return $result;
    }
}
if (!function_exists('productBulkWeightsFromPost')) {
    function productBulkWeightsFromPost($posted)
    {
        return productGenericsFromPost($posted, '`products_bulk_weights`');
    }
}
if (!function_exists('productBulkWeightsToDatabase')) {
    function productBulkWeightsToDatabase($id, &$items)
    {
        return productGenericsToDatabase($id, $items, '`products_bulk_weights`');
    }
}

if (!function_exists('productCategoryDelete')) {
    function productCategoryDelete($id)
    {
        return productGenericDelete($id, '`products_linked_categories`');
    }
}
if (!function_exists('productCategoryFromDatabase')) {
    function productCategoryFromDatabase($id)
    {
        return productGenericFromDatabase($id, '`products_linked_categories`');
    }
}
if (!function_exists('productCategoryToDatabase')) {
    function productCategoryToDatabase(&$item)
    {
        return productGenericToDatabase($item, '`products_linked_categories`');
    }
}
if (!function_exists('productCategoriesDelete')) {
    function productCategoriesDelete($id)
    {
        return productGenericsDelete($id, '`products_linked_categories`');
    }
}
if (!function_exists('productCategoriesFromDatabase')) {
    function productCategoriesFromDatabase($id)
    {
        return productGenericsFromDatabase($id, '`products_linked_categories`');
    }
}
if (!function_exists('productCategoriesFromPost')) {
    function productCategoriesFromPost($posted)
    {
        return productGenericsFromPost($posted, '`products_linked_categories`');
    }
}
if (!function_exists('productCategoriesToDatabase')) {
    function productCategoriesToDatabase($id, &$items)
    {
        return productGenericsToDatabase($id, $items, '`products_linked_categories`');
    }
}

if (!function_exists('productCommentDelete')) {
    function productCommentDelete($id)
    {
        return productGenericDelete($id, '`products_comments`');
    }
}
if (!function_exists('productCommentFromDatabase')) {
    function productCommentFromDatabase($id)
    {
        return productGenericFromDatabase($id, '`products_comments`');
    }
}
if (!function_exists('productCommentQuery')) {
    function productCommentQuery($criteria = array(), $lessthan = array(), $greaterthan = array())
    {
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

        $whereAND    = array();
        $field_types = $DB->table_types('`products_comments`');
        $all_fields  = array();
        foreach ($field_types as $k => $v) {
            $all_fields[$k] = "`" . $k . "`";
        }
        foreach ($all_fields as $field => $expr) {
            if (array_key_exists($field, $criteria)) {
                $whereOR = array();
                foreach ($criteria[$field] as $v) {
                    if (array_key_exists($field, $field_types) && substr($field_types[$field], 0, 4) == 'set(') {
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
                        '`comments`',
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
if (!function_exists('productCommentSearch')) {
    function productCommentSearch($criteria = null)
    {
        global $DB;

        if (is_array($criteria)) {
            $criteria = productCommentQuery($criteria);
        }

        $results = array();
        $query   = $DB->query(sprintf(
            "SELECT `id` FROM `products_comments`%s ORDER BY `id` ASC",
            (!is_null($criteria)) ? sprintf(" WHERE %s", $criteria) : ""
        ));
        while ($row = $DB->fetch_assoc($query)) {
            $results[] = $row['id'];
        }
        $DB->free_result($query);

        return $results;
    }
}
if (!function_exists('productCommentToDatabase')) {
    function productCommentToDatabase(&$item)
    {
        return productGenericToDatabase($item, '`products_comments`');
    }
}
if (!function_exists('productCommentsDelete')) {
    function productCommentsDelete($id)
    {
        return productGenericsDelete($id, '`products_comments`');
    }
}
if (!function_exists('productCommentsFromDatabase')) {
    function productCommentsFromDatabase($id)
    {
        return productGenericsFromDatabase($id, '`products_comments`');
    }
}
if (!function_exists('productCommentsFromPost')) {
    function productCommentsFromPost($posted)
    {
        return productGenericsFromPost($posted, '`products_comments`');
    }
}
if (!function_exists('productCommentsToDatabase')) {
    function productCommentsToDatabase($id, &$items)
    {
        return productGenericsToDatabase($id, $items, '`products_comments`');
    }
}

if (!function_exists('productContainerDelete')) {
    function productContainerDelete($id)
    {
        return productGenericDelete($id, '`container_load`', 'product_id');
    }
}
if (!function_exists('productContainerFromDatabase')) {
    function productContainerFromDatabase($id)
    {
        return productGenericFromDatabase($id, '`container_load`', 'product_id');
    }
}
if (!function_exists('productContainerToDatabase')) {
    function productContainerToDatabase(&$item)
    {
        return productGenericToDatabase($item, '`container_load`', 'product_id');
    }
}
if (!function_exists('productContainersDelete')) {
    function productContainersDelete($id)
    {
        return productGenericsDelete($id, '`container_load`');
    }
}
if (!function_exists('productContainersFromDatabase')) {
    function productContainersFromDatabase($id)
    {
        return productGenericsFromDatabase($id, '`container_load`', 'product_id');
    }
}
if (!function_exists('productContainersFromPost')) {
    function productContainersFromPost($posted)
    {
        return productGenericsFromPost($posted, '`container_load`');
    }
}
if (!function_exists('productContainersToDatabase')) {
    function productContainersToDatabase($id, &$items)
    {
        return productGenericsToDatabase($id, $items, '`container_load`', 'product_id');
    }
}

if (!function_exists('productDimensionDelete')) {
    function productDimensionDelete($id)
    {
        return productGenericDelete($id, '`products_dimen`');
    }
}
if (!function_exists('productDimensionFromDatabase')) {
    function productDimensionFromDatabase($id)
    {
        return productGenericFromDatabase($id, '`products_dimen`');
    }
}
if (!function_exists('productDimensionFromPost')) {
    function productDimensionFromPost($posted)
    {

        return productGenericsFromPost($posted, '`products_dimen`');
    }
}
if (!function_exists('updateProductConsignmentCost')) {
    function updateProductConsignmentCost($posted)
    {
        $data_cost=[];
        $product_sales_cost=[];
        $data_cost['cost_proce_usd']=convertCurrency($posted['cost_price'], $posted['cost_currency'], 'USD', false, $posted['precision']);
        $data_cost['cost_price']=$posted['cost_price'];
        $data_cost['cost_currency']=$posted['cost_currency'];
        $data_cost['markup']=$posted['markup'];
        $markup=$posted['markup'];
        $salesprice_usd=((($data_cost['cost_proce_usd']*$markup)/100)+$data_cost['cost_proce_usd']);
        $salesprice_usd=number_format((float) $salesprice_usd, 2, '.', '');
        $data_cost['sales_price_usd']=$salesprice_usd;
        $data_cost['avg_cost_price']=productAverageCostPrice($posted['id']);
        $data_cost['profit']=$data_cost['sales_price_usd']-$data_cost['cost_proce_usd'];
        $product_sales_cost['sales_price']=$data_cost['sales_price']=convertCurrency($data_cost['sales_price_usd'],'USD' ,$posted['sales_currency'], false, $posted['precision']);
        $data_cost['sales_currency']=$posted['sales_currency'];
        $data_cost['created_at']=formatDateTime();
        $exRates = getExchangeRateList();
        $data_cost['exchange_rate']=$exRates[$posted['cost_currency']];
        $product_sales_cost['id']=$data_cost['product_id']=$posted['id'];

        productGenericToDatabase($data_cost, '`consignment_price_update`');

        productToDatabase($product_sales_cost);

    }
}
if (!function_exists('getConsigmentUpdatePrice')) {
    function getConsigmentUpdatePrice($id)
    {
        global $DB;
        //$data=[];
          $data = $DB->query_first(sprintf(
            "SELECT *, UNIX_TIMESTAMP(`created_at`) AS `created_time` FROM %s  Where `%s` = %d  %s",
            '`consignment_price_update`',
            'product_id',
             $id,
            'ORDER BY `created_at` DESC LIMIT 1'
        ));
        
        /*while ($row = $DB->fetch_assoc($query)) {
            if (is_null($primary_key)) {
            $data[] = $row;
            } 
        }*/
        //$DB->free_result($query);
        return ($data)?$data:[];
    }
}
if (!function_exists('productDimensionToDatabase')) {
    function productDimensionToDatabase(&$item)
    {
        return productGenericToDatabase($item, '`products_dimen`');
    }
}
if (!function_exists('productDimensionsDelete')) {
    function productDimensionsDelete($id)
    {
        return productGenericsDelete($id, '`products_dimen`', 'id');
    }
}
if (!function_exists('productDimensionsFromDatabase')) {
    function productDimensionsFromDatabase($id)
    {
        return productGenericsFromDatabase($id, '`products_dimen`', 'id', 'id');
    }
}
if (!function_exists('productDimensionsFromPost')) {
    function productDimensionsFromPost($posted)
    {
        return productGenericsFromPost($posted, '`products_dimen`');
    }
}
if (!function_exists('productDimensionsToDatabase')) {
    function productDimensionsToDatabase($id, &$items)
    {
        return productGenericsToDatabase($id, $items, '`products_dimen`', 'id', 'id');
    }
}

if (!function_exists('productInventorySingleDelete')) {
    function productInventorySingleDelete($id)
    {
        return productGenericDelete($id, '`products_inventory`');
    }
}
if (!function_exists('productInventorySingleFromDatabase')) {
    function productInventorySingleFromDatabase($id)
    {
        return productGenericFromDatabase($id, '`products_inventory`');
    }
}
if (!function_exists('productInventorySingleToDatabase')) {
    function productInventorySingleToDatabase(&$item)
    {
        return productGenericToDatabase($item, '`products_inventory`');
    }
}
if (!function_exists('productInventoryDelete')) {
    function productInventoryDelete($id)
    {
        return productGenericsDelete($id, '`products_inventory`');
    }
}
if (!function_exists('productInventoryFromDatabase')) {
    function productInventoryFromDatabase($id)
    {
        return productGenericsFromDatabase($id, '`products_inventory`','id','product_id','ORDER BY `id` DESC');
    }
}
if (!function_exists('productInventoryFromPost')) {
    function productInventoryFromPost($posted)
    {
        return productGenericsFromPost($posted, '`products_inventory`');
    }
}
if (!function_exists('productInventoryToDatabase')) {
    function productInventoryToDatabase($id, $items)
    {
        return productGenericsToDatabase($id, $items, '`products_inventory`');
    }
}

if (!function_exists('productInvoicesFromDatabase')) {
    function productInvoicesFromDatabase($id)
    {
        moduleLoad('invoice'); //invoiceProductQuery, invoiceProductGroupCount

        //@todo Make these functions
        if (!function_exists('invoiceProductQuery') || !function_exists('invoiceProductGroupCount')) {
            return [];
        }

        $result = [];
        foreach (invoiceProductGroupCount(invoiceProductQuery([
            'product_id' => $id,
        ]), 'invoice_id') as $invoice_id => $count) {
            $result[] = $invoice_id;
        }

        return $result;
    }
}

if (!function_exists('productManualDelete')) {
    function productManualDelete($id)
    {
        return productGenericDelete($id, '`product_safety_approvels`');
    }
}
if (!function_exists('productManualFromDatabase')) {
    function productManualFromDatabase($id)
    {
        return productManualVirtual(productGenericFromDatabase($id, '`product_safety_approvels`'));
    }
}
if (!function_exists('productManualGetValue')) {
    function productManualGetValue($id, $field) {
        return productGenericGetValue($id, $field, '`product_safety_approvels`');
    }
}
if (!function_exists('productSafetyGetValue')) {
    function productSafetyGetValue($id, $field, $value) {
        $result = productGenericGetValue($id, $field, '`product_safety_approvels`');
        if (!is_null($result)) {
            return $result;
        }
        $item = productManualFromDatabase($id);
        if (array_key_exists($field, $item)) {
            return $item[$field];
        }
        return null;
    }
}
if (!function_exists('productManualToDatabase')) {
    function productManualToDatabase(&$item)
    {
        return productGenericToDatabase($item, '`product_safety_approvels`');
    }
}
if (!function_exists('productManualVirtual')) {
    function productManualVirtual($manual)
    {
        if (is_null($manual)) {
            return null;
        }

       // TODO(__FUNCTION__, func_get_args());return $manual; //TODO: Double check this function

        $manual['file_name'] = basename($manual['file_name']);

        $manual_path = productGetOptionList('manual_path');
        $test        = $manual_path . $manual['file_name'];
        if (is_file($test)) {

            $split              = explode('.', $manual['file_original']);
            $ext                = strtolower(array_pop($split));
            $manual['view_url'] = sprintf("/manuals/%d.%s", $manual['id'], $ext);
            if ('' !== $manual['slug']) {
                $manual['view_url'] = sprintf("/manuals/%s-%d.%s", $manual['slug'], $manual['id'], $ext);
            }

            $manual['filetype'] = mime_content_type($test);

            $manual['size'] = filesize($test);
        }
                return $manual;
    }
}
if (!function_exists('productSafetyCopy')) {
    function productSafetyCopy($id, $new_id)
    {
        foreach (productSafetyFromDatabase($id) as $v) {
            unset($v['id']);
            $v['product_id'] = $new_id;
            productManualToDatabase($v);
        }
    }
}
if (!function_exists('productSafetyDelete')) {
    function productSafetyDelete($id)
    {
        return productGenericsDelete($id, '`product_safety_approvels`');
    }
}
if (!function_exists('productSafetyFromDatabase')) {
    function productSafetyFromDatabase($id)
    {
        $result = array();
        foreach(productGenericsFromDatabase($id, '`product_safety_approvels`') as $k => $v) {
            $result[$k] = productManualVirtual($v);
        }
        return $result;
    }
}
if (!function_exists('productSafetyFromPost')) {
    function productSafetyFromPost($posted)
    {
        return productGenericsFromPost($posted, '`product_safety_approvels`');
    }
}
if(!function_exists('productArtWorkFromPost')){
    function productArtWorkFromPost($posted,$files,$key){
          global $DB;
         if(isset($files['name'][$key]['template_file'])){
            $file_orig = $files['name'][$key]['template_file'];
            $file_temp = $files['tmp_name'][$key]['template_file'];
            $error = $files['error'][$key]['template_file'];
            $size = intval($files['size'][$key]['template_file']);
            $file_ext=substr($file_orig,strrpos($file_orig,'.'));
            if(substr($file_ext,0,1)=='.') 
            $file_ext=substr($file_ext,1);
            $file_new = "template_file_".time().'_'.rand().'.'.$file_ext; 
            $dir = $_SERVER['DOCUMENT_ROOT'].'/webadmin/uploads/product/art_work/';
            if(move_uploaded_file($file_temp,$dir.$file_new)){
                $posted['template_image']=$file_new;
            }
        }
         if(isset($files['name'][$key]['product_file'])){
            $file_orig = $files['name'][$key]['product_file'];
            $file_temp = $files['tmp_name'][$key]['product_file'];
            $error = $files['error'][$key]['product_file'];
            $size = intval($files['size'][$key]['product_file']);
            $file_ext=substr($file_orig,strrpos($file_orig,'.'));
            if(substr($file_ext,0,1)=='.') 
            $file_ext=substr($file_ext,1);
            $file_new = "product_file_".time().'_'.rand().'.'.$file_ext; 
            $dir = $_SERVER['DOCUMENT_ROOT'].'/webadmin/uploads/product/art_work/';
            if(move_uploaded_file($file_temp,$dir.$file_new)){
                $posted['product_image']=$file_new;
            }
        }
        if (!is_array($posted)) {
            return array();
        }
        $result = array();
        $fields = $DB->table_columns('`product_art_work`');
        foreach ($posted as $k1 => $v1) {
                if (array_key_exists($k1,$fields)) {
                    if($v1 || $v1==0){
                        $result[$k1] = $v1;
                    }    
                }
        }
        return $result;
    }
}
if(!function_exists('productSubModuleFromPost')){
    function productSubModuleFromPost($posted,$files,$key){
        global $DB;
        if (!is_array($posted)) {
            return array();
        }
         if(isset($files['name'][$key]['product_file'])){
            $file_orig = $files['name'][$key]['product_file'];
            $file_temp = $files['tmp_name'][$key]['product_file'];
            $error = $files['error'][$key]['product_file'];
            $size = intval($files['size'][$key]['product_file']);
            $file_ext=substr($file_orig,strrpos($file_orig,'.'));
            if(substr($file_ext,0,1)=='.') 
            $file_ext=substr($file_ext,1);
            $file_new = "sub_product_file_".time().'_'.rand().'.'.$file_ext; 
            $dir = $_SERVER['DOCUMENT_ROOT'].'/webadmin/uploads/product/art_work/';
            if(move_uploaded_file($file_temp,$dir.$file_new)){
                $posted['product_image']=$file_new;
            }
        }
        $result = array();
        $fields = $DB->table_columns('`product_sub_module`');
        foreach ($posted as $k1 => $v1) {
                if (array_key_exists($k1,$fields)) {
                    if($v1 || $v1==0){
                        $result[$k1] = $v1;
                    }    
                }
        }
        return $result;
    }
}
if (!function_exists('productSafetyToDatabase')) {
    function productSafetyToDatabase($id, &$items)
    {
        foreach ($items as &$v) {
            if(!$v['product_id']){
                $v['product_id']=$id;
            }
            if($v['id']){
                productGenericToDatabase($v,'`product_safety_approvels`', 'id');
            }
        }
    }
}
if (!function_exists('productAdminFilesFromDatabase')) {
    function productAdminFilesFromDatabase($id)
    {
        $result = array();
        foreach(productGenericsFromDatabase($id, '`product_admin_files`') as $k => $v) {
            $result[$k] = productManualVirtual($v);
        }
        return $result;
    }
}
if (!function_exists('productAdminFilesFromPost')) {
    function productAdminFilesFromPost($posted)
    {
        return productGenericsFromPost($posted, '`product_admin_files`');
    }
}
if (!function_exists('productAdminFilesDelete')) {
    function productAdminFilesDelete($id)
    {
        return productGenericsDelete($id, '`product_admin_files`','id');
    }
}
if (!function_exists('productArtWorkDelete')) {
    function productArtWorkDelete($id)
    {
        return productGenericsDelete($id, '`product_art_work`','id');
    }
}
if (!function_exists('SubproductDelete')) {
    function SubproductDelete($id)
    {
        return productGenericsDelete($id, '`product_sub_module`','id');
    }
}
if (!function_exists('productAdminFilesToDatabase')) {
    function productAdminFilesToDatabase($id, &$items)
    {
        foreach ($items as &$v) {
            if(!$v['product_id']){
                $v['product_id']=$id;
            }
            if($v['id']){
                productGenericToDatabase($v,'`product_admin_files`', 'id');
            }
        }
    }
}
if (!function_exists('productSerialNoDelete')) {
    function productSerialNoDelete($id)
    {
        global $DB;
        $DB->query(sprintf(
        "UPDATE  `highwaygroup`.`serial_numbers` set `product_id`=0,`member_id`=0,`invoice_id`=0,`product_str`=''  WHERE `id` = %d LIMIT 1",
        $id
        ));
    }
}
if (!function_exists('productSerialNoFromPost')) {
    function productSerialNoFromPost($posted)
    {
         global $DB;
        if (!is_array($posted)) {
            return array();
        }
        $result = array();
        $fields = $DB->table_columns('`highwaygroup`.`serial_numbers`');

        foreach ($posted as $k1 => $v1) {
                if (array_key_exists($k1,$fields)) {
                    if($v1 || $v1==0){
                        $result[$k1] = $v1;
                    }    
                }
        }
        return $result;
    }
}
if (!function_exists('productSerialNoToDatabase')) {
    function productSerialNoToDatabase(&$item)
    {
        return productGenericToDatabase($item, '`highwaygroup`.`serial_numbers`');
    }
}
/*if (!function_exists('productMoviesDelete')) {
    function productMoviesDelete($id)
    {
        return productGenericsDelete($id, '`product_videos`');
    }
}
if (!function_exists('productMoviesFromDatabase')) {
    function productMoviesFromDatabase($id)
    {
        $result = productGenericsFromDatabase($id, '`product_videos`');
        uasort($result, function($a, $b) {
            return $a['rank'] - $b['rank'];
        });
        return $result;
    }
}
if (!function_exists('productMoviesFromPost')) {
    function productMoviesFromPost($posted)
    {
        return productGenericsFromPost($posted, '`product_videos`');
    }
}
if (!function_exists('productMoviesToDatabase')) {
    function productMoviesToDatabase($id, &$items)
    {
        return productGenericsToDatabase($id, $items, '`product_videos`');
    }
}*/

if (!function_exists('productOrderFromDatabase')) {
    function productOrderFromDatabase($id)
    {
        return productGenericFromDatabase($id, '`products_orders`');
    }
}
if (!function_exists('productOrderToDatabase')) {
    function productOrderToDatabase(&$item)
    {
        return productGenericToDatabase($item, '`products_orders`');
    }
}

if (!function_exists('productPackingDelete')) {
    function productPackingDelete($id)
    {
        return productGenericDelete($id, '`product_packing`', 'packing_id');
    }
}
if (!function_exists('productPackingFromDatabase')) {
    function productPackingFromDatabase($id)
    {
        $v = productGenericFromDatabase($id, '`product_packing`', 'packing_id');
        $v['_volume'] = bcmul($v['width'], bcmul($v['height'], $v['depth'], 2), 3);
        $v['_cubic'] = bcdiv($v['_volume'], '5000', 3);
        $v['_weight'] = (bccomp($v['weight'], $v['_cubic'], 3) >= 0) ? $v['weight'] : $v['_cubic'];
        return $v;
    }
}
if (!function_exists('productPackingToDatabase')) {
    function productPackingToDatabase(&$item)
    {
        return productGenericToDatabase($item, '`product_packing`', 'packing_id');
    }
}
if (!function_exists('productPackingsDelete')) {
    function productPackingsDelete($id)
    {
        return productGenericsDelete($id, '`product_packing`');
    }
}
if (!function_exists('productPackingsFromDatabase')) {
    function productPackingsFromDatabase($id,$key='id')
    {
        $result = [];
        foreach (productGenericsFromDatabase($id, '`product_packing`',$key) as $k => $v) {
            $v['_volume'] = bcmul($v['width'], bcmul($v['height'], $v['depth'], 2), 3);
            $v['_cubic'] = bcdiv($v['_volume'], '5000', 3);
            $v['_weight'] = (bccomp($v['weight'], $v['_cubic'], 3) >= 0) ? $v['weight'] : $v['_cubic'];
            $result[$k] = $v;
        }
        return $result;
    }
}
if (!function_exists('productPackingsFromPost')) {
    function productPackingsFromPost($posted)
    {
        return productGenericsFromPost($posted, '`product_packing`');
    }
}
if (!function_exists('productPackingsToDatabase')) {
    function productPackingsToDatabase($id, &$items)
    {
        return productGenericsToDatabase($id, $items, '`product_packing`', 'packing_id');
    }
}
if (!function_exists('productPartofDelete')) {
    function productPartofDelete($id)
    {
        return productGenericDelete($id, '`products_partof`');
    }
}
if (!function_exists('productPartofFromDatabase')) {
    function productPartofFromDatabase($id)
    {
        return productGenericFromDatabase($id, '`products_partof`');
    }
}
if (!function_exists('productPartofToDatabase')) {
    function productPartofToDatabase(&$items)
    {
            foreach ($items as &$v) {
                if(!$v['id']){
                  $insert_id =productGenericToDatabase($v, '`products_partof`', 'id');
                  $j=[];
                  $j['id']='';
                  $j['product_id']=$v['partof_id'];
                  $j['partof_id']=$v['product_id'];
                  $j['parent_id']=$insert_id;
                  $insert_id1 =productGenericToDatabase($j, '`products_partof`', 'id');
                  $v['id']=$insert_id;
                  $v['parent_id']=$insert_id1;
                  productGenericToDatabase($v, '`products_partof`', 'id');
                }  
            }
    }
}
if (!function_exists('productPartsofDelete')) {
    function productPartsofDelete($id)
    {
        productGenericsDelete($id, '`products_partof`','id');
        productGenericsDelete($id, '`products_partof`','parent_id');
    }
}
if (!function_exists('productPartsofFromDatabase')) {
    function productPartsofFromDatabase($id)
    {
        $result = productGenericsFromDatabase($id, '`products_partof`');
        uasort($result, function($a, $b) {
            return $b['partof_id'] - $a['partof_id'];
        });
        return $result;
    }
}
if (!function_exists('productPartsofFromPost')) {
    function productPartsofFromPost($posted)
    {
        return productGenericsFromPost($posted, '`products_partof`');
    }
}
if (!function_exists('productUpgradeLinkingFromPost')) {
    function productUpgradeLinkingFromPost($posted)
    {
        return productGenericsFromPost($posted, '`products_upgrade_linking`');
    }
}
if (!function_exists('productUpgradeLinkingToDatabase')) {
    function productUpgradeLinkingToDatabase(&$items)
    {
            foreach ($items as &$v) {
                $v['show_front']=(isset($v['show_front']))?$v['show_front']:'n';
                if(!$v['id']){
                  $insert_id =productGenericToDatabase($v, '`products_upgrade_linking`', 'id');
                  $j=[];
                  $j['id']='';
                  $j['product_id']=$v['linking_id'];
                  $j['linking_id']=$v['product_id'];
                  $j['parent_id']=$insert_id;
                  $insert_id1 =productGenericToDatabase($j, '`products_upgrade_linking`', 'id');
                  $v['id']=$insert_id;
                  $v['parent_id']=$insert_id1;
                  productGenericToDatabase($v, '`products_upgrade_linking`', 'id');
                }else{
                productGenericToDatabase($v, '`products_upgrade_linking`', 'id');
                }  
            }
    }
}
if (!function_exists('productUpgradeLinkingDelete')) {
    function productUpgradeLinkingDelete($id)
    {
        productGenericsDelete($id, '`products_upgrade_linking`','id');
        productGenericsDelete($id, '`products_upgrade_linking`','parent_id');
    }
}
if (!function_exists('productUpgradeLinkingFromDatabase')) {
    function productUpgradeLinkingFromDatabase($id)
    {
        $result = productGenericsFromDatabase($id, '`products_upgrade_linking`','id','product_id','ORDER BY `rank` ASC');
        /*uasort($result, function($a, $b) {
            return $b['linking_id'] - $a['linking_id'];
        });*/
        return $result;
    }
}
if (!function_exists('productMonitorPartLinkingFromPost')) {
    function productMonitorPartLinkingFromPost($posted)
    {
        return productGenericsFromPost($posted, '`products_monitor_part_linking`');
    }
}
if (!function_exists('monitorGenericsFromPost')) {
    function monitorGenericsFromPost($posted, $table)
    {
         global $DB;
        if (!is_array($posted)) {
            return array();
        }
        $result = array();
        $fields = $DB->table_columns($table);
        foreach ($posted as $k1 => $v1) {
                if (array_key_exists($k1,$fields)) {
                    if($v1 || $v1==0){
                        $result[$k1] = $v1;
                    }    
                }
        }
        return $result;
    }
}
if (!function_exists('productMonitorFromPost')) {
    function productMonitorFromPost($posted)
    {
        return monitorGenericsFromPost($posted, '`products_monitors`');
    }
}
if (!function_exists('productMonitorToDatabase')) {
    function productMonitorToDatabase(&$items)
    {
            return productGenericToDatabase($items, '`products_monitors`', 'id');
    }
}
if (!function_exists('productMonitorPartLinkingToDatabase')) {
    function productMonitorPartLinkingToDatabase(&$items,$id)
    {
        foreach ($items as &$v) {
            $v['monitor_id']=$id;
            productGenericToDatabase($v, '`products_monitor_part_linking`', 'id');
        }
    }
}
if (!function_exists('productMonitorPartLinkingDelete')) {
    function productMonitorPartLinkingDelete($id)
    {
        productGenericsDelete($id, '`products_monitor_part_linking`','id');
    }
}
if (!function_exists('productMonitorDelete')) {
    function productMonitorDelete($id)
    {
        productGenericsDelete($id, '`products_monitors`','id');
    }
}
if (!function_exists('productMonitorPartLinkingFromDatabase')) {
    function productMonitorPartLinkingFromDatabase($id)
    {
        $result = productGenericsFromDatabase($id, '`products_monitor_part_linking`','id','monitor_id');
        uasort($result, function($a, $b) {
            return $b['linking_id'] - $a['linking_id'];
        });
        return $result;
    }
}
if (!function_exists('productMonitorFromDatabase')) {
    function productMonitorFromDatabase($id)
    {
        global $DB;
        $monitor=[];
          $query = $DB->query(sprintf(
            "SELECT *, UNIX_TIMESTAMP(`created_at`) AS `created_time`, UNIX_TIMESTAMP(`updated_at`) AS `updated_time` FROM %s  Where `%s` = %d AND `deleted_at` IS NULL %s",
            '`products_monitors`',
            'product_id',
             $id,
            'ORDER BY `rank` ASC'
        ));
        while ($row = $DB->fetch_assoc($query)) {
            if (is_null($primary_key)) {
            $monitor[] = $row;
            } 
        }
        $DB->free_result($query);
        return $monitor; 
    }
}
if (!function_exists('productArtWorkFromDatabase')) {
    function productArtWorkFromDatabase($id)
    {
        global $DB;
        $artwork=[];
          $query = $DB->query(sprintf(
            "SELECT *, UNIX_TIMESTAMP(`created_at`) AS `created_time`, UNIX_TIMESTAMP(`updated_at`) AS `updated_time` FROM %s  Where `%s` = %d AND `deleted_at` IS NULL ",
            '`product_art_work`',
            'product_id',
             $id,
        ));
        while ($row = $DB->fetch_assoc($query)) {
            if (is_null($primary_key)) {
            $artwork[] = $row;
            } 
        }
        $DB->free_result($query);
        return $artwork; 
    }
}
if (!function_exists('subProductFromDatabase')) {
    function subProductFromDatabase($id)
    {
        global $DB;
        $sub_product=[];
          $query = $DB->query(sprintf(
            "SELECT *, UNIX_TIMESTAMP(`created_at`) AS `created_time`, UNIX_TIMESTAMP(`updated_at`) AS `updated_time` FROM %s  Where `%s` = %d AND `deleted_at` IS NULL ORDER BY `default` asc",
            '`product_sub_module`',
            'product_id',
             $id,
        ));
        while ($row = $DB->fetch_assoc($query)) {
            if (is_null($primary_key)) {
            $sub_product[] = $row;
            } 
        }
        $DB->free_result($query);
        return $sub_product; 
    }
}
if (!function_exists('productPartsofToDatabase')) {
    function productPartsofToDatabase($id, &$items)
    {
        return productGenericsToDatabase($id, $items, '`products_partof`');
    }
}

if (!function_exists('productRelationDelete')) {
    function productRelationDelete($id)
    {
        global $DB;
        $item = productRelationFromDatabase($id);
        if (!$item) {
            return 0;
        }
        $result = 0;
        $row = $DB->query_first(sprintf(
            "SELECT `id` FROM `products_related` WHERE `deleted_at` IS NULL AND `related_id` = %d AND `product_id` = %d LIMIT 1",
            $item['product_id'],
            $item['related_id']
        ), MYSQLI_ASSOC);
        if ($row) {
            $result = productGenericDelete($row['id'], '`products_related`');
        }

        return $result + productGenericDelete($id, '`products_related`');
    }
}

if (!function_exists('productRelationFromDatabase')) {
    function productRelationFromDatabase($id)
    {
        return productGenericFromDatabase($id, '`products_related`');
    }
}
if (!function_exists('productRelationToDatabase')) {
    function productRelationToDatabase(&$item)
    {
        global $DB;
        $row = $DB->query_first(sprintf(
            "SELECT `id` FROM `products_related` WHERE `related_id` = %d AND `product_id` = %d LIMIT 1",
            $item['product_id'],
            $item['related_id']
        ), MYSQLI_ASSOC);
        if (!$row) {
            $copy = array(
                'product_id' => $item['related_id'],
                'related_id' => $item['product_id'],
            );
            productGenericToDatabase($copy, '`products_related`');
        }
        return productGenericToDatabase($item, '`products_related`');
    }
}
if (!function_exists('productRelatedDelete')) {
    function productRelatedDelete($id)
    {
        return productGenericsDelete($id, '`products_related`') + productGenericsDelete($id, '`products_related`', 'id', 'related_id');
    }
}
if (!function_exists('productRelatedFromDatabase')) {
    function productRelatedFromDatabase($id)
    {
        $result = productGenericsFromDatabase($id, '`products_related`','id','product_id','ORDER BY `rank` ASC');
       /* uasort($result, function($a, $b) {
            return $b['related_id'] - $a['related_id'];
        });*/
        return $result;
    }
}
if (!function_exists('productRelatedFromPost')) {
    function productRelatedFromPost($posted)
    {
        return productGenericsFromPost($posted, '`products_related`');
    }
}
if (!function_exists('productRecommendedDelete')) {
    function productRecommendedDelete($id)
    {
        global $DB;
        $item = productRelationFromDatabase($id);
        if (!$item) {
            return 0;
        }
        $result = 0;
        $row = $DB->query_first(sprintf(
            "SELECT `id` FROM `products_recommended` WHERE `deleted_at` IS NULL AND `related_id` = %d AND `product_id` = %d LIMIT 1",
            $item['product_id'],
            $item['related_id']
        ), MYSQLI_ASSOC);
        if ($row) {
            $result = productGenericDelete($row['id'], '`products_recommended`');
        }

        return $result + productGenericDelete($id, '`products_recommended`');
    }
}
if (!function_exists('productRecommendedFromDatabase')) {
    function productRecommendedFromDatabase($id)
    {
        return productGenericsFromDatabase($id, '`products_recommended`','id','product_id');
    }
}
if (!function_exists('productBrochureFromDatabase')) {
    function productBrochureFromDatabase($id)
    {
        global $DB;
        $brochur_images=[];
          $query = $DB->query(sprintf(
            "SELECT *, UNIX_TIMESTAMP(`created_at`) AS `created_time`, UNIX_TIMESTAMP(`updated_at`) AS `updated_time` FROM %s  Where `%s` = %d AND `deleted_at` IS NULL %s",
            '`product_brochure_image`',
            'product_id',
             $id,
            'ORDER BY `rank` ASC'
        ));
        while ($row = $DB->fetch_assoc($query)) {
            if (is_null($primary_key)) {
            $brochur_images[] = $row;
            } 
        }
        $DB->free_result($query);
        return $brochur_images; 
    }
}
if (!function_exists('productBrochureFromPost')) {
    function productBrochureFromPost($posted, $table)
    {
         global $DB;
        if (!is_array($posted)) {
            return array();
        }
        $result = array();
        $fields = $DB->table_columns($table);
        foreach ($posted as $k1 => $v1) {
                if (array_key_exists($k1,$fields)) {
                    if($v1 || $v1==0){
                        $result[$k1] = $v1;
                    }    
                }
        }
        return $result;
    }
}
if (!function_exists('productRecommendedToDatabase')) {
    function productRecommendedToDatabase($id,&$items)
    {
        global $DB;
        foreach ($items as $key => $item) {
             productGenericToDatabase($item, '`products_recommended`');
        }
    }
}
if (!function_exists('productRecommendedFromPost')) {
    function productRecommendedFromPost($posted)
    {
        return productGenericsFromPost($posted, '`products_recommended`');
    }
}

if (!function_exists('productColourImages')) {
    function productColourImages($posted)
    {
        return productGenericsFromPost($posted, '`products_images`');
    }
}
if (!function_exists('productColourImagesToDatabase')) {
    function productColourImagesToDatabase($items)
    {
        foreach ($items as &$v) {
            if($v['id']){
                productGenericToDatabase($v,'`products_images`', 'id');
            }
        }
    }
}
if (!function_exists('productRelatedToDatabase')) {
    function productRelatedToDatabase($id, &$items)
    {
        foreach ($items as &$v) {
                $v['product_id'] = $id;
                productRelationToDatabase($v);
        }
    }
}

if (!function_exists('productReservationDelete')) {
    function productReservationDelete($id)
    {
        return productGenericDelete($id, '`products_reserved`');
    }
}
if (!function_exists('productReservationFromDatabase')) {
    function productReservationFromDatabase($id)
    {
        return productGenericFromDatabase($id, '`products_reserved`');
    }
}
if (!function_exists('productReservationToDatabase')) {
    function productReservationToDatabase(&$item)
    {
        return productGenericToDatabase($item, '`products_reserved`');
    }
}
if (!function_exists('productReservedDelete')) {
    function productReservedDelete($id)
    {
        return productGenericsDelete($id, '`products_reserved`');
    }
}
if (!function_exists('productReservedFromDatabase')) {
    function productReservedFromDatabase($id)
    {
        return productGenericsFromDatabase($id, '`products_reserved`');
    }
}
if (!function_exists('supplierTotalCostPrice')) {
    /**
     * Calculate the average cost price of the product
     *
     * @param int $product_id
     *
     * @return string
     */
    function supplierTotalCostPrice($product_id,$upplier_id)
    {
        global $DB;

        if ($row = $DB->query_first(sprintf(
            "SELECT SUM((`unit_cost`)*movement) AS `total_cost` FROM  `products_inventory` WHERE `movement` > 0 AND `stock_type` = 'NEW' AND `product_id` = %d and supplier_id=%d group by product_id",
            $product_id,
            $upplier_id
        ), MYSQLI_ASSOC)) {
            return number_format((float) $row['total_cost'], 2, '.', '');
        }

        return 0.00;
    }
}
if (!function_exists('getSupplierPurchaseHistory')) {
    function getSupplierPurchaseHistory($supplier_id,$products_id)
    {
        global $DB;

        $results = [];
        if ($query = $DB->query(sprintf(
            "SELECT `unit_cost` ,`created_at`,`unit_currency` FROM  `products_inventory` WHERE `movement` > 0 AND `stock_type` = 'NEW' AND `product_id` = %d and supplier_id=%d order by created_at ASC",
            $products_id,
            $supplier_id
        ))) {
            while ($row = $DB->fetch_assoc($query)) {
                $results[] = [date('Y-m-d',strtotime($row['created_at'])),convertCurrency($row['unit_cost'],$row['unit_currency'],'USD')];
            }
            $DB->free_result($query);
        }
        return $results;
    }
}
if (!function_exists('productAverageCostPrice')) {
    /**
     * Calculate the average cost price of the product
     *
     * @param int $product_id
     *
     * @return string
     */
    function productAverageCostPrice($product_id,$supplier_id = 0)
    {
        global $DB;

        $stock = array();
        $supplier_query = '';
        if($supplier_id > 0){
            $supplier_query = " AND `supplier_id` = ".$supplier_id;
        }
        if ($query = $DB->query(sprintf(
            "SELECT `unit_cost` + `unit_freight` AS `cost_price`,`unit_currency`, `movement`, `movement` AS `remain` FROM  `products_inventory` WHERE `movement` > 0 AND `stock_type` = 'NEW' AND `product_id` = %d %s ORDER BY `created_at` ASC",
            $product_id,
            $supplier_query
        ))) {
            while ($row = $DB->fetch_assoc($query)) {
                $stock[] = $row;
            }
            $DB->free_result($query);
        }

        if ($query = $DB->query(sprintf(
            "SELECT `movement` FROM `products_inventory` WHERE `movement` < 0 AND `stock_type` = 'NEW' AND `product_id` = %d %s ORDER BY `created_at` ASC",
            $product_id,
            $supplier_query
        ))) {
            while ($row = $DB->fetch_assoc($query)) {
                $qty = 0 - $row['movement'];
                foreach ($stock as $i => $v) {
                    $stock[$i]['remain'] = max(0, $v['remain'] - $qty);
                    $qty -= $v['remain'] - $stock[$i]['remain'];
                    if (0 == $stock[$i]['remain'] && count($stock) > 1) {
                        unset($stock[$i]);
                    }
                }
            }
            $DB->free_result($query);
        }

        $price = '0.00';
        $sum   = '0.00';
        $count = 0;
        foreach ($stock as $row) {
            if (bccomp($row['cost_price'], '0.00', 2)) {
                $price = convertCurrency($row['cost_price'], $row['unit_currency'], 'USD', false, 4);
                $count += $row['remain'];
                $sum = bcadd($sum, bcmul($price, $row['remain'], 3), 3);
            }
        }

        if ($count) {
            return round(bcdiv($sum, $count, 3), 2);
        }
        return round($price,2);
    }
}
if (!function_exists('productAverageCostPriceSummery')) {
    /**
     * Calculate the average cost price of the product
     *
     * @param int $product_id
     *
     * @return string
     */
    function productAverageCostPriceSummery($product_id)
    {
        global $DB;

        $stock = array();
        if ($query = $DB->query(sprintf(
            "SELECT `unit_cost` + `unit_freight` AS `cost_price`,`unit_currency`, `movement`, `movement` AS `remain` FROM  `products_inventory` WHERE `movement` > 0 AND `stock_type` = 'NEW' AND `product_id` = %d ORDER BY `created_at` ASC",
            $product_id,
        ))) {
            while ($row = $DB->fetch_assoc($query)) {
                $stock[] = $row;
            }
            $DB->free_result($query);
        }
        $reserveData = array_reverse($stock);
        $remainQuantity = 0;
        if ($query = $DB->query(sprintf(
            "SELECT `movement` FROM `products_inventory` WHERE `movement` < 0 AND `stock_type` = 'NEW' AND `product_id` = %d ORDER BY `created_at` ASC",
            $product_id
        ))) {
            while ($row = $DB->fetch_assoc($query)) {
                $qty = 0 - $row['movement'];
                foreach ($stock as $i => $v) {
                    $stock[$i]['remain'] = max(0, $v['remain'] - $qty);
                    $qty -= $v['remain'] - $stock[$i]['remain'];
                    if (0 == $stock[$i]['remain'] && count($stock) > 1) {
                        unset($stock[$i]);
                        $remainQuantity = 0;
                    }else{
                        $remainQuantity =  $stock[$i]['remain'];
                    }
                }
            }
            $DB->free_result($query);
        }
        if($remainQuantity == 0){
            if(isset($reserveData[0]['cost_price']) && $reserveData[0]['cost_price'] > 0){
               return convertCurrency($reserveData[0]['cost_price'], $reserveData[0]['unit_currency'], 'USD', false, 2);
            }else{
                return '0.00';
            }
        }else{
            $price = '0.00';
            $sum   = '0.00';
            $count = 0;
            foreach ($stock as $row) {
                if (bccomp($row['cost_price'], '0.00', 2)) {
                    $price = convertCurrency($row['cost_price'], $row['unit_currency'], 'USD', false, 4);
                    $count += $row['remain'];
                    $sum = bcadd($sum, bcmul($price, $row['remain'], 3), 3);
                }
            }

            if ($count) {
                return round(bcdiv($sum, $count, 3), 2);
            }
            return round($price,2); 
        }
        
    }
}
if (!function_exists('productReservedByColourTotalStock')) {
    /**
     * @param int $id
     * @param int $colour_id
     *
     * @return int
     */
    function productReservedByColourTotalStock($id, $colour_id)
    {
        global $DB;

        if ($row = $DB->query_first(sprintf(
            "SELECT SUM(`qty`) AS `total` FROM `products_reserved` WHERE `product_id` = %d AND `colour_id` = %d AND `deleted_at` IS NULL",
            $id,
            $colour_id
        ), MYSQLI_ASSOC)) {
            return $row['total'];
        }

        return 0;
    }
}
if (!function_exists('productReservedByColourFromDatabase')) {
    /**
     * @param int $id
     * @param int $colour_id
     *
     * @return mixed[][]
     */
    function productReservedByColourFromDatabase($id, $colour_id)
    {
        global $DB;

        $results = [];
        if ($query = $DB->query(sprintf(
            "SELECT * FROM `products_reserved` WHERE `product_id` = %d AND `colour_id` = %d AND `deleted_at` IS NULL",
            $id,
            $colour_id
        ))) {
            while ($row = $DB->fetch_assoc($query)) {
                $results[] = $row;
            }
            $DB->free_result($query);
        }

        return $results;
    }
}
if (!function_exists('productReservedFromPost')) {
    function productReservedFromPost($posted)
    {
        return productGenericsFromPost($posted, '`products_reserved`');
    }
}
if (!function_exists('productReservedToDatabase')) {
    function productReservedToDatabase($id, &$items)
    {
        return productGenericsToDatabase($id, $items, '`products_reserved`');
    }
}

if (!function_exists('productScreenshotDelete')) {
    function productScreenshotDelete($id)
    {
        return productGenericDelete($id, '`product_screenshots`', 'ss_id');
    }
}
if (!function_exists('productScreenshotColorDelete')) {
    function productScreenshotColorDelete($id)
    {
        return productGenericDelete($id, '`products_images`', 'id');
    }
}
if (!function_exists('productScreenshotFromDatabase')) {
    function productScreenshotFromDatabase($id)
    {
        return productScreenshotVirtual(productGenericFromDatabase($id, '`product_screenshots`', 'ss_id'));
    }
}
if (!function_exists('productScreenshotGetValue')) {
    function productScreenshotGetValue($id, $field) {
        $result = productGenericGetValue($id, $field, '`product_screenshots`', 'ss_id');
        if (!is_null($result)) {
            return $result;
        }
        $item = productScreenshotFromDatabase($id);
        if (array_key_exists($field, $item)) {
            return $item[$field];
        }
        return null;
    }
}
if (!function_exists('productScreenshotSetValue')) {
    function productScreenshotSetValue($id, $field, $value) {
        return productGenericSetValue($id, $field, $value, '`product_screenshots`', 'ss_id');
    }
}
if (!function_exists('productScreenshotToDatabase')) {
    function productScreenshotToDatabase(&$item)
    {
        return productGenericToDatabase($item, '`product_screenshots`', 'ss_id');
    }
}
if (!function_exists('productScreenshotVirtual')) {
    function productScreenshotVirtual($screenshot)
    {
        global $_GLOBALS;

        if (is_null($screenshot)) {
            return null;
        }

        $screenshot['filename'] = basename($screenshot['filename']);

        $screenshot_path = productGetOptionList('screenshot_path');
        $test            = $screenshot_path . $screenshot['filename'];
        if (is_file($test)) {

            $screenshot['image_url'] = '/' . $screenshot['filename'];
            $screenshot['thumb_url'] = '/' . $screenshot['filename'];
            //$screenshot['view_url'] = '/' . $screenshot['filename'];

            if (!empty($_GLOBALS["site_settings"]["static_path"] ?? '') && substr($_SERVER["REQUEST_URI"], 0, 10) != "/webadmin/") {
                $test = "/images/screenshots/large/" . $screenshot["ss_id"] . ".jpg";
                if (is_file($_GLOBALS["site_settings"]["static_path"] . $test) && file_exists($_GLOBALS["site_settings"]["static_path"] . $test)) {
                    $screenshot['image_url'] = $_GLOBALS["site_settings"]["static_uri"] . $test;
                    $screenshot['thumb_url'] = $_GLOBALS["site_settings"]["static_uri"] . $test;
                    //$screenshot['view_url'] = $_GLOBALS["site_settings"]["static_uri"] . $test;
                }
                $test = "/images/screenshots/thumb/" . $screenshot["ss_id"] . ".jpg";
                if (is_file($_GLOBALS["site_settings"]["static_path"] . $test) && file_exists($_GLOBALS["site_settings"]["static_path"] . $test)) {
                    $screenshot['thumb_url'] = $_GLOBALS["site_settings"]["static_uri"] . $test;
                }
            }
        }

        return $screenshot;
    }
}
if (!function_exists('productScreenshotsCopy')) {
    function productScreenshotsCopy($id, $new_id)
    {
        foreach (productScreenshotsFromDatabase($id) as $v) {
            unset($v['ss_id']);
            $v['id'] = $new_id;
            productScreenshotToDatabase($v);
        }
    }
}
if (!function_exists('productScreenshotsDelete')) {
    function productScreenshotsDelete($id)
    {
        return productGenericsDelete($id, '`product_screenshots`', 'id');
    }
}
if (!function_exists('productColorImagesFromDatabase')) {
    function productColorImagesFromDatabase($product_id)
    {
        global $DB, $_GLOBALS;
        $result = array();;
        $query  = $DB->query(sprintf(
            "SELECT `products_images`.*,`product_colour`.colour FROM `products_images`,`product_colour` WHERE `product_id` = %d and `products_images`.`deleted_at` IS NULL AND  `products_images`.color_id=`product_colour`.id ORDER BY `rank` ASC",
            $product_id
        ));
        while ($row = $DB->fetch_assoc($query)) {
            $displayimage=  basename($row['image']);
            $row['image'] = basename($row['image']);
            //////echo productGetOptionList('screenshot_path'). $row['image']; die;
            if ('' != $row['image'] && is_file(productGetOptionList('screenshot_path'). $row['image'])) {
                $row['image_url'] =  $_GLOBALS["site_settings"]["home_uri"] . $displayimage;
                $row['thumb_url'] = $_GLOBALS["site_settings"]["home_uri"] . $displayimage;
                $result[$row['color_id']]['colour_name']=$row['colour'];
                $result[$row['color_id']]['value'][$row['id']] = $row;
            }


        }
        return $result;
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
if (!function_exists('UpdateProductColourImagesRankStatus')) {
    function UpdateProductColourImagesRankStatus($data)
    {
        global $DB;
        $DB->query(sprintf(
                "UPDATE `products_images` SET `rank` = %d,`active`='%s' WHERE `id` = %d",
                    $data['rank'],
                    $data['active'],
                    $data['ss_id']
                ));
    }
}
if (!function_exists('productScreenshotsFromDatabase')) {
    function productScreenshotsFromDatabase($id)
    {
        $result = array();
        foreach (productGenericsFromDatabase($id, '`product_screenshots`', 'ss_id', 'id','ORDER BY `rank` ASC') as $k => $v) {
            $result[$k] = productScreenshotVirtual($v);
        }
        return $result;
    }
}
if (!function_exists('productScreenshotsFromPost')) {
    function productScreenshotsFromPost($posted,$files=[],$product_id=0)
    {
        foreach ($posted as $key => $value) {
            if(array_key_exists($key, $files['name'] ?? [])){
                $file_orig = $files['name'][$key]['image'];
                $file_temp = $files['tmp_name'][$key]['image'];
                $error     = $files['error'][$key]['image'];
                $file_ext = substr($file_orig, strrpos($file_orig, '.'));
                if (substr($file_ext, 0, 1) == '.') {
                    $file_ext = substr($file_ext, 1);
                }
                $file_new  = "ss_" . $product_id . '_' .rand(1,10000) . '.' . $file_ext;
                $dir       = productGetOptionList('screenshot_path');
                if (move_uploaded_file($file_temp, $dir . $file_new)) {
                    $posted[$key]['filename']=$file_new;
                    $posted[$key]['file_orig']=$file_orig;
                }
            }
        }
        return productGenericsFromPost($posted, '`product_screenshots`');
    }
}
if (!function_exists('productScreenshotsToDatabase')) {
    function productScreenshotsToDatabase($id, &$items)
    {
        return productGenericsToDatabase($id, $items, '`product_screenshots`', 'ss_id', 'id');
    }
}

if (!function_exists('productSupplierDelete')) {
    function productSupplierDelete($id)
    {
        return productGenericDelete($id, '`supplier_product`');
    }
}
if (!function_exists('productSupplierFromDatabase')) {
    function productSupplierFromDatabase($id)
    {
        return productGenericFromDatabase($id, '`supplier_product`');
    }
}
if (!function_exists('productSupplierToDatabase')) {
    function productSupplierToDatabase(&$item)
    {
        return productGenericToDatabase($item, '`supplier_product`');
    }
}
if (!function_exists('productSuppliersDelete')) {
    function productSuppliersDelete($id)
    {
        return productGenericsDelete($id, '`supplier_product`');
    }
}
if (!function_exists('productSuppliersFromDatabase')) {
    function productSuppliersFromDatabase($id)
    {
        $result = productGenericsFromDatabase($id, '`supplier_product`');
        uasort($result, function($a, $b) {
            return strcmp($b['supplier_name'], $a['supplier_name']);
        });
        return $result;
    }
}
if (!function_exists('defaultProductSuppliersFromDatabase')) {
    function defaultProductSuppliersFromDatabase($id)
    {
        global $DB;
        $row = $DB->query_first(sprintf(
            "SELECT * FROM `supplier_product` WHERE  `product_id` = %d  AND `main` = 'y' LIMIT 1",
            $id
        ), MYSQLI_ASSOC);
        return $row;
    }
}
if (!function_exists('productSuppliersFromPost')) {
    function productSuppliersFromPost($posted,$callfrom='supplier')
    {
        // call from is use to set default value that has been some from product
        if($callfrom=='product'){
             return productSuppliersGenericsFromPost($posted, '`supplier_product`');
        }else{
            return productGenericsFromPost($posted, '`supplier_product`');
        }
        
    }
}
if (!function_exists('productSuppliersHistoryFromPost')) {
    function productSuppliersHistoryFromPost($posted)
    {
        return productGenericsFromPost($posted, '`supplier_product_history`');
    }
}
if (!function_exists('productSuppliersToDatabase')) {
    function productSuppliersToDatabase($id, &$items)
    {
        return productGenericsToDatabase($id, $items, '`supplier_product`');
    }
}
if (!function_exists('productSuppliersHistoryToDatabase')) {
    function productSuppliersHistoryToDatabase($id,$product,$items)
    {
        foreach($items as $v) {
            if($product==0){
                if($items['product_id']){
                    $product=productFromDatabase($v['product_id']);
                }
            }else{
                $v['sales_price']=$product['consignment_sales_price'];
            }
            if($v['id']){
                $oldval=productSupplierFromDatabase($v['id']);
                if($oldval['cprice_usd']!=$v['cprice_usd']){
                    $v['id']='';
                    $v['movement']=((($v['cprice_usd']-$oldval['cprice_usd'])*100)/$oldval['cprice_usd']);
                    productGenericToDatabase($v, '`supplier_product_history`','id');
                    $comment = array(
                        'product_id' => $v['product_id'],
                        'comments' => "Supplier cost price changes: \n" ."cost Price changed from ".$oldval['cprice_usd']." to ".$v['cprice_usd']."",
                        'type' => 'change',
                    );
                    productCommentToDatabase($comment);
                }
            }else{
                
                $v['movement']=0;
                productGenericToDatabase($v, '`supplier_product_history`','id');
            }
        }
    }
    /////die('sdfsvchffffffffffffg');
}
if (!function_exists('getsupplieritemhistory')) {
    function getsupplieritemhistory($supplier_id,$products_id)
    {
        global $DB;

        $results = [];
        if ($query = $DB->query(sprintf(
            "SELECT * FROM `supplier_product_history` WHERE `product_id` = %d AND `supplier_id` = %d ",
            $products_id,
            $supplier_id
        ))) {
            while ($row = $DB->fetch_assoc($query)) {
                $results[] = [date('Y-m-d',strtotime($row['created_at'])),(float)$row['cprice_usd']];
            }
            $DB->free_result($query);
        }
        return $results;
    }
}
if (!function_exists('getsupplieritemfullhistory')) {
    function getsupplieritemfullhistory($supplier_id,$products_id,$limit='limit 5')
    {
        global $DB;
        $results = [];
        if ($query = $DB->query(sprintf(
            "SELECT * FROM `supplier_product_history` WHERE `product_id` = %d AND `supplier_id` = %d ORDER BY created_at DESC %s ",
            $products_id,
            $supplier_id,
            $limit
        ))) {
            while ($row = $DB->fetch_assoc($query)) {
                $results[] = $row;
            }
            $DB->free_result($query);
        }
        return $results;
    }
}
if (!function_exists('productTagDelete')) {
    function productTagDelete($id)
    {
        return productGenericDelete($id, '`products_tags`');
    }
}
if (!function_exists('productTagFromDatabase')) {
    function productTagFromDatabase($id)
    {
        return productGenericFromDatabase($id, '`products_tags`');
    }
}
if (!function_exists('productTagToDatabase')) {
    function productTagToDatabase(&$item)
    {
        return productGenericToDatabase($item, '`products_tags`');
    }
}
if (!function_exists('productTagsDelete')) {
    function productTagsDelete($id)
    {
        return productGenericsDelete($id, '`products_tags`');
    }
}
if (!function_exists('productTagsFromDatabase')) {
    function productTagsFromDatabase($id)
    {
        $result = productGenericsFromDatabase($id, '`products_tags`');
        uasort($result, function($a, $b) {
            return $b['product_qty'] - $a['product_qty'];
        });
        return $result;
    }
}
if (!function_exists('productTagsFromPost')) {
    function productTagsFromPost($posted)
    {
        return productGenericsFromPost($posted, '`products_tags`');
    }
}
if (!function_exists('productTagsToDatabase')) {
    function productTagsToDatabase($id, &$items)
    {
        return productGenericsToDatabase($id, $items, '`products_tags`');
    }
}


if (!function_exists('productToDatabase')) {
    function productToDatabase(&$product)
    {
        global $DB;
        $id         = intval($product['id']);
        $fields     = productGetOptionList('base_fields_types');
        $changed    = array();
        if (productExists($id)) {
            $old_vals = productFromDatabase($id);
            $set_vals = array();
            foreach ($product as $k => $v) if ($v !== $old_vals[$k]) {
                if (array_key_exists($k, $fields)) {
                    if (is_array($product[$k])) {
                        $set_vals[$k] = "`" . $k . "` = " . $DB->escape(implode(",", $product[$k]));
                    } elseif (isset($product[$k])) {
                        $set_vals[$k] = "`" . $k . "` = " . $DB->escape($product[$k]);
                    } else {
                        $set_vals[$k] = "`" . $k . "` = NULL";
                    }
                }
            }
            if (count($set_vals)) {
                $DB->query(sprintf(
                    "UPDATE `products` SET %s WHERE `id` = %d LIMIT 1",
                    implode(', ', $set_vals),
                    $id
                ));
            }
        } else {
            $insert_vals = array();
            foreach ($product as $k => $value) {
                if (array_key_exists($k, $fields)) {
                    if (is_array($product[$k]) && substr($fields[$k][1], 0, 4) == 'set(') {
                        $insert_vals[$k] = $DB->escape(implode(",", $product[$k]));
                    } elseif (isset($product[$k])) {
                        $insert_vals[$k] = $DB->escape($value);
                    } elseif ('YES' == $fields[$k][2]) {
                        $insert_vals[$k] = 'NULL';
                    }

                }
            }

            if (count($insert_vals)) {
                $DB->query(sprintf(
                    "INSERT INTO `products` (`%s`) VALUES (%s)",
                    implode('`, `', array_keys($insert_vals)),
                    implode(", ", $insert_vals)
                ));
                $product['id'] = $id = $DB->insert_id();

                if (999 === ($id % 1000)) {
                    $next_id     = $id;
                    foreach ([
                        "`convert_convert`",
                        "`fchcom_fchcom`",
                        "`hwmedia_hwmedia`",
                        "`hg_products`",
                        "`spcom_spcom`",
                    ] as $schema) {
                        if ($row = $DB->query_first(sprintf(
                            "SHOW TABLE STATUS FROM %s LIKE 'products'",
                            $schema
                        ), MYSQLI_ASSOC)) {
                            if ($row['Auto_increment'] >= $next_id) {
                                $auto_increment = intdiv($row['Auto_increment'], 1000) * 1000 + 1000;
                            }
                        }
                    }
                    if ($auto_increment > $id) {
                        $DB->query(sprintf(
                            "ALTER TABLE %s AUTO_INCREMENT = %d",
                            "`products`",
                            $auto_increment
                        ));
                    }
                }
            }
        }

        return $id;
    }
}
if (!function_exists('AddNewInventoryForproduct')) {
    function AddNewInventoryForproduct($link_id, $posted)
    {
        global $DB;
        $product_id = $link_id;
        $stock_sold = 'new';
        if('' != $posted['inventory']['movement']) {
            $movement = intval($posted['inventory']['movement']);
            $extra              = "";
            $before_product_qty = '';
            if ($movement < 0) {
                $extra = ", ";
                $DB->query("UPDATE `arcooda_www`.`products` SET total_sales=total_sales+" . abs($movement) . ", last_sold=NOW() WHERE id='$link_id'");
                // now have to reduce the stock from the inventory in FIFO method.
                productReduceStock($link_id, abs($movement));
                // now have to check if the stock level is less than alert level.
                $row = $DB->query_first("SELECT stock,alert_level FROM `arcooda_www`.`products` WHERE id=$link_id", MYSQLI_ASSOC);
                if ($row['stock'] <= $row['alert_level'] && $row['alert_level'] > 0) {
                    $vars = ['[PRODUCT_ID]' => $link_id, '[STOCK]' => $row['stock']];
                    if (function_exists('notifyActionMade')) {
                        notifyActionMade(NOTIFY_PRODUCT_INVENTORY_LOW, $_GLOBALS['site_settings']['site_id'], $vars);
                    }
                }
            } else {
                // if stock added then need to update the stock quantity.
                $before_product_qty = productGetValue($product_id, 'qty');
                $DB->query("UPDATE `arcooda_www`.`products` SET qty=qty+$movement WHERE id='$link_id'");
            }

            $cost    = ('' != $posted['inventory']['unit_value_usd']) ? $posted['inventory']['unit_value_usd'] : (('' != $posted['inventory']['unit_value_aud']) ? convertCurrency($posted['inventory']['unit_value_aud'], 'AUD' ,'USD') : '0.00');
            $freight = ('' != $posted['inventory']['unit_freight_usd']) ? $posted['inventory']['unit_freight_usd'] : (('' != $posted['inventory']['unit_freight_aud']) ? convertCurrency($posted['inventory']['unit_freight_aud'], 'AUD' ,'USD') : '0.00');
            $cost_a  = ('' != $posted['inventory']['unit_value_aud']) ? $posted['inventory']['unit_value_aud'] : (('' != $posted['inventory']['unit_value_usd']) ? convertCurrency($posted['inventory']['unit_value_usd'], 'USD' ,'AUD') : '0.00');
            $fght_a  = ('' != $posted['inventory']['unit_freight_aud']) ? $posted['inventory']['unit_freight_aud'] : (('' != $posted['inventory']['unit_freight_usd']) ? convertCurrency($posted['inventory']['unit_freight_usd'], 'USD' ,'AUD') : '0.00');
            if ($movement < 0) {
                // need to have cost as the selling prices.
                $cost    = $posted['pricing']['sales_price'];
                $cost_a  = $posted['pricing']['sales_price_au'];
                $freight = $fght_a = '0.00';
            }
            $qty       = $DB->query_first("SELECT qty FROM `arcooda_www`.`products` WHERE id='$link_id'");
            $available = $qty['qty'] - productReservedStock($link_id);
            $comments  = $posted['inventory']['comments'];
            $usd_cost  = $cost + $freight;
            $aud_cost  = $cost_a + $fght_a;
            if ($movement < 0) {
                $comments .= '(Sold for $' . $usd_cost . ' USD, $' . $aud_cost . ' AUD)';
            } else {
                $comments .= ' (Cost: $' . $cost . ' USD + Freight: $' . $freight . ' USD , Cost: $' . $cost_a . ' AUD + Freight: $' . $fght_a . ' AUD)';
            }

            if ($movement < 0) {
                $usd_cost = $aud_cost = $freight = $fght_a = 0;
            }
            $comments2 = $comments;
            $comments  = $DB->escape($comments);
            $n_move    = (($movement < 0) ? '- ' : '+ ') . abs($movement) . ' ' . strtoupper($stock_sold);
            $inv_id = productInventoryToDatabase($link_id, [[
                'movement'        => $movement,
                'comments'        => $comments,
                'available'       => $available,
                'unit_cost'       => $cost,
                'unit_freight'    => $freight,
                'unit_cost_au'    => $cost_a,
                'unit_freight_au' => $fght_a,
                'stock_type'      => $stock_sold,
                'stock_left'      => ($movement > 0) ? $movement : 0,
            ]]);

            if ($before_product_qty < 0 && $inv_id > 0) {
                $new_stock_left = abs($movement) + $before_product_qty;
                if ($new_stock_left < 0) {
                    $new_stock_left = 0;
                }

                $DB->query("UPDATE `arcooda_www`.`products_inventory` SET stock_left=" . intval($new_stock_left) . " WHERE id=" . intval($inv_id));
            }
            $changed[] = 'Inventory modified: Movement: ' . $movement . ' (' . strtoupper($stock_sold) . '), ' . $comments2;
        }
    }
}
if (!function_exists('productChunk')) {
    /**
     * Return the products grouped by field
     *
     * @param string       $criteria The product search query
     * @param array|string $group_by The fields to group by
     */
    function productChunk($criteria, $group_by = array())
    {
        global $DB;

        //TODO: Deprecated calling convention
        if (is_array($criteria)) {
            $criteria = productQuery(array('id' => $criteria));
        }

        if (!is_array($group_by)) {
            $group_by = array($group_by);
        }

        $field_list = array(
            'id' => '`p`.`id`',
        );
        foreach (productGetOptionList('all_fields') as $field => $expr) {
            if (in_array($field, $group_by)) {
                $field_list[] = sprintf("%s AS `%s`", $expr, $field);
            }
        }

        $results = array();
        $query   = $DB->query(sprintf(
            "SELECT %s FROM `products` AS `p` WHERE %s GROUP BY `p`.`id`",
            implode(', ', $field_list),
            $criteria
        ));
        while ($row = $DB->fetch_assoc($query)) {
            $pointers = array(&$results);
            foreach ($group_by as $group) {
                if (!array_key_exists($group, $row)) {
                    continue;
                }
                $k          = $row[$group];
                $new_pointers = array();
                foreach (explode(',', $k) as $k2) {
                    foreach ($pointers as $k1 => $pointer) {
                        if (!array_key_exists($k2, $pointers[$k1])) {
                            $pointers[$k1][$k2] = array();
                        }
                        $new_pointers[] = &$pointers[$k1][$k2];
                    }
                }
                $pointers = $new_pointers;
            }
            foreach ($pointers as $pointer_key => $pointer) {
                $pointers[$pointer_key][] = intval($row['id']); //TODO: This seems to be due to non-standard int in database
            }
        }
        $DB->free_result($query);
        return $results;
    }
}
if (!function_exists('productDelete')) {
    function productDelete($id)
    {
        $result = 0;
        $result += productBulkPricingDelete($id);
        $result += productBulkWeightsDelete($id);
        $result += productCategoriesDelete($id);
        $result += productCommentsDelete($id);
        $result += productDimensionsDelete($id);
        $result += productInventoryDelete($id);
        $result += productSafetyDelete($id);
        $result += productMoviesDelete($id);
        $result += productPackingsDelete($id);
        $result += productPartsofDelete($id);
        $result += productRelatedDelete($id);
        $result += productReservedDelete($id);
        $result += productScreenshotsDelete($id);
        $result += productSuppliersDelete($id);
        $result += productTagsDelete($id);

        $result += productGenericDelete($id, '`products`');

        return $result;
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
                urlencode(preg_replace('/\s+/', '-', $row['slug'])),
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
        $row['image_url_front']='';
         if ('' != $row['image'] && is_file(productGetOptionList('image_path') . $row['image'])) {
            $row['image_url_front'] = $row['image_180_url'] = sprintf(
                '/p/%s-%d.jpg??width=250&height=250&crop=true',
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
        // $row['qty'] = ($row['stock_type'] == 'consignment') ? $row['consignment_qty'] : $row['qty']; 
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
function getNextSerial(){
            global $DB;
            if ($row = $DB->query_first(sprintf(
                "SELECT MAX(`serial_id`) AS `max_serial` FROM %s.`%s` LIMIT 1",
                '`highwaygroup`',
                'serial_numbers',
            ), MYSQLI_ASSOC)) {
                return max(8000, $row['max_serial'] + 1);
            }
}

if (!function_exists('productFromPost')) {
    function productFromPost($posted)
    {
        global $_GLOBALS,$DB;
        $id                        = intval($posted['id']);
        $result                    = productFromDatabase($id);
        if (!is_array($result)) {
            $result = $DB->table_defaults("`products`");
        }

        $before                    = $result;

        //TODO: Use hidden inputs for toggling
        $result['special_flag']    = 'n';
        $result['allow_invoice']   = 'n';
        $result['additional_fees'] = 'n';
        $result['discontinued']    = 'n';
        $result['full_payment']    = 'n';
        $result['is_price_flux']    = 0;
        $result['approvals']    = [];
        foreach (array(
            'featured_expiry',
            'special_expiry',
        ) as $k) {
            if ($result[$k] == '') {
                $result[$k] = null;
            }
        }

        foreach ($DB->table_columns('`products`') as $k => $v) {
            if (array_key_exists($k, $posted)) {
                if (is_array($posted[$k])) {
                    $result[$k] = trim(implode(',', $posted[$k]), ',');
                } else {
                    $result[$k] = $posted[$k];
                }
            }
        }

        foreach (array(
            'factory_meta_title',
            'factory_meta_keywords',
            'factory_meta_description',
            'factory_slug',
        ) as $k) {
            if ($result[$k] !== $before[$k]) {
                $result['factory_meta_by'] = $_GLOBALS['staff']['id'];
                $result['factory_meta_at'] = formatDateTime();
                break;
            }
        }

        foreach (array(
            'meta_title',
            'meta_keywords',
            'meta_description',
            'slug',
        ) as $k) {
            if ($result[$k] !== $before[$k]) {
                $result['meta_by'] = $_GLOBALS['staff']['id'];
                $result['meta_at'] = formatDateTime();
            }
        }

        if ('n' == $result['special_flag']) {
            $result['special_expiry'] = null;
            $result['on_special'] = 'n';
        }
        if ('n' == $posted['discontinued']) {
            $result['discontinued_id']   = 0;
            $result['discontinued_name'] = '';
        }
        if (!isset($posted['is_harmonised_code']) || 'y' != $posted['is_harmonised_code']) {
            $result['is_harmonised_code'] = 'n';
        }
        if (!isset($posted['is_origin_country']) || 'y' != $posted['is_origin_country']) {
            $result['is_origin_country'] = 'n';
        }
        if (!isset($posted['is_product_usage']) || 'y' != $posted['is_product_usage']) {
            $result['is_product_usage'] = 'n';
        }
        if (!isset($posted['is_product_materials']) || 'y' != $posted['is_product_materials']) {
            $result['is_product_materials'] = 'n';
        }
        return productVirtual($result);
    }
}
if (!function_exists('productGetLock')) {
    function productGetLock($id)
    {
        global $DB;

        if ($row = $DB->query_first(sprintf(
            "SELECT * FROM `generic_locks` WHERE `section_name` = 'product' AND `generic_id` = %d AND `expiry_date` > NOW() LIMIT 1",
            $id
        ), MYSQLI_ASSOC)) {
            $row['expiry_time'] = strtotime($row['expiry_date']);
            return $row;
        }

        return null;
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

            //'icons' => 'icons',

            //'image_path' => 'image_path',

            //'thumb_path'             => 'thumb_path',

            //'screenshot_path'        => 'screenshot_path',

            //'location' => 'location',
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
            'product_material'         => 'product_material',
            'product_file_type'         => 'product_file_type',
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
            //case 'special':
            case 'active':
            case 'additional_fees':
            case 'ce':
            case 'ctick':
            case 'featured':
            case 'manufacturer_online':
            case 'on_special':
            case 'price_flux':
            case 'price_guarantee':
            case 'price_online':
            case 'rohs':
            case 'superseded':
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
            case 'product_series':
                $cache[$key] = array();
                $query       = $DB->query(sprintf(
                    "SELECT `id`, `name` FROM `product_series`  ORDER BY `rank` ASC"
                ));
                while ($row = $DB->fetch_assoc($query)) {
                    $cache[$key][$row['id']] = $row['name'];
                }
                $DB->free_result($query);
                break;
             case 'product_material':
                $cache[$key] = array();
                $query       = $DB->query(sprintf(
                    "SELECT `id`, `material` FROM `product_material` WHERE `deleted_at` IS NULL ORDER BY `rank` ASC"
                ));
                while ($row = $DB->fetch_assoc($query)) {
                    $cache[$key][$row['id']] = $row['material'];
                }
                $DB->free_result($query);
                break;
            case 'product_file_type':
                $cache[$key] = array();
                $query       = $DB->query(sprintf(
                    "SELECT `id`, `file_type` FROM `product_art_file_type` WHERE `deleted_at` IS NULL ORDER BY `rank` ASC"
                ));
                while ($row = $DB->fetch_assoc($query)) {
                    $cache[$key][$row['id']] = $row['file_type'];
                }
                $DB->free_result($query);
                break;
             case 'cabinet_button_brand':
                $cache[$key] = array();
                $query       = $DB->query(sprintf(
                    "SELECT `id`, `data_value` FROM `product_cabinet_button_brand` WHERE `deleted_at` IS NULL ORDER BY `rank` ASC"
                ));
                while ($row = $DB->fetch_assoc($query)) {
                    $cache[$key][$row['id']] = $row['data_value'];
                }
                $DB->free_result($query);
                break;
             case 'cabinet_joystick_brand':
                $cache[$key] = array();
                $query       = $DB->query(sprintf(
                    "SELECT `id`, `data_value` FROM `product_cabinet_joystick_brand` WHERE `deleted_at` IS NULL ORDER BY `rank` ASC"
                ));
                while ($row = $DB->fetch_assoc($query)) {
                    $cache[$key][$row['id']] = $row['data_value'];
                }
                $DB->free_result($query);
                break;
            case 'cabinet_lighting':
                $cache[$key] = array();
                $query       = $DB->query(sprintf(
                    "SELECT `id`, `data_value` FROM `product_cabinet_lighting` WHERE `deleted_at` IS NULL ORDER BY `rank` ASC"
                ));
                while ($row = $DB->fetch_assoc($query)) {
                    $cache[$key][$row['id']] = $row['data_value'];
                }
                $DB->free_result($query);
                break;
            case 'cabinet_panel_material':
                $cache[$key] = array();
                $query       = $DB->query(sprintf(
                    "SELECT `id`, `data_value` FROM `product_cabinet_panel_material` WHERE `deleted_at` IS NULL ORDER BY `rank` ASC"
                ));
                while ($row = $DB->fetch_assoc($query)) {
                    $cache[$key][$row['id']] = $row['data_value'];
                }
                $DB->free_result($query);
                break;
            case 'cabinet_material':
                $cache[$key] = array();
                $query       = $DB->query(sprintf(
                    "SELECT `id`, `data_value` FROM `product_cabinet_material` WHERE `deleted_at` IS NULL ORDER BY `rank` ASC"
                ));
                while ($row = $DB->fetch_assoc($query)) {
                    $cache[$key][$row['id']] = $row['data_value'];
                }
                $DB->free_result($query);
                break;
            case 'monitor_part_number':
                $cache[$key] = array();
                $query       = $DB->query(sprintf(
                    "SELECT `id`, `data_value` FROM `product_monitor_part_number` WHERE `deleted_at` IS NULL ORDER BY `rank` ASC"
                ));
                while ($row = $DB->fetch_assoc($query)) {
                    $cache[$key][$row['id']] = $row['data_value'];
                }
                $DB->free_result($query);
                break;
            case 'monitor_other_options':
                $cache[$key] = array();
                $query       = $DB->query(sprintf(
                    "SELECT `id`, `data_value` FROM `product_monitor_other_options` WHERE `deleted_at` IS NULL ORDER BY `rank` ASC"
                ));
                while ($row = $DB->fetch_assoc($query)) {
                    $cache[$key][$row['id']] = $row['data_value'];
                }
                $DB->free_result($query);
                break;
             case 'monitor_touchscreen':
                $cache[$key] = array();
                $query       = $DB->query(sprintf(
                    "SELECT `id`, `data_value` FROM `product_monitor_touchscreen` WHERE `deleted_at` IS NULL ORDER BY `rank` ASC"
                ));
                while ($row = $DB->fetch_assoc($query)) {
                    $cache[$key][$row['id']] = $row['data_value'];
                }
                $DB->free_result($query);
                break;
             case 'monitor_specifications':
                $cache[$key] = array();
                $query       = $DB->query(sprintf(
                    "SELECT `id`, `data_value` FROM `product_monitor_specifications` WHERE `deleted_at` IS NULL ORDER BY `rank` ASC"
                ));
                while ($row = $DB->fetch_assoc($query)) {
                    $cache[$key][$row['id']] = $row['data_value'];
                }
                $DB->free_result($query);
                break;
             case 'monitor_size':
                $cache[$key] = array();
                $query       = $DB->query(sprintf(
                    "SELECT `id`, `data_value` FROM `product_monitor_size` WHERE `deleted_at` IS NULL ORDER BY `rank` ASC"
                ));
                while ($row = $DB->fetch_assoc($query)) {
                    $cache[$key][$row['id']] = $row['data_value'];
                }
                $DB->free_result($query);
                break;

            case '360_view':
                $cache[$key] = array();
                if ($query = $DB->query(sprintf(
                    "SELECT `%s`, `name` FROM %s WHERE `status` = 1 ORDER BY `name` ASC",
                    "id",
                    "`360_view`"
                ))) {
                    while ($row = $DB->fetch_assoc($query)) {
                        $cache[$key][$row["id"]] = $row['name'];
                    }
                    $DB->free_result($query);
                }
                break;

            case 'product_builder' :
                $cache[$key] = array();
                $query       = $DB->query(sprintf(
                    "SELECT bul.`id`, bul.`name` FROM `pb_builders` bul ,`pb_machines` pm WHERE bul.`status` =1 AND bul.`id`=pm.`builder_id` AND pm.`product_id`= ORDER BY bul.`name` ASC"
                ));
                while ($row = $DB->fetch_assoc($query)) {
                    $cache[$key][$row['id']] = $row['name'];
                }
                $DB->free_result($query);
                break;
            case 'product_carousel' :
               $cache[$key] = array();
                $query       = $DB->query(sprintf(
                    "SELECT `id`, `name` FROM `product_page_carousel` WHERE `status` ='y' ORDER BY `name` ASC"
                ));
                while ($row = $DB->fetch_assoc($query)) {
                    $cache[$key][$row['id']] = $row['name'];
                }
                $DB->free_result($query);
               break;
            case 'product_slider' :
                $cache[$key] = array();
                $query       = $DB->query(sprintf(
                    "SELECT `id`, `name` FROM `product_page_slider` WHERE `status` ='y' ORDER BY `name` ASC"
                ));
                while ($row = $DB->fetch_assoc($query)) {
                    $cache[$key][$row['id']] = $row['name'];
                }
                $DB->free_result($query);
                break;
            case 'product_supported' :
                $cache[$key] = array();
                $query       = $DB->query(sprintf(
                    "SELECT `id`, `name` FROM `product_supported` WHERE `active` ='y' ORDER BY `name` ASC"
                ));
                while ($row = $DB->fetch_assoc($query)) {
                    $cache[$key][$row['id']] = $row['name'];
                }
                $DB->free_result($query);
                break;
            //return $cache[$key] = getAvailability();

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
                    "SELECT `id`, `parent`, `name` FROM `product_types` WHERE `deleted_at` IS NULL ORDER BY `name` ASC"
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
                /*
                $cache[$key] = array();
                $query = $DB->query(sprintf(
                "SELECT `id`, `first_name`, `last_name` FROM `highwaygroup`.`users` ORDER BY `first_name` ASC, `last_name` ASC"
                ));
                while ($row = $DB->fetch_assoc($query)) {
                $cache[$key][$row['id']] = trim($row['first_name'] . ' ' . $row['last_name']);
                }
                $DB->free_result($query);
                break;
                 */
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
                /*
                $cache[$key] = array();
                $query = $DB->query(sprintf(
                "SELECT `id`, `condition` FROM `product_conditions` WHERE `deleted_at` IS NULL"
                ));
                while ($row = $DB->fetch_assoc($query)) {
                $cache[$key][$row['id']] = $row['condition'];
                }
                $DB->free_result($query);
                break;
                 */
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
                /*
                $cache[$key] = array();
                $query = $DB->query(sprintf(
                "SELECT `code`, `name` FROM `highwaygroup`.`currency`"
                ));
                while ($row = $DB->fetch_assoc($query)) {
                $cache[$key][$row['code']] = $row['name'];
                }
                $DB->free_result($query);
                break;
                 */
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

                    //"last_sold_time"         => "UNIX_TIMESTAMP(`p`.`last_sold`)",
                    //"promotion_time_expiry"  => "UNIX_TIMESTAMP(`p`.`promotion_expiry`)",

                    //'product_str'           => "CONCAT(`p`.`id`, ' 0')",

                    //"special_price_entered"  => "IF(`p`.`special_priceus` > 0.0, 'y', 'n')",
                    //"year_released"          => "YEAR(`p`.`date_available`)",

                    "added_today"        => "IF(" . $today . " <= `p`.`created_at`, 'y', 'n')",
                    "precision"          => "2",

                    "featured"           => "IF(`p`.`feature` = 'y' AND (`p`.`featured_expiry` ='0000-00-00 00:00:00' OR `p`.`featured_expiry` >= " . $today . "), 'y', 'n')",
                    "num_days_old"       => "(TO_DAYS(" . $today . ") - TO_DAYS(`p`.`created_at`))",
                    //"on_special"             => "IF(`p`.`special_flag` = 'y' AND `p`.`price_online` = 'y' AND `p`.`special_priceus` > 0.0 AND (`p`.`special_expiry` IS NULL OR `p`.`special_expiry` > " . $today . "), 'y', 'n')",
                    //"promotion"              => "IF(`p`.`promotion_type` != '' AND `p`.`promotion_type` != 'none' AND (`p`.`promotion_expiry` >= " . $today . " OR `p`.`promotion_expiry` = '0000-00-00 00:00:00'), 'y', 'n')",
                    //"promotion_time_expired" => "IF(`p`.`promotion_expiry` > " . $today . " OR `p`.`promotion_expiry` = '0000-00-00 00:00:00', 'n', 'y')",
                    //"special_flag_expired"   => "IF(`p`.`special_expiry` IS NULL OR `p`.`special_expiry` > " . $today . ", 'n', 'y')",

                    //"secondary_currency" => "IF(`p`.`price_flux` = 'y', `p`.`cost_currency`, `p`.`sales_currency`)",
                    "secondary_currency" => "`p`.`_secondary_currency`",
                    //"secondary_price"    => "IF(`p`.`price_flux` = 'y', `p`.`cost_price` * (`p`.`markup` + 100.0) / 100.0, `p`.`sales_price`)",
                    //"secondary_price"    => "`p`.`_secondary_price`",
                    "secondary_price"    => "IF(`p`.`special_flag` = 'y' AND `p`.`special_priceus` > 0.0 AND (`p`.`special_expiry` >= " . $today . " OR `p`.`special_expiry` = '0000-00-00 00:00:00'), `p`.`special_priceus`, IF(`p`.`price_flux` = 'y', `p`.`cost_price` * (`p`.`markup` + 100.0) / 100.0, `p`.`sales_price`))",
                    //"precision"              => "`p`.`precision`",
                    //"primary_currency"   => "IF(`p`.`price_flux` = 'y', IF(`p`.`old_price` <> 0.0, `p`.`sales_currency`, `p`.`cost_currency`), `p`.`sales_currency`)",
                    "primary_currency"   => "`p`.`_primary_currency`",
                    //"primary_price"      => "IF(`p`.`price_flux` = 'y', IF(`p`.`old_price` <> 0.0, `p`.`old_price`, `p`.`cost_price` * (`p`.`markup` + 100.0) / 100.0), IF(`p`.`old_price` > `p`.`sales_price`, `p`.`old_price`, `p`.`sales_price`))",
                    "primary_price"      => "`p`.`_primary_price`",

                    //"on_special"         => "IF(`p`.`old_price` > IF(`p`.`price_flux` = 'y', `p`.`cost_price` * (`p`.`markup` + 100.0) / 100.0, `p`.`sales_price`), 'y', 'n')",
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
                    'site_id'            => "'3'",
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

            case 'custom_terms_condition':
                $cache[$key] = array();
                $query       = $DB->query(sprintf(
                    "SELECT * FROM `terms_condition_custom_templates` WHERE `status` = 'active' ORDER BY `id` ASC"
                ));
                while ($row = $DB->fetch_assoc($query)) {
                    $cache[$key][$row['id']] = trim($row['template_name']);
                }

                $DB->free_result($query);
                break;

            default:
                die('TODO: productGetOptionList(\'' . $key . '\')');
        }

        return $cache[$key];
    }
}
if (!function_exists('productGetValue')) {
    function productGetValue($id, $field)
    {
        global $DB;

        $fields = $DB->table_columns('products');
        if (array_key_exists($field, $fields)) {
            if ($row = $DB->query_first(sprintf(
                "SELECT `%s` FROM `products` AS `p` WHERE `p`.`id` = %s LIMIT 1",
                $field,
                $id
            ), MYSQLI_ASSOC)) {
                return $row[$field];
            }
        }

        $fields = productGetOptionList('ext_fields');
        if (array_key_exists($field, $fields)) {
            if ($row = $DB->query_first(sprintf(
                "SELECT %s AS `%s` FROM `products` AS `p` WHERE `p`.`id` = %d LIMIT 1",
                $fields[$field],
                $field,
                $id
            ), MYSQLI_ASSOC)) {
                return $row[$field];
            }
        }

        $item = productFromDatabase($id);
        if (array_key_exists($field, $item)) {
            return $item[$field];
        }

        return null;
    }
}
if (!function_exists('inventoryStockGetValue')) {
    function inventoryStockGetValue($product_id,$color,$field)
    {
        global $DB;

        $fields = $DB->table_columns('products_inventory_stock');
        if (array_key_exists($field, $fields)) {
            if ($row = $DB->query_first(sprintf(
                "SELECT `%s` FROM `products_inventory_stock` AS `pi` WHERE `pi`.`product_id` = %s and `pi`.`color_id` = %s LIMIT 1",
                $field,
                $product_id,
                $color
            ), MYSQLI_ASSOC)) {
                return $row[$field];
            }
        }
        return 0;
    }
}
if (!function_exists('inventoryStockadd_update_status')) {
    function inventoryStockadd_update_status($data)
    {
         $exist=inventoryStockGetValue($data['product_id'],$data['color_id'],'id');
        global $DB;
        if($exist){

              $DB->query(sprintf(
                "UPDATE `products_inventory_stock` SET `when_sold_out`='%s',`available`='%s' WHERE `product_id` = %d AND `color_id` = %d",
                    $data['when_sold_out'],
                    $data['available'],
                    $data['product_id'],
                    $data['color_id']
                ));
        }else{
            $DB->query(sprintf(
                "INSERT INTO `products_inventory_stock` (`product_id`, `color_id`, `user_id`,`when_sold_out`,`available`) VALUES (%d, %d, %d,'%s','%s')",
                $data['product_id'],
                $data['color_id'],
                $data['user_id'],
                $data['when_sold_out'],
                $data['available']
            ));
        }
    }
}
if (!function_exists('inventoryStockadd_update')) {
    function inventoryStockadd_update($data)
    {
        $exist=inventoryStockGetValue($data['product_id'],$data['color_id'],'id');
        global $DB;
        if($exist){
            if($data['factory_id']){
                $DB->query(sprintf(
                "UPDATE `products_inventory_stock` SET `qty` = %d,`factory_id`='%s',`when_sold_out`='%s' WHERE `product_id` = %d AND `color_id` = %d",
                    $data['qty'],
                    $data['factory_id'],
                    $data['when_sold_out'],
                    $data['product_id'],
                    $data['color_id']
                ));
            }else{
                  $DB->query(sprintf(
                    "UPDATE `products_inventory_stock` SET `qty` = %d ,`when_sold_out`='%s' WHERE `product_id` = %d AND `color_id` = %d",
                        $data['qty'],
                        $data['when_sold_out'],
                        $data['product_id'],
                        $data['color_id']
                    ));
            }

        }else{
            $DB->query(sprintf(
                "INSERT INTO `products_inventory_stock` (`product_id`, `color_id`, `qty`, `user_id`, `supplier_id`,`factory_id`,`when_sold_out`) VALUES (%d, %d, %d, %d,%d,'%s','%s')",
                $data['product_id'],
                $data['color_id'],
                $data['qty'],
                $data['user_id'],
                $data['supplier_id'],
                ($data['factory_id'])?$data['factory_id']:'',
                $data['when_sold_out']
            ));
        }
    }
}
if (!function_exists('addProductImages')) {
    function addProductImages($data){
        global $DB;
        $DB->query(sprintf(
            "INSERT INTO `products_images` (`product_id`, `color_id`, `user_id`,`image`, `caption`) VALUES (%d, %d, %d, '%s', '%s')",
            $data['product_id'],
            $data['color_id'],
            $data['user_id'],
            $data['image'],
            $data['caption']
        ));
    }
}
if (!function_exists('productCount')) {
    /**
     * Count the products grouped by field
     *
     * @param string       $criteria The product search query
     * @param array|string $group_by The fields to group by
     */
    function productCount($criteria, $group_by = '')
    {
        global $DB;

        //TODO: Deprecated calling convention
        if (is_array($criteria)) {
            $criteria = productQuery(array('id' => $criteria));
        }

        if (!is_array($group_by)) {
            $group_by = array($group_by);
        }

        $group_list = array();
        $field_list = array(
            'row_count' => 'COUNT(*) AS `row_count`',
        );
        foreach (productGetOptionList('all_fields') as $field => $expr) {
            if (in_array($field, $group_by)) {
                $group_list[] = sprintf("`%s`", $field);
                $field_list[] = sprintf("%s AS `%s`", $expr, $field);
            }
        }

        $results = array();
        $query   = $DB->query(sprintf(
            "SELECT %s FROM `products` AS `p` WHERE %s%s",
            implode(', ', $field_list),
            $criteria,
            (count($group_list)) ? sprintf(
                " GROUP BY %s",
                implode(', ', $group_list)
            ) : ''
        ));
        while ($row = $DB->fetch_assoc($query)) {
            $pointers = array(&$results);
            foreach ($group_by as $group) {
                if (!array_key_exists($group, $row)) {
                    continue;
                }
                $k          = $row[$group];
                $new_pointers = array();
                foreach (explode(',', $k) as $k2) {
                    foreach ($pointers as $k1 => $pointer) {
                        if (!array_key_exists($k2, $pointers[$k1])) {
                            $pointers[$k1][$k2] = array();
                        }
                        $new_pointers[] = &$pointers[$k1][$k2];
                    }
                }
                $pointers = $new_pointers;
            }
            foreach ($pointers as $pointer_key => $pointer) {
                if (is_array($pointers[$pointer_key])) {
                    $pointers[$pointer_key] = 0;
                }
                $pointers[$pointer_key] += $row['row_count'];
            }
        }
        $DB->free_result($query);
        return $results;
    }
}
if (!function_exists('productHasValue')) {
    function productHasValue($id, $field, $value)
    {
        return (productGetValue($id, $field) == $value);
    }
}
if (!function_exists('productIncrementViewCount')) {
    function productIncrementViewCount($id, $country)
    {
        global $DB;
        $DB->query(sprintf(
            "INSERT INTO `products_hits` (`product_id`, `country`) VALUES (%d, %s) ON DUPLICATE KEY UPDATE `hit_count` = `hit_count` + 1",
            $id,
            $DB->escape($country)
        ));
    }
}
if (!function_exists('getAvailableSupplierForProduct')) {
    function getAvailableSupplierForProduct($id)
    {
        global $DB;
        $response = array();
        $query       = $DB->query(sprintf(
            "SELECT `new_suppliers`.`supplier_id`, `company` FROM `new_suppliers`,supplier_product where new_suppliers.supplier_id=supplier_product.supplier_id AND supplier_product.product_id=$id ORDER BY `company` ASC"
        ));
        while ($row = $DB->fetch_assoc($query)) {
            $response[$row['supplier_id']] = $row['company'];
        }
        $DB->free_result($query);
        return $response;
    }
}
if (!function_exists('productPurchaseSummary')) {
    function productPurchaseSummary($id, $stock_left = false, $supplier_id = '')
    {
        global $DB;

        $result = array();
        $query  = $DB->query(sprintf(
            "SELECT * FROM `products_inventory` WHERE `deleted_at` IS NULL AND `product_id` = %d AND `movement` > 0%s%s ORDER BY `created_at` %s, `id` %s",
            $id,
            ($supplier_id) ? sprintf(
                " AND `supplier_id` = %d",
                $supplier_id
            ) : '',
            ($stock_left) ? " AND `stock_left` > 0" : '',
            ($stock_left) ? "ASC" : "DESC",
            ($stock_left) ? "ASC" : "DESC"
        ));
        while ($row = $DB->fetch_assoc($query)) {
            $result[] = $row;
        }
        $DB->free_result($query);

        return $result;
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
                        '`p`.`slug`',
                        //'`p`.`admin_notes`',
                        //'`p`.`excel_id`',
                        //'`p`.`factory_id`',
                        //'`p`.`factory_meta_description`',
                        //'`p`.`factory_meta_keywords`',
                        //'`p`.`factory_meta_title`',
                        //'`p`.`long_desc`',
                        //'`p`.`meta_description`',
                        //'`p`.`meta_keywords`',
                        //'`p`.`meta_title`',
                        //'`p`.`notes`',
                        //'`p`.`power_consumption_110`',
                        //'`p`.`power_consumption`',
                        //'`p`.`product_details`',
                        //'`p`.`short_desc`',
                        //'`p`.`stock_type`',
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
    /**
     * @param string $where
     *
     * @return int[]
     */
    function productSearch($where = null)
    {
        global $DB;
        if(isset($where['type'])){
            $where['category_id']=$where['type'];
        }
        
        
        $result = [];
        if ($query = $DB->query(sprintf(
            "SELECT `id` FROM `products` AS `p`%s ORDER BY `p`.`id` ASC",
            (!is_null($where)) ? sprintf(" WHERE %s", $where) : ""
        ))) {
            while ($row = $DB->fetch_assoc($query)) {
                $result[] = $row['id'];
            }
            $DB->free_result($query);
        }

        return $result;
    }
}
if (!function_exists('getproductBuilder')) {
    function getproductBuilder($product_id)
    {
                global $DB;
                $result= array();
                $query       = $DB->query(sprintf(
                    "SELECT bul.`id`, bul.`name` FROM `pb_builders` bul ,`pb_machines` pm WHERE bul.`status` =1 AND bul.`id`=pm.`builder_id` AND pm.`product_id`=".$product_id." GROUP BY bul.`id` ORDER BY bul.`name` ASC"
                ));
                while ($row = $DB->fetch_assoc($query)) {
                    $result[$row['id']]= $row['name'];
                }
                $DB->free_result($query);
                return $result;
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
        $fields = $DB->table_columns('`products`');
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
if (!function_exists('productSumValues')) {
    function productSumValues($product_list)
    {
        TODO(__FUNCTION__, func_get_args());return array(); //TODO: Double check this function

        global $DB;
        return $DB->query_first(sprintf(
            "SELECT SUM(IF(`p`.`stock_type` = 'stock_for_sale', `p`.`qty` * `p`.`product_costus`, 0)) AS `product_costus`, SUM(IF(`p`.`stock_type` = 'stock_for_sale', `p`.`qty` * `p`.`product_offerus`, 0)) AS `product_offerus` FROM `products` AS `p` WHERE `p`.`id` IN(%s)",
            implode(", ", $product_list)
        ), MYSQLI_ASSOC);
    }
}
if (!function_exists('productUpdateCostPrice')) {
    function productUpdateCostPrice($id)
    {
        //TODO(__FUNCTION__, func_get_args());return false; //TODO: Double check this function

        global $DB;

        $dPoints = intval(productGetValue($id, 'precision'));
        $res     = $DB->query(sprintf(
            "SELECT `stock_left` AS `movement`, (`unit_cost_usd` + `unit_freight_usd`) AS `unit_cost_usd` FROM `products_inventory` WHERE `id` = %d AND `movement` > 0 AND `stock_left` > 0",
            $id
        ));
        $count = $total = 0;
        while ($row = $DB->fetch_assoc($res)) {
            if ($row['unit_cost_usd'] > 0) {
                $count += $row['movement'];
                $total += ($row['movement'] * $row['unit_cost_usd']);
            }
        }
        $DB->free_result($res);
        productSetValue($id, 'cost_price', (($count > 0) ? convertCurrency($total / $count, 'USD', '', false, $dPoints) : 0));
        productSetValue($id, 'cost_currency', 'USD');
    }
}

if (!function_exists('productProblemSearch')) {
    function productProblemSearch()
    {
        global $DB;

        $result = array();
        $query  = $DB->query(sprintf(
            "SELECT `id` FROM `products` WHERE `sales_currency` != 'USD' OR `cost_currency` != 'USD' OR `sales_currency` != `cost_currency`"
        ));
        while ($row = $DB->fetch_assoc($query)) {
            $result[] = $row["id"];
        }
        $DB->free_result($query);

        return $result;
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
if (!defined('PRODUCT_TABLENAME')) {
    define('PRODUCT_TABLENAME', '`products`');
}
if (!defined('PRODUCT_PRIMARYKEY')) {
    define('PRODUCT_PRIMARYKEY', 'id');
}

if (!class_exists("Product_Handler")) {
    include_once $_SERVER['DOCUMENT_ROOT'] . '/webadmin/home_common/class.genericsection_handler.php';
    class Product_Handler extends GenericSection_Handler
    {
        public $section_name    = "product";
        public $table_name      = "products";
        public $database_prefix = "";
        public $search_bits     = array("`a`.`id`", "`a`.`name`", "`a`.`summary`", "`a`.`content`", "`a`.`title`", "`a`.`keywords`", "`a`.`description`");
        public $primary_key     = "id";
        public function getOptionList($table_name)
        {
            global $DB;
            global $_GLOBALS;
            $table       = "";
            $option_list = array();
            switch ($table_name) {
                case "manual_path":
                    return $_SERVER['DOCUMENT_ROOT'] . "/webadmin/uploads/product/manual";
                case "manual_archive_path":
                    return $_SERVER['DOCUMENT_ROOT'] . "/webadmin/uploads/product/manual_archive";
                case "firmware_path":
                    return $_SERVER['DOCUMENT_ROOT'] . "/webadmin/uploads/product/firmware";
                case "staff_id":
                    $table = "`highwaygroup`.`users` ORDER BY `id` DESC";
                    $key   = "`id`";
                    $value = "CONCAT_WS(' ', `first_name`, `last_name`)";
                    break;
                case "status":
                    return array(
                        "y" => "Available Now",
                        "d" => "No Longer in Production",
                        //"n" => "Coming Soon",
                    );
                case "active":
                    return array(
                        "y" => "Active",
                        "n" => "Inactive",
                    );
                case "members_only":
                    return array(
                        "n" => "No",
                        "y" => "Yes",
                    );
                default:
                    return parent::getOptionList($table_name);
            }
            if ("" != $table) {
                $query = $DB->query("SELECT " . $key . "," . $value . " FROM " . $table);
                while ($row = $DB->fetch_array($query, MYSQLI_NUM)) {
                    $option_list[$row[0]] = $row[1];
                }
            }
            return $option_list;
        }
        public function manualsFromPost($posted)
        {
            global $DB;
            $id           = $posted["id"];
            $result       = $this->manualsFromDatabase($id);
            $result["id"] = intval($id);
            if (is_array($posted)) {
                foreach ($posted as $k => $v) {
                    $result[$k] = $v;
                }
            }
            if ($id <= 0) {
                unset($result["id"]);
                $result["created_at"] = formatDateTime();
            }
            return $result;
        }
        public function manualsExists($id)
        {
            global $DB;

            return !!$DB->query_first(sprintf(
                "SELECT 1 FROM %s`%s_manuals` WHERE `id` = %d LIMIT 1",
                $this->database_prefix,
                $this->section_name,
                $id
            ), MYSQLI_ASSOC);
        }
        public function manualsToDatabase($item)
        {
            global $DB;
            $id      = intval($item["id"]);
            $fields  = array("product_id", "name", "comment", "active", "orig", "file", "pos");
            $changed = array();
            if ($this->manualsExists($id)) {
                $set_vals = array();
                foreach ($item as $k => $v) {
                    if (in_array($k, $fields)) {
                        $set_vals[$k] = "`" . $k . "` = " . $DB->escape($item[$k]);
                    }
                }
                if (count($set_vals)) {
                    $set_vals["modified"] = "`modified` = NOW()";
                    $DB->query(sprintf(
                        "UPDATE %s`%s_manuals` SET %s WHERE `id` = %d LIMIT 1",
                        $this->database_prefix,
                        $this->section_name,
                        implode(', ', $set_vals),
                        $id
                    ));
                }
                return $id;
            } else {
                $manuals = $this->allManualsFromDatabase($item["product_id"]);
                $lastManual = end($manuals);
                $item["pos"] = $lastManual["pos"] + 1;
                $insert_vals = array();
                foreach ($item as $k => $v) {
                    if (in_array($k, $fields)) {
                        $insert_vals[$k] = $DB->escape($v);
                    }
                }
                unset($insert_vals['timestamp']);
                if (count($insert_vals)) {
                    $insert_vals["modified"]   = "NOW()";
                    $insert_vals["created_at"] = "NOW()";
                    $DB->query("INSERT INTO " . $this->database_prefix . "`" . $this->section_name . "_manuals` (`" . implode('`, `', array_keys($insert_vals)) . "`) VALUES (" . implode(", ", $insert_vals) . ")");
                    return $DB->insert_id();
                }
            }
            return $id;
        }
        public function manualsFromDatabase($id)
        {
            global $DB;
            return $DB->query_first("SELECT * FROM " . $this->database_prefix . "`" . $this->section_name . "_manuals` WHERE `id` = " . intval($id) . " LIMIT 1", MYSQLI_ASSOC);
        }
        public function getManualsByOrig($orig)
        {
            global $DB;
            return $DB->query_first("SELECT * FROM " . $this->database_prefix . "`" . $this->section_name . "_manuals` WHERE `orig` = " . $DB->escape($orig) . " LIMIT 1", MYSQLI_ASSOC);
        }
        public function allManualsFromDatabase($id)
        {
            global $DB, $_GLOBALS;
            $result = array();
            $query  = $DB->query("SELECT pm.`created_at` as prodduct_created,pm.`product_id`,pm.`pos`,pm.`id` as product_manual_id ,m.* FROM " . $this->database_prefix . "`" . $this->section_name . "_manuals` as pm, `manuals` as m WHERE pm.`" . $this->section_name . "_id` = " . intval($id) . " AND m.id=pm.manual_id ORDER BY `pos` ASC");
            while ($row = $DB->fetch_assoc($query)) {
                $result[$row["id"]] = $row;
            }
            $DB->free_result($query);
            return $result;
        }
        public function updateManualsPosition($product_id, $item)
        {
            global $DB;
            $count=0;
            foreach ($item as $id => $pos) {
                $DB->query("UPDATE " . $this->database_prefix . "`" . $this->section_name . "_manuals` SET `pos` = " . $count . ",`product_id`=".$product_id." WHERE `id` = " . $id . " LIMIT 1");
                $count++;
            }
        }
        public function manualsDelete($id)
        {
            global $DB;
            $manuals = $this->manualsFromDatabase($id);
            unlink($this->getOptionList("manual_path") . "/" . $manuals["file"]);
            $DB->query("DELETE FROM " . $this->database_prefix . "`" . $this->section_name . "_manuals` WHERE `id` = " . intval($id));
        }
        public function firmwareExists($id)
        {
            static $results = array();
            if (array_key_exists($id, $results)) {
                return $results[$id];
            }
            global $DB;
            $row = $DB->query_first("SELECT `id` FROM " . $this->database_prefix . "`" . $this->section_name . "_firmware` WHERE `id` = " . intval($id) . " LIMIT 1", MYSQLI_ASSOC);
            if ($row) {
                return $results[$id] = true;
            }
            return false;
        }
        public function firmwareFromPost($posted)
        {
            global $DB;
            $id           = $posted["id"];
            $result       = $this->firmwareFromDatabase($id);
            $result["id"] = intval($id);
            if (is_array($posted)) {
                foreach ($posted as $k => $v) {
                    $result[$k] = $v;
                }
            }
            if ($id <= 0) {
                unset($result["id"]);
                $result["created"] = formatDateTime();
            }
            return $result;
        }
        public function firmwareFromDatabase($id)
        {
            global $DB;
            return $DB->query_first("SELECT * FROM " . $this->database_prefix . "`" . $this->section_name . "_firmware` WHERE `id` = " . intval($id) . " LIMIT 1", MYSQLI_ASSOC);
        }
        public function getFirmwareByOrig($orig)
        {
            global $DB;
            return $DB->query_first("SELECT * FROM " . $this->database_prefix . "`" . $this->section_name . "_firmware` WHERE `orig` = " . $DB->escape($orig) . " LIMIT 1", MYSQLI_ASSOC);
        }
        public function firmwareDelete($id)
        {
            global $DB;
            $firmware = $this->firmwareFromDatabase($id);
            unlink($this->getOptionList("firmware_path") . "/" . $firmware["file"]);
            $DB->query("DELETE FROM " . $this->database_prefix . "`" . $this->section_name . "_firmware` WHERE `id` = " . intval($id));
        }
        public function updateFirmwaresPosition($product_id, $item)
        {
            global $DB;
            $count=0;
            foreach ($item as $id => $pos) {
                $DB->query("UPDATE " . $this->database_prefix . "`" . $this->section_name . "_firmware` SET `pos` = " . $count . " WHERE `id` = " . $id . " LIMIT 1");
                $count++;
            }
      
        }
        public function firmwaresFromDatabase($id)
        {
            global $DB;
            $result = array();
 
            $query  = $DB->query("SELECT fm.`created`,fm.`product_id`,fm.`pos`,fm.`id` as product_firmware_id,f.* FROM " . $this->database_prefix . "`" . $this->section_name . "_firmware` as fm,`firmware` as f WHERE fm.`" . $this->section_name . "_id` = " . intval($id) . " AND f.id=fm.firmware_id  ORDER BY `pos` ASC");
            while ($row = $DB->fetch_assoc($query)) {
                $result[$row["id"]] = $row;
            }
            $DB->free_result($query);
            return $result;
        } 
        public function videosFromPost($posted,$product_id)
        {
            $count  = -1;
            $id     = intval($posted["id"]);
            $result = $this->videosFromDatabase($id);
            if (array_key_exists("videos", $posted) && is_array($posted["videos"])) {
                $to_delete = array();
                if (array_key_exists("affected", $posted["videos"])) {
                    $to_delete = preg_split("/[^0-9]+/", $posted["videos"]["affected"]);
                }
                foreach ($posted["videos"] as $k => $v) {
                    if (is_numeric($k)) {
                        $v["product_id"] = $product_id;
                        if (($key = array_search($v["id"], $to_delete)) !== false) {
                            unset($to_delete[$key]);
                        }
                        if (array_key_exists($v["id"], $result)) {
                            $v["modified"]    = formatDateTime();
                            $result[$v["id"]] = $v + $result[$v["id"]];
                        } else {
                            $v["created"]     = formatDateTime();
                            $result[$count--] = $v;
                        }
                    }
                }
                foreach ($to_delete as $k) {
                    unset($result[$k]);
                }
            }
            return $result;
        }
        public function videosToDatabase($id, $videos)
        {
            //die("DELETE FROM " . $this->database_prefix . "`" . $this->section_name . "_videos` WHERE `" . $this->section_name . "_id` = " . intval($id));
            global $DB;
            $DB->query("DELETE FROM " . $this->database_prefix . "`" . $this->section_name . "_videos` WHERE `" . $this->section_name . "_id` = " . intval($id));
            if (is_array($videos)) {
                foreach ($videos as $row) {
                    $DB->query("INSERT INTO " . $this->database_prefix . "`" . $this->section_name . "_videos` (`" . $this->section_name . "_id`, `rank`, `name`, `youtube_id`, `active`, `members_only`, `created`, `modified`) VALUES (" . intval($id) . "," . intval($row["rank"]) . ", " . $DB->escape($row['name']) . ", " . $DB->escape($row['youtube_id']) . ", " . $DB->escape($row['active']) . ", " . $DB->escape($row['members_only']) . ", " . $DB->escape($row['created']) . ", " . $DB->escape(($row['modified'])?$row['modified']:0) . ")");
                }
            }
        }
        public function videosFromDatabase($id)
        {
            global $DB, $_GLOBALS;
            $result = array();
            $query  = $DB->query("SELECT *, UNIX_TIMESTAMP(`created`) AS `created_time` FROM " . $this->database_prefix . "`" . $this->section_name . "_videos` WHERE `" . $this->section_name . "_id` = " . intval($id) . " ORDER BY `rank` ASC");
            while ($row = $DB->fetch_assoc($query)) {
                $result[$row["id"]] = $row;
            }
            $DB->free_result($query);
            return $result;
        }
        public function linksFromPost($posted,$product_id,$files)
        {
            $count  = -1;
            $id     = intval($posted["id"]);
            $result = $this->linksFromDatabase($id);
            if (array_key_exists("links", $posted) && is_array($posted["links"])) {
                $to_delete = array();
                if (array_key_exists("affected", $posted["links"])) {
                    $to_delete = preg_split("/[^0-9]+/", $posted["links"]["affected"]);
                }
                foreach ($posted["links"] as $k => $v) {
                    if(isset($files['name'][$k])){
                        $file_orig = $files['name'][$k];
                        $file_temp = $files['tmp_name'][$k];
                        $error = $files['error'][$k];
                        $size = intval($files['size'][$k]);
                        $file_ext=substr($file_orig,strrpos($file_orig,'.'));
                        if(substr($file_ext,0,1)=='.') 
                        $file_ext=substr($file_ext,1);
                        $file_new = "link_".$product_id.'_'.rand().'.'.$file_ext; 
                        $dir = $_SERVER['DOCUMENT_ROOT'].'/webadmin/uploads/product/links/';
                        if(move_uploaded_file($file_temp,$dir.$file_new)){
                            $v['attachment']=$file_new;
                            $v['file_original']=$file_orig;
                            $v['size']=$size;
                            $v['filetype']=$file_ext;
                        }
                    }
                    if (is_numeric($k)) {
                        $v["product_id"] = $product_id;
                        if (($key = array_search($v["id"], $to_delete)) !== false) {
                            unset($to_delete[$key]);
                        }
                        if (array_key_exists($v["id"], $result)) {
                            $v["modified"]    = formatDateTime();
                            $result[$v["id"]] = $v + $result[$v["id"]];
                        } else {
                            $v["created"]     = formatDateTime();
                            $result[$count--] = $v;
                        }
                    }
                }
                foreach ($to_delete as $k) {
                    unset($result[$k]);
                }
            }
            return $result;
        }
        public function linksToDatabase($id, $links)
        {
            global $DB;
            $DB->query("DELETE FROM " . $this->database_prefix . "`" . $this->section_name . "_links` WHERE `" . $this->section_name . "_id` = " . intval($id));
            if (is_array($links)) {
                foreach ($links as $row) {
                    $insert_array=[];
                    foreach ($row as $key => $value) {
                        if($value!=''){
                            $insert_array[$key]=$DB->escape($value);
                        }
                    }
                    $DB->query(sprintf(
                    "INSERT INTO %s (`%s`) VALUES (%s)",
                    "" . $this->database_prefix . "`" . $this->section_name . "_links`",
                    implode('`, `', array_keys($insert_array)),
                    implode(", ", $insert_array)
                    ));
                }
            }
        }
        public function linksFromDatabase($id)
        {
            global $DB, $_GLOBALS;
            $result = array();
            $query  = $DB->query("SELECT *, UNIX_TIMESTAMP(`created`) AS `created_time` FROM " . $this->database_prefix . "`" . $this->section_name . "_links` WHERE `" . $this->section_name . "_id` = " . intval($id) . " ORDER BY `rank` ASC");
            while ($row = $DB->fetch_assoc($query)) {
                $result[$row["id"]] = $row;
            }
            $DB->free_result($query);
            return $result;
        }
    }
}
if (!defined("QUERY_PRODUCT")) {
    define("QUERY_PRODUCT", 150928);
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
function userPermittedSections($user, $site_id = 0)
{
    static $cache = array();
    if (array_key_exists($user, $cache) && array_key_exists($site_id, $cache[$user])) {
        return $cache[$user][$site_id];
    }

    global $DB;
    if ('' == $user) {
        return array();
    }

    if (0 == $site_id) {
        $site_id = 11;
    }

    $result = array();
    $query  = $DB->query("SELECT `ase`.`sect_key`, `au`.`section_id` FROM `highwaygroup`.`admin_users` AS `au` LEFT JOIN `highwaygroup`.`admin_sections` AS `ase` ON `ase`.`section_id` = `au`.`section_id` WHERE `user` = " . $DB->escape($user) . " AND `site_id` = " . intval($site_id));
    while ($row = $DB->fetch_assoc($query)) {
        $result[] = $row['sect_key'];
    }

    $DB->free_result($query);

    $cache[$user][$site_id] = $result;

    return $result;
}
// @todo We have multiple conflicting product modules
include_once $_SERVER['DOCUMENT_ROOT'] . '/webadmin/new_products/functions/query_product.inc.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/webadmin/products/functions/query_products.inc.php';
include_once 'query_productreserve.inc.php';
include_once 'query_productinventory.inc.php';
