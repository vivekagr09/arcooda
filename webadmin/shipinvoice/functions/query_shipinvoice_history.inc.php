<?php
if (!defined('SHIPINVOICE_HISTORY_TABLENAME')) {
    define('SHIPINVOICE_HISTORY_TABLENAME', '`highwaygroup`.`shipinvoice_messages`');
}
if (!defined('SHIPINVOICE_HISTORY_PRIMARYKEY')) {
    define('SHIPINVOICE_HISTORY_PRIMARYKEY', 'id');
}
if (!defined('SHIPINVOICE_HISTORY_SECONDARYKEY')) {
    define('SHIPINVOICE_HISTORY_SECONDARYKEY', 'sinvoice_id');
}

if (!function_exists('shipinvoiceHistoryDefault')) {
    /**
     * @return mixed[]
     */
    function shipinvoiceHistoryDefault()
    {
        global $DB;

        return shipinvoiceHistoryVirtual($DB->modelDefault(
            SHIPINVOICE_HISTORY_TABLENAME,
            SHIPINVOICE_HISTORY_PRIMARYKEY
        ));
    }
}
if (!function_exists('shipinvoiceHistoryDelete')) {
    /**
     * @param int $id
     *
     * @return int
     */
    function shipinvoiceHistoryDelete($id)
    {
        global $DB;

        return $DB->modelDelete(
            $id,
            SHIPINVOICE_HISTORY_TABLENAME,
            SHIPINVOICE_HISTORY_PRIMARYKEY
        );
    }
}
if (!function_exists('shipinvoiceHistoryExists')) {
    /**
     * @param int $id
     *
     * @return bool
     */
    function shipinvoiceHistoryExists($id)
    {
        global $DB;

        return $DB->modelExists(
            $id,
            SHIPINVOICE_HISTORY_TABLENAME,
            SHIPINVOICE_HISTORY_PRIMARYKEY
        );
    }
}
if (!function_exists('shipinvoiceHistoryFromDatabase')) {
    /**
     * @param int $id
     *
     * @return mixed[]|null
     */
    /*
    function shipinvoiceHistoryFromDatabase($id)
    {
        global $DB;

        if ($row = $DB->modelLoad(
            $id,
            SHIPINVOICE_HISTORY_TABLENAME,
            SHIPINVOICE_HISTORY_PRIMARYKEY
        )) {
            return shipinvoiceHistoryVirtual($row);
        }

        return null;
    }
    */
}
if (!function_exists('shipinvoiceHistoryGetValue')) {
    /**
     * @param int    $id
     * @param string $field
     *
     * @return mixed
     */
    function shipinvoiceHistoryGetValue($id, $field)
    {
        global $DB;

        if (array_key_exists($field, $DB->table_columns(
            SHIPINVOICE_HISTORY_TABLENAME
        ))) {
            return $DB->modelGet(
                $id,
                $field,
                SHIPINVOICE_HISTORY_TABLENAME,
                SHIPINVOICE_HISTORY_PRIMARYKEY
            );
        }

        if ($model = shipinvoiceHistoryFromDatabase($id)) {
            return (array_key_exists($field, $model)) ? $model[$field] : null;
        }

        return null;
    }
}
if (!function_exists('shipinvoiceHistoryHasValue')) {
    /**
     * @param int    $id
     * @param string $field
     * @param mixed  $value
     *
     * @return bool
     */
    function shipinvoiceHistoryHasValue($id, $field, $value)
    {
        return shipinvoiceHistoryGetValue($id, $field) == $value;
    }
}
if (!function_exists('shipinvoiceHistoryQuery')) {
    /**
     * @todo
     *
     * @param mixed[] $eq
     * @param mixed[] $lt
     * @param mixed[] $gt
     *
     * @return string
     */
    function shipinvoiceHistoryQuery($eq = array(), $lt = array(), $gt = array())
    {
        global $DB;

        $whereAND = array();

        return (count($whereAND)) ? implode(" AND ", $whereAND) : "1";
    }
}
if (!function_exists('shipinvoiceHistorySearch')) {
    /**
     * @param string|null $where
     * @param string|null $order_by
     *
     * @return int[]
     */
    /*
    function shipinvoiceHistorySearch($where = null, $order_by = null)
    {
        global $DB;

        // Legacy accepts criteria as first parameter
        if (is_array($where)) {
            $where = shipinvoiceHistoryQuery($where);
        }

        return $DB->query_list(sprintf(
            "SELECT `%s` FROM %s%s%s",
            SHIPINVOICE_HISTORY_PRIMARYKEY,
            SHIPINVOICE_HISTORY_TABLENAME,
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
    */
}
if (!function_exists('shipinvoiceHistorySetValue')) {
    /**
     * @param int    $id
     * @param string $field
     * @param mixed  $value
     *
     * @return int
     */
    function shipinvoiceHistorySetValue($id, $field, $value)
    {
        global $DB;

        return $DB->modelSet(
            $id,
            $field,
            $value,
            SHIPINVOICE_HISTORY_TABLENAME,
            SHIPINVOICE_HISTORY_PRIMARYKEY
        );
    }
}
if (!function_exists('shipinvoiceHistorySort')) {
    /**
     * @param string|null $where
     * @param string|null $order_by
     *
     * @return int[]
     */
    function shipinvoiceHistorySort($where = null, $order_by = null)
    {
        // Legacy accepts list of id's as first parameter
        if (is_array($where)) {
            $where = shipinvoiceHistoryQuery(array(
                SHIPINVOICE_HISTORY_PRIMARYKEY => $where,
            ));
        }

        return shipinvoiceHistorySearch($where, $order_by);
    }
}
if (!function_exists('shipinvoiceHistoryToDatabase')) {
    /**
     * @param mixed[] $item
     *
     * @return int
     */
    /*
    function shipinvoiceHistoryToDatabase(&$item)
    {
        global $DB;

        return $DB->modelSave(
            $item,
            SHIPINVOICE_HISTORY_TABLENAME,
            SHIPINVOICE_HISTORY_PRIMARYKEY
        );
    }
    */
}
if (!function_exists('shipinvoiceHistoryVirtual')) {
    /**
     * @param mixed[] $row
     *
     * @return mixed[]
     */
    function shipinvoiceHistoryVirtual($row)
    {
        return $row;
    }
}

if (!function_exists('shipinvoiceHistoriesDelete')) {
    /**
     * @param int $id
     *
     * @return int
     */
    function shipinvoiceHistoriesDelete($id)
    {
        global $DB;

        return $DB->childrenDelete(
            $id,
            SHIPINVOICE_HISTORY_TABLENAME,
            SHIPINVOICE_HISTORY_SECONDARYKEY
        );
    }
}
if (!function_exists('shipinvoiceHistoriesFromDatabase')) {
    /**
     * @param int $id
     *
     * @return mixed[]|null
     */
    function shipinvoiceHistoriesFromDatabase($id)
    {
        global $DB;

        $result = array();
        foreach ($DB->childrenLoad(
            $id,
            SHIPINVOICE_HISTORY_TABLENAME,
            SHIPINVOICE_HISTORY_PRIMARYKEY,
            SHIPINVOICE_HISTORY_SECONDARYKEY
        ) as $k => $v) {
            $result[$k] = shipinvoiceHistoryVirtual($v);
        }

        return $result;
    }
}
if (!function_exists('shipinvoiceHistoriesToDatabase')) {
    /**
     * @param int       $id
     * @param mixed[][] $items
     *
     * @return void
     */
    function shipinvoiceHistoriesToDatabase($id, &$items)
    {
        global $DB;

        $DB->childrenSave(
            $id,
            $items,
            SHIPINVOICE_HISTORY_TABLENAME,
            SHIPINVOICE_HISTORY_PRIMARYKEY,
            SHIPINVOICE_HISTORY_SECONDARYKEY
        );
    }
}

if (!function_exists("shipinvoiceHistoryFromDatabase")) {
    function shipinvoiceHistoryFromDatabase($message_id)
    {
        global $DB;
        return $DB->query_first("SELECT `sm`.*, UNIX_TIMESTAMP(`sm`.`message_date`) AS `message_time` FROM `highwaygroup`.`shipinvoice_messages` AS `sm` WHERE `sm`.`message_id` = " . intval($message_id) . " LIMIT 1", MYSQLI_ASSOC);
    }
}
if (!function_exists("shipinvoiceHistorySearch")) {
    function shipinvoiceHistorySearch($criteria)
    {
        global $DB;
        $results = array();
        $sql     = " SELECT sm.message_id FROM `highwaygroup`.`shipinvoice_messages` sm";
        $joins   = array();
        $fields  = array();
        $query   = $DB->query("SHOW COLUMNS FROM `highwaygroup`.`shipinvoice_messages`");
        while ($row = $DB->fetch_array($query, MYSQLI_NUM)) {
            $fields[$row[0]] = $row;
        }

        $DB->free_result($query);
        $fields2 = array();
        $query   = $DB->query("SHOW COLUMNS FROM `highwaygroup`.`shipinvoices`");
        while ($row = $DB->fetch_array($query, MYSQLI_NUM)) {
            if (!array_key_exists($row[0], $fields)) {
                $fields2[$row[0]] = $row;
            }
        }

        $DB->free_result($query);
        $whereAND = array();
        if (is_array($criteria)) {
            foreach ($criteria as $field => $value) {
                if (array_key_exists($field, $fields)) {
                    if (is_array($value)) {
                        if (count($value) > 0) {
                            $whereOR = array();
                            foreach ($value as $sub_value) {
                                $whereOR[] = "	sm.`" . $field . "`=" . formatSQLString($sub_value);
                            }

                            $whereAND[] = "	(" . implode(" OR ", $whereOR) . ")";
                        } else {
                            return array();
                        }
                    } else {
                        $whereAND[] = "	sm.`" . $field . "`=" . formatSQLString($value);
                    }

                    unset($fields[$field]);
                }
                if (array_key_exists($field, $fields2)) {
                    $joins['sinvoice'] = " INNER JOIN `highwaygroup`.`shipinvoices` s ON s.sinvoice_id=sm.sinvoice_id";
                    if (is_array($value)) {
                        if (count($value) > 0) {
                            $whereOR = array();
                            foreach ($value as $sub_value) {
                                $whereOR[] = "	s.`" . $field . "`=" . formatSQLString($sub_value);
                            }

                            $whereAND[] = "	(" . implode(" OR ", $whereOR) . ")";
                        } else {
                            return array();
                        }
                    } else {
                        $whereAND[] = "	s.`" . $field . "`=" . formatSQLString($value);
                    }

                    unset($fields2[$field]);
                }
            }
        } elseif (is_string($criteria) && '' != $criteria) {
            $whereAND[] = "	sm.sinvoice_id=" . formatSQLString($criteria);
        }

        $search_for = array();
        if (is_array($criteria['any_text'])) {
            $search_for = $criteria['any_text'];
        }

        if (is_string($criteria['any_text'])) {
            $search_for = explode(' ', $criteria['any_text']);
        }

        if (count($search_for) > 0) {
            $searchBits = array('sm.message_notes', 'sm.message_title', 'sm.message_sender', 'sm.message_sentto');
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

        $sql .= " ORDER BY sm.message_id DESC";
        $query = $DB->query($sql);
        while ($row = $DB->fetch_array($query, MYSQLI_ASSOC)) {
            $results[] = $row['message_id'];
        }

        $DB->free_result($query);
        return $results;
    }
}
if (!function_exists("shipinvoiceHistoryToDatabase")) {
    function shipinvoiceHistoryToDatabase($sinvoice_id, $history_info)
    {
        global $DB;
        $DB->query(" INSERT INTO `highwaygroup`.`shipinvoice_messages` SET" . "	sinvoice_id=" . intval($sinvoice_id) . ", `message_date` = NOW()," . "	message_sender=" . formatSQLString($history_info['message_sender']) . "," . "	message_type=" . intval($history_info['message_type']) . "," . "	message_notes=" . formatSQLString($history_info['message_notes']));
    }
}
