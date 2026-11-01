<?php
include_once __DIR__ . '/query_staff_history.inc.php';

if (!defined('STAFF_TABLENAME')) {
    define('STAFF_TABLENAME', '`highwaygroup`.`users`');
}
if (!defined('STAFF_PRIMARYKEY')) {
    define('STAFF_PRIMARYKEY', 'id');
}

if (!defined('STAFF_TAX_TABLENAME')) {
    define('STAFF_TAX_TABLENAME', '`highwaygroup`.`users_tax`');
}
if (!defined('STAFF_TAX_PRIMARYKEY')) {
    define('STAFF_TAX_PRIMARYKEY', 'user');
}
if (!defined('STAFF_TAX_SECONDARYKEY')) {
    define('STAFF_TAX_SECONDARYKEY', 'user');
}

if (!function_exists('staffExists')) {
    /**
     * @param int $id
     *
     * @return bool
     */
    function staffExists($id)
    {
        global $DB;

        return !!$DB->query_first(sprintf(
            "SELECT 1 FROM %s WHERE `%s` = %d LIMIT 1",
            STAFF_TABLENAME,
            STAFF_PRIMARYKEY,
            $id
        ), MYSQLI_ASSOC);
    }
}
if (!function_exists('staffFromDatabase')) {
    /**
     * @param int $id
     *
     * @return mixed[]
     */
    function staffFromDatabase($id)
    {
        global $DB;

        if ($row = $DB->query_first(sprintf(
            "SELECT `ut`.*, `u`.* FROM %s AS `u` LEFT JOIN %s AS `ut` ON `ut`.`%s` = `u`.`user` WHERE `u`.`%s` = %d LIMIT 1",
            STAFF_TABLENAME,
            STAFF_TAX_TABLENAME,
            STAFF_TAX_PRIMARYKEY,
            STAFF_PRIMARYKEY,
            $id
        ), MYSQLI_ASSOC)) {
            return staffVirtual($row);
        }

        return array_merge(
            $DB->table_defaults(STAFF_TAX_TABLENAME),
            $DB->table_defaults(STAFF_TABLENAME)
        );
    }
}
if (!function_exists('staffFromUser')) {
    /**
     * @param string $user
     *
     * @return int
     */
    function staffFromUser($user)
    {
        global $DB;

        static $results = null;
        if (!is_null($results)) {
            return $results[$user] ?? 0;
        }

        $results = [];
        if ($query = $DB->query(sprintf(
            "SELECT `user`, `%s` FROM %s",
            STAFF_PRIMARYKEY,
            STAFF_TABLENAME,
        ))) {
            while ($row = $DB->fetch_assoc($query)) {
                $results[$row['user']] = $row[STAFF_PRIMARYKEY];
            }
            $DB->free_result($query);
        }

        return $results[$user] ?? 0;
    }
}
if (!function_exists('staffGetOptionList')) {
    /**
     * @param string $table_name
     *
     * @return mixed
     */
    function staffGetOptionList($table_name)
    {
        global $DB;

        static $option_cache = [];
        if (isset($option_cache[$table_name])) {
            return $option_cache[$table_name];
        }

        $table       = '';
        $key         = '';
        $value       = '';
        $option_list = [];
        switch ($table_name) {
            case 'id':
            case 'staff_id':
                $table = STAFF_TABLENAME;
                $key   = STAFF_PRIMARYKEY;
                $value = "TRIM(CONCAT_WS(' ', `first_name`, `last_name`))";
                break;

            case 'user':
                $table = STAFF_TABLENAME;
                $key   = '`user`';
                $value = "TRIM(CONCAT_WS(' ', `first_name`, `last_name`))";
                break;

            case 'default_language':
                $table = '`world`.`language`';
                $key   = '`id`';
                $value = '`name`';
                break;

            case 'email':
                $table = STAFF_TABLENAME;
                $key   = STAFF_PRIMARYKEY;
                $value = "`email`";
                break;

            case 'all_fields':
                return array_merge(staffGetOptionList('base_fields'), staffGetOptionList('ext_fields'));
            case 'all_fields_types':
                return array_merge(staffGetOptionList('base_fields_types'), staffGetOptionList('ext_fields_types'));
            case 'base_fields':
                $fields = [];
                foreach ($DB->table_fields(STAFF_TABLENAME) as $key) {
                    $fields[$key] = '`u`.`' . $key . '`';
                }

                return $fields;
            case 'base_fields_types':
                return $DB->table_columns(STAFF_TABLENAME);
            case 'ext_fields':
                return array(
                    "bonus_time"  => "UNIX_TIMESTAMP(`u`.`bonus_date`)",
                    "start_time"  => "UNIX_TIMESTAMP(`u`.`start_date`)",
                    "end_time"    => "UNIX_TIMESTAMP(`u`.`end_date`)",
                    "review_time" => "UNIX_TIMESTAMP(`u`.`review_date`)",
                    "birth_time"  => "UNIX_TIMESTAMP(`u`.`birth_date`)",
                    "last_time"   => "UNIX_TIMESTAMP(`u`.`last_date`)",
                );
            case 'ext_fields_types':
                return [];
            default:
                trigger_error('staffGetOptionList(' . json_encode($table_name) . ') is undefined');
                break;
        }
        if ('' != $table) {
            $query = $DB->query("SELECT " . $key . ", " . $value . " FROM " . $table);
            while ($row = $DB->fetch_array($query)) {
                $option_list[$row[0]] = $row[1];
            }

            $DB->free_result($query);
        }
        $option_cache[$table_name] = $option_list;
        return $option_list;
    }
}
if (!function_exists('staffGetValue')) {
    /**
     * @param int    $id
     * @param string $field
     *
     * @return mixed
     */
    function staffGetValue($id, $field)
    {
        global $DB;

        $fields = staffGetOptionList('all_fields');
        if (array_key_exists($field, $fields)) {
            if ($row = $DB->query_first(sprintf(
                "SELECT %s FROM %s WHERE `%s` = %d LIMIT 1",
                $fields[$field],
                STAFF_TABLENAME,
                STAFF_PRIMARYKEY,
                $id
            ), MYSQLI_ASSOC)) {
                return reset($row);
            }
        }

        if ($row = staffFromDatabase($id)) {
            return $row[$field] ?? null;
        }

        return null;
    }
}
if (!function_exists('staffSearch')) {
    /**
     * @param mixed $criteria
     *
     * @return int[]
     */
    function staffSearch($criteria = 1)
    {
        global $DB;
        $results = [];
        $sql     = "SELECT `u`.`" . STAFF_PRIMARYKEY . "` FROM " . STAFF_TABLENAME . " AS `u`";
        $fields  = $DB->table_columns(STAFF_TABLENAME);
        /*
        $fields = [];
        $query = $DB->query("SHOW COLUMNS FROM " . STAFF_TABLENAME);
        while ($row = $DB->fetch_array($query, MYSQLI_NUM)) $fields[$row[0]] = $row;
        $DB->free_result($query);
         */
        $whereAND = [];
        if (is_array($criteria)) {
            foreach ($criteria as $field => $value) {
                if (isset($fields[$field])) {
                    if (is_array($value)) {
                        if (count($value) > 0) {
                            $whereOR = [];
                            foreach ($value as $sub_value) {
                                $whereOR[] = "	u.`" . $field . "`=" . $DB->escape($sub_value);
                            }

                            $whereAND[] = "	(" . implode(" OR ", $whereOR) . ")";
                        } else {
                            return [];
                        }
                    } else {
                        $whereAND[] = "	u.`" . $field . "`=" . $DB->escape($value);
                    }

                    unset($fields[$field]);
                }
            }
        } else {
            $whereAND = [];
            switch ($criteria) {
                case 0:
                    break;

                case 2:
                    $whereAND[] = "	u.active='n'";
                    break;

                case 1:
                    $whereAND[] = "	u.active='y'";
                    break;

                default:
                    $whereAND[] = "	0";
            }
        }
        $search_for = [];
        if (is_array($criteria['any_text'])) {
            $search_for = $criteria['any_text'];
        }

        if (is_string($criteria['any_text'])) {
            $search_for = explode(' ', $criteria['any_text']);
        }

        if (count($search_for) > 0) {
            $searchBits = array('u.first_name', 'u.last_name', 'u.user', 'u.email', 'u.address', 'u.home_email', 'u.icq_no');
            foreach ($search_for as $findme) {
                $whereOR = [];
                foreach ($searchBits as $key) {
                    if (trim($findme) != '') {
                        $whereOR[] = $key . " LIKE " . $DB->escape('%' . addcslashes($findme, '%_') . '%');
                    }
                }
                if (count($whereOR) > 0) {
                    $whereAND[] = "	(" . implode(" OR ", $whereOR) . ")";
                }
            }
        }
        if (count($whereAND) > 0) {
            $sql .= " WHERE " . implode(" AND ", $whereAND);
        }

        $sql .= " ORDER BY u.last_name ASC";
        $query = $DB->query($sql);
        while ($row = $DB->fetch_array($query, MYSQLI_ASSOC)) {
            $results[$row[STAFF_PRIMARYKEY]] = $row[STAFF_PRIMARYKEY];
        }

        $DB->free_result($query);
        return $results;
    }
}
if (!function_exists('staffSetValue')) {
    /**
     * @param int    $id
     * @param string $field
     * @param mixed  $value
     *
     * @return int
     */
    function staffSetValue($id, $field, $value)
    {
        global $DB;

        if (array_key_exists($field, $DB->table_fields(STAFF_TABLENAME))) {
            if ($DB->query(sprintf(
                "UPDATE %s SET `%s` = %s WHERE `%s` = %d LIMIT 1",
                STAFF_TABLENAME,
                $field,
                $DB->escape($value),
                STAFF_PRIMARYKEY,
                $id
            ))) {
                return $DB->affected_rows();
            }
        }

        return 0;
    }
}
if (!function_exists('staffSort')) {
    /**
     * @param int[]  $staff_list
     * @param string $order_by
     * @param int    $order
     *
     * @return int[]
     */
    function staffSort($staff_list, $order_by = 'staff_id', $order = SORT_ASC)
    {
        global $DB;
        if (!is_array($staff_list) || count($staff_list) < 2) {
            return $staff_list;
        }

        $fields = $DB->table_columns(STAFF_TABLENAME);
        /*
        $fields = [];
        $query = $DB->query("SHOW COLUMNS FROM " . STAFF_TABLENAME);
        while ($row = $DB->fetch_array($query, MYSQLI_NUM)) $fields[$row[0]] = $row;
        $DB->free_result($query);
         */
        $joins = '';
        $force = false;
        switch ($order_by) {
            case 'staff_id':
                $order_by = STAFF_PRIMARYKEY;
                break;

            case 'full_name':
                $order_by = "CONCAT_WS(' ',u.first_name,u.last_name)";
                $force    = true;
                break;

            case 'time_here':
                $order_by = "UNIX_TIMESTAMP(u.end_date)-UNIX_TIMESTAMP(u.start_date)";
                $force    = true;
                break;

            case 'pay_rate':
                $order_by = "ut.pay_rate";
                $joins    = " LEFT JOIN " . STAFF_TAX_TABLENAME . " AS `ut` ON `ut`.`user` = `u`.`user`";
                $force    = true;
                break;
        }
        if (!isset($fields[$order_by]) && !$force) {
            return $staff_list;
        }

        $sql        = "SELECT `u`.`" . STAFF_PRIMARYKEY . "` FROM " . STAFF_TABLENAME . " AS `u`";
        $where      = " WHERE u." . STAFF_PRIMARYKEY . " IN(" . implode(',', $staff_list) . ")";
        $order      = " ORDER BY " . $order_by . ((SORT_DESC == $order) ? " DESC" : " ASC");
        $staff_list = [];
        $query      = $DB->query($sql . $joins . $where . $order);
        while ($row = $DB->fetch_array($query, MYSQLI_ASSOC)) {
            $staff_list[] = $row[STAFF_PRIMARYKEY];
        }

        $DB->free_result($query);
        return $staff_list;
    }
}
if (!function_exists('staffToDatabase')) {
    /**
     * @param mixed[] $summary
     *
     * @return int
     */
    function staffToDatabase($summary)
    {
        global $DB;
        $staff_id  = intval($summary['staff_id']);
        $old_staff = staffFromDatabase($staff_id);
        $user_name = $summary['user'];
        if ('' != $old_staff['user']) {
            $user_name = $old_staff['user'];
        }

        foreach ($old_staff as $key => $value) {
            if ($summary[$key] == $value) {
                unset($summary[$key]);
            }
        }

        foreach ($summary as $key => $value) {
            if (!array_key_exists($key, $old_staff)) {
                unset($summary[$key]);
            }
        }

        $tables_fields = [];
        foreach (array(STAFF_TABLENAME, STAFF_TAX_TABLENAME) as $table) {
            $tables_fields[$table] = $DB->table_columns($table);
            /*
        $table_fields = [];
        $query = $DB->query("SHOW COLUMNS FROM " . $table);
        while ($row = $DB->fetch_array($query, MYSQLI_NUM)) $table_fields[$row[0]] = $row;
        $tables_fields[$table] = $table_fields;
         */
        }
        $update_vals = [];
        $insert_vals = [];
        $changes     = [];
        foreach ($tables_fields as $table => $fields) {
            foreach ($summary as $key => $value) {
                if (array_key_exists($key, $fields)) {
                    if (isset($summary[$key])) {
                        $update_vals[$table][$key] = "`" . $key . "`=" . $DB->escape($value);
                        $insert_vals[$table][$key] = $DB->escape($value);
                        $changes[$key]             = $key . ' changed to ' . $value;
                    } else {
                        $update_vals[$table][$key] = "`" . $key . "`=NULL";
                        $insert_vals[$table][$key] = 'NULL';
                        $changes[$key]             = $key . ' has been removed';
                    }
                }
            }
        }

        $insert_vals[STAFF_TAX_TABLENAME]['user'] = $DB->escape($user_name);
        $new_staff                        = !staffExists($staff_id);
        if ($new_staff) {
            if (is_array($insert_vals[STAFF_TABLENAME])) {
                $insert_vals[STAFF_TABLENAME]['date_added'] = 'NOW()';
                $sql                                = "INSERT INTO " . STAFF_TABLENAME . " (" . implode(', ', array_keys($insert_vals[STAFF_TABLENAME])) . ") VALUES (" . implode(', ', $insert_vals[STAFF_TABLENAME]) . ")";
                $DB->query($sql);
            }
            $staff_id                   = $DB->insert_id();
            staffHistoryToDatabase($staff_id, [
                'message_type_id' => STAFF_HISTORY_TYPE_ADMIN_NOTE,
                'message_notes'   => 'Staff Created',
            ]);
        } elseif (count($changes) > 0) {
            staffHistoryToDatabase($staff_id, [
                'message_type_id' => STAFF_HISTORY_TYPE_DETAIL_CHANGE,
                'message_notes'   => implode("\n", $changes),
            ]);
        }

        $sql = "UPDATE " . STAFF_TABLENAME . " SET " . implode(', ', $update_vals[STAFF_TABLENAME]) . " WHERE `" . STAFF_PRIMARYKEY . "` =" . $staff_id . " LIMIT 1";
        $DB->query($sql);
        staffHistoryToDatabase($staff_id, [
            'message_type_id' => STAFF_HISTORY_TYPE_MYSQL_CHANGE,
            'message_notes'   => $sql,
        ]);

        $row = $DB->query_first(sprintf(
            "SELECT user FROM %s WHERE user=" . $DB->escape($user_name),
            STAFF_TAX_TABLENAME
        ));
        if ('' != $row['user']) {
            $sql = "UPDATE " . STAFF_TAX_TABLENAME . " SET " . implode(', ', $update_vals[STAFF_TAX_TABLENAME]) . " WHERE user=" . $DB->escape($user_name) . " LIMIT 1";
        } else {
            $sql = "INSERT INTO " . STAFF_TAX_TABLENAME . " (`" . implode('`, `', array_keys($insert_vals[STAFF_TAX_TABLENAME])) . "`) VALUES(" . implode(", ", $insert_vals[STAFF_TAX_TABLENAME]) . ")";
        }
        $DB->query($sql);

        staffHistoryToDatabase($staff_id, [
            'message_type_id' => STAFF_HISTORY_TYPE_MYSQL_CHANGE,
            'message_notes'   => $sql,
        ]);

        return $staff_id;
    }
}
if (!function_exists('staffVirtual')) {
    /**
     * @param mixed[] $row
     *
     * @return mixed[]
     */
    function staffVirtual($row)
    {
        $row['bonus_time']  = (is_null($row['bonus_date'])) ? null : strtotime($row['bonus_date']);
        $row['start_time']  = (is_null($row['start_date'])) ? null : strtotime($row['start_date']);
        $row['end_time']    = (is_null($row['end_date'])) ? null : strtotime($row['end_date']);
        $row['review_time'] = (is_null($row['review_date'])) ? null : strtotime($row['review_date']);
        $row['birth_time']  = (is_null($row['birth_date'])) ? null : strtotime($row['birth_date']);
        $row['last_time']   = (is_null($row['last_date'])) ? null : strtotime($row['last_date']);

        $time_here       = (time() - $row['start_time']) / (60 * 60 * 24);
        if (0 != $row['end_time']) {
            $time_here = abs($row['end_time'] - $row['start_time']) / (60 * 60 * 24);
        }
        if ($time_here < 0) {
            $time_here = 0;
        }

        $row['time_here']   = $time_here * 60 * 60 * 24;
        $row['years_here']  = floor(($time_here) / 365.25);
        $row['months_here'] = floor(($time_here - ($row['years_here'] * 365.25)) / 30.4375);
        $row['days_here']   = floor(($time_here - ($row['years_here'] * 365.25 + $row['months_here'] * 30.4375)) / 1);

        $row['bonus_pay'] = 'n';
        if (0 != $row['bonus_time'] && $row['bonus_time'] < time()) {
            $row['bonus_pay'] = 'y';
        }

        $row['staff_id'] = $row[STAFF_PRIMARYKEY];

        $row['name'] = $row['full_name'] = trim($row['first_name'] . ' ' . $row['last_name']);

        return $row;
    }
}
