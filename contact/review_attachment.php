<?php
 include_once __DIR__ . '/common.inc.php';
 moduleLoad('review'); 
 $product_id = intval($_GET['id']);
// print_r($_FILES); die;
// Check if files were uploaded
if (isset($_FILES['file'])) {
    $returnarray = [];
    foreach ($_FILES['file']['name'] as $key => $file) {
        // Process the uploaded files
        $file_orig = $_FILES['file']['name'][$key];
        $file_temp = $_FILES['file']['tmp_name'][$key];
        $error     = $_FILES['file']['error'][$key];
        $size      = intval($_FILES['file']['size'][$key]);
        $file_ext = substr($file_orig, strrpos($file_orig, '.'));
        if (substr($file_ext, 0, 1) == '.') {
            $file_ext = substr($file_ext, 1);
        }
        $file_new  = "ss_" . $product_id . '_' .rand(1,10000) . '.' . $file_ext;
        // Verify the directory exists and is writable
        if (move_uploaded_file($file_temp, $_SERVER['DOCUMENT_ROOT'] .'/images/review/' . $file_new)) {
                    $posted['file_orig'] = $file_orig;
                    $posted['name'] = $file_new;
                    $posted['filetype'] = $file_ext;
                    $posted['active'] = 'y';
                    $posted['size'] = $size;
                    unset($posted['id']);
                    $reviewScreenshot =  reviewGenericsFromPost($posted, '`product_reviews_images`');
                    $returnarray[] =   reviewGenericToDatabase($reviewScreenshot,'`product_reviews_images`','id');
        }
    }
    echo json_encode($returnarray);
} else {
    echo "No file uploaded.";
}