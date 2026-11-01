<?php
include_once ('wanted_common.php');
$search = true;
include_once ('templates/wanted_header.tpl.php');
$get_vars = $posted;
if (count($get_vars) > 0) foreach($get_vars as $key => $value) if (is_array($value)) unset($get_vars[$key]);
$doedit_url = $get_vars;
$doedit_url['action'] = 'do_add';
$action='add';
$add = true;
$doedit_url = 'index.php?' . http_build_query($doedit_url);
$wanted = array();
$member = array();
?>
<link rel="stylesheet" type="text/css" href="style.css" />
<form action="<?= $doedit_url
?>" method="post">
<?php
include_once ('templates/addedit_wanted.tpl.php');
?>
</form>
<?php
include_once ('templates/wanted_footer.tpl.php');
?>