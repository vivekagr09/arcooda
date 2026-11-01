<?php
include_once __DIR__ . '/common.inc.php';

$safety = array('|', '(', ')');
for ($i = 0; $i < 20; $i++) {
    $safety[] = chr($i);
}

for ($i = 127; $i < 256; $i++) {
    $safety[] = chr($i);
}

foreach ($posted['invoice'] as $key => $value) {
    $posted['invoice'][$key] = str_replace($safety, ' ', $value);
}

$QID = 0;
if ($DB->query(sprintf(
    "INSERT INTO `highwaygroup`.`credit_payments` (`credit_name`, `credit_amount`, `credit_email`, `credit_ip`, `credit_ref`, `credit_date`, `credit_company`) VALUES (%s, %s, %s, %s, %s, NOW(), %s)",
    $DB->escape($posted['invoice']['bill_name']),
    $DB->escape($posted['payment']['amount']),
    $DB->escape($posted['invoice']['bill_email']),
    $DB->escape($_SERVER['REMOTE_ADDR']),
    $DB->escape($posted['invoice']['invoice_number']),
    $DB->escape($posted['invoice']['bill_company'])
))) {
    $QID = $DB->insert_id();
}

if ($QID < 1) {
    die();
}

$MC             = new merchant_credit();
$MC->user       = 'TEST' . $_GLOBALS['site_settings']['credit_user'];
$MC->return_url = $_GLOBALS['site_settings']['credit_return_url'];
$MC->connect();
$receipt = $MC->fetch_payment(
    $posted['invoice']['invoice_number'],
    $posted['payment']['amount'],
    sprintf('Credit Transaction for %06d', $QID),
    '',
    $posted['payment']['card_number'],
    sprintf('%2d%2d', $posted['payment']['expire_year'], $posted['payment']['expire_month']),
    $posted['payment']['security_code']
);
$MC->close();
$MC->__destruct();
unset($MC);

$payment_id = 0;
if (false !== $receipt) {
    $payment_list = paymentSearch(array('TransactionNo' => $receipt['TransactionNo']));
    if (count($payment_list) > 0) {
        $payment_id = end($payment_list);
    } else {
        $payment_id = paymentToDatabase($receipt);
    }

}

$payment    = paymentFromDatabase($payment_id);
$invoice_id = intval($receipt['TxnRef']);

if ('approved' == $payment['result_approval']) {
    $HGRData = $DB->query_first(sprintf(
        "SELECT `credit_name`, `credit_amount`, `credit_email`, `credit_company`, `credit_ip`, `credit_currency_amount`, `credit_currency` FROM `highwaygroup`.`credit_payments` WHERE `credit_id` = %d LIMIT 1",
        substr($receipt['OrderInfo'], 24)
    ), MYSQLI_ASSOC);

    $CustMSG = "";
    if ($row = $DB->query_first(sprintf(
        "SELECT `value` FROM `highwaygroup`.`values` WHERE `name` = 'CREDIT_RETURN_EMAILMSG' LIMIT 1"
    ), MYSQLI_ASSOC)) {
        $CustMSG = $row['value'];
    }
    $CustMSG = str_replace('[customer_name]', $HGRData['credit_name'], $CustMSG);
    $CustMSG = str_replace('[receipt]', $receipt['ReceiptNo'], $CustMSG);
    $CustMSG = str_replace('[paymentamount]', $HGRData['credit_amount'], $CustMSG);
    $CustMSG = str_replace('[transactdate]', date('d F Y'), $CustMSG);
    mail($HGRData['credit_email'], 'Payment Received: Highway Group Pty Ltd', $CustMSG, 'From: Highway Group Pty Ltd<sales@highwaygroup.net>');

    $MailMSG = "Customer Name: {$HGRData['credit_name']}\n";
    $MailMSG .= "Customer Company: {$HGRData['credit_company']}\n";
    $MailMSG .= "Customer Reference: " . substr($MerchTxnRef, 3) . "\n";
    $MailMSG .= "Customer Email: {$HGRData['credit_email']}\n";
    $MailMSG .= "Customer IP: {$HGRData['credit_ip']}\n";
    $country_info = getCountryInfo($HGRData['credit_ip']);
    $MailMSG .= "Customer Country: " . $country_info['country'] . "\n";
    $MailMSG .= "Amount: {$HGRData['credit_amount']} AUD\n";
    $MailMSG .= "Customer Amount: {$HGRData['credit_currency_amount']} {$HGRData['credit_currency']}\n";
    $MailMSG .= "\nMessage Sent To Customer:\n" . $CustMSG;

    $HGRData = $DB->query_first(sprintf(
        "SELECT `value` FROM `highwaygroup`.`values` WHERE `name` = 'CREDIT_RETURN_EMAIL' LIMIT 1"
    ), MYSQLI_ASSOC);

    mail($HGRData['value'], "New Credit Payment For HGR", $MailMSG, 'From: Highway Group Pty Ltd<sales@highwaygroup.net>');

    header("Location: https://www.highwaygroup.net/creditdone.php?amount=" . urlencode($receipt['Amount']) . "&receipt=" . urlencode($receipt['ReceiptNo']));
    exit;
}

include_once $_SERVER['DOCUMENT_ROOT'] . '/connection.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/site_info.inc.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/slideshowscript.inc.php';
$site_id   = random_site_id(true);
$site_info = get_site_info($site_id);
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <style type="text/css">
/*<![CDATA[*/
a{font-weight:bold;color:white}
/*]]>*/
        </style>
    </head>
    <body style="background-color:black;">

<table align="center" style="background-color:#003366;">
	<tr><td style="padding:2px 2px 2px 2px;"><?=make_logo($site_info)?><a href="/index.html"><img border="0" src="/images/banners/front_image_logo.jpg" width="265" height="134" alt="Highway Group"></a><?php
start_slide_show();
?></td></tr>

<tr><td style="padding:2px 2px 2px 2px;color:white;">
<?=$_SERVER['REMOTE_USER']?>
A problem occured while processing your order and our support staff have been notified.<br />
<br />
Bank Response: <?=str_replace('<', '&lt;', $payment['result_message'])?><br />
<br />
Thank you for choosing Highway Group<br />
<br />
</td></tr>

</table>

    </body>
</html>
<?php
die();
