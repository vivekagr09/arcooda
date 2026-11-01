<?php
if (!defined('WAREHOUSE_COMMENT_TABLENAME')) {
    define('WAREHOUSE_COMMENT_TABLENAME', '`warehouse_comments`');
}
if (!defined('WAREHOUSE_COMMENT_PRIMARYKEY')) {
    define('WAREHOUSE_COMMENT_PRIMARYKEY', 'id');
}

if (!function_exists('warehouseCommentsFromDatabase')) {
    /**
     * @param int $member_id
     *
     * @return mixed[][]
     */
    function warehouseCommentsFromDatabase($warehouse_id)
    {
        global $DB;

        $result = array();
        if ($query = $DB->query(sprintf(
            "SELECT * FROM %s WHERE `warehouse_id` = %d ORDER BY id desc",
            WAREHOUSE_COMMENT_TABLENAME,
            $warehouse_id
        ))) {
            while ($row = $DB->fetch_assoc($query)) {
                $result[$row[WAREHOUSE_COMMENT_PRIMARYKEY]] = warehouseCommentVirtual($row);
            }
            $DB->free_result($query);
        }

        return $result;
    }
}
if (!function_exists('warehouseCommentAdd')) {
    /**
     * @param int    $member_id
     * @param string $comment
     *
     * @return int
     */
    function warehouseCommentAdd($warehouse_id, $comment)
    {
        global $DB;

        if ($DB->query(sprintf(
            "INSERT INTO %s (`warehouse_id`, `staff`, `date`, `comments`) VALUES (%d, %s, NOW(), %s)",
            WAREHOUSE_COMMENT_TABLENAME,
            $warehouse_id,
            formatSQLString($_SERVER['REMOTE_USER']),
            formatSQLString($comment)
        ))) {
            return $DB->insert_id();
        }

        return 0;
    }
}
if (!function_exists('warehouseCommentVirtual')) {
    /**
     * @param mixed[] $item
     *
     * @return mixed[]
     */
    function warehouseCommentVirtual($item)
    {
        $item['comment_time'] = (is_null($item['date'])) ? null : strtotime($item['date']);

        return $item;
    }
}
