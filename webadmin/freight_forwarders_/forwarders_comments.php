<?php
include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/header.inc.php';

include_once '../functions/quote_functions.inc.php';

$id = $_REQUEST['id'];

$statement = "SELECT * FROM `coinop`.`freight_forwarders_comments` WHERE `forwarder_id` = $id ORDER BY `date` DESC";

$n_comment = $DB->query($statement);

?>
<br />
<div id="content">
<table width="500" border="0" cellspacing="0" cellpadding="2" align="center" style="border-collapse: collapse; border: 1px solid #CCCCCC">
	<tr>
		<th colspan="3" class="bottomLeftRight">Admin Notes for Forwarder ID <?=$id?></th>
	</tr>
	<tr class = "alt">
		<td width="10%" align="center" nowrap class="bottomLeft"><strong>Date Added</strong></td>
		<td width="8%" align="center" nowrap class="bottomLeft"><strong>Staff</strong></td>
		<td width="82%" align="center" class="bottomLeftRight"><strong>Notes</strong></td>
	</tr>
<?php
while ($comm = $DB->fetch_assoc($n_comment)) {
    //while($counter <= count($comment))
    if ($counter % 2 == 1) {
        echo '<tr class = "alt">';
    } else {
        echo '<tr>';
    }

    //echo "<tr bgcolor=\"".($counter%2==1?"#EEEEEE":"#FFFFFF")."\">\n";
    echo "<td class=\"bottomLeft\" valign=\"top\" nowrap align=\"center\" style=\"padding:16px 16px 16px 16px;border:1px solid #DDDDDD;\">" . date('d M, Y', strtotime($comm['date'])) . "</td>\n";
    echo "<td class=\"bottomLeft\" valign=\"top\" nowrap align=\"center\" style=\"padding:16px 16px 16px 16px;border:1px solid #DDDDDD;\">" . $comm['staff'] . "&nbsp;</td>\n";
    echo "<td class=\"bottomLeftRight\" style=\"padding:16px 16px 16px 16px;border:1px solid #DDDDDD;\">" . nl2br(stripslashes($comm['comment'])) . "</td>\n";
    echo "</tr>\n";
    $counter++;
}
?>
</table>