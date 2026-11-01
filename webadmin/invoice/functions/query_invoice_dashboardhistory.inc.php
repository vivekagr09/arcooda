<?php
if (!defined('INVOICE_DASHBOARD_HISTORY_TABLENAME')) {
    define('INVOICE_DASHBOARD_HISTORY_TABLENAME', '`highwaygroup`.`invoice_dashboard_history`');
}
if (!defined('INVOICE_DASHBOARD_HISTORY_PRIMARYKEY')) {
    define('INVOICE_DASHBOARD_HISTORY_PRIMARYKEY', 'id');
}
if (!defined('INVOICE_DASHBOARD_HISTORY_SECONDARYKEY')) {
    define('INVOICE_DASHBOARD_HISTORY_SECONDARYKEY', 'invoice_id');
}

if (!function_exists('invoiceDashboardHistoryGenericToDatabase')) {
    function invoiceDashboardHistoryGenericToDatabase(&$item, $table, $primary_key = 'id') {
        global $DB, $_GLOBALS;
        $id         = intval($item[$primary_key]);
        $fields     = $DB->table_columns($table);
        $old_vals   = $DB->query_first(sprintf(
            "SELECT * FROM %s WHERE `%s` = %d LIMIT 1",
            $table,
            $primary_key,
            $id
        ), MYSQLI_ASSOC);
        if ($old_vals) {
            $set_vals = array();
            foreach ($fields as $k => $v) {
                if (array_key_exists($k, $item) && $item[$k] !=$old_vals[$k]) {
                    if (is_array($item[$k])) {
                        $set_vals[$k] = "`" . $k . "` = " . $DB->escape(implode(",", $item[$k]));
                    } elseif (isset($item[$k])) {
                        $set_vals[$k] = "`" . $k . "` = " . $DB->escape($item[$k]);
                    } else {
                        $set_vals[$k] = "`" . $k . "` = NULL";
                    }
                }
            }
            if (count($set_vals)) {
                if($table=='`supplier_product`'){
                   $set_vals['updated_at'] = "`updated_at` = NOW()";
                }

                $DB->query(sprintf(
                    "UPDATE %s SET %s WHERE `%s` = %d LIMIT 1",
                    $table,
                    implode(', ', $set_vals),
                    $primary_key,
                    $id
                ));
            }
        } else {                    
            $insert_vals = array();
            foreach ($fields as $k => $v) {
                if (array_key_exists($k, $item)) {
                    if (is_array($item[$k])) {
                        $insert_vals[$k] = $DB->escape(implode(",", $item[$k]));
                    } elseif (isset($item[$k])) {
                        $insert_vals[$k] = $DB->escape($item[$k]);
                    } else {
                        $insert_vals[$k] = "NULL";
                    }
                }
            }
            if (count($insert_vals)) {
                $DB->query(sprintf(
                    "INSERT INTO %s (`%s`) VALUES (%s)",
                    $table,
                    implode('`, `', array_keys($insert_vals)),
                    implode(", ", $insert_vals)
                ));

                $id = $DB->insert_id();


            }
        }
        return $id;
    }
}
if (!function_exists('invoiceDashboardHistoryFromPost')) {
    function invoiceDashboardHistoryFromPost($posted)
    {
        global $DB;
        if (!is_array($posted)) {
            return array();
        }
        $result = array();
        $fields = $DB->table_columns(INVOICE_DASHBOARD_HISTORY_TABLENAME);
        foreach ($posted as $k1 => $v1) {
            foreach ($fields as $k2 => $v2) {
                if ($k2 == $k1) {
                   $result[$k1] = $v1;
                }
            }
        }
        return $result;
    }
}
if (!function_exists('invoiceDashboardHistoryDelete')) {
    /**
     * @param int $id
     *
     * @return int
     */
    function invoiceDashboardHistoryDelete($id)
    {
        global $DB;

        return $DB->modelDelete(
            $id,
            INVOICE_DASHBOARD_HISTORY_TABLENAME,
            INVOICE_DASHBOARD_HISTORY_PRIMARYKEY
        );
    }
}
if (!function_exists('invoiceDashboardHistoryExists')) {
    /**
     * @param int $id
     *
     * @return bool
     */
    function invoiceDashboardHistoryExists($id)
    {
        global $DB;

        return $DB->modelExists(
            $id,
            INVOICE_DASHBOARD_HISTORY_TABLENAME,
            INVOICE_DASHBOARD_HISTORY_PRIMARYKEY
        );
    }
}
if (!function_exists('invoiceDashboardHistoryGetValue')) {
    /**
     * @param int    $id
     * @param string $field
     *
     * @return mixed
     */
    function invoiceDashboardHistoryGetValue($id, $field)
    {
        global $DB;

        if (array_key_exists($field, $DB->table_columns(
            INVOICE_DASHBOARD_HISTORY_TABLENAME
        ))) {
            return $DB->modelGet(
                $id,
                $field,
                INVOICE_DASHBOARD_HISTORY_TABLENAME,
                INVOICE_DASHBOARD_HISTORY_PRIMARYKEY
            );
        }

        if ($model = invoiceDashboardHistoryFromDatabase($id)) {
            return (array_key_exists($field, $model)) ? $model[$field] : null;
        }

        return null;
    }
}
if (!function_exists('invoiceDashboardHistoryHasValue')) {
    /**
     * @param int    $id
     * @param string $field
     * @param mixed  $value
     *
     * @return bool
     */
    function invoiceDashboardHistoryHasValue($id, $field, $value)
    {
        return invoiceDashboardHistoryGetValue($id, $field) == $value;
    }
}

if (!function_exists('invoiceDashboardHistorySetValue')) {
    /**
     * @param int    $id
     * @param string $field
     * @param mixed  $value
     *
     * @return int
     */
    function invoiceDashboardHistorySetValue($id, $field, $value)
    {
        global $DB;

        return $DB->modelSet(
            $id,
            $field,
            $value,
            INVOICE_DASHBOARD_HISTORY_TABLENAME,
            INVOICE_DASHBOARD_HISTORY_PRIMARYKEY
        );
    }
}
if (!function_exists('invoiceDashboardHistorySort')) {
    /**
     * @param string|null $where
     * @param string|null $order_by
     *
     * @return int[]
     */
    function invoiceDashboardHistorySort($where = null, $order_by = null)
    {
        // Legacy accepts list of id's as first parameter
        if (is_array($where)) {
            $where = invoiceDashboardHistoryQuery(array(
                INVOICE_DASHBOARD_HISTORY_PRIMARYKEY => $where,
            ));
        }

        return invoiceDashboardHistorySearch($where, $order_by);
    }
}

if (!function_exists("invoiceDashboardHistoryFromDatabase")) {
    /**
     * @param int $id
     *
     * @return mixed[]|false
     */
    function invoiceDashboardHistoryFromDatabase($id)
    {
        global $DB;
        return $DB->query_first(sprintf(
            "SELECT * FROM %s AS `im` WHERE `im`.`%s` = %d LIMIT 1",
            INVOICE_DASHBOARD_HISTORY_TABLENAME,
            INVOICE_DASHBOARD_HISTORY_PRIMARYKEY,
            $id
        ), MYSQLI_ASSOC);
    }
}
if (!function_exists('invoiceDashboardHistoryQuery')) {
    /**
     * @param mixed[] $eq
     * @param mixed[] $lt
     * @param mixed[] $gt
     *
     * @return string
     */
    function invoiceDashboardHistoryQuery($eq = array(), $lt = array(), $gt = array())
    {
        global $DB;

        if (empty($eq) && empty($lt) && empty($gt)) {
            return '1';
        }

        $field_types = getTableFields(INVOICE_DASHBOARD_HISTORY_TABLENAME);

        $whereAND = array();
        foreach (array(
            '=' => (is_null($eq)) ? array() : $eq,
            '<' => (is_null($lt)) ? array() : $lt,
            '>' => (is_null($gt)) ? array() : $gt,
        ) as $op => $criteria) {
            foreach ($criteria as $field => $value) {
                if (array_key_exists($field, $field_types)) {
                    $whereOR = array();
                    foreach ((array)$value as $sub_value) {
                        $whereOR[] = sprintf(
                            "`%s` %s %s",
                            $field,
                            $op,
                            $DB->escape($sub_value)
                        );
                    }
                    if (1 == count($whereOR)) {
                        $whereAND[] = implode("", $whereOR);
                    } elseif (count($whereOR)) {
                        $whereAND[] = sprintf("(%s)", implode(" OR ", $whereOR));
                    }
                }
            }
        }

        return (count($whereAND)) ? implode(" AND ", $whereAND) : "1";
    }
}
if (!function_exists('invoiceDashboardHistorySearch')) {
    /**
     * @param string|null $where
     * @param string|null $order_by
     *
     * @return int[]
     */
    function invoiceDashboardHistorySearch($where = null, $order_by = null)
    {
        global $DB;

        // Legacy accepts criteria as first parameter
        if (is_array($where)) {
            $where = invoiceDashboardHistoryQuery($where);
        }

        return $DB->query_list(sprintf(
            "SELECT `%s` FROM %s%s%s",
            INVOICE_DASHBOARD_HISTORY_PRIMARYKEY,
            INVOICE_DASHBOARD_HISTORY_TABLENAME,
            (!is_null($where)) ? sprintf(
                " WHERE %s",
                $where
            ) : "",
            (!is_null($order_by)) ? sprintf(
                " ORDER BY `%s` DESC",
                $order_by
            ) : ""
        ));
    }
}
if (!function_exists("invoiceDashboardHistoryToDatabase")) {
    /**
     * @param int     $id
     * @param mixed[] $history_info
     *
     * @return void
     */
    function invoiceDashboardHistoryToDatabase($history_info)
    {
        global $DB;
        foreach (array('invoice_id', 'display_to_customer', 'staff_id', 'created_at') as $k) {
            if (!array_key_exists($k, $history_info)) {
                $history_info[$k] = '';
            }
        }

        $DB->query(sprintf(
            "INSERT INTO %s (`invoice_id`, `display_to_customer`, `staff_id`, `created_at`) VALUES (%d, %s, %d, NOW())",
            INVOICE_DASHBOARD_HISTORY_TABLENAME,
            $history_info["invoice_id"],
            formatSQLString($history_info["display_to_customer"]),
            $history_info["staff_id"],
        ));
    }
}
if (!function_exists("invoiceDashboardHistoryNotesFromDatabase")) {
    /**
     * @param int     $id
     * @param mixed[] $history_info
     *
     * @return void
     */
    function invoiceDashboardHistoryNotesFromDatabase($invoice_id)
    {
        global $DB;
        $result = array();
        $query = $DB->query(sprintf(
            "SELECT * FROM %s WHERE  `%s` = %d  ORDER BY `id` DESC",
            INVOICE_DASHBOARD_HISTORY_TABLENAME,
            INVOICE_DASHBOARD_HISTORY_SECONDARYKEY,
            $invoice_id,
        ));
        while ($row = $DB->fetch_assoc($query)) {
            $result[] = [
                'message'   => $row['display_to_customer'],
                'date'      => strtotime($row['created_at']),
            ];
        }
        $DB->free_result($query);
        return $result;
    }
}
