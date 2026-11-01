<?php
$alt=false;

if(count($error_list))
{
?>
<!-- packing_error_list.tpl.php -->

<input type="hidden" name="error_list" value="b64_gz_ser:<?= base64_encode(gzcompress(serialize($error_list))) ?>" />

<table class="wide" align="center">
	<tr><th>Problems found:</th></tr>
<?php
	foreach($error_list as $error)
	{
?>
	<tr><td><?= nl2br(htmlspecialchars($error[2])) ?></td></tr>
<?php
	}
?>
	<tr><td class="left_title" style="text-align:center;"><input style="background-color:yellow;" type="submit" value="Send Errors to Product Manager" /></td></tr>
</table>

<br /><br />

<!-- /packing_error_list.tpl.php -->
<?php
}
