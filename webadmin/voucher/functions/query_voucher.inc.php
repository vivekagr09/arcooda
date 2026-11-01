<?php
if (!function_exists('voucherFromPost')) {
    /**
     * Get voucher from post
     *
     * @param mixed[] $posted Post to get voucher
     *
     * @return mixed[] Returns the result
     */
    function voucherFromPost($posted)
    {
        $voucher_id             = $posted['voucher_id'];
        $new_vals['voucher_id'] = intval($voucher_id);
        $old_vals               = voucherFromDatabase($voucher_id);
        foreach ($old_vals as $key => $value) {
            if (isset($posted[$key])) {
                $new_vals[$key] = $posted[$key];
            }
        }

        $hidden = array('amount_value', 'amount_extra', 'currency', 'terms', 'expiry_date', 'expiry_value');
        if ('preset' == $posted['voucher_type']) {
            foreach ($hidden as $field) {
                $new_vals[$field] = $posted[$field] = $posted['hidden_' . $field];
            }
        }

        $expiry = array('1' => '1 Month', '3' => '3 Months', '6' => '6 Months', '12' => '12 Months', '0' => 'Other');
        if ($old_vals['expiry_value'] != $posted['expiry_value']) {
            if (0 != $posted['expiry_value']) {
                $new_vals['expiry_date'] = formatDateTime(strtotime('+' . $posted['expiry_value'] . ' months'));
            }

        }
        $new_vals['amount'] = $posted['amount_extra'];
        if (0 != $posted['amount_value']) {
            $new_vals['amount'] = $new_vals['amount_value'] = $posted['amount_value'];
        }

        return $new_vals;
    }
}
if (!function_exists('voucherFromDatabase')) {
    /**
     * Get voucher from database
     *
     * @param int $voucher_id Voucher id for the query
     *
     * @return mixed[] Returns the result
     */
    function voucherFromDatabase($voucher_id)
    {
        global $DB;
        $voucher_id = intval($voucher_id);
        if (!$voucher_id) {
            $summary = array();
            foreach (getTableFields('`highwaygroup`.`gift_voucher`') as $row) {
                $summary[$row[0]] = $row[4];
            }

            return $summary;
        }
        $ext_fields = voucherGetOptionList('ext_fields');
        $ext_str    = array();
        foreach ($ext_fields as $key => $value) {
            $ext_str[] = $value . ' AS ' . $key;
        }

        $sql = " SELECT" .
            "	g.*" . ((count($ext_fields)) ? (",	" . implode(", ", $ext_str)) : '') .
            " FROM `highwaygroup`.`gift_voucher` g" .
            " WHERE g.id=" . $voucher_id;

        $voucher = $DB->query_first($sql, MYSQLI_ASSOC);

        if ('y' == $voucher['expired'] && 'open' == $voucher['status']) {
            $voucher['status'] = 'expired';
        }

        return $voucher;
    }
}
if (!function_exists('voucherToDatabase')) {
    /**
     * To add voucher to database
     *
     * @param mixed[] $summary Summary of voucher
     *
     * @return int Returns the id of voucher added
     */
    function voucherToDatabase($summary)
    {
        global $DB, $_GLOBALS;
        $voucher_id = intval($summary['voucher_id']);

        $old_voucher = voucherFromDatabase($voucher_id);

        foreach ($old_voucher as $key => $value) {
            if ((!is_array($summary[$key]) && !is_array($value) && formatSQLString($summary[$key]) == formatSQLString($value)) || $summary[$key] == $value) {
                unset($summary[$key]);
            }
        }

        foreach ($summary as $key => $value) {
            if (!array_key_exists($key, $old_voucher)) {
                unset($summary[$key]);
            }
        }

        $fields = array();
        $query  = $DB->query("SHOW COLUMNS FROM `highwaygroup`.`gift_voucher`");
        while ($row = $DB->fetch_array($query, MYSQLI_NUM)) {
            $fields[$row[0]] = $row;
        }

        $DB->free_result($query);

        $message = array('type' => 'change', 'staff_id' => $_GLOBALS['staff']['staff_id']);

        if (count($summary) > 0) {
            if (voucherExists($voucher_id)) {
                $set_vals = array();
                foreach ($summary as $key => $value) {
                    if (array_key_exists($key, $fields)) {
                        if (isset($summary[$key])) {
                            $set_vals[$key] = "`" . $key . "`=" . formatSQLString($value);
                        } else {
                            $set_vals[$key] = "`" . $key . "`=NULL";
                        }
                    }
                }

                $sql = "UPDATE `highwaygroup`.`gift_voucher` SET " . implode(', ', $set_vals) . " WHERE id=" . $voucher_id . " LIMIT 1";
                $DB->query($sql);

                if (count($set_vals) > 0) {
                    $add_msg            = $message;
                    $add_msg['message'] = "Gift Voucher Details Modified:\n" . $sql;
                    voucherMessageToDatabase($voucher_id, $add_msg);
                }
            } else {
                $set_vals = array();
                foreach ($summary as $key => $value) {
                    if (array_key_exists($key, $fields)) {
                        if (isset($summary[$key])) {
                            $set_vals[$key] = formatSQLString($value);
                        } else {
                            $set_vals[$key] = 'NULL';
                        }
                    }
                }

                $set_vals['date_added'] = "NOW()";
                $sql                    = "INSERT INTO `highwaygroup`.`gift_voucher`(`" . implode('`, `', array_keys($set_vals)) . "`) VALUES(" . implode(", ", $set_vals) . ")";
                $query                  = $DB->query($sql);
                $voucher_id             = $DB->insert_id();

                $add_msg            = $message;
                $add_msg['message'] = "Gift Voucher Added To Database:\n" . $sql;
                voucherMessageToDatabase($voucher_id, $add_msg);
            }
        }
        return $voucher_id;
    }
}
if (!function_exists('voucherExists')) {
    /**
     * To check if voucher exists in the database
     *
     * @param int $voucher_id Id of voucher to check
     *
     * @return mixed[] Returns the result of query
     */
    function voucherExists($voucher_id)
    {
        global $DB;
        $voucher_id = intval($voucher_id);
        return $DB->query_first("SELECT `id` FROM `highwaygroup`.`gift_voucher` WHERE `id` = " . $voucher_id . " LIMIT 1", MYSQLI_ASSOC);
    }
}
if (!function_exists('voucherPresetFromPost')) {
    /**
     * Get voucher preset from post
     *
     * @param mixed[] $posted Post to get voucher
     *
     * @return mixed[] Returns the result
     */
    function voucherPresetFromPost($posted)
    {
        global $DB;
        $preset_id             = $posted['preset_id'];
        $new_vals['preset_id'] = intval($preset_id);
        $old_vals              = voucherPresetFromDatabase($preset_id);
        foreach ($old_vals as $key => $value) {
            if (isset($posted[$key])) {
                $new_vals[$key] = $posted[$key];
            }
        }

        $expiry = array('1' => '1 Month', '3' => '3 Months', '6' => '6 Months', '12' => '12 Months', '0' => 'Other');
        if ($old_vals['expiry_value'] != $posted['expiry_value']) {
            if (0 != $posted['expiry_value']) {
                $new_vals['expiry_date'] = formatDateTime(strtotime('+' . $posted['expiry_value'] . ' months'));
            }

        }
        $new_vals['amount'] = $posted['amount_extra'];
        if (0 != $posted['amount_value']) {
            $new_vals['amount'] = $new_vals['amount_value'] = $posted['amount_value'];
        }

        return $new_vals;
    }
}
if (!function_exists('voucherPresetFromDatabase')) {
    /**
     * Get voucher preset from database
     *
     * @param int $preset_id Preset id for the query
     *
     * @return mixed[] Returns the result
     */
    function voucherPresetFromDatabase($preset_id)
    {
        global $DB;
        $preset_id = intval($preset_id);
        if (!$preset_id) {
            $summary = array();
            foreach (getTableFields('`highwaygroup`.`gift_preset`') as $row) {
                $summary[$row[0]] = $row[4];
            }

            return $summary;
        }

        return $DB->query_first("SELECT * FROM `highwaygroup`.`gift_preset` AS `p` WHERE `p`.`id` = " . $preset_id . " LIMIT 1", MYSQLI_ASSOC);
    }
}
if (!function_exists('voucherPresetToDatabase')) {
    /**
     * To add voucher preset to database
     *
     * @param mixed[] $summary Summary of voucher
     *
     * @return int Returns the id of preset added
     */
    function voucherPresetToDatabase($summary)
    {
        global $DB, $_GLOBALS;
        $preset_id = intval($summary['preset_id']);

        $old_preset = voucherPresetFromDatabase($preset_id);

        foreach ($old_preset as $key => $value) {
            if ((!is_array($summary[$key]) && !is_array($value) && formatSQLString($summary[$key]) == formatSQLString($value)) || $summary[$key] == $value) {
                unset($summary[$key]);
            }
        }

        foreach ($summary as $key => $value) {
            if (!array_key_exists($key, $old_preset)) {
                unset($summary[$key]);
            }
        }

        $fields = array();
        $query  = $DB->query("SHOW COLUMNS FROM `highwaygroup`.`gift_preset`");
        while ($row = $DB->fetch_array($query, MYSQLI_NUM)) {
            $fields[$row[0]] = $row;
        }

        $DB->free_result($query);

        $message = array('type' => 'change', 'staff_id' => $_GLOBALS['staff']['staff_id']);

        if (count($summary) > 0) {
            if (voucherPresetExists($preset_id)) {
                $set_vals = array();
                foreach ($summary as $key => $value) {
                    if (array_key_exists($key, $fields)) {
                        if (isset($summary[$key])) {
                            $set_vals[$key] = "`" . $key . "`=" . formatSQLString($value);
                        } else {
                            $set_vals[$key] = "`" . $key . "`=NULL";
                        }
                    }
                }

                $sql = "UPDATE `highwaygroup`.`gift_preset` SET " . implode(', ', $set_vals) . " WHERE id=" . $preset_id . " LIMIT 1";
                $DB->query($sql);
            } else {
                $set_vals = array();
                foreach ($summary as $key => $value) {
                    if (array_key_exists($key, $fields)) {
                        if (isset($summary[$key])) {
                            $set_vals[$key] = formatSQLString($value);
                        } else {
                            $set_vals[$key] = 'NULL';
                        }
                    }
                }

                $sql       = "INSERT INTO `highwaygroup`.`gift_preset`(`" . implode('`, `', array_keys($set_vals)) . "`) VALUES(" . implode(", ", $set_vals) . ")";
                $query     = $DB->query($sql);
                $preset_id = $DB->insert_id();
            }
        }
        return $preset_id;
    }
}
if (!function_exists('voucherPresetExists')) {
    /**
     * To check if preset exists in the database
     *
     * @param int $preset_id Id of preset to check
     *
     * @return mixed[] Returns the result of query
     */
    function voucherPresetExists($preset_id)
    {
        global $DB;
        $preset_id = intval($preset_id);
        return $DB->query_first("SELECT id FROM `highwaygroup`.`gift_preset` WHERE id=" . $preset_id, MYSQLI_ASSOC);
    }
}
if (!function_exists('voucherMessageSearch')) {
    /**
     * To search voucher message by criteria
     *
     * @param mixed[]|string $criteria The criteria used in query
     *
     * @return mixed[] Returns the result of the query
     */
    function voucherMessageSearch($criteria = '')
    {
        global $DB;
        $results = array();

        $sql   = " SELECT gm.id FROM `highwaygroup`.`gift_messages` gm";
        $joins = array();

        $fields = array();
        $query  = $DB->query("SHOW COLUMNS FROM `highwaygroup`.`gift_messages`");
        while ($row = $DB->fetch_array($query, MYSQLI_NUM)) {
            $fields[$row[0]] = $row;
        }

        $DB->free_result($query);

        $fields2 = array();
        $query   = $DB->query("SHOW COLUMNS FROM `highwaygroup`.`gift_voucher`");
        while ($row = $DB->fetch_array($query, MYSQLI_NUM)) {
            if (!isset($fields[$row[0]])) {
                $fields2[$row[0]] = $row;
            }
        }

        $DB->free_result($query);

        $whereAND = array();
        if (is_array($criteria)) {
            foreach ($criteria as $field => $value) {
                if (isset($fields[$field])) {
                    if (is_array($value)) {
                        if (count($value) > 0) {
                            $whereOR = array();
                            foreach ($value as $sub_value) {
                                $whereOR[] = "	gm.`" . $field . "`=" . formatSQLString($sub_value);
                            }

                            $whereAND[] = "	(" . implode(" OR ", $whereOR) . ")";
                        } else {
                            return array();
                        }
                    } else {
                        $whereAND[] = "	gm.`" . $field . "`=" . formatSQLString($value);
                    }

                    unset($fields[$field]);
                }
                if (isset($fields2[$field])) {
                    $joins['invoice'] = " INNER JOIN `highwaygroup`.`gift_voucher` gv ON gv.id=gm.voucher_id";
                    if (is_array($value)) {
                        if (count($value) > 0) {
                            $whereOR = array();
                            foreach ($value as $sub_value) {
                                $whereOR[] = "	gv.`" . $field . "`=" . formatSQLString($sub_value);
                            }

                            $whereAND[] = "	(" . implode(" OR ", $whereOR) . ")";
                        } else {
                            return array();
                        }

                    } else {
                        $whereAND[] = "	i.`" . $field . "`=" . formatSQLString($value);
                    }

                    unset($fields2[$field]);
                }
            }
        } elseif (is_string($criteria) && '' != $criteria) {
            $whereAND[] = "	gv.voucher_id=" . formatSQLString($criteria);
        }

        $search_for = array();
        if (is_array($criteria) && array_key_exists("any_text", $criteria)) {
            if (is_array($criteria['any_text'])) {
                $search_for = $criteria['any_text'];
            }

            if (is_string($criteria['any_text'])) {
                $search_for = explode(' ', $criteria['any_text']);
            }

        }
        if (count($search_for) > 0) {
            $searchBits = array('gm.message');
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

        if (count($joins) > 0) {
            $sql .= implode("", $joins);
        }

        if (count($whereAND) > 0) {
            $sql .= " WHERE " . implode(" AND ", $whereAND);
        }

        $sql .= " ORDER BY gm.date DESC, gm.id DESC";

        $query = $DB->query($sql);
        while ($row = $DB->fetch_array($query, MYSQLI_ASSOC)) {
            $results[] = $row['id'];
        }

        $DB->free_result($query);
        return $results;
    }
}
if (!function_exists('voucherMessageToDatabase')) {
    /**
     * To add voucher message to database
     *
     * @param int     $voucher_id Id of voucher
     * @param mixed[] $summary Data of voucher
     *
     * @return void
     */
    function voucherMessageToDatabase($voucher_id, $summary)
    {
        global $DB;
        $voucher_id = intval($voucher_id);
        if (!$voucher_id) {
            return;
        }

        $DB->query(" INSERT INTO `highwaygroup`.`gift_messages` SET" .
            "	voucher_id=" . intval($voucher_id) . "," .
            "	date=NOW()," .
            "	message=" . formatSQLString($summary['message']) . "," .
            "	staff_id=" . formatSQLString($summary['staff_id']) . "," .
            "	type=" . formatSQLString($summary['type']));
    }
}
if (!function_exists('voucherMessageFromDatabase')) {
    /**
     * To get voucher message from database
     *
     * @param int $message_id Id of message to be used in query
     *
     * @return mixed[] Returns the result of query
     */
    function voucherMessageFromDatabase($message_id)
    {
        global $DB;
        $sql = " SELECT" .
        "	gm.*," .
        "	UNIX_TIMESTAMP(gm.date) AS message_time" .
        " FROM `highwaygroup`.`gift_messages` gm" .
        " WHERE gm.id=" . intval($message_id) .
            " LIMIT 1";
        return $DB->query_first($sql, MYSQLI_ASSOC);
    }
}
if (!function_exists('voucherSearch')) {
    /**
     * To serach voucher by criteria
     *
     * @param mixed[]|string $criteria Criteria used to search voucher
     *
     * @return mixed[] Returns the result of query
     */
    function voucherSearch($criteria = '')
    {
        global $DB;
        $results = array();

        if (is_string($criteria) && '' != $criteria) {
            $criteria = array('any_text' => $criteria);
        }

        $sql = " SELECT g.id FROM `highwaygroup`.`gift_voucher` g";

        $field_types = getTableFields('`highwaygroup`.`gift_voucher`');
        $fields      = array();
        foreach ($field_types as $row) {
            $fields[$row[0]] = 'g.`' . $row[0] . '`';
        }

        $fields = array_merge($fields, voucherGetOptionList('ext_fields'));

        $whereAND = array();
        if (is_array($criteria)) {
            foreach ($criteria as $field => $value) {
                if (isset($fields[$field])) {
                    if (is_array($value)) {
                        if (count($value) > 0) {
                            $whereOR = array();
                            if (array_key_exists($field, $field_types) && substr($field_types[$field]['Type'], 0, 4) == 'set(') {
                                foreach ($value as $sub_value) {
                                    $whereOR[] = "	FIND_IN_SET(" . formatSQLString($sub_value) . "," . $fields[$field] . ")";
                                }
                            } else {
                                foreach ($value as $sub_value) {
                                    $whereOR[] = "	" . $fields[$field] . "=" . formatSQLString($sub_value);
                                }
                            }

                            $whereAND[] = "	(" . implode(" OR ", $whereOR) . ")";
                        } else {
                            return array();
                        }

                    } else {
                        if (array_key_exists($field, $field_types) && substr($field_types[$field]['Type'], 0, 4) == 'set(') {
                            $whereAND[] = "	FIND_IN_SET(" . formatSQLString($value) . "," . $fields[$field] . ")";
                        } else {
                            $whereAND[] = "	" . $fields[$field] . "=" . formatSQLString($value);
                        }

                    }
                    unset($fields[$field]);
                }
            }
        }

        $search_for = array();
        if (is_array($criteria) && array_key_exists("any_text", $criteria)) {
            if (is_array($criteria['any_text'])) {
                $search_for = $criteria['any_text'];
            }

            if (is_string($criteria['any_text'])) {
                $search_for = explode(' ', $criteria['any_text']);
            }

        }
        if (count($search_for) > 0) {
            $searchBits = array('g.id', 'g.member_id', 'g.member_name');
            foreach ($search_for as $findme) {
                $whereOR = array();
                foreach ($searchBits as $key) {
                    if (trim($findme) != '') {
                        $whereOR[] = $key . " LIKE " . formatSQLLikeString($findme);
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

        $sql .= " ORDER BY g.id ASC";

        if ($query = $DB->query($sql)) {
            while ($row = $DB->fetch_assoc($query)) {
                $results[] = $row['id'];
            }
            $DB->free_result($query);
        }

        return $results;
    }
}
if (!function_exists('voucherSort')) {
    /**
     * To sort the list of voucher
     *
     * @param mixed[][] $voucher_list List of voucher
     * @param string    $order_by      Field to be ordered by
     *
     * @return mixed[][] Returns the sorted voucher list
     */
    function voucherSort($voucher_list, $order_by = 'id')
    {
        global $DB;
        if (!is_array($voucher_list) || count($voucher_list) < 2) {
            return $voucher_list;
        }

        $fields = getTableFields('`highwaygroup`.`gift_voucher`');
        $sql    = " SELECT g.id FROM `highwaygroup`.`gift_voucher` g";
        $where  = " WHERE g.id IN('" . implode("', '", $voucher_list) . "')";
        $order  = '';
        if (isset($fields[$order_by])) {
            $order = " ORDER BY " . $order_by . " ASC";
        }

        $voucher_list = array();
        //if($_SERVER['REMOTE_USER']=='Jwalit') echo $sql.$where.$order.'<br />';
        $query = $DB->query($sql . $where . $order);
        while ($row = $DB->fetch_assoc($query)) {
            $voucher_list[] = $row['id'];
        }

        $DB->free_result($query);

        return $voucher_list;
    }
}
if (!function_exists('voucherGetOptionList')) {
    /**
     * Get option list of voucher
     *
     * @param string $table_name Name of option list
     *
     * @return mixed[] Returns options list if table name is 'ext_fields', returns empty array otherwise
     */
    function voucherGetOptionList($table_name = '')
    {
        switch ($table_name) {
            case 'ext_fields':
                return array(
                    "expired"      => "IF((status!='expired' AND (UNIX_TIMESTAMP()<=UNIX_TIMESTAMP(expiry_date) || UNIX_TIMESTAMP(expiry_date)=0)),'n','y')",
                    "expiry_time"  => "UNIX_TIMESTAMP(expiry_date)",
                    "time_added"   => "UNIX_TIMESTAMP(date_added)",
                    "process_time" => "UNIX_TIMESTAMP(process_date)",
                );
        }
        return array();
    }
}
if (!function_exists('voucherGetValue')) {
    /**
     * To get the value of voucher
     *
     * @param int    $voucher_id Id of voucher
     * @param string $field      Field of value to be queried
     *
     * @return mixed Returns the result of query
     */
    function voucherGetValue($voucher_id, $field)
    {
        global $DB;
        $fields = getTableFields('`highwaygroup`.`gift_voucher`');
        if (array_key_exists($field, $fields)) {
            $row = $DB->query_first("SELECT `" . $field . "` FROM `highwaygroup`.`gift_voucher` g WHERE g.id=" . intval($voucher_id) . " LIMIT 1", MYSQLI_ASSOC);
            return $row[$field];
        }
        $special_fields = voucherGetOptionList('ext_fields');
        if (array_key_exists($field, $special_fields)) {
            $row = $DB->query_first("SELECT " . $special_fields[$field] . " AS " . $field . " FROM `highwaygroup`.`gift_voucher` g WHERE g.id=" . intval($voucher_id) . " LIMIT 1", MYSQLI_ASSOC);
            return $row[$field];
        }
        return null;
    }
}
if (!function_exists('voucherHasValue')) {
    /**
     * To check if voucher has value
     *
     * @param int    $voucher_id Id of voucher
     * @param string $field      Field of value
     * @param int    $value      Value to be match
     *
     * @return boolean Returns true if value exists, false otherwise
     */
    function voucherHasValue($voucher_id, $field, $value)
    {
        return (voucherGetValue($voucher_id, $field) == $value);
    }
}
if (!function_exists('voucherFormatNumber')) {
    /**
     * To format the number in voucher
     *
     * @param int $voucher_id Id of voucher
     *
     * @return string Returns formated number
     */
    function voucherFormatNumber($voucher_id)
    {
        return sprintf('GV-%04d', $voucher_id);
    }
}
if (!function_exists('voucherTermsList')) {
    /**
     * Get the terms list of voucher
     *
     * @return mixed[] Returns the result of query
     */
    function voucherTermsList()
    {
        global $DB;
        $terms = array();
        $res   = $DB->query("SELECT * FROM `highwaygroup`.`gift_terms`");
        while ($row = $DB->fetch_assoc($res)) {
            $terms[$row['id']] = htmlspecialchars($row['display']);
        }

        return $terms;
    }
}
if (!function_exists('voucherGetLock')) {
    /**
     * Get the voucher lock
     *
     * @param int $voucher_id Id of voucher
     *
     * @return mixed[] Returns the result of query
     */
    function voucherGetLock($voucher_id)
    {
        global $DB;
        $row = $DB->query_first("SELECT *, UNIX_TIMESTAMP(expiry) AS expiry_time FROM `highwaygroup`.`gift_lock` WHERE voucher_id=" . intval($voucher_id) . " LIMIT 1", MYSQLI_ASSOC);
        if ($row['expiry_time'] < time()) {
            $row = false;
        }

        return $row;
    }
}
if (!function_exists('voucherSetLock')) {
    /**
     * Set the voucher lock
     *
     * @param int        $voucher_id Id of voucher to be locked
     * @param int|string $staff_id   Id of staff
     * @param int        $timeout    Value of timeout
     *
     * @return void
     */
    function voucherSetLock($voucher_id, $staff_id = '', $timeout = 0)
    {
        global $DB;
        $DB->query("DELETE FROM `highwaygroup`.`gift_lock` WHERE voucher_id=" . intval($voucher_id));
        if (0 == $timeout) {
            $timeout = 300;
        }

        if ('' != $staff_id) {
            $DB->query("INSERT INTO `highwaygroup`.`gift_lock` (voucher_id,staff_id,expiry) VALUES(" . intval($voucher_id) . "," . formatSQLString($staff_id) . "," . formatSQLDateTime(time() + $timeout) . ")");
        }
    }
}
