<?php
include_once __DIR__ . '/query_member.inc.php';

if (!defined('MEMBER_NOTIFYPARTY_TABLENAME')) {
    define('MEMBER_NOTIFYPARTY_TABLENAME', '`highwaygroup`.`new_members_notify`');
}
if (!defined('MEMBER_NOTIFYPARTY_PRIMARYKEY')) {
    define('MEMBER_NOTIFYPARTY_PRIMARYKEY', 'id');
}
if (!defined('MEMBER_NOTIFYPARTY_SECONDARYKEY')) {
    define('MEMBER_NOTIFYPARTY_SECONDARYKEY', 'member_id');
}

if (!function_exists('memberNotifyPartyDelete')) {
    /**
     * @param int $id
     *
     * @return int
     */
    function memberNotifyPartyDelete($id)
    {
        global $DB;

        if ($DB->query(sprintf(
            "DELETE FROM %s WHERE `%s` = %d LIMIT 1",
            MEMBER_NOTIFYPARTY_TABLENAME,
            MEMBER_NOTIFYPARTY_PRIMARYKEY,
            $id
        ))) {
            return $DB->affected_rows();
        }

        return 0;
    }
}
if (!function_exists('memberNotifyPartiesFromDatabase')) {
    /**
     * @param int $member_id
     *
     * @return mixed[][]
     */
    function memberNotifyPartiesFromDatabase($member_id)
    {
        global $DB;

        $result = array();
        if ($query = $DB->query(sprintf(
            "SELECT * FROM %s WHERE `member_id` = %d",
            MEMBER_NOTIFYPARTY_TABLENAME,
            $member_id
        ))) {
            while ($row = $DB->fetch_assoc($query)) {
                $result[$row[MEMBER_NOTIFYPARTY_PRIMARYKEY]] = memberNotifyPartyVirtual($row);
            }
            $DB->free_result($query);
        }

        return $result;
    }
}
if (!function_exists('memberNotifyPartiesFromPost')) {
    /**
     * @param mixed[] $posted
     *
     * @return mixed[][]
     */
    function memberNotifyPartiesFromPost($posted)
    {
        $member_id            = intval($posted['member_id']);
        $affected             = explode('|', $posted['notify_affected']);
        $table                = $posted['notify'];
        $member_notifyparties = memberNotifyPartiesFromDatabase($member_id);

        foreach ($affected as $key) {
            foreach ($member_notifyparties as $key2 => $member_notifyparty) {
                if ($member_notifyparty[MEMBER_NOTIFYPARTY_PRIMARYKEY] == $key) {
                    unset($member_notifyparties[$key2]);
                }
            }
        }

        if (is_array($table)) {
            foreach ($table as $member_notifyparty) {
                $member_notifyparty['member_id'] = $member_id;
                foreach (getTableFields(MEMBER_NOTIFYPARTY_TABLENAME) as $row) {
                    if (!isset($member_notifyparty[$row[0]])) {
                        $member_notifyparty[$row[0]] = $row[4];
                    }
                }

                $member_notifyparties[] = $member_notifyparty;
            }
        }

        return $member_notifyparties;
    }
}
if (!function_exists('memberNotifyPartiesToDatabase')) {
    function memberNotifyPartiesToDatabase($member_id, $member_notifyparties)
    {
        global $DB;
        $member_id = intval($member_id);
        if (0 == $member_id) {
            return;
        }

        $DB->query(sprintf(
            "DELETE FROM %s WHERE `member_id` = %d",
            MEMBER_NOTIFYPARTY_TABLENAME,
            $member_id
        ));

        $fields = getTableFields(MEMBER_NOTIFYPARTY_TABLENAME);
        foreach ($member_notifyparties as $member_notifyparty) {
            $insert_vals         = array();
            $member_notifyparty['member_id'] = $member_id;
            if (0 == $member_notifyparty['id']) {
                unset($member_notifyparty['id']);
            }

            foreach ($member_notifyparty as $key => $value) {
                if (isset($fields[$key])) {
                    $insert_vals[$key] = formatSQLString($value);
                }
            }

            if (count($insert_vals)) {
                $DB->query("INSERT INTO `highwaygroup`.`new_members_notify`(`" . implode('`, `', array_keys($insert_vals)) . "`) VALUES(" . implode(", ", $insert_vals) . ")");
            }

        }
    }
}
if (!function_exists('memberNotifyPartyToDatabase')) {
    /**
     * @param int     $member_id
     * @param mixed[] $member_notifyparty
     *
     * @return int
     */
    function memberNotifyPartyToDatabase($member_id, &$member_notifyparty)
    {
        global $DB;

        $member_notifyparty[MEMBER_NOTIFYPARTY_SECONDARYKEY] = $member_id;

        return $DB->modelSave($member_notifyparty, MEMBER_NOTIFYPARTY_TABLENAME, MEMBER_NOTIFYPARTY_PRIMARYKEY);
    }
}
if (!function_exists('memberNotifyPartyVirtual')) {
    /**
     * @param mixed[] $item
     *
     * @return mixed[]
     */
    function memberNotifyPartyVirtual($item)
    {
        $item['address_full'] = memberFormatAddress($item);

        return $item;
    }
}

if (!function_exists('memberNotifyPartySetDefault')) {
    /**
     * @param int $member_id
     * @param int $member_notifyparty_id
     *
     * @return bool
     */
    function memberNotifyPartySetDefault($member_id, $member_notifyparty_id)
    {
        global $DB;

        if (!$DB->query_first(sprintf(
            "SELECT 1 FROM %s WHERE `%s` = %d AND `%s` = %d LIMIT 1",
            MEMBER_NOTIFYPARTY_TABLENAME,
            MEMBER_NOTIFYPARTY_PRIMARYKEY,
            $member_notifyparty_id,
            MEMBER_NOTIFYPARTY_SECONDARYKEY,
            $member_id
        ), MYSQLI_ASSOC)) {
            return false;
        }

        if ($DB->query(sprintf(
            "UPDATE %s SET `default` = IF(`%s` = %d, 'y', 'n') WHERE `%s` = %d",
            MEMBER_NOTIFYPARTY_TABLENAME,
            MEMBER_NOTIFYPARTY_PRIMARYKEY,
            $member_notifyparty_id,
            MEMBER_NOTIFYPARTY_SECONDARYKEY,
            $member_id
        ))) {
            return true;
        }

        return false;
    }
}
