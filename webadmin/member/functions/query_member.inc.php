<?php
//moduleLoad('phpmailer');

if (!defined('MEMBER_TABLENAME')) {
    define('MEMBER_TABLENAME', "`highwaygroup`.`new_members`");
}
if (!defined('MEMBER_PRIMARYKEY')) {
    define('MEMBER_PRIMARYKEY', "member_id");
}

if (!function_exists('memberChunk')) {
    /**
     * @fixme first parameter should accept a WHERE string
     * @fixme $split_sets is deprecated, default to on
     *
     * @param int[]           $member_list
     * @param string[]|string $group_by
     * @param bool            $split_sets
     *
     * @return mixed[]
     */
    function memberChunk($member_list, $group_by = '', $split_sets = true)
    {
        global $DB;

        if (!is_array($member_list) || count($member_list) < 1) {
            return $member_list;
        }

        if (!is_array($group_by)) {
            $group_by = array($group_by);
        }

        $fields      = memberGetOptionList('all_fields');
        $field_types = memberGetOptionList('all_fields_types');
        $field_list  = array(MEMBER_PRIMARYKEY);
        foreach ($group_by as $field) {
            if (array_key_exists($field, $fields)) {
                $field_list[] = sprintf("%s AS `%s`", $fields[$field], $field);
            }
        }

        $field_list = array_unique($field_list);

        $result = array();
        if ($query = $DB->query(sprintf(
            "SELECT %s FROM %s AS `m` WHERE `m`.`%s` IN('%s')",
            implode(", ", $field_list),
            MEMBER_TABLENAME,
            MEMBER_PRIMARYKEY,
            implode("', '", $member_list)
        ))) {
            while ($row = $DB->fetch_assoc($query)) {
                $pointers = array(&$result);
                foreach ($group_by as $group) {
                    $key = $row[$group];
                    if ('' == $key) {
                        $key = null;
                    }
                    $new_pointers = array();
                    foreach ((substr($field_types[$group]['Type'], 0, 4) == 'set(' && $split_sets) ? explode(',', $key) : array($key) as $key2) {
                        foreach ($pointers as $key1 => $pointer) {
                            if (!is_array($pointers[$key1][$key2])) {
                                $pointers[$key1][$key2] = array();
                            }
                            $new_pointers[] = &$pointers[$key1][$key2];
                        }
                    }
                    $pointers = $new_pointers;
                }
                foreach ($pointers as $pointer_key => $pointer) {
                    $pointers[$pointer_key][] = $row[MEMBER_PRIMARYKEY];
                }
            }
            $DB->free_result($query);
        }

        return $result;
    }
}
if (!function_exists('memberDefault')) {
    /**
     * @return mixed[]
     */
    function memberDefault()
    {
        global $DB;

        return memberVirtual($DB->modelDefault(
            MEMBER_TABLENAME,
            MEMBER_PRIMARYKEY
        ), false);
    }
}
if (!function_exists('memberEmailExists')) {
    function memberEmailExists($email, $site = '')
    {
        if ('' == $email) {
            return false;
        }

        return (count(memberSearch(array('email_address' => $email))) > 0);
    }
}
if (!function_exists('memberExists')) {
    /**
     * @fixme Replace with $DB->modelExists($id, MEMBER_TABLENAME, MEMBER_PRIMARYKEY) when available
     *
     * @param int $id
     *
     * @return bool
     */
    function memberExists($id)
    {
        global $DB;

        return !!$DB->query_first(sprintf(
            "SELECT 1 FROM %s AS `m` WHERE `m`.`%s` = %d LIMIT 1",
            MEMBER_TABLENAME,
            MEMBER_PRIMARYKEY,
            $id
        ), MYSQLI_ASSOC);
    }
}
if (!function_exists('memberFormatAddress')) {
    function memberFormatAddress($member)
    {
        $country_list = getCountryList(false);
        $address      = array();
        if (isset($member['company_name'])) {
            $address[] = $member['company_name'];
        }

        if (isset($member['address'])) {
            $address[] = $member['address'];
        }

        $addline = '';
        if (isset($member['city'])) {
            $addline .= $member['city'] . ', ';
        }

        if (isset($member['state'])) {
            $addline .= $member['state'] . ' ';
        }

        if (isset($member['post_code'])) {
            $addline .= $member['post_code'];
        }

        if ('' != $addline) {
            $address[] = $addline;
        }

        if (isset($country_list[$member['country']])) {
            $address[] = $country_list[$member['country']];
        }

        $address[] = '';
        if (isset($member['phone_no']) && '' != $member['phone_no']) {
            $address[] = "\nPh: " . $member['phone_no'];
        }

        return implode("\n", $address);
    }
}
if (!function_exists('memberFormatNumber')) {
    function memberFormatNumber($member_id)
    {
        return sprintf('CU#%06d', $member_id);
    }
}
if (!function_exists('memberFromDatabase')) {
    /**
     * @fixme $invoice functionality should be relocated
     * @fixme Replace with $DB->modelLoad($id, MEMBER_TABLENAME, MEMBER_PRIMARYKEY) when available
     * @fixme memberDefault functionality should be relocated, replace with null
     *
     * @param int  $id
     * @param bool $invoice
     *
     * @return mixed[]|null
     */
    function memberFromDatabase($id, $invoice = false)
    {
        moduleLoad('invoice'); //invoiceHistoryFromDatabase, invoiceHistorySearch, invoiceSearch, invoiceChunk, invoiceSort, invoiceGetValue, invoiceSumValue

        global $DB;

        $result = array();

        if (memberExists($id)) {
            $fields = array('*' => 'm.*');
            foreach (memberGetOptionList('ext_fields') as $k => $v) {
                $fields[$k] = $v . " AS `" . $k . "`";
            }

            $result = $DB->query_first(sprintf(
                "SELECT %s FROM %s AS `m` WHERE `m`.`member_id` = %d LIMIT 1",
                implode(", ", $fields),
                MEMBER_TABLENAME,
                $id
            ), MYSQLI_ASSOC);
            $result['address_full'] = memberFormatAddress($result);

            if ($invoice) {
                $invoice_list   = invoiceSearch(array('member_id' => $id));
                $invoice_chunks = invoiceChunk($invoice_list, 'invoice_status');

                foreach ($invoice_chunks as $k => $v) {
                    $invoice_chunks[$k] = array_reverse(invoiceSort($v, 'proforma_date'));
                }

                foreach (array('processed', 'sales', 'proforma', 'inquiry', 'closed', 'cancelled') as $k) {
                    if (!is_array($invoice_chunks[$k])) {
                        $invoice_chunks[$k] = array();
                    }
                }

                $result['all_invoices']    = count($invoice_list);
                $result['total_inquiries'] = count($invoice_chunks['inquiry']) + count($invoice_chunks['closed']);
                $result['total_invoices']  = count($invoice_chunks['processed']);
                $result['total_orders']    = count($invoice_chunks['proforma']) + count($invoice_chunks['sales'] + $invoice_chunks['processed']);

                $temp                      = invoiceGetValue(reset($invoice_chunks['processed']), 'proforma_time');
                $result['last_order_time'] = ((0 != $temp) ? date(_('d/m/Y'), $temp) : 'Never');

                $temp                    = max($temp, invoiceGetValue(reset($invoice_chunks['inquiry']), 'proforma_time'));
                $temp                    = max($temp, invoiceGetValue(reset($invoice_chunks['closed']), 'proforma_time'));
                $result['last_inq_time'] = ((0 != $temp) ? date(_('d/m/Y'), $temp) : 'Never');

                $temp                    = invoiceSumValue($invoice_chunks['processed'], 'invoice_offerau');
                $result['order_total']   = '$' . convertCurrency($temp, 'USD', 'USD') . ' USD';
                $result['order_totalau'] = convertCurrency($temp, 'AUD');

                $temp                          = invoiceSumValue(array_merge($invoice_chunks['proforma'], $invoice_chunks['sales']), 'invoice_offerau');
                $result['outstanding_payment'] = '$' . convertCurrency($temp, 'AUD', 'USD') . ' USD';
                $temp += invoiceSumValue($invoice_chunks['processed'], 'invoice_offerau');
                $result['invoice_total'] = convertCurrency($temp, 'AUD');
                $result['invoice_totalus']   = '$' . convertCurrency($temp, 'USD', 'USD') . ' USD';
                $last_message                  = invoiceHistoryFromDatabase(reset(invoiceHistorySearch(array('member_id' => $id, 'message_type_id' => 2))));
                $result['last_contacted']      = $last_message['message_date'];
                $result['time_last_contacted'] = $last_message['message_time'];
            }
        } else {
            $result = memberDefault();

            unset($result['member_id']);
            unset($result['id']);
        }
        return $result;
    }
}
if (!function_exists('memberFromEmail')) {
    function memberFromEmail($email)
    {
        global $DB;

        if ('' == $email) {
            return null;
        }

        if ($row = $DB->query_first(sprintf(
            "SELECT * FROM %s AS `m` WHERE `m`.`email_address` = %s LIMIT 1",
            MEMBER_TABLENAME,
            formatSQLString($email)
        ), MYSQLI_ASSOC)) {
            return memberVirtual($row);
        }

        return null;
    }
}
if (!function_exists('memberFromPost')) {
    function memberFromPost($posted)
    {
        global $_GLOBALS;

        $member_id             = $posted['member_id'];
        $new_vals['member_id'] = intval($member_id);
        $old_vals              = memberFromDatabase($member_id);
        foreach (array("spammer", "timewaster", "tester", "sales_related","mailing_list") as $v) {
            if (!array_key_exists($v, $posted["member"])) {
                $posted["member"][$v] = "n";
            }
        }

        if (array_key_exists('member_check', $posted)) {
            $new_vals['check_staff_id'] = $_GLOBALS['staff']['id'];
            $new_vals['check_date']     = formatDateTime();
        }

        foreach ($old_vals as $key => $value) {
            if (isset($posted['member'][$key])) {
                $new_vals[$key] = $posted['member'][$key];
            }
        }

        if ('on' === $posted['hwgaming_products_update']) {
            if (array_key_exists('hwgaming_products', $posted)) {
                sort($posted['hwgaming_products'], SORT_NUMERIC);
                $new_vals['hwgaming_products'] = implode(',', $posted['hwgaming_products']);
            } else {
                $new_vals['hwgaming_products'] = '';
            }

        }

        if(is_array($posted['customer_type'])) {
            $new_vals['customer_type'] = implode(',',$posted['customer_type']);
        }

        $site_ids = array();
        if (is_array($posted['member_site'])) {
            foreach ($posted['member_site'] as $key => $on) {
                $site_ids[] = $key;
            }
        }

        $new_vals['site_id'] = implode(',', $site_ids);
        return $new_vals;
    }
}
if (!function_exists('memberGenericsDelete')) {
    function memberGenericsDelete($id, $table, $secondary_key = 'view_id')
    {
        global $DB;
        $DB->query(sprintf(
            "DELETE FROM %s WHERE `%s` = %d",
            $table,
            $secondary_key,
            $id
        ));
        return $DB->affected_rows();
    }
}
if (!function_exists('memberGenericsFromPost')) {
    function memberGenericsFromPost($posted, $table)
    {
        global $DB;

        if (!is_array($posted)) {
            return array();
        }

        $result = array();
        $fields = $DB->get_table_columns($table);
        foreach ($posted as $k1 => $v1) {
            $item = array();
            foreach ($fields as $k2 => $v2) {
                if (array_key_exists($k2, $v1)) {
                    $item[$k2] = $v1[$k2];
                }
            }
            $result[$k1] = $item;
        }

        return $result;
    }
}
if (!function_exists('memberGenericToDatabase')) {
    function memberGenericToDatabase(&$item, $table, $primary_key = 'id')
    {
        global $DB, $_GLOBALS;
        $id       = intval($item[$primary_key]);
        $fields   = $DB->get_table_columns($table);
        $old_vals = $DB->query_first(sprintf(
            "SELECT * FROM %s WHERE `%s` = %d LIMIT 1",
            $table,
            $primary_key,
            $id
        ), MYSQLI_ASSOC);
        if ($old_vals) {
            $set_vals = array();
            foreach ($fields as $k => $v) {
                if (array_key_exists($k, $item) && $item[$k] != $old_vals[$k]) {
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
                $DB->query(sprintf(
                    "UPDATE %s SET %s WHERE `%s` = %d LIMIT 1",
                    $table,
                    implode(', ', $set_vals),
                    $primary_key,
                    $id
                ));
            }
        } else {
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

                $id = $DB->insert_id();
            }
        }
        deleteMysqlCacheData($table, $id);
        return $id;
    }
}
if (!function_exists('memberGetLock')) {
    function memberGetLock($id)
    {
        global $DB;

        if ($row = $DB->query_first(sprintf(
            "SELECT *, UNIX_TIMESTAMP(`expiry_date`) AS `expiry_time` FROM `highwaygroup`.`generic_locks` WHERE `section_name` = 'member' AND `generic_id` = %d AND `expiry_date` > NOW() LIMIT 1",
            $id
        ), MYSQLI_ASSOC)) {
            if ($row['expiry_time'] < time()) {
                $row = false;
            }
        }

        return $row;
    }
}
if (!function_exists('memberGetLocks')) {
    function memberGetLocks()
    {
        global $DB;

        $result = array();
        if ($query  = $DB->query(sprintf(
            "SELECT *, UNIX_TIMESTAMP(`expiry_date`) AS `expiry_time` FROM `highwaygroup`.`generic_locks` WHERE `section_name` = 'member' AND `expiry_date` > NOW()"
        ))) {
            while ($row = $DB->fetch_assoc($query)) {
                $result[$row["generic_id"]] = $row;
            }
            $DB->free_result($query);
        }

        return $result;
    }
}
if (!function_exists('memberGetOptionList')) {
    /**
     * @fixme replace the all_fields/base_fields/ext_fields code
     *
     * @param string $table_name
     *
     * @return mixed
     */
    function memberGetOptionList($table_name = '')
    {
        global $DB;

        $table       = "";
        $key         = "";
        $value       = "";
        $option_list = array();
        switch ($table_name) {
            case 'currency':
                return getCurrencyList();

            case 'country':
                return getCountryList();

            case 'type_site':
                return array(
                    'asp'    => 20,
                    'car'    => 16,
                    'cnv'    => 9,
                    'he'     => 2,
                    'hg'     => 1,
                    'xbox'   => 18,
                    'agnet'  => 24,
                    'fchcom' => 26,
                );

            case 'member_type':
                $result = getWebsitesList();
                foreach (memberGetOptionList('type_site') as $k => $v) {
                    $result[$k] = $result[$v];
                    unset($result[$v]);
                }
                asort($result);
                return $result;

            case 'best_described':
                /*$table = "`highwaygroup`.`best_described`";
                $key   = "`code`";
                $value = "`best_described`";*/
                return ['commercial' => 'Commercial customer','residential' => 'Residential customer'];
                break;

            case 'site_id':
                $fields = getTableFields(MEMBER_TABLENAME);
                if (substr($fields['site_id']['Type'], 0, 4) === "set(") {
                    return array_intersect_key(getWebsitesList(), array_flip(explode("','", substr($fields['site_id'][1], 5, -2))));
                }
                return getWebsitesList();

            case 'status':
                return array(
                    'active'        => 'Active',
                    'not_active'    => 'Not Active',
                    'not_contacted' => 'Active But Never Contacted',
                );

            case 'all_fields':
                return array_merge(memberGetOptionList('base_fields'), memberGetOptionList('ext_fields'));

            case 'all_fields_types':
                return array_merge(memberGetOptionList('base_fields_types'), memberGetOptionList('ext_fields_types'));

            case 'base_fields':
                $fields = array();
                foreach (getTableColumns(MEMBER_TABLENAME) as $key) {
                    $fields[$key] = sprintf("`m`.`%s`", $key);
                }
                return $fields;

            case 'base_fields_types':
                return getTableFields(MEMBER_TABLENAME);

            case 'joined_fields':
                return array();

            case 'extra_fields':
                //no break
            case 'ext_fields':
                return array(
                    'id'            => sprintf("`m`.`%s`", MEMBER_PRIMARYKEY),
                    'time_joined'   => "UNIX_TIMESTAMP(`m`.`date_joined`)",
                    'last_initial'  => "LEFT(`m`.`last_name`, 1)",
                    'full_name'     => "CONCAT_WS(' ', `m`.`first_name`, `m`.`last_name`)",
                    'member_number' => sprintf(
                        "CONCAT('CU#', LPAD(`m`.`%s`, 5, '0'))",
                        MEMBER_PRIMARYKEY
                    ),
                );

            case 'ext_fields_types':
                return array();
        }
        if ('' != $table) {
            if ($query = $DB->query(sprintf(
                "SELECT %s, %s FROM %s",
                $key,
                $value,
                $table
            ))) {
                while ($row = $DB->fetch_array($query)) {
                    $option_list[$row[0]] = $row[1];
                }
                $DB->free_result($query);
            }
        }
        return $option_list;
    }
}
if (!function_exists('memberGetValue')) {
    function memberGetValue($member_id, $field)
    {
        global $DB;

        $fields = memberGetOptionList('all_fields');
        if (array_key_exists($field, $fields)) {
            $row = $DB->query_first(sprintf(
                "SELECT %s AS `%s` FROM %s AS `m` WHERE `m`.`member_id` = %d LIMIT 1",
                $fields[$field],
                $field,
                MEMBER_TABLENAME,
                $member_id
            ));
            return $row[$field];
        }

        if ($row = memberFromDatabase($member_id)) {
            return $row[$field];
        }

        return null;
    }
}
if (!function_exists('memberGroupCount')) {
    function memberGroupCount($item_list, $group_by = '', $split_sets = true)
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
        $fields      = memberGetOptionList('all_fields');
        $field_types = memberGetOptionList('all_fields_types');
        $field_list  = array('row_count' => 'COUNT(*) AS `row_count`');
        foreach ($group_by as $field) {
            if (array_key_exists($field, $fields)) {
                $field_list[$field] = $fields[$field] . " AS `" . $field . "`";
            }
        }

        foreach (memberGetOptionList('joined_fields') as $join => $fields) {
            foreach ($fields as $field => $field_string) {
                if (in_array($field, $group_by)) {
                    $join_list[$join]            = $join;
                    $field_list[$field]          = $fields[$field] . " AS `" . $field . "`";
                    $field_types[$field][1]      = 'set(...)';
                    $field_types[$field]['Type'] = 'set(...)';
                }
            }
        }

        $group_list = $field_list;
        unset($group_list['row_count']);
        if (count($group_list) == 0) {
            return array(count($item_list));
        }

        $results = array();
        if ($query = $DB->query(sprintf(
            "SELECT %s FROM %s AS `m` %s WHERE %s GROUP BY `%s`",
            implode(', ', $field_list),
            MEMBER_TABLENAME,
            implode(' ', $join_list),
            memberSQLList($item_list),
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
if (!function_exists('memberHasValue')) {
    function memberHasValue($member_id, $field, $value, $split = "")
    {
        if ("" != $split) {
            $found        = true;
            $member_value = explode($split, memberGetValue($member_id, $field));
            if (is_string($value)) {
                $value = explode($split, $value);
            }

            foreach ($value as $findme) {
                if (!in_array($findme, $member_value)) {
                    $found = false;
                }
            }

            return $found;
        } else {
            return (memberGetValue($member_id, $field) == $value);
        }
    }
}
if (!function_exists('memberHGSignup')) {
    function memberHGSignup($posted)
    {
        global $DB;
        $signup                  = $posted['personal'];
        $signup['account_type']  = 'private';
        $signup['email_address'] = $posted['signup_1']['email_address'];
        if ('Name123' == $signup['first_name'] && 'Last123' == $signup['last_name']) {
            return true;
        }

        $accept_only = array('email_address', 'first_name', 'last_name', 'company_name', 'address', 'city', 'state', 'post_code', 'country', 'phone_no', 'phone_no2', 'fax_no', 'mobile_no', 'website', 'account_type');
        $set         = array();
        foreach ($signup as $key => $value) {
            if (in_array($key, $accept_only)) {
                $set[] = '`' . $key . '`=' . formatSQLString($value);
            }
        }

        $ip      = getClientIP();
        $country = getCountryInfo($ip);
        $flag    = ('' != $country['a2'] && $country['a2'] == $posted['country']) ? 'green' : 'red';
        $random  = mt_rand(1, 65535); // random number correspoding to the member.
        //now set additional data to be set now..
        $set[] = "date_joined=NOW(), site_id='1', signup_IP=" . formatSQLString($ip) . ", flag='" . $flag . "', status='active', rand_num=" . formatSQLString($random);
        $DB->query(sprintf(
            "INSERT INTO %s SET %s",
            MEMBER_TABLENAME,
            implode(", ", $set)
        ));
        $member_id = $DB->insert_id();

        //Add to the login table...
        $DB->query(sprintf(
            "INSERT INTO %s (`%s`, `email_address`, `password`, `active`) VALUES (%d, %s, %s, 'y')",
            "`highwaygroup`.`new_members_logins`",
            "member_id",
            $member_id,
            formatSQLString($posted['signup_1']['email_address']),
            formatSQLString(md5($posted['signup_1']['password1']))
        ));

        return $member_id;
    }
}
if (!function_exists('memberIdFromEmail')) {
    function memberIdFromEmail($email)
    {
        global $DB;

        if (empty($email)) {
            return 0;
        }

        if ($row = $DB->query_first(sprintf(
            "SELECT `%s` FROM %s WHERE `email_address` = %s LIMIT 1",
            MEMBER_PRIMARYKEY,
            MEMBER_TABLENAME,
            $DB->escape($email)
        ), MYSQLI_ASSOC)) {
            return $row[MEMBER_PRIMARYKEY];
        }

        return 0;
    }
}
if (!function_exists('memberLogin')) {
    function memberLogin($member_id, $rand_num = '', $track_login = true, $shop_cart = true, $site = 'cnv')
    {
        global $DB;

        $date          = strtotime('+1 year');
        $member_cookie = sprintf("%08x%08x", $member_id, $rand_num);
        custom_setcookie('member_id', $_COOKIE['member_id'] = $member_cookie, $date, '/');

        /************** Shopping Cart functions ***********************/
        if ($shop_cart) {
            //Merge the shopping carts
            $shop_cart_v1 = cartLoadContents(0, $_GLOBALS['visitor']['visitor_id'] ?? 0, 'shopping');
            $shop_cart_v2 = cartLoadContents(0, $_GLOBALS['visitor']['visitor_id'] ?? 0, 'quote');
            $shop_cart_m1 = cartLoadContents($member_id, 0, 'shopping');
            $shop_cart_m2 = cartLoadContents($member_id, 0, 'quote');

            $shop_cart_vout = cartEmptyContents();
            $shop_cart_mout = cartCombineContents(
                cartCombineContents($shop_cart_v1, $shop_cart_m1),
                cartCombineContents($shop_cart_v2, $shop_cart_m2)
            );

            cartSaveContents(0, $shop_cart_vout);
            cartSaveContents($member_id, $shop_cart_mout);
        }

        // update the login track..
        if ($track_login) {
            $brow = @get_browser(null, true);
            $ip   = getClientIP();
            $DB->query("INSERT INTO `highwaygroup`.`new_members_login_track` (`member_id`, `login_date`, `login_ip`, `site`, `browser`, `user_agent`) VALUES (" . $member_id . ", NOW(), '" . $ip . "', '" . $site . "', " . formatSQLString($brow['browser']) . ", " . formatSQLString($_SERVER['HTTP_USER_AGENT']) . ")");
        }
    }
}
if (!function_exists('memberLoginDetailsExists')) {
    function memberLoginDetailsExists($email)
    {
        global $DB;

        return $DB->query_first(sprintf(
            "SELECT * FROM `highwaygroup`.`new_members_logins` AS `m` WHERE `m`.`email_address` = %s LIMIT 1",
            formatSQLString($email)
        ), MYSQLI_ASSOC);
    }
}
if (!function_exists('memberLoginDetailsFromDatabase')) {
    function memberLoginDetailsFromDatabase($member_id, $site = '')
    {
        global $DB;

        $result = array(
            'total_logins' => 0,
            'last_login'   => 'Never',
            'status_hgr'   => 'n',
        );

        if ($row = $DB->query_first(sprintf(
            "SELECT COUNT(*) AS `total_logins`, MAX(`login_date`) AS `login_date` FROM `highwaygroup`.`new_members_login_track` WHERE `member_id` = %d%s LIMIT 1",
            $member_id,
            ('' != $site) ? sprintf(
                " AND `site` = %s",
                formatSQLString($site)
            ) : ''
        ), MYSQLI_ASSOC)) {
            $result['total_logins'] = $row['total_logins'];
            $result['last_login']   = date(_('d/m/Y', strtotime($row['last_login'])));
        }

        if ($row = $DB->query_first(sprintf(
            "SELECT `active` FROM `highwaygroup`.`new_members_logins` WHERE `member_id` = %d AND `active` = 'y' LIMIT 1",
            $member_id
        ), MYSQLI_ASSOC)) {
            $result['status_hgr'] = $row['active'];
        }

        return $result;
    }
}
if (!function_exists('memberLoginFromDatabase')) {
    function memberLoginFromDatabase($member_id, $email_address = '')
    {
        global $DB;
        $member_id = intval($member_id);
        if (!$member_id && '' == $email_address) {
            return false;
        }

        if ($member_id) {
            return $DB->query_first(sprintf(
                "SELECT * FROM `highwaygroup`.`new_members_logins` WHERE `member_id` = %d%s LIMIT 1",
                $member_id,
                ('' != $email_address) ? sprintf(
                    " AND `email_address` = %s",
                    formatSQLString($email_address)
                ) : ''
            ), MYSQLI_ASSOC);
        }

        return $DB->query_first(sprintf(
            "SELECT * FROM `highwaygroup`.`new_members_logins` WHERE `email_address` = %s LIMIT 1",
            formatSQLString($email_address)
        ), MYSQLI_ASSOC);
    }
}
if (!function_exists('memberLoginHistoryFromDatabase')) {
    function memberLoginHistoryFromDatabase($member_id, $site = '', $limit = 10)
    {
        global $DB;

        $out = array();
        if ($query = $DB->query(sprintf(
            "SELECT *, UNIX_TIMESTAMP(`login_date`) AS `login_time` FROM `highwaygroup`.`new_members_login_track` WHERE `member_id` = %d%s ORDER BY `login_date` DESC, `id` DESC%s",
            $member_id,
            ('' != $site) ? sprintf(
                " AND `site` = '%s'",
                $site
            ) : '',
            ($limit > 0) ? sprintf(
                " LIMIT 0, %d",
                $limit
            ) : ''
        ))) {
            while ($row = $DB->fetch_assoc($query)) {
                $out[] = $row;
            }
            $DB->free_result($query);
        }

        return $out;
    }
}
if (!function_exists('memberLoginsChunk')) {
    function memberLoginsChunk($logins)
    {
        if (!is_array($logins)) {
            return $logins;
        }

        if (!count($logins)) {
            return $logins;
        }

        $new_logins = array();
        foreach ($logins as $login) {
            $new_logins[$login['site']][]    = $login;
            $new_logins[$login['site_id']][] = $login;
        }

        return $new_logins;
    }
}
if (!function_exists('memberLoginsFromDatabase')) {
    function memberLoginsFromDatabase($id)
    {
        global $DB;

        $result = array();
        if ($query  = $DB->query(sprintf(
            "SELECT `member_id`, `email_address` FROM `highwaygroup`.`new_members_logins` WHERE `member_id` = %d AND `active` = 'y'",
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
if (!function_exists('memberLoginsToDatabase')) {
    function memberLoginsToDatabase($id, &$item)
    {
        $item['member_id'] = $id;
        memberGenericToDatabase($item, '`highwaygroup`.`new_members_logins`', 'member_id');
    }
}
if (!function_exists('memberLoginToDatabase')) {
    function memberLoginToDatabase($summary)
    {
        global $DB;

        /*
        if (!intval($summary['member_id']) || '' == $summary['email_address']) {
            return false;
        }

        //TODO: This is a temporary patch, fix it
        $DB->query(sprintf(
            "DELETE FROM `highwaygroup`.`new_members_logins` WHERE `member_id` = %d",
            $summary["member_id"]
        ));

        if ("" != $summary["email_address"] && "" != $summary["password"] && 0 != $summary["member_id"] && "y" == $summary["active"]) {
            $DB->query(sprintf(
                "INSERT INTO %s (`%s`, `email_address`, `password`, `active`) VALUES (%d, %s, %s, 'y')",
                "`highwaygroup`.`new_members_logins`",
                "member_id",
                $summary["member_id"],
                formatSQLString($summary["email_address"]),
                formatSQLString($summary["password"])
            ));
        }

        return true;
        */

        $exists = memberLoginFromDatabase($summary['member_id']);
        $active = (in_array($summary['active'], array('y', 'n'))) ? $summary['active'] : 'y';
        $DB->query((($exists) ? "UPDATE" : "INSERT INTO") . " `highwaygroup`.`new_members_logins` SET `email_address` = " . formatSQLString($summary['email_address']) . ", `password` = " . formatSQLString($summary['password']) . ", `active` = " . formatSQLString($active) . (($exists) ? ' WHERE' : ',') . " `member_id` = " . formatSQLString($summary['member_id']));
        return true;
    }
}
if (!function_exists('memberMailingListFromPost')) {
    function memberMailingListFromPost($posted)
    {
        $new_mailing = array();
        $member_id   = intval($posted['member_id']);
        if (!$member_id) {
            return $new_mailing;
        }

        $mailing = $posted['members_mailing'];
        if (!is_array($mailing)) {
            return $new_mailing;
        }

        $allowed       = array('active', 'style_id', 'comments');
        $copy_from_old = array('date_joined', 'date_cancelled');
        $old           = memberMailingListFromDatabase($member_id);
        foreach ($mailing as $template_id => $list) {
            $tmp = array('style_id' => $template_id);
            foreach ($list as $key => $value) {
                if (in_array($key, $allowed)) {
                    $tmp[$key] = $value;
                }
            }

            foreach ($copy_from_old as $field) {
                $tmp[$field] = $old[$template_id][$field];
            }

            if (('n' == $old[$template_id]['active'] || '' == $old[$template_id]['active']) && 'y' == $list['active']) {
                $tmp['date_joined']    = formatDateTime();
                $tmp['date_cancelled'] = '0000-00-00 00:00:00'; //reset the cancelled date..

            }
            if ('y' == $old[$template_id]['active'] && 'n' == $list['active']) {
                $tmp['date_cancelled'] = formatDateTime();
            }

            $new_mailing[] = $tmp;
        }
        return $new_mailing;
    }
}
if (!function_exists('memberMailingListSearch')) {
    /**
     * @param mixed $criteria
     *
     * @return int[]
     */
    function memberMailingListSearch($criteria = '')
    {
        global $DB;

        if (is_string($criteria) && '' != $criteria) {
            $criteria = array('any_text' => $criteria);
        }

        $field_types = getTableFields(MEMBER_MAILINGLIST_TABLENAME);
        $fields      = array();
        foreach ($field_types as $row) {
            $fields[$row[0]] = 'm.`' . $row[0] . '`';
        }

        //$fields=array_merge($fields,memberGetOptionList('ext_fields'));
        $whereAND = array();
        if (is_array($criteria)) {
            foreach ($criteria as $field => $value) {
                if (isset($fields[$field])) {
                    if (is_array($value)) {
                        if (count($value) > 0) {
                            $whereOR = array();
                            if (substr($field_types[$field]['Type'], 0, 4) == 'set(') {
                                foreach ($value as $sub_value) {
                                    $whereOR[] = "FIND_IN_SET(" . formatSQLString($sub_value) . "," . $fields[$field] . ")";
                                }
                            } else {
                                foreach ($value as $sub_value) {
                                    $whereOR[] = $fields[$field] . "=" . formatSQLString($sub_value);
                                }
                            }

                            $whereAND[] = "(" . implode(" OR ", $whereOR) . ")";
                        } else {
                            return array();
                        }

                    } else {
                        if (substr($field_types[$field]['Type'], 0, 4) == 'set(') {
                            $whereAND[] = "FIND_IN_SET(" . formatSQLString($value) . "," . $fields[$field] . ")";
                        } else {
                            $whereAND[] = $fields[$field] . "=" . formatSQLString($value);
                        }

                    }
                    unset($fields[$field]);
                }
            }
        }

        $search_for = array();
        if (is_array($criteria) && is_array($criteria['any_text'])) {
            $search_for = $criteria['any_text'];
        }

        if (is_array($criteria) && is_string($criteria['any_text'])) {
            $search_for = explode(' ', $criteria['any_text']);
        }

        if (count($search_for) > 0) {
            $searchBits = array('m.member_id', 'm.style_id');
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

        $results = array();
        if ($query   = $DB->query(sprintf(
            "SELECT `m`.`member_id` FROM `highwaygroup`.`new_members_mailing_list` AS `m`%s ORDER BY `m`.`member_id` ASC",
            (count($whereAND)) ? sprintf(
                " WHERE %s",
                implode(" AND ", $whereAND)
            ) : ''
        ))) {
            while ($row = $DB->fetch_assoc($query)) {
                $results[] = $row['member_id'];
            }
            $DB->free_result($query);
        }

        return $results;
    }
}
if (!function_exists('memberNewPrefsFromDatabase')) {
    function memberNewPrefsFromDatabase($member_id)
    {
        global $DB;

        $member_id = intval($member_id);
        if (!$member_id) {
            return;
        }

        if ($row = $DB->query_first(sprintf(
            "SELECT `reseller`, `best_described`, `currency`, `mailing_list` FROM %s WHERE `member_id` = %d LIMIT 1",
            MEMBER_TABLENAME,
            $member_id
        ), MYSQLI_ASSOC)) {
            return $row;
        }

        return null;
    }
}
if (!function_exists('memberPreferencesFromDatabase')) {
    function memberPreferencesFromDatabase($member_id)
    {
        global $DB;

        $member_id = intval($member_id);
        if (!$member_id) {
            return;
        }

        if ($row = $DB->query_first(sprintf(
            "SELECT `reseller`, `best_described`, `currency`, `mailing_list` FROM %s WHERE `member_id` = %d LIMIT 1",
            MEMBER_TABLENAME,
            $member_id
        ), MYSQLI_ASSOC)) {
            return $row;
        }

        return null;
    }
}
if (!function_exists('memberPrefsExist')) {
    function memberPrefsExist($site, $member_id)
    {
        global $DB;

        $array = array(
            'he'  => '`HE`.`members_prefs`',
            'cnv' => '`converters`.`members_prefs`',
            'car' => '`converters`.`car_members`',
        );

        return !!$DB->query_first(sprintf(
            "SELECT 1 FROM %s WHERE `member_id` = %d LIMIT 1",
            $array[$site],
            $member_id
        ), MYSQLI_ASSOC);
    }
}
if (!function_exists('memberPrefsFromDatabase')) {
    function memberPrefsFromDatabase($member_id, $flag = false)
    {
        global $DB;

        $row_cnv = $DB->fetch_assoc($DB->query("SELECT `currency`,`products_per_page` FROM `converters`.`members_prefs` WHERE `member_id` = '$member_id'"));
        $row_he  = $DB->fetch_assoc($DB->query("SELECT `described`,`interest`,`mailing`,`currency` AS `he_currency` FROM `HE`.`members_prefs` WHERE `member_id` = '$member_id'"));
        $row_car = $DB->fetch_assoc($DB->query("SELECT * FROM `converters`.`car_members` WHERE `member_id` = '$member_id'"));

        $row_car['status_hgr'] = 'n';
        if ($status = $DB->query_first(sprintf(
            "SELECT `active` FROM `highwaygroup`.`new_members_logins` WHERE `member_id` = %d LIMIT 1",
            $member_id
        ), MYSQLI_ASSOC)) {
            $row_car['status_hgr'] = $status['active'];
        }

        if ($flag) {
            $ret        = array();
            $ret['cnv'] = $row_cnv;
            $ret['he']  = $row_he;
            $ret['car'] = $row_car;
            return $ret;
        }
        if (!is_array($row_cnv)) {
            $row_cnv = array();
        }

        if (!is_array($row_he)) {
            $row_he = array();
        }

        if (!is_array($row_car)) {
            $row_car = array();
        }

        return (array_merge($row_cnv, $row_he, $row_car));
    }
}
if (!function_exists('memberPrefsToDatabase')) {
    function memberPrefsToDatabase($posted, $member_id)
    {
        global $DB;

        $array = array(
            'he'  => '`HE`.`members_prefs`',
            'cnv' => "`converters`.`members_prefs`",
            'car' => "`converters`.`car_members`",
        );
        foreach ($array as $tab => $table) {
            if ('on' == $posted['member_type'][$tab]) {
                // only updates/adds preferences if particular member type is selected..
                $sql = "INSERT INTO ";
                $add = true;
                if (memberPrefsExist($tab, $member_id)) {
                    $sql = "UPDATE ";
                    $add = false;
                }
                $sql .= $table . " ";
                $updates = array();
                if (is_array($posted[$tab])) {
                    foreach ($posted[$tab] as $pref => $value) {
                        if ('industry' != $pref) {
                            $updates[] = "$pref=" . formatSQLString($value);
                        }

                    }
                    if ('car' == $tab) {
                        $updates[] = "car_member='y'";
                    }

                    $sql .= "SET " . implode(", ", $updates);
                    $sql .= ($add) ? ", member_id='$member_id'" : " WHERE member_id='$member_id'";
                    $DB->query($sql);
                    //echo 'SQL = '.$sql.'<br />';

                }
            }
        }
        // now have to take care of the Special Pricing......The delete function..
        if ($posted['RemoveAction']) {
            $DB->query("DELETE FROM `converters`.`special_price` WHERE prod_id=" . formatSQLString($posted['RemoveID']) . " AND mem_id='$member_id'");
        }

        //now update the customer discount total

        //check the password
        if ('' != $posted['login']['password']) {
            if (!!$DB->query_first(sprintf(
                "SELECT 1 FROM `highwaygroup`.`new_members_logins` WHERE `member_id` = %d LIMIT 1",
                $member_id
            ), MYSQLI_ASSOC)) {
                $DB->query(sprintf(
                    "UPDATE `highwaygroup`.`new_members_logins` SET `password` = %s WHERE `member_id` = %d",
                    $DB->escape(md5($posted['login']['password'])),
                    $member_id
                ));
            } else {
                $DB->query(sprintf(
                    "INSERT INTO `highwaygroup`.`new_members_logins` (`member_id`, `password`) VALUES (%d, %s)",
                    $member_id,
                    $DB->escape(md5($posted['login']['password']))
                ));
            }
        }
    }
}
if (!function_exists('memberProductSearch')) {
    function memberProductSearch($criteria = '')
    {
        moduleLoad('invoice'); //invoiceGetOptionList

        global $DB;
        $results = array();
        if ('' == $criteria) {
            return $results;
        }

        if (is_string($criteria) && '' != $criteria) {
            $criteria = array('any_text' => $criteria);
        }

        $joins       = array();
        $field_types = getTableFields('`highwaygroup`.`invoices`');
        $fields      = array();
        foreach ($field_types as $row) {
            $fields[$row[0]] = 'i.`' . $row[0] . '`';
        }

        $fields   = array_merge($fields, invoiceGetOptionList('ext_fields'));
        $whereAND = array();
        if (is_array($criteria)) {
            foreach ($criteria as $field => $value) {
                if (isset($fields[$field])) {
                    if (is_array($value)) {
                        if (count($value) > 0) {
                            $whereOR = array();
                            if (substr($field_types[$field]['Type'], 0, 4) == 'set(') {
                                foreach ($value as $sub_value) {
                                    $whereOR[] = "FIND_IN_SET(" . formatSQLString($sub_value) . "," . $fields[$field] . ")";
                                }
                            } else {
                                foreach ($value as $sub_value) {
                                    $whereOR[] = $fields[$field] . "=" . formatSQLString($sub_value);
                                }
                            }

                            $whereAND[] = "(" . implode(" OR ", $whereOR) . ")";
                        } else {
                            return array();
                        }

                    } else {
                        if (substr($field_types[$field]['Type'], 0, 4) == 'set(') {
                            $whereAND[] = "FIND_IN_SET(" . formatSQLString($value) . "," . $fields[$field] . ")";
                        } else {
                            $whereAND[] = $fields[$field] . "=" . formatSQLString($value);
                        }

                    }
                    unset($fields[$field]);
                }
            }
        }

        $joins      = array(
            "LEFT JOIN `highwaygroup`.`invoices` AS `i` ON `ip`.`invoice_id` = `i`.`invoice_id`",
        );
        $search_for = array();
        if (is_array($criteria) && is_array($criteria['any_text'])) {
            $search_for = $criteria['any_text'];
        }

        if (is_array($criteria) && is_string($criteria['any_text'])) {
            $search_for = explode(' ', $criteria['any_text']);
        }

        if (count($search_for) > 0) {
            $searchBits = array('ip.product_id', 'ip.sub_id', 'ip.product_name', 'ip.product_notes');
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
        if ($query = $DB->query(sprintf(
            "SELECT DISTINCT(`i`.`member_id`) FROM `highwaygroup`.`invoice_products` AS `ip`%s%s ORDER BY `i`.`member_id` ASC",
            (count($joins)) ? sprintf(
                " %s",
                implode(" ", $joins)
            ) : '',
            (count($whereAND)) ? sprintf(
                " WHERE %s",
                implode(" AND ", $whereAND)
            ) : ''
        ))) {
            while ($row = $DB->fetch_assoc($query)) {
                $results[] = $row['member_id'];
            }
            $DB->free_result($query);
        }

        return $results;
    }
}
if (!function_exists('memberProductSpecialPrices')) {
    function memberProductSpecialPrices($member_id, $product_str, $site_id, $sub_id = 0)
    {
        moduleLoad('product'); //productStrSplit

        global $DB;

        $product_id    = 0;
        $product_split = productStrSplit($product_str, 2);
        extract($product_split);

        $result = array();
        if ($query = $DB->query(sprintf(
            "SELECT * FROM `highwaygroup`.`new_members_special_pricing` WHERE `member_id` = %d AND `site_id` = %d AND `product_id` = %d AND `sub_id` = %d ORDER BY `min_order` ASC",
            $member_id,
            $site_id,
            $product_id,
            $sub_id
        ))) {
            while ($row = $DB->fetch_assoc($query)) {
                $result[] = $row;
            }
            $DB->free_result($query);
        }

        return $result;
    }
}
if (!function_exists('memberQuoteDetailsFromDatabase')) {
    function memberQuoteDetailsFromDatabase($member_id, $ret = false)
    {
        moduleLoad('invoice'); //invoiceFromDatabase

        $quote_details                        = array();
        $open_quotes                          = oldQuoteSearch('', 'open', $member_id);
        $not_quotes                           = oldQuoteSearch('', 'not_cancelled', $member_id);
        $quote_details['open']                = $open_quotes;
        $quote_details['not']                 = $not_quotes;
        $quote_details['total_orders']        = count($not_quotes);
        $quote_details['purchase_total']      = ordersTotal($not_quotes);
        $quote_details['outstanding_payment'] = ordersTotal($open_quotes);
        $max                                  = max((count($not_quotes) > 0 ? max($not_quotes) : 0), (count($open_quotes) > 0 ? max($open_quotes) : 0));
        $m_details                            = invoiceFromDatabase($max);
        //print_array($m_details);
        $quote_details['last_order'] = ('0000-00-00 00:00:00' != $m_details['date_modified']) ? date(_('jS F, Y'), strtotime($m_details['date_modified'])) : 'Never';
        if ($ret) {
            $quote_details['last_order'] = ('0000-00-00 00:00:00' != $m_details['date_modified']) ? date(_('d/m/Y'), strtotime($m_details['date_modified'])) : 'Never';
        }

        return $quote_details;
    }
}
if (!function_exists('memberQuotesFromDatabase')) {
    function memberQuotesFromDatabase($member_id)
    {
        moduleLoad('invoice'); //invoiceSearch

        return invoiceSearch(array('member_id' => $member_id));
        //return oldQuoteSearch('','all',$member_id,'DESC');

    }
}
if (!function_exists('memberRequestDelete')) {
    function memberRequestDelete($request_id)
    {
        global $DB;
        $request_id = intval($request_id);
        if (!$request_id) {
            return false;
        }

        if ($DB->query("DELETE FROM `highwaygroup`.`new_members_requests` WHERE `id` = $request_id")) {
            return true;
        }

        return false;
    }
}
if (!function_exists('memberRequestsFromDatabase')) {
    function memberRequestsFromDatabase($member_id)
    {
        global $DB;

        $results = array();
        if ($query = $DB->query(sprintf(
            "SELECT *, UNIX_TIMESTAMP(`date`) AS `comment_time` FROM `highwaygroup`.`new_members_requests` WHERE `member_id` = %d ORDER BY `date` DESC, `id` DESC",
            $member_id
        ))) {
            while ($row = $DB->fetch_assoc($query)) {
                $results[] = $row;
            }
            $DB->free_result($query);
        }

        return $results;
    }
}
if (!function_exists('memberRequestsToDatabase')) {
    function memberRequestsToDatabase($member_id, $warning)
    {
        global $DB;
        $DB->query("INSERT INTO `highwaygroup`.`new_members_requests` (member_id, staff, date, comments) VALUES (" . intval($member_id) . ", " . formatSQLString($_SERVER['REMOTE_USER']) . ", NOW(), " . formatSQLString($warning) . ")");
        return $DB->insert_id();
    }
}
if (!function_exists('memberSearch')) {
    function memberSearch($criteria = '')
    {
        global $DB;

        $results    = array();
        $fields     = getTableFields(MEMBER_TABLENAME);
        $ext_fields = memberGetOptionList('extra_fields');
        $whereAND   = array();
        if (is_array($criteria)) {
            foreach ($criteria as $field => $value) {
                if (array_key_exists($field, $fields)) {
                    if (is_array($value)) {
                        if (count($value) > 0) {
                            $whereOR = array();
                            if (substr($fields[$field][1], 0, 4) == 'set(') {
                                foreach ($value as $sub_value) {
                                    if ("" == $sub_value) {
                                        $whereOR[] = "`m`.`" . $field . "` = ''";
                                    } else {
                                        $whereOR[] = "FIND_IN_SET(" . formatSQLString($sub_value) . ", `m`.`" . $field . "`)";
                                    }
                                }

                            } else {
                                foreach ($value as $sub_value) {
                                    $whereOR[] = "`m`.`" . $field . "` = " . formatSQLString($sub_value);
                                }

                            }
                            $whereAND[] = "(" . implode(" OR ", $whereOR) . ")";
                        } else {
                            return array();
                        }

                    } else {
                        if (substr($fields[$field][1], 0, 4) == 'set(' && "" != $value) {
                            $whereAND[] = "FIND_IN_SET(" . formatSQLString($value) . ", `m`.`" . $field . "`)";
                        } else {
                            $whereAND[] = "`m`.`" . $field . "` = " . formatSQLString($value);
                        }

                    }
                    unset($fields[$field]);
                } elseif (isset($ext_fields[$field])) {
                    if (is_array($value)) {
                        if (count($value) > 0) {
                            $whereOR = array();
                            foreach ($value as $sub_value) {
                                $whereOR[] = $ext_fields[$field] . " = " . formatSQLString($sub_value);
                            }

                            $whereAND[] = "(" . implode(" OR ", $whereOR) . ")";
                        } else {
                            return array();
                        }

                    } else {
                        $whereAND[] = $ext_fields[$field] . " = " . formatSQLString($value);
                    }

                    unset($ext_fields[$field]);
                }
            }
        } elseif (is_string($criteria) && '' != $criteria) {
            $criteria = array('any_text' => $criteria);
        } else {
            $criteria = array();
        }

        $search_for = array();
        if (is_array($criteria) && array_key_exists("any_text", $criteria) && is_array($criteria['any_text'])) {
            $search_for = $criteria['any_text'];
        }

        if (is_array($criteria) && array_key_exists("any_text", $criteria) && is_string($criteria['any_text'])) {
            $search_for = explode(' ', $criteria['any_text']);
        }

        if (count($search_for) > 0) {
            $searchBits = array('`m`.`member_id`', '`m`.`company_name`', '`m`.`first_name`', '`m`.`last_name`', '`m`.`nick_name`', '`m`.`country`', "`m`.`phone_no`", "`m`.`phone_no2`", "`m`.`fax_no`", "`m`.`mobile_no`", '`m`.`website`', '`m`.`email_address`', '`m`.`site_id`');
            foreach ($search_for as $findme) {
                if (trim($findme) != '') {
                    $whereAND[] = sprintf(
                        "(`m`.`member_id` LIKE %s OR `m`.`site_id` LIKE %s OR MATCH(`company_name`, `first_name`, `last_name`, `nick_name`, `country`, `phone_no`, `phone_no2`, `fax_no`, `mobile_no`, `website`, `email_address`) AGAINST (%s IN BOOLEAN MODE) > 0)",
                        formatSQLLikeString($findme),
                        formatSQLLikeString($findme),
                        formatSQLString('"' . $findme . '"')
                    );
                    /*
                $whereOR = array();
                foreach ($searchBits as $key) {
                $whereOR[] = $key . " LIKE " . formatSQLLikeString($findme);
                }
                if (count($whereOR) > 0) {
                $whereAND[] = "(" . implode(" OR ", $whereOR) . ")";
                }
                 */
                }
            }

        }

        if ($query = $DB->query(sprintf(
            "SELECT `m`.`member_id` FROM %s AS `m`%s GROUP BY `m`.`member_id` ORDER BY `m`.`member_id` ASC",
            MEMBER_TABLENAME,
            (count($whereAND)) ? sprintf(
                " WHERE %s",
                implode(" AND ", $whereAND)
            ) : ''
        ))) {
            while ($row = $DB->fetch_assoc($query)) {
                $results[] = $row["member_id"];
            }
            $DB->free_result($query);
        }

        return $results;
    }
}



if (!defined('MEMBER_SERIAL_TABLENAME')) {
    define('MEMBER_SERIAL_TABLENAME', "`highwaygroup`.`serial_numbers`");
}
if (!defined('MEMBER_SERIAL_PRIMARYKEY')) {
    define('MEMBER_SERIAL_PRIMARYKEY', "id");
}
if (!defined('MEMBER_SERIAL_SECONDARYKEY')) {
    define('MEMBER_SERIAL_SECONDARYKEY', "member_id");
}

if (!function_exists('memberSerialsFromDatabase')) {
    function memberSerialsFromDatabase($id)
    {
        global $DB;

        $result = array();
        if ($query = $DB->query(sprintf(
            "SELECT * FROM %s WHERE `%s` = %d ORDER BY `serial_id` DESC",
            MEMBER_SERIAL_TABLENAME,
            MEMBER_SERIAL_SECONDARYKEY,
            $id
        ))) {
            while ($row = $DB->fetch_assoc($query)) {
                $result[$row["id"]] = $row;
            }
            $DB->free_result($query);
        }

        return $result;
    }
}



if (!function_exists('memberSetLock')) {
    function memberSetLock($id, $staff_id = 0, $timeout = 300)
    {
        global $DB;
        $section_name = formatSQLString("member");
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
if (!function_exists('memberSetValue')) {
    function memberSetValue($member_id, $field, $value)
    {
        global $DB;
        $fields = memberGetOptionList('base_fields');
        if (array_key_exists($field, $fields)) {
            $DB->query(sprintf(
                "UPDATE %s SET `%s` = %s WHERE `%s` = %d LIMIT 1",
                MEMBER_TABLENAME,
                $field,
                formatSQLString($value),
                MEMBER_PRIMARYKEY,
                $member_id
            ));
            return true;
        }
        return false;
    }
}
if (!function_exists('memberShippingAddressesFromPost')) {
    function memberShippingAddressesFromPost($posted)
    {
        $member_id = intval($posted['member_id']);
        $affected  = explode('|', $posted['shipping_affected']);
        $table     = $posted['shipping'];
        $current   = array();
        if (memberExists($member_id)) {
            $current = memberShippingAddressesFromDatabase($member_id);
        }

        foreach ($affected as $key) {
            foreach ($current as $key2 => $shipping) {
                if ($shipping['id'] == $key) {
                    unset($current[$key2]);
                }
            }
        }

        if (is_array($table)) {
            foreach ($table as $shipping) {
                $shipping['member_id'] = $member_id;
                foreach (getTableFields('`highwaygroup`.`new_members_shipping`') as $row) {
                    if (!isset($shipping[$row[0]])) {
                        $shipping[$row[0]] = $row[4];
                    }
                }

                $current[] = $shipping;
            }
        }

        return $current;
    }
}
if (!function_exists('memberShippingAddressesToDatabase')) {
    function memberShippingAddressesToDatabase($member_id, $current)
    {
        global $DB;
        $member_id = intval($member_id);
        if (0 == $member_id) {
            return;
        }

        $DB->query("DELETE FROM `highwaygroup`.`new_members_shipping` WHERE member_id=" . $member_id);
        $fields = getTableFields('`highwaygroup`.`new_members_shipping`');
        foreach ($current as $shipping) {
            $insert_vals           = array();
            $shipping['member_id'] = $member_id;
            if (!array_key_exists('id', $shipping) || 0 == $shipping['id']) {
                unset($shipping['id']);
            }

            foreach ($shipping as $key => $value) {
                if (isset($fields[$key])) {
                    $insert_vals[$key] = formatSQLString($value);
                }
            }

            if (count($insert_vals)) {
                $DB->query("INSERT INTO `highwaygroup`.`new_members_shipping`(`" . implode('`, `', array_keys($insert_vals)) . "`) VALUES(" . implode(", ", $insert_vals) . ")");
            }

        }
    }
}
if (!function_exists('memberSignup')) {
    function memberSignup($posted, $site)
    {
        global $DB;
        $match = array('cnv' => 9, 'he' => 2, 'car' => 16, 'hg' => 1);
        if (array_key_exists($site, $match)) {
            $signup                 = $posted['personal'];
            $signup['account_type'] = 'private';
            if (in_array('company', $posted['account_type'])) {
                $signup                 = $posted['company'];
                $signup['first_name']   = $posted['contacts'][0]['first_name'];
                $signup['last_name']    = $posted['contacts'][0]['last_name'];
                $signup['account_type'] = 'company';
            }

            // get all the IP / Country information
            $ip      = getClientIP();
            $country = getCountryInfo($ip);
            $pass    = md5($posted['signup_1']['password1']);

            $DB->query(sprintf(
                "INSERT INTO %s (`company_name`, `first_name`, `last_name`, `address`, `city`, `state`, `post_code`, `country`, `phone_no`, `phone_no2`, `fax_no`, `mobile_no`, `website`, `email_address`, `status`, `date_joined`, `site_id`, `signup_IP`, `flag`, `account_type`, `mailing_list`, `best_described`, `currency`, `products_per_page`, `news_per_page`) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, 'active', NOW(), %d, %s, '%s', %s, %s, %s, %s, %d, %d)",
                MEMBER_TABLENAME,
                formatSQLString($signup['company_name']),
                formatSQLString($signup['first_name']),
                formatSQLString($signup['last_name']),
                formatSQLString($signup['address']),
                formatSQLString($signup['city']),
                formatSQLString($signup['state']),
                formatSQLString($signup['post_code']),
                formatSQLString($signup['country']),
                formatSQLString($signup['phone_no']),
                formatSQLString($signup['phone_no2']),
                formatSQLString($signup['fax_no']),
                formatSQLString($signup['mobile_no']),
                formatSQLString($signup['website']),
                formatSQLString($posted['signup_1']['email_address']),
                $match[$site],
                formatSQLString($ip),
                ('' != $country['a2'] && $country['a2'] == $posted['country']) ? 'green' : 'red',
                formatSQLString($signup['account_type']),
                formatSQLString($posted['member']['mailing_list']),
                formatSQLString($posted['member']['best_described']),
                formatSQLString($posted['member']['currency']),
                $posted['member']['products_per_page'],
                $posted['member']['news_per_page']
            ));
            $member_id = $DB->insert_id();

            // now add to old HE mailing list database if selected as yes...
            if ('y' == $posted['member']['mailing_list']) {
                $DB->query(sprintf(
                    "INSERT INTO `HE`.`HE_mailinglist` (`id`, `email`, `name`, `date_added`, `ip`) VALUES (%d, %s, %s, NOW(), %s)",
                    $member_id,
                    formatSQLString($signup['email_address']),
                    formatSQLString($signup['first_name'] . ' ' . $signup['last_name']),
                    formatSQLString($ip)
                ));
            }

            //Add to the login table...
            $DB->query(sprintf(
                "INSERT INTO `highwaygroup`.`new_members_logins` (`member_id`, `email_address`, `password`, `active`) VALUES (%d, %s, %s, 'y')",
                $member_id,
                formatSQLString($posted['signup_1']['email_address']),
                formatSQLString(md5($posted['signup_1']['password1']))
            ));

            //now have to add additional contacts..
            foreach ($posted['contacts'] as $contact) {
                $contact['name'] = $contact['first_name'] . ' ' . $contact['last_name'];
                if ('' != $contact['name']) {
                    $DB->query(sprintf(
                        "INSERT INTO `highwaygroup`.`new_members_contacts` (`member_id`, `name`, `title`, `email`) VALUES (%d, %s, %s, %s)",
                        $member_id,
                        formatSQLString($contact['name']),
                        formatSQLString($contact['title']),
                        formatSQLString($contact['email'])
                    ));

                    // now have to create a login for the contact if the email address doesn't already exist.
                    $row = $DB->query_first(sprintf(
                        "SELECT * FROM `highwaygroup`.`new_members_logins` WHERE `email_address` = %s LIMIT 1",
                        $DB->escape($contact['email'])
                    ), MYSQLI_ASSOC);
                    if (!$row) {
                        $DB->query(sprintf(
                            "INSERT INTO `highwaygroup`.`new_members_logins` (`member_id`, `email_address`, `password`, `active`) VALUES (%d, %s, %s, 'y')",
                            $member_id,
                            formatSQLString($contact['email']),
                            formatSQLString($pass)
                        ));
                    }
                }
            }

            // now the shipping details..
            // firstly get all the shipping addresses entered...
            $copy_shipping    = ('y' == $posted['checkout']['copy_shipping']);
            $shipping_only    = array('company_name', 'address', 'city', 'state', 'post_code', 'country', 'phone_no', 'phone_no2', 'fax_no', 'mobile_no');
            $shipping_address = ($copy_shipping) ? $signup : $posted['shipping'];
            $sql              = "INSERT INTO `highwaygroup`.`new_members_shipping`";
            $set              = array();
            foreach ($shipping_only as $field) {
                if (isset($shipping_address[$field])) {
                    $set[] = $field . '=' . formatSQLString($shipping_address[$field]);
                }

            }
            $set[] = 'contact=' . formatSQLString($signup['first_name'] . ' ' . $signup['last_name']);
            $set[] = 'email=' . formatSQLString($signup['email_address']);
            if ('' != $posted['ship']['instructions']) {
                $set[] = 'instructions=' . formatSQLString($posted['ship']['instructions']);
            }

            $sql .= " SET " . implode(', ', $set) . ", member_id=" . intval($member_id);
            //echo $sql.'<hr />';
            $DB->query($sql);
            // now the couriers...
            if (is_array($posted['signup_2']['ship_account'])) {
                $checked_own = (in_array('own', $posted['signup_2']['ship_account']));
            }

            $checked_he   = !$checked_own;
            $courier_name = (-1 != $posted['ship']['courier']) ? getFreightServiceName($posted['ship']['courier']) : $posted['ship']['courier_name'];
            $courier_id   = (-1 != $posted['ship']['courier']) ? $posted['ship']['courier'] : 0;
            $account_num  = $posted['ship']['account_num'];
            $notes        = $posted['ship']['instructions'];
            if ($checked_own) {
                $DB->query("INSERT INTO `highwaygroup`.`new_members_couriers` (`member_id`, `courier_id`, `courier_name`, `account_num`, `notes`) VALUES (" . intval($member_id) . ", " . $courier_id . ", " . formatSQLString($courier_name) . ", " . formatSQLString($account_num) . ", " . formatSQLString($notes) . ")");
                if (0 == $courier_id && '' != $courier_name) {
                    moduleLoad('phpmailer');
                    $email           = new PHPMailer();
                    $email->From     = 'sales@' . str_replace('www.', '', $_SERVER['HTTP_HOST']);
                    $email->Sender   = $email->From;
                    $email->FromName = 'Automatic Updates';
                    $email->Mailer   = "sendmail";
                    $email->IsHTML(true);
                    $email->CharSet = "utf-8";
                    $email->Subject = 'New Freight Courier Added by a Customer';
                    $email->AddAddress('sales@highway.net.au');
                    ob_start();
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8" />
        <style type="text/css">
/*<![CDATA[*/
*{font-family:Verdana;font-size:11px;color:#717171}
/*]]>*/
        </style>
    </head>
    <body>
        Hi,<br />
        <br />
        This is just an alert email notifying you that a new Courier has been added to the database by a customer.<br />
        <br />
        <b>Courier Name : </b><?=$courier_name?>, <b>Added By : </b><?=$signup['first_name'] . ' ' . $signup['last_name']?> ( <a href="<?=htmlspecialchars(relative_url('/webadmin/member/?action=edit&member_id=' . $member_id))?>">View Member Details</a> )<br />
        <br />
        Regards,<br />
        Auto Alert System.
    </body>
</html>
<?php
                    $email->Body = ob_get_clean();
                    $email->Send();
                }
            }
            return $member_id;
        }
        return false;
    }
}
if (!function_exists('memberSignupEmailExists')) {
    function memberSignupEmailExists($email, $site = '')
    {
        global $DB;

        $exists = memberEmailExists($email, $site);
        if (!$exists) {
            return $DB->query_first(sprintf(
                "SELECT * FROM `highwaygroup`.`new_members_logins` WHERE `email_address` = %s LIMIT 1",
                $DB->escape($email)
            ), MYSQLI_ASSOC);
        }

        return false;
    }
}
if (!function_exists('memberSiteLoginDetailsFromDatabase')) {
    function memberSiteLoginDetailsFromDatabase($member_id, $site)
    {
        moduleLoad('invoice'); //invoiceFromDatabase

        global $DB;

        $login = memberLoginDetailsFromDatabase($member_id, $site);

        $login['date_joined'] = '&nbsp;';
        if ($row = $DB->query_first(sprintf(
            "SELECT `date_joined` FROM `highwaygroup`.`new_members_date_joined` WHERE `member_id` = %d AND `site` = %s LIMIT 1",
            $member_id,
            $DB->escape(strval($site))
        ), MYSQLI_ASSOC)) {
            $login['date_joined'] = ($row['date_joined']) ? date(_("d/m/Y"), strtotime($row['date_joined'])) : '&nbsp;';
        }

        if ('cnv' == $site || $site = 'car') {
            $not_quotes            = oldQuoteSearch('', 'not_cancelled', $member_id);
            $login['total_orders'] = count($not_quotes);
            $max                   = (count($not_quotes) > 0) ? max($not_quotes) : 0;
            $quote                 = invoiceFromDatabase($max);
            $login['last_order']   = ('0000-00-00 00:00:00' != $quote['date_modified']) ? date(_("d/m/Y"), strtotime($quote['date_modified'])) : 'Never';
        }

        return $login;
    }
}
if (!function_exists('memberSort')) {
    function memberSort($member_list, $sort_by = 'member_id')
    {
        global $DB;
        if (!is_array($member_list) || count($member_list) < 2) {
            return $member_list;
        }

        if ('total_logins' == $sort_by) {
            $results = array();
            foreach ($member_list as $k) {
                $results[$k] = $k;
            }

            if ($query = $DB->query(sprintf(
                "SELECT `member_id` FROM `highwaygroup`.`new_members_login_track` AS `m` WHERE %s GROUP BY `member_id` ORDER BY COUNT(`id`) ASC",
                memberSQLList($member_list)
            ))) {
                while ($row = $DB->fetch_assoc($query)) {
                    unset($results[$row['member_id']]);
                    $results[$row['member_id']] = $row['member_id'];
                }
                $DB->free_result($query);
            }

            return $results;
        } elseif ('total_orders' == $sort_by) {
            $results = array();
            foreach ($member_list as $k) {
                $results[$k] = $k;
            }

            if ($query = $DB->query(sprintf(
                "SELECT `i`.`member_id` FROM `highwaygroup`.`invoices` AS `i` WHERE `i`.`member_id` IN(%s) AND `i`.`invoice_status` IN('sales', 'proforma', 'processed') GROUP BY `i`.`member_id` ORDER BY COUNT(`i`.`invoice_id`) ASC",
                implode(', ', $member_list)
            ))) {
                while ($row = $DB->fetch_assoc($query)) {
                    unset($results[$row['member_id']]);
                    $results[$row['member_id']] = $row['member_id'];
                }
                $DB->free_result($query);
            }

            return $results;
        } elseif ('processed_count' == $sort_by) {
            $results = array();
            foreach ($member_list as $k) {
                $results[$k] = $k;
            }

            if ($query = $DB->query(sprintf(
                "SELECT `i`.`member_id` FROM `highwaygroup`.`invoices` AS `i` WHERE `i`.`member_id` IN(%s) AND `i`.`invoice_status` = 'processed' GROUP BY `i`.`member_id` ORDER BY COUNT(`i`.`invoice_id`) ASC",
                implode(',', $member_list)
            ))) {
                while ($row = $DB->fetch_assoc($query)) {
                    unset($results[$row['member_id']]);
                    $results[$row['member_id']] = $row['member_id'];
                }
                $DB->free_result($query);
            }

            return $results;
        } elseif ('processed_total' == $sort_by) {
            $results = array();
            foreach ($member_list as $k) {
                $results[$k] = $k;
            }

            if ($query = $DB->query(sprintf(
                "SELECT `i`.`member_id` FROM `highwaygroup`.`invoices` AS `i` WHERE `i`.`member_id` IN(%s) AND `i`.`invoice_status` = 'processed' GROUP BY `i`.`member_id` ORDER BY SUM(`i`.`invoice_offerau`) ASC",
                implode(', ', $member_list)
            ))) {
                while ($row = $DB->fetch_assoc($query)) {
                    unset($results[$row['member_id']]);
                    $results[$row['member_id']] = $row['member_id'];
                }
                $DB->free_result($query);
            }

            return $results;
        } else {
            $fields = memberGetOptionList('all_fields');
            if (!is_array($sort_by)) {
                $sort_by = array($sort_by);
            }

            $order = array();
            foreach ($sort_by as $field) {
                if (isset($fields[$field])) {
                    $order[] = $fields[$field] . " ASC";
                }
            }

            if (count($order) == 0) {
                return $member_list;
            }

            $result = array();
            if ($query  = $DB->query(sprintf(
                "SELECT `m`.`member_id` FROM %s AS `m` WHERE `m`.`member_id` IN('%s') ORDER BY %s",
                MEMBER_TABLENAME,
                implode("', '", $member_list),
                implode(', ', $order)
            ))) {
                while ($row = $DB->fetch_assoc($query)) {
                    $result[] = $row['member_id'];
                }
                $DB->free_result($query);
            }

            return $result;
        }
    }
}
if (!function_exists('memberSpecialPriceFromDatabase')) {
    function memberSpecialPriceFromDatabase($member_id, $site_id = 0)
    {
        global $DB;

        $result = array();
        if ($query = $DB->query(sprintf(
            "SELECT * FROM %s WHERE `%s` = %d%s",
            "`highwaygroup`.`new_members_special_pricing`",
            "member_id",
            $member_id,
            ($site_id) ? sprintf(
                " AND `site_id` = %d",
                $site_id
            ) : ""
        ))) {
            while ($row = $DB->fetch_assoc($query)) {
                $result[$row['site_id']][$row['id']] = $row;
            }
            $DB->free_result($query);
        }

        return $result;
    }
}
if (!function_exists('memberSpecialsFromDatabase')) {
    function memberSpecialsFromDatabase($member_id, $site_id = 0)
    {
        global $DB;
        $member_id = intval($member_id);
        $site_id   = intval($site_id);
        if (!$member_id) {
            return false;
        }

        $special_pricing = array();
        if ($query = $DB->query(sprintf(
            "SELECT * FROM `highwaygroup`.`new_members_special_pricing` WHERE `member_id` = %d%s",
            $member_id,
            ($site_id) ? sprintf(
                " AND `site_id` = %d",
                $site_id
            ) : ''
        ))) {
            while ($row = $DB->fetch_assoc($query)) {
                $special_pricing[$row['site_id']][$row['id']] = $row;
            }
            $DB->free_result($query);
        }

        return $special_pricing;
    }
}
if (!function_exists('memberSQLList')) {
    function memberSQLList($member_list)
    {
        if (count($member_list) == 0) {
            return "1";
        }

        $whereOR = array();
        $whereIN = array();
        foreach (list_to_partitions($member_list) as $k => $v) {
            if ($k > $v + 2) {
                $whereOR[] = "(`m`.`member_id` >= " . $v . " AND `m`.`member_id` <= " . $k . ")";
            } else {
                foreach (range($v, $k) as $k2) {
                    $whereIN[] = $k2;
                }
            }

        }
        if (count($whereIN) > 1) {
            $whereOR[] = "`m`.`member_id` IN(" . implode(", ", $whereIN) . ")";
        } elseif (count($whereIN) == 1) {
            $whereOR[] = "`m`.`member_id` = " . implode(" OR `m`.`member_id` = ", $whereIN);
        }

        return implode(" OR ", $whereOR);
    }
}
if (!function_exists('memberSurveyFromDatabase')) {
    function memberSurveyFromDatabase($member_id)
    {
        global $DB;

        $data  = array();
        $query = $DB->query(sprintf(
            "SELECT * FROM `highwaygroup`.`new_members_survey` WHERE `member_id` = %d",
            $member_id
        ));
        while ($row = $DB->fetch_assoc($query)) {
            $data[] = $row;
        }
        return $data;
    }
}
if (!function_exists('memberSurveyFromPost')) {
    function memberSurveyFromPost($posted)
    {
        $data = $posted['survey'];
        return $data;
    }
}
if (!function_exists('memberSurveyToDatabase')) {
    function memberSurveyToDatabase($member_id, $link)
    {
        global $DB;

        $member_id = intval($member_id);

        if ("" != $link) {
            $DB->query(sprintf("INSERT INTO `highwaygroup`.`new_members_survey` (`member_id`, `link`) VALUES (%d, %s)", $member_id, formatSQLString($link)));
        }
    }
}
if (!function_exists('memberTemplateVars')) {
    function memberTemplateVars($member = array())
    {
        global $DB, $_GLOBALS;

        $fields = array(
            'address_2',
            'address',
            'city',
            'company_code',
            'company_invoiceheader',
            'company_name',
            'company_signature',
            'company',
            'country',
            'email_address',
            'fax_no',
            'first_name',
            'full_name',
            'id',
            'last_initial',
            'last_name',
            'mobile_no',
            'phone_no',
            'post_code',
            'short_name',
            'staff_name',
            'state',
            'url',
            'vat_number',
            'website',
        );

        $company_info = $DB->query_first(sprintf(
            "SELECT * FROM `highwaygroup`.`company_info` WHERE `company_id` = %d LIMIT 1",
            $_GLOBALS['site_settings']['site_id']
        ), MYSQLI_ASSOC);

        $website = $DB->query_first(sprintf(
            "SELECT * FROM `highwaygroup`.`websites` AS `w` WHERE `w`.`id` = %d LIMIT 1",
            $_GLOBALS['site_settings']['site_id']
        ), MYSQLI_ASSOC);

        foreach ($fields as $k) {
            $temp[$k] = '';
            if (is_array($company_info) && array_key_exists($k, $company_info)) {
                $temp[$k] = $company_info[$k];
            } elseif (is_array($website) && array_key_exists($k, $website)) {
                $temp[$k] = $website[$k];
            } elseif (is_array($member) && array_key_exists($k, $member)) {
                $v           = $member[$k];
                $option_list = memberGetOptionList($k);
                $temp[$k]    = (is_array($option_list) && array_key_exists($v, $option_list)) ? $option_list[$v] : $v;
            } else {
                $temp[$k] = '';
            }
        }

        $temp['request_date'] = date(_('jS F, Y'), time());

        $result = array();
        foreach ($temp as $k => $v) {
            $result['[' . strtoupper($k) . ']']      = $v;
            $result['[' . strtoupper($k) . '_TXT]']  = $v;
            $result['[' . strtoupper($k) . '_HTML]'] = formatHTMLDescription($v);
        }
        return $result;
    }
}
if (!function_exists('memberToDatabase')) {
    function memberToDatabase($summary)
    {
        global $DB;

        $member_id = (array_key_exists('member_id', $summary)) ? intval($summary['member_id']) : 0;
        $fields    = memberGetOptionList('base_fields_types');
        if (memberExists($member_id)) {
            $set_vals = array();
            foreach ($summary as $key => $value) {
                if (array_key_exists($key, $fields)) {
                    if (is_array($summary[$key]) && substr($fields[$key][1], 0, 4) == 'set(') {
                        $set_vals[$key] = "`" . $key . "`=" . formatSQLString(implode(",", $summary[$key]));
                    } elseif (isset($summary[$key])) {
                        $set_vals[$key] = "`" . $key . "`=" . formatSQLString($summary[$key]);
                    } else {
                        $set_vals[$key] = "`" . $key . "`=NULL";
                    }

                }
            }

            if (count($set_vals)) {
                $sql = sprintf(
                    "UPDATE %s SET %s WHERE `member_id` = %d LIMIT 1",
                    MEMBER_TABLENAME,
                    implode(', ', $set_vals),
                    $member_id
                );
                $DB->query($sql);
                memberMessageToDatabase($member_id, $sql);
                if($summary['mailing_list'] == 'y'){
                    memberMailingListSubscribe($member_id,12);
                }else{
                    memberMailingListUnsubscribe($member_id,12);
                }
            }
        } else {
            $insert_vals = array();
            foreach ($summary as $key => $value) {
                if (array_key_exists($key, $fields)) {
                    if (is_array($summary[$key]) && substr($fields[$key][1], 0, 4) == 'set(') {
                        $insert_vals[$key] = formatSQLString(implode(",", $summary[$key]));
                    } elseif (isset($summary[$key])) {
                        $insert_vals[$key] = formatSQLString($value);
                    } else {
                        $insert_vals[$key] = 'NULL';
                    }

                }
            }

            if (count($insert_vals)) {
                if (!isset($insert_vals['date_joined'])) {
                    $insert_vals['date_joined'] = "NOW()";
                }

                if (!isset($insert_vals['rand_num'])) {
                    $insert_vals['rand_num'] = mt_rand(1, 65535);
                }

                $DB->query(sprintf(
                    "INSERT INTO %s (`%s`) VALUES (%s)",
                    MEMBER_TABLENAME,
                    implode('`, `', array_keys($insert_vals)),
                    implode(", ", array_values($insert_vals))
                ));
                $member_id = $DB->insert_id();
                memberMessageToDatabase($member_id, 'New Member Added');
                if($summary['mailing_list'] == 'y'){
                    memberMailingListSubscribe($member_id,12);
                }else{
                    memberMailingListUnsubscribe($member_id,12);
                }
            }
        }
        deleteMysqlCacheData('new_members', $member_id);
        return $member_id;
    }
}
if (!function_exists('memberTotalComments')) {
    function memberTotalComments($member_id)
    {
        global $DB;
        return count(memberCommentsFromDatabase($member_id, true));
    }
}
if (!function_exists('memberUpdateFromForm')) {
    function memberUpdateFromForm($posted, $member_id)
    {
        global $DB;
        $member_id   = intval($member_id);
        $member      = $posted['member'];
        $shipping    = $posted['shipping'];
        $old_pass    = $posted['old_password'];
        $member_only = array('first_name', 'last_name', 'company_name', 'address', 'city', 'state', 'post_code', 'country', 'phone_no', 'phone_no2', 'fax_no', 'mobile_no', 'website', 'mailing_list', 'best_described', 'currency', 'reseller', 'mailing_products', 'products_per_page', 'news_per_page');
        $ship_only   = array('company_name', 'address', 'city', 'state', 'post_code', 'country', 'phone_no', 'fax_no', 'mobile_no');
        $sql         = "UPDATE " . MEMBER_TABLENAME;
        $set         = array();
        foreach ($member as $key => $value) {
            if (in_array($key, $member_only)) {
                $set[] = $key . "=" . formatSQLString($value);
            }

        }
        if (count($set) > 0) {
            $sql .= " SET " . implode(', ', $set) . " WHERE member_id=$member_id";
        }

        //echo $sql."<hr />";
        $DB->query($sql);
        // Update the Shipping details now
        $shipping = $posted['shipping'];
        if (is_array($shipping)) {
            foreach ($shipping as $ship_id => $ship_data) {
                $add     = ('add' == $ship_data['add']) ? true : false;
                $sql     = (($add) ? "INSERT INTO " : "UPDATE ") . " `highwaygroup`.`new_members_shipping`";
                $allowed = array('company_name', 'address', 'city', 'state', 'post_code', 'country', 'phone_no', 'phone_no2', 'mobile_no', 'fax_no', 'contact', 'email');
                $set     = array();
                foreach ($ship_data as $key => $value) {
                    if (in_array($key, $allowed)) {
                        $set[] = $key . '=' . formatSQLString($value);
                    }

                }
                if (count($set)) {
                    $sql .= " SET " . implode(', ', $set) . (($add) ? ", member_id=$member_id" : " WHERE id=$ship_id");
                }

                $DB->query($sql);
            }
        }
        $msg = 'success';

        // Update the password, if new password entered..
        if ('' != $old_pass) {
            if ($row = $DB->query_first(sprintf(
                "SELECT * FROM `highwaygroup`.`new_members_logins` WHERE `member_id` = %d LIMIT 1",
                $member_id
            ), MYSQLI_ASSOC)) {
                $pass = $row['password'];
            }
            if (md5($old_pass) == $pass) {
                memberUpdatePassword($posted['password1'], $member_id);
                $msg = 'update_pass';
            } else {
                $msg = 'error_pass';
            }
        }

        // Now update the couriers.
        $couriers   = $posted['courier'];
        $ship_notes = $posted['shipping_notes'];
        if ('Please enter special shipping instructions here.' == $ship_notes) {
            $ship_notes = '';
        }

        $member_shippingaddresses = memberShippingAddressesFromDatabase($member_id);
        foreach ($member_shippingaddresses as $id => $member_shippingaddress) {
            $DB->query("UPDATE `highwaygroup`.`new_members_shipping` SET `instructions` = " . formatSQLString($ship_notes) . " WHERE `id` = " . intval($member_shippingaddress['id']));
        }

        $allowed = array('courier_id', 'courier_name', 'account_num', 'notes');
        //print_array($couriers);
        if (is_array($couriers)) {
            foreach ($couriers as $id => $array) {
                $add = ($id <= 0) ? true : false;
                $sql = (($add) ? "INSERT INTO" : "UPDATE") . " `highwaygroup`.`new_members_couriers`";
                $set = array();
                foreach ($array as $key => $value) {
                    if (in_array($key, $allowed)) {
                        $set[] = $key . '=' . formatSQLString($value);
                    }
                }

                if ($add) {
                    $set[] = 'member_id=' . $member_id;
                }

                if (count($set)) {
                    $sql .= " SET " . implode(', ', $set);
                    if (!$add) {
                        $sql .= " WHERE id=" . $id;
                    }

                    $DB->query($sql);
                }
                // now have to check if a new courier is added...
                if ($add && 0 == $array['courier_id'] && '' != $array['courier_name']) {
                    moduleLoad('phpmailer');
                    $email           = new PHPMailer();
                    $email->From     = 'sales@' . str_replace('www.', '', $_SERVER['HTTP_HOST']);
                    $email->Sender   = $email->From;
                    $email->FromName = 'Automatic Updates';
                    $email->Mailer   = "sendmail";
                    $email->IsHTML(true);
                    $email->CharSet = "utf-8";
                    $email->Subject = 'New Freight Courier Added by a Customer';
                    $email->AddAddress('sales@highway.net.au');
                    ob_start();
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8" />
        <style type="text/css">
/*<![CDATA[*/
*{font-family:Verdana;font-size:11px;color:#717171}
/*]]>*/
        </style>
    </head>
    <body>
        Hi,<br />
        <br />
        This is just an alert email notifying you that a new Courier has been added to the database by a customer.<br />
        <br />
        <b>Courier Name : </b><?=$array['courier_name']?>, <b>Added By : </b><?=$member['first_name'] . ' ' . $member['last_name']?> ( <a href="<?=htmlspecialchars(relative_url('/webadmin/member/?action=edit&member_id=' . $member_id))?>">View Member Details</a> )<br />
        <br />
        Regards,<br />
        Auto Alert System.
    </body>
</html>
<?php
                    $email->Body = ob_get_clean();
                    $email->Send();
                }
            }
        }

        // Now update the contacts
        $contacts = $posted['contacts'];
        if (is_array($contacts) && count($contacts)) {
            foreach ($contacts as $id => $contact) {
                if ('' != $contact['first_name']) {
                    $contact['name'] = $contact['first_name'] . ' ' . $contact['last_name'];
                    $add             = ('y' == $contact['add']) ? true : false;
                    if ('' == $contact['send_email_cc']) {
                        $contact['send_email_cc'] = 'n';
                    }

                    $sql         = (($add) ? "INSERT INTO" : "UPDATE") . " `highwaygroup`.`new_members_contacts`";
                    $accept_only = array('name', 'title', 'phone', 'email', 'main', 'send_email_cc');
                    $set         = array();
                    foreach ($contact as $key => $value) {
                        if (in_array($key, $accept_only)) {
                            $set[] = $key . '=' . formatSQLString($value);
                        }

                    }
                    if (count($set)) {
                        $sql .= " SET " . implode(', ', $set) . (($add) ? ", member_id=" . $member_id : " WHERE id=" . $id);
                        $DB->query($sql);
                    }
                }
            }
        }
        // now update the preferences..
        $prefs = $posted['preference'];
        if (is_array($prefs)) {
            foreach ($prefs as $key => $value) {
                memberUpdatePreferences($member_id, $key, $value);
            }

        }
        return $msg;
    }
}
if (!function_exists('memberUpdatePassword')) {
    function memberUpdatePassword($new_pass, $member_id)
    {
        global $DB;
        $member_id = intval($member_id);
        if (!$member_id) {
            return;
        }

        $email_address = memberGetValue($member_id, "email_address");

        if ($DB->query_first(sprintf(
            "SELECT `member_id` FROM `highwaygroup`.`new_members_logins` WHERE `member_id` = %d AND `email_address` = %s LIMIT 1",
            $member_id,
            $DB->escape($email_address)
        ), MYSQLI_ASSOC)) {
            $DB->query(sprintf(
                "UPDATE %s SET `password` = %s WHERE `%s` = %d AND `email_address` = %s LIMIT 1",
                "`highwaygroup`.`new_members_logins`",
                $DB->escape(md5($new_pass)),
                "member_id",
                $member_id,
                $DB->escape($email_address)
            ));
        } else {
            $DB->query(sprintf(
                "INSERT INTO %s (`%s`, `email_address`, `password`) VALUES (%d, %s, %s)",
                "`highwaygroup`.`new_members_logins`",
                "member_id",
                $member_id,
                $DB->escape($email_address),
                $DB->escape(md5($new_pass))
            ));
        }
    }
}
if (!function_exists('memberUpdatePreferences')) {
    function memberUpdatePreferences($member_id, $field, $newValue)
    {
        global $DB;
        $member_id = intval($member_id);
        if (!$member_id) {
            return false;
        }

        $row = $DB->query_first(sprintf(
            "SELECT `member_id` FROM `highwaygroup`.`new_members_preferences` WHERE `member_id` = %d LIMIT 1",
            $member_id
        ), MYSQLI_ASSOC);
        $add = true;
        if ($row) {
            $add = false;
        }

        $allowed_fields = array('mailing_list');
        if (in_array($field, $allowed_fields)) {
            $set = array();
            if ($add) {
                $set[] = 'member_id=' . $member_id;
            }

            $set[] = "`$field`=" . formatSQLString($newValue);
            $sql   = (($add) ? "INSERT INTO" : "UPDATE") . " `highwaygroup`.`new_members_preferences` SET " . implode(', ', $set);
            if (!$add) {
                $sql .= " WHERE member_id=$member_id";
            }

            $DB->query($sql);
        } elseif (in_array($field, array('products_per_page', 'news_per_page', 'currency'))) {
            $DB->query("UPDATE `highwaygroup`.`new_members` SET " . $field . "=" . formatSQLString($newValue) . " WHERE member_id=$member_id");
        }

    }
}
if (!function_exists('memberValidate')) {
    function memberValidate($email, $password, $site = '', $site_id = '')
    {
        global $DB;
        if ("" == $email) {
            return false;
        }

        $row = $DB->query_first(sprintf(
            "SELECT `ml`.`member_id`, `m`.`rand_num` FROM `highwaygroup`.`new_members_logins` AS `ml` LEFT JOIN %s AS `m` ON `ml`.`member_id` = `m`.`member_id` WHERE `ml`.`active` = 'y' AND `ml`.`email_address` = %s AND `ml`.`password` = %s%s LIMIT 1",
            MEMBER_TABLENAME,
            formatSQLString($email),
            formatSQLString(md5($password)),
            ('' != $site_id) ? sprintf(
                " AND FIND_IN_SET(%s, `m`.`site_id`)",
                formatSQLString($site_id)
            ) : ''
        ), MYSQLI_ASSOC);
        if ($row['member_id'] > 0) {
            return $row;
        }
        return false;
    }
}
if (!function_exists('memberValidateBySeed')) {
    function memberValidateBySeed($member_id, $rand_num = '')
    {
        global $DB;

        if ($row = $DB->query_first(sprintf(
            "SELECT `rand_num` FROM %s WHERE `member_id` = %d LIMIT 1",
            MEMBER_TABLENAME,
            $member_id
        ), MYSQLI_ASSOC)) {
            return (trim($row['rand_num']) == trim($rand_num));
        }

        return false;
    }
}
if (!function_exists('memberVirtual')) {
    function memberVirtual($row, $invoice = false)
    {
        moduleLoad('invoice'); //invoiceHistoryFromDatabase, invoiceHistorySearch, invoiceSearch, invoiceChunk, invoiceSort, invoiceGetValue, invoiceSumValue

        if (array_key_exists(MEMBER_PRIMARYKEY, $row)) {
            $row['id'] = $row['member_id'] = $row[MEMBER_PRIMARYKEY];
        }

        $row['first_name'] = $row['name']    = $row['first_name'] ?? $row['name'] ?? null;
        $row['last_name']  = $row['surname'] = $row['last_name'] ?? $row['surname'] ?? null;

        $row['address'] = $row['address'] ?? $row['address_1'];

        $row['company_name'] = $row['company'] = $row['company_name'] ?? $row['company'] ?? null;

        $row['post_code'] = $row['zip'] = $row['post_code'] ?? $row['zip'] ?? null;

        $row['phone_no']  = $row['phone_number'] = $row['phone_no'] ?? $row['phone_number'] ?? null;
        $row['fax_no']    = $row['fax_number'] = $row['fax_no'] ?? $row['fax_number'] ?? null;
        $row['mobile_no'] = $row['mobile_number'] = $row['mobile_no'] ?? $row['mobile_number'] ?? null;

        $row['website'] = $row['web_page_url'] = $row['website'] ?? $row['web_page_url'] ?? null;

        $row['created_at'] = $row['created'] = $row['created_at'] ?? $row['created'] ?? formatDateTime();
        $row['updated_at'] = $row['modified'] = $row['updated_at'] ?? $row['modified'] ?? null;

        //Remaining coinopex: secondary_email, third_email, fouth_email, business, order_requirements
        //Remaining hwgroup: forum_user_id, timezone, abn_number, news_notification, spammer, timewaster, sales_related, tester, xbox_status, xbox_qty, hwgaming_products, last_contacted, date_joined, mailing_id, discount_code, products_order, timestamp, business_interests, general_interests, created_by, updated_by, deleted_at, _site_1, _site_2, _site_9, _site_11, _site_16, _site_20, _last_initial, organization_title

        $row['full_name'] = trim(sprintf(
            '%s %s',
            (array_key_exists('first_name', $row)) ? $row['first_name'] : '',
            (array_key_exists('last_name', $row)) ? $row['last_name'] : ''
        ));

        $row['address_full'] = memberFormatAddress($row);

        if ($invoice) {
            $invoice_list   = invoiceSearch(array('member_id' => $row['member_id']));
            $invoice_chunks = invoiceChunk($invoice_list, 'invoice_status');

            foreach ($invoice_chunks as $k => $v) {
                $invoice_chunks[$k] = array_reverse(invoiceSort($v, 'proforma_date'));
            }

            foreach (array('processed', 'sales', 'proforma', 'inquiry', 'closed', 'cancelled') as $k) {
                if (!is_array($invoice_chunks[$k])) {
                    $invoice_chunks[$k] = array();
                }
            }

            $row['all_invoices']    = count($invoice_list);
            $row['total_inquiries'] = count($invoice_chunks['inquiry']) + count($invoice_chunks['closed']);
            $row['total_invoices']  = count($invoice_chunks['processed']);
            $row['total_orders']    = count($invoice_chunks['proforma']) + count($invoice_chunks['sales'] + $invoice_chunks['processed']);

            $temp                      = invoiceGetValue(reset($invoice_chunks['processed']), 'proforma_time');
            $row['last_order_time'] = ((0 != $temp) ? date(_('d/m/Y'), $temp) : 'Never');

            $temp                    = max($temp, invoiceGetValue(reset($invoice_chunks['inquiry']), 'proforma_time'));
            $temp                    = max($temp, invoiceGetValue(reset($invoice_chunks['closed']), 'proforma_time'));
            $row['last_inq_time'] = ((0 != $temp) ? date(_('d/m/Y'), $temp) : 'Never');

            $temp                    = invoiceSumValue($invoice_chunks['processed'], 'invoice_offerau');
            $row['order_total']   = '$' . convertCurrency($temp, 'AUD', 'USD') . ' USD';
            $row['order_totalau'] = convertCurrency($temp, 'AUD');

            $temp                          = invoiceSumValue(array_merge($invoice_chunks['proforma'], $invoice_chunks['sales']), 'invoice_offerau');
            $row['outstanding_payment'] = '$' . convertCurrency($temp, 'AUD', 'USD') . ' USD';
            $temp += invoiceSumValue($invoice_chunks['processed'], 'invoice_offerau');
            $row['invoice_total'] = convertCurrency($temp, 'AUD');

            $last_message                  = invoiceHistoryFromDatabase(reset(invoiceHistorySearch(array('member_id' => $row['member_id'], 'message_type_id' => 2))));
            $row['last_contacted']      = $last_message['message_date'];
            $row['time_last_contacted'] = $last_message['message_time'];
        }

        return $row;
    }
}
if (!function_exists('memberWarningsDelete')) {
    function memberWarningsDelete($warning_id)
    {
        global $DB;
        $warning_id = intval($warning_id);
        if (!$warning_id) {
            return false;
        }

        return $DB->query("DELETE FROM `highwaygroup`.`new_members_warnings` WHERE `id` = " . $warning_id);
    }
}
if (!function_exists('memberWarningsFromDatabase')) {
    function memberWarningsFromDatabase($member_id)
    {
        global $DB;

        $results = array();
        if ($query = $DB->query(sprintf(
            "SELECT *, UNIX_TIMESTAMP(`date`) AS `comment_time` FROM `highwaygroup`.`new_members_warnings` WHERE `member_id` = %d",
            $member_id
        ))) {
            while ($row = $DB->fetch_assoc($query)) {
                $results[] = $row;
            }
            $DB->free_result($query);
        }

        return $results;
    }
}
if (!function_exists('memberWarningsToDatabase')) {
    function memberWarningsToDatabase($member_id, $warning)
    {
        global $DB;
        $DB->query("INSERT INTO `highwaygroup`.`new_members_warnings` (member_id, staff, date, comments) VALUES (" . intval($member_id) . ", " . formatSQLString($_SERVER['REMOTE_USER']) . ", NOW(), " . formatSQLString($warning) . ")");
        return $DB->insert_id();
    }
}



if (!function_exists('productSerialNoDelete')) {
    /**
     * @todo Does not belong here, named incorrectly
     *
     * @param int $id
     *
     * @return void
     */
    function productSerialNoDelete($id)
    {
        global $DB;
        $DB->query(sprintf(
            "UPDATE  `highwaygroup`.`serial_numbers` set `member_id`=0,`invoice_id`=0  WHERE `id` = %d LIMIT 1",
            $id
        ));
    }
}
if (!function_exists('productSerialNoFromPost')) {
    /**
     * @todo Does not belong here, named incorrectly
     *
     * @param mixed[] $posted
     *
     * @return mixed[]
     */
    function productSerialNoFromPost($posted)
    {
        global $DB;
        if (!is_array($posted)) {
            return array();
        }
        $result = array();
        $fields = $DB->get_table_columns('`highwaygroup`.`serial_numbers`');

        foreach ($posted as $k1 => $v1) {
            if (array_key_exists($k1, $fields)) {
                if ($v1 || 0 == $v1) {
                    $result[$k1] = $v1;
                }
            }
        }
        return $result;
    }
}
if (!function_exists('productSerialNoToDatabase')) {
    /**
     * @todo Does not belong here, named incorrectly
     *
     * @param mixed[] $item
     *
     * @return int
     */
    function productSerialNoToDatabase(&$item)
    {
        return memberGenericToDatabase($item, '`highwaygroup`.`serial_numbers`');
    }
}

include_once __DIR__ . '/query_member_online.inc.php';
