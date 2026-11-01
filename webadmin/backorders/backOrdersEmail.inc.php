<form accept-charset="utf-8" autocomplete="off" method="post" action="backOrdersEmailWindow.php" target="_blank">
<table align="center" border="1" cellspacing="1" cellpadding="3">
	<tr bgcolor="#E3EBF7">
		<td colspan="2" align="center"><b>Email Message</b></td>
	</tr>

	<tr>
		<td colspan=2>
			<textarea name="extramessage" style="width:500px;height:300px"></textarea>
		</td>
	</tr>
	<tr>
		<td align="center"><input type="submit" value="Email To : "></input>
			<select name="to">
<?php
				$result = $DB->query("SELECT `user`, `email` FROM `highwaygroup`.`users` ORDER BY `user`");
				while($row = $DB->fetch_array($result))
				{
					echo '<option value="'.$row['user'].'">'.niceusername($row['user']).'</option>';
				}
?>
			</select>

		</td>
	</tr>
	<input type="hidden" name="id" value="<?= $id?>"></input>
</table>
</form>