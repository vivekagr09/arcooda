<?php
/**
 * @var mixed[] $posted
 * @var mixed[] $get_vars
 */
include_once __DIR__ . '/common.inc.php';

global $DB;

$action = $posted['action'];

if (!function_exists('memberContactsFromPost')) {
    /**
     * @param mixed[] $posted
     *
     * @return mixed[][]
     */
    function memberContactsFromPost($posted)
    {
        $member_id = intval($posted['member_id']);
        $affected  = explode('|', $posted['contacts_affected']);
        $table     = $posted['contacts'];
        $current   = memberContactsFromDatabase($member_id);

        foreach ($affected as $key) {
            foreach ($current as $key2 => $contact) {
                if ($contact['id'] == $key) {
                    unset($current[$key2]);
                }
            }
        }

        if (is_array($table)) {
            foreach ($table as $contact) {
                $contact['member_id'] = $member_id;
                foreach (getTableFields('`highwaygroup`.`new_members_contacts`') as $row) {
                    if (!isset($contact[$row[0]])) {
                        $contact[$row[0]] = $row[4];
                    }
                }

                $current[] = $contact;
            }
        }

        return $current;
    }
}
if (!function_exists('memberCouriersFromPost')) {
    /**
     * @param mixed[] $posted
     *
     * @return mixed[][]
     */
    function memberCouriersFromPost($posted)
    {
        $member_id = intval($posted['member_id']);
        $affected  = explode('|', $posted['couriers_affected']);
        $table     = $posted['couriers'];
        $current   = memberCouriersFromDatabase($member_id);

        foreach ($affected as $key) {
            foreach ($current as $key2 => $courier) {
                if ($courier[MEMBER_COURIER_PRIMARYKEY] == $key) {
                    unset($current[$key2]);
                }
            }
        }

        if (is_array($table)) {
            foreach ($table as $courier) {
                $courier['member_id'] = $member_id;
                foreach (getTableFields(MEMBER_COURIER_TABLENAME) as $row) {
                    if (!isset($courier[$row[0]])) {
                        $courier[$row[0]] = $row[4];
                    }
                }

                $current[] = $courier;
            }
        }

        return $current;
    }
}
if (!function_exists('memberForwardersFromPost')) {
    /**
     * @param mixed[] $posted
     *
     * @return mixed[][]
     */
    function memberForwardersFromPost($posted)
    {
        $member_id = intval($posted['member_id']);
        $affected  = explode('|', $posted['forwarder_affected']);
        $table     = $posted['forwarder'];
        $current   = memberForwardersFromDatabase($member_id);

        foreach ($affected as $key) {
            foreach ($current as $key2 => $forwarder) {
                if ($forwarder[MEMBER_FORWARDER_PRIMARYKEY] == $key) {
                    unset($current[$key2]);
                }
            }
        }

        if (is_array($table)) {
            foreach ($table as $forwarder) {
                $forwarder['member_id'] = $member_id;
                foreach (getTableFields(MEMBER_FORWARDER_TABLENAME) as $row) {
                    if (!isset($forwarder[$row[0]])) {
                        $forwarder[$row[0]] = $row[4];
                    }
                }
                $current[] = $forwarder;
            }
        }

        return $current;
    }
}

//Update Actions
switch ($action) {
    case 'do_add':
        unset($posted['member_id']);
    case 'do_edit':
        moduleLoad('product');

        $member   = memberFromPost($posted);
        $continue = true;
        if ('do_add' == $action && ('' == $member['first_name'] || memberEmailExists($member['email_address']))) {
            $continue = false;
        }

        if ($continue) {
            $get_vars['member_id']    = $posted['member_id'] = $member_id = memberToDatabase($member);
            $contacts                 = memberContactsFromPost($posted);
            $member_shippingaddresses = memberShippingAddressesFromPost($posted);
            $member_notifyparties     = memberNotifyPartiesFromPost($posted);
            $forwarders               = memberForwardersFromPost($posted);
            $couriers                 = memberCouriersFromPost($posted);
            $mailing_list             = memberMailingListFromPost($posted);
            $member_survey            = memberSurveyFromPost($posted);
            memberContactsToDatabase($member_id, $contacts);
            memberShippingAddressesToDatabase($member_id, $member_shippingaddresses);
            memberNotifyPartiesToDatabase($member_id, $member_notifyparties);
            memberForwardersToDatabase($member_id, $forwarders);
            memberCouriersToDatabase($member_id, $couriers);
            memberMailingListToDatabase($member_id, $mailing_list);
            memberSurveyToDatabase($member_id, $member_survey);

            // now the car converters preferences....
            $mem_type = explode(',', $member['site_id']);
            if (is_array($posted['car']) && in_array('16', $mem_type)) {
                $member_carconv          = $posted['car'];
                $member_carconv_industry = $posted['car'];

                //@TODO should be a memberCarconvToDatabase function to handle this
                $insert_vals = array();
                $update_vals = array();
                foreach (array('sales_rep', 'send_newsletter', 'heard_from') as $field) {
                    if (array_key_exists($field, $member_carconv)) {
                        $insert_vals[$field] = $DB->escape($member_carconv[$field]);
                        $update_vals[$field] = sprintf(
                            "`%s` = %s",
                            $field,
                            $DB->escape($member_carconv[$field])
                        );
                    }
                }
                if (count($insert_vals)) {
                    $insert_vals['member_id'] = $member_id;
                    $DB->query(sprintf(
                        "INSERT INTO `converters`.`car_members` (`%s`) VALUES (%s) ON DUPLICATE KEY UPDATE %s",
                        implode("`, `", array_keys($insert_vals)),
                        implode(", ", array_values($insert_vals)),
                        implode(", ", $update_vals)
                    ));
                }

                //@TODO should be a memberCarconvIndustryToDatabase function to handle this
                $insert_vals = array();
                $update_vals = array();
                foreach (array('ic_code') as $field) {
                    if (array_key_exists($field, $member_carconv_industry)) {
                        $insert_vals[$field] = $DB->escape($member_carconv_industry[$field]);
                        $update_vals[$field] = sprintf(
                            "`%s` = %s",
                            $field,
                            $DB->escape($member_carconv_industry[$field])
                        );
                    }
                }
                if (count($insert_vals)) {
                    $insert_vals['member_id'] = $member_id;
                    $DB->query(sprintf(
                        "INSERT INTO `converters`.`car_members_industry` (`%s`) VALUES (%s) ON DUPLICATE KEY UPDATE %s",
                        implode("`, `", array_keys($insert_vals)),
                        implode(", ", array_values($insert_vals)),
                        implode(", ", $update_vals)
                    ));
                }
            }

            //now the member discounts...
            if (is_array($posted['discounts'])) {
                memberDiscountsToDatabase($member_id, $posted['discounts']);
            }

            if ('' != $posted['admin_comment']) {
                memberCommentAdd($member_id, $posted['admin_comment']);
            }

            if ('' != $posted['member_request']) {
                memberRequestsToDatabase($member_id, $posted['member_request']);
            }

            if ('' != $posted['member_warning']) {
                memberWarningsToDatabase($member_id, $posted['member_warning']);
            }

            if (array_key_exists('product_serial', $_POST)) {
                foreach ($_POST['product_serial'] as $key => $value) {
                    if ($value['serial_id']) {
                        if ($value['id']) {
                            $value['modified'] = formatDateTime();
                        } else {
                            $value['created'] = formatDateTime();
                        }
                        productSerialNoToDatabase(productSerialNoFromPost($value));
                    }

                }

            }
            // If the password is changed or password is set when adding a new member...
            if ('' != $posted['login']['password'] && ($posted['login']['password'] == $posted['login']['c_password'])) {
                $summary = memberLoginFromDatabase($member_id);
                if (!$summary) {
                    $summary = array('member_id' => $member_id, 'email_address' => $posted['login']['email']);
                }

                $summary['password'] = md5($posted['login']['password']);
                $summary['active']   = $posted['login']['status'];
                memberLoginToDatabase($summary);
            }

            if (array_key_exists("members_authentication_affected", $posted)) {
                $auth_delete = array();
                $auth_update = array();
                $auth_insert = array();
                foreach (explode(",", $posted["members_authentication_affected"]) as $v) {
                    if ("" != $v) {
                        $auth_delete[$v] = $v;
                    }
                }

                if (is_array($posted["member_authentication"])) {
                    foreach ($posted["member_authentication"] as $auth) {
                        if ("" != $auth["email_address"]) {
                            unset($auth_delete[$auth["email_address"]]);
                            $update = array();
                            if ("" != $auth["active"]) {
                                $update["active"] = $auth["active"];
                            }

                            if ("" != $auth["email_address1"] && $auth["email_address1"] != $auth["email_address"]) {
                                $update["email_address"] = $auth["email_address1"];
                            }

                            if ("" != $auth["password1"] && $auth["password1"] == $auth["password2"]) {
                                $update["password"] = md5($auth["password1"]);
                            }

                            $auth_update[$auth["email_address"]] = $update;
                        } else {
                            $insert = array();
                            if ("" != $auth["active"]) {
                                $insert["active"] = $auth["active"];
                            }

                            if ("" != $auth["email_address1"]) {
                                $insert["email_address"] = $auth["email_address1"];
                            }

                            if ("" != $auth["password1"] && $auth["password1"] == $auth["password2"]) {
                                $insert["password"] = md5($auth["password1"]);
                            }

                            if ("" != $insert["email_address"] && ("" != $insert["password"] || "n" == $insert["active"])) {
                                $auth_insert[$auth["email_address1"]] = $insert;
                            }

                        }
                    }
                }

                $log = array();
                foreach ($auth_delete as $k => $v) {
                    $DB->query("DELETE FROM `highwaygroup`.`new_members_logins` WHERE `member_id` = " . intval($member_id) . " AND `email_address` = " . formatSQLString($k) . " LIMIT 1");
                    $log[] = "Deleted login authentication for " . $k;
                }
                foreach ($auth_update as $k => $v) {
                    $update = array();
                    foreach (array("password", "active", "plainpass") as $v2) {
                        if (array_key_exists($v2, $v)) {
                            $update[$v2] = "`" . $v2 . "` = " . formatSQLString($v[$v2]);
                        }
                    }

                    if (count($update)) {
                        $DB->query("UPDATE `highwaygroup`.`new_members_logins` SET " . implode(", ", $update) . " WHERE `member_id` = " . intval($member_id) . " AND `email_address` = " . formatSQLString($k) . " LIMIT 1");
                        $log[] = "Updated login authentication for " . $k;
                    }
                }
                foreach ($auth_insert as $k => $v) {
                    $insert = array("member_id" => intval($member_id), "email_address" => formatSQLString($k));
                    foreach (array("password", "active", "plainpass") as $v2) {
                        if (array_key_exists($v2, $v)) {
                            $insert[$v2] = formatSQLString($v[$v2]);
                        }
                    }

                    if (count($insert)) {
                        $DB->query("INSERT INTO `highwaygroup`.`new_members_logins` (`" . implode("`, `", array_keys($insert)) . "`) VALUES (" . implode(", ", array_values($insert)) . ")");
                        $log[] = "Added login authentication for " . $k;
                    }
                }
            }

            $action = 'edit';
            if (isset($posted['return_message'])) {
                $action = 'select';
            }

        } else {
            $action = 'error';
        }

        break;

    case 'lock':
        memberSetLock($posted['member_id'], $staff_id);
        $action = 'list';
        unset($get_vars['member_id']);
        break;

    case 'do_search':
        foreach ($get_vars as $k => $v) {
            if (is_string($v) && "" == $v) {
                unset($get_vars[$k]);
                unset($posted[$k]);
            }
        }

        $action = 'list';
        break;

    case 'unlock_sales':
    case 'unlock':
        memberSetLock($posted['member_id']);
        unset($get_vars['member_id']);
        if ('unlock_sales' == $action) {
            $action = 'sales_list';
        } else {
            $action = 'list';
        }

        break;

    case "bill_ship":
        $get_vars["notify_addr_id"] = 0;
        $action                     = "addresses";
        break;
    case "ship_notify":
        $get_vars["bill"] = 0;
        $action           = "addresses";
        break;
}
//$get_vars=$posted;
$keep_only = array('member_id', 'search_for', 'order_by', 'order', 'page', 'by', 'alpha_value', 'return_message', 'active', 'last_initial', 'multiple', 'country', 'bill', 'ship_addr_id', 'notify_addr_id');
if (count($get_vars) > 0) {
    foreach ($get_vars as $key => $value) {
        if (is_array($value) || !in_array($key, $keep_only)) {
            unset($get_vars[$key]);
        }
    }
}

foreach (array(
    'member_list',
    'site_id',
) as $k) {
    if (array_key_exists($k, $posted) && !empty($posted[$k])) {
        $get_vars[$k] = (is_string($posted[$k])) ? preg_split('/[^0-9]+/', $posted[$k]) : $posted[$k];
    }
}

switch ($action) {
    case 'search':
        header('Location: members_search.php?' . http_build_query($get_vars));
        break;

    case 'sales_search':
        header('Location: members_search_sales.php?' . http_build_query($get_vars));
        break;

    case 'error':
        $get_vars['action'] = 'error';
    case 'add':
        unset($get_vars['member_id']);
        header('Location: members_add.php?' . http_build_query($get_vars));
        break;

    case 'addresses':
        header('Location: members_addresses.php?' . http_build_query($get_vars));
        break;

    case 'comments':
        header('Location: members_comments.php?' . http_build_query($get_vars));
        break;

    case 'login':
        header('Location: members_login.php?' . http_build_query($get_vars));
        break;

    case 'select':
        header('Location: members_select.php?' . http_build_query($get_vars));
        break;

    case 'view':
        header('Location: members_summary.php?' . http_build_query($get_vars));
        break;

    case 'edit':
        $member_lock = memberGetLock($posted['member_id']);
        if (empty($member_lock) || $member_lock['staff_id'] == $_GLOBALS['staff']['staff_id']) {
            memberSetLock($posted['member_id'], $_GLOBALS['staff']['staff_id']);
        }

        header('Location: members_edit.php?' . http_build_query($get_vars));
        break;

    case 'sales_list':
        unset($get_vars['members_id']);
        header('Location: members_list_sales.php?' . http_build_query($get_vars));
        break;

    case 'list':
    default:
        unset($get_vars['members_id']);
        header('Location: members_list.php?' . http_build_query($get_vars));
}
die();
