<?php
/**
 * A collection of functions used to generate invoice data.
 *
 * @author Highway Group
 */
include_once __DIR__ . '/query_invoice_history.inc.php'; //invoiceHistoryToDatabase

if (!defined('INVOICE_TABLENAME')) {
    define('INVOICE_TABLENAME', '`highwaygroup`.`invoices`');
}
if (!defined('INVOICE_PRIMARYKEY')) {
    define('INVOICE_PRIMARYKEY', 'invoice_id');
}

if (!function_exists('invoiceDefault')) {
    /**
     * @return array{
     *     access_date          : string,
     *     amazon_order_id      : string,
     *     backorder_flag       : string,
     *     bill_country         : string,
     *     bill_timezone        : int,
     *     charges_cost         : string,
     *     charges_costau       : string,
     *     charges_costus       : string,
     *     charges_offer        : string,
     *     charges_offerau      : string,
     *     charges_offerus      : string,
     *     def_condition        : int,
     *     def_country          : string,
     *     def_currency         : string,
     *     def_packing          : int,
     *     def_plug             : int,
     *     def_terms            : int,
     *     def_voltage          : int,
     *     def_warranty         : int,
     *     default_supplier     : int,
     *     ebay_order_id        : string,
     *     frequency_id         : int,
     *     goods_packed_by      : int,
     *     goods_shipped_by     : int,
     *     handler_id           : int,
     *     he_id                : string,
     *     invoice_action       : string,
     *     invoice_cost         : string,
     *     invoice_costau       : string,
     *     invoice_costus       : string,
     *     invoice_currency     : string,
     *     invoice_exchange     : string,
     *     invoice_exchangeus   : string,
     *     invoice_offer        : string,
     *     invoice_offerau      : string,
     *     invoice_offerus      : string,
     *     invoice_online       : string,
     *     invoice_profit       : string,
     *     invoice_profitau     : string,
     *     invoice_profitus     : string,
     *     invoice_status       : string,
     *     mark_paid_on_invoice : int,
     *     member_id            : int,
     *     needby_date          : null,
     *     newsletter_id        : int,
     *     notify_country       : string,
     *     open_date            : string,
     *     own_freight_option   : string,
     *     pack_requirement_id  : int,
     *     packed_by            : string,
     *     payment_id           : int,
     *     payment_terms_id     : int,
     *     payments_locate_date : null,
     *     payments_staff_id    : int,
     *     plug_id              : int,
     *     products_cost        : string,
     *     products_costau      : string,
     *     products_costus      : string,
     *     products_locate_date : null,
     *     products_offer       : string,
     *     products_offerau     : string,
     *     products_offerus     : string,
     *     products_staff_id    : int,
     *     proforma_date        : string,
     *     rand_num             : int,
     *     revision_id          : int,
     *     ship_cost            : string,
     *     ship_costau          : string,
     *     ship_costus          : string,
     *     ship_country         : string,
     *     ship_cubic           : string,
     *     ship_express         : string,
     *     ship_id              : int,
     *     ship_insurance       : string,
     *     ship_locate_date     : null,
     *     ship_offer           : string,
     *     ship_offerau         : string,
     *     ship_offerus         : string,
     *     ship_online          : string,
     *     ship_receipted       : string,
     *     ship_registered      : string,
     *     ship_staff_id        : int,
     *     ship_supplier_id     : int,
     *     ship_tailgate        : string,
     *     ship_weight          : string,
     *     shipped_by           : string,
     *     site_id              : int,
     *     staff_id             : int,
     *     stock_received       : string,
     *     supply_country       : string,
     *     supply_locate_date   : null,
     *     supply_staff_id      : int,
     *     tax_date             : null,
     *     tax_id               : int,
     *     terms_id             : int,
     *     visitor_id           : int,
     *     voltage_id           : int,
     *     waranty_terms_id     : int,
     *     write_date           : string,
     * }
     */
    function invoiceDefault()
    {
        global $DB;

        return invoiceVirtual($DB->modelDefault(
            INVOICE_TABLENAME,
            INVOICE_PRIMARYKEY
        ));
    }
}
if (!function_exists('invoiceDelete')) {
    /**
     * @param int $id
     *
     * @return int
     */
    function invoiceDelete($id)
    {
        return 0; //Disabled

        global $DB;

        return $DB->modelDelete(
            $id,
            INVOICE_TABLENAME,
            INVOICE_PRIMARYKEY
        );
    }
}
if (!function_exists('invoiceExists')) {
    /**
     * @param int $id
     *
     * @return bool
     */
    function invoiceExists($id)
    {
        global $DB;

        return $DB->modelExists(
            $id,
            INVOICE_TABLENAME,
            INVOICE_PRIMARYKEY
        );
    }
}
if (!function_exists('invoiceFromDatabase')) {
    /**
     * @todo Fix function conformance (remove $product_stuff and return null instead of default)
     *
     * @param int $id
     *
     * @return mixed[]|null
     */
    function invoiceFromDatabase($id, $product_stuff = true)
    {
        global $DB;

        if ($row = $DB->modelLoad(
            $id,
            INVOICE_TABLENAME,
            INVOICE_PRIMARYKEY
        )) {
            return invoiceVirtual($row, $product_stuff);
        }

        return invoiceDefault();
        //return null;
    }
}
if (!function_exists('invoiceGetValue')) {
    /**
     * @param int    $id
     * @param string $field
     *
     * @return mixed
     */
    function invoiceGetValue($id, $field)
    {
        global $DB;

        if (array_key_exists($field, $DB->table_columns(
            INVOICE_TABLENAME
        ))) {
            return $DB->modelGet(
                $id,
                $field,
                INVOICE_TABLENAME,
                INVOICE_PRIMARYKEY
            );
        }

        if ($model = invoiceFromDatabase($id)) {
            return (array_key_exists($field, $model)) ? $model[$field] : null;
        }

        return null;
    }
}
if (!function_exists('invoiceHasValue')) {
    /**
     * @param int    $id
     * @param string $field
     * @param mixed  $value
     *
     * @return bool
     */
    function invoiceHasValue($id, $field, $value)
    {
        return invoiceGetValue($id, $field) == $value;
    }
}
if (!function_exists('invoiceQuery')) {
    /**
     * @todo
     *
     * @param mixed[] $eq
     * @param mixed[] $lt
     * @param mixed[] $gt
     *
     * @return string
     */
    /*
    function invoiceQuery($eq = array(), $lt = array(), $gt = array())
    {
        global $DB;

        $whereAND = array();

        return (count($whereAND)) ? implode(" AND ", $whereAND) : "1";
    }
    */
}
if (!function_exists('invoiceSearch')) {
    /**
     * @param string|null $where
     * @param string|null $order_by
     *
     * @return int[]
     */
    /*
    function invoiceSearch($where = null, $order_by = null)
    {
        global $DB;

        // Legacy accepts criteria as first parameter
        if (is_array($where)) {
            $where = invoiceQuery($where);
        }

        return $DB->query_list(sprintf(
            "SELECT `%s` FROM %s%s%s",
            INVOICE_PRIMARYKEY,
            INVOICE_TABLENAME,
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
if (!function_exists('invoiceSetValue')) {
    /**
     * @param int    $id
     * @param string $field
     * @param mixed  $value
     *
     * @return int
     */
    function invoiceSetValue($id, $field, $value)
    {
        global $DB;
        deleteMysqlCacheData('invoice',$id);
        return $DB->modelSet(
            $id,
            $field,
            $value,
            INVOICE_TABLENAME,
            INVOICE_PRIMARYKEY
        );
    }
}
if (!function_exists('invoiceSort')) {
    /**
     * @param string|null $where
     * @param string|null $order_by
     *
     * @return int[]
     */
    /*
    function invoiceSort($where = null, $order_by = null)
    {
        // Legacy accepts list of id's as first parameter
        if (is_array($where)) {
            $where = invoiceQuery(array(
                INVOICE_PRIMARYKEY => $where,
            ));
        }

        return invoiceSearch($where, $order_by);
    }
    */
}
if (!function_exists('invoiceToDatabase')) {
    /**
     * @param mixed[] $item
     *
     * @return int
     */
    function invoiceToDatabase(&$item)
    {
        global $_GLOBALS;
        global $DB;

        $id     = null;
        $before = null;
        if (array_key_exists(INVOICE_PRIMARYKEY, $item)) {
            $id     = $item[INVOICE_PRIMARYKEY];
            $before = invoiceFromDatabase($id);
        }
        if (is_null($before)) {
            $before = invoiceDefault();
        }

        /**
         * @todo this should be replaced with ON UPDATE in the database
         */
        $item['write_time'] = time();
        $item['write_date'] = formatDateTime();

        /**
         * @todo move this to invoiceFromPost since it deals with translating fields
         */
        if (!array_key_exists('handler', $item) || 0 == $item['handler_id']) {
            if (array_key_exists('staff', $_GLOBALS)) {
                $item['handler_id'] = $_GLOBALS['staff']['id'];
            }
        }

        /**
         * @todo move this to invoiceFromPost since it deals with translating fields
         */
        foreach (array(
            'products_located' => 'products_locate_date',
            'ship_located'     => 'ship_locate_date',
            'payments_located' => 'payments_locate_date',
            'supply_located'   => 'supply_locate_date',
        ) as $k => $v) {
            if (array_key_exists($k, $item) && 'n' === $item[$k]) {
                $item[$v] = null;
            }
        }

        /**
         * @todo move this to invoiceFromPost since it deals with checkbox translation
         */
        foreach (array(
            'ship_insurance',
            'ship_tailgate',
        ) as $k => $v) {
            if (array_key_exists($k, $item) && 'y' != $item[$k]) {
                $item[$k] = 'n';
            }
        }

        $invoice_id = $DB->modelSave(
            $item,
            INVOICE_TABLENAME,
            INVOICE_PRIMARYKEY
        );

        $message = array(
            'invoice_id'      => $invoice_id,
            'message_type_id' => INVOICE_MESSAGE_QUERY,
            'message_title'   => 'Invoice Updated',
            'message_notes'   => end($DB->all_queries),
        );
        invoiceHistoryToDatabase($invoice_id, $message);

        $changes = array();
        foreach ($before as $k => $v) {
            if (array_key_exists($k, $item) && $item[$k] != $v) {
                $changes[$k] = sprintf(
                    "%s changed from %s to %s",
                    $k,
                    $DB->escape($v),
                    $DB->escape($item[$k])
                );
            }
        }

        if (array_key_exists('invoice_status', $changes) && array_key_exists(INVOICE_PRIMARYKEY, $before)) {
            $message = array(
                'invoice_id'      => $invoice_id,
                'message_type_id' => INVOICE_MESSAGE_QUERY,
                'message_title'   => 'Invoice Modified',
                'message_notes'   => sprintf(
                    'Invoice Status changed from %s to %s',
                    ucfirst($before['invoice_status']),
                    ucfirst($item['invoice_status'])
                ),
            );
            invoiceHistoryToDatabase($invoice_id, $message);
        }
        deleteMysqlCacheData('invoice',$invoice_id);
        return $invoice_id;
    }
}
if (!function_exists('invoiceVirtual')) {
    /**
     * @todo Some of these fields are bad practice, will overwrite fields or have unexpected side-effects
     *
     * @param array{
     *     invoice_id?          : int,
     *     bill_name?           : string,
     *     bill_email?          : string,
     *     bill_company?        : string,
     *     bill_address?        : string,
     *     bill_city?           : string,
     *     bill_state?          : string,
     *     bill_country?        : string,
     *     bill_postcode?       : string,
     *     bill_phone?          : string,
     *     ship_name?           : string,
     *     ship_email?          : string,
     *     ship_company?        : string,
     *     ship_address?        : string,
     *     ship_city?           : string,
     *     ship_state?          : string,
     *     ship_country?        : string,
     *     ship_postcode?       : string,
     *     ship_phone?          : string,
     *     notify_name?         : string,
     *     notify_email?        : string,
     *     notify_company?      : string,
     *     notify_address?      : string,
     *     notify_city?         : string,
     *     notify_state?        : string,
     *     notify_country?      : string,
     *     notify_postcode?     : string,
     *     notify_phone?        : string,
     *     access_date          : string,
     *     amazon_order_id      : string,
     *     backorder_flag       : string,
     *     bill_country         : string,
     *     bill_timezone        : int,
     *     charges_cost         : string,
     *     charges_costau       : string,
     *     charges_costus       : string,
     *     charges_offer        : string,
     *     charges_offerau      : string,
     *     charges_offerus      : string,
     *     def_condition        : int,
     *     def_country          : string,
     *     def_currency         : string,
     *     def_packing          : int,
     *     def_plug             : int,
     *     def_terms            : int,
     *     def_voltage          : int,
     *     def_warranty         : int,
     *     default_supplier     : int,
     *     ebay_order_id        : string,
     *     frequency_id         : int,
     *     goods_packed_by      : int,
     *     goods_shipped_by     : int,
     *     handler_id           : int,
     *     he_id                : string,
     *     invoice_action       : string,
     *     invoice_cost         : string,
     *     invoice_costau       : string,
     *     invoice_costus       : string,
     *     invoice_currency     : string,
     *     invoice_exchange     : string,
     *     invoice_exchangeus   : string,
     *     invoice_offer        : string,
     *     invoice_offerau      : string,
     *     invoice_offerus      : string,
     *     invoice_online       : string,
     *     invoice_profit       : string,
     *     invoice_profitau     : string,
     *     invoice_profitus     : string,
     *     invoice_status       : string,
     *     mark_paid_on_invoice : int,
     *     member_id            : int,
     *     needby_date          : string|null,
     *     newsletter_id        : int,
     *     notify_country       : string,
     *     open_date            : string,
     *     own_freight_option   : string,
     *     pack_requirement_id  : int,
     *     packed_by            : string,
     *     payment_id           : int,
     *     payment_terms_id     : int,
     *     payments_locate_date : string|null,
     *     payments_staff_id    : int,
     *     plug_id              : int,
     *     products_cost        : string,
     *     products_costau      : string,
     *     products_costus      : string,
     *     products_locate_date : string|null,
     *     products_offer       : string,
     *     products_offerau     : string,
     *     products_offerus     : string,
     *     products_staff_id    : int,
     *     proforma_date        : string,
     *     rand_num             : int,
     *     revision_id          : int,
     *     ship_cost            : string,
     *     ship_costau          : string,
     *     ship_costus          : string,
     *     ship_country         : string,
     *     ship_cubic           : string,
     *     ship_express         : string,
     *     ship_id              : int,
     *     ship_insurance       : string,
     *     ship_locate_date     : string|null,
     *     ship_offer           : string,
     *     ship_offerau         : string,
     *     ship_offerus         : string,
     *     ship_online          : string,
     *     ship_receipted       : string,
     *     ship_registered      : string,
     *     ship_staff_id        : int,
     *     ship_supplier_id     : int,
     *     ship_tailgate        : string,
     *     ship_weight          : string,
     *     shipped_by           : string,
     *     site_id              : int,
     *     staff_id             : int,
     *     stock_received       : string,
     *     supply_country       : string,
     *     supply_locate_date   : string|null,
     *     supply_staff_id      : int,
     *     tax_date             : string|null,
     *     tax_id               : int,
     *     terms_id             : int,
     *     visitor_id           : int,
     *     voltage_id           : int,
     *     waranty_terms_id     : int,
     *     write_date           : string,
     * } $row
     *
     * @return mixed[]
     */
    function invoiceVirtual($row, $product_stuff = false)
    {
        global  $_GLOBALS;
        if (array_key_exists(INVOICE_PRIMARYKEY, $row)) {
            $row['id'] = $row[INVOICE_PRIMARYKEY];
        }

        $row['created_at'] = $row['open_date'] = (isset($row['created_at'])) ? $row['created_at'] : $row['open_date'];
        $row['updated_at'] = $row['write_date'] = (isset($row['updated_at'])) ? $row['updated_at'] : ((isset($row['write_date'])) ? $row['write_date'] : $row['created_at']);

       $row['needby_time'] = (is_null($row['needby_date']) || '0000-00-00 00:00:00' == $row['needby_date']) ? null : strtotime($row['needby_date']);
        $row['payments_locate_time'] = (is_null($row['payments_locate_date'])) ? null : strtotime($row['payments_locate_date']);
        $row['products_locate_time'] = (is_null($row['products_locate_date'])) ? null : strtotime($row['products_locate_date']);
        $row['ship_locate_time']     = (is_null($row['ship_locate_date'])) ? null : strtotime($row['ship_locate_date']);
        $row['supply_locate_time']   = (is_null($row['supply_locate_date'])) ? null : strtotime($row['supply_locate_date']);
        $row['proforma_time']        = (is_null($row['proforma_date'])) ? null : strtotime($row['proforma_date']);
        $row['write_time']           = strtotime($row['updated_at']);

        $row['payments_located'] = (is_null($row['payments_locate_time'])) ? 'n' : 'y';
        $row['products_located'] = (is_null($row['products_locate_time'])) ? 'n' : 'y';
        $row['ship_located']     = (is_null($row['ship_locate_time'])) ? 'n' : 'y';
        $row['supply_located']   = (is_null($row['supply_locate_time'])) ? 'n' : 'y';

        $row['open_time']    = strtotime($row['open_date']);
        $split               = explode('-', date('n-j-w-Y', $row['open_time']));
        $row['open_year']    = mktime(0, 0, 0, 1, 1, (int) $split[3]);
        $row['open_month']   = mktime(0, 0, 0, (int) $split[0], 1, (int) $split[3]);
        $row['open_day']     = mktime(0, 0, 0, (int) $split[0], (int) $split[1], (int) $split[3]);
        $row['open_quarter'] = mktime(0, 0, 0, intdiv((int) $split[0] - 1, 3) * 3 + 1, 1, (int) $split[3]);
        $row['open_week']    = mktime(0, 0, 0, (int) $split[0], (int) $split[1] - (int) $split[2], (int) $split[3]);

        $row['tax_time']    = strtotime((isset($row['tax_date'])) ? $row['tax_date'] : ((isset($row['payments_locate_date'])) ? $row['payments_locate_date'] : ((isset($row['proforma_date'])) ? $row['proforma_date'] : $row['open_date'])));
        $split              = explode('-', date('n-j-w-Y', $row['tax_time']));
        $row['tax_year']    = mktime(0, 0, 0, 1, 1, (int) $split[3]);
        $row['tax_month']   = mktime(0, 0, 0, (int) $split[0], 1, (int) $split[3]);
        $row['tax_day']     = mktime(0, 0, 0, (int) $split[0], (int) $split[1], (int) $split[3]);
        $row['tax_quarter'] = mktime(0, 0, 0, intdiv((int) $split[0] - 1, 3) * 3 + 1, 1, (int) $split[3]);
        $row['tax_week']    = mktime(0, 0, 0, (int) $split[0], (int) $split[1] - (int) $split[2], (int) $split[3]);
        $row['tax_fyear']   = mktime(0, 0, 0, 1, 1, (int) $split[3] - ((int) $split[0] > 6) ? 0 : 1);

        $row['ship_product_str'] = (0 != $row['ship_id']) ? sprintf(
            'F%02d%s%s%s%s%s',
            $row['ship_id'],
            (array_key_exists('ship_insurance', $row) && 'y' == $row['ship_insurance']) ? 'I' : 'N',
            (array_key_exists('ship_type', $row) && 'truck' == $row['ship_type']) ? 'T' : '',
            (array_key_exists('ship_registered', $row) && 'y' == $row['ship_registered']) ? 'R' : '',
            (array_key_exists('ship_insurance', $row) && 'y' == $row['ship_insurance']) ? 'A' : '',
            (array_key_exists('ship_express', $row) && 'y' == $row['ship_express']) ? 'E' : ''
        ) : '';

        $bill_country_list        = invoiceGetOptionList('bill_country');
        $row['bill_address_full'] = invoiceFormatAddress(array(
            'name'            => $row['bill_name'],
            'email'           => $row['bill_email'],
            'organization'    => $row['bill_company'],
            'street_address'  => $row['bill_address'],
            'address_level_2' => $row['bill_city'],
            'address_level_1' => $row['bill_state'],
            'country'         => (array_key_exists($row['bill_country'], $bill_country_list)) ? $bill_country_list[$row['bill_country']] : $row['bill_country'],
            'postal_code'     => $row['bill_postcode'],
            'tel'             => $row['bill_phone'],
        ));

        $ship_country_list        = invoiceGetOptionList('ship_country');
        $row['ship_address_full'] = invoiceFormatAddress(array(
            'name'            => $row['ship_name'],
            'email'           => (empty($row['ship_email'])) ? $row['bill_email'] : $row['ship_email'],
            'organization'    => $row['ship_company'],
            'street_address'  => trim($row['ship_address']),
            'address_level_2' => $row['ship_city'],
            'address_level_1' => $row['ship_state'],
            'country'         => (array_key_exists($row['ship_country'], $ship_country_list)) ? $ship_country_list[$row['ship_country']] : $row['ship_country'],
            'postal_code'     => $row['ship_postcode'],
            'tel'             => $row['ship_phone'],
        ));

        $notify_country_list        = invoiceGetOptionList('notify_country');
        $row['notify_address_full'] = invoiceFormatAddress(array(
            'name'            => (empty($row['notify_name'])) ? $row['ship_name'] : $row['notify_name'],
            'email'           => (empty($row['ship_email'])) ? $row['bill_email'] : $row['ship_email'],
            'organization'    => (empty($row['notify_company'])) ? $row['ship_company'] : $row['notify_company'],
            'street_address'  => (empty($row['notify_address'])) ? $row['ship_address'] : $row['notify_address'],
            'address_level_2' => (empty($row['notify_city'])) ? $row['ship_city'] : $row['notify_city'],
            'address_level_1' => (empty($row['notify_state'])) ? $row['ship_state'] : $row['notify_state'],
            'country'         => (array_key_exists($row['notify_country'], $notify_country_list)) ? $notify_country_list[$row['notify_country']] : $row['notify_country'],
            'postal_code'     => (empty($row['notify_postcode'])) ? $row['ship_postcode'] : $row['notify_postcode'],
            'tel'             => (empty($row['notify_email'])) ? $row['ship_phone'] : $row['notify_email'],
        ));

        $row['time_open'] = time() - $row['open_time'];
        if (in_array($row['invoice_status'], array('processed', 'closed', 'cancelled'))) {
            $row['time_open'] = $row['write_time'] - $row['open_time'];
        }
        $row['days_open'] = max(0, intval($row['time_open'] / 86400));
        $row['supply_from']=$_GLOBALS['site_settings']['supply_country'];
        $product_country=[];
        if ($product_stuff) {
            $row['products_needed'] = 0;
            $row['products_found']  = 0;
            foreach (invoiceProductsFromDatabase($row[INVOICE_PRIMARYKEY]) as $invoice_product) {
                $row['products_needed']++;
                if ('n' != $invoice_product['supplier_found']) {
                    $row['products_found']++;
                }
                if(!in_array($invoice_product['supplier_country'],$product_country)){
                    if($invoice_product['supplier_country']){
                        array_push($product_country,$invoice_product['supplier_country']);
                    }
                    
                }
            }
        }
        if(count($product_country)==1){
            // check if product from same location then supply from will be same country
            $row['supply_from']=$product_country[0];
        }
        if (array_key_exists(INVOICE_PRIMARYKEY, $row)) {
            $row["invoice_number"] = invoiceFormatNumber($row["invoice_id"], $row["revision_id"], $row["site_id"]);
        } else {
            $row["invoice_number"] = invoiceFormatNumber(0, 0, $row["site_id"]);
        }

        return $row;
    }
}

if (!function_exists('invoiceFormatAddress')) {
    /**
     * @param array{
     *    name:string,
     *    email:string,
     *    organization_title:string,
     *    organization:string,
     *    street_address:string,
     *    address_level_2:string,
     *    address_level_1:string,
     *    country:string,
     *    postal_code:string.
     *    tel:string,
     * } $address
     *
     * @return string
     */
    function invoiceFormatAddress($address)
    {
        $result = array();
        if(!empty($address['organization'])){
            $result[] = $address['organization'];
        }else{
             $result[] =  $address['name'];
        }
        if(!empty($address['street_address'])){
            $result[] = $address['street_address'];
        }
       
        $result[] = ltrim(sprintf(
            '%s, %s %s',
            $address['address_level_2'],
            $address['address_level_1'],
            $address['postal_code']
        ), ', ');
        $result[] = $address['country'];
         $result[]='';
      /*  if (empty($address['organization'])) {
            $result[] = $address['name'];
        } elseif (empty($address['name'])) {
            $result[] = $address['organization'];
        } elseif ($address['organization'] == $address['name']) {
            $result[] = $address['organization'];
        } else {
            //$result[] = $address['organization'];
            $result[] = 'Attn: ' . $address['name'];
        }*/
        if(!empty($address['organization'])){
            $result[] = 'Attn: ' . $address['name'];
        }
        if (!empty($address['tel'])) {
            $result[] = 'Ph: ' . $address['tel'];
        }
        if (!empty($address['email'])) {
            $result[] = 'Email: ' . $address['email'];
        }
        return implode("\n", $result);
    }
}

define("INVOICE_EMAIL_CONTACT", 27);
define("INVOICE_EMAIL_GENERAL", 1);
define("INVOICE_EMAIL_INQUIRY", 16);
define("INVOICE_EMAIL_ENQUIRY", 16);
define("INVOICE_EMAIL_INTEREST", 14);
define("INVOICE_EMAIL_INVOICE", 5);
define("INVOICE_EMAIL_MODIFIED", 11);
define("INVOICE_EMAIL_SHIPPED", 2);
define("INVOICE_EMAIL_VALIDATE", 3);

define("PBMAKER_JOINUS_CONTACT", 39);
define("PBMAKER_START_CONTACT", 40);

if (!function_exists('getFreightServiceDetails')) {
    /**
     * @param int   $id
     * @param mixed $fields
     *
     * @return mixed
     */
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

        $result = null;
        if ($row = $DB->query_first(sprintf(
            "SELECT %s FROM `highwaygroup`.`freight_services` WHERE `id` = %d LIMIT 1",
            $strField,
            $id
        ), MYSQLI_ASSOC)) {
            if (is_array($fields)) {
                foreach ($fields as $field) {
                    $result[$field] = $row[$field];
                }
            } else {
                $result = $row[$fields];
            }
        }

        return $result;
    }
}

if (!function_exists('invoiceChunk')) {
    /**
     * @param int[] $invoice_list
     * @param string|string[] $group_by
     *
     * @return mixed[]
     */
    function invoiceChunk($invoice_list, $group_by = '')
    {
        global $DB;
        if (!is_array($invoice_list) || count($invoice_list) < 1) {
            return $invoice_list;
        }

        if (!is_array($group_by)) {
            $group_by = array($group_by);
        }

        $fields = getTableColumns(INVOICE_TABLENAME);
        foreach ($fields as $key => $value) {
            $fields[$key] = '`i`.`' . $value . '`';
        }

        foreach (invoiceGetOptionList('ext_fields') as $key => $value) {
            $fields[$key] = $value . ' AS ' . $key;
        }

        $field_list = array('invoice_id');
        foreach ($group_by as $field) {
            if (array_key_exists($field, $fields)) {
                $field_list[] = $fields[$field];
            }
        }

        $field_list = array_unique($field_list);

        $results = array();
        if ($query = $DB->query(sprintf(
            "SELECT %s FROM %s AS `i` WHERE %s",
            implode(', ', $field_list),
            INVOICE_TABLENAME,
            invoiceSQLList($invoice_list)
        ))) {
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
        }

        return $results;
    }
}

if (!function_exists('invoiceFormatNumber')) {
    /**
     * @param int $invoice_id
     * @param int $revision_id
     * @param int $site_id
     *
     * @return string
     */
    function invoiceFormatNumber($invoice_id, $revision_id = 0, $site_id = 0)
    {
        $prefix1       = "Q";
        $status_lookup = array(
            'enquiry'   => 'E',
            'inquiry'   => 'Q',
            'closed'    => 'Q',
            'proforma'  => 'P',
            'sales'     => 'I',
            'processed' => 'I',
            'cancelled' => 'Q',
            'return'    => 'R',
            'completed' => 'R',
        );
        $invoice_status_list = invoiceGetOptionList("list_invoice_status");
        if (array_key_exists($invoice_id, $invoice_status_list) && array_key_exists($invoice_status_list[$invoice_id], $status_lookup)) {
            $prefix1 = $status_lookup[$invoice_status_list[$invoice_id]];
        }

        /**
         * Hack for pain in ass company that assumes quotes are invoices
         */
        if (in_array($invoice_id, array(21493, 22187))) {
            $prefix1 = "Q";
        }

        $prefix2             = "";
        $invoice_prefix_list = invoiceGetOptionList("invoice_prefix");
        if (array_key_exists($site_id, $invoice_prefix_list)) {
            $prefix2 = $invoice_prefix_list[$site_id];
        }

        $suffix = "";
        if ($revision_id > 0) {
            $suffix = chr(ord('A') + $revision_id - 1);
        }

        return sprintf("%s%s#%05d%s", $prefix1, $prefix2, $invoice_id, $suffix);
    }
}

if (!function_exists('invoiceForwarderQuotesFromPost')) {
    /**
     * @param mixed[] $posted
     *
     * @return mixed[][]
     */
    function invoiceForwarderQuotesFromPost($posted)
    {
        global $DB;
        $fields = array();
        $query  = $DB->query("SHOW COLUMNS FROM `highwaygroup`.`freight_services_quotes`");
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
    /**
     * @param int       $invoice_id
     * @param mixed[][] $quotes
     * @param int[]     $affected_quotes
     *
     * @return void
     */
    function invoiceForwarderQuotesToDatabase($invoice_id, $quotes, $affected_quotes)
    {
        global $DB;
        //now have to delete the freight quotes that were affected..
        foreach ($affected_quotes as $quote_id) {
            $DB->query("DELETE FROM `highwaygroup`.`freight_services_quotes` WHERE `id` = " . intval($quote_id));
        }

        $invoice_id = intval($invoice_id);
        if (0 == $invoice_id) {
            return;
        }

        $fields = array();
        $query  = $DB->query("SHOW COLUMNS FROM `highwaygroup`.`freight_services_quotes`");
        while ($row = $DB->fetch_array($query)) {
            if ('auto_increment' != $row[5]) {
                $fields[$row[0]] = $row[4];
            }
        }

        if (is_array($quotes) && count($quotes)) {
            foreach ($quotes as $quote) {
                $quote_id = intval($quote['id']);
                $sql      = (($quote_id > 0) ? "UPDATE" : "INSERT") . " `highwaygroup`.`freight_services_quotes` ";
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
                    $DB->query($sql);
                }
            }
        }
    }
}
function forwardersQuotesSearchNEW($criteria = array())
{
    global $DB;
    $results = array();

    $sql   = " SELECT fq.id FROM `highwaygroup`.`freight_services_quotes` fq";
    $joins = array();

    $fields = getTableColumns('`highwaygroup`.`freight_services_quotes`');
    foreach ($fields as $key => $value) {
        $fields[$key] = 'fq.`' . $value . '`';
    }

    $fields = array_merge($fields, forwardersQuotesGetOptionList('ext_fields'));

    $whereAND = array();
    if (is_array($criteria)) {
        foreach ($criteria as $field => $value) {
            if (isset($fields[$field])) {
                if (is_array($value)) {
                    if (count($value) > 0) {
                        $whereOR = array();
                        foreach ($value as $sub_value) {
                            $whereOR[] = "  " . $fields[$field] . "=" . $DB->escape($sub_value);
                        }

                        $whereAND[] = " (" . implode(" OR ", $whereOR) . ")";
                    } else {
                        return array();
                    }
                } else {
                    $whereAND[] = " " . $fields[$field] . "=" . $DB->escape($value);
                }

                unset($fields[$field]);
            } elseif (is_string($criteria) && '' != $criteria) {
                $whereAND[] = " fq.product_id=" . intval($criteria);
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
        $searchBits = array('fq.invoice_id', 'fq.customer_id', 'fq.customer_name', 'fq.transit_time', 'fq.lading_number', 'fq.container_number', 'fq.vessel_name', 'fq.master_number', 'fq.house_number', 'fq.general_notes');

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

    if (count($joins) > 0) {
        $sql .= " " . implode(" ", $joins);
    }

    if (count($whereAND) > 0) {
        $sql .= " WHERE " . implode(" AND ", $whereAND);
    }

    $sql .= " ORDER BY fq.id ASC";

    $query = $DB->query($sql);
    while ($row = $DB->fetch_array($query, MYSQLI_ASSOC)) {
        $results[] = $row['id'];
    }

    $DB->free_result($query);
    return $results;
}
function forwardersQuotesGetValueNEW($quote_id, $field)
{
    global $DB;

    $fields = getTableColumns(`highwaygroup`.`freight_services_quotes`);
    foreach ($fields as $key => $value) {
        $fields[$key] = 'fq.`' . $value . '`';
    }

    $fields = array_merge($fields, forwardersQuotesGetOptionList('ext_fields'));

    if (array_key_exists($field, $fields)) {
        $row = $DB->query_first("SELECT " . $fields[$field] . " FROM `highwaygroup`.`freight_services_quotes` fq WHERE fq.id=" . intval($quote_id), MYSQLI_NUM);
        return $row[0];
    }
    return null;
}
if (!function_exists('invoiceFromPost')) {
    /**
     * @param mixed[] $posted
     *
     * @return mixed[]
     */
    function invoiceFromPost($posted)
    {
        global $DB, $_GLOBALS;
        //Hack since invoice data is not seperated into posted invoice array
        if (is_array($posted['invoice'])) {
            foreach ($posted['invoice'] as $key => $value) {
                $posted[$key] = $value;
            }
        }

        $invoice_id = $posted['invoice_id'];
        $old_vals   = invoiceFromDatabase($invoice_id);
        $new_vals   = $old_vals;
        foreach ($old_vals as $key => $value) {
            if (array_key_exists($key, $posted)) {
                $new_vals[$key] = $posted[$key];
            }
        }

        $new_vals["staff_id"] = $_GLOBALS["staff"]["id"];

        $country_list = invoiceGetOptionList('bill_country');
        $bill_array   = array(('' != $new_vals['bill_company']) ? (trim($new_vals['bill_company'])) : (trim($new_vals['bill_name'])), ('' != $new_vals['bill_company'] && '' != $new_vals['bill_name']) ? ('Attn: ' . $new_vals['bill_name']) : '', trim($new_vals['bill_address']), ltrim(trim($new_vals['bill_city'] . ', ' . $new_vals['bill_state'] . ' ' . $new_vals['bill_postcode']), ','), trim($country_list[$new_vals['bill_country']]), "\r", //For some reason I can't just put a space here
            ('' != $new_vals['bill_phone']) ? trim('Ph: ' . $new_vals['bill_phone']) : '', ('' != $new_vals['bill_email']) ? trim('Email: ' . $new_vals['bill_email']) : '');
        foreach ($bill_array as $key => $value) {
            if ('' == $value) {
                unset($bill_array[$key]);
            }
        }
        if ($new_vals['handler_note'] !== $old_vals['handler_note']) {
            $new_vals['handler_date'] = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);
        }
        if ($new_vals['display_to_customer'] !== $old_vals['display_to_customer']) {
            $new_vals['is_display_customer'] = 1;
            $new_vals['display_to_date'] = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);
        }
        $new_vals['bill_address_full'] = implode("\n", $bill_array);
        if ('n' == $old_vals['products_located'] && 'y' == $new_vals['products_located']) {
            $new_vals['products_locate_date'] = formatDateTime();
            $new_vals['products_staff_id']    = $_GLOBALS['staff']['id'];
            $new_vals['backorder_flag']       = 'n';
        }
        if ('n' == $old_vals['ship_located'] && 'y' == $new_vals['ship_located']) {
            $new_vals['ship_locate_date'] = formatDateTime();
            $new_vals['ship_staff_id']    = $_GLOBALS['staff']['id'];
        }
        if ('n' == $old_vals['payments_located'] && 'y' == $new_vals['payments_located']) {
            $new_vals['payments_locate_date'] = formatDateTime();
            $new_vals['payments_staff_id']    = $_GLOBALS['staff']['id'];
            if ('payment' == $old_vals['invoice_action']) {
                $new_vals['invoice_action'] = 'shipping';
            }
        }
        if ('n' == $old_vals['supply_located'] && 'y' == $new_vals['supply_located']) {
            $new_vals['supply_locate_date'] = formatDateTime();
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
        if($old_vals['proforma_tracknumber'] == ''){
          $new_vals['freight_ship_date']     = date('Y-m-d');  
        }
        if ($invoice_id <= 0) {
            $new_vals['open_time']     = time();
            $new_vals['open_date']     = formatDateTime();
            $new_vals['proforma_date'] = formatDateTime();
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
        if (array_key_exists('shipto_flag', $posted)) {
            $shipto_id = 0;
            /*foreach($posted['shipto_flag'] as $key=>$value)
            if($value!='') $shipto_id=$key;*/
            $new_vals['ship_name']     = $posted['shipto_name'][$shipto_id];
            $new_vals['ship_company']  = $posted['shipto_company'][$shipto_id];
            $new_vals['ship_address']  = $posted['shipto_address'][$shipto_id];
            $new_vals['ship_city']     = $posted['shipto_city'][$shipto_id];
            $new_vals['ship_state']    = $posted['shipto_state'][$shipto_id];
            $new_vals['ship_postcode'] = $posted['shipto_postcode'][$shipto_id];
            $new_vals['ship_country']  = $posted['shipto_country'][$shipto_id];
            $new_vals['ship_phone']    = $posted['shipto_phone'][$shipto_id];
            $new_vals['ship_email']    = $posted['shipto_email'][$shipto_id];
            $ship_array                = array(('' != $new_vals['ship_company']) ? (trim($new_vals['ship_company'])) : (trim($new_vals['ship_name'])), ('' != $new_vals['ship_company'] && '' != $new_vals['ship_name']) ? ('Attn: ' . $new_vals['ship_name']) : '', trim($new_vals['ship_address']), ltrim(trim($new_vals['ship_city'] . ', ' . $new_vals['ship_state'] . ' ' . $new_vals['ship_postcode']), ','), trim($country_list[$new_vals['ship_country']]), "\r", //For some reason I can't just put a space here
                //($new_vals['ship_name']!='' && $new_vals['ship_company']!=$new_vals['ship_name'])?('Attn: '.$new_vals['ship_name']):'',
                ('' != $new_vals['ship_phone']) ? trim('Ph: ' . $new_vals['ship_phone']) : '', ('' != $new_vals['ship_email']) ? trim('Email: ' . $new_vals['ship_email']) : '');
            foreach ($ship_array as $key => $value) {
                if ('' == $value) {
                    unset($ship_array[$key]);
                }
            }

            $new_vals['ship_address_full'] = implode("\n", $ship_array);
        }
        if (array_key_exists('notify_flag', $posted)) {
            $member_notifyparty_id = 0;
            $new_vals['notify_name']     = $posted['notify_name'][$member_notifyparty_id];
            $new_vals['notify_company']  = $posted['notify_company'][$member_notifyparty_id];
            $new_vals['notify_address']  = $posted['notify_address'][$member_notifyparty_id];
            $new_vals['notify_city']     = $posted['notify_city'][$member_notifyparty_id];
            $new_vals['notify_state']    = $posted['notify_state'][$member_notifyparty_id];
            $new_vals['notify_postcode'] = $posted['notify_postcode'][$member_notifyparty_id];
            $new_vals['notify_country']  = $posted['notify_country'][$member_notifyparty_id];
            $new_vals['notify_phone']    = $posted['notify_phone'][$member_notifyparty_id];
            $new_vals['notify_email']    = $posted['notify_email'][$member_notifyparty_id];

            $notify_array                = array(('' != $new_vals['notify_company']) ? (trim($new_vals['notify_company'])) : (trim($new_vals['notify_name'])), ('' != $new_vals['notify_company'] && '' != $new_vals['notify_name']) ? ('Attn: ' . $new_vals['notify_name']) : '', trim($new_vals['notify_address']), ltrim(trim($new_vals['notify_city'] . ', ' . $new_vals['notify_state'] . ' ' . $new_vals['notify_postcode']), ','), trim($country_list[$new_vals['notify_country']]), "\r", //For some reason I can't just put a space here
                ('' != $new_vals['notify_phone']) ? trim('Ph: ' . $new_vals['notify_phone']) : '', ('' != $new_vals['notify_email']) ? trim('Email: ' . $new_vals['notify_email']) : '');
            foreach ($notify_array as $key => $value) {
                if ('' == $value) {
                    unset($notify_array[$key]);
                }
            }
            $new_vals['notify_address_full'] = implode("\n", $notify_array);
        }
        /* Disabling the auto script for Voltage, Frequency and Plug Adapter..
        //Autocalculate Plug Type
        $row=$DB->query_first("SELECT voltage, v_comments, plugs FROM `world`.`country` WHERE code=".formatSQLString($new_vals['ship_country']));
        if(array_key_exists('voltage_id',$new_vals) && $new_vals['voltage_id']<1)
        {
        $voltages=getOutletVoltages();
        $new_vals['voltage_id']=findClosestKey($voltages,$row['voltage']);
        }
        if(array_key_exists('frequency_id',$new_vals) && $new_vals['frequency_id']<1)
        {
        $frequencies=getOutletFrequencies();
        $new_vals['frequency_id']=findClosestKey($frequencies,$row['v_comments']);
        }
        if(array_key_exists('plug_id',$new_vals) && $new_vals['plug_id']<1)
        {
        $outlets=getOutletPlugs();
        $new_vals['plug_id']=findClosestKey($outlets,trim(str_replace(',',' ',$row['plugs'])));
        }
         */
        if ($invoice_id <= 0) {
            unset($new_vals['invoice_id']);
        }

        if (in_array($new_vals['invoice_status'], array('inquiry', 'closed'))) {
            if (in_array($posted['invoice_action'], array('proforma', 'sales'))) {
                $new_vals['invoice_status'] = $posted['invoice_action'];
                $new_vals['invoice_action'] = 'payment';
            } elseif (in_array($posted['invoice_action'], array('nointerest', 'nohelp', 'cancelled'))) {
                $new_vals['invoice_status'] = 'closed';
            } elseif (in_array($posted['invoice_action'], array('feedback', 'working'))) {
                $new_vals['invoice_status'] = 'inquiry';
            }
        } elseif (in_array($new_vals['invoice_status'], array('return', 'completed'))) {
            if (in_array($posted['invoice_action'], array('return_completed', 'return_cancelled'))) {
                $new_vals['invoice_status'] = 'completed';
            } elseif (in_array($posted['invoice_action'], array('working'))) {
                $new_vals['invoice_status'] = 'inquiry';
            }else {
                $new_vals['invoice_status'] = 'return';
            }
        } else {
            if (in_array($posted['invoice_action'], array('nointerest', 'nohelp', 'cancelled', 'returned'))) {
                $new_vals['invoice_status'] = 'cancelled';
            } elseif (in_array($posted['invoice_action'], array('processed'))) {
                $new_vals['invoice_status'] = 'processed';
            } elseif (in_array($posted['invoice_action'], array('shipped','shipped-loan', 'shipping', 'approval'))) {
                $new_vals['invoice_status'] = 'sales';
            } elseif (in_array($posted['invoice_action'], array('payment'))) {
                if ('proforma' == $old_vals['invoice_status']) {
                    $new_vals['invoice_status'] = 'proforma';
                } else {
                    $new_vals['invoice_status'] = 'sales';
                }
            }
            if ('shipped' == $posted['invoice_action'] && 'processed' == $old_vals['invoice_status']) {
                $new_vals['invoice_status'] = 'processed';
            }
            if ('shipped-loan' == $posted['invoice_action'] && 'processed' == $old_vals['invoice_status']) {
                $new_vals['invoice_status'] = 'processed';
            }
        }
        if (in_array($old_vals['invoice_status'], array('proforma', 'sales', 'processed')) && in_array($new_vals['invoice_status'], array('proforma', 'sales', 'processed'))) {
            foreach (array('bill_address', 'bill_company', 'bill_country', 'bill_email', 'bill_name', 'charges_offer', 'default_supplier', 'invoice_currency', 'invoice_offer', 'paymenttype_id', 'products_offer', 'proforma_date', 'ship_address', 'ship_country', 'ship_offer', 'terms_id') as $key) {
                if (formatCleanString($new_vals[$key]) != formatCleanString($old_vals[$key])) {
                    $new_vals['do_revision'] = true;
                }
            }
        }
        if ('with_packing' == $posted['packing_type'] || 'Without_packing' == $posted['packing_type']) {
                $new_vals['packing_type'] = $posted['packing_type'];
        }else{
            $new_vals['packing_type'] = NULL;
        }
        if (!isset( $posted['manual_odering'])) {
            $new_vals['manual_odering'] = 'n';
        }
        return $new_vals;
    }
}

if (!function_exists('invoiceGetLock')) {
    /**
     * @param int $id
     *
     * @return mixed[]|false
     */
    function invoiceGetLock($id)
    {
        global $DB;

        if ($row = $DB->query_first(sprintf(
            "SELECT *, UNIX_TIMESTAMP(`expiry_date`) AS `expiry_time` FROM `highwaygroup`.`generic_locks` WHERE `section_name` = 'invoice' AND `generic_id` = %d AND `expiry_date` > NOW() LIMIT 1",
            $id
        ), MYSQLI_ASSOC)) {
            if ($row['expiry_time'] < time()) {
                $row = false;
            }
            return $row;
        }

        return false;
    }
}

if (!function_exists("invoiceGetLocks")) {
    /**
     * @return mixed[][]
     */
    function invoiceGetLocks()
    {
        global $DB;

        static $result = null;
        if (!is_null($result)) {
            return $result;
        }

        $result = array();
        if ($query = $DB->query(sprintf(
            "SELECT *, UNIX_TIMESTAMP(`expiry_date`) AS `expiry_time` FROM `highwaygroup`.`generic_locks` WHERE `section_name` = 'invoice' AND `expiry_date` > NOW()"
        ))) {
            while ($row = $DB->fetch_assoc($query)) {
                $result[$row["generic_id"]] = $row;
            }
            $DB->free_result($query);
        }

        return $result;
    }
}

if (!function_exists("invoiceGetOptionList")) {
    /**
     * @param string $table_name
     *
     * @return mixed
     */
    function invoiceGetOptionList($table_name)
    {
        static $option_cache = array();
        if (array_key_exists($table_name, $option_cache)) {
            return $option_cache[$table_name];
        }

        global $DB;
        $table       = '';
        $key         = '';
        $value       = '';
        $option_list = array();
        switch ($table_name) {
            case 'products_located':
            case 'ship_located':
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

            case 'list_invoice_status':
                $table  = sprintf("%s AS `i`", INVOICE_TABLENAME);
                $key    = '`i`.`invoice_id`';
                $fields = invoiceGetOptionList("all_fields");
                $value  = $fields[substr($table_name, 5)];
                break;

            case 'invoice_status':
                return array(
                    'inquiry'   => 'Inquiry',
                    'closed'    => 'Closed',
                    'proforma'  => 'Proforma',
                    'sales'     => 'Sales',
                    'processed' => 'Processed',
                    'cancelled' => 'Cancelled',
                    'return'    => 'Returning',
                    'completed' => 'Returned',
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
                    'shipped-loan'     => 'Goods Shipped – Loan',
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
                    'Working_on_solution' => 'Working On Solution',
                    'waiting_customer' => 'Wating Customer Feedback'
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
                $table = '`highwaygroup`.`company_info`';
                $key   = '`company_id`';
                $value = 'LEFT(`company_invoicesuffix`, 2)';
                break;

            case 'seaport_from':
            case 'seaport_to':
                $table = '`world`.`seaports`';
                $key   = 'id';
                $value = 'name';
                break;
            case 'freight_tracking':
                $table = '`freight_tracking`';
                $key   = 'id';
                $value = 'freight';
                break;
            case 'freight_tracking_url':
                $table = '`freight_tracking`';
                $key   = 'id';
                $value = 'url';
                break;    
            case 'site_id':
                $table = '`highwaygroup`.`company_info` ORDER BY company_name ASC';
                $key   = 'company_id';
                $value = 'company_name';
                break;

            case 'charge_id':
                $table = '`highwaygroup`.`charges`';
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

                    0x0007 => 'Payment via HKD Bank Account',

                    0x0008 => 'Payment via JPY Bank Account',

                    0x0009 => 'Payment via USD Bank Account',
                     */
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

                    0x0009 => 'Cash Payment',

                    0x000A => 'Payment via Cheque',
                    0x000B => 'Payment via BPay',
                    0x000C => 'Payment via Amazon',
                    /*
                    0x000D => 'Payment via PayPal',
                     */
                    0x200D => 'Payment via ASP PayPal',
                    0x110D => 'Payment via HGR PayPal',

                    0x000E => 'Shortpayment Writeoff',
                    0x000F => 'Payment via Trade Account',

                    0x0010 => 'Payment via BankWest Bank Account',
                    0x0011 => 'Payment via Commonwealth Bank Account',

                    0x0012 => 'Refund',
                );

            case 'payment_terms_id':
                $table = '`highwaygroup`.`purchase_orders_payment`';
                $key   = 'id';
                $value = 'title';
                break;

            case 'invoice_suffix':
                $table = '`highwaygroup`.`company_info`';
                $key   = 'company_id';
                $value = 'company_invoicesuffix';
                break;

            case 'harmonised_code':
                $table = '`world`.`harmonised_codes`';
                $key   = 'code';
                $value = 'title';
                $type = 'product_type';
                $option_list = array();
                if ($query = $DB->query(sprintf(
                    "SELECT %s, %s ,%s FROM %s WHERE `flag`=1",
                    $key,
                    $value,
                    $type,
                    $table
                ))) {
                    while ($row = $DB->fetch_assoc($query)) {
                        $option_list[reset($row)] = end($row);
                    }
                    $DB->free_result($query);
                }
                return $option_list;
                break;
            case 'harmonised_code_with_type':
                $table = '`world`.`harmonised_codes`';
                $key   = 'code';
                $value = 'title';
                $type = 'product_type';
                $option_list = array();
                if ($query = $DB->query(sprintf(
                    "SELECT %s, %s ,%s FROM %s WHERE `flag`=1",
                    $key,
                    $value,
                    $type,
                    $table
                ))) {
                    while ($row = $DB->fetch_assoc($query)) {
                        $option_list[reset($row)] = $row;
                    }
                    $DB->free_result($query);
                }
                return $option_list;
                break;    
            case 'supplier_id':
                return supplierGetOptionList("id");

            case 'ship_id':
            case 'ship_supplier_id':
                //TODO
                break;

            case 'goods_packed_by':
            case 'goods_shipped_by':
            case 'handler_id':
            case 'payments_staff_id':
            case 'products_staff_id':
            case 'ship_staff_id':
            case 'staff_id':
                return getStaffList();

            case 'forwarder_country':
            case 'bill_country':
            case 'notify_country':
            case 'origin_country':
            case 'ship_country':
            case 'country_from':
            case 'country_to':
            case 'supply_country':
                return getCountryList(false);

            case 'warranty_terms_id':
            case 'waranty_terms_id':
            case "product_warranty":
                return getWarrantyTermsList();

            case 'invoice_currency':
                return getCurrencyList();

            case 'paymenttype_id':
                return getPaymentMethodsList();

            case 'terms_id':
                return getTermsList();

            case 'voltage_id':
                return getOutletVoltages();

            case 'voltage_id2':
                return getInvoiceVoltages();

            case 'frequency_id':
                return getOutletFrequencies();

            case 'plug_id':
                return getOutletPlugs();

            case 'packing_requirement':
                return invoicePackingRequirementList();

            case 'condition':
                return getProductCondition();

            case 'joined_fields':
                $joins = array('INNER JOIN `highwaygroup`.`invoice_payments` AS `ip` ON ip.invoice_id=i.invoice_id' => getTableColumns('`highwaygroup`.`invoice_payments`'));
                foreach (getTableColumns(INVOICE_TABLENAME) as $field) {
                    foreach ($joins as $join => $rows) {
                        unset($joins[$join][$field]);
                    }
                }
                return $joins;

            case 'all_fields':
                return array_merge(invoiceGetOptionList('base_fields'), invoiceGetOptionList('ext_fields'));

            case 'all_fields_types':
                return array_merge(invoiceGetOptionList('base_fields_types'), invoiceGetOptionList('ext_fields_types'));

            case 'base_fields':
                $fields = array();
                foreach (getTableColumns(INVOICE_TABLENAME) as $key) {
                    $fields[$key] = '`i`.`' . $key . '`';
                }
                return $fields;

            case 'base_fields_types':
                return getTableFields(INVOICE_TABLENAME);

            case 'extra_fields':
            case 'ext_fields':
                return array(
                    "needby_time"          => "UNIX_TIMESTAMP(`i`.`needby_date`)",
                    "payments_locate_time" => "UNIX_TIMESTAMP(`i`.`payments_locate_date`)",
                    "products_locate_time" => "UNIX_TIMESTAMP(`i`.`products_locate_date`)",
                    "ship_locate_time"     => "UNIX_TIMESTAMP(`i`.`ship_locate_date`)",
                    "supply_locate_time"   => "UNIX_TIMESTAMP(`i`.`supply_locate_date`)",
                    "write_time"           => "UNIX_TIMESTAMP(`i`.`write_date`)",

                    //'invoice_id' => "i.id",
                    'products_located'     => "IF(products_locate_date IS NULL OR products_locate_date = '0000-00-00 00:00:00','n','y')",
                    'ship_located'         => "IF(ship_locate_date IS NULL OR ship_locate_date = '0000-00-00 00:00:00','n','y')",
                    'payments_located'     => "IF(payments_locate_date IS NULL OR payments_locate_date = '0000-00-00 00:00:00','n','y')",
                    'open_year'            => "IF(open_date=0,0,UNIX_TIMESTAMP(DATE_FORMAT(open_date,'%Y-01-01 00:00:00')))",
                    'open_quarter'         => "IF(open_date=0,0,UNIX_TIMESTAMP(CONCAT_WS('',DATE_FORMAT(open_date,'%Y-'),LPAD(QUARTER(open_date)*3-2,2,'0'),'-01 00:00:00')))",
                    'open_month'           => "IF(open_date=0,0,UNIX_TIMESTAMP(DATE_FORMAT(open_date,'%Y-%m-01 00:00:00')))",
                    'open_week'            => "IF(open_date=0,0,UNIX_TIMESTAMP(DATE_FORMAT(DATE_SUB(open_date, INTERVAL WEEKDAY(open_date) DAY),'%Y-%m-%d 00:00:00')))",
                    'open_day'             => "IF(open_date=0,0,UNIX_TIMESTAMP(DATE_FORMAT(open_date,'%Y-%m-%d 00:00:00')))",
                    'open_time'            => "UNIX_TIMESTAMP(open_date)",
                    'proforma_time'        => "UNIX_TIMESTAMP(proforma_date)",

                    'tax_month'            => "IF (`tax_date` IS NULL OR `tax_date` = '0000-00-00', IF (`payments_locate_date` IS NULL OR `payments_locate_date` = '0000-00-00 00:00:00', IF (`proforma_date` IS NULL OR `proforma_date` = '0000-00-00', IF (`open_date` IS NULL OR `open_date` = '0000-00-00', 0, UNIX_TIMESTAMP(DATE_FORMAT(`open_date`, '%Y-%m-01 00:00:00'))), UNIX_TIMESTAMP(DATE_FORMAT(`proforma_date`, '%Y-%m-01 00:00:00'))), UNIX_TIMESTAMP(DATE_FORMAT(`payments_locate_date`, '%Y-%m-01 00:00:00'))), UNIX_TIMESTAMP(DATE_FORMAT(`tax_date`, '%Y-%m-01 00:00:00')))",
                    'tax_year'             => "IF (`tax_date` IS NULL OR `tax_date` = '0000-00-00', IF (`payments_locate_date` IS NULL OR `payments_locate_date` = '0000-00-00 00:00:00', IF (`proforma_date` IS NULL OR `proforma_date` = '0000-00-00', IF (`open_date` IS NULL OR `open_date` = '0000-00-00', 0, UNIX_TIMESTAMP(DATE_FORMAT(`open_date`, '%Y-01-01 00:00:00'))), UNIX_TIMESTAMP(DATE_FORMAT(`proforma_date`, '%Y-01-01 00:00:00'))), UNIX_TIMESTAMP(DATE_FORMAT(`payments_locate_date`, '%Y-01-01 00:00:00'))), UNIX_TIMESTAMP(DATE_FORMAT(`tax_date`, '%Y-01-01 00:00:00')))",

                    "ship_product_str"     => "IF(`ship_id` != 0, CONCAT('F', LPAD(`ship_id`, 2, '0'), IF(`ship_insurance` = 'y', 'I', 'N'), " . /*IF(`type` = 'truck', 'T', ''), */"IF(`ship_registered` = 'y', 'R', ''), IF(`ship_receipted` = 'y', 'A', ''), IF(`ship_express` = 'y', 'E', '')), '')",

                    'tax_time'             => "UNIX_TIMESTAMP(`tax_date`)",
                    'tax_fyear'            => "IF (`tax_date` IS NULL OR `tax_date` = '0000-00-00', 0, UNIX_TIMESTAMP(CONCAT(YEAR(`tax_date`) - IF(MONTH(`tax_date`) > 6, 0, 1), '-07-01 00:00:00')))",
                );

            case 'ext_fields_types':
                return array();
        }
        if ('' != $table) {
            $option_list = array();
            if ($query = $DB->query(sprintf(
                "SELECT %s, %s FROM %s",
                $key,
                $value,
                $table
            ))) {
                while ($row = $DB->fetch_assoc($query)) {
                    $option_list[reset($row)] = end($row);
                }
                $DB->free_result($query);
            }
        }
        $option_cache[$table_name] = $option_list;
        return $option_list;
    }
}

if (!function_exists("invoiceGroupCount")) {
    /**
     * @param int[]           $item_list
     * @param string|string[] $group_by
     * @param bool            $split_sets
     *
     * @return mixed
     */
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
        $field_types = invoiceGetOptionList('all_fields_types');
        $fields      = invoiceGetOptionList('all_fields');
        foreach ($group_by as $field) {
            if (array_key_exists($field, $fields)) {
                $field_list[$field] = $fields[$field] . " AS `" . $field . "`";
            }
        }

        foreach (invoiceGetOptionList('joined_fields') as $join => $fields) {
            foreach ($fields as $field => $field_string) {
                if (in_array($field, $group_by)) {
                    $join_list[$join]            = $join;
                    $field_list[$field]          = $fields[$field] . " AS `" . $field . "`";
                    $field_types[$field][1]      = 'set(...)';
                    $field_types[$field]['Type'] = 'set(...)';
                }
            }
        }

        $field_list = array_unique($field_list);
        $group_list = $field_list;
        unset($group_list['row_count']);
        if (count($group_list) == 0) {
            return array(count($item_list));
        }

        $results = array();
        if ($query = $DB->query(sprintf(
            "SELECT %s FROM %s AS `i` %s WHERE %s GROUP BY `%s`",
            implode(', ', $field_list),
            INVOICE_TABLENAME,
            implode(' ', $join_list),
            invoiceSQLList($item_list),
            implode('`, `', array_keys($group_list))
        ))) {
            while ($row = $DB->fetch_assoc($query)) {
                $pointers = array(&$results);
                foreach ($group_by as $group) {
                    $key = $row[$group];
                    if ('' == $key) {
                        $key = null;
                    }

                    $new_pointers = array();
                    foreach ((substr($field_types[$group]['Type'], 0, 4) == 'set(' && $split_sets) ? explode(',', $key) : array($key) as $key2) {
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
        }

        return $results;
    }
}

if (!function_exists("invoiceHistoryEmailsFromDatabase")) {
    /**
     * @param int $invoice_id
     *
     * @return string[]
     */
    function invoiceHistoryEmailsFromDatabase($invoice_id)
    {
        // This function generates the email list for online use.
        $invoice_history = invoiceHistorySearch(array('invoice_id' => $invoice_id, 'message_type_id' => array('2', '3', '12')));
        $handler_id      = invoiceGetValue($invoice_id, 'handler_id');

        $staff_list = invoiceGetOptionList('handler_id');
        $email_list = array();
        if ($handler_id > 0) {
            $email_list[$handler_id] = $staff_list[$handler_id];
        }

        foreach ($invoice_history as $history_id) {
            $history = invoiceHistoryFromDatabase($history_id);
            if (is_numeric($history['message_sender'])) {
                $email_list[$history['message_sender']] = $staff_list[intval($history['message_sender'])];
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

if (!function_exists("invoicePackingRequirementList")) {
    /**
     * @return string[]
     */
    function invoicePackingRequirementList()
    {
        global $DB;

        static $result = null;
        if (!is_null($result)) {
            return $result;
        }

        $result = array();
        if ($query = $DB->query(sprintf(
            "SELECT `id`, `packing` FROM `highwaygroup`.`invoice_packing`"
        ))) {
            while ($row = $DB->fetch_assoc($query)) {
                $result[$row['id']] = $row['packing'];
            }
            $DB->free_result($query);
        }

        return $result;
    }
}

if (!function_exists("invoiceSearch")) {
    /**
     * @param mixed $criteria
     * @param int   $time_from
     * @param int   $time_till
     *
     * @return int[]
     */
    function invoiceSearch($criteria = '', $time_from = 0, $time_till = 0)
    {
        global $DB;
        $results = array();
        $fields  = getTableColumns(INVOICE_TABLENAME);
        foreach ($fields as $k => $v) {
            $fields[$k] = '`i`.`' . $v . '`';
        }

        $fields   = array_merge($fields, invoiceGetOptionList('ext_fields'));
        $whereAND = array();
        if (is_array($criteria)) {
            foreach ($criteria as $field => $value) {
                if (array_key_exists($field, $fields)) {
                    if (is_array($value)) {
                        if (count($value) > 0) {
                            $whereOR = array();
                            foreach ($value as $sub_value) {
                                $whereOR[] = $fields[$field] . " = " . formatSQLString($sub_value);
                            }

                            $whereAND[] = "(" . implode(" OR ", $whereOR) . ")";
                        } else {
                            return array();
                        }
                    } else {
                        $whereAND[] = $fields[$field] . "=" . formatSQLString($value);
                    }

                    unset($fields[$field]);
                }
            }
        } elseif (is_string($criteria) && '' != $criteria) {
            $whereAND[] = "`i`.`invoice_status` = " . formatSQLString($criteria);
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
            //$searchBits=array('`i`.`invoice_id`', '`i`.`member_id`', '`i`.`bill_name`', '`i`.`bill_company`', '`i`.`bill_email`', '`i`.`bill_address`');
            $searchBits = array(
                '`i`.`invoice_id`',
                '`i`.`bill_name`',
                '`i`.`bill_company`',
                '`i`.`bill_email`',
                '`i`.`proforma_tracknumber`',
                '`i`.`amazon_order_id`',
            );
            $whereOR    = array();
            foreach ($search_for as $findme) {
                foreach ($searchBits as $key) {
                    if (trim($findme) != '') {
                        $whereOR[] = $key . " LIKE '%" . str_replace('%', '\%', str_replace('_', '\_', addslashes($findme))) . "%'";
                    }
                }
            }
            if (count($whereOR) > 0) {
                $whereAND[] = "(" . implode(" OR ", $whereOR) . ")";
            }
        }
        if (0 != $time_from || array_key_exists('time_from',$criteria)) {

            $time_from = array_key_exists('time_from',$criteria)? $criteria['time_from'] : $time_from;
            $whereAND[] = 'i.open_date >=' . formatSQLDateTime($time_from);
        }

        if (0 != $time_till || array_key_exists('time_till',$criteria)) {

             $time_till = array_key_exists('time_till',$criteria) ? $criteria['time_till'] : $time_till;
            $whereAND[] = 'i.open_date <' . formatSQLDateTime($time_till);
        }   
        if ($query = $DB->query(sprintf(
            "SELECT `i`.`invoice_id` FROM %s AS `i`%s ORDER BY `i`.`invoice_id` ASC",
            INVOICE_TABLENAME,
            (count($whereAND)) ? sprintf(" WHERE %s", implode(" AND ", $whereAND)) : ""
        ))) {
            while ($row = $DB->fetch_assoc($query)) {
                $results[] = $row['invoice_id'];
            }
            $DB->free_result($query);
        }

        return $results;
    }
}

if (!function_exists('invoiceSetLock')) {
    /**
     * @param int $id
     * @param int $staff_id
     * @param int $timeout
     *
     * @return void
     */
    function invoiceSetLock($id, $staff_id = 0, $timeout = 120)
    {
        global $DB;
        $section_name = formatSQLString("invoice");
        if (0 != $staff_id) {
            $DB->query(sprintf("UPDATE `highwaygroup`.`generic_locks` SET `expiry_date` = %s, `modified` = NOW() WHERE `section_name` = %s AND `generic_id` = %d AND `staff_id` = %d AND `expiry_date` > NOW() LIMIT 1", formatSQLDateTime(time() + $timeout), $section_name, $id, $staff_id));
            if ($DB->affected_rows() == 0) {
                $DB->query(sprintf("INSERT INTO `highwaygroup`.`generic_locks` (`section_name`, `generic_id`, `staff_id`, `expiry_date`, `created`, `modified`) VALUES (%s, %d, %d, %s, NOW(), NOW())", $section_name, $id, $staff_id, formatSQLDateTime(time() + $timeout)));
            }
        } else {
            $DB->query(sprintf("UPDATE `highwaygroup`.`generic_locks` SET `expiry_date` = NOW(), `modified` = NOW() WHERE `section_name` = %s AND `generic_id` = %d AND `expiry_date` > NOW()", $section_name, $id));
        }
    }
}

if (!function_exists("invoiceSort")) {
    /**
     * @param int[]  $invoice_list
     * @param string $order_by
     *
     * @return int[]
     */
    function invoiceSort($invoice_list, $order_by = 'invoice_id')
    {
        global $DB;
        if (!is_array($invoice_list) || count($invoice_list) < 2) {
            return $invoice_list;
        }

        $fields = getTableFields(INVOICE_TABLENAME);

        if (!array_key_exists($order_by, $fields)) {
            return $invoice_list;
        }

        $result = array();
        if ($query = $DB->query(sprintf(
            "SELECT `i`.`invoice_id` FROM %s AS `i` WHERE %s ORDER BY `%s` ASC",
            INVOICE_TABLENAME,
            invoiceSQLList($invoice_list),
            $order_by
        ))) {
            while ($row = $DB->fetch_assoc($query)) {
                $result[] = $row['invoice_id'];
            }
            $DB->free_result($query);
        }

        return $result;
    }
}

if (!function_exists("invoiceSQLList")) {
    /**
     * @param int[]  $invoice_list
     * @param string $key
     *
     * @return string
     */
    function invoiceSQLList($invoice_list, $key = "`i`.`invoice_id`")
    {
        if (count($invoice_list) == 0) {
            return "1";
        }

        $whereOR = array();
        $whereIN = array();
        foreach (list_to_partitions($invoice_list) as $k => $v) {
            if ($k > $v + 2) {
                $whereOR[] = "(" . $key . " >= " . $v . " AND " . $key . " <= " . $k . ")";
            } else {
                foreach (range($v, $k) as $k2) {
                    $whereIN[] = $k2;
                }
            }
        }
        if (count($whereIN) > 1) {
            $whereOR[] = $key . " IN(" . implode(", ", $whereIN) . ")";
        } elseif (count($whereIN) == 1) {
            $whereOR[] = $key . " = " . implode(" OR " . $key . " = ", $whereIN);
        }

        return implode(" OR ", $whereOR);
    }
}

if (!function_exists("invoiceStatusList")) {
    /**
     * @return string[]
     */
    function invoiceStatusList()
    {
        global $DB;

        $result = array();
        if ($row = $DB->query_first(sprintf(
            "SHOW COLUMNS FROM %s LIKE 'invoice_status'",
            INVOICE_TABLENAME
        ), MYSQLI_NUM)) {
            $result = explode("','", substr($row[1], 6, -2));
            $result = array_combine($result, array_map('ucfirst', $result));
        }

        return $result;
    }
}

if (!function_exists("invoiceSumValue")) {
    /**
     * @param int[] $invoice_list
     * @param string $field
     *
     * @return mixed
     */
    function invoiceSumValue($invoice_list, $field)
    {
        global $DB;
        if (count($invoice_list) < 1) {
            return 0;
        }

        $fields = getTableFields(INVOICE_TABLENAME);

        if (array_key_exists($field, $fields)) {
            if ($row = $DB->query_first(sprintf(
                "SELECT SUM(`%s`) AS `sum_value` FROM %s WHERE `invoice_id` IN (%s)",
                $field,
                INVOICE_TABLENAME,
                implode(', ', $invoice_list)
            ), MYSQLI_ASSOC)) {
                return $row['sum_value'];
            }
        }

        return null;
    }
}

if (!function_exists("invoiceTemplateVars")) {
    /**
     * @param mixed[]   $invoice
     * @param mixed[][] $invoice_products
     * @param mixed[][] $invoice_charges
     *
     * @return mixed[]
     */
    function invoiceTemplateVars($invoice = array(), $invoice_products = array(), $invoice_charges = array(),$invoice_gifts = array())
    {
        global $DB;

        $results = array();

        foreach (getTableColumns(INVOICE_TABLENAME) as $key) {
            $results['[' . strtoupper($key) . ']'] = '';
        }

        foreach (array(
            'BILL_ADDRESS_FULL',
            'COURIER_NAME',
            'PAYMENT_DETAILS',
            'PAYMENT_DETAILS_HTML',
            'SHIP_ADDRESS_FULL',
            'STAFF_NAME',
            'STAFF_FNAME',
            'STAFF_FINITIAL',
            'STAFF_LNAME',
            'STAFF_LINITIAL',
            'TRACK_NOTES',
            'TRACKING_AVAILABLE',
            'TRACKING_NUMBER',
            'TRACKING_WEBSITE',
        ) as $key) {
            $results['[' . strtoupper($key) . ']'] = '';
        }

        $results['[BILL_ADDRESS_FULL_HTML]'] = nl2br(htmlspecialchars(strip_tags($invoice['bill_address_full'])));
        $results['[SHIP_ADDRESS_FULL_HTML]'] = nl2br(htmlspecialchars($invoice['ship_address_full']));
        $results['[REQUEST_DATE]']           = date('jS F, Y', time());
        $results['[REQUEST_TIME]']           = date('g:i a', time());
        $temp                                = explode(' ', $invoice['bill_name']);
        $results['[FIRST_NAME]']             = trim(reset($temp));
        $temp                                = explode(' ', $invoice['bill_name']);
        $results['[LAST_NAME]']              = trim(end($temp));

        $invoice_labels = $DB->query_first(sprintf(
            "SELECT * FROM `highwaygroup`.`company_info` WHERE `company_id` = %d LIMIT 1",
            $invoice['site_id']
        ), MYSQLI_ASSOC);
        if (!$invoice_labels) {
            $invoice_labels = $DB->query_first(sprintf(
                "SELECT * FROM `highwaygroup`.`company_info` WHERE `company_id` = 11 LIMIT 1"
            ), MYSQLI_ASSOC);
        }

        if ('AU' == $invoice['ship_country'] && 'AU' == $invoice['bill_country']) {
            $invoice_labels['company_signature'] = $invoice_labels['company_signature_au'];
        }

        $results['[HTML_COMPANY_SIGNATURE]'] = nl2br(htmlspecialchars($invoice_labels['company_signature']));
        foreach ($invoice_labels as $key => $value) {
            $results['[' . strtoupper($key) . ']'] = $value;
        }

        $website_info = $DB->query_first(sprintf(
            "SELECT * FROM `highwaygroup`.`websites` WHERE `id` = %d LIMIT 1",
            $invoice['site_id']
        ), MYSQLI_ASSOC);
        $temp                   = explode('/', str_replace('https://', '', $website_info['url']));
        $results['[HTTP_HOST]'] = array_shift($temp);

        foreach ($invoice as $key => $value) {
            $results['[' . strtoupper($key) . ']'] = $value;
        }

        $results['[INVOICE_NUMBER]'] = invoiceFormatNumber(intval($invoice['invoice_id']), 0, $invoice['site_id']);

        if (array_key_exists("proforma_tracknumber", $invoice) && '' != $invoice['proforma_tracknumber']) {
            $results['[TRACKING_NUMBER]'] = $invoice['proforma_tracknumber'];
        }

        /*
        if (array_key_exists("ship_supplier_name", $invoice) && '' != $invoice['ship_supplier_name']) {
            $nameStr = $invoice['ship_product_name'];
            if (trim($invoice['ship_supplier_name']) != trim($invoice['ship_product_name'])) {
                $nameStr = $invoice['ship_supplier_name'] . ' - ' . $invoice['ship_product_name'];
            }
            $results['[COURIER_NAME]'] = $nameStr;
        }
        */
        if (array_key_exists('ship_product_name', $invoice) && !empty($invoice['ship_product_name'])) {
            $results['[COURIER_NAME]'] = $invoice['ship_product_name'];
        }

        
        if (array_key_exists('fright_tracking_service', $invoice) && $invoice['fright_tracking_service'] > 0 ) {
            $freight_service_list = invoiceGetOptionList('freight_tracking');
            $results['[COURIER_NAME]'] = $freight_service_list[$invoice['fright_tracking_service']];
        }

        if (array_key_exists("ship_id", $invoice) && $invoice['ship_id'] > 0) {
            $tracking                      = getFreightServiceDetails($invoice['ship_id'], array('track_website'));
            $results['[TRACKING_WEBSITE]'] = ('' != $tracking['track_website']) ? 'Tracking Website: ' . $tracking['track_website'] : 'Goods cannot be tracked online with this courier.';
            $results['[TRACK_NOTES]']      = (!in_array($invoice['site_id'], array(20, 26))) ? 'Please note that we are located in regional Australia and it may take up to 24hours for the tracking information to be displayed on courier websites.' : '';
        }
        if (array_key_exists('frieght_traking_url', $invoice) && !empty($invoice['frieght_traking_url'])) {
            $results['[TRACKING_WEBSITE]'] = $invoice['frieght_traking_url'];
        }
        if ('' != $invoice['paymenttype_id']) {
            $payment_types = getPaymentMethodsList();
            $payment_data  = $DB->query_first(sprintf(
                "SELECT * FROM `highwaygroup`.`payment_methods` WHERE `site_id` = %d AND `payment_method_id` = %s LIMIT 1",
                $invoice['site_id'],
                formatSQLString($invoice['paymenttype_id'])
            ), MYSQLI_ASSOC);
            $results['[PAYMENT_METHOD]'] = $payment_types[$invoice['paymenttype_id']];
            if ('' != $payment_data['payment_method_details']) {
                $results['[PAYMENT_DETAILS]']      = $payment_data['payment_method_details'];
                $results['[PAYMENT_DETAILS_HTML]'] = nl2br(htmlspecialchars($payment_data['payment_method_details']));
            }
        }

        $results['[PAYMENT_APPROVAL]']      = 'Awaiting';
        $results['[PAYMENT_APPROVAL_HTML]'] = '<span style="color:red;"><b>Awaiting</b></span>';
        if ("payment" == $invoice["invoice_action"]) {
            $results['[PAYMENT_APPROVAL]']      = 'Awaiting Payment';
            $results['[PAYMENT_APPROVAL_HTML]'] = '<span style="color:red;"><b>Awaiting Payment</b></span>';
        }
        if("approval" == $invoice['invoice_action']) {
            $results['[PAYMENT_APPROVAL]']      = 'Awaiting Payment Approval';
            $results['[PAYMENT_APPROVAL_HTML]'] = '<span style="color:green;"><b>Awaiting Payment Approval</b></span>';
        }
        if ("y" == $invoice["payments_located"]) {
            $results['[PAYMENT_APPROVAL]']      = 'Approved';
            $results['[PAYMENT_APPROVAL_HTML]'] = '<span style="color:green;"><b>Approved</b></span>';
        }

        

        $payment_method = $payment_types[$invoice['paymenttype_id']];

        $payment_status = $results['[PAYMENT_APPROVAL_HTML]'];

        $payment_details = '';

        // if ($row = $DB->query_first(sprintf(
        //     "SELECT * FROM `highwaygroup`.`payment_methods` WHERE `site_id` = %d AND `payment_method_id` = %s LIMIT 1",
        //     $invoice['site_id'],
        //     $invoice['paymenttype_id']
        // ), MYSQLI_ASSOC)) {
        //     $payment_details = $row['payment_method_id'];
        // }
        
        $html_str  = '';
        if("" != $payment_method) {
            $html_str .= '<table border="1" cellspacing="0" cellpadding="2" style="margin:auto;border-collapse:collapse;border:1px solid #ccc;font-size:inherit;">';
            $html_str .= '<tr><th style="background:#eee;color:#333">Payment Method :</th><td style="color:#777;">'.$payment_method.'</td></tr>';
            $html_str .= '<tr><th style="background:#eee;color:#333">Payment Approval :</th><td style="color:#777;">'.$payment_status.'</td></tr>';
            $html_str .= '<tr><th style="background:#eee;color:#333">Additional Payment Details :</th><td style="color:#777;">'.$payment_details.'</td></tr></table><br/><br/>';
        }
        $results['[PAYMENT_METHOD_HTML]'] = $html_str;

        foreach ($invoice as $k => $v) {
            $option_list = invoiceGetOptionList($k);
            if ('handler_id' == $k) {
                $split                      = explode(' ', $option_list[$v]);
                $results['[STAFF_NAME]']    = $option_list[$v];
                $results['[STAFF_FNAME']    = $split[0];
                $results['[STAFF_FINITIAL'] = substr($split[0], 0, 1) . '.';
                $results['[STAFF_LNAME']    = $split[1];
                $results['[STAFF_LINITIAL'] = substr($split[1], 0, 1) . '.';
            }

            if (count($option_list) > 0 && array_key_exists($v, $option_list)) {
                $results['[' . strtoupper($k) . ']'] = $option_list[$v];
            }
        }

        $html_str  = '';
        $temp_list = array();
        if (is_array($invoice_products)) {
            foreach ($invoice_products as $product) {
                $id = $product['product_id'];
                if (0 != $product["sub_id"]) {
                    $id .= "-" . $product["sub_id"];
                }

                $name  = $product['product_name'];
                $qty   = $product['product_qty'];
                $notes = $product['product_notes'];
                $offer = convertCurrency($product['product_offer'], $invoice['invoice_currency'], '', false, 2);
                if (2 == $invoice['site_id'] && $product['decimal'] > 2) {
                    $offer = convertCurrency($product['product_offer'], $invoice['invoice_currency'], '', false, $product['decimal']);
                }

                $total = ($qty * $offer);
                $html_str .= '<tr style="text-align:center"><td>' . $id . '</td> ';
                $html_str .= '<td style="text-align:left">' . htmlspecialchars($name) . ((trim('') != $notes) ? ('<br /><div style="font-size:10px;padding-left:15px;"><i>' . nl2br(htmlspecialchars($notes)) . '</i></div>') : '') . '</td>';
                $html_str .= '<td>' . $qty . '</td><td>' . $offer . '</td><td>' . sprintf('%.2f', $total) . '</td></tr>';
                $temp_list[] = $qty . ' x [' . (array_key_exists('product_number', $product) ? $product['product_number'] : $id) . '] ' . $name . ' ' . $offer . ((trim($notes) != '') ? "\n    [" . str_replace("\n", "]\n    [", trim($notes)) . "]" : '');
            }
        }
        if (is_array($invoice_gifts) && is_array($invoice_products)) {
            foreach ($invoice_gifts as $invoice_gift) {
                $id = $invoice_gift['id'];
                $name  = $invoice_gift['name'].' (FREE - Gift)';
                $qty   = $invoice_gift['qty'];
                $notes = $invoice_gift['gift_detail'];
                $offer = convertCurrency($invoice_gift['offer'], $invoice['invoice_currency'], '', false, 2);
                $total = 0.00;
                $html_str .= '<tr style="text-align:center"><td>' . $id . '</td> ';
                $html_str .= '<td style="text-align:left">' . htmlspecialchars($name) . ((trim('') != $notes) ? ('<br /><div style="font-size:10px;padding-left:15px;"><i>' . nl2br(htmlspecialchars($notes)) . '</i></div>') : '') . '</td>';
                $html_str .= '<td>' . $qty . '</td><td>' . $offer . '</td><td>' . sprintf('%.2f', $total) . '</td></tr>';
                $temp_list[] = $qty . ' x [' . (array_key_exists('product_number', $invoice_gift) ? $invoice_gift['product_number'] : $id) . '] ' . $name . ' ' . $offer . ((trim($notes) != '') ? "\n    [" . str_replace("\n", "]\n    [", trim($notes)) . "]" : '');
            }
        }
        $results['[INVOICE_TXT_PRODUCTS]']  = implode("\n", $temp_list);
        $results['[INVOICE_HTML_PRODUCTS]'] = $html_str;

        $html_str  = '';
        $temp_list = array();
        if ('' != $invoice['ship_product_name']) {
            $id = (('y' == $invoice['ship_insurance']) ? 'N' : 'F') . str_pad($invoice['ship_id'], 2, '0', STR_PAD_LEFT);
            $html_str .= '<tr style="text-align:center"><td>' . $id . '</td><td style="text-align:left">' . htmlspecialchars($invoice['ship_product_name']) . '</td><td>1</td> <td>' . $invoice['ship_offer'] . '</td><td>' . $invoice['ship_offer'] . '</td>';
            $temp_list[] = '1 x ' . $invoice['ship_product_name'] . ' ' . $invoice['ship_offer'];
        }
        $results['[INVOICE_TXT_SHIPPING]']  = implode("\n", $temp_list);
        $results['[INVOICE_HTML_SHIPPING]'] = $html_str;

        $html_str  = '';
        $temp_list = array();
        if (is_array($invoice_charges)) {
            foreach ($invoice_charges as $charge) {
                if (1 != $charge['charge_id']) {
                    $id = 'C' . str_pad($charge['charge_id'], 2, '0', STR_PAD_LEFT);
                    $html_str .= '<tr style="text-align:center"><td>' . $id . '</td><td style="text-align:left">' . htmlspecialchars($charge['charge_name']) . '</td><td>1</td> <td>' . $charge['charge_offer'] . '</td><td>' . $charge['charge_offer'] . ' </td></tr>';
                    $temp_list[] = '1 x ' . $charge['charge_name'] . ' ' . $charge['charge_offer'];
                }
            }
        }

        $results['[INVOICE_TXT_CHARGES]']  = implode("\n", $temp_list);
        $results['[INVOICE_HTML_CHARGES]'] = $html_str;

        $html_str  = '';
        $temp_list = array();
        $sub_total = $invoice['invoice_offer'];
        $gst_total = 0;
        if (is_array($invoice_charges)) {
            foreach ($invoice_charges as $charge) {
                if (1 == $charge['charge_id']) {
                    $gst_total += $charge['charge_offer'];
                }
            }
        }

        $sub_total -= $gst_total;
        $temp_list[] = 'Sub-Total: ' . convertCurrency($sub_total, $invoice['invoice_currency']);
        $html_str .= '<tr style="text-align:center"><td colspan="3">&nbsp;</td><td class="head">Sub-Total :</td><td>' . convertCurrency($sub_total, $invoice['invoice_currency']) . '</td></tr>';
        if ($gst_total > 0) {
            $temp_list[] = 'GST 10%: ' . convertCurrency($gst_total, $invoice['invoice_currency']);
            $html_str .= '<tr style="text-align:center;"><td colspan="3">&nbsp;</td><td class="head">GST 10%</td><td>' . convertCurrency($gst_total, $invoice['invoice_currency']) . '</td></tr>';
        }
        $grand_total = convertCurrency($invoice['invoice_offer'], $invoice['invoice_currency']) . ' ' . $invoice['invoice_currency'];
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
if (!function_exists("invoiceGiftsForProduct")) {
    /**
     * @param int     $invoice_id
     * @param mixed[] $invoice_products
     *
     * @return void
     */
    function invoiceGiftsForProduct($Product_id)
    {
        global $DB;
        /*$DB->query(sprintf(
            "DELETE FROM `highwaygroup`.`invoice_gifts` WHERE `invoice_id` = %d",
            $invoice_id
        ));*/    
        moduleLoad('product');
        $product = productFromDatabase($Product_id);
        if($product['available'] < 0 && $product['bonus'] == 'y' && $product['free_gifts'] == 'y' && $product['pre_order_close_date'] >= date('Y-m-d')) {
            
            // Insert gifts detail to invoice_gifts table
            $giftsProducts = productGenericsFromDatabase($product['id'], '`products_gifts`', 'id', 'product_id', 'ORDER BY `online` DESC');
        }elseif($product['free_gifts_product'] == 'y' && ($product['gift_promotion_expiry_date'] >= date('Y-m-d') || $product['gift_promotion_expiry_date'] == NULL) ){
            $giftsProducts = productGenericsFromDatabase($product['id'], '`products_free_gifts`', 'id', 'product_id', 'ORDER BY `online` DESC');
        }else{
            $giftsProducts = []; 
        } 
        $giftProductReturn = [];   
        if(count($giftsProducts) > 0){
            foreach ($giftsProducts as $gift) {
                $notes = array();
                $gift_product = productFromDatabase($gift['linked_id']);
                $warranty_list     = productGetOptionList('warranty');
                $notes[] = 'Warranty: ' . $warranty_list[$gift_product['warranty']];
                $last_note = '';

                $giftProductReturn = array(
                    'product_id'        => $product['id'],
                    'gift_product_id'   => $gift['linked_id'],
                    'gift_type'         => 'gift',
                    'gift_price'        => $gift['online'],
                    'gift_currency'     => $gift['currency'],
                    'gift_name'         => $gift['name'],
                    'gift_detail'       => trim(implode("\n", $notes)),
                    'gift_qty'          => $gift['qty']
                );
            }
        }

        return $giftProductReturn;
    }
}
if (!function_exists("invoiceGiftsToDatabase")) {
    /**
     * @param int     $invoice_id
     * @param mixed[] $invoice_products
     *
     * @return void
     */
    function invoiceGiftsToDatabase($invoice_id, $invoice_products)
    {

        global $DB;
        /*$DB->query(sprintf(
            "DELETE FROM `highwaygroup`.`invoice_gifts` WHERE `invoice_id` = %d",
            $invoice_id
        ));*/    
        moduleLoad('product');
        $existGiftProduct = array();
        if ($query = $DB->query(sprintf(
            "SELECT DISTINCT(`product_id`) FROM `highwaygroup`.`invoice_gifts` WHERE `invoice_id` = %d",
            $invoice_id
        ))) {
            while ($row = $DB->fetch_assoc($query)) {
                $existGiftProduct[] = $row['product_id'];
            }
            $DB->free_result($query);
        }
        if(count($existGiftProduct) > 0){
            foreach ($existGiftProduct as $proid) {
                if (empty(array_filter($invoice_products, function($item) use ($proid) {
                    return isset($item['product_id']) && $item['product_id'] == $proid;
                }))) {
                    $DB->query(sprintf(
                        "DELETE FROM `highwaygroup`.`invoice_gifts` WHERE `invoice_id` = %d AND product_id = %d",
                        (int)$invoice_id,
                        (int)$proid
                    ));
                }
            }
        }
        $invoice_id = intval($invoice_id);
        if ($invoice_id === 0) {
            return;
        }

        $invoice = invoiceFromDatabase($invoice_id);
        if (!is_array($invoice_products)) {
            return;
        }

        foreach ($invoice_products as $products) {
            //check if gift already exist for the product not included again.
            if(!in_array($products['product_id'], $existGiftProduct)){
            $product = productFromDatabase($products['product_id']);
            if($product['available'] < 0 && $product['bonus'] == 'y' && $product['free_gifts'] == 'y' && $product['pre_order_close_date'] >= date('Y-m-d')) {
                
                // Insert gifts detail to invoice_gifts table
                $giftsProducts = productGenericsFromDatabase($product['id'], '`products_gifts`', 'id', 'product_id', 'ORDER BY `online` DESC');
            }elseif($product['free_gifts_product'] == 'y' && ($product['gift_promotion_expiry_date'] >= date('Y-m-d') || $product['gift_promotion_expiry_date'] == NULL) ){
                $giftsProducts = productGenericsFromDatabase($product['id'], '`products_free_gifts`', 'id', 'product_id', 'ORDER BY `online` DESC');
            }else{
                $giftsProducts = []; 
            }   
            if(count($giftsProducts) > 0){
                foreach ($giftsProducts as $gift) {
                    $notes = array();
                    $gift_product = productFromDatabase($gift['linked_id']);
                    $warranty_list     = productGetOptionList('warranty');
                    $notes[] = 'Warranty: ' . $warranty_list[$gift_product['warranty']];
                    $last_note = '';

                    $giftProduct = array(
                        'invoice_id'        => $invoice_id,
                        'product_id'        => $product['id'],
                        'gift_product_id'   => $gift['linked_id'],
                        'gift_type'         => 'gift',
                        'gift_price'        => convertCurrency($gift['online'], $gift['currency'], $invoice['invoice_currency']),
                        'gift_name'         => $gift['name'],
                        'gift_detail'       => trim(implode("\n", $notes)),
                        'gift_qty'          => $gift['qty']*$products['product_qty'],
                        'total'             => bcmul(convertCurrency($gift['online'], $gift['currency'], $invoice['invoice_currency']), $gift['qty'], 2),
                        'site_id'           => $invoice['site_id']
                    );
                    
                    
                    $insert_vals = array();
                    foreach ($DB->table_fields('`highwaygroup`.`invoice_gifts`') as $k) {
                        if (array_key_exists($k, $giftProduct)) {
                            $insert_vals['`' . $k . '`'] = $DB->escape($giftProduct[$k]);
                        }
                    }
            
                    $DB->query(sprintf(
                        "INSERT INTO `highwaygroup`.`invoice_gifts` (%s) VALUES (%s)",
                        implode(', ', array_keys($insert_vals)),
                        implode(', ', array_values($insert_vals))
                    ));
                }
            }
            }
        }


    }
}

if (!function_exists("invoiceGiftsFromDatabase")) {
    /**
     * @param int|string $product_str
     * @param int        $sub_id
     *
     * @return mixed[][]
     */
    function invoiceGiftsFromDatabase($invoice_id)
    {
        global $DB;

        $result = array();
        
        if ($query = $DB->query(sprintf(
            "SELECT * FROM `highwaygroup`.`invoice_gifts` WHERE `invoice_id` = %d",
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

if (!function_exists("invoiceGiftsFromDatabasePrint")) {
    /**
     * @param int|string $product_str
     * @param int        $sub_id
     *
     * @return mixed[][]
     */

    function invoiceGiftsFromDatabasePrint($invoice_id)
    {
        global $DB;

        $result = array();

        if ($query = $DB->query(sprintf(
            "SELECT * FROM `highwaygroup`.`invoice_gifts` WHERE `invoice_id` = %d",
            $invoice_id
        ))) {
            $gifts = array();
            $invoice_total = 0;

            // Loop through the results and concatenate gift names
            while ($row = $DB->fetch_assoc($query)) {
                    $gift = array();
                    $gift['product_id'] = $row['gift_product_id'];
                    $gift['gift_name']  = $row['gift_name'];
                    $gift['gift_detail']= $row['gift_detail'];
                    $gift['gift_qty']   = $row['gift_qty'];
                    $gift['amount']     = $row['gift_price'];
                    $gift['total']      = $row['total'];
                    $gifts['gift'][]    = $gift; 
            }
            $DB->free_result($query);
        }

        return $gifts;
    }
    function invoiceGiftsFromDatabaseEmail($invoice_id)
    {
        global $DB;

         $gifts = array();
        if ($query = $DB->query(sprintf(
            "SELECT * FROM `highwaygroup`.`invoice_gifts` WHERE `invoice_id` = %d",
            $invoice_id
        ))) {
 
            $invoice_total = 0;

            // Loop through the results and concatenate gift names
            while ($row = $DB->fetch_assoc($query)) {
                    $gift = array();
                    $gift['id'] = $row['gift_product_id'];
                    $gift['name']  = $row['gift_name'];
                    $gift['gift_detail']= $row['gift_detail'];
                    $gift['qty']   = $row['gift_qty'];
                    $gift['offer']     = 0.00;
                    $gift['total']      = $row['total'];
                    $gifts[]    = $gift; 
            }
            $DB->free_result($query);
        }

        return $gifts;
    }
}



if (!function_exists("invoicePackingInformationFromPost")) {
    /**
     * Process form data into packing information array
     * 
     * @param array $posted Posted form data
     * @param int $invoice_id Invoice ID
     * @return array Processed packing information
     */
    function invoicePackingInformationFromPost($posted, $invoice_id) {
        $packing_info = array();
        
        if (isset($posted['productPackaging']) && is_array($posted['productPackaging'])) {
            foreach ($posted['productPackaging'] as $index => $item) {
                if($item['saved_product'] == 0){
                    continue;
                }
                // if (!isset($item['is_allow']) || $item['is_allow'] != '1') {
                //     continue;
                // }
                $is_allowed = (isset($item['is_allow']) && $item['is_allow']) == '1' ? 'y' : 'n';
                $allow_stack = (isset($item['allow_stack']) && $item['allow_stack']) == '1' ? 'y' : 'n';
                $packing_info[] = array(
                    // 'is_allowed' => 'y',
                    'is_allowed' => $is_allowed,
                    'allow_stack' => $allow_stack,
                    'product_name' => isset($item['product_name']) ? $item['product_name'] : '',
                    'rotate_width' => isset($item['width_allow']) && $item['width_allow'] == '1' ? 'y' : 'n',
                    'width' => ($posted['packing_type'] == 'with_packing') ? ($item['width_packing'] ?? $item['width'] ?? 0 ) :  ($item['width'] ?? 0),
                    'rotate_depth' => isset($item['depth_allow']) && $item['depth_allow'] == '1' ? 'y' : 'n',
                    'depth' =>  ($posted['packing_type'] == 'with_packing') ? ($item['depth_packing'] ?? $item['depth'] ?? 0 ) : ($item['depth'] ?? 0) ,
                    'rotate_height' => isset($item['height_allow']) && $item['height_allow'] == '1' ? 'y' : 'n',
                    'height' => ($posted['packing_type'] == 'with_packing') ? ($item['height_packing'] ?? $item['height'] ?? 0 ) : ($item['height'] ?? 0),
                    'weight' => ($posted['packing_type'] == 'with_packing') ? ($item['weight_packing'] ?? $item['weight'] ?? 0 ) : ($item['weight'] ?? 0),
                    'quantity' => isset($item['product_qty']) ? intval($item['product_qty']) : 0,
                    'invoice_id' => $invoice_id,
                    'product_id' => isset($item['product_id']) ? intval($item['product_id']) : 0,
                    'rank' => isset($posted['Packaging_product_rank'][$index]) ? intval($posted['Packaging_product_rank'][$index]) : 0
                );
            }
        }
        return $packing_info;
    }
}
if (!function_exists("invoicePackingInformationDelete")) {
    function invoicePackingInformationDelete($invoice_id) { 
        global $DB;
        $DB->query(sprintf(
            "DELETE FROM `packing_information` WHERE `invoice_id` = %d",
            $invoice_id
        ));     
    }
}
if (!function_exists("invoicePackingInformationToDatabase")) {
    /**
     * Save packing information to database
     * 
     * @param int $invoice_id Invoice ID
     * @param array $packing_info Array of packing information
     */
    function invoicePackingInformationToDatabase($invoice_id, $packing_info) {
        global $DB;
        
        // Debug: Log what we're about to save
        // echo "<pre>";
        // echo "Saving packing info for invoice_id: " . $invoice_id . "\n";
        // echo "Packing info data:\n";
        // print_r($packing_info);
        // echo "</pre>";
       
        // First delete existing packing information for this invoice
        // print_r($packing_info);exit;

         invoicePackingInformationDelete($invoice_id);
        // Insert new packing information
        foreach ($packing_info as $item) {
            $query = sprintf(
                "INSERT INTO `packing_information` 
                (`is_allowed`, `product_name`, `rotate_width`, `width`, `rotate_depth`, `depth`, 
                `rotate_height`, `height`, `weight`, `quantity`, `invoice_id`, `product_id`, `rank`,`allow_stack`)
                VALUES (%s, %s, %s, %f, %s, %f, %s, %f, %f, %d, %d, %d, %d,%s)",
                formatSQLString($item['is_allowed']),
                formatSQLString($item['product_name']),
                formatSQLString($item['rotate_width']),
                floatval($item['width']),
                formatSQLString($item['rotate_depth']),
                floatval($item['depth']),
                formatSQLString($item['rotate_height']),
                floatval($item['height']),
                floatval($item['weight']),
                intval($item['quantity']),
                intval($item['invoice_id']),
                intval($item['product_id']),
                intval($item['rank']),
                formatSQLString($item['allow_stack']),
            );
            $result = $DB->query($query);
        
        }
    }
}

if (!function_exists("invoicePackingInformationFromDatabase")) {
    /**
     * Get packing information from database for an invoice
     * 
     * @param int $invoice_id Invoice ID
     * @return array Array of packing information
     */
    function invoicePackingInformationFromDatabase($invoice_id) {
        global $DB;

        $packing_info = array();
        
        $result = $DB->query(sprintf(
            "SELECT * FROM `packing_information` WHERE `invoice_id` = %d ORDER BY `rank` ASC",
            $invoice_id
        ));
        
        while ($row = $DB->fetch_array($result)) {
            $packing_info[] = array(
                'is_allowed' => $row['is_allowed'],
                'allow_stack' => $row['allow_stack'],
                'product_name' => $row['product_name'],
                'rotate_width' => $row['rotate_width'],
                'width' => floatval($row['width']),
                'rotate_depth' => $row['rotate_depth'],
                'depth' => floatval($row['depth']),
                'rotate_height' => $row['rotate_height'],
                'height' => floatval($row['height']),
                'weight' => floatval($row['weight']),
                'quantity' => intval($row['quantity']),
                'invoice_id' => intval($row['invoice_id']),
                'product_id' => intval($row['product_id']),
                'rank' => intval($row['rank'])
            );
        }
        
        return $packing_info;
    }
}

if (!function_exists("deletePackingInformation")) {
    /**
     * Delete a specific product's packing information
     * 
     * @param int $invoice_id Invoice ID
     * @param int $product_id Product ID
     * @return bool Success status
     */
    // function deletePackingInformation($invoice_id, $product_id) {
    //     global $DB;
    //     $sql = "DELETE FROM `highwaygroup`.`packing_information` WHERE invoice_id = ? AND product_id = ?";
    //     return $DB->query($sql, array($invoice_id, $product_id));
    // }
}
