<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<link href="style.css" rel="stylesheet" type="text/css" />
<script language="javascript">
<!--
	function pushToOpener(id,name)
	{
		if(opener)
		{
			opener.selectSupplier(id,name);
			window.close();
		}
	}
//-->
</script>
</head>

<body>
<div id="sposcontent" style="text-align: center">
<h3>Search Results</h3>
<br />
<?php
if(!count($results)): ?>
No Results.<br /><input type="button" value="Close Window" onclick="javascript: window.close();" />
<?php
else: ?>
	<table class="wide">
		<tr>
			<th>ID</th>
			<th>Supplier Name </th>
			<th>Country</th>
			<th>Supply Types </th>
			<th>&nbsp;</th>
		</tr>
<?php
foreach($results as $s): ?>
		<tr<?= (!$tr_alt = !$tr_alt) ? " class=\"alt\"" : ""; ?>>
			<td><?= trim($s['supplier_id']); ?></td>
			<td><?= trim($s['supplier_name']); ?></td>
			<td><?= trim($s['country_name']); ?></td>
			<td><?= trim($s['supps']); ?></td>
			<td class="collapse">
				<input type="button" value="OK" onclick="javascript:pushToOpener(<?= $s['supplier_id']; ?>,'<?= $s['supplier_name']; ?>');" />
			</td>
		</tr>
<?php
endforeach; ?>
	</table>
<?php
endif; ?>
</div>
</body>
</html>
