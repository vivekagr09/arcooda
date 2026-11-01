<?php
include_once __DIR__ . '/common.inc.php';

$id     = 0;
$action = 'list';
$vars   = $_GET;
if (array_key_exists('id', $vars)) {
    $id = intval($vars['id']);
    unset($vars['id']);
}
if (array_key_exists('action', $vars)) {
    $action = $vars['action'];
    unset($vars['action']);
}

switch ($action) {
case 'do_':
}

switch ($action) {
case 'list':
    redirect($action . '.php?' . http_build_query($vars));
    die();
}
