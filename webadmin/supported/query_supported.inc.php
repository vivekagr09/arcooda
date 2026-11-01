<?php
if (!defined('SUPPORTED_TABLENAME')) {
    define('SUPPORTED_TABLENAME', "`product_supported`");
}
if (!defined('SUPPORTED_PRIMARYKEY')) {
    define('SUPPORTED_PRIMARYKEY', "id");
}

if (!function_exists('supportedDefault')) {
    /**
     * @return mixed[]
     */
    function supportedDefault()
    {
        global $DB;

        return supportedVirtual($DB->modelDefault(
            SUPPORTED_TABLENAME,
            SUPPORTED_PRIMARYKEY
        ));
    }
}
if (!function_exists('supportedDelete')) {
    /**
     * @param int $id
     *
     * @return int
     */
    function supportedDelete($id)
    {
        global $DB;

        return $DB->modelDelete(
            $id,
            SUPPORTED_TABLENAME,
            SUPPORTED_PRIMARYKEY
        );
    }
}
if (!function_exists('supportedExists')) {
    /**
     * @param int $id
     *
     * @return bool
     */
    function supportedExists($id)
    {
        global $DB;

        return $DB->modelExists(
            $id,
            SUPPORTED_TABLENAME,
            SUPPORTED_PRIMARYKEY
        );
    }
}
if (!function_exists('supportedFromDatabase')) {
    /**
     * @param int $id
     *
     * @return mixed[]|null
     */
    function supportedFromDatabase($id)
    {
        global $DB;

        if ($row = $DB->modelLoad(
            $id,
            SUPPORTED_TABLENAME,
            SUPPORTED_PRIMARYKEY
        )) {
            return supportedVirtual($row);
        }

        return null;
    }
}
if (!function_exists('supportedGetOptionList')) {
    /**
     * @param string $key
     *
     * @return mixed
     */
    function supportedGetOptionList($key)
    {
        global $DB;

        switch ($key) {
            case 'active':
                return [
                    'n' => 'No',
                    'y' => 'Yes',
                ];

            case 'img_path':
                return $_SERVER['DOCUMENT_ROOT'] . '/webadmin/uploads/supported/image';
        }
        die(__FILE__ . '@' . __LINE__ . ': ' . __FUNCTION__ . '(' . $key . ') not yet implemented');
    }
}
if (!function_exists('supportedGetValue')) {
    /**
     * @param int    $id
     * @param string $field
     *
     * @return mixed
     */
    function supportedGetValue($id, $field)
    {
        global $DB;

        if (array_key_exists($field, $DB->table_columns(
            SUPPORTED_TABLENAME
        ))) {
            return $DB->modelGet(
                $id,
                $field,
                SUPPORTED_TABLENAME,
                SUPPORTED_PRIMARYKEY
            );
        }

        if ($model = supportedFromDatabase($id)) {
            return $model[$field] ?? null;
        }

        return null;
    }
}
if (!function_exists('supportedHasValue')) {
    /**
     * @param int    $id
     * @param string $field
     * @param mixed  $value
     *
     * @return bool
     */
    function supportedHasValue($id, $field, $value)
    {
        return $value == supportedGetValue($id, $field);
    }
}
if (!function_exists('supportedQuery')) {
    /**
     * @param mixed[] $eq
     * @param mixed[] $lt
     * @param mixed[] $gt
     *
     * @return string
     */
    function supportedQuery($eq = array(), $lt = array(), $gt = array())
    {
        global $DB;

        if (empty($eq) && empty($lt) && empty($gt)) {
            return '1';
        }

        $field_types = getTableFields(SUPPORTED_TABLENAME);

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
if (!function_exists('supportedSearch')) {
    /**
     * @param string|null $where
     * @param string|null $order_by
     *
     * @return int[]
     */
    function supportedSearch($where = null, $order_by = null)
    {
        // Legacy accepts criteria as first parameter
        if (is_array($where)) {
            $where = supportedQuery($where);
        }

        return supportedSort($where, 'rank');
    }
}
if (!function_exists('supportedSetValue')) {
    /**
     * @param int    $id
     * @param string $field
     * @param mixed  $value
     *
     * @return int
     */
    function supportedSetValue($id, $field, $value)
    {
        global $DB;

        return $DB->modelSet(
            $id,
            $field,
            $value,
            SUPPORTED_TABLENAME,
            SUPPORTED_PRIMARYKEY
        );
    }
}
if (!function_exists('supportedSort')) {
    /**
     * @param string|null $where
     * @param string|null $order_by
     *
     * @return int[]
     */
    function supportedSort($where = null, $order_by = null)
    {
        global $DB;

        // Legacy accepts list of id's as first parameter
        if (is_array($where)) {
            $where = supportedQuery(array(
                SUPPORTED_PRIMARYKEY => $where,
            ));
        }

        return $DB->query_list(sprintf(
            "SELECT `%s` FROM %s%s%s",
            SUPPORTED_PRIMARYKEY,
            SUPPORTED_TABLENAME,
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
if (!function_exists('supportedToDatabase')) {
    /**
     * @param mixed[] $item
     *
     * @return int
     */
    function supportedToDatabase(&$item)
    {
        global $DB;

        return $DB->modelSave(
            $item,
            SUPPORTED_TABLENAME,
            SUPPORTED_PRIMARYKEY
        );
    }
}
if (!function_exists('supportedVirtual')) {
    /**
     * @param mixed[] $row
     *
     * @return mixed[]
     */
    function supportedVirtual($row)
    {
        if (array_key_exists(SUPPORTED_PRIMARYKEY, $row)) {
            $row['id'] = $row[SUPPORTED_PRIMARYKEY];
        }

        return $row;
    }
}
