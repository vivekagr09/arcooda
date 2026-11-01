<?php
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);
session_start();
if((($_SERVER['SERVER_ADDR']==$_SESSION['demo_server_ip']) && ($_SERVER['REMOTE_ADDR']!=$_SESSION['demo_developer_ip']) && ($_SESSION['id_user']==$_SESSION['demo_user_id'])) || ($_SESSION['sml_si']!=session_id())) {
    //DEMO CHECK
    die();
}
require_once("../../db/connection.php");
$id_map = $_POST['id_map'];
$id_marker = $_POST['id_marker'];
$zoom = $_POST['zoom'];
$description = str_replace("'","\'",htmlspecialchars_decode($_POST['description']));

$query = "UPDATE sml_story SET zoom=?,description=? WHERE id_marker=? AND id_map=?;";
if($smt = $mysqli->prepare($query)) {
    $smt->bind_param('isii',$zoom,$description,$id_marker,$id_map);
    $result = $smt->execute();
    if($result) {
        echo json_encode(array("status"=>"ok"));
    } else {
        echo json_encode(array("status"=>"error"));
    }
} else {
    echo json_encode(array("status"=>"error"));
}