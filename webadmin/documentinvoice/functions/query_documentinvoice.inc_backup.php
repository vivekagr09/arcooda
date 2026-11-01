<?php
if (!function_exists("documentinvoiceChunk")) {
    function documentinvoiceChunk($sinvoice_list, $group_by = '')
    {
        global $DB;
        if (!is_array($sinvoice_list) || count($sinvoice_list) < 1) {
            return $sinvoice_list;
        }

        if (!is_array($group_by)) {
            $group_by = array($group_by);
        }

        $fields = getTableColumns('`highwaygroup`.`documentinvoices`');
        foreach ($fields as $key => $value) {
            $fields[$key] = '`s`.`' . $value . '`';
        }

        foreach (documentinvoiceGetOptionList('ext_fields') as $key => $value) {
            $fields[$key] = $value . ' AS ' . $key;
        }

        $field_list = array('dinvoice_id');
        foreach ($group_by as $field) {
            if (array_key_exists($field, $fields)) {
                $field_list[] = $fields[$field];
            }
        }

        $field_list = array_unique($field_list);
        $query      = $DB->query("SELECT " . implode(', ', $field_list) . " FROM `highwaygroup`.`documentinvoices` AS `s` WHERE `s`.`dinvoice_id` IN(" . implode(', ', $sinvoice_list) . ")");
        $results    = array();
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
            $pointer[] = $row['dinvoice_id'];
        }
        $DB->free_result($query);
        return $results;
    }
}
if (!function_exists("documentinvoiceDelete")) {
    function documentinvoiceDelete($id)
    {
        return false;
    }
}
if (!function_exists("documentinvoiceExists")) {
    function documentinvoiceExists($id)
    {
        global $DB;
        $row = $DB->query_first("SELECT `dinvoice_id` FROM `highwaygroup`.`documentinvoices` WHERE `dinvoice_id` = " . intval($id) . " LIMIT 1", MYSQLI_ASSOC);
        return (0 != $row['dinvoice_id']);
    }
}
if (!function_exists("documentinvoiceFormatNumber")) {
    function documentinvoiceFormatNumber($id, $revision_id = 0, $site_id = 0)
    {
        global $DB;
        $result = 'D#';
        if ($site_id > 0) {
            $row = $DB->query_first("SELECT `company_invoicesuffix` FROM `highwaygroup`.`company_info` WHERE `company_id` = " . intval($site_id) . " LIMIT 1");
            if ($row) {
                $result = 'SD' . substr($row['company_invoicesuffix'], 0, 2) . '#';
            }
        }
        $result .= str_pad($id, 4, '0', STR_PAD_LEFT);
        return $result;
    }
}
if (!function_exists("documentinvoiceFromDatabase")) {
    function documentinvoiceFromDatabase($dinvoice_id)
    {
        global $DB;
        $dinvoice_id = intval($dinvoice_id);
        $fields      = array();
        foreach (documentinvoiceGetOptionList('ext_fields') as $key => $value) {
            $fields[] = $value . ' AS ' . $key;
        }

        $fields[] = "s.*";
        $summary  = array();
        if ($row = $DB->query_first("SELECT " . implode(', ', $fields) . " FROM `highwaygroup`.`documentinvoices` s WHERE s.dinvoice_id=" . $dinvoice_id . " LIMIT 1", MYSQLI_ASSOC)) {
            $country_list = getCountryList(false);
            $bill_array   = array(('' != $row['bill_company']) ? (trim($row['bill_company'])) : (trim($row['bill_name'])), ('' != $row['bill_company']) ? ('Attn: ' . $row['bill_name']) : '', trim($row['bill_address']), trim(trim(trim($row['bill_city'] . ', ' . $row['bill_state'] . ' ' . $row['bill_postcode']), ',')), trim($country_list[$row['bill_country']]), "\r", //For some reason I can't just put a space here
                ('' != $row['bill_phone']) ? ('Ph: ' . $row['bill_phone']) : '', ('' != $row['bill_email']) ? ('Email: ' . $row['bill_email']) : '');
            foreach ($bill_array as $key => $value) {
                if ('' == $value) {
                    unset($bill_array[$key]);
                }
            }

            $row['bill_address_full'] = implode("\n", $bill_array);
            $ship_array               = array(('' != $row['ship_company']) ? (trim($row['ship_company'])) : (trim($row['ship_name'])), ('' != $row['ship_company']) ? ('Attn: ' . $row['ship_name']) : '', trim($row['ship_address']), trim(trim(trim($row['ship_city'] . ', ' . $row['ship_state'] . ' ' . $row['ship_postcode']), ',')), trim($country_list[$row['ship_country']]), "\r", //For some reason I can't just put a space here
                //($new_vals['ship_name']!='' && $new_vals['ship_company']!=$new_vals['ship_name'])?('Attn: '.$new_vals['ship_name']):'',
                ('' != $row['ship_phone']) ? ('Ph: ' . $row['ship_phone']) : '', ('' != $row['ship_email']) ? ('Email: ' . $row['ship_email']) : '');
            foreach ($ship_array as $key => $value) {
                if ('' == $value) {
                    unset($ship_array[$key]);
                }
            }

            $row['ship_address_full'] = implode("\n", $ship_array);
            if ('' != $row['notify_address']) {
                $notify_array = array(('' != $row['notify_company']) ? (trim($row['notify_company'])) : (trim($row['notify_name'])), ('' != $row['notify_company']) ? ('Attn: ' . $row['notify_name']) : '', trim($row['notify_address']), trim(trim(trim($row['notify_city'] . ', ' . $row['notify_state'] . ' ' . $row['notify_postcode']), ',')), trim($country_list[$row['notify_country']]), "\r", ('' != $row['notify_phone']) ? ('Ph: ' . $row['notify_phone']) : '', ('' != $row['notify_email']) ? ('Email: ' . $row['notify_email']) : '');
                foreach ($notify_array as $key => $value) {
                    if ('' == $value) {
                        unset($notify_array[$key]);
                    }
                }

                $row['notify_address_full'] = implode("\n", $notify_array);
            } else {
                $row['notify_address_full'] = $row['ship_address_full'];
            }

            $summary = $row;
        } else {
            foreach (getTableFields('`highwaygroup`.`documentinvoices`') as $row) {
                $summary[$row[0]] = $row[4];
            }
        }

        return $summary;
    }
}
if (!function_exists("documentinvoiceFromPost")) {
    function documentinvoiceFromPost($posted)
    {
        global $DB;
        $dinvoice_id = $posted['dinvoice_id'];
        if (0 == $dinvoice_id) {
            $dinvoice_id = $posted['invoice_id'];
        }

        $old_vals = documentinvoiceFromDatabase($dinvoice_id);
        $new_vals = $old_vals;
        foreach ($old_vals as $key => $value) {
            if (is_array($posted) && array_key_exists($key, $posted)) {
                $new_vals[$key] = $posted[$key];
            }
        }

        if ('' == $posted['show_order']) {
            $new_vals['show_order'] = 'n';
        }

        if ('' == $posted['show_freight']) {
            $new_vals['show_freight'] = 'n';
        }

        if ('' == $posted['show_freight_cost']) {
            $new_vals['show_freight_cost'] = 'n';
        }

        if ('' == $posted['show_payment']) {
            $new_vals['show_payment'] = 'n';
        }

        if ('' == $posted['show_payment_info']) {
            $new_vals['show_payment_info'] = 'n';
        }

        if ('' == $posted['show_signature']) {
            $new_vals['show_signature'] = 'n';
        }

        if ('' == $posted['show_stamp']) {
            $new_vals['show_stamp'] = 'n';
        }

        $new_vals['dinvoice_id'] = $dinvoice_id;
        if ($dinvoice_id <= 0) {
            unset($new_vals['dinvoice_id']);
        }

        //Copy posted invoice values
        $array = array('name', 'email', 'company', 'address', 'city', 'state', 'postcode', 'country', 'phone');
        foreach ($array as $name) {
            $new_vals['bill_' . $name] = $posted['bill_' . $name];
            $new_vals['ship_' . $name] = $posted['ship_' . $name];
        }
        //unset($new_vals['ship_email']);
        $country_list = getCountryList(false);
        $bill_array   = array(('' != $new_vals['bill_company']) ? (trim($new_vals['bill_company'])) : (trim($new_vals['bill_name'])), ('' != $new_vals['bill_company']) ? ('Attn: ' . $new_vals['bill_name']) : '', trim($new_vals['bill_address']), trim(trim(trim($new_vals['bill_city'] . ', ' . $new_vals['bill_state'] . ' ' . $new_vals['bill_postcode']), ',')), trim($country_list[$new_vals['bill_country']]), "\r", //For some reason I can't just put a space here
            ('' != $new_vals['bill_phone']) ? ('Ph: ' . $new_vals['bill_phone']) : '', ('' != $new_vals['bill_email']) ? ('Email: ' . $new_vals['bill_email']) : '');
        foreach ($bill_array as $key => $value) {
            if ('' == $value) {
                unset($bill_array[$key]);
            }
        }

        $new_vals['bill_address_full'] = implode("\n", $bill_array);
        $ship_array                    = array(('' != $new_vals['ship_company']) ? (trim($new_vals['ship_company'])) : (trim($new_vals['ship_name'])), ('' != $new_vals['ship_company']) ? ('Attn: ' . $new_vals['ship_name']) : '', trim($new_vals['ship_address']), trim(trim(trim($new_vals['ship_city'] . ', ' . $new_vals['ship_state'] . ' ' . $new_vals['ship_postcode']), ',')), trim($country_list[$new_vals['ship_country']]), "\r", //For some reason I can't just put a space here
            //($new_vals['ship_name']!='' && $new_vals['ship_company']!=$new_vals['ship_name'])?('Attn: '.$new_vals['ship_name']):'',
            ('' != $new_vals['ship_phone']) ? ('Ph: ' . $new_vals['ship_phone']) : '', ('' != $new_vals['bill_email']) ? ('Email: ' . $new_vals['bill_email']) : '');
        foreach ($ship_array as $key => $value) {
            if ('' == $value) {
                unset($ship_array[$key]);
            }
        }

        $new_vals['ship_address_full'] = implode("\n", $ship_array);
        $notify_array                  = array(('' != $new_vals['notify_company']) ? (trim($new_vals['notify_company'])) : (trim($new_vals['notify_name'])), ('' != $new_vals['notify_company']) ? ('Attn: ' . $new_vals['notify_name']) : '', trim($new_vals['notify_address']), trim(trim(trim($new_vals['notify_city'] . ', ' . $new_vals['notify_state'] . ' ' . $new_vals['notify_postcode']), ',')), trim($country_list[$new_vals['notify_country']]), "\r", //For some reason I can't just put a space here
            //($new_vals['ship_name']!='' && $new_vals['ship_company']!=$new_vals['ship_name'])?('Attn: '.$new_vals['ship_name']):'',
            ('' != $new_vals['notify_phone']) ? ('Ph: ' . $new_vals['notify_phone']) : '', ('' != $new_vals['notify_email']) ? ('Email: ' . $new_vals['notify_email']) : '');
        foreach ($notify_array as $key => $value) {
            if ('' == $value) {
                unset($notify_array[$key]);
            }
        }

        $new_vals['notify_address_full'] = implode("\n", $notify_array);
        if (!documentinvoiceExists($dinvoice_id)) {
            $new_vals['open_time'] = time();
            $new_vals['open_date'] = formatDateTime();
        }
        /*
        $array=array('invoice_offer','invoice_offerau','products_offer', 'products_offerau', 'charges_offer', 'charges_offerau', 'ship_product_name', 'ship_id', 'ship_offer', 'ship_offerau', 'ship_account_num', 'ship_weight', 'ship_cubic', 'member_id', 'invoice_currency', 'site_id', 'company_name', 'company_address');
        foreach($array as $value)
        $new_vals[$value]=$posted[$value];
         */
        $array = array('products_offer', 'ship_offer', 'charges_offer', 'invoice_offer');
        foreach ($array as $key1) {
            $key2 = $key1 . 'au';
            if (!isset($posted[$key1]) && isset($posted[$key2])) {
                $new_vals[$key1] = convertCurrency($posted[$key2], 'AUD', $new_vals['invoice_currency']);
            }

            if (!isset($posted[$key2]) && isset($posted[$key1])) {
                $new_vals[$key2] = convertCurrency($posted[$key1], $new_vals['invoice_currency'], 'AUD');
            }

            if (0 == $posted[$key1] && 0 != $posted[$key2]) {
                $new_vals[$key1] = convertCurrency($posted[$key2], 'AUD', $new_vals['invoice_currency']);
            }

            if (0 == $posted[$key2] && 0 != $posted[$key1]) {
                $new_vals[$key2] = convertCurrency($posted[$key1], $new_vals['invoice_currency'], 'AUD');
            }
        }
        /*
        $new_vals['sinvoice_offer']=$new_vals['products_offer']+$new_vals['ship_offer']+$new_vals['charges_offer'];
        $new_vals['sinvoice_offerau']=$new_vals['products_offerau']+$new_vals['ship_offerau']+$new_vals['charges_offerau'];
         */
        return $new_vals;
    }
}
if (!function_exists("documentinvoiceGetOptionList")) {
    function documentinvoiceGetOptionList($key)
    {
        static $cache = array();
        if (array_key_exists($key, $cache)) {
            return $cache[$key];
        }

        switch ($key) {
            case 'joined_fields':
                return array();

            case 'extra_fields':
                //no break
            case 'ext_fields':
                return array(
                    'open_time'          => 'UNIX_TIMESTAMP(`open_date`)',
                    'proforma_time'      => 'UNIX_TIMESTAMP(`proforma_date`)',
                    'last_modified_time' => 'UNIX_TIMESTAMP(`last_modified`)',
                );
        }

        moduleLoad('invoice');
        if (function_exists('invoiceGetOptionList')) {
            return invoiceGetOptionList($key);
        }

        return $cache[$key] = null;
    }
}
if (!function_exists("documentinvoiceGetValue")) {
    function documentinvoiceGetValue($id, $field)
    {
        global $DB;
        $fields = array_merge(getTableColumns('`highwaygroup`.`documentinvoices`'), documentinvoiceGetOptionList('ext_fields'));
        if (array_key_exists($field, $fields)) {
            $row = $DB->query_first("SELECT " . $fields[$field] . " AS " . $field . " FROM `highwaygroup`.`documentinvoices` WHERE `dinvoice_id` = " . intval($id) . " LIMIT 1", MYSQLI_ASSOC);
            return $row[$field];
        }
        return null;
    }
}
if (!function_exists("documentinvoiceHasValue")) {
    function documentinvoiceHasValue($dinvoice_id, $field, $value)
    {
        return (documentinvoiceGetValue($dinvoice_id, $field) === $value);
    }
}
if (!function_exists("documentinvoiceSearch")) {
    function documentinvoiceSearch($criteria = '')
    {
        global $DB;
        $results = array();
        $sql     = " SELECT s.dinvoice_id FROM `highwaygroup`.`documentinvoices` s";
        $joins   = array();
        $fields  = array();
        foreach (getTableColumns('`highwaygroup`.`documentinvoices`') as $key => $value) {
            $fields[$key] = 's.' . $value;
        }

        foreach (documentinvoiceGetOptionList('ext_fields') as $key => $value) {
            $fields[$key] = $value . ' AS ' . $key;
        }

        $whereAND = array();
        if (is_array($criteria)) {
            foreach ($criteria as $field => $value) {
                if (array_key_exists($field, $fields)) {
                    if (is_array($value)) {
                        if (count($value) > 0) {
                            $whereOR = array();
                            foreach ($value as $sub_value) {
                                if (is_null($sub_value)) {
                                    $whereOR[] = "	" . $fields[$field] . " IS NULL";
                                } else {
                                    $whereOR[] = "	" . $fields[$field] . "=" . formatSQLString($sub_value);
                                }
                            }

                            $whereAND[] = "	(" . implode(" OR ", $whereOR) . ")";
                        } else {
                            return array();
                        }
                    } elseif (is_null($value)) {
                        $whereAND[] = "	" . $fields[$field] . " IS NULL";
                    } else {
                        $whereAND[] = "	" . $fields[$field] . "=" . formatSQLString($value);
                    }

                    unset($fields[$field]);
                } elseif (is_string($criteria) && '' != $criteria) {
                    $criteria = array('any_text' => $criteria);
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
            $searchBits = array('s.dinvoice_id');
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
            $sql .= " " . implode(" ", $joins);
        }

        if (count($whereAND) > 0) {
            $sql .= " WHERE " . implode(" AND ", $whereAND);
        }

        $sql .= " ORDER BY s.dinvoice_id ASC";
        $query = $DB->query($sql);
        while ($row = $DB->fetch_assoc($query)) {
            $results[] = $row['dinvoice_id'];
        }

        $DB->free_result($query);
        return $results;
    }
}
if (!function_exists("documentinvoiceSort")) {
    function documentinvoiceSort($sinvoice_list, $order_by = 'dinvoice_id')
    {
        global $DB;
        if (!is_array($sinvoice_list) || count($sinvoice_list) < 2) {
            return $sinvoice_list;
        }

        $fields = array_merge(getTableColumns('`highwaygroup`.`documentinvoices`'), documentinvoiceGetOptionList('ext_fields'));
        if (!array_key_exists($order_by, $fields)) {
            return $sinvoice_list;
        }

        $sql           = " SELECT s.dinvoice_id FROM `highwaygroup`.`documentinvoices` s";
        $where         = " WHERE s.dinvoice_id IN(" . implode(',', $sinvoice_list) . ")";
        $order         = " ORDER BY " . $fields[$order_by] . " ASC";
        $sinvoice_list = array();
        $query         = $DB->query($sql . $where . $order);
        while ($row = $DB->fetch_assoc($query)) {
            $sinvoice_list[] = $row['dinvoice_id'];
        }

        $DB->free_result($query);
        return $sinvoice_list;
    }
}
if (!function_exists("documentinvoiceToDatabase")) {
    function documentinvoiceToDatabase($summary)
    {
        global $DB;
        $dinvoice_id  = intval($summary['dinvoice_id']);
        $old_sinvoice = documentinvoiceFromDatabase($dinvoice_id);
        foreach ($old_sinvoice as $key => $value) {
            if ($summary[$key] == $value) {
                unset($summary[$key]);
            }
        }

        foreach ($summary as $key => $value) {
            if (!array_key_exists($key, $old_sinvoice)) {
                unset($summary[$key]);
            }
        }

        $fields = getTableColumns('`highwaygroup`.`documentinvoices`');
        if (documentinvoiceExists($dinvoice_id)) {
            $set_vals = array();
            foreach ($summary as $key => $value) {
                if (array_key_exists($key, $fields)) {
                    if (array_key_exists($key, $summary)) {
                        $set_vals[$key] = "`" . $key . "`=" . formatSQLString($value);
                    } elseif (is_null($summary[$key])) {
                        $set_vals[$key] = "`" . $key . "`=NULL";
                    }
                }
            }

            $sql = "UPDATE `highwaygroup`.`documentinvoices` SET " . implode(', ', $set_vals) . ", last_modified=NOW() WHERE dinvoice_id=" . $dinvoice_id . " LIMIT 1";
            if (count($set_vals) > 0) {
                $DB->query($sql);
            }
        } else {
            $set_vals = array();
            foreach ($summary as $key => $value) {
                if (array_key_exists($key, $fields)) {
                    if (array_key_exists($key, $summary)) {
                        $set_vals['`' . $key . '`'] = formatSQLString($value);
                    } elseif (is_null($summary[$key])) {
                        $set_vals['`' . $key . '`'] = 'NULL';
                    }
                }
            }

            $sql         = "INSERT INTO `highwaygroup`.`documentinvoices`(" . implode(', ', array_keys($set_vals)) . ") VALUES(" . implode(", ", $set_vals) . ")";
            $query       = $DB->query($sql);
            $dinvoice_id = $DB->insert_id;
        }
        return $dinvoice_id;
    }
}
if (!function_exists("documentinvoiceSetValue")) {
    function documentinvoiceSetValue($dinvoice_id, $field, $value)
    {
        global $DB;
        $fields = getTableColumns('`highwaygroup`.`documentinvoices`');
        if (array_key_exists($field, $fields)) {
            $DB->query("UPDATE `highwaygroup`.`documentinvoices` SET `" . $field . "`=" . formatSQLString($value) . " WHERE dinvoice_id=" . intval($dinvoice_id) . " LIMIT 1");
            return true;
        }
        return false;
    }
}
if (!function_exists("documentinvoiceTemplateVars")) {
    function documentinvoiceTemplateVars($sinvoice = array())
    {
        global $DB;
        $dinvoice_id = intval($sinvoice['dinvoice_id']);
        $results     = array();
        foreach (getTableColumns('`highwaygroup`.`documentinvoices`') as $key) {
            $results['[' . strtoupper($key) . ']'] = '';
        }

        foreach (array() as $key) {
            $results['[' . strtoupper($key) . ']'] = '';
        }

        foreach ($results as $key => $value) {
            $results[$key] = str_replace("\r", '', $value);
        }

        return $results;
    }
}
if (!function_exists("documentinvoiceSumValue")) {
    function documentinvoiceSumValue($sinvoice_list, $field)
    {
        global $DB;
        $fields = array();
        $query  = $DB->query("SHOW COLUMNS FROM `highwaygroup`.`documentinvoices`");
        while ($row = $DB->fetch_array($query, MYSQLI_NUM)) {
            $fields[$row[0]] = $row;
        }

        $DB->free_result($query);
        if (array_key_exists($field, $fields)) {
            $row = $DB->query_first("SELECT SUM(`" . $field . "`) AS sum_value FROM `highwaygroup`.`documentinvoices` WHERE dinvoice_id IN(" . implode(',', $sinvoice_list) . ")");
            return $row['sum_value'];
        }
        return null;
    }
}
