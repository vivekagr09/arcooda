<?php
/***********************
 * Address Functions
 * Filename: adresses.inc.php
 ***********************
 * Purpose:
 *	Dump for address-related functions.
 ***********************/

function makeAddressArray($dbp)
{
	global $DB;

	$result = array();
	if ($query = $DB->query(sprintf(
		"SELECT `dest_id`, `name` FROM %sship_destinations ORDER BY `name` ASC",
		$dbp
	))) {
		while ($d = $DB->fetch_array($query, MYSQLI_BOTH)) {
			$tmp['dest_id'] = $d['dest_id'];
			$tmp['name']    = $d['name'];
			$result[]       = $tmp;
		}
		$DB->free_result($query);
	}

	return $result;
}
