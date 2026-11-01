<?php
global $_GLOBALS;
global $DB;

include_once $_SERVER['DOCUMENT_ROOT'] . '/webadmin/header.inc.php';

?>
<link rel="stylesheet" type="text/css" href="style.css" />

<script type="text/javascript" src="shopping_cart_funcs.js.php"></script>

<div class="shopping_cart_content">
<?php
if ('default' == $_GLOBALS['layout']) {
	$row = $DB->query_first("SELECT COUNT(*) AS total FROM `highwaygroup`.`shopping_carts` WHERE site_id=" . intval($_GLOBALS['site_settings']['site_id']));
?>
	<table cellspacing="2" cellpadding="2" style="width:100%;">
		<tr>
			<td style="width:30%;background:#eee;text-align:center"><b><?=htmlspecialchars(_('Shopping Carts'))?></b></td>
			<td style="width:70%;background:#eee;text-align:right">
				<a href=".?action=add" target="_blank">Add New Shopping Cart</a> |
				<a href=".?action=list">List Shopping Carts(<?=$row['total']?>)</a>
			</td>
		</tr>
	</table>
<?php
}
?>
	<div style="text-align:center">
		<br />
