<?php
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);
session_start();
if((($_SERVER['SERVER_ADDR']==$_SESSION['demo_server_ip']) && ($_SERVER['REMOTE_ADDR']!=$_SESSION['demo_developer_ip']) && ($_SESSION['id_user']==$_SESSION['demo_user_id'])) || ($_SESSION['sml_si']!=session_id())) {
    //DEMO CHECK
    die();
}
require_once("../../db/connection.php");
$id_map = (int)$_POST['id_map'];
$password = strip_tags($_POST['password']);
if(empty($password)) {
    $query = "UPDATE sml_maps SET password=NULL WHERE id=?;";
    if($smt = $mysqli->prepare($query)) {
        $smt->bind_param('i',$id_map);
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
} else {
    if ($password != "keep_password") {
        $query = "UPDATE sml_maps SET password=MD5(?) WHERE id=?;";
        if ($smt = $mysqli->prepare($query)) {
            $smt->bind_param('si', $password, $id_map);
            $result = $smt->execute();
            if ($result) {
                ob_end_clean();
                echo json_encode(array("status" => "ok"));
            } else {
                ob_end_clean();
                echo json_encode(array("status" => "error"));
            }
        } else {
            ob_end_clean();
            echo json_encode(array("status" => "error"));
        }
    }
}