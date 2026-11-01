<?php
include_once 'common.inc.php';
include_once ($_SERVER['DOCUMENT_ROOT'] . '/webadmin/header.inc.php');
?>
<script type="text/javascript">
//<![CDATA[
var airports = <?=json_encode(getAirPortsList())?>;
var seaports = <?=json_encode(getSeaPortsList())?>;
function resize(t, minRows, minCols)
{
	if (minCols == null) minCols = 1;
	if (minRows == null) minRows = 1;
	t.rows = minRows;
	t.setAttribute("wrap", "off");
	t.style.overflow = "auto";
	lines = t.value.split("\n");
	t.cols = minCols;
	maxChars = lines[0].length;
	for (i = 1; i < lines.length; i++)
	{
		currentLength = lines[i].length;
		if (currentLength > maxChars) maxChars = currentLength;
	}
	t.setAttribute("rows", Math.max(lines.length + 1, minRows));
}
function getElement(name)
{
	var theElem = document.getElementById(name);
	if (theElem != null) return theElem;
	var theElems = document.getElementsByName(name);
	if (theElems.length > 0) return theElems[0];
	return null;
}
function getValue(name)
{
	var theElem = getElement(name);
	if (theElem == null) return null;
	return theElem.value;
}
function setValue(name, value)
{
	var theElem = getElement(name);
	if (theElem != null) if (theElem.value != null) theElem.value = value;
}
function addTableRow(theTable, newName, rownum)
{
	if (rownum == null) rownum = theTable.rows.length;
	var newRow = theTable.insertRow(rownum);
	newRow.name = newName;
	newRow.id = newName;
	return newRow;
}
function addTableCell(tableRow, innerStuff)
{
	var newCol = document.createElement('td');
	newCol.innerHTML = innerStuff;
	tableRow.appendChild(newCol);
	return newCol;
}
function toggleSection(sectionName)
{
	var theSect = getElement(sectionName);
	if (theSect == null) return;
	if (theSect.style.display == 'none') theSect.style.display = 'inline';
	else theSect.style.display = 'none';
}
function togglePic(name)
{
	var elem = getElement(name);
	if (elem.style.display == 'none') getElement(name + 'ImEx').src = 'triDown.gif';
	else getElement(name + 'ImEx').src = 'triUp.gif';
}
function updateList()
{
	var val = getValue('display');
	if (val.length > 3)
	{
		var url = 'freight_list.php?action=domestic&destination=' + val;
		if (window.XMLHttpRequest)
		{ // Mozilla, Safari, ...
			http_request = new XMLHttpRequest();
			if (http_request.overrideMimeType)
			{
				http_request.overrideMimeType('text/xml');
				// See note below about this line
			}
		}
		else if (window.ActiveXObject)
		{ //IE
			try
			{
				http_request = new ActiveXObject("Msxml2.XMLHTTP");
			}
			catch (e)
			{
				try
				{
					http_request = new ActiveXObject("Microsoft.XMLHTTP");
				}
				catch (e)
				{
				}
			}
		}
		if (!http_request)
		{
			alert('Giving up :( Cannot create an XMLHTTP instance');
			return false;
		}
		http_request.onreadystatechange = function ()
		{
			alertContents(http_request);
		};
		http_request.open('GET', url, true);
		http_request.send(null);
	}
	else
	{
		getElement('list').innerHTML = '';
		getElement('list_tr').style.display = 'none';
	}
}
function alertContents(http_request)
{
	if (http_request.readyState == 4)
	{
		if (http_request.status == 200)
		{
			var elem = getElement('list');
			getElement('list_tr').style.display = '';
			elem.innerHTML = '' + http_request.responseText;
			//alert(http_request.responseText);
		}
		else alert('There was a problem with the request.');
	}
}
function selectDestination(location, state, code)
{
	setValue('display', location + ", " + state + " (" + code + ")");
	setValue('destination', location);
	setValue('state', state);
	setValue('code', code);
	getElement('list_tr').style.display = 'none';
	getElement('list').innerHTML = '';
}
function resetData()
{
	setValue('display', '');
	setValue('destination', '');
	setValue('weight', '');
	setValue('state', '');
	setValue('code', '');
}
function buildAirports(from_air, to_air)
{
	var from = getValue('from');
	var to = getValue('to');
	setSelectOptions('from_air', airports[from], from_air);
	setSelectOptions('to_air', airports[to], to_air);
}
function buildSeaports(from_sea, to_sea)
{
	var from = getValue('from');
	var to = getValue('to');
	setSelectOptions('from_sea', seaports[from], from_sea);
	setSelectOptions('to_sea', seaports[to], to_sea);
}
function setSelectOptions(name, newOptions, selected)
{
	var elem = getElement(name);
	if (elem == null) return;
	elem.options.length = 0;
	var count = 0;
	for (var key in newOptions)
	{
		elem.options[count] = new Option(newOptions[key], key);
		if (key == selected) elem.options[count].selected = true;
		count++;
	}
}
function viewAll(hide)
{
	//alert(hide);
	var col = document.getElementsByName('break_up');
	for (i = 0; i < col.length; i++)
	col[i].style.display = (hide) ? 'none' : '';
	var show = (hide) ? 'view_link' : 'hide_link';
	var hd = (hide) ? 'hide_link' : 'view_link';
	getElement(show).style.display = '';
	getElement(hd).style.display = 'none';
}
function validateSearch()
{
	if (getElement('weight').value <= 0)
	{
		alert('Weight cannot be less than or equal to 0');
		return false;
	}
	return true;
}
//]]>
</script>

<div class="freight_content" style="width:100%;">
	<br />
