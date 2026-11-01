<?php
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);
session_start();
if((($_SERVER['SERVER_ADDR']==$_SESSION['demo_server_ip']) && ($_SERVER['REMOTE_ADDR']!=$_SESSION['demo_developer_ip']) && ($_SESSION['id_user']==$_SESSION['demo_user_id'])) || ($_SESSION['sml_si']!=session_id())) {
    //DEMO CHECK
    die();
}
require_once("../../db/connection.php");
require_once("../functions.php");
$id_user = $_SESSION['id_user'];
$id_map = $_POST['id_map'];
if(get_user_role($id_user)=='administrator') {
    $where = " WHERE id=?";
} else {
    $where = " WHERE id_user = ? AND id=? ";
}
$query = "DELETE FROM sml_maps $where; ";
if($smt = $mysqli->prepare($query)) {
    if(get_user_role($id_user)=='administrator') {
        $smt->bind_param('i',$id_map);
    } else {
        $smt->bind_param('ii',$id_user,$id_map);
    }
    $result = $smt->execute();
    if($result) {
        $mysqli->query("ALTER TABLE sml_maps AUTO_INCREMENT = 1;");
        $mysqli->query("ALTER TABLE sml_markers AUTO_INCREMENT = 1;");
        if(isset($_SESSION['id_mal_sel'])) {
            if($_SESSION['id_mal_sel']==$id_map) {
                unset($_SESSION['id_mal_sel']);
                unset($_SESSION['name_map_sel']);
            }
        }
        echo json_encode(array("status"=>"ok"));
    } else {
        echo json_encode(array("status"=>"error"));
    }
} else {
    echo json_encode(array("status"=>"error"));
}