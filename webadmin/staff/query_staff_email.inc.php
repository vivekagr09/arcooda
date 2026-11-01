<?php
include_once __DIR__ . '/query_staff.inc.php';

if (!defined('STAFF_EMAIL_TABLENAME')) {
    define('STAFF_EMAIL_TABLENAME', '`highwaygroup`.`users_email`');
}
if (!defined('STAFF_EMAIL_SECONDARYKEY')) {
    define('STAFF_EMAIL_SECONDARYKEY', 'staff_id');
}

if (!function_exists('staffEmailDelete')) {
    /**
     * @param int $staff_id
     * @param int $site_id
     *
     * @return int
     */
    function staffEmailDelete($staff_id, $site_id)
    {
        global $DB;

        if ($DB->query(sprintf(
            "DELETE FROM %s WHERE `%s` = %d AND `site_id` = %d LIMIT 1",
            STAFF_EMAIL_TABLENAME,
            STAFF_EMAIL_SECONDARYKEY,
            $staff_id,
            $site_id
        ))) {
            return $DB->affected_rows();
        }

        return 0;
    }
}
if (!function_exists('staffEmailGetValue')) {
    /**
     * @param int $id
     * @param int $site_id
     *
     * @return string|null
     */
    function staffEmailGetValue($id, $site_id = 0)
    {
        global $DB;

        if ($row = $DB->query_first(sprintf(
            "SELECT `email` FROM %s WHERE `%s` = %d AND `site_id` = %d LIMIT 1",
            STAFF_EMAIL_TABLENAME,
            STAFF_EMAIL_SECONDARYKEY,
            $id,
            $site_id
        ), MYSQLI_ASSOC)) {
            return $row['email'];
        }

        return staffGetValue($id, 'email');
    }
}
if (!function_exists('staffEmailSetValue')) {
    /**
     * @param int         $staff_id
     * @param int         $site_id
     * @param string|null $email
     *
     * @return void
     */
    function staffEmailSetValue($staff_id, $site_id, $email)
    {
        global $DB;

        if (is_null($email)) {
            staffEmailDelete($staff_id, $site_id);
            return;
        }

        $DB->query(sprintf(
            "INSERT INTO %s (`%s`, `site_id`, `email`) VALUES (%d, %d, %s) ON DUPLICATE KEY UPDATE `email` = %s",
            STAFF_EMAIL_TABLENAME,
            STAFF_EMAIL_SECONDARYKEY,
            $staff_id,
            $site_id,
            $DB->escape($email),
            $DB->escape($email)
        ));
    }
}
