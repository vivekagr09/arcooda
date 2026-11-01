<?php
function forwardersAccountsFromDatabase($forwarder_id)
{
    global $DB;
    $forwarder_id = intval($forwarder_id);
    $accounts     = array();
    $sql          = " SELECT fw.* FROM freight_services_bank fw WHERE fw.product_id=" . $forwarder_id;
    $query        = $DB->query($sql);
    while ($row = $DB->fetch_assoc($query)) {
        $accounts[$row['id']] = $row;
    }

    return $accounts;
}

function forwardersAccountsFromPost($forwarder_id, $posted)
{
    global $DB;
    $forwarder_id = intval($forwarder_id);
    $fields       = getTableFields('freight_services_bank');
    $accounts     = array();
    if (is_array($posted)) {
        foreach ($posted as $posted_account) {
            $account = array();
            foreach ($fields as $key => $row) {
                if (is_null($posted_account[$key])) {
                    $account[$key] = $row[4];
                } else {
                    $account[$key] = $posted_account[$key];
                }
            }

            if ($forwarder_id > 0) {
                $account['product_id'] = $forwarder_id;
            } else {
                unset($account['product_id']);
            }

            $accounts[] = $account;
        }
    }

    return $accounts;
}

function forwardersAccountsGetOptionList($table_name)
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
            );
            foreach (getTableColumns('freight_services_bank') as $field) {
                foreach ($joins as $join => $rows) {
                    unset($joins[$join][$field]);
                }
            }

            return $joins;
        case 'extra_fields':
        case 'ext_fields':
            return array(
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

function forwardersAccountsGetValue($account_id, $field)
{
    global $DB;

    $fields = getTableColumns('freight_services_bank');
    foreach ($fields as $key => $value) {
        $fields[$key] = 'fw.`' . $value . '`';
    }

    $fields = array_merge($fields, forwardersAccountsGetOptionList('ext_fields'));

    if (array_key_exists($field, $fields)) {
        $row = $DB->query_first("SELECT " . $fields[$field] . " FROM freight_services_bank fw WHERE fw.id=" . intval($account_id), MYSQLI_NUM);
        return $row[0];
    }
    return null;
}

function forwardersAccountsSearch($criteria = array())
{
    global $DB;
    $results = array();

    $sql   = " SELECT fw.id FROM freight_services_bank fw";
    $joins = array();

    $fields = getTableColumns('freight_services_bank');
    foreach ($fields as $key => $value) {
        $fields[$key] = 'fw.`' . $value . '`';
    }

    $fields = array_merge($fields, forwardersAccountsGetOptionList('ext_fields'));

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
                $whereAND[] = "	fw.product_id=" . intval($criteria);
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
        $searchBits = array('fw.product_id', 'fw.name', 'fw.address', 'fw.city', 'fw.post_code', 'fw.state', 'fw.country', 'fw.phone', 'fw.fax', 'fw.mobile', 'fw.email', 'fw.website');
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

    $sql .= " ORDER BY fw.id ASC";

    $query = $DB->query($sql);
    while ($row = $DB->fetch_array($query, MYSQLI_ASSOC)) {
        $results[] = $row['id'];
    }

    $DB->free_result($query);
    return $results;
}

function forwardersAccountsToDatabase($forwarder_id, $accounts)
{
    global $DB;
    $forwarder_id = intval($forwarder_id);
    if (0 == $forwarder_id) {
        return;
    }

    $sql = "DELETE FROM freight_services_bank WHERE product_id=" . $forwarder_id;
    $DB->query($sql);

    $fields = array();
    foreach (getTableFields("`freight_services_bank`") as $k => $v) {
        if ("auto_increment" != $v[5]) {
            $fields[$k] = $v[4];
        }
    }

    /*
    $query=$DB->query("SHOW COLUMNS FROM freight_services_bank");
    while($row=$DB->fetch_array($query))
    if($row[5]!='auto_increment')
    $fields[$row[0]]=$row[4];
     */

    foreach ($accounts as $account) {
        $insert_vals           = array();
        $account['product_id'] = $forwarder_id;
        foreach ($account as $key => $value) {
            if (isset($fields[$key])) {
                $insert_vals[$key] = addslashes($value);
            }
        }

        $sql = "INSERT INTO freight_services_bank(`" . implode('`, `', array_keys($insert_vals)) . "`) VALUES('" . implode("', '", $insert_vals) . "')";
        $DB->query($sql);
    }
}
