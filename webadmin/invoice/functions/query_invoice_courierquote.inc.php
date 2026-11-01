<?php
if (!defined('INVOICE_COURIERQUOTE_TABLENAME')) {
    define('INVOICE_COURIERQUOTE_TABLENAME', '`highwaygroup`.`invoice_courier_quotes`');
}
if (!defined('INVOICE_COURIERQUOTE_PRIMARYKEY')) {
    define('INVOICE_COURIERQUOTE_PRIMARYKEY', 'id');
}
if (!defined('INVOICE_COURIERQUOTE_SECONDARYKEY')) {
    define('INVOICE_COURIERQUOTE_SECONDARYKEY', 'invoice_id');
}

if (!function_exists('invoiceCourierQuoteDefault')) {
    /**
     * @return mixed[]
     */
    function invoiceCourierQuoteDefault()
    {
        global $DB;

        return invoiceCourierQuoteVirtual($DB->modelDefault(
            INVOICE_COURIERQUOTE_TABLENAME,
            INVOICE_COURIERQUOTE_PRIMARYKEY
        ));
    }
}
if (!function_exists('invoiceCourierQuoteDelete')) {
    /**
     * @param int $id
     *
     * @return int
     */
    function invoiceCourierQuoteDelete($id)
    {
        global $DB;

        return $DB->modelDelete(
            $id,
            INVOICE_COURIERQUOTE_TABLENAME,
            INVOICE_COURIERQUOTE_PRIMARYKEY
        );
    }
}
if (!function_exists('invoiceCourierQuoteExists')) {
    /**
     * @param int $id
     *
     * @return bool
     */
    function invoiceCourierQuoteExists($id)
    {
        global $DB;

        return $DB->modelExists(
            $id,
            INVOICE_COURIERQUOTE_TABLENAME,
            INVOICE_COURIERQUOTE_PRIMARYKEY
        );
    }
}
if (!function_exists('invoiceCourierQuoteFromDatabase')) {
    /**
     * @param int $id
     *
     * @return mixed[]|null
     */
    function invoiceCourierQuoteFromDatabase($id)
    {
        global $DB;

        if ($row = $DB->modelLoad(
            $id,
            INVOICE_COURIERQUOTE_TABLENAME,
            INVOICE_COURIERQUOTE_PRIMARYKEY
        )) {
            return invoiceCourierQuoteVirtual($row);
        }

        return null;
    }
}
if (!function_exists('invoiceCourierQuoteGetValue')) {
    /**
     * @param int    $id
     * @param string $field
     *
     * @return mixed
     */
    function invoiceCourierQuoteGetValue($id, $field)
    {
        global $DB;

        if (array_key_exists($field, $DB->table_columns(
            INVOICE_COURIERQUOTE_TABLENAME
        ))) {
            return $DB->modelGet(
                $id,
                $field,
                INVOICE_COURIERQUOTE_TABLENAME,
                INVOICE_COURIERQUOTE_PRIMARYKEY
            );
        }

        if ($model = invoiceCourierQuoteFromDatabase($id)) {
            return (array_key_exists($field, $model)) ? $model[$field] : null;
        }

        return null;
    }
}
if (!function_exists('invoiceCourierQuoteHasValue')) {
    /**
     * @param int    $id
     * @param string $field
     * @param mixed  $value
     *
     * @return bool
     */
    function invoiceCourierQuoteHasValue($id, $field, $value)
    {
        return invoiceCourierQuoteGetValue($id, $field) == $value;
    }
}
if (!function_exists('invoiceCourierQuoteQuery')) {
    /**
     * @todo
     *
     * @param mixed[] $eq
     * @param mixed[] $lt
     * @param mixed[] $gt
     *
     * @return string
     */
    function invoiceCourierQuoteQuery($eq = array(), $lt = array(), $gt = array())
    {
        global $DB;

        $whereAND = array();

        return (count($whereAND)) ? implode(" AND ", $whereAND) : "1";
    }
}
if (!function_exists('invoiceCourierQuoteSearch')) {
    /**
     * @param string|null $where
     * @param string|null $order_by
     *
     * @return int[]
     */
    function invoiceCourierQuoteSearch($where = null, $order_by = null)
    {
        global $DB;

        // Legacy accepts criteria as first parameter
        if (is_array($where)) {
            $where = invoiceCourierQuoteQuery($where);
        }

        return $DB->query_list(sprintf(
            "SELECT `%s` FROM %s%s%s",
            INVOICE_COURIERQUOTE_PRIMARYKEY,
            INVOICE_COURIERQUOTE_TABLENAME,
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
if (!function_exists('invoiceCourierQuoteSetValue')) {
    /**
     * @param int    $id
     * @param string $field
     * @param mixed  $value
     *
     * @return int
     */
    function invoiceCourierQuoteSetValue($id, $field, $value)
    {
        global $DB;

        return $DB->modelSet(
            $id,
            $field,
            $value,
            INVOICE_COURIERQUOTE_TABLENAME,
            INVOICE_COURIERQUOTE_PRIMARYKEY
        );
    }
}
if (!function_exists('invoiceCourierQuoteSort')) {
    /**
     * @param string|null $where
     * @param string|null $order_by
     *
     * @return int[]
     */
    function invoiceCourierQuoteSort($where = null, $order_by = null)
    {
        // Legacy accepts list of id's as first parameter
        if (is_array($where)) {
            $where = invoiceCourierQuoteQuery(array(
                INVOICE_COURIERQUOTE_PRIMARYKEY => $where,
            ));
        }

        return invoiceCourierQuoteSearch($where, $order_by);
    }
}
if (!function_exists('invoiceCourierQuoteToDatabase')) {
    /**
     * @param mixed[] $item
     *
     * @return int
     */
    function invoiceCourierQuoteToDatabase(&$item)
    {
        global $DB;

        return $DB->modelSave(
            $item,
            INVOICE_COURIERQUOTE_TABLENAME,
            INVOICE_COURIERQUOTE_PRIMARYKEY
        );
    }
}
if (!function_exists('invoiceCourierQuoteVirtual')) {
    /**
     * @param mixed[] $row
     *
     * @return mixed[]
     */
    function invoiceCourierQuoteVirtual($row)
    {
        return $row;
    }
}

if (!function_exists('invoiceCourierQuotesDelete')) {
    /**
     * @param int $id
     *
     * @return int
     */
    function invoiceCourierQuotesDelete($id)
    {
        global $DB;

        return $DB->childrenDelete(
            $id,
            INVOICE_COURIERQUOTE_TABLENAME,
            INVOICE_COURIERQUOTE_SECONDARYKEY
        );
    }
}
if (!function_exists('invoiceCourierQuotesFromDatabase')) {
    /**
     * @param int $id
     *
     * @return mixed[]|null
     */
    function invoiceCourierQuotesFromDatabase($id)
    {
        global $DB;

        $result = array();
        foreach ($DB->childrenLoad(
            $id,
            INVOICE_COURIERQUOTE_TABLENAME,
            INVOICE_COURIERQUOTE_PRIMARYKEY,
            INVOICE_COURIERQUOTE_SECONDARYKEY
        ) as $k => $v) {
            $result[$k] = invoiceCourierQuoteVirtual($v);
        }

        return $result;
    }
}
if (!function_exists('invoiceCourierQuotesToDatabase')) {
    /**
     * @todo original function also deletes quotes
     *
     * @param int       $id
     * @param mixed[][] $items
     *
     * @return void
     */
    /*
function invoiceCourierQuotesToDatabase($id, &$items)
{
global $DB;

$DB->childrenSave(
$id,
$items,
INVOICE_COURIERQUOTE_TABLENAME,
INVOICE_COURIERQUOTE_PRIMARYKEY,
INVOICE_COURIERQUOTE_SECONDARYKEY
);
}
 */
}

if (!function_exists('invoiceCourierQuotesFromPost')) {
    /**
     * @param mixed[] $posted
     *
     * @return mixed[][]
     */
    function invoiceCourierQuotesFromPost($posted)
    {
        global $DB;

        $invoice_id = $posted['invoice_id'];

        $quotes = array();

        $fields = array();
        if ($query = $DB->query(sprintf(
            "SHOW COLUMNS FROM %s",
            INVOICE_COURIERQUOTE_TABLENAME
        ))) {
            while ($row = $DB->fetch_array($query)) {
                if ('auto_increment' != $row[5]) {
                    $fields[$row[0]] = $row[4];
                } else {
                    $auto = $row[0];
                }
            }
            $DB->free_result($query);
        }

        if (!array_key_exists('quotes_affected', $posted)) {
            $quotes = invoiceCourierQuotesFromDatabase($invoice_id);
        }

        if (is_array($posted['freightcourier_id'])) {
            foreach ($posted['freightcourier_id'] as $key1 => $courier_id) {
                $quote                                    = $fields;
                $quote[INVOICE_COURIERQUOTE_SECONDARYKEY] = $invoice_id;
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
if (!function_exists('invoiceCourierQuotesToDatabase')) {
    /**
     * @param int $id
     * @param mixed[][] $quotes
     *
     * @return void
     */
    function invoiceCourierQuotesToDatabase($id, $quotes)
    {
        global $DB;
        $id = intval($id);
        if (0 == $id) {
            return;
        }

        $DB->query(sprintf(
            "DELETE FROM %s WHERE `%s` = %d",
            INVOICE_COURIERQUOTE_TABLENAME,
            INVOICE_COURIERQUOTE_SECONDARYKEY,
            $id
        ));

        $fields = array();
        if ($query = $DB->query(sprintf(
            "SHOW COLUMNS FROM %s",
            INVOICE_COURIERQUOTE_TABLENAME
        ))) {
            while ($row = $DB->fetch_array($query)) {
                if ('auto_increment' != $row[5]) {
                    $fields[$row[0]] = $row[4];
                }
            }
            $DB->free_result($query);
        }

        if (is_array($quotes)) {
            foreach ($quotes as $quote) {
                $insert_vals = array();
                if ($id > 0) {
                    $quote[INVOICE_COURIERQUOTE_SECONDARYKEY] = $id;
                }

                foreach ($quote as $key => $value) {
                    if (array_key_exists($key, $fields)) {
                        $insert_vals[$key] = addslashes($value);
                    }
                }

                $DB->query(sprintf(
                    "INSERT INTO %s (`%s`) VALUES ('%s')",
                    INVOICE_COURIERQUOTE_TABLENAME,
                    implode('`, `', array_keys($insert_vals)),
                    implode("', '", $insert_vals)
                ));
            }
        }
    }
}
