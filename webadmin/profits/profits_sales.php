<?php
/**
 * @var mixed[] $posted
 * @var mixed[] $get_vars
 */
include_once __DIR__ . '/common.inc.php';

/*
include_once __DIR__ . '/templates/profits_header.tpl.php';
*/

$type = $posted['type'];
$page = $posted['page'];
if ('' == $page) {
    $page = 0;
}

if ('' == $type) {
    $type = 'monthly';
}

$list = array();
$name = 'Sales Breakup - ';
switch ($type) {
    case 'weekly':
        redirect('profits_sales_weekly.php?' . http_build_query($get_vars));
        die();

    case 'monthly':
        redirect('profits_sales_monthly.php?' . http_build_query($get_vars));
        die();

    case 'yearly':
        redirect('profits_sales_yearly.php?' . http_build_query($get_vars));
        die();

    case 'quarterly':
        redirect('profits_sales_quarterly.php?' . http_build_query($get_vars));
        die();
}
