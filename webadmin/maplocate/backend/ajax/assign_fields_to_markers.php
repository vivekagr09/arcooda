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
$default_extra_fields = json_decode($_POST['default_extra_fields'],true);
$query = "UPDATE sml_markers SET extra_field_icon_1=?,extra_field_icon_2=?,extra_field_icon_3=?,extra_field_icon_4=?,extra_field_icon_5=?,extra_field_icon_6=?,extra_field_icon_7=?,extra_field_icon_8=?,extra_field_icon_9=?,extra_field_icon_10=?,extra_field_icon_11=?,extra_field_icon_12=?,extra_field_icon_13=?,extra_field_icon_14=?,extra_field_icon_15=?,extra_field_icon_16=?,extra_field_icon_17=?,extra_field_icon_18=?,extra_field_icon_19=?,extra_field_icon_20=? WHERE id_map=?;";
if($smt = $mysqli->prepare($query)) {
    $smt->bind_param('ssssssssssssssssssssi',$default_extra_fields[1],$default_extra_fields[2],$default_extra_fields[3],$default_extra_fields[4],$default_extra_fields[5],$default_extra_fields[6],$default_extra_fields[7],$default_extra_fields[8],$default_extra_fields[9],$default_extra_fields[10],$default_extra_fields[11],$default_extra_fields[12],$default_extra_fields[13],$default_extra_fields[14],$default_extra_fields[15],$default_extra_fields[16],$default_extra_fields[17],$default_extra_fields[18],$default_extra_fields[19],$default_extra_fields[20],$id);
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