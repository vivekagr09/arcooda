<?php
include_once __DIR__ . '/common.inc.php';

moduleLoad('product');

$invoice_id = intval($posted['invoice_id']);
$action     = $posted['action'];
if (!$invoice_id) {
    die('Invalid Parameters passed');
}

$invoice         = invoiceFromDatabase($invoice_id);
$payment_count   = intval($posted['payment_count']);
$payment         = invoicePaymentFromDatabase($invoice_id, $payment_count);
$payment_options = invoiceGetOptionList("payment_options");
$invoice_number  = invoiceFormatNumber($invoice_id, 0, $invoice['site_id']);

// Update the payment details, if submit button is clicked...
if ('do_edit' == $action) {
    $staff            = $_GLOBALS['staff'];
    $pay_amount       = $posted['payment_amount'];
    $pay_cur          = $posted['payment_currency'];
    $payment_amount   = convertCurrency($pay_amount, $pay_cur);
    $payment_amountau = convertCurrency($pay_amount, $pay_cur, 'AUD');
    $pay_txt          = ($posted['payment_options'] > 0) ? $payment_options[$posted['payment_options']] : $posted['payment_text'];
    $sql              = "UPDATE `invoice_payments` SET `payment_amount` = " . formatSQLString($payment_amount) . ", `payment_currency` = " . formatSQLString($pay_cur) . ", `payment_amountau` = " . formatSQLString($payment_amountau) . ", `payment_text` = " . formatSQLString($pay_txt) . ", `update_date` = " . formatSQLDateTime($_SERVER['REQUEST_TIME']) . ", `update_by` = " . intval($staff['staff_id']) . " WHERE `invoice_id` = " . $invoice_id . " AND `payment_count` = " . $payment_count . " LIMIT 1";
    $DB->query($sql);
?><!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8" />
		<title>Payment Edit - <?=$invoice_number?></title>
		<link type="text/css" rel="stylesheet" href="/webadmin/admin.css" />
		<link type="text/css" rel="stylesheet" href="style.css" />
		<style type="text/css">
/*<![CDATA[*/
.left_title{font-size:12px;width:25%}
table.wide{width:98%;font-size:12px}
/*]]>*/
		</style>
		<script type="text/javascript">
//<![CDATA[
function closeWindow(refresh)
{
	var parent=window.opener.window;
	if(refresh!=null && refresh) parent.location=parent.location;
	window.close();
}
//]]>
		</script>
	</head>
	<body>

<div class="invoice_content" style="text-align:center;font-size:12px;"><br /><h4>The Payment Details for <?=$invoice_number?> have been updated. The changes on the invoice page will be seen, once you refresh the invoice. Click on one of the 2 buttons below to close the page.<br /><br />
<input type="button" value="Close Window" onclick="closeWindow(false);" /><input type="button" value="Close Window (and Refresh Invoice Page)" onclick="closeWindow(true);" /></div>

	</body>
</html>
<?php
die();
}

$pay_text = $payment['payment_text'];
$pay_id   = 0;
foreach ($payment_options as $id => $txt) {
    if (formatCleanString($txt) == formatCleanString($pay_text)) {
        $pay_id = $id;
    }
}

if ($pay_id > 0) {
    $pay_text = '';
}

?><!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8" />
		<title>Payment Edit - <?=$invoice_number?></title>
		<link type="text/css" rel="stylesheet" href="/webadmin/admin.css" />
		<link type="text/css" rel="stylesheet" href="style.css" />
		<style type="text/css">
/*<![CDATA[*/
.left_title{font-size:12px;width:25%}
table.wide{width:98%;font-size:12px}
/*]]>*/
		</style>
	</head>
	<body>

<div class="invoice_content"><br /><form accept-charset="utf-8" action="" method="post"><table class="wide" align="center">
	<input type="hidden" name="action" value="do_edit" /><input type="hidden" name="invoice_id" value="<?=$invoice_id?>" />
	<input type="hidden" name="payment_count" value="<?=$payment_count?>" />
	<tr><th colspan="2">Edit Payment for <?=$invoice_number?></td></tr>
	<tr><td class="left_title">Total Invoice Payment :</td><td><?=convertCurrency($invoice['invoice_offer'], $invoice['invoice_currency']) . ' ' . $invoice['invoice_currency']?></td></tr>
	<tr><td class="left_title">Payment :</td><td><input style="width:45%;" type="text" name="payment_amount" value="<?=$payment['payment_amount']?>" /> <select name="payment_currency" style="width:50%;"><?=formatHTMLOptions(getCurrencyList(false), $payment['payment_currency'])?></select></td></tr>
	<tr><td class="left_title">Payment Notes :</td><td>
		<select name="payment_options" style="width:50%;"><?=formatHTMLOptions($payment_options, $pay_id)?></select>
		<input type="text" name="payment_text" style="width:45%;" value="<?=$pay_text?>" />
	</td></tr>
	<tr><td colspan="2" style="padding:6px;" align="center"><input type="submit" value="Update Payment" /></td></tr>
</table></form></div>

	</body>
</html>
