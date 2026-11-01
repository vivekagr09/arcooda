<?php
 // Import the class properly
include_once __DIR__ . '/common.inc.php';
moduleLoad('phpmailer');
use PHPMailer\PHPMailer\PHPMailer;
$action   = $posted['action'];
$staff_id = $_GLOBALS['staff']['id'];
$id       = intval($posted['id']);
/*if ($query = $DB->query("SELECT * FROM `highwaygroup`.`new_members` WHERE `site_id` LIKE '39' AND `mailing_list` = 'y' AND `status` != 'not_active'")) {
    while ($row = $DB->fetch_assoc($query)) {
         if ($row1 = $DB->query_first(sprintf(
            "SELECT * FROM `highwaygroup`.`new_members_mailing_list` WHERE `member_id`= %d AND `style_id`= 12 LIMIT 1",
            $row['member_id']
        ), MYSQLI_ASSOC)) {
            $DB->query(sprintf(
                "UPDATE %s SET `active` = 'y' WHERE `id` = %d LIMIT 1",
                '`highwaygroup`.`new_members_mailing_list`',
                $row1['id']
            ));
        }else{
             $DB->query("INSERT INTO `highwaygroup`.`new_members_mailing_list` (`member_id`, `style_id`, `active`, `date_joined`) VALUES (" . intval($row['member_id']) . ",12, " . formatSQLString('y') . ", " . formatSQLString($row['date_joined']) . ")");
        }
    }
    $DB->free_result($query);
}*/
//Update Actions
switch ($action) {
    case 'do_add':
        unset($posted[$section_name]['id']);
    case 'do_newsletter':
    case 'do_edit':
    case 'do_preview':
    case 'do_spamcheck':
        if (is_array($posted[$section_name])) {
            if (isset($posted['newsletter_settings'])) {
                $posted['newsletter']['settings'] = serialize($posted['newsletter_settings']);
            }

            if (!isset($posted[$section_name]['id'])) {
                $posted['newsletter_list_rebuild']   = 'on';
                $posted[$section_name]['staff_id']   = $_GLOBALS['staff']['staff_id'];
                $posted[$section_name]['date_added'] = substr(formatSQLDateTime($_SERVER['REQUEST_TIME']), 1, -1);
            }
            if (0 != $posted[$section_name]['style_id']) {
                unset($posted[$section_name]['description']);
            }

            $id                  = $posted[$section_name]['id'];
            $is_new              = !$section_handler->exists($id);
            $old_item            = $section_handler->fromDatabase($id);
            $new_item            = $section_handler->fromPost($posted[$section_name]);
            $style               = $section_handler->styleFromDatabase($new_item['style_id']);
            $new_item['site_id'] = $style['site_id'];
            $new_item['sender']  = $style['sender'];
            if ($is_new) {
                $new_item['staff_id']   = $_GLOBALS['staff']['staff_id'];
                $new_item['date_added'] = substr(formatSQLDateTime($_SERVER['REQUEST_TIME']), 1, -1);
                $new_item['time_added'] = $_SERVER['REQUEST_TIME'];
            }
            $id             = $section_handler->toDatabase($new_item);
            $new_item['id'] = $posted['id'] = $id;
            if ($is_new || $old_item['style_id'] != $new_item['style_id'] || $old_item['settings'] != $new_item['settings']) {
                $file = 'newsletters/style_' . $new_item['style_id'] . '.tpl.php';
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
            if ($is_new || '' != $posted['newsletter_list_rebuild']) {
                $final_list = $section_handler->generateMemberList($new_item);
                foreach ($final_list as $key => $value) {
                    $final_list[$key] = array(
                        'newsletter_id' => $id,
                        'member_id'     => $value,
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

            if ('' != $posted['comments']) {
                $section_handler->commentToDatabase($id, $posted['comments']);
                $delta[] = 'Comment added: ' . formatSQLString($posted['comments']);
            }
            if (count($delta)) {
                $section_handler->commentToDatabase($id, "Changes Made: \n" . implode("\n", $delta), 'sql');
            }

            if ('do_newsletter' == $action) {
                $action = 'cont_newsletter';
            } elseif ('do_preview' == $action) {
                $action = 'cont_preview';
            } elseif ('do_spamcheck' == $action) {
                $action = 'cont_spamcheck';
            } else {
                $action = 'edit';
            }

            if ('' != $posted['return_message']) {
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
        if ($section_handler->exists($id) && $posted['comments']) {
            $section_handler->commentToDatabase($id, $posted['comments']);
        }

        $action = 'statistics';
        break;
}

if (('cont_newsletter' == $action || 'cont_preview' == $action || 'cont_spamcheck' == $action) && $section_handler->HasValue($id, 'active', 'y')) {
    //Relist any failed emails... probably due to timeout
    $DB->query("UPDATE highwaygroup.newsletter_emails SET email_status='listed', date_sending=NULL WHERE email_status='sending' AND (date_sending IS NOT NULL) AND DATE_ADD(date_sending,INTERVAL 15 MINUTE)<NOW()");

    if ('cont_preview' == $action || 'cont_spamcheck' == $action) {
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

    $posted['email_id'] = array();
    if (count($by_status['sending']) || count($by_status['listed'])) {
        /**
         * Select X emails to send, and mark them as sending
         */
        $to_send = reset(array_chunk($by_status['listed'], 20));
        if (is_array($to_send) && count($to_send)) {
            $DB->query("UPDATE highwaygroup.newsletter_emails SET email_status='sending', date_sending=NOW() WHERE id IN(" . implode(',', $to_send) . ")");

            $site_list = getWebsitesList();
            $from_name = $site_list[$_GLOBALS['site_settings']['site_id']];
            if ('' != $site_list[$newsletter['site_id']]) {
                $from_name = $site_list[$newsletter['site_id']];
            }

            //Temporary hack for from name
            $from_name = $site_list[$_GLOBALS['site_settings']['site_id']];

            $newsletter = $section_handler->fromDatabase($id);

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
            $mailer->Password='Y1HUUQD9';
             */
            foreach ($to_send as $k) {
                $row                  = $member_list[$k];
                $posted['email_id'][] = $row['id'];
                $replace              = array();
                if (0 == $row['member_id']) {
                    $replace = array(
                        'id'                => 0,
                        'first_name'        => $_GLOBALS['staff']['first_name'],
                        'last_name'         => $_GLOBALS['staff']['last_name'],
                        'email_address'     => $_GLOBALS['staff']['email'],
                        'newsletter_cookie' => '',
                        'tracker_key'       => '',
                    );
                    $mailer->AddAddress($replace['email_address'], trim($replace['first_name'] . ' ' . $replace['last_name']));
                } else {
                    $replace                      = memberFromDatabase($row['member_id']);
                    $replace['newsletter_cookie'] = sprintf('%08x%08x', $replace['id'], $newsletter['id']);
                    $replace['tracker_key']       = '/mail/' . $replace['newsletter_cookie'];

                    $mailer->AddAddress($replace['email_address'], trim($replace['first_name'] . ' ' . $replace['last_name']));

                    $mailer->AddCustomHeader('X-Mailing-ID: ' . $replace['newsletter_cookie']);
                }

                $newsletter_content = $newsletter['description'];
                $newsletter_content = $section_handler->negotiateContent($newsletter_content, $replace);
                $newsletter_content = $section_handler->generateMailMerge($newsletter_content, $replace);
                $newsletter_content = $section_handler->generateMailMerge($newsletter_content);

                $mailer->AddCustomHeader('Precedence: bulk');
                if (preg_match("@href=\"([^\"]+unsubscribe[^\"]+)\"@", $newsletter_content, $m)) {
                    $mailer->AddCustomHeader("List-Unsubscribe: <" . $m[1] . ">");
                }

                $mailer->Body    = $newsletter_content;
                $mailer->AltBody = $section_handler->generateAlternateText($newsletter_content);

                if ('cont_spamcheck' == $action) {
                    header("Content-Type:application/json; charset=utf-8");
                    $mailer->preSend();
                    $content = "Received: by john.ourhunter.com (Postfix, from userid 48)\r\n";
                    $content .= "	id " . md5(time()) . "; " . date("D, j M Y H:i:s O (T)\r\n");
                    $content .= $mailer->GetSentMIMEMessage();
                    $ret = array();
                    exec("echo " . escapeshellarg($content) . " | /usr/bin/spamc -R", $ret);

                    $score = reset(explode("/", array_shift($ret))) - 1.9;
                    while (count($ret) && substr(array_shift($ret), 0, 4) != "----");
                    foreach ($ret as $k => $v) {
                        if (strpos($v, " NO_RELAYS ") !== false || "" == $v) {
                            unset($ret[$k]);
                        } else {
                            $ret[$k] = preg_split("/ +/", trim($v), 3);
                        }
                    }

                    echo "[" . $score . "," . jecho(array_values($ret), 1) . "]";
                    die();
                } elseif (0 == $row['id']) {
                    //Do Nothing
                } elseif ($mailer->Send()) {
                    $DB->query("UPDATE highwaygroup.newsletter_emails SET email_status='sent', date_sent=NOW() WHERE id=" . intval($row['id']) . " LIMIT 1");
                } else {
                    $DB->query("UPDATE highwaygroup.newsletter_emails SET email_status='error' WHERE id=" . intval($row['id']) . " LIMIT 1");
                }

                $mailer->ClearAllRecipients();
                $mailer->ClearCustomHeaders();
            }

            $section_handler->setValue($id, 'date_sent', formatDateTime($_SERVER['REQUEST_TIME']));
        }

        foreach ($by_status['sending'] as $k) {
            $posted['email_id'][] = $member_list[$k]['id'];
        }

    }
    $posted['email_id'] = implode(',', $posted['email_id']);
    $action             = 'newsletter_result';
}

$get_vars    = $posted;
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
        $section_handler->setLock($posted['id'], $_GLOBALS['staff']['staff_id']);
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
