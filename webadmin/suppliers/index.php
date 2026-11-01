<?php
include_once dirname(__FILE__) . "/suppliers_common.inc.php";

use PHPMailer\PHPMailer\PHPMailer;

global $_GLOBALS;

moduleLoad('phpmailer');

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
	unset($posted['supplier']['id']);
	$new_added=true;
case 'do_edit':
	if (is_array($posted['supplier']))
	{
		$id = $posted['supplier']['id'];
		$is_new = !supplierExists($id);
		$old_supplier = supplierFromDatabase($id);
		$new_supplier = supplierFromPost($posted['supplier']);
		$id = supplierToDatabase($new_supplier);

		$new_supplier['id'] = $posted['id'] = $id;

		$files=array('image'=>'', 'thumb'=>'');
		foreach($files as $key=>$name) if(!$_FILES['supplier_'.$key]['error'] && $_FILES['supplier_'.$key]['size'])
		{
			$file_ext = strtolower(end(explode('.', $_FILES['supplier_'.$key]['name'])));
			$filename=(($name=='')?$key:$name).'_'.$id.'.'.$file_ext;
			$path=supplierGetOptionList($key.'_path');
			if (move_uploaded_file($_FILES['supplier_'.$key]['tmp_name'], $path.'/'.$filename))
			{
				$new_supplier[$key.'_file']=$filename;
				$new_supplier[$key.'_orig']=$_FILES['supplier_'.$key]['name'];
				$new_supplier['staff_id'] = $_GLOBALS['staff']['staff_id'];
				supplierToDatabase($new_supplier);
			}
		}

		if(function_exists('updateToDatabase'))
		{
			$update_title='Supplier Details Updated';
			$update_msg='Supplier Details have been updated in the database.';
			$update_action=($new_added)?'add':'update';
			if($new_added)
			{
				$update_title='New Supplier Added';
				$update_msg='New Supplier has been added into the database';
			}
			$update_list=updateSearch(array('link_id'=>$id,'section'=>'supplier', 'day'=>date('d'), 'month'=>date('m'), 'year'=>date('Y'),'active'=>$new_supplier['active']));
			if(!count($update_list)) updateToDatabase(array('section'=>'supplier', 'action'=>$update_action, 'title'=>$update_title, 'changes'=>$update_msg, 'active'=>$new_supplier['active'],'link_id'=>$id));
		}
		$delta = array();
		foreach($new_supplier as $key => $value) if ($old_supplier[$key] != $new_supplier[$key] && !is_array($new_supplier[$key]) && !is_array($old_supplier[$key])) $delta[] = $key . ' changed from: ' . formatSQLString($old_supplier[$key]) . ' to: ' . formatSQLString($new_supplier[$key]);

		if ($posted['admin_note'] != '')
		{
			supplierCommentToDatabase($id, $posted['admin_note']);
			$delta[] = 'Admin Note added: ' . formatSQLString($posted['admin_note']);
		}

		if (count($delta)) supplierCommentToDatabase($id, "Changes Made: \n" . implode("\n", $delta), 'sql');

		if (is_array($posted['emailTo']))
		{
			$cc_list = $bcc_list = $email_list = array();
			foreach($posted['emailTo'] as $key => $on)
			{
				$email = $posted['email_manufacturer'][$key];
				if ($email != '')
				{
					if ($posted['email_flag'][$key] == 'cc') $cc_list[] = $email;
					elseif ($posted['email_flag'][$key] == 'bcc') $bcc_list[] = $email;
				}
			}
			$email_list[] = array_shift($cc_list);
			if (!count($email_list)) $email_list[] = array_shift($bcc_list);
			if (count($email_list))
			{
				$email = new PHPMailer();
				$email->From = 'sales@arcooda.com';
				$email->FromName = 'Highway Games';
				$email->Mailer = "sendmail";
				$email->CharSet = "utf-8";
				$email->Subject = $posted['subject'];
				$email->Body = nl2br($posted['message']);
				$email->AltBody = strip_tags($posted['message']);
				$address_list = array();
				foreach($email_list as $mail)
				{
					$address_list[] = $mail;
					$email->AddAddress($mail);
				}
				foreach($cc_list as $mail)
				{
					$address_list[] = $mail;
					$email->AddCC($mail);
				}
				foreach($bcc_list as $mail)
				{
					$address_list[] = $mail;
					$email->AddBCC($mail);
				}
				if ($email->Send()) {
					supplierChangesToDatabase(array(
						'supplier_id' => $posted['supplier_id'],
						'type'        => 'email',
						'sentto'      => htmlspecialchars(implode(', ', $address_list)),
						'title'       => htmlspecialchars($posted['subject']),
						'user'        => $_GLOBALS['staff']['user'],
						'query'       => htmlspecialchars($posted['message']),
					));
				}
			}
		}

		if($posted['email_message_staff'] != '')
		{
			$new_email=new PHPMailer();
			$new_email->Sender=$_GLOBALS['staff']['email'];
			$new_email->From=$_GLOBALS['staff']['email'];
			$new_email->FromName=trim($_GLOBALS['staff']['first_name'].' '.$_GLOBALS['staff']['last_name']);
			$new_email->Mailer="sendmail";
			$new_email->CharSet = "utf-8";
			$new_email->Subject="RE: ".$_GLOBALS['site_settings']['site_name']." Supplier #".$id;
			$new_email->Body=nl2br($posted['email_message_staff']);
			$new_email->AltBody=strip_tags($posted['email_message_staff']);
			$new_email->isHTML(true);
			$addresses=array();
			foreach($posted['email_target_staff'] as $staff_id) if($staff_id!='')
			{
				$staff=staffFromDatabase($staff_id);
				$addresses[$name]=$staff['email'];
				$new_email->AddAddress($staff['email'],trim($staff['first_name'].' '.$staff['last_name']));
			}
			if($new_email->Send()) supplierCommentToDatabase($id,'Email Sent to Staff: '.implode(', ',$addresses)."\n".htmlspecialchars($message),'email');
		}

		$action = 'edit';
		if($posted['return_message']!='') $action='select';
	}
	else
	{
		$action = 'error';
	}
	break;

case 'activate':
case 'do_activate':
	$supplier_id = intval($posted['id']);
	$active_id=explode(',',supplierGetValue($supplier_id,'active_id'));
	$active_id[]=$_GLOBALS['site_settings']['site_id'];
	supplierSetValue($supplier_id,'active_id',implode(',',$active_id));
	$action = 'list';
	break;

case 'deactivate':
case 'do_deactivate':
	$supplier_id = intval($posted['id']);
	$active_id=explode(',',supplierGetValue($supplier_id,'active_id'));
	if(in_array($_GLOBALS['site_settings']['site_id'],$active_id))
		unset($active_id[array_search($_GLOBALS['site_settings']['site_id'],$active_id)]);
	supplierSetValue($supplier_id,'active_id',implode(',',$active_id));
	$action = 'list';
	break;

case 'do_toggle':
	$supplier_id = intval($posted['id']);
	$active_id=explode(',',supplierGetValue($supplier_id,'active_id'));
	if(in_array($_GLOBALS['site_settings']['site_id'],$active_id))
		unset($active_id[array_search($_GLOBALS['site_settings']['site_id'],$active_id)]);
	else
		$active_id[]=$_GLOBALS['site_settings']['site_id'];
	supplierSetValue($supplier_id,'active_id',implode(',',$active_id));
	$action = 'list';
	break;
case 'unlock':
	supplierSetLock($posted['id']);
	break;
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

//Redirect Actions
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
	header('Location: suppliers_search.php?' . http_build_query($get_vars));
	break;

case 'select':
	header('Location: suppliers_select.php?' . http_build_query($get_vars));
	break;

case 'view':
	$supplier = supplierFromDatabase($posted['id']);
	header('Location: ' . $supplier['supplier_url'] . '?preview=true');
	break;

case 'delete':
	header('Location: suppliers_delete.php?' . http_build_query($get_vars));
	break;

default:
case 'list':
	header('Location: suppliers_list.php?' . http_build_query($get_vars));
	break;
}

die();
?>