<?php
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);
ini_set("memory_limit",-1);
ini_set('max_execution_time', 9999);
include_once(dirname(__FILE__)."/thumb.php");
use claviska\SimpleImage;

class ThumbNailer extends SimpleImage{
    public function generateThumbnail($source,$destination,$width=300,$height=300){
        try{
            $this->fromFile($source)
                ->thumbnail($width, $height, "center")
                ->toFile($destination);
            return true;
        }
        catch(Exception $e){
            echo $e;
            return false;
        }
    }
}

$tn = new ThumbNailer();

if (!file_exists(dirname(__FILE__).'/../viewer/marker_images/thumb/')) {
    mkdir(dirname(__FILE__).'/../viewer/marker_images/thumb/', 0775);
}
if (!file_exists(dirname(__FILE__).'/../viewer/marker_images_tmp/thumb/')) {
    mkdir(dirname(__FILE__).'/../viewer/marker_images_tmp/thumb/', 0775);
}
$path = dirname(__FILE__).'/../viewer/marker_images/';
$dir = new DirectoryIterator($path);
foreach ($dir as $fileinfo) {
    if (!$fileinfo->isDot() && ($fileinfo->isFile())) {
        $file_path = $fileinfo->getRealPath();
        $file_name = $fileinfo->getBasename();
        $thumb_path = str_replace(DIRECTORY_SEPARATOR."marker_images".DIRECTORY_SEPARATOR,DIRECTORY_SEPARATOR."marker_images".DIRECTORY_SEPARATOR."thumb".DIRECTORY_SEPARATOR,$file_path);
        if(!file_exists($thumb_path)) {
            $tn->generateThumbnail($file_path,$thumb_path,150,150);
        }
    }
}
$path = dirname(__FILE__).'/../viewer/marker_images_tmp/';
$dir = new DirectoryIterator($path);
foreach ($dir as $fileinfo) {
    if (!$fileinfo->isDot() && ($fileinfo->isFile())) {
        $file_path = $fileinfo->getRealPath();
        $file_name = $fileinfo->getBasename();
        echo $file_name."<br>";
        $thumb_path = str_replace(DIRECTORY_SEPARATOR."marker_images_tmp".DIRECTORY_SEPARATOR,DIRECTORY_SEPARATOR."marker_images_tmp".DIRECTORY_SEPARATOR."thumb".DIRECTORY_SEPARATOR,$file_path);
        if(!file_exists($thumb_path)) {
            $tn->generateThumbnail($file_path,$thumb_path,150,150);
        }
    }
}