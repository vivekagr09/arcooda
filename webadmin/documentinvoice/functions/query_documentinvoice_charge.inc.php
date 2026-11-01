<?php
if (!defined('documentinvoice_CHARGE_TABLENAME')) {
    define('documentinvoice_CHARGE_TABLENAME', '`highwaygroup`.`documentinvoice_charges`');
}
/*
if (!defined('documentinvoice_CHARGE_PRIMARYKEY')) {
    define('documentinvoice_CHARGE_PRIMARYKEY', 'id');
}
*/
if (!defined('documentinvoice_CHARGE_SECONDARYKEY')) {
    define('documentinvoice_CHARGE_SECONDARYKEY', 'dinvoice_id');
}

if (!function_exists('documentinvoiceChargeDefault')) {
    /**
     * @return mixed[]
     */
    /*
    function documentinvoiceChargeDefault()
    {
        global $DB;

        return documentinvoiceChargeVirtual($DB->modelDefault(
            documentinvoice_CHARGE_TABLENAME,
            documentinvoice_CHARGE_PRIMARYKEY
        ));
    }
    */
}
if (!function_exists('documentinvoiceChargeDelete')) {
    /**
     * @param int $id
     *
     * @return int
     */
    /*
    function documentinvoiceChargeDelete($id)
    {
        global $DB;

        return $DB->modelDelete(
            $id,
            documentinvoice_CHARGE_TABLENAME,
            documentinvoice_CHARGE_PRIMARYKEY
        );
    }
    */
}
if (!function_exists('documentinvoiceChargeExists')) {
    /**
     * @param int $id
     *
     * @return bool
     */
    /*
    function documentinvoiceChargeExists($id)
    {
        global $DB;

        return $DB->modelExists(
            $id,
            documentinvoice_CHARGE_TABLENAME,
            documentinvoice_CHARGE_PRIMARYKEY
        );
    }
    */
}
if (!function_exists('documentinvoiceChargeFromDatabase')) {
    /**
     * @param int $id
     *
     * @return mixed[]|null
     */
    /*
    function documentinvoiceChargeFromDatabase($id)
    {
        global $DB;

        if ($row = $DB->modelLoad(
            $id,
            documentinvoice_CHARGE_TABLENAME,
            documentinvoice_CHARGE_PRIMARYKEY
        )) {
            return documentinvoiceChargeVirtual($row);
        }

        return null;
    }
    */
}
if (!function_exists('documentinvoiceChargeGetValue')) {
    /**
     * @param int    $id
     * @param string $field
     *
     * @return mixed
     */
    /*
    function documentinvoiceChargeGetValue($id, $field)
    {
        global $DB;

        if (array_key_exists($field, $DB->table_columns(
            documentinvoice_CHARGE_TABLENAME
        ))) {
            return $DB->modelGet(
                $id,
                $field,
                documentinvoice_CHARGE_TABLENAME,
                documentinvoice_CHARGE_PRIMARYKEY
            );
        }

        if ($model = documentinvoiceChargeFromDatabase($id)) {
            return (array_key_exists($field, $model)) ? $model[$field] : null;
        }

        return null;
    }
    */
}
if (!function_exists('documentinvoiceChargeHasValue')) {
    /**
     * @param int    $id
     * @param string $field
     * @param mixed  $value
     *
     * @return bool
     */
    /*
    function documentinvoiceChargeHasValue($id, $field, $value)
    {
        return documentinvoiceChargeGetValue($id, $field) == $value;
    }
    */
}
if (!function_exists('documentinvoiceChargeQuery')) {
    /**
     * @todo
     *
     * @param mixed[] $eq
     * @param mixed[] $lt
     * @param mixed[] $gt
     *
     * @return string
     */
    function documentinvoiceChargeQuery($eq = array(), $lt = array(), $gt = array())
    {
        global $DB;

        $whereAND = array();

        return (count($whereAND)) ? implode(" AND ", $whereAND) : "1";
    }
}
if (!function_exists('documentinvoiceChargeSearch')) {
    /**
     * @param string|null $where
     * @param string|null $order_by
     *
     * @return int[]
     */
    /*
    function documentinvoiceChargeSearch($where = null, $order_by = null)
    {
        global $DB;

        // Legacy accepts criteria as first parameter
        if (is_array($where)) {
            $where = documentinvoiceChargeQuery($where);
        }

        return $DB->query_list(sprintf(
            "SELECT `%s` FROM %s%s%s",
            documentinvoice_CHARGE_PRIMARYKEY,
            documentinvoice_CHARGE_TABLENAME,
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
    */
}
if (!function_exists('documentinvoiceChargeSetValue')) {
    /**
     * @param int    $id
     * @param string $field
     * @param mixed  $value
     *
     * @return int
     */
    /*
    function documentinvoiceChargeSetValue($id, $field, $value)
    {
        global $DB;

        return $DB->modelSet(
            $id,
            $field,
            $value,
            documentinvoice_CHARGE_TABLENAME,
            documentinvoice_CHARGE_PRIMARYKEY
        );
    }
    */
}
if (!function_exists('documentinvoiceChargeSort')) {
    /**
     * @param string|null $where
     * @param string|null $order_by
     *
     * @return int[]
     */
    /*
    function documentinvoiceChargeSort($where = null, $order_by = null)
    {
        // Legacy accepts list of id's as first parameter
        if (is_array($where)) {
            $where = documentinvoiceChargeQuery(array(
                documentinvoice_CHARGE_PRIMARYKEY => $where,
            ));
        }

        return documentinvoiceChargeSearch($where, $order_by);
    }
    */
}
if (!function_exists('documentinvoiceChargeToDatabase')) {
    /**
     * @param mixed[] $item
     *
     * @return int
     */
    /*
    function documentinvoiceChargeToDatabase(&$item)
    {
        global $DB;

        return $DB->modelSave(
            $item,
            documentinvoice_CHARGE_TABLENAME,
            documentinvoice_CHARGE_PRIMARYKEY
        );
    }
    */
}
if (!function_exists('documentinvoiceChargeVirtual')) {
    /**
     * @param mixed[] $row
     *
     * @return mixed[]
     */
    function documentinvoiceChargeVirtual($row)
    {
        return $row;
    }
}

if (!function_exists('documentinvoiceChargesDelete')) {
    /**
     * @param int $id
     *
     * @return int
     */
    function documentinvoiceChargesDelete($id)
    {
        global $DB;

        return $DB->childrenDelete(
            $id,
            documentinvoice_CHARGE_TABLENAME,
            documentinvoice_CHARGE_SECONDARYKEY
        );
    }
}
if (!function_exists('documentinvoiceChargesFromDatabase')) {
    /**
     * @param int $id
     *
     * @return mixed[]|null
     */
    /*
    function documentinvoiceChargesFromDatabase($id)
    {
        global $DB;

        $result = array();
        foreach ($DB->childrenLoad(
            $id,
            documentinvoice_CHARGE_TABLENAME,
            documentinvoice_CHARGE_PRIMARYKEY,
            documentinvoice_CHARGE_SECONDARYKEY
        ) as $k => $v) {
            $result[$k] = documentinvoiceChargeVirtual($v);
        }

        return $result;
    }
    */
}
if (!function_exists('documentinvoiceChargesToDatabase')) {
    /**
     * @param int       $id
     * @param mixed[][] $items
     *
     * @return void
     */
    /*
    function documentinvoiceChargesToDatabase($id, &$items)
    {
        global $DB;

        $DB->childrenSave(
            $id,
            $items,
            documentinvoice_CHARGE_TABLENAME,
            documentinvoice_CHARGE_PRIMARYKEY,
            documentinvoice_CHARGE_SECONDARYKEY
        );
    }
    */
}

if (!function_exists("documentinvoiceChargesFromDatabase")) {
    function documentinvoiceChargesFromDatabase($id)
    {
        global $DB;
        $result = array();
        $query  = $DB->query("SELECT * FROM `highwaygroup`.`documentinvoice_charges` WHERE `dinvoice_id` = " . intval($id));
        while ($row = $DB->fetch_assoc($query)) {
            $result[] = $row;
        }

        $DB->free_result($query);
        return $result;
    }
}
if (!function_exists("documentinvoicechargesFromPost")) {
    function documentinvoicechargesFromPost($posted)
    {
        moduleLoad('invoice'); //invoiceExists

        global $DB;

        $fields = array();
        $query  = $DB->query("SHOW COLUMNS FROM `highwaygroup`.`documentinvoice_charges`");
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
        if (is_array($posted) && array_key_exists('charge_id', $posted)) {
            foreach ($posted['charge_id'] as $k1 => $v1) {
                $charge = $fields;
                foreach ($fields as $k2 => $v2) {
                    if (is_array($posted[$k2]) && array_key_exists($k1, $posted[$k2])) {
                        $charge[$k2] = $posted[$k2][$k1];
                    }
                }

                $result[] = $charge;
            }
        }

        return $result;
    }
}
if (!function_exists("documentinvoiceChargesToDatabase")) {
    function documentinvoiceChargesToDatabase($id, $charges)
    {
        global $DB;

        $fields = array();
        $query  = $DB->query("SHOW COLUMNS FROM `highwaygroup`.`documentinvoice_charges`");
        while ($row = $DB->fetch_array($query, MYSQLI_NUM)) {
            if ('auto_increment' != $row[5]) {
                $fields[$row[0]] = $row[4];
            }
        }

        $DB->free_result($query);

        $DB->query("DELETE FROM `highwaygroup`.`documentinvoice_charges` WHERE `dinvoice_id` = " . intval($id));

        foreach ($charges as $charge) {
            $insert_vals = array();
            if ($id > 0) {
                $charge['dinvoice_id'] = $id;
            }

            foreach ($charge as $k => $v) {
                if (array_key_exists($k, $fields)) {
                    $insert_vals[$k] = formatSQLString($v);
                }
            }

            $DB->query("INSERT INTO `highwaygroup`.`documentinvoice_charges` (`" . implode('`, `', array_keys($insert_vals)) . "`) VALUES (" . implode(", ", $insert_vals) . ")");
        }
    }
}
