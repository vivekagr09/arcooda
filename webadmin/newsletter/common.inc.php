<?php
include_once('query.inc.php');

include_once($_SERVER['DOCUMENT_ROOT'].'/webadmin/common.inc.php');
global $_GLOBALS;
moduleLoad('staff');
$allowed_sections = get_allowed_sections($_GLOBALS['staff']['user']);
if (!has_permission('NEWSLETTER', $allowed_sections)) {
    redirect('/webadmin');
}
$section_handler=new Newsletter_Handler();
$section_name=$section_handler->section_name;

moduleLoad("member");
?>