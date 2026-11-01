<?php
/**
 * @var string $username
 */
?><?php

global $_GLOBALS;
global $DB;

$items = $DB->query_first(sprintf(
    "SELECT * FROM `highwaygroup`.`users` WHERE `user` = %s LIMIT 1",
    $DB->escape($username)
), MYSQLI_ASSOC);
?>
<table border="0" align="center" cellpadding="4" cellspacing="0">
    <tr>
        <td nowrap="nowrap" class="bottom">&nbsp;</td>
        <td class="bottom">&nbsp;</td>
    </tr>
    <tr>
        <td nowrap="nowrap" bgcolor="#EEEEE0" class="left"><b>Username :</b></td>
        <td bgcolor="#EEEEE0" class="right"><input type="text" name="username" size="40" style="width:300px" value="<?=htmlspecialchars($username)?>" disabled="disabled" /></td>
    </tr>
    <tr>
        <td nowrap="nowrap" class="left"><b>New Password :</b></td>
        <td class="right"><input type="text" name="password" size="40" style="width:300px" /></td>
    </tr>
    <tr><td colspan="2" nowrap="nowrap" class="centre">&nbsp;</td></tr>
    <tr>
        <td nowrap="nowrap" class="left" valign="top"><b>Permissions :</b><br /><br />Sections where<br />the user can<br />view/edit/add</td>
        <td class="right"><select name="permissions[]" size="10" multiple="multiple" style="width:300px">
            <option value="">-------- NONE --------</option>
<?php
$sections = array();
if ($query = $DB->query(sprintf(
    "SELECT `section_id` FROM `highwaygroup`.`admin_users` WHERE `user` = %s AND `site_id` = %d",
    $DB->escape($username),
	$_GLOBALS['site_settings']['site_id']
))) {
    while ($row = $DB->fetch_assoc($query)) {
        $sections[] = $row['section_id'];
    }
    $DB->free_result($query);
}

if ($query = $DB->query(sprintf(
    "SELECT `section_id`, `section_description` FROM `highwaygroup`.`admin_sections` WHERE FIND_IN_SET('%d', `sites`) ORDER BY `section_description` ASC",
	$_GLOBALS['site_settings']['site_id']
))) {
    while ($row = $DB->fetch_assoc($query)) {
        printf(
            '<option value="%s"%s>%s</option>' . "\n",
            htmlspecialchars($row['section_id']),
            (in_array($row['section_id'], $sections)) ? ' selected="selected"' : '',
            htmlspecialchars('section_description')
        );
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
			<input type="hidden" name="username" value="<?=htmlspecialchars($username)?>" />
			<input type="hidden" name="query" value="" />
			<input type="hidden" name="action" value="do_edit" />
			<input type="submit" name="do_action" value="Update" />
			<input type="reset" name="clear" value="Reset" />
		</p></td>
	</tr>
</table>
