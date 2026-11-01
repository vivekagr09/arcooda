<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/webadmin/header.inc.php');
?>

<form action="/webadmin/world/report.php" method="get">
<table width="95%" border="0" align="center" cellpadding="1" cellspacing="0">
	<tr>
		<td bgcolor="#FF9900">
			<table width="100%" border="0" align="center" cellpadding="5" cellspacing="0">
				<tr>
					<td bgcolor="#FFFFCC">
						<input type="hidden" name="what" value="search" />
						<div align="center"><strong><font color="#FF6600" size="4">Add/Edit Information</font></strong></div>
					</td>
				</tr>
				<tr>
					<td bgcolor="#FFFFCC"><div align="center">Enter the text to be added in the World Summary section here. Do not forget to mention the country</div></td>
				</tr>
				<tr>
					<td bgcolor="#FFFFCC">
						<div align="center">
							<textarea rows=15 cols=25 name="value" style="width:600px;"></textarea>
						</div>
						<div align="center"><br>
							If you have any attachments you would like to add, please email them to <a href="mailto:molly@coinopexpress.com">molly@coinopexpress.com</a><br>
							<br><input type="submit" value="Send">
						</div>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
</form>

<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/webadmin/footer.inc.php');
?>