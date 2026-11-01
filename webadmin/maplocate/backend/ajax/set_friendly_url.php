<?php
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);
ob_start();
session_start();
if((($_SERVER['SERVER_ADDR']==$_SESSION['demo_server_ip']) && ($_SERVER['REMOTE_ADDR']!=$_SESSION['demo_developer_ip']) && ($_SESSION['id_user']==$_SESSION['demo_user_id'])) || ($_SESSION['sml_si']!=session_id())) {
    //DEMO CHECK
    die();
}
require_once("../../db/connection.php");
require_once("../functions.php");
session_write_close();
$settings = get_settings();
$id = (int)$_POST['id'];
$type = $_POST['type'];
$friendly_url = str_replace("'","",strip_tags($_POST['friendly_url']));
$friendly_url = str_replace("\"","",$friendly_url);
$friendly_url = str_replace(" ","_",$friendly_url);
$friendly_url = strtolower($friendly_url);
$furl_blacklist = explode(",",$settings['furl_blacklist']);
if(!empty($friendly_url)) {
    if(in_array($friendly_url,$furl_blacklist)) {
        ob_end_clean();
        echo json_encode(array("status"=>"error"));
        exit;
    }
    switch($type) {
        case 'map':
            $query_check = "SELECT id FROM sml_maps WHERE friendly_url='$friendly_url' AND id != $id;";
            break;
        case 'showcase':
            $query_check = "SELECT id FROM sml_showcases WHERE friendly_url='$friendly_url' AND id != $id;";
            break;
    }
    $result_check = $mysqli->query($query_check);
    if($result_check) {
        if($result_check->num_rows>0) {
            ob_end_clean();
            echo json_encode(array("status"=>"error"));
            exit;
        }
    }
}
switch($type) {
    case 'map':
        $query = "UPDATE sml_maps SET friendly_url=? WHERE id=?;";
        break;
    case 'showcase':
        $query = "UPDATE sml_showcases SET friendly_url=? WHERE id=?;";
        break;
}
if($smt = $mysqli->prepare($query)) {
    $smt->bind_param('si',$friendly_url,$id);
    $result = $smt->execute();
    if ($result) {
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