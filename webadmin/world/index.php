<?php
include_once __DIR__ . "/common.inc.php";

global $_GLOBALS;

$allowed_sections = get_allowed_sections($_GLOBALS['staff']['user']);

if (has_permission('WORLD', $allowed_sections)) {
    // All the function definitions can go here....
    $fields['country']    = 'center';
    $fields['capital']    = 'center';
    $fields['time']       = 'center';
    $fields['population'] = 'center';
    $fields['languages']  = 'center';
    $limit                = 50; // max countries per page...

    // include the functions file here....
    include 'world_functions.php';

    write_global_JS_funcs();
    // if-else scenario
    $code = $_REQUEST['code'];
    if ("" == $code) {
        if ("summary" != $action) {
            include_once 'header.php';
            draw_reports();
            draw_searchbox();
            display_countries();
        }
    } else {
        $action = $_REQUEST['action'];
        if ("summary" != $action) {
            include_once 'header.php';
            echo '<link rel="stylesheet" type="text/css" href="style.css" />';
        }

        if (has_permission('WORLD2', $allowed_sections)) {
            if ('' == $action) {
                display_country();
            } elseif ('do_edit' == $action) {
                do_edit();
            } elseif ('delete' == $action) {
                do_delete();
            } elseif ('delete_image' == $action) {
                delete_image();
            } elseif ('summary' == $action) {
                view_summary();
            }

        } else {
            if ('summary' == $action) {
                view_summary();
            } else {
                echo "You don't have permission to edit";
            }

        }
    }
}
