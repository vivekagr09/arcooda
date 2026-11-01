<?php
global $_GLOBALS;

$allowed_sections = get_allowed_sections($_GLOBALS['staff']['user']);

$website_list = getWebsitesList();

$staff_id = $_GLOBALS['staff']['id'];

$get_vars = $posted;
if (count($get_vars) > 0) {
    foreach ($get_vars as $key => $value) {
        if (is_array($value)) {
            unset($get_vars[$key]);
        }
    }
}
