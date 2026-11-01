<?php

include_once __DIR__ . '/common.inc.php';
?><html>
<head>
<title>Emails</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<style type="text/css">
<!--
body,p,td {  font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 12px}
.td_options {  color: #FFFFFF; font-size: 12px}
a:hover {  color: #FF0000}
-->
</style>
<script language="JavaScript">
<!--
function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}
//-->
</script>
</head>
<body>
<form action="product_notify_view_emails.php" method="post">
<div align="center"><input type="button" name="Button" value="Close" onClick="window.close()">
<?php if ('yes' != $sent) {?>
<input type="submit" name="delete" value="Delete Selected Emails">
<?php }?>
</div>
<br>
<?php
$id = intval($id);

if (is_array($email) && count($email) > 0) {
    $deletion = '""';
    for ($i = 0; $i < count($email); $i++) {
        $key = key($email);
        $deletion .= ", '$key'";
        next($email);
    }

    $statement = "DELETE FROM product_notify_email WHERE product_id = '$id' AND email IN ($deletion)";
    $DB->query($statement);
}
if ('yes' != $sent) {
    $statement = "SELECT * FROM product_notify_email WHERE product_id = '$id' AND  notified = 'n'";
} else {
    $statement = "SELECT * FROM product_notify_email WHERE product_id = '$id' AND  notified = 'y'";
}

$result = $DB->query($statement);

?>

<table align="center" cellspacing="1" cellpadding="2" align="center">
<tr bgcolor="#000066">
	<td align="center"><font color="white"><b>Email Address</b></font></td>
	<?php if ('yes' != $sent) {?>
	<td align="center"><font color="white"><b>Delete?</b></font></td>
	<?php }?>
</tr>
<?php
$alt_color = true;
while ($row = $DB->fetch_array($result)) {
?>
<tr bgcolor="<?=($alt_color = !$alt_color) ? '#EEEEEE' : '#FFFFFF';?>">
	<td><?=$row['email'];?></td>
	<?php if ('yes' != $sent) {?>
	<td align="center"><input type="checkbox" name="email[<?=$row['email'];?>]" value="1"></td>
	<?php }?>
</tr>
<?php
}
?>
</table><br>
<div align="center"><input type="button" name="Button" value="Close" onClick="window.close()">
<?php if ('yes' != $sent) {?>
<input type="submit" name="delete" value="Delete Selected Emails">
<?php }?>
<input type="hidden" name="id" value="<?=$id;?>">
</div>
</form>
</body>
</html>