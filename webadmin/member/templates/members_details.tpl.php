<!-- members_details.tpl.php -->

<?php
if ('' == $member['currency']) {
    $member['currency'] = 'USD';
}

?>

<script type="text/javascript">
//<![CDATA[
$(function() {
	$("[name='member[country]']").change(function() {
		if($(this).val()=="AU") $("[name='member[currency]']").val("AUD");
		if($(this).val()=="US") $("[name='member[currency]']").val("USD");
	});
});
//]]>
</script>

<?=formatHTMLTitle('<a href="#" onclick="toggleSection(\'sectionDetails\'); return false;">Customer Details</a>')?>

<?php
/**
 * The member data: first name, last name and email address, passed from livezilla chat
 * to add new member in database
 *
 * @author Kelvin (khtat@highwaygroup.net)
 */
if (isset($_GET['name']) === true && empty($_GET['name']) === false &&
    isset($_GET['email']) === true && empty($_GET['email']) === false) {
    $email = trim(strtolower($_GET['email']));
    $name  = explode(" ", trim(strtolower($_GET['name'])));

    $member['email_address'] = $email;
    $member['first_name']    = ucwords($name[0]);
    $member['last_name']     = (count($name) > 1) ? ucwords(end($name)) : "";
}
?>

<div id="sectionDetails"><table class="wide">
	<tr style="text-align:center;">
		<td class="left_title left_input">Account Type :</td>
		<td class="right_input"><select name="member[account_type]" required="required" class="form-control custom-select">
			<option value=""></option>
			<option value="company" <?=$member['account_type'] == 'company' ? ' selected="selected"' : ''?>>Commercial Customer</option>
			<option value="private" <?=$member['account_type'] == 'private' ? ' selected="selected"' : ''?>>Residential Customer</option>
		</select></td>
		<td class="left_title left_input">First Name :</td><td class="right_input"><input type="text" name="member[first_name]" value="<?=htmlspecialchars($member['first_name'])?>" onchange="<?=('add' == $action) ? 'updateContactName()' : ''?>" /></td>
	</tr>
	<tr style="text-align:center;">
		<td class="left_title left_input">Company Name :</td>
		<td class="right_input"><input type="text" name="member[company_name]" value="<?=htmlspecialchars($member['company_name'])?>" /></td>

		<td class="left_title left_input">Last Name :</td>
		<td class="right_input"><input onchange="<?=('add' == $action) ? 'updateContactName()' : ''?>" type="text" name="member[last_name]" value="<?=htmlspecialchars($member['last_name'])?>" /></td>
	</tr>
	<tr style="text-align:center;">
		<td class="left_title left_input" rowspan="2">Address :</td>
		<td class="right_input" rowspan="2" valign="top"><textarea name="member[address]"><?=htmlspecialchars($member['address'])?></textarea></td>

		<td class="left_title left_input">Phone :</td>
		<td class="right_input"><input type="text" name="member[phone_no]" value="<?=htmlspecialchars($member['phone_no'])?>" /></td>
	</tr>
	<tr style="text-align:center;">
		<td class="left_title left_input">Email Address :</td>
		<td class="right_input"><input type="text" name="member[email_address]" value="<?=htmlspecialchars($member['email_address'])?>" onchange="update<?=('add' == $action) ? 'Contact' : 'Login'?>Name()" /></td>
	</tr>
	<tr style="text-align:center;">
		<td class="left_title left_input">City :</td>
		<td class="right_input"><input type="text" name="member[city]" value="<?=htmlspecialchars($member['city'])?>" /></td>

		<td class="left_title left_input">2nd Contact Number :</td>
		<td class="right_input"><input type="text" name="member[phone_no2]" value="<?=htmlspecialchars($member['phone_no2'])?>" /></td>
	</tr>
	<tr style="text-align:center;">
		<td class="left_title left_input">State :</td>
		<td class="right_input"><input type="text" name="member[state]" value="<?=htmlspecialchars($member['state'])?>" /></td>

		<td class="left_title left_input">Fax :</td>
		<td class="right_input"><input type="text" name="member[fax_no]" value="<?=htmlspecialchars($member['fax_no'])?>" /></td>
	</tr>
	<tr style="text-align:center;">
		<td class="left_title left_input">Post Code :</td>
		<td class="right_input"><input type="text" name="member[post_code]" value="<?=htmlspecialchars($member['post_code'])?>" /></td>

		<td class="left_title left_input">Mobile :</td>
		<td class="right_input"><input type="text" name="member[mobile_no]" value="<?=htmlspecialchars($member['mobile_no'])?>" /></td>
	</tr>
	<tr style="text-align:center;">
		<td class="left_title left_input">Country :</td>
		<td class="right_input"><select name="member[country]"><?=formatHTMLOptions($country_list, $member['country'])?></select></td>

		<td class="left_title left_input"><?=($member['website']) ? '<a href="' . formatWebsite($member['website']) . '" target="_blank">' : ''?>Website :<?=('' != $member['website']) ? '</a>' : ''?></td>
		<td class="right_input"><input type="text" name="member[website]" value="<?=htmlspecialchars($member['website'])?>" /></td>
	</tr>
<?php
$currency_list = getCurrencyList();
?>
	<tr style="text-align:center;">
		<td class="left_title left_input" rowspan="2">Company # :</td>
		<td class="right_input" rowspan="2">
			<table class="wide" style="width:100%;">
				<tr style="text-align:center;">
					<th style="width:20%;text-align: left; background-color: #c7c7a8; color: #000; font-weight: 700;border-bottom: 1px solid grey;"> ABN </th>
					<td style="width:80%;">
						<input type="text" name="member[abn_number]" value="<?=htmlspecialchars($member['abn_number'])?>" placeholder="ABN NUMBER" />
					</td>
				</tr>
				<tr style="text-align:center;">
					<th style="width:20%;text-align: left; background-color: #c7c7a8; color: #000; font-weight: 700;border-bottom: 1px solid grey;"> Company </th>
					<td style="width:80%;">
						<input type="text" name="member[company_number]" value="<?=htmlspecialchars($member['company_number'])?>" placeholder="COMPANY NUMBER" />
					</td>
				</tr>
				<tr style="text-align:center;">
					<th style="width:20%;text-align: left; background-color: #c7c7a8; color: #000; font-weight: 700;border-bottom: 1px solid grey;"> VAT </th>
					<td style="width:80%;">
						<input type="text" name="member[vat_number]" value="<?=htmlspecialchars($member['vat_number'])?>" placeholder="VAT NUMBER" />
					</td>
				</tr>
				<tr style="text-align:center;">
					<th style="width:20%;text-align: left; background-color: #c7c7a8; color: #000; font-weight: 700;"> EORI </th>
					<td style="width:80%;">
						<input type="text" name="member[eori_number]" value="<?=htmlspecialchars($member['eori_number'])?>" placeholder="EORI NUMBER" />
					</td>
				</tr>
			</table>
		</td>
		<td class="left_title">Address Type :</td>
		<td class="right_input"><select id="b_addresstype" class="form-control" name="member[address_type]"><?=formatHTMLOptions(array(
    'commercial'  => 'Commercial Property',
    'residential' => 'Residential Property',
), $member['address_type'])?></select></td>
	</tr>
	<tr>
		<td class="left_title">Preferred Currency :</td>
		<td class="right_input"><select name="member[currency]"><option value=""></option><?=formatHTMLOptions($currency_list, $member['currency'])?></select></td>
	</tr>
</table></div><br />

<!-- /members_details.tpl.php -->