<?php
/***********************
 * Stocklist Query Functions
 * Filename: stocklist.inc.php
 ***********************
 * Purpose:
 *	Dump for stocklist-related functions.
 ***********************/

function searchProductSuppliers($product_id, $detailed = false)
{
	// init
	require("{$_SERVER['DOCUMENT_ROOT']}/webadmin/new_suppliers/spo_config.php");
	global $DB;
	$dbp = $config['db_prefix'];
	$result = array();

	$sql = "SELECT " .
			($detailed ? "*" : "{$dbp}stocklist.supplier_id, supplier_name, supplier_name_ch, buy_price, sell_price, currency, location") .
			" FROM {$dbp}stocklist JOIN {$dbp}suppliers USING(supplier_id) WHERE product_id='$product_id'";

	$q = $DB->query($sql);
	while($row = $DB->fetch_array($q, MYSQLI_ASSOC))
	{
		$result[] = $row;
	}

	return $result;
}
