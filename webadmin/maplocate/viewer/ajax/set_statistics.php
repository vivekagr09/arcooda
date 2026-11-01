<?php
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);
session_start();
require_once("../../db/connection.php");
$id_marker = (int)$_POST['id'];

$query = "UPDATE sml_markers SET access_count=access_count+1 WHERE id=?;";
if($smt = $mysqli->prepare($query)) {
    $smt->bind_param('i',$id_marker);
    $result = $smt->execute();
}