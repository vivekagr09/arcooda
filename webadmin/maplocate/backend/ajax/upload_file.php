<?php
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);
ob_start();
session_start();
if((($_SERVER['SERVER_ADDR']==$_SESSION['demo_server_ip']) && ($_SERVER['REMOTE_ADDR']!=$_SESSION['demo_developer_ip']) && ($_SESSION['id_user']==$_SESSION['demo_user_id'])) || ($_SESSION['sml_si']!=session_id())) {
    //DEMO CHECK
    die();
}
require_once(dirname(__FILE__).'/../functions.php');
$settings = get_settings();
$user_info = get_user_info($_SESSION['id_user']);
if(!empty($user_info['language'])) {
    set_language($user_info['language'],$settings['language_domain']);
} else {
    set_language($settings['language'],$settings['language_domain']);
}
if (!file_exists(dirname(__FILE__).'/../../viewer/content/')) {
    mkdir(dirname(__FILE__).'/../../viewer/content/', 0775);
}
if(isset($_FILES) && !empty($_FILES['file']['name'])){
    $filename = $_FILES['file']['name'];
    $moved = move_uploaded_file($_FILES['file']['tmp_name'],dirname(__FILE__).'/../../viewer/content/'.$filename);
    if($moved) {
        ob_end_clean();
        echo $filename;
    } else {
        ob_end_clean();
        echo 'ERROR: code:'.$_FILES["file"]["error"];
    }
}else{
    ob_end_clean();
    echo 'ERROR: '._("File not provided.");
}
exit;
