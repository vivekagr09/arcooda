<?php
if ($result = $DB->query(sprintf(
    "SELECT * FROM `highwaygroup`.`backorder_comment` WHERE `backorder` = %d ORDER BY `date` DESC",
    $id
))) {
?>
<table width="70%" style="border:1px solid;text-align:center;" cellspacing="2" cellpadding="2">
	<tr><td colspan="5" style="text-align:center;"><b>Previous Comments</b></td></tr>
	<tr style="background:#def;">
		<td height="28" nowrap="nowrap" style="background:#eed;text-align:center;" width="20%"><b>Date Added</b></div></td>
		<td height="28" nowrap="nowrap" style="background:#eed;text-align:center;" width="20%"><b>User</b></div></td>
		<td height="28" nowrap="nowrap" style="background:#eed;text-align:center;" width="60%"><b> Comments</b></div></td>
		<td height="28" nowrap="nowrap" style="background:#eed;text-align:center;" width="60%"><b> &nbsp;</b></div></td>
	</tr>
<?php
    $result = $DB->query($statement);
    $shaded = false;
    while ($row = $DB->fetch_array($result)) {
        $date  = $row['date'];
        $year  = substr($date, 0, 4);
        $month = substr($date, 5, 2);
        $day   = substr($date, 8, 2);
        $date  = $day . " - " . $month . " - " . $year;

        $person  = $row['person'];
        $comment = str_replace("\n", '<br />', stripslashes($row['comment']));
        if (true == $shaded) {echo '<tr bgcolor="#EEEEEE">';} else {echo '<tr bgcolor="#FFFFFF">';}
        echo '<td align="center" nowrap>';
        echo $date;
        echo '</td>';
        echo '<td align="center">';
        echo $person;
        echo '</td>';
        echo '<td align="left">';
        echo $comment;
        echo '</td>';
        echo '<td nowrap align="center"><a href="backOrders_comments.php?backorder=' . $row['backorder'] . '&action=edit&id=' . $row['id'] . '" target="_blank">Edit </a>|<a href="backOrders_comments.php?backorder=' . $row['backorder'] . '&action=delete&id=' . $row['id'] . '" target="_blank"> Delete</a></td>';
        echo '</tr>';
        if (true == $shaded) {$shaded = false;} else { $shaded = true;}
    }
    $DB->free_result($result);
?>
</table>
<?php
}
