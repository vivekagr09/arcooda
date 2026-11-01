<?php
return;

include_once('suppliers_common.php');
$search=true;
include_once('templates/suppliers_header.tpl.php');

$posted = array_merge($_GET, $_POST);

$supplier_id=intval($posted['supplier_id']);
$supplier=supplierFromDatabase($supplier_id);
$supplier_contacts=supplierContactsFromDatabase($supplier_id);

$sites = explode(',',$supplier['site']);

//unset($posted['link_id']);

//print_array($prefs);

$get_vars=$posted;
if(count($get_vars)>0)
	foreach($get_vars as $key=>$value)
		if(is_array($value))
			unset($get_vars[$key]);

$doedit_url=$get_vars;
$doedit_url['action']='do_edit';
$doedit_url='index.php?'.http_build_query($doedit_url);
if(supplierExists($supplier_id)) {
?>
<form action="<?= $doedit_url ?>" method="post" enctype="multipart/form-data">
<?php
include_once('templates/addedit_suppliers.tpl.php');
?>
</form>
<?php
}
else
	die("Supplier with that ID doesn't exist.");

include_once('templates/suppliers_footer.tpl.php');
?>