<html>
<body>
<?php

	global $DB, $at, $fields, $order, $by;
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
	$statement = "SELECT *, DATE_FORMAT(`date`,'%Y%m%d') AS `date` FROM `highwaygroup`.`weekly_report` WHERE `date` >= " . $nicestart . " AND `date` <= " . $niceend . " AND `from` = '" . $from . "' AND `to` = '" . $to . "'";
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
		$DB->query(sprintf(
			"TRUNCATE TABLE `highwaygroup`.`weekly_reportfilers`"
		));

		//find all users that are users of the report system
		if ($all_users = $DB->query(sprintf(
			"SELECT DISTINCT `user` FROM `highwaygroup`.`admin_users` WHERE `section_id` = %d",
			$section_id
		))) {
			while ($row = $DB->fetch_assoc($all_users))
			{
				//insert in to table all people who should file a report
				$DB->query(sprintf(
					"INSERT INTO `highwaygroup`.`weekly_reportfilers` (`user`) VALUES (%s)",
					$DB->escape($row['user'])
				));
			}
			$DB->free_result($all_users);
		}

		//return list filers for this week
		$filers_statement = "SELECT *, DATE_FORMAT(`date`, '%Y%m%d') AS `date` FROM `highwaygroup`.`weekly_report` WHERE `date` >= " . $nicestart . " AND `date` <= " . $niceend;
		$filers = $DB->query($filers_statement);
		while ($row = $DB->fetch_array($filers))
		{
			//delete form table all users who have filed a report
			$delete_statement = "DELETE FROM `highwaygroup`.`weekly_reportfilers` WHERE `user` = '".$row['user']."'";
			$DB->query($delete_statement);
			//echo $user = $row[user];
		}

		//return a list of all non filers
		$non_filers_statement = "SELECT * FROM `highwaygroup`.`weekly_reportfilers`";
		$non_filers = $DB->query($non_filers_statement);

		//test for blank
		$test = $DB->query($non_filers_statement);
		if($DB->fetch_array($test) != '')
		{
			echo "<table align='center'><tr><th style='color:#FF0033'><b>The following people have not filed a report</td></th></tr>";
			while ($row = $DB->fetch_array($non_filers))
			{
				echo "<tr><td align='center'>";

				$full_name = "SELECT `first_name`, `last_name` FROM `highwaygroup`.`users` WHERE `user` ='".$row['user']."'";
				$full_name = $DB->query_first($full_name);
				$full_name = $full_name['first_name'].' '.$full_name['last_name'];
				if($full_name != '')
				{
					$user = $full_name;
				}
				else
				{
					$user = $row[user];
				}
				echo ucwords($user);
				echo "</td></tr>";
			}
			echo "</table>";
		}



?>
</body>
<html>