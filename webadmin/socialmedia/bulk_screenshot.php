<?php
 ini_set('display_errors', 'On');
 error_reporting(1);
include_once __DIR__ . '/common.inc.php';
moduleLoad('shipinvoice');
global $DB, $_GLOBALS;

// print_r($_FILES); die;
// Check if files were uploaded
if (isset($_FILES['myInputName']) && !empty($_FILES['myInputName']['tmp_name'])) {
    // Process the uploaded files
    $file_orig = $_FILES['myInputName']['name'];
    $file_temp = $_FILES['myInputName']['tmp_name'];
    $error     = $_FILES['myInputName']['error'];
    $file_size = $_FILES['myInputName']['size'];
    $file_ext = substr($file_orig, strrpos($file_orig, '.'));
    if (substr($file_ext, 0, 1) == '.') {
        $file_ext = substr($file_ext, 1);
    }
    $file_new  = "mail_" . rand(). '_' . time() . '_' . uniqid() . '.' . $file_ext;
    $dir       = socialMediaPartnerGetOptionList('screenshot_path');
    // Verify the directory exists and is writable
    if (!is_dir($dir)) {
        echo "No file uploaded"; die;
    } elseif (!is_writable($dir)) {
        echo "No file uploaded"; die;
    }
    if (move_uploaded_file($file_temp, $dir . $file_new)) {
        echo  $file_new;
    } else {
        echo "No file uploaded"; die;
    }
} else {
    echo "No file uploaded";die;
}