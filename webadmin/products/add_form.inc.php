<!-- add_form.inc.php -->

<?php
global $_GLOBALS;

$id                  = intval($id);
$product_bulkpricing = array();

if ($id > 0) {
    include_once $_SERVER['DOCUMENT_ROOT'] . '/webadmin/functions/formatting.inc.php';
    $product = $DB->query_first(" SELECT" . "	*," . "	UNIX_TIMESTAMP(date_added) AS date_added," . "	UNIX_TIMESTAMP(last_modified) AS last_modified" . " FROM " . $table . " WHERE id='" . $id . "'");
    $product_id = intval($product['id']);
    function typeofManual($code)
    {
        $array = array('coe' => 'COE Manual', 'hgr' => 'HG Manual', 'fm' => 'Factory Manual', 'wd' => 'Wiring Diagram', 'sm' => 'Service Manual', 'oth' => 'Other');
        return $array[$code];
    }
    function human_file_size($size)
    {
        $filesizename = array(" Bytes", " KB", " MB", " GB", " TB", " PB", " EB", " ZB", " YB");
        return round($size / pow(1024, ($i = floor(log($size, 1024)))), 2) . $filesizename[$i];
    }
}
?>
<script type="text/javascript">
//<![CDATA[
var count=0;
function getElement(theName,pos)
{
	var theElem=document.getElementById(theName);
	if(theElem) return theElem;
	theElem=document.getElementsByName(theName);
	if(pos==null) pos=0;
	if(theElem.length>pos) return theElem[pos];
	return null;
}
//Function to calculate how many of the product will
//fit in a container using crating dimensions
function updateCratingApprox()
{
	var theWidths=document.getElementsByName('pack_width[]');
	var theDepths=document.getElementsByName('pack_depth[]');
	var theArea=0.0;
	for(var i=0;i<theWidths.length;i++)
		theArea+=Number(theWidths[i].value*theDepths[i].value);
	if(theArea==0.0) return;

	//20ft Container
	var flag=false;
	var theOutput=getElement('qty20_packed');
	var theRadio=document.getElementsByName('loading20_packed');
	for(var i=0;i<theRadio.length;i++)
		if(theRadio[i].value=='approx' && theRadio[i].checked!=false)
			flag=true;
	if(flag) theOutput.value=Math.round((235.0*590.0)/theArea);

	//40ft Container
	var flag=false;
	var theOutput=getElement('qty40_packed');
	var theRadio=document.getElementsByName('loading40_packed');
	for(var i=0;i<theRadio.length;i++)
		if(theRadio[i].value=='approx' && theRadio[i].checked!=false)
			flag=true;
	if(flag) theOutput.value=Math.round((235.0*1201.0)/theArea);
}
//Function to calculate how many of the product will
//fit in a container using without crating dimensions
function updateFreeApprox()
{
	var theWidth=getElement('width1').value;
	var theDepth=getElement('depth1').value;
	var theArea=Number(theWidth*theDepth);
	if(theArea==0.0) return;

	//20ft Container
	var flag=false;
	var theOutput=getElement('qty20_unpacked');
	var theRadio=document.getElementsByName('loading20_unpacked');
	for(var i=0;i<theRadio.length;i++)
		if(theRadio[i].value=='approx' && theRadio[i].checked!=false)
			flag=true;
	if(flag) theOutput.value=Math.round((235.0*590.0)/theArea);

	//40ft Container
	var flag=false;
	var theOutput=getElement('qty40_unpacked');
	var theRadio=document.getElementsByName('loading40_unpacked');
	for(var i=0;i<theRadio.length;i++)
		if(theRadio[i].value=='approx' && theRadio[i].checked!=false)
			flag=true;
	if(flag) theOutput.value=Math.round((235.0*1201.0)/theArea);
}
//Function to calculate the unpacked cubic metres and update
function updateCubicUnpacked()
{
	var cbm=0.0;
	var theWidth=getElement('width1');
	var theDepth=getElement('depth1');
	var theHeight=getElement('height1');
	if(theWidth==null || theDepth==null || theHeight==null) return;
	cbm=theWidth.value*theDepth.value*theHeight.value;
	var theOutput=getElement('data1[metres3_unpacked]');
	if(theOutput) theOutput.value=cbm/1000000.0;
	var theDimensions=getElement('data1[dimension]');
	//if(theDimensions!=null)
	//	theDimensions.value='W'+theWidth.value+'0 x D'+theDepth.value+'0 x H'+theHeight.value+'0 mm';
}
//Function to calculate the packed cubic metres and update
function updateCubicPacked()
{
	var inWidths=			document.getElementsByName('pack_width[]');
	var inDepths=			document.getElementsByName('pack_depth[]');
	var inHeights=			document.getElementsByName('pack_height[]');
	var inWeights=			document.getElementsByName('pack_weight[]');
	var outCbm_Packed=		getElement('data1[metres3_packed]');
	var outVolume_Weight=	getElement('volWt');
	var outShipping_Weight=	getElement('data1[weight]');
	var cbm_packed=0.0;
	for(var i=0;i<inWidths.length;i++)
		cbm_packed+=Number(inWidths[i].value*inDepths[i].value*inHeights[i].value)/1000000.0;
	var volume_weight=0.0;
	for(var i=0;i<inWidths.length;i++)
		volume_weight+=inWidths[i].value*inDepths[i].value*inHeights[i].value/6000.0;
	var shipping_weight=0.0;
	for(var i=0;i<inWidths.length;i++)
		shipping_weight+=Number(inWeights[i].value);
	if(outCbm_Packed)		outCbm_Packed.value=cbm_packed;
	if(outVolume_Weight)	outVolume_Weight.value=volume_weight;
	if(outShipping_Weight)	outShipping_Weight.value=shipping_weight;
}
function onChangeDimensions()
{
	updateCubicUnpacked();
	updateFreeApprox();
}
function onChangePackingDimensions()
{
	updateCubicPacked();
	updateCratingApprox();
}
function addPackingDimension()
{
	var theTable=document.getElementById('packing_table');
	var theRow=theTable.insertRow(theTable.rows.length-1);
	var theCol1=document.createElement('TD');
	theCol1.className='left';
	theCol1.innerHTML='<b>Packing Dimensions(cms/kgs)</b>';
	theRow.appendChild(theCol1);
	var theCol2=document.createElement('TD');
	theCol2.className='right';
	theCol2.innerHTML='<b>Width</b><input type="text" size="5" name="pack_width[]" value="" onchange="javascript:updateIns(this,\'width\','+count+',\'\');onChangePackingDimensions();" /> <b>Depth</b><input type="text" size="5" name="pack_depth[]" value="" onchange="javascript:updateIns(this,\'depth\','+count+',\'\');onChangePackingDimensions();" /> <b>Height</b><input type="text" size="5" name="pack_height[]" value="" onchange="javascript:updateIns(this,\'height\','+count+',\'\');onChangePackingDimensions();" /> <b>Weight</b><input type="text" size="5" name="pack_weight[]" value="" onchange="javascript:updateLbs(this,'+count+');onChangePackingDimensions();" />';
	theRow.appendChild(theCol2);
	addPackingInDimensions();
	count++;
}
/* New functions for adding a row for inches as well...
Added: 21/07/05
*/
function addPackingInDimensions()
{
	var theTable=document.getElementById('packing_table');
	var theRow=theTable.insertRow(theTable.rows.length-1);
	var theCol1=document.createElement('TD');
	theCol1.className='left';
	theCol1.innerHTML='<b>Packing Dimensions(inches/lbs)</b>';
	theRow.appendChild(theCol1);
	var theCol2=document.createElement('TD');
	theCol2.className='right';
	theCol2.innerHTML='<b>Width</b><input type="text" size="5" name="pack_width_in_'+count+'" value="" onchange="javascript:updateCMS(this,\'width\','+count+',\'\');onChangePackingDimensions();" /> <b>Depth</b><input type="text" size="5" name="pack_depth_in_'+count+'" value="" onchange="javascript:updateCMS(this,\'depth\','+count+',\'\');onChangePackingDimensions();" /> <b>Height</b><input type="text" size="5" name="pack_height_in_'+count+'" value="" onchange="javascript:updateCMS(this,\'height\','+count+',\'\');onChangePackingDimensions();" /> <b>Weight</b><input type="text" size="5" name="pack_weight_lb_'+count+'" value="" onchange="javascript:updateKgs(this,'+count+');onChangePackingDimensions();" />';
	theCol2.style.backgroundColor='#DFF7E1';
	theRow.appendChild(theCol2);
}
function updateCMS(obj,type,num,func)
{
	var str = '';
	if(func=='Dim')
		str = ''+type+'1';
	else
		str = 'pack_'+type+'[]';
	var cms = document.getElementsByName(str)[num];
	cms.value = 2.54*obj.value;
	//onChangePackingDimensions();
}
function updateKgs(obj,num,func)
{
	var str = '';
	if(func == 'Act')
		str = 'actProdWt';
	else
		str = 'pack_weight[]';
	var lbs = document.getElementsByName(str)[num];
	lbs.value = 0.45*obj.value;
	//onChangePackingDimensions();
}
function updateIns(obj,type,num,func)
{
	var str = '';
	if(func=='Dim')
		str = ''+type+'In';
	else
		str = 'pack_'+type+'_in_'+(num);
	var ins = document.getElementsByName(str)[0];
	var val = obj.value/2.54;
	ins.value = Math.round(val);
}
function updateLbs(obj,num,func)
{
	var str = '';
	if(func == 'Act')
		str = 'actProdWtL';
	else
		str = 'pack_weight_lb_'+(num);
	var lbs = document.getElementsByName(str)[0];
	var val = obj.value/0.45;
	lbs.value = Math.round(val);
}
function updateCount()
{
	count++;
}
function updateWeights(val)
{
	var obj = document.getElementsByName('data1[weight]')[0];
	if(obj.value == 0 || obj.value == '')
		obj.value = val;
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
		document.admin_form.markup.value = markup_value;
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
function note_changes()
{
	var cost_currency = document.admin_form.cost_currency.value;
	var sales_currency = document.admin_form.sales_currency.value;
	var price_flux_elem=document.admin_form.price_flux;
	if(cost_currency==sales_currency && price_flux_elem.value=='y')
	{
		alert('The Cost and Sales Currency are the same. There cannot be any price flux.');
		price_flux_elem.value='n';
	}
	else
	{
		document.admin_form.update_prices.checked = true;
		toggle_changes();
	}
}
function toggle_changes()
{
	var cost_currency = document.admin_form.cost_currency.value;
	var exch_rate = getRate(cost_currency);
	document.admin_form.approx_cost_exch_rate.value = exch_rate;
	//reverseMarkup();
}
//]]>
</script>
<?php
addCalendarHeader();
?>
<table border="0" align="center" cellpadding="4" cellspacing="0">
	<tr>
		<td nowrap="nowrap" class="bottom">&nbsp;</td>
		<td class="bottom">&nbsp;</td>
	</tr>
	<tr>
		<td nowrap="nowrap" class="left"><b>Product Name :</b></td>
		<td class="right"><input type="text" name="data1[product_name]" size="40" style="width:300px" value="<?php echo htmlspecialchars($product['product_name']) ?>" /></td>
	</tr>
	<tr>
		<td nowrap="nowrap" class="left"><b>Product Type :</b></td>
		<td class="right">
			<select name="data1[type]" style="width:300px;">
<?php
$result = $DB->query("SELECT id, name FROM product_types WHERE parent = '$parent' ORDER BY name ASC");
while ($row = $DB->fetch_array($result)) {
    echo '<option value="' . $row['id'] . '"';
    if ($id > 0) {
        echo $product['type'] == $row['id'] ? 'selected' : '';
    } else {
        echo $_GLOBALS['site_options']['default_product_type'] == $row['type_name'] ? ' selected ' : '';
    }
    echo '>' . ($row['name']) . '</option>';
}
?>
			</select>
			<a href="sections_admin.php?table=product_types&action=add" target="_blank">Add</a> | <a href="sections_admin.php?table=product_types&query=" target="_blank">Edit</a>
		</td>
	</tr>
	<tr>
		<td nowrap="nowrap" class="left"><b>Product Style :</b></td>
		<td class="right">
			<select name="data1[style]" style="width:300px">
				<option value=""></option>
<?php
$result = $DB->query("SELECT * FROM product_style ORDER BY style ASC");
while ($row = $DB->fetch_array($result)) {
    echo '<option value="' . $row['id'] . '"';
    if ($id > 0) {
        echo $product['style'] == $row['id'] ? 'selected' : '';
    } else {
        echo $_GLOBALS['site_options']['default_style'] == $row['style'] ? ' selected ' : '';
    }
    echo '>' . ($row['style']) . '</option>';
}
?>
			</select>
			<a href="/webadmin/generic_admin.php?table=product_style&action=add" target="_blank">Add</a> | <a href="/webadmin/generic_admin.php?table=product_style&query=" target="_blank">Edit</a>
		</td>
	</tr>
	<tr>
		<td nowrap="nowrap" class="left"><b>Manufacturer :</b></td>
		<td class="right">
			<select name="data1[manufacturer]" style="width:300px">
				<option value=""></option>
<?php
$result = $DB->query("SELECT * FROM manufacturers ORDER BY manufacturer ASC");
while ($row = $DB->fetch_array($result)) {
    echo '<option value="' . $row['id'] . '"';

    if ($id > 0) {
        echo $product['manufacturer'] == $row['id'] ? 'selected' : '';
    } else {
        echo $_GLOBALS['site_options']['default_manufacturer'] == $row['manufacturer'] ? ' selected ' : '';
    }

    echo '>' . ($row['manufacturer']) . '</option>';
}
?>
			</select>
			<a href="/webadmin/generic_admin.php?table=manufacturers&action=add" target="_blank">Add</a> | <a href="/webadmin/generic_admin.php?table=manufacturers&query=" target="_blank">Edit</a>
		</td>
	</tr>
	<tr>
		<td nowrap="nowrap" class="left"><b>Year Released :</b></td>
		<td class="right">
			<select name="data1[year_released]" style="width:300px">
				<option value=""></option>
<?php
$result = $DB->query("SELECT * FROM year_released ORDER BY year_released DESC");
while ($row = $DB->fetch_array($result)) {
    echo '<option value="' . $row['id'] . '"';

    if ($id > 0) {
        echo $product['year_released'] == $row['id'] ? 'selected' : '';
    } else {
        echo $_GLOBALS['site_options']['default_year_released'] == $row['year_released'] ? ' selected ' : '';
    }
    echo '>' . ($row['year_released']) . '</option>';
}
?>
			</select>
			<a href="/webadmin/generic_admin.php?table=year_released&action=add" target="_blank">Add</a> | <a href="/webadmin/generic_admin.php?table=year_released&query=" target="_blank">Edit</a>
		</td>
	</tr>
	<tr><td colspan="2" nowrap="nowrap" class="centre">&nbsp;</td></tr>
	<tr bgcolor="#EEEEE0">
		<td nowrap="nowrap" class="left"><b>Condition :</b></td>
		<td class="right"><select name="data1[condition]" style="width:300px;">
			<option value=""></option>
<?php
$result = $DB->query("SELECT * FROM product_conditions");
while ($row = $DB->fetch_array($result)) {
    echo '<option value="' . $row['id'] . '"';

    echo $product['condition'] == $row['id'] ? ' selected ' : '';

    echo '>' . ($row['condition']) . '</option>';
}
?>
		</select></td>
	</tr>
	<tr bgcolor="#EEEEE0">
		<td nowrap="nowrap" class="left"><b>Storage Location :</b></td>
		<td class="right">
			<select name="data1[storage]" style="width:300px">
				<option value=""></option>
<?php
$result = $DB->query("SELECT * FROM product_storage_location ORDER BY location ASC");
while ($row = $DB->fetch_array($result)) {
    echo '<option value="' . $row['id'] . '"';
    if ($id > 0) {
        echo $product['storage'] == $row['id'] ? 'selected' : '';
    } else {
        echo $_GLOBALS['site_options']['default_storage_location'] == $row['location'] ? ' selected ' : '';
    }
    echo '>' . ($row['location']) . '</option>';
}
?>
			</select>
			<a href="/webadmin/generic_admin.php?table=product_storage_location&action=add" target="_blank">Add</a> | <a href="/webadmin/generic_admin.php?table=product_storage_location&query=" target="_blank">Edit</a>
		</td>
	</tr>
	<tr>
		<td nowrap="nowrap" class="left"><b>Country/Location :</b></td>
		<td class="right">
			<select name="data1[country_location]" style="width:300px">
				<option value=""></option>
<?php
$result = $DB->query("SELECT * FROM product_country_location ORDER BY name ASC");
while ($row = $DB->fetch_array($result)) {
    echo '<option value="' . $row['id'] . '"';
    if ($id > 0) {
        echo $product['country_location'] == $row['id'] ? 'selected' : '';
    } else {
        echo $_GLOBALS['site_options']['default_country_location'] == $row['name'] ? ' selected ' : '';
    }
    echo '>' . ($row['name']) . '</option>';
}
?>
			</select>
			<a href="/webadmin/generic_admin.php?table=product_country_location&action=add" target="_blank">Add</a> | <a href="/webadmin/generic_admin.php?table=product_country_location&query=" target="_blank">Edit</a>
		</td>
	</tr>
	<tr>
		<td nowrap="nowrap" class="left"><b>Availability :</b></td>
		<td class="right">
			<select name="data1[available]" style="width:300px">
				<option value=""></option>
<?php
$result = $DB->query("SELECT * FROM product_availability ORDER BY availability ASC");
while ($row = $DB->fetch_array($result)) {
    echo '<option value="' . $row['id'] . '"';
    if ($id > 0) {
        echo $product['available'] == $row['id'] ? 'selected' : '';
    } else {
        echo $_GLOBALS['site_options']['default_availability'] == $row['availability'] ? ' selected ' : '';
    }
    echo '>' . ($row['availability']) . '</option>';
}
?>
			</select>
			<a href="/webadmin/generic_admin.php?table=product_availability&action=add" target="_blank">Add</a> | <a href="/webadmin/generic_admin.php?table=product_availability&query=" target="_blank">Edit</a>
		</td>
	</tr>
	<tr>
		<td nowrap="nowrap" class="left"><strong>Date Available :</strong></td>
		<td class="right">
			<input name="data1[date_available]" type="text" id="date_available" style="width:300px" value="<?php echo date("Y-m-d", time()) ?>" size="40" />
<?php
addCalendarBtn("date_available");
?>
		</td>
	</tr>
	<tr><td colspan="2" nowrap="nowrap" class="centre">&nbsp;</td></tr>
	<tr>
		<td nowrap="nowrap" class="left"><b>Short Desc :</b></td>
		<td class="right">
			<textarea name="data1[short_desc]" id="short_desc" cols="30" rows="5" style="width:350px"><?php echo htmlspecialchars($product['short_desc']) ?></textarea><br />
			<input type="button" name="copy_short_to_long" value="Copy the above short description to below" onclick="do_copy_short()" style="width:350px;" />
		</td>
	</tr>
	<tr>
		<td nowrap="nowrap" class="left"><b>Long Desc :</b></td>
		<td class="right"><textarea name="data1[long_desc]" id="long_desc" cols="30" rows="8" style="width:350px;"><?php echo htmlspecialchars($product['long_desc']) ?></textarea></td>
	</tr>
	<tr>
		<td nowrap="nowrap" class="left"><b>Product Details :</b></td>
		<td class="right"><textarea name="data1[product_details]" cols="30" rows="8" style="width:350px"><?php echo htmlspecialchars($product['product_details']) ?></textarea></td>
	</tr>
	<tr>
		<td nowrap="nowrap" class="left"><b>Notes :</b></td>
		<td class="right"><textarea name="data1[notes]" cols="30" rows="3" style="width:350px;"><?php echo htmlspecialchars($product['notes']) ?></textarea></td>
	</tr>
	<tr><td colspan="2" nowrap="nowrap" class="centre">&nbsp;</td></tr>
	<tr>
		<td nowrap="nowrap" bgcolor="#EEEEE0" class="left"><b>Admin Notes :</b><br /><br /><font color="red" size="-1">Admin notes are never<br />displayed on the main site</font></td>
		<td bgcolor="#EEEEE0" class="right"><textarea name="admin_notes" cols="30" rows="8" style="width:350px"></textarea></td>
	</tr>
	<tr><td colspan="2" nowrap="nowrap" class="centre">&nbsp;</td></tr>
<?php
$category_type_options = get_category_type();
for ($i = 0; $i < 3; $i++) {
?>
	<tr>
		<td nowrap="nowrap" class="left"><b>Related Title <?php echo ($i + 1) ?> :</b></td>
		<td class="right"><input type="text" name="related[<?php echo $i ?>][title]" size="40" style="width:300px" /> (Optional)</td>
	</tr>
	<tr>
		<td nowrap="nowrap" class="left"><b>Related Type <?php echo ($i + 1) ?> :</b></td>
		<td class="right"><select name="related[<?php echo $i ?>][type_id]" style="width:300px">
			<option value=""></option>
			<?php echo $category_type_options ?>
		</select></td>
	</tr>
	<tr><td colspan="2" nowrap="nowrap" class="centre">&nbsp;</td></tr>
<?php
}
?>
	<tr>
		<td nowrap="nowrap" class="left"><b>Dimensions :</b></td>
		<td class="right"><input type="text" name="data1[dimension]" size="40" style="width:300px" value="<?php echo htmlspecialchars($product['dimension']) ?>" /></td>
	</tr>
	<tr>
<?php
$statement = "SELECT * FROM products_dimen WHERE id = '$id'";
$result    = $DB->query($statement);
$row       = $DB->fetch_array($result);
?>
		<td nowrap="nowrap" class="left"><b>Product Dimensions(cms)</b></td>
		<td class="right">
			<b>Width</b><input type="text" size="5" name="width1" value="<?php echo $row['width'] ?>" onchange="javascript:updateIns(this,'width',0,'Dim');onChangeDimensions();" />
			<b>Depth</b><input type="text" size="5" name="depth1" value="<?php echo $row['depth'] ?>" onchange="javascript:updateIns(this,'depth',0,'Dim');onChangeDimensions();" />
			<b>Height</b><input type="text" size="5" name="height1" value="<?php echo $row['height'] ?>" onchange="javascript:updateIns(this,'height',0,'Dim');onChangeDimensions();" />
		</td>
	</tr>
	<tr>
		<td nowrap="nowrap" class="left"><b>Product Dimensions(inches)</b></td>
		<td class="right" style="background-color:#DFF7E1;">
			<b>Width</b><input type="text" size="5" name="widthIn" value="<?php echo $row['width'] ?>" onchange="javascript:updateCMS(this,'width',0,'Dim');onChangeDimensions();" />
			<b>Depth</b><input type="text" size="5" name="depthIn" value="<?php echo $row['depth'] ?>" onchange="javascript:updateCMS(this,'depth',0,'Dim');onChangeDimensions();" />
			<b>Height</b><input type="text" size="5" name="heightIn" value="<?php echo $row['height'] ?>" onchange="javascript:updateCMS(this,'height',0,'Dim');onChangeDimensions();" />
		</td>
	</tr>
	<tr><td colspan="2"><table id="packing_table" style="width:100%;" cellspacing="0">
		<tr name="packing_cloner">
			<td nowrap="nowrap" class="left"><b>Packing Dimensions(cms/kgs)</b></td>
			<td class="right">
				<b>Width</b><input type="text" size="5" name="pack_width[]" value="" onchange="javascript:updateIns(this,'width',0,'');onChangePackingDimensions();" />
				<b>Depth</b><input type="text" size="5" name="pack_depth[]" value="" onchange="javascript:updateIns(this,'depth',0,'');onChangePackingDimensions();" />
				<b>Height</b><input type="text" size="5" name="pack_height[]" value="" onchange="javascript:updateIns(this,'height',0,'');onChangePackingDimensions();" />
				<b>Weight</b><input type="text" size="5" name="pack_weight[]" value="" onchange="javascript:updateLbs(this,0,'');onChangePackingDimensions();" />
			</td>
		</tr>
		<tr name="packing_cloner_in">
			<td nowrap="nowrap" class="left"><b>Packing Dimensions(inches/lbs)</b></td>
			<td class="right" style="background-color:#DFF7E1;">
				<b>Width</b><input type="text" size="5" name="pack_width_in_0" value="" onchange="javascript:updateCMS(this,'width',0,'');onChangePackingDimensions();" />
				<b>Depth</b><input type="text" size="5" name="pack_depth_in_0" value="" onchange="javascript:updateCMS(this,'depth',0,'');onChangePackingDimensions();" />
				<b>Height</b><input type="text" size="5" name="pack_height_in_0" value="" onchange="javascript:updateCMS(this,'height',0,'');onChangePackingDimensions();"/>
				<b>Weight</b><input type="text" size="5" name="pack_weight_lb_0" value="" onchange="javascript:updateKgs(this,0,'');onChangePackingDimensions();" />
				<script type="text/javascript">
//<![CDATA[
updateCount();
//]]>
				</script>
			</td>
		</tr>
		<tr><td colspan="2" class="right" style="text-align:center;"><input type="button" name="pdim" value="Add Packing dimension" onclick="addPackingDimension();" /></td></tr>
	</table></td></tr>

<?php
$statement = "SELECT volume_weight, weight FROM products_dimen WHERE id='$id'";
$pds       = $DB->query($statement);
$pack      = $DB->fetch_array($pds)
?>
	<tr>
		<td class="left">Actual Product Weight(Kgs)</td>
		<td class="right"><input type="text" value="<?php echo $pack['weight'] ?>" name="actProdWt" size="40" style="width:300px" onchange="updateLbs(this,0,'Act');updateWeights(this.value)" /></td>
	</tr>
	<tr>
		<td class="left">Actual Product Weight (Lbs)</td>
		<td class="right"><input type="text" value="<?php echo round($pack['weight'] / 0.45, 2) ?>" name="actProdWtL" size="40" style="width:300px;" onchange="updateKgs(this,0,'Act')" /></td>
	</tr>
	<tr>
		<td class="left">Volume Weight(kgs)</td>
		<td class="right"><input type="text" value="<?php echo $pack['volume_weight'] ?>" name="volWt" size="40" style="width:300px" /></td>
	</tr>
	<tr>
		<td nowrap="nowrap" class="left"><b>Shipping Weight(Kgs)</b></td>
		<td class="right"><input type="text" name="data1[weight]" size="40" style="width:300px" value="<?php echo htmlspecialchars($product['weight']) ?>" /></td>
	</tr>
	<tr>
		<td nowrap="nowrap" class="left"><b>Increment (Kgs):</b></td>
		<td class="right"><input type="text" name="data1[weight_inc]" size="40" style="width:300px" value="<?php echo sprintf("%0.3f", $product['weight_inc']) ?>" /></td>
	</tr>
	<tr>
		<td nowrap="nowrap" class="left"><b>Cubic Metres (without crating):</b></td>
		<td class="right"><input type="text" name="data1[metres3_unpacked]" size="40" style="width:300px" value="<?php echo htmlspecialchars($product['metres3_unpacked']) ?>" /></td>
	</tr>
	<tr>
		<td nowrap="nowrap" class="left"><b>Cubic Metres (with crating):</b></td>
		<td class="right"><input type="text" name="data1[metres3_packed]" size="40" style="width:300px" value="<?php echo htmlspecialchars($product['metres3_packed']) ?>" /></td>
	</tr>

<?php
$str = 'SELECT * FROM container_load WHERE product_id=' . $id;
$res = $DB->query($str);
$row = $DB->fetch_array($res);
?>

	<tr>
		<td class="left">20ft Container Qty (without crating):</td>
		<td class="right">
			<input type="text" name="qty20_unpacked" value="" />
			<b>Approx</b><input type="radio" name="loading20_unpacked" value="approx" <?php echo ('real' != $row['freeload_20ft_type']) ? ' checked="checked"' : '' ?> />
			<b>Real</b><input type="radio" name="loading20_unpacked" value="real" <?php echo ('real' != $row['freeload_20ft_type']) ? '' : ' checked="checked"' ?> />
		</td>
	</tr>

	<tr>
		<td class="left">20ft Container Qty (with crating):</td>
		<td class="right">
			<input type="text" name="qty20_packed" value="" />
			<b>Approx</b><input type="radio" name="loading20_packed" value="approx" <?php echo ('real' != $row['contload_20ft_type']) ? ' checked="checked"' : '' ?> />
			<b>Real</b><input type="radio" name="loading20_packed" value="real" <?php echo ('real' != $row['contload_20ft_type']) ? '' : ' checked="checked"' ?> />
		</td>
	</tr>
	<tr>
		<td class="left">40ft Container Qty (without crating):</td>
		<td class="right">
			<input type="text" name="qty40_unpacked" value="" />
			<b>Approx</b><input type="radio" name="loading40_unpacked" value="approx" <?php echo ('real' != $row['freeload_40ft_type']) ? ' checked="checked"' : '' ?> />
			<b>Real</b><input type="radio" name="loading40_unpacked" value="real" <?php echo ('real' != $row['freeload_40ft_type']) ? '' : ' checked="checked"' ?> />
		</td>
	</tr>
	<tr>
		<td class="left">40ft Container Qty (with crating):</td>
		<td class="right">
			<input type="text" name="qty40_packed" value="" />
			<b>Approx</b><input type="radio" name="loading40_packed" value="approx" <?php echo ('real' != $row['contload_40ft_type']) ? ' checked="checked"' : '' ?> />
			<b>Real</b><input type="radio" name="loading40_packed" value="real" <?php echo ('real' != $row['contload_40ft_type']) ? '' : ' checked="checked"' ?> />
		</td>
	</tr>
	<tr><td colspan="2" nowrap="nowrap" class="centre">&nbsp;</td></tr>
	<tr>
		<td nowrap="nowrap" class="left"><b>Picture :</b></td>
		<td class="right">
			<input type="hidden" name="MAX_FILE_SIZE" value="<?=htmlspecialchars(convertSize(ini_get("upload_max_filesize")))?>" />
			<input name="images[image]" type="file" style="width:300px" size="40" />
		</td>
	</tr>
	<tr><td colspan="2" nowrap="nowrap" class="centre">&nbsp;</td></tr>
	<tr>
		<td nowrap="nowrap" bgcolor="#EEEEE0" class="left"><b>Supplier :</b></td>
		<td bgcolor="#EEEEE0" class="right"><select name="data2[supplier]" style="width:300px">
			<option value=""></option>
<?php
$result = $DB->query("SELECT id, supplier FROM suppliers ORDER BY supplier ASC");
while ($row = $DB->fetch_array($result)) {
    echo '<option value="' . $row['id'] . '"';
    if ($id > 0) {
        $supplier = $DB->query_first("SELECT * FROM supplier_product WHERE product_id = '$id'");
        echo $row['id'] == $supplier['supplier_id'] ? 'selected' : '';
    } else {
        echo $row['supplier'] == $_GLOBALS['site_options']['default_supplier'] ? " selected" : "";
    }
    echo '>';
    echo stripslashes($row['supplier']) . '</option>';
}
?>
		</select></td>
	</tr>
	<tr bgcolor="#EEEEE0">
		<td nowrap="nowrap" class="left"><b>Factory ID:</b><br /><font color="#FF0000" size="-1">not shown on site</font></td>
		<td class="right"><input type="text" name="data1[factory_id]" size="40" style="width:300px" value="<?php echo htmlspecialchars($product['factory_id']) ?>" /></td>
	</tr>
	<tr>
		<td nowrap="nowrap" bgcolor="#EEEEE0" class="left"><b>Cost Price :</b></td>
		<td bgcolor="#EEEEE0" class="right">
			<input type="hidden" name="prices[cost_currency]" value="<?php echo htmlspecialchars($_GLOBALS['site_options']['cost_currency']) ?>" />
			<input type="hidden" name="prices[cost_price]" value="<?php echo $product['cost_price'] ?>" />

			<input type="text" name="data1[cost_price]" id="cost_price" size="10" style="width:70px" onChange="setValue('prices[cost_price]',this.value);convert(this.form.cost_price, this.form.cost_currency, this.form.approx_cost_price, this.form.approx_cost_currency);doMarkup();setRate(this.form.approx_cost_exch_rate, this.form.cost_currency)" value="<?php echo $product['cost_price'] ?>" />
			<select name="data1[cost_currency]" style="width:230px" id="cost_currency" onChange="setValue('prices[cost_currency]',this.value);convert(this.form.cost_price, this.form.cost_currency, this.form.approx_cost_price, this.form.approx_cost_currency);setRate(this.form.approx_cost_exch_rate, this.form.cost_currency);">
<?php
$result = $DB->query("SELECT * FROM `highwaygroup`.`currency` ORDER BY `name` ASC");
while ($row = $DB->fetch_array($result)) {
    echo '<option value="' . $row['code'] . '"';
    if ($id > 0) {
        $row['code'] == $product['cost_currency'] ? 'selected' : '';
    } else {
        echo $row['code'] == $_GLOBALS['site_options']['cost_currency'] ? ' selected="selected"' : "";
    }
    echo '>';
    echo strtoupper($row['code']) . ' - ' . $row['name'] . '</option>';
}
?>
			</select>
		</td>
	</tr>
	<tr>
		<td nowrap="nowrap" bgcolor="#EEEEE0" class="left"><b>Approx Cost Price :</b></td>
		<td bgcolor="#EEEEE0" class="right">
			<input type="hidden" name="data1[approx_cost_exch_rate]" id="approx_cost_exch_rate" value="" />
			<input type="text" name="approx_cost_price" size="10" id="approx_cost_price" style="width:70px" disabled="disabled" onChange="doMarkup();note_changes();" />
			USD
			<select name="data1[approx_cost_currency]" id="approx_cost_currency" style="width:230px; display: none;" onChange="convert(this.form.cost_price, this.form.cost_currency, this.form.approx_cost_price, this.form.approx_cost_currency);doMarkup();">
<?php
$result = $DB->query("SELECT * FROM `highwaygroup`.`currency` ORDER BY `name` ASC");
while ($row = $DB->fetch_array($result)) {
    echo '<option value="' . $row['code'] . '"';
    echo $row['code'] == $_GLOBALS['site_options']['approx_cost_currency'] ? ' selected="selected"' : "";
    echo '>';
    echo strtoupper($row['code']) . ' - ' . $row['name'] . '</option>';
}
?>
			</select>
		</td>
	</tr>

	<script type="text/javascript">
//<![CDATA[
var fValue = document.admin_form.cost_price;
var fCurrency = document.admin_form.cost_currency;
var tValue = document.admin_form.approx_cost_price;
var tCurrency = document.admin_form.approx_cost_currency;
convert(fValue, fCurrency, tValue, tCurrency);
//]]>
	</script>


	<tr><td colspan="2" nowrap="nowrap" class="centre">&nbsp;</td></tr>
	<tr><td colspan="2" nowrap="nowrap" bgcolor="#EEEEE0"><b>Pricing</b></td></tr>


	<tr>
		<td nowrap="nowrap" bgcolor="#EEEEE0" class="left"><b>Cost Price :</b></td>
		<td bgcolor="#EEEEE0" class="right">
			<input type="hidden" name="" value="" />
			<input type="text" name="" size="10" id="pricing_cost_price" style="width:70px" disabled="disabled"/>
			USD
		</td>
	</tr>

	<tr>
		<td nowrap="nowrap" class="left"><b>Sales Price :</b></td>
		<td class="right">
			<input type="text" name="data1[sales_price]" id="sales_price" size="10" style="width:70px;" onchange="doMarkup();note_changes();" value="<?php echo $product['sales_price'] ?>" />
			USD
		</td>
	</tr>

	<tr>
		<td nowrap="nowrap" class="left"><b>Profit :</b></td>
		<td class="right">
			<input type="text" id="profit_price" size="10" style="width:70px;" readonly="" />
			USD
		</td>
	</tr>

	<script type="text/javascript">
		// Bad Bad Code Hack
		var cost_price = parseFloat(document.getElementById('approx_cost_price').value);
		var sales_price = parseFloat(document.getElementById('sales_price').value);
		var profit = sales_price - cost_price;
		if (isNaN(profit)) {
			profit = 0;
		}
		profit = profit.toFixed(2)
		document.getElementById('profit_price').value=parseFloat(profit);
		document.getElementById('pricing_cost_price').value=cost_price;
	</script>

	<tr>
		<td nowrap="nowrap" bgcolor="#EEEEE0" class="left"><b>Markup % :</b></td>
		<td bgcolor="#EEEEE0" class="right"><input type="text" name="data1[markup]" id="markup" size="10" style="width:70px;" onchange="reverseMarkup();note_changes();" value="<?php echo $product['markup'] ?>" /> %</td>
	</tr>
	<tr>
		<td nowrap="nowrap" class="left"><b>Minimum Order Qty:</b></td>
		<td class="right"><input type="text" name="data1[min_order]" size="10" style="width:70px" value="<?php echo $product['min_order'] ?>" /></td>
	</tr>






	<!--tr>
		<td nowrap="nowrap" class="left"><b>Sales Price :</b></td>
		<td class="right">
			<input type="hidden" name="prices[sales_currency]" value="<?php echo htmlspecialchars($_GLOBALS['site_options']['sales_currency']) ?>" />
			<input type="hidden" name="prices[sales_price]" value="<?php echo $product['sales_price'] ?>" />

			<input type="text" name="data1[sales_price]" id="sales_price" size="10" style="width:70px;" onChange="setValue('prices[sales_price]',this.value);doMarkup();setRate(this.form.approx_cost_exch_rate, this.form.cost_currency);" value="<?php echo $product['sales_price'] ?>" />
			<select name="data1[sales_currency]" id="sales_currency" style="width:230px;" onChange="setValue('prices[sales_currency]',this.value);doMarkup();setAllValues('show_sales_currency',this.value);">
<?php
$result = $DB->query("SELECT * FROM `highwaygroup`.`currency` ORDER BY `name` ASC");
while ($row = $DB->fetch_array($result)) {
    echo '<option value="' . $row['code'] . '"';
    echo $row['code'] == $_GLOBALS['site_options']['sales_currency'] ? " selected" : "";
    echo '>';
    echo strtoupper($row['code']) . ' - ' . $row['name'] . '</option>';
}
?>
			</select>
		</td>
	</tr>
	<tr>
		<td nowrap="nowrap" bgcolor="#EEEEE0" class="left"><b>Markup % :</b></td>
		<td bgcolor="#EEEEE0" class="right"><input type="text" name="data1[markup]" id="markup" size="10" style="width:70px;" onChange="reverseMarkup();note_changes();" value="<?php echo $product['markup'] ?>" /> %</td>
	</tr>
<?php
$options = array('y' => 'Yes', 'n' => 'No');
if (in_array($_GET['parent'], array('4', '1'))) {
    $options = array_reverse($options, true);
}

?>

	<tr>
		<td nowrap="nowrap" class="left"><b>Price Flux :<br /><span style="font-size:11px;color:red;">NB: If Cost and Sales currency<br />are the same, then Price Flux<br />should always be "No"</span></b></td>
		<td class="right"><table cellpadding="0" cellspacing="0" border="0" width="100%"><tr>
			<td width="20%"><select name="data1[price_flux]" id="price_flux" style="width:70px;" onChange="note_changes();">
				<option value="y"<?php echo ('y' == $product['price_flux']) ? ' selected="selected"' : '' ?>>Yes</option>
				<option value="n"<?php echo ('y' != $product['price_flux']) ? ' selected="selected"' : '' ?>>No</option>
			</select></td>
			<td>&nbsp;&nbsp;Allow automatic price fluctuation <br />&nbsp;&nbsp;according to curreny exchanges</td>
		</tr></table></td>
	</tr>
	<tr><td colspan="2" nowrap="nowrap" class="centre">&nbsp;</td></tr>
	<tr>
		<td nowrap="nowrap" class="left"><b>Unit Price:</b></td>
		<td class="right">
			<input type="text" name="data1[sample_price]" size="10" style="width:70px;" value="<?php echo $product['sample_price'] ?>" />
			<select name="data1[sample_currency]" id="sample_currency" style="width:230px;">
<?php
$result = $DB->query("SELECT * FROM `highwaygroup`.`currency` ORDER BY `name` ASC");
while ($row = $DB->fetch_array($result)) {
    echo '<option value="' . $row['code'] . '"';
    echo $row['code'] == $_GLOBALS['site_options']['sales_currency'] ? ' selected="selected"' : "";
    echo '>';
    echo strtoupper($row['code']) . ' - ' . $row['name'] . '</option>';
}
?>
			</select>
		</td>
	</tr>
	<tr style="display: none;">
		<td nowrap="nowrap" class="left"><b>Sample Qty:</b></td>
		<td class="right"><input type="text" name="data1[sample_qty]" size="10" style="width:70px" value="<?php echo $product['sample_qty'] ?>" /></td>
	</tr>
	<tr>
		<td nowrap="nowrap" class="left"><b>Minimum Order Qty:</b></td>
		<td class="right"><input type="text" name="data1[min_order]" size="10" style="width:70px" value="<?php echo $product['min_order'] ?>" /></td>
	</tr>
	<tr style="display: none;">
		<td nowrap="nowrap" class="left"><b>Qty :</b></td>
		<td class="right"><input type="text" name="data1[qty]" size="10" style="width:70px" value="1" /></td>
	</tr-->


	<tr><td colspan="2" nowrap="nowrap" class="centre">&nbsp;</td></tr>
<?php
include 'templates/products_bulkpricing.tpl.php';
?>
	<tr><td colspan="2" nowrap="nowrap" class="centre">&nbsp;</td></tr>
	<tr>
		<td class="left"><b>Display Price Online :</b></td>
		<td class="right"><select name="data1[price_online]">
			<?=formatHTMLOptions($options, $product['price_online'])?>
		</select></td>
	</tr>
	<tr>
            	<td class="left"><b>Stock Type :</b></td>
                <td class="right">
                        <input type="radio" name="data1[stock_type]" <?=('stock_for_sale' == $product['stock_type']) ? 'checked' : ''?> value="stock_for_sale"> Stock for sale
                        <input type="radio" name="data1[stock_type]" <?=('consignment' == $product['stock_type']) ? 'checked' : ''?> value="consignment"> Consignment
                        <input type="radio" name="data1[stock_type]" <?=('stock_sample' == $product['stock_type']) ? 'checked' : ''?> value="stock_sample"> Stock Sample
                        <input type="radio" name="data1[stock_type]" <?=('repair_item' == $product['stock_type']) ? 'checked' : ''?> value="repair_item"> Repair Item
                </td>
        </tr>
	<tr>
		<td nowrap="nowrap" class="left"><b>Active :</b></td>
		<td class="right"><select name="data1[active]" style="width:100%;">
			<option value="y"<?php echo ('y' == $product['active']) ? ' selected="selected"' : '' ?>>Yes</option>
			<option value="n"<?php echo ('n' == $product['active']) ? ' selected="selected"' : '' ?>>No</option>
		</select></td>
	</tr>
	<tr><td colspan="2" class="top">&nbsp;</td></tr>
	<tr>
		<td align="right"><input type="radio" name="go_next" value="true"<?php echo ('true' == $go_next) ? ' checked="checked"' : '' ?> /></td>
		<td>Add more products</td>
	</tr>
	<tr>
		<td align="right"><input type="radio" name="go_next" value="false"<?php echo ('true' != $go_next) ? ' checked="checked"' : '' ?> /></td>
		<td>Go back to previous page</td>
	</tr>
	<tr>
		<td nowrap="nowrap">&nbsp;</td>
		<td><p><input type="hidden" name="action" value="do_add" /> <input type="submit" name="do_action" value="Add" /> <input type="reset" name="clear" value="Clear" /></p></td>
	</tr>
</table>

<script type="text/javascript">
//<![CDATA[
$(function() {
	$("[name='data1[price_online']").change(function() {
    	$(this).css({
            "background": ("n" == $(this).val()) ? "pink" : "",
        });
	}).change();
});
//]]>
</script>

<!-- /add_form.inc.php -->
