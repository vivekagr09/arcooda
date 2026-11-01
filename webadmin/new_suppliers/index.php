<?php
include_once dirname(__FILE__) . "/suppliers_common.inc.php";

use PHPMailer\PHPMailer\PHPMailer;

moduleLoad('phpmailer');

global $_GLOBALS;

$action = $posted['action'];
$staff_id = $_GLOBALS['staff']['id'];
$id = intval($posted['id']);

//Update Actions
switch ($action)
{
case 'copy':
case 'do_copy':
	$supplier = supplierFromDatabase($posted['id']);
	unset($supplier['id']);
	$supplier_id = supplierToDatabase($supplier);

	$action = 'edit';
	$posted['id'] = $supplier_id;
	break;

case 'do_add':
	unset($posted['id']);
	$new_added=true;
case 'do_edit':
	if (is_array($posted))
	{
		$id = $posted['id'];
		$is_new = !supplierExists($id);
		$old_supplier = supplierFromDatabase($id);
		$new_supplier = supplierFromPost($posted);
		$id = supplierToDatabase($new_supplier);

		$new_supplier['id'] = $posted['id'] = $id;

		$delta = array();

		/*
		if(isset($posted['supplier_something_affected']))
		{
			$supplier_somethings=supplierSomethingsFromPost($posted);
			supplierSomethingsToDatabase($id,$supplier_somethings);
		}
		*/

		foreach($new_supplier as $key => $value) if ($old_supplier[$key] != $new_supplier[$key] && !is_array($new_supplier[$key]) && !is_array($old_supplier[$key])) $delta[] = $key . ' changed from: ' . formatSQLString($old_supplier[$key]) . ' to: ' . formatSQLString($new_supplier[$key]);

		if ($posted['admin_note'] != '')
		{
			supplierCommentToDatabase($id, $posted['admin_note']);
			$delta[] = 'Admin Note Added: ' . formatSQLString($posted['admin_note']);
		}

		if (count($delta)) supplierCommentToDatabase($id, "Changes Made: \n" . implode("\n", $delta), 'sql');

		$action = 'edit';
		if($posted['return_message']!='') $action='select';
	}
	else
	{
		$action = 'error';
	}
	/*
	$file_orig = $_FILES['supp_image']['name'];
	$file_temp = $_FILES['supp_image']['tmp_name'];
	$error = $_FILES['supp_image']['error'];
	$size = intval($_FILES['supp_image']['size']);
	$file_ext=substr($file_orig,strrpos($file_orig,'.'));
	if(substr($file_ext,0,1)=='.') $file_ext=substr($file_ext,1);
	$dir=$_SERVER['DOCUMENT_ROOT'] .'/webadmin/assets/suppliers/';
	$thumb_dir=$dir.'thumbnails/';
	$filename='image_'.$posted['id'].'.'.$file_ext;
	if($size && !$error)
	{
		if(move_uploaded_file($file_temp,$dir.$filename))
		{
			$DB->query("UPDATE `highwaygroup`.suppliers SET image=".formatSQLString($filename).", fileorig=". formatSQLString($file_orig) .", filetype=".formatSQLString($file_ext).", size=$size WHERE id=".$posted['id']);

			supplierChangesToDatabase(array('supplier_id'=>$posted['id'], 'user'=>$_GLOBALS['staff']['user'], 'query'=>'New Manufacturer Image Uploaded'));
		}
	}
	$file_orig = $_FILES['supp_thmb']['name'];
	$file_temp = $_FILES['supp_thmb']['tmp_name'];
	$error = $_FILES['supp_thmb']['error'];
	$size = intval($_FILES['supp_thmb']['size']);
	$file_ext=substr($file_orig,strrpos($file_orig,'.'));
	if(substr($file_ext,0,1)=='.') $file_ext=substr($file_ext,1);
	if($size && !$error)
	{
		supplierChangesToDatabase(array('supplier_id'=>$posted['id'], 'user'=>$_GLOBALS['staff']['user'], 'query'=>'New Manufacturer Thumbnail Uploaded'));
	}
	if($posted['site_id']==1)
	{
		$update_list=updateSearch(array('link_id'=>$posted['id'],'section'=>'manufacturer', 'day'=>date('d'), 'month'=>date('m'), 'year'=>date('Y'),'active'=>$posted['supplier']['active']));
		if(!count($update_list)) updateToDatabase(array('section'=>'manufacturer', 'action'=>'update', 'title'=>'Manufacturer Details Updated', 'changes'=>'Manufacturer Details have been updated in the database', 'active'=>$posted['supplier']['active'], 'link_id'=>$posted['id']));
	}
	if(is_array($posted['emailTo']))
	{
		$cc_list=$bcc_list=$email_list=array();
		foreach($posted['emailTo'] as $key=>$on)
		{
			$email=$posted['email_manufacturer'][$key];
			if($email!='')
			{
				if($posted['email_flag'][$key]=='cc') $cc_list[]=$email;
				elseif($posted['email_flag'][$key]=='bcc') $bcc_list[]=$email;
			}
		}
		$email_list[]=array_shift($cc_list);
		if(!count($email_list)) $email_list[]=array_shift($bcc_list);
		if(count($email_list))
		{
			$email=new PHPMailer();
			$email->From='sales@arcooda.com';
			$email->FromName='Highway Games';
			$email->Mailer="sendmail";
			$email->CharSet = "utf-8";
			$email->Subject=$posted['subject'];
			$email->Body=nl2br($posted['message']);
			$email->AltBody=strip_tags($posted['message']);
			$address_list=array();
			foreach($email_list as $mail)	{ $address_list[]=$mail; $email->AddAddress($mail); }
			foreach($cc_list as $mail)		{ $address_list[]=$mail; $email->AddCC($mail); }
			foreach($bcc_list as $mail)		{ $address_list[]=$mail; $email->AddBCC($mail); }
			if($email->Send()) supplierChangesToDatabase(array('supplier_id'=>$posted['id'], 'type'=>'email', 'sentto'=>htmlspecialchars(implode(', ',$address_list)), 'title'=>htmlspecialchars($posted['subject']), 'user'=>$_GLOBALS['staff']['user'], 'query'=>htmlspecialchars($posted['message'])));
		}
	}
	if($posted['email_message_staff'] != '')
	{
		$sender = getStaffName($_GLOBALS['staff']['user']);
		$s_email= getStaffEmail($_GLOBALS['staff']['user']);
		$message = $posted['email_message_staff'];
		$new_email=new PHPMailer();
		$new_email->From=$s_email;
		$new_email->FromName=$sender;
		$new_email->Mailer="sendmail";
		$new_email->CharSet = "utf-8";
		$new_email->Subject="RE: Highway Group Supplier #".$posted['id'].' - '.$posted['supplier']['manufacturer'];
		$addresses = array();
		foreach($posted['email_target_staff'] as $key=>$value)
		{
			if($value != '')
			{
				$name = getStaffName($value);
				$ad = getStaffEmail($value);
				$addresses[$name] = $ad;
				$new_email->AddAddress($ad, $name);
			}
		}
		$new_email->Body    = nl2br($message);
		$new_email->AltBody = strip_tags($message);
		if($new_email->Send())
			supplierChangesToDatabase(array('supplier_id'=>$posted['id'], 'user'=>$_GLOBALS['staff']['user'], 'query'=>'Email Sent to Staff: '.implode(', ',$addresses)."\n".htmlspecialchars($message)));
	}
	*/
	break;

case 'checkSupplier':
	$check = supplierFind($posted['name'],$posted['email']);
	if(!$check){
		$action = 'form_submit';
	}
	else {
		$alert = "Supplier Name or Email already exists in the database. Please check name/email.";
		$action = 'alert';
	}
	break;

case 'reset':
	resetSuppliers();
	$action = 'list';
	break;

case 'activate':
	supplierActivate($posted['id']);
	$action = 'list';
	break;

case 'deactivate':
	supplierDeactivate($posted['id']);
	$action = 'list';
	break;
	$action = 'edit';
	break;

case 'delete_contact':
	supplierDeleteContact($posted['contact_id']);
	$action = 'close_win';
}

$get_vars = $posted;
$keep_only = array_keys(supplierGetOptionList('all_fields'));
$keep_only[] = 'order_by';
$keep_only[] = 'search_for';
$keep_only[] = 'page_id';
$keep_only[] = 'order';
$keep_only[] = 'multiple';
$keep_only[] = 'return_message';
if (count($get_vars) > 0) foreach($get_vars as $key => $value) if (is_array($value) || !in_array($key, $keep_only)) unset($get_vars[$key]);

switch ($action)
{
case 'add':
	unset($get_vars['id']);
	header('Location: suppliers_add.php?' . http_build_query($get_vars));
	break;

case 'edit':
	supplierSetLock($posted['id'], $_GLOBALS['staff']['staff_id']);
	header('Location: suppliers_edit.php?' . http_build_query($get_vars));
	break;

case 'error':
	header('Location: suppliers_error.php?' . http_build_query($get_vars));
	trigger_error('Supplier Section Error');
	break;

case 'options':
	header('Location: suppliers_options.php?' . http_build_query($get_vars));
	break;

case 'search':
	header('Location: supplier_search.php?' . http_build_query($get_vars));
	break;

case 'select':
	header('Location: suppliers_select.php?' . http_build_query($get_vars));
	break;

case 'view':
	$supplier = supplierFromDatabase($posted['id']);
	header('Location: suppliers_view.php?' . http_build_query($get_vars));
	break;

case 'do_search':
case 'list':
default:
	unset($get_vars['id']);
	header('Location: suppliers_list.php?' . http_build_query($get_vars));
	break;

case 'update_last_orders':
	supplierLastOrders();
	break;
case 'comments':
	header('Location: suppliers_comments.php?'.http_build_query($get_vars));
	break;
case 'print':
	header('Location: suppliers_print.php?'.http_build_query($get_vars));
	break;
case 'view_orders':
	header('Location: suppliers_orders.php?'.http_build_query($get_vars));
	break;
}

die();
?>