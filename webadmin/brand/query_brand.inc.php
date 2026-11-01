<?php
if (!defined('BRAND_TABLENAME')) {
    define('BRAND_TABLENAME', '`brand`');
}
if (!defined('BRAND_PRIMARYKEY')) {
    define('BRAND_PRIMARYKEY', 'id');
}

if (!function_exists('brandDefault')) {
    /**
     * @return mixed[]
     */
    function brandDefault()
    {
        global $DB;

        return brandVirtual($DB->modelDefault(
            BRAND_TABLENAME,
            BRAND_PRIMARYKEY
        ));
    }
}
if (!function_exists('brandDelete')) {
    /**
     * @param int $id
     *
     * @return int
     */
    function brandDelete($id)
    {
        global $DB;

        return $DB->modelDelete(
            $id,
            BRAND_TABLENAME,
            BRAND_PRIMARYKEY
        );
    }
}
if (!function_exists('brandExists')) {
    /**
     * @param int $id
     *
     * @return bool
     */
    function brandExists($id)
    {
        global $DB;

        return $DB->modelExists(
            $id,
            BRAND_TABLENAME,
            BRAND_PRIMARYKEY
        );
    }
}
if (!function_exists('brandFromDatabase')) {
    /**
     * @param int $id
     *
     * @return mixed[]|null
     */
    function brandFromDatabase($id)
    {
        global $DB;

        if ($row = $DB->modelLoad(
            $id,
            BRAND_TABLENAME,
            BRAND_PRIMARYKEY
        )) {
            return brandVirtual($row);
        }

        return null;
    }
}
if (!function_exists('brandGetValue')) {
    /**
     * @param int    $id
     * @param string $field
     *
     * @return mixed
     */
    function brandGetValue($id, $field)
    {
        global $DB;

        if (array_key_exists($field, $DB->table_columns(
            BRAND_TABLENAME
        ))) {
            return $DB->modelGet(
                $id,
                $field,
                BRAND_TABLENAME,
                BRAND_PRIMARYKEY
            );
        }

        if ($model = brandFromDatabase($id)) {
            return $model[$field] ?? null;
        }

        return null;
    }
}
if (!function_exists('brandHasValue')) {
    /**
     * @param int    $id
     * @param string $field
     * @param mixed  $value
     *
     * @return bool
     */
    function brandHasValue($id, $field, $value)
    {
        return $value == brandGetValue($id, $field);
    }
}
if (!function_exists('brandQuery')) {
    /**
     * @param mixed[] $eq
     * @param mixed[] $lt
     * @param mixed[] $gt
     *
     * @return string
     */
    function brandQuery($eq = array(), $lt = array(), $gt = array())
    {
        global $DB;

        if (empty($eq) && empty($lt) && empty($gt)) {
            return '1';
        }

        $field_types = getTableFields(BRAND_TABLENAME);

        $whereAND = array();
        foreach (array(
            '=' => $eq,
            '<' => $lt,
            '>' => $gt,
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
if (!function_exists('brandSearch')) {
    /**
     * @param string|null $where
     * @param string|null $order_by
     *
     * @return int[]
     */
    function brandSearch($where = null, $order_by = null)
    {
        global $DB;

        // Legacy accepts criteria as first parameter
        if (is_array($where)) {
            $where = brandQuery($where);
        }

        return $DB->query_list(sprintf(
            "SELECT `%s` FROM %s%s%s",
            BRAND_PRIMARYKEY,
            BRAND_TABLENAME,
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
if (!function_exists('brandSetValue')) {
    /**
     * @param int    $id
     * @param string $field
     * @param mixed  $value
     *
     * @return int
     */
    function brandSetValue($id, $field, $value)
    {
        global $DB;

        return $DB->modelSet(
            $id,
            $field,
            $value,
            BRAND_TABLENAME,
            BRAND_PRIMARYKEY
        );
    }
}
if (!function_exists('brandSort')) {
    /**
     * @param string|null $where
     * @param string|null $order_by
     *
     * @return int[]
     */
    function brandSort($where = null, $order_by = null)
    {
        // Legacy accepts list of id's as first parameter
        if (is_array($where)) {
            $where = brandQuery(array(
                BRAND_PRIMARYKEY => $where,
            ));
        }

        return brandSearch($where, $order_by);
    }
}
if (!function_exists('brandToDatabase')) {
    /**
     * @param mixed[] $item
     *
     * @return int
     */
    function brandToDatabase(&$item)
    {
        global $DB;

        return $DB->modelSave(
            $item,
            BRAND_TABLENAME,
            BRAND_PRIMARYKEY
        );
    }
}
if (!function_exists('brandVirtual')) {
    /**
     * @param mixed[] $row
     *
     * @return mixed[]
     */
    function brandVirtual($row)
    {
        //@todo Remove this when admin is saving properly
        $row['img'] = basename($row['img']);

        return $row;
    }
}
