<!-- edit_form.inc.php -->

<?php
global $_GLOBALS;

if (!function_exists('getUStoRate')) {
    function getUStoRate($code = 'USD')
    {
        global $DB;
        if ('USD' == $code) {
            return '1.000000';
        }
        if ($row = $DB->query_first(sprintf(
            "SELECT `usto` FROM `highwaygroup`.`currency` WHERE `code` = %s LIMIT 1",
            formatSQLString($code)
        ), MYSQLI_ASSOC)) {
            return $row['usto'];
        }
        return '0.000000';
    }
}
$id                  = intval($id);
$product_bulkpricing = productBulkPricingFromDatabase($id);
$product_bulkweights = productBulkWeightsFromDatabase($id);
show_locked_msg($table, $id);
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

    $productUrl = "";

    $productType = $DB->query_first('SELECT * FROM `product_types` WHERE `id` = ' . $product['type'], MYSQLI_ASSOC);

    $productTypeName = strtolower(str_replace(' ', '_', $productType['name']));

    $productCatChunk = explode('_', $productType['img_location']);
    $productCat      = strtolower(end($productCatChunk));

    $productUrl = "/products/" . $productCat . "/" . $productTypeName . "/" . str_replace(' ', '_', $product['product_name']) . '_' . $product['id'] . '.html';

    $productNameChunk = explode(" ", $product['product_name']);
?>
<script type="text/javascript">
//<![CDATA[

function SEOCheckDesc(type, input, productName) {

	// Some quick hack for html entities.
	productName = productName.replace(/&amp;/g, '&');
	productName = productName.replace(/&rsquo;/g, '’');
	productName = productName.replace(/&lt;/g, '<');
	productName = productName.replace(/&gt;/g, '>');
	productName = productName.replace(/&quot;/g, '"');
	productName = productName.replace(/&apos;/g, '\'');

	var productNameChunk = productName.split(" ");

	var totalTicked = 0;

	for(var i = 0; i < productNameChunk.length; i++) {
		if (input.value.match(new RegExp(productNameChunk[i], 'i'))) {
			document.getElementById(type + '-desc-keyword-' + i).style.color = "gray";
			totalTicked++;
		} else {
			document.getElementById(type + '-desc-keyword-' + i).style.color = "red";
		}
	}

	if (totalTicked == productNameChunk.length) {
		document.getElementById(type + '-desc-status').innerHTML = "Keywords all good";
		document.getElementById(type + '-desc-status').style.color = "green";
	} else {
		document.getElementById(type + '-desc-status').innerHTML = "Keywords still not used";
		document.getElementById(type + '-desc-status').style.color = "black";
	}
}

var prevNode;

window.addEventListener("load",function(){
    document.getElementById("data1[active]").className="greenText";
},false);

/*function rebuild(tab)
{
	if(tab==null) tab='';
	var table=getElement("screenshots"+tab);
	var nodes = table.getElementsByTagName("tr");

	if(nodes.length > 1) {
		for(i=0;i<nodes.length;i++) {
			var node = nodes[i];
			var imgs = node.getElementsByTagName("img");

			//node.style.backgroundColor = (i%2==1) ? "#EEEEE0" : "#FFFFFF";

			imgs[0].style.visibility = ((i==0) ? 'visible' : ((i==(nodes.length-1)) ? 'hidden' : 'visible'));
			imgs[1].style.visibility = ((i==0) ? 'hidden' : ((i==(nodes.length-1)) ? 'visible' : 'visible'));
		}
	}
	else {
		node = nodes[0];
		var imgs = node.getElementsByTagName("img");
		//node.style.backgroundColor = "#FFFFFF";
		imgs[0].style.visibility='hidden';
		imgs[1].style.visibility='hidden';
	}

	updateRankOrder(tab);
}

function updateRankOrder(tab)
{
	if(tab==null) tab='';
	var table=getElement("screenshots"+tab);
	var nodes = table.getElementsByTagName("tr");
	var ranking = new Array(1);

	for(i=0;i<nodes.length;i++)
		if(nodes[i].getAttribute("ssid") != null)
			ranking[i] = nodes[i].getAttribute("ssid");
	setValue("ss_order"+tab,ranking.join(","));
}

function moveUp(node,table)
{
	if(table==null) table='';
	var newNode = node.cloneNode("true");
	newNode.setAttribute("position", (newNode.getAttribute("position") - 1));

	var prevRow = node_before(node);
	var prevRowNode = prevRow.cloneNode("true");
	prevRowNode.setAttribute("position", (parseInt(prevRowNode.getAttribute("position")) + 1.0));
	var tableNode = node.parentNode;

	var reattachee = tableNode.replaceChild( newNode, prevRow);
	var reattachee = tableNode.replaceChild(prevRowNode, node);

	rebuild(table);
}

function moveDown(node,table)
{
	if(table==null) table='';
	var newNode = node.cloneNode("true");
	newNode.setAttribute("position", (parseInt(newNode.getAttribute("position")) + 1.0));

	var nextRow = node_after(node);

	var nextRowNode = nextRow.cloneNode("true");
	nextRowNode.setAttribute("position", (nextRowNode.getAttribute("position") - 1));
	var tableNode = node.parentNode;

	var reattachee = tableNode.replaceChild( newNode, nextRow);
	var reattachee = tableNode.replaceChild(nextRowNode, node);
	rebuild(table);
}*/




function rebuild(tab, section)
{
	if(tab==null) tab='';
	var table=getElement(section + tab);
	var nodes = table.getElementsByTagName("tr");

	if(nodes.length > 1) {
		for(i=0;i<nodes.length;i++) {
			var node = nodes[i];
			var imgs = node.getElementsByTagName("img");

			//node.style.backgroundColor = (i%2==1) ? "#EEEEE0" : "#FFFFFF";

			imgs[0].style.visibility = ((i==0) ? 'visible' : ((i==(nodes.length-1)) ? 'hidden' : 'visible'));
			imgs[1].style.visibility = ((i==0) ? 'hidden' : ((i==(nodes.length-1)) ? 'visible' : 'visible'));
		}
	}
	else {
		node = nodes[0];
		var imgs = node.getElementsByTagName("img");
		//node.style.backgroundColor = "#FFFFFF";
		imgs[0].style.visibility='hidden';
		imgs[1].style.visibility='hidden';
	}

	updateRankOrder(tab, section);
}

function updateRankOrder(tab,section)
{
	var short = (section == 'videos') ? 'videos' : 'ss'
	if(tab==null) tab='';
	var table=getElement(section+tab);
	var nodes = table.getElementsByTagName("tr");
	var ranking = new Array(1);

	for(i=0;i<nodes.length;i++)
		if(nodes[i].getAttribute(short+"id") != null)
			ranking[i] = nodes[i].getAttribute(short + "id");
	setValue(short + "_order"+tab,ranking.join(","));
}

function moveUp(node,table,section)
{
	if(table==null) table='';
	var newNode = node.cloneNode("true");
	newNode.setAttribute("position", (newNode.getAttribute("position") - 1));

	var prevRow = node_before(node);
	var prevRowNode = prevRow.cloneNode("true");
	prevRowNode.setAttribute("position", (parseInt(prevRowNode.getAttribute("position")) + 1.0));
	var tableNode = node.parentNode;

	var reattachee = tableNode.replaceChild( newNode, prevRow);
	var reattachee = tableNode.replaceChild(prevRowNode, node);

	rebuild(table,section);
}

function moveDown(node,table,section)
{
	if(table==null) table='';
	var newNode = node.cloneNode("true");
	newNode.setAttribute("position", (parseInt(newNode.getAttribute("position")) + 1.0));

	var nextRow = node_after(node);

	var nextRowNode = nextRow.cloneNode("true");
	nextRowNode.setAttribute("position", (nextRowNode.getAttribute("position") - 1));
	var tableNode = node.parentNode;

	var reattachee = tableNode.replaceChild( newNode, nextRow);
	var reattachee = tableNode.replaceChild(nextRowNode, node);
	rebuild(table,section);
}


function deleteVideo(node, id)
{
	var videoId = document.getElementById('delete-video-input').value;

	if (videoId.length > 0) {
		videoId += ",";
	}

	videoId += id;

	setValue('delete_video_id', videoId);

	node.parentNode.parentNode.parentNode.removeChild(node.parentNode.parentNode);
}


function is_all_ws( nod )
{
	return !(/[^\t\n\r ]/.test(nod.data));
}

function is_ignorable( nod )
{
	return	( nod.nodeType == 8) || // A comment node
			( (nod.nodeType == 3) && is_all_ws(nod) ); // a text node, all ws
}

function node_before( sib )
{
	while ((sib = sib.previousSibling)) {
		if (!is_ignorable(sib)) return sib;
	}
	return null;
}

function node_after( sib )
{
	while ((sib = sib.nextSibling)) {
		if (!is_ignorable(sib)) return sib;
	}
	return null;
}

function last_child( par )
{
	var res=par.lastChild;
	while (res) {
		if (!is_ignorable(res)) return res;
		res = res.previousSibling;
	}
	return null;
}

function first_child( par )
{
	var res=par.firstChild;
	while (res) {
		if (!is_ignorable(res)) return res;
		res = res.nextSibling;
	}
	return null;
}

//============================================================Screenshot ranking methods End ========================================================\\

function editScreenshot(product_id, ss_id)
{
	openWindow('screenshot_admin.php?popup&table=product_screenshots&action=edit&ss_id='+ ss_id +'&id='+ product_id,700,300,true);
}

function deleteScreenshot(ss_id)
{
	openWindow('screenshot_admin.php?popup&table=product_screenshots&action=delete&ss_id='+ ss_id,700,300,true);
}

function addScreenshot(product_id)
{
	openWindow('screenshot_admin.php?popup&table=product_screenshots&action=add&id='+ product_id,700,300,true);
}

function addPromotion()
{
	openWindow('promotion_admin.php?popup&action=add',700,300,true);
}

function editPromotion()
{
	var promotionValue = document.getElementById('promotion-input').value;
	openWindow('promotion_admin.php?popup&action=edit&id=' + promotionValue, 700, 300, true);
}

function reloadPromotion(options) {
	console.log(options);
}

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
	var win=window.open(<?=json_encode('attachment_admin.php?action=add&prod_id=' . $product_id . '&prod_name=' . urlencode($product['product_name'])) ?>);
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

function styleActiveSelector() {
	document.getElementById('data1[active]').style.backgroundColor=((document.getElementById('data1[active]').value=='n')?'#FF0000':'#00FF00');
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
?>
<table border="0" align="center" cellpadding="4" cellspacing="0">

	<tr>
		<td nowrap="nowrap" class="bottom">&nbsp;</td>
		<td class="bottom">&nbsp;</td>
	</tr>

	<tr><td colspan="2" nowrap="nowrap" bgcolor="#EEEEE0"><b>Product Summary</b></td></tr>

	<tr>
		<td colspan="2">
			<table align="center" cellpadding="4" cellspacing="0" border="0" style="width: 100%;">
				<tbody>
					<tr>
						<td style="width:18%;" class="left">Product ID :</td>
						<td style="width:32%;" class="right"><a target="_blank" href="<?=$productUrl?>"><?=$product['id']?></a></td>
						<td style="width:18%;" class="left">Supplier :</td>
						<td style="width:32%;" class="right">
<?php
$supplier_product = $DB->query_first("SELECT * FROM supplier_product WHERE product_id = " . $product['id'], MYSQLI_ASSOC);
    if ($supplier && $supplier_product['supplier_id']) {
        $supplier = $DB->query_first(sprintf(
            "SELECT `supplier` FROM `suppliers` WHERE `id` = %d",
            $supplier_product['supplier_id']
        ), MYSQLI_ASSOC);
        echo $supplier['supplier'];
    }
?>
						</td>
					</tr>
					<tr>
						<td style="width:18%;" class="left">Product Name :</td>
						<td style="width:32%;" class="right">

<?php
$type      = encode_section_url($productType['name']);
    $item_link = encode_product_url($product['product_name'], $product['id']);
    echo "<a href=\"/products/" . $productCat . "/$type/$item_link.html?preview=true\" target=\"_blank\">" . stripslashes($product['product_name']) . "</a>";
?>
							<!-- a target="_blank" href="<?=$productUrl?>"><?=$product['product_name']?></a-->
						</td>
						<td style="width:18%;" class="left">Supplier Code :</td>
						<td style="width:32%;" class="right"></td>
					</tr>
					<tr>
						<td style="width:18%;" class="left">Cost Price (USD) :</td>
						<td style="width:32%;" class="right">$<?php echo showPriceFrom($product['cost_price'], $product['cost_currency'], $product['approx_cost_currency']); ?></td>
						<td style="width:18%;" class="left">Last Sold :</td>
						<td style="width:32%;" class="right"></td>
					</tr>
					<tr>
						<td style="width:18%;" class="left">Sale Price (USD) :</td>
						<td style="width:32%;" class="right">
							$<?php echo $product['sales_price'] ?>
						</td>
						<td style="width:18%;" class="left">Product Qty :</td>
						<td style="width:32%;" class="right"><?=$product['qty']?></td>
					</tr>
				</tbody>
			</table>
		</td>
	</tr>

	<tr><td colspan="2" nowrap="nowrap" class="centre">&nbsp;</td></tr>


	<tr>
		<td nowrap="nowrap" class="left"><b>Inventory :</b></td>
		<td class="right"><input type="text" name="data1[qty]" size="10" style="width:70px;" value="<?php echo $product['qty'] ?>" /></td>
	</tr>

	<tr><td colspan="2" nowrap="nowrap" class="centre">&nbsp;</td></tr>

	<tr>
		<td nowrap="nowrap" class="left">
<?php
$type      = encode_section_url($productType['name']);
    $item_link = encode_product_url($product['product_name'], $product['id']);
    echo "<a href=\"/products/" . $productCat . "/$type/$item_link.html?preview=true\" target=\"_blank\">Product Name</a><span> :</span>";
?>

		</td>
		<td class="right">
			<input type="text" name="data1[product_name]" size="40" style="width:300px" value="<?php echo htmlspecialchars($product['product_name']) ?>" />
		</td>
	</tr>
	<tr>
		<td nowrap="nowrap" class="left"><b>Product ID :</b></td>
		<td class="right">
<?php
$color = "";
    switch ($product['stock_type']) {
        case "consignment":
            $color = "#EEBBEE";
            break;
        case "stock_for_sale":
            $color = "#BBFFBB";
            break;
        case "stock_sample":
            $color = "#FEDC6C";
            break;
        case "repair_item":
            $color = "#EE90A0";
            break;
    }

    if ('y' == $product['replacement']) {
        $color = "#d3d3d3";
    }
?>
			<input type="text" name="" size="40" style="width:300px; background: <?=$color?>;" value="<?php echo htmlspecialchars($product['id']) ?>" readonly/>
		</td>
	</tr>
	<tr>
		<td nowrap="nowrap" class="left"><b>Product Type :</b></td>
		<td class="right">
			<select name="data1[type]" style="width:300px;">
<?php
$result = $DB->query("SELECT id, name FROM product_types WHERE parent = '$parent' ORDER BY name ASC");
    while ($row = $DB->fetch_array($result)) {
?>
				<option value="<?php echo $row['id'] ?>"<?php echo ($product['type'] == $row['id']) ? ' selected="selected"' : '' ?>><?php echo htmlspecialchars($row['name']) ?></option>
<?php
}
?>
			</select>
			<a href="sections_admin.php?table=product_types&amp;action=add" target="_blank">Add</a> | <a href="sections_admin.php?table=product_types&amp;query=" target="_blank">Edit</a>
		</td>
	</tr>
	<tr>
		<td nowrap="nowrap" class="left"><b>Product Style :</b></td>
		<td class="right">
			<select name="data1[style]" style="width:300px;">
				<option value=""></option>
<?php
$result = $DB->query("SELECT * FROM product_style ORDER BY style ASC");
    while ($row = $DB->fetch_array($result)) {
?>
				<option value="<?php echo htmlspecialchars($row['id']) ?>"<?php echo ($product['style'] == $row['id']) ? ' selected="selected"' : '' ?>><?php echo htmlspecialchars($row['style']) ?></option>
<?php
}
?>
			</select>
			<a href="/webadmin/generic_admin.php?table=product_style&amp;action=add" target="_blank">Add</a> | <a href="/webadmin/generic_admin.php?table=product_style&amp;query=" target="_blank">Edit</a>
		</td>
	</tr>
	<tr>
		<td nowrap="nowrap" class="left"><b>Manufacturer :</b></td>
		<td class="right">
			<select name="data1[manufacturer]" style="width:300px;">
				<option value=""></option>
<?php
$result = $DB->query("SELECT * FROM manufacturers ORDER BY manufacturer ASC");
    while ($row = $DB->fetch_array($result)) {
?>
				<option value="<?php echo htmlspecialchars($row['id']) ?>"<?php echo ($product['manufacturer'] == $row['id']) ? ' selected="selected"' : '' ?>><?php echo htmlspecialchars($row['manufacturer']) ?></option>
<?php
}
?>
			</select>
			<a href="/webadmin/generic_admin.php?table=manufacturers&amp;action=add" target="_blank">Add</a> | <a href="/webadmin/generic_admin.php?table=manufacturers&amp;query=" target="_blank">Edit</a>
		</td>
	</tr>
	<tr>
		<td nowrap="nowrap" class="left"><b>Year Released :</b></td>
		<td class="right">
			<select name="data1[year_released]" style="width:300px;">
				<option value=""></option>
<?php
$result = $DB->query("SELECT * FROM year_released ORDER BY year_released DESC");
    while ($row = $DB->fetch_array($result)) {
?>
				<option value="<?php echo htmlspecialchars($row['id']) ?>"<?php echo ($product['year_released'] == $row['id']) ? ' selected="selected"' : '' ?>><?php echo htmlspecialchars($row['year_released']) ?></option>
<?php
}
?>
			</select>
			<a href="/webadmin/generic_admin.php?table=year_released&amp;action=add" target="_blank">Add</a> | <a href="/webadmin/generic_admin.php?table=year_released&amp;query=" target="_blank">Edit</a>
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
?>
			<option value="<?php echo htmlspecialchars($row['id']) ?>"<?php echo ($product['condition'] == $row['id']) ? ' selected="selected"' : '' ?>><?php echo htmlspecialchars($row['condition']) ?></option>
<?php
}
?>
		</select></td>
	</tr>
	<tr bgcolor="#EEEEE0">
		<td nowrap="nowrap" class="left"><b>Storage Location :</b></td>
		<td class="right">
			<select name="data1[storage]" style="width:300px;">
				<option value=""></option>
<?php
$result = $DB->query("SELECT * FROM product_storage_location ORDER BY location ASC");
    while ($row = $DB->fetch_array($result)) {
?>
				<option value="<?php echo htmlspecialchars($row['id']) ?>"<?php echo ($product['storage'] == $row['id']) ? ' selected="selected"' : '' ?>><?php echo htmlspecialchars($row['location']) ?></option>
<?php
}
?>
			</select>
			<a href="/webadmin/generic_admin.php?table=product_storage_location&amp;action=add" target="_blank">Add</a> | <a href="/webadmin/generic_admin.php?table=product_storage_location&amp;query=" target="_blank">Edit</a>
		</td>
	</tr>
	<tr>
		<td nowrap="nowrap" class="left"><b>Country/Location :</b></td>
		<td class="right">
			<select name="data1[country_location]" style="width:300px;">
				<option value=""></option>
<?php
$result = $DB->query("SELECT * FROM product_country_location ORDER BY name ASC");
    while ($row = $DB->fetch_array($result)) {
?>
				<option value="<?php echo htmlspecialchars($row['id']) ?>"<?php echo ($product['country_location'] == $row['id']) ? ' selected="selected"' : '' ?>><?php echo htmlspecialchars($row['name']) ?></option>
<?php
}
?>
			</select>
			<a href="/webadmin/generic_admin.php?table=product_country_location&amp;action=add" target="_blank">Add</a> | <a href="/webadmin/generic_admin.php?table=product_country_location&amp;query=" target="_blank">Edit</a>
		</td>
	</tr>
	<tr>
		<td nowrap="nowrap" class="left"><b>Availability :</b></td>
		<td class="right">
			<select name="data1[available]" style="width:300px;">
				<option value=""></option>
<?php
$result = $DB->query("SELECT * FROM product_availability ORDER BY availability ASC");
    while ($row = $DB->fetch_array($result)) {
?>
				<option value="<?php echo htmlspecialchars($row['id']) ?>"<?php echo ($product['available'] == $row['id']) ? ' selected="selected"' : '' ?>><?php echo htmlspecialchars($row['availability']) ?></option>
<?php
}
?>
			</select>
			<a href="/webadmin/generic_admin.php?table=product_availability&amp;action=add" target="_blank">Add</a> | <a href="/webadmin/generic_admin.php?table=product_availability&amp;query=" target="_blank">Edit</a>
		</td>
	</tr>
	<tr>
		<td nowrap="nowrap" class="left"><strong>Date Available :</strong></td>
		<td class="right">
			<input name="data1[date_available]" type="text" id="date_available" style="width:300px;" value="<?php echo htmlspecialchars($product['date_available']) ?>" size="40" />
<?php
addCalendarBtn("date_available");

?>
		</td>
	</tr>
	<tr><td colspan="2" nowrap="nowrap" class="centre">&nbsp;</td></tr>
	<tr>
		<td nowrap="nowrap" class="left"><b>Short Desc :</b></td>
		<td class="right">
			<textarea name="data1[short_desc]" id="short_desc" cols="30" rows="5" style="width:350px;" onkeyup="SEOCheckDesc('short', this, '<?php hecho($product['product_name']);?>');"><?php echo htmlspecialchars($product['short_desc']) ?></textarea><br />

			<div>
				SEO: <span id="short-desc-status">Keywords still not used</span>

				<p style="color: red; margin: 0;">
					<?php foreach ($productNameChunk as $i => $pn) {?>
						<span id="short-desc-keyword-<?=$i?>"><?=$pn?></span>
					<?php }?>
				</p>
			</div> <br/>

			<script>
				// Check SEO
				SEOCheckDesc('short', document.getElementById('short_desc'), '<?php hecho($product['product_name']);?>');
			</script>

			<input type="button" name="copy_short_to_long" value="Copy the above short description to below" onclick="do_copy_short()" style="width:350px;" />
		</td>
	</tr>
	<tr>
		<td nowrap="nowrap" class="left"><b>Long Desc :</b></td>
		<td class="right">
			<textarea name="data1[long_desc]" id="long_desc" cols="30" rows="8" style="width:350px;" onkeyup="SEOCheckDesc('long', this, '<?php hecho($product['product_name']);?>');"><?php echo htmlspecialchars($product['long_desc']) ?></textarea>

			<div>
				SEO: <span id="long-desc-status">Keywords still not used</span>

				<p style="color: red; margin: 0;">
					<?php foreach ($productNameChunk as $i => $pn) {?>
						<span id="long-desc-keyword-<?=$i?>"><?=$pn?></span>
					<?php }?>
				</p>
			</div> <br/>

			<script>
				// Check SEO
				SEOCheckDesc('long', document.getElementById('long_desc'), '<?php hecho($product['product_name']);?>');
			</script>

		</td>
	</tr>
	<tr>
		<td nowrap="nowrap" class="left"><b>Product Details :</b></td>
		<td class="right"><textarea name="data1[product_details]" cols="30" rows="8" style="width:350px;"><?php echo htmlspecialchars($product['product_details']) ?></textarea></td>
	</tr>
	<tr>
		<td nowrap="nowrap" class="left"><b>Notes :</b></td>
		<td class="right"><textarea name="data1[notes]" cols="30" rows="3" style="width:350px;"><?php echo htmlspecialchars($product['notes']) ?></textarea></td>
	</tr>



	<tr><td colspan="2" nowrap="nowrap" class="centre">&nbsp;</td></tr>
	<tr>
		<td nowrap="nowrap" class="left"><b>Videos:</b></td>
		<td class="right">
			<input type="hidden" name="videos_order_ref">
			<input type="hidden" name="delete_video_id" id="delete-video-input">
			<table style="width: 100%; font-size: 14px;" id="ref_videos">
				<thead>
					<tr style="background: #cdf;">
						<td></td>
						<td><b>Youtube ID</b></td>
						<td></td>
					</tr>
				</thead>

				<tbody id="videos_ref">
<?php
$videosQuery = $DB->query('SELECT * FROM `product_videos` WHERE `product_id` = ' . intval($product['id']) . ' ORDER BY `rank` ASC');

    $videosTotalQuery = $DB->query_first('SELECT COUNT(*) as total FROM `product_videos` WHERE `product_id` = ' . intval($product['id']), MYSQLI_ASSOC);
    $videosTotal      = $videosTotalQuery['total'];

    $i = 0;
    while ($video = $DB->fetch_array($videosQuery)) {
        echo "<tr id='video[" . $video['id'] . "]' videosid='" . $video['id'] . "'>";
        echo "<td>";
        echo '<img src="/webadmin/down_arrow.gif" onclick="moveDown(this.parentNode.parentNode,\'_ref\', \'videos\')" style="visibility: ' . ((($i + 1) == intval($videosTotal)) ? 'hidden' : 'visible') . ';">';
        echo '<img src="/webadmin/up_arrow.gif" onclick="moveUp(this.parentNode.parentNode,\'_ref\', \'videos\')" style="visibility:' . ((0 == $i) ? 'hidden' : 'visible') . '">';
        echo "</td>";
        echo "<td><input type='text' name='videos[" . $video['id'] . "]' value='" . $video['youtube_id'] . "' style='width:100%;'/></td>";
        echo "<td style='text-align:right;'><a href='https://www.youtube.com/watch?v=" . $video['youtube_id'] . "' target='_blank'>View</a><button type='button' onclick='deleteVideo(this, " . $video['id'] . ");'>Delete</button></td>";
        echo "</tr>";

        $i++;
    }
?>
				</tbody>
			</table>
		</td>
	</tr>
	<tr>
		<td nowrap="nowrap" class="left"></td>
		<td class="right" style="text-align: center;">
			<button type="button" onclick="addNewVideo();">Add new video</button>

			<script>
				function addNewVideo() {
					var html = "<tr>";
					html += "<td></td>";

					html += "<td>";
					html += "<input type='text' name='new_youtube[]' value='' style='width: 100%;' >";
					html += "</td>";

					html += "<td style='text-align: right;'>";
					html += "<button type='button' onclick='this.parentNode.parentNode.parentNode.removeChild(this.parentNode.parentNode);'>Delete</button>";
					html += "</td>";

					html += "</tr>";

					$("#videos_ref").append(html);
				}
			</script>
		</td>
	</tr>

	<tr>
		<td colspan="2" class="top">&nbsp;</td>
	</tr>




	<tr bgcolor="#FFFFFF"><td colspan="2" nowrap="nowrap">&nbsp;</td></tr>
	<tr><td colspan="2" nowrap="nowrap"><table style="width:100%" border="0" cellspacing="0" cellpadding="3" align="center">
		<tr><td colspan="7" align="center" class="box"><strong>Manuals</strong></td></tr>
		<tr bgcolor="#E3EBF7">
			<td style="width:5%;" align="center" nowrap="nowrap" class="bottomLeft"><strong>Type Of Manual</strong></td>
			<td style="width:75%;" align="center" nowrap="nowrap" class="bottomLeft"><strong>Notes/Description</strong></td>
			<td style="width:5%;" align="center" class="bottomLeft"><strong>File Type</strong></td>
			<td style="width:5%;" align="center" class="bottomLeft"><strong>Size</strong></td>
			<td style="width:5%;" align="center" class="bottomLeft"><strong>Uploaded By</strong></td>
			<td style="width:5%;" align="center" class="bottomLeft"><strong>Date Added</strong></td>
			<td style="width:5%;" align="center" class="bottomLeftRight"><strong>Edit</strong></td>
		</tr>
		<tbody id="attach_list">
<?php
productAttachmentsFTPToDatabase($product['id']);
    $manuals = productAttachmentsFromDatabase($product['id']);
    $count++;
    foreach ($manuals as $man) {
        $manual_details = productAttachmentFromDatabase($man, $product['id']);
?>
			<tr id="attach[<?php echo $man ?>]" bgcolor="<?php echo ($count % 2 == 1) ? "#EEEEEE" : "#FFFFFF" ?>">
				<td class="bottomLeft" nowrap="nowrap" align="center">
					<a target="_blank" href="/manuals/C<?php echo $man ?>_<?php echo $product['id'] ?>.<?php echo $manual_details['filetype'] ?>">
						<!-- <a target="_blank" href="viewimage.php?product_id=<?php echo $product['id'] ?>&amp;attachment_id=<?php echo $man ?>"> -->
						<!-- <img src="viewimage.php?product_id=<?php echo $product['id'] ?>&amp;attachment_id=<?php echo $man ?>&amp;thumb=true"> -->
						<?php echo typeOfManual($manual_details['manual_type']) ?>
					</a>
				</td>
				<td class="bottomLeft" nowrap="nowrap" align="left">
					<a target="_blank" href="/manuals/C<?php echo $man ?>_<?php echo $product['id'] ?>.<?php echo $manual_details['filetype'] ?>">
						<!-- <a target="_blank" href="viewimage.php?product_id=<?php echo $product['id'] ?>&amp;attachment_id=<?php echo $man ?>"> -->
						<?php echo $manual_details['description'] ?>
					</a>
				</td>
				<td class="bottomLeft" nowrap="nowrap" align="center"><?php echo $manual_details['filetype'] ?></td>
<?php
$size = (0 != $manual_details['size']) ? human_file_size($manual_details['size']) : '&nbsp;';?>
				<td class="bottomLeft" nowrap="nowrap" align="center"><?php echo $size ?></td>
				<td class="bottomLeft" nowrap="nowrap" align="center"><?php echo ('' != $manual_details['user']) ? $manual_details['user'] : '&nbsp;' ?></td>
				<td class="bottomLeft" nowrap="nowrap" align="center"><?php echo date("d/m/y", strtotime($manual_details['attach_date'])) ?></td>
				<td class="bottomLeftRight"><input type="button" value="Edit" onclick="javascript:editAttachment(<?php echo $man ?>)" /></td>
				<!--<td class="bottomLeftRight"><input type="button" value="Remove" onClick="javascript:removeAttachWindow(<?php echo $man ?>)" /></td>-->
			</tr>
<?php
$count++;
    }
?>
		</tbody>
		<tfoot>
			<tr id="attach_foot" name="attach_foot" bgcolor="<?php echo ($count % 2 == 1) ? "#EEEEEE" : "#FFFFFF" ?>">
				<td class="bottomLeftRight" align="center" colspan="7">
					<input type="button" value="Add Manual" onclick="javascript:addAttachWindow()" />
					<input type="button" value="FTP Upload" onclick="window.open('<?php echo productAttachmentGetFTPURI($product['id']) ?>')" />
				</td>
			</tr>
		</tfoot>
	</table></td></tr>
	<tr><td colspan="2" nowrap="nowrap">&nbsp;</td></tr>
	<tr>
		<td nowrap="nowrap" bgcolor="#EEEEE0" class="left"><b>Admin Notes :</b><br /><br /><font color="red" size="-1">Admin notes are never<br />displayed on the main site</font></td>
		<td bgcolor="#EEEEE0" class="right"><textarea name="admin_notes" cols="30" rows="8" style="width:350px"></textarea></td>
	</tr>
	<tr><td colspan="2" nowrap="nowrap" class="centre">
<?php
$statement         = "SELECT *, UNIX_TIMESTAMP(date_added) AS date_added FROM product_admin_notes WHERE product_id = '$id' ORDER BY date_added DESC";
    $results           = $DB->query($statement);
    $total_admin_notes = $DB->num_rows($results);
    if ($total_admin_notes > 0) {
?>
		<br />
		<table width="500" border="0" cellspacing="0" cellpadding="2" align="center">
			<tr><td colspan="4" align="center" class="box"><strong>Admin Notes</strong></td></tr>
			<tr bgcolor="#E3EBF7">
				<td width="10%" align="center" nowrap="nowrap" class="bottomLeft"><strong>Date Added</strong></td>
				<td width="5%" align="center" nowrap="nowrap" class="bottomLeft"><strong>User</strong></td>
				<td width="80%" align="center" class="bottomLeftRight"><strong>Notes</strong></td>
				<td width="5%" align="center" class="bottomLeft" nowrap="nowrap"></td>
			</tr>
<?php
for ($i = 0; $i < $total_admin_notes && $i < 5; $i++) {
            $row = $DB->fetch_array($results);
            echo "<tr bgcolor=\"" . ($i % 2 == 1 ? "#EEEEEE" : "#FFFFFF") . "\">\n";
            echo "<td class=\"bottomLeft\" valign=\"top\" nowrap=\"nowrap\" align=\"center\">" . date('d M, Y', $row['date_added']) . "</td>\n";
            echo "<td class=\"bottomLeft\" valign=\"top\" nowrap=\"nowrap\" align=\"center\">" . $row['user'] . "&nbsp;</td>\n";
            echo "<td class=\"bottomLeftRight\"><div style=\"white-space:normal;\">" . nl2br(stripslashes($row['note'])) . "</div></td>\n";
            echo "<td class='bottomLeft'><a href='/webadmin/products/delete_admin_note.php?id=" . $row['id'] . "'>Delete</a></td>\n";
            echo "</tr>\n";
        }
?>
		</table>
<?php
if ($total_admin_notes >= 5) {
?>
		<a href="javascript:newWin = window.open('/webadmin/products/view_admin_notes.php?id=<?php echo $id ?>','noteWin','width=600,height=450,resizable,scrollbars'); newWin.focus();">More Admin Notes... </a>
<?php
}
?>
		<br /><br />
<?php
}
?>
		&nbsp;
	</td></tr>
<?php
$statement = "SELECT * FROM products_related WHERE product_id = '$id'";
    $results   = $DB->query($statement);
    for ($i = 0; $i < 3; $i++) {
        $related_title = '';
        $related_type  = '';
        if ($DB->num_rows($results) > $i) {
            $related       = $DB->fetch_array($results);
            $related_title = $related['title'];
            $related_type  = $related['type_id'];
        }
        $category_type_options = get_category_type($related_type);
?>
	<tr>
		<td nowrap="nowrap" class="left"><b>Related Title <?php echo ($i + 1) ?> :</b></td>
		<td class="right"><input type="text" name="related[<?php echo $i ?>][title]" size="40" value="<?php echo $related_title ?>" style="width:300px" />
		(Optional)</td>
	</tr>
	<tr>
		<td nowrap="nowrap" class="left"><b>Related Type <?php echo ($i + 1) ?> :</b></td>
		<td class="right"> <select name="related[<?php echo $i ?>][type_id]" style="width:300px">
			<option value=""></option>
<?php
echo $category_type_options; ?>
		</select> </td>
	</tr>
	<tr>
		<td colspan="2" nowrap="nowrap" class="centre">&nbsp;</td>
	</tr>
<?php
}?>
	<!-- //related links/title -->

	<tr>
		<td class="left"><b>Power Consumption</b></td>
		<td class="right"><input type="text" name="data1[power_consumption]" size="40" style="width:300px" value="<?php echo htmlspecialchars($product['power_consumption']) ?>" /></td>
	</tr>

	<tr>
		<td colspan="2" nowrap="nowrap" class="centre">&nbsp;</td>
	</tr>

	<tr>
		<td nowrap="nowrap" class="left"><b>Dimensions :</b></td>
		<td class="right"> <input type="text" name="data1[dimension]" size="40" style="width:300px" value="<?php echo htmlspecialchars($product['dimension']) ?>" />
		</td>
	</tr>
	<tr>
<?php
$statement = "SELECT * FROM products_dimen WHERE id = '$id'";
    $result    = $DB->query($statement);
    $row       = $DB->fetch_array($result);
?>
		<td nowrap="nowrap" class="left"><b>Product Dimensions (cms)</b> </td>
		<td class="right">
			<b>Width</b><input type="text" size="5" name="width1" value="<?php echo $row['width'] ?>" onchange="javascript:updateIns(this,'width',0,'Dim');onChangeDimensions();" />
			<b>Depth</b><input type="text" size="5" name="depth1" value="<?php echo $row['depth'] ?>" onchange="javascript:updateIns(this,'depth',0,'Dim');onChangeDimensions();" />
			<b>Height</b><input type="text" size="5" name="height1" value="<?php echo $row['height'] ?>" onchange="javascript:updateIns(this,'height',0,'Dim');onChangeDimensions();" />
		</td>
	</tr>
	<tr>
		<td nowrap="nowrap" class="left"><b>Product Dimensions (inches)</b> </td>
		<td class="right" style="background-color:#DFF7E1;">
			<b>Width</b><input type="text" size="5" name="widthIn" value="<?php echo round($row['width'] / 2.54, 2) ?>" onchange="javascript:updateCMS(this,'width',0,'Dim');onChangeDimensions();" />
			<b>Depth</b><input type="text" size="5" name="depthIn" value="<?php echo round($row['depth'] / 2.54, 2) ?>" onchange="javascript:updateCMS(this,'depth',0,'Dim');onChangeDimensions();" />
			<b>Height</b><input type="text" size="5" name="heightIn" value="<?php echo round($row['height'] / 2.54, 2) ?>" onchange="javascript:updateCMS(this,'height',0,'Dim');onChangeDimensions();" />
		</td>
	</tr>

	<tr><td colspan="2"><table id="packing_table" style="width:100%;" cellspacing="0">

<?php
$statement = "SELECT * FROM product_packing WHERE product_id='$id'";
    $pds       = $DB->query($statement);
    if (count($pds) > 0) {
        $packing_row = 0;
        while ($pack = $DB->fetch_array($pds)) {
?>
		<tr<?php echo (0 == $packing_row) ? ' id="packing_cloner"' : '' ?>>
			<td nowrap="nowrap" class="left"><b>Packing Dimensions (cms/kgs)</b></td>
			<td class="right">
				<b>Width</b><input type="text" size="5" name="pack_width[]" value="<?php echo $pack['width'] ?>" onchange="javascript:updateIns(this,'width',<?php echo $packing_row ?>,'');onChangePackingDimensions();" />
				<b>Depth</b><input type="text" size="5" name="pack_depth[]" value="<?php echo $pack['depth'] ?>" onchange="javascript:updateIns(this,'depth',<?php echo $packing_row ?>,'');onChangePackingDimensions();" />
				<b>Height</b><input type="text" size="5" name="pack_height[]" value="<?php echo $pack['height'] ?>" onchange="javascript:updateIns(this,'height',<?php echo $packing_row ?>,'');onChangePackingDimensions();" />
				<b>Weight</b><input type="text" size="5" name="pack_weight[]" value="<?php echo $pack['weight'] ?>" onchange="javascript:updateLbs(this,<?php echo $packing_row ?>);onChangePackingDimensions();" />
			</td>
		</tr>
		<tr><?php // New bit: added by Jwalit Shah on 21/07/05..
?>
			<td nowrap="nowrap" class="left"><b>Packing Dimensions (inches/lbs)</b></td>
			<td class="right" style="background-color:#DFF7E1;">
				<script type="text/javascript">
//<![CDATA[
updateCount();
//]]>
				</script>
				<b>Width</b><input type="text" size="5" name="pack_width_in_<?php echo $packing_row ?>" value="<?php echo round($pack['width'] / 2.54, 2) ?>" onchange="javascript:updateCMS(this,'width','<?php echo $packing_row ?>','');onChangePackingDimensions();" />
				<b>Depth</b><input type="text" size="5" name="pack_depth_in_<?php echo $packing_row ?>" value="<?php echo round($pack['depth'] / 2.54, 2) ?>" onchange="javascript:updateCMS(this,'depth','<?php echo $packing_row ?>','');onChangePackingDimensions();" />
				<b>Height</b><input type="text" size="5" name="pack_height_in_<?php echo $packing_row ?>" value="<?php echo round($pack['height'] / 2.54, 2) ?>" onchange="javascript:updateCMS(this,'height','<?php echo $packing_row ?>','');onChangePackingDimensions();" />
				<b>Weight</b><input type="text" size="5" name="pack_weight_lb_<?php echo $packing_row ?>" value="<?php echo round($pack['weight'] / 0.45, 2) ?>" onchange="javascript:updateKgs(this,'<?php echo $packing_row ?>');onChangePackingDimensions();" />
			</td>
		</tr>

<?php
$packing_row++;
        }
    }
?>

		<tr><td colspan="2" class="right" style="text-align:center;"><input type="button" name="pdim" value="Add Packing dimension" onclick="addPackingDimension();" /></td></tr>

	</table></td></tr>
<?php
$statement = "SELECT volume_weight, weight FROM products_dimen WHERE id='$id'";
    $pds       = $DB->query($statement);
    $pack      = $DB->fetch_array($pds)
?>


	<tr>
		<td class="left">Actual Product Weight (Kgs)</td>
		<td class="right"><input type="text" value="<?php echo $pack['weight'] ?>" name="actProdWt" size="40" style="width:300px;" onchange="javascript:updateLbs(this,0,'Act');updateWeights(this.value)" /></td>
	</tr>
	<tr>
		<td class="left">Actual Product Weight (Lbs)</td>
		<td class="right"><input type="text" value="<?php echo round($pack['weight'] / 0.45, 2) ?>" name="actProdWtL" size="40" style="width:300px;" onchange="javascript:updateKgs(this,0,'Act')" /></td>
	</tr>


	<tr>
		<td class="left">Volume Weight (Kgs)</td>
		<td class="right"><input type="text" value="<?php echo $pack['volume_weight'] ?>" name="volWt" size="40" style="width:300px;" /></td>
	</tr>

	<tr>
		<td nowrap="nowrap" class="left"><b>Shipping Weight (Kgs)</b></td>
		<td class="right"> <input type="text" name="data1[weight]" size="40" style="width:300px;" value="<?php echo htmlspecialchars($product['weight']) ?>" /></td>
	</tr>

	<tr>
		<td nowrap="nowrap" class="left"><b>Increment (Kgs):</b></td>
		<td class="right"> <input type="text" name="data1[weight_inc]" size="40" style="width:300px" value="<?php echo sprintf("%0.3f", $product['weight_inc']) ?>" /></td>
	</tr>
	<tr>
		<td nowrap="nowrap" class="left"><b>Cubic Metres (without crating):</b></td>
		<td class="right"> <input type="text" name="data1[metres3_unpacked]" size="40" style="width:300px" value="<?php echo htmlspecialchars($product['metres3_unpacked']) ?>" />
		</td>
	</tr>
	<tr>
		<td nowrap="nowrap" class="left"><b>Cubic Metres (with crating):</b></td>
		<td class="right"> <input type="text" name="data1[metres3_packed]" size="40" style="width:300px" value="<?php echo htmlspecialchars($product['metres3_packed']) ?>" />
		</td>
	</tr>

<?php
$str = 'SELECT * FROM container_load WHERE product_id=' . $product['id'];
    $res = $DB->query($str);
    $row = $DB->fetch_array($res);
?>
	<tr>
		<td class="left">20ft Container Qty (without crating):</td>
		<td class="right">
			<input type="text" name="qty20_unpacked" value="<?php echo $row['freeload_20ft_qty'] ?>" />
			<b>Approx</b><input type="radio" name="loading20_unpacked" value="approx"<?php echo ('real' != $row['freeload_20ft_type']) ? ' checked="checked"' : '' ?> />
			<b>Real</b><input type="radio" name="loading20_unpacked" value="real"<?php echo ('real' != $row['freeload_20ft_type']) ? '' : ' checked="checked"' ?> />
		</td>
	</tr>
	<tr>
		<td class="left">20ft Container Qty (with crating):</td>
		<td class="right">
			<input type="text" name="qty20_packed" value="<?php echo $row['contload_20ft_qty'] ?>" />
			<b>Approx</b><input type="radio" name="loading20_packed" value="approx"<?php echo ('real' != $row['contload_20ft_type']) ? ' checked="checked"' : '' ?> />
			<b>Real</b><input type="radio" name="loading20_packed" value="real"<?php echo ('real' != $row['contload_20ft_type']) ? '' : ' checked="checked"' ?> />
		</td>
	</tr>
	<tr>
		<td class="left">40ft Container Qty (without crating):</td>
		<td class="right">
			<input type="text" name="qty40_unpacked" value="<?php echo $row['freeload_40ft_qty'] ?>" />
			<b>Approx</b><input type="radio" name="loading40_unpacked" value="approx"<?php echo ('real' != $row['freeload_40ft_type']) ? ' checked="checked"' : '' ?> />
			<b>Real</b><input type="radio" name="loading40_unpacked" value="real"<?php echo ('real' != $row['freeload_40ft_type']) ? '' : ' checked="checked"' ?> />
		</td>
	</tr>
	<tr>
		<td class="left">40ft Container Qty (with crating):</td>
		<td class="right">
			<input type="text" name="qty40_packed" value="<?php echo $row['contload_40ft_qty'] ?>" />
			<b>Approx</b><input type="radio" name="loading40_packed" value="approx"<?php echo ('real' != $row['contload_40ft_type']) ? ' checked="checked"' : '' ?> />
			<b>Real</b><input type="radio" name="loading40_packed" value="real"<?php echo ('real' != $row['contload_40ft_type']) ? '' : ' checked="checked"' ?> />
		</td>
	</tr>

	<tr>
		<td colspan="2" nowrap="nowrap">&nbsp;</td>
	</tr>

	<tr><td colspan="2">
		Without Crating:<br />
		<iframe name="the_iframe1" src="packingsearch.php?product_id=<?php echo $id ?>&amp;product_qty=1&amp;with_crating=0" style="width:100%" onload="javascript:calcHeight();"></iframe>
	</td></tr>

	<tr><td colspan="2">
		With Crating:
		<iframe name="the_iframe2" src="packingsearch.php?product_id=<?php echo $id ?>&amp;product_qty=1&amp;with_crating=1" style="width:100%" onload="javascript:calcHeight();"></iframe>
	</td></tr>

	<script type="text/javascript">
//<![CDATA[
updateFreeApprox();
updateCratingApprox();
//setTimeout("calcHeight()", 2700);
//]]>
	</script>

	<tr>
		<td colspan="2" nowrap="nowrap" class="bottom">
			<!-- price stats -->
<?php
if ('' == $product['approx_cost_currency']) {
        $product['approx_cost_currency'] = 'USD';
    }

    //get the old exchange rate from the database (from cost currency to USD)
    $old_exch_rate = doubleval($product['approx_cost_exch_rate']);
    $UStoRate      = doubleval(getUStoRate($product['approx_cost_currency']));
    //calculate the old approximate cost (also knows the approx cost currency)
    $old_approx_price = $product['cost_price'] * $old_exch_rate * $UStoRate;
    //calculate the new approximate cost, convert from the cost currency to the approx cost currency
    $new_approx_price = showPriceFrom($product['cost_price'], $product['cost_currency'], $product['approx_cost_currency']);
    if (0 == $product['markup'] && 0 != $new_approx_price) {
        $recal_markup      = ($product['sales_price'] - $new_approx_price) / $new_approx_price * 100;
        $product['markup'] = sprintf('%01.2f', $recal_markup);
    }
    //the old sales price is calculated from the old approx cost and markup
    $old_sales_price = (1 + doubleval($product['markup']) / 100) * $old_approx_price;
    //the new sales price is calculated from the new approx cost and markup
    $new_sales_price = (1 + doubleval($product['markup']) / 100) * $new_approx_price;
    //calculate the profits
    $old_profit = $old_sales_price - $old_approx_price;
    $new_profit = $new_sales_price - $new_approx_price;
    //get the last modified date, either the dated first added or last edited date
    $last_modified = $product['date_added'];
    if ($product['last_modified'] > 0) {
        $last_modified = $product['last_modified'];
    }

?>
			<table width="100%" border="0" cellspacing="0" cellpadding="3">
				<tr>
					<td>&nbsp;</td>
					<td class="bottom">&nbsp;</td>
					<td class="bottom">&nbsp;</td>
				</tr>
				<tr>
					<td class="bottom" width="40%">&nbsp;</td>
					<td bgcolor="#F0F0FF" width="30%" class="bottomLeft">
						<div align="right"><strong><?php echo date('d M Y', $last_modified) ?></strong></div>
					</td>
					<td bgcolor="#F0FFF0" width="30%" class="bottomRight">
						<div align="right"><strong><?php echo date('d M Y', time()) ?></strong></div>
					</td>
				</tr>
				<tr>
					<td class="bottomLeft"><strong>Cost Price:</strong></td>
					<td bgcolor="#F0F0FF" nowrap="nowrap" align="right" class="bottom">
						<?php echo $product['cost_price'] . ' ' . $product['cost_currency'] ?>
					</td>
					<td bgcolor="#F0FFF0" nowrap="nowrap" align="right" class="bottomRight">
						<?php echo $product['cost_price'] . ' ' . $product['cost_currency'] ?>
					</td>
				</tr>
				<tr>
					<td class="bottomLeft"><strong>Approx Cost Price:</strong></td>
					<td bgcolor="#F0F0FF" nowrap="nowrap" align="right" class="bottom">
						<?php echo sprintf('%01.2f', $old_approx_price) . ' ' . $product['approx_cost_currency'] ?>
					</td>
					<td bgcolor="#F0FFF0" nowrap="nowrap" align="right" class="bottomRight">
						<?php echo sprintf('%01.2f', $new_approx_price) . ' ' . $product['approx_cost_currency'] ?>
					</td>
				</tr>
				<tr>
					<td class="bottomLeft"><strong>Sales Price:</strong></td>
					<td bgcolor="#F0F0FF" nowrap="nowrap" align="right" class="bottom">
						<?php echo showPriceFrom($old_sales_price, $product['approx_cost_currency'], $product['sales_currency']) . ' ' . $product['sales_currency'] ?>
					</td>
					<td bgcolor="#F0FFF0" nowrap="nowrap" align="right" class="bottomRight">
						<?php echo showPriceFrom($new_sales_price, $product['approx_cost_currency'], $product['sales_currency']) . ' ' . $product['sales_currency'] ?>
					</td>
				</tr>
				<tr>
					<td class="bottomLeft"><strong>Profit:</strong></td>
					<td bgcolor="#F0F0FF" nowrap="nowrap" align="right" class="bottom">
						<?php echo showPriceFrom($old_profit, $product['approx_cost_currency'], $product['sales_currency']) . ' ' . $product['sales_currency'] ?>
					</td>
					<td bgcolor="#F0FFF0" nowrap="nowrap" align="right" class="bottomRight">
						<?php echo showPriceFrom($new_profit, $product['approx_cost_currency'], $product['sales_currency']) . ' ' . $product['sales_currency'] ?>
					</td>
				</tr>
				<tr>
					<td class="bottomLeft"><strong>Markup %:</strong></td>
					<td bgcolor="#F0F0FF" nowrap="nowrap" align="right" class="bottom">
						<?php echo sprintf('%01.2f', $product['markup']) ?>%
					</td>
					<td bgcolor="#F0FFF0" nowrap="nowrap" align="right" class="bottomRight" >
						<?php echo sprintf('%01.2f', $product['markup']) ?>%
					</td>
				</tr>

			</table>
			<br />
			<!-- //price stats -->
		</td>
	</tr>
	<tr>
		<td nowrap="nowrap" bgcolor="#EEEEE0" class="left"><b>Supplier :</b></td>
		<td bgcolor="#EEEEE0" class="right">
<?php
$supplier     = $DB->query_first("SELECT * FROM supplier_product WHERE product_id = '$id'");
    $old_supplier = '';
    if (isset($supplier['supplier_id'])) {
        $old_supplier = $supplier['supplier_id'];
    }

?>
			<input type="hidden" name="data2[old_supplier]" value="<?php echo $old_supplier ?>" />
			<select name="data2[supplier]" style="width:300px;">
				<option value=""></option>
<?php
$result = $DB->query("SELECT id, supplier FROM suppliers ORDER BY supplier ASC");
    while ($row = $DB->fetch_array($result)) {
        echo '<option value="' . $row['id'] . '"';
        echo $row['id'] == $supplier['supplier_id'] ? ' selected="selected"' : "";
        echo '>';
        echo stripslashes($row['supplier']) . '</option>';
    }
?>
			</select>
		</td>
	</tr>
	<tr bgcolor="#EEEEE0">
		<td nowrap="nowrap" class="left"><b>Factory ID:</b><br /><font color="#FF0000" size="-1">not shown on site</font></td>
		<td class="right"><input type="text" name="data1[factory_id]" size="40" style="width:300px" value="<?php echo htmlspecialchars($product['factory_id']) ?>" /></td>
	</tr>
	<tr>
		<td nowrap="nowrap" bgcolor="#EEEEE0" class="left"><b>Cost Price :</b></td>
		<td bgcolor="#EEEEE0" class="right">
			<input type="text" name="data1[cost_price]" id="cost_price" size="10" style="width:70px" onchange="convert(this.form.cost_price, this.form.cost_currency, this.form.approx_cost_price, this.form.approx_cost_currency);doMarkup();note_changes();" value="<?php echo $product['cost_price'] ?>" />
			<select name="data1[cost_currency]" style="width:230px" id="cost_currency" onchange="convert(this.form.cost_price, this.form.cost_currency, this.form.approx_cost_price, this.form.approx_cost_currency);doMarkup();">
<?php
$result = $DB->query("SELECT * FROM `highwaygroup`.`currency` ORDER BY `name` ASC");
    while ($row = $DB->fetch_array($result)) {
        echo '<option value="' . $row['code'] . '"';
        echo $row['code'] == $product['cost_currency'] ? ' selected="selected"' : "";
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
			<input type="text" name="approx_cost_price" size="10" id="approx_cost_price" style="width:70px" disabled="disabled" onchange="doMarkup();note_changes();" />
			USD
			<select name="data1[approx_cost_currency]" id="approx_cost_currency" style="width:230px; display: none;" onchange="convert(this.form.cost_price, this.form.cost_currency, this.form.approx_cost_price, this.form.approx_cost_currency);doMarkup();">
<?php
$result = $DB->query("SELECT * FROM `highwaygroup`.`currency` ORDER BY `name` ASC");
    while ($row = $DB->fetch_array($result)) {
        echo '<option value="' . $row['code'] . '"';
        echo $row['code'] == $product['approx_cost_currency'] ? ' selected="selected"' : "";
        echo '>';
        echo strtoupper($row['code']) . ' - ' . $row['name'] . '</option>';
    }
?>
			</select>
		</td>
	</tr>
<?php
$options = array('y' => 'Yes', 'n' => 'No');
    if (in_array($_GET['parent'], array('4', '1'))) {
        $options = array_reverse($options, true);
    }

?>

	<script type="text/javascript">
//<![CDATA[
var fValue = document.admin_form.cost_price;
var fCurrency = document.admin_form.cost_currency;
var tValue = document.admin_form.approx_cost_price;
var tCurrency = document.admin_form.approx_cost_currency;
convert(fValue, fCurrency, tValue, tCurrency);
//]]>
	</script>
<?php
if ($product['sales_currency'] == $product['cost_currency']) {
        $product['price_flux'] = 'n';
    }

?>
	<tr>
		<td nowrap="nowrap" class="left"><b>Price Flux :<br /><span style="font-size:11px;color:red;">NB: If Cost and Sales currency<br />are the same, then Price Flux<br />should always be "No"</span></b></td>
		<td class="right"><table cellpadding="0" cellspacing="0" border="0" width="100%"><tr>
			<td width="20%"><select name="data1[price_flux]" id="price_flux" style="width:70px;" onchange="note_changes();">
				<option value="y"<?php echo ('y' == $product['price_flux']) ? ' selected="selected"' : '' ?>>Yes</option>
				<option value="n"<?php echo ('n' == $product['price_flux']) ? ' selected="selected"' : '' ?>>No</option>
			</select></td>
			<td>&nbsp;&nbsp;Allow automatic price fluctuation <br />&nbsp;&nbsp;according to curreny exchanges</td>
		</tr></table></td>
	</tr>
	<tr>
		<td nowrap="nowrap" class="left">&nbsp;</td>
		<td class="right"><table cellpadding="0" cellspacing="0" border="0" width="100%"><tr>
			<td><input type="checkbox" name="update_prices" value="1" onchange="toggle_changes();" /></td>
			<td>Update prices, markup, and currency.</td>
		</tr></table></td>
	</tr>
	<tr style="display: none;">
		<td nowrap="nowrap" class="left" ><b>Unit Price:</b></td>
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
		profit = profit.toFixed(2)
		document.getElementById('profit_price').value=profit;
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

	<tr>
		<td nowrap="nowrap" class="left"><b>Wholesale Price :</b></td>
		<td class="right">
			<input type="text" id="wholesale_price" size="10" name="data1[wholesale_price]" style="width:70px;" value="<?=$product['wholesale_price']?>"/>
			USD
		</td>
	</tr>

	<tr>
		<td nowrap="nowrap" class="left"><b>Show wholesale price :</b></td>
		<td class="right">
			<select name="data1[wholesale_price_checked]">
				<option value="1" <?=((1 == $product['wholesale_price_checked']) ? 'selected' : '')?>>Yes</option>
				<option value="0" <?=((1 != $product['wholesale_price_checked']) ? 'selected' : '')?>>No</option>
			</select>
		</td>
	</tr>














	<tr><td colspan="2" nowrap="nowrap" class="centre">&nbsp;</td></tr>
<?php
include 'templates/products_bulkpricing.tpl.php';
?>
	<tr><td colspan="2" nowrap="nowrap" class="centre">&nbsp;</td></tr>
<?php
include 'templates/products_bulkweights.tpl.php';
?>
	<tr><td colspan="2" nowrap="nowrap" class="centre">&nbsp;</td></tr>
	<tr>
		<td nowrap="nowrap" class="left"><b>Current Picture :</b></td>
		<td class="right"> <input type="text" size="40" style="width:300px" value="<?php echo $product['image'] ?>" disabled="disabled" /> <a href="/webadmin/gogetmemyimage.php?i=<?php echo $product['image'] ?>">Link</a>
		</td>
	</tr>
	<tr>
		<td nowrap="nowrap" class="left"><b>New Picture :</b></td>
		<td class="right">
			<input type="hidden" name="MAX_FILE_SIZE" value="<?=htmlspecialchars(convertSize(ini_get("upload_max_filesize")))?>" />
			<input name="images[image]" type="file" style="width:300px" size="40" />
		</td>
	</tr>
	<tr><td colspan="2" nowrap="nowrap" class="centre">&nbsp;</td></tr>

	<!--tr>
		<td nowrap="nowrap" class="left"><b>Screenshots:</b></td>
		<td class="right"> <b><a href="screenshot_admin.php?table=product_screenshots&amp;action=add&amp;id=<?php echo $id ?>">Add</a>
		| <a href="screenshot_admin.php?table=product_screenshots&amp;query=<?php echo $id ?>">Edit</a>
		</b> </td>
	</tr-->
		<!-- Social Media -->

	<tr><td colspan="2" nowrap="nowrap" class="center">&nbsp;</td></tr>
	<tr>
		<td nowrap="nowrap" class="left">
			<b>Facebook Image</b>
			<br>
			<small><b>(Recommended size: 1200 x 630)</b></small>
		</td>
		<td class="right">
			<input type="hidden" name="MAX_FILE_SIZE" value="<?=htmlspecialchars(convertSize(ini_get("upload_max_filesize")))?>" />
			<input type="file" name="fb" />

			<?php if (fbImgExists('p' . $product['id'])) {?>
				<br><img src="/img_fb/<?=fbImgGet('p' . $product['id'])?>"" style="width: 100%; max-width: 500px;">
			<?php } else {?>
				<span>If no image is selected first screenshot will be used.</span>
			<?php }?>

		</td>
	</tr>

	<tr>
		<td nowrap="nowrap" class="left">
			<b>Twitter Image</b>
			<br>
			<small><b>(Recommended size: 1200 x 675)</b></small>
		</td>
		<td class="right">
			<input type="hidden" name="MAX_FILE_SIZE" value="<?=htmlspecialchars(convertSize(ini_get("upload_max_filesize")))?>" />
			<input type="file" name="tt" />

			<?php if (ttImgExists('p' . $product['id'])) {?>
				<br><img src="/img_tt/<?=ttImgGet('p' . $product['id'])?>"" style="width: 100%; max-width: 500px;">
			<?php } else {?>
				<span>If no image is selected fisrt screenshot will be used.</span>
			<?php }?>
		</td>
	</tr>


	<!-- /Social Media -->

	<tr><td colspan="2" nowrap="nowrap" class="centre">&nbsp;</td></tr>
	<tr>
		<td nowrap="nowrap" class="left"><b>Screenshots:</b></td>
		<td class="right">
			<input type="hidden" name="ss_order_ref">
			<table style="width: 100%; font-size: 14px;" id="ref_shots">
				<thead>
					<tr style="background: #cdf;">
						<td></td>
						<td><b>Image</b></td>
						<td><b>Caption</b></td>
						<td style='text-align:right;'><b>Added Date</b></td>
						<td></td>
					</tr>
				</thead>

				<tbody id="screenshots_ref">

<?php
$screenshotsQuery = $DB->query('SELECT * FROM `product_screenshots` WHERE `id` = ' . intval($product['id']) . ' ORDER BY `rank` ASC');

    $screenshotsTotalQuery = $DB->query_first('SELECT COUNT(*) as total FROM `product_screenshots` WHERE `id` = ' . intval($product['id']), MYSQLI_ASSOC);
    $screenshotsTotal      = $screenshotsTotalQuery['total'];

    $i = 0;
    while ($ss = $DB->fetch_array($screenshotsQuery)) {

        $caption = str_replace(' ', '_', $ss['caption']);

        $chunk = explode('.', $ss['filename']);
        $ext   = end($chunk);

        $ssUrl = '/imgs/' . $ss['id'] . '/' . $caption . '.' . $ext;

        echo "<tr id='screenshot[" . $ss['ss_id'] . "]' ssid='" . $ss['ss_id'] . "'>";
        echo "<td>";
        echo '<img src="/webadmin/down_arrow.gif" onclick="moveDown(this.parentNode.parentNode,\'_ref\', \'screenshots\')" style="visibility: ' . ((($i + 1) == intval($screenshotsTotal)) ? 'hidden' : 'visible') . ';">';
        echo '<img src="/webadmin/up_arrow.gif" onclick="moveUp(this.parentNode.parentNode,\'_ref\', \'screenshots\')" style="visibility:' . ((0 == $i) ? 'hidden' : 'visible') . '">';
        echo "</td>";
        echo "<td><a target='_blank' href='" . $ssUrl . "'><img src='/" . $ss['filename'] . "' width='100' alt='" . $ss['caption'] . "'></a></td>";
        echo "<td><input type='text' name='caption' value='" . $ss['caption'] . "' style='width:100%;'/></td>";
        //    echo "<td>" . $ss['caption'] ."</td>";
        echo "<td style='text-align:right;'>" . date('d/m/Y', strtotime($ss['date_added'])) . "</td>";
        echo "<td style='text-align:right;'><button type='button' onclick='editScreenshot(" . $ss['id'] . ", " . $ss['ss_id'] . ");'>Edit</button> <button type='button' onclick='deleteScreenshot(" . $ss['ss_id'] . ");'>Delete</button></td>";
        echo "</tr>";

        $i++;
    }
?>
				</tbody>
			</table>
		</td>
	</tr>
	<tr>
		<td nowrap="nowrap" class="left"></td>
		<td class="right" style="text-align: center;">
			<button type="button" onclick="addScreenshot(<?=$product['id']?>);">Add new screenshot</button>
		</td>
	</tr>

	<tr>
		<td colspan="2" class="top">&nbsp;</td>
	</tr>

	<tr>
		<td nowrap="nowrap" class="left">Icons</td>
		<td class="right">
<?php
// Get all icons
    $icons       = array();
    $icons_query = $DB->query("SELECT * FROM `icons`");
    while ($row = $DB->fetch_assoc($icons_query)) {
        $icons[] = $row;
	}
	$DB->free_result($icons_query);

    $selected_icons = explode(',', $product['icons']);

?>

			<ul style="list-style: none;">
				<?php foreach ($icons as $icon): ?>
					<li style="text-align: center; display: inline-block; vertical-align: top; margin: 10px 5px;">
						<img src="/webadmin/assets/icon/image/<?=$icon['img']?>" width="32" height="32" onmouseover="tt_in('<h5><?=$icon['name']?><h5><h6><?=$icon['text']?></h6>');" onmouseout="tt_out();"/>
						<br />
						<input type="checkbox" name="data1[icons][]" value="<?=$icon['id']?>" <?=(in_array($icon['id'], $selected_icons)) ? 'checked' : ''?> />
					</li>
				<?php endforeach;?>
			</ul>
		</td>
	</tr>

	<tr>
		<td colspan="2" class="top">&nbsp;</td>
	</tr>


	<tr>
		<td nowrap="nowrap" class="left">Add Promotion</td>
		<td class="right">
			<select style="width: 300px;" name="data1[promotion]" id="promotion-input">
<?php

    $promotionQuery = $DB->query("SELECT * FROM `product_promotion`");

    echo "<option value='0' " . ((0 == $product['promotion']) ? 'selected' : '') . ">No Promotion</option>";

    while ($row = $DB->fetch_assoc($promotionQuery)) {
        echo "<option value='" . $row['id'] . "'" . (($product['promotion'] == $row['id']) ? 'selected' : '') . ">";
        echo $row['name'];
        echo "</option>";
    }

?>
			</select>

			<a href="#" onclick="addPromotion(); return false;">Add</a>
			|
			<a href="#" onclick="editPromotion(); return false;" style="background: yellow;">Edit</a>
		</td>
	</tr>

	<tr>
		<td colspan="2" class="top">&nbsp;</td>
	</tr>


	<!--tr>
		<td align="right"><input type="radio" name="go_next" value="true"<?php echo (isset($go_next) && 'true' == $go_next) ? ' checked="checked"' : '' ?> /></td>
		<td>Edit Next Product</td>
	</tr>
	<tr>
		<td align="right"><input type="radio" name="go_next" value="false"<?php echo (!isset($go_next) || (isset($go_next) && 'true' != $go_next)) ? ' checked="checked"' : '' ?> /></td>
		<td>Go Back To Listings</td>
	</tr-->
	<tr>
		<td class="left"><b>Display Price Online :</b></td>
		<td class="right"><select name="data1[price_online]">
			<?=formatHTMLOptions($options, $product['price_online'])?>
		</select></td>
	</tr>

	<tr>
		<td class="left" valign="top"><b>Product Discontinued :</b></td>
		<td class="right">
			<select name="data1[replacement]" onchange="if (this.value=='y') { document.getElementById('replacement').style.display='block'; } else { document.getElementById('replacement').style.display='none'; }">
				<?php echo formatHTMLOptions($options, $product['replacement']) ?>
			</select>

			<div id="replacement" style="display: <?=('y' == $product['replacement']) ? 'block' : 'none'?>;">
				Recommended Replacement: <input type="text" name="data1[replacement_product_id]" id="replacement_product_id" style="width: 100px;" placeholder="Product ID" value="<?=$product['replacement_product_id']?>">
				<button type="button" onclick="window.open('search_products.php?popup&query=' + document.getElementById('replacement_product_id').value);">Search</button>
			</div>

<?php

    if ('y' == $product['replacement'] && $product['replacement_product_id'] > 0) {
        $rproductUrl = "";

        $rproduct = $DB->query_first("SELECT * FROM `products` WHERE `id`=" . $product['replacement_product_id'], MYSQLI_ASSOC);

        $rproductType = $DB->query_first('SELECT * FROM `product_types` WHERE `id` = ' . $rproduct['type'], MYSQLI_ASSOC);

        $rproductTypeName = strtolower(str_replace(' ', '_', $rproductType['name']));

        $rproductCatChunk = explode('_', $rproductType['img_location']);
        $rproductCat      = strtolower(end($rproductCatChunk));

        $rproductUrl = "/products/" . $rproductCat . "/" . $rproductTypeName . "/" . str_replace(' ', '_', $rproduct['product_name']) . '_' . $rproduct['id'] . '.html';

        echo "<a target='_blank' id='replacement-product-link' href='{$rproductUrl}'>{$rproduct['product_name']}</a>";
    }

?>
		</td>
	</tr>
	<tr>
		<td class="left" style="background: yellow;"><b>Featured Product :</b></td>
		<td class="right"><select name="data1[feature]">
			<?php echo formatHTMLOptions($options, $product['feature']) ?>
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
		<td class="right"><select id="data1[active]" name="data1[active]" onchange="styleActiveSelector()" style="width:100%; <?=('y' == $product['active']) ? 'background: rgb(0,255,0);' : 'background: red;'?>">
			<option value="y"<?php echo ('y' == $product['active']) ? ' selected="selected"' : '' ?>>Yes</option>
			<option value="n"<?php echo ('n' == $product['active']) ? ' selected="selected"' : '' ?>>No</option>
		</select></td>
	</tr>
	<tr>
	<td></td>
	</tr>
	<tr>
	<td colspan="3" class="">
		<input type="hidden" name="data1[sales_currency]" value="USD" />
		<input type="hidden" name="id" value="<?=$product['id']?>">
		<input type="hidden" name="action" value="do_edit">
		<div  style="text-align: center">
			<input type="submit" name="add" value="Update" style="font-size:16px; font-weight:700;">
		</div>
		<div  style="text-align: right">
		<input type="button" value="Copy Product" onclick="window.open('admin.php?table=products&parent=<?php $_GET['parent']?>&action=add&id=<?php echo $product_id ?>');" />
		</div>
	</td>
	</tr>
<?php
} else {
?>
<table border="0" align="center">
	<tr>
		<td nowrap="nowrap">&nbsp;</td>
		<td>
			<br /><br /><b>No more products from the lists.</b><br />
			<p>
				<input type="hidden" name="data1[sales_currency]" value="USD" />
				<input type="hidden" name="go_next" value="false" />
				<input type="hidden" name="id" value="<?php echo $id ?>" />
				<input type="hidden" name="action" value="do_edit" />
				<input type="submit" name="add" value="Back To Listings" />

		</p></td>
	</tr>
<?php
}
?>
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

<?php
if ($id > 0 && $product['cost_currency'] != $product['sales_currency']) {
?>
<script type="text/javascript">
//<![CDATA[

styleActiveSelector();


reverseMarkup();

function setReplacementProductId(id) {
	document.getElementById('replacement_product_id').value=id;
	document.getElementById("replacement-product-link").style.display="none";
}


//]]>
</script>
<?php
}

function extract_str_diff($ref, $target)
{
    $dif      = '';
    $target_i = 0;
    $ref_i    = 0;
    for ($i = 0; $i < strlen($target); $i++) {
        //echo $ref_i.':'.$target_i.' ';
        if ($target[$target_i] != $ref[$ref_i]) {
            $dif .= $target[$i];
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
    if (strlen($string) > $max) {
        return trim(substr($string, 0, $max)) . $sufix;
    } else {
        return $string;
    }

}
$statement  = "SELECT * FROM query_log WHERE query REGEXP (\"^UPDATE products(.*)id = '$id'(.*)\") ORDER BY log_date ASC";
$result     = $DB->query($statement);
$total_logs = $DB->num_rows($result);
$old_str    = '';
$querys     = array();
if ($total_logs > 0) {
    $old_changed = array();
    while ($row = $DB->fetch_array($result)) {
        //$dif = trim(extract_str_diff($old_str, $row['query']));
        //$old_str = $row['query'];
        $dif = stripslashes(stripslashes($row['query']));
        if ('' != $dif) {
            $i                     = count($querys);
            $querys[$i]['query']   = elipse($dif, 95);
            $querys[$i]['date']    = $row['log_date'];
            $querys[$i]['user_id'] = '0' == $row['user_id'] ? '&nbsp;' : $row['user_id'];
            //                $querys[$i]['query']=$dif;
            $trim = explode(' ', $dif);
            foreach ($trim as $key => $value) {
                if (trim($value) == '') {
                    unset($trim[$key]);
                }
            }

            array_shift($trim);
            array_shift($trim);
            array_shift($trim);
            $changed = array();
            while (count($trim) > 0) {
                $key = array_shift($trim);
                if (array_shift($trim) == '=') {
                    $value = array_shift($trim);
                    while (substr($value, 0, 1) == "'" && !(substr($value, -1, 1) == "'" || substr($value, -2, 2) == "',") && count($trim) > 0) {
                        $value .= ' ' . array_shift($trim);
                    }

                    if (substr($value, -1, 1) == ',') {
                        $value = substr($value, 0, -1);
                    }

                    $changed[$key] = $value;
                } else {
                    break;
                }

            }
            if (count($old_changed) > 0) {
                foreach ($old_changed as $key => $value) {
                    if ($changed[$key] == $value) {
                        unset($changed[$key]);
                    }
                }
            }

            foreach ($changed as $key => $value) {
                $old_changed[$key] = $value;
            }

            unset($changed['old_price']);
            unset($changed['price_changed']);
            unset($changed['last_modified']);
            $link_list    = array('available', 'country_location', 'storage', 'year_released', 'manufacturer', 'style', 'type');
            $link_names   = array('active' => 'Active', 'cost_currency' => 'Cost Currency', 'cost_price' => 'Cost Price', 'date_available' => 'Date Available', 'dimension' => 'Dimensions', 'factory_id' => 'Factory ID', 'image' => 'Product Image', 'long_desc' => 'Long Description', 'metres3_packed' => 'Cubic Metres (with crating)', 'metres3_unpacked' => 'Cubic Metres (without crating)', 'min_order' => 'Minimum Order', 'notes' => 'Notes', 'power_consumption' => 'Power Consumption', 'product_details' => 'Product Details', 'product_name' => 'Product Name', 'qty' => 'Qty', 'sales_price' => 'Sales Price', 'sample_currency' => 'Sample Currency', 'sample_price' => 'Sample Price', 'sample_qty' => 'Sample Qty', 'short_desc' => 'Short Description', 'weight' => 'Shipping Weight (Kgs)', 'weight_inc' => 'Increment (Kgs)');
            $display_list = array();
            foreach ($changed as $key => $value) {
                if ('' != $link_names[$key]) {
                    $display_list[] = '<li><b>' . str_replace('<', '&lt;', $link_names[$key]) . '</b> changed to ' . str_replace('<', '&lt;', $value) . '</li>';
                } else {
                    $display_list[] = '<li><b>' . str_replace('<', '&lt;', $key) . '</b> changed to ' . str_replace('<', '&lt;', $value) . '</li>';
                }

            }
            if (count($display_list) > 0) {
                $querys[$i]['query'] = "<ul>\n\t" . implode("\n\t", $display_list) . "\n</ul>\n";
            } else {
                unset($querys[$i]);
            }

        }
    }
    //        $querys=array_reverse($querys);
    $max = 5;
?>
<!-- Query Log -->
<br />
<table width="500" border="0" cellspacing="0" cellpadding="2" align="center" id="sectionPreviousChanges">
	<tr>
		<td colspan="3" align="center" class="box"><strong>Latest <?php echo $max ?> Data Changes</strong> (<a href="javascript:toggleSection('sectionPreviousChanges');">Collapse</a>)</td>
	</tr>
	<tr bgcolor="#F4F8EF">
		<td width="10%" align="center" nowrap="nowrap" class="bottomLeft"><strong>Date</strong></td>
		<td width="5%" align="center" nowrap="nowrap" class="bottomLeft"><strong>User</strong></td>
		<td width="85%" align="center" class="bottomLeftRight"><strong>Query</strong></td>
	</tr>
<?php
for ($i = count($querys) - 1; $i >= 0 && $i >= count($querys) - $max; $i--) {
        echo "<tr bgcolor=\"" . (($i % 2 == 1) ? "#EEEEEE" : "#FFFFFF") . "\">\n";
        echo "<td class=\"bottomLeft\" valign=\"top\" nowrap=\"nowrap\" align=\"center\">" . $querys[$i]['date'] . "</td>\n";
        echo "<td class=\"bottomLeft\" valign=\"top\" nowrap=\"nowrap\" align=\"center\">" . $querys[$i]['user_id'] . "&nbsp;</td>\n";
        echo "<td class=\"bottomLeftRight\">" . $querys[$i]['query'] . "</td>\n";
        echo "</tr>\n";
    }
?>
</table>
<table width="500" border="0" cellspacing="0" cellpadding="2" align="center" id="sectionPreviousChanges_hideNote">
	<tr><td colspan="3" align="center" class="box"><strong>Latest <?php echo $max ?> Data Changes</strong> (<a href="javascript:toggleSection('sectionPreviousChanges');">Expand</a>)</td></tr>
</table>
<br /><br />
<script type="text/javascript">
//<![CDATA[
toggleSection('sectionPreviousChanges');
//]]>
</script>
<?php
}
?>
<!-- /edit_form.inc.php -->
