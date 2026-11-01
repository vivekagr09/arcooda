<!-- wanted_header.tpl -->

<?php
if (!$popup_window)
{
	include_once ($_SERVER['DOCUMENT_ROOT'] . '/webadmin/header.inc.php');

	$row = $DB->query_first("SELECT SUM(IF(`active` = 'yes', 1, 0)) AS `active`, COUNT(*) AS `total` FROM `wanted`");
?>
<table border="0" cellpadding="2" cellspacing="2" width="100%"><tr>
	<td class="pageMenu" width="20%"><div align="center"><b>Wanted</b></div></td>
	<td class="pageMenu" style="text-align:right;" width="80%">
		<a href="index.php?action=add">Add New Wanted</a> |
		<!--<a href="index.php?action=search">Search Products</a> | -->
		<a href="index.php?action=list&amp;active=yes">List Active Wanted (<?= $row['active'] ?>)</a> |
		<a href="index.php?action=list">List All Wanted (<?= $row['total'] ?>)</a>
	</td>
</tr></table>
<?php
}
?>
<script type="text/javascript">
//<![CDATA[
<?php
include ("wanted_funcs.js");
?>
//]]>
</script>
<div class="wanted_content" style="width:100%;">
	<br />

<!-- /wanted_header.tpl -->
