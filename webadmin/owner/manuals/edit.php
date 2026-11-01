<?php
include_once __DIR__ . "/common.inc.php";

include_once __DIR__ . "/header.inc.php";
?>
<link rel="stylesheet" type="text/css" href="style.css" />
<?php
$id = intval($posted['id']);
$manual = manualFromDatabase($id);
$manual_parts_product=getProductmanualData($id , 1);
$manual_machine_product=getProductmanualData($id , 2);
$manual_software_product = getProductmanualData($id , 3);
$manual_merchandise_product=getProductmanualData($id , 4);
if(!$manual)
	die("Page with that ID doesn't exist");
$get_vars=$posted;
if(count($get_vars)>0)
	foreach($get_vars as $key=>$value)
		if(is_array($value))
			unset($get_vars[$key]);

$doedit_url=$get_vars;
$doedit_url['action']='do_edit';
$doedit_url='.?'.http_build_query($doedit_url);

if($posted['changes'] != '') {
?>
	<center><span style="color:red">Following Changes Made :
	<br/><br /><?= nl2br($posted['changes'])?></span></center>
<?php } ?>
<form action="<?= $doedit_url ?>" method="post" enctype="multipart/form-data">
<?php
include __DIR__ . "/addedit.tpl.php";
?>
</form>
<?php

include_once __DIR__ . "/footer.inc.php";
?>