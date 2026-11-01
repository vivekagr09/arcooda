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

include 'coinop_report_date_functions.inc.php';
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

$script = 'coinop_report_admin.php';

$fields['id'] = 'left';
$fields['user'] = 'left';
$fields['date'] = 'left';
$fields['Title'] = 'left';
$fields['Description'] = 'left';

$user = ucwords(strtolower($user));
$items = $DB->query_first("SELECT COUNT(*) as total FROM coinop_report");
$gets = get_vars();

$actions[] = '<a href="$script?action=edit&id=$id">Edit</a>';
$actions[] = '<a href="$script?action=delete&id=$id">Delete</a>';
$statement = "DELETE FROM `coinop_reportweeks` WHERE `id` >= 0 ";
$DB->query($statement);

$statement = "SELECT DISTINCT date FROM coinop_report";
$dates = $DB->query($statement);
while($row = $DB->fetch_array($dates))
{
		$date = $row[date];

		$start = startOfWeek($date);
		$startyear = substr($start,0,4);
		$startmonth = substr($start,5,2);
		$startday = substr($start,8,2);
		$startdate = $startyear.$startmonth.$startday;

		$end = endOfWeek($date);
		$endyear = substr($end,0,4);
		$endmonth = substr($end,5,2);
		$endday = substr($end,8,2);
		$enddate = $endyear.$endmonth.$endday;

		$statement = "INSERT INTO coinop_reportweeks (startdate, enddate)";
		$statement .= "VALUES ('".addslashes(htmlspecialchars($startdate))."', '".addslashes(htmlspecialchars($enddate))."')";
		$DB->query($statement);
}

?>
<script src="tooltip.js" type="text/javascript"></script>
<META HTTP-EQUIV="refresh">
<form accept-charset="utf-8" autocomplete="off" method="post" action="<?= $script.$gets ?>">

	<br>
      <table width="100%" border="0" cellspacing="2" cellpadding="2">
        <tr>
          <td bgcolor="#EEEEEE" width="20%">
            <div align="center"><b>Master Account for Report to Highway Games</b>
			</div>
          </td>

          <td bgcolor="#EEEEEE" width="80%">
            <div align="right">
			<!--<a href="<?= $script ?>?action=add" tooltip="This will add a report under your name">Add New Report </a> | -->
			<a href="<?= $script ?>?action=list" tooltip="Click to see a list of weeks that have reports">All Reports(<?= $items[total] ?>)</a>
			</div>
          </td>
        </tr>
      </table>
	  <table>
		<!--
		<tr><td colspan='100%'><center><b><a href="<?= $script ?>?action=list" tooltip="Click to see a list of weeks that have reports">Recent Reports</a></b><center></td></tr>
		<tr>

<?php
				$statement = "SELECT DISTINCT startdate, enddate FROM coinop_reportweeks ORDER BY startdate ASC LIMIT 4";
				$dates = $DB->query($statement);
				while($row = $DB->fetch_array($dates))
				{
					$date = $row[startdate];

					$str1 = $row[startdate];
					$startyear = substr($str1,0,4);
					$startmonth = substr($str1,4,2);
					$startday = substr($str1,6,2);
					$nicestart = $startday."/".$startmonth."/".$startyear;


					$str2 = $row[enddate];
					$endyear = substr($str2,0,4);
					$endmonth = substr($str2,4,2);
					$endday = substr($str2,6,2);
					$niceend = $endday."/".$endmonth."/".$endyear;

					$output =  "<td>"."<a href='$script?action=view&somedate=".$date."'> | ".$nicestart." - ".$niceend." | </a></td>";
					echo $output;
				}

?>-->
		  </tr>
	</table>
	<hr>
<?php
function search($query)
{
	global $DB, $at, $fields, $order, $by;
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
	$statement = "SELECT * FROM coinop_report";

	//$service .=  $prop_id;
	//if max is not yet set
	if (!isset($max))
	{
		$result = $DB->query($statement.$extra);
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
	$result = $DB->query($statement.$extra.$order_by.$limit);

	//show_links2(get_links($max, "$script?query=".urlencode($query)."&max=$max&by=$by&order=$order",25,4));

	draw_header_row();
	$shaded = true;
	while ($row = $DB->fetch_array($result))
	{
		show_result($row, $shaded = !$shaded);
	}
	draw_header_end();
}

function draw_header_row()
{
	echo "<form accept-charset=\"utf-8\" autocomplete=\"off\" method=\"post\" action='coinop_report_print.php' target='_blank'>";
	//echo "<table width='75%' align='Left' name='results'>";
	global $fields, $order, $by, $query, $max, $at, $page, $script;

	reset($fields);
	echo "<br>";
	echo '<table width="600px" align="center">';
	echo '<tr>';
	for ($i = 0; $i < sizeof($fields); $i++)
	{
		$field = key($fields);
		if($field != 'id' && $field != 'Description')
		{
			echo "<td class='tableHeader'><b>";
			if($field == 'date')
			{
				echo "Date";
			}
			else if($field == 'user')
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
	global $prop_id;

	reset($fields);
	$id = $cols[key($fields)];
	echo $shaded?'<tr class="tableRow">':'<tr class="tableRowAlt">';
	for ($i = 0; $i < sizeof($fields); $i++)
	{
		$field = key($fields);
		if($field != 'id' && $field != 'Description')
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
				echo "<td valign='top'>".(stripslashes($cols[key($fields)])).'</td>';
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
		if ($i != 0)
			echo " | ";
		$str = addslashes($actions[$i]);
		eval ("\$str = \"$str\";");
		echo "$str";

	}
	echo '</td>';
	echo "</tr>";


}


function draw_header_end()
{

	//echo "<input type='hidden' name='printdate' value=$somedate>";
	echo "<input type='hidden' name='action' value='print'>";
	echo "<tr><td colspan='3' align='right'>Check All</td><td nowrap><input type='checkbox' name='selectall' onclick='selectAll(this.form,0);'>  </td><td><input type='submit' target='_blank' tooltip='Click to preview selected reports' value='Print Selected'></td></tr>";
	echo '</table>';
	echo "<br>";

	echo "</form>";

}
function show_links2($links){
//show more links.

	if (!(is_array($links) && count($links) > 1))
		return;

	//the << link to a previous set of links
	echo "<DIV ALIGN='Left'><FONT FACE='Verdana, Arial, Helvetica, sans-serif' SIZE='2'><b>Showing : $links[start_from] - $links[ends_at] of $links[max] | </b>";

	if (isset($links['url_prev_page']) && $prev_page = $links['url_prev_page'])
		echo " <A HREF=\"$prev_page\"><<</A> | ";

	//for each of the links
	for ($i = 0;$i < count($links['links']);$i++)
	{
		//get the link ID
		$key = key($links['links']);

		//if the link contains an URL, show it
		if (isset($links['links'][$key]['url']) && $url = $links['links'][$key]['url'])
			echo " <A HREF=\"$url\">".$links['links'][$key]['start']." - ".$links['links'][$key]['end']."</A>";
		else
		//else just show where the records starts and ends
			echo $links['links'][$key]['start'] ." - ". $links['links'][$key]['end'];
		echo ($i < count($links['links']) - 1)?" | ":"";
		next($links['links']);
	}

	//the >> link to the next set of links
	if (isset($links['url_next_page']) &&$next_page = $links['url_next_page'])
		echo " <A HREF=\"$next_page\">>></A> ";
	echo '</FONT></DIV>';
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
	global $DB, $id;
	$Title = ($_POST["Title"]);
	$Description = ($_POST["Description"]);
	$statement = "UPDATE coinop_report SET
					Title = '".addslashes(htmlspecialchars($Title))."',
					Description = '".addslashes(htmlspecialchars($Description))."'
				WHERE id = '$id'";
	$DB->query($statement);

	echo "Report ID:$id updated.<br>";
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
		case "do_add":
			add_coinop_report();
			break;
		case "do_edit":
			edit_coinop_report();
			break;
		case "do_delete":
			delete_coinop_report();
			break;
		case "add":
			include 'coinop_report_add_form.inc.php';
			break;
		case "edit":
			include 'coinop_report_admin_edit_form.inc.php';
			break;
		case "delete":
			include 'coinop_report_delete_form.inc.php';
			break;
		case "view":
			include 'coinop_report_display_form.inc.php';
			break;
		case "print":
?>
	<frame name="printFrame" id="myFrame">
<?php
				include 'coinop_report_print.php';
?>
	</frame>
<?php

			break;
		case "list":
			include 'coinop_report_list.inc.php';
			break;

	}
}


?>
</form>


<?php

include '../footer.inc.php';
