<?php
include_once ('wanted_common.php');
include_once ('templates/wanted_header.tpl.php');
?>
<link rel="stylesheet" type="text/css" href="style.css" />
<?php
$id = intval($posted['wanted_id']);
$wanted = wantedFromDatabase($id);
$wanted_offers=wantedOferDromDataBase($wanted['id']);
$action='edit';
//print_array($wanted);
if (!$wanted) die("Wanted with that ID doesn't exist");
$get_vars = $posted;
if (count($get_vars) > 0) foreach($get_vars as $key => $value) if (is_array($value)) unset($get_vars[$key]);
$doedit_url = $get_vars;
$doedit_url['action'] = 'do_edit';
$doedit_url = 'index.php?' . http_build_query($doedit_url);
if ($posted['changes'] != '')
{
?>
	<center><span style="color:red">Following Changes Made :
	<br/><br /><?= nl2br($posted['changes']) ?></span></center>
<?php
} ?>
<form action="<?= $doedit_url
?>" method="post">
<?php
include_once ('templates/addedit_wanted.tpl.php');
?>
</form>
<?php
include_once ('templates/wanted_footer.tpl.php');
?>