<?php
/**
 * @var Serial_Handler $section_handler
 * @var string         $section_name
 */
include_once __DIR__ . '/common.inc.php';

global $_GLOBALS;

register_shutdown_function(function ($a) {
    include_once $a;
}, __DIR__ . '/footer.inc.php');

include_once $_SERVER['DOCUMENT_ROOT'] . '/webadmin/header.inc.php';
$section_handler = new Serial_Handler();
$section_name    = $section_handler->section_name;
?>
<link rel="stylesheet" type="text/css" href="style.css" />

<table cellspacing="2" cellpadding="2" style="width:100%">
	<tr>
		<td style="text-align:center;font-weight:bold;width:20%;background-color:#eee"><?=htmlspecialchars(_('Serial Numbers'))?></td>
		<td style="text-align:right;width:80%;background-color:#eee">
			<a href=".?action=add" target="_blank"><?=htmlspecialchars(_('Add New Serial #'))?></a> |
			<a href=".?action=search" target="_blank"><?=htmlspecialchars(_('Search Serial #'))?></a> |
			<a href=".?action=list"><?=htmlspecialchars(_('List All Serial #'))?>(<?=count($section_handler->search($_GLOBALS['criteria_options'][$section_name] ?? []))?>)</a>

		</td>
	</tr>
</table>

<div class="serial_content" style="text-align:center">
	<br />
