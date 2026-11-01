<!-- members_shipping.tpl -->

<?php
function member_shippingaddress_row($count = '[ROW_COUNT]', $member_shippingaddress = array())
{
    $hidden = array(
        'shipping[' . $count . '][id]'           => $member_shippingaddress['id'],
        'shipping[' . $count . '][instructions]' => $member_shippingaddress['instructions'],
    );

    ob_start();
?>
<tr id="member_shippingaddress_rowa[<?=$count?>]" style="text-align:center;">
	<td class="left_title" rowspan="9" style="width:5%"><input onclick="shippingSelected('<?=$count?>')" type="checkbox" value="y" name="shipping[<?=$count?>][default]"<?=('y' == $member_shippingaddress['default']) ? ' checked="checked"' : ''?>/><br /><span id="default_str_shipping[<?=$count?>]" style="font-size:10px;<?=('y' == $member_shippingaddress['default']) ? '' : 'display:none'?>">Default</span></td>

	<td class="left_title left_input">Company Name :</td>
	<td class="right_input"><?=str_replace("\n", "", formatHTMLInputs($hidden))?><input type="text" name="shipping[<?=$count?>][company_name]" value="<?=htmlspecialchars($member_shippingaddress['company_name'])?>" /></td>

	<td class="left_title left_input">First Name :</td>
	<td class="right_input"><input type="text" name="shipping[<?=$count?>][contact]" value="<?=$member_shippingaddress['contact']?>" /></td>
</tr>
<tr id="member_shippingaddress_rowb[<?=$count?>]" style="text-align:center;">
	<td class="left_title left_input">Address Type :</td>
	<td class="right_input"><select name="shipping[<?=$count?>][address_type]"><?=formatHTMLOptions(array(
        'commercial'  => 'Commercial Property',
        'residential' => 'Residential Property',
    ), $member_shippingaddress['address_type'])?></select></td>

	<td class="left_title left_input">Last Name :</td>
	<td class="right_input"><input type="text" name="shipping[<?=$count?>][contact_last]" value="<?=$member_shippingaddress['contact_last']?>" /></td>
</tr>
<tr id="member_shippingaddress_rowc[<?=$count?>]" style="text-align:center;">
	<td class="left_title left_input">Address :</td>
	<td class="right_input"><textarea name="shipping[<?=$count?>][address]"><?=$member_shippingaddress['address']?></textarea></td>

	<td class="left_title left_input">Email :</td>
	<td class="right_input"><input type="text" name="shipping[<?=$count?>][email]" value="<?=$member_shippingaddress['email']?>" /></td>
</tr>
<tr id="member_shippingaddress_rowd[<?=$count?>]" style="text-align:center;">
	<td class="left_title left_input">City :</td>
	<td class="right_input"><input type="text" name="shipping[<?=$count?>][city]" value="<?=$member_shippingaddress['city']?>" /></td>

	<td class="left_title left_input">Phone :</td>
	<td class="right_input"><input type="text" name="shipping[<?=$count?>][phone_no]" value="<?=$member_shippingaddress['phone_no']?>" /></td>
</tr>
<tr id="member_shippingaddress_rowe[<?=$count?>]" style="text-align:center;">
	<td class="left_title left_input">State :</td>
	<td class="right_input"><input type="text" name="shipping[<?=$count?>][state]" value="<?=$member_shippingaddress['state']?>" /></td>

	<td class="left_title left_input">2nd Contact Number :</td>
	<td class="right_input"><input type="text" name="shipping[<?=$count?>][phone_no2]" value="<?=$member_shippingaddress['phone_no2']?>" /></td>
</tr>
<tr id="member_shippingaddress_rowf[<?=$count?>]" style="text-align:center;">
	<td class="left_title left_input">Post Code :</td>
	<td class="right_input"><input type="text" name="shipping[<?=$count?>][post_code]" value="<?=$member_shippingaddress['post_code']?>" /></td>

	<td class="left_title left_input">Fax :</td>
	<td class="right_input"><input type="text" name="shipping[<?=$count?>][fax_no]" value="<?=$member_shippingaddress['fax_no']?>" /></td>
</tr>
<tr id="member_shippingaddress_rowg[<?=$count?>]" style="text-align:center;">
	<td class="left_title left_input">Country :</td>
	<td class="right_input"><select name="shipping[<?=$count?>][country]"><?=formatHTMLOptions(getCountryList(), $member_shippingaddress['country'], "a")?></select></td>

	<td class="left_title left_input">Mobile :</td>
	<td class="right_input"><input type="text" name="shipping[<?=$count?>][mobile_no]" value="<?=$member_shippingaddress['mobile_no']?>" /></td>
</tr>
<tr id="member_shippingaddress_rowh[<?=$count?>]" style="text-align:center;">
	<td class="left_title left_input">Company # :</td>
	<td class="right_input"> <table class="wide" id="member_shippingaddress_company_table_<?=$count?>" style="width:100%;">
		<tr style="text-align:center;">
			<th style="width:20%;text-align: left; background-color: #c7c7a8; color: #000; font-weight: 700;border-bottom: 1px solid grey;"> ABN </th>
			<td style="width:80%;">
				<input  type="text" name="shipping[<?=$count?>][abn_number]" value="<?=$member_shippingaddress['abn_number']?>" placeholder="ABN NUMBER" />
			</td>
		</tr>
		<tr style="text-align:center;">
			<th style="width:20%;text-align: left; background-color: #c7c7a8; color: #000; font-weight: 700;border-bottom: 1px solid grey;"> Company </th>
			<td style="width:80%;">
				<input  type="text" name="shipping[<?=$count?>][company_number]" value="<?=$member_shippingaddress['company_number']?>" placeholder="COMPANY NUMBER" />
			</td>
		</tr>
		<tr style="text-align:center;">
			<th style="width:20%;text-align: left; background-color: #c7c7a8; color: #000; font-weight: 700;border-bottom: 1px solid grey;"> VAT </th>
			<td style="width:80%;">
				<input  type="text" name="shipping[<?=$count?>][vat_number]" value="<?=$member_shippingaddress['vat_number']?>" placeholder="VAT NUMBER" />
			</td>
		</tr>
		<tr style="text-align:center;">
			<th style="width:20%;text-align: left; background-color: #c7c7a8; color: #000; font-weight: 700;"> EORI </th>
			<td style="width:80%;">
				<input  type="text" name="shipping[<?=$count?>][eori_number]" value="<?=$member_shippingaddress['eori_number']?>" placeholder="EORI NUMBER" />
			</td>
		</tr>
	</table></td>
	<td class="left_title left_input">Default :</td>
	<td class="right_input"><select name="shipping[<?=$count?>][default]" style="background-color:<?=("y" == $member_shippingaddress["default"]) ? '#0f0' : '#f00'?>" disabled><?=formatHTMLOptions(array('n' => 'No', 'y' => 'yes'), $member_shippingaddress['default'])?></select></td>
</tr>
<tr id="member_shippingaddress_rowi[<?=$count?>]">
	<td colspan="4" class="left_title" style="text-align:center;"><input type="button" value="Delete" onclick="deleteShippingRow('<?=$count?>');" /> <div style="padding: 11px; background-color: #d773de; margin: 5px"></div></td>
</tr>
<?php
return ob_get_clean();
}
?>

<script type="text/javascript">
//<![CDATA[
function shippingSelected(row_id)
{
	$(getShippingRows()).each(function() {
		var test_id = this;
		$("[id='default_str_shipping[" + test_id + "]']").toggle(test_id == row_id);
		$("[name='shipping[" + test_id + "][default]']").each(function() {
			this.checked = (test_id == row_id);
		});
	});
}
var shipping_count = <?=max(100, count($member_shippingaddresses))?>;
var ship_array = [];
<?php
if (is_array($member_shippingaddresses) && count($member_shippingaddresses)) {
    foreach ($member_shippingaddresses as $member_shippingaddress) {
        echo "ship_array[ship_array.length] = " . $member_shippingaddress['id'] . "\n";
    }
}

?>
function addShippingRow()
{
	$("#shipping_table").append(<?=json_encode(member_shippingaddress_row('[ROW_COUNT]', array()))?>.replace(/\[ROW_COUNT\]/g, shipping_count));

	shipping_count++;
}
function deleteShippingRow(row)
{
	if(confirm('Are you sure you want to remove this shipping address??'))
	{
		var ar=Array('a','b','c','d','e','f','g','h','i');
		for(var i=0;i<ar.length;i++)
		{
			var theRow=getElement("member_shippingaddress_row"+ar[i]+"["+row+"]");
			if(theRow) theRow.parentNode.deleteRow(theRow.sectionRowIndex);
		}
	}
}
function getShippingRows()
{
	var results = [];
	$("tr[id^='member_shippingaddress_rowa[']").each(function() {
		results.push(this.id.split("[")[1].split("]")[0]);
	});
	return results;
}
function copyFromAbove()
{
	var ship_num=(ship_array[0]==null)?100:ship_array[0];
	if(ship_num==100 && getElement('shipping[100][company_name]')==null) addShippingRow();
	var ar=Array('company_name','address','city','state','post_code','country','phone_no', 'phone_no2', 'fax_no','mobile_no');
	for(var i=0;i<ar.length;i++)
		setValue('shipping['+ship_num+']['+ar[i]+']',getValue('member['+ar[i]+']'));
	setValue('shipping['+ship_num+'][contact]',getValue('member[first_name]'));
	setValue('shipping['+ship_num+'][contact_last]',getValue('member[last_name]'));
	setValue('shipping['+ship_num+'][email]',getValue('member[email_address]'));
}
//]]>
</script>

<?=formatHTMLTitle('<a href="#" onclick="toggleSection(\'sectionShipping\'); return false;">Shipping Addresses</a>', '<a href="#" onclick="copyFromAbove();return false;">Copy From Above</a> | <a href="#" onclick="addShippingRow(); return false;">Add New</a>')?>
<div id="sectionShipping"><table class="wide" id="shipping_table">
<?php
$affected = array();
foreach ($member_shippingaddresses as $member_shippingaddress) {
    $affected[] = $member_shippingaddress['id'];
    echo member_shippingaddress_row($member_shippingaddress['id'], $member_shippingaddress);
}
?>
	<input type="hidden" name="shipping_affected" value="<?=implode('|', $affected)?>" />
</table></div><br />

<!-- /members_shipping.tpl -->
