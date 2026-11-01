<?php
include_once __DIR__ . '/common.inc.php';
moduleLoad('phpmailer');
moduleLoad('template');
use PHPMailer\PHPMailer\PHPMailer;
/**
 * @param mixed[] $posted
 *
 * @return mixed[]
 */
function MediaPartnerFromPost($posted)
{
    $result = socialMediaPartnerFromDatabase($posted['id']) ?? socialMediaPartnerDefault();

    foreach ($posted as $k => $v) {
        if (is_array($v)) {
            $result[$k] = implode(',', $v);
        } else {
            $result[$k] = $v;
        }
    }

    $result['category'] = json_encode(explode(',', $result['category']));
    $result['media_links'] = json_encode(explode(',', $result['media_links']));

    return socialMediaPartnerVirtual($result);
}

/**
 * @param mixed[] $posted
 *
 * @return mixed[]
 */
function mediaContactFromPost($posted)
{
    $result = socialMediaContactFromDatabase($posted['id']) ?? socialMediaContactDefault();

    foreach ($posted as $k => $v) {
        if (is_array($v)) {
            $result[$k] = implode(',', $v);
        } else {
            $result[$k] = $v;
        }
    }

    return socialMediaContactVirtual($result);
}

/**
 * @param mixed[][] $posted
 *
 * @return mixed[][]
 */
function mediaContactsFromPost($posted)
{
    $result = [];
    foreach ($posted ?? [] as $v) {
        $result[] = mediaContactFromPost($v);
    }

    return $result;
}
$action = $_REQUEST['action'];
switch ($_REQUEST['action'] ?? '') {
    case 'do_toggle':
        $id = $_REQUEST['id'];
        socialMediaPartnerSetValue($id, 'active', (socialMediaPartnerHasValue($id, 'active', 'y')) ? 'n' : 'y');

        $_GET['action'] = 'list';
        break;
    
    case 'do_add':
        //no break
    case 'do_edit':
        ini_set('display_errors', '1');
        ini_set('display_startup_errors', '1');
        error_reporting(E_ALL);
        $posted = $_POST;
        $item   = MediaPartnerFromPost($posted['item']);
        $id     = socialMediaPartnerToDatabase($item);

        if (!empty(trim($posted['admin_comment']) ?? '')) {
            partnerCommentAdd($id, $posted['admin_comment']);
        }
        partnerLinksDelete($id);
                   
        if (count($posted['mediatype']) > 0) {
            foreach ($posted['mediatype'] as $key => $mediaType) {
                partnerLinksAdd($id, $mediaType);
            }
        }
        $item_contacts = mediaContactsFromPost($posted['item_contacts'] ?? []);
        socialMediaContactsToDatabase($id, $item_contacts);
        foreach ($posted['item_contacts_deleted'] ?? [] as $contact_id) {
            if (!empty($contact_id)) {
                socialMediaContactDelete($contact_id);
            }
        }

        $action = 'edit';
        $_GET['id']     = $id;
        break;
    case 'send_update_to_partner':
        $data = mediaPartnedNewsletterFromPost($posted);
        $updateId = mediaPartnedNewsletterToDatabase($data);
        if(!isset($posted['id'])){
            $criteria = [];
            if($posted['category'] > 0){
               $criteria['category']  = $posted['category'];
            }
            if($posted['member_type']){
               $criteria['member_type']  = $posted['member_type'];
            }
            if($posted['country']){
               $criteria['country']  = explode(',',$posted['country']);
            }
            $criteria['active'] = 'y';
            $item_query = socialMediaPartnerQuery($criteria,[], []);
            $item_list  = socialMediaPartnerSort($item_query, $sort_by);
            foreach ($item_list as $key => $value) {
                $final_list =  array(
                    'update_id' => $updateId,
                    'partner_id'     => $value,
                    'email_status'  => 'listed',
                );
                mediaUpdateEmailsToDatabase($final_list);
            }

              $DB->query("UPDATE media_update_emails SET email_status='listed', date_sending=NULL WHERE email_status='sending' AND (date_sending IS NOT NULL) AND DATE_ADD(date_sending,INTERVAL 15 MINUTE)<NOW()");

                $partner_list = mediaPartnermailinglistFromDatabase($updateId);
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
                foreach ($partner_list as $k => $v) {
                    $by_status[$v['email_status']][] = $k;
                }
                $posted['email_id'] = array();
                if (count($by_status['sending']) || count($by_status['listed'])) {
                    /**
                     * Select X emails to send, and mark them as sending
                     */
                    $to_send = reset(array_chunk($by_status['listed'], 20));
                    if (is_array($to_send) && count($to_send)) {
                        $DB->query("UPDATE media_update_emails SET email_status='sending', date_sending=NOW() WHERE id IN(" . implode(',', $to_send) . ")");
                        $newsletter = mediaPartnedNewsletterFromDatabase($updateId);
                        $site_list = getWebsitesList();
                        $from_name = $site_list[$_GLOBALS['site_settings']['site_id']];
                        $mailer           = new PHPMailer();
                        $mailer->From     = 'media@arcooda.com';
                        $mailer->FromName = $from_name;
                        $mailer->Sender   = 'no-reply@highwaygroup.net';
                        $mailer->CharSet  = 'utf-8';
                        $mailer->isHTML(true);
                        if($newsletter['attached_file']){
                            $dir= socialMediaPartnerGetOptionList('screenshot_path');
                            foreach(explode(',',$newsletter['attached_file']) as $key => $file) {
                                 $fdata = explode('-', $file);
                                 $mailer->addAttachment($dir . $fdata[0],$fdata[1]);
                            }
                        }
                        foreach ($to_send as $k) {
                            $row  = $partner_list[$k];
                            $partnerDeatail = socialMediaPartnerFromDatabase($row['partner_id']);
                            $vars            = socialMediaPartnerTemplateVars($partnerDeatail);
                            foreach ($vars as $key => $value) {
                                if (trim($value) == '') {
                                    unset($vars[$key]);
                                }
                            }
                            $templater = new Template();
                            $templater->set_vars($vars);
                            $templater->set_contents($newsletter['content']);
                            $message = $templater->apply();
                            $templater->set_contents($newsletter['subject']);
                            $subject  = $templater->apply(); 
                            $mailer->Subject  = $subject;
                            $mailer->Body    = nl2br($message);
                            $mailer->AltBody = $message;
                            $mailer->AddAddress($partnerDeatail['email_address'], trim($partnerDeatail['name'] . ' ' . $partnerDeatail['surname']));
                            if (0 == $row['id']) {
                                //Do Nothing
                            } elseif ($mailer->Send()) {
                                $DB->query("UPDATE media_update_emails SET email_status='sent', date_sent=NOW() WHERE id=" . intval($row['id']) . " LIMIT 1");
                            } else {
                                $DB->query("UPDATE media_update_emails SET email_status='error' WHERE id=" . intval($row['id']) . " LIMIT 1");
                            }

                            $mailer->ClearAllRecipients();
                            $mailer->ClearCustomHeaders();
                        }
                        mediaPartnedNewsletterSetValue($updateId, 'date_sent', formatDateTime($_SERVER['REQUEST_TIME']));
                        $action = 'result';
                    }
                }
        }else{
         redirect('sendupdate.php?id=' . $updateId);
         die;  
        }
        break;
    case 'delete':
        socialMediaPartnerDelete($_GET['id']);
        $action = 'list';
        break;
}
switch ($action) {
    case 'result':
     header('Location: ' . $action . '.php?id='.$updateId);
        break;
    case 'list':
        redirect('list.php?' . http_build_query($_GET));
        break;

    case 'add':
        redirect('add.php?' . http_build_query($_GET));
        break;

    case 'edit':
        redirect('edit.php?' . http_build_query($_GET));
        break;

    case 'item_image':
        redirect('item_image.php?' . http_build_query($_GET));
        break;
    default:
        //no break
}
