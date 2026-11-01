<?php
include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/header.inc.php';

include 'freight_wizard_functions.php';
?>
<table width="95%"  align="center" cellpadding='5' cellpadding='1'>
	<tr class="pageMenu">
		<td><a href="AdminMenu.php">Delete a Service</a></td><td align='left' width="312px"><a href="AdminMenu.php">Back</a></td>
	</tr>
</table>
<?php
deleteService();
echo "<center>Deletion Succesfull</center>";
?>