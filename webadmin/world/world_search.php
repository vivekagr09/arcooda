<?php
if(!isset($inc))
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
						<div align="center"><strong><font color="#FF6600" size="4">World Search</font></strong></div>
					</td>
				</tr>
				<tr>
					<td bgcolor="#FFFFCC"><div align="center">Enter a <b>country</b>, <b>city</b> or <b>port</b> name</div></td>
				</tr>
				<tr>
					<td bgcolor="#FFFFCC">
						<div align="center">
							<input type="text" name="value" size="30" value="<?php echo $_REQUEST['value']?>">
							<input type="submit" value="Search">
						</div>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
</form>

<?php
if(!isset($inc))
	include_once($_SERVER['DOCUMENT_ROOT'].'/webadmin/footer.inc.php');
?>