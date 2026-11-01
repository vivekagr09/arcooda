<?php
include_once __DIR__ . '/common.inc.php';

global $DB;

include '../header.inc.php';
include 'forward_menu.php';

//Checks For New Fowarder
if ("true" == $_POST["doedit"]) {

	$DB->query(sprintf(
    	"UPDATE `fowarder` SET `fow_company` = %s, `fow_contact` = %s, `fow_address1` = %s, `fow_city` = %s, `fow_country` = %s, `fow_zip` = %s, `fow_phone` = %s, `fow_mobile` = %s, `fow_website` = %s, `fow_state` = %s, `fow_fax` = %s, `fow_email` = %s WHERE `fow_id` = %d LIMIT 1",
		$DB->escape($_POST['company']),
		$DB->escape($_POST['contact']),
		$DB->escape($_POST['address1']),
		$DB->escape($_POST['city']),
		$DB->escape($_POST['country']),
		$DB->escape($_POST['zip']),
		$DB->escape($_POST['phone']),
		$DB->escape($_POST['mobile']),
		$DB->escape($_POST['site']),
		$DB->escape($_POST['state']),
		$DB->escape($_POST['fax']),
		$DB->escape($_POST['email']),
		$_POST['id']
	));

    //Display Note Saying It Was Done
    print "<font color=\"red\"><b>Updated Fowarder: {$_POST['company']}(" . $_REQUEST['id'] . ")</b></font>\n";

}

//Gets Current Data
$Data = $DB->query_first(sprintf(
	"SELECT * FROM `fowarder` WHERE `fow_id` = %d Limit 1",
	$_REQUEST['id']
));
?>
<form accept-charset="utf-8" autocomplete="off" method="post" action="edit_forward.php">
	<table align="center" cellspacing="0" cellpadding="5">
		<tr>
			<td bgcolor="#FFFFCC">Company Name</td>
			<td bgcolor="#CCCCCC"><input type="text" name="company" value="<?=$Data['fow_company']?>" /></td>
			<td bgcolor="#FFFFCC">Contact Name</td>
			<td bgcolor="#CCCCCC"><input type="text" name="contact" value="<?=$Data['fow_contact']?>" /></td>
		</tr>
		<tr>
			<td bgcolor="#FFFFCC">Address</td>
			<td bgcolor="#CCCCCC"><input type="text" name="address1" value="<?=$Data['fow_address1']?>" /></td>
			<td bgcolor="#FFFFCC">&nbsp;</td>
			<td bgcolor="#CCCCCC">&nbsp;</td>
		</tr>
		<tr>
			<td bgcolor="#FFFFCC">City</td>
			<td bgcolor="#CCCCCC"><input type="text" name="city" value="<?=$Data['fow_city']?>" /></td>
			<td bgcolor="#FFFFCC">State</td>
			<td bgcolor="#CCCCCC"><input type="text" name="state" value="<?=$Data['fow_state']?>" /></td>
		</tr>
		<tr>
			<td bgcolor="#FFFFCC">Postcode</td>
			<td bgcolor="#CCCCCC"><input type="text" name="zip" value="<?=$Data['fow_zip']?>" /></td>
			<td bgcolor="#FFFFCC">Country</td>
			<td bgcolor="#CCCCCC">
				<select name="country">
					<?=formatHTMLOptions(getCountryList(false), $Data['fow_country'])?>
				</select>
			</td>
		</tr>
		<tr>
			<td bgcolor="#FFFFCC">Phone</td>
			<td bgcolor="#CCCCCC"><input type="text" name="phone" value="<?=$Data['fow_phone']?>" /></td>
			<td bgcolor="#FFFFCC">Fax</td>
			<td bgcolor="#CCCCCC"><input type="text" name="fax" value="<?=$Data['fow_fax']?>" /></td>
		</tr>
		<tr>
			<td bgcolor="#FFFFCC">Mobile</td>
			<td bgcolor="#CCCCCC"><input type="text" name="mobile" value="<?=$Data['fow_mobile']?>" /></td>
			<td bgcolor="#FFFFCC">Email</td>
			<td bgcolor="#CCCCCC"><input type="text" name="email" value="<?=$Data['fow_email']?>" /></td>
		</tr>
		<tr>
			<td bgcolor="#FFFFCC">Website</td>
			<td bgcolor="#CCCCCC"><input type="text" name="site" value="<?=$Data['fow_website']?>" /></td>
			<td bgcolor="#FFFFCC">&nbsp;</td>
			<td bgcolor="#CCCCCC">&nbsp;</td>
		</tr>
		<tr>
		 <td colspan="4" align="center"><input type="submit" value=" Edit Fowarder " /></td>
		</tr>
	</table>
	<input type="hidden" name="id" value="<?=$Data['fow_id']?>" />
	<input type="hidden" name="doedit" value="true" />
</form>
<?php include '../footer.inc.php';?>
