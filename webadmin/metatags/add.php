<?php
include_once __DIR__ . "/common.inc.php";

include_once __DIR__ . "/header.inc.php";

$search = true;
$get_vars = $posted;
if (count($get_vars) > 0) foreach($get_vars as $key => $value) if (is_array($value)) unset($get_vars[$key]);

$doedit_url=$get_vars;
$doedit_url['action']='do_add';
$add = true;
$doedit_url='.?'.http_build_query($doedit_url);
?>
<link rel="stylesheet" type="text/css" href="style.css" />
<form action="<?= $doedit_url ?>" method="post">
<?php
include __DIR__ . "/addedit.tpl.php";
?>
</form>
<?php

include_once __DIR__ . "/footer.inc.php";
?>