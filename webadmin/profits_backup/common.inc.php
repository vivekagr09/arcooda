<?php
include_once __DIR__ . '/query.inc.php';

/**
 * @var mixed[] $posted
 */
include_once $_SERVER['DOCUMENT_ROOT'] . '/webadmin/common.inc.php';

global $_GLOBALS;

moduleOverride('profit', __DIR__ . '/query.inc.php');
moduleLoad('profit');

moduleLoad('invoice');
moduleLoad('product');

//Restrict to current website if not highwaygroup.net
if (!in_array($_GLOBALS["site_settings"]["site_id"], array(11, 43))) {
    $posted['site_id'] = $_GET['site_id'] = intval($_GLOBALS['site_settings']['site_id']);
}

$get_vars = $posted;
if (count($get_vars) > 0) {
    foreach ($get_vars as $key => $value) {
        if (is_array($value)) {
            unset($get_vars[$key]);
        }
    }
}

/*
$all_staff_list=array();
$active_staff=array();
$inactive_staff=array();

$query=$DB->query("SELECT user,id,CONCAT_WS(' ',first_name,last_name) AS name,active FROM `highwaygroup`.`users` ORDER BY user");
while ($row=$DB->fetch_assoc($query))
{
$all_staff_list[$row['user']]=$row['name'];
if ($row['active']=='y')
$active_staff[$row['user']]=$row['name'];
else
$inactive_staff[$row['user']]=$row['name'];
}
 */
