<?php
return;

include_once('suppliers_common.php');
include_once('templates/suppliers_header.tpl.php');

$supplier_id=intval($posted['supplier_id']);
if(!$supplier_id) die('Invalid ID passed');

$supplier_emails=array_reverse(supplierChangesFromDatabase($supplier_id,'email'));
echo '<div align="center">'.formatHTMLTitle('Email History');
if(count($supplier_emails)) include('templates/list_email_history.tpl.php');
else echo 'No Results Found...';
echo '</div>';
include_once('templates/suppliers_footer.tpl.php');

?>