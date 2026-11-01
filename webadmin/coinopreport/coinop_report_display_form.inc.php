<?php
global $_GLOBALS;
global $DB;

?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
	</head>
	<body>
<?php
	global $at, $fields, $order, $by;
	global $script, $somedate;

	$str1 = startOfWeek($somedate);
	$startyear = substr($str1,0,4);
	$startmonth = substr($str1,5,2);
	$startday = substr($str1,8,2);
	$nicestart = $startyear.$startmonth.$startday;
	$showstart = $startday."/".$startmonth."/".$startyear;

	$str2 = endOfWeek($somedate);
	$endyear = substr($str2,0,4);
	$endmonth = substr($str2,5,2);
	$endday = substr($str2,8,2);
	$niceend = $endyear.$endmonth.$endday;
	$showend = $endday."/".$endmonth."/".$endyear;

	//search statement
	$statement = "SELECT * FROM coinop_report WHERE date >= ".$nicestart." AND date <= ".$niceend;
	//if max is not yet set
	if (!isset($max))
	{
		$result = $DB->query($statement);
		$max = $DB->num_rows($result);
	}
	$inc = 25;
	$at = intval($at);

	if (!isset($order))
		$order = 'asc';

	if (!isset($by))
		$by = 'date';
	$order_by = " ORDER BY $by $order";
	$limit = " LIMIT $at, $inc ";


	//echo $statement.$extra;
	$result = $DB->query($statement.$order_by.$limit);

	//show_links2(get_links($max, "$script?$date=".urlencode($query)."&max=$max&by=$by&order=$order",25,4));
?>
		<table align='center'>
			<tr>
				<td><b>Report for the Week <?= $showstart;?> - <?= $showend;?></></td>
			</tr>
		</table>
<?php
	draw_header_row();
	$shaded = true;
	while ($row = $DB->fetch_array($result))
	{
		show_result($row, $shaded = !$shaded);
	}
	draw_header_end();
	//show more links
	//show_links2(get_links($max, "$script?query=".urlencode($query)."&max=$max&by=$by&order=$order",25,4));


	// Get a list of people that didn't file a report for this week
		//get section id from key
		$section_id = 0;
		if ($row = $DB->query_first(sprintf(
			"SELECT `section_id` FROM `highwaygroup`.`admin_sections` WHERE `sect_key` = 'REPORT' LIMIT 1"
		), MYSQLI_ASSOC)) {
			$section_id = $row['section_id'];
		}

		//clear manipulation table
		$delete_statement = "DELETE FROM `coinop_reportfilers`";
		$DB->query($delete_statement);

		//find all users that are users of the report system
		if ($all_users = $DB->query(sprintf(
			"SELECT DISTINCT `user` FROM `highwaygroup`.`admin_users` WHERE `section_id` = %d AND `site_id` = %d",
			$section_id,
			$_GLOBALS['site_settings']['site_id']
		))) {
			while ($row = $DB->fetch_array($all_users))
			{
				//insert in to table all people who should file a report
				$insert_statement = "INSERT INTO `coinop_reportfilers` (user) VALUES('".$row[user]."')";
				$DB->query($insert_statement);
				//echo $user = $row[user];
			}
			$DB->free_result($all_users);
		}

		//return list filers for this week
		$filers_statement = "SELECT * FROM coinop_report WHERE date >= ".$nicestart." AND date <= ".$niceend;
		$filers = $DB->query($filers_statement);
		while ($row = $DB->fetch_array($filers))
		{
			//delete form table all users who have filed a report
			$delete_statement = "DELETE FROM coinop_reportfilers WHERE user = '".$row[user]."'";
			$DB->query($delete_statement);
			//echo $user = $row[user];
		}

		//return a list of all non filers
		$non_filers_statement = "SELECT * FROM coinop_reportfilers";
		$non_filers = $DB->query($non_filers_statement);

		//test for blank
		$test = $DB->query($non_filers_statement);
		if($DB->fetch_array($test) != '')
		{
			echo "<table align='center'><tr><th style='color:#FF0033'><b>The following people have not filed a report</td></th></tr>";
			while ($row = $DB->fetch_array($non_filers))
			{
				echo "<tr><td align='center'>";
				$user = $row[user];
				echo ucwords($user);
				echo "</td></tr>";
			}
			echo "</table>";
		}



?>
</body>
<html>