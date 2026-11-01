<?php
include_once __DIR__ . "/common.inc.php";

$id = $_GET['id'];
$query = $DB->query('SELECT * FROM `world`.`plug_types` WHERE id='.$id);
if($query)
	$row=$DB->fetch_array($query);
header('content-type: image/gif');
readfile($row['file_path']);
