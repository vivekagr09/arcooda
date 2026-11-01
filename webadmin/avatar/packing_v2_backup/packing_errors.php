<?php
include_once('packing_common.inc.php');

use PHPMailer\PHPMailer\PHPMailer;

moduleLoad('phpmailer');
moduleLoad('product'); //productFromDatabase

$posted = array_merge($_GET, $_POST);

$toobig_list=array();
$nosize_list=array();
$badsize_list=array();

$staff_list = getStaffList();
$staff_id   = $_GLOBALS['staff']['id'];
$staff_name = $staff_list[$staff_id];

$error_list=$posted['error_list'];
if(substr($error_list,0,11)=='b64_gz_ser:')
	$error_list=unserialize(gzuncompress(base64_decode(substr($error_list,11))));

$products_list=array();
if(is_array($error_list))
	foreach($error_list as $error)
	{
		$errno=$error[0];
		$product_id=$error[3];
		$error_text=$error[1];
		$products_list[]=$product_id;
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
$products_list=array_unique($products_list);

if($action=='do_send')
{
	$staff_from=staffFromDatabase($staff_id);
	$staff_to=staffFromDatabase($message_to);

	$mail=new PHPMailer();
	$mail->Body=$posted['message'];
	$mail->IsHTML(false);
	$mail->From=$staff['email'];
	$mail->FromName=$staff_name;
	$mail->CharSet = "utf-8";
	$mail->Subject='Packing Dimensions Request';
	$mail->AddAddress($staff_to['email']);
	$result=$mail->Send();

	if($result)
		echo 'Email sent to '.htmlspecialchars($staff_list[$message_to]);
	elseif(!$email_present && $message_to!='')
		echo 'Email not sent, as no Email address found for the selected user in the database.';
	elseif($message_to=='')
		echo 'Email not sent, as no user selected.';
	else
		echo 'Email not sent. Email address = '.htmlspecialchars($staff_list[$message_to]);
?>
<br />
<br />
<input type="button" value="Close Window" onclick="window.close();" />
<?php
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
<form accept-charset="utf-8" action="packing_errors.php?action=do_send" method="post" style="width:100%;text-align:center;">
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
		echo 'https://'.$_SERVER['HTTP_HOST'].'/webadmin/products/?action=edit&product_id='.$product_id."\n";
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
		echo 'https://'.$_SERVER['HTTP_HOST'].'/webadmin/products/?action=edit&product_id='.$product_id."\n";
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
		echo 'https://'.$_SERVER['HTTP_HOST'].'/webadmin/products/?action=edit&product_id='.$product_id."\n";
	}
}
?>


https://<?= $_SERVER['HTTP_HOST'] ?>/webadmin/products/?subset_list=<?= urlencode(implode("|",$products_list)) ?>

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