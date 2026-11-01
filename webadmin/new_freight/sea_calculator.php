<?php
include_once('freight_common.php');

$posted = array_merge($_GET, $_POST);

$advanced = $posted['advanced'];
$from = $posted['from'];
$to = $posted['to'];
$currency = $posted['currency'];
if($currency == '' && $from != '' && isset($posted['currency'])) $currency = 'AUD';
$from_sea = $posted['from_sea'];
$to_sea = $posted['to_sea'];

$seaports = getSeaPortsList();


include_once('templates/freight_header.tpl.php');
?>
<body <?=($advanced)?'onLoad="buildSeaports(\''.$from_sea.'\',\''.$to_sea.'\')"':''?>>
<link rel="stylesheet" type="text/css" href="style.css" />
<center><?= formatHTMLTitle('Sea Cargo - '.(($advanced)?'Advanced ':'Quick ').'Search') ?></center>
<form accept-charset="utf-8" action="" method="GET">
<table class="wide" align="center" style="width: 60%;" cellspacing=0 cellpadding=2 border=0>
	<tr>
		<td class="left_title">Ship From :</td>
		<td>
			<select name="from" style="width: 100%" <?=($advanced)?'onchange="buildSeaports(\'\',\''.$to_sea.'\')"':''?>>
				<option value=""></option><?=formatHTMLOptions(getCountryList(false),$from)?>
			</select>
		</td>
	</tr>
<?php
if($advanced) { ?>
	<input type="hidden" name="advanced" value="true">
	<tr>
		<td class="left_title">Seaport :</td>
		<td><select name="from_sea" style="width: 100%"></select></td>
	</tr>
<?php
} ?>
	<tr>
		<td class="left_title">Ship To :</td>
		<td>
			<select name="to" style="width: 100%" <?=($advanced)?'onchange="buildSeaports(\''.$from_sea.'\',\'\')"':''?>>
				<option value=""></option><?=formatHTMLOptions(getCountryList(false),$to)?>
			</select>
		</td>
	</tr>
<?php
if($advanced) { ?>
	<tr>
		<td class="left_title">Seaport :</td>
		<td><select name="to_sea" style="width: 100%"></select></td>
	</tr>
<?php
} ?>
	<tr>
		<td class="left_title">Currency :</td>
		<td><select name="currency" style="width: 100%"><option value=""></option><?=formatHTMLOptions(getCurrencyList(),$currency)?></td>
	</tr>
	<tr>
		<td class="left_title" colspan=2 style="text-align: center">
			<input type="submit" style="width: 100px" value="Find"><br /><br />
<?php
if($advanced) { ?>
			<small><a style="color: blue" href="index.php?action=sea">Switch to Quick Mode</a></small>
<?php
} else { ?>
			<small><a style="color: blue" href="index.php?action=sea&advanced=true">Switch to Advanced Mode</a></small>
<?php
} ?>
			&nbsp;&nbsp;<small><a style="color: blue" href="index.php?action=air">Switch to Air Cargo Search</a></small>
		</td>
	</tr>
</table>
</form>
<br />
<?php
if($from != '') {
	//$list = freightSeaSearch($from,$to,$from_sea,$to_sea,$currency);
	//print_array($list);
	//if(count($list) > 0) {
?>
<center><h3>
	Search Results for sending Sea Cargo from <?=($advanced)?$seaports[$from][$from_sea]:getCountryName($from)?> to <?=($to!='')?(($advanced)?$seaports[$to][$to_sea]:getCountryName($to)):'ALL Destinations'?>
</h3></center>
<!--<center><?= formatHTMLTitle('Staff Summary') ?></center>-->
<table class="wide" align="center" style="width: 98%;" cellspacing=0 cellpadding=2 border=1>
	<tr>
		<th>Date</th><th>From</th><th>To</th><th>Weight (kgs)</th><th>Type</th><th>Service</th><th>Container Type</th><th>Transit Time</th><th>Price (<?=$currency?>)</th>
	</tr>
	<tr style="text-align:center">
		<td>9/07/07</td>
		<td><?=($advanced)?$seaports[$from][$from_sea]:getCountryName($from)?></td>
		<td><?=($to!='')?(($advanced)?$seaports[$to][$to_sea]:getCountryName($to)):'China'?></td>
		<td>1120</td><td>Quote</td><td>Indirect</td><td>40 FT</td><td>2-3 Weeks</td><td>$2000.00</td>
	</tr>
	<tr class="alt" style="text-align:center">
		<td>16/07/07</td>
		<td><?=($advanced)?$seaports[$from][$from_sea]:getCountryName($from)?></td>
		<td><?=($to!='')?(($advanced)?$seaports[$to][$to_sea]:getCountryName($to)):'Hong Kong'?></td>
		<td>1100</td><td>Invoice</td><td>Direct</td><td>20 FT</td><td>2-3 Weeks</td><td>$1500.00</td>
	</tr>
</table>
<center><input type="button" value="Print List" onclick="return false"></center>
<br />
<center><h5><i>NB: This is just test data</i></h5></center>
<?php
} ?>