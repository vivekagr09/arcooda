<?php
if (!defined('MEMBER_WANTEDOFFER_TABLENAME')) {
    define('MEMBER_WANTEDOFFER_TABLENAME', "`highwaygroup`.`new_members_wanted_offers`");
}
/*
if (!defined('MEMBER_WANTEDOFFER_PRIMARYKEY')) {
define('MEMBER_WANTEDOFFER_PRIMARYKEY', "id");
}
 */
if (!defined('MEMBER_WANTEDOFFER_SECONDARYKEY')) {
    define('MEMBER_WANTEDOFFER_SECONDARYKEY', "wanted_id");
}

if (!function_exists('memberWantedOffersFromEmail')) {
    /**
     * @param string $email
     *
     * @return mixed[][]
     */
    function memberWantedOffersFromEmail($email)
    {
        global $DB;

        $result = array();
        if ($query = $DB->query(sprintf(
            "SELECT * FROM %s WHERE `email` = %s",
            MEMBER_WANTEDOFFER_TABLENAME,
            $DB->escape($email)
        ))) {
            while ($row = $DB->fetch_assoc($query)) {
                $result[] = $row;
            }
            $DB->free_result($query);
        }

        return $result;
    }
}
