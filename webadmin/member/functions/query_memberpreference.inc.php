<?php
if (!defined('MEMBER_PREFERENCE_TABLENAME')) {
    define('MEMBER_PREFERENCE_TABLENAME', '`highwaygroup`.`new_members_preferences`');
}
if (!defined('MEMBER_PREFERENCE_PRIMARYKEY')) {
    define('MEMBER_PREFERENCE_PRIMARYKEY', 'member_id');
}

if (!function_exists('memberCurrency')) {
    /**
     * @param int $member_id
     *
     * @return bool
     */
    function memberCurrency($member_id)
    {
        global $DB;

        if (!$member_id) {
            return false;
        }

        if ($row = $DB->query_first(sprintf(
            "SELECT `currency` FROM %s WHERE `%s` = %d LIMIT 1",
            MEMBER_PREFERENCE_TABLENAME,
            MEMBER_PREFERENCE_PRIMARYKEY,
            $member_id
        ), MYSQLI_ASSOC)) {
            return $row['currency'];
        }

        return false;
    }
}
if (!function_exists('memberUpdatePreferences')) {
    /**
     * @param int    $member_id
     * @param string $field
     * @param mixed  $newValue
     *
     * @return void
     */
    function memberUpdatePreferences($member_id, $field, $newValue)
    {
        global $DB;

        if (in_array($field, array('mailing_list'))) {
            $DB->query(sprintf(
                "INSERT INTO %s (`%s`, `%s`) VALUES (%d, %s) ON DUPLICATE KEY UPDATE `mailing_list` = %s",
                MEMBER_PREFERENCE_TABLENAME,
                MEMBER_PREFERENCE_PRIMARYKEY,
                $field,
                $member_id,
                formatSQLString($newValue),
                formatSQLString($newValue)
            ));
        } elseif (in_array($field, array('products_per_page', 'news_per_page', 'currency'))) {
            $DB->query(sprintf(
                "UPDATE `highwaygroup`.`new_members` SET `%s` = %s WHERE `%s` = %d LIMIT 1",
                $field,
                formatSQLString($newValue),
                MEMBER_PREFERENCE_PRIMARYKEY,
                $member_id
            ));
        }

    }
}
