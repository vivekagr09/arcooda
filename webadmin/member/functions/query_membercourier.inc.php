<?php
if (!defined('MEMBER_COURIER_TABLENAME')) {
    define('MEMBER_COURIER_TABLENAME', '`highwaygroup`.`new_members_couriers`');
}
if (!defined('MEMBER_COURIER_PRIMARYKEY')) {
    define('MEMBER_COURIER_PRIMARYKEY', 'id');
}
if (!defined('MEMBER_COURIER_SECONDARYKEY')) {
    define('MEMBER_COURIER_SECONDARYKEY', 'member_id');
}

if (!function_exists('memberCourierDelete')) {
    /**
     * @param int $id
     *
     * @return int
     */
    function memberCourierDelete($id)
    {
        global $DB;

        if ($DB->query(sprintf(
            "DELETE FROM %s WHERE `%s` = %d LIMIT 1",
            MEMBER_COURIER_TABLENAME,
            MEMBER_COURIER_PRIMARYKEY,
            $id
        ))) {
            return 1;
        }

        return 0;
    }
}
if (!function_exists('memberCouriersFromDatabase')) {
    /**
     * @param int $member_id
     *
     * @return mixed[][]
     */
    function memberCouriersFromDatabase($member_id)
    {
        global $DB;

        $result = array();

        if ($query = $DB->query(sprintf(
            "SELECT * FROM %s WHERE `member_id` = %d",
            MEMBER_COURIER_TABLENAME,
            $member_id
        ))) {
            while ($row = $DB->fetch_assoc($query)) {
                if (0 == $row['courier_id'] && '' != $row['courier_name']) {
                    //Keep courier_name from database
                } elseif ($row['courier_id'] < 100) {
                    if (function_exists('getCourierServiceName')) {
                        $row['courier_name'] = getCourierServiceName($row['courier_id']);
                    } elseif (function_exists('getFreightServiceName')) {
                        $row['courier_name'] = getFreightServiceName($row['courier_id']);
                    }
                } else {
                    if (function_exists('getFreightServiceProviderName')) {
                        $row['courier_name'] = getFreightServiceProviderName($row['courier_id']);
                    }
                }
                $result[] = $row;
            }
            $DB->free_result($query);
        }

        return $result;
    }
}
if (!function_exists('memberCouriersToDatabase')) {
    /**
     * @param int       $member_id
     * @param mixed[][] $current
     *
     * @return void
     */
    function memberCouriersToDatabase($member_id, $current)
    {
        global $DB;

        $member_id = intval($member_id);
        if (0 == $member_id) {
            return;
        }

        $c_list = getFreightServices('personal', '', true);

        $DB->query(sprintf(
            "DELETE FROM %s WHERE `member_id` = %d",
            MEMBER_COURIER_TABLENAME,
            $member_id
        ));

        $fields = getTableFields(MEMBER_COURIER_TABLENAME);
        foreach ($current as $courier) {
            $insert_vals          = array();
            $courier['member_id'] = $member_id;
            if (0 == $courier[MEMBER_COURIER_PRIMARYKEY]) {
                unset($courier[MEMBER_COURIER_PRIMARYKEY]);
            }

            if (0 != $courier['courier_id']) {
                $courier['courier_name'] = $c_list[$courier['courier_id']];
            }

            foreach ($courier as $key => $value) {
                if (isset($fields[$key])) {
                    $insert_vals[$key] = formatSQLString($value);
                }
            }

            if (count($insert_vals)) {
                $DB->query(sprintf(
                    "INSERT INTO %s (`%s`) VALUES (%s)",
                    MEMBER_COURIER_TABLENAME,
                    implode('`, `', array_keys($insert_vals)),
                    implode(", ", array_values($insert_vals))
                ));
            }
        }
    }
}
if (!function_exists('memberCourierSetMain')) {
    /**
     * @param int $member_id
     * @param int $id
     *
     * @return bool
     */
    function memberCourierSetMain($member_id, $id)
    {
        global $DB;

        if (!$DB->query_first(sprintf(
            "SELECT 1 FROM %s WHERE `%s` = %d AND `%s` = %d LIMIT 1",
            MEMBER_COURIER_TABLENAME,
            MEMBER_COURIER_PRIMARYKEY,
            $id,
            MEMBER_COURIER_SECONDARYKEY,
            $member_id
        ), MYSQLI_ASSOC)) {
            return false;
        }

        if ($DB->query(sprintf(
            "UPDATE %s SET `main` = IF(`%s` = %d, 'y', 'n') WHERE `%s` = %d",
            MEMBER_COURIER_TABLENAME,
            MEMBER_COURIER_PRIMARYKEY,
            $id,
            MEMBER_COURIER_SECONDARYKEY,
            $member_id
        ))) {
            return true;
        }

        return false;
    }
}
if (!function_exists('memberCourierToDatabase')) {
    function memberCourierToDatabase($member_id, &$courier)
    {
        global $DB;

        $courier['member_id'] = $member_id;

        return $DB->modelSave($courier, MEMBER_COURIER_TABLENAME, MEMBER_COURIER_PRIMARYKEY);
    }
}
if (!function_exists('memberCourierGetMain')) {
    /**
     * @param int $member_id
     * @param int $id
     *
     * @return bool
     */
    function memberCourierGetMain($member_id)
    {
        global $DB;
        $row=[];
        if ($row=$DB->query_first(sprintf(
            "SELECT *  FROM %s WHERE `main` = 'y' AND `%s` = %d LIMIT 1",
            MEMBER_COURIER_TABLENAME,
            MEMBER_COURIER_SECONDARYKEY,
            $member_id
        ), MYSQLI_ASSOC)) {
            if (0 == $row['courier_id'] && '' != $row['courier_name']) {
                //Keep courier_name from database
            } elseif ($row['courier_id'] < 100) {
                if (function_exists('getCourierServiceName')) {
                    $row['courier_name'] = getCourierServiceName($row['courier_id']);
                } elseif (function_exists('getFreightServiceName')) {
                    $row['courier_name'] = getFreightServiceName($row['courier_id']);
                }
            } else {
                if (function_exists('getFreightServiceProviderName')) {
                    $row['courier_name'] = getFreightServiceProviderName($row['courier_id']);
                }
            }
        }
        return $row;
    }
}

