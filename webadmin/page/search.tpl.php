<!-- search.tpl -->

<?php
$post_url = $get_vars;
unset($post_url['page_id']);
unset($post_url['search_for']);
$post_url['action'] = 'list';
$post_url           = 'index.php?' . http_build_query($post_url);

?>
<table align="center" style="border:1px solid #f90;width:95%;">
	<form action="<?=$post_url?>" method="post">
	<tr><td style="text-align:center;background:#ffc;color:#f90;"><h2>Search Topics</h2></td></tr>
	<tr><td style="text-align:center;background:#ffc;padding-bottom:10px;">Enter <b>Topic ID</b> or <b>Page Name</b></td></tr>
	<tr><td style="text-align:center;background:#ffc;">Search For: <input type="text" style="width:256px;" name="search_for" value="<?php hecho($search_for)?>" /></td></tr>
	<tr><td style="text-align:center;background:#ffc;"><input type="submit" value="Search" /></td></tr>
	</form>
</table>

<!-- /search.tpl -->
