<?php
if (!defined('NEWS_VIDEO_TABLENAME')) {
    define('NEWS_VIDEO_TABLENAME', '`news_videos`');
}
if (!defined('NEWS_VIDEO_PRIMARYKEY')) {
    define('NEWS_VIDEO_PRIMARYKEY', 'id');
}
if (!defined('NEWS_VIDEO_SECONDARYKEY')) {
    define('NEWS_VIDEO_SECONDARYKEY', 'news_id');
}

if (!function_exists('newsVideoDefault')) {
    /**
     * @return mixed[]
     */
    function newsVideoDefault()
    {
        global $DB;

        return newsVideoVirtual($DB->modelDefault(
            NEWS_VIDEO_TABLENAME,
            NEWS_VIDEO_PRIMARYKEY
        ));
    }
}
if (!function_exists('newsVideoDelete')) {
    /**
     * @param int $id
     *
     * @return int
     */
    function newsVideoDelete($id)
    {
        global $DB;

        return $DB->modelDelete(
            $id,
            NEWS_VIDEO_TABLENAME,
            NEWS_VIDEO_PRIMARYKEY
        );
    }
}
if (!function_exists('newsVideoExists')) {
    /**
     * @param int $id
     *
     * @return bool
     */
    function newsVideoExists($id)
    {
        global $DB;

        return $DB->modelExists(
            $id,
            NEWS_VIDEO_TABLENAME,
            NEWS_VIDEO_PRIMARYKEY
        );
    }
}
if (!function_exists('newsVideoFromDatabase')) {
    /**
     * @param int $id
     *
     * @return mixed[]|null
     */
    function newsVideoFromDatabase($id)
    {
        global $DB;

        if ($row = $DB->modelLoad(
            $id,
            NEWS_VIDEO_TABLENAME,
            NEWS_VIDEO_PRIMARYKEY
        )) {
            return newsVideoVirtual($row);
        }

        return null;
    }
}
if (!function_exists('newsVideoGetValue')) {
    /**
     * @param int    $id
     * @param string $field
     *
     * @return mixed
     */
    function newsVideoGetValue($id, $field)
    {
        global $DB;

        if (array_key_exists($field, $DB->table_columns(
            NEWS_VIDEO_TABLENAME
        ))) {
            return $DB->modelGet(
                $id,
                $field,
                NEWS_VIDEO_TABLENAME,
                NEWS_VIDEO_PRIMARYKEY
            );
        }

        if ($model = newsVideoFromDatabase($id)) {
            return $model[$field] ?? null;
        }

        return null;
    }
}
if (!function_exists('newsVideoHasValue')) {
    /**
     * @param int    $id
     * @param string $field
     * @param mixed  $value
     *
     * @return bool
     */
    function newsVideoHasValue($id, $field, $value)
    {
        return $value == newsVideoGetValue($id, $field);
    }
}
if (!function_exists('newsVideoQuery')) {
    /**
     * @param mixed[] $eq
     * @param mixed[] $lt
     * @param mixed[] $gt
     *
     * @return string
     */
    function newsVideoQuery($eq = array(), $lt = array(), $gt = array())
    {
        global $DB;

        if (empty($eq) && empty($lt) && empty($gt)) {
            return '1';
        }

        $field_types = getTableFields(NEWS_VIDEO_TABLENAME);

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
if (!function_exists('newsVideoSearch')) {
    /**
     * @param string|null $where
     * @param string|null $order_by
     *
     * @return int[]
     */
    function newsVideoSearch($where = null, $order_by = null)
    {
        global $DB;

        // Legacy accepts criteria as first parameter
        if (is_array($where)) {
            $where = newsVideoQuery($where);
        }

        return $DB->query_list(sprintf(
            "SELECT `%s` FROM %s%s%s",
            NEWS_VIDEO_PRIMARYKEY,
            NEWS_VIDEO_TABLENAME,
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
if (!function_exists('newsVideoSetValue')) {
    /**
     * @param int    $id
     * @param string $field
     * @param mixed  $value
     *
     * @return int
     */
    function newsVideoSetValue($id, $field, $value)
    {
        global $DB;

        return $DB->modelSet(
            $id,
            $field,
            $value,
            NEWS_VIDEO_TABLENAME,
            NEWS_VIDEO_PRIMARYKEY
        );
    }
}
if (!function_exists('newsVideoSort')) {
    /**
     * @param string|null $where
     * @param string|null $order_by
     *
     * @return int[]
     */
    function newsVideoSort($where = null, $order_by = null)
    {
        // Legacy accepts list of id's as first parameter
        if (is_array($where)) {
            $where = newsVideoQuery(array(
                NEWS_VIDEO_PRIMARYKEY => $where,
            ));
        }

        return newsVideoSearch($where, $order_by);
    }
}
if (!function_exists('newsVideoToDatabase')) {
    /**
     * @param mixed[] $item
     *
     * @return int
     */
    function newsVideoToDatabase(&$item)
    {
        global $DB;

        return $DB->modelSave(
            $item,
            NEWS_VIDEO_TABLENAME,
            NEWS_VIDEO_PRIMARYKEY
        );
    }
}
if (!function_exists('newsVideoVirtual')) {
    /**
     * @param mixed[] $row
     *
     * @return mixed[]
     */
    function newsVideoVirtual($row)
    {
        return $row;
    }
}

if (!function_exists('newsVideosDelete')) {
    /**
     * @param int $id
     *
     * @return int
     */
    function newsVideosDelete($id)
    {
        global $DB;

        return $DB->childrenDelete(
            $id,
            NEWS_VIDEO_TABLENAME,
            NEWS_VIDEO_SECONDARYKEY
        );
    }
}
if (!function_exists('newsVideosFromDatabase')) {
    /**
     * @param int $id
     *
     * @return mixed[]|null
     */
    function newsVideosFromDatabase($id)
    {
        global $DB;

        $result = array();
        foreach ($DB->childrenLoad(
            $id,
            NEWS_VIDEO_TABLENAME,
            NEWS_VIDEO_PRIMARYKEY,
            NEWS_VIDEO_SECONDARYKEY
        ) as $k => $v) {
            $result[$k] = newsVideoVirtual($v);
        }

        return $result;
    }
}
if (!function_exists('newsVideosToDatabase')) {
    /**
     * @param int       $id
     * @param mixed[][] $items
     *
     * @return void
     */
    function newsVideosToDatabase($id, &$items)
    {
        global $DB;

        $DB->childrenSave(
            $id,
            $items,
            NEWS_VIDEO_TABLENAME,
            NEWS_VIDEO_PRIMARYKEY,
            NEWS_VIDEO_SECONDARYKEY
        );
    }
}
