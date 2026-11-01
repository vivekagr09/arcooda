<?php
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);
ob_start();
session_start();
if($_SESSION['sml_si']!=session_id()) {
    die();
}
require_once("../../db/connection.php");
require_once("../functions.php");
$id_map = $_POST['id_map'];

$array = array();
$query = "SELECT * FROM sml_icons WHERE id_map=?;";
if($smt = $mysqli->prepare($query)) {
    $smt->bind_param('i',$id_map);
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
ob_end_clean();
echo json_encode($array);