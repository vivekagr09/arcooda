<?php
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);
session_start();
if($_SESSION['sml_si']!=session_id()) {
    die();
}
require_once("../functions.php");
require_once("../../db/connection.php");
$id_map = $_POST['id_map'];

$code = "";
$query = "SELECT m.code FROM sml_maps as m WHERE m.id=?;";
if($smt = $mysqli->prepare($query)) {
    $smt->bind_param('i',$id_map);
    $result = $smt->execute();
    if($result) {
        $result = get_result($smt);
        if(count($result)==1) {
            $row = array_shift($result);
            $code = $row['code'];
        }
    }
}
echo json_encode(array("code"=>$code));