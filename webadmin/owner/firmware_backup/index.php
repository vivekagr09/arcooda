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
	$firmware_product=getProductfirmware($firmwareid);
	$deleted_product=[];
	if(array_key_exists('products', $posted) && count($posted['products'])){
	$deleted_product = array_diff($firmware_product, $posted['products']); 
	}else{
			$deleted_product=$firmware_product;
	} 
	if(count($deleted_product)){
		foreach ($deleted_product as $key => $product) {
				deleteProductfirmware($firmwareid,$product);
		}
	}
	if(array_key_exists('products', $posted) && count($posted['products'])){
		foreach ($posted['products'] as $key => $product) {
			if(!in_array($product, $firmware_product)){
				firmwareproductToDatabase($firmwareid,$product);
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
	$firmwareid=firmwareToDatabase($posted);
	if(array_key_exists('products', $posted) && count($posted['products'])){
		foreach ($posted['products'] as $key => $product) {
			firmwareproductToDatabase($firmwareid,$product);
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