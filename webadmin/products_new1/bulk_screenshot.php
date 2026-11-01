<?php
 ini_set('display_errors', 'On');
 error_reporting(0);
include_once __DIR__ . '/common.inc.php';
include_once __DIR__ . '/query.inc.php';
global $DB, $_GLOBALS;

$product_id = intval($_GET['id']);
// print_r($_FILES); die;
// Check if files were uploaded
if (isset($_FILES['myInputName']) && !empty($_FILES['myInputName']['tmp_name'])) {
    // Process the uploaded files
    $file_orig = $_FILES['myInputName']['name'];
    $file_temp = $_FILES['myInputName']['tmp_name'];
    $error     = $_FILES['myInputName']['error'];
    $file_ext = substr($file_orig, strrpos($file_orig, '.'));
    if (substr($file_ext, 0, 1) == '.') {
        $file_ext = substr($file_ext, 1);
    }
    $file_new  = "ss_" . $product_id . '_' .rand(1,10000) . '.' . $file_ext;
    $dir       = productGetOptionList('screenshot_path');

    // Verify the directory exists and is writable
    if (!is_dir($dir)) {
        echo "Directory does not exist: " . htmlspecialchars($dir) . "<br>"; die;
    } elseif (!is_writable($dir)) {
        echo "Directory is not writable: " . htmlspecialchars($dir) . "<br>"; die;
    }
    if (move_uploaded_file($file_temp, $dir . $file_new)) {
        // Prepare and execute the database query
        $DB->query(sprintf(
            "INSERT INTO `product_screenshots` (`id`, `file_orig`, `filename`, `caption`, `ss_type`, `active`, `created_by`, `created_at`, `date_added`) VALUES (%d, %s, %s, %d, %d, 'n', %d, NOW(), NOW())",
            $product_id,
            $DB->escape($file_orig),
            $DB->escape($file_new),
            $DB->escape($file_new),
            1,
            $_GLOBALS['staff']['id']
        ));
        unset($file_orig);
        unset($file_new);
        unset($file_temp);
        unset($file_ext);
        unset($file_ext);
        echo "Sucess, file has been saved successfully";
    } else {
        echo "Failed to move uploaded file.";
    }
} else {
    echo "No file uploaded.";
}