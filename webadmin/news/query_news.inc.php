<?php
if (!defined('NEWS_TABLENAME')) {
    define('NEWS_TABLENAME', '`news`');
}
if (!defined('NEWS_PRIMARYKEY')) {
    define('NEWS_PRIMARYKEY', 'news_id');
}

if (!function_exists('newsDefault')) {
    /**
     * @return mixed[]
     */
    function newsDefault()
    {
        global $DB;

        return newsVirtual($DB->modelDefault(
            NEWS_TABLENAME,
            NEWS_PRIMARYKEY
        ));
    }
}
if (!function_exists('newsDelete')) {
    /**
     * @param int $id
     *
     * @return int
     */
    function newsDelete($id)
    {
        global $DB;

        return $DB->modelDelete(
            $id,
            NEWS_TABLENAME,
            NEWS_PRIMARYKEY
        );
    }
}
if (!function_exists('newsExists')) {
    /**
     * @param int $id
     *
     * @return bool
     */
    function newsExists($id)
    {
        global $DB;

        return $DB->modelExists(
            $id,
            NEWS_TABLENAME,
            NEWS_PRIMARYKEY
        );
    }
}
if (!function_exists('newsFromDatabase')) {
    /**
     * @param int $id
     *
     * @return mixed[]|null
     */
    function newsFromDatabase($id)
    {
        global $DB;

        if ($row = $DB->modelLoad(
            $id,
            NEWS_TABLENAME,
            NEWS_PRIMARYKEY
        )) {
            return newsVirtual($row);
        }

        return null;
    }
}
if (!function_exists('newsGetValue')) {
    /**
     * @param int    $id
     * @param string $field
     *
     * @return mixed
     */
    function newsGetValue($id, $field)
    {
        global $DB;

        if (array_key_exists($field, $DB->table_columns(
            NEWS_TABLENAME
        ))) {
            return $DB->modelGet(
                $id,
                $field,
                NEWS_TABLENAME,
                NEWS_PRIMARYKEY
            );
        }

        if ($model = newsFromDatabase($id)) {
            return $model[$field] ?? null;
        }

        return null;
    }
}
if (!function_exists('newsHasValue')) {
    /**
     * @param int    $id
     * @param string $field
     * @param mixed  $value
     *
     * @return bool
     */
    function newsHasValue($id, $field, $value)
    {
        return $value == newsGetValue($id, $field);
    }
}
if (!function_exists('newsQuery')) {
    /**
     * @param mixed[] $eq
     * @param mixed[] $lt
     * @param mixed[] $gt
     *
     * @return string
     */
    function newsQuery($eq = array(), $lt = array(), $gt = array())
    {
        global $DB;

        if (empty($eq) && empty($lt) && empty($gt)) {
            return '1';
        }

        $field_types = getTableFields(NEWS_TABLENAME);

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
if (!function_exists('newsSearch')) {
    /**
     * @param string|null $where
     * @param string|null $order_by
     *
     * @return int[]
     */
    function newsSearch($where = null)
    {
        // Legacy accepts criteria as first parameter
        if (is_array($where)) {
            $where = newsQuery($where);
        }

        return array_reverse(newsSort($where, NEWS_PRIMARYKEY));
    }
}
if (!function_exists('newsSetValue')) {
    /**
     * @param int    $id
     * @param string $field
     * @param mixed  $value
     *
     * @return int
     */
    function newsSetValue($id, $field, $value)
    {
        global $DB;

        return $DB->modelSet(
            $id,
            $field,
            $value,
            NEWS_TABLENAME,
            NEWS_PRIMARYKEY
        );
    }
}
if (!function_exists('newsSort')) {
    /**
     * @param string|null $where
     * @param string|null $order_by
     *
     * @return int[]
     */
    function newsSort($where = null, $order_by = null)
    {
        global $DB;

        // Legacy accepts list of id's as first parameter
        if (is_array($where)) {
            $where = newsQuery(array(
                NEWS_PRIMARYKEY => $where,
            ));
        }

        return $DB->query_list(sprintf(
            "SELECT `%s` FROM %s%s%s",
            NEWS_PRIMARYKEY,
            NEWS_TABLENAME,
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
if (!function_exists('newsToDatabase')) {
    /**
     * @param mixed[] $item
     *
     * @return int
     */
    function newsToDatabase(&$item)
    {
        global $DB;

        return $DB->modelSave(
            $item,
            NEWS_TABLENAME,
            NEWS_PRIMARYKEY
        );
    }
}
if (!function_exists('newsVirtual')) {
    /**
     * @param mixed[] $row
     *
     * @return mixed[]
     */
    function newsVirtual($row)
    {
        return $row;
    }
}
