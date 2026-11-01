<?php
if (!defined('SERIES_TABLENAME')) {
    define('SERIES_TABLENAME', "`product_series`");
}
if (!defined('SERIES_PRIMARYKEY')) {
    define('SERIES_PRIMARYKEY', "id");
}

if (!function_exists('seriesDefault')) {
    /**
     * @return mixed[]
     */
    function seriesDefault()
    {
        global $DB;

        return seriesVirtual($DB->modelDefault(
            SERIES_TABLENAME,
            SERIES_PRIMARYKEY
        ));
    }
}
if (!function_exists('seriesDelete')) {
    /**
     * @param int $id
     *
     * @return int
     */
    function seriesDelete($id)
    {
        global $DB;

        return $DB->modelDelete(
            $id,
            SERIES_TABLENAME,
            SERIES_PRIMARYKEY
        );
    }
}
if (!function_exists('seriesExists')) {
    /**
     * @param int $id
     *
     * @return bool
     */
    function seriesExists($id)
    {
        global $DB;

        return $DB->modelExists(
            $id,
            SERIES_TABLENAME,
            SERIES_PRIMARYKEY
        );
    }
}
if (!function_exists('seriesFromDatabase')) {
    /**
     * @param int $id
     *
     * @return mixed[]|null
     */
    function seriesFromDatabase($id)
    {
        global $DB;

        if ($row = $DB->modelLoad(
            $id,
            SERIES_TABLENAME,
            SERIES_PRIMARYKEY
        )) {
            return seriesVirtual($row);
        }

        return null;
    }
}
if (!function_exists('seriesGetOptionList')) {
    /**
     * @param string $key
     *
     * @return mixed
     */
    function seriesGetOptionList($key)
    {
        global $DB;

        switch ($key) {
            case 'active':
                return [
                    'n' => 'No',
                    'y' => 'Yes',
                ];

            case 'img_path':
                return $_SERVER['DOCUMENT_ROOT'] . '/webadmin/uploads/series/image';
        }
        die(__FILE__ . '@' . __LINE__ . ': ' . __FUNCTION__ . '(' . $key . ') not yet implemented');
    }
}
if (!function_exists('seriesGetValue')) {
    /**
     * @param int    $id
     * @param string $field
     *
     * @return mixed
     */
    function seriesGetValue($id, $field)
    {
        global $DB;

        if (array_key_exists($field, $DB->table_columns(
            SERIES_TABLENAME
        ))) {
            return $DB->modelGet(
                $id,
                $field,
                SERIES_TABLENAME,
                SERIES_PRIMARYKEY
            );
        }

        if ($model = seriesFromDatabase($id)) {
            return $model[$field] ?? null;
        }

        return null;
    }
}
if (!function_exists('seriesHasValue')) {
    /**
     * @param int    $id
     * @param string $field
     * @param mixed  $value
     *
     * @return bool
     */
    function seriesHasValue($id, $field, $value)
    {
        return $value == seriesGetValue($id, $field);
    }
}
if (!function_exists('seriesQuery')) {
    /**
     * @param mixed[] $eq
     * @param mixed[] $lt
     * @param mixed[] $gt
     *
     * @return string
     */
    function seriesQuery($eq = array(), $lt = array(), $gt = array())
    {
        global $DB;

        if (empty($eq) && empty($lt) && empty($gt)) {
            return '1';
        }

        $field_types = getTableFields(SERIES_TABLENAME);

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
if (!function_exists('seriesSearch')) {
    /**
     * @param string|null $where
     * @param string|null $order_by
     *
     * @return int[]
     */
    function seriesSearch($where = null, $order_by = null)
    {
        // Legacy accepts criteria as first parameter
        if (is_array($where)) {
            $where = seriesQuery($where);
        }

        return seriesSort($where, 'rank');
    }
}
if (!function_exists('seriesSetValue')) {
    /**
     * @param int    $id
     * @param string $field
     * @param mixed  $value
     *
     * @return int
     */
    function seriesSetValue($id, $field, $value)
    {
        global $DB;

        return $DB->modelSet(
            $id,
            $field,
            $value,
            SERIES_TABLENAME,
            SERIES_PRIMARYKEY
        );
    }
}
if (!function_exists('seriesSort')) {
    /**
     * @param string|null $where
     * @param string|null $order_by
     *
     * @return int[]
     */
    function seriesSort($where = null, $order_by = null)
    {
        global $DB;

        // Legacy accepts list of id's as first parameter
        if (is_array($where)) {
            $where = seriesQuery(array(
                SERIES_PRIMARYKEY => $where,
            ));
        }

        return $DB->query_list(sprintf(
            "SELECT `%s` FROM %s%s%s",
            SERIES_PRIMARYKEY,
            SERIES_TABLENAME,
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
if (!function_exists('seriesToDatabase')) {
    /**
     * @param mixed[] $item
     *
     * @return int
     */
    function seriesToDatabase(&$item)
    {
        global $DB;

        return $DB->modelSave(
            $item,
            SERIES_TABLENAME,
            SERIES_PRIMARYKEY
        );
    }
}
if (!function_exists('seriesVirtual')) {
    /**
     * @param mixed[] $row
     *
     * @return mixed[]
     */
    function seriesVirtual($row)
    {
        if (array_key_exists(SERIES_PRIMARYKEY, $row)) {
            $row['id'] = $row[SERIES_PRIMARYKEY];
        }

        return $row;
    }
}
