<?php
if (!defined('MEMBER_DISCOUNT_TABLENAME')) {
    define('MEMBER_DISCOUNT_TABLENAME', '`highwaygroup`.`new_members_discounts`');
}
if (!defined('MEMBER_DISCOUNT_PRIMARYKEY')) {
    define('MEMBER_DISCOUNT_PRIMARYKEY', 'member_id');
}
if (!defined('MEMBER_DISCOUNT_SECONDARYKEY')) {
    define('MEMBER_DISCOUNT_SECONDARYKEY', 'site_id');
}

if (!function_exists('memberDiscountsFromDatabase')) {
    /**
     * @param int $member_id
     * @param int $site_id
     *
     * @return int[]|int
     */
    function memberDiscountsFromDatabase($member_id, $site_id = 0)
    {
        global $DB;

        if ($site_id > 0) {
            if ($row = $DB->query_first(sprintf(
                "SELECT * FROM %s WHERE `%s` = %d AND `%s` = %d LIMIT 1",
                MEMBER_DISCOUNT_TABLENAME,
                MEMBER_DISCOUNT_PRIMARYKEY,
                $member_id,
                MEMBER_DISCOUNT_SECONDARYKEY,
                $site_id
            ), MYSQLI_ASSOC)) {
                return intval($row['discount']);
            }
            return 0;
        }

        $result = array();

        if ($query = $DB->query(sprintf(
            "SELECT * FROM %s WHERE `%s` = %d",
            MEMBER_DISCOUNT_TABLENAME,
            MEMBER_DISCOUNT_PRIMARYKEY,
            $member_id
        ))) {
            while ($row = $DB->fetch_assoc($query)) {
                $result[$row[MEMBER_DISCOUNT_SECONDARYKEY]] = $row['discount'];
            }
            $DB->free_result($query);
        }

        return $result;
    }
}
if (!function_exists('memberDiscountsToDatabase')) {
    /**
     * @param int   $member_id
     * @param int[] $summary
     *
     * @return void
     */
    function memberDiscountsToDatabase($member_id, $summary)
    {
        global $DB;

        if (!is_array($summary) || !count($summary)) {
            return;
        }

        $DB->query(sprintf(
            "DELETE FROM %s WHERE `%s` = %d",
            MEMBER_DISCOUNT_TABLENAME,
            MEMBER_DISCOUNT_PRIMARYKEY,
            $member_id
        ));
        foreach ($summary as $site_id => $discount) {
            if ($discount > 0) {
                $DB->query(sprintf(
                    "INSERT INTO %s (`%s`, `%s`, `discount`) VALUES (%d, %d, %d)",
                    MEMBER_DISCOUNT_TABLENAME,
                    MEMBER_DISCOUNT_PRIMARYKEY,
                    MEMBER_DISCOUNT_SECONDARYKEY,
                    $member_id,
                    $site_id,
                    $discount
                ));
            }
        }

    }
}
