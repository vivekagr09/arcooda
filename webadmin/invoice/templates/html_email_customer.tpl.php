<?php
//$mail_to        = List of email addresses
//$message        = Message From Staff To Customer
//$invoice_id    = Invoice ID
//$customer_id    = Member ID Who Message Is Being Sent To
//$rand_num        = The Random Security Number
$image_url = "http://" . $_SERVER['SERVER_NAME'] . "/messages/viewimage.php&#63;invoice_id={$invoice_id}&amp;customer_id={$customer_id}&amp;rand_num={$rand_num}&amp;image_id=";
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8" />
	</head>
	<body>

<p>
<?=nl2br(htmlspecialchars($message))?>
</p>
<?php
if (count($attachment_list) > 0) {
?>
<hr />
Attachments:<br />
<table>
	<tr>
<?php
foreach ($attachment_list as $key => $item) {
?>
		<td style="text-align:center;">
			<a href="<?=$image_url?><?=$key?>" target="_blank">
				<img src="<?=$image_url?><?=$key?>&amp;thumb=true" /><br />
				<?=$item?>
			</a>
		</td>
<?php
}
?>
	</tr>
</table>
<?php
}
?>
<hr />
<p style="background-color:yellow;">
To reply to this email please click <a
href="http://<?=$_SERVER['SERVER_NAME']?>/messages/.&#63;invoice_id=<?=$invoice_id?>&amp;customer_id=<?=$customer_id?>&amp;rand_num=<?=$rand_num?>"
>here</a>
</p>
<hr />
<p>PLEASE NOTE: If you reply to this email via regular email, your
message will be sent to a general mailbox which may delay your
invoice.</p>
</body></html>
