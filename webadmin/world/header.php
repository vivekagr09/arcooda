<?php
include_once __DIR__ . "/header.inc.php";
$items = $DB->query_first(sprintf(
	"SELECT COUNT(*) AS `total` FROM `world`.`country`"
), MYSQLI_ASSOC);
?>
<br />
<br />
<table width="100%" border="0" cellspacing="1" cellpadding="5">
	<tr>
		<td bgcolor="#EEEEEE" width="20%">
			<div align="center"><b>World Summary</b></div>
		</td>
		<td bgcolor="#EEEEEE" width="60%">
			<div align="right">
				<a href="<?php echo $admin_root ?>world/index.php">List All Countries (<?php echo $items["total"] ?>)</a>
				| <a href="<?php echo $admin_root ?>world/world_search.php">Search</a>
<?php
if (!has_permission('WORLD2', $allowed_sections))
{
?>
				| <a href="<?php echo $admin_root ?>world/msg.php">Add/Edit Information</a>
<?php
}
?>
			</div>
		</td>
	</tr>
</table>
<?php
function draw_searchbox()
{
?>
<div align="right">
<form action="/webadmin/world/report.php" method="get">
	<input type="hidden" name="what" value="search" />
	Search:
	<input type="text" name="value" style="width:128px;" />
	<input type="submit" value="Go" /><br />
</form>
</div>
<?php
}
function draw_reports()
{
	global $DB;
?>
	<script language="Javascript" src="../expandToggle.js"></script>
	<a href="#" onclick="expandIt('reports'); return false"><img name="imex" src="triDown.gif" width="16" height="16" border="0" alt="expand/collapse all"><b>Reports (5)</b></a>
	<br>
	<div id="reportsChild" class="child">
	<table width="95%" border="0" cellspacing="1" cellpadding="2" align="center">
	<tr>
		<td><a href="report.php?value=population" target="_blank">Population</a></td><td><a href="report.php?value=internet_users" target="_blank">Internet Users</td><td><a href="report.php?&value=dial_code" target="_blank">Country Codes</td><td><a href="report.php?&value=internet_code" target="_blank">Internet codes</td>
	</tr>
	<tr>
		<td><a href="report.php?value=time" target="_blank">World Times</a></td>
	</tr>
	</table>
	</div>
<?php
	$row = $DB->query_first("SELECT COUNT(*) AS `total` FROM `world`.`language`", MYSQLI_ASSOC);
?>
	<a href="#" onclick="expandIt('language'); return false"><img name="imex" src="triDown.gif" width="16" height="16" border="0" alt="expand/collapse all"><b>Languages (<?php echo $row['total'] ?>)</b></a>
	<br>
	<div id="languageChild" class="child">
	<table width="95%" border="0" cellspacing="1" cellpadding="2" align="center">
<?php
	$query = $DB->query("SELECT * FROM `world`.`language` ORDER BY `name` ASC");
	$count = 0;
	while ($row = $DB->fetch_assoc($query))
	{
		if ($count % 10 == 0) echo '<tr>';
		if ($row['id'] == $l_id_selected) echo "<td bgcolor=\"#FFCC00\">";
		else echo "<td>";
		// now get all the countries that have that language.
		$count_lan = $DB->query_first(sprintf(
			"SELECT COUNT(*) AS `total` FROM `world`.`country` WHERE `languages` LIKE %s",
			$DB->escape('%' . $row['name'] . '%')
		), MYSQLI_ASSOC);
		echo '<a href="report.php?what=languages&value=' . $row['name'] . '" target="_blank">' . trim($row['name']) . ' (' . $count_lan["total"] . ')</a>';
		if (($count + 1) % 10 == 0) echo "</tr>\n";
		$count++;
	}
	$DB->free_result($query);
?>
	</table>
	</div>
<?php
	$row = $DB->query_first("SELECT COUNT(*) AS `total` FROM `world`.`video_signal`", MYSQLI_ASSOC);
?>
	<a href="#" onclick="expandIt('signal'); return false"><img name="imex" src="triDown.gif" width="16" height="16" border="0" alt="expand/collapse all"> <b>Video Signals (<?php echo $row['total'] ?>)</b></a>
	<br>
	<div id="signalChild" class="child">
	<table width="95%" border="0" cellspacing="1" cellpadding="2" align="center">
<?php
	$query = $DB->query("SELECT * FROM `world`.`video_signal` ORDER BY `value` ASC");
	$count = 0;
	while ($row = $DB->fetch_assoc($query))
	{
		if ($count % 4 == 0) echo '<tr>';
		if ($row['id'] == $v_id_selected) echo "<td bgcolor=\"#FFCC00\">";
		else echo "<td>";
		$tmp = $row['value'] . (($row['value'] == 'SECAM K') ? '' : '%');
		$count_lan = $DB->query_first(sprintf(
			"SELECT COUNT(*) AS `total` FROM `world`.`country` WHERE `video_signal` LIKE %s",
			$DB->escape('%' . $tmp . '%')
		), MYSQLI_ASSOC);
		echo '<a href="report.php?what=video_signal&value=' . $row['value'] . '" target="_blank">' . trim($row['value']) . ' (' . $count_lan["total"] . ')</a>';
		if (($count + 1) % 4 == 0) echo "</tr>\n";
		$count++;
	}
	$DB->free_result($query);
?>
	</table>
	</div>
<?php
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
}
?>