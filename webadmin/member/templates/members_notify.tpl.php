<!-- members_notify.tpl -->

<?php
function member_notifyparty_row($count = '[ROW_COUNT]', $member_notifyparty = array())
{
    $hidden = array(
        'notify[' . $count . '][id]' => $member_notifyparty['id'],

    );

    ob_start();
?>
<tr id="member_notifyparty_rowa[<?=$count?>]" style="text-align:center;">
	<td class="left_title" rowspan="9" style="width:5%"><input onclick="notifySelected('<?=$count?>')" type="checkbox" value="y" name="notify[<?=$count?>][default]"<?=('y' == $member_notifyparty['default']) ? ' checked="checked"' : ''?> /><br /><span id="default_str_notify[<?=$count?>]" style="font-size:10px;<?=('y' == $member_notifyparty['default']) ? '' : 'display:none'?>">Default</span></td>
	<td class="left_title left_input">Company Name :</td>
	<td class="right_input"><?=str_replace("\n", "", formatHTMLInputs($hidden))?><input type="text" name="notify[<?=$count?>][company_name]" value="<?=htmlspecialchars($member_notifyparty['company_name'])?>" /></td>
	<td class="left_title left_input">Phone :</td>
	<td class="right_input"><input type="text" name="notify[<?=$count?>][phone_no]" value="<?=$member_notifyparty['phone_no']?>" /></td>
</tr>
<tr id="member_notifyparty_rowb[<?=$count?>]" style="text-align:center;">
	<td class="left_title left_input" rowspan="2" valign="top">Address :</td>
	<td class="right_input" rowspan="2"><textarea name="notify[<?=$count?>][address]"><?=$member_notifyparty['address']?></textarea></td>
	<td class="left_title left_input">2nd Contact Number :</td>
	<td class="right_input"><input type="text" name="notify[<?=$count?>][phone_no2]" value="<?=$member_notifyparty['phone_no2']?>" /></td>
</tr>
<tr id="member_notifyparty_rowc[<?=$count?>]" style="text-align:center;">
	<td class="left_title left_input">Fax :</td>
	<td class="right_input"><input type="text" name="notify[<?=$count?>][fax_no]" value="<?=$member_notifyparty['fax_no']?>" /></td>
</tr>
<tr id="member_notifyparty_rowd[<?=$count?>]" style="text-align:center;">
	<td class="left_title left_input">City :</td>
	<td class="right_input"><input type="text" name="notify[<?=$count?>][city]" value="<?=$member_notifyparty['city']?>" /></td>
	<td class="left_title left_input">Mobile :</td>
	<td class="right_input"><input type="text" name="notify[<?=$count?>][mobile_no]" value="<?=$member_notifyparty['mobile_no']?>" /></td>
</tr>
<tr id="member_notifyparty_rowe[<?=$count?>]" style="text-align:center;">
	<td class="left_title left_input">State :</td>
	<td class="right_input"><input type="text" name="notify[<?=$count?>][state]" value="<?=$member_notifyparty['state']?>" /></td>
	<td class="left_title left_input">Post Code :</td>
	<td class="right_input"><input type="text" name="notify[<?=$count?>][post_code]" value="<?=$member_notifyparty['post_code']?>" /></td>
</tr>
<tr id="member_notifyparty_rowf[<?=$count?>]" style="text-align:center;">
	<td class="left_title left_input">Country :</td>
	<td class="right_input"><select name="notify[<?=$count?>][country]"><?=formatHTMLOptions(getCountryList(), $member_notifyparty['country'], "a")?></select></td>
	<td class="left_title left_input">Email :</td>
	<td class="right_input"><input type="text" name="notify[<?=$count?>][email]" value="<?=$member_notifyparty['email']?>" /></td>
</tr>
<tr id="member_notifyparty_rowg[<?=$count?>]" style="text-align:center;">
	<td class="left_title left_input">Attention To :</td>
	<td class="right_input"><input type="text" name="notify[<?=$count?>][contact]" value="<?=$member_notifyparty['contact']?>" /></td>
	<td class="left_title left_input">Default :</td>
	<td class="right_input"><select name="notify[<?=$count?>][default]" style="background-color:<?=("y" == $member_notifyparty["default"]) ? '#0f0' : '#f00'?>" disabled="disabled"><?=formatHTMLOptions(array('n' => 'No', 'y' => 'yes'), $member_notifyparty['default'])?></select></td>
</tr>
<tr id="member_notifyparty_rowh[<?=$count?>]" style="text-align:center;">
	<td class="left_title left_input">Company # :</td>
	<td class="right_input"> <table class="wide" id="member_notifyparty_company_table_<?=$count?>" style="width:100%;">
		<tr style="text-align:center;">
			<th style="width:20%;text-align: left; background-color: #c7c7a8; color: #000; font-weight: 700;border-bottom: 1px solid grey;"> ABN </th>
			<td style="width:80%;">
				<input type="text" name="notify[<?=$count?>][abn_number]" value="<?=$member_notifyparty['abn_number']?>" placeholder="ABN NUMBER" />
			</td>
		</tr>
		<tr style="text-align:center;">
			<th style="width:20%;text-align: left; background-color: #c7c7a8; color: #000; font-weight: 700;border-bottom: 1px solid grey;"> Company </th>
			<td style="width:80%;">
				<input type="text" name="notify[<?=$count?>][company_number]" value="<?=$member_notifyparty['company_number']?>" placeholder="COMPANY NUMBER" />
			</td>
		</tr>
		<tr style="text-align:center;">
			<th style="width:20%;text-align: left; background-color: #c7c7a8; color: #000; font-weight: 700;border-bottom: 1px solid grey;"> VAT </th>
			<td style="width:80%;">
				<input type="text" name="notify[<?=$count?>][vat_number]" value="<?=$member_notifyparty['vat_number']?>" placeholder="VAT NUMBER" />
			</td>
		</tr>
		<tr style="text-align:center;">
			<th style="width:20%;text-align: left; background-color: #c7c7a8; color: #000; font-weight: 700;"> EORI </th>
			<td style="width:80%;">
				<input type="text" name="notify[<?=$count?>][eori_number]" value="<?=$member_notifyparty['eori_number']?>" placeholder="EORI NUMBER" />
			</td>
		</tr>
	</table></td>

</tr>
<tr id="member_notifyparty_rowi[<?=$count?>]">
	<td colspan="4" class="left_title" style="text-align:center;"><input type="button" value="Delete" onclick="deleteNotifyRow('<?=$count?>');" /><div style="padding: 11px; background-color: #d773de; margin: 5px"></div></td>
</tr>
<?php
return ob_get_clean();
}
?>

<script type="text/javascript">
//<![CDATA[
function notifySelected(row_id)
{
	$(getNotifyRows()).each(function() {
		var test_id = this;
		$("[id='default_str_notify[" + test_id + "]']").toggle(test_id == row_id);
		$("[name='notify[" + test_id + "][default]']").each(function() {
			this.checked = (test_id == row_id);
		});
	});
}

var notify_count = <?=max(100, count($member_notifyparties))?>;

function addNotifyRow()
{
	$("#notify_table").append(<?=json_encode(member_notifyparty_row('[ROW_COUNT]', array()))?>.replace(/\[ROW_COUNT\]/g, String(notify_count)));
	notify_count++;
}
function deleteNotifyRow(row)
{
	if(confirm('Are you sure you want to remove this notify address??'))
	{
		var ar=Array('a','b','c','d','e','f','g','h','i');
		for(var i=0;i<ar.length;i++)
		{
			var theRow=getElement("member_notifyparty_row"+ar[i]+"["+row+"]");
			if(theRow) theRow.parentNode.deleteRow(theRow.sectionRowIndex);
		}
	}
}
function getNotifyRows()
{
	var results = [];
	$("tr[id^='member_notifyparty_rowa[']").each(function() {
		results.push(this.id.split('[')[1].split(']')[0]);
	});
	return results;
}

function notifyCopyFromAbove()
{
	var base_key = "notify[" + String(notify_count) + "]";
	addNotifyRow();
	var ar = ['company_name','address','city','state','post_code','country','phone_no', 'phone_no2', 'fax_no','mobile_no'];
	for(var i = 0;i < ar.length;i++) setValue(base_key + "[" + ar[i] + "]", getValue("member[" + ar[i] + "]"));
	setValue(base_key + "[contact]", getValue("member[first_name]") + " " + getValue("member[last_name]"));
	setValue(base_key + "[email]", getValue("member[email_address]"));
	return false;

	var ship_num=(ship_array[0]==null)?100:ship_array[0];
	if(ship_num==100 && getElement('notify[100][company_name]')==null) addNotifyRow();
	var ar=Array('company_name','address','city','state','post_code','country','phone_no', 'phone_no2', 'fax_no','mobile_no');
	for(var i=0;i<ar.length;i++)
		setValue('notify['+ship_num+']['+ar[i]+']',getValue('member['+ar[i]+']'));
	setValue('notify['+ship_num+'][contact]',getValue('member[first_name]')+' '+getValue('member[last_name]'));
	setValue('notify['+ship_num+'][email]',getValue('member[email_address]'));
}
//]]>
</script>

<?=formatHTMLTitle('<a href="#" onclick="toggleSection(\'sectionNotify\'); return false;">Notify Party</a>', '<a href="#" onclick="return notifyCopyFromAbove()">Copy From Above</a> | <a href="#" onclick="addNotifyRow(); return false;">Add New</a>')?>
<div id="sectionNotify"><table class="wide" id="notify_table">
<?php
$affected = array();
foreach ($member_notifyparties as $member_notifyparty) {
    $affected[] = $member_notifyparty['id'];
    echo member_notifyparty_row($member_notifyparty['id'], $member_notifyparty);
}
?>
	<input type="hidden" name="notify_affected" value="<?=implode('|', $affected)?>" />
</table></div><br />

<!-- /members_notify.tpl -->
