<?php

//to be used for product notification
include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/header.inc.php';

$include_footer = true;

include __DIR__ . '/HE_mail/class.html.mime.mail.inc';

function send($section, $from, $product_id, $max_tries = 3)
{
    global $DB;

    $section = addslashes($section);

    $from = urldecode($from);

    $max_tries = intval($max_tries);

    if ($max_tries <= 0) {
        $max_tries = 3;
    }

    $statement = "SELECT * FROM generic_mailout WHERE section='$section' AND tries < $max_tries AND product_id = '$product_id' AND active = 'y' LIMIT 0,10";

    $result = $DB->query($statement);

    $num_emails = $DB->num_rows($result);

    while ($row = $DB->fetch_array($result)) {
        $response = sendMail($row['name'],
            $row['email'],
            stripslashes($row['subject']),
            stripslashes($row['html_msg']),
            stripslashes($row['text_msg']),
            $from);
        if (true == $response) {
            $statement = "UPDATE generic_mailout SET tries = $max_tries, date_sent = NOW() WHERE id = '$row[id]'";
            echo '<li>' . $row['email'] . '.....SENT</li>';

            $statement2 = "UPDATE product_notify_email SET notified = 'y' WHERE email = '$row[email]' AND product_id = '$product_id'";
            $DB->query($statement2);

        } else {
            $statement = "UPDATE generic_mailout SET tries = tries + 1 WHERE id = '$row[id]'";
            echo '<li>' . $row['email'] . '..... tries ' . ($row['tries'] + 1) . '/' . $max_tries . '</li>';
        }

        $DB->query($statement);

    }

    $DB->free_result($result);
    $statement  = "SELECT * FROM generic_mailout WHERE section='$section' AND tries < $max_tries AND product_id = '$product_id' AND active = 'y' LIMIT 0,10";
    $result     = $DB->query($statement);
    $num_emails = $DB->num_rows($result);
    $DB->free_result($result);

    if ($num_emails > 0) {

        $link = "&from=" . urlencode($from) . "section=$section&max_tries=$max_tries&product_id=$product_id";
?>
<script>

function go()
{
	window.open('generic_mail.php?action=send_generic_mail<?=$link;?>&time=<?=time();?>','_self','toolbar=yes,location=yes,status=yes,menubar=yes,scrollbars=yes,resizable=yes');
}

setTimeout('go()', 5000);

</script>

<?php
} else {
        echo "<br><Br><b>Finished Sending Emails.</b>";
    }
}

/**
 * @param string $name
 * @param string $email
 * @param string $subject
 * @param string $html_message
 * @param string $text_message
 * @param string $from
 *
 * @return bool
 */
function sendMail($name, $email, $subject, $html_message, $text_message, $from)
{
    $mail = new html_mime_mail(array('X-Mailer: Html Mime Mailer'));
    $mail->add_html($html_message, $text_message);
    $mail->build_message();

    //now send it!
    return $mail->send($name, $email, $from, $from, $subject);
}

if ('' != $action && 'send_generic_mail' == $action) {
    send($section, $from, $product_id, $max_tries);
}

if ($include_footer) {
    include_once __DIR__ . '/footer.inc.php';
}
