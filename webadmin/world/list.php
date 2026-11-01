<?php
include_once __DIR__ . "/common.inc.php";

include_once __DIR__ . "/header.inc.php";

$order = "asc";
$page_id = 0;
$order_by = "name";

if (array_key_exists("order", $_REQUEST)) $order = $_REQUEST["order"];
if ($order != "asc") $order = "desc";
if (array_key_exists("page_id", $_REQUEST)) $page_id = intval($_REQUEST["page_id"]);
if (array_key_exists("order_by", $_REQUEST)) $order_by = strtolower($_REQUEST["order_by"]);

//Build a search criteria array
$criteria = $_GLOBALS["criteria_options"][$section_name];
foreach($section_handler->getOptionList("all_fields") as $k => $v) if (array_key_exists($k, $_REQUEST))
{
	if (is_array($_REQUEST[$k])) $criteria[$k] = $_REQUEST[$k];
	elseif ($_REQUEST[$k] !== "") $criteria[$k] = preg_split("/[^0-9a-z]+/i", $_REQUEST[$k]);
	foreach($criteria[$k] as $k2 => $v2) if (empty($v2)) unset($criteria[$k][$k2]);
}

unset($criteria["id"]);
if (array_key_exists("search_for", $_REQUEST) && $_REQUEST["search_for"] != "") $criteria["any_text"] = explode("|", $_REQUEST["search_for"]);
if (array_key_exists("subset_list", $_REQUEST) && $_REQUEST["subset_list"] != "") $criteria["id"] = explode("|", $_REQUEST["subset_list"]);

$sub_criteria = $criteria;
unset($sub_criteria['report_type']);
$item_list = $section_handler->search($sub_criteria);
if (count($item_list) > 0)
{
	$list_by1 = 'report_type';
	$list_by2 = '';
?>
<div style="text-align:left;">
	<a href="javascript:void(toggleSection('sectionViewByReportType'))"><img name="imEx" src="/webadmin/images/triDown.gif" alt="Expand/Collapse All" border="0" height="16" width="16" /> <b><?php hecho(_("Reports")) ?></b></a>
	<br /><br />
	<div id="sectionViewByReportType" class="child" style="display:none;">
<?php
	include __DIR__ . '/navigate.tpl.php';
?>
	</div>
</div>
<?php
}

$sub_criteria = $criteria;
unset($sub_criteria['languages']);
$item_list = $section_handler->search($sub_criteria);
if (count($item_list) > 0)
{
	$list_by1 = 'languages';
	$list_by2 = '';
?>
<div style="text-align:left;">
	<a href="javascript:void(toggleSection('sectionViewByLanguages'))"><img name="imEx" src="/webadmin/images/triDown.gif" alt="Expand/Collapse All" border="0" height="16" width="16" /> <b><?php hecho(_("Languages")) ?></b></a>
	<br /><br />
	<div id="sectionViewByLanguages" class="child" style="display:none;">
<?php
	include __DIR__ . '/navigate.tpl.php';
?>
	</div>
</div>
<?php
}

$sub_criteria = $criteria;
unset($sub_criteria['video_signal']);
$item_list = $section_handler->search($sub_criteria);
if (count($item_list) > 0)
{
	$list_by1 = 'video_signal';
	$list_by2 = '';
?>
<div style="text-align:left;">
	<a href="javascript:void(toggleSection('sectionViewByVideoSignals'))"><img name="imEx" src="/webadmin/images/triDown.gif" alt="Expand/Collapse All" border="0" height="16" width="16" /> <b><?php hecho(_("Video Signals")) ?></b></a>
	<br /><br />
	<div id="sectionViewByVideoSignals" class="child" style="display:none;">
<?php
	include __DIR__ . '/navigate.tpl.php';
?>
	</div>
</div>
<?php
}

$sub_criteria = $criteria;
unset($sub_criteria['voltage']);
$item_list = $section_handler->search($sub_criteria);
if (count($item_list) > 0)
{
	$list_by1 = 'voltage';
	$list_by2 = '';
?>
<div style="text-align:left;">
	<a href="javascript:void(toggleSection('sectionViewByVoltage'))"><img name="imEx" src="/webadmin/images/triDown.gif" alt="Expand/Collapse All" border="0" height="16" width="16" /> <b><?php hecho(_("Voltages")) ?></b></a>
	<br /><br />
	<div id="sectionViewByVoltage" class="child" style="display:none;">
<?php
	include __DIR__ . '/navigate.tpl.php';
?>
	</div>
</div>
<?php
}

	$row = $DB->query_first("SELECT COUNT(*) AS `total` FROM `world`.`voltage`", MYSQLI_ASSOC);
?>
	<a href="#" onclick="expandIt('voltage'); return false"><img name="imex" src="triDown.gif" width="16" height="16" border="0" alt="expand/collapse all"> <b>Voltages (<?php echo $row['total'] ?>)</b></a>
	<br>
	<div id="voltageChild" class="child">
	<table width="95%" border="0" cellspacing="1" cellpadding="2" align="center">
<?php
	$query = $DB->query("SELECT * FROM `world`.`voltage` ORDER BY `value` ASC");
	$count = 0;
	while ($row = $DB->fetch_assoc($query))
	{
		if ($count % 4 == 0) echo '<tr>';
		if ($row['id'] == $vo_id_selected) echo "<td bgcolor=\"#FFCC00\">";
		else echo "<td>";
		$count_lan = $DB->query_first(sprintf(
			"SELECT COUNT(*) AS `total` FROM `world`.`country` WHERE `voltage` LIKE %s",
			$DB->escape('%' . $row['value'] . '%')
		), MYSQLI_ASSOC);
		echo '<a href="report.php?what=voltage&value=' . $row['value'] . '" target="_blank">' . trim($row['value']) . ' (' . $count_lan["total"] . ')</a>';
		if (($count + 1) % 4 == 0) echo "</tr>\n";
		$count++;
	}
	$DB->free_result($query);
?>
	</table>
	</div>
<?php
	$row = $DB->query_first("SELECT COUNT(*) AS `total` FROM `world`.`plug_types`", MYSQLI_ASSOC);
?>
	<a href="#" onclick="expandIt('plugs'); return false"><img name="imex" src="triDown.gif" width="16" height="16" border="0" alt="expand/collapse all"> <b>Plug Types (<?php echo $row['total'] ?>)</b></a>
	<br>
	<div id="plugsChild" class="child">
	<table width="95%" border="0" cellspacing="1" cellpadding="2" align="center">
<?php
	$query = $DB->query("SELECT * FROM `world`.`plug_types` ORDER BY `value` ASC");
	$count = 0;
	while ($row = $DB->fetch_assoc($query))
	{
		if ($count % 4 == 0) echo '<tr>';
		if ($row['id'] == $vo_id_selected) echo "<td bgcolor=\"#FFCC00\">";
		else echo "<td>";
		$count_lan = $DB->query_first(sprintf(
			"SELECT COUNT(*) AS `total` FROM `world`.`country` WHERE `plugs` LIKE %s",
			$DB->escape('%' . $row['value'] . '%')
		), MYSQLI_ASSOC);
		echo '<a href="report.php?what=plugs&value=' . $row['value'] . '" target="_blank">' . trim($row['value']) . ' (' . $count_lan["total"] . ')</a>';
		if (($count + 1) % 4 == 0) echo "</tr>\n";
		$count++;
	}
	$DB->free_result($query);
?>
	</table>
	</div>
<?php

include_once __DIR__ . "/footer.inc.php";
