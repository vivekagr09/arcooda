<?php

require "../includes/init.php";
$page->requirePermission('admin-access');

$resolverName = 'quickstart-resolve-sitemap';
$redirectTo = '../content/files/sitemap.php';

$studio->setopt($resolverName, 'On');
header("Location: $redirectTo");
die;

?>
