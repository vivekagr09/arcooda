<?php
if (!function_exists("manufacturerNewsFromDatabase")) {
    function manufacturerNewsFromDatabase($man_id)
    {
        global $DB;
        if (!$man_id) {
            return array();
        }

        $man_list = array();
        /*
        $res = $DB->query("SELECT * FROM `news_manufacturers` WHERE `manufacturer_id` = " . intval($man_id));
        while($row = $DB->fetch_assoc($res)) $man_list[$row['id']] = $row;
        $DB->free_result($res);
         */
        return $man_list;
    }
}
if (!function_exists("resetSuppliers")) {
    function resetSuppliers()
    {
        global $DB;
        $count = 1;
        $sql   = "";
        $res   = $DB->query("SELECT `id` FROM `suppliers` ORDER BY `id` ASC");
        while ($row = $DB->fetch_assoc($res)) {
            $sql .= "UPDATE `suppliers` SET `id` = " . $count . " WHERE `id` = " . $row['id'] . ";";
            $count++;
        }
        $DB->free_result($res);
        $DB->query($sql);
    }
}
if (!function_exists("supplierActivate")) {
    function supplierActivate($supplier_id)
    {
        global $DB;
        $DB->query("UPDATE `suppliers` SET `active` = 'y' WHERE `id` = " . intval($supplier_id) . " LIMIT 1");
    }
}
if (!function_exists("supplierAlphaList")) {
    function supplierAlphaList($alpha_value = '', $page_url, $extra = '', $type = '', $site_id = 0)
    {
        global $DB;
        if ('' != $type) {
            $extra .= (('' != $extra) ? " AND " : '') . "`s`.`type` IN ('" . implode("', '", explode('|', $type)) . "')";
        }

        echo '<table class="wide" style="width:98%;"><tr><td bgcolor=#FAFAFA style="text-align:center;width:83%"><b>By Supplier Name :</b> ';
        for ($i = A;; $i++) {
            $res = $DB->query("SELECT * FROM `suppliers` AS `s` WHERE `s`.`supplier` LIKE '$i%'" . (('' != $extra) ? " AND " . $extra : ''));
            if (mysqli_num_rows($res) > 0) {
                if ($i == $alpha_value) {
                    echo '<span style="background-color:#7FFFD4;">' . $i . '</span>&nbsp;';
                } else {
                    echo '<a href="' . $page_url . '&amp;by=alpha&amp;alpha_value=' . $i . '">' . $i . '</a>&nbsp;';
                }

            } else {
                echo '<font color="#999999">' . $i . '</font>&nbsp;';
            }

            if ("Z" == $i) {
                break;
            }

        }
        echo '</td></tr></table>';
        $DB->free_result();
    }
}
if (!function_exists("supplierAttachmentsFromDatabase")) {
    function supplierAttachmentsFromDatabase($supplier_id)
    {
        global $DB;
        $result = array();
        /*
        $query = $DB->query("SELECT `id` FROM `supplier_attachments` WHERE `supplier_id` = " . intval($supplier_id));
        while($row = $DB->fetch_assoc($query)) $result[] = $row['id'];
        $DB->free_result($query);
         */
        return $result;
    }
}
if (!function_exists("supplierBanksFromDatabase")) {
    function supplierBanksFromDatabase($supplier_id, $count = false)
    {
        global $DB;

        if ($count) {
            /*
            if ($row = $DB->query_first(sprintf(
                "SELECT COUNT(*) AS `total` FROM `supplier_banks` WHERE `supplier_id` = %d LIMIT 1",
                $supplier_id
            ), MYSQLI_ASSOC)) {
                return $row['total'];
            }
            */
            return 0;
        }

        $result = array();
        /*
        $query = $DB->query("SELECT * FROM `supplier_banks` WHERE `supplier_id` = " . intval($supplier_id));
        while($row = $DB->fetch_assoc($query)) $result[] = $row;
        $DB->free_result($query);
         */
        return $result;
    }
}
if (!function_exists("supplierChangesFromDatabase")) {
    function supplierChangesFromDatabase($supplier_id, $type = 'admin')
    {
        global $DB;
        $types = array('admin', 'email');
        if (!in_array($type, $types)) {
            $type = 'admin';
        }

        $result = array();
        /*
        $query = $DB->query("SELECT *, UNIX_TIMESTAMP(`date`) AS `change_time` FROM `supplier_changes` WHERE `supplier_id` = " . intval($supplier_id) . " AND `type` = '" . $type . "' ORDER BY `date` ASC");
        while($row = $DB->fetch_assoc($query)) $result[] = $row;
        $DB->free_result($query);
         */
        return $result;
    }
}
if (!function_exists("supplierChangesToDatabase")) {
    function supplierChangesToDatabase($summary)
    {
        /*
        global $DB;

        $fields = array();
        $query = $DB->query("SHOW COLUMNS FROM `supplier_changes`");
        while($row = $DB->fetch_array($query, MYSQLI_NUM)) $fields[$row[0]] = $row;
        $DB->free_result($query);

        $set = array();
        foreach($summary as $k => $v) if (array_key_exists($k, $fields)) $set["`" . $k . "`"] = formatSQLString($v);
        $set["`date`"] = "NOW()";
        $DB->query("INSERT INTO `supplier_changes` (" . implode(", ", array_keys($set)) . ") VALUES (" . implode(", ", array_values($set)) . ")");
        return $DB->insert_id();
         */
        return false;
    }
}
if (!function_exists("supplierChunk")) {
    function supplierChunk($supplier_list, $group_by = '')
    {
        global $DB;
        if (!is_array($supplier_list) || count($supplier_list) < 1) {
            return $supplier_list;
        }

        if (!is_array($group_by)) {
            $group_by = array($group_by);
        }

        $fields      = supplierGetOptionList('all_fields');
        $field_types = supplierGetOptionList('all_fields_types');
        $field_list  = array('id');
        foreach ($group_by as $field) {
            if (array_key_exists($field, $fields)) {
                $field_list[] = $fields[$field] . " AS " . $field;
            }
        }

        $field_list = array_unique($field_list);
        $sql        = "SELECT " . implode(', ', $field_list) . " FROM `suppliers` AS `s` WHERE `s`.`id` IN(" . implode(', ', $supplier_list) . ")";
        $query      = $DB->query($sql);
        $results    = array();
        while ($row = $DB->fetch_assoc($query)) {
            $pointers = array(&$results);
            foreach ($group_by as $group) {
                $key = $row[$group];
                if ('' == $key) {
                    $key = null;
                }

                $new_pointers = array();
                foreach ((substr($field_types[$group]['Type'], 0, 4) == 'set(') ? explode(',', $key) : array($key) as $key2) {
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
                $pointers[$pointer_key][] = $row['id'];
            }

        }
        $DB->free_result($query);
        return $results;
    }
}
if (!function_exists("supplierCommentsFromDatabase")) {
    function supplierCommentsFromDatabase($supplier_id)
    {
        global $DB;
        $result = array();
        /*
        $query = $DB->query("SELECT * FROM `supplier_comments` WHERE `supplier_id` = " . intval($supplier_id) . " ORDER BY `date` DESC, `id` DESC");
        while($row = $DB->fetch_assoc($query)) $result[] = $row;
        $DB->free_result($query);
         */
        return $result;
    }
}
if (!function_exists("supplierContactsFromDatabase")) {
    function supplierContactsFromDatabase($supplier_id, $main = false)
    {
        global $DB;
        $result = array();
        /*
        $query = $DB->query("SELECT * FROM `supplier_contacts` WHERE `supplier_id` = " . intval($supplier_id) . (($main) ? " AND `main` = 'y'" : ''));
        while($row = $DB->fetch_assoc($query)) $result[] = $row;
        $DB->free_result($query);
        if (count($result) == 1) $result[0]['main'] = 'y';
         */
        return $result;
    }
}
if (!function_exists("supplierCountries")) {
    function supplierCountries($site_id = 0, $type = '')
    {
        global $DB;

        $country_list = getCountryList(false);

        $whereAND = array();
        if (0 != $site_id) {
            $whereAND[] = "FIND_IN_SET('" . intval($site_id) . "', `s`.`site`) > 0";
        }

        if ('' != $type) {
            $whereAND[] = "`s`.`type` IN('" . implode("', '", explode('|', $type)) . "')";
        }

        $extra = (count($whereAND)) ? " WHERE " . implode(" AND ", $whereAND) : '';

        $result = array();
        if ($query  = $DB->query(sprintf(
            "SELECT COUNT(*) AS `total`, `s`.`country` AS `code` FROM `suppliers` AS `s`%s GROUP BY `s`.`country`",
            $extra
        ))) {
            while ($row = $DB->fetch_assoc($query)) {
                $row['name'] = $country_list[$row['code']] ?? $row['code'];
                $result[]    = $row;
            }
            $DB->free_result($query);
        }

        return $result;
    }
}
if (!function_exists("supplierCreateAlphaList")) {
    function supplierCreateAlphaList($supplier_list = array())
    {
        global $DB;
        if (!count($supplier_list)) {
            return array();
        }

        $alpha_list = array();
        $query      = $DB->query("SELECT DISTINCT LEFT(`supplier`, 1) AS `alpha_value` FROM `suppliers` WHERE `id` IN('" . implode("', '", $supplier_list) . "')");
        while ($row = $DB->fetch_assoc($query)) {
            $alpha_list[] = $row['alpha_value'];
        }

        $DB->free_result($query);

        return $alpha_list;
    }
}
if (!function_exists("supplierCreateSubAlphaList")) {
    function supplierCreateSubAlphaList($supplier_list = array(), $alpha_name)
    {
        global $DB;
        if (!count($supplier_list)) {
            return array();
        }

        $extra = '';
        if ('0' == $alpha_name) {
            $extra = "`supplier` REGEXP('^[^a-zA-Z]')";
        } else {
            $extra = "`supplier` LIKE '" . str_replace('%', '\%', str_replace('_', '\_', addslashes($alpha_name))) . "%'";
        }

        $alpha_list = array();
        $query      = $DB->query("SELECT DISTINCT(LEFT(`supplier`, 2)) AS `alpha_value` FROM `suppliers` WHERE `id` IN('" . implode("', '", $supplier_list) . "')" . (('' != $extra) ? ' AND ' . $extra : '') . " ORDER BY `supplier` ASC");
        while ($row = $DB->fetch_assoc($query)) {
            $alpha_list[] = strtolower($row['alpha_value']);
        }

        $DB->free_result($query);

        return $alpha_list;
    }
}
if (!function_exists("supplierDeactivate")) {
    function supplierDeactivate($supplier_id)
    {
        global $DB;
        $DB->query("UPDATE `suppliers` SET `active` = 'n' WHERE `id` = " . intval($supplier_id) . " LIMIT 1");
    }
}
if (!function_exists("supplierDeleteContact")) {
    function supplierDeleteContact($contact_id)
    {
        /*
    global $DB;
    $DB->query("DELETE FROM `supplier_contacts` WHERE `id` = " . intval($contact_id) . " LIMIT 1");
     */
    }
}
if (!function_exists("supplierExists")) {
    function supplierExists($supplier_id)
    {
        global $DB;
        $row = $DB->query_first("SELECT `id` FROM `suppliers` WHERE `id` = " . intval($supplier_id) . " LIMIT 1", MYSQLI_ASSOC);
        if ($row) {
            return true;
        }

        return false;
    }
}
if (!function_exists("supplierFind")) {
    function supplierFind($name, $email = '')
    {
        global $DB;
        if ('' == $name) {
            return false;
        }

        $row = $DB->query_first("SELECT `id` FROM `suppliers` WHERE `supplier` = " . formatSQLString($name) . (('' != $email) ? " OR `email` = " . formatSQLString($email) : '') . " LIMIT 1", MYSQLI_ASSOC);
        if ($row) {
            return true;
        }

        return false;
    }
}
if (!function_exists("supplierFormatAddress")) {
    function supplierFormatAddress($supplier)
    {
        $address = array();
        if (isset($supplier['supplier_name'])) {
            $address[] = $supplier['supplier_name'];
        }

        if (isset($supplier['address'])) {
            $address[] = $supplier['address'];
        }

        $addline = '';
        if (isset($supplier['city'])) {
            $addline .= $supplier['city'] . ', ';
        }

        if (isset($supplier['state'])) {
            $addline .= $supplier['state'] . ' ';
        }

        if (isset($supplier['post_code'])) {
            $addline .= $supplier['post_code'];
        }

        if ('' != $addline) {
            $address[] = $addline;
        }

        $address[] = $supplier['name'];
        if (isset($supplier['phone']) && '' != $supplier['phone']) {
            $address[] = "\nPh: " . $supplier['phone'];
        }

        return implode("\n", $address);
    }
}
if (!function_exists("supplierFormatNumber")) {
    function supplierFormatNumber($supplier_id)
    {
        return sprintf('SU#%06d', $supplier_id);
    }
}
if (!function_exists("supplierFromDatabase")) {
    function supplierFromDatabase($supplier_id, $formatAddress = false)
    {
        global $DB;
        if (!$supplier_id) {
            return array();
        }

        $fields = array('*' => '`s`.*', 'country_full' => '`c`.`name` AS `country_full`');
        foreach (supplierGetOptionList('ext_fields') as $k => $v) {
            $fields[$k] = $v . " AS `" . $k . "`";
        }

        $supplier = $DB->query_first("SELECT " . implode(", ", $fields) . " FROM `suppliers` AS `s` LEFT JOIN `world`.`country` AS `c` ON `s`.`country` = `c`.`code` WHERE s.id=" . intval($supplier_id) . " LIMIT 1", MYSQLI_ASSOC);

        if ($supplier) {
            foreach ($supplier as $k => $v) {
                $supplier[$k] = htmlspecialchars(stripslashes($v));
            }

            if ($formatAddress) {
                unset($supplier['address']);
                $supplier['address'] = supplierFormatAddress($supplier);
            }
            if ('' == $supplier['url_keys']) {
                $count = 0;
                $words = array();
                foreach (explode(' ', $supplier['supplier']) as $k => $v) {
                    if ($count > 2) {
                        break;
                    }

                    if (strlen($v) >= 3 && !is_numeric($v)) {
                        $words[] = $v;
                        $count++;
                    }
                }
                $supplier['url_keys_formatted'] = implode(' ', $words);
                $supplier['url_keys']           = formatURLSafe($supplier['url_keys_formatted']);
            }
            $url_keys                 = $supplier['url_keys'];
            $supplier['supplier_url'] = '/manufacturers/' . $url_keys . '/' . $supplier_id . '.html';
            if (is_file($_SERVER['DOCUMENT_ROOT'] . '/webadmin/assets/suppliers/' . $supplier['image'])) {
                $supplier['image_url'] = '/manufacturers/images/' . $url_keys . '/' . $supplier_id . '.' . $supplier['filetype'];
                $supplier['thumb_url'] = '/manufacturers/images/' . $url_keys . '/' . $supplier_id . '_sml.' . $supplier['filetype'];
            }
            $supplier['short_desc'] = str_replace("\n\n", "\n", $supplier['short_desc']);
            $supplier['long_desc']  = str_replace("\n\n", "\n", $supplier['long_desc']);
            //$supplier['open_orders'] = supplierOrders($supplier['supplier_name'], 'open');
            //$supplier['total_orders'] = supplierOrders($supplier['supplier_name'], 'all');
            //$supplier['total_purchases'] = supplierTotalPurchases($supplier['supplier_name'], $supplier['currency']);
            $last_order                  = supplierLastOrder($supplier['supplier_name']);
            $supplier['last_order']      = ($last_order['open_date']) ? date("jS M y", $last_order['open_date']) : 'Never';
            $supplier['date_added']      = ($supplier['date_added']) ? date("d/m/y", $supplier['date_added']) : 'No Date';
            $supplier['order_value']     = $last_order['value_usd'];
            $contacts                    = supplierContactsFromDatabase($supplier_id, true);
            $supplier['contact']         = $contacts[0];
            $supplier['pay_outstanding'] = supplierOutstandingPayments($supplier['supplier_name']);
        } else {
            $supplier = array();
            foreach (supplierGetOptionList('all_fields_types') as $k => $v) {
                $supplier[$k] = $v[4];
            }

            return $supplier;
        }

        return $supplier;
    }
}
if (!function_exists('supplierGetLock')) {
    function supplierGetLock($id)
    {
        global $DB;
        $row = $DB->query_first("SELECT *, UNIX_TIMESTAMP(`expiry_date`) AS `expiry_time` FROM `generic_locks` WHERE `section_name` = 'supplier' AND `generic_id` = " . intval($id) . " AND `expiry_date` > NOW() LIMIT 1", MYSQLI_ASSOC);
        if ($row['expiry_time'] < time()) {
            $row = false;
        }

        return $row;
    }
}
if (!function_exists("supplierGetOptionList")) {
    function supplierGetOptionList($table_name)
    {
        global $DB;
        static $option_cache = array();
        if (isset($option_cache[$table_name])) {
            return $option_cache[$table_name];
        }

        $table       = '';
        $key         = '';
        $value       = '';
        $option_list = array();
        switch ($table_name) {
            case 'active':
                return array('y' => 'Yes', 'n' => 'No');
            case 'country':
                return getCountryList();

            case 'all_fields':
                return array_merge(supplierGetOptionList('base_fields'), supplierGetOptionList('ext_fields'));
            case 'all_fields_types':
                return array_merge(supplierGetOptionList('base_fields_types'), supplierGetOptionList('ext_fields_types'));
            case 'base_fields':
                $fields = array();
                foreach (getTableColumns('suppliers') as $key) {
                    $fields[$key] = 's.`' . $key . '`';
                }

                return $fields;
            case 'base_fields_types':
                return getTableFields('suppliers');
            case 'ext_fields':
            case 'extra_fields':
                return array(
                    "supplier_name" => "`s`.`supplier`",
                    "date_added"    => "UNIX_TIMESTAMP(`s`.`date_added`)",
                    "meta_time"     => "UNIX_TIMESTAMP(`s`.`modified`)",
                    'site'          => '`s`.`site_id`',
                    'post_code'     => '`s`.`postal_code`',
                    'short_desc'    => '`s`.`notes`',
                    'manufacturer'  => '`s`.`supplier`',
                );
            case 'ext_fields_types':
                return array();
            default:
                echo 'supplierGetOptionList(' . json_encode($table_name) . ') is undefined<br />';
                break;
        }
        if ('' != $table) {
            $query = $DB->query("SELECT " . $key . "," . $value . " FROM " . $table);
            while ($row = $DB->fetch_assoc($query)) {
                $option_list[$row[0]] = $row[1];
            }

            $DB->free_result($query);
        }
        $option_cache[$table_name] = $option_list;
        return $option_list;
    }
}
if (!function_exists("supplierGroupCount")) {
    function supplierGroupCount($supplier_list, $group_by = '', $split_sets = true)
    {
        global $DB;
        if (!is_array($supplier_list) || count($supplier_list) < 1) {
            return $supplier_list;
        }

        if ('' === $group_by) {
            return count($supplier_list);
        }

        if (!is_array($group_by)) {
            $group_by = array($group_by);
        }

        if (count($group_by) == 0) {
            return count($supplier_list);
        }

        $fields      = supplierGetOptionList('all_fields');
        $field_types = supplierGetOptionList('all_fields_types');
        $field_list  = array('row_count' => 'COUNT(*) AS row_count');
        foreach ($group_by as $field) {
            if (array_key_exists($field, $fields)) {
                $field_list[$field] = $fields[$field] . " AS " . $field;
            }
        }

        $field_list = array_unique($field_list);
        $group_list = $field_list;
        unset($group_list['row_count']);
        if (count($group_list) == 0) {
            return array(0 => count($supplier_list));
        }

        $query   = $DB->query("SELECT " . implode(', ', $field_list) . " FROM `suppliers` AS `s` WHERE `s`.`id` IN(" . implode(',', $supplier_list) . ") GROUP BY " . implode(', ', array_keys($group_list)));
        $results = array();
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
        return $results;
    }
}
if (!function_exists("supplierIncrementViewCount")) {
    function supplierIncrementViewCount($supplier_id)
    {
        global $DB;
        if (!$supplier_id) {
            return;
        }

        $DB->query("UPDATE `suppliers` SET `view_count` = `view_count` + 1 WHERE `id` = " . intval($supplier_id) . " LIMIT 1");
    }
}
if (!function_exists("supplierLastOrder")) {
    function supplierLastOrder($supplier_name)
    {
        global $DB;

        /*
        if ($row = $DB->query_first(sprintf(
            "SELECT `id`, UNIX_TIMESTAMP(`open_date`) AS `open_date` FROM `purchase_orders` WHERE `supplier` = %s ORDER BY `open_date` DESC LIMIT 1",
            $DB->escape($supplier_name)
        ), MYSQLI_ASSOC)) {
            return array(
                'open_date' => $row['open_date'],
                'value_usd' => supplierOrderValue($row['id']),
            );
        }
        */

        return array(
            'open_date' => null,
            'value_usd' => '0.00',
        );
    }
}
if (!function_exists("supplierOrderValue")) {
    function supplierOrderValue($order_id, $currency = 'USD')
    {
        global $DB;
        $total = 0;

        /*
        $query = $DB->query("SELECT `qty`, `price`, `currency`, `discount` FROM `purchase_order_products` WHERE `order_id` = " . intval($order_id));
        while($row = $DB->fetch_assoc($query))
        {
        if ($row['discount'] == 'y') $total -= (convertCurrency($row['price'], $row['currency'], $currency) * $row['qty']);
        else $total += (convertCurrency($row['price'], $row['currency'], $currency) * $row['qty']);
        }
        $DB->free_result($query);

        $order = $DB->query_first("SELECT `account`, `charge_usd` FROM `purchase_orders_shipping` WHERE `order_id` = " . intval($order_id) . " LIMIT 1");
        if ($order['account'] == 'sup') $total += $order['charge_usd'];
         */

        return number_format($total, 2, '.', '');
    }
}
if (!function_exists("supplierOrders")) {
    function supplierOrders($supplier_name, $type = 'all')
    {
        /*
        global $DB;

        $types = array(
            'open'      => "<> 'processed'",
            'approval'  => "= 'approval'",
            'payment'   => "= 'payment'",
            'goods'     => "= 'goods'",
            'checking'  => "= 'checking'",
            'cancelled' => "= 'cancelled'",
        );

        $join  = "";
        $extra = '';

        if ('all' != $type && in_array($type, array_keys($types))) {
            $extra = " AND `o`.`status` " . $types[$type];
        }

        if ($type == 'open')
        {
            $join = "LEFT JOIN `purchase_orders_status` AS `s` ON `s`.`order_id` = `o`.`id`";
            $extra .= " OR (`s`.`condition` = 'bad' AND `s`.`problem_solved` = 'n')";
        }

        if ($row = $DB->query_first(sprintf(
            "SELECT COUNT(*) AS `total` FROM `purchase_orders` AS `o` %s WHERE `o`.`supplier` = %s%s",
            $join,
            formatSQLString($supplier_name),
            $extra
        ), MYSQLI_ASSOC)) {
            return $row['total'];
        }
        */

        return 0;
    }
}
if (!function_exists("supplierOrdersFromDatabase")) {
    function supplierOrdersFromDatabase($supplier_name)
    {
        global $DB;
        $result = array();
        /*
        $query = $DB->query("SELECT `id` FROM `purchase_orders` WHERE `supplier` = " . formatSQLString($supplier_name) . " ORDER BY `open_date` DESC, `id` DESC", MYSQLI_ASSOC);
        while($row = $DB->fetch_assoc($query)) $result[] = $row['id'];
        $DB->free_result($query);
         */
        return $result;
    }
}
if (!function_exists("supplierOrdersList")) {
    function supplierOrdersList($supplier_name, $type = 'all')
    {
        global $DB;
        $types = array(
            'open'      => "!= 'processed'",
            'approval'  => "= 'approval'",
            'payment'   => "= 'payment'",
            'goods'     => "= 'goods'",
            'checking'  => "= 'checking'",
            'cancelled' => "= 'cancelled'",
        );
        $extra = '';
        $join  = "";
        if ('all' != $type && in_array($type, array_keys($types))) {
            $extra = "AND `o`.`status` " . $types[$type];
        }

        /*
        if ($type == 'open')
        {
        $join = "LEFT JOIN `purchase_orders_status` AS `s` ON `s`.`order_id` = `o`.`id`";
        $extra .= " || (`s`.`condition` = 'bad' AND `s`.`problem_solved` = 'n')";
        }
         */

        $result = array();
        /*
        $query = $DB->query("SELECT `id` FROM `purchase_orders` AS `o` " . $join . " WHERE `o`.`supplier` = " . formatSQLString($supplier_name) . " " . $extra);
        while($row=$DB->fetch_assoc($query)) $result[] = $row['id'];
        $DB->free_result($query);
         */

        return $result;
    }
}
if (!function_exists("supplierOutstandingPayments")) {
    function supplierOutstandingPayments($supplier_name)
    {
        global $DB;
        $total = 0;
        /*
        foreach(supplierOrdersFromDatabase($supplier_name) as $key => $id)
        {
        $row = $DB->query_first("SELECT `pay_outstanding` FROM `purchase_orders_status` WHERE `order_id` = " . $id . " LIMIT 1", MYSQLI_ASSOC);
        $total += $row['pay_outstanding'];
        }
         */
        return $total;
    }
}
if (!function_exists("supplierSearch")) {
    function supplierSearch($criteria = '', $extra = '', $site_id = 0, $type = '')
    {
        global $DB;

        $fields = getTableColumns('suppliers');
        foreach ($fields as $k => $v) {
            $fields[$k] = '`s`.`' . $v . '`';
        }

        $fields = array_merge($fields, supplierGetOptionList('ext_fields'));

        $whereAND = array();
        if (is_array($criteria)) {
            foreach ($criteria as $field => $value) {
                if (isset($fields[$field])) {
                    if (is_array($value)) {
                        if (count($value) > 0) {
                            $whereOR = array();
                            foreach ($value as $sub_value) {
                                $whereOR[] = $fields[$field] . " = " . formatSQLString($sub_value);
                            }

                            $whereAND[] = "	(" . implode(" OR ", $whereOR) . ")";
                        } else {
                            return array();
                        }
                    } else {
                        $whereAND[] = $fields[$field] . " = " . formatSQLString($value);
                    }

                    unset($fields[$field]);
                } elseif (is_string($criteria) && '' != $criteria) {
                    $criteria = array('any_text' => explode(' ', $criteria));
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
            $searchBits = array('s.id', 's.supplier');
            $whereOR    = array();
            foreach ($search_for as $findme) {
                foreach ($searchBits as $key) {
                    if (trim($findme) != '') {
                        $whereOR[] = $key . " LIKE '%" . str_replace('%', '\%', str_replace('_', '\_', addslashes($findme))) . "%'";
                    }
                }

            }
            if (count($whereOR) > 0) {
                $whereAND[] = "	(" . implode(" OR ", $whereOR) . ")";
            }

        }

        if ("" != $extra) {
            $whereAND[] = $extra;
        }

        if ($site_id > 0) {
            $whereAND[] = "FIND_IN_SET('" . intval($site_id) . "', `site`) > 0";
        }

        if ('' != $type) {
            $whereAND[] = "`type` IN('" . implode("', '", explode("|", $type)) . "')";
        }

        $sql = "SELECT `s`.`id` FROM `suppliers` AS `s`";
        if (count($whereAND) > 0) {
            $sql .= " WHERE " . implode(" AND ", $whereAND);
        }

        $sql .= " ORDER BY `s`.`id` ASC";

        $results = array();
        $query   = $DB->query($sql);
        while ($row = $DB->fetch_assoc($query)) {
            $results[] = $row['id'];
        }

        $DB->free_result($query);

        return $results;
    }
}
if (!function_exists('supplierSetLock')) {
    function supplierSetLock($id, $staff_id = 0, $timeout = 120)
    {
        global $DB;
        $section_name = formatSQLString("supplier");
        if (0 != $staff_id) {
            $DB->query(sprintf("UPDATE `generic_locks` SET `expiry_date` = %s, `modified` = NOW() WHERE `section_name` = %s AND `generic_id` = %d AND `staff_id` = %d AND `expiry_date` > NOW() LIMIT 1", formatSQLDateTime(time() + $timeout), $section_name, $id, $staff_id));
            if ($DB->affected_rows() == 0) {
                $DB->query(sprintf("INSERT INTO `generic_locks` (`section_name`, `generic_id`, `staff_id`, `expiry_date`, `created`, `modified`) VALUES (%s, %d, %d, %s, NOW(), NOW())", $section_name, $id, $staff_id, formatSQLDateTime(time() + $timeout)));
            }

        } else {
            $DB->query(sprintf("UPDATE `generic_locks` SET `expiry_date` = NOW(), `modified` = NOW() WHERE `section_name` = %s AND `generic_id` = %d AND `expiry_date` > NOW()", $section_name, $id));
        }

    }
}
if (!function_exists("supplierSites")) {
    function supplierSites($type = '')
    {
        global $DB;
        $result = array();
        $array  = array(
            16 => 'Car Converters',
            11 => 'Highway Group',
            9  => 'Converters.tv',
            3  => 'Coinop Express',
            2  => 'Highway Entertainment',
            1  => 'Highway Games',
        );
        foreach ($array as $site => $name) {
            $row      = $DB->query_first("SELECT COUNT(`id`) AS `total` FROM `suppliers` WHERE FIND_IN_SET('" . $site . "', `site`) > 0" . (('' != $type) ? " AND `type` IN('" . implode("', '", explode('|', $type)) . "')" : ''), MYSQLI_ASSOC);
            $result[] = array('code' => $site, 'name' => $name, 'total' => $row['total']);
        }
        return $result;
    }
}
if (!function_exists("supplierSort")) {
    function supplierSort($supplier_list, $order_by = 'id')
    {
        global $DB;
        if (!is_array($supplier_list) || count($supplier_list) < 2) {
            return $supplier_list;
        }

        $choices = array(
            'id'         => '`s`.`id`',
            'date_added' => '`s`.`date_added`',
            'name'       => '`s`.`supplier`',
            'country'    => '`s`.`country`',
            'site'       => '`s`.`site`',
            'date'       => '`s`.`last_order`',
            'view_count' => '`s`.`view_count`',
        );
        if (isset($choices[$order_by])) {
            $query         = $DB->query("SELECT `s`.`id` FROM `suppliers` AS `s` WHERE `s`.`id` IN(" . implode(", ", $supplier_list) . ") ORDER BY " . $choices[$order_by] . " ASC, `id` ASC");
            $supplier_list = array();
            while ($row = $DB->fetch_assoc($query)) {
                $supplier_list[] = $row['id'];
            }

            $DB->free_result($query);
        } else {
            asort($supplier_list);
        }

        return $supplier_list;
    }
}
if (!function_exists("supplierToDatabase")) {
    function supplierToDatabase($posted)
    {
        global $_GLOBALS;
        global $DB;

        $staff_id    = $_GLOBALS['staff']['id'];
        $errors      = array();
        $action      = $posted['action'];
        $add         = ('do_edit' == $action) ? false : true;
        $supplier_id = intval($posted['supplier_id']);
        $old         = (!$add) ? supplierFromDatabase($supplier_id) : array();
        $sql         = (($add) ? "INSERT INTO " : "UPDATE ") . " suppliers ";
        $table       = $posted['supplier'];
        $set         = array();
        $ignore      = false;
        $changed     = array();
        foreach ($table as $key => $value) {
            if ('supplier' == $key && '' == $value) {
                $ignore = true;
            }

            if (formatCleanString($value) != formatCleanString($old[$key])) {
                $changed[$key] = array('before' => $old[$key], 'after' => $value);
            }

            $set[] = $key . "=" . formatSQLString($value);
        }
        $meta_change = false;
        foreach (array('meta_title', 'meta_keys', 'meta_desc', 'url_keys') as $key) {
            if (formatCleanString($old[$key]) != formatCleanString($posted['supplier'][$key])) {
                $meta_change = true;
            }
        }

        if ($meta_change) {
            $set[] = '`modified` = NOW(), `changed_by` = ' . $staff_id;
        }

        $set['icode'] = '`industry_code` = ""';
        if ('' != $posted['icode'] && is_array($posted['icode']) && count($posted['icode'])) {
            $new_code = implode(',', $posted['icode']);
            if ($old['industry_code'] != $new_code) {
                $changed['industry_code'] = array('before' => $old['industry_code'], 'after' => $new_code);
            }

            $set['icode'] = '`industry_code` = ' . formatSQLString($new_code);
        }
        $sql .= "SET " . implode(', ', $set) . (($add) ? ', date_added=NOW()' : " WHERE `id` = " . $supplier_id);
        if (!$ignore) {
            $DB->query($sql);
            if ($add) {
                $supplier_id = intval($DB->insert_id());
            }

        } else {
            $error[] = "Supplier Name is empty. Cannot Add/Edit supplier if name is empty.";
        }

        /*
        $DB->query("UPDATE supplier_contacts SET main='n' WHERE supplier_id=$supplier_id");
         */
        $updates = array(); //'warehouse', 'contacts', 'bank');
        $check   = array('warehouse' => 'supplier', 'contacts' => 'name');
        foreach ($updates as $tbl) {
            $table = $posted[$tbl];
            if (is_array($table)) {
                foreach ($table as $id => $array) {
                    $check_field = $check[$tbl];
                    if ('bank' == $tbl) {
                        if ('b' == $array['type']) {
                            $check_field = 'beneficiary';
                        } else {
                            $check_field = 'details';
                        }

                    }
                    if ('' != $array[$check_field]) {
                        $sql = (('add' == $array['add']) ? "INSERT INTO " : "UPDATE ") . "supplier_$tbl";
                        if ('add' == $array['add']) {
                            $changed[] = 'New ' . ucwords($tbl) . ' added';
                        }

                        $set = array();
                        foreach ($array as $key => $value) {
                            if ('add' != $key && 'id' != $key) {
                                $set[] = "`" . $key . "`=" . formatSQLString($value);
                            }
                        }

                        $sql .= " SET " . implode(', ', $set) . (('add' == $array['add']) ? (", `supplier_id` = " . $supplier_id) : " WHERE `id` = " . intval($id));
                        $DB->query($sql);
                    }
                }
            }

        }
        $tab = $posted['supplier_for'];
        if (is_array($tab)) {
            $sites = implode(',', array_keys($tab));
            if ('' != $sites) {
                $DB->query("UPDATE `suppliers` SET `site` = '$sites' WHERE `id` = $supplier_id");
            }

        }
        if ('' != $posted['comments']) {
            $DB->query("INSERT INTO `supplier_comments` (`supplier_id`, `date`, `staff`, `comments`) VALUES (" . $supplier_id . ", NOW(), " . formatSQLString($_GLOBALS['staff']['user']) . ", " . formatSQLString($posted['comments']) . ")");
        }

        if (is_array($posted['values'])) {
            foreach ($posted['values'] as $id => $array) {
                $summary = array('id' => $id, 'supplier_id' => $supplier_id);
                foreach ($array as $key => $value) {
                    $summary[$key] = $value;
                }

                $new_id = supplierValuesToDatabase($summary);
            }
        }

        $array   = array('supplier' => 'Name');
        $content = '';
        if (count($changed)) {
            foreach ($changed as $key => $value) {
                if (is_array($value)) {
                    $key = in_array($key, array_keys($array)) ? $array[$key] : $key;
                    $content .= ucwords(str_replace("_", " ", $key)) . " modified: Before: " . $value['before'] . ". New Value: " . $value['after'] . "\n";
                } else {
                    $content .= htmlspecialchars($value) . "\n";
                }

            }
            $change = array(
                'supplier_id' => $supplier_id,
                'user'        => $_GLOBALS['staff']['user'],
                'query'       => $content,
            );
            supplierChangesToDatabase($change);
        }
        return $supplier_id;
    }
}
if (!function_exists("supplierTotalPurchases")) {
    function supplierTotalPurchases($supplier_name, $currency = 'USD')
    {
        global $DB;
        $orders = supplierOrdersFromDatabase($supplier_name);
        $total  = 0;
        foreach ($orders as $key => $id) {
            $total += supplierOrderValue($id, $currency);
        }

        return $total;
    }
}
if (!function_exists("supplierValuesFromDatabase")) {
    function supplierValuesFromDatabase($supplier_id, $section = 0)
    {
        global $DB;
        if (!$supplier_id) {
            return;
        }

        $result = array();
        /*
        $query = $DB->query("SELECT *, UNIX_TIMESTAMP(`date`) AS `time_added` FROM `supplier_defaults` WHERE `supplier_id` = " . intval($supplier_id) . (($section != 0) ? (" AND `section` = " . intval($section)) : ''));
        while($row = $DB->fetch_assoc($query)) $result[] = $row;
        $DB->free_result($query);
         */
        return $result;
    }
}
if (!function_exists("supplierValuesToDatabase")) {
    /**
     * @todo
     *
     * @param mixed[] $summary
     *
     * @return int|null
     */
    function supplierValuesToDatabase($summary)
    {
        return null;
    }
}
if (!function_exists("supplierWarehousesFromDatabase")) {
    function supplierWarehousesFromDatabase($supplier_id, $count = false)
    {
        global $DB;

        if ($count) {
            /*
            if ($row = $DB->query_first(sprintf(
                "SELECT COUNT(*) AS `total` FROM `supplier_warehouse` WHERE `supplier_id` = %d LIMIT 1",
                $supplier_id
            ), MYSQLI_ASSOC)) {
                return $row['total'];
            }
            */
            return 0;
        }

        $result = array();
        /*
        $query = $DB->query("SELECT * FROM `supplier_warehouse` WHERE `supplier_id` = " . intval($supplier_id));
        while($row = $DB->fetch_assoc($query)) $result[] = $row;
        $DB->free_result($query);
         */
        return $result;
    }
}
if (!function_exists('supplierGetValue')) {
    /**
     * @param int    $id
     * @param string $field
     *
     * @return mixed
     */
    function supplierGetValue($id, $field)
    {
        global $DB;

        if (array_key_exists($field, $DB->table_columns(
            "`suppliers`"
        ))) {
            return $DB->modelGet(
                $id,
                $field,
                "`suppliers`",
                "id"
            );
        }

        if ($model = supplierFromDatabase($id)) {
            return $model[$field] ?? null;
        }

        return null;
    }
}
if (!function_exists('supplierSetValue')) {
    /**
     * @param int    $id
     * @param string $field
     * @param mixed  $value
     *
     * @return int
     */
    function supplierSetValue($id, $field, $value)
    {
        global $DB;

        return $DB->modelSet(
            $id,
            $field,
            $value,
            "`suppliers`",
            "id"
        );
    }
}
