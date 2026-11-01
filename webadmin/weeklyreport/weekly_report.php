<?php
include_once __DIR__ . '/common.inc.php';

//include_once '../header.inc.php';

/***********************************************************************
** Title.........:
** Version.......:
** Author........:	Conrad Neilands conradneilands@bigpond.com
** Filename......:
** Last changed..:
** Notes.........:
***********************************************************************/

$script = 'weekly_report.php';

$fields[id] = 'left';
$fields[user] = 'left';
$fields[date] = 'left';
$fields[Title] = 'left';
$fields[Description] = 'left';

if (trim($query) == '')
{
	switch ($action)
	{
		case "do_add":
			add_weekly_report();
			break;
		case "do_edit":
			edit_weekly_report();
			break;
		case "do_delete":
			delete_weekly_report();
			break;
	}
}
$user = get_user();
$user = ucwords(strtolower($user));
$items = $DB->query_first("SELECT COUNT(*) AS `total` FROM `highwaygroup`.`weekly_report` WHERE `user` = '$user' AND `from` = '" . $from . "' AND `to` = '" . $to . "'");
//$gets = get_vars();

$actions[] = '<a href="$script?action=edit&id=$id&user=$user&from=$from&to=$to" tooltip="Click here to Edit this report">Edit</a>';
$actions[] = '<a href="$script?action=delete&id=$id&user=$user&from=$from&to=$to" tooltip="Do not delete unless necessary">Delete</a>';
?>
<script src="tooltip.js" type="text/javascript"></script>
<form action="<?= $script.$gets ?>" method="post">

	<br>
      <table width="95%" border="0" cellspacing="2" cellpadding="2">
        <tr>
          <td bgcolor="#EEEEEE" width="20%">

<?php
		   $toname = $DB->query_first("SELECT * FROM `highwaygroup`.`websites` WHERE `id` = '".$to."'");
		   $fromname = $DB->query_first("SELECT * FROM `highwaygroup`.`websites` WHERE `id` = '".$from."'");
?>
            <div align="center"><b><?= $fromname['website'];?> Report for <?= $toname['website'];?></b>
			</div>
          </td>
          <td bgcolor="#EEEEEE" width="80%">
            <div align="right">
			<a href="<?= $script ?>?action=add&from=<?= $from;?>&to=<?= $to;?>">Add New Report</a> |
			<a href="<?= $script ?>?query=&from=<?= $from;?>&to=<?= $to;?>">My Reports(<?= $items[total] ?>)</a>
			</div>
          </td>
        </tr>
      </table>
<!-- search box -->
      <p align="right">
         <!--<input type="text" name="query" size="20" value="<?= htmlspecialchars(stripslashes($query)) ?>">
        <input type="submit" name="search" value="Search"> -->
      </p>
<!-- //search box -->
<?php

function search_wanted($query)
{
	global $DB, $at, $fields, $order, $by, $user;
	global $script, $from, $to;

	//keywords
	$arrKeywords = explode(" ",trim($query));

	//search query by key words
	if (!empty($query))
		$extra = " ";

	for ($index = 0;$index < count($arrKeywords); $index++)
	{
		$word = trim(strtolower($arrKeywords[$index]));

		if (!empty($word))
			$extra .= " AND ";

		if (!empty($word))
		{
			if (preg_match("/^\\d+$/",$word)) //if its a search by id
				$extra .= "(s.ss_id = '$word' OR s.id = '$word')";
			elseif (preg_match("/^[\\*\\+\\\\]/",$word))
				$extra .= "(LOWER(item) LIKE '%$word%')";
			else
				$extra .= "(LOWER(item) REGEXP \"$word\")"; //else just a normail regular exp. search
		}
	}//end search query keywords

	//search statement
	$statement = "SELECT *, DATE_FORMAT(`date`, '%Y%m%d') AS `date` FROM `highwaygroup`.`weekly_report` WHERE `user` = '" . $user . "' AND `from` = '" . $from . "' AND `to` = '" . $to . "'";

	//if max is not yet set
	if (!isset($max))
	{
		$result = $DB->query($statement.$extra);
		$max = $DB->num_rows($result);
	}


	$inc = 25;
	$at = intval($at);

	if (!isset($order))
		$order = 'desc';

	if (!isset($by))
		$by = 'date';

	$order_by = " ORDER BY $by $order";
	$limit = " LIMIT $at, $inc ";

	//echo $statement.$extra;
	$result = $DB->query($statement.$extra.$order_by.$limit);

	show_links(get_links($max, "$script?query=".urlencode($query)."&max=$max&by=$by&order=$order&user=$user",25,4));

	draw_header_row();
	$shaded = true;
	while ($row = $DB->fetch_array($result))
	{
		show_result($row, $shaded = !$shaded);
	}
	draw_header_end();
	//show more links
	show_links(get_links($max, "$script?query=".urlencode($query)."&max=$max&by=$by&order=$order&user=$user",25,4));
}



function draw_header_row()
{
	global $fields, $order, $by, $query, $max, $at, $page, $script, $from, $to, $user;

	reset($fields);
	echo "<br>";
	echo '<table align="center">';
	echo '<tr bgcolor="#336699">';
	for ($i = 0; $i < sizeof($fields); $i++)
	{
		$field = key($fields);
		if($field != 'id' && $field != 'user' && $field != 'Description')
		{
			if ($field == 'date')
			{
				echo '<td align="center" '.($by==$field?'bgcolor="#336699"':'').'><b>';
				echo "<a href=\"$script?query=".urlencode($query)."&max=$max&at=$at&page=$page&user=$user&from=$from&to=$to";
				echo "&by=$field&order=".($order=="desc"?"asc":"desc").'" class="td_options">';
				echo "Date";
				echo '</a>';
				if ($by == $field)
					echo '  <img src="/webadmin/'.$order.'.gif" width="7" height="7" border="0">  ';
				'</b></td>';
			}
			else
			{
				echo '<td align="center" '.($by==$field?'bgcolor="#336699"':'').'><b>';
				echo "<a href=\"$script?query=".urlencode($query)."&max=$max&at=$at&page=$page&user=$user&from=$from&to=$to";
				echo "&by=$field&order=".($order=="desc"?"asc":"desc").'" class="td_options">';
				echo $field;
				echo '</a>';
				if ($by == $field)
					echo '  <img src="/webadmin/'.$order.'.gif" width="7" height="7" border="0">  ';
				'</b></td>';
			}
		}
		next($fields);
	}
	echo "<td>&nbsp;</td>";
	echo '</tr>';

}

function draw_header_end()
{
	echo '</table>';
	echo "<br>";
}




function show_result($cols, $shaded)
{
	global $fields, $actions, $script , $from, $to;

	reset($fields);
	$id = $cols[key($fields)];
	echo $shaded?'<tr class="tableRow">':'<tr class="tableRowAlt">';
	for ($i = 0; $i < sizeof($fields); $i++)
	{
		$field = key($fields);
		if($field != 'id' && $field != 'user' && $field != 'Description')
		{
			if ($field == 'date')
			{
				$str1 = (stripslashes($cols[key($fields)]));
				$year = substr($str1,0,4);
				$month = substr($str1,4,2);
				$day = substr($str1,6,2);
				echo $nice = "<td valign='top'>".$day."/".$month."/".$year.'</td>';
			}
			else
			{
				if ($field == 'Description')
				{
					$output = stripslashes($cols[key($fields)]);
					$output = nl2br($output);
					echo "<td valign='top'>".$output."</td>";
				}
				else
				{
					echo "<td valign='top'>";
					echo (stripslashes($cols[key($fields)])).'</td>';
				}




				//
				//
				//
			}
		}
		next($fields);
	}
	echo "<td nowrap valign='top'>";
	for ($i = 0;$i < sizeof($actions);$i++)
	{
		if ($i != 0)
			echo " | ";
		$str = addslashes($actions[$i]);
		eval ("\$str = \"$str\";");
		echo "$str";

	}
	echo '</td>';
	echo "</tr>";
}


function add_weekly_report()
{
	global $DB, $id, $from, $to;
	$user = get_user();

	$date = ($_POST["date"]);
	$Title = ($_POST["Title"]);
	$Description = ($_POST["Description"]);

	$statement = "INSERT INTO `highwaygroup`.`weekly_report` (`user`, `Title`, `Description`, `from`, `to`)";

	$statement .= "VALUES ('".addslashes(htmlspecialchars($user))."', '".addslashes(htmlspecialchars($Title))."', '".addslashes(htmlspecialchars($Description))."', '".addslashes(htmlspecialchars($from))."', '".addslashes(htmlspecialchars($to))."')";
	//echo $statement;
	$DB->query($statement);

	echo "New Report Added To The Database <br>";
}


function edit_weekly_report()
{
	global $DB, $id, $from, $to;

	$Title = ($_POST["Title"]);
	$Description = ($_POST["Description"]);
	$statement = "UPDATE `highwaygroup`.`weekly_report` SET
					`Title` = '".addslashes(htmlspecialchars($Title))."',
					`Description` = '".addslashes(htmlspecialchars($Description))."',
					`from` = '".addslashes(htmlspecialchars($from))."',
					`to` = '".addslashes(htmlspecialchars($to))."'
				WHERE `id` = '$id'";
				//echo $statement;
	$DB->query($statement);

	echo "Report ID:$id updated.<br>";
}

function delete_weekly_report()
{
	global $id, $DB, $from, $to;

	$statement = "DELETE FROM `highwaygroup`.`weekly_report` WHERE id = '$id'";
	$DB->query($statement);
	echo "Report ID:$id Deleted.<br>";
}

if (trim($query) == '')
{

	switch ($action)
	{
		case "add":
			include 'weekly_report_add_form.inc.php';
			break;
		case "edit":
			include 'weekly_report_edit_form.inc.php';
			break;
		case "delete":
			include 'weekly_report_delete_form.inc.php';
			break;
	}
}

if(isset($query))
{
	$query = htmlspecialchars($query);
	search_wanted(addslashes($query));
}



?>
</form>

<!--//wanted admin -->
<?php
include '../footer.inc.php';
?>
