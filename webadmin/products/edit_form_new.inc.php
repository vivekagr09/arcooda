<!--
	Created and modified by Mingze Xi@ Feb 2, 2015

	This edit form is based on currently used edit_form.inc.php
	The newly modified version is upgrading old product pages to AGS version. e.g. https://www.arcade-game-sales.com/webadmin/products/products_edit.php?site_id=2&product_type=ind&active=&product_id=11704&sub_id=5
-->
<?php
$id = intval($id);
$product_bulkpricing = productBulkPricingFromDatabase($id);
show_locked_msg($table, $id);
if ($id > 0)
{
	include_once ($_SERVER['DOCUMENT_ROOT'] . '/webadmin/functions/formatting.inc.php');
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
?>
<script type="text/javascript">
//<![CDATA[
function getElement(name)
{
	var elem=document.getElementById(name);
	if(elem) return elem;
	elem=document.getElementsByName(name);
	if(elem.length>0) return elem[0];
	if(document.all!=null)
	{
		for(var key in document.all)
		{
			if(document.all[key].id==name) return document.all[key];
			if(document.all[key].name==name) return document.all[key];
		}
	}
	return null;
}
function toggleSection(sectionName,force)
{
	var theSect=getElement(sectionName);
	var hideNote=getElement(sectionName+'_hideNote');
	if(theSect==null) return;
	var newDisplay=(theSect.style.display=='none');
	if(force!=null) newDisplay=force;
	if(newDisplay==true)
	{
		theSect.style.display='';
		if(hideNote!=null) hideNote.style.display='none';
	}
	else
	{
		theSect.style.display='none';
		if(hideNote!=null) hideNote.style.display='';
	}
}
function addAttachWindow()
{
	var win=window.open(<?=json_encode('attachment_admin.php?action=add&prod_id=' . $product_id . '&prod_name=' . urlencode($product['product_name']))?>);
}
function removeAttachWindow(img)
{
	window.open('addimage.php?product_id=<?php echo $product_id ?>&attachment_id='+img+'&action=remove','', 'toolbar=0,scrollbars=0,location=0,statusbar=0,menubar=0,resizable=0,width=450,height=200,left=440,top=312');
}
function editAttachment(id)
{
	window.open("attachment_admin.php?action=edit&id="+id);
}
function delAttachment(img)
{
	var theRow=getElement("attach["+img+"]");
	var att_table=getElement("attach_list");
	var foot = getElement("attach_foot");
	if(theRow)	theRow.parentNode.deleteRow(theRow.sectionRowIndex);
	if(att_table.rows.length%2==1)
		foot.style.backgroundColor="#FFFFFF";
	else
		foot.style.backgroundColor="#EEEEEE";
}
function delQuoteAttachment(id,method)
{
	var theRow = '';
	if(method=='Courier')
		theRow=getElement("quote_courier_attach["+id+"]");
	else
		theRow=getElement("quote_attach["+id+"]");

	if(theRow.hasChildNodes()) theRow.parentNode.deleteRow(theRow.sectionRowIndex);
}
function addAttachment(img,desc,orig,filetype)
{
	//alert('Vars Passed = '+img+', '+desc+', '+orig+', '+filetype);
	var att_table=getElement("attach_list");
	var foot = getElement("attach_foot");
	//alert(foot);
	if(att_table==null) return;

	if(orig==null) orig=desc;
	if(filetype==null) filetype='---';

	var newRow = '';
	newRow=addTableRow(att_table,"attach["+img+"]");
	var href = 'viewimage.php?product_id=<?php echo $product_id ?>&attachment_id='+img;
	var src = 'viewimage.php?product_id=<?php echo $product_id ?>&attachment_id='+img+'&thumb=true';

	var newCol = addTableCell(newRow,'<a href="'+href+'" alt="'+orig+'" target="_blank"><img border="0" src="'+src+'" alt="'+orig+'"></a>');
	newCol.className="bottomLeft";
	newCol.align="center";
	newCol = addTableCell(newRow,'<a href="'+href+'" target="_blank">'+desc+'</a>');
	newCol.className="bottomLeft";
	newCol.align="left";
	newCol = addTableCell(newRow,'<?php echo date("d/m/y", strtotime($time_now)) ?>');
	newCol.className="bottomLeft";
	newCol.align="center";
	newCol = addTableCell(newRow,filetype);
	newCol.className="bottomLeft";
	newCol.align="center";
	newCol = addTableCell(newRow,'<input type="button" onclick="javascript:removeAttachWindow('+img+');" value="Remove" />');
	newCol.className="bottomLeftRight";
	newCol.align="center";

	if(att_table.rows.length%2==1)
	{
		newRow.style.backgroundColor="#EEEEEE";
		foot.style.backgroundColor="#FFFFFF";
	}
	else
		foot.style.backgroundColor="#EEEEEE";
}
//Function to find the first occuring element with the name/id given
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
	//	if(theDimensions!=null)
	//		theDimensions.value='W'+theWidth.value+'0 x D'+theDepth.value+'0 x H'+theHeight.value+'0 mm';
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
		volume_weight+=inWidths[i].value*inDepths[i].value*inHeights[i].value/5000.0;
	var shipping_weight=0.0;
	for(var i=0;i<inWidths.length;i++)
		shipping_weight+=Number(inWeights[i].value);
	if(outCbm_Packed)		outCbm_Packed.value=cbm_packed;
	if(outVolume_Weight)	outVolume_Weight.value=volume_weight;
	if(outShipping_Weight)	outShipping_Weight.value=shipping_weight;
}
//Function to update all the metric packed dimensions
function updateMetricPacked()
{
	var theWidthsC=document.getElementsByName('pack_width[]');
	var theWidthsI=document.getElementsByName('pack_widthIns[]');
	var theHeightC=document.getElementsByName('pack_height[]');
	var theHeightI=document.getElementsByName('pack_heightIns[]');
	var theDepthsC=document.getElementsByName('pack_depth[]');
	var theDepthsI=document.getElementsByName('pack_depthIns[]');
	var theWeightK=document.getElementsByName('pack_weight[]');
	var theWeightL=document.getElementsByName('pack_weightIns[]');
	for(var i=0;i<theWidthsC.length;i++)
		theWidthsC[i].value=Number(theWidthsI[i].value*2.54);
	for(var i=0;i<theHeightC.length;i++)
		theHeightC[i].value=Number(theHeightI[i].value*2.54);
	for(var i=0;i<theDepthsC.length;i++)
		theDepthsC[i].value=Number(theDepthsI[i].value*2.54);
	for(var i=0;i<theWeightK.length;i++)
		theWeightK[i].value=Math.round(Number(theWeightL[i].value/0.45));
}
//Function to update all the imperial packed dimensions
function updateImperialPacked()
{
	var theWidthsC=document.getElementsByName('pack_width[]');
	var theWidthsI=document.getElementsByName('pack_widthIns[]');
	var theHeightC=document.getElementsByName('pack_height[]');
	var theHeightI=document.getElementsByName('pack_heightIns[]');
	var theDepthsC=document.getElementsByName('pack_depth[]');
	var theDepthsI=document.getElementsByName('pack_depthIns[]');
	var theWeightK=document.getElementsByName('pack_weight[]');
	var theWeightL=document.getElementsByName('pack_weightIns[]');
	for(var i=0;i<theWidthsI.length;i++)
		theWidthsI[i].value=Math.round(Number(theWidthsC[i].value/2.54));
	for(var i=0;i<theHeightI.length;i++)
		theHeightI[i].value=Math.round(Number(theHeightC[i].value/2.54));
	for(var i=0;i<theDepthsI.length;i++)
		theDepthsI[i].value=Math.round(Number(theDepthsC[i].value/2.54));
	for(var i=0;i<theWeightL.length;i++)
		theWeightL[i].value=Number(theWeightK[i].value*0.45);
}
//Function to update all the metric unpacked dimensions
function updateMetricUnpacked()
{
	getElement('width1').value=Number(getElement('widthIn').value*2.54);
	getElement('height1').value=Number(getElement('heightIn').value*2.54);
	getElement('depth1').value=Number(getElement('depthIn').value*2.54);
	getElement('actProdWt').value=Math.round(Number(getElement('actProdWtL').value/0.45));
}
//Function to update all the imperial unpacked dimensions
function updateImperialUnpacked()
{
	getElement('widthIn').value=Math.round(Number(getElement('width1').value/2.54));
	getElement('heightIn').value=Math.round(Number(getElement('height1').value/2.54));
	getElement('depthIn').value=Math.round(Number(getElement('depth1').value/2.54));
	getElement('actProdWtL').value=Number(getElement('actProdWt').value*0.45);
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
var count=0;
function addPackingDimension()
{
	//	var theCopyRow=document.getElementById('packing_cloner');
	//	var theCloneRow=theCopyRow.cloneNode(theCopyRow.rowIndex);
	//	theCopyRow.parentNode.insertBefore(theCloneRow,theCopyRow);
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
function calcHeight()
{
	var the_frame=getElement("the_iframe");
	if(the_frame==null) return;
	var the_height=the_frame.contentWindow.document.body.scrollHeight;
	if(the_height<50) the_height=50;
	the_frame.style.height=the_height+15;
}
function addTableCell(tableRow,innerStuff)
{
	var newCol=document.createElement('td');
	newCol.innerHTML=innerStuff;
	tableRow.appendChild(newCol);
	return newCol;
}
function addTableRow(theTable,newName)
{
	var newRow=theTable.insertRow(theTable.rows.length);
	newRow.name=newName;
	newRow.id=newName;
	return newRow;
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
	var cost=document.admin_form.cost_price.value;
	var approx_cost = document.admin_form.approx_cost_price.value;
	var sales = document.admin_form.sales_price.value;
	var cost_currency = document.admin_form.cost_currency.value;
	var approx_currency = document.admin_form.approx_cost_currency.value;
	var sales_currency = document.admin_form.sales_currency.value;

	var price_flux=document.admin_form.price_flux.value;

	var markup = document.admin_form.markup.value;
	markup = parseFloat(markup/100);

	if (markup == 0)
	{
		doMarkup();
		return;
	}

	//if (approx_currency == sales_currency )
	if(cost_currency != sales_currency && price_flux=='y')
	{
		var newCost=convertCurrency(cost,cost_currency,sales_currency);
		var newSales = newCost * (1 + markup)*100;
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
	}
?>

<!--tables here-->

<?php
include 'templates/products_summary.tpl.php';
include 'templates/products_about.tpl.php';
include 'templates/products_screenshots.tpl.php';
include 'templates/products_description.tpl.php';
include 'templates/products_manuals.tpl.php';
include 'templates/products_power.tpl.php';
include 'templates/products_shipping.tpl.php';
include 'templates/products_adminnotes.tpl.php';
include 'templates/products_pricing2.tpl.php';
//include 'templates/products_bulkpricing_h.tpl.php';
include 'templates/products_changes.tpl.php';
include 'templates/products_status.tpl.php';
?>

<!-- /edit_form_new.inc.php -->
