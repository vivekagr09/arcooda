<!--
Created and modified by Mingze Xi@ Feb 2, 2015
Last Update: Feb 9, 2015

Update log:
	Feb 9, 2015: Correct indentions

Finished: 80%
Unfinished job: picture sections A9;

The product about section starts here, based on products_about.tpl.php from AGS
Difference: Manufaturer section and Date Product Released section are added to product section instead of Product Reference section (not available for coinexpress websites)
-->

<?php echo formatHTMLTitle('<a href="#" onclick="toggleSection(\'sectionAbout\');return false">About the Product</a>') ?>
<div id="sectionAbout">

<table class="table_fields" style="width:95%;margin:auto;font-size:100%">
		<!--Product name A1, Q2-->
		<tr>
			<th style="width:25%;">&nbsp;</th>
			<td style="width:75%;background-color:#45A744">&nbsp;</td>
		</tr>
		<tr>
			<th>Product Name :<br /><small style="color:red">www.coinopexpress.com</th>
			<td style="background-color:#45A744"><input type="text" name="data1[product_name]" size="40" value="<?php echo htmlspecialchars($product['product_name']) ?>" /></td>
		</tr>
		<tr>
			<th>&nbsp;</th>
			<td style="background-color:#45A744">&nbsp;</td>
		</tr>
		<!--Manufacturer P1-->
		<tr>
		<th>Manufacturer (<a href="/webadmin/generic_admin.php?table=manufacturers&amp;action=add" target="_blank">Add</a> / <a href="/webadmin/generic_admin.php?table=manufacturers&amp;query=" target="_blank">Edit</a>) :</th>
		<td class="right">
			<select name="data1[manufacturer]" style="width:100%;">
				<option value=""></option>
<?php
	$result = $DB->query("SELECT * FROM manufacturers ORDER BY manufacturer ASC");
	while ($row = $DB->fetch_array($result))
	{
?>
				<option value="<?php echo htmlspecialchars($row['id']) ?>"<?php echo ($product['manufacturer'] == $row['id']) ? ' selected="selected"' : '' ?>><?php echo htmlspecialchars($row['manufacturer']) ?></option>
<?php
	}
?>
			</select>

		</td>
	</tr>
		<!--Date product released / year released P2-->
		<tr>
		<th>Year Released (<a href="/webadmin/generic_admin.php?table=year_released&amp;action=add" target="_blank">Add</a> / <a href="/webadmin/generic_admin.php?table=year_released&amp;query=" target="_blank">Edit</a>) :</th>
		<td class="right">
			<select name="data1[year_released]" style="width:100%;">
				<option value=""></option>
<?php
	$result = $DB->query("SELECT * FROM year_released ORDER BY year_released DESC");
	while ($row = $DB->fetch_array($result))
	{
?>
				<option value="<?php echo htmlspecialchars($row['id']) ?>"<?php echo ($product['year_released'] == $row['id']) ? ' selected="selected"' : '' ?>><?php echo htmlspecialchars($row['year_released']) ?></option>
<?php
	}
?>
			</select>

		</td>
	</tr>
		<!--Product Type A2-->
		<tr>
			<th>Product Type (<a href="sections_admin.php?table=product_types&amp;action=add" target="_blank">Add</a> / <a href="sections_admin.php?table=product_types&amp;query=" target="_blank">Edit</a>) :</th>
			<td>
				<select name="data1[type]" style="width:80%;">
<?php
				$result = $DB->query("SELECT id, name FROM product_types WHERE parent = '$parent' ORDER BY name ASC");
				while ($row = $DB->fetch_array($result))
				{
?>
				<option value="<?php echo $row['id'] ?>"<?php echo ($product['type'] == $row['id']) ? ' selected="selected"' : '' ?>><?php echo htmlspecialchars($row['name']) ?></option>
<?php
				}
?>
				</select>
				<a href="#" onclick="<?php hecho('$("#category_tr").toggle();return false') ?>" class="addedit">(View All)</a>
			</td>
		</tr>

		<!--Product style A3-->
		<tr>
			<th>Product Style (<a href="/webadmin/generic_admin.php?table=product_style&amp;action=add" target="_blank">Add</a> / <a href="/webadmin/generic_admin.php?table=product_style&amp;query=" target="_blank">Edit</a>) :</th>
			<td>
			<select name="data1[style]" style="width:100%;">
				<option value=""></option>
<?php
	$result = $DB->query("SELECT * FROM product_style ORDER BY style ASC");
	while ($row = $DB->fetch_array($result))
	{
?>
				<option value="<?php echo htmlspecialchars($row['id']) ?>"<?php echo ($product['style'] == $row['id']) ? ' selected="selected"' : '' ?>><?php echo htmlspecialchars($row['style']) ?></option>
<?php
	}
?>
			</select>
		</tr>
		<!--country/ location A4-->
		<tr>
			<th>Country Location (<a href="/webadmin/generic_admin.php?table=product_country_location&amp;action=add" target="_blank">Add</a> / <a href="/webadmin/generic_admin.php?table=product_country_location&amp;query=" target="_blank">Edit</a>) :</th>
			<td><select name="data1[country_location]" style="width:100%;">
				<option value=""></option>
<?php
	$result = $DB->query("SELECT * FROM product_country_location ORDER BY name ASC");
	while ($row = $DB->fetch_array($result))
	{
?>
				<option value="<?php echo htmlspecialchars($row['id']) ?>"<?php echo ($product['country_location'] == $row['id']) ? ' selected="selected"' : '' ?>><?php echo htmlspecialchars($row['name']) ?></option>
<?php
	}
?>
			</select></td>
		</tr>

		<!--Storage location A5-->
		<tr>
			<th>Storage Location (<a href="/webadmin/generic_admin.php?table=product_storage_location&amp;action=add" target="_blank">Add</a> / <a href="/webadmin/generic_admin.php?table=product_storage_location&amp;query=" target="_blank">Edit</a>) :</th>
			<td><select name="data1[storage]" style="width:100%;">
				<option value=""></option>
<?php
	$result = $DB->query("SELECT * FROM product_storage_location ORDER BY location ASC");
	while ($row = $DB->fetch_array($result))
	{
?>
				<option value="<?php echo htmlspecialchars($row['id']) ?>"<?php echo ($product['storage'] == $row['id']) ? ' selected="selected"' : '' ?>><?php echo htmlspecialchars($row['location']) ?></option>
<?php
	}
?>
			</select></td>
		</tr>

		<!--Availability A6-->
		<tr>
			<th>Availability (<a href="/webadmin/generic_admin.php?table=product_availability&amp;action=add" target="_blank">Add</a> / <a href="/webadmin/generic_admin.php?table=product_availability&amp;query=" target="_blank">Edit</a>) :</th>
			<td>
			<select name="data1[available]" style="width:100%;">
				<option value=""></option>
<?php
	$result = $DB->query("SELECT * FROM product_availability ORDER BY availability ASC");
	while ($row = $DB->fetch_array($result))
	{
?>
				<option value="<?php echo htmlspecialchars($row['id']) ?>"<?php echo ($product['available'] == $row['id']) ? ' selected="selected"' : '' ?>><?php echo htmlspecialchars($row['availability']) ?></option>
<?php
	}
?>
			</select>
			</td>
		</tr>

		<!--Data available A7-->
		<tr>
			<th>Date Available :</th>
			<td>
				<input name="data1[date_available]" type="text" id="date_available" style="width:80%;" value="<?php echo htmlspecialchars($product['date_available']) ?>" size="40" />
<?php
	addCalendarBtn("date_available");
?>
		</tr>
		<tr>
			<th>Condition :</th>
			<td class="right"><select name="data1[condition]" style="width:100%;">
			<option value=""></option>
<?php
	$result = $DB->query("SELECT * FROM product_conditions");
	while ($row = $DB->fetch_array($result))
	{
?>
			<option value="<?php echo htmlspecialchars($row['id']) ?>"<?php echo ($product['condition'] == $row['id']) ? ' selected="selected"' : '' ?>><?php echo htmlspecialchars($row['condition']) ?></option>
<?php
	}
?>
		</select></td>
		</tr>
		</table>
<br />