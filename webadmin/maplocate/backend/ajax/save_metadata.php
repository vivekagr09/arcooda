<?php
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);
ob_start();
session_start();
if((($_SERVER['SERVER_ADDR']==$_SESSION['demo_server_ip']) && ($_SERVER['REMOTE_ADDR']!=$_SESSION['demo_developer_ip']) && ($_SESSION['id_user']==$_SESSION['demo_user_id'])) || ($_SESSION['sml_si']!=session_id())) {
    die();
}
require_once("../../db/connection.php");
require_once("../functions.php");
session_write_close();
$settings = get_settings();
$type = $_POST['type'];
$id = (int)$_POST['id'];
$meta_title = strip_tags($_POST['meta_title']);
$meta_description = strip_tags($_POST['meta_description']);
$meta_image = strip_tags($_POST['meta_image']);
switch($type) {
    case 'map':
        $query = "UPDATE sml_maps SET meta_title=?,meta_description=?,meta_image=? WHERE id=?;";
        break;
    case 'showcase':
        $query = "UPDATE sml_showcases SET meta_title=?,meta_description=?,meta_image=? WHERE id=?;";
        break;
}
if($smt = $mysqli->prepare($query)) {
    $smt->bind_param('sssi',$meta_title,$meta_description,$meta_image,$id);
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
    echo json_encode(array("status" => "error"));
}