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
    case 'delete' :
        global $DB;
        $DB->query(sprintf(
        "UPDATE  `highwaygroup`.`serial_numbers` set `product_id`=0,`member_id`=0,`invoice_id`=0,`product_str`=''  WHERE `id` = %d LIMIT 1",
        $_GET['id']
        ));
    case 'do_add':
        if (!array_key_exists($section_name, $_REQUEST) || !is_array($_REQUEST[$section_name])) {
            break;
        }

        $_REQUEST[$section_name]['id']       = 0;
        $_REQUEST[$section_name]['staff_id'] = $_GLOBALS['staff']['id'];
        $_REQUEST[$section_name]['created']  = formatDateTime();

        //no break
    case 'do_edit':
        if (!array_key_exists($section_name, $_REQUEST) || !is_array($_REQUEST[$section_name])) {
            break;
        }

        $_REQUEST[$section_name]['modified'] = formatDateTime();

        $new_item       = $section_handler->fromPost($_REQUEST[$section_name]);
        $old_item       = $section_handler->fromDatabase($new_item['id']);
        $new_item['id'] = $_GET['id'] = $section_handler->toDatabase($new_item);

        $delta = [];
        foreach ($new_item as $k => $v) {
            if (!is_array($v) && !is_array($old_item[$k]) && $old_item[$k] != $v) {
                $delta[] = $k . ' changed from: ' . json_encode($old_item[$k]) . ' to: ' . json_encode($v);
            }
        }

        if (count($delta)) {
            $section_handler->commentToDatabase($new_item['id'], "Changes Made: \n" . implode("\n", $delta), 'sql');
        }

        $action = 'edit';

        if (is_array($_REQUEST[$section_name]['serial_id2'])) {
            foreach ($_REQUEST[$section_name]['serial_id2'] as $v) {
                $_REQUEST[$section_name]['serial_id'] = $v;
                $item                                 = $section_handler->fromPost($_REQUEST[$section_name]);
                unset($item['id']);
                $section_handler->toDatabase($item);
            }
            unset($_GET['id']);
            $action = 'list';
        }
        break;

    case 'unlock':
        $section_handler->setLock($id);
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
        // no break
    case 'add':
        // no break
    case 'print':
        // no break
    case 'export':
        if (array_key_exists('id', $_GET)) {
            unset($_GET['id']);
        }
        // no break
    case 'edit':
        if (array_key_exists('id', $_GET)) {
            $section_handler->setLock($_GET['id'], $_GLOBALS['staff']['id']);
        }

        header('Location: ' . $action . '.php?' . http_build_query($_GET));
        break;
}
