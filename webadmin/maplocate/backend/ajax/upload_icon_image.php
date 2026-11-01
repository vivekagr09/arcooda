<?php
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);
ob_start();
session_start();
if((($_SERVER['SERVER_ADDR']==$_SESSION['demo_server_ip']) && ($_SERVER['REMOTE_ADDR']!=$_SESSION['demo_developer_ip']) && ($_SESSION['id_user']==$_SESSION['demo_user_id'])) || ($_SESSION['sml_si']!=session_id())) {
    //DEMO CHECK
    die();
}
require_once(dirname(__FILE__).'/ImageResize.php');
use \Gumlet\ImageResize;
require_once(dirname(__FILE__).'/../functions.php');
$settings = get_settings();
$user_info = get_user_info($_SESSION['id_user']);
if(!empty($user_info['language'])) {
    set_language($user_info['language'],$settings['language_domain']);
} else {
    set_language($settings['language'],$settings['language_domain']);
}
if (!file_exists(dirname(__FILE__).'/../../viewer/icons/')) {
    mkdir(dirname(__FILE__).'/../../viewer/icons/', 0775);
}
if(isset($_FILES) && !empty($_FILES['file']['name'])){
    $filename = $_FILES['file']['name'];
    $ext = explode('.',$filename);
    $ext = end($ext);
    $milliseconds = round(microtime(true) * 1000);
    $name = "icon_".$milliseconds.".$ext";
    $moved = move_uploaded_file($_FILES['file']['tmp_name'],dirname(__FILE__).'/../../viewer/icons/'.$name);
    if($moved) {
        switch(strtolower($ext)) {
            case 'jpg':
            case 'jpeg':
                try {
                    $image = new ImageResize(dirname(__FILE__).'/../../viewer/icons/'.$name);
                    $image->quality_jpg = 90;
                    $image->gamma(false);
                    $image->resizeToBestFit(300,300, true);
                    $image->save(dirname(__FILE__).'/../../viewer/icons/'.$name);
                    ob_end_clean();
                    echo $name;
                } catch (Exception $e) {
                    ob_end_clean();
                    echo 'ERROR: Retry';
                }
                break;
            case 'png':
                try {
                    $image = new ImageResize(dirname(__FILE__).'/../../viewer/icons/'.$name);
                    $image->quality_png = 8;
                    $image->gamma(false);
                    $image->resizeToBestFit(300,300, true);
                    $image->save(dirname(__FILE__).'/../../viewer/icons/'.$name);
                    ob_end_clean();
                    echo $name;
                } catch (Exception $e) {
                    ob_end_clean();
                    echo 'ERROR: Retry';
                }
                break;
            default:
                ob_end_clean();
                echo $name;
                break;

        }
    } else {
        ob_end_clean();
        echo 'ERROR: code:'.$_FILES["file"]["error"];
    }
}else{
    ob_end_clean();
    echo 'ERROR: '._("File not provided.");
}
exit;
