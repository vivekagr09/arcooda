<?php
if (!defined('STAFF_HISTORY_TABLENAME')) {
    define('STAFF_HISTORY_TABLENAME', '`highwaygroup`.`users_messages`');
}
if (!defined('STAFF_HISTORY_PRIMARYKEY')) {
    define('STAFF_HISTORY_PRIMARYKEY', 'message_id');
}
if (!defined('STAFF_HISTORY_SECONDARYKEY')) {
    define('STAFF_HISTORY_SECONDARYKEY', 'staff_id');
}

if (!defined('STAFF_HISTORY_TYPE_TABLENAME')) {
    define('STAFF_HISTORY_TYPE_TABLENAME', '`highwaygroup`.`users_message_types`');
}
if (!defined('STAFF_HISTORY_TYPE_PRIMARYKEY')) {
    define('STAFF_HISTORY_TYPE_PRIMARYKEY', 'message_type_id');
}

if (!defined('STAFF_HISTORY_TYPE_PASSWORD_CHANGE')) {
    define('STAFF_HISTORY_TYPE_PASSWORD_CHANGE', 1);
}
if (!defined('STAFF_HISTORY_TYPE_SIGNATURE_CHANGE')) {
    define('STAFF_HISTORY_TYPE_SIGNATURE_CHANGE', 2);
}
if (!defined('STAFF_HISTORY_TYPE_PHOTO_CHANGE')) {
    define('STAFF_HISTORY_TYPE_PHOTO_CHANGE', 3);
}
if (!defined('STAFF_HISTORY_TYPE_DETAIL_CHANGE')) {
    define('STAFF_HISTORY_TYPE_DETAIL_CHANGE', 4);
}
if (!defined('STAFF_HISTORY_TYPE_TAXINFO_CHANGE')) {
    define('STAFF_HISTORY_TYPE_TAXINFO_CHANGE', 5);
}
if (!defined('STAFF_HISTORY_TYPE_ADMIN_NOTE')) {
    define('STAFF_HISTORY_TYPE_ADMIN_NOTE', 6);
}
if (!defined('STAFF_HISTORY_TYPE_MYSQL_CHANGE')) {
    define('STAFF_HISTORY_TYPE_MYSQL_CHANGE', 7);
}

if (!function_exists('staffHistoryFromDatabase')) {
    /**
     * @param int $id
     *
     * @return mixed[]|null
     */
    function staffHistoryFromDatabase($id)
    {
        global $DB;

        if ($row = $DB->query_first(sprintf(
            "SELECT `umt`.*, `um`.* FROM %s AS `um` LEFT JOIN %s AS `umt` ON `umt`.`%s` = `um`.`message_type_id` WHERE `um`.`%s` = %d LIMIT 1",
            STAFF_HISTORY_TABLENAME,
            STAFF_HISTORY_TYPE_TABLENAME,
            STAFF_HISTORY_TYPE_PRIMARYKEY,
            STAFF_HISTORY_PRIMARYKEY,
            $id
        ), MYSQLI_ASSOC)) {
            return staffHistoryVirtual($row);
        }

        return null;
    }
}
if (!function_exists('staffHistorySearch')) {
    /**
     * @param mixed $criteria
     *
     * @return int[]
     */
    function staffHistorySearch($criteria)
    {
        global $DB;

        $fields   = $DB->table_columns(STAFF_HISTORY_TABLENAME);
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
            $whereAND[] = sprintf(
                "`%s` = %s",
                STAFF_HISTORY_SECONDARYKEY,
                $DB->escape($criteria)
            );
        }

        $search_for = [];
        if (is_array($criteria['any_text'])) {
            $search_for = $criteria['any_text'];
        }

        if (is_string($criteria['any_text'])) {
            $search_for = explode(' ', $criteria['any_text']);
        }

        if (count($search_for) > 0) {
            $searchBits = array('`message_notes`', '`message_sender`');
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

        $results  = [];
        if ($query = $DB->query(sprintf(
            "SELECT `%s` FROM %s%s ORDER BY `message_id` DESC",
            STAFF_HISTORY_PRIMARYKEY,
            STAFF_HISTORY_TABLENAME,
            (count($whereAND)) ? sprintf(
                " WHERE %s",
                implode(" AND ", $whereAND)
            ) : ""
        ))) {
            while ($row = $DB->fetch_assoc($query)) {
                $results[] = $row[STAFF_HISTORY_PRIMARYKEY];
            }
            $DB->free_result($query);
        }

        return $results;
    }
}
if (!function_exists('staffHistoryToDatabase')) {
    /**
     * @param int     $staff_id
     * @param mixed[] $item
     *
     * @return int
     */
    function staffHistoryToDatabase($staff_id, $item)
    {
        global $DB;
        global $_GLOBALS;

        if ($DB->query(sprintf(
            "INSERT INTO %s (`%s`, `message_notes`, `message_sender`, `message_type_id`) VALUES (%d, %s, %s, %d)",
            STAFF_HISTORY_TABLENAME,
            STAFF_HISTORY_SECONDARYKEY,
            $staff_id,
            $DB->escape($item['message_notes']),
            $_GLOBALS['staff']['id'],
            $item['message_type_id']
        ))) {
            return $item[STAFF_HISTORY_PRIMARYKEY] = $DB->insert_id();
        }

        return 0;
    }
}
if (!function_exists('staffHistoryVirtual')) {
    /**
     * @param mixed[] $row
     *
     * @return mixed[]
     */
    function staffHistoryVirtual($row)
    {
        $row['message_time'] = (is_null($row['message_date'])) ? null : strtotime($row['message_date']);

        return $row;
    }
}
