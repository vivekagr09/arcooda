<?php
/**
 * @var int       $invoice_id
 * @var mixed[]   $invoice
 * @var mixed[][] $invoice_payments
 * @var int       $invoice_type
 */
?>
<!-- <?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->

<?php
global $_GLOBALS;
global $DB;

$sections = array();
if (function_exists('get_allowed_sections')) {
	$sections = get_allowed_sections($_GLOBALS['staff']['user']);
}

if ('' == $invoice['payment_terms_id'] || 0 == $invoice['payment_terms_id']) {
    $invoice['payment_terms_id'] = 4;
}

$payment_due = $invoice['invoice_offer'];
foreach ($invoice_payments as $payment) {
    $payment_due = bcsub($payment_due, convertCurrency($payment['payment_amount'], $payment["payment_currency"], $invoice["invoice_currency"]), 2);
}

$all_staff_list = invoiceGetOptionList("staff_id");

$credit_resultcode = array();
if ($query = $DB->query(sprintf(
    "SELECT `result_code`, `result_message` FROM `highwaygroup`.`credit_resultcodes`"
))) {
    while ($row = $DB->fetch_assoc($query)) {
        $credit_resultcode[$row["result_code"]] = $row["result_message"];
    }
    $DB->free_result($query);
}

$invoice_declines = array();
if ($query = $DB->query(sprintf(
    "SELECT *, UNIX_TIMESTAMP(`created`) AS `created_time`, 'red' AS `color` FROM `highwaygroup`.`invoice_declines` WHERE `invoice_id` = %d",
    $invoice_id
))) {
    while ($row = $DB->fetch_assoc($query)) {
        $invoice_declines[] = $row;
    }
    $DB->free_result($query);
}
if ($query = $DB->query(sprintf(
    "SELECT `BatchNo`, `Amount`, `Card`, `Message`, `TxnResponseCode`, IF(`AcqResponseCode` = 'null', '--', `AcqResponseCode`) AS `AcqResponseCode`, `3DSenrolled` FROM `highwaygroup`.`credit_transactions` WHERE `BatchNo` != '0' AND (`MerchTxnRef` = %s OR `OrderInfo` REGEXP '^[A-Z]{2,3}#%05d[^\\\\d]' OR `MerchTxnRef` REGEXP '\\\\sinvoice:[A-Z]{2,3}#%05d[^\\\\d]')", // AND (`TxnResponseCode` != '0' OR `AcqResponseCode` != '00')",
    formatSQLString($invoice_id),
    $invoice_id,
    $invoice_id
))) {
    while ($row = $DB->fetch_assoc($query)) {
        $split              = str_split($row["BatchNo"], 2);
        $invoice_declines[] = array(
            "reason"       => trim($credit_resultcode[$row["TxnResponseCode"] . $row["AcqResponseCode"]] . ((!empty($row['Message'])) ? ("\nMessage: " . $row['Message']) : '')),
            "comments"     => sprintf(
                _("Requested amount: $%s AUD\nResponse code: %s%s\nCard Type: %s\n3DS Enrolled: %s"),
                bcmul($row["Amount"], "0.01", 2),
                $row["TxnResponseCode"],
                $row["AcqResponseCode"],
                $row["Card"],
                ('Y' === $row["3DSenrolled"]) ? 'Yes' : 'No'
            ),
            "created_time" => mktime(0, 0, 0, intval($split[2]), intval($split[3]), intval($split[0] . $split[1])),
            "color"        => ('0' != $row["TxnResponseCode"] || '00' != $row["AcqResponseCode"]) ? 'red' : 'green',
        );
    }
    $DB->free_result($query);
}
if ($query = $DB->query(sprintf(
    "SELECT * FROM `highwaygroup`.`paypal_transaction` WHERE `invoice` = %s", // AND `payment_status` != 'Completed'",
    formatSQLString($invoice_id)
))) {
    while ($row = $DB->fetch_assoc($query)) {
        $invoice_declines[] = array(
            "reason"       => $row["payment_status"],
            "comments"     => ("Pending" == $row["payment_status"]) ? sprintf("Payment amount: $%s %s\nPayer status: %sPending Reason: %s", $row["mc_gross"], $row["mc_currency"], $row["payer_status"], $row["pending_reason"]) : sprintf("Payment amount: $%s %s", $row["mc_gross"], $row["mc_currency"]),
            "created_time" => strtotime($row["payment_date"]),
            "color"        => ('Completed' != $row["payment_status"]) ? 'red' : 'green',
        );
    }
    $DB->free_result($query);
}
?>

<script type="text/javascript">
//<![CDATA[
<?php
if ($invoice_lock['staff_id'] == $_GLOBALS['staff']['id']) {
?>
$(function() {
	$("#decline_add_button").click(function() {
		window.open("addDecline.php?invoice_id=<?=$invoice_id?>");
	}).toggle(true);
});
<?php
}
?>
$(function() {
	$("[name='payment_options']").change(function() {
		if ($(this).val() == 0x0012) {
			var payment_made = $("[name='payment_made']");
			payment_made.val("-" + payment_made.val().replace("-", ""));
		}
	}).change();
	$("#payment_information_payment_method_export").click(function(e) {
		e.preventDefault();
		var invoice_id     = <?=($invoice_id) ? $invoice_id : '""'?>;
		var site_id        = $("[name='site_id']").val();
		var paymenttype_id = $("[name='paymenttype_id']").val();
		var member_id      = $("[name='member_id']").val();

		var w = 575;
		var h = 378;
		var x = (window.outerWidth - w) / 2 + window.screenLeft;
		var y = (window.outerHeight - h) / 2 + window.screenTop;

		window.open("payment_method.php?invoice_id=" + invoice_id + "&site_id=" + site_id + "&paymenttype_id=" + paymenttype_id + "&member_id=" + member_id, "_blank", "popup=1,width=" + w + ",height=" + h + ",left=" + x + ",top=" + y);
	}).show();
	$("#payment_information_payment_method_warning").click(function(e) {
		e.preventDefault();

		var w = 768;
		var h = 515;
		var x = (window.outerWidth - w) / 2 + window.screenLeft;
		var y = (window.outerHeight - h) / 2 + window.screenTop;

		window.open("wise_warning.php", "_blank", "popup=1,width=" + w + ",height=" + h + ",left=" + x + ",top=" + y);
	});
	$("[name='paymenttype_id']").change(function() {
		var val            = $(this).val();
		var site_id        = Invoice.get("site_id");
		var invoice_status = Invoice.get("invoice_status");
		$("#arcooda_payment_link").stop().toggle("39" == site_id && ("proforma" == invoice_status || "sales" == invoice_status) && ("GOOGLE" == val || "APPLE" == val || "AMEX" == val || "MASTERCARD" == val || "VISA" == val || "CREDIT" == val || "CREDIT_SECURE" == val || "PAYPAL" == val || "BANK" == val || "EUR_BANK" == val || "EUR_BANK2" == val || "GBP_BANK" == val || "GBP_BANK2" == val || "HKD_BANK" == val || "JPY_BANK" == val || "USD_BANK3" == val || "USD_BANK" == val || "USD_BANK2" == val || "USD_BANK4" == val));
		$("#payment_information_payment_method_warning").stop().toggle("USD_BANK2" == val || "USD_BANK4" == val);
	}).change();
});
function setDeclineData(data)
{
	$("#decline_table").append("<tr><td style=\"text-align:center;\">" + data.created + "</td><td style=\"text-align:center;color:#f00;\">" + data.reason + "</td><td>" + data.comments + "</td></tr>").find("tr").removeClass("alt").filter(":odd").addClass("alt");
}
function updatePaymentLink(selectElement)
{
	var selectedValue = selectElement.value;
	if(selectedValue == 'Airwallex') {
		$('#airwallex_payment_link').show();
	} else {
		$('#airwallex_payment_link').hide();
	} 
	if(selectedValue == 'EUR_BANK5') {
		$('#euro_payment_data').show();
	}else{
		$('#euro_payment_data').hide();
	}
}
function updateEurAmount()
{
	setValue("invoice_offereu", convertCurrency(getValue("payment_due"), getValue("invoice_currency"),"EUR"));

}
//]]>
</script>

<?=formatHTMLTitle('<a href="#" onclick="toggleSection(\'sectionPaymentInfo\');return false">Payment Information</a>')?>

<div id="sectionPaymentInfo">
	<table class="wide">
		<tr>
			<td class="left_title" style="width:25%;">Payment Method</td>
			<td>
				<select name="paymenttype_id" onchange="updatePaymentLink(this)" style="width:80%;">
					<option value="">Not Selected</option>
					<!-- <option value="Airwallex">Airwallex</option> -->
					<?=formatHTMLOptions(invoiceGetOptionList("paymenttype_id"), $invoice['paymenttype_id'])?>
				</select>
				<a id="payment_information_payment_method_export" class="hide" href="#">Export</a>
				<a id="payment_information_payment_method_warning" class="hide" href="#" style="color:#f00;border:1px solid #f00;display:inline-block;padding-left:4px;padding-right:4px">Warning</a>
			</td>
		</tr>
		<tr>
			<td class="left_title">Payment Terms</td>
			<td>
				<select name="payment_terms_id" style="width:80%;">
					<option value="0">Not Selected</option>
					<?=formatHTMLOptions(invoiceGetOptionList("payment_terms_id"), $invoice['payment_terms_id'])?>
				</select>
				<a href="invoice_options.php?section=payment_terms" target="_blank">Add / Edit</a>
			</td>
		</tr>
<?php
if ($invoice_type > 2) {
?>
		<tr>
			<td class="left_title">Payment Due</td>
			<td><input type="text" name="payment_due" readonly="readonly" value="<?=htmlspecialchars($payment_due)?>" style="text-align:right;" />&nbsp;<b><span name="invoice_currency"><?=htmlspecialchars($invoice['invoice_currency'])?></span></b>
				<span id="euro_payment_data" style="<?= $invoice['payment_terms_id'] == 'EUR_BANK5' ? 'display:none;':''?>">
					<input type="text" name="invoice_offereu"  value="<?=htmlspecialchars($invoice['invoice_offereu'])?>" style="text-align:right;" />&nbsp;<b><span name="invoice_currency">EUR</span>   <input type="button" onclick="updateEurAmount();" value="Reset"/>
				</span>
			</td>
		</tr>
		<tr>
			<td class="left_title" style="width:25%;">Payment Made</td>
			<td><input type="text" name="payment_made" style="text-align:right;" /></td>
		</tr>
		<tr>
			<td class="left_title">Payment Notes</td>
			<td>
				<select name="payment_options" style="width:275px;">
					<option value="0"></option>
					<?=formatHTMLOptions(invoiceGetOptionList("payment_options"))?>
				</select>
				<input type="text" name="payment_notes" style="width:215px;" />
			</td>
		</tr>
		<tr>
			<td class="left_title">&nbsp;</td>
			<td><input type="button" value="Update"<?=($invoice_lock['staff_id'] == $_GLOBALS['staff']['id'] || 'add' == $action) ? ' onclick="if (validateInvoice()) this.form.submit();"' : ' disabled="disabled"'?> /></td>
		</tr>
		<tr>
			<td class="left_title">History</td>
			<td><table style="width:96%;border-collapse:collapse;border:1px solid #ccc;" cellpadding="5" border="1" bordercolor="#CCCCCC">
				<tr style="font-size:13px;">
					<th>&nbsp;</th>
					<th>Date</th>
					<th>Amount</th>
					<th>Entered By</th>
					<th>Notes</th>
<?php
if (in_array('ADMIN', $sections)) {
?>
					<th>Edit</th>
<?php
}
?>
				</tr>
				<tr>
					<td style="text-align:left;">Order</td>
					<td style="text-align:center;"><?=date('d/m/y', (($invoice['proforma_time'] > 0) ? $invoice['proforma_time'] : time()))?></td>
					<td style="text-align:center;"><?=sprintf('%.2f', $invoice['invoice_offer'])?> <span name="invoice_currency"><?=$invoice['invoice_currency']?></span></td>
					<td style="text-align:center;">&nbsp;</td>
					<td style="text-align:center;">&nbsp;</td>
<?php
if (in_array('ADMIN', $sections)) {
?>
					<td>&nbsp;</td>
<?php
}
?>
				</tr>
<?php
foreach ($invoice_payments as $payment) {
?>
				<tr>
					<td style="text-align:left;"><?=(bccomp($payment['payment_amount'], '0.00') < 0) ? 'Refund' : 'Payment'?></td>
					<td style="text-align:center;"><?=date('d/m/y', $payment['payment_time'])?></td>
					<td style="text-align:center;"><?=sprintf('%.2f', 0 - $payment['payment_amount'])?> <span name="invoice_currency"><?=$payment['payment_currency']?></span></td>
					<td style="text-align:center;"><?=$all_staff_list[$payment['staff_id']]?></td>
					<td style="text-align:center;padding:3px;"><?=nl2br(htmlspecialchars(wordwrap($payment['payment_text'], 50, "\n", true))) . (($payment['update_time'] > 0) ? '<br />' . 'Manually Updated By :<br />' . $all_staff_list[$payment['update_by']] . ' on ' . date('d/m/y', $payment['update_time']) : '')?></td>
<?php
if (in_array('ADMIN', $sections)) {
?>
					<td><input style="<?=(!$payment['staff_id']) ? 'display:none' : ''?>" type="button" value="Edit" onclick="openWindow('payment_edit.php?invoice_id=<?=$invoice_id?>&payment_count=<?=$payment['payment_count']?>', 600, 350, true);" /></td>
<?php
}
?>
				</tr>
<?php
}
?>
				<tr>
					<td style="text-align:left;border-top:1px solid black;">Balance</td>
					<td style="text-align:center;border-top:1px solid black;"><?=date('d/m/y')?></td>
					<td style="text-align:center;border-top:1px solid black;background-color:#<?=($payment_due > 0) ? 'e9' : '9e'?>a;"><?=sprintf('%.2f', $payment_due)?> <span name="invoice_currency"><?=$invoice['invoice_currency']?></span></td>
					<td colspan="<?=(in_array('ADMIN', $sections)) ? '3' : '2'?>" style="border-top:1px solid black;">&nbsp;</td>
				</tr>
			</table></td>
		</tr>
		<!-- <tr><td>&nbsp;</td><td>&nbsp;</td></tr> -->
		<?php 
		$arcooda_payment_link = 'https://www.arcooda.com/payment/payment-checkout?' . http_build_query(array(
			'id'  => $invoice['invoice_id'],
			'crc' => $invoice['rand_num'],
		));
		?>
		<tr id="arcooda_payment_link" style="display:none;line-height: 40px;">
			<td class="left_title"><a href="<?=htmlspecialchars($arcooda_payment_link)?>" target="_blank">Payment Link</a></td>
			<td>
				<?=htmlspecialchars($arcooda_payment_link)?>
				<a href="#" onclick="<?=htmlspecialchars('navigator.clipboard.writeText(' . json_encode($arcooda_payment_link) . ');return false')?>"><i class="far fa-clone"></i></a>
			</td>
		</tr>
		<!-- <tr><td>&nbsp;</td><td>&nbsp;</td></tr> -->
		<tr>
			<td class="left_title">Payments Authorized </td>
			<td><select name="payments_located" style="background-color:<?=('y' == $invoice['payments_located']) ? '#00FF00' : '#FF0000'?>;" onchange="updateSelect(this);return false;"><?=formatHTMLOptions(array('n' => 'No', 'y' => 'Yes'), $invoice['payments_located'])?></select></td>
		</tr>
		
<?php
}

if ('y' == $invoice['payments_located']) {
?>
		<tr style="color:red;font-weight:bold;">
			<td class="left_title">Authorised</td>
			<td>
				<i><?=$all_staff_list[$invoice['payments_staff_id']]?> - <?=date('d/m/y h:i', $invoice['payments_locate_time'])?></i>

<?php
    if ('y' == $invoice['payments_located']) {
?>
					<span style="margin-left: 10px; color: #000; font-weight: normal;">
						<input type="checkbox" name="mark_paid_on_invoice" id="mark_paid_on_invoice_input" <?=(1 == $invoice['mark_paid_on_invoice']) ? 'checked' : ''?>> Mark paid on Invoice
					</span>
<?php
    }
?>
			</td>
		</tr>
<?php
}


/*$airwallex_payment_link = 'https://www.arcooda.com/payment/airwallex-checkout?' . http_build_query(array(
	'id'  => $invoice['invoice_id'],
	'crc' => $invoice['rand_num'],
));*/
?>
		
		<!-- <tr>
			<td class="left_title"><a href="<?=htmlspecialchars($airwallex_payment_link)?>" target="_blank">Airwallex Link</a></td>
			<td>
				<?=htmlspecialchars($airwallex_payment_link)?>
				<a href="#" onclick="<?=htmlspecialchars('navigator.clipboard.writeText(' . json_encode($airwallex_payment_link) . ');return false')?>"><i class="far fa-clone"></i></a>
			</td>
		</tr> -->
		
	</table>
	<br />

	<table class="table_grid wide" id="decline_table">
		<tr><th colspan="3">Recorded Payment Attempts</th></tr>
		<tr>
			<th>Date</th>
			<th>Bank Reason</th>
			<th>Comments</th>
		</tr>
<?php
$alt = true;
foreach ($invoice_declines as $decline) {
?>
		<tr<?=($alt = !$alt) ? ' class="alt"' : ''?>>
			<td style="text-align:center;"><?=(date("d/m/Y", $decline["created_time"]))?></td>
			<td style="text-align:center;color:<?=$decline['color']?>;"><?=nl2br(htmlspecialchars($decline["reason"]))?></td>
			<td><?=nl2br($decline["comments"])?></td>
		</tr>
<?php
}
?>
<!--  To display payment confirmation
 * 29-06-2023 * -->

<?php 
$invoice_payments = invoicePaymentsFromDatabase($invoice['invoice_id']);
foreach ($invoice_payments as $payments) {
	if($payments['payment_count'] == 1)
	{
?>
		<tr<?=($alt = !$alt) ? ' class="alt"' : ''?>>
			<td style="text-align:center;"><?=(date("d/m/Y", strtotime($payments["payment_date"])))?></td>
			<td style="text-align:center;color:green;"><?=nl2br(htmlspecialchars('Approved or completed successfully 
                    Message : Approved'))?></td>
			<td>
				<?= nl2br($payments["comments"])?>
			</td>
		</tr>
<?php
	}
}
?>

<!-- /* End */ -->
	</table>
	<div style="text-align:center;"><input id="decline_add_button" style="display:none;" type="button" value="Add Attempt" /></div>
	<br />

	<br />
</div>

<div style="background-color:white;text-align:center;padding:8px 8px 8px 8px;display:none;" id="sectionPaymentInfo_hideNote">
	Payment Information can be viewed by Clicking "Payment Information" above<br />
</div>

<!-- /<?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->
