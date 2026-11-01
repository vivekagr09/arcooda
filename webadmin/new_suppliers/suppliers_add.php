<?php
include_once __DIR__ . '/suppliers_common.inc.php';

$search = true;
include_once __DIR__ . '/templates/suppliers_header.tpl.php';

$posted = array_merge($_GET, $_POST);

$supplier = $supplier_contacts = array();

$get_vars = $posted;
if (count($get_vars) > 0) {
    foreach ($get_vars as $key => $value) {
        if (is_array($value)) {
            unset($get_vars[$key]);
        }
    }
}

$link_id  = $posted['link_id'];
$supplier = ('' != $link_id) ? supplierFromDatabase($link_id) : array();
$sites    = explode(',', $supplier['site']);

$doedit_url           = $get_vars;
$doedit_url['action'] = 'do_add';
$add                  = true;
$doedit_url           = 'index.php?' . http_build_query($doedit_url);
?>
<form action="<?=$doedit_url?>" method="post" onsubmit="return checkSupplier();" id="addForm" name="addForm">
<?php
include_once __DIR__ . '/templates/edit_supplier.tpl.php';
?>
</form>
<?php

include_once __DIR__ . '/templates/suppliers_footer.tpl.php';
