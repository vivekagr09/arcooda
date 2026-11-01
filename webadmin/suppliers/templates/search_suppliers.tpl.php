<!-- products_search.tpl.php -->
<?php
if(!isset($posted['type']))
	$get_vars['type']=$posted['type']='supp';

$post_url=$get_vars;
unset($post_url['page']);
unset($post_url['type']);
unset($post_url['status']);
unset($post_url['search_for']);
$post_url['action']='list';
$post_url='index.php?'.http_build_query($post_url);

$type=$posted['type'];
$type_ex=explode('|', $type);
if($posted['search_for']=='') $type_ex=array('both');

?>
<table align="center" cellspacing="0" style="border:1px solid #FF9900;width:95%;">
	<form action="<?=$post_url?>" method="post">
	<tr><td style="text-align:center;background-color:#FFFFCC;"><h2 style="color:#FF9900;">Supplier/Manufacturer Search</h2></td></tr>
	<tr><td style="text-align:center;background-color:#FFFFCC;padding-bottom:10px;">Enter a <b>ID</b> or a <b>Name</b></td></tr>
	<tr>
		<td style="text-align:center;background-color:#FFFFCC;">
			<input type="text" style="width:256px;" name="search_for" value="<?= htmlspecialchars($search_for) ?>" />
		</td>
	</tr>
	<tr>
		<td style="text-align:center;background-color:#FFFFCC;">
			<input type="radio" name="type" value="both" <?=(in_array('both',$type_ex)) ? 'checked' : ''?>/>View All&nbsp;&nbsp;
			<input type="radio" name="type" value="supp" <?=(in_array('supp',$type_ex)) ? 'checked' : ''?>/>View Suppliers Only&nbsp;&nbsp;
			<input type="radio" name="type" value="man" <?=(in_array('man',$type_ex)) ? 'checked' : ''?>/>View Manufacturers Only
		</td>
	</tr>
	<tr><td style="text-align:center;background-color:#FFFFCC;"><input type="submit" value="Search" /></td></tr>
	</form>
</table>

<!-- /products_search.tpl.php -->
