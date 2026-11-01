<?php
header('Content-Type: text/javascript');

include_once __DIR__ . '/common.inc.php';

$script_time = filemtime(__FILE__);
$temp        = explode('-', date('m-d-Y'));
$day_time    = mktime(0, 0, 0, $temp[0], $temp[1], $temp[2]);
$want_time   = max($script_time, $day_time);
$cache_etag  = '"' . md5($_SERVER['PHP_SELF'] . $want_time) . '"';

$if_modified_since = preg_replace('/;.*$/', '', $_SERVER['HTTP_IF_MODIFIED_SINCE'] ?? '');
if (gmdate(DATE_RFC1123, $want_time) == $if_modified_since || ($_SERVER['HTTP_IF_NONE_MATCH'] ?? '') == $cache_etag) {
    header("HTTP/1.0 304 Not Modified");
    header('Expires:');
    header('Cache-Control:');
    header('ETag: ' . $cache_etag);
    die();
}
ob_start();
?>

function array_search(needle, haystack)
{
	if (needle == null || haystack == null) return null;
	for (var i = 0;i < haystack.length;i++) if (needle == haystack[i]) return i;
	return null;
}
function roundAmount(amount)
{
	var isneg=false;
	if (amount < 0)
	{
		amount = Math.abs(amount);
		isneg = true;
	}
	amount = Number(amount).toFixed(2);
	var array = explode('.', String(amount));
	var before = Number(array[0]);
	var after = array[1];
	if (after == null) after = "00";
	var num = Number(after.charAt(0));
	var numLast = Number(after.charAt(1));
	var newLast = 0;
	if (num == 9 && numLast >= 7)
	{
		before++;
		newLast = num = 0;
	}
	else
	{
		convert = ['0','0','0','5','5','5','5','0','0','0'];
		newLast = convert[numLast];
		if (numLast > 6) num++;
	}
	var newAmount = Number(before + '.' + num + newLast).toFixed(2);
	if (isneg) return Number(0 - newAmount).toFixed(2);
	return newAmount;
}

<?php
if (function_exists('getExchangeRateList')) {
    $exRates = getExchangeRateList();
?>
function convertCurrency(price, fromCurrency, toCurrency, round, decimalPoints)
{
	if (decimalPoints === undefined) decimalPoints = 2;
	if (toCurrency === undefined || toCurrency == "") toCurrency = fromCurrency;
	var exKeys = <?php jecho(array_keys($exRates))?>;
	var exRate = <?php jecho(array_values($exRates))?>;
	fromCurrency = array_search(fromCurrency.slice(0, 3).toUpperCase(), exKeys);
	toCurrency = array_search(toCurrency.slice(0, 3).toUpperCase(), exKeys);
	if (fromCurrency == null || toCurrency == null || fromCurrency == toCurrency)
	{
		if (round != null && round) return Number(roundAmount(price)).toFixed(decimalPoints);
		return Number(price).toFixed(decimalPoints);
	}
	var newP = price * (exRate[fromCurrency] / exRate[toCurrency]);
	if (round != null && round) return Number(roundAmount(newP)).toFixed(decimalPoints);
	return newP.toFixed(decimalPoints);
}

<?php
}

$shipnotes_list = array();
if ($query = $DB->query(sprintf(
	"SELECT `c`.`code`, COUNT(`fum`.`id`) AS `fum`, COUNT(`cer`.`id`) AS `cer`, COUNT(`shi`.`id`) AS `shi` FROM `world`.`country` AS `c` LEFT JOIN `world`.`fumigation_notes` AS `fum` ON `c`.`code` = `fum`.`country`  LEFT JOIN `world`.`certificates` AS `cer` ON `c`.`code` = `cer`.`country`  LEFT JOIN `world`.`shipping_notes` AS `shi` ON `c`.`code` = `shi`.`country` GROUP BY `c`.`code` HAVING `fum` > 0 OR `cer` > 0 OR `shi` > 0"
))) {
	while ($row = $DB->fetch_assoc($query)) {
		$shipnotes_list[$row['code']] = [
			'fum' => intval($row['fum']),
			'cer' => intval($row['cer']),
			'shi' => intval($row['shi'])
		];
	}
	$DB->free_result($query);
}
?>
function countryShipNotes(country_id) {
  var shipnotes_list = <?php jecho($shipnotes_list)?>;
  country_id = (country_id || "").toUpperCase();
  if (shipnotes_list.hasOwnProperty(country_id)) return shipnotes_list[country_id];
  return {fum:0, cer:0, shi:0};
}

<?php
if (function_exists('getAirPortsList')) {
    $info            = getAirPortsList();
    $count1          = 0;
    $count2          = 0;
    $countryList     = array();
    $countryAirports = array();
    $airportIDs      = array();
    $airportNames    = array();
    foreach ($info as $key => $value) {
        $countryList[$count1] = strtoupper($key);
        foreach ($value as $id => $airport) {
            $countryAirports[$count1][] = $count2;
            $airportIDs[$count2]        = $id;
            $airportNames[$count2]      = $airport;
            $count2++;
        }
        $count1++;
    }
?>
function countryAirports(country_id)
{
	var countryList = <?php jecho($countryList)?>;
	var countryAirports = <?php jecho($countryAirports)?>;
	var airportIDs = <?php jecho($airportIDs)?>;
	var airportNames = <?php jecho($airportNames)?>;
	countryKey = array_search(country_id.toUpperCase(), countryList);
	if (countryKey == null) return null;
	var result = [];
	result[0] = 'None';
	for(var i = 0;i < countryAirports[countryKey].length;i++)
	{
		var airport_id = countryAirports[countryKey][i];
		result[airportIDs[airport_id]] = airportNames[airport_id];
	}
	return result;
}
<?php
}
if (function_exists('getSeaPortsList')) {
    $info            = getSeaPortsList();
    $count1          = 0;
    $count2          = 0;
    $countryList     = array();
    $countrySeaports = array();
    $seaportIDs      = array();
    $seaportNames    = array();
    foreach ($info as $key => $value) {
        $countryList[$count1] = strtoupper($key);
        foreach ($value as $id => $seaport) {
            $countrySeaports[$count1][] = $count2;
            $seaportIDs[$count2]        = $id;
            $seaportNames[$count2]      = $seaport;
            $count2++;
        }
        $count1++;
    }
?>
function countrySeaports(country_id)
{
	var countryList = <?php jecho($countryList)?>;
	var countrySeaports = <?php jecho($countrySeaports)?>;
	var seaportIDs = <?php jecho($seaportIDs)?>;
	var seaportNames = <?php jecho($seaportNames)?>;
	countryKey = array_search(country_id.toUpperCase(), countryList);
	if (countryKey == null) return null;
	var result = [];
	result[0] = 'None';
	for(var i = 0;i < countrySeaports[countryKey].length;i++)
	{
		var seaport_id = countrySeaports[countryKey][i];
		result[seaportIDs[seaport_id]] = seaportNames[seaport_id];
	}
	return result;
}
<?php
}
if (function_exists('getCountryList')) {
?>
function getCountryList()
{
	return <?=json_encode(getCountryList())?>;
}
<?php
}
?>

function getElements(name, fail_safe)
{
	if (fail_safe == null) fail_safe = true;
	var elemList = document.getElementsByName(name);
	if (document.all != null && elemList.length == 0 && fail_safe)
	{
		elemList = [];
		for (var key in document.all) if (document.all[key].name == name) elemList[elemList.length] = document.all[key];
	}
	var idElem = document.getElementById(name);
	if (idElem != null) elemList[elemList.length] = idElem;
	return elemList;
}
function getElement(name,fail_safe)
{
	if(fail_safe==null) fail_safe=true;
	var elem=document.getElementById(name);
	if(elem) return elem;
	elem=document.getElementsByName(name);
	if(elem.length>0) return elem[0];
	if(document.all!=null && fail_safe)
	{
		for(var key in document.all)
		{
			if(document.all[key].id==name) return document.all[key];
			if(document.all[key].name==name) return document.all[key];
		}
	}
	return null;
}
function getValue(name)
{
	var elem=getElement(name);
	if(elem==null) return null;
	if(elem.value!=null) return elem.value;
	if(elem.innerHTML!=null) return elem.innerHTML;
	return null;
}
function setElement(elem,value)
{
	var set=false;
	if(elem.tagName=='SELECT')
	{
		for(var i=0;i < elem.options.length;i++)
		{
			if(elem.options[i].value==value)
			{
				elem.options[i].selected=true;
				elem.selectedIndex=i;
				set=true;
			}
			else
				elem.options[i].selected=false;
		}
	}
	if(!set && (elem.tagName=='SPAN' || elem.tagName=='DIV' || elem.tagName=='TD'))
	{
		//elem.innerText=value;
		elem.innerHTML=value;
		set=true;
	}
	if(elem.value!=null && !set) elem.value=value;
	else if(elem.innerHTML && !set) elem.innerHTML=value;
}
function setValue(name,value)
{
	var elem=getElement(name);
	if(elem!=null) setElement(elem,value);
}
function setClass(name,value)
{
	var elem=getElement(name);
	if(elem!=null) elem.className=value;
}
function setAllValues(name,value)
{
	var elemList=getElements(name);
	if(elemList.length>0)
		for(var i=0;i < elemList.length;i++)
			setElement(elemList[i],value);
}
function setSelectOptions(name, newOptions, append, appendGroup)
{
	if(append == null) append=false;
	var elem=getElement(name);
	if(elem==null) return;
	if(!append)
	{
		elem.options.length=0;
		// also have to remove any optgroups the select menu might have..
		var optGrps = elem.getElementsByTagName("optgroup");
		var olength=optGrps.length;
		if(olength>0) for (var i=0; i < olength; i++) elem.removeChild(optGrps[0]);
	}
	for(var key in newOptions)
	{
		if(is_array(newOptions[key]))
		{
			optGroup=document.createElement('optgroup');
			optGroup.label=key;
			elem.appendChild(optGroup);
			setSelectOptions(name, newOptions[key], true, optGroup);
		}
		else
		{
			var newOption=new Option(newOptions[key],key)
			elem.options[elem.options.length]=newOption;
			if(appendGroup != null) appendGroup.appendChild(newOption);
		}
	}
}
function addTableRow(theTable,newName,offset)
{
	if(offset==null) offset=theTable.rows.length;
	if(offset < 0) offset=theTable.rows.length+offset+1;
	var newRow=theTable.insertRow(offset);
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
function textareaAutoSize(theElem,stepSize,minSize,maxSize)
{
	if(theElem==null || theElem.rows==null || theElem.cols==null || theElem.value==null) return;
	if(stepSize==null || stepSize < 1) stepSize=5;

	var rowContent=theElem.value.split('\n');
	var rowCount=rowContent.length;

	for(i=0;i < rowContent.length;i++)
		rowCount+=Math.floor(rowContent[i].length/((theElem.cols>0)?theElem.cols:60));

	rowCount=Math.floor(rowCount/stepSize)*stepSize+stepSize;

	if(minSize!=null && minSize>rowCount) rowCount=minSize;
	if(maxSize!=null && maxSize>minSize && maxSize < rowCount) rowCount=maxSize;

	theElem.rows=rowCount;
}
function adjustTextareaHeight(elem) 
{
	elem.style.height = 'auto';
	elem.style.height = (elem.scrollHeight) + 'px';
}
function toggleSection(sectionName,force)
{
	var theSect=getElement(sectionName);
	var hideNote=getElement(sectionName+'_hideNote',false);
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
function toggleTabs(tabName, tabArray)
{
	if(tabArray==null || !is_array(tabArray)) return;
	for(i=0;i < tabArray.length;i++)
	{
		toggleSection('section'+tabArray[i], (tabName==tabArray[i]));
		getElement('span'+tabArray[i]).className= ((tabName==tabArray[i]) ? '' : 'tabHeaderInactive');
	}
}
function togglePic(sectionName)
{
	var elem=getElement(sectionName);
	if(elem)
		getElement(sectionName+'ImEx').src=(elem.style.display=='none') ? 'triDown.gif' : 'triUp.gif';
}
function testPost(theFrm)
{
	var oldact=theFrm.action;
	var oldtrg=theFrm.target;
	theFrm.action="developer/testpost.php";
	theFrm.target="_blank";
	theFrm.submit();
	theFrm.action=oldact;
	theFrm.target=oldtrg;
}
function is_all_ws(nod)
{
	return !(/[^\t\n\r ]/.test(nod.data));
}
function is_ignorable(nod)
{
	return(nod.nodeType==8) || ((nod.nodeType==3) && is_all_ws(nod));
}
function node_before(sib)
{
	while((sib=sib.previousSibling))
		if(!is_ignorable(sib))
			return sib;
	return null;
}
function node_after(sib)
{
	while((sib=sib.nextSibling))
		if(!is_ignorable(sib))
			return sib;
	return null;
}
function last_child(par)
{
	var res=par.lastChild;
	while(res)
	{
		if(!is_ignorable(res)) return res;
		res=res.previousSibling;
	}
	return null;
}
function first_child(par)
{
	var res=par.firstChild;
	while(res)
	{
		if(!is_ignorable(res)) return res;
		res=res.nextSibling;
	}
	return null;
}
function is_array(obj)
{
   if (obj.constructor.toString().indexOf("Array") == -1)
      return false;
   else
      return true;
}
function explode(str_separator,str_string)
{
	return str_string.split(str_separator);
}
function implode(str_glue,arr_pieces)
{
	return arr_pieces.join(str_glue);
}
function in_array(needle,haystack)
{
	if(needle==null || haystack==null) return false;
	for(var i=0;i < haystack.length;i++)
		if(needle==haystack[i])
			return true;
	return false;
}
function join(str_glue,arr_pieces)
{
	return implode(str_glue,arr_pieces);
}
function str_replace(str_search,str_replace,str_subject)
{
	return implode(str_replace,explode(str_search,str_subject));
}
function round(num_val,num_precision)
{
	if(num_precision==null || num_precision < 1)
		num_precision=0;
	return Number(num_val).toFixed(num_precision);
}
function my_trim(array)
{
	for(i=0;i < array.length;i++)
	{
		var word = array[i]+"";
		array[i] = word.replace(" ","");
	}
	return array;
}
function formatMETA(theString,type)
{
	//theString = strtolower(strip_tags($theString));
	theString = str_replace('"', "'", theString);
	theString = str_replace(';', "", theString);

	if(type == 'keyword')
	{
		theString = str_replace(",", " ", theString);
		theString = str_replace("-", " ", theString);

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
function generateURLKeys(product_name)
{
	var pr_ar=explode(' ',product_name);
	var new_words = [];
	var count=0;
	for(i=0;i < pr_ar.length;i++)
	{
		if(isNaN(pr_ar[i]) && pr_ar[i].length>2 && count < 3)
		{
			new_words[count]=trim(pr_ar[i]," ");
			count++;
		}
	}
	return implode(' ',new_words);
}
function trim(str, chars)
{
    return ltrim(rtrim(str, chars), chars);
}
function ltrim(str, chars)
{
    chars = chars || "\\s";
    return str.replace(new RegExp("^[" + chars + "]+", "g"), "");
}
function rtrim(str, chars)
{
    chars = chars || "\\s";
    return str.replace(new RegExp("[" + chars + "]+$", "g"), "");
}
function isValidEmail(email_address)
{
	regex=/^[A-z0-9][\w.-]*@[A-z0-9][\w\-\.]+\.[A-z0-9]{2,6}$/;
	return regex.test(email_address);
}
function openWindow(url,w,h,centered,features)
{
	if(features==null) features='';
	if(features!='') features+=',';
	if(centered == null) centered = false;
	features += 'width='+w+', height='+h;
	if(centered) features += ', top='+((screen.height - h) / 2)+', left='+((screen.width  - w)  / 2);
	window.open(url,'',features);
}
function bullet_insert(name)
{
	var theElem = getElement(name);
	if (theElem == null) return;
	if (theElem.selectionStart != null && theElem.selectionEnd != null)
	{
		var before = theElem.value.substring(0, theElem.selectionStart);
		var middle = "\n- " + theElem.value.substring(theElem.selectionStart, theElem.selectionEnd).split("\n").join("\n- ") + "\n";
		var after = theElem.value.substring(theElem.selectionEnd);
		theElem.value = before + middle + after;
		theElem.selectionStart = before.length;
		theElem.selectionEnd = before.length + middle.length;
		theElem.focus();
	}
	else
	{
		var repltext = null;
		var seltext = (document.all) ? document.selection.createRange() : document.getSelection();
		var selit = (document.all) ? document.selection.createRange().text : document.getSelection();
		if (seltext != null && selit.length >= 1) seltext.text = "\n- " + seltext.text.split("\n").join("\n- ") + "\n";
		else theElem.value += "\n- ";
	}
}
function generic_insert(name,tag,extras)
{
	var theElem=getElement(name);
	if(theElem==null) return;

	if(extras==null) extras='';

	var double_tag=true;
	var uni_tag=['param','option','meta','link','input','img','hr','frame','col','br','base','area'];
	for(var i=0;i < uni_tag.length;i++) if(tag==uni_tag[i]) double_tag=false;

	if(theElem.selectionStart!=null && theElem.selectionEnd!=null)
	{
		var a=theElem.selectionStart;
		var b=theElem.selectionEnd;
		if(double_tag)
		{
			theElem.value=theElem.value.substring(0,a)+'<'+tag+extras+'>'+theElem.value.substring(a,b)+'</'+tag+'>'+theElem.value.substring(b);
			theElem.selectionStart=a+2+tag.length+extras.length;
			theElem.selectionEnd=2+tag.length+extras.length+b;
		}
		else
		{
			theElem.value=theElem.value.substring(0,a)+'<'+tag+extras+' />'+theElem.value.substring(a);
			theElem.selectionStart=a+4+tag.length+extras.length;
			theElem.selectionEnd=b+4+tag.length+extras.length;
		}
		theElem.focus();
	}
	else
	{
		var repltext=null;
		var seltext=(document.all)?document.selection.createRange():document.getSelection();
		var selit=(document.all)?document.selection.createRange().text:document.getSelection();
		if(seltext!=null && selit.length>=1)
		{
			if(double_tag)
				seltext.text='<'+tag+extras+'>'+seltext.text+'</'+tag+'>';
			else
				seltext.text='<'+tag+extras+' />'+seltext.text;
		}
		else
		{
			if(double_tag)
				theElem.value+='<'+tag+extras+'>TEXT</'+tag+'>';
			else
				theElem.value+='<'+tag+extras+' />';
		}
	}
}
function scrollToElement(elem)
{
	if(elem==null) return;
	var posX=elem.offsetLeft
	var posY=elem.offsetTop;
	elem=elem.offsetParent;
	while(elem != null)
	{
		posX+=elem.offsetLeft
		posY+=elem.offsetTop;
		elem=elem.offsetParent;
	}
	window.scrollTo(posX ,posY-95);
}
function print_array(ar)
{
	if(ar!=null)
	{
		var str='';
		for(key in ar)
			str+=key+"=>"+ar[key]+"\n";
		if(str!='') alert(str);
	}
}
<?php
$output = ob_get_clean();
header('Date: ' . gmdate(DATE_RFC1123, time()));
header('Last-Modified: ' . gmdate(DATE_RFC1123, $want_time));
header('Content-Length: ' . strlen($output));
header('Content-Type: text/javascript');
header('ETag: ' . $cache_etag);
header('X-Powered-By:');
header('Expires:');
header('Pragma:');
header('Cache-Control:');
echo $output;
die();
