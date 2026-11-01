<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Suppliers Search</title>
<link href="style.css" rel="stylesheet" type="text/css" />
</head>
<body>
<form method="post" action="?action=results">
<div id="sposcontent">
	<table>
		<tr>
			<td colspan="2"><h3 style="text-align: center">Suppliers Search Form</h3></td>
		</tr>
		<tr>
			<td class="left_title">Supplier Name: </td>
			<td><input name="name" type="text" class="intext" /></td>
		</tr>
		<tr>
			<td class="left_title">Supplier Type:</td>
			<td><select name="type">
				<option value="0">Any Types</option>
				<option value="1">Full Machines</option>
				<option value="2">PCB's and Kits</option>
				<option value="3">Spare Parts</option>
				<option value="4">Merchandise</option>
				<option value="5">Services</option>
			</select></td>
		</tr>
		<tr>
			<td class="left_title">Country:</td>
			<td><select name="country">
				<option value="">Any Country</option>
<?php
foreach($countries as $c): ?>
				<option value="<?php echo $c['code']; ?>"><?php echo $c['name']; ?></option>
<?php
endforeach; ?>
			</select></td>
		</tr>
		<tr style="text-align: center">
			<td colspan="2"><input name="Submit" type="submit" value="Submit" /></td>
		</tr>
	</table>
</div>
</form>
</body>
</html>
