<?php
include_once __DIR__ . '/common.inc.php';

if ($_REQUEST['i']) {
    $img = $_SERVER['DOCUMENT_ROOT'] . "/" . $_REQUEST['i'];
    if (file_exists($img)) {
        $info = explode(".", $img);
        $ext  = strtolower($info[count($info) - 1]);
        switch ($ext) {
            case "jpeg":
            case "jpg":
                header("Content-type: image/jpeg");
                break;
            case "gif":
                header("Content-type: image/gif");
                break;
            case "png":
                header("Content-type: image/png");
                break;
            default:
                echo "Image not recognized.";
                return 1;
        }
        echo file_get_contents($img);
    } else {
        echo "Image not found.";
        return 1;
    }
} else {
    echo "Image not specified.";
    return 1;
}
