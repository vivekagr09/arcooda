<?php
include_once('../header.inc.php');
?>
<br />
<?php
include_once('header.inc.php');
?>
<br />
<form accept-charset="utf-8" action="sender.php?action2=send&resume=yes&testonly=yes" method="post">
<blockquote>
	<p>Please enter a name and email address to send a test to</p>
	<table width="0">
		<tr>
			<td>Name :</td>
			<td><input type="text" name="test_name" value="" style="width:256px;" /></td>
		</tr>
		<tr>
			<td>Email :</td>
			<td><input type="text" name="test_email" value="" style="width:256px;" /></td>
		</tr>
	</table>
	<p><input type="submit" /></p>
</blockquote>
</form>
<?php
include_once('../footer.inc.php');
?>