<?php
/***********************************************************************
** Title.........:
** Version.......:
** Author........:	Xiang Wei ZHUO <c9807224@studentmail.newcastle.edu.au>
** Filename......:
** Last changed..:
** Notes.........:
***********************************************************************/
$statement = "SELECT COUNT(*) AS total, response FROM mailout_tmp_list GROUP BY response";
$overview_result = $DB->query($statement);

?>
<br><br>
	<table cellpadding="4" cellspacing="1" border="0" align="center">
		<tr bgcolor="#000099">
			<td class="td_options"><font color="#FFFFFF">Email Status</font></td><td class="td_options"><font color="#FFFFFF">Count</font></td>
		</tr>
<?php
$shade = true;
while ($row = $DB->fetch_array($overview_result))
{
	$response = $row['response']==''?'Pending':$row['response'];

	$bgcolor = ($shade = !$shade)?'#EEEEEE':'#FFFFFF';

	echo "<tr bgcolor=\"$bgcolor\"><td>$response</td><td align=right>".$row['total']."</td></tr>\n";
}
?>
	</table>