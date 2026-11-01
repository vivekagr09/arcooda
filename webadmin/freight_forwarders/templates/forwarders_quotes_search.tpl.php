<!-- forwarders_quotes_search.tpl.php -->

<?php
/*
$advanced;
$sea_air;
$country_from;
$country_to;
$port_from;
$port_to;
$currency;
*/

$seaports_list=getSeaportsList();
$airports_list=getAirportsList();
?>
<script type="text/javascript">
function onFieldChanged(changed)
{
	var transport=getValue('sea_air');
	var country_from=getValue('country_from');
	var country_to=getValue('country_to');
	if(changed=='transport')
	{
		toggleSection('port_from_row',transport=='sea' || transport=='air');
		toggleSection('port_to_row',transport=='sea' || transport=='air');
		setValue('port_to','');
		setValue('port_from','');
		switch(transport)
		{
		case 'sea': setAllValues('display_transport_type','Sea'); break;
		case 'air': setAllValues('display_transport_type','Air'); break;
		}
	}
	if(changed=='country_from' || changed=='transport')
	{
		setValue('port_from','');
		var elem=getElement('port_from');
		if(elem)
		{
			var port_list=Array();
			if(transport=='sea') port_list=countrySeaports(country_from);
			if(transport=='air') port_list=countryAirports(country_from);
			elem.options.length=0;
			elem.options[0]=new Option('ALL','');
			for(var key in port_list)
				elem.options[elem.options.length]=new Option(port_list[key],key);
		}
	}
	if(changed=='country_to' || changed=='transport')
	{
		setValue('port_to','');
		var elem=getElement('port_to');
		if(elem)
		{
			var port_list=Array();
			if(transport=='sea') port_list=countrySeaports(country_to);
			if(transport=='air') port_list=countryAirports(country_to);
			elem.options.length=0;
			elem.options[0]=new Option('ALL','');
			for(var key in port_list)
				elem.options[elem.options.length]=new Option(port_list[key],key);
		}
	}
}
</script>

<table align="center" style="width:95%;">
	<form action="index.php?action=search" method="post">
	<tr>
		<td class="left_title" style="width:20%;">Transport Type:</td>
		<td><select name="sea_air" onchange="onFieldChanged('transport');">
			<?php echo formatHTMLOptions(array(''=>'Any','sea'=>'Sea','air'=>'Air'),$sea_air) ?>
		</select></td>
	</tr>
<?php
if($advanced)
{
?>
	<input type="hidden" name="advanced" value="true" />
	<tr>
		<td class="left_title">Country From:</td>
		<td><select name="country_from" onchange="onFieldChanged('country_from');">
			<option value="">ALL</option>
			<?php echo formatHTMLOptions(getCountryList(),$country_from) ?>
		</select></td>
	</tr>
	<tr id="port_from_row"<?php echo (!in_array($sea_air,array('sea','air')))?' style="display:none;"':'' ?>>
		<td class="left_title"><span name="display_transport_type"><?php echo ucfirst($sea_air) ?></span>port From:</td>
		<td><select name="port_from">
			<option value="">ALL</option>
<?php
	if($sea_air=='sea') echo formatHTMLOptions($seaports_list[$country_from],$port_from);
	if($sea_air=='air') echo formatHTMLOptions($airports_list[$country_from],$port_from);
?>
		</select></td>
	</tr>
	<tr>
		<td class="left_title">Country To:</td>
		<td><select name="country_to" onchange="onFieldChanged('country_to');">
			<option value="">ALL</option>
			<?php echo formatHTMLOptions(getCountryList(),$country_to) ?>
		</select></td>
	</tr>
	<tr id="port_to_row"<?php echo (!in_array($sea_air,array('sea','air')))?' style="display:none;"':'' ?>>
		<td class="left_title"><span name="display_transport_type"><?php echo ucfirst($sea_air) ?></span>port To:</td>
		<td><select name="port_to">
			<option value="">ALL</option>
<?php
	if($sea_air=='sea') echo formatHTMLOptions($seaports_list[$country_to],$port_to);
	if($sea_air=='air') echo formatHTMLOptions($airports_list[$country_to],$port_to);
?>
		</select></td>
	</tr>
<?php
}
else
{
?>
	<input type="hidden" name="advanced" value="false" />
	<tr>
		<td class="left_title">Country From:</td>
		<td><select name="country_from">
			<option value="">ALL</option>
			<?php echo formatHTMLOptions(getCountryList(),$country_from) ?>
		</select></td>
	</tr>
	<tr>
		<td class="left_title">Country To:</td>
		<td><select name="country_to">
			<option value="">ALL</option>
			<?php echo formatHTMLOptions(getCountryList(),$country_to) ?>
		</select></td>
	</tr>
<?php
}

$switch_url=$get_vars;
$switch_url['action']='search';
$switch_url['advanced']=($advanced)?'false':'true';
if($advanced)
{
	unset($switch_url['sea_air']);
	unset($switch_url['port_from']);
	unset($switch_url['port_to']);
}
$switch_url='index.php?'.http_build_query($switch_url);
?>
	<tr>
		<td class="left_title">Currency:</td>
		<td><select name="currency">
			<?php echo formatHTMLOptions(getCurrencyList(),($currency!='')?$currency:'AUD') ?>
		</select></td>
	</tr>
	<tr>
		<td class="left_title" colspan="2" style="text-align:center;">
			<input type="submit" value="Find" /><br />
			<br />
			<small><a href="<?php echo $switch_url ?>">Switch to <?php echo ($advanced)?'Quick':'Advanced' ?> Mode</a></small>
		</td>
	</tr>
	</form>
</table>

<!-- /forwarders_quotes_search.tpl.php -->