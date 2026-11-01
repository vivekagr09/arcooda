<table width="70%" border="1" align="center" cellspacing="2" cellpadding="2">
  <tr>
	<td colspan=4 align="center"><b>Previous Comments</b></td>
  </tr>
  <tr bgcolor="#E3EBF7">
    <td height="28" nowrap bgcolor="#EEEEE0" width="20%" align="center"><b>Date Added</b></div></td>
    <td height="28" nowrap bgcolor="#EEEEE0" width="20%" align="center"><b>User</b></div></td>
    <td height="28" nowrap bgcolor="#EEEEE0" width="60%" align="center"><b> Comments</b></div></td>
  </tr>

<?php
	$statement = "SELECT * FROM rma_comments WHERE rma='$id' ORDER BY date DESC";
	$result = $DB->query($statement);
	$shaded = false;
    while ($row = $DB->fetch_array($result))
	{
		$date = $row['date'];
		$year = substr($date, 0, 4);
		$month = substr($date, 5,2);
		$day = substr($date,8,2);
		$date = $day. " - ".$month. " - ".$year;

		$person = $row['person'];
		$comment = $row['comment'];

		if($shaded == true){echo '<tr bgcolor="#EEEEEE">';}
		else{echo '<tr bgcolor="#FFFFFF">';}
        echo '<td align="center">';
		echo $date;
		echo '</td>';
        echo '<td align="center">';
		echo $person;
		echo '</td>';
        echo '<td align="left">';
		echo $comment;
		echo '</td>';
		echo '</tr>';
		if($shaded == true){$shaded = false;}
		else{$shaded = true;}
	}
?>

</table>