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
        $dir       = $_SERVER['DOCUMENT_ROOT'] . '/webadmin/assets/fileassets';
        if (move_uploaded_file($file_temp, $dir . $file_new)) {
        	$posted['assets']['file_name']=$file_new;
        	$posted['assets']['file_orig']=$file_orig; 
        	$posted['assets']['filetype']=$file_ext; 
        	$posted['assets']['size']=$size;  
        	$posted['assets']['slug']=md5($file_new);        
        }
	}
	$content = fileAssetsToDatabase($posted);
	$assetsid=($posted['id'])?$posted['id']:$content;
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
	    $file_new  = "assets_" . rand() . '_' . time() . '.' . $file_ext;
        $dir       = $_SERVER['DOCUMENT_ROOT'] . '/webadmin/assets/fileassets/';
        if (move_uploaded_file($file_temp, $dir . $file_new)) {
        	$posted['assets']['file_name']=$file_new;
        	$posted['assets']['file_orig']=$file_orig; 
        	$posted['assets']['filetype']=$file_ext; 
        	$posted['assets']['size']=$size;  
        	$posted['assets']['slug']=md5($file_new);         
        }
	}
	$firmwareid=fileAssetsToDatabase($posted);
	$action = 'list';
	break;
case 'delete':
	fileAssetsFromDatabase($posted['id']);
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