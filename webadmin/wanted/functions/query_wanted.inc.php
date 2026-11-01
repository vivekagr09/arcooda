<?php
function wantedChangeActiveStatus($wanted_id, $status)
{
    global $DB;
    $DB->query("UPDATE `wanted` SET `active` = " . $DB->escape($status) . " WHERE `id` = " . intval($wanted_id) . " LIMIT 1");
}
function wantedDeleteFromDatabase($wanted_id)
{
    global $DB;
    $DB->query("DELETE FROM `wanted` WHERE `id` = " . intval($wanted_id) . " LIMIT 1");
}
function wantedExists($wanted_id)
{
    global $DB;
    return $DB->query_first("SELECT `id` FROM `wanted` WHERE `id` = " . intval($wanted_id) . " LIMIT 1");
}

function wantedFromDatabase($wanted_id)
{
    global $DB;
    $fields = array("`w`.*");
    foreach (wantedGetOptionList("ext_fields") as $k => $v) {
        $fields[] = $v . " AS `" . $k . "`";
    }

    $row = $DB->query_first("SELECT " . implode(", ", $fields) . " FROM `wanted` AS `w` WHERE `w`.`id` = " . intval($wanted_id) . " LIMIT 1", MYSQLI_ASSOC);

    if ($row) {
        return $row;
    }

    return false;
}
function wantedOferDromDataBase($wanted_id)
{
    global $DB;
    $wanted_offer=[];
    $query = $DB->query("SELECT * FROM `wanted_offers` WHERE `wanted_id` = " . intval($wanted_id) . "");
    while ($row = $DB->fetch_assoc($query)) {
        $wanted_offer[] = $row;
    }
    return  $wanted_offer;
}
function wantedSalesOferDromDataBase($email)
{
    global $DB;
    $wanted_offer=[];
    $query = $DB->query("SELECT * FROM `wanted_offers` WHERE `email` = " . formatSQLString(trim($email).""));
    while ($row = $DB->fetch_assoc($query)) {
        $wanted_offer[] = $row;
    }
    return  $wanted_offer;
}
function wantedToDatabase($item){
    moduleLoad('invoice'); //invoiceProductsToDatabase, invoiceToDatabase
    moduleLoad('member'); //memberFromDatabase, memberToDatabase, memberIdFromEmail

    global $DB;
     $insert_wanted  = array();
      $item['item']['section_id']=$_REQUEST['item']["type"];
        if(isset($item['item']['invoice'])){
            $item['item']['invoice']='y';
        }else{
            $item['item']['invoice']='n';
        }
        if(!$item['id']){
            $member_email = $item['item']['email_address'] ?? '';
            $member_id    = memberIdFromEmail($member_email);
            if ($member_id) {
            }else{
                $member_data=[];
                $member_data['name']          = $member_data['first_name'] = $item['item']['first_name'];
                $member_data['surname']       = $member_data['last_name']  = $item['item']['last_name'];
                $member_data['email_address'] = $item['item']['email_address'];
                $member_data['phone_number']  = $item['item']['phone_number'];
                $member_data['country']       = $item['item']['member_location'];
                $member_id = memberToDatabase($member_data);
            }

            $item['item']['member_id'] = $member_id;
                $insert_wanted['date_added'] = "NOW()";
        }
            $old_vals   = $DB->query_first(sprintf(
            "SELECT * FROM %s WHERE `%s` = %d LIMIT 1",
                'wanted',
                'id',
                $item['id']
            ), MYSQLI_ASSOC);
            if ($old_vals) {
              if($old_vals['invoice']=='n' && $item['item']['invoice']=='y'){
                    $member = memberFromDatabase($old_vals['member_id']);
                    $_GLOBALS['member'] = $member;

                    /**
                     * @var mixed[]   $invoice
                     * @var mixed[][] $invoice_products
                     */
                    include __DIR__ . '/build_invoice.inc.php';

                    $invoice_id = invoiceToDatabase($invoice);
                    $item['item']['invoice_id']=$invoice_id;
                    invoiceProductsToDatabase($invoice_id, $invoice_products);
                    $item['item']['active']='n';
                }
                $item['item']['updated_at']=formatDateTime();
               foreach ([
                    'id' => 'item_id',
                    'active'=>'active',
                    'approved'=>'approved',
                    'product_name' => 'item',
                    'member_id'   =>'member_id',
                    'type'         => 'type',
                    'wanted_notes'  => 'description',
                    'country_location' => 'pref_location',
                    'price'      => 'target_price',
                    'currency'     => 'target_currency',
                    'qty'          => 'qty_needed',
                    'request_duration' => 'duration_days',
                    'order_time'    => 'order_time',
                    'first_name'    => 'customer_name',
                    'admin_notes'    => 'admin_notes',
                    'email_address'   => 'customer_email',
                    'online_item'   =>'online_item',
                    'online_type'   =>'online_type',
                    'online_pref_location'   =>'online_pref_location',
                    'online_qty_needed'   =>'online_qty_needed',
                    'online_description'   =>'online_description',
                    'online_item_id'  =>'online_item_id',
                    'invoice'         =>'invoice',
                    'invoice_id'      =>'invoice_id',
                    'section_id'     =>'section_id',
                    'updated_at'     =>'updated_at'
                ] as $k => $v) {
                    if (array_key_exists($k, $item['item'])) {
                        $insert_wanted[$v] = "`" . $v . "` = " . formatSQLString(trim($item['item'][$k]));
                    }
                }

                $DB->query(sprintf(
                    "UPDATE %s SET %s WHERE `%s` = %d LIMIT 1",
                    'wanted',
                    implode(', ', $insert_wanted),
                    'id',
                     $item['id']
                ));
              
            }else{
                if($item['item']['invoice']=='y'){
                    $member = memberFromDatabase($member_id);
                    $_GLOBALS['member'] = $member;
                    include __DIR__ . '/build_invoice.inc.php';
                    $invoice_id = invoiceToDatabase($invoice);
                    invoiceProductsToDatabase($invoice_id, $invoice_products);
                    $item['item']['invoice_id']=$invoice_id;
                    $item['item']['active']='n';
                }
                 foreach ([
                    'id' => 'item_id',
                    'active'=>'active',
                    'approved'=>'approved',
                    'product_name' => 'item',
                    'member_id'   =>'member_id',
                    'type'         => 'type',
                    'wanted_notes'  => 'description',
                    'country_location' => 'pref_location',
                    'price'      => 'target_price',
                    'currency'     => 'target_currency',
                    'qty'          => 'qty_needed',
                    'request_duration' => 'duration_days',
                    'order_time'    => 'order_time',
                    'first_name'    => 'customer_name',
                    'admin_notes'    => 'admin_notes',
                    'email_address'   => 'customer_email',
                    'online_item'   =>'online_item',
                    'online_type'   =>'online_type',
                    'online_pref_location'   =>'online_pref_location',
                    'online_qty_needed'   =>'online_qty_needed',
                    'online_description'   =>'online_description',
                    'online_item_id'  =>'online_item_id',
                    'invoice'         =>'invoice',
                    'invoice_id'      =>'invoice_id',
                    'section_id'     =>'section_id'
                ] as $k => $v) {
                    if (array_key_exists($k, $item['item'])) {
                        $insert_wanted[$v] = formatSQLString(trim($item['item'][$k]));
                    }
                }
                $DB->query(sprintf(
                    "INSERT INTO %s (`%s`) VALUES (%s)",
                    'wanted',
                    implode('`, `', array_keys($insert_wanted)),
                    implode(", ", $insert_wanted)
                ));
            }
            if(count($item['item_seller'])){
                foreach ($item['item_seller'] as $key => $seller_item) {
                    $insert_offer=[];
                         $old_vals   = $DB->query_first(sprintf(
                        "SELECT * FROM %s WHERE `%s` = %d LIMIT 1",
                            'wanted_offers',
                            'id',
                            $seller_item['id']
                        ), MYSQLI_ASSOC);
                        if ($old_vals) {
                              foreach ([
                                'wanted_id' => 'wanted_id',
                                'name'=>'name',
                                'email'=>'email',
                                'description' => 'description',
                                'price'   =>'price',
                                'product_location' => 'product_location',
                                'quantity_offered'  => 'quantity_offered',
                                'country' => 'country',
                                'status'      => 'status',
                                ] as $k => $v) {
                                    if (array_key_exists($k, $seller_item)) {
                                        $insert_offer[$v] = "`" . $v . "` = " . formatSQLString(trim($seller_item[$k]));
                                    }
                                }
                            $DB->query(sprintf(
                                "UPDATE %s SET %s WHERE `%s` = %d LIMIT 1",
                                'wanted_offers',
                                implode(', ', $insert_offer),
                                'id',
                                 $seller_item['id']
                            ));
                        }else{
                             foreach ([
                                'wanted_id' => 'wanted_id',
                                'name'=>'name',
                                'email'=>'email',
                                'description' => 'description',
                                'price'   =>'price',
                                'product_location' => 'product_location',
                                'quantity_offered'  => 'quantity_offered',
                                'country' => 'country',
                                'status'      => 'status',
                                ] as $k => $v) {
                                    if (array_key_exists($k, $seller_item)) {
                                        $insert_offer[$v] = formatSQLString(trim($seller_item[$k]));
                                    }
                                }
                                $insert_offer['date_added'] = "NOW()";   
                            $DB->query(sprintf(
                                "INSERT INTO %s (`%s`) VALUES (%s)",
                                'wanted_offers',
                                implode('`, `', array_keys($insert_offer)),
                                implode(", ", $insert_offer)
                            ));
                        }
                }
            }
}
function wantedGetOptionList($table_name)
{
    static $option_cache = array();
    if (isset($option_cache[$table_name])) {
        return $option_cache[$table_name];
    }

    global $DB, $_GLOBALS;
    $table       = '';
    $key         = '';
    $value       = '';
    $order_by    = '';
    $option_list = array();
    switch ($table_name) {
        case 'active':
            return array('n' => 'No', 'y' => 'Yes');
        case 'all_fields':
            return array_merge(wantedGetOptionList('base_fields'), wantedGetOptionList('ext_fields'));
        case 'all_fields_types':
            return array_merge(wantedGetOptionList('base_fields_types'), wantedGetOptionList('ext_fields_types'));
        case 'base_fields':
            $fields = array();
            foreach ($DB->get_table_columns('wanted') as $key) {
                $fields[$key] = '`w`.`' . $key . '`';
            }

            return $fields;
        case 'base_fields_types':
            return $DB->get_table_fields('wanted');
        case 'joined_fields':
            return array();
        case 'ext_fields':
        case 'extra_fields':
            return array(
                'expired'         => "IF(`w`.`expiry_date` > '1970-01-01 00:00:00' AND `w`.`expiry_date` < NOW(), 'y', 'n')",
                'wanted_id'       => '`w`.`id`',
                'date_added_unix' => 'UNIX_TIMESTAMP(`w`.`date_added`)',
                'expiry_unix'     => 'UNIX_TIMESTAMP(`w`.`expiry_date`)',
                "text"            => "`w`.`item`",
                "name"            => "`w`.`item`",
                "title"           => "`w`.`item`",
            );
        case 'ext_fields_types':
            return array();
        default:
            echo 'wantedGetOptionList(' . json_encode($table_name) . ') is undefined<br />';
            break;
    }
    if ('' != $table) {
        $query = $DB->query("SELECT " . $key . "," . $value . " FROM " . $table . (('' != $order_by) ? " ORDER BY " . $order_by . " ASC" : ""));
        while ($row = $DB->fetch_array($query)) {
            $option_list[$row[0]] = $row[1];
        }

        $DB->free_result($query);
    }
    $option_cache[$table_name] = $option_list;
    return $option_list;
}
function wantedGetValue($wanted_id, $field)
{
    global $DB;
    $fields = wantedGetOptionList("all_fields");
    if (array_key_exists($field, $fields)) {
        $row = $DB->query_first("SELECT " . $fields[$field] . " AS `" . $field . "` FROM `wanted` AS `w` WHERE `w`.`id` = " . intval($wanted_id) . " LIMIT 1", MYSQLI_ASSOC);
        return $row[$field];
    }
    return null;
}
function wantedSearch($criteria = '')
{
    global $DB;
    $results = array();
    if (is_string($criteria) && '' != $criteria) {
        $criteria = array('any_text' => $criteria);
    }

    $sql         = " SELECT `w`.`id` FROM `wanted` AS `w`";
    $joins       = array();
    $field_types = wantedGetOptionList('all_fields_types');
    $fields      = wantedGetOptionList('all_fields');
    $whereAND    = array();
    if (is_array($criteria)) {
        foreach ($criteria as $field => $value) {
            if (isset($fields[$field])) {
                if (is_array($value)) {
                    if (count($value) > 0) {
                        $whereOR = array();
                        if (substr($field_types[$field]['Type'], 0, 4) == 'set(') {
                            foreach ($value as $sub_value) {
                                $whereOR[] = "FIND_IN_SET(" . $DB->escape($sub_value) . "," . $fields[$field] . ")";
                            }
                        } else {
                            $temp = array();
                            foreach ($value as $sub_value) {
                                $temp[] = $DB->escape($sub_value);
                            }

                            $whereOR[] = $fields[$field] . " IN(" . implode(", ", $temp) . ")";
                        }
                        $whereAND[] = "	(" . implode(" OR ", $whereOR) . ")";
                    } else {
                        return array();
                    }

                } else {
                    if (substr($field_types[$field]['Type'], 0, 4) == 'set(') {
                        $whereAND[] = "FIND_IN_SET(" . $DB->escape($value) . "," . $fields[$field] . ")";
                    } else {
                        $whereAND[] = $fields[$field] . "=" . $DB->escape($value);
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
        $searchBits = array('`w`.`id`');
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
    if (count($joins) > 0) {
        $sql .= " " . implode(" ", $joins);
    }

    if (count($whereAND) > 0) {
        $sql .= " WHERE " . implode(" AND ", $whereAND);
    }

    $sql .= " GROUP BY `w`.`id`";
    $sql .= " ORDER BY `w`.`id` ASC";
    $query = $DB->query($sql);
    while ($row = $DB->fetch_array($query, MYSQLI_ASSOC)) {
        $results[] = $row['id'];
    }

    $DB->free_result($query);
    return $results;
}
function wantedSort($wanted_list, $order_by = 'id')
{
    global $DB;
    if (!is_array($wanted_list) || count($wanted_list) < 2) {
        return $wanted_list;
    }

    $sql         = "SELECT `w`.`id` FROM `wanted` AS `w`";
    $choices     = wantedGetOptionList("all_fields");
    $where       = " WHERE `w`.`id` IN ('" . implode("', '", $wanted_list) . "')";
    $wanted_list = array();
    $order       = " ORDER BY " . (('' != $choices[$order_by]) ? $choices[$order_by] : '`w`.`id`') . " ASC";
    $query       = $DB->query($sql . $where . $order);
    while ($row = $DB->fetch_assoc($query)) {
        $wanted_list[] = $row['id'];
    }

    return $wanted_list;
}
function wantedFormatNumber($wanted_id)
{
    return sprintf('W%04d', $wanted_id);
}
function wantedChunk($item_list, $group_by = '', $split_sets = true)
{
    global $DB;
    if (!is_array($item_list) || count($item_list) < 1) {
        return $item_list;
    }

    if (!is_array($group_by)) {
        $group_by = array($group_by);
    }

    $fields      = wantedGetOptionList('all_fields');
    $field_types = wantedGetOptionList('all_fields_types');
    $field_list  = array('id');
    foreach ($group_by as $field) {
        if (array_key_exists($field, $fields)) {
            $field_list[] = $fields[$field] . " AS " . $field;
        }
    }

    $field_list = array_unique($field_list);
    $sql        = "SELECT " . implode(', ', $field_list) . " FROM `wanted` AS `w` WHERE `w`.`id` IN(" . implode(",", $item_list) . ")";
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
            foreach (($split_sets) ? explode(',', $key) : array($key) as $key2) {
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
if (!function_exists('wantedSetValue')) {
    /**
     * @param int    $id
     * @param string $field
     * @param mixed  $value
     *
     * @return int
     */
    function wantedSetValue($id, $field, $value)
    {
        global $DB;

        return $DB->modelSet(
            $id,
            $field,
            $value,
            "`wanted`",
            "id"
        );
    }
}
