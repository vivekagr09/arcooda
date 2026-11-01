<?php
/**
 * @var Serial_Handler $section_handler
 */
?>
<!-- <?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->

<style type="text/css">
/*<![CDATA[*/
table.search_table { width:95%; margin:auto; }
table.search_table td { background-color:#FFFFCC; text-align:center; }
table.search_table td.left { border-left:1px solid #FF9900; text-align:left; padding-left:55px; width:35%; font-size:12px; }
/*]]>*/
</style>

<div style="margin:0 16px;border:1px solid #f90;background:#ffc;text-align:center;padding:16px"><form action="." method="post">
	<input type="hidden" name="action" value="do_search" />
	<h2 style="color:#FF9900;margin:2px;font-size:1.5em"><?=htmlspecialchars(_('Search'))?></h2>
	<br />
	<table style="margin:auto">
		<tr>
			<th style="width:25%"><?=htmlspecialchars(_('Serial Number'))?></th>
			<td style="width:75%"><input type="text" class="wide" name="search_for" value="<?=htmlspecialchars($_GET['search_for'] ?? '')?>" /></td>
		</tr>
	</table>
	<br />
	<input type="submit" value="Search" />
</form>

<!-- /<?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->
