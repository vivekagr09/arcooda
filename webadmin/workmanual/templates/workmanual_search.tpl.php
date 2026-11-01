<table align="center" style="border:1px solid #FF9900;width:95%;">
	<form action="index.php?action=do_search" method="post">
	<tr><td colspan="2" style="text-align:center;background-color:#FFFFCC;"><h2 style="color:#FF9900;">Search Work Manuals</h2></td></tr>
	<tr>
		<td style="text-align:right;background-color:#FFFFCC;width:40%;">Search For Text:</td>
		<td style="text-align:left;background-color:#FFFFCC;width:60%;"><input type="text" name="any_text" value="" /></td>
	</tr>
	<tr>
		<td style="text-align:right;background-color:#FFFFCC;">Country:</td>
		<td style="text-align:left;background-color:#FFFFCC;"><select name="country_id">
			<option value=""></option>
			<?= formatHTMLOptions($country_list) ?>
		</select></td>
	</tr>
	<tr>
		<td style="text-align:right;background-color:#FFFFCC;">Continent:</td>
		<td style="text-align:left;background-color:#FFFFCC;"><select name="continent_id">
			<option value=""></option>
			<?= formatHTMLOptions(getContinentList()) ?>
		</select></td>
	</tr>
	<tr><td colspan="2" style="text-align:center;background-color:#FFFFCC;"><input type="submit" value="Search" /></td></tr>
	</form>
</table>
