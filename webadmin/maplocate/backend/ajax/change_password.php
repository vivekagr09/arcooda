<?php
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);
session_start();
if((($_SERVER['SERVER_ADDR']==$_SESSION['demo_server_ip']) && ($_SERVER['REMOTE_ADDR']!=$_SESSION['demo_developer_ip']) && ($_SESSION['id_user']==$_SESSION['demo_user_id'])) || ($_SESSION['sml_si']!=session_id())) {
    //DEMO CHECK
    die();
}
require_once("../../db/connection.php");
require_once("../../config/config.inc.php");

$id = $_POST['id'];
$password = strip_tags($_POST['password']);
$key_enc = PASSWORDS_ENCRYPT_KEY;

$query = "UPDATE sml_users SET password=AES_ENCRYPT(?, ?) WHERE id=?;";
if($smt = $mysqli->prepare($query)) {
    $smt->bind_param('ssi',  $password, $key_enc,$id);
    $result = $smt->execute();
    if($result) {
        echo json_encode(array("status"=>"ok"));
    } else {
        echo json_encode(array("status"=>"error"));
    }
}

