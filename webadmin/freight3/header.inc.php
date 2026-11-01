<?php
include_once __DIR__ . '/common.inc.php';

$_STACK[] = $section_handler;
include_once $_SERVER["DOCUMENT_ROOT"] . "/webadmin/header.inc.php";
$section_handler = array_pop($_STACK);

if (!$popup_window)
{
?>
<table class="wide"><tr>
	<td style="width:20%;"><b>Freight Services</b></td>
	<td style="text-align:right;width:80%">
		<a href="index.php?action=add&amp;status=sales">Add New Service</a> |
		<a href="index.php?action=list">List All Services (<?= count($section_handler->search($_GLOBALS["criteria_options"][$section_handler->section_name])) ?>)</a>
	</td>
</tr></table>
<?php
}
?>

<!-- /header.inc -->
