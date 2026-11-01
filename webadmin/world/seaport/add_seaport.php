<script type="text/javascript">
//<![CDATA[
function check(obj)
{
	if (obj.elements['data[country]'].value == "")
	{
		alert("You have not selected a country");
		return false;
	}
	else return true;
}
//]]>
</script>
<?php
function display_fields()
{
	global $num, $DB;
?>
<form action="seaports.php" method="post" onSubmit="return check(this);"><table border="0" align="center" cellpadding="4" cellspacing="0">
	<tr>
		<td nowrap="nowrap" class="bottom">&nbsp;</td>
		<td class="bottom">&nbsp;</td>
	</tr>
<?php
	for ($i = 0; $i < $num; $i++)
	{
?>
	<tr>
		<td class="left" nowrap="nowrap"><b>New Seaport Name :</b></td>
		<td class="right"><input type="text" name="data[<?php echo $i ?>][name]" size="40" style="width:400px"></td>
	</tr>
<?php
	}
?>
	<tr>
		<td class="left" nowrap="nowrap"><b>Country :</b></td>
		<td class="right"><select name="data[country]" style="width:400px;">
			<option value="">[SELECT ONE]</option>
			<?=formatHTMLOptions(getCountryList(false))?>
		</select></td>
	</tr>
	<tr><td colspan="2" class="top">&nbsp;</td></tr>
	<tr>
		<td nowrap="nowrap">&nbsp;</td>
		<td><p>
			<input type="hidden" name="num" value="<?php echo $num ?>" />
			<input type="hidden" name="action" value="do_add" />
			<input type="submit" name="do_action" value="Add" />
			<input type="reset" name="clear" value="Clear" />
		</p></td>
	</tr>
</table></form>
<?php
}
function display_num()
{
?>
<form action="seaports.php" method="post" onsubmit="return check(this);"><table border="0" align="center" cellpadding="4" cellspacing="0">
	<tr>
		<td nowrap="nowrap" class="bottom">&nbsp;</td>
		<td class="bottom">&nbsp;</td>
	</tr>
	<tr>
		<td class="left" nowrap="nowrap"><b>Number of Seaports to be entered:</b></td>
		<td class="right"><input type="text" name="num" size="40" style="width:400px;" /></td>
	</tr>
	<tr><td colspan="2" class="top">&nbsp;</td></tr>
	<tr>
		<td nowrap="nowrap">&nbsp;</td>
		<td><p>
			<input type="hidden" name="action" value="add" />
			<input type="hidden" name="step" value="2" />
			<input type="submit" name="do_action" value="OK" />
			<input type="reset" name="clear" value="Clear" />
		</p></td>
	</tr>
</table></form>
<?php
}

$step = $_REQUEST['step'];
$num = $_REQUEST['num'];
if ($step == '1' || $step == "") display_num();
else display_fields();
?>