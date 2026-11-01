<?php
if (!defined('SHIPINVOICE_PRODUCT_TABLENAME')) {
    define('SHIPINVOICE_PRODUCT_TABLENAME', '`highwaygroup`.`shipinvoice_products`');
}
if (!defined('SHIPINVOICE_PRODUCT_PRIMARYKEY')) {
    define('SHIPINVOICE_PRODUCT_PRIMARYKEY', 'id');
}
if (!defined('SHIPINVOICE_PRODUCT_SECONDARYKEY')) {
    define('SHIPINVOICE_PRODUCT_SECONDARYKEY', 'sinvoice_id');
}

if (!function_exists('shipinvoiceProductDefault')) {
    /**
     * @return mixed[]
     */
    function shipinvoiceProductDefault()
    {
        global $DB;

        return shipinvoiceProductVirtual($DB->modelDefault(
            SHIPINVOICE_PRODUCT_TABLENAME,
            SHIPINVOICE_PRODUCT_PRIMARYKEY
        ));
    }
}
if (!function_exists('shipinvoiceProductDelete')) {
    /**
     * @param int $id
     *
     * @return int
     */
    function shipinvoiceProductDelete($id)
    {
        global $DB;

        return $DB->modelDelete(
            $id,
            SHIPINVOICE_PRODUCT_TABLENAME,
            SHIPINVOICE_PRODUCT_PRIMARYKEY
        );
    }
}
if (!function_exists('shipinvoiceProductExists')) {
    /**
     * @param int $id
     *
     * @return bool
     */
    function shipinvoiceProductExists($id)
    {
        global $DB;

        return $DB->modelExists(
            $id,
            SHIPINVOICE_PRODUCT_TABLENAME,
            SHIPINVOICE_PRODUCT_PRIMARYKEY
        );
    }
}
if (!function_exists('shipinvoiceProductFromDatabase')) {
    /**
     * @param int $id
     *
     * @return mixed[]|null
     */
    function shipinvoiceProductFromDatabase($id)
    {
        global $DB;

        if ($row = $DB->modelLoad(
            $id,
            SHIPINVOICE_PRODUCT_TABLENAME,
            SHIPINVOICE_PRODUCT_PRIMARYKEY
        )) {
            return shipinvoiceProductVirtual($row);
        }

        return null;
    }
}
if (!function_exists('shipinvoiceProductGetValue')) {
    /**
     * @param int    $id
     * @param string $field
     *
     * @return mixed
     */
    function shipinvoiceProductGetValue($id, $field)
    {
        global $DB;

        if (array_key_exists($field, $DB->table_columns(
            SHIPINVOICE_PRODUCT_TABLENAME
        ))) {
            return $DB->modelGet(
                $id,
                $field,
                SHIPINVOICE_PRODUCT_TABLENAME,
                SHIPINVOICE_PRODUCT_PRIMARYKEY
            );
        }

        if ($model = shipinvoiceProductFromDatabase($id)) {
            return (array_key_exists($field, $model)) ? $model[$field] : null;
        }

        return null;
    }
}
if (!function_exists('shipinvoiceProductHasValue')) {
    /**
     * @param int    $id
     * @param string $field
     * @param mixed  $value
     *
     * @return bool
     */
    function shipinvoiceProductHasValue($id, $field, $value)
    {
        return shipinvoiceProductGetValue($id, $field) == $value;
    }
}
if (!function_exists('shipinvoiceProductQuery')) {
    /**
     * @todo
     *
     * @param mixed[] $eq
     * @param mixed[] $lt
     * @param mixed[] $gt
     *
     * @return string
     */
    function shipinvoiceProductQuery($eq = array(), $lt = array(), $gt = array())
    {
        global $DB;

        $whereAND = array();

        return (count($whereAND)) ? implode(" AND ", $whereAND) : "1";
    }
}
if (!function_exists('shipinvoiceProductSearch')) {
    /**
     * @param string|null $where
     * @param string|null $order_by
     *
     * @return int[]
     */
    function shipinvoiceProductSearch($where = null, $order_by = null)
    {
        global $DB;

        // Legacy accepts criteria as first parameter
        if (is_array($where)) {
            $where = shipinvoiceProductQuery($where);
        }

        return $DB->query_list(sprintf(
            "SELECT `%s` FROM %s%s%s",
            SHIPINVOICE_PRODUCT_PRIMARYKEY,
            SHIPINVOICE_PRODUCT_TABLENAME,
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
if (!function_exists('shipinvoiceProductSetValue')) {
    /**
     * @param int    $id
     * @param string $field
     * @param mixed  $value
     *
     * @return int
     */
    function shipinvoiceProductSetValue($id, $field, $value)
    {
        global $DB;

        return $DB->modelSet(
            $id,
            $field,
            $value,
            SHIPINVOICE_PRODUCT_TABLENAME,
            SHIPINVOICE_PRODUCT_PRIMARYKEY
        );
    }
}
if (!function_exists('shipinvoiceProductSort')) {
    /**
     * @param string|null $where
     * @param string|null $order_by
     *
     * @return int[]
     */
    function shipinvoiceProductSort($where = null, $order_by = null)
    {
        // Legacy accepts list of id's as first parameter
        if (is_array($where)) {
            $where = shipinvoiceProductQuery(array(
                SHIPINVOICE_PRODUCT_PRIMARYKEY => $where,
            ));
        }

        return shipinvoiceProductSearch($where, $order_by);
    }
}
if (!function_exists('shipinvoiceProductToDatabase')) {
    /**
     * @param mixed[] $item
     *
     * @return int
     */
    function shipinvoiceProductToDatabase(&$item)
    {
        global $DB;

        return $DB->modelSave(
            $item,
            SHIPINVOICE_PRODUCT_TABLENAME,
            SHIPINVOICE_PRODUCT_PRIMARYKEY
        );
    }
}
if (!function_exists('shipinvoiceProductVirtual')) {
    /**
     * @param mixed[] $row
     *
     * @return mixed[]
     */
    function shipinvoiceProductVirtual($row)
    {
        moduleLoad('product');
        $productData = productFromDatabase($row['product_id']);
        $row['is_harmonised_code'] = $productData['is_harmonised_code'];
        $row['is_origin_country'] = $productData['is_origin_country'];
        $row['is_product_materials'] = $productData['is_product_materials'];
        $row['is_product_usage'] = $productData['is_product_usage'];
        $row['harmonised_code'] = $productData['harmonised_code'];
        $row['origin_country'] = $productData['origin_country'];
        $row['product_usage'] = $productData['product_usage'];
        $row['product_materials'] = $productData['product_materials'];
        return $row;
    }
}

if (!function_exists('shipinvoiceProductsDelete')) {
    /**
     * @param int $id
     *
     * @return int
     */
    function shipinvoiceProductsDelete($id)
    {
        global $DB;

        return $DB->childrenDelete(
            $id,
            SHIPINVOICE_PRODUCT_TABLENAME,
            SHIPINVOICE_PRODUCT_SECONDARYKEY
        );
    }
}
if (!function_exists('shipinvoiceProductsFromDatabase')) {
    /**
     * @param int $id
     *
     * @return mixed[]|null
     */
    function shipinvoiceProductsFromDatabase($id)
    {
        global $DB;

          $result = array();
        if ($query = $DB->query(sprintf(
            "SELECT * FROM %s WHERE `%s` = %d",
            SHIPINVOICE_PRODUCT_TABLENAME,
            SHIPINVOICE_PRODUCT_SECONDARYKEY,
            $id
        ))) {
            while ($row = $DB->fetch_assoc($query)) {
                $result[] = shipinvoiceProductVirtual($row);
            }
            $DB->free_result($query);
        }

     /*   foreach ($DB->childrenLoad(
            $id,
            SHIPINVOICE_PRODUCT_TABLENAME,
            SHIPINVOICE_PRODUCT_PRIMARYKEY,
            SHIPINVOICE_PRODUCT_SECONDARYKEY
        ) as $k => $v) {
            $result[$k] = shipinvoiceProductVirtual($v);
        }*/

        return $result;
    }
}
if (!function_exists('shipinvoiceProductsToDatabase')) {
    /**
     * @param int       $id
     * @param mixed[][] $items
     *
     * @return void
     */
    /*
    function shipinvoiceProductsToDatabase($id, &$items)
    {
        global $DB;

        $DB->childrenSave(
            $id,
            $items,
            SHIPINVOICE_PRODUCT_TABLENAME,
            SHIPINVOICE_PRODUCT_PRIMARYKEY,
            SHIPINVOICE_PRODUCT_SECONDARYKEY
        );
    }
    */
}

if (!function_exists("shipInvoiceProductsFromDatabase")) {
    function shipInvoiceProductsFromDatabase($id)
    {
        global $DB;
        $result = array();
        $query  = $DB->query("SELECT * FROM `highwaygroup`.`shipinvoice_products` WHERE `sinvoice_id` = " . intval($id));
        while ($row = $DB->fetch_assoc($query)) {
            $result[] = $row;
        }

        $DB->free_result($query);
        return $result;
    }
}
if (!function_exists("shipInvoiceProductsFromPost")) {
    function shipInvoiceProductsFromPost($posted)
    {
        moduleLoad('invoice'); //invoiceExists

        global $DB;

        $fields = array();
        $query  = $DB->query("SHOW COLUMNS FROM `highwaygroup`.`shipinvoice_products`");
        while ($row = $DB->fetch_array($query, MYSQLI_NUM)) {
            if ('auto_increment' != $row[5]) {
                $fields[$row[0]] = $row[4];
            }
        }

        $DB->free_result($query);

        $fields['sinvoice_id'] = $posted['sinvoice_id'];
        if (invoiceExists($posted['invoice_id'])) {
            $fields['sinvoice_id'] = intval($posted['invoice_id']);
        }

        $result = array();
        if (is_array($posted) && array_key_exists('product_id', $posted)) {
            foreach ($posted['product_id'] as $k1 => $v1) {
                $product = $fields;
                foreach ($fields as $k2 => $v2) {
                    if (is_array($posted[$k2]) && array_key_exists($k1, $posted[$k2])) {
                        $product[$k2] = $posted[$k2][$k1];
                    }
                }

                $result[] = $product;
            }
        }

        return $result;
    }
}
if (!function_exists("shipinvoiceProductsToDatabase")) {
    function shipinvoiceProductsToDatabase($sinvoice_id, $products)
    {
        global $DB;
        $sinvoice_id = intval($sinvoice_id);
        if (0 == $sinvoice_id) {
            return;
        }

        $sql = "DELETE FROM `highwaygroup`.`shipinvoice_products` WHERE sinvoice_id=" . $sinvoice_id;
        $DB->query($sql);
        $fields = array();
        $query  = $DB->query("SHOW COLUMNS FROM `highwaygroup`.`shipinvoice_products`");
        while ($row = $DB->fetch_array($query)) {
            if ('auto_increment' != $row[5]) {
                $fields[$row[0]] = $row[4];
            }
        }

        foreach ($products as $product) {
            $insert_vals = array();
            if ($sinvoice_id > 0) {
                $product['sinvoice_id'] = $sinvoice_id;
            }

            foreach ($product as $key => $value) {
                if (array_key_exists($key, $fields)) {
                    $insert_vals[$key] = addslashes($value);
                }
            }

            $sql = "INSERT INTO `highwaygroup`.`shipinvoice_products`(`" . implode('`, `', array_keys($insert_vals)) . "`) VALUES('" . implode("', '", $insert_vals) . "')";
            $DB->query($sql);
        }
    }
}
