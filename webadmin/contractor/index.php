<?php
/**
 * @var mixed[] $posted
 */
include_once __DIR__ . '/common.inc.php';

use PHPMailer\PHPMailer\PHPMailer;

global $_GLOBALS;
global $DB;

moduleLoad('notify'); //notifyActionMade

if (!function_exists('contractorFromPost')) {
    function contractorFromPost($posted)
    {
        $contractor_id           = $posted['contractor_id'];
        $result                  = contractorFromDatabase($contractor_id) ?? contractorDefault();
        $result['contractor_id'] = intval($contractor_id);

        foreach ($posted as $key => $value) {
            if (is_array($value)) {
                $result[$key] = implode(',', $value);
            } else {
                $result[$key] = $value;
            }
        }

        if ($contractor_id <= 0) {
            unset($result['contractor_id']);
            $result['created_time'] = time();
            $result['created']      = formatDateTime();
        }

        // DUMMY HACK FOR NULL
        $nullHack = array(
            "packing_string",
            "freight_preference",
            "products_per_page",
            "news_per_page",
            "account_type",
            "flag",
            "reseller",
            "partner",
            "insurance_pref",
            "site_id",
            "contractor_type",
            "signup_IP",
            "nick_name",
            "phone_no2",
            "mailing_list",
            "best_described",
            "mailing_products",
            "browser",
            "b_version",
            "user_agent",
            "created_url",
            "rand_num",
        );

        foreach ($nullHack as $hack) {
            if (!isset($result[$hack]) || null == $result[$hack]) {
                $result[$hack] = "";
            }
        }

        return $result;
    }
}
if (!function_exists('contractorContactsFromPost')) {
    function contractorContactsFromPost($posted)
    {
        $contractor_id = intval($posted['contractor_id']);
        $modified  = array();
        if (is_array($posted['contractor_contacts'])) {
            foreach ($posted['contractor_contacts'] as $contact) {
                $modified[] = $contact['id'];
            }
        }
        $old_contacts = contractorContactsFromDatabase($contractor_id);
        $new_contacts = array();
        foreach ($old_contacts as $contact) {
            if (!in_array($contact['id'], $affected) || in_array($contact['id'], $modified)) {
                $new_contacts[$contact['id']] = $contact;
            }
        }
        if (is_array($posted['contractor_contacts'])) {
            foreach ($posted['contractor_contacts'] as $contact) {
                if (0 != $contact['id']) {
                    $contact['contractor_id']=$contractor_id;
                    foreach ($contact as $key => $value) {
                        $new_contacts[$contact['id']][$key] = $value;
                    }
                } else {
                    $contact['contractor_id']=$contractor_id;
                    $new_contacts[] = $contact;
                }
            }
        }
        return $new_contacts;
    }
}

$action    = $posted['action'];
$staff_id  = $_GLOBALS['staff']['id'];
$contractor_id = intval($posted['contractor_id']);

//Update Actions
switch ($action) {
    case 'do_add':
    case 'do_edit':
            $contractor_id  = $posted['contractor']['contractor_id'];
            $is_new         = !contractorExists($contractor_id);
            $old_contractor = contractorFromDatabase($contractor_id) ?? contractorDefault();
            $posted['contractor']['tax']=(!isset($posted['contractor']['tax']) || $posted['contractor']['tax']!='y')?'n':'y';
            $new_contractor = contractorFromPost($posted['contractor']);
            
            $contractor_id                = contractorToDatabase($new_contractor);
            $new_contractor['contractor_id'] = $posted['contractor_id'] = $contractor_id;

            $delta = array();

            if (isset($posted['contractor_contacts_affected'])) {
                           //die('dfgdfg');
                contractorContactsToDatabase($contractor_id, contractorContactsFromPost($posted));
            }
            if (array_key_exists('contractor_contacts_deleted', $_POST)) {
                foreach (preg_split('/[^0-9]+/', $_POST['contractor_contacts_deleted']) as $v) {
                    if ($v) {
                        contractorContactDelete($v);
                    }
                }
            }

            foreach ($new_contractor as $key => $value) {
                if ($old_contractor[$key] != $new_contractor[$key] && !is_array($new_contractor[$key]) && !is_array($old_contractor[$key])) {
                    $delta[] = $key . ' changed from: ' . formatSQLString($old_contractor[$key]) . ' to: ' . formatSQLString($new_contractor[$key]);
                }
            }
            if ('' != $posted['comments']) {
                contractorCommentAdd($contractor_id, $posted['comments']);
                $delta[] = 'Comment added: ' . formatSQLString($posted['comments']);
            }

            if ('' != $posted['new_request']) {
                contractorCommentAdd($contractor_id, $posted['new_request'], 'warning');
                $delta[] = 'Warning added: ' . formatSQLString($posted['new_request']);
            }

            if (count($delta)) {
                contractorCommentAdd($contractor_id, "Changes Made: \n" . implode("\n", $delta), 'sql');
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
                    contractorJobToDatabase($contractor_id, implode("\r\n", $job_content));
                }
            }
            $action = 'edit';
            if ('' != $posted['return_message']) {
                $action = 'select';
            }
        break; 
    case "do_job":
        moduleLoad('phpmailer');

        $job_id = intval($_REQUEST['job_id']);
        $job    = contractorJobFromDatabase($job_id);
        if ($job['contractor_id'] == $contractor_id) {
            $contractor  = contractorFromDatabase($contractor_id);
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

            $site_emails = array();
            if ($query = $DB->query(sprintf(
                "SELECT `id`, `website`, `sales_email` FROM `highwaygroup`.`websites` ORDER BY `id` ASC"
            ))) {
                while ($row = $DB->fetch_assoc($query)) {
                    $site_emails[$row['id']] = [$row['website'], $row['sales_email']];
                }
                $DB->free_result($query);
            }

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

            $notify_vars                    = contractorTemplateVars($contractor);
            $notify_vars['[EMAIL_SUBJECT]'] = $email->Subject;
            $notify_vars['[EMAIL_BODY]']    = $email->Body;

            if (function_exists('notifyActionMade')) {
                notifyActionMade(
                    NOTIFY_GENERIC,
                    $site_id,
                    $notify_vars,
                    false
                );
            }

            unset($notify_vars);

            contractorCommentAdd($contractor_id, implode("\n", array(
                'comment_title'  => $job_subject,
                'comment_sentto' => (array_key_exists('Cc', $job_headers) ? implode(', ', $job_headers['Cc']) : '') . (array_key_exists('Bcc', $job_headers) ? ('BCC: ' . implode(', ', $job_headers['Bcc'])) : ''),
                'comment_text'   => $job_message,
            ), 'email'));

            $DB->query(sprintf(
                "DELETE FROM `contractors_jobs` WHERE `id` = %d LIMIT 1",
                $job_id
            ));
        }
        $action = 'edit';
        break;

    case "delete_job":
        $job_id = intval($_REQUEST['job_id']);
        $job    = contractorJobFromDatabase($job_id);
        if ($job['contractor_id'] == $contractor_id) {
            $DB->query(sprintf(
                "DELETE FROM `contractors_jobs` WHERE `id` = %d",
                $job_id
            ));
        }
        $action = 'edit';
        break;
    case 'deletecontractor':
    if(isset($_POST['contractor_id'])){
        contractorDelete($_POST['contractor_id']);
        $action = 'list';
    }
    break;
}

$get_vars    = $posted;
$keep_only   = array_keys(contractorGetOptionList('all_fields'));
$keep_only[] = 'order_by';
$keep_only[] = 'search_for';
$keep_only[] = 'page_id';
$keep_only[] = 'order';
$keep_only[] = 'multiple';
$keep_only[] = 'return_message';
$keep_only[] = 'option';
$keep_only[] = 'notify_addr_id';
$keep_only[] = 'bill';
$keep_only[] = 'site_id';
$keep_only[] = 'distributor_id';

if (count($get_vars) > 0) {
    foreach ($get_vars as $key => $value) {
        if (is_array($value) || !in_array($key, $keep_only)) {
            unset($get_vars[$key]);
        }
    }
}

switch ($action) {
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
        unset($get_vars['contractor_id']);
        header('Location: add.php?' . http_build_query($get_vars));
        break;
    case 'addresses':
        header('Location: addresses.php?' . http_build_query($get_vars));
        break;
    case 'select':
        header('Location: select.php?' . http_build_query($get_vars));
        break;
    case 'view':
        header('Location: summary.php?' . http_build_query($get_vars));
        break;
    case 'edit':
        $contractor_lock = contractorGetLock($contractor_id);
        if (false === $contractor_lock || $contractor_lock['staff_id'] == $_GLOBALS["staff"]["id"]) {
            contractorSetLock($contractor_id, $_GLOBALS["staff"]["id"]);
            $contractor_jobs = contractorJobsFromDatabase($contractor_id);
            if (count($contractor_jobs)) {
                $get_vars['job_id'] = reset($contractor_jobs);
                header('Location: job.php?' . http_build_query($get_vars));
                break;
            }
        }
        header('Location: edit.php?' . http_build_query($get_vars));
        break;
    case 'do_search':
    case 'list':
    default:
        unset($get_vars['contractors_id']);
        if (in_array($_SERVER['HTTP_HOST'], array('www.highwayparks.com', 'www.arcadeforums.com', 'www.highwaygames.com'))) {
            header('Location: list2.php?' . http_build_query($get_vars));
        } else {
            header('Location: list.php?' . http_build_query($get_vars));
        }

}
