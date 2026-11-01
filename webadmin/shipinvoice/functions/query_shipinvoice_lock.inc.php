<?php
if (!defined('SHIPINVOICE_LOCK_TABLENAME')) {
    define('SHIPINVOICE_LOCK_TABLENAME', '`highwaygroup`.`shipinvoice_locks`');
}
if (!defined('SHIPINVOICE_LOCK_PRIMARYKEY')) {
    define('SHIPINVOICE_LOCK_PRIMARYKEY', 'id');
}
if (!defined('SHIPINVOICE_LOCK_SECONDARYKEY')) {
    define('SHIPINVOICE_LOCK_SECONDARYKEY', 'sinvoice_id');
}

if (!function_exists('shipinvoiceLockDefault')) {
    /**
     * @return mixed[]
     */
    function shipinvoiceLockDefault()
    {
        global $DB;

        return shipinvoiceLockVirtual($DB->modelDefault(
            SHIPINVOICE_LOCK_TABLENAME,
            SHIPINVOICE_LOCK_PRIMARYKEY
        ));
    }
}
if (!function_exists('shipinvoiceLockDelete')) {
    /**
     * @param int $id
     *
     * @return int
     */
    function shipinvoiceLockDelete($id)
    {
        global $DB;

        return $DB->modelDelete(
            $id,
            SHIPINVOICE_LOCK_TABLENAME,
            SHIPINVOICE_LOCK_PRIMARYKEY
        );
    }
}
if (!function_exists('shipinvoiceLockExists')) {
    /**
     * @param int $id
     *
     * @return bool
     */
    function shipinvoiceLockExists($id)
    {
        global $DB;

        return $DB->modelExists(
            $id,
            SHIPINVOICE_LOCK_TABLENAME,
            SHIPINVOICE_LOCK_PRIMARYKEY
        );
    }
}
if (!function_exists('shipinvoiceLockFromDatabase')) {
    /**
     * @param int $id
     *
     * @return mixed[]|null
     */
    function shipinvoiceLockFromDatabase($id)
    {
        global $DB;

        if ($row = $DB->modelLoad(
            $id,
            SHIPINVOICE_LOCK_TABLENAME,
            SHIPINVOICE_LOCK_PRIMARYKEY
        )) {
            return shipinvoiceLockVirtual($row);
        }

        return null;
    }
}
if (!function_exists('shipinvoiceLockGetValue')) {
    /**
     * @param int    $id
     * @param string $field
     *
     * @return mixed
     */
    function shipinvoiceLockGetValue($id, $field)
    {
        global $DB;

        if (array_key_exists($field, $DB->table_columns(
            SHIPINVOICE_LOCK_TABLENAME
        ))) {
            return $DB->modelGet(
                $id,
                $field,
                SHIPINVOICE_LOCK_TABLENAME,
                SHIPINVOICE_LOCK_PRIMARYKEY
            );
        }

        if ($model = shipinvoiceLockFromDatabase($id)) {
            return (array_key_exists($field, $model)) ? $model[$field] : null;
        }

        return null;
    }
}
if (!function_exists('shipinvoiceLockHasValue')) {
    /**
     * @param int    $id
     * @param string $field
     * @param mixed  $value
     *
     * @return bool
     */
    function shipinvoiceLockHasValue($id, $field, $value)
    {
        return shipinvoiceLockGetValue($id, $field) == $value;
    }
}
if (!function_exists('shipinvoiceLockQuery')) {
    /**
     * @todo
     *
     * @param mixed[] $eq
     * @param mixed[] $lt
     * @param mixed[] $gt
     *
     * @return string
     */
    function shipinvoiceLockQuery($eq = array(), $lt = array(), $gt = array())
    {
        global $DB;

        $whereAND = array();

        return (count($whereAND)) ? implode(" AND ", $whereAND) : "1";
    }
}
if (!function_exists('shipinvoiceLockSearch')) {
    /**
     * @param string|null $where
     * @param string|null $order_by
     *
     * @return int[]
     */
    function shipinvoiceLockSearch($where = null, $order_by = null)
    {
        global $DB;

        // Legacy accepts criteria as first parameter
        if (is_array($where)) {
            $where = shipinvoiceLockQuery($where);
        }

        return $DB->query_list(sprintf(
            "SELECT `%s` FROM %s%s%s",
            SHIPINVOICE_LOCK_PRIMARYKEY,
            SHIPINVOICE_LOCK_TABLENAME,
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
if (!function_exists('shipinvoiceLockSetValue')) {
    /**
     * @param int    $id
     * @param string $field
     * @param mixed  $value
     *
     * @return int
     */
    function shipinvoiceLockSetValue($id, $field, $value)
    {
        global $DB;

        return $DB->modelSet(
            $id,
            $field,
            $value,
            SHIPINVOICE_LOCK_TABLENAME,
            SHIPINVOICE_LOCK_PRIMARYKEY
        );
    }
}
if (!function_exists('shipinvoiceLockSort')) {
    /**
     * @param string|null $where
     * @param string|null $order_by
     *
     * @return int[]
     */
    function shipinvoiceLockSort($where = null, $order_by = null)
    {
        // Legacy accepts list of id's as first parameter
        if (is_array($where)) {
            $where = shipinvoiceLockQuery(array(
                SHIPINVOICE_LOCK_PRIMARYKEY => $where,
            ));
        }

        return shipinvoiceLockSearch($where, $order_by);
    }
}
if (!function_exists('shipinvoiceLockToDatabase')) {
    /**
     * @param mixed[] $item
     *
     * @return int
     */
    function shipinvoiceLockToDatabase(&$item)
    {
        global $DB;

        return $DB->modelSave(
            $item,
            SHIPINVOICE_LOCK_TABLENAME,
            SHIPINVOICE_LOCK_PRIMARYKEY
        );
    }
}
if (!function_exists('shipinvoiceLockVirtual')) {
    /**
     * @param mixed[] $row
     *
     * @return mixed[]
     */
    function shipinvoiceLockVirtual($row)
    {
        return $row;
    }
}

if (!function_exists('shipinvoiceLocksDelete')) {
    /**
     * @param int $id
     *
     * @return int
     */
    function shipinvoiceLocksDelete($id)
    {
        global $DB;

        return $DB->childrenDelete(
            $id,
            SHIPINVOICE_LOCK_TABLENAME,
            SHIPINVOICE_LOCK_SECONDARYKEY
        );
    }
}
if (!function_exists('shipinvoiceLocksFromDatabase')) {
    /**
     * @param int $id
     *
     * @return mixed[]|null
     */
    function shipinvoiceLocksFromDatabase($id)
    {
        global $DB;

        $result = array();
        foreach ($DB->childrenLoad(
            $id,
            SHIPINVOICE_LOCK_TABLENAME,
            SHIPINVOICE_LOCK_PRIMARYKEY,
            SHIPINVOICE_LOCK_SECONDARYKEY
        ) as $k => $v) {
            $result[$k] = shipinvoiceLockVirtual($v);
        }

        return $result;
    }
}
if (!function_exists('shipinvoiceLocksToDatabase')) {
    /**
     * @param int       $id
     * @param mixed[][] $items
     *
     * @return void
     */
    function shipinvoiceLocksToDatabase($id, &$items)
    {
        global $DB;

        $DB->childrenSave(
            $id,
            $items,
            SHIPINVOICE_LOCK_TABLENAME,
            SHIPINVOICE_LOCK_PRIMARYKEY,
            SHIPINVOICE_LOCK_SECONDARYKEY
        );
    }
}

if (!function_exists("shipinvoiceGetLock")) {
    function shipinvoiceGetLock($id)
    {
        return null;
    }
}
if (!function_exists("shipinvoiceSetLock")) {
    function shipinvoiceSetLock($sinvoice_id, $staff_id = '', $timeout = 0)
    {
        //Do Nothing
    }
}
