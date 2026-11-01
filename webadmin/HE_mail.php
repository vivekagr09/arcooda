<?php
/***********************************************************************
 ** Title.........:
 ** Version.......:
 ** Author........:    Xiang Wei ZHUO <c9807224@studentmail.newcastle.edu.au>
 ** Filename......:
 ** Last changed..:
 ** Notes.........:
 ***********************************************************************/

$section_code = "EMAILS";
include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/header.inc.php';

$script = 'HE_mail';

include __DIR__ . '/HE_mail/class.html.mime.mail.inc';
include __DIR__ . '/HE_mail/uploader.php';

$db_vars          = getEmailVars();
$attachment_files = filelist();

$save_query    = '';
$save_html_msg = '';
$save_subject  = '';

/**
 * @return string[]
 */
function getEmailVars()
{
    global $DB;

    $result = [];
    if ($query = $DB->query(sprintf(
        "SELECT `var`, `param` FROM `HE_mailout_vars`"
    ))) {
        while ($row = $DB->fetch_assoc($query)) {
            $result[$row['var']] = $row['param'];
        }
        $DB->free_result($query);
    }

    return $result;
}

/**
 * @return void
 */
function saveMessage()
{
    global $DB, $db_vars;

    $save_query    = addslashes($db_vars['querys']);
    $save_html_msg = addslashes($db_vars['html_message']);
    $save_subject  = addslashes($db_vars['subject']);

    $DB->query(sprintf(
        "INSERT INTO `HE_mail_sent` (`date_sent`, `subject`, `contents`, `querys`) VALUES (NOW(), '%s', '%s', '%s')",
        $DB->escape_string($save_subject),
        $DB->escape_string($save_html_msg),
        $DB->escape_string($save_query)
    ));

    echo "Email Saved<br>";
}

/**
 * @param string[] $vars
 *
 * @return void
 */
function setVars($vars)
{
    global $DB;

    foreach ($vars as $k => $v) {
        $DB->query(sprintf(
            "UPDATE `HE_mailout_vars` SET `param` = '%s' WHERE `var` = '%s' LIMIT 1",
            $DB->escape_string($k),
            $DB->escape_string($v)
        ));
    }
}

/**
 * @param int $id
 *
 * @return void
 */
function previewOldEmails($id)
{
    global $DB;

    $send_mail = $DB->query_first(sprintf(
        "SELECT *, `contents` AS `html_message` FROM `HE_mail_sent` WHERE `id` = %d",
        $id
    ), MYSQLI_ASSOC);
/*
$vars['html_message'] = $send_mail['html_message'];

$vars['text_message'] = trim(word_wrap(strip_tags($vars['html_message'])));
$vars['text_message'] = str_replace(["\t", "  "], '', $vars['text_message']);
$vars['text_message'] = preg_replace("/\\n\\n+/", "\n", $vars['text_message']);

$statement = $send_mail['querys'];

$DB->query('DELETE FROM HE_mailout');
$DB->query($statement);

$vars['total'] = 0;
if ($row = $DB->query_first(sprintf(
    "SELECT COUNT(*) AS `total` FROM `HE_mailout`"
), MYSQLI_ASSOC)) {
    $vars['total'] = $row['total'];
}

set_vars($vars);
 */
    include __DIR__ . '/HE_mail/mail_compose.inc.php';

}

/**
 * @return void
 */
function preview()
{
    global $DB;
    global $vars, $db_vars, $EditorValue;

    include __DIR__ . '/HE_mail/word_wrap.inc';

    $vars['html_message'] = $EditorValue;
    $vars['text_message'] = trim(word_wrap(strip_tags($vars['html_message'])));
    $vars['text_message'] = str_replace(["\t", "  "], '', $vars['text_message']);
    $vars['text_message'] = preg_replace("/\\n\\n+/", "\n", $vars['text_message']);

    $vars['total'] = 0;
    if ($row = $DB->query_first(sprintf(
        "SELECT COUNT(*) AS `total` FROM `HE_mailout`"
    ), MYSQLI_ASSOC)) {
        $vars['total'] = $row['total'];
    }

    $vars['files'] = fileList_text();
    setVars($vars);
    $db_vars = getEmailVars();

    include __DIR__ . '/HE_mail/mail_preview.inc.php';
}

/**
 * @return void
 */
function send()
{
    global $DB;
    global $db_vars, $resume;

    echo "Total Emails: $db_vars[total]<BR><BR>";

    $where_clause = " response = '' ";

    if ('yes' == $resume) {
        $where_clause .= " OR response != 'Sent' ";
    }

    $count = 1;
    if ($query = $DB->query(sprintf(
        "SELECT `email`, `name` FROM `HE_mailout` WHERE (%s) AND `counter` < 5 LIMIT 0, 10",
        $where_clause
    ))) {
        while ($row = $DB->fetch_assoc($query)) {
            $name  = $row['name'];
            $email = $row['email'];

            $html_message = str_ireplace("[name]", $name, $db_vars['html_message']);
            $text_message = str_ireplace("[name]", $name, $db_vars['text_message']);
            $subject      = str_ireplace("[name]", $name, $db_vars['subject']);

            $html_message = str_ireplace("[email]", $email, $html_message);
            $text_message = str_ireplace("[email]", $email, $text_message);
            $subject      = str_ireplace("[email]", $email, $subject);

            sendMail($count, $name, $email, $subject, $html_message, $text_message);
            $count++;
        }
        $DB->free_result($query);
    }

    if ($count > 1) {
        include __DIR__ . '/HE_mail/js_refresh.inc.php';
    } else {
        cleanUp();
    }

}

/**
 * @return void
 */
function cleanUp()
{
    global $DB;
    global $attachment_files, $attachment_path;

    //need to delete all the attchment files.
    $DB->query(sprintf(
        "UPDATE `HE_mailout_vars` SET `param` = '0' WHERE `var` = 'count' LIMIT 1"
    ));

    if (count($attachment_files) > 0) {
        echo "<br>Cleaing up attachment files...";

        for ($i = 0; $i < count($attachment_files); $i++) {
            unlink($attachment_path . $attachment_files[$i]);
        }

        echo " OK<br>";
    }

    echo "Done<br>";
}

/**
 * @param int $count
 * @param string $name
 * @param string $email
 * @param string $subject
 * @param string $html_message
 * @param string $text_message
 *
 * @return void
 */
function sendMail($count, $name, $email, $subject, $html_message, $text_message)
{
    global $DB;
    global $db_vars, $attachment_files, $attachment_path;

    $mail = new html_mime_mail(['X-Mailer: Arcooda Mailer', 'Return-Path: no-reply@highwaygroup.net']);

    $mail->add_html($html_message, $text_message);

    //for each of the attachments we add it
    //to the email list.
    for ($j = 0; $j < count($attachment_files); $j++) {
        $file     = $attachment_files[$j];
        $filename = "$attachment_path$file";

        $attachment = $mail->get_file($filename);

        //add it to the email
        $mail->add_attachment($attachment, $file, file_type($file));

    } /*for*/

    $mail->build_message();

    echo "<BR> " . ($db_vars['count'] + $count - 1) . " [" . formatDateTime();
    echo "] Sending $count: $name &lt;$email&gt; ...";

    //now send it!
    $response = $mail->send($name, $email, 'Arcooda', 'sales@arcooda.com', $subject);

    if (true == $response) {
        $response  = 'Sent';
        $DB->query(sprintf(
            "UPDATE `HE_mailout_vars` SET `param` = `param` + 1 WHERE `var` = 'count' LIMIT 1"
        ));

    } else {
        $response = 'Error in sending';
    }

    echo "<FONT COLOR=red>$response</FONT><br>";
    $DB->query(sprintf(
        "UPDATE `HE_mailout` SET `response` = '%s', `date_sent` = NOW(), `counter` = `counter` + 1 WHERE name = '%s' AND `email` = '%s'",
        $DB->escape_string($response),
        $DB->escape_string($name),
        $DB->escape_string($email)
    ));
}

/**
 * @return bool
 */
function compose()
{
    global $DB;

    if ($row = $DB->query_first(sprintf(
        "SELECT COUNT(*) AS `total` FROM `HE_mailout` WHERE `response` != 'Sent'"
    ), MYSQLI_ASSOC)) {
        if ($row['total'] > 0) {
            include __DIR__ . '/HE_mail/list_not_empty.inc.php';
            return false;
        }
    }
    $send_mail = getEmailVars();
    include __DIR__ . '/HE_mail/mail_compose.inc.php';
    return true;
}

/**
 * @return void
 */
function createList()
{
    global $DB;

    $DB->query(sprintf(
        "TRUNCATE TABLE `HE_mailout`"
    ));
    $DB->query(sprintf(
        $sql = "INSERT INTO `HE_mailout` (`name`, `email`) SELECT `name`, `email` FROM `HE_mailinglist` WHERE `active` = '1'"
    ));

    setVars(['querys' => $sql]);
}

/**
 * @return void
 */
function resetList()
{
    global $DB;

    //need to delete all the attchment files.
    cleanUp();

    $DB->query(sprintf(
        "TRUNCATE TABLE `HE_mailout`"
    ));

    echo "Temporary List Cleared.";

    echo "<BR><BR>";
}

switch ($_REQUEST['action']) {
    case 'old_mail':
        previewOldEmails(intval($_REQUEST['id']));
        break;
    case 'preview':
        createList();
        preview();
        break;
    case 'do_send':
        saveMessage();
    case 'send':
        send();
        break;
    case 'reset':
        resetList();
    default:
        compose();
        break;
}

include_once __DIR__ . '/footer.inc.php';
