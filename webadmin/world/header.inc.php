<?php
include_once __DIR__ . "/common.inc.php";

include_once $_SERVER["DOCUMENT_ROOT"] . "/webadmin/header.inc.php";
?>
<link rel="stylesheet" type="text/css" href="style.css" />

<div class="world_content">
	<table cellspacing="2" cellpadding="2" style="width:100%;">
		<tr>
			<td style="text-align:center;font-weight:bold;width:20%;background-color:#eee;"><?php hecho(_("World")) ?></td>
			<td style="text-align:right;width:80%;background-color:#eee;">
				<!-- <a href="index.php?action=add" target="_blank"><?php hecho(_("Add Country")) ?></a> | -->
				<a href="index.php?action=search" target="_blank"><?php hecho(_("Search Countries")) ?></a> |
				<a href="index.php?action=list"><?php hecho(_("List Countries")) ?>(<?php echo count($section_handler->search($_GLOBALS["criteria_options"][$section_name])) ?>)</a>

			</td>
		</tr>
	</table>

	<div>
		<br />
