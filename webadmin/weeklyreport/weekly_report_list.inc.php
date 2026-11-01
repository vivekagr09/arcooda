<html>
<head>
</head>
<body>
	  <table align='center'>
		<tr>
			<td colspan="100%" align='center' class="tableHeader"><h4>All Reports</h4></td>
		</tr>
<?php
				$statement = "SELECT DISTINCT startdate, enddate FROM reportweeks ORDER BY startdate DESC";
				$dates = $DB->query($statement);
				$shaded=false;
				echo '</form>';
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

					echo $shaded?'<tr class="tableRow">':'<tr class="tableRowAlt">';
					$output =  "<td align='center' valign='center' nowrap><a href='";
					$link = "$script?action=view&from=".$from."&to=".$to."&somedate=".$date;
					 $output .= $link."'>".$nicestart." - ".$niceend."</a>";
					 $output .= '</td><form action="'.$link.'&email=true" method="post"><td align="center" valign="center" nowrap>';
					$output .= '<input type="submit" value="Send Email"></input>';
					$output .= "</td></form></tr>";
					echo $output;
					$shaded = !$shaded;
				}
?>
	</table>
</body>
</html>