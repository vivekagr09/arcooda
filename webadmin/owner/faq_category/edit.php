<?php
include_once __DIR__ . "/common.inc.php";

include_once __DIR__ . "/header.inc.php";
?>
<link rel="stylesheet" type="text/css" href="style.css" />
<?php
$id = intval($posted['id']);
$faqcategory = manualFromDatabase($id);
$product_list=getProductFromdatabase();
$manual_product=getProductmanual($id);
if(!$faqcategory)
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