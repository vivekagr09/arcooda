<?php
if (!defined('WAREHOUSE_CONTACT_TABLENAME')) {
    define('WAREHOUSE_CONTACT_TABLENAME', '`warehouse_contacts`');
}
if (!defined('WAREHOUSE_CONTACT_PRIMARYKEY')) {
    define('WAREHOUSE_CONTACT_PRIMARYKEY', 'id');
}
if (!defined('WAREHOUSE_CONTACT_SECONDARYKEY')) {
    define('WAREHOUSE_CONTACT_SECONDARYKEY', 'warehouse_id');
}

if (!function_exists('warehouseContactDefault')) {
    /**
     * @return mixed[]
     */
    function warehouseContactDefault()
    {
        global $DB;

        return warehouseContactVirtual($DB->modelDefault(
            WAREHOUSE_CONTACT_TABLENAME,
            WAREHOUSE_CONTACT_PRIMARYKEY
        ));
    }
}
if (!function_exists('warehouseContactDelete')) {
    /**
     * @param int $id
     *
     * @return int
     */
    function warehouseContactDelete($id)
    {
        global $DB;

        return $DB->modelDelete(
            $id,
            WAREHOUSE_CONTACT_TABLENAME,
            WAREHOUSE_CONTACT_PRIMARYKEY
        );
    }
}
if (!function_exists('warehouseContactExists')) {
    /**
     * @param int $id
     *
     * @return bool
     */
    function warehouseContactExists($id)
    {
        global $DB;

        return $DB->modelExists(
            $id,
            WAREHOUSE_CONTACT_TABLENAME,
            WAREHOUSE_CONTACT_PRIMARYKEY
        );
    }
}
if (!function_exists('warehouseContactFromDatabase')) {
    /**
     * @param int $id
     *
     * @return mixed[]|null
     */
    function warehouseContactFromDatabase($id)
    {
        global $DB;

        if ($row = $DB->modelLoad(
            $id,
            WAREHOUSE_CONTACT_TABLENAME,
            WAREHOUSE_CONTACT_PRIMARYKEY
        )) {
            return warehouseContactVirtual($row);
        }

        return null;
    }
}
if (!function_exists('warehouseContactGetValue')) {
    /**
     * @param int    $id
     * @param string $field
     *
     * @return mixed
     */
    function warehouseContactGetValue($id, $field)
    {
        global $DB;

        if (array_key_exists($field, $DB->table_columns(
            WAREHOUSE_CONTACT_TABLENAME
        ))) {
            return $DB->modelGet(
                $id,
                $field,
                WAREHOUSE_CONTACT_TABLENAME,
                WAREHOUSE_CONTACT_PRIMARYKEY
            );
        }

        if ($model = warehouseContactFromDatabase($id)) {
            return $model[$field] ?? null;
        }

        return null;
    }
}
if (!function_exists('warehouseContactHasValue')) {
    /**
     * @param int    $id
     * @param string $field
     * @param mixed  $value
     *
     * @return bool
     */
    function warehouseContactHasValue($id, $field, $value)
    {
        return $value == warehouseContactGetValue($id, $field);
    }
}
if (!function_exists('warehouseContactQuery')) {
    /**
     * @param mixed[] $eq
     * @param mixed[] $lt
     * @param mixed[] $gt
     *
     * @return string
     */
    function warehouseContactQuery($eq = array(), $lt = array(), $gt = array())
    {
        global $DB;

        if (empty($eq) && empty($lt) && empty($gt)) {
            return '1';
        }

        $field_types = getTableFields(WAREHOUSE_CONTACT_TABLENAME);

        $whereAND = array();
        foreach (array(
            '=' => (is_null($eq)) ? array() : $eq,
            '<' => (is_null($lt)) ? array() : $lt,
            '>' => (is_null($gt)) ? array() : $gt,
        ) as $op => $criteria) {
            foreach ($criteria as $field => $value) {
                if (array_key_exists($field, $field_types)) {
                    $whereOR = array();
                    foreach ((array)$value as $sub_value) {
                        $whereOR[] = sprintf(
                            "`%s` %s %s",
                            $field,
                            $op,
                            $DB->escape($sub_value)
                        );
                    }
                    if (1 == count($whereOR)) {
                        $whereAND[] = implode("", $whereOR);
                    } elseif (count($whereOR)) {
                        $whereAND[] = sprintf("(%s)", implode(" OR ", $whereOR));
                    }
                }
            }
        }

        return (count($whereAND)) ? implode(" AND ", $whereAND) : "1";
    }
}
if (!function_exists('warehouseContactSearch')) {
    /**
     * @param string|null $where
     * @param string|null $order_by
     *
     * @return int[]
     */
    function warehouseContactSearch($where = null, $order_by = null)
    {
        global $DB;

        // Legacy accepts criteria as first parameter
        if (is_array($where)) {
            $where = warehouseContactQuery($where);
        }

        return $DB->query_list(sprintf(
            "SELECT `%s` FROM %s%s%s",
            WAREHOUSE_CONTACT_PRIMARYKEY,
            WAREHOUSE_CONTACT_TABLENAME,
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
if (!function_exists('warehouseContactSetValue')) {
    /**
     * @param int    $id
     * @param string $field
     * @param mixed  $value
     *
     * @return int
     */
    function warehouseContactSetValue($id, $field, $value)
    {
        global $DB;

        return $DB->modelSet(
            $id,
            $field,
            $value,
            WAREHOUSE_CONTACT_TABLENAME,
            WAREHOUSE_CONTACT_PRIMARYKEY
        );
    }
}
if (!function_exists('warehouseContactSort')) {
    /**
     * @param string|null $where
     * @param string|null $order_by
     *
     * @return int[]
     */
    function warehouseContactSort($where = null, $order_by = null)
    {
        // Legacy accepts list of id's as first parameter
        if (is_array($where)) {
            $where = warehouseContactQuery(array(
                WAREHOUSE_CONTACT_PRIMARYKEY => $where,
            ));
        }

        return warehouseContactSearch($where, $order_by);
    }
}
if (!function_exists('warehouseContactToDatabase')) {
    /**
     * @param mixed[] $item
     *
     * @return int
     */
    function warehouseContactToDatabase(&$item)
    {
        global $DB;

        return $DB->modelSave(
            $item,
            WAREHOUSE_CONTACT_TABLENAME,
            WAREHOUSE_CONTACT_PRIMARYKEY
        );
    }
}
if (!function_exists('warehouseContactVirtual')) {
    /**
     * @param mixed[] $row
     *
     * @return mixed[]
     */
    function warehouseContactVirtual($row)
    {
        return $row;
    }
}

if (!function_exists('warehouseContactsDelete')) {
    /**
     * @param int $id
     *
     * @return int
     */
    function warehouseContactsDelete($id)
    {
        global $DB;

        return $DB->childrenDelete(
            $id,
            WAREHOUSE_CONTACT_TABLENAME,
            WAREHOUSE_CONTACT_SECONDARYKEY
        );
    }
}
if (!function_exists('warehouseContactsFromDatabase')) {
    /**
     * @param int $id
     *
     * @return mixed[]|null
     */
    function warehouseContactsFromDatabase($id)
    {
        global $DB;

        $result = array();
        foreach ($DB->childrenLoad(
            $id,
            WAREHOUSE_CONTACT_TABLENAME,
            WAREHOUSE_CONTACT_PRIMARYKEY,
            WAREHOUSE_CONTACT_SECONDARYKEY
        ) as $k => $v) {
            $result[$k] = warehouseContactVirtual($v);
        }

        return $result;
    }
}
if (!function_exists('warehouseContactsToDatabase')) {
    /**
     * @param int       $id
     * @param mixed[][] $items
     *
     * @return void
     */
    function warehouseContactsToDatabase($id, $items)
    {
        global $DB;

        $DB->childrenSave(
            $id,
            $items,
            WAREHOUSE_CONTACT_TABLENAME,
            WAREHOUSE_CONTACT_PRIMARYKEY,
            WAREHOUSE_CONTACT_SECONDARYKEY
        );
    }
}
