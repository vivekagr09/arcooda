<?php
include_once __DIR__ . '/common.inc.php';

global $_GLOBALS;
global $DB;

$UpID     = intval($_GET['id']);
$SecCheck = $DB->query_first(sprintf(
	"SELECT `up_staff` FROM `software_updates` WHERE `up_id` = %d LIMIT 1",
	$UpID
));
if ($_GLOBALS['staff']['id'] != $SecCheck['up_staff']) {
    header("location: index.php");
}

if ($_POST['edited']) {
    $DB->query(sprintf(
		"UPDATE `software_updates` SET `up_title` = %s, `up_url` = %s, `up_desc` = %s, `section_id` = %s WHERE `up_id` = %d LIMIT 1",
		$DB->escape($_POST['up_title']),
		$DB->escape($_POST['up_url']),
		$DB->escape($_POST['up_desc']),
		$DB->escape($_POST['section_id']),
		$UpID
	));
}
include_once '../header.inc.php';
include_once 'header.php';

$UpID      = intval($_GET['id']);
$UpDetails = $DB->query_first(sprintf(
	"SELECT * FROM `software_updates` WHERE `up_id` = %d",
	$UpID
), MYSQLI_ASSOC);
?>
<br />
<?=formatHTMLTitle('<a href="javascript:toggleSection(\'sectionEditDetails\');">Edit Details</a>')?>

<div id="sectionEditDetails">
	<table class="wide">
		<form action="edit.php?id=<?=$UpID?>" method="post">
		<tr>
			<td class="left_title">Title</td>
			<td><input class="intext" type="text" name="up_title" value="<?=$UpDetails['up_title']?>" /></td>
		</tr>
		<tr>
			<td class="left_title">Dated</td>
			<td><?=date("d-m-Y", strtotime($UpDetails['up_date']))?></td>
		</tr>
		<tr>
			<td class="left_title">Example URL</td>
			<td><input class="intext" type="text" name="up_url" value="<?=$UpDetails['up_url']?>" /></td>
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
			"<option value=\"%s\"%s>%s</option>\n",
			htmlspecialchars($row['sect_key']),
			($UpDetails['section_id'] == $row['sect_key']) ? ' selected="selected"' : '',
			htmlspecialchars($row['section_description'])
		);
	}
	$DB->free_result($data);
}
?>
			</select></td>
		</tr>
		<tr>
			<td class="left_title">Updated</td>
			<td><textarea rows="10" name="up_desc"><?=nl2br($UpDetails['up_desc'])?></textarea></td>
		</tr>
		<tr class="alt">
			<td colspan="2" style="text-align:center;"><input type="submit" name="edited" value="Update Details" /></td>
		</tr>
		</form>
	</table>
</div>
<?php
include_once 'footer.php';
include_once '../footer.inc.php';
?>