<?php
if (!defined('PARTNER_COMMENT_TABLENAME')) {
    define('PARTNER_COMMENT_TABLENAME', '`media_partner_comments`');
}
if (!defined('PARTNER_COMMENT_PRIMARYKEY')) {
    define('PARTNER_COMMENT_PRIMARYKEY', 'id');
}

if (!function_exists('partnerCommentsFromDatabase')) {
    /**
     * @param int $member_id
     *
     * @return mixed[][]
     */
    function partnerCommentsFromDatabase($partner_id)
    {
        global $DB;

        $result = array();
        if ($query = $DB->query(sprintf(
            "SELECT * FROM %s WHERE `partner_id` = %d ORDER BY id desc",
            PARTNER_COMMENT_TABLENAME,
            $partner_id
        ))) {
            while ($row = $DB->fetch_assoc($query)) {
                $result[$row[PARTNER_COMMENT_PRIMARYKEY]] = partnerCommentVirtual($row);
            }
            $DB->free_result($query);
        }

        return $result;
    }
}
if (!function_exists('partnerCommentAdd')) {
    /**
     * @param int    $member_id
     * @param string $comment
     *
     * @return int
     */
    function partnerCommentAdd($partner_id, $comment)
    {
        global $DB;

        if ($DB->query(sprintf(
            "INSERT INTO %s (`partner_id`, `staff`, `date`, `comments`) VALUES (%d, %s, NOW(), %s)",
            PARTNER_COMMENT_TABLENAME,
            $partner_id,
            formatSQLString($_SERVER['REMOTE_USER']),
            formatSQLString($comment)
        ))) {
            return $DB->insert_id();
        }

        return 0;
    }
}
if (!function_exists('partnerCommentVirtual')) {
    /**
     * @param mixed[] $item
     *
     * @return mixed[]
     */
    function partnerCommentVirtual($item)
    {
        $item['comment_time'] = (is_null($item['date'])) ? null : strtotime($item['date']);

        return $item;
    }
}
