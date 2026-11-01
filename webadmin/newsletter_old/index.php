<?php
/**
 * @var string $section_name
 * @var object $section_handler
 */
include_once __DIR__ . '/common.inc.php';

use PHPMailer\PHPMailer\PHPMailer;

moduleLoad('member'); //memberCommentAdd
moduleLoad('phpmailer');

global $_GLOBALS;
global $DB;

$action   = $_REQUEST['action'];
$staff_id = $_GLOBALS['staff']['id'];
$id       = intval($_REQUEST['id']);

//Update Actions
switch ($action) {
    case 'do_add':
        unset($_REQUEST[$section_name]['id']);
    case 'do_newsletter':
    case 'do_edit':
    case 'do_preview':
        if (is_array($_REQUEST[$section_name])) {
            if (isset($_REQUEST['newsletter_settings'])) {
                $_REQUEST['newsletter']['settings'] = serialize($_REQUEST['newsletter_settings']);
            }

            if (!isset($_REQUEST[$section_name]['id'])) {
                $_REQUEST['newsletter_list_rebuild']   = 'on';
                $_REQUEST[$section_name]['staff_id']   = $_GLOBALS['staff']['staff_id'];
                $_REQUEST[$section_name]['date_added'] = formatDateTime();
            }
            if (0 != $_REQUEST[$section_name]['style_id']) {
                unset($_REQUEST[$section_name]['description']);
            }

            $id       = $_REQUEST[$section_name]['id'];
            $is_new   = !$section_handler->exists($id);
            $old_item = $section_handler->fromDatabase($id);
            $new_item = $section_handler->fromPost($_REQUEST[$section_name]);
            $style    = $section_handler->styleFromDatabase($new_item['style_id']);
            if ($style) {
                $new_item['site_id'] = $style['site_id'];
                $new_item['sender']  = $style['sender'];
            }
            if ($is_new) {
                $new_item['staff_id']   = $_GLOBALS['staff']['staff_id'];
                $new_item['date_added'] = formatDateTime();
                $new_item['time_added'] = time();
            }
            $id             = $section_handler->toDatabase($new_item);
            $new_item['id'] = $_REQUEST['id'] = $id;
            if ($is_new || $old_item['style_id'] != $new_item['style_id'] || $old_item['settings'] != $new_item['settings']) {
                $file = __DIR__ . '/newsletters/style_' . $new_item['style_id'] . '.tpl.php';
                if (is_file($file)) {
                    ob_start();
                    $item          = $new_item;
                    $item_settings = unserialize($new_item['settings']);
                    $_STACK[]      = $id;
                    include $file;
                    $id                      = array_pop($_STACK);
                    $new_item['description'] = $section_handler->generateMailMerge(ob_get_clean(), array(
                        'staff_name'    => $_GLOBALS['staff']['first_name'],
                        'newsletter_id' => $id,
                    ));
                }
                $section_handler->toDatabase($new_item);
            }
            $delta = array();
            if ($is_new || '' != $_REQUEST['newsletter_list_rebuild']) {
                $final_list = $section_handler->generateMemberList($new_item);
                foreach ($final_list as $key => $value) {
                    $final_list[$key] = array(
                        'newsletter_id' => $id,
                        //'member_id' => ,
                        'email_address' => $value,
                        'email_status'  => 'listed',
                    );
                }

                $section_handler->mailinglistToDatabase($id, $final_list);
                $delta[] = "Rebuilt mailing list: " . count($final_list) . " Results";
            }
            foreach ($new_item as $key => $value) {
                if ($old_item[$key] != $new_item[$key] && !is_array($new_item[$key]) && !is_array($old_item[$key])) {
                    $delta[] = $key . ' changed from: ' . formatSQLString($old_item[$key]) . ' to: ' . formatSQLString($new_item[$key]);
                }
            }

            if ('' != $_REQUEST['comments']) {
                $section_handler->commentToDatabase($id, $_REQUEST['comments']);
                $delta[] = 'Comment added: ' . formatSQLString($_REQUEST['comments']);
            }
            if (count($delta)) {
                $section_handler->commentToDatabase($id, "Changes Made: \n" . implode("\n", $delta), 'sql');
            }

            if ('do_newsletter' == $action) {
                $action = 'cont_newsletter';
            } elseif ('do_preview' == $action) {
                $action = 'cont_preview';
            } else {
                $action = 'edit';
            }

            if ('' != $_REQUEST['return_message']) {
                $action = 'select';
            }

        } else {
            $action = 'error';
        }
        break;

    case 'unlock':
        $section_handler->setLock($id);
        $action = 'list';
        break;

    case 'toggle':
        $section_handler->setValue($id, 'display_online', (($section_handler->getValue($id, 'display_online') == 'y') ? 'n' : 'y'));
        $action = 'list';
        break;

    case 'do_comment':
        if ($section_handler->exists($id) && $_REQUEST['comments']) {
            $section_handler->commentToDatabase($id, $_REQUEST['comments']);
        }

        $action = 'statistics';
        break;
}

if (('cont_newsletter' == $action || 'cont_preview' == $action) && $section_handler->HasValue($id, 'active', 'y')) {
    //Relist any failed emails... probably due to timeout
    $DB->query("UPDATE `newsletter_emails` SET `email_status` = 'listed', `date_sending` = NULL WHERE `email_status` = 'sending' AND (`date_sending` IS NOT NULL) AND DATE_ADD(`date_sending`, INTERVAL 15 MINUTE) < NOW()");

    if ('cont_preview' == $action) {
        $member_list = array(
            array(
                'member_id'    => 0,
                'id'           => 0,
                'email_status' => 'listed',
            ),
        );
    } else {
        $member_list = $section_handler->mailinglistFromDatabase($id);
    }

    $by_status = array(
        'bounced'      => array(),
        'clicked'      => array(),
        'error'        => array(),
        'listed'       => array(),
        'ordered'      => array(),
        'sending'      => array(),
        'sent'         => array(),
        'unsubscribed' => array(),
        'viewed'       => array(),
    );
    foreach ($member_list as $k => $v) {
        $by_status[$v['email_status']][] = $k;
    }

    $_REQUEST['email_id'] = array();
    if (count($by_status['sending']) || count($by_status['listed'])) {
        /**
         * Select X emails to send, and mark them as sending
         */
        $temp    = array_chunk($by_status['listed'], 20);
        $to_send = reset($temp);
        if (is_array($to_send) && count($to_send)) {
            $DB->query("UPDATE `newsletter_emails` SET `email_status` = 'sending', `date_sending` = NOW() WHERE `id` IN(" . implode(', ', $to_send) . ")");

            $newsletter          = $section_handler->fromDatabase($id);
            $newsletter_settings = unserialize($newsletter['settings']);

            $site_list = getWebsitesList();
            $from_name = $site_list[$_GLOBALS['site_settings']['site_id']];
            if ('' != $site_list[$newsletter['site_id']]) {
                $from_name = $site_list[$newsletter['site_id']];
            }

            //Temporary hack for from name
            $from_name = $site_list[$_GLOBALS['site_settings']['site_id']];

            $mailer           = new PHPMailer();
            $mailer->From     = $newsletter['sender'];
            $mailer->FromName = $from_name;
            $mailer->Sender   = 'no-reply@highwaygroup.net';
            $mailer->CharSet  = 'utf-8';
            $mailer->Subject  = $newsletter['subject'];
            $mailer->isHTML(true);
            /*
            $mailer->IsSMTP(true);    //SMTP Seems faster, but cannot send to remote users properly without auth
            $mailer->Host="localhost";
            $mailer->SMTPDebug=0;
            $mailer->Username='no-reply@highwaygroup.net';
            $mailer->Password='fWL9gnEI';
             */

            foreach ($to_send as $k) {
                $row                    = $member_list[$k];
                $_REQUEST['email_id'][] = $row['id'];
                $replace                = array();

                $replace = array(
                    'id'                => $row["id"],
                    'first_name'        => "",
                    'last_name'         => "",
                    'email_address'     => "",
                    'newsletter_cookie' => "",
                    'tracker_key'       => "",
                );
                $split                        = explode("<", $row["email_address"], 2);
                $replace["full_name"]         = array_shift($split);
                $split                        = explode(">", array_shift($split), 2);
                $replace["email_address"]     = array_shift($split);
                $split                        = explode(" ", $replace["full_name"], 2);
                $replace["first_name"]        = array_shift($split);
                $replace["last_name"]         = array_shift($split);
                $replace['newsletter_cookie'] = sprintf('%08x%08x', $row['id'], $newsletter['id']);
                $replace['tracker_key']       = '/mailout_' . $replace['newsletter_cookie'];

                $replace["name_url_encoded"]  = urlencode($replace["full_name"]);
                $replace["email_url_encoded"] = urlencode($replace["email_address"]);

                $mailer->AddAddress($replace['email_address'], trim($replace['first_name'] . ' ' . $replace['last_name']));

                $mailer->AddCustomHeader('X-Mailing-ID: ' . $replace['newsletter_cookie']);

                $newsletter_content = $newsletter['description'];
                $newsletter_content = $section_handler->negotiateContent($newsletter_content, $replace);
                $newsletter_content = $section_handler->generateMailMerge($newsletter_content, $replace);
                $newsletter_content = $section_handler->generateMailMerge($newsletter_content);

                $mailer->Body    = $newsletter_content;
                $mailer->AltBody = $section_handler->generateAlternateText($newsletter_content);

                if (0 == $row['id']) {
                    //Do Nothing
                } elseif ($mailer->Send()) {
                    $DB->query("UPDATE newsletter_emails SET email_status='sent', date_sent=NOW() WHERE id=" . intval($row['id']) . " LIMIT 1");
                    if (is_array($newsletter_settings)) {
                        if ($newsletter_settings['as_member_comment']['value']) {
                            memberCommentAdd($row['member_id'], implode("\r\n\r\n", [
                                'comment_title'  => $newsletter['subject'],
                                'comment_sentto' => $row['email_address'],
                                'comment_text'   => $mailer->AltBody,
                            ]));
                        }
                        if ($newsletter_settings['check_member']['value']) {
                            $DB->query(sprintf(
                                "UPDATE `highwaygroup`.`new_members` SET `check_staff_id` = %d, `check_date` = NOW() WHERE `member_id` = %d LIMIT 1",
                                $_GLOBALS['staff']['id'],
                                $row['member_id']
                            ));
                        }
                    }
                } else {
                    $DB->query("UPDATE newsletter_emails SET email_status='error' WHERE id=" . intval($row['id']) . " LIMIT 1");
                }

                $mailer->ClearAllRecipients();
                $mailer->ClearCustomHeaders();

            }

            $section_handler->setValue($id, 'date_sent', formatDateTime());
        }

        foreach ($by_status['sending'] as $k) {
            $_REQUEST['email_id'][] = $member_list[$k]['id'];
        }

    }
    $_REQUEST['email_id'] = implode(',', $_REQUEST['email_id']);
    $action               = 'newsletter_result';
}

$get_vars    = $_REQUEST;
$keep_only   = array_keys($section_handler->getOptionList('all_fields'));
$keep_only[] = 'order_by';
$keep_only[] = 'search_for';
$keep_only[] = 'page_id';
$keep_only[] = 'order';
$keep_only[] = 'multiple';
$keep_only[] = 'member_id';
$keep_only[] = 'return_message';
$keep_only[] = 'email_status';
$keep_only[] = 'email_id';
if (count($get_vars) > 0) {
    foreach ($get_vars as $key => $value) {
        if (!in_array($key, $keep_only)) {
            unset($get_vars[$key]);
        }
    }
}

switch ($action) {
    case 'edit':
        $section_handler->setLock($_REQUEST['id'], $_GLOBALS['staff']['staff_id']);
    case 'add':
    case 'search':
    case 'comments':
    case 'select':
    case 'view':
    case 'cont_newsletter':
    case 'newsletter_result':
    case 'preview_list':
    case 'statistics':
    case 'subscribers':
    case 'view_list':
        header('Location: ' . $action . '.php?' . http_build_query($get_vars));
        break;

    case 'add_member':
    case 'edit_member':
    case 'view_member':
    case 'unlock_member':
    case 'select_member':
        $get_vars['action'] = str_replace('_member', '', $action);
        header('Location: /webadmin/member/?' . http_build_query($get_vars));
        break;

    default:
        $action = 'list';
    case 'member_list':
    case 'build':
    case 'list':
        unset($get_vars['id']);
        header('Location: ' . $action . '.php?' . http_build_query($get_vars));
        break;
}
