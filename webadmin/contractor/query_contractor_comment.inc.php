<?php
if (!defined('CONTRACTOR_COMMENT_TABLENAME')) {
    define('CONTRACTOR_COMMENT_TABLENAME', '`contractors_comments`');
}
if (!defined('CONTRACTOR_COMMENT_PRIMARYKEY')) {
    define('CONTRACTOR_COMMENT_PRIMARYKEY', 'id');
}
if (!defined('CONTRACTOR_COMMENT_SECONDARYKEY')) {
    define('CONTRACTOR_COMMENT_SECONDARYKEY', 'contractor_id');
}

if (!function_exists('contractorCommentDefault')) {
    /**
     * @return mixed[]
     */
    function contractorCommentDefault()
    {
        global $DB;

        return contractorCommentVirtual($DB->modelDefault(
            CONTRACTOR_COMMENT_TABLENAME,
            CONTRACTOR_COMMENT_PRIMARYKEY
        ));
    }
}
if (!function_exists('contractorCommentDelete')) {
    /**
     * @param int $id
     *
     * @return int
     */
    function contractorCommentDelete($id)
    {
        global $DB;

        return $DB->modelDelete(
            $id,
            CONTRACTOR_COMMENT_TABLENAME,
            CONTRACTOR_COMMENT_PRIMARYKEY
        );
    }
}
if (!function_exists('contractorCommentExists')) {
    /**
     * @param int $id
     *
     * @return bool
     */
    function contractorCommentExists($id)
    {
        global $DB;

        return $DB->modelExists(
            $id,
            CONTRACTOR_COMMENT_TABLENAME,
            CONTRACTOR_COMMENT_PRIMARYKEY
        );
    }
}
if (!function_exists('contractorCommentFromDatabase')) {
    /**
     * @param int $id
     *
     * @return mixed[]|null
     */
    function contractorCommentFromDatabase($id)
    {
        global $DB;

        if ($row = $DB->modelLoad(
            $id,
            CONTRACTOR_COMMENT_TABLENAME,
            CONTRACTOR_COMMENT_PRIMARYKEY
        )) {
            return contractorCommentVirtual($row);
        }

        return null;
    }
}
if (!function_exists('contractorCommentGetValue')) {
    /**
     * @param int    $id
     * @param string $field
     *
     * @return mixed
     */
    function contractorCommentGetValue($id, $field)
    {
        global $DB;

        if (array_key_exists($field, $DB->table_columns(
            CONTRACTOR_COMMENT_TABLENAME
        ))) {
            return $DB->modelGet(
                $id,
                $field,
                CONTRACTOR_COMMENT_TABLENAME,
                CONTRACTOR_COMMENT_PRIMARYKEY
            );
        }

        if ($model = contractorCommentFromDatabase($id)) {
            return $model[$field] ?? null;
        }

        return null;
    }
}
if (!function_exists('contractorCommentHasValue')) {
    /**
     * @param int    $id
     * @param string $field
     * @param mixed  $value
     *
     * @return bool
     */
    function contractorCommentHasValue($id, $field, $value)
    {
        return $value == contractorCommentGetValue($id, $field);
    }
}
if (!function_exists('contractorCommentQuery')) {
    /**
     * @param mixed[] $eq
     * @param mixed[] $lt
     * @param mixed[] $gt
     *
     * @return string
     */
    function contractorCommentQuery($eq = [], $lt = [], $gt = [])
    {
        global $DB;

        $field_types = getTableFields('`contractors_comments`');

        $whereAND = [];
        foreach ([
            '=' => $eq,
            '<' => $lt,
            '>' => $gt,
        ] as $op => $criteria) {
            foreach ($criteria as $field => $value) {
                if (array_key_exists($field, $field_types)) {
                    $whereOR = [];
                    if(is_array($value)) {
                        foreach ($value as $sub_value) {
                            $whereOR[] = sprintf("`%s` %s %s", $field, $op, $DB->escape($sub_value));
                        }
                    } else {
                        $whereOR[] = sprintf("`%s` %s %s", $field, $op, $DB->escape($value));
                    }
                    $whereAND[] = "	(" . implode(" OR ", $whereOR) . ")";
                }
            }
        }

        return (count($whereAND)) ? implode(" AND ", $whereAND) : "1";
    }
}
if (!function_exists('contractorCommentSearch')) {
    /**
     * @param string|null $where
     * @param string|null $order_by
     *
     * @return int[]
     */
    function contractorCommentSearch($where = null, $order_by = null)
    {
        global $DB;

        // Legacy accepts criteria as first parameter
        if (is_array($where)) {
            $where = contractorCommentQuery($where);
        }

        return $DB->query_list(sprintf(
            "SELECT `%s` FROM %s%s%s",
            CONTRACTOR_COMMENT_PRIMARYKEY,
            CONTRACTOR_COMMENT_TABLENAME,
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
if (!function_exists('contractorCommentSetValue')) {
    /**
     * @param int    $id
     * @param string $field
     * @param mixed  $value
     *
     * @return int
     */
    function contractorCommentSetValue($id, $field, $value)
    {
        global $DB;

        return $DB->modelSet(
            $id,
            $field,
            $value,
            CONTRACTOR_COMMENT_TABLENAME,
            CONTRACTOR_COMMENT_PRIMARYKEY
        );
    }
}
if (!function_exists('contractorCommentSort')) {
    /**
     * @param string|null $where
     * @param string|null $order_by
     *
     * @return int[]
     */
    function contractorCommentSort($where = null, $order_by = null)
    {
        // Legacy accepts list of id's as first parameter
        if (is_array($where)) {
            $where = contractorCommentQuery([
                CONTRACTOR_COMMENT_PRIMARYKEY => $where,
            ]);
        }

        return contractorCommentSearch($where, $order_by);
    }
}
if (!function_exists('contractorCommentToDatabase')) {
    /**
     * @param mixed[] $item
     *
     * @return int
     */
    function contractorCommentToDatabase(&$item)
    {
        global $DB;

        return $DB->modelSave(
            $item,
            CONTRACTOR_COMMENT_TABLENAME,
            CONTRACTOR_COMMENT_PRIMARYKEY
        );
    }
}
if (!function_exists('contractorCommentVirtual')) {
    /**
     * @param mixed[] $row
     *
     * @return mixed[]
     */
    function contractorCommentVirtual($row)
    {
        $row['comment_time'] = strtotime($row['comment_date']);

        return $row;
    }
}

if (!function_exists('contractorCommentsDelete')) {
    /**
     * @param int $id
     *
     * @return int
     */
    function contractorCommentsDelete($id)
    {
        global $DB;

        return $DB->childrenDelete(
            $id,
            CONTRACTOR_COMMENT_TABLENAME,
            CONTRACTOR_COMMENT_SECONDARYKEY
        );
    }
}
if (!function_exists('contractorCommentsFromDatabase')) {
    /**
     * @param int $id
     *
     * @return mixed[]|null
     */
    function contractorCommentsFromDatabase($id)
    {
        global $DB;

        $result = [];
        foreach ($DB->childrenLoad(
            $id,
            CONTRACTOR_COMMENT_TABLENAME,
            CONTRACTOR_COMMENT_PRIMARYKEY,
            CONTRACTOR_COMMENT_SECONDARYKEY
        ) as $k => $v) {
            $result[$k] = contractorCommentVirtual($v);
        }

        return $result;
    }
}
if (!function_exists('contractorCommentsToDatabase')) {
    /**
     * @param int       $id
     * @param mixed[][] $items
     *
     * @return void
     */
    function contractorCommentsToDatabase($id, &$items)
    {
        global $DB;

        $DB->childrenSave(
            $id,
            $items,
            CONTRACTOR_COMMENT_TABLENAME,
            CONTRACTOR_COMMENT_PRIMARYKEY,
            CONTRACTOR_COMMENT_SECONDARYKEY
        );
    }
}

if (!function_exists('contractorCommentAdd')) {
    function contractorCommentAdd($contractor_id, $comment, $type = 'admin')
    {
        global $DB, $_GLOBALS;
        $title = $sentto = '';
        if (is_array($comment)) {
            $title   = $comment['comment_title'];
            $sentto  = $comment['comment_sentto'];
            $comment = $comment['comment_text'];
        }
        $DB->query(sprintf(
            "INSERT INTO `contractors_comments` (`contractor_id`, `staff_id`, `comment_type`, `comment_date`, `comment_text`, `comment_title`, `comment_sentto`) VALUES (%d, %d, %s, NOW(), %s, %s, %s)",
            $contractor_id,
            $_GLOBALS['staff']['staff_id'],
            formatSQLString($type),
            formatSQLString($comment),
            formatSQLString($title),
            formatSQLString($sentto)
        ));
        return $DB->insert_id();
    }
}
