<?php
include_once('freight_common.php');

$posted = array_merge($_GET, $_POST);

$advanced=$posted['advanced'];
$from=$posted['from'];
$to=$posted['to'];
$currency=$posted['currency'];
if($currency=='' && $from!='' && isset($posted['currency'])) $currency='AUD';
$from_air=$posted['from_air'];
$to_air=$posted['to_air'];

$airports=getAirPortsList();

include_once('templates/freight_header.tpl.php');
?>
<body<?=($advanced) ? (' onload="' . htmlspecialchars('buildAirports(' . json_encode($from_air) . ', ' . json_encode($to_air) . ')') . '"') : ''?>>
	<link rel="stylesheet" type="text/css" href="style.css" />
	<center><?= formatHTMLTitle('Air Cargo - '.(($advanced)?'Advanced Search':'Quick Search')) ?></center>
	<form accept-charset="utf-8" action="" method="get">
		<table class="wide" align="center" style="width:60%;" cellspacing="0" cellpadding="2" border="0">
			<tr>
				<td class="left_title">Ship From :</td>
				<td><select name="from" style="width:100%;"<?=($advanced) ? (' onchange="' . htmlspecialchars('buildAirports("", ' . json_encode($to_air) . ')') . '"') : ''?>>
					<option value=""></option>
					<?= formatHTMLOptions(getCountryList(false),$from) ?>
				</select></td>
			</tr>
<?php
if($advanced)
{
?>
			<tr>
				<td class="left_title"><input type="hidden" name="advanced" value="true" />Airport :</td>
				<td><select name="from_air" style="width:100%;"></select></td>
			</tr>
<?php
}
?>
			<tr>
				<td class="left_title">Ship To :</td>
				<td><select name="to" style="width:100%;"<?=($advanced) ? (' onchange="' . htmlspecialchars('buildAirports(' . json_encode($from_air) . ', "")') . '"') : ''?>>
					<option value=""></option>
					<?= formatHTMLOptions(getCountryList(false),$to) ?>
				</select></td>
			</tr>
<?php
if($advanced)
{
?>
			<tr>
				<td class="left_title">Airport :</td>
				<td><select name="to_air" style="width:100%;"></select></td>
			</tr>
<?php
}
?>
			<tr>
				<td class="left_title">Currency :</td>
				<td><select name="currency" style="width:100%;">
					<option value=""></option>
					<?= formatHTMLOptions(getCurrencyList(),$currency) ?>
				</select></td>
			</tr>
			<tr>
				<td class="left_title" colspan="2" style="text-align:center;">
					<input type="submit" style="width:100px;" value="Find" /><br />
					<br />
<?php
if($advanced)
{
?>
					<small><a style="color:blue;" href="index.php?action=air">Switch to Quick Mode</a></small>
<?php
}
else
{
?>
					<small><a style="color:blue;" href="index.php?action=air&advanced=true">Switch to Advanced Mode</a></small>
<?php
}
?>
					&nbsp;&nbsp;
					<small><a style="color:blue;" href="index.php?action=sea">Switch to Sea Cargo Search</a></small>
				</td>
			</tr>
		</table>
	</form>
	<br />
<?php
$list=array();
//if($from!='') $list=freightAirSearch($from,$to,$from_air,$to_air,$currency);

//if(count($list))
{
?>
	<center><h3>Search Results for sending Air Cargo from <?= ($advanced)?$airports[$from][$from_air]:getCountryName($from) ?> to <?= ($to!='')?(($advanced)?$airports[$to][$to_air]:getCountryName($to)):'ALL Destinations' ?></h3></center>
	<table class="wide" align="center" style="width:98%;" cellspacing="0" cellpadding="2" border="1">
		<tr>
			<th>Date</th>
			<th>From</th>
			<th>To</th>
			<th>Weight (kgs)</th>
			<th>Type</th>
			<th>Service</th>
			<th>Transit Time</th>
			<th>Price (<?= $currency ?>)</th>
		</tr>
		<tr style="text-align:center;">
			<td>9/07/07</td>
			<td><?= ($advanced)?$airports[$from][$from_air]:getCountryName($from) ?></td>
			<td><?= ($to!='')?(($advanced)?$airports[$to][$to_air]:getCountryName($to)):'China' ?></td>
			<td>1120</td>
			<td>Quote</td>
			<td>Indirect</td>
			<td>2-3 Days</td>
			<td>$2000.00</td>
		</tr>
		<tr class="alt" style="text-align:center;">
			<td>16/07/07</td>
			<td><?= ($advanced)?$airports[$from][$from_air]:getCountryName($from) ?></td>
			<td><?= ($to!='')?(($advanced)?$airports[$to][$to_air]:getCountryName($to)):'Hong Kong' ?></td>
			<td>1100</td>
			<td>Invoice</td>
			<td>Direct</td>
			<td>2-3 Days</td>
			<td>$1500.00</td>
		</tr>
	</table>
	<center><input type="button" value="Print List" onclick="return false" /></center>
	<br />
	<center><h5><i>NB: This is just test data</i></h5></center>
<?php
}
?>
</body>
<?php
include_once('templates/freight_footer.tpl.php');
?>