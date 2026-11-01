<?php
/***********************
 * Currency Functions
 * Filename: shipping.inc.php
 ***********************
 * Purpose:
 *    Central repository for all functions related to couriers and shipping.
 ***********************/

function makeTermsArray($dbp)
{
    global $DB;

    $result = array();
	if ($query = $DB->query(sprintf(
		"SELECT `dest_id`, `name` FROM %sshipping_terms ORDER BY `term_id` ASC",
		$dbp
	))) {
		while ($d = $DB->fetch_array($query, MYSQLI_BOTH)) {
			$tmp['dest_id'] = $d['dest_id'];
			$tmp['name']    = $d['name'];
			$result[]         = $tmp;
		}
		$DB->free_result($query);
	}

    return $result;
}

function makeCouriersArray($c_id = 0)
{
    global $DB;
    // init
    $couriers = array();

    // mess with the DB    TODO: FIX CONRAD'S SHIT
    $sql    = "SELECT * FROM shipping_services" . (($c_id) ? " WHERE prop_id='$c_id'" : "");
    $result = $DB->query($sql);
    while ($row = $DB->fetch_array($result)) {
        $tmp['id']         = $row['prop_id'];
        $tmp['name']       = $row['name'];
        $tmp['account_no'] = $row['accountno'];
        $couriers[]        = $tmp;
    }

    return $couriers;
}
