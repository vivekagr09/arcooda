<?php
/***********************************************************************
 ** Title.........:
 ** Version.......:
 ** Author........:    Xiang Wei ZHUO <c9807224@studentmail.newcastle.edu.au>
 ** Filename......:
 ** Last changed..:
 ** Notes.........:
 ***********************************************************************/

include_once __DIR__ . '/common.inc.php';

global $DB;
global $_GLOBALS;

include_once __DIR__ . '/header.inc.php';

include_once __DIR__ . '/email_functions.php';
include_once __DIR__ . '/auto_contents.php';
include_once __DIR__ . '/mailer/htmlMimeMail.php';

if (!isset($time) && 'yes' != $_REQUEST['resume'] && 'send' == $_REQUEST['action2']) {
    echo "Generating the email...<br />";
    generate_email();
    $vars = get_mailout_vars();
    $DB->query(sprintf(
        "INSERT INTO `mailout_archive` (`date_sent`, `user_id`, `subject`, `html_contents`, `text_contents`, `mail_key`) VALUES (NOW(), %d, %s, %s, %s, %s)",
        $_GLOBALS['staff']['id'],
        formatSQLString($vars['subject']),
        formatSQLString($vars['html_message']),
        formatSQLString($vars['text_message']),
        formatSQLString($vars['mail_key'])
    ));
}

/**
 * @return void
 */
function send()
{
    global $resume, $DB;
    $email_vars = get_mailout_vars();
    if (!isset($email_vars['num_sent']) || "" == $email_vars['num_sent']) {
        $email_vars['num_sent'] = 0;
    }

    echo "Sent " . $email_vars['num_sent'] . " emails.<br />";
    $temp    = $DB->query_first("SELECT MAX(`counter`) FROM `mailout_tmp_list`");
    $counter = intval($temp[0]);
    ////////// TEST EMAIL ONLY /////////////
    if ('yes' == $_GET['testonly']) {
        $name  = $_POST['test_name'];
        $email = $_POST['test_email'];

        $html_message = str_replace("[name]", $name, $email_vars['html_message']);
        $text_message = str_replace("[name]", $name, $email_vars['text_message']);
        $subject      = str_replace("[name]", $name, stripslashes($email_vars['subject']));

        $html_message = str_replace("[email]", $email, $html_message);
        $text_message = str_replace("[email]", $email, $text_message);
        $subject      = str_replace("[email]", $email, $subject);

        send_mail(++$counter, $name, $email, $subject, $html_message, $text_message, "html");
        return;
    }
    ////////////////////////////////////////
    ////////// SEND TO BOSS FIRST //////////
    if (0 == $counter) {
        $name         = "Steve Josifovski";
        $html_message = str_replace("[name]", $name, $email_vars['html_message']);
        $text_message = str_replace("[name]", $name, $email_vars['text_message']);
        $subject      = str_replace("[name]", $name, stripslashes($email_vars['subject']));

        $email        = "steve@highway.net.au";
        $html_message = str_replace("[email]", $email, $html_message);
        $text_message = str_replace("[email]", $email, $text_message);
        $subject      = str_replace("[email]", $email, $subject);

        send_mail(++$counter, $name, $email, $subject, $html_message, $text_message, "html");
    }
    ///////////////////////////////////////
    $where_clause = "";
    if ('yes' == $resume) {
        $where_clause = "`response` != 'Sent'";
    } else {
        $where_clause = "`counter` = 0";
    }

    $statement = "SELECT `email`, `name`, `type` FROM `mailout_tmp_list` WHERE $where_clause ORDER BY `name` LIMIT 50";
    $result    = $DB->query($statement);
    if ($DB->num_rows($result) > 0) {
        while ($row = $DB->fetch_array($result)) {
            $counter++;

            $name         = $row['name'];
            $html_message = str_replace("[name]", $name, $email_vars['html_message']);
            $text_message = str_replace("[name]", $name, $email_vars['text_message']);
            $subject      = str_replace("[name]", $name, stripslashes($email_vars['subject']));

            $email        = $row['email'];
            $html_message = str_replace("[email]", $email, $html_message);
            $text_message = str_replace("[email]", $email, $text_message);
            $subject      = str_replace("[email]", $email, $subject);

            $type = $row['type'];
            send_mail($counter, $name, $email, $subject, $html_message, $text_message, $type);
        }
        include 'js_refresh.inc.php';
    } else {
        clean_up();
    }

}

/**
 * @param int    $counter
 * @param string $name
 * @param string $email
 * @param string $subject
 * @param string $html
 * @param string $text
 * @param string $type
 *
 * @return void
 */
function send_mail($counter, $name, $email, $subject, $html, $text, $type)
{
    global $DB, $email_vars, $attachment_files, $attachment_path;

    static $mail = null;
    if (is_null($mail)) {
        $mail = new htmlMimeMail();

        //for each of the attachments we add it
        //to the email list.
        for ($j = 0; $j < count($attachment_files); $j++) {
            $file       = $attachment_files[$j];
            $filename   = "$attachment_path$file";
            $attachment = $mail->getfile($filename);
            //add it to the email
            $mail->addAttachment($attachment, $file, file_type($file));
        }

        $mail->setReturnPath('sales@arcooda.com');
        $mail->setFrom('"Coinop Express" <sales@arcooda.com>');
        $mail->setHeader('X-Mailer', 'Coinop Express Mailer');
        $mail->setSMTPParams(null, null, null, true, "no-reply@highwaygroup.net", "fWL9gnEI");
    }

    $mail->setSubject($subject);
    if ('text' == $type) {
        $mail->setText($text);
    } else {
        $mail->setHtml($html, $text);
    }

    //$mail->build_message();

    echo "<br /> " . ($counter) . " [" . date("d-m-Y H:i:s") . "] Sending : $name &lt;$email&gt; ... ";

    //now send it!
    $response = $mail->send(array("\"$name\" <$email>") /*, "smtp"*/);
    if ($response) {
        $response  = 'Sent';
        $statement = "UPDATE `mailout_vars` SET `param` = `param` + 1 WHERE `var` = 'num_sent'";
        $DB->query($statement);
    } else {
        $response = 'Error in sending';
    }

    echo '<font color="#ff0000">' . $response . '</font><br />';
    $statement = "UPDATE `mailout_tmp_list` SET `response` = " . formatSQLString($response) . ", `date_sent` = NOW(), `counter` = " . intval($counter) . " WHERE `email` = " . formatSQLString($email);
    $DB->query($statement);
}

switch ($_REQUEST['action2']) {
    case 'send':
        send();
        break;
}
include_once '../footer.inc.php';
