<?php
$exRates = array();
$query   = $DB->query("SELECT `code`, `usfrom` FROM `arcooda_highwaygroup`.`currency` ORDER BY `code` ASC");
while ($row = $DB->fetch_array($query)) {
    $exRates[$row['code']] = $row['usfrom'];
}

?>
var exRates=Array(<?=count($exRates)?>);
<?php
foreach ($exRates as $key => $item) {
?>
exRates['<?=$key?>']=<?=floatval($item)?>;
<?php
}
?>
function urlEncode(inText)
{
	if(inText==null) return '';
	inText=String(inText);
	var SAFECHARS="0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz-_.!~*'()";
	var HEX="0123456789ABCDEF";
	var outText="";
	for(var i=0;i<inText.length;i++)
	{
		var ch=inText.charAt(i);
	    if(ch==' ')
		    outText+='+';
		else
			if(SAFECHARS.indexOf(ch)!=-1)
			{
			    outText+=ch;
			} else
			{
			    var charCode=ch.charCodeAt(0);
				outText+='%'+HEX.charAt((charCode>>4)&0xF)+HEX.charAt(charCode&0xF);
			}
	}
	return outText;
}

function htmlEncode(inText)
{
	var outText="";
	for(var i=0;i<inText.length;i++)
		if(Math.random()<0.25)
			outText+="&#"+inText.charCodeAt(i)+";";
		else
			outText+=inText.charAt(i);
	return outText;
}

function sprint_price(price)
{
	var sprice=String(price+0.005);
	var counting=false;
	var thecount=0;
	var outtext="";
	for(var i=0;(i<sprice.length && thecount<2);i++)
	{
		var ch=sprice.charAt(i);
		if(counting) thecount++;
		if(ch=='.') counting=true;
		outtext+=ch;
	}
	return outtext;
}

function concat_ws(filler,word1,word2)
{
	var count=Number(0);
	var result=String();
	if(word1)
	{
		if(count>0) result+=filler;
		result+=word1;
		count++;
	}
	if(word2)
	{
		if(count>0) result+=filler;
		result+=word2;
		count++;
	}
	return result;
}

function toCurrency(price,fromCurrency,toCurrency)
{
	fromCurrency=fromCurrency.slice(0,3).toUpperCase();
	toCurrency=toCurrency.slice(0,3).toUpperCase();
	if(fromCurrency==toCurrency) return price;
	var r=exRates[fromCurrency]/exRates[toCurrency];
	var newP=price*r;
	return sprint_price(newP);
}

function getElement(name)
{
	var elems=document.getElementsByTagName('input');
	for(i=0;i<elems.length;i++)
	{
		if(elems[i].id==name)
			return elems[i];
		if(elems[i].name==name)
			return elems[i];
	}
	var elems=document.getElementsByTagName('textarea');
	for(i=0;i<elems.length;i++)
	{
		if(elems[i].id==name)
			return elems[i];
		if(elems[i].name==name)
			return elems[i];
	}
	var elem=document.getElementById(name);
	if(elem!=null) return elem;
	elem=document.getElementsByName(name);
	if(elem.length>0) return elem[0];
	return null;
}

function setValue(name,value)
{
	var elem=getElement(name);
	if(elem==null) return;
	switch(elem.tagName) {
	case "INPUT":
		if(elem.type=='checkbox' && (value==true || value==false))
			elem.checked=value;
		else
			elem.value=value;
		break;
	case "TEXTAREA":
		elem.value=value;
	break;
	case "SPAN":		elem.innerHTML=value;	break;
	case "DIV":			elem.innerHTML=value;	break;
	case "SELECT":
		elem.value=value;
		for(var i=0;i<elem.options.length;i++)
			if(elem.options[i].value==value) {
				elem.options[i].selected=true;
				elem.selectedIndex=i;
			} else elem.options[i].selected=false;
		break;
	default:
		if(elem.value) elem.value=value;
		else if(elem.innerHTML) elem.innerHTML=value;
	}
	return;
}

function getValue(name)
{
	var elem=getElement(name);
	if(elem==null) return null;
	if(elem.tagName=='DIV') return elem.innerHTML;
	return elem.value;
	if(elem.value) return elem.value;
	if(elem.innerHTML) return elem.innerHTML;
	return null;
}

function showElement(name)
{
	var elem=getElement(name);
	if(elem==null) return;
	if(elem.style==null) return;
	elem.style.display='inline';
}

function hideElement(name)
{
	var elem=getElement(name);
	if(elem==null) return;
	if(elem.style==null) return;
	elem.style.display='none';
}

function addTableRow(theTable,newName)
{
	var newRow=theTable.insertRow(theTable.rows.length);
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

function toggleSection(sectionName)
{
	var theSect=getElement(sectionName);
	if(theSect==null) return;
	if(theSect.style.display=='none')
		theSect.style.display='inline';
	else
		theSect.style.display='none';
}
