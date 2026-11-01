<?php
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);
ob_start();
session_start();
if (!file_exists(dirname(__FILE__).'/../marker_images_tmp/')) {
    mkdir(dirname(__FILE__).'/../marker_images_tmp', 0775);
}
if(isset($_FILES) && !empty($_FILES['file']['name'])){
    $allowed_ext = array('jpg','jpeg','png');
    $filename = $_FILES['file']['name'];
    $ext = explode('.',$filename);
    $ext = strtolower(end($ext));
    if(in_array($ext,$allowed_ext)){
        $name = "image_".time().".$ext";
        $moved = move_uploaded_file($_FILES['file']['tmp_name'],dirname(__FILE__).'/../marker_images_tmp/'.$name);
        if($moved) {
            include("../../services/generate_thumb.php");
            if(file_exists(dirname(__FILE__).'/../marker_images_tmp/thumb/'.$name)) {
                ob_end_clean();
                echo $name;
            } else {
                ob_end_clean();
                echo 'ERROR: '._("Invalid image.");
            }
        } else {
            ob_end_clean();
            echo 'ERROR: code:'.$_FILES["file"]["error"];
        }
    }else{
        ob_end_clean();
        echo 'ERROR: '._("Only jpg,png files are supported.");
    }
}else{
    ob_end_clean();
    echo 'ERROR: '._("File not provided.");
}
exit;
