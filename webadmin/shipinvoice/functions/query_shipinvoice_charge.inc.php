<?php
if (!defined('SHIPINVOICE_CHARGE_TABLENAME')) {
    define('SHIPINVOICE_CHARGE_TABLENAME', '`highwaygroup`.`shipinvoice_charges`');
}
/*
if (!defined('SHIPINVOICE_CHARGE_PRIMARYKEY')) {
    define('SHIPINVOICE_CHARGE_PRIMARYKEY', 'id');
}
*/
if (!defined('SHIPINVOICE_CHARGE_SECONDARYKEY')) {
    define('SHIPINVOICE_CHARGE_SECONDARYKEY', 'sinvoice_id');
}

if (!function_exists('shipinvoiceChargeDefault')) {
    /**
     * @return mixed[]
     */
    /*
    function shipinvoiceChargeDefault()
    {
        global $DB;

        return shipinvoiceChargeVirtual($DB->modelDefault(
            SHIPINVOICE_CHARGE_TABLENAME,
            SHIPINVOICE_CHARGE_PRIMARYKEY
        ));
    }
    */
}
if (!function_exists('shipinvoiceChargeDelete')) {
    /**
     * @param int $id
     *
     * @return int
     */
    /*
    function shipinvoiceChargeDelete($id)
    {
        global $DB;

        return $DB->modelDelete(
            $id,
            SHIPINVOICE_CHARGE_TABLENAME,
            SHIPINVOICE_CHARGE_PRIMARYKEY
        );
    }
    */
}
if (!function_exists('shipinvoiceChargeExists')) {
    /**
     * @param int $id
     *
     * @return bool
     */
    /*
    function shipinvoiceChargeExists($id)
    {
        global $DB;

        return $DB->modelExists(
            $id,
            SHIPINVOICE_CHARGE_TABLENAME,
            SHIPINVOICE_CHARGE_PRIMARYKEY
        );
    }
    */
}
if (!function_exists('shipinvoiceChargeFromDatabase')) {
    /**
     * @param int $id
     *
     * @return mixed[]|null
     */
    /*
    function shipinvoiceChargeFromDatabase($id)
    {
        global $DB;

        if ($row = $DB->modelLoad(
            $id,
            SHIPINVOICE_CHARGE_TABLENAME,
            SHIPINVOICE_CHARGE_PRIMARYKEY
        )) {
            return shipinvoiceChargeVirtual($row);
        }

        return null;
    }
    */
}
if (!function_exists('shipinvoiceChargeGetValue')) {
    /**
     * @param int    $id
     * @param string $field
     *
     * @return mixed
     */
    /*
    function shipinvoiceChargeGetValue($id, $field)
    {
        global $DB;

        if (array_key_exists($field, $DB->table_columns(
            SHIPINVOICE_CHARGE_TABLENAME
        ))) {
            return $DB->modelGet(
                $id,
                $field,
                SHIPINVOICE_CHARGE_TABLENAME,
                SHIPINVOICE_CHARGE_PRIMARYKEY
            );
        }

        if ($model = shipinvoiceChargeFromDatabase($id)) {
            return (array_key_exists($field, $model)) ? $model[$field] : null;
        }

        return null;
    }
    */
}
if (!function_exists('shipinvoiceChargeHasValue')) {
    /**
     * @param int    $id
     * @param string $field
     * @param mixed  $value
     *
     * @return bool
     */
    /*
    function shipinvoiceChargeHasValue($id, $field, $value)
    {
        return shipinvoiceChargeGetValue($id, $field) == $value;
    }
    */
}
if (!function_exists('shipinvoiceChargeQuery')) {
    /**
     * @todo
     *
     * @param mixed[] $eq
     * @param mixed[] $lt
     * @param mixed[] $gt
     *
     * @return string
     */
    function shipinvoiceChargeQuery($eq = array(), $lt = array(), $gt = array())
    {
        global $DB;

        $whereAND = array();

        return (count($whereAND)) ? implode(" AND ", $whereAND) : "1";
    }
}
if (!function_exists('shipinvoiceChargeSearch')) {
    /**
     * @param string|null $where
     * @param string|null $order_by
     *
     * @return int[]
     */
    /*
    function shipinvoiceChargeSearch($where = null, $order_by = null)
    {
        global $DB;

        // Legacy accepts criteria as first parameter
        if (is_array($where)) {
            $where = shipinvoiceChargeQuery($where);
        }

        return $DB->query_list(sprintf(
            "SELECT `%s` FROM %s%s%s",
            SHIPINVOICE_CHARGE_PRIMARYKEY,
            SHIPINVOICE_CHARGE_TABLENAME,
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
if (!function_exists('shipinvoiceChargeSetValue')) {
    /**
     * @param int    $id
     * @param string $field
     * @param mixed  $value
     *
     * @return int
     */
    /*
    function shipinvoiceChargeSetValue($id, $field, $value)
    {
        global $DB;

        return $DB->modelSet(
            $id,
            $field,
            $value,
            SHIPINVOICE_CHARGE_TABLENAME,
            SHIPINVOICE_CHARGE_PRIMARYKEY
        );
    }
    */
}
if (!function_exists('shipinvoiceChargeSort')) {
    /**
     * @param string|null $where
     * @param string|null $order_by
     *
     * @return int[]
     */
    /*
    function shipinvoiceChargeSort($where = null, $order_by = null)
    {
        // Legacy accepts list of id's as first parameter
        if (is_array($where)) {
            $where = shipinvoiceChargeQuery(array(
                SHIPINVOICE_CHARGE_PRIMARYKEY => $where,
            ));
        }

        return shipinvoiceChargeSearch($where, $order_by);
    }
    */
}
if (!function_exists('shipinvoiceChargeToDatabase')) {
    /**
     * @param mixed[] $item
     *
     * @return int
     */
    /*
    function shipinvoiceChargeToDatabase(&$item)
    {
        global $DB;

        return $DB->modelSave(
            $item,
            SHIPINVOICE_CHARGE_TABLENAME,
            SHIPINVOICE_CHARGE_PRIMARYKEY
        );
    }
    */
}
if (!function_exists('shipinvoiceChargeVirtual')) {
    /**
     * @param mixed[] $row
     *
     * @return mixed[]
     */
    function shipinvoiceChargeVirtual($row)
    {
        return $row;
    }
}

if (!function_exists('shipinvoiceChargesDelete')) {
    /**
     * @param int $id
     *
     * @return int
     */
    function shipinvoiceChargesDelete($id)
    {
        global $DB;

        return $DB->childrenDelete(
            $id,
            SHIPINVOICE_CHARGE_TABLENAME,
            SHIPINVOICE_CHARGE_SECONDARYKEY
        );
    }
}
if (!function_exists('shipinvoiceChargesFromDatabase')) {
    /**
     * @param int $id
     *
     * @return mixed[]|null
     */
    /*
    function shipinvoiceChargesFromDatabase($id)
    {
        global $DB;

        $result = array();
        foreach ($DB->childrenLoad(
            $id,
            SHIPINVOICE_CHARGE_TABLENAME,
            SHIPINVOICE_CHARGE_PRIMARYKEY,
            SHIPINVOICE_CHARGE_SECONDARYKEY
        ) as $k => $v) {
            $result[$k] = shipinvoiceChargeVirtual($v);
        }

        return $result;
    }
    */
}
if (!function_exists('shipinvoiceChargesToDatabase')) {
    /**
     * @param int       $id
     * @param mixed[][] $items
     *
     * @return void
     */
    /*
    function shipinvoiceChargesToDatabase($id, &$items)
    {
        global $DB;

        $DB->childrenSave(
            $id,
            $items,
            SHIPINVOICE_CHARGE_TABLENAME,
            SHIPINVOICE_CHARGE_PRIMARYKEY,
            SHIPINVOICE_CHARGE_SECONDARYKEY
        );
    }
    */
}

if (!function_exists("shipinvoiceChargesFromDatabase")) {
    function shipinvoiceChargesFromDatabase($id)
    {
        global $DB;
        $result = array();
        $query  = $DB->query("SELECT * FROM `highwaygroup`.`shipinvoice_charges` WHERE `sinvoice_id` = " . intval($id));
        while ($row = $DB->fetch_assoc($query)) {
            $result[] = $row;
        }

        $DB->free_result($query);
        return $result;
    }
}
if (!function_exists("shipinvoicechargesFromPost")) {
    function shipinvoicechargesFromPost($posted)
    {
        moduleLoad('invoice'); //invoiceExists

        global $DB;

        $fields = array();
        $query  = $DB->query("SHOW COLUMNS FROM `highwaygroup`.`shipinvoice_charges`");
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
if (!function_exists("shipinvoiceChargesToDatabase")) {
    function shipinvoiceChargesToDatabase($id, $charges)
    {
        global $DB;

        $fields = array();
        $query  = $DB->query("SHOW COLUMNS FROM `highwaygroup`.`shipinvoice_charges`");
        while ($row = $DB->fetch_array($query, MYSQLI_NUM)) {
            if ('auto_increment' != $row[5]) {
                $fields[$row[0]] = $row[4];
            }
        }

        $DB->free_result($query);

        $DB->query("DELETE FROM `highwaygroup`.`shipinvoice_charges` WHERE `sinvoice_id` = " . intval($id));

        foreach ($charges as $charge) {
            $insert_vals = array();
            if ($id > 0) {
                $charge['sinvoice_id'] = $id;
            }

            foreach ($charge as $k => $v) {
                if (array_key_exists($k, $fields)) {
                    $insert_vals[$k] = formatSQLString($v);
                }
            }

            $DB->query("INSERT INTO `highwaygroup`.`shipinvoice_charges` (`" . implode('`, `', array_keys($insert_vals)) . "`) VALUES (" . implode(", ", $insert_vals) . ")");
        }
    }
}
