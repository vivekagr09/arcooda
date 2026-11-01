<?php
include_once __DIR__ . '/common.inc.php';

global $_GLOBALS;
global $DB;

include_once '../header.inc.php';
include_once "header.php";
if ("true" == $_POST['sent']) {
    $DB->query(sprintf(
        "INSERT INTO `software_updates` (`up_title`, `up_date`, `up_staff`, `up_desc`, `up_url`, `section_id`) VALUES(%s, NOW(), %s, %s, %s, %s)",
        $DB->escape($_POST['title']),
        $DB->escape($_GLOBALS['staff']['id']),
        $DB->escape($_POST['desc']),
        $DB->escape($_POST['url']),
        $DB->escape($_POST['section_id'])
    ));
    echo 'New Update created!!!<br />';
}
?>
<br />
<?=formatHTMLTitle('<a href="javascript:toggleSection(\'sectionAddUpdate\');">Add Update</a>')?>

<div id="sectionAddUpdate">
	<table class="wide">
		<form action="add.php" method="post">
		<tr>
			<td class="left_title">Title</td>
			<td><input class="intext" type="text" name="title" /></td>
		</tr>
		<tr>
			<td class="left_title">Example URL</td>
			<td><input class="intext" type="text" name="url" /></td>
		</tr>
		<tr>
			<td class="left_title">Section</td>
			<td><select name="section_id">
				<option value=""></option>
<?php
if ($data = $DB->query(sprintf(
    "SELECT `sect_key`, `section_description` FROM `highwaygroup`.`admin_sections` WHERE FIND_IN_SET('%d', `sites`) ORDER BY `section_description` ASC",
	$_GLOBALS['site_settings']['site_id']
))) {
    while ($row = $DB->fetch_assoc($data)) {
        printf(
            "<option value=\"%s\">%s</option>\n",
            htmlspecialchars($row['sect_key']),
            htmlspecialchars($row['section_description'])
        );
    }
    $DB->free_result($data);
}
?>
			</select></td>
		</tr>
		<tr>
			<td class="left_title">Description</td>
			<td><textarea name="desc" rows="10"></textarea></td>
		</tr>
		<tr class="alt">
			<td colspan="2" style="text-align:center;"><input type="submit" value="Add New Update" /></td>
		</tr>
		<input type="hidden" value="true" name="sent" />
		</form>
	</table>
</div>
<?php
include_once 'footer.php';
include_once '../footer.inc.php';
?>