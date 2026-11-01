<?php
include_once('freight_common.php');

$posted = array_merge($_GET, $_POST);

if($_SERVER['REMOTE_USER'] == 'Jwalit')
{
	/*
	$res=$DB->query("SELECT id FROM freight_services_destinations WHERE del_time_no=45");
	while($row=$DB->fetch_assoc($res)) {
		$notes='Zone outside express delivery network. Delivery will be done ASAP.';
		$sql="UPDATE freight_services_destinations SET notes=".formatSQLString($notes)." WHERE id=".intval($row['id']);
		$DB->query($sql);
		echo $sql.'<br />';
	}
	die('Finished!!!');
	*/
	$postCodes=array();
	$taken=array();
	$res=$DB->query("SELECT id, LPAD(Pcode,4,'0') as Pcode, Locality, State FROM post_codes ORDER BY Pcode ASC");
	while($row=$DB->fetch_assoc($res))
		$postCodes[$row['Pcode']][]=array('id'=>$row['id'], 'state'=>$row['State'],'suburb'=>$row['Locality']);

	$res=$DB->query("SELECT suburb, LPAD(post_code,4,'0') as post_code, state FROM freight_services_destinations WHERE product_id=4 ORDER BY post_code ASC");
	while($row=$DB->fetch_assoc($res))
		$taken[$row['post_code']][]=array('state'=>$row['state'],'suburb'=>$row['suburb']);

	foreach($taken as $postCode=>$array)
	{
		if(count($postCodes[$postCode]) == count($array))
			unset($postCodes[$postCode]);
		else
		{
			foreach($array as $key=>$ar)
			{
				foreach($postCodes[$postCode] as $key2=>$ar2)
				{
					if(formatCleanString($ar['suburb']) == formatCleanString($ar2['suburb']) && formatCleanString($ar['state']) == formatCleanString($ar2['state']))
						unset($postCodes[$postCode][$key2]);
				}
			}
		}
	}
	// now the post codes have only destinations that aren't entered for the selected courier....so we need to enter them in the system now..
	foreach($postCodes as $postCode=>$parray)
	{
		foreach($parray as $key=>$array)
		{
			$sql=$DB->query_first("SELECT * FROM freight_services_destinations WHERE product_id=4 AND post_code='".$postCode."' AND state=".formatSQLString($array['state'])." AND suburb=".formatSQLString($row['suburb']),MYSQLI_ASSOC);
			if(!$sql)
			{
				$set=array();
				$set[]='product_id=4';
				$set[]="post_code=".formatSQLString($postCode);
				$set[]="suburb=".formatSQLString(ucwords(strtolower($array['suburb'])));
				$set[]="state=".formatSQLString($array['state']);
				$set[]="del_time='2-7 Day Service'";
				$set[]="del_time_no='45'";
				$set[]="notes='Zone outside express delivery network. Delivery will be done ASAP.'";
				$set[]="pcode_id='".$array['id']."'";
				$set[]="date=NOW()";
				$set[]="staff='Jwalit'";

				$sql="INSERT INTO freight_services_destinations SET ".implode(', ',$set);
				$DB->query($sql);
				echo $sql.'<br />';
			}
		}
	}
	//print_array($postCodes);
	$count=0;
	foreach($postCodes as $pCode=>$array)
		$count+=count($array);
	echo $count.'<br/>';

}
else
	die('Not Authorized!!!');
?>