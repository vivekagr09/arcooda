<?php
if (!defined('DISTRIBUTOR_TABLENAME')) {
    define('DISTRIBUTOR_TABLENAME', '`distributor`');
}
if (!defined('DISTRIBUTOR_PRIMARYKEY')) {
    define('DISTRIBUTOR_PRIMARYKEY', 'id');
}

if (!function_exists('distributorDefault')) {
    /**
     * @return mixed[]
     */
    function distributorDefault()
    {
        global $DB;

        return distributorVirtual($DB->modelDefault(
            DISTRIBUTOR_TABLENAME,
            DISTRIBUTOR_PRIMARYKEY
        ));
    }
}
if (!function_exists('distributorDelete')) {
    /**
     * @param int $id
     *
     * @return int
     */
    function distributorDelete($id)
    {
        global $DB;

        return $DB->modelDelete(
            $id,
            DISTRIBUTOR_TABLENAME,
            DISTRIBUTOR_PRIMARYKEY
        );
    }
}
if (!function_exists('distributorExists')) {
    /**
     * @param int $id
     *
     * @return bool
     */
    function distributorExists($id)
    {
        global $DB;

        return $DB->modelExists(
            $id,
            DISTRIBUTOR_TABLENAME,
            DISTRIBUTOR_PRIMARYKEY
        );
    }
}
if (!function_exists('distributorFromDatabase')) {
    /**
     * @param int $id
     *
     * @return mixed[]|null
     */
    function distributorFromDatabase($id)
    {
        global $DB;

        if ($row = $DB->modelLoad(
            $id,
            DISTRIBUTOR_TABLENAME,
            DISTRIBUTOR_PRIMARYKEY
        )) {
            return distributorVirtual($row);
        }

        return null;
    }
}
if (!function_exists('distributorGetValue')) {
    /**
     * @param int    $id
     * @param string $field
     *
     * @return mixed
     */
    function distributorGetValue($id, $field)
    {
        global $DB;

        if (array_key_exists($field, $DB->table_columns(
            DISTRIBUTOR_TABLENAME
        ))) {
            return $DB->modelGet(
                $id,
                $field,
                DISTRIBUTOR_TABLENAME,
                DISTRIBUTOR_PRIMARYKEY
            );
        }

        if ($model = distributorFromDatabase($id)) {
            return $model[$field] ?? null;
        }

        return null;
    }
}
if (!function_exists('distributorHasValue')) {
    /**
     * @param int    $id
     * @param string $field
     * @param mixed  $value
     *
     * @return bool
     */
    function distributorHasValue($id, $field, $value)
    {
        return $value == distributorGetValue($id, $field);
    }
}
if (!function_exists('distributorQuery')) {
    /**
     * @param mixed[] $eq
     * @param mixed[] $lt
     * @param mixed[] $gt
     *
     * @return string
     */
    function distributorQuery($eq = array(), $lt = array(), $gt = array())
    {
        global $DB;

        if (empty($eq) && empty($lt) && empty($gt)) {
            return '1';
        }

        $field_types = getTableFields(DISTRIBUTOR_TABLENAME);

        $whereAND = array();
        foreach (array(
            '=' => (is_null($eq)) ? array() : $eq,
            '<' => (is_null($lt)) ? array() : $lt,
            '>' => (is_null($gt)) ? array() : $gt,
        ) as $op => $criteria) {
            foreach ($criteria as $field => $value) {
                if($field == 'category'){
                    if($value > 0)
                    $whereAND[] = $field . " LIKE '%" . str_replace('%', '\%', str_replace('_', '\_', addslashes($value))) . "%'";
                }else{
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
        }

        return (count($whereAND)) ? implode(" AND ", $whereAND) : "1";
    }
}
if (!function_exists('distributorSearch')) {
    /**
     * @param string|null $where
     * @param string|null $order_by
     *
     * @return int[]
     */
    function distributorSearch($where = null, $order_by = null)
    {
        global $DB;

        // Legacy accepts criteria as first parameter
        if (is_array($where)) {
            $where = distributorQuery($where);
        }
        return $DB->query_list(sprintf(
            "SELECT `%s` FROM %s%s%s",
            DISTRIBUTOR_PRIMARYKEY,
            DISTRIBUTOR_TABLENAME,
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
if (!function_exists('distributorSetValue')) {
    /**
     * @param int    $id
     * @param string $field
     * @param mixed  $value
     *
     * @return int
     */
    function distributorSetValue($id, $field, $value)
    {
        global $DB;

        return $DB->modelSet(
            $id,
            $field,
            $value,
            DISTRIBUTOR_TABLENAME,
            DISTRIBUTOR_PRIMARYKEY
        );
    }
}
if (!function_exists('distributorSort')) {
    /**
     * @param string|null $where
     * @param string|null $order_by
     *
     * @return int[]
     */
    function distributorSort($where = null, $order_by = null)
    {
        // Legacy accepts list of id's as first parameter
        if (is_array($where)) {
            $where = distributorQuery(array(
                DISTRIBUTOR_PRIMARYKEY => $where,
            ));
        }

        return distributorSearch($where, $order_by);
    }
}
if (!function_exists('distributorToDatabase')) {
    /**
     * @param mixed[] $item
     *
     * @return int
     */
    function distributorToDatabase(&$item)
    {
        global $DB;

        return $DB->modelSave(
            $item,
            DISTRIBUTOR_TABLENAME,
            DISTRIBUTOR_PRIMARYKEY
        );
    }
}
if (!function_exists('distributorVirtual')) {
    /**
     * @param mixed[] $row
     *
     * @return mixed[]
     */
    function distributorVirtual($row)
    {
        $country = distributorGetOptionList('country_location');
        $row['country'] = $country[$row['country_location']];
        $row['full_address'] = distributorFormatAddress( $row);
        $row['formated_address'] =distributorFormatEmail($row);
        return $row;
    }
}
function distributorFormatAddress($address)
{
    $result = array();

    if(!empty($address['address_1'])){
        $result[] = $address['address_1'];
    }
    $result[] = ltrim(sprintf(
            '%s, %s %s',
            $address['city'],
            $address['state'],
            $address['zip']
    ), ', ');
    $country = distributorGetOptionList('country_location');
    if (!empty($address['country_location'])) {
        $result[] = $country[$address['country_location']];
    }
    
    return implode("\n", array_filter($result, 'boolval'));
}
function distributorFormatEmail($address)
{
    $result = array();
   if (!empty($address['mobile_number'])) {
        $result[] = 'Ph: ' . $address['mobile_number'];
    }
    if (!empty($address['fax_number'])) {
        $result[] = 'Fax: ' . $address['fax_number'];
    }
    if (!empty($address['name'])) {
        $result[] = 'Contact: ' . $address['name']. ' '.$address['surname'];
    }
    if (!empty($address['email_address'])) {
        $result[] = 'Email: ' . $address['email_address'];
    }
    
    return implode("\n", array_filter($result, 'boolval'));
}
if (!function_exists('distributorGetOptionList')) {
    /**
     * @param string $key
     *
     * @return mixed
     */
    function distributorGetOptionList($key)
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

            case 'distributor_type':
                return [
                    'distributor' => 'Distributor',
                    'agent'       => 'Agent',
                ];

            case 'item_image_path':
                return $_SERVER['DOCUMENT_ROOT'] . '/webadmin/uploads/distributor/item_image';
        }

        return null;
    }
}
if (!function_exists('distributorAddComment')) {
    /**
     * @param int $id
     * @param string $comment
     *
     * @return int
     */
    function distributorAddComment($id, $comment)
    {
        $member_id = distributorGetValue($id, 'member_id');

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