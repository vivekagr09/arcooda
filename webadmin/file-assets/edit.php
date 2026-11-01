<?php
include_once __DIR__ . "/common.inc.php";

include_once __DIR__ . "/header.inc.php";
?>
<link rel="stylesheet" type="text/css" href="style.css" />
<?php
$doedit_url['action']='do_edit';
$doedit_url='.?'.http_build_query($doedit_url);
$id = intval($posted['id']);
$assets = fileAssetsFromDatabase($id);
?>
<form action="<?= $doedit_url ?>" method="post" enctype="multipart/form-data">
<?php
include __DIR__ . "/addedit.tpl.php";
?>
</form>
<?php

include_once __DIR__ . "/footer.inc.php";
?>