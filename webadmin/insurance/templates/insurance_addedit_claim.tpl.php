<!-- insurance_addedit_claim.tpl.php -->

<script type="text/javascript">
var site_list = <?php jecho(invoiceGetOptionList('site_id')) ?>;
var all_staff_list = <?php jecho($all_staff_list) ?>;

function validateInsuranceClaim()
{
	return true;
}

function searchInvoice()
{
	var searchFor=getValue('insurance_claim[invoice_id]');
	var url='/webadmin/invoice/index.php?action=list';
	url+='&return_message=select';
	url+='&search_for='+escape(searchFor);
	var features='scrollbars, resizable';//height=700, width=800
	window.open(url,'invoice_popup',features);
}

function setInvoiceData(data)
{
	switch(data['return_message'])
	{
	case 'select': onSelectInvoice(data); break;
	}
}

function onSelectInvoice(data)
{
	//Fill the claim
	setValue('insurance_claim[invoice_id]',data['invoice_id']);
//	setValue('insurance_claim[claim_amount]',data['products_offerau']);
	setValue('insurance_claim[courier_id]',data['ship_id']);

	//Fill the invoice summary
	setValue('invoice[bill_name]',data['bill_name']);
	setValue('invoice[days_open]',data['days_open']);
	setValue('invoice[handler_note]',data['handler_note']);
	setValue('invoice[invoice_id]',data['invoice_id']);
	setValue('invoice[write_date]',data['write_date']);
	setValue('invoice[company_name]',site_list[data['site_id']]);
	setValue('invoice[handler_name]',all_staff_list[data['handler_id']]);
	setValue('invoice[invoice_offerau]',data['invoice_offerau']+' AUD');
	setValue('invoice[invoice_profitau]',data['invoice_profitau']+' AUD');
	setValue('invoice[last_staff]',all_staff_list[data['staff_id']]);
	var yesno={'y':'Yes', 'n':'No', 'yp':'Deposit'};
	setValue('invoice[payment_received]',yesno[data['payment_received']]);
	setValue('invoice[products_located]',yesno[data['products_located']]);
	setValue('invoice[ship_located]',yesno[data['ship_located']]);
<?php
	if (moduleLoad("invoice"))
	{
?>
	var status_array = <?php jecho(invoiceGetOptionList("invoice_action")) ?>;
	setValue('invoice[invoice_action]',status_array[data['invoice_action']]);
//	setValue('invoice[invoice_links]',data['invoice_links']);
//	setValue('invoice[view_url]',data['view_url']);
<?php
	}
?>
	toggleSection('invoice_summary',true);
}
</script>

<form method="post" action="index.php?action=do_<?php echo $action ?>">

<?php echo formatHTMLTitle('<a href="javascript:toggleSection(\'sectionAddClaimLoss\');">Add Claim/Loss</a>') ?>
<div id="sectionAddClaimLoss">

<input type="hidden" name="insurance_claim[insurance_id]" value="<?php echo intval($insurance_claim['insurance_id']) ?>" />
<input type="hidden" name="insurance_claim[claim_id]" value="<?php echo intval($insurance_claim['claim_id']) ?>" />

<table class="wide">
	<tr>
		<td class="left_title" style="width:25%;">Date</td>
		<td><input name="claim_date" style="width:236px;" value="<?php echo ($insurance_claim['claim_time']!=0)?date('Y-m-d',$insurance_claim['claim_time']):'' ?>" /><input style="width:20px;" value="V" onclick="gfPop.fPopCalendar(getElement('claim_date'))" type="button" /></td>
	</tr>
	<tr>
		<td class="left_title">Invoice ID</td>
		<td><input name="insurance_claim[invoice_id]" style="width:236px;" value="<?php hecho($insurance_claim['invoice_id']) ?>" /><input value="S" onclick="searchInvoice()" type="button" /></td>
	</tr>
	<tr>
		<td class="left_title">Claim Amount</td>
		<td>$<input name="insurance_claim[claim_amount]" style="text-align:right;" type="text" value="<?php hecho($insurance_claim['claim_amount']) ?>" /> AUD</td>
	</tr>
	<tr>
		<td class="left_title">Type Of Damage</td>
		<td><select name="insurance_claim[claim_type]">
			<option value=""></option>
			<?php echo formatHTMLOptions(array('loss'=>'Lost Shipment','damage'=>'Damage in Transit'),$insurance_claim['claim_type']) ?>
		</select></td>
	</tr>
	<tr>
		<td class="left_title">Courier</td>
		<td><select name="insurance_claim[courier_id]">
			<option value=""></option>
<?php
$freight_list=getAllFreightServices();
asort($freight_list);
?>
			<?php echo formatHTMLOptions($freight_list,$insurance_claim['courier_id']) ?>
		</select></td>
	</tr>
	<tr>
		<td class="left_title">Notes/Comments</td>
		<td><textarea name="insurance_claim_comments" rows="2" onkeyup="javascript:textareaAutoSize(this,1,2);" style="width:98%;"></textarea></td>
	</tr>
</table>
<br />
<br />
<?php
if(count($insurance_claim_comments))
{
?>
<table class="wide">
	<tr>
		<th>Date</th>
		<th>Staff ID</th>
		<th>Comment</th>
	</tr>
<?php
	foreach($insurance_claim_comments as $comment)
	{
?>
	<tr>
		<td style="text-align:center;"><?php echo date('d/m/Y',$comment['comment_time']) ?></td>
		<td style="text-align:center;"><?php hecho($all_staff_list[$comment['staff_id']]) ?></td>
		<td style="text-align:center;"><?php echo nl2br(hecho($comment['comment_text'], 1)) ?></td>
	</tr>
<?php
	}
?>
</table>
<br />
<br />
<?php
}
?>
</div>

<div style="background-color:white;text-align:center;padding:8px 8px 8px 8px;display:none;" id="sectionAddClaimLoss_hideNote">
	Add Claim/Loss can be viewed by Clicking "Add Claim/Loss" above<br />
</div>

<div id="invoice_summary"<?php echo ($insurance_claim['invoice_id']>0)?'':' style="display:none;"' ?>>
<?php
include 'insurance_invoice_summary.tpl.php';
?>
</div>

<input type="button" value="Submit Changes"<?php echo ($invoice_lock['staff_id']==$staff_id || $action=='add' || true)?' onclick="javascript:if(validateInsuranceClaim()) this.form.submit();"':' disabled="disabled"' ?> />
<input type="button" value="Print" onclick="javascript:window.print();" />

</form>

<!-- /insurance_addedit_claim.tpl.php -->