<?php
if (!defined('MEMBER_MESSAGE_TABLENAME')) {
    define('MEMBER_MESSAGE_TABLENAME', '`highwaygroup`.`new_members_messages`');
}
if (!defined('MEMBER_MESSAGE_PRIMARYKEY')) {
    define('MEMBER_MESSAGE_PRIMARYKEY', 'message_id');
}

if (!function_exists('memberMessageToDatabase')) {
    /**
     * @param int    $member_id
     * @param string $message
     *
     * @return int
     */
    function memberMessageToDatabase($member_id, $message)
    {
        global $DB, $_GLOBALS;

        $DB->query(sprintf(
            "INSERT INTO %s (`member_id`, `staff_id`, `message_date`, `message_notes`) VALUES (%d, %d, NOW(), %s)",
            MEMBER_MESSAGE_TABLENAME,
            $member_id,
            (array_key_exists('staff', $_GLOBALS) && array_key_exists('id', $_GLOBALS['staff'])) ? $_GLOBALS['staff']['id'] : 0,
            formatSQLString($message)
        ));
        return $DB->insert_id();
    }
}
if (!function_exists('memberMessagesFromDatabase')) {
    /**
     * @param int $member_id
     *
     * @return mixed[][]
     */
    function memberMessagesFromDatabase($member_id)
    {
        global $DB;

        $result = array();
        if ($query = $DB->query(sprintf(
            "SELECT * FROM %s WHERE `member_id` = %d",
            MEMBER_MESSAGE_TABLENAME,
            $member_id
        ))) {
            while ($row = $DB->fetch_assoc($query)) {
                $result[$row[MEMBER_MESSAGE_PRIMARYKEY]] = memberMessageVirtual($row);
            }
            $DB->free_result($query);
        }

        return $result;
    }
}
if (!function_exists('memberMessageVirtual')) {
    /**
     * @param mixed[] $item
     *
     * @return mixed[]
     */
    function memberMessageVirtual($item)
    {
        $item['message_time'] = (is_null($item['message_date'])) ? null : strtotime($item['message_date']);

        return $item;
    }
}
