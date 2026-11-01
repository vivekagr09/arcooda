<?php
include_once dirname(__FILE__) . "/header.inc.php";

$edit_mode = true;
$go_next   = true;
function get_category_type()
{ /*STUB*/}
function addCalendarHeader()
{ /*STUB*/}
function addCalendarBtn()
{ /*STUB*/}

function formatHTMLOptions($inarray, $sel = '')
{
    $outtext = '';
    if (count($inarray)) {
        foreach ($inarray as $key => $item) {
            $outtext .= '<option value="' . $key . '"' . (($key == $sel) ? ' selected="selected"' : '') . '>' . htmlspecialchars($item) . "</option>\n";
        }
    }

    return $outtext;
}

function query_list($query, $id_key = 0, $value_key = 1)
{
    global $DB;
    $results = array();
    $sql     = $DB->query($query);
    while ($row = $DB->fetch_array($sql, MYSQLI_BOTH)) {
        $results[$row[$id_key]] = ucfirst($row[$value_key]);
    }

    return $results;
}

$type_list             = query_list("SELECT id,name FROM product_types WHERE parent=" . intval($parent) . " ORDER BY name ASC");
$style_list            = query_list("SELECT id,style FROM product_style ORDER BY style ASC");
$manufacturer_list     = query_list("SELECT id,manufacturer FROM manufacturers ORDER BY manufacturer ASC");
$year_released_list    = query_list("SELECT id,year_released FROM year_released ORDER BY year_released DESC");
$condition_list        = query_list("SELECT id,`condition` FROM product_conditions");
$storage_list          = query_list("SELECT id,location FROM product_storage_location ORDER BY location ASC");
$country_location_list = query_list("SELECT id,name FROM product_country_location ORDER BY name ASC");
$available_list        = query_list("SELECT id,availability FROM product_availability ORDER BY availability ASC");
$related_list          = query_list("SELECT t.id,CONCAT_WS(' - ',c.name,t.name) FROM product_types t INNER JOIN product_types c ON t.parent=c.id ORDER BY c.name ASC,t.name ASC");

?>
<link type="text/css" href="style2.css" rel="stylesheet"/>
<script type="text/javascript">
//<![CDATA[
function getElement(theName, pos)
{
	var theElem1 = document.getElementsByName(theName);
	if (pos != null && theElem1.length > pos) {
		return theElem1[pos];
	}
	var theElem2 = document.getElementById(theName);
	if (theElem2) {
		return theElem2;
	}
	if (theElem1.length > 0) {
		return theElem1[0];
	}
	return null;
}
function getValue(theName, pos)
{
	var theElem = getElement(theName, pos);
	if (theElem != null) {
		if (theElem.value != null) {
			return theElem.value;
		}
	}
	return '';
}
function setValue(theName, value, pos)
{
	var theElem = getElement(theName,pos);
	if (theElem != null) {
		if (theElem.value != null) {
			theElem.value = value;
		}
	}
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
		<td>Factory ID :<br /><div style="color:#FF0000;font-size:-1">(not shown on site)</div></td>
		<td><input type="text" name="data1[factory_id]" class="input_wide" value="<?php echo htmlspecialchars($data1['factory_id']) ?>" /></td>
	</tr>
	<tr>
		<td>Product Name :</td>
		<td><input type="text" name="data1[product_name]" class="input_wide" value="<?php echo htmlspecialchars($data1['product_name']) ?>" /></td>
	</tr>
	<tr>
		<td>Product Type :</td>
		<td>
			<select name="data1[type]" class="input_wide">
				<option value=""></option>
				<?php echo formatHTMLOptions($type_list, $data1['type']) ?>
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
				<?php echo formatHTMLOptions($style_list, $data1['style']) ?>
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
				<?php echo formatHTMLOptions($manufacturer_list, $data1['manufacturer']) ?>
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
				<?php echo formatHTMLOptions($year_released_list, $data1['year_released']) ?>
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
				<?php echo formatHTMLOptions($condition_list, $data1['condition']) ?>
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
				<?php echo formatHTMLOptions($storage_list, $data1['storage']) ?>
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
				<?php echo formatHTMLOptions($country_location_list, $data1['country_location']) ?>
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
				<?php echo formatHTMLOptions($available_list, $data1['available']) ?>
			</select>
			<a href="/webadmin/generic_admin.php?table=product_availability&amp;action=add" target="_blank">Add</a>
			| <a href="/webadmin/generic_admin.php?table=product_availability&amp;query=" target="_blank">Edit</a>
		</td>
	</tr>
	<tr>
		<td>Date Available :</td>
		<td>
			<input name="data1[date_available]" type="text" id="date_available" class="input_wide" />
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
			<textarea name="data1[short_desc]" id="short_desc" rows="5"></textarea><br />
			<input type="button" name="copy_short_to_long" value="Copy the above short description to below" onclick="javascript:do_copy_short()" />
		</td>
	</tr>
	<tr>
		<td>Long Desc :</td>
		<td><textarea name="data1[long_desc]" id="long_desc" rows="8"></textarea></td>
	</tr>
	<tr>
		<td>Product Details :</td>
		<td><textarea name="data1[product_details]" rows="8"></textarea></td>
	</tr>
	<tr>
		<td><b>Notes :</td>
		<td><textarea name="data1[notes]" rows="3"></textarea></td>
	</tr>

	<tr><td colspan="2" class="spacer"></td></tr>

	<tr>
		<td>Admin Notes :<br />
			<br />
			<div style="color:#FF0000;font-size:-1">Admin notes are never<br />displayed on the main site</div>
		</td>
		<td><textarea name="admin_notes" rows="8"></textarea></td>
	</tr>

	<tr><td colspan="2" class="spacer"></td></tr>

<!--related links/title -->
<?php
for ($i = 0; $i < 3; $i++) {
?>
	<tr>
		<td>Related Title :</td>
		<td><input type="text" name="related[][title]" />(Optional)</td>
	</tr>
	<tr>
		<td>Related Type :</td>
		<td>
			<select name="related[][type_id]">
				<option value=""></option>
				<?php echo formatHTMLOptions($related_list) ?>
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
		<td><input name="data1[dimension]" type="text" /></td>
	</tr>

	<tr><td colspan="2" class="spacer"></td></tr>

	<tr>
		<td></td>
		<td>
			<div class="small" style="float:left;text-align:center;font-weight:bold;">Width</div>
			<div class="small" style="float:left;text-align:center;font-weight:bold;">Depth</div>
			<div class="small" style="float:left;text-align:center;font-weight:bold;">Height</div>
			<div class="small" style="float:left;text-align:center;font-weight:bold;">Weight</div>
			<div class="small" style="float:left;text-align:center;font-weight:bold;">Units</div>
		</td>
	</tr>
	<tr>
		<td>Product Dimensions :</td>
		<td>
			<input type="text" name="data2[0][width]" onchange="javascript:onChangeLength(0);" class="small" style="text-align:right;" />
			<input type="text" name="data2[0][depth]" onchange="javascript:onChangeLength(0);" style="width:60px;text-align:right;" />
			<input type="text" name="data2[0][height]" onchange="javascript:onChangeLength(0);" style="width:60px;text-align:right;" />
			<input type="text" name="data2[0][weight]" onchange="javascript:onChangeWeight(0);" style="width:60px;text-align:right;" />
			<select name="data2[0][units]" onchange="javascript:onChangeUnits(0);" style="width:19%;">
				<option value="0">cms / kgs</option>
				<option value="1">inches / lbs</option>
			</select>
		</td>
	</tr>
	<tr>
		<td>Increment :</td>
		<td class="right_content">
			<div style="width:20%;float:left;text-align:center;font-weight:bold;"></div>
			<div style="width:20%;float:left;text-align:center;font-weight:bold;"></div>
			<div style="width:20%;float:left;text-align:center;font-weight:bold;"></div>
			<input type="text" name="data2[0][increment]" style="width:19%;text-align:right;" value="" />
			<select style="width:19%;">
				<option>kgs</option>
				<option>lbs</option>
			</select>
		</td>
	</tr>
	<tr>
		<td>Volume Weight :</td>
		<td class="right_content"><input type="text" size="5" name="data2[0][volume_weight]" style="text-align:right;" /></td>
	</tr>
	<tr>
		<td>Cubic Metres :</td>
		<td class="right_content"><input type="text" size="5" name="data2[0][cubic_metres]" style="text-align:right;" /></td>
	</tr>
	<tr>
		<td>20ft Container Qty :</td>
		<td class="right_content">
			<input type="text" size="5" name="data2[0][con20ft_qty]" style="text-align:right;" />
			<input name="data2[0][con20ft_real]" type="radio" value="approx" checked="checked" /><b>Approx</b>
			<input name="data2[0][con20ft_real]" type="radio" value="real" /><b>Real</b>
		</td>
	</tr>
	<tr>
		<td>40ft Container Qty :</td>
		<td class="right_content">
			<input type="text" size="5" name="data2[0][con40ft_qty]" style="text-align:right;" />
			<input name="data2[0][con40ft_real]" type="radio" value="approx" checked="checked" /><b>Approx</b>
			<input name="data2[0][con40ft_real]" type="radio" value="real" /><b>Real</b>
		</td>
	</tr>

	<tr><td colspan="2" class="spacer"></td></tr>

	<tr><td colspan="2" class="center_title">Without Carton, Packing</td></tr>
	<tr>
		<td></td>
		<td class="right_content">
			<div style="width:20%;float:left;text-align:center;font-weight:bold;">Width</div>
			<div style="width:20%;float:left;text-align:center;font-weight:bold;">Depth</div>
			<div style="width:20%;float:left;text-align:center;font-weight:bold;">Height</div>
			<div style="width:20%;float:left;text-align:center;font-weight:bold;">Weight</div>
			<div style="width:20%;float:left;text-align:center;font-weight:bold;">Units</div>
		</td>
	</tr>
	<tr>
		<td>Part 1 :</td>
		<td class="right_content">
			<input type="text" name="data2[1][width]" onchange="javascript:onChangeLength(1);" style="width:19%;text-align:right;" />
			<input type="text" name="data2[1][depth]" onchange="javascript:onChangeLength(1);" style="width:19%;text-align:right;" />
			<input type="text" name="data2[1][height]" onchange="javascript:onChangeLength(1);" style="width:19%;text-align:right;" />
			<input type="text" name="data2[1][weight]" onchange="javascript:onChangeWeight(1);" style="width:19%;text-align:right;" />
			<select name="data2[1][units]" style="width:19%;" onchange="javascript:onChangeUnits(1);">
				<option value="0">cms / kgs</option>
				<option value="1">inches / lbs</option>
			</select>
		</td>
	</tr>
	<tr>
		<td></td>
		<td class="right_content"><input type="button" value="Add Part" onclick="javascript:addPackingRow(1);" /></td>
	</tr>
	<tr>
		<td>Cubic Metres :</td>
		<td class="right_content"><input type="text" name="data2[1][cubic_metres]" style="text-align:right;" /></td>
	</tr>
	<tr>
		<td>20ft Container Qty :</td>
		<td class="right_content">
			<input type="text" name="data2[1][con20ft_qty]" style="text-align:right;" />
			<input type="radio" name="data2[1][con20ft_real]" value="approx" checked="checked" /><b>Approx</b>
			<input type="radio" name="data2[1][con20ft_real]" value="real" /><b>Real</b>
		</td>
	</tr>
	<tr>
		<td>40ft Container Qty :</td>
		<td class="right_content">
			<input type="text" name="data2[1][con40ft_qty]" style="text-align:right;" />
			<input type="radio" name="data2[1][con40ft_real]" value="approx" checked="checked" /><b>Approx</b>
			<input type="radio" name="data2[1][con40ft_real]" value="real" /><b>Real</b>
		</td>
	</tr>

	<tr><td colspan="2" class="spacer"></td></tr>

	<tr><td colspan="2" class="left" style="text-align:center;white-space:nowrap;">With Paper Carton Packing</td></tr>
	<tr>
		<td style="white-space:nowrap;" class="left"></td>
		<td class="right"><div
		style="width:19%;float:left;text-align:center;font-weight:bold;">Width</div><div
		style="width:19%;float:left;text-align:center;font-weight:bold;">Depth</div><div
		style="width:19%;float:left;text-align:center;font-weight:bold;">Height</div><div
		style="width:19%;float:left;text-align:center;font-weight:bold;">Weight</div><div
		style="width:19%;float:left;text-align:center;font-weight:bold;">Units</div></td>
	</tr>
	<tr>
		<td style="white-space:nowrap;" class="left"><b>Part 1 :</b></td>
		<td class="right"><input
		type="text" size="5" value="" style="width:19%;text-align:right;" /><input
		type="text" size="5" value="" style="width:19%;text-align:right;" /><input
		type="text" size="5" value="" style="width:19%;text-align:right;" /><input
		type="text" size="5" value="" style="width:19%;text-align:right;" /><select
		style="width:19%;"><option>cms / kgs</option><option>inches / lbs</option>
		</select></td>
	</tr>
	<tr>
		<td style="white-space:nowrap;" class="left"><b>Part 2 :</b></td>
		<td class="right"><input
		type="text" size="5" value="" style="width:19%;text-align:right;" /><input
		type="text" size="5" value="" style="width:19%;text-align:right;" /><input
		type="text" size="5" value="" style="width:19%;text-align:right;" /><input
		type="text" size="5" value="" style="width:19%;text-align:right;" /><select
		style="width:19%;"><option>cms / kgs</option><option>inches / lbs</option>
		</select></td>
	</tr>
	<tr>
		<td style="white-space:nowrap;" class="left"></td>
		<td class="right"><input type="button" value="Add Part" /></td>
	</tr>
	<tr>
		<td style="white-space:nowrap;" class="left">Cubic Metres :</td>
		<td class="right"><input type="text" size="5" style="text-align:right;" /></td>
	</tr>
	<tr>
		<td class="left">20ft Container Qty :</td>
		<td class="right">
			<input type="text" size="5" style="text-align:right;" />
			<input type="radio" value="approx" checked="checked" /><b>Approx</b>
			<input type="radio" value="real" /><b>Real</b>
		</td>
	</tr>
	<tr>
		<td class="left">40ft Container Qty :</td>
		<td class="right">
			<input type="text" size="5" style="text-align:right;" />
			<input type="radio" value="approx" checked="checked" /><b>Approx</b>
			<input type="radio" value="real" /><b>Real</b>
		</td>
	</tr>

	<tr><td colspan="2" style="white-space:nowrap;" class="centre"> </td></tr>

	<tr>
		<td style="white-space:nowrap;" class="left"><b>Dimensions :</b></td>
		<td class="right"><input type="text" name="data1[dimension]" size="40" style="width:300px;" /></td>
	</tr>

	<tr>
		<td style="white-space:nowrap;" class="left"><b>Product Dimensions(cms) :</b></td>
		<td class="right">
			<b>Width</b><input type="text" size="5" name="width1" value="" onchange="javascript:updateIns(this,'width',0,'Dim');onChangeDimensions();" />
			<b>Depth</b><input type="text" size="5" name="depth1" value="" onchange="javascript:updateIns(this,'depth',0,'Dim');onChangeDimensions();" />
			<b>Height</b><input type="text" size="5" name="height1" value="" onchange="javascript:updateIns(this,'height',0,'Dim');onChangeDimensions();" />
		</td>
	</tr>
	<tr>
		<td style="white-space:nowrap;" class="left"><b>Product Dimensions(inches) :</b></td>
		<td class="right">
			<b>Width</b><input type="text" size="5" name="widthIn" value="" onchange="javascript:updateCMS(this,'width',0,'Dim');onChangeDimensions();" />
			<b>Depth</b><input type="text" size="5" name="depthIn" value="" onchange="javascript:updateCMS(this,'depth',0,'Dim');onChangeDimensions();" />
			<b>Height</b><input type="text" size="5" name="heightIn" value="" onchange="javascript:updateCMS(this,'height',0,'Dim');onChangeDimensions();" />
		</td>
	</tr>

	<tr><td colspan="2"><table id="packing_table" style="width:100%;" cellspacing="0">
		<tr name="packing_cloner">
			<td style="white-space:nowrap;" class="left"><b>Packing Dimensions(cms/kgs) :</b></td>
			<td class="right">
				<b>Width</b><input type="text" size="5" name="pack_width[]" value="" onchange="javascript:updateIns(this,'width',0,'');onChangePackingDimensions();" />
				<b>Depth</b><input type="text" size="5" name="pack_depth[]" value="" onchange="javascript:updateIns(this,'depth',0,'');onChangePackingDimensions();" />
				<b>Height</b><input type="text" size="5" name="pack_height[]" value="" onchange="javascript:updateIns(this,'height',0,'');onChangePackingDimensions();" />
				<b>Weight</b><input type="text" size="5" name="pack_weight[]" value="" onchange="javascript:updateLbs(this,0,'');onChangePackingDimensions();" />
			</td>
		</tr>
		<tr name="packing_cloner_in">
			<td style="white-space:nowrap;" class="left"><b>Packing Dimensions(inches/lbs) :</b></td>
			<td class="right">
				<b>Width</b><input type="text" size="5" name="pack_width_in_0" value="" onchange="javascript:updateCMS(this,'width',0,'');onChangePackingDimensions();" />
				<b>Depth</b><input type="text" size="5" name="pack_depth_in_0" value="" onchange="javascript:updateCMS(this,'depth',0,'');onChangePackingDimensions();" />
				<b>Height</b><input type="text" size="5" name="pack_height_in_0" value="" onchange="javascript:updateCMS(this,'height',0,'');onChangePackingDimensions();"/>
				<b>Weight</b><input type="text" size="5" name="pack_weight_lb_0" value="" onchange="javascript:updateKgs(this,0,'');onChangePackingDimensions();" />
			</td>
			<script>updateCount();</script>
		</tr>
		<tr><td colspan="2" class="right" style="text-align:center;"><input type="button" name="pdim" value="Add Packing dimension" onclick="addPackingDimension();" /></td></tr>
	</table></td></tr>

	<tr>
		<td class="left">Actual Product Weight(kgs) :</td>
		<td class="right"><input type="text" name="actProdWt" size="40" style="width:300px" onchange="javascript:updateLbs(this,0,'Act');updateWeights(this.value)" /></td>
	</tr>
	<tr>
		<td class="left">Actual Product Weight(lbs) :</td>
		<td class="right"><input type="text" value="" name="actProdWtL" size="40" style="width:300px;" onchange="javascript:updateKgs(this,0,'Act')" /></td>
	</tr>
	<tr>
		<td class="left">Volume Weight(kgs) :</td>
		<td class="right"><input type="text" name="volWt" size="40" style="width:300px" /></td>
	</tr>
	<tr>
		<td style="white-space:nowrap;" class="left"><b>Shipping Weight(kgs) :</b></td>
		<td class="right"><input type="text" name="data1[weight]" size="40" style="width:300px" value="" /></td>
	</tr>
	<tr>
		<td style="white-space:nowrap;" class="left"><b>Increment(kgs) :</b></td>
		<td class="right"><input type="text" name="data1[weight_inc]" size="40" style="width:300px" value="" /></td>
	</tr>
	<tr>
		<td style="white-space:nowrap;" class="left"><b>Cubic Metres(without crating) :</b></td>
		<td class="right"><input type="text" name="data1[metres3_unpacked]" size="40" style="width:300px" value="" /></td>
	</tr>
	<tr>
		<td style="white-space:nowrap;" class="left"><b>Cubic Metres(with crating) :</b></td>
		<td class="right"><input type="text" name="data1[metres3_packed]" size="40" style="width:300px" value="" /></td>
	</tr>
	<tr>
		<td class="left">20ft Container Qty(without crating) :</td>
		<td class="right">
			<input type="text" name="qty20_unpacked" value="" />
			<b>Approx</b><input type="radio" name="loading20_unpacked" value="approx" checked="checked" />
			<b>Real</b><input type="radio" name="loading20_unpacked" value="real" />
		</td>
	</tr>
	<tr>
		<td class="left">20ft Container Qty(with crating) :</td>
		<td class="right">
			<input type="text" name="qty20_packed" value="" />
			<b>Approx</b><input type="radio" name="loading20_packed" value="approx" checked="checked" />
			<b>Real</b><input type="radio" name="loading20_packed" value="real" />
		</td>
	</tr>
	<tr>
		<td class="left">40ft Container Qty(without crating) :</td>
		<td class="right">
			<input type="text" name="qty40_unpacked" value="" />
			<b>Approx</b><input type="radio" name="loading40_unpacked" value="approx" checked="checked" />
			<b>Real</b><input type="radio" name="loading40_unpacked" value="real" />
		</td>
	</tr>
	<tr>
		<td class="left">40ft Container Qty(with crating) :</td>
		<td class="right">
			<input type="text" name="qty40_packed" value="" />
			<b>Approx</b><input type="radio" name="loading40_packed" value="approx" checked="checked" />
			<b>Real</b><input type="radio" name="loading40_packed" value="real" />
		</td>
	</tr>

	<tr><td colspan="2" style="white-space:nowrap;" class="centre"> </td></tr>

	<tr>
		<td style="white-space:nowrap;" class="left"><b>Picture :</b></td>
		<td class="right">
			<input type="hidden" name="MAX_FILE_SIZE" value="<?=htmlspecialchars(convertSize(ini_get("upload_max_filesize")))?>" />
			<input name="images[image]" type="file" style="width:300px" size="40" />
		</td>
	</tr>

	<tr><td colspan="2" style="white-space:nowrap;" class="centre"> </td></tr>

<!--supplier -->
	<tr>
		<td style="white-space:nowrap;" bgcolor="#EEEEE0" class="left"><b>Supplier :</b></td>
		<td bgcolor="#EEEEE0" class="right"><select name="data2[supplier]" style="width:300px;">
			<option value=""></option>
<?php
$result = $DB->query("SELECT id, supplier FROM suppliers ORDER BY supplier ASC");
while ($row = $DB->fetch_array($result)) {
?>
			<option value=""></option>
<?php
}
?>
		</select></td>
	</tr>
<!-- //supplier -->

<!--cost price -->
	<tr>
		<td style="white-space:nowrap;" bgcolor="#EEEEE0" class="left"><b>Cost Price :</b></td>
		<td bgcolor="#EEEEE0" class="right">
			<input type="text" name="data1[cost_price]" id="cost_price" size="10" style="width:70px" onChange="convert(this.form.cost_price, this.form.cost_currency, this.form.approx_cost_price, this.form.approx_cost_currency);doMarkup();setRate(this.form.approx_cost_exch_rate, this.form.cost_currency);" />
			<select name="data1[cost_currency]" style="width:230px" id="cost_currency" onChange="convert(this.form.cost_price, this.form.cost_currency, this.form.approx_cost_price, this.form.approx_cost_currency);setRate(this.form.approx_cost_exch_rate, this.form.cost_currency);">
<?php
$result = $DB->query("SELECT * FROM `highwaygroup`.`currency` ORDER BY `name` ASC");
while ($row = $DB->fetch_array($result)) {
?>
				<option value=""> - </option>
<?php
}
?>
			</select>
		</td>
	</tr>
<!--//cost price -->

<!--approx cost price -->
	<tr>
		<td style="white-space:nowrap;" bgcolor="#EEEEE0" class="left"><b>Approx Cost Price :</b></td>
		<td bgcolor="#EEEEE0" class="right">
			<input type="hidden" name="data1[approx_cost_exch_rate]" id="approx_cost_exch_rate" value="" />
			<input type="text" name="approx_cost_price" size="10" id="approx_cost_price" style="width:70px" disabled="disabled" onChange="doMarkup();" />
			<select name="data1[approx_cost_currency]" id="approx_cost_currency" style="width:230px" onChange="convert(this.form.cost_price, this.form.cost_currency, this.form.approx_cost_price, this.form.approx_cost_currency);doMarkup();">
<?php
$result = $DB->query("SELECT * FROM `highwaygroup`.`currency` ORDER BY `name` ASC");
while ($row = $DB->fetch_array($result)) {
?>
				<option value=""> - </option>
<?php
}
?>
			</select>
		</td>
	</tr>
<!--//approx cost price -->

<!--sales price -->
	<tr>
		<td style="white-space:nowrap;" class="left"><b>Sales Price :</b></td>
		<td class="right">
			<input type="text" name="data1[sales_price]" id="sales_price" size="10" style="width:70px;" onChange="doMarkup();setRate(this.form.approx_cost_exch_rate, this.form.cost_currency);" />
			<select name="data1[sales_currency]" id="sales_currency" style="width:230px" onChange="doMarkup();">
<?php
$result = $DB->query("SELECT * FROM `highwaygroup`.`currency` ORDER BY `name` ASC");
while ($row = $DB->fetch_array($result)) {
?>
				<option value=""> - </option>
<?php
}
?>
			</select>
		</td>
	</tr>
<!--//sales price -->

	<tr>
		<td style="white-space:nowrap;" bgcolor="#EEEEE0" class="left"><b>Markup % :</b></td>
		<td bgcolor="#EEEEE0" class="right"><input type="text" name="data1[markup]" id="markup" size="10" style="width:70px" onChange="reverseMarkup()" /> % </td>
	</tr>
	<tr>
		<td style="white-space:nowrap;" class="left"><b>Price Flux :</b></td>
		<td class="right"><table cellpadding="0" cellspacing="0" border="0" width="100%"><tr>
			<td width="20%"><select name="data1[price_flux]" style="width:70px">
				<option value="y">Yes</option>
				<option value="n" selected="selected">No</option>
			</select></td>
			<td>  Allow automatic price fluctuation<br />  according to curreny exchanges</td>
		</tr></table></td>
	</tr>

	<tr><td colspan="2" style="white-space:nowrap;" class="centre"> </td></tr>

<!--sample price -->
	<tr>
		<td style="white-space:nowrap;" class="left"><b>Sample Price :</b></td>
		<td class="right">
			<input type="text" name="data1[sample_price]" size="10" style="width:70px" />
			<select name="data1[sample_currency]" id="sample_currency" style="width:230px">
<?php
$result = $DB->query("SELECT * FROM `highwaygroup`.`currency` ORDER BY `name` ASC");
while ($row = $DB->fetch_array($result)) {
?>
				<option value=""> - </option>
<?php
}
?>
			</select>
		</td>
	</tr>
<!--//sample price -->

	<tr>
		<td style="white-space:nowrap;" class="left"><b>Sample Qty :</b></td>
		<td class="right"><input type="text" name="data1[sample_qty]" size="10" style="width:70px" value="" /></td>
	</tr>
	<tr>
		<td style="white-space:nowrap;" class="left"><b>Minimum Order :</b></td>
		<td class="right"><input type="text" name="data1[min_order]" size="10" style="width:70px" /></td>
	</tr>
	<tr>
		<td style="white-space:nowrap;" class="left"><b>Qty :</b></td>
		<td class="right"><input type="text" name="data1[qty]" size="10" style="width:70px" value="1" /></td>
	</tr>

	<tr><td colspan="2" style="white-space:nowrap;" class="centre"> </td></tr>

	<tr>
		<td style="white-space:nowrap;" class="left"><b>Active :</b></td>
		<td class="right"><select name="data1[active]" style="width:70px">
			<option value="y" selected="selected">Yes</option>
			<option value="n">No</option>
		</select></td>
	</tr>

	<tr><td colspan="2" style="white-space:nowrap;" class="centre"> </td></tr>

	<tr>
		<td align="right"><input type="radio" name="go_next" value="true" /></td>
		<td>Add more products</td>
	</tr>
	<tr>
		<td align="right"><input type="radio" name="go_next" value="false" /></td>
		<td>Go back to previous page</td>
	</tr>
	<tr>
		<td style="white-space:nowrap;"> </td>
		<td><p>
			<input type="hidden" name="action" value="do_add" />
			<input type="submit" name="do_action" value="Add" />
			<input type="reset" name="clear" value="Clear" />
		</p></td>
	</tr>
</table>
</div>
