<?php
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);
session_start();
if($_SESSION['sml_si']!=session_id()) {
    die();
}
require_once("../functions.php");
require_once("../../db/connection.php");
$id_map = $_POST['id_map'];

$return = array();
$query = "SELECT id,name FROM sml_markers WHERE id NOT IN(SELECT id_marker FROM sml_story WHERE id_map=?) AND id_map=?;";
if($smt = $mysqli->prepare($query)) {
    $smt->bind_param('ii',$id_map,$id_map);
    $result = $smt->execute();
    if($result) {
        $result = get_result($smt);
        if(count($result)>0) {
            while ($row = array_shift($result)) {
                $return[] = $row;
            }
        }
    }
}
echo json_encode(array("markers"=>$return));