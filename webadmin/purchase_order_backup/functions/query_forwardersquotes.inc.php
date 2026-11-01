<?php
function forwardersQuotesChunk($quote_list, $group_by = '')
{
    global $DB;
    if (!is_array($quote_list) || count($quote_list) < 1) {
        return $quote_list;
    }

    if (!is_array($group_by)) {
        $group_by = array($group_by);
    }

    $fields = getTableColumns('freight_services_quotes');
    foreach ($fields as $key => $value) {
        $fields[$key] = 'fq.`' . $value . '`';
    }

    foreach (forwardersGetOptionList('ext_fields') as $key => $value) {
        $fields[$key] = $value . ' AS ' . $key;
    }

    $field_list = array('id');
    foreach ($group_by as $field) {
        if (array_key_exists($field, $fields)) {
            $field_list[] = $fields[$field];
        }
    }

    $field_list = array_unique($field_list);
    $sql        = "SELECT " . implode(', ', $field_list) . " FROM `freight_services_quotes` AS `fq` WHERE `fq`.`id` IN(" . implode(', ', $quote_list) . ")";
    $query      = $DB->query($sql);
    $results    = array();
    while ($row = $DB->fetch_assoc($query)) {
        $pointer = &$results;
        foreach ($group_by as $group) {
            $key = $row[$group];
            if ('' == $key) {
                $key = ' ';
            }

//            {
            if (!array_key_exists($key, $pointer)) {
                $pointer[$key] = array();
            }

            $pointer = &$pointer[$key];
//            }
        }
        $pointer[] = $row['id'];
    }
    $DB->free_result($query);
    return $results;
}

function forwardersQuotesGetOptionList($table_name)
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
        case 'product_id':
            $query   = $DB->query("SELECT `name`, `id` FROM `freight_services` WHERE `type` = 'sea' OR `type` = 'air' OR `type` = 'sea_air' ORDER BY `name`");
            $results = array();
            while ($row = $DB->fetch_assoc($query)) {
                $results[$row['id']] = $row['name'];
            }

            return $results;
        case 'sea_product_id':
            $query   = $DB->query("SELECT `name`, `id` FROM `freight_services` WHERE `type` = 'sea' OR `type` = 'sea_air' ORDER BY `name`");
            $results = array();
            while ($row = $DB->fetch_assoc($query)) {
                $results[$row['id']] = $row['name'];
            }

            return $results;
        case 'air_product_id':
            $query   = $DB->query("SELECT `name`, `id` FROM `freight_services` WHERE `type` = 'air' OR `type` = 'sea_air' ORDER BY `name`");
            $results = array();
            while ($row = $DB->fetch_assoc($query)) {
                $results[$row['id']] = $row['name'];
            }

            return $results;
        case 'country_from':
        case 'country_to':
            return getCountryList();
        case 'joined_fields':
            $joins = array(
            );
            foreach (getTableColumns('freight_services_quotes') as $field) {
                foreach ($joins as $join => $rows) {
                    unset($joins[$join][$field]);
                }
            }

            return $joins;
        case 'extra_fields':
        case 'ext_fields':
            return array(
                'time'           => 'UNIX_TIMESTAMP(date)',
                'validity_time'  => 'UNIX_TIMESTAMP(validity_date)',
                'arrival_time'   => 'UNIX_TIMESTAMP(arrival_date)',
                'departure_time' => 'UNIX_TIMESTAMP(departure_date)',
//            'transit_time'=>'(UNIX_TIMESTAMP(arrival_date)-UNIX_TIMESTAMP(departure_date))'
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

function forwardersQuotesGetValue($quote_id, $field)
{
    global $DB;

    $fields = getTableColumns('freight_services_quotes');
    foreach ($fields as $key => $value) {
        $fields[$key] = 'fq.`' . $value . '`';
    }

    $fields = array_merge($fields, forwardersQuotesGetOptionList('ext_fields'));

    if (array_key_exists($field, $fields)) {
        $row = $DB->query_first("SELECT " . $fields[$field] . " FROM `freight_services_quotes` AS `fq` WHERE `fq`.`id` = " . intval($quote_id), MYSQLI_NUM);
        return $row[0];
    }
    return null;
}

function forwardersQuotesSearch($criteria = array())
{
    global $DB;
    $results = array();

    $sql   = "SELECT `fq`.`id` FROM `freight_services_quotes` AS `fq`";
    $joins = array();

    $fields = getTableColumns('freight_services_quotes');
    foreach ($fields as $key => $value) {
        $fields[$key] = 'fq.`' . $value . '`';
    }

    $fields = array_merge($fields, forwardersQuotesGetOptionList('ext_fields'));

    $whereAND = array();
    if (is_array($criteria)) {
        foreach ($criteria as $field => $value) {
            if (isset($fields[$field])) {
                if (is_array($value)) {
                    if (count($value) > 0) {
                        $whereOR = array();
                        foreach ($value as $sub_value) {
                            $whereOR[] = "	" . $fields[$field] . "=" . formatSQLString($sub_value);
                        }

                        $whereAND[] = "	(" . implode(" OR ", $whereOR) . ")";
                    } else {
                        return array();
                    }
                } else {
                    $whereAND[] = "	" . $fields[$field] . "=" . formatSQLString($value);
                }

                unset($fields[$field]);
            } elseif (is_string($criteria) && '' != $criteria) {
                $whereAND[] = "	fq.product_id=" . intval($criteria);
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
        $searchBits = array('fq.invoice_id', 'fq.customer_id', 'fq.customer_name', 'fq.transit_time', 'fq.lading_number', 'fq.container_number', 'fq.vessel_name', 'fq.master_number', 'fq.house_number', 'fq.general_notes');

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
        $sql .= " " . implode(" ", $joins);
    }

    if (count($whereAND) > 0) {
        $sql .= " WHERE " . implode(" AND ", $whereAND);
    }

    $sql .= " ORDER BY fq.id ASC";

    $query = $DB->query($sql);
    while ($row = $DB->fetch_array($query, MYSQLI_ASSOC)) {
        $results[] = $row['id'];
    }

    $DB->free_result($query);
    return $results;
}

function forwardersQuotesSort($quote_list, $order_by = 'id')
{
    global $DB;
    if (!is_array($quote_list) || count($quote_list) < 2) {
        return $quote_list;
    }

    $fields = getTableFields('freight_services_quotes');
    if (!isset($fields[$order_by])) {
        return $quote_list;
    }

    $sql   = "SELECT `fq`.`id` FROM `freight_services_quotes` AS `fq`";
    $where = " WHERE fq.id IN(" . implode(',', $quote_list) . ")";
    if (isset($fields[$order_by])) {
        $order = " ORDER BY " . $order_by . " ASC";
    }

    $quote_list = array();
    $query      = $DB->query($sql . $where . $order);
    while ($row = $DB->fetch_array($query, MYSQLI_ASSOC)) {
        $quote_list[] = $row['id'];
    }

    $DB->free_result($query);

    return $quote_list;
}

function forwardersQuoteExists($quote_id)
{
    global $DB;
    $quote_id = intval($quote_id);
    if (!$quote_id) {
        return false;
    }

    $row = $DB->query_first("SELECT `id` FROM `freight_services_quotes` WHERE `id` = $quote_id", MYSQLI_ASSOC);
    if ($row) {
        return true;
    }

    return false;
}

function forwardersQuoteFromDatabase($quote_id)
{
    global $DB;
    $quote_id = intval($quote_id);

    $fields = array('fq.*');
    foreach (forwardersQuotesGetOptionList('ext_fields') as $key => $value) {
        $fields[] = $value . ' AS ' . $key;
    }

    $sql = "SELECT " . implode(', ', $fields) . " FROM `freight_services_quotes` AS `fq` WHERE `fq`.`id` = " . $quote_id . " LIMIT 1";
    $row = $DB->query_first($sql, MYSQLI_ASSOC);

    if ($row) {
        $summary = $row;
    } else {
        $fields  = getTableFields('freight_services_quotes');
        $summary = array();
        foreach ($fields as $key => $row) {
            $summary[$key] = $row[4];
        }

    }
    return $summary;
}

function forwardersQuotesFromDatabase($forwarder_id)
{
    global $DB;
    $forwarder_id = intval($forwarder_id);
    $quotes       = array();
    $query        = $DB->query("SELECT *, UNIX_TIMESTAMP(`date`) AS `time_added` FROM `freight_services_quotes` WHERE `product_id` = " . $forwarder_id . " ORDER BY `date` DESC, `id` DESC");
    while ($row = $DB->fetch_assoc($query)) {
        $quotes[$row['id']] = $row;
    }

    return $quotes;
}

function forwardersQuotesFromPost($forwarder_id, $posted)
{
    global $DB;
    $forwarder_id = intval($forwarder_id);
    $fields       = getTableFields('freight_services_quotes');
    $quotes       = array();
    if (is_array($posted)) {
        foreach ($posted as $posted_quote) {
            $quote = array();
            foreach ($fields as $key => $row) {
                if (is_null($posted_quote[$key])) {
                    $quote[$key] = $row[4];
                } else {
                    $quote[$key] = $posted_quote[$key];
                }
            }

            if ($forwarder_id > 0) {
                $quote['product_id'] = $forwarder_id;
            } else {
                unset($quote['product_id']);
            }

            $quotes[] = $quote;
        }
    }

    return $quotes;
}

function forwardersQuotesToDatabase($forwarder_id, $quotes)
{
    global $DB;
    $forwarder_id = intval($forwarder_id);
    if (0 == $forwarder_id) {
        return;
    }

    $sql = "DELETE FROM `freight_services_quotes` WHERE `product_id` = " . $forwarder_id;
    $DB->query($sql);

    $fields = array();
    $query  = $DB->query("SHOW COLUMNS FROM `freight_services_quotes`");
    while ($row = $DB->fetch_array($query)) {
        if ('auto_increment' != $row[5]) {
            $fields[$row[0]] = $row[4];
        }
    }

    foreach ($quotes as $quote) {
        $insert_vals         = array();
        $quote['product_id'] = $forwarder_id;
        foreach ($quote as $key => $value) {
            if (isset($fields[$key])) {
                $insert_vals[$key] = addslashes($value);
            }
        }

        $sql = "INSERT INTO `freight_services_quotes` (`" . implode('`, `', array_keys($insert_vals)) . "`) VALUES('" . implode("', '", $insert_vals) . "')";
        $DB->query($sql);
    }
}
