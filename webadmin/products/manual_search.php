<?php
if(!isset($inc))
	include_once($_SERVER['DOCUMENT_ROOT'].'/webadmin/header.inc.php');
?>
<br /><br />
<form action="" method="get">
<table width="95%" border="0" align="center" cellpadding="1" cellspacing="0">
	<tr>
		<td bgcolor="#FF9900">
			<table width="100%" border="0" align="center" cellpadding="5" cellspacing="0">
				<tr>
					<td bgcolor="#FFFFCC">
						<input type="hidden" name="action" value="do_search" />
						<div align="center"><strong><font color="#FF6600" size="4">Manuals Search</font></strong></div>
					</td>
				</tr>
				<tr>
					<td bgcolor="#FFFFCC"><div align="center">Enter a <b>Product ID</b> or a <b>Product Name</b></div></td>
				</tr>
				<tr>
					<td bgcolor="#FFFFCC">
						<div align="center">
							<input type="text" name="query" size="30" value="<?php echo $_REQUEST['query']?>">
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