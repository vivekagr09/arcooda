<?php
 ini_set('display_errors', 'On');
 error_reporting(1);
include_once __DIR__ . '/common.inc.php';
include_once __DIR__ . '/query.inc.php';
global $DB, $_GLOBALS;

$product_id = intval($_GET['id']);
$sub_id = intval($_GET['sub_id']);
$type = intval($_GET['type']);
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
    $file_new  = "ss_" . $product_id . '_' . time() . '_' . uniqid() . '.' . $file_ext;
    $dir       = productGetOptionList('screenshot_path');

    // Verify the directory exists and is writable
    if (!is_dir($dir)) {
        echo "Directory does not exist: " . htmlspecialchars($dir) . "<br>"; die;
    } elseif (!is_writable($dir)) {
        echo "Directory is not writable: " . htmlspecialchars($dir) . "<br>"; die;
    }
    if (move_uploaded_file($file_temp, $dir . $file_new)) {
        $DB->query(sprintf(
            "INSERT INTO %s (`id`, `caption`, `ss_type`, `file_orig`, `created_by`, `filename`, `date_added`) VALUES (%d, %s,%d, %s,%s,%s, NOW())",
            "`arcooda_www`.`product_screenshots`",
            $product_id,
            formatSQLString(pathinfo($file_orig, PATHINFO_FILENAME)),
            $type,
            formatSQLString($file_orig),
            formatSQLString($_GLOBALS['staff']['user']),
            formatSQLString($file_new),
            formatSQLString($file_ext),
            intval($file_size),
            0,
            formatSQLString(''),
            0
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