<?php
include_once __DIR__ . '/query_staff.inc.php';

if (!defined('STAFF_PAYRATE_TABLENAME')) {
    define('STAFF_PAYRATE_TABLENAME', '`highwaygroup`.`users_payrates`');
}
if (!defined('STAFF_PAYRATE_PRIMARYKEY')) {
    define('STAFF_PAYRATE_PRIMARYKEY', 'change_id');
}
if (!defined('STAFF_PAYRATE_SECONDARYKEY')) {
    define('STAFF_PAYRATE_SECONDARYKEY', 'staff_id');
}

if (!function_exists('staffPayRatesFromDatabase')) {
    /**
     * @param int $staff_id
     *
     * @return mixed[][]
     */
    function staffPayRatesFromDatabase($staff_id)
    {
        global $DB;

        $result = [];
        if ($query = $DB->query(sprintf(
            "SELECT * FROM %s WHERE `active` = 'y' AND `%s` = %d ORDER BY `%s` DESC",
            STAFF_PAYRATE_TABLENAME,
            STAFF_PAYRATE_SECONDARYKEY,
            $staff_id,
            STAFF_PAYRATE_PRIMARYKEY
        ))) {
            while ($row = $DB->fetch_assoc($query)) {
                $result[] = staffPayRateVirtual($row);
            }
            $DB->free_result($query);
        }

        return $result;
    }
}
if (!function_exists('staffPayRateVirtual')) {
    /**
     * @param mixed[] $row
     *
     * @return mixed[]
     */
    function staffPayRateVirtual($row)
    {
        $row['change_time'] = (is_null($row['change_date'])) ? null : strtotime($row['change_date']);

        return $row;
    }
}
if (!function_exists('staffPayRateGetValue')) {
    /**
     * @param int      $id
     * @param int|null $time
     *
     * @return string|null
     */
    function staffPayRateGetValue($id, $time = null)
    {
        global $DB;

        if ($row = $DB->query_first(sprintf(
            "SELECT `pay_rate` FROM %s WHERE `%s` = %d AND `active` = 'y' AND `change_date` <= %s ORDER BY `change_date` DESC LIMIT 1",
            STAFF_PAYRATE_TABLENAME,
            STAFF_PAYRATE_SECONDARYKEY,
            $id,
            $DB->escape(formatDateTime($time))
        ), MYSQLI_ASSOC)) {
            return $row['pay_rate'];
        }

        return staffGetValue($id, 'pay_rate');
    }
}
if (!function_exists('staffPayRateSetValue')) {
    /**
     * @param int      $id
     * @param string   $pay_rate
     * @param int|null $time
     *
     * @return void
     */
    function staffPayRateSetValue($id, $pay_rate, $time = null)
    {
        global $DB;
        global $_GLOBALS;

        $DB->query(sprintf(
            "UPDATE %s SET `active` = 'n' WHERE `%s` = %d AND `change_date` >= %s",
            STAFF_PAYRATE_TABLENAME,
            STAFF_PAYRATE_SECONDARYKEY,
            $id,
            $DB->escape(formatDateTime($time))
        ));

        $DB->query(sprintf(
            "INSERT INTO %s (`%s`, `change_staff`, `change_date`, `pay_rate`) VALUES (%d, %d, %s, %s)",
            STAFF_PAYRATE_TABLENAME,
            STAFF_PAYRATE_SECONDARYKEY,
            $id,
            $_GLOBALS['staff']['id'],
            $DB->escape(formatDateTime($time)),
            $DB->escape($pay_rate)
        ));

        staffSetValue($id, 'pay_rate', $pay_rate);
    }
}
