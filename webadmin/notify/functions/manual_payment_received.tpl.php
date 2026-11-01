<?php
//Inputs:
// - $details
// - - payment_id

moduleLoad('member'); //memberQuery, memberSearch
moduleLoad('payment');

global $DB;

$payment = null;
if (0 != $template_vars['[PAYMENT_ID]'] && function_exists("paymentFromDatabase")) {
    $payment = paymentFromDatabase($template_vars['[PAYMENT_ID]']);
}
?><!DOCTYPE html>
<html>
<head>
    <style type="text/css">
/*<![CDATA[*/
table,td,th,tr{font-family:inherit;font-size:inherit}
/*]]>*/
    </style>
    <title>[EMAIL_SUBJECT]</title>
</head>
<body><div style="font-family:verdana;margin:36px;font-size:11px;color:#777;line-height:20px;">
<p><b>Status: <span style="color:#f00;">Manual Payment Received</span></b></p>
    <!-- <p><?= $template_vars['[PAYMENT_ID]'] ?></p> -->
    <p>Payment received successfully. The details are as follows :</p>
<?php
$member_id = 0;
if (preg_match_all('/\b\d+\b/', $payment["MerchTxnRef"], $match)) {
    $extra = $DB->query_first(sprintf(
        "SELECT * FROM `highwaygroup`.`credit_payments` WHERE `credit_id` = %d LIMIT 1",
        $match[0][1]
    ), MYSQLI_ASSOC);
    if ($extra) {
        $country = 'Unknown';
        if ($temp = getCountryInfo($extra["credit_ip"])) {
            $country = sprintf(
                '<i class="flagicon-%s" title="%s"></i>',
                strtolower($temp['country']['iso_code']),
                htmlspecialchars($temp['country']['names']['en'])
            );
            unset($temp);
        }

        if (function_exists('memberQuery') && function_exists('memberSearch')) {
            $member_query = memberQuery([
                "email_address" => $extra["credit_email"],
            ]);
            $member_list = memberSearch($member_query);
            if (count($member_list) == 1) {
                $member_id = reset($member_list);
            }
        }

?>
    <table border="1" cellspacing="0" cellpadding="2" style="width:100%;margin:auto;border-collapse:collapse;border:1px solid #ccc;font-size:inherit;">
        <tr>
            <th style="width:25%;text-align:left;background:#eee;color:#333">Company Name</th>
            <td><?=htmlspecialchars($extra["credit_company"])?></td>
        </tr>
        <tr>
            <th style="width:25%;text-align:left;background:#eee;color:#333">Your Name</th>
            <td><?=htmlspecialchars($extra["credit_name"])?></td>
        </tr>
        
        <tr>
            <th style="width:25%;text-align:left;background:#eee;color:#333">Reference#</th>
            <td><?=htmlspecialchars($extra["credit_ref"])?></td>
        </tr>
        <tr>
            <th style="width:25%;text-align:left;background:#eee;color:#333">Email Address</th>
            <td><?=htmlspecialchars($extra["credit_email"])?></td>
        </tr>
        <tr>
            <th style="width:25%;text-align:left;background:#eee;color:#333">Customer IP</th>
            <td><?=htmlspecialchars($extra["credit_ip"])?></td>
        </tr>
        <tr>
            <th style="width:25%;text-align:left;background:#eee;color:#333">Customer Country</th>
            <td><?=$country?></td>
        </tr>
        <tr>
            <th style="width:25%;text-align:left;background:#eee;color:#333">Notes/Comment</th>
            <td style="line-height: 70px;"><?=htmlspecialchars($extra["credit_notes"])?></td>
        </tr>
        <tr>
            <th style="width:25%;text-align:left;background:#eee;color:#333">Customer Amount</th>
            <td><?=htmlspecialchars(getCurrencySymbol($extra["credit_currency"], "UTF8") . convertCurrency($extra["credit_currency_amount"], $extra["credit_currency"]) . " " . $extra["credit_currency"])?></td>
        </tr>
    </table>
    <br />
<?php
}
}
?>
    <table border="1" cellspacing="0" cellpadding="2" style="margin:auto;border-collapse:collapse;border:1px solid #ccc;font-size:inherit;">
        <tr>
            <th style="width:25%;text-align:left;background:#eee;color:#333">Transaction ID</th>
            <td><?=htmlspecialchars($payment["TransactionNo"])?></td>
        </tr>
        <tr>
            <th style="width:25%;text-align:left;background:#eee;color:#333">Receipt Number</th>
            <td><?=htmlspecialchars($payment["ReceiptNo"])?></td>
        </tr>
        <tr>
            <th style="width:25%;text-align:left;background:#eee;color:#333">Purchase Amount</th>
            <!-- <td>$<?=convertCurrency($payment["Amount"], "AUD")?> AUD</td> -->
            <td>$<?=$payment["Amount"]?> USD</td>
        </tr>
        <tr>
            <th style="width:25%;text-align:left;background:#eee;color:#333">Card Type</th>
            <td><?=htmlspecialchars($payment["Card"])?></td>
        </tr>

        <tr>
            <th style="width:25%;text-align:left;background:#eee;color:#333">Reference Name</th>
            <td><?=htmlspecialchars($payment["MerchTxnRef"])?></td>
        </tr>
        <tr>
            <th style="width:25%;text-align:left;background:#eee;color:#333">Reference Info</th>
            <td><?=htmlspecialchars($payment["OrderInfo"])?></td>
        </tr>
        <tr>
            <th style="width:25%;text-align:left;background:#eee;color:#333">Result Code</th>
            <td><?=htmlspecialchars($payment["TxnResponseCode"])?></td>
        </tr>
        <tr>
            <th style="text-align:left;background:#eee;color:#333">Result Message</th>
            <td><?=htmlspecialchars($payment["Message"])?></td>
        </tr>
        <tr>
            <th style="width:25%;text-align:left;background:#eee;color:#333">Approval</th>
            <td><?=htmlspecialchars($payment["result_approval"])?></td>
        </tr>
        <tr>
            <th style="width:25%;text-align:left;background:#eee;color:#333">Authorise Id</th>
            <td><?=htmlspecialchars($payment["AuthorizeId"])?></td>
        </tr>
        <tr>
            <th style="width:25%;text-align:left;background:#eee;color:#333">Batch Number</th>
            <td><?=htmlspecialchars($payment["BatchNo"])?></td>
        </tr>
        <tr>
            <th style="width:25%;text-align:left;background:#eee;color:#333">3DS Enrolled</th>
            <td><?=htmlspecialchars($payment["3DSenrolled"] ?? $_GET['vpc_3DSenrolled'])?></td>
        </tr>
        <tr>
            <th style="width:25%;text-align:left;background:#eee;color:#333">Raw Data</th>
            <td><?= http_build_query($_GET, '', ' : ');?></td>
        </tr>
    </table>
    <br />
    <p>
        Links :
<?php
if (0 != $member_id) {
?>
        <a href="<?=htmlspecialchars("https://www.highwaygroup.net/webadmin/new_members/?member_id=" . $member_id)?>" target="_blank">View Member</a>
<?php
}
?>
        <a href="https://www.highwaygroup.net/webadmin/payment/?action=view&payment_id=<?=htmlspecialchars($payment["id"])?>" target="_blank">View Receipt</a>
    </p>
    </div></body>
</html>
