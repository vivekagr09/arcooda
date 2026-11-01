<?php
$title = 'Compose Email';
include 'email2/include/header.inc';
?>
<br />
<style type="text/css">
.textfields
{
	border:1px solid;
	font-size:8pt;
	font-family:verdana, arial, helvetica, sans-serif;
	height:16px
}
</style>
<form action="email.php3?page=preview_email" name="email" method="post"><table border="0" cellspacing="0" cellpadding="4" align="center" width="95%">
	<tr><td colspan="2" bgcolor="#eeeeee"><font size="2" color="#0000ff"><b><font color="#990000">NB.</font></b><font color="#990000"> All text containing [first] or/and [last] will be replace with the recipients with first name and last name respectively in the email</font></font></td></tr>
	<tr><td colspan="2">&nbsp;</td></tr>
	<tr><td colspan="2" bgcolor="#eeeeee"><font size="2"><b><font color="#000066">Step 1</font></b><font color="#000066"> - Enter the email subject.</font></font></td></tr>
	<tr>
		<td width="11%"><div align="right"><b>Subject:</b></div></td>
		<td width="89%"><input type="text" name="vars[subject]" size="50" class="textbox" value="<?php echo $db_vars['subject'] ?>" /></td>
	</tr>
	<tr><td valign="top" colspan="2">&nbsp;</td></tr>
	<tr><td valign="top" colspan="2" bgcolor="#eeeeee"><font size="2"><b><font color="#000066">Step 2</font></b><font color="#000066"> - Write the HTML message below, please check that the image tags and links are working.</font></font></td></tr>
	<tr>
		<td valign="top" colspan="2">
<?php
include 'email2/insert_components.php';
?>
			<div align="center"><textarea name="vars[html_message]" cols="40" rows="15" class="textbox" style="width:100%"></textarea></div>
		</td>
	</tr>
	<tr><td colspan="2">&nbsp;</td></tr>
	<tr><td colspan="2" bgcolor="#eeeeee"><b><font size="2" color="#000066">Step 3</font></b><font size="2" color="#000066"> - select what should be automatically added to the email</font></td></tr>
	<tr><td colspan="2"><table width="100%" border="0" cellspacing="0" cellpadding="0"><tr><td bgcolor="#cccccc"><table width="100%" border="0" cellspacing="1" cellpadding="0">
		<tr><td bgcolor="#ffffff"><table width="100%" border="0" cellspacing="1" cellpadding="2">
			<tr><td colspan="8" nowrap="nowrap"><font face="verdana, arial, helvetica, sans-serif" size="2"><b>Marketplace</b></font></td></tr>
			<tr>
				<td width="1%" nowrap="nowrap"><font face="Verdana, Arial, Helvetica, sans-serif" size="2">
					<input type="checkbox" name="trade_news" value="1"<?php echo ($db_vars['send_specials']==1)?' checked="checked"':'' ?> />
					<input type="text" name="tn_x" size="1" maxlength="2" value="3" class="textfields" />
				</font></td>
				<td width="24%" nowrap="nowrap"><font face="verdana, arial, helvetica, sans-serif" size="2">Trading News</font></td>
				<td width="1%" nowrap="nowrap"><font face="verdana, arial, helvetica, sans-serif" size="2">
					<input type="checkbox" name="trade_specials" value="1"<?php echo ($db_vars['send_specials']==1)?' checked="checked"':'' ?> />
					<input type="text" name="sp_x" size="1" maxlength="2" value="30" class="textfields" />
				</font></td>
				<td width="24%" nowrap="nowrap"><font face="verdana, arial, helvetica, sans-serif" size="2">Trading Specials</font></td>
				<td width="1%" nowrap="nowrap"><font face="verdana, arial, helvetica, sans-serif" size="2">
					<input type="checkbox" name="classified" value="1"<?php echo ($db_vars['send_specials']==1)?' checked="checked"':'' ?> />
					<input type="text" name="cl_x" size="1" maxlength="2" value="20" class="textfields" />
				</font></td>
				<td width="24%" nowrap="nowrap"><font face="verdana, arial, helvetica, sans-serif" size="2">Classified</font></td>
				<td width="1%" nowrap="nowrap"><font face="verdana, arial, helvetica, sans-serif" size="2">
					<input type="checkbox" name="wanted" value="1"<?php echo ($db_vars['send_specials']==1)?' checked="checked"':'' ?> />
					<input type="text" name="wn_x" size="1" maxlength="2" value="20" class="textfields" />
				</font></td>
				<td width="24%" nowrap="nowrap"><font face="verdana, arial, helvetica, sans-serif" size="2">Wanted</font></td>
			</tr>
		</table></td></tr>
		<tr><td bgcolor="#ffffff"><table width="100%" border="0" cellspacing="1" cellpadding="2">
			<tr><td colspan="8" nowrap="nowrap"><font face="verdana, arial, helvetica, sans-serif" size="2"><b>Weekly news reports</b></font></td></tr>
			<tr>
				<td width="1%" nowrap="nowrap"><font face="Verdana, Arial, Helvetica, sans-serif" size="2">
					<input type="checkbox" name="news" value="1"<?php echo ($db_vars['send_weekly']==1)?' checked="checked"':'' ?> />
					<input type="text" name="nw_x" size="1" maxlength="2" value="3" class="textfields" />
				</font></td>
				<td width="24%" nowrap="nowrap"><font face="verdana, arial, helvetica, sans-serif" size="2">Latest News</font></td>
				<td width="1%" nowrap="nowrap"><font face="Verdana, Arial, Helvetica, sans-serif" size="2">
					<input type="checkbox" name="products" value="1"<?php echo ($db_vars['send_weekly']==1)?' checked="checked"':'' ?> />
					<input type="text" name="pd_x" size="1" maxlength="2" value="3" class="textfields" />
				</font></td>
				<td width="24%" nowrap="nowrap"><font face="verdana, arial, helvetica, sans-serif" size="2">Latest Products </font></td>
				<td width="1%" nowrap="nowrap"> <font face="verdana, arial, helvetica, sans-serif" size="2">
					<input type="checkbox" name="forum" value="1"<?php echo ($db_vars['send_weekly']==1)?' checked="checked"':'' ?> />
					<input type="text" name="fm_x" size="1" maxlength="2" value="3" class="textfields" />
				</font></td>
				<td width="24%" nowrap="nowrap"><font face="verdana, arial, helvetica, sans-serif" size="2">Forum Discussion</font></td>
				<td width="1%" nowrap="nowrap"> <font face="verdana, arial, helvetica, sans-serif" size="2">
					<input type="checkbox" name="trade_shows" value="1"<?php echo ($db_vars['send_weekly']==1)?' checked="checked"':'' ?> />
					<input type="text" name="ts_x" size="1" maxlength="2" value="3" class="textfields" />
				</font></td>
				<td width="24%" nowrap="nowrap"><font face="verdana, arial, helvetica, sans-serif" size="2">Trade shows</font></td>
			</tr>
		</table></td></tr>
	</table></td></tr></table></td></tr>
	<tr><td colspan="2">&nbsp;</td></tr>
	<tr bgcolor="#eeeeee"><td colspan="2"><font size="2" color="#000066"><b>Step 4</b> - To add attachement, click &quot;Attachments&quot;. Click &quot;Preview&quot; to continue</font></td></tr>
	<tr><td colspan="2" align="center">&nbsp;</td></tr>
	<tr><td colspan="2" align="center">
			<input type="button" name="Submit" value="&lt;&lt; Back" onclick="history.back(-1)" class="button" />
			<input type="button" name="Submit2" value="Attachments" onclick="MM_openBrWindow('email2/attachment.php3', 'addatt', 'scrollbars=yes,resizable=yes,width=500,height=450')" class="button" />
			<input type="submit" name="Submit" value="Preview >>" class="button" />
	</td></tr>
</table></form>
<br />
<?php
$vars['last_activity'] = last_activity('Composing Email');
set_vars($vars);
include 'email2/include/footer.inc';
?>