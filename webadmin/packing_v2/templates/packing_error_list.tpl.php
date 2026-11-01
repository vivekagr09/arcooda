<?php
$alt=false;

if(count($error_list))
{
?>
<!-- packing_error_list.tpl.php -->
<form accept-charset="utf-8" target="_blank" action="packing_errors.php" method="post">

<input type="hidden" name="error_list" value="b64_gz_ser:<?= base64_encode(gzcompress(serialize($error_list))) ?>" />

<table class="wide" align="center">
	<tr><th>Problems found:</th></tr>
<?php
	foreach($error_list as $error)
	{
?>
	<tr><td><?= nl2br(formatHTMLSafe($error[2])) ?></td></tr>
<?php
	}
?>
	<tr><td class="left_title" style="text-align:center;"><input type="submit" value="Send Errors to Product Manager" /></td></tr>
</table>

</form>

<br /><br />

<!-- /packing_error_list.tpl.php -->
<?php
}
?>