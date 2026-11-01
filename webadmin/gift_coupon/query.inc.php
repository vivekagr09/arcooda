<?php

//------------------------------------------------------------------------
/**
 * giftcouponFromPost
 *
 * A function that creates an array representing a gift coupon from existing database entries and input argument $posted.
 *
 * @access public
 * @param array
 *
 * @return array
 */
function giftcouponFromPost($posted)
{
	global $DB;
	$id=$posted['id'];
	$new_vals['id']=intval($id);
	$old_vals=giftcouponFromDatabase($id);
	foreach($old_vals as $key=>$value)
		if(isset($posted[$key]))
			$new_vals[$key]=$posted[$key];
	$hidden=array('amount_value', 'amount_extra','bonus_value', 'expiry_date');
	if($old_vals['expiry_value']!=$posted['expiry_value'])
	{
		if($posted['expiry_value']!=0)
			$new_vals['expiry_date']=formatDateTime(strtotime('+'.$posted['expiry_value'].' days'));
	}
	$new_vals['amount']=$posted['amount_extra'];
	if($posted['amount_value']!=0) $new_vals['amount']=$posted['amount_value'];
	if($posted['bonus_value']!=0) $new_vals['bonus']=$posted['bonus_value'];
	return $new_vals;
}

//------------------------------------------------------------------------
/**
 * giftcouponFromDatabase
 *
 * A function that creates an array representing a gift coupon from a database entry.
 * A single row from the database table highwaygroup.gift_coupon who's id attribute matches the input argument $id is selected and returned as an array.
 *
 * @access public
 * @param integer
 *
 * @return array
 */
function giftcouponFromDatabase($id)
{
	global $DB;
	$id=intval($id);

	
	if(!$id)
	{


		$summary=array();
		foreach(getTableFields('`highwaygroup`.`gift_coupon`') as $row)
			$summary[$row[0]]=$row[4];
		return $summary;
	}
	$ext_fields=giftcouponGetOptionList('ext_fields');

	$ext_str=array();
	foreach($ext_fields as $key=>$value)
		$ext_str[]=$value.' AS '.$key;
	 $sql=" SELECT".
		"	g.*,IF(`expiry_date` > NOW(), 'n', 'y') AS expired, UNIX_TIMESTAMP(`expiry_date`) AS expiry_time, UNIX_TIMESTAMP(`created`) AS time_added  FROM highwaygroup.gift_coupon g".
		" WHERE g.id=".$id;
	$res=$DB->query($sql);
	while($row=$DB->fetch_assoc($res)){
		$preset_list[]=$row;
	}
	
	// echo '<pre>';
	// 	print_r($preset_list);
	// echo '</pre>';
	return $preset_list[0];

}

//------------------------------------------------------------------------
/**
 * giftcouponToDatabase
 *
 * A function that saves a gift coupon to database, input is taken as an array (summary input argument).
 * If a coupon exists in the database with the same id attribute as the new coupon's,
 * that row is updated, if no coupon with the same ID exists, a new row is created.
 * The id attribute of the new coupon's is returned as an integer.
 *
 * @access public
 * @param array
 *
 * @return integer
 */
function giftcouponToDatabase($summary)
{
	global $DB, $_GLOBALS;
	$id=intval($summary['id']);

	$old_giftcoupon=giftcouponFromDatabase($id);

	foreach($old_giftcoupon as $key=>$value)
		if((!is_array($summary[$key]) && !is_array($value) && formatSQLString($summary[$key])==formatSQLString($value)) || $summary[$key]==$value)
			unset($summary[$key]);
	foreach($summary as $key=>$value)
		if(!array_key_exists($key,$old_giftcoupon))
			unset($summary[$key]);

	$fields=array();
	$query=$DB->query("SHOW COLUMNS FROM highwaygroup.gift_coupon");
	while($row=$DB->fetch_array($query))
		$fields[$row[0]]=$row;
	$DB->free_result($query);


	if(count($summary)>0)
	{
		if(giftcouponExists($id))
		{
			$set_vals=array();
			foreach($summary as $key=>$value)
				if(array_key_exists($key,$fields))
					if(isset($summary[$key]))
						$set_vals[$key]="`".$key."`=".formatSQLString($value);
					else
						$set_vals[$key]="`".$key."`=NULL";

			$sql="UPDATE highwaygroup.gift_coupon SET ".implode(', ',$set_vals)." WHERE id=".$id." LIMIT 1";
			$DB->query($sql);
		}
		else
		{
			$set_vals=array();
			foreach($summary as $key=>$value)
				if(array_key_exists($key,$fields))
					if(isset($summary[$key])) $set_vals[$key]=formatSQLString($value);
					else $set_vals[$key]='NULL';
			$set_vals['created']=formatSQLDateTime($_SERVER['REQUEST_TIME']);
			$sql="INSERT INTO highwaygroup.gift_coupon(`".implode('`, `',array_keys($set_vals))."`) VALUES(".implode(", ",$set_vals).")";
			$query=$DB->query($sql);
			$id=$DB->insert_id();

		}
	}
	return $id;
}

//------------------------------------------------------------------------
/**
 * giftcouponExists
 *
 * A function that queries the table highwaygroup.gift_coupon and returns one element.
 * If an element exists who's id attribute equals the input variable id, that element's id is returned.
 *
 * @access public
 * @param integer
 *
 * @return integer
 */
function giftcouponExists($id)
{
	global $DB;
	$id=intval($id);
	return $DB->query_first("SELECT id FROM highwaygroup.gift_coupon WHERE id=".$id, MYSQL_ASSOC);
}

//------------------------------------------------------------------------
/**
 * giftcouponPresetFromPost
 *
 * A function that generates a gift coupon represented by an array from the input argument $posted.
 *
 * @access public
 * @param array
 *
 * @return array
 */
function giftcouponPresetFromPost($posted)
{
	global $DB;
	$preset_id=$posted['preset_id'];
	$new_vals['id']=intval($preset_id);
	$old_vals=giftcouponPresetFromDatabase($preset_id);
	foreach($old_vals as $key=>$value)
		if(isset($posted[$key]))
			$new_vals[$key]=$posted[$key];
	if($old_vals['expiry_value']!=$posted['expiry_value'])
	{
		if($posted['expiry_value']!=0) {
			$new_vals['expiry_date']=formatDateTime(strtotime('+'.$posted['expiry_value'].' day'));
		}
	} else {
		if($new_vals['expiry_date'] != $old_vals['expiry_date']) {
			$new_vals['expiry_date'] = $posted['expiry_date'];
		} else {
			$new_vals['expiry_date'] = $old_vals['expiry_date'];
		}
	}
	$new_vals['value']=$posted['amount_extra'];
	if($posted['amount_value']!=0) $new_vals['value']=$posted['amount_value'];
	if($posted['bonus_value']!=0) $new_vals['bonus']=$posted['bonus_value'];
	$new_vals['title'] = $posted['coupon_title'];
	$new_vals['active'] = $posted['active'];
	$new_vals['fixed_price'] = $posted['fixed_sell_price'];
	$new_vals['sell_price'] = $posted['product_sale_price'];
	$new_vals['cost_price'] = $posted['product_cost_price'];
	$new_vals['currency'] = $posted['product_currency'];
	$new_vals['restriction_value'] = $posted['restriction_value'];
	$new_vals['restriction_product'] = $posted['restriction_product'];
	$new_vals['restriction_product_url'] = $posted['restriction_product_url'];
	$new_vals['restriction_product_name'] = $posted['restriction_product_name'];

	$new_vals['cat'] = "";

	switch($new_vals['section']) {
		case 1 :
			$new_vals['cat'] = $posted['category_1'];
			break;

		case 2 :
			$new_vals['cat'] = $posted['category_2'];
			break;

		case 3 :
			$new_vals['cat'] = $posted['category_3'];
			break;

		default :
			$new_vals['cat'] = $posted['category_0'];
			break;

	}

	return $new_vals;
}

//------------------------------------------------------------------------
/**
 * giftcouponPresetFromDatabase
 *
 * A function that creates an array representing a gift coupon from a database entry.
 * A single row from the database table highwaygroup.gift_coupon who's id attribute matches the input argument $id is selected and returned as an array.
 *
 * @access public
 * @param integer
 *
 * @return array
 */
function giftcouponPresetFromDatabase($preset_id)
{
	global $DB;
	$preset_id=intval($preset_id);
	if(!$preset_id)
	{
		$summary=array();
		foreach(getTableFields('highwaygroup.gift_coupon') as $row)
			$summary[$row[0]]=$row[4];
		return $summary;
	}
	$ext_fields=array();//presetGetOptionList('ext_fields');
	$ext_str=array();
	foreach($ext_fields as $key=>$value)
		$ext_str[]=$value.' AS '.$key;
	$sql=" SELECT".
		"	p.*".((count($ext_fields))? (",	".implode(", ",$ext_str)):'').
		" FROM highwaygroup.gift_coupon p".
		" WHERE p.id=".$preset_id;
	$preset=$DB->query_first($sql);
	return $preset;
}

//------------------------------------------------------------------------
/**
 * giftcouponPresetToDatabase
 *
 * A function that saves a gift coupon to database, input is taken as an array (summary input argument).
 * if a coupon exists in the database with the same id attribute as the new coupon's,
 * that row is updated, if no coupon with the same ID exists, a new row is created.
 * The id attribute of the new coupon's is returned as an integer.
 *
 * This is what being used to update the DB, all coupon are preset!
 *
 * @access public
 * @param array
 *
 * @return integer
 */
function giftcouponPresetToDatabase($summary)
{
	global $DB, $_GLOBALS;
	$preset_id=intval($summary['id']);
	
	$old_preset=giftcouponPresetFromDatabase($preset_id);

	foreach($old_preset as $key=>$value)
		if((!is_array($summary[$key]) && !is_array($value) && formatSQLString($summary[$key])==formatSQLString($value)) || $summary[$key]==$value)
			unset($summary[$key]);
	foreach($summary as $key=>$value)
		if(!array_key_exists($key,$old_preset))
			unset($summary[$key]);

	$fields=array();
	$query=$DB->query("SHOW COLUMNS FROM highwaygroup.gift_coupon");
	while($row=$DB->fetch_array($query))
		$fields[$row[0]]=$row;
	$DB->free_result($query);

	if(count($summary)>0)
	{
		if(giftcouponPresetExists($preset_id))
		{
			$set_vals=array();
			foreach($summary as $key=>$value)
				if(array_key_exists($key,$fields))
					if(isset($summary[$key]))
						$set_vals[$key]="`".$key."`=".formatSQLString($value);
					else
						$set_vals[$key]="`".$key."`=NULL";

			$sql="UPDATE highwaygroup.gift_coupon SET ".implode(', ',$set_vals)." WHERE id=".$preset_id." LIMIT 1";
			$DB->query($sql);
		}
		else
		{
			$set_vals=array();
			foreach($summary as $key=>$value)
				if(array_key_exists($key,$fields))
					if(isset($summary[$key]))
						$set_vals[$key]=formatSQLString($value);
					else
						$set_vals[$key]='NULL';
			 $sql="INSERT INTO highwaygroup.gift_coupon(`".implode('`, `',array_keys($set_vals))."`) VALUES(".implode(", ",$set_vals).")";
			
			$query=$DB->query($sql);
			$preset_id=$DB->insert_id();
		}
	}
	return $preset_id;
}

//------------------------------------------------------------------------
/**
 * giftcouponPresetExists
 *
 * ********** DUPLICATE OF FUNCTION: giftcouponExists **********
 *
 * A function that queries the table highwaygroup.gift_coupon and returns one element.
 * If an element exists who's id attribute equals the input variable preset_id, that element's id is returned.
 *
 * @access public
 * @param integer
 *
 * @return integer
 */
function giftcouponPresetExists($preset_id)
{
	global $DB;
	$preset_id=intval($preset_id);
	return $DB->query_first("SELECT id FROM highwaygroup.gift_coupon WHERE id=".$preset_id);
}

//------------------------------------------------------------------------
/**
 * giftcouponSearch
 *
 * A function that queries the database table highwaygroup.gift_coupon using the input argument 'criteria' as a search argument.
 * Any rows found who share elements with criteria are returned in an array.
 *
 * @access public
 * @param array
 *
 * @return array
 */
function giftcouponSearch($criteria = '')
{
	global $DB;
	$results = array();
	$sql = "SELECT `a`.`id` FROM `highwaygroup`.`gift_coupon` AS `a`";
	$joins = array();
	$fields = getTableFields("`highwaygroup`.`gift_coupon`");
	$ext_fields = giftcouponGetOptionList('extra_fields');
	$whereAND = array();
	if (is_array($criteria))
	{
		foreach($criteria as $field => $value)
		{
			if (isset($fields[$field]))
			{
				if (is_array($value))
				{
					if (count($value) > 0)
					{
						$whereOR = array();
						if (substr($fields[$field][1], 0, 4) == 'set(')
						{
							foreach($value as $sub_value) if ($sub_value == "") $whereOR[] = "`a`.`" . $field . "` = ''";
							else $whereOR[] = "FIND_IN_SET(" . formatSQLString($sub_value) . ", `a`.`" . $field . "`)";
						}
						else
						{
							foreach($value as $sub_value) $whereOR[] = "`a`.`" . $field . "` = " . formatSQLString($sub_value);
						}
						$whereAND[] = "(" . implode(" OR ", $whereOR) . ")";
					}
					else
					{
						return array();
					}
				}
				else
				{
					if (substr($fields[$field][1], 0, 4) == 'set(' && $value != "")
					{
						$whereAND[] = "FIND_IN_SET(" . formatSQLString($value) . ", `a`.`" . $field . "`)";
					}
					else
					{
						$whereAND[] = "`a`.`" . $field . "` = " . formatSQLString($value);
					}
				}
				unset($fields[$field]);
			}
			elseif (isset($ext_fields[$field]))
			{
				if (is_array($value))
				{
					if (count($value) > 0)
					{
						$whereOR = array();
						foreach($value as $sub_value) $whereOR[] = $ext_fields[$field] . " = " . formatSQLString($sub_value);
						$whereAND[] = "(" . implode(" OR ", $whereOR) . ")";
					}
					else
					{
						return array();
					}
				}
				else
				{
					$whereAND[] = $ext_fields[$field] . " = " . formatSQLString($value);
				}
				unset($ext_fields[$field]);
			}
		}
	}
	elseif (is_string($criteria) && $criteria != '')
	{
		$criteria = array('any_text' => $criteria);
	}
	else $criteria = array();
	$search_for = array();
	if (array_key_exists("any_text", $criteria))
	{
		if (is_array($criteria['any_text'])) $search_for = $criteria['any_text'];
		if (is_string($criteria['any_text'])) $search_for = explode(' ', $criteria['any_text']);
	}
	if (count($search_for) > 0)
	{
		$searchBits = array();
		foreach($search_for as $findme)
		{
			$whereOR = array("`title`", "`name`", "`description`");
			foreach($searchBits as $key) if (trim($findme) != '') $whereOR[] = $key . " LIKE '%" . str_replace('%', '\%', str_replace('_', '\_', addslashes($findme))) . "%'";
			if (count($whereOR) > 0) $whereAND[] = "(" . implode(" OR ", $whereOR) . ")";
		}
	}
	if (count($whereAND) > 0) $sql.= " WHERE " . implode(" AND ", $whereAND);
	$sql.= " ORDER BY `a`.`id` ASC";
	$query = $DB->query($sql);
	while ($row = $DB->fetch_assoc($query)) $results[] = $row["id"];
	$DB->free_result($query);
	return $results;
}

//------------------------------------------------------------------------
/**
 * giftcouponSort
 *
 * A function that returns an array of coupons from the database table 'highwaygroup.gift_coupon' ordered by the input argument $order_by.
 *
 * @access	public
 * @param array
 * @param string
 *
 * @return array
 */
function giftcouponSort($item_list,$order_by='id')
{
	global $DB;
	if(!is_array($item_list) || count($item_list)<2) return $item_list;

	$fields=getTableFields('highwaygroup.gift_coupon');
	$sql=" SELECT g.id FROM highwaygroup.gift_coupon g";
	$where=" WHERE g.id IN('".implode("', '",$item_list)."')";
	if(isset($fields[$order_by])) $order=" ORDER BY ".$order_by." ASC";

	$item_list=array();
	$query=$DB->query($sql.$where.$order);
	while($row=$DB->fetch_assoc($query))
		$item_list[]=$row['id'];

	$DB->free_result($query);

	return $item_list;
}

//------------------------------------------------------------------------
/**
 * giftcouponGetOptionList
 *
 * A function that returns an array of strings selected using the input argument $table_name.
 * Output represents a list of input options.
 * If input table_name equals 'section' a database query is executed to produce the output containing products from database.
 *
 * @access	public
 * @param string
 *
 * @return array
 */
function giftcouponGetOptionList($table_name='')
{
	global $DB;
	global $_GLOBALS;
	$table = "";
	$option_list = array();
	switch($table_name)
	{
	case "section":
		$table = "`products_section`";
		$key = "`id`";
		$value = "`name`";
		break;
	case "site_id":
		// HE, CON, ASP, [AGS same site id as HE]
		$requiredWebsites = array(2, 9, 20,39);
		$allWebsites = getWebsitesList();
		$websites = array();
		foreach($allWebsites as $k => $v) {
			if (in_array($k, $requiredWebsites)) {
				$websites[$k] = $v;
			}
		}
		return $websites;

	case "value":
	case "bonus":
		return array(
			'0.00' => '0%',
			'0.05' => '5%',
			'0.10' => '10%',
			'0.15' => '15%',
			'0.20' => '20%',
			'0.25' => '25%',
			'0.30' => '30%',
			'0.35' => '35%',
			'0.40' => '40%',
			'0.45' => '45%',
			'0.50' => '50%',
			'0.55' => '55%',
			'0.60' => '60%',
			'0.65' => '65%',
			'0.70' => '70%',
			'0.75' => '75%',
			'0.80' => '80%',
			'0.85' => '85%',
			'0.90' => '90%',
			'0.95' => '95%',
			'1.00' => '100%',
		);
	case "active":
		return array(
			'y' => 'Active',
			'n' => 'Cancelled',
		);
	case "expiry_value":
		return array(
			'1' => '1 day',
			'7' => '7 days',
			'30' => '30 days',
			'0' => 'Other',
		);
	case 'extra_fields':
	case 'ext_fields':
		return array(
			"expired"=>"IF(`expiry_date` > NOW(), 'n', 'y')",
			"expiry_time"=>"UNIX_TIMESTAMP(`expiry_date`)",
			"time_added"=>"UNIX_TIMESTAMP(`created`)"
		);

	case 'cat_0':
		$cat = array(0 => "All");

		return $cat;

	case 'cat_1':
		$cat_query = $DB->query("SELECT * FROM `product_types` WHERE `section_id` = 1");
		$cat = array();
		$cat[0] = "All";
		while($row = $DB->fetch_assoc($cat_query)) $cat[$row['type_id']] = $row['name'];

		return $cat;

	case 'cat_2':
		$cat_query = $DB->query("SELECT * FROM `product_types` WHERE `section_id` = 2");
		$cat[0] = "All";
		while($row = $DB->fetch_assoc($cat_query)) $cat[$row['type_id']] = $row['name'];

		return $cat;

	case 'cat_3':
		$cat_query = $DB->query("SELECT * FROM `product_types` WHERE `section_id` = 3");
		$cat[0] = "All";
		while($row = $DB->fetch_assoc($cat_query)) $cat[$row['type_id']] = $row['name'];

		return $cat;

	case 'coupon_type' :
		return array(
			0 => "All",
			1 => "Category",
			2 => "Individual Product"
		);

	case 'show_coupon' :
		return array(
			0 => "No",
			1 => "Yes"
		);

	case 'discount_type' :
		return array(
			0 => "% Discount",
			1 => "Fixed Price"
		);
	}
	if ($table != "")
	{
		$query = $DB->query("SELECT " . $key . "," . $value . " FROM " . $table);
		while ($row = $DB->fetch_array($query)) $option_list[$row[0]] = $row[1];
	}
	return $option_list;
}

//------------------------------------------------------------------------
/**
 * giftcouponGetValue
 *
 * A function that returns a single element from the table highwaygroup.gift_coupon.
 * Output is selected using input arguments id and field where the row equals id and the column equals field.
 *
 * If no element matching the input is found, output is null.
 *
 * @access	public
 * @param integer
 * @param string
 * @return array
 */
function giftcouponGetValue($id, $field)
{
	global $DB;
	$fields=getTableFields('highwaygroup.gift_coupon');
	if(array_key_exists($field,$fields))
	{

		$row=$DB->query_first("SELECT `".$field."` FROM highwaygroup.gift_coupon g WHERE g.id=".intval($id)." LIMIT 1", MYSQL_ASSOC);
		return $row[$field];
	}
	$special_fields=giftcouponGetOptionList('ext_fields');
	if(array_key_exists($field,$special_fields))
	{
		$row=$DB->query_first("SELECT ".$special_fields[$field]." AS ".$field." FROM highwaygroup.gift_coupon g WHERE g.id=".intval($id)." LIMIT 1",MYSQL_ASSOC);
		return $row[$field];
	}
	return null;
}

//------------------------------------------------------------------------
/**
 * giftcouponHasValue
 *
 * Returns true or false based on wether input argument $value equals the output
 * of function giftcouponGetValue.
 *
 * @access	public
 * @param integer
 * @param string
 * @param integer
 *
 * @return boolean
 */
function giftcouponHasValue($id, $field, $value)
{
	return (giftcouponGetValue($id,$field)==$value);
}

//------------------------------------------------------------------------
/**
 * giftcouponFormatNumber
 *
 * Returns the value of input argument $id as a string of the form: 'CP-' - up to 4 leading zeros - value.
 * e.g. 9 == CP-0009, 54 == CP-0054
 *
 * @access	public
 * @param integer
 *
 * @return string
 */
function giftcouponFormatNumber($id)
{
	return 'CP-'.str_pad($id,4,'0',STR_PAD_LEFT);
}

//------------------------------------------------------------------------
/**
 * giftcouponGetLock
 *
 * A function queries the table highwaygroup.gift_lock and returns a row who's voucher_id element equals the input argument $id.
 * If no entry is found, false is returned.
 *
 * @access	public
 * @param integer
 *
 * @return mixed, array or boolean
 */
function giftcouponGetLock($id)
{
	global $DB;
	$res=$DB->query("SELECT *, UNIX_TIMESTAMP(expiry) AS expiry_time FROM `highwaygroup`.`gift_lock` WHERE voucher_id=".intval($id)." LIMIT 1");
	$row=$DB->fetch_assoc($res);

	if($row['expiry_time']<$_SERVER['REQUEST_TIME'])
		$row=false;
	return $row;
}

//------------------------------------------------------------------------
/**
 * giftcouponSetLock
 *
 * Function that deletes from the table highwaygroup.gift_lock any rows who's voucher_id element equals input argument id.
 * A new row is then created in highwaygroup.gift_lock with attributes voucher_id, staff_id, expiry equal to input arguments id, staff_id and timeout respectively.
 *
 * @access	public
 * @param integer
 * @param string
 * @param integer
 */
function giftcouponSetLock($id,$staff_id='',$timeout=0)
{
	global $DB;
	$DB->query("DELETE FROM highwaygroup.gift_lock WHERE voucher_id=".intval($id));
	if($timeout==0) $timeout=300;
	if($staff_id!='') $DB->query("INSERT INTO highwaygroup.gift_lock(voucher_id,staff_id,expiry) VALUES(".intval($id).",".formatSQLString($staff_id).",".formatSQLDateTime($_SERVER['REQUEST_TIME']+$timeout).")");
}
?>