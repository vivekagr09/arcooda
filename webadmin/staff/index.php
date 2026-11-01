<?php
/**
 * @var mixed[] $posted
 */
include_once __DIR__ . '/common.inc.php';

moduleLoad('staff');

global $_GLOBALS;
global $DB;

$action = $posted['action'];

/**
 * @param mixed[] $posted
 *
 * @return mixed[]
 */
function staffFromPost($posted)
{
    $staff_id = $posted['staff_id'];
    unset($posted['user']);
    if (isset($posted['icq_no'])) {
        $icq_no = '';
        for ($i = 0; $i < strlen($posted['icq_no']); $i++) {
            if (is_numeric($posted['icq_no'][$i])) {
                $icq_no .= $posted['icq_no'][$i];
            }
        }

        if ("" != $icq_no) {
            $icq_no = number_format($icq_no, 0, '-', '-');
        }

        $posted['icq_no'] = $icq_no;
    }
    foreach (array('start', 'end', 'bonus', 'review', 'birth') as $date_key) {
        if (isset($posted[$date_key . '_date'])) {
            $split                       = explode('-', $posted[$date_key . '_date']);
            $posted[$date_key . '_time'] = mktime(0, 0, 0, intval($split[1]), intval($split[2]), intval($split[0]));
            if (0 == $posted[$date_key . '_time']) {
                $posted[$date_key . '_time'] = null;
                $posted[$date_key . '_date'] = null;
            }
        }
    }

    $icq_no   = $posted['icq_no'];
    $old_vals = staffFromDatabase($staff_id);
    foreach ($old_vals as $key => $value) {
        if (array_key_exists($key, $posted)) {
            $old_vals[$key] = $posted[$key];
        }
    }

    if ($staff_id <= 0) {
        unset($old_vals['staff_id']);
    }

    if ('' == $old_vals['user']) {
        if ('' != $old_vals['last_name']) {
            $want_username = strtolower(substr($old_vals['first_name'], 0, 1) . $old_vals['last_name']);
        } else {
            $want_username = strtolower($old_vals['first_name']);
        }

        if ('' != $want_username) {
            $old_vals['user'] = $want_username;
            $counter          = 1;
            while (staffFromUser($old_vals['user'])) {
                $counter++;
                $old_vals['user'] = $want_username . $counter;
            }
        }
    }
    return $old_vals;
}

//Update Actions
switch ($action) {
    case 'do_add':
        $add = true; // needed to check if new staff is added to give them default permissions.
    case 'do_edit':
        $staff = staffFromPost($posted);
        if ('' != $posted['pay_date']) {
            unset($staff['pay_rate']);
        }
        //Handle pay rate overiding below
        $posted['staff_id'] = $staff_id = staffToDatabase($staff);

        if ($add) {
            // need to add the default staff permissions here..
            $res      = $DB->query("SELECT `section_id`, `sites` FROM `highwaygroup`.`admin_sections` WHERE `default` = 'y'");
            $sections = array();
            while ($row = $DB->fetch_assoc($res)) {
                $sections[$row['section_id']] = $row['sites'];
            }

            foreach ($sections as $section_id => $sites) {
                foreach (explode(',', $sites) as $site_id) {
                    $DB->query("INSERT INTO `highwaygroup`.`admin_users` (`user`, `section_id`, `site_id`) VALUES (" . $DB->escape($staff['user']) . ", " . intval($section_id) . ", " . intval($site_id) . ")");
                }
            }
        }

        if (in_array('USERS', $allowed_sections)) {
            $pw_file = $_SERVER['DOCUMENT_ROOT'] . '/webadmin/users/.htpasswd';
            if (trim($posted['login_password']) != '' && 'y' == $staff['active']) {
                passthru('/usr/bin/htpasswd -b ' . $pw_file . ' ' . escapeshellarg($staff['user']) . ' ' . escapeshellarg(trim($posted['login_password'])));
            }

            if ('n' == $staff['active']) {
                passthru('/usr/bin/htpasswd -D ' . $pw_file . ' ' . escapeshellarg($staff['user']));
                $DB->query(sprintf(
                    "DELETE FROM `highwaygroup`.`admin_users` WHERE `user` = %s LIMIT 1",
                    $DB->escape($staff['user'])
                ));
            }
        }

        $url         = parse_url($_FILES['new_photo']['name']);
        $pinfo       = pathinfo($url['path']);
        $file_target = formatFileSafe($staff['user'] . '.' . $pinfo['extension']);

        $tmp_photo = $_FILES['new_photo']['tmp_name'];
        if (is_uploaded_file($tmp_photo) && 0 == $_FILES['new_photo']['error']) {
            move_uploaded_file($tmp_photo, $_SERVER['DOCUMENT_ROOT'] . '/images/staff/' . $file_target);
            $DB->query(sprintf(
                "UPDATE %s SET `photo` = %s WHERE `id` = %d LIMIT 1",
                "`highwaygroup`.`users`",
                $DB->escape($file_target),
                $staff_id
            ));
        }

        $url         = parse_url($_FILES['new_sig']['name']);
        $pinfo       = pathinfo($url['path']);
        $file_target = formatFileSafe($staff['user'] . '.' . $pinfo['extension']);

        $tmp_sig = $_FILES['new_sig']['tmp_name'];
        if (is_uploaded_file($tmp_sig) && 0 == $_FILES['new_sig']['error']) {
            move_uploaded_file($tmp_sig, $_SERVER['DOCUMENT_ROOT'] . '/images/sigs/' . $file_target);
            $DB->query(sprintf(
                "UPDATE %s SET `signature` = %s WHERE `id` = %d LIMIT 1",
                "`highwaygroup`.`users`",
                $DB->escape($file_target),
                $staff_id
            ));

            //Overwrite converters.tv signature
            //copy($_SERVER['DOCUMENT_ROOT'] . '/images/sigs/' . $file_target, $_SERVER['DOCUMENT_ROOT'] . '/sigs/' . $file_target);
            //$DB->query("DELETE FROM `converters`.`sigs` WHERE `user_name`=" . $DB->escape($staff['user']) . " LIMIT 1");
            //$DB->query("INSERT INTO `converters`.`sigs`(sig_image,user_name) VALUES (" . $DB->escape($file_target) . "," . $DB->escape($staff['user']) . ")");
        }

        $pay_split = explode('-', $posted['pay_date']);
        if (date('Y-m-d') != $posted['pay_date'] && count($pay_split) == 3) {
            $pay_time = mktime(0, 0, 0, $pay_split[1], $pay_split[2], $pay_split[0]);
            staffPayRateSetValue($staff_id, $posted['pay_rate'], $pay_time);
        }

        if (is_array($posted['bank_id'])) {
            foreach ($posted['bank_id'] as $key => $bank_id) {
                $bank_info = array();
                if ($bank_id > 0) {
                    $bank_info['bank_id'] = $bank_id;
                }

                $bank_info['bank_active'] = 'n';
                if ('' != $posted['bank_active'][$key]) {
                    $bank_info['bank_active'] = 'y';
                }

                foreach (array('bank_name', 'bank_bank', 'bank_bsb', 'bank_account') as $field) {
                    if (trim($posted[$field][$key]) != '') {
                        $bank_info[$field] = $posted[$field][$key];
                    }
                }

                if (count($bank_info) > 1) {
                    staffBankToDatabase($staff_id, $bank_info);
                }
            }
        }

        if (is_array($posted['fund_id'])) {
            foreach ($posted['fund_id'] as $key => $fund_id) {
                $fund_info = array();
                if ($fund_id > 0) {
                    $fund_info['fund_id'] = $fund_id;
                }

                $fund_info['fund_active'] = 'n';
                if ('' != $posted['fund_active'][$key]) {
                    $fund_info['fund_active'] = 'y';
                }

                foreach (array('fund_name', 'fund_address', 'fund_policy', 'fund_contact', 'fund_phone') as $field) {
                    if (trim($posted[$field][$key]) != '') {
                        $fund_info[$field] = $posted[$field][$key];
                    }
                }

                if (count($fund_info) > 1) {
                    staffSuperToDatabase($staff_id, $fund_info);
                }
            }
        }

        if ('' != $posted['pay_review']['review_comments']) {
            $review_time = 0;
            if ('' != $posted['pay_review']['review_date']) {
                $split       = explode('-', $posted['pay_review']['review_date']);
                $review_time = mktime(0, 0, 0, intval($split[1]), intval($split[2]), intval($split[0]));
            }
            $message = array(
                'staff_id'        => $staff_id,
                'review_comments' => $posted['pay_review']['review_comments'],
                'review_time'     => time(),
            );
            if (0 != $review_time) {
                $message['review_time'] = $review_time;
            } else {
                $review_time = time();
            }

            staffReviewToDatabase($staff_id, $message);
        }
        if ('' != $posted['admin_notes']) {
            staffHistoryToDatabase($staff_id, [
                'message_type_id' => STAFF_HISTORY_TYPE_ADMIN_NOTE,
                'message_notes'   => $posted['admin_notes'],
            ]);
        }
        if (is_array($posted['staff_sites'])) {
            foreach (staffWebsitesSearch($staff_id) as $site_id) {
                staffWebsiteDelete($staff_id, $site_id);
            }
            foreach ($posted['staff_sites'] as $site_id) {
                staffWebsiteSetValue($staff_id, $site_id, true);
            }
        }

        //now need to add the email address to the database
        foreach ($posted['staff_emails'] ?? [] as $site_id => $email) {
            staffEmailSetValue($staff_id, $site_id, (empty($email)) ? null : $email);
        }

        $action = 'edit';
        break;
    case 'do_options':
        if ($posted['staff_id'] > 0) {
            $staff              = staffFromPost($posted);
            $posted['staff_id'] = staffToDatabase($staff);
            $staff_id           = $posted['staff_id'];
        }
        $action = 'options';
        if ('' != $posted['return_message']) {
            $action = 'select';
        }

        break;
}

$get_vars  = $posted;
$keep_only = array('order_by', 'search_for', 'page', 'page_id', 'order', 'staff_id', 'show_status', 'summarize', 'return_message');
if (count($get_vars) > 0) {
    foreach ($get_vars as $key => $value) {
        if (is_array($value) || !in_array($key, $keep_only)) {
            unset($get_vars[$key]);
        }
    }
}

//Redirect Actions
switch ($action) {
    case 'add':
        redirect('staff_add.php?' . http_build_query($get_vars));
        die();

    case 'staff_summary':
        redirect('staff_summary.php?' . http_build_query($get_vars));
        die();

    case 'options':
        redirect('staff_options.php?' . http_build_query($get_vars));
        die();

    case 'edit':
        if (!$get_vars['staff_id'] > 0) {
            $get_vars['staff_id'] = $_GLOBALS['staff']['id'];
        }
        redirect('staff_edit.php?' . http_build_query($get_vars));
        die();

    case 'history':
        if ($get_vars['staff_id'] > 0) {
            redirect('staff_history.php?' . http_build_query($get_vars));
            die();
        }
        break;

    case 'select':
        if ($get_vars['staff_id'] > 0) {
            redirect('staff_return.php?' . http_build_query($get_vars));
            die();
        }
        break;

    case 'view':
        if ($get_vars['staff_id'] > 0) {
            redirect('staff_view.php?' . http_build_query($get_vars));
            die();
        }
        break;

    case 'delete':
        if ($get_vars['staff_id'] > 0) {
            redirect('/webadmin/users/admin.php?action=delete&id=' . $get_vars['staff_id']);
            die();
        }
        break;

    case 'list':
    default:
        unset($get_vars['staff_id']);
        redirect('staff_list.php?' . http_build_query($get_vars));
        die();
}
