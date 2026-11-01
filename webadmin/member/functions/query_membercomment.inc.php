<?php
if (!defined('MEMBER_COMMENT_TABLENAME')) {
    define('MEMBER_COMMENT_TABLENAME', '`highwaygroup`.`new_members_comments`');
}
if (!defined('MEMBER_COMMENT_PRIMARYKEY')) {
    define('MEMBER_COMMENT_PRIMARYKEY', 'id');
}

if (!function_exists('memberCommentsFromDatabase')) {
    /**
     * @param int $member_id
     *
     * @return mixed[][]
     */
    function memberCommentsFromDatabase($member_id)
    {
        global $DB;

        $result = array();
        if ($query = $DB->query(sprintf(
            "SELECT * FROM %s WHERE `member_id` = %d",
            MEMBER_COMMENT_TABLENAME,
            $member_id
        ))) {
            while ($row = $DB->fetch_assoc($query)) {
                $result[$row[MEMBER_COMMENT_PRIMARYKEY]] = memberCommentVirtual($row);
            }
            $DB->free_result($query);
        }

        return $result;
    }
}
if (!function_exists('memberCommentAdd')) {
    /**
     * @param int    $member_id
     * @param string $comment
     *
     * @return int
     */
    function memberCommentAdd($member_id, $comment)
    {
        global $DB;

        if ($DB->query(sprintf(
            "INSERT INTO %s (`member_id`, `staff`, `date`, `comments`) VALUES (%d, %s, NOW(), %s)",
            MEMBER_COMMENT_TABLENAME,
            $member_id,
            formatSQLString($_SERVER['REMOTE_USER']),
            formatSQLString($comment)
        ))) {
            return $DB->insert_id();
        }

        return 0;
    }
}
if (!function_exists('memberCommentVirtual')) {
    /**
     * @param mixed[] $item
     *
     * @return mixed[]
     */
    function memberCommentVirtual($item)
    {
        $item['comment_time'] = (is_null($item['date'])) ? null : strtotime($item['date']);

        return $item;
    }
}
