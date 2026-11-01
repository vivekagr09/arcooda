<!-- insurance_claims_list.tpl.php -->
<?php
$alt=false;
$ord_img='<img src="/webadmin/images/'.(($order=='desc')?'desc':'asc').'.png" />';

$sort_url=$get_vars;
$sort_url['action']='claims';
$sort_url['order']=($order=='desc')?'asc':'desc';
unset($sort_url['order_by']);
$sort_url='index.php?'.http_build_query($sort_url).'&order_by=';

$edit_url=$get_vars;
$edit_url['action']='edit_claim';
unset($edit_url['claim_id']);
$edit_url='index.php?'.http_build_query($edit_url).'&claim_id=';

$comments_url=$get_vars;
$comments_url['action']='claim_comments';
unset($comments_url['claim_id']);
$comments_url='index.php?'.http_build_query($comments_url).'&claim_id=';

$invoice_url='/webadmin/invoice/?action=view&invoice_id=';

$select_url=$get_vars;
$select_url['action']='select_claim';
unset($select_url['claim_id']);
$select_url='index.php?'.http_build_query($select_url).'&claim_id=';

$freight_list=getAllFreightServices();
asort($freight_list);

?>
<table class="wide spacetable" align="center">
	<tr>
		<th><a href="<?php echo $sort_url ?>claim_date">Date</a><?php echo ($order_by=='claim_date')?$ord_img:'' ?></th>
		<th><a href="<?php echo $sort_url ?>invoice_id">Invoice #</a><?php echo ($order_by=='invoice_id')?$ord_img:'' ?></th>
		<th>Customer</th>
		<th>Value of Shipment</th>
		<th><a href="<?php echo $sort_url ?>claim_amount">Value of Claim</a><?php echo ($order_by=='claim_amount')?$ord_img:'' ?></th>
		<th><a href="<?php echo $sort_url ?>courier_id">Courier</a><?php echo ($order_by=='courier_id')?$ord_img:'' ?></th>
		<th>Notes</th>
		<th>&nbsp;</th>
	</tr>

<?php
foreach($claim_list as $claim_id)
{
	$claim=insuranceClaimFromDatabase($claim_id);
	$invoice=invoiceFromDatabase($claim['invoice_id']);
	$insurance_claim_comments=insuranceClaimCommentsFromDatabase($claim_id);

	$class='';
	if($alt=!$alt)		$class=' class="alt"';
?>
	<tr<?php echo $class ?>>
		<td style="text-align:center;"><?php echo date('d/m/Y',$claim['claim_time']) ?></td>
		<td style="text-align:center;"><a href="<?php echo $invoice_url.$claim['invoice_id'] ?>" target="_blank"><?php echo invoiceFormatNumber($invoice['invoice_id'],$invoice['revision_id'],$invoice['site_id']) ?></a></td>
		<td style="text-align:center;"><?php hecho($invoice['bill_name']) ?></td>
		<td style="text-align:center;">$<?php echo convertCurrency($invoice['invoice_offerau'],'AUD') ?> AUD</td>
		<td style="text-align:center;">$<?php echo convertCurrency($claim['claim_amount'],'AUD') ?> AUD</td>
		<td style="text-align:center;"><?php hecho($freight_list[$claim['courier_id']]) ?></td>
<?php
	if(count($insurance_claim_comments))
	{
		ob_start();
		include 'templates/insurance_claims_comments.tpl.php';
		$comments_tooltip=ob_get_clean();
?>
		<td style="text-align:center;" onmouseover="<?php hecho("tt_in(" . jecho($comments_tooltip, 1) . ")") ?>" onmouseout="tt_out();"><a href="<?php echo $comments_url.$claim_id ?>" target="_blank"><img border="0" src="/webadmin/images/comment_icon.gif" /></a></td>
<?php
	}
	else
	{
?>
		<td></td>
<?php
	}
?>
		<td style="text-align:center;">
<?php
	if($popup_window)
	{
?>
			<input type="button" value=" Select " onclick="window.location='<?php echo $select_url.$claim_id ?>'" />
			<input type="button" value=" Edit " onclick="window.open('<?php echo $edit_url.$claim_id ?>');" />
<?php
	}
	else
	{
?>
			<a href="<?php echo $edit_url.$claim_id ?>" target="_blank">Edit</a>
<?php
	}
?>
		</td>
	</tr>
<?php
}
?>
</table>

<!-- /insurance_claims_list.tpl.php -->
