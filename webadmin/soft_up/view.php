<?php
include_once __DIR__ . '/common.inc.php';

global $_GLOBALS;
global $DB;

$UpID = intval($_GET['id']);
if ($_POST['comment']) {
    if ($_POST['email']) {
        mail($_POST['email_address'], $_POST['comment'], "From: Comments<no-reply@highwaygroup.net>");
        print "Mail Sent";
    }
    $_POST['comment'] = addslashes($_POST['comment']);
    $_POST['comment'] = str_replace("\n", "\\n", $_POST['comment']);
    $DB->query(sprintf(
		"INSERT INTO `software_comments` (`up_id`, `up_com_msg`, `up_com_staff`, `up_come_date`) VALUES (%d, %s, %d, NOW())",
		$UpID,
		$DB->escape($_POST['comment']),
		$_GLOBALS['staff']['id']
	));
    //header("Location: view.php?id={$_GET['id']}");
}
include '../header.inc.php';
include "header.php";

$UpDetails = $DB->query_first(sprintf(
	"SELECT * FROM `software_updates` WHERE `up_id` = %d LIMIT 1",
	$UpID
), MYSQLI_ASSOC);
//$UpDetails;
?>
<br />
<?=formatHTMLTitle('<a href="javascript:toggleSection(\'sectionUpdateDetails\');">Update Details</a>')?>

<div id="sectionUpdateDetails">
	<table class="wide">
		<tr>
			<td class="left_title">Title</td>
			<td><?=$UpDetails['up_title']?></td>
		</tr>
		<tr><td class="left_title">&nbsp;</td><td>&nbsp;</td>
		<tr>
			<td class="left_title">Dated</td>
			<td><?=date("d/m/Y", strtotime($UpDetails['up_date']))?></td>
		</tr>
		<tr><td class="left_title">&nbsp;</td><td>&nbsp;</td>
		<tr>
			<td class="left_title">Example URL</td>
			<td><a href='<?=$UpDetails['up_url']?>' target="_BLANK"><?=$UpDetails['up_url']?></a></td>
		</tr>
		<tr><td class="left_title">&nbsp;</td><td>&nbsp;</td>
		<tr>
			<td class="left_title" valign="top">Updated</td>
			<td><?=nl2br(htmlspecialchars($UpDetails['up_desc']))?></td>
		</tr>
	</table>
</div>

<br />
<?=formatHTMLTitle('<a href="javascript:toggleSection(\'sectionAddComment\');">Add Comment</a>')?>

<div id="sectionAddComment">
	<table class="wide">
		<form action="view.php?id=<?=$UpID?>" method="post">
		<tr>
			<td align="center">
				<b>Add Comment<b><br />
				<textarea name="comment" style="height:500px;"></textarea><br />
				<input type="checkbox" name="email" value="true" /> Send Email Of Comment To
				<select name="email_address" style="width:auto;">
<?php
if ($data = $DB->query(sprintf(
	"SELECT `first_name`, `last_name`, `email` FROM `highwaygroup`.`users` ORDER BY `first_name` ASC"
))) {
	while ($row = $DB->fetch_assoc($data)) {
		printf(
			"<option value=\"%s\">%s</option>\n",
			htmlspecialchars($row['email']),
			htmlspecialchars(trim($row['first_name'] . ' ' . $row['last_name']))
		);
	}
	$DB->free_result($data);
}
?>
				</select><br />
				<input type="submit" name="submit" value="Add Comment" />
			</td>
		</tr>
		</form>
	</table>
</div>

<br />
<?=formatHTMLTitle('<a href="javascript:toggleSection(\'sectionComments\');">Comments</a>')?>

<div id="sectionComments">
	<table class="wide spacetable">
		<tr>
			<th>Date Added</th>
			<th>User</th>
			<th>Comment</th>
		</tr>
<?php
if ($UpComs = $DB->query(sprintf(
	"SELECT * FROM `software_comments` WHERE `up_id` = %d ORDER BY `up_com_id` DESC",
	$UpID
))) {
	$alt = false;
	while ($row = $DB->fetch_assoc($UpComs)) {
?>
		<tr<?=($alt = !$alt) ? ' ' : ' class="alt"'?>>
			<td style="white-space:nowrap;"><?=date("d/m/Y", strtotime($row['up_come_date']))?></td>
			<td style="white-space:nowrap;"><?=$row['up_com_staff']?></td>
			<td style="width:100%;"><?=nl2br($row['up_com_msg'])?></td>
		</tr>
<?php
	}
	$DB->free_result($UpComs);
}
?>
	</table><br />
</div>
<?php
include "footer.php";
include '../footer.inc.php';
