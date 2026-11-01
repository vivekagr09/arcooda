<?php
include_once __DIR__ . '/common.inc.php';

global $DB;

include '../header.inc.php';
include 'forward_menu.php';

//Checks For New Fowarder
if ("true" == $_POST["doadd"]) {

    //Query For Inserting New Product
    if ($DB->query(sprintf(
		"INSERT INTO `fowarder` (`fow_company`, `fow_contact`, `fow_address1`, `fow_address2`, `fow_city`, `fow_country`, `fow_zip`, `fow_phone`, `fow_mobile`, `fow_website`, `fow_state`, `fow_fax`, `fow_email`) VALUES	('%s', '%s', '%s', '', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')",
		$DB->escape_string($_POST['company']),
		$DB->escape_string($_POST['contact']),
		$DB->escape_string($_POST['address1']),
		$DB->escape_string($_POST['city']),
		$DB->escape_string($_POST['country']),
		$DB->escape_string($_POST['zip']),
		$DB->escape_string($_POST['phone']),
		$DB->escape_string($_POST['mobile']),
		$DB->escape_string($_POST['site']),
		$DB->escape_string($_POST['state']),
		$DB->escape_string($_POST['fax']),
		$DB->escape_string($_POST['email'])
	))) {

		//Display Note Saying It Was Done
		print "<font color=\"red\"><b>Added New Fowarder: {$_POST['company']}(" . $DB->insert_id() . ")</b></font>\n";

	}
}
?>
<form accept-charset="utf-8" autocomplete="off" method="post" action="add_forward.php">
	<table align="center" cellspacing="0" cellpadding="5">
		<tr>
			<td bgcolor="#FFFFCC">Company Name</td>
			<td bgcolor="#CCCCCC"><input type="text" name="company" /></td>
			<td bgcolor="#FFFFCC">Contact Name</td>
			<td bgcolor="#CCCCCC"><input type="text" name="contact" /></td>
		</tr>
		<tr>
			<td bgcolor="#FFFFCC">Address</td>
			<td bgcolor="#CCCCCC"><input type="text" name="address1" /></td>
			<td bgcolor="#FFFFCC">&nbsp;</td>
			<td bgcolor="#CCCCCC">&nbsp;</td>
		</tr>
		<tr>
			<td bgcolor="#FFFFCC">City</td>
			<td bgcolor="#CCCCCC"><input type="text" name="city" /></td>
			<td bgcolor="#FFFFCC">State</td>
			<td bgcolor="#CCCCCC"><input type="text" name="state" /></td>
		</tr>
		<tr>
			<td bgcolor="#FFFFCC">Postcode</td>
			<td bgcolor="#CCCCCC"><input type="text" name="zip" /></td>
			<td bgcolor="#FFFFCC">Country</td>
			<td bgcolor="#CCCCCC">
				<select name="country">
					<?=formatHTMLOptions(getCountryList(false))?>
				</select>
			</td>
		</tr>
		<tr>
			<td bgcolor="#FFFFCC">Phone</td>
			<td bgcolor="#CCCCCC"><input type="text" name="phone" /></td>
			<td bgcolor="#FFFFCC">Fax</td>
			<td bgcolor="#CCCCCC"><input type="text" name="fax" /></td>
		</tr>
		<tr>
			<td bgcolor="#FFFFCC">Mobile</td>
			<td bgcolor="#CCCCCC"><input type="text" name="mobile" /></td>
			<td bgcolor="#FFFFCC">Email</td>
			<td bgcolor="#CCCCCC"><input type="text" name="email" /></td>
		</tr>
		<tr>
			<td bgcolor="#FFFFCC">Website</td>
			<td bgcolor="#CCCCCC"><input type="text" name="site" /></td>
			<td bgcolor="#FFFFCC">&nbsp;</td>
			<td bgcolor="#CCCCCC">&nbsp;</td>
		</tr>
		<tr>
		 <td colspan="4" align="center"><input type="submit" value=" Add Fowarder " /></td>
		</tr>
	</table>
	<input type="hidden" name="doadd" value="true" />
</form>
<?php include '../footer.inc.php';?>
