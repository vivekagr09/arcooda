<?php
global $_GLOBALS;
global $DB;

?>
<table border="0" align="center" cellpadding="4" cellspacing="0">
	<tr>
		<td nowrap="nowrap" class="bottom">&nbsp;</td>
		<td class="bottom">&nbsp;</td>
	</tr>
	<tr>
		<td nowrap="nowrap" bgcolor="#EEEEE0" class="left"><b>Username :</b></td>
		<td bgcolor="#EEEEE0" class="right"><input type="text" name="username" size="40" style="width:300px;" /></td>
	</tr>
	<tr bgcolor="#EEEEE0">
		<td nowrap="nowrap" class="left"><b>Password :</b></td>
		<td class="right"> <input type="text" name="password" size="40" style="width:300px;" />
		</td>
	</tr>
	<tr>
		<td nowrap="nowrap" bgcolor="#EEEEE0" class="left"><b>First Name :</b></td>
		<td bgcolor="#EEEEE0" class="right"><input type="text" name="first_name" size="40" style="width:300px;" /></td>
	</tr>

	<tr>
		<td nowrap="nowrap" bgcolor="#EEEEE0" class="left"><b>Last Name :</b></td>
		<td bgcolor="#EEEEE0" class="right"><input type="text" name="last_name" size="40" style="width:300px;" /></td>
	</tr>
	<tr>
		<td nowrap="nowrap" bgcolor="#EEEEE0" class="left"><b>Email :</b></td>
		<td bgcolor="#EEEEE0" class="right"><input type="text" name="email" size="40" style="width:300px;" /></td>
	</tr>
	<tr>
		<td nowrap="nowrap" bgcolor="#EEEEE0" class="left"><b>Address :</b></td>
		<td bgcolor="#EEEEE0" class="right"><input type="text" name="address" size="40" style="width:300px;" /></td>
	</tr>
	<tr>
		<td nowrap="nowrap" bgcolor="#EEEEE0" class="left"><b>Post Code :</b></td>
		<td bgcolor="#EEEEE0" class="right"><input type="text" name="post_code" size="40" style="width:300px;" /></td>
	</tr>
	<tr>
		<td nowrap="nowrap" bgcolor="#EEEEE0" class="left"><b> Home Phone :</b></td>
		<td bgcolor="#EEEEE0" class="right"><input type="text" name="home_phone" size="40" style="width:300px;" /></td>
	</tr>
	<tr>
		<td nowrap="nowrap" bgcolor="#EEEEE0" class="left"><b> Mobile :</b></td>
		<td bgcolor="#EEEEE0" class="right"><input type="text" name="mobile" size="40" style="width:300px;" /></td>
	</tr>
	<tr>
		<td nowrap="nowrap" bgcolor="#EEEEE0" class="left"><b> Home Email :</b></td>
		<td bgcolor="#EEEEE0" class="right"><input type="text" name="home_email" size="40" style="width:300px;" /></td>
	</tr>
	<tr>
		<td nowrap="nowrap" bgcolor="#EEEEE0" class="left"><b> ICQ Number :</b></td>
		<td bgcolor="#EEEEE0" class="right"><input type="text" name="icq_no" size="40" style="width:300px;" /></td>
	</tr>
	<tr>
		<td nowrap="nowrap" bgcolor="#EEEEE0" class="left"><b> Pay Rate :</b></td>
		<td bgcolor="#EEEEE0" class="right"><input type="text" name="pay_rate" size="40" style="width:300px;" /></td>
	</tr>
	<tr><td colspan="2" nowrap="nowrap" class="centre">&nbsp;</td></tr>
	<tr>
		<td nowrap="nowrap" class="left" valign="top"><b>Permissions :</b><br /><br />Sections where<br />the user can<br />view/edit/add</td>
		<td class="right"><select name="permissions[]" size="10" multiple="multiple" style="width:300px;">
			<option value="">-------- NONE --------</option>
<?php
if ($query = $DB->query(sprintf(
	"SELECT `section_id`, `section_description` FROM `highwaygroup`.`admin_sections` WHERE FIND_IN_SET('%d', `sites`) ORDER BY `section_description` ASC",
	$_GLOBALS['site_settings']['site_id']
))) {
	while ($row = $DB->fetch_assoc($query)) {
		echo "<option value=\"" . $row['section_id'] . "\">" . $row['section_description'] . "</option>\n";
	}
	$DB->free_result($query);
}
?>
<table border="0" align="center" cellpadding="4" cellspacing="0">
    <tr>
        <td nowrap="nowrap" class="bottom">&nbsp;</td>
        <td class="bottom">&nbsp;</td>
    </tr>
    <tr>
        <td nowrap="nowrap" bgcolor="#EEEEE0" class="left"><b>Username :</b></td>
        <td bgcolor="#EEEEE0" class="right"><input type="text" name="username" size="40" style="width:300px" /></td>
    </tr>
    <tr bgcolor="#EEEEE0">
        <td nowrap="nowrap" class="left"><b>Password :</b></td>
        <td class="right"><input type="text" name="password" size="40" style="width:300px" />
        </td>
    </tr>
    <tr>
        <td nowrap="nowrap" bgcolor="#EEEEE0" class="left"><b>First Name :</b></td>
        <td bgcolor="#EEEEE0" class="right"><input type="text" name="first_name" size="40" style="width:300px" /></td>
    </tr>

    <tr>
        <td nowrap="nowrap" bgcolor="#EEEEE0" class="left"><b>Last Name :</b></td>
        <td bgcolor="#EEEEE0" class="right"><input type="text" name="last_name" size="40" style="width:300px" /></td>
    </tr>
    <tr>
        <td nowrap="nowrap" bgcolor="#EEEEE0" class="left"><b>Email :</b></td>
        <td bgcolor="#EEEEE0" class="right"><input type="text" name="email" size="40" style="width:300px" /></td>
    </tr>
    <tr>
        <td nowrap="nowrap" bgcolor="#EEEEE0" class="left"><b> Home Phone :</b></td>
        <td bgcolor="#EEEEE0" class="right"><input type="text" name="home_phone" size="40" style="width:300px" /></td>
    </tr>
    <tr>
        <td nowrap="nowrap" bgcolor="#EEEEE0" class="left"><b> Mobile :</b></td>
        <td bgcolor="#EEEEE0" class="right"><input type="text" name="mobile" size="40" style="width:300px" /></td>
    </tr>
    <tr>
        <td nowrap="nowrap" bgcolor="#EEEEE0" class="left"><b> Home Email :</b></td>
        <td bgcolor="#EEEEE0" class="right"><input type="text" name="home_email" size="40" style="width:300px" /></td>
    </tr>
    <tr>
        <td nowrap="nowrap" bgcolor="#EEEEE0" class="left"><b> ICQ Number :</b></td>
        <td bgcolor="#EEEEE0" class="right"><input type="text" name="icq_no" size="40" style="width:300px" /></td>
    </tr>
    <tr><td colspan="2" nowrap="nowrap" class="centre">&nbsp;</td></tr>
    <tr>
        <td nowrap="nowrap" class="left" valign="top"><b>Permissions :</b><br /><br />Sections where<br />the user can<br />view/edit/add</td>
        <td class="right"><select name="permissions[]" size="10" multiple="multiple" style="width:300px">
            <option value="">-------- NONE --------</option>
<?php
if ($query = $DB->query(sprintf(
    "SELECT `section_id`, `section_description` FROM `highwaygroup`.`admin_sections` ORDER BY `section_description`"
))) {
    while ($row = $DB->fetch_assoc($query)) {
        echo "<option value=\"" . htmlspecialchars($row['section_id']) . "\">" . htmlspecialchars($row['section_description']) . "</option>\n";
    }
    $DB->free_result($query);
}
?>
        </select></td>
    </tr>
    <tr><td colspan="2" class="top">&nbsp;</td></tr>
    <tr>
        <td nowrap="nowrap">&nbsp;</td>
        <td><p>
            <input type="hidden" name="action" value="do_add" />
            <input type="submit" name="do_action" value="Add" />
            <input type="reset" name="clear" value="Clear" />
        </p></td>
    </tr>
</table>
