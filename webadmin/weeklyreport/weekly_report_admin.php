<?php
include_once __DIR__ . '/common.inc.php';

$user = get_user();
$allowed_sections = get_allowed_sections($user);
if (has_permission('COE_REP_AD', $allowed_sections))
{
	include 'weekly_report_date_functions.inc.php';
?>
<script language="JavaScript" type="text/javascript">
function selectAll(formObj, isInverse)
{
   for (var i=0;i < formObj.length;i++)
   {
	  fldObj = formObj.elements[i];
      if (fldObj.type == 'checkbox')
      {
         if(isInverse)
            fldObj.checked = (fldObj.checked) ? false : true;
         else fldObj.checked = true;
       }
   }
}
</script>
<?php
	$script = 'weekly_report_admin.php';
	$fields[id] = 'left';
	$fields[user] = 'left';
	$fields[date] = 'left';
	$fields[Title] = 'left';
	$fields[Description] = 'left';
	$user = ucwords(strtolower($user));
	$items = $DB->query_first("SELECT COUNT(*) AS `total` FROM `highwaygroup`.`weekly_report` WHERE `from` = '" . $from . "' AND `to` = '" . $to . "'");
	$gets = get_vars();
	$actions[] = '<a href="$script?action=edit&id=$id&from=$from&to=$to">Edit</a>';
	$actions[] = '<a href="$script?action=delete&id=$id&from=$from&to=$to">Delete</a>';
	$statement = "TRUNCATE TABLE reportweeks";
	$DB->query($statement);
	$statement = "SELECT DISTINCT DATE_FORMAT(`date`, '%Y%m%d') AS `date` FROM `highwaygroup`.`weekly_report` WHERE `from` = '" . $from . "' AND `to` = '" . $to . "'";
	$dates = $DB->query($statement);
	while ($row = $DB->fetch_array($dates))
	{
		$date = $row[date];
		$start = startOfWeek($date);
		$startyear = substr($start, 0, 4);
		$startmonth = substr($start, 5, 2);
		$startday = substr($start, 8, 2);
		$startdate = $startyear . $startmonth . $startday;
		$end = endOfWeek($date);
		$endyear = substr($end, 0, 4);
		$endmonth = substr($end, 5, 2);
		$endday = substr($end, 8, 2);
		$enddate = $endyear . $endmonth . $endday;
		$statement = "INSERT INTO reportweeks (startdate, enddate)";
		$statement.= "VALUES ('" . addslashes(htmlspecialchars($startdate)) . "', '" . addslashes(htmlspecialchars($enddate)) . "')";
		$DB->query($statement);
	}
?>
<script src="tooltip.js" type="text/javascript"></script>
<META HTTP-EQUIV="refresh">
<form action="<?= $script.$gets.'&email='.$email ?>" method="post" name='blah' id='blah'>
	<br />
      <table width="100%" border="0" cellspacing="2" cellpadding="2">
        <tr>
          <td bgcolor="#EEEEEE" width="20%">
<?php
	$toname = $DB->query_first("SELECT * FROM `highwaygroup`.`websites` WHERE `id` = '" . $to . "'");
	$fromname = $DB->query_first("SELECT * FROM `highwaygroup`.`websites` WHERE `id` = '" . $from . "'");
?>
            <div align="center"><b>Master Account for <?= $fromname['website'] ?> Reports for <?= $toname['website'] ?></b>
			</div>
          </td>

          <td bgcolor="#EEEEEE" width="80%">
            <div align="right">
			<!--<a href="<?= $script ?>?action=add" tooltip="This will add a report under your name">Add New Report </a> | -->
			<a href="<?= $script ?>?action=list&from=<?= $from ?>&to=<?= $to ?>" tooltip="Click to see a list of weeks that have reports">All Reports(<?= $items[total] ?>)</a>
			</div>
          </td>
        </tr>
      </table>
	  <table>
		  </tr>
	</table>
	<hr>
<?php
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
		case "add":
			include 'weekly_report_add_form.inc.php';
			break;
		case "edit":
			include 'weekly_report_admin_edit_form.inc.php';
			break;
		case "delete":
			include 'weekly_report_delete_form.inc.php';
			break;
		case "view":
			include 'weekly_report_display_form.inc.php';
			break;
		case "print":
?>
			<frame name="printFrame" id="myFrame">
<?php
			include 'weekly_report_print.php';
?>
			</frame>
<?php
			break;
		case "list":
			include 'weekly_report_list.inc.php';
			break;
		}
	}
	//if(!isset($action))
	//{
	//	$query = htmlspecialchars($query);
	//search(addslashes($query));
	//}

?>
</form>
<script defer language="JavaScript" type="text/javascript">
<?php
	if ($email)
	{
		echo 'selectAll(document.forms[0],0);';
	}
?>
</script>

<?php
	include '../footer.inc.php';
}

function search($query)
{
	global $DB, $at, $fields, $order, $by, $from, $to;
	global $script;
	//keywords
	$arrKeywords = explode(" ", trim($query));
	//search query by key words
	if (!empty($query)) $extra = " ";
	for ($index = 0;$index < count($arrKeywords);$index++)
	{
		$word = trim(strtolower($arrKeywords[$index]));
		if (!empty($word)) $extra.= " AND ";
		if (!empty($word))
		{
			if (preg_match("/^\\d+$/", $word)) //if its a search by id
			$extra.= "(s.ss_id = '$word' OR s.id = '$word')";
			elseif (preg_match("/^[\\*\\+\\\\]/", $word)) $extra.= "(LOWER(item) LIKE '%$word%')";
			else $extra.= "(LOWER(item) REGEXP \"$word\")"; //else just a normail regular exp. search

		}
	} //end search query keywords
	//search statement
	echo $statement = "SELECT DATE_FORMAT(`date`, '%Y%m%d') AS `date` FROM `highwaygroup`.`weekly_report` WHERE `from` = '" . $from . "' AND `to` = '" . $to . "'";
	//$service .=  $prop_id;
	//if max is not yet set
	if (!isset($max))
	{
		$result = $DB->query($statement . $extra);
		$max = $DB->num_rows($result);
	}
	$inc = 25;
	$at = intval($at);
	if (!isset($order)) $order = 'asc';
	if (!isset($by)) $by = 'date';
	$order_by = " ORDER BY $by $order";
	$limit = " LIMIT $at, $inc ";
	//echo $statement.$extra;
	$result = $DB->query($statement . $extra . $order_by . $limit);
	//show_links2(get_links($max, "$script?query=".urlencode($query)."&max=$max&by=$by&order=$order",25,4));
	draw_header_row();
	$shaded = true;
	while ($row = $DB->fetch_array($result))
	{
		show_result($row, $shaded = !$shaded);
	}
	draw_header_end();
	//show more links
	//show_links2(get_links($max, "$script?query=".urlencode($query)."&max=$max&by=$by&order=$order",25,4));

}

function draw_header_row()
{
	global $from, $to;
	echo "<form action='weekly_report_print.php?from=" . $from . "&to=" . $to . "' target='_blank' method='POST'>";
	//echo "<table width='75%' align='Left' name='results'>";
	global $fields, $order, $by, $query, $max, $at, $page, $script;
	reset($fields);
	echo "<br />";
	echo '<table width="600px" align="center">';
	echo '<tr>';
	for ($i = 0;$i < sizeof($fields);$i++)
	{
		$field = key($fields);
		if ($field != 'id' && $field != 'Description')
		{
			//echo '<td align="left" '.($by==$field?'bgcolor="#336699"':'').'><b>';
			//echo "<a href=\"$script?query=".urlencode($query)."&max=$max&at=$at&page=$page&user=$user";
			//echo "&by=$field&order=".($order=="desc"?"asc":"desc").'" class="td_options">';
			echo "<td class='tableHeader'><b>";
			if ($field == 'date')
			{
				echo "Date";
			}
			elseif ($field == 'user')
			{
				echo "Staff";
			}
			else
			{
				echo $field;
			}
			//echo '</a>';
			//if ($by == $field)
			//	echo '  <img src="'.$order.'.gif" width="7" height="7" border="0"> </b>';
			echo '</b></td>';
		}
		next($fields);
	}
	echo "<td align='left' class='tableHeader'><b>View</b></td>";
	echo "<td align='left' class='tableHeader'><b>Modify</b></td>";
	echo '</tr>';
}

function show_result($cols, $shaded)
{
	global $fields, $actions, $script;
	global $prop_id, $from, $to;
	reset($fields);
	$id = $cols[key($fields) ];
	echo $shaded ? '<tr class="tableRow">' : '<tr class="tableRowAlt">';
	for ($i = 0;$i < sizeof($fields);$i++)
	{
		$field = key($fields);
		if ($field != 'id' && $field != 'Description')
		{
			if ($field == 'date')
			{
				$str1 = (stripslashes($cols[key($fields) ]));
				$year = substr($str1, 0, 4);
				$month = substr($str1, 4, 2);
				$day = substr($str1, 6, 2);
				echo $nice = "<td valign='top'>" . $day . "/" . $month . "/" . $year . '</td>';
			}
			elseif ($field == 'user')
			{
				echo "<td valign='top'>" . niceusername($cols[key($fields) ]) . '</td>';
			}
			else
			{
				echo "<td valign='top'>" . (stripslashes($cols[key($fields) ])) . '</td>';
			}
			//echo "<td align=\"".$fields[key($fields)]."\">";
			//echo (stripslashes($cols[key($fields)])).'</td>';

		}
		next($fields);
	}
	echo "<td nowrap align='left'><INPUT TYPE='CHECKBOX' Name=idarray[] Value='$id'></td>";
	echo '<td nowrap>';
	for ($i = 0;$i < sizeof($actions);$i++)
	{
		if ($i != 0) echo " | ";
		$str = addslashes($actions[$i]);
		eval("\$str = \"$str\";");
		echo "$str";
	}
	echo '</td>';
	echo "</tr>";
}

function draw_header_end()
{
	global $from, $to, $email;
	//echo "<input type='hidden' name='printdate' value=$somedate>";
	echo "<input type='hidden' name='action' value='print'>";
	echo "<tr><td colspan='3' align='right'>Check All</td><td nowrap><input type='checkbox' name='selectall' onclick='selectAll(this.form,0);'>  </td><td>";
	if ($email)
	{
		echo "<input type='submit' target='_blank' tooltip='Click to email selected reports' value='Email Selected To:'>";
	}
	else
	{
		echo "<input type='submit' target='_blank' tooltip='Click to preview selected reports' value='Print Selected'>";
	}
	echo "</td></tr>";
	//echo "<tr><td colspan='3'></td><td><input type='button' value='Invert' name='selectall'  onclick='selectAll(this.form,1);'></td></tr>";
	echo '</table>';
	echo "<br />";
	echo "</form>";
}

function show_links2($links)
{
	global $from, $to;
	//show more links.
	if (!(is_array($links) && count($links) > 1)) return;
	//the << link to a previous set of links
	echo "<DIV ALIGN='Left'><FONT FACE='Verdana, Arial, Helvetica, sans-serif' SIZE='2'><b>Showing : $links[start_from] - $links[ends_at] of $links[max] | </b>";
	if (isset($links['url_prev_page']) && $prev_page = $links['url_prev_page']) echo " <A HREF=\"$prev_page\"><<</A> | ";
	//for each of the links
	for ($i = 0;$i < count($links['links']);$i++)
	{
		//get the link ID
		$key = key($links['links']);
		//if the link contains an URL, show it
		if (isset($links['links'][$key]['url']) && $url = $links['links'][$key]['url']) echo " <A HREF=\"$url\">" . $links['links'][$key]['start'] . " - " . $links['links'][$key]['end'] . "</A>";
		else
		//else just show where the records starts and ends
		echo $links['links'][$key]['start'] . " - " . $links['links'][$key]['end'];
		echo ($i < count($links['links']) -1) ? " | " : "";
		next($links['links']);
	}
	//the >> link to the next set of links
	if (isset($links['url_next_page']) && $next_page = $links['url_next_page']) echo " <A HREF=\"$next_page\">>></A> ";
	echo '</FONT></DIV>';
}

function add_weekly_report()
{
	global $DB, $user, $id, $from, $to;
	$date = ($_POST["date"]);
	$Title = ($_POST["Title"]);
	$Description = ($_POST["Description"]);
	$statement = "INSERT INTO `highwaygroup`.`weekly_report` (`user`, `Title`, Description, `from`, `to`)";
	$statement.= "VALUES ('" . addslashes(htmlspecialchars($user)) . "', '" . addslashes(htmlspecialchars($Title)) . "', '" . addslashes(htmlspecialchars($Description)) . "', '" . addslashes(htmlspecialchars($from)) . "', '" . addslashes(htmlspecialchars($to)) . "')";
	$DB->query($statement);
	echo "New Report Added To The Database <br />";
}

function edit_weekly_report()
{
	global $DB, $id, $from, $to;
	$Title = ($_POST["Title"]);
	$Description = ($_POST["Description"]);
	$statement = "UPDATE `highwaygroup`.`weekly_report` SET
				`Title` = '" . addslashes(htmlspecialchars($Title)) . "',
				`Description` = '" . addslashes(htmlspecialchars($Description)) . "',
				`from` = '" . addslashes(htmlspecialchars($from)) . "',
				`to` = '" . addslashes(htmlspecialchars($to)) . "'
			WHERE `id` = '$id'";
	$DB->query($statement);
	echo "Report ID:$id updated.<br />";
}

function delete_weekly_report()
{
	global $id, $DB, $from, $to;
	$statement = "DELETE FROM `highwaygroup`.`weekly_report` WHERE `id` = '$id'";
	$DB->query($statement);
	echo "Report ID:$id Deleted.<br />";
}
?>