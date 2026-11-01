<!--
Created and modified by Mingze Xi@ Feb 9, 2015
Last Update: Feb 16, 2015

Update log:

Problem:


Finished:100%


The product about section starts here, based on products_comments.tpl.php @AGS/products/template
-->
<!-- products_adminnotes.tpl -->
<div id="sectionNote">
<div align="center">
	<?php echo formatHTMLTitle('<a href="javascript:toggleSection(\'sectionNote\');">Admin Notes</a>');?>
</div>
<div id="sectionNote">
<table class="table_fields">
	<tr>
		<td style="text-align: right;background-color: #EEEEE0;color: #000000;font-weight: bold">Admin Notes :<br /><br /><font color="red">Admin notes are never<br />displayed on the main site</font></td>
		<td colspan=3 style="width:82%"><textarea rows=15 style="width:100%" name="admin_notes"></textarea></td>
	</tr>
</table>

<?php
	$statement = "SELECT *, UNIX_TIMESTAMP(date_added) AS date_added FROM product_admin_notes WHERE product_id = '$id' ORDER BY date_added DESC";
	$results = $DB->query($statement);
	$total_admin_notes = $DB->num_rows($results);
	if ($total_admin_notes > 0)
	{
?>
	<br />
	<table align="center" style="width:95%;padding-left: 3;border: 1px solid #CCCCCC;border-collapse: collapse;margin-left: auto;margin-right: auto;">
		<tr>
			<th style="background-color:#FFCFAF;font-weight:bold;font-size:12" colspan="3">Previous Notes</th>
		</tr>
		<tr style="background-color:#E3EBF7">
			<td width="5%" align="center" nowrap class="bottomLeft"><b>Date Added</b></td>
			<td width="10%" align="center" nowrap class="bottomLeft"><b>User</b></td>
			<td width="85%" align="center" nowrap class="bottomLeftRight"><b>Notes</b></td>
		</tr>
<?php
		for ($i = 0; $i < $total_admin_notes && $i < 5; $i++)
		{
			$row = $DB->fetch_array($results);
?>
		<tr <?php echo ($i%2==1)?'bgcolor="#EEEEEE"':'bgcolor="#FFFFFF"'?>>
			<td nowrap class="bottomLeft" style="text-align:center"><?php echo date('d M, Y', $row['date_added'])?></td>
			<td nowrap class="bottomLeft" style="text-align: center"><?php echo $row['user']?></td>
			<td class="bottomLeftRight" style="text-align:left"><?php echo nl2br(htmlspecialchars($row['note']))?></td>
		</tr>
<?php
		}
?>
	</table>

<?php
		if ($total_admin_notes >= 5)
		{
?>
		<a href="javascript:newWin = window.open('/webadmin/products/view_admin_notes.php?id=<?php echo $id
?>','noteWin','width=600,height=450,resizable,scrollbars'); newWin.focus();">More Admin Notes... </a>
<?php
		}
?>
		<br /><br />

<?php
	}
?>
<br/>
</div>