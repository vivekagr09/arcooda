<?php
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);
ob_start();
session_start();
if(($_SERVER['SERVER_ADDR']=='5.9.29.89') && ($_SERVER['REMOTE_ADDR']!=$_SESSION['ip_developer']) && ($_SESSION['id_user']==1)) {
    //DEMO CHECK
    die();
}
require_once("../../db/connection.php");
require_once("../functions.php");

$id_map = $_POST['id_map'];
$id_user = $_POST['id_user'];
$checked = $_POST['checked'];

if($checked==1) {
    $mysqli->query("INSERT INTO sml_assign_maps(id_user,id_map) VALUES($id_user,$id_map);");
} else {
    $mysqli->query("DELETE FROM sml_assign_maps WHERE id_user=$id_user AND id_map=$id_map;");
}