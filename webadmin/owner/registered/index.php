<?php
/**
 * @var Serial_Handler $section_handler
 * @var string         $section_name
 */
include_once __DIR__ . '/common.inc.php';

global $_GLOBALS;

$action = $_REQUEST['action'];
$id     = intval($_REQUEST['id']);

//Update Actions
switch ($action) {
    case 'do_search':
        $_GET   = array_merge($_GET, $_POST);
        $action = 'list';
        break; 
}

if (array_key_exists('action', $_GET)) {
    unset($_GET['action']);
}

//Other Actions
switch ($action) {
    default:
        $action = 'list';
        // no break
    case 'list':
        // no break
    case 'search':
    if (array_key_exists('id', $_GET)) {
            $section_handler->setLock($_GET['id'], $_GLOBALS['staff']['id']);
        }

        header('Location: ' . $action . '.php?' . http_build_query($_GET));
        break;
}
