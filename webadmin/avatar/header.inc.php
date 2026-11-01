<?php
include_once __DIR__ . "/common.inc.php";

global $DB;

include_once $_SERVER['DOCUMENT_ROOT'] . "/webadmin/header.inc.php";

$total_avatar = $DB->query_first(sprintf(
	"SELECT COUNT(*) AS total_count FROM `highwaygroup`.`new_members` WHERE `avatar_image` IS NOT NULL"
), MYSQLI_ASSOC);
?>
<table border="0" cellpadding="2" cellspacing="2" width="100%">
	<tr>
		<td class="pageMenu" width="20%" style="text-align:center"><b>Avatars</b></td>
		<td class="pageMenu" style="text-align:right;" width="80%">
			<a href="/webadmin/avatar/for_upload/svgavatars.php">Add New Avatar</a> |
			<a href=".?action=list">List All Avatars (<?=$total_avatar['total_count']?>)</a>
		</td>
	</tr>
</table>
<div class="meta_content" style="width:100%;">
<br />