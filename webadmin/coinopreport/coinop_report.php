<?php
/***********************************************************************
** Title.........:
** Version.......:
** Author........:	Conrad Neilands conradneilands@bigpond.com
** Filename......:
** Last changed..:
** Notes.........:
***********************************************************************/

include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/header.inc.php';

$script = 'coinop_report.php';

$fields[id] = 'left';
$fields[user] = 'left';
$fields[date] = 'left';
$fields[Title] = 'left';
$fields[Description] = 'left';

$user = ucwords(strtolower($user));

if (trim($query) == '')
{

	switch ($action)
	{
		case "do_add":
			add_coinop_report();
			break;
		case "do_edit":
			edit_coinop_report();
			break;
		case "do_delete":
			delete_coinop_report();
			break;
	}
}
$items = $DB->query_first("SELECT COUNT(*) as total FROM coinop_report WHERE user = '$user'");
$gets = get_vars();

$actions[] = '<a href="$script?action=edit&id=$id&user=$user" tooltip="Click here to Edit this report">Edit</a>';
$actions[] = '<a href="$script?action=delete&id=$id&user=$user" tooltip="Do not delete unless necessary">Delete</a>';
?>
<script src="tooltip.js" type="text/javascript"></script>
<form accept-charset="utf-8" autocomplete="off" method="post" action="<?= $script.$gets ?>">

	<br>
      <table width="95%" border="0" cellspacing="2" cellpadding="2">
        <tr>
          <td bgcolor="#EEEEEE" width="20%">
            <div align="center"><b>Report on work for Highway Games</b>
			</div>
          </td>
          <td bgcolor="#EEEEEE" width="80%">
            <div align="right">
			<a href="<?= $script ?>?action=add" tooltip="Click to add a new report">Add New Report</a> |
			<a href="<?= $script ?>?query=" tooltip="Click to list all your reports">My Reports(<?= $items[total] ?>)</a>
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
	global $script;

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
	$statement = "SELECT * FROM coinop_report WHERE user = '$user'";

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
	global $fields, $order, $by, $query, $max, $at, $page, $script;

	reset($fields);
	echo "<br>";
	echo '<table align="center">';
	echo '<tr class="tableHeader">';
	for ($i = 0; $i < sizeof($fields); $i++)
	{
		$field = key($fields);
		if($field != 'id' && $field != 'user' && $field != 'Description')
		{
			if ($field == 'date')
			{
				echo '<td align="center" '.($by==$field?'bgcolor="#336699"':'').'><b>';
				echo "<a href=\"$script?query=".urlencode($query)."&max=$max&at=$at&page=$page&user=$user";
				echo "&by=$field&order=".($order=="desc"?"asc":"desc").'" class="td_options">';
				echo "Date";
				echo '</a>';
				if ($by == $field)
					echo '  <img src="'.$order.'.gif" width="7" height="7" border="0">  ';
				'</b></td>';
			}
			else
			{
				echo '<td align="center" '.($by==$field?'bgcolor="#336699"':'').'><b>';
				echo "<a href=\"$script?query=".urlencode($query)."&max=$max&at=$at&page=$page&user=$user";
				echo "&by=$field&order=".($order=="desc"?"asc":"desc").'" class="td_options">';
				echo $field;
				echo '</a>';
				if ($by == $field)
					echo '  <img src="'.$order.'.gif" width="7" height="7" border="0">  ';
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
	global $fields, $actions, $script;

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
					echo "<td valign='top'>" . nl2br(htmlspecialchars($cols[key($fields)])) . "</td>";
				}
				else
				{
					echo "<td valign='top'>" . htmlspecialchars($cols[key($fields)]) . '</td>';
				}
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


function add_coinop_report()
{
	global $DB, $user, $id;

	$date = ($_POST["date"]);
	$Title = ($_POST["Title"]);
	$Description = ($_POST["Description"]);

	$statement = "INSERT INTO coinop_report (user, date, Title, Description)";

	$statement .= "VALUES ('".addslashes(htmlspecialchars($user))."', NULL, '".addslashes(htmlspecialchars($Title))."', '".addslashes(htmlspecialchars($Description))."')";
	$DB->query($statement);

	echo "New Report Added To The Database <br>";
}


function edit_coinop_report()
{
	global $DB, $user, $id;

	$Title = ($_POST["Title"]);
	$Description = ($_POST["Description"]);
	$statement = "UPDATE coinop_report SET
					user = '".addslashes(htmlspecialchars($user))."',
					date = NULL,
					Title = '".addslashes(htmlspecialchars($Title))."',
					Description = '".addslashes(htmlspecialchars($Description))."'
				WHERE id = '$id'";
	$DB->query($statement);

	echo "Zone ID:$id updated.<br>";
}

function delete_coinop_report()
{
	global $id, $DB;

	$statement = "DELETE FROM coinop_report WHERE id = '$id'";
	$DB->query($statement);
	echo "Report ID:$id Deleted.<br>";
}

if (trim($query) == '')
{

	switch ($action)
	{
		case "add":
			include 'coinop_report_add_form.inc.php';
			break;
		case "edit":
			include 'coinop_report_edit_form.inc.php';
			break;
		case "delete":
			include 'coinop_report_delete_form.inc.php';
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