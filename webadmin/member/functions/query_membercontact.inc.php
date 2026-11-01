<?php
if (!defined('MEMBER_CONTACT_TABLENAME')) {
    define('MEMBER_CONTACT_TABLENAME', "`highwaygroup`.`new_members_contacts`");
}
if (!defined('MEMBER_CONTACT_PRIMARYKEY')) {
    define('MEMBER_CONTACT_PRIMARYKEY', "id");
}
if (!defined('MEMBER_CONTACT_SECONDARYKEY')) {
    define('MEMBER_CONTACT_SECONDARYKEY', "member_id");
}

if (!function_exists('memberContactDelete')) {
    /**
     * @param int $id
     *
     * @return int
     */
    function memberContactDelete($id)
    {
        global $DB;

        return $DB->modelDelete(
            $id,
            MEMBER_CONTACT_TABLENAME,
            MEMBER_CONTACT_PRIMARYKEY
        );
    }
}
if (!function_exists('memberContactToDatabase')) {
    function memberContactToDatabase($id, &$member_contact)
    {
        global $DB;

        $member_contact[MEMBER_CONTACT_SECONDARYKEY] = $id;

        return $DB->modelSave(
            $member_contact,
            MEMBER_CONTACT_TABLENAME,
            MEMBER_CONTACT_PRIMARYKEY
        );
    }
}
if (!function_exists('memberContactDefault')) {
    function memberContactDefault()
    {
        global $DB;

        return memberContactVirtual($DB->modelDefault(
            MEMBER_CONTACT_TABLENAME,
            MEMBER_CONTACT_PRIMARYKEY
        ));
    }
}
if (!function_exists('memberContactVirtual')) {
    function memberContactVirtual($result)
    {
        return $result;
    }
}
if (!function_exists('memberContactsFromDatabase')) {
    /**
     * @param int  $id
     * @param bool $sendCC
     *
     * @return mixed[][]
     */
    function memberContactsFromDatabase($id, $sendCC = false)
    {
        global $DB;

        $result = array();
        foreach ($DB->childrenLoad(
            $id,
            MEMBER_CONTACT_TABLENAME,
            MEMBER_CONTACT_PRIMARYKEY,
            MEMBER_CONTACT_SECONDARYKEY
        ) as $k => $v) {
            if ($sendCC && 'y' !== $v['send_email_cc']) {
                continue;
            }
            $result[$k] = memberContactVirtual($v);
        }

        return $result;
    }
}
if (!function_exists('memberContactsToDatabase')) {
    /**
     * @param int       $id
     * @param mixed[][] $current
     *
     * @return void
     */
    function memberContactsToDatabase($id, $current)
    {
        global $DB;
        $id = intval($id);
        if (0 == $id) {
            return;
        }

        $DB->query(sprintf(
            "DELETE FROM %s WHERE `%s` = %d",
            MEMBER_CONTACT_TABLENAME,
            MEMBER_CONTACT_SECONDARYKEY,
            $id
        ));
        $fields = getTableFields(MEMBER_CONTACT_TABLENAME);
        foreach ($current as $contact) {
            $insert_vals                          = array();
            $contact[MEMBER_CONTACT_SECONDARYKEY] = $id;
            if (!array_key_exists(MEMBER_CONTACT_PRIMARYKEY, $contact) || 0 == $contact[MEMBER_CONTACT_PRIMARYKEY]) {
                unset($contact[MEMBER_CONTACT_PRIMARYKEY]);
            }

            foreach ($contact as $key => $value) {
                if (array_key_exists($key, $fields)) {
                    $insert_vals[$key] = formatSQLString($value);
                }
            }

            if (count($insert_vals)) {
                $DB->query(sprintf(
                    "INSERT INTO %s (`%s`) VALUES (%s)",
                    MEMBER_CONTACT_TABLENAME,
                    implode('`, `', array_keys($insert_vals)),
                    implode(", ", array_values($insert_vals))
                ));
            }
        }
    }
}
if (!function_exists('memberContactSetMain')) {
    /**
     * @param int $id
     * @param int $contact_id
     *
     * @return bool
     */
    function memberContactSetMain($id, $contact_id)
    {
        global $DB;

        if (!$DB->query_first(sprintf(
            "SELECT 1 FROM %s WHERE `%s` = %d AND `%s` = %d LIMIT 1",
            MEMBER_CONTACT_TABLENAME,
            MEMBER_CONTACT_PRIMARYKEY,
            $contact_id,
            MEMBER_CONTACT_SECONDARYKEY,
            $id
        ), MYSQLI_ASSOC)) {
            return false;
        }

        if ($DB->query(sprintf(
            "UPDATE %s SET `main` = IF(`%s` = %d, 'y', 'n'), `send_email_cc` =  IF(`%s` = %d, 'y', 'n') WHERE `%s` = %d",
            MEMBER_CONTACT_TABLENAME,
            MEMBER_CONTACT_PRIMARYKEY,
            $contact_id,
            MEMBER_CONTACT_PRIMARYKEY,
            $contact_id,
            MEMBER_CONTACT_SECONDARYKEY,
            $id
        ))) {
            return true;
        }

        return false;
    }
}
