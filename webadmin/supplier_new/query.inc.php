<?php
//@todo move to /webadmin/member/...
if (!function_exists('memberPreferencesToDatabase')) {
    /**
     * @deprecated Does not belong here, or wrong name
     */
    function memberPreferencesToDatabase($id,$prefs)
    {
        global $_GLOBALS;
        global $DB;

        if (!is_array($prefs)) {
            return array();
        }
        $closeout     = ('y' == $prefs['closeout']) ? 'y' : 'n';
        $weekly       = ('y' == $prefs['weekly']) ? 'y' : 'n';
        $customer     = ('y' == $prefs['customer']) ? 'y' : 'n';
        $row = $DB->query_first(sprintf(
            "SELECT * FROM `highwaygroup`.`new_members_mailing_list` WHERE `member_id` = %d LIMIT 1",
            $id
        ));
        if (!$row) {
            if ('y' == $weekly || 'y' == $closeout || 'y' == $customer) {
                $DB->query(sprintf(
                    "INSERT INTO `highwaygroup`.`new_members_mailing_list` (`member_id`, `weekly`, `closeout`, `customer`, `created_by`) VALUES (%d, %s, %s, %s, %d)",
                    $id,
                    formatSQLString($weekly),
                    formatSQLString($closeout),
                    formatSQLString($customer),
                    $_GLOBALS['staff']['id']
                ));
            }
        } elseif ($row['weekly'] != $weekly || $row['closeout'] != $closeout || $row['customer'] != $customer) {
            $DB->query(sprintf(
                "UPDATE `highwaygroup`.`new_members_mailing_list` SET `weekly` = %s, `closeout` = %s, `customer` = %s, `updated_by` = %d WHERE `member_id` = %d LIMIT 1",
                formatSQLString($weekly),
                formatSQLString($closeout),
                formatSQLString($customer),
                $_GLOBALS['staff']['id'],
                $id
            ));
        }
        if($prefs[3]['new_password']==$prefs[3]['confirm_password']){
             $update_password = "UPDATE `highwaygroup`.`new_members_logins` SET password='".md5($prefs[3]['new_password'])."' WHERE member_id=$id";
             $DB->query($update_password);
        }
        $closeout     = ('y' == $prefs['closeout']) ? 1 : 0;
        $weekly       = ('y' == $prefs['weekly']) ? 1 : 0;
        $customer     = ('y' == $prefs['customer']) ? 1 : 0;
        $mem_opts_sql = "UPDATE `highwaygroup`.`new_members_options` SET currency='{$prefs['currency']}', lang='en', per_page='{$prefs['num_products']}', mail_weekly='$weekly', mail_closeout='$closeout', mail_customer='$customer', password='' WHERE member_id='$id'";
        $DB->query($mem_opts_sql);
        return $DB->affected_rows();
    }
}
if (!function_exists('memberRequestsToDatabase')) {
    /**
     * @deprecated Does not belong here, or wrong name
     */
    function memberRequestsToDatabase($member_id, $requests)
    {
        global $DB;

        $member_id = intval($member_id);
        if (0 == $member_id) {
            return;
        }

        $sql = "DELETE FROM `highwaygroup`.`new_members_requests` WHERE `member_id` = " . $member_id;
        $DB->query($sql);

        $fields = getTableFields('`highwaygroup`.`new_members_requests`');

        foreach ($requests as $request) {
            $insert_vals          = array();
            $request['member_id'] = $member_id;
            if (0 == $request['id']) {
                unset($request['id']);
            }

            foreach ($request as $key => $value) {
                if (isset($fields[$key])) {
                    $insert_vals[$key] = formatSQLString($value);
                }
            }

            $sql = "INSERT INTO `member_requests` (`" . implode('`, `', array_keys($insert_vals)) . "`) VALUES (" . implode(", ", $insert_vals) . ")";
            if (count($insert_vals)) {
                $DB->query($sql);
            }

        }
    }
}

//@todo This is a contractor function, not member
if (!function_exists('memberJobFromDatabase')) {
    /**
     * @deprecated Does not belong here, or wrong name
     */
    function memberJobFromDatabase($id)
    {
        global $DB;
        return $DB->query_first(sprintf(
            "SELECT * FROM `highwaygroup`.`new_members_jobs` AS `mj` WHERE `mj`.`id` = %d LIMIT 1",
            $id
        ), MYSQLI_ASSOC);
    }
}

if (!function_exists('supplierGenericDelete')) {
    function supplierGenericDelete($id, $table, $primary_key = 'id')
    {
        global $DB;
        $DB->query(sprintf(
            "UPDATE %s SET `deleted_at` = NOW() WHERE `deleted_at` IS NULL AND `%s` = %d LIMIT 1",
            //"DELETE FROM %s WHERE `%s` = %d LIMIT 1",
            $table,
            $primary_key,
            $id
        ));
        return $DB->affected_rows();
    }
}
if (!function_exists('supplierGenericToDatabase')) {
    function supplierGenericToDatabase(&$item, $table, $primary_key = 'id')
    {
        global $DB, $_GLOBALS;
        $id       = intval($item[$primary_key]);
        $fields   = getTableFields($table);
        $old_vals = $DB->query_first(sprintf(
            "SELECT * FROM %s WHERE `deleted_at` IS NULL AND `%s` = %d LIMIT 1",
            $table,
            $primary_key,
            $id
        ), MYSQLI_ASSOC);
        if ($old_vals) {
            $set_vals = array();
            foreach ($fields as $k => $v) {
                if (array_key_exists($k, $item) && $item[$k] !== $old_vals[$k]) {
                    if (is_array($item[$k])) {
                        $set_vals[$k] = "`" . $k . "` = " . formatSQLString(implode(",", $item[$k]));
                    } elseif (isset($item[$k])) {
                        $set_vals[$k] = "`" . $k . "` = " . formatSQLString($item[$k]);
                    } else {
                        $set_vals[$k] = "`" . $k . "` = NULL";
                    }
                }
            }
            if (count($set_vals)) {
                $item['updated_by']     = $_GLOBALS['staff']['id'];
                $set_vals['updated_by'] = "`updated_by` = " . intval($item['updated_by']);
                $DB->query(sprintf(
                    "UPDATE %s SET %s WHERE `deleted_at` IS NULL AND `%s` = %d LIMIT 1",
                    $table,
                    implode(', ', $set_vals),
                    $primary_key,
                    $id
                ));
            }
        } else {
            $item['created_by'] = $_GLOBALS['staff']['id'];
            $insert_vals        = array();
            foreach ($fields as $k => $v) {
                if (array_key_exists($k, $item)) {
                    if (is_array($item[$k])) {
                        $insert_vals[$k] = formatSQLString(implode(",", $item[$k]));
                    } elseif (isset($item[$k])) {
                        $insert_vals[$k] = formatSQLString($item[$k]);
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
                $item[$primary_key] = $id = $DB->insert_id();
            }
        }
        return $id;
    }
}
if (!function_exists('supplierGenericsFromDatabase')) {
    function supplierGenericsFromDatabase($id, $table, $primary_key = 'id', $secondary_key = 'supplier_id')
    {
        global $DB;
        $result = array();
        $query  = $DB->query(sprintf(
            "SELECT * FROM %s WHERE `deleted_at` IS NULL AND `%s` = %d",
            $table,
            $secondary_key,
            $id
        ));
        while ($row = $DB->fetch_assoc($query)) {
            $row['created_time'] = (is_null($row['created_at'])) ? null : strtotime($row['created_at']);
            $row['updated_time'] = (is_null($row['updated_at'])) ? null : strtotime($row['updated_at']);
            if (is_null($primary_key)) {
                $result[] = $row;
            } else {
                $result[$row[$primary_key]] = $row;
            }
        }
        $DB->free_result($query);
        return $result;
    }
}
if (!function_exists('supplierGenericsToDatabase')) {
    function supplierGenericsToDatabase($id, &$items, $table, $primary_key = 'id', $secondary_key = 'supplier_id')
    {
        foreach ($items as &$v) {
            $v[$secondary_key] = $id;
            supplierGenericToDatabase($v, $table, $primary_key);
        }
    }
}
if (!function_exists('supplierContactDelete')) {
    function supplierContactDelete($id)
    {
        return supplierGenericDelete($id, '`new_suppliers_contacts`');
    }
}
if (!function_exists('getsupplieritemfullhistorynew')) {
    function getsupplieritemfullhistorynew($supplier_id,$products_id,$limit='limit 1')
    {
        global $DB;
        $results = $DB->query_first(sprintf(
            "SELECT * FROM `supplier_product_history` WHERE `product_id` = %d AND `supplier_id` = %d ORDER BY created_at DESC %s ",
            $products_id,
            $supplier_id,
            $limit
        ));
        return $results;
    }
}
if (!function_exists('OtherSupplierForProduct')) {
    function OtherSupplierForProduct($supplier_id,$products_id)
    {
        global $DB;
           $sql="SELECT count(*) as total from supplier_product where `supplier_id`!=$supplier_id AND product_id=$products_id AND deleted_at IS NULL LIMIT 1";
            $data = $DB->query_first($sql, MYSQLI_ASSOC);
            if($data){
                return $data['total'];
            }else{
                return 0;
            }
    }
}
if (!function_exists('totalSupplierStockFromDatabase')) {
    function totalSupplierStockFromDatabase($id)
    {
        global $DB;
        $result=0;
        $row = $DB->query_first(sprintf(
                "SELECT COUNT(*) AS `total` FROM `supplier_product` WHERE supplier_id=%d AND deleted_at IS NULL ",
                $id
            ), MYSQLI_ASSOC);
            $result = $row['total'];
        return $result;
    }
}
if (!function_exists('totalSupplierInventoryFromDatabase')) {
    function totalSupplierInventoryFromDatabase($id)
    {
        global $DB;
        $result=array();
        $query  = $DB->query(sprintf(
            "SELECT `product_id` from `products_inventory` where supplier_id=%d AND deleted_at IS NULL",
            $id
        ));
        while ($row = $DB->fetch_assoc($query)) {
            $result[]=$row['product_id'];
        }
        $query  = $DB->query(sprintf(
            "SELECT `product_id` from `purchase_order_products` where product_supplier_id=%d ",
            $id
        ));
        while ($row = $DB->fetch_assoc($query)) {
            $result[]=$row['product_id'];
        }
       $DB->free_result($query);
       return array_unique($result);
    }
}
if (!function_exists('supplierStockFromDatabase')) {
    function supplierStockFromDatabase($id,$order_by='id',$order='asc',$discontinued =false)
    {
        $subsql='';
        if($discontinued){
            $subsql=" AND sp.discontinued='n' ";
        }
        global $DB;
        $result = array();
        $query  = $DB->query(sprintf(
            "SELECT  p.id as pro_id,p.sales_price,p.active,p.product_name,sp.*,CONCAT(s.name, ' ' , s.surname) as supplier_name,sp.sup_name as supplier_product_name, IF(p.price_flux='y',(p.markup+100.0)*p.cost_price/100.0,p.sales_price) AS offer_price, IF(p.price_flux='y',p.cost_currency,p.sales_currency) AS offer_currency,pt.name as product_subtype, pt.parent as product_roottype FROM products p INNER JOIN product_types pt ON p.type=pt.id INNER JOIN supplier_product sp ON sp.product_id=p.id INNER JOIN new_suppliers s ON s.supplier_id=sp.supplier_id WHERE sp.supplier_id=%d AND sp.deleted_at IS NULL %s ORDER BY %s %s",
            $id,
            $subsql,
           $order_by,
           $order,

        ));
        while ($row = $DB->fetch_assoc($query)) {
            $row['created_time'] = (is_null($row['created_at'])) ? null : strtotime($row['created_at']);
            $row['updated_time'] = (is_null($row['updated_at'])) ? null : strtotime($row['updated_at']);
                $result[] = $row;
            
        }
        $DB->free_result($query);
        return $result;
    }
}
if (!function_exists('supplierContactsFromDatabase')) {
    function supplierContactsFromDatabase($id)
    {
        $result = supplierGenericsFromDatabase($id, '`new_suppliers_contacts`');
        uasort($result, function ($a, $b) {
            return strcmp($b['main'], $a['main']) ?: strcmp($b['send_email_cc'], $a['send_email_cc']) ?: ($b['id'] - $a['id']);
        });
        return $result;
    }
}
if (!function_exists('supplierContactsFromPost')) {
    function supplierContactsFromPost($posted)
    {
        $supplier_id = $posted['supplier_id'];

        $affected  = explode(',', $posted['supplier_contacts_affected']);
        $modified  = array();
        if (is_array($posted['supplier_contacts'])) {
            foreach ($posted['supplier_contacts'] as $contact) {
                $modified[] = $contact['id'];
            }
        }
        $old_contacts = supplierContactsFromDatabase($supplier_id);
        $new_contacts = array();
        foreach ($old_contacts as $contact) {
            if (!in_array($contact['id'], $affected) || in_array($contact['id'], $modified)) {
                $new_contacts[$contact['id']] = $contact;
            }
        }
        if (is_array($posted['supplier_contacts'])) {
            foreach ($posted['supplier_contacts'] as $contact) {
                if (0 != $contact['id']) {
                    foreach ($contact as $key => $value) {
                        $new_contacts[$contact['id']][$key] = $value;
                    }
                } else {
                    $new_contacts[] = $contact;
                }
            }
        }
        return $new_contacts;
    }
}
if (!function_exists('supplierContactsToDatabase')) {
    function supplierContactsToDatabase($id, &$items)
    {
        return supplierGenericsToDatabase($id, $items, '`new_suppliers_contacts`');
    }
}
if (!function_exists('supplierShippingAddressDelete')) {
    function supplierShippingAddressDelete($id)
    {
        return supplierGenericDelete($id, '`new_supplier_address`', 'id');
    }
}
if (!function_exists('supplierBankAddressDelete')) {
    function supplierBankAddressDelete($id)
    {
        return supplierGenericDelete($id, '`new_supplier_bank_detail`', 'id');
    }
}

if (!function_exists('supplierBankAddressesFromDatabase')) {
    function supplierBankAddressesFromDatabase($id)
    {
        return supplierGenericsFromDatabase($id, '`new_supplier_bank_detail`', 'id');
    }
}
if (!function_exists('supplierShippingAddressesFromDatabase')) {
    function supplierShippingAddressesFromDatabase($id)
    {
        $result = supplierGenericsFromDatabase($id, '`new_supplier_address`', 'id');
        foreach ($result as &$row) {
            foreach (array(
                'id'           => 'id',
                'company_name' => 'company',
                'post_code'    => 'zip',
                'phone_no'     => 'phone_number',
                'fax_no'       => 'fax_number',
                'mobile_no'    => 'mobile_number',
            ) as $k => $v) {
                $row[$k] = $row[$v];
            }
            $row['address']      = trim($row['address_1'] . "\n" . $row['address_2']);
            $row['contact']      = trim($row['name'] . ' ' . $row['surname']);
            $row['address_full'] = supplierFormatAddress($row);
        }
        uasort($result, function ($a, $b) {
            return strcmp($b['default'], $a['default']) ?: strcmp($b['name'], $a['name']) ?: strcmp($b['surname'], $a['surname']) ?: strcmp($b['company'], $a['company']) ?: ($b['id'] - $a['id']);
        });
        return $result;
    }
}
if (!function_exists('supplierBankAddressesFromPost')) {
    function supplierBankAddressesFromPost($posted)
    {
        $supplier_id = intval($posted['supplier']['supplier_id']);
        $affected  = explode(',', $posted['supplier_bank_affected']);
        $modified  = array();
        if (is_array($posted['supplier_bank'])) {
            foreach ($posted['supplier_bank'] as $shipping) {
                $modified[] = $shipping['id'];
            }
        }
        $old_shipping = supplierBankFromDatabase($supplier_id);
        $new_shipping = array();
        foreach ($old_shipping as $shipping) {
            if (!in_array($shipping['id'], $affected) || in_array($shipping['id'], $modified)) {
                $new_shipping[$shipping['id']] = $shipping;
            }
        }
        if (is_array($posted['supplier_bank'])) {
            foreach ($posted['supplier_bank'] as $shipping) {
                if (0 != $shipping['id']) {
                    foreach ($shipping as $key => $value) {
                        $new_shipping[$shipping['id']][$key] = $value;
                    }
                } else {
                    $new_shipping[] = $shipping;
                }
            }
        }
        return $new_shipping;
    }
}
if (!function_exists('supplierShippingAddressesFromPost')) {
    function supplierShippingAddressesFromPost($posted)
    {
        $supplier_id = intval($posted['supplier']['supplier_id']);
        $affected  = explode(',', $posted['supplier_shipping_affected']);
        $modified  = array();
        if (is_array($posted['supplier_shipping'])) {
            foreach ($posted['supplier_shipping'] as $shipping) {
                $modified[] = $shipping['id'];
            }
        }
        $old_shipping = supplierShippingFromDatabase($supplier_id);
        $new_shipping = array();
        foreach ($old_shipping as $shipping) {
            if (!in_array($shipping['id'], $affected) || in_array($shipping['id'], $modified)) {
                $new_shipping[$shipping['id']] = $shipping;
            }
        }
        if (is_array($posted['supplier_shipping'])) {
            foreach ($posted['supplier_shipping'] as $shipping) {
                $shipping['default'] = ($shipping['default'] == 'y') ? 'y':'n';
                if (0 != $shipping['id']) {
                    foreach ($shipping as $key => $value) {
                        $new_shipping[$shipping['id']][$key] = $value;
                    }
                } else {
                    $new_shipping[] = $shipping;
                }
            }
        }
        return $new_shipping;
    }
}
if (!function_exists('supplierShippingAddressesToDatabase')) {
    function supplierShippingAddressesToDatabase($id, &$items)
    {
        return supplierGenericsToDatabase($id, $items, '`new_supplier_address`', 'id');
    }
}
if (!function_exists('supplierBankAddressesToDatabase')) {
    function supplierBankAddressesToDatabase($id, &$items)
    {
        return supplierGenericsToDatabase($id, $items, '`new_supplier_bank_detail`', 'id');
    }
}
if (!function_exists('supplierAttachmentDelete')) {
    function supplierAttachmentDelete($id)
    {
        global $DB;
        $DB->query(sprintf(
            "DELETE FROM `new_suppliers_attachments` WHERE `id` = %d LIMIT 1",
            $id
        ));
    }
}
if (!function_exists('supplierAttachmentFromDatabase')) {
    function supplierAttachmentFromDatabase($id)
    {
        global $DB;
        $row = $DB->query_first(sprintf(
            "SELECT *, UNIX_TIMESTAMP(`ma`.`date`) AS `time_added` FROM `new_suppliers_attachments` AS `ma` WHERE `ma`.`id` = %d LIMIT 1",
            $id
        ), MYSQLI_ASSOC);
        if ($row) {
            $ext = end(explode('.', $row['filename']));
            if (is_file(supplierGetOptionList('attachment_path') . '/' . $row['filename'])) {
                $row['thumb_url'] = sprintf(
                    '/webadmin/supplier_new/images/%d_%d_sml.%s',
                    $id, $row['supplier_id'], $ext
                );
                $row['image_url'] = sprintf(
                    '/webadmin/supplier_new/images/%d_%d.%s',
                    $id, $row['supplier_id'], $ext
                );
            }
            $row['date_str'] = date('d/m/Y', $row['time_added']);
        }
        return $row;
    }
}
if (!function_exists('supplierAttachmentsFromDatabase')) {
    function supplierAttachmentsFromDatabase($id)
    {
        global $DB;
        $results = array();
        $query   = $DB->query(sprintf(
            "SELECT `ma`.`id` FROM `new_suppliers_attachments` AS `ma` WHERE `ma`.`supplier_id` = %d",
            $id
        ));
        while ($row = $DB->fetch_assoc($query)) {
            $results[] = $row['id'];
        }
        $DB->free_result($query);
        return $results;
    }
}
if (!function_exists('supplierCommentFromDatabase')) {
    function supplierCommentFromDatabase($id)
    {
        global $DB;
        return $DB->query_first(sprintf(
            "SELECT *, UNIX_TIMESTAMP(`mc`.`comment_date`) AS `comment_time` FROM `new_supplier_comments` AS `mc` WHERE `mc`.`id` = %d LIMIT 1",
            $id
        ), MYSQLI_ASSOC);
    }
}
if (!function_exists('supplierCommentSearch')) {
    function supplierCommentSearch($criteria, $min_time = 0, $max_time = 0)
    {
        global $DB;
        $field_types = getTableFields('`new_supplier_comments`');
        $whereAND    = array();
        if (is_array($criteria)) {
            foreach ($criteria as $field => $value) {
                if (isset($field_types[$field])) {
                    if (is_array($value)) {
                        if (count($value) > 0) {
                            $whereOR = array();
                            if (substr($field_types[$field]['Type'], 0, 4) == 'set(') {
                                foreach ($value as $sub_value) {
                                    $whereOR[] = "	FIND_IN_SET(" . formatSQLString($sub_value) . "," . $field . ")";
                                }

                            } else {
                                foreach ($value as $sub_value) {
                                    $whereOR[] = "	" . $field . "=" . formatSQLString($sub_value);
                                }

                            }
                            $whereAND[] = "	(" . implode(" OR ", $whereOR) . ")";
                        } else {
                            return array();
                        }
                    } else {
                        if (substr($field_types[$field]['Type'], 0, 4) == 'set(') {
                            $whereAND[] = "	FIND_IN_SET(" . formatSQLString($value) . "," . $field . ")";
                        } else {
                            $whereAND[] = "	" . $field . "=" . formatSQLString($value);
                        }
                    }
                    unset($field);
                }
            }

        } elseif ('' != $criteria) {
            $whereAND[] = '`mc`.`supplier_id_id` = ' . intval($criteria);
        }

        if (0 != $min_time) {
            $whereAND[] = 'UNIX_TIMESTAMP(`mc`.`comment_date`) > ' . $min_time;
        }

        if (0 != $max_time) {
            $whereAND[] = 'UNIX_TIMESTAMP(`mc`.`comment_date`) < ' . $max_time;
        }

        $results = array();
        $query   = $DB->query(sprintf(
            "SELECT `mc`.`id` FROM `new_supplier_comments` AS `mc`%s GROUP BY `mc`.`id` ORDER BY `mc`.`id` DESC",
            (count($whereAND)) ? (' WHERE ' . implode(' AND ', $whereAND)) : ''
        ));
        while ($row = $DB->fetch_assoc($query)) {
            $results[] = $row['id'];
        }
        $DB->free_result($query);
        return $results;
    }
}
if (!function_exists('supplierCommentToDatabase')) {
    function supplierCommentToDatabase($supplier_id, $comment, $type = 'admin')
    {
        global $DB, $_GLOBALS;
        $title = $sentto = '';
        if (is_array($comment)) {
            $title   = $comment['comment_title'];
            $sentto  = $comment['comment_sentto'];
            $comment = $comment['comment_text'];
        }
        $DB->query(sprintf(
            "INSERT INTO `new_supplier_comments` (`supplier_id`, `staff_id`, `comment_type`, `comment_date`, `comment_text`, `comment_title`, `comment_sentto`) VALUES (%d, %d, %s, NOW(), %s, %s, %s)",
            $supplier_id,
            $_GLOBALS['staff']['staff_id'],
            formatSQLString($type),
            formatSQLString($comment),
            formatSQLString($title),
            formatSQLString($sentto)
        ));
        return $DB->insert_id();
    }
}
if (!function_exists('supplierEmailTemplatesList')) {
    function supplierEmailTemplatesList()
    {
        global $DB;

        $result = array();
        if ($query = $DB->query(sprintf(
            "SELECT `et`.`template_id`, `et`.`template_name`, `et`.`template_subject`, `et`.`template_text` FROM `email_templates` AS `et` WHERE FIND_IN_SET('suppliers', `et`.`template_group`)"
        ))) {
            while ($row = $DB->fetch_assoc($query)) {
                $result[$row['template_id']] = $row;
            }
            $DB->free_result($query);
        }

        return $result;
    }
}
if (!function_exists('supplierExists')) {
    function supplierExists($supplier_id)
    {
        static $results = array();
        if (array_key_exists($supplier_id, $results)) {
            return $results[$supplier_id];
        }

        global $DB;
        $row = $DB->query_first("SELECT `s`.`supplier_id` FROM `new_suppliers` AS `s` WHERE `s`.`supplier_id` = " . intval($supplier_id) . " LIMIT 1", MYSQLI_ASSOC);
        if ($row) {
            return $results[$supplier_id] = true;
        }

        return false;
    }
}
if (!function_exists('supplierFormatAddress')) {
    function supplierFormatAddress($supplier)
    {
        $country_list = supplierGetOptionList('country');
        $address      = array();
        if (isset($supplier['company'])) {
            $address[] = $supplier['company'];
        }

        if (isset($supplier['address_1'])) {
            $address[] = $supplier['address_1'];
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

        if (isset($country_list[$supplier['country']])) {
            $address[] = $country_list[$supplier['country']];
        }

        $address[] = '';
        if (isset($supplier['phone_no']) && '' != $supplier['phone_no']) {
            $address[] = "\nPh: " . $supplier['phone_no'];
        }

        return implode("\n", $address);
    }
}
if (!function_exists('supplierFormatAddressPrint')) {
    function supplierFormatAddressPrint($supplier)
    {
        $country_list = supplierGetOptionList('country');
        $address      = array();

        if (isset($supplier['address_1'])) {
            $address[] = $supplier['address_1'];
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

        if (isset($country_list[$supplier['country']])) {
            $address[] = $country_list[$supplier['country']];
        }
        return implode("\n", $address);
    }
}
if (!function_exists('supplierFormatNumber')) {
    function supplierFormatNumber($supplier_id)
    {
        return sprintf('SU#%06d', $supplier_id);
    }
}
if (!function_exists('supplierComplete')) {
    function supplierComplete($supplier)
    {
        $supplier['supplier_number'] = supplierFormatNumber($supplier['supplier_id']);
        $supplier['address_full']  = supplierFormatAddress($supplier);
        return $supplier;
    }
}
if (!function_exists('supplierListFromDatabase')) {
    function supplierListFromDatabase($supplier_list)
    {
        global $DB;
        $result = array();
        $fields = array('*' => '`s`.*');
        foreach (supplierGetOptionList('ext_fields') as $k => $v) {
            $fields[$k] = $v . " AS `" . $k . "`";
        }
        $query = $DB->query("SELECT " . implode(", ", $fields) . " FROM `new_suppliers` AS `s` WHERE " . supplierSQLList($supplier_list));
        while ($row = $DB->fetch_assoc($query)) {
            $result[$row['supplier_id']] = supplierComplete($row);
        }
        $DB->free_result($query);
        return $result;
    }
}
if (!function_exists('supplierFromDatabase')) {
    function supplierFromDatabase($supplier_id)
    {
        global $DB;
        if (supplierExists($supplier_id)) {
            $fields = array('*' => 's.*');
            foreach (supplierGetOptionList('ext_fields') as $key => $value) {
                $fields[$key] = $value . " AS " . $key;
            }
            $supplier = $DB->query_first("SELECT " . implode(", ", $fields) . " FROM `new_suppliers` AS `s` WHERE `s`.`supplier_id` = " . intval($supplier_id) . " LIMIT 1", MYSQLI_ASSOC);

            $supplier = supplierComplete($supplier);

            return $supplier;
        } else {
            $result = array();
            foreach (supplierGetOptionList('all_fields_types') as $key => $row) {
                $result[$key] = $row[4];
            }

            return $result;
        }
    }
}
if (!function_exists('supplierLastPurchase')) {
    function supplierLastPurchase($supplier_id,$criteria)
    {
        global $DB;
        $status="'" . implode ( "', '", $criteria ) . "'";
        if (supplierExists($supplier_id)) {
            $sql="SELECT open_date from purchase_order where `supplier_id`=$supplier_id AND order_status IN (".$status.")  ORDER BY open_date DESC LIMIT 1";
            $data = $DB->query_first($sql, MYSQLI_ASSOC);
            if($data){
                return date('d/m/Y',strtotime($data['open_date']));
            }else{
                return 'N/A';
            }
        } else {
            return  'N/A';
        }
    }
}
if (!function_exists('supplierTotalPurchase')) {
    function supplierTotalPurchase($supplier_id,$criteria)
    {
        $status="'" . implode ( "', '", $criteria ) . "'";

        global $DB;
        if (supplierExists($supplier_id)) {
            $sql="SELECT count(*) as total_purchase from purchase_order where `supplier_id`=$supplier_id AND order_status IN (".$status.") LIMIT 1";
            $data = $DB->query_first($sql, MYSQLI_ASSOC);
            if($data){
                return $data['total_purchase'];
            }else{
                return 0;
            }
        } else {
            return  0;
        }
    }
}
if (!function_exists('supplierPurchaseTotal')) {
    function supplierPurchaseTotal($supplier_id,$criteria)
    {
        $status="'" . implode ( "', '", $criteria ) . "'";

        global $DB;
        if (supplierExists($supplier_id)) {
            $sql="SELECT sum(total_invoice_cost) as total_purchase from purchase_order where `supplier_id`=$supplier_id AND order_status IN (".$status.") LIMIT 1";
            $data = $DB->query_first($sql, MYSQLI_ASSOC);
            if($data){
                return $data['total_purchase'];
            }else{
                return 0;
            }
        } else {
            return  0;
        }
    }
}
if (!function_exists('supplierFromPost')) {
    function supplierFromPost($posted)
    {
        global $DB;
        $supplier_id           = $posted['supplier_id'];
        $result              = supplierFromDatabase($supplier_id);
        $result['supplier_id'] = intval($supplier_id);

        foreach ($posted as $key => $value) {
            if (is_array($value)) {
                $result[$key] = implode(',', $value);
            } else {
                $result[$key] = $value;
            }
        }

        if ($supplier_id <= 0) {
            unset($result['supplier_id']);
            $result['created_time'] = time();
            $result['created']      = formatDateTime();
        }

        $nullHack = array(
            "site_id",
            "supplier_type",
            "phone_no2"
        );

        foreach ($nullHack as $hack) {
            if (!isset($result[$hack]) || null == $result[$hack]) {
                $result[$hack] = "";
            }
        }

        return $result;
    }
}
if (!function_exists('supplierGetLock')) {
    function supplierGetLock($id)
    {
        global $DB;
        $row = $DB->query_first("SELECT *, UNIX_TIMESTAMP(`gl`.`expiry_date`) AS `expiry_time` FROM `generic_locks` AS `gl` WHERE `gl`.`section_name` = 'supplier'  LIMIT 1", MYSQLI_ASSOC);
        /*if ($row['expiry_time'] < time()) {
            $row = false;
        }*/

        return $row;
    }
}
if (!function_exists('supplierGetOptionList')) {
    function supplierGetOptionList($table_name)
    {
        global $DB;
        $table       = '';
        $option_list = array();
        switch ($table_name) {
            case 'id':
                $table = 'new_suppliers';
                $key   = 'supplier_id';
                $value = "CONCAT_WS(' ',name,surname)";
                break;

            case 'currency':return getCurrencyList();
            case 'country':return getCountryList(false);
            case 'check_staff_id':
            case 'staff_id':return getStaffList();

            case 'survey_type':

            case 'site_id':
                $table = '`highwaygroup`.`websites`';
                $key   = 'id';
                $value = 'website';
                break;

            case 'supplier_type':
                return array(
                    '3' => 'Coinop Express',
                );
                break;

            case 'attachment_path':
                return $_SERVER['DOCUMENT_ROOT'] . '/webadmin/uploads/supplier/attachment';

            case 'joined_fields':
                $joins = array(
                    'LEFT JOIN `new_suppliers_contacts` AS `mc` ON `mc`.`supplier_id` = `s`.`supplier_id`' => array(
                        'contact_title' => '`mc`.`title`',
                        'contact_name'  => '`mc`.`name`',
                        'contact_phone' => '`mc`.`phone`',
                        'contact_email' => '`mc`.`email`',
                    ),
                );
                return $joins;

            case 'all_fields':
                return array_merge(supplierGetOptionList('base_fields'), supplierGetOptionList('ext_fields'));
            case 'all_fields_types':
                return array_merge(supplierGetOptionList('base_fields_types'), supplierGetOptionList('ext_fields_types'));
            case 'base_fields':
                $fields = array();
                foreach (getTableColumns('`new_suppliers`') as $key) {
                    $fields[$key] = 's.`' . $key . '`';
                }

                return $fields;
            case 'base_fields_types':
                return getTableFields('`new_suppliers`');
            case 'ext_fields_types':
                return array();
            case 'extra_fields':
            case 'ext_fields':
                return array(
                    'id'             => 'supplier_id',
                    'address'        => 'address_1',
                    'company_name'   => 'company',
                    'fax_no'         => 'fax_number',
                    'mobile_no'      => 'mobile_number',
                    'first_name'     => 'name',
                    'phone_no'       => 'phone_number',
                    'last_name'      => 'surname',
                    'website'        => 'web_page_url',
                    'post_code'      => 'post_code',
                    'created_time'   => "UNIX_TIMESTAMP(`s`.`created`)",
                    'modified_time'  => "UNIX_TIMESTAMP(`s`.`modified`)",
                    'last_initial'   => 'SUBSTRING(s.surname,1,1)',
                    'full_name'      => "CONCAT_WS(' ',s.name,s.surname)",
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
if (!function_exists('supplierJobToDatabase')) {
    function supplierJobToDatabase($supplier_id, $content)
    {
        global $DB;
        echo sprintf(
            "INSERT INTO `suppliers_jobs` (`supplier_id`, `content`, `created`) VALUES (%d, %s, NOW())",
            $supplier_id,
            $DB->escape($content)
        ); die;
        $DB->query(sprintf(
            "INSERT INTO `suppliers_jobs` (`supplier_id`, `content`, `created`) VALUES (%d, %s, NOW())",
            $supplier_id,
            $DB->escape($content)
        ));
    }
}
if (!function_exists('supplierJobsFromDatabase')) {
    function supplierJobsFromDatabase($supplier_id)
    {
        global $DB;
        $results = array();
        $query   = $DB->query(sprintf(
            "SELECT `mj`.`id` FROM `new_suppliers_jobs` AS `mj` WHERE `mj`.`supplier_id` = %d ORDER BY `mj`.`id` ASC",
            $supplier_id
        ));
        while ($row = $DB->fetch_assoc($query)) {
            $results[] = $row['id'];
        }
        $DB->free_result($query);
        return $results;
    }
}
if (!function_exists('supplierRequestsFromDatabase')) {
    function supplierRequestsFromDatabase($supplier_id)
    {
        global $DB;
        $results = array();
        $query   = $DB->query("SELECT * FROM `new_suppliers_requests` AS `mr` WHERE `mr`.`supplier_id` = " . intval($supplier_id) . " ORDER BY `mr`.`date` DESC, `mr`.`id` DESC");
        while ($row = $DB->fetch_assoc($query)) {
            $results[] = $row;
        }

        $DB->free_result($query);
        return $results;
    }
}
if (!function_exists('supplierSearch')) {
    function supplierSearch($criteria = '')
    {
        global $DB;
        $results = array();

        $joins  = array();
        $fields = supplierGetOptionList('all_fields');
        $whereAND = array();
        if (is_array($criteria)) {
            foreach ($criteria as $field => $value) {
                if (array_key_exists($field, $fields)) {
                    if (is_array($value)) {
                        if (count($value)) {
                            if (false) {

                            } else {
                                $whereOR = array();
                                foreach ($value as $sub_value) {
                                    $whereOR[] = $fields[$field] . " = " . formatSQLString($sub_value);
                                }
                                $whereAND[] = "(" . implode(" OR ", $whereOR) . ")";
                            }
                        } else {
                            return array();
                        }
                    } else {
                        $whereAND[] = $fields[$field] . " = " . formatSQLString($value);
                    }
                    unset($fields[$field]);
                }
            }
        } elseif (is_string($criteria) && '' != $criteria) {
            $criteria = array('any_text' => $criteria);
        }
        $search_for = array();
        if (array_key_exists('any_text', $criteria)) {
            if (is_array($criteria['any_text'])) {
                $search_for = $criteria['any_text'];
            } elseif (is_string($criteria['any_text'])) {
                $search_for = explode(' ', $criteria['any_text']);
            }
        }

        if (count($search_for)) {
            $searchBits = array('`s`.`supplier_id`', '`s`.`company`', '`s`.`name`', '`s`.`surname`', '`s`.`email_address`');

            $joins[]    = " LEFT JOIN `new_suppliers_contacts` AS `mc` ON `mc`.`supplier_id` = `s`.`supplier_id`";
            $searchBits = array_merge($searchBits, array('`mc`.`title`', '`mc`.`name`', '`mc`.`phone`', '`mc`.`email`'));

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
        $query = $DB->query(sprintf(
            "SELECT `s`.`supplier_id` FROM `new_suppliers` AS `s`%s%s GROUP BY `s`.`supplier_id` ORDER BY `s`.`supplier_id` ASC",
            (count($joins)) ? (" " . implode(" ", $joins)) : "",
            (count($whereAND)) ? (" WHERE " . implode(" AND ", $whereAND)) : ""
        ));
        while ($row = $DB->fetch_assoc($query)) {
            $results[] = $row['supplier_id'];
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
if (!function_exists('supplierSort')) {

    function supplierSort($item_list, $order_by = 'supplier_id')
    {
        global $DB;
        if (!is_array($item_list) || count($item_list) < 2) {
            return $item_list;
        }

        $fields = supplierGetOptionList('all_fields');
        if (!isset($fields[$order_by])) {
            return $item_list;
        }

        $results = array();
        $query   = $DB->query(sprintf(
            "SELECT `m`.`supplier_id` FROM `new_suppliers` AS `m` WHERE `m`.`supplier_id` IN(" . implode(', ', $item_list) . ")%s",
            (isset($fields[$order_by])) ? (" ORDER BY " . $fields[$order_by] . " ASC") : ""
        ));
        while ($row = $DB->fetch_assoc($query)) {
            $results[] = $row['supplier_id'];
        }
        $DB->free_result($query);
        return $results;
    }
}
if (!function_exists("supplierSQLList")) {
    function supplierSQLList($item_list, $field = '`s`.`supplier_id`')
    {
        if (count($item_list) == 0) {
            return "1";
        }

        $whereOR = array();
        $whereIN = array();
        foreach (list_to_partitions($item_list) as $k => $v) {
            if ($k > $v + 2) {
                $whereOR[] = "(" . $field . " >= " . $v . " AND " . $field . " <= " . $k . ")";
            } else {
                foreach (range($v, $k) as $k2) {
                    $whereIN[] = $k2;
                }
            }

        }
        if (count($whereIN) > 1) {
            $whereOR[] = $field . " IN(" . implode(", ", $whereIN) . ")";
        } elseif (count($whereIN) == 1) {
            $whereOR[] = $field . " = " . implode(" OR " . $field . " = ", $whereIN);
        }

        return implode(" OR ", $whereOR);
    }
}
if (!function_exists('memberSurveySearch')) {
    /**
     * @deprecated Does not belong here, or wrong name
     */
    function memberSurveySearch($criteria = '')
    {
        global $DB;
        $results  = array();
        $joins    = array();
        $fields   = getTableFields('`highwaygroup`.`new_members_survey`');
        $whereAND = array();
        if (is_array($criteria)) {
            foreach ($criteria as $field => $value) {
                if (isset($fields[$field])) {
                    if (is_array($value)) {
                        if (count($value) > 0) {
                            $whereOR = array();
                            if (substr($fields[$field][1], 0, 4) == 'set(') {
                                foreach ($value as $sub_value) {
                                    $whereOR[] = "	FIND_IN_SET(" . formatSQLString($sub_value) . ",ms.`" . $field . "`)";
                                }

                            } else {
                                foreach ($value as $sub_value) {
                                    $whereOR[] = "	ms.`" . $field . "`=" . formatSQLString($sub_value);
                                }

                            }
                            $whereAND[] = "	(" . implode(" OR ", $whereOR) . ")";
                        } else {
                            return array();
                        }
                    } else {
                        if (substr($fields[$field][1], 0, 4) == 'set(') {
                            $whereAND[] = "	FIND_IN_SET(" . formatSQLString($value) . ",ms.`" . $field . "`)";
                        } else {
                            $whereAND[] = "	ms.`" . $field . "`=" . formatSQLString($value);
                        }
                    }
                    unset($fields[$field]);
                } elseif (isset($ext_fields[$field])) {
                    if (is_array($value)) {
                        if (count($value) > 0) {
                            $whereOR = array();
                            foreach ($value as $sub_value) {
                                $whereOR[] = "	" . $ext_fields[$field] . "=" . formatSQLString($sub_value);
                            }

                            $whereAND[] = "	(" . implode(" OR ", $whereOR) . ")";
                        } else {
                            return array();
                        }
                    } else {
                        $whereAND[] = "	" . $ext_fields[$field] . "=" . formatSQLString($value);
                    }
                    unset($ext_fields[$field]);
                }
            }
        } elseif (is_string($criteria) && '' != $criteria) {
            $criteria = array('any_text' => $criteria);
        }
        $search_for = array();
        if (is_array($criteria['any_text'])) {
            $search_for = $criteria['any_text'];
        }

        if (is_string($criteria['any_text'])) {
            $search_for = explode(' ', $criteria['any_text']);
        }

        if (count($search_for) > 0) {
            $searchBits = array();
            foreach ($search_for as $findme) {
                $whereOR = array();
                foreach ($searchBits as $key) {
                    if (trim($findme) != '') {
                        $whereOR[] = $key . " LIKE '" . str_replace('%', '\%', str_replace('_', '\_', addslashes($findme))) . "%'";
                    }
                }

                if (count($whereOR) > 0) {
                    $whereAND[] = "	(" . implode(" OR ", $whereOR) . ")";
                }

            }
        }

        $query = $DB->query(sprintf(
            "SELECT `ms`.`member_id` FROM `highwaygroup`.`new_members_survey` AS `ms`%s ORDER BY `ms`.`member_id` ASC",
            (count($whereAND)) ? (" WHERE " . implode(" AND ", $whereAND)) : ""
        ));
        while ($row = $DB->fetch_assoc($query)) {
            $results[] = $row['member_id'];
        }
        $DB->free_result($query);
        return $results;
    }
}
if (!function_exists('supplierToDatabase')) {
    function supplierToDatabase($supplier)
    {
        global $DB;
        $supplier_id = intval($supplier['supplier_id']);
        $fields = supplierGetOptionList('base_fields_types');
        $changed = array();
        if (supplierExists($supplier_id)) {
            $old_vals = supplierFromDatabase($supplier_id);
            $set_vals = array();
            foreach ($supplier as $key => $value) {
                if (array_key_exists($key, $fields)) {
                    if (is_array($supplier[$key]) && substr($fields[$key][1], 0, 4) == 'set(') {
                        $set_vals[$key] = "`" . $key . "`=" . formatSQLString(implode(",", $supplier[$key]));
                    } elseif (isset($supplier[$key])) {
                        $set_vals[$key] = "`" . $key . "`=" . formatSQLString($supplier[$key]);
                    } else {
                        $set_vals[$key] = "`" . $key . "`=NULL";
                    }

                }
            }

            if (count($set_vals)) {
                $set_vals["modified"] = "`modified` = NOW()";
                $sql                  = "UPDATE `new_suppliers` SET " . implode(', ', $set_vals) . " WHERE `supplier_id` = " . $supplier_id . " LIMIT 1";
                $DB->query($sql);
            }
            return $supplier_id;
        } else {
            $insert_vals               = array();
            $insert_vals["created"]    = "NOW()";
            foreach ($supplier as $key => $value) {
                if (array_key_exists($key, $fields)) {
                    if (is_array($supplier[$key]) && substr($fields[$key][1], 0, 4) == 'set(') {
                        $insert_vals[$key] = formatSQLString(implode(",", $supplier[$key]));
                    } elseif (isset($supplier[$key])) {
                        $insert_vals[$key] = formatSQLString($value);
                    } else {
                        $insert_vals[$key] = 'null';
                    }

                }
            }

            if (count($insert_vals)) {
                $insert_vals["modified"] = "`modified` = NOW()";
                $sql = "INSERT INTO `new_suppliers` (`" . implode('`, `', array_keys($insert_vals)) . "`) VALUES (" . implode(", ", $insert_vals) . ")";
                $DB->query($sql);
                return $DB->insert_id();
            }
        }
        return $supplier_id;
    }
}

//TODO: Deprecated -> memberShippingAddressesFromDatabase (Name was not descriptively precise)
if (!function_exists('supplierShippingFromDatabase')) {
    function supplierShippingFromDatabase($supplier_id)
    {
        return supplierShippingAddressesFromDatabase($supplier_id);
    }
}
if (!function_exists('supplierBankFromDatabase')) {
    function supplierBankFromDatabase($supplier_id)
    {
        return supplierBankAddressesFromDatabase($supplier_id);
    }
}
//TODO: Deprecated -> memberShippingAddressesFromPost (Name was not descriptively precise)
if (!function_exists('supplierShippingFromPost')) {
    function supplierShippingFromPost($posted)
    {
        return supplierShippingAddressesFromPost($posted);
    }
}
if (!function_exists('supplierBankFromPost')) {
    function supplierBankFromPost($posted)
    {
        return supplierBankAddressesFromPost($posted);
    }
}
//TODO: Deprecated -> memberShippingAddressesToDatabase (Name was not descriptively precise)
if (!function_exists('supplierShippingToDatabase')) {
    function supplierShippingToDatabase($supplier_id, &$supplier_shipping)
    {
        return supplierShippingAddressesToDatabase($supplier_id, $supplier_shipping);
    }
}
if (!function_exists('supplierBankToDatabase')) {
    function supplierBankToDatabase($supplier_id, &$supplier_bank)
    {
        return supplierBankAddressesToDatabase($supplier_id, $supplier_bank);
    }
}
