<?php
include_once __DIR__ . '/common.inc.php';

if (!function_exists('get_user')) {
    /**
     * @return string|null
     */
    function get_user()
    {
        return $_SERVER["REMOTE_USER"] ?? null;
    }
}
if (!function_exists('get_allowed_sections')) {
    /**
     * @param string $user
     *
     * @return string[]
     */
    function get_allowed_sections($user, $site_id = null)
    {
        global $_GLOBALS;
        global $DB;

        $result = [];
        if ($query = $DB->query(sprintf(
            "SELECT `as`.`sect_key` FROM `highwaygroup`.`admin_users` AS `au` INNER JOIN `highwaygroup`.`admin_sections` AS `as` ON `au`.`section_id` = `as`.`section_id` WHERE `au`.`user` = %s AND `au`.`site_id` = %d",
            $DB->escape($user),
            $site_id ?? $_GLOBALS['site_settings']['site_id'] ?? 0
        ))) {
            while ($row = $DB->fetch_assoc($query)) {
                $result[] = $row['sect_key'];
            }
            $DB->free_result($query);
        }

        return $result;
    }
}
if (!function_exists('get_all_sections')) {
    /**
     * @return string[]
     */
    function get_all_sections()
    {
        global $DB;

        $result = array();
        if ($query = $DB->query(sprintf(
            "SELECT `sect_key` FROM `highwaygroup`.`admin_sections`"
        ))) {
            while ($row = $DB->fetch_assoc($query)) {
                $result[] = $row['sect_key'];
            }
            $DB->free_result($query);
        }

        return $result;
    }
}
if (!function_exists('has_permission')) {
    /**
     * @param string[]|string $sections
     * @param string[]        $allowed
     *
     * @return bool
     */
    function has_permission($sections, $allowed)
    {
        //@todo Sort out the permissions and remove this line

        if (!is_array($allowed)) {
            return false;
        }

        if (is_array($sections)) {
            foreach ($sections as $v) {
                if (in_array($v, $allowed)) {
                    return true;
                }
            }
        } else {
            return in_array($sections, $allowed);
        }

        return false;
    }
}
