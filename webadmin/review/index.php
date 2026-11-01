<?php
include_once __DIR__ . '/common.inc.php';

$action = $_GET['action'] ?? 'list';
$if = 0;
if (array_key_exists('action', $_REQUEST)) {
    $action = $_REQUEST['action'];
}
if (array_key_exists('id', $_REQUEST)) {
	$id = $_REQUEST['id'];
}

$update_status = false;

//Update Actions
switch ($action) {
    case 'do_add':
        unset($_REQUEST['page']['id']);
    case 'do_edit':
        $posted = reviewFromPost($posted);
        $posted['item'] = $posted;
        $posted['action'] = 'do_edit';
        
        $reviewId = reviewToDatabase($posted);
        if($posted['action'] == 'do_edit' && isset($_POST['attachmentsremoved']) && count($_POST['attachmentsremoved']) > 0){
            foreach ($_POST['attachmentsremoved'] as $key => $id) {
                reviewGenericDelete($id, '`product_reviews_images`','id');
            }
        }
        $action = 'edit';
        break;
    case 'summary':
        $action = 'summary';
        break;
    case 'activate':
        reviewSetValue($_REQUEST['id'], 'status', 'y');
        $action = 'list';
        break;

    case 'deactivate':
        reviewSetValue($_REQUEST['id'], 'status', 'n');
        $action = 'list';
        break;
}

$keep_only          = array('id', 'page_id', 'return_message');
$posted             = array_merge($_GET, $_POST);
$get_vars           = $posted;
if (count($get_vars) > 0) {
    foreach ($get_vars as $key => $value) {
        if (!in_array($key, $keep_only)) {
            unset($get_vars[$key]);
        } elseif (is_array($value)) {
            $get_vars[$key] = implode('|', $value);
        }
    }
}

//Redirect Actions
switch ($action) {
    case 'add':
        unset($get_vars['id']);
        redirect('add.php?' . http_build_query($get_vars));
        break;
    case 'edit':
        redirect('edit.php?' . http_build_query($get_vars));
        break;
    case 'delete':
        redirect('delete.php?' . http_build_query($get_vars));
        break;
    case 'summary':
        redirect('summary.php?' . http_build_query($get_vars));
        break;
    case 'search':
        $get_vars['search_for'] = '';
    case 'list':
    default:
        unset($get_vars['id']);
        header('Location: list.php?' . http_build_query($get_vars));
}

die();
