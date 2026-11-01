<?php
include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/header.inc.php';
?>
 <link rel="stylesheet" href="/webadmin/admin.css" type="text/css">
<script src="tooltip.js" type="text/javascript"></script>
<table width="95%"  align="center" cellpadding='5' cellpadding='1'>
	<tr class="pageMenu">
		<td><a href="AdminMenu.php">Generate Weights</a></td><td align='left' width="312px"><a href="AdminMenu.php">Back</a></td>
	</tr>
</table>
<form accept-charset="utf-8" action="generate2.php?prop_id=<?=$prop_id?>" method="POST">
	<table align="center">
		<tr>
			<td class='left'>
				Number of weights:
			</td>
			<td class='right'>
				<input type="text" style="width:30px" name="number" tooltip=""></input>
			</td>
		</tr>
		<tr>
			<td class='left'>
				Starting at:
			</td>
			<td class='right'>
				<input type="text" style="width:30px" name="starting" tooltip=""></input>kg
			</td>
		</tr>
		<tr>
			<td class='left'>
				Increment by:
			</td>
			<td class='right'>
				<input type="text" style="width:30px" name="incrementby" tooltip=""></input>kg
			</td>
		</tr>
		<tr>
			<td align='center' colspan='2'>
				<input type="submit" name="Submit" value="Next" tooltip=''>
			</td>
		</tr>
	</table>
</form>