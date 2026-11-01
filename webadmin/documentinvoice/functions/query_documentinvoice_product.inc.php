<?php
if (!defined('documentinvoice_PRODUCT_TABLENAME')) {
    define('documentinvoice_PRODUCT_TABLENAME', '`highwaygroup`.`documentinvoice_products`');
}
if (!defined('documentinvoice_PRODUCT_PRIMARYKEY')) {
    define('documentinvoice_PRODUCT_PRIMARYKEY', 'id');
}
if (!defined('documentinvoice_PRODUCT_SECONDARYKEY')) {
    define('documentinvoice_PRODUCT_SECONDARYKEY', 'dinvoice_id');
}

if (!function_exists('documentinvoiceProductDefault')) {
    /**
     * @return mixed[]
     */
    function documentinvoiceProductDefault()
    {
        global $DB;

        return documentinvoiceProductVirtual($DB->modelDefault(
            documentinvoice_PRODUCT_TABLENAME,
            documentinvoice_PRODUCT_PRIMARYKEY
        ));
    }
}
if (!function_exists('documentinvoiceProductDelete')) {
    /**
     * @param int $id
     *
     * @return int
     */
    function documentinvoiceProductDelete($id)
    {
        global $DB;

        return $DB->modelDelete(
            $id,
            documentinvoice_PRODUCT_TABLENAME,
            documentinvoice_PRODUCT_PRIMARYKEY
        );
    }
}
if (!function_exists('documentinvoiceProductExists')) {
    /**
     * @param int $id
     *
     * @return bool
     */
    function documentinvoiceProductExists($id)
    {
        global $DB;

        return $DB->modelExists(
            $id,
            documentinvoice_PRODUCT_TABLENAME,
            documentinvoice_PRODUCT_PRIMARYKEY
        );
    }
}
if (!function_exists('documentinvoiceProductFromDatabase')) {
    /**
     * @param int $id
     *
     * @return mixed[]|null
     */
    function documentinvoiceProductFromDatabase($id)
    {
        global $DB;

        if ($row = $DB->modelLoad(
            $id,
            documentinvoice_PRODUCT_TABLENAME,
            documentinvoice_PRODUCT_PRIMARYKEY
        )) {
            return documentinvoiceProductVirtual($row);
        }

        return null;
    }
}
if (!function_exists('documentinvoiceProductGetValue')) {
    /**
     * @param int    $id
     * @param string $field
     *
     * @return mixed
     */
    function documentinvoiceProductGetValue($id, $field)
    {
        global $DB;

        if (array_key_exists($field, $DB->table_columns(
            documentinvoice_PRODUCT_TABLENAME
        ))) {
            return $DB->modelGet(
                $id,
                $field,
                documentinvoice_PRODUCT_TABLENAME,
                documentinvoice_PRODUCT_PRIMARYKEY
            );
        }

        if ($model = documentinvoiceProductFromDatabase($id)) {
            return (array_key_exists($field, $model)) ? $model[$field] : null;
        }

        return null;
    }
}
if (!function_exists('documentinvoiceProductHasValue')) {
    /**
     * @param int    $id
     * @param string $field
     * @param mixed  $value
     *
     * @return bool
     */
    function documentinvoiceProductHasValue($id, $field, $value)
    {
        return documentinvoiceProductGetValue($id, $field) == $value;
    }
}
if (!function_exists('documentinvoiceProductQuery')) {
    /**
     * @todo
     *
     * @param mixed[] $eq
     * @param mixed[] $lt
     * @param mixed[] $gt
     *
     * @return string
     */
    function documentinvoiceProductQuery($eq = array(), $lt = array(), $gt = array())
    {
        global $DB;

        $whereAND = array();

        return (count($whereAND)) ? implode(" AND ", $whereAND) : "1";
    }
}
if (!function_exists('documentinvoiceProductSearch')) {
    /**
     * @param string|null $where
     * @param string|null $order_by
     *
     * @return int[]
     */
    function documentinvoiceProductSearch($where = null, $order_by = null)
    {
        global $DB;

        // Legacy accepts criteria as first parameter
        if (is_array($where)) {
            $where = documentinvoiceProductQuery($where);
        }

        return $DB->query_list(sprintf(
            "SELECT `%s` FROM %s%s%s",
            documentinvoice_PRODUCT_PRIMARYKEY,
            documentinvoice_PRODUCT_TABLENAME,
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
if (!function_exists('documentinvoiceProductSetValue')) {
    /**
     * @param int    $id
     * @param string $field
     * @param mixed  $value
     *
     * @return int
     */
    function documentinvoiceProductSetValue($id, $field, $value)
    {
        global $DB;

        return $DB->modelSet(
            $id,
            $field,
            $value,
            documentinvoice_PRODUCT_TABLENAME,
            documentinvoice_PRODUCT_PRIMARYKEY
        );
    }
}
if (!function_exists('documentinvoiceProductSort')) {
    /**
     * @param string|null $where
     * @param string|null $order_by
     *
     * @return int[]
     */
    function documentinvoiceProductSort($where = null, $order_by = null)
    {
        // Legacy accepts list of id's as first parameter
        if (is_array($where)) {
            $where = documentinvoiceProductQuery(array(
                documentinvoice_PRODUCT_PRIMARYKEY => $where,
            ));
        }

        return documentinvoiceProductSearch($where, $order_by);
    }
}
if (!function_exists('documentinvoiceProductToDatabase')) {
    /**
     * @param mixed[] $item
     *
     * @return int
     */
    function documentinvoiceProductToDatabase(&$item)
    {
        global $DB;

        return $DB->modelSave(
            $item,
            documentinvoice_PRODUCT_TABLENAME,
            documentinvoice_PRODUCT_PRIMARYKEY
        );
    }
}
if (!function_exists('documentinvoiceProductVirtual')) {
    /**
     * @param mixed[] $row
     *
     * @return mixed[]
     */
    function documentinvoiceProductVirtual($row)
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

if (!function_exists('documentinvoiceProductsDelete')) {
    /**
     * @param int $id
     *
     * @return int
     */
    function documentinvoiceProductsDelete($id)
    {
        global $DB;

        return $DB->childrenDelete(
            $id,
            documentinvoice_PRODUCT_TABLENAME,
            documentinvoice_PRODUCT_SECONDARYKEY
        );
    }
}
if (!function_exists('documentinvoiceProductsFromDatabase')) {
    /**
     * @param int $id
     *
     * @return mixed[]|null
     */
    function documentinvoiceProductsFromDatabase($id)
    {
        global $DB;

          $result = array();
        if ($query = $DB->query(sprintf(
            "SELECT * FROM %s WHERE `%s` = %d",
            documentinvoice_PRODUCT_TABLENAME,
            documentinvoice_PRODUCT_SECONDARYKEY,
            $id
        ))) {
            while ($row = $DB->fetch_assoc($query)) {
                $result[] = documentinvoiceProductVirtual($row);
            }
            $DB->free_result($query);
        }

     /*   foreach ($DB->childrenLoad(
            $id,
            documentinvoice_PRODUCT_TABLENAME,
            documentinvoice_PRODUCT_PRIMARYKEY,
            documentinvoice_PRODUCT_SECONDARYKEY
        ) as $k => $v) {
            $result[$k] = documentinvoiceProductVirtual($v);
        }*/

        return $result;
    }
}
if (!function_exists('documentinvoiceProductsToDatabase')) {
    /**
     * @param int       $id
     * @param mixed[][] $items
     *
     * @return void
     */
    /*
    function documentinvoiceProductsToDatabase($id, &$items)
    {
        global $DB;

        $DB->childrenSave(
            $id,
            $items,
            documentinvoice_PRODUCT_TABLENAME,
            documentinvoice_PRODUCT_PRIMARYKEY,
            documentinvoice_PRODUCT_SECONDARYKEY
        );
    }
    */
}

if (!function_exists("documentinvoiceProductsFromDatabase")) {
    function documentinvoiceProductsFromDatabase($id)
    {
        global $DB;
        $result = array();
        $query  = $DB->query("SELECT * FROM `highwaygroup`.`documentinvoice_products` WHERE `dinvoice_id` = " . intval($id));
        while ($row = $DB->fetch_assoc($query)) {
            $result[] = $row;
        }

        $DB->free_result($query);
        return $result;
    }
}
if (!function_exists("documentinvoiceProductsFromPost")) {
    function documentinvoiceProductsFromPost($posted)
    {
        moduleLoad('invoice'); //invoiceExists

        global $DB;

        $fields = array();
        $query  = $DB->query("SHOW COLUMNS FROM `highwaygroup`.`documentinvoice_products`");
        while ($row = $DB->fetch_array($query, MYSQLI_NUM)) {
            if ('auto_increment' != $row[5]) {
                $fields[$row[0]] = $row[4];
            }
        }

        $DB->free_result($query);

        $fields['dinvoice_id'] = $posted['dinvoice_id'];
        if (invoiceExists($posted['invoice_id'])) {
            $fields['dinvoice_id'] = intval($posted['invoice_id']);
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
if (!function_exists("documentinvoiceProductsToDatabase")) {
    function documentinvoiceProductsToDatabase($dinvoice_id, $products)
    {
        global $DB;
        $dinvoice_id = intval($dinvoice_id);
        if (0 == $dinvoice_id) {
            return;
        }

        $sql = "DELETE FROM `highwaygroup`.`documentinvoice_products` WHERE dinvoice_id=" . $dinvoice_id;
        $DB->query($sql);
        $fields = array();
        $query  = $DB->query("SHOW COLUMNS FROM `highwaygroup`.`documentinvoice_products`");
        while ($row = $DB->fetch_array($query)) {
            if ('auto_increment' != $row[5]) {
                $fields[$row[0]] = $row[4];
            }
        }

        foreach ($products as $product) {
            $insert_vals = array();
            if ($dinvoice_id > 0) {
                $product['dinvoice_id'] = $dinvoice_id;
            }

            foreach ($product as $key => $value) {
                if (array_key_exists($key, $fields)) {
                    $insert_vals[$key] = addslashes($value);
                }
            }

            $sql = "INSERT INTO `highwaygroup`.`documentinvoice_products`(`" . implode('`, `', array_keys($insert_vals)) . "`) VALUES('" . implode("', '", $insert_vals) . "')";
            $DB->query($sql);
        }
    }
}
