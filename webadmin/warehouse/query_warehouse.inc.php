<?php
if (!defined('WAREHOUSE_TABLENAME')) {
    define('WAREHOUSE_TABLENAME', '`tbl_warehouse`');
}
if (!defined('WAREHOUSE_PRIMARYKEY')) {
    define('WAREHOUSE_PRIMARYKEY', 'id');
}

if (!function_exists('wareHouseDefault')) {
    /**
     * @return mixed[]
     */
    function wareHouseDefault()
    {
        global $DB;

        return wareHouseVirtual($DB->modelDefault(
            WAREHOUSE_TABLENAME,
            WAREHOUSE_PRIMARYKEY
        ));
    }
}
if (!function_exists('wareHouseDelete')) {
    /**
     * @param int $id
     *
     * @return int
     */
    function wareHouseDelete($id)
    {
        global $DB;

        return $DB->modelDelete(
            $id,
            WAREHOUSE_TABLENAME,
            WAREHOUSE_PRIMARYKEY
        );
    }
}
if (!function_exists('wareHouseExists')) {
    /**
     * @param int $id
     *
     * @return bool
     */
    function wareHouseExists($id)
    {
        global $DB;

        return $DB->modelExists(
            $id,
            WAREHOUSE_TABLENAME,
            WAREHOUSE_PRIMARYKEY
        );
    }
}
if (!function_exists('wareHouseFromDatabase')) {
    /**
     * @param int $id
     *
     * @return mixed[]|null
     */
    function wareHouseFromDatabase($id)
    {
        global $DB;

        if ($row = $DB->modelLoad(
            $id,
            WAREHOUSE_TABLENAME,
            WAREHOUSE_PRIMARYKEY
        )) {
            return wareHouseVirtual($row);
        }

        return null;
    }
}
if (!function_exists('wareHouseGetValue')) {
    /**
     * @param int    $id
     * @param string $field
     *
     * @return mixed
     */
    function wareHouseGetValue($id, $field)
    {
        global $DB;

        if (array_key_exists($field, $DB->table_columns(
            WAREHOUSE_TABLENAME
        ))) {
            return $DB->modelGet(
                $id,
                $field,
                WAREHOUSE_TABLENAME,
                WAREHOUSE_PRIMARYKEY
            );
        }

        if ($model = wareHouseFromDatabase($id)) {
            return $model[$field] ?? null;
        }

        return null;
    }
}
if (!function_exists('wareHouseHasValue')) {
    /**
     * @param int    $id
     * @param string $field
     * @param mixed  $value
     *
     * @return bool
     */
    function wareHouseHasValue($id, $field, $value)
    {
        return $value == wareHouseGetValue($id, $field);
    }
}
if (!function_exists('wareHouseQuery')) {
    /**
     * @param mixed[] $eq
     * @param mixed[] $lt
     * @param mixed[] $gt
     *
     * @return string
     */
    function wareHouseQuery($eq = array(), $lt = array(), $gt = array())
    {
        global $DB;

        if (empty($eq) && empty($lt) && empty($gt)) {
            return '1';
        }

        $field_types = getTableFields(WAREHOUSE_TABLENAME);

        $whereAND = array();
        foreach (array(
            '=' => (is_null($eq)) ? array() : $eq,
            '<' => (is_null($lt)) ? array() : $lt,
            '>' => (is_null($gt)) ? array() : $gt,
        ) as $op => $criteria) {
            foreach ($criteria as $field => $value) {
                if (array_key_exists($field, $field_types)) {
                    $whereOR = array();
                    foreach ((array)$value as $sub_value) {
                        $whereOR[] = sprintf(
                            "`%s` %s %s",
                            $field,
                            $op,
                            $DB->escape($sub_value)
                        );
                    }
                    if (1 == count($whereOR)) {
                        $whereAND[] = implode("", $whereOR);
                    } elseif (count($whereOR)) {
                        $whereAND[] = sprintf("(%s)", implode(" OR ", $whereOR));
                    }
                }
            }
        }

        return (count($whereAND)) ? implode(" AND ", $whereAND) : "1";
    }
}
if (!function_exists('wareHouseSearch')) {
    /**
     * @param string|null $where
     * @param string|null $order_by
     *
     * @return int[]
     */
    function wareHouseSearch($where = null, $order_by = null)
    {
        global $DB;

        // Legacy accepts criteria as first parameter
        if (is_array($where)) {
            $where = wareHouseQuery($where);
        }

        return $DB->query_list(sprintf(
            "SELECT `%s` FROM %s%s%s",
            WAREHOUSE_PRIMARYKEY,
            WAREHOUSE_TABLENAME,
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
if (!function_exists('wareHouseSetValue')) {
    /**
     * @param int    $id
     * @param string $field
     * @param mixed  $value
     *
     * @return int
     */
    function wareHouseSetValue($id, $field, $value)
    {
        global $DB;

        return $DB->modelSet(
            $id,
            $field,
            $value,
            WAREHOUSE_TABLENAME,
            WAREHOUSE_PRIMARYKEY
        );
    }
}
if (!function_exists('wareHouseSort')) {
    /**
     * @param string|null $where
     * @param string|null $order_by
     *
     * @return int[]
     */
    function wareHouseSort($where = null, $order_by = null)
    {
        // Legacy accepts list of id's as first parameter
        if (is_array($where)) {
            $where = wareHouseQuery(array(
                WAREHOUSE_PRIMARYKEY => $where,
            ));
        }

        return wareHouseSearch($where, $order_by);
    }
}
if (!function_exists('wareHouseToDatabase')) {
    /**
     * @param mixed[] $item
     *
     * @return int
     */
    function wareHouseToDatabase(&$item)
    {
        global $DB;

        return $DB->modelSave(
            $item,
            WAREHOUSE_TABLENAME,
            WAREHOUSE_PRIMARYKEY
        );
    }
}
if (!function_exists('wareHouseVirtual')) {
    /**
     * @param mixed[] $row
     *
     * @return mixed[]
     */
    function wareHouseVirtual($row)
    {
        if(isset($row['default_warehouse ']) && $row['default_warehouse '] == 'y'){
            $row['default_warehouse '] = 'y';
        }else{
            $row['default_warehouse '] = 'n';
        }
        return $row;
    }
}

if (!function_exists('wareHouseGetOptionList')) {
    /**
     * @param string $key
     *
     * @return mixed
     */
    function wareHouseGetOptionList($key)
    {
        switch ($key) {
            case 'active':
                return [
                    'n' => 'No',
                    'y' => 'Yes',
                ];

            case 'country':
            case 'country_location':
                return getCountryList(false);

            case 'category':
                return getProductSections();

            case 'continent':
                return getContinentList();

            case 'media_type':
                return [
                    'youtube' => 'Youtube',
                    'facebook'       => 'FaceBook',
                    'instagram'       => 'Instagram',
                    'tiktok'       => 'TikTok',
                    'twiter'       => 'X',
                    'forum'       => 'Forum',
                    'twitch'       => 'Twitch',
                    'discord'       => 'Discord',
                ];

             case 'member_type':
                return [
                    'Customer' => 'Customer',
                    'Distributor'       => 'Distributor',
                    'Media Partner'       => 'Media Partner',
                    'Product Tester'       => 'Product Tester',
                ];    
            case 'item_image_path':
                return $_SERVER['DOCUMENT_ROOT'] . '/webadmin/uploads/media_partner/item_image';
            case 'all_fields':
                $fields = array();
                foreach (getTableColumns(WAREHOUSE_TABLENAME) as $key) {
                    $fields[$key] = sprintf("`p`.`%s`", $key);
                }
                return $fields;

            case 'all_fields_types':
                return getTableFields(WAREHOUSE_TABLENAME);
            case 'screenshot_path':
                return '/home/arcooda/public_html/webadmin/socialmedia/screenshot/';
        }

        return null;
    }
}
if (!function_exists('wareHouseAddComment')) {
    /**
     * @param int $id
     * @param string $comment
     *
     * @return int
     */
    function wareHouseAddComment($id, $comment)
    {
        $member_id = wareHouseGetValue($id, 'member_id');

        if (!$member_id) {
            return 0;
        }

        moduleLoad('member');
        if (function_exists('memberCommentAdd')) {
            return memberCommentAdd($member_id, $comment);
        }

        return 0;
    }
}
if (!function_exists('partnerSQLList')) {
    function partnerSQLList($partner_list)
    {
        if (count($partner_list) == 0) {
            return "1";
        }

        $whereOR = array();
        $whereIN = array();
        foreach (list_to_partitions($partner_list) as $k => $v) {
            if ($k > $v + 2) {
                $whereOR[] = "(`p`.`id` >= " . $v . " AND `p`.`id` <= " . $k . ")";
            } else {
                foreach (range($v, $k) as $k2) {
                    $whereIN[] = $k2;
                }
            }

        }
        if (count($whereIN) > 1) {
            $whereOR[] = "`p`.`id` IN(" . implode(", ", $whereIN) . ")";
        } elseif (count($whereIN) == 1) {
            $whereOR[] = "`p`.`id` = " . implode(" OR `p`.`id` = ", $whereIN);
        }

        return implode(" OR ", $whereOR);
    }
}
if (!function_exists('wareHouseGroupCount')) {
    function wareHouseGroupCount($item_list, $group_by = '', $split_sets = true)
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
        $fields      = wareHouseGetOptionList('all_fields');
        $field_types = wareHouseGetOptionList('all_fields_types');
        $field_list  = array('row_count' => 'COUNT(*) AS `row_count`');
        foreach ($group_by as $field) {
            if (array_key_exists($field, $fields)) {
                $field_list[$field] = $fields[$field] . " AS `" . $field . "`";
            }
        }

        $group_list = $field_list;
        unset($group_list['row_count']);
        if (count($group_list) == 0) {
            return array(count($item_list));
        }

        $results = array();
        if ($query = $DB->query(sprintf(
            "SELECT %s FROM %s AS `p` %s WHERE %s GROUP BY `%s`",
            implode(', ', $field_list),
            WAREHOUSE_TABLENAME,
            implode(' ', $join_list),
            partnerSQLList($item_list),
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
if (!function_exists("wareHouseTemplateVars")) {
    /**
     * @param mixed[]   $partner
     */
    function wareHouseTemplateVars($partner = array())
    {
        global $DB,$_GLOBALS;
        $invoice_labels = $DB->query_first(sprintf(
            "SELECT * FROM `highwaygroup`.`company_info` WHERE `company_id` = %d LIMIT 1",
            $_GLOBALS['site_settings']['site_id']
        ), MYSQLI_ASSOC);
        $results = array();

        foreach (array(
            'PARTNER_ID',
            'MEMBER_ID',
            'COMPANY',
            'FIRST_NAME',
            'LAST_NAME',
            'ADDRESS',
            'EMAIL',
            'CITY',
            'PHONE_NUMBER',
            'STATE',
            'COUNTRY',
            'MOBILE',
            'NDA_SIGNED',
            'STATUS',
        ) as $key) {
            $results['[' . strtoupper($key) . ']'] = '';
        }

        $results['[PARTNER_ID]'] = $partner['id'] ?? '';
        $results['[MEMBER_ID]'] = $partner['member_id']?? '';
        $results['[REQUEST_DATE]']           = date('jS F, Y', time());
        $results['[REQUEST_TIME]']           = date('g:i a', time());
        $results['[FIRST_NAME]']             = trim($partner['name'])?? trim($partner['first_name']) ??  '';
        $results['[LAST_NAME]']              = trim($partner['surname'])?? trim($partner['last_name']) ??  '';
        $results['[NAME]']              = trim($results['[FIRST_NAME]']).' '.trim($results['[LAST_NAME]']);
        $results['[ADDRESS]']              = trim($partner['address_1'])?? '';
        $results['[EMAIL]']              = trim($partner['email_address'])?? trim($partner['email']) ?? '';
        $results['[CITY]']              = trim($partner['city'])?? '';
        $results['[PHONE_NUMBER]']              = trim($partner['phone_number'])?? '';
        $results['[STATE]']              = trim($partner['state'])?? '';
        $results['[COUNTRY]']              = trim($partner['country'])?? '';
        $results['[MOBILE]']              = trim($partner['mobile_number'])?? '';
        $results['[NDA_SIGNED]']              = $partner['nda_signed'] == 'y'?'YES':'No';
         $results['[STATUS]']              =  $partner['active '] == 'y'?'YES':'No';
           $results['[COMPANY_SIGNATURE]']              = $invoice_labels['company_signature_au'] ?? 'Arcooda';
        return $results;
    }
}