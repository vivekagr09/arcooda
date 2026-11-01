<?php
include_once __DIR__ . "/common.inc.php";

$id = intval($_REQUEST["id"]);
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Admin Notes for Product ID <?php echo $id ?></title>
		<link rel="stylesheet" href="/webadmin/admin.css" />
		<style type="text/css">
body, p, td	{ font-family:Verdana,Arial,Helvetica,sans-serif; font-size:12px; }
.td_options	{ color:#fff; font-size:12px; }
a:hover	{ color:#f00; }
.top	{ border-top:1px solid #ccc; }
.left	{ border-left:1px solid #ccc; }
.right	{ border-right:1px solid #ccc; }
.centre	{ border-top:1px solid #ccc; border-bottom:1px solid #ccc; }
.bottom	{ border-bottom:1px solid #ccc; }
.bottomLeft	{ border-bottom:1px solid #ccc; border-left:1px solid #ccc; }
.bottomRight	{ border-right:1px solid #ccc; border-bottom:1px solid #ccc; }
.bottomLeftRight	{ border-right:1px solid #ccc; border-bottom:1px solid #ccc; border-left:1px solid #ccc; }
.box	{ border:1px solid #ccc; }
tr.even	{ background:#fff; }
tr.odd	{ background:#eee; }

		</style>
	</head>
	<body>

<table width="500" border="0" cellspacing="0" cellpadding="2" align="center">
	<tr><td colspan="3" align="center" class="box"><strong>Admin Notes</strong></td></tr>
	<tr style="background:#E3EBF7;">
		<td width="10%" align="center" nowrap="nowrap" class="bottomLeft"><strong>Date Added</strong></td>
		<td width="5%" align="center" nowrap class="bottomLeft"><strong>User</strong></td>
		<td width="85%" align="center" class="bottomLeftRight"><strong>Notes</strong></td>
	</tr>
<?php
$alt = true;
$query = $DB->query("SELECT *, UNIX_TIMESTAMP(`date_added`) AS `date_added` FROM `product_admin_notes` WHERE `product_id` = " . intval($id) . " ORDER BY `date_added` DESC");
while($row = $DB->fetch_assoc($query))
{
?>
	<tr class="<?php echo ($alt = !$alt) ? "odd" : "even" ?>">
		<td class="bottomLeft" valign="top" nowrap="nowrap" align="center"><?php echo date('d M, Y', $row['date_added']) ?></td>
		<td class="bottomLeft" valign="top" nowrap="nowrap" align="center"><?php echo $row['user'] ?>&nbsp;</td>
		<td class="bottomLeftRight"><?php echo nl2br(hecho($row['note'], 1)) ?></td>
	</tr>
<?php
}
$DB->free_result($query);
?>
</table>
<br />
<div style="text-align:center;"><strong>Please use the Close button to return to the Product Edit page.</strong></div>

	</body>
</html>
