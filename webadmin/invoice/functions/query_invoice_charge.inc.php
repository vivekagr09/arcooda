<?php
if (!defined('INVOICE_CHARGE_TABLENAME')) {
    define('INVOICE_CHARGE_TABLENAME', '`highwaygroup`.`invoice_charges`');
}
if (!defined('INVOICE_CHARGE_PRIMARYKEY')) {
    define('INVOICE_CHARGE_PRIMARYKEY', 'id');
}
if (!defined('INVOICE_CHARGE_SECONDARYKEY')) {
    define('INVOICE_CHARGE_SECONDARYKEY', 'invoice_id');
}

if (!function_exists('invoiceChargeDefault')) {
    /**
     * @return mixed[]
     */
    function invoiceChargeDefault()
    {
        global $DB;

        return invoiceChargeVirtual($DB->modelDefault(
            INVOICE_CHARGE_TABLENAME,
            INVOICE_CHARGE_PRIMARYKEY
        ));
    }
}
if (!function_exists('invoiceChargeDelete')) {
    /**
     * @param int $id
     *
     * @return int
     */
    function invoiceChargeDelete($id)
    {
        global $DB;

        return $DB->modelDelete(
            $id,
            INVOICE_CHARGE_TABLENAME,
            INVOICE_CHARGE_PRIMARYKEY
        );
    }
}
if (!function_exists('invoiceChargeExists')) {
    /**
     * @param int $id
     *
     * @return bool
     */
    function invoiceChargeExists($id)
    {
        global $DB;

        return $DB->modelExists(
            $id,
            INVOICE_CHARGE_TABLENAME,
            INVOICE_CHARGE_PRIMARYKEY
        );
    }
}
if (!function_exists('invoiceChargeFormatNumber')) {
    /**
     * @var int $id
     *
     * @return string
     */
    function invoiceChargeFormatNumber($id)
    {
        return sprintf('C%02d', $id);
    }
}
if (!function_exists('invoiceChargeFromDatabase')) {
    /**
     * @param int $id
     *
     * @return mixed[]|null
     */
    function invoiceChargeFromDatabase($id)
    {
        global $DB;

        if ($row = $DB->modelLoad(
            $id,
            INVOICE_CHARGE_TABLENAME,
            INVOICE_CHARGE_PRIMARYKEY
        )) {
            return invoiceChargeVirtual($row);
        }

        return null;
    }
}
if (!function_exists('invoiceChargeGetValue')) {
    /**
     * @param int    $id
     * @param string $field
     *
     * @return mixed
     */
    function invoiceChargeGetValue($id, $field)
    {
        global $DB;

        if (array_key_exists($field, $DB->table_columns(
            INVOICE_CHARGE_TABLENAME
        ))) {
            return $DB->modelGet(
                $id,
                $field,
                INVOICE_CHARGE_TABLENAME,
                INVOICE_CHARGE_PRIMARYKEY
            );
        }

        if ($model = invoiceChargeFromDatabase($id)) {
            return (array_key_exists($field, $model)) ? $model[$field] : null;
        }

        return null;
    }
}
if (!function_exists('invoiceChargeHasValue')) {
    /**
     * @param int    $id
     * @param string $field
     * @param mixed  $value
     *
     * @return bool
     */
    function invoiceChargeHasValue($id, $field, $value)
    {
        return invoiceChargeGetValue($id, $field) == $value;
    }
}
if (!function_exists('invoiceChargeQuery')) {
    /**
     * @todo
     *
     * @param mixed[] $eq
     * @param mixed[] $lt
     * @param mixed[] $gt
     *
     * @return string
     */
    function invoiceChargeQuery($eq = array(), $lt = array(), $gt = array())
    {
        global $DB;

        $whereAND = array();

        return (count($whereAND)) ? implode(" AND ", $whereAND) : "1";
    }
}
if (!function_exists('invoiceChargeSearch')) {
    /**
     * @param string|null $where
     * @param string|null $order_by
     *
     * @return int[]
     */
    function invoiceChargeSearch($where = null, $order_by = null)
    {
        global $DB;

        // Legacy accepts criteria as first parameter
        if (is_array($where)) {
            $where = invoiceChargeQuery($where);
        }

        return $DB->query_list(sprintf(
            "SELECT `%s` FROM %s%s%s",
            INVOICE_CHARGE_PRIMARYKEY,
            INVOICE_CHARGE_TABLENAME,
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
if (!function_exists('invoiceChargeSetValue')) {
    /**
     * @param int    $id
     * @param string $field
     * @param mixed  $value
     *
     * @return int
     */
    function invoiceChargeSetValue($id, $field, $value)
    {
        global $DB;

        return $DB->modelSet(
            $id,
            $field,
            $value,
            INVOICE_CHARGE_TABLENAME,
            INVOICE_CHARGE_PRIMARYKEY
        );
    }
}
if (!function_exists('invoiceChargeSort')) {
    /**
     * @param string|null $where
     * @param string|null $order_by
     *
     * @return int[]
     */
    function invoiceChargeSort($where = null, $order_by = null)
    {
        // Legacy accepts list of id's as first parameter
        if (is_array($where)) {
            $where = invoiceChargeQuery(array(
                INVOICE_CHARGE_PRIMARYKEY => $where,
            ));
        }

        return invoiceChargeSearch($where, $order_by);
    }
}
if (!function_exists('invoiceChargeToDatabase')) {
    /**
     * @param mixed[] $item
     *
     * @return int
     */
    function invoiceChargeToDatabase(&$item)
    {
        global $DB;

        return $DB->modelSave(
            $item,
            INVOICE_CHARGE_TABLENAME,
            INVOICE_CHARGE_PRIMARYKEY
        );
    }
}
if (!function_exists('invoiceChargeVirtual')) {
    /**
     * @param mixed[] $row
     *
     * @return mixed[]
     */
    function invoiceChargeVirtual($row)
    {
        global $DB;

        static $charge_list;

        if (empty($row['charge_name'])) {
            if (is_null($charge_list)) {
                $charge_list = array();
                if ($query = $DB->query(sprintf(
                    "SELECT `charge_id`, `charge_name` FROM `highwaygroup`.`charges` ORDER BY `charge_name` ASC"
                ))) {
                    while ($row = $DB->fetch_assoc($query)) {
                        $charge_list[$row['charge_id']] = $row['charge_name'];
                    }
                    $DB->free_result($query);
                }
            }

            $row['charge_name'] = '';
            if (array_key_exists($row['charge_id'], $charge_list)) {
                $row['charge_name'] = $charge_list[$row['charge_id']];
            }
        }

        $row['charge_number'] = invoiceChargeFormatNumber($row['charge_id']);

        return $row;
    }
}

if (!function_exists('invoiceChargesDelete')) {
    /**
     * @param int $id
     *
     * @return int
     */
    function invoiceChargesDelete($id)
    {
        global $DB;

        return $DB->childrenDelete(
            $id,
            INVOICE_CHARGE_TABLENAME,
            INVOICE_CHARGE_SECONDARYKEY
        );
    }
}
if (!function_exists('invoiceChargesFromDatabase')) {
    /**
     * @param int $id
     *
     * @return mixed[][]
     */
    function invoiceChargesFromDatabase($id)
    {
        global $DB;

        $result = array();
        foreach ($DB->childrenLoad(
            $id,
            INVOICE_CHARGE_TABLENAME,
            INVOICE_CHARGE_PRIMARYKEY,
            INVOICE_CHARGE_SECONDARYKEY
        ) as $k => $v) {
            $result[$k] = invoiceChargeVirtual($v);
        }

        uasort($result, function ($a, $b) {
            return $a['charge_rank'] - $b['charge_rank'];
        });

        return $result;
    }
}
if (!function_exists('invoiceChargesToDatabase')) {
    /**
     * @todo previous version also recounts rank and deletes unlisted
     *
     * @param int       $id
     * @param mixed[][] $items
     *
     * @return void
     */
    /*
function invoiceChargesToDatabase($id, &$items)
{
global $DB;

$DB->childrenSave(
$id,
$items,
INVOICE_CHARGE_TABLENAME,
INVOICE_CHARGE_PRIMARYKEY,
INVOICE_CHARGE_SECONDARYKEY
);
}
 */
}

if (!function_exists('invoiceChargesFromPost')) {
    /**
     * Returns an array of invoice charge values.
     * Invoices selected from the database table `highwaygroup`
     * Input variable $posted used to select the charges returned.
     *
     * @param mixed[] $posted
     *
     * @return mixed[][]
     */
    function invoiceChargesFromPost($posted)
    {
        global $DB;

        $charges = array();
        $fields  = array();
        if ($query = $DB->query(sprintf(
            "SHOW COLUMNS FROM %s",
            INVOICE_CHARGE_TABLENAME
        ))) {
            while ($row = $DB->fetch_array($query)) {
                if ('auto_increment' != $row[5]) {
                    $fields[$row[0]] = $row[4];
                }
            }
            $DB->free_result($query);
        }

        $invoice_id = intval($posted['invoice_id']);
        $charges    = invoiceChargesFromDatabase($invoice_id);

        if (array_key_exists('charge_affected', $posted)) {
            foreach (explode(',', $posted['charge_affected']) as $key1) {
                foreach ($charges as $key2 => $charge) {
                    if ($charge['charge_count'] == $key1) {
                        unset($charges[$key2]);
                    }
                }
            }
        }

        if (array_key_exists('charge_count', $posted)) {
            foreach ($posted['charge_count'] as $key1) {
                foreach ($charges as $key2 => $charge) {
                    if ($charge['charge_count'] == $key1) {
                        unset($charges[$key2]);
                    }
                }
            }
        }

        if (array_key_exists('charge_id', $posted)) {
            foreach ($posted['charge_id'] as $key1 => $charge_id) {
                $charge                              = $fields;
                $charge[INVOICE_CHARGE_SECONDARYKEY] = $invoice_id;
                foreach ($fields as $key2 => $value) {
                    if (is_array($posted[$key2]) && array_key_exists($key1, $posted[$key2])) {
                        $charge[$key2] = $posted[$key2][$key1];
                    }
                }

                $charges[] = $charge;
            }
        }
        return $charges;
    }
}
if (!function_exists('invoiceChargesSearch')) {
    /**
     * A function that returns an array of integers representing charges.
     * Output is selected using the input variable $criteria.
     * Output represents the id attribute of a charge.
     *
     * @param mixed $criteria
     *
     * @return int[]
     */
    function invoiceChargesSearch($criteria = '')
    {
        global $DB;
        $sql         = "SELECT `c`.`charge_id` " . "FROM `highwaygroup`.`charges` AS `c`";
        $joins       = array();
        $field_types = getTableFields('`highwaygroup`.`charges`');
        $fields      = array();
        foreach (getTableColumns('`highwaygroup`.`charges`') as $key) {
            $fields[$key] = '`c`.`' . $key . '`';
        }

        $whereAND = array();
        if (is_array($criteria)) {
            foreach ($criteria as $field => $value) {
                if (array_key_exists($field, $fields)) {
                    if (is_array($value)) {
                        if (count($value) > 0) {
                            $whereOR = array();
                            if (substr($field_types[$field]['Type'], 0, 4) == 'set(') {
                                foreach ($value as $sub_value) {
                                    $whereOR[] = "FIND_IN_SET(" . formatSQLString($sub_value) . "," . $fields[$field] . ")";
                                }
                            } else {
                                foreach ($value as $sub_value) {
                                    $whereOR[] = $fields[$field] . " = " . formatSQLString($sub_value);
                                }
                            }
                            $whereAND[] = "(" . implode(" OR ", $whereOR) . ")";
                        } else {
                            return array();
                        }
                    } else {
                        if (substr($field_types[$field]['Type'], 0, 4) == 'set(') {
                            $whereAND[] = "FIND_IN_SET(" . formatSQLString($value) . ", " . $fields[$field] . ")";
                        } else {
                            $whereAND[] = $fields[$field] . " = " . formatSQLString($value);
                        }
                    }
                    unset($fields[$field]);
                }
            }
        }
        $joins      = array();
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
            $searchBits = array('charge_id');
            foreach ($search_for as $findme) {
                $whereOR = array();
                foreach ($searchBits as $key) {
                    if (trim($findme) != '') {
                        $whereOR[] = $key . " LIKE '%" . str_replace('%', '\%', str_replace('_', '\_', addslashes($findme))) . "%'";
                    }
                }

                if (count($whereOR) > 0) {
                    $whereAND[] = "(" . implode(" OR ", $whereOR) . ")";
                }
            }
        }

        $sql .= implode('', $joins);

        if (count($whereAND) > 0) {
            $sql .= " WHERE " . implode(" AND ", $whereAND);
        }

        $result = array();
        if ($query = $DB->query(sprintf(
            "%s GROUP BY `c`.`charge_id` ORDER BY `c`.`charge_name` ASC",
            $sql
        ))) {
            while ($row = $DB->fetch_assoc($query)) {
                $result[] = $row['charge_id'];
            }
            $DB->free_result($query);
        }

        return $result;
    }
}
if (!function_exists('invoiceChargesToDatabase')) {
    /**
     * invoiceChargesToDatabase
     *
     * @param int       $id
     * @param mixed[][] $charges
     *
     * @return void
     */
    function invoiceChargesToDatabase($id, $charges)
    {
        global $DB;
        $id = intval($id);
        if (0 == $id) {
            return;
        }

        $DB->query(sprintf(
            "DELETE FROM %s WHERE `%s` = %d",
            INVOICE_CHARGE_TABLENAME,
            INVOICE_CHARGE_SECONDARYKEY,
            $id
        ));

        $fields = array();
        if ($query = $DB->query(sprintf(
            "SHOW COLUMNS FROM %s",
            INVOICE_CHARGE_TABLENAME
        ))) {
            while ($row = $DB->fetch_array($query)) {
                if ('auto_increment' != $row[5]) {
                    $fields[$row[0]] = $row[4];
                }
            }
            $DB->free_result($query);
        }

        $re_sort = array();
        foreach ($charges as $key => $charge) {
            $re_sort[$key] = $charge['charge_rank'];
        }

        asort($re_sort);
        $counter = 1;
        foreach ($re_sort as $key => $rank) {
            $charges[$key]['charge_rank'] = $counter++;
        }

        foreach ($charges as $charge) {
            $insert_vals = array();
            if ($id > 0) {
                $charge[INVOICE_CHARGE_SECONDARYKEY] = $id;
            }

            foreach ($charge as $key => $value) {
                if (array_key_exists($key, $fields)) {
                    $insert_vals[$key] = addslashes($value);
                }
            }

            $DB->query(sprintf(
                "INSERT INTO %s (`%s`) VALUES ('%s')",
                INVOICE_CHARGE_TABLENAME,
                implode('`, `', array_keys($insert_vals)),
                implode("', '", $insert_vals)
            ));
        }
    }
}

if (!function_exists('invoiceChargesFormatNumber')) {
    /**
     * invoiceChargesFormatNumber
     *
     * A function that formats a number into a string.
     * Output is of the form, 'C' - up to two leadig zeros - the input.
     * e.g. C04, C545
     *
     * @param int $charge_id
     *
     * @return string
     */
    function invoiceChargesFormatNumber($charge_id)
    {
        return sprintf('C%02d', $charge_id);
    }
}
