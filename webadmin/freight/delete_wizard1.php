<?php
include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/header.inc.php';

include 'freight_wizard_functions.php';
?>
 <link rel="stylesheet" href="/webadmin/admin.css" type="text/css">
<META HTTP-EQUIV="refresh">
<br>
<table width="95%"  align="center" cellpadding='5' cellpadding='1'>
	<tr class="pageMenu">
		<td><a href="AdminMenu.php">Delete a Service</a></td><td align='left' width="312px"><a href="AdminMenu.php">Back</a></td>
	</tr>
</table>
<form accept-charset="utf-8" autocomplete="off" method="post" action="delete_wizard2.php">
<script LANGUAGE="JavaScript">
	<!--

	function confirmSubmit()
	{
		var agree=confirm("Are you sure you wish to Delete?");
		if (agree)
		{
			return true ;
		}
		else
		{
			return false ;
		}
	}
	// -->
</script>
	<table align='center'>
		<tr>
			<td class="left">
				Service Name:
			</td>
			<td class="right">
					<select name="courier" style="width:190px">
<?php
$result = $DB->query("SELECT * FROM shipping_services ORDER BY name ASC");
while ($row = $DB->fetch_array($result)) {
    echo '<option value="';
    echo $row[prop_id];
    echo '">';
    echo $row[name];
    echo "	-	";
    $source         = $row[source];
    $source_country = $DB->query("SELECT * FROM shipping_location WHERE code = '$source';");
    $row2           = $DB->fetch_array($source_country);
    $source_country = $row2[name];
    echo $source_country;
    echo "</option>";
}
?>
			</td>
		</tr>
	<tr>
		<td colspan='2' align='center'>
			<input type="button" value="Go back" onClick="window.location = 'AdminMenu.php';"></input>
			<input type="submit" name="Submit" value="Delete" onClick="return confirmSubmit()"></input>
		</td>
	</tr>
	</table>

	</form>