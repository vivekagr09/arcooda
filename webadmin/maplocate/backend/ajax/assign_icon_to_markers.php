<?php
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);
ob_start();
session_start();
if((($_SERVER['SERVER_ADDR']==$_SESSION['demo_server_ip']) && ($_SERVER['REMOTE_ADDR']!=$_SESSION['demo_developer_ip']) && ($_SESSION['id_user']==$_SESSION['demo_user_id'])) || ($_SESSION['sml_si']!=session_id())) {
    //DEMO CHECK
    die();
}
require_once("../../db/connection.php");
$id = $_POST['id'];
$type = $_POST['type'];
$markers_size = $_POST['markers_size'];
$markers_color_hex = $_POST['markers_color_hex'];
$markers_icon_color_hex = $_POST['markers_icon_color_hex'];
if(empty($markers_icon_color_hex)) $markers_icon_color_hex="#ffffff";
$markers_color_hex_hover = $_POST['markers_color_hex_hover'];
$markers_icon_color_hex_hover = $_POST['markers_icon_color_hex_hover'];
$markers_id_icon_library = $_POST['markers_id_icon_library'];
$markers_icon = strip_tags($_POST['markers_icon']);
switch($type) {
    case 'category':
        $query = "UPDATE sml_markers SET marker_size=$markers_size,color_hex='$markers_color_hex',icon_color_hex='$markers_icon_color_hex',color_hex_hover='$markers_color_hex_hover',icon_color_hex_hover='$markers_icon_color_hex_hover',id_icon_library=$markers_id_icon_library,icon='$markers_icon' WHERE id IN (SELECT DISTINCT id_marker FROM sml_markers_categories_assoc WHERE id_category=$id);";
        break;
    case 'map':
        $query = "UPDATE sml_markers SET marker_size=$markers_size,color_hex='$markers_color_hex',icon_color_hex='$markers_icon_color_hex',color_hex_hover='$markers_color_hex_hover',icon_color_hex_hover='$markers_icon_color_hex_hover',id_icon_library=$markers_id_icon_library,icon='$markers_icon' WHERE id_map=$id;";
        break;
}
$result = $mysqli->query($query);

if($result) {
    ob_end_clean();
    echo json_encode(array("status"=>"ok","query"=>$query));
} else {
    ob_end_clean();
    echo json_encode(array("status"=>"error","query"=>$query));
}