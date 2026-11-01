<?php
if (!defined('INVOICE_HISTORY_TABLENAME')) {
    define('INVOICE_HISTORY_TABLENAME', '`highwaygroup`.`invoice_messages`');
}
if (!defined('INVOICE_HISTORY_PRIMARYKEY')) {
    define('INVOICE_HISTORY_PRIMARYKEY', 'message_id');
}
if (!defined('INVOICE_HISTORY_SECONDARYKEY')) {
    define('INVOICE_HISTORY_SECONDARYKEY', 'invoice_id');
}

define("INVOICE_MESSAGE_ADMIN", 4);
define("INVOICE_MESSAGE_FROM_CUSTOMER", 3);
define("INVOICE_MESSAGE_INVOICE_MODIFIED", 6);
define("INVOICE_MESSAGE_INVOICE_SENT", 12);
define("INVOICE_MESSAGE_INQUIRY_SENT", 12);
define("INVOICE_MESSAGE_ENQUIRY_SENT", 12);
define("INVOICE_MESSAGE_QUERY", 10);
define("INVOICE_MESSAGE_TO_CUSTOMER", 2);
define("INVOICE_MESSAGE_TO_STAFF", 5);

if (!function_exists('invoiceHistoryDefault')) {
    /**
     * @return mixed[]
     */
    function invoiceHistoryDefault()
    {
        global $DB;

        return invoiceHistoryVirtual($DB->modelDefault(
            INVOICE_HISTORY_TABLENAME,
            INVOICE_HISTORY_PRIMARYKEY
        ));
    }
}
if (!function_exists('invoiceHistoryDelete')) {
    /**
     * @param int $id
     *
     * @return int
     */
    function invoiceHistoryDelete($id)
    {
        global $DB;

        return $DB->modelDelete(
            $id,
            INVOICE_HISTORY_TABLENAME,
            INVOICE_HISTORY_PRIMARYKEY
        );
    }
}
if (!function_exists('invoiceHistoryExists')) {
    /**
     * @param int $id
     *
     * @return bool
     */
    function invoiceHistoryExists($id)
    {
        global $DB;

        return $DB->modelExists(
            $id,
            INVOICE_HISTORY_TABLENAME,
            INVOICE_HISTORY_PRIMARYKEY
        );
    }
}
if (!function_exists('invoiceHistoryFromDatabase')) {
    /**
     * @param int $id
     *
     * @return mixed[]|null
     */
    /*
    function invoiceHistoryFromDatabase($id)
    {
        global $DB;

        if ($row = $DB->modelLoad(
            $id,
            INVOICE_HISTORY_TABLENAME,
            INVOICE_HISTORY_PRIMARYKEY
        )) {
            return invoiceHistoryVirtual($row);
        }

        return null;
    }
    */
}
if (!function_exists('invoiceHistoryGetValue')) {
    /**
     * @param int    $id
     * @param string $field
     *
     * @return mixed
     */
    function invoiceHistoryGetValue($id, $field)
    {
        global $DB;

        if (array_key_exists($field, $DB->table_columns(
            INVOICE_HISTORY_TABLENAME
        ))) {
            return $DB->modelGet(
                $id,
                $field,
                INVOICE_HISTORY_TABLENAME,
                INVOICE_HISTORY_PRIMARYKEY
            );
        }

        if ($model = invoiceHistoryFromDatabase($id)) {
            return (array_key_exists($field, $model)) ? $model[$field] : null;
        }

        return null;
    }
}
if (!function_exists('invoiceHistoryHasValue')) {
    /**
     * @param int    $id
     * @param string $field
     * @param mixed  $value
     *
     * @return bool
     */
    function invoiceHistoryHasValue($id, $field, $value)
    {
        return invoiceHistoryGetValue($id, $field) == $value;
    }
}
if (!function_exists('invoiceHistoryQuery')) {
    /**
     * @todo
     *
     * @param mixed[] $eq
     * @param mixed[] $lt
     * @param mixed[] $gt
     *
     * @return string
     */
    function invoiceHistoryQuery($eq = array(), $lt = array(), $gt = array())
    {
        global $DB;

        $whereAND = array();

        return (count($whereAND)) ? implode(" AND ", $whereAND) : "1";
    }
}
if (!function_exists('invoiceHistorySearch')) {
    /**
     * @param string|null $where
     * @param string|null $order_by
     *
     * @return int[]
     */
    /*
    function invoiceHistorySearch($where = null, $order_by = null)
    {
        global $DB;

        // Legacy accepts criteria as first parameter
        if (is_array($where)) {
            $where = invoiceHistoryQuery($where);
        }

        return $DB->query_list(sprintf(
            "SELECT `%s` FROM %s%s%s",
            INVOICE_HISTORY_PRIMARYKEY,
            INVOICE_HISTORY_TABLENAME,
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
if (!function_exists('invoiceHistorySetValue')) {
    /**
     * @param int    $id
     * @param string $field
     * @param mixed  $value
     *
     * @return int
     */
    function invoiceHistorySetValue($id, $field, $value)
    {
        global $DB;

        return $DB->modelSet(
            $id,
            $field,
            $value,
            INVOICE_HISTORY_TABLENAME,
            INVOICE_HISTORY_PRIMARYKEY
        );
    }
}
if (!function_exists('invoiceHistorySort')) {
    /**
     * @param string|null $where
     * @param string|null $order_by
     *
     * @return int[]
     */
    function invoiceHistorySort($where = null, $order_by = null)
    {
        // Legacy accepts list of id's as first parameter
        if (is_array($where)) {
            $where = invoiceHistoryQuery(array(
                INVOICE_HISTORY_PRIMARYKEY => $where,
            ));
        }

        return invoiceHistorySearch($where, $order_by);
    }
}
if (!function_exists('invoiceHistoryToDatabase')) {
    /**
     * @param mixed[] $item
     *
     * @return int
     */
    /*
function invoiceHistoryToDatabase(&$item)
{
global $DB;

return $DB->modelSave(
$item,
INVOICE_HISTORY_TABLENAME,
INVOICE_HISTORY_PRIMARYKEY
);
}
 */
}
if (!function_exists('invoiceHistoryVirtual')) {
    /**
     * @param mixed[] $row
     *
     * @return mixed[]
     */
    function invoiceHistoryVirtual($row)
    {
        $row['message_time'] = strtotime($row['message_date']);

        /*
        $row = array_merge($row, invoiceHistoryTypeFromDatabase($row['message_type_id']));
        */

        return $row;
    }
}

if (!function_exists('invoiceHistoriesDelete')) {
    /**
     * @param int $id
     *
     * @return int
     */
    function invoiceHistoriesDelete($id)
    {
        global $DB;

        return $DB->childrenDelete(
            $id,
            INVOICE_HISTORY_TABLENAME,
            INVOICE_HISTORY_SECONDARYKEY
        );
    }
}
if (!function_exists('invoiceHistoriesFromDatabase')) {
    /**
     * @param int $id
     *
     * @return mixed[]|null
     */
    function invoiceHistoriesFromDatabase($id)
    {
        global $DB;

        $result = array();
        foreach ($DB->childrenLoad(
            $id,
            INVOICE_HISTORY_TABLENAME,
            INVOICE_HISTORY_PRIMARYKEY,
            INVOICE_HISTORY_SECONDARYKEY
        ) as $k => $v) {
            $result[$k] = invoiceHistoryVirtual($v);
        }

        return $result;
    }
}
if (!function_exists('invoiceHistoriesToDatabase')) {
    /**
     * @param int       $id
     * @param mixed[][] $items
     *
     * @return void
     */
    function invoiceHistoriesToDatabase($id, &$items)
    {
        global $DB;

        $DB->childrenSave(
            $id,
            $items,
            INVOICE_HISTORY_TABLENAME,
            INVOICE_HISTORY_PRIMARYKEY,
            INVOICE_HISTORY_SECONDARYKEY
        );
    }
}

if (!function_exists("invoiceHistoryFromDatabase")) {
    /**
     * @param int $id
     *
     * @return mixed[]|false
     */
    function invoiceHistoryFromDatabase($id)
    {
        global $DB;
        return $DB->query_first(sprintf(
            "SELECT `imt`.*, `im`.*, UNIX_TIMESTAMP(`im`.`message_date`) AS `message_time` FROM %s AS `im` INNER JOIN `highwaygroup`.`invoice_message_types` AS `imt` ON `imt`.`message_type_id` = `im`.`message_type_id` WHERE `im`.`%s` = %d LIMIT 1",
            INVOICE_HISTORY_TABLENAME,
            INVOICE_HISTORY_PRIMARYKEY,
            $id
        ), MYSQLI_ASSOC);
    }
}
if (!function_exists("invoiceHistorySearch")) {
    /**
     * @param mixed $criteria
     *
     * @return int[]
     */
    function invoiceHistorySearch($criteria)
    {
        global $DB;

        $results = array();
        $joins   = array();
        $fields  = getTableFields(INVOICE_HISTORY_TABLENAME);
        $fields2 = array();
        foreach (getTableFields("`highwaygroup`.`invoices`") as $k => $v) {
            if (!array_key_exists($k, $fields)) {
                $fields2[$k] = $v;
            }
        }

        $whereAND = array();
        if (is_array($criteria)) {
            foreach ($criteria as $field => $value) {
                if (array_key_exists($field, $fields)) {
                    if (is_array($value)) {
                        if (count($value) > 0) {
                            $whereOR = array();
                            foreach ($value as $sub_value) {
                                $whereOR[] = "`im`.`" . $field . "` = " . formatSQLString($sub_value);
                            }

                            $whereAND[] = "(" . implode(" OR ", $whereOR) . ")";
                        } else {
                            return array();
                        }
                    } else {
                        $whereAND[] = "`im`.`" . $field . "` = " . formatSQLString($value);
                    }

                    unset($fields[$field]);
                }
                if (array_key_exists($field, $fields2)) {
                    $joins['invoice'] = sprintf(
                        "INNER JOIN `highwaygroup`.`invoices` AS `i` ON `i`.`invoice_id` = `im`.`%s`",
                        INVOICE_HISTORY_SECONDARYKEY
                    );
                    if (is_array($value)) {
                        if (count($value) > 0) {
                            $whereOR = array();
                            foreach ($value as $sub_value) {
                                $whereOR[] = "`i`.`" . $field . "` = " . formatSQLString($sub_value);
                            }

                            $whereAND[] = "(" . implode(" OR ", $whereOR) . ")";
                        } else {
                            return array();
                        }
                    } else {
                        $whereAND[] = "`i`.`" . $field . "` = " . formatSQLString($value);
                    }

                    unset($fields2[$field]);
                }
            }
        } elseif (is_string($criteria) && '' != $criteria) {
            $whereAND[] = "`im`.`" . INVOICE_HISTORY_SECONDARYKEY . "` = " . formatSQLString($criteria);
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
            $searchBits = array('`im`.`message_notes`', '`im`.`message_title`', '`im`.`message_sender`', '`im`.`message_sentto`');
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

        if ($query = $DB->query(sprintf(
            "SELECT `im`.`%s` FROM %s AS `im`%s%s ORDER BY `im`.`message_id` DESC",
            INVOICE_HISTORY_PRIMARYKEY,
            INVOICE_HISTORY_TABLENAME,
            (count($joins)) ? (" " . implode(" ", $joins)) : "",
            (count($whereAND)) ? (" WHERE " . implode(" AND ", $whereAND)) : ""
        ))) {
            while ($row = $DB->fetch_assoc($query)) {
                $results[] = $row[INVOICE_HISTORY_PRIMARYKEY];
            }
            $DB->free_result($query);
        }

        return $results;
    }
}
if (!function_exists("invoiceHistoryToDatabase")) {
    /**
     * @param int     $id
     * @param mixed[] $history_info
     *
     * @return void
     */
    function invoiceHistoryToDatabase($id, $history_info)
    {
        global $DB;
        foreach (array('message_sender', 'message_sentto', 'message_title', 'message_notes', 'message_raw') as $k) {
            if (!array_key_exists($k, $history_info)) {
                $history_info[$k] = '';
            }
        }

        $DB->query(sprintf(
            "INSERT INTO %s (`%s`, `message_type_id`, `message_date`, `message_sender`, `message_sentto`, `message_title`, `message_notes`, `message_raw`) VALUES (%d, %d, NOW(), %s, %s, %s, %s, %s)",
            INVOICE_HISTORY_TABLENAME,
            INVOICE_HISTORY_SECONDARYKEY,
            $id,
            $history_info["message_type_id"],
            formatSQLString($history_info["message_sender"]),
            formatSQLString($history_info["message_sentto"]),
            formatSQLString($history_info["message_title"]),
            formatSQLString($history_info["message_notes"]),
            formatSQLString($history_info["message_raw"])
        ));
    }
}
if (!function_exists("invoiceHistoryCustomerMessage")) {
    /**
     * @param int     $invoice_id
     * @param date $date
     *
     * @return history_data
     */
    function invoiceHistoryCustomerMessage($date, $invoice_id)
    {
        global $DB; 
        if ($query = $DB->query(sprintf(
            "SELECT `im`.`%s`,`ur`.`first_name`,`ur`.`last_name` FROM %s AS `im` 
             LEFT JOIN `highwaygroup`.`new_members` as `ur` ON `im`.`message_sender`= `ur`.`member_id` where `im`.`message_date`>= '%s' AND `im`.`invoice_id`=%d AND `im`.`message_type_id`=3
            ORDER BY `im`.`message_id` DESC",
            INVOICE_HISTORY_PRIMARYKEY,
            INVOICE_HISTORY_TABLENAME,
            $date,
            $invoice_id

        ))) {
            while ($row = $DB->fetch_assoc($query)) {
                $results[] = $row;
            }
            $DB->free_result($query);
        }

        return $results;
    }
}
