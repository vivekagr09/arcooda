<?php
/**
 * @var string[] $active_staff
 * @var string[] $all_staff_list
 * @var string[] $assigned_to
 * @var string[] $assigned_to_email
 * @var string[] $status_list
 * @var string[] $website_list
 */
include_once __DIR__ . '/softjob_common.inc.php';

use PHPMailer\PHPMailer\PHPMailer;

moduleLoad('phpmailer');

global $DB;

$action = $_REQUEST['action'];

//Update Actions
switch ($action) {
    case 'do_add':
        unset($_REQUEST['softjob']['id']);
    case 'do_edit':
        $softjob_id     = $_REQUEST['softjob']['id'];
        $is_new         = !softjobExists($softjob_id);
        $old_softjob    = softjobFromDatabase($softjob_id) ?? softjobDefault();
        $new_softjob    = softjobFromPost($_REQUEST['softjob']);
        $softjob_id     = softjobToDatabase($new_softjob);
        $_REQUEST['id'] = $softjob_id;

        if ('' != $_REQUEST['comment']) {
            softjobNoteToDatabase($softjob_id, $_REQUEST['comment']);
        }

        $staff_list = array();
        //if($new_softjob['assigned']!='') $staff_list[]=$new_softjob['assigned'];
        if ('' != $new_softjob['reporter']) {
            $staff_list[] = $new_softjob['reporter'];
        }

        //if($old_softjob['assigned']!='') $staff_list[]=$old_softjob['assigned'];
        $staff_list = array_intersect(array_unique($staff_list), array_keys($active_staff));

        if ('' != $_REQUEST['skip_emails']) {
            $staff_list = array();
        }

        if ('' != $_REQUEST['comment'] || ($old_softjob['id'] > 0 && $old_softjob['status'] != $new_softjob['status']) || 0 == $old_softjob['id']) {
            $email_list = array();
            foreach ($staff_list as $staff_id) {
                $staff_mapping = softjobGetOptionList('staff_mapping');
                $coe_staff_id  = array_search($staff_id, $staff_mapping);
                if (false == $coe_staff_id) {
                    $coe_staff_id = $staff_id;
                }

                $staff = staffFromDatabase($coe_staff_id);
                if (trim($staff['email']) != '') {
                    $email_list[] = '"' . $all_staff_list[$staff_id] . '" <' . trim($staff['email']) . '>';
                }

            }
            if ('' == $_REQUEST['skip_emails']) {
                if ('' != $new_softjob['assigned']) {
                    $email_list[] = '"' . $assigned_to[$new_softjob['assigned']] . '" <' . trim($assigned_to_email[$new_softjob['assigned']]) . '>';
                }

                if ('' != $old_softjob['assigned']) {
                    $email_list[] = '"' . $assigned_to[$old_softjob['assigned']] . '" <' . trim($assigned_to_email[$old_softjob['assigned']]) . '>';
                }

            }
            $email_list = array_unique($email_list);

            $email           = new PHPMailer();
            $email->From     = 'softjobs@coinopexpress.com';
            $email->Sender   = $email->From;
            $email->FromName = 'SoftJobs';
            $email->Mailer   = "sendmail";
            $email->CharSet  = "utf-8";
            $email->Subject  = "SoftJob ID:" . $softjob_id . " - " . $new_softjob['title'] . ". Status - " . $status_list[$new_softjob['status']];
            $body            = array();
            $body[]          = "Please Note:";

            $list   = array();
            $list[] = "<ul>";
            if ($is_new) {
                $list[] = "<li>This a new job</li>";
            }

            if ('' != $_REQUEST['comment']) {
                $list[] = "<li>There has been a new comment added for this Job.</li>";
            }

            if ($old_softjob['assigned'] != $new_softjob['assigned'] && $new_softjob['assigned'] > 0) {
                $list[] = "<li>This job has been assigned to " . $all_staff_list[$new_softjob['assigned']] . ".</li>";
            }

            if ($old_softjob['status'] != $new_softjob['status'] && !$is_new) {
                $list[] = "<li>The status of this job has been changed from " . $status_list[$old_softjob['status']] . " to " . $status_list[$new_softjob['status']] . "</li>";
            }

            $list[] = "</ul>";
            $body[] = implode("\n", $list);

            $priority = 'Low';
            if ($new_softjob['priority'] < 8) {
                $priority = 'Medium';
            }

            if ($new_softjob['priority'] < 4) {
                $priority = 'High';
            }

            $body[] = "Website: " . $website_list[$new_softjob['website']];
            $body[] = "Title: " . $new_softjob['title'];
            $body[] = "Priority: " . $priority;
            $body[] = '<a href="https://www.coinopexpress.com/webadmin/new_softjobs/index.php?action=edit&id=' . $softjob_id . '" target="_blank">Click here for more details.</a>';

            $body[] = '';

            $body[]  = 'Description:';
            $table   = array();
            $table[] = "<table>";
            $table[] = "<tr><th>Date</th><th>User</th><th>Comment</th></tr>";
            $table[] = "<tr>" .
            "<td>" . date('d/m/Y', $new_softjob['time_added']) . "</td>" .
            "<td>" . htmlspecialchars($all_staff_list[$new_softjob['reporter']]) . "</td>" .
            '<td style="border-top:1px dotted black">' . nl2br(htmlspecialchars($new_softjob['description'])) . "</td>" .
                "</tr>";
            $softjob_notes = softjobNotesFromDatabase($softjob_id);
            if (count($softjob_notes) > 0) {
                foreach ($softjob_notes as $note_id) {
                    $note    = softjobNoteFromDatabase($note_id);
                    $table[] = "<tr>" .
                    "<td>" . date('d/m/Y', $note['time_added']) . "</td>" .
                    "<td>" . htmlspecialchars($all_staff_list[$note['user_id']]) . "</td>" .
                    '<td style="border-top:1px dotted black">' . nl2br(htmlspecialchars($note['comment'])) . "</td>" .
                        "</tr>";
                }
            }

            $table[] = "</table>";
            $body[]  = implode("\n", $table);

            $email->Body = implode("<br />\n", $body);
            $email->IsHTML(true);
            foreach ($email_list as $mailto) {
                $name    = explode('" <', $mailto);
                $address = substr(end($name), 0, -1);
                $name    = substr(implode('" <', $name), 1);
                if ('' != $address) {
                    $email->AddAddress($address, $name);
                }

                //if($mailto!='"" <>') $email->AddAddress($mailto);
            }
            $result = $email->Send();
            $email->ClearAddresses();
            $email->ClearAttachments();
        }

        $action = 'edit';
        break;
    case 'do_activate':
    case 'do_deactivate':
    case 'do_toggle':
        $softjob_id = intval($_REQUEST['id']);
        $DB->query(sprintf(
            "UPDATE `highwaygroup`.`bugs` SET `status` = IF(`status` = 3, 1, 3) WHERE `id` = %d",
            $softjob_id
        ));
        $action = 'list';
        break;
}

$get_vars  = $_REQUEST;
$keep_only = array('order_by', 'search_for', 'page_id', 'order', 'id', 'website', 'assigned', 'reporter', 'status', 'priority');
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
        unset($get_vars['id']);
        header('Location: softjob_add.php?' . http_build_query($get_vars));
        break;
    case 'edit':
        header('Location: softjob_edit.php?' . http_build_query($get_vars));
        break;
    case 'view':
        header('Location: softjob_view.php?' . http_build_query($get_vars));
        break;
    case 'delete':
        header('Location: softjob_delete.php?' . http_build_query($get_vars));
        break;
    case 'search':
        header('Location: softjob_search.php?' . http_build_query($get_vars));
        break;
    case 'officeprint':
        header('Location: softjob_officeprint.php?' . http_build_query($get_vars));
        break;
    case 'report':
        header('Location: softjob_report.php?' . http_build_query($get_vars));
        break;
    case 'do_report':
        $softjob_list = $_REQUEST['report_rank'];
        if (!is_array($softjob_list)) {
            $softjob_list = array();
        }

        foreach ($softjob_list as $key => $job_id) {
            if ('' == $job_id) {
                unset($softjob_list[$key]);
            }
        }

        asort($softjob_list);
        $softjob_list   = array_keys($softjob_list);
        $get_vars['id'] = implode('_', $softjob_list);
        header('Location: softjob_doreport.php?' . http_build_query($get_vars));
        break;
    default:
    case 'list':
        header('Location: softjob_list.php?' . http_build_query($get_vars));
        break;
}

die();
