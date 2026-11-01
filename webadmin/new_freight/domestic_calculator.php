<?php
include_once('freight_common.php');

$posted = array_merge($_GET, $_POST);

$action=$posted['action'];

include_once('templates/freight_header.tpl.php');
?>
<link rel="stylesheet" type="text/css" href="style.css" />
<center><?= formatHTMLTitle('Freight Calculator - Domestic') ?></center>

<form accept-charset="utf-8" action="" method="GET" onsubmit="return validateSearch()">
<table class="wide" align="center" style="width: 75%;" cellspacing=0 cellpadding=2 border=0>
	<tr>
		<td class="left_title">Destination :</td>
		<td><input type="text" name="display" style="width: 100%" value="<?=$posted['display']?>" onkeyup="updateList()"></td>
	</tr>
	<tr id="list_tr" style="display: none"><td class="left_title">&nbsp;</td><td><div id="list" style="border: 1px solid blue"></div></td></tr>
	<tr><td class="left_title">Weight :</td><td><input type="text" name="weight" style="width: 85%" value="<?=$posted['weight']?>"> Kgs</td></tr>
	<tr>
		<td class="left_title">Dimensions (cms) :</td>
		<td>
			<input type="text" size="5" name="width" value="<?=$posted['width']?>"/> <b>Width x</b>
			<input type="text" size="5" name="depth" value="<?=$posted['depth']?>"/> <b>Depth x</b>
			<input type="text" size="5" name="height" value="<?=$posted['height']?>"/> <b>Height</b>
		</td>
	</tr>
	<tr>
		<td class="left_title">Value of Goods :(with GST)</td>
		<td><input type="text" name="value" style="width: 85%" value="<?=$posted['value']?>"> AUD</td>
	</tr>
	<tr>
		<td class="left_title" colspan=2 style="text-align: center">
			<input type="submit" style="width: 100px" value="Calculate">&nbsp;
			<input type="button" style="width: 100px" value="Reset" onclick="javascript:resetData()"><br /><br />
			<small><a style="color: blue" href="index.php?action=international">Switch to International Calculator</a></small>
		</td>
	</tr>
</table>
<input type="hidden" name="state" value="<?=$posted['state']?>"><input type="hidden" name="code" value="<?=$posted['code']?>"><input type="hidden" name="destination" value="<?=$posted['destination']?>">
</form>
<br />
<?php
$dest = $posted['destination'];
if($dest == '' && $posted['display'] != '') {
	$display = $posted['display'];
	if(strpos($display,'(')) {
		list($display,$post_code) = explode(',',$display);
		list($state,$post_code) = explode('(',$post_code);
		$post_code = str_replace(')','',$post_code);
		$display = trim($display); $post_code = trim($post_code); $state = trim($state);
		$display = $display.' , '.$post_code.' , '.$state;
	}
	$list1 = destinationSearch($display);
	$list2 = postcodeSearch($display);
	if(count($list1) > count($list2)) $list = $list1;
	else $list = $list2;
	if(count($list)) {
		$posted['destination'] = $list[0]['Locality'];
		$posted['state'] = $list[0]['State'];
		$posted['code'] = $list[0]['Pcode'];
	}
	$posted['display'] = $posted['destination'].', '.$posted['state'].' ('.$posted['code'].')';
}
if($posted['destination'] != '') {
$status = array('y'=>'Yes','n'=>'No');
$dimensions=array($posted['width'],$posted['depth'],$posted['height']);
$list = freightDomesticCalculate($posted['destination'],$posted['state'],$posted['code'], $posted['weight']);
$list = freightCalculatePrices($list, $posted['weight'], $posted['value'],$dimensions);
if(count($list)) {
	foreach($list as $id=>$array) {
		if($posted['value']==0)
			$list[$id]['insurance']='n';
	}
	foreach($list as $id=>$array) {
		$list[$id]['staff_display']=true;
		if($array['insurance'] == 'y' && $array['insurance_cost'] >= 1 && !strpos($array['insurance_cost'],'(')) {
			$new_array = $array;
			$new_array['sell_price'] = $new_array['sell_price'] + $new_array['insurance_cost'];
			$new_array['insurance'] == 'y';
			$list[$id]['insurance'] = 'n';
			//if($new_array['tail_gate_charge']!=0) $new_array['sell_price']+=$new_array['tail_gate_charge'];
			//$new_array['tail_gate_charge']=0;
			$new_array['staff_display'] = false;
			$list[] = $new_array;
		}
	}
	foreach($list as $id=>$array) {
		if($array['tail_gate_charge']!=0) {
			$new_array=$array;
			$new_array['staff_display']=false;
			$new_array['sell_price'] += $array['tail_gate_charge'];
			$new_array['tail_gate'] = 'y';
			$list[$id]['tail_gate'] = 'n';
			//$list[$id]['sell_price'] += $array['tail_gate_charge'];
			$list[] = $new_array;
		}
	}
}
$list = freightSortArray($list);

if(count($list) > 0) {
	$spec_notes = false;
	$eNote=false;
	$alt=false;
	foreach($list as $id=>$array) {
		if($array['notes'] != '') {
			$spec_notes = true;
			if($array['notes']=="Zone outside express delivery network. Delivery will be done ASAP.")
				$eNote=true;
		}
	}
?>
<?php
$extra = '<small><i><a name="view_link" style="color:blue" href="#" onclick="viewAll(false);return false;">View All</a><a name="hide_link" style="color:blue;display: none" href="#" onclick="viewAll(true);return false;">Hide</a></i></small>'; ?>
<center><h3>Results for sending <?=$posted['weight']?> kgs from Newcastle to <?=ucwords($posted['display'])?></h3></center>
<center><?= formatHTMLTitle('Staff Summary',$extra) ?></center>
<table class="wide" align="center" style="width: 98%;" cellspacing=0 cellpadding=2 border=1 bordercolor="#CCCCCC">
	<tr><th style="width:5%" nowrap>Provider</th><th>Service</th><th>Buy Rate</th><th>Retail Price</th><th>Our Sell Price</th><th>Profit</th></tr>
<?php
	foreach($list as $id=>$array) {
		if($array['display'] && $array['staff_display']) {
?>
	<tr <?= ($alt=!$alt)?'':' class="alt"' ?> style="text-align: center">
		<td style="width:5%" nowrap>
			<a href="<?=$array['forward_url']?>" target="_blank" onclick="<?=($array['supplier'] == 0 ? 'return false;' : '')?>">
				<?=getFreightForwarderName($array['supplier'])?>
			</a>
		</td>
		<td><a href="<?=$array['edit_url']?>" target="_blank" style="color: black"><?=$array['name']?></a></td>
		<td>$<?=sprintf("%.2f",$array['cost_price']*1.1)?></td>
		<td>$<?=sprintf("%.2f",$array['retail_price']*1.1)?></td>
		<td><b>$<?=sprintf("%.2f",ceil($array['sell_price'])*1.1)?></b></td>
		<td>$<?=sprintf("%.2f",$array['profit'])?></td>
	</tr>
<?php
if($array['break_up'] != '') { ?>
	<tr name="break_up" id="break_up" style="display: none;background-color: #F7F7F7;">
		<td colspan=6><?=nl2br($array['break_up'])?></td>
	</tr>
<?php
}
}
}
?>
</table>
<br /><br/>
<center><?= formatHTMLTitle('Customer Summary') ?></center><br/><br/>
<table class="wide" align="center" style="width: 75%;border: 1px solid black;" cellspacing=0 cellpadding=2 bordercolor="black" border=1>
<?php
$colspan = 5; if($weight > 25 || $spec_notes) $colspan=6; if($weight > 25 && $spec_notes) $colspan=7;?>
	<tr>
		<td colspan="<?=$colspan?>" style="text-align: center;font-weight:bold;font-size:9pt">
			&nbsp<br/>Prices for sending <?=$posted['weight']?> kgs from Newcastle to <?=ucwords($posted['display'])?><br/>&nbsp;
		</td>
	</tr>
	<tr><th>Price (AUD)</th><th>Service</th><th>Transit Time</th><th>Tracking</th><th>Insurance</th>
	<?php if($weight > 25) {?><th>Tail Gate</th><?php }?>
	<?php if($spec_notes) { ?><th>Special Notes</th><?php }?></tr>
<?php
	foreach($list as $id=>$array) {
		if($array['display']) {
			if($array['tail_gate_charges']!=0) $array['sell_price']+=$array['tail_gate_charges']
?>
	<tr style="text-align: center;" class="alt">
		<td style="font-size: 8pt"><b>$<?=sprintf("%.2f",ceil($array['sell_price'])*1.1)?></b></td>
		<td style="font-size: 8pt"><?=$array['name']?></td>
		<td style="font-size: 8pt"><?=$array['del_time']?></td>
		<td style="font-size: 8pt"><?=$status[$array['track']]?></td>
		<?php $str=($posted['value_aud']==0) ? 'Yes' : 'Included'; ?>
		<?php $ins = (strpos($array['insurance_cost'],'(')) ? $array['insurance_cost'] : (($array['insurance'] == 'y') ? $str : 'No'); ?>
		<td style="font-size: 8pt"><?=$ins?></td>
<?php
if($posted['weight'] > 25) {
			$t_charge = ($array['tail_gate'] == 'y') ? 'Included' : 'No';
?>
		<td style="font-size:8pt"><?=$t_charge?></td>
<?php
} ?>
<?php
if($spec_notes) { ?>
		<td style="font-size: 8pt"><?=$array['notes']?></td>
<?php
} ?>
	</tr>
<?php } } ?>
	<tr class="alt">
		<td colspan="<?=$colspan?>" style="font-size: 9px;font-family:Verdana;text-align: left;padding:10px 0px 10px 25px;">
			<li>Prices given above are inclusive of 10% GST</i></li>
			<li><b>DELIVERY :</b> Please note the transit times are estimates only.</li>
			<li><b>TRACKING :</b> Some couriers give you to the facility to Track the status of your parcel over the Internet. (A tracking number & website will be emailed to you once your goods have been shipped, if available).
			</li>
			<li><b>INSURANCE :</b> Some couriers also provide Insurance. The cost of insurance is usually based on some percent of the total value of the goods.</li>
			<?php if($posted['weight'] > 25) {?>
			<li><b>TAIL GATE :</b> Goods will be delivered on a truck with a tailgate lifter, i.e no lifting will be involved</li>
<?php
} ?>
			<?php if($eNote) {?>
			<li><b>EXPRESS POST :</b> For zones outside the express delivery network, please contact Australia Post on 131318 to get a more accurate transit time.</li>
<?php
} ?>
		</td>
	</tr>
</table>
<br />
<?php
} else { ?>
<center><h4>No Freight available for sending <?=$posted['weight']?> kgs from Newcastle to <?=ucwords($posted['display'])?></h4></center>
<?php
} ?>
<?php
} ?>
<br /><br />