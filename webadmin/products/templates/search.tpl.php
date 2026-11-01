<!-- search.tpl.php -->

<?php
if (!isset($posted['product_type'])) $get_vars['product_type'] = $posted['product_type'] = 'ind';
if (!isset($posted['active'])) $get_vars['active'] = $posted['active'] = '';
if ($posted['product_type'] == 'm') $posted['site_id'] = '1|2';
$post_url = $get_vars;
unset($post_url['page']);
unset($post_url['product_type']);
unset($post_url['active']);
unset($post_url['status']);
unset($post_url['search_for']);
$post_url['action'] = 'list';
$post_url = 'index.php?' . http_build_query($post_url);
$product_type = $posted['product_type'];
$active = explode('|', $posted['active']);
?>
<table align="center" cellspacing="0" style="border:1px solid #FF9900;width:95%;">
	<form action="<?php hecho($post_url) ?>" method="post">
	<tr style="text-align:center;background-color:#FFFFCC;"><td colspan="2"><h2 style="color:#FF9900;">Product Search</h2></td></tr>
	<tr style="text-align:center;background-color:#FFFFCC;"><td colspan="2">Enter a <b>Product ID</b> or a <b>Product Name</b> or a <b>Supplier Product Name</b> or a <b>Product Code</b></td></tr>
	<tr style="text-align:center;background-color:#FFFFCC;">
		<td colspan="2"><input type="text" style="width:256px;" name="search_for" value="<?php hecho($search_for) ?>" /></td>
	</tr>
	<tr style="background-color:#FFFFCC;">
		<td style="width:30%;text-align:right;"><b>Active Status :</b></td>
		<td style="text-align:left;">
			<input type="radio" name="active" value="" <?php echo (in_array('', $active)) ? 'checked' : '' ?>/>View All&nbsp;&nbsp;
			<input type="radio" name="active" value="y" <?php echo (in_array('y', $active)) ? 'checked' : '' ?>/>View Active Products&nbsp;&nbsp;
			<input type="radio" name="active" value="n" <?php echo (in_array('cs', $active)) ? 'checked' : '' ?>/>Inactive Products&nbsp;&nbsp;
		</td>
	</tr>
	<tr><td colspan="2" style="text-align:center;background-color:#FFFFCC;"><input type="submit" value="Search" /></td></tr>
	</form>
</table>

<!-- /search.tpl.php -->
