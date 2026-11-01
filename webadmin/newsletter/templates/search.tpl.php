<!-- search.tpl -->

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

<?php echo formatHTMLTitle('Search Newsletters') ?>

<div id="sectionNewsletterSpecialSearch">
	<form action="<?php echo formatHTMLSafe($post_url) ?>" method="post">
		<table class="wide" style="border:1px solid #f90;">
			<tr><td colspan="2" style="text-align:center;background-color:#ffc;"><h2 style="color:#f90;">Search Newsletters</h2></td></tr>
			<tr><td colspan="2" style="text-align:center;background-color:#ffc;padding-bottom:10px;">Enter a <b>Newsletter ID</b> or <b>Description</b></td></tr>
			<tr><td colspan="2" style="text-align:center;background-color:#ffc;">Search For: <input type="text" style="width:256px;" name="search_for" value="<?php echo formatHTMLSafe($search_for) ?>" /></td></tr>
			<!-- <tr>
				<td colspan="2" style="text-align:right;background-color:#ffc;">Search For:</td>
				<td style="text-align:left;background-color:#ffc;"><input type="text" style="width:256px;" name="search_for" value="<?php echo formatHTMLSafe($search_for) ?>" /></td>
			</tr> -->
			<tr><td colspan="2" style="text-align:center;background-color:#ffc;">
<?php
if(is_array($websites_list)) foreach($websites_list as $site_id=>$site_name)
{
	if(!is_array($selected)) $selected=explode('|',$selected);
?>
				<input type="checkbox" name="site_id[]" value="<?php echo $site_id ?>"<?php echo (in_array($site_id,$selected))?' checked="checked"':'' ?> /><?php echo formatHTMLSafe($site_name) ?>
<?php
}
?>
			</td></tr>
			<tr><td colspan="2" style="text-align:center;background-color:#ffc;padding-bottom:10px;"><input type="submit" value="Search" /></td></tr>
		</table>
	</form>
</div>

<!-- /search.tpl -->
