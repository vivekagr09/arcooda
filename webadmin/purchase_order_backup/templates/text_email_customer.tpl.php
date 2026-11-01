<?php
//$mail_to        = List of email addresses
//$message        = Message From Staff To Customer
//$invoice_id    = Invoice ID
//$customer_id    = Member ID Who Message Is Being Sent To
//$rand_num        = The Random Security Number
$image_url = "https://" . $_SERVER['SERVER_NAME'] . "/messages/viewimage.php&#63;invoice_id={$invoice_id}&amp;customer_id={$customer_id}&amp;rand_num={$rand_num}&amp;image_id=";
?>
<?=stripslashes($message)?>

<?php
if (count($attachment_list) > 0) {
?>
--------------------------------------------------------------------

Attachments:
<?php
    foreach ($attachment_list as $key => $item) {
?>
<?=$item?>:
<?=$image_url . $key?>

<?php
    } //foreach($attach_list as $key=>$item)
} //if(count($attach_list)>0)
?>

--------------------------------------------------------------------

To reply to this email please click the following link:
https://<?=$_SERVER['SERVER_NAME']?>/messages/index.php?invoice_id=<?=$invoice_id?>&customer_id=<?=$customer_id?>&rand_num=<?=$rand_num?>


--------------------------------------------------------------------

PLEASE NOTE: If you reply to this email via regular email, your
message will be sent to a general mailbox which may delay your
invoice.
