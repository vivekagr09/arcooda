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
if(!get_user_role($id_user)=='administrator') {
    ob_end_clean();
    echo json_encode(array("status"=>"error"));
    exit;
}
$id_user = (int)$_POST['id'];
$username = strip_tags($_POST['username']);
$role = $_POST['role'];
$active = $_POST['active'];

$query = "UPDATE sml_users SET username=?,role=?,active=? WHERE id=?;";
if($smt = $mysqli->prepare($query)) {
    $smt->bind_param('ssii', $username, $role, $active, $id_user);
    $result = $smt->execute();
    if ($result) {
        echo json_encode(array("status" => "ok"));
    } else {
        echo json_encode(array("status" => "error"));
    }
}