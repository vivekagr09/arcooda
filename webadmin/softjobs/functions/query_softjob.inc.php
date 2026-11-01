<?php
include_once __DIR__ . '/query_softjob_note.inc.php';

if (!defined('SOFTJOB_TABLENAME')) {
    define('SOFTJOB_TABLENAME', '`highwaygroup`.`bugs`');
}
if (!defined('SOFTJOB_PRIMARYKEY')) {
    define('SOFTJOB_PRIMARYKEY', 'id');
}
define('SOFTJOB_PRIORITY_HIGH', 1);
define('SOFTJOB_PRIORITY_MEDIUM', 6);
define('SOFTJOB_PRIORITY_LOW', 10);

define('SOFTJOB_STATUS_OPEN', 1);
define('SOFTJOB_STATUS_FIXING', 2);
define('SOFTJOB_STATUS_CLOSED', 3);
if (!function_exists('softjobDefault')) {
    /**
     * @return mixed[]
     */
    function softjobDefault()
    {
        global $DB;

        return softjobVirtual($DB->modelDefault(
            SOFTJOB_TABLENAME,
            SOFTJOB_PRIMARYKEY
        ));
    }
}
if (!function_exists('softjobDelete')) {
    /**
     * @param int $id
     *
     * @return int
     */
    function softjobDelete($id)
    {
        global $DB;

        return $DB->modelDelete(
            $id,
            SOFTJOB_TABLENAME,
            SOFTJOB_PRIMARYKEY
        ) + softjobNotesDelete($id);
    }
}
if (!function_exists('softjobExists')) {
    /**
     * @param int $id
     *
     * @return bool
     */
    function softjobExists($id)
    {
        global $DB;

        return $DB->modelExists(
            $id,
            SOFTJOB_TABLENAME,
            SOFTJOB_PRIMARYKEY
        );
    }
}
if (!function_exists('softjobFromDatabase')) {
    /**
     * @param int $id
     *
     * @return mixed[]|null
     */
    function softjobFromDatabase($id)
    {
        global $DB;

        if ($row = $DB->modelLoad(
            $id,
            SOFTJOB_TABLENAME,
            SOFTJOB_PRIMARYKEY
        )) {
            return softjobVirtual($row);
        }

        return null;
    }
}
if (!function_exists('softjobGetValue')) {
    /**
     * @param int    $id
     * @param string $field
     *
     * @return mixed
     */
    function softjobGetValue($id, $field)
    {
        global $DB;

        if (array_key_exists($field, $DB->table_columns(
            SOFTJOB_TABLENAME
        ))) {
            return $DB->modelGet(
                $id,
                $field,
                SOFTJOB_TABLENAME,
                SOFTJOB_PRIMARYKEY
            );
        }

        if ($model = softjobFromDatabase($id)) {
            return $model[$field] ?? null;
        }

        return null;
    }
}
if (!function_exists('softjobHasValue')) {
    /**
     * @param int    $id
     * @param string $field
     * @param mixed  $value
     *
     * @return bool
     */
    function softjobHasValue($id, $field, $value)
    {
        return $value == softjobGetValue($id, $field);
    }
}
if (!function_exists('softjobQuery')) {
    /**
     * @todo
     *
     * @param mixed[] $eq
     * @param mixed[] $lt
     * @param mixed[] $gt
     *
     * @return string
     */
    function softjobQuery($eq = array(), $lt = array(), $gt = array())
    {
        global $DB;

        $fields = array();
        if ($query = $DB->query(sprintf(
            "SHOW COLUMNS FROM `highwaygroup`.`bugs`"
        ))) {
            while ($row = $DB->fetch_array($query, MYSQLI_NUM)) {
                $fields[$row[0]] = $row;
            }
            $DB->free_result($query);
        }

        $whereAND = array();
        if (is_array($eq)) {
            foreach ($eq as $field => $value) {
                if (isset($fields[$field])) {
                    if (is_array($value)) {
                        if (count($value) > 0) {
                            $whereOR = array();
                            if (substr($fields[$field][1], 0, 4) == 'set(') {
                                foreach ($value as $sub_value) {
                                    $whereOR[] = "	FIND_IN_SET(" . $DB->escape($sub_value) . ",b.`" . $field . "`)";
                                }
                            } else {
                                foreach ($value as $sub_value) {
                                    $whereOR[] = "	b.`" . $field . "`=" . $DB->escape($sub_value);
                                }
                            }

                            $whereAND[] = "	(" . implode(" OR ", $whereOR) . ")";
                        } else {
                            return array();
                        }

                    } else {
                        if (substr($fields[$field][1], 0, 4) == 'set(') {
                            $whereAND[] = "	FIND_IN_SET(" . $DB->escape($value) . ",b.`" . $field . "`)";
                        } else {
                            $whereAND[] = "	b.`" . $field . "`=" . $DB->escape($value);
                        }

                    }
                    unset($fields[$field]);
                }
            }
        }

        if (is_string($eq) && '' != $eq) {
            $whereAND[] = "	b.status=" . $DB->escape($eq);
        }

        $search_for = array();
        if (is_array($eq) && is_array($eq['any_text'])) {
            $search_for = $eq['any_text'];
        }

        if (is_array($eq) && is_string($eq['any_text'])) {
            $search_for = explode(' ', $eq['any_text']);
        }

        if (count($search_for) > 0) {
            $searchBits = array('b.title', 'b.description');
            foreach ($search_for as $findme) {
                $whereOR = array();
                foreach ($searchBits as $key) {
                    if (trim($findme) != '') {
                        $whereOR[] = $key . " LIKE '%" . str_replace('%', '\%', str_replace('_', '\_', addslashes($findme))) . "%'";
                    }
                }

                if (count($whereOR) > 0) {
                    $whereAND[] = "	(" . implode(" OR ", $whereOR) . ")";
                }

            }
        }

        return (count($whereAND)) ? implode(" AND ", $whereAND) : "1";
    }
}
if (!function_exists('softjobSearch')) {
    /**
     * @param string|null $where
     * @param string|null $order_by
     *
     * @return int[]
     */
    function softjobSearch($where = null, $order_by = null)
    {
        global $DB;

        // Legacy accepts criteria as first parameter
        if (is_array($where)) {
            $where = softjobQuery($where);
        }
        return $DB->query_list(sprintf(
            "SELECT `%s` FROM %s b %s%s",
            SOFTJOB_PRIMARYKEY,
            SOFTJOB_TABLENAME,
            (!is_null($where)) ? sprintf(
                " WHERE %s",
                $where
            ) : "",
            (!is_null($order_by)) ? sprintf(
                " ORDER BY `%s`",
                $order_by
            ) : ""
        ));
    }
}
if (!function_exists('softjobSetValue')) {
    /**
     * @param int    $id
     * @param string $field
     * @param mixed  $value
     *
     * @return int
     */
    function softjobSetValue($id, $field, $value)
    {
        global $DB;

        return $DB->modelSet(
            $id,
            $field,
            $value,
            SOFTJOB_TABLENAME,
            SOFTJOB_PRIMARYKEY
        );
    }
}
if (!function_exists('softjobSort')) {
    /**
     * @param string|null $where
     * @param string|null $order_by
     *
     * @return int[]
     */
    function softjobSort($where = null, $order_by = null)
    {
        // Legacy accepts list of id's as first parameter
        if (is_array($where)) {
            $where = softjobQuery(array(
                SOFTJOB_PRIMARYKEY => $where,
            ));
        }

        return softjobSearch($where, $order_by);
    }
}
if (!function_exists('softjobToDatabase')) {
    /**
     * @param mixed[] $item
     *
     * @return int
     */
    /*
    function softjobToDatabase(&$item)
    {
        global $DB;

        return $DB->modelSave(
            $item,
            SOFTJOB_TABLENAME,
            SOFTJOB_PRIMARYKEY
        );
    }
    */
}
if (!function_exists('softjobVirtual')) {
    /**
     * @param mixed[] $row
     *
     * @return mixed[]
     */
    function softjobVirtual($row)
    {
        $row['time_added'] = strtotime($row['date_added']);
        $row['website'] = $_GLOBALS['site_settings']['site_id'];
        $row['title'] = $row['title'] ?? '';
        $row['example_url'] = $row['example_url'] ?? '';
        $row['error_log'] = $row['error_log'] ?? '';
        $row['description'] = $row['description'] ?? '';
        return $row;
    }
}

if (!function_exists('softjobGetOptionList')) {
    function softjobGetOptionList($table_name)
    {
        global $DB;

        $table       = '';
        $option_list = array();

        switch ($table_name) {
            case 'priority':
                $table = '`highwaygroup`.priority';
                $key   = 'id';
                $value = 'description';
                return array('1' => 'High', '6' => 'Medium', '10' => 'Low');
            case 'staff_id':
            case 'handler':
            case 'reporter':
            case 'assigned':
                $temp     = getStaffList();
                $temp[37] = $temp[35];
                $temp[35] = $temp[71];
                $temp[48] = $temp[86];
                $temp[2]  = $temp[33];
                return $temp;
            case 'staff_mapping':
                return array(35 => 37, 71 => 35, 86 => 48, 33 => 2);
            case 'status':
                $table = 'status';
                $key   = 'id';
                $value = 'status';
                break;
            case 'website':
                $table = '`highwaygroup`.website';
                $key   = 'id';
                $value = 'website';
                return array();
                break;
        }

        if ('' != $table) {
            $query = $DB->query("SELECT " . $key . "," . $value . " FROM " . $table);
            while ($row = $DB->fetch_array($query)) {
                $option_list[$row[0]] = $row[1];
            }

        }

        return $option_list;
    }
}
if (!function_exists('softjobChunk')) {
    function softjobChunk($softjob_list, $group_by = '')
    {
        global $DB;
        if (!is_array($softjob_list) || count($softjob_list) < 1) {
            return $softjob_list;
        }

        if (!is_array($group_by)) {
            $group_by = array($group_by);
        }

        $fields = array();
        if ($query = $DB->query(sprintf(
            "SHOW COLUMNS FROM `highwaygroup`.`bugs`"
        ))) {
            while ($row = $DB->fetch_array($query, MYSQLI_NUM)) {
                $fields[$row[0]] = $row;
            }
            $DB->free_result($query);
        }

        $field_list = array('id');
        foreach ($group_by as $field) {
            if (array_key_exists($field, $fields)) {
                $field_list[] = $field;
            }
        }

        $field_list = array_unique($field_list);

        $results = array();
        if ($query = $DB->query(sprintf(
            "SELECT %s FROM `highwaygroup`.`bugs` WHERE `id` IN(%s)",
            implode(', ', $field_list),
            implode(', ', $softjob_list)
        ))) {
            while ($row = $DB->fetch_assoc($query)) {
                $pointer = &$results;
                foreach ($group_by as $group) {
                    $key = $row[$group];
                    if ('' != $key) {
                        if (!array_key_exists($key, $pointer)) {
                            $pointer[$key] = array();
                        }
                        $pointer = &$pointer[$key];
                    }
                }
                $pointer[] = $row['id'];
            }
            $DB->free_result($query);
        }

        return $results;
    }
}
if (!function_exists('softjobFromPost')) {
    function softjobFromPost($posted)
    {
        $softjob_id = $posted['id'];

        $result       = softjobFromDatabase($softjob_id) ?? softjobDefault();
        $result['id'] = intval($softjob_id);

        foreach ($result as $key => $value) {
            if (array_key_exists($key, $posted) && !is_array($posted[$key])) {
                $result[$key] = trim($posted[$key]);
            }
        }

        if ($softjob_id <= 0) {
            unset($result['id']);
            $result['date_added'] = formatDateTime();
            $result['time_added'] = time();
        }

        if (isset($posted['time_value']) && isset($posted['time_units'])) {
            $time = $posted['time_value'];
            if ('minutes' == $posted['time_units']) {
                $time *= 60;
            }

            if ('hours' == $posted['time_units']) {
                $time *= 60 * 60;
            }

            if ('days' == $posted['time_units']) {
                $time *= 60 * 60 * 24;
            }

            if ('weeks' == $posted['time_units']) {
                $time *= 60 * 60 * 24 * 7;
            }

            if ('months' == $posted['time_units']) {
                $time *= 60 * 60 * 24 * 30.445833;
            }

            if ('years' == $posted['time_units']) {
                $time *= 60 * 60 * 24 * 365.25;
            }

            $result['time_guess'] = $time;
        }
        return $result;
    }
}
if (!function_exists('softjobToDatabase')) {
    function softjobToDatabase($summary)
    {
        global $_GLOBALS;
        global $DB;

        $softjob_id = intval($summary['id']);

        $safety = $summary;

        $old_softjob = softjobFromDatabase($softjob_id) ?? softjobDefault();

        foreach ($old_softjob as $key => $value) {
            if ($summary[$key] == $value) {
                unset($summary[$key]);
            }
        }

        foreach ($summary as $key => $value) {
            if (!array_key_exists($key, $old_softjob)) {
                unset($summary[$key]);
            }
        }

        $summary['last_modified'] = formatDateTime();
        if (3 == $summary['status']) {
            $summary['date_fixed'] = $summary['last_modified'];
        }

        $fields = array();
        if ($query = $DB->query(sprintf(
            "SHOW COLUMNS FROM `highwaygroup`.`bugs`"
        ))) {
            while ($row = $DB->fetch_array($query, MYSQLI_NUM)) {
                $fields[$row[0]] = $row;
            }
            $DB->free_result($query);
        }

        if (count($summary) > 0) {
            $staff_id      = $_GLOBALS['staff']['id'];
            $staff_mapping = softjobGetOptionList('staff_mapping');
            if (0 != $staff_mapping[$staff_id]) {
                $staff_id = $staff_mapping[$staff_id];
            }

            if (softjobExists($softjob_id)) {
                $set_vals = array();
                foreach ($summary as $key => $value) {
                    if (array_key_exists($key, $fields)) {
                        if (isset($summary[$key])) {
                            $set_vals[$key] = "`" . $key . "`=" . $DB->escape($value);
                        } else {
                            $set_vals[$key] = "`" . $key . "`=NULL";
                        }
                    }
                }

                if (count($set_vals)) {
                    if ($DB->query($sql = sprintf(
                        "UPDATE `highwaygroup`.`bugs` SET %s WHERE `id` = %d LIMIT 1",
                        implode(', ', $set_vals),
                        $softjob_id
                    ))) {
                        $DB->query(sprintf(
                            "INSERT INTO `highwaygroup`.`bug_messages` (`bug_id`, `staff_id`, `message_date`, `message_notes`) VALUES (%d, %d, NOW(), %s)",
                            $softjob_id,
                            $staff_id,
                            $DB->escape($sql)
                        ));
                    }
                }
            } else {
                $summary['date_added'] = formatDateTime();
                $set_vals              = array();
                foreach ($summary as $key => $value) {
                    if (array_key_exists($key, $fields)) {
                        if (isset($summary[$key])) {
                            $set_vals[$key] = $DB->escape($value);
                        } else {
                            $set_vals[$key] = "NULL";
                        }
                    }
                }

                if (count($set_vals)) {
                    if ($DB->query($sql = sprintf(
                        "INSERT INTO `highwaygroup`.`bugs` (%s) VALUES (%s)",
                        implode(', ', array_keys($set_vals)),
                        implode(', ', array_values($set_vals))
                    ))) {
                        $softjob_id = $DB->insert_id();

                        $DB->query(sprintf(
                            "INSERT INTO `highwaygroup`.`bug_messages` (`bug_id`, `staff_id`, `message_date`, `message_notes`) VALUES (%d, %d, NOW(), %s)",
                            $softjob_id,
                            $staff_id,
                            $DB->escape($sql)
                        ));
                    }
                }
            }
        }

        $DB->query(sprintf(
            "INSERT INTO `highwaygroup`.`bug_safety` (`bug_posted`, `bug_query`) VALUES (%s, %s)",
            $DB->escape(json_encode($safety, JSON_PRETTY_PRINT)),
            $DB->escape($sql)
        ));

        return $softjob_id;
    }
}
