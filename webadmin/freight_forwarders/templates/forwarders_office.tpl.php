<!-- forwarders_office.tpl.php -->
<?php echo formatHTMLTitle('<a href="javascript:toggleSection(\'sectionOfficeDetails\');">Freight Forwarder Office Details</a>') ?>

<div id="sectionOfficeDetails">

<table class="wide">
	<tr>
		<td class="left_title" style="width:25%;">Forwarder Type</td>
		<td><select name="forwarder[type]">
			<?php echo formatHTMLOptions(array('sea'=>'Sea','air'=>'Air','sea_air'=>'Air and Sea'),$forwarder['type']) ?>
		</select></td>
	</tr>
</table>

<br /><br />

<table class="wide">
	<tr>
		<td class="left_title" style="width:10%;">Company Name</td>
		<td style="width:40%;"><input name="forwarder[name]" type="text" style="width:98%;" value="<?php hecho($forwarder['name']) ?>" /></td>
		<td class="left_title" style="width:10%;">Phone</td>
		<td style="width:40%;"><input name="forwarder[phone]" type="text" style="width:98%;" value="<?php hecho($forwarder['phone']) ?>" /></td>
	</tr>
	<tr>
		<td class="left_title" rowspan="2">Address</td>
		<td rowspan="2"><textarea name="forwarder[address]" style="width:98%;height:98%;"><?php hecho($forwarder['address']) ?></textarea></td>
		<td class="left_title">Fax</td>
		<td><input name="forwarder[fax]" type="text" style="width:98%;" value="<?php hecho($forwarder['fax']) ?>" /></td>
	</tr>
	<tr>
		<td class="left_title">Mobile</td>
		<td><input name="forwarder[mobile]" type="text" style="width:98%;" value="<?php hecho($forwarder['mobile']) ?>" /></td>
	</tr>
	<tr>
		<td class="left_title">City</td>
		<td><input name="forwarder[city]" type="text" style="width:98%;" value="<?php hecho($forwarder['city']) ?>" /></td>
		<td class="left_title"></td>
		<td></td>
	</tr>
	<tr>
		<td class="left_title">State</td>
		<td><input name="forwarder[state]" type="text" style="width:98%;" value="<?php hecho($forwarder['state']) ?>" /></td>
		<td class="left_title"></td>
		<td></td>
	</tr>
	<tr>
		<td class="left_title">Post Code</td>
		<td><input name="forwarder[post_code]" type="text" style="width:98%;" value="<?php hecho($forwarder['post_code']) ?>" /></td>
		<td class="left_title">Email</td>
		<td><input name="forwarder[email]" type="text" style="width:98%;" value="<?php hecho($forwarder['email']) ?>" /></td>
	</tr>
	<tr>
		<td class="left_title">Country</td>
		<td><select name="forwarder[country]">
			<option value=""></option>
			<?php echo formatHTMLOptions(getCountryList(),$forwarder['country']) ?>
		</select></td>
		<td class="left_title">Website</td>
		<td><input name="forwarder[website]" type="text" style="width:98%;" value="<?php hecho($forwarder['website']) ?>" /></td>
	</tr>
</table>
<br /><br />
</div>
<div style="background-color:white;text-align:center;padding:8px 8px 8px 8px;display:none;" id="sectionOfficeDetails_hideNote">
Freight Forwarder Office Details can be viewed by Clicking "Freight Forwarder Office Details" above
<br />
</div>

<!-- /forwarders_office.tpl.php -->
