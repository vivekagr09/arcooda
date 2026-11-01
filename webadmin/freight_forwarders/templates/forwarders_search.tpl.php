<table align="center" style="width:95%;">
	<form action="index.php?action=list&type=<?php echo $posted['type'] ?>" method="post">
	<tr><td style="text-align:right;">
		Search:
		<input type="text" style="width:256px;" name="search_for" value="<?php hecho($search_for) ?>" />
		<input type="submit" value="Go" />
	</td></tr>
	</form>
</table>
