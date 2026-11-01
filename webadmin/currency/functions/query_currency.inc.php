<?php
function currencyChunk($currency_list, $group_by = '')
{
    global $DB;
    if (!is_array($currency_list) || count($currency_list) < 1) {
        return $currency_list;
    }

    if (!is_array($group_by)) {
        $group_by = array($group_by);
    }

    foreach ($currency_list as $key => $value) {
        $currency_list[$key] = $DB->escape($value);
    }

    $fields = getTableColumns('currency');
    foreach ($fields as $key => $value) {
        $fields[$key] = 'c.`' . $value . '`';
    }

    foreach (currencyGetOptionList('ext_fields') as $key => $value) {
        $fields[$key] = $value . ' AS ' . $key;
    }

    $field_list = array('code');
    foreach ($group_by as $field) {
        if (array_key_exists($field, $fields)) {
            $field_list[] = $fields[$field];
        }
    }

    $field_list = array_unique($field_list);
    $sql        = "SELECT " . implode(', ', $field_list) . " FROM `highwaygroup`.`currency` AS `c` WHERE `c`.`code` IN(" . implode(', ', $currency_list) . ")";
    $query      = $DB->query($sql);
    $results    = array();
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
        $pointer[] = $row['code'];
    }
    $DB->free_result($query);
    return $results;
}

function currencyExists($currency_id)
{
    global $DB;

    $sql = "SELECT `c`.`code` FROM `highwaygroup`.`currency` AS `c` WHERE `c`.`code` = " . $DB->escape($currency_id) . " LIMIT 1";
    $row = $DB->query_first($sql, MYSQLI_ASSOC);
    if ($row) {
        return true;
    }

    return false;
}

function currencyGetOptionList($table_name)
{
    global $DB;
    static $option_cache = array();
    if (isset($option_cache[$table_name])) {
        return $option_cache[$table_name];
    }

    $table       = '';
    $key         = '';
    $value       = '';
    $option_list = array();
    switch ($table_name) {
        case 'joined_fields':
            $joins = array(
//            'INNER JOIN ..... ON ....'=>getTableColumns('.....')
            );
            foreach (getTableColumns('currency') as $field) {
                foreach ($joins as $join => $rows) {
                    unset($joins[$join][$field]);
                }
            }

            return $joins;

        case 'extra_fields':
        case 'ext_fields':
            return array(
                'code_first_letter' => 'LOWER(SUBSTRING(code FROM 1 FOR 1))',
                'name_first_letter' => 'LOWER(SUBSTRING(name FROM 1 FOR 1))',
            );
    }
    if ('' != $table) {
        $query = $DB->query("SELECT " . $key . "," . $value . " FROM " . $table);
        while ($row = $DB->fetch_array($query)) {
            $option_list[$row[0]] = $row[1];
        }

    }
    $option_cache[$table_name] = $option_list;
    return $option_list;
}

function currencySearch($criteria = '')
{
    global $DB;
    $results = array();

    $sql   = "SELECT `c`.`code` FROM `highwaygroup`.`currency` AS `c`";
    $joins = array();

    $fields = getTableColumns('currency');
    foreach ($fields as $key => $value) {
        $fields[$key] = 'c.`' . $value . '`';
    }

    $fields = array_merge($fields, currencyGetOptionList('ext_fields'));

    $whereAND = array();
    if (is_array($criteria)) {
        foreach ($criteria as $field => $value) {
            if (isset($fields[$field])) {
                if (is_array($value)) {
                    if (count($value) > 0) {
                        $whereOR = array();
                        foreach ($value as $sub_value) {
                            $whereOR[] = "	" . $fields[$field] . "=" . $DB->escape($sub_value);
                        }

                        $whereAND[] = "	(" . implode(" OR ", $whereOR) . ")";
                    } else {
                        return array();
                    }
                } else {
                    $whereAND[] = "	" . $fields[$field] . "=" . $DB->escape($value);
                }

                unset($fields[$field]);
            } elseif (is_string($criteria) && '' != $criteria) {
                $whereAND[] = "	c.code=" . $DB->escape($criteria);
            }
        }
    }

    $search_for = array();
    if (is_array($criteria['any_text'])) {
        $search_for = $criteria['any_text'];
    }

    if (is_string($criteria['any_text'])) {
        $search_for = explode(' ', $criteria['any_text']);
    }

    if (count($search_for) > 0) {
        $searchBits = array('c.code', 'c.name');
        $whereOR    = array();
        foreach ($search_for as $findme) {
            foreach ($searchBits as $key) {
                if (trim($findme) != '') {
                    $whereOR[] = $key . " LIKE '%" . str_replace('%', '\%', str_replace('_', '\_', addslashes($findme))) . "%'";
                }
            }

        }
        if (count($whereOR) > 0) {
            $whereAND[] = "	(" . implode(" OR ", $whereOR) . ")";
        }

    }

    if (count($joins) > 0) {
        $sql .= " " . implode(" ", $joins);
    }

    if (count($whereAND) > 0) {
        $sql .= " WHERE " . implode(" AND ", $whereAND);
    }

    $sql .= " ORDER BY c.name ASC";

    $query = $DB->query($sql);
    while ($row = $DB->fetch_assoc($query)) {
        $results[] = $row['code'];
    }

    $DB->free_result($query);
    return $results;
}

function currencySort($currency_list, $order_by = 'code')
{
    global $DB;

    if (!is_array($currency_list) || count($currency_list) < 2) {
        return $currency_list;
    }

    $fields = array();
    $query  = $DB->query("SHOW COLUMNS FROM `highwaygroup`.`currency`");
    while ($row = $DB->fetch_array($query, MYSQLI_NUM)) {
        $fields[$row[0]] = $row;
    }

    $DB->free_result($query);

    if (!isset($fields[$order_by])) {
        return $currency_list;
    }

    $in_list = array();
    foreach ($currency_list as $code) {
        $in_list[] = $DB->escape($code);
    }

    $sql   = "SELECT `c`.`code` FROM `highwaygroup`.`currency` AS `c`";
    $where = " WHERE c.code IN(" . implode(',', $in_list) . ")";
    if (isset($fields[$order_by])) {
        $order = " ORDER BY " . $order_by . " ASC";
    }

    $currency_list = array();
    $query         = $DB->query($sql . $where . $order);
    while ($row = $DB->fetch_array($query, MYSQLI_ASSOC)) {
        $currency_list[] = $row['code'];
    }

    $DB->free_result($query);

    return $currency_list;
}

function currencyFromDatabase($currency_id)
{
    global $DB;

    $sql = "SELECT `c`.* FROM `highwaygroup`.`currency` AS `c` WHERE `c`.`code` = " . $DB->escape($currency_id) . " LIMIT 1";
    $row = $DB->query_first($sql, MYSQLI_ASSOC);

    if ($row) {
        $summary = $row;
    } else {
        $fields = array();
        $query  = $DB->query("SHOW COLUMNS FROM `highwaygroup`.`currency`");
        while ($row = $DB->fetch_array($query, MYSQLI_NUM)) {
            $fields[$row[0]] = $row;
        }

        $DB->free_result($query);

        $summary = array();
        foreach ($fields as $key => $row) {
            $summary[$key] = $row[4];
        }

    }

    return $summary;
}

function currencyToDatabase($currency)
{
    return;
}

function currencyHistoryFromDatabase($currency_id)
{
    global $DB;
    $results = array();
    $query   = $DB->query("SELECT `cl`.*, UNIX_TIMESTAMP(`cl`.`change_date`) AS `change_time` FROM `highwaygroup`.`currency_log` AS `cl` WHERE `code` = " . $DB->escape($currency_id) . " ORDER BY `change_date` ASC");
    while ($row = $DB->fetch_assoc($query)) {
        $results[$row['log_id']] = $row;
    }

    return $results;
}
