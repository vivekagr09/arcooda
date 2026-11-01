<!-- payment_information.tpl -->

<?php
//$invoice
//$invoice_payments
//$invoice_type

if ('' == $invoice['payment_terms_id'] || 0 == $invoice['payment_terms_id']) {
    $invoice['payment_terms_id'] = 4;
}

$payment_due = $invoice['total_invoice_cost'];
foreach ($invoice_payments as $payment) {
    $payment_due = bcsub($payment_due, convertCurrency($payment['payment_amount'], $payment["payment_currency"], $invoice["invoice_currency"]), 2);
}

$all_staff_list = invoiceGetOptionList("staff_id");
/*
$commweb2_resultcode = array();
$query = $DB->query("SELECT `result_code`, `result_message` FROM `converters`.`commweb2_resultcode`");
while($row = $DB->fetch_assoc($query)) $commweb2_resultcode[$row["result_code"]] = $row["result_message"];
$DB->free_result($query);

$anz_resultcode = array();
$query = $DB->query("SELECT `result_code`, `result_message` FROM `converters`.`anz_resultcode`");
while($row = $DB->fetch_assoc($query)) $anz_resultcode[$row["result_code"]] = $row["result_message"];
$DB->free_result($query);

$commweb_resultcode = array();
$query = $DB->query("SELECT `result_code`, `result_message` FROM `converters`.`commweb_resultcode`");
while($row = $DB->fetch_assoc($query)) $commweb_resultcode[$row["result_code"]] = $row["result_message"];
$DB->free_result($query);
 */
$invoice_declines = array();

$query = $DB->query("SELECT *, UNIX_TIMESTAMP(`created`) AS `created_time` FROM `invoice_declines` WHERE `invoice_id` = " . intval($invoice_id));
while ($row = $DB->fetch_assoc($query)) {
    $invoice_declines[] = $row;
}

$DB->free_result($query);
/*
$query = $DB->query("SELECT `BatchNo`, `Amount`, `Card`, `TxnResponseCode`, IF(`AcqResponseCode` = 'null', '--', `AcqResponseCode`) AS `AcqResponseCode` FROM `highwaygroup`.`credit_transactions` WHERE `BatchNo` != '0' AND `MerchTxnRef` = '" . intval($invoice_id) . "' AND (`TxnResponseCode` != '0' OR `AcqResponseCode` != '00')");
while($row = $DB->fetch_assoc($query))
{
$split = str_split($row["BatchNo"], 2);
$invoice_declines[] = array(
"reason" => $commweb2_resultcode[$row["TxnResponseCode"] . $row["AcqResponseCode"]],
"comments" => sprintf("Requested amount: $%s AUD\nResponse code: %s%s\nCard Type: %s", bcmul($row["Amount"], "0.01", 2), $row["TxnResponseCode"], $row["AcqResponseCode"], $row["Card"]),
"created_time" => mktime(0, 0, 0, intval($split[2]), intval($split[3]), intval($split[0] . $split[1])),
);
}
$DB->free_result($query);

$query = $DB->query("SELECT * FROM `paypal_transaction` WHERE `invoice` = '" . intval($invoice_id) . "' AND `payment_status` != 'Completed'");
while($row = $DB->fetch_assoc($query)) $invoice_declines[] = array(
"reason" => $row["payment_status"],
"comments" => ($row["payment_status"] == "Pending") ? sprintf("Payment amount: $%s %s\nPayer status: %sPending Reason: %s", $row["mc_gross"], $row["mc_currency"], $row["payer_status"], $row["pending_reason"]) : sprintf("Payment amount: $%s %s", $row["mc_gross"], $row["mc_currency"]),
"created_time" => strtotime($row["payment_date"]),
);
$DB->free_result($query);
 */
?>

<script type="text/javascript">
//<![CDATA[
<?php
if ($invoice_lock['staff_id'] == $staff_id) {
?>
$(function() {
	$("#decline_add_button").click(function() {
		window.open("addDecline.php?invoice_id=<?= $invoice_id ?>");
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
});
function setDeclineData(data)
{
	$("#decline_table").append("<tr><td style=\"text-align:center;\">" + data.created + "</td><td style=\"text-align:center;color:#f00;\">" + data.reason + "</td><td>" + data.comments + "</td></tr>").find("tr").removeClass("alt").filter(":odd").addClass("alt");
}
//]]>
</script>

<?= formatHTMLTitle('<a href="#" onclick="toggleSection(\'sectionPaymentInfo\');return false">Payment Information</a>') ?>

<div id="sectionPaymentInfo">
	<table class="wide">
		<tr>
			<td class="left_title" style="width:25%;">Payment Method</td>
			<td><select name="paymenttype_id">
				<option value="">Not Selected</option>
				<?= formatHTMLOptions(purchaseOrderGetOptionList("paymenttype_id"), $invoice['paymenttype_id']) ?>
			</select></td>
		</tr>
		<tr>
			<td class="left_title">Payment Terms</td>
			<td>
				<select name="payment_terms_id" style="width:80%;">
					<option value="0">Not Selected</option>
					<?= formatHTMLOptions(purchaseOrderGetOptionList("payment_terms_id"), $invoice['payment_terms_id']) ?>
				</select>
				<a href="invoice_options.php?section=payment_terms" target="_blank">Add / Edit</a>
			</td>
		</tr>
<?php
/*if ($invoice_type > 1)*/ if (1){
?>
		<tr>
			<td class="left_title">Payment Due</td>
			<td><input type="text" name="payment_due" readonly="readonly" value="<?php hecho($payment_due)?>" style="text-align:right;" />&nbsp;<b><span name="order_currency"><?php hecho($invoice['order_currency'])?></span></b></td>
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
					<?= formatHTMLOptions(purchaseOrderGetOptionList("payment_options"),$invoice['payment_options']) ?>
				</select>
				<input type="text" name="payment_notes" style="width:215px;" value="<?= $invoice['payment_notes'] ?>"/>
			</td>
		</tr>
		<tr>
			<td class="left_title">&nbsp;</td>
			<td><input type="button" value="Update"<?= ($invoice_lock['staff_id'] == $staff_id || 'add' == $action) ? ' onclick="if (validateInvoice()) this.form.submit();"' : ' disabled="disabled"' ?> /></td>
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
				</tr>
				<tr>
					<td style="text-align:left;">Order</td>
					<td style="text-align:center;"><?= date('d/m/y', (($invoice['proforma_time'] > 0) ? $invoice['proforma_time'] : $_SERVER['REQUEST_TIME'])) ?></td>
					<td style="text-align:center;"><?= sprintf('%.2f', $invoice['order_offer']) ?> <span name="invoice_currency"><?= $invoice['order_currency'] ?></span></td>
					<td style="text-align:center;">&nbsp;</td>
					<td style="text-align:center;">&nbsp;</td>
				</tr>
<?php
foreach ($invoice_payments as $payment) {
?>
				<tr>
					<td style="text-align:left;"><?=(bccomp($payment['payment_amount'], '0.00') < 0) ? 'Refund' : 'Payment'?></td>
					<td style="text-align:center;"><?= date('d/m/y', $payment['payment_time']) ?></td>
					<td style="text-align:center;"><?= sprintf('%.2f', 0 - $payment['payment_amount']) ?> <span name="invoice_currency"><?= $payment['payment_currency'] ?></span></td>
					<td style="text-align:center;"><?= $all_staff_list[$payment['staff_id']] ?></td>
					<td style="text-align:center;padding:3px;"><?= nl2br(hecho(wordwrap($payment['payment_text'], 50, "\n", true), 1)) . (($payment['update_time'] > 0) ? '<br />' . 'Manually Updated By :<br />' . $all_staff_list[$payment['update_by']] . ' on ' . date('d/m/y', $payment['update_time']) : '') ?></td>
				</tr>
<?php
}
?>
				<tr>
					<td style="text-align:left;border-top:1px solid black;">Balance</td>
					<td style="text-align:center;border-top:1px solid black;"><?= date('d/m/y', $_SERVER['REQUEST_TIME']) ?></td>
					<td style="text-align:center;border-top:1px solid black;background-color:#<?= ($payment_due > 0) ? 'e9' : '9e' ?>a;"><?= sprintf('%.2f', $payment_due) ?> <span name="invoice_currency"><?= $invoice['invoice_currency'] ?></span></td>
					<td colspan="2" style="border-top:1px solid black;">&nbsp;</td>
				</tr>
			</table></td>
		</tr>
		<tr>
			<td class="left_title">
	            <h4 style="padding: 5px;">Payment Receipt </h4>
	            <input onclick="addgenericimage(0,3);return false;" type="button" value="Add Image">
	        </td>
	         <td>
	            <table class="wide" style="border: 0px;margin-top: 10px;">
	                <tr class="alt" id="supplier_payment_information_attachment">
	                   <?php 
                            $proimages=purchaseOrderimagesFromDatabase($order_id,3,0);
                            foreach ($proimages as $key => $images) { ?>
                                <td style="width:20%" id="generic_product_attach_id_<?=$images['id']?>"> 
                                    <table class="wide" style="border: 0px;margin-top: 10px;"> 
                                        <tr class="alt"> <td colspan="2"> 
                                            <img src="addGenericAttachment.php?action=view&id=<?=$images['id']?>" width="100px;"></td> </tr> <tr class="alt"> <td class="right_title" style="width: 150px;"><?=$images['caption']?></td> <td><img src="/svg/remove.png" style="float:left;" onclick="deleteGenericImage(<?=$images['id']?>);"></td> </tr> <tr class="alt"> <td colspan="2"> <div><input type="hidden" name="payment_image[<?=$images['id']?>][id]" value="<?=$images['id']?>"><input type="hidden" name="payment_image[<?=$images['id']?>][type]" value="<?=$images['image_type']?>"><input type="checkbox" name="payment_image[<?=$images['id']?>][purchase_order_inc]" value="1" <?php echo ($images['purchase_order_inc']==1)?'checked':'';?>> Include In Purchase Order</div> <div><input type="checkbox" name="payment_image[<?=$images['id']?>][email_attachment]" value="1" <?php echo ($images['email_attachment']==1)?'checked':'';?> > Email Attachment</div> </td> 
                                         </tr>  
                                        </table> 
                                    </td>
                           <?php } ?>
	                </tr>
	            </table>
	        </td>
		</tr>

		<tr>
			<td class="left_title">Payment Made</td>
			<td><select name="payments_located" style="background-color:<?= ('y' == $invoice['payments_located']) ? '#00FF00' : '#FF0000' ?>;" onchange="updateSelect(this);return false;"><?= formatHTMLOptions(array('n' => 'No', 'y' => 'Yes'), $invoice['payments_located']) ?></select></td>
		</tr>
<?php
}

if ('y' == $invoice['payments_located']) {
?>
		<tr style="color:red;font-weight:bold;">
			<td class="left_title">Authorised</td>
			<td>
				<i><?= $all_staff_list[$invoice['payments_staff_id']] ?> - <?= date('d/m/y h:i', $invoice['payments_locate_time']) ?></i>

			</td>
		</tr>
<?php
}
?>
	</table>
	<br />
	<div style="text-align:center;"><input id="decline_add_button" style="display:none;" type="button" value="Add Failed Attempt" /></div>
	<br />

	<br />
</div>

<div style="background-color:white;text-align:center;padding:8px 8px 8px 8px;display:none;" id="sectionPaymentInfo_hideNote">
	Payment Information can be viewed by Clicking "Payment Information" above<br />
</div>


<!-- /payment_information.tpl -->
