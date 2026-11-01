<?php
function searchForwarderQuotes($inquiry_id=0)
{
	global $DB;
	$results=array();

	$sql="SELECT `id` FROM `coinop`.`freight_services_quotes`";
	if($inquiry_id!=0) $sql.=" WHERE `invoice_id` = ".intval($inquiry_id);

	$query=$DB->query($sql);
	while($row=$DB->fetch_array($query,MYSQLI_ASSOC))
		$results[]=$row['id'];

	return $results;
}

function searchCourierQuotes($inquiry_id=0)
{
	global $DB;

	$result = array();
	if ($query = $DB->query(sprintf(
		"SELECT `id` FROM `coinop`.`inquiry_courier_quotes`%s",
		(0 != $inquiry_id) ? sprintf(
			" WHERE `inquiry_id` = %d",
			$inquiry_id
		) : ""
	))) {
		while ($row = $DB->fetch_assoc($query)) {
			$result[] = $row['id'];
		}
		$DB->free_result($query);
	}

	return $result;
}

function summarizeForwarderQuote($quote_id)
{
	global $DB;

	$sql="SELECT * FROM `coinop`.`freight_services_quotes` WHERE `id` = " . intval($quote_id);
	$row=$DB->query_first($sql,MYSQLI_ASSOC);

	if($row) return $row;
	return array();
}

function summarizeCourierQuote($quote_id)
{
	global $DB;

	$sql="SELECT * FROM `coinop`.`inquiry_courier_quotes` WHERE `id` = ".intval($quote_id);
	$row=$DB->query_first($sql,MYSQLI_ASSOC);
	if($row) return $row;

	return array();
}

function updateForwarderQuote($quote_summary)
{
	global $DB;

	if($quote_summary['id']<=0)
	{
		$sql="INSERT INTO `coinop`.`freight_services_quotes` () VALUES ()";
		$DB->query($sql);
		$quote_summary['id']=$DB->insert_id;
	}

	$old_summary=summarizeForwarderQuote($quote_summary['id']);

	$updates=array();
	foreach($old_summary as $key=>$value)
		if(isset($quote_summary[$key]) && $quote_summary[$key]!=$value)
			$updates[$key]=$quote_summary[$key];

	if(count($updates)>0)
	{
		$sql="UPDATE `coinop`.`freight_services_quotes` SET ";
		$sqlupdate=array();
		foreach($updates as $key=>$value)
			$sqlupdate="`".$key."`='".addslashes($value)."'";
		$sql.=implode(', ',$sqlupdate);
		$sql.=" WHERE id=".intval($quote_summary['id']);
		$DB->query($sql);
	}

	return $quote_summary['id'];
}
