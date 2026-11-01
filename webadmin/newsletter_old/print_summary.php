<?php
include_once __DIR__ . '/common.inc.php';

$_GET['return_message'] = 'popup';
include_once __DIR__ . '/header.inc.php';

$action   = 'statistics';
$id       = intval($posted['id']);
$staff_id = $_GLOBALS['staff']['id'];
$site_id  = $_GLOBALS['site_settings']['site_id'];

$item = $section_handler->fromDatabase($id);
?>
<h2>Newsletter Statistics - <?=date('d/m/Y', time())?></h2>
<?php

include __DIR__ . '/summary_information.tpl.php';

$_GET['return_message'] = 'popup';
include_once __DIR__ . '/footer.inc.php';
