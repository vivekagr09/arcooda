<!-- products_changes.tpl.php

Created and modified by Mingze Xi@ Feb 9, 2015
Last Update: Feb 16, 2015

Update log: 1. Changes log are now available in the table.
			2. Removed "expand" options as it is less necessary.
			3. "Update" and "Clear" button were added.

Problem:


Finished: 100%

Unfinished job:

The product last 5 changes (tag:L) section starts here, based on products_changes.tpl.php @AGS/products/template
-->

<!--query log-->
<?php
function extract_str_diff($ref, $target)
{
	$dif = '';
	$target_i = 0;
	$ref_i = 0;
	for ($i = 0; $i < strlen($target); $i++)
	{
		//echo $ref_i.':'.$target_i.' ';
		if ($target[$target_i] != $ref[$ref_i])
		{
			$dif.= $target[$i];
			$ref_i--;
		}
		$target_i++;
		$ref_i++;
	}
	return $dif;
	//echo "$dif";

}
function elipse($string, $max = 15, $sufix = '...')
{
	//when the string length exceeds max length
	//then append the sufix
	if (strlen($string) > $max) return trim(substr($string, 0, $max)) . $sufix;
	else return $string;
}
$statement = "SELECT * FROM query_log WHERE query REGEXP (\"^UPDATE products(.*)id = '$id'(.*)\") ORDER BY log_date ASC";
$result = $DB->query($statement);
$total_logs = $DB->num_rows($result);
$old_str = '';
$querys = array();
if ($total_logs > 0)
{
	$old_changed = array();
	while ($row = $DB->fetch_array($result))
	{
		//$dif = trim(extract_str_diff($old_str, $row['query']));
		//$old_str = $row['query'];
		$dif = $row['query'];
		if ($dif != '')
		{
			$i = count($querys);
			$querys[$i]['query'] = elipse($dif, 95);
			$querys[$i]['date'] = $row['log_date'];
			$querys[$i]['user_id'] = $row['user_id'] == '0' ? '&nbsp;' : $row['user_id'];
			//				$querys[$i]['query']=$dif;
			$trim = explode(' ', $dif);
			foreach($trim as $key => $value) if (trim($value) == '') unset($trim[$key]);
			array_shift($trim);
			array_shift($trim);
			array_shift($trim);
			$changed = array();
			while (count($trim) > 0)
			{
				$key = array_shift($trim);
				if (array_shift($trim) == '=')
				{
					$value = array_shift($trim);
					while (substr($value, 0, 1) == "'" && !(substr($value, -1, 1) == "'" || substr($value, -2, 2) == "',") && count($trim) > 0) $value.= ' ' . array_shift($trim);
					if (substr($value, -1, 1) == ',') $value = substr($value, 0, -1);
					$changed[$key] = $value;
				}
				else break;
			}
			if (count($old_changed) > 0) foreach($old_changed as $key => $value) if ($changed[$key] == $value) unset($changed[$key]);
			foreach($changed as $key => $value) $old_changed[$key] = $value;
			unset($changed['old_price']);
			unset($changed['price_changed']);
			unset($changed['last_modified']);
			$link_list = array('available', 'country_location', 'storage', 'year_released', 'manufacturer', 'style', 'type');
			$link_names = array('active' => 'Active', 'cost_currency' => 'Cost Currency', 'cost_price' => 'Cost Price', 'date_available' => 'Date Available', 'dimension' => 'Dimensions', 'factory_id' => 'Factory ID', 'image' => 'Product Image', 'long_desc' => 'Long Description', 'metres3_packed' => 'Cubic Metres (with crating)', 'metres3_unpacked' => 'Cubic Metres (without crating)', 'min_order' => 'Minimum Order', 'notes' => 'Notes', 'power_consumption' => 'Power Consumption', 'product_details' => 'Product Details', 'product_name' => 'Product Name', 'qty' => 'Qty', 'sales_price' => 'Sales Price', 'sample_currency' => 'Sample Currency', 'sample_price' => 'Sample Price', 'sample_qty' => 'Sample Qty', 'short_desc' => 'Short Description', 'weight' => 'Shipping Weight (Kgs)', 'weight_inc' => 'Increment (Kgs)');
			$display_list = array();
			foreach($changed as $key => $value)
			{
				if ($link_names[$key] != '') $display_list[] = '<li><b>' . str_replace('<', '&lt;', $link_names[$key]) . '</b> changed to ' . str_replace('<', '&lt;', $value) . '</li>';
				else $display_list[] = '<li><b>' . str_replace('<', '&lt;', $key) . '</b> changed to ' . str_replace('<', '&lt;', $value) . '</li>';
			}
			if (count($display_list) > 0) $querys[$i]['query'] = "<ul>\n\t".implode("\n\t", $display_list)."\n</ul>\n";
			else unset($querys[$i]);
		}
	}
	//		$querys=array_reverse($querys);
	$max = 5	; //This is the total number of changes that you want to show.
}
?>
<!-- Query Log Ends here-->
<br />

<div align="center">
	<?php echo formatHTMLTitle('<a href="javascript:toggleSection(\'sectionChanges\');">Last 5 Changes</a>');?>
</div>

<div id="sectionChanges">
	<table style="width:95%" border="0" cellspacing="0" cellpadding="4" align="center">
		<tr>
			<th style="background-color:#FFCFAF;font-weight:bold;font-size:12" colspan="4" align="center">Last <?=$max?> Changes</th>
		</tr>
		<tr style="background-color:#E3EBF7">
			<td width="5%" align="center" nowrap class="bottomLeft"><b>Date</b></td>
			<td width="10%" align="center" nowrap class="bottomLeft"><b>User</b></td>
			<td width="85%" align="center" nowrap class="bottomLeftRight"><b>Query</b></td>
		</tr>
<?php
		for ($i = count($querys) - 1; $i >= 0 && $i >= count($querys) - $max; $i--)
		{
			echo "<tr bgcolor=\"" . (($i % 2 == 1) ? "#FFFFFF" : "#EEEEE0") . "\">\n";
			echo "<td class=\"bottomLeft\" valign=\"top\" nowrap=\"nowrap\" align=\"center\">" . $querys[$i]['date'] . "</td>\n";
			echo "<td class=\"bottomLeft\" valign=\"top\" nowrap=\"nowrap\" align=\"center\">" . $querys[$i]['user_id'] . "&nbsp;</td>\n";
			echo "<td class=\"bottomLeftRight\">" . $querys[$i]['query'] . "</td>\n";
			echo "</tr>\n";
		}
?>
	</table>
<?php
	if ($id > 0 && $product['cost_currency'] != $product['sales_currency'])
	{
?>
		<script type="text/javascript">
		//<![CDATA[
		reverseMarkup();
		//]]>
		</script>
<?php
	}
?>
</div>