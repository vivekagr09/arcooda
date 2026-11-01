<!-- inquiries.tpl.php -->

<?php
moduleLoad('invoice'); //invoiceGetOptionList, invoiceFromDatabase

if (isset($member_inquiries) && is_array($member_inquiries) && moduleLoad('invoice')) {
    $pt = array(
        0 => "/webadmin/images/other.jpg",
        1 => "/webadmin/images/parts.jpg",
        2 => "/webadmin/images/machines.jpg",
        3 => "/webadmin/images/pcbs.jpg",
        4 => "/webadmin/images/merchandises.jpg",
    );

    $paymenttype_list = invoiceGetOptionList('paymenttype_id');
    $status_list      = invoiceGetOptionList('invoice_action');
?>

<?=formatHTMLTitle('<a href="#" onclick="toggleSection(\'sectionMemberInquiries\');return false">Our Purchase Inquires</a>', '<i><a href="/webadmin/purchase_order/?action=add&amp;status=inquiry&amp;member_id=' . $member_id . '" style="color:#000;" target="_blank">Create Purchase Inquiry</a> | <a href="/webadmin/purchase_order/?action=list&amp;member_id=' . $member_id . '&amp;status=inquiry|enquiry|closed|cancelled" target="_blank">View All Inquires (' . $member['total_inquiries'] . ')</a></i>', false)?>

<div id="sectionMemberInquiries">
	<table class="table_grid">
		<tr>
			<th>Date</th>
			<th>Inquiry #</th>
			<th>Type</th>
			<th>Value</th>
			<th>Handled</th>
			<th>Status</th>
		</tr>
<?php
    foreach ($member_inquiries as $invoice_id) {
        $invoice = invoiceFromDatabase($invoice_id);
?>
		<tr>
			<td style="text-align: center;"><?=date("dS F, Y", $invoice['open_time'])?></td>
			<td style="text-align: center;"><a href="/webadmin/invoice/?action=edit&amp;invoice_id=<?=$invoice['id']?>"><?php hecho($invoice['invoice_number'])?></a></td>
			<td style="text-align: center;"><img src="<?=$pt[$invoice['producttype_id']]?>" width="63" height="46" alt="<?=$pt[$invoice['producttype_id']]?>" /></td>
			<td style="text-align: center;"><?=sprintf('%s%s %s', getCurrencySymbol($invoice['invoice_currency']), convertCurrency($invoice['invoice_offer'], $invoice['invoice_currency']), $invoice['invoice_currency'])?></td>
			<td style="text-align: center;"><?php hecho($invoice['last_staff'])?></td>
			<td style="text-align: center;"><span class="status_<?=strtolower($invoice['invoice_status'])?>"><?=$invoice['invoice_status']?></span></td>
		</tr>
<?php
    }
?>
	</table>
	<br />
	<input type="button" onclick="<?php hecho("window.open(" . jecho("/webadmin/invoice/?action=list&member_id=" . $member_id . "&status=inquiry|enquiry|closed|cancelled", 1) . ")")?>" value="View All Inquires (<?=$member['total_inquiries']?>)" />
	<br />
	<br />
	<br />
</div>

<div style="background-color:#fff;text-align:center;padding:8px;display:none;" id="sectionMemberInquiries_hideNote">Customer Inquiries can be viewed by Clicking "Customer Inquiries" above</div>

<?php
}
?>

<!-- /inquiries.tpl.php -->
