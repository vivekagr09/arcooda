<?php
if (!defined('MEMBER_FORWARDER_TABLENAME')) {
    define('MEMBER_FORWARDER_TABLENAME', '`highwaygroup`.`new_members_forwarders`');
}
if (!defined('MEMBER_FORWARDER_PRIMARYKEY')) {
    define('MEMBER_FORWARDER_PRIMARYKEY', 'id');
}

if (!function_exists('memberForwarderDelete')) {
    /**
     * @param int $id
     *
     * @return int
     */
    function memberForwarderDelete($id)
    {
        global $DB;

        if ($DB->query(sprintf(
            "DELETE FROM %s WHERE `%s` = %d LIMIT 1",
            MEMBER_FORWARDER_TABLENAME,
            MEMBER_FORWARDER_PRIMARYKEY,
            $id
        ))) {
            return 1;
        }

        return 0;
    }
}
if (!function_exists('memberForwardersFromDatabase')) {
    /**
     * @param int $member_id
     *
     * @return mixed[][]
     */
    function memberForwardersFromDatabase($member_id)
    {
        global $DB;

        $result = array();
        if ($query = $DB->query(sprintf(
            "SELECT * FROM %s WHERE `member_id` = %d",
            MEMBER_FORWARDER_TABLENAME,
            $member_id
        ))) {
            while ($row = $DB->fetch_assoc($query)) {
                $result[] = $row;
            }
            $DB->free_result($query);
        }

        return $result;
    }
}
if (!function_exists('memberForwardersToDatabase')) {
    /**
     * @param int       $member_id
     * @param mixed[][] $current
     *
     * @return void
     */
    function memberForwardersToDatabase($member_id, $current)
    {
        global $DB;

        $DB->query(sprintf(
            "DELETE FROM %s WHERE `member_id` = %d",
            MEMBER_FORWARDER_TABLENAME,
            $member_id
        ));
        $fields = getTableFields(MEMBER_FORWARDER_TABLENAME);
        foreach ($current as $forwarder) {
            $insert_vals            = array();
            $forwarder['member_id'] = intval($member_id);
            if (0 == $forwarder[MEMBER_FORWARDER_PRIMARYKEY]) {
                unset($forwarder[MEMBER_FORWARDER_PRIMARYKEY]);
            }

            foreach ($forwarder as $key => $value) {
                if (isset($fields[$key])) {
                    $insert_vals[$key] = formatSQLString($value);
                }
            }

            if (count($insert_vals)) {
                $DB->query(sprintf(
                    "INSERT INTO %s (`%s`) VALUES (%s)",
                    MEMBER_FORWARDER_TABLENAME,
                    implode('`, `', array_keys($insert_vals)),
                    implode(", ", array_values($insert_vals))
                ));
            }

        }
    }
}
