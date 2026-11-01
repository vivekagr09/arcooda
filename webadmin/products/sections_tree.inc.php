<?php
function show_sections($root_id, $tab, $shade)
{
	global $DB, $admin_lang, $script;
	$default_lang = 'EN';
	if ($admin_lang != null && trim($admin_lang) != '') $default_lang = $admin_lang;
	$query = $DB->query("SELECT * FROM `product_types` WHERE `parent` = " . intval($root_id) . " ORDER BY `ordering`, `name`");
	while ($row = $DB->fetch_assoc($query))
	{
		$parent = $row['id'];
		if ($root_id != 0)
		{
			$total_product = $DB->query_first(sprintf(
			    "SELECT COUNT(*) AS `total` FROM %s WHERE `type`=%d",
			    '`products`',
			    $row['id']
			), MYSQLI_ASSOC);
?>
<tr<?php echo ($shade = !$shade) ? ' bgcolor="#EEEEEE"' : "" ?> onmouseover="this.style.backgroundColor='#9DD6D2';" onmouseout="this.style.backgroundColor='';">
	<td><?php echo $tab . $tab ?> &#8226; <?php  echo '<a href="/webadmin/products_new/list.php?action=list&type='.$row['id'].'" target="_blank">'.$row['name'].'( '.$total_product["total"].' ) </a></td>';  ?></td>
<?php
		}
		else
		{
?>
<tr><td>&nbsp;</td><td>&nbsp;</td></tr>
<tr bgcolor="#EEEEEE">
	<td>&#8226; <?php hecho($row['name']) ?></td>
<?php
		}
?>
	<td>&nbsp;&nbsp;&nbsp;<a href="<?php hecho($script . "&action=edit&id=" . $parent) ?>">Edit</a> | <a href="<?php hecho($script . "&action=delete&id=" . $parent) ?>">Delete</a></td>
</tr>
<?php
		if ($parent != $root_id) show_sections($parent, $tab.$tab, true);
	}
	$DB->free_result($query);
}
function show_sections_options($root_id, $tab, $selected, $current_id, $default)
{
	global $DB, $admin_lang;
	$default_lang = 'EN';
	if ($admin_lang != null && trim($admin_lang) != '') $default_lang = $admin_lang;
	$query = $DB->query("SELECT * FROM `product_types` WHERE `parent` = " . intval($root_id) . " ORDER BY `ordering`, `name`");
	while ($row = $DB->fetch_assoc($query))
	{
		$parent = $row['id'];
		if ($parent != $current_id)
		{
?>
<option value="<?php hecho($parent) ?>"<?php echo ($parent == $selected && $parent != "0" || $selected == "0" && $row["name"] == $default) ? ' selected="selected"' : "" ?>>
<?php
			if ($root_id != 0) echo $tab;
			hecho($row['name']);
			echo '<br />' . "\n";
		}
	}
	$DB->free_result($query);
}
?>