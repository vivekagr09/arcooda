<table align="center" style="border:1px solid #FF9900;width:95%;">
	<form accept-charset="utf-8" action="index.php?action=list" method="post">
	<tr><td colspan="2" style="text-align:center;background-color:#FFFFCC;"><h2 style="color:#FF9900;">Search News</h2></td></tr>
	<tr>
		<td style="text-align:right;background-color:#FFFFCC;width:40%;">Search For Text:</td>
		<td style="text-align:left;background-color:#FFFFCC;width:60%;"><input type="text" name="search_for" value="<?php hecho($posted['search_for']) ?>" /></td>
	</tr>
	<tr><td colspan="2" style="text-align:center;background-color:#FFFFCC;"><input type="submit" value="Search" /></td></tr>
	<input type="hidden" name="return_message" value="<?=$posted['return_message']?>" />
	</form>
</table>
