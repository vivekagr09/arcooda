<?php
/**
 * @var mixed[] $posted
 */
include_once __DIR__ . '/common.inc.php';

use PHPMailer\PHPMailer\PHPMailer;

moduleLoad('member'); //memberCommentAdd, memberTemplateVars, memberFromDatabase, memberJobFromDatabase
moduleLoad('notify');
moduleLoad('phpmailer');

global $_GLOBALS;
global $DB;

$action    = $posted['action'];
$staff_id  = $_GLOBALS['staff']['id'];
$member_id = intval($posted['member_id']);

//Update Actions
switch ($action) {
    case 'do_mass_email':
        moduleLoad('product');

        $mailing_list = array();
        $query        = $DB->query(sprintf(
            "SELECT `member_id`, `name`, `surname`, `email_address` FROM `highwaygroup`.`new_members` WHERE `member_id` IN(%s)",
            trim(preg_replace('/[^0-9]+/', ',', $_REQUEST['mass_email_member_list']), ',')
        ));
        while ($row = $DB->fetch_assoc($query)) {
            $mailing_list[$row['member_id']] = array(
                'member_id'     => $row['member_id'],
                'email_address' => trim($row['name'] . ' ' . $row['surname']) . '<' . $row['email_address'] . '>',
                'email_status'  => 'listed',
            );
        }
        $DB->free_result($query);

        //Note: This is here because the serialize below gets corrupted when saved to database
        $message = strtr($_REQUEST['direct_email']['message'], array("\r\n" => "\n", "\n\r" => "\n", "\r" => "\n"));

        moduleLoad('newsletter');
        $NH = new Newsletter_Handler();
        $id = $NH->toDatabase(array(
            'site_id'     => '39',
            'subscr_list' => 'site_' . $_GLOBALS['site_settings']['site_id'],
            'settings'    => serialize(array(
                'paragraph'         => array(
                    'value' => $message,
                ),
                'as_member_comment' => array(
                    'value' => array_key_exists('mass_email_as_direct_email', $_REQUEST),
                ),
                'check_member'      => array(
                    'value' => array_key_exists('mass_email_check_member', $_REQUEST),
                ),
            )),
            'staff_id'    => $_GLOBALS['staff']['id'],
            'date_added'  => formatDateTime(),
            'sender'      => 'no-reply@highwaygroup.net',
            'subject'     => $_REQUEST['direct_email']['subject'],
            'short_desc'  => '',
            'description' => formatHTMLDescription($message),
            'alt_text'    => $message,
        ));
        $NH->mailinglistToDatabase($id, $mailing_list);

        /*
        if (array_key_exists('mass_email_as_direct_email', $_REQUEST)) {
        foreach ($mailing_list as $row) {
        $DB->query(sprintf(
        "INSERT INTO `highwaygroup`.`new_members_comments` (`member_id`, `staff_id`, `comment_type`, `comment_date`, `comment_text`, `comment_title`, `comment_sentto`) VALUES (%d, %d, 'email', NOW(), %s, %s, %s)",
        $row['member_id'],
        $_GLOBALS['staff']['id'],
        formatSQLString($message),
        formatSQLString($_REQUEST['direct_email']['subject']),
        $row['email_address']
        ));
        }
        }
        if (array_key_exists('mass_email_check_member', $_REQUEST)) $DB->query(sprintf(
        "UPDATE `highwaygroup`.`new_members` SET `check_staff_id` = %d, `check_date` = NOW() WHERE `member_id` IN(%s)",
        $_GLOBALS['staff']['id'],
        implode(', ', array_keys($mailing_list))
        ));
         */

        header('Location: /webadmin/newsletter/?action=edit&id=' . $id);
        die();
    case 'updatesupplierproduct':
       if (array_key_exists('stock', $_POST)) {
             productSuppliersHistoryToDatabase(0,0, productSuppliersHistoryFromPost($_POST['stock']));
            productSuppliersToDatabase(0, productSuppliersFromPost($_POST['stock']));
        }
        $action = 'edit';
        $supplier_id  = $_POST['supplier_id'];
        break;
    case 'do_add':
        unset($posted['supplier']['supplier_id']);
    case 'do_edit':
        moduleLoad('product');

        if (is_array($posted['supplier'])) {
            $supplier_id  = $posted['supplier']['supplier_id'];
            $is_new     = !supplierExists($supplier_id);
            $old_supplier = supplierFromDatabase($supplier_id);
            $new_supplier = supplierFromPost($posted['supplier']);
            if($supplier_id){
                $new_supplier['supplier_id']=$supplier_id;
            }
            if (array_key_exists('supplier_type_affected', $posted)) {
                $new_supplier['supplier_type'] = '';
                if (array_key_exists('supplier_type', $posted)) {
                    $new_supplier['supplier_type'] = implode(',', $posted['supplier_type']);
                }
            }
            if (array_key_exists('product_type_affected', $posted)) {
                $new_supplier['product_type'] = '';
/*                $allowarray=['machines','pcbs','parts','merchandies'];*/
                if (array_key_exists('product_type', $posted)) {
                    $new_supplier['product_type'] = implode(',', $posted['product_type']);
                }
            }
            $supplier_id                = supplierToDatabase($new_supplier);
            $new_supplier['supplier_id'] = $posted['supplier_id'] = $supplier_id;

            $delta = array();

            if (isset($posted['supplier_contacts_affected'])) {
                supplierContactsToDatabase($supplier_id, supplierContactsFromPost($posted));
            }
            if (array_key_exists('supplier_contacts_deleted', $_POST)) {
                foreach (preg_split('/[^0-9]+/', $_POST['supplier_contacts_deleted']) as $v) {
                    if ($v) {
                        supplierContactDelete($v);
                    }
                }
            }
            if (isset($posted['supplier_shipping_affected'])) {
                supplierShippingToDatabase($supplier_id, supplierShippingFromPost($posted));
            }
            if (array_key_exists('supplier_shipping_deleted', $_POST)) {
                foreach (preg_split('/[^0-9]+/', $_POST['supplier_shipping_deleted']) as $v) {
                    if ($v) {
                        supplierShippingAddressDelete($v);
                    }
                }
            }
            if (isset($posted['supplier_bank_affected'])) {
                supplierBankToDatabase($supplier_id, supplierBankFromPost($posted));
            }
            if (array_key_exists('supplier_bank_deleted', $_POST)) {
                foreach (preg_split('/[^0-9]+/', $_POST['supplier_bank_deleted']) as $v) {
                    if ($v) {
                        supplierBankAddressDelete($v);
                    }
                }
            }
            if ('' != $posted['comments']) {
                supplierCommentToDatabase($supplier_id, $posted['comments']);
                $delta[] = 'Comment added: ' . formatSQLString($posted['comments']);
            }

            if ('' != $posted['new_request']) {
                supplierCommentToDatabase($supplier_id, $posted['new_request'], 'warning');
                $delta[] = 'Warning added: ' . formatSQLString($posted['new_request']);
            }

            if (count($delta)) {
                supplierCommentToDatabase($supplier_id, "Changes Made: \n" . implode("\n", $delta), 'sql');
            }
            if (array_key_exists('supplier_product_delete', $_POST)) {
                foreach (preg_split('/[^0-9]+/', $_POST['supplier_product_delete']) as $v) {
                    if ($v) {
                        productSupplierDelete($v);
                    }
                }
            }
            if (isset($posted['direct_email'])) {
                $email_found = false;
                $job_content = array();
                foreach ($posted['direct_email'] as $k => $v) {
                    if (is_numeric($k)) {
                        if (array_key_exists('enabled', $v) && '' != $v['method'] && '' != $v['email_address']) {
                            $email_found   = true;
                            $job_content[] = ucwords($v['method']) . ' ' . $v['email_address'];
                        }
                    } elseif ('message' == $k) {
                    } else {
                        $job_content[] = ucwords($k) . ': ' . $v;
                    }
                }
                $job_content[] = '';
                $job_content[] = $posted['direct_email']['message'];
                if ($email_found) {
                    supplierJobToDatabase($supplier_id, implode("\r\n", $job_content));
                }
            }
            $action = 'edit';
            if ('' != $posted['return_message']) {
                $action = 'select';
            }

        } else {
            $action = 'error';
        }
        break;

    case "do_job":
        $job_id = intval($_REQUEST['job_id']);
        $job    = memberJobFromDatabase($job_id);
        if ($job['member_id'] == $member_id) {
            $member  = memberFromDatabase($member_id);
            $site_id = $_GLOBALS['site_settings']['site_id'];

            $job_headers = array();
            $job_message = explode("\r\n", $job['content']);
            while (($row = array_shift($job_message)) != '') {
                $row                                    = explode(':', $row, 2);
                $job_headers[trim(array_shift($row))][] = trim(implode(':', $row));
            }
            $job_message = $_REQUEST['job']['message'];
            $job_subject = $_REQUEST['job']['subject'];

            $staff_email = staffEmailFromDatabase($_GLOBALS['staff']['id'], $site_id);
            if ('' == $staff_email) {
                $staff_email = $_GLOBALS['staff']['email'];
            }

            $email = new PHPMailer();

            $email->Sender   = $staff_email;
            $email->From     = 'sales@arcooda.com';
            $email->FromName = "Jody Luo";
            $email->Mailer   = "sendmail";
            $email->CharSet  = "utf-8";
            $email->Subject  = $job_subject;
            $email->Body     = $job_message;
            $email->IsHTML(false);

            foreach ($job_headers['Cc'] as $v) {
                foreach (explode(',', $v) as $v2) {
                    $email->AddAddress(trim($v2));
                }
            }
            $email->AddBcc('backup@highwaygroup.net');
            $email->AddBcc($staff_email);
            foreach ($job_headers['Bcc'] as $v) {
                foreach (explode(',', $v) as $v2) {
                    $email->AddBCC(trim($v2));
                }
            }

            $site_emails = array(
                1  => array("Highwaygames", "sales@highwaygames.com"),
                2  => array("Highway Entertainment", "sales@highway.net.au"),
                3  => array("Coinopexpress", "sales@coinopexpress.com"),
                5  => array("Channel Beat", "sales@channelbeat.com"),
                9  => array("Converters.tv", "sales@converters.tv"),
                10 => array("Josifovski Admin", "sales@josifovski.com"),
                11 => array("Highway Group", "sales@highwaygroup.net"),
                14 => array("The Games Network", "sales@thegamesnetwork.tv"),
                13 => array("Home Pinballs", "sales@homepinballs.com"),
                15 => array("Fortunewave", "sales@fortunewave.com"),
                16 => array("Car Converters", "sales@carconverters.com.au"),
                17 => array("Boxer Machine", "sales@boxermachine.com"),
                18 => array("Xbox Arcade Machine", "sales@xboxarcademachines.com"),
                19 => array("Converters.com.hk", "sales@converters.com.hk"),
                20 => array("Arcade Spare Parts", "sales@arcadespareparts.com"),
                21 => array("Great Delta", "sales@greatdelta.com"),
                22 => array("GameGateAsia", "sales@gamegateasia.com"),
                23 => array("Radikal Darts Australia", "sales@radikaldarts.com.au"),
                24 => array("ArcadeGames.net", "sales@arcadegames.net"),
                25 => array("CYP Converters", "sales@cypconverters.com.au"),
                26 => array("Factory Clearance House", "sales@factoryclearancehouse.com"),
                27 => array("SVOS", "sales@svos.com.au"),
                28 => array("Destiny Bridal", "sales@destinybridal.com.au"),
                29 => array("Images By Angelo", "sales@imagesbyangelo.com.au"),
                30 => array("Highway Sales", "sales@highwaysales.net"),
                31 => array("Amusements", "sales@amusements.com.au"),
                32 => array("FotoInc", "sales@fotoinc.com.au"),
                33 => array("Highway Media", "sales@highwaymedia.com.au"),
            );
            if (array_key_exists($site_id, $site_emails)) {
                $email->AddBcc($site_emails[$site_id][1], $site_emails[$site_id][0]);
                $email->Sender = $site_emails[$site_id][1];
                $email->From   = $site_emails[$site_id][1];
            }
            if ('y' == $_REQUEST['job']['ConfirmReading']) {
                $email->ConfirmReadingTo = $staff_email;
            }
            $result = $email->Send();
            $email->ClearAttachments();
            $email->ClearAddresses();

            $notify_vars                    = memberTemplateVars($member);
            $notify_vars['[EMAIL_SUBJECT]'] = $email->Subject;
            $notify_vars['[EMAIL_BODY]']    = $email->Body;
            if (function_exists('notifyActionMade')) {
                notifyActionMade(NOTIFY_GENERIC, $site_id, $notify_vars, false);
            }
            unset($notify_vars);

            memberCommentAdd($member_id, implode("\r\n\r\n", [
                'comment_title'  => $job_subject,
                'comment_sentto' => (array_key_exists('Cc', $job_headers) ? implode(', ', $job_headers['Cc']) : '') . (array_key_exists('Bcc', $job_headers) ? ('BCC: ' . implode(', ', $job_headers['Bcc'])) : ''),
                'comment_text'   => $job_message,
            ]));

            $DB->query(sprintf(
                "DELETE FROM `highwaygroup`.`new_members_jobs` WHERE `id` = %d LIMIT 1",
                $job_id
            ));
        }
        $action = 'edit';
        break;

    case "delete_job":
        moduleLoad('member'); //memberJobFromDatabase

        $job_id = intval($_REQUEST['job_id']);
        $job    = memberJobFromDatabase($job_id);
        if ($job['member_id'] == $member_id) {
            $DB->query(sprintf(
                "DELETE FROM `highwaygroup`.`new_members_jobs` WHERE `id` = %d",
                $job_id
            ));
        }
        $action = 'edit';
        break;

    case "supplier_detail_english":
        $posted["chinese"] = 0;
        $action   = "addresses";
        break;
    case "supplier_detail_chinese":
        $posted["english"] = 0;
        $action         = "addresses";
        break;
}
$get_vars    = $posted;
$keep_only   = array_keys(supplierGetOptionList('all_fields'));
$keep_only[] = 'order_by';
$keep_only[] = 'search_for';
$keep_only[] = 'page_id';
$keep_only[] = 'order';
$keep_only[] = 'multiple';
$keep_only[] = 'return_message';
$keep_only[] = 'option';
$keep_only[] = 'notify_addr_id';
$keep_only[] = 'bill';
$keep_only[] = 'english';
$keep_only[] = 'chinese';
if (count($get_vars) > 0) {
    foreach ($get_vars as $key => $value) {
        if (is_array($value) || !in_array($key, $keep_only)) {
            unset($get_vars[$key]);
        }
    }
}
switch ($action) {
    case 'mass_email':
        header('Location: mass_email.php?' . http_build_query($_REQUEST));
        break;
    case 'options':
        header('Location: options.php?' . http_build_query($_REQUEST));
        break;
    case 'search':
        header('Location: search.php?' . http_build_query($get_vars));
        break;
    case 'search2':
        header('Location: search2.php?' . http_build_query($get_vars));
        break;
    case 'search3':
        header('Location: search3.php?' . http_build_query($get_vars));
        break;
    case 'view_all':
        header('Location: comments.php?' . http_build_query($get_vars));
        break;
    case 'add':
        unset($get_vars['supplier_id']);
        header('Location: add.php?' . http_build_query($get_vars));
        break;
    case 'addresses':
        header('Location: addresses.php?' . http_build_query($get_vars));
        break;
    case 'select':
        header('Location: select.php?' . http_build_query($get_vars));
        break;
    case 'select-product':
        header('Location: select-product.php?' . http_build_query($get_vars));
        break;
    case 'view':
        header('Location: summary.php?' . http_build_query($get_vars));
        break;
    case 'edit':
        // $supplier_lock = supplierGetLock($supplier_id);
        $supplier_lock = supplierGetLock($get_vars['supplier_id']);
        if (false === $supplier_lock || $supplier_lock['staff_id'] == $_GLOBALS["staff"]["id"]) {
            supplierSetLock($get_vars['supplier_id'], $_GLOBALS["staff"]["id"]);
            $member_jobs = supplierJobsFromDatabase($get_vars['supplier_id']);
            if (count($member_jobs)) {
                $get_vars['job_id'] = reset($member_jobs);
                header('Location: job.php?' . http_build_query($get_vars));
                break;
            }
        }
        header('Location: edit.php?' . http_build_query($get_vars));
        break;
    case 'do_search':
    case 'list':
    default:
        unset($get_vars['members_id']);
        if (in_array($_SERVER['HTTP_HOST'], array('www.highwayparks.com', 'www.arcadeforums.com', 'www.highwaygames.com'))) {
            header('Location: list2.php?' . http_build_query($get_vars));
        } else {
            header('Location: list.php?' . http_build_query($get_vars));
        }

}
