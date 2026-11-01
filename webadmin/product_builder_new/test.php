<?php
include_once __DIR__ . '/common.inc.php';

$pb = new ProductBuilder($DB);
echo "\ngetBuilders:\n";
var_export($pb->getBuilders());
echo "\ngetBuilderIdByWebsite:\n";
var_export($pb->getBuilderIdByWebsite('https://www.arcade-game-sales.com/builder/test'));
echo "\ngetOptions:\n";
var_export($pb->getOptions());
echo "\nsearchBuildersByName:\n";
var_export($pb->searchBuildersByName('machine'));
echo "\nsearchOptionsByName:\n";
var_export($pb->searchOptionsByName('panel'));
echo "\ngetBuilderById:\n";
var_export($pb->getBuilderById('0d9aca0e290a72f2ab53f8784822302e'));
echo "\ngetOptionById:\n";
var_export($pb->getOptionById('cb091f03c3617682defba6f3b47a8340'));
echo "\ngetCategoryNameById:\n";
var_export($pb->getCategoryNameById(2));
echo "\ngetAllCategoryNames:\n";
var_export($pb->getAllCategoryNames());
echo "\nisBuilderExist:\n";
var_export($pb->isBuilderExist('0d9aca0e290a72f2ab53f8784822302e'));
die();
