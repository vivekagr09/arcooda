<?php
if (!defined('STAFF_WEBSITE_TABLENAME')) {
    define('STAFF_WEBSITE_TABLENAME', '`highwaygroup`.`website_access`');
}
if (!defined('STAFF_WEBSITE_SECONDARYKEY')) {
    define('STAFF_WEBSITE_SECONDARYKEY', 'staff_id');
}

if (!function_exists('staffWebsiteDelete')) {
    /**
     * @param int $staff_id
     * @param int $site_id
     *
     * @return int
     */
    function staffWebsiteDelete($staff_id, $site_id)
    {
        global $DB;

        if ($DB->query(sprintf(
            "DELETE FROM %s WHERE `%s` = %d AND `site_id` = %d LIMIT 1",
            STAFF_WEBSITE_TABLENAME,
            STAFF_WEBSITE_SECONDARYKEY,
            $staff_id,
            $site_id
        ))) {
            return $DB->affected_rows();
        }

        return 0;
    }
}
if (!function_exists('staffWebsiteGetValue')) {
    /**
     * @param int $staff_id
     * @param int $site_id
     *
     * @return bool
     */
    function staffWebsiteGetValue($staff_id, $site_id = 0)
    {
        global $DB;

        return !!$DB->query_first(sprintf(
            "SELECT 1 FROM %s WHERE `%s` = %d AND `site_id` = %d LIMIT 1",
            STAFF_WEBSITE_TABLENAME,
            STAFF_WEBSITE_SECONDARYKEY,
            $staff_id,
            $site_id
        ), MYSQLI_ASSOC);
    }
}
if (!function_exists('staffWebsiteSetValue')) {
    /**
     * @param int  $staff_id
     * @param int  $site_id
     * @param bool $value
     *
     * @return void
     */
    function staffWebsiteSetValue($staff_id, $site_id, $value = true)
    {
        global $DB;

        if (!$value) {
            staffWebsiteDelete($staff_id, $site_id);
            return;
        }

        $DB->query(sprintf(
            "INSERT IGNORE INTO %s (`%s`, `site_id`) VALUES (%d, %d)",
            STAFF_WEBSITE_TABLENAME,
            STAFF_WEBSITE_SECONDARYKEY,
            $staff_id,
            $site_id
        ));
    }
}
if (!function_exists('staffWebsiteSearch')) {
    /**
     * @param int $id
     *
     * @return int[]
     */
    function staffWebsiteSearch($id)
    {
        global $DB;

        $result = [];
        if ($query  = $DB->query(sprintf(
            "SELECT `site_id` FROM %s WHERE `%s` = %d",
            STAFF_WEBSITE_TABLENAME,
            STAFF_WEBSITE_SECONDARYKEY,
            $id
        ))) {
            while ($row = $DB->fetch_assoc($query)) {
                $result[] = $row['site_id'];
            }
            $DB->free_result($query);
        }

        return $result;
    }
}
