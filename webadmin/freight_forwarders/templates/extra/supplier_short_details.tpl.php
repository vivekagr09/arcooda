<html>
	<head>
		<link rel="stylesheet" href="/webadmin/admin.css" type="text/css" />
		<link rel="stylesheet" href="style.css" type="text/css" />
		<script type="text/javascript">
<!--
function loadToParent()
{
	if (window == parent) return;
	parent.displayExternal();
}

//-->
		</script>
	</head>

	<body onload="javascript: loadToParent()" >
		<div id="sposcontent">
			<table>
				<tr>
					<td class="left_title">Supplier Name:</td>
					<td><?php echo $s['supplier_name'] ?></td>
				</tr>
				<tr>
					<td class="left_title">Supplier Address:</td>
					<td><?php echo (trim($s['address']))?nl2br(trim($s['address'])):"Not Specified"; ?></td>
				</tr>
			</table>
		</div>
	</body>
</html>
