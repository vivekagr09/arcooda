<?php
if (!defined('INVOICE_DECLINE_TABLENAME')) {
    define('INVOICE_DECLINE_TABLENAME', '`highwaygroup`.`invoice_declines`');
}
if (!defined('INVOICE_DECLINE_PRIMARYKEY')) {
    define('INVOICE_DECLINE_PRIMARYKEY', 'id');
}
if (!defined('INVOICE_DECLINE_SECONDARYKEY')) {
    define('INVOICE_DECLINE_SECONDARYKEY', 'invoice_id');
}

if (!function_exists("invoiceDeclineToDatabase")) {
    /**
     * @param int     $invoice_id
     * @param mixed[] $decline
     *
     * @return int
     */
    function invoiceDeclineToDatabase($invoice_id, $decline)
    {
        global $DB;

        if ($decline['id']) {
            if ($DB->query(sprintf(
                "INSERT INTO %s (`%s`, `%s`, `reason`, `comments`) VALUES (%d, %d, %s, %s) ON DUPLICATE KEY UPDATE `%s` = %d, `reason` = %s, `comments` = %s",
                INVOICE_DECLINE_TABLENAME,
                INVOICE_DECLINE_PRIMARYKEY,
                INVOICE_DECLINE_SECONDARYKEY,
                $decline['id'],
                $invoice_id,
                $DB->escape($decline['reason']),
                $DB->escape($decline['comments']),
                INVOICE_DECLINE_SECONDARYKEY,
                $invoice_id,
                $DB->escape($decline['reason']),
                $DB->escape($decline['comments'])
            ))) {
                $decline['invoice_id'] = $invoice_id;
                return $decline['id'];
            }
        }

        if ($DB->query(sprintf(
            "INSERT INTO %s (`%s`, `reason`, `comments`,`payment_id`) VALUES (%d, %s, %s,%s)",
            INVOICE_DECLINE_TABLENAME,
            INVOICE_DECLINE_SECONDARYKEY,
            $invoice_id,
            $DB->escape($decline['reason']),
            $DB->escape($decline['comments']),
            isset($decline['payment_id'])?$DB->escape($decline['payment_id']):''
        ))) {
            $decline['invoice_id'] = $invoice_id;
            return $decline['id']  = $DB->insert_id();
        }

        return 0;
    }
}
if (!function_exists("invoiceDeclineFromDatabase")) {
    /**
     * @param int $id
     *
     * @return mixed[]
     */
    function invoiceDeclineFromDatabase($id)
    {
        global $DB;

        if ($row = $DB->query_first(sprintf(
            "SELECT * FROM %s WHERE `%s` = %d LIMIT 1",
            INVOICE_DECLINE_TABLENAME,
            INVOICE_DECLINE_PRIMARYKEY,
            $id
        ), MYSQLI_ASSOC)) {
            return $row;
        }

        return array();
    }
}
if (!function_exists("invoiceDeclinesFromDatabase")) {
    /**
     * @param int $invoice_id
     *
     * @return mixed[][]
     */
    function invoiceDeclinesFromDatabase($invoice_id)
    {
        global $DB;

        $result = array();
        if ($query = $DB->query(sprintf(
            "SELECT * FROM %s WHERE `%s` = %d",
            INVOICE_DECLINE_TABLENAME,
            INVOICE_DECLINE_SECONDARYKEY,
            $invoice_id
        ))) {
            while ($row = $DB->fetch_assoc($query)) {
                $result[] = $row;
            }
            $DB->free_result($query);
        }

        return $result;
    }
}

if (!function_exists('invoiceDeclineAdd')) {
    /**
     * @param int    $invoice_id
     * @param string $reason
     * @param string $comments
     *
     * @return int
     */
    function invoiceDeclineAdd($invoice_id, $reason, $comments = '')
    {
        $invoice_decline = array(
            'reason'   => $reason,
            'comments' => $comments,
        );
        return invoiceDeclineToDatabase($invoice_id, $invoice_decline);
    }
}
if (!function_exists('invoiceDeclineExists')) {
    /**
     * @param int $id
     *
     * @return bool
     */
    function invoiceDeclineExists($id)
    {
        
        global $DB;

        return  $DB->modelExists(
            $id,
            INVOICE_DECLINE_TABLENAME,
            'payment_id'
        );
    }

}

