<!-- suppliers_search.tpl.php -->

<?php
$post_url=$get_vars;
unset($post_url['page_id']);
unset($post_url['search_for']);
$post_url['action']='list';
$post_url='index.php?'.http_build_query($post_url);
?>

<script type="text/javascript">
//<![CDATA[
//]]>
</script>

<?= formatHTMLTitle('Search Suppliers') ?>

<div id="sectionSupplierSearch">
	<form action="<?= htmlspecialchars($post_url) ?>" method="post">
		<table class="wide" style="border:1px solid #f90;">
			<tr><td colspan="2" style="text-align:center;background-color:#ffc;"><h2 style="color:#f90;">Search Suppliers</h2></td></tr>
			<tr><td colspan="2" style="text-align:center;background-color:#ffc;padding-bottom:10px;">Enter <b>Supplier ID</b> or <b>Supplier Name</b></td></tr>
			<tr><td colspan="2" style="text-align:center;background-color:#ffc;">Search For:<input type="text" style="width:256px;" name="search_for" value="<?= htmlspecialchars($search_for) ?>" /></td></tr>
			</tr>
			<!-- <tr>
				<td style="text-align:right;background-color:#ffc;">Search For:</td>
				<td style="text-align:left;background-color:#ffc;"><input type="text" style="width:256px;" name="search_for" value="<?= htmlspecialchars($search_for) ?>" /></td>
			</tr> -->
			<tr><td colspan="2" style="text-align:center;background-color:#ffc;padding-bottom:10px;"><input type="submit" value="Search" /></td></tr>
		</table>
	</form>
</div>

<!-- /suppliers_search.tpl.php -->
