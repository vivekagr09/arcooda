<?php
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);
ob_start();
session_start();
if((($_SERVER['SERVER_ADDR']==$_SESSION['demo_server_ip']) && ($_SERVER['REMOTE_ADDR']!=$_SESSION['demo_developer_ip']) && ($_SESSION['id_user']==$_SESSION['demo_user_id'])) || ($_SESSION['sml_si']!=session_id())) {
    die();
}
require_once("../../db/connection.php");
require_once('../functions.php');
$settings = get_settings();
$id_user = $_SESSION['id_user'];
$user_info = get_user_info($id_user);
if(!empty($user_info['language'])) {
    set_language($user_info['language'],$settings['language_domain']);
} else {
    set_language($settings['language'],$settings['language_domain']);
}
$username = strip_tags($_POST['username']);
$language = $_POST['language'];
$query_check = "SELECT id FROM sml_users WHERE username=? AND id!=?;";
if($smt = $mysqli->prepare($query_check)) {
    $smt->bind_param('si', $username, $id_user);
    $result_check = $smt->execute();
    if ($result_check) {
        $result_check = get_result($smt);
        if (count($result_check) > 0) {
            ob_end_clean();
            echo json_encode(array("status"=>"error","msg"=>_("Username already registered!")));
            exit;
        }
    }
}
$reload = 0;
$query_l = "SELECT language,username FROM sml_users WHERE id=$id_user LIMIT 1;";
$result_l = $mysqli->query($query_l);
if($result_l->num_rows==1) {
    $row_l = $result_l->fetch_array(MYSQLI_ASSOC);
    $language_exist = $row_l['language'];
    $username_exist = $row_l['username'];
    if($language!=$language_exist) {
        $_SESSION['lang']=$language;
        $reload = 1;
    }
    if($username!=$username_exist) {
        $reload = 1;
    }
}
$query = "UPDATE sml_users SET username=?,language=? WHERE id=?;";
if($smt = $mysqli->prepare($query)) {
    $smt->bind_param('ssi',  $username,$language,$id_user);
    $result = $smt->execute();
    if ($result) {
        ob_end_clean();
        echo json_encode(array("status"=>"ok","reload_page"=>$reload));
    } else {
        ob_end_clean();
        echo json_encode(array("status"=>"error","msg"=>_("Error")));
    }
} else {
    echo json_encode(array("status"=>"error","msg"=>_("Error")));
}

