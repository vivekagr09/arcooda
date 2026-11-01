<?php
include_once __DIR__ . '/query.inc.php';

include_once $_SERVER['DOCUMENT_ROOT'] . '/webadmin/common.inc.php';
global $_GLOBALS;
moduleLoad('staff');
moduleLoad('asset');
moduleOverride('socialmedia', __DIR__ . '/query.inc.php');
moduleLoad('socialmedia');
