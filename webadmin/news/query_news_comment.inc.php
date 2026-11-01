<?php
if (!defined('NEWS_COMMENT_TABLENAME')) {
    define('NEWS_COMMENT_TABLENAME', '`news_comments`');
}
if (!defined('NEWS_COMMENT_PRIMARYKEY')) {
    define('NEWS_COMMENT_PRIMARYKEY', 'id');
}
if (!defined('NEWS_COMMENT_SECONDARYKEY')) {
    define('NEWS_COMMENT_SECONDARYKEY', 'news_id');
}

if (!function_exists('newsCommentDefault')) {
    /**
     * @return mixed[]
     */
    function newsCommentDefault()
    {
        global $DB;

        return newsCommentVirtual($DB->modelDefault(
            NEWS_COMMENT_TABLENAME,
            NEWS_COMMENT_PRIMARYKEY
        ));
    }
}
if (!function_exists('newsCommentDelete')) {
    /**
     * @param int $id
     *
     * @return int
     */
    function newsCommentDelete($id)
    {
        global $DB;

        return $DB->modelDelete(
            $id,
            NEWS_COMMENT_TABLENAME,
            NEWS_COMMENT_PRIMARYKEY
        );
    }
}
if (!function_exists('newsCommentExists')) {
    /**
     * @param int $id
     *
     * @return bool
     */
    function newsCommentExists($id)
    {
        global $DB;

        return $DB->modelExists(
            $id,
            NEWS_COMMENT_TABLENAME,
            NEWS_COMMENT_PRIMARYKEY
        );
    }
}
if (!function_exists('newsCommentFromDatabase')) {
    /**
     * @param int $id
     *
     * @return mixed[]|null
     */
    function newsCommentFromDatabase($id)
    {
        global $DB;

        if ($row = $DB->modelLoad(
            $id,
            NEWS_COMMENT_TABLENAME,
            NEWS_COMMENT_PRIMARYKEY
        )) {
            return newsCommentVirtual($row);
        }

        return null;
    }
}
if (!function_exists('newsCommentGetValue')) {
    /**
     * @param int    $id
     * @param string $field
     *
     * @return mixed
     */
    function newsCommentGetValue($id, $field)
    {
        global $DB;

        if (array_key_exists($field, $DB->table_columns(
            NEWS_COMMENT_TABLENAME
        ))) {
            return $DB->modelGet(
                $id,
                $field,
                NEWS_COMMENT_TABLENAME,
                NEWS_COMMENT_PRIMARYKEY
            );
        }

        if ($model = newsCommentFromDatabase($id)) {
            return $model[$field] ?? null;
        }

        return null;
    }
}
if (!function_exists('newsCommentHasValue')) {
    /**
     * @param int    $id
     * @param string $field
     * @param mixed  $value
     *
     * @return bool
     */
    function newsCommentHasValue($id, $field, $value)
    {
        return $value == newsCommentGetValue($id, $field);
    }
}
if (!function_exists('newsCommentQuery')) {
    /**
     * @param mixed[] $eq
     * @param mixed[] $lt
     * @param mixed[] $gt
     *
     * @return string
     */
    function newsCommentQuery($eq = array(), $lt = array(), $gt = array())
    {
        global $DB;

        if (empty($eq) && empty($lt) && empty($gt)) {
            return '1';
        }

        $field_types = getTableFields(NEWS_COMMENT_TABLENAME);

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
if (!function_exists('newsCommentSearch')) {
    /**
     * @param string|null $where
     * @param string|null $order_by
     *
     * @return int[]
     */
    function newsCommentSearch($where = null, $order_by = null)
    {
        global $DB;

        // Legacy accepts criteria as first parameter
        if (is_array($where)) {
            $where = newsCommentQuery($where);
        }

        return $DB->query_list(sprintf(
            "SELECT `%s` FROM %s%s%s",
            NEWS_COMMENT_PRIMARYKEY,
            NEWS_COMMENT_TABLENAME,
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
if (!function_exists('newsCommentSetValue')) {
    /**
     * @param int    $id
     * @param string $field
     * @param mixed  $value
     *
     * @return int
     */
    function newsCommentSetValue($id, $field, $value)
    {
        global $DB;

        return $DB->modelSet(
            $id,
            $field,
            $value,
            NEWS_COMMENT_TABLENAME,
            NEWS_COMMENT_PRIMARYKEY
        );
    }
}
if (!function_exists('newsCommentSort')) {
    /**
     * @param string|null $where
     * @param string|null $order_by
     *
     * @return int[]
     */
    function newsCommentSort($where = null, $order_by = null)
    {
        // Legacy accepts list of id's as first parameter
        if (is_array($where)) {
            $where = newsCommentQuery(array(
                NEWS_COMMENT_PRIMARYKEY => $where,
            ));
        }

        return newsCommentSearch($where, $order_by);
    }
}
if (!function_exists('newsCommentToDatabase')) {
    /**
     * @param mixed[] $item
     *
     * @return int
     */
    function newsCommentToDatabase(&$item)
    {
        global $DB;

        return $DB->modelSave(
            $item,
            NEWS_COMMENT_TABLENAME,
            NEWS_COMMENT_PRIMARYKEY
        );
    }
}
if (!function_exists('newsCommentVirtual')) {
    /**
     * @param mixed[] $row
     *
     * @return mixed[]
     */
    function newsCommentVirtual($row)
    {
        return $row;
    }
}

if (!function_exists('newsCommentsDelete')) {
    /**
     * @param int $id
     *
     * @return int
     */
    function newsCommentsDelete($id)
    {
        global $DB;

        return $DB->childrenDelete(
            $id,
            NEWS_COMMENT_TABLENAME,
            NEWS_COMMENT_SECONDARYKEY
        );
    }
}
if (!function_exists('newsCommentsFromDatabase')) {
    /**
     * @param int $id
     *
     * @return mixed[]|null
     */
    function newsCommentsFromDatabase($id)
    {
        global $DB;

        $result = array();
        foreach ($DB->childrenLoad(
            $id,
            NEWS_COMMENT_TABLENAME,
            NEWS_COMMENT_PRIMARYKEY,
            NEWS_COMMENT_SECONDARYKEY
        ) as $k => $v) {
            $result[$k] = newsCommentVirtual($v);
        }

        return $result;
    }
}
if (!function_exists('newsCommentsToDatabase')) {
    /**
     * @param int       $id
     * @param mixed[][] $items
     *
     * @return void
     */
    function newsCommentsToDatabase($id, &$items)
    {
        global $DB;

        $DB->childrenSave(
            $id,
            $items,
            NEWS_COMMENT_TABLENAME,
            NEWS_COMMENT_PRIMARYKEY,
            NEWS_COMMENT_SECONDARYKEY
        );
    }
}
