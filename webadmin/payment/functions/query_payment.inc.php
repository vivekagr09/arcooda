<?php
if (!function_exists('paymentTemplateVars')) {
    /**
     * @param mixed[] $payment
     *
     * @return mixed[]
     */
    function paymentTemplateVars($payment)
    {
        $result = array();
        if (is_array($payment)) {
            foreach ($payment as $key => $value) {
                $result['[' . strtoupper($key) . ']'] = $value;
            }
        }

        return $result;
    }
}
if (!function_exists('paymentDatabaseFromPortal')) {
    /**
     * @return string
     */
    function paymentDatabaseFromPortal()
    {
        return '`highwaygroup`.`credit_transactions`';
    }
}
if (!function_exists('paymentExists')) {
    /**
     * @param int $id
     *
     * @return bool
     */
    function paymentExists($id)
    {
        global $DB;

        return !!$DB->query_first(sprintf(
            "SELECT 1 FROM `highwaygroup`.`credit_transactions` WHERE `id` = %d LIMIT 1",
            $id
        ), MYSQLI_ASSOC);
    }
}
if (!function_exists('paymentSearch')) {
    /**
     * @param mixed $criteria
     *
     * @return int[]
     */
    function paymentSearch($criteria = '')
    {
        global $DB;

        $fields = getTableFields('`highwaygroup`.`credit_transactions`');

        $joins = array();

        $whereAND = array();
        if (is_array($criteria)) {
            foreach ($criteria as $k => $v1) {
                if (array_key_exists($k, $fields)) {
                    $whereOR = array();
                    foreach ((array) $v1 as $v2) {
                        if (!empty($v2)) {
                            $whereOR[] = sprintf("`ct`.`%s` = %s", $k, $DB->escape($v2));
                        }
                    }
                    if (count($whereOR)) {
                        $whereAND[] = sprintf("(%s)", implode(" OR ", $whereOR));
                    }
                    unset($fields[$k]);
                }
            }
            if (array_key_exists('result_approval', $criteria) && '' != $criteria['result_approval']) {
                $joins[]    = " LEFT JOIN `converters`.`commweb_resultcode` AS `cr` ON `cr`.`result_code` = CONCAT(`ct`.`TxnResponseCode`, IF(`ct`.`AcqResponseCode` <> '', `ct`.`AcqResponseCode`, '--'))";
                $whereAND[] = sprintf(
                    "`cr`.`result_approval` = %s",
                    $DB->escape($criteria['result_approval'])
                );
            }
        }

        $search_for = array();
        if (is_string($criteria) && '' != $criteria) {
            $search_for = explode(' ', $criteria);
        }
        if (is_array($criteria) && is_array($criteria['any_text'])) {
            $search_for = $criteria['any_text'];
        }
        if (is_array($criteria) && is_string($criteria['any_text'])) {
            $search_for = explode(' ', $criteria['any_text']);
        }
        if (count($search_for)) {
            $searchBits = array('`ct`.`OrderInfo`');
            foreach ($search_for as $findme) {
                $whereOR = array();
                foreach ($searchBits as $key) {
                    if (trim($findme) != '') {
                        $whereOR[] = $key . " LIKE '%" . str_replace('%', '\%', str_replace('_', '\_', addslashes($findme))) . "%'";
                    }
                }
                if (count($whereOR)) {
                    $whereAND[] = "	(" . implode(" OR ", $whereOR) . ")";
                }
            }
        }

        $result = array();
        if ($query = $DB->query(sprintf(
            "SELECT `ct`.`id` FROM `highwaygroup`.`credit_transactions` AS `ct`%s%s GROUP BY `ct`.`id` ORDER BY `ct`.`id` ASC",
            (count($joins)) ? implode('', $joins) : '',
            (count($whereAND)) ? (" WHERE " . implode(" AND ", $whereAND)) : ''
        ))) {
            while ($row = $DB->fetch_assoc($query)) {
                $result[] = $row['id'];
            }
            $DB->free_result($query);
        }

        return $result;
    }
}
if (!function_exists('paymentSort')) {
    /**
     * @param int[]  $payment_list
     * @param string $sort_by
     * @param int    $order
     *
     * @return int[]
     */
    function paymentSort($payment_list, $sort_by, $order = SORT_ASC)
    {
        global $DB;
        if (!is_array($payment_list) || count($payment_list) < 2) {
            return $payment_list;
        }

        $fields    = getTableFields('`highwaygroup`.`credit_transactions`');
        $temp_list = array();
        if (isset($fields[$sort_by])) {
            if ($query = $DB->query(sprintf(
                "SELECT `ct`.`id` FROM `highwaygroup`.`credit_transactions` AS `ct` WHERE `ct`.`id` IN(%s) ORDER BY `%s` ASC",
                implode(', ', $payment_list),
                $sort_by
            ))) {
                while ($row = $DB->fetch_assoc($query)) {
                    $temp_list[] = $row['id'];
                }
                $DB->free_result($query);
            }
        }
        if (count($temp_list) > 0) {
            $payment_list = $temp_list;
        }

        if (SORT_DESC == $order) {
            return array_reverse($payment_list);
        }

        return $payment_list;
    }
}
if (!function_exists('paymentChunk')) {
    /**
     * @param int[]           $payment_list
     * @param string[]|string $group_by
     *
     * @return mixed[]
     */
    function paymentChunk($payment_list, $group_by = '')
    {
        global $DB;
        if (!is_array($payment_list) || count($payment_list) < 1) {
            return $payment_list;
        }

        if (!is_array($group_by)) {
            $group_by = array($group_by);
        }

        $fields     = getTableFields('`highwaygroup`.`credit_transactions`');
        $field_list = array('id');
        foreach ($group_by as $field) {
            if (array_key_exists($field, $fields)) {
                $field_list[] = $field;
            }
        }

        $field_list = array_unique($field_list);

        $results = array();
        if ($query = $DB->query(sprintf(
            "SELECT %s FROM `highwaygroup`.`credit_transactions` AS `ct` WHERE `ct`.`id` IN(%s)",
            implode(', ', $field_list),
            implode(', ', $payment_list)
        ))) {
            while ($row = $DB->fetch_assoc($query)) {
                $pointer = &$results;
                foreach ($group_by as $group) {
                    $key = '';
                    $key = $row[$group];
                    if ('' != $key) {
                        if (!array_key_exists($key, $pointer)) {
                            $pointer[$key] = array();
                        }

                        $pointer = &$pointer[$key];
                    }
                }
                $pointer[] = $row['id'];
            }
            $DB->free_result($query);
        }

        return $results;
    }
}
if (!function_exists('paymentFromDatabase')) {
    /**
     * @param int $id
     *
     * @return mixed[]
     */
    function paymentFromDatabase($id)
    {
        global $DB;

        $summary = array();
        if (paymentExists($id)) {
            $summary = $DB->query_first(sprintf(
                "SELECT * FROM `highwaygroup`.`credit_transactions` WHERE `id` = %d LIMIT 1",
                $id
            ), MYSQLI_ASSOC);
        } else {
            foreach (getTableFields('`highwaygroup`.`credit_transactions`') as $row) {
                $summary[$row[0]] = $row[4];
            }
        }

        if ($row = $DB->query_first(sprintf(
            "SELECT * FROM `converters`.`commweb_resultcode` WHERE `result_code` = %s",
            $DB->escape($summary['TxnResponseCode'] . "--")
        ), MYSQLI_ASSOC)) {
            foreach ($row as $k => $v) {
                $summary[$k] = $v;
            }
        }

        if ($row = $DB->query_first(sprintf(
            "SELECT * FROM `converters`.`commweb_resultcode` WHERE `result_code` = %s LIMIT 1",
            $DB->escape($summary['TxnResponseCode'] . $summary['AcqResponseCode'])
        ), MYSQLI_ASSOC)) {
            foreach ($row as $k => $v) {
                $summary[$k] = $v;
            }
        }

        $summary['website_id'] = 9;
        $summary['TxnRef']     = $summary['MerchTxnRef'];

        if ($query = $DB->query(sprintf(
            "SELECT `id`, `short_name` FROM `highwaygroup`.`websites` ORDER BY LENGTH(`short_name`) ASC"
        ))) {
            while ($row = $DB->fetch_assoc($query)) {
                if (substr($summary['MerchTxnRef'], 0, strlen($row['short_name'])) == $row['short_name']) {
                    $summary['website_id'] = $row['id'];
                    $summary['TxnRef']     = substr($summary['MerchTxnRef'], strlen($row['short_name']));
                }
            }
            $DB->free_result($query);
        }

        if (substr($summary['MerchTxnRef'], 0, 3) == 'HGR' && 11 != $summary['website_id']) {
            $summary['website_id'] = 11;
            $summary['TxnRef']     = substr($summary['MerchTxnRef'], 3);
        }

        if (substr($summary['TxnRef'], 0, 24) == 'Credit Transaction for #' && strlen($summary['TxnRef']) == 30) {
            $summary['TxnRef']     = intval(substr($summary['TxnRef'], -6));
            $summary['website_id'] = 16;
        }

        $summary['payment_id'] = $summary['id'];
        return $summary;
    }
}
if (!function_exists('paymentFromPost')) {
    /**
     * @param mixed[] $posted
     *
     * @return mixed[]
     */
    function paymentFromPost($posted)
    {
        global $DB;
        $id           = $posted['id'];
        $result       = paymentFromDatabase($id);
        $result['id'] = intval($id);
        foreach ($result as $key => $value) {
            if (array_key_exists($key, $posted) && !is_array($posted[$key])) {
                $result[$key] = trim($posted[$key]);
            }
        }

        if ($id <= 0) {
            unset($result['id']);
        }

        unset($result['id']);
        return $result;
    }
}
if (!function_exists('paymentToDatabase')) {
    /**
     * @param mixed[] $summary
     *
     * @return int
     */
    function paymentToDatabase($summary = array())
    {
        global $DB;

        if (0 == count($summary)) {
            if ($DB->query(sprintf(
                "INSERT INTO `highwaygroup`.`credit_transactions` VALUES ()"
            ))) {
                return $DB->insert_id();
            }
            return 0;
        }

        $id          = intval($summary['id']);
        $old_payment = paymentFromDatabase($id);
        foreach ($summary as $key => $value) {
            if (!array_key_exists($key, $old_payment)) {
                unset($summary[$key]);
            }
        }

        $fields = getTableFields('`highwaygroup`.`credit_transactions`');
        if (count($summary) > 0) {
            if (paymentExists($id)) {
                $set_vals = array();
                foreach ($summary as $key => $value) {
                    if (array_key_exists($key, $fields)) {
                        if (isset($summary[$key])) {
                            $set_vals[$key] = "`" . $key . "` = " . $DB->escape($value);
                        } else {
                            $set_vals[$key] = "`" . $key . "` = NULL";
                        }
                    }
                }

                if (count($set_vals) > 0) {
                    $DB->query(sprintf(
                        "UPDATE `highwaygroup`.`credit_transactions` SET %s WHERE `id` = %d LIMIT 1",
                        implode(', ', $set_vals),
                        $id
                    ));
                }
            } else {
                $set_vals = array();
                foreach ($summary as $key => $value) {
                    if (array_key_exists($key, $fields)) {
                        if (isset($summary[$key])) {
                            $set_vals[$key] = $DB->escape($value);
                        } else {
                            $set_vals[$key] = "NULL";
                        }
                    }
                }

                if (count($set_vals) > 0) {
                    $DB->query(sprintf(
                        "INSERT INTO `highwaygroup`.`credit_transactions` (%s) VALUES (%s)",
                        implode(', ', array_keys($set_vals)),
                        implode(', ', array_values($set_vals))
                    ));
                }

                $id = $DB->insert_id();
            }
        }
        return $id;
    }
}
if (!function_exists('paymentDelete')) {
    /**
     * @param int $id
     *
     * @return void
     */
    function paymentDelete($id)
    {
        global $DB;

        $DB->query(sprintf(
            "DELETE FROM `highwaygroup`.`credit_transactions` WHERE `id` = %d LIMIT 1",
            $id
        ));
    }
}
if (!function_exists('paymentNotesFromDatabase')) {
    /**
     * @param int $id
     *
     * @return mixed[][]
     */
    function paymentNotesFromDatabase($id)
    {
        return array();
    }
}
if (!function_exists('paymentNoteFromDatabase')) {
    /**
     * @param int $note_id
     *
     * @return mixed[]|null
     */
    function paymentNoteFromDatabase($note_id)
    {
        return null;
    }
}
if (!function_exists('paymentNoteToDatabase')) {
    /**
     * @param int     $id
     * @param mixed[] $note
     *
     * @return int|null
     */
    function paymentNoteToDatabase($id, $note)
    {
        return null;
    }
}
if (!function_exists('paymentMethodsDistinct')) {
    /**
     * @return string[]
     */
    function paymentMethodsDistinct()
    {
        global $DB;

        static $result = null;
        if (!is_null($result)) {
            return $result;
        }

        $result = array();
        if ($query = $DB->query(sprintf(
            "SELECT DISTINCT(`payment_method_id`) AS `payment_method` FROM `highwaygroup`.`payment_methods`"
        ))) {
            while ($row = $DB->fetch_assoc($query)) {
                $result[] = $row['payment_method'];
            }
            $DB->free_result($query);
        }

        return $result;
    }
}
if (!function_exists('paymentMethodsFromDatabase')) {
    /**
     * @param string $payment_method_id
     * @param int    $site_id
     *
     * @return string[][]|string[]
     */
    function paymentMethodsFromDatabase($payment_method_id, $site_id = 0)
    {
        global $DB;

        if ('' == $payment_method_id) {
            return;
        }

        if (0 != $site_id) {
            return $DB->query_first(sprintf(
                "SELECT * FROM `highwaygroup`.`payment_methods` WHERE `payment_method_id` = %s AND `site_id` = %d LIMIT 1",
                $DB->escape($payment_method_id),
                $site_id
            ), MYSQLI_ASSOC);
        } else {
            $result = array();
            if ($query = $DB->query(sprintf(
                "SELECT * FROM `highwaygroup`.`payment_methods` WHERE `payment_method_id` = %s ORDER BY `site_id` ASC",
                $DB->escape($payment_method_id)
            ))) {
                while ($row = $DB->fetch_assoc($query)) {
                    $result[] = $row;
                }
                $DB->free_result($query);
            }

            return $result;
        }
    }
}
if (!function_exists('paymentName')) {
    /**
     * @param string $payment_method_id
     *
     * @return string|false
     */
    function paymentName($payment_method_id)
    {
        global $DB;

        if ($row = $DB->query_first(sprintf(
            "SELECT `payment_method_name` FROM `highwaygroup`.`payment_methods` WHERE `payment_method_id` = %s LIMIT 1",
            $DB->escape($payment_method_id)
        ), MYSQLI_ASSOC)) {
            return $row['payment_method_name'];
        }

        return false;
    }
}
if (!function_exists('paymentMethodToDatabase')) {
    /**
     * @param mixed[] $posted
     *
     * @return void
     */
    function paymentMethodToDatabase($posted)
    {
        global $DB;
        $accepted_currencies = 'AUD';
        if (is_array($posted['currencies']) && count($posted['currencies'])) {
            $accepted_currencies = implode(',', $posted['currencies']);
        }

        $accepted = array(
            'payment_method_name',
            'payment_method_details',
            'payment_method_description',
            'payment_method_public',
            'payment_charge_percent',
            'payment_charge_amount',
        );

        $set = array();
        foreach ($posted as $key => $value) {
            if (in_array($key, $accepted)) {
                $set[] = sprintf(
                    "`%s` = %s",
                    $key,
                    $DB->escape($value)
                );
            }
        }

        $set[] = '`accepted_currencies` = ' . $DB->escape($accepted_currencies);
        if (count($set)) {
            $DB->query(sprintf(
                "UPDATE `highwaygroup`.`payment_methods` SET %s WHERE `payment_method_id` = %s AND `site_id` = %d LIMIT 1",
                implode(',', $set),
                $DB->escape($posted['payment_method_id']),
                $posted['site_id']
            ));
        }

        //now have to check if the payment method is related to an invoice charge.
        if ($row = $DB->query_first(sprintf(
            "SELECT `payment_charge_percent`, `payment_charge_amount`, `charges_link` FROM `highwaygroup`.`payment_methods` WHERE `payment_method_id` = %s AND `site_id` = %d AND `charges_link` <> 0 LIMIT 1",
            $DB->escape($posted['payment_method_id']),
            $posted['site_id']
        ), MYSQLI_ASSOC)) {
            $DB->query(sprintf(
                "UPDATE `highwaygroup`.`charges` SET `charge_percent` = %s, `charge_amount` = %s WHERE `charge_id` = %d LIMIT 1",
                $DB->escape($row['payment_charge_percent']),
                $DB->escape($row['payment_charge_amount']),
                $row['charges_link']
            ));
        }
    }
}
