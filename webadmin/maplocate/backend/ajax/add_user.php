<?php
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);
session_start();
if((($_SERVER['SERVER_ADDR']==$_SESSION['demo_server_ip']) && ($_SERVER['REMOTE_ADDR']!=$_SESSION['demo_developer_ip']) && ($_SESSION['id_user']==$_SESSION['demo_user_id'])) || ($_SESSION['sml_si']!=session_id())) {
    //DEMO CHECK
    die();
}
require_once("../../db/connection.php");
require_once("../../config/config.inc.php");
require_once("../functions.php");
$id_user = $_SESSION['id_user'];
if(!get_user_role($id_user)=='administrator') {
    ob_end_clean();
    echo json_encode(array("status"=>"error"));
    exit;
}

$username = strip_tags($_POST['username']);
$password = strip_tags($_POST['password']);
$role = $_POST['role'];
$key_enc = PASSWORDS_ENCRYPT_KEY;

$query = "INSERT INTO sml_users(username,password,role) VALUES(?,AES_ENCRYPT(?, ?),?); ";
if($smt = $mysqli->prepare($query)) {
    $smt->bind_param('ssss',$username,$password,$key_enc,$role);
    $result = $smt->execute();
    if($result) {
        echo json_encode(array("status"=>"ok"));
    } else {
        echo json_encode(array("status"=>"error"));
    }
}

