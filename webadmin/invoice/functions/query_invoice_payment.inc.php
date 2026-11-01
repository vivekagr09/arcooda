<?php
if (!defined('INVOICE_PAYMENT_TABLENAME')) {
    define('INVOICE_PAYMENT_TABLENAME', '`highwaygroup`.`invoice_payments`');
}
/*
if (!defined('INVOICE_PAYMENT_PRIMARYKEY')) {
define('INVOICE_PAYMENT_PRIMARYKEY', 'id');
}
 */
if (!defined('INVOICE_PAYMENT_SECONDARYKEY')) {
    define('INVOICE_PAYMENT_SECONDARYKEY', 'invoice_id');
}

if (!function_exists('invoicePaymentDefault')) {
    /**
     * @return mixed[]
     */
    /*
    function invoicePaymentDefault()
    {
        global $DB;

        return invoicePaymentVirtual($DB->modelDefault(
            INVOICE_PAYMENT_TABLENAME,
            INVOICE_PAYMENT_PRIMARYKEY
        ));
    }
    */
}
if (!function_exists('invoicePaymentDelete')) {
    /**
     * @param int $id
     *
     * @return int
     */
    /*
function invoicePaymentDelete($id)
{
global $DB;

return $DB->modelDelete(
$id,
INVOICE_PAYMENT_TABLENAME,
INVOICE_PAYMENT_PRIMARYKEY
);
}
 */
}
if (!function_exists('invoicePaymentExists')) {
    /**
     * @param int $id
     *
     * @return bool
     */
function invoicePaymentExists($id)
{
    
    global $DB;

    return $DB->modelExists(
        $id,
        INVOICE_PAYMENT_TABLENAME,
        'TransactionNo'
    );
}

}
if (!function_exists('invoicePaymentFromDatabase')) {
    /**
     * @param int $id
     *
     * @return mixed[]|null
     */
    /*
function invoicePaymentFromDatabase($id)
{
global $DB;

if ($row = $DB->modelLoad(
$id,
INVOICE_PAYMENT_TABLENAME,
INVOICE_PAYMENT_PRIMARYKEY
)) {
return invoicePaymentVirtual($row);
}

return null;
}
 */
}
if (!function_exists('invoicePaymentGetValue')) {
    /**
     * @param int    $id
     * @param string $field
     *
     * @return mixed
     */
    /*
    function invoicePaymentGetValue($id, $field)
    {
        global $DB;

        if (array_key_exists($field, $DB->table_columns(
            INVOICE_PAYMENT_TABLENAME
        ))) {
            return $DB->modelGet(
                $id,
                $field,
                INVOICE_PAYMENT_TABLENAME,
                INVOICE_PAYMENT_PRIMARYKEY
            );
        }

        if ($model = invoicePaymentFromDatabase($id)) {
            return (array_key_exists($field, $model)) ? $model[$field] : null;
        }

        return null;
    }
*/
}
if (!function_exists('invoicePaymentHasValue')) {
    /**
     * @param int    $id
     * @param string $field
     * @param mixed  $value
     *
     * @return bool
     */
    /*
    function invoicePaymentHasValue($id, $field, $value)
    {
        return invoicePaymentGetValue($id, $field) == $value;
    }
    */
}
if (!function_exists('invoicePaymentQuery')) {
    /**
     * @todo
     *
     * @param mixed[] $eq
     * @param mixed[] $lt
     * @param mixed[] $gt
     *
     * @return string
     */
    function invoicePaymentQuery($eq = array(), $lt = array(), $gt = array())
    {
        global $DB;

        $whereAND = array();

        return (count($whereAND)) ? implode(" AND ", $whereAND) : "1";
    }
}
if (!function_exists('invoicePaymentSearch')) {
    /**
     * @param string|null $where
     * @param string|null $order_by
     *
     * @return int[]
     */
    /*
    function invoicePaymentSearch($where = null, $order_by = null)
    {
        global $DB;

        // Legacy accepts criteria as first parameter
        if (is_array($where)) {
            $where = invoicePaymentQuery($where);
        }

        return $DB->query_list(sprintf(
            "SELECT `%s` FROM %s%s%s",
            INVOICE_PAYMENT_PRIMARYKEY,
            INVOICE_PAYMENT_TABLENAME,
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
if (!function_exists('invoicePaymentSetValue')) {
    /**
     * @param int    $id
     * @param string $field
     * @param mixed  $value
     *
     * @return int
     */
    /*
function invoicePaymentSetValue($id, $field, $value)
{
global $DB;

return $DB->modelSet(
$id,
$field,
$value,
INVOICE_PAYMENT_TABLENAME,
INVOICE_PAYMENT_PRIMARYKEY
);
}
 */
}
if (!function_exists('invoicePaymentSort')) {
    /**
     * @param string|null $where
     * @param string|null $order_by
     *
     * @return int[]
     */
    /*
    function invoicePaymentSort($where = null, $order_by = null)
    {
        // Legacy accepts list of id's as first parameter
        if (is_array($where)) {
            $where = invoicePaymentQuery(array(
                INVOICE_PAYMENT_PRIMARYKEY => $where,
            ));
        }

        return invoicePaymentSearch($where, $order_by);
    }
    */
}
if (!function_exists('invoicePaymentToDatabase')) {
    /**
     * @param mixed[] $item
     *
     * @return int
     */
    /*
function invoicePaymentToDatabase(&$item)
{
global $DB;

return $DB->modelSave(
$item,
INVOICE_PAYMENT_TABLENAME,
INVOICE_PAYMENT_PRIMARYKEY
);
}
 */
}
if (!function_exists('invoicePaymentVirtual')) {
    /**
     * @param mixed[] $row
     *
     * @return mixed[]
     */
    function invoicePaymentVirtual($row)
    {
        return $row;
    }
}

if (!function_exists('invoicePaymentsDelete')) {
    /**
     * @param int $id
     *
     * @return int
     */
    function invoicePaymentsDelete($id)
    {
        global $DB;

        return $DB->childrenDelete(
            $id,
            INVOICE_PAYMENT_TABLENAME,
            INVOICE_PAYMENT_SECONDARYKEY
        );
    }
}
if (!function_exists('invoicePaymentsFromDatabase')) {
    /**
     * @param int $id
     *
     * @return mixed[]|null
     */
    /*
    function invoicePaymentsFromDatabase($id)
    {
        global $DB;

        $result = array();
        foreach ($DB->childrenLoad(
            $id,
            INVOICE_PAYMENT_TABLENAME,
            INVOICE_PAYMENT_PRIMARYKEY,
            INVOICE_PAYMENT_SECONDARYKEY
        ) as $k => $v) {
            $result[$k] = invoicePaymentVirtual($v);
        }

        return $result;
    }
    */
}
if (!function_exists('invoicePaymentsToDatabase')) {
    /**
     * @param int       $id
     * @param mixed[][] $items
     *
     * @return void
     */
    /*
function invoicePaymentsToDatabase($id, &$items)
{
global $DB;

$DB->childrenSave(
$id,
$items,
INVOICE_PAYMENT_TABLENAME,
INVOICE_PAYMENT_PRIMARYKEY,
INVOICE_PAYMENT_SECONDARYKEY
);
}
 */
}

if (!function_exists("invoicePaymentToDatabase")) {
    /**
     * @param int     $id
     * @param mixed[] $payment
     *
     * @return void
     */
    function invoicePaymentToDatabase($id, $payment)
    {
        global $DB;
        $id = intval($id);
        if (0 == $id) {
            return;
        }

        unset($payment['payment_count']);
        $payment[INVOICE_PAYMENT_SECONDARYKEY] = $id;
        if ($payment['payment_time'] > 0) {
            $payment['payment_date'] = formatDateTime($payment['payment_time']);
        }

        $insert_vals = array();
        foreach (getTableColumns(INVOICE_PAYMENT_TABLENAME) as $k) {
            if (array_key_exists($k, $payment)) {
                $insert_vals['`' . $k . '`'] = formatSQLString($payment[$k]);
            }
        }

        $DB->query(sprintf(
            "INSERT INTO %s (%s) VALUES (%s)",
            INVOICE_PAYMENT_TABLENAME,
            implode(', ', array_keys($insert_vals)),
            implode(', ', array_values($insert_vals))
        ));

        /*
    $DB->query(sprintf(
    "UPDATE `highwaygroup`.`invoices` SET `write_date` = NOW(), `proforma_date` = IF(`proforma_date` > %s, `proforma_date`, %s) WHERE `%s` = %d LIMIT 1",
    formatSQLString($payment['payment_date']),
    formatSQLString($payment['payment_date']),
    INVOICE_PAYMENT_SECONDARYKEY,
    $id
    ));
     */
    }
}
if (!function_exists("invoicePaymentLogToDatabase")) {
    /**
     * @param int     $id
     * @param mixed[] $payment
     *
     * @return void
     */
    function invoicePaymentLogToDatabase($payment)
    {
        global $DB;

        $insert_vals = array();
        foreach (getTableColumns('`invoice_payments_logs`') as $k) {
            if (array_key_exists($k, $payment)) {
                $insert_vals['`' . $k . '`'] = formatSQLString($payment[$k]);
            }
        }

        $DB->query(sprintf(
            "INSERT INTO %s (%s) VALUES (%s)",
            '`invoice_payments_logs`',
            implode(', ', array_keys($insert_vals)),
            implode(', ', array_values($insert_vals))
        ));
    }
}
if (!function_exists("invoicePaymentFromDatabase")) {
    /**
     * @param int $id
     * @param int $payment_count
     *
     * @return mixed[]
     */
    function invoicePaymentFromDatabase($id, $payment_count)
    {
        global $DB;

        if ($row = $DB->query_first(sprintf(
            "SELECT * FROM %s WHERE `%s` = %d AND `payment_count` = %d LIMIT 1",
            INVOICE_PAYMENT_TABLENAME,
            INVOICE_PAYMENT_SECONDARYKEY,
            $id,
            $payment_count
        ), MYSQLI_ASSOC)) {
            return $row;
        }

        return array();
    }
}
if (!function_exists("invoicePaymentsFromDatabase")) {
    /**
     * @param int $id
     *
     * @return mixed[][]
     */
    function invoicePaymentsFromDatabase($id)
    {
        global $DB;

        $result = array();
        if ($query = $DB->query(sprintf(
            "SELECT `p`.*, UNIX_TIMESTAMP(`p`.`payment_date`) AS `payment_time`, UNIX_TIMESTAMP(`p`.`update_date`) AS `update_time` FROM %s AS `p` WHERE `p`.`%s` = %d",
            INVOICE_PAYMENT_TABLENAME,
            INVOICE_PAYMENT_SECONDARYKEY,
            $id
        ))) {
            while ($row = $DB->fetch_assoc($query)) {
                $result[] = $row;
            }
            $DB->free_result($query);
        }

        return $result;
    }
}

if (!function_exists("invoicePaymentTemplateVars")) {
    /**
     * @param mixed[] $payment
     *
     * @return mixed[]
     */
    function invoicePaymentTemplateVars($payment)
    {
        $result = array();
        foreach ($payment as $key => $value) {
            $result['[' . strtoupper($key) . ']'] = $value;
        }

        return $result;
    }
}
