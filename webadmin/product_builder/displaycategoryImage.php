<?php
//include_once __DIR__ . '/common.inc.php';

$action     = strtolower($_GET['action']);
$$name         = $_GET['name'];

function viewScreenshot($name, $thumb)
{
    $filename  = $name;
    $dir       = $_SERVER['DOCUMENT_ROOT'] . '/images/builder/';
    $thumb_dir = $dir . 'modified/';
    $file      = $dir . $filename;
    $mime_type = mime_content_type($filename);
    header('Date: ' . gmdate(DATE_RFC1123, time()));
    header("Content-Type: " . $mime_type);
    header('Content-Disposition: inline; filename="' . $filename . '"');
    echo file_get_contents($file);

}

/**********************************************************Functions Definitions: End ********************************************************************/

// Main Switch-Case...
switch ($action) {
    
    case "view":
        viewScreenshot($_GET['name'], $_GET['thumb']);
        break;
    default:
        die("Invalid Selection!!");
}
?>