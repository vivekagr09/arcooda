<?php 
include_once __DIR__ . '/common.inc.php';
global $_GLOBALS,$DB;
$reserve_by=$_POST['reserve_by']; 
$database_prefix= '`highwaygroup`.';
$table_name='serial_numbers';   
if ($row = $DB->query_first(sprintf(
    "SELECT serial_id,id FROM %s`%s` where `product_id`=0 AND `member_id`=0 AND `invoice_id`=0 AND `reserve_by` != '".$reserve_by."' LIMIT 1",
    	$database_prefix,
    	$table_name
     ), MYSQLI_ASSOC)) {
         $DB->query(sprintf(
        "UPDATE  %s`%s` set  `reserve_by`='".$reserve_by."' WHERE id= %d LIMIT 1",
        $database_prefix,
    	$table_name,
        $row['id']
         ));
     echo json_encode(['serial_id'=>$row['serial_id'],'type'=>'old','id'=>$row['id']]);
}elseif ($row = $DB->query_first(sprintf(
    "SELECT MAX(`serial_id`) AS `max_serial` FROM %s`%s` LIMIT 1",
    $database_prefix,
    $table_name,
), MYSQLI_ASSOC)) {
     echo  json_encode(['serial_id'=>max(8000, $row['max_serial'] + 1),'type'=>'new','id'=>0]);
}else{
	echo  json_encode(['serial_id'=>8000,'type'=>'new','id'=>0]);
}
?>