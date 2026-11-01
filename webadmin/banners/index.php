<?php
include_once __DIR__ . '/common.inc.php';

$action = $_REQUEST['action'];

//Processing Switch-case
switch ($action) {
    case 'do_edit':
        bannerToDatabase($_REQUEST);
        $action = 'edit';
        break;

    case 'do_add':
        bannerToDatabase($_REQUEST);
        $action = 'list';
        break;

    case 'activate':
        bannerChangeActiveStatus($_REQUEST['id'], 'y');
        unset($_REQUEST['id']);
        $action = 'list';
        break;

    case 'deactivate':
        bannerChangeActiveStatus($_REQUEST['id'], 'n');
        unset($_REQUEST['id']);
        $action = 'list';
        break;

    case 'delete':
        bannerDeleteFromDatabase($_REQUEST['id']);
        unset($_REQUEST['id']);
        $action = 'list';
        break;

    case 'clickReset':
        bannerClicksReset($_REQUEST['id']);
        unset($_REQUEST['id']);
        $action = 'list';
        break;

    case 'viewReset':
        bannerViewsReset($_REQUEST['id']);
        unset($_REQUEST['id']);
        $action = 'list';
        break;
}

$get_vars['action'] = $action;
$keep_only          = array_merge(array_keys(bannerGetOptionList('all_fields')), array('id', 'query', 'order_by', 'order', 'page', 'by', 'alpha_value', 'search_for', 'return_message'));
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
        header('Location: add.php?' . http_build_query($get_vars));
        break;

    case 'edit':
        header('Location: edit.php?' . http_build_query($get_vars));
        break;

    case 'select':
        header('Location: select.php?' . http_build_query($get_vars));
        break;

    case 'list':
    default:
        unset($get_vars['manual_id']);
        header('Location: list.php?' . http_build_query($get_vars));
}
