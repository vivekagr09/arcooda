<?php
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);
ob_start();
session_start();
require_once("../../backend/functions.php");
require_once("../../db/connection.php");
$id_map = $_POST['id_map'];
$query = "SELECT name FROM sml_maps WHERE id=? LIMIT 1; ";
if($smt = $mysqli->prepare($query)) {
    $smt->bind_param('i',  $id_map);
    $result = $smt->execute();
    if ($result) {
        $result = get_result($smt);
        if($result) {
            if(count($result)==1) {
                $row = array_shift($result);
                $name = $row['name'];
                $_SESSION['id_map_sel'] = $id_map;
                $_SESSION['name_map_sel'] = $name;
                ob_end_clean();
                echo json_encode(array("status"=>"ok"));
                exit;
            }
        }
    }
}
ob_end_clean();
echo json_encode(array("status"=>"error"));