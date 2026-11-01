<?php
if (!defined('INVOICE_PRODUCT_TABLENAME')) {
    define('INVOICE_PRODUCT_TABLENAME', '`highwaygroup`.`invoice_products`');
}
if (!defined('INVOICE_PRODUCT_PRIMARYKEY')) {
    define('INVOICE_PRODUCT_PRIMARYKEY', 'id');
}
if (!defined('INVOICE_PRODUCT_SECONDARYKEY')) {
    define('INVOICE_PRODUCT_SECONDARYKEY', 'invoice_id');
}

if (!function_exists('invoiceProductDefault')) {
    /**
     * @return mixed[]
     */
    function invoiceProductDefault()
    {
        global $DB;

        return invoiceProductVirtual($DB->modelDefault(
            INVOICE_PRODUCT_TABLENAME,
            INVOICE_PRODUCT_PRIMARYKEY
        ));
    }
}
if (!function_exists('invoiceProductDelete')) {
    /**
     * @param int $id
     *
     * @return int
     */
    function invoiceProductDelete($id)
    {
        global $DB;

        return $DB->modelDelete(
            $id,
            INVOICE_PRODUCT_TABLENAME,
            INVOICE_PRODUCT_PRIMARYKEY
        );
    }
}
if (!function_exists('invoiceProductExists')) {
    /**
     * @param int $id
     *
     * @return bool
     */
    function invoiceProductExists($id)
    {
        global $DB;

        return $DB->modelExists(
            $id,
            INVOICE_PRODUCT_TABLENAME,
            INVOICE_PRODUCT_PRIMARYKEY
        );
    }
}
if (!function_exists('invoiceProductFromDatabase')) {
    /**
     * @param int $id
     *
     * @return mixed[]|null
     */
    function invoiceProductFromDatabase($id)
    {
        global $DB;

        if ($row = $DB->modelLoad(
            $id,
            INVOICE_PRODUCT_TABLENAME,
            INVOICE_PRODUCT_PRIMARYKEY
        )) {
            return invoiceProductVirtual($row);
        }

        return null;
    }
}
if (!function_exists('invoiceProductGetValue')) {
    /**
     * @param int    $id
     * @param string $field
     *
     * @return mixed
     */
    function invoiceProductGetValue($id, $field)
    {
        global $DB;

        if (array_key_exists($field, $DB->table_columns(
            INVOICE_PRODUCT_TABLENAME
        ))) {
            return $DB->modelGet(
                $id,
                $field,
                INVOICE_PRODUCT_TABLENAME,
                INVOICE_PRODUCT_PRIMARYKEY
            );
        }

        if ($model = invoiceProductFromDatabase($id)) {
            return (array_key_exists($field, $model)) ? $model[$field] : null;
        }

        return null;
    }
}
if (!function_exists('invoiceProductHasValue')) {
    /**
     * @param int    $id
     * @param string $field
     * @param mixed  $value
     *
     * @return bool
     */
    function invoiceProductHasValue($id, $field, $value)
    {
        return invoiceProductGetValue($id, $field) == $value;
    }
}
if (!function_exists('invoiceProductQuery')) {
    /**
     * @todo
     *
     * @param mixed[] $eq
     * @param mixed[] $lt
     * @param mixed[] $gt
     *
     * @return string
     */
    function invoiceProductQuery($eq = array(), $lt = array(), $gt = array())
    {
        global $DB;

        $whereAND = array();

        return (count($whereAND)) ? implode(" AND ", $whereAND) : "1";
    }
}
if (!function_exists('invoiceProductSearch')) {
    /**
     * @param string|null $where
     * @param string|null $order_by
     *
     * @return int[]
     */
    function invoiceProductSearch($where = null, $order_by = null)
    {
        global $DB;

        // Legacy accepts criteria as first parameter
        if (is_array($where)) {
            $where = invoiceProductQuery($where);
        }

        return $DB->query_list(sprintf(
            "SELECT `%s` FROM %s%s%s",
            INVOICE_PRODUCT_PRIMARYKEY,
            INVOICE_PRODUCT_TABLENAME,
            (!is_null($where)) ? sprintf(
                " WHERE %s",
                $where
            ) : "",
            (!is_null($order_by)) ? sprintf(
                " ORDER BY `%s`",
                $order_by
            ) : ""
        ));
    }
}
if (!function_exists('invoiceProductSetValue')) {
    /**
     * @param int    $id
     * @param string $field
     * @param mixed  $value
     *
     * @return int
     */
    function invoiceProductSetValue($id, $field, $value)
    {
        global $DB;

        return $DB->modelSet(
            $id,
            $field,
            $value,
            INVOICE_PRODUCT_TABLENAME,
            INVOICE_PRODUCT_PRIMARYKEY
        );
    }
}
if (!function_exists('invoiceProductSort')) {
    /**
     * @param string|null $where
     * @param string|null $order_by
     *
     * @return int[]
     */
    function invoiceProductSort($where = null, $order_by = null)
    {
        // Legacy accepts list of id's as first parameter
        if (is_array($where)) {
            $where = invoiceProductQuery(array(
                INVOICE_PRODUCT_PRIMARYKEY => $where,
            ));
        }

        return invoiceProductSearch($where, $order_by);
    }
}
if (!function_exists('invoiceProductToDatabase')) {
    /**
     * @param mixed[] $item
     *
     * @return int
     */
    function invoiceProductToDatabase(&$item)
    {
        global $DB;

        return $DB->modelSave(
            $item,
            INVOICE_PRODUCT_TABLENAME,
            INVOICE_PRODUCT_PRIMARYKEY
        );
    }
}
if (!function_exists('invoiceProductVirtual')) {
    /**
     * @param mixed[] $row
     *
     * @return mixed[]
     */
    /*
    function invoiceProductVirtual($row)
    {
        return $row;
    }
    */
}

if (!function_exists('invoiceProductsDelete')) {
    /**
     * @param int $id
     *
     * @return int
     */
    function invoiceProductsDelete($id)
    {
        global $DB;

        return $DB->childrenDelete(
            $id,
            INVOICE_PRODUCT_TABLENAME,
            INVOICE_PRODUCT_SECONDARYKEY
        );
    }
}
if (!function_exists('invoiceProductsFromDatabase')) {
    /**
     * @param int $id
     *
     * @return mixed[]|null
     */
    /*
    function invoiceProductsFromDatabase($id)
    {
        global $DB;

        $result = array();
        foreach ($DB->childrenLoad(
            $id,
            INVOICE_PRODUCT_TABLENAME,
            INVOICE_PRODUCT_PRIMARYKEY,
            INVOICE_PRODUCT_SECONDARYKEY
        ) as $k => $v) {
            $result[$k] = invoiceProductVirtual($v);
        }

        return $result;
    }
    */
}
if (!function_exists('invoiceProductsToDatabase')) {
    /**
     * @param int       $id
     * @param mixed[][] $items
     *
     * @return void
     */
    /*
    function invoiceProductsToDatabase($id, &$items)
    {
        global $DB;

        $DB->childrenSave(
            $id,
            $items,
            INVOICE_PRODUCT_TABLENAME,
            INVOICE_PRODUCT_PRIMARYKEY,
            INVOICE_PRODUCT_SECONDARYKEY
        );
    }
    */
}

if (!function_exists("invoiceProductsFromDatabase")) {
    /**
     * @param int $id
     *
     * @return mixed[][]
     */
    function invoiceProductsFromDatabase($id)
    {
        global $DB;

        static $cache = array();
        if (array_key_exists($id, $cache)) {
            return $cache[$id];
        }

        $result = array();
        if ($query = $DB->query(sprintf(
            "SELECT * FROM %s WHERE `%s` = %d ORDER BY `product_rank` ASC",
            INVOICE_PRODUCT_TABLENAME,
            INVOICE_PRODUCT_SECONDARYKEY,
            $id
        ))) {
            while ($row = $DB->fetch_assoc($query)) {
                $result[] = invoiceProductVirtual($row);
            }
            $DB->free_result($query);
        }

        $cache[$id] = $result;

        return $result;
    }
}
if (!function_exists("invoiceProductsFromPost")) {
    /**
     * @param mixed[] $posted
     *
     * @return mixed[][]
     */
    function invoiceProductsFromPost($posted)
    {
        global $DB;

        $fields = array();
        if ($query = $DB->query(sprintf(
            "SHOW COLUMNS FROM %s",
            INVOICE_PRODUCT_TABLENAME
        ))) {
            while ($row = $DB->fetch_array($query)) {
                if ('auto_increment' != $row[5]) {
                    $fields[$row[0]] = $row[4];
                } else {
                    $auto = $row[0];
                }
            }
            $DB->free_result($query);
        }

        $products = array();
        $id       = intval($posted['invoice_id']);
        if (invoiceExists($id)) {
            $products = invoiceProductsFromDatabase($id);
            $status   = invoiceGetValue($id, 'invoice_status');
        }
        if (array_key_exists('product_affected', $posted)) {
            foreach (explode(',', $posted['product_affected']) as $key1) {
                foreach ($products as $key2 => $product) {
                    if ($product['prod_count'] == $key1) {
                        unset($products[$key2]);
                    }
                }
            }
        }
        if (array_key_exists('prod_count', $posted)) {
            foreach ($posted['prod_count'] as $key1) {
                foreach ($products as $key2 => $product) {
                    if ($product['prod_count'] == $key1) {
                        unset($products[$key2]);
                    }
                }
            }
        }

        if (array_key_exists('product_id', $posted)) {
            foreach ($posted['product_id'] as $key1 => $product_id) {
                $product                               = $fields;
                $product[INVOICE_PRODUCT_SECONDARYKEY] = $id;
                foreach ($fields as $key2 => $value) {
                    if (array_key_exists($key2, $posted) && is_array($posted[$key2]) && array_key_exists($key1, $posted[$key2])) {
                        $product[$key2] = $posted[$key2][$key1];
                    }
                }

                $products[] = $product;
            }
        }
        /*
        if($status=='proforma')
        {
        foreach(array('bill_name', 'bill_email', 'bill_company', 'bill_address', 'bill_country', 'invoice_currency', 'paymenttype_id', 'ship_address', 'ship_country', 'products_offer', 'ship_offer', 'charges_offer', 'invoice_offer', 'terms_id', 'proforma_date') as $key)
        if($new_vals[$key]!=$old_vals[$key]) $products['do_revision']=true;
        }
         */
        return $products;
    }
}
if (!function_exists("invoiceProductsToDatabase")) {
    /**
     * @param int       $id
     * @param mixed[][] $products
     *
     * @return void
     */
    function invoiceProductsToDatabase($id, $products)
    {
        global $DB;
        $id = intval($id);
        if (0 == $id) {
            return;
        }

        $DB->query(sprintf(
            "DELETE FROM %s WHERE `%s` = %d",
            INVOICE_PRODUCT_TABLENAME,
            INVOICE_PRODUCT_SECONDARYKEY,
            $id
        ));

        $fields = array();
        if ($query = $DB->query(sprintf(
            "SHOW COLUMNS FROM %s",
            INVOICE_PRODUCT_TABLENAME
        ))) {
            while ($row = $DB->fetch_array($query)) {
                if ('auto_increment' != $row[5]) {
                    $fields[$row[0]] = $row[4];
                }
            }
            $DB->free_result($query);
        }

        $re_sort = array();
        foreach ($products as $key => $product) {
            $re_sort[$key] = $product['product_rank'];
        }

        asort($re_sort);
        $counter = 1;
        foreach ($re_sort as $key => $rank) {
            $products[$key]['product_rank'] = $counter++;
        }

        foreach ($products as $product) {
            $insert_vals = array();
            if ($id > 0) {
                $product[INVOICE_PRODUCT_SECONDARYKEY] = $id;
            }

            foreach ($product as $key => $value) {
                if (array_key_exists($key, $fields)) {
                    $insert_vals[$key] = addslashes($value);
                }
            }

            $DB->query(sprintf(
                "INSERT INTO %s (`%s`) VALUES ('%s')",
                INVOICE_PRODUCT_TABLENAME,
                implode('`, `', array_keys($insert_vals)),
                implode("', '", $insert_vals)
            ));
        }
    }
}
if (!function_exists('invoiceProductVirtual')) {
    /**
     * @param mixed[] $row
     *
     * @return mixed[]
     */
    function invoiceProductVirtual($row)
    {
        moduleLoad('product');

        $row['product_number'] = $row['product_id'];
        $productData = productFromDatabase($row['product_id']);
        $row['is_harmonised_code'] = $productData['is_harmonised_code'];
        $row['is_origin_country'] = $productData['is_origin_country'];
        $row['is_product_materials'] = $productData['is_product_materials'];
        $row['is_product_usage'] = $productData['is_product_usage'];
        $row['harmonised_code'] = $productData['harmonised_code'];
        $row['origin_country'] = $productData['origin_country'];
        $row['product_usage'] = $productData['product_usage'];
        $row['product_materials'] = $productData['product_materials'];
        $row['product_shipping_name'] = $productData['product_shipping_name'];
        if($row['colour_id']>0){
            $row['colour_name']= productGenericFromDatabase($row['colour_id'], 'product_colour')['colour'];
            $row['colour_code']= productGenericFromDatabase($row['colour_id'], 'product_colour')['code3'];
        }else{
             $row['colour_name']='';
             $row['colour_code']='';
        }
        if ('' != $row['generic_code'] && '0' == $row['product_id']) {
            $row['product_number'] = $row['generic_code'];
        } elseif ($row['product_id'] > 0 && $row['sub_id'] > 0) {
            $row['product_number'] = sprintf('%05d - %04d', $row['product_id'], $row['sub_id']);
        }

        /**
         * @hack Missing site_id for invoiceProduct
         */
        if (0 == $row['site_id'] && in_array($row['generic'], array('n', 'b'))) {
            $row['site_id'] = invoiceGetValue($row['invoice_id'], 'site_id');
        }

        /**
         * @hack Missing product_weight for invoiceProduct
         */
        if ('n' == $row['generic'] && function_exists('productDimensionFromDatabase') && bccomp($row['product_weight'], "0.000", 3) <= 0) {
            $product_str = sprintf('%d %d %d', $row['product_id'], $row['sub_id'], $row['site_id']);
            $dim         = productDimensionFromDatabase($product_str);
            if (is_array(reset($dim))) {
                $dim = reset($dim);
            }
            if (bccomp($dim['online_weight'], "0.000", 3) > 0) {
                $row['product_weight']     = $dim['online_weight'];
                $row['product_weight_inc'] = (bccomp($dim['inc_weight'], "0.000", 3) > 0) ? $dim['inc_weight'] : $dim['online_weight'];
            } else {
                $row['product_weight']     = $dim['unit_online_weight'];
                $row['product_weight_inc'] = (bccomp($dim['unit_inc_weight'], "0.000", 3) > 0) ? $dim['unit_inc_weight'] : $dim['unit_online_weight'];
            }
            $row['product_total_weight'] = bcadd(bcmul(strval(max(0, $row['product_qty'] - 1)), $row['product_weight_inc'], 3), $row['product_weight'], 3);
        }

        return $row;
    }
}

if (!function_exists("invoiceProductFormatNumber")) {
    /**
     * @param int $product_id
     * @param int $sub_id
     *
     * @return string
     */
    function invoiceProductFormatNumber($product_id, $sub_id)
    {
        /*
        @TODO: Test this works correctly
        return sprintf(
            "%05d%s",
            $product_id,
            ($sub_id > 0) ? sprintf(
                "-%04d",
                $sub_id
            ) : ""
        );
        */
        return str_pad((string)$product_id, 5, '0', STR_PAD_LEFT) . (($sub_id > 0) ? '-' . str_pad((string)$sub_id, 4, '0', STR_PAD_LEFT) : '');
    }
}
