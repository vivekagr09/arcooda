<?php
/***********************
 * Supplier/Contact Functions
 * Filename: supplier_contact.inc.php
 ***********************
 * Purpose:
 *	Dump for supplier functions.
 ***********************/

// Grab ALL Supplier ID's and names and whack it in an array
function listSuppliers($active_only = TRUE)
{
	global $DB;

	// init
	global $config;
	$dbp = $config['db_prefix'];

	$result = array();
	if ($query = $DB->query(sprintf(
		"SELECT `supplier_id`, `supplier_name` FROM %ssuppliers%s",
		$dbp,
		($active_only) ? " WHERE `active` = 1" : ""
	))) {
		while ($row = $DB->fetch_assoc($query)) {
			$result[] = $row;
		}
		$DB->free_result($query);
	}

	return $result;
}

// Grab contact ID's and names related to a supplier and whack it in an array
// If supplier ID is not set, grab ALL ACTIVE contacts
function listContacts($sup_id = 0)
{
	global $DB;

	// init
	global $config;
	$dbp = $config['db_prefix'];
	$contacts = array();

	// Do SQL
	if($sup_id)
	{
		$contacts_sql = "SELECT contact_id, supplier_id, contact_name, contact_email
							FROM {$dbp}suppliers_contacts WHERE supplier_id='$sup_id'";
	}
	else
	{
		$contacts_sql = "SELECT contact_id, {$dbp}suppliers_contacts.supplier_id, contact_name, contact_email
							FROM {$dbp}suppliers_contacts INNER JOIN {$dbp}suppliers USING(supplier_id)
							WHERE active='1'";
	}

	if ($contacts_query = $DB->query($contacts_sql)) {
		while($c = $DB->fetch_array($contacts_query)) {
			$contacts[] = $c;
		}
		$DB->free_result($contacts_query);
	}

	return $contacts;
}
