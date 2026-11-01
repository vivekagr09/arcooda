<?php
include_once __DIR__ . "/common.inc.php";

$action=$posted['action'];

//Processing Switch-case
switch($action)
{
case 'do_edit':
	if (array_key_exists('file_name', $_FILES) && $_FILES['file_name']['error'] == UPLOAD_ERR_OK) {
		$file_orig = $_FILES['file_name']['name'];
	    $file_temp = $_FILES['file_name']['tmp_name'];
	    $error     = $_FILES['file_name']['error'];
	    $size      = intval($_FILES['file_name']['size']);
	    $file_ext  = substr($file_orig, strrpos($file_orig, '.'));
	    if (substr($file_ext, 0, 1) == '.') {
	        $file_ext = substr($file_ext, 1);
	    }
	    $file_new  = "blog_" . rand() . '_' . time() . '.' . $file_ext;
        $dir       = $_SERVER['DOCUMENT_ROOT'] . '/webadmin/assets/owner/firmware/';
        if (move_uploaded_file($file_temp, $dir . $file_new)) {
        	$posted['firmware']['file_name']=$file_new;
        	$posted['firmware']['file_orig']=$file_orig;        
        }
	}
	$content = firmwareToDatabase($posted);
	$firmwareid=($posted['id'])?$posted['id']:$content;
	global $DB;
    //echo $firmwareid; die;
	if(array_key_exists('item_addon', $posted) && count($posted['item_addon'])){
		foreach ($posted['item_addon'] as $key => $product) {
			if(empty($product['id'])){
				firmwareproductToDatabase($firmwareid,$product);
			}	
		}
	}
	if(array_key_exists('add_on_deleted', $posted) && !empty($posted['add_on_deleted'])){
		foreach (explode(',', $posted['add_on_deleted']) as $key => $id) { 
			if($id){
			 	deleteProductfirmware($id);
			}
		}
	}
	if(array_key_exists('admin_file', $posted) && !empty($posted['admin_file'])){
		foreach($posted['admin_file'] as $key => $files) {
			if(!empty($files)){
				updateAdminFiles($files);
			}	
		}
	}
	if(array_key_exists('admin_files_deleted', $posted) && !empty($posted['admin_files_deleted'])){
		foreach (explode(',', $posted['admin_files_deleted']) as $key => $id) { 
			if($id){
			 	deleteAdminFiles($id);
			}
		}
	}
	if($content != '')
		$posted['changes'] = $content;
	$action = 'edit';
	break;
case 'do_add':
	if (array_key_exists('file_name', $_FILES) && $_FILES['file_name']['error'] == UPLOAD_ERR_OK) {
		$file_orig = $_FILES['file_name']['name'];
	    $file_temp = $_FILES['file_name']['tmp_name'];
	    $error     = $_FILES['file_name']['error'];
	    $size      = intval($_FILES['file_name']['size']);
	    $file_ext  = substr($file_orig, strrpos($file_orig, '.'));
	    if (substr($file_ext, 0, 1) == '.') {
	        $file_ext = substr($file_ext, 1);
	    }
	    $file_new  = "blog_" . rand() . '_' . time() . '.' . $file_ext;
        $dir       = $_SERVER['DOCUMENT_ROOT'] . '/webadmin/assets/owner/firmware/';
        if (move_uploaded_file($file_temp, $dir . $file_new)) {
        	$posted['firmware']['file_name']=$file_new;
        	$posted['firmware']['file_orig']=$file_orig;        
        }
	}
	firmwareToDatabase($posted);
	//echo $firmwareid; die;
	if(array_key_exists('item_addon', $posted) && count($posted['item_addon'])){
		foreach ($posted['item_addon'] as $key => $product) {
			if(empty($product['id'])){
				firmwareproductToDatabase($firmwareid,$product);
			}	
		}
	}
	if(array_key_exists('add_on_deleted', $posted) && !empty($posted['add_on_deleted'])){
		foreach (explode(',', $posted['add_on_deleted']) as $key => $id) {
			if($id){
					deleteProductfirmware($id);
			}
		}
	}
	$action = 'list';
	break;
case 'delete':
	firmwareDeleteFromDatabase($posted['id']);
	$action = 'list';
	break;
}

$get_vars=$posted;
$keep_only=array('id','query','order_by','order','page','by','alpha_value', 'changes');
if(count($get_vars)>0) {
	foreach($get_vars as $key=>$value) {
		if(is_array($value) || !in_array($key,$keep_only))
			unset($get_vars[$key]);
	}
}

//Redirect Actions
switch($action)
{
case 'add':
	redirect('add.php?'.http_build_query($get_vars));
	break;
case 'edit':
	redirect('edit.php?'.http_build_query($get_vars));
	break;
case 'view':
	redirect('view.php?'.http_build_query($get_vars));
	break;
case 'search':
	redirect('search.php?'.http_build_query($get_vars));
	break;
case 'list':
default:
	redirect('list.php?'.http_build_query($get_vars));
}
?>