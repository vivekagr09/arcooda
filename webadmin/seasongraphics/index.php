<?php
include_once __DIR__ . "/common.inc.php";

$action = $posted['action'];

//Processing Switch-case
switch ($action) {
    case 'do_edit':
        $content = graphicsToDatabase($posted);
        if ('' != $content) {
            $posted['changes'] = $content;
        }

        $action = 'edit';
        break;

    case 'do_add':
        graphicsToDatabase($posted);
        $action = 'list';
        break;

    case 'delete':
        graphicsDeleteFromDatabase($posted['meta_id']);
        $action = 'list';
        break;

    case 'do_toggle':
        $id = $posted['id'] ?? 0;
        graphicsSetValue($id, 'status', (graphicsHasValue($id, 'status', 'y')) ? 'n' : 'y');
        $action = 'list';
        break;
}

$get_vars  = $posted;
$keep_only = array('id', 'query', 'order_by', 'order', 'page', 'by', 'alpha_value', 'changes');
if (count($get_vars) > 0) {
    foreach ($get_vars as $key => $value) {
        if (is_array($value) || !in_array($key, $keep_only)) {
            unset($get_vars[$key]);
        }

    }
}

//Redirect Actions
switch ($action) {
    case 'add':
        redirect('add.php?' . http_build_query($get_vars));
        break;
    case 'edit':
        redirect('edit.php?' . http_build_query($get_vars));
        break;
    case 'view':
        redirect('view.php?' . http_build_query($get_vars));
        break;
    case 'search':
        redirect('search.php?' . http_build_query($get_vars));
        break;
    case 'list':
    default:
        redirect('list.php?' . http_build_query($get_vars));
}
