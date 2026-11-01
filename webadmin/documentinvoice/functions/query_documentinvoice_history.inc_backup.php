<?php
if (!defined('documentinvoice_HISTORY_TABLENAME')) {
    define('documentinvoice_HISTORY_TABLENAME', '`highwaygroup`.`documentinvoice_messages`');
}
if (!defined('documentinvoice_HISTORY_PRIMARYKEY')) {
    define('documentinvoice_HISTORY_PRIMARYKEY', 'id');
}
if (!defined('documentinvoice_HISTORY_SECONDARYKEY')) {
    define('documentinvoice_HISTORY_SECONDARYKEY', 'dinvoice_id');
}

if (!function_exists('documentinvoiceHistoryDefault')) {
    /**
     * @return mixed[]
     */
    function documentinvoiceHistoryDefault()
    {
        global $DB;

        return documentinvoiceHistoryVirtual($DB->modelDefault(
            documentinvoice_HISTORY_TABLENAME,
            documentinvoice_HISTORY_PRIMARYKEY
        ));
    }
}
if (!function_exists('documentinvoiceHistoryDelete')) {
    /**
     * @param int $id
     *
     * @return int
     */
    function documentinvoiceHistoryDelete($id)
    {
        global $DB;

        return $DB->modelDelete(
            $id,
            documentinvoice_HISTORY_TABLENAME,
            documentinvoice_HISTORY_PRIMARYKEY
        );
    }
}
if (!function_exists('documentinvoiceHistoryExists')) {
    /**
     * @param int $id
     *
     * @return bool
     */
    function documentinvoiceHistoryExists($id)
    {
        global $DB;

        return $DB->modelExists(
            $id,
            documentinvoice_HISTORY_TABLENAME,
            documentinvoice_HISTORY_PRIMARYKEY
        );
    }
}
if (!function_exists('documentinvoiceHistoryFromDatabase')) {
    /**
     * @param int $id
     *
     * @return mixed[]|null
     */
    /*
    function documentinvoiceHistoryFromDatabase($id)
    {
        global $DB;

        if ($row = $DB->modelLoad(
            $id,
            documentinvoice_HISTORY_TABLENAME,
            documentinvoice_HISTORY_PRIMARYKEY
        )) {
            return documentinvoiceHistoryVirtual($row);
        }

        return null;
    }
    */
}
if (!function_exists('documentinvoiceHistoryGetValue')) {
    /**
     * @param int    $id
     * @param string $field
     *
     * @return mixed
     */
    function documentinvoiceHistoryGetValue($id, $field)
    {
        global $DB;

        if (array_key_exists($field, $DB->table_columns(
            documentinvoice_HISTORY_TABLENAME
        ))) {
            return $DB->modelGet(
                $id,
                $field,
                documentinvoice_HISTORY_TABLENAME,
                documentinvoice_HISTORY_PRIMARYKEY
            );
        }

        if ($model = documentinvoiceHistoryFromDatabase($id)) {
            return (array_key_exists($field, $model)) ? $model[$field] : null;
        }

        return null;
    }
}
if (!function_exists('documentinvoiceHistoryHasValue')) {
    /**
     * @param int    $id
     * @param string $field
     * @param mixed  $value
     *
     * @return bool
     */
    function documentinvoiceHistoryHasValue($id, $field, $value)
    {
        return documentinvoiceHistoryGetValue($id, $field) == $value;
    }
}
if (!function_exists('documentinvoiceHistoryQuery')) {
    /**
     * @todo
     *
     * @param mixed[] $eq
     * @param mixed[] $lt
     * @param mixed[] $gt
     *
     * @return string
     */
    function documentinvoiceHistoryQuery($eq = array(), $lt = array(), $gt = array())
    {
        global $DB;

        $whereAND = array();

        return (count($whereAND)) ? implode(" AND ", $whereAND) : "1";
    }
}
if (!function_exists('documentinvoiceHistorySearch')) {
    /**
     * @param string|null $where
     * @param string|null $order_by
     *
     * @return int[]
     */
    /*
    function documentinvoiceHistorySearch($where = null, $order_by = null)
    {
        global $DB;

        // Legacy accepts criteria as first parameter
        if (is_array($where)) {
            $where = documentinvoiceHistoryQuery($where);
        }

        return $DB->query_list(sprintf(
            "SELECT `%s` FROM %s%s%s",
            documentinvoice_HISTORY_PRIMARYKEY,
            documentinvoice_HISTORY_TABLENAME,
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
if (!function_exists('documentinvoiceHistorySetValue')) {
    /**
     * @param int    $id
     * @param string $field
     * @param mixed  $value
     *
     * @return int
     */
    function documentinvoiceHistorySetValue($id, $field, $value)
    {
        global $DB;

        return $DB->modelSet(
            $id,
            $field,
            $value,
            documentinvoice_HISTORY_TABLENAME,
            documentinvoice_HISTORY_PRIMARYKEY
        );
    }
}
if (!function_exists('documentinvoiceHistorySort')) {
    /**
     * @param string|null $where
     * @param string|null $order_by
     *
     * @return int[]
     */
    function documentinvoiceHistorySort($where = null, $order_by = null)
    {
        // Legacy accepts list of id's as first parameter
        if (is_array($where)) {
            $where = documentinvoiceHistoryQuery(array(
                documentinvoice_HISTORY_PRIMARYKEY => $where,
            ));
        }

        return documentinvoiceHistorySearch($where, $order_by);
    }
}
if (!function_exists('documentinvoiceHistoryToDatabase')) {
    /**
     * @param mixed[] $item
     *
     * @return int
     */
    /*
    function documentinvoiceHistoryToDatabase(&$item)
    {
        global $DB;

        return $DB->modelSave(
            $item,
            documentinvoice_HISTORY_TABLENAME,
            documentinvoice_HISTORY_PRIMARYKEY
        );
    }
    */
}
if (!function_exists('documentinvoiceHistoryVirtual')) {
    /**
     * @param mixed[] $row
     *
     * @return mixed[]
     */
    function documentinvoiceHistoryVirtual($row)
    {
        return $row;
    }
}

if (!function_exists('documentinvoiceHistoriesDelete')) {
    /**
     * @param int $id
     *
     * @return int
     */
    function documentinvoiceHistoriesDelete($id)
    {
        global $DB;

        return $DB->childrenDelete(
            $id,
            documentinvoice_HISTORY_TABLENAME,
            documentinvoice_HISTORY_SECONDARYKEY
        );
    }
}
if (!function_exists('documentinvoiceHistoriesFromDatabase')) {
    /**
     * @param int $id
     *
     * @return mixed[]|null
     */
    function documentinvoiceHistoriesFromDatabase($id)
    {
        global $DB;

        $result = array();
        foreach ($DB->childrenLoad(
            $id,
            documentinvoice_HISTORY_TABLENAME,
            documentinvoice_HISTORY_PRIMARYKEY,
            documentinvoice_HISTORY_SECONDARYKEY
        ) as $k => $v) {
            $result[$k] = documentinvoiceHistoryVirtual($v);
        }

        return $result;
    }
}
if (!function_exists('documentinvoiceHistoriesToDatabase')) {
    /**
     * @param int       $id
     * @param mixed[][] $items
     *
     * @return void
     */
    function documentinvoiceHistoriesToDatabase($id, &$items)
    {
        global $DB;

        $DB->childrenSave(
            $id,
            $items,
            documentinvoice_HISTORY_TABLENAME,
            documentinvoice_HISTORY_PRIMARYKEY,
            documentinvoice_HISTORY_SECONDARYKEY
        );
    }
}

if (!function_exists("documentinvoiceHistoryFromDatabase")) {
    function documentinvoiceHistoryFromDatabase($message_id)
    {
        global $DB;
        return $DB->query_first("SELECT `sm`.*, UNIX_TIMESTAMP(`sm`.`message_date`) AS `message_time` FROM `highwaygroup`.`documentinvoice_messages` AS `sm` WHERE `sm`.`message_id` = " . intval($message_id) . " LIMIT 1", MYSQLI_ASSOC);
    }
}
if (!function_exists("documentinvoiceHistorySearch")) {
    function documentinvoiceHistorySearch($criteria)
    {
        global $DB;
        $results = array();
        $sql     = " SELECT sm.message_id FROM `highwaygroup`.`documentinvoice_messages` sm";
        $joins   = array();
        $fields  = array();
        $query   = $DB->query("SHOW COLUMNS FROM `highwaygroup`.`documentinvoice_messages`");
        while ($row = $DB->fetch_array($query, MYSQLI_NUM)) {
            $fields[$row[0]] = $row;
        }

        $DB->free_result($query);
        $fields2 = array();
        $query   = $DB->query("SHOW COLUMNS FROM `highwaygroup`.`documentinvoices`");
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
                    $joins['sinvoice'] = " INNER JOIN `highwaygroup`.`documentinvoices` s ON s.dinvoice_id=sm.dinvoice_id";
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
            $whereAND[] = "	sm.dinvoice_id=" . formatSQLString($criteria);
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
if (!function_exists("documentinvoiceHistoryToDatabase")) {
    function documentinvoiceHistoryToDatabase($dinvoice_id, $history_info)
    {
        global $DB;
        $DB->query(" INSERT INTO `highwaygroup`.`documentinvoice_messages` SET" . "	dinvoice_id=" . intval($dinvoice_id) . ", `message_date` = NOW()," . "	message_sender=" . formatSQLString($history_info['message_sender']) . "," . "	message_type=" . intval($history_info['message_type']) . "," . "	message_notes=" . formatSQLString($history_info['message_notes']));
    }
}
