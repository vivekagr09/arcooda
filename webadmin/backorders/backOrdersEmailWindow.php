<?php
include_once __DIR__ . '/common.inc.php';

global $_GLOBALS;
?>
<script language="JavaScript" type="text/javascript">
        window.resizeTo(400,300);
</script>

<?php
//echo $to.get_user().$extramessage.$id;

if (!empty($_REQUEST['to']) && '' != $_REQUEST['to']) {
    if (sendbackorderemail($_REQUEST['to'], $_GLOBALS['staff']['id'], $_REQUEST['extramessage'], $_REQUEST['id'])) {
        if (!empty($_REQUEST['extramessage']) && '' != $_REQUEST['extramessage']) {
            addtocomments($_GLOBALS['staff']['id'], $_REQUEST['extramessage'], $_REQUEST['id']);
        }
        echo '<h3>Your email was successfully sent <br> Please close this window</h3>';
    } else {
        echo '<h3>Your Email Failed. Consult a programmer</h3>';
    }
} else {
    echo '<h3>Sorry this email could not be sent, This is because the user you are sending to does not have their email address recorded in the database</h3>';
}

/**
 * @param string $to
 * @param string $from
 * @param string $extramessage
 * @param int    $id
 *
 * @return bool
 */
function sendbackorderemail($to, $from, $extramessage, $id)
{
    global $_GLOBALS;
    global $DB;

    $sendto    = $DB->query_first("SELECT `user`, `email` FROM `highwaygroup`.`users` WHERE `user` = '" . $to . "'");
    $from      = $DB->query_first("SELECT `user`, `email` FROM `highwaygroup`.`users` WHERE `user` = '" . $from . "'");
    $backorder = $DB->query_first(sprintf(
        "SELECT * FROM `highwaygroup`.`back_orders` WHERE `id` = %d LIMIT 1",
        $id
    ), MYSQLI_ASSOC);

    $subject = "Back Orders - Back Order ID: " . $id . " - " . $backorder['customer'] . ' - ' . $backorder['product_name'];

    $message = '<html><head><title>Back Orders: ID ' . $id . ' - ' . $backorder['customer'] . ' - ' . $backorder['product_name'] . '</title></head>';

    $message .= '<body><p>' . $extramessage . '</p>';

    $message .= '<p>Please note you have been sent a request from ' . $_GLOBALS['staff']['name'] . ' to look at the following back order</p>';

    $message .= '<p><a href="https://www.arcooda.com/webadmin/backorders/backOrders.php?action=edit&id=' . $id . '">Click Here To Take a Look</a></p></body></html>';

    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";

    $headers .= "To: " . niceusername($sendto['user']) . " <" . $sendto['email'] . ">\r\n";
    $headers .= "From: Back Orders <sales@arcooda.com>\r\n";

    if (mail($sendto['email'], $subject, $message, $headers)) {
        // echo $message.$headers;
        return true;
    } else {
        return false;
    }
}

/**
 * @param string $from
 * @param string $extramessage
 * @param int    $id
 *
 * @return void
 */
function addtocomments($from, $extramessage, $id)
{
    global $DB;
    $message = 'Email Excerpt:<br />';
    $message .= $extramessage;
    $DB->query(sprintf(
        "INSERT INTO `highwaygroup`.`backorder_comment` (`backorder`, `date`, `person`, `comment`) VALUES (%d, NOW(), $s, $s)",
        $id,
        $DB->escape($from),
        $DB->escape($message)
    ));
}
