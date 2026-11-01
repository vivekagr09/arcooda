<?php
include_once __DIR__ . '/wanted_common.php';

$action = $posted['action'];

//Processing Switch-case
switch ($_REQUEST['action']) {
    case 'do_edit':
        wantedToDatabase($_POST);
        $action = 'edit';
        break;

    case 'do_add':
        wantedToDatabase($_POST);
        $action = 'list';
        break;

    case 'activate':
        wantedChangeActiveStatus($posted['wanted_id'], 'y');
        unset($posted['wanted_id']);
        $action = 'list';
        break;

    case 'deactivate':
        wantedChangeActiveStatus($posted['wanted_id'], 'n');
        unset($posted['wanted_id']);
        $action = 'list';
        break;

    case 'delete':
        wantedDeleteFromDatabase($posted['wanted_id']);
        unset($posted['wanted_id']);
        $action = 'list';
        break;

    case 'clickReset':
        wantedSetValue($posted['wanted_id'], 'clicks', 0); //@todo create the field
        unset($posted['wanted_id']);
        $action = 'list';
        break;

    case 'viewReset':
        wantedSetValue($posted['wanted_id'], 'views', 0); //@todo create the field
        unset($posted['wanted_id']);
        $action = 'list';
        break;
}

//$get_vars = $posted;
$get_vars['action'] = $action;
$keep_only          = array_merge(array_keys(wantedGetOptionList('all_fields')), array('wanted_id', 'query', 'order_by', 'order', 'page', 'by', 'alpha_value', 'search_for', 'return_message'));
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
        header('Location: wanted_add.php?' . http_build_query($get_vars));
        break;

    case 'edit':
        header('Location: wanted_edit.php?' . http_build_query($get_vars));
        break;

    case 'select':
        header('Location: wanted_select.php?' . http_build_query($get_vars));
        break;

    case 'list':
    default:
        unset($get_vars['manual_id']);
        header('Location: wanted_list.php?' . http_build_query($get_vars));
}
