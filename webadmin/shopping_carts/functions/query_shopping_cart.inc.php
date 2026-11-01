<?php
function shopping_cartChunk($shopping_cart_list, $group_by = '')
{
    global $DB;
    if (!is_array($shopping_cart_list) || count($shopping_cart_list) < 1) {
        return $shopping_cart_list;
    }

    if (!is_array($group_by)) {
        $group_by = array($group_by);
    }

    $fields     = $DB->table_columns("`highwaygroup`.`shopping_carts`");
    $field_list = array('shopping_cart_id');
    foreach ($group_by as $field) {
        if (array_key_exists($field, $fields)) {
            $field_list[] = $field;
        }
    }

    $field_list = array_unique($field_list);
    $sql        = "SELECT " . implode(', ', $field_list) . " FROM `highwaygroup`.`shopping_carts` AS `n` WHERE `n`.`shopping_cart_id` IN(" . implode(', ', $shopping_cart_list) . ")";
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
        $pointer[] = $row['shopping_cart_id'];
    }
    $DB->free_result($query);
    return $results;
}
function shopping_cartDelete($shopping_cart_id)
{
    global $DB;
    $DB->query("DELETE FROM `highwaygroup`.`shopping_carts` WHERE `shopping_cart_id` = " . intval($shopping_cart_id));
    $DB->query("DELETE FROM `highwaygroup`.`shopping_cart_products` WHERE `shopping_cart_id` = " . intval($shopping_cart_id));
    return true;
}
function shopping_cartExists($shopping_cart_id)
{
    global $DB;
    $row = $DB->query_first("SELECT `shopping_cart_id` FROM `highwaygroup`.`shopping_carts` WHERE `shopping_cart_id` = " . intval($shopping_cart_id));
    if ($row) {
        return true;
    }

    return false;
}
function shopping_cartFromDatabase($shopping_cart_id)
{
    global $DB;
    $summary = array();
    if (shopping_cartExists($shopping_cart_id)) {
        $shopping_cart_id = intval($shopping_cart_id);
        $summary          = $DB->query_first("SELECT `n`.*, UNIX_TIMESTAMP(`n`.`date_added`) AS `time_added`, UNIX_TIMESTAMP(`n`.`last_updated`) AS `time_updated`, UNIX_TIMESTAMP(`n`.`date_ordered`) AS `time_ordered`, UNIX_TIMESTAMP(`n`.`payment_date`) AS `payment_time`, UNIX_TIMESTAMP(`n`.`rejected_date`) AS `rejected_time` FROM `highwaygroup`.`shopping_carts` AS `n` WHERE `n`.`shopping_cart_id` = " . $shopping_cart_id . " LIMIT 1", MYSQLI_ASSOC);
    } else {
        $summary = $DB->table_defaults("`highwaygroup`.`shopping_carts`");
    }
    return $summary;
}
function shopping_cartGetLock($shopping_cart_id)
{
    return false;
}
function shopping_cartGetValue($shopping_cart_id, $field)
{
    global $DB;
    $fields = $DB->table_columns("`highwaygroup`.`shopping_carts`");
    if (array_key_exists($field, $fields)) {
        $row = $DB->query_first("SELECT " . $field . " FROM `highwaygroup`.`shopping_carts` WHERE `shopping_cart_id` = " . intval($shopping_cart_id));
        return $row[$field];
    }
    return null;
}
function shopping_cartHasValue($shopping_cart_id, $field, $value)
{
    return (shopping_cartGetValue($shopping_cart_id, $field) == $value);
}
function shopping_cartProductsFromDatabase($shopping_cart_id)
{
    global $DB;
    $shopping_cart_id = intval($shopping_cart_id);
    $sql              = "SELECT `ip`.* FROM `highwaygroup`.`shopping_cart_products` AS `ip` WHERE `ip`.`shopping_cart_id` = " . $shopping_cart_id;
    $query            = $DB->query($sql);
    $result           = array();
    while ($row = $DB->fetch_assoc($query)) {
        $result[] = $row;
    }

    $DB->free_result($query);
    return $result;
}
function shopping_cartProductsToDatabase($shopping_cart_id, $shopping_cart_products)
{
    global $DB;
    $shopping_cart_id = intval($shopping_cart_id);
    if (0 == $shopping_cart_id) {
        return;
    }

    $sql = "DELETE FROM `highwaygroup`.`shopping_cart_products` WHERE `shopping_cart_id` = " . $shopping_cart_id;
    $DB->query($sql);

    $fields = $DB->table_defaults("`highwaygroup`.`shopping_cart_products`");

    foreach ($shopping_cart_products as $product) {
        $insert_vals = array();
        if ($shopping_cart_id > 0) {
            $product['shopping_cart_id'] = $shopping_cart_id;
        }

        foreach ($product as $key => $value) {
            if (isset($fields[$key])) {
                $insert_vals[$key] = addslashes($value);
            }
        }

        $sql = "INSERT INTO `highwaygroup`.`shopping_cart_products` (`" . implode('`, `', array_keys($insert_vals)) . "`) VALUES('" . implode("', '", $insert_vals) . "')";
        $DB->query($sql);
    }
}
function shopping_cartSearch($criteria = '')
{
    global $DB, $_GLOBALS;
    $results = array();
    $sql     = " SELECT `i`.`shopping_cart_id` FROM `highwaygroup`.`shopping_carts` AS `i`";
    $joins   = array();
    $fields  = $DB->table_columns("`highwaygroup`.`shopping_carts`");
    if (!in_array($_GLOBALS["site_settings"]["site_id"], array(11, 43))) {
        if (is_array($criteria)) {
            $criteria['site_id'] = $_GLOBALS['site_settings']['site_id'];
        } else {
            $criteria = array('any_text' => $criteria, 'site_id' => $_GLOBALS['site_settings']['site_id']);
        }
    }
    $whereAND = array();
    if (is_array($criteria)) {
        foreach ($criteria as $field => $value) {
            if (isset($fields[$field])) {
                if (is_array($value)) {
                    if (count($value) > 0) {
                        $whereOR = array();
                        foreach ($value as $sub_value) {
                            $whereOR[] = "	`i`.`" . $field . "`=" . $DB->escape($sub_value);
                        }

                        $whereAND[] = "	(" . implode(" OR ", $whereOR) . ")";
                    } else {
                        return array();
                    }
                } else {
                    $whereAND[] = "	`i`.`" . $field . "` = " . $DB->escape($value);
                }

                unset($fields[$field]);
            } elseif (is_string($criteria) && '' != $criteria) {
                $criteria = array('any_text' => $criteria);
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
        $searchBits = array('`i`.`shopping_cart_id`', '`i`.`member_id`', '`i`.`visitor_id`', '`i`.`status`');
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
    if (count($whereAND) > 0) {
        $sql .= " WHERE " . implode(" AND ", $whereAND);
    }

    $sql .= " ORDER BY `i`.`shopping_cart_id` ASC";
    $query = $DB->query($sql);
    while ($row = $DB->fetch_array($query, MYSQLI_ASSOC)) {
        $results[] = $row['shopping_cart_id'];
    }

    $DB->free_result($query);
    return $results;
}
function shopping_cartSetLock($shopping_cart_id, $staff_id = '', $timeout = 0)
{
    return;
}
function shopping_cartSetValue($shopping_cart_id, $field, $value)
{
    global $DB;
    if (shopping_cartExists($shopping_cart_id)) {
        $fields = $DB->table_columns("`highwaygroup`.`shopping_carts`");
        if (array_key_exists($field, $fields)) {
            $DB->query("UPDATE `highwaygroup`.`shopping_carts` SET `" . $field . "` = " . $DB->escape($value) . " WHERE `shopping_cart_id` = " . intval($shopping_cart_id) . " LIMIT 1");
            return true;
        }
    }
    return false;
}
function shopping_cartSort($shopping_cart_list, $sort_by)
{
    global $DB;
    if (!is_array($shopping_cart_list) || count($shopping_cart_list) < 2) {
        return $shopping_cart_list;
    }

    $fields    = $DB->table_columns("`highwaygroup`.`shopping_carts`");
    $temp_list = array();
    if (isset($fields[$sort_by])) {
        $query = $DB->query("SELECT `n`.`shopping_cart_id` FROM `highwaygroup`.`shopping_carts` AS `n` WHERE `n`.`shopping_cart_id` IN(" . implode(', ', $shopping_cart_list) . ") ORDER BY `" . $sort_by . "` ASC");
        while ($row = $DB->fetch_assoc($query)) {
            $temp_list[] = $row['shopping_cart_id'];
        }

        $DB->free_result($query);
    }
    if (count($temp_list) > 0) {
        $shopping_cart_list = $temp_list;
    }

    return $shopping_cart_list;
}
function shopping_cartSumValue($shopping_cart_list, $field)
{
    global $DB;
    $fields = $DB->table_columns("`highwaygroup`.`shopping_carts`");
    if (array_key_exists($field, $fields)) {
        $row = $DB->query_first("SELECT SUM(`" . $field . "`) AS `sum_value` FROM `highwaygroup`.`shopping_carts` WHERE `shopping_cart_id` IN(" . implode(', ', $shopping_cart_list) . ")");
        return $row['sum_value'];
    }
    return null;
}
function shopping_cartToDatabase($summary)
{
    global $DB, $_GLOBALS;
    $shopping_cart_id = intval($summary['shopping_cart_id']);
    if (0 == $summary['site_id']) {
        $summary['site_id'] = $_GLOBALS['site_settings']['site_id'];
    }

    $safety            = $summary;
    $old_shopping_cart = shopping_cartFromDatabase($shopping_cart_id);
    foreach ($old_shopping_cart as $key => $value) {
        if ($summary[$key] == $value) {
            unset($summary[$key]);
        }
    }

    foreach ($summary as $key => $value) {
        if (!array_key_exists($key, $old_shopping_cart)) {
            unset($summary[$key]);
        }
    }

    $fields = $DB->table_columns("`highwaygroup`.`shopping_carts`");
    if (count($summary) > 0) {
        if (shopping_cartExists($shopping_cart_id)) {
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

            $sql = "UPDATE `highwaygroup`.`shopping_carts` SET " . implode(', ', $set_vals) . " WHERE `shopping_cart_id` = " . $shopping_cart_id . " LIMIT 1";
            if (count($set_vals) > 0) {
                $DB->query($sql);
            }
        } else {
            $summary['create_date'] = formatDateTime();
            $set_vals               = array();
            foreach ($summary as $key => $value) {
                if (array_key_exists($key, $fields)) {
                    if (isset($summary[$key])) {
                        $set_vals[$key] = $DB->escape($value);
                    } else {
                        $set_vals[$key] = "NULL";
                    }
                }
            }

            $sql = "INSERT INTO `highwaygroup`.`shopping_carts` (" . implode(', ', array_keys($set_vals)) . ") VALUES (" . implode(', ', array_values($set_vals)) . ")";
            if (count($set_vals) > 0) {
                $DB->query($sql);
            }

            $shopping_cart_id = $DB->insert_id();
        }
    }
    return $shopping_cart_id;
}
