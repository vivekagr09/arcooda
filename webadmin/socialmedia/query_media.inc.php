<?php
if (!defined('SOVIAL_MEDIA_PARTNER_TABLENAME')) {
    define('SOCIAL_MEDIA_PARTNER_TABLENAME', '`social_media_partner`');
}
if (!defined('SOCIAL_MEDIA_PARTNER_PRIMARYKEY')) {
    define('SOCIAL_MEDIA_PARTNER_PRIMARYKEY', 'id');
}

if (!function_exists('socialMediaPartnerDefault')) {
    /**
     * @return mixed[]
     */
    function socialMediaPartnerDefault()
    {
        global $DB;

        return socialMediaPartnerVirtual($DB->modelDefault(
            SOCIAL_MEDIA_PARTNER_TABLENAME,
            SOCIAL_MEDIA_PARTNER_PRIMARYKEY
        ));
    }
}
if (!function_exists('socialMediaPartnerDelete')) {
    /**
     * @param int $id
     *
     * @return int
     */
    function socialMediaPartnerDelete($id)
    {
        global $DB;

        return $DB->modelDelete(
            $id,
            SOCIAL_MEDIA_PARTNER_TABLENAME,
            SOCIAL_MEDIA_PARTNER_PRIMARYKEY
        );
    }
}
if (!function_exists('socialMediaPartnerExists')) {
    /**
     * @param int $id
     *
     * @return bool
     */
    function socialMediaPartnerExists($id)
    {
        global $DB;

        return $DB->modelExists(
            $id,
            SOCIAL_MEDIA_PARTNER_TABLENAME,
            SOCIAL_MEDIA_PARTNER_PRIMARYKEY
        );
    }
}
if (!function_exists('socialMediaPartnerFromDatabase')) {
    /**
     * @param int $id
     *
     * @return mixed[]|null
     */
    function socialMediaPartnerFromDatabase($id)
    {
        global $DB;

        if ($row = $DB->modelLoad(
            $id,
            SOCIAL_MEDIA_PARTNER_TABLENAME,
            SOCIAL_MEDIA_PARTNER_PRIMARYKEY
        )) {
            return socialMediaPartnerVirtual($row);
        }

        return null;
    }
}
if (!function_exists('socialMediaPartnerGetValue')) {
    /**
     * @param int    $id
     * @param string $field
     *
     * @return mixed
     */
    function socialMediaPartnerGetValue($id, $field)
    {
        global $DB;

        if (array_key_exists($field, $DB->table_columns(
            SOCIAL_MEDIA_PARTNER_TABLENAME
        ))) {
            return $DB->modelGet(
                $id,
                $field,
                SOCIAL_MEDIA_PARTNER_TABLENAME,
                SOCIAL_MEDIA_PARTNER_PRIMARYKEY
            );
        }

        if ($model = socialMediaPartnerFromDatabase($id)) {
            return $model[$field] ?? null;
        }

        return null;
    }
}
if (!function_exists('socialMediaPartnerHasValue')) {
    /**
     * @param int    $id
     * @param string $field
     * @param mixed  $value
     *
     * @return bool
     */
    function socialMediaPartnerHasValue($id, $field, $value)
    {
        return $value == socialMediaPartnerGetValue($id, $field);
    }
}
if (!function_exists('socialMediaPartnerQuery')) {
    /**
     * @param mixed[] $eq
     * @param mixed[] $lt
     * @param mixed[] $gt
     *
     * @return string
     */
    function socialMediaPartnerQuery($eq = array(), $lt = array(), $gt = array())
    {
        global $DB;

        if (empty($eq) && empty($lt) && empty($gt)) {
            return '1';
        }

        $field_types = getTableFields(SOCIAL_MEDIA_PARTNER_TABLENAME);

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
if (!function_exists('socialMediaPartnerSearch')) {
    /**
     * @param string|null $where
     * @param string|null $order_by
     *
     * @return int[]
     */
    function socialMediaPartnerSearch($where = null, $order_by = null)
    {
        global $DB;

        // Legacy accepts criteria as first parameter
        if (is_array($where)) {
            $where = socialMediaPartnerQuery($where);
        }

        return $DB->query_list(sprintf(
            "SELECT `%s` FROM %s%s%s",
            SOCIAL_MEDIA_PARTNER_PRIMARYKEY,
            SOCIAL_MEDIA_PARTNER_TABLENAME,
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
if (!function_exists('socialMediaPartnerSetValue')) {
    /**
     * @param int    $id
     * @param string $field
     * @param mixed  $value
     *
     * @return int
     */
    function socialMediaPartnerSetValue($id, $field, $value)
    {
        global $DB;

        return $DB->modelSet(
            $id,
            $field,
            $value,
            SOCIAL_MEDIA_PARTNER_TABLENAME,
            SOCIAL_MEDIA_PARTNER_PRIMARYKEY
        );
    }
}
if (!function_exists('socialMediaPartnerSort')) {
    /**
     * @param string|null $where
     * @param string|null $order_by
     *
     * @return int[]
     */
    function socialMediaPartnerSort($where = null, $order_by = null)
    {
        // Legacy accepts list of id's as first parameter
        if (is_array($where)) {
            $where = socialMediaPartnerQuery(array(
                SOCIAL_MEDIA_PARTNER_PRIMARYKEY => $where,
            ));
        }

        return socialMediaPartnerSearch($where, $order_by);
    }
}
if (!function_exists('socialMediaPartnerToDatabase')) {
    /**
     * @param mixed[] $item
     *
     * @return int
     */
    function socialMediaPartnerToDatabase(&$item)
    {
        global $DB;

        return $DB->modelSave(
            $item,
            SOCIAL_MEDIA_PARTNER_TABLENAME,
            SOCIAL_MEDIA_PARTNER_PRIMARYKEY
        );
    }
}
if (!function_exists('socialMediaPartnerVirtual')) {
    /**
     * @param mixed[] $row
     *
     * @return mixed[]
     */
    function socialMediaPartnerVirtual($row)
    {
        if(isset($row['nda_signed']) && $row['nda_signed'] == 'y'){
            $row['nda_signed'] = 'y';
        }else{
            $row['nda_signed'] = 'n';
        }
        if(isset($row['last_connected']) && $row['last_connected'] == 'y'){
            $row['last_connected_date'] = date('Y-m-d');
        }
        return $row;
    }
}

if (!function_exists('socialMediaPartnerGetOptionList')) {
    /**
     * @param string $key
     *
     * @return mixed
     */
    function socialMediaPartnerGetOptionList($key)
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
            case 'media_icons':
                return [
                    'youtube' => '<i class="sicon fab fa-youtube"></i>',
                    'facebook'       => '<i class="sicon fab fa-facebook-f"></i>',
                    'instagram'       => '<i class="sicon fab fa-instagram"></i>',
                    'tiktok'       => '<i class="sicon fab fa-tiktok"></i>',
                    'twitter' =>'<i class="sicon fab fa-twitter"></i>',
                    'forum' =>'<i class="sicon fas fa-comments"></i>',
                    'twitch' =>'<i class="sicon fab fa-twitch"></i>',
                    'discord' =>'<i class="sicon fab fa-discourse"></i>'
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
                foreach (getTableColumns(SOCIAL_MEDIA_PARTNER_TABLENAME) as $key) {
                    $fields[$key] = sprintf("`p`.`%s`", $key);
                }
                return $fields;

            case 'all_fields_types':
                return getTableFields(SOCIAL_MEDIA_PARTNER_TABLENAME);
            case 'screenshot_path':
                return '/home/arcooda/public_html/webadmin/socialmedia/screenshot/';
        }

        return null;
    }
}
if (!function_exists('socialMediaPartnerAddComment')) {
    /**
     * @param int $id
     * @param string $comment
     *
     * @return int
     */
    function socialMediaPartnerAddComment($id, $comment)
    {
        $member_id = socialMediaPartnerGetValue($id, 'member_id');

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
if (!function_exists('socialMediaPartnerGroupCount')) {
    function socialMediaPartnerGroupCount($item_list, $group_by = '', $split_sets = true)
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
        $fields      = socialMediaPartnerGetOptionList('all_fields');
        $field_types = socialMediaPartnerGetOptionList('all_fields_types');
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
            SOCIAL_MEDIA_PARTNER_TABLENAME,
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
if (!function_exists("socialMediaPartnerTemplateVars")) {
    /**
     * @param mixed[]   $partner
     */
    function socialMediaPartnerTemplateVars($partner = array())
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