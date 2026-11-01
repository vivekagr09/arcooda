<script type="text/javascript">
//<![CDATA[
function viewMember()
{
	var name=getValue('payment_member_id');
	var url='/webadmin/member/?action=view&return_message=view&member_id='+escape(name);
	var features='scrollbars, resizable, height=400, width=600';
	window.open(url,'member_popup',features);
}

function searchMember()
{
	var name=getValue('payment_member_id');
	var url='/webadmin/member/?action=do_search&return_message=select&query='+escape(name);
	var features='scrollbars, resizable, height=400, width=600';
	window.open(url,'member_popup',features);
}

function setMemberData(data)
{
	if(data==null) return;
	if(data['member_id']!=null)
	{
		var site_list = data["site_id"].split(",");
		setValue('payment_member_id',data['member_id']);
		getElement('payment_site[car]').checked=(site_list.indexOf('16')>=0);
		getElement('payment_site[cnv]').checked=(site_list.indexOf('9')>=0);
		getElement('payment_site[he]').checked=(site_list.indexOf('2')>=0);
		getElement('payment_site[hg]').checked=(site_list.indexOf('1')>=0);
		setValue('company_name',data['company_name']);
		setValue('first_name',data['first_name']);
		setValue('address',data['address']);
		setValue('last_name',data['last_name']);
		setValue('email_address',data['email_address']);
		setValue('city',data['city']);
		setValue('phone_no',data['phone_no']);
		setValue('state',data['state']);
		setValue('fax_no',data['fax_no']);
		setValue('post_code',data['post_code']);
		setValue('mobile_no',data['mobile_no']);
		setValue('country',data['country']);
		setValue('website',data['website']);
		for(var i=0;i<data['contacts'].length;i++)
			addContact(data['contacts'][i]);
		setValue('payment_country',data['country']);
		setValue('payment_state',data['state']);
		onchangeCountry(data['country']);
	}
}
//]]>
</script>

<input type="hidden" value="<?=$payment_id?>" name="payment_id" />
<br />
<?=formatHTMLTitle('Payment Details')?>

<table>
	<tr>
		<td class="left_title" style="width:25%;">Customer Name</td>
		<td colspan="3">
			<input type="text" name="payment_member_id" value="<?=$payment['payment_member_id']?>" />
			<input type="button" onclick="searchMember()" value="Search" />
		</td>
	</tr>
	<tr>
		<td class="left_title" style="width:25%;">Websites</td>
		<td colspan="3">
<?php
$payment_site  = explode(',', $payment['payment_site']);
$website_names = array('car' => 'Car Converters', 'cnv' => 'Converters.TV', 'hg' => 'Highway Games', 'he' => 'Highway Entertainment');
foreach ($website_list as $website_id) {
    $checked = false;
    if (in_array($website_id, $payment_site)) {
        $checked = true;
    }

?>
			<input type="checkbox" name="payment_site[<?=$website_id?>]" id="payment_site[<?=$website_id?>]"<?=($checked) ? ' checked="checked"' : ''?> /><label for="payment_site[<?=$website_id?>]"><?=$website_names[$website_id]?></label><br />
<?php
}
?>
		</td>
	</tr>
</table>

<br />

<table>
	<tr>
		<td class="left_title" style="width:25%;">Company Name :</td>
		<td style="width:25%;" id="company_name"><?=htmlspecialchars($payment['member']['company_name'])?></td>
		<td class="left_title" style="width:25%;">Contact Name :</td>
		<td style="width:25%;" id="first_name"><?=htmlspecialchars($payment['member']['first_name'])?></td>
	</tr>
	<tr>
		<td class="left_title" rowspan="2">Address :</td>
		<td rowspan="2" id="address"><?=htmlspecialchars($payment['member']['address'])?></td>
		<td class="left_title">Last Name :</td>
		<td id="last_name"><?=htmlspecialchars($payment['member']['last_name'])?></td>
	</tr>
	<tr>
		<td class="left_title">Email :</td>
		<td id="email_address"><?=htmlspecialchars($payment['member']['email_address'])?></td>
	</tr>
	<tr>
		<td class="left_title">City :</td>
		<td id="city"><?=htmlspecialchars($payment['member']['city'])?></td>
		<td class="left_title">Phone :</td>
		<td id="phone_no"><?=htmlspecialchars($payment['member']['phone_no'])?></td>
	</tr>
	<tr>
		<td class="left_title">State :</td>
		<td id="state"><?=htmlspecialchars($payment['member']['state'])?></td>
		<td class="left_title">Fax :</td>
		<td id="fax_no"><?=htmlspecialchars($payment['member']['fax_no'])?></td>
	</tr>
	<tr>
		<td class="left_title">Post Code :</td>
		<td id="post_code"><?=htmlspecialchars($payment['member']['post_code'])?></td>
		<td class="left_title">Mobile :</td>
		<td id="mobile_no"><?=htmlspecialchars($payment['member']['mobile_no'])?></td>
	</tr>
	<tr>
		<td class="left_title">Country :</td>
		<td id="country"><?=htmlspecialchars($country_list[$payment['member']['country']])?></td>
		<td class="left_title">Website :</td>
		<td id="website"><?=htmlspecialchars($payment['member']['website'])?></td>
	</tr>
	<tr>
		<td class="left_title" colspan="4" style="text-align:center;"><input type="button" value="View Complete Details" onclick="viewMember()" /></td>
	</tr>
</table>

<br /><br />

<?=formatHTMLTitle('Payment Contacts')?>

<table id="contact_table">
<?php
if (is_array($payment['contacts'])) {
    foreach ($payment['contacts'] as $contact) {
?>
	<tr><td><?=contact_row($contact)?></td></tr>
<?php
}
}

?>
</table>

<br /><br />

<?=formatHTMLTitle('Worldwide Coverage')?>

<table>
	<tr>
		<td class="left_title" style="width:25%;">Country</td>
		<td><select name="payment_country" style="width:50%;" onchange="onchangeCountry(this.value)">
			<option value=""></option>
			<?=formatHTMLOptions($country_list, $payment['payment_country'])?>
		</select></td>
	</tr>
	<tr id="payment_state_row"<?=('AU' != $payment['payment_country']) ? ' style="display:none;"' : ''?>>
		<td class="left_title" style="width:25%;">State</td>
		<td><select name="payment_state" style="width:50%;">
			<option value=""></option>
			<?=formatHTMLOptions($state_list, $payment['payment_state'])?>
		</select></td>
	</tr>
	<tr>
		<td class="left_title" style="width:25%;">Continent</td>
		<td><select name="payment_continent" style="width:50%;">
			<option value=""></option>
			<?=formatHTMLOptions($continent_list, $payment['payment_continent'])?>
		</select></td>
	</tr>
	<tr>
		<td class="left_title" style="width:25%;">Type</td>
		<td>
			<input type="checkbox" name="payment_sales" id="payment_sales"<?=('y' == $payment['payment_sales']) ? ' checked="checked"' : ''?> /><label for="payment_sales">Sales</label><br />
			<input type="checkbox" name="payment_install" id="payment_install"<?=('y' == $payment['payment_install']) ? ' checked="checked"' : ''?> /><label for="payment_install">Installation</label>
		</td>
	</tr>
</table>

<br />
<br />

<?=formatHTMLTitle('Special Notes')?>

<table>
	<tr>
		<td class="left_title" style="width:25%;">Special Notes</td>
		<td><textarea name="payment_note" rows="10"></textarea></td>
	</tr>
</table>

<?php
if (count($payment_notes) > 0) {
?>
<table>
	<tr>
		<th>Staff</th>
		<th>Date</th>
		<th>Notes</th>
	</tr>
<?php
foreach ($payment_notes as $note_id) {
        $row = paymentNoteFromDatabase($note_id);
?>
	<tr>
		<td><?=htmlspecialchars($all_staff_list[$row['staff_id']])?></td>
		<td><?=date('d/m/Y', $row['note_time'])?></td>
		<td><?=nl2br(htmlspecialchars($row['note_note']))?></td>
	</tr>
<?php
}
?>
</table>
<?php
}
?>

<br />
<br />

<?=formatHTMLTitle('Company Logo')?>

<table>
<?php
if ('' != $payment['payment_logo']) {
?>
	<tr>
		<td class="left_title" style="width:25%;">Previous Logo</td>
		<td><img border="1" src="/webadmin/uploads/payment/logo/<?=$payment['payment_logo']?>" /></td>
	</tr>
<?php
}
?>
	<tr>
		<td class="left_title" style="width:25%;">Upload New Logo</td>
		<td>
			<input type="hidden" name="MAX_FILE_SIZE" value="<?=htmlspecialchars(convertSize(ini_get("upload_max_filesize")))?>" />
			<input type="file" name="payment_logo_file" />
		</td>
	</tr>
</table>

<br />
<br />

<input type="submit" value="Save Payment" /><br />
