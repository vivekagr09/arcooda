<?php
if (!defined('documentinvoice_LOCK_TABLENAME')) {
    define('documentinvoice_LOCK_TABLENAME', '`highwaygroup`.`documentinvoice_locks`');
}
if (!defined('documentinvoice_LOCK_PRIMARYKEY')) {
    define('documentinvoice_LOCK_PRIMARYKEY', 'id');
}
if (!defined('documentinvoice_LOCK_SECONDARYKEY')) {
    define('documentinvoice_LOCK_SECONDARYKEY', 'dinvoice_id');
}

if (!function_exists('documentinvoiceLockDefault')) {
    /**
     * @return mixed[]
     */
    function documentinvoiceLockDefault()
    {
        global $DB;

        return documentinvoiceLockVirtual($DB->modelDefault(
            documentinvoice_LOCK_TABLENAME,
            documentinvoice_LOCK_PRIMARYKEY
        ));
    }
}
if (!function_exists('documentinvoiceLockDelete')) {
    /**
     * @param int $id
     *
     * @return int
     */
    function documentinvoiceLockDelete($id)
    {
        global $DB;

        return $DB->modelDelete(
            $id,
            documentinvoice_LOCK_TABLENAME,
            documentinvoice_LOCK_PRIMARYKEY
        );
    }
}
if (!function_exists('documentinvoiceLockExists')) {
    /**
     * @param int $id
     *
     * @return bool
     */
    function documentinvoiceLockExists($id)
    {
        global $DB;

        return $DB->modelExists(
            $id,
            documentinvoice_LOCK_TABLENAME,
            documentinvoice_LOCK_PRIMARYKEY
        );
    }
}
if (!function_exists('documentinvoiceLockFromDatabase')) {
    /**
     * @param int $id
     *
     * @return mixed[]|null
     */
    function documentinvoiceLockFromDatabase($id)
    {
        global $DB;

        if ($row = $DB->modelLoad(
            $id,
            documentinvoice_LOCK_TABLENAME,
            documentinvoice_LOCK_PRIMARYKEY
        )) {
            return documentinvoiceLockVirtual($row);
        }

        return null;
    }
}
if (!function_exists('documentinvoiceLockGetValue')) {
    /**
     * @param int    $id
     * @param string $field
     *
     * @return mixed
     */
    function documentinvoiceLockGetValue($id, $field)
    {
        global $DB;

        if (array_key_exists($field, $DB->table_columns(
            documentinvoice_LOCK_TABLENAME
        ))) {
            return $DB->modelGet(
                $id,
                $field,
                documentinvoice_LOCK_TABLENAME,
                documentinvoice_LOCK_PRIMARYKEY
            );
        }

        if ($model = documentinvoiceLockFromDatabase($id)) {
            return (array_key_exists($field, $model)) ? $model[$field] : null;
        }

        return null;
    }
}
if (!function_exists('documentinvoiceLockHasValue')) {
    /**
     * @param int    $id
     * @param string $field
     * @param mixed  $value
     *
     * @return bool
     */
    function documentinvoiceLockHasValue($id, $field, $value)
    {
        return documentinvoiceLockGetValue($id, $field) == $value;
    }
}
if (!function_exists('documentinvoiceLockQuery')) {
    /**
     * @todo
     *
     * @param mixed[] $eq
     * @param mixed[] $lt
     * @param mixed[] $gt
     *
     * @return string
     */
    function documentinvoiceLockQuery($eq = array(), $lt = array(), $gt = array())
    {
        global $DB;

        $whereAND = array();

        return (count($whereAND)) ? implode(" AND ", $whereAND) : "1";
    }
}
if (!function_exists('documentinvoiceLockSearch')) {
    /**
     * @param string|null $where
     * @param string|null $order_by
     *
     * @return int[]
     */
    function documentinvoiceLockSearch($where = null, $order_by = null)
    {
        global $DB;

        // Legacy accepts criteria as first parameter
        if (is_array($where)) {
            $where = documentinvoiceLockQuery($where);
        }

        return $DB->query_list(sprintf(
            "SELECT `%s` FROM %s%s%s",
            documentinvoice_LOCK_PRIMARYKEY,
            documentinvoice_LOCK_TABLENAME,
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
if (!function_exists('documentinvoiceLockSetValue')) {
    /**
     * @param int    $id
     * @param string $field
     * @param mixed  $value
     *
     * @return int
     */
    function documentinvoiceLockSetValue($id, $field, $value)
    {
        global $DB;

        return $DB->modelSet(
            $id,
            $field,
            $value,
            documentinvoice_LOCK_TABLENAME,
            documentinvoice_LOCK_PRIMARYKEY
        );
    }
}
if (!function_exists('documentinvoiceLockSort')) {
    /**
     * @param string|null $where
     * @param string|null $order_by
     *
     * @return int[]
     */
    function documentinvoiceLockSort($where = null, $order_by = null)
    {
        // Legacy accepts list of id's as first parameter
        if (is_array($where)) {
            $where = documentinvoiceLockQuery(array(
                documentinvoice_LOCK_PRIMARYKEY => $where,
            ));
        }

        return documentinvoiceLockSearch($where, $order_by);
    }
}
if (!function_exists('documentinvoiceLockToDatabase')) {
    /**
     * @param mixed[] $item
     *
     * @return int
     */
    function documentinvoiceLockToDatabase(&$item)
    {
        global $DB;

        return $DB->modelSave(
            $item,
            documentinvoice_LOCK_TABLENAME,
            documentinvoice_LOCK_PRIMARYKEY
        );
    }
}
if (!function_exists('documentinvoiceLockVirtual')) {
    /**
     * @param mixed[] $row
     *
     * @return mixed[]
     */
    function documentinvoiceLockVirtual($row)
    {
        return $row;
    }
}

if (!function_exists('documentinvoiceLocksDelete')) {
    /**
     * @param int $id
     *
     * @return int
     */
    function documentinvoiceLocksDelete($id)
    {
        global $DB;

        return $DB->childrenDelete(
            $id,
            documentinvoice_LOCK_TABLENAME,
            documentinvoice_LOCK_SECONDARYKEY
        );
    }
}
if (!function_exists('documentinvoiceLocksFromDatabase')) {
    /**
     * @param int $id
     *
     * @return mixed[]|null
     */
    function documentinvoiceLocksFromDatabase($id)
    {
        global $DB;

        $result = array();
        foreach ($DB->childrenLoad(
            $id,
            documentinvoice_LOCK_TABLENAME,
            documentinvoice_LOCK_PRIMARYKEY,
            documentinvoice_LOCK_SECONDARYKEY
        ) as $k => $v) {
            $result[$k] = documentinvoiceLockVirtual($v);
        }

        return $result;
    }
}
if (!function_exists('documentinvoiceLocksToDatabase')) {
    /**
     * @param int       $id
     * @param mixed[][] $items
     *
     * @return void
     */
    function documentinvoiceLocksToDatabase($id, &$items)
    {
        global $DB;

        $DB->childrenSave(
            $id,
            $items,
            documentinvoice_LOCK_TABLENAME,
            documentinvoice_LOCK_PRIMARYKEY,
            documentinvoice_LOCK_SECONDARYKEY
        );
    }
}

if (!function_exists("documentinvoiceGetLock")) {
    function documentinvoiceGetLock($id)
    {
        return null;
    }
}
if (!function_exists("documentinvoiceSetLock")) {
    function documentinvoiceSetLock($dinvoice_id, $staff_id = '', $timeout = 0)
    {
        //Do Nothing
    }
}
