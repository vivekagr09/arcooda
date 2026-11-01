<?php
if (!defined('STAFF_SUPER_TABLENAME')) {
    define('STAFF_SUPER_TABLENAME', '`highwaygroup`.`users_superfund`');
}
if (!defined('STAFF_SUPER_PRIMARYKEY')) {
    define('STAFF_SUPER_PRIMARYKEY', 'fund_id');
}
if (!defined('STAFF_SUPER_SECONDARYKEY')) {
    define('STAFF_SUPER_SECONDARYKEY', 'staff_id');
}

if (!function_exists('staffSuperFromDatabase')) {
    /**
     * @param int $id
     *
     * @return mixed[]|null
     */
    function staffSuperFromDatabase($id)
    {
        global $DB;

        if ($row = $DB->modelLoad(
            $id,
            STAFF_SUPER_TABLENAME,
            STAFF_SUPER_PRIMARYKEY
        )) {
            return $row;
        }

        return null;
    }
}
if (!function_exists('staffSuperSearch')) {
    /**
     * @param mixed $criteria
     *
     * @return int[]
     */
    function staffSuperSearch($criteria)
    {
        global $DB;

        $fields   = $DB->table_columns(STAFF_SUPER_TABLENAME);
        $whereAND = [];
        if (is_array($criteria)) {
            foreach ($criteria as $field => $value) {
                if (isset($fields[$field])) {
                    if (is_array($value)) {
                        if (count($value) > 0) {
                            $whereOR = [];
                            foreach ($value as $sub_value) {
                                $whereOR[] = "`" . $field . "` = " . $DB->escape($sub_value);
                            }

                            $whereAND[] = "(" . implode(" OR ", $whereOR) . ")";
                        } else {
                            return [];
                        }
                    } else {
                        $whereAND[] = "`" . $field . "` = " . $DB->escape($value);
                    }

                    unset($fields[$field]);
                }
            }
        } elseif (is_string($criteria) && '' != $criteria) {
            $whereAND[] = "`" . STAFF_SUPER_SECONDARYKEY . "` = " . $DB->escape($criteria);
        }

        $search_for = [];
        if (is_array($criteria['any_text'])) {
            $search_for = $criteria['any_text'];
        }

        if (is_string($criteria['any_text'])) {
            $search_for = explode(' ', $criteria['any_text']);
        }

        if (count($search_for) > 0) {
            $searchBits = array('fund_name', 'fund_address', 'fund_policy', 'fund_contact', 'fund_phone');
            foreach ($search_for as $findme) {
                $whereOR = [];
                foreach ($searchBits as $key) {
                    if (trim($findme) != '') {
                        $whereOR[] = $key . " LIKE " . $DB->escape('%' . addcslashes($findme, '%_') . '%');
                    }
                }
                if (count($whereOR) > 0) {
                    $whereAND[] = "(" . implode(" OR ", $whereOR) . ")";
                }
            }
        }

        $result = [];
        if ($query = $DB->query(sprintf(
            "SELECT `%s` FROM %s%s ORDER BY `fund_active` DESC",
            STAFF_SUPER_PRIMARYKEY,
            STAFF_SUPER_TABLENAME,
            (count($whereAND)) ? sprintf(" WHERE %s", implode(" AND ", $whereAND)) : ""
        ))) {
            while ($row = $DB->fetch_array($query, MYSQLI_ASSOC)) {
                $result[] = $row[STAFF_SUPER_PRIMARYKEY];
            }
            $DB->free_result($query);
        }

        return $result;
    }
}
if (!function_exists('staffSuperToDatabase')) {
    /**
     * @param int     $staff_id
     * @param mixed[] $fund_info
     *
     * @return int
     */
    function staffSuperToDatabase($staff_id, $fund_info)
    {
        global $DB;
        if ($fund_info[STAFF_SUPER_PRIMARYKEY] > 0) {
            if ('n' != $fund_info['fund_active']) {
                $DB->query(sprintf(
                    "UPDATE %s SET `fund_active` = 'y' WHERE `%s` = " . intval($fund_info['fund_id']) . " LIMIT 1",
                    STAFF_SUPER_TABLENAME,
                    STAFF_SUPER_PRIMARYKEY
                ));
            }
        } else {
            $DB->query(sprintf(
                "INSERT INTO %s SET `%s` = " . intval($staff_id) . "," . "	fund_name=" . $DB->escape($fund_info['fund_name']) . "," . "	fund_address=" . $DB->escape($fund_info['fund_address']) . "," . "	fund_policy=" . $DB->escape($fund_info['fund_policy']) . "," . "	fund_contact=" . $DB->escape($fund_info['fund_contact']) . "," . "	fund_phone=" . $DB->escape($fund_info['fund_phone']) . "," . "	fund_active=" . $DB->escape($fund_info['fund_active']),
                STAFF_SUPER_TABLENAME,
                STAFF_SUPER_SECONDARYKEY
            ));
            $fund_info[STAFF_SUPER_PRIMARYKEY] = $DB->insert_id();
        }
        if ('n' != $fund_info['fund_active']) {
            $DB->query(sprintf(
                "UPDATE %s SET `fund_active` = 'n' WHERE `%s` <> " . intval($fund_info['fund_id']),
                STAFF_SUPER_TABLENAME,
                STAFF_SUPER_PRIMARYKEY
            ));
        }

        return $fund_info[STAFF_SUPER_PRIMARYKEY];
    }
}
