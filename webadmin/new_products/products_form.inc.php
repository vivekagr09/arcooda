<script type="text/javascript">
//<![CDATA[
function getElement(theName,pos)
{
	var theElem1=document.getElementsByName(theName);
	if(pos!=null && theElem1.length>pos)
		return theElem1[pos];
	var theElem2=document.getElementById(theName);
	if(theElem2) return theElem2;
	if(theElem1.length>0) return theElem1[0];
	return null;
}

function getValue(theName,pos)
{
	var theElem=getElement(theName,pos);
	if(theElem!=null)
		if(theElem.value!=null)
			return theElem.value;
	return '';
}

function setValue(theName,value,pos)
{
	var theElem=getElement(theName,pos);
	if(theElem!=null)
		if(theElem.value!=null)
			theElem.value=value;
}

function onChangeLength(sectionID)
{
	var dataSection='data2['+sectionID+']';
	var inWidths=document.getElementsByName(dataSection+'[width]');
	var inDepths=document.getElementsByName(dataSection+'[depth]');
	var inHeights=document.getElementsByName(dataSection+'[height]');
	var inUnits=document.getElementsByName(dataSection+'[units]');
	var inCon20ftReal=getValue(dataSection+'[con20ft_real]',0);
	var inCon40ftReal=getValue(dataSection+'[con40ft_real]',0);

	var VolumeWeight=0.0;
	var CubicMetres=0.0;
	var SquareMetres=0.0;
	for(var i=0;i<inWidths.length;i++)
	{
		var cbm=Number(inWidths[i].value*inDepths[i].value*inHeights[i].value);
		var sqm=Number(inWidths[i].value*inDepths[i].value);
		if(inUnits[i].value==1)
		{
			cbm*=16.387064;
			sqm*=6.4516;
		}
		CubicMetres+=cbm/1000000.0;
		VolumeWeight+=cbm/5000.0;
		SquareMetres+=sqm;
	}
	var con20ft_qty=(235.0*590.0)/sqm;
	var con40ft_qty=(235.0*1201.0)/sqm;

	setValue(dataSection+'[volume_weight]',VolumeWeight,0);
	setValue(dataSection+'[cubic_metres]',CubicMetres,0);
	if(inCon20ftReal=='approx') setValue(dataSection+'[con20ft_qty]',con20ft_qty,0);
	if(inCon40ftReal=='approx') setValue(dataSection+'[con40ft_qty]',con40ft_qty,0);
}

function onChangeWeight(sectionID)
{
	var dataSection='data2['+sectionID+']';
	var inWeights=document.getElementsByName(dataSection+'[weight]');
	var inUnits=document.getElementsByName(dataSection+'[units]');

	var TotalWeight=0.0;
	for(var i=0;i<inWeights.length;i++)
	{
		var kgs=Number(inWeights[i].value);
		if(inUnits[i].value==1)
			kgs/=2.2;
		TotalWeight+=kgs;
	}

	setValue(dataSection+'[total_weight]',TotalWeight,0);
}

function onChangeUnits(sectionID)
{
	onChangeLength(sectionID);
	onChangeWeight(sectionID);
}

function addPackingRow(sectionID)
{
	var dataSection='data2['+sectionID+']';
	var inRows=document.getElementsByName('packRow['+sectionID+'][]');
	var partNumber=inRows.length+1;
	var insertRow=getElement('packTotalRow['+sectionID+']');
	if(insertRow==null || insertRow.rowIndex==null || insertRow.parentNode==null) return;

	var newRow=insertRow.parentNode.insertRow(insertRow.rowIndex);
	newRow.insertCell(0).innerHTML="asdsdas";
	newRow.insertCell(1).innerHTML="sdiansldkn";
}

function do_copy_short()
{
	document.admin_form.long_desc.value = document.admin_form.short_desc.value;
}

function doMarkup()
{
	var approx_cost = document.admin_form.approx_cost_price.value;
	var sales = document.admin_form.sales_price.value;
	var approx_currency = document.admin_form.approx_cost_currency.value;
	var sales_currency = document.admin_form.sales_currency.value;

	var markup_value = parseInt((sales - approx_cost) / approx_cost * 10000);

	markup_value = markup_value / 100;

	if (approx_currency == sales_currency && isNaN(markup_value) == false)
	{
		document.admin_form.markup.value = markup_value;
	}
}

function reverseMarkup()
{
	var approx_cost = document.admin_form.approx_cost_price.value;
	var sales = document.admin_form.sales_price.value;
	var approx_currency = document.admin_form.approx_cost_currency.value;
	var sales_currency = document.admin_form.sales_currency.value;

	var markup = document.admin_form.markup.value;
	markup = parseFloat(markup/100);
	if (approx_currency == sales_currency)
	{
		var newSales = approx_cost * (1 + markup)*100;

		newSales = parseInt(newSales) / 100;

		document.admin_form.sales_price.value = newSales;
	}
}
//]]>
</script>
<?php
addCalendarHeader();
?>
<div id="admin_content">
<table border="0" align="center" cellpadding="4" cellspacing="0">
	<tr><td colspan="2" class="spacer"></td></tr>

	<tr>
		<td class="left_title">Factory ID :<br /><div style="color:#FF0000;font-size:-1">(not shown on site)</div></td>
		<td class="right_content"><input type="text" name="data1[factory_id]" class="input_wide" value="<?= htmlspecialchars($data1['factory_id']) ?>" /></td>
	</tr>
	<tr>
		<td>Product Name :</td>
		<td><input type="text" name="data1[product_name]" class="input_wide" value="<?= htmlspecialchars($data1['product_name']) ?>" /></td>
	</tr>
	<tr>
		<td>Product Type :</td>
		<td>
			<select name="data1[type]" class="input_wide">
				<option value=""></option>
				<?= formatHTMLOptions($type_list,$data1['type']) ?>
			</select>
			<a href="sections_admin.php?table=product_types&amp;action=add" target="_blank">Add</a>
			| <a href="sections_admin.php?table=product_types&amp;query=" target="_blank">Edit</a>
		</td>
	</tr>

<!-- style -->
	<tr>
		<td>Product Style :</td>
		<td>
			<select name="data1[style]" class="input_wide">
				<option value=""></option>
				<?= formatHTMLOptions($style_list,$data1['style']) ?>
			</select>
			<a href="/webadmin/generic_admin.php?table=product_style&amp;action=add" target="_blank">Add</a>
			| <a href="/webadmin/generic_admin.php?table=product_style&amp;query=" target="_blank">Edit</a>
		</td>
	</tr>
<!--//style -->

<!--manufacturer -->
	<tr>
		<td>Manufacturer :</td>
		<td>
			<select name="data1[manufacturer]" class="input_wide">
				<option value=""></option>
				<?= formatHTMLOptions($manufacturer_list,$data1['manufacturer']) ?>
			</select>
			<a href="/webadmin/generic_admin.php?table=manufacturers&amp;action=add" target="_blank">Add</a>
			| <a href="/webadmin/generic_admin.php?table=manufacturers&amp;query=" target="_blank">Edit</a>
		</td>
	</tr>
<!--//manufacturer -->

<!-- year -->
	<tr>
		<td>Year Released :</td>
		<td>
			<select name="data1[year_released]" class="input_wide">
				<option value=""></option>
				<?= formatHTMLOptions($year_released_list,$data1['year_released']) ?>
			</select>
			<a href="/webadmin/generic_admin.php?table=year_released&amp;action=add" target="_blank">Add</a>
			| <a href="/webadmin/generic_admin.php?table=year_released&amp;query=" target="_blank">Edit</a>
		</td>
	</tr>
<!--//year -->

	<tr><td colspan="2" class="spacer"></td></tr>

<!--condition -->
	<tr>
		<td>Condition :</td>
		<td>
			<select name="data1[condition]" class="input_wide">
				<option value=""></option>
				<?= formatHTMLOptions($condition_list,$data1['condition']) ?>
			</select>
		</td>
	</tr>
<!--//condition -->

<!--storage -->
	<tr>
		<td>Storage Location :</td>
		<td>
			<select name="data1[storage]" class="input_wide">
				<option value=""></option>
				<?= formatHTMLOptions($storage_list,$data1['storage']) ?>
			</select>
			<a href="/webadmin/generic_admin.php?table=product_storage_location&amp;action=add" target="_blank">Add</a>
			| <a href="/webadmin/generic_admin.php?table=product_storage_location&amp;query=" target="_blank">Edit</a>
		</td>
	</tr>
<!--//storage -->

<!--country -->
	<tr>
		<td>Country/Location :</td>
		<td>
			<select name="data1[country_location]" class="input_wide">
				<option value=""></option>
				<?= formatHTMLOptions($country_location_list,$data1['country_location']) ?>
			</select>
			<a href="/webadmin/generic_admin.php?table=product_country_location&amp;action=add" target="_blank">Add</a>
			| <a href="/webadmin/generic_admin.php?table=product_country_location&amp;query=" target="_blank">Edit</a>
		</td>
	</tr>
<!--//country -->

<!-- available -->
	<tr>
		<td>Availability :</td>
		<td>
			<select name="data1[available]" class="input_wide">
				<option value=""></option>
				<?= formatHTMLOptions($available_list,$data1['available']) ?>
			</select>
			<a href="/webadmin/generic_admin.php?table=product_availability&amp;action=add" target="_blank">Add</a>
			| <a href="/webadmin/generic_admin.php?table=product_availability&amp;query=" target="_blank">Edit</a>
		</td>
	</tr>
	<tr>
		<td>Date Available :</td>
		<td>
			<input name="data1[date_available]" type="text" id="date_available" class="input_wide" value="<?= htmlspecialchars($data1['date_available']) ?>" />
<?php
addCalendarBtn("date_available");
?>
		</td>
	</tr>
<!--//available -->

	<tr><td colspan="2" class="spacer"></td></tr>

	<tr>
		<td>Short Desc :</td>
		<td>
			<textarea name="data1[short_desc]" id="short_desc" rows="5" class="input_wide"><?= htmlspecialchars($data1['short_desc']) ?></textarea><br />
			<input type="button" name="copy_short_to_long" value="Copy the above short description to below" onclick="javascript:do_copy_short()" class="input_wide" />
		</td>
	</tr>
	<tr>
		<td>Long Desc :</td>
		<td><textarea name="data1[long_desc]" id="long_desc" rows="8" class="input_wide"><?= htmlspecialchars($data1['long_desc']) ?></textarea></td>
	</tr>
	<tr>
		<td>Product Details :</td>
		<td><textarea name="data1[product_details]" rows="8" class="input_wide"><?= htmlspecialchars($data1['product_details']) ?></textarea></td>
	</tr>
	<tr>
		<td><b>Notes :</td>
		<td><textarea name="data1[notes]" rows="3" class="input_wide"><?= htmlspecialchars($data1['notes']) ?></textarea></td>
	</tr>

	<tr><td colspan="2" class="spacer"></td></tr>

	<tr>
		<td>Admin Notes :<br />
			<br />
			<div style="color:#FF0000;font-size:-1">Admin notes are never<br />displayed on the main site</div>
		</td>
		<td><textarea name="admin_notes" rows="8" class="input_wide"></textarea></td>
	</tr>

	<tr><td colspan="2" class="spacer"></td></tr>

<!--related links/title -->
<?php
for($i=0;$i<3;$i++)
{
?>
	<tr>
		<td>Related Title :</td>
		<td><input type="text" name="related[][title]" class="input_wide" value="<?= htmlspecialchars($related[$i]['title']) ?>" />(Optional)</td>
	</tr>
	<tr>
		<td>Related Type :</td>
		<td>
			<select name="related[][type_id]" class="input_wide">
				<option value=""></option>
				<?= formatHTMLOptions($related_list,$related[$i]['type_id']) ?>
			</select>
		</td>
	</tr>

	<tr><td colspan="2" class="spacer"></td></tr>

<?php
}
?>
<!--//related links/title -->

	<tr>
		<td>Old Dimensions :</td>
		<td><input name="data1[dimension]" type="text" class="input_wide" value="<?= htmlspecialchars($data1['dimension']) ?>" /></td>
	</tr>

	<tr><td colspan="2" class="spacer"></td></tr>

	<tr>
		<td></td>
		<td>
			<div class="input_small" style="float:left;text-align:center;font-weight:bold;">Width</div>
			<div class="input_small" style="float:left;text-align:center;font-weight:bold;">Depth</div>
			<div class="input_small" style="float:left;text-align:center;font-weight:bold;">Height</div>
			<div class="input_small" style="float:left;text-align:center;font-weight:bold;">Weight</div>
			<div class="input_small" style="float:left;text-align:center;font-weight:bold;">Units</div>
		</td>
	</tr>
	<tr>
		<td>Product Dimensions :</td>
		<td>
			<input type="text" name="data2[0][width]" onchange="javascript:onChangeLength(0);" class="input_small" style="text-align:right;" value="<?= $data2[0]['width'] ?>"
			/><input type="text" name="data2[0][depth]" onchange="javascript:onChangeLength(0);" class="input_small" style="text-align:right;" value="<?= $data2[0]['depth'] ?>"
			/><input type="text" name="data2[0][height]" onchange="javascript:onChangeLength(0);" class="input_small" style="text-align:right;" value="<?= $data2[0]['heigth'] ?>"
			/><input type="text" name="data2[0][weight]" onchange="javascript:onChangeWeight(0);" class="input_small" style="text-align:right;" value="<?= $data2[0]['weight'] ?>"
			/><select name="data2[0][units]" onchange="javascript:onChangeUnits(0);" class="input_small">
				<option value="0" selected="selected">cms / kgs</option>
				<option value="1">inches / lbs</option>
			</select>
		</td>
	</tr>
	<tr>
		<td>Increment :</td>
		<td>
			<div class="input_small" style="float:left;">&nbsp;</div>
			<div class="input_small" style="float:left;">&nbsp;</div>
			<div class="input_small" style="float:left;">&nbsp;</div>
			<input type="text" name="data2[0][increment]" class="input_small" style="text-align:right;" value="<?= $data2[0]['increment'] ?>"
			/><select name="data2[0][inc_units]" class="input_small">
				<option value="0" selected="selected">kgs</option>
				<option value="1">lbs</option>
			</select>
		</td>
	</tr>
	<tr>
		<td>Volume Weight :</td>
		<td><input type="text" size="5" name="data2[0][volume_weight]" class="input_small" style="text-align:right;" value="<?= $data2[0]['volume_weight'] ?>" /></td>
	</tr>
	<tr>
		<td>Cubic Metres :</td>
		<td><input type="text" size="5" name="data2[0][cubic_metres]" class="input_small" style="text-align:right;" value="<?= $data2[0]['cubic_metres'] ?>" /></td>
	</tr>
	<tr>
		<td>20ft Container Qty :</td>
		<td>
			<input type="text" size="5" name="data2[0][con20ft_qty]" class="input_small" style="text-align:right;" value="<?= $data2[0]['con20ft_qty'] ?>" />
			<input name="data2[0][con20ft_real]" type="radio" value="approx"<?= ($data2[0]['con20ft_real']=='real')?'':' checked="checked"' ?> class="input_auto" /><b>Approx</b>
			<input name="data2[0][con20ft_real]" type="radio" value="real"<?= ($data2[0]['con20ft_real']=='real')?' checked="checked"':'' ?> class="input_auto" /><b>Real</b>
		</td>
	</tr>
	<tr>
		<td>40ft Container Qty :</td>
		<td>
			<input type="text" size="5" name="data2[0][con40ft_qty]" class="input_small" style="text-align:right;" value="<?= $data2[0]['con40ft_qty'] ?>" />
			<input name="data2[0][con40ft_real]" type="radio" value="approx"<?= ($data2[0]['con40ft_real']=='real')?'':' checked="checked"' ?> class="input_auto" /><b>Approx</b>
			<input name="data2[0][con40ft_real]" type="radio" value="real"<?= ($data2[0]['con40ft_real']=='real')?' checked="checked"':'' ?> class="input_auto" /><b>Real</b>
		</td>
	</tr>

	<tr><td colspan="2" class="spacer"></td></tr>

<?php
for($i=1;$i<3;$i++)
{
?>
	<tr><td colspan="2" class="center_title">Packing Style <?= $i ?></td></tr>
	<tr>
		<td></td>
		<td>
			<div class="input_small" style="float:left;text-align:center;font-weight:bold;">Width</div>
			<div class="input_small" style="float:left;text-align:center;font-weight:bold;">Depth</div>
			<div class="input_small" style="float:left;text-align:center;font-weight:bold;">Height</div>
			<div class="input_small" style="float:left;text-align:center;font-weight:bold;">Weight</div>
			<div class="input_small" style="float:left;text-align:center;font-weight:bold;">Units</div>
		</td>
	</tr>
	<tr>
		<td>Part 1 :</td>
		<td>
			<input type="text" name="data2[<?= $i ?>][width][]" onchange="javascript:onChangeLength(<?= $i ?>);" class="input_small" style="text-align:right;" value="<?= $data2[$i]['width'] ?>"
			/><input type="text" name="data2[<?= $i ?>][depth][]" onchange="javascript:onChangeLength(<?= $i ?>);" class="input_small" style="text-align:right;" value="<?= $data2[$i]['depth'] ?>"
			/><input type="text" name="data2[<?= $i ?>][height][]" onchange="javascript:onChangeLength(<?= $i ?>);" class="input_small" style="text-align:right;" value="<?= $data2[$i]['height'] ?>"
			/><input type="text" name="data2[<?= $i ?>][weight][]" onchange="javascript:onChangeWeight(<?= $i ?>);" class="input_small" style="text-align:right;" value="<?= $data2[$i]['weight'] ?>"
			/><select name="data2[<?= $i ?>][units][]" class="input_small" onchange="javascript:onChangeUnits(<?= $i ?>);">
				<option value="0" selected="selected">cms / kgs</option>
				<option value="1">inches / lbs</option>
			</select>
		</td>
	</tr>
	<tr>
		<td></td>
		<td style="text-align:center;"><input type="button" value="Add Part" class="input_auto" onclick="javascript:addPackingRow(<?= $i ?>);" /></td>
	</tr>
	<tr>
		<td>Cubic Metres :</td>
		<td><input type="text" name="data2[<?= $i ?>][cubic_metres]" class="input_small" style="text-align:right;" value="<?= $data2[$i]['cubic_metres'] ?>" /></td>
	</tr>
	<tr>
		<td>20ft Container Qty :</td>
		<td>
			<input type="text" size="5" name="data2[<?= $i ?>][con20ft_qty]" class="input_small" style="text-align:right;" value="<?= $data2[$i]['con20ft_qty'] ?>" />
			<input name="data2[<?= $i ?>][con20ft_real]" type="radio" value="approx"<?= ($data2[$i]['con20ft_real']=='real')?'':' checked="checked"' ?> class="input_auto" /><b>Approx</b>
			<input name="data2[<?= $i ?>][con20ft_real]" type="radio" value="real"<?= ($data2[$i]['con20ft_real']=='real')?' checked="checked"':'' ?> class="input_auto" /><b>Real</b>
		</td>
	</tr>
	<tr>
		<td>40ft Container Qty :</td>
		<td>
			<input type="text" size="5" name="data2[<?= $i ?>][con40ft_qty]" class="input_small" style="text-align:right;" value="<?= $data2[$i]['con40ft_qty'] ?>" />
			<input name="data2[<?= $i ?>][con40ft_real]" type="radio" value="approx"<?= ($data2[$i]['con40ft_real']=='real')?'':' checked="checked"' ?> class="input_auto" /><b>Approx</b>
			<input name="data2[<?= $i ?>][con40ft_real]" type="radio" value="real"<?= ($data2[$i]['con40ft_real']=='real')?' checked="checked"':'' ?> class="input_auto" /><b>Real</b>
		</td>
	</tr>

	<tr><td colspan="2" class="spacer"></td></tr>

	<tr><td colspan="2" class="spacer"></td></tr>

	<tr><td colspan="2" class="spacer"></td></tr>
<?php
}
?>

	<tr>
		<td>Picture :</td>
		<td>
			<input type="hidden" name="MAX_FILE_SIZE" value="<?=htmlspecialchars(convertSize(ini_get("upload_max_filesize")))?>" />
			<input name="images[image]" type="file" class="input_wide" size="33" />
		</td>
	</tr>

	<tr><td colspan="2" class="spacer"></td></tr>

<!--supplier -->
	<tr>
		<td>Supplier :</td>
		<td><select name="data2[supplier]" class="input_wide">
			<option value=""></option>
			<?= formatHTMLOptions($supplier_list,$data2['supplier']) ?>
		</select></td>
	</tr>
<!-- //supplier -->

<!--cost price -->
	<tr>
		<td>Cost Price :</td>
		<td>
			<input type="text" name="data1[cost_price]" class="input_small" style="text-align:right;" onchange="javascript:updateCostApprox();"
			/><select name="data1[cost_currency]" class="input_medium" id="cost_currency" onchange="javascript:updateCostApprox();">
				<option value="JPY">JPY - Japanese Yen</option>
				<?= formatHTMLOptions($currency_list,$data1['cost_currency']) ?>
			</select>
		</td>
	</tr>
<!--//cost price -->

<!--approx cost price -->
	<tr>
		<td>Approx Cost Price :</td>
		<td>
			<input type="text" name="data1[approx_cost_price]" class="input_small" style="text-align:right;" readonly="readonly"
			/><select name="data1[approx_cost_currency]" class="input_medium" onchange="javascript:updateCostApprox();">
				<option value="USD">USD - U.S. Dollars</option>
				<?= formatHTMLOptions($currency_list,$data1['approx_cost_currency']) ?>
			</select>
		</td>
	</tr>
<!--//approx cost price -->

<!--sales price -->
	<tr>
		<td>Sales Price :</td>
		<td>
			<input type="text" name="data1[sales_price]" class="input_small" style="text-align:right;"
			/><select name="data1[sales_currency]" class="input_medium">
				<option value="USD">USD - U.S. Dollars</option>
				<?= formatHTMLOptions($currency_list,$data1['sales_currency']) ?>
			</select>
		</td>
	</tr>
<!--//sales price -->

	<tr>
		<td>Markup % :</td>
		<td><input type="text" name="data1[markup]" class="input_small" style="text-align:right;" value="<?= $data1['markup'] ?>" /> %</td>
	</tr>
	<tr>
		<td>Price Flux :</td>
		<td>
			<select name="data1[price_flux]" class="input_small" style="float:left;">
				<option value="y">Yes</option>
				<option value="n" selected="selected">No</option>
			</select>
			<div style="float:left;"> - Allow automatic price fluctuation<br /> according to currency exchanges</div>
		 </td>
	</tr>

	<tr><td colspan="2" class="spacer"></td></tr>

<!--sample price -->
	<tr>
		<td>Sample Price :</td>
		<td>
			<input type="text" name="data1[sample_price]" class="input_small" style="text-align:right;" value="<?= $data1['sample_price'] ?>" />
			<select name="data1[sample_currency]" class="input_medium">
				<option value="USD">USD - U.S. Dollars</option>
				<?= formatHTMLOptions($currency_list,$data1['sample_currency']) ?>
			</select>
		</td>
	</tr>
<!--//sample price -->

	<tr>
		<td>Sample Qty :</td>
		<td><input type="text" name="data1[sample_qty]" class="input_small" style="text-align:right;" value="<?= $data1['sample_qty'] ?>" /></td>
	</tr>
	<tr>
		<td>Minimum Order :</td>
		<td><input type="text" name="data1[min_order]" class="input_small" style="text-align:right;" value="<?= $data1['min_order'] ?>" /></td>
	</tr>
	<tr>
		<td>Qty :</td>
		<td><input type="text" name="data1[qty]" class="input_small" style="text-align:right;" value="<?= $data1['qty'] ?>" /></td>
	</tr>

	<tr><td colspan="2" class="spacer"></td></tr>

	<tr>
		<td>Active :</td>
		<td><select name="data1[active]" class="input_small">
			<option value="y"<?= ($data1['active']!='n')?' selected="selected"':'' ?>>Yes</option>
			<option value="n"<?= ($data1['active']=='n')?' selected="selected"':'' ?>>No</option>
		</select></td>
	</tr>

	<tr><td colspan="2" class="spacer"></td></tr>

	<tr>
		<td></td>
		<td>
			<input type="radio" name="go_next" value="true" class="input_auto" checked="checked" />Add more products<br />
			<input type="radio" name="go_next" value="false" class="input_auto" />Go back to previous page
		</td>
	</tr>
	<tr>
		<td></td>
		<td>
			<input type="hidden" name="action" value="do_add" class="input_auto" />
			<input type="submit" name="do_action" value="Add" class="input_auto" />
			<input type="reset" name="clear" value="Clear" class="input_auto" />
		</td>
	</tr>
</table>
</div>
