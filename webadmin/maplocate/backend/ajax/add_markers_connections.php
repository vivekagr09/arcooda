<?php
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);
ob_start();
session_start();
if((($_SERVER['SERVER_ADDR']==$_SESSION['demo_server_ip']) && ($_SERVER['REMOTE_ADDR']!=$_SESSION['demo_developer_ip']) && ($_SESSION['id_user']==$_SESSION['demo_user_id'])) || ($_SESSION['sml_si']!=session_id())) {
    //DEMO CHECK
    die();
}
require_once("../../db/connection.php");
$marker_source = $_POST['marker_source'];
$marker_dest = $_POST['marker_dest'];
$color = $_POST['color'];
$width = $_POST['width'];
if(empty($color)) $color="#ff0000";
if(empty($width)) $width=2;
$title = $_POST['title'];
$description = str_replace("'","\'",htmlspecialchars_decode($_POST['description']));

$query = "INSERT INTO sml_markers_connects(id_marker_source,id_marker_dest,color,width,title,description) VALUES(?,?,?,?,?,?);";
if($smt = $mysqli->prepare($query)) {
    $smt->bind_param('iisiss',$marker_source,$marker_dest,$color,$width,$title,$description);
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