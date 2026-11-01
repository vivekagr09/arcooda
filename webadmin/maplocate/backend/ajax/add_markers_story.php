<?php
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);
ob_start();
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

$query_order = "SELECT (MAX(priority)+1) as priority FROM sml_story WHERE id_map=$id_map;";
$result_order = $mysqli->query($query_order);
if($result_order) {
    $row_order = $result_order->fetch_array(MYSQLI_ASSOC);
    $order = $row_order['priority'];
}
if(empty($order)) $order=0;

$query = "INSERT INTO sml_story(id_marker,id_map,zoom,description,priority) VALUES(?,?,?,?,?);";
if($smt = $mysqli->prepare($query)) {
    $smt->bind_param('iiisi',$id_marker,$id_map,$zoom,$description,$order);
    $result = $smt->execute();
    if($result) {
        ob_end_clean();
        echo json_encode(array("status"=>"ok"));
    } else {
        ob_end_clean();
        echo json_encode(array("status"=>"error"));
    }
} else {
    ob_end_clean();
    echo json_encode(array("status"=>"error"));
}