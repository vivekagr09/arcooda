<?php
if (!defined('MEMBER_SHIPPINGADDRESS_TABLENAME')) {
    define('MEMBER_SHIPPINGADDRESS_TABLENAME', "`highwaygroup`.`new_members_shipping`");
}
if (!defined('MEMBER_SHIPPINGADDRESS_PRIMARYKEY')) {
    define('MEMBER_SHIPPINGADDRESS_PRIMARYKEY', "id");
}
if (!defined('MEMBER_SHIPPINGADDRESS_SECONDARYKEY')) {
    define('MEMBER_SHIPPINGADDRESS_SECONDARYKEY', "member_id");
}

if (!function_exists('memberShippingAddressFromDatabase')) {
    function memberShippingAddressFromDatabase($shipping_id)
    {
        global $DB;

        if ($row = $DB->query_first(sprintf(
            "SELECT * FROM %s WHERE `id` = %d LIMIT 1",
            MEMBER_SHIPPINGADDRESS_TABLENAME,
            $shipping_id
        ), MYSQLI_ASSOC)) {
            return memberShippingAddressVirtual($row);
        }

        return null;
    }
}
if (!function_exists('memberShippingAddressDelete')) {
    function memberShippingAddressDelete($ship_id)
    {
        global $DB;
        $ship_id = intval($ship_id);
        if (!$ship_id) {
            return false;
        }

        if ($DB->query(sprintf(
            "DELETE FROM %s WHERE `id` = %d",
            MEMBER_SHIPPINGADDRESS_TABLENAME,
            $ship_id
        ))) {
            return true;
        }

        return false;
    }
}
if (!function_exists('memberShippingAddressesFromDatabase')) {
    function memberShippingAddressesFromDatabase($member_id)
    {
        global $DB;

        $results = array();
        if ($query = $DB->query(sprintf(
            "SELECT * FROM %s WHERE `member_id` = %d",
            MEMBER_SHIPPINGADDRESS_TABLENAME,
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
                foreach (getTableFields(MEMBER_SHIPPINGADDRESS_TABLENAME) as $row) {
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

        $DB->query(sprintf(
            "DELETE FROM %s WHERE `member_id` = %d",
            MEMBER_SHIPPINGADDRESS_TABLENAME,
            $member_id
        ));

        $fields = getTableFields(MEMBER_SHIPPINGADDRESS_TABLENAME);
        foreach ($current as $shipping) {
            $insert_vals           = array();
            $shipping['member_id'] = $member_id;
            if (0 == $shipping['id']) {
                unset($shipping['id']);
            }

            foreach ($shipping as $key => $value) {

                if (isset($fields[$key])) {
                    $insert_vals[$key] = formatSQLString($value);
                }
            }
            $insert_vals['deleted_at'] = 'NULL';
            if (count($insert_vals)) {
                $DB->query(sprintf(
                    "INSERT INTO %s (`%s`) VALUES (%s)",
                    MEMBER_SHIPPINGADDRESS_TABLENAME,
                    implode('`, `', array_keys($insert_vals)),
                    implode(", ", array_values($insert_vals))
                ));

            }
        }
    }
}

if (!function_exists('memberShippingAddressSetDefault')) {
    /**
     * @param int $member_id
     * @param int $shipaddr_id
     *
     * @return bool
     */
    function memberShippingAddressSetDefault($member_id, $shipaddr_id)
    {
        global $DB;

        if (!$DB->query_first(sprintf(
            "SELECT 1 FROM %s WHERE `%s` = %d AND `%s` = %d LIMIT 1",
            MEMBER_SHIPPINGADDRESS_TABLENAME,
            "id",
            $shipaddr_id,
            "member_id",
            $member_id
        ), MYSQLI_ASSOC)) {
            return false;
        }

        if ($DB->query(sprintf(
            "UPDATE %s SET `default` = IF(`%s` = %d, 'y', 'n') WHERE `%s` = %d",
            MEMBER_SHIPPINGADDRESS_TABLENAME,
            "id",
            $shipaddr_id,
            "member_id",
            $member_id
        ))) {
            return true;
        }

        return false;
    }
}
if (!function_exists('memberShippingAddressToDatabase')) {
    function memberShippingAddressToDatabase($member_id, &$item)
    {
        global $DB;

        $item['member_id'] = $member_id;

        return $DB->modelSave($item, MEMBER_SHIPPINGADDRESS_TABLENAME, "id");
    }
}
if (!function_exists('memberShippingAddressVirtual')) {
    function memberShippingAddressVirtual($row)
    {
        $row['company']      = $row['company'] ?? $row['company_name'] ?? null;
        $row['company_name'] = $row['company_name'] ?? $row['company'] ?? null;

        $row['zip']       = $row['zip'] ?? $row['post_code'] ?? null;
        $row['post_code'] = $row['post_code'] ?? $row['zip'] ?? null;

        $row['phone_number'] = $row['phone_number'] ?? $row['phone_no'] ?? null;
        $row['phone_no']     = $row['phone_no'] ?? $row['phone_number'] ?? null;

        $row['fax_number'] = $row['fax_number'] ?? $row['fax_no'] ?? null;
        $row['fax_no']     = $row['fax_no'] ?? $row['fax_number'] ?? null;

        $row['mobile_number'] = $row['mobile_number'] ?? $row['mobile_no'] ?? null;
        $row['mobile_no']     = $row['mobile_no'] ?? $row['mobile_number'] ?? null;

        $row['address_2'] = $row['address_2'] ?? '';
        $row['address']   = $row['address'] ?? trim($row['address_1'] . "\n" . $row['address_2']);
        $row['address_1'] = $row['address_1'] ?? $row['address'] ?? '';

        $row['contact'] = $row['contact'] ?? $row['name'] ?? '';
        $row['name']    = $row['name'] ?? $row['contact'] ?? '';

        $row['surname']      = $row['surname'] ?? $row['contact_last'] ?? '';
        $row['contact_last'] = $row['contact_last'] ?? $row['surname'] ?? '';

        $row['address_full'] = memberFormatAddress($row);

        return $row;
    }
}
