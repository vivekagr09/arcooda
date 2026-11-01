<!-- search.tpl.php -->

<?php
$post_url = $get_vars;
unset($post_url['page_id']);
unset($post_url['search_for']);
$post_url['action'] = 'list';
$post_url           = '.?' . http_build_query($post_url);
?>

<?=formatHTMLTitle('Search Members', '<a href=".?action=search2" style="color:blue;">Switch to sales search</a>')?>

<div id="sectionMemberSearch">
	<form accept-charset="utf-8" autocomplete="off" method="post" action="<?php hecho($post_url)?>" style="margin:0px">
		<table class="table_search">
			<tr><td colspan="2"><h2 style="color:#f90;">Search Members</h2></td></tr>
			<tr><td colspan="2">Enter a <b>Member ID</b>, <b>Member Name</b>, <b>Company Name</b> or a <b>Member Email</b></td></tr>
			<tr><td colspan="2">Search For: <input type="text" style="width:256px;" name="search_for" value="<?php hecho($search_for)?>" /></td></tr>
			<tr><td colspan="2">
<?php
foreach ($websites_list as $site_id => $site_name) {
    if (!is_array($selected)) {
        $selected = explode('|', $selected);
    }

?>
				<input type="checkbox" name="site_id[]" value="<?=$site_id?>"<?=(in_array($site_id, $selected)) ? ' checked="checked"' : ''?> /><?php hecho($site_name)?>
<?php
}
?>
			</td></tr>
			<tr><td colspan="2"><input type="submit" value="Search" /></td></tr>
		</table>
		<br /><br />
	</form>
</div>

<!-- /search.tpl.php -->
