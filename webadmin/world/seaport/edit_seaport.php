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
<form action="seaports.php" method="post" onsubmit="return check(this);"><table border="0" align="center" cellpadding="4" cellspacing="0">
	<tr>
		<td nowrap class="bottom">&nbsp;</td>
		<td class="bottom">&nbsp;</td>
	</tr>
	<tr>
		<td class="left" nowrap><b>Seaport Name :</b></td>
<?php
$id =intval($posted['id']);
$seaport = $DB->query_first("SELECT `name`, `country` FROM `world`.`seaports` WHERE `id` = " . intval($id) . " LIMIT 1", MYSQLI_ASSOC);
?>
		<td class="right"><input type="text" name="data[name]" value="<?php hecho($seaport['name']) ?>" size="40" style="width:400px;" /></td>
	</tr>
	<tr>
		<td class="left" nowrap><b>Country :</b></td>
		<td class="right"><select name="data[country]" style="width:400px;">
			<option value="" selected="selected">[SELECT ONE]</option>
			<?=formatHTMLOptions(getCountryList(false), $seaport['country'])?>
		</select></td>
	</tr>
	<tr><td colspan="2" class="top">&nbsp;</td></tr>
	<tr>
		<td nowrap>&nbsp;</td>
		<td><p>
			<input type="hidden" name="id" value="<?php echo $id ?>" />
			<input type="hidden" name="action" value="do_edit" />
			<input type="submit" name="do_action" value="Update" />
			<input type="reset" name="clear" value="Clear" />
		</p></td>
	</tr>
</table></form>
