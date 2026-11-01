<?php
 ini_set('display_errors', 'On');
 error_reporting(1);
include_once __DIR__ . '/../common.inc.php';
global $DB, $_GLOBALS;

$invoice_id = intval($_GET['id']);
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
    // $file_new  = "ss_" . $product_id . '_' .rand(1,10000) . '.' . $file_ext;
    $file_new  = "invoice_" . $invoice_id . '_' . time() . uniqid() . '.' . $file_ext;
    $dir       =  $_SERVER['DOCUMENT_ROOT'] . '/webadmin/uploads/invoice/attachment/';

    // Verify the directory exists and is writable
    if (!is_dir($dir)) {
        echo "Directory does not exist: " . htmlspecialchars($dir) . "<br>"; die;
    } elseif (!is_writable($dir)) {
        echo "Directory is not writable: " . htmlspecialchars($dir) . "<br>"; die;
    }
    if (move_uploaded_file($file_temp, $dir . $file_new)) {
        $DB->query(sprintf(
            "INSERT INTO %s (`invoice_id`, `staff_id`, `asset_rank`, `asset_date`, `file_name`, `file_type`,`file_orig`,`file_size`,`asset_caption`)VALUES (%d, %d,%d, %s,%s,%s,%s,%s,%s)",
            "`highwaygroup`.`invoice_assets`",
            $invoice_id,
            $_GLOBALS['staff']['id'],
            0,
            $DB->escape(date('Y-m-d h:i:s')),
             $DB->escape($file_new),
             $DB->escape($file_ext),
            $DB->escape($file_orig),
            intval($file_size),
            $DB->escape($file_orig),
        ));

        unset($file_orig);
        unset($file_new);
        unset($file_temp);
        unset($file_ext);
        unset($file_size);
        echo "Sucess, file has been saved successfully";
    } else {
        echo "Failed to move uploaded file.";
    }
} else {
    echo "No file uploaded.";
}