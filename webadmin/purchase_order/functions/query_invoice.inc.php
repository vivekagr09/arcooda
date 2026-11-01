<?php
define("INVOICE_EMAIL_CONTACT", 27);
define("INVOICE_EMAIL_GENERAL", 1);
define("INVOICE_EMAIL_ENQUIRY", 16);
define("INVOICE_EMAIL_INTEREST", 14);
define("INVOICE_EMAIL_INVOICE", 5);
define("INVOICE_EMAIL_MODIFIED", 11);
define("INVOICE_EMAIL_SHIPPED", 2);
define("INVOICE_EMAIL_VALIDATE", 3);

define("INVOICE_MESSAGE_GENERAL", 1);
define("INVOICE_MESSAGE_TO_CUSTOMER", 2);
define("INVOICE_MESSAGE_FROM_CUSTOMER", 3);
define("INVOICE_MESSAGE_ADMIN", 4);
define("INVOICE_MESSAGE_TO_STAFF", 5);
define("INVOICE_MESSAGE_INVOICE_MODIFIED", 6);
define("INVOICE_MESSAGE_RECORDED_ERROR", 7);
define("INVOICE_MESSAGE_ATTACHMENT_ADDED", 8);
define("INVOICE_MESSAGE_ATTACHMENT_REMOVED", 9);
define("INVOICE_MESSAGE_QUERY", 10);
define("INVOICE_MESSAGE_ENQUIRY_MODIFIED", 11);
define("INVOICE_MESSAGE_INVOICE_SENT", 12);
define("INVOICE_MESSAGE_ENQUIRY_SENT", 12);
define("INVOICE_MESSAGE_INQUIRY_SENT", 12);

//------------------------------------------------------------------------
/**
 * A collection of functions used to generate invoice data.
 *
 * @author Highway Group
 */

//------------------------------------------------------------------------
/*
Tables:
//`world`.`country`                    select
`charges`                     select
`company_info`                select
`email_templates`             select
`freight_services_quotes`            insert/delete/update
`freight_services`            select
`generic_locks`               select/insert/      /update
`inquiries`                   select
`inquiry_packing_requirement` select
`inquiry_products`            select
`invoice_charges`             select/insert/delete
`invoice_courier_quotes`      select/insert/delete
`invoice_jobs`                select/insert
`invoice_linker`                     insert
`invoice_message_types`       select
`invoice_messages`            select/insert
`invoice_payments`            select/insert
`invoice_permissions`         select
`invoice_products`            select/insert/delete
`invoices`                    select/insert/delete/update
`payment_methods`             select
`websites`                    select
 */

if (!function_exists("getInvoicePermissions")) {
    function getInvoicePermissions($user_id)
    {
        global $DB;
        $results = array();
        $query   = $DB->query('SELECT * FROM `invoice_permissions` WHERE `user` = ' . intval($user_id));
        while ($row = $DB->fetch_array($query)) {
            $results[$row['section_id']] = $row['section_id'];
        }

        return $results;
    }
}
if (!function_exists('getFreightServiceDetails')) {
    function getFreightServiceDetails($id, $fields = '')
    {
        global $DB;
        $id = intval($id);
        if (!$id) {
            return false;
        }

        $strField = $fields;
        if (is_array($fields)) {
            $strField = implode(', ', $fields);
        }

        $sql = "SELECT " . $strField . " FROM `freight_services` WHERE `id` = $id";
        $row = $DB->query_first($sql, MYSQLI_ASSOC);
        $ret = '';
        if (is_array($fields)) {
            foreach ($fields as $field) {
                $ret[$field] = $row[$field];
            }
        } else {
            $ret = $row[$field];
        }

        return $ret;
    }
}

//------------------------------------------------------------------------
/**
 * invoiceChargesFormatNumber
 *
 * A function that formats a number into a string.
 * Output is of the form, 'C' - up to two leadig zeros - the input.
 * e.g. C04, C545
 *
 * @param float
 *
 * @return string
 */
if (!function_exists('invoiceChargesFormatNumber')) {
    function invoiceChargesFormatNumber($charge_id)
    {
        return sprintf('C%02d', $charge_id);
    }
}
//------------------------------------------------------------------------
/**
 * purchaseorderChargesFromDatabase
 *
 * A function that returns invoice data from the database all invoices returned have the id $invoice_id.
 *
 * @param integer
 * @return array
 */
if (!function_exists('purchaseorderChargesFromDatabase')) {
    function purchaseorderChargesFromDatabase($order_id)
    {
        global $DB;
        $order_id = intval($order_id);
        $sql        = "SELECT `ic`.*, `c`.`charge_name` AS `charge_name2` FROM `purchase_order_charges` AS `ic` LEFT JOIN `charges` AS `c` ON `c`.`charge_id` = `ic`.`charge_id` WHERE `ic`.`order_id` = " . $order_id . " ORDER BY `ic`.`charge_rank` ASC";
        $query      = $DB->query($sql);
        $result     = array();
        while ($row = $DB->fetch_assoc($query)) {
            if ('' == $row['charge_name']) {
                $row['charge_name'] = $row['charge_name2'];
            }

            unset($row['charge_name2']);
            $result[] = $row;
        }
        $DB->free_result($query);
        return $result;
    }
}

//------------------------------------------------------------------------
/**
 * purchaseorderChargesFromPost
 *
 * Returns an array of invoice charge values.
 * Input variable $posted used to select the charges returned.
 *
 * @param array
 * @return array
 */
if (!function_exists('purchaseorderChargesFromPost')) {
    function purchaseorderChargesFromPost($posted)
    {
        global $DB;
        $charges = array();
        $fields  = array();
        $query   = $DB->query("SHOW COLUMNS FROM `purchase_order_charges`");
        while ($row = $DB->fetch_array($query)) {
            if ('auto_increment' != $row[5]) {
                $fields[$row[0]] = $row[4];
            } else {
                $auto = $row[0];
            }
        }

        $status     = 'enquiry';
        $order_id = intval($posted['order_id']);
        if (purchaseOrderExists($invoice_id)) {
            $charges = purchaseorderChargesFromDatabase($invoice_id);
            $row     = $DB->query_first("SELECT `order_status` FROM `purchase_order` WHERE `order_id` = " . $order_id, MYSQLI_ASSOC);
            $status  = $row['order_status'];
        }
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
                $charge               = $fields;
                $charge['order_id'] = $posted['order_id'];
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

//------------------------------------------------------------------------
/**
 * invoiceChargesSearch
 *
 * A function that returns an array of integers representing charges.
 * Output is selected using the input variable $criteria.
 * Output represents the id attribute of a charge.
 *
 * @param array
 * @return array
 */
if (!function_exists('invoiceChargesSearch')) {
    function invoiceChargesSearch($criteria = '')
    {
        global $DB;
        $joins       = array();
        $field_types = getTableFields('`charges`');
        $fields      = array();
        foreach (getTableColumns('`charges`') as $key) {
            $fields[$key] = '`c`.`' . $key . '`';
        }

        $whereAND = array();
        if (is_array($criteria)) {
            foreach ($criteria as $field => $value) {
                if (array_key_exists($field, $fields)) {
                    if (is_array($value)) {
                        if (count($value) > 0) {
                            $whereOR = array();
                            if (substr($field_types[$field][1], 0, 4) == 'set(') {
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
                        if (substr($field_types[$field][1], 0, 4) == 'set(') {
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
        if (is_array($criteria) && is_array($criteria['any_text'])) {
            $search_for = $criteria['any_text'];
        }

        if (is_array($criteria) && is_string($criteria['any_text'])) {
            $search_for = explode(' ', $criteria['any_text']);
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

        $results = array();
        $query   = $DB->query(sprintf(
            "SELECT `c`.`charge_id` FROM `charges` AS `c`%s%s GROUP BY `c`.`charge_id` ORDER BY `c`.`charge_name` ASC",
            (count($joins)) ? implode('', $joins) : '',
            (count($whereAND)) ? (" WHERE " . implode(" AND ", $whereAND)) : ""
        ));
        while ($row = $DB->fetch_assoc($query)) {
            $results[] = $row['charge_id'];
        }

        $DB->free_result($query);
        return $results;
    }
}
if (!function_exists('productFormatNumber')) {
    function productFormatNumber($id)
    {
        return sprintf('%05d', $id);
    }
}
//------------------------------------------------------------------------
/**
 * invoiceChargesToDatabase
 *
 * @param array
 * @return array
 */
if (!function_exists('purchaseOrderChargesToDatabase')) {
    function purchaseOrderChargesToDatabase($order_id, $charges)
    {
        global $DB;
        $order_id = intval($order_id);
        if (0 == $order_id) {
            return;
        }

        $sql = "DELETE FROM `purchase_order_charges` WHERE `order_id` = " . $order_id;
        $DB->query($sql);
        $fields = array();
        $query  = $DB->query("SHOW COLUMNS FROM `purchase_order_charges`");
        while ($row = $DB->fetch_array($query)) {
            if ('auto_increment' != $row[5]) {
                $fields[$row[0]] = $row[4];
            }
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
            if ($invoice_id > 0) {
                $charge['invoice_id'] = $invoice_id;
            }

            foreach ($charge as $key => $value) {
                if (array_key_exists($key, $fields)) {
                    $insert_vals[$key] = addslashes($value);
                }
            }

            $sql = "INSERT INTO `purchase_order_charges` (`" . implode('`, `', array_keys($insert_vals)) . "`) VALUES ('" . implode("', '", $insert_vals) . "')";
            $DB->query($sql);
        }
    }
}
if (!function_exists('invoiceChunk')) {
    function invoiceChunk($invoice_list, $group_by = '')
    {
        global $DB;
        if (!is_array($invoice_list) || count($invoice_list) < 1) {
            return $invoice_list;
        }
        if (!is_array($group_by)) {
            $group_by = array($group_by);
        }

        $fields = getTableColumns('`invoices`');
        foreach ($fields as $key => $value) {
            $fields[$key] = '`i`.`' . $value . '`';
        }

        foreach (purchaseOrderGetOptionList('ext_fields') as $key => $value) {
            $fields[$key] = $value . ' AS ' . $key;
        }

        $field_list = array('invoice_id');
        foreach ($group_by as $field) {
            if (array_key_exists($field, $fields)) {
                $field_list[] = $fields[$field];
            }
        }

        $field_list = array_unique($field_list);

        $sql     = "SELECT " . implode(', ', $field_list) . " FROM `invoices` AS `i` WHERE " . purchaseorderSQLList($invoice_list);
        $query   = $DB->query($sql);
        $results = array();
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
            $pointer[] = $row['invoice_id'];
        }
        $DB->free_result($query);

        $fields = getTableColumns('`inquiries`');
        foreach ($fields as $k => $v) {
            $fields[$k] = '`i`.`' . $v . '`';
        }

        foreach (purchaseOrderGetOptionList('inquiry_fields') as $k => $v) {
            $fields[$k] = $v . ' AS `' . $k . '`';
        }

        $field_list = array('inquiry_id');
        foreach ($group_by as $k) {
            if (array_key_exists($k, $fields)) {
                $field_list[] = $fields[$k];
            }
        }

        $field_list = array_unique($field_list);
        $query      = $DB->query("SELECT " . implode(', ', $field_list) . " FROM `inquiries` AS `i` WHERE " . inquirySQLList($invoice_list));
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
            $pointer[] = $row['inquiry_id'];
        }
        $DB->free_result($query);

        return $results;
    }
}
if (!function_exists('invoiceCourierQuotesFromDatabase')) {
    function invoiceCourierQuotesFromDatabase($invoice_id)
    {
        global $DB;
        $invoice_id = intval($invoice_id);
        $sql        = "SELECT `cq`.* FROM `invoice_courier_quotes` AS `cq` WHERE `cq`.`invoice_id` = " . $invoice_id;
        $query      = $DB->query($sql);
        $result     = array();
        while ($row = $DB->fetch_assoc($query)) {
            $result[] = $row;
        }

        $DB->free_result($query);
        return $result;
    }
}
if (!function_exists('invoiceCourierQuotesFromPost')) {
    function invoiceCourierQuotesFromPost($posted)
    {
        global $DB;
        $quotes = array();
        $fields = array();
        $query  = $DB->query("SHOW COLUMNS FROM `invoice_courier_quotes`");
        while ($row = $DB->fetch_array($query)) {
            if ('auto_increment' != $row[5]) {
                $fields[$row[0]] = $row[4];
            } else {
                $auto = $row[0];
            }
        }

        if (!array_key_exists('quotes_affected', $posted)) {
            $quotes = invoiceCourierQuotesFromDatabase($invoice_id);
        }

        if (is_array($posted['freightcourier_id'])) {
            foreach ($posted['freightcourier_id'] as $key1 => $courier_id) {
                $quote               = $fields;
                $quote['invoice_id'] = $posted['invoice_id'];
                foreach ($fields as $key2 => $value) {
                    if (is_array($posted['freightcourier_' . $key2]) && array_key_exists($key1, $posted['freightcourier_' . $key2])) {
                        $quote[$key2] = $posted['freightcourier_' . $key2][$key1];
                    }
                }

                $quote['flag']      = ('on' == $quote['flag']) ? 'y' : 'n';
                $quote['insurance'] = ('Yes' == $quote['insurance']) ? 'y' : (('No' == $quote['insurance']) ? 'n' : $quote['insurance']);
                $quote['tail_gate'] = ('Yes' == $quote['tail_gate']) ? 'y' : (('No' == $quote['tail_gate']) ? 'n' : $quote['tail_gate']);
                $quote['track']     = ('Yes' == $quote['track']) ? 'y' : (('No' == $quote['track']) ? 'n' : $quote['track']);
                if (isset($posted['freightcourier_notes'][$key1])) {
                    $quote['del_time'] = $posted['freightcourier_notes'][$key1];
                }

                if (isset($posted['freightcourier_cost'][$key1])) {
                    $quote['cost_price'] = $posted['freightcourier_cost'][$key1];
                }

                if (isset($posted['freightcourier_offer'][$key1])) {
                    $quote['sell_price'] = $posted['freightcourier_offer'][$key1];
                }

                if (isset($posted['freightcourier_online'][$key1])) {
                    $quote['retail_price'] = $posted['freightcourier_online'][$key1];
                }

                if (isset($posted['freightcourier_costau'][$key1])) {
                    $quote['cost_priceau'] = $posted['freightcourier_costau'][$key1];
                }

                if (isset($posted['freightcourier_costus'][$key1])) {
                    $quote['cost_priceus'] = $posted['freightcourier_costus'][$key1];
                }

                if (isset($posted['freightcourier_offerau'][$key1])) {
                    $quote['sell_priceau'] = $posted['freightcourier_offerau'][$key1];
                }

                if (isset($posted['freightcourier_offerus'][$key1])) {
                    $quote['sell_priceus'] = $posted['freightcourier_offerus'][$key1];
                }

                if (isset($posted['freightcourier_supplier_id'][$key1])) {
                    $quote['supplier'] = $posted['freightcourier_supplier_id'][$key1];
                }

                $quotes[] = $quote;
            }
        }
        return $quotes;
    }
}
if (!function_exists('productReservationToDatabase')) {
    function productReservationToDatabase(&$item)
    {
        global $DB;
        $insert_vals = array();
        foreach (getTableColumns('`products_reserved`') as $k) {
            if (array_key_exists($k, $item)) {
                $insert_vals['`' . $k . '`'] = formatSQLString($item[$k]);
            }
        }
        $DB->query(sprintf(
            "INSERT INTO `products_reserved` (%s) VALUES (%s)",
            implode(', ', array_keys($insert_vals)),
            implode(', ', array_values($insert_vals))
        ));
    }
}
if (!function_exists('invoiceCourierQuotesToDatabase')) {
    function invoiceCourierQuotesToDatabase($invoice_id, $quotes)
    {
        global $DB;
        $invoice_id = intval($invoice_id);
        if (0 == $invoice_id) {
            return;
        }

        $DB->query("DELETE FROM `invoice_courier_quotes` WHERE `invoice_id` = " . $invoice_id);
        $fields = array();
        $query  = $DB->query("SHOW COLUMNS FROM `invoice_courier_quotes`");
        while ($row = $DB->fetch_array($query)) {
            if ('auto_increment' != $row[5]) {
                $fields[$row[0]] = $row[4];
            }
        }

        if (is_array($quotes)) {
            foreach ($quotes as $quote) {
                $insert_vals = array();
                if ($invoice_id > 0) {
                    $quote['invoice_id'] = $invoice_id;
                }

                foreach ($quote as $key => $value) {
                    if (array_key_exists($key, $fields)) {
                        $insert_vals[$key] = addslashes($value);
                    }
                }

                $DB->query("INSERT INTO `invoice_courier_quotes` (`" . implode('`, `', array_keys($insert_vals)) . "`) VALUES ('" . implode("', '", $insert_vals) . "')");
            }
        }

    }
}
if (!function_exists('getCourierproviderList')) {
    function getCourierproviderList()
    {
        global $DB;
        $results = array();
        $sql="SELECT * FROM `couriers`";
        $query   = $DB->query($sql);
        while ($row = $DB->fetch_assoc($query)) {
            $results[$row['courier_id']] = $row['courier_name'];
        }

        $DB->free_result($query);
        return $results;
    }
}
if (!function_exists('getFreightList')) {
    function getFreightList()
    {
        global $DB;
        $results = array();
        $sql="SELECT * FROM `purchase_order_freight`";
        $query   = $DB->query($sql);
        while ($row = $DB->fetch_assoc($query)) {
            $results[$row['id']] = $row;
        }

        $DB->free_result($query);
        return $results;
    }
}
if (!function_exists('getOrderAddress')) {
    function getOrderAddress()
    {
        global $DB;
        $results = array();
        $sql="SELECT * FROM `purchase_order_address`";
        $query   = $DB->query($sql);
        while ($row = $DB->fetch_assoc($query)) {
            $results[$row['id']] = $row['address'];
        }

        $DB->free_result($query);
        return $results;
    }
}
if (!function_exists('getOrderAddressByid')) {
    function getOrderAddressByid($id)
    {
        global $DB;
        $results = '';
        $sql="SELECT * FROM `purchase_order_address` where id=".$id;
        $query   = $DB->query($sql);
        while ($row = $DB->fetch_assoc($query)) {
            $results = $row['address'];
        }

        $DB->free_result($query);
        return $results;
    }
}
if (!function_exists('invoiceDelete')) {
    function invoiceDelete($invoice_id)
    {
        $invoice_id = intval($invoice_id);
       
    }
}
if (!function_exists('invoiceEmailTemplatesList')) {
    function invoiceEmailTemplatesList()
    {
        global $DB;
        $results = array();
        $query   = $DB->query("SELECT `template_id`, `template_name`, `template_subject`, `template_text` FROM `email_templates` WHERE FIND_IN_SET('invoice', `template_group`)");
        while ($row = $DB->fetch_assoc($query)) {
            $results[$row['template_id']] = $row;
        }

        $DB->free_result($query);
        return $results;
    }
}
if (!function_exists('purchaseOrderExists')) {
    function purchaseOrderExists($order_id)
    {
        global $DB;
        $row = $DB->query_first("SELECT `i`.`order_id` FROM `purchase_order` AS `i` WHERE `i`.`order_id` = " . intval($order_id) . " LIMIT 1", MYSQLI_ASSOC);
        if ($row) {
            return true;
        }

        return false;
    }
}
if (!function_exists('purchaseOrderFormatNumber')) {
    function purchaseOrderFormatNumber($invoice_id, $revision_id = 0, $site_id = 3,$space=0)
    {
        $invoice_status = purchaseOrderGetValue($invoice_id, "order_status");
        if ("" == $invoice_status) {
            $invoice_status = "inquiry";
        }
        $prefix1_lookup = array(
            'inquiry'   => 'Q',
            'enquiry'   => 'Q',
            'closed'    => 'Q',
            'proforma'  => 'P',
            'sales'     => 'I',
            'processed' => 'I',
            'cancelled' => 'Q',
            'return'    => 'R',
            'completed' => 'R',
        );
        $prefix1 = $prefix1_lookup[$invoice_status];

        $prefix2_lookup = array(
            'inquiry'   => 'POCO#',
            'enquiry'   => 'POCO#',
            'closed'    => 'POCO#',
            'proforma'  => 'POCO#',
            'sales'     => 'POCO#',
            'processed' => 'POCO#',
            'cancelled' => 'POCO#',
            'return'    => 'POCO#',
            'completed' => 'POCO#',
        );

        $prefix2 = $prefix2_lookup[$invoice_status];

        $prefix3_lookup = purchaseOrderGetOptionList("invoice_prefix");
        $prefix3        = $prefix3_lookup[$site_id];

        $suffix = "";
        if ($revision_id > 0) {
            $suffix = chr(ord('A') + $revision_id - 1);
        }

        if (3 == $site_id) {
            if($space){
                return sprintf("%s%s%06d%s", $prefix2,' ', $invoice_id, $suffix);
            }else{
                return sprintf("%s%06d%s", $prefix2, $invoice_id, $suffix);
            }
            
        }

        /**
         * Hack for pain in ass company that assumes quotes are invoices
         */
        if (in_array($invoice_id, array(21493, 22187))) {
            $prefix1 = "Q";
        }

        return sprintf("%s%s%05d%s", $prefix2, $prefix3, $invoice_id, $suffix);
    }
}
if (!function_exists('invoiceForwarderQuotesFromPost')) {
    function invoiceForwarderQuotesFromPost($posted)
    {
        global $DB;
        $fields = array();
        $query  = $DB->query("SHOW COLUMNS FROM `freight_services_quotes`");
        while ($row = $DB->fetch_array($query)) {
            if ('auto_increment' != $row[5]) {
                $fields[$row[0]] = $row[4];
            } else {
                $auto = $row[0];
            }
        }

        $new_quotes = array();
        $quotes     = $posted['freightquote'];
        $count      = 1;
        if (is_array($quotes) && count($quotes)) {
            foreach ($quotes as $qcount => $quote) {
                if ('' == $quote['quote_currency']) {
                    $quote['quote_currency'] = $posted['invoice_currency'];
                }

                $new_quotes[$qcount]['customer_id']   = $posted['member_id'];
                $new_quotes[$qcount]['prefered_flag'] = 'n';
                if ('on' == $quote['prefered']) {
                    $new_quotes[$qcount]['prefered_flag'] = 'y';
                }

                $new_quotes[$qcount]['cost_flags'] = (is_array($quote['cost_flags'])) ? implode(',', array_keys($quote['cost_flags'])) : '';
                $new_quotes[$qcount]['id']         = $quote['id'];
                $new_quotes[$qcount]['cost_price'] = $quote['total_amount'];
                unset($quote['cost_flags']);
                foreach ($quote as $key => $value) {
                    if (array_key_exists($key, $fields)) {
                        $new_quotes[$qcount][$key] = $value;
                    }
                }

                $new_quotes[$qcount]['qcount'] = $count++;
                if ('' == $quote['product_id']) {
                    unset($new_quotes[$qcount]);
                }

            }
        }
        return $new_quotes;
    }
}
if (!function_exists('invoiceForwarderQuotesToDatabase')) {
    function invoiceForwarderQuotesToDatabase($invoice_id, $quotes, $affected_quotes)
    {
        global $DB;
        //now have to delete the freight quotes that were affected..
        foreach ($affected_quotes as $quote_id) {
            $DB->query("DELETE FROM `freight_services_quotes` WHERE `id` = " . intval($quote_id));
        }

        $invoice_id = intval($invoice_id);
        if (0 == $invoice_id) {
            return;
        }

        $fields = array();
        $query  = $DB->query("SHOW COLUMNS FROM `freight_services_quotes`");
        while ($row = $DB->fetch_array($query)) {
            if ('auto_increment' != $row[5]) {
                $fields[$row[0]] = $row[4];
            }
        }

        if (is_array($quotes) && count($quotes)) {
            foreach ($quotes as $quote) {
                $quote_id = intval($quote['id']);
                $sql      = (($quote_id > 0) ? "UPDATE" : "INSERT") . " `freight_services_quotes` ";
                $set      = array();
                if ($quote_id <= 0) {
                    $set[] = "`date`=NOW()";
                }

                if ($invoice_id > 0) {
                    $quote['invoice_id'] = $invoice_id;
                }

                foreach ($quote as $key => $value) {
                    if (array_key_exists($key, $fields)) {
                        $set[] = $key . "=" . formatSQLString($value);
                    }
                }

                if (count($set)) {
                    $sql .= " SET " . implode(', ', $set) . (($quote_id > 0) ? " WHERE id=" . $quote_id : "");
                    //echo $sql;
                    $DB->query($sql);
                }
            }
        }
    }
}
if (!function_exists('deleteproductimagesnotsaved')) {
    function deleteproductimagesnotsaved()
    {
        global $DB;
        $sql = "DELETE FROM `suppliers_invoice_attachment` WHERE `invoice_id` = 0";
        $DB->query($sql);
    }
}
if (!function_exists('purchaseOrderFromDatabase')) {
    function purchaseOrderFromDatabase($order_id, $product_stuff = true)
    {
        global $DB;
        $order_id = intval($order_id);

        $fields = array('`i`.*');
        foreach (purchaseOrderGetOptionList('ext_fields') as $k => $v) {
            $fields[] = $v . " AS `" . $k . "`";
        }

        $sql = "SELECT " . implode(", ", $fields) . " FROM `purchase_order` AS `i` WHERE `i`.`order_id` = " . $order_id . " LIMIT 1";
        $row = $DB->query_first($sql, MYSQLI_ASSOC);
        if ($row) {
            $summary = $row;
        } else {
                $fields = array();
                $query  = $DB->query("SHOW COLUMNS FROM `purchase_order`");
                while ($row = $DB->fetch_array($query, MYSQLI_NUM)) {
                    $fields[$row[0]] = $row;
                }
                $DB->free_result($query);
                $summary = array();
                foreach ($fields as $key => $row) {
                    $summary[$key] = $row[4];
                }
        }
        $country_list = getCountryList(false);

        $bill_array = array(
            ('' != $summary['bill_company']) ? (trim($summary['bill_company'])) : (trim($summary['bill_name'])),
            trim($summary['bill_address']),
            ltrim(trim($summary['bill_city'] . (('' != $summary['bill_state']) ? ', ' . $summary['bill_state'] : '') . ' ' . $summary['bill_postcode']), ','),
            ('' != $summary['bill_country']) ? trim($country_list[$summary['bill_country']]) : '',
            "\r",
            ('' != $summary['bill_vat']) ? ('VAT: ' . $summary['bill_vat']) : '',
            ('' != $summary['bill_company'] && '' != $summary['bill_name'] && $summary['bill_name'] != $summary['bill_company']) ? ('Attn: ' . $summary['bill_name']) : '',
            ('' != $summary['bill_phone']) ? ('Ph: ' . $summary['bill_phone']) : '',
            ('' != $summary['bill_email']) ? ('Email: ' . $summary['bill_email']) : '',
        );
        foreach ($bill_array as $key => $value) {
            if ('' == $value) {
                unset($bill_array[$key]);
            }
        }

        $summary['bill_address_full'] = implode("\n", $bill_array);
        
        $summary['time_open']           = $_SERVER['REQUEST_TIME'] - strtotime($summary['open_date']);
        if ('processed' == $summary['invoice_status'] || 'closed' == $summary['invoice_status']) {
            $summary['time_open'] = $summary['write_time'] - strtotime($summary['open_date']);
        }

        $summary['days_open'] = max(0, intval($summary['time_open'] / 86400));
        if ($product_stuff) {
            $summary['products_needed'] = 0;
            $summary['products_found']  = 0;
            $products_list              = purchaseOrderProductsFromDatabase($invoice_id);
            foreach ($products_list as $product) {
                $summary['products_needed']++;
                if ('n' != $product['supplier_found']) {
                    $summary['products_found']++;
                }

            }
            $summary['products_located'] = 'n';
            if ('' != $summary['products_locate_date'] && '0000-00-00 00:00:00' != $summary['products_locate_date']) {
                $summary['products_located'] = 'y';
            }

            $summary['ship_located'] = 'n';
            if ('' != $summary['ship_locate_date'] && '0000-00-00 00:00:00' != $summary['ship_locate_date']) {
                $summary['ship_located'] = 'y';
            }

            $summary['payments_located'] = 'n';
            if ('' != $summary['payments_locate_date'] && '0000-00-00 00:00:00' != $summary['payments_locate_date']) {
                $summary['payments_located'] = 'y';
            }

            $summary['supply_located'] = 'n';
            if ('' != $summary['supply_locate_date'] && '0000-00-00 00:00:00' != $summary['supply_locate_date']) {
                $summary['supply_located'] = 'y';
            }

        }
        $summary["invoice_number"] = purchaseOrderFormatNumber($summary["order_id"], $summary["revision_id"], $summary["site_id"]);
        if (array_key_exists('inquiry_id', $summary)) {
            $summary["invoice_number"] = sprintf("Q#%06d", $summary["inquiry_id"]);
        }

        return $summary;
    }
}
if (!function_exists('purchaseorderFromPost')) {
    function purchaseorderFromPost($posted)
    {
        global $DB, $_GLOBALS;
        //Hack since invoice data is not seperated into posted invoice array
        if (is_array($posted['order'])) {
            foreach ($posted['order'] as $key => $value) {
                $posted[$key] = $value;
            }
        }

        $order_id = $posted['order_id'];
        $old_vals   = purchaseOrderFromDatabase($order_id);
        $new_vals   = $old_vals;
        foreach ($old_vals as $key => $value) {
            if (array_key_exists($key, $posted)) {
                $new_vals[$key] = $posted[$key];
            }
        }

        if ($new_vals['handler_note'] !== $old_vals['handler_note'] || $new_vals['handler_status'] !== $old_vals['handler_status']) {
            $new_vals['handler_date'] = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);
        }

        if (is_array($posted['proforma_comments'])) {
            $new_vals['proforma_comments'] = implode(', ', $posted['proforma_comments']);
        }

        $country_list = getCountryList(false);
        $bill_array   = array(
            ('' != $new_vals['bill_company']) ? (trim($new_vals['bill_company'])) : (trim($new_vals['bill_name'])),
            ('' != $new_vals['bill_company'] && '' != $new_vals['bill_name']) ? ('Attn: ' . $new_vals['bill_name']) : '',
            trim($new_vals['bill_address']),
            ltrim(trim($new_vals['bill_city'] . ', ' . $new_vals['bill_state'] . ' ' . $new_vals['bill_postcode']), ','),
            trim($country_list[$new_vals['bill_country']]),
            "\r", //For some reason I can't just put a space here
            ('' != $new_vals['bill_vat']) ? ('VAT: ' . $new_vals['bill_vat']) : '',
            ('' != $new_vals['bill_phone']) ? ('Ph: ' . $new_vals['bill_phone']) : '',
            ('' != $new_vals['bill_email']) ? ('Email: ' . $new_vals['bill_email']) : '',
        );
        foreach ($bill_array as $key => $value) {
            if ('' == $value) {
                unset($bill_array[$key]);
            }
        }

        $new_vals['bill_address_full'] = implode("\n", $bill_array);
        if ('n' == $old_vals['products_located'] && 'y' == $new_vals['products_located']) {
            $new_vals['products_locate_date'] = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);
            $new_vals['products_staff_id']    = $_GLOBALS['staff']['id'];
            $new_vals['backorder_flag']       = 'n';
        }
        if ('n' == $old_vals['ship_located'] && 'y' == $new_vals['ship_located']) {
            $new_vals['ship_locate_date'] = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);
            $new_vals['ship_staff_id']    = $_GLOBALS['staff']['id'];
        }
        if ('n' == $old_vals['payments_located'] && 'y' == $new_vals['payments_located']) {
            $new_vals['payments_locate_date'] = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);
            $new_vals['payments_staff_id']    = $_GLOBALS['staff']['id'];
            if ('payment' == $old_vals['invoice_action']) {
                $new_vals['invoice_action'] = 'shipping';
            }

        }
        if ('n' == $old_vals['supply_located'] && 'y' == $new_vals['supply_located']) {
            $new_vals['supply_locate_date'] = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);
            $new_vals['supply_staff_id']    = $_GLOBALS['staff']['id'];
        }
        if ('n' == $new_vals['products_located']) {
            $new_vals['products_staff_id'] = 0;
        }

        if ('n' == $new_vals['ship_located']) {
            $new_vals['ship_staff_id'] = 0;
        }

        if ('n' == $new_vals['payments_located']) {
            $new_vals['payments_staff_id'] = 0;
        }

        if ('n' == $new_vals['supply_located']) {
            $new_vals['supply_staff_id'] = 0;
        }

        if ($invoice_id <= 0) {
            $new_vals['open_time']     = $_SERVER['REQUEST_TIME'];
            $new_vals['open_date']     = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);
            $new_vals['proforma_date'] = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);
            $new_vals['days_open']     = 0;
            $new_vals['time_open']     = 0;
        }
        $array = array('products_offer', 'products_cost', 'ship_offer', 'ship_cost', 'charges_offer', 'charges_cost', 'invoice_offer', 'invoice_cost');
        foreach ($array as $key1) {
            $key2 = $key1 . 'au';
            if (!array_key_exists($key1, $posted) && array_key_exists($key2, $posted)) {
                $new_vals[$key1] = convertCurrency($posted[$key2], 'AUD', $new_vals['invoice_currency']);
            }

            if (!array_key_exists($key2, $posted) && array_key_exists($key1, $posted)) {
                $new_vals[$key2] = convertCurrency($posted[$key1], $new_vals['invoice_currency'], 'AUD');
            }

            if (0 == $posted[$key1] && 0 != $posted[$key2]) {
                $new_vals[$key1] = convertCurrency($posted[$key2], 'AUD', $new_vals['invoice_currency']);
            }

            if (0 == $posted[$key2] && 0 != $posted[$key1]) {
                $new_vals[$key2] = convertCurrency($posted[$key1], $new_vals['invoice_currency'], 'AUD');
            }

            $key2 = $key1 . 'us';
            if (!array_key_exists($key1, $posted) && array_key_exists($key2, $posted)) {
                $new_vals[$key1] = convertCurrency($posted[$key2], 'USD', $new_vals['invoice_currency']);
            }

            if (!array_key_exists($key2, $posted) && array_key_exists($key1, $posted)) {
                $new_vals[$key2] = convertCurrency($posted[$key1], $new_vals['invoice_currency'], 'USD');
            }

            if (0 == $posted[$key1] && 0 != $posted[$key2]) {
                $new_vals[$key1] = convertCurrency($posted[$key2], 'USD', $new_vals['invoice_currency']);
            }

            if (0 == $posted[$key2] && 0 != $posted[$key1]) {
                $new_vals[$key2] = convertCurrency($posted[$key1], $new_vals['invoice_currency'], 'USD');
            }

        }
        //re-calculate costs and offers...
        $new_vals['invoice_cost']     = $new_vals['products_cost'] + $new_vals['ship_cost'] + $new_vals['charges_cost'];
        $new_vals['invoice_costau']   = $new_vals['products_costau'] + $new_vals['ship_costau'] + $new_vals['charges_costau'];
        $new_vals['invoice_costus']   = $new_vals['products_costus'] + $new_vals['ship_costus'] + $new_vals['charges_costus'];
        $new_vals['invoice_offer']    = $new_vals['products_offer'] + $new_vals['ship_offer'] + $new_vals['charges_offer'];
        $new_vals['invoice_offerau']  = $new_vals['products_offerau'] + $new_vals['ship_offerau'] + $new_vals['charges_offerau'];
        $new_vals['invoice_offerus']  = $new_vals['products_offerus'] + $new_vals['ship_offerus'] + $new_vals['charges_offerus'];
        $new_vals['invoice_profit']   = $new_vals['invoice_offer'] - $new_vals['invoice_cost'];
        $new_vals['invoice_profitau'] = $new_vals['invoice_offerau'] - $new_vals['invoice_costau'];
        $new_vals['invoice_profitus'] = $new_vals['invoice_offerus'] - $new_vals['invoice_costus'];

        if ($invoice_id <= 0) {
            unset($new_vals['invoice_id']);
        }

        if (in_array($new_vals['order_status'], array('enquiry', 'inquiry','sales', 'closed'))) {
            if (in_array($posted['order_action'], array('working', 'supplier'))) {
                $new_vals['order_status'] = 'enquiry';
            } elseif (in_array($posted['invoice_action'], array('nointerest', 'nohelp', 'cancelled', 'spam'))) {
                $new_vals['invoice_status'] = 'closed';
            } elseif (in_array($posted['invoice_action'], array('feedback', 'working'))) {
                $new_vals['invoice_status'] = 'enquiry';
            }elseif (in_array($posted['order_action'], array('order', 'order-working', 'supplier-waiting','goods','checking-goods'))) {
                $new_vals['order_status'] = 'sales';
            }elseif (in_array($posted['order_action'], array('processed'))) {
                $new_vals['order_status'] = 'processed';
            }elseif (in_array($posted['order_action'], array('cancelled'))) {
                $new_vals['order_status'] = 'cancelled';
            }

        } elseif (in_array($new_vals['order_status'], array('return', 'completed'))) {
            if (in_array($posted['order_action'], array('return_completed', 'return_cancelled'))) {
                $new_vals['order_status'] = 'completed';
            } else {
                $new_vals['order_status'] = 'return';
            }

        } else {
            if (in_array($posted['order_action'], array('nointerest', 'nohelp', 'cancelled', 'returned', 'spam'))) {
                $new_vals['order_status'] = 'cancelled';
            } elseif (in_array($posted['order_action'], array('processed'))) {
                $new_vals['order_status'] = 'processed';
            } elseif (in_array($posted['order_action'], array('order', 'order-working', 'supplier-waiting','goods','checking-goods'))) {
                $new_vals['order_status'] = 'sales';
            } elseif (in_array($posted['order_action'], array('payment'))) {

                    $new_vals['order_status'] = 'proforma';
               
            }
            if ('shipped' == $posted['order_status'] && 'processed' == $old_vals['order_status']) {
                $new_vals['order_status'] = 'processed';
            }

        }
        if (in_array($old_vals['invoice_status'], array('proforma', 'sales', 'processed')) && in_array($new_vals['invoice_status'], array('proforma', 'sales', 'processed'))) {
            foreach (array('bill_address', 'bill_company', 'bill_country', 'bill_email', 'bill_name', 'charges_offer', 'default_supplier', 'invoice_currency', 'invoice_offer', 'paymenttype_id', 'paymenttype_id', 'products_offer', 'proforma_date', 'ship_address', 'ship_country', 'ship_vat', 'ship_offer', 'terms_id') as $key) {
                if (formatCleanString($new_vals[$key]) != formatCleanString($old_vals[$key])) {
                    $new_vals['do_revision'] = true;
                }
            }
        }
        if(!isset($posted['add_to_inventory'])){
            $new_vals['add_to_inventory'] = 'n';
        }else{
            $new_vals['add_to_inventory'] = 'y';
        }
        return $new_vals;
    }
}
if (!function_exists('purchaseOrderGetLock')) {
    function purchaseOrderGetLock($id)
    {
        global $DB;
        $row = $DB->query_first("SELECT *, UNIX_TIMESTAMP(`expiry_date`) AS `expiry_time` FROM `generic_locks` WHERE `section_name` = 'purchaseorder' AND `generic_id` = " . intval($id) . " AND `expiry_date` > NOW() LIMIT 1", MYSQLI_ASSOC);
        if ($row['expiry_time'] < $_SERVER['REQUEST_TIME']) {
            $row = false;
        }
        return $row;
    }
}
if (!function_exists('purchaseOrderGetOptionList')) {
    function purchaseOrderGetOptionList($table_name)
    {
        static $option_cache = array();
        if (array_key_exists($table_name, $option_cache)) {
            return $option_cache[$table_name];
        }

        global $DB;
        $table       = '';
        $option_list = array();
        switch ($table_name) {
            case 'payments_located':
            case 'products_located':
            case 'ship_located':
            case 'stock_received':
                return array(
                    'y' => 'Yes',
                    'n' => 'No',
                );

            case 'payment_received':
                return array(
                    'y'  => 'Yes',
                    'n'  => 'No',
                    'yp' => 'Deposit',
                );

            case 'handler_status':
                return array(
                    'working'   => 'Working on Purchase Enquiry',
                    'supplier'  => 'COE - Waiting Supplier Reply',
                    'payment'   => 'Waiting Payment',
                    'goods'  => 'Waiting Goods',
                    'checking'   => 'Checking Goods',
                    'proceed'   => 'Processed',
                );

            case 'list_invoice_status':
                $table  = '`invoices` AS `i`';
                $key    = '`i`.`invoice_id`';
                $fields = purchaseOrderGetOptionList("all_fields");
                $value  = $fields[substr($table_name, 5)];
                break;

            case 'invoice_status':
                return array(
                    'enquiry'   => 'Enquiry',
                    'closed'    => 'Closed',
                    'proforma'  => 'Proforma',
                    'sales'     => 'Sales',
                    'processed' => 'Processed',
                    'cancelled' => 'Cancelled',
                );

            case 'invoice_action':
                return array(
                    'working'          => 'Working On Inquiry',
                    'feedback'         => 'Waiting Customer Feedback',
                    'nointerest'       => 'Customer Not Interested',
                    'payment'          => 'Awaiting Payment',
                    'approval'         => 'Awaiting Payment Approval',
                    'shipping'         => 'Awaiting Shipping',
                    'shipped'          => 'Goods Shipped',
                    'processed'        => 'Processed',
                    'cancelled'        => 'Cancelled',
                    'nohelp'           => 'Unable to help Customer',
                    'returned'         => 'Returned',
                    'sent_factory'     => 'Goods sent to Factory',
                    'prepare_factory'  => 'Goods preparing to send to Factory',
                    'prepare_customer' => 'Goods preparing to send to Customer',
                    'sent_customer'    => 'Goods sent to Customer',
                    'return_completed' => 'Processed',
                    'return_cancelled' => 'Cancelled',
                );
            case 'ship_insurance':
            case 'ship_tailgate':
                return array('n' => 'No', 'y' => 'Yes');
            case 'airport_from':
            case 'airport_to':
                $table = '`world`.`airports`';
                $key   = 'id';
                $value = 'name';
                break;

            case 'invoice_prefix':
                $table = '`company_info`';
                $key   = '`company_id`';
                $value = 'LEFT(`company_invoicesuffix`, 2)';
                break;

            case 'seaport_from':
            case 'seaport_to':
                $table = '`world`.`seaports`';
                $key   = 'id';
                $value = 'name';
                break;

            case 'site_id':
                $table = '`company_info` ORDER BY `company_name` ASC';
                $key   = 'company_id';
                $value = 'company_name';
                break;

            case 'charge_id':
                $table = '`charges`';
                $key   = 'charge_id';
                $value = 'charge_name';
                break;

            case 'bill_timezone':
                $table = '`world`.`time_zones`';
                $key   = 'id';
                $value = 'name';
                break;

            case 'payment_options':
                return array(
                    0x0001 => 'Payment via Visa',
                    0x0002 => 'Payment via Mastercard',
                    0x0003 => 'Payment via Amex',
                    /*
                    0x0004 => 'Payment via AUD Bank Account',
                    0x0005 => 'Payment via EUR Bank Account',
                    0x0006 => 'Payment via GBP Bank Account',
                     */
                    0x0007 => 'Payment via HKD Bank Account',
                    /*
                    0x0008 => 'Payment via JPY Bank Account',
                     */
                    0x0009 => 'Payment via USD Bank Account',
                    /*
                    0x0203 => 'Payment via HE AUD Bank Account',
                    0x0103 => 'Payment via HG AUD Bank Account',
                    0x0108 => 'Payment via HG USD Bank Account',
                    0x0104 => 'Payment via HG EUR Bank Account',
                    0x0105 => 'Payment via HG GBP Bank Account',
                    0x2006 => 'Payment via ASP HKD Bank Account',
                    0x2007 => 'Payment via ASP JPY Bank Account',
                    0x2008 => 'Payment via ASP USD Bank Account',
                    0x0903 => 'Payment via CNV AUD Bank Account',
                    0x1103 => 'Payment via HGR AUD Bank Account',
                     */
                    0x0009 => 'Cash Payment',
                    /*
                    0x000A => 'Payment via Cheque',
                    0x000B => 'Payment via BPay',
                    0x000C => 'Payment via Amazon',
                     */
                    0x000D => 'Payment via PayPal',
                    /*
                    0x200D => 'Payment via ASP PayPal',
                    0x110D => 'Payment via HGR PayPal',
                     */
                    0x000E => 'Shortpayment Writeoff',
                    0x000F => 'Payment via Trade Account',
                    /*
                    0x0010 => 'Payment via BankWest Bank Account',
                    0x0011 => 'Payment via Commonwealth Bank Account',
                     */
                    0x0012 => 'Refund',
                );

            case 'payment_terms_id':
                $table = '`invoice_payment_terms`';
                $key   = 'id';
                $value = 'title';
                break;

            case 'invoice_suffix':
                $table = '`company_info`';
                $key   = 'company_id';
                $value = 'company_invoicesuffix';
                break;

            case 'harmonised_code':
                $table = '`world`.`harmonised_codes`';
                $key   = 'code';
                $value = 'title';
                break;

            case 'default_supplier':
            case 'supplier_id':
                return array();
                supplierGetOptionList("id");
                break;

            case 'ship_id':
            case 'ship_supplier_id':
                //TODO
                break;
             case 'supplier':
                $supplier_list = array();
                $query       = $DB->query(sprintf(
                    "SELECT `supplier_id`, `company` FROM `new_suppliers` ORDER BY `company` ASC"
                ));
                while ($row = $DB->fetch_assoc($query)) {
                     $supplier_list[$row['supplier_id']] = $row['company'];
                }
                $DB->free_result($query);
                return $supplier_list;
                break;
            case 'producttype_id':
                $table = '`product_types`';
                $key   = 'id';
                $value = 'IF(`parent` = 0, `description`, `name`)';
                break;

            case 'handler_id':
            case 'staff_id':
            case 'products_staff_id':
            case 'ship_staff_id':
            case 'payments_staff_id':
                return getStaffList();

            case 'bill_country':
            case 'country_from':
            case 'country_to':
            case 'def_country':
            case 'forwarder_country':
            case 'origin_country':
            case 'ship_country':
            case 'supply_country':
                return getCountryList();

            case 'def_warranty':
            case 'warranty_terms_id':
            case 'waranty_terms_id':
            case "product_warranty":
                return getWarantyTermsList();
            case 'def_currency':
            case 'invoice_currency':
                return getCurrencyList();
            case 'paymenttype_id':
                return getPaymentMethodsList();
            case 'def_terms':
            case 'terms_id':
                return getTermsList();
            case 'def_voltage':
            case 'voltage_id':
            case 'voltage_id2':
                return getInvoiceVoltages();
            case 'frequency_id':
                return getOutletFrequencies();
            case 'def_plug':
            case 'plug_id':
                return getOutletPlugs();
            case 'def_packing':
            case 'packing_requirement':
            case 'pack_requirement_id':
                return invoicePackingRequirementList();
            case 'condition':
            case 'def_condition':
                return getProductCondition();
            case 'joined_fields':
                $joins = array('INNER JOIN `invoice_payments` AS `ip` ON `ip`.`invoice_id` = `i`.`invoice_id`' => getTableColumns('`invoice_payments`'));
                foreach (getTableColumns('`invoices`') as $field) {
                    foreach ($joins as $join => $rows) {
                        unset($joins[$join][$field]);
                    }
                }

                return $joins;
            case 'all_fields':
                return array_merge(purchaseOrderGetOptionList('base_fields'), purchaseOrderGetOptionList('ext_fields'));
            case 'all_fields_types':
                return array_merge(purchaseOrderGetOptionList('base_fields_types'), purchaseOrderGetOptionList('ext_fields_types'));
            case 'base_fields':
                $fields = array();
                foreach (getTableColumns('`invoices`') as $key) {
                    $fields[$key] = '`i`.`' . $key . '`';
                }

                return $fields;
            case 'base_fields_types':
                return getTableFields('`invoices`');
            case 'extra_fields':
            case 'ext_fields':
                return array(
                    //'invoice_id' => "i.id",
                    'id'                   => '`order_id`',
                    'payments_located'     => "IF(payments_locate_date IS NULL OR payments_locate_date = '0000-00-00 00:00:00','n','y')",
                    'open_year'            => "IF(open_date=0,0,UNIX_TIMESTAMP(DATE_FORMAT(open_date,'%Y-01-01 00:00:00')))",
                    'open_quarter'         => "IF(open_date=0,0,UNIX_TIMESTAMP(CONCAT_WS('',DATE_FORMAT(open_date,'%Y-'),LPAD(QUARTER(open_date)*3-2,2,'0'),'-01 00:00:00')))",
                    'open_month'           => "IF(open_date=0,0,UNIX_TIMESTAMP(DATE_FORMAT(open_date,'%Y-%m-01 00:00:00')))",
                    'open_week'            => "IF(open_date=0,0,UNIX_TIMESTAMP(DATE_FORMAT(DATE_SUB(open_date, INTERVAL WEEKDAY(open_date) DAY),'%Y-%m-%d 00:00:00')))",
                    'open_day'             => "IF(open_date=0,0,UNIX_TIMESTAMP(DATE_FORMAT(open_date,'%Y-%m-%d 00:00:00')))",
                    'open_time'            => "UNIX_TIMESTAMP(open_date)",

                    'tax_time'             => "UNIX_TIMESTAMP(`tax_date`)",
                    'tax_fyear'            => "IF (`tax_date` IS NULL OR `tax_date` = '0000-00-00', 0, UNIX_TIMESTAMP(CONCAT(YEAR(`tax_date`) - IF(MONTH(`tax_date`) > 6, 0, 1), '-07-01 00:00:00')))",

                    'delivery_time'        => 'UNIX_TIMESTAMP(delivery_date)',
                    'payments_locate_time' => 'UNIX_TIMESTAMP(`payments_locate_date`)',
                    'supply_locate_time'   => 'UNIX_TIMESTAMP(`supply_locate_date`)',
                    'needby_time'          => 'UNIX_TIMESTAMP(`needby_date`)',

                );
            case 'ext_fields_types':
                return array();
            case 'inquiry_fields':
                return array(
                    'id'                   => 'inquiry_id',
                    "bill_address"         => "customer_address",
                    "bill_company"         => "customer_company",
                    "bill_country"         => "customer_country",
                    "bill_email"           => "customer_email",
                    "bill_name"            => "customer_name",
                    "bill_timezone"        => "customer_timezone",
                    "handler_id"           => "staff_handler",
                    "invoice_currency"     => "inquiry_currency",
                    "invoice_id"           => "inquiry_id",
                    "invoice_offerus"      => "inquiry_value",
                    "invoice_profitus"     => "inquiry_profit",
                    "member_id"            => "customer_id",
                    "packing_string"       => "packing_requirement",
                    "products_cost"        => "product_cost_total",
                    "products_costus"      => "product_costus_total",
                    "products_locate_date" => "product_locate_date",
                    "products_offer"       => "product_offer_total",
                    "products_offerus"     => "product_offerus_total",
                    "products_staff_id"    => "product_staff_id",
                    "proforma_tracknumber" => "tracking_number",
                    "ship_address"         => "shipping_address",
                    "ship_cost"            => "freight_cost",
                    "ship_costus"          => "freight_costus",
                    "ship_id"              => "courier_id",
                    "ship_insurance"       => "terms_insurance",
                    "ship_locate_date"     => "freight_locate_date",
                    "ship_name"            => "freight_name",
                    "ship_needs"           => "freight_requests",
                    "ship_offer"           => "freight_offer",
                    "ship_offerus"         => "freight_offerus",
                    "ship_product_name"    => "freight_name",
                    "ship_product_notes"   => "freight_notes",
                    "ship_staff_id"        => "freight_staff_id",
                    "ship_supplier_id"     => "freight_supplier_id",
                    "ship_weight"          => "freight_weight",
                    "supply_country"       => "supplier_country",
                    "supply_notes"         => "supplier_notes",
                    "site_id"              => "3",

                    "invoice_cost"         => "`freight_cost` + `product_cost_total`",
                    "invoice_offer"        => "IF(ISNULL(`freight_offer`), 0, `freight_offer`) + `product_offer_total`",
                    "invoice_costus"       => "`freight_costus` + `product_costus_total`",
                    "invoice_profit"       => "(IF(ISNULL(`freight_offer`), 0, `freight_offer`) + `product_offer_total`) - (`freight_cost` + `product_cost_total`)",

                    "invoice_status"       => "IF(`i`.`inquiry_status` = 'Open', 'enquiry', IF(`i`.`inquiry_status` = 'Proforma', 'proforma', IF(`i`.`inquiry_status` = 'Closed', 'cancelled', IF(`i`.`inquiry_status` = 'Processed', 'processed', 'enquiry'))))",
                    "invoice_action"       => "IF(`i`.`inquiry_status` = 'Open', 'working', IF(`i`.`inquiry_status` = 'Proforma', 'approval', IF(`i`.`inquiry_status` = 'Closed', 'shipped', IF(`i`.`inquiry_status` = 'Processed', 'processed', 'working'))))",

                    'products_located'     => "IF(`product_locate_date` IS NULL, 'n', 'y')",
                    'ship_located'         => "IF(`freight_locate_date` IS NULL, 'n', 'y')",
                    'open_year'            => "IF(`open_date` = 0, 0, UNIX_TIMESTAMP(DATE_FORMAT(`open_date`, '%Y-01-01 00:00:00')))",
                    'open_month'           => "IF(`open_date` = 0, 0, UNIX_TIMESTAMP(DATE_FORMAT(`open_date`, '%Y-%m-01 00:00:00')))",
                    'open_week'            => "IF(`open_date` = 0, 0, UNIX_TIMESTAMP(DATE_FORMAT(DATE_SUB(`open_date`, INTERVAL WEEKDAY(`open_date`) DAY),'%Y-%m-%d 00:00:00')))",
                    'open_time'            => "UNIX_TIMESTAMP(`open_date`)",

                    'products_located'     => "IF(`product_locate_date` IS NULL OR `product_locate_date` = '0000-00-00 00:00:00', 'n', 'y')",
                    'ship_located'         => "IF(`freight_locate_date` IS NULL OR `freight_locate_date` = '0000-00-00 00:00:00', 'n', 'y')",
                    'open_year'            => "IF(`open_date` = 0, 0, UNIX_TIMESTAMP(DATE_FORMAT(`open_date`, '%Y-01-01 00:00:00')))",
                    'open_quarter'         => "IF(`open_date` = 0, 0, UNIX_TIMESTAMP(CONCAT_WS('', DATE_FORMAT(`open_date`, '%Y-'), LPAD(QUARTER(`open_date`) * 3 - 2, 2, '0'), '-01 00:00:00')))",
                    'open_month'           => "IF(`open_date` = 0, 0, UNIX_TIMESTAMP(DATE_FORMAT(`open_date`, '%Y-%m-01 00:00:00')))",
                    'open_week'            => "IF(`open_date` = 0, 0, UNIX_TIMESTAMP(DATE_FORMAT(DATE_SUB(`open_date`, INTERVAL WEEKDAY(`open_date`) DAY), '%Y-%m-%d 00:00:00')))",
                    'open_day'             => "IF(`open_date` = 0, 0, UNIX_TIMESTAMP(DATE_FORMAT(`open_date`, '%Y-%m-%d 00:00:00')))",
                    'proforma_time'        => "UNIX_TIMESTAMP(`proforma_date`)",

                    "payments_located"     => "`i`.`payment_received`",
                    "products_locate_time" => "UNIX_TIMESTAMP(`i`.`product_locate_date`)",
                    "ship_locate_time"     => "UNIX_TIMESTAMP(`i`.`freight_locate_date`)",
                    "supply_locate_time"   => "UNIX_TIMESTAMP(`i`.`product_locate_date`)",
                    "write_time"           => "UNIX_TIMESTAMP(`i`.`write_date`)",

                    "open_date"            => "`i`.`open_date`",
                    "payments_locate_date" => "`i`.`product_locate_date`",
                    "producttype_id"       => "`i`.`producttype_id`",
                    "staff_id"             => "`i`.`staff_id`",
                    "write_date"           => "`i`.`write_date`",

                    "bill_city"            => "''",
                    "bill_phone"           => "''",
                    "bill_postcode"        => "''",
                    "bill_state"           => "''",
                    "bill_vat"             => "''",
                    "notify_address"       => "''",
                    "notify_city"          => "''",
                    "notify_company"       => "''",
                    "notify_country"       => "''",
                    "notify_email"         => "''",
                    "notify_name"          => "''",
                    "notify_phone"         => "''",
                    "notify_postcode"      => "''",
                    "notify_state"         => "''",
                    "notify_vat"           => "''",
                    "ship_city"            => "''",
                    "ship_company"         => "''",
                    "ship_country"         => "''",
                    "ship_email"           => "''",
                    "ship_phone"           => "''",
                    "ship_postcode"        => "''",
                    "ship_state"           => "''",
                    "ship_vat"             => "''",
                    "supply_locate_date"   => "'0000-00-00 00:00:00'",

                );
        }
        if ('' != $table) {
            $query = $DB->query("SELECT " . $key . ", " . $value . " FROM " . $table);
            while ($row = $DB->fetch_array($query)) {
                $option_list[$row[0]] = $row[1];
            }

            $DB->free_result($query);
        }
        $option_cache[$table_name] = $option_list;
        return $option_list;
    }
}
if (!function_exists('invoiceGetStaffPermissions')) {
    function invoiceGetStaffPermissions($staff_id)
    {
        global $DB;
        $result = array();
        $query  = $DB->query("SELECT `section_id` FROM `invoice_permissions` WHERE `staff_id` = " . intval($staff_id));
        while ($row = $DB->fetch_assoc($query)) {
            $result[] = $row['section_id'];
        }

        $DB->free_result($query);
        return $result;
    }
}
if (!function_exists('purchaseOrderGetValue')) {
    function purchaseOrderGetValue($invoice_id, $field)
    {
        if (is_array($invoice_id)) {
            $result = array();
            foreach ($invoice_id as $k) {
                $result[$k] = purchaseOrderGetValue($k, $field);
            }

            return $result;
        }
        global $DB;
        $fields = getTableColumns('`purchase_order`');
        foreach ($fields as $key => $value) {
            $fields[$key] = '`i`.`' . $value . '`';
        }

        $fields = array_merge($fields, purchaseOrderGetOptionList('ext_fields'));
        if (array_key_exists($field, $fields)) {
            $row = $DB->query_first("SELECT " . $fields[$field] . " AS " . $field . " FROM `purchase_order` AS `i` WHERE `i`.`order_id` = " . intval($invoice_id));
            if (is_array($row)) {
                return $row[$field];
            }

        }

        if ("invoice_number" == $field) {
            return purchaseOrderFormatNumber($invoice_id, purchaseOrderGetValue($invoice_id, "revision_id"), purchaseOrderGetValue($invoice_id, "site_id"));
        }

        return null;
    }
}
if (!function_exists('invoiceGroupCount')) {
    function invoiceGroupCount($item_list, $group_by = '', $split_sets = true)
    {
        global $DB;
        if (!is_array($item_list) || count($item_list) < 1) {
            return $item_list;
        }

        if ('' === $group_by) {
            return count($item_list);
        }

        if (!is_array($group_by)) {
            $group_by = array($group_by);
        }

        if (count($group_by) == 0) {
            return count($item_list);
        }

        $join_list   = array();
        $field_list  = array('row_count' => 'COUNT(*) AS `row_count`');
        $field_types = purchaseOrderGetOptionList('all_fields_types');
        $fields      = purchaseOrderGetOptionList('all_fields');
        foreach ($group_by as $field) {
            if (array_key_exists($field, $fields)) {
                $field_list[$field] = $fields[$field] . " AS `" . $field . "`";
            }
        }

        foreach (purchaseOrderGetOptionList('joined_fields') as $join => $fields) {
            foreach ($fields as $field => $field_string) {
                if (in_array($field, $group_by)) {
                    $join_list[$join]       = $join;
                    $field_list[$field]     = $fields[$field] . " AS `" . $field . "`";
                    $field_types[$field][1] = 'set(...)';
                }
            }
        }

        $field_list = array_unique($field_list);
        $group_list = $field_list;
        unset($group_list['row_count']);
        if (count($group_list) == 0) {
            return array(count($item_list));
        }

        $query   = $DB->query("SELECT " . implode(', ', $field_list) . " FROM `invoices` AS `i` " . implode(' ', $join_list) . " WHERE `i`.`invoice_id` IN(" . implode(', ', $item_list) . ") GROUP BY `" . implode('`, `', array_keys($group_list)) . "`");
        $results = array();
        while ($row = $DB->fetch_assoc($query)) {
            $pointers = array(&$results);
            foreach ($group_by as $group) {
                $key = $row[$group];
                if ('' == $key) {
                    $key = null;
                }

                $new_pointers = array();
                foreach ((substr($field_types[$group][1], 0, 4) == 'set(' && $split_sets) ? explode(',', $key) : array($key) as $key2) {
                    foreach ($pointers as $key1 => $pointer) {
                        if (!isset($pointers[$key1][$key2])) {
                            $pointers[$key1][$key2] = array();
                        }

                        $new_pointers[] = &$pointers[$key1][$key2];
                    }
                }
                $pointers = $new_pointers;
            }
            foreach ($pointers as $pointer_key => $pointer) {
                if (is_array($pointers[$pointer_key])) {
                    $pointers[$pointer_key] = 0;
                }

                $pointers[$pointer_key] += $row['row_count'];
            }
        }
        $DB->free_result($query);
        return $results;
    }
}
if (!function_exists('purchaseOrderGroupCount')) {
    function purchaseOrderGroupCount($item_list, $group_by = '', $split_sets = true)
    {
        global $DB;
        if (!is_array($item_list) || count($item_list) < 1) {
            return $item_list;
        }

        if ('' === $group_by) {
            return count($item_list);
        }

        if (!is_array($group_by)) {
            $group_by = array($group_by);
        }

        if (count($group_by) == 0) {
            return count($item_list);
        }

        $join_list   = array();
        $field_list  = array('row_count' => 'COUNT(*) AS `row_count`');
        $field_types = purchaseOrderGetOptionList('all_fields_types');
        $fields      = purchaseOrderGetOptionList('all_fields');
        foreach ($group_by as $field) {
            if (array_key_exists($field, $fields)) {
                $field_list[$field] = $fields[$field] . " AS `" . $field . "`";
            }
        }

        foreach (purchaseOrderGetOptionList('joined_fields') as $join => $fields) {
            foreach ($fields as $field => $field_string) {
                if (in_array($field, $group_by)) {
                    $join_list[$join]       = $join;
                    $field_list[$field]     = $fields[$field] . " AS `" . $field . "`";
                    $field_types[$field][1] = 'set(...)';
                }
            }
        }

        $field_list = array_unique($field_list);
        $group_list = $field_list;
        unset($group_list['row_count']);
        if (count($group_list) == 0) {
            return array(count($item_list));
        }
        $query   = $DB->query("SELECT " . implode(', ', $field_list) . " FROM `purchase_order` AS `i` " . implode(' ', $join_list) . " WHERE `i`.`order_id` IN(" . implode(', ', $item_list) . ") GROUP BY `" . implode('`, `', array_keys($group_list)) . "`");
        $results = array();
        while ($row = $DB->fetch_assoc($query)) {
            $pointers = array(&$results);
            foreach ($group_by as $group) {
                $key = $row[$group];
                if ('' == $key) {
                    $key = null;
                }

                $new_pointers = array();
                foreach ((substr($field_types[$group][1], 0, 4) == 'set(' && $split_sets) ? explode(',', $key) : array($key) as $key2) {
                    foreach ($pointers as $key1 => $pointer) {
                        if (!isset($pointers[$key1][$key2])) {
                            $pointers[$key1][$key2] = array();
                        }

                        $new_pointers[] = &$pointers[$key1][$key2];
                    }
                }
                $pointers = $new_pointers;
            }
            foreach ($pointers as $pointer_key => $pointer) {
                if (is_array($pointers[$pointer_key])) {
                    $pointers[$pointer_key] = 0;
                }

                $pointers[$pointer_key] += $row['row_count'];
            }
        }
        $DB->free_result($query);
        return $results;
    }
}
if (!function_exists('invoiceHasValue')) {
    function invoiceHasValue($invoice_id, $field, $value)
    {
        return (purchaseOrderGetValue($invoice_id, $field) == $value);
    }
}
if (!function_exists('invoiceHistoryEmailsFromDatabase')) {
    function invoiceHistoryEmailsFromDatabase($invoice_id)
    {
        // This function generates the email list for online use.
        $invoice_history = invoiceHistorySearch(array('invoice_id' => $invoice_id, 'message_type_id' => array(INVOICE_MESSAGE_TO_CUSTOMER, INVOICE_MESSAGE_FROM_CUSTOMER, INVOICE_MESSAGE_INVOICE_SENT)));
        $handler_id      = purchaseOrderGetValue($invoice_id, 'handler_id');
        $email_list      = array();
        if ($handler_id > 0) {
            $email_list[$handler_id] = getStaffName($handler_id);
        }

        foreach ($invoice_history as $history_id) {
            $history = purchaseOrderHistoryFromDatabase($history_id);
            if (is_numeric($history['message_sender'])) {
                $email_list[$history['message_sender']] = getStaffName(intval($history['message_sender']));
            } else {
                $staff_id = getStaffIDFromName($history['message_sender']);
                if ($staff_id > 0) {
                    $email_list[$staff_id] = $history['message_sender'];
                }

            }
        }
        return array_unique($email_list);
    }
}
if (!function_exists('purchaseOrderHistoryFromDatabase')) {
    function purchaseOrderHistoryFromDatabase($message_id)
    {
        global $DB;
        return $DB->query_first(sprintf("SELECT `imt`.*, `im`.*, UNIX_TIMESTAMP(`im`.`message_date`) AS `message_time` FROM `purchase_order_messages` AS `im` INNER JOIN `highwaygroup`.`invoice_message_types` AS `imt` ON `imt`.`message_type_id` = `im`.`message_type_id` WHERE `im`.`message_id` = %d LIMIT 1", $message_id), MYSQLI_ASSOC);
    }
}
if (!function_exists('purchaseOrderHistorySearch')) {
    function purchaseOrderHistorySearch($criteria)
    {
        global $DB;
        $results = array();
        $joins   = array();
        $fields  = getTableFields("`purchase_order_messages`");
        $fields2 = array();
        foreach (getTableFields("`purchase_order`") as $k => $v) {
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
                    $joins['invoice'] = "INNER JOIN `purchase_order` AS `i` ON `i`.`order_id` = `im`.`order_id`";
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
            $whereAND[] = "`im`.`order_id` = " . formatSQLString($criteria);
        }

        $search_for = array();
        if (array_key_exists('any_text', $criteria)) {
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
                    $whereAND[] = " (" . implode(" OR ", $whereOR) . ")";
                }

            }
        }
        $query = $DB->query(sprintf(
            "SELECT `im`.`message_id` FROM `purchase_order_messages` AS `im`%s%s ORDER BY `im`.`message_id` DESC",
            (count($joins)) ? implode(" ", $joins) : "",
            (count($whereAND)) ? (" WHERE " . implode(" AND ", $whereAND)) : ""
        ));
        while ($row = $DB->fetch_array($query, MYSQLI_ASSOC)) {
            $results[] = $row['message_id'];
        }

        $DB->free_result($query);
        return $results;
    }
}

if (!function_exists('getProductForSupplier')) {
    function getProductForSupplier($ids)
    {
        global $DB;
        $result=[];
        $sql    = " SELECT";
        $sql .= " sp.id as supplier_product_id,sp.supplier_id," .
        " sp.sup_currency," .
        " sp.sup_code as supplier_sup_code," .
        " sp.sup_name as supplier_sup_name," .
        "   p.*," .
        "   p.cost_price as buy_price," .
        "  pt.parent AS producttype_id," .
        "   p.cost_currency as currency," .
        "   CONCAT(s.name, ' ' , s.surname) as supplier_name," .
        "   '' as supplier_name_ch," .
        "   s.country as location" .
        " FROM supplier_product sp" .
        " INNER JOIN new_suppliers s ON s.supplier_id=sp.supplier_id" .
        " INNER JOIN products p ON p.id=sp.product_id" .
        " INNER JOIN product_types pt ON p.type=pt.id".
        " WHERE FIND_IN_SET(sp.id, '".$ids."')";
        $q = $DB->query($sql);
        $currency='USD';
        while ($product = $DB->fetch_array($q, MYSQLI_ASSOC)) {
            if ('y' == $product['price_flux']) {
                $product['sales_price']    = ($product['markup'] + 100.0) * $product['cost_price'] / 100.0;
                $product['sales_currency'] = $product['cost_currency'];
            }
            $return_data['producttype_id'] = $product['producttype_id'];

            $return_data['product_id']         = $product['id'];
            $return_data['product_name']       = stripslashes($product['product_name']);
            $return_data['product_qty']        = 1;
            $return_data['product_cost']       = convertCurrency($product['cost_price'], $product['cost_currency'], $currency);
            $return_data['product_costus']     = convertCurrency($product['cost_price'], $product['cost_currency'], 'USD');
            $return_data['product_online']     = convertCurrency($product['sales_price'], $product['sales_currency'], $currency);
            $return_data['product_offerus']    = convertCurrency($product['sales_price'], $product['sales_currency'], 'USD');
            $return_data['product_offer']      = $return_data['product_online'];
            $return_data['product_weight']     = convertWeight($product['weight']);
            $return_data['product_weight_inc'] = $product['weight_inc'];
            $return_data['product_notes']      = trim($product['notes']);
            $return_data['min_order']          = $product['min_order'];
            if ($product['min_order'] > 1) {
                $return_data['product_notes'] = trim($return_data['product_notes'] . "\n- unit price in $" . convertCurrency($product['sales_price'], $product['sales_currency'], $currency) . strtolower($currency) . ' for MOQ in ' . $product['min_order'] . ' pieces');
            } else {
                $return_data['min_order'] = 1;
            }

            $return_data['supplier_productid']   = $product['id'];
            $return_data['supplier_qty']         = 1;
            $return_data['supplier_cost']        = $return_data['product_cost'];
            $return_data['supplier_productname'] = $return_data['product_name'];
            $return_data['supplier_id']             = $product['supplier_id'];
            
            $return_data['product_supplier_id']    = $product['supplier_id'];
            $return_data['supplier_costus']         = convertCurrency($product['buy_price'], $product['currency'], 'USD');
            $return_data['supplier_name']           = $product['supplier_name'] . ' ' . $supplier['supplier_name_ch'];
            $return_data['supplier_country']        = $product['location'];
            $return_data['supplier_notes']          = '';
            $return_data['supplier_cost_orig']      = $product['buy_price'];
            $return_data['sup_code']      = $product['supplier_sup_code'];
            $return_data['product_name']      = $product['supplier_sup_name'];
            $return_data['supplier_cost_orig_curr'] = $product['currency'];
            $result[] = $return_data;
        }
        return $result;
    }
}
if (!function_exists('purchaseordereHistoryToDatabase')) {
    function purchaseordereHistoryToDatabase($order_id, $history_info)
    {
        global $DB;
        $DB->query(sprintf(
            "INSERT INTO `purchase_order_messages` (`order_id`, `message_type_id`, `message_date`, `message_sender`, `message_sentto`, `message_title`, `message_notes`, `message_raw`) VALUES (%d, %d, %s, %s, %s, %s, %s, %s)",
            $order_id,
            $history_info["message_type_id"],
            formatSQLDateTime($_SERVER["REQUEST_TIME"]),
            formatSQLString($history_info["message_sender"]),
            formatSQLString($history_info["message_sentto"]),
            formatSQLString($history_info["message_title"]),
            formatSQLString($history_info["message_notes"]),
            formatSQLString($history_info["message_raw"])
        ));
    }
}
if (!function_exists('purchaseOrderJobFromDatabase')) {
    function purchaseOrderJobFromDatabase($job_id)
    {
        global $DB;
        return $DB->query_first("SELECT * FROM `purchase_order_jobs` WHERE `job_id` = '" . $job_id . "' LIMIT 1", MYSQLI_ASSOC);
    }
}
if (!function_exists('purchaseOrderJobToDatabase')) {
    function purchaseOrderJobToDatabase($invoice_id, $job_info)
    {
        global $DB;
        $DB->query("INSERT INTO `purchase_order_jobs` SET `invoice_id` = " . intval($invoice_id) . ", `job_type` = " . intval($job_info['job_type']) . ", `job_content` = " . formatSQLString($job_info['job_content']));
    }
}
if (!function_exists('purchaseOrderJobsFromDatabase')) {
    function purchaseOrderJobsFromDatabase($invoice_id)
    {
        global $DB;

        $results = array();
        $query   = $DB->query("SELECT `job_id` FROM `purchase_order_jobs` WHERE `order_id` = " . intval($invoice_id) . " ORDER BY `job_id` ASC");
        while ($row = $DB->fetch_assoc($query)) {
            $results[] = $row['job_id'];
        }

        $DB->free_result($query);

        return $results;
    }
}
if (!function_exists('invoicePackingRequirementList')) {
    function invoicePackingRequirementList()
    {
        global $DB;
        static $results = array();
        if (count($results)) {
            return $results;
        }

        $query = $DB->query("SELECT `id`, `full_name` FROM `inquiry_packing_requirement` ORDER BY `full_name` ASC");
        while ($row = $DB->fetch_assoc($query)) {
            $results[$row['id']] = $row['full_name'];
        }

        $DB->free_result($query);

        return $results;
    }
}
if (!function_exists('invoicePaymentTemplateVars')) {
    function invoicePaymentTemplateVars($payment)
    {
        $result = array();
        foreach ($payment as $key => $value) {
            $result['[' . strtoupper($key) . ']'] = $value;
        }

        return $result;
    }
}
if (!function_exists('purchaseOrderPaymentToDatabase')) {
    function purchaseOrderPaymentToDatabase($invoice_id, $payment)
    {
        global $DB;
        $invoice_id = intval($invoice_id);
        if (0 == $invoice_id) {
            return;
        }

        unset($payment['payment_count']);
        $payment['order_id'] = $invoice_id;
        if ($payment['payment_time'] > 0) {
            $payment['payment_date'] = date('Y-m-d H:i:s', $payment['payment_time']);
        }

        $insert_vals = array();
        foreach (getTableColumns('`purchase_order_payments`') as $k) {
            if (array_key_exists($k, $payment)) {
                $insert_vals['`' . $k . '`'] = formatSQLString($payment[$k]);
            }
        }

        $DB->query(sprintf(
            "INSERT INTO `purchase_order_payments` (%s) VALUES (%s)",
            implode(', ', array_keys($insert_vals)),
            implode(', ', array_values($insert_vals))
        ));

    }
}
if (!function_exists('invoicePaymentFromDatabase')) {
    function invoicePaymentFromDatabase($invoice_id, $payment_count)
    {
        global $DB;
        $invoice_id    = intval($invoice_id);
        $payment_count = intval($payment_count);
        if (!$invoice_id || !$payment_count) {
            return array();
        }

        return $DB->query_first("SELECT * FROM `invoice_payments` WHERE `invoice_id` = " . $invoice_id . " AND `payment_count` = " . $payment_count);
    }
}
if (!function_exists('purchaseorderPaymentsFromDatabase')) {
    function purchaseorderPaymentsFromDatabase($invoice_id)
    {
        global $DB;
        $invoice_id = intval($invoice_id);
        $sql        = "SELECT `p`.*, UNIX_TIMESTAMP(`p`.`payment_date`) AS `payment_time`, UNIX_TIMESTAMP(`p`.`update_date`) AS `update_time` FROM `purchase_order_payments` AS `p` WHERE `p`.`order_id` = " . $invoice_id;
        $query      = $DB->query($sql);
        $result     = array();
        while ($row = $DB->fetch_assoc($query)) {
            $result[] = $row;
        }

        $DB->free_result($query);
        return $result;
    }
}
if (!function_exists('purchaseOrderProductFormatNumber')) {
    function purchaseOrderProductFormatNumber($product_id, $sub_id)
    {
        return str_pad($product_id, 5, '0', STR_PAD_LEFT) . (($sub_id > 0) ? '-' . str_pad($sub_id, 4, '0', STR_PAD_LEFT) : '');
    }
}
if (!function_exists('purchaseOrderimagesFromDatabase')) {
    function purchaseOrderimagesFromDatabase($order_id=0,$type=0,$product_id=0)
    {
        global $DB;
        $result = array();
        $query =$DB->query("SELECT * FROM `suppliers_invoice_attachment`  WHERE `invoice_id` = " . intval($order_id) . " AND `product_id` = " . intval($product_id) . " AND `image_type` = " . intval($type) . " ORDER BY `id` ASC");
        while ($row = $DB->fetch_assoc($query)) {
            $result[] = $row;
        }

        $DB->free_result($query);
        return $result;
    }
}
if (!function_exists('purchaseOrderProductsFromDatabase')) {
    function purchaseOrderProductsFromDatabase($order_id)
    {
        global $DB;
        $result = array();
        //$invoice_id=mysqli_real_escape_string($invoice_id);
        $query = $DB->query("SELECT `ip`.* FROM `purchase_order_products` AS `ip` WHERE `ip`.`order_id` = " . intval($order_id) . " ORDER BY `product_rank` ASC");
        while ($row = $DB->fetch_assoc($query)) {
            $result[] = $row;
        }

        $DB->free_result($query);

       
        return $result;
    }
}
if (!function_exists('invoiceColourFromDatabase')) {
    function invoiceColourFromDatabase($colour_id)
    {
        global $DB;
        $result = $DB->query_first("SELECT `product_colour`.* FROM `product_colour` WHERE `product_colour`.`id` = " . intval($colour_id));
        return $result;
    }
}
if (!function_exists('purchaseorderProductsFromPost')) {
    function purchaseorderProductsFromPost($posted)
    {
        global $DB;
        $products = array();
        $fields   = array();
        $query    = $DB->query("SHOW COLUMNS FROM `purchase_order_products`");
        while ($row = $DB->fetch_array($query)) {
            if ('auto_increment' != $row[5]) {
                $fields[$row[0]] = $row[4];
            } else {
                $auto = $row[0];
            }
        }

        $status     = 'enquiry';
        $order_id = intval($posted['order_id']);
        if (purchaseOrderExists($order_id)) {
            $products = purchaseOrderProductsFromDatabase($order_id);
            $row      = $DB->query_first("SELECT `order_status` FROM `purchase_order` WHERE `order_id` = " . $order_id, MYSQLI_ASSOC);
            $status   = $row['order_status'];
        }
        if (array_key_exists('product_affected', $posted)) {
            foreach (explode(',', $posted['product_affected']) as $key1) {
                foreach ($products as $key2 => $product) {
                    if ($product['prod_count'] == $key1) {
                        unset($products[$key2]);
                    }
                }
            }
        }
        if (array_key_exists('prod_count', $posted)) {
            foreach ($posted['prod_count'] as $key1) {
                foreach ($products as $key2 => $product) {
                    if ($product['prod_count'] == $key1) {
                        unset($products[$key2]);
                    }
                }
            }
        }
        if (array_key_exists('product_id', $posted)) {
            foreach ($posted['product_id'] as $key1 => $product_id) {
                $product               = $fields;
                $product['order_id'] = $posted['order_id'];
                foreach ($fields as $key2 => $value) {
                    if (array_key_exists($key2, $posted) && is_array($posted[$key2]) && array_key_exists($key1, $posted[$key2])) {
                        $product[$key2] = $posted[$key2][$key1];
                    }
                }
                if(isset($posted['product_image'][$key1])){
                    $product['images']=array_values($posted['product_image'][$key1]);
                }else{
                    $product['images']=[];
                }
                if(isset($posted['product_image_received'][$key1])){
                    $product['receivedimages']=array_values($posted['product_image_received'][$key1]);
                }else{
                    $product['receivedimages']=[];
                }
                $products[] = $product;
            }
        }
        return $products;
    }
}
if (!function_exists('addAndUpdateOrderImage')) {
    function addAndUpdateOrderImage($order_id, $images,$type)
    {
        global $DB;
        $order_id = intval($order_id);
        $DB->query("UPDATE `suppliers_invoice_attachment` SET `invoice_id` = 0,`product_id` = 0,`email_attachment` = 0,`purchase_order_inc` = 0 WHERE `invoice_id` = " . intval($order_id) . " AND `image_type`=".$type."");
          if(count($images)){
            foreach ($images as $key => $image) {
                $email_attachment=(isset($image['email_attachment']) && $image['email_attachment']==1)?1:0;
                $purchase_order_inc=(isset($image['purchase_order_inc']) && $image['purchase_order_inc']==1)?1:0;
                $product_id=(isset($image['product_id']) && $image['product_id']>0)?$image['product_id']:0;
                $sales_invoice_received=(isset($image['sales_invoice_received']) && $image['sales_invoice_received']=='y')?'y':'n';
                $email_attachment_received=(isset($image['email_attachment_received']) && $image['email_attachment_received']=='y')?'y':'n';
 
                $DB->query("UPDATE `suppliers_invoice_attachment` SET `invoice_id` = " .$order_id. ",`email_attachment` = " .$email_attachment. ",`purchase_order_inc` = " .$purchase_order_inc. ",`image_type`=" .$type. ",`product_id` = " .$product_id. ",`sales_invoice_received` = '" .$sales_invoice_received. "',`email_attachment_received` = '" .$email_attachment_received. "' WHERE `id` = " . intval($image['id']) . " LIMIT 1");
                $DB->free_result($query);
            }
        }
    }
}
if (!function_exists('purchaseOrderProductsToDatabase')) {
    function purchaseOrderProductsToDatabase($order_id, $products)
    {
        global $DB;
        $order_id = intval($order_id);
        if (0 == $order_id) {
            return;
        }

        $sql = "DELETE FROM `purchase_order_products` WHERE `order_id` = " . $order_id;
        $DB->query($sql);
        $DB->query("UPDATE `suppliers_invoice_attachment` SET `invoice_id` = 0,`product_id` = 0,`email_attachment` = 0,`purchase_order_inc` = 0 WHERE `invoice_id` = " . intval($order_id) . " AND `image_type`=1");
        $DB->query("UPDATE `suppliers_invoice_attachment` SET `invoice_id` = 0,`product_id` = 0,`email_attachment` = 0,`purchase_order_inc` = 0 WHERE `invoice_id` = " . intval($order_id) . " AND `image_type`=4");
/*        $sql = "DELETE FROM `suppliers_invoice_attachment` WHERE `invoice_id` = ". $order_id." AND image_type=1" ;
        $DB->query($sql);*/
        $fields = array();
        $query  = $DB->query("SHOW COLUMNS FROM `purchase_order_products`");
        while ($row = $DB->fetch_array($query)) {
            if ('auto_increment' != $row[5]) {
                $fields[$row[0]] = $row[4];
            }
        }

        $re_sort = array();
        foreach ($products as $key => $product) {
            $re_sort[$key] = $product['product_rank'];
        }
        asort($re_sort);
        $counter = 1;
        foreach ($re_sort as $key => $rank) {
            $products[$key]['product_rank'] = $counter++;
        }
        foreach ($products as $product) {
            $insert_vals = array();
            if ($order_id > 0) {
                $product['order_id'] = $order_id;
            }

            foreach ($product as $key => $value) {
                if (array_key_exists($key, $fields)) {
                    $insert_vals[$key] = addslashes($value);
                }
            }
            $sql = "INSERT INTO `purchase_order_products` (`" . implode('`, `', array_keys($insert_vals)) . "`) VALUES ('" . implode("', '", $insert_vals) . "')";
            $DB->query($sql);
            $pro_id = $DB->insert_id();
            $DB->free_result($query);
            if(count($product['images'])){
                foreach ($product['images'] as $key => $image) {
                    $email_attachment=(isset($image['email_attachment']) && $image['email_attachment']==1)?1:0;
                    $purchase_order_inc=(isset($image['purchase_order_inc']) && $image['purchase_order_inc']==1)?1:0;
                    if(isset($image['newId']) && $image['newId'] == $insert_vals['product_id']){
                        $file_orig = $image['image_path'];
                        $file_ext  = substr($file_orig, strrpos($file_orig, '.'));
                        if (substr($file_ext, 0, 1) == '.') {
                            $file_ext = substr($file_ext, 1);
                        }
                        $file_new  = "supplier_" . time() . '_' .$image['newId'] . '.' . $file_ext;
                        $sourceFile = $image['image_path']; 
                        $caption = $image['caption'] ?? '';
                        $file_orig = $image['caption'] ?? ''; 
                        $size = 500; 
                        $type = 1;
                        $destinationFile = $_SERVER['DOCUMENT_ROOT'] . '/webadmin/assets/suppliers/'. $file_new; 
                        if (copy($sourceFile, $destinationFile)) {
                             $sql       = "INSERT INTO suppliers_invoice_attachment (`date`, `caption`, `file_orig`, `staff_id`, `filetype`, `size`,`image_type`,`filename`,`invoice_id`,`product_id`,`email_attachment`,`purchase_order_inc`) VALUES ( NOW(), " . formatSQLString($caption) . ", " . formatSQLString($file_orig) . ", " . intval($_GLOBALS['staff']['id']) . ", '$file_ext', $size,$type, " . formatSQLString($file_new) . ",$order_id,$pro_id,$email_attachment,$purchase_order_inc)";
                             $DB->query($sql);
                        }
                    }else{
                        $DB->query("UPDATE `suppliers_invoice_attachment` SET `invoice_id` = " .$order_id. ",`product_id` = " .$pro_id. ",`email_attachment` = " .$email_attachment. ",`purchase_order_inc` = " .$purchase_order_inc. ",`image_type`=1 WHERE `id` = " . intval($image['id']) . " LIMIT 1");
                    }
                 
                    $DB->free_result($query);
                }
            }
            if(count($product['receivedimages'])){
                foreach ($product['receivedimages'] as $key => $image) {
                     $sales_invoice_received=(isset($image['sales_invoice_received']) && $image['sales_invoice_received']=='y')?'y':'n';
                    $email_attachment_received=(isset($image['email_attachment_received']) && $image['email_attachment_received']=='y')?'y':'n';
                    
                    $DB->query("UPDATE `suppliers_invoice_attachment` SET `invoice_id` = " .$order_id. ",`product_id` = " .$pro_id. ",`sales_invoice_received` = '" .$email_attachment. "',`email_attachment_received` = '" .$email_attachment_received. "',`image_type`=4 WHERE `id` = " . intval($image['id']) . " LIMIT 1");
                                $DB->free_result($query);
                }
            }
        }
    }
}
if (!function_exists('purchaseOrderSearch')) {
    function purchaseOrderSearch($criteria = '', $time_from = 0, $time_till = 0)
    {
        global $DB;
        $results = array();
        $joins   = array();
        $fields  = getTableColumns('`purchase_order`');
        foreach ($fields as $k => $v) {
            $fields[$k] = '`i`.`' . $v . '`';
        }
        $fields = array_merge($fields, purchaseOrderGetOptionList('ext_fields'));

        $whereAND = array();
        if (is_array($criteria)) {
            foreach ($criteria as $field => $value) {
                if (array_key_exists($field, $fields)) {
                    if (is_array($value)) {
                        if (count($value)) {
                            if (in_array($field, array('supplier_id'))) {
                                $whereAND[] = '(' . purchaseorderSQLList($value, $fields[$field]) . ')';
                            } else {
                                $whereOR = array();
                                foreach ($value as $sub_value) {
                                    $whereOR[] = $fields[$field] . " = " . formatSQLString($sub_value);
                                }
                                $whereAND[] = "(" . implode(" OR ", $whereOR) . ")";
                            }
                        } else {
                            return array();
                        }
                    } else {
                        $whereAND[] = $fields[$field] . " = " . formatSQLString($value);
                    }
                    unset($fields[$field]);
                }
            }
        } elseif (is_string($criteria) && '' != $criteria) {
            $criteria = array('any_text' => $criteria);
        }

        $search_for = array();
        if (array_key_exists('any_text', $criteria)) {
            if (is_array($criteria['any_text'])) {
                $search_for = $criteria['any_text'];
            } elseif (is_string($criteria['any_text'])) {
                $search_for = explode(' ', $criteria['any_text']);
            }
        }

        if (count($search_for)) {
            $searchBits = array('`i`.`order_id`', '`i`.`bill_name`', '`i`.`bill_company`', '`i`.`bill_email`');

            foreach ($search_for as $findme) {
                $whereOR = array();
                foreach ($searchBits as $key) {
                    if (trim($findme) != '') {
                        $whereOR[] = $key . " LIKE " . formatSQLLikeString($findme);
                    }
                }
                if (count($whereOR) > 0) {
                    $whereAND[] = "(" . implode(" OR ", $whereOR) . ")";
                }
            }
        }

        if ($time_from) {
            $whereAND[] = '`i`.`open_date` > ' . formatSQLDateTime($time_from);
        }
        if ($time_till) {
            $whereAND[] = '`i`.`open_date` < ' . formatSQLDateTime($time_till);
        }
        
        $query = $DB->query(sprintf(
            "SELECT `i`.`order_id` FROM `purchase_order` AS `i`%s%s ORDER BY `i`.`order_id` DESC",
            (count($joins)) ? (" " . implode(" ", $joins)) : "",
            (count($whereAND)) ? (" WHERE " . implode(" AND ", $whereAND)) : ""
        ));
        while ($row = $DB->fetch_assoc($query)) {
            $results[] = $row['order_id'];
        }
        $DB->free_result($query);

        asort($results);

        return $results;
    }
}
if (!function_exists('purchaseorderSetLock')) {
    function purchaseorderSetLock($id, $staff_id = 0, $timeout = 120)
    {
       /* global $DB;
        $section_name = formatSQLString("purchaseorder");
        if (0 != $staff_id) {
            $DB->query(sprintf("UPDATE `generic_locks` SET `expiry_date` = %s, `modified` = NOW() WHERE `section_name` = %s AND `generic_id` = %d AND `staff_id` = %d AND `expiry_date` > NOW() LIMIT 1", formatSQLDateTime($_SERVER["REQUEST_TIME"] + $timeout), $section_name, $id, $staff_id));
            if ($DB->affected_rows() == 0) {
                $DB->query(sprintf("INSERT INTO `generic_locks` (`section_name`, `generic_id`, `staff_id`, `expiry_date`, `created`, `modified`) VALUES (%s, %d, %d, %s, NOW(), NOW())", $section_name, $id, $staff_id, formatSQLDateTime($_SERVER['REQUEST_TIME'] + $timeout)));
            }

        } else {
            $DB->query(sprintf("UPDATE `generic_locks` SET `expiry_date` = NOW(), `modified` = NOW() WHERE `section_name` = %s AND `generic_id` = %d AND `expiry_date` > NOW()", $section_name, $id));
        }*/

    }
}
if (!function_exists('purchaseOrderSetValue')) {
    function purchaseOrderSetValue($invoice_id, $field, $value)
    {
        global $DB;
        $fields = array();
        $query  = $DB->query("SHOW COLUMNS FROM `purchase_order`");
        while ($row = $DB->fetch_array($query, MYSQLI_NUM)) {
            $fields[$row[0]] = $row;
        }

        $DB->free_result($query);
        if (array_key_exists($field, $fields)) {
            if ("YES" == $fields[$field][2] && is_null($value)) {
                $DB->query("UPDATE `purchase_order` SET `" . $field . "` = NULL WHERE `order_id` = " . intval($invoice_id) . " LIMIT 1");
            } else {
                $DB->query("UPDATE `purchase_order` SET `" . $field . "` = " . formatSQLString($value) . " WHERE `order_id` = " . intval($invoice_id) . " LIMIT 1");
            }

            return true;
        }
        return false;
    }
}
if (!function_exists('purchaseordereSort')) {
    function purchaseordereSort($invoice_list, $order_by = 'order_id')
    {
        global $DB;
        if (!is_array($invoice_list) || count($invoice_list) < 2) {
            return $invoice_list;
        }

        $fields1 = purchaseOrderGetOptionList('all_fields');
        if (!array_key_exists($order_by, $fields1)) {
            return $invoice_list;
        }

        $fields2 =[];// purchaseOrderGetOptionList('inquiry_fields');
        if (!array_key_exists($order_by, $fields2)) {
            $query = $DB->query("SELECT `i`.`order_id` AS `id` FROM `purchase_order` AS `i` WHERE " . purchaseorderSQLList($invoice_list) . " ORDER BY " . $fields1[$order_by] . " ASC");
        } else {
            $query = $DB->query("SELECT `id` FROM (SELECT `i`.`order_id` AS `id`, " . $fields1[$order_by] . " AS `sortable` FROM `purchase_order` AS `i` WHERE " . purchaseorderSQLList($invoice_list) .") AS `i` ORDER BY `i`.`sortable` ASC");
        }

        $invoice_list = array();
        while ($row = $DB->fetch_assoc($query)) {
            $invoice_list[] = $row['id'];
        }
        $DB->free_result($query);
        return $invoice_list;
    }
}
if (!function_exists("purchaseorderSQLList")) {
    function purchaseorderSQLList($invoice_list, $field = '`i`.`invoice_id`')
    {
        if (count($invoice_list) == 0) {
            return "1";
        }

        $whereOR = array();
        $whereIN = array();
        foreach (list_to_partitions($invoice_list) as $k => $v) {
            if ($k > $v + 2) {
                $whereOR[] = "(" . $field . " >= " . $v . " AND " . $field . " <= " . $k . ")";
            } else {
                foreach (range($v, $k) as $k2) {
                    $whereIN[] = $k2;
                }
            }

        }
        if (count($whereIN) > 1) {
            $whereOR[] = $field . " IN(" . implode(", ", $whereIN) . ")";
        } elseif (count($whereIN) == 1) {
            $whereOR[] = $field . " = " . implode(" OR " . $field . " = ", $whereIN);
        }

        return implode(" OR ", $whereOR);
    }
}
if (!function_exists("inquirySQLList")) {
    function inquirySQLList($inquiry_list)
    {
        if (count($inquiry_list) == 0) {
            return "1";
        }

        $whereOR = array();
        $whereIN = array();
        foreach (list_to_partitions($inquiry_list) as $k => $v) {
            if ($k > $v + 2) {
                $whereOR[] = "(`i`.`inquiry_id` >= " . $v . " AND `i`.`inquiry_id` <= " . $k . ")";
            } else {
                foreach (range($v, $k) as $k2) {
                    $whereIN[] = $k2;
                }
            }

        }
        if (count($whereIN) > 1) {
            $whereOR[] = "`i`.`inquiry_id` IN(" . implode(", ", $whereIN) . ")";
        } elseif (count($whereIN) == 1) {
            $whereOR[] = "`i`.`inquiry_id` = " . implode(" OR `i`.`inquiry_id` = ", $whereIN);
        }

        return implode(" OR ", $whereOR);
    }
}
if (!function_exists('invoiceSplit')) {
    function invoiceSplit($invoice, $prod_counts)
    {
        global $DB;
        $invoice1 = summarizeInvoice($invoice1_id, true);
        $invoice2 = $invoice1;
        unset($invoice2['invoice_id']);
        $invoice1_products = getInvoiceProducts($invoice1_id, true);
        $invoice2_products = array();
        $offer1_total      = $invoice1['freight_offer'];
        $offer2_total      = $invoice2['freight_offer'];
        $cost1_total       = $invoice1['freight_cost'];
        $cost2_total       = $invoice2['freight_cost'];
        $offerus1_total    = $invoice1['freight_offerus'];
        $offerus2_total    = $invoice2['freight_offerus'];
        $costus1_total     = $invoice1['freight_costus'];
        $costus2_total     = $invoice2['freight_costus'];
        foreach ($invoice1_products as $key => $product) {
            if (in_array($product['prod_count'], $prod_counts)) {
                $offer2_total += $product['product_offer'] * $product['product_qty'];
                $cost2_total += $product['supplier_cost'] * $product['product_qty'];
                $offerus2_total += $product['product_offerus'] * $product['product_qty'];
                $costus2_total += $product['supplier_costus'] * $product['product_qty'];
                $invoice2_products[] = $product;
                unset($invoice1_products[$key]);
            } else {
                $offer1_total += $product['product_offer'] * $product['product_qty'];
                $cost1_total += $product['supplier_cost'] * $product['product_qty'];
                $offerus1_total += $product['product_offerus'] * $product['product_qty'];
                $costus1_total += $product['supplier_costus'] * $product['product_qty'];
            }
        }

        $invoice1['product_offer_total'] = $offer1_total;
        $invoice2['product_offer_total'] = $offer2_total;
        $invoice1['product_cost_total']  = $cost1_total;
        $invoice2['product_cost_total']  = $cost2_total;
        $invoice1['invoice_value']       = $offerus1_total;
        $invoice2['invoice_value']       = $offerus2_total;
        $invoice1['costus_total']        = $costus1_total;
        $invoice2['costus_total']        = $costus2_total;
        $invoice1['invoice_profit']      = $offerus1_total - $costus1_total;
        $invoice2['invoice_profit']      = $offerus2_total - $costus2_total;
        $invoice2_id                     = setInvoiceSummary($invoice2, true);
        if ($invoice2_id > 0) {
            setInvoiceProducts($invoice2_id, $invoice2_products);
            setInvoiceSummary($invoice1, false);
            setInvoiceProducts($invoice1_id, $invoice1_products);
            if (0 == $invoice1['group_id']) {
                $DB->query("INSERT INTO `invoice_linker` (`invoice_id`) VALUES (" . $invoice1_id . ")");
                $invoice1['group_id'] = $DB->insert_id();
                $DB->query("UPDATE `invoices` SET `group_id` = " . $invoice1['group_id'] . " WHERE `invoice_id` = " . $invoice1_id . " LIMIT 1");
                $DB->query("INSERT INTO `invoice_linker` (`group_id`, `invoice_id`) VALUES (" . $invoice1['group_id'] . ", " . $invoice2_id . ")");
                $DB->query("UPDATE `invoices` SET `group_id` = " . $invoice1['group_id'] . " WHERE `invoice_id` = " . $invoice2_id . " LIMIT 1");
            }
        }
        return $invoice2_id;
    }
}
if (!function_exists('invoiceStatusList')) {
    function invoiceStatusList()
    {
        global $DB;
        $row     = $DB->query_first("SHOW COLUMNS FROM `invoices` LIKE 'invoice_status'", MYSQLI_NUM);
        $results = explode("','", substr($row[1], 6, -2));
        return array_combine($results, array_map('ucfirst', $results));
    }
}
if (!function_exists('invoiceSumValue')) {
    function invoiceSumValue($invoice_list, $field)
    {
        global $DB;
        if (count($invoice_list) < 1) {
            return 0;
        }

        $sum = 0;

        $fields = getTableColumns('`invoices`');
        $fields = array_merge($fields, purchaseOrderGetOptionList('ext_fields'));
        if (array_key_exists($field, $fields)) {
            $row = $DB->query_first("SELECT SUM(" . $fields[$field] . ") AS `" . $field . "` FROM `invoices` WHERE `invoice_id` IN(" . implode(', ', $invoice_list) . ")");
            $sum += $row[$field];
            //return $row[$field];
        }

        $fields = getTableColumns('`inquiries`');
        foreach ($fields as $key => $value) {
            $fields[$key] = '`i`.`' . $value . '`';
        }

        $fields = array_merge($fields, purchaseOrderGetOptionList('inquiry_fields'));
        if (array_key_exists($field, $fields)) {
            $row = $DB->query_first("SELECT SUM(" . $fields[$field] . ") AS `" . $field . "` FROM `inquiries` WHERE `inquiry_id` IN(" . implode(', ', $invoice_list) . ")");
            $sum += $row[$field];
            //return $row[$field];
        }
        return $sum;

        return null;
    }
}
if (!function_exists('invoiceTemplateVars')) {
    function invoiceTemplateVars($invoice = array(), $invoice_products = array(), $invoice_charges = array())
    {
        global $DB;
        $invoice_id     = intval($invoice['invoice_id']);
        $invoice_labels = $DB->query_first("SELECT * FROM `company_info` WHERE `company_id` = " . intval($invoice['site_id']) . " LIMIT 1", MYSQLI_ASSOC);
        $website_info   = $DB->query_first("SELECT * FROM `websites` WHERE `id` = " . intval($invoice['site_id']) . " LIMIT 1", MYSQLI_ASSOC);
        if (!$invoice_labels) {
            $invoice_labels = $DB->query_first("SELECT * FROM `company_info` WHERE `company_id` = 3 LIMIT 1", MYSQLI_ASSOC);
        }

        $results = array();

        foreach (getTableColumns('`invoices`') as $key) {
            $results['[' . strtoupper($key) . ']'] = '';
        }

        foreach (array(
            'BILL_ADDRESS_FULL',
            'COURIER_NAME',
            'PAYMENT_DETAILS',
            'PAYMENT_DETAILS_HTML',
            'SHIP_ADDRESS_FULL',
            'STAFF_NAME',
            'TRACK_NOTES',
            'TRACKING_AVAILABLE',
            'TRACKING_NUMBER',
            'TRACKING_WEBSITE',
            'PRODUCT_TYPE',
        ) as $key) {
            $results['[' . strtoupper($key) . ']'] = '';
        }

        $results['[BILL_ADDRESS_FULL_HTML]'] = nl2br(hecho($invoice['bill_address_full'], 1));
        $results['[SHIP_ADDRESS_FULL_HTML]'] = nl2br(hecho($invoice['ship_address_full'], 1));
        $results['[REQUEST_DATE]']           = date('jS F, Y', $_SERVER['REQUEST_TIME']);
        $results['[REQUEST_TIME]']           = date('g:i a', $_SERVER['REQUEST_TIME']);
        $results['[HTTP_HOST]']              = array_shift(explode('/', str_replace('http://', '', $website_info['url'])));
        $results['[STAFF_NAME]']             = '';
        $results['[FIRST_NAME]']             = trim(reset(explode(' ', $invoice['bill_name'])));
        $results['[LAST_NAME]']              = trim(end(explode(' ', $invoice['bill_name'])));

        //if ($invoice['ship_country'] == 'AU' && $invoice['bill_country'] == 'AU') $invoice_labels['company_signature'] = $invoice_labels['company_signature_au'];
        $results['[HTML_COMPANY_SIGNATURE]'] = nl2br(hecho($invoice_labels['company_signature'], 1));
        if (is_array($invoice_labels) && count($invoice_labels)) {
            foreach ($invoice_labels as $key => $value) {
                $results['[' . strtoupper($key) . ']'] = $value;
            }
        }

        $results['[HTTP_HOST]'] = array_shift(explode('/', str_replace('http://', '', $website_info['url'])));

        foreach ($invoice as $key => $value) {
            $results['[' . strtoupper($key) . ']'] = $value;
        }

        $results['[INVOICE_NUMBER]'] = purchaseOrderFormatNumber($invoice_id, null, $invoice['site_id']);

        if ('' != $invoice['proforma_tracknumber']) {
            $results['[TRACKING_NUMBER]'] = $invoice['proforma_tracknumber'];
        }

        if ('' != $invoice['ship_supplier_name']) {
            $nameStr = $invoice['ship_product_name'];
            if (trim($invoice['ship_supplier_name']) != trim($invoice['ship_product_name'])) {
                $nameStr = $invoice['ship_supplier_name'] . ' - ' . $invoice['ship_product_name'];
            }

            $results['[COURIER_NAME]'] = $nameStr;
        }
        /*
        if ($invoice['ship_id'] > 0)
        {
        $tracking = getFreightServiceDetails($invoice['ship_id'], array('track_website'));
        $results['[TRACKING_WEBSITE]'] = ($tracking['track_website'] != '') ? 'Tracking Website: ' . $tracking['track_website'] : 'Goods cannot be tracked online with this courier.';
        $results['[TRACK_NOTES]'] = (!in_array($invoice['site_id'], array(20, 26))) ? 'Please note that we are located in regional Australia and it may take up to 24hours for the tracking information to be displayed on courier websites.' : '';
        }
         */
        if ('' != $invoice['paymenttype_id']) {
            $payment_types               = getPaymentMethodsList();
            $payment_data                = $DB->query_first("SELECT * FROM `payment_methods` WHERE `site_id` = " . intval($invoice['site_id']) . " AND `payment_method_id` = " . formatSQLString($invoice['paymenttype_id']) . " LIMIT 1", MYSQLI_ASSOC);
            $results['[PAYMENT_METHOD]'] = $payment_types[$invoice['paymenttype_id']];
            if ('' != $payment_data['payment_method_details']) {
                $results['[PAYMENT_DETAILS]']      = $payment_data['payment_method_details'];
                $results['[PAYMENT_DETAILS_HTML]'] = nl2br(hecho($payment_data['payment_method_details'], 1));
            }
        }

        $results['[PAYMENT_APPROVAL]']      = ('y' == $invoice['payments_located']) ? 'Approved' : 'Awaiting';
        $results['[PAYMENT_APPROVAL_HTML]'] = '<span style="color:' . (('y' == $invoice['payments_located']) ? 'green"><b>Approved' : 'red"><b>Awaiting') . '</b></span>';

        foreach ($invoice as $k => $v) {
            $option_list = purchaseOrderGetOptionList($k);
            if ('handler_id' == $k) {
                $results['[STAFF_NAME]'] = $option_list[$v];
            }
            if (count($option_list) > 0) {
                $results['[' . strtoupper($k) . ']'] = (array_key_exists($v, $option_list)) ? $option_list[$v] : '';
            }
        }

        $html_str  = '';
        $temp_list = array();
        if (is_array($invoice_products)) {
            foreach ($invoice_products as $product) {
                $id    = $product['product_id'];
                $name  = $product['product_name'];
                $qty   = $product['product_qty'];
                $notes = $product['product_notes'];
                $offer = convertCurrency($product['product_offer'], $invoice['invoice_currency'], '', false, 2);
                if (2 == $invoice['site_id'] && $product['decimal'] > 2) {
                    $offer = convertCurrency($product['product_offer'], $invoice['invoice_currency'], '', false, $product['decimal']);
                }

                $total = ($qty * $offer);
                $offer = ($offer > 0) ? $offer : 'TBA';
                $total = ($total > 0) ? sprintf('%.2f', $total) : 'TBA';
                $html_str .= '<tr style="text-align:center"><td>' . $id . '</td>';
                $html_str .= '<td style="text-align:left">' . hecho($name, 1) . ((trim('' != $notes) ? ('<br /><div style="font-size:10px;color:#717171;padding-left:15px;"><i>' . nl2br(hecho($notes, 1)) . '</i></div>') : '')) . '</td>';
                $html_str .= '<td>' . $qty . '</td><td>' . $offer . '</td><td>' . sprintf('%.2f', $total) . '</td></tr>';
                $temp_list[] = $qty . ' x ' . $name . ' ' . $offer . ((trim($notes) != '') ? "\n    [" . str_replace("\n", "]\n    [", trim($notes)) . "]" : '');
            }
        }

        $results['[INVOICE_TXT_PRODUCTS]']  = implode("\n", $temp_list);
        $results['[INVOICE_HTML_PRODUCTS]'] = $html_str;

        $html_str  = '';
        $temp_list = array();
        if ('' != $invoice['ship_product_name']) {
            $id = (('y' == $invoice['ship_insurance']) ? 'N' : 'F') . str_pad($invoice['ship_id'], 2, '0', STR_PAD_LEFT);
            $html_str .= '<tr style="text-align:center"><td>' . $id . '</td><td style="text-align:left">' . hecho($invoice['ship_product_name'], 1) . '</td><td>1</td> <td>' . $invoice['ship_offer'] . '</td><td>' . $invoice['ship_offer'] . '</td>';
            $temp_list[] = '1 x ' . $invoice['ship_product_name'] . ' ' . $invoice['ship_offer'];
        }
        $results['[INVOICE_TXT_SHIPPING]']  = implode("\n", $temp_list);
        $results['[INVOICE_HTML_SHIPPING]'] = $html_str;

        $html_str  = '';
        $temp_list = array();
        if (is_array($invoice_charges)) {
            foreach ($invoice_charges as $charge) //if ($charge['charge_id'] != 1)
            {
                $id = 'C' . str_pad($charge['charge_id'], 2, '0', STR_PAD_LEFT);
                $html_str .= '<tr style="text-align:center"><td>' . $id . '</td><td style="text-align:left">' . hecho($charge['charge_name'], 1) . '</td><td>1</td> <td>' . $charge['charge_offer'] . '</td><td>' . $charge['charge_offer'] . ' </td></tr>';
                $temp_list[] = '1 x ' . $charge['charge_name'] . ' ' . $charge['charge_offer'];
            }
        }

        $results['[INVOICE_TXT_CHARGES]']  = implode("\n", $temp_list);
        $results['[INVOICE_HTML_CHARGES]'] = $html_str;

        $html_str  = '';
        $temp_list = array();
        if (0.0 != $invoice['products_offer']) {
            $sub_total   = convertCurrency($invoice['products_offer'], $invoice['invoice_currency']);
            $temp_list[] = 'Product Total: ' . $sub_total;
            $html_str .= '<tr><td colspan="3">&nbsp;</td><td style="text-align:center;background-color:#EBEBEB;color:#333333;"><b>Sub-Total</b></td><td style="text-align:center">' . $sub_total . '</td></tr>';
        }
        if (0.0 != $invoice['ship_offer']) {
            $freight     = convertCurrency($invoice['ship_offer'], $invoice['invoice_currency']);
            $temp_list[] = 'Shipping Total: ' . $freight;
            $html_str .= '<tr><td colspan="3">&nbsp;</td><td style="text-align:center;background-color:#EBEBEB;color:#333333;"><b>Freight</b></td><td style="text-align:center">' . $freight . '</td></tr>';
        }
        if (0.0 != $invoice['charges_offer']) {
            $charges     = convertCurrency($invoice['charges_offer'], $invoice['invoice_curreny']);
            $temp_list[] = 'Charges Total: ' . $charges;
            $gst         = 0;
            if (is_array($invoice_charges)) {
                foreach ($invoice_charges as $charge) {
                    if (1 == $charge['charge_id']) {
                        $gst = $charge['charge_offer'];
                    }

                }
            }
            $charges -= $gst;
            if ($gst > 0) {
                $html_str .= '<tr><td colspan="3">&nbsp;</td><td style="text-align:center;background-color:#EBEBEB;color:#333333;"><b>GST 10%</b></td><td style="text-align:center">' . $gst . '</td></tr>';
            }

            if ($charges > 0) {
                $html_str .= '<tr><td colspan="3">&nbsp;</td><td style="text-align:center;background-color:#EBEBEB;color:#333333;"><b>Charges</b></td><td style="text-align:center">' . $charges . '</td></tr>';
            }

        }
        $grand_total = ($invoice["invoice_offer"] > 0) ? convertCurrency($invoice['invoice_offer'], $invoice['invoice_currency']) . ' ' . $invoice['invoice_currency'] : 'TBA';
        $temp_list[] = 'Grand Total: ' . $grand_total;
        $html_str .= '<tr style="text-align:center;"><td colspan="3">&nbsp;</td><td class="head"><b>Grand Total</b></td><td>' . $grand_total . '</td></tr>';
        $results['[INVOICE_TXT_TOTALS]']  = implode("\n", $temp_list);
        $results['[INVOICE_HTML_TOTALS]'] = $html_str;

        foreach ($results as $k => $v) {
            $results[$k] = str_replace("\r", '', $v);
        }

        return $results;
    }
}
if (!function_exists('purchaseOrderToDatabase')) {
    function purchaseOrderToDatabase($summary)
    {
        global $DB, $_GLOBALS;
        $order_id = 0;
        if (array_key_exists('order_id', $summary)) {
            $order_id = intval($summary['order_id']);
        }

        $old_order = purchaseOrderFromDatabase($order_id);

        $summary['write_time'] = $_SERVER['REQUEST_TIME'];
        $summary['write_date'] = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);
        if (0 == $summary['handler_id']) {
            $summary['handler_id'] = $_GLOBALS['staff']['id'];
        }

        if ('n' == $summary['products_located']) {
            $summary['products_locate_date'] = null;
        }

        if ('n' == $summary['ship_located']) {
            $summary['ship_locate_date'] = null;
        }

        if ('n' == $summary['payments_located']) {
            $summary['payments_locate_date'] = null;
        }

        if ('n' == $summary['supply_located']) {
            $summary['supply_locate_date'] = null;
        }

        if ('y' != $summary['ship_insurance']) {
            $summary['ship_insurance'] = 'n';
        }

        if ('y' != $summary['ship_tailgate']) {
            $summary['ship_tailgate'] = 'n';
        }

        foreach ($old_order as $k => $v) {
            if (array_key_exists($k, $summary)) {
                if ((!is_array($summary[$k]) && !is_array($v) && formatSQLString($summary[$k]) == formatSQLString($v)) || $summary[$k] == $v) {
                    unset($summary[$k]);
                }
            }
        }

        if('y' == $summary['payments_located']){
            $summary['order_action'] = 'order';
        }

        if('y' == $summary['goods_checked']){
            $summary['order_action'] = 'shipping';
        }

        foreach ($summary as $k => $v) {
            if (!array_key_exists($k, $old_order)) {
                unset($summary[$k]);
            }
        }
        // echo "<pre>";print_r($summary); die;
        $fields = array();
        $query  = $DB->query("SHOW COLUMNS FROM `purchase_order`");
        while ($row = $DB->fetch_array($query, MYSQLI_NUM)) {
            $fields[$row[0]] = $row;
        }

        $DB->free_result($query);
        $message = array(
            'order_id'      => $order_id,
            'message_sender'  => $_GLOBALS['staff']['id'],
            'message_sentto'  => 'Invoice System',
            'message_type_id' => INVOICE_MESSAGE_INVOICE_MODIFIED,
        );
        if (count($summary) > 0) {
            if (purchaseOrderExists($order_id)) {
                $set_vals = array();
                foreach ($summary as $k => $v) {
                    if (array_key_exists($k, $fields) && array_key_exists($k, $summary)) {
                        if ('YES' == $fields[$k][2] && is_null($v)) {
                            $set_vals[$k] = "`" . $k . "` = NULL";
                        } else {
                            $set_vals[$k] = "`" . $k . "` = " . formatSQLString($v);
                        }

                    }
                }
                unset($set_vals['open_date']);
                $sql = "UPDATE `purchase_order` SET " . implode(', ', $set_vals) . " WHERE `order_id` = " . $order_id . " LIMIT 1";
                $DB->query($sql);

                if ($set_vals['order_status']) {
                    $add_msg                  = $message;
                    $add_msg['message_title'] = 'Order Modified';
                    $add_msg['message_notes'] = 'Order Status changed from ' . ucfirst($old_order['order_status']) . ' to ' . ucfirst($summary['order_status']);
                }
                if (count($set_vals) > 0) {
                    $add_msg                    = $message;
                    $add_msg['message_type_id'] = INVOICE_MESSAGE_QUERY;
                    $add_msg['message_title']   = 'Invoice Modified';
                    $add_msg['message_notes']   = $sql;
                    purchaseordereHistoryToDatabase($order_id, $add_msg);
                }
            } else {
                $set_vals = array();
                foreach ($summary as $k => $v) {
                    if (array_key_exists($k, $fields) && array_key_exists($k, $summary)) {
                        if ('YES' == $fields[$k][2] && is_null($v)) {
                            $set_vals[$k] = 'NULL';
                        } else {
                            $set_vals[$k] = formatSQLString($v);
                        }

                    }
                }

                $sql = "INSERT INTO `purchase_order` (`" . implode('`, `', array_keys($set_vals)) . "`) VALUES (" . implode(", ", $set_vals) . ")";

                /*$row = $DB->query_first("SHOW TABLE STATUS LIKE 'inquiries'", MYSQLI_ASSOC);
                $DB->query("ALTER TABLE `purchase_order` AUTO_INCREMENT=" . $row['Auto_increment']);*/
                $query      = $DB->query($sql);
                $order_id = $DB->insert_id();
               /* $row        = $DB->query_first("SHOW TABLE STATUS LIKE 'invoices'", MYSQLI_ASSOC);
                $DB->query("ALTER TABLE `inquiries` AUTO_INCREMENT=" . $row['Auto_increment']);*/

                if (count($set_vals) > 0) {
                    $add_msg                    = $message;
                    $add_msg['order_id']      = $order_id;
                    $add_msg['message_type_id'] = INVOICE_MESSAGE_QUERY;
                    $add_msg['message_title']   = 'Purchase Created';
                    $add_msg['message_notes']   = $sql;
                    purchaseordereHistoryToDatabase($order_id, $add_msg);
                }
                $add_msg                  = $message;
                $add_msg['order_id']    = $order_id;
                $add_msg['message_title'] = 'Purchase Created';
                purchaseordereHistoryToDatabase($order_id, $add_msg);
            }
        }
        return $order_id;
    }
    if (!function_exists('deleteInventoryOrder')) {
        function deleteInventoryOrder($order_id)
        {
            global $DB;
            $sql = "DELETE FROM `products_orders` WHERE `id` = " . $order_id;
            $DB->query($sql);
            $sql = "DELETE FROM `products_inventory` WHERE `order_id` = " . $order_id;
            $DB->query($sql);
        }
    }
    if (!function_exists('productGenericToDatabase')) {
        function productGenericToDatabase(&$item, $table, $primary_key = 'id') {
            global $DB, $_GLOBALS;
            $id         = intval($item[$primary_key]);
            $fields     = $DB->get_table_columns($table);
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
                    $item['updated_by'] = $_GLOBALS['staff']['id'];
                    $set_vals['updated_by'] = "`updated_by` = " . intval($item['updated_by']);
                    if($table=='`supplier_product`'){
                       $set_vals['updated_at'] = "`updated_at` = '".date('Y-m-d H:i:s')."'";
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
                $item['created_by'] = $_GLOBALS['staff']['id'];
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
                    if($table!='`products_inventory`'){
                        $item[$primary_key] = $id = $DB->insert_id();
                    }else{
                        $id = $DB->insert_id();
                    }

                }
            }
            return $id;
        }
    }
    if (!function_exists('productOrderToDatabase')) {
        function productOrderToDatabase(&$item)
        {
            return productGenericToDatabase($item, '`products_orders`');
        }
    }
    if (!function_exists('productInventorySingleToDatabase')) {
        function productInventorySingleToDatabase(&$item)
        {
            return productGenericToDatabase($item, '`products_inventory`');
        }
    }
    if (!function_exists('productSetValue')) {
        function productSetValue($id, $field, $value)
        {
            global $DB;
            $fields = $DB->get_table_columns('`products`');
            if (array_key_exists($field, $fields)) {
                $DB->query(sprintf(
                    "UPDATE `products` SET `%s` = %s WHERE `id` = %d LIMIT 1",
                    $field,
                    $DB->escape($value),
                    $id
                ));
                return true;
            }
            return false;
        }
    }
    if (!function_exists('purchaseOrderSetValue')) {
        function purchaseOrderSetValue($id, $field, $value)
        {
            global $DB;
            $fields = $DB->get_table_columns('`purchase_order`');
            if (array_key_exists($field, $fields)) {
                $DB->query(sprintf(
                    "UPDATE `purchase_order` SET `%s` = %s WHERE `id` = %d LIMIT 1",
                    $field,
                    $DB->escape($value),
                    $id
                ));
                return true;
            }
            return false;
        }
    }
    if (!function_exists('productGenericFromDatabase')) {
        function productGenericFromDatabase($id, $table, $primary_key = 'id') {
            global $DB;
            return $DB->query_first(sprintf(
                "SELECT *, UNIX_TIMESTAMP(`created_at`) AS `created_time`, UNIX_TIMESTAMP(`updated_at`) AS `updated_time` FROM %s WHERE `%s` = %d LIMIT 1",
                $table,
                $primary_key,
                $id
            ), MYSQLI_ASSOC);
        }
    }
    if (!function_exists('productGenericsFromDatabase')) {
        function productGenericsFromDatabase($id, $table, $primary_key = 'id', $secondary_key = 'product_id') {
            global $DB;
            $result = array();
            $query = $DB->query(sprintf(
                "SELECT *, UNIX_TIMESTAMP(`created_at`) AS `created_time`, UNIX_TIMESTAMP(`updated_at`) AS `updated_time` FROM %s WHERE `deleted_at` IS NULL AND `%s` = %d",
                $table,
                $secondary_key,
                $id
            ));
            while ($row = $DB->fetch_assoc($query)) {
                if (is_null($primary_key)) {
                    $result[] = $row;
                } else {
                    $result[$row[$primary_key]] = $row;
                }
            }
            $DB->free_result($query);
            return $result;
        }
    }
    if (!function_exists('productInventoryFromDatabase')) {
        function productInventoryFromDatabase($id)
        {
            return productGenericsFromDatabase($id, '`products_inventory`','id','order_id');
        }
    }
    if (!function_exists('productOrderFromDatabase')) {
        function productOrderFromDatabase($id)
        {
            $data= productGenericFromDatabase($id, '`products_orders`');
            return ($data)?$data:[];
        }
    }
        
}
