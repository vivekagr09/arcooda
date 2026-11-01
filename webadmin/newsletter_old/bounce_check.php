<?php
include_once __DIR__ . '/common.inc.php';

header("Content-Type: text/plain");

$time_start = microtime_float();
define("_PATH_BMH", $_SERVER['DOCUMENT_ROOT'] . "/webadmin/home_common/3rdparty/phpmailer-bmh/");
include _PATH_BMH . "class.phpmailer-bmh.php";

$bmh = new BounceMailHandler();
/*
$bmh->action_function    = "callbackAction";
$bmh->verbose            = VERBOSE_SIMPLE; //VERBOSE_REPORT; //VERBOSE_DEBUG; //VERBOSE_QUIET;
$bmh->use_fetchstructure = false;
$bmh->testmode           = true;
$bmh->debug_body_rule    = true;
$bmh->debug_dsn_rule     = true;
$bmh->purge_unprocessed  = true;
 */
$bmh->isCustomHeader = true;
$bmh->customHeader   = 'X-Mailing-ID';
//$bmh->disable_delete = true;
//$bmh->openLocal("/var/spool/mail/root");
$bmh->mailhost         = "localhost"; // your mail server
$bmh->mailbox_username = "no-reply@highwaygroup.net"; // your mailbox username
$bmh->mailbox_password = "fWL9gnEI"; // your mailbox password
$bmh->port             = 143; // the port to access your mailbox, default is 143
$bmh->service          = "imap"; // the service to use (imap or pop3), default is 'imap'
$bmh->service_option   = "notls"; // the service options (none, tls, notls, ssl, etc.), default is 'notls'
$bmh->boxname          = "INBOX"; // the mailbox to access, default is 'INBOX'
//$bmh->moveHard           = true; // default is false
//$bmh->hardMailbox        = "INBOX.hardtest"; // default is 'INBOX.hard' - NOTE: must start with 'INBOX.'
//$bmh->moveSoft           = true; // default is false
//$bmh->softMailbox        = "INBOX.softtest"; // default is 'INBOX.soft' - NOTE: must start with 'INBOX.'
//$bmh->deleteMsgDate      = "2009-01-05"; // format must be as 'yyyy-mm-dd'
$bmh->openMailbox();

$bmh->processMailbox();

/**
 * rest used regardless what type of connection it is
 */
$time_end = microtime_float();
$time     = $time_end - $time_start;
echo "Seconds to process: " . $time . "<br />";

/**
 * @return float
 */
function microtime_float()
{
    list($usec, $sec) = explode(" ", microtime());
    return ((float) $usec + (float) $sec);
}

/**
 * Callback (action) function
 *
 * @param int          $msgnum        the message number returned by Bounce Mail Handler
 * @param string       $bounce_type   the bounce type: 'antispam','autoreply','concurrent','content_reject','command_reject','internal_error','defer','delayed'        => array('remove'=>0,'bounce_type'=>'temporary'),'dns_loop','dns_unknown','full','inactive','latin_only','other','oversize','outofoffice','unknown','unrecognized','user_reject','warning'
 * @param string       $email         the target email address
 * @param string       $subject       the subject, ignore now
 * @param string       $xheader       the XBounceHeader from the mail
 * @param string       $cheader
 * @param bool         $remove        remove status, 1 means removed, 0 means not removed
 * @param false|string $rule_no       Bounce Mail Handler detect rule no.
 * @param false|string $rule_cat      Bounce Mail Handler detect rule category.
 * @param string       $rule_msg
 * @param int          $totalFetched  total number of messages in the mailbox
 *
 * @return bool
 */
function callbackAction($msgnum, $bounce_type, $email, $subject, $xheader, $cheader, $remove, $rule_no = false, $rule_cat = false, $rule_msg = '', $totalFetched = 0)
{
    global $DB;

    $temp    = explode(':', $cheader);
    $cheader = trim(end($temp));
    if (preg_match('/^([0-9a-f]{16})$/i', $cheader, $match)) {
        $newsletter_cookie = $match[1];
        $newsletter_id     = hexdec(substr($newsletter_cookie, -8));
        $newsletter_cookie = substr($newsletter_cookie, 0, -8);
        $member_id         = hexdec(substr($newsletter_cookie, -8));
        $newsletter_cookie = substr($newsletter_cookie, 0, -8);
        $DB->query(sprintf(
            "UPDATE `newsletter_emails` SET `email_status` = 'bounced' WHERE `newsletter_id` = %d AND `member_id` = %d LIMIT 1",
            $newsletter_id,
            $member_id
        ));
    }
    //echo $msgnum . "\t" . $bounce_type . "\t" . $email . "\t" . $subject . "\t" . $xheader . "\t" . $cheader . "\t" . $remove . "\t" . $rule_no . "\t" . $rule_cat . "\t" . $rule_msg . "\t" . $totalFetched . "\n";

    return true;
}
