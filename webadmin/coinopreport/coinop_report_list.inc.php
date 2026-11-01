<html>
<head>
</head>
<body>
	  <table align='center'>
		<tr>
			<td colspan="100%" align='center' class="tableHeader"><h4>All Reports</h4></td>
		</tr>
<?php
				$statement = "SELECT DISTINCT startdate, enddate FROM coinop_reportweeks ORDER BY startdate DESC";
				$dates = $DB->query($statement);
				$shaded=false;
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
					$output =  "<td align='center'>"."<a href='$script?action=view&somedate=".$date."'>".$nicestart." - ".$niceend."</a></td></tr>";
					echo $output;
					$shaded = !$shaded;
				}
?>
	</table>
</body>
</html>