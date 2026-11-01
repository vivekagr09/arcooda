<?php
include_once __DIR__ . "/common.inc.php";

$action=$posted['action'];


//Processing Switch-case
switch($action)
{
case 'do_edit':
	if (array_key_exists('item_thumb', $_FILES) && $_FILES['item_thumb']['error'] == UPLOAD_ERR_OK) {
		$file_orig = $_FILES['item_thumb']['name'];
	    $file_temp = $_FILES['item_thumb']['tmp_name'];
	    $error     = $_FILES['item_thumb']['error'];
	    $size      = intval($_FILES['item_thumb']['size']);
	    $file_ext  = substr($file_orig, strrpos($file_orig, '.'));
	    if (substr($file_ext, 0, 1) == '.') {
	        $file_ext = substr($file_ext, 1);
	    }
	    $file_new  = "blog_" . rand() . '_' . time() . '.' . $file_ext;
        $dir       = $_SERVER['DOCUMENT_ROOT'] . '/webadmin/assets/owner/blog/';
        if (move_uploaded_file($file_temp, $dir . $file_new)) {
        	$posted['blog']['thumb_url']=$file_new;
        }

	}
	if (array_key_exists('header_image', $_FILES) && $_FILES['header_image']['error'] == UPLOAD_ERR_OK) {
		$file_orig = $_FILES['header_image']['name'];
	    $file_temp = $_FILES['header_image']['tmp_name'];
	    $error     = $_FILES['header_image']['error'];
	    $size      = intval($_FILES['header_image']['size']);
	    $file_ext  = substr($file_orig, strrpos($file_orig, '.'));
	    if (substr($file_ext, 0, 1) == '.') {
	        $file_ext = substr($file_ext, 1);
	    }
	    $file_new  = "blog_" . rand() . '_' . time() . '.' . $file_ext;
        $dir       = $_SERVER['DOCUMENT_ROOT'] . '/webadmin/assets/owner/blog/';
        if (move_uploaded_file($file_temp, $dir . $file_new)) {
        	$posted['blog']['header_image']=$file_new;
        }
	}
	$content = blogToDatabase($posted);
	if($content != '')
		$posted['changes'] = $content;
	$action = 'edit';
	break;
case 'do_add':
	if (array_key_exists('item_thumb', $_FILES) && $_FILES['item_thumb']['error'] == UPLOAD_ERR_OK) {
		$file_orig = $_FILES['item_thumb']['name'];
	    $file_temp = $_FILES['item_thumb']['tmp_name'];
	    $error     = $_FILES['item_thumb']['error'];
	    $size      = intval($_FILES['item_thumb']['size']);
	    $file_ext  = substr($file_orig, strrpos($file_orig, '.'));
	    if (substr($file_ext, 0, 1) == '.') {
	        $file_ext = substr($file_ext, 1);
	    }
	    $file_new  = "blog_" . rand() . '_' . time() . '.' . $file_ext;
        $dir       = $_SERVER['DOCUMENT_ROOT'] . '/webadmin/assets/owner/blog/';
        if (move_uploaded_file($file_temp, $dir . $file_new)) {
        	$posted['blog']['thumb_url']=$file_new;
        }

	}
	if (array_key_exists('header_image', $_FILES) && $_FILES['header_image']['error'] == UPLOAD_ERR_OK) {
		$file_orig = $_FILES['header_image']['name'];
	    $file_temp = $_FILES['header_image']['tmp_name'];
	    $error     = $_FILES['header_image']['error'];
	    $size      = intval($_FILES['header_image']['size']);
	    $file_ext  = substr($file_orig, strrpos($file_orig, '.'));
	    if (substr($file_ext, 0, 1) == '.') {
	        $file_ext = substr($file_ext, 1);
	    }
	    $file_new  = "blog_" . rand() . '_' . time() . '.' . $file_ext;
        $dir       = $_SERVER['DOCUMENT_ROOT'] . '/webadmin/assets/owner/blog/';
        if (move_uploaded_file($file_temp, $dir . $file_new)) {
        	$posted['blog']['header_image']=$file_new;
        }
	}
	blogToDatabase($posted);
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