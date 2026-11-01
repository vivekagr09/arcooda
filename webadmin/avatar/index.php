<?php
include_once __DIR__ . '/common.inc.php';


switch ($_REQUEST['action'] ?? '') {
    case 'do_add':
        //no break
    case 'do_edit':
        break;

    case 'do_activate':
        break;
}

switch ($_GET['action'] ?? '') {
    default:
        //no break
    case 'list':
        redirect('list.php?' . http_build_query($_GET));
        die();

    case 'add':
        redirect('add.php?' . http_build_query($_GET));
        die();

    case 'edit':
        redirect('edit.php?' . http_build_query($_GET));
        die();
}
