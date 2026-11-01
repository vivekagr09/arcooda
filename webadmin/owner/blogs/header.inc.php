<?php
include_once __DIR__ . "/common.inc.php";

global $DB;

include_once $_SERVER['DOCUMENT_ROOT'] . "/webadmin/header.inc.php";

$items = $DB->query_first(sprintf(
	"SELECT COUNT(*) AS `total` FROM %s",
	"`blogs`"
), MYSQLI_ASSOC);
?>
<table border="0" cellpadding="2" cellspacing="2" width="100%">
	<tr>
		<td class="pageMenu" width="20%" style="text-align:center"><b>Owner News</b></td>
		<td class="pageMenu" style="text-align:right;" width="80%">
			<a href=".?action=add">Add New News</a> |
			<a href=".?action=list">List All News (<?= $items['total']?>)</a>
		</td>
	</tr>
</table>
<div class="meta_content" style="width:100%;">
<br />

<script type="text/javascript">
//<![CDATA[
function resize(theElem, stepSize, minSize, maxSize)
{
	if(theElem==null || theElem.rows==null || theElem.cols==null || theElem.value==null) return;
	if(stepSize==null || stepSize<1) stepSize=2;
	if(maxSize==null || maxSize<1) maxSize=100;
	if(minSize==null || minSize<1) minSize=2;

	var rowContent=theElem.value.split('\n');
	var rowCount=rowContent.length;

	if(theElem.cols>0)
		for(i=0;i<rowContent.length;i++)
			rowCount+=Math.floor(rowContent[i].length/theElem.cols);

	rowCount=Math.floor(rowCount/stepSize)*stepSize+stepSize;

	if(minSize!=null && minSize>rowCount) rowCount=minSize;
	if(maxSize!=null && maxSize>minSize && maxSize<rowCount) rowCount=maxSize;

	theElem.rows=rowCount;
}

function getElement(name)
{
	var theElem=document.getElementById(name);
	if(theElem!=null) return theElem;
	var theElems=document.getElementsByName(name);
	if(theElems.length>0) return theElems[0];
	return null;
}

function getValue(name)
{
	var theElem=getElement(name);
	if(theElem==null) return null;
	return theElem.value;
}

function setValue(name,value)
{
	var elem=getElement(name);
	if(elem==null) return;
	if(elem.tagName=='SELECT')
		for(var i=0;i<elem.options.length;i++)
			if(elem.options[i].value==value)
			{
				elem.options[i].selected=true;
				elem.selectedIndex=i;
			}
			else
				elem.options[i].selected=false;
	if(elem.tagName=='SPAN' || elem.tagName=='DIV')
		elem.innerHTML=value;
	if(elem.value!=null) elem.value=value;
	else if(elem.innerHTML) elem.innerHTML=value;
}

function addTableRow(theTable,newName,rownum)
{
	if(rownum==null) rownum=theTable.rows.length;
	var newRow=theTable.insertRow(rownum);
	newRow.name=newName;
	newRow.id=newName;
	return newRow;
}

function addTableCell(tableRow,innerStuff)
{
	var newCol=document.createElement('td');
	newCol.innerHTML=innerStuff;
	tableRow.appendChild(newCol);
	return newCol;
}

function updateTags(what, title)
{
	setValue(what+'[meta_title]', getValue(what+'['+title+']'));
	setValue(what+'[meta_keys]', formatMETA(getValue(what+'['+title+']')+' '+getValue(what+'[short_desc]'),'keyword'));
	setValue(what+'[meta_desc]', getValue(what+'[short_desc]'));

	updateTotals(what);
}

function updateTotals(what)
{
	var title = getValue(what+'[meta_title]');
	var meta = getValue(what+'[meta_keys]');
	var desc = getValue(what+'[meta_desc]');

	//now update the length of the words
	var keywords = meta.split(',');
	var len = 0;
	for(i=0;i<keywords.length;i++) {
		if(keywords[i] != '')
			len++;
	}
	getElement('meta_title').innerHTML = title.length;
	getElement('meta_keys').innerHTML = len;
	getElement('meta_desc').innerHTML = desc.length;
}

function clearTags(what)
{
	setValue(what+'[meta_title]','');
	setValue(what+'[meta_keys]','');
	setValue(what+'[meta_desc]','');

	updateTotals(what);
}

function my_trim(array)
{
	for(i=0;i<array.length;i++){
		//alert(array[i]);
		var word = array[i]+"";
		array[i] = word.replace(" ","");
	}
	//alert(array);
	return array;
}

function formatMETA(theString,type)
{
	//theString = strtolower(strip_tags($theString));
	theString = str_replace(theString,'"', "'");
	theString = str_replace(theString,';', "");

	if(type == 'keyword')
	{
		theString = str_replace(theString,",", " ");
		theString = str_replace(theString,"-", " ");

		var words = theString.split(' ');
		words = my_trim(words);
		//$words = array_unique($words);

		var keywords = '';
		for (i = 0; i < 50 && i < words.length; i++)
		{
			if(words[i].replace(" ","") != ''){
				var let = words[i].charAt(0);
				let = let.toUpperCase();
				keywords += let+words[i].substring(1,words[i].length)+", ";
			}
		}
		return keywords;
	}
	return theString;
}
//]]>
</script>
