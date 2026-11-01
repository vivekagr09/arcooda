<?php
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);
ob_start();
require_once("../../backend/functions.php");
require_once("../../db/connection.php");
$id_map = $_POST['id_map'];
$password = str_replace("'","\'",strip_tags($_POST['password']));
$query = "SELECT id FROM sml_maps WHERE id=? AND password=MD5(?);";
if($smt = $mysqli->prepare($query)) {
    $smt->bind_param('is', $id_map,$password);
    $result = $smt->execute();
    if ($result) {
        $result = get_result($smt);
        if (count($result) == 1) {
            ob_end_clean();
            echo json_encode(array("status"=>"ok"));
        } else {
            ob_end_clean();
            echo json_encode(array("status"=>"incorrect"));
        }
    } else {
        ob_end_clean();
        echo json_encode(array("status"=>"error"));
    }
} else {
    ob_end_clean();
    echo json_encode(array("status"=>"error"));
}