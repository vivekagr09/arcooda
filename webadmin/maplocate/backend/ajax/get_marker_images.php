<?php
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);
session_start();
if($_SESSION['sml_si']!=session_id()) {
    die();
}
require_once("../../db/connection.php");
require_once("../functions.php");
$id_marker = $_POST['id_marker'];
$array = array();

$query = "SELECT id,image,main FROM sml_images WHERE id_marker=?;";
if($smt = $mysqli->prepare($query)) {
    $smt->bind_param('i',$id_marker);
    $result = $smt->execute();
    if($result) {
        $result = get_result($smt);
        if(count($result)>0) {
            while($row = array_shift($result)) {
                $array[]=$row;
            }
        }
    }
}

echo json_encode($array);