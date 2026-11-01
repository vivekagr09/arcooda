<?php
//moduleLoad('invoice');
//moduleLoad('phpmailer');

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
        global $DB;

        $fields = array('*' => "`m`.*");
        foreach (memberGetOptionList('ext_fields') as $k => $v) {
            $fields[$k] = sprintf("%s AS `%s`", $v, $k);
        }

        if ($result = $DB->query_first(sprintf(
            "SELECT %s FROM %s AS `m` WHERE `m`.`%s` = %d LIMIT 1",
            implode(", ", $fields),
            MEMBER_TABLENAME,
            MEMBER_PRIMARYKEY,
            $id
        ), MYSQLI_ASSOC)) {
            return memberVirtual($result, $invoice);
        }

        return memberDefault();
    }
}
if (!function_exists('memberLoginToDatabase')) {
    function memberLoginToDatabase($summary)
    {
        global $DB;

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

        $exists = memberLoginFromDatabase($summary['member_id']);
        $active = (in_array($summary['active'], array('y', 'n'))) ? $summary['active'] : 'y';
        $DB->query((($exists) ? "UPDATE" : "INSERT INTO") . " `highwaygroup`.`new_members_logins` SET `email_address` = " . formatSQLString($summary['email_address']) . ", `password` = " . formatSQLString($summary['password']) . ", `active` = " . formatSQLString($active) . (($exists) ? ' WHERE' : ',') . " `member_id` = " . formatSQLString($summary['member_id']));
        return true;
    }
}
if (!function_exists('memberSearch')) {
    function memberSearch($criteria = '', $alpha = false, $country = false, $site = false)
    {
        global $DB;
        $results = array();
        if (is_string($criteria) && '' != $criteria) {
            $criteria = array('any_text' => $criteria);
        }

        $field_types = memberGetOptionList('all_fields_types');
        $fields      = memberGetOptionList('all_fields');
        $whereAND    = array();
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
                                $temp = array();
                                foreach ($value as $sub_value) {
                                    $temp[] = formatSQLString($sub_value);
                                }

                                $whereOR[] = $fields[$field] . " IN(" . implode(", ", $temp) . ")";
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
                        "(`m`.`member_id` LIKE %s OR `m`.`site_id` LIKE %s OR MATCH(`company_name`, `first_name`, `last_name`, `nick_name`, `country`, `phone_no`, `phone_no2`, `fax_no`, `mobile_no`, `website`, `email_address`) AGAINST(%s))",
                        formatSQLLikeString($findme),
                        formatSQLLikeString($findme),
                        formatSQLString($findme)
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
if (!function_exists('memberSignup')) {
    function memberSignup($posted, $site)
    {
        global $DB;
        $match = array('cnv' => 9, 'he' => 2, 'car' => 16, 'hg' => 1);
        if (array_key_exists($site, $match)) {
            $eml                    = $posted['signup_1']['email_address'];
            $signup                 = $posted['personal'];
            $signup['account_type'] = 'private';
            if (in_array('company', $posted['account_type'])) {
                $signup                 = $posted['company'];
                $signup['first_name']   = $posted['contacts'][0]['first_name'];
                $signup['last_name']    = $posted['contacts'][0]['last_name'];
                $signup['account_type'] = 'company';
            }
            $signup['email_address'] = $eml;

            $accept_only = array('email_address', 'first_name', 'last_name', 'company_name', 'address', 'city', 'state', 'post_code', 'country', 'phone_no', 'phone_no2', 'fax_no', 'mobile_no', 'website', 'account_type');
            $set         = array();
            foreach ($accept_only as $field) {
                if (isset($signup[$field])) {
                    $set[] = '`' . $field . '`=' . formatSQLString($signup[$field]);
                }
            }

            // get all the IP / Country information
            $ip      = getClientIP();
            $country = getCountryInfo($ip);
            $flag    = ('' != $country['a2'] && $country['a2'] == $posted['country']) ? 'green' : 'red';
            $random  = mt_rand(1, 65535); // random number correspoding to the member.
            //now set additional data to be set now..
            $set[] = "date_joined=NOW(), site_id='" . $match[$site] . "', signup_IP=" . formatSQLString($ip) . ", flag='" . $flag . "', status='active', rand_num=" . formatSQLString($random);
            $set[] = "mailing_list='" . $posted['member']['mailing_list'] . "', `best_described` = '" . $posted['member']['best_described'] . "', currency='" . $posted['member']['currency'] . "', products_per_page='" . $posted['member']['products_per_page'] . "', news_per_page='" . $posted['member']['news_per_page'] . "'";

            $sql = "INSERT INTO `highwaygroup`.`new_members` SET " . implode(', ', $set);
            //echo $sql.'<hr />';
            $DB->query($sql);
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
