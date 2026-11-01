<?php

if (!function_exists('formatHTMLTitle')) {
    function formatHTMLTitle($title)
    {
        return "<br />\n<div class=\"flagHeader\"><span>" . $title . "</span></div>\n<br />\n";
    }
}

function html_safe($string)
{
    return str_replace('"', '&quot;', str_replace('<', '&lt;', $string));
}

function html_options($list, $default = '')
{
    $out_string = '';
    foreach ($list as $key => $value) {
        $out_string .= '<option value="' . html_safe($key) . '"';
        if ($default === $key) {
            $out_string .= ' selected="selected"';
        }

        $out_string .= '>' . html_safe($value) . '</option>';
    }
    return $out_string;
}

function input_dropdown($name, $default = '', $list = array())
{
?>
<select name="<?=$name?>">
	<?=html_options($list, $default)?>
</select>
<?php
}

function input_country($name, $default = 'USD')
{
    $country_list = getCountryList(false);

    //Bring important ones to the top
    $important      = array('Australia', 'China', 'Hong Kong', 'Italy', 'Japan', 'Korea (North)', 'Korea (South)', 'Spain', 'Taiwan', 'United States');
    $important_list = array();
    foreach ($important as $key) {
        $important_list[$key] = $country_list[$key];
    }

    if (isset($country_list[$default])) {
        $important_list[$default] = $country_list[$default];
    }

?>
<select name="<?=$name?>">
	<option value=""></option>
	<optgroup label="Common">
		<?=html_options($important_list, $default)?>
	</optgroup>
	<optgroup label="Others">
		<?=html_options($country_list)?>
	</optgroup>
</select>
<?php
}

function input_available($name, $default = '')
{
    global $DB;
    $available_list = array();
    $query          = $DB->query("SELECT availability FROM product_availability ORDER BY rank DESC,availability ASC");
    while ($row = $DB->fetch_array($query, MYSQLI_ASSOC)) {
        $available_list[$row['availability']] = $row['availability'];
    }

    input_dropdown($name, $default, $available_list);
}

function input_condition($name, $default = '')
{
    global $DB;
    $condition_list = array();
    $query          = $DB->query("SELECT `condition` FROM product_conditions");
    while ($row = $DB->fetch_array($query, MYSQLI_ASSOC)) {
        $condition_list[$row['condition']] = $row['condition'];
    }

    input_dropdown($name, $default, $condition_list);
}

function input_currency($name, $default = 'USD')
{
    global $DB;
    $currency_list = array();
    $query         = $DB->query("SELECT * FROM `highwaygroup`.`currency` ORDER BY `name`");
    while ($row = $DB->fetch_array($query, MYSQLI_ASSOC)) {
        $currency_list[$row['code']] = $row['name'] . ' - ' . $row['code'];
    }

    //Bring important ones to the top
    $important      = array('ADP', 'AUD', 'CNY', 'EUR', 'HKD', 'JPY', 'USD');
    $important_list = array();
    foreach ($important as $key) {
        $important_list[$key] = $currency_list[$key];
    }

    if (isset($currency_list[$default])) {
        $important_list[$default] = $currency_list[$default];
    }

?>
<select name="<?=$name?>">
	<option value=""></option>
	<optgroup label="Common">
		<?=html_options($important_list, $default)?>
	</optgroup>
	<optgroup label="Others">
		<?=html_options($currency_list)?>
	</optgroup>
</select>
<?php
}

function input_blurb($name, $default = '')
{
?>
<textarea name="<?=$name?>" style="width:512px;height:400px;"><?=html_safe($default)?></textarea>
<?php
}

function input_text($name, $default = '')
{
?>
<input type="text" name="<?=$name?>" value="<?=html_safe($default)?>" style="width:512px;" />
<?php
}

function input_price($name, $default = '')
{
    $default = sprintf('%.2f', floatval($default));
?>
<input type="text" style="text-align:right;" value="<?=$default?>" onchange="javascript:onchange_price(this);" />
<?php
}

function input_percent($name, $default = '')
{
    input_float($name, $default);
}

function input_integer($name, $default = '')
{
    $default = intval($default);
?>
<input type="text" style="text-align:right;" value="<?=$default?>" onchange="javascript:onchange_integer(this);" />
<?php
}

function input_float($name, $default = '')
{
    $default = floatval($default);
?>
<input type="text" style="text-align:right;" value="<?=$default?>" onchange="javascript:onchange_float(this);" />
<?php
}

function input_colour($name, $default = '')
{
    $default = substr('000000' . str_replace('#', '', $default), -6);
    $red     = substr($default, 0, 2);
    $green   = substr($default, 2, 2);
    $blue    = substr($default, 4, 2);
    $inverse = '#000000';
    if ((intval($red, 16) + intval($green, 16) + intval($blue, 16)) < 375) {
        $inverse = '#FFFFFF';
    }

?>
<table style="width:0px;background-color:<?=$default?>;color:<?=$inverse?>;" id="<?=$name?>_table">
	<input type="hidden" value="#<?=$default?>" name="<?=$name?>" />
	<tr style="background-color:transparent;">
		<td>Red</td>
		<td><input type="text" size="2" value="<?=$red?>" onchange="javascript:onchange_colour_slider('<?=$name?>',this,1);" /></td>
	</tr>
	<tr style="background-color:transparent;">
		<td>Green</td>
		<td><input type="text" size="2" value="<?=$green?>" onchange="javascript:onchange_colour_slider('<?=$name?>',this,3);" /></td>
	</tr>
	<tr style="background-color:transparent;">
		<td>Blue</td>
		<td><input type="text" size="2" value="<?=$blue?>" onchange="javascript:onchange_colour_slider('<?=$name?>',this,5);" /></td>
	</tr>
</table>
<?php
}
?>
<script type="text/javascript">
//<![CDATA[
<?php if (true) {?>
function toggleSection(a){if(a=document.getElementById(a))a.style.display="none"==a.style.display?"inline":"none"}function hexval(a){return a.toString(16).toUpperCase()}function onchange_price(a){a.value=(100*a.value|0)/100}function onchange_integer(a){a.value|=0}function onchange_float(a){a.value=parseFloat(a.value)||0}
function onchange_colour_slider(a,b,c){var e=parseInt(b.value,16)&255;b.value=e.toString(16);var d=document.getElementsByName(a)[0];d&&(b=parseInt(d.value.slice(-6),16)&16777215,c=5-c<<2,b=b^b&255<<c|e<<c,d.value="#"+("000000"+b.toString(16)).slice(-6),a=document.getElementById(a+"_table"))&&(a.style.backgroundColor="#"+("000000"+b.toString(16)).slice(-6),a.style.color="#"+("000000"+(65793*(((b&255)+(b>>8&255)+(b>>16&255))/3&255)^8421504).toString(16)).slice(-6))};
<?php } else {?>
function toggleSection(sectionName) {
	var theSect = document.getElementById(sectionName);
	if (theSect == null) return;
	if (theSect.style.display=='none')
		theSect.style.display='inline';
	else
		theSect.style.display='none';
}
function hexval(in_value)
{
	var digits="0123456789ABCDEF";
	var out_value=digits.substr(in_value&15,1);
	while(in_value>15)
	{
		in_value>>=4;
		out_value=digits.substr(in_value&15,1)+out_value;
	}
	return out_value;
}
function onchange_price(sourceElem)
{
	var newValue=parseInt(sourceElem.value*100.0);
	if(isNaN(newValue)) newValue=0;
	newValue/=100.0;
	sourceElem.value=newValue;
}
function onchange_integer(sourceElem)
{
	var newValue=parseInt(sourceElem.value);
	if(isNaN(newValue)) newValue=0;
	sourceElem.value=newValue;
}
function onchange_float(sourceElem)
{
	var newValue=parseFloat(sourceElem.value);
	if(isNaN(newValue)) newValue=0.0;
	sourceElem.value=newValue;
}
function onchange_colour_slider(targetName,sourceElem,pos)
{
	var sourceValue=hexval(parseInt(sourceElem.value,16));
	if(sourceValue.length>2) sourceValue=sourceValue.substr(0,2);
	if(sourceValue.length<2) sourceValue=('00'+sourceValue).substr(-2);
	if(sourceValue!=sourceElem.value) sourceElem.value=sourceValue;

	var targetElem=document.getElementsByName(targetName)[0];
	if(targetElem==null) return;

	var targetValue=targetElem.value;
	var targetValue=targetValue.substr(0,pos)+sourceValue+targetValue.substr(pos+2);
	targetElem.value=targetValue;

	var brightness=parseInt(targetValue.substr(1,2),16)+parseInt(targetValue.substr(3,2),16)+parseInt(targetValue.substr(5,2),16);
	var inverseValue='#FFFFFF';
	if(brightness>375) inverseValue='#000000';

	var elem=document.getElementById(targetName+"_table");
	if(elem.style)
		if(elem.style.backgroundColor)
		{
			elem.style.backgroundColor=targetValue;
			elem.style.color=inverseValue;
		}
}
<?php }?>
//]]>
</script>
<?php

// Social media sharing

function getAllowedExt()
{
    return array('jpg', 'jpeg', 'png');
}

function isExtValid($ext)
{
    return in_array(strtolower($ext), getAllowedExt());
}

function ttImgExists($name)
{
    $allowedExt = getAllowedExt();

    foreach ($allowedExt as $ext) {
        if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/img_tt/' . $name . '.' . $ext)) {
            return true;
        }
    }

    return false;
}

function ttImgDelete($name)
{
    $allowedExt = getAllowedExt();

    foreach ($allowedExt as $ext) {
        if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/img_tt/' . $name . '.' . $ext)) {
            return unlink($_SERVER['DOCUMENT_ROOT'] . '/img_tt/' . $name . '.' . $ext);
        }
    }

    return false;
}

function ttImgGet($name)
{
    $allowedExt = getAllowedExt();

    foreach ($allowedExt as $ext) {
        if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/img_tt/' . $name . '.' . $ext)) {
            return '' . $name . '.' . $ext;
        }
    }

    return "";
}

function fbImgExists($name)
{
    $allowedExt = getAllowedExt();

    foreach ($allowedExt as $ext) {
        if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/img_fb/' . $name . '.' . $ext)) {
            return true;
        }
    }

    return false;
}

function fbImgDelete($name)
{
    $allowedExt = getAllowedExt();

    foreach ($allowedExt as $ext) {
        if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/img_fb/' . $name . '.' . $ext)) {
            return unlink($_SERVER['DOCUMENT_ROOT'] . '/img_fb/' . $name . '.' . $ext);
        }
    }

    return false;
}

function fbImgGet($name)
{
    $allowedExt = getAllowedExt();

    foreach ($allowedExt as $ext) {
        if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/img_fb/' . $name . '.' . $ext)) {
            return $name . '.' . $ext;
        }
    }

    return "";
}

?>