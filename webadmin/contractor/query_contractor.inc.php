<?php
if (!defined('CONTRACTOR_TABLENAME')) {
    define('CONTRACTOR_TABLENAME', '`contractors`');
}
if (!defined('CONTRACTOR_PRIMARYKEY')) {
    define('CONTRACTOR_PRIMARYKEY', 'contractor_id');
}

if (!function_exists('contractorDefault')) {
    /**
     * @return mixed[]
     */
    function contractorDefault()
    {
        global $DB;

        return contractorVirtual($DB->modelDefault(
            CONTRACTOR_TABLENAME,
            CONTRACTOR_PRIMARYKEY
        ));
    }
}
if (!function_exists('contractorDelete')) {
    /**
     * @param int $id
     *
     * @return int
     */
    function contractorDelete($id)
    {
        global $DB;

        return $DB->modelDelete(
            $id,
            CONTRACTOR_TABLENAME,
            CONTRACTOR_PRIMARYKEY
        );
    }
}
if (!function_exists('contractorExists')) {
    /**
     * @param int $id
     *
     * @return bool
     */
    function contractorExists($id)
    {
        global $DB;

        return $DB->modelExists(
            $id,
            CONTRACTOR_TABLENAME,
            CONTRACTOR_PRIMARYKEY
        );
    }
}
if (!function_exists('contractorFromDatabase')) {
    /**
     * @param int $id
     *
     * @return mixed[]|null
     */
    function contractorFromDatabase($id)
    {
        global $DB;

        if ($row = $DB->modelLoad(
            $id,
            CONTRACTOR_TABLENAME,
            CONTRACTOR_PRIMARYKEY
        )) {
            return contractorVirtual($row);
        }

        return null;
    }
}
if (!function_exists('contractorGetValue')) {
    /**
     * @param int    $id
     * @param string $field
     *
     * @return mixed
     */
    function contractorGetValue($id, $field)
    {
        global $DB;

        if (array_key_exists($field, $DB->table_columns(
            CONTRACTOR_TABLENAME
        ))) {
            return $DB->modelGet(
                $id,
                $field,
                CONTRACTOR_TABLENAME,
                CONTRACTOR_PRIMARYKEY
            );
        }

        if ($model = contractorFromDatabase($id)) {
            return $model[$field] ?? null;
        }

        return null;
    }
}
if (!function_exists('contractorHasValue')) {
    /**
     * @param int    $id
     * @param string $field
     * @param mixed  $value
     *
     * @return bool
     */
    function contractorHasValue($id, $field, $value)
    {
        return $value == contractorGetValue($id, $field);
    }
}
if (!function_exists('contractorQuery')) {
    /**
     * @todo
     *
     * @param mixed[] $eq
     * @param mixed[] $lt
     * @param mixed[] $gt
     *
     * @return string
     */
    function contractorQuery($eq = [], $lt = [], $gt = [])
    {
        global $DB;
        unset($eq['site_id']);
        $fields = $DB->table_columns(CONTRACTOR_TABLENAME);

        $whereAND = [];
        foreach ([
            '=' => (array)$eq,
            '<' => (array)$lt,
            '>' => (array)$gt,
        ] as $op => $criteria) {
            foreach ($criteria as $field => $value) {
                if (array_key_exists($field, $fields)) {
                    $whereOR = [];
                    foreach ((array)$value as $sub_value) {
                        $whereOR[] = sprintf(
                            "`%s` %s %s",
                            $field,
                            $op,
                            $DB->escape($sub_value)
                        );
                    }
                    if (0 == count($whereOR)) {
                        return "0";
                    }
                    $whereAND[] = (1 == count($whereOR)) ? implode(" OR ", $whereOR) : sprintf("(%s)", implode(" OR ", $whereOR));
                }
            }
        }

        return (count($whereAND)) ? implode(" AND ", $whereAND) : "1";
    }
}
if (!function_exists('contractorSearch')) {
    /**
     * @param string|null $where
     * @param string|null $order_by
     *
     * @return int[]
     */
    function contractorSearch($where = null, $order_by = null)
    {
        global $DB;

        // Legacy accepts criteria as first parameter
        if (is_array($where)) {
            $where = contractorQuery($where);
        }   

        return $DB->query_list(sprintf(
            "SELECT `%s` FROM %s%s%s",
            CONTRACTOR_PRIMARYKEY,
            CONTRACTOR_TABLENAME,
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
if (!function_exists('contractorSetValue')) {
    /**
     * @param int    $id
     * @param string $field
     * @param mixed  $value
     *
     * @return int
     */
    function contractorSetValue($id, $field, $value)
    {
        global $DB;

        return $DB->modelSet(
            $id,
            $field,
            $value,
            CONTRACTOR_TABLENAME,
            CONTRACTOR_PRIMARYKEY
        );
    }
}
if (!function_exists('contractorSort')) {
    /**
     * @param string|null $where
     * @param string|null $order_by
     *
     * @return int[]
     */
    function contractorSort($where = null, $order_by = null)
    {
        // Legacy accepts list of id's as first parameter
        if (is_array($where)) {
            $where = contractorQuery([
                CONTRACTOR_PRIMARYKEY => $where,
            ]);
        }

        return contractorSearch($where, $order_by);
    }
}
if (!function_exists('contractorToDatabase')) {
    /**
     * @param mixed[] $item
     *
     * @return int
     */
    function contractorToDatabase(&$item)
    {
        global $DB;

        return $DB->modelSave(
            $item,
            CONTRACTOR_TABLENAME,
            CONTRACTOR_PRIMARYKEY
        );
    }
}
if (!function_exists('contractorVirtual')) {
    /**
     * @param mixed[] $row
     *
     * @return mixed[]
     */
    function contractorVirtual($row)
    {
        $row['id'] = $row[CONTRACTOR_PRIMARYKEY];

        $row['address']        = $row['address_1'];
        $row['company_name']   = $row['company'];
        $row['fax_no']         = $row['fax_number'];
        $row['last_contacted'] = $row['modified'];
        $row['mobile_no']      = $row['mobile_number'];
        $row['first_name']     = $row['name'];
        $row['phone_no']       = $row['phone_number'];
        $row['last_name']      = $row['surname'];
        $row['website']        = $row['web_page_url'];
        $row['post_code']      = $row['zip'];

        $row['check_time']    = (is_null($row['check_date'])) ? null : strtotime($row['check_date']);
        $row['created_time']  = (is_null($row['created'])) ? null : strtotime($row['created']);
        $row['modified_time'] = (is_null($row['modified'])) ? null : strtotime($row['modified']);

        $row['last_initial'] = substr($row['surname'], 0, 1);
        $row['full_name']    = trim($row['name'] . ' ' . $row['surname']);

        $row['contractor_number'] = contractorFormatNumber($row['contractor_id']);
        $row['address_full']      = contractorFormatAddress($row);

        return $row;
    }
}

if (!function_exists('contractorEmailTemplatesList')) {
    function contractorEmailTemplatesList()
    {
        global $DB;

        static $result;
        if (!is_null($result)) {
            return $result;
        }

        $result = array();
        if ($query = $DB->query(sprintf(
            "SELECT `template_id`, `template_name`, `template_subject`, `template_text` FROM `highwaygroup`.`email_templates`` WHERE FIND_IN_SET('contractor', `template_group`)"
        ))) {
            while ($row = $DB->fetch_assoc($query)) {
                $result[$row['template_id']] = $row;
            }
            $DB->free_result($query);
        }

        return $result;
    }
}
if (!function_exists('contractorFormatAddress')) {
    function contractorFormatAddress($contractor)
    {
        $country_list = contractorGetOptionList('country');
        $address      = array();
        if (isset($contractor['company_name'])) {
            $address[] = $contractor['company_name'];
        }

        if (isset($contractor['address'])) {
            $address[] = $contractor['address'];
        }

        $addline = '';
        if (isset($contractor['city'])) {
            $addline .= $contractor['city'] . ', ';
        }

        if (isset($contractor['state'])) {
            $addline .= $contractor['state'] . ' ';
        }

        if (isset($contractor['post_code'])) {
            $addline .= $contractor['post_code'];
        }

        if ('' != $addline) {
            $address[] = $addline;
        }

        if (isset($country_list[$contractor['country']])) {
            $address[] = $country_list[$contractor['country']];
        }

        $address[] = '';
        if (isset($contractor['phone_no']) && '' != $contractor['phone_no']) {
            $address[] = "\nPh: " . $contractor['phone_no'];
        }

        return implode("\n", $address);
    }
}
if (!function_exists('contractorFormatNumber')) {
    function contractorFormatNumber($contractor_id)
    {
        return sprintf('CT#%06d', $contractor_id);
    }
}
if (!function_exists('contractorGetOptionList')) {
    function contractorGetOptionList($table_name)
    {
        global $DB;

        $table       = '';
        $option_list = array();
        switch ($table_name) {
            case 'id':
                $table = '`contractors`';
                $key   = 'contractor_id';
                $value = "CONCAT_WS(' ', `name`, `surname`)";
                break;

            case 'currency':return getCurrencyList();
            case 'country':return getCountryList(false);
            case 'courier_id':
                $table = 'couriers';
                $key   = 'courier_id';
                $value = 'courier_name';
                break;
            case 'check_staff_id':
            case 'staff_id':return getStaffList();

            case 'survey_type':
            case 'option':
                $table = '`contractors_survey_types`';
                $key   = 'survey_type';
                $value = 'short_desc';
                break;
            case 'contractor_category':
                $table = '`contractor_category`';
                $key   = 'id';
                $value = 'name';
                break;

            case 'site_id':
                //no break
            case 'contractor_type':
                return getWebsitesList();

            case 'performance':
                $response=[];
                for($i=1;$i<=10;$i++) {
                    $response[$i]=$i;
                }
                return $response;
                break;
            case 'attachment_path':
                return $_SERVER['DOCUMENT_ROOT'] . '/webadmin/uploads/contractor/attachment';

            case 'joined_fields':
                $joins = array(
                    'LEFT JOIN `contractors_contacts` AS `mc` ON `mc`.`contractor_id` = `m`.`contractor_id`' => array(
                        'contact_title' => '`mc`.`title`',
                        'contact_name'  => '`mc`.`name`',
                        'contact_phone' => '`mc`.`phone`',
                        'contact_email' => '`mc`.`email`',
                    ),
                    'LEFT JOIN `contractors_emails` AS `me` ON `me`.`contractor_id` = `m`.`contractor_id`'   => array(
                        'email_title' => '`me`.`title`',
                        'email_name'  => '`me`.`name`',
                        'email_phone' => '`me`.`phone`',
                        'email_email' => '`me`.`email_address`',
                    ),
                );
                foreach (getTableColumns('`invoices`') as $field) {
                    foreach ($joins as $join => $rows) {
                        unset($joins[$join][$field]);
                    }
                }
                return $joins;

            case 'all_fields':
                return array_merge(contractorGetOptionList('base_fields'), contractorGetOptionList('ext_fields'));
            case 'all_fields_types':
                return array_merge(contractorGetOptionList('base_fields_types'), contractorGetOptionList('ext_fields_types'));
            case 'base_fields':
                $fields = array();
                foreach (getTableColumns('`contractors`') as $key) {
                    $fields[$key] = 'm.`' . $key . '`';
                }

                return $fields;
            case 'base_fields_types':
                return getTableFields('`contractors`');
            case 'ext_fields_types':
                return array();
            case 'extra_fields':
            case 'ext_fields':
                return array(
                    'id'             => 'contractor_id',
                    'address'        => 'address_1',
                    'company_name'   => 'company',
                    'fax_no'         => 'fax_number',
                    'last_contacted' => 'modified',
                    'mobile_no'      => 'mobile_number',
                    'first_name'     => 'name',
                    'phone_no'       => 'phone_number',
                    'last_name'      => 'surname',
                    'website'        => 'web_page_url',
                    'post_code'      => 'zip',

                    'check_time'     => "UNIX_TIMESTAMP(`m`.`check_date`)",
                    'created_time'   => "UNIX_TIMESTAMP(`m`.`created`)",
                    'modified_time'  => "UNIX_TIMESTAMP(`m`.`modified`)",
                    'last_initial'   => 'SUBSTRING(m.surname,1,1)',
                    'full_name'      => "CONCAT_WS(' ',m.name,m.surname)",
                );
                break;
        }
        if ('' != $table) {
            $query = $DB->query("SELECT " . $key . "," . $value . " FROM " . $table);
            while ($row = $DB->fetch_array($query, MYSQLI_NUM)) {
                $option_list[$row[0]] = $row[1];
            }

        }
        return $option_list;
    }
}
if (!function_exists('contractorGroupCount')) {
    function contractorGroupCount($contractor_list, $group_by = '', $split_sets = true)
    {
        global $DB;
        if (!is_array($contractor_list) || count($contractor_list) < 1) {
            return $contractor_list;
        }

        if ('' === $group_by) {
            return count($contractor_list);
        }

        if (!is_array($group_by)) {
            $group_by = array($group_by);
        }

        if (count($group_by) == 0) {
            return count($contractor_list);
        }

        $fields      = contractorGetOptionList('all_fields');
        $field_types = contractorGetOptionList('all_fields_types');
        $field_list  = array('row_count' => 'COUNT(*) AS row_count');
        foreach ($group_by as $field) {
            if (array_key_exists($field, $fields)) {
                $field_list[$field] = $fields[$field] . " AS " . $field;
            }
        }

        $field_list = array_unique($field_list);
        $group_list = $field_list;
        unset($group_list['row_count']);
        $query   = $DB->query("SELECT " . implode(', ', $field_list) . " FROM `contractors` AS `m` WHERE `m`.`contractor_id` IN(" . implode(', ', $contractor_list) . ") GROUP BY " . implode(', ', array_keys($group_list)));
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
if (!function_exists('contractorTemplateVars')) {
    function contractorTemplateVars($contractor = array())
    {
        global $DB, $_GLOBALS;
        $contractor_id = intval($contractor['contractor_id']);

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
            "SELECT * FROM `highwaygroup`.`company_info` AS `ci` WHERE `ci`.`company_id` = %d LIMIT 1",
            $_GLOBALS['site_settings']['site_id']
        ), MYSQLI_ASSOC);

        $website = $DB->query_first(sprintf(
            "SELECT * FROM `websites` AS `w` WHERE `w`.`id` = %d LIMIT 1",
            $_GLOBALS['site_settings']['site_id']
        ), MYSQLI_ASSOC);

        foreach ($fields as $k) {
            $temp[$k] = '';
            if (is_array($company_info) && array_key_exists($k, $company_info)) {
                $temp[$k] = $company_info[$k];
            } elseif (is_array($website) && array_key_exists($k, $website)) {
                $temp[$k] = $website[$k];
            } elseif (is_array($contractor) && array_key_exists($k, $contractor)) {
                $v           = $contractor[$k];
                $option_list = contractorGetOptionList($k);
                $temp[$k]    = (is_array($option_list) && array_key_exists($v, $option_list)) ? $option_list[$v] : $v;
            } else {
                $temp[$k] = '';
            }
        }

        $temp['request_date'] = date(_('jS F, Y'));

        $result = array();
        foreach ($temp as $k => $v) {
            $result['[' . strtoupper($k) . ']']      = $v;
            $result['[' . strtoupper($k) . '_TXT]']  = $v;
            $result['[' . strtoupper($k) . '_HTML]'] = formatHTMLDescription($v);
        }
        return $result;
    }
}
