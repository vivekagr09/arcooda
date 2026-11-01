<?php
include_once __DIR__ . '/common.inc.php';

global $DB;
global $popup_window;

register_shutdown_function(function ($a) {include_once $a;}, __DIR__ . '/footer.inc.php');

if (!$popup_window) {
    include_once $_SERVER['DOCUMENT_ROOT'] . '/webadmin/header.inc.php';

    $row = $DB->query_first(sprintf(
        "SELECT SUM(IF(`active` = 'y', 1, 0)) AS `active`, COUNT(*) AS `total` FROM %s",
		"`banners`"
    ));
?>
<table cellpadding="2" cellspacing="2" width="100%"><tr>
	<td class="pageMenu" width="20%"><div align="center"><b>Banners</b></div></td>
	<td class="pageMenu" style="text-align:right;" width="80%">
		<a href=".?action=add">Add New Banner</a> |
		<!--<a href=".?action=search">Search Products</a> | -->
		<a href=".?action=list&amp;active=y">List Active Banners (<?=$row['active']?>)</a> |
		<a href=".?action=list">List All Banners (<?=$row['total']?>)</a>
	</td>
</tr></table>
<?php
}

?>
<script type="text/javascript">
//<![CDATA[
<?php
include __DIR__ . '/funcs.js';
?>
//]]>
</script>
<div class="banner_content" style="width:100%;">
	<br />
