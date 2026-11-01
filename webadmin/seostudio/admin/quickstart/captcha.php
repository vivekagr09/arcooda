<?php

require "../includes/init.php";
$page->requirePermission('admin-access');

$resolverName = 'quickstart-resolve-captcha';
$redirectTo = '../settings/ratelimit.php';

$studio->setopt($resolverName, 'On');
header("Location: $redirectTo");
die;

?>
