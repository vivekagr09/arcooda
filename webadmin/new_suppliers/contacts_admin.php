<?php
/***********************
 * Random File Description
 * Filename: contact_admin.php
 ***********************
 * Purpose:
 *	NONE
 * Configuration Variables used:
 *	NONE
 * Functions defined:
 *	NONE
 ***********************
 * TODO:
 *	Locking!
 ***********************/
include_once __DIR__ . '/common.inc.php';

moduleLoad('template');

require_once("spo_config.php");

require_once("functions/lock.inc.php");			// DB Locking (requires DB)
require_once("functions/menu.inc.php");			// Menu updater (requires DB)
require_once("functions/supplier_contact.inc.php");	// Supplier Magic (Requires DB)

// Handle request for action
switch($_REQUEST['action'])
{
	case "add":
		$content = addNewContact();
		break;
	case "edit":
		$content = editContact();
		break;
	case "doAdd":
		$content = doAddNewContact();
		break;
	case "doEdit":
		$content = doEditContact();
		break;
	default:
		//$content = listItems();
		break;
}

if($_REQUEST['pushback'])
{
	$page = new Template($config['template_path']);
	$page->set("contacts", listContacts($_REQUEST['supplier_id']));
	echo $page->fetch("contact_pushback.tpl.php");
}
elseif($_REQUEST['popup'])
{
	$page = new Template($config['template_path']);
	$page->set("popup_content", $content['content']);
	echo $page->fetch("popup.tpl.php");
}
else
{
	$content['counts'] = menuCounters($config['db_prefix']);
	outputHTML($content, $msg, $config['template_path']);
}


function addNewContact()
{
	global $config;

	$page = new Template($config['template_path']);
	//$page->set("edit", $edit);
	$page->set("supplier", $_REQUEST['supplier_id']);
	$page->set("suppliers", listSuppliers());
	$page->set("popup", $_REQUEST['popup']);
	$ret['content'] = $page->fetch("edit_contacts.tpl.php");
	$ret['msg'] = "";
	$ret['header'] = "Edit Contact Details";
	$ret['title'] = $ret['header'];

	return $ret;
}

function doAddNewContact()
{
	global $DB, $config;
	$dbp = $config['db_prefix'];

	// Check for existence
	$cc_sql = "SELECT contact_email, contact_name
			FROM {$dbp}suppliers_contacts
			WHERE
				contact_name='{$_REQUEST['contact_name']}' AND
				contact_email='{$_REQUEST['contact_email']}'";
	$DB->query($cc_sql);
	if($DB->num_rows())
	{	// Record exists!
		$ret = addNewContact();
		$ret['msg'] = "<font color=\"red\">Error: " . $_REQUEST['contact_name'] . " already exists in contacts list.</font><br /><br />\n";
		return $ret;
	}

	// Insert
	$nc_sql = "INSERT INTO {$dbp}suppliers_contacts SET
			supplier_id='{$_REQUEST['supplier']}',
			contact_name='{$_REQUEST['contact_name']}',
			contact_email='{$_REQUEST['contact_email']}'";
	$DB->query($nc_sql);

	$ret['msg'] = $_REQUEST['contact_name'] . " added to contacts list.<br />\n";
	return $ret;
}

function editContact()
{
	global $DB, $config;
	$dbp = $config['db_prefix'];

	// Find ID
	if(!$_REQUEST['contact_id'])
	{
		$ret = addNewContact();
		$ret['msg'] = "<font color=\"red\">Error: No Contact ID supplied.</font><br /><br />\n";
		return $ret;
	}

	$cc_sql = "SELECT * FROM {$dbp}suppliers_contacts WHERE contact_id='{$_REQUEST['contact_id']}'";
	$cc = $DB->query_first($cc_sql);

	$page = new Template($config['template_path']);
	$page->set("edit", TRUE);
	$page->set("supplier", $cc['supplier_id']);
	$page->set("suppliers", listSuppliers());
	$page->set("contact_name", $cc['contact_name']);
	$page->set("contact_email", $cc['contact_email']);
	$page->set("popup", $_REQUEST['popup']);
	$ret['content'] = $page->fetch("edit_contacts.tpl.php");
	$ret['msg'] = "";
	$ret['header'] = "Edit Contact Details";
	$ret['title'] = $ret['header'];

	return $ret;
}

function doEditContact()
{
	global $DB, $config;
	$dbp = $config['db_prefix'];

	// Find ID
	if(!$_REQUEST['contact_id'])
	{
		$ret = editContact();
		$ret['msg'] = "<font color=\"red\">Error: No Contact ID supplied.</font><br /><br />\n";
		return $ret;
	}

	// Check data completeness
	if(!$_REQUEST['contact_name'] || !$_REQUEST['contact_email'])
	{
		$ret = editContact();
		$ret['msg'] = "<font color=\"red\">Error: All fields are required.</font><br /><br />\n";
		return $ret;
	}

	// Check contact exists
	$cc_sql = "SELECT * FROM {$dbp}suppliers_contacts WHERE contact_id='{$_REQUEST['contact_id']}'";
	$DB->query($cc_sql);
	if(!$DB->num_rows())
	{
		$ret = editContact();
		$ret['msg'] = "<font color=\"red\">Error: Contact does not exist.</font><br /><br />\n";
		return $ret;
	}

	// Update
	$nc_sql = "UPDATE {$dbp}suppliers_contacts SET
			contact_name='{$_REQUEST['contact_name']}',
			contact_email='{$_REQUEST['contact_email']}'
			WHERE contact_id='{$_REQUEST['contact_id']}'";
	$nc_query = $DB->query($nc_sql);

	$ret['msg'] = $_REQUEST['contact_name'] . " updated in contacts list.<br />\n";
	return $ret;
}
?>
