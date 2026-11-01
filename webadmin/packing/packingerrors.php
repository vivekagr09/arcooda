<?php
include_once('packing_common.php');

use PHPMailer\PHPMailer\PHPMailer;

moduleLoad('phpmailer');
moduleLoad('product'); //productFromDatabase

global $_GLOBALS;

$posted = array_merge($_GET, $_POST);

$toobig_list=array();
$nosize_list=array();
$badsize_list=array();
$staff_list=getStaffList();
$staff_name = $_GLOBALS['staff']['name'];

foreach(explode('_',$posted['error_list']) as $key=>$error)
{
	$error=hexdec($error);
	$errno=substr($error,0,1);
	$product_id=substr($error,1);
	switch($errno)
	{
	case 3:
		$nosize_list[]=$product_id;
	case 5:
		$badsize_list[]=$product_id;
	case 4:
	case 6:
		$toobig_list[]=$product_id;
	}
}

if($action=='do_send')
{
	$mail=new PHPMailer();
	$mail->Body=$posted['message'];
	$mail->IsHTML(false);
	$mail->From="no-reply@highwaygroup.net";
	$mail->CharSet = "utf-8";
	$mail->Sender=$mail->From;
	$mail->FromName=$staff_name;
	$mail->Subject='Packing Dimensions Request';
	if($message_to != '')
		$row = $DB->query_first("SELECT `email` FROM `highwaygroup`.`users` WHERE `id` = '$message_to'");
	$email_present = false;
	if($row && $row['email'] != ''){
		$mail->AddAddress($row['email']);
		$email_present = true;
	}
	$result=false;
	if($config['live'] && $email_present) $result=$mail->Send();

	if($result)
		echo 'Email sent to '.$row['email'];
	else if(!$email_present && $message_to != '')
		echo 'Email not sent, as no Email address found for the selected user in the database.';
	else if($message_to == '')
		echo 'Email not sent, as no user selected.';
	else
		echo 'Email not sent. Email address = '.$row['email'];

	die();
}

$product_list=$posted['product_nosize'];
$other_error=$posted['product_other'];
?>
<html>
	<head><title>Packing Dimensions Request Form</title></head>
	<body style="width:100%;height:100%;padding:0px 0px 0px 0px;margin:0px 0px 0px 0px;">
		<div style="width:100%;height:100%;text-align:center;padding:0px 0px 0px 0px;margin:0px 0px 0px 0px;">

<h1>Packing Dimensions Request Form</h1>
<p>Please enter your requests below</p>
<table style="width:95%;" align="center">
<form action="packingerrors.php?action=do_send" method="post" style="width:100%;text-align:center;">
	<tr><td style="text-align:center;">
		<textarea name="message" style="width:100%;" rows="24">
Dear Product Manager,

I have received the following errors while trying to use the freight packing calculator
<?php
if(count($nosize_list)>0)
{
?>


No Size Information Available for:
<?php
	foreach($nosize_list as $product_id)
	{
		$product=productFromDatabase($product_id);
		echo "\n".$product_id.' - '.htmlspecialchars($product['product_name'])."\n";
		$parent = substr($product['product_type'],0,1);
		echo 'https://'.$_SERVER['HTTP_HOST'].'/webadmin/products/admin.php?table=products&parent='.$parent.'&action=edit&id='.$product_id."\n";
	}
}
if(count($badsize_list)>0)
{
?>


Invalid Size Information for:
<?php
	foreach($badsize_list as $product_id)
	{
		$product=productFromDatabase($product_id);
		echo "\n".$product_id.' - '.htmlspecialchars($product['product_name'])."\n";
		$parent = substr($product['product_type'],0,1);
		echo 'https://'.$_SERVER['HTTP_HOST'].'/webadmin/products/admin.php?table=products&parent='.$parent.'&action=edit&id='.$product_id."\n";
	}
}
if(count($toobig_list)>0)
{
?>


Product Too Big to Fit Any Container for:
<?php
	foreach($toobig_list as $product_id)
	{
		$product=productFromDatabase($product_id);
		echo "\n".$product_id.' - '.htmlspecialchars($product['product_name'])."\n";
		$parent = substr($product['product_type'],0,1);
		echo 'https://'.$_SERVER['HTTP_HOST'].'/webadmin/products/admin.php?table=products&parent='.$parent.'&action=edit&id='.$product_id."\n";
	}
}
?>


Could you please update the dimensions of these products for me

Thank You
<?= $staff_name ?>
</textarea>
<tr>
		<td align="center"><b>Send To :</b>
		<select name="message_to" style="width:75%">
			<option value=""></option>
<?php
$short_staff_list=$staff_list;
foreach($short_staff_list as $key=>$value)
	if($value=="!")
		unset($short_staff_list[$key]);
?>
			<?= formatHTMLOptions($short_staff_list) ?>
		</select></td></tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td align="center">
		<input type="submit" style="width:80%;" value="Send Request" />
	</td></tr>
</form>
</table></div></body></html>