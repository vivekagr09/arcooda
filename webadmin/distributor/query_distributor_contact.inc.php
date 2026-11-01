<?php
if (!defined('DISTRIBUTOR_CONTACT_TABLENAME')) {
    define('DISTRIBUTOR_CONTACT_TABLENAME', '`distributor_contacts`');
}
if (!defined('DISTRIBUTOR_CONTACT_PRIMARYKEY')) {
    define('DISTRIBUTOR_CONTACT_PRIMARYKEY', 'id');
}
if (!defined('DISTRIBUTOR_CONTACT_SECONDARYKEY')) {
    define('DISTRIBUTOR_CONTACT_SECONDARYKEY', 'distributor_id');
}

if (!function_exists('distributorContactDefault')) {
    /**
     * @return mixed[]
     */
    function distributorContactDefault()
    {
        global $DB;

        return distributorContactVirtual($DB->modelDefault(
            DISTRIBUTOR_CONTACT_TABLENAME,
            DISTRIBUTOR_CONTACT_PRIMARYKEY
        ));
    }
}
if (!function_exists('distributorContactDelete')) {
    /**
     * @param int $id
     *
     * @return int
     */
    function distributorContactDelete($id)
    {
        global $DB;

        return $DB->modelDelete(
            $id,
            DISTRIBUTOR_CONTACT_TABLENAME,
            DISTRIBUTOR_CONTACT_PRIMARYKEY
        );
    }
}
if (!function_exists('distributorContactExists')) {
    /**
     * @param int $id
     *
     * @return bool
     */
    function distributorContactExists($id)
    {
        global $DB;

        return $DB->modelExists(
            $id,
            DISTRIBUTOR_CONTACT_TABLENAME,
            DISTRIBUTOR_CONTACT_PRIMARYKEY
        );
    }
}
if (!function_exists('distributorContactFromDatabase')) {
    /**
     * @param int $id
     *
     * @return mixed[]|null
     */
    function distributorContactFromDatabase($id)
    {
        global $DB;

        if ($row = $DB->modelLoad(
            $id,
            DISTRIBUTOR_CONTACT_TABLENAME,
            DISTRIBUTOR_CONTACT_PRIMARYKEY
        )) {
            return distributorContactVirtual($row);
        }

        return null;
    }
}
if (!function_exists('distributorContactGetValue')) {
    /**
     * @param int    $id
     * @param string $field
     *
     * @return mixed
     */
    function distributorContactGetValue($id, $field)
    {
        global $DB;

        if (array_key_exists($field, $DB->table_columns(
            DISTRIBUTOR_CONTACT_TABLENAME
        ))) {
            return $DB->modelGet(
                $id,
                $field,
                DISTRIBUTOR_CONTACT_TABLENAME,
                DISTRIBUTOR_CONTACT_PRIMARYKEY
            );
        }

        if ($model = distributorContactFromDatabase($id)) {
            return $model[$field] ?? null;
        }

        return null;
    }
}
if (!function_exists('distributorContactHasValue')) {
    /**
     * @param int    $id
     * @param string $field
     * @param mixed  $value
     *
     * @return bool
     */
    function distributorContactHasValue($id, $field, $value)
    {
        return $value == distributorContactGetValue($id, $field);
    }
}
if (!function_exists('distributorContactQuery')) {
    /**
     * @param mixed[] $eq
     * @param mixed[] $lt
     * @param mixed[] $gt
     *
     * @return string
     */
    function distributorContactQuery($eq = array(), $lt = array(), $gt = array())
    {
        global $DB;

        if (empty($eq) && empty($lt) && empty($gt)) {
            return '1';
        }

        $field_types = getTableFields(DISTRIBUTOR_CONTACT_TABLENAME);

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
if (!function_exists('distributorContactSearch')) {
    /**
     * @param string|null $where
     * @param string|null $order_by
     *
     * @return int[]
     */
    function distributorContactSearch($where = null, $order_by = null)
    {
        global $DB;

        // Legacy accepts criteria as first parameter
        if (is_array($where)) {
            $where = distributorContactQuery($where);
        }

        return $DB->query_list(sprintf(
            "SELECT `%s` FROM %s%s%s",
            DISTRIBUTOR_CONTACT_PRIMARYKEY,
            DISTRIBUTOR_CONTACT_TABLENAME,
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
if (!function_exists('distributorContactSetValue')) {
    /**
     * @param int    $id
     * @param string $field
     * @param mixed  $value
     *
     * @return int
     */
    function distributorContactSetValue($id, $field, $value)
    {
        global $DB;

        return $DB->modelSet(
            $id,
            $field,
            $value,
            DISTRIBUTOR_CONTACT_TABLENAME,
            DISTRIBUTOR_CONTACT_PRIMARYKEY
        );
    }
}
if (!function_exists('distributorContactSort')) {
    /**
     * @param string|null $where
     * @param string|null $order_by
     *
     * @return int[]
     */
    function distributorContactSort($where = null, $order_by = null)
    {
        // Legacy accepts list of id's as first parameter
        if (is_array($where)) {
            $where = distributorContactQuery(array(
                DISTRIBUTOR_CONTACT_PRIMARYKEY => $where,
            ));
        }

        return distributorContactSearch($where, $order_by);
    }
}
if (!function_exists('distributorContactToDatabase')) {
    /**
     * @param mixed[] $item
     *
     * @return int
     */
    function distributorContactToDatabase(&$item)
    {
        global $DB;

        return $DB->modelSave(
            $item,
            DISTRIBUTOR_CONTACT_TABLENAME,
            DISTRIBUTOR_CONTACT_PRIMARYKEY
        );
    }
}
if (!function_exists('distributorContactVirtual')) {
    /**
     * @param mixed[] $row
     *
     * @return mixed[]
     */
    function distributorContactVirtual($row)
    {
        return $row;
    }
}

if (!function_exists('distributorContactsDelete')) {
    /**
     * @param int $id
     *
     * @return int
     */
    function distributorContactsDelete($id)
    {
        global $DB;

        return $DB->childrenDelete(
            $id,
            DISTRIBUTOR_CONTACT_TABLENAME,
            DISTRIBUTOR_CONTACT_SECONDARYKEY
        );
    }
}
if (!function_exists('distributorContactsFromDatabase')) {
    /**
     * @param int $id
     *
     * @return mixed[]|null
     */
    function distributorContactsFromDatabase($id)
    {
        global $DB;

        $result = array();
        foreach ($DB->childrenLoad(
            $id,
            DISTRIBUTOR_CONTACT_TABLENAME,
            DISTRIBUTOR_CONTACT_PRIMARYKEY,
            DISTRIBUTOR_CONTACT_SECONDARYKEY
        ) as $k => $v) {
            $result[$k] = distributorContactVirtual($v);
        }

        return $result;
    }
}
if (!function_exists('distributorContactsToDatabase')) {
    /**
     * @param int       $id
     * @param mixed[][] $items
     *
     * @return void
     */
    function distributorContactsToDatabase($id, &$items)
    {
        global $DB;

        $DB->childrenSave(
            $id,
            $items,
            DISTRIBUTOR_CONTACT_TABLENAME,
            DISTRIBUTOR_CONTACT_PRIMARYKEY,
            DISTRIBUTOR_CONTACT_SECONDARYKEY
        );
    }
}
